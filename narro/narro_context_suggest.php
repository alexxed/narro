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

    require_once(dirname(__FILE__) . '/configuration/prepend.inc.php');

    class NarroContextSuggestForm extends NarroForm {
        protected $objContextInfo;

        protected $intTextFilter;
        protected $objProject;
        protected $objFile;
        protected $intSearchType;
        protected $strSearchText;
        protected $intCurrentContext;
        protected $intContextsCount;
        protected $intSortColumnIndex;
        protected $intSortDirection;
        protected $blnShowComments;

        protected $pnlMainTab;
        protected $pnlContextSuggest;

        protected function Form_Create() {
            parent::Form_Create();

            $this->SetupNarroContextInfo();

            $this->pnlBreadcrumb->setElements(
                NarroLink::ProjectList(t('Projects')),
                $this->objProject->ProjectName
            );

            $this->pnlMainTab = new QTabPanel($this);
            $this->pnlMainTab->UseAjax = false;

            $this->pnlContextSuggest = new NarroContextSuggestPanel(
                $this->pnlMainTab,
                null,
                $this->objProject,
                $this->objFile,
                $this->objContextInfo,
                $this->intTextFilter,
                $this->intSearchType,
                $this->strSearchText,
                $this->intContextsCount,
                $this->intCurrentContext,
                $this->intSortColumnIndex,
                $this->intSortDirection,
                $this->blnShowComments
            );

            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Overview'), NarroLink::Project($this->objProject->ProjectId));
            if ($this->objProject instanceof NarroProject && QApplication::HasPermission('Can edit project', $this->objProject->ProjectId))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Edit'), NarroLink::ProjectEdit($this->objProject->ProjectId));
            elseif (QApplication::HasPermission('Can add project'))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Add'));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Files'), NarroLink::ProjectFileList($this->objProject->ProjectId));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Texts'), NarroLink::ProjectTextList($this->objProject->ProjectId));
            $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Comments'), NarroLink::ProjectTextCommentList($this->objProject->ProjectId));
            if ($this->intTextFilter == NarroTextListPanel::SHOW_UNTRANSLATED_TEXTS) {
                $this->pnlMainTab->addTab($this->pnlContextSuggest, t('Translate'));
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Review'), NarroLink::ContextSuggest($this->objProject->ProjectId, QApplication::QueryString('f'), null, NarroTextListPanel::SHOW_TEXTS_THAT_REQUIRE_APPROVAL, QApplication::QueryString('st'), QApplication::QueryString('s')));
            }
            else {
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Translate'), NarroLink::ContextSuggest($this->objProject->ProjectId, QApplication::QueryString('f'), null, NarroTextListPanel::SHOW_UNTRANSLATED_TEXTS, QApplication::QueryString('st'), QApplication::QueryString('s')));
                $this->pnlMainTab->addTab($this->pnlContextSuggest, t('Review'));
            }

            if (QApplication::HasPermissionForThisLang('Can import project', $this->objProject->ProjectId))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Import'), NarroLink::ProjectImport($this->objProject->ProjectId));
            if (QApplication::HasPermissionForThisLang('Can export project', $this->objProject->ProjectId))
                $this->pnlMainTab->addTab(new QPanel($this->pnlMainTab), t('Export'), NarroLink::ProjectExport($this->objProject->ProjectId));


            if ($this->intTextFilter == NarroTextListPanel::SHOW_UNTRANSLATED_TEXTS)
                $this->pnlMainTab->SelectedTab = t('Translate');
            else
                $this->pnlMainTab->SelectedTab = t('Review');
        }

        protected function SetupNarroContextInfo() {

            // Lookup Object PK information from Query String (if applicable)
            $intContextId = QApplication::QueryString('c');

            $this->intTextFilter = QApplication::QueryString('tf');
            $this->objProject = NarroProject::Load(QApplication::QueryString('p'));
            $this->objFile = NarroFile::Load(QApplication::QueryString('f'));
            $this->intSearchType = QApplication::QueryString('st');
            $this->strSearchText = QApplication::QueryString('s');

            $this->intCurrentContext = QApplication::QueryString('ci');
            $this->intContextsCount = QApplication::QueryString('cc');
            $this->intSortColumnIndex = QApplication::QueryString('o');
            $this->intSortDirection = QApplication::QueryString('a');
            $this->blnShowComments = QApplication::QueryString('sc');

            if (!$this->objProject && !$this->objFile) {
                QApplication::Redirect(NarroLink::ProjectList());
            }

            if ($intContextId) {
                $this->objContextInfo = NarroContextInfo::LoadByContextIdLanguageId($intContextId, QApplication::GetLanguageId());
            }

            if (!$intContextId || !$this->objContextInfo instanceof NarroContextInfo) {
                if ($this->objFile instanceof NarroFile)
                    $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objFile->FileId);
                else
                    $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objProject->ProjectId);

                $objExtraCondition = QQ::AndCondition(
                    QQ::GreaterThan(QQN::NarroContextInfo()->ContextId, 1),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::GetLanguageId()),
                    $objFilterCodition,
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
                );

                if
                (
                    $objContext = NarroContextInfo::GetContext(
                        1,
                        $this->strSearchText,
                        $this->intSearchType,
                        $this->intTextFilter,
                        QQ::OrderBy(array(QQN::NarroContextInfo()->ContextId, true)),
                        $objExtraCondition
                    )
                )
                {
                    QApplication::Redirect(
                        NarroLink::ContextSuggest(
                            $this->objProject->ProjectId,
                            ($this->objFile instanceof NarroFile)?$this->objFile->FileId:null,
                            $objContext->ContextId,
                            $this->intTextFilter,
                            $this->intSearchType,
                            $this->strSearchText,
                            null,
                            QApplication::QueryString('cc'),
                            null,
                            null,
                            QApplication::QueryString('sc')

                        )
                    );
                }
                elseif ($this->objFile instanceof NarroFile)
                    QApplication::Redirect(NarroLink::FileTextList($this->objProject->ProjectId, $this->objFile->FileId, $this->intTextFilter, $this->intSearchType, $this->strSearchText ));
                elseif ($this->objProject->ProjectId)
                    QApplication::Redirect(NarroLink::ProjectTextList($this->objProject->ProjectId, $this->intTextFilter, $this->intSearchType, $this->strSearchText));
                else
                    QApplication::Redirect(NarroLink::ProjectList());
            }
        }
    }

    NarroContextSuggestForm::Run('NarroContextSuggestForm');

