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

    class NarroMozillaDtdFileImporter extends NarroMozillaFileImporter {
        protected $blnCommentStarted = false;
        const ENTITY_REGEX = '/<!ENTITY\s+([^\s]+)\s+"([^"]*)"\s?>\s*|<!ENTITY\s+([^\s]+)\s+\'([^\']*)\'\s?>\s*/m';
        /**
         * Preprocesses the whole file, e.g. removing trailing spaces
         * @param string $strFile file content
         * @return string
         */
        protected function PreProcessFile($strFile) {
            return $strFile;
        }

        /**
         * Tells whether the file is a comment
         * This function helps with comments that spread over multiple lines
         * @param string $strLine
         * @return boolean
         */
        protected function IsComment($strLine) {
            if ($this->blnCommentStarted == false) {
                if (strstr($strLine, '<!--') !== false) {
                    // Started comment, set the flag to true if the comment spreads over multiple lines and return true for this line
                    if (strstr($strLine, '-->') === false) {
                        $this->blnCommentStarted = true;
                    }
                    return true;
                }
                else
                    return false;
            } else {
                // Closing comment, set the flag to false and return true for this line
                if (strstr($strLine, '-->') !== false) {
                    $this->blnCommentStarted = false;
                }
                return true;
            }
        }

        /**
         * Preprocesses the line if needed
         * e.g. in the source file there's a comment like '# #define MOZ_LANGPACK_CONTRIBUTORS that should be uncommented
         * @param string $strLine
         * @param array $arrComment
         * @param array $arrLinesBefore
         * @return array an array with the arguments received; processed if needed
         */
        protected function PreProcessLine($strLine, $arrComment, $arrLinesBefore) {
            if (!preg_match(self::ENTITY_REGEX, $strLine))
                return false;

            if (strstr($strLine, 'credit.translation'))
                $strLine = preg_replace('/<!ENTITY\s+credit.translation\s+"">/', '<!ENTITY credit.translation "<h3>Translators</h3><ul><li>Name Here</li></ul>">', $strLine);

            return array($strLine, $arrComment, $arrLinesBefore);
        }

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
        protected function ProcessLine($strLine, $arrComment, $arrLinesBefore) {
            if (preg_match(self::ENTITY_REGEX, $strLine, $arrMatches)) {
                return array(array('key'=>isset($arrMatches[1])?$arrMatches[1]:$arrMatches[3], 'value'=>isset($arrMatches[2])?$arrMatches[2]:$arrMatches[4]), $arrComment, $arrLinesBefore);
            }
            else {
                return array(false, $arrComment, $arrLinesBefore);
            }
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
                $arrTransKey = $this->GetAccessKeys($arrTransKey);

                foreach($arrSourceKey as $strKey=>$arrData) {
                    // skip found access keys
                    if (!isset($arrData['label_ctx']))
                        $this->AddTranslation(
                                    $arrData['text'],
                                    @$arrData['access_key'],
                                    isset($arrTransKey[$strKey])?trim($arrTransKey[$strKey]['text']):null,
                                    @$arrTransKey[$strKey]['access_key'],
                                    trim($strKey),
                                    trim($arrData['comment'])
                        );
                }
            }
            elseif ($strTranslatedFile) {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                chmod($strTranslatedFile, 0666);
            }
        }

        public function ExportFile($strTemplateFile, $strTranslatedFile) {
            $intTime = time();

            $arrSourceKey = $this->FileAsArray($strTemplateFile);

            $intElapsedTime = time() - $intTime;
            if ($intElapsedTime > 0)
                QApplication::LogDebug(sprintf('Preprocessing %s took %d seconds.', $this->objFile->FileName, $intElapsedTime));

            QApplication::LogDebug(sprintf('Found %d contexts in file %s.', count($arrSourceKey), $this->objFile->FileName));

            if (is_array($arrSourceKey)) {
                $arrSourceKey = $this->GetAccessKeys($arrSourceKey);
                $arrTranslation = $this->GetTranslations($arrSourceKey);

                $hndTranslationFile = fopen($strTranslatedFile, 'w');

                if ($this->objFile->Header)
                    fwrite($hndTranslationFile, $this->objFile->Header . "\n");

                $arrUsers = array();
                foreach($this->objFile->LoadArrayOfAuthors($this->objTargetLanguage->LanguageId) as $objUser) {
                    $arrUsers[] = sprintf("# %s <%s>", $objUser->Username, $objUser->Email);
                }

                if (count($arrUsers))
                    fwrite($hndTranslationFile, sprintf("<!--\n# Translator(s):\n#\n%s\n-->\n", join("\n", $arrUsers)));

                foreach($arrSourceKey as $strContext=>$arrData) {
                    $arrLine = array();

                    if (strlen($arrData['before_line']) > 0)
                        $arrLine[] = $arrData['before_line'];

                    if (!is_null($arrData['comment']))
                        $arrLine[] = $arrData['comment'] . "\n";

                    if (isset($arrTranslation[$strContext]))
                        $arrLine[] = str_replace($arrData['text'], $arrTranslation[$strContext], $arrData['full_line']) . "\n";
                    else
                        if ($this->blnSkipUntranslated == false)
                            $arrLine[] = $arrData['full_line'] . "\n";
                        else
                            continue;

                    fwrite($hndTranslationFile, join('', $arrLine));
                }

                fclose($hndTranslationFile);
                NarroUtils::Chmod($strTranslatedFile, 0666);
                return true;
            }
            else {
                QApplication::LogWarn(sprintf('Found a empty template (%s), copying the original', $strTemplateFile));
                copy($strTemplateFile, $strTranslatedFile);
                NarroUtils::Chmod($strTranslatedFile, 0666);
                return false;
            }
        }
    }
?>
