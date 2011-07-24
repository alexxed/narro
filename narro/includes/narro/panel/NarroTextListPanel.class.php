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

    class NarroTextListPanel extends QPanel {
        public $dtgNarroContextInfo;
        public $objProject;

        // DataGrid Columns
        protected $colContext;
        protected $colOriginalTextLength;
        protected $colOriginalText;
        protected $colTranslatedText;

        public $lstTextFilter;
        public $txtSearch;
        public $lstSearchType;
        public $btnSearch;

        public $btnMultiApprove;
        public $btnMultiApproveCancel;
        public $btnMultiApproveBottom;
        public $btnMultiApproveCancelBottom;

        public $btnMultiTranslate;
        public $btnMultiTranslateCancel;
        public $btnMultiTranslateBottom;
        public $btnMultiTranslateCancelBottom;

        protected $arrSuggestionList;
        protected $arrTexBoxList;

        public $lblMessage;

        const SHOW_ALL_TEXTS = 1;
        const SHOW_UNTRANSLATED_TEXTS = 2;
        const SHOW_APPROVED_TEXTS = 3;
        const SHOW_TEXTS_THAT_REQUIRE_APPROVAL = 4;

        const SEARCH_TEXTS = 1;
        const SEARCH_SUGGESTIONS = 2;
        const SEARCH_CONTEXTS = 3;
        const SEARCH_AUTHORS = 4;
        const SEARCH_FILES = 5;

        public function __construct(NarroProject $objNarroProject, $objParentObject, $strControlId = null) {
            parent::__construct($objParentObject, $strControlId);

            $this->objProject = $objNarroProject;

            // Setup DataGrid Columns
            $this->colContext = new QDataGridColumn(
                t('Context'),
                '<?= $_CONTROL->ParentControl->dtgNarroContextInfo_Context_Render($_ITEM); ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(QQN::NarroContextInfo()->Context->Context),
                    'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroContextInfo()->Context->Context, false)
                )
            );
            $this->colContext->BackColor = 'lightgreen';
            $this->colOriginalText = new QDataGridColumn(
                t('Original text'),
                '<?= $_CONTROL->ParentControl->dtgNarroContextInfo_OriginalText_Render($_ITEM); ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(QQN::NarroContextInfo()->Context->Text->TextValue),
                    'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroContextInfo()->Context->Text->TextValue, false)
                )
            );
            $this->colOriginalText->HtmlEntities = false;
            $this->colOriginalText->Width = '50%';

            $this->colTranslatedText = new QDataGridColumn(
                t('Translated text'),
                '<?= $_CONTROL->ParentControl->dtgNarroContextInfo_TranslatedText_Render($_ITEM); ?>'
            );
            $this->colTranslatedText->HtmlEntities = false;
            $this->colTranslatedText->CssClass = QApplication::$TargetLanguage->TextDirection;
            $this->colTranslatedText->Width = '50%';

            // Setup DataGrid
            $this->dtgNarroContextInfo = new NarroDataGrid($this);
            $this->dtgNarroContextInfo->Title = sprintf(t('Texts from the project "%s"'), $this->objProject->ProjectName);

            // Datagrid Paginator
            $this->dtgNarroContextInfo->Paginator = new QPaginator($this->dtgNarroContextInfo);
            $this->dtgNarroContextInfo->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');

            $this->dtgNarroContextInfo->PaginatorAlternate = new QPaginator($this->dtgNarroContextInfo);

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroContextInfo->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroContextInfo->SetDataBinder('dtgNarroContextInfo_Bind', $this);

            if (QApplication::QueryString('st') == 3)
                $this->dtgNarroContextInfo->AddColumn($this->colContext);
            $this->dtgNarroContextInfo->AddColumn($this->colOriginalText);
            $this->dtgNarroContextInfo->AddColumn($this->colTranslatedText);

            $this->lstTextFilter = new QListBox($this);
            $this->lstTextFilter->AddItem(t('All texts'), self::SHOW_ALL_TEXTS, true);
            $this->lstTextFilter->AddItem(t('Untranslated texts'), self::SHOW_UNTRANSLATED_TEXTS);
            $this->lstTextFilter->AddItem(t('Approved texts'), self::SHOW_APPROVED_TEXTS);
            $this->lstTextFilter->AddItem(t('Texts that require approval'), self::SHOW_TEXTS_THAT_REQUIRE_APPROVAL);
            if (QApplication::QueryString('tf') > 0)
                $this->lstTextFilter->SelectedValue = QApplication::QueryString('tf');
            $this->lstTextFilter->AddAction(new QChangeEvent(), new QServerControlAction($this, 'lstTextFilter_Change'));

            $this->txtSearch = new QTextBox($this);
            $this->txtSearch->Text = QApplication::QueryString('s');

            $this->lstSearchType = new QListBox($this);
            $this->lstSearchType->AddItem(t('original texts'), self::SEARCH_TEXTS, true);
            $this->lstSearchType->AddItem(t('translations'), self::SEARCH_SUGGESTIONS);
            $this->lstSearchType->AddItem(t('contexts'), self::SEARCH_CONTEXTS);
            $this->lstSearchType->AddItem(t('authors'), self::SEARCH_AUTHORS);
            $this->lstSearchType->AddItem(t('files'), self::SEARCH_FILES);
            $this->lstSearchType->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('qc.getControl(\'%s\').className=((this.selectedIndex == 1)?\'%s\':\'ltr\');', $this->txtSearch->ControlId, QApplication::$TargetLanguage->TextDirection)));
            if (QApplication::QueryString('st') > 0)
                $this->lstSearchType->SelectedValue = QApplication::QueryString('st');

            $this->btnSearch = new QButton($this);
            $this->btnSearch->Text = t('Search');
            $this->btnSearch->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSearch_Click'));
            $this->btnSearch->PrimaryButton = true;

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->Visible = false;
            //$this->lblMessage->FontBold = true;
            $this->lblMessage->FontItalic = true;
            $this->lblMessage->Padding = 3;
            $this->lblMessage->ForeColor = 'white';
            $this->lblMessage->DisplayStyle = QDisplayStyle::Block;

            $this->btnMultiApprove = new QButton($this);
            $this->btnMultiApprove->Text = t('Mass approve');
            $this->btnMultiApprove->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId);
            if (QApplication::$UseAjax)
                $this->btnMultiApprove->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnMultiApprove_Click'));
            else
                $this->btnMultiApprove->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApprove_Click'));

            $this->btnMultiApproveCancel = new QButton($this);
            $this->btnMultiApproveCancel->Text = t('Cancel mass approval');
            $this->btnMultiApproveCancel->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId) && $this->btnMultiApprove->Text == t('Save');
            $this->btnMultiApproveCancel->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApproveCancel_Click'));

            $this->btnMultiApproveBottom = new QButton($this);
            $this->btnMultiApproveBottom->Text = t('Mass approve');
            $this->btnMultiApproveBottom->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId);
            if (QApplication::$UseAjax)
                $this->btnMultiApproveBottom->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnMultiApprove_Click'));
            else
                $this->btnMultiApproveBottom->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApprove_Click'));

            $this->btnMultiApproveCancelBottom = new QButton($this);
            $this->btnMultiApproveCancelBottom->Text = t('Cancel mass approval');
            $this->btnMultiApproveCancelBottom->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId) && $this->btnMultiApprove->Text == t('Save');
            $this->btnMultiApproveCancelBottom->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApproveCancel_Click'));

            $this->btnMultiTranslate = new QButton($this);
            $this->btnMultiTranslate->Text = t('Mass translate');
            $this->btnMultiTranslate->Display = QApplication::HasPermissionForThisLang('Can suggest', $this->objProject->ProjectId);
            if (QApplication::$UseAjax)
                $this->btnMultiTranslate->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnMultiTranslate_Click'));
            else
                $this->btnMultiTranslate->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiTranslate_Click'));

            $this->btnMultiTranslateCancel = new QButton($this);
            $this->btnMultiTranslateCancel->Text = t('Cancel mass translation');
            $this->btnMultiTranslateCancel->Display = $this->btnMultiTranslate->Text == t('Save');
            $this->btnMultiTranslateCancel->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApproveCancel_Click'));

            $this->btnMultiTranslateBottom = new QButton($this);
            $this->btnMultiTranslateBottom->Text = t('Mass translate');
            $this->btnMultiTranslateBottom->Display = QApplication::HasPermissionForThisLang('Can suggest', $this->objProject->ProjectId);
            if (QApplication::$UseAjax)
                $this->btnMultiTranslateBottom->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnMultiTranslate_Click'));
            else
                $this->btnMultiTranslateBottom->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiTranslate_Click'));

            $this->btnMultiTranslateCancelBottom = new QButton($this);
            $this->btnMultiTranslateCancelBottom->Text = t('Cancel mass translation');
            $this->btnMultiTranslateCancelBottom->Display = $this->btnMultiTranslateBottom->Text == t('Save');
            $this->btnMultiTranslateCancelBottom->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnMultiApproveCancel_Click'));
        }

        public function btnMultiApproveCancel_Click($strFormId, $strControlId, $strParameter) {
            QApplication::Redirect(QApplication::$ScriptName . '?' . QApplication::$QueryString);
        }

        public function btnMultiTranslate_Click($strFormId, $strControlId, $strParameter) {
            $this->btnMultiApproveCancel->Display = false;
            $this->btnMultiApproveCancelBottom->Display = false;
            $this->btnMultiApprove->Text = t('Mass approve');
            $this->btnMultiApproveBottom->Text = t('Mass approve');

            if ($this->btnMultiTranslate->Text == t('Mass translate')) {
                $this->btnMultiTranslate->Text = t('Save');
                $this->btnMultiApprove->Display = false;
                $this->btnMultiApproveBottom->Display = false;
                $this->btnMultiTranslateBottom->Text = t('Save');
                $this->SetMessage(t('Mass translate mode is the quick and dirty way to translate. If a text has an approved translation, it will be prefilled in the translation textbox. All the translations are added as new and not approved.'));
                $this->dtgNarroContextInfo->MarkAsModified();
            }
            else {
                $blnCanContinue = true;

                /**
                 * Store translations
                 */
                if (is_array($this->arrTexBoxList)) {
                    $intProcessed = 0;
                    $intAdded = 0;

                    // @todo Add plugin validation and a checkbox (Ignore)
                    foreach($this->arrTexBoxList as $intContextInfoId=>$objTranslationText) {
                        $intProcessed++;

                        if (trim($objTranslationText->Text) == '') continue;

                        $objContextInfo = NarroContextInfo::Load($intContextInfoId);
                        $objNewSuggestion = new NarroSuggestion();
                        $objNewSuggestion->TextId = $objTranslationText->ActionParameter;
                        $objNewSuggestion->UserId = QApplication::GetUserId();
                        $objNewSuggestion->LanguageId = QApplication::GetLanguageId();
                        $objNewSuggestion->SuggestionValue = $objTranslationText->Text;
                        try {
                            $objNewSuggestion->Save();
                            $intAdded++;
                        }
                        catch (Exception $objEx) {
                            if (!strstr($objEx->getMessage(), 'Duplicate entry')) {
                                $this->arrTexBoxList[$intContextInfoId]->Warning = $objEx->getMessage();
                                $this->arrTexBoxList[$intContextInfoId]->MarkAsModified();
                                $blnCanContinue = false;
                            }
                        }

                        if (!$objContextInfo->HasSuggestions) {
                            $objContextInfo->HasSuggestions = 1;
                            try {
                                $objContextInfo->Save();
                                $this->dtgNarroContextInfo->RemoveChildControl($this->arrTexBoxList[$intContextInfoId]->ControlId, true);
                            }
                            catch (Exception $objEx) {
                                $this->arrTexBoxList[$intContextInfoId]->Warning .= $objEx->getMessage();
                                $this->arrTexBoxList[$intContextInfoId]->MarkAsModified();
                                $blnCanContinue = false;
                            }
                        }
                    }
                }

                if ($blnCanContinue) {
                    // Reset the stored textboxes
                    $this->arrTexBoxList = array();
                    $this->dtgNarroContextInfo->MarkAsModified();
                }



                if ($intAdded > 0)
                    $this->SetMessage(sprintf(t('Added %d translations.'), $intAdded));
                else
                    $this->SetMessage(t('No translations added.'));

                $this->btnMultiApprove->Display = true;
                $this->btnMultiApproveBottom->Display = true;
            }

            $this->btnMultiTranslateCancel->Display = $this->btnMultiTranslate->Text == t('Save');
            $this->btnMultiTranslateCancelBottom->Display = $this->btnMultiTranslateBottom->Text == t('Save');

        }

        public function btnMultiApprove_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId))
              return false;

            $this->btnMultiTranslateCancel->Display = false;
            $this->btnMultiTranslateCancelBottom->Display = false;
            $this->btnMultiTranslate->Text = t('Mass translate');
            $this->btnMultiTranslateBottom->Text = t('Mass translate');


            if ($this->btnMultiApprove->Text == t('Mass approve')) {
                $this->btnMultiApprove->Text = t('Save');
                $this->btnMultiTranslate->Display = false;
                $this->btnMultiTranslateBottom->Display = false;
                $this->btnMultiApproveBottom->Text = t('Save');
                $this->SetMessage(t('Mass approve mode is the quick way to approve short translations. Leave empty to disapprove.'));
                if (QApplication::QueryString('st') != 3)
                    $this->dtgNarroContextInfo->AddColumnAt(0, $this->colContext);
                $this->dtgNarroContextInfo->MarkAsModified();
            }
            else {
                /**
                 * Approve changes
                 */
                if (is_array($this->arrSuggestionList)) {
                    $intProcessed = 0;
                    $intModified = 0;
                    foreach($this->arrSuggestionList as $intContextInfoId=>$objSuggestionList) {
                        $intProcessed++;
                        $objContextInfo = NarroContextInfo::Load($intContextInfoId);
                        if ($objContextInfo->ValidSuggestionId != $objSuggestionList->SelectedValue) {
                            if ($objSuggestionList->SelectedValue) {
                                $objContextInfo->ValidSuggestionId = $objSuggestionList->SelectedValue;
                                QApplication::$PluginHandler->ApproveSuggestion($objContextInfo->Context->Text->TextValue, $objSuggestionList->SelectedName, $objContextInfo->Context->Context, $objContextInfo->Context->File, $objContextInfo->Context->Project);
                            }
                            else {
                                $objContextInfo->ValidSuggestionId = null;
                                QApplication::$PluginHandler->DisapproveSuggestion($objContextInfo->Context->Text->TextValue, $objSuggestionList->SelectedName, $objContextInfo->Context->Context, $objContextInfo->Context->File, $objContextInfo->Context->Project);
                            }

                            $objContextInfo->ValidatorUserId = QApplication::GetUserId();
                            try {
                                $objContextInfo->Save();
                            }
                            catch (Exception $objEx) {
                                $this->SetMessage(
                                sprintf(t('Saved %d changes.'), $intModified) . ' ' .
                                sprintf(t('An error ocurred: %s'), $objEx->GetMessage()) . ' ' .
                                sprintf(t('Aborting.'))
                                );
                                return false;
                            }
                            $intModified++;
                        }
                    }

                    if ($intModified > 0)
                        $this->SetMessage(sprintf(t('Saved %d changes.'), $intModified));
                    else
                        $this->SetMessage(t('No changes.'));

                    // Reset the stored listboxes
                    $this->arrSuggestionList = array();

                    $this->btnMultiTranslate->Display = true;
                    $this->btnMultiTranslateBottom->Display = true;
                }

                $this->dtgNarroContextInfo->MarkAsModified();
            }

            $this->btnMultiApproveCancel->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId) && $this->btnMultiApprove->Text == t('Save');
            $this->btnMultiApproveCancelBottom->Display = QApplication::HasPermissionForThisLang('Can approve', $this->objProject->ProjectId) && $this->btnMultiApprove->Text == t('Save');

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

        public function dtgNarroContextInfo_OriginalText_Render(NarroContextInfo $objNarroContextInfo, $strLink = null) {
            if (!is_null($objNarroContextInfo->Context->Text)) {
                $strText = QApplication::$PluginHandler->DisplayText($objNarroContextInfo->Context->Text->TextValue);

                if (!$strText)
                    $strText = $objNarroContextInfo->Context->Text->TextValue;

                if ($objNarroContextInfo->TextAccessKey)
                    $strText = preg_replace('/' . $objNarroContextInfo->TextAccessKey . '/', '<u>' . $objNarroContextInfo->TextAccessKey . '</u>', $strText, 1);

                return NarroString::HtmlEntities($strText);
            }
            else
                return null;
        }

        public function dtgNarroContextInfo_Context_Render(NarroContextInfo $objNarroContextInfo) {
            if (!is_null($objNarroContextInfo->Context->Context)) {
                $strContext = QApplication::$PluginHandler->DisplayContext($objNarroContextInfo->Context->Context);
                if (!$strContext)
                    $strContext = $objNarroContextInfo->Context->Context;
                return $strContext;
            }
            else
                return '<div width="100%" style="background:gray">&nbsp;</div>';
        }

        public function dtgNarroContextInfo_ApproveTranslatedText_Render(NarroContextInfo $objNarroContextInfo) {
            $this->arrSuggestionList[$objNarroContextInfo->ContextInfoId] = new QListBox($this->dtgNarroContextInfo);
            $this->arrSuggestionList[$objNarroContextInfo->ContextInfoId]->AddItem('', '');
            foreach(NarroSuggestion::LoadArrayByTextIdForCurrentLanguage($objNarroContextInfo->Context->TextId) as $objSuggestion) {
                $this->arrSuggestionList[$objNarroContextInfo->ContextInfoId]->AddItem($objSuggestion->SuggestionValue, $objSuggestion->SuggestionId, ($objNarroContextInfo->ValidSuggestionId == $objSuggestion->SuggestionId));
            }

            return $this->arrSuggestionList[$objNarroContextInfo->ContextInfoId]->Render(false);
        }

        public function dtgNarroContextInfo_EditTranslatedText_Render(NarroContextInfo $objNarroContextInfo, $strLink = null) {
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId] = new QTextBox($this->dtgNarroContextInfo);
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->ActionParameter = $objNarroContextInfo->Context->TextId;
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->TextMode = QTextMode::MultiLine;
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->Rows = 1;
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->Width = '100%';
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->DisplayStyle = QDisplayStyle::Block;

            if ($objNarroContextInfo->ValidSuggestionId)
                $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->Text = $objNarroContextInfo->ValidSuggestion->SuggestionValue;
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->Columns = 50;
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->AddAction(new QFocusEvent(), new QJavaScriptAction(sprintf('this.rows=4; this.cols=100;highlight_datagrid_row_by_control(this);')));
            $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->AddAction(new QBlurEvent(), new QJavaScriptAction(sprintf('this.rows=1; this.cols=50;reset_datagrid_row_by_control(this);')));

            $btnCopy = new QImageButton($this->dtgNarroContextInfo);
            $btnCopy->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/edit-copy.png';
            $btnCopy->AlternateText = t('Copy the original');
            $btnCopy->ToolTip = t('Copy the original');
            $btnCopy->SetCustomStyle('vertical-align', 'super');
            $btnCopy->Cursor = QCursor::Pointer;
            $btnCopy->TabIndex = -1;
            $btnCopy->ActionParameter = $objNarroContextInfo->Context->TextId . ',' . $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->ControlId;
            $btnCopy->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnCopy_Click'));

            return
                $this->arrTexBoxList[$objNarroContextInfo->ContextInfoId]->RenderWithError(false) .
                $btnCopy->Render(false) .
                sprintf(
                    '<a tabindex="-1" style="vertical-align:super" href="%s" title="Open the translation page"><img tabindex="-1" src="%s/edit-find-replace.png" alt="Details" /></a>',
                    $strLink, __NARRO_IMAGE_ASSETS__
                );
        }

        public function btnCopy_Click($strFormId, $strControlId, $strParameter) {
            list($intTextId, $strTxtCtlId) = explode(',', $strParameter);
            $objText = NarroText::Load($intTextId);
            if ($objText) {
                $txtCtl = $this->dtgNarroContextInfo->GetChildControl($strTxtCtlId);
                if ($txtCtl) {
                    $txtCtl->Text = $objText->TextValue;
                    $txtCtl->Focus();
                }
            }
        }

        public function dtgNarroContextInfo_TranslatedText_Render(NarroContextInfo $objNarroContextInfo, $strLink = null) {
            if ($this->btnMultiApprove->Text != t('Mass approve') && $objNarroContextInfo->HasSuggestions && !$objNarroContextInfo->TextAccessKey && $objNarroContextInfo->Context->Text->TextCharCount < 100) {
                return $this->dtgNarroContextInfo_ApproveTranslatedText_Render($objNarroContextInfo);
            }
            elseif ($this->btnMultiTranslate->Text != t('Mass translate')) {
                return $this->dtgNarroContextInfo_EditTranslatedText_Render($objNarroContextInfo, $strLink);
            }

            /**
            * if there is a valid suggestion, show it
            * if not and a user has made a suggestion, show it in green
            * if not, show the most voted suggestion
            */
            if (!is_null($objNarroContextInfo->ValidSuggestion)) {
                $strSuggestionValue = QApplication::$PluginHandler->DisplaySuggestion($objNarroContextInfo->ValidSuggestion->SuggestionValue);
                if (!$strSuggestionValue)
                    $strSuggestionValue = $objNarroContextInfo->ValidSuggestion->SuggestionValue;

                $strSuggestionValue = NarroString::HtmlEntities($strSuggestionValue);

                if ($objNarroContextInfo->TextAccessKey && $objNarroContextInfo->SuggestionAccessKey && QApplication::HasPermissionForThisLang('Can approve', $objNarroContextInfo->Context->ProjectId))
                    $strSuggestionValue = NarroString::Replace($objNarroContextInfo->SuggestionAccessKey, '<u>' . $objNarroContextInfo->SuggestionAccessKey . '</u>', $strSuggestionValue, 1);

                return sprintf('<a href="%s" title="%s"><div style="width:100%%;color:black">%s</div></a>', $strLink, t('Approved translation. Click for details'), $strSuggestionValue);
            }
            elseif (
                $objSuggestion =
                         NarroSuggestion::QuerySingle(
                             QQ::AndCondition(
                                 QQ::Equal(QQN::NarroSuggestion()->TextId, $objNarroContextInfo->Context->TextId),
                                 QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::GetLanguageId()),
                                 QQ::Equal(QQN::NarroSuggestion()->UserId, QApplication::GetUserId())
                             )
                         )
                   ) {
                $strSuggestionValue = QApplication::$PluginHandler->DisplaySuggestion($objSuggestion->SuggestionValue);
                if (!$strSuggestionValue)
                    $strSuggestionValue = $objSuggestion->SuggestionValue;

                return sprintf('<a href="%s" title="%s"><div style="width:100%%;color:green">%s</div></a>', $strLink, t('Your translation, not approved yet. Click for details'), NarroString::HtmlEntities($strSuggestionValue));
            }
            elseif (
                $arrSuggestions =
                        NarroSuggestion::QueryArray(
                            QQ::AndCondition(
                                QQ::Equal(QQN::NarroSuggestion()->TextId, $objNarroContextInfo->Context->TextId),
                                QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::GetLanguageId())
                            )
                        )
                   ) {
                $intVoteCnt = 0;
                $strSuggestionValue = $arrSuggestions[0]->SuggestionValue;
                foreach($arrSuggestions as $objSuggestion) {
                    $intSuggVotCnt = NarroSuggestionVote::QueryCount(QQ::AndCondition(QQ::Equal(QQN::NarroSuggestionVote()->ContextId, $objNarroContextInfo->ContextId), QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objSuggestion->SuggestionId)));
                    if ($intSuggVotCnt > $intVoteCnt) {
                        $intVoteCnt = $intSuggVotCnt;
                        $strSuggestionValue = $objSuggestion->SuggestionValue;
                    }
                }

                $strSuggestionValue = QApplication::$PluginHandler->DisplaySuggestion($objSuggestion->SuggestionValue);
                if (!$strSuggestionValue)
                    $strSuggestionValue = $objSuggestion->SuggestionValue;

                return sprintf('<a href="%s" title="%s"><div style="width:100%%;color:blue">%s</div></a>', $strLink, t('Translation not approved yet. Click to help'), NarroString::HtmlEntities($strSuggestionValue));
            }
            else {
                return sprintf('<a href="%s" title="%s"><div style="width:100%%;background:gray">&nbsp;</div></a>', $strLink, t('Click to translate this text'));
            }
        }

    }


?>
