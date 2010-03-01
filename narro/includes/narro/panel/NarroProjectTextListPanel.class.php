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
    require_once('includes/configuration/prepend.inc.php');

    class NarroProjectTextListPanel extends NarroTextListPanel {
        public $objNarroProject;

        public function __construct(NarroProject $objNarroProject, $objParentObject, $strControlId = null) {
            parent::__construct($objNarroProject, $objParentObject, $strControlId);

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectTextListPanel.tpl.php';

            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->SetMessage(t('Note that, since you\'re searching suggestions, you won\'t see the texts without suggestions.'));
                    break;
                case NarroTextListForm::SEARCH_AUTHORS:
                    $this->SetMessage(t('Note that, since you\'re searching authors of suggestions, you won\'t see the texts without suggestions.'));
                    break;
            }

            $this->dtgNarroContextInfo->Title = t('Texts from the project');

        }

        public function dtgNarroContextInfo_TranslatedText_Render(NarroContextInfo $objNarroContextInfo, $strLink = null) {
            return parent::dtgNarroContextInfo_TranslatedText_Render(
                $objNarroContextInfo,
                NarroLink::ContextSuggest(
                    $this->objNarroProject->ProjectId,
                    0,
                    $objNarroContextInfo->Context->ContextId,
                    $this->lstTextFilter->SelectedValue,
                    $this->lstSearchType->SelectedValue,
                    $this->txtSearch->Text,
                    $this->dtgNarroContextInfo->CurrentRowIndex + 1 + (($this->dtgNarroContextInfo->PageNumber - 1) * $this->dtgNarroContextInfo->ItemsPerPage),
                    $this->dtgNarroContextInfo->TotalItemCount,
                    $this->dtgNarroContextInfo->SortColumnIndex,
                    $this->dtgNarroContextInfo->SortDirection
               )
           );
        }

        public function lstTextFilter_Change() {
            QApplication::Redirect(NarroLink::ProjectTextList($this->objNarroProject->ProjectId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }

        public function btnSearch_Click() {
            QApplication::Redirect(NarroLink::ProjectTextList($this->objNarroProject->ProjectId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }


        public function dtgNarroContextInfo_Bind() {
            switch($this->lstTextFilter->SelectedValue) {
                case NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL:
                    $this->dtgNarroContextInfo->LabelForNoneFound = t('All the texts from this project are already approved.');
                    break;
                case NarroTextListForm::SHOW_UNTRANSLATED_TEXTS:
                    $this->dtgNarroContextInfo->LabelForNoneFound = t('All the texts from this project are already translated.');
                    break;
            }

            $this->arrSuggestionList = array();

            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            if (QApplication::HasPermissionForThisLang('Can mass approve', $this->objNarroProject->ProjectId) && $this->btnMultiApprove->Text == t('Save'))
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1),
                    QQ::LessThan(QQN::NarroContextInfo()->Context->Text->TextCharCount, 100),
                    QQ::IsNull(QQN::NarroContextInfo()->TextAccessKey)
                );
            else
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
                );

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_TEXTS:
                    $this->dtgNarroContextInfo->TotalItemCount = NarroContextInfo::CountByTextValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->dtgNarroContextInfo->TotalItemCount = NarroContextInfo::CountBySuggestionValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_CONTEXTS:
                    $this->dtgNarroContextInfo->TotalItemCount = NarroContextInfo::CountByContext(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_AUTHORS:
                    $this->dtgNarroContextInfo->TotalItemCount = NarroContextInfo::CountByAuthor(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_FILES:
                    $this->dtgNarroContextInfo->TotalItemCount = NarroContextInfo::CountByFileName(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
            }

            // Setup the $objClauses Array
            $objClauses = array();

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgNarroContextInfo->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgNarroContextInfo->LimitClause)
                array_push($objClauses, $objClause);
            else
                array_push($objClauses, QQ::LimitInfo($this->dtgNarroContextInfo->ItemsPerPage));

            // Set the DataSource to be the array of all NarroContextInfo objects, given the clauses above
            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_TEXTS:
                    $this->dtgNarroContextInfo->DataSource = NarroContextInfo::LoadArrayByTextValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroContextInfo->LimitClause,
                        $this->dtgNarroContextInfo->OrderByClause,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->dtgNarroContextInfo->DataSource = NarroContextInfo::LoadArrayBySuggestionValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroContextInfo->LimitClause,
                        $this->dtgNarroContextInfo->OrderByClause,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_CONTEXTS:
                    $this->dtgNarroContextInfo->DataSource = NarroContextInfo::LoadArrayByContext(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroContextInfo->LimitClause,
                        $this->dtgNarroContextInfo->OrderByClause,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_AUTHORS:
                    $this->dtgNarroContextInfo->DataSource = NarroContextInfo::LoadArrayByAuthor(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroContextInfo->LimitClause,
                        $this->dtgNarroContextInfo->OrderByClause,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_FILES:
                    $this->dtgNarroContextInfo->DataSource = NarroContextInfo::LoadArrayByFileName(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroContextInfo->LimitClause,
                        $this->dtgNarroContextInfo->OrderByClause,
                        $objCommonCondition
                    );
                    break;
            }

            if ($this->dtgNarroContextInfo->TotalItemCount) {
                $this->dtgNarroContextInfo->AlwaysShowPaginator = false;
                $this->dtgNarroContextInfo->ShowFooter = true;
                $this->dtgNarroContextInfo->ShowHeader = true;
            }
            else {
                $this->dtgNarroContextInfo->AlwaysShowPaginator = true;
                $this->dtgNarroContextInfo->ShowFooter = false;
                $this->dtgNarroContextInfo->ShowHeader = false;
            }
        }

    }
?>
