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

    require_once(dirname(__FILE__) . '/configuration/prepend.inc.php');

    class NarroProjectLanguageListForm extends NarroForm {
        protected $dtgNarroLanguage;

        // DataGrid Columns
        protected $colLanguageName;
        protected $colPercentTranslated;

        protected $objProject;

        protected function SetupNarroProject() {
            // Lookup Object PK information from Query String (if applicable)
            // Set mode to Edit or New depending on what's found
            $intProjectId = QApplication::QueryString('p');
            if ($intProjectId > 0) {
                $this->objProject = NarroProject::Load(($intProjectId));

                if (!$this->objProject)
                    QApplication::Redirect(NarroLink::ProjectList());

            } else
                QApplication::Redirect(NarroLink::ProjectList());

        }

        protected function Form_Create() {
            parent::Form_Create();

            $this->SetupNarroProject();

            // Setup DataGrid Columns
            $this->colLanguageName = new QDataGridColumn(t('Language'), '<?= $_FORM->dtgNarroLanguage_LanguageNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroLanguage()->LanguageName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroLanguage()->LanguageName, false)));
            $this->colLanguageName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_FORM->dtgNarroLanguage_PercentTranslated_Render($_ITEM); ?>');
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Width = 160;


            // Setup DataGrid
            $this->dtgNarroLanguage = new NarroDataGrid($this);

            // Datagrid Paginator
            $this->dtgNarroLanguage->Paginator = new QPaginator($this->dtgNarroLanguage);
            $this->dtgNarroLanguage->PaginatorAlternate = new QPaginator($this->dtgNarroLanguage);
            $this->dtgNarroLanguage->ItemsPerPage = QApplication::$User->getPreferenceValueByName('Items per page');
            $this->dtgNarroLanguage->SortColumnIndex = 0;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroLanguage->UseAjax = false;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroLanguage->SetDataBinder('dtgNarroLanguage_Bind');

            $this->dtgNarroLanguage->AddColumn($this->colLanguageName);
            $this->dtgNarroLanguage->AddColumn($this->colPercentTranslated);

            $this->dtgNarroLanguage->SortColumnIndex = 0;

            $this->pnlBreadcrumb->strSeparator = ' | ';

            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectTextList($this->objProject->ProjectId, 1, 1, '', $this->objProject->ProjectName),
                NarroLink::ProjectFileList($this->objProject->ProjectId, null, null, t('Files'))
            );

            if (QApplication::HasPermissionForThisLang('Can import project', $this->objProject->ProjectId))
                $this->pnlBreadcrumb->addElement(NarroLink::ProjectImport($this->objProject->ProjectId, t('Import')));

            if (QApplication::HasPermissionForThisLang('Can export project', $this->objProject->ProjectId))
                $this->pnlBreadcrumb->addElement(NarroLink::ProjectExport($this->objProject->ProjectId, t('Export')));


            if (QApplication::HasPermissionForThisLang('Can edit project', $this->objProject->ProjectId))
                $this->pnlBreadcrumb->addElement(NarroLink::ProjectEdit($this->objProject->ProjectId, t('Edit')));

            $this->pnlBreadcrumb->addElement(t('Languages'));

        }

        public function dtgNarroLanguage_PercentTranslated_Render(NarroLanguage $objNarroLanguage) {
            $sOutput = '';

            $objDatabase = QApplication::$Database[1];

            $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM narro_context c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND c.active=1', $this->objProject->ProjectId, $objNarroLanguage->LanguageId);

            // Perform the Query
            $objDbResult = $objDatabase->Query($strQuery);

            if ($objDbResult) {
                $mixRow = $objDbResult->FetchArray();
                $intTotalTexts = $mixRow['cnt'];

                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM narro_context c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1', $this->objProject->ProjectId, $objNarroLanguage->LanguageId);

                // Perform the Query
                $objDbResult = $objDatabase->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intTranslatedTexts = $mixRow['cnt'];
                }

                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1', $this->objProject->ProjectId, $objNarroLanguage->LanguageId);
                // Perform the Query
                $objDbResult = $objDatabase->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intApprovedTexts = $mixRow['cnt'];
                }

                $objProgressBar = $this->GetControl('progressbar' . $objNarroLanguage->LanguageId);
                if (!$objProgressBar instanceof NarroTranslationProgressBar)
                    $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroLanguage, 'progressbar' . $objNarroLanguage->LanguageId);
                $objProgressBar->Total = $intTotalTexts;
                $objProgressBar->Translated = $intApprovedTexts;
                $objProgressBar->Fuzzy = $intTranslatedTexts;

                $sOutput .= $objProgressBar->Render(false);
            }
            return $sOutput;

        }

        public function dtgNarroLanguage_LanguageNameColumn_Render(NarroLanguage $objNarroLanguage) {
            return sprintf('<a href="%s?l=%s">%s</a>', basename(__FILE__), $objNarroLanguage->LanguageCode, $objNarroLanguage->LanguageName);
        }

        protected function dtgNarroLanguage_Bind() {
            $this->dtgNarroLanguage->TotalItemCount = NarroLanguage::CountAll();

            $this->dtgNarroLanguage->DataSource = NarroLanguage::LoadAllActive(QQ::Clause(
                $this->dtgNarroLanguage->OrderByClause,
                $this->dtgNarroLanguage->LimitClause
            ));

            QApplication::ExecuteJavaScript('highlight_datagrid();');
        }

    }

    NarroProjectLanguageListForm::Run('NarroProjectLanguageListForm');

?>