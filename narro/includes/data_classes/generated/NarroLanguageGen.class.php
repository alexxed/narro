<?php
	/**
	 * The abstract NarroLanguageGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroLanguage subclass which
	 * extends this NarroLanguageGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroLanguage class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroLanguageGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroLanguage from PK Info
		 * @param integer $intLanguageId
		 * @return NarroLanguage
		 */
		public static function Load($intLanguageId) {
			// Use QuerySingle to Perform the Query
			return NarroLanguage::QuerySingle(
				QQ::Equal(QQN::NarroLanguage()->LanguageId, $intLanguageId)
			);
		}

		/**
		 * Load all NarroLanguages
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroLanguage[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroLanguage::QueryArray to perform the LoadAll query
			try {
				return NarroLanguage::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroLanguages
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroLanguage::QueryCount to perform the CountAll query
			return NarroLanguage::QueryCount(QQ::All());
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
			$objDatabase = NarroLanguage::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroLanguage-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_language');
			NarroLanguage::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_language` AS `narro_language`');

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
		 * Static Qcodo Query method to query for a single NarroLanguage object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroLanguage the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroLanguage::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroLanguage object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroLanguage::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroLanguage objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroLanguage[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroLanguage::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroLanguage::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroLanguage objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroLanguage::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroLanguage::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_language_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroLanguage-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroLanguage::GetSelectFields($objQueryBuilder);
				NarroLanguage::GetFromFields($objQueryBuilder);

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
			return NarroLanguage::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroLanguage
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_language`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`language_id` AS ' . $strAliasPrefix . 'language_id`');
			$objBuilder->AddSelectItem($strTableName . '.`language_name` AS ' . $strAliasPrefix . 'language_name`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroLanguage from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroLanguage::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroLanguage
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;

			// See if we're doing an array expansion on the previous item
			if (($strExpandAsArrayNodes) && ($objPreviousItem) &&
				($objPreviousItem->intLanguageId == $objDbRow->GetColumn($strAliasPrefix . 'language_id', 'Integer'))) {

				// We are.  Now, prepare to check for ExpandAsArray clauses
				$blnExpandedViaArray = false;
				if (!$strAliasPrefix)
					$strAliasPrefix = 'narro_language__';


				if ((array_key_exists($strAliasPrefix . 'narrotextsuggestionaslanguage__suggestion_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextsuggestionaslanguage__suggestion_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextSuggestionAsLanguageArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextSuggestionAsLanguageArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionaslanguage__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextSuggestionAsLanguageArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextSuggestionAsLanguageArray, NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionaslanguage__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				// Either return false to signal array expansion, or check-to-reset the Alias prefix and move on
				if ($blnExpandedViaArray)
					return false;
				else if ($strAliasPrefix == 'narro_language__')
					$strAliasPrefix = null;
			}

			// Create a new instance of the NarroLanguage object
			$objToReturn = new NarroLanguage();
			$objToReturn->__blnRestored = true;

			$objToReturn->intLanguageId = $objDbRow->GetColumn($strAliasPrefix . 'language_id', 'Integer');
			$objToReturn->strLanguageName = $objDbRow->GetColumn($strAliasPrefix . 'language_name', 'VarChar');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_language__';




			// Check for NarroTextSuggestionAsLanguage Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextsuggestionaslanguage__suggestion_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextsuggestionaslanguage__suggestion_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextSuggestionAsLanguageArray, NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionaslanguage__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextSuggestionAsLanguage = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionaslanguage__', $strExpandAsArrayNodes);
			}

			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroLanguages from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroLanguage[]
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
					$objItem = NarroLanguage::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroLanguage::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroLanguage object,
		 * by LanguageId Index(es)
		 * @param integer $intLanguageId
		 * @return NarroLanguage
		*/
		public static function LoadByLanguageId($intLanguageId) {
			return NarroLanguage::QuerySingle(
				QQ::Equal(QQN::NarroLanguage()->LanguageId, $intLanguageId)
			);
		}
			
		/**
		 * Load a single NarroLanguage object,
		 * by LanguageName Index(es)
		 * @param string $strLanguageName
		 * @return NarroLanguage
		*/
		public static function LoadByLanguageName($strLanguageName) {
			return NarroLanguage::QuerySingle(
				QQ::Equal(QQN::NarroLanguage()->LanguageName, $strLanguageName)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroLanguage
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_language` (
							`language_name`
						) VALUES (
							' . $objDatabase->SqlVariable($this->strLanguageName) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intLanguageId = $objDatabase->InsertId('narro_language', 'language_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_language`
						SET
							`language_name` = ' . $objDatabase->SqlVariable($this->strLanguageName) . '
						WHERE
							`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
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
		 * Delete this NarroLanguage
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroLanguage with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_language`
				WHERE
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '');
		}

		/**
		 * Delete all NarroLanguages
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_language`');
		}

		/**
		 * Truncate narro_language table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_language`');
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
				case 'LanguageId':
					/**
					 * Gets the value for intLanguageId (Read-Only PK)
					 * @return integer
					 */
					return $this->intLanguageId;

				case 'LanguageName':
					/**
					 * Gets the value for strLanguageName (Unique)
					 * @return string
					 */
					return $this->strLanguageName;


				///////////////////
				// Member Objects
				///////////////////

				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

				case '_NarroTextSuggestionAsLanguage':
					/**
					 * Gets the value for the private _objNarroTextSuggestionAsLanguage (Read-Only)
					 * if set due to an expansion on the narro_text_suggestion.language_id reverse relationship
					 * @return NarroTextSuggestion
					 */
					return $this->_objNarroTextSuggestionAsLanguage;

				case '_NarroTextSuggestionAsLanguageArray':
					/**
					 * Gets the value for the private _objNarroTextSuggestionAsLanguageArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_suggestion.language_id reverse relationship
					 * @return NarroTextSuggestion[]
					 */
					return (array) $this->_objNarroTextSuggestionAsLanguageArray;

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
				case 'LanguageName':
					/**
					 * Sets the value for strLanguageName (Unique)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strLanguageName = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
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

			
		
		// Related Objects' Methods for NarroTextSuggestionAsLanguage
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextSuggestionsAsLanguage as an array of NarroTextSuggestion objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		*/ 
		public function GetNarroTextSuggestionAsLanguageArray($objOptionalClauses = null) {
			if ((is_null($this->intLanguageId)))
				return array();

			try {
				return NarroTextSuggestion::LoadArrayByLanguageId($this->intLanguageId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextSuggestionsAsLanguage
		 * @return int
		*/ 
		public function CountNarroTextSuggestionsAsLanguage() {
			if ((is_null($this->intLanguageId)))
				return 0;

			return NarroTextSuggestion::CountByLanguageId($this->intLanguageId);
		}

		/**
		 * Associates a NarroTextSuggestionAsLanguage
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function AssociateNarroTextSuggestionAsLanguage(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextSuggestionAsLanguage on this unsaved NarroLanguage.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextSuggestionAsLanguage on this NarroLanguage with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . '
			');
		}

		/**
		 * Unassociates a NarroTextSuggestionAsLanguage
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function UnassociateNarroTextSuggestionAsLanguage(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this unsaved NarroLanguage.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this NarroLanguage with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`language_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . ' AND
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
			');
		}

		/**
		 * Unassociates all NarroTextSuggestionsAsLanguage
		 * @return void
		*/ 
		public function UnassociateAllNarroTextSuggestionsAsLanguage() {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this unsaved NarroLanguage.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`language_id` = null
				WHERE
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextSuggestionAsLanguage
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextSuggestionAsLanguage(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this unsaved NarroLanguage.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this NarroLanguage with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . ' AND
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextSuggestionsAsLanguage
		 * @return void
		*/ 
		public function DeleteAllNarroTextSuggestionsAsLanguage() {
			if ((is_null($this->intLanguageId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsLanguage on this unsaved NarroLanguage.');

			// Get the Database Object for this Class
			$objDatabase = NarroLanguage::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`
				WHERE
					`language_id` = ' . $objDatabase->SqlVariable($this->intLanguageId) . '
			');
		}




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_language.language_id
		 * @var integer intLanguageId
		 */
		protected $intLanguageId;
		const LanguageIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_language.language_name
		 * @var string strLanguageName
		 */
		protected $strLanguageName;
		const LanguageNameMaxLength = 128;
		const LanguageNameDefault = null;


		/**
		 * Private member variable that stores a reference to a single NarroTextSuggestionAsLanguage object
		 * (of type NarroTextSuggestion), if this NarroLanguage object was restored with
		 * an expansion on the narro_text_suggestion association table.
		 * @var NarroTextSuggestion _objNarroTextSuggestionAsLanguage;
		 */
		private $_objNarroTextSuggestionAsLanguage;

		/**
		 * Private member variable that stores a reference to an array of NarroTextSuggestionAsLanguage objects
		 * (of type NarroTextSuggestion[]), if this NarroLanguage object was restored with
		 * an ExpandAsArray on the narro_text_suggestion association table.
		 * @var NarroTextSuggestion[] _objNarroTextSuggestionAsLanguageArray;
		 */
		private $_objNarroTextSuggestionAsLanguageArray = array();

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






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroLanguage"><sequence>';
			$strToReturn .= '<element name="LanguageId" type="xsd:int"/>';
			$strToReturn .= '<element name="LanguageName" type="xsd:string"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroLanguage', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroLanguage'] = NarroLanguage::GetSoapComplexTypeXml();
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroLanguage::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroLanguage();
			if (property_exists($objSoapObject, 'LanguageId'))
				$objToReturn->intLanguageId = $objSoapObject->LanguageId;
			if (property_exists($objSoapObject, 'LanguageName'))
				$objToReturn->strLanguageName = $objSoapObject->LanguageName;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroLanguage::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroLanguage extends QQNode {
		protected $strTableName = 'narro_language';
		protected $strPrimaryKey = 'language_id';
		protected $strClassName = 'NarroLanguage';
		public function __get($strName) {
			switch ($strName) {
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'LanguageName':
					return new QQNode('language_name', 'string', $this);
				case 'NarroTextSuggestionAsLanguage':
					return new QQReverseReferenceNodeNarroTextSuggestion($this, 'narrotextsuggestionaslanguage', 'reverse_reference', 'language_id');

				case '_PrimaryKeyNode':
					return new QQNode('language_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroLanguage extends QQReverseReferenceNode {
		protected $strTableName = 'narro_language';
		protected $strPrimaryKey = 'language_id';
		protected $strClassName = 'NarroLanguage';
		public function __get($strName) {
			switch ($strName) {
				case 'LanguageId':
					return new QQNode('language_id', 'integer', $this);
				case 'LanguageName':
					return new QQNode('language_name', 'string', $this);
				case 'NarroTextSuggestionAsLanguage':
					return new QQReverseReferenceNodeNarroTextSuggestion($this, 'narrotextsuggestionaslanguage', 'reverse_reference', 'language_id');

				case '_PrimaryKeyNode':
					return new QQNode('language_id', 'integer', $this);
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