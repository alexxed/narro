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
        protected $intUserId = 1;
        protected $intLanguageId = 1;
        protected $blnLogOutput = true;
        protected $blnEchoOutput = true;

        protected $hndLogFile;
        protected $strLogFile;

        protected $arrStatistics;

        protected function startTimer() {
            $this->arrStatistics['Start time'] = time();
        }

        protected function stopTimer() {
            $this->arrStatistics['End time'] = time();
        }

        public function Output($intMessageType, $strText) {

            if ($this->blnEchoOutput)
                echo $strText;

            if ($this->blnLogOutput) {
                if ($this->strLogFile)
                    $this->OutputLog($intMessageType, $strText);
                else
                    error_log($strText);
            }
        }

        public function OutputLog($intMessageType, $strText) {

            if (!$this->hndLogFile)
                $this->hndLogFile = fopen($this->strLogFile, 'a+');

            fputs($this->hndLogFile, $strText . "\n");
        }


        /**
         * A translation here consists of the project, file, text, translation, context, plurals, validation, ignore equals
         *
         * @param integer $intProjectId
         * @param NarroFile $objFile
         * @param string $strOriginal the original text
         * @param string $strTranslation the translated text from the import file (can be empty)
         * @param string $strContext the context where the text/transaltion appears in the file
         * @param string $intPluralForm if this is a plural, what plural form is it (0 singular, 1 plural form 1, and so on)
         * @param bool $blnValidate validated the translation
         * @param bool $blnCheckEqual check if the translation is equal to original text and don't import it if it is
         */
        protected function AddTranslation($intProjectId, NarroFile $objFile, $strOriginal, $strTranslation, $strContext, $intPluralForm, $blnCheckEqual = false, $blnValidate = false, $blnOnlySuggestions = false) {
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
                if ($blnOnlySuggestions) return false;
                $objNarroText = new NarroText();
                $objNarroText->TextValue = $strOriginal;
                $objNarroText->TextValueMd5 = md5($strOriginal);
                $objNarroText->TextCharCount = strlen($strOriginal);
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
            $objNarroContext = NarroTextContext::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroTextContext()->TextId, $objNarroText->TextId),
                                        /**
                                         * If you change the file structure, and would like to reuse contexts, you might want to comment the following line
                                         */
                                        QQ::Equal(QQN::NarroTextContext()->FileId, $objFile->FileId),
                                        QQ::Equal(QQN::NarroTextContext()->ProjectId, $intProjectId),
                                        QQ::Equal(QQN::NarroTextContext()->Context, $strContext)
                                    )
                                );

            if (!$objNarroContext instanceof NarroTextContext) {
                if ($blnOnlySuggestions) return false;
                $objNarroContext = new NarroTextContext();
                $objNarroContext->TextId = $objNarroText->TextId;
                $objNarroContext->ProjectId = $intProjectId;
                $objNarroContext->Context = $strContext;
                $objNarroContext->Translatable = 1;
                $this->Output(1, sprintf(QApplication::Translate('Added the context "%s" from the file "%s"'), $strContext, $objFile->FileName));
                $this->arrStatistics['Imported contexts']++;
            }
            else {
                $this->arrStatistics['Reused contexts']++;
            }

            /**
             * this lies outside the if/else if reusing contexts is activated, so if a context was moved in another file, we'll just update the file_id
             */
            $objNarroContext->FileId = $objFile->FileId;

            /**
             * if a translation is not empty and equal checking is required and missed, go ahead with the suggestion
             */
            if ($strTranslation != '' && !($blnCheckEqual && strlen($strOriginal)>1 && $strOriginal == $strTranslation)) {

                /**
                 * See if a suggesstion already exists, fetch it by its md5 and text_id
                 */
                $objNarroSuggestion = NarroTextSuggestion::QuerySingle(
                                            QQ::AndCondition(
                                                QQ::Equal(QQN::NarroTextSuggestion()->TextId, $objNarroText->TextId),
                                                QQ::Equal(QQN::NarroTextSuggestion()->SuggestionValueMd5, md5($strTranslation))
                                            )
                );

                if (!$objNarroSuggestion instanceof NarroTextSuggestion) {
                    $objNarroSuggestion = new NarroTextSuggestion();
                    $objNarroSuggestion->UserId = $this->intUserId;
                    $objNarroSuggestion->LanguageId = $this->intLanguageId;
                    $objNarroSuggestion->TextId = $objNarroText->TextId;
                    $objNarroSuggestion->SuggestionValue = $strTranslation;
                    $objNarroSuggestion->SuggestionValueMd5 = md5($strTranslation);
                    $objNarroSuggestion->SuggestionCharCount = strlen($strTranslation);
                    $objNarroSuggestion->Save();
                    $this->arrStatistics['Imported suggestions']++;
                }
                else {
                    $this->arrStatistics['Reused suggestions']++;
                }

                if ($blnValidate) {
                    $objNarroContext->ValidSuggestionId = $objNarroSuggestion->SuggestionId;
                    $this->arrStatistics['Validated suggestions']++;
                }
            }
            else {
                if ($strTranslation != '') {
                    $this->Output(2, sprintf(QApplication::Translate('Skipped "%s" because "%s" has the same value. From "%s".'), $strOriginal, $strTranslation, $objFile->FileName));
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

            if ($objNarroContext->HasSuggestion != 1) {
                $intSuggestionCnt = NarroTextSuggestion::CountByTextId($objNarroText->TextId);
                $objNarroContext->HasSuggestion = ($intSuggestionCnt && $intSuggestionCnt>0)?1:0;
            }

            if ($objNarroContext instanceof NarroTextContext) {
                try {
                    $objNarroContext->Active = 1;
                    if (!is_null($intPluralForm)) {
                        $objNarroContext->HasPlural = 1;
                    }
                    else {
                        $objNarroContext->HasPlural = 0;
                    }
                    $objNarroContext->Save();
                } catch(Exception $objExc) {
                    $this->Output(3, sprintf(QApplication::Translate('Error while setting context "%s" to active: %s'), $strContext, $objExc->getMessage()));
                    $this->intSkippedContextsCount++;
                }
            }



            if (!is_null($intPluralForm)) {
                $objNarroPlural = NarroTextContextPlural::QuerySingle(QQ::Equal(QQN::NarroTextContextPlural()->ContextId, $objNarroContext->ContextId));

                if (!$objNarroPlural instanceof NarroTextContextPlural) {
                    if ($blnOnlySuggestions) return false;
                    $objNarroPlural = new NarroTextContextPlural();
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



    }

?>
