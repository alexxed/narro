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

    require(__MODEL_GEN__ . '/NarroFileGen.class.php');

    /**
    * The NarroFile class defined here contains any
    * customized code for the NarroFile class in the
    * Object Relational Model.  It represents the "narro_file" table
    * in the database, and extends from the code generated abstract NarroFileGen
    * class, which contains all the basic CRUD-type functionality as well as
    * basic methods to handle relationships and index-based loading.
    *
    * @package My Application
    * @subpackage DataObjects
    *
    */
    class NarroFile extends NarroFileGen {
        /**
        * Default "to string" handler
        * Allows pages to _p()/echo()/print() this object, and to define the default
        * way this object would be outputted.
        *
        * Can also be called directly via $objNarroFile->__toString().
        *
        * @return string a nicely formatted string representation of this object
        */
        public function __toString() {
            return sprintf('NarroFile Object %s',  $this->intFileId);
        }

        public static function LoadByProjectIdFileNameParentId($intProjectId, $strFileName, $intParentId) {
                return NarroFile::QuerySingle(
                                QQ::AndCondition(
                                    QQ::Equal(QQN::NarroFile()->ProjectId, $intProjectId),
                                    QQ::Equal(QQN::NarroFile()->FileName, $strFileName),
                                    QQ::Equal(QQN::NarroFile()->ParentId, $intParentId)
                                )
                );
        }

        public static function LoadArrayByFileName($strFileName, $objLimitInfo = null, $objSortInfo = null, $objExtraCondition = null) {
            if (!is_object($objExtraCondition))
                $objExtraCondition = QQ::All();

            if (!is_object($objSortInfo))
                $objSortInfo = QQ::OrderBy(array(QQN::NarroContext()->ContextId, false));

            if (!is_object($objLimitInfo))
                $objLimitInfo = QQ::LimitInfo(20, 0);

            if (preg_match("/^'.*'$/", $strFileName))
                $objSearchCondition = QQ::Equal(QQN::NarroFile()->FileName, substr($strFileName, 1, -1));
            else
                $objSearchCondition = QQ::Like(QQN::NarroFile()->FileName, '%' . $strFileName . '%');

            $arrFiles = NarroFile::QueryArray(QQ::AndCondition($objSearchCondition, $objExtraCondition));
            return $arrFiles;
        }

        public static function CountByFileName($strFileName, $objExtraCondition = null) {
            if (!is_object($objExtraCondition))
                $objExtraCondition = QQ::All();

            if (preg_match("/^'.*'$/", $strFileName))
                $objSearchCondition = QQ::Equal(QQN::NarroFile()->FileName, substr($strFileName, 1, -1));
            else
                $objSearchCondition = QQ::Like(QQN::NarroFile()->FileName, '%' . $strFileName . '%');

            $intFileCount = NarroFile::QueryCount(QQ::AndCondition($objSearchCondition, $objExtraCondition));

            return $intFileCount;

        }

        public function CountAllTextsByLanguage($intLanguageId = null) {

            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            if ($this->TypeId == NarroFileType::Folder)
                $strQuery = sprintf('SELECT COUNT(DISTINCT c.context_id) AS cnt FROM `narro_context` c, `narro_file` f WHERE f.project_id=c.project_id AND f.file_id=c.file_id AND c.project_id=%d AND c.active=1 AND f.active=1 AND f.file_path LIKE \'%s%%\'', $this->ProjectId, $this->FilePath);
            else
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c WHERE c.project_id=%d AND c.active=1 AND c.file_id=%d', $this->ProjectId, $this->FileId);

            // Perform the Query
            $objDbResult = self::GetDatabase()->Query($strQuery);

            if ($objDbResult) {
                $mixRow = $objDbResult->FetchArray();
                $intTotalTexts = $mixRow['cnt'];

                $this->UpdateFileProgress($intLanguageId, 'TotalTextCount', $intTotalTexts);

            }

            return $intTotalTexts;
        }

        public function CountTranslatedTextsByLanguage($intLanguageId = null) {

            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            if ($this->TypeId == NarroFileType::Folder)
                $strQuery = sprintf('SELECT COUNT(DISTINCT c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci, narro_file f WHERE f.project_id=c.project_id AND f.file_id=c.file_id AND c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1 AND f.active=1 AND f.file_path LIKE \'%s%%\'', $this->ProjectId, $intLanguageId, $this->FilePath);
            else
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NULL AND ci.has_suggestions=1 AND c.active=1 AND c.file_id=%d', $this->ProjectId, $intLanguageId, $this->FileId);

            // Perform the Query
            $objDbResult = self::GetDatabase()->Query($strQuery);

            if ($objDbResult) {
                $mixRow = $objDbResult->FetchArray();
                $intTranslatedTexts = $mixRow['cnt'];

                $this->UpdateFileProgress($intLanguageId, 'FuzzyTextCount', $intTranslatedTexts);
            }

            return $intTranslatedTexts;
        }

        public function CountApprovedTextsByLanguage($intLanguageId = null) {

            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            if ($this->TypeId == NarroFileType::Folder)
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci, narro_file f WHERE f.project_id=c.project_id AND f.file_id=c.file_id AND c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1 AND f.active=1 AND f.file_path LIKE \'%s%%\'', $this->ProjectId, $intLanguageId, $this->FilePath);
            else
                $strQuery = sprintf('SELECT COUNT(c.context_id) AS cnt FROM `narro_context` c, narro_context_info ci WHERE c.context_id=ci.context_id AND c.project_id = %d AND ci.language_id=%d AND ci.valid_suggestion_id IS NOT NULL AND c.active=1 AND c.file_id=%d', $this->ProjectId, $intLanguageId, $this->FileId);

            // Perform the Query
            $objDbResult = self::GetDatabase()->Query($strQuery);

            if ($objDbResult) {
                $mixRow = $objDbResult->FetchArray();
                $intApprovedTexts = $mixRow['cnt'];

                $this->UpdateFileProgress($intLanguageId, 'ApprovedTextCount', $intApprovedTexts);
            }

            return $intApprovedTexts;
        }

        protected function UpdateFileProgress($intLanguageId, $strColumn, $intValue) {
            $objFileProgress = NarroFileProgress::LoadByFileIdLanguageId($this->FileId, $intLanguageId);

            $blnNew = false;

            if (!$objFileProgress instanceof NarroFileProgress) {
                $objFileProgress = new NarroFileProgress();
                $objFileProgress->LanguageId = $intLanguageId;
                $objFileProgress->FileId = $this->FileId;
                $objFileProgress->TotalTextCount = 0;
                $objFileProgress->Export = 1;
                $objFileProgress->ApprovedTextCount = 0;
                $objFileProgress->FuzzyTextCount = 0;
                $objFileProgress->ProgressPercent = 0;

                $blnNew = true;
            }

            // Nothing changed
            if (!$blnNew && $objFileProgress->$strColumn == $intValue)
                return true;

            $objFileProgress->$strColumn = $intValue;

            if ($objFileProgress->TotalTextCount)
                $objFileProgress->ProgressPercent = floor($objFileProgress->ApprovedTextCount*100 / $objFileProgress->TotalTextCount);
            else
                $objFileProgress->ProgressPercent = 0;

            $objFileProgress->Save();

            return true;
        }

        public function LoadArrayOfAuthors($intLanguageId = null) {
            if (is_null($intLanguageId)) $intLanguageId = QApplication::GetLanguageId();

            return NarroUser::QueryArray(
                QQ::AndCondition(
                    QQ::NotEqual(QQN::NarroUser()->UserId, NarroUser::ANONYMOUS_USER_ID),
                    QQ::OrCondition(
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->LanguageId, $intLanguageId),
                            QQ::Equal(QQN::NarroUser()->NarroSuggestionAsUser->NarroContextInfoAsValidSuggestion->Context->FileId, $this->FileId)
                        ),
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroUser()->NarroContextInfoAsValidatorUser->LanguageId, $intLanguageId),
                            QQ::Equal(QQN::NarroUser()->NarroContextInfoAsValidatorUser->Context->FileId, $this->FileId)
                        )
                    )
                ),
                array(
                    QQ::Distinct(),
                    QQ::OrderBy(QQN::NarroUser()->Username)
                )
            );
        }



    }
?>