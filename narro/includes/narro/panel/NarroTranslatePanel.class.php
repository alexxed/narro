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


    class NarroTranslatePanel extends QPanel {

        public $dtrText;
        public $btnMore;
        public $btnLess;
        public $intMaxRowCount = 0;
        public $intStart = 0;
        public $objWaitIcon;

        public $lstProject;
        public $txtFile;
        public $lstFilter;
        public $txtSearch;
        public $lstSort;
        public $lstSortDir;
        public $btnSearch;
        public $chkLast;

        public $intTotalItemCount;
        public $strCurrentTranslationId;
        protected $arrConditions;
        protected $arrClauses;

        const SHOW_NOT_TRANSLATED = 1;
        const SHOW_NOT_APPROVED = 2;
        const SHOW_APPROVED_AND_NOT_APPROVED = 3;
        const SHOW_APPROVED = 4;
        const SHOW_ALL = 5;

        const SORT_TEXT = 1;
        const SORT_TRANSLATION = 2;
        const SORT_TEXT_LENGTH = 3;

        public function __construct($objParentObject, $strControlId = null) {
            parent::__construct($objParentObject, $strControlId);

            $this->strTemplate = dirname(__FILE__) . '/' . __CLASS__ . '.tpl.php';

            $this->chkLast = new QCheckBox($this, 'endReached');
            $this->chkLast->DisplayStyle = QDisplayStyle::None;

            $this->dtrText = new QDataRepeater($this);

            $this->dtrText->Template = dirname(__FILE__) . '/NarroTranslatePanel_DataRepeater.tpl.php';

            $this->dtrText->SetDataBinder('dtrText_Bind', $this);

            $this->objWaitIcon = new QWaitIcon($this);
            $this->objWaitIcon->Text = sprintf('<div align="center"><img align="center" src="%s/loading45.gif" width="100" height="100" alt="Loading..."/></div>', __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/assets/images');


            $this->btnMore = new QButton($this);
            $this->btnMore->Text = 'More';
            $this->btnMore->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnMore_Click', $this->objWaitIcon));

            $this->lstProject = new QListBox($this);
            $this->lstProject->AddItem('All');
            foreach(NarroProject::LoadArrayByActive(1, array(QQ::OrderBy(QQN::NarroProject()->ProjectName))) as $objProject)
                $this->lstProject->AddItem($objProject->ProjectName, $objProject->ProjectId);
            $this->lstProject->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));

            if (QApplication::QueryString('p') > 0)
                $this->lstProject->SelectedValue = QApplication::QueryString('p');

            $this->txtFile = new QTextBox($this);
            if (QApplication::QueryString('f'))
                $this->txtFile->Text = QApplication::QueryString('f');

            $this->lstFilter = new QListBox($this);
            $this->lstFilter->AddItem('All', self::SHOW_ALL);
            $this->lstFilter->AddItem('Not translated yet', self::SHOW_NOT_TRANSLATED, true);
            $this->lstFilter->AddItem('Translated, but not approved', self::SHOW_NOT_APPROVED);
            $this->lstFilter->AddItem('Translated or approved', self::SHOW_APPROVED_AND_NOT_APPROVED);
            $this->lstFilter->AddItem('Approved', self::SHOW_APPROVED);
            $this->lstFilter->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));

            if (QApplication::QueryString('t'))
                $this->lstFilter->SelectedValue = QApplication::QueryString('t');

            $this->txtSearch = new QTextBox($this);
            if (QApplication::QueryString('s'))
                $this->txtSearch->Text = QApplication::QueryString('s');

            $this->lstSort = new QListBox($this);
            $this->lstSort->AddItem('-- unsorted --');
            $this->lstSort->AddItem('Original text', self::SORT_TEXT);
            $this->lstSort->AddItem('Translation', self::SORT_TRANSLATION);
            $this->lstSort->AddItem('Words in the original text', self::SORT_TEXT_LENGTH);
            $this->lstSort->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));

            if (QApplication::QueryString('o'))
                $this->lstSort->SelectedValue = QApplication::QueryString('o');

            $this->lstSortDir = new QListBox($this);
            $this->lstSortDir->AddItem('Ascending', 1, true);
            $this->lstSortDir->AddItem('Descending', 0);
            $this->lstSortDir->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));

            if (QApplication::QueryString('h'))
                $this->lstSortDir->SelectedValue = QApplication::QueryString('h');

            $this->btnSearch = new QButton($this);
            $this->btnSearch->PrimaryButton = true;
            $this->btnSearch->Text = 'Search';
            $this->btnSearch->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));

            if (QApplication::QueryString('a'))
                $this->intMaxRowCount = QApplication::QueryString('a');

            $this->intStart = max(0, QApplication::QueryString('i'));

            $this->btnLess_Create();

            $this->btnMore->DisplayStyle = QDisplayStyle::Block;
            $this->dtrText_Conditions(false);
            $this->intTotalItemCount = NarroContextInfo::QueryCount(QQ::AndCondition($this->arrConditions));
            $this->dtrText_Bind(null, null, null, false);
        }

        public function btnLess_Create() {
            $this->btnLess = new QButton($this);
            $this->btnLess->Text = 'Less';
            $this->btnLess->Display = ($this->intStart > 0);
            $this->btnLess->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnLess_Click', $this->objWaitIcon));
        }

        public function btnLess_Click($strFormId = null, $strControlId = null, $strParameter = null) {
            $this->intStart = max (0, $this->intStart -= 10);
            $this->dtrText_Conditions(false);
            $this->dtrText_Bind(null, null, null, false);
        }

        public function btnMore_Click($strFormId = null, $strControlId = null, $strParameter = null) {
            $this->dtrText_Conditions(false);
            $this->dtrText_Bind(null, null, null, false);
        }

        public function btnSearch_Click($strFormId = null, $strControlId = null, $strParameter = null) {
            $this->btnMore->DisplayStyle = QDisplayStyle::Block;
            $this->dtrText_Conditions(true);
            $this->intTotalItemCount = NarroContextInfo::QueryCount(QQ::AndCondition($this->arrConditions));
            $this->dtrText_Bind(null, null, null, true);
        }

        public function txtTranslation_Focus($strFormId, $strControlId, $strParameter) {
            foreach($this->dtrText->GetChildControls() as $ctl) {
                if ($ctl instanceof NarroContextInfoEditor) {
                    $blnSaveResult = $ctl->btnSave_Click($strFormId, $strControlId, $strParameter);

                    if ($ctl->Translation->ControlId == $strControlId) {
                        $this->strCurrentTranslationId = $ctl->Translation->ControlId;
                    }
                }
            }
        }

        protected function dtrText_Conditions($blnReset = false) {
            $this->arrConditions = array(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, true),
                    QQ::Equal(QQN::NarroContextInfo()->Context->File->Active, true)
                )
            );
            if ($blnReset)
                $this->intMaxRowCount = 0;

            $this->arrClauses = array(
                QQ::LimitInfo($this->intMaxRowCount+=10, $this->intStart),
                QQ::Expand(QQN::NarroContextInfo()->Context),
                QQ::Expand(QQN::NarroContextInfo()->Context->Text),
                QQ::Expand(QQN::NarroContextInfo()->Context->File),
                QQ::Expand(QQN::NarroContextInfo()->Context->Project),
                QQ::Expand(QQN::NarroContextInfo()->ValidSuggestion)
            );

            if ($this->lstProject->SelectedValue > 0)
                $this->arrConditions[] = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->lstProject->SelectedValue);

            switch($this->lstFilter->SelectedValue) {
                case self::SHOW_NOT_TRANSLATED:
                    $this->arrConditions[] = QQ::Equal(QQN::NarroContextInfo()->HasSuggestions, false);
                    break;

                case self::SHOW_NOT_APPROVED:
                    $this->arrConditions[] = QQ::IsNull(QQN::NarroContextInfo()->ValidSuggestionId);
                    break;

                case self::SHOW_APPROVED:
                    $this->arrConditions[] = QQ::IsNotNull(QQN::NarroContextInfo()->ValidSuggestionId);
                    break;

                case self::SHOW_APPROVED_AND_NOT_APPROVED:
                    $this->arrConditions[] = QQ::Equal(QQN::NarroContextInfo()->HasSuggestions, true);
                    break;

            }

            if ($this->txtFile->Text)
                if (preg_match("/^'.*'$/", $this->txtFile->Text))
                    $this->arrConditions[] = QQ::Equal(QQN::NarroContextInfo()->Context->File->FilePath, substr($this->txtFile->Text, 1, -1));
                else
                    $this->arrConditions[] = QQ::Like(QQN::NarroContextInfo()->Context->File->FilePath, '%' . $this->txtFile->Text . '%');

            if ($this->txtSearch->Text) {
                if (preg_match("/^'.*'$/", $this->txtSearch->Text))
                    $this->arrConditions[] = QQ::OrCondition(
                        QQ::Equal(QQN::NarroContextInfo()->Context->Text->TextValue, substr($this->txtSearch->Text, 1, -1)),
                        QQ::Equal(QQN::NarroContextInfo()->Context->Context, substr($this->txtSearch->Text, 1, -1))
                    );
                else
                    $this->arrConditions[] = QQ::OrCondition(
                        QQ::Like(QQN::NarroContextInfo()->Context->Text->TextValue, '%' . $this->txtSearch->Text . '%'),
                        QQ::Like(QQN::NarroContextInfo()->Context->Context, '%' . $this->txtSearch->Text . '%')
                    );

            }

            switch($this->lstSort->SelectedValue) {
                case self::SORT_TEXT:
                    $this->arrClauses[] = QQ::OrderBy(QQN::NarroContextInfo()->Context->Text->TextValue, $this->lstSortDir->SelectedValue);
                    break;
                case self::SORT_TEXT_LENGTH:
                    $this->arrClauses[] = QQ::OrderBy(QQN::NarroContextInfo()->Context->Text->TextWordCount, $this->lstSortDir->SelectedValue);
                    break;
                case self::SORT_TRANSLATION:
                    $this->arrClauses[] = QQ::OrderBy(QQN::NarroContextInfo()->ValidSuggestion->SuggestionValue, $this->lstSortDir->SelectedValue);
                    break;

            }
        }

        public function dtrText_Bind($strFormId = null, $strControlId = null, $strParameter = null, $blnReset = false) {
            if ($blnReset) $this->dtrText->RemoveChildControls(true);
            $this->dtrText->DataSource = NarroContextInfo::QueryArray(
                QQ::AndCondition($this->arrConditions),
                $this->arrClauses
            );

            if ($this->intStart == 0)
                $this->btnLess->Display = false;

            if ($this->intTotalItemCount == $this->dtrText->ItemCount) {
                $this->chkLast->Checked = true;
                $this->btnMore->DisplayStyle = QDisplayStyle::None;
            }
            else {
                $this->chkLast->Checked = false;
            }

            if ($this->strCurrentTranslationId) {
                $txtTranslation = $this->Form->GetControl($this->strCurrentTranslationId);
                if ($txtTranslation instanceof QTextBox)
                    $txtTranslation->Focus();
            }
        }
    }
