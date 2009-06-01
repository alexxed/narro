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

    class NarroProjectExportPanel extends QPanel {
        protected $objNarroProject;
        public $objExportProgress;

        public $pnlLogViewer;
        public $lblExport;

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

            $this->strTemplate = __DOCROOT__ . __SUBDIRECTORY__ . '/templates/NarroProjectExportPanel.tpl.php';

            $this->objNarroProject = $objNarroProject;

            $this->pnlLogViewer = new NarroLogViewerPanel($this);
            $this->pnlLogViewer->Visible = false;

            $this->lblExport = new QLabel($this);
            $this->lblExport->HtmlEntities = false;
            $strArchiveName = $this->objNarroProject->ProjectName . '-' . NarroApp::$Language->LanguageCode . '.zip';
            $strExportFile = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $strArchiveName;
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

            if (NarroUtils::IsProcessRunning('export', $this->objNarroProject->ProjectId)) {
                $this->btnExport->Visible = false;
                $this->objExportProgress->Visible = true;
                $this->objExportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'export');
                NarroApp::ExecuteJavaScript(sprintf('lastExportId = setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $this->Form->FormId, $this->btnExport->ControlId, 2000));
            }

            $this->btnExport = new QButton($this);
            $this->btnExport->Text = t('Export');
            if (NarroApp::$UseAjax)
                $this->btnExport->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnExport_Click'));
            else
                $this->btnExport->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnExport_Click'));
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
            if (!NarroApp::HasPermissionForThisLang('Can export project', $this->objNarroProject->ProjectId))
                return false;

            $strExportLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . NarroApp::$Language->LanguageCode . '-export.log';
            $strProcLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . NarroApp::$Language->LanguageCode . '-export-process.log';

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');

            $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strExportLogFile));

            $this->pnlLogViewer->LogFile = $strExportLogFile;

            if ($strParameter == 1) {
                if (NarroUtils::IsProcessRunning('export', $this->objNarroProject->ProjectId)) {
                    $this->objExportProgress->Translated = NarroProgress::GetProgress($this->objNarroProject->ProjectId, 'export');
                    $this->objExportProgress->MarkAsModified();
                }
                else {

                    $this->lblExport->Text = t('Export finished.');

                    if (NarroApp::$UseAjax)
                        NarroApp::ExecuteJavaScript('if (typeof lastExportId != \'undefined\') clearInterval(lastExportId)');

                    if (file_exists($strProcLogFile) && filesize($strProcLogFile))
                        $objLogger->info(sprintf('There are messages from the background process: %s', file_get_contents($strProcLogFile)));

                    $this->lblExport->Visible = true;
                    $this->btnExport->Visible = true;
                    $this->objExportProgress->Translated = 0;
                    $this->objExportProgress->Visible = false;

                    $this->CreateExportArchive(
                        __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroApp::$Language->LanguageCode,
                        __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . NarroApp::$Language->LanguageCode . '.zip'
                    );
                    if (file_exists(__DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . NarroApp::$Language->LanguageCode . '.zip')) {
                        $strDownloadUrl = __HTTP_URL__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . $this->objNarroProject->ProjectName . '-' . NarroApp::$Language->LanguageCode . '.zip';
                        $this->lblExport->Text .= ' ' . sprintf(t('Download link: <a href="%s">%s</a>'), $strDownloadUrl, $this->objNarroProject->ProjectName . '-' . NarroApp::$Language->LanguageCode . '.zip');
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

                $objNarroImporter->Logger = $objLogger;

                /**
                 * Get boolean options
                 */
                $objNarroImporter->CopyUnhandledFiles = $this->chkCopyUnhandledFiles->Checked;
                $objNarroImporter->ExportedSuggestion = $this->lstExportSuggestionType->SelectedValue;
                $objNarroImporter->Project = $this->objNarroProject;
                $objNarroImporter->User = NarroUser::LoadAnonymousUser();
                $objNarroImporter->TargetLanguage = NarroApp::$Language;
                $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en-US');
                try {
                    $objNarroImporter->TranslationPath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroApp::$Language->LanguageCode;
                    $objNarroImporter->TemplatePath = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/en-US';
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');
                }

                try {
                    $objNarroImporter->ExportProject();
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');
                }

                $this->lblExport->Visible = true;
                $this->btnExport->Visible = true;
                $this->objExportProgress->Visible = false;

                $this->pnlLogViewer->MarkAsModified();

            }
            else {
                unlink($strExportLogFile);
                $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strExportLogFile));
                $this->btnExport->Visible = false;
                $this->objExportProgress->Visible = true;
                $this->objExportProgress->Translated = 0;
                $this->lblExport->Text = '';
                try {
                    $strCommand = sprintf(
                        '%s %s --export --project %d --user %d --template-lang en-US --translation-lang %s --template-directory "%s" --translation-directory "%s" --exported-suggestion %d %s',
                        __PHP_CLI_PATH__,
                        escapeshellarg('includes/narro/importer/narro-cli.php'),
                        $this->objNarroProject->ProjectId,
                        0,
                        NarroApp::$Language->LanguageCode,
                        __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/en-US',
                        __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroApp::$Language->LanguageCode,
                        $this->lstExportSuggestionType->SelectedValue,
                        (($this->chkCopyUnhandledFiles->Checked)?'--copy-unhandled-files ':'')
                    );
                }
                catch (Exception $objEx) {
                    $objLogger->err(sprintf('An error occured during export: %s', $objEx->getMessage()));
                    $this->lblExport->Text = t('Export failed.');

                    $this->lblExport->Visible = true;
                    $this->btnExport->Visible = true;
                    $this->objExportProgress->Translated = 0;
                    $this->objExportProgress->Visible = false;

                    $this->pnlLogViewer->MarkAsModified();
                    return false;
                }


                if (file_exists($strProcLogFile) && is_writable($strProcLogFile))
                    unlink($strProcLogFile);

                $mixProcess = proc_open("$strCommand &", array(2 => array("file", $strProcLogFile, 'a')), $foo);

                if ($mixProcess) {
                    if (NarroApp::$UseAjax)
                        NarroApp::ExecuteJavaScript(sprintf('lastExportId = setInterval("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'1\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnExport_Click($strFormId, $strControlId, 1);
                }
                else {
                    $this->objExportProgress->Visible = false;
                    $objLogger->err('Failed to launch a background process, there will be no progress displayed, and it might take a while, please wait for more messages');
                    $this->pnlLogViewer->MarkAsModified();
                    /**
                     * try exporting without launching a background process
                     */
                    if (NarroApp::$UseAjax)
                        NarroApp::ExecuteJavaScript(sprintf('lastExportId = setTimeout("qc.pA(\'%s\', \'%s\', \'QClickEvent\', \'2\')", %d);', $strFormId, $strControlId, 2000));
                    else
                        $this->btnExport_Click($strFormId, $strControlId, 2);
                }
            }
        }

        private function CreateExportArchive($strTranslationPath, $strArchive) {
            $strExportLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . NarroApp::$Language->LanguageCode . '-export.log';

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');

            $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strExportLogFile));

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
                $objLogger->err(sprintf('Failed to create a new archive %s', $strArchive));
                return false;
            }
            $objZipFile->close();
            if (file_exists($strArchive))
                chmod($strArchive, 0666);
            else {
                $objLogger->err(sprintf('Failed to create an archive %s', $strArchive));
                return false;
            }
            return true;
        }

    }
