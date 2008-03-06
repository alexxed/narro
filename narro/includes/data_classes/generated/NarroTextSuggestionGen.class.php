<?php
	/**
	 * The abstract NarroTextSuggestionGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroTextSuggestion subclass which
	 * extends this NarroTextSuggestionGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroTextSuggestion class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroTextSuggestionGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroTextSuggestion from PK Info
		 * @param integer $intSuggestionId
		 * @return NarroTextSuggestion
		 */
		public static function Load($intSuggestionId) {
			// Use QuerySingle to Perform the Query
			return NarroTextSuggestion::QuerySingle(
				QQ::Equal(QQN::NarroTextSuggestion()->SuggestionId, $intSuggestionId)
			);
		}

		/**
		 * Load all NarroTextSuggestions
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroTextSuggestion::QueryArray to perform the LoadAll query
			try {
				return NarroTextSuggestion::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroTextSuggestions
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroTextSuggestion::QueryCount to perform the CountAll query
			return NarroTextSuggestion::QueryCount(QQ::All());
		}



		///////////////////////////////
		// QCODO QUERY-RELATED METHODS
		///////////////////////////////

		/**
		 * Static method to retrieve the Database object that owns this class.
		 * @return QDatabaseBase reference to the Database object that can query this class
		 */
		public static function GetDatabase() {
			return QApplication::$Database[1];
		}

		/**
		 * Internally called method to assist with calling Qcodo Query for this class
		 * on load methods.
		 * @param QQueryBuilder &$objQueryBuilder the QueryBuilder object that will be created
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with (sending in null will skip the PrepareStatement step)
		 * @param boolean $blnCountOnly only select a rowcount
		 * @return string the query statement
		 */
		protected static function BuildQueryStatement(&$objQueryBuilder, QQCondition $objConditions, $objOptionalClauses, $mixParameterArray, $blnCountOnly) {
			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroTextSuggestion-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_text_suggestion');
			NarroTextSuggestion::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_text_suggestion` AS `narro_text_suggestion`');

			// Set "CountOnly" option (if applicable)
			if ($blnCountOnly)
				$objQueryBuilder->SetCountOnlyFlag();

			// Apply Any Conditions
			if ($objConditions)
				$objConditions->UpdateQueryBuilder($objQueryBuilder);

			// Iterate through all the Optional Clauses (if any) and perform accordingly
			if ($objOptionalClauses) {
				if (!is_array($objOptionalClauses))
					throw new QCallerException('Optional Clauses must be a QQ::Clause() or an array of QQClause objects');
				foreach ($objOptionalClauses as $objClause)
					$objClause->UpdateQueryBuilder($objQueryBuilder);
			}

			// Get the SQL Statement
			$strQuery = $objQueryBuilder->GetStatement();

			// Prepare the Statement with the Query Parameters (if applicable)
			if ($mixParameterArray) {
				if (is_array($mixParameterArray)) {
					if (count($mixParameterArray))
						$strQuery = $objDatabase->PrepareStatement($strQuery, $mixParameterArray);

					// Ensure that there are no other Unresolved Named Parameters
					if (strpos($strQuery, chr(QQNamedValue::DelimiterCode) . '{') !== false)
						throw new QCallerException('Unresolved named parameters in the query');
				} else
					throw new QCallerException('Parameter Array must be an array of name-value parameter pairs');
			}

			// Return the Objects
			return $strQuery;
		}

		/**
		 * Static Qcodo Query method to query for a single NarroTextSuggestion object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextSuggestion the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextSuggestion::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroTextSuggestion object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextSuggestion::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroTextSuggestion objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextSuggestion[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextSuggestion::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextSuggestion::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroTextSuggestion objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextSuggestion::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and return the row_count
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);

			// Figure out if the query is using GroupBy
			$blnGrouped = false;

			if ($objOptionalClauses) foreach ($objOptionalClauses as $objClause) {
				if ($objClause instanceof QQGroupBy) {
					$blnGrouped = true;
					break;
				}
			}

			if ($blnGrouped)
				// Groups in this query - return the count of Groups (which is the count of all rows)
				return $objDbResult->CountRows();
			else {
				// No Groups - return the sql-calculated count(*) value
				$strDbRow = $objDbResult->FetchRow();
				return QType::Cast($strDbRow[0], QType::Integer);
			}
		}

/*		public static function QueryArrayCached($strConditions, $mixParameterArray = null) {
			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_text_suggestion_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroTextSuggestion-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroTextSuggestion::GetSelectFields($objQueryBuilder);
				NarroTextSuggestion::GetFromFields($objQueryBuilder);

				// Ensure the Passed-in Conditions is a string
				try {
					$strConditions = QType::Cast($strConditions, QType::String);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

				// Create the Conditions object, and apply it
				$objConditions = eval('return ' . $strConditions . ';');

				// Apply Any Conditions
				if ($objConditions)
					$objConditions->UpdateQueryBuilder($objQueryBuilder);

				// Get the SQL Statement
				$strQuery = $objQueryBuilder->GetStatement();

				// Save the SQL Statement in the Cache
				$objCache->SaveData($strQuery);
			}

			// Prepare the Statement with the Parameters
			if ($mixParameterArray)
				$strQuery = $objDatabase->PrepareStatement($strQuery, $mixParameterArray);

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objDatabase->Query($strQuery);
			return NarroTextSuggestion::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroTextSuggestion
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_text_suggestion`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`suggestion_id` AS ' . $strAliasPrefix . 'suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`user_id` AS ' . $strAliasPrefix . 'user_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`language_id` AS ' . $strAliasPrefix . 'language_id`');
			$objBuilder->AddSelectItem($strTableName . '.`suggestion_value` AS ' . $strAliasPrefix . 'suggestion_value`');
			$objBuilder->AddSelectItem($strTableName . '.`suggestion_value_md5` AS ' . $strAliasPrefix . 'suggestion_value_md5`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroTextSuggestion from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroTextSuggestion::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroTextSuggestion
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;

			// See if we're doing an array expansion on the previous item
			if (($strExpandAsArrayNodes) && ($objPreviousItem) &&
				($objPreviousItem->intSuggestionId == $objDbRow->GetColumn($strAliasPrefix . 'suggestion_id', 'Integer'))) {

				// We are.  Now, prepare to check for ExpandAsArray clauses
				$blnExpandedViaArray = false;
				if (!$strAliasPrefix)
					$strAliasPrefix = 'narro_text_suggestion__';


				if ((array_key_exists($strAliasPrefix . 'narrosuggestioncommentassuggestion__comment_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestioncommentassuggestion__comment_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroSuggestionCommentAsSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroSuggestionCommentAsSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroSuggestionComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestioncommentassuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroSuggestionCommentAsSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroSuggestionCommentAsSuggestionArray, NarroSuggestionComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestioncommentassuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrosuggestionvoteassuggestion__suggestion_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestionvoteassuggestion__suggestion_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroSuggestionVoteAsSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroSuggestionVoteAsSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteassuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroSuggestionVoteAsSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroSuggestionVoteAsSuggestionArray, NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteassuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextasvalidsuggestion__context_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextasvalidsuggestion__context_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextAsValidSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextAsValidSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextasvalidsuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextAsValidSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextAsValidSuggestionArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextasvalidsuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextaspopularsuggestion__context_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextaspopularsuggestion__context_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextAsPopularSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextAsPopularSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextaspopularsuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextAsPopularSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextAsPopularSuggestionArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextaspopularsuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__plural_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__plural_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextPluralAsValidSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextPluralAsValidSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextPluralAsValidSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextPluralAsValidSuggestionArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__plural_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__plural_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextPluralAsPopularSuggestionArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextPluralAsPopularSuggestionArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextPluralAsPopularSuggestionArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextPluralAsPopularSuggestionArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				// Either return false to signal array expansion, or check-to-reset the Alias prefix and move on
				if ($blnExpandedViaArray)
					return false;
				else if ($strAliasPrefix == 'narro_text_suggestion__')
					$strAliasPrefix = null;
			}

			// Create a new instance of the NarroTextSuggestion object
			$objToReturn = new NarroTextSuggestion();
			$objToReturn->__blnRestored = true;

			$objToReturn->intSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'suggestion_id', 'Integer');
			$objToReturn->intUserId = $objDbRow->GetColumn($strAliasPrefix . 'user_id', 'Integer');
			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->intLanguageId = $objDbRow->GetColumn($strAliasPrefix . 'language_id', 'Integer');
			$objToReturn->strSuggestionValue = $objDbRow->GetColumn($strAliasPrefix . 'suggestion_value', 'Blob');
			$objToReturn->strSuggestionValueMd5 = $objDbRow->GetColumn($strAliasPrefix . 'suggestion_value_md5', 'VarChar');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_text_suggestion__';

			// Check for User Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'user_id__user_id')))
				$objToReturn->objUser = NarroUser::InstantiateDbRow($objDbRow, $strAliasPrefix . 'user_id__', $strExpandAsArrayNodes);

			// Check for Text Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'text_id__text_id')))
				$objToReturn->objText = NarroText::InstantiateDbRow($objDbRow, $strAliasPrefix . 'text_id__', $strExpandAsArrayNodes);

			// Check for Language Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'language_id__language_id')))
				$objToReturn->objLanguage = NarroLanguage::InstantiateDbRow($objDbRow, $strAliasPrefix . 'language_id__', $strExpandAsArrayNodes);




			// Check for NarroSuggestionCommentAsSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestioncommentassuggestion__comment_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrosuggestioncommentassuggestion__comment_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroSuggestionCommentAsSuggestionArray, NarroSuggestionComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestioncommentassuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroSuggestionCommentAsSuggestion = NarroSuggestionComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestioncommentassuggestion__', $strExpandAsArrayNodes);
			}

			// Check for NarroSuggestionVoteAsSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestionvoteassuggestion__suggestion_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrosuggestionvoteassuggestion__suggestion_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroSuggestionVoteAsSuggestionArray, NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteassuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroSuggestionVoteAsSuggestion = NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteassuggestion__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextAsValidSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextasvalidsuggestion__context_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextasvalidsuggestion__context_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextAsValidSuggestionArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextasvalidsuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextAsValidSuggestion = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextasvalidsuggestion__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextAsPopularSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextaspopularsuggestion__context_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextaspopularsuggestion__context_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextAsPopularSuggestionArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextaspopularsuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextAsPopularSuggestion = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextaspopularsuggestion__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextPluralAsValidSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__plural_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__plural_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextPluralAsValidSuggestionArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextPluralAsValidSuggestion = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralasvalidsuggestion__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextPluralAsPopularSuggestion Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__plural_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__plural_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextPluralAsPopularSuggestionArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextPluralAsPopularSuggestion = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralaspopularsuggestion__', $strExpandAsArrayNodes);
			}

			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroTextSuggestions from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroTextSuggestion[]
		 */
		public static function InstantiateDbResult(QDatabaseResultBase $objDbResult, $strExpandAsArrayNodes = null) {
			$objToReturn = array();

			// If blank resultset, then return empty array
			if (!$objDbResult)
				return $objToReturn;

			// Load up the return array with each row
			if ($strExpandAsArrayNodes) {
				$objLastRowItem = null;
				while ($objDbRow = $objDbResult->GetNextRow()) {
					$objItem = NarroTextSuggestion::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroTextSuggestion::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroTextSuggestion object,
		 * by SuggestionId Index(es)
		 * @param integer $intSuggestionId
		 * @return NarroTextSuggestion
		*/
		public static function LoadBySuggestionId($intSuggestionId) {
			return NarroTextSuggestion::QuerySingle(
				QQ::Equal(QQN::NarroTextSuggestion()->SuggestionId, $intSuggestionId)
			);
		}
			
		/**
		 * Load a single NarroTextSuggestion object,
		 * by TextId, SuggestionValueMd5, LanguageId Index(es)
		 * @param integer $intTextId
		 * @param string $strSuggestionValueMd5
		 * @param integer $intLanguageId
		 * @return NarroTextSuggestion
		*/
		public static function LoadByTextIdSuggestionValueMd5LanguageId($intTextId, $strSuggestionValueMd5, $intLanguageId) {
			return NarroTextSuggestion::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroTextSuggestion()->TextId, $intTextId),
				QQ::Equal(QQN::NarroTextSuggestion()->SuggestionValueMd5, $strSuggestionValueMd5),
				QQ::Equal(QQN::NarroTextSuggestion()->LanguageId, $intLanguageId)
				)
			);
		}
			
		/**
		 * Load an array of NarroTextSuggestion objects,
		 * by UserId Index(es)
		 * @param integer $intUserId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		*/
		public static function LoadArrayByUserId($intUserId, $objOptionalClauses = null) {
			// Call NarroTextSuggestion::QueryArray to perform the LoadArrayByUserId query
			try {
				return NarroTextSuggestion::QueryArray(
					QQ::Equal(QQN::NarroTextSuggestion()->UserId, $intUserId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextSuggestions
		 * by UserId Index(es)
		 * @param integer $intUserId
		 * @return int
		*/
		public static function CountByUserId($intUserId) {
			// Call NarroTextSuggestion::QueryCount to perform the CountByUserId query
			return NarroTextSuggestion::QueryCount(
				QQ::Equal(QQN::NarroTextSuggestion()->UserId, $intUserId)
			);
		}
			
		/**
		 * Load an array of NarroTextSuggestion objects,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		*/
		public static function LoadArrayByTextId($intTextId, $objOptionalClauses = null) {
			// Call NarroTextSuggestion::QueryArray to perform the LoadArrayByTextId query
			try {
				return NarroTextSuggestion::QueryArray(
					QQ::Equal(QQN::NarroTextSuggestion()->TextId, $intTextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextSuggestions
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return int
		*/
		public static function CountByTextId($intTextId) {
			// Call NarroTextSuggestion::QueryCount to perform the CountByTextId query
			return NarroTextSuggestion::QueryCount(
				QQ::Equal(QQN::NarroTextSuggestion()->TextId, $intTextId)
			);
		}
			
		/**
		 * Load an array of NarroTextSuggestion objects,
		 * by LanguageId Index(es)
		 * @param integer $intLanguageId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		*/
		public static function LoadArrayByLanguageId($intLanguageId, $objOptionalClauses = null) {
			// Call NarroTextSuggestion::QueryArray to perform the LoadArrayByLanguageId query
			try {
				return NarroTextSuggestion::QueryArray(
					QQ::Equal(QQN::NarroTextSuggestion()->LanguageId, $intLanguageId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextSuggestions
		 * by LanguageId Index(es)
		 * @param integer $intLanguageId
		 * @return int
		*/
		public static function CountByLanguageId($intLanguageId) {
			// Call NarroTextSuggestion::QueryCount to perform the CountByLanguageId query
			return NarroTextSuggestion::QueryCount(
				QQ::Equal(QQN::NarroTextSuggestion()->LanguageId, $intLanguageId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroTextSuggestion
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_text_suggestion` (
							`user_id`,
							`text_id`,
							`language_id`,
							`suggestion_value`,
							`suggestion_value_md5`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intUserId) . ',
							' . $objDatabase->SqlVariable($this->intTextId) . ',
							' . $objDatabase->SqlVariable($this->intLanguageId) . ',
							' . $objDatabase->SqlVariable($this->strSuggestionValue) . ',
							' . $objDatabase->SqlVariable($this->strSuggestionValueMd5) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intSuggestionId = $objDatabase->InsertId('narro_text_suggestion', 'suggestion_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_text_suggestion`
						SET
							`user_id` = ' . $objDatabase->SqlVariable($this->intUserId) . ',
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ',
							`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . ',
							`suggestion_value` = ' . $objDatabase->SqlVariable($this->strSuggestionValue) . ',
							`suggestion_value_md5` = ' . $objDatabase->SqlVariable($this->strSuggestionValueMd5) . '
						WHERE
							`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
					');
				}

			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Update __blnRestored and any Non-Identity PK Columns (if applicable)
			$this->__blnRestored = true;


			// Return 
			return $mixToReturn;
		}

				/**
		 * Delete this NarroTextSuggestion
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroTextSuggestion with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '');
		}

		/**
		 * Delete all NarroTextSuggestions
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`');
		}

		/**
		 * Truncate narro_text_suggestion table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_text_suggestion`');
		}



		////////////////////
		// PUBLIC OVERRIDERS
		////////////////////

				/**
		 * Override method to perform a property "Get"
		 * This will get the value of $strName
		 *
		 * @param string $strName Name of the property to get
		 * @return mixed
		 */
		public function __get($strName) {
			switch ($strName) {
				///////////////////
				// Member Variables
				///////////////////
				case 'SuggestionId':
					/**
					 * Gets the value for intSuggestionId (Read-Only PK)
					 * @return integer
					 */
					return $this->intSuggestionId;

				case 'UserId':
					/**
					 * Gets the value for intUserId 
					 * @return integer
					 */
					return $this->intUserId;

				case 'TextId':
					/**
					 * Gets the value for intTextId (Not Null)
					 * @return integer
					 */
					return $this->intTextId;

				case 'LanguageId':
					/**
					 * Gets the value for intLanguageId (Not Null)
					 * @return integer
					 */
					return $this->intLanguageId;

				case 'SuggestionValue':
					/**
					 * Gets the value for strSuggestionValue (Not Null)
					 * @return string
					 */
					return $this->strSuggestionValue;

				case 'SuggestionValueMd5':
					/**
					 * Gets the value for strSuggestionValueMd5 (Not Null)
					 * @return string
					 */
					return $this->strSuggestionValueMd5;


				///////////////////
				// Member Objects
				///////////////////
				case 'User':
					/**
					 * Gets the value for the NarroUser object referenced by intUserId 
					 * @return NarroUser
					 */
					try {
						if ((!$this->objUser) && (!is_null($this->intUserId)))
							$this->objUser = NarroUser::Load($this->intUserId);
						return $this->objUser;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Text':
					/**
					 * Gets the value for the NarroText object referenced by intTextId (Not Null)
					 * @return NarroText
					 */
					try {
						if ((!$this->objText) && (!is_null($this->intTextId)))
							$this->objText = NarroText::Load($this->intTextId);
						return $this->objText;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Language':
					/**
					 * Gets the value for the NarroLanguage object referenced by intLanguageId (Not Null)
					 * @return NarroLanguage
					 */
					try {
						if ((!$this->objLanguage) && (!is_null($this->intLanguageId)))
							$this->objLanguage = NarroLanguage::Load($this->intLanguageId);
						return $this->objLanguage;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

				case '_NarroSuggestionCommentAsSuggestion':
					/**
					 * Gets the value for the private _objNarroSuggestionCommentAsSuggestion (Read-Only)
					 * if set due to an expansion on the narro_suggestion_comment.suggestion_id reverse relationship
					 * @return NarroSuggestionComment
					 */
					return $this->_objNarroSuggestionCommentAsSuggestion;

				case '_NarroSuggestionCommentAsSuggestionArray':
					/**
					 * Gets the value for the private _objNarroSuggestionCommentAsSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_suggestion_comment.suggestion_id reverse relationship
					 * @return NarroSuggestionComment[]
					 */
					return (array) $this->_objNarroSuggestionCommentAsSuggestionArray;

				case '_NarroSuggestionVoteAsSuggestion':
					/**
					 * Gets the value for the private _objNarroSuggestionVoteAsSuggestion (Read-Only)
					 * if set due to an expansion on the narro_suggestion_vote.suggestion_id reverse relationship
					 * @return NarroSuggestionVote
					 */
					return $this->_objNarroSuggestionVoteAsSuggestion;

				case '_NarroSuggestionVoteAsSuggestionArray':
					/**
					 * Gets the value for the private _objNarroSuggestionVoteAsSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_suggestion_vote.suggestion_id reverse relationship
					 * @return NarroSuggestionVote[]
					 */
					return (array) $this->_objNarroSuggestionVoteAsSuggestionArray;

				case '_NarroTextContextAsValidSuggestion':
					/**
					 * Gets the value for the private _objNarroTextContextAsValidSuggestion (Read-Only)
					 * if set due to an expansion on the narro_text_context.valid_suggestion_id reverse relationship
					 * @return NarroTextContext
					 */
					return $this->_objNarroTextContextAsValidSuggestion;

				case '_NarroTextContextAsValidSuggestionArray':
					/**
					 * Gets the value for the private _objNarroTextContextAsValidSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context.valid_suggestion_id reverse relationship
					 * @return NarroTextContext[]
					 */
					return (array) $this->_objNarroTextContextAsValidSuggestionArray;

				case '_NarroTextContextAsPopularSuggestion':
					/**
					 * Gets the value for the private _objNarroTextContextAsPopularSuggestion (Read-Only)
					 * if set due to an expansion on the narro_text_context.popular_suggestion_id reverse relationship
					 * @return NarroTextContext
					 */
					return $this->_objNarroTextContextAsPopularSuggestion;

				case '_NarroTextContextAsPopularSuggestionArray':
					/**
					 * Gets the value for the private _objNarroTextContextAsPopularSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context.popular_suggestion_id reverse relationship
					 * @return NarroTextContext[]
					 */
					return (array) $this->_objNarroTextContextAsPopularSuggestionArray;

				case '_NarroTextContextPluralAsValidSuggestion':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsValidSuggestion (Read-Only)
					 * if set due to an expansion on the narro_text_context_plural.valid_suggestion_id reverse relationship
					 * @return NarroTextContextPlural
					 */
					return $this->_objNarroTextContextPluralAsValidSuggestion;

				case '_NarroTextContextPluralAsValidSuggestionArray':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsValidSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context_plural.valid_suggestion_id reverse relationship
					 * @return NarroTextContextPlural[]
					 */
					return (array) $this->_objNarroTextContextPluralAsValidSuggestionArray;

				case '_NarroTextContextPluralAsPopularSuggestion':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsPopularSuggestion (Read-Only)
					 * if set due to an expansion on the narro_text_context_plural.popular_suggestion_id reverse relationship
					 * @return NarroTextContextPlural
					 */
					return $this->_objNarroTextContextPluralAsPopularSuggestion;

				case '_NarroTextContextPluralAsPopularSuggestionArray':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsPopularSuggestionArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context_plural.popular_suggestion_id reverse relationship
					 * @return NarroTextContextPlural[]
					 */
					return (array) $this->_objNarroTextContextPluralAsPopularSuggestionArray;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

				/**
		 * Override method to perform a property "Set"
		 * This will set the property $strName to be $mixValue
		 *
		 * @param string $strName Name of the property to set
		 * @param string $mixValue New value of the property
		 * @return mixed
		 */
		public function __set($strName, $mixValue) {
			switch ($strName) {
				///////////////////
				// Member Variables
				///////////////////
				case 'UserId':
					/**
					 * Sets the value for intUserId 
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objUser = null;
						return ($this->intUserId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'TextId':
					/**
					 * Sets the value for intTextId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objText = null;
						return ($this->intTextId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'LanguageId':
					/**
					 * Sets the value for intLanguageId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objLanguage = null;
						return ($this->intLanguageId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'SuggestionValue':
					/**
					 * Sets the value for strSuggestionValue (Not Null)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strSuggestionValue = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'SuggestionValueMd5':
					/**
					 * Sets the value for strSuggestionValueMd5 (Not Null)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strSuggestionValueMd5 = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
				case 'User':
					/**
					 * Sets the value for the NarroUser object referenced by intUserId 
					 * @param NarroUser $mixValue
					 * @return NarroUser
					 */
					if (is_null($mixValue)) {
						$this->intUserId = null;
						$this->objUser = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroUser object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroUser');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroUser object
						if (is_null($mixValue->UserId))
							throw new QCallerException('Unable to set an unsaved User for this NarroTextSuggestion');

						// Update Local Member Variables
						$this->objUser = $mixValue;
						$this->intUserId = $mixValue->UserId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'Text':
					/**
					 * Sets the value for the NarroText object referenced by intTextId (Not Null)
					 * @param NarroText $mixValue
					 * @return NarroText
					 */
					if (is_null($mixValue)) {
						$this->intTextId = null;
						$this->objText = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroText object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroText');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroText object
						if (is_null($mixValue->TextId))
							throw new QCallerException('Unable to set an unsaved Text for this NarroTextSuggestion');

						// Update Local Member Variables
						$this->objText = $mixValue;
						$this->intTextId = $mixValue->TextId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'Language':
					/**
					 * Sets the value for the NarroLanguage object referenced by intLanguageId (Not Null)
					 * @param NarroLanguage $mixValue
					 * @return NarroLanguage
					 */
					if (is_null($mixValue)) {
						$this->intLanguageId = null;
						$this->objLanguage = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroLanguage object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroLanguage');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroLanguage object
						if (is_null($mixValue->LanguageId))
							throw new QCallerException('Unable to set an unsaved Language for this NarroTextSuggestion');

						// Update Local Member Variables
						$this->objLanguage = $mixValue;
						$this->intLanguageId = $mixValue->LanguageId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				default:
					try {
						return parent::__set($strName, $mixValue);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/**
		 * Lookup a VirtualAttribute value (if applicable).  Returns NULL if none found.
		 * @param string $strName
		 * @return string
		 */
		public function GetVirtualAttribute($strName) {
			if (array_key_exists($strName, $this->__strVirtualAttributeArray))
				return $this->__strVirtualAttributeArray[$strName];
			return null;
		}



		///////////////////////////////
		// ASSOCIATED OBJECTS
		///////////////////////////////

			
		
		// Related Objects' Methods for NarroSuggestionCommentAsSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroSuggestionCommentsAsSuggestion as an array of NarroSuggestionComment objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionComment[]
		*/ 
		public function GetNarroSuggestionCommentAsSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroSuggestionComment::LoadArrayBySuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroSuggestionCommentsAsSuggestion
		 * @return int
		*/ 
		public function CountNarroSuggestionCommentsAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroSuggestionComment::CountBySuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroSuggestionCommentAsSuggestion
		 * @param NarroSuggestionComment $objNarroSuggestionComment
		 * @return void
		*/ 
		public function AssociateNarroSuggestionCommentAsSuggestion(NarroSuggestionComment $objNarroSuggestionComment) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionCommentAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionCommentAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_comment`
				SET
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionComment->CommentId) . '
			');
		}

		/**
		 * Unassociates a NarroSuggestionCommentAsSuggestion
		 * @param NarroSuggestionComment $objNarroSuggestionComment
		 * @return void
		*/ 
		public function UnassociateNarroSuggestionCommentAsSuggestion(NarroSuggestionComment $objNarroSuggestionComment) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_comment`
				SET
					`suggestion_id` = null
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionComment->CommentId) . ' AND
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroSuggestionCommentsAsSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroSuggestionCommentsAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_comment`
				SET
					`suggestion_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroSuggestionCommentAsSuggestion
		 * @param NarroSuggestionComment $objNarroSuggestionComment
		 * @return void
		*/ 
		public function DeleteAssociatedNarroSuggestionCommentAsSuggestion(NarroSuggestionComment $objNarroSuggestionComment) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_comment`
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionComment->CommentId) . ' AND
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroSuggestionCommentsAsSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroSuggestionCommentsAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionCommentAsSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_comment`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroSuggestionVoteAsSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroSuggestionVotesAsSuggestion as an array of NarroSuggestionVote objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		*/ 
		public function GetNarroSuggestionVoteAsSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroSuggestionVote::LoadArrayBySuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroSuggestionVotesAsSuggestion
		 * @return int
		*/ 
		public function CountNarroSuggestionVotesAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroSuggestionVote::CountBySuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroSuggestionVoteAsSuggestion
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function AssociateNarroSuggestionVoteAsSuggestion(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionVoteAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionVoteAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . '
			');
		}

		/**
		 * Unassociates a NarroSuggestionVoteAsSuggestion
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function UnassociateNarroSuggestionVoteAsSuggestion(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`suggestion_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . ' AND
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroSuggestionVotesAsSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroSuggestionVotesAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`suggestion_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroSuggestionVoteAsSuggestion
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function DeleteAssociatedNarroSuggestionVoteAsSuggestion(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this NarroTextSuggestion with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . ' AND
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroSuggestionVotesAsSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroSuggestionVotesAsSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextAsValidSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextsAsValidSuggestion as an array of NarroTextContext objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/ 
		public function GetNarroTextContextAsValidSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroTextContext::LoadArrayByValidSuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextsAsValidSuggestion
		 * @return int
		*/ 
		public function CountNarroTextContextsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroTextContext::CountByValidSuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroTextContextAsValidSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function AssociateNarroTextContextAsValidSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextAsValidSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function UnassociateNarroTextContextAsValidSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`valid_suggestion_id` = null
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextsAsValidSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`valid_suggestion_id` = null
				WHERE
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextAsValidSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextAsValidSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextsAsValidSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsValidSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextAsPopularSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextsAsPopularSuggestion as an array of NarroTextContext objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/ 
		public function GetNarroTextContextAsPopularSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroTextContext::LoadArrayByPopularSuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextsAsPopularSuggestion
		 * @return int
		*/ 
		public function CountNarroTextContextsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroTextContext::CountByPopularSuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroTextContextAsPopularSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function AssociateNarroTextContextAsPopularSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextAsPopularSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function UnassociateNarroTextContextAsPopularSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`popular_suggestion_id` = null
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextsAsPopularSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`popular_suggestion_id` = null
				WHERE
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextAsPopularSuggestion
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextAsPopularSuggestion(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextsAsPopularSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsPopularSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextPluralAsValidSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextPluralsAsValidSuggestion as an array of NarroTextContextPlural objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/ 
		public function GetNarroTextContextPluralAsValidSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroTextContextPlural::LoadArrayByValidSuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextPluralsAsValidSuggestion
		 * @return int
		*/ 
		public function CountNarroTextContextPluralsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroTextContextPlural::CountByValidSuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroTextContextPluralAsValidSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function AssociateNarroTextContextPluralAsValidSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextPluralAsValidSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function UnassociateNarroTextContextPluralAsValidSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`valid_suggestion_id` = null
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextPluralsAsValidSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextPluralsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`valid_suggestion_id` = null
				WHERE
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextPluralAsValidSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextPluralAsValidSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextPluralsAsValidSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextPluralsAsValidSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsValidSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextPluralAsPopularSuggestion
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextPluralsAsPopularSuggestion as an array of NarroTextContextPlural objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/ 
		public function GetNarroTextContextPluralAsPopularSuggestionArray($objOptionalClauses = null) {
			if ((is_null($this->intSuggestionId)))
				return array();

			try {
				return NarroTextContextPlural::LoadArrayByPopularSuggestionId($this->intSuggestionId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextPluralsAsPopularSuggestion
		 * @return int
		*/ 
		public function CountNarroTextContextPluralsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				return 0;

			return NarroTextContextPlural::CountByPopularSuggestionId($this->intSuggestionId);
		}

		/**
		 * Associates a NarroTextContextPluralAsPopularSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function AssociateNarroTextContextPluralAsPopularSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextPluralAsPopularSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function UnassociateNarroTextContextPluralAsPopularSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`popular_suggestion_id` = null
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextPluralsAsPopularSuggestion
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextPluralsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`popular_suggestion_id` = null
				WHERE
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextPluralAsPopularSuggestion
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextPluralAsPopularSuggestion(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this unsaved NarroTextSuggestion.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this NarroTextSuggestion with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextPluralsAsPopularSuggestion
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextPluralsAsPopularSuggestion() {
			if ((is_null($this->intSuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsPopularSuggestion on this unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextSuggestion::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . '
			');
		}




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_text_suggestion.suggestion_id
		 * @var integer intSuggestionId
		 */
		protected $intSuggestionId;
		const SuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_suggestion.user_id
		 * @var integer intUserId
		 */
		protected $intUserId;
		const UserIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_suggestion.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_suggestion.language_id
		 * @var integer intLanguageId
		 */
		protected $intLanguageId;
		const LanguageIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_suggestion.suggestion_value
		 * @var string strSuggestionValue
		 */
		protected $strSuggestionValue;
		const SuggestionValueDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_suggestion.suggestion_value_md5
		 * @var string strSuggestionValueMd5
		 */
		protected $strSuggestionValueMd5;
		const SuggestionValueMd5MaxLength = 128;
		const SuggestionValueMd5Default = null;


		/**
		 * Private member variable that stores a reference to a single NarroSuggestionCommentAsSuggestion object
		 * (of type NarroSuggestionComment), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_suggestion_comment association table.
		 * @var NarroSuggestionComment _objNarroSuggestionCommentAsSuggestion;
		 */
		private $_objNarroSuggestionCommentAsSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroSuggestionCommentAsSuggestion objects
		 * (of type NarroSuggestionComment[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_suggestion_comment association table.
		 * @var NarroSuggestionComment[] _objNarroSuggestionCommentAsSuggestionArray;
		 */
		private $_objNarroSuggestionCommentAsSuggestionArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroSuggestionVoteAsSuggestion object
		 * (of type NarroSuggestionVote), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_suggestion_vote association table.
		 * @var NarroSuggestionVote _objNarroSuggestionVoteAsSuggestion;
		 */
		private $_objNarroSuggestionVoteAsSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroSuggestionVoteAsSuggestion objects
		 * (of type NarroSuggestionVote[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_suggestion_vote association table.
		 * @var NarroSuggestionVote[] _objNarroSuggestionVoteAsSuggestionArray;
		 */
		private $_objNarroSuggestionVoteAsSuggestionArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextAsValidSuggestion object
		 * (of type NarroTextContext), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_text_context association table.
		 * @var NarroTextContext _objNarroTextContextAsValidSuggestion;
		 */
		private $_objNarroTextContextAsValidSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextAsValidSuggestion objects
		 * (of type NarroTextContext[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_text_context association table.
		 * @var NarroTextContext[] _objNarroTextContextAsValidSuggestionArray;
		 */
		private $_objNarroTextContextAsValidSuggestionArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextAsPopularSuggestion object
		 * (of type NarroTextContext), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_text_context association table.
		 * @var NarroTextContext _objNarroTextContextAsPopularSuggestion;
		 */
		private $_objNarroTextContextAsPopularSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextAsPopularSuggestion objects
		 * (of type NarroTextContext[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_text_context association table.
		 * @var NarroTextContext[] _objNarroTextContextAsPopularSuggestionArray;
		 */
		private $_objNarroTextContextAsPopularSuggestionArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextPluralAsValidSuggestion object
		 * (of type NarroTextContextPlural), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural _objNarroTextContextPluralAsValidSuggestion;
		 */
		private $_objNarroTextContextPluralAsValidSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextPluralAsValidSuggestion objects
		 * (of type NarroTextContextPlural[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural[] _objNarroTextContextPluralAsValidSuggestionArray;
		 */
		private $_objNarroTextContextPluralAsValidSuggestionArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextPluralAsPopularSuggestion object
		 * (of type NarroTextContextPlural), if this NarroTextSuggestion object was restored with
		 * an expansion on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural _objNarroTextContextPluralAsPopularSuggestion;
		 */
		private $_objNarroTextContextPluralAsPopularSuggestion;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextPluralAsPopularSuggestion objects
		 * (of type NarroTextContextPlural[]), if this NarroTextSuggestion object was restored with
		 * an ExpandAsArray on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural[] _objNarroTextContextPluralAsPopularSuggestionArray;
		 */
		private $_objNarroTextContextPluralAsPopularSuggestionArray = array();

		/**
		 * Protected array of virtual attributes for this object (e.g. extra/other calculated and/or non-object bound
		 * columns from the run-time database query result for this object).  Used by InstantiateDbRow and
		 * GetVirtualAttribute.
		 * @var string[] $__strVirtualAttributeArray
		 */
		protected $__strVirtualAttributeArray = array();

		/**
		 * Protected internal member variable that specifies whether or not this object is Restored from the database.
		 * Used by Save() to determine if Save() should perform a db UPDATE or INSERT.
		 * @var bool __blnRestored;
		 */
		protected $__blnRestored;



		///////////////////////////////
		// PROTECTED MEMBER OBJECTS
		///////////////////////////////

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_suggestion.user_id.
		 *
		 * NOTE: Always use the User property getter to correctly retrieve this NarroUser object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroUser objUser
		 */
		protected $objUser;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_suggestion.text_id.
		 *
		 * NOTE: Always use the Text property getter to correctly retrieve this NarroText object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroText objText
		 */
		protected $objText;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_suggestion.language_id.
		 *
		 * NOTE: Always use the Language property getter to correctly retrieve this NarroLanguage object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroLanguage objLanguage
		 */
		protected $objLanguage;






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroTextSuggestion"><sequence>';
			$strToReturn .= '<element name="SuggestionId" type="xsd:int"/>';
			$strToReturn .= '<element name="User" type="xsd1:NarroUser"/>';
			$strToReturn .= '<element name="Text" type="xsd1:NarroText"/>';
			$strToReturn .= '<element name="Language" type="xsd1:NarroLanguage"/>';
			$strToReturn .= '<element name="SuggestionValue" type="xsd:string"/>';
			$strToReturn .= '<element name="SuggestionValueMd5" type="xsd:string"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroTextSuggestion', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroTextSuggestion'] = NarroTextSuggestion::GetSoapComplexTypeXml();
				NarroUser::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroText::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroLanguage::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroTextSuggestion::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroTextSuggestion();
			if (property_exists($objSoapObject, 'SuggestionId'))
				$objToReturn->intSuggestionId = $objSoapObject->SuggestionId;
			if ((property_exists($objSoapObject, 'User')) &&
				($objSoapObject->User))
				$objToReturn->User = NarroUser::GetObjectFromSoapObject($objSoapObject->User);
			if ((property_exists($objSoapObject, 'Text')) &&
				($objSoapObject->Text))
				$objToReturn->Text = NarroText::GetObjectFromSoapObject($objSoapObject->Text);
			if ((property_exists($objSoapObject, 'Language')) &&
				($objSoapObject->Language))
				$objToReturn->Language = NarroLanguage::GetObjectFromSoapObject($objSoapObject->Language);
			if (property_exists($objSoapObject, 'SuggestionValue'))
				$objToReturn->strSuggestionValue = $objSoapObject->SuggestionValue;
			if (property_exists($objSoapObject, 'SuggestionValueMd5'))
				$objToReturn->strSuggestionValueMd5 = $objSoapObject->SuggestionValueMd5;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroTextSuggestion::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objUser)
				$objObject->objUser = NarroUser::GetSoapObjectFromObject($objObject->objUser, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intUserId = null;
			if ($objObject->objText)
				$objObject->objText = NarroText::GetSoapObjectFromObject($objObject->objText, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intTextId = null;
			if ($objObject->objLanguage)
				$objObject->objLanguage = NarroLanguage::GetSoapObjectFromObject($objObject->objLanguage, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intLanguageId = null;
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroTextSuggestion extends QQNode {
		protected $strTableName = 'narro_text_suggestion';
		protected $strPrimaryKey = 'suggestion_id';
		protected $strClassName = 'NarroTextSuggestion';
		public function __get($strName) {
			switch ($strName) {
				case 'SuggestionId':
					return new QQNode('suggestion_id', 'integer', $this);
				case 'UserId':
					return new QQNode('user_id', 'integer', $this);
				case 'User':
					return new QQNodeNarroUser('user_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'Language':
					return new QQNodeNarroLanguage('language_id', 'integer', $this);
				case 'SuggestionValue':
					return new QQNode('suggestion_value', 'string', $this);
				case 'SuggestionValueMd5':
					return new QQNode('suggestion_value_md5', 'string', $this);
				case 'NarroSuggestionCommentAsSuggestion':
					return new QQReverseReferenceNodeNarroSuggestionComment($this, 'narrosuggestioncommentassuggestion', 'reverse_reference', 'suggestion_id');
				case 'NarroSuggestionVoteAsSuggestion':
					return new QQReverseReferenceNodeNarroSuggestionVote($this, 'narrosuggestionvoteassuggestion', 'reverse_reference', 'suggestion_id');
				case 'NarroTextContextAsValidSuggestion':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextasvalidsuggestion', 'reverse_reference', 'valid_suggestion_id');
				case 'NarroTextContextAsPopularSuggestion':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextaspopularsuggestion', 'reverse_reference', 'popular_suggestion_id');
				case 'NarroTextContextPluralAsValidSuggestion':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralasvalidsuggestion', 'reverse_reference', 'valid_suggestion_id');
				case 'NarroTextContextPluralAsPopularSuggestion':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralaspopularsuggestion', 'reverse_reference', 'popular_suggestion_id');

				case '_PrimaryKeyNode':
					return new QQNode('suggestion_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroTextSuggestion extends QQReverseReferenceNode {
		protected $strTableName = 'narro_text_suggestion';
		protected $strPrimaryKey = 'suggestion_id';
		protected $strClassName = 'NarroTextSuggestion';
		public function __get($strName) {
			switch ($strName) {
				case 'SuggestionId':
					return new QQNode('suggestion_id', 'integer', $this);
				case 'UserId':
					return new QQNode('user_id', 'integer', $this);
				case 'User':
					return new QQNodeNarroUser('user_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'Language':
					return new QQNodeNarroLanguage('language_id', 'integer', $this);
				case 'SuggestionValue':
					return new QQNode('suggestion_value', 'string', $this);
				case 'SuggestionValueMd5':
					return new QQNode('suggestion_value_md5', 'string', $this);
				case 'NarroSuggestionCommentAsSuggestion':
					return new QQReverseReferenceNodeNarroSuggestionComment($this, 'narrosuggestioncommentassuggestion', 'reverse_reference', 'suggestion_id');
				case 'NarroSuggestionVoteAsSuggestion':
					return new QQReverseReferenceNodeNarroSuggestionVote($this, 'narrosuggestionvoteassuggestion', 'reverse_reference', 'suggestion_id');
				case 'NarroTextContextAsValidSuggestion':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextasvalidsuggestion', 'reverse_reference', 'valid_suggestion_id');
				case 'NarroTextContextAsPopularSuggestion':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextaspopularsuggestion', 'reverse_reference', 'popular_suggestion_id');
				case 'NarroTextContextPluralAsValidSuggestion':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralasvalidsuggestion', 'reverse_reference', 'valid_suggestion_id');
				case 'NarroTextContextPluralAsPopularSuggestion':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralaspopularsuggestion', 'reverse_reference', 'popular_suggestion_id');

				case '_PrimaryKeyNode':
					return new QQNode('suggestion_id', 'integer', $this);
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