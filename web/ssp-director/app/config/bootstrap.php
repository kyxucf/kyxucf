<?php
/* SVN FILE: $Id: bootstrap.php 2951 2006-05-25 22:12:33Z phpnut $ */
/**
 * Short description for file.
 *
 * Long description for file
 *
 * PHP versions 4 and 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright (c) 2006, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP Project
 * @package			cake
 * @subpackage		cake.app.config
 * @since			CakePHP v 0.10.8.2117
 * @version			$Revision: 2951 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2006-05-25 17:12:33 -0500 (Thu, 25 May 2006) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 *
 * This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
 * This is an application wide file to load any function that is not used within a class define.
 * You can also use this to include or require any files in your application.
 *
 */
/**
 * The settings below can be used to set additional paths to models, views and controllers.
 * This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
 *
 * $modelPaths = array('full path to models', 'second full path to models', 'etc...');
 * $viewPaths = array('this path to views', 'second full path to views', 'etc...');
 * $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
 *
 */
//EOF

// Set some Director vars
if (!defined('ALBUM_DIR')) {
	define('ALBUM_DIR', 'albums');
}
define('ALBUMS', ROOT . DS . ALBUM_DIR);
define('AVATARS', ALBUMS . DS . 'avatars');
define('WATERMARKS', ALBUMS . DS . 'watermarks');
define('AUDIO', ROOT . DS . 'album-audio');
define('THUMBS', ROOT . DS . 'album-thumbs');
define('IMPORTS', ALBUMS . DS . 'imports');

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
	$protocol = 'https://';
} else {
	$protocol = 'http://';
}

define('DIR_REL_HOST', str_replace('/index.php?', '', Configure::read('App.baseUrl')));
define('DIR_HOST', $protocol . preg_replace('/:80$/', '', env('HTTP_HOST')) . DIR_REL_HOST);
define('DATA_LINK', DIR_HOST . '/images.php');
define('XML_CACHE', ROOT . DS . 'xml_cache');
define('THEMES', WWW_ROOT . 'styles');
define('USER_THEMES', ROOT . DS . 'themes');
define('DIR_VERSION_FULL', '1.5.4.10265');
define('DB_VERSION', '1521');
define('DIR_CACHE', 'director');
define('PLUGS', APP . 'director_plugins');
define('CUSTOM_PLUGS', ROOT . DS . 'plugins');

$parts = explode('.', DIR_VERSION_FULL);
define('DIR_VERSION', $parts[0] . '.' . $parts[1] . '.' . $parts[2] . ' (Build ' . $parts[3] . ')');

if (!defined('MAGICK_PATH')) {
	define('MAGICK_PATH_FINAL', 'convert');
} else if (strpos(strtolower(MAGICK_PATH), 'c:\\') !== false) {
	define('MAGICK_PATH_FINAL', '"' . MAGICK_PATH . '"');	
} else {
	define('MAGICK_PATH_FINAL', MAGICK_PATH);	
}

if (!defined('FFMPEG_PATH')) {
	define('FFMPEG_PATH_FINAL', 'ffmpeg');	
} else {
	define('FFMPEG_PATH_FINAL', FFMPEG_PATH);	
}

if (!defined('XDOM_CHECK')) {
	define('XDOM_CHECK', true);
}

if (!defined('FORCE_GD')) {
	define('FORCE_GD', false);
}

if (!defined('AJAX_CHECK')) {
	define('AJAX_CHECK', true);
}

if (!defined('AUTO_UPDATE')) {
	define('AUTO_UPDATE', true);
}

if (!defined('RELATIVE_XML_PATHS')) {
	define('RELATIVE_XML_PATHS', true);
}

// Bring in database configuration
if (@include_once(ROOT . DS . 'config' . DS . 'conf.php')) {
	define('DIR_DB_HOST', $host);
	define('DIR_DB_USER', $user);
	define('DIR_DB_PASSWORD', $pass);
	define('DIR_DB', $db);
	define('DIR_DB_PRE', $pre);
	if (isset($interface)) {
		define('DIR_DB_INT', $interface);
		if ($interface == 'mysqli') {
			define('DIR_DB_CONN', 'mysqli_connect');
		} else {
			define('DIR_DB_CONN', 'mysql_connect');
		}
	} else {
		define('DIR_DB_INT', 'mysql');
		define('DIR_DB_CONN', 'mysql_connect');
	}
	if (isset($encoding)) {
		define('DIR_DB_ENCODING', $encoding);
	} else {
		define('DIR_DB_ENCODING', '');
	}
	if (isset($port) && !empty($port)) {
		define('DIR_PORT', $port);
	} else if (isset($socket) && !empty($socket)) {
		define('DIR_PORT', $socket);
	} else {
		define('DIR_PORT', '');
	}
} else {
	// No config file, we need to redirect them to the install page
	if (preg_match('/install/', env('QUERY_STRING')) || preg_match('/translate/', env('QUERY_STRING'))) {
		define('INSTALLING', true);
	} else {
		$url = DIR_HOST . '/index.php?/install';
		header("Location: $url");
		exit;
	}
}

if (!defined('INSTALLING')) {
	define('INSTALLING', false);
}

include_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'bradleyboy' . DS . 'ensure.php');
include_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'director' . DS . 'salt.php');

function p() {
	$args = func_get_args();
	$src = $args[0];
	$aid = $args[1];
	if (strpos($aid, 'avatar-') !== false) {
		$bits = explode('-', $aid);
		$aid = $bits[1];
		$m = filemtime(ALBUMS . DS . 'avatars' . DS . $aid . DS . $src);
	} else {
		$m = filemtime(ALBUMS . DS . 'album-' . $aid . DS . 'lg' . DS . $src);
	}
	$args = join(',', $args);
	$crypt = convert($args);
	return DIR_HOST . '/p.php?a=' . $crypt . '&amp;m=' . $m;
}

function __p($options) {
	$defaults = array(
			'src' => '',
			'album_id' => null,
			'width' => 176,
			'height' => 132,
			'square' => 1,
			'quality' => 70,
			'sharpening' => 1,
			'anchor_x' => 50,
			'anchor_y' => 50,
			'watermark_id' => 0,
			'watermark_location' => 5,
			'watermark_opacity' => 50,
			'force' => false
		);
	$o = array_merge($defaults, $options);
	if (strpos($o['album_id'], 'avatar-') !== false) {
		$bits = explode('-', $o['album_id']);
		$aid = $bits[1];
		$m = filemtime(ALBUMS . DS . 'avatars' . DS . $aid . DS . $o['src']);
	} else {
		$m = filemtime(ALBUMS . DS . 'album-' . $o['album_id'] . DS . 'lg' . DS . $o['src']);
	}
	$args = join(',', array($o['src'], $o['album_id'], $o['width'], $o['height'], $o['square'], $o['quality'], $o['sharpening'], $o['anchor_x'], $o['anchor_y'], (int) $o['force'], $o['watermark_id'], $o['watermark_location'], $o['watermark_opacity'])); 
	$crypt = convert($args);
	return DIR_HOST . '/p.php?a=' . $crypt . '&amp;m=' . $m;
}

function computeSize($file, $new_w, $new_h, $scale) {
	$dims = getimagesize($file);
	$old_x = $dims[0];
	$old_y = $dims[1];
	if ($scale == 1) {
		if ($old_x < $new_w || $old_y < $new_h) {
			$new_w = $old_x;
			$new_h = $old_y;
		}
		$x = $new_w;
		$y = $new_h;
	} else {
		$original_aspect = $old_x/$old_y;
		$new_aspect = $new_w/$new_h;
		if ($scale == 2) {
			$x = $old_x;
			$y = $old_y;
		} else if ($scale == 1) {
			$x = $new_w;
			$y = $new_h;
		} else {
			if ($original_aspect >= $new_aspect) {
				if ($new_w > $old_x) {
					$x = $old_x;
					$y = $old_y;
				}
				$x = $new_w;
				$y = ($new_w*$old_y)/$old_x;
			} else { 
				if ($new_h > $old_y) {
					$x = $old_x;
					$y = $old_y;
				}
				$x = ($new_h*$old_x)/$old_y;
				$y = $new_h;
			}
		}
	}
	return array(round($x), round($y));
}

function computeFocal($old_w, $old_h, $new_w, $new_h, $x, $y, $square = 1) {
		$original_aspect = $old_w/$old_h;
		$new_aspect = $new_w/$new_h;
		
		if ($new_w == $old_w && $new_h == $old_h) {
			return array(($x/100)*$new_w, ($y/100)*$new_h);
		}

		if ($square) {
			$base = 'w';
			$edge_x = $edge_y = false;
			if ($original_aspect >= $new_aspect) {
				$thumb_w = ($new_h*$old_w)/$old_h;
				$thumb_h = $new_h;				
				$pos_x = $thumb_w * ($x/100);
				$pos_y = $thumb_h * ($y/100);
				$base = 'h';
			} else {
				$thumb_w = $new_w;
				$thumb_h = ($new_w*$old_h)/$old_w;
				$pos_x = $thumb_w * ($x/100);
				$pos_y = $thumb_h * ($y/100);
			}
			
			$crop_y = $pos_y - ($new_h/2);
			$crop_x = $pos_x - ($new_w/2);
			if ($crop_y < 0) { 
				$crop_y = 0;
			} else if (($crop_y+$new_h) > $thumb_h) {
				$crop_y = $thumb_h - $new_h;
				$edge_y = true;
			}
			if ($crop_x < 0) { 
				$crop_x = 0;
			} else if (($crop_x+$new_w) > $thumb_w) {
				$crop_x = $thumb_w - $new_w;
				$edge_x = true;
			}

			if ($base == 'h') {
				$focal_y = $pos_y;
				if ($edge_x) {
					$focal_x = $pos_x - ($thumb_w - $new_w);
				} else if ($crop_x == 0) {
					$focal_x = $pos_x;
				} else {
					$focal_x = $new_w*.5;
				}
			} else {
				$focal_x = $pos_x;
				if ($edge_y) {
					$focal_y = $pos_y - ($thumb_h - $new_h);
				} else if ($crop_y == 0) {
					$focal_y = $pos_y;
				} else {
					$focal_y = $new_h*.5;
				}
			}
			return array($focal_x, $focal_y);
		} else {
			if ($original_aspect >= $new_aspect) {
				if ($new_w > $old_w) {
					$new_w = $old_w;
					$new_h = $old_h;
				} else {
					$new_h = ($new_w*$old_h)/$old_w;
				}
			} else { 
				if ($new_h > $old_h) {
					$x = $old_x;
					$y = $old_y;
				} else {
					$new_w = ($new_h*$old_w)/$old_h;
				}
			}
		 	return array(($x/100)*$new_w, ($y/100)*$new_h);
		}
}

function allowableFile($fn) {
	if (eregi('\.flv$|.\f4v$|\.mov$|\.mp4$|\.m4a$|\.m4v$|\.3gp$|\.3g2$|\.swf$|\.jpg$|\.jpeg$|\.gif$|\.png$', $fn)) {
		return true;
	}
	return false;
}

function isVideo($fn) {
	if (eregi('\.flv$|\.f4v$|\.mov$|\.mp4$|\.m4a$|\.m4v$|\.3gp$|\.3g2$', $fn)) {
		return true;
	} else {
		return false;
	}
}

function isImage($fn) {
	return !isNotImg($fn);
}

function isSwf($fn) {
	if (eregi('\.swf$', $fn)) {
		return true;
	} else {
		return false;
	}
}

function isNotImg($fn) {
	if (isSwf($fn) || isVideo($fn)) {
		return true;
	} else {
		return false;
	}
}

function parse_anchor($anchor) {
	$anchor = unserialize($anchor);
	if (empty($anchor)) {
		$x = $y = 50;
	} else {
		$x = $anchor['x'];
		$y = $anchor['y'];
	}
	return array($x, $y);
}

function convert_smart_quotes($string) 
{ 
   	$search = array(chr(0xe2) . chr(0x80) . chr(0x98),
                 	chr(0xe2) . chr(0x80) . chr(0x99),                  
					chr(0xe2) . chr(0x80) . chr(0x9c),                  
					chr(0xe2) . chr(0x80) . chr(0x9d),                  
					chr(0xe2) . chr(0x80) . chr(0x93),                  
					chr(0xe2) . chr(0x80) . chr(0x94));   
					
	$replace = array(	'&lsquo;',
	                 	'&rsquo;',                   
						'&ldquo;',                   
						'&rdquo;',                   
						'&ndash;',                   
						'&mdash;');                     
						
	return str_replace($search, $replace, $string);
}

?>
