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

    class NarroProjectCategoryPanel extends QPanel {
        protected $dtgProjectCategory;
        protected $colProjectCategory;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->colProjectCategory = new QDataGridColumn(t('Category'), '<?= $_CONTROL->ParentControl->dtgProjectCategory_colProjectCategory_Render($_ITEM); ?>');
            $this->colProjectCategory->HtmlEntities = false;

            // Setup DataGrid
            $this->dtgProjectCategory = new NarroDataGrid($this);
            $this->dtgProjectCategory->SetCustomStyle('padding', '5px');
            //$this->dtgProjectCategory->SetCustomStyle('margin-left', '15px');


            // Specify Whether or Not to Refresh using Ajax
            $this->dtgProjectCategory->UseAjax = QApplication::$UseAjax;

            // Specify the local databind method this datagrid will use
            $this->dtgProjectCategory->SetDataBinder('dtgProjectCategory_Bind', $this);

            $this->dtgProjectCategory->AddColumn($this->colProjectCategory);
        }

        public function dtgProjectCategory_colProjectCategory_Render( $objProjectCategory ) {
            return NarroLink::ProjectList($objProjectCategory->CategoryName);
        }

        public function dtgProjectCategory_Bind() {
            $this->dtgProjectCategory->DataSource = NarroProjectCategory::LoadArrayByLanguageId(QApplication::GetLanguageId());

            QApplication::ExecuteJavaScript('highlight_datagrid();');
        }

        protected function GetControlHtml() {
            return $this->dtgProjectCategory->Render(false);
        }

    }
?>
