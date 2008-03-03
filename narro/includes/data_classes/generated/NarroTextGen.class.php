<?php
	/**
	 * The abstract NarroTextGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroText subclass which
	 * extends this NarroTextGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroText class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroTextGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroText from PK Info
		 * @param integer $intTextId
		 * @return NarroText
		 */
		public static function Load($intTextId) {
			// Use QuerySingle to Perform the Query
			return NarroText::QuerySingle(
				QQ::Equal(QQN::NarroText()->TextId, $intTextId)
			);
		}

		/**
		 * Load all NarroTexts
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroText[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroText::QueryArray to perform the LoadAll query
			try {
				return NarroText::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroTexts
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroText::QueryCount to perform the CountAll query
			return NarroText::QueryCount(QQ::All());
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
			$objDatabase = NarroText::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroText-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_text');
			NarroText::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_text` AS `narro_text`');

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
		 * Static Qcodo Query method to query for a single NarroText object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroText the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroText::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroText object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroText::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroText objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroText[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroText::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroText::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroText objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroText::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroText::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_text_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroText-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroText::GetSelectFields($objQueryBuilder);
				NarroText::GetFromFields($objQueryBuilder);

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
			return NarroText::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroText
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_text`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_value` AS ' . $strAliasPrefix . 'text_value`');
			$objBuilder->AddSelectItem($strTableName . '.`text_value_md5` AS ' . $strAliasPrefix . 'text_value_md5`');
			$objBuilder->AddSelectItem($strTableName . '.`text_char_count` AS ' . $strAliasPrefix . 'text_char_count`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroText from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroText::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroText
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;

			// See if we're doing an array expansion on the previous item
			if (($strExpandAsArrayNodes) && ($objPreviousItem) &&
				($objPreviousItem->intTextId == $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer'))) {

				// We are.  Now, prepare to check for ExpandAsArray clauses
				$blnExpandedViaArray = false;
				if (!$strAliasPrefix)
					$strAliasPrefix = 'narro_text__';


				if ((array_key_exists($strAliasPrefix . 'narrosuggestionvoteastext__suggestion_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestionvoteastext__suggestion_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroSuggestionVoteAsTextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroSuggestionVoteAsTextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteastext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroSuggestionVoteAsTextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroSuggestionVoteAsTextArray, NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteastext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextastext__context_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextastext__context_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextAsTextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextAsTextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextastext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextAsTextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextAsTextArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextastext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextcontextpluralastext__plural_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralastext__plural_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextContextPluralAsTextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextContextPluralAsTextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralastext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextContextPluralAsTextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextContextPluralAsTextArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralastext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				if ((array_key_exists($strAliasPrefix . 'narrotextsuggestionastext__suggestion_id', $strExpandAsArrayNodes)) &&
					(!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextsuggestionastext__suggestion_id')))) {
					if ($intPreviousChildItemCount = count($objPreviousItem->_objNarroTextSuggestionAsTextArray)) {
						$objPreviousChildItem = $objPreviousItem->_objNarroTextSuggestionAsTextArray[$intPreviousChildItemCount - 1];
						$objChildItem = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionastext__', $strExpandAsArrayNodes, $objPreviousChildItem);
						if ($objChildItem)
							array_push($objPreviousItem->_objNarroTextSuggestionAsTextArray, $objChildItem);
					} else
						array_push($objPreviousItem->_objNarroTextSuggestionAsTextArray, NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionastext__', $strExpandAsArrayNodes));
					$blnExpandedViaArray = true;
				}

				// Either return false to signal array expansion, or check-to-reset the Alias prefix and move on
				if ($blnExpandedViaArray)
					return false;
				else if ($strAliasPrefix == 'narro_text__')
					$strAliasPrefix = null;
			}

			// Create a new instance of the NarroText object
			$objToReturn = new NarroText();
			$objToReturn->__blnRestored = true;

			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->strTextValue = $objDbRow->GetColumn($strAliasPrefix . 'text_value', 'Blob');
			$objToReturn->strTextValueMd5 = $objDbRow->GetColumn($strAliasPrefix . 'text_value_md5', 'VarChar');
			$objToReturn->intTextCharCount = $objDbRow->GetColumn($strAliasPrefix . 'text_char_count', 'Integer');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_text__';




			// Check for NarroSuggestionVoteAsText Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrosuggestionvoteastext__suggestion_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrosuggestionvoteastext__suggestion_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroSuggestionVoteAsTextArray, NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteastext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroSuggestionVoteAsText = NarroSuggestionVote::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrosuggestionvoteastext__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextAsText Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextastext__context_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextastext__context_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextAsTextArray, NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextastext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextAsText = NarroTextContext::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextastext__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextContextPluralAsText Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextcontextpluralastext__plural_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextcontextpluralastext__plural_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextContextPluralAsTextArray, NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralastext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextContextPluralAsText = NarroTextContextPlural::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextcontextpluralastext__', $strExpandAsArrayNodes);
			}

			// Check for NarroTextSuggestionAsText Virtual Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'narrotextsuggestionastext__suggestion_id'))) {
				if (($strExpandAsArrayNodes) && (array_key_exists($strAliasPrefix . 'narrotextsuggestionastext__suggestion_id', $strExpandAsArrayNodes)))
					array_push($objToReturn->_objNarroTextSuggestionAsTextArray, NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionastext__', $strExpandAsArrayNodes));
				else
					$objToReturn->_objNarroTextSuggestionAsText = NarroTextSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'narrotextsuggestionastext__', $strExpandAsArrayNodes);
			}

			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroTexts from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroText[]
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
					$objItem = NarroText::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroText::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroText object,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return NarroText
		*/
		public static function LoadByTextId($intTextId) {
			return NarroText::QuerySingle(
				QQ::Equal(QQN::NarroText()->TextId, $intTextId)
			);
		}
			
		/**
		 * Load a single NarroText object,
		 * by TextValueMd5 Index(es)
		 * @param string $strTextValueMd5
		 * @return NarroText
		*/
		public static function LoadByTextValueMd5($strTextValueMd5) {
			return NarroText::QuerySingle(
				QQ::Equal(QQN::NarroText()->TextValueMd5, $strTextValueMd5)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroText
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_text` (
							`text_value`,
							`text_value_md5`,
							`text_char_count`
						) VALUES (
							' . $objDatabase->SqlVariable($this->strTextValue) . ',
							' . $objDatabase->SqlVariable($this->strTextValueMd5) . ',
							' . $objDatabase->SqlVariable($this->intTextCharCount) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intTextId = $objDatabase->InsertId('narro_text', 'text_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_text`
						SET
							`text_value` = ' . $objDatabase->SqlVariable($this->strTextValue) . ',
							`text_value_md5` = ' . $objDatabase->SqlVariable($this->strTextValueMd5) . ',
							`text_char_count` = ' . $objDatabase->SqlVariable($this->intTextCharCount) . '
						WHERE
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
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
		 * Delete this NarroText
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroText with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text`
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '');
		}

		/**
		 * Delete all NarroTexts
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text`');
		}

		/**
		 * Truncate narro_text table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_text`');
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
				case 'TextId':
					/**
					 * Gets the value for intTextId (Read-Only PK)
					 * @return integer
					 */
					return $this->intTextId;

				case 'TextValue':
					/**
					 * Gets the value for strTextValue (Not Null)
					 * @return string
					 */
					return $this->strTextValue;

				case 'TextValueMd5':
					/**
					 * Gets the value for strTextValueMd5 (Unique)
					 * @return string
					 */
					return $this->strTextValueMd5;

				case 'TextCharCount':
					/**
					 * Gets the value for intTextCharCount (Not Null)
					 * @return integer
					 */
					return $this->intTextCharCount;


				///////////////////
				// Member Objects
				///////////////////

				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////

				case '_NarroSuggestionVoteAsText':
					/**
					 * Gets the value for the private _objNarroSuggestionVoteAsText (Read-Only)
					 * if set due to an expansion on the narro_suggestion_vote.text_id reverse relationship
					 * @return NarroSuggestionVote
					 */
					return $this->_objNarroSuggestionVoteAsText;

				case '_NarroSuggestionVoteAsTextArray':
					/**
					 * Gets the value for the private _objNarroSuggestionVoteAsTextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_suggestion_vote.text_id reverse relationship
					 * @return NarroSuggestionVote[]
					 */
					return (array) $this->_objNarroSuggestionVoteAsTextArray;

				case '_NarroTextContextAsText':
					/**
					 * Gets the value for the private _objNarroTextContextAsText (Read-Only)
					 * if set due to an expansion on the narro_text_context.text_id reverse relationship
					 * @return NarroTextContext
					 */
					return $this->_objNarroTextContextAsText;

				case '_NarroTextContextAsTextArray':
					/**
					 * Gets the value for the private _objNarroTextContextAsTextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context.text_id reverse relationship
					 * @return NarroTextContext[]
					 */
					return (array) $this->_objNarroTextContextAsTextArray;

				case '_NarroTextContextPluralAsText':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsText (Read-Only)
					 * if set due to an expansion on the narro_text_context_plural.text_id reverse relationship
					 * @return NarroTextContextPlural
					 */
					return $this->_objNarroTextContextPluralAsText;

				case '_NarroTextContextPluralAsTextArray':
					/**
					 * Gets the value for the private _objNarroTextContextPluralAsTextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_context_plural.text_id reverse relationship
					 * @return NarroTextContextPlural[]
					 */
					return (array) $this->_objNarroTextContextPluralAsTextArray;

				case '_NarroTextSuggestionAsText':
					/**
					 * Gets the value for the private _objNarroTextSuggestionAsText (Read-Only)
					 * if set due to an expansion on the narro_text_suggestion.text_id reverse relationship
					 * @return NarroTextSuggestion
					 */
					return $this->_objNarroTextSuggestionAsText;

				case '_NarroTextSuggestionAsTextArray':
					/**
					 * Gets the value for the private _objNarroTextSuggestionAsTextArray (Read-Only)
					 * if set due to an ExpandAsArray on the narro_text_suggestion.text_id reverse relationship
					 * @return NarroTextSuggestion[]
					 */
					return (array) $this->_objNarroTextSuggestionAsTextArray;

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
				case 'TextValue':
					/**
					 * Sets the value for strTextValue (Not Null)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strTextValue = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'TextValueMd5':
					/**
					 * Sets the value for strTextValueMd5 (Unique)
					 * @param string $mixValue
					 * @return string
					 */
					try {
						return ($this->strTextValueMd5 = QType::Cast($mixValue, QType::String));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'TextCharCount':
					/**
					 * Sets the value for intTextCharCount (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intTextCharCount = QType::Cast($mixValue, QType::Integer));
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

			
		
		// Related Objects' Methods for NarroSuggestionVoteAsText
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroSuggestionVotesAsText as an array of NarroSuggestionVote objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		*/ 
		public function GetNarroSuggestionVoteAsTextArray($objOptionalClauses = null) {
			if ((is_null($this->intTextId)))
				return array();

			try {
				return NarroSuggestionVote::LoadArrayByTextId($this->intTextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroSuggestionVotesAsText
		 * @return int
		*/ 
		public function CountNarroSuggestionVotesAsText() {
			if ((is_null($this->intTextId)))
				return 0;

			return NarroSuggestionVote::CountByTextId($this->intTextId);
		}

		/**
		 * Associates a NarroSuggestionVoteAsText
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function AssociateNarroSuggestionVoteAsText(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionVoteAsText on this unsaved NarroText.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroSuggestionVoteAsText on this NarroText with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . '
			');
		}

		/**
		 * Unassociates a NarroSuggestionVoteAsText
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function UnassociateNarroSuggestionVoteAsText(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this unsaved NarroText.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this NarroText with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`text_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Unassociates all NarroSuggestionVotesAsText
		 * @return void
		*/ 
		public function UnassociateAllNarroSuggestionVotesAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_suggestion_vote`
				SET
					`text_id` = null
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes an associated NarroSuggestionVoteAsText
		 * @param NarroSuggestionVote $objNarroSuggestionVote
		 * @return void
		*/ 
		public function DeleteAssociatedNarroSuggestionVoteAsText(NarroSuggestionVote $objNarroSuggestionVote) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this unsaved NarroText.');
			if ((is_null($objNarroSuggestionVote->SuggestionId)) || (is_null($objNarroSuggestionVote->TextId)) || (is_null($objNarroSuggestionVote->UserId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this NarroText with an unsaved NarroSuggestionVote.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->TextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($objNarroSuggestionVote->UserId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes all associated NarroSuggestionVotesAsText
		 * @return void
		*/ 
		public function DeleteAllNarroSuggestionVotesAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroSuggestionVoteAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextAsText
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextsAsText as an array of NarroTextContext objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContext[]
		*/ 
		public function GetNarroTextContextAsTextArray($objOptionalClauses = null) {
			if ((is_null($this->intTextId)))
				return array();

			try {
				return NarroTextContext::LoadArrayByTextId($this->intTextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextsAsText
		 * @return int
		*/ 
		public function CountNarroTextContextsAsText() {
			if ((is_null($this->intTextId)))
				return 0;

			return NarroTextContext::CountByTextId($this->intTextId);
		}

		/**
		 * Associates a NarroTextContextAsText
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function AssociateNarroTextContextAsText(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextAsText on this NarroText with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextAsText
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function UnassociateNarroTextContextAsText(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this NarroText with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`text_id` = null
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextsAsText
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context`
				SET
					`text_id` = null
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextAsText
		 * @param NarroTextContext $objNarroTextContext
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextAsText(NarroTextContext $objNarroTextContext) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContext->ContextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this NarroText with an unsaved NarroTextContext.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`context_id` = ' . $objDatabase->SqlVariable($objNarroTextContext->ContextId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextsAsText
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context`
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextContextPluralAsText
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextContextPluralsAsText as an array of NarroTextContextPlural objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextContextPlural[]
		*/ 
		public function GetNarroTextContextPluralAsTextArray($objOptionalClauses = null) {
			if ((is_null($this->intTextId)))
				return array();

			try {
				return NarroTextContextPlural::LoadArrayByTextId($this->intTextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextContextPluralsAsText
		 * @return int
		*/ 
		public function CountNarroTextContextPluralsAsText() {
			if ((is_null($this->intTextId)))
				return 0;

			return NarroTextContextPlural::CountByTextId($this->intTextId);
		}

		/**
		 * Associates a NarroTextContextPluralAsText
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function AssociateNarroTextContextPluralAsText(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextContextPluralAsText on this NarroText with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . '
			');
		}

		/**
		 * Unassociates a NarroTextContextPluralAsText
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function UnassociateNarroTextContextPluralAsText(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this NarroText with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`text_id` = null
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Unassociates all NarroTextContextPluralsAsText
		 * @return void
		*/ 
		public function UnassociateAllNarroTextContextPluralsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_context_plural`
				SET
					`text_id` = null
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextContextPluralAsText
		 * @param NarroTextContextPlural $objNarroTextContextPlural
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextContextPluralAsText(NarroTextContextPlural $objNarroTextContextPlural) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextContextPlural->PluralId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this NarroText with an unsaved NarroTextContextPlural.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`plural_id` = ' . $objDatabase->SqlVariable($objNarroTextContextPlural->PluralId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextContextPluralsAsText
		 * @return void
		*/ 
		public function DeleteAllNarroTextContextPluralsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextContextPluralAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_context_plural`
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

			
		
		// Related Objects' Methods for NarroTextSuggestionAsText
		//-------------------------------------------------------------------

		/**
		 * Gets all associated NarroTextSuggestionsAsText as an array of NarroTextSuggestion objects
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroTextSuggestion[]
		*/ 
		public function GetNarroTextSuggestionAsTextArray($objOptionalClauses = null) {
			if ((is_null($this->intTextId)))
				return array();

			try {
				return NarroTextSuggestion::LoadArrayByTextId($this->intTextId, $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Counts all associated NarroTextSuggestionsAsText
		 * @return int
		*/ 
		public function CountNarroTextSuggestionsAsText() {
			if ((is_null($this->intTextId)))
				return 0;

			return NarroTextSuggestion::CountByTextId($this->intTextId);
		}

		/**
		 * Associates a NarroTextSuggestionAsText
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function AssociateNarroTextSuggestionAsText(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextSuggestionAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call AssociateNarroTextSuggestionAsText on this NarroText with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . '
			');
		}

		/**
		 * Unassociates a NarroTextSuggestionAsText
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function UnassociateNarroTextSuggestionAsText(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this NarroText with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`text_id` = null
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Unassociates all NarroTextSuggestionsAsText
		 * @return void
		*/ 
		public function UnassociateAllNarroTextSuggestionsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				UPDATE
					`narro_text_suggestion`
				SET
					`text_id` = null
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes an associated NarroTextSuggestionAsText
		 * @param NarroTextSuggestion $objNarroTextSuggestion
		 * @return void
		*/ 
		public function DeleteAssociatedNarroTextSuggestionAsText(NarroTextSuggestion $objNarroTextSuggestion) {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this unsaved NarroText.');
			if ((is_null($objNarroTextSuggestion->SuggestionId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this NarroText with an unsaved NarroTextSuggestion.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($objNarroTextSuggestion->SuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}

		/**
		 * Deletes all associated NarroTextSuggestionsAsText
		 * @return void
		*/ 
		public function DeleteAllNarroTextSuggestionsAsText() {
			if ((is_null($this->intTextId)))
				throw new QUndefinedPrimaryKeyException('Unable to call UnassociateNarroTextSuggestionAsText on this unsaved NarroText.');

			// Get the Database Object for this Class
			$objDatabase = NarroText::GetDatabase();

			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_text_suggestion`
				WHERE
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . '
			');
		}




		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////
		
		/**
		 * Protected member variable that maps to the database PK Identity column narro_text.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text.text_value
		 * @var string strTextValue
		 */
		protected $strTextValue;
		const TextValueDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_text.text_value_md5
		 * @var string strTextValueMd5
		 */
		protected $strTextValueMd5;
		const TextValueMd5MaxLength = 64;
		const TextValueMd5Default = null;


		/**
		 * Protected member variable that maps to the database column narro_text.text_char_count
		 * @var integer intTextCharCount
		 */
		protected $intTextCharCount;
		const TextCharCountDefault = null;


		/**
		 * Private member variable that stores a reference to a single NarroSuggestionVoteAsText object
		 * (of type NarroSuggestionVote), if this NarroText object was restored with
		 * an expansion on the narro_suggestion_vote association table.
		 * @var NarroSuggestionVote _objNarroSuggestionVoteAsText;
		 */
		private $_objNarroSuggestionVoteAsText;

		/**
		 * Private member variable that stores a reference to an array of NarroSuggestionVoteAsText objects
		 * (of type NarroSuggestionVote[]), if this NarroText object was restored with
		 * an ExpandAsArray on the narro_suggestion_vote association table.
		 * @var NarroSuggestionVote[] _objNarroSuggestionVoteAsTextArray;
		 */
		private $_objNarroSuggestionVoteAsTextArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextAsText object
		 * (of type NarroTextContext), if this NarroText object was restored with
		 * an expansion on the narro_text_context association table.
		 * @var NarroTextContext _objNarroTextContextAsText;
		 */
		private $_objNarroTextContextAsText;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextAsText objects
		 * (of type NarroTextContext[]), if this NarroText object was restored with
		 * an ExpandAsArray on the narro_text_context association table.
		 * @var NarroTextContext[] _objNarroTextContextAsTextArray;
		 */
		private $_objNarroTextContextAsTextArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextContextPluralAsText object
		 * (of type NarroTextContextPlural), if this NarroText object was restored with
		 * an expansion on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural _objNarroTextContextPluralAsText;
		 */
		private $_objNarroTextContextPluralAsText;

		/**
		 * Private member variable that stores a reference to an array of NarroTextContextPluralAsText objects
		 * (of type NarroTextContextPlural[]), if this NarroText object was restored with
		 * an ExpandAsArray on the narro_text_context_plural association table.
		 * @var NarroTextContextPlural[] _objNarroTextContextPluralAsTextArray;
		 */
		private $_objNarroTextContextPluralAsTextArray = array();

		/**
		 * Private member variable that stores a reference to a single NarroTextSuggestionAsText object
		 * (of type NarroTextSuggestion), if this NarroText object was restored with
		 * an expansion on the narro_text_suggestion association table.
		 * @var NarroTextSuggestion _objNarroTextSuggestionAsText;
		 */
		private $_objNarroTextSuggestionAsText;

		/**
		 * Private member variable that stores a reference to an array of NarroTextSuggestionAsText objects
		 * (of type NarroTextSuggestion[]), if this NarroText object was restored with
		 * an ExpandAsArray on the narro_text_suggestion association table.
		 * @var NarroTextSuggestion[] _objNarroTextSuggestionAsTextArray;
		 */
		private $_objNarroTextSuggestionAsTextArray = array();

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
			$strToReturn = '<complexType name="NarroText"><sequence>';
			$strToReturn .= '<element name="TextId" type="xsd:int"/>';
			$strToReturn .= '<element name="TextValue" type="xsd:string"/>';
			$strToReturn .= '<element name="TextValueMd5" type="xsd:string"/>';
			$strToReturn .= '<element name="TextCharCount" type="xsd:int"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroText', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroText'] = NarroText::GetSoapComplexTypeXml();
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroText::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroText();
			if (property_exists($objSoapObject, 'TextId'))
				$objToReturn->intTextId = $objSoapObject->TextId;
			if (property_exists($objSoapObject, 'TextValue'))
				$objToReturn->strTextValue = $objSoapObject->TextValue;
			if (property_exists($objSoapObject, 'TextValueMd5'))
				$objToReturn->strTextValueMd5 = $objSoapObject->TextValueMd5;
			if (property_exists($objSoapObject, 'TextCharCount'))
				$objToReturn->intTextCharCount = $objSoapObject->TextCharCount;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroText::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroText extends QQNode {
		protected $strTableName = 'narro_text';
		protected $strPrimaryKey = 'text_id';
		protected $strClassName = 'NarroText';
		public function __get($strName) {
			switch ($strName) {
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'TextValue':
					return new QQNode('text_value', 'string', $this);
				case 'TextValueMd5':
					return new QQNode('text_value_md5', 'string', $this);
				case 'TextCharCount':
					return new QQNode('text_char_count', 'integer', $this);
				case 'NarroSuggestionVoteAsText':
					return new QQReverseReferenceNodeNarroSuggestionVote($this, 'narrosuggestionvoteastext', 'reverse_reference', 'text_id');
				case 'NarroTextContextAsText':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextastext', 'reverse_reference', 'text_id');
				case 'NarroTextContextPluralAsText':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralastext', 'reverse_reference', 'text_id');
				case 'NarroTextSuggestionAsText':
					return new QQReverseReferenceNodeNarroTextSuggestion($this, 'narrotextsuggestionastext', 'reverse_reference', 'text_id');

				case '_PrimaryKeyNode':
					return new QQNode('text_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroText extends QQReverseReferenceNode {
		protected $strTableName = 'narro_text';
		protected $strPrimaryKey = 'text_id';
		protected $strClassName = 'NarroText';
		public function __get($strName) {
			switch ($strName) {
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'TextValue':
					return new QQNode('text_value', 'string', $this);
				case 'TextValueMd5':
					return new QQNode('text_value_md5', 'string', $this);
				case 'TextCharCount':
					return new QQNode('text_char_count', 'integer', $this);
				case 'NarroSuggestionVoteAsText':
					return new QQReverseReferenceNodeNarroSuggestionVote($this, 'narrosuggestionvoteastext', 'reverse_reference', 'text_id');
				case 'NarroTextContextAsText':
					return new QQReverseReferenceNodeNarroTextContext($this, 'narrotextcontextastext', 'reverse_reference', 'text_id');
				case 'NarroTextContextPluralAsText':
					return new QQReverseReferenceNodeNarroTextContextPlural($this, 'narrotextcontextpluralastext', 'reverse_reference', 'text_id');
				case 'NarroTextSuggestionAsText':
					return new QQReverseReferenceNodeNarroTextSuggestion($this, 'narrotextsuggestionastext', 'reverse_reference', 'text_id');

				case '_PrimaryKeyNode':
					return new QQNode('text_id', 'integer', $this);
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