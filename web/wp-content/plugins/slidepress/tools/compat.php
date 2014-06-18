<?php
// Wrapper for settings_fields function which doesn't exist in wordpress MU 2.6.5
if ( ! function_exists( 'settings_fields' ) ) {
	function settings_fields( $option_group ) {
		echo "<input type='hidden' name='option_page' value='{$option_group}' />";
		echo '<input type="hidden" name="action" value="update" />';
		wp_nonce_field( "{$option_group}-options" );
	}
}