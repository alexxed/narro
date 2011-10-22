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

    class NarroGenericProjectForm extends NarroForm {
        /**
         *
         * @var NarroProject
         */
        protected $objProject;
        /**
         * @var QTabPanel
         */
        protected $pnlMainTab;
        protected $pnlSelectedTab;

        protected function Form_Create() {
            parent::Form_Create();

            if ($this->SetupNarroProject() === false)
                return false;

            $this->pnlMainTab = new QTabPanel($this);
            $this->pnlMainTab->UseAjax = false;

            if ($this->objProject instanceof NarroProject)
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Overview'), NarroLink::Project($this->objProject->ProjectId));

            if ($this->objProject instanceof NarroProject && QApplication::HasPermissionForThisLang('Can edit project', $this->objProject->ProjectId))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Edit'), NarroLink::ProjectEdit($this->objProject->ProjectId));
            elseif (QApplication::HasPermission('Can add project'))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Add'));

            if ($this->objProject instanceof NarroProject) {
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::Translate($this->objProject->ProjectId, '', 0, '', 0, 0, 10, 0, 0));
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Files'), NarroLink::ProjectFileList($this->objProject->ProjectId));
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Languages'), NarroLink::ProjectLanguages($this->objProject->ProjectId));
                if (QApplication::HasPermissionForThisLang('Can import project', $this->objProject->ProjectId))
                    $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Import'), NarroLink::ProjectImport($this->objProject->ProjectId));
                if (QApplication::HasPermissionForThisLang('Can export project', $this->objProject->ProjectId))
                    $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Export'), NarroLink::ProjectExport($this->objProject->ProjectId));
            }

        }

        protected function SetupNarroProject() {


            // Lookup Object PK information from Query String (if applicable)
            $intProjectId = QApplication::QueryString('p');

            if ($intProjectId) {
                $this->objProject = NarroProject::Load($intProjectId);


                if (!$this->objProject) {
                    QApplication::Redirect(NarroLink::ProjectList());
                    return false;
                }
                else {
                    $this->pnlBreadcrumb->setElements(
                        NarroLink::ProjectList(t('Projects')),
                        $this->objProject->ProjectName
                    );

                    return true;

                }

            }
        }
    }
?>
