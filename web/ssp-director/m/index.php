<?php
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if (strpos($ua, 'Android') !== false) {
		$glib = 'png';
	} else {
		$glib = 'svg';
	}
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Loading</title>
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<link rel="stylesheet" media="only screen" href="c/base.css" type="text/css" />
</head>
<body>
	<div id="bg"></div>
	<div id="cover"></div>
	
	<script src="j/mobile.js"></script>
	<script src="j/<?php echo $glib; ?>.js"></script>
	<script>
		if (/iPad|iPhone|iPod|Kindle\sFire|Android [2-9]\.\d/.exec(navigator.userAgent) === null) {
			// iOS and Android 2.0+ support only
			window.location.replace('unsupported.html');
		} else {
			setTimeout(function() {
				M.load();
			}, 1);
		};
	</script>
</body>
</html>