<?php
if(!function_exists('get_option')) {
	$path = ( defined('ABSPATH') ? ABSPATH : dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
	require_once($path.'wp-blog-header.php');
}
header("HTTP/1.1 200 OK");
header("Status: 200 All rosy") ;
# include some data arrays
include_once( 'ssp_data.php' );
	
$xml = '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
$xml.= '<params>'. "\n";
$xml.= '<customParams sspWidth="' .$_POST['sspWidth'] .'" sspHeight="'.$_POST['sspHeight'].'" navButtonStyle="'.$_POST['navButtonStyle'].'" />'."\n";
$xml.= '<nativeParams ';
foreach ($ssp_params as $key=> $value):
	if (in_array($key, array( 'id', 'sspWidth', 'sspHeight', 'sspGalleryId', 'sspName', 'sspGalleryStatus', 'createThumbnails', 'startAlbumID', 'startContentID' ) ) ) continue;
	# if fields are empty just reload the add gallery page. TODO: display an alert or something...
	if (isset($_POST[$key]) && !in_array($key, array('startAlbumID', 'startContentID')) && $_POST[$key] == '')
  {
    die("Invalid parameter: {$key}");
  }
  
	# replace '#' with '0x' in color items
	if (isset($_POST[$key]) && !empty($_POST[$key]) && $_POST[$key][0] == '#') $_POST[$key] = str_replace('#', '0x', $_POST[$key]);
	# check for unchecked checkboxes
	if (!isset($_POST[$key])) $_POST[$key] = strpos($key, 'Appearance') !== false ? 'Hidden' : 'Off'; 
	# merge array-like items
	if (is_array($_POST[$key])) $_POST[$key] = join(',', $_POST[$key]);
	$val = esc_attr($_POST[$key]);
	# change ssploop to loop
	if( $key == 'ssploop' ):
			$key = 'loop';
	endif;		
	# add staff to the query
	$xml.= $key . ' = "' . $val . '" ';
endforeach;
$xml.= ' />'."\n";
$xml.= '</params>';

$filename = strtolower( sanitize_title_with_dashes( $_POST['xmlExportName'] ) ) . '.xml';

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=\"".$filename."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".strlen($xml));
echo $xml;
exit();

?>