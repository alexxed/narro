<?php

$pnlEditor = $_FORM->GetControl($_CONTROL->ControlId . 'i' . $_ITEM->ContextInfoId);
if (!$pnlEditor) {
    $pnlEditor = new NarroContextInfoEditor($_CONTROL, $_CONTROL->ControlId . 'i' . $_ITEM->ContextInfoId, $_ITEM);
    $pnlEditor->Translation->AddAction(new QFocusEvent(), new QJavaScriptAction(
            sprintf("if  (jQuery(window).scrollTop() + 200 > jQuery(document).height() - jQuery(window).height() && jQuery('#endReached').attr('checked') == false) qc.pA('%s', '%s', 'QClickEvent', '%s', '%s')", $_CONTROL->Form->FormId, $_CONTROL->ParentControl->btnMore->ControlId, $pnlEditor->Translation->ControlId, $_CONTROL->ParentControl->objWaitIcon->ControlId)));
}

$pnlEditor->Index = ($_CONTROL->CurrentItemIndex + 1) . '/' . $_CONTROL->ParentControl->intTotalItemCount;

if ($_CONTROL->ParentControl->intTotalItemCount == $_CONTROL->CurrentItemIndex + 1) {
    $pnlEditor->SaveButton->DisplayStyle = QDisplayStyle::Inline;
}

if (($_CONTROL->CurrentItemIndex % 2) == 0)
    $pnlEditor->CssClass = 'narro_context_info_editor odd';
else
    $pnlEditor->CssClass = 'narro_context_info_editor even';

$pnlEditor->Render();
?>