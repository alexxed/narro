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
    class NarroSelfTranslate extends NarroPlugin {
        protected $strName;
        protected $arrErrors;
        const NARRO_PROJECT_ID = 5;

        public function __construct() {
            parent::__construct();
            $this->strName = t('Narro self translator');
        }

        public function ValidateSuggestion($strText, $strSuggestion) {
            self::UpdateTranslation($strText, $strSuggestion);

            return true;
        }

        public function SaveSuggestion($strOriginal, $strTranslation, $strContext, $objFile, $objProject) {
            if ($objProject->ProjectId == self::NARRO_PROJECT_ID)
                self::UpdateTranslation($strTranslation, $strTranslation);

            return array($strOriginal, $strTranslation, $strContext, $objFile, $objProject);
        }

        public function DeleteSuggestion($strText, $strSuggestion) {
            self::UpdateTranslation($strText, $strSuggestion);

            return true;
        }


        public function VoteSuggestion($strText, $strSuggestion) {
            self::UpdateTranslation($strText, $strSuggestion);

            return true;

        }

        public static function UpdateTranslation($strText, $strSuggestion) {
            $strIdentifier = sprintf('narro_%d', QApplication::$objUser->Language->LanguageId);
            $strUserIdentifier = sprintf('narro_%d_%d', QApplication::$objUser->Language->LanguageId, QApplication::$objUser->UserId);

            $arrTextSuggestions = QApplication::$Cache->load($strIdentifier);
            $arrUserSuggestions = QApplication::$Cache->load($strUserIdentifier);

            $arrTextSuggestions[md5($strText)] = $strSuggestion;
            $arrUserSuggestions[md5($strText)] = $strSuggestion;

            QApplication::$Cache->save($arrUserSuggestions, $strUserIdentifier);
            QApplication::$Cache->save($arrTextSuggestions, $strIdentifier);

        }

        public static function CacheTranslation($strText) {
            $strIdentifier = sprintf('narro_%d', QApplication::$objUser->Language->LanguageId);
            $strUserIdentifier = sprintf('narro_%d_%d', QApplication::$objUser->Language->LanguageId, QApplication::$objUser->UserId);

            $arrTextSuggestions = QApplication::$Cache->load($strIdentifier);
            $arrUserSuggestions = QApplication::$Cache->load($strUserIdentifier);

            if (
                $arrSuggestions =
                         NarroSuggestion::QueryArray(
                             QQ::AndCondition(
                                 QQ::Equal(QQN::NarroSuggestion()->Text->TextValueMd5, md5($strText)),
                                 QQ::Equal(QQN::NarroSuggestion()->LanguageId, QApplication::$objUser->Language->LanguageId)
                             )
                         )
               )
            {
                /**
                 * if this is the first suggestion, consider it most_voted
                 */
                if (count($arrSuggestions) == 1) {
                    $arrTextSuggestions[md5($strText)] = $arrSuggestions[0]->SuggestionValue;
                }
                else {
                    $intVoteCnt = 0;

                    foreach($arrSuggestions as $objSuggestion) {
                        $intSuggVotCnt = NarroSuggestionVote::QueryCount(
                                QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objSuggestion->SuggestionId)
                        );

                        $intUserVote = NarroSuggestionVote::QueryCount(
                            QQ::AndCondition(
                                QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $objSuggestion->SuggestionId),
                                QQ::Equal(QQN::NarroSuggestionVote()->UserId, QApplication::$objUser->UserId)
                            )
                        );

                        $arrUserSuggestions[md5($strText)] = $objSuggestion->SuggestionValue;

                        if ($intUserVote >= $intVoteCnt) {
                            $intVoteCnt = $intUserVote;
                            $arrTextSuggestions[md5($strText)] = $objSuggestion->SuggestionValue;
                        }
                    }
                }
            }

            if (isset($arrUserSuggestions))
                QApplication::$Cache->save($arrUserSuggestions, $strUserIdentifier);

            if (isset($arrTextSuggestions))
                QApplication::$Cache->save($arrTextSuggestions, $strIdentifier);

            if (isset($arrUserSuggestions[md5($strText)]))
                return $arrUserSuggestions[md5($strText)];
            elseif (isset($arrSuggestions[md5($strText)]))
                return $arrTextSuggestions[md5($strText)];
            else
                return $strText;

        }

        public static function Translate($strText) {
            $strIdentifier = sprintf('narro_%d', QApplication::$objUser->Language->LanguageId);
            $strUserIdentifier = sprintf('narro_%d_%d', QApplication::$objUser->Language->LanguageId, QApplication::$objUser->UserId);

            $arrTextSuggestions = QApplication::$Cache->load($strIdentifier);
            $arrUserSuggestions = QApplication::$Cache->load($strUserIdentifier);

            if (isset($arrUserSuggestions[md5($strText)]))
                return $arrUserSuggestions[md5($strText)];
            elseif (isset($arrTextSuggestions[md5($strText)]))
                return $arrTextSuggestions[md5($strText)];
            else
                return self::CacheTranslation($strText);
        }

        /////////////////////////
        // Public Properties: GET
        /////////////////////////
        public function __get($strName) {
            switch ($strName) {
                case "Errors": return $this->arrErrors;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
?>