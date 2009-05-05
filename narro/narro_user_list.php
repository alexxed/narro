<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008 Alexandru Szasz <alexxed@gmail.com>
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
    require_once('includes/prepend.inc.php');

    class NarroUserListForm extends QForm {
        protected $pnlTab;
        protected $pnlUserList;

        protected function Form_Create() {
            parent::Form_Create();
            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlUserList = new NarroUserListPanel($this->pnlTab);

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Projects'), NarroLink::ProjectList());
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Languages'), NarroLink::LanguageList());
            $this->pnlTab->addTab($this->pnlUserList, t('Users'));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Roles'), NarroLink::RoleList());

            $this->pnlTab->SelectedTab = 2;
        }
    }

    NarroUserListForm::Run('NarroUserListForm', 'templates/narro_user_list.tpl.php');

?>