<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2010 Alexandru Szasz <alexxed@gmail.com>
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

    require_once(dirname(__FILE__) . '/../../../configuration/prepend.inc.php');

    QApplication::LogInfo(sprintf('Starting %s with parameters "%s"', basename(__FILE__), join(' ', $argv)));

    if (!isset($argv[2])) {
        echo
            sprintf(
                    "php %s [--import|--export] [options]\n" .
                    "--import                     import a project\n" .
                    "--export                     export a project\n" .
                    "--project                    project id from the database\n" .
                    "--template-lang              language code for the original texts, optional, defaults to %s\n" .
                    "--translation-lang           language code for the translations\n" .
                    "--template-directory         the directory that holds the original texts" .
                    "--translation-directory      the directory that holds the translations" .
                    "--user                       user id that will be used for the added\n" .
                    "--disable-plugins            disable plugins during import/export\n" .
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
                    "--skip-untranslated          skip likes that don't have translated texts\n" .
                    "--approve                    approve the imported suggestions\n" .
                    "--approve-already-approved   overwrite translations approved in Narro\n" .
                    "--import-unchanged-files     import files marked unchanged after the last import\n" .
                    "--copy-unhandled-files       copy unhandled files when exporting\n" .
                    "--only-suggestions           import only suggestions, don't add files, texts\n" .
                    "                             or contexts\n",
                basename(__FILE__),
                NarroLanguage::SOURCE_LANGUAGE_CODE
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
        $objNarroImporter->ApproveAlreadyApproved = (bool) array_search('--approve-already-approved', $argv);
        $objNarroImporter->OnlySuggestions = (bool) array_search('--only-suggestions', $argv);
        $objNarroImporter->ImportUnchangedFiles = (bool) array_search('--import-unchanged-files', $argv);
        NarroPluginHandler::$blnEnablePlugins = (bool) array_search('--disable-plugins', $argv);

        /**
         * Get specific options
         */
        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--template-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--template-lang', $argv)+1];
        else
            $strSourceLanguage = NarroLanguage::SOURCE_LANGUAGE_CODE;

        if (array_search('--translation-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--translation-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];


        /**
         * Load the specified user or the anonymous user if unspecified
         */
        $objUser = NarroUser::LoadByUserId($intUserId);
        if (!$objUser instanceof NarroUser) {
            QApplication::LogInfo(sprintf('User id=%s does not exist in the database, will try to use the anonymous user.', $intUserId));
            $objUser = NarroUser::LoadAnonymousUser();
            if (!$objUser instanceof NarroUser) {
                QApplication::LogInfo(sprintf('The anonymous user id=%s does not exist in the database.', $intUserId));
                return false;
            }
        }

        QApplication::$User = $objUser;

        /**
         * Load the specified project
         */
        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            QApplication::LogInfo(sprintf('Project with id=%s does not exist in the database.', $intProjectId));
            return false;
        }

        /**
         * Load the specified target language
         */
        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            QApplication::LogInfo(sprintf('Language %s does not exist in the database.', $strTargetLanguage));
            return false;
        }

        QApplication::$TargetLanguage = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        QApplication::LogInfo(sprintf('Target language is %s', $objNarroImporter->TargetLanguage->LanguageName));

        /**
         * Load the specified source language
         */
        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            QApplication::LogInfo(sprintf('Language %s does not exist in the database.', $strSourceLanguage));
            return false;
        }

        QApplication::LogInfo(sprintf('Source language is %s', $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        if (array_search('--template-directory', $argv) !== false)
            $objNarroImporter->TemplatePath = $argv[array_search('--template-directory', $argv)+1];
        else
            $objNarroImporter->TemplatePath = $objNarroImporter->Project->DefaultTemplatePath;

        if (array_search('--translation-directory', $argv) !== false)
            $objNarroImporter->TranslationPath = $argv[array_search('--translation-directory', $argv)+1];
        else
            $objNarroImporter->TranslationPath = $objNarroImporter->Project->DefaultTranslationPath;

        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanImportDirectory();
        }



        try {
            $intPid = NarroUtils::IsProcessRunning('import', $objNarroImporter->Project->ProjectId);

            if ($intPid && $intPid <> getmypid())
                throw new Exception(sprintf('An import process is already running for this project with pid %d', $intPid));

            $strProcPidFile = __TMP_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '-' . $objNarroImporter->TargetLanguage->LanguageCode . '-import-process.pid';
            if (file_exists($strProcPidFile))
                unlink($strProcPidFile);

            file_put_contents($strProcPidFile, getmypid());

            $objNarroImporter->ImportProject();
        }
        catch (Exception $objEx) {
            QApplication::LogError(sprintf('An error occurred during import: %s', $objEx->getMessage()));
            $objNarroImporter->CleanImportDirectory();
            exit();
        }

        $objNarroImporter->CleanImportDirectory();
        foreach(NarroImportStatistics::$arrStatistics as $strName=>$strValue) {
            if ($strValue != 0)
                QApplication::LogInfo(stripslashes($strName) . ': ' . $strValue);
        }
     }
     elseif (in_array('--export', $argv)) {

        $objNarroImporter = new NarroProjectImporter();
        $objNarroImporter->SkipUntranslated = (bool) array_search('--skip-untranslated', $argv);
        NarroPluginHandler::$blnEnablePlugins = (bool) array_search('--disable-plugins', $argv);

        if (array_search('--exported-suggestion', $argv))
            $objNarroImporter->ExportedSuggestion = $argv[array_search('--exported-suggestion', $argv)+1];

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--template-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--template-lang', $argv)+1];
        else
            $strSourceLanguage = NarroLanguage::SOURCE_LANGUAGE_CODE;

        if (array_search('--translation-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--translation-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];

        $objUser = NarroUser::LoadByUserId($intUserId);
        if (!$objUser instanceof NarroUser) {
            QApplication::LogInfo(sprintf('User id=%s does not exist in the database, will try to use the anonymous user.', $intUserId));
            $objUser = NarroUser::LoadAnonymousUser();
            if (!$objUser instanceof NarroUser) {
                QApplication::LogInfo(sprintf('The anonymous user id=%s does not exist in the database.', $intUserId));
                return false;
            }
        }

        QApplication::$User = $objUser;

        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            QApplication::LogInfo(sprintf('Project with id=%s does not exist in the database.', $intProjectId));
            return false;
        }

        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            QApplication::LogInfo(sprintf('Language %s does not exist in the database.', $strTargetLanguage));
            return false;
        }

        QApplication::$TargetLanguage = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        QApplication::LogInfo(sprintf('Target language is %s', $objNarroImporter->TargetLanguage->LanguageName));

        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            QApplication::LogInfo(sprintf('Language %s does not exist in the database.', $strSourceLanguage));
            return false;
        }

        QApplication::LogInfo(sprintf('Source language is %s', $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;
        $objNarroImporter->CopyUnhandledFiles = ((bool) array_search('--copy-unhandled-files', $argv));
        if (array_search('--template-directory', $argv) !== false)
            $objNarroImporter->TemplatePath = $argv[array_search('--template-directory', $argv)+1];
        else
            $objNarroImporter->TemplatePath = $objNarroImporter->Project->DefaultTemplatePath;

        if (array_search('--translation-directory', $argv) !== false)
            $objNarroImporter->TranslationPath = $argv[array_search('--translation-directory', $argv)+1];
        else
            $objNarroImporter->TranslationPath = $objNarroImporter->Project->DefaultTranslationPath;


        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanExportDirectory();
        }

        try {
            $intPid = NarroUtils::IsProcessRunning('export', $objNarroImporter->Project->ProjectId);

            if ($intPid && $intPid <> getmypid())
                QApplication::LogInfo(sprintf('An export process is already running for this project with pid %d', $intPid));

            $strProcPidFile = __TMP_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '-' . $objNarroImporter->TargetLanguage->LanguageCode . '-export-process.pid';
            if (file_exists($strProcPidFile))
                unlink($strProcPidFile);

            file_put_contents($strProcPidFile, getmypid());

            $objNarroImporter->ExportProject();
        }
        catch (Exception $objEx) {
            QApplication::LogError(sprintf('An error occurred during export: %s', $objEx->getMessage()));
            $objNarroImporter->CleanExportDirectory();
            exit();
        }

        $objNarroImporter->CleanExportDirectory();
        foreach(NarroImportStatistics::$arrStatistics as $strName=>$strValue) {
            if ($strValue != 0)
                QApplication::LogInfo(stripslashes($strName) . ': ' . $strValue);
        }

     }


?>
