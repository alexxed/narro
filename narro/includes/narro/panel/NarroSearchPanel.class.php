<?php
    /**
     * @package Narro
     * @subpackage Panels
     *
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

    class NarroSearchPanel extends QPanel {
        public $ctlWrapper;

        public $dtgProject;
        public $dtgText;
        public $dtgSuggestion;

        public $txtSearch;
        public $btnSearch;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroSearchPanel.tpl.php';

            $this->dtgProject = new NarroProjectDataGrid($this);
            $this->dtgProject->Title = t('Projects');
            $this->dtgProject->ShowHeader = true;
            $this->dtgProject->ShowFilter = false;
            $this->dtgProject->SetCustomStyle('margin-bottom', '1em');
            $colName = $this->dtgProject->MetaAddColumn('ProjectName');
            $colName->Html = '<?=$_CONTROL->ParentControl->dtgProject_colName_Render($_ITEM)?>';
            $colName->HtmlEntities = false;
            // Datagrid Paginator
            $this->dtgProject->Paginator = new QPaginator($this->dtgProject);
            $this->dtgProject->PaginatorAlternate = new QPaginator($this->dtgProject);
            $this->dtgProject->ItemsPerPage = 10;

            $colPercentTranslated = new QDataGridColumn(
                t('Progress'),
                '<?= $_CONTROL->ParentControl->dtgProject_colPercentTranslated_Render($_ITEM) ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(
                        QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, true,
                        QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, true
                    ),
                    'ReverseOrderByClause' => QQ::OrderBy(
                        QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, false,
                        QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, false
                    )
                )
            );
            $colPercentTranslated->HtmlEntities = false;
            $colPercentTranslated->Wrap = false;

            $colLastActivity = new QDataGridColumn(
                t('Last Activity'),
                '<?= $_CONTROL->ParentControl->dtgProject_colLastActivity_Render($_ITEM) ?>',
                array(
                    'OrderByClause' => QQ::OrderBy(
                        QQN::NarroProject()->NarroProjectProgressAsProject->LastModified, true
                    ),
                    'ReverseOrderByClause' => QQ::OrderBy(
                        QQN::NarroProject()->NarroProjectProgressAsProject->LastModified, false
                    )
                )
            );
            $colLastActivity->HtmlEntities = false;

            $this->dtgProject->AddColumn($colLastActivity);
            $this->dtgProject->AddColumn($colPercentTranslated);



            // Specify Whether or Not to Refresh using Ajax
            $this->dtgProject->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgProject->SetDataBinder('dtgProject_Bind', $this);

            $this->dtgSuggestion = new NarroSuggestionDataGrid($this);
            $this->dtgSuggestion->Title = t('Translations');
            $this->dtgSuggestion->ShowHeader = true;
            $this->dtgSuggestion->ShowFilter = false;
            $this->dtgSuggestion->SetCustomStyle('margin-bottom', '1em');

            $colText = $this->dtgSuggestion->MetaAddColumn(QQN::NarroSuggestion()->Text->TextValue);
            $colText->Name = t('Text');

            $colTranslation = $this->dtgSuggestion->MetaAddColumn('SuggestionValue');
            $colTranslation->Name = t('Translation');

            $colProjects = new QDataGridColumn(
                t('Projects'),
                '<?= $_CONTROL->ParentControl->dtgSuggestion_colProjects_Render($_ITEM) ?>'
            );
            $colProjects->HtmlEntities = false;

            $this->dtgSuggestion->AddColumn($colProjects);

            $this->dtgSuggestion->AdditionalClauses = array(QQ::Expand(QQN::NarroSuggestion()->Text));
            // Datagrid Paginator
            $this->dtgSuggestion->Paginator = new QPaginator($this->dtgSuggestion);
            $this->dtgSuggestion->PaginatorAlternate = new QPaginator($this->dtgSuggestion);
            $this->dtgSuggestion->ItemsPerPage = 10;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestion->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestion->SetDataBinder('dtgSuggestion_Bind', $this);

            $this->dtgText = new NarroContextInfoDataGrid($this);
            $this->dtgText->Title = t('Untranslated texts');
            $this->dtgText->ShowHeader = true;
            $this->dtgText->ShowFilter = false;

            $colText = $this->dtgText->MetaAddColumn(QQN::NarroContextInfo()->Context->Text->TextValue);
            $colText->Name = 'Untranslated text';
            $colText->Html = '<?=NarroLink::ContextSuggest($_ITEM->Context->ProjectId, $_ITEM->Context->FileId, $_ITEM->ContextId, null, null, null, null, null, -1, 0, 0, nl2br(NarroString::HtmlEntities($_ITEM->Context->Text->TextValue)), "nolink")?>';
            $colText->HtmlEntities = false;
            // Datagrid Paginator
            $this->dtgText->Paginator = new QPaginator($this->dtgText);
            $this->dtgText->PaginatorAlternate = new QPaginator($this->dtgText);
            $this->dtgText->ItemsPerPage = 10;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgText->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgText->SetDataBinder('dtgText_Bind', $this);

            $this->txtSearch_Create();
            $this->btnSearch_Create();

        }

        protected function txtSearch_Create() {
            $this->txtSearch = new QTextBox($this);
            $this->txtSearch->FontSize = '1.5em';
            $this->txtSearch->Width = '80%';
        }

        protected function btnSearch_Create() {
            $this->btnSearch = new QButton($this);
            $this->btnSearch->Text = t('Search');
            $this->btnSearch->PrimaryButton = true;
            $this->btnSearch->FontSize = '1.5em';

            if (QApplication::$UseAjax)
                $this->btnSearch->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnSearch_Click'));
            else
                $this->btnSearch->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSearch_Click'));
        }

        public function dtgProject_colLastActivity_Render(NarroProject $objProject) {
            if ($objProject->_NarroProjectProgressAsProject && $objProject->_NarroProjectProgressAsProject->LastModified->Timestamp > 0) {
                $objDateSpan = new QDateTimeSpan(time() - $objProject->_NarroProjectProgressAsProject->LastModified->Timestamp);
                $strModifiedWhen = $objDateSpan->SimpleDisplay();
                return sprintf(t('%s ago'), $strModifiedWhen);
            }
            else {
                return t('never');
            }
        }

        public function dtgProject_colPercentTranslated_Render(NarroProject $objProject) {
            $intTotalTexts = $objProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objProject->CountApprovedTextsByLanguage();
            $strOutput = '';

            $objProgressBar = new NarroTranslationProgressBar($this->dtgProject);

            $objProgressBar->Total = $intTotalTexts;
            $objProgressBar->Translated = $intApprovedTexts;
            $objProgressBar->Fuzzy = $intTranslatedTexts;

            $strOutput .= $objProgressBar->Render(false);

            $strOutput =
                NarroLink::ContextSuggest(
                    $objProject->ProjectId,
                    0,
                    0,
                    NarroTextListForm::SHOW_UNTRANSLATED_TEXTS,
                    NarroTextListForm::SEARCH_TEXTS,
                    '',
                    0,
                    $intTotalTexts - $intApprovedTexts - $intTranslatedTexts,
                    -1,
                    0,
                    0,
                    $strOutput
                );

            QApplication::$PluginHandler->DisplayInProjectListInProgressColumn($objProject);

            if (is_array(QApplication::$PluginHandler->PluginReturnValues)) {
                $strOutput .= '';
                foreach(QApplication::$PluginHandler->PluginReturnValues as $strPluginName=>$mixReturnValue) {
                    if (count($mixReturnValue) == 2 && $mixReturnValue[0] instanceof NarroProject && is_string($mixReturnValue[1]) && $mixReturnValue[1] != '') {
                        $strOutput .= sprintf('<span style="font-size:small" title="%s">%s</span><br />', $strPluginName, $mixReturnValue[1]);
                    }
                }
                $strOutput .= '';
            }

            return $strOutput;
        }

        public function dtgProject_colName_Render(NarroProject $objProject) {

            $intTotalTexts = $objProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objProject->CountApprovedTextsByLanguage();

            if ($objProject->Active)
                $strProjectName =
                    '<span style="font-size:1.2em;font-weight:bold;">' .
                    $objProject->ProjectName .
                    '</span>';
            else
                $strProjectName =
                    '<span style="color:gray;font-style:italic;font-size:1.2em">' .
                    $objProject->ProjectName .
                    '</span>';

            return
                NarroLink::Project($objProject->ProjectId, $strProjectName) .
                '<div style="display:block">' .
                htmlspecialchars($objProject->ProjectDescription) .
                '</div>';
        }

        public function dtgSuggestion_colProjects_Render(NarroSuggestion $objSuggestion) {
            $arrProjects = array();
            $objDatabase = NarroSuggestion::GetDatabase();
            $objDbResult = $objDatabase->query(sprintf('SELECT DISTINCT p.project_id, p.project_name FROM narro_project p, narro_context c, narro_text t, narro_suggestion s WHERE s.text_id=t.text_id AND t.text_id=c.text_id AND c.active = 1 AND c.project_id=p.project_id AND s.suggestion_id=%d', $objSuggestion->SuggestionId));
            if ($objDbResult) {
                while ($arrRow = $objDbResult->FetchArray()) {
                    $arrProjects[] = NarroLink::ProjectTextList($arrRow[0], NarroTextListForm::SHOW_ALL_TEXTS, NarroTextListForm::SEARCH_TEXTS, $objSuggestion->Text->TextValue, $arrRow[1]);
                }
            }

            return sprintf('<div style="font-weight: bold">%s</div>', join(', ', $arrProjects));
        }

        public function btnSearch_Click() {
            $this->Refresh();
        }

        public function dtgProject_Bind() {
            $this->dtgProject->AdditionalConditions = QQ::Like(QQN::NarroProject()->ProjectName, '%' . $this->txtSearch->Text . '%');
            $this->dtgProject->MetaDataBinder();
            $this->dtgProject->Display = (bool) $this->dtgProject->TotalItemCount;
        }

        public function dtgText_Bind() {
            if (strlen($this->txtSearch->Text) > 2) {
                $this->dtgText->AdditionalConditions = QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->HasSuggestions, 0),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1),
                    QQ::Like(QQN::NarroContextInfo()->Context->Text->TextValue, '%' . $this->txtSearch->Text . '%')
                );
                $this->dtgText->MetaDataBinder();
            }
            $this->dtgText->Display = (bool) $this->dtgText->TotalItemCount;
        }

        public function dtgSuggestion_Bind() {
            if (strlen($this->txtSearch->Text) > 2) {
                $this->dtgSuggestion->AdditionalConditions = QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::GetLanguageId()),
                    QQ::OrCondition(
                        QQ::Like(QQN::NarroSuggestion()->Text->TextValue, '%' . $this->txtSearch->Text . '%'),
                        QQ::Like(QQN::NarroSuggestion()->SuggestionValue, '%' . $this->txtSearch->Text . '%')
                    )
                );
                $this->dtgSuggestion->MetaDataBinder();
            }
            $this->dtgSuggestion->Display = (bool) $this->dtgSuggestion->TotalItemCount;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {

                default:
                    try {
                        return parent::__get($strName);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }

    }
?>
