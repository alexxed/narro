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

    class NarroTextCommentPanel extends QPanel {
        public $dtgComments;
        public $txtComment;
        public $btnSave;
        protected $intTextId;

        public function __construct(NarroText $objText, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }
            
            $this->blnAutoRenderChildren = true;
            
            $this->intTextId = $objText->TextId;
            
            $this->strText = sprintf('<b>%s</b>', t('Comments'));
            
            $this->dtgComments = new NarroTextCommentDataGrid($this);
            $this->dtgComments->MetaAddColumn(QQN::NarroTextComment()->CommentText);
            $this->dtgComments->MetaAddColumn(QQN::NarroTextComment()->Language->LanguageName);
            $this->dtgComments->MetaAddColumn(QQN::NarroTextComment()->User->Username, 'Html="<?=sprintf(\'<a href="%s" tabindex="-1">%s</a>\', NarroLink::UserProfile($_ITEM->UserId), $_ITEM->User->Username)?>"', 'HtmlEntities=false');
            $this->dtgComments->MetaAddColumn(QQN::NarroTextComment()->Created, 'Html="<?=sprintf(t(\'%s ago \'), new QDateTimeSpan(time() - strtotime($_ITEM->Created)))?>"');
            $this->dtgComments->ShowFilter = false;
            $this->dtgComments->ShowHeader = false;
            $this->dtgComments->SortColumnIndex = 3;
            $this->dtgComments->SortDirection = 1;
            $this->dtgComments->AdditionalConditions = QQ::Equal(QQN::NarroTextComment()->TextId, $objText->TextId);
            
            $this->txtComment = new QTextBox($this);
            $this->txtComment->Name = t('Comment');
            $this->txtComment->TextMode = QTextMode::MultiLine;
            $this->txtComment->PreferedRenderMethod = 'Render';
            $this->txtComment->Columns = 80;
            
            $this->btnSave = new QImageButton($this);
            $this->btnSave->AlternateText = t('Save');
            $this->btnSave->CssClass = 'imgbutton save';
            $this->btnSave->ToolTip = $this->btnSave->AlternateText;
            $this->btnSave->ImageUrl = __NARRO_IMAGE_ASSETS__ . '/comment.png';
            $this->btnSave->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSave_Click'));
        }
        
        public function btnSave_Click() {
            if (trim($this->txtComment->Text)) {
                $objComment = new NarroTextComment();
                $objComment->UserId = QApplication::GetUserId();
                $objComment->LanguageId = QApplication::GetLanguageId();
                $objComment->TextId = $this->intTextId;
                $objComment->Created = QDateTime::Now();
                $objComment->CommentText = $this->txtComment->Text;
                $objComment->CommentTextMd5 = md5($objComment->CommentText);
                $objComment->Save();
                
                $this->dtgComments->Refresh();
            }
        }
    }
?>
