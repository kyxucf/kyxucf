<?php
define('WP_ADMIN', true);
$path = ( defined('ABSPATH') ? ABSPATH : dirname(dirname(dirname(dirname(__FILE__)))) . '/');
	
if ( defined('ABSPATH') ){
	require_once(ABSPATH . 'wp-load.php');
	}else{
	require_once($path.'wp-load.php');
}
// Flash often fails to send cookies with the POST or upload, so we need to pass it in GET or POST instead
if ( is_ssl() && empty($_COOKIE[SECURE_AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[SECURE_AUTH_COOKIE] = $_REQUEST['auth_cookie'];
elseif ( empty($_COOKIE[AUTH_COOKIE]) && !empty($_REQUEST['auth_cookie']) )
	$_COOKIE[AUTH_COOKIE] = $_REQUEST['auth_cookie'];
unset($current_user);
require_once($path.'wp-admin/admin.php');

if ( !current_user_can('upload_files') )
	wp_die(__('You do not have permission to upload files.'));

check_admin_referer('slideshowpro.swf-upload');

function slidepress_handle_upload_error(&$file, $message) {
	return $message;
}

function slidepress_handle_upload( &$file ) {
	global $slidepress;

	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'slidepress_handle_upload_error';

	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'slidepress_handle_upload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the <code>upload_max_filesize</code> directive in <code>php.ini</code>." ),
		__( "The uploaded file exceeds the <em>MAX_FILE_SIZE</em> directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		'',
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ),
		__( "File upload stopped by extension." ));

	// All tests are on by default. Most can be turned off by $override[{test_name}] = false;
	$test_form = false;
	$test_size = true;

	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;
	$mimes = false;

	// A correct form post will pass this test.
	if ( $test_form && (!isset( $_POST['action'] ) || ($_POST['action'] != $action ) ) )
		return $upload_error_handler( $file, __( 'Invalid form submission.' ));

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
		return $upload_error_handler( $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !($file['size'] > 0 ) )
		return $upload_error_handler( $file, __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini.' ));

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if (! @ is_uploaded_file( $file['tmp_name'] ) )
		return $upload_error_handler( $file, __( 'Specified file failed upload test.' ));
		
	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype( $file['name'], $mimes );

		extract( $wp_filetype );

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return $upload_error_handler( $file, __( 'File type does not meet security guidelines. Try another.' ));

		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');

		if ( !$type )
			$type = $file['type'];
	} else {
		$type = '';
	}

	$destination = $slidepress->upload_path . 'flash/';
	$new_file = $destination . 'slideshowpro.swf';

	if ( false === @ move_uploaded_file( $file['tmp_name'], $new_file ) ) {
		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $destination ) );
	}

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	return 'success';
}

$result = slidepress_handle_upload( $_FILES['ssp-swf'] );

header('Content-Type: plain/text');
echo $result;
?>