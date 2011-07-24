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
    class NarroActivityPanel extends QPanel {
        public $plotChart;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // set graph options
            $this->plotChart = new QJqplot($this);
            $this->plotChart->Width = '100%';

            $this->plotChart->xaxis = new QJqplotAxis();
            $this->plotChart->xaxis->Renderer = 'DateAxisRenderer';
            $this->plotChart->title = sprintf('Activity for all projects in %s', QApplication::$TargetLanguage->LanguageName);

            $this->plotChart->yaxis = new QJqplotAxis();
            $this->plotChart->yaxis->min = 0;
            $this->plotChart->yaxis->padMin = 1;
            $this->plotChart->yaxis->padMax = 2;

            $this->plotChart->AddSeriesWithLabel('texts added');
            $this->plotChart->series['texts added']->markerOptions->show = false;
            $this->plotChart->AddSeriesWithLabel('translations added');
            $this->plotChart->series['translations added']->markerOptions->show = false;
            $this->plotChart->AddSeriesWithLabel('translations approved');
            $this->plotChart->series['translations approved']->markerOptions->show = false;
            $this->plotChart->AddSeriesWithLabel('comments');
            $this->plotChart->series['comments']->markerOptions->show = false;

            $this->strTemplate = __NARRO_INCLUDES__ . '/narro/panel/NarroActivityPanel.tpl.php';
        }

        public function GetControlHtml() {

            $objDatabase = QApplication::$Database[1];

            $dttThen = QDateTime::FromTimestamp(time() - QDateTimeSpan::SecondsPerYear);
            $strQuery = sprintf('
                SELECT
                      DATE(narro_context_info.modified) AS date_modified, COUNT(context_info_id) AS cnt
                FROM
                    `narro_context_info`, `narro_context`, `narro_suggestion`
                WHERE
                    narro_context_info.valid_suggestion_id=narro_suggestion.suggestion_id AND
                    narro_context_info.validator_user_id > 0 AND
                    narro_context_info.context_id=narro_context.context_id AND
                    NOT narro_suggestion.is_imported AND
                    narro_context_info.language_id=%d AND
                    narro_context_info.modified > \'%s\'
                GROUP BY date_modified',
                QApplication::GetLanguageId(),
                $dttThen->format('Y-m-d')
            );

            $objDbResult = $objDatabase->Query($strQuery);
            $arrSeriesData = array();
            while($arrRow = $objDbResult->FetchArray()) {
                $arrSeriesData[$arrRow['date_modified']] = $arrRow['cnt'];
            }

            if ($arrSeriesData)
                $this->plotChart->series['translations approved']->data = $arrSeriesData;
//
//            $strQuery = sprintf('
//                SELECT
//                    DATE(narro_text.created) AS date_created, COUNT(narro_text.text_id) AS cnt
//                FROM
//                    `narro_text`
//                WHERE
//                    narro_text.created > 0
//                GROUP BY date_created',
//                QApplication::GetLanguageId()
//            );
//            $objDbResult = $objDatabase->Query($strQuery);
//            $arrSeriesData = array();
//            while($arrRow = $objDbResult->FetchArray())
//                $arrSeriesData[$arrRow['date_created']] = $arrRow['cnt'];
//
//            if ($arrSeriesData)
//                $this->plotChart->series['texts added']->data = $arrSeriesData;


            $strQuery = sprintf('
                SELECT
                    DATE(narro_suggestion.created) AS date_created, COUNT(narro_suggestion.suggestion_id) AS cnt
                FROM
                    `narro_suggestion`
                WHERE
                    narro_suggestion.user_id > 0 AND
                    NOT narro_suggestion.is_imported AND
                    narro_suggestion.language_id=%d AND
                    narro_suggestion.created > \'%s\'
                GROUP BY narro_suggestion.created',
                QApplication::GetLanguageId(),
                $dttThen->format('Y-m-d')
            );

            $objDbResult = $objDatabase->Query($strQuery);
            $arrSeriesData = array();
            while($arrRow = $objDbResult->FetchArray())
                 $arrSeriesData[$arrRow['date_created']] = $arrRow['cnt'];

            if ($arrSeriesData)
                $this->plotChart->series['translations added']->data = $arrSeriesData;

            return parent::GetControlHtml();
        }

        public function btnRefresh_Click() {
            $this->MarkAsModified();
        }
    }