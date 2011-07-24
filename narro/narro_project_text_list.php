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

    class NarroProjectTextListForm extends NarroGenericProjectForm {

        protected function Form_Create() {
            parent::Form_Create();
            
            $this->pnlSelectedTab = new NarroProjectTextListPanel($this->objProject, $this->pnlMainTab);
            
            switch(QApplication::QueryString('tf')) {
                case NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL:
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Texts'), NarroLink::ProjectTextList($this->objProject->ProjectId, NarroTextListForm::SHOW_ALL_TEXTS, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::ContextSuggest($this->objProject->ProjectId, null, null, NarroTextListForm::SHOW_UNTRANSLATED_TEXTS, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->replaceTab($this->pnlSelectedTab, t('Review'));
                    $this->pnlMainTab->SelectedTab = t('Review');
                    break;
                case NarroTextListForm::SHOW_UNTRANSLATED_TEXTS:
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Texts'), NarroLink::ProjectTextList($this->objProject->ProjectId, NarroTextListForm::SHOW_ALL_TEXTS, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->replaceTab($this->pnlSelectedTab, t('Translate'));
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Review'), NarroLink::ContextSuggest($this->objProject->ProjectId, null, null, NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->SelectedTab = t('Translate');
                    break;
                default:
                    $this->pnlMainTab->replaceTab($this->pnlSelectedTab, t('Texts'));
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::ContextSuggest($this->objProject->ProjectId, null, null, NarroTextListForm::SHOW_UNTRANSLATED_TEXTS, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->replaceTab(new QPanel($this->pnlMainTab), t('Review'), NarroLink::ContextSuggest($this->objProject->ProjectId, null, null, NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL, QApplication::QueryString('st'), QApplication::QueryString('s')));
                    $this->pnlMainTab->SelectedTab = t('Texts');
            }
        }
    }

    NarroProjectTextListForm::Run('NarroProjectTextListForm');
?>
