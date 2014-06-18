<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) die("Can't call this file directly");

# manage galleries page
?>
<div class="wrap ssp_wrap ssp-page-manage">
	<br class="clear"/> 
	
	<?php include_once( 'ssp_header.php' ); ?> 
	
	<?php include_once( 'ssp_subnav.php' ); ?>
	
	<h2>Manage Galleries</h2>
	
	<?php if (!is_null($ssp_msg)) echo $ssp_msg; ?>
		
	<div style="float:right;"><form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_addgallery"><input type="submit" class="button" value="Create new gallery" /></form></div>

	<p>Edit, preview or delete a saved gallery below.</p>
	
	<table class="widefat" cellspacing="0" cellpadding="0">
	<thead>
	<tr>
		<th scope="col">Gallery ID</th>
		<th scope="col">Gallery Name</th>
		<th scope="col" width="30%">Action</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($galleries as $gallery) : ?>
	<tr class='alternate author-self status-publish' valign="top">
		<td><?php echo $gallery->sspGalleryId; ?></td>
		<td><?php echo $gallery->sspName; ?></td>
		<td id="<?php echo $gallery->id;?>" sspWidth="<?php echo $gallery->sspWidth; ?>" sspHeight="<?php echo $gallery->sspHeight; ?>" sspGalleryId="<?php echo $gallery->sspGalleryId; ?>">
			<a href="javascript:;" class="ssp_manage" title="Edit gallery">Edit Gallery</a>&nbsp;|&nbsp;
			<a href="javascript:;" class="ssp_preview" title="Preview">Preview</a>&nbsp;|&nbsp;
			<a href="#ssp_modal" rel="facebox" class="ssp_delete" title="Delete">Delete</a>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	
	<!--- Paging added CCR --->
	<div class="tablenav">
	<?php
	$page_links = paginate_links( array(
		'base' => add_query_arg( array( 'paged'=> '%#%' ) ),
		'format' => '',
		'total' => ceil($count / 10),
		'current' => $_GET['paged']
	));
	
	if ( $page_links )
		echo "<div class='tablenav-pages'>$page_links</div>";
	?>
	</div>

	<form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_managegallery" style="visibility:hidden;height:0;" id="ssp_f_delete">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="id" id="ssp_f_delete_id" />
		<input type="submit" />
	</form>
	
	<form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=ssp_show_admin_managegallery" style="visibility:hidden;height:0;" id="ssp_f_manage">
		<input type="hidden" name="action" value="manage" />
		<input type="hidden" name="id" id="ssp_f_manage_id" />
		<input type="submit" />
	</form>

	<script>
		function ssp_delete_gallery(id) {
			jQuery('#ssp_f_delete_id').val(id);
			jQuery('#ssp_f_delete').submit();
		}
	
		jQuery(document).ready(function() {
			jQuery('.ssp_manage').bind('click', function(event) {
				var id = jQuery(this).parent('td:first').attr('id');
				jQuery('#ssp_f_manage_id').val(id);
				jQuery('#ssp_f_manage').submit();
			});
			
			jQuery('.ssp_delete').click(function(e) {
				e.preventDefault();
				var id = jQuery(this).parent('td:first').attr('id');
				ssp_areYouSure(id);
			});
			
			function ssp_areYouSure(id) {
				jQuery.facebox('<div id="ssp_modal"><div id="ssp_modal_content">Are you sure you want to delete this gallery?<\/div><div id="ssp_modal_buttons"><input class="button" type="button" value="&nbsp;&nbsp;Delete&nbsp;&nbsp;" onclick="ssp_delete_gallery('+id+')" /><input class="button" type="button" value="&nbsp;&nbsp;Cancel&nbsp;&nbsp;" onclick="jQuery(document).trigger(\'close.facebox\')" /><\/div><\/div>');
        jQuery('#facebox .footer').remove();
			}
				
			jQuery('.ssp_preview').bind('click', function(event) {
				var id = jQuery(this).parent('td:first').attr('id');
				var sspWidth = parseInt(jQuery(this).parent('td:first').attr('sspWidth'));
				var sspHeight = parseInt(jQuery(this).parent('td:first').attr('sspHeight'));
				var sspGalleryId = jQuery(this).parent('td:first').attr('sspGalleryId');
				
				window.open('<?php echo $this->url; ?>tools/preview.php?sspWidth='+sspWidth+'&sspHeight='+sspHeight+'&sspGalleryId='+sspGalleryId, sspGalleryId, 'left=20,top=20,width='+(sspWidth+20)+',height='+(sspHeight+20)+',toolbar=0,resizable=0,location=0,directories=0,status=0,menubar=0,scrollbars=0');
				
			});
			
		});
	</script>
	

	<?php if (strcmp($action, 'Hide')) : ?> 
		
	<form method="post" id="ssp_form" >
				
		<!-- Manage SlidePress Galleryies -->
		<table class="form-table ssp_form-table ssp_noborder">
		<tr valign="top" class="manage-gallery-first-box">
			<th colspan="3" scope="row" class="ssp_form-h2"><h3>Edit Gallery: "<?php echo $sspName;?>"</h3></th>
			
		</tr>
		<tr valign="top">
			<th scope="row"><label for="sspName">Name</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td><input name="sspName" type="text" id="sspName" value="<?php echo $sspName;?>" size="50" maxlength="100" /></td>
		</tr>
		<tr valign="top" id="sspDescriptionField">
			<th scope="row"><label for="sspDescription">Description</label><br /><span class="description">(Optional)</span></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden;"></span></td>
			<td><textarea cols="" rows="" name="sspDescription" id="sspDescription"><?php echo $sspDescription;?></textarea></td>
		</tr>
		<tr valign="top">
			<th scope="row" class="help"><label for="xmlFilePath">XML Source</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<input name="xmlFilePath" type="text" id="xmlFilePath" value="<?php echo $xmlFilePath;?>" size="50" maxlength="255" />
				<textarea cols="" rows="" id="xmlManualSource" name="xmlManualSource"><?php	echo ('Manual Entry' == $xmlFileType)?htmlentities(file_get_contents( $this->upload_path . 'xml/' . $sspGalleryId . '.xml' )):'';	?></textarea>
				&nbsp;&nbsp;
				<select id="xmlFileType" name="xmlFileType"><option value="">Select XML Type</option><?php foreach ($xmlFileType_options as $option) : ?><option value="<?php echo $option;?>"<?php if (!strcmp($xmlFileType, $option)) echo ' selected="selected"';?>><?php echo $option;?></option><?php endforeach;?></select> 
				&nbsp;&nbsp;
				<input type="button" id="xmlTypeAdvanced" value="Advanced" class="button" />
				<div id="createThumbnailsField">
					<input type="checkbox"<?php echo ((isset($createThumbnails) && $createThumbnails)?' checked="checked"':'')?> id="createThumbnails" name="createThumbnails" value="1" />
					<label for="createThumbnails">Generate Navigation Thumbnails</label>
				</div>
			</td>
		</tr>
		
		<tr id="sspAdvancedSettings">
			 <th scope="row">&nbsp;</th>
			 <td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			 <td>Start Album ID: <input name="startAlbumID" type="text" id="startAlbumID" value="<?php echo ( empty( $startAlbumID ) ? '' : $startAlbumID ); ?>" size="8" />&nbsp;&nbsp;Start Content ID: <input name="startContentID" type="text" id="startContentID" value="<?php echo ( empty( $startContentID ) ? '' : $startContentID ); ?>" size="8" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="ssp_name">Size (W x H)</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td><input name="sspWidth" type="text" id="sspWidth" value="<?php echo $sspWidth;?>" size="4" maxlength="4" />&nbsp;x&nbsp;<input name="sspHeight" type="text" id="sspHeight" value="<?php echo $sspHeight;?>" size="4" maxlength="4" />
			</td>
		</tr>
		</table>
		<!--/ Manage SlidePress Galleryies -->
		<input type="hidden" id="ssp_hidden_gallery_id" name="id" value="<?php echo $id; ?>" />
		<input type="hidden" name="xmlExportName" id="xmlExportName2" size="40" maxlength="50" />
		
		<?php /* form continued in ssp_settings.php ... */ ?>
		
		<?php include_once( 'ssp_settings.php' ); ?>
		
		<?php if (!strcmp($action, 'Update Gallery')) : ?>
						
		<!--/ Presentation Settings: -->
		
		<?php endif; ?>
		
		<br class="clear"/>
		
	<?php endif; ?>
	
	<br class="clear"/>
</div>