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

    require_once('includes/configuration/prepend.inc.php');

    class NarroProjectEditForm extends NarroForm {
        protected $pnlTab;
        protected $pnlProjectEdit;
        protected $objNarroProject;

        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            // Set mode to Edit or New depending on what's found
            $intProjectId = QApplication::QueryString('p');
            $this->objNarroProject = NarroProject::Load(($intProjectId));
        }
        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->SetupNarroProject();

            $this->pnlProjectEdit = new NarroProjectEditPanel($this->objNarroProject, $this->pnlTab);

            if ($this->objNarroProject instanceof NarroProject) {
                if (!QApplication::HasPermissionForThisLang('Can edit project', $this->objNarroProject->ProjectId))
                    QApplication::Redirect(NarroLink::ProjectList());

                $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), NarroLink::ProjectTextList($this->objNarroProject->ProjectId, null, null, null, $this->objNarroProject->ProjectName), t('Edit'));
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Import'), NarroLink::ProjectImport($this->objNarroProject->ProjectId));
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Export'), NarroLink::ProjectExport($this->objNarroProject->ProjectId));
                $this->pnlTab->addTab($this->pnlProjectEdit, t('Edit'));
                $this->pnlTab->SelectedTab = t('Edit');
            }
            else {
                if (!QApplication::HasPermissionForThisLang('Can add project'))
                    QApplication::Redirect(NarroLink::ProjectList());

                $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), t('Add'));
                $this->pnlTab->addTab($this->pnlProjectEdit, t('Add'));
                $this->pnlTab->SelectedTab = t('Add');
            }
        }
    }


    NarroProjectEditForm::Run('NarroProjectEditForm', 'templates/narro_project_edit.tpl.php');
?>
