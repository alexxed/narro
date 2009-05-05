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
		const Sourcefilestoignore = 1;
		const Forceasciiaccesskeys = 2;

		const MaxId = 2;

		public static $NameArray = array(
			1 => 'Source files to ignore',
			2 => 'Force ascii access keys');

		public static $TokenArray = array(
			1 => 'Sourcefilestoignore',
			2 => 'Forceasciiaccesskeys');

		public static function ToString($intNarroProjectSettingTypeId) {
			switch ($intNarroProjectSettingTypeId) {
				case 1: return 'Source files to ignore';
				case 2: return 'Force ascii access keys';
				default:
					throw new QCallerException(sprintf('Invalid intNarroProjectSettingTypeId: %s', $intNarroProjectSettingTypeId));
			}
		}

		public static function ToToken($intNarroProjectSettingTypeId) {
			switch ($intNarroProjectSettingTypeId) {
				case 1: return 'Sourcefilestoignore';
				case 2: return 'Forceasciiaccesskeys';
				default:
					throw new QCallerException(sprintf('Invalid intNarroProjectSettingTypeId: %s', $intNarroProjectSettingTypeId));
			}
		}
	}
?>