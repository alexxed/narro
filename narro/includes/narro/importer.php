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
    elseif (in_array('--import-po', $argv)) {

        include_once(dirname(__FILE__) . '/narro_po_importer.class.php');
        $objNarroImporter = new NarroPoImporter();
        $intProjectId = $argv[array_search('--project_id', $argv)+1];
        $strArchiveFile = str_replace('"','', $argv[array_search('--archive', $argv)+1]);
        $objNarroImporter->ImportProjectArchive($intProjectId, $strArchiveFile);

     }
    elseif (in_array('--delete-project-files', $argv)) {
        $intProjectId = $argv[array_search('--project_id', $argv)+1];

        $strQuery = sprintf("UPDATE `narro_context` SET valid_suggestion_id=NULL, popular_suggestion_id=NULL WHERE project_id=%d", $intProjectId);

        if (!$objResult = db_query($strQuery)) {
            error_log( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
            return false;
        }

        $strQuery = sprintf("DELETE FROM `narro_context` WHERE project_id=%d", $intProjectId);

        if (!$objResult = db_query($strQuery)) {
            error_log( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
            return false;
        }

            $strQuery = sprintf("DELETE FROM `narro_file` WHERE project_id=%d", $intProjectId);

        if (!$objResult = db_query($strQuery)) {
            error_log( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
            return false;
        }


    }
    elseif (in_array('--search-texts-suggestions', $argv))
        $objNarroImporter->SearchTextsAndSuggestions($argv[1], $argv[2]);
    elseif (in_array('--index-suggestions', $argv))
        $objNarroImporter->IndexSuggestions();
    elseif (in_array('--import-sdf', $argv)) {

        include_once(dirname(__FILE__) . '/narro_ooo_file_importer.class.php');
        $objNarroImporter = new NarroOooFileImporter();
        if (array_search('--project', $argv))
            $intProjectId = $argv[array_search('--project', $argv)+1];
        if (array_search('--minloglevel', $argv))
            $objNarroImporter->MinLogLevel = $argv[array_search('--loglevel', $argv)+1];
        if (array_search('--validate', $argv))
            $blnValidate = true;
        if (array_search('--check-equal', $argv))
            $blnCheckEqual = true;
        if (array_search('--only-suggestions', $argv))
            $blnOnlySuggestions = true;
        if (array_search('--source-lang', $argv))
            $strSourceLang = $argv[array_search('--source-lang', $argv)+1];
        if (array_search('--target-lang', $argv))
            $strTargetLang = $argv[array_search('--target-lang', $argv)+1];
        if (array_search('--template', $argv))
            $strTemplateFile = str_replace('"','', $argv[array_search('--template', $argv)+1]);
        $objNarroImporter->ImportSdfFile($intProjectId, $strSourceLang, $strTargetLang, $strTemplateFile, $blnCheckEqual, $blnValidate , $blnOnlySuggestions);

    }
    elseif (in_array('--export-mozilla', $argv)) {

        include_once(dirname(__FILE__) . '/narro_mozilla_file_importer.class.php');
        $objNarroImporter = new NarroMozillaFileImporter();
        $intProjectId = $argv[array_search('--project_id', $argv)+1];
        $strArchiveFile = str_replace('"','', $argv[array_search('--archive', $argv)+1]);
        $objNarroImporter->ExportProjectArchive($intProjectId, $strArchiveFile);

    }
    elseif (in_array('--export-sdf', $argv)) {
        include_once(dirname(__FILE__) . '/narro_ooo_file_importer.class.php');
        $objNarroImporter = new NarroOooFileImporter();
        $intProjectId = $argv[array_search('--project_id', $argv)+1];
        $strTemplateFile = str_replace('"','', $argv[array_search('--template', $argv)+1]);
        $strTargetFile = str_replace('"','', $argv[array_search('--output', $argv)+1]);
        $objNarroImporter->ExportSdfFile($intProjectId, $strTemplateFile, $strTargetFile);
    }
    elseif (in_array('--import-suggestions', $argv))
        $objNarroImporter->ImportSuggestionsTextFile($argv[1], $argv[2], str_replace('"','', $argv[3]), true);

?>