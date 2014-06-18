<?php

	$full = DIR_HOST . '/popup.php?src=[width:800,height:800,crop:0,quality:90,sharpening:1]&title=[img_src]';
	
	$displayName = __('Open resized image at 800x800 in chromeless popup window', true);
	$template = "javascript:if (window.NewWindow) { NewWindow.close(); }; NewWindow=window.open('$full','myWindow','width=800,height=800,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,titlebar=no');NewWindow.focus(); void(0);";
	$target = 0;

?>