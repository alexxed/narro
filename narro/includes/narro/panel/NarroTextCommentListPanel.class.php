<?php
    /**
     * This is the abstract Panel class for the List All functionality
     * of the NarroTextComment class.  This code-generated class
     * contains a datagrid to display an HTML page that can
     * list a collection of NarroTextComment objects.  It includes
     * functionality to perform pagination and sorting on columns.
     *
     * To take advantage of some (or all) of these control objects, you
     * must create a new QPanel which extends this NarroTextCommentListPanelBase
     * class.
     *
     * Any and all changes to this file will be overwritten with any subsequent re-
     * code generation.
     *
     * @package Narro
     * @subpackage Drafts
     *
     */
    class NarroTextCommentListPanel extends QPanel {
        // Local instance of the Meta DataGrid to list NarroTextComments
        public $dtgTextComments;

        // Other public QControls in this panel
        public $btnCreateNew;
        public $pxyEdit;

        // Callback Method Names
        protected $strSetEditPanelMethod;
        protected $strCloseEditPanelMethod;

        public function __construct($objParentObject, $strSetEditPanelMethod, $strCloseEditPanelMethod, $strControlId = null) {
            // Call the Parent
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (QCallerException $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            // Record Method Callbacks
            $this->strSetEditPanelMethod = $strSetEditPanelMethod;
            $this->strCloseEditPanelMethod = $strCloseEditPanelMethod;

            // Setup the Template
            $this->Template = dirname(__FILE__) . '/NarroTextCommentListPanel.tpl.php';

            // Instantiate the Meta DataGrid
            $this->dtgTextComments = new NarroTextCommentDataGrid($this);

            // Style the DataGrid (if desired)
            $this->dtgTextComments->CssClass = 'datagrid';
            $this->dtgTextComments->AlternateRowStyle->CssClass = 'alternate';

            // Add Pagination (if desired)
            $this->dtgTextComments->Paginator = new QPaginator($this->dtgTextComments);
            $this->dtgTextComments->ItemsPerPage = 8;
            $this->dtgTextComments->AdditionalConditions = QQ::AndCondition(QQ::Equal(QQN::NarroTextComment()->LanguageId, QApplication::GetLanguageId()));

            // Create the Other Columns (note that you can use strings for narro_text_comment's properties, or you
            // can traverse down QQN::narro_text_comment() to display fields that are down the hierarchy)
            $colText = $this->dtgTextComments->MetaAddColumn(QQN::NarroTextComment()->Text->TextValue);
            $colText->Name = t('Text');
            $colText->Html = '<?= $_CONTROL->ParentControl->dtgTextComments_colText_Render($_ITEM) ?>';

            $colUser = $this->dtgTextComments->MetaAddColumn(QQN::NarroTextComment()->User->Username);
            $colUser->Name = t('User');
            $colUser->Html = '<?= $_CONTROL->ParentControl->dtgTextComments_colUser_Render($_ITEM) ?>';
            $colUser->HtmlEntities = false;

            $colCreated = $this->dtgTextComments->MetaAddColumn('Created');
            $colCreated->Html = '<?= $_CONTROL->ParentControl->dtgTextComments_colCreated_Render($_ITEM) ?>';
            $colCreated->Name = t('Added');
            $colCreated->Filter = null;
            $colCreated->FilterType = null;

            $colComment = $this->dtgTextComments->MetaAddColumn('CommentText');
            $colComment->Html = '<?= $_CONTROL->ParentControl->dtgTextComments_colComment_Render($_ITEM) ?>';
            $colComment->Name = t('Comment');
            $colComment->FilterBoxSize = 50;

            $colUsedIn = new QDataGridColumn(t('Found in'));
            $colUsedIn->Html = '<?= $_CONTROL->ParentControl->dtgTextComments_colUsedIn_Render($_ITEM) ?>';
            $colUsedIn->HtmlEntities = false;
            $colUsedIn->Width = 200;

            $this->dtgTextComments->AddColumn($colUsedIn);
        }

        public function dtgTextComments_colUsedIn_Render(NarroTextComment $objTextComment) {
            $arrProjects = NarroProject::QueryArray(QQ::Equal(QQN::NarroProject()->NarroContextAsProject->TextId, $objTextComment->TextId), QQ::Distinct());
            $strText = '';

            if (is_array($arrProjects))
                foreach($arrProjects as $objProject) {
                    $strText .= NarroLink::ProjectTextList($objProject->ProjectId, NarroTextListPanel::SHOW_ALL_TEXTS, NarroTextListPanel::SEARCH_TEXTS, "'" . $objTextComment->Text->TextValue . "'", $objProject->ProjectName) . '<br />';
                }

            return $strText;
        }

        public function dtgTextComments_colComment_Render(NarroTextComment $objTextComment) {
            return nl2br(NarroString::HtmlEntities($objTextComment->CommentText));
        }

        public function dtgTextComments_colText_Render(NarroTextComment $objTextComment) {
            return nl2br(NarroString::HtmlEntities($objTextComment->Text->TextValue));
        }

        public function dtgTextComments_colUser_Render(NarroTextComment $objTextComment) {
            return NarroLink::UserProfile($objTextComment->UserId, $objTextComment->User->Username);
        }

        public function dtgTextComments_colCreated_Render(NarroTextComment $objTextComment) {
            $objDateSpan = new QDateTimeSpan(time() - strtotime($objTextComment->Created));
            return sprintf(t('%s ago'), $objDateSpan->SimpleDisplay());
        }

    }
?>