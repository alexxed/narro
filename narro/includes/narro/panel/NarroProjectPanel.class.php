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
    class NarroProjectPanel extends QPanel {
        // General Panel Variables
        /**
         * @var NarroProject
         */
        public $objProject;
        public $pnlProjectReport;
        protected $strTemplate;

        public $dtgTranslators;
        public $dtgReviewers;

        public $btnShowTranslators;
        public $btnShowReviewers;

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

            // Setup DataGrid Columns
            $colUsername = new QDataGridColumn(t('Username'), '<?= NarroLink::UserProfile($_ITEM->UserId, $_ITEM->Username) ?>');
            $colUsername->HtmlEntities = false;

            $colWordsTranslated = new QDataGridColumn(t('Words Translated'), '<?= $_CONTROL->ParentControl->colWorldsTranslated_Render($_ITEM) ?>');
            $colWordsTranslated->HtmlEntities = false;

            // Setup DataGrid
            $this->dtgTranslators = new NarroDataGrid($this);
            $this->dtgTranslators->SetCustomStyle('width', '100%');
            $this->dtgTranslators->AlwaysShowPaginator = true;
            $this->dtgTranslators->Display = false;

            // Datagrid Paginator
            $this->dtgTranslators->Paginator = new QPaginator($this->dtgTranslators);
            $this->dtgTranslators->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');
            $this->dtgTranslators->SortColumnIndex = 0;

            $this->dtgTranslators->Title = t('Translators');

            // Specify the local databind method this datagrid will use
            $this->dtgTranslators->SetDataBinder('dtgTranslators_Bind', $this);

            $this->dtgTranslators->AddColumn($colUsername);
            $this->dtgTranslators->AddColumn($colWordsTranslated);

            // Setup DataGrid Columns
            $colUsername = new QDataGridColumn(t('Username'), '<?= NarroLink::UserProfile($_ITEM->ValidatorUserId, $_ITEM->ValidatorUser->Username) ?>');
            $colUsername->HtmlEntities = false;

            $colTextsApproved = new QDataGridColumn(t('Texts Approved'), '<?= $_ITEM->GetVirtualAttribute("TotalTextsApproved") ?>');

            //NarroLink::ProjectTextList($this->objProject->ProjectId, NarroTextListForm::SHOW_APPROVED_TEXTS)

            // Setup DataGrid
            $this->dtgReviewers = new NarroDataGrid($this);
            $this->dtgReviewers->SetCustomStyle('width', '100%');
            $this->dtgReviewers->AlwaysShowPaginator = true;
            $this->dtgReviewers->Display = false;

            // Datagrid Paginator
            $this->dtgReviewers->Paginator = new QPaginator($this->dtgReviewers);
            $this->dtgReviewers->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');
            $this->dtgReviewers->SortColumnIndex = 0;

            $this->dtgReviewers->Title = t('Reviewers');

            // Specify the local databind method this datagrid will use
            $this->dtgReviewers->SetDataBinder('dtgReviewers_Bind', $this);
            $this->dtgReviewers->AddColumn($colUsername);
            $this->dtgReviewers->AddColumn($colTextsApproved);

            $this->pnlProjectReport = new NarroProjectReportPanel($this->objProject, $this);

            $this->btnShowTranslators = new QButton($this);
            $this->btnShowTranslators->Text = t('Show Translators');
            $this->btnShowTranslators->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\'')));
            $this->btnShowTranslators->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnShowTranslators_Click'));

            $this->btnShowReviewers = new QButton($this);
            $this->btnShowReviewers->Text = t('Show Reviewers');
            $this->btnShowReviewers->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf('this.disabled=\'disabled\'')));
            $this->btnShowReviewers->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnShowReviewers_Click'));
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
            return NarroLink::ProjectTextList($this->objProject->ProjectId, NarroTextListForm::SHOW_ALL_TEXTS, NarroTextListForm::SEARCH_AUTHORS, '\'' . $objUser->Username . '\'', $objUser->GetVirtualAttribute("TotalWordsTranslated"));
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
