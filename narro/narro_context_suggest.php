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
    require_once('includes/narro/narro_suggestion_list_panel.class.php');
    require_once('includes/narro/narro_diacritics_panel.class.php');

    class NarroContextSuggestForm extends QForm {

        protected $pnlNavigator;

        // Button Actions
        protected $btnSave;
        protected $btnNext;
        protected $btnNext100;
        protected $btnPrevious100;
        protected $btnPrevious;

        protected $chkGoToNext;
        protected $chkIgnoreSpellcheck;
        protected $chkValidate;

        protected $objNarroContextInfo;
        protected $pnlOriginalText;
        protected $pnlContext;
        public $txtSuggestionValue;
        protected $txtSuggestionComment;

        protected $pnlSuggestionList;
        protected $lblMessage;

        protected $intTextFilter;
        protected $intProjectId;
        protected $intFileId;
        protected $intSearchType;
        protected $strSearchText;

        protected $pnlSpellcheckText;
        protected $pnlDiacritics;

        protected function SetupNarroContextInfo() {

            // Lookup Object PK information from Query String (if applicable)
            $intContextId = QApplication::QueryString('c');

            $this->intTextFilter = QApplication::QueryString('tf');
            $this->intProjectId = QApplication::QueryString('p');
            $this->intFileId = QApplication::QueryString('f');
            $this->intSearchType = QApplication::QueryString('st');
            $this->strSearchText = QApplication::QueryString('s');

            if (!$this->intProjectId && !$this->intFileId) {
                QApplication::Redirect('narro_project_list.php');
            }

            if ($intContextId) {
                $this->objNarroContextInfo = NarroContextInfo::LoadByContextIdLanguageId($intContextId, QApplication::$objUser->Language->LanguageId);
            }

            if (!$intContextId || !$this->objNarroContextInfo instanceof NarroContextInfo) {
                if ($this->intFileId)
                    $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->intFileId);
                else
                    $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId);

                $objExtraCondition = QQ::AndCondition(
                    QQ::GreaterThan(QQN::NarroContextInfo()->ContextId, 1),
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
                    $strCommonUrl = sprintf('p=%d&c=%d&tf=%d&st=%d&s=%s&is=%d&gn=%d', $this->intProjectId, $objContext->ContextId, $this->intTextFilter, $this->intSearchType, $this->strSearchText, 0, 1);
                    if ($this->intFileId)
                        QApplication::Redirect('narro_context_suggest.php?' . $strCommonUrl . sprintf( '&f=%d', $this->intFileId));
                    else
                        QApplication::Redirect('narro_context_suggest.php?' . $strCommonUrl);
                }
                elseif ($this->intFileId)
                    QApplication::Redirect('narro_file_text_list.php?' . sprintf('p=%d&f=%d&tf=%d&s=%s', $this->intProjectId, $this->intFileId, $this->intTextFilter, $this->strSearchText ));
                elseif ($this->intProjectId)
                    QApplication::Redirect('narro_project_text_list.php?' . sprintf('p=%d&tf=%d&s=%s', $this->intProjectId, $this->intTextFilter, $this->strSearchText ));
                else
                    QApplication::Redirect('narro_project_list.php?');
            }
        }

        protected function Form_Create() {
            $this->SetupNarroContextInfo();
            $this->intTextFilter = $this->intTextFilter;

            $this->pnlOriginalText_Create();

            // Create/Setup Button Action controls
            $this->btnSave_Create();
            $this->btnNext_Create();
            $this->btnNext100_Create();
            $this->btnPrevious100_Create();
            $this->btnPrevious_Create();
            $this->chkValidate_Create();
            $this->chkGoToNext_Create();
            $this->chkIgnoreSpellcheck_Create();

            $this->pnlContext_Create();
            $this->txtSuggestionValue_Create();

            $this->pnlSuggestionList = new NarroSuggestionListPanel($this);
            $this->pnlSuggestionList->ToolTip = QApplication::Translate('Other suggestions so far');

            $this->lblMessage = new QLabel($this);
            $this->lblMessage->ForeColor = 'green';
            $this->pnlSpellcheckText = new QPanel($this);
            $this->pnlSpellcheckText->BorderWidth = 0;
            $this->pnlSpellcheckText->BorderStyle = QBorderStyle::None;
            $this->pnlSpellcheckText->Visible = false;
            $this->pnlSpellcheckText->SetCustomStyle('padding', '5px');

            $this->pnlNavigator = new QPanel($this);
            $this->UpdateNavigator();

            $this->UpdateData();

            $this->pnlDiacritics = new NarroDiacriticsPanel($this);
            $this->pnlDiacritics->strTextareaControlId = $this->txtSuggestionValue->ControlId;

        }

        // Protected Create Methods

        // Create and Setup pnlOriginalText
        protected function pnlOriginalText_Create() {
            $this->pnlOriginalText = new QPanel($this);
            $this->pnlOriginalText->ToolTip = QApplication::Translate('Original text');
            $this->pnlOriginalText->FontBold = true;
            $this->pnlOriginalText->DisplayStyle = QDisplayStyle::Inline;
            //$this->pnlOriginalText->AddAction(new QChangeEvent(), new QJavascriptAction(sprintf("var sTitle=document.getElementById('%s');if (sTitle.innerHTML) document.title='%s „' + ((sTitle.innerHTML.length>30)?sTitle.innerHTML.slice(0,3) + '...':sTitle.innerHTML) + '”';", $this->pnlOriginalText->ControlId, (QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))?'Traduceţi ':'Vedeţi sugestii pentru ')));

        }

        // Create and Setup pnlContext
        protected function pnlContext_Create() {
            $this->pnlContext = new QPanel($this);
            $this->pnlContext->ToolTip = QApplication::Translate('Details about the place where the text is used');
        }

        // Update values from objNarroContextInfo
        protected function UpdateData() {
            $this->pnlOriginalText->Text = htmlspecialchars($this->objNarroContextInfo->Context->Text->TextValue,null,'utf-8');
            $this->pnlContext->Text = nl2br(htmlspecialchars($this->objNarroContextInfo->Context->Context,null,'utf-8'));
            $this->pnlSuggestionList->NarroContextInfo = $this->objNarroContextInfo;
            //$this->txtSuggestionComment->Text = '';
            $this->txtSuggestionValue->Text = '';

            $this->chkIgnoreSpellcheck->Checked = false;
            $this->chkValidate->Checked = false;

            $this->lblMessage->Text = '';

            $this->ClearSpellCheck();

        }

        protected function UpdateNavigator() {
            $this->pnlNavigator->Text =
            sprintf('<a href="%s">'.QApplication::Translate('Projects').'</a>', 'narro_project_list.php') .
            sprintf(' -> <a href="%s">%s</a>',
                'narro_project_text_list.php?' . sprintf('p=%d&tf=%d&st=%d&s=%s',
                    $this->objNarroContextInfo->Context->File->Project->ProjectId,
                    $this->intTextFilter,
                    QApplication::QueryString('st'),
                    $this->strSearchText
                ),
                $this->objNarroContextInfo->Context->File->Project->ProjectName
                ) .
            sprintf(' -> <a href="%s">'.QApplication::Translate('Files').'</a>',
                'narro_project_file_list.php?' . sprintf('p=%d&tf=%d',
                    $this->objNarroContextInfo->Context->File->Project->ProjectId,
                    $this->intTextFilter
                )) .
            sprintf(' -> <a href="%s">%s</a>',
                'narro_file_text_list.php?' . sprintf('f=%d&tf=%d&st=%d&s=%s',
                    $this->objNarroContextInfo->Context->FileId,
                    $this->intTextFilter,
                    QApplication::QueryString('st'),
                    $this->strSearchText
                ),
                $this->objNarroContextInfo->Context->File->FileName
            );


            $strFilter = '';
            switch ($this->intTextFilter) {
                case NarroTextListForm::SHOW_UNTRANSLATED_TEXTS:
                        $this->pnlNavigator->Text .= ' -> ' . QApplication::Translate('Untranslated texts');
                        break;
                case NarroTextListForm::SHOW_VALIDATED_TEXTS:
                        $this->pnlNavigator->Text .= ' -> ' . QApplication::Translate('Validated texts');
                        break;
                case NarroTextListForm::SHOW_TEXTS_THAT_REQUIRE_VALIDATION:
                        $this->pnlNavigator->Text .= ' -> ' . QApplication::Translate('Texts that require validation');
                        break;
                default:

            }

            if ($this->strSearchText != ''){
                switch ($this->intSearchType) {
                    case NarroTextListForm::SEARCH_TEXTS:
                        $this->pnlNavigator->Text .= ' -> ' . sprintf(QApplication::Translate('Search in original texts for "%s"'), $this->strSearchText);
                        break;
                    case NarroTextListForm::SEARCH_SUGGESTIONS:
                        $this->pnlNavigator->Text .= ' -> ' . sprintf(QApplication::Translate('Search in suggestions for "%s"'), $this->strSearchText);
                        break;
                    case NarroTextListForm::SEARCH_CONTEXTS:
                        $this->pnlNavigator->Text .= ' -> ' . sprintf(QApplication::Translate('Search in contexts for "%s"'), $this->strSearchText);
                        break;
                    default:
                }
            }
            else {
                $strSearchType = '';
            }

            $strText = htmlspecialchars($this->objNarroContextInfo->Context->Text->TextValue,null,'utf-8');
            $strPageTitle =
                sprintf((QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))?QApplication::Translate('Translate "%s"'):QApplication::Translate('See suggestions for "%s"'),
                (strlen($this->objNarroContextInfo->Context->Text->TextValue)>30)?substr($strText, 0, 30) . '...':$strText);

            $this->pnlNavigator->Text .=  ' -> ' . $strPageTitle;
            $this->pnlNavigator->MarkAsModified();
        }

        // Create and Setup chkGoToNext
        protected function chkGoToNext_Create() {
            $this->chkGoToNext = new QCheckBox($this);
            $this->chkGoToNext->Checked = (bool) QApplication::QueryString('gn');
        }

        // Create and Setup chkValidate
        protected function chkValidate_Create() {
            $this->chkValidate = new QCheckBox($this);
        }

        // Create and Setup chkIgnoreSpellcheck
        protected function chkIgnoreSpellcheck_Create() {
            $this->chkIgnoreSpellcheck = new QCheckBox($this);
            //$this->chkIgnoreSpellcheck->Checked = (bool) QApplication::QueryString('is');
        }

        // Create and Setup txtSuggestionValue
        protected function txtSuggestionValue_Create() {
            $this->txtSuggestionValue = new QTextBox($this);
            $this->txtSuggestionValue->Text = '';
            //$this->txtSuggestionValue->BorderStyle = QBorderStyle::None;
            $this->txtSuggestionValue->CssClass = 'green3dbg';
            $this->txtSuggestionValue->Width = '100%';
            $this->txtSuggestionValue->Height = 85;
            $this->txtSuggestionValue->Required = true;
            $this->txtSuggestionValue->TextMode = QTextMode::MultiLine;
            $this->txtSuggestionValue->TabIndex = 1;
            $this->txtSuggestionValue->CrossScripting = QCrossScripting::Allow;
            //$this->txtSuggestionValue->Display = QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId);
            //$this->txtSuggestionValue->AddAction(new QKeyUpEvent(), new QJavaScriptAction(sprintf("document.getElementById('%s').style.display=(this.value!='')?'inline':'none';document.getElementById('%s_div').style.display=(this.value!='')?'block':'none'", $this->btnSave->ControlId, $this->txtSuggestionComment->ControlId)));
        }

        // Create and Setup txtSuggestionValueMd5
        protected function txtSuggestionComment_Create() {
            $this->txtSuggestionComment = new QTextBox($this);
            $this->txtSuggestionComment->TextMode = QTextMode::MultiLine;
            //$this->txtSuggestionComment->BorderStyle = QBorderStyle::None;
            $this->txtSuggestionComment->Name = QApplication::Translate('Suggestion comment (optional):');
            $this->txtSuggestionComment->Width = 465;
            $this->txtSuggestionComment->Height = 85;
            $this->txtSuggestionComment->Text = '';
            $strOrigText = $this->objNarroContextInfo->Context->Text;
            //$this->txtSuggestionComment->MaxLength = strlen($strOrigText) + ceil(20 * strlen($strOrigText) / 100 );
            $this->txtSuggestionComment->TabIndex = 2;
            $this->txtSuggestionComment->Display = QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId);
        }

        // Setup btnSave
        protected function btnSave_Create() {
            $this->btnSave = new QButton($this);
            $this->btnSave->Text = QApplication::Translate('Save');
            if (QApplication::$blnUseAjax)
                $this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));
            else
                $this->btnSave->AddAction(new QClickEvent(), new QServerAction('btnSave_Click'));
            //$this->btnSave->PrimaryButton = true;
            $this->btnSave->CausesValidation = true;
            $this->btnSave->TabIndex = 3;
            $this->btnSave->Display = QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId);
            //$this->btnSave->DisplayStyle = QDisplayStyle::None;
        }

        // Setup btnSaveIgnore
        protected function btnSaveIgnore_Create() {
            $this->btnSaveIgnore = new QButton($this);
            $this->btnSaveIgnore->Text = QApplication::Translate('Ignore and save');
            if (QApplication::$blnUseAjax)
                $this->btnSaveIgnore->AddAction(new QClickEvent(), new QAjaxAction('btnSaveIgnore_Click'));
            else
                $this->btnSaveIgnore->AddAction(new QClickEvent(), new QServerAction('btnSaveIgnore_Click'));
            $this->btnSaveIgnore->CausesValidation = true;
            $this->btnSaveIgnore->TabIndex = 3;
            $this->btnSaveIgnore->Visible = false;
            $this->btnSaveIgnore->Display = QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId);
        }

        // Setup btnSaveValidate
        protected function btnSaveValidate_Create() {
            $this->btnSaveValidate = new QButton($this);
            $this->btnSaveValidate->Text = QApplication::Translate('Save and validate');
            if (QApplication::$blnUseAjax)
                $this->btnSaveValidate->AddAction(new QClickEvent(), new QAjaxAction('btnSaveValidate_Click'));
            else
                $this->btnSaveValidate->AddAction(new QClickEvent(), new QServerAction('btnSaveValidate_Click'));
            $this->btnSaveValidate->CausesValidation = true;
            $this->btnSaveValidate->TabIndex = 7;
            $this->btnSaveValidate->Visible = true;
            $this->btnSaveValidate->Display = QApplication::$objUser->hasPermission('Can validate', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId);
        }

        // Setup btnNext
        protected function btnNext_Create() {
            $this->btnNext = new QButton($this);
            $this->btnNext->Text = QApplication::Translate('Next');

            if (QApplication::$blnUseAjax)
                $this->btnNext->AddAction(new QClickEvent(), new QAjaxAction('btnNext_Click'));
            else
                $this->btnNext->AddAction(new QClickEvent(), new QServerAction('btnNext_Click'));

            $this->btnNext->CausesValidation = false;
            $this->btnNext->TabIndex = 4;
        }

        // Setup btnNext100
        protected function btnNext100_Create() {
            $this->btnNext100 = new QButton($this);
            $this->btnNext100->Text = QApplication::Translate('100 forward');

            if (QApplication::$blnUseAjax)
                $this->btnNext100->AddAction(new QClickEvent(), new QAjaxAction('btnNext100_Click'));
            else
                $this->btnNext100->AddAction(new QClickEvent(), new QServerAction('btnNext100_Click'));
            $this->btnNext100->CausesValidation = false;
            $this->btnNext100->TabIndex = 5;
        }

        // Setup btnPrevious100
        protected function btnPrevious100_Create() {
            $this->btnPrevious100 = new QButton($this);
            $this->btnPrevious100->Text = QApplication::Translate('100 back');
            if (QApplication::$blnUseAjax)
                $this->btnPrevious100->AddAction(new QClickEvent(), new QAjaxAction('btnPrevious100_Click'));
            else
                $this->btnPrevious100->AddAction(new QClickEvent(), new QServerAction('btnPrevious100_Click'));
            $this->btnPrevious100->CausesValidation = false;
            $this->btnPrevious100->TabIndex = 6;
        }


        // Setup btnPrevious
        protected function btnPrevious_Create() {
            $this->btnPrevious = new QButton($this);
            $this->btnPrevious->Text = QApplication::Translate('Previous');
            if (QApplication::$blnUseAjax)
                $this->btnPrevious->AddAction(new QClickEvent(), new QAjaxAction('btnPrevious_Click'));
            else
                $this->btnPrevious->AddAction(new QClickEvent(), new QServerAction('btnPrevious_Click'));
            $this->btnPrevious->CausesValidation = false;
            $this->btnPrevious->TabIndex = 6;
        }

        protected function EntitityCheck() {
            /**
            if ($this->txtSuggestionValue->Text == $this->objNarroContextInfo->Context->Text->TextValue) {
                $this->pnlSpellcheckText->Text =
                        '<span style="color:red">' .
                        QApplication::Translate('The suggestion is identical to the text. If you think the text needs no translation, move on.') .
                        '</span>';
                $this->pnlSpellcheckText->Visible = true;
                return false;
            }
            */

            if ($this->chkIgnoreSpellcheck->Checked) return $this->ClearSpellCheck();

            $strOriginalText = $this->objNarroContextInfo->Context->Text->TextValue;

            preg_match_all('/%[Ssd]/', $strOriginalText, $arrPoMatches);
            preg_match_all('/[\$\[\#\%]{1,3}[a-zA-Z\_\-0-9]+[\$\]\#\%]{0,3}[\s\.\;$]/', $strOriginalText, $arrMatches);
            preg_match_all('/&[a-zA-Z\-0-9]+\;/', $strOriginalText, $arrMoz1Matches);
            preg_match_all('/\%[0-9]\$S/', $strOriginalText, $arrMoz2Matches);
            if (is_array($arrPoMatches[0])) {
                $arrMatches[0] = array_merge($arrMatches[0], $arrPoMatches[0]);
                $arrMatches[0] = array_unique($arrMatches[0]);
            }
            if (is_array($arrMoz1Matches[0])) {
                $arrMatches[0] = array_merge($arrMatches[0], $arrMoz1Matches[0]);
                $arrMatches[0] = array_unique($arrMatches[0]);
            }
            if (is_array($arrMoz2Matches[0])) {
                $arrMatches[0] = array_merge($arrMatches[0], $arrMoz2Matches[0]);
                $arrMatches[0] = array_unique($arrMatches[0]);
            }

            if (isset($arrMatches[0]) && count($arrMatches[0])) {
                foreach($arrMatches[0] as $strMatch)
                    if (strpos($this->txtSuggestionValue->Text, trim($strMatch)) === false)
                        $arrDiff[] = htmlspecialchars(trim($strMatch), null, 'utf-8');
                if ($arrDiff) {
                    $this->pnlSpellcheckText->Text =
                        sprintf(
                            QApplication::Translate(
                                '<span style="color:red">You translated or forgot some variables that should have been left as they were. <br /> These are: %s</span><br /> If you think this is a mistake, check "%s" and then press "%s"<br />If you wish to correct and check again, correct the text and press "%s" again.'),
                            join(', ', $arrDiff),
                            QApplication::Translate('Ignore spellchecking'),
                            QApplication::Translate('Save'),
                            QApplication::Translate('Save')
                        );
                    $this->pnlSpellcheckText->Visible = true;
                    return false;
                }
            }

            preg_match('/[\.\!\?\:]+$/', $strOriginalText, $arrOriginalTextMatches);
            preg_match('/[\.\!\?\:]+$/', $this->txtSuggestionValue->Text, $arrSuggestionMatches);

            if (isset($arrOriginalTextMatches[0]) && !isset($arrSuggestionMatches[0])) {
                    $this->pnlSpellcheckText->Text =
                        sprintf(
                            QApplication::Translate('
                                <span style="color:red">
                                Did you forget the ending "%s"?</span><br />
                                If you think you didn\'t, check "%s" and press "%s".<br />If you wish to correct and check again, correct the text and press "%s" again.'
                            ),
                        $arrOriginalTextMatches[0],
                        QApplication::Translate('Ignore spellchecking'),
                        QApplication::Translate('Save'),
                        QApplication::Translate('Save')

                        );
                    $this->pnlSpellcheckText->Visible = true;
                    return false;
            }
            elseif (!isset($arrOriginalTextMatches[0]) && isset($arrSuggestionMatches[0])) {
                    $this->pnlSpellcheckText->Text =
                        sprintf(
                            QApplication::Translate('
                                <span style="color:red">
                                The original text does not end with "%s".</span><br />
                                If you think this is a mistake, check "%s" and then press "%s"<br />If you wish to correct and check again, correct the text and press "%s" again.'
                            ),
                            $arrSuggestionMatches[0],
                            QApplication::Translate('Ignore spellchecking'),
                            QApplication::Translate('Save'),
                            QApplication::Translate('Save')
                        );
                    $this->pnlSpellcheckText->Visible = true;
                    return false;

            }
            elseif (isset($arrOriginalTextMatches[0]) && isset($arrSuggestionMatches[0]) && $arrOriginalTextMatches[0] != $arrSuggestionMatches[0]) {
                    $this->pnlSpellcheckText->Text =
                        sprintf(
                            QApplication::Translate('
                                <span style="color:red">
                                The original text ends with "%s", but your suggestion ends in "%s".</span><br />
                                If you think this is a mistake, check "%s" and then press "%s"<br />If you wish to correct and check again, correct the text and press "%s" again.'
                            ),
                            $arrOriginalTextMatches[0],
                            $arrSuggestionMatches[0],
                            QApplication::Translate('Ignore spellchecking'),
                            QApplication::Translate('Save'),
                            QApplication::Translate('Save')
                        );
                    $this->pnlSpellcheckText->Visible = true;
                    return false;

            }

            if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $this->txtSuggestionValue->Text, $arrMatches)) {

                    $strCorrectedText = $this->txtSuggestionValue->Text;
                    foreach($arrMatches[0] as $intKey=>$strTextWithQuotes) {
                        $strCorrectedText = str_replace($strTextWithQuotes, '„' . $arrMatches[1][$intKey] . '”', $strCorrectedText);
                    }
                    $this->pnlSpellcheckText->Text =
                        '<span style="color:red">' .
                        'Se pare că aţi folosit ghilimele englezeşti. <br />
                        <span style="color:green">Vă rugăm să folosiţi ghilimele româneşti reprezentate prin simbolurile „ (99 jos) şi ” (99 sus). <br />Dacă nu le puteţi introduce de la tastatură, le puteţi introduce cu un clic pe ele de sub textul sugestiei.</span></span><br />' .
                        'Iată textul corectat cu ghilimele româneşti:' .
                        '<div style="border:1px dotted green;padding:5px; margin:5px;">' . $strCorrectedText . '</div>' .
                        'Dacă credeţi că este o greşeală, puteţi salva totuşi sugestia bifând „Ignoră ortografia” şi apoi apăsând „Salvează”<br />Dacă doriţi să corectaţi şi să verificaţi din nou, corectaţi şi apăsaţi „Salvează”';
                    $this->pnlSpellcheckText->Visible = true;
                    return false;
            }

            return true;
        }

        protected function ClearSpellCheck() {
            $this->pnlSpellcheckText->Visible = false;
            $this->pnlSpellcheckText->Text = '';
            return true;
        }

        protected function Spellcheck() {
            if ($this->chkIgnoreSpellcheck->Checked) return $this->ClearSpellCheck();
            $strSuggestionValue = QApplication::$objPluginHandler->ProcessSuggestion($this->txtSuggestionValue->Text);
            if (!$strSuggestionValue)
                $strSuggestionValue = $this->txtSuggestionValue->Text;

            $arrTextSuggestions = QApplication::GetSpellSuggestions($strSuggestionValue);
            $strSpellcheckText = '';

            if (is_array($arrTextSuggestions))
                foreach($arrTextSuggestions as $strWord=>$arrSuggestions) {

                    $strSpellcheckText .= '<b>' . $strWord . '</b> ' . QApplication::Translate('is mispelled') . '<br />';

                    if (count($arrSuggestions)) {
                        $strSpellcheckText .= ' ' . QApplication::Translate('Maybe you meant') . ' <b>';
                        $strSpellcheckText .= join(', ', $arrSuggestions) . '</b>';
                    }

                    $strSpellcheckText .= '<br />';
                }

            if ($strSpellcheckText != '') {
                $this->pnlSpellcheckText->Text = QApplication::Translate('You seem to have a few spellchecking errors:'). '<br /><div style="margin-left:15px;color:red;padding:5px;border:1px dotted black">' .
                                                    $strSpellcheckText. '</div><br /> '. sprintf(QApplication::Translate('If you think this is a mistake, you can still save the suggestion by checking "%s" and pressing "%s".'), QApplication::Translate('Ignore spellchecking'), QApplication::Translate('Save')) . '<br />' . sprintf(QApplication::Translate('If you want to correct and check again, please correct and press "%s"'), QApplication::Translate('Save'));
                $this->pnlSpellcheckText->Visible = true;
                return false;
            }
            else {
                return $this->ClearSpellCheck();
            }
        }

        // Control ServerActions
        protected function btnSaveIgnore_Click($strFormId, $strControlId, $strParameter) {
            $this->ClearSpellCheck();
            $this->SaveSuggestion();
        }

        protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
            if ($this->EntitityCheck() && $this->Spellcheck())
                $this->SaveSuggestion();
        }

        protected function btnSaveValidate_Click($strFormId, $strControlId, $strParameter) {
            if ($this->EntitityCheck() && $this->Spellcheck())
                $this->SaveSuggestion();
        }

        protected function SaveSuggestion() {

            $blnValidate = $this->chkValidate->Checked;

            if (!QApplication::$objUser->hasPermission('Can suggest', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))
                return false;

            $objSuggestion = new NarroSuggestion();
            $objSuggestion->UserId = QApplication::$objUser->UserId;
            $objSuggestion->LanguageId = QApplication::$objUser->Language->LanguageId;
            $objSuggestion->TextId = $this->objNarroContextInfo->Context->TextId;
            $strSuggestionValue = QApplication::$objPluginHandler->ProcessSuggestion($this->txtSuggestionValue->Text);
            if (!$strSuggestionValue)
                $strSuggestionValue = $this->txtSuggestionValue->Text;

            $objSuggestion->SuggestionValue = $strSuggestionValue;
            $objSuggestion->SuggestionValueMd5 = md5($strSuggestionValue);
            $objSuggestion->SuggestionCharCount = mb_strlen($strSuggestionValue);

            try {
                $objSuggestion->Save();
            } catch (QMySqliDatabaseException $objExc) {
                if (strpos($objExc->getMessage(), 'Duplicate entry') === false) {
                    throw $objExc;
                }
                else {
                    $this->btnNext_Click($this->FormId, null, null);
                    /**
                    $this->pnlSuggestionList->lstSuggestion->SelectedValue = $objSuggestion->SuggestionId;
                    $this->pnlSuggestionList->btnVote_Click(0,0,0);
                    */
                }
            }

            $arrNarroText = NarroText::QueryArray(QQ::Equal(QQN::NarroText()->TextValue, $this->objNarroContextInfo->Context->Text->TextValue));
            if (count($arrNarroText)) {
                foreach($arrNarroText as $objNarroText) {
                    $arrNarroContextInfo = NarroContextInfo::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroContextInfo()->LanguageId, QApplication::$objUser->Language->LanguageId), QQ::Equal(QQN::NarroContextInfo()->Context->TextId, $objNarroText->TextId), QQ::Equal(QQN::NarroContextInfo()->HasSuggestions, 0)));
                        if (count($arrNarroContextInfo)) {
                            foreach($arrNarroContextInfo as $objNarroContextInfo) {
                                $objNarroContextInfo->HasSuggestions = 1;
                                if (QApplication::$objUser->hasPermission('Can validate', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) && $blnValidate && $this->objNarroContextInfo->ContextId == $objNarroContext->ContextId)
                                    $objNarroContextInfo->ValidSuggestionId = $objSuggestion->SuggestionId;
                                $objNarroContextInfo->Save();
                            }
                        }

                }
            }

            $this->objNarroContextInfo->HasSuggestions = 1;
            if (QApplication::$objUser->hasPermission('Can validate', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId) && $blnValidate && $this->objNarroContextInfo->ValidSuggestionId != $objSuggestion->SuggestionId) {
                $this->objNarroContextInfo->ValidSuggestionId = $objSuggestion->SuggestionId;
                $this->objNarroContextInfo->Save();
            }


            if ($this->txtSuggestionComment && trim($this->txtSuggestionComment->Text) != '' && $objSuggestion->SuggestionId) {
                $objSuggestionComment = new NarroSuggestionComment();
                $objSuggestionComment->SuggestionId = $objSuggestion->SuggestionId;
                $objSuggestionComment->CommentText = trim($this->txtSuggestionComment->Text);
                $objSuggestionComment->Save();
            }
            if ($this->chkGoToNext->Checked) {
                $this->btnNext_Click();
            }
            elseif(QApplication::$blnUseAjax)
                $this->UpdateData();

            return true;
        }

        protected function btnPrevious_Click($strFormId, $strControlId, $strParameter) {
            if ($this->intFileId)
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->intFileId);
            else
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId);

            $objExtraCondition = QQ::AndCondition(
                                    QQ::LessThan(QQN::NarroContextInfo()->ContextId, $this->objNarroContextInfo->ContextId),
                                    $objFilterCodition,
                                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
            );

            if
            (
                $objContext = NarroContextInfo::GetContext(
                                                    $this->objNarroContextInfo->ContextId,
                                                    $this->strSearchText,
                                                    $this->intSearchType,
                                                    $this->intTextFilter,
                                                    QQ::OrderBy(array(QQN::NarroContextInfo()->ContextId, false)),
                                                    $objExtraCondition
                )
            )
            {
                $this->btnNext->Visible = true;
                $this->btnNext100->Visible = true;
                $this->GoToContext($objContext);
            }
            else {
                $this->btnPrevious->Visible = false;
                $this->btnPrevious100->Visible = false;
            }
        }

        protected function btnNext_Click($strFormId, $strControlId, $strParameter) {
            if ($this->intFileId)
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->intFileId);
            else
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId);

            $objExtraCondition = QQ::AndCondition(
                                    QQ::GreaterThan(QQN::NarroContextInfo()->ContextId, $this->objNarroContextInfo->ContextId),
                                    $objFilterCodition,
                                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
            );

            if
            (
                $objContext = NarroContextInfo::GetContext(
                                                    $this->objNarroContextInfo->ContextId,
                                                    $this->strSearchText,
                                                    $this->intSearchType,
                                                    $this->intTextFilter,
                                                    QQ::OrderBy(array(QQN::NarroContextInfo()->ContextId, true)),
                                                    $objExtraCondition
                )
            )
            {
                $this->btnPrevious->Visible = true;
                $this->btnPrevious100->Visible = true;
                $this->GoToContext($objContext);
            }
            else {
                $this->btnNext->Visible = false;
                $this->btnNext100->Visible = false;
            }

        }

        protected function btnNext100_Click($strFormId, $strControlId, $strParameter) {
            if ($this->intFileId)
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->intFileId);
            else
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId);

            $objExtraCondition = QQ::AndCondition(
                                    QQ::GreaterThan(QQN::NarroContextInfo()->ContextId, $this->objNarroContextInfo->ContextId  + 100),
                                    $objFilterCodition,
                                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
            );

            if
            (
                $objContext = NarroContextInfo::GetContext(
                                                    $this->objNarroContextInfo->ContextId,
                                                    $this->strSearchText,
                                                    $this->intSearchType,
                                                    $this->intTextFilter,
                                                    QQ::OrderBy(array(QQN::NarroContextInfo()->ContextId, true)),
                                                    $objExtraCondition
                )
            )
            {
                $this->btnPrevious->Visible = true;
                $this->btnPrevious100->Visible = true;
                $this->GoToContext($objContext);
            }
            else {
                $this->btnNext100->Visible = false;
            }

        }

        protected function btnPrevious100_Click($strFormId, $strControlId, $strParameter) {
            if ($this->intFileId)
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->intFileId);
            else
                $objFilterCodition = QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId);

            $objExtraCondition = QQ::AndCondition(
                                    QQ::LessThan(QQN::NarroContextInfo()->ContextId, $this->objNarroContextInfo->ContextId - 100),
                                    $objFilterCodition,
                                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
            );

            if
            (
                $objContext = NarroContextInfo::GetContext(
                                                    $this->objNarroContextInfo->ContextId,
                                                    $this->strSearchText,
                                                    $this->intSearchType,
                                                    $this->intTextFilter,
                                                    QQ::OrderBy(array(QQN::NarroContextInfo()->ContextId, false)),
                                                    $objExtraCondition
                )
            )
            {
                $this->btnNext->Visible = true;
                $this->btnNext100->Visible = true;
                $this->GoToContext($objContext);
            }
            else {
                $this->btnPrevious100->Visible = false;
            }

        }

        protected function GoToContext($objContext) {
            if (QApplication::$blnUseAjax) {
                $this->objNarroContextInfo = $objContext;
                $this->UpdateData();
                $this->UpdateNavigator();
                return true;
            }
            else {
                $strCommonUrl = sprintf('p=%d&c=%d&tf=%d&s=%s&is=%d&gn=%d', $this->intProjectId, $objContext->ContextId, $this->intTextFilter, $this->strSearchText, $this->chkIgnoreSpellcheck->Checked, $this->chkGoToNext->Checked);
                if ($this->intFileId)
                    QApplication::Redirect('narro_context_suggest.php?' . $strCommonUrl . sprintf( '&f=%d', $this->intFileId));
                else
                    QApplication::Redirect('narro_context_suggest.php?' . $strCommonUrl);
            }
        }

        public function btnValidate_Click($strFormId, $strControlId, $strParameter) {
            if (!QApplication::$objUser->hasPermission('Can validate', $this->objNarroContextInfo->Context->ProjectId, QApplication::$objUser->Language->LanguageId))
              return false;

            if ($strParameter != $this->objNarroContextInfo->ValidSuggestionId) {
                $this->objNarroContextInfo->ValidSuggestionId = (int) $strParameter;
            }
            else {
                $this->objNarroContextInfo->ValidSuggestionId = null;
            }
            $this->objNarroContextInfo->Save();
            $this->pnlSuggestionList->NarroContextInfo =  $this->objNarroContextInfo;
            $this->pnlSuggestionList->MarkAsModified();

            if ($this->chkGoToNext->Checked ) {
                $this->btnNext_Click();
            }

        }

    }

    NarroContextSuggestForm::Run('NarroContextSuggestForm', 'templates/narro_context_suggest.tpl.php');
?>
