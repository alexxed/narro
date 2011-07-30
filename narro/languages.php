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

    class NarroLanguageListForm extends NarroForm {
        protected $pnlTab;
        protected $pnlLanguageList;

        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlLanguageList = new QTabPanel($this->pnlTab);
            $this->pnlLanguageList->addTab(new NarroLanguageListPanel($this->pnlLanguageList), t('List'));
            if (QApplication::HasPermissionForThisLang('Can add language', null))
                $this->pnlLanguageList->addTab(new QPanel($this->pnlLanguageList), t('Add'), NarroLink::LanguageEdit());

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Projects'), NarroLink::ProjectList());
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Translate'), NarroLink::Translate());
            $this->pnlTab->addTab($this->pnlLanguageList, t('Languages'));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Users'), NarroLink::UserList());
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Roles'), NarroLink::RoleList());

            $this->pnlTab->SelectedTab = 2;
            $this->pnlLanguageList->SelectedTab = 0;
        }
    }

    NarroLanguageListForm::Run('NarroLanguageListForm');
?>
