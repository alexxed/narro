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

    class NarroProjectForm extends NarroForm {
        protected $objNarroProject;
        protected $pnlMainTab;

        protected function Form_Create() {
            parent::Form_Create();
            
            $this->SetupNarroProject();

            $this->pnlMainTab = new QTabPanel($this);
            $this->pnlMainTab->UseAjax = false;

            $this->pnlMainTab->addTab(new NarroProjectPanel($this->objNarroProject, $this->pnlMainTab), t('Overview'));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Files'), NarroLink::ProjectFileList($this->objNarroProject->ProjectId, ''));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Texts'), NarroLink::ProjectTextList($this->objNarroProject->ProjectId, ''));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::ContextSuggest($this->objNarroProject->ProjectId, null, null, 2));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Review'), NarroLink::ContextSuggest($this->objNarroProject->ProjectId, null, null, 4));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Import'), NarroLink::ProjectImport($this->objNarroProject->ProjectId));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Export'), NarroLink::ProjectExport($this->objNarroProject->ProjectId));
            
        }
        
        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            $intProjectId = QApplication::QueryString('p');
            if (($intProjectId)) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                if (!$this->objNarroProject) {
                    QApplication::Redirect(NarroLink::ProjectList());
                    return false;
                }

            } else {
                QApplication::Redirect(NarroLink::ProjectList());
                return false;
            }

            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectList(t('Projects')),
                $this->objNarroProject->ProjectName
            );
        }
        
    }

    NarroProjectForm::Run('NarroProjectForm', 'templates/narro_project.tpl.php');
?>
