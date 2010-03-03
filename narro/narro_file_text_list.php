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

    class NarroFileTextListForm extends NarroGenericProjectForm {
        protected $objNarroProject;
        protected $objNarroFile;

        protected function Form_Create() {
            parent::Form_Create();
            
            $this->pnlSelectedTab = new NarroFileTextListPanel($this->objNarroProject, $this->objNarroFile, $this->pnlMainTab);
            $this->pnlMainTab->replaceTab($this->pnlSelectedTab, t('Texts'));
            $this->pnlMainTab->SelectedTab = t('Texts');
        }
        
        protected function SetupNarroProject() {
            parent::SetupNarroProject();
            // Lookup Object PK information from Query String (if applicable)
            $intFileId = QApplication::QueryString('f');
            
            if ($intFileId) {
                $this->objNarroFile = NarroFile::Load(($intFileId));

                if (!$this->objNarroFile instanceof NarroFile) {
                    QApplication::Redirect(NarroLink::ProjectFileList($this->objNarroProject->ProjectId));
                    return false;
                }

            } else {
                QApplication::Redirect(NarroLink::ProjectFileList($this->objNarroProject->ProjectId));
                return false;
            }
        }
    }

    NarroFileTextListForm::Run('NarroFileTextListForm', 'templates/narro_file_text_list.tpl.php');
?>
