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
    class NarroFileTextListForm extends NarroTextListForm {

        protected $objNarroFile;


        protected function SetupNarroObject() {
            // Lookup Object PK information from Query String (if applicable)
            $intFileId = QApplication::QueryString('f');
            if (($intFileId)) {
                $this->objNarroFile = NarroFile::Load(($intFileId));

                if (!$this->objNarroFile)
                    QApplication::Redirect('narro_project_file_list.php?p=' . $this->objNarroFile->Project->ProjectId);

            } else
                QApplication::Redirect('narro_project_file_list.php?p=' . $this->objNarroFile->Project->ProjectId);
        }

        public function dtgNarroTextContext_Actions_Render(NarroTextContext $objNarroTextContext) {
            if (user_access('narro suggest') && user_access('narro vote'))
                $strText = QApplication::Translate('Suggest / Vote');
            elseif (user_access('narro suggest'))
                $strText = QApplication::Translate('Suggest');
            elseif (user_access('narro vote'))
                $strText = QApplication::Translate('Vote');
            else
                $strText = QApplication::Translate('Details');

            return sprintf('<a href="narro_text_context_suggest.php?p=%d&f=%d&c=%d&tf=%d&st=%d&s=%s">%s</a>',
                        $this->objNarroFile->Project->ProjectId,
                        $this->objNarroFile->FileId,
                        $objNarroTextContext->ContextId,
                        $this->lstTextFilter->SelectedValue,
                        $this->lstSearchType->SelectedValue,
                        $this->txtSearch->Text,
                        $strText
                   );
        }

        public function lstTextFilter_Change() {
            QApplication::Redirect('narro_file_text_list.php?' . sprintf('f=%d&tf=%d&st=%d&s=%s', $this->objNarroFile->FileId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }

        public function btnSearch_Click() {
            QApplication::Redirect('narro_file_text_list.php?' . sprintf('f=%d&tf=%d&st=%d&s=%s', $this->objNarroFile->FileId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }


        protected function dtgNarroTextContext_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            $objCommonCondition = QQ::AndCondition(
                QQ::Equal(QQN::NarroTextContext()->FileId, $this->objNarroFile->FileId),
                QQ::Equal(QQN::NarroTextContext()->Active, 1)/*,
                QQ::Equal(QQN::NarroTextContext()->Translatable, 1)*/
            );

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

    NarroFileTextListForm::Run('NarroFileTextListForm', 'templates/narro_file_text_list.tpl.php');
?>
