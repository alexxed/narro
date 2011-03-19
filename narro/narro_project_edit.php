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

    require_once(dirname(__FILE__) . '/configuration/prepend.inc.php');

    class NarroProjectEditForm extends NarroGenericProjectForm {
        protected function Form_Create() {
            parent::Form_Create();
            
            if ($this->objNarroProject instanceof NarroProject && !QApplication::HasPermissionForThisLang('Can edit project', $this->objNarroProject->ProjectId))
                QApplication::Redirect(NarroLink::ProjectList());
            elseif (!$this->objNarroProject && !QApplication::HasPermissionForThisLang('Can add project'))
                QApplication::Redirect(NarroLink::ProjectList());
            
            if ($this->objNarroProject instanceof NarroProject)
                $strTabTitle = t('Edit');
            else
                $strTabTitle = t('Add');
                
            $this->pnlMainTab->replaceTab(new NarroProjectEditPanel($this->objNarroProject, $this->pnlMainTab), $strTabTitle);
            $this->pnlMainTab->SelectedTab = $strTabTitle;
        }
        
        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            $intProjectId = QApplication::QueryString('p');
            if (($intProjectId)) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                $this->pnlBreadcrumb->setElements(
                    NarroLink::ProjectList(t('Projects')),
                    $this->objNarroProject->ProjectName
                );
            }
            else {
                $this->pnlBreadcrumb->setElements(
                    NarroLink::ProjectList(t('Projects')),
                    t('Add project')
                );
            }

        }
    }

    NarroProjectEditForm::Run('NarroProjectEditForm');
?>
