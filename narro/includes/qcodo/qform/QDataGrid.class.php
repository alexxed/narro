<?php
    class QDataGrid extends QDataGridBase  {
        ///////////////////////////
        // DataGrid Preferences
        ///////////////////////////

        // Feel free to specify global display preferences/defaults for all QDataGrid controls
        public function __construct($objParentObject, $strControlId = null) {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException  $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

                    //$this->CellSpacing = 0;
            $this->CellPadding = 4;
            $this->BorderStyle = QBorderStyle::Solid;
            $this->BorderColor = '#DDDDDD';
            $this->BorderWidth = 1;
            $this->GridLines = QGridLines::Both;
            $this->Width = '100%';
            $objStyle = new QDataGridRowStyle();
            $objStyle->CssClass = 'datagrid_header';
            $this->HeaderRowStyle = $objStyle;
            $objStyle = new QDataGridRowStyle();
            $objStyle->CssClass = 'datagrid_row datagrid_even';
            $this->RowStyle = $objStyle;
            $objStyle = new QDataGridRowStyle();
            $objStyle->CssClass = 'datagrid_row datagrid_odd';
            $this->AlternateRowStyle = $objStyle;


        }

//        protected function GetPaginatorRowHtml($objPaginator) {
//            if ($this->objPaginator->TotalItemCount < $this->objPaginator->ItemsPerPage)
//                return false;
//            else
//                return parent::GetPaginatorRowHtml($objPaginator);
//        }

//      protected function GetHeaderRowHtml() {}

        protected $blnShowFooter = true;
        protected function GetFooterRowHtml() {
            if ($this->objPaginatorAlternate && $this->objPaginator->TotalItemCount > 10)
                return $this->GetPaginatorRowHtml($this->objPaginatorAlternate);
        }

        protected function GetHeaderRowHtml() {
            if ($this->objPaginator->TotalItemCount)
                return parent::GetHeaderRowHtml();
            else
                return '';
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {
            switch ($strName) {
                case 'Title':
                    try {
                        $this->strLabelForNoneFound = sprintf('<b>%s:</b> %s', $mixValue, QApplication::Translate('%s found nothing.'));/**Translators: ignore %s */
                        $this->strLabelForOneFound = sprintf('<b>%s:</b> %s', $mixValue, QApplication::Translate(' 1 %s found.'));/**Translators: ignore %s */
                        $this->strLabelForMultipleFound = sprintf('<b>%s:</b> %s', $mixValue, QApplication::Translate(' %d %s found.'));/**Translators: ignore %s */
                        $this->strLabelForPaginated = sprintf('<b>%s:</b> %s', $mixValue, QApplication::Translate('%s %d-%d of %d.'));/**Translators: ignore %s */
                        $this->strNoun = '';
                        $this->strNounPlural = '';

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
?>