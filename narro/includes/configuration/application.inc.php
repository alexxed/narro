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

    class QApplication extends QApplicationBase {
        /**
         * @var boolean
         */
        public static $UseAjax = true;
        /**
         * @var NarroUser
         */
        public static $User;
        /**
         * @var NarroPluginHandler
         */
        public static $PluginHandler;
        /**
         * @var array
         */
        public static $AvailablePreferences;
        /**
         * @var Zend_Cache_Core
         */
        public static $Cache;
        /**
         * @var NarroLanguage
         */
        public static $Language;
        /**
         * @var Zend_Translate
         */
        public static $TranslationEngine;

        ////////////////////////////
        // Additional Static Methods
        ////////////////////////////

        public static function Autoload($strClassName) {
            if (!parent::Autoload($strClassName)) {
                if (file_exists($strFilePath = sprintf('%s/narro/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/database/%s.class.php', __QCUBED_CORE__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/importer/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/panel/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/narro/search/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/model/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/qcubed_custom_controls/%s.class.php', __NARRO_INCLUDES__, $strClassName)))
                    require_once($strFilePath);
                elseif (file_exists($strFilePath = sprintf('%s/%s.php', __NARRO_INCLUDES__, str_replace('_', '/', $strClassName))))
                    require_once($strFilePath);
                else
                    throw new Exception(sprintf('Cannot find the file that contains the class "%s"', $strClassName));
            }

        }

        public static function RegisterPreference($strName, $strType = 'text', $strDescription = '', $strDefaultValue = '', $arrValues = array()) {
            self::$AvailablePreferences[$strName] = array('type'=> $strType, 'description'=>$strDescription, 'default'=>$strDefaultValue, 'values'=>$arrValues);
        }

        public static function RegisterFormat($strName, $strPluginName) {
            self::$arrFileFormats[$strName] = $strPluginName;
        }

        public static function GetUserId() {
            return self::$User->UserId;
        }

        public static function GetLanguageId() {
            return self::$Language->LanguageId;
        }

        public static function HasPermissionForThisLang($strPermissionName, $intProjectId = null) {
            if (self::$User instanceof NarroUser)
                return self::$User->hasPermission($strPermissionName, $intProjectId, self::GetLanguageId());
            else
                return false;
        }

        public static function HasPermission($strPermissionName, $intProjectId = null, $intLanguageId = null) {
            if (self::$User instanceof NarroUser)
                return self::$User->hasPermission($strPermissionName, $intProjectId, $intLanguageId);
            else
                return false;
        }

        /**
         * Translation function, no plural suport yet in Zend_Translate
         * @param $strText
         * @param $strPlural
         * @param $intCnt
         * @return string
         */
        public static function Translate($strText, $strPlural = null, $intCnt = null) {
            if (isset(self::$TranslationEngine))
                return self::$TranslationEngine->_($strText);
            else
                return $strText;
        }

        public static function ResetUser($intUserId) {
            foreach(QApplication::$Cache->getIdsMatchingTags(array('NarroUser' . $intUserId)) as $strCacheId)
                QApplication::$Cache->remove($strCacheId);
        }

        public static function GetBrowserLanguage() {
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                if (strstr($_SERVER['HTTP_ACCEPT_LANGUAGE'], ';')) {
                    $arrLangGroups = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    foreach($arrLangGroups as $strLangGroup) {
                        if (strstr($strLangGroup, ',')) {
                            $arrLangCodes = explode(',', $strLangGroup);
                            foreach($arrLangCodes as $strLangCode) {
                                $objLanguage = NarroLanguage::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->LanguageCode, $strLangCode), QQ::Equal(QQN::NarroLanguage()->Active, 1)));
                                if ($objLanguage instanceof NarroLanguage) {
                                    return $objLanguage;
                                }
                            }
                        }
                        else {
                            $objLanguage = NarroLanguage::QueryArray(QQ::AndCondition(QQ::Equal(QQN::NarroLanguage()->LanguageCode, $strLangGroup), QQ::Equal(QQN::NarroLanguage()->Active, 1)));
                            if ($objLanguage instanceof NarroLanguage) {
                                return $objLanguage;
                            }
                        }
                    }
                }
            }
            return false;
        }
    }

    spl_autoload_register(array('QApplication', 'Autoload'));
?>
