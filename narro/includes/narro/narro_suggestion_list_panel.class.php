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

    class NarroSuggestionListPanel extends QPanel {
        // General Panel Variables
        protected $objNarroContextInfo;

        public $lblMessage;

        protected $lblSuggestions;

        protected $dtgSuggestions;

        protected $colSuggestion;
        protected $colAuthor;
        protected $colVote;
        protected $colActions;

        protected $chkShowAllLanguages;

        protected $intEditSuggestionId;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->ForeColor = 'green';
            $this->lblMessage->HtmlEntities = false;
            $this->lblMessage->DisplayStyle = QDisplayStyle::Block;

            $this->lblSuggestions = new QLabel($this);

            $this->chkShowAllLanguages = new QCheckBox($this);
            $this->chkShowAllLanguages->Text = __t('Show suggestions from all languages');
            if (QApplication::$blnUseAjax)
                $this->chkShowAllLanguages->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgSuggestions_Bind'));
            else
                $this->chkShowAllLanguages->AddAction(new QClickEvent(), new QServerControlAction($this, 'dtgSuggestions_Bind'));


            // Setup DataGrid Columns
            $this->colSuggestion = new QDataGridColumn(QApplication::Translate('Other suggestions'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colSuggestion_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->SuggestionValue), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->SuggestionValue, false)));
            $this->colSuggestion->HtmlEntities = false;

            /**
            $this->colAuthor = new QDataGridColumn(QApplication::Translate('Author'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colAuthor_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->UserId), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->UserId, false)));
            $this->colAuthor->HtmlEntities = false;
            */

            $this->colVote = new QDataGridColumn(QApplication::Translate('Votes'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colVote_Render($_ITEM); ?>');
            $this->colVote->HtmlEntities = false;
            //$this->colVote->Width = 30;
            $this->colActions = new QDataGridColumn(QApplication::Translate('Actions'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colActions_Render($_ITEM); ?>');
            $this->colActions->HtmlEntities = false;
            //$this->colActions->Width = 100;

            // Setup DataGrid
            $this->dtgSuggestions = new QDataGrid($this);
            $this->dtgSuggestions->ShowHeader = false;

            // Datagrid Paginator
            $this->dtgSuggestions->Paginator = new QPaginator($this->dtgSuggestions);
            $this->dtgSuggestions->ItemsPerPage = QApplication::$objUser->getPreferenceValueByName('Items per page');

            $this->dtgSuggestions->PaginatorAlternate = new QPaginator($this->dtgSuggestions);



            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestions->UseAjax = true;

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestions->SetDataBinder('dtgSuggestions_Bind', $this);

            $this->dtgSuggestions->AddColumn($this->colSuggestion);
            //$this->dtgSuggestions->AddColumn($this->colAuthor);
            $this->dtgSuggestions->AddColumn($this->colVote);
            $this->dtgSuggestions->AddColumn($this->colActions);
        }

        public function GetControlHtml() {
            if ($this->dtgSuggestions->TotalItemCount) {
                $this->lblSuggestions->Text = QApplication::Translate('Others have suggested:');
                $this->dtgSuggestions->Visible = true;
            }
            else {
                $this->lblSuggestions->Text = QApplication::Translate('No suggestions yet.');
                $this->dtgSuggestions->Visible = false;
            }

            $this->strText =
                $this->lblSuggestions->Render(false) . '<br />' .
                $this->dtgSuggestions->Render(false) . '<br />' .
                '<div style="text-align:right;width:100%">' . $this->chkShowAllLanguages->Render(false) . '</div>';
            $this->strText .= $this->lblMessage->Render(false);
            return $this->strText;
        }

        public function dtgSuggestions_colProject_Render(NarroSuggestion $objNarroSuggestion) {
            if ($strProjectName = $this->objNarroContextInfo->Context->File->Project->ProjectName)
                return sprintf('<a href="%s">%s</a>',
                    'narro_project_file_list.php?intProjectId=' . $this->objNarroContextInfo->Context->File->Project->ProjectId,
                    $strProjectName);
        }


        public function dtgSuggestions_colSuggestion_Render(NarroSuggestion $objNarroSuggestion) {

            $strSuggestionValue = QApplication::$objPluginHandler->DisplaySuggestion($objNarroSuggestion->SuggestionValue);
            if (!$strSuggestionValue)
                $strSuggestionValue = $objNarroSuggestion->SuggestionValue;

            if ($objNarroSuggestion->LanguageId == QApplication::$objUser->Language->LanguageId)
                $arrWordSuggestions = QApplication::GetSpellSuggestions($objNarroSuggestion->SuggestionValue);
            else
                $arrWordSuggestions = array();

            $strSuggestionValue = htmlentities($strSuggestionValue, null, 'utf-8');

            if (is_array($arrWordSuggestions))
            foreach($arrWordSuggestions as $strWord=>$arrSuggestion) {
                $strSuggestionValue = str_replace($strWord, sprintf(QApplication::Translate('<span style="color:red" title="Misspelled. Suggestions: %s">%s</span>'), addslashes(join(',', $arrSuggestion)), $strWord), $strSuggestionValue);
            }

            if ($objNarroSuggestion->SuggestionId == $this->objNarroContextInfo->ValidSuggestionId)
                $strCellValue = '<b>' . $strSuggestionValue . '</b>';
            else
                $strCellValue = $strSuggestionValue;

            if
            (
                (
                    QApplication::$objUser->hasPermission(
                        'Can edit any suggestion',
                        $this->objNarroContextInfo->Context->ProjectId,
                        QApplication::$objUser->Language->LanguageId
                    ) ||
                    ($objNarroSuggestion->UserId == QApplication::$objUser->UserId && QApplication::$objUser->UserId != NarroUser::ANONYMOUS_USER_ID )
                ) &&
                $this->intEditSuggestionId == $objNarroSuggestion->SuggestionId
            ) {
                $strControlId = 'txtEditSuggestion' . $objNarroSuggestion->SuggestionId;
                $txtEditSuggestion = $this->objForm->GetControl($strControlId);
                if (!$txtEditSuggestion) {
                    $txtEditSuggestion = new QTextBox($this->dtgSuggestions, $strControlId);
                    $txtEditSuggestion->BackColor = 'lightgreen';
                    $txtEditSuggestion->Width = '100%';
                    $txtEditSuggestion->Height = 85;
                    $txtEditSuggestion->Required = true;
                    $txtEditSuggestion->TextMode = QTextMode::MultiLine;
                    $txtEditSuggestion->CrossScripting = QCrossScripting::Allow;
                    $txtEditSuggestion->Text = $objNarroSuggestion->SuggestionValue;
                }
                $strCellValue = $txtEditSuggestion->Render(false);

            }
            if ($this->chkShowAllLanguages->Checked)
                return '<div style="color:gray;font-size:70%">' . $objNarroSuggestion->Language->LanguageName . '</div>' . $strCellValue;
            else
                return $strCellValue;

        }

        public function dtgSuggestions_colComment_Render(NarroSuggestion $objNarroSuggestion) {
            $arrComments = NarroSuggestionComment::LoadArrayBySuggestionId($objNarroSuggestion->SuggestionId);
            if (count($arrComments)) {
            foreach($arrComments as $objComment) {
                $arrCommentTexts[] = $objComment->CommentText;
            }
            return join('<hr />', $arrCommentTexts);
            }
            else
                return '';
        }

        public function dtgSuggestions_colVote_Render(NarroSuggestion $objNarroSuggestion) {
            $intVoteCount = NarroSuggestionVote::QueryCount(QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objNarroSuggestion->SuggestionId));
            if ($intVoteCount)
                return QApplication::Translate(sprintf('%s votes', $intVoteCount));
            else
                return QApplication::Translate('no votes');
        }

        public function dtgSuggestions_colAuthor_Render( NarroSuggestion $objNarroSuggestion ) {
            return sprintf('<a href="narro_user_profile.php?u=%d">%s</a>', $objNarroSuggestion->User->UserId, $objNarroSuggestion->User->Username);
        }


        public function dtgSuggestions_colActions_Render(NarroSuggestion $objNarroSuggestion) {

            $strControlId = 'btnEditSuggestion' . $objNarroSuggestion->SuggestionId;
            $btnEdit = $this->objForm->GetControl($strControlId);
            if (!$btnEdit) {
                $btnEdit = new QButton($this->dtgSuggestions, $strControlId);
                if (QApplication::$blnUseAjax)
                    $btnEdit->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnEdit_Click'));
                else
                    $btnEdit->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnEdit_Click'));
            }

            if ($objNarroSuggestion->SuggestionId != $this->intEditSuggestionId)
                $btnEdit->Text = QApplication::Translate('Edit');

            $btnEdit->ActionParameter = $objNarroSuggestion->SuggestionId;

            $strControlId = 'btnDelete' . $this->dtgSuggestions->CurrentRowIndex;

            $btnDelete = $this->objForm->GetControl($strControlId);
            if (!$btnDelete) {
                $btnDelete = new QButton($this->dtgSuggestions, $strControlId);
                $btnDelete->Text = QApplication::Translate('Delete');
                if (QApplication::$blnUseAjax)
                    $btnDelete->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnDelete_Click'));
                else
                    $btnDelete->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDelete_Click')
                );
            }

            $btnDelete->ActionParameter = $objNarroSuggestion->SuggestionId;

            $strControlId = 'btnVote' . $this->dtgSuggestions->CurrentRowIndex;

            $btnVote = $this->objForm->GetControl($strControlId);
            if (!$btnVote) {
                $btnVote = new QButton($this->dtgSuggestions, $strControlId);
                $btnVote->Text = QApplication::Translate('Vote');
                if (QApplication::$blnUseAjax)
                    $btnVote->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnVote_Click'));
                else
                    $btnVote->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnVote_Click')
                );

            }

            $btnVote->ActionParameter = $objNarroSuggestion->SuggestionId;

            $strControlId = 'btnValidate' . $this->dtgSuggestions->CurrentRowIndex;

            $btnValidate = $this->objForm->GetControl($strControlId);
            if (!$btnValidate) {
                $btnValidate = new QButton($this->dtgSuggestions, $strControlId);
                if (QApplication::$blnUseAjax)
                    $btnValidate->AddAction(new QClickEvent(), new QAjaxAction('btnValidate_Click'));
                else
                    $btnValidate->AddAction(new QClickEvent(), new QServerAction('btnValidate_Click')
                );
            }
            if ($this->objNarroContextInfo->ValidSuggestionId == $objNarroSuggestion->SuggestionId) {
                $btnValidate->Text = QApplication::Translate('Cancel validation');
            }
            else {
                $btnValidate->Text = QApplication::Translate('Validate');
            }

            $btnValidate->ActionParameter = $objNarroSuggestion->SuggestionId;

            $strText = '';

            if (QApplication::$objUser->hasPermission('Can vote', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))
                $strText .= '&nbsp;' . $btnVote->Render(false);
            if (QApplication::$objUser->hasPermission('Can edit any suggestion', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) || ($objNarroSuggestion->UserId == QApplication::$objUser->UserId && QApplication::$objUser->UserId != NarroUser::ANONYMOUS_USER_ID ))
                $strText .= '&nbsp;' . $btnEdit->Render(false);
            if (QApplication::$objUser->hasPermission('Can delete any suggestion', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) || ($objNarroSuggestion->UserId == QApplication::$objUser->UserId && QApplication::$objUser->UserId != NarroUser::ANONYMOUS_USER_ID ))
                $strText .= '&nbsp;' . $btnDelete->Render(false);
            if (QApplication::$objUser->hasPermission('Can validate', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))
                $strText .= '&nbsp;' . $btnValidate->Render(false);

            return $strText;
        }

        public function dtgSuggestions_Bind() {
            // Get Total Count b/c of Pagination
            if ($this->chkShowAllLanguages->Checked)
                $this->dtgSuggestions->TotalItemCount = NarroSuggestion::CountByTextId($this->objNarroContextInfo->Context->TextId);
            else
                $this->dtgSuggestions->TotalItemCount = NarroSuggestion::QueryCount(
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroSuggestion()->TextId, $this->objNarroContextInfo->Context->TextId),
                            QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::$objUser->Language->LanguageId)
                        )
                );


            $objClauses = QQ::Clause(QQ::OrderBy(QQN::NarroSuggestion()->LanguageId));
            if ($objClause = $this->dtgSuggestions->OrderByClause)
                array_push($objClauses, $objClause);
            if ($objClause = $this->dtgSuggestions->LimitClause)
                array_push($objClauses, $objClause);

            if ($this->chkShowAllLanguages->Checked)
                $this->dtgSuggestions->DataSource = NarroSuggestion::LoadArrayByTextId($this->objNarroContextInfo->Context->TextId, $objClauses);
            else
                $this->dtgSuggestions->DataSource =
                    NarroSuggestion::QueryArray(
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroSuggestion()->TextId, $this->objNarroContextInfo->Context->TextId),
                            QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::$objUser->Language->LanguageId)
                        ),
                        $objClauses
                    );
            $this->blnModified = true;
        }

        // Control ServerActions
        public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
            if (!$this->IsSuggestionUsed($strParameter)) {

                $objSuggestion = NarroSuggestion::Load($strParameter);

                if (!QApplication::$objUser->hasPermission('Can delete any suggestion', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) && ($objSuggestion->UserId != QApplication::$objUser->UserId || QApplication::$objUser->UserId == NarroUser::ANONYMOUS_USER_ID ))
                  return false;


                try {
                    $objSuggestion->Delete();
                }
                catch (Exception $objEx) {
                    $this->lblMessage->Text = QApplication::Translate('You cannot delete the suggestion because it is validate or it has votes or comments.');
                    $this->MarkAsModified();
                    return false;
                }
                $this->lblMessage->Text = QApplication::Translate('Suggestion succesfully deleted.');
                $this->MarkAsModified();
            }

        }

        public function btnVote_Click($strFormId, $strControlId, $strParameter) {

            if (!QApplication::$objUser->hasPermission('Can vote', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))
              return false;

            $arrSuggestion = NarroSuggestionVote::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestionVote()->TextId, $this->objNarroContextInfo->Context->TextId),
                    QQ::Equal(QQN::NarroSuggestionVote()->UserId, QApplication::$objUser->UserId)
                )
            );

            if (count($arrSuggestion)) {
                $objNarroSuggestionVote = $arrSuggestion[0];
                if ($objNarroSuggestionVote->SuggestionId == $strParameter)
                    return true;
                    $objNarroSuggestionVote->SuggestionId = $strParameter;
            }
            else {

                $objNarroSuggestionVote = new NarroSuggestionVote();
                $objNarroSuggestionVote->SuggestionId = $strParameter;
                $objNarroSuggestionVote->TextId = $this->objNarroContextInfo->Context->TextId;
                $objNarroSuggestionVote->UserId = QApplication::$objUser->UserId;
                $objNarroSuggestionVote->VoteValue = 1;
            }

            $objNarroSuggestionVote->Save();

            $this->lblMessage->Text = QApplication::Translate('Thank you for your vote. You can change it anytime by voting another suggestion.');
            $this->MarkAsModified();

        }

        public function btnEdit_Click($strFormId, $strControlId, $strParameter) {

            if (!QApplication::$objUser->hasPermission('Can edit any suggestion', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) && ($objNarroSuggestion->UserId != QApplication::$objUser->UserId || QApplication::$objUser->UserId == NarroUser::ANONYMOUS_USER_ID ))
              return false;

            $btnEdit = $this->objForm->GetControl($strControlId);
            if ($btnEdit->Text == QApplication::Translate('Edit')) {
                $btnEdit->Text = QApplication::Translate('Save');
                $this->intEditSuggestionId = $strParameter;
            }
            else {
                // save
                if (!$this->IsSuggestionUsed($strParameter)) {
                    $objSuggestion = NarroSuggestion::Load($strParameter);
                    $txtControlId = str_replace('btnEditSuggestion', 'txtEditSuggestion', $strControlId);
                    $txtControl = $this->objForm->GetControl($txtControlId);
                    if ($txtControl) {
                        $strSuggestionValue = QApplication::$objPluginHandler->ProcessSuggestion($txtControl->Text);
                        if (!$strSuggestionValue)
                            $strSuggestionValue = $txtControl->Text;

                        $objSuggestion->SuggestionValue = $strSuggestionValue;
                        $objSuggestion->SuggestionValueMd5 = md5($strSuggestionValue);
                        try {
                            $objSuggestion->Save();
                            $this->lblMessage->Text = QApplication::Translate('Your changes were saved succesfully.');
                            $btnEdit->Text = QApplication::Translate('Edit');
                            $this->intEditSuggestionId = null;
                        } catch (QMySqliDatabaseException $objExc) {
                            $this->lblMessage->Text = QApplication::Translate('The text you are trying to save already exists.');
                        }
                    }
                }
            }
            //$this->dtgSuggestions_Bind();
            $this->MarkAsModified();
        }

        protected function IsSuggestionUsed($strSuggestionId) {
            if ($arrCtx = NarroContextInfo::LoadArrayByValidSuggestionId($strSuggestionId)) {
                foreach($arrCtx as $objContext) {
                    if ($objContext->ContextId != $this->objNarroContextInfo->ContextId)
                        $arrTexts[sprintf('<a target="_blank" href="narro_context_suggest.php?p=%d&c=%d&f=%d&tf=%d&s=%s">%s</a>',
                            QApplication::QueryString('p'),
                            $objContext->ContextId,
                            QApplication::QueryString('f'),
                            QApplication::QueryString('tf'),
                            QApplication::QueryString('s'),
                            $objContext->ContextId
                            )] = 1;
                }
                if (isset($arrTexts) && count(array_keys($arrTexts))) {
                    $this->lblMessage->Text = sprintf(QApplication::Translate('The suggestion is marked as valid for the following contexts: %s'), join(', ', array_keys($arrTexts)));
                    $this->MarkAsModified();
                    return true;
                }
            }
            return false;
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {
                // APPEARANCE
                case "NarroContextInfo":
                    try {
                        $this->objNarroContextInfo = $mixValue;
                        $this->lblMessage->Text = '';
                        $this->dtgSuggestions_Bind();
                        $this->MarkAsModified();
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }

    }
?>
