<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

# ssp template
?>

<!-- SlidePress Gallery <?php echo $this->version; ?> [<?php echo $sspGalleryId; ?>] -->

<a href="#ssp_lv_<?php echo $sspGalleryId; ?>" class="lightview" title=' :: :: topclose: true, autosize: true' ><?php echo $sspGalleryId; ?></a>

<div id="ssp_lv_<?php echo $sspGalleryId; ?>" style="display:none">
	<?php echo html_entity_decode($alternative_content); ?>
</div>	
<script type="text/javascript">
	var so = new SWFObject("<?php echo $this->upload_url; ?>flash/loader.swf", "loader", "<?php echo $sspWidth; ?>", "<?php echo $sspHeight; ?>", "9", "#121212");
	so.addParam("allowFullScreen","true");
	so.addParam("quality", "best");
	so.addParam("base",".");
	so.addParam("allowScriptAccess","always");
	so.addVariable("paramXMLPath","<?php echo $this->url; ?>tools/param.php?gid=<?php echo $sspGalleryId; ?>");
  <?php if (!empty($startAlbumID)): ?>
    so.addVariable("startAlbumID", $startAlbumID);
  <?php endif; ?>
  <?php if (!empty($startContentID)): ?>
    so.addVariable("startContentID", $startContentID);
  <?php endif; ?>
	so.write("ssp_lv_<?php echo $sspGalleryId; ?>");	     
</script>

<!-- SlidePress Gallery ends -->