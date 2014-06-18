<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;
?>

		<input type="hidden" name="action" id="action" value="<?php echo $action;?>" />
		
		<table class="form-table ssp_form-table">
		<tr>			
			<th scope="row"><label>Style</label></th>
			<td width="8">&nbsp;</td>
			<td>
				<select id="sspChangeStyle" name="active_style">
					<?php if ($active_style == 'Custom'): ?>
						<option value="-1" selected="selected">Custom</option>
					<?php endif; ?>
					<optgroup label="Templates">
						<option style_type="xml" value="default">default</option>
						<?php if (!empty($styles_xml)) : ?>
							<?php foreach ($styles_xml as $option ) : ?><option style_type="xml" value="<?php echo $option;?>"<?php if (!strcmp($active_style, $option)) echo 'selected="selected"'; ?>><?php echo $option;?></option><?php endforeach;?>
						<?php endif;?>
					</optgroup>
<?php /*					<optgroup label="Existing Gallery Styles">
						<?php if (!empty($styles_db)) : ?>
							<?php foreach ($styles_db as $option ) : ?><option style_type="db" value="<?php echo $option;?>"<?php if (!strcmp($active_style, $option)) echo 'selected="selected"'; ?>><?php echo $option;?></option><?php endforeach;?>
						<?php endif; ?>
					</optgroup> */ ?>
					<optgroup label="Other galleries">
						<?php if ( in_array( $active_style, array_keys( $styles_db ) ) ): ?>
							<option style_type="db" selected="selected" value="<?php echo $active_style ?>"><?php echo $styles_db[$active_style]; ?></option>
						<?php endif; ?>
						<option value="use_saved">Use saved gallery style</option>
					</optgroup>
				</select>
		&nbsp;&nbsp;
			<span class="submit"><input class="button" type="button" id="visibility-toggle" value="Edit style settings" /></span>
			<?php if ($action != 'Save Gallery'):?>&nbsp;&nbsp;<input type="button" id="exportStyleButton" value="Export style" class="button" /><?php endif ?>
			</td>
		</tr> 
		<?php if ($action != 'Save Gallery'):?>	
			<tr id="exportStyleFields">
				<td>&nbsp;</td>
				<td width="8">&nbsp;</td>
				<td>
					<label for="xmlExportName1">Enter file name (e.g. mystyle.xml):</label><br />
					<input type="text" name="xmlExportName" id="xmlExportName1" value="<?php echo $sspGalleryId; ?>" size="40" maxlength="50" />
					&nbsp;&nbsp;
					<input type="button" id="xmlExport" value="Export" class="button-primary" />
				</td>
			</tr>
		<?php endif ?>
		  <tr>
			   <td colspan="3" class="ssp_form-update"><input class="button-primary" type="submit" value="<?php echo $action;?>" /></td>  
		  </tr>
		</table> 
		
		<br class="clear" />
		
		<?php if ($action == 'Save Gallery'):?>
		<!-- 
		<table class="ssp-save-gallery-first-button form-table ssp_form-table">
			<tr>				<th colspan="2" scope="row"></th>
				<td class="ssp_form-update"><p class="submit ssp_submit"><input type="submit" class="button-primary" value="<?php echo $action;?>" /></p></td>
			</tr>
		</table>
		-->
		<?php endif; ?>

		<br class="clear"/> 

		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function(){
				jQuery('.accord-head').click(function() {
					var el = jQuery(this);
					if (!el.next().is(':visible')) {
						jQuery('html,body').animate({scrollTop: el.offset().top}, 500);
					}
					el.next().toggle('normal');
					return false;
				}).next().hide();
			});
		</script> 
		
		<div class="edit-form">
			
		<h3>Style settings: "<?php echo($active_style); ?>"</h3>  
		
		<!-- <div class="wrap"> -->
		   

		<!-- Playback : -->
		<div class="accord">  

			<div class="accord-head">
				<div class="accord-toggle"></div>
				<h4>Playback</h4> <span>Transition, Pan and Zoom, and content loading</span>
			</div>
		
			<div class="accord-interior">

				<div class="edit-sub-head">
					<div class="cat-one">
					General				
					</div>
				</div>
		
			<table class="form-table ssp_form-table"> 
			<tr class="alt">			
				<th scope="row"><label for="displayMode">Display Mode</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="displayMode" id="displayMode"><?php foreach ($displayMode_options as $option) : ?><option<?php if (!strcmp($displayMode, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row"><label for="autoFinishMode">Auto Finish Mode</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="autoFinishMode" id="autoFinishMode"><?php foreach ($autoFinishMode_options as $option) : ?><option<?php if (!strcmp($autoFinishMode, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>
			<tr class="alt">			
				<th scope="row"><label for="contentOrder">Order</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="contentOrder" id="contentOrder"><?php foreach ($contentOrder_options as $option) : ?><option<?php if (!strcmp($contentOrder, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="startup">Startup</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<select name="startup" id="startup"><?php foreach ($startup_options as $option) : ?><option<?php if (!strcmp($startup, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>
			<tr valign="alt">
				<th scope="row"><label for="loop">Loop</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="ssploop" id="ssploop">
						<?php foreach ($loop_options as $option) : ?>
						<option<?php if (!strcmp($ssploop, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>">
							<?php echo $option;?>
						</option>
						<?php endforeach;?>
					</select>
				</td>
			</tr> 
			</table>
		
			<div class="edit-sub-head"> 
				<div class="cat-one">
				   <input name="panZoom" type="checkbox"<?php echo (!strcmp($panZoom, 'On' ) || !strcmp($panZoom, 'Visible' )) ? ' checked="checked"' : ''; ?> id="panZoom" value="On" /> <label for="panZoom">Use Pan and Zoom</label> <span>("Ken Burns" content style animation. "Crop to Fit" Content / Scale setting recommended.)</span>
				</div>
			</div>
			
			<!-- 
			  Checking the above displays the div below
			-->
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#panZoom').bind('change click', function() {
						if (jQuery('#panZoom:checked').val() == 'On') {
							if (init) {
								jQuery('#panZoomWrap').show('slide');
							} else {
								jQuery('#panZoomWrap').show();
							}
						} else {
							if (init) {
								jQuery('#panZoomWrap').hide('slide');
							} else {
								jQuery('#panZoomWrap').hide();
							}
						}
					}).change();
				});
			</script>
		
			<div id="panZoomWrap">
			<table class="form-table ssp_form-table"> 
			<tr valign="top" class="alt">
				<th scope="row"><label for="panZoomDirection">Direction</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="panZoomDirection" id="panZoomDirection"><?php foreach ($panZoomDirection_options as $option) : ?><option<?php if (!strcmp($panZoomDirection, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>

			<tr>			
				<th scope="row"><label for="panZoomFinish">Complete effect before ending</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="panZoomFinish" type="checkbox"<?php echo (!strcmp($panZoomFinish, 'On' ) || !strcmp($panZoomFinish, 'Visible' )) ? ' checked="checked"' : ''; ?> id="panZoomFinish" value="On" />
				</td>
			</tr>

			<tr valign="top" class="alt">
				<th scope="row"><label for="panZoomScale">Scale</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="panZoomScale" type="text" id="panZoomScale" value="<?php echo $panZoomScale;?>" size="10" />&nbsp;&nbsp;Minimum/maximum effect scale. 1 = 100% (no scale). 
				</td>
			</tr> 
			</table>
			</div>

			<div class="edit-sub-head"> 
				<div class="cat-one">
				   Transition <span>(Effect applied when moving from one photo/video to the next)</span>
				</div>
			</div>			 
		
			<table class="form-table ssp_form-table"> 

					<tr class="alt">					
						<th scope="row"><label for="transitionStyle">Style</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="transitionStyle" id="transitionStyle"><?php foreach ($transitionStyle_options as $option) : ?><option<?php if (!strcmp($transitionStyle, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="transitionDirection">Direction</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="transitionDirection" id="transitionDirection"><?php foreach ($transitionDirection_options as $option) : ?><option<?php if (!strcmp($transitionDirection, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr>
			<tr class="alt">					
				<th scope="row"><label for="transitionLength">Length</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="transitionLength" type="text" id="transitionLength" value="<?php echo $transitionLength;?>" size="4" maxlength="4" />&nbsp;seconds
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="transitionPause">Pause</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="transitionPause" type="text" id="transitionPause" value="<?php echo $transitionPause;?>" size="4" maxlength="4" />&nbsp;seconds
				</td>
			</tr>
			<tr class="button-row">
				<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
			</tr>
			</table>	   
		
	   </div>
	</div>
	
	 <!--/ Playback: -->
	   
			 
	
		 <!-- Content: --> 
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Content</h4> <span>Photo and video scaling, alignment and interactivity</span>
			</div>	  

			<div class="accord-interior">

				 <div class="edit-sub-head">  
					<div class="cat-one">
					   Content <span>(Applied to slideshow photos and videos)</span>
					</div>	
				 </div>

					<table class="form-table ssp_form-table">
					<tr class="alt">			
						<th scope="row"><label for="contentAlign">Alignment</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<select name="contentAlign" id="contentAlign"><?php foreach ($contentAlign_options as $option) : ?><option<?php if (!strcmp($contentAlign, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="contentScale">Scale</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<select name="contentScale" id="contentScale"><?php foreach ($contentScale_options as $option) : ?><option<?php if (!strcmp($contentScale, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
						</td>
					</tr>
				</table>
						
					
					<!-- 
						Parent div for scale percent option. 
						Show if Scale above is set to Downscale Only or Proportional.
					-->
					<script type="text/javascript" charset="utf-8">
						jQuery(document).ready(function() {
							jQuery('#contentScale').change(function() {
								if (jQuery('#contentScale').val() == 'Downscale Only' || jQuery('#contentScale').val() == 'Proportional') {
									if (init) {
										jQuery('#scalePercentWrap').show('slide');
									} else {
										jQuery('#scalePercentWrap').show();
									}
								} else {
									if (init) {
										jQuery('#scalePercentWrap').hide('slide');
									} else {
										jQuery('#scalePercentWrap').hide();
									}
								}
							}).change();
						});
					</script>
					
					<div id="scalePercentWrap">
						<table class="form-table ssp_form-table">
						<tr>
							<th scope="row">&nbsp;</th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
							<td>
								 <table class="form-table ssp-settings-nested">
									<tr>			
										<th scope="row"><label for="contentScalePercent">Scale Percent</label></th>
										<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
											<input name="contentScalePercent" type="text" id="contentScalePercent" value="<?php echo $contentScalePercent;?>" size="3" maxlength="3" />&nbsp;&nbsp;Percentage (e.g., 2 = 200%)	
										</td>
									</tr>
								</table>
							</td>
						</tr>  
						</table>	
					</div>

				 <div class="edit-sub-head">  
					<div class="cat-one">
					   Content Area <span>(Main empty space where content appears)</span>
					</div>	
				 </div>


				<table class="form-table ssp_form-table">
				<tr class="alt">			
					<th scope="row"><label for="contentAreaInteractivity">Interactivity</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<select name="contentAreaInteractivity" id="contentAreaInteractivity"><?php foreach ($contentAreaInteractivity_options as $option) : ?><option<?php if (!strcmp($contentAreaInteractivity, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="contentAreaAction">Action</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<select name="contentAreaAction" id="contentAreaAction"><?php foreach ($contentAreaAction_options as $option) : ?><option<?php if (!strcmp($contentAreaAction, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
					</td>
				</tr> 
				<tr class="alt">			
					<th scope="row"><label for="contentAreaBackgroundColor">Background Color</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentAreaBackgroundColor" type="text" id="contentAreaBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $contentAreaBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $contentAreaBackgroundColor);?>" />
					</td>
				</tr> 
				<tr valign="top">
					<th scope="row"><label for="contentAreaStrokeColor">Stroke Color</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentAreaStrokeColor" type="text" id="contentAreaStrokeColor" value="#<?php echo preg_replace('/^0x/', '', $contentAreaStrokeColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $contentAreaStrokeColor);?>" />
					</td>
				</tr>
				<tr class="alt">			
					<th scope="row"><label for="contentAreaBackgroundAlpha">Background Alpha</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentAreaBackgroundAlpha" type="text" id="contentAreaBackgroundAlpha" value="<?php echo $contentAreaBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="contentAreaStrokeAppearance">Show Outer Stroke</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentAreaStrokeAppearance" type="checkbox"<?php echo (!strcmp($contentAreaStrokeAppearance, 'On' ) || !strcmp($contentAreaStrokeAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="contentAreaStrokeAppearance" value="Visible" />
					</td>
				</tr>
				</table>


				 <div class="edit-sub-head">  
					<div class="cat-one">
					   Content Frame <span>(Outer frame drawn underneath/around slideshow content)</span>
					</div>	
				 </div>

				  <table class="form-table ssp_form-table">
					<tr class="alt">			
						<th scope="row"><label for="contentFramePadding">Padding</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="contentFramePadding" type="text" id="contentFramePadding" value="<?php echo $contentFramePadding;?>" size="4" maxlength="4" />&nbsp;px
						</td>
					</tr>
				<tr valign="top">
					<th scope="row"><label for="contentFrameColor">Color</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentFrameColor" type="text" id="contentFrameColor" value="#<?php echo preg_replace('/^0x/', '', $contentFrameColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $contentFrameColor);?>" />
					</td>
				</tr>
				<tr valign="top" class="alt">
					<th scope="row"><label for="contentFrameAlpha">Alpha</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentFrameAlpha" type="text" id="contentFrameAlpha" value="<?php echo $contentFrameAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
					</td>
				</tr> 
				 <tr>			
						<th scope="row"><label for="contentFrameStrokeAppearance">Show Outer Stroke</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="contentFrameStrokeAppearance" type="checkbox"<?php echo (!strcmp($contentFrameStrokeAppearance, 'On' ) || !strcmp($contentFrameStrokeAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="contentFrameStrokeAppearance" value="Visible" />
					</td>
				</tr>
				</table>
				 
				
				<!-- 
					Parent div for content frame stroke.
					Show if Show Outer Stroke above is checked. Otherwise not.
				-->
				
				<script type="text/javascript" charset="utf-8">
					jQuery(document).ready(function() {
						jQuery('#contentFrameStrokeAppearance').bind('change click', function() {
							if (jQuery('#contentFrameStrokeAppearance:checked').val() == 'Visible') {
								if (init) {
									jQuery('#outerStrokeWrap').show('slide');
								} else {
									jQuery('#outerStrokeWrap').show();
								}
							} else {
								if (init) {
									jQuery('#outerStrokeWrap').hide('slide');
								} else {
									jQuery('#outerStrokeWrap').hide();
								}
							}
						}).change();
					});
				</script>
				
				<div id="outerStrokeWrap">
					<table class="form-table ssp_form-table">
					<tr>
						<th scope="row">&nbsp;</th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
						<td>
							 <table class="form-table ssp-settings-nested">
								<tr>			
									<th scope="row"><label for="contentFrameStrokeColor">Stroke Color</label></th>
									<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
										<input name="contentFrameStrokeColor" type="text" id="contentFrameStrokeColor" value="#<?php echo preg_replace('/^0x/', '', $contentFrameStrokeColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $contentFrameStrokeColor);?>" />
									</td>
								</tr>
							</table>
						</td>
					</tr>  
					</table>	
				</div>
				 
				<table class="form-table ssp_form-table">
				<tr class="button-row">
					<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
				</tr>
				</table>


		</div>	
		</div>
		<!--/ Content: --> 
							 


		<!-- Navigation: -->  
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Navigation</h4> <span>Settings for the navigation bar</span>
			</div>	  

			<div class="accord-interior"> 
				
				 <div class="edit-sub-head"> 
					<div class="cat-one">
					   <input name="" type="checkbox" id="showNavigation" value="Visible"<?php if ( $navAppearance != 'Hidden' ) echo ' checked="checked"'; ?> /> <label for="showNavigation">Show Navigation</label>
					</div>
				</div>	  
			
				 
			<!-- parent div for all navigation settings-->
			
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#showNavigation').bind('change click', function() {
						if (jQuery('#showNavigation:checked').val() == 'Visible') {
							if (init) {
								jQuery('#showNavigationWrap').show('slide');
							} else {
								jQuery('#showNavigationWrap').show();
							}
							if (jQuery('#navAppearance').val() == 'Hidden') {
								jQuery('#navAppearance').val('Always Visible');
							}
						} else {
							if (init) {
								jQuery('#showNavigationWrap').hide('slide');
							} else {
								jQuery('#showNavigationWrap').hide();
							}
							jQuery('#navAppearance').val('Hidden');
						}
					}).change();
					
					jQuery('#navAppearance').change(function() {
						if (jQuery('#navAppearance').val() == 'Hidden' && jQuery('#showNavigation:checked').val() == 'Visible') {
							jQuery('#showNavigation').attr('checked', false).change();
						}
					}).change();
				});
			</script>
			
			<div id="showNavigationWrap">

			<table class="form-table ssp_form-table">

				<tr class="alt">			
			<th scope="row"><label for="navAppearance">Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<select name="navAppearance" id="navAppearance" next="27"><?php foreach ($navAppearance_options as $option) : ?><option<?php if (!strcmp($navAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr> 
		
		<tr>			
			<th scope="row"><label for="navPosition">Position</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<select name="navPosition" id="navPosition"><?php foreach ($navPosition_options as $option) : ?><option<?php if (!strcmp($navPosition, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr> 
		<tr class="alt">			
			<th scope="row"><label for="navGradientAppearance">Gradient Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<select name="navGradientAppearance" id="navGradientAppearance"><?php foreach ($navGradientAppearance_options as $option) : ?><option<?php if (!strcmp($navGradientAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		</table>
			
		<!-- parent div for gradient appearance settings -->
		
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#navGradientAppearance').change(function() {
					if (jQuery('#navGradientAppearance').val() != 'Hidden') {
						if (init) {
							jQuery('#navGradientWrap').show('slide');
						} else {
							jQuery('#navGradientWrap').show();
						}
					} else {
						if (init) {
							jQuery('#navGradientWrap').hide('slide');
						} else {
							jQuery('#navGradientWrap').hide();
						}
					}
				}).change();
			});
		</script>
		
		<div id="navGradientWrap">
			<table class="form-table ssp_form-table">
			<tr class="alt">
				<th scope="row">&nbsp;</th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					 <table class="form-table ssp-settings-nested">
						<tr valign="top">
							<th scope="row"><label for="navGradientAlpha">Gradient Alpha</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="navGradientAlpha" type="text" id="navGradientAlpha" value="<?php echo $navGradientAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	
							</td>
						</tr>
					</table>
				</td>
			</tr>  
			</table>	
		</div>
			
		<table class="form-table ssp_form-table">
		<tr>			
			<th scope="row"><label for="navBackgroundColor">Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navBackgroundColor" type="text" id="navBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $navBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navBackgroundColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="navBackgroundAlpha">Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navBackgroundAlpha" type="text" id="navBackgroundAlpha" value="<?php echo $navBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  

			</td>
		</tr>
		</table>
		
		<div class="edit-sub-head">
			   <div class="cat-two">
			Links
			</div>
		 </div>
		
		<table class="form-table ssp_form-table">
		<tr valign="top" class="alt">
				<th scope="row" class="indent"><label for="navLinksBackgroundColor">Background Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="navLinksBackgroundColor" type="text" id="navLinksBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $navLinksBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navLinksBackgroundColor);?>" />
				</td>
			</tr>
		<tr valign="top">
			<th scope="row" class="indent"><label for="navLinksBackgroundAlpha">Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinksBackgroundAlpha" type="text" id="navLinksBackgroundAlpha" value="<?php echo $navLinksBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	 
			</td>
		</tr>	
		<tr valign="top">
			<th scope="row" class="indent"><label for="navLinksBackgroundShadowAlpha">Background Shadow Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinksBackgroundShadowAlpha" type="text" id="navLinksBackgroundShadowAlpha" value="<?php echo $navLinksBackgroundShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	 
			</td>
		</tr>	
		<tr class="alt">			
			<th scope="row" class="indent"><label for="navLinkSpacing">Spacing</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkSpacing" type="text" id="navLinkSpacing" value="<?php echo $navLinkSpacing;?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr>
		<tr>			
			<th scope="row" class="indent"><label for="navLinkActiveColor">Active Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkActiveColor" type="text" id="navLinkActiveColor" value="#<?php echo preg_replace('/^0x/', '', $navLinkActiveColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navLinkActiveColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row" class="indent"><label for="navLinkInactiveColor">Inactive Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkInactiveColor" type="text" id="navLinkInactiveColor" value="#<?php echo preg_replace('/^0x/', '', $navLinkInactiveColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navLinkInactiveColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="indent"><label for="navLinkRolloverColor">Rollover Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<input name="navLinkRolloverColor" type="text" id="navLinkRolloverColor" value="#<?php echo preg_replace('/^0x/', '', $navLinkRolloverColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navLinkRolloverColor);?>" />
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row" class="indent"><label for="navLinkShadowAlpha">Link Shadow Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<input name="navLinkShadowAlpha" type="text" id="navLinkShadowAlpha" value="<?php echo $navLinkShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	 
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row" class="indent"><label for="navLinkAnimate">Animation</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkAnimate" type="checkbox"<?php echo (!strcmp($navLinkAnimate, 'On' ) || !strcmp($navLinkAnimate, 'Visible' )) ? ' checked="checked"' : ''; ?> id="navLinkAnimate" value="Visible" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="indent"><label for="navLinkAppearance">Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="navLinkAppearance" id="navLinkAppearance"><?php foreach ($navLinkAppearance_options as $option) : ?><option<?php if (!strcmp($navLinkAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		</table>
		
			 
		<!-- parent div of number link settings -->
		
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#navLinkAppearance').change(function() {
					if (jQuery('#navLinkAppearance').val() == 'Numbers') {
						if (init) {
							jQuery('#navNumbersWrap').show('slide');
							jQuery('#navThumbsWrap').hide('slide');
						} else {
							jQuery('#navNumbersWrap').show();
							jQuery('#navThumbsWrap').hide();
						}
					} else {
						if (init) {
							jQuery('#navThumbsWrap').show('slide');
							jQuery('#navNumbersWrap').hide('slide');
						} else {
							jQuery('#navThumbsWrap').show();
							jQuery('#navNumbersWrap').hide();
						}
					}
				}).change();
			});
		</script>
		
		<div id="navNumbersWrap">
		<table class="form-table ssp_form-table">
		<tr>
			<th class="indent">&nbsp;</th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<!-- Number parameters -->
				<table class="form-table ssp-settings-nested">
				<tr>			
					<th scope="row"><label for="navNumberLinkSize">Number Text Size</label></th>
					<td>
						<input name="navNumberLinkSize" type="text" id="navNumberLinkSize" value="<?php echo $navNumberLinkSize;?>" size="4" maxlength="4" />&nbsp;px
					</td>
				</tr>
				</table> 
			</td>
		</tr>
		</table>
		</div>
		
		
		  <!-- parent div of thumbnail link settings -->
		<div id="navThumbsWrap">
		<table class="form-table ssp_form-table">
			   <tr>
					<th class="indent">&nbsp;</th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
					<td>  
			
						 <table class="form-table ssp-settings-nested">
								<tr valign="top">
									<th scope="row"><label for="navThumbLinkSize">Thumbnail Size</label></th>
									<td>
										<input name="navThumbLinkSize[]" type="text" id="navThumbLinkSize0" value="<?php echo $navThumbLinkSize[0];?>" size="4" maxlength="4" />&nbsp;x&nbsp;<input name="navThumbLinkSize[]" type="text" id="navThumbLinkSize1" value="<?php echo $navThumbLinkSize[1];?>" size="4" maxlength="4" />
									</td>
								</tr>	
								 <tr>			
										<th scope="row"><label for="navThumbLinkInactiveAlpha">Thumbnail Inactive Alpha</label></th>
										<td>
											<input name="navThumbLinkInactiveAlpha" type="text" id="navThumbLinkInactiveAlpha" value="<?php echo $navThumbLinkInactiveAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
										</td>
									</tr>
									<tr valign="top">
										<th scope="row"><label for="navThumbLinkStrokeWeight">Thumbnail Stroke Weight</label></th>
										<td>
											<input name="navThumbLinkStrokeWeight" type="text" id="navThumbLinkStrokeWeight" value="<?php echo $navThumbLinkStrokeWeight;?>" size="4" maxlength="4" />&nbsp;px
										</td>
									</tr>
							</table>	
				   </td>
			</tr>
		</table>
		</div>
			
		
		 <div class="edit-sub-head">
			   <div class="cat-two">
			Buttons
			</div>
		 </div>

		<table class="form-table ssp_form-table">
		<tr valign="top" class="alt">
			<th scope="row" class="indent"><label for="navButtonsAppearance">Buttons Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="navButtonsAppearance" id="navButtonsAppearance"><?php foreach ($navButtonsAppearance_options as $option) : ?><option<?php if (!strcmp($navButtonsAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" class="indent"><label for="navButtonInactiveAlpha">Inactive Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
			<td>
				<input name="navButtonInactiveAlpha" type="text" id="navButtonInactiveAlpha" value="<?php echo $navButtonInactiveAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  

			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row" class="indent"><label for="navButtonStyle">Style</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="navButtonStyle" id="navButtonStyle" next="27"><?php foreach ($navButtonStyle_options as $option) : ?><option<?php if (!strcmp($navButtonStyle, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		</table>
		
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#navButtonStyle').change(function() {
					if (jQuery('#navButtonStyle').val() == 'Default') {
						if (init) {
							jQuery('#buttonStyleWrap').show('slide');
						} else {
							jQuery('#buttonStyleWrap').show();
						}
					} else {
						if (init) {
							jQuery('#buttonStyleWrap').hide('slide');
						} else {
							jQuery('#buttonStyleWrap').hide();
						}
					}
				}).change();
			});
		</script>
		
		<!-- container div for button style options -->
		<div id="buttonStyleWrap">
			<table class="form-table ssp_form-table">
			<tr class="alt">
				<th class="indent">&nbsp;</th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<table class="form-table ssp-settings-nested">
					<tr valign="top">
						<th scope="row" class="indent"><label for="navButtonShadowStyle">Shadow Style</label></th>
						<td>
							<select name="navButtonShadowStyle" id="navButtonShadowStyle" next="27"><?php foreach ($navButtonShadowStyle_options as $option) : ?><option<?php if (!strcmp($navButtonShadowStyle, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="navButtonColor">Color</label></th>
						<td>
							<input name="navButtonColor" type="text" id="navButtonColor" value="#<?php echo preg_replace('/^0x/', '', $navButtonColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navButtonColor);?>" />
						</td>
					</tr>
					<tr>			
						<th scope="row" class="indent"><label for="navButtonRolloverColor">Rollover Color</label></th>
						<td>
							<input name="navButtonRolloverColor" type="text" id="navButtonRolloverColor" value="#<?php echo preg_replace('/^0x/', '', $navButtonRolloverColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navButtonRolloverColor);?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="indent"><label for="navButtonGlowAlpha">Glow Alpha</label></th>
						<td>
							<input name="navButtonGlowAlpha" type="text" id="navButtonGlowAlpha" value="<?php echo $navButtonGlowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  

						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="indent"><label for="navButtonGradientAlpha">Gradient Alpha</label></th>
						<td>
							<input name="navButtonGradientAlpha" type="text" id="navButtonGradientAlpha" value="<?php echo $navButtonGradientAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  

						</td>
					</tr>
					<tr>			
						<th scope="row"><label for="navButtonShadowAlpha">Shadow Alpha</label></th>
						<td>
							<input name="navButtonShadowAlpha" type="text" id="navButtonShadowAlpha" value="<?php echo $navButtonShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	
						</td>
					</tr>
					</table>
				</td>
			</tr>
			</table>	 
		</div>
		
		 <div class="edit-sub-head">
				   <div class="cat-two">
						 <input name="navLinkPreviewAppearance" type="checkbox"<?php echo (!strcmp($navLinkPreviewAppearance, 'On' ) || !strcmp($navLinkPreviewAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="navLinkPreviewAppearance" value="Visible" /> <label for="navLinkPreviewAppearance">Show Link Previews</label>				  
				</div> 
			</div>
		
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#navLinkPreviewAppearance').bind('change click', function() {
						if (jQuery('#navLinkPreviewAppearance:checked').val() == 'Visible') {
							if (init) {
								jQuery('#linkPreviewWrap').show('slide');
							} else {
								jQuery('#linkPreviewWrap').show();
							}
						} else {
							if (init) {
								jQuery('#linkPreviewWrap').hide('slide');
							} else {
								jQuery('#linkPreviewWrap').hide();
							}
						}
					}).change();
				});
			</script>
			
		<!-- parent div for link preview settings -->
		<div id="linkPreviewWrap">
		<table class="form-table ssp_form-table">
		<tr class="alt">			
			<th scope="row" class="indent"><label for="navLinkPreviewSize">Size</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkPreviewSize[]" type="text" id="navLinkPreviewSize0" value="<?php echo $navLinkPreviewSize[0];?>" size="4" maxlength="4" />&nbsp;x&nbsp;<input name="navLinkPreviewSize[]" type="text" id="navLinkPreviewSize1" value="<?php echo $navLinkPreviewSize[1];?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr> 
		<tr>			
			<th scope="row" class="indent"><label for="navLinkPreviewBackgroundColor">Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkPreviewBackgroundColor" type="text" id="navLinkPreviewBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $navLinkPreviewBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $navLinkPreviewBackgroundColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row" class="indent"><label for="navLinkPreviewBackgroundAlpha">Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkPreviewBackgroundAlpha" type="text" id="navLinkPreviewBackgroundAlpha" value="<?php echo $navLinkPreviewBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row" class="indent"><label for="navLinkPreviewShadowAlpha">Shadow Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkPreviewShadowAlpha" type="text" id="navLinkPreviewShadowAlpha" value="<?php echo $navLinkPreviewShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>
		<tr>			
			<th scope="row" class="indent"><label for="navLinkPreviewStrokeWeight">Stroke Weight</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="navLinkPreviewStrokeWeight" type="text" id="navLinkPreviewStrokeWeight" value="<?php echo $navLinkPreviewStrokeWeight;?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr>
		 <tr valign="top" class="alt">
				<th scope="row" class="indent"><label for="navLinkPreviewScale">Scale</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="navLinkPreviewScale" id="navLinkPreviewScale"><?php foreach ($navLinkPreviewScale_options as $option) : ?><option<?php if (!strcmp($navLinkPreviewScale, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
				</td>
			</tr> 

		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>
		</div>
		
		</div> 
		
		</div>
		
		</div>
		<!--/ Navigation: -->



								 

		<!-- Gallery: --> 
		<div class="accord"> 
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Gallery</h4> <span>Space where albums are selected and organized</span>
			</div>	  

			<div class="accord-interior"> 
				
				 <div class="edit-sub-head">
			   <div class="cat-one">
			  <!--  <input name="galleryAppearance" type="checkbox"<?php echo (!strcmp($galleryAppearance, 'On' ) || !strcmp($galleryAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="galleryAppearance" value="Visible" />  -->
			<select name="galleryAppearance" id="galleryAppearance">
				<?php foreach ($galleryAppearance_options as $option) : ?>
				<option<?php if (!strcmp($galleryAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option>
				<?php endforeach;?>
			</select>
<label for="galleryAppearance">Gallery Appearance</label>	  
			</div>
			</div>		 
			 
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#galleryAppearance').bind('change', function() {
						if (jQuery('#galleryAppearance').val() != 'Hidden') {
							if (init) {
								jQuery('#galleryAllWrap').show('slide');
							} else {
								jQuery('#galleryAllWrap').show();
							}
						} else {
							if (init) {
								jQuery('#galleryAllWrap').hide('slide');
							} else {
								jQuery('#galleryAllWrap').hide();
							}
						}
					}).change();
				});
			</script>
			
		<!-- wrapper div for all gallery props -->
		<div id="galleryAllWrap">
					
		<table class="form-table ssp_form-table">
			<tr class="alt">			
				<th scope="row"><label for="galleryBackgroundColor">Background Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="galleryBackgroundColor" type="text" id="galleryBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $galleryBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryBackgroundColor);?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="galleryBackgroundAlpha">Background Alpha</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="galleryBackgroundAlpha" type="text" id="galleryBackgroundAlpha" value="<?php echo $galleryBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
				</td>
			</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="galleryColumns">Columns</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="galleryColumns" type="text" id="galleryColumns" value="<?php echo $galleryColumns;?>" size="4" maxlength="4" />
			</td>
		</tr>
		<tr>			
			<th scope="row"><label for="galleryRows">Rows</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="galleryRows" type="text" id="galleryRows" value="<?php echo $galleryRows;?>" size="4" maxlength="4" />
			</td>
		</tr>
		<tr valign="top"  class="alt">
			<th scope="row"><label for="galleryOrder">Order</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="galleryOrder" id="galleryOrder"><?php foreach ($galleryOrder_options as $option) : ?><option<?php if (!strcmp($galleryOrder, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr>			
			<th scope="row"><label for="galleryPadding">Padding</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="galleryPadding" type="text" id="galleryPadding" value="<?php echo $galleryPadding;?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="galleryContentShadowAlpha">Content Shadow Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="galleryContentShadowAlpha" type="text" id="galleryContentShadowAlpha" value="<?php echo $galleryContentShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>
		</table>
		
		<div class="edit-sub-head">
			   <div class="cat-two">
				   <input name="galleryNavAppearance" type="checkbox"<?php echo (!strcmp($galleryNavAppearance, 'On' ) || !strcmp($galleryNavAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="galleryNavAppearance" value="Visible" /> <label for="galleryNavAppearance">Show Screen Navigation</label>	   
			</div> 
		</div>
		 
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#galleryNavAppearance').bind('change click', function() {
					if (jQuery('#galleryNavAppearance:checked').val() == 'Visible') {
						if (init) {
							jQuery('#galleryScreenWrap').show('slide');
						} else {
							jQuery('#galleryScreenWrap').show();
						}
					} else {
						if (init) {
							jQuery('#galleryScreenWrap').hide('slide');
						} else {
							jQuery('#galleryScreenWrap').hide();
						}
					}
				}).change();
			});
		</script>
		
		<!-- wrapper div for screen navigation props -->
		<div id="galleryScreenWrap">
			<table class="form-table ssp_form-table">
			<tr valign="top"  class="alt">
				<th scope="row" class="indent"><label for="galleryNavActiveColor">Active Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<input name="galleryNavActiveColor" type="text" id="galleryNavActiveColor" value="#<?php echo preg_replace('/^0x/', '', $galleryNavActiveColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryNavActiveColor);?>" />
				</td>
			</tr>
			<tr>			
				<th scope="row" class="indent"><label for="galleryNavStrokeColor">Stroke Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<input name="galleryNavStrokeColor" type="text" id="galleryNavStrokeColor" value="#<?php echo preg_replace('/^0x/', '', $galleryNavStrokeColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryNavStrokeColor);?>" />
				</td>
			</tr>
			<tr valign="top" class="alt">
				<th scope="row" class="indent"><label for="galleryNavTextColor">Text Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<input name="galleryNavTextColor" type="text" id="galleryNavTextColor" value="#<?php echo preg_replace('/^0x/', '', $galleryNavTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryNavTextColor);?>" />
				</td>
			</tr>
			<tr>			
				<th scope="row" class="indent"><label for="galleryNavInactiveColor">Inactive Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="galleryNavInactiveColor" type="text" id="galleryNavInactiveColor" value="#<?php echo preg_replace('/^0x/', '', $galleryNavInactiveColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryNavInactiveColor);?>" />
				</td>
			</tr>
			<tr valign="top" class="alt">
				<th scope="row" class="indent"><label for="galleryNavRolloverColor">Rollover Color</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<input name="galleryNavRolloverColor" type="text" id="galleryNavRolloverColor" value="#<?php echo preg_replace('/^0x/', '', $galleryNavRolloverColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $galleryNavRolloverColor);?>" />
				</td>
			</tr>
			<tr>			
				<th scope="row" class="indent"><label for="galleryNavTextSize">Text Size</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td>
					<input name="galleryNavTextSize" type="text" id="galleryNavTextSize" value="<?php echo $galleryNavTextSize;?>" size="4" maxlength="4" />
				</td>
			</tr> 
			</table>  
		</div>
	   
		<table class="form-table ssp_form-table">
			<tr class="button-row">
				<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
			</tr>  
		</table>
		
		</div> 
		
		</div>
		
		</div>
		<!--/ Gallery: -->		
							 

		<!-- Album: -->	  
		<div class="accord">
		
				<div class="accord-head">
					<div class="accord-toggle"></div>
					<h4>Albums</h4> <span>Elements in the gallery for loading albums</span>
				</div>	  

				<div class="accord-interior"> 
					
					<div class="edit-sub-head">
						<div class="cat-one">
						   General
						</div>
					</div>
					
					<table class="form-table ssp_form-table">
						<tr class="alt">				
							<th scope="row"><label for="albumBackgroundColor">Background Color</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumBackgroundColor" type="text" id="albumBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $albumBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumBackgroundColor);?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="albumRolloverColor">Rollover Color</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumRolloverColor" type="text" id="albumRolloverColor" value="#<?php echo preg_replace('/^0x/', '', $albumRolloverColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumRolloverColor);?>" />
							</td>
						</tr>
						<tr class="alt">			
							<th scope="row"><label for="albumBackgroundAlpha">Background Alpha</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
							<td>
								<input name="albumBackgroundAlpha" type="text" id="albumBackgroundAlpha" value="<?php echo $albumBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	
							</td>
						</tr>
						<tr>			
							<th scope="row"><label for="albumPadding">Padding</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumPadding" type="text" id="albumPadding" value="<?php echo $albumPadding;?>" size="4" maxlength="4" />&nbsp;px
							</td>
						</tr>
						<tr class="alt">			
							<th scope="row"><label for="albumStrokeAppearance">Show Outer Stroke</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
							<td>
							<input name="albumStrokeAppearance" type="checkbox"<?php echo (!strcmp($albumStrokeAppearance, 'On' ) || !strcmp($albumStrokeAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="albumStrokeAppearance" value="Visible" />
							</td>
						</tr>
					</table>
					
					<script type="text/javascript" charset="utf-8">
						jQuery(document).ready(function() {
							jQuery('#albumStrokeAppearance').bind('change click', function() {
								if (jQuery('#albumStrokeAppearance:checked').val() == 'Visible') {
									if (init) {
										jQuery('#albumStrokeWrap').show('slide');
									} else {
										jQuery('#albumStrokeWrap').show();
									}
								} else {
									if (init) {
										jQuery('#albumStrokeWrap').hide('slide');
									} else {
										jQuery('#albumStrokeWrap').hide();
									}
								}
							}).change();
						});
					</script>
					
					<!--
					  When "Show Outer Stroke" above is checked, show the div below (and vice versa)
					
					-->
					<div id="albumStrokeWrap">
						<table class="form-table ssp_form-table">
						<tr class="alt">
							<th scope="row">&nbsp;</th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
							<td>
								 <table class="form-table ssp-settings-nested">
									<tr>				
										<th scope="row"><label for="albumStrokeColor">Color</label></th>
										<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
											<input name="albumStrokeColor" type="text" id="albumStrokeColor" value="#<?php echo preg_replace('/^0x/', '', $albumStrokeColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumStrokeColor);?>" />
										</td>
									</tr>
								</table>
							</td>
						</tr>  
						</table>	
					</div>
					
					
					<div class="edit-sub-head">
						<div class="cat-one">
						  Preview Graphic <span>(The album thumbnail image)
						</div>
					</div> 
					
					 <table class="form-table ssp_form-table">

					 <tr class="alt">				
						<th scope="row"><label for="albumPreviewStyle">Style</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<select name="albumPreviewStyle" id="albumPreviewStyle"><?php foreach ($albumPreviewStyle_options as $option) : ?><option<?php if (!strcmp($albumPreviewStyle, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
							</td>
						</tr> 
						
						<!--
						  If "Scale" below is set to "Fill", we should collapse the div above
						that is wrapping the 'album text settings' (since text is not written).
						-->
						
						<tr valign="top">
							<th scope="row"><label for="albumPreviewScale">Scale</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<select name="albumPreviewScale" id="albumPreviewScale"><?php foreach ($albumPreviewScale_options as $option) : ?><option<?php if (!strcmp($albumPreviewScale, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
							</td>
						</tr>
						
						<tr class="alt">				
							<th scope="row"><label for="albumPreviewSize">Size</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumPreviewSize[]" type="text" id="albumPreviewSize0" value="<?php echo $albumPreviewSize[0];?>" size="4" maxlength="4" />&nbsp;x&nbsp;<input name="albumPreviewSize[]" type="text" id="albumPreviewSize1" value="<?php echo $albumPreviewSize[1];?>" size="4" maxlength="4" />&nbsp;px
							</td>
						</tr>
						<tr>				
							<th scope="row"><label for="albumPreviewStrokeColor">Stroke Color</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumPreviewStrokeColor" type="text" id="albumPreviewStrokeColor" value="#<?php echo preg_replace('/^0x/', '', $albumPreviewStrokeColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumPreviewStrokeColor);?>" />
							</td>
						</tr>
						 <tr valign="top" class="alt">
								<th scope="row"><label for="albumPreviewStrokeWeight">Stroke Weight</label></th>
								<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
									<input name="albumPreviewStrokeWeight" type="text" id="albumPreviewStrokeWeight" value="<?php echo $albumPreviewStrokeWeight;?>" size="4" maxlength="4" />&nbsp;px
								</td>
							</tr> 
						</table>
					

					
					<div class="edit-sub-head">
					   <div class="cat-one">
							Text <span>(The album title and description. Not displayed with "Fill" preview graphics.)</span>
						</div>
					</div>
					
					<script type="text/javascript" charset="utf-8">
						jQuery(document).ready(function() {
							jQuery('#albumPreviewStyle').change(function() {
								if (jQuery('#albumPreviewStyle').val() == 'Fill') {
									if (init) {
										jQuery('#albumTextWrap').hide('slide');
									} else {
										jQuery('#albumTextWrap').hide();
									}
								} else {
									if (init) {
										jQuery('#albumTextWrap').show('slide');
									} else {
										jQuery('#albumTextWrap').show();
									}
								}
							}).change();
						});
					</script>
					<!-- parent div for all album text settings -->
					<div id="albumTextWrap">	  
					<table class="form-table ssp_form-table">
						 <tr class="alt">			
							<th scope="row"><label for="albumTextAlignment">Alignment</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<select name="albumTextAlignment" id="albumTextAlignment"><?php foreach ($albumTextAlignment_options as $option) : ?><option<?php if (!strcmp($albumTextAlignment, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="albumTitleColor">Title Color</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumTitleColor" type="text" id="albumTitleColor" value="#<?php echo preg_replace('/^0x/', '', $albumTitleColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumTitleColor);?>" /
							</td>
						</tr>
						<tr valign="top" class="alt">
							<th scope="row"><label for="albumTitleSize">Title Font Size</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumTitleSize" type="text" id="albumTitleSize" value="<?php echo $albumTitleSize;?>" size="4" maxlength="4" />&nbsp;px
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="albumDescColor">Description Color</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumDescColor" type="text" id="albumDescColor" value="#<?php echo preg_replace('/^0x/', '', $albumDescColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $albumDescColor);?>" />
							</td>
						</tr>
						<tr valign="top" class="alt">
							<th scope="row"><label for="albumDescSize">Description Font Size</label></th>
							<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
								<input name="albumDescSize" type="text" id="albumDescSize" value="<?php echo $albumDescSize;?>" size="4" maxlength="4" />&nbsp;px
							</td>
						</tr> 
					</table>
					</div>

					
					
					 <table class="form-table ssp_form-table">
				<tr class="button-row">
					<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
				</tr>
				</table>
			</div>
		 </div>
		<!--/ Album: -->


		<!-- Caption: -->
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Caption</h4> <span>Titles and descriptions displayed with slideshow content</span>
			</div>	  

			<div class="accord-interior"> 
						  
		 <div class="edit-sub-head">  
			<div class="cat-one">
			   <input name="" type="checkbox" id="captionAppearanceCheck" value="On"<?php echo (strcmp($captionAppearance, 'Hidden') ? ' checked="checked"' : ''); ?> /> <label for="captionAppearanceCheck">Show Caption</label>
			</div>	
		 </div>
		
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#captionAppearanceCheck').bind('change click', function() {
						if (jQuery('#captionAppearanceCheck:checked').val() == 'On') {
							if (init) {
								jQuery('#captionAppearanceWrap').show('slide');
							} else {
								jQuery('#captionAppearanceWrap').show();
							}
							if (jQuery('#captionAppearance').val() == 'Hidden') {
								jQuery('#captionAppearance').val('Overlay on Rollover (if Available)').change();
							}
						} else {
							if (init) {
								jQuery('#captionAppearanceWrap').hide('slide');
							} else {
								jQuery('#captionAppearanceWrap').hide();
							}
							jQuery('#captionAppearance').val('Hidden');
						}
					}).change();
					
					jQuery('#captionAppearance').change(function() {
						if (jQuery('#captionAppearance').val() == 'Hidden' && jQuery('#captionAppearanceCheck:checked').val() == 'On') {
							jQuery('#captionAppearanceCheck').attr('checked', false).change();
						}
					}).change();
				});
			</script>
		
		<!--
		  
			When Show Caption is checked...
			1) The div below appears.
			2) The "Hidden" value of "Appearance" is not shown
	
			When Show Caption is unchecked...
			1) The div below disappears.
			2) The value of "Appearance" below is set as "Hidden"
		
		-->
		
		<div id="captionAppearanceWrap">		
		<table class="form-table ssp_form-table">
		<tr class="alt">			
			<th scope="row"><label for="captionAppearance">Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<select name="captionAppearance" id="captionAppearance" next="11"><?php foreach ($captionAppearance_options as $option) : ?><option<?php if (!strcmp($captionAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="captionPosition">Position</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="captionPosition" id="captionPosition"><?php foreach ($captionPosition_options as $option) : ?><option<?php if (!strcmp($captionPosition, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr> 
		<tr class="alt">			
			<th scope="row"><label for="captionElements">Elements</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="captionElements" id="captionElements"><?php foreach ($captionElements_options as $option) : ?><option<?php if (!strcmp($captionElements, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr> 
		<tr valign="top">
			<th scope="row"><label for="captionHeaderTextColor">Header Text Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionHeaderTextColor" type="text" id="captionHeaderTextColor" value="#<?php echo preg_replace('/^0x/', '', $captionHeaderTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $captionHeaderTextColor);?>" />
			</td>
		</tr>
		<tr class="alt">
			<th scope="row"><label for="captionHeaderText">Header Text</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionHeaderText" type="text" id="captionHeaderText" value="<?php echo $captionHeaderText;?>" size="20" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="captionTextColor">Text Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionTextColor" type="text" id="captionTextColor" value="#<?php echo preg_replace('/^0x/', '', $captionTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $captionTextColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="captionBackgroundColor">Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionBackgroundColor" type="text" id="captionBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $captionBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $captionBackgroundColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="captionBackgroundAlpha">Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionBackgroundAlpha" type="text" id="captionBackgroundAlpha" value="<?php echo $captionBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="captionHeaderBackgroundAlpha">Header Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionHeaderBackgroundAlpha" type="text" id="captionHeaderBackgroundAlpha" value="<?php echo $captionHeaderBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)	
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="captionHeaderPadding">Header Padding</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionHeaderPadding[]" type="text" id="captionHeaderPadding0" value="<?php echo $captionHeaderPadding[0];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionHeaderPadding[]" type="text" id="captionHeaderPadding1" value="<?php echo $captionHeaderPadding[1];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionHeaderPadding[]" type="text" id="captionHeaderPadding2" value="<?php echo $captionHeaderPadding[2];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionHeaderPadding[]" type="text" id="captionHeaderPadding3" value="<?php echo $captionHeaderPadding[3];?>" size="4" maxlength="4" />&nbsp;px&nbsp;(top, right, bottom, left)
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><label for="captionPadding">Padding</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionPadding[]" type="text" id="captionPadding0" value="<?php echo $captionPadding[0];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionPadding[]" type="text" id="captionPadding1" value="<?php echo $captionPadding[1];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionPadding[]" type="text" id="captionPadding2" value="<?php echo $captionPadding[2];?>" size="4" maxlength="4" />&nbsp;&nbsp;<input name="captionPadding[]" type="text" id="captionPadding3" value="<?php echo $captionPadding[3];?>" size="4" maxlength="4" />&nbsp;px&nbsp;(Distance between caption text and background; top, right, bottom, left)
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="captionTextAlignment">Text Alignment</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="captionTextAlignment" id="captionTextAlignment"><?php foreach ($captionTextAlignment_options as $option) : ?><option<?php if (!strcmp($captionTextAlignment, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="captionTextSize">Text Size</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionTextSize" type="text" id="captionTextSize" value="<?php echo $captionTextSize;?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="captionTextShadowAlpha">Text Shadow Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="captionTextShadowAlpha" type="text" id="captionTextShadowAlpha" value="<?php echo $captionTextShadowAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>
		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table> 
		</div>	
		</div>
		</div>
		<!--/ Caption: -->
 


	   

		
		
	
		


		<!-- Feedback: -->
		<div class="accord">  
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Feedback</h4> <span>Timer and preloader elements</span>
			</div>	  

			<div class="accord-interior">

				<div class="edit-sub-head">
					<div class="cat-one">		
					General
						</div>	 
				</div>
				
				<table class="form-table ssp_form-table">
					<tr class="alt">			
						<th scope="row"><label for="feedbackBackgroundColor">Background Color</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="feedbackBackgroundColor" type="text" id="feedbackBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $feedbackBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $feedbackBackgroundColor);?>" />&nbsp;&nbsp;Bottom color
						</td>
					</tr>  
					<tr valign="top">
						<th scope="row"><label for="feedbackHighlightColor">Highlight Color</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="feedbackHighlightColor" type="text" id="feedbackHighlightColor" value="#<?php echo preg_replace('/^0x/', '', $feedbackHighlightColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $feedbackHighlightColor);?>" />&nbsp;&nbsp;Top color
						</td>
					</tr> 
					<tr class="alt">			
						<th scope="row"><label for="feedbackBackgroundAlpha">Background Alpha</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="feedbackBackgroundAlpha" type="text" id="feedbackBackgroundAlpha" value="<?php echo $feedbackBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="feedbackHighlightAlpha">Highlight Alpha</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="feedbackHighlightAlpha" type="text" id="feedbackHighlightAlpha" value="<?php echo $feedbackHighlightAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
						</td>
					</tr>
				</table>
						
				<div class="edit-sub-head">
					<div class="cat-one">
					   <input name="" type="checkbox" id="showPreloaderCheck" value="On" checked="checked" /> <label for="showPreloaderCheck">Show Preloader</label>  
						<span>(Displayed when content is loading to communicate progress)</span>
					</div>
				</div> 
				
				<script type="text/javascript" charset="utf-8">
					jQuery(document).ready(function() {
						jQuery('#showPreloaderCheck').bind('change click', function() {
							if (jQuery('#showPreloaderCheck:checked').val() == 'On') {
								if (init) {
									jQuery('#showPreloaderWrap').show('slide');
								} else {
									jQuery('#showPreloaderWrap').show();
								}
								if (jQuery('#feedbackPreloaderAppearance').val() == 'Hidden') {
									jQuery('#feedbackPreloaderAppearance').val('Beam');
								}
							} else {
								if (init) {
									jQuery('#showPreloaderWrap').hide('slide');
								} else {
									jQuery('#showPreloaderWrap').hide();
								}
								jQuery('#feedbackPreloaderAppearance').val('Hidden');
							}
						}).change();

						jQuery('#feedbackPreloaderAppearance').change(function() {
							if (jQuery('#feedbackPreloaderAppearance').val() == 'Hidden' && jQuery('#showPreloaderCheck:checked').val() == 'On') {
								jQuery('#showPreloaderCheck').attr('checked', false).change();
							}
						}).change();
					});
				</script>
				
				<!-- 
				Checking the option above:
				1) Displays the div below
				2) Modifies "Appearance" so that "Hidden" is not an option
				
				Unchecking the option above:
				1) Hides the div below
				2) Sets "Appearance" to "Hidden" when written
				-->
				
				<div id="showPreloaderWrap">
				<table class="form-table ssp_form-table">
		<tr class="alt">			
			<th scope="row"><label for="feedbackPreloaderAppearance">Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="feedbackPreloaderAppearance" id="feedbackPreloaderAppearance"><?php foreach ($feedbackPreloaderAppearance_options as $option) : ?><option<?php if (!strcmp($feedbackPreloaderAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
			</td>
		</tr>
		<tr>			
			<th scope="row"><label for="feedbackPreloaderTextSize">Text Size</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="feedbackPreloaderTextSize" type="text" id="feedbackPreloaderTextSize" value="<?php echo $feedbackPreloaderTextSize;?>" size="4" maxlength="4" />&nbsp;Pixel size of Beam preloader text field
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="feedbackPreloaderScale">Scale</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="feedbackPreloaderScale" type="text" id="feedbackPreloaderScale" value="<?php echo $feedbackPreloaderScale;?>" size="4" maxlength="4" />&nbsp;Percentage (e.g., 2 = 200%)
			</td>
		</tr>
		<tr>			
			<th scope="row"><label for="feedbackPreloaderAlign">Alignment</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="feedbackPreloaderAlign" id="feedbackPreloaderAlign"><?php foreach ($feedbackPreloaderAlign_options as $option) : ?><option<?php if (!strcmp($feedbackPreloaderAlign, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
			</td>
		</tr> 
		<tr valign="top" class="alt">
			<th scope="row"><label for="feedbackPreloaderPosition">Position</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="feedbackPreloaderPosition" id="feedbackPreloaderPosition"><?php foreach ($feedbackPreloaderPosition_options as $option) : ?><option<?php if (!strcmp($feedbackPreloaderPosition, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
			</td>
		</tr>
		</table>
		</div>
		
		<div class="edit-sub-head">
			<div class="cat-one">
		   <input name="feedbackTimerAppearance" id="feedbackTimerAppearance" next="5" type="checkbox"<?php echo (!strcmp($feedbackTimerAppearance, 'On' ) || !strcmp($feedbackTimerAppearance, 'Visible' )) ? ' checked="checked"' : ''; ?> id="feedbackTimerAppearance" value="Visible" /> <label for="feedbackTimerAppearance">Show Timer</label>
		<span>(Displayed during a transition pause while in auto-playback)</span> 
		</div>
		</div>
		
		<script type="text/javascript" charset="utf-8">
			jQuery(document).ready(function() {
				jQuery('#feedbackTimerAppearance').bind('change click', function() {
					if (jQuery('#feedbackTimerAppearance:checked').val() == 'Visible') {
						if (init) {
							jQuery('#feedbackTimerWrap').show('slide');
						} else {
							jQuery('#feedbackTimerWrap').show();
						}
					} else {
						if (init) {
							jQuery('#feedbackTimerWrap').hide('slide');
						} else {
							jQuery('#feedbackTimerWrap').hide();
						}
					}
				}).change();
			});
		</script>
		
		<!-- 
		Checking the option above:
		1) Displays the div below
		
		Unchecking the option above:
		1) Hides the div below
		-->		
		
		<div id="feedbackTimerWrap"> 
		<table class="form-table ssp_form-table">
		 <tr class="alt">		
			<th scope="row"><label for="feedbackTimerScale">Scale</label></th>
		<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
			<input name="feedbackTimerScale" type="text" id="feedbackTimerScale" value="<?php echo $feedbackTimerScale;?>" size="4" maxlength="4" />&nbsp;Percentage (e.g., 2 = 200%)
		</td>
	</tr>  
	<tr valign="top">
		<th scope="row"><label for="feedbackTimerAlign">Alignment</label></th>
		<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
			<select name="feedbackTimerAlign" id="feedbackTimerAlign"><?php foreach ($feedbackTimerAlign_options as $option) : ?><option<?php if (!strcmp($feedbackTimerAlign, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
		</td>
	</tr>
	<tr class="alt">		
		<th scope="row"><label for="feedbackTimerPosition">Position</label></th>
		<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
			<select name="feedbackTimerPosition" id="feedbackTimerPosition"><?php foreach ($feedbackTimerPosition_options as $option) : ?><option<?php if (!strcmp($feedbackTimerPosition, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;
		</td>
	</tr> 
	</table>
	</div>
	
		 <div class="edit-sub-head">
			   <div class="cat-one">
				Video Play Button <span>(Button that appears when videos end or when Video / Auto Start is unchecked.)
				</div>
			</div>
			
			<table class="form-table ssp_form-table">
	<tr valign="top" class="alt">
		<th scope="row"><label for="feedbackVideoButtonScale">Scale</label></th>
		<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
			<input name="feedbackVideoButtonScale" type="text" id="feedbackVideoButtonScale" value="<?php echo $feedbackVideoButtonScale;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage (e.g., 2 = 200%)
		</td>
	</tr>

		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>	
		</div>	 
		</div>
		<!--/ Feedback: -->




		


		<!-- Media Player: -->
		<div class="accord"> 
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Media Player</h4> <span>Interface for controlling audio and video</span>
			</div>	  

			<div class="accord-interior"> 
				
				<div class="edit-sub-head"> 
					<div class="cat-one">
					   <input name="" type="checkbox" id="showMediaCheck" value="Visible" <?php echo strcmp($mediaPlayerAppearance, 'Hidden' ) ? ' checked="checked"' : ''; ?> /> <label for="showMediaCheck">Show Media Player</label> <span>(If checked, displays when video or audio is loaded)</span>				   
					</div>
					</div> 
					
					<script type="text/javascript" charset="utf-8">
						jQuery(document).ready(function() {
							jQuery('#showMediaCheck').bind('change click', function() {
								if (jQuery('#showMediaCheck:checked').val() == 'Visible') {
									if (init) {
										jQuery('#showMediaWrap').show('slide');
									} else {
										jQuery('#showMediaWrap').show();
									}
									if (jQuery('#mediaPlayerAppearance').val() == 'Hidden') {
										jQuery('#mediaPlayerAppearance').val('Visible on Rollover').change();
									}
								} else {
									if (init) {
										jQuery('#showMediaWrap').hide('slide');
									} else {
										jQuery('#showMediaWrap').hide();
									}
									jQuery('#mediaPlayerAppearance').val('Hidden');
								}
							}).change();

							jQuery('#mediaPlayerAppearance').change(function() {
								if (jQuery('#mediaPlayerAppearance').val() == 'Hidden' && jQuery('#showMediaCheck:checked').val() == 'Visible') {
									jQuery('#showMediaCheck').attr('checked', false).change();
								}
							}).change();
						});
					</script>
					
					<!-- 
					Checking the option above:
					1) Displays the div below

					Unchecking the option above:
					1) Hides the div below
					-->
			 
		 <div id="showMediaWrap">				
		<table class="form-table ssp_form-table">

		<tr class="alt">		
			<th scope="row"><label for="mediaPlayerAppearance">Appearance</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="mediaPlayerAppearance" id="mediaPlayerAppearance"><?php foreach ($mediaPlayerAppearance_options as $option) : ?><option<?php if (!strcmp($mediaPlayerAppearance, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerPosition">Position</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="mediaPlayerPosition" id="mediaPlayerPosition"><?php foreach ($mediaPlayerPosition_options as $option) : ?><option<?php if (!strcmp($mediaPlayerPosition, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerBackgroundColor">Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerBackgroundColor" type="text" id="mediaPlayerBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerBackgroundColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerBufferColor">Buffer Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerBufferColor" type="text" id="mediaPlayerBufferColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerBufferColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerBufferColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerButtonColor">Button Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerButtonColor" type="text" id="mediaPlayerButtonColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerButtonColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerButtonColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerElapsedBackgroundColor">Elapsed Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerElapsedBackgroundColor" type="text" id="mediaPlayerElapsedBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerElapsedBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerElapsedBackgroundColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerElapsedTextColor">Elapsed Text Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerElapsedTextColor" type="text" id="mediaPlayerElapsedTextColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerElapsedTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerElapsedTextColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerVolumeBackgroundColor">Volume Background Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerVolumeBackgroundColor" type="text" id="mediaPlayerVolumeBackgroundColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerVolumeBackgroundColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerVolumeBackgroundColor);?>" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerVolumeHighlightColor">Volume Highlight Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerVolumeHighlightColor" type="text" id="mediaPlayerVolumeHighlightColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerVolumeHighlightColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerVolumeHighlightColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerProgressColor">Progress Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerProgressColor" type="text" id="mediaPlayerProgressColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerProgressColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerProgressColor);?>" />
			</td>
		</tr> 
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerTextColor">Text Color</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerTextColor" type="text" id="mediaPlayerTextColor" value="#<?php echo preg_replace('/^0x/', '', $mediaPlayerTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $mediaPlayerTextColor);?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerBackgroundAlpha">Background Alpha</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerBackgroundAlpha" type="text" id="mediaPlayerBackgroundAlpha" value="<?php echo $mediaPlayerBackgroundAlpha;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage: 0 (transparent) - 1 (opaque)  
			</td>
		</tr>  
		<tr class="alt">			
			<th scope="row"><label for="mediaPlayerScale">Scale</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerScale" type="text" id="mediaPlayerScale" value="<?php echo $mediaPlayerScale;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage (e.g., 2 = 200%)
			</td>
		</tr>  
		<tr valign="top">
			<th scope="row"><label for="mediaPlayerTextSize">Text Size</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="mediaPlayerTextSize" type="text" id="mediaPlayerTextSize" value="<?php echo $mediaPlayerTextSize;?>" size="4" maxlength="4" />&nbsp;px
			</td>
		</tr>
		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>
		</div>
		</div> 
		</div>
		<!--/ Media Player: -->
				




		


		<!-- Tool Tips: -->	 
		<div class="accord">  
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Tool Tips</h4> <span>Rollover messaging in the navigation and over slideshow content</span>
			</div>	  

			<div class="accord-interior">
				
				<div class="edit-sub-head">
						   <div class="cat-one">
								 General
						</div> 
					</div>
					
					<table class="form-table ssp_form-table">
					<tr class="alt">			
						<th scope="row"><label for="toolColor">Color</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="toolColor" type="text" id="toolColor" value="#<?php echo preg_replace('/^0x/', '', $toolColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $toolColor);?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="toolTextColor">Text Color</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="toolTextColor" type="text" id="toolTextColor" value="#<?php echo preg_replace('/^0x/', '', $toolTextColor);?>" size="8" maxlength="8" />&nbsp;&nbsp;<input readonly="true" class="ssp_colorpicker" style="background:#<?php echo preg_replace('/^0x/', '', $toolTextColor);?>" />
						</td>
					</tr>
					<tr class="alt">			
						<th scope="row"><label for="toolTextSize">Text Size</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="toolTextSize" type="text" id="toolTextSize" value="<?php echo $toolTextSize;?>" size="3" maxlength="3" />
						</td>
					</tr>
					</table>
					
					
				
				
				<div class="edit-sub-head">
						   <div class="cat-one">
								 <input name="" type="checkbox" id="toolAppearanceCheck" value="Visible"<?php echo strcmp($toolAppearanceContentArea, 'Hidden' ) ? ' checked="checked"' : ''; ?> /> <label for="toolAppearanceCheck">Show on Content Mouse Over</label>
						</div> 
					</div> 
					
					<script type="text/javascript" charset="utf-8">
						jQuery(document).ready(function() {
							jQuery('#toolAppearanceCheck').bind('change click', function() {
								if (jQuery('#toolAppearanceCheck:checked').val() == 'Visible') {
									if (init) {
										jQuery('#toolAppearanceWrap').show('slide');
									} else {
										jQuery('#toolAppearanceWrap').show();
									}
									if (jQuery('#toolAppearanceContentArea').val() == 'Hidden') {
										jQuery('#toolAppearanceContentArea').val('Visible');
									}
								} else {
									if (init) {
										jQuery('#toolAppearanceWrap').hide('slide');
									} else {
										jQuery('#toolAppearanceWrap').hide();
									}
									jQuery('#toolAppearanceContentArea').val('Hidden');
								}
							}).change();

							jQuery('#toolAppearanceContentArea').change(function() {
								if (jQuery('#toolAppearanceContentArea').val() == 'Hidden' && jQuery('#toolAppearanceCheck:checked').val() == 'Visible') {
									jQuery('#toolAppearanceCheck').attr('checked', false).change();
								}
							}).change();
						});
					</script>
					
					 <!-- 
						Checking the option above:
						1) Displays the div below
						2) Does not display "Hidden" as option for toolAppearanceContentArea

						Unchecking the option above:
						1) Hides the div below
						2) Sets toolAppearanceContentArea to "Hidden"
						-->
					
					<div id="toolAppearanceWrap">
					<table class="form-table ssp_form-table"> 
						 <tr class="alt">			
							<th scope="row"><label for="toolAppearanceContentArea">Appearance</label></th>
								<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
									<select name="toolAppearanceContentArea" id="toolAppearanceContentArea"><?php foreach ($toolAppearanceContentArea_options as $option) : ?><option<?php if (!strcmp($toolAppearanceContentArea, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>
								</td>
							</tr>
					<tr valign="top">
						<th scope="row"><label for="toolDelayContentArea">Delay</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="toolDelayContentArea" type="text" id="toolDelayContentArea" value="<?php echo $toolDelayContentArea;?>" size="3" maxlength="3" />&nbsp;&nbsp;seconds
						</td>
					</tr>  
					<tr valign="top" class="alt">
						<th scope="row"><label for="toolTimeoutContentArea">Timeout</label></th>
						<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
							<input name="toolTimeoutContentArea" type="text" id="toolTimeoutContentArea" value="<?php echo $toolTimeoutContentArea;?>" size="3" maxlength="3" />&nbsp;&nbsp;seconds
						</td>
					</tr>  
					</table>
					</div>
					
					
				
					
			   <div class="edit-sub-head">
					 <div class="cat-one"> 
					   <input name="toolAppearanceNav" type="checkbox"<?php echo (!strcmp($toolAppearanceNav, 'On' ) || !strcmp($toolAppearanceNav, 'Visible' )) ? ' checked="checked"' : ''; ?> id="toolAppearanceNav" value="Visible" />
						<label for="toolAppearanceNav">Show on Navigation Mouse Over</label>
					</div>
			   </div>
				
				
				<script type="text/javascript" charset="utf-8">
					jQuery(document).ready(function() {
						jQuery('#toolAppearanceNav').bind('change click', function() {
							if (jQuery('#toolAppearanceNav:checked').val() == 'Visible') {
								if (init) {
									jQuery('#toolAppearanceNavWrap').show('slide');
								} else {
									jQuery('#toolAppearanceNavWrap').show();
								}
							} else {
								if (init) {
									jQuery('#toolAppearanceNavWrap').hide('slide');
								} else {
									jQuery('#toolAppearanceNavWrap').hide();
								}
							}
						}).change();
					});
				</script>
				 <!-- 
					Checking the option above:
					1) Displays the div below

					Unchecking the option above:
					1) Hides the div below
					-->
					
						
			   <div id="toolAppearanceNavWrap">
				<table class="form-table ssp_form-table">
				<tr class="alt">			
					<th scope="row"><label for="toolDelayNav">Delay Navigation</label></th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
						<input name="toolDelayNav" type="text" id="toolDelayNav" value="<?php echo $toolDelayNav;?>" size="3" maxlength="3" />&nbsp;&nbsp;seconds
					</td>
				</tr>
				</table>  
				</div>
				
				<div class="edit-sub-head">
						 <div class="cat-one"> 
						   Text <span>(The text that appears inside tool-tips)</span>
						</div>
				   </div>
				
				<table class="form-table ssp_form-table">
		<tr valign="top" class="alt">			
			<th scope="row" valign="top"><label for="toolLabels">Labels</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="toolLabels[]" type="text" id="toolLabels0" value="<?php echo $toolLabels[0];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels1" value="<?php echo $toolLabels[1];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels2" value="<?php echo $toolLabels[2];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels3" value="<?php echo $toolLabels[3];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels4" value="<?php echo $toolLabels[4];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels5" value="<?php echo $toolLabels[5];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels6" value="<?php echo $toolLabels[6];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels7" value="<?php echo $toolLabels[7];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels8" value="<?php echo $toolLabels[8];?>" size="20" /><br /><br />
				<input name="toolLabels[]" type="text" id="toolLabels9" value="<?php echo $toolLabels[9];?>" size="20" /><br /><br />
			</td>
		</tr>
		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>
		</div> 
		</div>
		<!--/ Tooltips: -->


		<!-- Audio & Video: -->	 
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Audio &amp; Video</h4> <span>Settings for when loading background audio and/or video content</span>
			</div>	  

			<div class="accord-interior">
				
				 <div class="edit-sub-head">
						 <div class="cat-one"> 
						   Audio
						</div>
				   </div>
						
		<table class="form-table ssp_form-table">
		<tr class="alt">			
			<th scope="row"><label for="audioAutoStart">Auto Start</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="audioAutoStart" type="checkbox"<?php echo (!strcmp($audioAutoStart, 'On' ) || !strcmp($audioAutoStart, 'Visible' )) ? ' checked="checked"' : ''; ?> id="audioAutoStart" value="On" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="audioLoop">Loop</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="audioLoop" type="checkbox"<?php echo (!strcmp($audioLoop, 'On' ) || !strcmp($audioLoop, 'Visible' )) ? ' checked="checked"' : ''; ?> id="audioLoop" value="On" />
			</td>
		</tr>
		<tr class="alt">			
			<th scope="row"><label for="audioPause">Pause</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="audioPause" type="checkbox"<?php echo (!strcmp($audioPause, 'On' ) || !strcmp($audioPause, 'Visible' )) ? ' checked="checked"' : ''; ?> id="audioPause" value="On" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="audioVolume">Volume</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="audioVolume" type="text" id="audioVolume" value="<?php echo $audioVolume;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage (e.g., 0 = mute, 1 = full volume)	 
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="soundEffectsVolume">Sound Effects Volume</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="soundEffectsVolume" type="text" id="soundEffectsVolume" value="<?php echo $soundEffectsVolume;?>" size="4" maxlength="4" />&nbsp;&nbsp;Percentage (e.g., 0 = mute, 1 = full volume)	 
			</td>
		</tr>
		</table>
		
		<div class="edit-sub-head">
				 <div class="cat-one"> 
				   Video
				</div>
		   </div>
		<table class="form-table ssp_form-table">
		<tr class="alt">			
			<th scope="row"><label for="videoAutoStart">Auto Start</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="videoAutoStart" type="checkbox"<?php echo (!strcmp($videoAutoStart, 'On' ) || !strcmp($videoAutoStart, 'Visible' )) ? ' checked="checked"' : ''; ?> id="videoAutoStart" value="On" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="videoBufferTime">Buffer Time</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="videoBufferTime" type="text" id="videoBufferTime" value="<?php echo $videoBufferTime;?>" size="4" maxlength="4" />&nbsp;&nbsp;seconds
			</td>
		</tr> 
		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>
		</div>	 
		</div>
		<!--/ Audio & Video: -->
				   


		<!-- Options: --> 
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
				<h4>Options</h4> <span>Miscellaneous slideshow settings</span>
			</div>	  

			<div class="accord-interior">
						
		<table class="form-table ssp_form-table">
		 
		<tr>			<th scope="row"><label for="cacheContent">Cache Content</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<select name="cacheContent" id="cacheContent"><?php foreach ($cacheContent_options as $option) : ?><option<?php if (!strcmp($cacheContent, $option)) echo ' selected="selected"';?> value="<?php echo $option;?>"><?php echo $option;?></option><?php endforeach;?></select>&nbsp;&nbsp;

			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="fullScreenReformat">Full Screen Reformat</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="fullScreenReformat" type="checkbox"<?php echo (!strcmp($fullScreenReformat, 'On' ) || !strcmp($fullScreenReformat, 'Visible' )) ? ' checked="checked"' : ''; ?> id="fullScreenReformat" value="On" />
			</td>
		</tr>
		<tr>			<th scope="row"><label for="fullScreenTakeOver">Full Screen Take Over</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="fullScreenTakeOver" type="checkbox"<?php echo (!strcmp($fullScreenTakeOver, 'On' ) || !strcmp($fullScreenTakeOver, 'Visible' )) ? ' checked="checked"' : ''; ?> id="fullScreenTakeOver" value="On" />
			</td>
		</tr> 
		<tr valign="top" class="alt">
			<th scope="row"><label for="keyboardControl">Keyboard Control</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="keyboardControl" type="checkbox"<?php echo (!strcmp($keyboardControl, 'On' ) || !strcmp($keyboardControl, 'Visible' )) ? ' checked="checked"' : ''; ?> id="keyboardControl" value="On" />
&nbsp;&nbsp;Slideshow control using keyboard enabled if selected
			</td>
		</tr>  
		<tr>			<th scope="row"><label for="permalinks">Permalinks</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="permalinks" type="checkbox"<?php echo (!strcmp($permalinks, 'On' ) || !strcmp($permalinks, 'Visible' )) ? ' checked="checked"' : ''; ?> id="permalinks" value="On" />&nbsp;&nbsp;When selected the document path in the browser will automatically update with a direct link to the currently viewed content.
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="smoothing">Smoothing</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="smoothing" type="checkbox"<?php echo (!strcmp($smoothing, 'On' ) || !strcmp($smoothing, 'Visible' )) ? ' checked="checked"' : ''; ?> id="smoothing" value="On" />
			</td>
		</tr>
		<tr>			<th scope="row"><label for="textStrings">Text Strings</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="textStrings[]" type="text" id="textStrings0" value="<?php echo $textStrings[0];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used for the left button in the gallery screen)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings1" value="<?php echo $textStrings[1];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used for the right button in the gallery screen)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings2" value="<?php echo $textStrings[2];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used as a screen-count field in the gallery screen)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings3" value="<?php echo $textStrings[3];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used in both the caption title and the screen-count field in the gallery screen)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings4" value="<?php echo $textStrings[4];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used in both the caption title and the screen-count field in the gallery screen)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings5" value="<?php echo $textStrings[5];?>" size="20" maxlength="20" />&nbsp;&nbsp;(Used when a caption doesn't exist for an image)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings6" value="<?php echo $textStrings[6];?>" size="20" maxlength="20" /><br /><br />
				<input name="textStrings[]" type="text" id="textStrings7" value="<?php echo $textStrings[7];?>" size="20" maxlength="20" />&nbsp;&nbsp;(For use with audio captions in the media player)<br /><br />
				<input name="textStrings[]" type="text" id="textStrings8" value="<?php echo $textStrings[8];?>" size="20" maxlength="20" />&nbsp;&nbsp;(For use when Audio / Auto Start is unchecked and an audio file is loaded)<br /><br />
				English text values used for SlideShowPro's user interface. they may be exchanged with English and non-English words as needed.
			</td>
		</tr>
		<tr valign="top" class="alt">
			<th scope="row"><label for="typeface">Typeface</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="typeface" type="text" id="typeface" value="<?php echo $typeface;?>" size="50" maxlength="100" />&nbsp;&nbsp;<br />Typeface names to be used as the general font for SlideShowPro.<br /><strong>Note: do not include spaces between commas and a font name.</strong><br />SlideShowPro will use the first available typeface in the comma-separated list.
			</td>
		</tr>
		<tr>			<th scope="row"><label for="typefaceHead">Typeface Head</label></th>
			<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
				<input name="typefaceHead" type="text" id="typefaceHead" value="<?php echo $typefaceHead;?>" size="50" maxlength="100" />&nbsp;&nbsp;<br />Typeface names to be used as the "header" font for SlideShowPro.<br />This applies to gallery item titles and photo caption titles.<br /><strong>Note: do not include spaces between commas and a font name.</strong><br />SlideShowPro will use the first available typeface in the comma-separated list.
			</td>
		</tr> 
		<tr class="button-row">
			<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
		</tr>
		</table>
		</div> 
		</div>
		<!--/ Miscellaneous: -->
		
		
		<!-- Director Settings: -->	 
		<div class="accord">
		<div class="accord-head">
			<div class="accord-toggle"></div>
			<h4>Director Publishing</h4> <span>Settings for when loading content from SlideShowPro Director</span>
		</div>
		
		<div class="accord-interior">
		
			<table class="form-table ssp_form-table">
			<tr>			   
				<th scope="row"><label for="directorLargePublishing">Large Publishing</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
				<td valign="bottom">
					<input name="directorLargePublishing" type="checkbox"<?php echo (!strcmp($directorLargePublishing, 'On' ) || !strcmp($directorLargePublishing, 'Visible' )) ? ' checked="checked"' : ''; ?> id="directorLargePublishing" value="On" />
				</td>
			</tr>
			</table>
			 
			<script type="text/javascript" charset="utf-8">
				jQuery(document).ready(function() {
					jQuery('#directorLargePublishing').bind('change click', function() {
						if (jQuery('#directorLargePublishing:checked').val() == 'On') {
							if (init) {
								jQuery('#directorPubWrap').show('slide');
							} else {
								jQuery('#directorPubWrap').show();
							}
						} else {
							if (init) {
								jQuery('#directorPubWrap').hide('slide');
							} else {
								jQuery('#directorPubWrap').hide();
							}
						}
					}).change();
				});
			</script>
			
			<div id="directorPubWrap">
				<table class="form-table ssp_form-table">
				<tr>
					<th scope="row">&nbsp;</th>
					<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
					<td>
						 <table class="form-table ssp-settings-nested"> 
							<tr>
								<th scope="row"><label for="directorLargeQuality">Large Quality</label></th>
								<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td>
								<td valign="middle">
									<input name="directorLargeQuality" type="text" id="directorLargeQuality" value="<?php echo $directorLargeQuality;?>" size="3" maxlength="3" />%
								</td>	 
							</tr>	   
							<tr>				
								<th scope="row"><label for="directorLargeSharpening">Large Sharpening</label></th>
								<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
									<input name="directorLargeSharpening" type="text" id="directorLargeSharpening" value="<?php echo $directorLargeSharpening;?>" size="3" maxlength="3" />
								</td>
							</tr>
						</table>
					</td>
				</tr>  
				</table>	
			</div>
			
			<table class="form-table ssp_form-table">
			<tr valign="top" class="alt">
				<th scope="row"><label for="directorThumbQuality">Thumb Quality</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="directorThumbQuality" type="text" id="directorThumbQuality" value="<?php echo $directorThumbQuality;?>" size="3" maxlength="3" />%
				</td>
			</tr>

			<tr>				
				<th scope="row"><label for="directorThumbSharpening">Thumb Sharpening</label></th>
				<td width="2"><span class="ssp_err_exclm" style="visibility:hidden"> </span></td><td>
					<input name="directorThumbSharpening" type="text" id="directorThumbSharpening" value="<?php echo $directorThumbSharpening;?>" size="3" maxlength="3" />
				</td>
			</tr>


			<tr class="button-row">
				<td colspan="3" class="ssp_form-update"><input class="button" type="submit" value="<?php echo $action;?>" /></td>
			</tr>
			</table>			 
		</div>	  
		</div>
		<!--/ Director: -->
		
		<!--/ wrap -->
</div>						 <!--/ edit-form-->

	</form>


  <form method="post" style="visibility:hidden" id="ssp_f_style">
	<input type="hidden" name="sspName" id="sspName_style" />
	<input type="hidden" name="xmlFilePath" id="xmlFilePath_style"	/>
	<input type="hidden" name="xmlFileType" id="xmlFileType_style" />
	<input type="hidden" name="sspWidth" id="sspWidth_style" />
	<input type="hidden" name="sspHeight" id="sspHeight_style" />
	<input type="hidden" name="action" value="change_style" />
	<input type="hidden" name="active_style" id="ssp_f_active_style" />
	<input type="hidden" name="active_style_type" id="ssp_f_active_style_type" />
	<input type="submit" />
  </form>

<div class="slidepress-saved-styles">
	<h3>Use Saved Gallery Style</h3>
	<p>Click "Use" next to the gallery you wish to copy style settings from. If edits are made to the linked gallery's style settings, this gallery will automatically receive those updates.</p>
	<div class="modal-scroller">
		<table class="widefat" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th scope="col">Gallery ID</th>
					<th scope="col">Gallery Name</th>
					<th scope="col" width="30%" class="actions">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php if (!empty($styles_db)) : ?>
					<?php foreach ($styles_db as $id => $name ) : ?>
						<?php $row_class = 'alternate' == $row_class ? '' : 'alternate'; ?>
						<?php if ( $id != $active_style ): ?>
							<tr class="author-self status-publish <?php echo $row_class; ?>" valign="top">
								<td class="id"><?php echo $id; ?></td>
								<td class="name"><?php echo $name; ?></td>
								<td class="actions">
									<a href="#" class="use">Use</a>
								</td>
							</tr>
						<?php endif ?>
					<?php endforeach;?>
				<?php endif; ?>
			</tbody>
		</table>   
	</div>
</div>

<script>

	jQuery(document).ready(function() {
var ssp_form = jQuery('#ssp_form')
		jQuery('#xmlExport').click(function(event) {
			var xmlName = jQuery('#xmlExportName1').val();
			if (xmlName == '' || (/[\?@#!\$%\^&\*\(\)'"\\\/\{\}\[\];,\|~`+=]/.test(xmlName)) ) {
				jQuery('#xmlExportName1').css('borderColor', '#FF2700');
				return;
			} else {
				jQuery('#xmlExportName1').css('borderColor', '#C6D9E9');
				jQuery('#xmlExportName2').val(xmlName);
				jQuery('#action').val('export_xml');
				ssp_form.get(0).setAttribute('action', '<?php echo $this->url; ?>tools/export_xml.php');
				ssp_form.addClass('exported');
				ssp_form.submit();
			}
		});

		jQuery('.submit input[type=submit]').click(function(event) {
			if (ssp_form.hasClass('exported')) {
				jQuery('#action').val('Update Gallery');
				ssp_form.get(0).setAttribute('action',"");
			}
		});

var styleSelect = jQuery('#sspChangeStyle');
styleSelect.data('orig', styleSelect.val());
<?php if ( ! is_null( $sspGalleryId ) && $sspGalleryId != $active_style ): ?>
ssp_form.find(':text, :checkbox, select, :radio').each(function(){
  jQuery(this).data('orig', jQuery(this).val());
}).change(function(){
  var $this = jQuery(this);
  if (jQuery.inArray($this.attr('id'), ['sspName', 'xmlFilePath', 'xmlFileType', 'createThumbnails', 'sspWidth', 'sspHeight', 'sspChangeStyle', 'xmlExportName1' ]) === -1) {
	if ($this.hasClass('changed')) {
	  if ($this.val() == $this.data('orig')) {
		jQuery('#sspChangeStyle option[value=-1]').remove();
		$this.removeClass('changed');
		if (jQuery('#ssp_form .changed').size() == 0) {
		  jQuery('#sspChangeStyle option[value=' + styleSelect.data('orig') + ']').attr('selected', 'selected');
		  styleSelect.removeClass('custom');
		}
	  }
	} else {
	  $this.addClass('changed');
	  jQuery('#sspChangeStyle option[selected]').removeAttr('selected');
	  styleSelect.prepend('<option value="-1" selected="selected">Custom</option>');
	  styleSelect.addClass('custom');
	}
  }
});
<?php endif; ?>
	});

	jQuery(document).ready(function() {
		jQuery('#visibility-toggle').toggle(
			function(){
				jQuery('.edit-form').show();
				jQuery(this).val('Hide style settings');
				jQuery('.ssp-save-gallery-first-button').hide();
			},
			function(){
				jQuery('.edit-form').hide();
				jQuery(this).val('Edit style settings');
				jQuery('.ssp-save-gallery-first-button').show();
			}
		);
	  var req_fields = [ 'sspName', 'xmlFilePath', 'xmlFileType', 'sspWidth', 'sspHeight' ];
	  jQuery('#sspChangeStyle').change(function(event) {
		jQuery.each(req_fields, function() {
		  jQuery('#'+this+'_style').val(jQuery('#'+this).val());
		});
		if (jQuery('#ssp_hidden_gallery_id').size() > 0) {
		  jQuery('#ssp_hidden_gallery_id').clone().appendTo('#ssp_f_style');
		}

		jQuery('#ssp_f_active_style_type').val(jQuery(this).get(0).options[jQuery(this).get(0).selectedIndex].getAttribute('style_type'));
		jQuery('#ssp_f_active_style').val(jQuery(this).val());

		if (jQuery(this).val() == 'use_saved') {
			var modal_content = jQuery('.slidepress-saved-styles').clone();
			modal_content.find('table').attr('id', 'slidepress-saved-styles-table');
			jQuery.facebox(modal_content.html());
			jQuery('#slidepress-saved-styles-table .actions a').click(function(e){
				e.preventDefault();
				var $this = jQuery(this);
				var id = $this.parent().siblings('.id').text();
				var name = $this.parent().siblings('.name').text();
				if ($this.hasClass('use')) {
					jQuery('#ssp_f_active_style_type').val('db');
					jQuery('#ssp_f_active_style').val(id);
					jQuery(document).trigger('close.facebox');
					jQuery('#ssp_f_style').submit();
				}
			});
		} else if ((jQuery('#sspChangeStyle').hasClass('custom') || jQuery('#sspChangeStyle').data('orig') == -1)) {
		  jQuery.facebox('<div id="ssp_modal"><div id="ssp_modal_content">Changing style preset will replace any parameter modifications already assigned to the gallery.<br />(Clicking "OK" will assign the newly chosen style preset, while "Cancel" will retain the current settings)<\/div><div id="ssp_modal_buttons"><input type="button" value="OK" /><input type="button" value="Cancel" /><\/div><\/div>');
		  jQuery('#facebox .footer').remove();
		  jQuery('#facebox .body').width(700);
		  jQuery('#facebox input[value=OK]').click(function(){
			jQuery('#ssp_f_style').submit();
		  })
		  jQuery('#facebox input[value=Cancel]').click(function(){
			jQuery('#sspChangeStyle option[value=-1]').remove();
			jQuery('#sspChangeStyle option[selected]').removeAttr('selected');
			jQuery('#sspChangeStyle').prepend('<option value="-1" selected="selected">Custom</option>');
			jQuery(document).trigger('close.facebox');
		  })
		} else {
		  jQuery('#ssp_f_style').submit();
		}
	  });
	
		jQuery('#exportStyleButton').toggle(
			function(){
				jQuery('#exportStyleFields').show();
			},
			function(){
				jQuery('#exportStyleFields').hide();
			}
		);
	});
  </script>

<script type="text/javascript">
	var init = false;
	jQuery(document).ready(function(){
		init = true;
	});
</script>

<?php include('ssp_validate_js.php'); ?>