<?php
	/**
	 * The abstract NarroContextPluralGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroContextPlural subclass which
	 * extends this NarroContextPluralGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroContextPlural class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroContextPluralGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroContextPlural from PK Info
		 * @param integer $intPluralId
		 * @return NarroContextPlural
		 */
		public static function Load($intPluralId) {
			// Use QuerySingle to Perform the Query
			return NarroContextPlural::QuerySingle(
				QQ::Equal(QQN::NarroContextPlural()->PluralId, $intPluralId)
			);
		}

		/**
		 * Load all NarroContextPlurals
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPlural[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroContextPlural::QueryArray to perform the LoadAll query
			try {
				return NarroContextPlural::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroContextPlurals
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroContextPlural::QueryCount to perform the CountAll query
			return NarroContextPlural::QueryCount(QQ::All());
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
			$objDatabase = NarroContextPlural::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroContextPlural-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_context_plural');
			NarroContextPlural::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_context_plural` AS `narro_context_plural`');

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
		 * Static Qcodo Query method to query for a single NarroContextPlural object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroContextPlural the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroContextPlural object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroContextPlural::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroContextPlural objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroContextPlural[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroContextPlural::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroContextPlural objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroContextPlural::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroContextPlural::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_context_plural_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroContextPlural-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroContextPlural::GetSelectFields($objQueryBuilder);
				NarroContextPlural::GetFromFields($objQueryBuilder);

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
			return NarroContextPlural::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroContextPlural
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_context_plural`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`plural_id` AS ' . $strAliasPrefix . 'plural_id`');
			$objBuilder->AddSelectItem($strTableName . '.`context_id` AS ' . $strAliasPrefix . 'context_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`plural_form` AS ' . $strAliasPrefix . 'plural_form`');
			$objBuilder->AddSelectItem($strTableName . '.`active` AS ' . $strAliasPrefix . 'active`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroContextPlural from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroContextPlural::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroContextPlural
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;

			// See if we're doing an array expansion on the previous item
			if (($strExpandAsArrayNodes) && ($objPreviousItem) &&
				($objPreviousItem->intPluralId == $objDbRow->GetColumn($strAliasPrefix . 'plural_id', 'Integer'))) {

				// We are.  Now, prepare to check for ExpandAsArray clauses
				$blnExpandedViaArray = false;
				if (!$strAliasPrefix)
					$strAliasPrefix = 'narro_context_plural__';


				if ((array_key_exists($strAliasPrefix . 'narrocontextpluralinfoasplural__plural_info_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrocontextpluralinfoasplural__plural_info_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroContextPluralInfoAsPluralArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroContextPluralInfoAsPluralArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroContextPluralInfo::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrocontextpluralinfoasplural__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroContextPluralInfoAsPluralArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroContextPluralInfoAsPluralArray, NarroContextPluralInfo::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrocontextpluralinfoasplural__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				// Either return false to signal array expansion, or check-to-reset the Alias prefix and move on
				if ($blnExpandedViaArray)
					return false;
				else if ($strAliasPrefix == 'narro_context_plural__')
					$strAliasPrefix = null;
			}

			// Create a new instance of the NarroContextPlural object
			$objToReturn = new NarroContextPlural();
			$objToReturn->__blnRestored = true;

			$objToReturn->intPluralId = $objDbRow->GetColumn($strAliasPrefix . 'plural_id', 'Integer');
			$objToReturn->intContextId = $objDbRow->GetColumn($strAliasPrefix . 'context_id', 'Integer');
			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->blnPluralForm = $objDbRow->GetColumn($strAliasPrefix . 'plural_form', 'Bit');
			$objToReturn->blnActive = $objDbRow->GetColumn($strAliasPrefix . 'active', 'Bit');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_context_plural__';

			// Check for Context Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'context_id__context_id')))
				$objToReturn->objContext = NarroContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'context_id__', $strExpandAsArrayNodes);

			// Check for Text Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'text_id__text_id')))
				$objToReturn->objText = NarroText::InstantiateDbRow($objDbRow, $strAliasPrefix . 'text_id__', $strExpandAsArrayNodes);




			// Check for NarroContextPluralInfoAsPlural Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrocontextpluralinfoasplural__plural_info_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrocontextpluralinfoasplural__plural_info_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroContextPluralInfoAsPluralArray, NarroContextPluralInfo::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrocontextpluralinfoasplural__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroContextPluralInfoAsPlural = NarroContextPluralInfo::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrocontextpluralinfoasplural__', $strExpandAsArrayNodes);
			}

			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroContextPlurals from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroContextPlural[]
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
					$objItem = NarroContextPlural::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroContextPlural::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroContextPlural object,
		 * by PluralId Index(es)
		 * @param integer $intPluralId
		 * @return NarroContextPlural
		*/
		public static function LoadByPluralId($intPluralId) {
			return NarroContextPlural::QuerySingle(
				QQ::Equal(QQN::NarroContextPlural()->PluralId, $intPluralId)
			);
		}
			
		/**
		 * Load an array of NarroContextPlural objects,
		 * by ContextId Index(es)
		 * @param integer $intContextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPlural[]
		*/
		public static function LoadArrayByContextId($intContextId, $objOptionalClauses = null) {
			// Call NarroContextPlural::QueryArray to perform the LoadArrayByContextId query
			try {
				return NarroContextPlural::QueryArray(
					QQ::Equal(QQN::NarroContextPlural()->ContextId, $intContextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPlurals
		 * by ContextId Index(es)
		 * @param integer $intContextId
		 * @return int
		*/
		public static function CountByContextId($intContextId) {
			// Call NarroContextPlural::QueryCount to perform the CountByContextId query
			return NarroContextPlural::QueryCount(
				QQ::Equal(QQN::NarroContextPlural()->ContextId, $intContextId)
			);
		}
			
		/**
		 * Load an array of NarroContextPlural objects,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPlural[]
		*/
		public static function LoadArrayByTextId($intTextId, $objOptionalClauses = null) {
			// Call NarroContextPlural::QueryArray to perform the LoadArrayByTextId query
			try {
				return NarroContextPlural::QueryArray(
					QQ::Equal(QQN::NarroContextPlural()->TextId, $intTextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroContextPlurals
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return int
		*/
		public static function CountByTextId($intTextId) {
			// Call NarroContextPlural::QueryCount to perform the CountByTextId query
			return NarroContextPlural::QueryCount(
				QQ::Equal(QQN::NarroContextPlural()->TextId, $intTextId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroContextPlural
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_context_plural` (
							`context_id`,
							`text_id`,
							`plural_form`,
							`active`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intContextId) . ',
							' . $objDatabase->SqlVariable($this->intTextId) . ',
							' . $objDatabase->SqlVariable($this->blnPluralForm) . ',
							' . $objDatabase->SqlVariable($this->blnActive) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intPluralId = $objDatabase->InsertId('narro_context_plural', 'plural_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_context_plural`
						SET
							`context_id` = ' . $objDatabase->SqlVariable($this->intContextId) . ',
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ',
							`plural_form` = ' . $objDatabase->SqlVariable($this->blnPluralForm) . ',
							`active` = ' . $objDatabase->SqlVariable($this->blnActive) . '
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
		 * Delete this NarroContextPlural
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroContextPlural with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '');
		}

		/**
		 * Delete all NarroContextPlurals
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural`');
		}

		/**
		 * Truncate narro_context_plural table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_context_plural`');
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

				case 'PluralForm':
					/**
					 * Gets the value for blnPluralForm (Not Null)
					 * @return boolean
					 */
					return $this->blnPluralForm;

				case 'Active':
					/**
					 * Gets the value for blnActive (Not Null)
					 * @return boolean
					 */
					return $this->blnActive;


				///////////////////
				// Member Objects
				///////////////////
				case 'Context':
					/**
					 * Gets the value for the NarroContext object referenced by intContextId (Not Null)
					 * @return NarroContext
					 */
					try {
						if ((!$this->objContext) && (!is_null($this->intContextId)))
							$this->objContext = NarroContext::Load($this->intContextId);
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


				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

				case '_NarroContextPluralInfoAsPlural':
					/**
					 * Gets the value for the private _objNarroContextPluralInfoAsPlural (Read-Only)
					 * if set due to an expansion on the narro_context_plural_info.plural_id reverse relationship
					 * @return NarroContextPluralInfo
					 */
					return $this->_objNarroContextPluralInfoAsPlural;

				case '_NarroContextPluralInfoAsPluralArray':
					/**
					 * Gets the value for the private _objNarroContextPluralInfoAsPluralArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_context_plural_info.plural_id reverse relationship
					 * @return NarroContextPluralInfo[]
					 */
					return (array) $this->_objNarroContextPluralInfoAsPluralArray;

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

				case 'Active':
					/**
					 * Sets the value for blnActive (Not Null)
					 * @param boolean $mixValue
					 * @return boolean
					 */
					try {
						return ($this->blnActive = QType::Cast($mixValue, QType::Boolean));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
				case 'Context':
					/**
					 * Sets the value for the NarroContext object referenced by intContextId (Not Null)
					 * @param NarroContext $mixValue
					 * @return NarroContext
					 */
					if (is_null($mixValue)) {
						$this->intContextId = null;
						$this->objContext = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroContext object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroContext');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						} 

						// Make sure $mixValue is a SAVED NarroContext object
						if (is_null($mixValue->ContextId))
							throw new QCallerException('Unable to set an unsaved Context for this NarroContextPlural');

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
							throw new QCallerException('Unable to set an unsaved Text for this NarroContextPlural');

						// Update Local Member Variables
						$this->objText = $mixValue;
						$this->intTextId = $mixValue->TextId;

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

			
		
		// Related Objects' Methods for NarroContextPluralInfoAsPlural
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroContextPluralInfosAsPlural as an array of NarroContextPluralInfo objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroContextPluralInfo[]
		*/ 
		public function GetNarroContextPluralInfoAsPluralArray($objOptionalClauses = null) {
			if ((is_null($this->intPluralId)))
				return array();

			try {
				return NarroContextPluralInfo::LoadArrayByPluralId($this->intPluralId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroContextPluralInfosAsPlural
		 * @return int
		*/ 
		public function CountNarroContextPluralInfosAsPlural() {
			if ((is_null($this->intPluralId)))
				return 0;

			return NarroContextPluralInfo::CountByPluralId($this->intPluralId);
		}

		/**
		 * Associates a NarroContextPluralInfoAsPlural
		 * @param NarroContextPluralInfo $objNarroContextPluralInfo
		 * @return void
		*/ 
		public function AssociateNarroContextPluralInfoAsPlural(NarroContextPluralInfo $objNarroContextPluralInfo) {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroContextPluralInfoAsPlural on this unsaved NarroContextPlural.');
			if ((is_null($objNarroContextPluralInfo->PluralInfoId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroContextPluralInfoAsPlural on this NarroContextPlural with an unsaved NarroContextPluralInfo.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_context_plural_info`
				SET
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
				WHERE
					`plural_info_id` = ' . $objDatabase->SqlVariable($objNarroContextPluralInfo->PluralInfoId) . '
			');
		}

		/**
		 * Unassociates a NarroContextPluralInfoAsPlural
		 * @param NarroContextPluralInfo $objNarroContextPluralInfo
		 * @return void
		*/ 
		public function UnassociateNarroContextPluralInfoAsPlural(NarroContextPluralInfo $objNarroContextPluralInfo) {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this unsaved NarroContextPlural.');
			if ((is_null($objNarroContextPluralInfo->PluralInfoId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this NarroContextPlural with an unsaved NarroContextPluralInfo.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_context_plural_info`
				SET
					`plural_id` = null
				WHERE
					`plural_info_id` = ' . $objDatabase->SqlVariable($objNarroContextPluralInfo->PluralInfoId) . ' AND
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
			');
		}

		/**
		 * Unassociates all NarroContextPluralInfosAsPlural
		 * @return void
		*/ 
		public function UnassociateAllNarroContextPluralInfosAsPlural() {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this unsaved NarroContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_context_plural_info`
				SET
					`plural_id` = null
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
			');
		}

		/**
		 * Deletes an associated NarroContextPluralInfoAsPlural
		 * @param NarroContextPluralInfo $objNarroContextPluralInfo
		 * @return void
		*/ 
		public function DeleteAssociatedNarroContextPluralInfoAsPlural(NarroContextPluralInfo $objNarroContextPluralInfo) {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this unsaved NarroContextPlural.');
			if ((is_null($objNarroContextPluralInfo->PluralInfoId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this NarroContextPlural with an unsaved NarroContextPluralInfo.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural_info`
				WHERE
					`plural_info_id` = ' . $objDatabase->SqlVariable($objNarroContextPluralInfo->PluralInfoId) . ' AND
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
			');
		}

		/**
		 * Deletes all associated NarroContextPluralInfosAsPlural
		 * @return void
		*/ 
		public function DeleteAllNarroContextPluralInfosAsPlural() {
			if ((is_null($this->intPluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroContextPluralInfoAsPlural on this unsaved NarroContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroContextPlural::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_context_plural_info`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($this->intPluralId) . '
			');
		}




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_context_plural.plural_id
		 * @var integer intPluralId
		 */
		protected $intPluralId;
		const PluralIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural.context_id
		 * @var integer intContextId
		 */
		protected $intContextId;
		const ContextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural.plural_form
		 * @var boolean blnPluralForm
		 */
		protected $blnPluralForm;
		const PluralFormDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_context_plural.active
		 * @var boolean blnActive
		 */
		protected $blnActive;
		const ActiveDefault = null;


		/**
		 * Private member variable that stores a reference to a single NarroContextPluralInfoAsPlural object
		 * (of type NarroContextPluralInfo), if this NarroContextPlural object was restored with
		 * an expansion on the narro_context_plural_info association table.
		 * @var NarroContextPluralInfo _objNarroContextPluralInfoAsPlural;
		 */
		private $_objNarroContextPluralInfoAsPlural;

		/**
		 * Private member variable that stores a reference to an array of NarroContextPluralInfoAsPlural objects
		 * (of type NarroContextPluralInfo[]), if this NarroContextPlural object was restored with
		 * an ExpandAsArray on the narro_context_plural_info association table.
		 * @var NarroContextPluralInfo[] _objNarroContextPluralInfoAsPluralArray;
		 */
		private $_objNarroContextPluralInfoAsPluralArray = array();

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
		 * in the database column narro_context_plural.context_id.
		 *
		 * NOTE: Always use the Context property getter to correctly retrieve this NarroContext object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroContext objContext
		 */
		protected $objContext;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_context_plural.text_id.
		 *
		 * NOTE: Always use the Text property getter to correctly retrieve this NarroText object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroText objText
		 */
		protected $objText;






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroContextPlural"><sequence>';
			$strToReturn .= '<element name="PluralId" type="xsd:int"/>';
			$strToReturn .= '<element name="Context" type="xsd1:NarroContext"/>';
			$strToReturn .= '<element name="Text" type="xsd1:NarroText"/>';
			$strToReturn .= '<element name="PluralForm" type="xsd:boolean"/>';
			$strToReturn .= '<element name="Active" type="xsd:boolean"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroContextPlural', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroContextPlural'] = NarroContextPlural::GetSoapComplexTypeXml();
				NarroContext::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroText::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroContextPlural::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroContextPlural();
			if (property_exists($objSoapObject, 'PluralId'))
				$objToReturn->intPluralId = $objSoapObject->PluralId;
			if ((property_exists($objSoapObject, 'Context')) &&
				($objSoapObject->Context))
				$objToReturn->Context = NarroContext::GetObjectFromSoapObject($objSoapObject->Context);
			if ((property_exists($objSoapObject, 'Text')) &&
				($objSoapObject->Text))
				$objToReturn->Text = NarroText::GetObjectFromSoapObject($objSoapObject->Text);
			if (property_exists($objSoapObject, 'PluralForm'))
				$objToReturn->blnPluralForm = $objSoapObject->PluralForm;
			if (property_exists($objSoapObject, 'Active'))
				$objToReturn->blnActive = $objSoapObject->Active;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroContextPlural::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objContext)
				$objObject->objContext = NarroContext::GetSoapObjectFromObject($objObject->objContext, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intContextId = null;
			if ($objObject->objText)
				$objObject->objText = NarroText::GetSoapObjectFromObject($objObject->objText, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intTextId = null;
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroContextPlural extends QQNode {
		protected $strTableName = 'narro_context_plural';
		protected $strPrimaryKey = 'plural_id';
		protected $strClassName = 'NarroContextPlural';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'Context':
					return new QQNodeNarroContext('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'PluralForm':
					return new QQNode('plural_form', 'boolean', $this);
				case 'Active':
					return new QQNode('active', 'boolean', $this);
				case 'NarroContextPluralInfoAsPlural':
					return new QQReverseReferenceNodeNarroContextPluralInfo($this, 'narrocontextpluralinfoasplural', 'reverse_reference', 'plural_id');

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

	class QQReverseReferenceNodeNarroContextPlural extends QQReverseReferenceNode {
		protected $strTableName = 'narro_context_plural';
		protected $strPrimaryKey = 'plural_id';
		protected $strClassName = 'NarroContextPlural';
		public function __get($strName) {
			switch ($strName) {
				case 'PluralId':
					return new QQNode('plural_id', 'integer', $this);
				case 'ContextId':
					return new QQNode('context_id', 'integer', $this);
				case 'Context':
					return new QQNodeNarroContext('context_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'PluralForm':
					return new QQNode('plural_form', 'boolean', $this);
				case 'Active':
					return new QQNode('active', 'boolean', $this);
				case 'NarroContextPluralInfoAsPlural':
					return new QQReverseReferenceNodeNarroContextPluralInfo($this, 'narrocontextpluralinfoasplural', 'reverse_reference', 'plural_id');

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