<?php
    require(__DATAGEN_CLASSES__ . '/NarroUserGen.class.php');

    /**
    * The NarroUser class defined here contains any
    * customized code for the NarroUser class in the
    * Object Relational Model.  It represents the "narro_user" table
    * in the database, and extends from the code generated abstract NarroUserGen
    * class, which contains all the basic CRUD-type functionality as well as
    * basic methods to handle relationships and index-based loading.
    *
    * @package Narro
    * @subpackage DataObjects
    *
    */
    class NarroUser extends NarroUserGen {
        protected $arrPermissions;
        public $arrPreferences;
        const ANONYMOUS_USER_ID = 0;
        /**
        * Default "to string" handler
        * Allows pages to _p()/echo()/print() this object, and to define the default
        * way this object would be outputted.
        *
        * Can also be called directly via $objNarroUser->__toString().
        *
        * @return string a nicely formatted string representation of this object
        */
        public function __toString() {
            return sprintf('NarroUser Object %s',  $this->intUserId);
        }

        public function getPreferenceValueByName($strName) {
            if (isset($this->arrPreferences[$strName]))
                return $this->arrPreferences[$strName];
            else
                return QApplication::$arrPreferences[$strName]['default'];
        }

        public static function LoadByUsernameAndPassword($strUsername, $strPassword) {
            $objUser = NarroUser::QuerySingle(
                        QQ::AndCondition(
                            QQ::Equal(QQN::NarroUser()->Username, $strUsername),
                            QQ::Equal(QQN::NarroUser()->Password, $strPassword)
                        )
            );
            if (!$objUser instanceof NarroUser)
                return false;
            $arrUserPermissions = NarroUserPermission::LoadArrayByUserId($objUser->intUserId);
            foreach($arrUserPermissions as $objUserPermission) {
                $objPermission = NarroPermission::Load($objUserPermission->PermissionId);
                $objUser->arrPermissions[$objPermission->PermissionName] = $objUserPermission;
            }
            $objUser->arrPreferences = unserialize($objUser->Data);
            return $objUser;
        }

        public static function LoadAnonymousUser() {
            $objUser = NarroUser::LoadByUserId(self::ANONYMOUS_USER_ID);
            $arrUserPermissions = NarroUserPermission::LoadArrayByUserId(self::ANONYMOUS_USER_ID);
            foreach($arrUserPermissions as $objUserPermission) {
                $objPermission = NarroPermission::Load($objUserPermission->PermissionId);
                $objUser->arrPermissions[$objPermission->PermissionName] = $objUserPermission;
            }

            $objUser->arrPreferences = unserialize($objUser->Data);
            return $objUser;
        }

        public function hasPermission($strPermissionName, $intProjectId = null) {
            if ($intProjectId) {
                if (isset($this->arrPermissions[$strPermissionName])) {
                    $objUserPermission = $this->arrPermissions[$strPermissionName];
                    if ( $objUserPermission instanceof NarroUserPermission && ( $objUserPermission->ProjectId == $intProjectId || is_null($objUserPermission->ProjectId) ))
                        return true;
                    else
                        return false;
                }
                else
                    return false;
            }
            else {
                if (isset($this->arrPermissions[$strPermissionName]))
                    return true;
                else
                    return false;
            }
        }



        // Override or Create New Load/Count methods
        // (For obvious reasons, these methods are commented out...
        // but feel free to use these as a starting point)
/*
        public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses = null) {
            // This will return an array of NarroUser objects
            return NarroUser::QueryArray(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->Param1, $strParam1),
                    QQ::GreaterThan(QQN::NarroUser()->Param2, $intParam2)
                ),
                $objOptionalClauses
            );
        }

        public static function LoadBySample($strParam1, $intParam2, $objOptionalClauses = null) {
            // This will return a single NarroUser object
            return NarroUser::QuerySingle(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->Param1, $strParam1),
                    QQ::GreaterThan(QQN::NarroUser()->Param2, $intParam2)
                ),
                $objOptionalClauses
            );
        }

        public static function CountBySample($strParam1, $intParam2, $objOptionalClauses = null) {
            // This will return a count of NarroUser objects
            return NarroUser::QueryCount(
                QQ::AndCondition(
                    QQ::Equal(QQN::NarroUser()->Param1, $strParam1),
                    QQ::Equal(QQN::NarroUser()->Param2, $intParam2)
                ),
                $objOptionalClauses
            );
        }

        public static function LoadArrayBySample($strParam1, $intParam2, $objOptionalClauses) {
            // Performing the load manually (instead of using Qcodo Query)

            // Get the Database Object for this Class
            $objDatabase = NarroUser::GetDatabase();

            // Properly Escape All Input Parameters using Database->SqlVariable()
            $strParam1 = $objDatabase->SqlVariable($strParam1);
            $intParam2 = $objDatabase->SqlVariable($intParam2);

            // Setup the SQL Query
            $strQuery = sprintf('
                SELECT
                    `narro_user`.*
                FROM
                    `narro_user` AS `narro_user`
                WHERE
                    param_1 = %s AND
                    param_2 < %s',
                $strParam1, $intParam2);

            // Perform the Query and Instantiate the Result
            $objDbResult = $objDatabase->Query($strQuery);
            return NarroUser::InstantiateDbResult($objDbResult);
        }
*/



        // Override or Create New Properties and Variables
        // For performance reasons, these variables and __set and __get override methods
        // are commented out.  But if you wish to implement or override any
        // of the data generated properties, please feel free to uncomment them.
/*
        protected $strSomeNewProperty;

        public function __get($strName) {
            switch ($strName) {
                case 'SomeNewProperty': return $this->strSomeNewProperty;

                default:
                    try {
                        return parent::__get($strName);
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
*/

        public function __set($strName, $mixValue) {
            switch ($strName) {
                case 'Preferences':
                    try {
                        return ($this->arrPreferences = $mixValue);
                    } catch (QInvalidCastException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }

                default:
                    try {
                        return (parent::__set($strName, $mixValue));
                    } catch (QCallerException $objExc) {
                        $objExc->IncrementOffset();
                        throw $objExc;
                    }
            }
        }
    }
?>