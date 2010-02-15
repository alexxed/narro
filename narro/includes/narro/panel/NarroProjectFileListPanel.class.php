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
        protected $objNarroProject;
        public $pnlBreadcrumb;

        public $dtgNarroFile;
        protected $objParentFile;

        // DataGrid Columns
        protected $colFileName;
        protected $colPercentTranslated;
        protected $colActions;

        public $chkShowHierarchy;
        public $chkShowFolders;

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

            $this->objNarroProject = $objNarroProject;

            // Setup DataGrid Columns
            $this->colFileName = new QDataGridColumn(t('File name'), '<?= $_CONTROL->ParentControl->dtgNarroFile_FileNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->FileName, false)));
            $this->colFileName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_CONTROL->ParentControl->dtgNarroFile_PercentTranslated_Render($_ITEM); ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->ProgressPercent), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroFile()->NarroFileProgressAsFile->ProgressPercent, false)));
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Width = 160;

            $this->colActions = new QDataGridColumn(t('Actions'), '<?= $_CONTROL->ParentControl->dtgNarroFile_ActionsColumn_Render($_ITEM) ?>');
            $this->colActions->HtmlEntities = false;

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
            $this->dtgNarroFile->AddColumn($this->colActions);

            $this->chkShowHierarchy = new QCheckBox($this);
            $this->chkShowHierarchy->Checked = true;
            $this->chkShowHierarchy->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgNarroFile_Bind'));

            $this->chkShowFolders = new QCheckBox($this);
            $this->chkShowFolders->Checked = true;
            $this->chkShowFolders->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgNarroFile_Bind'));

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectFileListPanel.tpl.php';

            $this->ChangeDirectory($strCurrentPath);

        }

        public function ChangeDirectory($strPath) {

            if ($strPath)
                $this->objParentFile = NarroFile::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroFile()->ProjectId, $this->objNarroProject->ProjectId),
                        QQ::Equal(QQN::NarroFile()->Active, 1),
                        QQ::Equal(QQN::NarroFile()->FilePath, $strPath)
                    )
                );

            $this->pnlBreadcrumb->Visible = false;
            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectFileList($this->objNarroProject->ProjectId, null, '..')
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
                                    $this->objNarroProject->ProjectId,
                                    $strProgressivePath,
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

        public function dtgNarroFile_PercentTranslated_Render(NarroFile $objNarroFile) {
            $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroFile);

            $objProgressBar->Total = $objNarroFile->CountAllTextsByLanguage();
            $objProgressBar->Translated = $objNarroFile->CountApprovedTextsByLanguage();
            $objProgressBar->Fuzzy = $objNarroFile->CountTranslatedTextsByLanguage();

            $sOutput = $objProgressBar->Render(false);

            if ($objNarroFile->TypeId == NarroFileType::Folder)
                return $sOutput;

            if ($objProgressBar->Translated + $objProgressBar->Fuzzy < $objProgressBar->Total)
                return NarroLink::FileTextList($objNarroFile->ProjectId, $objNarroFile->FileId, NarroTextListForm::SHOW_UNTRANSLATED_TEXTS, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);
            elseif ($objProgressBar->Translated < $objProgressBar->Total)
                return NarroLink::FileTextList($objNarroFile->ProjectId, $objNarroFile->FileId, NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_APPROVAL, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);
            else
                return NarroLink::FileTextList($objNarroFile->ProjectId, $objNarroFile->FileId, NarroTextListForm::SHOW_ALL_TEXTS, NarroTextListForm::SEARCH_TEXTS, '', $sOutput);

        }

        public function dtgNarroFile_FileNameColumn_Render(NarroFile $objNarroFile) {
            if ($objNarroFile->TypeId == NarroFileType::Folder)
                return sprintf('<img src="%s" style="vertical-align:middle" /> %s',
                    'assets/images/folder.png',
                    NarroLink::ProjectFileList(
                        $this->objNarroProject->ProjectId,
                        $objNarroFile->FilePath,
                        $objNarroFile->FileName
                    )
                );
            else {
                switch($objNarroFile->TypeId) {
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
                        $objNarroFile->ProjectId,
                        $objNarroFile->FileId,
                        1,
                        1,
                        '',
                        $objNarroFile->FileName
                    )
                );
            }
        }

        public function dtgNarroFile_ActionsColumn_Render(NarroFile $objNarroFile) {
            if ($objNarroFile->TypeId == NarroFileType::Folder) {
                return '';
            }
            else {
                $strTemplateFile = __IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE . $objNarroFile->FilePath;

                if (!file_exists($strTemplateFile)) return 'No template on disk';

                if (!$objExportButton = $this->Form->GetControl('btnExport' . $objNarroFile->FileId)) {
                    $objExportButton = new QButton($this->dtgNarroFile, 'btnExport' . $objNarroFile->FileId);
                    $objExportButton->Text = t('Export');
                    $objExportButton->ActionParameter = $objNarroFile->FileId;
                    $objExportButton->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnExport_Click'));
                }
                $objExportButton->Visible = QApplication::HasPermissionForThisLang('Can export file', $objNarroFile->ProjectId);
                if (!$objExportButton->Visible) {
                    if (file_exists($strTemplateFile) && filesize($strTemplateFile) < __MAXIMUM_FILE_SIZE_TO_EXPORT__)
                        $objExportButton->Visible = true;
                }

                if (!$objImportButton = $this->Form->GetControl('btnImport' . $objNarroFile->FileId)) {
                    $objImportButton = new QButton($this->dtgNarroFile, 'btnImport' . $objNarroFile->FileId);
                    $objImportButton->Text = t('Import');
                    $objImportButton->ActionParameter = $objNarroFile->FileId;
                    $objImportButton->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnImport_Click'));
                }
                $objImportButton->Visible = QApplication::HasPermissionForThisLang('Can import file', $objNarroFile->ProjectId);
                if (!$objImportButton->Visible) {
                    if (file_exists($strTemplateFile) && filesize($strTemplateFile) < __MAXIMUM_FILE_SIZE_TO_IMPORT__)
                        $objImportButton->Visible = true;
                    else
                        return filesize($strTemplateFile);
                }

                if (!$objImportFile = $this->Form->GetControl('fileImport' . $objNarroFile->FileId)) {
                    $objImportFile = new QFileControl($this->dtgNarroFile, 'fileImport' . $objNarroFile->FileId);
                }
                $objImportFile->Visible = $objImportButton->Visible;

                if (!$objExportFile = $this->Form->GetControl('fileExport' . $objNarroFile->FileId)) {
                    $objExportFile = new QFileControl($this->dtgNarroFile, 'fileExport' . $objNarroFile->FileId);
                }
                $objExportFile->Visible = $objExportButton->Visible;

                $strImportAction = '';
                $strExportAction = '';

                if ($objImportButton->Visible)
                    $strImportAction = t('File to import') . ': ' . $objImportFile->Render(false) . $objImportButton->Render(false);

                if ($objExportButton->Visible)
                    $strExportAction = t('Model to use') . ': ' . $objExportFile->Render(false) . $objExportButton->Render(false);


                return $strImportAction . '<br />' . $strExportAction;
            }
        }

        public function dtgNarroFile_Bind() {
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
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder) ));
            }
            elseif ($this->objParentFile) {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId)));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::Equal(QQN::NarroFile()->ParentId, $this->objParentFile->FileId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)));
            }
            else {
                if ($this->chkShowFolders->Checked)
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId)));
                else
                    $this->dtgNarroFile->TotalItemCount = NarroFile::QueryCount(QQ::AndCondition($objCommonCondition, QQ::IsNull(QQN::NarroFile()->ParentId), QQ::NotEqual(QQN::NarroFile()->TypeId, NarroFileType::Folder)));
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

        protected function btnExport_Click($strFormId, $strControlId, $strParameter) {
            $objFile = NarroFile::Load($strParameter);
            $objFileControl = $this->Form->GetControl('fileExport' . $strParameter);

            switch($objFile->TypeId) {
                case NarroFileType::MozillaDtd:
                    $objFileImporter = new NarroMozillaDtdFileImporter();
                    break;
                case NarroFileType::MozillaInc:
                    $objFileImporter = new NarroMozillaIncFileImporter();
                    break;
                case NarroFileType::MozillaIni:
                    $objFileImporter = new NarroMozillaIniFileImporter();
                    break;
                case NarroFileType::GettextPo:
                    $objFileImporter = new NarroGettextPoFileImporter();
                    break;
                case NarroFileType::DumbGettextPo:
                    $objFileImporter = new NarroDumbGettextPoFileImporter();
                    break;
                case NarroFileType::OpenOfficeSdf:
                    $objFileImporter = new NarroOpenOfficeSdfFileImporter();
                    break;
                case NarroFileType::Svg:
                    $objFileImporter = new NarroSvgFileImporter();
                    break;
                case NarroFileType::PhpMyAdmin:
                    $objFileImporter = new NarroPhpMyAdminFileImporter();
                    break;
                default:
                    throw new Exception(sprintf(t('Tried to export an unknown file type: %d'), $strParameter));
            }

            $objFileImporter->User = QApplication::$User;
            $objFileImporter->Project = $this->objNarroProject;
            $objFileImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
            $objFileImporter->TargetLanguage = QApplication::$Language;
            $objFileImporter->File = $objFile;

            $strImportLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-import.log';

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');
            $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strImportLogFile));

            $objFileImporter->Logger = $objLogger;

            $strTempFileName = tempnam(__TMP_PATH__, QApplication::$Language->LanguageCode);

            if ($objFileControl instanceof QFileControl && file_exists($objFileControl->File)) {
                $objFileImporter->ExportFile($objFileControl->File, $strTempFileName);
                unlink($objFileControl->File);
            }
            else
                $objFileImporter->ExportFile(__IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE . $objFile->FilePath, $strTempFileName);

            header(sprintf('Content-Disposition: attachment; filename="%s"', $objFile->FileName));
            readfile($strTempFileName);
            unlink($strTempFileName);
            exit;
        }

        protected function btnImport_Click($strFormId, $strControlId, $strParameter) {
            $objFileControl = $this->Form->GetControl('fileImport' . $strParameter);
            if (!$objFileControl instanceof QFileControl) return false;

            $objFile = NarroFile::Load($strParameter);
            if (!$objFile instanceof NarroFile) return false;

            switch($objFile->TypeId) {
                case NarroFileType::MozillaDtd:
                    $objFileImporter = new NarroMozillaDtdFileImporter();
                    break;
                case NarroFileType::MozillaInc:
                    $objFileImporter = new NarroMozillaIncFileImporter();
                    break;
                case NarroFileType::MozillaIni:
                    $objFileImporter = new NarroMozillaIniFileImporter();
                    break;
                case NarroFileType::GettextPo:
                    $objFileImporter = new NarroGettextPoFileImporter();
                    break;
                case NarroFileType::DumbGettextPo:
                    $objFileImporter = new NarroDumbGettextPoFileImporter();
                    break;
                case NarroFileType::Svg:
                    $objFileImporter = new NarroSvgFileImporter();
                    break;
                case NarroFileType::OpenOfficeSdf:
                    $objFileImporter = new NarroOpenOfficeSdfFileImporter();
                    break;
                case NarroFileType::PhpMyAdmin:
                    $objFileImporter = new NarroPhpMyAdminFileImporter();
                    break;
                default:
                    throw new Exception(sprintf(t('Tried to import an unknown file type: %d'), $strParameter));
            }

            $objFileImporter->User = QApplication::$User;
            $objFileImporter->Project = $this->objNarroProject;
            $objFileImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode(NarroLanguage::SOURCE_LANGUAGE_CODE);
            $objFileImporter->TargetLanguage = QApplication::$Language;
            $objFileImporter->CheckEqual = true;
            $objFileImporter->File = $objFile;

            $strImportLogFile = __TMP_PATH__ . '/' . $this->objNarroProject->ProjectId . '-' . QApplication::$Language->LanguageCode . '-import.log';

            require_once('Zend/Log.php');
            require_once('Zend/Log/Writer/Stream.php');
            $objLogger = new Zend_Log(new Zend_Log_Writer_Stream($strImportLogFile));

            $objFileImporter->Logger = $objLogger;
            $objFileImporter->OnlySuggestions = !QApplication::HasPermissionForThisLang('Can approve', $objFile->ProjectId);
            $objFileImporter->DeactivateFiles = false;
            $objFileImporter->DeactivateContexts = false;

            $objFileImporter->Approve = QApplication::HasPermissionForThisLang('Can approve', $objFile->ProjectId);

            $strTempFileName = tempnam(__TMP_PATH__, QApplication::$Language->LanguageCode);

            $objFileImporter->ImportFile(__IMPORT_PATH__ . '/' . $this->objNarroProject->ProjectId . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE . $objFile->FilePath, $objFileControl->File);

        }

    }
?>
