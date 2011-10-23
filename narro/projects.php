<?php
    /**
     * This page shows the project list inside a tab panel.
     * Being an entry point for users, it should be fast and easy to understand
     *
     * @package Narro
     * @subpackage Forms
     *
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

    /**
     * This page is used to clean stale form states if FileFormStateHandler is used
     */
    NarroUtils::CleanStaleFormStates();

    class NarroProjectListForm extends NarroForm {
        /**
         * The main tab is the tab panel, used to group panels in a tabbed control for easy access
         * @var QTabPanel
         */
        protected $pnlTab;
        /**
         * This is the project list panel
         * @see includes/narro/panel/NarroProjectListPanel.class.php
         * @var NarroProjectListPanel
         */
        protected $pnlProjectList;

        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlTab = new QTabPanel($this);
            $this->pnlTab->UseAjax = false;

            /**
             * Create the project list panel and set the filter from the url.
             * The filter is used to show only projects of a given status based on their progress
             * (finished, empty, in progress).
             */
            $this->pnlProjectList = new NarroProjectListPanel($this->pnlTab);

            $this->pnlTab->addTab($this->pnlProjectList, t('Projects'));
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Translate'), NarroLink::Translate(0, '', NarroTranslatePanel::SHOW_ALL, '', 0, 0, 10, 0, 0));

            /**
             * Do not show the langauge tab if only two languages are active (source and target
             * Unless the user is an administrator and might want to set another one active
             */
            if (NarroLanguage::CountAllActive() > 2 || QApplication::HasPermission('Administrator'))
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Languages'), NarroLink::LanguageList());

            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Users'), NarroLink::UserList());
            $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Roles'), NarroLink::RoleList());
            if (QApplication::HasPermissionForThisLang('Administrator'))
                $this->pnlTab->addTab(new QPanel($this->pnlTab), t('Application Log'), NarroLink::Log());

        }
    }

    NarroProjectListForm::Run('NarroProjectListForm');
?>
