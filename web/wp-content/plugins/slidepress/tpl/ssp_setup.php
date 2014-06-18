<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

# setup page

?>

<script type="text/javascript">
//<![CDATA[
(function(){
	var updatePercent = function(percent) {
		jQuery('#upload-progress-bar .bar').css('width', percent + '%');
		jQuery('#upload-progress-bar span').text(Math.round(percent) + '%');
	}
	
	var fileDialogComplete = function(num_files_queued) {
		try {
			if (num_files_queued > 0) {
				this.startUpload();
			}
		} catch (ex) {
			this.debug(ex);
		}
	}
	
	var uploadStart = function() {
		jQuery('#upload-progress-bar').fadeIn(300);
		return true;
	}
	
	var uploadComplete = function() {
	}
	
	var uploadProgress = function(file, completed, total) {
		var percent = completed / total * 100;
		updatePercent(percent);
	}
	
	var uploadSuccess = function(file, data, response) {
		updatePercent(100);
		jQuery('#upload-progress-bar').fadeOut(300);
		if (data == 'success') {
			jQuery('<div class="ssp_msg><code>slideshowpro.swf</code> uploaded successfully!</div>"').insertAfter('#screen-meta-links');
			jQuery('.ssp_error_source').hide();
		} else {
			jQuery('<div class="ssp_error ssp_error_source">' + data + '</div>').insertAfter('#screen-meta-links');
		}
	}
	
	var settings = {
		button_text: '<span class="button"><?php _e('Browse'); ?></span>',
		button_text_style: '.button { text-align: center; font-family:"Lucida Grande",Verdana,Arial,"Bitstream Vera Sans",sans-serif; }',
		button_height: "24",
		button_width: "132",
		button_text_top_padding: 2,
		button_image_url: '<?php echo includes_url('images/upload.png'); ?>',
		button_placeholder_id: "flash-browse-button",
		upload_url : "<?php echo $this->url ?>upload_swf.php",
		flash_url : "<?php echo includes_url('js/swfupload/swfupload.swf'); ?>",
		file_post_name: "ssp-swf",
		file_types: "slideshowpro.swf",
		post_params : {
			"auth_cookie" : "<?php if ( is_ssl() ) echo $_COOKIE[SECURE_AUTH_COOKIE]; else echo $_COOKIE[AUTH_COOKIE]; ?>",
			"_wpnonce" : "<?php echo wp_create_nonce('slideshowpro.swf-upload'); ?>",
		},
		file_size_limit : "<?php echo wp_max_upload_size(); ?>b",
//		file_dialog_start_handler : fileDialogStart,
//		file_queued_handler : fileQueued,
		upload_start_handler : uploadStart,
		upload_progress_handler : uploadProgress,
//		upload_error_handler : uploadError,
		upload_success_handler : uploadSuccess,
		upload_complete_handler : uploadComplete,
//		file_queue_error_handler : fileQueueError,
		file_dialog_complete_handler : fileDialogComplete,
//		swfupload_pre_load_handler: swfuploadPreLoad,
//		swfupload_load_failed_handler: swfuploadLoadFailed,
		debug: false
	};
	SWFUpload.onload = function() {
			var swfu;
			swfu = new SWFUpload(settings);
	};
	
})()
//]]>
</script>

<div class="wrap ssp_wrap">

	<br class="clear"/>
	
	<?php include_once( 'ssp_header.php' ); ?> 
	
	<?php include_once( 'ssp_subnav.php' ); ?>
	
	<h2>Setup</h2>

	<?php if (!is_null($ssp_msg)) echo $ssp_msg; ?>

	<form method="post" id="ssp_form" action="options.php">
		<?php settings_fields('ssp'); ?>

		<!-- SlideShowPro Integration -->
		<h3 id="swf-upload">SlideShowPro Player SWF Upload</h3>
		<table class="form-table ssp_form-table">
			<tr valign="top">
				<th scope="row" class="th-full">
					<div id="flash-upload-ui">
						<div id="flash-browse-button-wrapper"><div id="flash-browse-button"></div></div>
						<div id="upload-progress-bar"><div class="bar"></div><span></span></div>
					</div>
					<p>SlidePress <?php echo $this->version; ?> requires SlideShowPro Player SWF <?php echo $this->swf_version; ?> or higher. Click the "Browse" button above to upload <code>slideshowpro.swf</code> from the SlideShowPro Player SWF package on your local machine. You may also manually upload the swf to <code>wp-content/uploads/slidepress/flash/</code>.</p>
				</th>
			</tr>
		</table>
		<!--/ SlideShowPro Integration -->
		<!-- Check Swf Version -->  
		<!-- added 7/13 -->		
	<table class="form-table ssp_form-table">
		<tr valign="top">
    	<th scope="row" class="th-full">
    	<input name="ssp_check_swf_version" type="checkbox"<?php if ($ssp_check_swf_version) echo ' checked="checked"'; ?> id="ssp_check_swf_version" value="1" /> 
    	<label for="ssp_check_swf_version">Display SlideShowPro Player SWF Version Warning</label>
		<p><?php _e("If unchecked, you will not be alerted if your version of SlideShowPro Player SWF is incompatible with this version of slidepress."); ?></p>
		</th> 
    	</tr>
	</table>

		<!-- / swf version -->
		<!-- Presentation Effects  -->  
		<h3>Presentation Effects</h3>
		
		<table class="form-table ssp_form-table">
        
<?php /*
		<tr valign="top">
			<th scope="row"><label for="ssp_standaloneMode">Standalone / in-line integration</label></th>
			<td><input type="checkbox" checked="checked" id="ssp_standaloneMode" value="1" disabled="disabled" />
			&nbsp;&nbsp;Standard Mode. Use <strong>[slidepress gallery='gallery id']</strong> to display in posts &amp; pages.</td>
		</tr>
		*/ ?>
	   
		<tr valign="top">
			<th scope="row" class="th-full"><input name="ssp_thickboxMode" type="checkbox"<?php if ($ssp_thickboxMode) echo ' checked="checked"'; ?> id="ssp_thickboxMode" value="1" /> <label for="ssp_thickboxMode">ThickBox Mode</label>
				<p>Use <code>[slidepress thickbox='gallery id' title='mytitle' image='mypreviewurl']</code> to launch SlidePress in a ThickBox.</p>
			</th>
		</tr>
		</table>
		<!--/ Presentation Effects -->


		<!-- Accessibility Settings -->  
		<h3 id="accessibility-settings">Accessibility Settings</h3>
		
		<p>How to handle users without the Flash Player installed or Javascript enabled in their browser.</p>
		
		<table class="form-table ssp_form-table">
			<tr valign="top">
				<th scope="row"><label>No Flash Alternative</label></th>
				<td>
					
						<input id="ssp_no_flash_image" type="radio"<?php echo ($ssp_noFlash == 'images') ? ' checked="checked"' : '' ?> name="ssp_noFlash" value="images" /> <label for="ssp_no_flash_image">Display list of gallery images</label> (WordPress gallery and manual XML source only. Reverts to custom HTML content below if otherwise.)
				       <br /><br />
						<input id="ssp_no_flash_html" type="radio"<?php echo ($ssp_noFlash == 'html') ? ' checked="checked"' : '' ?> name="ssp_noFlash" value="html" /> <label for="ssp_no_flash_html">Display custom HTML content below:</label><br />
						<textarea style="margin-left:20px;" rows="4" cols="40" name="ssp_noFlashHtml"><?php echo get_option('ssp_noFlashHtml'); ?></textarea>
				</td>
			</tr>
		</table>

		<!-- Cross Domain -->  
		<h3 id="cross-domain-configuration">Cross-Domain Configuration</h3>
		
		<table class="form-table ssp_form-table">
			<tr valign="top">
  			<th scope="row" class="help"><label for="ssp_crossDomain">Approved Domain Access</label></th>
  			<td>
  				<textarea id="ssp_crossDomain" name="ssp_crossDomain" cols="40" rows="4"><?php echo $ssp_crossDomain; ?></textarea>
  				<?php if (!is_writable($_SERVER['DOCUMENT_ROOT'])): ?>
  					<p style="color:#ff0000;">
  						<?php _e("Currently the root path is not writable. Please check the permission again, or use the Export button below and upload manually to the root folder.");?>
  					</p>
  				<?php endif; ?>
  				<div class="form-wrap">
				<p>
  					<?php _e("A crossdomain.xml file is used by the Flash Player to ensure it has permission to load XML data from a domain. By default, SlidePress writes this file for you. If the file cannot be written automatically, download the file below then upload it to the root of this domain. Click the info button at left for more.")?>
  				</p>
				</div>
				<p><input id="xmlExport" type="submit" name="Export" value="Download cross-domain policy file" class="button" /></p>
  			</td>
			</tr>
		</table>

		<script>
			jQuery(document).ready(function() {
				jQuery('#xmlExport').click(function(event) {
					jQuery('#ssp_form').get(0).setAttribute('action', '<?php echo $this->url; ?>tools/export_crossdomain.php');
					jQuery('#ssp_form').addClass('exported');
					jQuery('#ssp_form').submit();
				});

				jQuery('.submit input[name=Submit]').click(function(event) {
					if (jQuery('#ssp_form').hasClass('exported')) {
						jQuery('#ssp_form').get(0).setAttribute('action',"options.php");
					}
				});
			});
		</script>

		<!-- / Cross Domain -->
		
		

		<!-- User Roles: -->
		<h3>User Roles</h3>
		
		<table class="form-table ssp_form-table">

		<tr valign="top">
			<th scope="row"><label for="addNewGallery">Add a New SlidePress Gallery</label></th>
			<td>
				<select name="ssp_addNewGallery" id="ssp_addNewGallery"><?php foreach ($this->roles as $option) : ?><option<?php if (!strcmp($ssp_addNewGallery, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;Minimum user level to add new SlidePress galleries
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="changeOptions">Modify / Delete SlidePress Galleries</label></th>
			<td>
				<select name="ssp_modifyGallery" id="ssp_modifyGallery"><?php foreach ($this->roles as $option) : ?><option<?php if (!strcmp($ssp_modifyGallery, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;Minimum user level to modify / delete existing SlidePress galleries
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="changeOptions">Change Options</label></th>
			<td>
				<select name="ssp_changeOptions" id="ssp_changeOptions"><?php foreach ($this->roles as $option) : ?><option<?php if (!strcmp($ssp_changeOptions, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;Minimum user level to modify these options
			</td>
		</tr>

		</table>
		<!--/ User Roles: -->

		<!-- Purge Data -->   
		<h3>Deactivation</h3>
		
		<table class="form-table ssp_form-table">

			<tr valign="top">
    			<th scope="row" class="th-full"><input name="ssp_purgeUponDeactivation" type="checkbox"<?php if ($ssp_purgeUponDeactivation) echo ' checked="checked"'; ?> id="ssp_purgeUponDeactivation" value="1" /> <label for="ssp_purgeUponDeactivation">Delete All Data Upon Deactivation</label>
<p><?php _e("If checked, all data and options created by SlidePress will be purged when the plugin is deactivated."); ?></p>
				</th> 

    	</tr>

		</table>
		<!-- / Purge/Restore Data -->
		                                                                       
		<p class="submit">
		<input type="submit" name="Submit" value="Save changes" class="button-primary" />
		</p>

	</form>


	<br class="clear"/>
	<br class="clear"/>
</div>