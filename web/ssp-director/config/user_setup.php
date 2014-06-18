<?php

/* 	
	You can use this file to set specific variables outside the SlideShowPro Director core,
	so updates won't affect them. Warning: This is for advanced users only, those with a 
	proper understanding of PHP and its' syntax.
	
	NOTE: To activate any line in this file, the two forward slashes must be removed.
*/

/*
	SESSION SAVE PATH
	A common use of this file is to set a custom session save path, as required by your host.
	Uncomment out the line below and replace the path with your host's session
	save path.
*/

	// session_save_path('/path/from/your/host');
	
/*
	MAGICK PATH
	If the ImageMagick image processing library is installed on your server but is not in the
	server's path, you can enter the direct path to the convert function here. If you aren't sure
	what path to use, contact your hosting provider.
*/

	// define('MAGICK_PATH', '/path/to/convert');
	
/*
	FORCE GD
	If you are having issues with ImageMagick or with a blank screen when trying to access
	Director, set the following to true.
*/

	// define('FORCE_GD', false);
	
/*
	SALT
	This random string helps secure your Director installation. Enter a random, alphanumeric string
	or passphrase below. It can be anything you want, just make sure it is not something that 
	someone would guess. IMPORTANT: For best performance, this string should be longer than 8 characters.
*/

	// define('SALT', 'mysaltedstring');
	
/*
	PERFORM AJAX CHECK
	By default, Director performs a check to ensure AJAX requests are valid. Some servers do not properly 
	set header, breaking many of Director's functions. You can disable the check by uncommenting the 
	following line.
*/

	// define('AJAX_CHECK', false);
	
/*
	PERFORM CROSSDOMAIN CHECK
	By default, Director checks to make sure you have a crossdomain.xml file in place. Some server 
	configurations cause Director to think a crossdomain.xml file does not exist when it actually does. If
	this is happening to you, uncomment the following line.
*/

	// define('XDOM_CHECK', false);
	
/*
	ACTIVATION PROXY
	If your server requires the use of a proxy for outgoing connections, enter it below.
	If the proxy requires a password, fill in those values in the second line.
*/	

	// define('ACTIVATION_PROXY', 'http://proxy.server.com:port');
	// define('ACTIVATION_LOGIN', 'user:password');
	
/*
	CHARACTER SET
	If the Director interface does not properly show international characters, uncomment the line below.
	
	// ini_set('default_charset', 'UTF-8');
*/
	
/*
	RENAME ALBUMS FOLDER
	If you have renamed the ssp_director/albums folder, uncomment the line below and enter the new name
	you have given the folder. IMPORTANT: After changing this setting, you need to delete all files/folders
	found in the following directories: app/tmp, xml_cache
	
	// define('ALBUM_DIR', 'my_secret_location');
*/
