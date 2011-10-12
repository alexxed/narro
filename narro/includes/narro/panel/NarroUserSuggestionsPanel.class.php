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

    class NarroUserSuggestionsPanel extends QPanel {
        protected $dtgSuggestions;
        protected $colText;
        protected $colSuggestion;
        protected $colCreated;
        protected $colLanguage;

        protected $objUser;
        protected $pnlTranslatedPerProjectPie;
        protected $pnlApprovedPie;

        public function __construct($objUser, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->objUser = $objUser;

            $this->colSuggestion = new QDataGridColumn(t('Translated text'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colSuggestion_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->SuggestionValue), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->SuggestionValue, false)));
            $this->colText = new QDataGridColumn(t('Original text'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colText_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->Text->TextValue), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->Text->TextValue, false)));
            $this->colText->HtmlEntities = false;
            $this->colLanguage = new QDataGridColumn(t('Language'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colLanguage_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->LanguageId), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->LanguageId, false)));
            $this->colCreated = new QDataGridColumn(t('Created'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colCreated_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->Created), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroSuggestion()->Created, false)));
            $this->colCreated->HtmlEntities = false;
            $this->colCreated->Wrap = false;

            // Setup DataGrid
            $this->dtgSuggestions = new NarroDataGrid($this);
            $this->dtgSuggestions->SetCustomStyle('padding', '5px');
            $this->dtgSuggestions->Title = sprintf(t('Translations made by <b>%s</b>'), $this->objUser->Username);
            //$this->dtgSuggestions->SetCustomStyle('margin-left', '15px');


            // Datagrid Paginator
            $this->dtgSuggestions->Paginator = new QPaginator($this->dtgSuggestions);
            $this->dtgSuggestions->ItemsPerPage = QApplication::$User->GetPreferenceValueByName('Items per page');

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestions->UseAjax = true;

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestions->SetDataBinder('dtgSuggestions_Bind', $this);

            $this->dtgSuggestions->AddColumn($this->colText);
            $this->dtgSuggestions->AddColumn($this->colSuggestion);
            $this->dtgSuggestions->AddColumn($this->colCreated);
            $this->dtgSuggestions->AddColumn($this->colLanguage);

            $this->dtgSuggestions->SortColumnIndex = 2;
            $this->dtgSuggestions->SortDirection = true;
        }

        public function dtgSuggestions_colSuggestion_Render( NarroSuggestion $objNarroSuggestion ) {
            return $objNarroSuggestion->SuggestionValue;
        }

        public function dtgSuggestions_colText_Render( NarroSuggestion $objNarroSuggestion ) {
            return
                str_replace(
            		'?l=' . QApplication::$TargetLanguage->LanguageCode,
                	'?l=' . $objNarroSuggestion->Language->LanguageCode,
                    NarroLink::Translate(null, null, NarroTranslatePanel::SHOW_ALL, "'" . $objNarroSuggestion->Text->TextValue . "'", null, 1, 10, 0, '', $objNarroSuggestion->Text->TextValue)
                );
        }

        public function dtgSuggestions_colLanguage_Render( NarroSuggestion $objNarroSuggestion ) {
            return t($objNarroSuggestion->Language->LanguageName);
        }

        public function dtgSuggestions_colCreated_Render( NarroSuggestion $objNarroSuggestion ) {
            $objDateSpan = new QDateTimeSpan(time() - $objNarroSuggestion->Created->Timestamp);
            $strModifiedWhen = $objDateSpan->SimpleDisplay();

            return sprintf(t('%s ago'), $strModifiedWhen);
        }

        public function dtgSuggestions_Bind() {
            // Get Total Count b/c of Pagination
            //$this->dtgSuggestions->TotalItemCount = NarroSuggestion::CountByTextId($this->objNarroContext->TextId);

            $objClauses = array();
            if ($objClause = $this->dtgSuggestions->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgSuggestions->LimitClause)
                array_push($objClauses, $objClause);
            else
                array_push($objClauses, QQ::LimitInfo($this->dtgSuggestions->ItemsPerPage));

            $this->dtgSuggestions->TotalItemCount = NarroSuggestion::CountByUserId($this->objUser->UserId);
            $this->dtgSuggestions->DataSource = NarroSuggestion::LoadArrayByUserId($this->objUser->UserId, $objClauses);

            QApplication::ExecuteJavaScript('highlight_datagrid();');
        }

        protected function GetControlHtml() {
            $this->strText = '';
            $this->pnlTranslatedPerProjectPie = new QDatabasePieChart($this);
            $this->pnlTranslatedPerProjectPie->Query = sprintf('
                SELECT
                    narro_project.project_name AS label, COUNT(narro_suggestion.suggestion_id) AS cnt
                FROM
                    narro_suggestion, narro_context, narro_project
                WHERE
                    narro_context.text_id = narro_suggestion.text_id AND
                    narro_project.project_id = narro_context.project_id AND
                    narro_suggestion.user_id=%d
                GROUP BY narro_context.project_id',
                QApplication::GetLanguageId(),
                $this->objUser->UserId
            );
            $intSuggestionCount = NarroSuggestion::CountByUserId($this->objUser->UserId);

            $this->pnlTranslatedPerProjectPie->Total = $intSuggestionCount;
            $this->pnlTranslatedPerProjectPie->MinimumDataValue = 0;

            $this->pnlApprovedPie = new QPieChart($this);
            $this->pnlApprovedPie->Total = $this->pnlTranslatedPerProjectPie->Total;
            $this->pnlApprovedPie->MinimumDataValue = 0;

            $objDatabase = NarroContextInfo::GetDatabase();
            $strQuery = sprintf("
                SELECT
                    DISTINCT narro_context_info.valid_suggestion_id
                FROM
                    narro_context_info, narro_suggestion
                WHERE
                    narro_context_info.valid_suggestion_id=narro_suggestion.suggestion_id AND
                    narro_suggestion.user_id=%d",
                $this->objUser->UserId);

            $objDbResult = $objDatabase->Query($strQuery);

            $intValidSuggestionCount = $objDbResult->CountRows();

            $this->pnlApprovedPie->Data = array(t('Approved')=>$intValidSuggestionCount, t('Not approved') => ($intSuggestionCount - $intValidSuggestionCount));

            if ($intSuggestionCount){
                $this->strText .= '<table align="center"><tr><td>' . $this->pnlTranslatedPerProjectPie->Render(false) . '</td>';
                if ($intValidSuggestionCount)
                    $this->strText .= '<td>' . $this->pnlApprovedPie->Render(false) . '</td>';
                $this->strText .= '</tr></table>';
            }

            $this->strText .= $this->dtgSuggestions->Render(false);

            return parent::GetControlHtml();
        }
    }
?>
