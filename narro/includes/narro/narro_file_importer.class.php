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
        public $blnLogOutput = true;
        public $blnEchoOutput = true;

        protected $hndLogFile;
        public $strLogFile = 'importer.log';

        protected $arrStatistics;

        protected function startTimer() {
            $this->arrStatistics['Start time'] = time();
        }

        protected function stopTimer() {
            $this->arrStatistics['End time'] = time();

            if ($this->arrStatistics['Time passed'] > 3600) {
                $this->arrStatistics['elapsed_time_string'] = floor($this->arrStatistics['Time passed']/3600) . 'h, ' . floor(($this->arrStatistics['Time passed']%3600) / 60) . 'm';
            }
            elseif ($this->arrStatistics['Time passed'] > 60) {
                $this->arrStatistics['elapsed_time_string'] = floor($this->arrStatistics['Start time'] / 60) . 'm, ' . floor($this->arrStatistics['Time passed']%60) . 's';
            }
            else {
                $this->arrStatistics['elapsed_time_string'] = $this->arrStatistics['Start time'] . 's';
            }

        }

        public function Output($strText) {

            if ($this->blnEchoOutput)
                error_log($strText);

            if ($this->blnLogOutput)
                $this->OutputLog($strText);
        }

        public function OutputLog($strText) {

            if (!$this->hndLogFile)
                $this->hndLogFile = fopen($this->strLogFile, 'a+');

            fputs($this->hndLogFile, $strText . "\n");
        }


        /**
         * @param bool $blnCheckEqual check if the translation is equal to original text.
         */
        protected function AddTranslation($intProjectId, $objFile, $strOriginal, $strTranslation, $strContext, $intPluralForm, $blnValidate = false, $blnCheckEqual = false) {
            //$arrArgs = func_get_args();
            //$this->Output(__FUNCTION__ . var_export($arrArgs,true));
            //if ($blnCheckEqual && $strOriginal == $strTranslation)
                //$strTranslation = '';
            //echo $objFile->FileName . '|' . $strContext . '|' . $strOriginal . '|' . $strTranslation . "\n";
            //return true;
            $strOriginal = trim($strOriginal);
            $strTranslation = trim(QApplication::ConvertToSedila($strTranslation));
            $strContext = trim($strContext);

            if ($strOriginal == '') {
                $this->OutputLog(sprintf('S-a sărit peste „%s” pentru că textul original „%s” era gol. Din „%s”', $strContext, $strOriginal, $objFile->FileName));
                $this->arrStatistics['Skipped contexts']++;
                $this->arrStatistics['Empty original texts']++;
                return false;
            }

            //$objNarroText = NarroText::QuerySingle(QQ::Equal(QQN::NarroText()->TextValue, mysql_real_escape_string($strOriginal)));
            /**
             * fetch the text by its md5
             */
            $objNarroText = NarroText::QuerySingle(QQ::Equal(QQN::NarroText()->TextValueMd5, md5($strOriginal)));
            if (!$objNarroText instanceof NarroText) {
                $objNarroText = new NarroText();
                $objNarroText->TextValue = $strOriginal;
                $objNarroText->TextValueMd5 = md5($strOriginal);
                $objNarroText->TextCharCount = strlen($strOriginal);
                try {
                    $objNarroText->Save();
                    //$this->OutputLog(sprintf('S-a adăugat textul „%s” din fișierul „%s”', $strOriginal, $objFile->FileName));
                    $this->arrStatistics['Imported texts']++;
                } catch(Exception $objExc) {
                    $this->Output(sprintf('Atenție, eroare la adăugarea „%s”: %s', $strOriginal, $objExc->getMessage()));
                    $this->arrStatistics['Skipped texts']++;
                    $this->arrStatistics['Texts that had errors while adding']++;
                    continue;
                }
            }

            /**
             * fetch the context by fileid, textid and context string
             * project id is not necessary since is unique and is tied to the file
             */
            $objNarroContext = NarroTextContext::QuerySingle(
                                    QQ::AndCondition(
                                        QQ::Equal(QQN::NarroTextContext()->TextId, $objNarroText->TextId),
                                        /**
                                         * Very important! IF you change the file structure, comment the following line
                                         */
                                        QQ::Equal(QQN::NarroTextContext()->FileId, $objFile->FileId),
                                        QQ::Equal(QQN::NarroTextContext()->ProjectId, $intProjectId),
                                        QQ::Equal(QQN::NarroTextContext()->Context, $strContext)
                                    )
                                );

            if (!$objNarroContext instanceof NarroTextContext) {
                $objNarroContext = new NarroTextContext();
                $objNarroContext->TextId = $objNarroText->TextId;
                $objNarroContext->ProjectId = $intProjectId;
                $objNarroContext->Context = $strContext;
                $objNarroContext->Translatable = 1;
                $objNarroContext->IsFuzzy = 0;
                $this->OutputLog(sprintf('S-a adăugat contextul „%s” din fișierul „%s”', $strContext, $objFile->FileName));
                $this->arrStatistics['Imported contexts']++;
            }
            else {
                $this->arrStatistics['Skipped contexts']++;
            }

            $objNarroContext->FileId = $objFile->FileId;

            if ($strTranslation != '' && !($blnCheckEqual && strlen($strOriginal)>1 && $strOriginal == $strTranslation)) {

                /**
                 * See if a suggesstion already exists
                 */
                $objNarroSuggestion = NarroTextSuggestion::QuerySingle(
                                            QQ::AndCondition(
                                                QQ::Equal(QQN::NarroTextSuggestion()->TextId, $objNarroText->TextId),
                                                QQ::Equal(QQN::NarroTextSuggestion()->SuggestionValue, $strTranslation),
                                                QQ::Equal(QQN::NarroTextSuggestion()->SuggestionValueMd5, md5($strTranslation))
                                            )
                );

                if (!$objNarroSuggestion instanceof NarroTextSuggestion) {
                    $objNarroSuggestion = new NarroTextSuggestion();
                    $objNarroSuggestion->UserId = 9;
                    $objNarroSuggestion->TextId = $objNarroText->TextId;
                    $objNarroSuggestion->SuggestionValue = $strTranslation;
                    $objNarroSuggestion->SuggestionValueMd5 = md5($strTranslation);
                    $objNarroSuggestion->Save();
                    $this->arrStatistics['Imported suggestions']++;
                }
                else {
                    $this->arrStatistics['Unchanged suggestions']++;
                }

                if ($blnValidate) {
                    $objNarroContext->ValidSuggestionId = $objNarroSuggestion->SuggestionId;
                    $this->arrStatistics['Validated suggestions']++;
                }
            }
            else {
                if ($strTranslation != '') {
                    $this->OutputLog(sprintf('S-a sărit peste „%s” pentru că „%s” are aceaşi valoare. Din „%s”.', $strOriginal, $strTranslation, $objFile->FileName));
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

            $intSuggestionCnt = NarroTextSuggestion::CountByTextId($objNarroText->TextId);
            $objNarroContext->HasSuggestion = ($intSuggestionCnt && $intSuggestionCnt>0)?1:0;

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
                    $this->Output(sprintf('Atenție, eroare la adăugarea contextului „%s”: %s', $strContext, $objExc->getMessage()));
                    $this->intSkippedContextsCount++;
                }
            }



            if (!is_null($intPluralForm)) {
                $objNarroPlural = NarroTextContextPlural::QuerySingle(QQ::Equal(QQN::NarroTextContextPlural()->ContextId, $objNarroContext->ContextId));

                if (!$objNarroPlural instanceof NarroTextContextPlural)
                    $objNarroPlural = new NarroTextContextPlural();

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
