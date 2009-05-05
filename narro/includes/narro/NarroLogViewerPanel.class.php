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

    class NarroLogViewerPanel extends QPanel {
        protected $strLogFile;
        protected $strLogContents;
        protected $lstFilter;
        protected $btnDownloadLog;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->lstFilter = new QListBox($this);
            $this->lstFilter->DisplayStyle = QDisplayStyle::Block;
            $this->lstFilter->AddItem(t('Warning'), 0, true);
            $this->lstFilter->AddItem(t('Debug'), 1);

            if (NarroApp::$UseAjax)
                $this->lstFilter->AddAction(new QChangeEvent(), new QAjaxControlAction($this, 'lstFilter_Click'));
            else
                $this->lstFilter->AddAction(new QChangeEvent(), new QServerControlAction($this, 'lstFilter_Click'));

            $this->btnDownloadLog = new QButton($this);
            $this->btnDownloadLog->Text = t('Download');

            $this->btnDownloadLog->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDownloadLog_Click'));
        }

        public function GetControlHtml() {
            if (file_exists($this->strLogFile)) {
                $strLogContents = file_get_contents($this->strLogFile);
                $hndFile = fopen($this->strLogFile, 'r');
                if ($hndFile) {
                    $strLogContents = '';
                    while (!feof($hndFile)) {
                        $strLogLine = fgets($hndFile);
                        switch($this->lstFilter->SelectedValue) {
                            case 0:
                                if (!preg_match('/[0-9\-T:]+\sDEBUG\s\(7\)/', $strLogLine))
                                    $strLogContents .= preg_replace('/[0-9\-T:]+\s[A-Z]+\s\([0-9]+\):/', '', $strLogLine);
                                break;
                            default:
                                $strLogContents .= preg_replace('/[0-9\-T:]+\s[A-Z]+\s\([0-9]+\):/', '', $strLogLine);
                        }
                    }
                    fclose($hndFile);
                }
            }

            $this->strText = sprintf(
                '<div class="section_title">%s</div>
                <div class="section">
                    <div align="right">%s</div>
                    <div style="max-height:300px;overflow:auto">%s</div>
                    <div align="right">%s</div>
                </div>',
                t('Operation log'),
                $this->lstFilter->Render(false),
                nl2br(NarroString::HtmlEntities($strLogContents)),
                $this->btnDownloadLog->Render(false)
            );

            return parent::GetControlHtml();
        }

        public function lstFilter_Click($strFormId, $strControlId, $strParameter) {
            $this->blnModified = true;
        }

        public function btnDownloadLog_Click($strFormId, $strControlId, $strParameter) {
            header('Content-type: text/plain');
            header(sprintf('Content-Disposition: attachment; filename="%s.txt"', basename($this->strLogFile)));
            readfile($this->strLogFile);
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {
                case "LogFile": return $this->strLogFile;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {

            switch ($strName) {
                case "LogFile":
                    try {
                        $this->strLogFile = QType::Cast($mixValue, QType::String);
                        $this->blnVisible = true;
                        $this->blnModified = true;
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        parent::__set($strName, $mixValue);
                        break;
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
