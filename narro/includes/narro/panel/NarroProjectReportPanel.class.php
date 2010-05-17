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
    class NarroProjectReportPanel extends QPanel {
        protected $objNarroProject;
        public $flotReport;
        public $dtxFrom;
        public $calFrom;
        public $dtxTo;
        public $calTo;
        public $btnRefresh;

        public function __construct(NarroProject $objNarroProject, $objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->dtxFrom = new QDateTimeTextBox($this);
            $this->calFrom = new QCalendar($this, $this->dtxFrom);
            $this->dtxFrom->AddAction(new QFocusEvent(), new QBlurControlAction($this->dtxFrom));
            $this->dtxFrom->AddAction(new QClickEvent(), new QShowCalendarAction($this->calFrom));

            $this->dtxTo = new QDateTimeTextBox($this);
            $this->calTo = new QCalendar($this, $this->dtxTo);
            $this->dtxTo->AddAction(new QFocusEvent(), new QBlurControlAction($this->dtxTo));
            $this->dtxTo->AddAction(new QClickEvent(), new QShowCalendarAction($this->calTo));

            $this->objNarroProject = $objNarroProject;


            // set graph options
            $this->flotReport = new QFlot($this);
            $this->flotReport->DisplayVariables = true;
            $this->flotReport->VariablesTitle = '';
            $this->flotReport->XTimeSeries = true;
            $this->flotReport->GridHoverable = true;
            $this->flotReport->ShowTooltip = true;
            $this->flotReport->YMin = 0;
            $this->flotReport->YTickDecimals = 0;
            $this->flotReport->Width = 1024;

            $this->btnRefresh = new QButton($this);
            $this->btnRefresh->Text = t('Refresh');
            $this->btnRefresh->PrimaryButton = true;
            $this->btnRefresh->AddAction(new QClickEvent(), new QAjaxControlAction($this, 'btnRefresh_Click'));

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroProjectReportPanel.tpl.php';
        }

        public function GetControlHtml() {
            $strLowestFrom = date('Y-m-d');
            $strHighestTo = date('Y-m-d', time() - (3600*24));

            $objApprovedSeries = new QFlotSeries('Approved');
            $objApprovedSeries->Lines = true;
            $objApprovedSeries->LinesFill = false;
            $objApprovedSeries->Points = false;

            $objDatabase = QApplication::$Database[1];

            $strQuery = sprintf('
                SELECT
                    DATE(narro_context_info.modified) AS date_modified, COUNT(context_info_id) AS cnt
                FROM
                    `narro_context_info`, `narro_context`
                WHERE
                    narro_context_info.context_id=narro_context.context_id AND
                    valid_suggestion_id IS NOT NULL AND
                    narro_context_info.modified>0 AND
                    language_id=%d AND
                    project_id=%d AND
                    %s
                GROUP BY DATE(narro_context_info.modified)
                ORDER BY narro_context_info.modified',
                QApplication::GetLanguageId(),
                $this->objNarroProject->ProjectId,
                ($this->dtxFrom->Text != '' && $this->dtxTo->Text != '')
                ?
                sprintf(
                    'narro_context_info.modified BETWEEN \'%s\' AND \'%s\'',
                    date('Y-m-d', strtotime($this->dtxFrom->Text)),
                    date('Y-m-d', strtotime($this->dtxTo->Text))
                )
                :'1'
            );

            $strCacheId = 'ReportPanelApproved' . md5($strQuery);
            $arrSeriesData = QApplication::$Cache->load($strCacheId);
            if ($arrSeriesData === false) {
                $objDbResult = $objDatabase->Query($strQuery);
                while($arrRow = $objDbResult->FetchArray()) {
                    $arrSeriesData[$arrRow['date_modified']] = $arrRow['cnt'];
                }
                if (count($arrSeriesData))
                    QApplication::$Cache->save($arrSeriesData, $strCacheId, array('Project' . $this->objNarroProject->ProjectId));
            }

            if (is_array($arrSeriesData)) {
                foreach($arrSeriesData as $strDate=>$intCount) {
                    if (strtotime($strDate) < strtotime($strLowestFrom)) $strLowestFrom = $strDate;
                    if (strtotime($strDate) > strtotime($strHighestTo)) $strHighestTo = $strDate;
                    $objApprovedSeries->AddDataPoint($strDate, $intCount);
                }
            }
            $this->flotReport->ReplaceSeries(0, $objApprovedSeries);

            $objTranslatedSeries = new QFlotSeries('Translated');
            $objTranslatedSeries->Lines = true;
            $objTranslatedSeries->LinesFill = false;
            $objTranslatedSeries->Points = false;

            $strQuery = sprintf('
                SELECT
                    DATE(narro_suggestion.created) AS date_created, COUNT(narro_suggestion.suggestion_id) AS cnt
                FROM
                    `narro_context_info`, `narro_context`, `narro_suggestion`
                WHERE
                    narro_context_info.context_id=narro_context.context_id AND
                    narro_context.text_id=narro_suggestion.text_id AND
                    narro_suggestion.language_id=%d AND
                    narro_context.project_id=%d AND
                    %s
                GROUP BY DATE(narro_suggestion.created)
                ORDER BY narro_suggestion.created',
                QApplication::GetLanguageId(),
                $this->objNarroProject->ProjectId,
                ($this->dtxFrom->Text != '' && $this->dtxTo->Text != '')
                ?
                sprintf(
                    'narro_suggestion.created BETWEEN \'%s\' AND \'%s\'',
                    date('Y-m-d', strtotime($this->dtxFrom->Text)),
                    date('Y-m-d', strtotime($this->dtxTo->Text))
                )
                :'1'
            );

            $strCacheId = 'ReportPanelTranslated' . md5($strQuery);
            $arrSeriesData = QApplication::$Cache->load($strCacheId);
            if ($arrSeriesData === false) {
                $objDbResult = $objDatabase->Query($strQuery);
                while($arrRow = $objDbResult->FetchArray()) {
                    $arrSeriesData[$arrRow['date_created']] = $arrRow['cnt'];
                }
                if (count($arrSeriesData))
                    QApplication::$Cache->save($arrSeriesData, $strCacheId, array('Project' . $this->objNarroProject->ProjectId));
            }

            if (is_array($arrSeriesData))
                foreach($arrSeriesData as $strDate=>$intCount) {
                    if (strtotime($strDate) < strtotime($strLowestFrom)) $strLowestFrom = $strDate;
                    if (strtotime($strDate) > strtotime($strHighestTo)) $strHighestTo = $strDate;
                    $objTranslatedSeries->AddDataPoint($strDate, $intCount);
                }

            $this->flotReport->ReplaceSeries(1, $objTranslatedSeries);

            if ($this->dtxFrom->Text == '')
                $this->dtxFrom->Text = $strLowestFrom;

            if ($this->dtxTo->Text == '')
                $this->dtxTo->Text = $strHighestTo;

            return parent::GetControlHtml();
        }

        public function btnRefresh_Click() {
            $this->MarkAsModified();
        }
    }