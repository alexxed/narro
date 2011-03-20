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
    abstract class NarroMozillaFileImporter extends NarroFileImporter {
        protected function FileAsArray($strFile) {
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

            $arrKeys = array();
            $arrComment = array();
            $arrLinesBefore = array();
            $strComment = null;
            $strLineOnMultipleRows = '';
            foreach($arrFile as $intPos=>$strLine) {
                // If the line is a comment
                if ($this->IsComment($strLine)) {
                    // build the comment until one valid key/value is found
                    $arrComment[] = $strLine;
                    continue;
                }

                // if the line is empty, add it to the lines before
                if (trim($strLine) == '') {
                    if (count($arrKeys) == 0 && count($arrComment) > 0) {
                        // Found the file header
                        $this->objFile->Header = join("\n", $arrComment);
                        $this->objFile->Save();
                    }
                    $arrComment = array();
                    $arrLinesBefore[] = "\n";
                    continue;
                }

                if ($strLineOnMultipleRows !== '') {
                    $strLine = $strLineOnMultipleRows . $strLine;
                }

                // prepare the line, comments and lines before if necessary
                $arrProcessedLine = $this->PreProcessLine($strLine, $arrComment, $arrLinesBefore);
                if ($arrProcessedLine === false) {
                    // read lines until the line is preprocessed successfully
                    $strLineOnMultipleRows .= $strLine;
                    continue;
                }
                else {
                    list($strLine, $arrComment, $arrLinesBefore) = $arrProcessedLine;
                    $strLineOnMultipleRows = '';
                }


                // process the line
                list($arrData, $arrComment, $arrLinesBefore) = $this->ProcessLine($strLine, $arrComment, $arrLinesBefore);
                if ($arrData) {
                    if (count($arrComment))
                        $strComment = join("\n", $arrComment);

                    // The key is the context and usually unique in ini/properties files
                    $arrKeys[$arrData['key']] = array('text' => $arrData['value'], 'comment' => $strComment, 'full_line' => $strLine, 'before_line' => join('', $arrLinesBefore));

                    // we got all we need, reset the arrays
                    $arrComment = array();
                    $arrLinesBefore = array();
                    $strComment = null;
                }
                else {
                    QFirebug::warn(sprintf('Processing the line "%s" from %s failed, skipping it', $strLine, $this->objFile->FileName));
                    $arrLinesBefore[] = $strLine . "\n";
                }
            }

            return $arrKeys;
        }


        /**
         * This function looks for accesskey entries and creates po style texts, e.g. &File
         * @param array $arrTexts an array with context as keys and texts as values
         */
        public function GetAccessKeys($arrTexts) {
            if (is_array($arrTexts)) {
                foreach($arrTexts as $strContext=>$arrData) {
                    $strAccKey = $arrData['text'];
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
                                QApplication::LogWarn(sprintf('Found acesskey %s in context %s but didn\'t find any label to match "%s" (.label, Label, etc).', $strAccKey, $strContext, $arrMatches[1]));
                                continue;
                            }

                            if ($strLabelCtx) {
                                QApplication::LogDebug(sprintf('Found label context "%s", looking for an acceptable access key', $strLabelCtx));
                                /**
                                 * strip mozilla entities when looking for an acceptable access key
                                 */
                                $strOriginalText = preg_replace('/&[^;]+;/', '', $arrTexts[$strLabelCtx]['text']);
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

                                    $arrTexts[$strLabelCtx]['access_key'] = mb_substr($strOriginalText, $intPos, 1);
                                    QApplication::LogDebug(sprintf('Found access key %s, using it', $arrTexts[$strLabelCtx]['access_key']));
                                }
                                else {
                                    $arrTexts[$strLabelCtx]['access_key'] = $strAccKey;
                                    QApplication::LogWarn(sprintf('No acceptable access key found for context "%s", text "%s", leaving the original.', $strLabelCtx, $strOriginalText));
                                }

                                $arrTexts[$strContext]['label_ctx'] = $strLabelCtx;
                                $arrTexts[$strLabelCtx]['access_key_ctx'] = $strContext;

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

                if ($objNarroContextInfo->TextAccessKey) {
                    if ($objNarroContextInfo->SuggestionAccessKey && isset($arrTemplate[$objNarroContextInfo->Context->Context]['access_key_ctx'])) {
                        $arrTranslation[$arrTemplate[$objNarroContextInfo->Context->Context]['access_key_ctx']] = $objNarroContextInfo->SuggestionAccessKey;
                    }
                    else
                        $arrTranslation[$arrTemplate[$objNarroContextInfo->Context->Context]['access_key_ctx']] = $objNarroContextInfo->TextAccessKey;
                }
            }

            return $arrTranslation;
        }
    }
?>