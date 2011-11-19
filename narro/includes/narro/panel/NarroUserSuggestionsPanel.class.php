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

            // Setup DataGrid
            $this->dtgSuggestions = new NarroSuggestionDataGrid($this);
            $this->dtgSuggestions->SetCustomStyle('padding', '5px');
            $this->dtgSuggestions->Title = sprintf(t('Translations made by <b>%s</b>'), $this->objUser->RealName);
            //$this->dtgSuggestions->SetCustomStyle('margin-left', '15px');

            $this->colSuggestion = $this->dtgSuggestions->MetaAddColumn(QQN::NarroSuggestion()->SuggestionValue);
            $this->colSuggestion->Name = t('Translated text');
            $this->colSuggestion->Html = '<?= $_CONTROL->ParentControl->dtgSuggestions_colSuggestion_Render($_ITEM); ?>';
            
            $this->colText = $this->dtgSuggestions->MetaAddColumn(QQN::NarroSuggestion()->Text->TextValue);
            $this->colText->Name = t('Original text');
            $this->colText->Html = '<?= $_CONTROL->ParentControl->dtgSuggestions_colText_Render($_ITEM); ?>';
            $this->colText->HtmlEntities = false;
            
            $this->colLanguage = $this->dtgSuggestions->MetaAddColumn(QQN::NarroSuggestion()->Language->LanguageName);
            $this->colLanguage->Name = t('Language');
            $this->colLanguage->Filter = null;
            foreach(NarroLanguage::LoadAllActive() as $objLanguage) {
                $this->colLanguage->FilterAddListItem($objLanguage->LanguageName, QQ::Equal(QQN::NarroSuggestion()->LanguageId, $objLanguage->LanguageId));
            }
            $this->colLanguage->FilterActivate(QApplication::$TargetLanguage->LanguageName);
            $this->colLanguage->Html = '<?= $_CONTROL->ParentControl->dtgSuggestions_colLanguage_Render($_ITEM); ?>';
            
            $this->colCreated = $this->dtgSuggestions->MetaAddColumn(QQN::NarroSuggestion()->Language->LanguageName);
            $this->colCreated->Name = t('Created');
            $this->colCreated->FilterType = QFilterType::None;
            $this->colCreated->Html = '<?= $_CONTROL->ParentControl->dtgSuggestions_colCreated_Render($_ITEM); ?>';
            $this->colCreated->HtmlEntities = false;
            $this->colCreated->Wrap = false;

            // Datagrid Paginator
            $this->dtgSuggestions->Paginator = new QPaginator($this->dtgSuggestions);
            $this->dtgSuggestions->ItemsPerPage = QApplication::$User->GetPreferenceValueByName('Items per page');

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestions->UseAjax = true;

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestions->SetDataBinder('dtgSuggestions_Bind', $this);

            $this->dtgSuggestions->SortColumnIndex = 2;
            $this->dtgSuggestions->SortDirection = true;

            
            $this->dtgSuggestions->AdditionalClauses = array(
                QQ::Expand(QQN::NarroSuggestion()->Text),
                QQ::Expand(QQN::NarroSuggestion()->Language)
            );
            
            $this->dtgSuggestions->AdditionalConditions = QQ::Equal(QQN::NarroSuggestion()->UserId, $this->objUser->UserId);
            
            $this->dtgSuggestions->btnFilter_Click($this->Form->FormId, $this->dtgSuggestions->FilterButton->ControlId, '');
        }

        public function dtgSuggestions_colSuggestion_Render( NarroSuggestion $objNarroSuggestion ) {
            return $objNarroSuggestion->SuggestionValue;
        }

        public function dtgSuggestions_colText_Render( NarroSuggestion $objNarroSuggestion ) {
            return
                str_replace(
            		'?l=' . QApplication::$TargetLanguage->LanguageCode,
                	'?l=' . $objNarroSuggestion->Language->LanguageCode,
                    NarroLink::Translate(0, '', NarroTranslatePanel::SHOW_ALL, "'" . $objNarroSuggestion->Text->TextValue . "'", 0, 0, 10, 0, 0, NarroString::HtmlEntities($objNarroSuggestion->Text->TextValue))
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
            $this->dtgSuggestions->MetaDataBinder();
            $objConditions = $this->dtgSuggestions->Conditions;
            if(null !== $this->dtgSuggestions->AdditionalConditions)
            $objConditions = QQ::AndCondition($this->dtgSuggestions->AdditionalConditions, $objConditions);
            
            // Setup the $objClauses Array
            $objClauses = array();
            
            if(null !== $this->dtgSuggestions->AdditionalClauses)
                $objClauses = $this->dtgSuggestions->AdditionalClauses;
            
            $this->dtgSuggestions->TotalItemCount = NarroSuggestion::QueryCount($objConditions, $objClauses);
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
