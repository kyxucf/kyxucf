<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

# add gallery page
?>
<div class="wrap ssp_wrap">
	
	<?php if ( ! isset( $_GET['iframe'] ) ): ?>
		<br class="clear"/>
	<?php endif; ?> 
	
	<?php include_once( 'ssp_header.php' ); ?>
	
	<?php include_once( 'ssp_subnav.php' ); ?>
	
	<h2>Add Gallery</h2>
	
	<?php if (!is_null($ssp_msg)) echo $ssp_msg; ?>
		
	<p>
		Use the form to create a slideshow gallery for a WordPress blog post or page.  
	 </p>
	
	<form method="post" id="ssp_form">
				
		<!-- Add a new SlidePress Gallery -->
		<table class="form-table ssp_form-table">	
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
				<textarea cols="" rows="" id="xmlManualSource" name="xmlManualSource"></textarea>
				&nbsp;&nbsp;<select id="xmlFileType" name="xmlFileType"><option value="">Select XML Type</option><?php foreach ($xmlFileType_options as $option) : ?><option value="<?php echo $option;?>"<?php if (!strcmp($xmlFileType, $option)) echo 'selected="selected"'; ?>><?php echo $option;?></option><?php endforeach;?></select>
					&nbsp;&nbsp;
					<input type="button" id="xmlTypeAdvanced" value="Advanced" class="button" />
				<div id="createThumbnailsField">
					<input type="checkbox" checked="checked" id="createThumbnails" name="createThumbnails" value="1" />
					<label for="createThumbnails">Generate Navigation Thumbnails</label>
				</div>
			</td>
		</tr>
		
		<tr id="sspAdvancedSettings">
			 <th scope="row">&nbsp;</th>
			 <td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			 <td>Start Album ID: <input name="startAlbumID" type="text" id="startAlbumID" value="" size="8" />&nbsp;&nbsp;Start Content ID: <input name="startContentID" type="text" id="startContentID" value="" size="8" /></td>
		</tr>
		

		<tr valign="top">
			<th scope="row"><label for="ssp_name">Size (W x H)</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td><input name="sspWidth" type="text" id="sspWidth" value="<?php echo(is_numeric($sspWidth) ? $sspWidth : '450'); ?>" size="4" maxlength="4" />&nbsp;x&nbsp;<input name="sspHeight" type="text" id="sspHeight" value="<?php echo(is_numeric($sspHeight) ? $sspHeight : '372'); ?>" size="4" maxlength="4" />
			</td>
		</tr>
		</table>
		
		<?php /* form continued in ssp_settings.php ... */ ?>
	
	<?php include_once( 'ssp_settings.php' ); ?>
	
	<br class="clear" />
	

</div>