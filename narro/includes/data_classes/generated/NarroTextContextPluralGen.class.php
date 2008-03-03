<?php
	/**
	 * The abstract NarroTextContextPluralGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroTextContextPlural subclass which
	 * extends this NarroTextContextPluralGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroTextContextPlural class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroTextContextPluralGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroTextContextPlural from PK Info
		 * @param integer $intPluralId
		 * @return NarroTextContextPlural
		 */
		public static function Load($intPluralId) {
			// Use QuerySingle to Perform the Query
			return NarroTextContextPlural::QuerySingle(
				QQ::Equal(QQN::NarroTextContextPlural()->PluralId, $intPluralId)
			);
		}

		/**
		 * Load all NarroTextContextPlurals
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroTextContextPlural::QueryArray to perform the LoadAll query
			try {
				return NarroTextContextPlural::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroTextContextPlurals
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroTextContextPlural::QueryCount to perform the CountAll query
			return NarroTextContextPlural::QueryCount(QQ::All());
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
			$objDatabase = NarroTextContextPlural::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroTextContextPlural-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_text_context_plural');
			NarroTextContextPlural::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_text_context_plural` AS `narro_text_context_plural`');

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
		 * Static Qcodo Query method to query for a single NarroTextContextPlural object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextContextPlural the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroTextContextPlural object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextContextPlural::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroTextContextPlural objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroTextContextPlural[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroTextContextPlural::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroTextContextPlural objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroTextContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroTextContextPlural::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_text_context_plural_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroTextContextPlural-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroTextContextPlural::GetSelectFields($objQueryBuilder);
				NarroTextContextPlural::GetFromFields($objQueryBuilder);

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
			return NarroTextContextPlural::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroTextContextPlural
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_text_context_plural`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`plural_id` AS ' . $strAliasPrefix . 'plural_id`');
			$objBuilder->AddSelectItem($strTableName . '.`context_id` AS ' . $strAliasPrefix . 'context_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`valid_suggestion_id` AS ' . $strAliasPrefix . 'valid_suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`popular_suggestion_id` AS ' . $strAliasPrefix . 'popular_suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`plural_form` AS ' . $strAliasPrefix . 'plural_form`');
			$objBuilder->AddSelectItem($strTableName . '.`has_suggestion` AS ' . $strAliasPrefix . 'has_suggestion`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroTextContextPlural from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroTextContextPlural::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroTextContextPlural
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;


			// Create a new instance of the NarroTextContextPlural object
			$objToReturn = new NarroTextContextPlural();
			$objToReturn->__blnRestored = true;

			$objToReturn->intPluralId = $objDbRow->GetColumn($strAliasPrefix . 'plural_id', 'Integer');
			$objToReturn->intContextId = $objDbRow->GetColumn($strAliasPrefix . 'context_id', 'Integer');
			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->intValidSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id', 'Integer');
			$objToReturn->intPopularSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id', 'Integer');
			$objToReturn->blnPluralForm = $objDbRow->GetColumn($strAliasPrefix . 'plural_form', 'Bit');
			$objToReturn->blnHasSuggestion = $objDbRow->GetColumn($strAliasPrefix . 'has_suggestion', 'Bit');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_text_context_plural__';

			// Check for Context Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'context_id__context_id')))
				$objToReturn->objContext = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'context_id__', $strExpandAsArrayNodes);

			// Check for Text Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'text_id__text_id')))
				$objToReturn->objText = NarroText::InstantiateDbRow($objDbRow, $strAliasPrefix . 'text_id__', $strExpandAsArrayNodes);

			// Check for ValidSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'valid_suggestion_id__suggestion_id')))
				$objToReturn->objValidSuggestion = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'valid_suggestion_id__', $strExpandAsArrayNodes);

			// Check for PopularSuggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'popular_suggestion_id__suggestion_id')))
				$objToReturn->objPopularSuggestion = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'popular_suggestion_id__', $strExpandAsArrayNodes);




			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroTextContextPlurals from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroTextContextPlural[]
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
					$objItem = NarroTextContextPlural::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroTextContextPlural::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroTextContextPlural object,
		 * by PluralId Index(es)
		 * @param integer $intPluralId
		 * @return NarroTextContextPlural
		*/
		public static function LoadByPluralId($intPluralId) {
			return NarroTextContextPlural::QuerySingle(
				QQ::Equal(QQN::NarroTextContextPlural()->PluralId, $intPluralId)
			);
		}
			
		/**
		 * Load an array of NarroTextContextPlural objects,
		 * by ContextId Index(es)
		 * @param integer $intContextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/
		public static function LoadArrayByContextId($intContextId, $objOptionalClauses = null) {
			// Call NarroTextContextPlural::QueryArray to perform the LoadArrayByContextId query
			try {
				return NarroTextContextPlural::QueryArray(
					QQ::Equal(QQN::NarroTextContextPlural()->ContextId, $intContextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContextPlurals
		 * by ContextId Index(es)
		 * @param integer $intContextId
		 * @return int
		*/
		public static function CountByContextId($intContextId) {
			// Call NarroTextContextPlural::QueryCount to perform the CountByContextId query
			return NarroTextContextPlural::QueryCount(
				QQ::Equal(QQN::NarroTextContextPlural()->ContextId, $intContextId)
			);
		}
			
		/**
		 * Load an array of NarroTextContextPlural objects,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/
		public static function LoadArrayByTextId($intTextId, $objOptionalClauses = null) {
			// Call NarroTextContextPlural::QueryArray to perform the LoadArrayByTextId query
			try {
				return NarroTextContextPlural::QueryArray(
					QQ::Equal(QQN::NarroTextContextPlural()->TextId, $intTextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContextPlurals
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return int
		*/
		public static function CountByTextId($intTextId) {
			// Call NarroTextContextPlural::QueryCount to perform the CountByTextId query
			return NarroTextContextPlural::QueryCount(
				QQ::Equal(QQN::NarroTextContextPlural()->TextId, $intTextId)
			);
		}
			
		/**
		 * Load an array of NarroTextContextPlural objects,
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/
		public static function LoadArrayByValidSuggestionId($intValidSuggestionId, $objOptionalClauses = null) {
			// Call NarroTextContextPlural::QueryArray to perform the LoadArrayByValidSuggestionId query
			try {
				return NarroTextContextPlural::QueryArray(
					QQ::Equal(QQN::NarroTextContextPlural()->ValidSuggestionId, $intValidSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContextPlurals
		 * by ValidSuggestionId Index(es)
		 * @param integer $intValidSuggestionId
		 * @return int
		*/
		public static function CountByValidSuggestionId($intValidSuggestionId) {
			// Call NarroTextContextPlural::QueryCount to perform the CountByValidSuggestionId query
			return NarroTextContextPlural::QueryCount(
				QQ::Equal(QQN::NarroTextContextPlural()->ValidSuggestionId, $intValidSuggestionId)
			);
		}
			
		/**
		 * Load an array of NarroTextContextPlural objects,
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/
		public static function LoadArrayByPopularSuggestionId($intPopularSuggestionId, $objOptionalClauses = null) {
			// Call NarroTextContextPlural::QueryArray to perform the LoadArrayByPopularSuggestionId query
			try {
				return NarroTextContextPlural::QueryArray(
					QQ::Equal(QQN::NarroTextContextPlural()->PopularSuggestionId, $intPopularSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroTextContextPlurals
		 * by PopularSuggestionId Index(es)
		 * @param integer $intPopularSuggestionId
		 * @return int
		*/
		public static function CountByPopularSuggestionId($intPopularSuggestionId) {
			// Call NarroTextContextPlural::QueryCount to perform the CountByPopularSuggestionId query
			return NarroTextContextPlural::QueryCount(
				QQ::Equal(QQN::NarroTextContextPlural()->PopularSuggestionId, $intPopularSuggestionId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroTextContextPlural
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContextPlural::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_text_context_plural` (
							`context_id`,
							`text_id`,
							`valid_suggestion_id`,
							`popular_suggestion_id`,
							`plural_form`,
							`has_suggestion`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intContextId) . ',
							' . $objDatabase->SqlVariable($this->intTextId) . ',
							' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->blnPluralForm) . ',
							' . $objDatabase->SqlVariable($this->blnHasSuggestion) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intPluralId = $objDatabase->InsertId('narro_text_context_plural', 'plural_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_text_context_plural`
						SET
							`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . ',
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ',
							`valid_suggestion_id` = ' . $objDatabase->SqlVariable($this->intValidSuggestionId) . ',
							`popular_suggestion_id` = ' . $objDatabase->SqlVariable($this->intPopularSuggestionId) . ',
							`plural_form` = ' . $objDatabase->SqlVariable($this->blnPluralForm) . ',
							`has_suggestion` = ' . $objDatabase->SqlVariable($this->blnHasSuggestion) . '
						WHERE
							`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
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
		 * Delete this NarroTextContextPlural
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroTextContextPlural with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroTextContextPlural::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '');
		}

		/**
		 * Delete all NarroTextContextPlurals
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContextPlural::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`');
		}

		/**
		 * Truncate narro_text_context_plural table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroTextContextPlural::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_text_context_plural`');
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
				case 'PluralId':
					/**
					 * Gets the value for intPluralId (Read-Only PK)
					 * @return integer
					 */
					return $this->intPluralId;

				case 'ContextId':
					/**
					 * Gets the value for intContextId (Not Null)
					 * @return integer
					 */
					return $this->intContextId;

				case 'TextId':
					/**
					 * Gets the value for intTextId (Not Null)
					 * @return integer
					 */
					return $this->intTextId;

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

				case 'PluralForm':
					/**
					 * Gets the value for blnPluralForm (Not Null)
					 * @return boolean
					 */
					return $this->blnPluralForm;

				case 'HasSuggestion':
					/**
					 * Gets the value for blnHasSuggestion (Not Null)
					 * @return boolean
					 */
					return $this->blnHasSuggestion;


				///////////////////
				// Member Objects
				///////////////////
				case 'Context':
					/**
					 * Gets the value for the NarroTextContext object referenced by intContextId (Not Null)
					 * @return NarroTextContext
					 */
					try {
						if ((!$this->objContext) && (!is_null($this->intContextId)))
							$this->objContext = NarroTextContext::Load($this->intContextId);
						return $this->objContext;
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
				case 'ContextId':
					/**
					 * Sets the value for intContextId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objContext = null;
						return ($this->intContextId = QType::Cast($mixValue, QType::Integer));
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

				case 'PluralForm':
					/**
					 * Sets the value for blnPluralForm (Not Null)
					 * @param boolean $mixValue
					 * @return boolean
					 */
					try {
						return ($this->blnPluralForm = QType::Cast($mixValue, QType::Boolean));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'HasSuggestion':
					/**
					 * Sets the value for blnHasSuggestion (Not Null)
					 * @param boolean $mixValue
					 * @return boolean
					 */
					try {
						return ($this->blnHasSuggestion = QType::Cast($mixValue, QType::Boolean));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
				case 'Context':
					/**
					 * Sets the value for the NarroTextContext object referenced by intContextId (Not Null)
					 * @param NarroTextContext $mixValue
					 * @return NarroTextContext
					 */
					if (is_null($mixValue)) {
						$this->intContextId = null;
						$this->objContext = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroTextContext object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroTextContext');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroTextContext object
						if (is_null($mixValue->ContextId))
							throw new QCallerException('Unable to set an unsaved Context for this NarroTextContextPlural');

						// Update Local Member Variables
						$this->objContext = $mixValue;
						$this->intContextId = $mixValue->ContextId;

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
							throw new QCallerException('Unable to set an unsaved Text for this NarroTextContextPlural');

						// Update Local Member Variables
						$this->objText = $mixValue;
						$this->intTextId = $mixValue->TextId;

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
							throw new QCallerException('Unable to set an unsaved ValidSuggestion for this NarroTextContextPlural');

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
							throw new QCallerException('Unable to set an unsaved PopularSuggestion for this NarroTextContextPlural');

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
		 * Protected member variable that maps to the database PK Identity column narro_text_context_plural.plural_id
		 * @var integer intPluralId
		 */
		protected $intPluralId;
		const PluralIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.context_id
		 * @var integer intContextId
		 */
		protected $intContextId;
		const ContextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.valid_suggestion_id
		 * @var integer intValidSuggestionId
		 */
		protected $intValidSuggestionId;
		const ValidSuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.popular_suggestion_id
		 * @var integer intPopularSuggestionId
		 */
		protected $intPopularSuggestionId;
		const PopularSuggestionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.plural_form
		 * @var boolean blnPluralForm
		 */
		protected $blnPluralForm;
		const PluralFormDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text_context_plural.has_suggestion
		 * @var boolean blnHasSuggestion
		 */
		protected $blnHasSuggestion;
		const HasSuggestionDefault = null;


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
		 * in the database column narro_text_context_plural.context_id.
		 *
		 * NOTE: Always use the Context property getter to correctly retrieve this NarroTextContext object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroTextContext objContext
		 */
		protected $objContext;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context_plural.text_id.
		 *
		 * NOTE: Always use the Text property getter to correctly retrieve this NarroText object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroText objText
		 */
		protected $objText;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context_plural.valid_suggestion_id.
		 *
		 * NOTE: Always use the ValidSuggestion property getter to correctly retrieve this NarroTextSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroTextSuggestion objValidSuggestion
		 */
		protected $objValidSuggestion;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_text_context_plural.popular_suggestion_id.
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
			$strToReturn = '<complexType name="NarroTextContextPlural"><sequence>';
			$strToReturn .= '<element name="PluralId" type="xsd:int"/>';
			$strToReturn .= '<element name="Context" type="xsd1:NarroTextContext"/>';
			$strToReturn .= '<element name="Text" type="xsd1:NarroText"/>';
			$strToReturn .= '<element name="ValidSuggestion" type="xsd1:NarroTextSuggestion"/>';
			$strToReturn .= '<element name="PopularSuggestion" type="xsd1:NarroTextSuggestion"/>';
			$strToReturn .= '<element name="PluralForm" type="xsd:boolean"/>';
			$strToReturn .= '<element name="HasSuggestion" type="xsd:boolean"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroTextContextPlural', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroTextContextPlural'] = NarroTextContextPlural::GetSoapComplexTypeXml();
				NarroTextContext::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroText::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroTextSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroTextSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroTextContextPlural::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroTextContextPlural();
			if (property_exists($objSoapObject, 'PluralId'))
				$objToReturn->intPluralId = $objSoapObject->PluralId;
			if ((property_exists($objSoapObject, 'Context')) &&
				($objSoapObject->Context))
				$objToReturn->Context = NarroTextContext::GetObjectFromSoapObject($objSoapObject->Context);
			if ((property_exists($objSoapObject, 'Text')) &&
				($objSoapObject->Text))
				$objToReturn->Text = NarroText::GetObjectFromSoapObject($objSoapObject->Text);
			if ((property_exists($objSoapObject, 'ValidSuggestion')) &&
				($objSoapObject->ValidSuggestion))
				$objToReturn->ValidSuggestion = NarroTextSuggestion::GetObjectFromSoapObject($objSoapObject->ValidSuggestion);
			if ((property_exists($objSoapObject, 'PopularSuggestion')) &&
				($objSoapObject->PopularSuggestion))
				$objToReturn->PopularSuggestion = NarroTextSuggestion::GetObjectFromSoapObject($objSoapObject->PopularSuggestion);
			if (property_exists($objSoapObject, 'PluralForm'))
				$objToReturn->blnPluralForm = $objSoapObject->PluralForm;
			if (property_exists($objSoapObject, 'HasSuggestion'))
				$objToReturn->blnHasSuggestion = $objSoapObject->HasSuggestion;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroTextContextPlural::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objContext)
				$objObject->objContext = NarroTextContext::GetSoapObjectFromObject($objObject->objContext, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intContextId = null;
			if ($objObject->objText)
				$objObject->objText = NarroText::GetSoapObjectFromObject($objObject->objText, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intTextId = null;
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

	class QQNodeNarroTextContextPlural extends QQNode {
		protected $strTableName = 'narro_text_context_plural';
		protected $strPrimaryKey = 'plural_id';
		protected $strClassName = 'NarroTextContextPlural';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'Context':
					return new QQNodeNarroTextContext('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroTextSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroTextSuggestion('popular_suggestion_id', 'integer', $this);
				case 'PluralForm':
					return new QQNode('plural_form', 'boolean', $this);
				case 'HasSuggestion':
					return new QQNode('has_suggestion', 'boolean', $this);

				case '_PrimaryKeyNode':
					return new QQNode('plural_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroTextContextPlural extends QQReverseReferenceNode {
		protected $strTableName = 'narro_text_context_plural';
		protected $strPrimaryKey = 'plural_id';
		protected $strClassName = 'NarroTextContextPlural';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'Context':
					return new QQNodeNarroTextContext('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'ValidSuggestionId':
					return new QQNode('valid_suggestion_id', 'integer', $this);
				case 'ValidSuggestion':
					return new QQNodeNarroTextSuggestion('valid_suggestion_id', 'integer', $this);
				case 'PopularSuggestionId':
					return new QQNode('popular_suggestion_id', 'integer', $this);
				case 'PopularSuggestion':
					return new QQNodeNarroTextSuggestion('popular_suggestion_id', 'integer', $this);
				case 'PluralForm':
					return new QQNode('plural_form', 'boolean', $this);
				case 'HasSuggestion':
					return new QQNode('has_suggestion', 'boolean', $this);

				case '_PrimaryKeyNode':
					return new QQNode('plural_id', 'integer', $this);
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