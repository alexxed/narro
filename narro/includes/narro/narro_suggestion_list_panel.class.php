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
        protected $objNarroTextContext;

        public $lblMessage;

        protected $dtgSuggestions;

        protected $colSuggestion;
        protected $colVote;
        protected $colActions;

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

            // Setup DataGrid Columns
            $this->colSuggestion = new QDataGridColumn(QApplication::Translate('Other suggestions'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colSuggestion_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroTextSuggestion()->SuggestionValue), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroTextSuggestion()->SuggestionValue, false)));
            $this->colSuggestion->HtmlEntities = false;
            $this->colVote = new QDataGridColumn(QApplication::Translate('Votes'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colVote_Render($_ITEM); ?>');
            $this->colVote->HtmlEntities = false;
            //$this->colVote->Width = 30;
            $this->colActions = new QDataGridColumn(QApplication::Translate('Actions'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colActions_Render($_ITEM); ?>');
            $this->colActions->HtmlEntities = false;
            //$this->colActions->Width = 100;

            // Setup DataGrid
            $this->dtgSuggestions = new QDataGrid($this);
            $this->dtgSuggestions->ShowHeader = false;
            $this->dtgSuggestions->CellSpacing = 0;
            $this->dtgSuggestions->CellPadding = 4;
            $this->dtgSuggestions->GridLines = QGridLines::Both;
            $this->dtgSuggestions->BorderWidth = 1;
            $this->dtgSuggestions->Width = '100%';
            $this->dtgSuggestions->BorderStyle = QBorderStyle::Dotted;
            $this->dtgSuggestions->SetCustomStyle('margin', '5px 0px');


            // Datagrid Paginator
            //$this->dtgSuggestions->Paginator = new QPaginator($this->dtgSuggestions);
            //$this->dtgSuggestions->ItemsPerPage = 5;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestions->UseAjax = true;

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestions->SetDataBinder('dtgSuggestions_Bind', $this);

            $this->dtgSuggestions->AddColumn($this->colSuggestion);

            $this->dtgSuggestions->AddColumn($this->colVote);
            if (user_access('narro vote'))
                $this->dtgSuggestions->AddColumn($this->colActions);


        }

        public function GetControlHtml() {
            $intUserId = 0;
            $this->strText = '';

            if (NarroTextSuggestion::CountByTextId($this->objNarroTextContext->TextId))
                $this->strText = QApplication::Translate('Others have suggested:') . '<br />' . $this->dtgSuggestions->Render(false);
            $this->strText .= $this->lblMessage->Render(false);
            return $this->strText;
        }

        public function dtgSuggestions_colProject_Render(NarroTextSuggestion $objNarroTextSuggestion) {
            if ($strProjectName = $this->objNarroTextContext->File->Project->ProjectName)
                return sprintf('<a href="%s">%s</a>',
                    'narro_project_file_list.php?intProjectId=' . $this->objNarroTextContext->File->Project->ProjectId,
                    $strProjectName);
        }


        public function dtgSuggestions_colSuggestion_Render(NarroTextSuggestion $objNarroTextSuggestion) {
            $intUserId = 0;

            $arrWordSuggestions = QApplication::GetSpellSuggestions(QApplication::ConvertToSedila($objNarroTextSuggestion->SuggestionValue));
            $strSuggestionValue = htmlentities($objNarroTextSuggestion->SuggestionValue, null, 'utf-8');

            foreach($arrWordSuggestions as $strWord=>$arrSuggestion) {
                $strSuggestionValue = str_replace($strWord, sprintf(QApplication::Translate('<span style="color:red" title="Misspelled. Suggestions: %s">%s</span>'), addslashes(join(',', $arrSuggestion)), $strWord), $strSuggestionValue);
            }

            if ($objNarroTextSuggestion->SuggestionId == $this->objNarroTextContext->ValidSuggestionId)
                $strCellValue = '<b>' . $strSuggestionValue . '</b>';
            else
                $strCellValue = $strSuggestionValue;

            if ((user_access('administrator') || $objNarroTextSuggestion->UserId == $intUserId) && $this->intEditSuggestionId == $objNarroTextSuggestion->SuggestionId) {
                $strControlId = 'txtEditSuggestion' . $objNarroTextSuggestion->SuggestionId;
                $txtEditSuggestion = $this->objForm->GetControl($strControlId);
                if (!$txtEditSuggestion) {
                    $txtEditSuggestion = new QTextBox($this->dtgSuggestions, $strControlId);
                    $txtEditSuggestion->BackColor = 'lightgreen';
                    $txtEditSuggestion->Width = '100%';
                    $txtEditSuggestion->Height = 85;
                    $txtEditSuggestion->Required = true;
                    $txtEditSuggestion->TextMode = QTextMode::MultiLine;
                    $txtEditSuggestion->CrossScripting = QCrossScripting::Allow;
                    $txtEditSuggestion->Text = $objNarroTextSuggestion->SuggestionValue;
                }
                $strCellValue = $txtEditSuggestion->Render(false);

            }

            return $strCellValue;

        }

        public function dtgSuggestions_colComment_Render(NarroTextSuggestion $objNarroTextSuggestion) {
            $arrComments = NarroSuggestionComment::LoadArrayBySuggestionId($objNarroTextSuggestion->SuggestionId);
            if (count($arrComments)) {
            foreach($arrComments as $objComment) {
                $arrCommentTexts[] = $objComment->CommentText;
            }
            return join('<hr />', $arrCommentTexts);
            }
            else
                return '';
        }

        public function dtgSuggestions_colVote_Render(NarroTextSuggestion $objNarroTextSuggestion) {
            $intVoteCount = NarroSuggestionVote::QueryCount(QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objNarroTextSuggestion->SuggestionId));
            if ($intVoteCount)
                return QApplication::Translate(sprintf('%s votes', $intVoteCount));
            else
                return QApplication::Translate('no votes');
        }

        public function dtgSuggestions_colUser_Render( NarroTextSuggestion $objNarroTextSuggestion ) {
            if (in_array($objNarroTextSuggestion->UserId, array(9)))
                return '';

            $strQuery = sprintf("SELECT * FROM users WHERE uid=%d", $objNarroTextSuggestion->UserId);

            if ($objResult = db_query($strQuery)) {
                if ($arrDbRow = db_fetch_array($objResult)) {
                    return sprintf('<a href="/qdrupal/narro_user_profile.php?user=%d">%s</a>', $objNarroTextSuggestion->UserId, $arrDbRow['name']);
                }
            }

            return $objNarroTextSuggestion->UserId;
        }


        public function dtgSuggestions_colActions_Render(NarroTextSuggestion $objNarroTextSuggestion) {
            $intUserId = 0;

            $strControlId = 'btnEditSuggestion' . $objNarroTextSuggestion->SuggestionId;
            $btnEdit = $this->objForm->GetControl($strControlId);
            if (!$btnEdit) {
                $btnEdit = new QButton($this->dtgSuggestions, $strControlId);
                if (QApplication::$blnUseAjax)
                    $btnEdit->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnEdit_Click'));
                else
                    $btnEdit->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnEdit_Click'));
            }

            if ($objNarroTextSuggestion->SuggestionId != $this->intEditSuggestionId)
                $btnEdit->Text = QApplication::Translate('Edit');

            $btnEdit->ActionParameter = $objNarroTextSuggestion->SuggestionId;

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

            $btnDelete->ActionParameter = $objNarroTextSuggestion->SuggestionId;

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

            $btnVote->ActionParameter = $objNarroTextSuggestion->SuggestionId;

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
            if ($this->objNarroTextContext->ValidSuggestionId == $objNarroTextSuggestion->SuggestionId) {
                $btnValidate->Text = QApplication::Translate('Cancel validation');
            }
            else {
                $btnValidate->Text = QApplication::Translate('Validate');
            }

            $btnValidate->ActionParameter = $objNarroTextSuggestion->SuggestionId;

            $strText = '';
            if (user_access('narro vote'))
                $strText .= '&nbsp;' . $btnVote->Render(false);
            if (user_access('narro administrator') || $objNarroTextSuggestion->UserId == $intUserId)
                $strText .= '&nbsp;' . $btnEdit->Render(false);
            if (user_access('narro delete') || $objNarroTextSuggestion->UserId == $intUserId)
                $strText .= '&nbsp;' . $btnDelete->Render(false);
            if (user_access('narro validate'))
                $strText .= '&nbsp;' . $btnValidate->Render(false);

            return $strText;
        }

        public function dtgSuggestions_Bind() {
            // Get Total Count b/c of Pagination
            //$this->dtgSuggestions->TotalItemCount = NarroTextSuggestion::CountByTextId($this->objNarroTextContext->TextId);

            $objClauses = array();
            if ($objClause = $this->dtgSuggestions->OrderByClause)
                array_push($objClauses, $objClause);
            if ($objClause = $this->dtgSuggestions->LimitClause)
                array_push($objClauses, $objClause);
            $this->dtgSuggestions->DataSource = NarroTextSuggestion::LoadArrayByTextId($this->objNarroTextContext->TextId, $objClauses);
        }

        // Control ServerActions
        public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
            $intUserId = 0;
            if (!$this->IsSuggestionUsed($strParameter)) {

                $objSuggestion = NarroTextSuggestion::Load($strParameter);

                if (!user_access('narro delete') && $objSuggestion->UserId != $intUserId)
                  return false;


                try {
                    $objSuggestion->Delete();
                }
                catch (Exception $objEx) {
                    $this->lblMessage->Text = QApplication::Translate('You can\'t delete the suggestion because it already has votes or comments.');
                    $this->MarkAsModified();
                    return false;
                }
                $this->lblMessage->Text = QApplication::Translate('Suggestion succesfully deleted.');
                $this->MarkAsModified();
            }

        }

        public function btnVote_Click($strFormId, $strControlId, $strParameter) {
            $intUserId = 0;

            if (!user_access('narro vote'))
              return false;

            $arrSuggestion = NarroSuggestionVote::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestionVote()->TextId, $this->objNarroTextContext->TextId),
                    QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId)
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
                $objNarroSuggestionVote->TextId = $this->objNarroTextContext->TextId;
                $objNarroSuggestionVote->UserId = $intUserId;
                $objNarroSuggestionVote->VoteValue = 1;
            }

            $objNarroSuggestionVote->Save();

            $this->lblMessage->Text = QApplication::Translate('Thank you for your vote. You can change it anytime by voting another suggestion.');
            $this->MarkAsModified();

        }

        public function btnEdit_Click($strFormId, $strControlId, $strParameter) {
            $intUserId = 0;

            if (!user_access('narro administrator') && $objNarroTextSuggestion->UserId != $intUserId)
              return false;

            $btnEdit = $this->objForm->GetControl($strControlId);
            if ($btnEdit->Text == QApplication::Translate('Edit')) {
                $btnEdit->Text = QApplication::Translate('Save');
                $this->intEditSuggestionId = $strParameter;
            }
            else {
                // save
                if (!$this->IsSuggestionUsed($strParameter)) {
                    $objSuggestion = NarroTextSuggestion::Load($strParameter);
                    $txtControlId = str_replace('btnEditSuggestion', 'txtEditSuggestion', $strControlId);
                    $txtControl = $this->objForm->GetControl($txtControlId);
                    if ($txtControl) {
                        $objSuggestion->SuggestionValue = QApplication::ConvertToSedila($txtControl->Text);
                        $objSuggestion->SuggestionValueMd5 = md5(QApplication::ConvertToSedila($txtControl->Text));
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
            if ($arrCtx = NarroTextContext::LoadArrayByValidSuggestionId($strSuggestionId)) {
                foreach($arrCtx as $objContext) {
                    if ($objContext->ContextId != $this->objNarroTextContext->ContextId)
                        $arrTexts[sprintf('<a target="_blank" href="'.url('qdrupal/narro_text_context_suggest.php','p=%d&c=%d&f=%d&tf=%d&s=%s').'">%s</a>',
                            QApplication::QueryString('p'),
                            $objContext->ContextId,
                            QApplication::QueryString('f'),
                            QApplication::QueryString('tf'),
                            QApplication::QueryString('s'),
                            $objContext->ContextId
                            )] = 1;
                }
                if (count(array_keys($arrTexts))) {
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
                case "NarroTextContext":
                    try {
                        $this->objNarroTextContext = $mixValue;
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
