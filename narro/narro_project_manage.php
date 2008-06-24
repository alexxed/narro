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

        protected $txtProjectName;
        protected $lstProjectType;
        protected $lstProjectActive;
        protected $btnSaveProject;

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
        protected $txtImportFromDirectory;
        protected $filImportFromFile;
        protected $objImportProgress;
        protected $lblImport;

        protected $btnExport;
        protected $txtExportToDirectory;
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

            $this->txtProjectName = new QTextBox($this);
            $this->txtProjectName->Text = $this->objNarroProject->ProjectName;

            $this->lstProjectType = new QListBox($this);
            foreach(NarroProjectType::$TokenArray as $intTypeId=>$strType) {
                $this->lstProjectType->AddItem($strType, $intTypeId, $intTypeId == $this->objNarroProject->ProjectType);
            }

            $this->lstProjectActive = new QListBox($this);
            $this->lstProjectActive->AddItem(t('Yes'), 1, $this->objNarroProject->Active == 1);
            $this->lstProjectActive->AddItem(t('No'), 0, $this->objNarroProject->Active == 0);

            $this->btnSaveProject = new QButton($this);
            $this->btnSaveProject->Text = t('Save');

            $this->lstExportedSuggestion = new QListBox($this);
            $this->lstExportedSuggestion->AddItem(t('The validated suggestion'), 1);
            $this->lstExportedSuggestion->AddItem(t('The most voted suggestion'), 2);
            $this->lstExportedSuggestion->AddItem(t('My suggestion'), 3);

            $this->chkForce = new QCheckBox($this);

            $this->chkDoNotDeactivateFiles = new QCheckBox($this);
            $this->chkDoNotDeactivateFiles->Checked = true;

            $this->chkDoNotDeactivateContexts = new QCheckBox($this);
            $this->chkDoNotDeactivateContexts->Checked = true;

            $this->chkValidate = new QCheckBox($this);
            $this->chkValidate->Checked = true;

            $this->chkOnlySuggestions = new QCheckBox($this);


            $this->txtImportFromDirectory = new QTextBox($this);
            $this->txtImportFromDirectory->Text = dirname(__FILE__) . '/data/import/' . $this->objNarroProject->ProjectId;

            $this->filImportFromFile = new QFileControl($this);

            $this->btnImport = new QButton($this);
            $this->btnImport->Text = t('Import');
            $this->btnImport->AddAction(new QClickEvent(), new QAjaxAction('btnImport_Click'));

            $this->objImportProgress = new NarroTranslationProgressBar($this);
            $this->objImportProgress->Total = 100;
            $this->objImportProgress->Visible = false;

            $this->lblImport = new QLabel($this);
            $this->lblImport->Visible = false;

            $this->txtExportToDirectory = new QTextBox($this);
            $this->txtExportToDirectory->Text = dirname(__FILE__) . '/data/import/' . $this->objNarroProject->ProjectId;

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

            $this->Form_PreRender();

        }

        public function Form_PreRender() {
        }

        public function btnImport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            if ($this->txtImportFromDirectory->Text == '')
                $strDirectory = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
            else
                $strDirectory = $this->txtImportFromDirectory->Text;

            if ($strParameter != 1) {
                $this->btnImport->Visible = false;
                $this->objImportProgress->Visible = true;
                QApplication::ExecuteJavaScript(sprintf('setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 1000));
            }
            else {
                if (!file_exists($strDirectory . '/' . QApplication::$objUser->Language->LanguageCode . '/import.status')) {
                    $this->lblImport->Text = t('Import finished.');
                    $this->lblImport->Visible = true;
                    $this->objImportProgress->Visible = false;
                }
                else {
                    $this->objImportProgress->Translated = (int) trim(file_get_contents($this->txtImportFromDirectory->Text . '/' . QApplication::$objUser->Language->LanguageCode . '/import.status'));
                    $this->objImportProgress->MarkAsModified();
                }
                return true;
            }
            require_once('NarroLog.class.php');
            NarroLog::$strLogFile = '';
            $strCommand = sprintf(
                '/usr/bin/php ' .
                    escapeshellarg(__INCLUDES__ . '/narro/importer/importer.php').
                    ' --import --minloglevel 3 --project %d --user %d ' .
                    (($this->chkValidate->Checked)?'--validate ':'') .
                    (($this->chkForce->Checked)?'--force ':'') .
                    (($this->chkOnlySuggestions->Checked)?'--only-suggestions --do-not-deactivate-files --do-not-deactivate-contexts ':'') .
                    '--check-equal --source-lang en_US --target-lang %s %s',
                $this->objNarroProject->ProjectId,
                QApplication::$objUser->UserId,
                QApplication::$objUser->Language->LanguageCode,
                escapeshellarg($strDirectory)
            );

            proc_close(proc_open ("$strCommand &", array(), $foo));
        }

        public function btnExport_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can manage project', $this->objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            if ($this->txtExportToDirectory->Text == '')
                $strDirectory = __DOCROOT__ . __SUBDIRECTORY__ . __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId;
            else
                $strDirectory = $this->txtExportToDirectory->Text;

            if ($strParameter != 1) {
                $this->btnExport->Visible = false;
                $this->objExportProgress->Visible = true;
                QApplication::ExecuteJavaScript(sprintf('setInterval("qcodo.postAjax(\'%s\', \'%s\', \'QClickEvent\', \'1\');", %d);', $strFormId, $strControlId, 1000));
            }
            else {
                if (!file_exists($strDirectory . '/' . QApplication::$objUser->Language->LanguageCode . '/export.status')) {
                    $this->lblExport->Text =
                        sprintf('<span style="color:green;font-weight:bold;">%s</span><br /><br />', t('Export finished.'));

                    if (file_exists(sprintf('%s/%s-%s.tar.bz2', $strDirectory, $this->objNarroProject->ProjectName, QApplication::$objUser->Language->LanguageCode)))
                        $this->lblExport->Text .= sprintf(' <a href="%s/%s/%d/%s-%s.tar.bz2">%s</a>', __HTTP_URL__, __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . __IMPORT_PATH__, $this->objNarroProject->ProjectId, $this->objNarroProject->ProjectName, QApplication::$objUser->Language->LanguageCode, t('Download the export archive'));

                    $this->lblExport->Visible = true;
                    $this->objExportProgress->Visible = false;
                }
                else {
                    $this->objExportProgress->Translated = (int) trim(file_get_contents($strDirectory . '/' . QApplication::$objUser->Language->LanguageCode . '/export.status'));
                    $this->objExportProgress->MarkAsModified();
                }
                return true;
            }
            require_once('NarroLog.class.php');
            NarroLog::$strLogFile = '';
            $strCommand = sprintf(
                '/usr/bin/php ' .
                    escapeshellarg(__INCLUDES__ . '/narro/importer/importer.php').
                    ' --export --minloglevel 3 --project %d --user %d ' .
                    (($this->chkForce->Checked)?'--force ':'') .
                    '--check-equal --source-lang en_US --target-lang %s %s',
                $this->objNarroProject->ProjectId,
                QApplication::$objUser->UserId,
                QApplication::$objUser->Language->LanguageCode,
                escapeshellarg($strDirectory)
            );

            proc_close(proc_open ("$strCommand &", array(), $foo));
        }
    }

    NarroProjectManageForm::Run('NarroProjectManageForm', 'templates/narro_project_manage.tpl.php');
?>
