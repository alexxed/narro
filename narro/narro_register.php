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

    class NarroRegisterForm extends NarroForm {
        protected $pnlTab;
        protected $pnlUserRegister;

        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlBreadcrumb->setElements(NarroLink::ProjectList(t('Projects')), NarroLink::UserList('', t('Users')), t('Register'));

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            $this->pnlUserRegister = new NarroUserRegisterPanel($this->pnlTab);

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Login'), NarroLink::UserLogin());
            $this->pnlTab->addTab($this->pnlUserRegister, t('Register'));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Lost and found'), NarroLink::UserRecoverPassword());

            $this->pnlTab->SelectedTab = t('Register');
        }
    }

    NarroRegisterForm::Run('NarroRegisterForm', 'templates/narro_register.tpl.php');
?>
