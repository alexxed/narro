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
     *
     * @property QLabel $Message
     */

    class NarroContextInfoEditor extends QPanel {
        protected $objContextInfo;
        protected $lblText;
        protected $lblIndex;
        protected $txtTranslation;
        protected $lblContextInfo;
        protected $txtAccessKey;
        protected $dtgTranslation;
        protected $lblMessage;
        protected $btnCopy;
        protected $btnHelp;
        protected $btnSave;
        protected $chkChanged;
        protected $btnSaveIgnore;

        public function __construct($objParentObject, $strControlId = null, NarroContextInfo $objContextInfo) {
            parent::__construct($objParentObject, $strControlId);

            $this->objContextInfo = $objContextInfo;

            $this->lblIndex = new QLabel($this);
            $this->lblIndex->CssClass = 'index';
            $this->lblIndex->HtmlEntities = false;

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->CssClass = 'message';
            $this->lblMessage->HtmlEntities = false;

            $this->chkChanged = new QCheckBox($this);
            $this->chkChanged->DisplayStyle = QDisplayStyle::None;
            
            $this->txtTranslation = new QTextBox($this);
            $this->txtTranslation->ActionParameter = $objContextInfo->ContextInfoId;
            $this->txtTranslation->TextMode = QTextMode::MultiLine;
            $this->txtTranslation->CssClass = 'translation_box';
            $this->txtTranslation->Rows = 1;
            $this->txtTranslation->Width = '100%';
            $this->txtTranslation->DisplayStyle = QDisplayStyle::Block;

            if ($this->objContextInfo->Context->TextAccessKey) {
                $this->txtAccessKey = new QTextBox($this);
                $this->txtAccessKey->ToolTip = sprintf('Access key (original access key: %s)', $this->objContextInfo->Context->TextAccessKey);
                $this->txtAccessKey->TextMode = QTextMode::SingleLine;
                $this->txtAccessKey->Columns = 1;
                $this->txtAccessKey->MaxLength = 1;
                $this->txtAccessKey->Text = $this->objContextInfo->SuggestionAccessKey;
            }

            if ($objContextInfo->ValidSuggestionId)
                $this->txtTranslation->Text = $objContextInfo->ValidSuggestion->SuggestionValue;
            $this->txtTranslation->Columns = 50;

            $this->btnCopy = new QImageButton($this);
            $this->btnCopy->AlternateText = t('Copy');
            $this->btnCopy->CssClass = 'imgbutton copy';
            $this->btnCopy->ToolTip = $this->btnCopy->AlternateText;
            $this->btnCopy->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/copy.png';
            $this->btnCopy->TabIndex = -1;
            $this->btnCopy->DisplayStyle = QDisplayStyle::None;

            $this->lblText = new QLabel($this);
            $this->lblText->Width = '100%';
            $this->lblText->TagName = 'pre';
            $this->lblText->CssClass = 'originalText';
            $this->lblText->Text = $this->objContextInfo->Context->Text->TextValue;

            $this->btnHelp = new QImageButton($this);
            $this->btnHelp->AlternateText = t('Help');
            $this->btnHelp->CssClass = 'imgbutton help';
            $this->btnHelp->ToolTip = $this->btnHelp->AlternateText;
            $this->btnHelp->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/help.png';
            $this->btnHelp->TabIndex = -1;
            $this->btnHelp->DisplayStyle = QDisplayStyle::None;
            $this->btnHelp->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnHelp_Click'));

            $this->btnSave = new QImageButton($this);
            $this->btnSave->AlternateText = t('Save');
            $this->btnSave->CssClass = 'imgbutton save';
            $this->btnSave->ToolTip = $this->btnSave->AlternateText;
            $this->btnSave->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/save.png';
            $this->btnSave->TabIndex = -1;
            $this->btnSave->DisplayStyle = QDisplayStyle::None;
            $this->btnSave->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSave_Click'));

            $this->lblContextInfo_Create();

            $this->txtTranslation->AddAction(new QFocusEvent(), new QJavaScriptAction(sprintf('ctx_editor_focus("%s", "%s", "%s", "%s", "%s", "%s")', $this->ControlId, $this->txtTranslation->ControlId, $this->btnCopy->ControlId, $this->btnHelp->ControlId, $this->lblContextInfo->ControlId, $this->chkChanged->ControlId)));

            $this->txtTranslation->AddAction(new QChangeEvent(), new QJavaScriptAction(sprintf('jQuery("#%s").attr("checked", true);', $this->chkChanged->ControlId)));
            $this->txtTranslation->AddAction(new QFocusEvent(), new QAjaxControlAction($this, 'txtTranslation_Focus'));

            $this->btnCopy->AddAction(
                new QClickEvent(),
                new QJavaScriptAction(
                    sprintf(
                        'if (jQuery("#%s").attr("alt") == "%s") {jQuery("#%s").val(jQuery("#%s").text());jQuery("#%s").attr("alt", "%s");} else {jQuery("#%s").val("");jQuery("#%s").attr("alt", "%s");}',
                        $this->btnCopy->ControlId,
                        t('Copy'),
                        $this->txtTranslation->ControlId,
                        $this->lblText->ControlId,
                        $this->btnCopy->ControlId,
                        t('Clear'),
                        $this->txtTranslation->ControlId,
                        $this->btnCopy->ControlId,
                        t('Copy')
                    )
                )
            );

            if ($this->objContextInfo->HasSuggestions && is_null($this->objContextInfo->ValidSuggestionId))
                $this->dtgTranslation_Create();

            $this->strTemplate = dirname(__FILE__) . '/' . __CLASS__ . '.tpl.php';
        }
        
        public function btnSaveIgnore_Create() {
            $this->btnSaveIgnore = new QLinkButton($this);
            $this->btnSaveIgnore->Text = t('Ignore and save');
            $this->btnSaveIgnore->Display = false;
            $this->btnSaveIgnore->TabIndex = -1;
            $this->btnSaveIgnore->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSave_Click'));
        }

        public function lblContextInfo_Create() {
            if (!$this->lblContextInfo) {
                $this->lblContextInfo = new QLabel($this);
                $this->lblContextInfo->CssClass = 'instructions ctxinfo';
                $this->lblContextInfo->TagName = 'div';
                $this->lblContextInfo->DisplayStyle = QDisplayStyle::None;
                if (QApplication::QueryString('p'))
                    $this->lblContextInfo->Text = sprintf('%s<br /><span>%s</span>', $this->objContextInfo->Context->File->FilePath, NarroString::HtmlEntities($this->objContextInfo->Context->Context));
                else
                    $this->lblContextInfo->Text = sprintf('<b>%s</b>%s<br /><span>%s</span>', $this->objContextInfo->Context->Project->ProjectName, $this->objContextInfo->Context->File->FilePath, NarroString::HtmlEntities($this->objContextInfo->Context->Context));
                $this->lblContextInfo->HtmlEntities = false;

                if ($this->objContextInfo->Context->Comment)
                    $this->lblContextInfo->Text .= '<br />' . nl2br(str_replace(array('<!--', '-->'), array('', ''), NarroString::HtmlEntities($this->objContextInfo->Context->Comment)));
            }
        }

        public function dtgTranslation_colAuthor_Render(NarroSuggestion $objSuggestion) {
            $objDateSpan = new QDateTimeSpan(time() - strtotime($objSuggestion->Created));
            $strModifiedWhen = $objDateSpan->SimpleDisplay();

            if (strtotime($objSuggestion->Modified) > 0 && $strModifiedWhen && $objSuggestion->User->Username)
                $strAuthorInfo = sprintf(
                    (($objSuggestion->IsImported)?t('imported by <a href="%s" tabindex="-1">%s</a>, %s ago'):t('<a href="%s" tabindex="-1">%s</a>, %s ago')),
                    NarroLink::UserProfile($objSuggestion->User->UserId),
                    $objSuggestion->User->Username,
                    $strModifiedWhen
                );
            elseif (strtotime($objSuggestion->Modified) > 0 && $strModifiedWhen && !$objSuggestion->User->Username)
                $strAuthorInfo = sprintf(t('%s ago'), $strModifiedWhen);
            elseif ($objSuggestion->User)
                $strAuthorInfo = sprintf(
                    ($objSuggestion->IsImported)?t('imported by <a href="%s" tabindex="-1">%s</a>'):'<a href="%s" tabindex="-1">%s</a>',
                    NarroLink::UserProfile($objSuggestion->User->UserId),
                    $objSuggestion->User->Username
                );
            else
                $strAuthorInfo = t('Unknown');

            if ($objSuggestion->SuggestionId == $this->objContextInfo->ValidSuggestionId && $this->objContextInfo->ValidatorUserId != NarroUser::ANONYMOUS_USER_ID) {
                $objDateSpan = new QDateTimeSpan(time() - strtotime($this->objContextInfo->Modified));
                $strModifiedWhen = $objDateSpan->SimpleDisplay();
                $strAuthorInfo .= ', ' . sprintf(sprintf(t('approved by <a href="%s" tabindex="-1">%s</a>'), NarroLink::UserProfile($this->objContextInfo->ValidatorUser->UserId), $this->objContextInfo->ValidatorUser->Username . ' %s'), (($objDateSpan->SimpleDisplay())?sprintf(t('%s ago'), $objDateSpan->SimpleDisplay()):''));
            }

            return sprintf('<small>-- %s</small>', $strAuthorInfo);
        }

        public function dtgTranslation_colActions_Render(NarroSuggestion $objSuggestion) {

            if (
                $this->objContextInfo->ValidSuggestionId != $objSuggestion->SuggestionId &&
                (
                    QApplication::HasPermissionForThisLang('Can delete any suggestion', $this->objContextInfo->Context->ProjectId) ||
                    ($objSuggestion->UserId == QApplication::GetUserId() && QApplication::GetUserId() != NarroUser::ANONYMOUS_USER_ID )
                )
            ) {
                $strControlId = 'del' . $this->objContextInfo->ContextInfoId . 's' . $objSuggestion->SuggestionId;

                $btnDelete = $this->dtgTranslation->GetChildControl($strControlId);
                if (!$btnDelete) {
                    $btnDelete = new QImageButton($this->dtgTranslation, $strControlId);
                    $btnDelete->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/delete.png';
                    $btnDelete->AlternateText = t('Delete');
                    $btnDelete->ToolTip = $btnDelete->AlternateText;
                    $btnDelete->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\'')));
                    $btnDelete->AddAction(new QClickEvent(), new QConfirmAction(t('Are you sure you want to delete this suggestion?')));
                    if (QApplication::$UseAjax)
                        $btnDelete->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnDelete_Click'));
                    else
                        $btnDelete->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDelete_Click')
                    );

                }

                $btnDelete->ActionParameter = $objSuggestion->SuggestionId;
            }

            $strControlId = 'vot' . $this->objContextInfo->ContextInfoId . 's' . $objSuggestion->SuggestionId;

            if ($objSuggestion->UserId <> QApplication::GetUserId() && QApplication::HasPermissionForThisLang('Can vote', $this->objContextInfo->Context->ProjectId)) {
                $btnVote = $this->dtgTranslation->GetChildControl($strControlId);
                if (!$btnVote) {
                    $btnVote = new QImageButton($this->dtgTranslation, $strControlId);
                    $btnVote->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/vote.png';
                    $btnVote->Display = QApplication::HasPermissionForThisLang('Can vote', $this->objContextInfo->Context->ProjectId);
                    $btnVote->AlternateText = t('Vote');
                    $btnVote->ToolTip = $btnVote->AlternateText;
                    $btnVote->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\'')));
                    if (QApplication::$UseAjax)
                        $btnVote->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnVote_Click'));
                    else
                        $btnVote->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnVote_Click')
                    );

                }

                $btnVote->ActionParameter = $objSuggestion->SuggestionId;
            }

            if ($this->objContextInfo->ValidSuggestionId != $objSuggestion->SuggestionId && QApplication::HasPermissionForThisLang('Can approve', $this->objContextInfo->Context->ProjectId)) {

                $strControlId = 'apr' . $this->objContextInfo->ContextInfoId . 's' . $objSuggestion->SuggestionId;

                $btnApprove = $this->dtgTranslation->GetChildControl($strControlId);
                if (!$btnApprove) {
                    $btnApprove = new QImageButton($this->dtgTranslation, $strControlId);
                    $btnApprove->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/approve.png';
                    $btnApprove->AlternateText = t('Approve');
                    $btnApprove->ToolTip = $btnApprove->AlternateText;
                    $btnApprove->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\'')));
                    if (QApplication::$UseAjax)
                        $btnApprove->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnApprove_Click'));
                    else
                        $btnApprove->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnApprove_Click')
                    );
                }

                $btnApprove->ActionParameter = $objSuggestion->SuggestionId;
            }

            $strText = '';

            if (isset($btnApprove))
                $strText .= '&nbsp;' . $btnApprove->Render(false);

            if (isset($btnVote))
                $strText .= '&nbsp;' . $btnVote->Render(false);

            if (isset($btnDelete))
                $strText .= '&nbsp;' . $btnDelete->Render(false);

            return '<div style="float:right">' . $strText . '</div>';
        }

        private function dtgTranslation_Create() {
            $this->dtgTranslation = new NarroSuggestionDataGrid($this);
            $this->dtgTranslation->ShowFilter = false;
            $this->dtgTranslation->ShowHeader = false;
            $this->dtgTranslation->ShowFooter = false;
            $this->dtgTranslation->AdditionalClauses = array(
                QQ::Expand(QQN::NarroSuggestion()->User)
            );
            $this->dtgTranslation->AdditionalConditions = QQ::AndCondition(
                QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::GetLanguageId()),
                QQ::Equal(QQN::NarroSuggestion()->TextId, $this->objContextInfo->Context->TextId)
            );
            $colSuggestion = $this->dtgTranslation->MetaAddColumn('SuggestionValue');
            $colSuggestion->HtmlEntities = false;
            $colSuggestion->Html = '<?=$_CONTROL->ParentControl->dtgTranslaton_colSuggestion_Render($_ITEM)?>';

            $colActions = new QDataGridColumn('Actions');
            $colActions->HtmlEntities = false;
            $colActions->CssClass = 'actions';
            $colActions->Html = '<?=$_CONTROL->ParentControl->dtgTranslation_colActions_Render($_ITEM)?>';

            $this->dtgTranslation->AddColumn($colActions);
        }

        public function dtgTranslaton_colSuggestion_Render(NarroSuggestion $objSuggestion) {
            return sprintf(
                "<pre%s>%s</pre>%s",
                (($objSuggestion->SuggestionId == $this->objContextInfo->ValidSuggestionId)?" class=\"approved\"":""),
                htmlspecialchars($objSuggestion->SuggestionValue, ENT_NOQUOTES, "UTF-8"),
                $this->dtgTranslation_colAuthor_Render($objSuggestion)
            );
        }

        private function dtgTranslation_Destroy() {
            if ($this->dtgTranslation)
                $this->RemoveChildControl($this->dtgTranslation->ControlId, true);
            $this->dtgTranslation = null;
        }

        public function btnHelp_Click($strFormId, $strControlId, $strParameter) {
            if (!$this->dtgTranslation)
                $this->dtgTranslation_Create();

            if (!$this->lblContextInfo)
                $this->lblContextInfo_Create();

            $this->dtgTranslation->Display = true;
            $this->lblContextInfo->Display = true;
            $this->btnHelp->Display = false;
            
            if ($strParameter != '1')
                $this->txtTranslation->Focus();
        }


        public function Validate() {
            if ($_POST['Qform__FormControl'] == $this->btnSaveIgnore->ControlId) return true;
            
            $blnEmpty = ($this->txtTranslation->Text == '');
            $blnCanSuggest = QApplication::HasPermissionForThisLang('Can suggest', $this->objContextInfo->Context->ProjectId);

            if (!$blnEmpty && $blnCanSuggest) {
                $arrResult = QApplication::$PluginHandler->SaveSuggestion(
                    $this->objContextInfo->Context->Text->TextValue,
                    $this->txtTranslation->Text,
                    $this->objContextInfo->Context->Context,
                    $this->objContextInfo->Context->File,
                    $this->objContextInfo->Context->Project
                );

                if (is_array($arrResult) && isset($arrResult[1]))
                    $strSuggestionValue = $arrResult[1];
                else
                    $strSuggestionValue = $this->txtTranslation->Text;

                if (QApplication::$PluginHandler->Error) {
                    $this->lblMessage->Text = '';
                    foreach(QApplication::$PluginHandler->PluginErrors as $strPluginName=>$arrErors)
                        $this->lblMessage->Text .= '<b class="error">' . $strPluginName . '</b><div class="plugin_message">' . join('<br />', $arrErors) . '</div>';
                    return false;
                }
                else {
                    /**
                     * Make sure that we're not putting in a empty suggestion
                     */
                    if ($strSuggestionValue == '' && $this->txtTranslation->Text != '') {
                        $this->lblMessage->Text = t('A plugin returned an empty value after processing your translation.');
                        return false;
                    }
                    else
                        return true;
                }
            }
            else {
                if (!$blnCanSuggest) {
                    $this->lblMessage->Text = t("You don't have the permission to add translations.");
                    return false;
                }
                else
                    return false;
            }
        }

        public function btnSave_Click($strFormId, $strControlId, $strParameter) {
            if ($this->txtTranslation->Text != '' && ($this->chkChanged->Checked || $this->btnSaveIgnore->ControlId == $strControlId)) {
                if ($strControlId != $this->btnSaveIgnore->ControlId && !$this->Validate()) {
                    $this->lblMessage->Text .= t('Clear the textbox to skip this translation or ');
                    $this->btnSaveIgnore->Display = true;
                    $this->chkChanged->Checked = false;
                    return false;
                }
                
                $this->btnSaveIgnore->Display = false;

                if (!$objSuggestion = NarroSuggestion::LoadByTextIdLanguageIdSuggestionValueMd5($this->objContextInfo->Context->TextId, QApplication::GetLanguageId(), md5($this->txtTranslation->Text))) {
                    $objSuggestion = new NarroSuggestion();
                    $objSuggestion->IsImported = false;
                    $objSuggestion->HasComments = false;
                    $objSuggestion->LanguageId = QApplication::GetLanguageId();
                    $objSuggestion->TextId = $this->objContextInfo->Context->TextId;
                    $objSuggestion->SuggestionValue = $this->txtTranslation->Text;
                    $objSuggestion->UserId = QApplication::GetUserId();
                    $objSuggestion->Save();

                    if ($this->objContextInfo->HasSuggestions != 1) {
                        $this->objContextInfo->HasSuggestions = 1;
                        $this->objContextInfo->Save();
                    }

                    if ($this->dtgTranslation)
                        $this->dtgTranslation->MarkAsModified();
                }

                if ($this->ParentControl->ParentControl->chkApprove->Checked == true)
                    $this->btnApprove_Click($strFormId, $strControlId, $objSuggestion->SuggestionId);
                else {
                    foreach($this->Form->GetAllControls() as $ctl) {
                        if ($ctl instanceof NarroContextInfoEditor) {
                            if ($ctl->Text->Text == $this->lblText->Text) {
                                $ctl->btnHelp_Click($this->Form->FormId, $ctl->btnHelp->ControlId, '1');
                            }
                        }
                    }
                }

                $this->chkChanged->Checked = false;

                $this->lblMessage->Text = '';
            }
            elseif ($this->txtTranslation->Text == '' && $this->objContextInfo->ValidSuggestionId) {
                $this->objContextInfo->ValidSuggestionId = null;
                $this->objContextInfo->ValidatorUserId = null;
                $this->objContextInfo->Save();

                if ($this->dtgTranslation)
                    $this->dtgTranslation->MarkAsModified();
            }

            return true;
        }

        public function txtTranslation_Focus($strFormId, $strControlId, $strParameter) {
            if (
                method_exists($this->ParentControl->ParentControl, 'txtTranslation_Focus')
            )
                $this->ParentControl->ParentControl->txtTranslation_Focus($strFormId, $strControlId, $strParameter);
        }

        public function btnApprove_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can approve', $this->objContextInfo->Context->ProjectId))
                return false;

            if ($strParameter != $this->objContextInfo->ValidSuggestionId) {
                $this->objContextInfo->ValidSuggestionId = (int) $strParameter;
                $this->objContextInfo->ValidatorUserId = QApplication::GetUserId();
                QApplication::$PluginHandler->ApproveSuggestion($this->objContextInfo->Context->Text->TextValue, $this->txtTranslation->Text, $this->objContextInfo->Context->Context, $this->objContextInfo->Context->File, $this->objContextInfo->Context->Project);

                $objSuggestion = NarroSuggestion::Load($strParameter);
                $strSuggestionValue = $objSuggestion->SuggestionValue;

                if ($this->objContextInfo->Context->TextAccessKey) {
                    if (mb_stripos($strSuggestionValue, $this->objContextInfo->Context->TextAccessKey) === false)
                        $this->objContextInfo->SuggestionAccessKey = mb_substr($strSuggestionValue, 0, 1);
                    elseif (mb_strpos($strSuggestionValue, mb_strtoupper($this->objContextInfo->Context->TextAccessKey)) === false)
                        $this->objContextInfo->SuggestionAccessKey = mb_strtolower($this->objContextInfo->Context->TextAccessKey);
                    else
                        $this->objContextInfo->SuggestionAccessKey = mb_strtoupper($this->objContextInfo->Context->TextAccessKey);
                }

                $this->objContextInfo->Modified = QDateTime::Now();
                $this->objContextInfo->Save();

                $this->txtTranslation->Text = $objSuggestion->SuggestionValue;

                if ($this->dtgTranslation)
                    $this->dtgTranslation->MarkAsModified();
            }
        }

        protected function IsSuggestionUsed($objSuggestion) {
            if (
                NarroContextInfo::QueryCount(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroContextInfo()->File->Active, true),
                        QQ::Equal(QQN::NarroContextInfo()->Context->Active, true),
                        QQ::Equal(QQN::NarroContextInfo()->ValidSuggestionId, $objSuggestion->SuggestionId)
                    )
                )
            ) {
                $strLink = NarroLink::Translate(
                    null,
                    null,
                    NarroTranslatePanel::SHOW_APPROVED,
                    "'" . $objSuggestion->Text->TextValue . "'"
                );
                $this->txtTranslation->Warning = sprintf(t('This translation was already approved somewhere.<br />If you still want to delete it, click <a href="%s" target="_blank">here</a> to edit all the texts that use it.'), $strLink);
                return true;
            }
            /**
            elseif ($intVoteCount = NarroSuggestionVote::QueryCount(QQ::AndCondition(QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $strSuggestionId), QQ::NotEqual(QQN::NarroSuggestionVote()->UserId, QApplication::GetUserId())))) {
                $this->lblMessage->ForeColor = 'red';
                $this->lblMessage->Text = sprintf(t('You cannot alter this suggestion because it has %d vote(s).'), $intVoteCount);
                $this->MarkAsModified();
                return true;
            }
            elseif ($intCommentsCount = NarroSuggestionComment::QueryCount(QQ::AndCondition(QQ::Equal(QQN::NarroSuggestionComment()->SuggestionId, $strSuggestionId), QQ::NotEqual(QQN::NarroSuggestionComment()->UserId, QApplication::GetUserId())))) {
                $this->lblMessage->ForeColor = 'red';
                $this->lblMessage->Text = sprintf(t('You cannot alter this suggestion because it has %d comment(s).'), $intVoteCount);
                $this->MarkAsModified();
                return true;
            }
            */

            return false;
        }

        public function btnDelete_Click($strFormId, $strControlId, $strParameter) {
            $objSuggestion = NarroSuggestion::Load($strParameter);
            if (!$this->IsSuggestionUsed($objSuggestion)) {

                QApplication::$PluginHandler->DeleteSuggestion($this->objContextInfo->Context->Text->TextValue, $objSuggestion->SuggestionValue, $this->objContextInfo->Context->Context, $this->objContextInfo->Context->File, $this->objContextInfo->Context->Project);

                if (
                    !QApplication::HasPermissionForThisLang('Can delete any suggestion', $this->objContextInfo->Context->ProjectId) &&
                    (
                        $objSuggestion->UserId != QApplication::GetUserId() ||
                        QApplication::GetUserId() == NarroUser::ANONYMOUS_USER_ID
                    )
                )
                  return false;

                $objSuggestion->Delete();

                if (NarroSuggestion::QueryCount(QQ::Equal(QQN::NarroSuggestion()->TextId, $this->objContextInfo->Context->TextId)) == 0) {
                    $arrCtx = NarroContextInfo::QueryArray(QQ::Equal(QQN::NarroContextInfo()->Context->TextId, $this->objContextInfo->Context->TextId));

                    foreach($arrCtx as $objContextInfo) {
                        $objContextInfo->HasSuggestions = 0;
                        $objContextInfo->Modified = QDateTime::Now();
                        $objContextInfo->Save();
                    }

                    $this->objContextInfo->HasSuggestions = 0;
                }

                foreach($this->Form->GetAllControls() as $ctl) {
                    if ($ctl instanceof NarroContextInfoEditor) {
                        if ($ctl->TranslationList && $ctl->Text->Text == $this->lblText->Text) {
                            $ctl->btnHelp_Click($this->Form->FormId, $ctl->btnHelp->ControlId, '');
                        }
                    }
                }

                $this->lblMessage->Text = t('Suggestion succesfully deleted.');
                $this->blnModified = true;
            }

        }

        public function btnVote_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::HasPermissionForThisLang('Can vote', $this->objContextInfo->Context->ProjectId))
                return false;

            $objSuggestion = NarroSuggestion::Load($strParameter);
            if ($objSuggestion->UserId == QApplication::GetUserId())
                return false;

            QApplication::$PluginHandler->VoteSuggestion($this->objContextInfo->Context->Text->TextValue, $objSuggestion->SuggestionValue, $this->objContextInfo->Context->Context, $this->objContextInfo->Context->File, $this->objContextInfo->Context->Project);

            $arrSuggestion = NarroSuggestionVote::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestionVote()->ContextId, $this->objContextInfo->ContextId),
                    QQ::Equal(QQN::NarroSuggestionVote()->UserId, QApplication::GetUserId())
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
                $objNarroSuggestionVote->ContextId = $this->objContextInfo->ContextId;
                $objNarroSuggestionVote->UserId = QApplication::GetUserId();
                $objNarroSuggestionVote->Created = QDateTime::Now();;
                $objNarroSuggestionVote->VoteValue = 1;
            }

            $objNarroSuggestionVote->Modified = QDateTime::Now();;
            $objNarroSuggestionVote->Save();

            $this->txtTranslation->Warning = t('Thank you for your vote. You can change it anytime by voting another suggestion.');

        }



        public function __get($strName) {
            switch ($strName) {
                case 'CopyButton': return $this->btnCopy;
                case 'HelpButton': return $this->btnHelp;
                case 'SaveButton': return $this->btnSave;
                case 'Text': return $this->lblText;
                case 'AccessKey': return $this->txtAccessKey;
                case 'Translation': return $this->txtTranslation;
                case 'ContextInfo': return $this->lblContextInfo;
                case 'TranslationList': return $this->dtgTranslation;
                case 'Message': return $this->lblMessage;
                case 'Changed': return $this->chkChanged->Checked;
                case 'Index': return $this->lblIndex;
                case 'ChangedCheckbox': return $this->chkChanged;
                case 'SaveIgnoreButton': if (!$this->btnSaveIgnore) $this->btnSaveIgnore_Create(); return $this->btnSaveIgnore;

                default:
                    try {
                        return parent::__get($strName);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {
                case 'Changed':
                    try {
                        $this->chkChanged->Checked = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    return;
                case "Index":
                    try {
                        $this->lblIndex->Text = QType::Cast($mixValue, QType::String);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    return;

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
