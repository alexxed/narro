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
    require_once(realpath(dirname(__FILE__) . '/../../../includes/configuration/prepend.inc.php'));
    require_once('Zend/Search/Lucene.php');

    if (isset($argv[1]) && $argv[1] == '--create')
        NarroTextSuggestionIndexer::CreateIndex($argv[2]);
    elseif (isset($argv[1]))
        var_export(NarroTextSuggestionIndexer::LoadSimilarSuggestionsByTextValue($argv[1], 1));

    class NarroTextSuggestionIndexer {
        public static $intBatchSize = 10000;
        public static $floatMinimumScore = 0.7;
        public static $intMaxValueLength = 300;
        public static $intMinValueLength = 2;

        public static function CreateIndex($strLocale) {
            $objLanguage = NarroLanguage::LoadByLanguageCode($strLocale);

            Zend_Search_Lucene_Storage_Directory_Filesystem::setDefaultFilePermissions(0666);

            try {
                $objLuceneIndex = Zend_Search_Lucene::open( __SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx');
            } catch( Zend_Search_Lucene_Exception $objEx) {
                $objLuceneIndex = Zend_Search_Lucene::create( __SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx');
            }

            if (file_exists(__SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx/last_suggestion_id')) {
                $intLastSuggestionId = file_get_contents(__SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx/last_suggestion_id');
                error_log('Resuming from suggestion_id=' . $intLastSuggestionId);
            }
            else
                $intLastSuggestionId = 0;

            $arrSuggestion = NarroSuggestion::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroSuggestion()->LanguageId, $objLanguage->LanguageId),
                    QQ::GreaterThan(QQN::NarroSuggestion()->SuggestionCharCount, self::$intMinValueLength),
                    QQ::LessThan(QQN::NarroSuggestion()->SuggestionCharCount, self::$intMaxValueLength),
                    QQ::GreaterThan(QQN::NarroSuggestion()->SuggestionId, $intLastSuggestionId)
                )
                , array(QQ::LimitInfo(self::$intBatchSize, 0), QQ::Distinct()));
            $intNumRows = count($arrSuggestion);

            foreach($arrSuggestion as $objSuggestion) {
                $objLuceneDoc = new Zend_Search_Lucene_Document();
                $objLuceneDoc->addField(Zend_Search_Lucene_Field::UnIndexed('suggestion_id', $objSuggestion->SuggestionId));
                $objLuceneDoc->addField(Zend_Search_Lucene_Field::UnIndexed('suggestion_value', $objSuggestion->SuggestionValue, 'UTF-8'));
                $objLuceneDoc->addField(Zend_Search_Lucene_Field::Text('text_value', $objSuggestion->Text->TextValue, 'UTF-8'));
                $objLuceneIndex->addDocument($objLuceneDoc);
                $intCurrentRow++;

                if (round(($intCurrentRow*100)/$intNumRows) != $intCurrentPercent) {
                    $intCurrentPercent = round(($intCurrentRow*100)/$intNumRows);
                    error_log(round(($intCurrentRow*100)/$intNumRows) . "%");
                }
                file_put_contents(__SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx/last_suggestion_id', $objSuggestion->SuggestionId);
            }
            error_log('Optimizing index...');
            $objLuceneIndex->commit();
            $objLuceneIndex->optimize();
            error_log('Count: ' . $objLuceneIndex->count());

            NarroUtils::RecursiveChmod(__SEARCH_INDEX_PATH__ . '/' . $strLocale . '/text_suggestion_idx/');
        }

        public static function LoadSimilarSuggestionsByTextValue($strTextValue, $intLanguageId, $intLimit = 0, $intOffset = 0) {
            Zend_Search_Lucene::setDefaultSearchField('text_value');

            $objLanguage = NarroLanguage::Load($intLanguageId);
            if (!$objLanguage instanceof NarroLanguage)
                return array('count'=>0, 'rows' => array());;

            if (!file_exists(__SEARCH_INDEX_PATH__ . '/' . $objLanguage->LanguageCode . '/text_suggestion_idx'))
                return array('count'=>0, 'rows' => array());;

            $objLuceneIndex = Zend_Search_Lucene::open( __SEARCH_INDEX_PATH__ . '/' . $objLanguage->LanguageCode . '/text_suggestion_idx');

            $objQuery = Zend_Search_Lucene_Search_QueryParser::parse($strTextValue);
            $arrHits = $objLuceneIndex->find($objQuery);

            if (is_array($arrHits)) {
                $intCnt = 0;
                foreach ($arrHits as $intIdx=>$objHit) {
                    if ($objHit->score < self::$floatMinimumScore) {
                        unset($arrHits[$intIdx]);
                        continue;
                    }

                    if ((($intOffset > 0 && $intIdx >=$intOffset) || $intOffset == 0) && (($intLimit>0 && $intCnt <= $intLimit) || $intLimit == 0)) {
                        $intCnt++;
                        $arrResults[] = array(
                            'suggestion_id' => $objHit->suggestion_id,
                            'text_value' => $objHit->text_value,
                            'suggestion_value' => $objHit->suggestion_value
                        );
                    }
                }
            }

            return array('count'=>count($arrHits), 'rows' => $arrResults);
        }

    }
?>
