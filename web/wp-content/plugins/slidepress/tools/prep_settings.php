<?php
global $ssp_params;

# read params from $active_style xml file, unless current action is update
if (strcmp($action, 'Update Gallery')) {
	$active_style = $this->db->escape($active_style);
	
	if ( !strcmp($active_style_type, 'db') ) { # get style from db
			$style = $this->db->get_row( "SELECT * FROM ".$this->table_name." WHERE sspGalleryId = '".$active_style."'", ARRAY_A );
		    $style['id'] = $id;
		    extract( $style, EXTR_OVERWRITE );
	} else { # get style from xml
		if ( function_exists( 'simplexml_load_file' ) ) {
			if ( file_exists($this->upload_path . 'templates/' . $active_style . '.xml' ) )
				$sxe = simplexml_load_file( $this->upload_path . 'templates/' . $active_style . '.xml');
			elseif ( file_exists( $this->upload_path . 'templates/' . $active_style . '.xml' ) )
				$sxe = simplexml_load_file( $this->upload_path . 'templates/default.xml');
			else
				$sxe = simplexml_load_file( $this->path . 'templates/default.xml');
		}

		
		$customParams =	 get_object_vars( $sxe->customParams[0]->attributes() );
		extract( $customParams['@attributes'], EXTR_OVERWRITE );

		$nativeParams =	 get_object_vars( $sxe->nativeParams[0]->attributes() );
		$style = $nativeParams['@attributes'];
		extract( $nativeParams['@attributes'], EXTR_OVERWRITE );
	}

	if ($action == 'Change Style') 
	{
		$action = 'Update Gallery';
		$sql = "UPDATE {$this->table_name} SET active_style = '{$active_style}' WHERE id = {$id}";		
		$this->db->query($sql);
	}
	
	//find empty values and set to default
}

extract($ssp_params, EXTR_SKIP );
#captionHeaderText Fix
$captionHeaderText = ($captionHeaderText == 'Off') ? '{imageTitle}' : $captionHeaderText;

# assign default values depending on current action
if (!strcmp($action, 'Add') && strcmp($_POST['action'], 'take_this_gallery_name')) {
	$sspName = $xmlFilePath = $xmlFileType = $sspWidth = $sspHeight = NULL;
} elseif ( isset( $_POST['action'] ) && !strcmp($_POST['action'], 'take_this_gallery_name')) {
	$xmlFilePath = $xmlFileType = $sspWidth = $sspHeight = NULL;
	$sspName = $_POST['sspName'];
}

if ( isset( $_POST['action'] ) && !strcmp($_POST['action'], 'change_style')) {
	$sspName		= strip_tags($_POST['sspName']);
	$xmlFilePath	= strip_tags($_POST['xmlFilePath']);
	$xmlFileType	= strip_tags($_POST['xmlFileType']);
	$sspWidth		= strip_tags($_POST['sspWidth']);
	$sspHeight		= strip_tags($_POST['sspHeight']);
}

# prepare items that need special treatment
$albumPreviewSize					= explode(',', $albumPreviewSize);
$navLinkPreviewSize				= explode(',', $navLinkPreviewSize);
$navThumbLinkSize				= explode(',', $navThumbLinkSize);
$captionPadding					= explode(',', $captionPadding);
$captionHeaderPadding			= explode(',', $captionHeaderPadding);
$textStrings					= explode(',', $textStrings);
$toolLabels				= explode(',', $toolLabels);

# define options for selectable items
		$navButtonStyle_options		        = array( 
														"Quartz Small", 
														"Quartz Large", 
														"Pearl Small", 
														"Pearl Large", 
														"Default" 
														);
			
		$xmlFileType_options			     = array( 
														"Default" => "Default", 
														"Director" => "Director", 
														"Media RSS" => "Media RSS", 
														"OPML" => "OPML", 
														"Manual Entry" => "Manual Entry", 
														"WordPress Gallery" => "WordPress Gallery", 
														"Single Content" => "Single Content"
														);
													
		$albumPreviewScale_options		     = array( "Proportional", 
														"None", 
														"Crop to Fit" 
														);
													
		$albumPreviewStyle_options		     = array( 
														"Inline Left", 
														"Inline Right", 
														"Fill", 
														"Banner" 
														);
														
		$albumTextAlignment_options		     = array( 
														"Center", 
														"Left", 
														"Right" 
														);
														
		$galleryOrder_options		 	     = array( 
														"Left to Right", 
														"Top to Bottom" 
														);
														
		$galleryAppearance_options			= array(
														"Visible",
														"Visible without Navigation",
														"Hidden"
													);
													
		$loop_options						= array(
														"Off",
														"On"
													);
														
		$mediaPlayerPosition_options 	     = array( 
														"Bottom", 
														"Top" 
														);
														
		$mediaPlayerAppearance_options	     = array(
														'Hidden', 
														'Always Visible', 
														'Visible on Rollover'
														);
														
		$navAppearance_options			     = array( 
														"Hidden",  
														"Always Visible", 
														"Visible on Rollover" 
														);
														
		$navButtonsAppearance_options	     = array( 
														"Hidden", 
														"All Visible", 
														"Hide Display Mode Button", 
														"Hide Gallery Button", 
														"Hide Full Screen Button", 
														"Hide Display Mode and Gallery Button", 
														"Hide Display Mode and Full Screen Buttons", 
														"Hide Display Mode and Gallery and Full Screen Buttons", 
														"Hide Gallery and Full Screen Buttons" 
														);
														
		$navGradientAppearance_options	     = array( 
														"Hidden", 
														"Glass Dark", 
														"Glass Light", 
														"Smooth Dark", 
														"Smooth Light", 
														"Concave Dark", 
														"Concave Light" 
														);
														
		$navLinkAppearance_options		     = array( 
														"Numbers", 
														"Thumbnails" 
														);
														
		$navLinkPreviewScale_options	     = array( 
														"None", 
														"Proportional", 
														"Crop to Fit" 
														);
														
		$navPosition_options			     = array( 
														"Bottom", 
														"Top" 
														);
														
		$captionElements_options		     = array( 
														"Header and Caption", 
														"Header Only", 
														"Caption Only" 
														);
														
		$contentAlign_options		 	     = array( 
														"Center", 
														"Top Left", 
														"Top Center", 
														"Top Right", 
														"Center Right", 
														"Bottom Right", 
														"Bottom Center", 
														"Bottom Left", 
														"Center Left" 
														);
														
		$contentAreaAction_options	         = array( 
														"Launch Hyperlink", 
														"Event", 
														"Toggle Display Mode", 
														"Toggle Full Screen", 
														"Open Gallery" 
														);
														
		$contentAreaInteractivity_options	 = array( 
														"Action Area Only", 
														"Action Area and Navigation" 
														);
														
		$contentOrder_options			     = array( 
														"Random", 
														"Sequential"
														 );
														 
		$contentScale_options			     = array( 
														"Crop to Fit", 
														"Crop to Fit All", 
														"Proportional", 
														"Downscale Only", 
														"None" 
														);
														
		$captionAppearance_options		     = array( 
														"Hidden", 
														"Inline", 
														"Overlay on Rollover", 
														"Overlay on Rollover (if Available)", 
														"Overlay Auto", 
														"Overlay Auto (if Available)", 
														"Fixed", 
														"Fixed (if Available)",
														"Fixed on Rollover", 
														"Fixed on Rollover (if Available)"
														);
														
		$captionPosition_options		    = array( 
														"Bottom", 
														"Top" 
														);
														
		$captionTextAlignment_options	    = array( 
														"Center", 
														"Left", 
														"Right" 
														);
														
		$feedbackPreloaderAlign_options		= array( 
														"Center", 
														"Top Left", 
														"Top Center", 
														"Top Right", 
														"Center Right", 
														"Bottom Right", 
														"Bottom Center", 
														"Bottom Left", 
														"Center Left" 
														);
														
		$feedbackPreloaderAppearance_options= array( 
														"Beam", 
														"Hidden", 
														"Bar", 
														"Line", 
														"Pie", 
														"Pie Spinner", 
														"Spinner" 
														);
														
		$feedbackPreloaderPosition_options	= array( 
														"Inside Content", 
														"Inside Content Area" 
														);
														
		$feedbackTimerAlign_options			= array( 
														"Center", 
														"Top Left", 
														"Top Center", 
														"Top Right", 
														"Center Right", 
														"Bottom Right", 
														"Bottom Center", 
														"Bottom Left", 
														"Center Left" 
														);
														
		$feedbackTimerPosition_options		= array( 
														"Inside Content", 
														"Inside Content Area" 
														);
														
		$cacheContent_options				= array( 
														"None", 
														"Only Images", 
														"Only Thumbnails", 
														"All" 
														);
														
		$displayMode_options				= array( 
														"Auto", 
														"Always Auto", 
														"Manual" 
														);
														
		$panZoomDirection_options 			= array(
														"In", 
														"Out", 
														"In and Out", 
														"Random"
														);
														
		$transitionStyle_options			= array( 
														"None", 
														"Blur", 
														"Cross Fade", 
														"Complete Fade", 
														"Dissolve", 
														"Drop", 
														"Lens", 
														"Photo Flash", 
														"Push", 
														"Wipe", 
														"Wipe to Background", 
														"Wipe and Fade", 
														"Wipe and Fade to Background" 
														);
														
		$transitionDirection_options		= array( 
														"Top to Bottom", 
														"Left to Right", 
														"Right to Left", 
														"Bottom to Top" 
														);
														
		$autoFinishMode_options				= array( 
														"Switch", 
														"Stop", 
														"Restart", 
														"Open Gallery" 
														);
														
		$startup_options					= array( 
														"Load Album", 
														"Load Album Then Wait", 
														"Open Gallery" 
														);
														
		$toolAppearanceContentArea_options	= array( 
														"Hidden", 
														"Visible", 
														"Action Area Hidden" 
														);
														
		$navButtonShadowStyle_options       = array( 
														"Inner", 
														"Under" 
														);
?>