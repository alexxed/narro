<?php
	/**
	 * This is a MetaControl class, providing a QForm or QPanel access to event handlers
	 * and QControls to perform the Create, Edit, and Delete functionality
	 * of the NarroSuggestionComment class.  This code-generated class
	 * contains all the basic elements to help a QPanel or QForm display an HTML form that can
	 * manipulate a single NarroSuggestionComment object.
	 *
	 * To take advantage of some (or all) of these control objects, you
	 * must create a new QForm or QPanel which instantiates a NarroSuggestionCommentMetaControl
	 * class.
	 *
	 * Any and all changes to this file will be overwritten with any subsequent
	 * code re-generation.
	 * 
	 * @package Narro
	 * @subpackage MetaControls
	 * property-read NarroSuggestionComment $NarroSuggestionComment the actual NarroSuggestionComment data class being edited
	 * property QLabel $CommentIdControl
	 * property-read QLabel $CommentIdLabel
	 * property QListBox $SuggestionIdControl
	 * property-read QLabel $SuggestionIdLabel
	 * property QListBox $UserIdControl
	 * property-read QLabel $UserIdLabel
	 * property QTextBox $CommentTextControl
	 * property-read QLabel $CommentTextLabel
	 * property QTextBox $CommentTextMd5Control
	 * property-read QLabel $CommentTextMd5Label
	 * property QDateTimePicker $CreatedControl
	 * property-read QLabel $CreatedLabel
	 * property QDateTimePicker $ModifiedControl
	 * property-read QLabel $ModifiedLabel
	 * property-read string $TitleVerb a verb indicating whether or not this is being edited or created
	 * property-read boolean $EditMode a boolean indicating whether or not this is being edited or created
	 */

	class NarroSuggestionCommentMetaControlGen extends QBaseClass {
		// General Variables
		protected $objNarroSuggestionComment;
		protected $objParentObject;
		protected $strTitleVerb;
		protected $blnEditMode;

		// Controls that allow the editing of NarroSuggestionComment's individual data fields
		protected $lblCommentId;
		protected $lstSuggestion;
		protected $lstUser;
		protected $txtCommentText;
		protected $txtCommentTextMd5;
		protected $calCreated;
		protected $calModified;

		// Controls that allow the viewing of NarroSuggestionComment's individual data fields
		protected $lblSuggestionId;
		protected $lblUserId;
		protected $lblCommentText;
		protected $lblCommentTextMd5;
		protected $lblCreated;
		protected $lblModified;

		// QListBox Controls (if applicable) to edit Unique ReverseReferences and ManyToMany References

		// QLabel Controls (if applicable) to view Unique ReverseReferences and ManyToMany References


		/**
		 * Main constructor.  Constructor OR static create methods are designed to be called in either
		 * a parent QPanel or the main QForm when wanting to create a
		 * NarroSuggestionCommentMetaControl to edit a single NarroSuggestionComment object within the
		 * QPanel or QForm.
		 *
		 * This constructor takes in a single NarroSuggestionComment object, while any of the static
		 * create methods below can be used to construct based off of individual PK ID(s).
		 *
		 * @param mixed $objParentObject QForm or QPanel which will be using this NarroSuggestionCommentMetaControl
		 * @param NarroSuggestionComment $objNarroSuggestionComment new or existing NarroSuggestionComment object
		 */
		 public function __construct($objParentObject, NarroSuggestionComment $objNarroSuggestionComment) {
			// Setup Parent Object (e.g. QForm or QPanel which will be using this NarroSuggestionCommentMetaControl)
			$this->objParentObject = $objParentObject;

			// Setup linked NarroSuggestionComment object
			$this->objNarroSuggestionComment = $objNarroSuggestionComment;

			// Figure out if we're Editing or Creating New
			if ($this->objNarroSuggestionComment->__Restored) {
				$this->strTitleVerb = QApplication::Translate('Edit');
				$this->blnEditMode = true;
			} else {
				$this->strTitleVerb = QApplication::Translate('Create');
				$this->blnEditMode = false;
			}
		 }

		/**
		 * Static Helper Method to Create using PK arguments
		 * You must pass in the PK arguments on an object to load, or leave it blank to create a new one.
		 * If you want to load via QueryString or PathInfo, use the CreateFromQueryString or CreateFromPathInfo
		 * static helper methods.  Finally, specify a CreateType to define whether or not we are only allowed to 
		 * edit, or if we are also allowed to create a new one, etc.
		 * 
		 * @param mixed $objParentObject QForm or QPanel which will be using this NarroSuggestionCommentMetaControl
		 * @param integer $intCommentId primary key value
		 * @param QMetaControlCreateType $intCreateType rules governing NarroSuggestionComment object creation - defaults to CreateOrEdit
 		 * @return NarroSuggestionCommentMetaControl
		 */
		public static function Create($objParentObject, $intCommentId = null, $intCreateType = QMetaControlCreateType::CreateOrEdit) {
			// Attempt to Load from PK Arguments
			if (strlen($intCommentId)) {
				$objNarroSuggestionComment = NarroSuggestionComment::Load($intCommentId);

				// NarroSuggestionComment was found -- return it!
				if ($objNarroSuggestionComment)
					return new NarroSuggestionCommentMetaControl($objParentObject, $objNarroSuggestionComment);

				// If CreateOnRecordNotFound not specified, throw an exception
				else if ($intCreateType != QMetaControlCreateType::CreateOnRecordNotFound)
					throw new QCallerException('Could not find a NarroSuggestionComment object with PK arguments: ' . $intCommentId);

			// If EditOnly is specified, throw an exception
			} else if ($intCreateType == QMetaControlCreateType::EditOnly)
				throw new QCallerException('No PK arguments specified');

			// If we are here, then we need to create a new record
			return new NarroSuggestionCommentMetaControl($objParentObject, new NarroSuggestionComment());
		}

		/**
		 * Static Helper Method to Create using PathInfo arguments
		 *
		 * @param mixed $objParentObject QForm or QPanel which will be using this NarroSuggestionCommentMetaControl
		 * @param QMetaControlCreateType $intCreateType rules governing NarroSuggestionComment object creation - defaults to CreateOrEdit
		 * @return NarroSuggestionCommentMetaControl
		 */
		public static function CreateFromPathInfo($objParentObject, $intCreateType = QMetaControlCreateType::CreateOrEdit) {
			$intCommentId = QApplication::PathInfo(0);
			return NarroSuggestionCommentMetaControl::Create($objParentObject, $intCommentId, $intCreateType);
		}

		/**
		 * Static Helper Method to Create using QueryString arguments
		 *
		 * @param mixed $objParentObject QForm or QPanel which will be using this NarroSuggestionCommentMetaControl
		 * @param QMetaControlCreateType $intCreateType rules governing NarroSuggestionComment object creation - defaults to CreateOrEdit
		 * @return NarroSuggestionCommentMetaControl
		 */
		public static function CreateFromQueryString($objParentObject, $intCreateType = QMetaControlCreateType::CreateOrEdit) {
			$intCommentId = QApplication::QueryString('intCommentId');
			return NarroSuggestionCommentMetaControl::Create($objParentObject, $intCommentId, $intCreateType);
		}



		///////////////////////////////////////////////
		// PUBLIC CREATE and REFRESH METHODS
		///////////////////////////////////////////////

		/**
		 * Create and setup QLabel lblCommentId
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function lblCommentId_Create($strControlId = null) {
			$this->lblCommentId = new QLabel($this->objParentObject, $strControlId);
			$this->lblCommentId->Name = QApplication::Translate('Comment Id');
			if ($this->blnEditMode)
				$this->lblCommentId->Text = $this->objNarroSuggestionComment->CommentId;
			else
				$this->lblCommentId->Text = 'N/A';
			return $this->lblCommentId;
		}

		/**
		 * Create and setup QListBox lstSuggestion
		 * @param string $strControlId optional ControlId to use
		 * @return QListBox
		 */
		public function lstSuggestion_Create($strControlId = null) {
			$this->lstSuggestion = new QListBox($this->objParentObject, $strControlId);
			$this->lstSuggestion->Name = QApplication::Translate('Suggestion');
			$this->lstSuggestion->Required = true;
			if (!$this->blnEditMode)
				$this->lstSuggestion->AddItem(QApplication::Translate('- Select One -'), null);
			$objSuggestionArray = NarroSuggestion::LoadAll();
			if ($objSuggestionArray) foreach ($objSuggestionArray as $objSuggestion) {
				$objListItem = new QListItem($objSuggestion->__toString(), $objSuggestion->SuggestionId);
				if (($this->objNarroSuggestionComment->Suggestion) && ($this->objNarroSuggestionComment->Suggestion->SuggestionId == $objSuggestion->SuggestionId))
					$objListItem->Selected = true;
				$this->lstSuggestion->AddItem($objListItem);
			}
			return $this->lstSuggestion;
		}

		/**
		 * Create and setup QLabel lblSuggestionId
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function lblSuggestionId_Create($strControlId = null) {
			$this->lblSuggestionId = new QLabel($this->objParentObject, $strControlId);
			$this->lblSuggestionId->Name = QApplication::Translate('Suggestion');
			$this->lblSuggestionId->Text = ($this->objNarroSuggestionComment->Suggestion) ? $this->objNarroSuggestionComment->Suggestion->__toString() : null;
			$this->lblSuggestionId->Required = true;
			return $this->lblSuggestionId;
		}

		/**
		 * Create and setup QListBox lstUser
		 * @param string $strControlId optional ControlId to use
		 * @return QListBox
		 */
		public function lstUser_Create($strControlId = null) {
			$this->lstUser = new QListBox($this->objParentObject, $strControlId);
			$this->lstUser->Name = QApplication::Translate('User');
			$this->lstUser->Required = true;
			if (!$this->blnEditMode)
				$this->lstUser->AddItem(QApplication::Translate('- Select One -'), null);
			$objUserArray = NarroUser::LoadAll();
			if ($objUserArray) foreach ($objUserArray as $objUser) {
				$objListItem = new QListItem($objUser->__toString(), $objUser->UserId);
				if (($this->objNarroSuggestionComment->User) && ($this->objNarroSuggestionComment->User->UserId == $objUser->UserId))
					$objListItem->Selected = true;
				$this->lstUser->AddItem($objListItem);
			}
			return $this->lstUser;
		}

		/**
		 * Create and setup QLabel lblUserId
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function lblUserId_Create($strControlId = null) {
			$this->lblUserId = new QLabel($this->objParentObject, $strControlId);
			$this->lblUserId->Name = QApplication::Translate('User');
			$this->lblUserId->Text = ($this->objNarroSuggestionComment->User) ? $this->objNarroSuggestionComment->User->__toString() : null;
			$this->lblUserId->Required = true;
			return $this->lblUserId;
		}

		/**
		 * Create and setup QTextBox txtCommentText
		 * @param string $strControlId optional ControlId to use
		 * @return QTextBox
		 */
		public function txtCommentText_Create($strControlId = null) {
			$this->txtCommentText = new QTextBox($this->objParentObject, $strControlId);
			$this->txtCommentText->Name = QApplication::Translate('Comment Text');
			$this->txtCommentText->Text = $this->objNarroSuggestionComment->CommentText;
			$this->txtCommentText->Required = true;
			$this->txtCommentText->TextMode = QTextMode::MultiLine;
			return $this->txtCommentText;
		}

		/**
		 * Create and setup QLabel lblCommentText
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function lblCommentText_Create($strControlId = null) {
			$this->lblCommentText = new QLabel($this->objParentObject, $strControlId);
			$this->lblCommentText->Name = QApplication::Translate('Comment Text');
			$this->lblCommentText->Text = $this->objNarroSuggestionComment->CommentText;
			$this->lblCommentText->Required = true;
			return $this->lblCommentText;
		}

		/**
		 * Create and setup QTextBox txtCommentTextMd5
		 * @param string $strControlId optional ControlId to use
		 * @return QTextBox
		 */
		public function txtCommentTextMd5_Create($strControlId = null) {
			$this->txtCommentTextMd5 = new QTextBox($this->objParentObject, $strControlId);
			$this->txtCommentTextMd5->Name = QApplication::Translate('Comment Text Md 5');
			$this->txtCommentTextMd5->Text = $this->objNarroSuggestionComment->CommentTextMd5;
			$this->txtCommentTextMd5->Required = true;
			$this->txtCommentTextMd5->MaxLength = NarroSuggestionComment::CommentTextMd5MaxLength;
			return $this->txtCommentTextMd5;
		}

		/**
		 * Create and setup QLabel lblCommentTextMd5
		 * @param string $strControlId optional ControlId to use
		 * @return QLabel
		 */
		public function lblCommentTextMd5_Create($strControlId = null) {
			$this->lblCommentTextMd5 = new QLabel($this->objParentObject, $strControlId);
			$this->lblCommentTextMd5->Name = QApplication::Translate('Comment Text Md 5');
			$this->lblCommentTextMd5->Text = $this->objNarroSuggestionComment->CommentTextMd5;
			$this->lblCommentTextMd5->Required = true;
			return $this->lblCommentTextMd5;
		}

		/**
		 * Create and setup QDateTimePicker calCreated
		 * @param string $strControlId optional ControlId to use
		 * @return QDateTimePicker
		 */
		public function calCreated_Create($strControlId = null) {
			$this->calCreated = new QDateTimePicker($this->objParentObject, $strControlId);
			$this->calCreated->Name = QApplication::Translate('Created');
			$this->calCreated->DateTime = $this->objNarroSuggestionComment->Created;
			$this->calCreated->DateTimePickerType = QDateTimePickerType::DateTime;
			$this->calCreated->Required = true;
			return $this->calCreated;
		}

		/**
		 * Create and setup QLabel lblCreated
		 * @param string $strControlId optional ControlId to use
		 * @param string $strDateTimeFormat optional DateTimeFormat to use
		 * @return QLabel
		 */
		public function lblCreated_Create($strControlId = null, $strDateTimeFormat = null) {
			$this->lblCreated = new QLabel($this->objParentObject, $strControlId);
			$this->lblCreated->Name = QApplication::Translate('Created');
			$this->strCreatedDateTimeFormat = $strDateTimeFormat;
			$this->lblCreated->Text = sprintf($this->objNarroSuggestionComment->Created) ? $this->objNarroSuggestionComment->Created->qFormat($this->strCreatedDateTimeFormat) : null;
			$this->lblCreated->Required = true;
			return $this->lblCreated;
		}

		protected $strCreatedDateTimeFormat;


		/**
		 * Create and setup QDateTimePicker calModified
		 * @param string $strControlId optional ControlId to use
		 * @return QDateTimePicker
		 */
		public function calModified_Create($strControlId = null) {
			$this->calModified = new QDateTimePicker($this->objParentObject, $strControlId);
			$this->calModified->Name = QApplication::Translate('Modified');
			$this->calModified->DateTime = $this->objNarroSuggestionComment->Modified;
			$this->calModified->DateTimePickerType = QDateTimePickerType::DateTime;
			return $this->calModified;
		}

		/**
		 * Create and setup QLabel lblModified
		 * @param string $strControlId optional ControlId to use
		 * @param string $strDateTimeFormat optional DateTimeFormat to use
		 * @return QLabel
		 */
		public function lblModified_Create($strControlId = null, $strDateTimeFormat = null) {
			$this->lblModified = new QLabel($this->objParentObject, $strControlId);
			$this->lblModified->Name = QApplication::Translate('Modified');
			$this->strModifiedDateTimeFormat = $strDateTimeFormat;
			$this->lblModified->Text = sprintf($this->objNarroSuggestionComment->Modified) ? $this->objNarroSuggestionComment->Modified->qFormat($this->strModifiedDateTimeFormat) : null;
			return $this->lblModified;
		}

		protected $strModifiedDateTimeFormat;




		/**
		 * Refresh this MetaControl with Data from the local NarroSuggestionComment object.
		 * @param boolean $blnReload reload NarroSuggestionComment from the database
		 * @return void
		 */
		public function Refresh($blnReload = false) {
			if ($blnReload)
				$this->objNarroSuggestionComment->Reload();

			if ($this->lblCommentId) if ($this->blnEditMode) $this->lblCommentId->Text = $this->objNarroSuggestionComment->CommentId;

			if ($this->lstSuggestion) {
					$this->lstSuggestion->RemoveAllItems();
				if (!$this->blnEditMode)
					$this->lstSuggestion->AddItem(QApplication::Translate('- Select One -'), null);
				$objSuggestionArray = NarroSuggestion::LoadAll();
				if ($objSuggestionArray) foreach ($objSuggestionArray as $objSuggestion) {
					$objListItem = new QListItem($objSuggestion->__toString(), $objSuggestion->SuggestionId);
					if (($this->objNarroSuggestionComment->Suggestion) && ($this->objNarroSuggestionComment->Suggestion->SuggestionId == $objSuggestion->SuggestionId))
						$objListItem->Selected = true;
					$this->lstSuggestion->AddItem($objListItem);
				}
			}
			if ($this->lblSuggestionId) $this->lblSuggestionId->Text = ($this->objNarroSuggestionComment->Suggestion) ? $this->objNarroSuggestionComment->Suggestion->__toString() : null;

			if ($this->lstUser) {
					$this->lstUser->RemoveAllItems();
				if (!$this->blnEditMode)
					$this->lstUser->AddItem(QApplication::Translate('- Select One -'), null);
				$objUserArray = NarroUser::LoadAll();
				if ($objUserArray) foreach ($objUserArray as $objUser) {
					$objListItem = new QListItem($objUser->__toString(), $objUser->UserId);
					if (($this->objNarroSuggestionComment->User) && ($this->objNarroSuggestionComment->User->UserId == $objUser->UserId))
						$objListItem->Selected = true;
					$this->lstUser->AddItem($objListItem);
				}
			}
			if ($this->lblUserId) $this->lblUserId->Text = ($this->objNarroSuggestionComment->User) ? $this->objNarroSuggestionComment->User->__toString() : null;

			if ($this->txtCommentText) $this->txtCommentText->Text = $this->objNarroSuggestionComment->CommentText;
			if ($this->lblCommentText) $this->lblCommentText->Text = $this->objNarroSuggestionComment->CommentText;

			if ($this->txtCommentTextMd5) $this->txtCommentTextMd5->Text = $this->objNarroSuggestionComment->CommentTextMd5;
			if ($this->lblCommentTextMd5) $this->lblCommentTextMd5->Text = $this->objNarroSuggestionComment->CommentTextMd5;

			if ($this->calCreated) $this->calCreated->DateTime = $this->objNarroSuggestionComment->Created;
			if ($this->lblCreated) $this->lblCreated->Text = sprintf($this->objNarroSuggestionComment->Created) ? $this->objNarroSuggestionComment->Created->qFormat($this->strCreatedDateTimeFormat) : null;

			if ($this->calModified) $this->calModified->DateTime = $this->objNarroSuggestionComment->Modified;
			if ($this->lblModified) $this->lblModified->Text = sprintf($this->objNarroSuggestionComment->Modified) ? $this->objNarroSuggestionComment->Modified->qFormat($this->strModifiedDateTimeFormat) : null;

		}



		///////////////////////////////////////////////
		// PROTECTED UPDATE METHODS for ManyToManyReferences (if any)
		///////////////////////////////////////////////





		///////////////////////////////////////////////
		// PUBLIC NARROSUGGESTIONCOMMENT OBJECT MANIPULATORS
		///////////////////////////////////////////////

		/**
		 * This will save this object's NarroSuggestionComment instance,
		 * updating only the fields which have had a control created for it.
		 */
		public function SaveNarroSuggestionComment() {
			try {
				// Update any fields for controls that have been created
				if ($this->lstSuggestion) $this->objNarroSuggestionComment->SuggestionId = $this->lstSuggestion->SelectedValue;
				if ($this->lstUser) $this->objNarroSuggestionComment->UserId = $this->lstUser->SelectedValue;
				if ($this->txtCommentText) $this->objNarroSuggestionComment->CommentText = $this->txtCommentText->Text;
				if ($this->txtCommentTextMd5) $this->objNarroSuggestionComment->CommentTextMd5 = $this->txtCommentTextMd5->Text;
				if ($this->calCreated) $this->objNarroSuggestionComment->Created = $this->calCreated->DateTime;
				if ($this->calModified) $this->objNarroSuggestionComment->Modified = $this->calModified->DateTime;

				// Update any UniqueReverseReferences (if any) for controls that have been created for it

				// Save the NarroSuggestionComment object
				$this->objNarroSuggestionComment->Save();

				// Finally, update any ManyToManyReferences (if any)
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}

		/**
		 * This will DELETE this object's NarroSuggestionComment instance from the database.
		 * It will also unassociate itself from any ManyToManyReferences.
		 */
		public function DeleteNarroSuggestionComment() {
			$this->objNarroSuggestionComment->Delete();
		}		



		///////////////////////////////////////////////
		// PUBLIC GETTERS and SETTERS
		///////////////////////////////////////////////

		/**
		 * Override method to perform a property "Get"
		 * This will get the value of $strName
		 *
		 * @param string $strName Name of the property to get
		 * @return mixed
		 */
		public function __get($strName) {
			switch ($strName) {
				// General MetaControlVariables
				case 'NarroSuggestionComment': return $this->objNarroSuggestionComment;
				case 'TitleVerb': return $this->strTitleVerb;
				case 'EditMode': return $this->blnEditMode;

				// Controls that point to NarroSuggestionComment fields -- will be created dynamically if not yet created
				case 'CommentIdControl':
					if (!$this->lblCommentId) return $this->lblCommentId_Create();
					return $this->lblCommentId;
				case 'CommentIdLabel':
					if (!$this->lblCommentId) return $this->lblCommentId_Create();
					return $this->lblCommentId;
				case 'SuggestionIdControl':
					if (!$this->lstSuggestion) return $this->lstSuggestion_Create();
					return $this->lstSuggestion;
				case 'SuggestionIdLabel':
					if (!$this->lblSuggestionId) return $this->lblSuggestionId_Create();
					return $this->lblSuggestionId;
				case 'UserIdControl':
					if (!$this->lstUser) return $this->lstUser_Create();
					return $this->lstUser;
				case 'UserIdLabel':
					if (!$this->lblUserId) return $this->lblUserId_Create();
					return $this->lblUserId;
				case 'CommentTextControl':
					if (!$this->txtCommentText) return $this->txtCommentText_Create();
					return $this->txtCommentText;
				case 'CommentTextLabel':
					if (!$this->lblCommentText) return $this->lblCommentText_Create();
					return $this->lblCommentText;
				case 'CommentTextMd5Control':
					if (!$this->txtCommentTextMd5) return $this->txtCommentTextMd5_Create();
					return $this->txtCommentTextMd5;
				case 'CommentTextMd5Label':
					if (!$this->lblCommentTextMd5) return $this->lblCommentTextMd5_Create();
					return $this->lblCommentTextMd5;
				case 'CreatedControl':
					if (!$this->calCreated) return $this->calCreated_Create();
					return $this->calCreated;
				case 'CreatedLabel':
					if (!$this->lblCreated) return $this->lblCreated_Create();
					return $this->lblCreated;
				case 'ModifiedControl':
					if (!$this->calModified) return $this->calModified_Create();
					return $this->calModified;
				case 'ModifiedLabel':
					if (!$this->lblModified) return $this->lblModified_Create();
					return $this->lblModified;
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
			try {
				switch ($strName) {
					// Controls that point to NarroSuggestionComment fields
					case 'CommentIdControl':
						return ($this->lblCommentId = QType::Cast($mixValue, 'QControl'));
					case 'SuggestionIdControl':
						return ($this->lstSuggestion = QType::Cast($mixValue, 'QControl'));
					case 'UserIdControl':
						return ($this->lstUser = QType::Cast($mixValue, 'QControl'));
					case 'CommentTextControl':
						return ($this->txtCommentText = QType::Cast($mixValue, 'QControl'));
					case 'CommentTextMd5Control':
						return ($this->txtCommentTextMd5 = QType::Cast($mixValue, 'QControl'));
					case 'CreatedControl':
						return ($this->calCreated = QType::Cast($mixValue, 'QControl'));
					case 'ModifiedControl':
						return ($this->calModified = QType::Cast($mixValue, 'QControl'));
					default:
						return parent::__set($strName, $mixValue);
				}
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}
		}
	}
?>