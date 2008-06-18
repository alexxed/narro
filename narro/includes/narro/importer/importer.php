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
                t(
                    "php %s [--import|--export] [options]\n" .
                    "--import                      import a project\n" .
                    "--export                      export a project\n" .
                    "--minloglevel                 minimum level of errors logged, 1 gives the most information\n" .
                    "--project                     project id from the database\n" .
                    "--source-lang                 source language code, optional, defaults to en_US\n" .
                    "--target-lang                 target language code\n" .
                    "--user                        user id that will be used for the added suggestions, optional, defaults to anonymous\n" .
                    "--exported-suggestion         1 for validated, 2 - the most voted, 3 - the user's suggestion\n" .
                    "--force                       run the operation even if a previous operation is reported to be running\n" .
                    "--do-not-deactivate-files     do not deactivate project files before importing\n" .
                    "--do-not-deactivate-contexts  do not deactivate project contexts before importing\n" .
                    "--check-equal                 check if the translation is equal to the original text and don't import it\n" .
                    "--validate                    validate the imported suggestions\n" .
                    "--only-suggestions            import only suggestions, don't add files, texts or contexts\n"
                ), 
                basename(__FILE__)
            )
        ;
        exit();
    }
        
    require_once(dirname(__FILE__) . '/NarroProjectImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaIncFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaDtdFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaIniFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroGettextPoFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroOpenOfficeSdfFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroSvgFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroImportStatistics.class.php');
    require_once(dirname(__FILE__) . '/NarroLog.class.php');
    require_once(dirname(__FILE__) . '/NarroRss.class.php');
    require_once(dirname(__FILE__) . '/NarroMozilla.class.php');

    if (in_array('--import', $argv)) {

        $objNarroImporter = new NarroProjectImporter();

        NarroLog::$blnEchoOutput = false;
        
        /**
         * Get boolean options
         */
        $objNarroImporter->DeactivateFiles = !((bool) array_search('--do-not-deactivate-files', $argv));
        $objNarroImporter->DeactivateContexts = !((bool) !array_search('--do-not-deactivate-contexts', $argv));
        $objNarroImporter->CheckEqual = (bool) array_search('--check-equal', $argv);
        $objNarroImporter->Validate = (bool) array_search('--validate', $argv);
        $objNarroImporter->OnlySuggestions = (bool) array_search('--only-suggestions', $argv);
        
        /**
         * Get specific options
         */
        if (array_search('--minloglevel', $argv))
            NarroLog::$intMinLogLevel = $argv[array_search('--minloglevel', $argv)+1];

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--source-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--source-lang', $argv)+1];
        else
            $strSourceLanguage = 'en_US';

        if (array_search('--target-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--target-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];
        
        /**
         * Load the specified user or the anonymous user if unspecified
         */
        $objUser = NarroUser::Load($intUserId);
        if (!$objUser instanceof NarroUser) {
            NarroLog::LogMessage(2, sprintf(t('User id=%s does not exist in the database, will try to use the anonymous user.'), $intUserId));
            $objUser = NarroUser::Load(NarroUser::ANONYMOUS_USER_ID);
            if (!$objUser instanceof NarroUser) {
                NarroLog::LogMessage(3, sprintf(t('The anonymous user id=%s does not exist in the database.'), $intUserId));
                return false;
            }
        }

        /**
         * Load the specified project
         */
        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            NarroLog::LogMessage(3, sprintf(t('Project with id=%s does not exist in the database.'), $intProjectId));
            return false;
        }
        
        /**
         * Strip the " if they were used to enclose the path
         */
        $strArchiveFile = str_replace('"','', $argv[$argc-1]);

        if (!file_exists($strArchiveFile)) {
            NarroLog::LogMessage(3, sprintf(t('File "%s" does not exist.'), $strArchiveFile));
            return false;
        }
        
        /**
         * Load the specified target language
         */
        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), $strTargetLanguage));
            return false;
        }

        QApplication::$objUser->Language = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        NarroLog::LogMessage(3, sprintf(t('Target language is %s'), $objNarroImporter->TargetLanguage->LanguageName));

        /**
         * Load the specified source language
         */
        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), $strSourceLanguage));
            return false;
        }

        NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanImportDirectory($strArchiveFile);
        }

        $objNarroImporter->ImportProjectArchive($strArchiveFile);

        NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));
        NarroLog::LogMessage(3, sprintf(t('Import took %d seconds'), NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));
     }
     elseif (in_array('--export', $argv)) {

        $objNarroImporter = new NarroProjectImporter();
        NarroLog::$blnEchoOutput = false;

        if (array_search('--minloglevel', $argv))
            $objNarroImporter->MinLogLevel = $argv[array_search('--minloglevel', $argv)+1];
            
        if (array_search('--exported-suggestion', $argv))
            $objNarroImporter->ExportedSuggestion = $argv[array_search('--exported-suggestion', $argv)+1];
            
        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--source-lang', $argv) !== false)
            $strSourceLanguage = $argv[array_search('--source-lang', $argv)+1];
        else
            $strSourceLanguage = 'en_US';

        if (array_search('--target-lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--target-lang', $argv)+1];

        if (array_search('--user', $argv) !== false)
            $intUserId = $argv[array_search('--user', $argv)+1];



        $objUser = NarroUser::Load($intUserId);
        if (!$objUser instanceof NarroUser) {
            NarroLog::LogMessage(2, sprintf(t('User id=%s does not exist in the database, will try to use the anonymous user.'), $intUserId));
            $objUser = NarroUser::Load(NarroUser::ANONYMOUS_USER_ID);
            if (!$objUser instanceof NarroUser) {
                NarroLog::LogMessage(3, sprintf(t('The anonymous user id=%s does not exist in the database.'), $intUserId));
                return false;
            }
        }

        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            NarroLog::LogMessage(3, sprintf(t('Project with id=%s does not exist in the database.'), $intProjectId));
            return false;
        }

        $strArchiveFile = str_replace('"','', $argv[$argc-1]);

        if (!file_exists($strArchiveFile)) {
            NarroLog::LogMessage(3, sprintf(t('File "%s" does not exist.'), $strArchiveFile));
            return false;
        }

        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), $strTargetLanguage));
            return false;
        }

        QApplication::$objUser->Language = $objLanguage;

        $objNarroImporter->TargetLanguage = $objLanguage;

        NarroLog::LogMessage(3, sprintf(t('Target language is %s'), $objNarroImporter->TargetLanguage->LanguageName));

        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode($strSourceLanguage);
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), $strSourceLanguage));
            return false;
        }

        NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageName));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        if (in_array('--force', $argv)) {
            $objNarroImporter->CleanExportDirectory($strArchiveFile);
        }

        $objNarroImporter->ExportProjectArchive($strArchiveFile);

        NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));
        NarroLog::LogMessage(2, sprintf(t('Export took %d seconds'), NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));

     }


?>
