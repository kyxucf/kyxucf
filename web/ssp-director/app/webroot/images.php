<?php 
	////
	// This is a passthrough file. Requests are either 
	// redirected to an existing cache file, or to site/data
	// to render a new fresh XML document.
	////

	function clean($var) {
		if (is_numeric($var)) {
			return ceil($var);
		} else {
			exit;
		}
	}
	
	function clean_domain($host) {
		return preg_replace('/(http:\/\/)?(www\.)?|(:\d+)?(\/.*)?$/', '', $host);
	}
	
	$here = clean_domain($_SERVER['HTTP_HOST']);
	$refer = 'cantmatchme';
	
	if (isset($_SERVER['HTTP_REFERER'])) {
		$info = pathinfo($_SERVER['HTTP_REFERER']);
		if ($info['extension'] != 'swf') {
			$base_refer = array_shift(explode('?', basename($_SERVER['HTTP_REFERER'])));
			if ($base_refer != 'images.php') {
				$refer = clean_domain($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	if ($here == $refer) {
		$wedge = 'l';
	} else {
		$wedge = 'r';
	}
	
	$gid = 'no';
	$aid = 0;

	$ds = DIRECTORY_SEPARATOR;
	
	$path_to_cache = 'xml_cache/images';
	if (isset($_GET['gid']) || isset($_GET['gallery'])):
		$gid = isset($_GET['gid']) ? $_GET['gid'] : $_GET['gallery'];
		$gid = clean($gid);
		$path_to_cache .= '_gallery_' . $gid;
	elseif (isset($_GET['album'])):
		$aid = clean($_GET['album']);
		$path_to_cache .= '_album_' . $aid;
	endif;
	
	if (!isset($_GET['w'])) {
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'flash') === false) {
			$tail = "$gid/$aid/$specs";
			header("Location: index.php?/site/data/$tail");
			exit;
		} else {
			exit;
		}
	}
	
	$w = clean($_GET['w']);
	$h = clean($_GET['h']);
	$s = clean($_GET['s']);
	$q = clean($_GET['q']);
	$sh = clean($_GET['sh']);
	
	if ($s == 2) {
		// Get around fluid scaling/cache issues when originals are requested
		$w = $h = $q = $sh = 0;
	}
	
	$tw = clean($_GET['tw']);
	$th = clean($_GET['th']);
	$ts = clean($_GET['ts']);
	$tq = clean($_GET['tq']);
	$tsh = clean($_GET['tsh']);
	
	$pw = clean($_GET['pw']);
	$ph = clean($_GET['ph']);
	$ps = clean($_GET['aps']);

	$specs = "{$w}_{$h}_{$s}_{$q}_{$sh}_{$tw}_{$th}_{$ts}_{$tq}_{$tsh}_{$pw}_{$ph}_{$ps}";
	
	if (isset($_GET['tlw'])) {
		$tlw = clean($_GET['tlw']);
		$tlh = clean($_GET['tlh']);
		$specs .= "_{$tlw}_{$tlh}";
	} else {
		$specs .= "_{$tw}_{$th}";
	}
	
	$specs .= "_{$wedge}";

	$path_to_cache .= '_' . $specs . '.xml';
	$full_path = dirname(dirname(dirname(__FILE__))) . $ds . str_replace('/', $ds, $path_to_cache);

	if (file_exists($full_path)):   
		$tail = filemtime($path_to_cache);
		$day_m = date('nj', $tail);
		$day = date('nj');
		if ($day_m == $day) {
			header("Location: $path_to_cache?$tail");
			exit;
		}
	endif;

	$tail = "$gid/$aid/$specs";
	header('Pragma: no-cache');
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Location: index.php?/site/data/$tail");
?>