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

    class NarroProjectExportPanel extends QPanel {
        protected $objNarroProject;
        public $objExportProgress;

        public $pnlLogViewer;
        public $lblExport;
        public $btnKillProcess;

        public $chkCopyUnhandledFiles;
        public $lstExportSuggestionType;

        public $btnExport;

        public function __construct($objNarroProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectExportPanel.tpl.php';

            $this->objNarroProject = $objNarroProject;

            $this->pnlLogViewer = new NarroLogViewerPanel($this);
            $this->pnlLogViewer->Visible = false;

            $this->lblExport = new QLabel($this);
            $this->lblExport->HtmlEntities = false;
            $strArchiveName = $this->objNarroProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip';
            $strExportFile = __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $strArchiveName;
            if (file_exists($strExportFile)) {
                $objDateSpan = new QDateTimeSpan(time() - filemtime($strExportFile));
                $this->lblExport->Text = sprintf(t('Link to last export: <a href="%s">%s</a>, exported %s ago'), str_replace(__DOCROOT__, __HTTP_URL__, $strExportFile) , $strArchiveName, $objDateSpan->SimpleDisplay());
            }


            $this->chkCopyUnhandledFiles = new QCheckBox($this);
            $this->chkCopyUnhandledFiles->Name = t('Copy unhandled files');

            $this->lstExportSuggestionType = new QListBox($this);
            $this->lstExportSuggestionType->Name = t('Export translations using') . ':';
            $this->lstExportSuggestionType->AddItem(t('Approved suggestion'), 1);
            $this->lstExportSuggestionType->AddItem(t('Approved, then most voted suggestion'), 2);
            $this->lstExportSuggestionType->AddItem(t('Approved, then most recent suggestion'), 3);
            $this->lstExportSuggestionType->AddItem(t('Approved, then most voted and then most recent suggestion'), 4);
            $this->lstExportSuggestionType->AddItem(t('Approved, then my suggestion'), 5);

            $this->objExportProgress = new NarroTranslationProgressBar($this);
            $this->objExportProgress->Total = 100;
            $this->objExportProgress->Visible = false;

            $this->btnKillProcess = new QButton($this);
            $this->btnKillProcess->Text = 'Kill process';
            if (QApplication::$UseAjax)
                $this->btnKillProcess->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnKillProcess_Click'));
            else
                $this->btnKillProcess->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnKillProcess_Click'));


            if (NarroUtils::IsProcessRunning('export', $this->objNarroProject->ProjectId)) {
                $this->btnExport->Visible = false;
                $this->objExportProgress->Visible = true;
                $this->objExportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'export');
                QApplication::ExecuteJavaScript(sprintf('lastExportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->Form->FormId, $this->btnExport->ControlId, 2000));
            }

            $this->btnExport = new QButton($this);
            $this->btnExport->Text = t('Export');
            if (QApplication::$UseAjax)
                $this->btnExport->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnExport_Click'));
            else
                $this->btnExport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnExport_Click'));

            $this->btnKillProcess->Visible = QApplication::HasPermission('Administrator',$this->objNarroProject,QApplication::$LanguageCode) && !$this->btnExport->Visible;
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can export project', $this->objNarroProject->ProjectId))
                return false;

            $strProcLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-export-process.log';

            $this->pnlLogViewer->LogFile = QApplication::$LogFile;

            if ($strParameter == 1) {
                if (NarroUtils::IsProcessRunning('export', $this->objNarroProject->ProjectId)) {
                    $this->objExportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'export');
                    $this->objExportProgress->MarkAsModified();
                }
                else {

                    $this->lblExport->Text = t('Export finished.');

                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript('if (typeof lastExportId != \'undefined\') clearInterval(lastExportId)');

                    if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                        QApplication::$Logger->info(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                    $this->lblExport->Visible = true;
                    $this->btnExport->Visible = true;
                    $this->btnKillProcess->Visible = false;
                    $this->objExportProgress->Translated = 0;
                    $this->objExportProgress->Visible = false;

                    $this->CreateExportArchive(
                        $this->objNarroProject->DefaultTranslationPath,
                        __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip'
                    );
                    if (file_exists(__IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip')) {
                        // @todo replace this with a download method that can serve files from a non web public directory
                        $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . str_replace(__DOCROOT__ . __SUBDIRECTORY__, '', __IMPORT_PATH__) . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip';
                        $this->lblExport->Text .= ' ' . sprintf(t('Download link: <a href="%s">%s</a>'), $strDownloadUrl, $this->objNarroProject->ProjectName . '-' . QApplication::$Language->LanguageCode . '.zip');
                    }
                    else {
                        $this->lblExport->Text .= ' ' . t('Failed to create an archive for download');
                    }



                    $this->pnlLogViewer->MarkAsModified();
                }
            }
            elseif ($strParameter == 2) {
                set_time_limit(0);

                $objNarroImporter = new NarroProjectImporter();

                /**
                 * Get boolean options
                 */
                $objNarroImporter->CopyUnhandledFiles = $this->chkCopyUnhandledFiles->Checked;
                $objNarroImporter->ExportedSuggestion = $this->lstExportSuggestionType->SelectedValue;
                $objNarroImporter->Project = $this->objNarroProject;
                $objNarroImporter->User = NarroUser::LoadAnonymousUser();
                $objNarroImporter->TargetLanguage = QApplication::$Language;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
                try {
                    $objNarroImporter->TranslationPath = $this->objNarroProject->DefaultTranslationPath;
                    $objNarroImporter->TemplatePath = $this->objNarroProject->DefaultTemplatePath;
                }
                catch (Exception $objEx) {
                    QApplication::$Logger->err(sprintf('An error occurred during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');
                }

                try {
                    $objNarroImporter->ExportProject();
                }
                catch (Exception $objEx) {
                    QApplication::$Logger->err(sprintf('An error occurred during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');
                }

                $this->lblExport->Visible = true;
                $this->btnExport->Visible = true;
                $this->btnKillProcess->Visible = false;
                $this->objExportProgress->Visible = false;

                $this->pnlLogViewer->MarkAsModified();

            }
            else {
                QApplication::ClearLog();
                $this->btnExport->Visible = false;
                $this->btnKillProcess->Visible = $this->btnKillProcess->Visible = QApplication::HasPermission('Administrator',$this->objNarroProject,QApplication::$LanguageCode);
                $this->objExportProgress->Visible = true;
                $this->objExportProgress->Translated = 0;
                $this->lblExport->Text = '';
                try {
                    $strCommand = sprintf(
                        '%s %s --export --project %d --user %d --template-lang %s --translation-lang %s --template-directory "%s" --translation-directory "%s" --exported-suggestion %d %s',
                        __PHP_CLI_PATH__,
                        escapeshellarg('includes/narro/importer/narro-cli.php'),
                        $this->objNarroProject->ProjectId,
                        0,
                        NarroLanguage::SOURCE_LANGUAGE_CODE,
                        QApplication::$Language->LanguageCode,
                        $this->objNarroProject->DefaultTemplatePath,
                        $this->objNarroProject->DefaultTranslationPath,
                        $this->lstExportSuggestionType->SelectedValue,
                        (($this->chkCopyUnhandledFiles->Checked)?'--copy-unhandled-files ':'')
                    );
                }
                catch (Exception $objEx) {
                    QApplication::$Logger->err(sprintf('An error occurred during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');

                    $this->lblExport->Visible = true;
                    $this->btnExport->Visible = true;
                    $this->btnKillProcess->Visible = false;
                    $this->objExportProgress->Translated = 0;
                    $this->objExportProgress->Visible = false;

                    $this->pnlLogViewer->MarkAsModified();
                    return false;
                }


                if (file_exists($strProcLogFile) && is_writable($strProcLogFile))
                    unlink($strProcLogFile);

                $mixProcess = proc_open("$strCommand &", array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript(sprintf('lastExportId = setInterval("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'1\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnExport_Click($strFormId, $strControlId, 1);
                }
                else {
                    $this->objExportProgress->Visible = false;
                    QApplication::$Logger->err('Failed to launch a background process, there will be no progress displayed, and it might take a while, please wait for more messages');
                    $this->pnlLogViewer->MarkAsModified();
                    /**
                     * try exporting without launching a background process
                     */
                    if (QApplication::$UseAjax)
                        QApplication::ExecuteJavaScript(sprintf('lastExportId = setTimeout("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'2\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnExport_Click($strFormId, $strControlId, 2);
                }
            }
        }

        private function CreateExportArchive($strTranslationPath, $strArchive) {
            if (file_exists($strArchive))
                unlink($strArchive);

            $arrFiles = NarroUtils::ListDirectory($strTranslationPath, null, null, null, true);

            $objZipFile = new ZipArchive;
            if ($objZipFile->open($strArchive, ZipArchive::OVERWRITE) === TRUE) {
                foreach($arrFiles as $strFileName) {
                    if (is_dir($strFileName)) {
                        $objZipFile->addEmptyDir(str_replace($strTranslationPath, '', $strFileName ));
                    }
                    elseif (is_file($strFileName)) {
                        $objZipFile->addFile($strFileName, str_replace($strTranslationPath . '/', '', $strFileName ));
                    }
                }
            } else {
                QApplication::$Logger->err(sprintf('Failed to create a new archive %s', $strArchive));
                return false;
            }
            $objZipFile->close();
            if (file_exists($strArchive))
                chmod($strArchive, 0666);
            else {
                QApplication::$Logger->err(sprintf('Failed to create an archive %s', $strArchive));
                return false;
            }
            return true;
        }

        public function btnKillProcess_Click($strFormId, $strControlId, $strParameter) {
            $strProcLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-export-process.log';
            $strProcPidFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-export-process.pid';

            if (!file_exists($strProcPidFile)) {
                QApplication::$Logger->err('Could not find a pid file for the background process.');
                $this->pnlLogViewer->MarkAsModified();
                return false;
            }

            $intPid = file_get_contents($strProcPidFile);

            if (is_numeric(trim($intPid))) {

                $mixProcess = proc_open(sprintf('kill -9 %d', $intPid), array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    proc_close($mixProcess);
                    QApplication::$Logger->info('Process killed');
                }
                else {
                    QApplication::$Logger->info('Failed to kill process');
                }

                if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                    QApplication::$Logger->info(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                $this->pnlLogViewer->MarkAsModified();
            }

        }

    }
