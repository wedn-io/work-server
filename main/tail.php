	<? if($_DEV){ ?>
		<div class="dev-info">
			<?=DEV_PATH($INCLUDE_CONTENTS)?>
			<?=DEV_INFO()?>
			<?=DEV_PARAMETER($_POST, $_GET)?>
			<?=DEV_SESSION()?>
			<?
				error_reporting( E_ALL );
				ini_set( "display_errors", 1 );
			?>
		</div>
	<? } ?>
</body>
