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

    include_once './includes/bootstrap.inc';
    drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

    include_once(dirname(__FILE__) . '/narro_file_importer.class.php');

    if (!defined('__INCLUDES__'))
        define('__INCLUDES__', realpath(dirname(__FILE__) . '/../../../../includes/qcodo/includes'));
    set_include_path(get_include_path() . PATH_SEPARATOR . __INCLUDES__);
    _qdrupal_bootstrap();

    if (in_array('--import-mozilla', $argv)) {

        include_once(dirname(__FILE__) . '/narro_mozilla_file_importer.class.php');
        $objNarroImporter = new NarroMozillaFileImporter();
        $intProjectId = $argv[array_search('--project_id', $argv)+1];
        $strArchiveFile = str_replace('"','', $argv[array_search('--archive', $argv)+1]);
        $objNarroImporter->ImportProjectArchive($intProjectId, $strArchiveFile);

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

        $strQuery = sprintf("UPDATE `narro_text_context` SET valid_suggestion_id=NULL, popular_suggestion_id=NULL WHERE project_id=%d", $intProjectId);

        if (!$objResult = db_query($strQuery)) {
            error_log( __METHOD__ . ':' . __LINE__ . ':db_query failed. $strQuery=' . $strQuery );
            return false;
        }

        $strQuery = sprintf("DELETE FROM `narro_text_context` WHERE project_id=%d", $intProjectId);

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
        $intProjectId = $argv[array_search('--project_id', $argv)+1];
        $strTemplateFile = str_replace('"','', $argv[array_search('--template', $argv)+1]);
        $objNarroImporter->ImportSdfFile($intProjectId, $strTemplateFile);

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