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

    require(__MODEL_GEN__ . '/NarroProjectGen.class.php');

    /**
    * The NarroProject class defined here contains any
    * customized code for the NarroProject class in the
    * Object Relational Model.  It represents the "narro_project" table
    * in the database, and extends from the code generated abstract NarroProjectGen
    * class, which contains all the basic CRUD-type functionality as well as
    * basic methods to handle relationships and index-based loading.
    *
    * @package My Application
    * @subpackage DataObjects
    *
    */
    class NarroProject extends NarroProjectGen {
        /**
        * Default "to string" handler
        * Allows pages to _p()/echo()/print() this object, and to define the default
        * way this object would be outputted.
        *
        * Can also be called directly via $objNarroProject->__toString().
        *
        * @return string a nicely formatted string representation of this object
        */
        public function __toString() {
            return sprintf('NarroProject Object %s',  $this->intProjectId);
        }

        public function DeleteAllTextsCacheByLanguage($intLanguageId = null) {
            foreach(QApplication::$Cache->getIdsMatchingTags(array('Project' . $this->ProjectId, 'total_texts')) as $strCacheId) {
                QApplication::$Cache->remove($strCacheId);
            }
        }

        public function DeleteTranslatedTextsByLanguage($intLanguageId = null) {
            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            foreach(QApplication::$Cache->getIdsMatchingTags(array('Language' . $intLanguageId, 'Project' . $this->ProjectId, 'translated_texts')) as $strCacheId) {
                QApplication::$Cache->remove($strCacheId);
            }
        }

        public function DeleteApprovedTextsByLanguage($intLanguageId = null) {
            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            foreach(QApplication::$Cache->getIdsMatchingTags(array('Language' . $intLanguageId, 'Project' . $this->ProjectId, 'approved_texts')) as $strCacheId) {
                QApplication::$Cache->remove($strCacheId);
            }
        }


        public function CountTranslatedTextsByLanguage($intLanguageId = null) {
            $intTranslatedTexts = 0;

            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            $intTranslatedTexts = QApplication::$Cache->load('translated_texts_' . $this->ProjectId . '_' . $intLanguageId);

            if ($intTranslatedTexts === false) {
                // Cache miss
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM narro_context c, narro_context_info ci, narro_file f WHERE f.active=1 AND f.file_id=c.file_id AND c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1', $this->ProjectId, $intLanguageId);

                // Perform the Query
                $objDbResult = self::GetDatabase()->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intTranslatedTexts = $mixRow['cnt'];

                    $this->UpdateProjectProgress('FuzzyTextCount', $intTranslatedTexts);
                }
            }

            QApplication::$Cache->save($intTranslatedTexts, 'translated_texts_' . $this->ProjectId . '_' . $intLanguageId, array('Project' . $this->ProjectId, 'Language' . $intLanguageId, 'translated_texts'));

            return $intTranslatedTexts;
        }

        public function CountAllTextsByLanguage($intLanguageId = null) {
            $intTotalTexts = 0;

            $intTranslatedTexts = QApplication::$Cache->load('total_texts' . $this->ProjectId);

            if ($intTotalTexts === false) {
                // Cache miss
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM narro_context c WHERE c.project_id = %d AND c.active=1', $this->ProjectId);

                // Perform the Query
                $objDbResult = self::GetDatabase()->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intTotalTexts = $mixRow['cnt'];

                    $this->UpdateProjectProgress('FuzzyTextCount', $intTotalTexts);
                }
            }

            QApplication::$Cache->save($intTotalTexts, 'total_texts' . $this->ProjectId, array('Project' . $this->ProjectId, 'total_texts'));

            return $intTotalTexts;
        }

        protected function UpdateProjectProgress($strColumn, $intValue) {
            $objProjectProgress = NarroProjectProgress::LoadByProjectIdLanguageId($this->ProjectId, $intLanguageId);

            $blnChanged = false;

            if (!$objProjectProgress instanceof NarroProjectProgress) {
                $objProjectProgress = new NarroProjectProgress();
                $objProjectProgress->LanguageId = $intLanguageId;
                $objProjectProgress->ProjectId = $this->ProjectId;
                $objProjectProgress->TotalTextCount = 0;
                $objProjectProgress->ApprovedTextCount = 0;
                $objProjectProgress->FuzzyTextCount = 0;
                $objProjectProgress->ProgressPercent = 0;
                $objProjectProgress->LastModified = QDateTime::Now();

                $blnChanged = true;
            }

            // Nothing changed
            if (!$blnChanged && $objProjectProgress->$strColumn == $intValue)
                return true;

            $objProjectProgress->$strColumn = $intValue;

            if ($objProjectProgress->TotalTextCount)
                $objProjectProgress->ProgressPercent = floor($objProjectProgress->ApprovedTextCount*100 / $objProjectProgress->TotalTextCount);
            else
                $objProjectProgress->ProgressPercent = 0;

            $objLastContextInfo = NarroContextInfo::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->intProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Active, true),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, $intLanguageId)
                ),
                QQ::OrderBy(QQN::NarroContextInfo()->Modified, 0)
            );
            if ($objLastContextInfo)
                $objProjectProgress->LastModified = $objLastContextInfo->Modified;

            $objProjectProgress->Save();

            return true;
        }

        public function CountApprovedTextsByLanguage($intLanguageId = null) {
            $intApprovedTexts = 0;
            $intApprovedTexts = QApplication::$Cache->load('approved_texts_' . $this->ProjectId . '_' . $intLanguageId);

            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            if ($intApprovedTexts === false) {
                // Cache miss
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci, narro_file f WHERE f.active=1 AND f.file_id=c.file_id AND c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1', $this->ProjectId, $intLanguageId);
                // Perform the Query
                $objDbResult = self::GetDatabase()->Query($strQuery);

                if ($objDbResult) {
                    $mixRow = $objDbResult->FetchArray();
                    $intApprovedTexts = $mixRow['cnt'];

                    $this->UpdateProjectProgress('ApprovedTextCount', $intApprovedTexts);
                }
            }

            QApplication::$Cache->save($intApprovedTexts, 'approved_texts_' . $this->ProjectId . '_' . $intLanguageId, array('Project' . $this->ProjectId, 'Language' . $intLanguageId, 'approved_texts'));
            return $intApprovedTexts;
        }

        public function Save($blnForceInsert = false, $blnForceUpdate = false) {
            $blnNew = (!$this->__blnRestored) || ($blnForceInsert);
            $mixResult = parent::Save($blnForceInsert, $blnForceUpdate);

            if ($blnNew) {
                if (!file_exists($this->DefaultTemplatePath))
                    @mkdir($this->DefaultTemplatePath, 0777, true);

                foreach(NarroLanguage::LoadAll() as $objLanguage) {

                    $objProjectProgress = new NarroProjectProgress();
                    $objProjectProgress->LanguageId = $objLanguage->LanguageId;
                    $objProjectProgress->ProjectId = $this->ProjectId;
                    $objProjectProgress->Active = $this->Active;
                    $objProjectProgress->TotalTextCount = 0;
                    $objProjectProgress->ApprovedTextCount = 0;
                    $objProjectProgress->FuzzyTextCount = 0;
                    $objProjectProgress->ProgressPercent = 0;
                    $objProjectProgress->Active = 1;
                    $objProjectProgress->LastModified = QDateTime::Now();
                    $objProjectProgress->Save();

                    if (!file_exists($this->DefaultTranslationPath))
                        @mkdir($this->DefaultTranslationPath, 0777, true);
                    NarroUtils::RecursiveChmod($this->DefaultTranslationPath, 0666, 0777);
                }
            }

            return $mixResult;
        }

        /**
         * Override method to perform a property "Get"
         * This will get the value of $strName
         *
         * @param string $strName Name of the property to get
         * @return mixed
         */
        public function __get($strName) {
            switch ($strName) {
                ///////////////////
                // Member Variables
                ///////////////////
                case 'DefaultTemplatePath':
                    return __IMPORT_PATH__ . '/' . $this->ProjectId . '/' . NarroLanguage::SOURCE_LANGUAGE_CODE;

                case 'DefaultTranslationPath':
                    return __IMPORT_PATH__ . '/' . $this->ProjectId . '/' . QApplication::$TargetLanguage->LanguageCode;

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