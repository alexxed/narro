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

    require_once('includes/prepend.inc.php');

    include_once(dirname(__FILE__) . '/narro_file_importer.class.php');

    if (in_array('--import-mozilla', $argv)) {

        include_once(dirname(__FILE__) . '/narro_mozilla_file_importer.class.php');
        $objNarroImporter = new NarroMozillaFileImporter();
        $objNarroImporter->EchoOutput = false;

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
            $objNarroImporter->Output(2, sprintf(t('User id=%s does not exist in the database, will try to use the anonymous user.'), $intUserId));
            $objUser = NarroUser::Load(NarroUser::ANONYMOUS_USER_ID);
            if (!$objUser instanceof NarroUser) {
                $objNarroImporter->Output(3, sprintf(t('The anonymous user id=%s does not exist in the database.'), $intUserId));
                return false;
            }
        }

        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            $objNarroImporter->Output(3, sprintf(t('Project with id=%s does not exist in the database.'), $intProjectId));
            return false;
        }

        $strArchiveFile = str_replace('"','', $argv[$argc-1]);

        if (!file_exists($strArchiveFile)) {
            $objNarroImporter->Output(3, sprintf(t('File "%s" does not exist.'), $strArchiveFile));
            return false;
        }

        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            $objNarroImporter->Output(3, sprintf(t('Language %s does not exist in the database.'), $strTargetLanguage));
            return false;
        }

        $objNarroImporter->Output(2, sprintf(t('Target language is %s'), $strTargetLanguage));

        $objNarroImporter->Language = $objLanguage;
        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        $objNarroImporter->ImportProjectArchive($strArchiveFile);

        $objNarroImporter->Output(2, var_export($objNarroImporter->Statistics, true));
        $objNarroImporter->Output(2, sprintf(t('Import took %d seconds'), $objNarroImporter->Statistics['End time'] - $objNarroImporter->Statistics['Start time']));

     }
     elseif (in_array('--export-mozilla', $argv)) {

        include_once(dirname(__FILE__) . '/narro_mozilla_file_importer.class.php');
        $objNarroImporter = new NarroMozillaFileImporter();
        $objNarroImporter->EchoOutput = false;

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
            $objNarroImporter->Output(2, sprintf(t('User id=%s does not exist in the database, will try to use the anonymous user.'), $intUserId));
            $objUser = NarroUser::Load(NarroUser::ANONYMOUS_USER_ID);
            if (!$objUser instanceof NarroUser) {
                $objNarroImporter->Output(3, sprintf(t('The anonymous user id=%s does not exist in the database.'), $intUserId));
                return false;
            }
        }

        $objProject = NarroProject::Load($intProjectId);
        if (!$objProject instanceof NarroProject) {
            $objNarroImporter->Output(3, sprintf(t('Project with id=%s does not exist in the database.'), $intProjectId));
            return false;
        }

        $strArchiveFile = str_replace('"','', $argv[$argc-1]);

        if (!file_exists($strArchiveFile)) {
            $objNarroImporter->Output(3, sprintf(t('File "%s" does not exist.'), $strArchiveFile));
            return false;
        }

        $objLanguage = NarroLanguage::LoadByLanguageCode($strTargetLanguage);
        if (!$objLanguage instanceof NarroLanguage) {
            $objNarroImporter->Output(3, sprintf(t('Language %s does not exist in the database.'), $strTargetLanguage));
            return false;
        }

        $objNarroImporter->Output(2, sprintf(t('Target language is %s'), $strTargetLanguage));

        $objNarroImporter->Language = $objLanguage;
        $objNarroImporter->Project = $objProject;
        $objNarroImporter->User = $objUser;

        $objNarroImporter->ExportProjectArchive($strArchiveFile);

        $objNarroImporter->Output(2, var_export($objNarroImporter->Statistics, true));
        $objNarroImporter->Output(2, sprintf(t('Export took %d seconds'), $objNarroImporter->Statistics['End time'] - $objNarroImporter->Statistics['Start time']));

     }


?>