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

    class NarroLogViewerPanel extends QPanel {
        protected $strLogFile;
        protected $strLogContents;
        protected $btnDownloadLog;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->btnDownloadLog = new QButton($this);
            $this->btnDownloadLog->Text = t('Download');

            $this->btnDownloadLog->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnDownloadLog_Click'));
        }

        public function GetControlHtml() {
            $strLogContents = '';

            if (file_exists($this->strLogFile)) {
                $this->blnVisible = true;
                $arrLogLine = preg_match_all('/([0-9]{4,4}\-[0-9]{2,2}-[0-9]{2,2}T[0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}\+[0-9]{2,2}:[0-9]{2,2})\s([^\s]+)[^:]+:\s(.*)/', file_get_contents($this->strLogFile), $arrMatches);
                foreach($arrMatches[0] as $intIndex=>$strMatch) {
                    if (in_array($arrMatches[2][$intIndex], array('ERR', 'WARN', 'INFO')))
                        $strLogContents .= sprintf('<div class="%s" title="%s">%s</div>', strtolower($arrMatches[2][$intIndex]), $arrMatches[1][$intIndex], nl2br(NarroString::HtmlEntities($arrMatches[3][$intIndex])));
                }
            }
            else {
                $this->blnVisible = false;
            }

            $this->strText = sprintf(
                '<div class="section_title">%s</div>
                <div class="section">
                    <div style="max-height:300px;overflow:auto">%s</div>
                    <div align="right">%s</div>
                </div>',
                t('Operation log'),
                $strLogContents,
                $this->btnDownloadLog->Render(false)
            );

            return parent::GetControlHtml();
        }

        public function btnDownloadLog_Click($strFormId, $strControlId, $strParameter) {
            header('Content-type: text/plain');
            header(sprintf('Content-Disposition: attachment; filename="%s.txt"', basename($this->strLogFile)));
            readfile($this->strLogFile);
            exit;
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
