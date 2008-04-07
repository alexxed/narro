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
    class NarroRss {
        protected static $arrObjRss;
        protected static $arrDescription = '';
        protected static $intRssCount;

        public static function AddContext($objProject, $objFile, $objContext, $objText, $objSuggestion) {
            if (self::$intRssCount > 0) return true;

            if (count(self::$arrDescription[QApplication::$objUser->Language->LanguageId]) < 20) {
                if (!isset(self::$arrDescription[QApplication::$objUser->Language->LanguageId]))
                    self::$arrDescription[QApplication::$objUser->Language->LanguageId] = array();

                if (!in_array($objText->TextValue, self::$arrDescription[QApplication::$objUser->Language->LanguageId]))
                    self::$arrDescription[QApplication::$objUser->Language->LanguageId][] =  nl2br(htmlspecialchars($objText->TextValue, null,'utf-8'));
                return true;
            }

            if (self::$arrObjRss[QApplication::$objUser->Language->LanguageId] instanceof QRssFeed) {
                $objItem = new QRssItem(sprintf(t('New contexts to review for %s'), $objProject->ProjectName),
                    sprintf(__HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__ . '/narro_project_text_list.php?p=%d&tf=2&st=1&s=', $objProject->ProjectId),
                    join('<br />', self::$arrDescription[QApplication::$objUser->Language->LanguageId])
                    );

                $objItem->Author = QApplication::$objUser->Username;

                self::$arrObjRss[QApplication::$objUser->Language->LanguageId]->AddItem($objItem);
                self::$intRssCount++;
            }
            else {
                // Setup the Feed, itself
                self::$arrObjRss[QApplication::$objUser->Language->LanguageId] = new QRssFeed(sprintf(t('%s %s texts'), $objProject->ProjectName, QApplication::$objUser->Language->LanguageName), __HTTP_URL__ . __VIRTUAL_DIRECTORY__ . __SUBDIRECTORY__, sprintf(t('New texts to translate in %s for %s'), QApplication::$objUser->Language->LanguageName, $objProject->ProjectName));
                //self::$arrObjRss[QApplication::$objUser->Language->LanguageId]->Image = new QRssImage('http://www.qcodo.com/images/qcodo_smaller.png');
                self::$arrObjRss[QApplication::$objUser->Language->LanguageId]->PubDate = new QDateTime(QDateTime::Now);

            }
        }

        public static function Save($objProject, $objLanguage) {
            if (self::$arrObjRss[$objLanguage->LanguageId] instanceof QRssFeed )
                QApplication::$Cache->save(self::$arrObjRss[$objLanguage->LanguageId], sprintf('rssfeed_%d_%d', $objProject->ProjectId, $objLanguage->LanguageId));
        }


        public static function Run($intProjectId) {

            self::$arrObjRss[QApplication::$objUser->Language->LanguageId] = QApplication::$Cache->load(sprintf('rssfeed_%d_%d', $intProjectId, QApplication::$objUser->Language->LanguageId));

            if (self::$arrObjRss[QApplication::$objUser->Language->LanguageId] instanceof QRssFeed) {
                self::$arrObjRss[QApplication::$objUser->Language->LanguageId]->Run();
            }
            else {
                echo 'No rss';
            }
        }
    }
?>