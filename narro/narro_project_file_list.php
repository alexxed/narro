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

    require_once('includes/prepend.inc.php');
    require_once('includes/narro/narro_progress_bar.class.php');

    class NarroProjectFileListForm extends QForm {
        protected $dtgNarroFile;

        // DataGrid Columns
        protected $colFileName;
        protected $colPercentTranslated;

        protected $objNarroProject;
        protected $objParentFile;

        protected $chkShowHierarchy;
        protected $chkShowFolders;

        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            // Set mode to Edit or New depending on what's found
            $intProjectId = QApplication::QueryString('p');
            if ($intProjectId) {
                $this->objNarroProject = NarroProject::Load(($intProjectId));

                if (!$this->objNarroProject)
                    QApplication::Redirect('narro_project_list.php');

            } else
                QApplication::Redirect('narro_project_list.php');

            $intParentId = QApplication::QueryString('pf');

            if ($intParentId)
                $this->objParentFile = NarroFile::Load($intParentId);

        }

        protected function Form_Create() {
            $this->SetupNarroProject();

            // Setup DataGrid Columns
            $this->colFileName = new QDataGridColumn(QApplication::Translate('File name'), '<?= $_FORM->dtgNarroFile_FileNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName, false)));
            $this->colFileName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(QApplication::Translate('Progress'), '<?= $_FORM->dtgNarroFile_PercentTranslated_Render($_ITEM); ?>');
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Width = 160;


            // Setup DataGrid
            $this->dtgNarroFile = new QDataGrid($this);

            // Datagrid Paginator
            $this->dtgNarroFile->Paginator = new QPaginator($this->dtgNarroFile);
            $this->dtgNarroFile->ItemsPerPage = 20;
            $this->dtgNarroFile->PaginatorAlternate = new QPaginator($this->dtgNarroFile);

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroFile->UseAjax = false;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroFile->SetDataBinder('dtgNarroFile_Bind');

            $this->dtgNarroFile->AddColumn($this->colFileName);
            $this->dtgNarroFile->AddColumn($this->colPercentTranslated);

            $this->chkShowHierarchy = new QCheckBox($this);
            $this->chkShowHierarchy->Checked = true;
            $this->chkShowHierarchy->AddAction(new QClickEvent(), new QAjaxAction('dtgNarroFile_Bind'));

            $this->chkShowFolders = new QCheckBox($this);
            $this->chkShowFolders->Checked = true;
            $this->chkShowFolders->AddAction(new QClickEvent(), new QAjaxAction('dtgNarroFile_Bind'));

        }

        public function dtgNarroFile_PercentTranslated_Render(NarroFile $objNarroFile) {
            if ($objNarroFile->TypeId != NarroFileType::Dosar) {
                $sOutput = '';

                $objDatabase = QApplication::$Database[1];

                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c WHERE c.project_id=%d AND c.active=1 AND c.file_id=%d', $objNarroFile->ProjectId, $objNarroFile->FileId);

                // Perform the Query
                $objDbResult = $objDatabase->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intTotalTexts = $mixRow['cnt'];

                    $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1 AND c.file_id=%d', $objNarroFile->ProjectId, QApplication::$objUser->Language->LanguageId, $objNarroFile->FileId);

                    // Perform the Query
                    $objDbResult = $objDatabase->Query($strQuery);

                    if ($objDbResult) {
                        $mixRow = $objDbResult->FetchArray();
                        $intTranslatedTexts = $mixRow['cnt'];
                    }

                    $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1 AND c.file_id=%d', $objNarroFile->ProjectId, QApplication::$objUser->Language->LanguageId, $objNarroFile->FileId);
                    // Perform the Query
                    $objDbResult = $objDatabase->Query($strQuery);

                    if ($objDbResult) {
                        $mixRow = $objDbResult->FetchArray();
                        $intValidatedTexts = $mixRow['cnt'];
                    }

                    $objProgressBar = $this->GetControl('progressbar' . $objNarroFile->FileId);
                    if (!$objProgressBar instanceof NarroTranslationProgressBar)
                        $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroFile, 'progressbar' . $objNarroFile->FileId);

                    $objProgressBar->Total = $intTotalTexts;
                    $objProgressBar->Translated = $intValidatedTexts;
                    $objProgressBar->Fuzzy = $intTranslatedTexts;

                    $sOutput .= $objProgressBar->Render(false);

                }
                return $sOutput;
            }
            else
                return '';

        }

        public function dtgNarroFile_FileNameColumn_Render(NarroFile $objNarroFile) {
            if ($objNarroFile->TypeId != NarroFileType::Dosar)
                return sprintf('<a href="narro_file_text_list.php?p=%d&f=%s">%s</a>',
                    $this->objNarroProject->ProjectId,
                    $objNarroFile->FileId,
                    $objNarroFile->FileName
                );
            else
                return sprintf('<a href="narro_project_file_list.php?p=%d&pf=%d">%s</a>',
                    $objNarroFile->ProjectId,
                    $objNarroFile->FileId,
                    $objNarroFile->FileName
                );
        }

        protected function dtgNarroFile_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            $objCommonCondition = QQ::AndCondition(
                QQ::Equal(QQN::NarroFile()->Active, 1),
                QQ::Equal(QQN::NarroFile()->ProjectId, $this->objNarroProject->ProjectId)
            );

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            if (!$this->chkShowHierarchy->Checked) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount($objCommonCondition);
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar) ));
            }
            elseif ($this->objParentFile) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId)));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar)));
            }
            else {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId)));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar)));
            }

            // Setup the $objClauses Array
            $objClauses = array();

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgNarroFile->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgNarroFile->LimitClause)
                array_push($objClauses, $objClause);

            // Set the DataSource to be the array of all NarroFile objects, given the clauses above
            if (!$this->chkShowHierarchy->Checked) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray($objCommonCondition, $objClauses);
                else
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar) ), $objClauses);
            }
            elseif ($this->objParentFile) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId)), $objClauses);
                else
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar)), $objClauses);
            }
            else {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId)), $objClauses);
                else
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Dosar)), $objClauses);
            }

        }

        protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
            $objNarroFile = NarroFile::Load($strParameter);
            if ($objNarroFile instanceof NarroFile) {
                $objEncodingBox = $this->GetControl('fileenc' . $strParameter);
                $objNarroFile->Encoding = $objEncodingBox->TextValue;
                $objNarroFile->Save();
            }
        }

    }

    NarroProjectFileListForm::Run('NarroProjectFileListForm', 'templates/narro_project_file_list.tpl.php');

?>