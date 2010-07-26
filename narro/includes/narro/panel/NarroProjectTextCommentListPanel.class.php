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

    class NarroProjectTextCommentListPanel extends QPanel {
        public $dtgNarroTextComment;
        public $txtNarroTextComment;
        public $btnAddTextComment;
        /**
         * @var NarroText
         */
        protected $objNarroText;

        public function __construct($objParentObject, $strControlId = null) {
            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectTextCommentListPanel.tpl.php';
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Setup DataGrid
            $this->dtgNarroTextComment = new QDataRepeater($this);
            $this->dtgNarroTextComment->Width = '100%';
            $this->dtgNarroTextComment->Display = QDisplayStyle::Block;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroTextComment->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroTextComment->SetDataBinder('dtgNarroTextComment_Bind', $this);

            $this->dtgNarroTextComment->Template = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectTextComment.tpl.php';

            $this->txtNarroTextComment = new QTextBox($this);
            $this->txtNarroTextComment->Text = '';
            $this->txtNarroTextComment->CssClass = QApplication::$Language->TextDirection . ' green3dbg';
            $this->txtNarroTextComment->Width = '60%';
            $this->txtNarroTextComment->Height = 85;
            $this->txtNarroTextComment->TextMode = QTextMode::MultiLine;
            $this->txtNarroTextComment->CrossScripting = QCrossScripting::Allow;

            $this->btnAddTextComment = new QButton($this);
            $this->btnAddTextComment->Text = t('Save');
            $this->btnAddTextComment->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnAddTextComment_Click'));

        }

        public function btnAddTextComment_Click($strFormId, $strControlId, $strParameter) {
            if (trim($this->txtNarroTextComment->Text) == '') return false;
            if (!QApplication::HasPermissionForThisLang('Can comment', QApplication::QueryString('p'))) return false;

            $objNarroTextComment = new NarroTextComment();
            $objNarroTextComment->TextId = $this->objNarroText->TextId;
            $objNarroTextComment->UserId = QApplication::GetUserId();
            $objNarroTextComment->LanguageId = QApplication::GetLanguageId();
            $objNarroTextComment->Created = QDateTime::Now();

            $strResult = QApplication::$PluginHandler->SaveTextComment($this->txtNarroTextComment->Text);
            if (!QApplication::$PluginHandler->Error)
                $objNarroTextComment->CommentText = $strResult;
            else
                $objNarroTextComment->CommentText = $this->txtNarroTextComment->Text;

            $objNarroTextComment->CommentTextMd5 = md5($this->txtNarroTextComment->Text);
            try {
                $objNarroTextComment->Save();
            } catch (Exception $objEx) {
                $this->txtNarroTextComment->Text = $objEx->getMessage();
                return false;
            }

            if ($this->objNarroText->HasComments != 1) {
                $this->objNarroText->HasComments = 1;
                $this->objNarroText->Modified = QDateTime::Now();
                $this->objNarroText->Save();
            }

            $arrUsersToNotify = NarroUser::QueryArray(
                QQ::AndCondition(
                    QQ::OrCondition(
                        QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->TextId, $this->objNarroText->TextId),
                        QQ::Equal(QQN::NarroUser()->NarroTextCommentAsUser->TextId, $this->objNarroText->TextId),
                        QQ::Equal(QQN::NarroUser()->NarroSuggestionVoteAsUser->Suggestion->TextId, $this->objNarroText->TextId)
                    ),
                    QQ::NotEqual(QQN::NarroUser()->UserId, QApplication::GetUserId()),
                    QQ::OrCondition(
                        QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->LanguageId, QApplication::GetLanguageId()),
                        QQ::Equal(QQN::NarroUser()->NarroTextCommentAsUser->LanguageId, QApplication::GetLanguageId())
                    ),
                    QQ::NotEqual(QQN::NarroUser()->UserId, Narrouser::ANONYMOUS_USER_ID),
                    QQ::Like(QQN::NarroUser()->Email, '%@%.%')
                ),
                QQ::Distinct()
            );

            foreach($arrUsersToNotify as $objUser) {
                $objMessage = new QEmailMessage();
                $objMessage->From = sprintf('"%s" <%s>', __FROM_EMAIL_NAME__, __FROM_EMAIL_ADDRESS__);
                $objMessage->To = $objUser->Email;
                $objMessage->Subject = sprintf(t('New comment in Narro, on %s'), __HTTP_URL__);

                $objMessage->Body = sprintf(t(
"Hello %s,

%s just added a new comment on a text you translated, voted or commented on:

%s

Here's a link to that text: %s/%s

and the comment:

%s

--
Narro running on %s
"),
                    $objUser->Username,
                    QApplication::$User->Username,
                    $this->objNarroText->TextValue,
                    __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__,
                    NarroLink::ContextSuggest(QApplication::QueryString('p'), QApplication::QueryString('f'), QApplication::QueryString('c'), null, null, null, null, null, null, null, 1),
                    $this->txtNarroTextComment->Text,
                    __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__
                );

                try {
                    if (SERVER_INSTANCE == 'prod')
                        QEmailServer::Send($objMessage);
                }
                catch (Exception $objEx) {
                    //QApplication::$Logger->warn(sprintf('Error while sending out a notification email to %s: %s', $objMessage->To, $objEx->getMessage()));
                    error_log(sprintf('Error while sending out a notification email to %s %s: %s', $objUser->Username, $objUser->Email, $objEx->getMessage()));
                }
            }

            $this->txtNarroTextComment->Text = '';
            $this->dtgNarroTextComment_Bind();
        }

        public function dtgNarroTextComment_Bind() {

            $this->dtgNarroTextComment->DataSource =
                NarroTextComment::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroTextComment()->LanguageId, QApplication::GetLanguageId()),
                        QQ::Equal(QQN::NarroTextComment()->TextId, $this->objNarroText->TextId)
                    ),
                    array(QQ::OrderBy(QQN::NarroTextComment()->Created, 1))
                );
        }

        public function __get($strName) {
            switch ($strName) {
                case "NarroText":
                    return $this->objNarroText;

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

        public function __set($strName, $mixValue) {
            switch ($strName) {
                case "NarroText":
                    if ($mixValue instanceof NarroText)
                        $this->objNarroText = $mixValue;
                    else
                        throw new Exception(t('NarroText should be set with an instance of NarroText'));
                    $this->MarkAsModified();
                    break;

                default:
                    try {
                        return (parent::__set($strName, $mixValue));
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
?>