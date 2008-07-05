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

    require_once('includes/prepend.inc.php');
    require_once('includes/narro/narro_progress_bar.class.php');
    require_once(__INCLUDES__ . '/archive.class.php');

    class NarroProjectManageForm extends QForm {
        protected $objNarroProject;

        protected $pnlLogViewer;

        /**
         * common options
         */
        protected $chkForce;
        protected $lstLogLevel;

        /**
         * import controls and options
         */
        protected $btnImport;
        protected $flaImportFromFile;
        protected $objImportProgress;
        protected $lblImport;

        protected $chkDoNotDeactivateFiles;
        protected $chkDoNotDeactivateContexts;
        protected $chkValidate;
        protected $chkOnlySuggestions;
        protected $chkCheckEqual;

        /**
         * export controls and options
         */
        protected $btnExport;
        protected $flaExportFromFile;
        protected $objExportProgress;
        protected $lblExport;

        protected $lstExportedSuggestion;
        protected $lstExportArchiveType;

        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            // Set mode to Edit or New depending on what's found
            $intProjectId = QApplication::QueryString('p');
            if ($intProjectId > 0) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                if (!$this->objNarroProject)
                    QApplication::Redirect('narro_project_list.php');

            } else
                QApplication::Redirect('narro_project_list.php');

        }

        protected function Form_Create() {

            $this->SetupNarroProject();

            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                QApplication::Redirect('narro_project_list.php');

            $this->pnlLogViewer = new QPanel($this);
            $this->pnlLogViewer->Visible = false;

            $this->lstExportedSuggestion = new QListBox($this);
            $this->lstExportedSuggestion->AddItem(t('The validated suggestion'), 1);
            $this->lstExportedSuggestion->AddItem(t('The most voted suggestion'), 2);
            $this->lstExportedSuggestion->AddItem(t('My suggestion'), 3);
            $this->lstExportedSuggestion->Enabled = false;

            $this->lstLogLevel = new QListBox($this);
            $this->lstLogLevel->AddItem(1, 1);
            $this->lstLogLevel->AddItem(2, 2);
            $this->lstLogLevel->AddItem(3, 3, true);

            $this->chkCheckEqual = new QCheckBox($this);
            $this->chkCheckEqual->Checked = true;

            $this->chkDoNotDeactivateFiles = new QCheckBox($this);
            $this->chkDoNotDeactivateFiles->Checked = true;

            $this->chkDoNotDeactivateContexts = new QCheckBox($this);
            $this->chkDoNotDeactivateContexts->Checked = true;

            $this->chkValidate = new QCheckBox($this);
            $this->chkValidate->Checked = true;

            $this->chkOnlySuggestions = new QCheckBox($this);


            $this->flaImportFromFile = new QFileAsset($this);
            $this->flaImportFromFile->TemporaryUploadPath = __TMP_PATH__;
            $this->flaImportFromFile->FileAssetType = QFileAssetType::Archive;

            $this->objImportProgress = new NarroTranslationProgressBar($this);
            $this->objImportProgress->Total = 100;
            $this->objImportProgress->Visible = false;

            $this->btnImport = new QButton($this);
            $this->btnImport->Text = t('Import');
            $this->btnImport->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('document.getElementById(\'%s\').disabled=true;document.getElementById(\'%s\_ctl\').visible=true;', $this->btnImport->ControlId, $this->objImportProgress->ControlId)));
            if (function_exists('proc_open') && QApplication::$blnUseAjax)
                $this->btnImport->AddAction(new QClickEvent(), new QAjaxAction('btnImport_Click'));
            else
                $this->btnImport->AddAction(new QClickEvent(), new QServerAction('btnImport_Click'));

            $this->lblImport = new QLabel($this);
            $this->lblImport->Visible = false;

            $this->flaExportFromFile = new QFileAsset($this);
            $this->flaExportFromFile->TemporaryUploadPath = __TMP_PATH__;
            $this->flaExportFromFile->FileAssetType = QFileAssetType::Archive;

            $this->btnExport = new QButton($this);
            $this->btnExport->Text = t('Export');
            if (function_exists('proc_open') && QApplication::$blnUseAjax)
                $this->btnExport->AddAction(new QClickEvent(), new QAjaxAction('btnExport_Click'));
            else
                $this->btnExport->AddAction(new QClickEvent(), new QServerAction('btnExport_Click'));

            $this->objExportProgress = new NarroTranslationProgressBar($this);
            $this->objExportProgress->Total = 100;
            $this->objExportProgress->Visible = false;

            $this->lblExport = new QLabel($this);
            $this->lblExport->Visible = false;
            $this->lblExport->HtmlEntities = false;

            $this->lstExportArchiveType = new QListBox($this);
            $this->lstExportArchiveType->AddItem('tar.gz', 'tar.gz');
            $this->lstExportArchiveType->AddItem('tar.bz2', 'tar.bz2');
            $this->lstExportArchiveType->AddItem('zip', 'zip');

            $this->chkForce = new QCheckBox($this);
            $this->chkForce->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('document.getElementById(\'%s\').disabled = false', $this->btnImport->ControlId)));
            $this->chkForce->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('document.getElementById(\'%s\').disabled = false', $this->btnExport->ControlId)));

            if (file_exists(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . QApplication::$objUser->Language->LanguageCode . '/import.pid')) {
                $this->btnImport->Enabled = false;
                $this->objImportProgress->Visible = true;
                $strImportPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
                NarroProgress::SetProgressFile($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.progress');
                $this->objImportProgress->Translated = NarroProgress::GetProgress();
                QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->FormId, $this->btnImport->ControlId, 2000));
            }

            if (file_exists(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . QApplication::$objUser->Language->LanguageCode . '/export.pid')) {
                $this->btnExport->Enabled = false;
                $this->objExportProgress->Visible = true;
                $strImportPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
                NarroProgress::SetProgressFile($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.progress');
                $this->objExportProgress->Translated = NarroProgress::GetProgress();
                QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->FormId, $this->btnExport->ControlId, 2000));
            }

            $this->Form_PreRender();

        }

        public function Form_PreRender() {
        }

        public function btnImport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            $strImportPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
            NarroProgress::SetProgressFile($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.progress');
            NarroLog::SetLogFile($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.log');

            if ($strParameter != 1) {
                $this->btnImport->Enabled = false;
                $this->objImportProgress->Visible = true;
                $this->pnlLogViewer->Text = '';
                $this->lblImport->Text = '';

                if (file_exists($this->flaImportFromFile->File)) {
                    $strWorkDir = __TMP_PATH__ . '/import-project-' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode;
                    if (file_exists($strWorkDir))
                        NarroUtils::RecursiveDelete($strWorkDir);
                    mkdir($strWorkDir);
                    chmod($strWorkDir, 0777);
                    chdir($strWorkDir);

                    $strExportArchive = $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.' . $this->lstExportArchiveType->SelectedValue;

                    if (file_exists($strExportPath . '/' . $strExportArchive))
                        unlink($strExportPath . '/' . $strExportArchive);

                    if (strstr($this->flaImportFromFile->File, '.bz2')) {
                        $objArchiver = new bzip_file($this->flaImportFromFile->File);
                    }
                    elseif (strstr($this->flaImportFromFile->File, '.gz')) {
                        $objArchiver = new gzip_file($this->flaImportFromFile->File);
                    }
                    elseif (strstr($this->flaImportFromFile->File, '.zip')) {
                        $objArchiver = new zip_file($this->flaImportFromFile->File);
                    }
                    else {
                        NarroLog::LogMessage(3, t('Unable to detect the type for the uploaded file'));
                    }

                    if (isset($objArchiver)) {
                        $objArchiver->set_options(array('overwrite' => 1));
                        $objArchiver->extract_files();
                        if (count($objArchiver->errors) == 0) {
                            NarroLog::LogMessage(3, join("\n", $objArchiver->errors));
                        }
                        NarroUtils::RecursiveChmod($strWorkDir);
                    }

                    //exec('tar jxvf ' . $this->flaImportFromFile->File);
                    if (!file_exists($strWorkDir . '/en-US') && !file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                        NarroLog::LogMessage(3, sprintf(t('The uploaded archive should have at least one directory named "en-US" or one named "%s" that contains the files with the original texts'), QApplication::$objUser->Language->LanguageCode));
                        $this->lblImport->Text = t('Import failed.');
                        NarroUtils::RecursiveDelete($strWorkDir);

                        if (file_exists($this->flaImportFromFile->File))
                            unlink($this->flaImportFromFile->File);

                        $this->btnImport->Enabled = true;
                        $this->objImportProgress->Visible = false;
                        $this->showLog();
                        return false;
                    }
                    else {
                        if (file_exists($strWorkDir . '/en-US')) {
                            NarroUtils::RecursiveDelete($strImportPath . '/en-US');
                            NarroUtils::RecursiveCopy($strWorkDir . '/en-US', __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/en-US');
                            NarroUtils::RecursiveChmod(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/en-US');
                        }

                        if (file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                            NarroUtils::RecursiveDelete($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/*');
                            NarroUtils::RecursiveCopy($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode, __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . QApplication::$objUser->Language->LanguageCode);
                            NarroUtils::RecursiveChmod(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . QApplication::$objUser->Language->LanguageCode);
                        }

                        NarroUtils::RecursiveDelete($strWorkDir);

                        NarroLog::LogMessage(3, sprintf(t('The directories "%s" and "%s" from the uploaded archive "%s" were extracted to "%s"'), 'en-US', QApplication::$objUser->Language->LanguageCode, $this->flaImportFromFile->FileName, __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId));
                    }

                }

                /**
                 * refresh the page to show the progress. keep the interval id as a global variable (no var before it) to clear it afterwards
                 */
                if (function_exists('proc_open') && QApplication::$blnUseAjax)
                    QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 2000));
            }
            else {
                if (!file_exists($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.pid')) {
                    $this->lblImport->Text = t('Import finished.');

                    if (file_exists($this->flaImportFromFile->File))
                        unlink($this->flaImportFromFile->File);

                    QApplication::ExecuteJavaScript('if (typeof lastImportId != \'undefined\') clearInterval(lastImportId)');

                    $this->showLog();

                    $this->lblImport->Visible = true;
                    $this->btnImport->Enabled = true;
                    $this->objImportProgress->Translated = 0;
                    $this->objImportProgress->Visible = false;
                }
                else {
                    $this->objImportProgress->Translated = NarroProgress::GetProgress();
                    $this->objImportProgress->MarkAsModified();
                }
                return true;
            }

            chdir(__DOCROOT__ . __SUBDIRECTORY__);

            if (function_exists('proc_open')) {
                $strCommand = sprintf(
                    '/usr/bin/php ' .
                        escapeshellarg('includes/narro/importer/importer.php').
                        ' --import --minloglevel %d --project %d --user %d ' .
                        (($this->chkValidate->Checked)?'--validate ':'') .
                        (($this->chkForce->Checked)?'--force ':'') .
                        (($this->chkOnlySuggestions->Checked)?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                        ' --check-equal --source-lang en-US --target-lang %s',
                    $this->lstLogLevel->SelectedValue,
                    $this->objNarroProject->ProjectId,
                    QApplication::$objUser->UserId,
                    QApplication::$objUser->Language->LanguageCode
                );

                proc_close(proc_open ("$strCommand &", array(), $foo));
            } elseif ($strParameter != 1) {
                set_time_limit(0);

                $objNarroImporter = new NarroProjectImporter();

                NarroLog::$blnEchoOutput = false;

                /**
                 * Get boolean options
                 */
                $objNarroImporter->DeactivateFiles = true;
                $objNarroImporter->DeactivateContexts = true;
                $objNarroImporter->CheckEqual = $this->chkCheckEqual->Checked;
                $objNarroImporter->Validate = $this->chkValidate->Checked;
                $objNarroImporter->OnlySuggestions = $this->chkOnlySuggestions->Checked;
                $objNarroImporter->MinLogLevel = $this->lstLogLevel->SelectedValue;
                $objNarroImporter->Project = $this->objNarroProject;
                $objNarroImporter->User = QApplication::$objUser;
                $objNarroImporter->TargetLanguage = QApplication::$objUser->Language;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en-US');
                $objNarroImporter->TranslationPath = $strImportPath . '/' . $objNarroImporter->TargetLanguage->LanguageCode;
                $objNarroImporter->TemplatePath = $strImportPath . '/' . $objNarroImporter->SourceLanguage->LanguageCode;

                NarroLog::$intMinLogLevel = $this->lstLogLevel->SelectedValue;

                NarroLog::LogMessage(3, sprintf(t('Target language is %s'), $objNarroImporter->TargetLanguage->LanguageName));
                NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageName));
                NarroLog::LogMessage(3, sprintf(t('Importing using templates from %s'), $strImportPath . '/' . $objNarroImporter->SourceLanguage->LanguageCode));

                if ($this->chkForce->Checked)
                    $objNarroImporter->CleanImportDirectory();

                try {
                    $objNarroImporter->ImportProject();
                }
                catch (Exception $objEx) {
                    NarroLog::LogMessage(3, sprintf(t('An error occured during import: %s'), $objEx->getMessage()));
                    $objNarroImporter->CleanImportDirectory();
                    $this->lblImport->Text = t('Import failed.');
                    $this->showLog();
                }

                $this->showLog();

                $objNarroImporter->CleanImportDirectory();
                NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));

                $this->btnImport_Click($strFormId, $strControlId, 1);
            }
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            $strExportPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
            NarroProgress::SetProgressFile($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.progress');
            NarroLog::SetLogFile($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.log');

            if ($strParameter != 1) {
                $this->btnExport->Enabled = false;
                $this->pnlLogViewer->Text = '';
                $this->lblExport->Text = '';

                /**
                 * refresh the page to show the progress. keep the interval id as a global variable (no var before it) to clear it afterwards
                 */
                if (function_exists('proc_open') && QApplication::$blnUseAjax) {
                    $this->objExportProgress->Visible = true;
                    QApplication::ExecuteJavaScript(sprintf('lastExportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 2000));
                }
            }
            else {
                if (!file_exists($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.pid')) {
                    $this->lblExport->Text = t('Export finished.');
                    QApplication::ExecuteJavaScript('if (typeof lastExportId != \'undefined\') clearInterval(lastExportId)');

                    $this->lblExport->Visible = true;
                    $this->btnExport->Enabled = true;
                    $this->objExportProgress->Visible = false;

                    /**
                     * Create an archive
                     */
                    if (file_exists($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode)) {
                        $strExportArchive = $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.' . $this->lstExportArchiveType->SelectedValue;

                        if (file_exists($strExportPath . '/' . $strExportArchive))
                            unlink($strExportPath . '/' . $strExportArchive);

                        switch($this->lstExportArchiveType->SelectedValue) {
                            case 'tar.gz':
                                $objArchiver = new gzip_file($strExportArchive);
                                break;
                            case 'zip':
                                $objArchiver = new zip_file($strExportArchive);
                                break;
                            default:
                                $objArchiver = new bzip_file($strExportArchive);
                        }

                        $objArchiver->set_options(array('basedir' => $strExportPath, 'overwrite' => 1, 'level' => 1));

                        $objArchiver->add_files(QApplication::$objUser->Language->LanguageCode . '/*');
                        $objArchiver->add_files('en-US/*');

                        $objArchiver->exclude_files(QApplication::$objUser->Language->LanguageCode . '/*.log');
                        $objArchiver->exclude_files(QApplication::$objUser->Language->LanguageCode . '/*.pid');
                        $objArchiver->exclude_files(QApplication::$objUser->Language->LanguageCode . '/*.status');

                        $objArchiver->create_archive();

                        // Check for errors (you can check for errors at any point)
                        if (count($objArchiver->errors) == 0) {
                            chmod($strExportPath . '/' . $strExportArchive, 0666);
                            if (file_exists($strExportPath . '/' . $strExportArchive)) {
                                $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $strExportArchive;
                                QApplication::ExecuteJavaScript(sprintf('setInterval("document.location=\'%s\';"), 2000', $strDownloadUrl));
                                $this->objExportProgress->Translated = 0;
                            }
                        }

                        $this->showLog();
                    }
                }
                else {
                    $this->objExportProgress->Translated = NarroProgress::GetProgress();
                    $this->objExportProgress->MarkAsModified();
                }

                return true;
            }

            chdir(__DOCROOT__ . __SUBDIRECTORY__);

            if (function_exists('proc_open')) {
                $strCommand = sprintf(
                    '/usr/bin/php ' .
                        escapeshellarg('includes/narro/importer/importer.php').
                        ' --export --minloglevel %d --project %d --user %d ' .
                        (($this->chkValidate->Checked)?'--validate ':'') .
                        (($this->chkForce->Checked)?'--force ':'') .
                        (($this->chkOnlySuggestions->Checked)?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                        ' --check-equal --source-lang en-US --target-lang %s',
                    $this->lstLogLevel->SelectedValue,
                    $this->objNarroProject->ProjectId,
                    QApplication::$objUser->UserId,
                    QApplication::$objUser->Language->LanguageCode
                );

                proc_close(proc_open ("$strCommand &", array(), $foo));
            }
            elseif($strParameter != 1) {
                set_time_limit(0);

                $objNarroImporter = new NarroProjectImporter();
                NarroLog::$blnEchoOutput = false;
                $objNarroImporter->MinLogLevel = $this->lstLogLevel->SelectedValue;
                NarroLog::$intMinLogLevel = $this->lstLogLevel->SelectedValue;
                $objNarroImporter->ExportedSuggestion = $this->lstExportedSuggestion->SelectedValue;
                $objNarroImporter->TargetLanguage = QApplication::$objUser->Language;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en-US');
                $objNarroImporter->Project = $this->objNarroProject;
                $objNarroImporter->User = QApplication::$objUser;
                $objNarroImporter->TranslationPath = $strExportPath . '/' . QApplication::$objUser->Language->LanguageCode;
                $objNarroImporter->TemplatePath = $strExportPath . '/en-US';

                NarroLog::LogMessage(3, sprintf(t('Source language is %s'), $objNarroImporter->SourceLanguage->LanguageName));
                NarroLog::LogMessage(3, sprintf(t('Target language is %s'), $objNarroImporter->TargetLanguage->LanguageName));
                NarroLog::LogMessage(3, sprintf(t('Exporting using templates from %s'), $strExportPath . '/en-US'));

                if ($this->chkForce->Checked)
                    $objNarroImporter->CleanExportDirectory();

                try {
                    $objNarroImporter->ExportProject();
                }
                catch (Exception $objEx) {
                    NarroLog::LogMessage(3, sprintf(t('An error occured during export: %s'), $objEx->getMessage()));
                    $objNarroImporter->CleanExportDirectory();
                    $this->lblExport->Text = t('Export failed.');
                    $this->showLog();
                }

                $objNarroImporter->CleanExportDirectory();
                NarroLog::LogMessage(2, var_export(NarroImportStatistics::$arrStatistics, true));
                $this->showLog();

                $this->btnExport_Click($strFormId, $strControlId, 1);
            }
        }

        private function showLog() {
            $this->pnlLogViewer->Text = '<div class="dotted_box">
            <div class="dotted_box_title">' . t('Operation log') . '</div>
            <div class="dotted_box_content">' . nl2br(NarroLog::GetLogContents()) . '</div></div></div>';

            $this->pnlLogViewer->Visible = true;
            NarroLog::ClearLog();
        }

    }


    NarroProjectManageForm::Run('NarroProjectManageForm', 'templates/narro_project_manage.tpl.php');
?>
