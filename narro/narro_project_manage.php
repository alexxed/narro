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

    class NarroProjectManageForm extends QForm {
        protected $objNarroProject;

        protected $pnlLogViewer;

        protected $btnDelProject;
        protected $btnDelProjectFiles;
        protected $btnDelProjectContexts;

        protected $lstExportedSuggestion;
        protected $chkForce;
        protected $chkDoNotDeactivateFiles;
        protected $chkDoNotDeactivateContexts;
        protected $chkValidate;
        protected $chkOnlySuggestions;

        protected $btnImport;
        protected $flaImportFromFile;
        protected $objImportProgress;
        protected $lblImport;

        protected $btnExport;
        protected $flaExportFromFile;
        protected $objExportProgress;
        protected $lblExport;

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
            $this->btnImport->AddAction(new QClickEvent(), new QAjaxAction('btnImport_Click'));

            $this->lblImport = new QLabel($this);
            $this->lblImport->Visible = false;

            $this->flaExportFromFile = new QFileAsset($this);
            $this->flaExportFromFile->TemporaryUploadPath = __TMP_PATH__;
            $this->flaExportFromFile->FileAssetType = QFileAssetType::Archive;

            $this->btnExport = new QButton($this);
            $this->btnExport->Text = t('Export');
            $this->btnExport->AddAction(new QClickEvent(), new QAjaxAction('btnExport_Click'));

            $this->objExportProgress = new NarroTranslationProgressBar($this);
            $this->objExportProgress->Total = 100;
            $this->objExportProgress->Visible = false;

            $this->lblExport = new QLabel($this);
            $this->lblExport->Visible = false;
            $this->lblExport->HtmlEntities = false;

            $this->btnDelProjectContexts = new QButton($this);
            $this->btnDelProjectContexts->Text = t('Delete project contexts');

            $this->btnDelProjectFiles = new QButton($this);
            $this->btnDelProjectFiles->Text = t('Delete project files');

            $this->btnDelProject = new QButton($this);
            $this->btnDelProject->Text = t('Delete project');

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

            if ($strParameter != 1) {
                $this->btnImport->Enabled = false;
                $this->objImportProgress->Visible = true;

                if (file_exists($this->flaImportFromFile->File)) {
                    $strWorkDir = __TMP_PATH__ . '/import-project-' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode;
                    exec('rm -rf ' . escapeshellarg($strWorkDir));
                    mkdir($strWorkDir);
                    chmod($strWorkDir, 0777);
                    chdir($strWorkDir);
                    exec('tar jxvf ' . $this->flaImportFromFile->File);
                    if (!file_exists($strWorkDir . '/en-US') && !file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                        NarroLog::LogMessage(3, sprintf(t('The uploaded archive should have at least one directory named "en-US" or one named "%s" that contains the files with the original texts'), QApplication::$objUser->Language->LanguageCode));
                        $this->lblImport->Text = t('Import failed.');
                        $this->showLog();
                        exec('rm -rf ' . $strWorkDir);
                        if (file_exists($this->flaImportFromFile->File))
                            unlink($this->flaImportFromFile->File);
                        $this->btnImport->Enabled = true;
                        $this->objImportProgress->Visible = false;
                        return false;
                    }
                    else {
                        if (file_exists($strWorkDir . '/en-US')) {
                            exec('rm -rf ' . escapeshellarg($strImportPath . '/en-US/*'));
                            exec('cp -R ' . escapeshellarg($strWorkDir . '/en-US') . ' ' . escapeshellarg(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId));
                        }

                        if (file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                            exec('rm -rf ' . escapeshellarg($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/*'));
                            exec('cp -R ' . escapeshellarg($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode) . ' ' . escapeshellarg(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId));
                        }

                        exec('rm -rf ' . escapeshellarg($strWorkDir));
                    }

                }
                /**
                 * refresh the page to show the progress. keep the interval id as a global variable (no var before it) to clear it afterwards
                 */
                QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 2000));
            }
            else {
                if (!file_exists($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.pid')) {
                    $this->lblImport->Text = t('Import finished.');
                    QApplication::ExecuteJavaScript('clearInterval(lastImportId)');

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

            if (file_exists($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.log'))
                unlink($strImportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/import.log');

            chdir(__DOCROOT__ . __SUBDIRECTORY__);
            $strCommand = sprintf(
                '/usr/bin/php ' .
                    escapeshellarg('includes/narro/importer/importer.php').
                    ' --import --minloglevel 3 --project %d --user %d ' .
                    (($this->chkValidate->Checked)?'--validate ':'') .
                    (($this->chkForce->Checked)?'--force ':'') .
                    (($this->chkOnlySuggestions->Checked)?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                    ' --check-equal --source-lang en-US --target-lang %s',
                $this->objNarroProject->ProjectId,
                QApplication::$objUser->UserId,
                QApplication::$objUser->Language->LanguageCode
            );

            proc_close(proc_open ("$strCommand &", array(), $foo));
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            $strExportPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
            NarroProgress::SetProgressFile($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.progress');
            exec('rm -rf ' . escapeshellarg($strExportPath . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.tar.bz2'));

            if ($strParameter != 1) {
                $this->btnExport->Enabled = false;
                $this->objExportProgress->Visible = true;

                if (file_exists($this->flaExportFromFile->File)) {
                    $strWorkDir = __TMP_PATH__ . '/export-project-' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode;
                    exec('rm -rf ' . escapeshellarg($strWorkDir));
                    mkdir($strWorkDir);
                    chmod($strWorkDir, 0777);
                    chdir($strWorkDir);
                    exec('tar jxvf ' . $this->flaExportFromFile->File);
                    if (!file_exists($strWorkDir . '/en-US') && !file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                        NarroLog::LogMessage(3, sprintf(t('The uploaded archive should have at least one directory named "en-US" or one named "%s" that contains the files with the original texts'), QApplication::$objUser->Language->LanguageCode));
                        $this->lblExport->Text = t('Export failed.');
                        $this->showLog();
                        exec('rm -rf ' . $strWorkDir);
                        if (file_exists($this->flaExportFromFile->File))
                            unlink($this->flaExportFromFile->File);
                        $this->btnExport->Enabled = true;
                        $this->objExportProgress->Visible = false;
                        return false;
                    }
                    else {
                        if (file_exists($strWorkDir . '/en-US')) {
                            exec('rm -rf ' . escapeshellarg($strExportPath . '/en-US/*'));
                            exec('cp -R ' . escapeshellarg($strWorkDir . '/en-US') . ' ' . escapeshellarg(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId));
                        }

                        if (file_exists($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode)) {
                            exec('rm -rf ' . escapeshellarg($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/*'));
                            exec('cp -R ' . escapeshellarg($strWorkDir . '/' . QApplication::$objUser->Language->LanguageCode) . ' ' . escapeshellarg(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId));
                        }

                        exec('rm -rf ' . escapeshellarg($strWorkDir));
                    }

                }
                /**
                 * refresh the page to show the progress. keep the interval id as a global variable (no var before it) to clear it afterwards
                 */
                QApplication::ExecuteJavaScript(sprintf('lastExportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 2000));
            }
            else {
                if (!file_exists($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.pid')) {
                    $this->lblExport->Text = t('Export finished.');
                    QApplication::ExecuteJavaScript('clearInterval(lastExportId)');

                    $this->showLog();

                    $this->lblExport->Visible = true;
                    $this->btnExport->Enabled = true;
                    $this->objExportProgress->Visible = false;

                    chdir($strExportPath);
                    if (file_exists(QApplication::$objUser->Language->LanguageCode)) {
                        exec(sprintf('tar cjvf %s %s/* %s/*', escapeshellarg($this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.tar.bz2'), 'en-US',  QApplication::$objUser->Language->LanguageCode));

                        if (file_exists($strExportPath . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.tar.bz2')) {
                            $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$objUser->Language->LanguageCode . '.tar.bz2';
                            QApplication::ExecuteJavaScript(sprintf('setInterval("document.location=\'%s\';"), 2000', $strDownloadUrl));
                            $this->objExportProgress->Translated = 0;
                        }
                    }
                }
                else {
                    $this->objExportProgress->Translated = NarroProgress::GetProgress();
                    $this->objExportProgress->MarkAsModified();
                }
                return true;
            }

            if (file_exists($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.log'))
                unlink($strExportPath . '/' . QApplication::$objUser->Language->LanguageCode . '/export.log');

            chdir(__DOCROOT__ . __SUBDIRECTORY__);
            $strCommand = sprintf(
                '/usr/bin/php ' .
                    escapeshellarg('includes/narro/importer/importer.php').
                    ' --export --minloglevel 3 --project %d --user %d ' .
                    (($this->chkValidate->Checked)?'--validate ':'') .
                    (($this->chkForce->Checked)?'--force ':'') .
                    (($this->chkOnlySuggestions->Checked)?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                    ' --check-equal --source-lang en-US --target-lang %s',
                $this->objNarroProject->ProjectId,
                QApplication::$objUser->UserId,
                QApplication::$objUser->Language->LanguageCode
            );

            proc_close(proc_open ("$strCommand &", array(), $foo));
        }

        private function showLog() {
            $this->pnlLogViewer->Text = '<div class="dotted_box">
            <div class="dotted_box_title">' . t('Operation log') . '</div>
            <div class="dotted_box_content">' . nl2br(NarroLog::GetLogContents()) . '</div></div></div>';

            $this->pnlLogViewer->Visible = true;
        }

    }


    NarroProjectManageForm::Run('NarroProjectManageForm', 'templates/narro_project_manage.tpl.php');
?>
