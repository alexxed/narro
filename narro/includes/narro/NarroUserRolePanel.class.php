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

    class NarroUserRolePanel extends QPanel {
        public $objUser;
        public $dtgUserRole;

        // DataGrid Columns
        protected $colLanguage;
        protected $colProject;
        protected $colRole;
        protected $colActions;

        public $lstProject;
        public $lstLanguage;
        public $lstRole;
        public $btnAddRole;

        public $blnCanManageSomeRoles;

        public function __construct($objUser, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __DOCROOT__ . __SUBDIRECTORY__ . '/templates/NarroUserRolePanel.tpl.php';

            $this->objUser = $objUser;

            // Setup DataGrid Columns
            $this->colLanguage = new QDataGridColumn(t('Language'), '<?= $_CONTROL->ParentControl->dtgUserRole_LanguageColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Language->LanguageName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Language->LanguageName, false)));
            $this->colLanguage->HtmlEntities = false;
            $this->colProject = new QDataGridColumn(t('Project'), '<?= $_CONTROL->ParentControl->dtgUserRole_ProjectColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Project->ProjectName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Project->ProjectName, false)));
            $this->colProject->HtmlEntities = false;
            $this->colRole = new QDataGridColumn(t('Roles'), '<?= $_CONTROL->ParentControl->dtgUserRole_RoleColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Role->RoleName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroUserRole()->Role->RoleName, false)));
            $this->colRole->HtmlEntities = false;
            $this->colActions = new QDataGridColumn(t('Actions'), '<?= $_CONTROL->ParentControl->dtgUserRole_ActionsColumn_Render($_ITEM) ?>');
            $this->colActions->HtmlEntities = false;


            // Setup DataGrid
            $this->dtgUserRole = new QDataGrid($this);
            $this->dtgUserRole->Title = sprintf(t('<b>%s</b>\'s roles'), NarroLink::UserProfile($this->objUser->UserId, $this->objUser->Username));
            $this->dtgUserRole->ShowHeader = true;
            $this->dtgUserRole->Paginator = new QPaginator($this->dtgUserRole);
            $this->dtgUserRole->PaginatorAlternate = new QPaginator($this->dtgUserRole);
            $this->dtgUserRole->ItemsPerPage = NarroApp::$User->getPreferenceValueByName('Items per page');

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgUserRole->UseAjax = NarroApp::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgUserRole->SetDataBinder('dtgUserRole_Bind', $this);

            $this->dtgUserRole->AddColumn($this->colLanguage);
            $this->dtgUserRole->AddColumn($this->colProject);
            $this->dtgUserRole->AddColumn($this->colRole);

            $this->lstLanguage = new QListBox($this);
            $this->lstLanguage->AddItem('Any');
            foreach(NarroLanguage::LoadAllActive(array(QQ::OrderBy(QQN::NarroLanguage()->LanguageName))) as $objNarroLanguage) {
                if (NarroApp::HasPermission('Can manage user roles', null, $objNarroLanguage->LanguageId))
                    $this->blnCanManageSomeRoles = true;
                $this->lstLanguage->AddItem($objNarroLanguage->LanguageName, $objNarroLanguage->LanguageId);
            }

            $this->lstLanguage->SelectedValue = NarroApp::GetLanguageId();

            $this->lstProject = new QListBox($this);
            $this->lstProject->AddItem('Any');
            foreach(NarroProject::QueryArray(QQ::Equal(QQN::NarroProject()->Active, 1), array(QQ::OrderBy(QQN::NarroProject()->ProjectName))) as $objNarroProject) {
                if (NarroApp::HasPermission('Can manage user roles', $objNarroProject->ProjectId))
                    $this->blnCanManageSomeRoles = true;
                $this->lstProject->AddItem($objNarroProject->ProjectName, $objNarroProject->ProjectId);
            }

            if (!$this->blnCanManageSomeRoles && NarroApp::HasPermission('Can manage user roles'))
                $this->blnCanManageSomeRoles = true;

            if ($this->blnCanManageSomeRoles)
                $this->dtgUserRole->AddColumn($this->colActions);

            $this->lstRole = new QListBox($this);
            foreach(NarroRole::LoadAll(array(QQ::OrderBy(QQN::NarroRole()->RoleName))) as $objNarroRole)
                $this->lstRole->AddItem($objNarroRole->RoleName, $objNarroRole->RoleId);

            $this->btnAddRole = new QButton($this);
            $this->btnAddRole->Text = t('Add');
            $this->btnAddRole->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnAddRole_Click'));

        }

        public function Validate() {return true;}
            public function dtgUserRole_LanguageColumn_Render(NarroUserRole $objUserRole) {
            if (is_null($objUserRole->LanguageId))
                return t('Any');
            else
                return $objUserRole->Language->LanguageName;
        }

        public function dtgUserRole_ProjectColumn_Render(NarroUserRole $objUserRole) {
            if (is_null($objUserRole->ProjectId))
                return t('Any');
            else
                return $objUserRole->Project->ProjectName;
        }

        public function dtgUserRole_RoleColumn_Render(NarroUserRole $objUserRole) {
            return NarroLink::RoleList($objUserRole->RoleId, 'user', $objUserRole->Role->RoleName);
        }

        public function dtgUserRole_ActionsColumn_Render(NarroUserRole $objUserRole) {
            $strControlId = 'btnEditRole' . $objUserRole->UserRoleId;
            $btnEdit = $this->Form->GetControl($strControlId);
            if (!$btnEdit) {
                $btnEdit = new QButton($this->dtgUserRole, $strControlId);
                $btnEdit->Text = t('Edit');
                if (NarroApp::$UseAjax)
                    $btnEdit->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnEditRole_Click'));
                else
                    $btnEdit->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnEditRole_Click'));
            }
            $btnEdit->ActionParameter = $objUserRole->UserRoleId;
            $btnEdit->Display = NarroApp::HasPermission('Can manage user roles', $objUserRole->ProjectId, $objUserRole->LanguageId);

            $strControlId = 'btnDeleteRole' . $objUserRole->UserRoleId;
            $btnDelete = $this->Form->GetControl($strControlId);
            if (!$btnDelete) {
                $btnDelete = new QButton($this->dtgUserRole, $strControlId);
                $btnDelete->Text = t('Delete');
                $btnDelete->AddAction(new QClickEvent(), new QConfirmAction(t('Are you sure you want to revoke this role for this user?')));
                if (NarroApp::$UseAjax)
                    $btnDelete->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnDeleteRole_Click'));
                else
                    $btnDelete->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDeleteRole_Click'));
            }
            $btnDelete->ActionParameter = $objUserRole->UserRoleId;
            $btnDelete->Display = NarroApp::HasPermission('Can manage user roles', $objUserRole->ProjectId, $objUserRole->LanguageId);

            return $btnEdit->Render(false) . ' ' . $btnDelete->Render(false);
        }

        public function dtgUserRole_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            // Setup the $objClauses Array
            $objClauses = array();

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgUserRole->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgUserRole->LimitClause)
                array_push($objClauses, $objClause);

            // Set the DataSource to be the array of all NarroUser objects, given the clauses above
            $this->dtgUserRole->DataSource = NarroUserRole::LoadArrayByUserId($this->objUser->UserId, $objClauses);
        }

        public function btnAddRole_Click($strFormId, $strControlId, $strParameter) {

            if (is_numeric($strParameter)) {
                $objUserRole = NarroUserRole::Load($strParameter);
                $this->btnAddRole->Text = t('Add');
                $strControlId = 'btnEditRole' . $strParameter;
                $btnEdit = $this->Form->GetControl($strControlId);
                $btnEdit->Text = t('Edit');
                $this->btnAddRole->ActionParameter = 'a';
            }
            else {
                $objUserRole = new NarroUserRole();
                $objUserRole->UserId = $this->objUser->UserId;
            }

            $objUserRole->LanguageId = $this->lstLanguage->SelectedValue;
            $objUserRole->ProjectId = $this->lstProject->SelectedValue;
            $objUserRole->RoleId = $this->lstRole->SelectedValue;

            if (!NarroApp::HasPermission('Can manage user roles', $objUserRole->ProjectId, $objUserRole->LanguageId)) {
                NarroApp::ExecuteJavaScript(sprintf('alert(\'%s\')', sprintf(t('You don\\\'t have permissions to give permissions on the project %s, language %s'), $this->lstProject->SelectedName, $this->lstLanguage->SelectedName)));
                return false;
            }

            try {
                $objUserRole->Save();
                NarroApp::ResetUser($objUserRole->UserId);
            } catch (QMySqliDatabaseException $objExc) {
                if (strpos($objExc->getMessage(), 'Duplicate entry') === false) {
                    throw $objExc;
                }
                else {
                    //
                }
            }

            $this->dtgUserRole_Bind();
        }

        public function btnEditRole_Click($strFormId, $strControlId, $strParameter) {
            $strControlId = 'btnEditRole' . $strParameter;
            $btnEdit = $this->Form->GetControl($strControlId);

            if ($btnEdit->Text == t('Cancel edit')) {
                $this->btnAddRole->Text = t('Add');
                $btnEdit->Text = t('Edit');
            }
            else {
                $objUserRole = NarroUserRole::Load($strParameter);

                $this->lstLanguage->SelectedValue = $objUserRole->LanguageId;
                $this->lstLanguage->Enabled = NarroApp::HasPermission('Can manage user roles', null, $objUserRole->LanguageId);
                $this->lstProject->SelectedValue = $objUserRole->ProjectId;
                $this->lstProject->Enabled = NarroApp::HasPermission('Can manage user roles', $objUserRole->ProjectId);
                $this->lstRole->SelectedValue = $objUserRole->RoleId;

                $this->btnAddRole->Text = t('Save');
                $this->btnAddRole->ActionParameter = $strParameter;

                $btnEdit->Text = t('Cancel edit');
            }
        }

        public function btnDeleteRole_Click($strFormId, $strControlId, $strParameter) {
            $objUserRole = NarroUserRole::Load($strParameter);

            if (!NarroApp::HasPermission('Can manage user roles', $objUserRole->ProjectId, $objUserRole->LanguageId))
                return false;

            $objUserRole->Delete();
            NarroApp::ResetUser($objUserRole->UserId);

            $this->dtgUserRole_Bind();
        }
    }
