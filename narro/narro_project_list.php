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

    class NarroProjectListForm extends QForm {
        protected $dtgNarroProject;

        // DataGrid Columns
        protected $colProjectName;
        protected $colPercentTranslated;

        protected $pnlTopUsers;
        protected $pnlNewUsers;

        protected function Form_Create() {
            parent::Form_Create();

            // Setup DataGrid Columns
            $this->colProjectName = new QDataGridColumn(t('Name'), '<?= $_FORM->dtgNarroProject_ProjectNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName, false)));
            $this->colProjectName->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_FORM->dtgNarroProject_PercentTranslated_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, true, QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, true), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, false, QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, false)));
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Wrap = false;

            // Setup DataGrid
            $this->dtgNarroProject = new QDataGrid($this);

            // Datagrid Paginator
            $this->dtgNarroProject->Paginator = new QPaginator($this->dtgNarroProject);
            $this->dtgNarroProject->ItemsPerPage = NarroApp::$User->getPreferenceValueByName('Items per page');

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgNarroProject->UseAjax = false;

            // Specify the local databind method this datagrid will use
            $this->dtgNarroProject->SetDataBinder('dtgNarroProject_Bind');

            $this->dtgNarroProject->AddColumn($this->colProjectName);

            $this->dtgNarroProject->AddColumn($this->colPercentTranslated);

            $this->dtgNarroProject->SortColumnIndex = 0;

            $this->pnlTopUsers = new NarroTopUsersPanel($this);
            $this->pnlNewUsers = new NarroNewUsersPanel($this);

        }

        public function dtgNarroProject_PercentTranslated_Render(NarroProject $objNarroProject) {
            $intTotalTexts = $objNarroProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objNarroProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objNarroProject->CountApprovedTextsByLanguage();

            $objProgressBar = new NarroTranslationProgressBar($this->dtgNarroProject);

            $objProgressBar->Total = $intTotalTexts;
            $objProgressBar->Translated = $intApprovedTexts;
            $objProgressBar->Fuzzy = $intTranslatedTexts;

            $strOutput .= $objProgressBar->Render(false);

            $objActions = new NarroBreadcrumbPanel($this->dtgNarroProject);

            $objActions->strSeparator = ' | ';
            $objActions->CssClass = '';
            $objActions->SetCustomStyle('padding-top', '3px');

            if ($intTotalTexts) {
                $objActions->addElement(NarroLink::ProjectTextList($objNarroProject->ProjectId, 1, 1, '', t('Texts')));
                $objActions->addElement(NarroLink::ProjectFileList($objNarroProject->ProjectId, null, t('Files')));
                $objActions->addElement(sprintf('<a href="narro_project_language_list.php?l=%s&p=%d">%s</a>', NarroApp::$Language->LanguageCode, $objNarroProject->ProjectId, t('Languages')));
            }

            if (NarroApp::HasPermissionForThisLang('Can manage project', $objNarroProject->ProjectId))
                $objActions->addElement(
                    sprintf('<a href="narro_project_manage.php?l=%s&p=%d">%s</a>', NarroApp::$Language->LanguageCode, $objNarroProject->ProjectId, t('Manage'))
                );

            if (NarroApp::HasPermissionForThisLang('Can edit project', $objNarroProject->ProjectId))
                $objActions->addElement(
                    sprintf('<a href="narro_project_edit.php?l=%s&p=%d">%s</a>', NarroApp::$Language->LanguageCode, $objNarroProject->ProjectId, t('Edit'))
                );


            $strOutput =
                NarroLink::ContextSuggest(
                    $objNarroProject->ProjectId,
                    0,
                    0,
                    2,
                    1,
                    '',
                    0,
                    $intTotalTexts - $intApprovedTexts - $intTranslatedTexts,
                    -1,
                    0,
                    $strOutput
                );

            $strOutput .= $objActions->Render(false);

            return $strOutput;
        }

        public function dtgNarroProject_ProjectNameColumn_Render(NarroProject $objNarroProject) {

            $intTotalTexts = $objNarroProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objNarroProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objNarroProject->CountApprovedTextsByLanguage();

            if ($objNarroProject->Active)
                $strProjectName = '<span style="font-size:1.2em">' . $objNarroProject->ProjectName . '</span>';
            else
                $strProjectName = '<span style="color:gray;font-style:italic;font-size:1.2em">' . $objNarroProject->ProjectName . '</span>';

            $arrUser = NarroApp::$Cache->load('users_that_review_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId());
            if (1 || $arrUser === false) {
                $arrUser = NarroUser::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroUser()->NarroUserRoleAsUser->Role->NarroRolePermissionAsRole->Permission->PermissionName, 'Can approve'),
                        QQ::OrCondition(
                            QQ::Equal(QQN::NarroUser()->NarroUserRoleAsUser->ProjectId, $objNarroProject->ProjectId),
                            QQ::IsNull(QQN::NarroUser()->NarroUserRoleAsUser->ProjectId)
                        ),
                        QQ::OrCondition(
                            QQ::Equal(QQN::NarroUser()->NarroUserRoleAsUser->LanguageId, NarroApp::GetLanguageId()),
                            QQ::IsNull(QQN::NarroUser()->NarroUserRoleAsUser->LanguageId)
                        )
                    ),
                    array(QQ::Distinct(), QQ::OrderBy(QQN::NarroUser()->NarroUserRoleAsUser->UserRoleId))
                );

                NarroApp::$Cache->save($arrUser, 'users_that_review_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId(), array(), 3600 * 24);
            }

            $arrUserLinks = array();
            foreach($arrUser as $objUser) {
                $arrUserLinks[] = NarroLink::UserProfile($objUser->UserId, $objUser->Username);
            }

            $strMore = '';
            if (count($arrUserLinks) > 4) {
                $arrUserLinks = array_slice($arrUserLinks, 0, 4);
                $strMore = ', ...';
            }

            if (count($arrUserLinks))
                $strReviewers = '<div style="color:gray;display:block;text-align:left;font-style:italic">' . sprintf(t('Reviewers') . ': %s', join(', ', $arrUserLinks)) . $strMore . '</div>';

            $arrUser = NarroApp::$Cache->load('users_that_translated_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId());
            if (1 || $arrUser === false) {
                $arrUser = NarroUser::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->ProjectId, $objNarroProject->ProjectId),
                        QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, NarroApp::GetLanguageId()),
                        QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID)
                    ),
                    array(QQ::Distinct(), QQ::OrderBy(QQN::NarroUser()->NarroSuggestionAsUser->Created, false))
                );

                NarroApp::$Cache->save($arrUser, 'users_that_translated_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId(), array(), 3600 * 24);
            }

            $arrUserLinks = array();
            foreach($arrUser as $objUser) {
               $arrUserLinks[] = NarroLink::UserProfile($objUser->UserId, $objUser->Username);
            }

            $strMore = '';
            if (count($arrUserLinks) > 4) {
                $arrUserLinks = array_slice($arrUserLinks, 0, 4);
                $strMore = ', ...';
            }

            if (count($arrUserLinks))
               $strTranslators = '<div style="color:gray;display:block;text-align:left;font-style:italic">' . sprintf(t('Translators') . ': %s', join(', ', $arrUserLinks)) . $strMore . '</div>';

            return
                NarroLink::ContextSuggest(
                    $objNarroProject->ProjectId,
                    0,
                    0,
                    2,
                    1,
                    '',
                    0,
                    $intTotalTexts - $intApprovedTexts - $intTranslatedTexts,
                    -1,
                    0,
                    $strProjectName
                ) .
                $strReviewers .
                $strTranslators;
        }

        protected function dtgNarroProject_Bind() {
            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            if (NarroApp::HasPermissionForThisLang('Can manage project', null))
                $this->dtgNarroProject->TotalItemCount = NarroProject::CountAll();
            else
                $this->dtgNarroProject->TotalItemCount = NarroProject::QueryCount(QQ::Equal(QQN::NarroProject()->Active, 1));

            if ($this->dtgNarroProject->TotalItemCount == 0 && NarroApp::HasPermissionForThisLang('Can manage project', null))
                NarroApp::Redirect(sprintf('narro_project_edit.php?l=%s', NarroApp::$Language->LanguageCode));

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
            if (NarroApp::HasPermissionForThisLang('Can manage project', null))
                $this->dtgNarroProject->DataSource = NarroProject::LoadAll($objClauses);
            else
                $this->dtgNarroProject->DataSource = NarroProject::QueryArray(QQ::Equal(QQN::NarroProject()->Active, 1), $objClauses);

            NarroApp::ExecuteJavaScript('highlight_datagrid();');
        }
    }

    NarroProjectListForm::Run('NarroProjectListForm', 'templates/narro_project_list.tpl.php');
?>
