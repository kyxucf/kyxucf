<?php 
	////
	// This is a passthrough file. Requests
	// are handled by the images.php file
	// in app/webroot/
	////
	
	error_reporting(0);
	$ds = DIRECTORY_SEPARATOR;
	require dirname(__FILE__) . $ds . 'app' . $ds . 'webroot' . $ds . 'images.php';
	
?>