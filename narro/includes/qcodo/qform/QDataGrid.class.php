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

        // Override any of these methods/variables below to alter the way the DataGrid gets rendered

//      protected function GetPaginatorRowHtml() {}

//      protected function GetHeaderRowHtml() {}

        protected $blnShowFooter = true;
        protected function GetFooterRowHtml() {
            if ($this->objPaginatorAlternate)
                return $this->GetPaginatorRowHtml($this->objPaginatorAlternate);
        }

//      protected function GetDataGridRowHtml($objObject) {}
    }
?>