<?php

$pnlEditor = $_FORM->GetControl('i' . $_ITEM->ContextInfoId);
if (!$pnlEditor) {
    $pnlEditor = new NarroContextInfoEditor($_CONTROL, 'i' . $_ITEM->ContextInfoId, $_ITEM);
    $pnlEditor->Translation->AddAction(new QFocusEvent(), new QJavaScriptAction(
            sprintf("if  (jQuery(window).scrollTop() + 200 > jQuery(document).height() - jQuery(window).height() && jQuery('#endReached').attr('checked') == false) qc.pA('%s', '%s', 'QClickEvent', '%s', '%s')", $_CONTROL->Form->FormId, $_CONTROL->ParentControl->btnMore->ControlId, $pnlEditor->Translation->ControlId, $_CONTROL->ParentControl->objWaitIcon->ControlId)));
}

$intCurrentItem = $_CONTROL->CurrentItemIndex + 1 + $_CONTROL->ParentControl->intStart;

$pnlEditor->Index = sprintf(
    '<a href="%s&i=%d#i%d" id="#i%d" tabindex="-1">%s</a>',
    NarroLink::Translate(
        $_CONTROL->ParentControl->lstProject->SelectedValue,
        $_CONTROL->ParentControl->txtFile->Text,
        $_CONTROL->ParentControl->lstFilter->SelectedValue,
        $_CONTROL->ParentControl->txtSearch->Text,
        $_CONTROL->ParentControl->lstSort->SelectedValue,
        $_CONTROL->ParentControl->lstSortDir->SelectedValue,
        $intCurrentItem - 1,
        $_ITEM->ContextInfoId
    ),
    $_ITEM->ContextInfoId,
    ($intCurrentItem) . '/' . $_CONTROL->ParentControl->intTotalItemCount
);

if ($_CONTROL->ParentControl->intTotalItemCount == $_CONTROL->CurrentItemIndex + 1) {
    $pnlEditor->SaveButton->DisplayStyle = QDisplayStyle::Inline;
}

if ($pnlEditor->Translation->Warning != '')
    $pnlEditor->CssClass = 'narro_context_info_editor narro_context_info_editor_selected';
else
    $pnlEditor->CssClass = 'narro_context_info_editor';

if (($_CONTROL->CurrentItemIndex % 2) == 0)
    $pnlEditor->CssClass .= ' odd';
else
    $pnlEditor->CssClass .= ' even';

$pnlEditor->RenderWithError();

?>