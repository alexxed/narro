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

    abstract class NarroFileImporter {
        /**
         * the user object used for import
         * @var NarroUser
         */
        protected $objUser;
        /**
         * the language object used to import in
         * @var NarroLanguage
         */
        protected $objSourceLanguage;
        /**
         * the language object used to import from
         * @var NarroLanguage
         */
        protected $objTargetLanguage;
        /**
         * the project object that is imported
         * @var NarroProject
         */
        protected $objProject;
        /**
         * the file that is imported
         * @var NarroFile
         */
        protected $objFile;
        /**
         * whether to check if the suggestion value is the same as the original text
         * if it's true, the suggestions that are the same as the original text are not imported
         * @var boolean
         */
        protected $blnCheckEqual = true;
        /**
         * whether to approve the imported suggestions
         * @var boolean
         */
        protected $blnApprove = true;
        /**
         * whether to export the source text if no translation is found
         * @var boolean
         */
        protected $blnSkipUntranslated = false;
        /**
         * whether to approve the import suggestions even if another suggestion is approved in Narro
         * @var boolean
         */
        protected $blnApproveAlreadyApproved = false;
        /**
         * whether to import only suggestions, that is don't add anything else than suggestions
         * @var boolean
         */
        protected $blnOnlySuggestions = false;
        /**
         * what suggestions are exported
         * 1 = approved
         * 2 = approved and most voted
         * 3 = approved and most recent suggestion
         * 4 = approved and most voted and most recent suggestion
         * 5 = approved and current user's suggestion
         * @var integer
         */
        protected $intExportedSuggestion = 1;

        /**
         * An array with all the context info objects from this file
         * @var NarroContextInfo[]
         */
        protected $arrContextInfo = array();

        /**
         * An array with all the suggestions for the texts from this file
         * @var NarroSuggestion[];
         */
        protected $arrSuggestion = array();

        /**
         * A list of context ids from this file; whenever a context is found, the id is removed from the list
         * all remaining ids will be marked as inactive
         * @var array
         */
        protected $arrContextId;

        public function __construct($objImporter = null) {

            if ($objImporter instanceof NarroProjectImporter) {

                $this->objUser = $objImporter->User;
                $this->objSourceLanguage = $objImporter->SourceLanguage;
                $this->objTargetLanguage = $objImporter->TargetLanguage;
                $this->objProject = $objImporter->Project;
                $this->blnCheckEqual = $objImporter->CheckEqual;
                $this->blnApprove = $objImporter->Approve;
                $this->blnSkipUntranslated = $objImporter->SkipUntranslated;
                $this->blnApproveAlreadyApproved = $objImporter->ApproveAlreadyApproved;
                $this->blnOnlySuggestions = $objImporter->OnlySuggestions;
                $this->intExportedSuggestion = $objImporter->ExportedSuggestion;
            }

        }

        protected function GetContextInfoArray() {
            $this->arrContextInfo = NarroContextInfo::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objFile->FileId),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, $this->objTargetLanguage->LanguageId)
                ),
                array(
                    QQ::Expand(QQN::NarroContextInfo()->Context->Text)
                )
            );

            foreach($this->arrContextInfo as $objContextInfo) {
                $this->arrContextId[$objContextInfo->ContextId] = $objContextInfo->ContextId;
            }
        }

        protected function GetSugestionArray() {
            $this->arrSuggestion = NarroSuggestion::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestion()->Text->NarroContextAsText->FileId, $this->objFile->FileId),
                    QQ::Equal(QQN::NarroSuggestion()->LanguageId, $this->objTargetLanguage->LanguageId)
                ),
                array(
                    QQ::Expand(QQN::NarroSuggestion()->User),
                    QQ::Expand(QQN::NarroSuggestion()->Text)
                )
            );
        }

        /**
         * Returns a suggestion object from this file based on the text and translation text
         * @param string $strOriginal
         * @param string $strTranslation
         * @return NarroSuggestion
         */
        protected function GetSuggestion($strOriginal, $strTranslation) {
            foreach($this->arrSuggestion as $objSuggestion) {
                if ($objSuggestion->Text->TextValue == $strOriginal && $objSuggestion->SuggestionValue == $strTranslation)
                    return $objSuggestion;
            }

            return false;
        }

        /**
         * Returns a text from this file, in the current language
         * @param string $strText
         * @return NarroText
         */
        protected function GetText($strText, $strContext) {
            foreach($this->arrContextInfo as $objContextInfo) {
                if ($objContextInfo->Context->Text->TextValue == $strText && $objContextInfo->Context->Context == $strContext)
                    return $objContextInfo->Context->Text;
            }

            return false;
        }


        protected function startTimer() {
            NarroImportStatistics::$arrStatistics['Start time'] = time();
        }

        protected function stopTimer() {
            NarroImportStatistics::$arrStatistics['End time'] = time();
        }

        /**
         * A translation here consists of the project, file, text, translation, context, approval, ignore equals
         *
         * @param string $strOriginal the original text
         * @param string $strOriginalAccKey access key for the original text
         * @param string $strTranslation the translated text from the import file (can be empty)
         * @param string $strOriginalAccKey access key for the translated text
         * @param string $strContext the context where the text/translation appears in the file
         * @param string $strComment a comment from the imported file
         */
        protected function AddTranslation($strOriginal, $strOriginalAccKey = null, $strTranslation, $strTranslationAccKey = null, $strContext, $strComment = null) {

            $blnContextInfoChanged = false;
            $blnContextChanged = false;

            /**
             * First, let the plug-ins process the data
             */
            if ($strOriginal == '') {
                QApplication::LogWarn(sprintf('In file "%s", the context "%s" was skipped because the original text "%s" was empty.', $this->objFile->FileName, $strContext, $strOriginal));
                NarroImportStatistics::$arrStatistics['Skipped contexts']++;
                NarroImportStatistics::$arrStatistics['Skipped suggestions']++;
                NarroImportStatistics::$arrStatistics['Skipped texts']++;
                NarroImportStatistics::$arrStatistics['Empty original texts']++;
                return false;
            }
            else {
                $arrResult = QApplication::$PluginHandler->SaveText($strOriginal, $strTranslation, $strContext, $this->objFile, $this->objProject);
                if
                (
                    $arrResult[0] != '' &&
                    $arrResult[1] == $strTranslation &&
                    $arrResult[2] == $strContext &&
                    $arrResult[3] == $this->objFile &&
                    $arrResult[4] == $this->objProject
                ) {

                    $strOriginal = $arrResult[0];
                }
                else
                    QApplication::LogWarn(sprintf('The plug-in %s returned an unexpected result while processing the text "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $strOriginal, print_r($arrResult, true)));
            }

            if ($strTranslation != '') {
                $arrResult = QApplication::$PluginHandler->SaveSuggestion($strOriginal, $strTranslation, $strContext, $this->objFile, $this->objProject);
                if
                (
                    $arrResult[1] != '' &&
                    $arrResult[0] == $strOriginal &&
                    $arrResult[2] == $strContext &&
                    $arrResult[3] == $this->objFile &&
                    $arrResult[4] == $this->objProject
                ) {
                    $strTranslation = $arrResult[1];
                }
                else
                    QApplication::LogWarn(sprintf('The plug-in %s returned an unexpected result while processing the translation "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $strTranslation, print_r($arrResult, true)));
            }

            $strContext = trim($strContext);
            $arrResult = QApplication::$PluginHandler->SaveContext($strOriginal, $strTranslation, $strContext, $this->objFile, $this->objProject);
            if
            (
                (trim($arrResult[2]) != '' || $strContext == '') &&
                $arrResult[0] == $strOriginal &&
                $arrResult[1] == $strTranslation &&
                $arrResult[3] == $this->objFile &&
                $arrResult[4] == $this->objProject
            ) {

                $strContext = $arrResult[2];
            }
            else
                QApplication::LogWarn(sprintf('The plug-in %s returned an unexpected result while processing the context "%s": %s', QApplication::$PluginHandler->CurrentPluginName, $strContext, print_r($arrResult, true)));

            $objText = $this->GetText($strOriginal, $strContext);

            if (!$objText)
                $objText = NarroText::LoadByTextValueMd5(md5($strOriginal));

            if (!$this->blnOnlySuggestions && !$objText instanceof NarroText) {

                $objText = new NarroText();
                $objText->TextValue = $strOriginal;

                QApplication::$PluginHandler->AddText($strOriginal, $strTranslation, $strContext, $this->objFile, $this->objProject);

                try {
                    $objText->Save();
                    QApplication::LogDebug(sprintf('Added text "%s" from the file "%s"', $strOriginal, $this->objFile->FileName));
                    NarroImportStatistics::$arrStatistics['Imported texts']++;
                } catch(Exception $objExc) {
                    QApplication::LogError(sprintf('Error while adding "%s": %s', $strOriginal, $objExc->getMessage()));
                    NarroImportStatistics::$arrStatistics['Skipped contexts']++;
                    NarroImportStatistics::$arrStatistics['Skipped suggestions']++;
                    NarroImportStatistics::$arrStatistics['Skipped texts']++;
                    NarroImportStatistics::$arrStatistics['Texts that had errors while adding']++;
                    /**
                     * If there's no text, there's no context and no suggestion
                     */
                    return false;
                }

            }
            elseif (!$objText instanceof NarroText) {
                /**
                 * If there's no text, there's no context and no suggestion
                 */
                return false;
            }

            $objContext = $this->GetContext($strOriginal, $strContext, trim($strComment));
            if (!$objContext)
                $objContext = NarroContext::LoadByTextIdContextMd5FileId($objText->TextId, md5($strContext), $this->objFile->FileId);

            if (!$this->blnOnlySuggestions && !$objContext instanceof NarroContext) {

                $objContext = new NarroContext();
                $objContext->TextId = $objText->TextId;
                $objContext->ProjectId = $this->objProject->ProjectId;
                $objContext->Context = $strContext;
                $objContext->ContextMd5 = md5($strContext);
                $objContext->FileId = $this->objFile->FileId;
                $objContext->Active = 1;
                $objContext->Modified = QDateTime::Now();
                $objContext->Created = QDateTime::Now();
                $objContext->Comment = trim($strComment);
                try {
                    $objContext->Save();
                }
                catch (Exception $objException) {
                    QApplication::LogError(sprintf('An error occurred while saving the context: %s. Skipping the text "%s"', $objException->getMessage(), $strOriginal));
                    return false;
                }

                QApplication::LogDebug(sprintf('Added the context "%s" from the file "%s"', nl2br($strContext), $this->objFile->FileName));
                NarroImportStatistics::$arrStatistics['Imported contexts']++;
            }
            elseif($objContext instanceof NarroContext) {
                unset($this->arrContextId[$objContext->ContextId]);
                NarroImportStatistics::$arrStatistics['Reused contexts']++;
            }
            else {
                QApplication::LogWarn(sprintf('Cannot add the context for text "%s" from the file "%s" because the importer is running with the option to import only suggestions.', $objText->TextValue, $this->objFile->FilePath));
                return false;
            }


            /**
             * load the context info
             */
            $objContextInfo = $this->GetContextInfo($strOriginal, $strContext);
            if (!$objContextInfo)
                $objContextInfo = NarroContextInfo::LoadByContextIdLanguageId($objContext->ContextId, $this->objTargetLanguage->LanguageId);

            /**
             * Add context infos even if only suggestion is selected to allow users that have permissions only on one language to approve suggestions
             */
            if (!$objContextInfo instanceof NarroContextInfo) {

                $objContextInfo = new NarroContextInfo();
                $objContextInfo->ContextId = $objContext->ContextId;
                $objContextInfo->LanguageId = $this->objTargetLanguage->LanguageId;
                $objContextInfo->HasSuggestions = 0;
                $objContextInfo->Created = QDateTime::Now();
                $blnContextInfoChanged = true;
            }
            elseif ($objContextInfo instanceof NarroContextInfo) {
                NarroImportStatistics::$arrStatistics['Reused context informations']++;
            }

            if ($objContext instanceof NarroContext && $objContextInfo instanceof NarroContextInfo) {
                /**
                 * this lies outside the if/else if reusing contexts is activated, so if a context was moved in another file, we'll just update the file_id
                 */
                if ($objContext->FileId != $this->objFile->FileId) {
                    $blnContextChanged = true;
                    $objContext->FileId = $this->objFile->FileId;
                }

                if ($objContextInfo->TextAccessKey != $strOriginalAccKey) {
                    $blnContextInfoChanged = true;
                    $objContextInfo->TextAccessKey = $strOriginalAccKey;
                }
            }

            if  ( $strTranslation == '' ) {
                /**
                 * just ignore, used for import without suggestions
                 */
                NarroImportStatistics::$arrStatistics['Texts without suggestions']++;
            }
            /**
             * if a translation is not empty and the suggestion is/isn't equal to the original
             */
            elseif ($this->blnCheckEqual && $strOriginal == $strTranslation)
            {
                QApplication::LogDebug(sprintf('Skipped "%s" because "%s" has the same value. From "%s".', $strOriginal, $strTranslation, $this->objFile->FileName));
                NarroImportStatistics::$arrStatistics['Skipped suggestions']++;
                NarroImportStatistics::$arrStatistics['Suggestions that kept the original text']++;
            }
            /**
             * Finally, we can process the suggestion if we got so far
             */
            else {
                /**
                 * See if a suggestion already exists, fetch it
                 */
                $objSuggestion = $this->GetSuggestion($strOriginal, $strTranslation);
                if (!$objSuggestion)
                    $objSuggestion = NarroSuggestion::LoadByTextIdLanguageIdSuggestionValueMd5($objText->TextId, $this->objTargetLanguage->LanguageId, md5($strTranslation));

                if (!$objSuggestion instanceof NarroSuggestion) {

                    $objSuggestion = new NarroSuggestion();
                    $objSuggestion->IsImported = 1;
                    $objSuggestion->UserId = $this->objUser->UserId;
                    $objSuggestion->TextId = $objText->TextId;
                    $objSuggestion->LanguageId = $this->objTargetLanguage->LanguageId;
                    $objSuggestion->SuggestionValue = $strTranslation;
                    try {
                        $objSuggestion->Save();
                    }
                    catch (Exception $objException) {
                        QApplication::LogError(sprintf('An error occurred while adding the suggestion "%s": %s. Skipping the text "%s"', $strTranslation, $objException->getMessage(), $strOriginal));
                        return false;
                    }


                    QApplication::$PluginHandler->AddSuggestion($strOriginal, $strTranslation, $strContext, $this->objFile, $this->objProject);

                    /**
                     * update the HasSuggestions if it was 0 and we added a suggestion
                     */
                    if ($objContextInfo instanceof NarroContextInfo && $objContextInfo->HasSuggestions == 0 && $objSuggestion instanceof NarroSuggestion ) {
                        $objContextInfo->HasSuggestions = 1;
                        $blnContextInfoChanged = true;
                    }

                    NarroImportStatistics::$arrStatistics['Imported suggestions']++;
                }
                else {
                    NarroImportStatistics::$arrStatistics['Reused suggestions']++;
                }

                if (
                        $objContextInfo instanceof NarroContextInfo &&
                        $this->blnApprove &&
                        (is_null($objContextInfo->ValidSuggestionId) || $this->blnApproveAlreadyApproved) &&
                        $objContextInfo->ValidSuggestionId != $objSuggestion->SuggestionId) {
                    $objContextInfo->ValidSuggestionId = $objSuggestion->SuggestionId;
                    $objContextInfo->ValidatorUserId = QApplication::GetUserId();
                    $blnContextInfoChanged = true;
                    NarroImportStatistics::$arrStatistics['Approved suggestions']++;
                }

                if ($objContextInfo instanceof NarroContextInfo && !is_null($strTranslationAccKey) && $objContextInfo->SuggestionAccessKey != $strTranslationAccKey) {
                    $blnContextInfoChanged = true;
                    $objContextInfo->SuggestionAccessKey = $strTranslationAccKey;
                }

            }

            if ($blnContextInfoChanged && $objContextInfo instanceof NarroContextInfo) {
                $objContextInfo->Modified = QDateTime::Now();
                try {
                    $objContextInfo->Save();
                } catch(Exception $objExc) {
                    QApplication::LogError(sprintf('Error while saving context info for context %s: %s', $strContext, $objExc->getMessage()));
                    NarroImportStatistics::$arrStatistics['Skipped context infos']++;
                }
            }

            return true;
        }

        /**
         * Get the context info for a certain context
         *
         * @param string $strText
         * @param string $strContext
         * @return NarroContextInfo
         */
        public function GetContextInfo($strOriginal, $strContext) {
            foreach($this->arrContextInfo as $objContextInfo) {
                if (
                    $objContextInfo->Context->ProjectId == $this->objProject->ProjectId &&
                    $objContextInfo->Context->FileId == $this->objFile->FileId &&
                    $objContextInfo->Context->Context == $strContext &&
                    $objContextInfo->Context->Text->TextValue == $strOriginal &&
                    $objContextInfo->LanguageId == $this->objTargetLanguage->LanguageId
                )
                    return $objContextInfo;
            }

            return false;
        }

        /**
         * Get the context for a certain text
         *
         * @param string $strText
         * @param string $strContext
         * @return NarroContext
         */
        public function GetContext($strOriginal, $strContext, $strComment) {
            foreach($this->arrContextInfo as $objContextInfo) {
                if (
                    $objContextInfo->Context->ProjectId == $this->objProject->ProjectId &&
                    $objContextInfo->Context->FileId == $this->objFile->FileId &&
                    $objContextInfo->Context->Context == $strContext &&
                    $objContextInfo->Context->Comment == $strComment &&
                    $objContextInfo->Context->Text->TextValue == $strOriginal &&
                    $objContextInfo->LanguageId == $this->objTargetLanguage->LanguageId
                )
                    return $objContextInfo->Context;
            }

            return false;
        }

        /**
         * Get the suggestion made by a specified user
         *
         * @param integer $intContextId
         * @param integer $intTextId
         * @param integer $intUserId
         * @return NarroSuggestion
         */
        public function GetUserSuggestion($intContextId, $intTextId, $intUserId) {
            $arrSuggestion = NarroSuggestion::QueryArray(
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroSuggestion()->UserId, $intUserId),
                            QQ::Equal(QQN::NarroSuggestion()->TextId, $intTextId)
                        )
            );

            if (count($arrSuggestion) == 1) {
                return $arrSuggestion[0];
            }
            elseif(count($arrSuggestion) > 1) {
                /**
                 * if there are more suggestions for a user, get the one that he has voted for
                 */
                $objSuggestionVote = NarroSuggestionVote::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId),
                        QQ::Equal(QQN::NarroSuggestionVote()->ContextId, $intContextId),
                        QQ::Equal(QQN::NarroSuggestionVote()->Context->NarroContextInfoAsContext->LanguageId, $this->objTargetLanguage->LanguageId)
                    )
                );

                if ($objSuggestionVote)
                    return NarroSuggestion::Load($objSuggestionVote->SuggestionId);
                else
                    /**
                     * the user has more suggestions but has voted none
                     */
                    return false;
            }
            else
                /**
                 * The user has no suggestions for this text
                 */
                return false;
        }

        /**
         * Get the most voted suggestion for a context
         *
         * @param integer $intContextId
         * @return NarroSuggestion
         */
        public function GetMostVotedSuggestion($intContextId) {
            $strQuery = sprintf(
                'SELECT suggestion_id, SUM(vote_value) as votes ' .
                'FROM narro_suggestion_vote, narro_context_info ' .
                'WHERE narro_context_info.context_id=narro_suggestion_vote.context_id AND narro_context_info.language_id=%d AND narro_suggestion_vote.context_id=%d ' .
                'GROUP BY suggestion_id ' .
                'ORDER BY votes DESC ' .
                'LIMIT 1',
                $this->objTargetLanguage->LanguageId,
                $intContextId
            );
            $objDatabase = QApplication::$Database[1];

            if (!$objDbResult = $objDatabase->Query($strQuery)) {
                QApplication::LogError('db_query failed. $strQuery=' . $strQuery);
                return false;
            }
            else {

                if ($objDbResult->CountRows()) {
                    $arrDbRow = $objDbResult->FetchArray();
                    return NarroSuggestion::Load($arrDbRow['suggestion_id']);
                }
                else {
                    QApplication::LogDebug(sprintf('There are no votes recorded for context_id=%d', $intContextId));
                    return false;
                }
            }
        }

        /**
         * Get the most recent suggestion for a context
         *
         * @param integer $intTextId
         * @return NarroSuggestion
         */
        public function GetMostRecentSuggestion($intTextId) {
            return
                NarroSuggestion::QuerySingle(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroSuggestion()->TextId, $intTextId),
                        QQ::Equal(QQN::NarroSuggestion()->LanguageId, $this->objTargetLanguage->LanguageId)
                    ),
                    array(QQ::OrderBy(QQN::NarroSuggestion()->Created, 0))
                );
        }

        /**
         * Returns a suggestion based on intExportedSuggestion value
         * @param NarroContextInfo $objContextInfo
         * @return string or false
         */
        public function GetExportedSuggestion(NarroContextInfo $objContextInfo) {
            switch($this->ExportedSuggestion) {
                case 1:
                    if ($objContextInfo->ValidSuggestionId)
                        return $objContextInfo->ValidSuggestion->SuggestionValue;
                    else
                        return false;
                /**
                 * If there is no approved suggestion, export the most voted one (minimum 1 vote required)
                 */
                case 2:
                    $objSuggestion = $this->GetMostVotedSuggestion($objContextInfo->ContextId);
                    if ($objSuggestion instanceof NarroSuggestion) {
                        QApplication::LogDebug(sprintf('Exporting most voted suggestion "%s" for "%s"', $objSuggestion->SuggestionValue, $objContextInfo->Context->Text->TextValue));
                        return $objSuggestion->SuggestionValue;
                    }
                    else {
                        return false;
                    }
                /**
                 * If there is no approved suggestion, export the most recent one added
                 */
                case 3:
                    $objSuggestion = $this->GetMostRecentSuggestion($objContextInfo->Context->TextId);
                    if ($objSuggestion instanceof NarroSuggestion) {
                        QApplication::LogDebug(sprintf('Exporting most recent suggestion "%s" for "%s"', $objSuggestion->SuggestionValue, $objContextInfo->Context->Text->TextValue));
                        return $objSuggestion->SuggestionValue;
                    }
                    else {
                        return false;
                    }
                /**
                 * If there is no approved suggestion, export the most voted one (minimum 1 vote required)
                 * If there is no voted suggestion, export the most recent one
                 */
                case 4:
                    $objSuggestion = $this->GetMostVotedSuggestion($objContextInfo->ContextId);
                    if ($objSuggestion instanceof NarroSuggestion) {
                        QApplication::LogDebug(sprintf('Exporting most voted suggestion "%s" for "%s"', $objSuggestion->SuggestionValue, $objContextInfo->Context->Text->TextValue));
                        return $objSuggestion->SuggestionValue;
                    }
                    else {
                        $objSuggestion = $this->GetMostRecentSuggestion($objContextInfo->Context->TextId);
                        if ($objSuggestion instanceof NarroSuggestion) {
                            QApplication::LogDebug(sprintf('Exporting most recent suggestion "%s" for "%s"', $objSuggestion->SuggestionValue, $objContextInfo->Context->Text->TextValue));
                            return $objSuggestion->SuggestionValue;
                        }
                        else {
                            return false;
                        }
                    }
                case 5:
                    $objSuggestion = $this->GetUserSuggestion($objContextInfo->ContextId, $objContextInfo->Context->TextId, QApplication::GetUserId());
                    if ($objSuggestion instanceof NarroSuggestion) {
                        QApplication::LogDebug(sprintf('Exporting %s\'s suggestion "%s" for "%s"', QApplication::$User->Username, $objSuggestion->SuggestionValue, $objContextInfo->Context->Text->TextValue));
                        return $objSuggestion->SuggestionValue;
                    }
                    else {
                        return false;
                    }
                default:
                    return false;
            }
        }

        /**
         * Preprocesses the whole file, e.g. removing trailing spaces
         * @param string $strFile file content
         * @return string
         */
        abstract protected function PreProcessFile($strFile);

        /**
         * Converts the file to an associative array
         * array(
         *     'key' => ''
         *     array(
         *         'text' => '',
         *         'comment' => '',
         *         'full_line' => '',
         *         'before_line' => ''
         *     )
         * );
         *
         * The key is something that must be unique to each text from that file; in most cases it can be the actual text
         * @param string $strFile file path
         * @return array
         */
        abstract protected function FileAsArray($strFilePath);

        /**
         * Tells whether the file is a comment
         * This function helps with comments that spread over multiple lines
         * @param string $strLine
         * @return boolean
         */
        abstract protected function IsComment($strLine);

        /**
         * Preprocesses the line if needed
         * e.g. in the source file there's a comment like '# #define MOZ_LANGPACK_CONTRIBUTORS that should be uncommented
         * @param string $strLine
         * @param array $arrComment
         * @param array $arrLinesBefore
         * @return array an array with the arguments received; processed if needed
         */
        abstract protected function PreProcessLine($strLine, $arrComment, $arrLinesBefore);

        /**
         * Process the line by splitting the $strLine in key=>value
         * array(array('key' => $strKey, 'value' => $strValue), $arrComment, $arrLinesBefore)
         * or
         * array(false, $arrComment, $arrLinesBefore)
         * @param string $strLine
         * @param array $arrComment
         * @param array $arrLinesBefore
         * @return array
         */
        abstract protected function ProcessLine($strLine, $arrComment, $arrLinesBefore);

        abstract public function ImportFile($strTemplateFile, $strTranslatedFile = null);
        abstract public function ExportFile($strTemplateFile, $strTranslatedFile);

        public function MarkUnusedContextsAsInactive() {
            if (count($this->arrContextId))
                NarroFile::GetDatabase()->NonQuery(
                    sprintf(
                        'UPDATE narro_context SET active=0 WHERE context_id IN (%s)',
                        join(',', $this->arrContextId)
                    )
                );
        }



        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {
                case "User": return $this->objUser;
                case "Project": return $this->objProject;
                case "SourceLanguage": return $this->objSourceLanguage;
                case "TargetLanguage": return $this->objTargetLanguage;
                case "Approve": return $this->blnApprove;
                case "ApproveAlreadyApproved": return $this->blnApproveAlreadyApproved;
                case "CheckEqual": return $this->blnCheckEqual;
                case "OnlySuggestions": return $this->blnOnlySuggestions;
                case "ExportedSuggestion": return $this->intExportedSuggestion;
                case "Logger": return QApplication::GetLogger();

                default: return false;
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {

            switch ($strName) {
                case "User":
                    if ($mixValue instanceof NarroUser) {
                        $this->objUser = $mixValue;
                        break;
                    }
                    else
                        throw new Exception(t('User should be set with an instance of NarroUser'));

                    break;

                case "Project":
                    if ($mixValue instanceof NarroProject) {
                        $this->objProject = $mixValue;
                        break;
                    }
                    else
                        throw new Exception(t('Project should be set with an instance of NarroProject'));

                    break;

                case "File":
                    if ($mixValue instanceof NarroFile) {
                        $this->objFile = $mixValue;
                        $this->GetContextInfoArray();
                        $this->GetSugestionArray();
                        QApplication::LogDebug(
                            sprintf(
                                'Processing "%s" with blnCheckEqual=%s, blnApprove=%s, blnApproveAlreadyApproved=%s, blnOnlySuggestions=%s, intExportedSuggestion=%s',
                                $mixValue->FileName,
                                (int) $this->blnCheckEqual,
                                (int) $this->blnApprove,
                                (int) $this->blnApproveAlreadyApproved,
                                (int) $this->blnOnlySuggestions,
                                $this->intExportedSuggestion
                            )
                        );
                        break;
                    }
                    else
                        throw new Exception(t('File should be set with an instance of NarroFile'));

                    break;

                case "TargetLanguage":
                    if ($mixValue instanceof NarroLanguage) {
                        $this->objTargetLanguage = $mixValue;
                        break;
                    }
                    else
                        throw new Exception(t('TargetLanguage should be set with an instance of NarroLanguage'));

                    break;

                case "SourceLanguage":
                    if ($mixValue instanceof NarroLanguage) {
                        $this->objSourceLanguage = $mixValue;
                        return false;
                    }
                    else
                        throw new Exception(t('SourceLanguage should be set with an instance of NarroLanguage'));

                    break;


                case "Approve":
                    try {
                        $this->blnApprove = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "ApproveAlreadyApproved":
                    try {
                        $this->blnApproveAlreadyApproved = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "CheckEqual":
                    try {
                        $this->blnCheckEqual = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "OnlySuggestions":
                    try {
                        $this->blnOnlySuggestions = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "ExportedSuggestion":
                    try {
                        $this->intExportedSuggestion = QType::Cast($mixValue, QType::Integer);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    return false;
            }
        }
    }

?>
