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

    class NarroFileTextListPanel extends NarroTextListPanel {
        public $objNarroFile;
        public $pnlBreadcrumb;
        public $pnlImportFile;
        public $pnlExportFile;

        public function __construct(NarroProject $objNarroProject, NarroFile $objNarroFile, $objParentObject, $strControlId = null) {
            parent::__construct($objNarroProject, $objParentObject, $strControlId);

            $this->objNarroFile = $objNarroFile;

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroFileTextListPanel.tpl.php';

            $this->pnlBreadcrumb = new NarroBreadcrumbPanel($this);
            $this->pnlBreadcrumb->strSeparator = ' / ';
            $this->pnlBreadcrumb->Visible = false;
            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectFileList($this->objNarroProject->ProjectId, null, null, '..')
            );
            $arrPaths = explode('/', $this->objNarroFile->FilePath);
            $strProgressivePath = '';
            if (is_array($arrPaths)) {
                $this->pnlBreadcrumb->Visible = true;
                /**
                 * remove the first part that is empty because paths begin with /
                 * and the last part that will be displayed unlinked
                 */
                unset($arrPaths[count($arrPaths) - 1]);
                unset($arrPaths[0]);
                foreach($arrPaths as $intCnt =>$strPathPart) {
                    $strProgressivePath .= '/' . $strPathPart;
                    $this->pnlBreadcrumb->addElement(
                        NarroLink::ProjectFileList(
                                $this->objNarroProject->ProjectId,
                                $strProgressivePath,
                                null,
                                $strPathPart
                        )
                    );
                }

                $this->pnlBreadcrumb->addElement($this->objNarroFile->FileName);
            }

            switch($this->lstSearchType->SelectedValue) {
                case NarroTextListForm::SEARCH_SUGGESTIONS:
                    $this->SetMessage(t('Note that, since you\'re searching suggestions, you won\'t see the texts without suggestions.'));
                    break;
                case NarroTextListForm::SEARCH_AUTHORS:
                    $this->SetMessage(t('Note that, since you\'re searching authors of suggestions, you won\'t see the texts without suggestions.'));
                    break;
            }

            $this->dtgNarroContextInfo->Title = sprintf(t('Texts from the file "%s"'), $this->objNarroFile->FileName);

            $this->pnlImportFile = new NarroFileImportPanel($this->objNarroFile, $this);
            $this->pnlExportFile = new NarroFileExportPanel($this->objNarroFile, $this);

        }

        public function dtgNarroContextInfo_TranslatedText_Render(NarroContextInfo $objNarroContextInfo, $strLink = null) {
            return parent::dtgNarroContextInfo_TranslatedText_Render(
                $objNarroContextInfo,
                NarroLink::ContextSuggest(
                        $this->objNarroFile->Project->ProjectId,
                        $this->objNarroFile->FileId,
                        $objNarroContextInfo->ContextId,
                        $this->lstTextFilter->SelectedValue,
                        $this->lstSearchType->SelectedValue,
                        $this->txtSearch->Text,
                        $this->dtgNarroContextInfo->CurrentRowIndex + 1 + (($this->dtgNarroContextInfo->PageNumber - 1) * $this->dtgNarroContextInfo->ItemsPerPage),
                        $this->dtgNarroContextInfo->TotalItemCount,
                        $this->dtgNarroContextInfo->SortColumnIndex,
                        $this->dtgNarroContextInfo->SortDirection,
                        0
                   )
               );
        }

        public function lstTextFilter_Change() {
            QApplication::Redirect(NarroLink::FileTextList($this->objNarroFile->ProjectId, $this->objNarroFile->FileId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }

        public function btnSearch_Click() {
            QApplication::Redirect(NarroLink::FileTextList($this->objNarroFile->ProjectId, $this->objNarroFile->FileId, $this->lstTextFilter->SelectedValue, $this->lstSearchType->SelectedValue, $this->txtSearch->Text));
        }

        public function dtgNarroContextInfo_Bind() {
            $this->arrSuggestionList = array();

            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()
            if (QApplication::HasPermissionForThisLang('Can mass approve', $this->objNarroProject->ProjectId) && $this->btnMultiApprove->Text == t('Save'))
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objNarroFile->FileId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1),
                    QQ::LessThan(QQN::NarroContextInfo()->Context->Text->TextCharCount, 100),
                    QQ::IsNull(QQN::NarroContextInfo()->TextAccessKey)
                );

            else
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objNarroFile->FileId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
                );

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
        }

    }
?>
