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

    class NarroRoleListForm extends NarroForm {
        protected $pnlTab;
        protected $pnlRoleList;

        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlRoleList = new QTabPanel($this->pnlTab);

            $this->pnlRoleList->addTab(new NarroRoleListPanel($this->pnlRoleList), t('List'));
            if (QApplication::HasPermissionForThisLang('Can add role', null)) {
                $this->pnlRoleList->addTab(new QPanel($this->pnlRoleList), t('Add'), NarroLink::RoleEdit());
            }

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Projects'), NarroLink::ProjectList());
            if (NarroLanguage::CountAllActive() > 2 || QApplication::HasPermission('Administrator'))
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Languages'), NarroLink::LanguageList());
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Users'), NarroLink::UserList());
            $this->pnlTab->addTab($this->pnlRoleList, t('Roles'));

            $this->pnlTab->SelectedTab = t('Roles');

            $this->pnlRoleList->SelectedTab = 0;
        }
    }

    NarroRoleListForm::Run('NarroRoleListForm', 'templates/narro_role_list.tpl.php');
?>
