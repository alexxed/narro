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
    QApplication::$LogFile = sprintf('%s/import_all.log', __TMP_PATH__);

    if (!isset($argv)) exit;
    QFirebug::setEnabled(false);

    if (array_search('--help', $argv) !== false) {
        echo
            sprintf(
                    "php %s [options]\n" .
                    "--template-lang              language code for the original texts, optional, defaults to %s\n" .
                    "--translation-lang           language code for the translations\n" .
                    "--template-directory         the directory that holds the original texts" .
                    "--translation-directory      the directory that holds the translations" .
                    "--user                       user id that will be used for the added\n" .
                    "--disable-plugins            disable plugins during import/export\n" .
                    "                             suggestions, optional, defaults to anonymous\n" .
                    "--do-not-deactivate-files    do not deactivate project files before importing\n" .
                    "--do-not-deactivate-contexts do not deactivate project contexts before\n" .
                    "                             importing\n" .
                    "--do-not-check-equal         don't check if the translation is equal to the original\n" .
                    "                             text and don't import it\n" .
                    "--skip-untranslated          skip likes that don't have translated texts\n" .
                    "--approve                    approve the imported suggestions\n" .
                    "--approve-already-approved   overwrite translations approved in Narro\n" .
                    "--import-unchanged-files     import files marked unchanged after the last import\n" .
                    "--only-suggestions           import only suggestions, don't add files, texts\n" .
                    "                             or contexts\n",
                basename(__FILE__),
                NarroLanguage::SOURCE_LANGUAGE_CODE
            )
        ;
        exit();
    }

    $intProjCnt = NarroProject::CountByActive(1);
    $intLangCnt = NarroLanguage::CountAllActive();

    $intStartTime = time();
    foreach(NarroProject::LoadArrayByActive(1) as $intProjIdx=>$objProject) {
        foreach(NarroLanguage::LoadAllActive() as $intLangIdx=>$objLanguage) {
            if (in_array('--progress', $argv)) {
                $strProjectProgress = '';
                for($i=1;$i<11;$i++) {
                  if (($intProjIdx * 10)/$intProjCnt <= $i)
                      $strProjectProgress .= '-';
                  else
                      $strProjectProgress .= '+';
                }

                $strLanguageProgress = '';
                for($i=1;$i<11;$i++) {
                  if (($intLangIdx * 10)/$intLangCnt <= $i)
                      $strLanguageProgress .= '-';
                  else
                      $strLanguageProgress .= '+';
                }

                $objDateSpan = new QDateTimeSpan(time() - $intStartTime);
                printf("\rProject %s [%s], %d/%d, language %s, [%s], %d/%d, started %s ago               ", $objProject->ProjectName, $strProjectProgress, $intProjIdx, $intProjCnt, $objLanguage->LanguageName, $strLanguageProgress, $intLangIdx, $intLangCnt, $objDateSpan->SimpleDisplay());
                @ob_flush();
            }

            QApplication::$TargetLanguage = $objLanguage;
            QApplication::$LogFile = sprintf('%s/project-%d-%s.log', __TMP_PATH__, $objProject->ProjectId, $objLanguage->LanguageCode);
            QApplication::$Logger = new Zend_Log();
            QApplication::$Logger->addWriter(new Zend_Log_Writer_Stream(QApplication::$LogFile));

            $objProjectProgress = NarroProjectProgress::LoadByProjectIdLanguageId($objProject->ProjectId, $objLanguage->LanguageId);

            if (!$objProjectProgress || $objProjectProgress->Active) {
                $objNarroImporter = new NarroProjectImporter();

                /**
                 * Get boolean options
                 */
                $objNarroImporter->CheckEqual = !(bool) array_search('--do-not-check-equal', $argv);
                $objNarroImporter->Approve = (bool) array_search('--approve', $argv);
                $objNarroImporter->ApproveAlreadyApproved = (bool) array_search('--approve-already-approved', $argv);
                $objNarroImporter->OnlySuggestions = (bool) array_search('--only-suggestions', $argv) || $intLangIdx > 0;
                $objNarroImporter->ImportUnchangedFiles = (bool) array_search('--import-unchanged-files', $argv);
                NarroPluginHandler::$blnEnablePlugins = !(bool) array_search('--disable-plugins', $argv);

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

                $objNarroImporter->TemplatePath = $objNarroImporter->Project->DefaultTemplatePath;
                $objNarroImporter->TranslationPath = $objNarroImporter->Project->DefaultTranslationPath;

                try {
                    $intPid = NarroUtils::IsProcessRunning('import', $objNarroImporter->Project->ProjectId);

                    if ($intPid && $intPid <> getmypid())
                        throw new Exception(sprintf('An import process is already running for this project with pid %d', $intPid));

                    $strProcPidFile = __TMP_PATH__ . '/' . $objNarroImporter->Project->ProjectId . '-' . $objNarroImporter->TargetLanguage->LanguageCode . '-import-process.pid';
                    if (file_exists($strProcPidFile))
                        unlink($strProcPidFile);

                    file_put_contents($strProcPidFile, getmypid());

                    $blnResult = $objNarroImporter->ImportProject();
                }
                catch (Exception $objEx) {
                    QApplication::LogError(sprintf('An error occurred during import: %s', $objEx->getMessage()));
                    exit();
                }

                if ($blnResult)
                    foreach(NarroImportStatistics::$arrStatistics as $strName=>$strValue) {
                        if ($strName == 'Start time')
                            $strValue = date('Y-m-d H:i:s', $strValue);

                        if ($strName == 'End time')
                            $strValue = date('Y-m-d H:i:s', $strValue);

                        if ($strValue != 0)
                            QApplication::LogInfo(stripslashes($strName) . ': ' . $strValue);
                    }
            }
        }
    }