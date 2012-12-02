<?php
	/**
	 * The abstract NarroRolePermissionGen class defined here is
	 * code-generated and contains all the basic CRUD-type functionality as well as
	 * basic methods to handle relationships and index-based loading.
	 *
	 * To use, you should use the NarroRolePermission subclass which
	 * extends this NarroRolePermissionGen class.
	 *
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroRolePermission class.
	 *
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 * @property-read integer $RolePermissionId the value for intRolePermissionId (Read-Only PK)
	 * @property integer $RoleId the value for intRoleId (Not Null)
	 * @property integer $PermissionId the value for intPermissionId (Not Null)
	 * @property NarroRole $Role the value for the NarroRole object referenced by intRoleId (Not Null)
	 * @property NarroPermission $Permission the value for the NarroPermission object referenced by intPermissionId (Not Null)
	 * @property-read boolean $__Restored whether or not this object was restored from the database (as opposed to created new)
	 */
	class NarroRolePermissionGen extends QBaseClass implements IteratorAggregate {

		///////////////////////////////////////////////////////////////////////
		// PROTECTED MEMBER VARIABLES and TEXT FIELD MAXLENGTHS (if applicable)
		///////////////////////////////////////////////////////////////////////

		/**
		 * Protected member variable that maps to the database PK Identity column narro_role_permission.role_permission_id
		 * @var integer intRolePermissionId
		 */
		protected $intRolePermissionId;
		const RolePermissionIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_role_permission.role_id
		 * @var integer intRoleId
		 */
		protected $intRoleId;
		const RoleIdDefault = null;


		/**
		 * Protected member variable that maps to the database column narro_role_permission.permission_id
		 * @var integer intPermissionId
		 */
		protected $intPermissionId;
		const PermissionIdDefault = null;


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
		 * in the database column narro_role_permission.role_id.
		 *
		 * NOTE: Always use the Role property getter to correctly retrieve this NarroRole object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroRole objRole
		 */
		protected $objRole;

		/**
		 * Protected member variable that contains the object pointed by the reference
		 * in the database column narro_role_permission.permission_id.
		 *
		 * NOTE: Always use the Permission property getter to correctly retrieve this NarroPermission object.
		 * (Because this class implements late binding, this variable reference MAY be null.)
		 * @var NarroPermission objPermission
		 */
		protected $objPermission;



		/**
		 * Initialize each property with default values from database definition
		 */
		public function Initialize()
		{
			$this->intRolePermissionId = NarroRolePermission::RolePermissionIdDefault;
			$this->intRoleId = NarroRolePermission::RoleIdDefault;
			$this->intPermissionId = NarroRolePermission::PermissionIdDefault;
		}


		///////////////////////////////
		// CLASS-WIDE LOAD AND COUNT METHODS
		///////////////////////////////

		/**
		 * Static method to retrieve the Database object that owns this class.
		 * @return QDatabaseBase reference to the Database object that can query this class
		 */
		public static function GetDatabase() {
			return QApplication::$Database[1];
		}

		/**
		 * Load a NarroRolePermission from PK Info
		 * @param integer $intRolePermissionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroRolePermission
		 */
		public static function Load($intRolePermissionId, $objOptionalClauses = null) {
			$strCacheKey = false;
			if (QApplication::$objCacheProvider && !$objOptionalClauses && QApplication::$Database[1]->Caching) {
				$strCacheKey = QApplication::$objCacheProvider->CreateKey('narro', 'NarroRolePermission', $intRolePermissionId);
				$objCachedObject = QApplication::$objCacheProvider->Get($strCacheKey);
				if ($objCachedObject !== false) {
					return $objCachedObject;
				}
			}
			// Use QuerySingle to Perform the Query
			$objToReturn = NarroRolePermission::QuerySingle(
				QQ::AndCondition(
					QQ::Equal(QQN::NarroRolePermission()->RolePermissionId, $intRolePermissionId)
				),
				$objOptionalClauses
			);
			if ($strCacheKey !== false) {
				QApplication::$objCacheProvider->Set($strCacheKey, $objToReturn);
			}
			return $objToReturn;
		}

		/**
		 * Load all NarroRolePermissions
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroRolePermission[]
		 */
		public static function LoadAll($objOptionalClauses = null) {
			if (func_num_args() > 1) {
				throw new QCallerException("LoadAll must be called with an array of optional clauses as a single argument");
			}
			// Call NarroRolePermission::QueryArray to perform the LoadAll query
			try {
				return NarroRolePermission::QueryArray(QQ::All(), $objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count all NarroRolePermissions
		 * @return int
		 */
		public static function CountAll() {
			// Call NarroRolePermission::QueryCount to perform the CountAll query
			return NarroRolePermission::QueryCount(QQ::All());
		}




		///////////////////////////////
		// QCUBED QUERY-RELATED METHODS
		///////////////////////////////

		/**
		 * Internally called method to assist with calling Qcubed Query for this class
		 * on load methods.
		 * @param QQueryBuilder &$objQueryBuilder the QueryBuilder object that will be created
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause object or array of QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with (sending in null will skip the PrepareStatement step)
		 * @param boolean $blnCountOnly only select a rowcount
		 * @return string the query statement
		 */
		protected static function BuildQueryStatement(&$objQueryBuilder, QQCondition $objConditions, $objOptionalClauses, $mixParameterArray, $blnCountOnly) {
			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();

			// Create/Build out the QueryBuilder object with NarroRolePermission-specific SELET and FROM fields
			$objQueryBuilder = new QQueryBuilder($objDatabase, 'narro_role_permission');

			$blnAddAllFieldsToSelect = true;
			if ($objDatabase->OnlyFullGroupBy) {
				// see if we have any group by or aggregation clauses, if yes, don't add the fields to select clause
				if ($objOptionalClauses instanceof QQClause) {
					if ($objOptionalClauses instanceof QQAggregationClause || $objOptionalClauses instanceof QQGroupBy) {
						$blnAddAllFieldsToSelect = false;
					}
				} else if (is_array($objOptionalClauses)) {
					foreach ($objOptionalClauses as $objClause) {
						if ($objClause instanceof QQAggregationClause || $objClause instanceof QQGroupBy) {
							$blnAddAllFieldsToSelect = false;
							break;
						}
					}
				}
			}
			if ($blnAddAllFieldsToSelect) {
				NarroRolePermission::GetSelectFields($objQueryBuilder, null, QQuery::extractSelectClause($objOptionalClauses));
			}
			$objQueryBuilder->AddFromItem('narro_role_permission');

			// Set "CountOnly" option (if applicable)
			if ($blnCountOnly)
				$objQueryBuilder->SetCountOnlyFlag();

			// Apply Any Conditions
			if ($objConditions)
				try {
					$objConditions->UpdateQueryBuilder($objQueryBuilder);
				} catch (QCallerException $objExc) {
					$objExc->IncrementOffset();
					throw $objExc;
				}

			// Iterate through all the Optional Clauses (if any) and perform accordingly
			if ($objOptionalClauses) {
				if ($objOptionalClauses instanceof QQClause)
					$objOptionalClauses->UpdateQueryBuilder($objQueryBuilder);
				else if (is_array($objOptionalClauses))
					foreach ($objOptionalClauses as $objClause)
						$objClause->UpdateQueryBuilder($objQueryBuilder);
				else
					throw new QCallerException('Optional Clauses must be a QQClause object or an array of QQClause objects');
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
		 * Static Qcubed Query method to query for a single NarroRolePermission object.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroRolePermission the queried object
		 */
		public static function QuerySingle(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroRolePermission::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query, Get the First Row, and Instantiate a new NarroRolePermission object
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);

			// Do we have to expand anything?
			if ($objQueryBuilder->ExpandAsArrayNodes) {
				$objToReturn = array();
				while ($objDbRow = $objDbResult->GetNextRow()) {
					$objItem = NarroRolePermission::InstantiateDbRow($objDbRow, null, $objQueryBuilder->ExpandAsArrayNodes, $objToReturn, $objQueryBuilder->ColumnAliasArray);
					if ($objItem)
						$objToReturn[] = $objItem;
				}
				if (count($objToReturn)) {
					// Since we only want the object to return, lets return the object and not the array.
					return $objToReturn[0];
				} else {
					return null;
				}
			} else {
				// No expands just return the first row
				$objDbRow = $objDbResult->GetNextRow();
				if(null === $objDbRow)
					return null;
				return NarroRolePermission::InstantiateDbRow($objDbRow, null, null, null, $objQueryBuilder->ColumnAliasArray);
			}
		}

		/**
		 * Static Qcubed Query method to query for an array of NarroRolePermission objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return NarroRolePermission[] the queried objects as an array
		 */
		public static function QueryArray(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroRolePermission::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the Query and Instantiate the Array Result
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);
			return NarroRolePermission::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes, $objQueryBuilder->ColumnAliasArray);
		}

		/**
		 * Static Qcodo query method to issue a query and get a cursor to progressively fetch its results.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return QDatabaseResultBase the cursor resource instance
		 */
		public static function QueryCursor(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the query statement
			try {
				$strQuery = NarroRolePermission::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Perform the query
			$objDbResult = $objQueryBuilder->Database->Query($strQuery);

			// Return the results cursor
			$objDbResult->QueryBuilder = $objQueryBuilder;
			return $objDbResult;
		}

		/**
		 * Static Qcubed Query method to query for a count of NarroRolePermission objects.
		 * Uses BuildQueryStatment to perform most of the work.
		 * @param QQCondition $objConditions any conditions on the query, itself
		 * @param QQClause[] $objOptionalClausees additional optional QQClause objects for this query
		 * @param mixed[] $mixParameterArray a array of name-value pairs to perform PrepareStatement with
		 * @return integer the count of queried objects as an integer
		 */
		public static function QueryCount(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null) {
			// Get the Query Statement
			try {
				$strQuery = NarroRolePermission::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, true);
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

		public static function QueryArrayCached(QQCondition $objConditions, $objOptionalClauses = null, $mixParameterArray = null, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();

			$strQuery = NarroRolePermission::BuildQueryStatement($objQueryBuilder, $objConditions, $objOptionalClauses, $mixParameterArray, false);

			$objCache = new QCache('qquery/narrorolepermission', $strQuery);
			$cacheData = $objCache->GetData();

			if (!$cacheData || $blnForceUpdate) {
				$objDbResult = $objQueryBuilder->Database->Query($strQuery);
				$arrResult = NarroRolePermission::InstantiateDbResult($objDbResult, $objQueryBuilder->ExpandAsArrayNodes, $objQueryBuilder->ColumnAliasArray);
				$objCache->SaveData(serialize($arrResult));
			} else {
				$arrResult = unserialize($cacheData);
			}

			return $arrResult;
		}

		/**
		 * Updates a QQueryBuilder with the SELECT fields for this NarroRolePermission
		 * @param QQueryBuilder $objBuilder the Query Builder object to update
		 * @param string $strPrefix optional prefix to add to the SELECT fields
		 */
		public static function GetSelectFields(QQueryBuilder $objBuilder, $strPrefix = null, QQSelect $objSelect = null) {
			if ($strPrefix) {
				$strTableName = $strPrefix;
				$strAliasPrefix = $strPrefix . '__';
			} else {
				$strTableName = 'narro_role_permission';
				$strAliasPrefix = '';
			}

            if ($objSelect) {
			    $objBuilder->AddSelectItem($strTableName, 'role_permission_id', $strAliasPrefix . 'role_permission_id');
                $objSelect->AddSelectItems($objBuilder, $strTableName, $strAliasPrefix);
            } else {
			    $objBuilder->AddSelectItem($strTableName, 'role_permission_id', $strAliasPrefix . 'role_permission_id');
			    $objBuilder->AddSelectItem($strTableName, 'role_id', $strAliasPrefix . 'role_id');
			    $objBuilder->AddSelectItem($strTableName, 'permission_id', $strAliasPrefix . 'permission_id');
            }
		}



		///////////////////////////////
		// INSTANTIATION-RELATED METHODS
		///////////////////////////////

		/**
		 * Instantiate a NarroRolePermission from a Database Row.
		 * Takes in an optional strAliasPrefix, used in case another Object::InstantiateDbRow
		 * is calling this NarroRolePermission::InstantiateDbRow in order to perform
		 * early binding on referenced objects.
		 * @param DatabaseRowBase $objDbRow
		 * @param string $strAliasPrefix
		 * @param string $strExpandAsArrayNodes
		 * @param QBaseClass $arrPreviousItem
		 * @param string[] $strColumnAliasArray
		 * @return NarroRolePermission
		*/
		public static function InstantiateDbRow($objDbRow, $strAliasPrefix = null, $strExpandAsArrayNodes = null, $arrPreviousItems = null, $strColumnAliasArray = array()) {
			// If blank row, return null
			if (!$objDbRow) {
				return null;
			}

			// Create a new instance of the NarroRolePermission object
			$objToReturn = new NarroRolePermission();
			$objToReturn->__blnRestored = true;

			$strAlias = $strAliasPrefix . 'role_permission_id';
			$strAliasName = array_key_exists($strAlias, $strColumnAliasArray) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objToReturn->intRolePermissionId = $objDbRow->GetColumn($strAliasName, 'Integer');
			$strAlias = $strAliasPrefix . 'role_id';
			$strAliasName = array_key_exists($strAlias, $strColumnAliasArray) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objToReturn->intRoleId = $objDbRow->GetColumn($strAliasName, 'Integer');
			$strAlias = $strAliasPrefix . 'permission_id';
			$strAliasName = array_key_exists($strAlias, $strColumnAliasArray) ? $strColumnAliasArray[$strAlias] : $strAlias;
			$objToReturn->intPermissionId = $objDbRow->GetColumn($strAliasName, 'Integer');

			if (isset($arrPreviousItems) && is_array($arrPreviousItems)) {
				foreach ($arrPreviousItems as $objPreviousItem) {
					if ($objToReturn->RolePermissionId != $objPreviousItem->RolePermissionId) {
						continue;
					}

					// complete match - all primary key columns are the same
					return null;
				}
			}

			// Instantiate Virtual Attributes
			$strVirtualPrefix = $strAliasPrefix . '__';
			$strVirtualPrefixLength = strlen($strVirtualPrefix);
			foreach ($objDbRow->GetColumnNameArray() as $strColumnName => $mixValue) {
				if (strncmp($strColumnName, $strVirtualPrefix, $strVirtualPrefixLength) == 0)
					$objToReturn->__strVirtualAttributeArray[substr($strColumnName, $strVirtualPrefixLength)] = $mixValue;
			}

			// Prepare to Check for Early/Virtual Binding
			if (!$strAliasPrefix)
				$strAliasPrefix = 'narro_role_permission__';

			// Check for Role Early Binding
			$strAlias = $strAliasPrefix . 'role_id__role_id';
			$strAliasName = array_key_exists($strAlias, $strColumnAliasArray) ? $strColumnAliasArray[$strAlias] : $strAlias;
			if (!is_null($objDbRow->GetColumn($strAliasName)))
				$objToReturn->objRole = NarroRole::InstantiateDbRow($objDbRow, $strAliasPrefix . 'role_id__', $strExpandAsArrayNodes, null, $strColumnAliasArray);

			// Check for Permission Early Binding
			$strAlias = $strAliasPrefix . 'permission_id__permission_id';
			$strAliasName = array_key_exists($strAlias, $strColumnAliasArray) ? $strColumnAliasArray[$strAlias] : $strAlias;
			if (!is_null($objDbRow->GetColumn($strAliasName)))
				$objToReturn->objPermission = NarroPermission::InstantiateDbRow($objDbRow, $strAliasPrefix . 'permission_id__', $strExpandAsArrayNodes, null, $strColumnAliasArray);




			return $objToReturn;
		}

		/**
		 * Instantiate an array of NarroRolePermissions from a Database Result
		 * @param DatabaseResultBase $objDbResult
		 * @param string $strExpandAsArrayNodes
		 * @param string[] $strColumnAliasArray
		 * @return NarroRolePermission[]
		 */
		public static function InstantiateDbResult(QDatabaseResultBase $objDbResult, $strExpandAsArrayNodes = null, $strColumnAliasArray = null) {
			$objToReturn = array();

			if (!$strColumnAliasArray)
				$strColumnAliasArray = array();

			// If blank resultset, then return empty array
			if (!$objDbResult)
				return $objToReturn;

			// Load up the return array with each row
			if ($strExpandAsArrayNodes) {
				$objToReturn = array();
				while ($objDbRow = $objDbResult->GetNextRow()) {
					$objItem = NarroRolePermission::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, $objToReturn, $strColumnAliasArray);
					if ($objItem) {
						$objToReturn[] = $objItem;
					}
				}
			} else {
				while ($objDbRow = $objDbResult->GetNextRow())
					$objToReturn[] = NarroRolePermission::InstantiateDbRow($objDbRow, null, null, null, $strColumnAliasArray);
			}

			return $objToReturn;
		}


		/**
		 * Instantiate a single NarroRolePermission object from a query cursor (e.g. a DB ResultSet).
		 * Cursor is automatically moved to the "next row" of the result set.
		 * Will return NULL if no cursor or if the cursor has no more rows in the resultset.
		 * @param QDatabaseResultBase $objDbResult cursor resource
		 * @return NarroRolePermission next row resulting from the query
		 */
		public static function InstantiateCursor(QDatabaseResultBase $objDbResult) {
			// If blank resultset, then return empty result
			if (!$objDbResult) return null;

			// If empty resultset, then return empty result
			$objDbRow = $objDbResult->GetNextRow();
			if (!$objDbRow) return null;

			// We need the Column Aliases
			$strColumnAliasArray = $objDbResult->QueryBuilder->ColumnAliasArray;
			if (!$strColumnAliasArray) $strColumnAliasArray = array();

			// Pull Expansions (if applicable)
			$strExpandAsArrayNodes = $objDbResult->QueryBuilder->ExpandAsArrayNodes;

			// Load up the return result with a row and return it
			return NarroRolePermission::InstantiateDbRow($objDbRow, null, $strExpandAsArrayNodes, null, $strColumnAliasArray);
		}




		///////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Single Load and Array)
		///////////////////////////////////////////////////

		/**
		 * Load a single NarroRolePermission object,
		 * by RolePermissionId Index(es)
		 * @param integer $intRolePermissionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroRolePermission
		*/
		public static function LoadByRolePermissionId($intRolePermissionId, $objOptionalClauses = null) {
			return NarroRolePermission::QuerySingle(
				QQ::AndCondition(
					QQ::Equal(QQN::NarroRolePermission()->RolePermissionId, $intRolePermissionId)
				),
				$objOptionalClauses
			);
		}

		/**
		 * Load an array of NarroRolePermission objects,
		 * by RoleId Index(es)
		 * @param integer $intRoleId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroRolePermission[]
		*/
		public static function LoadArrayByRoleId($intRoleId, $objOptionalClauses = null) {
			// Call NarroRolePermission::QueryArray to perform the LoadArrayByRoleId query
			try {
				return NarroRolePermission::QueryArray(
					QQ::Equal(QQN::NarroRolePermission()->RoleId, $intRoleId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroRolePermissions
		 * by RoleId Index(es)
		 * @param integer $intRoleId
		 * @return int
		*/
		public static function CountByRoleId($intRoleId) {
			// Call NarroRolePermission::QueryCount to perform the CountByRoleId query
			return NarroRolePermission::QueryCount(
				QQ::Equal(QQN::NarroRolePermission()->RoleId, $intRoleId)
			);
		}

		/**
		 * Load an array of NarroRolePermission objects,
		 * by PermissionId Index(es)
		 * @param integer $intPermissionId
		 * @param QQClause[] $objOptionalClauses additional optional QQClause objects for this query
		 * @return NarroRolePermission[]
		*/
		public static function LoadArrayByPermissionId($intPermissionId, $objOptionalClauses = null) {
			// Call NarroRolePermission::QueryArray to perform the LoadArrayByPermissionId query
			try {
				return NarroRolePermission::QueryArray(
					QQ::Equal(QQN::NarroRolePermission()->PermissionId, $intPermissionId),
					$objOptionalClauses);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * Count NarroRolePermissions
		 * by PermissionId Index(es)
		 * @param integer $intPermissionId
		 * @return int
		*/
		public static function CountByPermissionId($intPermissionId) {
			// Call NarroRolePermission::QueryCount to perform the CountByPermissionId query
			return NarroRolePermission::QueryCount(
				QQ::Equal(QQN::NarroRolePermission()->PermissionId, $intPermissionId)
			);
		}



		////////////////////////////////////////////////////
		// INDEX-BASED LOAD METHODS (Array via Many to Many)
		////////////////////////////////////////////////////





		//////////////////////////
		// SAVE, DELETE AND RELOAD
		//////////////////////////

		/**
		 * Save this NarroRolePermission
		 * @param bool $blnForceInsert
		 * @param bool $blnForceUpdate
		 * @return int
		 */
		public function Save($blnForceInsert = false, $blnForceUpdate = false) {
			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();

			$mixToReturn = null;

			try {
				if ((!$this->__blnRestored) || ($blnForceInsert)) {
					// Perform an INSERT query
					$objDatabase->NonQuery('
						INSERT INTO `narro_role_permission` (
							`role_id`,
							`permission_id`
						) VALUES (
							' . $objDatabase->SqlVariable($this->intRoleId) . ',
							' . $objDatabase->SqlVariable($this->intPermissionId) . '
						)
					');

					// Update Identity column and return its value
					$mixToReturn = $this->intRolePermissionId = $objDatabase->InsertId('narro_role_permission', 'role_permission_id');
				} else {
					// Perform an UPDATE query

					// First checking for Optimistic Locking constraints (if applicable)

					// Perform the UPDATE query
					$objDatabase->NonQuery('
						UPDATE
							`narro_role_permission`
						SET
							`role_id` = ' . $objDatabase->SqlVariable($this->intRoleId) . ',
							`permission_id` = ' . $objDatabase->SqlVariable($this->intPermissionId) . '
						WHERE
							`role_permission_id` = ' . $objDatabase->SqlVariable($this->intRolePermissionId) . '
					');
				}

			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

			// Update __blnRestored and any Non-Identity PK Columns (if applicable)
			$this->__blnRestored = true;


			$this->DeleteCache();

			// Return
			return $mixToReturn;
		}

		/**
		 * Delete this NarroRolePermission
		 * @return void
		 */
		public function Delete() {
			if ((is_null($this->intRolePermissionId)))
				throw new QUndefinedPrimaryKeyException('Cannot delete this NarroRolePermission with an unset primary key.');

			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();


			// Perform the SQL Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_role_permission`
				WHERE
					`role_permission_id` = ' . $objDatabase->SqlVariable($this->intRolePermissionId) . '');

			$this->DeleteCache();
		}

        /**
 	     * Delete this NarroRolePermission ONLY from the cache
 		 * @return void
		 */
		public function DeleteCache() {
			if (QApplication::$objCacheProvider && QApplication::$Database[1]->Caching) {
				$strCacheKey = QApplication::$objCacheProvider->CreateKey('narro', 'NarroRolePermission', $this->intRolePermissionId);
				QApplication::$objCacheProvider->Delete($strCacheKey);
			}
		}

		/**
		 * Delete all NarroRolePermissions
		 * @return void
		 */
		public static function DeleteAll() {
			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				DELETE FROM
					`narro_role_permission`');

			if (QApplication::$objCacheProvider && QApplication::$Database[1]->Caching) {
				QApplication::$objCacheProvider->DeleteAll();
			}
		}

		/**
		 * Truncate narro_role_permission table
		 * @return void
		 */
		public static function Truncate() {
			// Get the Database Object for this Class
			$objDatabase = NarroRolePermission::GetDatabase();

			// Perform the Query
			$objDatabase->NonQuery('
				TRUNCATE `narro_role_permission`');

			if (QApplication::$objCacheProvider && QApplication::$Database[1]->Caching) {
				QApplication::$objCacheProvider->DeleteAll();
			}
		}

		/**
		 * Reload this NarroRolePermission from the database.
		 * @return void
		 */
		public function Reload() {
			// Make sure we are actually Restored from the database
			if (!$this->__blnRestored)
				throw new QCallerException('Cannot call Reload() on a new, unsaved NarroRolePermission object.');

			$this->DeleteCache();

			// Reload the Object
			$objReloaded = NarroRolePermission::Load($this->intRolePermissionId);

			// Update $this's local variables to match
			$this->RoleId = $objReloaded->RoleId;
			$this->PermissionId = $objReloaded->PermissionId;
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
				case 'RolePermissionId':
					/**
					 * Gets the value for intRolePermissionId (Read-Only PK)
					 * @return integer
					 */
					return $this->intRolePermissionId;

				case 'RoleId':
					/**
					 * Gets the value for intRoleId (Not Null)
					 * @return integer
					 */
					return $this->intRoleId;

				case 'PermissionId':
					/**
					 * Gets the value for intPermissionId (Not Null)
					 * @return integer
					 */
					return $this->intPermissionId;


				///////////////////
				// Member Objects
				///////////////////
				case 'Role':
					/**
					 * Gets the value for the NarroRole object referenced by intRoleId (Not Null)
					 * @return NarroRole
					 */
					try {
						if ((!$this->objRole) && (!is_null($this->intRoleId)))
							$this->objRole = NarroRole::Load($this->intRoleId);
						return $this->objRole;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'Permission':
					/**
					 * Gets the value for the NarroPermission object referenced by intPermissionId (Not Null)
					 * @return NarroPermission
					 */
					try {
						if ((!$this->objPermission) && (!is_null($this->intPermissionId)))
							$this->objPermission = NarroPermission::Load($this->intPermissionId);
						return $this->objPermission;
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				////////////////////////////
				// Virtual Object References (Many to Many and Reverse References)
				// (If restored via a "Many-to" expansion)
				////////////////////////////


				case '__Restored':
					return $this->__blnRestored;

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
				case 'RoleId':
					/**
					 * Sets the value for intRoleId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objRole = null;
						return ($this->intRoleId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}

				case 'PermissionId':
					/**
					 * Sets the value for intPermissionId (Not Null)
					 * @param integer $mixValue
					 * @return integer
					 */
					try {
						$this->objPermission = null;
						return ($this->intPermissionId = QType::Cast($mixValue, QType::Integer));
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}


				///////////////////
				// Member Objects
				///////////////////
				case 'Role':
					/**
					 * Sets the value for the NarroRole object referenced by intRoleId (Not Null)
					 * @param NarroRole $mixValue
					 * @return NarroRole
					 */
					if (is_null($mixValue)) {
						$this->intRoleId = null;
						$this->objRole = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroRole object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroRole');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						}

						// Make sure $mixValue is a SAVED NarroRole object
						if (is_null($mixValue->RoleId))
							throw new QCallerException('Unable to set an unsaved Role for this NarroRolePermission');

						// Update Local Member Variables
						$this->objRole = $mixValue;
						$this->intRoleId = $mixValue->RoleId;

						// Return $mixValue
						return $mixValue;
					}
					break;

				case 'Permission':
					/**
					 * Sets the value for the NarroPermission object referenced by intPermissionId (Not Null)
					 * @param NarroPermission $mixValue
					 * @return NarroPermission
					 */
					if (is_null($mixValue)) {
						$this->intPermissionId = null;
						$this->objPermission = null;
						return null;
					} else {
						// Make sure $mixValue actually is a NarroPermission object
						try {
							$mixValue = QType::Cast($mixValue, 'NarroPermission');
						} catch (QInvalidCastException $objExc) {
							$objExc->IncrementOffset();
							throw $objExc;
						}

						// Make sure $mixValue is a SAVED NarroPermission object
						if (is_null($mixValue->PermissionId))
							throw new QCallerException('Unable to set an unsaved Permission for this NarroRolePermission');

						// Update Local Member Variables
						$this->objPermission = $mixValue;
						$this->intPermissionId = $mixValue->PermissionId;

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
		// ASSOCIATED OBJECTS' METHODS
		///////////////////////////////



		
		///////////////////////////////
		// METHODS TO EXTRACT INFO ABOUT THE CLASS
		///////////////////////////////

		/**
		 * Static method to retrieve the Database object that owns this class.
		 * @return string Name of the table from which this class has been created.
		 */
		public static function GetTableName() {
			return "narro_role_permission";
		}

		/**
		 * Static method to retrieve the Table name from which this class has been created.
		 * @return string Name of the table from which this class has been created.
		 */
		public static function GetDatabaseName() {
			return QApplication::$Database[NarroRolePermission::GetDatabaseIndex()]->Database;
		}

		/**
		 * Static method to retrieve the Database index in the configuration.inc.php file.
		 * This can be useful when there are two databases of the same name which create
		 * confusion for the developer. There are no internal uses of this function but are
		 * here to help retrieve info if need be!
		 * @return int position or index of the database in the config file.
		 */
		public static function GetDatabaseIndex() {
			return 1;
		}

		////////////////////////////////////////
		// METHODS for SOAP-BASED WEB SERVICES
		////////////////////////////////////////

		public static function GetSoapComplexTypeXml() {
			$strToReturn = '<complexType name="NarroRolePermission"><sequence>';
			$strToReturn .= '<element name="RolePermissionId" type="xsd:int"/>';
			$strToReturn .= '<element name="Role" type="xsd1:NarroRole"/>';
			$strToReturn .= '<element name="Permission" type="xsd1:NarroPermission"/>';
			$strToReturn .= '<element name="__blnRestored" type="xsd:boolean"/>';
			$strToReturn .= '</sequence></complexType>';
			return $strToReturn;
		}

		public static function AlterSoapComplexTypeArray(&$strComplexTypeArray) {
			if (!array_key_exists('NarroRolePermission', $strComplexTypeArray)) {
				$strComplexTypeArray['NarroRolePermission'] = NarroRolePermission::GetSoapComplexTypeXml();
				NarroRole::AlterSoapComplexTypeArray($strComplexTypeArray);
				NarroPermission::AlterSoapComplexTypeArray($strComplexTypeArray);
			}
		}

		public static function GetArrayFromSoapArray($objSoapArray) {
			$objArrayToReturn = array();

			foreach ($objSoapArray as $objSoapObject)
				array_push($objArrayToReturn, NarroRolePermission::GetObjectFromSoapObject($objSoapObject));

			return $objArrayToReturn;
		}

		public static function GetObjectFromSoapObject($objSoapObject) {
			$objToReturn = new NarroRolePermission();
			if (property_exists($objSoapObject, 'RolePermissionId'))
				$objToReturn->intRolePermissionId = $objSoapObject->RolePermissionId;
			if ((property_exists($objSoapObject, 'Role')) &&
				($objSoapObject->Role))
				$objToReturn->Role = NarroRole::GetObjectFromSoapObject($objSoapObject->Role);
			if ((property_exists($objSoapObject, 'Permission')) &&
				($objSoapObject->Permission))
				$objToReturn->Permission = NarroPermission::GetObjectFromSoapObject($objSoapObject->Permission);
			if (property_exists($objSoapObject, '__blnRestored'))
				$objToReturn->__blnRestored = $objSoapObject->__blnRestored;
			return $objToReturn;
		}

		public static function GetSoapArrayFromArray($objArray) {
			if (!$objArray)
				return null;

			$objArrayToReturn = array();

			foreach ($objArray as $objObject)
				array_push($objArrayToReturn, NarroRolePermission::GetSoapObjectFromObject($objObject, true));

			return unserialize(serialize($objArrayToReturn));
		}

		public static function GetSoapObjectFromObject($objObject, $blnBindRelatedObjects) {
			if ($objObject->objRole)
				$objObject->objRole = NarroRole::GetSoapObjectFromObject($objObject->objRole, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intRoleId = null;
			if ($objObject->objPermission)
				$objObject->objPermission = NarroPermission::GetSoapObjectFromObject($objObject->objPermission, false);
			else if (!$blnBindRelatedObjects)
				$objObject->intPermissionId = null;
			return $objObject;
		}


		////////////////////////////////////////
		// METHODS for JSON Object Translation
		////////////////////////////////////////

		// this function is required for objects that implement the
		// IteratorAggregate interface
		public function getIterator() {
			///////////////////
			// Member Variables
			///////////////////
			$iArray['RolePermissionId'] = $this->intRolePermissionId;
			$iArray['RoleId'] = $this->intRoleId;
			$iArray['PermissionId'] = $this->intPermissionId;
			return new ArrayIterator($iArray);
		}

		// this function returns a Json formatted string using the
		// IteratorAggregate interface
		public function getJson() {
			return json_encode($this->getIterator());
		}

		/**
		 * Default "toJsObject" handler
		 * Specifies how the object should be displayed in JQuery UI lists and menus. Note that these lists use
		 * value and label differently.
		 *
		 * value 	= The short form of what to display in the list and selection.
		 * label 	= [optional] If defined, is what is displayed in the menu
		 * id 		= Primary key of object.
		 *
		 * @return an array that specifies how to display the object
		 */
		public function toJsObject () {
			return JavaScriptHelper::toJsObject(array('value' => $this->__toString(), 'id' =>  $this->intRolePermissionId ));
		}



	}



	/////////////////////////////////////
	// ADDITIONAL CLASSES for QCubed QUERY
	/////////////////////////////////////

    /**
     * @uses QQNode
     *
     * @property-read QQNode $RolePermissionId
     * @property-read QQNode $RoleId
     * @property-read QQNodeNarroRole $Role
     * @property-read QQNode $PermissionId
     * @property-read QQNodeNarroPermission $Permission
     *
     *

     * @property-read QQNode $_PrimaryKeyNode
     **/
	class QQNodeNarroRolePermission extends QQNode {
		protected $strTableName = 'narro_role_permission';
		protected $strPrimaryKey = 'role_permission_id';
		protected $strClassName = 'NarroRolePermission';
		public function __get($strName) {
			switch ($strName) {
				case 'RolePermissionId':
					return new QQNode('role_permission_id', 'RolePermissionId', 'Integer', $this);
				case 'RoleId':
					return new QQNode('role_id', 'RoleId', 'Integer', $this);
				case 'Role':
					return new QQNodeNarroRole('role_id', 'Role', 'Integer', $this);
				case 'PermissionId':
					return new QQNode('permission_id', 'PermissionId', 'Integer', $this);
				case 'Permission':
					return new QQNodeNarroPermission('permission_id', 'Permission', 'Integer', $this);

				case '_PrimaryKeyNode':
					return new QQNode('role_permission_id', 'RolePermissionId', 'Integer', $this);
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

    /**
     * @property-read QQNode $RolePermissionId
     * @property-read QQNode $RoleId
     * @property-read QQNodeNarroRole $Role
     * @property-read QQNode $PermissionId
     * @property-read QQNodeNarroPermission $Permission
     *
     *

     * @property-read QQNode $_PrimaryKeyNode
     **/
	class QQReverseReferenceNodeNarroRolePermission extends QQReverseReferenceNode {
		protected $strTableName = 'narro_role_permission';
		protected $strPrimaryKey = 'role_permission_id';
		protected $strClassName = 'NarroRolePermission';
		public function __get($strName) {
			switch ($strName) {
				case 'RolePermissionId':
					return new QQNode('role_permission_id', 'RolePermissionId', 'integer', $this);
				case 'RoleId':
					return new QQNode('role_id', 'RoleId', 'integer', $this);
				case 'Role':
					return new QQNodeNarroRole('role_id', 'Role', 'integer', $this);
				case 'PermissionId':
					return new QQNode('permission_id', 'PermissionId', 'integer', $this);
				case 'Permission':
					return new QQNodeNarroPermission('permission_id', 'Permission', 'integer', $this);

				case '_PrimaryKeyNode':
					return new QQNode('role_permission_id', 'RolePermissionId', 'integer', $this);
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
