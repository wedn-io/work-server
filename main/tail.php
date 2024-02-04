	<? if($_DEV){ ?>
		<div class="dev-info">
			<?=DEV_PATH($INCLUDE_CONTENTS)?>
			<?=DEV_INFO()?>
			<?=DEV_PARAMETER($_POST, $_GET)?>
		</div>
	<? } ?>
</body>
