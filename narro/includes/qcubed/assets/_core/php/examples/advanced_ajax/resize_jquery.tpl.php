<?php require('../includes/header.inc.php'); ?>
	<?php $this->RenderBegin(); ?>

	<div class="instructions">
		<h1 class="instruction_title">Resizing Block Controls</h1>
		This example shows how you can setup multiple positioned panels to create a user
		interface with resizable panels.  Only <b>QBlockControls</b> are capable of being resized.  And moreover, <b>QBlockControls</b>
		are the only controls that are capable of being "resize handles".<br/><br/>

		A "resize handle" is simply a block control which is assigned to handle the resizing of 
		another block control.  So in our example below, we have three generic block controls:
		<b>pnlLeftTop</b>, <b>pnlLeftBottom</b>, and <b>pnlRight</b>, which corresponds to the three blocks of text
		in the Left Top, Left Bottom, and Right.<br/><br/>
		
		There are also two block controls, <b>pnlVerticalResizer</b> and <b>pnlHorizontalResizer</b>, which are
		smaller/thinner <b>QPanels</b>, that have been set up to handle the resizing of the other
		three text-filled panels.<br/><br/>
		
		Note how you can clock and drag this thin pannels to resize the larger ones.
		Also note that we have setup <b>ResizeHandleMinimum</b> and <b>ResizeHandleMaximum</b> values
		on both resizers.
	</div>

		<p><?php $this->pnlLeftTop->Render('BackColor=#f0e0ff','BorderColor=#9966cc','BorderWidth=1px 1px 1px 1px'); ?></p>
		<p><?php $this->txtTextbox->Render(); ?></p>

	<?php $this->RenderEnd(); ?>
<?php require('../includes/footer.inc.php'); ?>
