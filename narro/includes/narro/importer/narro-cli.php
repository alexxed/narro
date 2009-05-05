<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008 Alexandru Szasz <alexxed@gmail.com>
     * http://code.google.com/p/narro/
     *
     * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
     * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any
     * later version.
     *
     * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
     * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
     * more details.
     *
     * You should have received a copy of the GNU General Public License along with this program; if not, write to the
     * Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
     */

    require_once(dirname(__FILE__) . '/../../prepend.inc.php');

    if (!isset($argv[2])) {
        echo
            sprintf(
                    "php %s [--import|--export] [options]\n" .
                    "--import                     import a project\n" .
                    "--export                     export a project\n" .
                    "--project                    project id from the database\n" .
                    "--template-lang              language code for the original texts, optional, defaults to en-US\n" .
                    "--translation-lang           language code for the translations\n" .
                    "--template-directory         the directory that holds the original texts" .
                    "--translation-directory      the directory that holds the translations" .
                    "--user                       user id that will be used for the added\n" .
                    "                             suggestions, optional, defaults to anonymous\n" .
                    "--exported-suggestion        1 for approved,\n" .
                    "                             2 - approved, then most voted,\n" .
                    "                             3 - approved, then most recent,\n" .
                    "                             4 approved, most voted, most recent,\n" .
                    "                             5 approved, my suggestion\n" .
                    "--do-not-deactivate-files    do not deactivate project files before importing\n" .
                    "--do-not-deactivate-contexts do not deactivate project contexts before\n" .
                    "                             importing\n" .
                    "--check-equal                check if the translation is equal to the original\n" .
                    "                             text and don't import it\n" .
                    "--approve                    approve the imported suggestions\n" .
                    "--import-unchanged-files     import files marked unchanged after the last import\n" .
                    "--copy-unhandled-files       copy unhandled files when exporting\n" .
                    "--only-suggestions           import only suggestions, don't add files, texts\n" .
                    "                             or contexts\n",
                basename(__FILE__)
            )
        ;
        exit();
    }

    if (in_array('--import', $argv)) {

        $objNarroImporter = new NarroProjectImporter();

        /**
         * Get boolean options
         */
        $objNarroImporter->DeactivateFiles = !((bool) array_search('--do-not-deactivate-files', $argv));
        $objNarroImporter->DeactivateContexts = !((bool) array_search('--do-not-deactivate-contexts', $argv));
        $objNarroImporter->CheckEqual = (bool) array_search('--check-equal', $argv);
        $objNarroImporter->Approve = (bool) array_search('--approve', $argv);
        $objNarroImporter->OnlySuggestions = (bool) array_search('--only-suggestions', $argv);
        $objNarroImporter->ImportUnchangedFiles = (bool) array_search('--import-unchanged-files', $argv);

        /**
         * Get specific options
         */

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--template-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--template-lang', $argv)+1];
        else
            $strSourceLanguage = 'en-US';

        if (array_search('--translation-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--translation-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];


        require_once('Zend/Log.php');
        require_once('Zend/Log/Writer/Stream.php');

        $objLogger = new Zend_Log(new Zend_Log_Writer_Stream(__TMP_PATH__ . '/' . $intProjectId . '-' . $strTargetLanguage . '-import.log'));

        $objNarroImporter->Logger = $objLogger;

        /**
         * Load the specified user or the anonymous user if unspecified
         */
        $objUser = NarroUser::LoadByUserId($intUserId);
        if (!$objUser instanceof NarroUser) {
            $objLogger->info(sprintf('User id=%s does not exist in the database, will try to use the anonymous user.', $intUserId));
            $objUser = NarroUser::LoadAnonymousUser();
            if (!$objUser instanceof NarroUser) {
                $objLogger->info(sprintf('The anonymous user id=%s does not exist in the database.', $intUserId));
                return false;
            }
        }

        NarroApp::$User = $objUser;

        /**
         * Load the specified project
         */
        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            $objLogger->info(sprintf('Project with id=%s does not exist in the database.', $intProjectId));
            return false;
        }

        /**
         * Load the specified target language
         */
        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            $objLogger->info(sprintf('Language %s does not exist in the database.', $strTargetLanguage));
            return false;
        }

        NarroApp::$Language = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        $objLogger->info(sprintf('Target language is %s', $objNarroImporter->TargetLanguage->LanguageName));

        /**
         * Load the specified source language
         */
        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            $objLogger->info(sprintf('Language %s does not exist in the database.', $strSourceLanguage));
            return false;
        }

        $objLogger->info(sprintf('Source language is %s', $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        if (array_search('--template-directory', $argv) !== false)
            $objNarroImporter->TemplatePath = $argv[array_search('--template-directory', $argv)+1];
        else
            $objNarroImporter->TemplatePath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '/' . $objNarroImporter->SourceLanguage->LanguageCode;

        if (array_search('--translation-directory', $argv) !== false)
            $objNarroImporter->TranslationPath = $argv[array_search('--translation-directory', $argv)+1];
        else
            $objNarroImporter->TranslationPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '/' . $objNarroImporter->TargetLanguage->LanguageCode;

        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanImportDirectory();
        }



        try {
            $intPid = NarroUtils::IsProcessRunning('import', $objNarroImporter->Project->ProjectId);

            if ($intPid && $intPid <> getmypid())
                throw new Exception(sprintf('An import process is already for this project with pid %d', $intPid));

            $objNarroImporter->ImportProject();
        }
        catch (Exception $objEx) {
            $objLogger->info(sprintf('An error occured during import: %s', $objEx->getMessage()));
            $objNarroImporter->CleanImportDirectory();
            exit();
        }

        $objNarroImporter->CleanImportDirectory();
        $objLogger->info(var_export(NarroImportStatistics::$arrStatistics, true));
     }
     elseif (in_array('--export', $argv)) {

        $objNarroImporter = new NarroProjectImporter();

        if (array_search('--exported-suggestion', $argv))
            $objNarroImporter->ExportedSuggestion = $argv[array_search('--exported-suggestion', $argv)+1];

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--template-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--template-lang', $argv)+1];
        else
            $strSourceLanguage = 'en-US';

        if (array_search('--translation-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--translation-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];

        require_once('Zend/Log.php');
        require_once('Zend/Log/Writer/Stream.php');
        $objLogger = new Zend_Log(new Zend_Log_Writer_Stream(__TMP_PATH__ . '/' . $intProjectId . '-' . $strTargetLanguage . '-export.log'));

        $objNarroImporter->Logger = $objLogger;

        $objUser = NarroUser::LoadByUserId($intUserId);
        if (!$objUser instanceof NarroUser) {
            $objLogger->info(sprintf('User id=%s does not exist in the database, will try to use the anonymous user.', $intUserId));
            $objUser = NarroUser::LoadAnonymousUser();
            if (!$objUser instanceof NarroUser) {
                $objLogger->info(sprintf('The anonymous user id=%s does not exist in the database.', $intUserId));
                return false;
            }
        }

        NarroApp::$User = $objUser;

        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            $objLogger->info(sprintf('Project with id=%s does not exist in the database.', $intProjectId));
            return false;
        }

        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            $objLogger->info(sprintf('Language %s does not exist in the database.', $strTargetLanguage));
            return false;
        }

        NarroApp::$Language = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        $objLogger->info(sprintf('Target language is %s', $objNarroImporter->TargetLanguage->LanguageName));

        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            $objLogger->info(sprintf('Language %s does not exist in the database.', $strSourceLanguage));
            return false;
        }

        $objLogger->info(sprintf('Source language is %s', $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;
        $objNarroImporter->CopyUnhandledFiles = ((bool) array_search('--copy-unhandled-files', $argv));

        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanExportDirectory();
        }

        try {
            $objNarroImporter->TranslationPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '/' . $objNarroImporter->TargetLanguage->LanguageCode;
            $objNarroImporter->TemplatePath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '/' . $objNarroImporter->SourceLanguage->LanguageCode;
            $intPid = NarroUtils::IsProcessRunning('export', $objNarroImporter->Project->ProjectId);

            if ($intPid && $intPid <> getmypid())
                $objLogger->info(sprintf('An export process is already for this project with pid %d', $intPid));

            $objNarroImporter->ExportProject();
        }
        catch (Exception $objEx) {
            $objLogger->info(sprintf('An error occured during export: %s', $objEx->getMessage()));
            $objNarroImporter->CleanExportDirectory();
            exit();
        }

        $objNarroImporter->CleanExportDirectory();
        $objLogger->info(var_export(NarroImportStatistics::$arrStatistics, true));

     }


?>
