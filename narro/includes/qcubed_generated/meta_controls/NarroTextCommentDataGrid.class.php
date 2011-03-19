<?php
    require(__META_CONTROLS_GEN__ . '/NarroTextCommentDataGridGen.class.php');

    /**
     * This is the "Meta" DataGrid customizable subclass for the List functionality
     * of the NarroTextComment class.  This code-generated class extends
     * from the generated Meta DataGrid class which contains a QDataGrid class which
     * can be used by any QForm or QPanel, listing a collection of NarroTextComment
     * objects.  It includes functionality to perform pagination and sorting on columns.
     *
     * To take advantage of some (or all) of these control objects, you
     * must create an instance of this DataGrid in a QForm or QPanel.
     *
     * This file is intended to be modified.  Subsequent code regenerations will NOT modify
     * or overwrite this file.
     *
     * @package Narro
     * @subpackage MetaControls
     *
     */
    class NarroTextCommentDataGrid extends NarroTextCommentDataGridGen {
            /**
         * This will set the property $strName to be $mixValue
         *
         * @param string $strName Name of the property to set
         * @param string $mixValue New value of the property
         * @return mixed
         */
        public function __set($strName, $mixValue) {
            switch ($strName) {
                case 'AdditionalClauses':
                try {
                    $this->clsAdditionalClauses = $mixValue;
                    return true;
                } catch (QCallerException $objExc) {
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