<?php
function ssp_parse_url_vars() {
$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
	$query = explode("?", $_SERVER['QUERY_STRING']); 
	// modify/delete data 
	foreach($query as $q) 
	{ 
	    list($key, $value) = explode("=", $q);
	   	$_GET[$key] = $value;
	}
}

ssp_parse_url_vars();

if (!isset($_GET['gid'])) exit;

if(!function_exists('get_option')) {
	$path = ( defined('ABSPATH') ? ABSPATH : dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
	require_once($path.'wp-blog-header.php');
}

header("HTTP/1.1 200 OK");
header("Status: 200 All rosy") ;

global $wpdb, $slidepress;

$this_gallery = $slidepress->db->get_row( "SELECT sspName, sspGalleryId, sspHeight, sspWidth, xmlFilePath, xmlFileType, sspDescription, createThumbnails, active_style, startAlbumID, startContentID FROM " . $slidepress->table_name . " WHERE sspGalleryId = '".$wpdb->escape($_GET['gid'])."'", ARRAY_A );
$active_style = $this_gallery['active_style'];
$this_gallery_styles = $slidepress->db->get_row( "SELECT * FROM " . $slidepress->table_name . " WHERE sspGalleryId = '{$active_style}'", ARRAY_A );

$params = array_merge( $this_gallery_styles, $this_gallery );

//$params = $wpdb->get_row( "SELECT * FROM ".$slidepress->table_name." WHERE sspGalleryId = '".$wpdb->escape($_GET['gid'])."'", ARRAY_A );
if (!$params) exit;

switch ($params['xmlFileType']) {
	case 'Manual Entry' :
			$params['xmlFileType'] = 'Default';
		break;
	case 'WordPress Gallery' :
			$params['xmlFileType'] = 'Default';
			$params['xmlFilePath'] = $slidepress->url . 'tools/wordpress_xml.php?id=' . $params['xmlFilePath'] . '&gid=' . $_GET['gid'];
		break;
	case 'Director' :
			//$params['xmlFileType'] = get_transient( $active_style ) ? null : $params['xmlFileType'];
			//$params['xmlFilePath'] = get_transient( $active_style ) ? null : $params['xmlFilePath'];
		break;
			
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
?>
<params>
<customParams sspWidth="<?php echo $params['sspWidth']; ?>" sspHeight="<?php echo $params['sspHeight']; ?>" navButtonStyle = "<?php echo $params['navButtonStyle']; ?>" />
<nativeParams<?php
	foreach ($ssp_params as $key => $value ) {
		$value = $params[$key];
		if (in_array($key, array( 'startAlbumID', 'startContentID', 'id', 'sspWidth', 'sspHeight', 'sspGalleryId', 'sspName', 'sspGalleryStatus', 'xmlManualSource', 'createThumbnails', 'navButtonStyle' ) ) || !in_array($param, $ssp_params) || ( empty($value) && $value != 0 ) ) continue;

		if ('xmlFileType' == $key && 'Manual Entry' == $value) {
			$value = 'Default';
		}
		
		if('captionHeaderText' == $key){
			$value = ($value == 'Off') ? '{imageTitle}' : $value;
		}
		
		if( $key == 'ssploop' ):
			$key = 'loop';
		endif;
		
		echo "\n ". $key . '="'. htmlentities($value, ENT_QUOTES) .'" ';
	}
?> />
</params>