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

    if (!isset($argv[2])) {
        echo 'Wrong parameters: ' . var_export($argv,true) . "\n";
        exit();
    }

    require_once(dirname(__FILE__) . '/../../prepend.inc.php');
    require_once(dirname(__FILE__) . '/NarroProjectImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaIncFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaDtdFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroMozillaIniFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroGettextPoFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroOpenOfficeSdfFileImporter.class.php');
    require_once(dirname(__FILE__) . '/NarroImportStatistics.class.php');
    require_once(dirname(__FILE__) . '/NarroLog.class.php');
    require_once(dirname(__FILE__) . '/NarroRss.class.php');
    require_once(dirname(__FILE__) . '/NarroMozilla.class.php');

    if (in_array('--import-mozilla', $argv)) {

        $objNarroImporter = new NarroProjectImporter();

        NarroLog::$blnEchoOutput = false;

        $objNarroImporter->CheckEqual = true;
        $objNarroImporter->Validate = true;

        if (array_search('--minloglevel', $argv))
            NarroLog::$intMinLogLevel = $argv[array_search('--minloglevel', $argv)+1];

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--lang', $argv)+1];

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

        NarroLog::LogMessage(3, sprintf(t('Target language is %s'), $strTargetLanguage));

        $objNarroImporter->TargetLanguage = $objLanguage;

        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en_US');
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), 'en_US'));
            return false;
        }

        NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageCode));

        $objNarroImporter->SourceLanguage->LanguageCode = 'en-US';
        $objNarroImporter->TargetLanguage->LanguageCode = str_replace('_', '-', $objNarroImporter->TargetLanguage->LanguageCode);
        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        $objNarroImporter->ImportProjectArchive($strArchiveFile);

        NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));
        NarroLog::LogMessage(3, sprintf(t('Import took %d seconds'), NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));

        NarroRss::Save($objNarroImporter->Project, $objNarroImporter->TargetLanguage);

     }
     elseif (in_array('--export-mozilla', $argv)) {

        $objNarroImporter = new NarroProjectImporter();
        NarroLog::$blnEchoOutput = false;
        NarroLog::$strLogFile = 'exporter.log';

        if (array_search('--minloglevel', $argv))
            $objNarroImporter->MinLogLevel = $argv[array_search('--minloglevel', $argv)+1];

        if (array_search('--project', $argv) !== false)
            $intProjectId = $argv[array_search('--project', $argv)+1];

        if (array_search('--lang', $argv) !== false)
            $strTargetLanguage = $argv[array_search('--lang', $argv)+1];

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

        NarroLog::LogMessage(2, sprintf(t('Target language is %s'), $strTargetLanguage));

        $objNarroImporter->TargetLanguage = $objLanguage;

        $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en_US');
        if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
            NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), 'en_US'));
            return false;
        }
        $objNarroImporter->SourceLanguage->LanguageCode = 'en-US';

        NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageCode));

        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        $objNarroImporter->ExportProjectArchive($strArchiveFile);

        NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));
        NarroLog::LogMessage(2, sprintf(t('Export took %d seconds'), NarroImportStatistics::$arrStatistics['End time'] - NarroImportStatistics::$arrStatistics['Start time']));

     }


?>