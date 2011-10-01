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
        
        public $pnlProgressBar;

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
            $this->dtgTranslators = new NarroDataGrid($this);
            $this->dtgTranslators->SetCustomStyle('width', 'auto');
            $this->dtgTranslators->ShowFilter = false;

            $this->dtgTranslators->SortColumnIndex = 1;
            $this->dtgTranslators->SortDirection = 1;

            $this->dtgTranslators->Title = t('Translators');
            $this->dtgTranslators->SetDataBinder('dtgTranslators_Bind', $this);
            $this->dtgTranslators->DisplayStyle = QDisplayStyle::InlineBlock;
            $this->dtgTranslators->SetCustomStyle('margin', '10px');

            $colUsername = new QDataGridColumn(t('Translator'));
            $colUsername->HtmlEntities = false;
            $colUsername->Html = '<?= NarroLink::UserProfile($_ITEM["user"]->UserId, $_ITEM["user"]->Username) ?>';
            $this->dtgTranslators->AddColumn($colUsername);

            $colWordCount = new QDataGridColumn(t('Words'));
            $colWordCount->Html = '<?=$_CONTROL->ParentControl->colWorldsTranslated_Render($_ITEM);?>';
            $colWordCount->HtmlEntities = false;
            $this->dtgTranslators->AddColumn($colWordCount);
            
            $colLastTranslation = new QDataGridColumn(t('Last translation'));
            $colLastTranslation->Html = '<?=$_ITEM["last_translation"];?>';
            $this->dtgTranslators->AddColumn($colLastTranslation);            

            // Setup DataGrid
            $this->dtgReviewers = new NarroDataGrid($this);
            $this->dtgReviewers->SetCustomStyle('width', 'auto');
            $this->dtgReviewers->ShowFilter = false;
            $this->dtgReviewers->DisplayStyle = QDisplayStyle::InlineBlock;
            $this->dtgReviewers->SetCustomStyle('vertical-align', 'top');
            $this->dtgReviewers->SetCustomStyle('margin', '10px');

            $this->dtgReviewers->SortColumnIndex = 1;
            $this->dtgReviewers->SortDirection = 1;

            $this->dtgReviewers->Title = t('Reviewers');
            $this->dtgReviewers->SetDataBinder('dtgReviewers_Bind', $this);

            $colUsername = new QDataGridColumn(t('Reviewer'));
            $colUsername->HtmlEntities = false;
            $colUsername->Html = '<?= NarroLink::UserProfile($_ITEM["user"]->UserId, $_ITEM["user"]->Username) ?>';
            $this->dtgReviewers->AddColumn($colUsername);

            $colReviews = new QDataGridColumn(t('Reviews'));
            $colReviews->Html = '<?=$_ITEM["reviews"];?>';
            $this->dtgReviewers->AddColumn($colReviews);
            
            $this->pnlProgressBar = new NarroTranslationProgressBar($this);
            $this->pnlProgressBar->Name = t('Translation progress');
            $this->pnlProgressBar->Instructions = t('Hover over the bar to get some details, click on it to refresh it');
            $this->pnlProgressBar->ActionParameter = $this->objProject->ProjectId;
            $this->pnlProgressBar->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnRefresh_Click', $objWaitIcon));
            $this->pnlProgressBar->Total = $this->objProject->ProjectProgressForCurrentLanguage->TotalTextCount;
            $this->pnlProgressBar->Translated = $this->objProject->ProjectProgressForCurrentLanguage->ApprovedTextCount;
            $this->pnlProgressBar->Fuzzy = $this->objProject->ProjectProgressForCurrentLanguage->FuzzyTextCount;
        }

        public function dtgTranslators_Bind() {
            $objDbResult = NarroSuggestion::GetDatabase()->Query(
                sprintf(
            		'SELECT 
            			narro_suggestion.user_id, 
            			SUM(narro_text.text_word_count) AS words_translated, 
            			MAX(narro_suggestion.created) AS last_translation 
            		FROM 
            			narro_text, narro_suggestion, narro_context 
            		WHERE 
            			narro_context.text_id=narro_text.text_id AND 
            			narro_suggestion.text_id=narro_text.text_id AND 
            			narro_suggestion.is_imported=0 AND 
            			narro_suggestion.language_id AND
            			narro_context.project_id=%d 
            		GROUP BY narro_suggestion.user_id',
            		QApplication::GetLanguageId(),
                    $this->objProject->ProjectId
                )
            );
            
            if ($objDbResult)
                while($arrRow = $objDbResult->FetchArray()) {
                    if ($arrRow['user_id'] != NarroUser::ANONYMOUS_USER_ID && $arrRow['words_translated'] > 0) {
                        $objUser = NarroUser::Load($arrRow['user_id']);
                        $arrWordsTranslated[] = $arrRow['words_translated'];
                        $objDateSpan = new QDateTimeSpan(time() - strtotime($arrRow['last_translation']));
                        $strLastTranslation = $objDateSpan->SimpleDisplay();
                        $arrData[] = array('user' => $objUser, 'words_translated' => $arrRow['words_translated'], 'last_translation' => sprintf(t('%s ago'), $strLastTranslation));
                    }
                }
            
            array_multisort($arrWordsTranslated, SORT_DESC, SORT_NUMERIC, $arrData);
            
            $this->dtgTranslators->DataSource = $arrData;
        }

        

        
        public function dtgReviewers_Bind() {
            $objDbResult = NarroSuggestion::GetDatabase()->Query(
                sprintf(
            		'SELECT
                    	narro_context_info.validator_user_id,
                    	COUNT(narro_context_info.context_info_id) AS reviews,
                    	MAX(narro_context_info.modified) AS last_review
                    FROM
                    	narro_context_info, narro_context
                    WHERE
                    	narro_context_info.context_id=narro_context.context_id AND
                    	narro_context_info.validator_user_id IS NOT NULL AND
                    	narro_context_info.language_id=%d AND
                    	narro_context.project_id=%d
                    GROUP BY narro_context_info.validator_user_id',
                    QApplication::GetLanguageId(),
                    $this->objProject->ProjectId
                )
            );
            
            if ($objDbResult)
                while($arrRow = $objDbResult->FetchArray()) {
                    if ($arrRow['validator_user_id'] != NarroUser::ANONYMOUS_USER_ID && $arrRow['reviews'] > 0) {
                        $objUser = NarroUser::Load($arrRow['validator_user_id']);
                        $arrReviews[] = $arrRow['reviews'];
                        $objDateSpan = new QDateTimeSpan(time() - strtotime($arrRow['last_review']));
                        $strLastReview = $objDateSpan->SimpleDisplay();
                        $arrData[] = array('user' => $objUser, 'reviews' => $arrRow['reviews'], 'last_review' => sprintf(t('%s ago'), $strLastReview));
                    }
                }
            
            array_multisort($arrReviews, SORT_DESC, SORT_NUMERIC, $arrData);
            
            $this->dtgReviewers->DataSource = $arrData;
        }

        public function colWorldsTranslated_Render($arrRow) {
            return $arrRow['words_translated'];
        }
        
        public function btnRefresh_Click($strFormId, $strControlId, $strParameter) {
            $this->pnlProgressBar->Total = $this->objProject->CountAllTextsByLanguage();
            $this->pnlProgressBar->Translated = $this->objProject->CountApprovedTextsByLanguage();
            $this->pnlProgressBar->Fuzzy = $this->objProject->CountTranslatedTextsByLanguage();
            $this->pnlProgressBar->MarkAsModified();
        }        
    }
