<?php
    /**
     * This page shows the project list inside a tab panel.
     * Being an entry point for users, it should be fast and easy to understand
     *
     * @package Narro
     * @subpackage Forms
     *
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

    class NarroTextCommentListForm extends NarroForm {
        /**
         * The main tab is the tab panel, used to group panels in a tabbed control for easy access
         * @var QTabPanel
         */
        protected $pnlMainTab;
        /**
         * This is the debate panel
         * @see includes/narro/panel/NarroDebatePanel.class.php
         * @var NarroDebatePanel
         */
        protected $pnlTextCommentList;

        protected function Form_Create() {
            parent::Form_Create();

            $this->pnlMainTab = new QTabPanel($this);
            $this->pnlMainTab->UseAjax = false;

            $this->pnlTextCommentList = new NarroTextCommentListPanel($this->pnlMainTab, 'pnlTextCommentList_SetEdit', 'pnlTextCommentList_CloseEdit');

            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Projects'), NarroLink::ProjectList());

            /**
             * Do not show the langauge tab if only two languages are active (source and target
             * Unless the user is an administrator and might want to set another one active
             */
            if (NarroLanguage::CountAllActive() > 2 || QApplication::HasPermission('Administrator'))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Languages'), NarroLink::LanguageList());

            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Users'), NarroLink::UserList());
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Roles'), NarroLink::RoleList());
            $this->pnlMainTab->addTab($this->pnlTextCommentList, t('Comments'));

            $this->pnlMainTab->SelectedTab = t('Comments');

        }
    }

    NarroTextCommentListForm::Run('NarroTextCommentListForm', 'templates/narro_text_comment_list.tpl.php');
?>
