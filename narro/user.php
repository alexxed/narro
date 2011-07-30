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

    class NarroUserProfileForm extends NarroForm {
        protected $pnlTab;
        protected $pnlUserSuggestions;
        protected $objUser;

        protected function Form_Create() {
            parent::Form_Create();

            $this->objUser = NarroUser::Load(QApplication::QueryString('u'));

            if (!$this->objUser instanceof NarroUser)
                QApplication::Redirect(NarroLink::UserList());

            $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), NarroLink::UserList('', t('Users')), $this->objUser->Username);

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlUserSuggestions = new NarroUserSuggestionsPanel($this->objUser, $this->pnlTab);

            $this->pnlTab->addTab($this->pnlUserSuggestions, t('Profile'));

            if (QApplication::GetUserId() == $this->objUser->UserId || QApplication::HasPermissionForThisLang('Can manage users', null))
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Preferences'), NarroLink::UserPreferences($this->objUser->UserId));

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Roles'), NarroLink::UserRole($this->objUser->UserId));

            if (QApplication::GetUserId() == $this->objUser->UserId || QApplication::HasPermissionForThisLang('Can manage users', null))
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Edit'), NarroLink::UserEdit($this->objUser->UserId));

            $this->pnlTab->SelectedTab = 0;
        }
    }

    NarroUserProfileForm::Run('NarroUserProfileForm');
?>
