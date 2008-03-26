<?php
	/**
	 * The abstract NarroContextPluralInfoGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroContextPluralInfo subclass which
	 * extends this NarroContextPluralInfoGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroContextPluralInfo class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroContextPluralInfoGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroContextPluralInfo from PK Info
		 * @param integer $intPluralInfoId
		 * @return NarroContextPluralInfo
		 */
		public static function Load($intPluralInfoId) {
			// Use QuerySingle to Perform the Query
			return NarroContextPluralInfo::QuerySingle(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralInfoId, $intPluralInfoId)
			);
		}

		/**
		 * Load all NarroContextPluralInfos
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroContextPluralInfo::QueryArray to perform the LoadAll query
			try {
				return NarroContextPluralInfo::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroContextPluralInfos
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroContextPluralInfo::QueryCount to perform the CountAll query
			return NarroContextPluralInfo::QueryCount(QQ::All());
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
			$objDatabase = NarroContextPluralInfo::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroContextPluralInfo-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_context_plural_info');
			NarroContextPluralInfo::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_context_plural_info` AS `narro_context_plural_info`');

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
		 * Static Qcodo Query method to query for a single NarroContextPluralInfo object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroContextPluralInfo the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPluralInfo::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroContextPluralInfo object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroContextPluralInfo::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroContextPluralInfo objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroContextPluralInfo[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPluralInfo::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroContextPluralInfo::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroContextPluralInfo objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPluralInfo::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroContextPluralInfo::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_context_plural_info_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroContextPluralInfo-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroContextPluralInfo::GetSelectFields($objQueryBuilder);
				NarroContextPluralInfo::GetFromFields($objQueryBuilder);

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
			return NarroContextPluralInfo::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroContextPluralInfo
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_context_plural_info`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`plural_info_id` AS ' . $strAliasPrefix . 'plural_info_id`');
			$objBuilder->AddSelectItem($strTableName . '.`plural_id` AS ' . $strAliasPrefix . 'plural_id`');
			$objBuilder->AddSelectItem($strTableName . '.`language_id` AS ' . $strAliasPrefix . 'language_id`');
			$objBuilder->AddSelectItem($strTableName . '.`valid_suggestion_id` AS ' . $strAliasPrefix . 'valid_suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`popular_suggestion_id` AS ' . $strAliasPrefix . 'popular_suggestion_id`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroContextPluralInfo from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroContextPluralInfo::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroContextPluralInfo
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;


			// Create a new instance of the NarroContextPluralInfo object
			$objToReturn = new NarroContextPluralInfo();
			$objToReturn->__blnRestored = true;

			$objToReturn->intPluralInfoId = $objDbRow->GetColumn($strAliasPrefix . 'plural_info_id', 'Integer');
			$objToReturn->intPluralId = $objDbRow->GetColumn($strAliasPrefix . 'plural_id', 'Integer');
			$objToReturn->intLanguageId = $objDbRow->GetColumn($strAliasPrefix . 'language_id', 'Integer');
			$objToReturn->intValidSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id', 'Integer');
			$objToReturn->intPopularSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id', 'Integer');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_context_plural_info__';

			// Check for Plural Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'plural_id__plural_id')))
				$objToReturn->objPlural = NarroContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'plural_id__', $strExpandAsArrayNodes);

			// Check for Language Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'language_id__language_id')))
				$objToReturn->objLanguage = NarroLanguage::InstantiateDbRow($objDbRow, $strAliasPrefix . 'language_id__', $strExpandAsArrayNodes);

			// Check for ValidSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id__suggestion_id')))
				$objToReturn->objValidSuggestion = NarroSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'valid_suggestion_id__', $strExpandAsArrayNodes);

			// Check for PopularSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id__suggestion_id')))
				$objToReturn->objPopularSuggestion = NarroSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'popular_suggestion_id__', $strExpandAsArrayNodes);




			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroContextPluralInfos from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroContextPluralInfo[]
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
					$objItem = NarroContextPluralInfo::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroContextPluralInfo::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroContextPluralInfo object,
		 * by PluralInfoId Index(es)
		 * @param integer $intPluralInfoId
		 * @return NarroContextPluralInfo
		*/
		public static function LoadByPluralInfoId($intPluralInfoId) {
			return NarroContextPluralInfo::QuerySingle(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralInfoId, $intPluralInfoId)
			);
		}
			
		/**
		 * Load a single NarroContextPluralInfo object,
		 * by PluralId, LanguageId Index(es)
		 * @param integer $intPluralId
		 * @param integer $intLanguageId
		 * @return NarroContextPluralInfo
		*/
		public static function LoadByPluralIdLanguageId($intPluralId, $intLanguageId) {
			return NarroContextPluralInfo::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralId, $intPluralId),
				QQ::Equal(QQN::NarroContextPluralInfo()->LanguageId, $intLanguageId)
				)
			);
		}
			
		/**
		 * Load a single NarroContextPluralInfo object,
		 * by PluralId, LanguageId, ValidSuggestionId Index(es)
		 * @param integer $intPluralId
		 * @param integer $intLanguageId
		 * @param integer $intValidSuggestionId
		 * @return NarroContextPluralInfo
		*/
		public static function LoadByPluralIdLanguageIdValidSuggestionId($intPluralId, $intLanguageId, $intValidSuggestionId) {
			return NarroContextPluralInfo::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralId, $intPluralId),
				QQ::Equal(QQN::NarroContextPluralInfo()->LanguageId, $intLanguageId),
				QQ::Equal(QQN::NarroContextPluralInfo()->ValidSuggestionId, $intValidSuggestionId)
				)
			);
		}
			
		/**
		 * Load a single NarroContextPluralInfo object,
		 * by PluralId, LanguageId, PopularSuggestionId Index(es)
		 * @param integer $intPluralId
		 * @param integer $intLanguageId
		 * @param integer $intPopularSuggestionId
		 * @return NarroContextPluralInfo
		*/
		public static function LoadByPluralIdLanguageIdPopularSuggestionId($intPluralId, $intLanguageId, $intPopularSuggestionId) {
			return NarroContextPluralInfo::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralId, $intPluralId),
				QQ::Equal(QQN::NarroContextPluralInfo()->LanguageId, $intLanguageId),
				QQ::Equal(QQN::NarroContextPluralInfo()->PopularSuggestionId, $intPopularSuggestionId)
				)
			);
		}
			
		/**
		 * Load an array of NarroContextPluralInfo objects,
		 * by PluralId Index(es)
		 * @param integer $intPluralId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		*/
		public static function LoadArrayByPluralId($intPluralId, $objOptionalClauses = null) {
			// Call NarroContextPluralInfo::QueryArray to perform the LoadArrayByPluralId query
			try {
				return NarroContextPluralInfo::QueryArray(
					QQ::Equal(QQN::NarroContextPluralInfo()->PluralId, $intPluralId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPluralInfos
		 * by PluralId Index(es)
		 * @param integer $intPluralId
		 * @return int
		*/
		public static function CountByPluralId($intPluralId) {
			// Call NarroContextPluralInfo::QueryCount to perform the CountByPluralId query
			return NarroContextPluralInfo::QueryCount(
				QQ::Equal(QQN::NarroContextPluralInfo()->PluralId, $intPluralId)
			);
		}
			
		/**
		 * Load an array of NarroContextPluralInfo objects,
		 * by LanguageId Index(es)
		 * @param integer $intLanguageId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		*/
		public static function LoadArrayByLanguageId($intLanguageId, $objOptionalClauses = null) {
			// Call NarroContextPluralInfo::QueryArray to perform the LoadArrayByLanguageId query
			try {
				return NarroContextPluralInfo::QueryArray(
					QQ::Equal(QQN::NarroContextPluralInfo()->LanguageId, $intLanguageId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPluralInfos
		 * by LanguageId Index(es)
		 * @param integer $intLanguageId
		 * @return int
		*/
		public static function CountByLanguageId($intLanguageId) {
			// Call NarroContextPluralInfo::QueryCount to perform the CountByLanguageId query
			return NarroContextPluralInfo::QueryCount(
				QQ::Equal(QQN::NarroContextPluralInfo()->LanguageId, $intLanguageId)
			);
		}
			
		/**
		 * Load an array of NarroContextPluralInfo objects,
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		*/
		public static function LoadArrayByValidSuggestionId($intValidSuggestionId, $objOptionalClauses = null) {
			// Call NarroContextPluralInfo::QueryArray to perform the LoadArrayByValidSuggestionId query
			try {
				return NarroContextPluralInfo::QueryArray(
					QQ::Equal(QQN::NarroContextPluralInfo()->ValidSuggestionId, $intValidSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPluralInfos
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @return int
		*/
		public static function CountByValidSuggestionId($intValidSuggestionId) {
			// Call NarroContextPluralInfo::QueryCount to perform the CountByValidSuggestionId query
			return NarroContextPluralInfo::QueryCount(
				QQ::Equal(QQN::NarroContextPluralInfo()->ValidSuggestionId, $intValidSuggestionId)
			);
		}
			
		/**
		 * Load an array of NarroContextPluralInfo objects,
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		*/
		public static function LoadArrayByPopularSuggestionId($intPopularSuggestionId, $objOptionalClauses = null) {
			// Call NarroContextPluralInfo::QueryArray to perform the LoadArrayByPopularSuggestionId query
			try {
				return NarroContextPluralInfo::QueryArray(
					QQ::Equal(QQN::NarroContextPluralInfo()->PopularSuggestionId, $intPopularSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPluralInfos
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @return int
		*/
		public static function CountByPopularSuggestionId($intPopularSuggestionId) {
			// Call NarroContextPluralInfo::QueryCount to perform the CountByPopularSuggestionId query
			return NarroContextPluralInfo::QueryCount(
				QQ::Equal(QQN::NarroContextPluralInfo()->PopularSuggestionId, $intPopularSuggestionId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroContextPluralInfo
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPluralInfo::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_context_plural_info` (
							`plural_id`,
							`language_id`,
							`valid_suggestion_id`,
							`popular_suggestion_id`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intPluralId) . ',
							' . $objDatabase->SqlVariable($this->intLanguageId) . ',
							' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intPluralInfoId = $objDatabase->InsertId('narro_context_plural_info', 'plural_info_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_context_plural_info`
						SET
							`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . ',
							`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . ',
							`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . '
						WHERE
							`plural_info_id` = ' . $objDatabase->SqlVariable($this->intPluralInfoId) . '
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
		 * Delete this NarroContextPluralInfo
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intPluralInfoId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroContextPluralInfo with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPluralInfo::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural_info`
				WHERE
					`plural_info_id` = ' . $objDatabase->SqlVariable($this->intPluralInfoId) . '');
		}

		/**
		 * Delete all NarroContextPluralInfos
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPluralInfo::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural_info`');
		}

		/**
		 * Truncate narro_context_plural_info table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPluralInfo::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_context_plural_info`');
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
				case 'PluralInfoId':
					/**
					 * Gets the value for intPluralInfoId (Read-Only PK)
					 * @return integer
					 */
					return $this->intPluralInfoId;

				case 'PluralId':
					/**
					 * Gets the value for intPluralId (Not Null)
					 * @return integer
					 */
					return $this->intPluralId;

				case 'LanguageId':
					/**
					 * Gets the value for intLanguageId (Not Null)
					 * @return integer
					 */
					return $this->intLanguageId;

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


				///////////////////
				// Member Objects
				///////////////////
				case 'Plural':
					/**
					 * Gets the value for the NarroContextPlural object referenced by intPluralId (Not Null)
					 * @return NarroContextPlural
					 */
					try {
						if ((!$this->objPlural) && (!is_null($this->intPluralId)))
							$this->objPlural = NarroContextPlural::Load($this->intPluralId);
						return $this->objPlural;
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

				case 'ValidSuggestion':
					/**
					 * Gets the value for the NarroSuggestion object referenced by intValidSuggestionId 
					 * @return NarroSuggestion
					 */
					try {
						if ((!$this->objValidSuggestion) && (!is_null($this->intValidSuggestionId)))
							$this->objValidSuggestion = NarroSuggestion::Load($this->intValidSuggestionId);
						return $this->objValidSuggestion;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'PopularSuggestion':
					/**
					 * Gets the value for the NarroSuggestion object referenced by intPopularSuggestionId 
					 * @return NarroSuggestion
					 */
					try {
						if ((!$this->objPopularSuggestion) && (!is_null($this->intPopularSuggestionId)))
							$this->objPopularSuggestion = NarroSuggestion::Load($this->intPopularSuggestionId);
						return $this->objPopularSuggestion;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

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
				case 'PluralId':
					/**
					 * Sets the value for intPluralId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objPlural = null;
						return ($this->intPluralId = QType::Cast($mixValue, QType::Integer));
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


				///////////////////
				// Member Objects
				///////////////////
				case 'Plural':
					/**
					 * Sets the value for the NarroContextPlural object referenced by intPluralId (Not Null)
					 * @param NarroContextPlural $mixValue
					 * @return NarroContextPlural
					 */
					if (is_null($mixValue)) {
						$this->intPluralId = null;
						$this->objPlural = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroContextPlural object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroContextPlural');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroContextPlural object
						if (is_null($mixValue->PluralId))
							throw new QCallerException('Unable to set an unsaved Plural for this NarroContextPluralInfo');

						// Update Local Member Variables
						$this->objPlural = $mixValue;
						$this->intPluralId = $mixValue->PluralId;

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
							throw new QCallerException('Unable to set an unsaved Language for this NarroContextPluralInfo');

						// Update Local Member Variables
						$this->objLanguage = $mixValue;
						$this->intLanguageId = $mixValue->LanguageId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'ValidSuggestion':
					/**
					 * Sets the value for the NarroSuggestion object referenced by intValidSuggestionId 
					 * @param NarroSuggestion $mixValue
					 * @return NarroSuggestion
					 */
					if (is_null($mixValue)) {
						$this->intValidSuggestionId = null;
						$this->objValidSuggestion = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroSuggestion object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroSuggestion');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroSuggestion object
						if (is_null($mixValue->SuggestionId))
							throw new QCallerException('Unable to set an unsaved ValidSuggestion for this NarroContextPluralInfo');

						// Update Local Member Variables
						$this->objValidSuggestion = $mixValue;
						$this->intValidSuggestionId = $mixValue->SuggestionId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'PopularSuggestion':
					/**
					 * Sets the value for the NarroSuggestion object referenced by intPopularSuggestionId 
					 * @param NarroSuggestion $mixValue
					 * @return NarroSuggestion
					 */
					if (is_null($mixValue)) {
						$this->intPopularSuggestionId = null;
						$this->objPopularSuggestion = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroSuggestion object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroSuggestion');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroSuggestion object
						if (is_null($mixValue->SuggestionId))
							throw new QCallerException('Unable to set an unsaved PopularSuggestion for this NarroContextPluralInfo');

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




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_context_plural_info.plural_info_id
		 * @var integer intPluralInfoId
		 */
		protected $intPluralInfoId;
		const PluralInfoIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural_info.plural_id
		 * @var integer intPluralId
		 */
		protected $intPluralId;
		const PluralIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural_info.language_id
		 * @var integer intLanguageId
		 */
		protected $intLanguageId;
		const LanguageIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural_info.valid_suggestion_id
		 * @var integer intValidSuggestionId
		 */
		protected $intValidSuggestionId;
		const ValidSuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural_info.popular_suggestion_id
		 * @var integer intPopularSuggestionId
		 */
		protected $intPopularSuggestionId;
		const PopularSuggestionIdDefault = null;


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
		 * in the database column narro_context_plural_info.plural_id.
		 *
		 * NOTE: Always use the Plural property getter to correctly retrieve this NarroContextPlural object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroContextPlural objPlural
		 */
		protected $objPlural;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_context_plural_info.language_id.
		 *
		 * NOTE: Always use the Language property getter to correctly retrieve this NarroLanguage object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroLanguage objLanguage
		 */
		protected $objLanguage;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_context_plural_info.valid_suggestion_id.
		 *
		 * NOTE: Always use the ValidSuggestion property getter to correctly retrieve this NarroSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroSuggestion objValidSuggestion
		 */
		protected $objValidSuggestion;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_context_plural_info.popular_suggestion_id.
		 *
		 * NOTE: Always use the PopularSuggestion property getter to correctly retrieve this NarroSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroSuggestion objPopularSuggestion
		 */
		protected $objPopularSuggestion;






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroContextPluralInfo"><sequence>';
			$strToReturn .= '<element name="PluralInfoId" type="xsd:int"/>';
			$strToReturn .= '<element name="Plural" type="xsd1:NarroContextPlural"/>';
			$strToReturn .= '<element name="Language" type="xsd1:NarroLanguage"/>';
			$strToReturn .= '<element name="ValidSuggestion" type="xsd1:NarroSuggestion"/>';
			$strToReturn .= '<element name="PopularSuggestion" type="xsd1:NarroSuggestion"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroContextPluralInfo', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroContextPluralInfo'] = NarroContextPluralInfo::GetSoapComplexTypeXml();
				NarroContextPlural::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroLanguage::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroContextPluralInfo::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroContextPluralInfo();
			if (property_exists($objSoapObject, 'PluralInfoId'))
				$objToReturn->intPluralInfoId = $objSoapObject->PluralInfoId;
			if ((property_exists($objSoapObject, 'Plural')) &&
				($objSoapObject->Plural))
				$objToReturn->Plural = NarroContextPlural::GetObjectFromSoapObject($objSoapObject->Plural);
			if ((property_exists($objSoapObject, 'Language')) &&
				($objSoapObject->Language))
				$objToReturn->Language = NarroLanguage::GetObjectFromSoapObject($objSoapObject->Language);
			if ((property_exists($objSoapObject, 'ValidSuggestion')) &&
				($objSoapObject->ValidSuggestion))
				$objToReturn->ValidSuggestion = NarroSuggestion::GetObjectFromSoapObject($objSoapObject->ValidSuggestion);
			if ((property_exists($objSoapObject, 'PopularSuggestion')) &&
				($objSoapObject->PopularSuggestion))
				$objToReturn->PopularSuggestion = NarroSuggestion::GetObjectFromSoapObject($objSoapObject->PopularSuggestion);
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroContextPluralInfo::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objPlural)
				$objObject->objPlural = NarroContextPlural::GetSoapObjectFromObject($objObject->objPlural, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intPluralId = null;
			if ($objObject->objLanguage)
				$objObject->objLanguage = NarroLanguage::GetSoapObjectFromObject($objObject->objLanguage, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intLanguageId = null;
			if ($objObject->objValidSuggestion)
				$objObject->objValidSuggestion = NarroSuggestion::GetSoapObjectFromObject($objObject->objValidSuggestion, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intValidSuggestionId = null;
			if ($objObject->objPopularSuggestion)
				$objObject->objPopularSuggestion = NarroSuggestion::GetSoapObjectFromObject($objObject->objPopularSuggestion, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intPopularSuggestionId = null;
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroContextPluralInfo extends QQNode {
		protected $strTableName = 'narro_context_plural_info';
		protected $strPrimaryKey = 'plural_info_id';
		protected $strClassName = 'NarroContextPluralInfo';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralInfoId':
					return new QQNode('plural_info_id', 'integer', $this);
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'Plural':
					return new QQNodeNarroContextPlural('plural_id', 'integer', $this);
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'Language':
					return new QQNodeNarroLanguage('language_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroSuggestion('popular_suggestion_id', 'integer', $this);

				case '_PrimaryKeyNode':
					return new QQNode('plural_info_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroContextPluralInfo extends QQReverseReferenceNode {
		protected $strTableName = 'narro_context_plural_info';
		protected $strPrimaryKey = 'plural_info_id';
		protected $strClassName = 'NarroContextPluralInfo';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralInfoId':
					return new QQNode('plural_info_id', 'integer', $this);
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'Plural':
					return new QQNodeNarroContextPlural('plural_id', 'integer', $this);
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'Language':
					return new QQNodeNarroLanguage('language_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroSuggestion('popular_suggestion_id', 'integer', $this);

				case '_PrimaryKeyNode':
					return new QQNode('plural_info_id', 'integer', $this);
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