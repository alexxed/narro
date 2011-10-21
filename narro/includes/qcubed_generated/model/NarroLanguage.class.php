<?php
    /**
     * Narro is an application that allows online software translation and maintenance.
     * Copyright (C) 2008-2011 Alexandru Szasz <alexxed@gmail.com>
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

    require(__MODEL_GEN__ . '/NarroLanguageGen.class.php');

    /**
    * The NarroLanguage class defined here contains any
    * customized code for the NarroLanguage class in the
    * Object Relational Model.  It represents the "narro_language" table
    * in the database, and extends from the code generated abstract NarroLanguageGen
    * class, which contains all the basic CRUD-type functionality as well as
    * basic methods to handle relationships and index-based loading.
    *
    * @package Narro
    * @subpackage DataObjects
    *
    */
    class NarroLanguage extends NarroLanguageGen {
        const SOURCE_LANGUAGE_CODE = 'en-US';
        /**
        * Default "to string" handler
        * Allows pages to _p()/echo()/print() this object, and to define the default
        * way this object would be outputted.
        *
        * Can also be called directly via $objNarroLanguage->__toString().
        *
        * @return string a nicely formatted string representation of this object
        */
        public function __toString() {
            return sprintf('NarroLanguage Object %s',  $this->intLanguageId);
        }

        public static function LoadAllActive($objOptionalClauses = null) {
            if (is_null($objOptionalClauses))
                $objOptionalClauses = array(QQ::OrderBy(QQN::NarroLanguage()->LanguageName));
            
            return
                parent::QueryArray(
                    QQ::AndCondition(
                        QQ::NotEqual(QQN::NarroLanguage()->LanguageCode, NarroLanguage::SOURCE_LANGUAGE_CODE),
                        QQ::Equal(QQN::NarroLanguage()->Active, 1)
                    ),
                    $objOptionalClauses
                );
        }

        public static function CountAllActive() {
            return parent::QueryCount(
                QQ::AndCondition(
                    QQ::NotEqual(QQN::NarroLanguage()->LanguageCode, NarroLanguage::SOURCE_LANGUAGE_CODE),
                    QQ::Equal(QQN::NarroLanguage()->Active, 1)
                )
            );
        }

        public function Save($blnForceInsert = false, $blnForceUpdate = false) {

            $mixResult = parent::Save($blnForceInsert, $blnForceUpdate);

            foreach(NarroProject::LoadAll() as $objProject) {
                $objProjectProgress = NarroProjectProgress::LoadByProjectIdLanguageId($objProject->ProjectId, $this->LanguageId);
                if (!$objProjectProgress) {
                    $objProject->CountAllTextsByLanguage($this->LanguageId);
                }
            }

            return $mixResult;
        }

        public function __get($strName) {
            switch ($strName) {
                case 'Plurals':
                    if (preg_match('/nplurals=([0-9])/i', $this->strPluralForm, $arrMatches))
                        return $arrMatches[1];
                    else
                        return 2;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }

    }
?>