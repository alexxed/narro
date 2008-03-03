<?php
	/**
	 * The NarroFileType class defined here contains
	 * code for the NarroFileType enumerated type.  It represents
	 * the enumerated values found in the "narro_file_type" table
	 * in the database.
	 * 
	 * To use, you should use the NarroFileType subclass which
	 * extends this NarroFileTypeGen class.
	 * 
	 * Because subsequent re-code generations will overwrite any changes to this
	 * file, you should leave this file unaltered to prevent yourself from losing
	 * any information or code changes.  All customizations should be done by
	 * overriding existing or implementing new methods, properties and variables
	 * in the NarroFileType class.
	 * 
	 * @package Narro
	 * @subpackage GeneratedDataObjects
	 */
	abstract class NarroFileTypeGen extends QBaseClass {
		const PoGettext = 1;
		const SdfOpenOffice = 2;
		const Dosar = 3;
		const DtdMozilla = 4;
		const IniProperties = 5;

		const MaxId = 5;

		public static $NameArray = array(
			1 => 'PoGettext',
			2 => 'SdfOpenOffice',
			3 => 'Dosar',
			4 => 'DtdMozilla',
			5 => 'IniProperties');

		public static $TokenArray = array(
			1 => 'PoGettext',
			2 => 'SdfOpenOffice',
			3 => 'Dosar',
			4 => 'DtdMozilla',
			5 => 'IniProperties');

		public static function ToString($intNarroFileTypeId) {
			switch ($intNarroFileTypeId) {
				case 1: return 'PoGettext';
				case 2: return 'SdfOpenOffice';
				case 3: return 'Dosar';
				case 4: return 'DtdMozilla';
				case 5: return 'IniProperties';
				default:
					throw new QCallerException(sprintf('Invalid intNarroFileTypeId: %s', $intNarroFileTypeId));
			}
		}

		public static function ToToken($intNarroFileTypeId) {
			switch ($intNarroFileTypeId) {
				case 1: return 'PoGettext';
				case 2: return 'SdfOpenOffice';
				case 3: return 'Dosar';
				case 4: return 'DtdMozilla';
				case 5: return 'IniProperties';
				default:
					throw new QCallerException(sprintf('Invalid intNarroFileTypeId: %s', $intNarroFileTypeId));
			}
		}
	}
?>