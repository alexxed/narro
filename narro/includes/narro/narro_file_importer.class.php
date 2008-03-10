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

    class NarroFileImporter {
        /**
         * the user id used for the suggestions stored, defaults to 1, anonymous user
         */
        protected $objUser;
        protected $objLanguage;
        protected $objProject;

        protected $blnLogOutput = true;
        protected $blnEchoOutput = true;
        protected $hndLogFile;
        protected $strLogFile;
        protected $intMinLogLevel;

        protected $arrStatistics;

        protected $blnCheckEqual = true;
        protected $blnValidate = true;
        protected $blnOnlySuggestions = false;


        protected function startTimer() {
            $this->arrStatistics['Start time'] = time();
        }

        protected function stopTimer() {
            $this->arrStatistics['End time'] = time();
        }

        public function Output($intMessageType, $strText) {
            if ($intMessageType < $this->intMinLogLevel)
                return false;

            if ($this->blnEchoOutput)
                echo $strText . "\n";

            if ($this->blnLogOutput) {
                if ($this->strLogFile)
                    $this->OutputLog($intMessageType, $strText);
                else
                    error_log($strText);
            }
        }

        protected function OutputLog($intMessageType, $strText) {

            if (!$this->hndLogFile)
                $this->hndLogFile = fopen($this->strLogFile, 'a+');

            if ($this->hndLogFile)
                fputs($this->hndLogFile, $strText . "\n");
            else
                error_log($strText);

        }


        /**
         * A translation here consists of the project, file, text, translation, context, plurals, validation, ignore equals
         *
         * @param NarroFile $objFile
         * @param string $strOriginal the original text
         * @param string $strTranslation the translated text from the import file (can be empty)
         * @param string $strContext the context where the text/transaltion appears in the file
         * @param string $intPluralForm if this is a plural, what plural form is it (0 singular, 1 plural form 1, and so on)
         */
        protected function AddTranslation(NarroFile $objFile, $strOriginal, $strTranslation, $strContext, $intPluralForm = null) {
            //$arrArgs = func_get_args();
            //$this->Output(1, __FUNCTION__ . var_export($arrArgs,true));

            /**
             * Avoid trimming the strings to preserve spaces; let the plugins handle eventual processing needs
             */
            $strOriginal = QApplication::$objPluginHandler->ProcessText($strOriginal);
            $strTranslation = QApplication::$objPluginHandler->ProcessSuggestion($strTranslation);
            $strContext = QApplication::$objPluginHandler->ProcessContext($strContext);
            if ($strContext == '') {
                $this->Output(2, sprintf(QApplication::Translate('In file "%s", the context "%s" was skipped because it was empty.'), $objFile->FileName, $strContext));
                $this->arrStatistics['Skipped contexts']++;
                $this->arrStatistics['Skipped suggestions']++;
                $this->arrStatistics['Skipped texts']++;
                return false;
            }

            if ($strOriginal == '') {
                $this->Output(2, sprintf(QApplication::Translate('In file "%s", the context "%s" was skipped because the original text "%s" was empty.'), $objFile->FileName, $strContext, $strOriginal));
                $this->arrStatistics['Skipped contexts']++;
                $this->arrStatistics['Skipped suggestions']++;
                $this->arrStatistics['Skipped texts']++;
                $this->arrStatistics['Empty original texts']++;
                return false;
            }

            /**
             * Fetch the text by its md5; we could fetch it by the full text but it would be slower
             * @example $objNarroText = NarroText::QuerySingle(QQ::Equal(QQN::NarroText()->TextValue, mysql_real_escape_string($strOriginal)));
             */
            $objNarroText = NarroText::QuerySingle(QQ::Equal(QQN::NarroText()->TextValueMd5, md5($strOriginal)));

            if (!$objNarroText instanceof NarroText) {
                if ($this->blnOnlySuggestions) return false;
                $objNarroText = new NarroText();
                $objNarroText->TextValue = $strOriginal;
                $objNarroText->TextValueMd5 = md5($strOriginal);
                $objNarroText->TextCharCount = mb_strlen($strOriginal);
                try {
                    $objNarroText->Save();
                    $this->Output(1, sprintf(QApplication::Translate('Added text "%s" from the file "%s"'), $strOriginal, $objFile->FileName));
                    $this->arrStatistics['Imported texts']++;
                } catch(Exception $objExc) {
                    $this->Output(3, sprintf(QApplication::Translate('Error while adding "%s": %s'), $strOriginal, $objExc->getMessage()));
                    $this->arrStatistics['Skipped contexts']++;
                    $this->arrStatistics['Skipped suggestions']++;
                    $this->arrStatistics['Skipped texts']++;
                    $this->arrStatistics['Texts that had errors while adding']++;
                    /**
                     * If there's no text, there's no context and no suggestion
                     */
                    return false;
                }
            }

            /**
             * fetch the context by fileid, projectid, textid and context string
             */
            $objNarroContext = NarroContext::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroContext()->TextId, $objNarroText->TextId),
                                        /**
                                         * If you change the file structure, and would like to reuse contexts, you might want to comment the following line
                                         */
                                        QQ::Equal(QQN::NarroContext()->FileId, $objFile->FileId),
                                        QQ::Equal(QQN::NarroContext()->ProjectId, $this->objProject->ProjectId),
                                        QQ::Equal(QQN::NarroContext()->Context, $strContext)
                                    )
                                );

            if (!$objNarroContext instanceof NarroContext) {

                if ($this->blnOnlySuggestions) return false;

                $objNarroContext = new NarroContext();
                $objNarroContext->TextId = $objNarroText->TextId;
                $objNarroContext->ProjectId = $this->objProject->ProjectId;
                $objNarroContext->Context = $strContext;
                $objNarroContext->FileId = $objFile->FileId;
                $objNarroContext->Active = 1;
                $objNarroContext->Save();
                $this->Output(1, sprintf(QApplication::Translate('Added the context "%s" from the file "%s"'), $strContext, $objFile->FileName));
                $this->arrStatistics['Imported contexts']++;
            }
            else {
                $this->arrStatistics['Reused contexts']++;
            }

            $objContextInfo = NarroContextInfo::LoadByContextIdLanguageId($objNarroContext->ContextId, $this->objLanguage->LanguageId);

            if (!$objContextInfo instanceof NarroContextInfo) {
                $objContextInfo = new NarroContextInfo();
                $objContextInfo->ContextId = $objNarroContext->ContextId;
                $objContextInfo->LanguageId = $this->objLanguage->LanguageId;
            }


            /**
             * this lies outside the if/else if reusing contexts is activated, so if a context was moved in another file, we'll just update the file_id
             */
            $objNarroContext->FileId = $objFile->FileId;

            /**
             * if a translation is not empty and equal checking is required and missed, go ahead with the suggestion
             */
            if ($strTranslation != '' && !($this->blnCheckEqual && strlen($strOriginal)>1 && $strOriginal == $strTranslation)) {

                /**
                 * See if a suggesstion already exists, fetch it by its md5 and text_id
                 */
                $objNarroSuggestion = NarroSuggestion::QuerySingle(
                                            QQ::AndCondition(
                                                QQ::Equal(QQN::NarroSuggestion()->TextId, $objNarroText->TextId),
                                                QQ::Equal(QQN::NarroSuggestion()->LanguageId, $this->objLanguage->LanguageId),
                                                QQ::Equal(QQN::NarroSuggestion()->SuggestionValueMd5, md5($strTranslation))
                                            )
                );

                if (!$objNarroSuggestion instanceof NarroSuggestion) {
                    $objNarroSuggestion = new NarroSuggestion();
                    $objNarroSuggestion->UserId = $this->objUser->UserId;
                    $objNarroSuggestion->TextId = $objNarroText->TextId;
                    $objNarroSuggestion->LanguageId = $this->objLanguage->LanguageId;
                    $objNarroSuggestion->SuggestionValue = $strTranslation;
                    $objNarroSuggestion->SuggestionValueMd5 = md5($strTranslation);
                    $objNarroSuggestion->SuggestionCharCount = mb_strlen($strTranslation);
                    $objNarroSuggestion->Save();
                    $this->arrStatistics['Imported suggestions']++;
                }
                else {
                    $this->arrStatistics['Reused suggestions']++;
                }

                if ($this->blnValidate) {
                    $objContextInfo->ValidSuggestionId = $objNarroSuggestion->SuggestionId;
                    $this->arrStatistics['Validated suggestions']++;
                }
            }
            else {
                if ($strTranslation != '') {
                    $this->Output(1, sprintf(QApplication::Translate('Skipped "%s" because "%s" has the same value. From "%s".'), $strOriginal, $strTranslation, $objFile->FileName));
                    $this->arrStatistics['Skipped suggestions']++;
                    $this->arrStatistics['Suggestions that kept the original text']++;
                }
                else {
                    /**
                     * just ignore, used for import without suggestions
                     */
                    $this->arrStatistics['Texts without suggestions']++;
                }
            }

            if ($objContextInfo->HasSuggestions != 1) {
                $intSuggestionCnt = NarroSuggestion::QueryCount(
                                        QQ::AndCondition(
                                            QQ::Equal(
                                                QQN::NarroSuggestion()->TextId,
                                                $objNarroText->TextId
                                            ),
                                            QQ::Equal(
                                                QQN::NarroSuggestion()->LanguageId,
                                                $this->objLanguage->LanguageId
                                            )
                                        )
                );

                $objContextInfo->HasSuggestions = ($intSuggestionCnt && $intSuggestionCnt>0)?1:0;
            }

            if ($objNarroContext instanceof NarroContext) {
                try {
                    $objNarroContext->Active = 1;
                    $objNarroContext->Save();
                } catch(Exception $objExc) {
                    $this->Output(3, sprintf(__t('Error while setting context "%s" to active: %s'), $strContext, $objExc->getMessage()));
                    $this->arrStatistics['Skipped contexts']++;
                }
            }

            if ($objContextInfo instanceof NarroContextInfo) {
                try {
                    if (!is_null($intPluralForm)) {
                        $objContextInfo->HasPlural = 1;
                    }
                    else {
                        $objContextInfo->HasPlural = 0;
                    }
                    $objContextInfo->Save();
                } catch(Exception $objExc) {
                    $this->Output(3, sprintf(__t('Error while saving context info for context %s: %s'), $strContext, $objExc->getMessage()));
                    $this->arrStatistics['Skipped context infos']++;
                }
            }


            /**
             * @todo update this piece to the new db structure
             */
            if (!is_null($intPluralForm)) {
                $objNarroPlural = NarroContextPlural::QuerySingle(QQ::Equal(QQN::NarroContextPlural()->ContextId, $objNarroContext->ContextId));

                if (!$objNarroPlural instanceof NarroContextPlural) {
                    if ($this->blnOnlySuggestions) return false;
                    $objNarroPlural = new NarroContextPlural();
                }

                $objNarroPlural->ContextId = $objNarroContext->ContextId;
                $objNarroPlural->PluralForm = $intPluralForm;
                /**
                 * do this only if changed?
                 */
                $objNarroPlural->Save();
            }

            return true;
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {
                case "User": return $this->objUser;
                case "Project": return $this->objProject;
                case "Language": return $this->objLanguage;
                case "Validate": return $this->blnValidate;
                case "CheckEqual": return $this->blnCheckEqual;
                case "OnlySuggestions": return $this->blnOnlySuggestions;
                case "LogOutput": return $this->blnLogOutput;
                case "EchoOutput": return $this->blnEchoOutput;
                case "LogFile": return $this->strLogFile;
                case "Statistics": return $this->arrStatistics;
                case "MinLogLevel": return $this->intMinLogLevel;

                default: return false;
            }
        }

        /////////////////////////
        // Public Properties: SET
        /////////////////////////
        public function __set($strName, $mixValue) {

            switch ($strName) {
                case "User":
                    if ($mixValue instanceof NarroUser)
                        $this->objUser = $mixValue;
                    else
                        throw new Exception(__t('User should be set with an instance of NarroUser'));

                    break;

                case "Project":
                    if ($mixValue instanceof NarroProject)
                        $this->objProject = $mixValue;
                    else
                        throw new Exception(__t('Project should be set with an instance of NarroProject'));

                    break;

                case "Language":
                    if ($mixValue instanceof NarroLanguage)
                        $this->objLanguage = $mixValue;
                    else
                        throw new Exception(__t('Language should be set with an instance of NarroLanguage'));

                    break;

                case "Validate":
                    try {
                        $this->blnValidate = QType::Cast($mixValue, QType::Boolean);
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

                case "EchoOutput":
                    try {
                        $this->blnEchoOutput = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "LogOutput":
                    try {
                        $this->blnLogOutput = QType::Cast($mixValue, QType::Boolean);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "MinLogLevel":
                    try {
                        $this->intMinLogLevel = QType::Cast($mixValue, QType::Integer);
                        break;
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                case "LogFile":
                    try {
                        $this->intLogLevel = QType::Cast($mixValue, QType::String);
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
