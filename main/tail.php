	<? if($DEV_IP){ ?>
		<div class="dev-info">
			<?=DEV_PATH($INCLUDE_CONTENTS)?>
			<?=DEV_PARAMETER($_POST, $_GET)?>
			<?=DEV_INFO()?>
		</div>
	<? } ?>
</body>
