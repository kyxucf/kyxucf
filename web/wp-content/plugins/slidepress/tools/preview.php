<?php
if(!function_exists('get_option')) {
	$ssp_wp_path = ( defined('ABSPATH') ? ABSPATH : dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
	require_once($ssp_wp_path.'wp-blog-header.php');
}
header("HTTP/1.1 200 OK");
header("Status: 200 All rosy") ;
global $wpdb;
global $slidepress;
$sspWidth = $_GET['sspWidth'];
$sspHeight = $_GET['sspHeight'];
$sspGalleryId = $_GET['sspGalleryId'];
if ( !is_numeric($sspWidth) || !is_numeric($sspWidth) || empty($sspGalleryId) || preg_match('/[\'\"`#]/', $sspGalleryId) ) die('Invalid Width, Height or GalleryId');
$gallery = $wpdb->get_row( "SELECT startAlbumID, startContentID, xmlFileType, xmlFilePath FROM {$slidepress->table_name} WHERE sspGalleryId = '".$wpdb->escape( $sspGalleryId )."'", ARRAY_A );
extract( $gallery, EXTR_SKIP );
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $sspGalleryId; ?></title>
	<?php
	$host = parse_url($xmlFilePath);
		if( ($host['host'] != FALSE) && ($xmlFileType == 'Director')  )
		{
			if( get_transient( $sspGalleryId ) )
				delete_transient( $sspGalleryId );

			$path = parse_url($xmlFilePath, PHP_URL_PATH );
			$path = str_replace('images.php', '', $path );
			$embed_url = 'http://'.$host['host'].$path.'m/embed.js';
			$ssp_djs = $slidepress->is_valid_url($embed_url);
			if( $ssp_djs !== FALSE )
			{
				$use_director_embed = true;
				//get params
				$director_params = $slidepress->db->get_row( "SELECT * FROM ".$slidepress->table_name." WHERE sspGalleryId = '".$slidepress->db->escape( $sspGalleryId )."'", ARRAY_A );
				$director_params = $slidepress->params_to_object( $director_params );
				set_transient($sspGalleryId, TRUE);
			}
			else {
				$use_director_embed = false;
			}
		}
	if( FALSE == $use_director_embed ): ?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript">
		var SlidePress = {
			galleryId : '<?php echo str_replace('-', '_', $sspGalleryId); ?>',
			swfVersion : '<?php echo $slidepress->swf_version; ?>',
			sspVersion : '<?php echo $slidepress->version; ?>',
			sspWidth : '<?php echo $sspWidth; ?>',
			sspHeight : '<?php echo $sspHeight; ?>'
		};
	</script>
	<?php if (get_option('ssp_check_swf_version', 1)) { ?>
	<script type="text/javascript" src="../js/preview_check_version.js"></script>
	<?php } ?>
	<?php endif; ?>
		
</head>
<body>

<?php include( '../tpl/ssp_template.php' ); ?>

</body>
</html>