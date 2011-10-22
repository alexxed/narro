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

    class NarroUserEditForm extends NarroForm {
        protected $pnlTab;
        protected $pnlUser;
        protected $objUser;

        protected function Form_Create() {
            parent::Form_Create();

            if (QApplication::GetUserId() != QApplication::QueryString('u') && QApplication::HasPermissionForThisLang('Can manage users', null))
                $this->objUser = NarroUser::Load(QApplication::QueryString('u'));

            if (!$this->objUser instanceof NarroUser)
                $this->objUser = QApplication::$User;

            $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), NarroLink::UserList('', t('Users')), $this->objUser->RealName);

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlUser = new NarroUserEditPanel($this->objUser, $this->pnlTab);

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Profile'), NarroLink::UserProfile($this->objUser->UserId));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Preferences'), NarroLink::UserPreferences($this->objUser->UserId));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Roles'), NarroLink::UserRole($this->objUser->UserId));
            $this->pnlTab->addTab($this->pnlUser, t('Edit'));

            $this->pnlTab->SelectedTab = 3;
        }
    }

    NarroUserEditForm::Run('NarroUserEditForm');
?>
