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

    class NarroProjectListPanel extends QPanel {
        public $dtgProject;

        // DataGrid Columns
        protected $colProjectName;
        protected $colLastActivity;
        protected $colPercentTranslated;

        protected $arrShowTranslators;
        protected $arrShowReviewers;

        public $lstFilter;
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

            $this->strTemplate = __DOCROOT__ . __SUBDIRECTORY__ . '/templates/NarroProjectListPanel.tpl.php';

            // Setup DataGrid Columns
            $this->colProjectName = new QDataGridColumn(t('Name'), '<?= $_CONTROL->ParentControl->dtgProject_ProjectNameColumn_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroProject()->ProjectName, false)));
            $this->colProjectName->HtmlEntities = false;

            $this->colLastActivity = new QDataGridColumn(t('Last activity'), '<?= $_CONTROL->ParentControl->dtgProject_LastActivityColumn_Render($_ITEM) ?>');
            $this->colLastActivity->HtmlEntities = false;

            $this->colPercentTranslated = new QDataGridColumn(t('Progress'), '<?= $_CONTROL->ParentControl->dtgProject_PercentTranslated_Render($_ITEM) ?>', array('OrderByClause' => QQ::OrderBy(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, true, QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, true), 'ReverseOrderByClause' => QQ::OrderBy(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, false, QQN::NarroProject()->NarroProjectProgressAsProject->FuzzyTextCount, false)));
            $this->colPercentTranslated->HtmlEntities = false;
            $this->colPercentTranslated->Wrap = false;

            // Setup DataGrid
            $this->dtgProject = new QDataGrid($this);
            $this->dtgProject->ShowHeader = true;
            $this->dtgProject->Title = t('Projects');

            // Datagrid Paginator
            $this->dtgProject->Paginator = new QPaginator($this->dtgProject);
            $this->dtgProject->PaginatorAlternate = new QPaginator($this->dtgProject);
            $this->dtgProject->ItemsPerPage = NarroApp::$User->getPreferenceValueByName('Items per page');

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgProject->UseAjax = NarroApp::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgProject->SetDataBinder('dtgProject_Bind', $this);

            $this->dtgProject->AddColumn($this->colProjectName);
            $this->dtgProject->AddColumn($this->colLastActivity);
            $this->dtgProject->AddColumn($this->colPercentTranslated);

            $this->dtgProject->SortColumnIndex = 0;



            $this->lstFilter = new QListBox($this);
            $this->lstFilter->AddItem(t('all'), 0);
            $this->lstFilter->AddItem(t('in progress'), 1, true);
            $this->lstFilter->AddItem(t('completed'), 2);
            $this->lstFilter->AddItem(t('empty'), 3);
            if (NarroApp::HasPermission('Administrator'))
                $this->lstFilter->AddItem(t('inactive'), 4);
            if (NarroApp::$UseAjax)
                $this->lstFilter->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'dtgProject_Bind'));
            else
                $this->lstFilter->AddAction(new QChangeEvent(), new QServerControlAction($this, 'dtgProject_Bind'));

            $this->txtSearch = new QTextBox($this);

            $this->btnSearch = new QButton($this);
            $this->btnSearch->Text = t('Search');
            $this->btnSearch->PrimaryButton = true;

            if (NarroApp::$UseAjax)
                $this->btnSearch->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'dtgProject_Bind'));
            else
                $this->btnSearch->AddAction(new QClickEvent(), new QServerControlAction($this, 'dtgProject_Bind'));

        }

        public function dtgProject_LastActivityColumn_Render(NarroProject $objNarroProject) {
            $objLastModifiedContext = NarroContextInfo::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $objNarroProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, NarroApp::GetLanguageId())
                ),
                array(QQ::OrderBy(QQN::NarroContextInfo()->Modified, false))
            );
            if ($objLastModifiedContext instanceof NarroContextInfo) {
                $objDateSpan = new QDateTimeSpan(time() - strtotime($objLastModifiedContext->Modified));
                $strModifiedWhen = $objDateSpan->SimpleDisplay();
                return sprintf(t('%s ago'), $strModifiedWhen);
            }
            else {
                return t('never');
            }
        }

        public function dtgProject_PercentTranslated_Render(NarroProject $objNarroProject) {
            $intTotalTexts = $objNarroProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objNarroProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objNarroProject->CountApprovedTextsByLanguage();

            $objProgressBar = new NarroTranslationProgressBar($this->dtgProject);

            $objProgressBar->Total = $intTotalTexts;
            $objProgressBar->Translated = $intApprovedTexts;
            $objProgressBar->Fuzzy = $intTranslatedTexts;

            $strOutput .= $objProgressBar->Render(false);

            $objActions = new NarroBreadcrumbPanel($this->dtgProject);

            $objActions->strSeparator = ' | ';
            $objActions->CssClass = '';
            $objActions->SetCustomStyle('padding-top', '3px');

            if ($intTotalTexts) {
                $objActions->addElement(NarroLink::ProjectTextList($objNarroProject->ProjectId, 1, 1, '', t('Texts')));
                $objActions->addElement(NarroLink::ProjectFileList($objNarroProject->ProjectId, null, t('Files')));
                $objActions->addElement(sprintf('<a href="narro_project_language_list.php?l=%s&p=%d">%s</a>', NarroApp::$Language->LanguageCode, $objNarroProject->ProjectId, t('Languages')));
            }

            if (NarroApp::HasPermissionForThisLang('Can import project', $objNarroProject->ProjectId))
                $objActions->addElement( NarroLink::ProjectImport($objNarroProject->ProjectId, t('Import')));

            if (NarroApp::HasPermissionForThisLang('Can export project', $objNarroProject->ProjectId))
                $objActions->addElement( NarroLink::ProjectExport($objNarroProject->ProjectId, t('Export')));

            if (NarroApp::HasPermission('Can edit project', $objNarroProject->ProjectId))
                $objActions->addElement( NarroLink::ProjectEdit($objNarroProject->ProjectId, t('Edit')));


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

        public function dtgProject_ProjectNameColumn_Render(NarroProject $objNarroProject) {

            $intTotalTexts = $objNarroProject->CountAllTextsByLanguage();
            $intTranslatedTexts = $objNarroProject->CountTranslatedTextsByLanguage();
            $intApprovedTexts = $objNarroProject->CountApprovedTextsByLanguage();

            if ($objNarroProject->Active)
                $strProjectName = '<span style="font-size:1.2em;font-weight:bold;">' . $objNarroProject->ProjectName . '</span>';
            else
                $strProjectName = '<span style="color:gray;font-style:italic;font-size:1.2em">' . $objNarroProject->ProjectName . '</span>';

            $arrUser = NarroApp::$Cache->load('users_that_review_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId());
            if ($arrUser === false) {
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

            $objLinkMoreReviewers = $this->Form->GetControl('morereviewers' . $objNarroProject->ProjectId);
            if (!$objLinkMoreReviewers) {
                $objLinkMoreReviewers = new QLinkButton($this->dtgProject, 'morereviewers' . $objNarroProject->ProjectId);
                $objLinkMoreReviewers->ActionParameter = $objNarroProject->ProjectId;
                if (NarroApp::$UseAjax)
                    $objLinkMoreReviewers->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'objLinkMoreReviewers_Click'));
                else
                    $objLinkMoreReviewers->AddAction(new QClickEvent(), new QServerControlAction($this, 'objLinkMoreReviewers_Click'));
            }

            if (!isset($this->arrShowReviewers[$objNarroProject->ProjectId]) && count($arrUserLinks) > 4) {
                $objLinkMoreReviewers->Text = ', ...';
                $arrUserLinks = array_slice($arrUserLinks, 0, 4);
            }
            else
                $objLinkMoreReviewers->Text = '';

            if (count($arrUserLinks))
                $strReviewers = '<div style="color:gray;display:block;text-align:left;font-style:italic">' . sprintf(t('Reviewers') . ': %s', join(', ', $arrUserLinks)) . $objLinkMoreReviewers->Render(false) . '</div>';

            $arrUser = NarroApp::$Cache->load('users_that_translated_' . $objNarroProject->ProjectId . '_' . NarroApp::GetLanguageId());
            if ($arrUser === false) {
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

            $objLinkMoreTranslators = $this->Form->GetControl('moretranslators' . $objNarroProject->ProjectId);
            if (!$objLinkMoreTranslators) {
                $objLinkMoreTranslators = new QLinkButton($this->dtgProject, 'moretranslators' . $objNarroProject->ProjectId);
                $objLinkMoreTranslators->ActionParameter = $objNarroProject->ProjectId;
                if (NarroApp::$UseAjax)
                    $objLinkMoreTranslators->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'objLinkMoreTranslators_Click'));
                else
                    $objLinkMoreTranslators->AddAction(new QClickEvent(), new QServerControlAction($this, 'objLinkMoreTranslators_Click'));

                $objLinkMoreTranslators->Text = ', ...';
            }

            if (!isset($this->arrShowTranslators[$objNarroProject->ProjectId]) && count($arrUserLinks) > 4) {
                $objLinkMoreTranslators->Text = ', ...';
                $arrUserLinks = array_slice($arrUserLinks, 0, 4);
            }
            else
                $objLinkMoreTranslators->Text = '';

            if (count($arrUserLinks))
               $strTranslators = '<div style="color:gray;display:block;text-align:left;font-style:italic">' . sprintf(t('Translators') . ': %s', join(', ', $arrUserLinks)) . $objLinkMoreTranslators->Render(false) . '</div>';

            $pnlTranslatorPie = $this->Form->GetControl('translatorspie' . $objNarroProject->ProjectId);
            if (!$pnlTranslatorPie) {
                $pnlTranslatorPie = new QDatabasePieChart($this->dtgProject, 'translatorspie' . $objNarroProject->ProjectId);
                $pnlTranslatorPie->Query = sprintf('
                    SELECT
                        COUNT(narro_suggestion.user_id) AS cnt, narro_user.username AS label
                    FROM
                        narro_context_info, narro_suggestion,narro_context,narro_user
                    WHERE
                        valid_suggestion_id=suggestion_id AND
                        narro_context.context_id=narro_context_info.context_id AND
                        project_id=%d AND
                        narro_context_info.language_id=%d AND
                        narro_context.active=1 AND
                        narro_user.user_id=narro_suggestion.user_id
                    GROUP BY narro_suggestion.user_id
                    ORDER BY cnt DESC',
                    $objNarroProject->ProjectId,
                    NarroApp::GetLanguageId()
                );
                $pnlTranslatorPie->Total = $objNarroProject->CountApprovedTextsByLanguage(NarroApp::GetLanguageId());
                $pnlTranslatorPie->MinimumDataValue = 0;
            }
            $pnlTranslatorPie->Display = isset($this->arrShowTranslators[$objNarroProject->ProjectId]);

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
                $strTranslators . $pnlTranslatorPie->Render(false);
        }

        public function dtgProject_Bind() {
            if ($this->txtSearch->Text != '')
                $objSearchCondition = QQ::Like(QQN::NarroProject()->ProjectName, sprintf('%%%s%%', $this->txtSearch->Text));
            else
                $objSearchCondition = QQ::All();

            switch ($this->lstFilter->SelectedValue) {
                /**
                 * In progress
                 */
                case 1:
                    $objFilterCondition =
                        QQ::AndCondition(
                            $objSearchCondition,
                            QQ::Equal(QQN::NarroProject()->NarroProjectProgressAsProject->LanguageId, NarroApp::GetLanguageId()),
                            QQ::LessThan(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, 100),
                            QQ::GreaterThan(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, 0)
                        );
                    break;
                /**
                 * Completed
                 */
                case 2:
                    $objFilterCondition =
                        QQ::AndCondition(
                            $objSearchCondition,
                            QQ::Equal(QQN::NarroProject()->NarroProjectProgressAsProject->LanguageId, NarroApp::GetLanguageId()),
                            QQ::Equal(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, 100)
                        );
                    break;
                /**
                 * Empty
                 */
                case 3:
                    $objFilterCondition =
                        QQ::AndCondition(
                            $objSearchCondition,
                            QQ::Equal(QQN::NarroProject()->NarroProjectProgressAsProject->LanguageId, NarroApp::GetLanguageId()),
                            QQ::Equal(QQN::NarroProject()->NarroProjectProgressAsProject->ProgressPercent, 0)
                        );
                    break;
                /**
                 * Inactive
                 */
                case 4:
                    $objFilterCondition = QQ::AndCondition($objSearchCondition, QQ::Equal(QQN::NarroProject()->Active, 0));
                    break;
                /**
                 * 0 - show all
                 */
                default:
                    $objFilterCondition = $objSearchCondition;

            }


            // Because we want to enable pagination AND sorting, we need to setup the $objClauses array to send to LoadAll()

            // Remember!  We need to first set the TotalItemCount, which will affect the calcuation of LimitClause below
            $this->dtgProject->TotalItemCount = NarroProject::QueryCount($objFilterCondition);

            // Setup the $objClauses Array
            $objClauses = array();

            // If a column is selected to be sorted, and if that column has a OrderByClause set on it, then let's add
            // the OrderByClause to the $objClauses array
            if ($objClause = $this->dtgProject->OrderByClause)
                array_push($objClauses, $objClause);

            // Add the LimitClause information, as well
            if ($objClause = $this->dtgProject->LimitClause)
                array_push($objClauses, $objClause);

            // Set the DataSource to be the array of all NarroProject objects, given the clauses above
            $this->dtgProject->DataSource = NarroProject::QueryArray($objFilterCondition, $objClauses);

            NarroApp::ExecuteJavaScript('highlight_datagrid();');
        }

        public function objLinkMoreTranslators_Click($strFormId, $strControlId, $strParameter) {
            if (isset($this->arrShowTranslators[$strParameter]))
                unset($this->arrShowTranslators[$strParameter]);
            else
                $this->arrShowTranslators[$strParameter] = 1;

            $this->MarkAsModified();
        }

        public function objLinkMoreReviewers_Click($strFormId, $strControlId, $strParameter) {
            if (isset($this->arrShowReviewers[$strParameter]))
                unset($this->arrShowReviewers[$strParameter]);
            else
                $this->arrShowReviewers[$strParameter] = 1;

            $this->MarkAsModified();
        }

    }
?>
