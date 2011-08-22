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

    class NarroProjectImportPanel extends QPanel {
        protected $objProject;
        public $objImportProgress;

        public $pnlLogViewer;
        public $lblImport;
        public $btnKillProcess;

        public $pnlTextsSource;
        public $pnlTranslationsSource;

        public $chkApproveImportedTranslations;
        public $chkApproveOnlyNotApproved;
        public $chkImportOnlyTranslations;

        public $btnImport;

        public function __construct($objNarroProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectImportPanel.tpl.php';

            $this->objProject = $objNarroProject;

            $this->pnlLogViewer = new NarroLogViewerPanel($this);
            $this->pnlLogViewer->DisplayStyle = QDisplayStyle::None;

            $this->lblImport = new QLabel($this);
            $this->lblImport->Visible = false;

            $this->pnlTextsSource = new NarroProjectTextSourcePanel($this->objProject, NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE), $this);
            $this->pnlTextsSource->Display = QApplication::HasPermission('Can import project', $this->objProject->ProjectId);

            $this->pnlTranslationsSource = new NarroProjectTranslationSourcePanel($this->objProject, QApplication::$TargetLanguage, $this);

            $this->chkApproveImportedTranslations = new QCheckBox($this);
            $this->chkApproveImportedTranslations->Name = t('Approve the imported translations');
            if (QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId)) {
                $this->chkApproveImportedTranslations->Display = true;
                $this->chkApproveImportedTranslations->Checked = true;
            }
            else {
                $this->chkApproveImportedTranslations->Display = false;
                $this->chkApproveImportedTranslations->Checked = false;
            }

            if (QApplication::$UseAjax)
                $this->chkApproveImportedTranslations->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'chkApproveImportedTranslations_Click'));
            else
                $this->chkApproveImportedTranslations->AddAction(new QClickEvent(), new QServerControlAction($this, 'chkApproveImportedTranslations_Click'));

            $this->chkApproveOnlyNotApproved = new QCheckBox($this);
            $this->chkApproveOnlyNotApproved->Name = t('Approve only translations that are not approved yet in Narro');
            if (QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId)) {
                $this->chkApproveOnlyNotApproved->Display = true;
            }
            else {
                $this->chkApproveOnlyNotApproved->Display = false;
            }
            $this->chkApproveOnlyNotApproved->Checked = true;

            $this->chkImportOnlyTranslations = new QCheckBox($this);
            $this->chkImportOnlyTranslations->Name = t('Do not add texts, just add found translations for existing texts');
            if (QApplication::HasPermission('Can import project', $this->objProject->ProjectId)) {
                $this->chkImportOnlyTranslations->Display = true;
                $this->chkImportOnlyTranslations->Checked = false;
            }
            else {
                $this->chkImportOnlyTranslations->Checked = true;
                $this->chkImportOnlyTranslations->Display = false;
            }

            $this->objImportProgress = new NarroTranslationProgressBar($this);
            $this->objImportProgress->Total = 100;
            $this->objImportProgress->Visible = false;

            $this->btnKillProcess = new QButton($this);
            $this->btnKillProcess->Text = 'Kill process';
            if (QApplication::$UseAjax)
                $this->btnKillProcess->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnKillProcess_Click'));
            else
                $this->btnKillProcess->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnKillProcess_Click'));

            $this->btnImport = new QButton($this);
            $this->btnImport->Text = t('Import');
            $this->btnImport->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('if (document.getElementById(\'%s\')) document.getElementById(\'%s\').innerHTML=\'\'', $this->lblImport->ControlId, $this->lblImport->ControlId)));
            $this->btnImport->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\';this.value=\'%s\'', t('Please wait...'))));
            if (QApplication::$UseAjax && QApplication::$User->getPreferenceValueByName('Launch imports and exports in background') == 'Yes')
                $this->btnImport->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnImport_Click'));
            else {
                $this->btnImport->ActionParameter = 2;
                $this->btnImport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnImport_Click'));
            }

            if (NarroUtils::IsProcessRunning('import', $this->objProject->ProjectId)) {
                $this->btnImport->Visible = false;
                $this->objImportProgress->Visible = true;
                $this->objImportProgress->Translated = NarroProgress::GetProgress($this->objProject->ProjectId, 'import');
                QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->Form->FormId, $this->btnImport->ControlId, 2000));
            }

            $this->btnKillProcess->Visible = QApplication::HasPermission('Administrator', $this->objProject->ProjectId, QApplication::$TargetLanguage->LanguageCode) && !$this->btnImport->Visible;
        }

        public function chkApproveImportedTranslations_Click($strFormId, $strControlId, $strParameter) {
            $this->chkApproveOnlyNotApproved->Display = $this->chkApproveImportedTranslations->Checked;
            $this->MarkAsModified();
        }

        public function btnImport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can import project', $this->objProject->ProjectId))
                return false;

            $strProcLogFile = __TMP_PATH__ . '/' . $this->objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.log';
            $strProcPidFile = __TMP_PATH__ . '/' . $this->objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.pid';
            $strProgressFile = __TMP_PATH__ . '/import-' . $this->objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode;

            $this->pnlLogViewer->LogFile = QApplication::$LogFile;

            if ($strParameter == 1) {
                if (NarroUtils::IsProcessRunning('import', $this->objProject->ProjectId)) {
                    $this->objImportProgress->Translated = NarroProgress::GetProgress($this->objProject->ProjectId, 'import');
                    $this->objImportProgress->MarkAsModified();
                }
                else {

                    $this->lblImport->Text = t('Import finished.');

                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript('if (typeof lastImportId != \'undefined\') clearInterval(lastImportId)');

                    if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                        QApplication::LogInfo(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                    if (file_exists($strProcLogFile))
                        unlink($strProcLogFile);

                    if (file_exists($strProcPidFile))
                        unlink($strProcPidFile);

                    if (file_exists($strProgressFile))
                        unlink($strProgressFile);

                    $this->lblImport->Visible = true;
                    $this->btnImport->Visible = true;
                    $this->btnKillProcess->Visible = false;
                    $this->objImportProgress->Translated = 0;
                    $this->objImportProgress->Visible = false;

                    $this->pnlLogViewer->MarkAsModified();
                }
            }
            elseif ($strParameter == 2) {
                QApplication::ClearLog();
                NarroProgress::ClearProgressFileName($this->objProject->ProjectId, 'import');
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
                $objNarroImporter->Approve = $this->chkApproveImportedTranslations->Checked;
                $objNarroImporter->ApproveAlreadyApproved = !$this->chkApproveOnlyNotApproved->Checked;
                $objNarroImporter->OnlySuggestions = $this->chkImportOnlyTranslations->Checked;
                $objNarroImporter->Project = $this->objProject;
                $objNarroImporter->User = NarroUser::LoadAnonymousUser();
                $objNarroImporter->TargetLanguage = QApplication::$TargetLanguage;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
                try {
                    $objNarroImporter->TranslationPath = $this->pnlTranslationsSource->Directory;
                    $objNarroImporter->TemplatePath = $this->pnlTextsSource->Directory;
                }
                catch (Exception $objEx) {
                    QApplication::LogError(sprintf('An error occurred during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = sprintf(t('Import failed: %s'), $objEx->getMessage());
                    return false;
                }

                try {
                    $objNarroImporter->ImportProject();
                }
                catch (Exception $objEx) {
                    QApplication::LogError(sprintf('An error occurred during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = sprintf(t('Import failed: %s'), $objEx->getMessage());
                }

                $this->lblImport->Visible = true;
                $this->btnImport->Visible = true;
                $this->btnKillProcess->Visible = false;
                $this->objImportProgress->Visible = false;

                $this->pnlLogViewer->MarkAsModified();

            }
            else {
                QApplication::ClearLog();
                NarroProgress::ClearProgressFileName($this->objProject->ProjectId, 'import');
                $this->pnlLogViewer->MarkAsModified();
                $this->btnImport->Visible = false;
                $this->btnKillProcess->Visible = QApplication::HasPermission('Administrator',$this->objProject,QApplication::$TargetLanguage->LanguageCode) && !$this->btnImport->Visible;
                $this->objImportProgress->Visible = true;
                $this->objImportProgress->Translated = 0;
                $this->lblImport->Text = '';
                try {
                    $strCommand = sprintf(
                        __PHP_CLI_PATH__ . ' ' .
                            escapeshellarg('includes/narro/importer/narro-cli.php').
                            ' --import --minloglevel 3 --project %d --user %d --check-equal ' .
                            (($this->chkApproveImportedTranslations->Checked)?'--approve ':'') .
                            (($this->chkApproveOnlyNotApproved->Checked)?'':'--approve-already-approved ') .
                            (($this->chkImportOnlyTranslations->Checked || !QApplication::HasPermission('Can import project', $this->objProject->ProjectId))?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                            ' --template-lang %s --translation-lang %s --template-directory "%s" --translation-directory "%s"',
                        $this->objProject->ProjectId,
                        0,
                        NarroLanguage::SOURCE_LANGUAGE_CODE,
                        QApplication::$TargetLanguage->LanguageCode,
                        $this->pnlTextsSource->Directory,
                        $this->pnlTranslationsSource->Directory
                    );
                }
                catch (Exception $objEx) {
                    QApplication::LogError(sprintf('An error occurred during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = sprintf(t('Import failed: %s'), $objEx->getMessage());

                    $this->lblImport->Visible = true;
                    $this->btnImport->Visible = true;
                    $this->btnKillProcess->Visible = QApplication::HasPermission('Administrator',$this->objProject,QApplication::$TargetLanguage->LanguageCode) && !$this->btnImport->Visible;
                    $this->objImportProgress->Translated = 0;
                    $this->objImportProgress->Visible = false;

                    $this->pnlLogViewer->MarkAsModified();
                    return false;
                }


                if (file_exists($strProcLogFile) && is_writable($strProcLogFile)) {
                    unlink($strProcLogFile);
                }

                $mixProcess = proc_open("$strCommand &", array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'1\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnImport_Click($strFormId, $strControlId, 1);
                }
                else {
                    $this->objImportProgress->Visible = false;
                    QApplication::LogError('Failed to launch a background process, there will be no progress displayed, and it might take a while, please wait for more messages');
                    $this->pnlLogViewer->MarkAsModified();
                    /**
                     * try importing without launching a background process
                     */
                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript(sprintf('lastImportId = setTimeout("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'2\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnImport_Click($strFormId, $strControlId, 2);
                }
            }
        }

        public function btnKillProcess_Click($strFormId, $strControlId, $strParameter) {
            $strProcLogFile = __TMP_PATH__ . '/' . $this->objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.log';
            $strProcPidFile = __TMP_PATH__ . '/' . $this->objProject->ProjectId . '-' . QApplication::$TargetLanguage->LanguageCode . '-import-process.pid';

            if (!file_exists($strProcPidFile)) {
                QApplication::LogError('Could not find a pid file for the background process.');
                $this->pnlLogViewer->MarkAsModified();
                return false;
            }

            $intPid = file_get_contents($strProcPidFile);

            if (is_numeric(trim($intPid))) {

                $mixProcess = proc_open(sprintf('kill -9 %d', $intPid), array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    proc_close($mixProcess);
                    QApplication::LogError('Process killed');
                }
                else {
                    QApplication::LogError('Failed to kill process');
                }

                if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                    QApplication::LogWarn(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                $this->pnlLogViewer->MarkAsModified();
            }

        }

    }
