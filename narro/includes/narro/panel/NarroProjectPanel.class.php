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
        public $objNarroProject;
        public $flotReport;
        protected $strTemplate;
        
        public $dtgTranslators;
        public $dtgReviewers;
        
        public $pnlTranslatorsPie;
        public $pnlReviewersPie;


        protected function SetupNarroProject(NarroProject $objNarroProject) {
            $this->objNarroProject = $objNarroProject;
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
            
            //NarroLink::ProjectTextList($this->objNarroProject->ProjectId, NarroTextListForm::SHOW_APPROVED_TEXTS)
            
            // Setup DataGrid
            $this->dtgReviewers = new NarroDataGrid($this);
            $this->dtgReviewers->SetCustomStyle('width', '100%');
            $this->dtgReviewers->AlwaysShowPaginator = true;
            
            // Datagrid Paginator
            $this->dtgReviewers->Paginator = new QPaginator($this->dtgReviewers);
            $this->dtgReviewers->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');
            $this->dtgReviewers->SortColumnIndex = 0;

            $this->dtgReviewers->Title = t('Reviewers');
            
            // Specify the local databind method this datagrid will use
            $this->dtgReviewers->SetDataBinder('dtgReviewers_Bind', $this);
            $this->dtgReviewers->AddColumn($colUsername);
            $this->dtgReviewers->AddColumn($colTextsApproved);
            
//            $objDatabase = QApplication::$Database[1];
//            $objDbResult = $objDatabase->Query(
//                sprintf('
//                    SELECT
//                        COUNT(narro_suggestion.user_id) AS cnt, narro_user.username AS label
//                    FROM
//                        narro_context_info, narro_suggestion,narro_context,narro_user
//                    WHERE
//                        valid_suggestion_id=suggestion_id AND
//                        narro_context.context_id=narro_context_info.context_id AND
//                        project_id=%d AND
//                        narro_context_info.language_id=%d AND
//                        narro_context.active=1 AND
//                        narro_user.user_id=narro_suggestion.user_id
//                    GROUP BY narro_suggestion.user_id
//                    ORDER BY cnt DESC',
//                    $this->objNarroProject->ProjectId,
//                    QApplication::GetLanguageId()
//                )
//            );
//            
//            $arrSeries = array();
//            
//            $i = 0;
//            $intYMax = 0;
//            while($arrRow = $objDbResult->FetchArray()) {
//                if ($arrRow['label'] == '') continue;
//                $i++;
//                $arrSeries[substr($arrRow['label'], 0, 15)] = array($i=>$arrRow['cnt']);
//                if ($arrRow['cnt'] > $intYMax)
//                    $intYMax = $arrRow['cnt'];
//                
//            }
//
//            $this->flotReport = new QFlot($this);   
//            $this->flotReport->DisplayVariables = true;
//            $this->flotReport->YMin = 0;
//            $this->flotReport->YMax = $intYMax;
//            $this->flotReport->YTickDecimals = 0;
//            $this->flotReport->XTickDecimals = 0;
//            $this->flotReport->Width = 1780;
//            $this->flotReport->XMax = count($arrSeries) + 1;
//            $this->flotReport->Name = "Completed Training Levels";
//            
//            foreach($arrSeries as $Serie => $Data){
//                $tempSerie = new QFlotSeries($Serie);
//                $tempSerie->Bars = true;
//                $tempSerie->DataSet = $Data;
//                $this->flotReport->AddSeries($tempSerie);
//            }            
            
            $this->pnlTranslatorsPie = new QDatabasePieChart($this);
//            $this->pnlTranslatorsPie->Query = sprintf('
//                SELECT
//                    SUM(narro_suggestion.suggestion_word_count) AS cnt, narro_user.username AS label
//                FROM
//                    narro_context_info, narro_suggestion,narro_context,narro_user
//                WHERE
//                    valid_suggestion_id=suggestion_id AND
//                    narro_context.context_id=narro_context_info.context_id AND
//                    project_id=%d AND
//                    narro_context_info.language_id=%d AND
//                    narro_context.active=1 AND
//                    narro_user.user_id=narro_suggestion.user_id
//                GROUP BY narro_suggestion.user_id
//                ORDER BY cnt DESC',
//                $this->objNarroProject->ProjectId,
//                QApplication::GetLanguageId()
//            );
//            
//            $this->pnlTranslatorsPie->TotalQuery = sprintf('
//                SELECT
//                    SUM(narro_suggestion.suggestion_word_count) AS cnt
//                FROM
//                    narro_context_info, narro_suggestion,narro_context
//                WHERE
//                    valid_suggestion_id=suggestion_id AND
//                    narro_context.context_id=narro_context_info.context_id AND
//                    project_id=%d AND
//                    narro_context_info.language_id=%d AND
//                    narro_context.active=1',
//                $this->objNarroProject->ProjectId,
//                QApplication::GetLanguageId()
//            );
//            
//            $this->pnlTranslatorsPie->MinimumDataValue = 0;
            
            $this->pnlReviewersPie = new QDatabasePieChart($this);
//            $this->pnlReviewersPie->Query = sprintf('
//                SELECT
//                    SUM(narro_context.context_id) AS cnt, narro_user.username AS label
//                FROM
//                    narro_context_info, narro_context, narro_user
//                WHERE
//                    valid_suggestion_id IS NOT NULL AND
//                    narro_context.context_id=narro_context_info.context_id AND
//                    narro_context.project_id=%d AND
//                    narro_context_info.language_id=%d AND
//                    narro_context.active=1 AND
//                    narro_context_info.validator_user_id=narro_user.user_id
//                GROUP BY narro_context_info.validator_user_id
//                ORDER BY cnt DESC',
//                $this->objNarroProject->ProjectId,
//                QApplication::GetLanguageId()
//            );
//            $this->pnlReviewersPie->Total = $this->objNarroProject->CountApprovedTextsByLanguage(QApplication::GetLanguageId());
//            $this->pnlReviewersPie->MinimumDataValue = 0;   

//            // set graph options
//            $this->flotReport = new QFlot($this);
//            $this->flotReport->DisplayVariables = true;
//            $this->flotReport->VariablesTitle = t('Activity');
//            $this->flotReport->XTimeSeries = true;
//            $this->flotReport->YMin = 0;
//            $this->flotReport->YTickDecimals = 0;
//            $this->flotReport->Width = 780;
//        
//            $objDatabase = QApplication::$Database[1];
//            
//            $objApprovedSeries = new QFlotSeries('Approving');
//            $objApprovedSeries->Lines = true;
//            $objApprovedSeries->LinesFill = false;
//            $objApprovedSeries->Points = false;
//            
//            $objDbResult = $objDatabase->Query(
//                sprintf('
//                    SELECT 
//                        DATE(narro_context_info.modified) AS date_modified, COUNT(context_info_id) AS cnt 
//                    FROM 
//                        `narro_context_info`, `narro_context`
//                    WHERE 
//                        narro_context_info.context_id=narro_context.context_id AND
//                        valid_suggestion_id IS NOT NULL AND 
//                        validator_user_id!=%d AND
//                        narro_context_info.modified>0 AND
//                        language_id=%d AND
//                        project_id=%d
//                    GROUP BY DATE(narro_context_info.modified)
//                    ORDER BY narro_context_info.modified',
//                    NarroUser::ANONYMOUS_USER_ID,
//                    QApplication::GetLanguageId(),
//                    $this->objNarroProject->ProjectId
//                )
//            );
//            
//            $arrSeries = array();
//            
//            while($arrRow = $objDbResult->FetchArray()) {
//                $arrSeries[$arrRow['date_modified']] = $arrRow['cnt'];
//            }
//            
//            $objApprovedSeries->DataSet = $arrSeries;
//            $this->flotReport->AddSeries($objApprovedSeries);           
//
//            $objTranslatedSeries = new QFlotSeries('Translating');
//            $objTranslatedSeries->Lines = true;
//            $objTranslatedSeries->LinesFill = false;
//            $objTranslatedSeries->Points = false;
//            
//            $objDbResult = $objDatabase->Query(
//                sprintf('
//                    SELECT 
//                        DATE(narro_context_info.modified) AS date_modified, COUNT(context_info_id) AS cnt 
//                    FROM 
//                        `narro_context_info`, `narro_context`
//                    WHERE 
//                        narro_context_info.context_id=narro_context.context_id AND
//                        narro_context_info.modified>0 AND
//                        language_id=%d AND
//                        project_id=%d
//                    GROUP BY DATE(narro_context_info.modified)
//                    ORDER BY narro_context_info.modified',
//                    QApplication::GetLanguageId(),
//                    $this->objNarroProject->ProjectId
//                )
//            );
//            
//            $arrSeries = array();
//            
//            while($arrRow = $objDbResult->FetchArray()) {
//                $arrSeries[$arrRow['date_modified']] = $arrRow['cnt'];
//            }
//            
//            $objTranslatedSeries->DataSet = $arrSeries;
//            $this->flotReport->AddSeries($objTranslatedSeries);                       
        }
        
        public function dtgTranslators_Bind() {
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
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                $objClauses
            );
            
            $this->dtgTranslators->TotalItemCount = NarroUser::QueryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                array(QQ::Distinct())
            );                
        }
        
        public function dtgReviewers_Bind() {
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
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroContextInfo()->ValidatorUserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                $objClauses
            );
            
            $this->dtgReviewers->TotalItemCount = NarroContextInfo::QueryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::NotEqual(QQN::NarroContextInfo()->ValidatorUserId, NarroUser::ANONYMOUS_USER_ID)
                ),
                array(QQ::GroupBy(QQN::NarroContextInfo()->ValidatorUserId))
            );          
        }

        public function colWorldsTranslated_Render(NarroUser $objUser) {
            return NarroLink::ProjectTextList($this->objNarroProject->ProjectId, NarroTextListForm::SHOW_ALL_TEXTS, NarroTextListForm::SEARCH_AUTHORS, '\'' . $objUser->Username . '\'', $objUser->GetVirtualAttribute("TotalWordsTranslated"));
        }
    }
