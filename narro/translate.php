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

    require_once(dirname(__FILE__) . '/configuration/prepend.inc.php');

    class NarroTranslateForm extends NarroGenericProjectForm {
        protected $pnlMainTab;
        protected $pnlTranslate;

        protected function Form_Create() {
            parent::Form_Create();

            if (QApplication::QueryString('p') == '') {
                $this->pnlMainTab = new QTabPanel($this);
                $this->pnlMainTab->UseAjax = false;
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Projects'), NarroLink::ProjectList());
                $this->pnlTranslate = new NarroTranslatePanel($this->pnlMainTab);
                $this->pnlMainTab->addTab($this->pnlTranslate, t('Translate'));
                if (NarroLanguage::CountAllActive() > 2 || QApplication::HasPermission('Administrator'))
                    $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Languages'), NarroLink::LanguageList());
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Users'), NarroLink::UserList());
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Roles'), NarroLink::RoleList());

                $this->pnlMainTab->SelectedTab = 1;
            }
            else {
                $this->pnlTranslate = new NarroTranslatePanel($this->pnlMainTab);
                $this->pnlMainTab->replaceTab($this->pnlTranslate, t('Translate'));
                $this->pnlMainTab->SelectedTab = 3;
            }
        }
    }

    NarroTranslateForm::Run('NarroTranslateForm');

