<?php
	/**
	 * The abstract NarroSuggestionVoteGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroSuggestionVote subclass which
	 * extends this NarroSuggestionVoteGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroSuggestionVote class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * 
	 */
	class NarroSuggestionVoteGen extends QBaseClass {
		///////////////////////////////
		// COMMON LOAD METHODS
		///////////////////////////////

		/**
		 * Load a NarroSuggestionVote from PK Info
		 * @param integer $intSuggestionId
		 * @param integer $intTextId
		 * @param integer $intUserId
		 * @return NarroSuggestionVote
		 */
		public static function Load($intSuggestionId, $intTextId, $intUserId) {
			// Use QuerySingle to Perform the Query
			return NarroSuggestionVote::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $intSuggestionId),
				QQ::Equal(QQN::NarroSuggestionVote()->TextId, $intTextId),
				QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId)
				)
			);
		}

		/**
		 * Load all NarroSuggestionVotes
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			// Call NarroSuggestionVote::QueryArray to perform the LoadAll query
			try {
				return NarroSuggestionVote::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroSuggestionVotes
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroSuggestionVote::QueryCount to perform the CountAll query
			return NarroSuggestionVote::QueryCount(QQ::All());
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
			$objDatabase = NarroSuggestionVote::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroSuggestionVote-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_suggestion_vote');
			NarroSuggestionVote::GetSelectFields($objQueryBuilder);
			$objQueryBuilder->AddFromItem('`narro_suggestion_vote` AS `narro_suggestion_vote`');

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
		 * Static Qcodo Query method to query for a single NarroSuggestionVote object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroSuggestionVote the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroSuggestionVote::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroSuggestionVote object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroSuggestionVote::InstantiateDbRow($objDbResult->GetNextRow());
		}

		/**
		 * Static Qcodo Query method to query for an array of NarroSuggestionVote objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroSuggestionVote[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroSuggestionVote::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroSuggestionVote::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes);
		}

		/**
		 * Static Qcodo Query method to query for a count of NarroSuggestionVote objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroSuggestionVote::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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
			$objDatabase = NarroSuggestionVote::GetDatabase();

			// Lookup the QCache for This Query Statement
			$objCache = new QCache('query', 'narro_suggestion_vote_' . serialize($strConditions));
			if (!($strQuery = $objCache->GetData())) {
				// Not Found -- Go ahead and Create/Build out a new QueryBuilder object with NarroSuggestionVote-specific fields
				$objQueryBuilder = new QQueryBuilder($objDatabase);
				NarroSuggestionVote::GetSelectFields($objQueryBuilder);
				NarroSuggestionVote::GetFromFields($objQueryBuilder);

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
			return NarroSuggestionVote::InstantiateDbResult($objDbResult);
		}*/

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroSuggestionVote
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null) {
			if ($strPrefix) {
				$strTableName = '`' . $strPrefix . '`';
				$strAliasPrefix = '`' . $strPrefix . '__';
			} else {
				$strTableName = '`narro_suggestion_vote`';
				$strAliasPrefix = '`';
			}

			$objBuilder->AddSelectItem($strTableName . '.`suggestion_id` AS ' . $strAliasPrefix . 'suggestion_id`');
			$objBuilder->AddSelectItem($strTableName . '.`text_id` AS ' . $strAliasPrefix . 'text_id`');
			$objBuilder->AddSelectItem($strTableName . '.`user_id` AS ' . $strAliasPrefix . 'user_id`');
			$objBuilder->AddSelectItem($strTableName . '.`vote_value` AS ' . $strAliasPrefix . 'vote_value`');
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroSuggestionVote from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroSuggestionVote::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @return NarroSuggestionVote
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $objPreviousItem = null) {
			// If blank row, return null
			if (!$objDbRow)
				return null;


			// Create a new instance of the NarroSuggestionVote object
			$objToReturn = new NarroSuggestionVote();
			$objToReturn->__blnRestored = true;

			$objToReturn->intSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'suggestion_id', 'Integer');
			$objToReturn->__intSuggestionId = $objDbRow->GetColumn($strAliasPrefix . 'suggestion_id', 'Integer');
			$objToReturn->intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->__intTextId = $objDbRow->GetColumn($strAliasPrefix . 'text_id', 'Integer');
			$objToReturn->intUserId = $objDbRow->GetColumn($strAliasPrefix . 'user_id', 'Integer');
			$objToReturn->__intUserId = $objDbRow->GetColumn($strAliasPrefix . 'user_id', 'Integer');
			$objToReturn->intVoteValue = $objDbRow->GetColumn($strAliasPrefix . 'vote_value', 'Integer');

			// Instantiate Virtual Attributes
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				$strVirtualPrefix = $strAliasPrefix . '__';
				$strVirtualPrefixLength = strlen($strVirtualPrefix);
				if (substr($strColumnName, 0, $strVirtualPrefixLength) == $strVirtualPrefix)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_suggestion_vote__';

			// Check for Suggestion Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'suggestion_id__suggestion_id')))
				$objToReturn->objSuggestion = NarroSuggestion::InstantiateDbRow($objDbRow, $strAliasPrefix . 'suggestion_id__', $strExpandAsArrayNodes);

			// Check for Text Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'text_id__text_id')))
				$objToReturn->objText = NarroText::InstantiateDbRow($objDbRow, $strAliasPrefix . 'text_id__', $strExpandAsArrayNodes);

			// Check for User Early Binding
			if (!is_null($objDbRow->GetColumn($strAliasPrefix . 'user_id__user_id')))
				$objToReturn->objUser = NarroUser::InstantiateDbRow($objDbRow, $strAliasPrefix . 'user_id__', $strExpandAsArrayNodes);




			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroSuggestionVotes from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @return NarroSuggestionVote[]
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
					$objItem = NarroSuggestionVote::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objLastRowItem);
					if ($objItem) {
						array_push($objToReturn, $objItem);
						$objLastRowItem = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					array_push($objToReturn, NarroSuggestionVote::InstantiateDbRow($objDbRow));
			}

			return $objToReturn;
		}



		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////
			
		/**
		 * Load a single NarroSuggestionVote object,
		 * by SuggestionId, TextId, UserId Index(es)
		 * @param integer $intSuggestionId
		 * @param integer $intTextId
		 * @param integer $intUserId
		 * @return NarroSuggestionVote
		*/
		public static function LoadBySuggestionIdTextIdUserId($intSuggestionId, $intTextId, $intUserId) {
			return NarroSuggestionVote::QuerySingle(
				QQ::AndCondition(
				QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $intSuggestionId),
				QQ::Equal(QQN::NarroSuggestionVote()->TextId, $intTextId),
				QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId)
				)
			);
		}
			
		/**
		 * Load an array of NarroSuggestionVote objects,
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		*/
		public static function LoadArrayByTextId($intTextId, $objOptionalClauses = null) {
			// Call NarroSuggestionVote::QueryArray to perform the LoadArrayByTextId query
			try {
				return NarroSuggestionVote::QueryArray(
					QQ::Equal(QQN::NarroSuggestionVote()->TextId, $intTextId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroSuggestionVotes
		 * by TextId Index(es)
		 * @param integer $intTextId
		 * @return int
		*/
		public static function CountByTextId($intTextId) {
			// Call NarroSuggestionVote::QueryCount to perform the CountByTextId query
			return NarroSuggestionVote::QueryCount(
				QQ::Equal(QQN::NarroSuggestionVote()->TextId, $intTextId)
			);
		}
			
		/**
		 * Load an array of NarroSuggestionVote objects,
		 * by SuggestionId Index(es)
		 * @param integer $intSuggestionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		*/
		public static function LoadArrayBySuggestionId($intSuggestionId, $objOptionalClauses = null) {
			// Call NarroSuggestionVote::QueryArray to perform the LoadArrayBySuggestionId query
			try {
				return NarroSuggestionVote::QueryArray(
					QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $intSuggestionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroSuggestionVotes
		 * by SuggestionId Index(es)
		 * @param integer $intSuggestionId
		 * @return int
		*/
		public static function CountBySuggestionId($intSuggestionId) {
			// Call NarroSuggestionVote::QueryCount to perform the CountBySuggestionId query
			return NarroSuggestionVote::QueryCount(
				QQ::Equal(QQN::NarroSuggestionVote()->SuggestionId, $intSuggestionId)
			);
		}
			
		/**
		 * Load an array of NarroSuggestionVote objects,
		 * by UserId Index(es)
		 * @param integer $intUserId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroSuggestionVote[]
		*/
		public static function LoadArrayByUserId($intUserId, $objOptionalClauses = null) {
			// Call NarroSuggestionVote::QueryArray to perform the LoadArrayByUserId query
			try {
				return NarroSuggestionVote::QueryArray(
					QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroSuggestionVotes
		 * by UserId Index(es)
		 * @param integer $intUserId
		 * @return int
		*/
		public static function CountByUserId($intUserId) {
			// Call NarroSuggestionVote::QueryCount to perform the CountByUserId query
			return NarroSuggestionVote::QueryCount(
				QQ::Equal(QQN::NarroSuggestionVote()->UserId, $intUserId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////



		//////////////////
		// SAVE AND DELETE
		//////////////////

		/**
		 * Save this NarroSuggestionVote
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return void
		*/
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroSuggestionVote::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_suggestion_vote` (
							`suggestion_id`,
							`text_id`,
							`user_id`,
							`vote_value`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intSuggestionId) . ',
							' . $objDatabase->SqlVariable($this->intTextId) . ',
							' . $objDatabase->SqlVariable($this->intUserId) . ',
							' . $objDatabase->SqlVariable($this->intVoteValue) . '
						)
					');


				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_suggestion_vote`
						SET
							`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . ',
							`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ',
							`user_id` = ' . $objDatabase->SqlVariable($this->intUserId) . ',
							`vote_value` = ' . $objDatabase->SqlVariable($this->intVoteValue) . '
						WHERE
							`suggestion_id` = ' . $objDatabase->SqlVariable($this->__intSuggestionId) . ' AND
							`text_id` = ' . $objDatabase->SqlVariable($this->__intTextId) . ' AND
							`user_id` = ' . $objDatabase->SqlVariable($this->__intUserId) . '
					');
				}

			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Update __blnRestored and any Non-Identity PK Columns (if applicable)
			$this->__blnRestored = true;
			$this->__intSuggestionId = $this->intSuggestionId;
			$this->__intTextId = $this->intTextId;
			$this->__intUserId = $this->intUserId;


			// Return 
			return $mixToReturn;
		}

				/**
		 * Delete this NarroSuggestionVote
		 * @return void
		*/
		public function Delete() {
			if ((is_null($this->intSuggestionId)) || (is_null($this->intTextId)) || (is_null($this->intUserId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroSuggestionVote with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroSuggestionVote::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`
				WHERE
					`suggestion_id` = ' . $objDatabase->SqlVariable($this->intSuggestionId) . ' AND
					`text_id` = ' . $objDatabase->SqlVariable($this->intTextId) . ' AND
					`user_id` = ' . $objDatabase->SqlVariable($this->intUserId) . '');
		}

		/**
		 * Delete all NarroSuggestionVotes
		 * @return void
		*/
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroSuggestionVote::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_suggestion_vote`');
		}

		/**
		 * Truncate narro_suggestion_vote table
		 * @return void
		*/
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroSuggestionVote::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_suggestion_vote`');
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
					 * Gets the value for intSuggestionId (PK)
					 * @return integer
					 */
					return $this->intSuggestionId;

				case 'TextId':
					/**
					 * Gets the value for intTextId (PK)
					 * @return integer
					 */
					return $this->intTextId;

				case 'UserId':
					/**
					 * Gets the value for intUserId (PK)
					 * @return integer
					 */
					return $this->intUserId;

				case 'VoteValue':
					/**
					 * Gets the value for intVoteValue (Not Null)
					 * @return integer
					 */
					return $this->intVoteValue;


				///////////////////
				// Member Objects
				///////////////////
				case 'Suggestion':
					/**
					 * Gets the value for the NarroSuggestion object referenced by intSuggestionId (PK)
					 * @return NarroSuggestion
					 */
					try {
						if ((!$this->objSuggestion) && (!is_null($this->intSuggestionId)))
							$this->objSuggestion = NarroSuggestion::Load($this->intSuggestionId);
						return $this->objSuggestion;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Text':
					/**
					 * Gets the value for the NarroText object referenced by intTextId (PK)
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

				case 'User':
					/**
					 * Gets the value for the NarroUser object referenced by intUserId (PK)
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
				case 'SuggestionId':
					/**
					 * Sets the value for intSuggestionId (PK)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objSuggestion = null;
						return ($this->intSuggestionId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'TextId':
					/**
					 * Sets the value for intTextId (PK)
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

				case 'UserId':
					/**
					 * Sets the value for intUserId (PK)
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

				case 'VoteValue':
					/**
					 * Sets the value for intVoteValue (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						return ($this->intVoteValue = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
				case 'Suggestion':
					/**
					 * Sets the value for the NarroSuggestion object referenced by intSuggestionId (PK)
					 * @param NarroSuggestion $mixValue
					 * @return NarroSuggestion
					 */
					if (is_null($mixValue)) {
						$this->intSuggestionId = null;
						$this->objSuggestion = null;
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
							throw new QCallerException('Unable to set an unsaved Suggestion for this NarroSuggestionVote');

						// Update Local Member Variables
						$this->objSuggestion = $mixValue;
						$this->intSuggestionId = $mixValue->SuggestionId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'Text':
					/**
					 * Sets the value for the NarroText object referenced by intTextId (PK)
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
							throw new QCallerException('Unable to set an unsaved Text for this NarroSuggestionVote');

						// Update Local Member Variables
						$this->objText = $mixValue;
						$this->intTextId = $mixValue->TextId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'User':
					/**
					 * Sets the value for the NarroUser object referenced by intUserId (PK)
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
							throw new QCallerException('Unable to set an unsaved User for this NarroSuggestionVote');

						// Update Local Member Variables
						$this->objUser = $mixValue;
						$this->intUserId = $mixValue->UserId;

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
		 * Protected member variable that maps to the database PK column narro_suggestion_vote.suggestion_id
		 * @var integer intSuggestionId
		 */
		protected $intSuggestionId;
		const SuggestionIdDefault = null;


		/**
		 * Protected internal member variable that stores the original version of the PK column value (if restored)
		 * Used by Save() to update a PK column during UPDATE
		 * @var integer __intSuggestionId;
		 */
		protected $__intSuggestionId;

		/**
		 * Protected member variable that maps to the database PK column narro_suggestion_vote.text_id
		 * @var integer intTextId
		 */
		protected $intTextId;
		const TextIdDefault = null;


		/**
		 * Protected internal member variable that stores the original version of the PK column value (if restored)
		 * Used by Save() to update a PK column during UPDATE
		 * @var integer __intTextId;
		 */
		protected $__intTextId;

		/**
		 * Protected member variable that maps to the database PK column narro_suggestion_vote.user_id
		 * @var integer intUserId
		 */
		protected $intUserId;
		const UserIdDefault = null;


		/**
		 * Protected internal member variable that stores the original version of the PK column value (if restored)
		 * Used by Save() to update a PK column during UPDATE
		 * @var integer __intUserId;
		 */
		protected $__intUserId;

		/**
		 * Protected member variable that maps to the database column narro_suggestion_vote.vote_value
		 * @var integer intVoteValue
		 */
		protected $intVoteValue;
		const VoteValueDefault = null;


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
		 * in the database column narro_suggestion_vote.suggestion_id.
		 *
		 * NOTE: Always use the Suggestion property getter to correctly retrieve this NarroSuggestion object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroSuggestion objSuggestion
		 */
		protected $objSuggestion;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_suggestion_vote.text_id.
		 *
		 * NOTE: Always use the Text property getter to correctly retrieve this NarroText object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroText objText
		 */
		protected $objText;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_suggestion_vote.user_id.
		 *
		 * NOTE: Always use the User property getter to correctly retrieve this NarroUser object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroUser objUser
		 */
		protected $objUser;






		////////////////////////////////////////
		// METHODS for WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroSuggestionVote"><sequence>';
			$strToReturn .= '<element name="Suggestion" type="xsd1:NarroSuggestion"/>';
			$strToReturn .= '<element name="Text" type="xsd1:NarroText"/>';
			$strToReturn .= '<element name="User" type="xsd1:NarroUser"/>';
			$strToReturn .= '<element name="VoteValue" type="xsd:int"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroSuggestionVote', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroSuggestionVote'] = NarroSuggestionVote::GetSoapComplexTypeXml();
				NarroSuggestion::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroText::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroUser::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroSuggestionVote::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroSuggestionVote();
			if ((property_exists($objSoapObject, 'Suggestion')) &&
				($objSoapObject->Suggestion))
				$objToReturn->Suggestion = NarroSuggestion::GetObjectFromSoapObject($objSoapObject->Suggestion);
			if ((property_exists($objSoapObject, 'Text')) &&
				($objSoapObject->Text))
				$objToReturn->Text = NarroText::GetObjectFromSoapObject($objSoapObject->Text);
			if ((property_exists($objSoapObject, 'User')) &&
				($objSoapObject->User))
				$objToReturn->User = NarroUser::GetObjectFromSoapObject($objSoapObject->User);
			if (property_exists($objSoapObject, 'VoteValue'))
				$objToReturn->intVoteValue = $objSoapObject->VoteValue;
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroSuggestionVote::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objSuggestion)
				$objObject->objSuggestion = NarroSuggestion::GetSoapObjectFromObject($objObject->objSuggestion, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intSuggestionId = null;
			if ($objObject->objText)
				$objObject->objText = NarroText::GetSoapObjectFromObject($objObject->objText, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intTextId = null;
			if ($objObject->objUser)
				$objObject->objUser = NarroUser::GetSoapObjectFromObject($objObject->objUser, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intUserId = null;
			return $objObject;
		}
	}





	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCODO QUERY
	/////////////////////////////////////

	class QQNodeNarroSuggestionVote extends QQNode {
		protected $strTableName = 'narro_suggestion_vote';
		protected $strPrimaryKey = 'suggestion_id';
		protected $strClassName = 'NarroSuggestionVote';
		public function __get($strName) {
			switch ($strName) {
				case 'SuggestionId':
					return new QQNode('suggestion_id', 'integer', $this);
				case 'Suggestion':
					return new QQNodeNarroSuggestion('suggestion_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'UserId':
					return new QQNode('user_id', 'integer', $this);
				case 'User':
					return new QQNodeNarroUser('user_id', 'integer', $this);
				case 'VoteValue':
					return new QQNode('vote_value', 'integer', $this);

				case '_PrimaryKeyNode':
					return new QQNodeNarroSuggestion('suggestion_id', 'integer', $this);
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

	class QQReverseReferenceNodeNarroSuggestionVote extends QQReverseReferenceNode {
		protected $strTableName = 'narro_suggestion_vote';
		protected $strPrimaryKey = 'suggestion_id';
		protected $strClassName = 'NarroSuggestionVote';
		public function __get($strName) {
			switch ($strName) {
				case 'SuggestionId':
					return new QQNode('suggestion_id', 'integer', $this);
				case 'Suggestion':
					return new QQNodeNarroSuggestion('suggestion_id', 'integer', $this);
				case 'TextId':
					return new QQNode('text_id', 'integer', $this);
				case 'Text':
					return new QQNodeNarroText('text_id', 'integer', $this);
				case 'UserId':
					return new QQNode('user_id', 'integer', $this);
				case 'User':
					return new QQNodeNarroUser('user_id', 'integer', $this);
				case 'VoteValue':
					return new QQNode('vote_value', 'integer', $this);

				case '_PrimaryKeyNode':
					return new QQNodeNarroSuggestion('suggestion_id', 'integer', $this);
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