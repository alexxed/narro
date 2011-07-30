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

    class NarroProjectEditPanel extends QPanel {
        // General Panel Variables
        protected $objProject;
        public $strTitleVerb;
        protected $blnEditMode;

        protected $strTemplate;

        // Controls for NarroProject's Data Fields
        public $lblMessage;
        public $lstProjectCategory;
        public $txtProjectName;
        public $lstProjectType;
        public $txtProjectDescription;
        public $txtActive;

        // Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References

        // Button Actions
        public $btnSave;
        public $btnCancel;
        public $btnDelete;

        protected function SetupNarroProject($objNarroProject) {
            if ($objNarroProject) {
                $this->objProject = $objNarroProject;
                $this->strTitleVerb = t('Edit');
                $this->blnEditMode = true;
            } else {
                $this->objProject = new NarroProject();
                $this->strTitleVerb = t('Add');
                $this->blnEditMode = false;
            }
        }

        public function __construct($objNarroProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Call SetupNarroProject to either Load/Edit Existing or Create New
            $this->SetupNarroProject($objNarroProject);

            // Create/Setup Controls for NarroProject's Data Fields
            $this->lblMessage_Create();
            $this->lstProjectCategory_Create();
            $this->txtProjectName_Create();
            $this->lstProjectType_Create();
            $this->txtProjectDescription_Create();
            $this->txtActive_Create();

            // Create/Setup ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References

            // Create/Setup Button Action controls
            $this->btnSave_Create();
            $this->btnCancel_Create();
            $this->btnDelete_Create();

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectEditPanel.tpl.php';
        }

        // Protected Create Methods
        // Create and Setup lblMessage
        protected function lblMessage_Create() {
            $this->lblMessage = new QLabel($this);
        }

        // Create and Setup lstProjectCategory
        protected function lstProjectCategory_Create() {
            $this->lstProjectCategory = new QListBox($this);
            $objProjectCategoryArray = NarroProjectCategory::LoadAll();
            if ($objProjectCategoryArray) foreach ($objProjectCategoryArray as $objProjectCategory) {
                $objListItem = new QListItem($objProjectCategory->CategoryName, $objProjectCategory->ProjectCategoryId);
                if (($this->objProject->ProjectCategory) && ($this->objProject->ProjectCategory->ProjectCategoryId == $objProjectCategory->ProjectCategoryId))
                    $objListItem->Selected = true;
                $this->lstProjectCategory->AddItem($objListItem);
            }
        }

        // Create and Setup txtProjectName
        protected function txtProjectName_Create() {
            $this->txtProjectName = new QTextBox($this);
            $this->txtProjectName->Text = $this->objProject->ProjectName;
            $this->txtProjectName->Required = true;
            $this->txtProjectName->MaxLength = NarroProject::ProjectNameMaxLength;
        }

        // Create and Setup lstProjectType
        protected function lstProjectType_Create() {
            $this->lstProjectType = new QListBox($this);
            $this->lstProjectType->Required = true;
            foreach (NarroProjectType::$NameArray as $intId => $strValue)
                $this->lstProjectType->AddItem(new QListItem($strValue, $intId, $this->objProject->ProjectType == $intId));
        }

        // Create and Setup txtProjectDescription
        protected function txtProjectDescription_Create() {
            $this->txtProjectDescription = new QTextBox($this);
            $this->txtProjectDescription->Text = $this->objProject->ProjectDescription;
            $this->txtProjectDescription->MaxLength = NarroProject::ProjectDescriptionMaxLength;
            $this->txtProjectDescription->TextMode = QTextMode::MultiLine;
            $this->txtProjectDescription->Rows = 3;
            $this->txtProjectDescription->Width = 400;
        }

        // Create and Setup txtActive
        protected function txtActive_Create() {
            $this->txtActive = new QCheckBox($this);
            $this->txtActive->Checked = $this->objProject->Active;
        }


        // Setup btnSave
        protected function btnSave_Create() {
            $this->btnSave = new QButton($this);
            $this->btnSave->Text = t('Save');
            $this->btnSave->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSave_Click'));
            $this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
        }

        // Setup btnCancel
        protected function btnCancel_Create() {
            $this->btnCancel = new QButton($this);
            $this->btnCancel->Text = t('Cancel');
            $this->btnCancel->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnCancel_Click'));
            $this->btnCancel->CausesValidation = false;
        }

        // Setup btnDelete
        protected function btnDelete_Create() {
            $this->btnDelete = new QButton($this);
            $this->btnDelete->Text = t('Delete');
            $this->btnDelete->AddAction(new QClickEvent(), new QConfirmAction(sprintf(t('Are you SURE you want to DELETE %s?\nThe texts and associated suggestions will NOT be deleted.\nThey will be preserved for use in other projects.'), addslashes($this->objProject->ProjectName))));
            $this->btnDelete->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDelete_Click'));
            $this->btnDelete->CausesValidation = false;
            $this->btnDelete->Visible = $this->blnEditMode;
        }

        // Protected Update Methods
        protected function UpdateNarroProjectFields() {
            $this->objProject->ProjectCategoryId = $this->lstProjectCategory->SelectedValue;
            $this->objProject->ProjectName = $this->txtProjectName->Text;
            $this->objProject->ProjectType = $this->lstProjectType->SelectedValue;
            $this->objProject->ProjectDescription = $this->txtProjectDescription->Text;
            $this->objProject->Active = (int) $this->txtActive->Checked;
        }


        // Control ServerActions
        public function btnSave_Click($strFormId, $strControlId, $strParameter) {
            $this->UpdateNarroProjectFields();

            try {
                $this->objProject->Save();
            }
            catch (Exception $objEx) {
                $this->lblMessage->Text = $objEx->getMessage();
                return false;
            }

            $objProjectProgress = NarroProjectProgress::LoadByProjectIdLanguageId($this->objProject->ProjectId, QApplication::GetLanguageId());
            $objProjectProgress->Active = $this->txtActive->Checked;
            $objProjectProgress->Save();

            $this->lblMessage->Text = t('Project saved sucessfully.');

            if ($this->strTitleVerb == t('Add')) {
                /**
                 * If a new project is added, the project directory and source and target are created
                 * Also sample export.sh and import.sh are written in the project directory, ready for use
                 */
                $strProjectDir = __IMPORT_PATH__ . '/' . $this->objProject->ProjectId;
                if (!file_exists($strProjectDir)) {
                    mkdir($strProjectDir, 0777);
                    chmod($strProjectDir, 0777);
                    file_put_contents(
                        $strProjectDir . '/export.sh',
                        "#!/bin/bash\n".
                        "# \$1 - language code\n".
                        "# \$2 - language id\n".
                        "# \$3 - project name\n".
                        "# \$4 - project id\n".
                        "# \$5 - user id\n".
                        "\n" .
                        sprintf("echo \"You can run commands before import or after export by editing export.sh and import.sh from '%s'\"\n", $strProjectDir) .
                        "export retVal=\$?\n".
                        "# the script will exit with the echo command exit code, 0 = successful run\n".
                        "exit \$retVal"
                    );
                    copy($strProjectDir . '/export.sh', $strProjectDir . '/import.sh');
                    chmod($strProjectDir . '/export.sh', 0666);
                    chmod($strProjectDir . '/import.sh', 0666);

                    if (!file_exists($strProjectDir . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE))
                        mkdir($strProjectDir . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE, 0777);

                    if (!file_exists($strProjectDir . '/' . QApplication::GetLanguageId()))
                        mkdir($strProjectDir . '/' . QApplication::GetLanguageId(), 0777);
                }
                QApplication::Redirect(NarroLink::ProjectImport($this->objProject->ProjectId));
            }
        }

        public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
            QApplication::Redirect(NarroLink::ProjectList());
        }

        public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermission('Can delete project', $this->objProject->ProjectId))
                QApplication::Redirect(NarroLink::ProjectList());

            $objDatabase = QApplication::$Database[1];

            try {
                $strQuery = sprintf("DELETE FROM narro_context_comment USING narro_context_comment LEFT JOIN narro_context ON narro_context_comment.context_id=narro_context.context_id WHERE narro_context_comment.language_id=%d AND narro_context.project_id=%d", QApplication::GetLanguageId(), $this->objProject->ProjectId);
                $objDatabase->NonQuery($strQuery);
                $strQuery = sprintf("DELETE FROM narro_context_info USING narro_context_info LEFT JOIN narro_context ON narro_context_info.context_id=narro_context.context_id WHERE narro_context_info.language_id=%d AND narro_context.project_id=%d", QApplication::GetLanguageId(), $this->objProject->ProjectId);
                $objDatabase->NonQuery($strQuery);
                $strQuery = sprintf("DELETE FROM `narro_context` WHERE project_id = %d", $this->objProject->ProjectId);
                $objDatabase->NonQuery($strQuery);
                $strQuery = sprintf("DELETE FROM `narro_file` WHERE project_id = %d", $this->objProject->ProjectId);
                $objDatabase->NonQuery($strQuery);
                $strQuery = sprintf("DELETE FROM `narro_user_role` WHERE project_id = %d", $this->objProject->ProjectId);
                $objDatabase->NonQuery($strQuery);

                $this->objProject->Delete();
            }
            catch (Exception $objEx) {
                $this->lblMessage->Text = $objEx->getMessage();
                return false;
            }

            QApplication::Redirect(NarroLink::ProjectList());
        }

    }
