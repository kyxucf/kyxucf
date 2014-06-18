<?php
	
	$force = false;
	if (isset($_GET['all']) && $_GET['all'] == 1) { $force = true; }
	
	if (!defined('ALBUM_DIR')) {
		define('ALBUM_DIR', 'albums');
	}
	$ds = DIRECTORY_SEPARATOR;
	$albums = dirname(dirname(dirname(__FILE__))) . $ds . ALBUM_DIR;
	
	if (isset($_GET['all']) && $_GET['all'] == 'xml') { 
		$force = true; 
		$dir = dirname(dirname(dirname(__FILE__)));
		$files = glob($dir . $ds . 'xml_cache' . $ds . '*');
		$internals = glob($dir . $ds . 'app' . $ds . 'tmp' . $ds . 'cache' . $ds . 'xml' . $ds . '*');
		$files = array_merge($files, $internals);
	} elseif (isset($_GET['all']) && $_GET['all'] == 'internal') {
		$force = true; 
		$dir = dirname(dirname(dirname(__FILE__))) . $ds . 'app' . $ds . 'tmp' . $ds . 'cache' . $ds . '*';
		$files = glob($dir . $ds . '*');
	} else {
		$files = glob($albums . $ds . '*' . $ds . 'cache' . $ds . '*');
	}

	foreach ($files as $file) {
		if ((fileatime($file) < strtotime('-1 week')) || $force) {
			@unlink($file);
		}
	}

?>