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

    class NarroDumbGettextPoFileImporter extends NarroFileImporter {
        protected function getPoFields(&$hndFile) {
            $arrFields = array();

            if (!is_resource($hndFile) || feof($hndFile)) return $arrFields;

            $strLine = fgets($hndFile, 8192);
            NarroLog::LogMessage(1, "Processing " . $strLine . "<br />");
            if (strpos($strLine, '# ') === 0) {
                 NarroLog::LogMessage(1, 'Found translator comment. <br />');
                $arrFields['TranslatorComment'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '# ') === 0)
                        $arrFields['TranslatorComment'] .= $strLine;
                    else
                        break;

                }
            }

            if (strpos($strLine, '#.') === 0) {
                NarroLog::LogMessage(1, 'Found extracted comment. <br />');
                $arrFields['ExtractedComment'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#.') === 0)
                        $arrFields['ExtractedComment'] .= $strLine;
                    else
                        break;

                }
            }

            if (strpos($strLine, '#:') === 0) {
                NarroLog::LogMessage(1, 'Found reference. <br />');
                $arrFields['Reference'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#:') === 0)
                        $arrFields['Reference'] .= $strLine;
                    else
                        break;
                }
            }

            if (strpos($strLine, '#,') === 0) {
                NarroLog::LogMessage(1, 'Found flag. <br />');
                $arrFields['Flag'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#,') === 0)
                        $arrFields['Flag'] .= $strLine;
                    else
                        break;
                }
            }

            if (strpos($strLine, '#| msgctxt') === 0) {
                NarroLog::LogMessage(1, 'Found previous context. <br />');
                $arrFields['PreviousContext'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#| msgctxt') === 0)
                        $arrFields['PreviousContext'] .= $strLine;
                    else
                        break;
                }
            }

            if (strpos($strLine, '#| msgid') === 0) {
                NarroLog::LogMessage(1, 'Found previous translated string. <br />');
                $arrFields['PreviousUntranslated'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#| msgid') === 0)
                        $arrFields['PreviousUntranslated'] .= $strLine;
                    else
                        break;
                }
            }

            if (strpos($strLine, '#| msgid_plural') === 0) {
                NarroLog::LogMessage(1, 'Found previous translated plural string. <br />');
                $arrFields['PreviousUntranslatedPlural'] = $strLine;
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '#| msgid_plural') === 0)
                        $arrFields['PreviousUntranslatedPlural'] .= $strLine;
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgctxt ') === 0) {
                NarroLog::LogMessage(1, 'Found string. <br />');
                preg_match('/msgctxt\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgContext'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgContext'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgid ') === 0) {
                NarroLog::LogMessage(1, 'Found msgid. <br />');
                preg_match('/msgid\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgId'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgId'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgid_plural') === 0) {
                NarroLog::LogMessage(1, 'Found plural string. <br />');
                preg_match('/msgid_plural\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgPluralId'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgPluralId'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgstr ') === 0) {
                NarroLog::LogMessage(1, 'Found translation. <br />');
                preg_match('/msgstr\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgStr'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgStr'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgstr[0]') === 0) {
                NarroLog::LogMessage(1, 'Found translation plural 1. <br />');
                preg_match('/msgstr\[0\]\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgStr0'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgStr0'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgstr[1]') === 0) {
                NarroLog::LogMessage(1, 'Found translation plural 2. <br />');
                preg_match('/msgstr\[1\]\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgStr1'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgStr1'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            if (strpos($strLine, 'msgstr[2]') === 0) {
                NarroLog::LogMessage(1, 'Found translation plural 3. <br />');
                preg_match('/msgstr\[2\]\s+\"(.*)\"/', $strLine, $arrMatches);
                $arrFields['MsgStr2'] = str_replace('\"', '"', $arrMatches[1]);
                while (!feof($hndFile)) {
                    $strLine = fgets($hndFile, 8192);
                    if (strpos($strLine, '"') === 0) {
                        $arrFields['MsgStr2'] .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                    }
                    else
                        break;
                }
            }

            return $arrFields;
        }

        public function ExportFile($strTemplate, $strTranslatedFile = null) {
            $hndExportFile = fopen($strTranslatedFile, 'w');
            if (!$hndExportFile) {
                NarroLog::LogMessage(3, sprintf(t('Cannot create or write to "%s".'), $strTranslatedFile));
                return false;
            }

            $hndTemplateFile = fopen($strTemplate, 'r');
            if ($hndTemplateFile) {
                $strCurrentGroup = 1;
                while (!feof($hndTemplateFile)) {
                    $arrTemplateFields = $this->getPoFields($hndTemplateFile);

                    if(isset($arrTemplateFields['MsgId']) && $arrTemplateFields['MsgId'] != '') {

                        /**
                         * if the string is marked fuzzy, don't import the translation and delete fuzzy flag
                         */
                        if (strstr($arrTemplateFields['Flag'], ', fuzzy')) {
                            if (!is_null($arrTemplateFields['MsgStr'])) $arrTemplateFields['MsgStr'] = '';

                            if (!is_null($arrTemplateFields['MsgStr0'])) $arrTemplateFields['MsgStr0'] = '';
                            if (!is_null($arrTemplateFields['MsgStr1'])) $arrTemplateFields['MsgStr1'] = '';
                            if (!is_null($arrTemplateFields['MsgStr2'])) $arrTemplateFields['MsgStr2'] = '';

                            $arrTemplateFields['Flag'] = str_replace(', fuzzy', '', $arrTemplateFields['Flag']);
                            /**
                             * if no other flags are found, just empty the variable
                             */
                            if (strlen(trim($arrTemplateFields['Flag'])) < 4) $arrTemplateFields['Flag'] = null;
                        }

                        $arrTemplateFields['Context'] = $arrTemplateFields['TranslatorComment'] . $arrTemplateFields['ExtractedComment'] . $arrTemplateFields['Reference'] . $arrTemplateFields['Flag'] . $arrTemplateFields['PreviousContext'] . $arrTemplateFields['PreviousUntranslated'] . $arrTemplateFields['PreviousUntranslatedPlural'] . $arrTemplateFields['MsgContext'];
                        NarroLog::LogMessage(1, 'Context is: ' . $arrTemplateFields['Context']);

                        if (!is_null($arrTemplateFields['MsgId'])) $arrTemplateFields['MsgId'] = str_replace('\"', '"', $arrTemplateFields['MsgId']);
                        if (!is_null($arrTemplateFields['MsgStr'])) $arrTemplateFields['MsgStr'] = str_replace('\"', '"', $arrTemplateFields['MsgStr']);

                        if (!is_null($arrTemplateFields['MsgPluralId'])) $arrTemplateFields['MsgPluralId'] = str_replace('\"', '"', $arrTemplateFields['MsgPluralId']);
                        if (!is_null($arrTemplateFields['MsgStr0'])) $arrTemplateFields['MsgStr0'] = str_replace('\"', '"', $arrTemplateFields['MsgStr0']);
                        if (!is_null($arrTemplateFields['MsgStr1'])) $arrTemplateFields['MsgStr1'] = str_replace('\"', '"', $arrTemplateFields['MsgStr1']);
                        if (!is_null($arrTemplateFields['MsgStr2'])) $arrTemplateFields['MsgStr2'] = str_replace('\"', '"', $arrTemplateFields['MsgStr2']);

                        if (trim($arrTemplateFields['Context']) == '') {
                            $arrTemplateFields['Context'] = sprintf('This text has no context info. The text is used in %s. Position in file: %d', $this->objFile->FileName, $strCurrentGroup);
                        }

                        /**
                         * if it's not a plural, just add msgid and msgstr
                         */
                        if (is_null($arrTemplateFields['MsgPluralId'])) {
                            $arrTemplateFields['MsgStr'] = $this->GetTranslation($this->stripAccessKey($arrTemplateFields['MsgStr']), $this->getAccessKey($arrTemplateFields['MsgStr']), $this->getAccessKeyPrefix($arrTemplateFields['MsgStr']), null , null, $arrTemplateFields['Context'] . $arrTemplateFields['MsgId']);

                        }
                        else {
                            /**
                             * if it's a plural, add the pluralid with all the msgstr's available
                             * currently limited to 3 (so 3 plural forms)
                             * the first one is added with msgid/msgstr[0] (this is the singular)
                             * the next ones (currently 2) are added with plural id, so in fact they will be tied to the same text
                             */
                            $strSingularText = $arrTemplateFields['MsgStr0'];

                            if (!is_null($arrTemplateFields['MsgStr0']))
                                $arrTemplateFields['MsgStr0'] = $this->GetTranslation($this->stripAccessKey($arrTemplateFields['MsgStr0']), $this->getAccessKey($arrTemplateFields['MsgStr0']), $this->getAccessKeyPrefix($arrTemplateFields['MsgStr0']), null, null, $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This text has plurals.", 0);
                            if (!is_null($arrTemplateFields['MsgStr1']))
                                $arrTemplateFields['MsgStr1'] = $this->GetTranslation($this->stripAccessKey($arrTemplateFields['MsgStr1']), $this->getAccessKey($arrTemplateFields['MsgStr1']), $this->getAccessKeyPrefix($arrTemplateFields['MsgStr1']), null, null, $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This is plural form 1 for the text \"".$strSingularText."\".", 1);
                            if (!is_null($arrTemplateFields['MsgStr2']))
                                $arrTemplateFields['MsgStr2'] = $this->GetTranslation($this->stripAccessKey($arrTemplateFields['MsgStr2']), $this->getAccessKey($arrTemplateFields['MsgStr2']), $this->getAccessKeyPrefix($arrTemplateFields['MsgStr2']), null, null, $this->getAccessKey($arrTemplateFields['MsgStr2']), $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This is plural form 2 for the text \"".$strSingularText."\".", 2);
                        }
                    }

                    if (!is_null($arrTemplateFields['TranslatorComment']))
                        fputs($hndExportFile, $arrTemplateFields['TranslatorComment']);
                    if (!is_null($arrTemplateFields['ExtractedComment']))
                        fputs($hndExportFile, $arrTemplateFields['ExtractedComment']);
                    if (!is_null($arrTemplateFields['Reference']))
                        fputs($hndExportFile, $arrTemplateFields['Reference']);
                    if (!is_null($arrTemplateFields['Flag']))
                        fputs($hndExportFile, $arrTemplateFields['Flag']);
                    if (!is_null($arrTemplateFields['PreviousContext']))
                        fputs($hndExportFile, $arrTemplateFields['PreviousContext']);
                    if (!is_null($arrTemplateFields['PreviousUntranslated']))
                        fputs($hndExportFile, $arrTemplateFields['PreviousUntranslated']);
                    if (!is_null($arrTemplateFields['PreviousUntranslatedPlural']))
                        fputs($hndExportFile, $arrTemplateFields['PreviousUntranslatedPlural']);
                    if (!is_null($arrTemplateFields['MsgContext']))
                        fputs($hndExportFile, sprintf('msgctxt "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgContext'])));
                    if (!is_null($arrTemplateFields['MsgId']))
                        fputs($hndExportFile, sprintf('msgid "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgId'])));
                    if (!is_null($arrTemplateFields['MsgPluralId']))
                        fputs($hndExportFile, sprintf('msgid_plural "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgPluralId'])));

                    if (!is_null($arrTemplateFields['MsgStr']))
                        if ($arrTemplateFields['MsgId'] == '') {
                            /**
                             * this must be the po header
                             */
                            $arrTemplateFields['PoHeader'] = sprintf("msgstr \"\"\n\"%s\"\n", str_replace('\n', "\\n\"\n\"", $arrTemplateFields['MsgStr']));
                            $arrTemplateFields['PoHeader'] = preg_replace('/\n""/', '', $arrTemplateFields['PoHeader']);
                            fputs($hndExportFile, $arrTemplateFields['PoHeader']);
                        }
                        else
                            fputs($hndExportFile, sprintf('msgstr "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgStr'])));
                    if (!is_null($arrTemplateFields['MsgStr0']))
                        fputs($hndExportFile, sprintf('msgstr[0] "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgStr0'])));
                    if (!is_null($arrTemplateFields['MsgStr1']))
                        fputs($hndExportFile, sprintf('msgstr[1] "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgStr1'])));
                    if (!is_null($arrTemplateFields['MsgStr2']))
                        fputs($hndExportFile, sprintf('msgstr[2] "%s"' . "\n", str_replace('"', '\"', $arrTemplateFields['MsgStr2'])));

                    fputs($hndExportFile, "\n");

                    if ($arrTemplateFields['MsgId'] == '') {
                        fputs($hndExportFile, $strLine);
                    }

                    $strCurrentGroup++;
                }

                fclose($hndExportFile);
                chmod($strTranslatedFile, 0666);
            }
            else {
                NarroLog::LogMessage(3, sprintf(t('Cannot open file "%s".'), $strTemplate));
            }
        }

        public function ImportFile($strFileToImport, $strTranslatedFile = null) {
            $hndTemplateFile = fopen($strFileToImport, 'r');
            $hndTranslatedFile = fopen($strTranslatedFile, 'r');
            if ($hndTemplateFile) {
                $strCurrentGroup = 1;
                while (!feof($hndTemplateFile)) {

                    $arrTemplateFields = $this->getPoFields($hndTemplateFile);
                    $arrTranslatedFields = $this->getPoFields($hndTranslatedFile);

                    if($arrTemplateFields['MsgId']) {

                        /**
                         * if the string is marked fuzzy, don't import the translation and delete fuzzy flag
                         */
                        if (strstr($arrTemplateFields['Flag'], ', fuzzy')) {
                            if (!is_null($arrTemplateFields['MsgStr'])) $arrTemplateFields['MsgStr'] = '';

                            if (!is_null($arrTemplateFields['MsgStr0'])) $arrTemplateFields['MsgStr0'] = '';
                            if (!is_null($arrTemplateFields['MsgStr1'])) $arrTemplateFields['MsgStr1'] = '';
                            if (!is_null($arrTemplateFields['MsgStr2'])) $arrTemplateFields['MsgStr2'] = '';

                            $arrTemplateFields['Flag'] = str_replace(', fuzzy', '', $arrTemplateFields['Flag']);
                            /**
                             * if no other flags are found, just empty the variable
                             */
                            if (strlen(trim($arrTemplateFields['Flag'])) < 4) $arrTemplateFields['Flag'] = null;
                        }

                        $arrTemplateFields['Context'] = $arrTemplateFields['TranslatorComment'] . $arrTemplateFields['ExtractedComment'] . $arrTemplateFields['Reference'] . $arrTemplateFields['Flag'] . $arrTemplateFields['PreviousContext'] . $arrTemplateFields['PreviousUntranslated'] . $arrTemplateFields['PreviousUntranslatedPlural'] . $arrTemplateFields['MsgContext'];

                        if (!is_null($arrTemplateFields['MsgId'])) $arrTemplateFields['MsgId'] = str_replace('\"', '"', $arrTemplateFields['MsgId']);
                        if (!is_null($arrTemplateFields['MsgStr'])) $arrTemplateFields['MsgStr'] = str_replace('\"', '"', $arrTemplateFields['MsgStr']);

                        if (!is_null($arrTemplateFields['MsgPluralId'])) $arrTemplateFields['MsgPluralId'] = str_replace('\"', '"', $arrTemplateFields['MsgPluralId']);
                        if (!is_null($arrTemplateFields['MsgStr0'])) $arrTemplateFields['MsgStr0'] = str_replace('\"', '"', $arrTemplateFields['MsgStr0']);
                        if (!is_null($arrTemplateFields['MsgStr1'])) $arrTemplateFields['MsgStr1'] = str_replace('\"', '"', $arrTemplateFields['MsgStr1']);
                        if (!is_null($arrTemplateFields['MsgStr2'])) $arrTemplateFields['MsgStr2'] = str_replace('\"', '"', $arrTemplateFields['MsgStr2']);

                        if (trim($arrTemplateFields['Context']) == '') {
                            $arrTemplateFields['Context'] = sprintf('This text has no context info. The text is used in %s. Position in file: %d', $this->objFile->FileName, $strCurrentGroup);
                        }

                        if (isset($arrTranslatedFields['MsgStr']) && $arrTranslatedFields['MsgStr'] != '' && isset($arrTranslatedFields['MsgId']) == $arrTemplateFields['MsgId'])
                            $strTranslatedText = str_replace('\"', '"', $arrTranslatedFields['MsgStr']);
                        else
                            $strTranslatedText = null;

                        if (isset($arrTranslatedFields['MsgStr0']) && $arrTranslatedFields['MsgStr0'] != '' && isset($arrTranslatedFields['MsgPluralId']) == $arrTemplateFields['MsgPluralId'])
                            $strTranslatedText0 = str_replace('\"', '"', $arrTranslatedFields['MsgStr0']);
                        else
                            $strTranslatedText0 = null;

                        if (isset($arrTranslatedFields['MsgStr1']) && $arrTranslatedFields['MsgStr1'] != '' && isset($arrTranslatedFields['MsgPluralId']) == $arrTemplateFields['MsgPluralId'])
                            $strTranslatedText1 = str_replace('\"', '"', $arrTranslatedFields['MsgStr1']);
                        else
                            $strTranslatedText1 = null;

                        if (isset($arrTranslatedFields['MsgStr2']) && $arrTranslatedFields['MsgStr2'] != '' && isset($arrTranslatedFields['MsgPluralId']) == $arrTemplateFields['MsgPluralId'])
                            $strTranslatedText2 = str_replace('\"', '"', $arrTranslatedFields['MsgStr2']);
                        else
                            $strTranslatedText2 = null;

                        /**
                         * if it's not a plural, just add msgid and msgstr
                         */
                        if (is_null($arrTemplateFields['MsgPluralId'])) {
                                $this->AddTranslation($this->stripAccessKey($arrTemplateFields['MsgStr']), $this->getAccessKey($arrTemplateFields['MsgStr']), $strTranslatedText, $this->getAccessKey($strTranslatedText), $arrTemplateFields['Context'] . $arrTemplateFields['MsgId']);
                        }
                        else {
                            /**
                             * if it's a plural, add the pluralid with all the msgstr's available
                             * currently limited to 3 (so 3 plural forms)
                             * the first one is added with msgid/msgstr[0] (this is the singular)
                             * the next ones (currently 2) are added with plural id, so in fact they will be tied to the same text
                             * @todo add unlimited plurals support
                             */
                            if (!is_null($arrTemplateFields['MsgStr0'])) {
                                $this->AddTranslation($this->stripAccessKey($arrTemplateFields['MsgStr0']), $this->getAccessKey($arrTemplateFields['MsgStr0']), $strTranslatedText0, $this->getAccessKey($strTranslatedText0), $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This text has plurals.");
                            }

                            if (!is_null($arrTemplateFields['MsgStr1'])) {
                                $this->AddTranslation($this->stripAccessKey($arrTemplateFields['MsgStr1']), $this->getAccessKey($arrTemplateFields['MsgStr1']), $strTranslatedText1, $this->getAccessKey($strTranslatedText1), $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This is plural form 1 for the text \"" . $arrTemplateFields['MsgStr0'] . "\".");
                            }

                            if (!is_null($arrTemplateFields['MsgStr2'])) {
                                $this->AddTranslation($this->stripAccessKey($arrTemplateFields['MsgStr2']), $this->getAccessKey($arrTemplateFields['MsgStr2']), $strTranslatedText2, $this->getAccessKey($strTranslatedText2), $arrTemplateFields['Context'] . $arrTemplateFields['MsgId'] . $arrTemplateFields['MsgPluralId'] . "This is plural form 2 for the text \"" . $arrTemplateFields['MsgStr0'] . "\".");
                            }
                        }
                    }

                    $arrTemplateFields['TranslatorComment'] = null;
                    $arrTemplateFields['ExtractedComment'] = null;
                    $arrTemplateFields['Reference'] = null;
                    $arrTemplateFields['Flag'] = null;
                    $arrTemplateFields['PreviousUntranslated'] = null;
                    $arrTemplateFields['PreviousContext'] = null;
                    $arrTemplateFields['PreviousUntranslatedPlural'] = null;
                    $arrTemplateFields['MsgContext'] = null;
                    $arrTemplateFields['MsgId'] = null;
                    $arrTemplateFields['MsgPluralId'] = null;
                    $arrTemplateFields['MsgStr'] = null;
                    $arrTemplateFields['MsgStr0'] = null;
                    $arrTemplateFields['MsgStr1'] = null;
                    $arrTemplateFields['MsgStr2'] = null;

                    $strCurrentGroup++;
                }
            }
            else {
                NarroLog::LogMessage(3, sprintf(t('Cannot open file "%s".'), $strFileToImport));
            }
        }

        private function getAccessKeyAndStrippedText($strText) {
            $strCleanText = preg_replace('/<literal>.*<\/literal>/', '', $strText);
            $strCleanText = strip_tags($strCleanText);
            $strCleanText = html_entity_decode($strCleanText);
            $strCleanText = preg_replace('/\$[a-z0-9A-Z_\-]+/', '', $strCleanText);

            if (preg_match('/_(\w)/', $strCleanText, $arrMatches)) {
                return array(NarroString::Replace('_' . $arrMatches[1], $arrMatches[1], $strText), '_', $arrMatches[1]);
            }
            else {
                if (preg_match('/&(\w)/', $strCleanText, $arrMatches)) {
                    return array(NarroString::Replace('&' . $arrMatches[1], $arrMatches[1], $strText), '&', $arrMatches[1]);
                }
                else
                    return array($strText, null);
            }
        }

        protected function getAccessKey($strText) {
            list($strStrippedText, $strAccKeyPrefix, $strAccKey) = $this->getAccessKeyAndStrippedText($strText);
            return $strAccKey;
        }

        protected function stripAccessKey($strText) {
            list($strStrippedText, $strAccKeyPrefix, $strAccKey) = $this->getAccessKeyAndStrippedText($strText);
            return $strStrippedText;
        }

        protected function getAccessKeyPrefix($strText) {
            list($strStrippedText, $strAccKeyPrefix, $strAccKey) = $this->getAccessKeyAndStrippedText($strText);
            return $strAccKeyPrefix;
        }

        /**
         * A translation here consists of the project, file, text, translation, context, plurals, validation, ignore equals
         *
         * @param string $strOriginal the original text
         * @param string $strOriginalAccKey access key for the original text
         * @param string $strTranslation the translated text from the import file (can be empty)
         * @param string $strOriginalAccKey access key for the translated text
         * @param string $strContext the context where the text/translation appears in the file
         * @param string $intPluralForm if this is a plural, what plural form is it (0 singular, 1 plural form 1, and so on)
         * @param string $strComment a comment from the imported file
         *
         * @return string valid suggestion
         */
        protected function GetTranslation($strOriginal, $strOriginalAccKey = null, $strOriginalAccKeyPrefix = null, $strTranslation, $strTranslationAccKey = null, $strContext, $intPluralForm = null, $strComment = null) {

            $objNarroContextInfo = NarroContextInfo::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $this->objFile->FileId),
                    QQ::Equal(QQN::NarroContextInfo()->Context->ContextMd5, md5($strContext)),
                    QQ::Equal(QQN::NarroContextInfo()->Context->Text->TextValueMd5, md5($strOriginal)),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, $this->objTargetLanguage->LanguageId),
                    QQ::IsNotNull(QQN::NarroContextInfo()->ValidSuggestionId)
                )
            );

            if ( $objNarroContextInfo instanceof NarroContextInfo ) {
                $arrResult = QApplication::$objPluginHandler->ExportSuggestion($strOriginal, $objNarroContextInfo->ValidSuggestion->SuggestionValue, $strContext, $this->objFile, $this->objProject);
                if
                (
                    trim($arrResult[1]) != '' &&
                    $arrResult[0] == $strOriginal &&
                    $arrResult[2] == $strContext &&
                    $arrResult[3] == $this->objFile &&
                    $arrResult[4] == $this->objProject
                ) {
                $objNarroContextInfo->ValidSuggestion->SuggestionValue = $arrResult[1];
                }
            else
            NarroLog::LogMessage(2, sprintf(t('A plugin returned an unexpected result while processing the suggestion "%s": %s'), $strTranslation, $strTranslation));

                if (!is_null($strOriginalAccKey) && !is_null($strOriginalAccKeyPrefix)) {
                    /**
                     * @todo don't export if there's no valid access key
                     */
                    $strTextWithAccKey = NarroString::Replace($objNarroContextInfo->SuggestionAccessKey, $strOriginalAccKeyPrefix . $objNarroContextInfo->SuggestionAccessKey, $objNarroContextInfo->ValidSuggestion->SuggestionValue, 1);
                    return $strTextWithAccKey;
                }
                else
                    return $objNarroContextInfo->ValidSuggestion->SuggestionValue;
            }
            else {
                /**
                 * leave it untranslated
                 */
                return $strOriginal;
            }
        }
    }
?>
