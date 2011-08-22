<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2011 Alexandru Szasz <alexxed@gmail.com>
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

    require_once(dirname(__FILE__) . '/../configuration/prepend.inc.php');

    if (!isset($argv)) exit;
    QFirebug::setEnabled(false);

    foreach(NarroProject::LoadArrayByActive(1) as $objProject) {
        foreach(NarroLanguage::LoadAllActive() as $objLanguage) {
            if (array_search('--verbose', $argv)) {
                echo $objLanguage->LanguageName . "\n";
                ob_flush();
            }
            QApplication::$TargetLanguage = $objLanguage;
            $objProjectProgress = NarroProjectProgress::LoadByProjectIdLanguageId($objProject->ProjectId, $objLanguage->LanguageId);

            if (!$objProjectProgress || $objProjectProgress->Active) {
                $strProcLogFile = __TMP_PATH__ . '/' . $objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.log';
                $strProcPidFile = __TMP_PATH__ . '/' . $objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.pid';
                $strProgressFile = __TMP_PATH__ . '/import-' . $objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode;

                QApplication::ClearLog();
                NarroProgress::ClearProgressFileName($objProject->ProjectId, 'import');
                set_time_limit(0);

                if (file_exists($strProcLogFile))
                    unlink($strProcLogFile);

                if (file_exists($strProcPidFile))
                    unlink($strProcPidFile);

                if (file_exists($strProgressFile))
                    unlink($strProgressFile);

                $objNarroImporter = new NarroProjectImporter();

                /**
                 * Get boolean options
                 */
                $objNarroImporter->CheckEqual = true;
                $objNarroImporter->Approve = true;
                $objNarroImporter->ApproveAlreadyApproved = false;
                $objNarroImporter->OnlySuggestions = false;
                $objNarroImporter->Project = $objProject;
                $objNarroImporter->User = NarroUser::LoadAnonymousUser();
                $objNarroImporter->TargetLanguage = QApplication::$TargetLanguage;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
                $objNarroImporter->TranslationPath = $objProject->DefaultTranslationPath;
                $objNarroImporter->TemplatePath = $objProject->DefaultTemplatePath;
                $objNarroImporter->ImportProject();
            }
        }
    }