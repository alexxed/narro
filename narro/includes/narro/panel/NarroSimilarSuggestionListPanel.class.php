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

    class NarroSimilarSuggestionListPanel extends QPanel {
        // General Panel Variables
        protected $objNarroText;

        protected $dtgSuggestions;

        protected $colText;
        protected $colSuggestion;
        protected $colActions;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Setup DataGrid Columns
            $this->colText = new QDataGridColumn(t('Original text'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colText_Render($_ITEM); ?>');
            $this->colText->HtmlEntities = false;
            $this->colText->Width = '50%';

            // Setup DataGrid Columns
            $this->colSuggestion = new QDataGridColumn(t('Translation'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colSuggestion_Render($_ITEM); ?>');
            $this->colSuggestion->HtmlEntities = false;
            $this->colSuggestion->CssClass = QApplication::$TargetLanguage->TextDirection;
            $this->colSuggestion->Width = '50%';

            $this->colActions = new QDataGridColumn(t('Actions'), '<?= $_CONTROL->ParentControl->dtgSuggestions_colActions_Render($_ITEM); ?>');
            $this->colActions->HtmlEntities = false;
            $this->colActions->Wrap = false;

            // Setup DataGrid
            $this->dtgSuggestions = new NarroDataGrid($this);
            $this->dtgSuggestions->ShowHeader = true;
            $this->dtgSuggestions->AlwaysShowPaginator = true;
            $this->dtgSuggestions->Title = t('Translations of similar texts');

            // Datagrid Paginator
            $this->dtgSuggestions->Paginator = new QPaginator($this->dtgSuggestions);
            $this->dtgSuggestions->ItemsPerPage = 5;

            // Specify Whether or Not to Refresh using Ajax
            $this->dtgSuggestions->UseAjax = QApplication::$UseAjax;            

            // Specify the local databind method this datagrid will use
            $this->dtgSuggestions->SetDataBinder('dtgSuggestions_Bind', $this);

            $this->dtgSuggestions->AddColumn($this->colText);
            $this->dtgSuggestions->AddColumn($this->colSuggestion);
            if (QApplication::HasPermissionForThisLang('Can suggest', QApplication::QueryString('p')))
                $this->dtgSuggestions->AddColumn($this->colActions);
        }

        public function GetControlHtml() {
            $this->strText = '<br />' .
                $this->dtgSuggestions->Render(false);

            return $this->strText;
        }

        public function dtgSuggestions_colSuggestion_Render($arrSuggestionData) {

            $strSuggestionValue = QApplication::$PluginHandler->DisplaySuggestion($arrSuggestionData['suggestion_value']);
            if (!$strSuggestionValue)
                $strSuggestionValue = $arrSuggestionData['suggestion_value'];

            $strSuggestionValue = NarroString::ShowLeadingAndTrailingSpaces(NarroString::HtmlEntities($strSuggestionValue));

            return $strSuggestionValue;
        }

        public function dtgSuggestions_colText_Render($arrSuggestionData) {
            $strTextValue = QApplication::$PluginHandler->DisplayText($arrSuggestionData['text_value']);
            if (!$strTextValue)
                $strTextValue = $arrSuggestionData['text_value'];

            $strTextValue = NarroString::ShowLeadingAndTrailingSpaces(NarroString::HtmlEntities($strTextValue));

            return $strTextValue;
        }

        public function dtgSuggestions_colActions_Render($arrSuggestionData) {

            $strControlId = 'btnCopySuggestion' . $arrSuggestionData['suggestion_id'];
            $btnCopy = $this->objForm->GetControl($strControlId);
            if (!$btnCopy) {
                $btnCopy = new QButton($this->dtgSuggestions, $strControlId);
                $btnCopy->Text = t('Copy');
                if (QApplication::$UseAjax)
                    $btnCopy->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnCopy_Click'));
                else
                    $btnCopy->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnCopy_Click'));
            }

            $btnCopy->ActionParameter = $arrSuggestionData['suggestion_id'];

            return $btnCopy->Render(false);
        }

        public function dtgSuggestions_Bind() {
            if ($this->dtgSuggestions->LimitInfo)
                list($intOffset, $intLimit) = explode (',', $this->dtgSuggestions->LimitInfo);
            else {
                $intOffset = 0;
                $intLimit = $this->dtgSuggestions->ItemsPerPage;
            }

            if ($this->blnVisible) {
                try {
                    $arrSuggestions = NarroTextSuggestionIndexer::LoadSimilarSuggestionsByTextValue($this->objNarroText->TextValue, QApplication::GetLanguageId(), $intLimit, $intOffset);
                }
                catch (Exception $objEx) {
                    $objEx = null;
                }
            }
            else
                $arrSuggestions = array('count'=>0, 'rows'=>array());

            $this->dtgSuggestions->TotalItemCount = $arrSuggestions['count'];
            $this->dtgSuggestions->ShowFooter = ($arrSuggestions['count'] > $this->dtgSuggestions->ItemsPerPage);
            $this->dtgSuggestions->DataSource = $arrSuggestions['rows'];
            QApplication::ExecuteJavaScript('highlight_datagrid();');
        }

        public function btnCopy_Click($strFormId, $strControlId, $strParameter) {
            $blnResult = true;
            $strControlId = 'btnCopySuggestion' . $arrSuggestionData['suggestion_id'];
            $btnCopy = $this->objForm->GetControl($strControlId);
            $btnCopy->Enabled = true;

            $objSuggestion = NarroSuggestion::Load($strParameter);
            if ($objSuggestion instanceof NarroSuggestion) {
                $this->Form->txtSuggestionValue->Text = $objSuggestion->SuggestionValue;
                $this->Form->txtSuggestionValue->MarkAsModified();
                $this->Form->txtSuggestionValue->Focus();
                return true;
            }
            else {
                if ($btnCopy instanceof QButton) {
                    $btnCopy->Enabled = false;
                    return false;
                }
                else
                    return false;
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {
            $this->blnModified = true;

            switch ($strName) {
                // APPEARANCE
                case "NarroText":
                    try {
                        $this->objNarroText = $mixValue;
                        if ($this->blnVisible)
                            $this->dtgSuggestions->PageNumber = 1;

                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;

                case "Visible":
                    $this->blnVisible = $mixValue;
                    $this->dtgSuggestions->PageNumber = 1;
                    break;

                default:
                    try {
                        parent::__set($strName, $mixValue);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
                    break;
            }
        }

    }
?>
