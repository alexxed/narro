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
    abstract class NarroMozillaFileImporter extends NarroFileImporter {
        protected function FileAsArray($strFile) {
            $intTime = time();

            if (!file_exists($strFile)) {
                QApplication::LogInfo(sprintf(t('%s does not exist'), $strFile));
                return false;
            }

            $strFileContent = file_get_contents($strFile);
            if (!$strFileContent) {
                QApplication::LogInfo(sprintf(t('%s is empty'), $strFile));
                return false;
            }

            $strFileContent = $this->PreProcessFile($strFileContent);

            $arrFile = explode("\n", $strFileContent);

            unset($strFileContent);

            $arrKeys = array();
            $strPreviousLines = '';
            $strFileHeader = '';
            $blnFirstEntityFound = false;
            $blnHeaderFound = false;

            foreach($arrFile as $intPos=>$strLine) {
                if ($blnHeaderFound == false && $blnFirstEntityFound == false && $strLine == '' && $strPreviousLines != '') {
                    $this->objFile->Header = $strPreviousLines;
                    $this->objFile->Save();
                    $blnHeaderFound = true;

                    $strPreviousLines = '';
                }

                $strPreProcessedLine = $this->PreProcessLine($strLine);
                $strLineToProcess = $strPreviousLines . $strPreProcessedLine;

                $mixResult = $this->ProcessLine($strLineToProcess);

                if ($mixResult instanceof NarroFileEntity) {
                    $strLastKey = $mixResult->Key;
                    $arrKeys[$mixResult->Key] = $mixResult;
                    $strPreviousLines = '';
                    $blnFirstEntityFound = true;
                }
                else {
                    $strPreviousLines = $strPreviousLines . $strLine . "\n";
                }
            }
            
            if (isset($strLastKey))
                $arrKeys[$strLastKey]->AfterValue .= $strLineToProcess;

            QApplication::LogDebug(sprintf('Converted file to array in %s second(s)', (time() - $intTime)));

            return $arrKeys;
        }


        /**
         * This function looks for accesskey entries and creates po style texts, e.g. &File
         * @param array $arrTexts an array with context as keys and texts as values
         */
        public function GetAccessKeys($arrTexts) {
            if (is_array($arrTexts)) {
                foreach($arrTexts as $strContext=>$objEntity) {
                    $strAccKey = $objEntity->Value;
                    if (stristr($strContext, 'accesskey')) {
                        /**
                         * if this is an accesskey, look for the label
                         * until now the following label and accesskeys are matched:
                         *
                         * ctx.label / ctx.acesskey
                         * ctxLabel / ctxAccesskey
                         * ctx / ctx.accesskey
                         *
                         * and so on
                         */
                        $arrMatches = array();
                        $strLabelCtx = false;
                        $strNewAcc = false;

                        if (preg_match('/([A-Z0-9a-z\.\_\-]+)([\.\-\_]a|[\.\-\_]{0,1}A)ccesskey$/s', $strContext, $arrMatches)) {
                            $arrMatches[2] = str_replace('a', '', $arrMatches[2]);

                            if (isset($arrTexts[$arrMatches[1] . $arrMatches[2] . 'label']))
                                $strLabelCtx = $arrMatches[1] . $arrMatches[2] . 'label';
                            elseif (isset($arrTexts[$arrMatches[1] . $arrMatches[2] . 'message']))
                                $strLabelCtx = $arrMatches[1] . $arrMatches[2] . 'message';
                            elseif (isset($arrTexts[$arrMatches[1] . $arrMatches[2] . 'title']))
                                $strLabelCtx = $arrMatches[1] . $arrMatches[2] . 'title';
                            elseif (isset($arrTexts[$arrMatches[1] . 'Label']))
                                $strLabelCtx = $arrMatches[1] . 'Label';
                            elseif (isset($arrTexts[$arrMatches[1]]))
                                $strLabelCtx = $arrMatches[1];
                            else {
                                $strLabelCtx = '';
                                QApplication::LogDebug(sprintf('Found acesskey %s in context %s but didn\'t find any label to match "%s" (.label, Label, etc).', $strAccKey, $strContext, $arrMatches[1]));
                                continue;
                            }

                            if ($strLabelCtx) {
                                QApplication::LogDebug(sprintf('Found label context "%s", looking for an acceptable access key', $strLabelCtx));
                                /**
                                 * strip mozilla entities when looking for an acceptable access key
                                 */
                                $strOriginalText = preg_replace('/&[^;]+;/', '', $arrTexts[$strLabelCtx]->Value);
                                /**
                                 * search for the accesskey in the label
                                 * the case of the access keys doesn't matter in Mozilla, so it's a insensitive search
                                 */
                                $intPos = @mb_stripos( $strOriginalText, $strAccKey);
                                if ($intPos !== false) {
                                    /**
                                     * Try to keep the case at import if possible
                                     */
                                    $intKeySensitivePos = mb_strpos($strOriginalText, $strAccKey);
                                    if ($intKeySensitivePos !== false)
                                        $intPos = $intKeySensitivePos;

                                    $arrTexts[$strLabelCtx]->AccessKey = mb_substr($strOriginalText, $intPos, 1);
                                    QApplication::LogDebug(sprintf('Found access key %s, using it', $arrTexts[$strLabelCtx]->AccessKey));
                                }
                                elseif (preg_match('/[a-z]/i', $strOriginalText, $arrMatches)) {
                                    $arrTexts[$strLabelCtx]->AccessKey = $arrMatches[0];
                                    QApplication::LogDebug(sprintf('Using as access key the first ascii letter from the translation, %s', $arrMatches[0]));
                                } else {
                                    $arrTexts[$strLabelCtx]->AccessKey = $strAccKey;
                                    QApplication::LogWarn(sprintf('No acceptable access key found for context "%s", text "%s", leaving the original.', $strLabelCtx, $strOriginalText));
                                }

                                $arrTexts[$strContext]->LabelCtx = $strLabelCtx;
                                $arrTexts[$strLabelCtx]->AccessKeyCtx = $strContext;

                            }
                            else {
                                QApplication::LogWarn(sprintf('Found acesskey %s in context %s but didn\'t find any label to match "%s" (.label, Label, etc). Importing it as a text.', $strAccKey, $strContext, $arrMatches[1]));
                                continue;
                            }
                        }
                    }
                    else
                        continue;
                }
            }

            return $arrTexts;


        }

        /**
         * This function does the opposite of GetAccessKeys
         * @param array $arrTemplate an array with context as keys and original texts as values
         * @return array $arrTranslation an array with context as keys and translations as values
         */
        public function GetTranslations($arrTemplate) {
            $arrTranslation = array();

            $arrTranslationObjects =
                NarroContextInfo::QueryArray(
                    QQ::AndCondition(
                        QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objFile->FileId),
                        QQ::Equal(QQN::NarroContextInfo()->LanguageId, $this->objTargetLanguage->LanguageId),
                        QQ::Equal(QQN::NarroContextInfo()->Context->Active, 1)
                    ),
                    QQ::Expand(QQN::NarroContextInfo()->Context)
                );

            foreach($arrTranslationObjects as $objNarroContextInfo) {
                $arrTranslation[$objNarroContextInfo->Context->Context] = $this->GetExportedSuggestion($objNarroContextInfo);
                if ($arrTranslation[$objNarroContextInfo->Context->Context] === false) {
                    if ($this->blnSkipUntranslated == false)
                        $arrTranslation[$objNarroContextInfo->Context->Context] = $objNarroContextInfo->Context->Text->TextValue;
                    else
                        unset($arrTranslation[$objNarroContextInfo->Context->Context]);
                }

                if ($objNarroContextInfo->Context->TextAccessKey) {
                    if ($objNarroContextInfo->SuggestionAccessKey && isset($arrTemplate[$objNarroContextInfo->Context->Context]->AccessKeyCtx)) {
                        $arrTranslation[$arrTemplate[$objNarroContextInfo->Context->Context]->AccessKeyCtx] = $objNarroContextInfo->SuggestionAccessKey;
                    }
                    else
                        $arrTranslation[$arrTemplate[$objNarroContextInfo->Context->Context]->AccessKeyCtx] = $objNarroContextInfo->Context->TextAccessKey;
                }
            }

            return $arrTranslation;
        }

        public function ImportFile($strTemplateFile, $strTranslatedFile = null) {
            $intTime = time();

            if ($strTranslatedFile)
                $arrTransKey = $this->FileAsArray($strTranslatedFile);

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                $arrSourceKey = $this->GetAccessKeys($arrSourceKey);
                if (isset($arrTransKey))
                    $arrTransKey = $this->GetAccessKeys($arrTransKey);

                foreach($arrSourceKey as $strKey=>$objEntity) {
                    // if it's a matched access key, keep going
                    if (isset($objEntity->LabelCtx))
                        continue;

                    if (strstr($objEntity->Comment, 'DONT_TRANSLATE') !== false)
                        continue;

                    $this->AddTranslation(
                                $objEntity->Value,
                                $objEntity->AccessKey,
                                isset($arrTransKey[$strKey])?$arrTransKey[$strKey]->Value:null,
                                isset($arrTransKey[$strKey])?(isset($arrTransKey[$strKey]->AccessKey)?$arrTransKey[$strKey]->AccessKey:null):null,
                                trim($strKey),
                                (isset($objEntity->AccessKeyCtx))?
                                    trim($objEntity->Comment) . "\n" .
                                    trim($arrSourceKey[$objEntity->AccessKeyCtx]->Comment):
                                    trim($objEntity->Comment)
                    );
                }
            }
            else {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
            }
        }
    }
?>