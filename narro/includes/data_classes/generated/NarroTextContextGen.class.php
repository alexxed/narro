<?php
	/**
	 * The abstract NarroTextContextGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroTextContext subclass which
	 * extends this NarroTextContextGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroTextContext class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroTextContextGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroTextContext from PK Info
		 * @param integer $intContextId
		 * @return NarroTextContext
		 */
		public static function Load($intContextId) {
			// Use QuerySingle to Perform the Query
			return NarroTextContext::QuerySingle(
				QQ::Equal(QQN::NarroTextContext()->ContextId, $intContextId)
			);
		}

		/**
		 * Load all NarroTextContexts
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadAll query
			try {
				return NarroTextContext::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroTextContexts
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroTextContext::QueryCount to perform the CountAll query
			return NarroTextContext::QueryCount(QQ::All());
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
			$objDatabase = NarroTextContext::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroTextContext-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_text_context');
			NarroTextContext::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_text_context` AS `narro_text_context`');

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
		 * Static Qcodo Query method to query for a single NarroTextContext object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextContext the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContext::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroTextContext object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextContext::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroTextContext objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextContext[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContext::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextContext::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroTextContext objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContext::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroTextContext::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_text_context_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroTextContext-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroTextContext::GetSelectFields($objQueryBuilder);
				NarroTextContext::GetFromFields($objQueryBuilder);

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
			return NarroTextContext::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroTextContext
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_text_context`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`context_id` AS ' . $strAliasPrefix . 'context_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`project_id` AS ' . $strAliasPrefix . 'project_id`');
			$objBuilder->AddSelectItem($strTableName . '.`context` AS ' . $strAliasPrefix . 'context`');
			$objBuilder->AddSelectItem($strTableName . '.`file_id` AS ' . $strAliasPrefix . 'file_id`');
			$objBuilder->AddSelectItem($strTableName . '.`valid_suggestion_id` AS ' . $strAliasPrefix . 'valid_suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`popular_suggestion_id` AS ' . $strAliasPrefix . 'popular_suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`is_fuzzy` AS ' . $strAliasPrefix . 'is_fuzzy`');
			$objBuilder->AddSelectItem($strTableName . '.`has_suggestion` AS ' . $strAliasPrefix . 'has_suggestion`');
			$objBuilder->AddSelectItem($strTableName . '.`has_plural` AS ' . $strAliasPrefix . 'has_plural`');
			$objBuilder->AddSelectItem($strTableName . '.`active` AS ' . $strAliasPrefix . 'active`');
			$objBuilder->AddSelectItem($strTableName . '.`translatable` AS ' . $strAliasPrefix . 'translatable`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroTextContext from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroTextContext::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroTextContext
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;

			// See if we're doing an array expansion on the previous item
			if (($strExpandAsArrayNodes) && ($objPreviousItem) &&
				($objPreviousItem->intContextId == $objDbRow->GetColumn($strAliasPrefix . 'context_id', 'Integer'))) {

				// We are.  Now, prepare to check for ExpandAsArray clauses
				$blnExpandedViaArray = false;
				if (!$strAliasPrefix)
					$strAliasPrefix = 'narro_text_context__';


				if ((array_key_exists($strAliasPrefix . 'narrotextcontextcommentascontext__comment_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextcommentascontext__comment_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextCommentAsContextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextCommentAsContextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContextComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextcommentascontext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextCommentAsContextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextCommentAsContextArray, NarroTextContextComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextcommentascontext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextpluralascontext__plural_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralascontext__plural_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextPluralAsContextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextPluralAsContextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralascontext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextPluralAsContextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextPluralAsContextArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralascontext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				// Either return false to signal array expansion, or check-to-reset the Alias prefix and move on
				if ($blnExpandedViaArray)
					return false;
				else if ($strAliasPrefix == 'narro_text_context__')
					$strAliasPrefix = null;
			}

			// Create a new instance of the NarroTextContext object
			$objToReturn = new NarroTextContext();
			$objToReturn->__blnRestored = true;

			$objToReturn->intContextId = $objDbRow->GetColumn($strAliasPrefix . 'context_id', 'Integer');
			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->intProjectId = $objDbRow->GetColumn($strAliasPrefix . 'project_id', 'Integer');
			$objToReturn->strContext = $objDbRow->GetColumn($strAliasPrefix . 'context', 'Blob');
			$objToReturn->intFileId = $objDbRow->GetColumn($strAliasPrefix . 'file_id', 'Integer');
			$objToReturn->intValidSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id', 'Integer');
			$objToReturn->intPopularSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id', 'Integer');
			$objToReturn->intIsFuzzy = $objDbRow->GetColumn($strAliasPrefix . 'is_fuzzy', 'Integer');
			$objToReturn->intHasSuggestion = $objDbRow->GetColumn($strAliasPrefix . 'has_suggestion', 'Integer');
			$objToReturn->blnHasPlural = $objDbRow->GetColumn($strAliasPrefix . 'has_plural', 'Bit');
			$objToReturn->intActive = $objDbRow->GetColumn($strAliasPrefix . 'active', 'Integer');
			$objToReturn->intTranslatable = $objDbRow->GetColumn($strAliasPrefix . 'translatable', 'Integer');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_text_context__';

			// Check for Text Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'text_id__text_id')))
				$objToReturn->objText = NarroText::InstantiateDbRow($objDbRow, $strAliasPrefix . 'text_id__', $strExpandAsArrayNodes);

			// Check for Project Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'project_id__project_id')))
				$objToReturn->objProject = NarroProject::InstantiateDbRow($objDbRow, $strAliasPrefix . 'project_id__', $strExpandAsArrayNodes);

			// Check for File Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'file_id__file_id')))
				$objToReturn->objFile = NarroFile::InstantiateDbRow($objDbRow, $strAliasPrefix . 'file_id__', $strExpandAsArrayNodes);

			// Check for ValidSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id__suggestion_id')))
				$objToReturn->objValidSuggestion = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'valid_suggestion_id__', $strExpandAsArrayNodes);

			// Check for PopularSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id__suggestion_id')))
				$objToReturn->objPopularSuggestion = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'popular_suggestion_id__', $strExpandAsArrayNodes);




			// Check for NarroTextContextCommentAsContext Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextcommentascontext__comment_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextcommentascontext__comment_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextCommentAsContextArray, NarroTextContextComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextcommentascontext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextCommentAsContext = NarroTextContextComment::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextcommentascontext__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextPluralAsContext Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralascontext__plural_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextpluralascontext__plural_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextPluralAsContextArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralascontext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextPluralAsContext = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralascontext__', $strExpandAsArrayNodes);
			}

			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroTextContexts from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroTextContext[]
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
					$objItem = NarroTextContext::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroTextContext::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroTextContext object,
		 * by ContextId Index(es)
		 * @param integer $intContextId
		 * @return NarroTextContext
		*/
		public static function LoadByContextId($intContextId) {
			return NarroTextContext::QuerySingle(
				QQ::Equal(QQN::NarroTextContext()->ContextId, $intContextId)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByTextId($intTextId, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByTextId query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->TextId, $intTextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return int
		*/
		public static function CountByTextId($intTextId) {
			// Call NarroTextContext::QueryCount to perform the CountByTextId query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->TextId, $intTextId)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by FileId Index(es)
		 * @param integer $intFileId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByFileId($intFileId, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByFileId query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->FileId, $intFileId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by FileId Index(es)
		 * @param integer $intFileId
		 * @return int
		*/
		public static function CountByFileId($intFileId) {
			// Call NarroTextContext::QueryCount to perform the CountByFileId query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->FileId, $intFileId)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by HasPlural Index(es)
		 * @param boolean $blnHasPlural
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByHasPlural($blnHasPlural, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByHasPlural query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->HasPlural, $blnHasPlural),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by HasPlural Index(es)
		 * @param boolean $blnHasPlural
		 * @return int
		*/
		public static function CountByHasPlural($blnHasPlural) {
			// Call NarroTextContext::QueryCount to perform the CountByHasPlural query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->HasPlural, $blnHasPlural)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByValidSuggestionId($intValidSuggestionId, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByValidSuggestionId query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->ValidSuggestionId, $intValidSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @return int
		*/
		public static function CountByValidSuggestionId($intValidSuggestionId) {
			// Call NarroTextContext::QueryCount to perform the CountByValidSuggestionId query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->ValidSuggestionId, $intValidSuggestionId)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by HasSuggestion Index(es)
		 * @param integer $intHasSuggestion
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByHasSuggestion($intHasSuggestion, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByHasSuggestion query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->HasSuggestion, $intHasSuggestion),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by HasSuggestion Index(es)
		 * @param integer $intHasSuggestion
		 * @return int
		*/
		public static function CountByHasSuggestion($intHasSuggestion) {
			// Call NarroTextContext::QueryCount to perform the CountByHasSuggestion query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->HasSuggestion, $intHasSuggestion)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by ProjectId Index(es)
		 * @param integer $intProjectId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByProjectId($intProjectId, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByProjectId query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->ProjectId, $intProjectId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by ProjectId Index(es)
		 * @param integer $intProjectId
		 * @return int
		*/
		public static function CountByProjectId($intProjectId) {
			// Call NarroTextContext::QueryCount to perform the CountByProjectId query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->ProjectId, $intProjectId)
			);
		}
			
		/**
		 * Load an array of NarroTextContext objects,
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/
		public static function LoadArrayByPopularSuggestionId($intPopularSuggestionId, $objOptionalClauses = null) {
			// Call NarroTextContext::QueryArray to perform the LoadArrayByPopularSuggestionId query
			try {
				return NarroTextContext::QueryArray(
					QQ::Equal(QQN::NarroTextContext()->PopularSuggestionId, $intPopularSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContexts
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @return int
		*/
		public static function CountByPopularSuggestionId($intPopularSuggestionId) {
			// Call NarroTextContext::QueryCount to perform the CountByPopularSuggestionId query
			return NarroTextContext::QueryCount(
				QQ::Equal(QQN::NarroTextContext()->PopularSuggestionId, $intPopularSuggestionId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroTextContext
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_text_context` (
							`text_id`,
							`project_id`,
							`context`,
							`file_id`,
							`valid_suggestion_id`,
							`popular_suggestion_id`,
							`is_fuzzy`,
							`has_suggestion`,
							`has_plural`,
							`active`,
							`translatable`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intTextId) . ',
							' . $objDatabase->SqlVariable($this->intProjectId) . ',
							' . $objDatabase->SqlVariable($this->strContext) . ',
							' . $objDatabase->SqlVariable($this->intFileId) . ',
							' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->intIsFuzzy) . ',
							' . $objDatabase->SqlVariable($this->intHasSuggestion) . ',
							' . $objDatabase->SqlVariable($this->blnHasPlural) . ',
							' . $objDatabase->SqlVariable($this->intActive) . ',
							' . $objDatabase->SqlVariable($this->intTranslatable) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intContextId = $objDatabase->InsertId('narro_text_context', 'context_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_text_context`
						SET
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ',
							`project_id` = ' . $objDatabase->SqlVariable($this->intProjectId) . ',
							`context` = ' . $objDatabase->SqlVariable($this->strContext) . ',
							`file_id` = ' . $objDatabase->SqlVariable($this->intFileId) . ',
							`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . ',
							`is_fuzzy` = ' . $objDatabase->SqlVariable($this->intIsFuzzy) . ',
							`has_suggestion` = ' . $objDatabase->SqlVariable($this->intHasSuggestion) . ',
							`has_plural` = ' . $objDatabase->SqlVariable($this->blnHasPlural) . ',
							`active` = ' . $objDatabase->SqlVariable($this->intActive) . ',
							`translatable` = ' . $objDatabase->SqlVariable($this->intTranslatable) . '
						WHERE
							`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
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
		 * Delete this NarroTextContext
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroTextContext with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '');
		}

		/**
		 * Delete all NarroTextContexts
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`');
		}

		/**
		 * Truncate narro_text_context table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_text_context`');
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
				case 'ContextId':
					/**
					 * Gets the value for intContextId (Read-Only PK)
					 * @return integer
					 */
					return $this->intContextId;

				case 'TextId':
					/**
					 * Gets the value for intTextId (Not Null)
					 * @return integer
					 */
					return $this->intTextId;

				case 'ProjectId':
					/**
					 * Gets the value for intProjectId (Not Null)
					 * @return integer
					 */
					return $this->intProjectId;

				case 'Context':
					/**
					 * Gets the value for strContext (Not Null)
					 * @return string
					 */
					return $this->strContext;

				case 'FileId':
					/**
					 * Gets the value for intFileId (Not Null)
					 * @return integer
					 */
					return $this->intFileId;

				case 'ValidSuggestionId':
					/**
					 * Gets the value for intValidSuggestionId 
					 * @return integer
					 */
					return $this->intValidSuggestionId;

				case 'PopularSuggestionId':
					/**
					 * Gets the value for intPopularSuggestionId 
					 * @return integer
					 */
					return $this->intPopularSuggestionId;

				case 'IsFuzzy':
					/**
					 * Gets the value for intIsFuzzy (Not Null)
					 * @return integer
					 */
					return $this->intIsFuzzy;

				case 'HasSuggestion':
					/**
					 * Gets the value for intHasSuggestion (Not Null)
					 * @return integer
					 */
					return $this->intHasSuggestion;

				case 'HasPlural':
					/**
					 * Gets the value for blnHasPlural (Not Null)
					 * @return boolean
					 */
					return $this->blnHasPlural;

				case 'Active':
					/**
					 * Gets the value for intActive (Not Null)
					 * @return integer
					 */
					return $this->intActive;

				case 'Translatable':
					/**
					 * Gets the value for intTranslatable (Not Null)
					 * @return integer
					 */
					return $this->intTranslatable;


				///////////////////
				// Member Objects
				///////////////////
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

				case 'Project':
					/**
					 * Gets the value for the NarroProject object referenced by intProjectId (Not Null)
					 * @return NarroProject
					 */
					try {
						if ((!$this->objProject) && (!is_null($this->intProjectId)))
							$this->objProject = NarroProject::Load($this->intProjectId);
						return $this->objProject;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'File':
					/**
					 * Gets the value for the NarroFile object referenced by intFileId (Not Null)
					 * @return NarroFile
					 */
					try {
						if ((!$this->objFile) && (!is_null($this->intFileId)))
							$this->objFile = NarroFile::Load($this->intFileId);
						return $this->objFile;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'ValidSuggestion':
					/**
					 * Gets the value for the NarroTextSuggestion object referenced by intValidSuggestionId 
					 * @return NarroTextSuggestion
					 */
					try {
						if ((!$this->objValidSuggestion) && (!is_null($this->intValidSuggestionId)))
							$this->objValidSuggestion = NarroTextSuggestion::Load($this->intValidSuggestionId);
						return $this->objValidSuggestion;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'PopularSuggestion':
					/**
					 * Gets the value for the NarroTextSuggestion object referenced by intPopularSuggestionId 
					 * @return NarroTextSuggestion
					 */
					try {
						if ((!$this->objPopularSuggestion) && (!is_null($this->intPopularSuggestionId)))
							$this->objPopularSuggestion = NarroTextSuggestion::Load($this->intPopularSuggestionId);
						return $this->objPopularSuggestion;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

				case '_NarroTextContextCommentAsContext':
					/**
					 * Gets the value for the private _objNarroTextContextCommentAsContext (Read-Only)
					 * if set due to an expansion on the narro_text_context_comment.context_id reverse relationship
					 * @return NarroTextContextComment
					 */
					return $this->_objNarroTextContextCommentAsContext;

				case '_NarroTextContextCommentAsContextArray':
					/**
					 * Gets the value for the private _objNarroTextContextCommentAsContextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context_comment.context_id reverse relationship
					 * @return NarroTextContextComment[]
					 */
					return (array) $this->_objNarroTextContextCommentAsContextArray;

				case '_NarroTextContextPluralAsContext':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsContext (Read-Only)
					 * if set due to an expansion on the narro_text_context_plural.context_id reverse relationship
					 * @return NarroTextContextPlural
					 */
					return $this->_objNarroTextContextPluralAsContext;

				case '_NarroTextContextPluralAsContextArray':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsContextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context_plural.context_id reverse relationship
					 * @return NarroTextContextPlural[]
					 */
					return (array) $this->_objNarroTextContextPluralAsContextArray;

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

				case 'ProjectId':
					/**
					 * Sets the value for intProjectId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objProject = null;
						return ($this->intProjectId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Context':
					/**
					 * Sets the value for strContext (Not Null)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strContext = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'FileId':
					/**
					 * Sets the value for intFileId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objFile = null;
						return ($this->intFileId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'ValidSuggestionId':
					/**
					 * Sets the value for intValidSuggestionId 
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objValidSuggestion = null;
						return ($this->intValidSuggestionId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'PopularSuggestionId':
					/**
					 * Sets the value for intPopularSuggestionId 
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objPopularSuggestion = null;
						return ($this->intPopularSuggestionId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'IsFuzzy':
					/**
					 * Sets the value for intIsFuzzy (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intIsFuzzy = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'HasSuggestion':
					/**
					 * Sets the value for intHasSuggestion (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intHasSuggestion = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'HasPlural':
					/**
					 * Sets the value for blnHasPlural (Not Null)
					 * @param boolean $mixValue
					 * @return boolean
					 */
					try {
						return ($this->blnHasPlural = QType::Cast($mixValue, QType::Boolean));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Active':
					/**
					 * Sets the value for intActive (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intActive = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Translatable':
					/**
					 * Sets the value for intTranslatable (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intTranslatable = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
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
							throw new QCallerException('Unable to set an unsaved Text for this NarroTextContext');

						// Update Local Member Variables
						$this->objText = $mixValue;
						$this->intTextId = $mixValue->TextId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'Project':
					/**
					 * Sets the value for the NarroProject object referenced by intProjectId (Not Null)
					 * @param NarroProject $mixValue
					 * @return NarroProject
					 */
					if (is_null($mixValue)) {
						$this->intProjectId = null;
						$this->objProject = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroProject object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroProject');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroProject object
						if (is_null($mixValue->ProjectId))
							throw new QCallerException('Unable to set an unsaved Project for this NarroTextContext');

						// Update Local Member Variables
						$this->objProject = $mixValue;
						$this->intProjectId = $mixValue->ProjectId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'File':
					/**
					 * Sets the value for the NarroFile object referenced by intFileId (Not Null)
					 * @param NarroFile $mixValue
					 * @return NarroFile
					 */
					if (is_null($mixValue)) {
						$this->intFileId = null;
						$this->objFile = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroFile object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroFile');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroFile object
						if (is_null($mixValue->FileId))
							throw new QCallerException('Unable to set an unsaved File for this NarroTextContext');

						// Update Local Member Variables
						$this->objFile = $mixValue;
						$this->intFileId = $mixValue->FileId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'ValidSuggestion':
					/**
					 * Sets the value for the NarroTextSuggestion object referenced by intValidSuggestionId 
					 * @param NarroTextSuggestion $mixValue
					 * @return NarroTextSuggestion
					 */
					if (is_null($mixValue)) {
						$this->intValidSuggestionId = null;
						$this->objValidSuggestion = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroTextSuggestion object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroTextSuggestion');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroTextSuggestion object
						if (is_null($mixValue->SuggestionId))
							throw new QCallerException('Unable to set an unsaved ValidSuggestion for this NarroTextContext');

						// Update Local Member Variables
						$this->objValidSuggestion = $mixValue;
						$this->intValidSuggestionId = $mixValue->SuggestionId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'PopularSuggestion':
					/**
					 * Sets the value for the NarroTextSuggestion object referenced by intPopularSuggestionId 
					 * @param NarroTextSuggestion $mixValue
					 * @return NarroTextSuggestion
					 */
					if (is_null($mixValue)) {
						$this->intPopularSuggestionId = null;
						$this->objPopularSuggestion = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroTextSuggestion object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroTextSuggestion');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroTextSuggestion object
						if (is_null($mixValue->SuggestionId))
							throw new QCallerException('Unable to set an unsaved PopularSuggestion for this NarroTextContext');

						// Update Local Member Variables
						$this->objPopularSuggestion = $mixValue;
						$this->intPopularSuggestionId = $mixValue->SuggestionId;

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

			
		
		// Related Objects' Methods for NarroTextContextCommentAsContext
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextCommentsAsContext as an array of NarroTextContextComment objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextComment[]
		*/ 
		public function GetNarroTextContextCommentAsContextArray($objOptionalClauses = null) {
			if ((is_null($this->intContextId)))
				return array();

			try {
				return NarroTextContextComment::LoadArrayByContextId($this->intContextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextCommentsAsContext
		 * @return int
		*/ 
		public function CountNarroTextContextCommentsAsContext() {
			if ((is_null($this->intContextId)))
				return 0;

			return NarroTextContextComment::CountByContextId($this->intContextId);
		}

		/**
		 * Associates a NarroTextContextCommentAsContext
		 * @param NarroTextContextComment $objNarroTextContextComment
		 * @return void
		*/ 
		public function AssociateNarroTextContextCommentAsContext(NarroTextContextComment $objNarroTextContextComment) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextCommentAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextCommentAsContext on this NarroTextContext with an unsaved NarroTextContextComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_comment`
				SET
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroTextContextComment->CommentId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextCommentAsContext
		 * @param NarroTextContextComment $objNarroTextContextComment
		 * @return void
		*/ 
		public function UnassociateNarroTextContextCommentAsContext(NarroTextContextComment $objNarroTextContextComment) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this NarroTextContext with an unsaved NarroTextContextComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_comment`
				SET
					`context_id` = null
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroTextContextComment->CommentId) . ' AND
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextCommentsAsContext
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextCommentsAsContext() {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_comment`
				SET
					`context_id` = null
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextCommentAsContext
		 * @param NarroTextContextComment $objNarroTextContextComment
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextCommentAsContext(NarroTextContextComment $objNarroTextContextComment) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextComment->CommentId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this NarroTextContext with an unsaved NarroTextContextComment.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_comment`
				WHERE
					`comment_id` = ' . $objDatabase->SqlVariable($objNarroTextContextComment->CommentId) . ' AND
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextCommentsAsContext
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextCommentsAsContext() {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextCommentAsContext on this unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_comment`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextPluralAsContext
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextPluralsAsContext as an array of NarroTextContextPlural objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/ 
		public function GetNarroTextContextPluralAsContextArray($objOptionalClauses = null) {
			if ((is_null($this->intContextId)))
				return array();

			try {
				return NarroTextContextPlural::LoadArrayByContextId($this->intContextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextPluralsAsContext
		 * @return int
		*/ 
		public function CountNarroTextContextPluralsAsContext() {
			if ((is_null($this->intContextId)))
				return 0;

			return NarroTextContextPlural::CountByContextId($this->intContextId);
		}

		/**
		 * Associates a NarroTextContextPluralAsContext
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function AssociateNarroTextContextPluralAsContext(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsContext on this NarroTextContext with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextPluralAsContext
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function UnassociateNarroTextContextPluralAsContext(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this NarroTextContext with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`context_id` = null
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextPluralsAsContext
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextPluralsAsContext() {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`context_id` = null
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextPluralAsContext
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextPluralAsContext(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this unsaved NarroTextContext.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this NarroTextContext with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextPluralsAsContext
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextPluralsAsContext() {
			if ((is_null($this->intContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsContext on this unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContext::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . '
			');
		}




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_text_context.context_id
		 * @var integer intContextId
		 */
		protected $intContextId;
		const ContextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.project_id
		 * @var integer intProjectId
		 */
		protected $intProjectId;
		const ProjectIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.context
		 * @var string strContext
		 */
		protected $strContext;
		const ContextDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.file_id
		 * @var integer intFileId
		 */
		protected $intFileId;
		const FileIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.valid_suggestion_id
		 * @var integer intValidSuggestionId
		 */
		protected $intValidSuggestionId;
		const ValidSuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.popular_suggestion_id
		 * @var integer intPopularSuggestionId
		 */
		protected $intPopularSuggestionId;
		const PopularSuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.is_fuzzy
		 * @var integer intIsFuzzy
		 */
		protected $intIsFuzzy;
		const IsFuzzyDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.has_suggestion
		 * @var integer intHasSuggestion
		 */
		protected $intHasSuggestion;
		const HasSuggestionDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.has_plural
		 * @var boolean blnHasPlural
		 */
		protected $blnHasPlural;
		const HasPluralDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.active
		 * @var integer intActive
		 */
		protected $intActive;
		const ActiveDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context.translatable
		 * @var integer intTranslatable
		 */
		protected $intTranslatable;
		const TranslatableDefault = null;


		/**
		 * Private member variable that stores a reference to a single NarroTextContextCommentAsContext object
		 * (of type NarroTextContextComment), if this NarroTextContext object was restored with
		 * an expansion on the narro_text_context_comment association table.
		 * @var NarroTextContextComment _objNarroTextContextCommentAsContext;
		 */
		private $_objNarroTextContextCommentAsContext;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextCommentAsContext objects
		 * (of type NarroTextContextComment[]), if this NarroTextContext object was restored with
		 * an ExpandAsArray on the narro_text_context_comment association table.
		 * @var NarroTextContextComment[] _objNarroTextContextCommentAsContextArray;
		 */
		private $_objNarroTextContextCommentAsContextArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextPluralAsContext object
		 * (of type NarroTextContextPlural), if this NarroTextContext object was restored with
		 * an expansion on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural _objNarroTextContextPluralAsContext;
		 */
		private $_objNarroTextContextPluralAsContext;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextPluralAsContext objects
		 * (of type NarroTextContextPlural[]), if this NarroTextContext object was restored with
		 * an ExpandAsArray on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural[] _objNarroTextContextPluralAsContextArray;
		 */
		private $_objNarroTextContextPluralAsContextArray = array();

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
		 * in the database column narro_text_context.text_id.
		 *
		 * NOTE: Always use the Text property getter to correctly retrieve this NarroText object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroText objText
		 */
		protected $objText;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context.project_id.
		 *
		 * NOTE: Always use the Project property getter to correctly retrieve this NarroProject object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroProject objProject
		 */
		protected $objProject;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context.file_id.
		 *
		 * NOTE: Always use the File property getter to correctly retrieve this NarroFile object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroFile objFile
		 */
		protected $objFile;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context.valid_suggestion_id.
		 *
		 * NOTE: Always use the ValidSuggestion property getter to correctly retrieve this NarroTextSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroTextSuggestion objValidSuggestion
		 */
		protected $objValidSuggestion;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context.popular_suggestion_id.
		 *
		 * NOTE: Always use the PopularSuggestion property getter to correctly retrieve this NarroTextSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroTextSuggestion objPopularSuggestion
		 */
		protected $objPopularSuggestion;






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroTextContext"><sequence>';
			$strToReturn .= '<element name="ContextId" type="xsd:int"/>';
			$strToReturn .= '<element name="Text" type="xsd1:NarroText"/>';
			$strToReturn .= '<element name="Project" type="xsd1:NarroProject"/>';
			$strToReturn .= '<element name="Context" type="xsd:string"/>';
			$strToReturn .= '<element name="File" type="xsd1:NarroFile"/>';
			$strToReturn .= '<element name="ValidSuggestion" type="xsd1:NarroTextSuggestion"/>';
			$strToReturn .= '<element name="PopularSuggestion" type="xsd1:NarroTextSuggestion"/>';
			$strToReturn .= '<element name="IsFuzzy" type="xsd:int"/>';
			$strToReturn .= '<element name="HasSuggestion" type="xsd:int"/>';
			$strToReturn .= '<element name="HasPlural" type="xsd:boolean"/>';
			$strToReturn .= '<element name="Active" type="xsd:int"/>';
			$strToReturn .= '<element name="Translatable" type="xsd:int"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroTextContext', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroTextContext'] = NarroTextContext::GetSoapComplexTypeXml();
				NarroText::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroProject::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroFile::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroTextSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroTextSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroTextContext::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroTextContext();
			if (property_exists($objSoapObject, 'ContextId'))
				$objToReturn->intContextId = $objSoapObject->ContextId;
			if ((property_exists($objSoapObject, 'Text')) &&
				($objSoapObject->Text))
				$objToReturn->Text = NarroText::GetObjectFromSoapObject($objSoapObject->Text);
			if ((property_exists($objSoapObject, 'Project')) &&
				($objSoapObject->Project))
				$objToReturn->Project = NarroProject::GetObjectFromSoapObject($objSoapObject->Project);
			if (property_exists($objSoapObject, 'Context'))
				$objToReturn->strContext = $objSoapObject->Context;
			if ((property_exists($objSoapObject, 'File')) &&
				($objSoapObject->File))
				$objToReturn->File = NarroFile::GetObjectFromSoapObject($objSoapObject->File);
			if ((property_exists($objSoapObject, 'ValidSuggestion')) &&
				($objSoapObject->ValidSuggestion))
				$objToReturn->ValidSuggestion = NarroTextSuggestion::GetObjectFromSoapObject($objSoapObject->ValidSuggestion);
			if ((property_exists($objSoapObject, 'PopularSuggestion')) &&
				($objSoapObject->PopularSuggestion))
				$objToReturn->PopularSuggestion = NarroTextSuggestion::GetObjectFromSoapObject($objSoapObject->PopularSuggestion);
			if (property_exists($objSoapObject, 'IsFuzzy'))
				$objToReturn->intIsFuzzy = $objSoapObject->IsFuzzy;
			if (property_exists($objSoapObject, 'HasSuggestion'))
				$objToReturn->intHasSuggestion = $objSoapObject->HasSuggestion;
			if (property_exists($objSoapObject, 'HasPlural'))
				$objToReturn->blnHasPlural = $objSoapObject->HasPlural;
			if (property_exists($objSoapObject, 'Active'))
				$objToReturn->intActive = $objSoapObject->Active;
			if (property_exists($objSoapObject, 'Translatable'))
				$objToReturn->intTranslatable = $objSoapObject->Translatable;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroTextContext::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objText)
				$objObject->objText = NarroText::GetSoapObjectFromObject($objObject->objText, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intTextId = null;
			if ($objObject->objProject)
				$objObject->objProject = NarroProject::GetSoapObjectFromObject($objObject->objProject, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intProjectId = null;
			if ($objObject->objFile)
				$objObject->objFile = NarroFile::GetSoapObjectFromObject($objObject->objFile, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intFileId = null;
			if ($objObject->objValidSuggestion)
				$objObject->objValidSuggestion = NarroTextSuggestion::GetSoapObjectFromObject($objObject->objValidSuggestion, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intValidSuggestionId = null;
			if ($objObject->objPopularSuggestion)
				$objObject->objPopularSuggestion = NarroTextSuggestion::GetSoapObjectFromObject($objObject->objPopularSuggestion, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intPopularSuggestionId = null;
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroTextContext extends QQNode {
		protected $strTableName = 'narro_text_context';
		protected $strPrimaryKey = 'context_id';
		protected $strClassName = 'NarroTextContext';
		public function __get($strName) {
			switch ($strName) {
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'ProjectId':
					return new QQNode('project_id', 'integer', $this);
				case 'Project':
					return new QQNodeNarroProject('project_id', 'integer', $this);
				case 'Context':
					return new QQNode('context', 'string', $this);
				case 'FileId':
					return new QQNode('file_id', 'integer', $this);
				case 'File':
					return new QQNodeNarroFile('file_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroTextSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroTextSuggestion('popular_suggestion_id', 'integer', $this);
				case 'IsFuzzy':
					return new QQNode('is_fuzzy', 'integer', $this);
				case 'HasSuggestion':
					return new QQNode('has_suggestion', 'integer', $this);
				case 'HasPlural':
					return new QQNode('has_plural', 'boolean', $this);
				case 'Active':
					return new QQNode('active', 'integer', $this);
				case 'Translatable':
					return new QQNode('translatable', 'integer', $this);
				case 'NarroTextContextCommentAsContext':
					return new QQReverseReferenceNodeNarroTextContextComment($this, 'narrotextcontextcommentascontext', 'reverse_reference', 'context_id');
				case 'NarroTextContextPluralAsContext':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralascontext', 'reverse_reference', 'context_id');

				case '_PrimaryKeyNode':
					return new QQNode('context_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroTextContext extends QQReverseReferenceNode {
		protected $strTableName = 'narro_text_context';
		protected $strPrimaryKey = 'context_id';
		protected $strClassName = 'NarroTextContext';
		public function __get($strName) {
			switch ($strName) {
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'ProjectId':
					return new QQNode('project_id', 'integer', $this);
				case 'Project':
					return new QQNodeNarroProject('project_id', 'integer', $this);
				case 'Context':
					return new QQNode('context', 'string', $this);
				case 'FileId':
					return new QQNode('file_id', 'integer', $this);
				case 'File':
					return new QQNodeNarroFile('file_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroTextSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroTextSuggestion('popular_suggestion_id', 'integer', $this);
				case 'IsFuzzy':
					return new QQNode('is_fuzzy', 'integer', $this);
				case 'HasSuggestion':
					return new QQNode('has_suggestion', 'integer', $this);
				case 'HasPlural':
					return new QQNode('has_plural', 'boolean', $this);
				case 'Active':
					return new QQNode('active', 'integer', $this);
				case 'Translatable':
					return new QQNode('translatable', 'integer', $this);
				case 'NarroTextContextCommentAsContext':
					return new QQReverseReferenceNodeNarroTextContextComment($this, 'narrotextcontextcommentascontext', 'reverse_reference', 'context_id');
				case 'NarroTextContextPluralAsContext':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralascontext', 'reverse_reference', 'context_id');

				case '_PrimaryKeyNode':
					return new QQNode('context_id', 'integer', $this);
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