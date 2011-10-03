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

    class NarroProjectTranslationTargetPanel extends QPanel {

        protected $pnlMainTab;
        protected $objProject;
        protected $objLanguage;

        public function __construct(NarroProject $objProject, NarroLanguage $objLanguage, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objProject = $objProject;
            $this->objLanguage = $objLanguage;

            $this->pnlMainTab = new QTabPanel($this);
            $this->pnlMainTab->UseAjax = QApplication::$UseAjax;
            $this->pnlMainTab->addTab(new NarroDirectoryTargetPanel($objProject, $objLanguage, $this->pnlMainTab), t('Save to disk'));
            $this->pnlMainTab->addTab(new NarroMercurialTargetPanel($objProject, $objLanguage, $this->pnlMainTab), t('Commit to Mercurial'));
            // @todo finish the implementation for SVN
            // $this->pnlMainTab->addTab(new NarroSvnTargetPanel($objProject, $objLanguage, $this->pnlMainTab), t('Commit to SVN'));
        }

        public function GetControlHtml() {
            $this->strText = $this->pnlMainTab->Render(false);
            return parent::GetControlHtml();
        }

        public function __get($strName) {
            switch ($strName) {
                case "Directory":
                    return $this->pnlMainTab->SelectedTab->Directory;

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
?>
