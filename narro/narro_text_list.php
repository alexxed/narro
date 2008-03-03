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

    class NarroTextListForm extends QForm {
        protected $dtgNarroTextContext;

        // DataGrid Columns
        protected $colContext;
        protected $colOriginalText;
        protected $colTranslatedText;
        protected $colActions;


        protected $lstTextFilter;
        protected $txtSearch;
        protected $lstSearchType;
        protected $btnSearch;

        protected $lblMessage;

        const SHOW_ALL_TEXTS = 1;
        const SHOW_UNTRANSLATED_TEXTS = 2;
        const SHOW_VALIDATED_TEXTS = 3;
        const SHOW_TEXTS_THAT_REQUIRE_VALIDATION = 4;

        const SEARCH_TEXTS = 1;
        const SEARCH_SUGGESTIONS = 2;
        const SEARCH_CONTEXTS = 3;

        protected function Form_Create() {
            $this->SetupNarroObject();

            // Setup DataGrid Columns
            $this->colContext = new QDataGridColumn(
                QApplication::Translate('Context'),
                '<?= $_FORM->dtgNarroTextContext_Context_Render($_ITEM); ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(QQN::NarroTextContext()->Context),
                    'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroTextContext()->Context, false)
                )
            );
            $this->colContext->BackColor = 'lightgreen';
            $this->colOriginalText = new QDataGridColumn(
                QApplication::Translate('Original text'),
                '<?= $_FORM->dtgNarroTextContext_OriginalText_Render($_ITEM); ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(QQN::NarroTextContext()->Text->TextValue),
                    'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroTextContext()->Text->TextValue, false)
                )
            );
            $this->colTranslatedText = new QDataGridColumn(
                QApplication::Translate('Translated text'),
                '<?= $_FORM->dtgNarroTextContext_TranslatedText_Render($_ITEM); ?>'
            );
            $this->colTranslatedText->HtmlEntities = false;
            $this->colActions = new QDataGridColumn(
                QApplication::Translate('Actions'),
                '<?= $_FORM->dtgNarroTextContext_Actions_Render($_ITEM); ?>'
            );
            $this->colActions->HtmlEntities = false;

            // Setup DataGrid
            $this->dtgNarroTextContext = new QDataGrid($this);
            $this->dtgNarroTextContext->CellSpacing = 0;
            $this->dtgNarroTextContext->CellPadding = 4;
            $this->dtgNarroTextContext->BorderStyle = QBorderStyle::Solid;
            $this->dtgNarroTextContext->BorderWidth = 1;
            $this->dtgNarroTextContext->GridLines = QGridLines::Both;
            $this->dtgNarroTextContext->Width = '100%';

            // Datagrid Paginator
            $this->dtgNarroTextContext->Paginator = new QPaginator($this->dtgNarroTextContext);
            $this->dtgNarroTextContext->ItemsPerPage = 20;

            $this->dtgNarroTextContext->PaginatorAlternate = new QPaginator($this->dtgNarroTextContext);

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroTextContext->UseAjax = true;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroTextContext->SetDataBinder('dtgNarroTextContext_Bind');

            if (QApplication::QueryString('st') == 3)
                $this->dtgNarroTextContext->AddColumn($this->colContext);
            $this->dtgNarroTextContext->AddColumn($this->colOriginalText);
            $this->dtgNarroTextContext->AddColumn($this->colTranslatedText);
            $this->dtgNarroTextContext->AddColumn($this->colActions);

            $this->lstTextFilter = new QListBox($this);
            $this->lstTextFilter->AddItem(QApplication::Translate('All texts'), self::SHOW_ALL_TEXTS, true);
            $this->lstTextFilter->AddItem(QApplication::Translate('Untranslated texts'), self::SHOW_UNTRANSLATED_TEXTS);
            $this->lstTextFilter->AddItem(QApplication::Translate('Validated texts'), self::SHOW_VALIDATED_TEXTS);
            $this->lstTextFilter->AddItem(QApplication::Translate('Texts that require validation'), self::SHOW_TEXTS_THAT_REQUIRE_VALIDATION);
            if (QApplication::QueryString('tf') > 0)
                $this->lstTextFilter->SelectedValue = QApplication::QueryString('tf');
            $this->lstTextFilter->AddAction(new QChangeEvent(), new QServerAction('lstTextFilter_Change'));

            $this->txtSearch = new QTextBox($this);
            $this->txtSearch->Text = QApplication::QueryString('s');

            $this->lstSearchType = new QListBox($this);
            $this->lstSearchType->AddItem(QApplication::Translate('original texts'), self::SEARCH_TEXTS, true);
            $this->lstSearchType->AddItem(QApplication::Translate('translations'), self::SEARCH_SUGGESTIONS);
            $this->lstSearchType->AddItem(QApplication::Translate('contexts'), self::SEARCH_CONTEXTS);
            if (QApplication::QueryString('st') > 0)
                $this->lstSearchType->SelectedValue = QApplication::QueryString('st');

            $this->btnSearch = new QButton($this);
            $this->btnSearch->Text = QApplication::Translate('Search');
            $this->btnSearch->AddAction(new QClickEvent(), new QServerAction('btnSearch_Click'));

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->Visible = false;
            //$this->lblMessage->FontBold = true;
            $this->lblMessage->FontItalic = true;
            $this->lblMessage->Padding = 3;
            $this->lblMessage->ForeColor = 'white';
            $this->lblMessage->DisplayStyle = QDisplayStyle::Block;
        }

        protected function SetMessage($strText) {
            $this->lblMessage->BackColor = 'green';
            $this->lblMessage->Text = $strText;
            $this->lblMessage->Visible = true;
        }

        protected function SetErrorMessage($strText) {
            $this->lblMessage->BackColor = 'red';
            $this->lblMessage->Text = $strText;
            $this->lblMessage->Visible = true;
        }

        protected function ClearMessage() {
            $this->lblMessage->Visible = false;
        }

        public function dtgNarroTextContext_OriginalText_Render(NarroTextContext $objNarroTextContext) {
            if (!is_null($objNarroTextContext->Text))
                return $objNarroTextContext->Text->TextValue;
            else
                return null;
        }

        public function dtgNarroTextContext_Context_Render(NarroTextContext $objNarroTextContext) {
            if (!is_null($objNarroTextContext->Context))
                return $objNarroTextContext->Context;
            else
                return '<div width="100%" style="background:gray">&nbsp;</div>';
        }

        public function dtgNarroTextContext_TranslatedText_Render(NarroTextContext $objNarroTextContext) {
            $intUserId = 0;
            /**
            * if there is a valid suggestion, show it
            * if not and a user has made a suggestion, show it in green
            * if not, show the most voted suggestion
            */
            if (!is_null($objNarroTextContext->ValidSuggestion))
                return htmlentities($objNarroTextContext->ValidSuggestion->SuggestionValue, null, 'utf-8');
            elseif (
                $arrSuggestions =
                         NarroTextSuggestion::QueryArray(
                             QQ::AndCondition(
                                 QQ::Equal(QQN::NarroTextSuggestion()->TextId, $objNarroTextContext->TextId),
                                 QQ::Equal(QQN::NarroTextSuggestion()->UserId, $intUserId)
                             )
                         )
                   ) {
                $strSuggestionValue = $arrSuggestions[0]->SuggestionValue;
                return '<div style="color:green">' . htmlentities($strSuggestionValue, null, 'utf-8') . '</div>';
            }
            elseif (
                $arrSuggestions =
                        NarroTextSuggestion::QueryArray(
                            QQ::AndCondition(
                                QQ::Equal(QQN::NarroTextSuggestion()->TextId, $objNarroTextContext->TextId)
                            )
                        )
                   ) {
                $intVoteCnt = 0;
                $strSuggestionValue = $arrSuggestions[0]->SuggestionValue;
                foreach($arrSuggestions as $objSuggestion) {
                    $intSuggVotCnt = NarroSuggestionVote::QueryCount(QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objSuggestion->SuggestionId));
                    if ($intSuggVotCnt > $intVoteCnt) {
                        $intVoteCnt = $intSuggVotCnt;
                        $strSuggestionValue = $objSuggestion->SuggestionValue;
                    }
                }

                return '<div style="color:blue">' . htmlentities($strSuggestionValue, null, 'utf-8') . '</div>';
            }
            else {
                return '<div width="100%" style="background:gray">&nbsp;</div>';
            }
        }

    }


?>
