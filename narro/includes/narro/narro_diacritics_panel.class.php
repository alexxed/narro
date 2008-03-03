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

    class NarroDiacriticsPanel extends QPanel {

        public $strTextareaControlId;

        public function __construct($objParentObject, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->SetCustomStyle('title', 'Diacritice şi semne de punctuaţie româneşti');
        }

        public function GetControlHtml() {
            $this->strText =
                $this->GetLabelHtml('ă') .
                $this->GetLabelHtml('î') .
                $this->GetLabelHtml('â') .
                $this->GetLabelHtml('ş') .
                $this->GetLabelHtml('ţ') .
                $this->GetLabelHtml('Ă') .
                $this->GetLabelHtml('Î') .
                $this->GetLabelHtml('Â') .
                $this->GetLabelHtml('Ş') .
                $this->GetLabelHtml('Ţ') .
                $this->GetLabelHtml('„') .
                $this->GetLabelHtml('”');

            return $this->strText;
        }

        protected function GetLabelHtml($strText) {
            $objLabel = new QLabel($this);
            $objLabel->Text = $strText;
            $objLabel->FontSize = 20;
            $objLabel->Padding = 3;
            $objLabel->AddAction(new QMouseOverEvent(), new QJavaScriptAction("this.style.backgroundColor='red'; this.style.color='white'; this.style.cursor='crosshair'"));
            $objLabel->AddAction(new QMouseOutEvent(), new QJavaScriptAction("this.style.backgroundColor=''; this.style.color='black'; this.style.cursor='normal'"));
            $objLabel->AddAction(new QClickEvent(), new QJavaScriptAction(sprintf("qc.getControl('%s').value += '%s'; qc.getControl('%s').focus();", $this->strTextareaControlId, $strText, $this->strTextareaControlId)));

            return $objLabel->Render(false);
        }



    }
?>