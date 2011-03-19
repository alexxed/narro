<?php
	/**
	 * This is a quick-and-dirty draft QForm object to do the List All functionality
	 * of the NarroSuggestionComment class.  It uses the code-generated
	 * NarroSuggestionCommentDataGrid control which has meta-methods to help with
	 * easily creating/defining NarroSuggestionComment columns.
	 *
	 * Any display customizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 * 
	 * NOTE: This file is overwritten on any code regenerations.  If you want to make
	 * permanent changes, it is STRONGLY RECOMMENDED to move both narro_suggestion_comment_list.php AND
	 * narro_suggestion_comment_list.tpl.php out of this Form Drafts directory.
	 *
	 * @package Narro
	 * @subpackage FormBaseObjects
	 */
	abstract class NarroSuggestionCommentListFormBase extends QForm {
		// Local instance of the Meta DataGrid to list NarroSuggestionComments
		/**
		 * @var NarroSuggestionCommentDataGrid dtgNarroSuggestionComments
		 */
		protected $dtgNarroSuggestionComments;

		// Create QForm Event Handlers as Needed

//		protected function Form_Exit() {}
//		protected function Form_Load() {}
//		protected function Form_PreRender() {}
//		protected function Form_Validate() {}

		protected function Form_Run() {
			parent::Form_Run();
		}

		protected function Form_Create() {
			parent::Form_Create();

			// Instantiate the Meta DataGrid
			$this->dtgNarroSuggestionComments = new NarroSuggestionCommentDataGrid($this);

			// Style the DataGrid (if desired)
			$this->dtgNarroSuggestionComments->CssClass = 'datagrid';
			$this->dtgNarroSuggestionComments->AlternateRowStyle->CssClass = 'alternate';

			// Add Pagination (if desired)
			$this->dtgNarroSuggestionComments->Paginator = new QPaginator($this->dtgNarroSuggestionComments);
			$this->dtgNarroSuggestionComments->ItemsPerPage = 20;

			// Use the MetaDataGrid functionality to add Columns for this datagrid

			// Create an Edit Column
			$strEditPageUrl = __VIRTUAL_DIRECTORY__ . __FORM_DRAFTS__ . '/narro_suggestion_comment_edit.php';
			$this->dtgNarroSuggestionComments->MetaAddEditLinkColumn($strEditPageUrl, 'Edit', 'Edit');

			// Create the Other Columns (note that you can use strings for narro_suggestion_comment's properties, or you
			// can traverse down QQN::narro_suggestion_comment() to display fields that are down the hierarchy)
			$this->dtgNarroSuggestionComments->MetaAddColumn('CommentId');
			$this->dtgNarroSuggestionComments->MetaAddColumn(QQN::NarroSuggestionComment()->Suggestion);
			$this->dtgNarroSuggestionComments->MetaAddColumn(QQN::NarroSuggestionComment()->User);
			$this->dtgNarroSuggestionComments->MetaAddColumn('CommentText');
			$this->dtgNarroSuggestionComments->MetaAddColumn('CommentTextMd5');
			$this->dtgNarroSuggestionComments->MetaAddColumn('Created');
			$this->dtgNarroSuggestionComments->MetaAddColumn('Modified');
		}
	}
?>
