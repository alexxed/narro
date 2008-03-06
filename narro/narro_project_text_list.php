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

    require_once('narro_text_list.php');
    class NarroProjectTextListForm extends NarroTextListForm {

        protected $objNarroProject;

        protected function Form_Create() {
            parent::Form_Create();
            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->SetMessage(QApplication::Translate('Note that, since you\'re searching suggestions, you won\'t see the texts without suggestions.'));
                    break;
            }

        }


        protected function SetupNarroObject() {
            // Lookup Object PK information from Query String (if applicable)
            $intProjectId = QApplication::QueryString('p');
            if (($intProjectId)) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                if (!$this->objNarroProject)
                    QApplication::Redirect('narro_project_list.php');

            } else
                QApplication::Redirect('narro_project_list.php');
        }

        public function dtgNarroTextContext_Actions_Render(NarroTextContext $objNarroTextContext) {
            if (QApplication::$objUser->hasPermission('Can suggest', $this->objNarroProject->ProjectId) && QApplication::$objUser->hasPermission('Can vote', $this->objNarroProject->ProjectId) )
                $strText = QApplication::Translate('Suggest/Vote');
            elseif (QApplication::$objUser->hasPermission('Can suggest', $this->objNarroProject->ProjectId))
                $strText = QApplication::Translate('Suggest');
            elseif (QApplication::$objUser->hasPermission('Can vote', $this->objNarroProject->ProjectId))
                $strText = QApplication::Translate('Vote');
            else
                $strText = QApplication::Translate('Details');

            return sprintf('<a href="narro_text_context_suggest.php?p=%d&c=%d&tf=%d&st=%d&s=%s">%s</a>',
                        $this->objNarroProject->ProjectId,
                        $objNarroTextContext->ContextId,
                        $this->lstTextFilter->SelectedValue,
                        $this->lstSearchType->SelectedValue,
                        $this->txtSearch->Text,
                        $strText
                   );
        }

        public function lstTextFilter_Change() {
            QApplication::Redirect('narro_project_text_list.php?' . sprintf('p=%d&tf=%d&st=%d&s=%s', $this->objNarroProject->ProjectId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }

        public function btnSearch_Click() {
            QApplication::Redirect('narro_project_text_list.php?' . sprintf('p=%d&tf=%d&st=%d&s=%s', $this->objNarroProject->ProjectId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }


        protected function dtgNarroTextContext_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            $objCommonCondition = QQ::AndCondition(
                QQ::Equal(QQN::NarroTextContext()->ProjectId, $this->objNarroProject->ProjectId),
                QQ::Equal(QQN::NarroTextContext()->Active, 1)/*,
                QQ::Equal(QQN::NarroTextContext()->Translatable, 1)*/
            );

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_TEXTS:
                    $this->dtgNarroTextContext->TotalItemCount = NarroTextContext::CountByTextValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->dtgNarroTextContext->TotalItemCount = NarroTextContext::CountBySuggestionValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_CONTEXTS:
                    $this->dtgNarroTextContext->TotalItemCount = NarroTextContext::CountByContext(
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
            if ($objClause = $this->dtgNarroTextContext->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgNarroTextContext->LimitClause)
                array_push($objClauses, $objClause);
            else
                array_push($objClauses, QQ::LimitInfo($this->dtgNarroTextContext->ItemsPerPage));

            // Set the DataSource to be the array of all NarroTextContext objects, given the clauses above
            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_TEXTS:
                    $this->dtgNarroTextContext->DataSource = NarroTextContext::LoadArrayByTextValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroTextContext->LimitClause,
                        $this->dtgNarroTextContext->OrderByClause,
                        $objCommonCondition
                    );
                    break;
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->dtgNarroTextContext->DataSource = NarroTextContext::LoadArrayBySuggestionValue(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroTextContext->LimitClause,
                        $this->dtgNarroTextContext->OrderByClause,
                        $objCommonCondition
                    );
                    break;

                case NarroTextListForm::SEARCH_CONTEXTS:
                    $this->dtgNarroTextContext->DataSource = NarroTextContext::LoadArrayByContext(
                        $this->txtSearch->Text,
                        $this->lstTextFilter->SelectedValue,
                        $this->dtgNarroTextContext->LimitClause,
                        $this->dtgNarroTextContext->OrderByClause,
                        $objCommonCondition
                    );
                    break;
            }
        }

    }

    NarroProjectTextListForm::Run('NarroProjectTextListForm', 'templates/narro_project_text_list.tpl.php');
?>
