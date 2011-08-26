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
    class NarroProjectPanel extends QPanel {
        // General Panel Variables
        /**
         * @var NarroProject
         */
        public $objProject;

        public $dtgTranslators;
        public $dtgReviewers;

        protected function SetupNarroProject(NarroProject $objNarroProject) {
            $this->objProject = $objNarroProject;
        }

        public function __construct(NarroProject $objNarroProject, $objParentObject, $strControlId = null) {

            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Call SetupNarroProject to either Load/Edit Existing or Create New
            $this->SetupNarroProject($objNarroProject);

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectPanel.tpl.php';

            // Setup DataGrid
            $this->dtgTranslators = new NarroUserDataGrid($this);
            $this->dtgTranslators->SetCustomStyle('width', '100%');
            $this->dtgTranslators->ShowFilter = false;

            $this->dtgTranslators->SortColumnIndex = 1;
            $this->dtgTranslators->SortDirection = 1;

            $this->dtgTranslators->Title = t('Translators');

            $colUsername = $this->dtgTranslators->MetaAddColumn('Username');
            $colUsername->Name = t('Username');
            $colUsername->HtmlEntities = false;
            $colUsername->Html = '<?= NarroLink::UserProfile($_ITEM->UserId, $_ITEM->Username) ?>';
            $this->dtgTranslators->AdditionalConditions = QQ::AndCondition(
                QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->Text->NarroContextAsText->ProjectId, $this->objProject->ProjectId),
                QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->LanguageId, QApplication::GetLanguageId()),
                QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
            );

            $this->dtgTranslators->AdditionalClauses = array(
                QQ::Sum(QQN::NarroUser()->NarroSuggestionAsUser->SuggestionWordCount, 'translation_word_count'),
                QQ::GroupBy(QQN::NarroUser()->UserId)
            );

            $colWordCount = new QDataGridColumn(t('Words'));
            $colWordCount->Html = '<?=$_ITEM->GetVirtualAttribute("translation_word_count");?>';
            $colWordCount->OrderByClause =  QQ::OrderBy('__translation_word_count', true);
            $colWordCount->ReverseOrderByClause =  QQ::OrderBy('__translation_word_count', false);
            $this->dtgTranslators->AddColumn($colWordCount);

            // Setup DataGrid
            $this->dtgReviewers = new NarroUserDataGrid($this);
            $this->dtgReviewers->SetCustomStyle('width', '100%');
            $this->dtgReviewers->ShowFilter = false;

            $this->dtgReviewers->SortColumnIndex = 1;
            $this->dtgReviewers->SortDirection = 1;

            $this->dtgReviewers->Title = t('Reviewers');

            $colUsername = $this->dtgReviewers->MetaAddColumn('Username');
            $colUsername->Name = t('Username');
            $colUsername->HtmlEntities = false;
            $colUsername->Html = '<?= NarroLink::UserProfile($_ITEM->UserId, $_ITEM->Username) ?>';
            $this->dtgReviewers->AdditionalConditions = QQ::AndCondition(
                QQ::Equal(QQN::NarroUser()->NarroContextInfoAsValidatorUser->Context->ProjectId, $this->objProject->ProjectId),
                QQ::Equal(QQN::NarroUser()->NarroContextInfoAsValidatorUser->LanguageId, QApplication::GetLanguageId()),
                QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
            );

            $this->dtgReviewers->AdditionalClauses = array(
                QQ::Count(QQN::NarroUser()->NarroContextInfoAsValidatorUser->ContextInfoId, 'translations_reviewed'),
                QQ::GroupBy(QQN::NarroUser()->NarroContextInfoAsValidatorUser->ValidatorUserId)
            );

            $colWordCount = new QDataGridColumn(t('Reviews'));
            $colWordCount->Html = '<?=$_ITEM->GetVirtualAttribute("translations_reviewed");?>';
            $colWordCount->OrderByClause =  QQ::OrderBy('__translations_reviewed', true);
            $colWordCount->ReverseOrderByClause =  QQ::OrderBy('__translations_reviewed', false);
            $this->dtgReviewers->AddColumn($colWordCount);

        }

        public function dtgTranslators_Bind() {
            if ($this->dtgTranslators->Display == false) return false;
            // Setup the $objClauses Array
            $objClauses = array(
                QQ::GroupBy(QQN::NarroUser()->UserId),
                QQ::Sum(QQN::NarroUser()->NarroSuggestionAsUser->SuggestionWordCount, 'TotalWordsTranslated')
            );

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgTranslators->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgTranslators->LimitClause)
                array_push($objClauses, $objClause);

            $this->dtgTranslators->DataSource = NarroUser::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, QApplication::GetLanguageId()),
                    QQ::GreaterThan(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->ValidSuggestion->SuggestionWordCount, 0),
                    QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                $objClauses
            );

            $this->dtgTranslators->TotalItemCount = NarroUser::QueryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, QApplication::GetLanguageId()),
                    QQ::GreaterThan(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->ValidSuggestion->SuggestionWordCount, 0),
                    QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                array(QQ::Distinct())
            );
        }

        public function dtgReviewers_Bind() {
            if ($this->dtgReviewers->Display == false) return false;
            // Setup the $objClauses Array
            $objClauses = array(
                QQ::GroupBy(QQN::NarroContextInfo()->ValidatorUserId),
                QQ::Count(QQN::NarroContextInfo()->ContextId, 'TotalTextsApproved')
            );

            $objCommonClauses = $objClauses;

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgReviewers->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgReviewers->LimitClause)
                array_push($objClauses, $objClause);

            $this->dtgReviewers->DataSource = NarroContextInfo::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroContextInfo()->ValidatorUserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                $objClauses
            );

            $this->dtgReviewers->TotalItemCount = NarroContextInfo::QueryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroContextInfo()->ValidatorUserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                array(QQ::GroupBy(QQN::NarroContextInfo()->ValidatorUserId))
            );
        }

        public function colWorldsTranslated_Render(NarroUser $objUser) {
            return NarroLink::ProjectTextList($this->objProject->ProjectId, NarroTranslatePanel::SHOW_ALL, '\'' . $objUser->Username . '\'', $objUser->GetVirtualAttribute("TotalWordsTranslated"));
        }

        public function btnShowTranslators_Click() {
            $this->dtgTranslators->Display = true;
            $this->btnShowTranslators->Display = false;
        }

        public function btnShowReviewers_Click() {
            $this->dtgReviewers->Display = true;
            $this->btnShowReviewers->Display = false;
        }
    }
