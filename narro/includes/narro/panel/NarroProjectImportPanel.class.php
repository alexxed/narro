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

    class NarroProjectImportPanel extends QPanel {
        protected $objNarroProject;
        public $objImportProgress;

        public $pnlLogViewer;
        public $lblImport;
        public $btnKillProcess;

        public $pnlTextsSource;
        public $pnlTranslationsSource;

        public $chkApproveImportedTranslations;
        public $chkApproveOnlyNotApproved;
        public $chkImportOnlyTranslations;
        public $chkImportUnchangedFiles;

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

            $this->objNarroProject = $objNarroProject;

            $this->pnlLogViewer = new NarroLogViewerPanel($this);
            $this->pnlLogViewer->Visible = false;

            $this->lblImport = new QLabel($this);
            $this->lblImport->Visible = false;

            $this->pnlTextsSource = new NarroProjectTextSourcePanel($this->objNarroProject, $this);

            $this->pnlTranslationsSource = new NarroProjectTranslationSourcePanel($this->objNarroProject, $this);

            $this->chkApproveImportedTranslations = new QCheckBox($this);
            $this->chkApproveImportedTranslations->Name = t('Approve the imported translations');
            $this->chkApproveImportedTranslations->Checked = true;
            if (QApplication::$UseAjax)
                $this->chkApproveImportedTranslations->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'chkApproveImportedTranslations_Click'));
            else
                $this->chkApproveImportedTranslations->AddAction(new QClickEvent(), new QServerControlAction($this, 'chkApproveImportedTranslations_Click'));

            $this->chkApproveOnlyNotApproved = new QCheckBox($this);
            $this->chkApproveOnlyNotApproved->Name = t('Approve only translations that are not approved yet in Narro');
            $this->chkApproveOnlyNotApproved->Checked = true;

            $this->chkImportUnchangedFiles = new QCheckBox($this);
            $this->chkImportUnchangedFiles->Name = t('Import the files that are marked as not changed');
            $this->chkImportUnchangedFiles->Checked = false;

            $this->chkImportOnlyTranslations = new QCheckBox($this);
            $this->chkImportOnlyTranslations->Name = t('Do not add texts, just add found translations for existing texts');
            $this->chkImportOnlyTranslations->Checked = false;

            $this->objImportProgress = new NarroTranslationProgressBar($this);
            $this->objImportProgress->Total = 100;
            $this->objImportProgress->Visible = false;

            if (NarroUtils::IsProcessRunning('import', $this->objNarroProject->ProjectId)) {
                $this->btnImport->Visible = false;
                $this->objImportProgress->Visible = true;
                $this->objImportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'import');
                QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->Form->FormId, $this->btnImport->ControlId, 2000));
            }

            $this->btnKillProcess = new QButton($this);
            $this->btnKillProcess->Text = 'Kill process';
            $this->btnKillProcess->Visible = false;
            if (QApplication::$UseAjax)
                $this->btnKillProcess->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnKillProcess_Click'));
            else
                $this->btnKillProcess->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnKillProcess_Click'));

            $this->btnImport = new QButton($this);
            $this->btnImport->Text = t('Import');
            if (QApplication::$UseAjax)
                $this->btnImport->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnImport_Click'));
            else
                $this->btnImport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnImport_Click'));
        }

        public function chkApproveImportedTranslations_Click($strFormId, $strControlId, $strParameter) {
            $this->chkApproveOnlyNotApproved->Visible = $this->chkApproveImportedTranslations->Checked;
            $this->MarkAsModified();
        }

        public function btnImport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can import project', $this->objNarroProject->ProjectId))
                return false;

            $strImportLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-import.log';
            $strProcLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-import-process.log';

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');
            $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strImportLogFile));

            if ($strParameter == 1) {
                if (NarroUtils::IsProcessRunning('import', $this->objNarroProject->ProjectId)) {
                    $this->objImportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'import');
                    $this->objImportProgress->MarkAsModified();
                }
                else {

                    $this->lblImport->Text = t('Import finished.');

                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript('if (typeof lastImportId != \'undefined\') clearInterval(lastImportId)');

                    if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                        $objLogger->info(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                    $this->lblImport->Visible = true;
                    $this->btnImport->Visible = true;
                    $this->objImportProgress->Translated = 0;
                    $this->objImportProgress->Visible = false;

                    $this->pnlLogViewer->LogFile = $strImportLogFile;
                    $this->pnlLogViewer->MarkAsModified();
                }
            }
            elseif ($strParameter == 2) {
                set_time_limit(0);

                $objNarroImporter = new NarroProjectImporter();

                $objNarroImporter->Logger = $objLogger;

                /**
                 * Get boolean options
                 */
                $objNarroImporter->DeactivateFiles = !$this->chkImportOnlyTranslations->Checked;
                $objNarroImporter->DeactivateContexts = !$this->chkImportOnlyTranslations->Checked;
                $objNarroImporter->CheckEqual = true;
                $objNarroImporter->Approve = $this->chkApproveImportedTranslations->Checked;
                $objNarroImporter->OnlySuggestions = $this->chkImportOnlyTranslations->Checked;
                $objNarroImporter->Project = $this->objNarroProject;
                $objNarroImporter->User = NarroUser::LoadAnonymousUser();
                $objNarroImporter->TargetLanguage = QApplication::$Language;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
                try {
                    $objNarroImporter->TranslationPath = $this->pnlTranslationsSource->Directory;
                    $objNarroImporter->TemplatePath = $this->pnlTextsSource->Directory;
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = t('Import failed.');
                }

                try {
                    $objNarroImporter->ImportProject();
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = t('Import failed.');
                }

                $this->lblImport->Visible = true;
                $this->btnImport->Visible = true;
                $this->objImportProgress->Visible = false;

                $this->pnlLogViewer->MarkAsModified();

            }
            else {
                unlink($strImportLogFile);
                $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strImportLogFile));
                $this->btnImport->Visible = false;
                $this->objImportProgress->Visible = true;
                $this->objImportProgress->Translated = 0;
                $this->lblImport->Text = '';
                try {
                    $strCommand = sprintf(
                        __PHP_CLI_PATH__ . ' ' .
                            escapeshellarg('includes/narro/importer/narro-cli.php').
                            ' --import --minloglevel 3 --project %d --user %d ' .
                            (($this->chkApproveImportedTranslations->Checked)?'--approve ':'') .
                            (($this->chkImportUnchangedFiles->Checked)?'--import-unchanged-files ':'') .
                            (($this->chkImportOnlyTranslations->Checked || !QApplication::HasPermission('Can import project', $this->objNarroProject->ProjectId))?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                            ' --template-lang %s --translation-lang %s --template-directory "%s" --translation-directory "%s"',
                        $this->objNarroProject->ProjectId,
                        0,
                        NarroLanguage::SOURCE_LANGUAGE_CODE,
                        QApplication::$Language->LanguageCode,
                        $this->pnlTextsSource->Directory,
                        $this->pnlTranslationsSource->Directory
                    );
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during import: %s', $objEx->getMessage()));
                    $this->lblImport->Text = t('Import failed.');

                    $this->lblImport->Visible = true;
                    $this->btnImport->Visible = true;
                    $this->objImportProgress->Translated = 0;
                    $this->objImportProgress->Visible = false;

                    $this->pnlLogViewer->MarkAsModified();
                    return false;
                }


                if (file_exists($strProcLogFile) && is_writable($strProcLogFile))
                    unlink($strProcLogFile);

                $mixProcess = proc_open("$strCommand &", array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript(sprintf('lastImportId = setInterval("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'1\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnImport_Click($strFormId, $strControlId, 1);
                }
                else {
                    $this->objImportProgress->Visible = false;
                    $objLogger->err('Failed to launch a background process, there will be no progress displayed, and it might take a while, please wait for more messages');
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

    }
