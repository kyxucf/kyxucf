
<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

global $user_level;

# overview page
?>
<div class="wrap ssp_wrap">
	
	<br class="clear"/>
	
	<?php include_once( 'ssp_header.php' ); ?>
	
	<?php include_once( 'ssp_subnav.php' ); ?>
 	
	<h2>Overview</h2>
	    
	<p>Welcome to SlidePress! This plugin is designed for publishing slideshows and photo galleries in WordPress pages and posts using the <a href="http://slideshowpro.net/products/slideshowpro/" title="SlideShowPro" target="_blank">SlideShowPro Player SWF</a>. You can display content from your WordPress Media Library, Media RSS Feeds, or content published by our custom content management system <a href="http://slideshowpro.net/products/slideshowpro_director/" title="SlideShowPro Director" target="_blank">SlideShowPro Director</a>.
		</p>   
		
    <p>
	<a href="http://wiki.slideshowpro.net/SSPsa/SP-Installation" title="SlidePress Help Documentation" target="_blank">Documentation</a>
	|
	<a href="http://forums.slideshowpro.net/viewforum.php?id=34" title="support forum" target="_blank">Support Forum</a>
	</p>
	
	<p>&nbsp;</p>   	
	
	<?php if (sizeof($galleries)) : ?>
   
	
	<h3>Recently added SlidePress Galleries</h3>
	
	<table class="widefat">
	<thead>
	<tr>
		<th scope="col">Gallery ID</th>
		<th scope="col">Gallery Name</th>
		<th scope="col" width="30%">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php $show_edit = $this->check_permission_against(get_option( 'ssp_modifyGallery' )); ?>
	
	<?php foreach ($galleries as $gallery) : ?>
	<tr class='alternate author-self status-publish' valign="top">
		<td><?php echo $gallery->sspGalleryId; ?></td>
		<td><?php echo $gallery->sspName; ?></td>
		<td id="<?php echo $gallery->id;?>" sspWidth="<?php echo $gallery->sspWidth; ?>" sspHeight="<?php echo $gallery->sspHeight; ?>" sspGalleryId="<?php echo $gallery->sspGalleryId; ?>">
			<?php if ($show_edit) : ?><a href="javascript:;" class="ssp_manage" title="Edit gallery">Edit Gallery</a>&nbsp;|&nbsp; <?php endif; ?>
			<a href="javascript:;" class="ssp_preview" title="Preview">Preview</a>
		</td>	
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_managegallery" style="visibility:hidden" id="ssp_f_manage">
		<input type="hidden" name="action" value="manage" />
		<input type="hidden" name="id" id="ssp_f_manage_id" />
		<input type="submit" />
	</form>

	<script>
		jQuery(document).ready(function() {
			jQuery('.ssp_manage').bind('click', function(event) {
				var id = jQuery(this).parent('td:first').attr('id');
				jQuery('#ssp_f_manage_id').val(id);
				jQuery('#ssp_f_manage').submit(); 
			});
			
			jQuery('.ssp_preview').bind('click', function(event) {
				var id = jQuery(this).parent('td:first').attr('id');
				var sspWidth = parseInt(jQuery(this).parent('td:first').attr('sspWidth'));
				var sspHeight = parseInt(jQuery(this).parent('td:first').attr('sspHeight'));
				var sspGalleryId = jQuery(this).parent('td:first').attr('sspGalleryId');
				
				window.open('<?php echo $this->url; ?>/tools/preview.php?sspWidth='+sspWidth+'&sspHeight='+sspHeight+'&sspGalleryId='+sspGalleryId, sspGalleryId, 'left=20,top=20,width='+(sspWidth+20)+',height='+(sspHeight+20)+',toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0');
				
			});
		});
	</script>
	<?php endif; ?>

	<br class="clear"/>             
	
	<?php if ( $this->check_permission_against(get_option( 'ssp_addNewGallery' )) ) : ?>
	<h3>Add a new SlidePress Gallery</h3>
	<!-- Add a new SlidePress Gallery -->
	<form method="post" id="ssp_form" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_addgallery" class="ssp_form">
		<input type="hidden" name="action" value="take_this_gallery_name" />
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="sspName">Gallery Name:</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<input name="sspName" type="text" id="sspName" size="35" maxlength="100" />&nbsp;&nbsp;<input type="submit" value="Add gallery" class="button-primary" />
			</td>
		</tr>
		</table>
	</form>
	
	<?php include( 'ssp_validate_js.php' ); ?>
	<?php endif; ?>

	<br class="clear"/> 
</div>