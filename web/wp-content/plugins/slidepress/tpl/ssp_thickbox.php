<?php
# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;
global $slidepress;
# ssp template
?>

<!-- SlidePress Gallery <?php echo $slidepress->version; ?> [<?php echo $sspGalleryId; ?>] -->

<a href="#TB_inline?width=<?php echo $sspWidth - 30; ?>&amp;height=<?php echo $sspHeight; ?>&amp;inlineId=ssp_t_<?php echo $sspGalleryId; ?>" class="thickbox" <?php echo 'title="'.$sspName.'"';?>><?php echo (!is_null($sspGalleryImage) ? '<img src="'.$sspGalleryImage.'" alt="" />' : $sspGalleryTitle); ?></a>

<?php if ( $use_director_embed ): ?>

<script type="text/javascript" src="<?php echo $embed_url; ?>"></script>

<?php endif; ?>

<div class="slidepress-gallery">
	<div id="ssp_t_<?php echo $sspGalleryId; ?>" style="display:none">
		<style>
		#TB_window {
			}
		#TB_ajaxContent{
			overflow:hidden;
			padding:0;
			width:<?php echo $sspWidth; ?>px !important;
			height:<?php echo $sspHeight; ?>px !important;
			}
		#TB_ajaxContent object{
			padding:0 !important;
			margin:0 !important;
			}
		</style>
		<div id="ssp_t_content_<?php echo str_replace('-','_',$sspGalleryId); ?>"><?php echo html_entity_decode($alternative_content); ?></div>
	</div>	
</div>
<script type="text/javascript">
<?php if ( $use_director_embed ): ?>

var flashvars = <?php echo $director_params; ?>;

var attributes = {
	id: "ssp_t_content_<?php echo str_replace('-','_',$sspGalleryId); ?>",
	width: "<?php echo $sspWidth; ?>",
	height: "<?php echo $sspHeight; ?>"
};

<?php else: ?>

var flashvars = { 
	paramXMLPath: "<?php echo $slidepress->url; ?>tools/param.php?gid=<?php echo $sspGalleryId; ?>",
	initialURL: escape(document.location),
	useExternalInterface: true
};

var attributes = {};

<?php endif; ?>

<?php if ( !in_array( $xmlFileType, array( 'WordPress Gallery', 'Media RSS' ) ) ): ?>
	<?php if ( !empty( $startAlbumID ) ): ?>
    	flashvars.startAlbumID = "<?php echo $startAlbumID; ?>";
  	<?php endif; ?>
  	<?php if ( !empty( $startContentID ) ): ?>
    	flashvars.startContentID = "<?php echo $startContentID; ?>";
 	<?php endif; ?>
<?php endif; ?>

var params = {
	quality: "best",
	bgcolor: "#121212",
	wmode: "transparent",
	allowfullscreen: "true",
	allowScriptAccess: "always"
};

<?php if ( $use_director_embed ): ?>

SlideShowPro({attributes: attributes, params: params, flashvars: flashvars});

<?php else: ?>

params.base = "."; 
swfobject.embedSWF("<?php echo $slidepress->upload_url; ?>flash/slideshowpro.swf", "ssp_t_content_<?php echo str_replace('-','_',$sspGalleryId); ?>", "<?php echo $sspWidth; ?>", "<?php echo $sspHeight; ?>", "9.0.0", false, flashvars, params, attributes);

<?php endif; ?>

</script>

<!-- SlidePress Gallery ends -->