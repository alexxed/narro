<?php
	/**
	 * This is a quick-and-dirty draft QForm object to do Create, Edit, and Delete functionality
	 * of the NarroUserRole class.  It uses the code-generated
	 * NarroUserRoleMetaControl class, which has meta-methods to help with
	 * easily creating/defining controls to modify the fields of a NarroUserRole columns.
	 *
	 * Any display customizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 * 
	 * NOTE: This file is overwritten on any code regenerations.  If you want to make
	 * permanent changes, it is STRONGLY RECOMMENDED to move both narro_user_role_edit.php AND
	 * narro_user_role_edit.tpl.php out of this Form Drafts directory.
	 *
	 * @package Narro
	 * @subpackage FormBaseObjects
	 */
	abstract class NarroUserRoleEditFormBase extends QForm {
		// Local instance of the NarroUserRoleMetaControl
		/**
		 * @var NarroUserRoleMetaControlGen mctNarroUserRole
		 */
		protected $mctNarroUserRole;

		// Controls for NarroUserRole's Data Fields
		protected $lblUserRoleId;
		protected $lstUser;
		protected $lstRole;
		protected $lstProject;
		protected $lstLanguage;

		// Other ListBoxes (if applicable) via Unique ReverseReferences and ManyToMany References

		// Other Controls
		/**
		 * @var QButton Save
		 */
		protected $btnSave;
		/**
		 * @var QButton Delete
		 */
		protected $btnDelete;
		/**
		 * @var QButton Cancel
		 */
		protected $btnCancel;

		// Create QForm Event Handlers as Needed

//		protected function Form_Exit() {}
//		protected function Form_Load() {}
//		protected function Form_PreRender() {}

		protected function Form_Run() {
			parent::Form_Run();
		}

		protected function Form_Create() {
			parent::Form_Create();

			// Use the CreateFromPathInfo shortcut (this can also be done manually using the NarroUserRoleMetaControl constructor)
			// MAKE SURE we specify "$this" as the MetaControl's (and thus all subsequent controls') parent
			$this->mctNarroUserRole = NarroUserRoleMetaControl::CreateFromPathInfo($this);

			// Call MetaControl's methods to create qcontrols based on NarroUserRole's data fields
			$this->lblUserRoleId = $this->mctNarroUserRole->lblUserRoleId_Create();
			$this->lstUser = $this->mctNarroUserRole->lstUser_Create();
			$this->lstRole = $this->mctNarroUserRole->lstRole_Create();
			$this->lstProject = $this->mctNarroUserRole->lstProject_Create();
			$this->lstLanguage = $this->mctNarroUserRole->lstLanguage_Create();

			// Create Buttons and Actions on this Form
			$this->btnSave = new QButton($this);
			$this->btnSave->Text = QApplication::Translate('Save');
			$this->btnSave->AddAction(new QClickEvent(), new QAjaxAction('btnSave_Click'));
			$this->btnSave->CausesValidation = true;

			$this->btnCancel = new QButton($this);
			$this->btnCancel->Text = QApplication::Translate('Cancel');
			$this->btnCancel->AddAction(new QClickEvent(), new QAjaxAction('btnCancel_Click'));

			$this->btnDelete = new QButton($this);
			$this->btnDelete->Text = QApplication::Translate('Delete');
			$this->btnDelete->AddAction(new QClickEvent(), new QConfirmAction(sprintf(QApplication::Translate('Are you SURE you want to DELETE this %s?'), QApplication::Translate('NarroUserRole'))));
			$this->btnDelete->AddAction(new QClickEvent(), new QAjaxAction('btnDelete_Click'));
			$this->btnDelete->Visible = $this->mctNarroUserRole->EditMode;
		}

		/**
		 * This Form_Validate event handler allows you to specify any custom Form Validation rules.
		 * It will also Blink() on all invalid controls, as well as Focus() on the top-most invalid control.
		 */
		protected function Form_Validate() {
			// By default, we report the result of validation from the parent
			$blnToReturn = parent::Form_Validate();

			// Custom Validation Rules
			// TODO: Be sure to set $blnToReturn to false if any custom validation fails!
			// Check for records that may violate Unique Clauses
			if (($objNarroUserRole = NarroUserRole::LoadByUserIdRoleIdProjectIdLanguageId($this->lstUser->SelectedValue,$this->lstRole->SelectedValue,$this->lstProject->SelectedValue,$this->lstLanguage->SelectedValue)) && ($objNarroUserRole->UserRoleId != $this->mctNarroUserRole->NarroUserRole->UserRoleId )){
				$blnToReturn = false;
				$this->lstUser->Warning = QApplication::Translate("Already in Use");
				$this->lstRole->Warning = QApplication::Translate("Already in Use");
				$this->lstProject->Warning = QApplication::Translate("Already in Use");
				$this->lstLanguage->Warning = QApplication::Translate("Already in Use");
			}

			$blnFocused = false;
			foreach ($this->GetErrorControls() as $objControl) {
				// Set Focus to the top-most invalid control
				if (!$blnFocused) {
					$objControl->Focus();
					$blnFocused = true;
				}

				// Blink on ALL invalid controls
				$objControl->Blink();
			}

			return $blnToReturn;
		}

		// Button Event Handlers

		protected function btnSave_Click($strFormId, $strControlId, $strParameter) {
			// Delegate "Save" processing to the NarroUserRoleMetaControl
			$this->mctNarroUserRole->SaveNarroUserRole();
			$this->RedirectToListPage();
		}

		protected function btnDelete_Click($strFormId, $strControlId, $strParameter) {
			// Delegate "Delete" processing to the NarroUserRoleMetaControl
			$this->mctNarroUserRole->DeleteNarroUserRole();
			$this->RedirectToListPage();
		}

		protected function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			$this->RedirectToListPage();
		}

		// Other Methods

		protected function RedirectToListPage() {
			QApplication::Redirect(__VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__ . '/narro_user_role_list.php');
		}
	}
?>
