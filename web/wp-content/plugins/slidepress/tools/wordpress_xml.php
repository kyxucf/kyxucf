<?php
	if (!isset($_GET['id']) || !isset($_GET['gid'])) exit;
	if(!function_exists('get_option')) {
	$path = ( defined('ABSPATH') ? ABSPATH : dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
	require_once($path.'wp-blog-header.php');
	}
	global $slidepress;
	header("HTTP/1.1 200 OK");
	header("Status: 200 All rosy") ;
	global $wpdb;
	$id = (int) $_GET['id'];
	$gid = $_GET['gid'];
  	header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
	$images = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
	$videos = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'video/quicktime', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
	$upload_dir = $slidepress->get_wp_upload_path();
	$thumbs_dir = $upload_dir['url'] . '/' . $gid. '_thumbs/';
	$gallery = $wpdb->get_row( "SELECT sspName, sspDescription, createThumbnails FROM " . $slidepress->table_name . " WHERE sspGalleryId = '" . $gid ."'", ARRAY_A );
?>
<gallery>
	<album title="<?php esc_attr_e( $gallery['sspName'] ); ?>" description="<?php esc_attr_e( $gallery['sspDescription'] ); ?>">
		<?php if (!empty($images)): foreach ($images as $iid => $image): ?>
			<?php $wp_image = wp_get_attachment_image_src($iid, false);
				$file = $thumbs_dir . basename($wp_image[0]);
				$info = pathinfo($file);
				$dir = $info['dirname'];
				$ext = $info['extension']; // jpg and JPG consistency. issue #21809
				$name = basename($file, ".{$ext}") . '-thumb';
				if ( $ext == 'JPG' )
					$ext = 'jpg';
				$filename = "{$dir}/{$name}.{$ext}";
			?>
			<img src="<?php echo htmlentities($wp_image[0]); ?>" target="_self"
				<?php if ( ! empty( $image->post_excerpt ) ): ?>
					caption="<?php esc_attr_e( $image->post_excerpt ); ?>"
				<?php endif ?>
				<?php if ( ! empty( $image->post_content ) ): ?>
					link="<?php esc_attr_e( $image->post_content ); ?>"
				<?php endif ?>
				<?php if ( ! empty( $image->post_title ) ): ?>
					title="<?php esc_attr_e( $image->post_title ); ?>"
				<?php endif ?>
				<?php if ( $gallery['createThumbnails'] == '1' ): ?>
					tn="<?php esc_attr_e( $filename ); ?>"
				<?php endif ?>
				 />
		<?php endforeach; endif;?>
		
		<?php if (!empty($videos)): foreach ($videos as $iid => $video): ?>
			<?php 
				$wp_video = wp_get_attachment_url( $iid );
			?>
			<img src="<?php echo htmlentities($wp_video); ?>" caption="<?php esc_attr_e($video->post_excerpt); ?>" link="<?php esc_attr_e($video->post_content); ?>" title="<?php esc_attr_e($video->post_title); ?>" />
		<?php endforeach; endif;?>
	</album>
</gallery>