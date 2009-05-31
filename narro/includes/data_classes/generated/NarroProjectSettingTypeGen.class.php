<?php
	/**
	 * The NarroProjectSettingType class defined here contains
	 * code for the NarroProjectSettingType enumerated type.  It represents
	 * the enumerated values found in the "narro_project_setting_type" table
	 * in the database.
	 * 
	 * To use, you should use the NarroProjectSettingType subclass which
	 * extends this NarroProjectSettingTypeGen class.
	 * 
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroProjectSettingType class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 */
	abstract class NarroProjectSettingTypeGen extends QBaseClass {

		const MaxId = 0;

		public static $NameArray = array();

		public static $TokenArray = array();

		public static function ToString($intNarroProjectSettingTypeId) {
			switch ($intNarroProjectSettingTypeId) {
				default:
					throw new QCallerException(sprintf('Invalid intNarroProjectSettingTypeId: %s', $intNarroProjectSettingTypeId));
			}
		}

		public static function ToToken($intNarroProjectSettingTypeId) {
			switch ($intNarroProjectSettingTypeId) {
				default:
					throw new QCallerException(sprintf('Invalid intNarroProjectSettingTypeId: %s', $intNarroProjectSettingTypeId));
			}
		}
	}
?>