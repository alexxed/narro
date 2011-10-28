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

    class NarroReviewForm extends NarroGenericProjectForm {
        protected $pnlMainTab;
        protected $pnlReview;

        protected function Form_Create() {
            parent::Form_Create();
            
            if (QApplication::QueryString('p') < 1) {
    
                $this->pnlMainTab = new QTabPanel($this);
                $this->pnlMainTab->UseAjax = false;
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Projects'), NarroLink::ProjectList());
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::Translate(0, '', NarroTranslatePanel::SHOW_ALL, '', 0, 0, 0, 0, 0));
                $this->pnlReview = new NarroReviewPanel($this->pnlMainTab);
                $this->pnlMainTab->addTab($this->pnlReview, t('Review'));
                if (NarroLanguage::CountAllActive() > 2 || QApplication::HasPermission('Administrator'))
                    $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Languages'), NarroLink::LanguageList());
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Users'), NarroLink::UserList());
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Roles'), NarroLink::RoleList());
                if (QApplication::HasPermissionForThisLang('Administrator'))
                    $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Application Log'), NarroLink::Log());
            }
            else {
                $this->pnlMainTab->replaceTab(new NarroReviewPanel($this->pnlMainTab), t('Review'));
            }
            
            $this->pnlMainTab->SelectedTab = t('Review');
        }
    }

    NarroReviewForm::Run('NarroReviewForm');

