<h1>Live Simple Clock</h1>
<?php settings_errors(); ?>
<form method="post" action="options.php">
	<?php settings_fields( 'tka-lsc-setting-group' ); ?>
	<?php do_settings_sections( 'setting_lcs.php' ); ?>
	<?php submit_button(); ?>
</form>
