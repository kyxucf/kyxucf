<?php
	
	error_reporting(0);
	
	define('USE_X_SEND', false);
	
	$val = $_GET['a'];
	if (strpos($val, 'http://') !== false || substr($val, 0, 1) == '/') {
		header('Location: ' . $val);
		exit;
	} else {
		$val = str_replace(' ', '%2B', $val);
	}
	
	function n($var, $default = false) {
		$var = trim($var);
		if (is_numeric($var)) {
			return $var;
		} else if (is_numeric($default)) {
			return $default;
		} else {
			exit;
		}
	}
	
	function returnExt($file) {
		$pos = strrpos($file, '.');
		return strtolower(substr($file, $pos+1, strlen($file)));
	}
	
	define('ROOT', dirname(dirname(dirname(__FILE__))));
	define('DS', DIRECTORY_SEPARATOR);
	
	@include(ROOT . DS . 'config' . DS . 'user_setup.php');
	include_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'director' . DS . 'salt.php');
	
	if (!defined('ALBUM_DIR')) {
		define('ALBUM_DIR', 'albums');
	}
	
	$crypt = convert($val, false);
	$a = explode(',', $crypt);
	$file = $fn = basename($a[0]);
	
	// Make sure supplied filename contains only approved chars
	if (preg_match("/[^A-Za-z0-9._-\s]/", $file)) {
		header('HTTP/1.1 403 Forbidden'); 
		exit;
	}
	
	$aid = $a[1];
	$w = n($a[2]);
	$h = n($a[3]);
	$s = n($a[4]);
	$q = n($a[5], 100);
	$sh = n($a[6], 0);
	$x = n($a[7], 50);
	$y = n($a[8], 50);
	$force = n($a[9], 0);
	$w_id = n($a[10]);
	$w_location = n($a[11]);
	$w_opacity = n($a[12]);
	
	if (isset($_GET['full'])) {
		list($w, $h) = explode(',', $_GET['full']);
		$w = n($w);
		$h = n($h);
	}
	
	$ext = returnExt($file);
	
	if (strpos($aid, 'avatar') !== false) {
		$bits = explode('-', $aid);
		$id = $bits[1];
		if (!is_numeric($id)) {
			exit;
		}
		define('PATH', ROOT . DS . ALBUM_DIR . DS . 'avatars' . DS . $id);
		$original = PATH . DS . $file;
		$base_dir = PATH;
	} else {
		if (!is_numeric($aid)) {
			exit;
		}
		define('PATH', ROOT . DS . ALBUM_DIR . DS . 'album-' . $aid);
		$original = PATH . DS . 'lg' . DS . $file;
		$base_dir = PATH . DS . 'lg';
	}

	if ($s == 2) {
		$path_to_cache = $original;
	} else {
		$fn .= "_{$w}_{$h}_{$s}_{$q}_{$sh}_{$x}_{$y}";
		if ($w_id != 0) {
			$fn .= "_{$w_id}_{$w_location}_{$w_opacity}";
		}
		$fn .= ".$ext";
		$base_dir = PATH . DS . 'cache';
		$path_to_cache = PATH . DS . 'cache' . DS . $fn;
	}
	
	// Make sure dirname of the cached copy is sane
	if (dirname($path_to_cache) !== $base_dir) {
		header('HTTP/1.1 403 Forbidden'); 
		exit;
	}

	$noob = false;

	if (!file_exists($path_to_cache)) {
		$noob = true;
		if ($s == 2) {
			copy($original, $path_to_cache);
		} else {
			if (!defined('MAGICK_PATH')) {
				define('MAGICK_PATH_FINAL', 'convert');
			} else if (strpos(strtolower(MAGICK_PATH), 'c:\\') !== false) {
				define('MAGICK_PATH_FINAL', '"' . MAGICK_PATH . '"');	
			} else {
				define('MAGICK_PATH_FINAL', MAGICK_PATH);	
			}
			if (!defined('FORCE_GD')) {
				define('FORCE_GD', 0);
			}
			if (!is_dir(dirname($path_to_cache))) {
				$parent_perms = substr(sprintf('%o', fileperms(dirname(dirname($path_to_cache)))), -4);
				$old = umask(0);
				mkdir(dirname($path_to_cache), octdec($parent_perms));
				umask($old);
			}
			require(ROOT . DS . 'app' . DS . 'vendors' . DS . 'bradleyboy' . DS . 'darkroom.php');
			$d = new Darkroom;
			$d->develop($original, $path_to_cache, $w, $h, $q, $s, null, $sh, $x, $y, $force, $w_id, $w_location, $w_opacity); 
		}
	}

	$mtime = filemtime($path_to_cache);
	$etag = md5($path_to_cache . $mtime);
	
	if (!$noob) {
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && ($_SERVER['HTTP_IF_NONE_MATCH'] == $etag)) {
			header("HTTP/1.1 304 Not Modified");
		    exit;
		}
	
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= filemtime($path_to_cache))) {
			header("HTTP/1.1 304 Not Modified");
		    exit;
		}	
	}
	
	$disabled_functions = explode(',', str_replace(' ', '', ini_get('disable_functions')));

	if (USE_X_SEND) {
		header("X-Sendfile: $path_to_cache");
	} else {
		$specs = getimagesize($path_to_cache);
		header('Content-type: ' . $specs['mime']);
		header('Content-length: ' . filesize($path_to_cache));
		header('Cache-Control: public');
		header('Expires: ' . gmdate('D, d M Y H:i:s', strtotime('+1 year')));
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($path_to_cache)));
		header('ETag: ' . $etag);
		if (is_callable('readfile') && !in_array('readfile', $disabled_functions)) {
			readfile($path_to_cache);
		} else {
			die(file_get_contents($path_to_cache));
		}
	}
?>