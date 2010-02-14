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

    class NarroProjectExportForm extends NarroForm {
        protected $pnlTab;
        protected $pnlProjectExport;
        protected $objNarroProject;

        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            // Set mode to Edit or New depending on what's found
            $intProjectId = QApplication::QueryString('p');
            if ($intProjectId > 0) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                if (!$this->objNarroProject)
                    QApplication::Redirect(NarroLink::ProjectList());

            } else
                QApplication::Redirect(NarroLink::ProjectList());

        }
        protected function Form_Create() {
            parent::Form_Create();

            $this->SetupNarroProject();

            if (!QApplication::HasPermissionForThisLang('Can manage project', $this->objNarroProject->ProjectId))
                QApplication::Redirect(NarroLink::ProjectList());

            $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), NarroLink::ProjectTextList($this->objNarroProject->ProjectId, null, null, null, $this->objNarroProject->ProjectName), 'Manage');

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlProjectExport = new NarroProjectExportPanel($this->objNarroProject, $this->pnlTab);

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Overview'), NarroLink::Project($this->objNarroProject->ProjectId));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Files'), NarroLink::ProjectFileList($this->objNarroProject->ProjectId, ''));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Translate'), NarroLink::ContextSuggest($this->objNarroProject->ProjectId, null, null, 2));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Review'), NarroLink::ContextSuggest($this->objNarroProject->ProjectId, null, null, 4));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Import'), NarroLink::ProjectImport($this->objNarroProject->ProjectId));
            $this->pnlTab->addTab($this->pnlProjectExport, t('Export'));

            $this->pnlTab->SelectedTab = t('Export');
        }
    }


    NarroProjectExportForm::Run('NarroProjectExportForm', 'templates/narro_project_export.tpl.php');
?>
