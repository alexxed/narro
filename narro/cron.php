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

    require_once('includes/configuration/prepend.inc.php');

    if (!isset($argv)) exit;

    if (array_search('--project', $argv) !== false) {
        $intProjectId = $argv[array_search('--project', $argv)+1];
        $objProjCondition = QQ::Equal(QQN::NarroProject()->ProjectId, $intProjectId);
    }
    else
        $objProjCondition = QQ::All();

    if (array_search('--translation-lang', $argv) !== false) {
        $strTargetLanguage = $argv[array_search('--translation-lang', $argv)+1];
        $objLangCondition = QQ::Equal(QQN::NarroLanguage()->LanguageCode, $strTargetLanguage);
    }
    else
        $objLangCondition = QQ::All();

    /**
     * Export all projects, all languages
     */
    foreach(NarroProject::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroProject()->Active, 1), $objProjCondition)) as $objProject) {
        foreach(NarroLanguage::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->Active, 1), $objLangCondition)) as $intCnt=>$objLanguage) {
            echo "Exporting $objProject->ProjectName $objLanguage->LanguageName\n";
                passthru(
                    sprintf('%s %s/includes/narro/importer/narro-cli.php ' .
                    '--export --exported-suggestion 1 --copy-unhandled-files '.
                    '--project ' . $objProject->ProjectId . ' '.
                    '--user 0 '.
                    '--template-lang en-US '.
                    '--translation-lang ' . $objLanguage->LanguageCode . ' '.
                    '--template-directory %s/data/import/' . $objProject->ProjectId . '/en-US '.
                    '--translation-directory %s/data/import/' . $objProject->ProjectId . '/' . $objLanguage->LanguageCode,
                    __PHP_CLI_PATH__,
                    __DOCROOT__ . __SUBDIRECTORY__,
                    __DOCROOT__ . __SUBDIRECTORY__,
                    __DOCROOT__ . __SUBDIRECTORY__
                    )
                );
        }
    }

    /**
     * Import all projects, all languages
     */
    foreach(NarroProject::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroProject()->Active, 1), $objProjCondition)) as $objProject) {
        foreach(NarroLanguage::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->Active, 1), $objLangCondition)) as $intCnt=>$objLanguage) {
            echo "Importing $objProject->ProjectName $objLanguage->LanguageName\n";
                passthru(
                    sprintf('%s %s/includes/narro/importer/narro-cli.php ' .
                    '--import '.
                    '--import-unchanged-files '.
                    (($intCnt == 0)?'':'--do-not-deactivate-files --do-not-deactivate-contexts --only-suggestions ').
                    '--project ' . $objProject->ProjectId . ' '.
                    '--user 0 '.
                    '--approve '.
                    '--check-equal '.
                    '--template-lang en-US '.
                    '--translation-lang ' . $objLanguage->LanguageCode . ' '.
                    '--template-directory %s/data/import/' . $objProject->ProjectId . '/en-US '.
                    '--translation-directory %s/data/import/' . $objProject->ProjectId . '/' . $objLanguage->LanguageCode,
                    __PHP_CLI_PATH__,
                    __DOCROOT__ . __SUBDIRECTORY__,
                    __DOCROOT__ . __SUBDIRECTORY__,
                    __DOCROOT__ . __SUBDIRECTORY__
                    )
                );
        }
    }

    /**
     * remove old sessions and form states
     */
    exec("find " . __DOCROOT__ . __SUBDIRECTORY__ . "/data/tmp/qform_state -mtime +1 -exec rm -f {} \;");
    exec("find " . __DOCROOT__ . __SUBDIRECTORY__ . "/data/tmp -maxdepth 1 -type f -mtime +1 -exec rm -f {} \;");
    exec("find " . __DOCROOT__ . __SUBDIRECTORY__ . "/data/tmp/session -mtime +10 -exec rm -f {} \;");
