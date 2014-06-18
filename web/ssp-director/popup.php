<?php error_reporting(0); if (isset($_GET['dl'])) {
	$file = str_replace('/', DIRECTORY_SEPARATOR, $_GET['src']);
	if (strpos($file, '..') !== false) {
		exit;
	}
	$name = basename($file);
	$info = pathinfo($name);
	$ext = $info['extension'];
	$full_path = dirname(__FILE__) . $file;
	header("Content-Disposition: attachment; filename=$name");
	switch(strtolower($ext)) {
		case 'jpg':
			$ct = 'image/jpeg';
			break;
		case 'gif':
			$ct = 'image/gif';
			break;
		case 'png':
			$ct = 'image/png';
			break;
		default:
			$ct = 'application/octet-stream';
			break;
	}
	
	header('Content-type: ' . $ct);
	header('Content-length: ' . filesize($full_path));
	
	$disabled_functions = explode(',', ini_get('disable_functions'));
	
	if (is_callable('readfile') && !in_array('readfile', $disabled_functions)) {
		readfile($full_path);
	} else {
		die(file_get_contents($full_path));
	}
} else { ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo(strip_tags($_GET['title'])); ?></title> 
		<style type="text/css" media="screen">
		/* <![CDATA[ */
			* { margin:0; padding:0; overflow:auto;}
		/* ]]> */
		</style>
		
		<script type="text/javascript" charset="utf-8">
			function isIE() {
		  		return /msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent);
			}
			
			function resize(img_w, img_h) {
				if (isIE()) {
					var h = document.documentElement.clientHeight;
				} else {
					var h = window.innerHeight;
				}
				var w = document.body.clientWidth;
				var adj_w = img_w - w; 
				var adj_h = img_h - h;
		       	window.resizeBy(adj_w, adj_h);
				window.focus();
			}
		</script>
	</head>
	
	<body>
		<?php
		
			if (strpos($_GET['src'], 'p.php?a=') !== false) {
				$src = $_GET['src'];
				$bits = explode('?a=', $src);
				$src = $bits[0] . '?a=' . urlencode($bits[1]);
			} else {
				$src = strip_tags($_GET['src']);
			}
		
		?>
    	<img onload="resize(this.width, this.height);" src="<?php echo($src); ?>" alt="<?php echo(strip_tags($_GET['title'])); ?>" />
	</body>
</html><?php } ?>