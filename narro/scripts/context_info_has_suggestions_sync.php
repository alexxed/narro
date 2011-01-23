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

    require_once(dirname(__FILE__) . '/../includes/configuration/prepend.inc.php');

    if (!isset($argv)) exit;

    try {
        $strQuery = NarroContextInfo::GetQueryForConditions($objQueryBuilder, QQ::All());
    } catch (QCallerException $objExc) {
        $objExc->IncrementOffset();
        die($objExc);
    }

    printf("\nProcessing query '%s'... ", $strQuery);
    @ob_flush();

    // Perform the Query and Instantiate the Array Result
    $objDbResult = $objQueryBuilder->Database->Query($strQuery);

    printf("done\n");
    @ob_flush();

    $intModified = 0;
    $intNotModified = 0;
    $intCurrentRow;
    $intRowCount = $objDbResult->CountRows();
    while ($objDbRow = $objDbResult->GetNextRow()) {
        $objContextInfo = NarroContextInfo::InstantiateDbRow($objDbRow, null, $objQueryBuilder->ExpandAsArrayNodes, null, $objQueryBuilder->ColumnAliasArray);

        $blnHasSuggestions = $objContextInfo->HasSuggestions;
        $objContextInfo->HasSuggestions = QType::Cast(NarroSuggestion::CountByTextIdLanguageId($objContextInfo->Context->TextId, $objContextInfo->LanguageId), QType::Boolean);
        if ($blnHasSuggestions != $objContextInfo->HasSuggestions) {
            $objContextInfo->Save();
            echo "\r+";
            $intModified++;
        }
        else {
            echo "\r-";
            $intNotModified++;
        }

        $intCurrentRow++;
        $strProgress = '';
        for($i=1;$i<11;$i++) {
          if (($intCurrentRow * 10)/$intRowCount <= $i)
              $strProgress .= '-';
          else
              $strProgress .= '+';
        }

        printf("\rProgress: [%s], %s", $strProgress, sprintf('%d%% done %d modified, %d untouched', intval(($intCurrentRow * 100)/$intRowCount), $intModified, $intNotModified));
        @ob_flush();
    }

    printf("\rDone. %d modified, %d untouched", $intModified, $intNotModified);