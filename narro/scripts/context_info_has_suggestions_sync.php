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
	
	printf('Processing query "%s"... ', $strQuery);

	// Perform the Query and Instantiate the Array Result
	$objDbResult = $objQueryBuilder->Database->Query($strQuery);
	
	printf("done\n");

	$intModified = 0;
	$intNotModified = 0;
	while ($objDbRow = $objDbResult->GetNextRow()) {
		$objContextInfo = NarroContextInfo::InstantiateDbRow($objDbRow, null, $objQueryBuilder->ExpandAsArrayNodes, null, $objQueryBuilder->ColumnAliasArray);

        $blnHasSuggestions = $objContextInfo->HasSuggestions;
        $objContextInfo->HasSuggestions = QType::Cast(NarroSuggestion::CountByTextIdLanguageId($objContextInfo->Context->TextId, $objContextInfo->LanguageId), QType::Boolean);
        if ($blnHasSuggestions != $objContextInfo->HasSuggestions) {
            $objContextInfo->Save();
            echo '+';
            $intModified++;
        }
        else {
            echo '-';
            $intNotModified++;
        }
        
        ob_flush();
    }
    
    printf("\n%d modified, %d untouched\n", $intModified, $intNotModified);
