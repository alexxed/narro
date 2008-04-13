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

    class NarroGettextPoFileImporter extends NarroFileImporter {
        public function ExportFile($objFile, $strTemplate, $strTranslatedFile = null) {
            $hndExportFile = fopen($strTranslatedFile, 'w');
            if (!$hndExportFile) {
                NarroLog::LogMessage(3, sprintf(t('Cannot create or write to "%s".'), $strTranslatedFile));
                return false;
            }

            $hndTemplate = fopen($strTemplate, 'r');
            if ($hndTemplate) {
                $strCurrentGroup = 1;
                while (!feof($hndTemplate)) {
                    $strLine = fgets($hndTemplate, 8192);

                    // echo "Processing " . $strLine . "<br />";
                    if (strpos($strLine, '# ') === 0) {
                        // echo 'Found translator comment. <br />';
                        $strTranslatorComment = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '# ') === 0)
                                $strTranslatorComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#.') === 0) {
                        // echo 'Found extracted comment. <br />';
                        $strExtractedComment = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#.') === 0)
                                $strExtractedComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#:') === 0) {
                        // echo 'Found reference. <br />';
                        $strReference = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#:') === 0)
                                $strReference .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#,') === 0) {
                        // echo 'Found flag. <br />';
                        $strFlag = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#,') === 0)
                                $strFlag .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgctxt') === 0) {
                        // echo 'Found previous context. <br />';
                        $strPreviousContext = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgctxt') === 0)
                                $strPreviousContext .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid') === 0) {
                        // echo 'Found previous translated string. <br />';
                        $strPreviousUntranslated = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgid') === 0)
                                $strPreviousUntranslated .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid_plural') === 0) {
                        // echo 'Found previous translated plural string. <br />';
                        $strPreviousUntranslatedPlural = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgid_plural') === 0)
                                $strPreviousUntranslatedPlural .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgctxt ') === 0) {
                        // echo 'Found string. <br />';
                        preg_match('/msgctxt\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgContext = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgContext .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid ') === 0) {
                        preg_match('/msgid\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid_plural') === 0) {
                        // echo 'Found plural string. <br />';
                        preg_match('/msgid_plural\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgPluralId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgPluralId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr ') === 0) {
                        // echo 'Found translation. <br />';
                        preg_match('/msgstr\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[0]') === 0) {
                        // echo 'Found translation plural 1. <br />';
                        preg_match('/msgstr\[0\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr0 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr0 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[1]') === 0) {
                        // echo 'Found translation plural 2. <br />';
                        preg_match('/msgstr\[1\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr1 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr1 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[2]') === 0) {
                        // echo 'Found translation plural 3. <br />';
                        preg_match('/msgstr\[2\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr2 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr2 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if($strMsgId) {
                        /**
                        echo '$strTranslatorComment: ' . $strTranslatorComment . "<br />";
                        echo '$strExtractedComment: ' . $strExtractedComment . "<br />";
                        echo '$strReference: ' . $strReference . "<br />";
                        echo '$strFlag: ' . $strFlag . "<br />";
                        echo '$strPreviousContext: ' . $strPreviousContext . "<br />";
                        echo '$strPreviousUntranslated: ' . $strPreviousUntranslated . "<br />";
                        echo '$strPreviousUntranslatedPlural: ' . $strPreviousUntranslatedPlural . "<br />";
                        echo '$strMsgContext: ' . $strMsgContext . "<br />";
                        echo '$strMsgId: ' . $strMsgId . "<br />";
                        echo '$strMsgPluralId: ' . $strMsgPluralId . "<br />";
                        echo '$strMsgStr: ' . $strMsgStr . "<br />";
                        echo '$strMsgStr0: ' . $strMsgStr0 . "<br />";
                        echo '$strMsgStr1: ' . $strMsgStr1 . "<br />";
                        echo '$strMsgStr2: ' . $strMsgStr2 . "<br />";
                        echo '<hr />';
                        */

                        /**
                         * if the string is marked fuzzy, don't import the translation and delete fuzzy flag
                         */
                        if (strstr($strFlag, ', fuzzy')) {
                            if (!is_null($strMsgStr)) $strMsgStr = '';

                            if (!is_null($strMsgStr0)) $strMsgStr0 = '';
                            if (!is_null($strMsgStr1)) $strMsgStr1 = '';
                            if (!is_null($strMsgStr2)) $strMsgStr2 = '';

                            $strFlag = str_replace(', fuzzy', '', $strFlag);
                            /**
                             * if no other flags are found, just empty the variable
                             */
                            if (strlen(trim($strFlag)) < 4) $strFlag = null;
                        }

                        $strContext = $strTranslatorComment . $strExtractedComment . $strReference . $strFlag . $strPreviousContext . $strPreviousUntranslated . $strPreviousUntranslatedPlural . $strMsgContext;

                        if (!is_null($strMsgId)) $strMsgId = str_replace('\"', '"', $strMsgId);
                        if (!is_null($strMsgStr)) $strMsgStr = str_replace('\"', '"', $strMsgStr);

                        if (!is_null($strMsgPluralId)) $strMsgPluralId = str_replace('\"', '"', $strMsgPluralId);
                        if (!is_null($strMsgStr0)) $strMsgStr0 = str_replace('\"', '"', $strMsgStr0);
                        if (!is_null($strMsgStr1)) $strMsgStr1 = str_replace('\"', '"', $strMsgStr1);
                        if (!is_null($strMsgStr2)) $strMsgStr2 = str_replace('\"', '"', $strMsgStr2);

                        if (trim($strContext) == '') {
                            $strContext = sprintf('This text has no context info. The text is used in %s. Position in file: %d', $objFile->FileName, $strCurrentGroup);
                        }

                        /**
                         * if it's not a plural, just add msgid and msgstr
                         */
                        if (is_null($strMsgPluralId)) {
                                $strMsgStr = $this->GetTranslation($objFile, $strMsgId, $this->getAccessKey($strMsgId), $strMsgStr, $this->getAccessKey($strMsgStr), $strContext);
                        }
                        else {
                            /**
                             * if it's a plural, add the pluralid with all the msgstr's available
                             * currently limited to 3 (so 3 plural forms)
                             * the first one is added with msgid/msgstr[0] (this is the singular)
                             * the next ones (currently 2) are added with plural id, so in fact they will be tied to the same text
                             */
                            if (!is_null($strMsgStr0))
                                $strMsgStr0 = $this->GetTranslation($objFile, $strMsgId, $this->getAccessKey($strMsgId), $strMsgStr0, $this->getAccessKey($strMsgStr0), $strContext . "\nThis text has plurals.", 0);
                            if (!is_null($strMsgStr1))
                                $strMsgStr1 = $this->GetTranslation($objFile, $strMsgPluralId, $this->getAccessKey($strMsgPluralId), $strMsgStr1, $this->getAccessKey($strMsgStr1), $strContext . "\nThis is plural form 1 for the text \"$strMsgId\".", 1);
                            if (!is_null($strMsgStr2))
                                $strMsgStr2 = $this->GetTranslation($objFile, $strMsgPluralId, $this->getAccessKey($strMsgPluralId), $strMsgStr2, $this->getAccessKey($strMsgStr2), $strContext . "\nThis is plural form 2 for the text \"$strMsgId\".", 2);
                        }
                    }

                    if (!is_null($strTranslatorComment))
                        fputs($hndExportFile, $strTranslatorComment . "\n");
                    if (!is_null($strExtractedComment))
                        fputs($hndExportFile, $strExtractedComment . "\n");
                    if (!is_null($strReference))
                        fputs($hndExportFile, $strReference . "\n");
                    if (!is_null($strFlag))
                        fputs($hndExportFile, $strFlag . "\n");
                    if (!is_null($strPreviousContext))
                        fputs($hndExportFile, $strPreviousContext . "\n");
                    if (!is_null($strPreviousUntranslated))
                        fputs($hndExportFile, $strPreviousUntranslated . "\n");
                    if (!is_null($strPreviousUntranslatedPlural))
                        fputs($hndExportFile, $strPreviousUntranslatedPlural . "\n");
                    if (!is_null($strMsgContext))
                        fputs($hndExportFile, sprintf('msgctxt "%s"' . "\n", str_replace('"', '\"', $strMsgContext)));
                    if (!is_null($strMsgId))
                        fputs($hndExportFile, sprintf('msgid "%s"' . "\n", str_replace('"', '\"', $strMsgId)));
                    if (!is_null($strMsgPluralId))
                        fputs($hndExportFile, sprintf('msgid_plural "%s"' . "\n", str_replace('"', '\"', $strMsgPluralId)));
                    if (!is_null($strMsgStr))
                        fputs($hndExportFile, sprintf('msgstr "%s"' . "\n", str_replace('"', '\"', $strMsgStr)));
                    if (!is_null($strMsgStr0))
                        fputs($hndExportFile, sprintf('msgstr[0] "%s"' . "\n", str_replace('"', '\"', $strMsgStr0)));
                    if (!is_null($strMsgStr1))
                        fputs($hndExportFile, sprintf('msgstr[1] "%s"' . "\n", str_replace('"', '\"', $strMsgStr1)));
                    if (!is_null($strMsgStr2))
                        fputs($hndExportFile, sprintf('msgstr[2] "%s"' . "\n", str_replace('"', '\"', $strMsgStr2)));

                    fputs($hndExportFile, "\n");

                    $strTranslatorComment = null;
                    $strExtractedComment = null;
                    $strReference = null;
                    $strFlag = null;
                    $strPreviousUntranslated = null;
                    $strPreviousContext = null;
                    $strPreviousUntranslatedPlural = null;
                    $strMsgContext = null;
                    $strMsgId = null;
                    $strMsgPluralId = null;
                    $strMsgStr = null;
                    $strMsgStr0 = null;
                    $strMsgStr1 = null;
                    $strMsgStr2 = null;

                    $strCurrentGroup++;
                }
            }
            else {
                NarroLog::LogMessage(3, sprintf(t('Cannot open file "%s".'), $strFileToImport));
            }
        }

        public function ImportFile($objFile, $strFileToImport, $strTranslatedFile = null) {
            $hndTemplate = fopen($strFileToImport, 'r');
            if ($hndTemplate) {
                $strCurrentGroup = 1;
                while (!feof($hndTemplate)) {
                    $strLine = fgets($hndTemplate, 8192);
                    // echo "Processing " . $strLine . "<br />";
                    if (strpos($strLine, '# ') === 0) {
                        // echo 'Found translator comment. <br />';
                        $strTranslatorComment = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '# ') === 0)
                                $strTranslatorComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#.') === 0) {
                        // echo 'Found extracted comment. <br />';
                        $strExtractedComment = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#.') === 0)
                                $strExtractedComment .= $strLine;
                            else
                                break;

                        }
                    }

                    if (strpos($strLine, '#:') === 0) {
                        // echo 'Found reference. <br />';
                        $strReference = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#:') === 0)
                                $strReference .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#,') === 0) {
                        // echo 'Found flag. <br />';
                        $strFlag = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#,') === 0)
                                $strFlag .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgctxt') === 0) {
                        // echo 'Found previous context. <br />';
                        $strPreviousContext = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgctxt') === 0)
                                $strPreviousContext .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid') === 0) {
                        // echo 'Found previous translated string. <br />';
                        $strPreviousUntranslated = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgid') === 0)
                                $strPreviousUntranslated .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, '#| msgid_plural') === 0) {
                        // echo 'Found previous translated plural string. <br />';
                        $strPreviousUntranslatedPlural = $strLine;
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '#| msgid_plural') === 0)
                                $strPreviousUntranslatedPlural .= $strLine;
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgctxt ') === 0) {
                        // echo 'Found string. <br />';
                        preg_match('/msgctxt\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgContext = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgContext .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid ') === 0) {
                        preg_match('/msgid\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgid_plural') === 0) {
                        // echo 'Found plural string. <br />';
                        preg_match('/msgid_plural\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgPluralId = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgPluralId .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr ') === 0) {
                        // echo 'Found translation. <br />';
                        preg_match('/msgstr\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[0]') === 0) {
                        // echo 'Found translation plural 1. <br />';
                        preg_match('/msgstr\[0\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr0 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr0 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[1]') === 0) {
                        // echo 'Found translation plural 2. <br />';
                        preg_match('/msgstr\[1\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr1 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr1 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if (strpos($strLine, 'msgstr[2]') === 0) {
                        // echo 'Found translation plural 3. <br />';
                        preg_match('/msgstr\[2\]\s+\"(.*)\"/', $strLine, $arrMatches);
                        $strMsgStr2 = str_replace('\"', '"', $arrMatches[1]);
                        while (!feof($hndTemplate)) {
                            $strLine = fgets($hndTemplate, 8192);
                            if (strpos($strLine, '"') === 0) {
                                $strMsgStr2 .= str_replace('\"', '"', substr(trim($strLine), 1, strlen(trim($strLine)) - 2));
                            }
                            else
                                break;
                        }
                    }

                    if($strMsgId) {
                        /**
                        echo '$strTranslatorComment: ' . $strTranslatorComment . "<br />";
                        echo '$strExtractedComment: ' . $strExtractedComment . "<br />";
                        echo '$strReference: ' . $strReference . "<br />";
                        echo '$strFlag: ' . $strFlag . "<br />";
                        echo '$strPreviousContext: ' . $strPreviousContext . "<br />";
                        echo '$strPreviousUntranslated: ' . $strPreviousUntranslated . "<br />";
                        echo '$strPreviousUntranslatedPlural: ' . $strPreviousUntranslatedPlural . "<br />";
                        echo '$strMsgContext: ' . $strMsgContext . "<br />";
                        echo '$strMsgId: ' . $strMsgId . "<br />";
                        echo '$strMsgPluralId: ' . $strMsgPluralId . "<br />";
                        echo '$strMsgStr: ' . $strMsgStr . "<br />";
                        echo '$strMsgStr0: ' . $strMsgStr0 . "<br />";
                        echo '$strMsgStr1: ' . $strMsgStr1 . "<br />";
                        echo '$strMsgStr2: ' . $strMsgStr2 . "<br />";
                        echo '<hr />';
                        */

                        /**
                         * if the string is marked fuzzy, don't import the translation and delete fuzzy flag
                         */
                        if (strstr($strFlag, ', fuzzy')) {
                            if (!is_null($strMsgStr)) $strMsgStr = '';

                            if (!is_null($strMsgStr0)) $strMsgStr0 = '';
                            if (!is_null($strMsgStr1)) $strMsgStr1 = '';
                            if (!is_null($strMsgStr2)) $strMsgStr2 = '';

                            $strFlag = str_replace(', fuzzy', '', $strFlag);
                            /**
                             * if no other flags are found, just empty the variable
                             */
                            if (strlen(trim($strFlag)) < 4) $strFlag = null;
                        }

                        $strContext = $strTranslatorComment . $strExtractedComment . $strReference . $strFlag . $strPreviousContext . $strPreviousUntranslated . $strPreviousUntranslatedPlural . $strMsgContext;

                        if (!is_null($strMsgId)) $strMsgId = str_replace('\"', '"', $strMsgId);
                        if (!is_null($strMsgStr)) $strMsgStr = str_replace('\"', '"', $strMsgStr);

                        if (!is_null($strMsgPluralId)) $strMsgPluralId = str_replace('\"', '"', $strMsgPluralId);
                        if (!is_null($strMsgStr0)) $strMsgStr0 = str_replace('\"', '"', $strMsgStr0);
                        if (!is_null($strMsgStr1)) $strMsgStr1 = str_replace('\"', '"', $strMsgStr1);
                        if (!is_null($strMsgStr2)) $strMsgStr2 = str_replace('\"', '"', $strMsgStr2);

                        if (trim($strContext) == '') {
                            $strContext = sprintf('This text has no context info. The text is used in %s. Position in file: %d', $objFile->FileName, $strCurrentGroup);
                        }

                        /**
                         * if it's not a plural, just add msgid and msgstr
                         */
                        if (is_null($strMsgPluralId)) {
                                list($strMsgId, $strMsgIdAccKey) = $this->getAccessKey($strMsgId);
                                list($strMsgStr, $strMsgStrAccKey) = $this->getAccessKey($strMsgStr);
                                $this->AddTranslation($objFile, $strMsgId, $strMsgIdAccKey, $strMsgStr, $strMsgStrAccKey, $strContext);
                        }
                        else {
                            /**
                             * if it's a plural, add the pluralid with all the msgstr's available
                             * currently limited to 3 (so 3 plural forms)
                             * the first one is added with msgid/msgstr[0] (this is the singular)
                             * the next ones (currently 2) are added with plural id, so in fact they will be tied to the same text
                             * @todo add unlimited plurals support
                             */
                            if (!is_null($strMsgStr0)) {
                                list($strMsgId, $strMsgIdAccKey) = $this->getAccessKey($strMsgId);
                                list($strMsgStr0, $strMsgStr0AccKey) = $this->getAccessKey($strMsgStr0);
                                $this->AddTranslation($objFile, $strMsgId, $strMsgIdAccKey, $strMsgStr0, $strMsgStr0AccKey, $strContext . "\nThis text has plurals.");
                            }

                            if (!is_null($strMsgStr1)) {
                                list($strMsgId, $strMsgIdAccKey) = $this->getAccessKey($strMsgId);
                                list($strMsgStr1, $strMsgStr1AccKey) = $this->getAccessKey($strMsgStr1);
                                $this->AddTranslation($objFile, $strMsgPluralId, $strMsgIdAccKey, $strMsgStr1, $strMsgIdAccKey, $strContext . "\nThis is plural form 1 for the text \"$strMsgId\".");
                            }

                            if (!is_null($strMsgStr2)) {
                                list($strMsgId, $strMsgIdAccKey) = $this->getAccessKey($strMsgId);
                                list($strMsgStr2, $strMsgStr2AccKey) = $this->getAccessKey($strMsgStr2);
                                $this->AddTranslation($objFile, $strMsgPluralId, $strMsgIdAccKey, $strMsgStr2, $strMsgStr2AccKey, $strContext . "\nThis is plural form 2 for the text \"$strMsgId\".");
                            }
                        }
                    }

                    $strTranslatorComment = null;
                    $strExtractedComment = null;
                    $strReference = null;
                    $strFlag = null;
                    $strPreviousUntranslated = null;
                    $strPreviousContext = null;
                    $strPreviousUntranslatedPlural = null;
                    $strMsgContext = null;
                    $strMsgId = null;
                    $strMsgPluralId = null;
                    $strMsgStr = null;
                    $strMsgStr0 = null;
                    $strMsgStr1 = null;
                    $strMsgStr2 = null;

                    $strCurrentGroup++;
                }
            }
            else {
                NarroLog::LogMessage(3, sprintf(t('Cannot open file "%s".'), $strFileToImport));
            }
        }

        protected function getAccessKey($strText) {
            if (preg_match('/_(\w)/', $strText, $arrMatches)) {
                return array(NarroString::Replace('_' . $arrMatches[1], $arrMatches[1], $strText), $arrMatches[1]);
            }
            else
                return array($strText, null);
        }

        /**
         * A translation here consists of the project, file, text, translation, context, plurals, validation, ignore equals
         *
         * @param NarroFile $objFile
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
        protected function GetTranslation(NarroFile $objFile, $strOriginal, $strOriginalAccKey = null, $strTranslation, $strTranslationAccKey = null, $strContext, $intPluralForm = null, $strComment = null) {
            $objNarroContextInfo = NarroContextInfo::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroContextInfo()->Context->ProjectId, $this->objProject->ProjectId),
                    QQ::Equal(QQN::NarroContextInfo()->Context->FileId, $objFile->FileId),
                    QQ::Equal(QQN::NarroContextInfo()->Context->ContextMd5, md5($strContext)),
                    QQ::Equal(QQN::NarroContextInfo()->LanguageId, $this->objTargetLanguage->LanguageId),
                    QQ::IsNotNull(QQN::NarroContextInfo()->ValidSuggestionId)
                )
            );

            if ( $objNarroContextInfo instanceof NarroContextInfo ) {
                if (!is_null($strTranslationAccKey))
                    return NarroString::Replace($objNarroContextInfo->SuggestionAccessKey, '_' . $objNarroContextInfo->SuggestionAccessKey, $objNarroContextInfo->ValidSuggestion->SuggestionValue, 1);
                else
                    return $objNarroContextInfo->ValidSuggestion->SuggestionValue;
            }
            else {
                if (!is_null($strOriginalAccKey))
                    return NarroString::Replace($strOriginalAccKey, '_' . $strOriginalAccKey, $strOriginal, 1);
                else
                    return $strOriginal;
            }
        }
    }
?>
