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

    class NarroProjectListForm extends QForm {
        protected $dtgNarroProject;

        // DataGrid Columns
        protected $colProjectName;
        protected $colPercentTranslated;
        protected $colActions;


        protected function Form_Create() {
            // Setup DataGrid Columns
            $this->colProjectName = new QDataGridColumn(t('Project name'), '<?= $_FORM->dtgNarroProject_ProjectNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName, false)));
            $this->colProjectName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_FORM->dtgNarroProject_PercentTranslated_Render($_ITEM) ?>');
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Width = 160;

            $this->colActions = new QDataGridColumn(t('Actions'), '<?= $_FORM->dtgNarroProject_Actions_Render($_ITEM) ?>');
            $this->colActions->HtmlEntities = false;
            $this->colActions->Width = 160;

            // Setup DataGrid
            $this->dtgNarroProject = new QDataGrid($this);

            // Datagrid Paginator
            $this->dtgNarroProject->Paginator = new QPaginator($this->dtgNarroProject);
            $this->dtgNarroProject->ItemsPerPage = 20;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroProject->UseAjax = false;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroProject->SetDataBinder('dtgNarroProject_Bind');

            $this->dtgNarroProject->AddColumn($this->colProjectName);
            $this->dtgNarroProject->AddColumn($this->colPercentTranslated);
            $this->dtgNarroProject->AddColumn($this->colActions);

        }

        public function dtgNarroProject_PercentTranslated_Render(NarroProject $objNarroProject) {
            $sOutput = '';

            $objDatabase = QApplication::$Database[1];

            $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c WHERE c.project_id=%d AND c.active=1', $objNarroProject->ProjectId);

            // Perform the Query
            $objDbResult = $objDatabase->Query($strQuery);

            if ($objDbResult) {
                $mixRow = $objDbResult->FetchArray();
                $intTotalTexts = $mixRow['cnt'];

                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM narro_context c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1', $objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId);

                // Perform the Query
                $objDbResult = $objDatabase->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intTranslatedTexts = $mixRow['cnt'];
                }

                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1', $objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId);
                // Perform the Query
                $objDbResult = $objDatabase->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intValidatedTexts = $mixRow['cnt'];
                }

                $objProgressBar = $this->GetControl('progressbar' . $objNarroProject->ProjectId);
                if (!$objProgressBar instanceof NarroTranslationProgressBar)
                    $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroProject, 'progressbar' . $objNarroProject->ProjectId);
                $objProgressBar->Total = $intTotalTexts;
                $objProgressBar->Translated = $intValidatedTexts;
                $objProgressBar->Fuzzy = $intTranslatedTexts;

                $sOutput .= $objProgressBar->Render(false);
            }
            return $sOutput;

        }

        public function dtgNarroProject_ProjectNameColumn_Render(NarroProject $objNarroProject) {
            return sprintf('<a href="narro_context_suggest.php?p=%s&tf=2&st=1&s=">%s</a>',
                $objNarroProject->ProjectId,
                $objNarroProject->ProjectName
            );
        }

        public function dtgNarroProject_Actions_Render(NarroProject $objNarroProject) {
            $strOutput = '';
            //if (QApplication::$objUser->hasPermission('Can export', $objNarroProject->ProjectId, QApplication::$objUser->Language->LanguageId)) {
            if (QApplication::$objUser->UserId != NarroUser::ANONYMOUS_USER_ID && $objNarroProject->ProjectType == NarroProjectType::Mozilla) {

                $btnExportButton = new QButton($this->dtgNarroProject, 'exportbut' . $objNarroProject->ProjectId);
                $btnExportButton->Text = t('Export');
                $btnExportButton->AddAction(new QClickEvent(), new QServerAction('btnExportButton_Click'));
                $btnExportButton->ActionParameter = $objNarroProject->ProjectId;

                $strOutput .= $btnExportButton->Render(false);
            }

            return $strOutput;
        }

        protected function dtgNarroProject_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            $this->dtgNarroProject->TotalItemCount = NarroProject::CountAll();

            // Setup the $objClauses Array
            $objClauses = array();

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgNarroProject->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgNarroProject->LimitClause)
                array_push($objClauses, $objClause);

            // Set the DataSource to be the array of all NarroProject objects, given the clauses above
            $this->dtgNarroProject->DataSource = NarroProject::LoadAll($objClauses);
        }

        public function btnExportButton_Click($strFormId, $strControlId, $strParameter) {
            require_once('NarroProjectImporter.class.php');
            require_once('NarroFileImporter.class.php');
            require_once('NarroMozillaIncFileImporter.class.php');
            require_once('NarroMozillaDtdFileImporter.class.php');
            require_once('NarroMozillaIniFileImporter.class.php');
            require_once('NarroImportStatistics.class.php');
            require_once('NarroLog.class.php');
            require_once('NarroMozilla.class.php');

            $objNarroImporter = new NarroProjectImporter();

            $objNarroImporter->TargetLanguage = QApplication::$objUser->Language;

            $objNarroImporter->SourceLanguage = NarroLanguage::LoadByLanguageCode('en_US');
            if (!$objNarroImporter->SourceLanguage instanceof NarroLanguage) {
                NarroLog::LogMessage(3, sprintf(t('Language %s does not exist in the database.'), 'en_US'));
                return false;
            }
            $objNarroImporter->SourceLanguage->LanguageCode = 'en-US';

            $objNarroImporter->Project = NarroProject::Load($strParameter);
            $objNarroImporter->User = QApplication::$objUser;

            $objNarroImporter->ExportProjectArchive();
            QApplication::Redirect(sprintf('%s/%d/%s-%s.tar.bz2', str_replace(__DOCROOT__, '', __IMPORT_PATH__) , $objNarroImporter->Project->ProjectId, $objNarroImporter->Project->ProjectName, $objNarroImporter->TargetLanguage->LanguageCode));

        }

    }

    NarroProjectListForm::Run('NarroProjectListForm', 'templates/narro_project_list.tpl.php');
?>
