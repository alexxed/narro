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

    class NarroPluginHandler {
        protected $arrPluginErrors;
        protected $arrPlugins;
        protected $arrKnownMethods = array(
            'ProcessSuggestion',
            'ProcessText',
            'ProcessContext',
            'ProcessSuggestionComment',
            'ProcessContextComment',
            'ValidateSuggestion',
            'ValidateSuggestionComment',
            'ValidateContextComment',
            'DisplaySuggestion',
            'DisplayText',
            'DisplayContext',
            'DisplaySuggestionComment',
            'DisplayContextComment'
        );

        public function __construct($strPluginFolder) {

            // Open a known directory, and proceed to read its contents
            if (is_dir($strPluginFolder)) {
                if ($objDirHandler = opendir($strPluginFolder)) {
                    while (($strFileName = readdir($objDirHandler)) !== false) {
                        if (strstr($strFileName, '.class.php')) {
                            include_once($strPluginFolder . '/' . $strFileName);
                            $this->RegisterPlugin(str_replace('.class.php', '', $strFileName));
                        }
                    }
                    closedir($objDirHandler);
                }
            }
        }

        protected function RegisterPlugin($strPluginClass) {
            $this->arrPlugins[] = new $strPluginClass();
        }


        public function __call($strMethod, $arrParameters) {
            foreach($this->arrPlugins as $objPlugin) {
                if (method_exists($objPlugin, $strMethod)) {
                    $mixResult = call_user_func_array( array($objPlugin, $strMethod), $arrParameters);

                    if ($objPlugin->Errors) {
                        $this->arrPluginErrors[$objPlugin->Name] = $objPlugin->Errors;
                        return false;
                    }
                    else {
                        return $mixResult;
                    }
                }
                else
                    return false;
            }
        }
    }
?>