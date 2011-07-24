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

    class NarroProjectFileListPanel extends QPanel {
        protected $objProject;
        public $pnlBreadcrumb;

        public $dtgNarroFile;
        protected $objParentFile;

        // DataGrid Columns
        protected $colFileName;
        protected $colPercentTranslated;
        protected $colExport;

        public $chkShowHierarchy;
        public $chkShowFolders;

        public $txtSearch;
        public $btnSearch;

        public function __construct(NarroProject $objNarroProject, string $strCurrentPath = null, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->pnlBreadcrumb = new NarroBreadcrumbPanel($this);
            $this->pnlBreadcrumb->strSeparator = ' / ';

            $this->objProject = $objNarroProject;

            // Setup DataGrid Columns
            $this->colFileName = new QDataGridColumn(t('File name'), '<?= $_CONTROL->ParentControl->dtgNarroFile_FileNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName, false)));
            $this->colFileName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_CONTROL->ParentControl->dtgNarroFile_PercentTranslated_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->ProgressPercent), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->ProgressPercent, false)));
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Width = 160;

            $this->colExport = new QDataGridColumn(t('Export'), '<?= $_CONTROL->ParentControl->dtgNarroFile_ExportColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->Export), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->Export, false)));
            $this->colExport->HtmlEntities = false;

            // Setup DataGrid
            $this->dtgNarroFile = new NarroDataGrid($this);

            // Datagrid Paginator
            $this->dtgNarroFile->Paginator = new QPaginator($this->dtgNarroFile);
            $this->dtgNarroFile->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');
            $this->dtgNarroFile->PaginatorAlternate = new QPaginator($this->dtgNarroFile);
            $this->dtgNarroFile->SortColumnIndex = 0;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroFile->UseAjax = false;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroFile->SetDataBinder('dtgNarroFile_Bind', $this);

            $this->dtgNarroFile->AddColumn($this->colFileName);
            $this->dtgNarroFile->AddColumn($this->colPercentTranslated);
            if (QApplication::HasPermission('Can manage project', $this->objProject->ProjectId, QApplication::GetLanguageId()))
                $this->dtgNarroFile->AddColumn($this->colExport);

            $this->chkShowHierarchy = new QCheckBox($this);
            $this->chkShowHierarchy->Checked = (QApplication::QueryString('s') == '');
            $this->chkShowHierarchy->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgNarroFile_Bind'));

            $this->chkShowFolders = new QCheckBox($this);
            $this->chkShowFolders->Checked = true;
            $this->chkShowFolders->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgNarroFile_Bind'));

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectFileListPanel.tpl.php';

            $this->ChangeDirectory($strCurrentPath);

            $this->btnSearch = new QButton($this);
            $this->btnSearch->Text = t('Search');
            $this->btnSearch->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSearch_Click'));
            $this->btnSearch->PrimaryButton = true;

            $this->txtSearch = new QTextBox($this);
            $this->txtSearch->Text = QApplication::QueryString('s');

            if ($this->txtSearch->Text != '')
                $this->ChangeDirectory('');

        }

        public function ChangeDirectory($strPath) {

            if ($strPath)
                $this->objParentFile = NarroFile::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                        QQ::Equal(QQN::NarroFile()->Active, 1),
                        QQ::Equal(QQN::NarroFile()->FilePath, $strPath)
                    )
                );

            $this->pnlBreadcrumb->Visible = false;
            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectFileList($this->objProject->ProjectId, null, null, '..')
            );

            if ($this->objParentFile) {
                $arrPaths = explode('/', $this->objParentFile->FilePath);
                $strProgressivePath = '';
                if (is_array($arrPaths)) {
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
                                    $this->objProject->ProjectId,
                                    $strProgressivePath,
                                    null,
                                    $strPathPart
                            )
                        );
                    }
                }
            }

            if ($this->objParentFile instanceof NarroFile) {
                $this->pnlBreadcrumb->addElement($this->objParentFile->FileName);
                $this->pnlBreadcrumb->Visible = true;
            }
        }

        public function dtgNarroFile_PercentTranslated_Render(NarroFile $objFile) {
            $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroFile);

            $objProgressBar->Total = $objFile->CountAllTextsByLanguage();
            $objProgressBar->Translated = $objFile->CountApprovedTextsByLanguage();
            $objProgressBar->Fuzzy = $objFile->CountTranslatedTextsByLanguage();

            $sOutput = $objProgressBar->Render(false);

            if ($objFile->TypeId == NarroFileType::Folder)
                return $sOutput;

            if ($objProgressBar->Translated + $objProgressBar->Fuzzy < $objProgressBar->Total)
                return NarroLink::FileTextList($objFile->ProjectId, $objFile->FileId, NarroTextListForm::SHOW_UNTRANSLATED_TEXTS, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);
            elseif ($objProgressBar->Translated < $objProgressBar->Total)
                return NarroLink::FileTextList($objFile->ProjectId, $objFile->FileId, NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);
            else
                return NarroLink::FileTextList($objFile->ProjectId, $objFile->FileId, NarroTextListForm::SHOW_ALL_TEXTS, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);

        }

        public function dtgNarroFile_FileNameColumn_Render(NarroFile $objFile) {
            if ($objFile->TypeId == NarroFileType::Folder)
                return sprintf('<img src="%s" style="vertical-align:middle" /> %s',
                    'assets/images/folder.png',
                    NarroLink::ProjectFileList(
                        $this->objProject->ProjectId,
                        $objFile->FilePath,
                        null,
                        $objFile->FileName
                    )
                );
            else {
                switch($objFile->TypeId) {
                    case NarroFileType::MozillaDtd:
                            $strIcon = 'dtd_file.gif';
                            break;
                    case NarroFileType::MozillaInc:
                            $strIcon = 'inc_file.gif';
                            break;
                    case NarroFileType::MozillaIni:
                            $strIcon = 'ini_file.gif';
                            break;
                    default:
                            $strIcon = 'dtd_file.gif';
                }

                return sprintf('<img src="%s" style="vertical-align:middle" /> %s',
                    __IMAGE_ASSETS__ . '/../../images/' . $strIcon,
                    NarroLink::FileTextList(
                        $objFile->ProjectId,
                        $objFile->FileId,
                        1,
                        1,
                        '',
                        $objFile->FileName
                    )
                );
            }
        }

        public function dtgNarroFile_ExportColumn_Render(NarroFile $objFile) {
            if ($objFile->TypeId == NarroFileType::Folder)
                return '';

            $strControlId = 'chkExport' . $this->dtgNarroFile->CurrentRowIndex;
            $chkExport = $this->dtgNarroFile->GetChildControl($strControlId);
            if (!$chkExport) {
                $chkExport = new QCheckBox($this, $strControlId);
                $chkExport->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'chkExport_Click'));
            }
            $chkExport->ActionParameter = $objFile->FileId;
            $chkExport->Checked = NarroFileProgress::CountByFileIdLanguageIdExport($objFile->FileId, QApplication::GetLanguageId(), 1);

            return $chkExport->Render(false);
        }

        public function chkExport_Click($strFormId, $strControlId, $intFileId) {
            $chkExport = $this->dtgNarroFile->GetChildControl($strControlId);
            $objFileProgress = NarroFileProgress::LoadByFileIdLanguageId($intFileId, QApplication::GetLanguageId());
            if ($objFileProgress) {
                $objFileProgress->Export = !$objFileProgress->Export;
                $objFileProgress->Save();
            }
        }

        public function dtgNarroFile_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            if ($this->txtSearch->Text == '')
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroFile()->Active, 1),
                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId)
                );
            else {
                $objCommonCondition = QQ::AndCondition(
                    QQ::Equal(QQN::NarroFile()->Active, 1),
                    QQ::Equal(QQN::NarroFile()->ProjectId, $this->objProject->ProjectId),
                    QQ::Like(QQN::NarroFile()->FileName, sprintf('%%%s%%', $this->txtSearch->Text))
                );
                $this->chkShowHierarchy->Checked = false;
            }

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            if (!$this->chkShowHierarchy->Checked) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount($objCommonCondition);
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder) ));
            }
            elseif ($this->objParentFile) {
                $objParentCondition = QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId);
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, $objParentCondition));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, $objParentCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)));
            }
            else {
                $objParentCondition = QQ::IsNull(QQN::NarroFile()->ParentId);
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, $objParentCondition));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, $objParentCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)));
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
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder) ), $objClauses);
            }
            elseif ($this->objParentFile) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId)), $objClauses);
                else
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)), $objClauses);
            }
            else {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId)), $objClauses);
                else
                    $this->dtgNarroFile->DataSource = NarroFile::QueryArray(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)), $objClauses);
            }
        }

        public function btnSearch_Click() {
            QApplication::Redirect(NarroLink::ProjectFileList($this->objProject->ProjectId, ($this->objParentFile instanceof NarroFile)?$this->objParentFile->FilePath:'', $this->txtSearch->Text));
        }
    }
?>
