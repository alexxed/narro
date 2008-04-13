<?php
class NarroString {


    /**
     *  Multibyte equivalent for htmlentities()
     *
     * @param string $str
     * @param string $encoding
     * @return string
     **/
    public static function HtmlEntities($strText, $strEncoding = 'utf-8') {
        $arrPattern = array('<', '>');
        $arrReplacement = array('&lt;', '&gt;');
        for ($i=0; $i<count($arrPattern); $i++) {
            $strText = mb_ereg_replace($arrPattern[$i], $arrReplacement[$i], $strText);
        }
        return $strText;
    }

    public static function Replace($strNeedle, $strReplacement, $strText, $intCount = null) {
        $intPos = mb_strpos($strText, $strNeedle, 0);
        $intCurrentResult = 0;
        while ($intPos !== false && ($intCount == null || $intCount > $intCurrentResult)) {
            $strText = mb_substr($strText, 0, $intPos) . $strReplacement . mb_substr($strText, $intPos + mb_strlen($strNeedle));
            if ($intPos + mb_strlen($strNeedle) >= mb_strlen($strText))
                $intPos = mb_strpos($strText, $strNeedle, $intPos + mb_strlen($strNeedle));
            $intCurrentResult++;
        }

        return $strText;
    }

}
?>