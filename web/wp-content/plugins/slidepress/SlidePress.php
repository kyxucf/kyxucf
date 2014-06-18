<?php
/*
Plugin Name: SlidePress
Plugin URI: http://slidepress.net
Description: Create and manage SlideShowPro galleries from within WordPress.	<a href="http://wiki.slideshowpro.net/SSPsa/SP-SlidePress" title="SlidePress documentation">Documentation</a> | <a href="http://forums.slideshowpro.net/" title="SlidePress/SlideShowPro community forums">Forums</a> | <a href="http://slideshowpro.net/contact/" title="Contact us">Support</a>
Author: Dominey Design Inc.
Version: 1.4.7
Author URI: http://slideshowpro.net
Copyright 2010 by Dominey Design Inc.
*/

// include parameter list
include_once( 'tools/ssp_data.php' );

class SlidePress
{
	function SlidePress() {
		global $wpdb;
		$this->db         =& $wpdb;
		$this->folder_name = dirname( plugin_basename( __FILE__ ) );
		$this->path        = trailingslashit( dirname( __FILE__ ) );
		$this->prefix      = 'ssp_';
		$this->roles       = array();
		$this->swf_version = '1.9.8.5';
		$this->table_name  = $this->db->prefix . $this->prefix . 'galleries';
		$upload_dir = wp_upload_dir();
		$this->wp_uploads  = trailingslashit( $upload_dir['basedir'] );
		$this->upload_path = $this->wp_uploads .  'slidepress/';
		$this->upload_url  = trailingslashit( $upload_dir['baseurl'] ) . 'slidepress/';
		$this->url         = trailingslashit( get_option( 'siteurl' ) . '/wp-content/plugins/' . $this->folder_name );
		$this->version     = '1.4.7';
		$this->options     = array (
			'ssp_version'               => $this->version,
			'ssp_standaloneMode'        => 1,
			'ssp_lightboxMode'          => 0,
			'ssp_thickboxMode'          => 0,
			'ssp_lightviewMode'         => 0,
			'ssp_addNewGallery'         => 'Administrator',
			'ssp_modifyGallery'         => 'Administrator',
			'ssp_changeOptions'         => 'Administrator',
			'ssp_purgeUponDeactivation' => 0,
			'ssp_crossDomain'           => '',
			'ssp_crossDomain_notice'    => 1,
			'ssp_xml_folder_notice'     => 1,
			'ssp_noFlash'               => 'html',
			'ssp_noFlashHtml'           => '<p>This SlideShowPro photo gallery requires the Flash Player plugin and a web browser with JavaScript enabled.</p>',
			'ssp_remote'                => 1,
			'ssp_check_swf_version'     => 1
		);
		
		$this->dbsql =  "CREATE TABLE {$this->table_name} (
				id mediumint(9) NOT NULL AUTO_INCREMENT ,
				sspName varchar(250) NULL,
				sspGalleryId varchar(250) NULL,
				sspGalleryStatus BOOL NOT NULL DEFAULT '0' ,
				sspDescription text NULL,
				sspWidth varchar(10) NULL ,
				sspHeight varchar(10) NULL ,
				active_style varchar(250) NULL,
				albumBackgroundAlpha varchar(10) NULL ,
				albumBackgroundColor varchar(10) NULL ,
				albumDescColor varchar(10) NULL ,
				albumDescSize text NULL ,
				albumPadding text NULL ,
				albumPreviewScale text NULL ,
				albumPreviewSize text NULL ,
				albumPreviewStrokeColor varchar(10) NULL ,
				albumPreviewStrokeWeight text NULL ,
				albumPreviewStyle text NULL ,
				albumRolloverColor varchar(10) NULL ,
				albumStrokeAppearance text NULL ,
				albumStrokeColor varchar(10) NULL ,
				albumTextAlignment text NULL ,
				albumTitleColor varchar(10) NULL ,
				albumTitleSize text NULL ,
				audioAutoStart text NULL,
				audioLoop text NULL ,
				audioPause text NULL ,
				audioVolume text NULL ,
				autoFinishMode text NULL ,
				cacheContent text NULL ,
				captionAppearance text NULL ,
				captionBackgroundAlpha text NULL ,
				captionBackgroundColor varchar(10) NULL ,
				captionElements text NULL ,
				captionHeaderBackgroundAlpha text NULL,
				captionHeaderPadding text NULL ,
				captionHeaderText text NULL ,
				captionPadding text NULL ,
				captionPosition text NULL ,
				captionTextAlignment text NULL ,
				captionHeaderTextColor varchar(10) NULL ,
				captionTextColor varchar(10) NULL ,
				captionTextShadowAlpha text NULL ,
				captionTextSize text NULL ,
				contentAlign text NULL ,
				contentAreaAction text NULL ,
				contentAreaBackgroundAlpha text NULL ,
				contentAreaBackgroundColor varchar(10) NULL ,
				contentAreaInteractivity text NULL ,
				contentAreaStrokeAppearance text NULL ,
				contentAreaStrokeColor varchar(10) NULL ,
				contentFrameAlpha text NULL ,
				contentFrameColor varchar(10) NULL ,
				contentFramePadding text NULL ,
				contentFrameStrokeAppearance text NULL ,
				contentFrameStrokeColor varchar(10) NULL ,
				contentOrder text NULL ,
				contentScale text NULL ,
				contentScalePercent varchar(3) NULL,
				displayMode text NULL ,
				directorLargePublishing text NULL ,
				directorLargeQuality text NULL ,
				directorLargeSharpening text NULL ,
				directorThumbQuality text NULL ,
				directorThumbSharpening text NULL ,
				feedbackBackgroundAlpha text NULL ,
				feedbackBackgroundColor varchar(10) NULL ,
				feedbackHighlightAlpha text NULL ,
				feedbackHighlightColor varchar(10) NULL ,
				feedbackPreloaderAlign text NULL ,
				feedbackPreloaderAppearance text NULL ,
				feedbackPreloaderPosition text NULL ,
				feedbackPreloaderTextSize text NULL,
				feedbackPreloaderScale text NULL,
				feedbackTimerScale text NULL,
				feedbackTimerAlign text NULL ,
				feedbackTimerAppearance text NULL ,
				feedbackTimerPosition text NULL ,
				feedbackVideoButtonScale text NULL,
				fullScreenReformat text NULL ,
				fullScreenTakeOver text NULL ,
				galleryAppearance text NULL ,
				galleryBackgroundAlpha text NULL ,
				galleryBackgroundColor varchar(10) NULL ,
				galleryColumns text NULL ,
				galleryContentShadowAlpha text NULL ,
				galleryOrder text NULL ,
				galleryPadding text NULL ,
				galleryRows text NULL ,
				galleryNavActiveColor varchar(10) NULL ,
				galleryNavAppearance text NULL ,
				galleryNavInactiveColor varchar(10) NULL ,
				galleryNavRolloverColor varchar(10) NULL ,
				galleryNavStrokeAppearance text NULL ,
				galleryNavStrokeColor varchar(10) NULL ,
				galleryNavTextColor varchar(10) NULL ,
				galleryNavTextSize text NULL ,
				keyboardControl text NULL ,
				ssploop text NULL ,
				mediaPlayerAppearance text NULL ,
				mediaPlayerBackgroundAlpha text NULL ,
				mediaPlayerBackgroundColor varchar(10) NULL ,
				mediaPlayerBufferColor varchar(10) NULL ,
				mediaPlayerButtonColor varchar(10) NULL , 
				mediaPlayerControlColor varchar(10) NULL ,
				mediaPlayerElapsedBackgroundColor varchar(10) NULL ,
				mediaPlayerElapsedTextColor varchar(10) NULL ,
				mediaPlayerPosition text NULL ,
				mediaPlayerProgressColor varchar(10) NULL ,
				mediaPlayerScale text NULL ,
				mediaPlayerTextColor varchar(10) NULL ,
				mediaPlayerTextSize text NULL ,
				mediaPlayerVolumeBackgroundColor varchar(10) NULL ,
				mediaPlayerVolumeHighlightColor varchar(10) NULL ,
				navAppearance text NULL ,
				navBackgroundAlpha text NULL ,
				navBackgroundColor varchar(10) NULL ,
				navButtonsAppearance text NULL ,
				navButtonColor varchar(10) NULL ,
				navButtonInactiveAlpha text NULL ,
				navButtonShadowAlpha text NULL , 
				navGradientAlpha text NULL ,
				navGradientAppearance text NULL ,
				navButtonGlowAlpha text NULL ,
				navButtonGradientAlpha text NULL ,
				navButtonRolloverColor text NULL ,
				navButtonShadowStyle text NULL ,
				navButtonStyle text NULL,
				navLinkAppearance text NULL ,
				navLinkAnimate text NULL ,
				navLinkActiveColor varchar(10) NULL ,
				navLinkInactiveColor text NULL ,
				navLinkPreviewAppearance text NULL ,
				navLinkPreviewBackgroundAlpha varchar(10) NULL ,
				navLinkPreviewBackgroundColor varchar(10) NULL ,
				navLinkPreviewScale text NULL ,
				navLinkPreviewShadowAlpha text NULL ,
				navLinkPreviewSize text NULL ,
				navLinkPreviewStrokeWeight text NULL ,
				navLinkRolloverColor varchar(10) NULL ,
				navLinkSpacing text NULL ,
				navLinksBackgroundAlpha text NULL ,
				navLinksBackgroundColor varchar(10) NULL ,
				navLinksBackgroundShadowAlpha text NULL ,
				navNumberLinkSize text NULL ,
				navPosition text NULL ,
				navThumbLinkInactiveAlpha varchar(10) NULL ,
				navLinkShadowAlpha varchar(10) NULL ,
				navThumbLinkSize text NULL ,
				navThumbLinkStrokeWeight text NULL ,
				originalStyle text NULL,
				panZoom text NULL ,
				panZoomDirection text NULL,
				panZoomFinish text NULL,
				panZoomScale text NULL,
				permalinks text NULL,
				smoothing text NULL ,
				soundEffectsVolume text NULL ,
				startup text NULL ,
				startAlbumID text NULL,
				startContentID text NULL,
				textStrings text NULL ,
				toolAppearanceContentArea text NULL ,
				toolAppearanceNav text NULL ,
				toolColor varchar(10) NULL ,
				toolTextColor varchar(10) NULL ,
				toolTextSize text NULL ,
				toolDelayContentArea text NULL ,
				toolDelayNav text NULL ,
				toolTimeoutContentArea text NULL ,
				toolLabels text NULL ,
				transitionDirection text NULL ,
				transitionLength text NULL ,
				transitionPause text NULL ,
				transitionStyle text NULL ,
				typeface text NULL ,
				typefaceHead text NULL ,
				typefaceEmbed text NULL ,
				videoAutoStart text NULL ,
				videoBufferTime text NULL ,
				xmlFilePath text NULL ,
				xmlFileType text NULL,
				xmlManualSource text NULL,
				createThumbnails bool NULL,
				PRIMARY KEY	 (id),
				KEY sspGalleryStatus (sspGalleryStatus)
		);";

		
		// activation / deactivation hooks
		register_activation_hook  ( __FILE__, array( $this, 'action_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'action_deactivate' ) );
		
		// registers WordPress action hooks
		add_action( 'admin_footer'    , array( $this, 'action_init_colorpicker' ) );       // inserts farbtastic color picker container (has to be placed on top level)
		add_action( 'admin_head'      , array( $this, 'action_load_styles' ) );            // loads styles for admin pages
		add_action( 'admin_init'      , array( $this, 'action_init_options' ) );           // initializes SlidePress setup options
		add_action( 'admin_menu'      , array( $this, 'action_init_menu') );               // initializes top and sub menu items
		add_action( 'admin_notices'   , array( $this, 'action_init_check' ) );             // displays warning messages
		add_action( 'init'            , array( $this, 'action_load_scripts' ) );           // loads required scripts
		add_action( 'init'            , array( $this, 'action_register_tinymce') );        // registers tinymce button and menu
		add_action( 'pre_post_update' , array( $this, 'action_pre_post_update_thumbs' ) ); // updates WordPress gallery thumbs when the original post is updated
		add_action( 'set_current_user', array( $this, 'action_init_roles' ) );             // checks user roles
		add_action( 'wp_head'         , array( $this, 'action_load_styles' ) );            // loads styles for front-end pages
		add_action( 'wp_footer'       , array( $this, 'action_thickbox_fix' ) );           // fixes thickbox image paths
		
		// registers parametized action hooks
		add_action( 'after_plugin_row_slidepress/SlidePress.php', array( $this, 'action_notification' ) );		
		add_action( 'update_option_ssp_crossDomain', array( $this, 'action_update_crossdomain' ) );
		
		// registers WordPress admin area ajax hooks
		add_action( 'wp_ajax_hide_crossdomain_notice', array( $this, 'action_hide_crossdomain_notice' ) ); // clicking on the "hide" button on the crossdomain warning
		add_action( 'wp_ajax_hide_xml_folder_notice' , array( $this, 'action_hide_xml_folder_notice' ) );  // clicking on the "hide" button on the xml folder warning
		
		// registers SlidePress shortcode
		add_shortcode('slidepress', array( $this, 'shortcode' ) );
	}
	
	function action_activate() {

		// prepares the necessary directory structure for SlidePress
		$this->prepare_directory_structure();
		
		// checks if crossdomain.xml already exists and import its settings to SlidePress
		$this->check_crossdomain();

		$current_version = $this->get_current_version();

		
		// if gallery table doesn't exist, create it. otherwise, update
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $this->dbsql );
		
		// performs additional update queries based on current version
		if ( ! empty( $current_version ) )
			$this->update_database( $current_version );

		if ( false !== get_option( 'ssp_crossDomain_notice' ) )
			update_option( 'ssp_crossDomain_notice', 1 );

		if ( false !== get_option( 'ssp_xml_folder_notice' ) )
			update_option( 'ssp_xml_folder_notice', 1 );

		if ( false !== get_option( 'ssp_version' ) )
		{
			update_option( 'ssp_version', $this->version );
		}

		if ( false !== get_option( 'ssp_remote' ) )
		{
			update_option( 'ssp_remote', 1 );
		}
		
		if ( false !== get_option( 'ssp_check_swf_version' ) )
		{
			update_option( 'ssp_check_swf_version', 1 );
		}

		foreach ( $this->options as $option => $default_value ) {
			add_option( $option, $default_value );
		}

	}
	
	function action_deactivate() {
		if ( get_option( 'ssp_purgeUponDeactivation' ) == 1 ) {
			if ($this->db->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) == $this->table_name) {
				$this->db->query( "DROP TABLE {$this->table_name}" );
			}
			$this->delete_options();
		}
	}
	
	function action_hide_crossdomain_notice() {
		update_option( 'ssp_crossDomain_notice', 0 );
	}
	
	function action_hide_xml_folder_notice() {
		update_option( 'ssp_xml_folder_notice', 0 );
	}
	
	function action_init_check() {
		$user_dir    = str_replace( ABSPATH, '', $this->upload_path );
		$uploads_dir = get_option( 'upload_path', WP_CONTENT_DIR . '/uploads' );
		$errs        = array();

		if ( ! file_exists( $this->wp_uploads ) ) {
			$path = str_replace( ABSPATH, '', $this->wp_uploads );
			$errs['wp_uploads'] = "<strong>Error</strong>: the <code>{$path}</code> folder does not exist. Please create this folder before uploading images to your WordPress posts and adding SlidePress galleries.";
		} elseif( ! file_exists( $this->upload_path ) ) {
			$errs['upload_path'] = "<strong>Error</strong>: The necessary SlidePress folder structure has not been created. Try deactivating then activating the SlidePress plugin to solve this problem.";
		} else {
			// first checks for recent migration without updating upload path
			if ( strpos($this->upload_path, ABSPATH) === FALSE )
				$errs['migration'] = __('The Upload Directory path specified in <a href="options-misc.php">Settings -> Miscellaneous</a> is not correct. This is probably due to recent migration of your server. Please update the path in order for SlidePress to work properly.');
			else
			{
				// checks WordPress version
				if ( $GLOBALS['wp_version'] < '2.7' )
					$errs['wp_version'] = __('The SlidePress plugin requires WordPress 2.7.x or greater for many options to function properly. Please visit <a href="http://wordpress.org" target="blank">WordPress</a> and upgrade now.');

				// checks slideshowpro.swf
				if ( ! file_exists( $this->upload_path . 'flash/slideshowpro.swf' ) )
					$errs['source'] = __("SlidePress requires the SlideShowPro Standalone player to operate. <a href='admin.php?page=ssp_show_admin_setup#swf-upload' title='Setup'>Click here to upload the requisite SWF</a>. To purchase and/or download the most recent version of the player, visit <a target='_blank' href='http://www.slideshowpro.net'>SlideShowPro.net</a>");

				// checks default.xml (Default Style)
				if ( ! file_exists( $this->upload_path . 'templates/default.xml' ) ) {
					$relative_path = trailingslashit( str_replace( ABSPATH, '', WP_PLUGIN_DIR ) );
					$errs['default style'] = __("There is no <strong>Default Style (default.xml)</strong> for SlideShowPro in <code>{$user_dir}templates</code> folder. In order for SlidePress to work properly, please manually copy the default.xml file from <code>{$relative_path}slidepress/templates</code> to <code>{$user_dir}templates</code>.");	
				}

			 	// checks lightview
				if ( get_option( 'ssp_lightviewMode' ) && ! file_exists( $this->path . 'effects/lightview/js/lightview.js' ) ) {
					$site_url = trailingslashit( get_option( 'site_url' ) );
					$errs['lightview'] = __("You have activated <a href='http://www.nickstakenburg.com/projects/lightview/' target='_blank'>LightView</a> support in your SlidePress <a href='{$site_url}wp-admin/admin.php?page=ssp_show_admin_setup'>setup</a>, but it seems that you do not have LightView Installed in: <code>slidepress/effects/lightview</code> folder. Please refer to our <a href='{$siteurl}wp-admin/admin.php?page=ssp_show_admin_help'>help section</a> about the matter.");
				}

				// checks xml folder	
				if ( get_option( 'ssp_xml_folder_notice', 1 ) && ! is_writable( $this->upload_path . 'xml/' ) ) {
					$errs['xml'] = __("The <code>{$user_dir}xml</code> folder is currently write-protected. Please enable writing permission for xml folder now by chmodding it to 775 or 777.");
				}
			}
			
		}

		// checks crossdomain.xml
		
		$check_crossdomain = $this->check_crossdomain();
		
		// if crossdomain.xml doesn't exist
		if ( $check_crossdomain === FALSE && get_option( 'ssp_crossDomain_notice', 1 ) ) {
			// erase the cross domain option
			if ( get_option( 'ssp_crossDomain', '' ) !== '' )
				update_option( 'ssp_crossDomain', '' );

			$errs['crossdomain'] = __('To make sure SlideShowPro works properly, please create a crossdomain.xml file via <a href="admin.php?page=ssp_show_admin_setup#cross-domain-configuration">SlidePress -> Setup</a>.');
		}
		
		// if SimpleXML doesn't exist
		if ( $check_crossdomain === NULL )
			$errs['simplexml'] = __('<strong class="red">This is critical warning!</strong> Your server does not have the SimpleXML PHP extension installed (most likely because PHP is older than version 5). SlidePress cannot function without this extension. For more information, please contact your web host to inquire about upgrading to PHP 5 and installing SimpleXML. You may also read the complete <a href="admin.php?page=ssp_show_admin_help#Requirements">SlidePress requirements here</a>.');
		
		// if crossdomain.xml is not syntactically correct
		if ( $check_crossdomain === 0 ) {
			$url = parse_url( get_bloginfo( 'wpurl' ) );
			$errs['crossdomain_valid'] = __("Please check the syntax of your <a target='_blank' href='{$url['scheme']}://{$url['host']}/crossdomain.xml'>crossdomain.xml</a> file.");
		}

		// displays warnings if problems are detected
		if ( ! empty( $errs ) )
			foreach ( $errs as $type => $err ) {
				if ( 'crossdomain' == $type )
					$err .= '<span class="crossdomain-close-button close-button">[hide]</span>';
					
				if ( 'xml' == $type )
					$err .= '<span class="xml-folder-close-button close-button">[hide]</span>';
					
				echo "<div class='ssp_error ssp_error_{$type}'>{$err}</div>";
			}
	}
	
	function action_init_colorpicker() {
		echo "\n" . '<div id="ssp_farbtastic" style="display:none"> </div>' . "\n";
	}
	
	function action_init_menu() {
		add_menu_page( 'SlidePress Admin Interface', 'SlidePress', 1, $this->folder_name, array( $this, 'admin_page_overview' ) );

		add_submenu_page( $this->folder_name, 'SlidePress Admin Interface', 'Overview', 1, $this->folder_name, array( $this, 'admin_page_overview' ) );

		// checks for permisions before adding menu items
		if ( $this->check_permission_against( get_option( 'ssp_addNewGallery' ) ) )
			add_submenu_page( $this->folder_name, 'SlidePress Add Gallery', 'Add Gallery', 1, 'ssp_show_admin_addgallery', array( $this, 'admin_page_add_gallery' ) );
			
		if ( $this->check_permission_against( get_option( 'ssp_modifyGallery' ) ) )
			add_submenu_page( $this->folder_name, 'SlidePress Manage Galleries', 'Manage Galleries', 1, 'ssp_show_admin_managegallery', array( $this, 'admin_page_manage_gallery' ) );
			
		if ( $this->check_permission_against(get_option( 'ssp_changeOptions' ) ) )
			add_submenu_page( $this->folder_name, 'SlidePress Setup', 'Setup', 1, 'ssp_show_admin_setup', array( $this, 'admin_page_setup') );

		add_submenu_page( $this->folder_name, 'SlidePress Help', 'Help', 1, 'ssp_show_admin_help', array( $this, 'admin_page_help' ) );
		add_submenu_page( $this->folder_name, 'SlidePress Bug Report', 'Bug Report', 1, 'ssp_show_admin_debug_info', array( $this, 'admin_page_debug_info' ) );
	}
		
	function action_init_options() {
		$function = '';
		
		if ( function_exists( 'register_setting' ) )
			$function = 'register_setting';
		elseif ( function_exists( 'add_option_update_handler' ) )
			$function = 'add_option_update_handler';
			
		if ( !empty( $function ) )
			foreach ( $this->options as $option => $default_value ) {
				call_user_func( $function, 'ssp', $option );
			}
	}
	
	function action_init_roles() {
		global $wp_roles;
		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();
		array_walk( $wp_roles->get_names(), array($this, 'map_roles') );
	}
	
	function action_load_scripts() {
		wp_enqueue_script( 'swfobject' );

		// if admin panel is being viewed
		if ( strpos($_SERVER['PHP_SELF'], 'wp-admin' ) !== false ) {
			// embed global variables
			wp_localize_script('jquery','SlidePress', array(
				'siteurl' => get_bloginfo('wpurl'),
				'sspurl' => $this->url
			));

			wp_enqueue_script( 'facebox', $this->url .'js/facebox.js', array( 'jquery' ) );

			if ( isset( $_GET['page'] ) )
				switch ( $_GET['page'] ) {
					// loads scripts for SlidePress Add & Manage pages
					case 'ssp_show_admin_addgallery' :
						if ( isset( $_GET['iframe'] ) && $_GET['iframe'] == 1 )
							wp_enqueue_script( 'addgalleryiframe', $this->url .'js/addgalleryiframe.js', array( 'jquery' ) );
					case 'ssp_show_admin_managegallery' :
						wp_enqueue_script( 'farbtastic',     $this->url . 'js/farbtastic.js', array( 'jquery' ) );
						wp_enqueue_script( 'rgbcolor',       $this->url . 'js/rgbcolor.js' );
						wp_enqueue_script( 'ssp_farbtastic', $this->url . 'js/ssp_farbtastic.js', array( 'farbtastic', 'rgbcolor' ) );
						wp_enqueue_script( 'ssp_help',       $this->url . 'js/help.js', array( 'facebox' ) );
						wp_enqueue_script( 'xmlFileType',    $this->url . 'js/xmlfiletype.js', array( 'jquery' ) );
						break;
					// loads scripts for SlidePress Setup page	
					case 'ssp_show_admin_setup' :
						wp_enqueue_script( 'swfupload-all' );
						wp_enqueue_script( 'ssp_help', $this->url . 'js/help.js', array( 'facebox' ) );
						break;
					// loads scripts for SlidePress Bug Report page
					case 'ssp_show_admin_debug_info' :
						wp_enqueue_script( 'ssp_bug_report', $this->url . 'js/bug_report.js', array( 'jquery' ) );
						break;
				}

			if (get_option('ssp_crossDomain_notice', 1))
				wp_enqueue_script( 'crossDomain_notice', $this->url . 'js/crossDomain_notice.js', array( 'jquery' ) );

			if (get_option('ssp_xml_folder_notice', 1))
				wp_enqueue_script( 'xml_folder_notice', $this->url . 'js/xml_folder_notice.js', array( 'jquery' ) );

		} else { 
			// if it's not admin area, load these scripts
			
			if ( current_user_can('manage_options') ) {
				if (get_option('ssp_check_swf_version', 1)) {
					wp_enqueue_script( 'slidepress-check-swf-version', "{$this->url}js/check_swf_version.js", array( 'jquery', 'swfobject' ) );
					wp_localize_script( 'slidepress-check-swf-version', 'SlidePress', array(
						'swfVersion' => $this->swf_version,
						'sspVersion' => $this->version,
						'images_url'  => "{$this->url}css/",
						));
				}
			}
			
			if ( get_option( 'ssp_lightviewMode' ) & file_exists( $this->path . 'effects/lightview/js/lightview.js' ) ) {
				wp_enqueue_script( 'prototype-1.6.0.2',         $this->url .'js/prototype-1.6.0.2.js', false, '1.6.0.2' );
				wp_enqueue_script( 'ssp_scriptaculous',	        '/wp-includes/js/scriptaculous/scriptaculous.js', array( 'prototype-1.6.0.2' ) );
				wp_enqueue_script( 'ssp_scriptaculous_effects', '/wp-includes/js/scriptaculous/effects.js', array( 'ssp_scriptaculous' ) );
				wp_enqueue_script( 'lightview',                 $this->url .'effects/lightview/js/lightview.js', array( 'ssp_scriptaculous_effects' ) );
			}

			if ( get_option( 'ssp_thickboxMode' ) )
				add_thickbox();
		}
	}
	
	function action_load_styles() {
		// if current page is in admin area
		if ( strpos( $_SERVER['PHP_SELF'], 'wp-admin' ) !== false ) {
			wp_admin_css( 'dashboard' );

			echo '<link rel="stylesheet" href="' . $this->url . 'css/SlidePress.css" type="text/css" media="screen" />'."\n";
			echo '<link rel="stylesheet" href="' . $this->url . 'css/facebox.css" type="text/css" media="screen" />';

			if ( isset( $_GET['page'] ) ) 
				switch ($_GET['page']) {
					case 'ssp_show_admin_addgallery':
						if ( isset( $_GET['iframe'] ) && $_GET['iframe'] == 1 )
							echo '<link rel="stylesheet" href="' . $this->url . 'css/addgalleryiframe.css" type="text/css" media="screen" />'."\n";
					case 'ssp_show_admin_managegallery':
						echo '<link rel="stylesheet" href="' . $this->url . 'css/farbtastic.css" type="text/css" media="screen" />'."\n";
						break;
				}

			// loads compatible styles for WordPress 2.7.x and 2.8.x
			if ( strstr( $GLOBALS['wp_version'], '2.7' ) || strstr( $GLOBALS['wp_version'], '2.8' ) )
				echo '<link rel="stylesheet" href="' . $this->url . 'css/wordpress27.css" type="text/css" media="screen" />';
		
		} else {
			// if not in admin area
			
			if ( get_option( 'ssp_lightviewMode' ) && file_exists( $this->path . 'effects/lightview/js/lightview.js' ) )
				echo '<link rel="stylesheet" href="' . $this->url . 'effects/lightview/css/lightview.css" type="text/css" media="screen" />';

			if ( get_option( 'ssp_thickboxMode' ) ) {
				echo '<link rel="stylesheet" href="' . get_option('siteurl') . '/wp-includes/js/thickbox/thickbox.css" type="text/css" media="screen" />' . "\n";
			}

		}
	}
		
	function action_notification( $plugin ) {
		$current = get_option( 'update_plugins' );
		if ( ! empty( $current ) && isset( $current->response[$plugin] ) ) {
			$release   = $current->response[$plugin];
			$file_name = "http://slidepress.net/releases/{$release->new_version}.txt";
			$response  = wp_remote_get( $file_name );
			if ( ! is_wp_error( $response ) && $response['response']['code'] == 200 ) {
				$temp    = explode('|', $response['body']);
				$class   = $temp[0];
				$title   = $temp[1];
				$message = $temp[2];
				echo "<tr class='slidepress-update-info'><td class='{$class}' colspan='5'><h6>{$title}</h6><div>{$message}</div></td></tr>";
			}
		}
	}
	
	function action_register_tinymce() {
		// check the requirements and permissions
		// TODO: checks for slidepress permissions as well!
		if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_pages' ) )
			return;
		
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			add_filter( 'mce_buttons'         , array( $this, 'filter_tinymce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_tinymce_plugin' ) );
			add_filter( 'the_editor'          , array( $this, 'filter_hidden_field'   ) );
		}
	}
	
	function action_pre_post_update_thumbs( $post_id ) {
		$query      = $this->db->prepare( "SELECT `sspGalleryId`, `navLinkPreviewSize`, `createThumbnails` FROM {$this->table_name} WHERE `xmlFilePath` = %s AND `xmlFileType` = 'WordPress Gallery'", $post_id );
		$results    = $this->db->get_results( $query );

		foreach ( $results as $gallery ) {
			if ( $gallery->createThumbnails == '1') {
				$size = explode( ',', $gallery->navLinkPreviewSize );
				$this->update_thumbs( $post_id, $gallery->sspGalleryId, $size[0], $size[1] );
			}
		}
	}
	
	function action_thickbox_fix() {
		echo '<script type="text/javascript">var tb_pathToImage = "'.get_option('siteurl').'/wp-includes/js/thickbox/loadingAnimation.gif";var tb_closeImage = "'.get_option('siteurl').'/wp-includes/js/thickbox/tb-close.png";</script>' . "\n";
	}
	
	function action_update_crossdomain( $old_value ) {
		$file = trailingslashit( $_SERVER['DOCUMENT_ROOT'] ) . 'crossdomain.xml';
		if ( (get_option( 'ssp_crossDomain' ) !== '' ) && ( is_writable( $file ) || ( is_writable( $_SERVER['DOCUMENT_ROOT'] ) && ! file_exists( $file ) ) ) ) {
			require_once( 'tools/crossdomain.php' );
			$xml    = ssp_crossdomain_xml( get_option( 'ssp_crossDomain' ) );
			$handle = fopen( $file, 'w' );
			fwrite( $handle, $xml );
			fclose( $handle );
		}
	}

	function admin_page_add_gallery() {
		global $ssp_msg;
		$active_style      = 'default';
		$active_style_type = 'xml';
		
		if ( isset( $_POST['action'] ) )
			switch ( $_POST['action'] ) :
				case 'take_this_gallery_name':
					$sspName = $this->db->escape( strip_tags( $_POST['sspName'] ) );
					break;
				case 'change_style':
					$active_style      = addslashes( strip_tags( $_POST['active_style'] ) );
					$active_style_type = addslashes( strip_tags( $_POST['active_style_type'] ) );
					break;
				case 'Save Gallery':
					$this->run_query( 'INSERT', '<div class="ssp_msg ssp_gallery_added">Gallery added successfully!</div>' );
					break;
			endswitch;

		$styles_xml = $this->get_xml_styles();

		$i = 0;

		$db_styles = $this->db->get_results( "SELECT sspGalleryId, sspName FROM $this->table_name ORDER BY id DESC" );
		$styles_db = array();
		foreach ($db_styles as $style) {
			$styles_db[$style->sspGalleryId] = $style->sspName;
		}

		$action = 'Save Gallery';

		include_once( $this->path . 'tools/prep_settings.php' );
		include_once( $this->path . 'tpl/ssp_add.php' );
	}

	function admin_page_debug_info() {
		if ( isset( $_POST['submit'] ) && check_admin_referer( 'ssp_submit_report' ) ) {
			$description = stripslashes_deep( $_POST['description'] );
			$report      = stripslashes_deep( $_POST['report'] );
			$body        = "Bug Description\n---------------------\n{$description}\n---------------------\n{$report}";
			$mail_result = @mail( 'support@slidepress.net', 'Debug Report', $body );
		}
		
		$table_exists   = $this->db->get_var( "SHOW TABLES LIKE '{$this->table_name}'" ) == $this->table_name;
		$all_plugins    = get_plugins();
		$active_plugins = array();
		
		foreach ( $all_plugins as $file => $plugin ) {
			if ( is_plugin_active( $file ) ) {
				$active_plugins[] = "{$plugin['Name']} ({$plugin['PluginURI']})";
			}
		}
		
		$current_theme       = current_theme_info();
		$permalink_structure = get_option('permalink_structure');
		$crossdomain         = trailingslashit($_SERVER['DOCUMENT_ROOT']) . 'crossdomain.xml';
		
		if ( file_exists( $crossdomain ) && is_readable( $crossdomain ) ) {
			$fh = fopen( $crossdomain, "r" );
			$crossdomain_contents = fread( $fh, filesize( $crossdomain ) );
			fclose( $fh );
		}
		else {
			$crossdomain_contents = "crossdomain.xml file either doesn't exist or is not accessible.";
		}
		
		$htaccess = get_home_path() . '.htaccess';
		if ( file_exists( $htaccess ) && is_readable( $htaccess ) ) {
			$fh = fopen( $htaccess, "r");
			$htaccess_contents = fread( $fh, filesize( $htaccess ) );
			fclose( $fh );
		}
		else {
			$htaccess_contents = ".htaccess file either doesn't exist or is not accessible.";
		}
		
		$params = array(
				'Environment Variables'	=> array(
				'Admin Email Address'               => get_option( 'admin_email' ),
				'SlidePress User Path'              => $this->upload_path,
				'SlidePress User Dir URL'           => $this->upload_url,
				'Installation URL'                  => get_option( 'siteurl' ),
				'PHP Version'                       => phpversion(),
				'MySQL Version'                     => mysql_get_server_info(),
				'WordPress Version'                 => $GLOBALS['wp_version'],
				'SlidePress Version'                => $this->version,
				'SlidePress database table created'	=> ( $table_exists ) ? 'Yes' : 'No',
				'Number of SlidePress galleries'    => ( $table_exists ) ? $this->db->get_var( "SELECT COUNT(*) FROM `{$this->table_name}`" ) : 0,
				'Allowed domains'                   => implode( ', ', explode( "\n", get_option( 'ssp_crossDomain' ) ) ),
				'Active Plugins'                    => implode( ', ', $active_plugins ),
				'Active Theme'                      => $current_theme->name,
				'Permalink Structure'               => empty( $permalink_structure ) ? 'Default' : $permalink_structure ),
			'crossdomain.xml'       => $crossdomain_contents,
			'.htaccess'             => $htaccess_contents,
		);
		include_once( $this->path . 'tpl/ssp_debug_info.php' );
	}

	function admin_page_help() {
		include_once( $this->path . 'tpl/ssp_help.php' );
	}

	function admin_page_manage_gallery() {
		$id = NULL;
		global $ssp_params, $ssp_msg;

		# operate depending on action passed from outside
		switch ( $_POST['action'] ) :
			case 'delete':
				if ( $this->db->query( "DELETE FROM " . $this->table_name . " WHERE id = '" . $this->db->escape( $_POST['id'] ) . "'" ) )
					$ssp_msg = '<div class="ssp_msg">Gallery deleted successfully!</div>';
				else
					$ssp_msg = '<div class="ssp_error">A gallery by that name doesn\'t exist.</div>';
					
				$action = 'Hide';
				break;
			case 'change_style':
				extract( $_POST );
				$action       = 'Change Style';
				$id           = $this->db->escape( $_POST['id'] );
				$last_gallery = $this->db->get_row( "SELECT * FROM ".$this->table_name." WHERE id = '".(!is_null($id)?$id:$galleries[0]->id)."'", ARRAY_A );
				
				if ( $active_style != $last_gallery['sspGalleryId'] && $last_gallery['active_style'] == $last_gallery['sspGalleryId'] ) {
					$sspName = $last_gallery['sspName'];
					$style= array();
					
					foreach ( $last_gallery as $param => $value ) {
						if ( in_array($param, $ssp_params) && ! in_array( $param, array( 'sspName', 'sspWidth', 'sspHeight', 'xmlFilePath', 'xmlFileType' ) ) )
							$style[$param] = $value;
					}
					
					$original_style = serialize( $style );
				}
				
				include_once( $this->path . 'tools/prep_settings.php' );
				break;
			case 'manage':
				$action       = 'Update Gallery';
				$id           = $this->db->escape( $_POST['id'] );
				$this_gallery = $this->db->get_row( "SELECT sspName, sspGalleryId, sspHeight, sspWidth, xmlFilePath, xmlFileType, sspDescription, createThumbnails, active_style, startAlbumID, startContentID FROM " . $this->table_name . " WHERE id = '". ( ! is_null( $id ) ? $id : $galleries[0]->id) . "'", ARRAY_A );
				extract( $this_gallery );
				$this_gallery_styles = $this->db->get_row( "SELECT * FROM " . $this->table_name . " WHERE sspGalleryId = '{$active_style}'", ARRAY_A );
				if ( is_array( $this_gallery_styles ) && ! empty( $this_gallery_styles ) )
					extract($this_gallery_styles, EXTR_SKIP);
				include_once( $this->path . 'tools/prep_settings.php' );
				break;
			case 'Update Gallery':
				$action = 'Update Gallery';
				$id     = $this->db->escape( $_POST['id'] );
				
			   if ( $_POST['active_style'] == -1 ) {
					$_POST['active_style'] = 'Custom';
					$_POST['originalStyle'] = serialize( $this->db->get_row( "SELECT " . implode( ',', array_slice( $ssp_params, 3 ) ) . " FROM " . $this->table_name . " WHERE id = '" . ( ! is_null( $id ) ? $id : $galleries[0]->id )."'", ARRAY_A ) );
				}
				
				$this->run_query( 'UPDATE', '<div class="ssp_msg">Gallery updated successfully!</div>', "WHERE id = '" . $id . "'" );
				
				$last_gallery = $this->db->get_row( "SELECT * FROM " . $this->table_name . " WHERE id = '" . ( ! is_null( $id ) ? $id : $galleries[0]->id ) ."'", ARRAY_A );
				extract( $last_gallery );
				include_once( $this->path . 'tools/prep_settings.php' );
				break;
			default:
				$action = 'Hide';
		endswitch;

		# get galleries
		
		# Get the num of results per page
		$limit = 10;  
		
		# Get the number of rows in the table
		$count = $this->db->get_var($this->db->prepare("SELECT COUNT(*) FROM $this->table_name;"));
		
		// pagination
		$_GET['paged'] = isset($_GET['paged']) ? intval($_GET['paged']) : 0;
		if ( $_GET['paged'] < 1 )
			$_GET['paged'] = 1;
			
		$start = ( $_GET['paged'] - 1 ) * 10;
		
		if ( $start < 1 )
			$start = 0;
	
		# Retrieve galleries
		$query = "SELECT id, sspGalleryId, sspGalleryStatus, sspName, sspWidth, sspHeight FROM $this->table_name ORDER BY id DESC LIMIT ".$start.", ".$limit;
		$galleries	= $this->db->get_results($query);

		# if no galleries yet, show corresponding text
		if ( ! sizeof( $galleries ) ) {
			$ssp_notice_h2 = 'Manage Galleries';
			$ssp_notice_p = 'No galleries have been created.<br /><form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=ssp_show_admin_addgallery"><input type="submit" class="button" value="Create new gallery" /></form>';
			include_once( $this->path . 'tpl/ssp_notice.php' );
			return;
		}

		$i = 0;
		# get styles from database
		$db_styles = $this->db->get_results( "SELECT sspGalleryId, sspName FROM $this->table_name ORDER BY id DESC" );
		$styles_db = array();
		foreach ($db_styles as $style) {
			$styles_db[$style->sspGalleryId] = $style->sspName;
		}
		
		$styles_xml = $this->get_xml_styles();

		include_once( $this->path . 'tpl/ssp_manage.php' );
	}

	function admin_page_overview() {
		$galleries	= $this->db->get_results( "SELECT id, sspGalleryId, sspGalleryStatus, sspName, sspWidth, sspHeight FROM {$this->table_name} ORDER BY id DESC LIMIT 0, 3" );

		include_once( $this->path . 'tpl/ssp_overview.php' );
	}

	function admin_page_setup() {
		$ssp_options = $this->get_options();
		extract( $ssp_options );
		include_once( $this->path . 'tpl/ssp_setup.php' );
	}

	function filter_hidden_field( $content ) {
		$galleries  = $this->db->get_results("SELECT sspGalleryId, sspName FROM {$this->table_name}");
		if ( function_exists( 'json_encode' ) )
			$galleries_json = json_encode( $galleries );
		else
			$galleries_json = $this->galleries_to_json( $galleries );
		$content   .= '<script type="text/javascript">SlidePress.galleries = ' . $galleries_json . '</script>';
		return $content;
	}

	function filter_tinymce_button( $buttons ) {
		array_push( $buttons, '|', 'slidepress_button' );
		return $buttons;
	}
	
	function filter_tinymce_plugin( $plugin_array ) {
		$plugin_array['slidepress'] = $this->url . 'js/editor_plugin.js';
		return $plugin_array;
	}

	function check_crossdomain() {
		$file = $_SERVER['DOCUMENT_ROOT'] . '/crossdomain.xml';

		if ( ! function_exists( 'simplexml_load_file' ) )
			return NULL;

		if ( ! file_exists( $file ) ||  ! is_readable( $file ) )
			return FALSE;

		$xml = @simplexml_load_file( $file );
		if ( ! $xml )
			return 0;

		$allowed_domains = array();

		foreach ( $xml->{'allow-access-from'} as $allow ) {
			$allowed_domains[] = $allow['domain'];
		}

		$output = implode( "\n", $allowed_domains );

		if ( $output != get_option( 'ssp_crossDomain','' ) )
			update_option( 'ssp_crossDomain', implode( "\n", $allowed_domains ) );

		return TRUE;
	}

	function check_permission_against($role) {
		global $current_user;
		$role_index = array_search( $this->get_current_user_role(), $this->roles );
		return ( $role_index !== FALSE && $role_index <= array_search( $role, $this->roles ) );
	}

	function chmod_recursive( $dir, $mode ) {
		$files = scandir( $dir );
		foreach ( $files as $item ) {
			if ( ! in_array( $item, array( '.', '..' ) ) ) {
				$item_name = trailingslashit( $dir ) . $item;
				@chmod( $item_name, $mode );

				if ( is_dir( $item_name ) )
					$this->chmod_recursive( $item_name, $mode );
			}
		}
	}

	function copy_dir( $old, $new ) {
		$files = scandir( $old );
		foreach ( $files as $name )
		{
			$oldname = trailingslashit( $old ) . $name;
			$newname = trailingslashit( $new ) . $name;
			if ( ! in_array( $name, array( '.', '..' ) ) ) {
				if ( is_dir( $name ) ) {
					if ( ! file_exists( $newname ) ) 
						if ( ! @mkdir( $newname ) )
							return FALSE;

					if ( ! $this->copy_dir( $oldname, $newname ) )
						return FALSE;
				}
				else {
					if ( ! @copy( $oldname, $newname ) )
						return FALSE;
				}
			}
		}
		return TRUE;
	}

	function delete_options() {
		foreach ( $this->options as $option => $default_value ) {
			delete_option( $option );
		}
	}
	
function page_exists($url){
  $parts=parse_url($url);
  if(!$parts) return false; /* the URL was seriously wrong */
 
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
 
  /* set the user agent - might help, doesn't hurt */
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
 
  /* try to follow redirects */
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
 
  /* timeout after the specified number of seconds. assuming that this script runs 
    on a server, 20 seconds should be plenty of time to verify a valid URL.  */
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
  curl_setopt($ch, CURLOPT_TIMEOUT, 20);
 
  /* don't download the page, just the header (much faster in this case) */
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_setopt($ch, CURLOPT_HEADER, true);
 
  /* handle HTTPS links */
  if($parts['scheme']=='https'){
  	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  1);
  	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  }
 
  $response = curl_exec($ch);
  curl_close($ch);
 
  /*  get the status code from HTTP headers */
  if(preg_match('/HTTP\/1\.\d+\s+(\d+)/', $response, $matches)){
  	$code=intval($matches[1]);
  } else {
  	return false;
  };
 
  /* see if code indicates success */	
  return (($code>=200) && ($code<400));	
}

function is_valid_url($original_url)
{

	$url = $original_url;
    $url = @parse_url($url);
    if (!$url)
    {
        return false;
    }

    $url = array_map('trim', $url);
    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';

    if ($path == '')
    {
        $path = '/';
    }

    $path .= (isset($url['query'])) ? "?$url[query]" : '';

    if ( isset($url['host']) AND $url['host'] )
    {

        if (PHP_VERSION >= 5 && ini_get('allow_url_fopen') )
        {
            $headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
        }
        else
        {
            $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

            if (!$fp)
            {
                return false;
            }
            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
            $headers = fread($fp, 4096);
            fclose($fp);
        }
        $headers = (is_array($headers)) ? implode("\n", $headers) : $headers;
        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
      }
    return false;
}

	function display_gallery( $sspGalleryId, $template = 'template', $echo = TRUE, $content = NULL, $sspGalleryTitle = NULL, $sspGalleryImage = NULL ) {
		$noFlash_opt = get_option( 'ssp_noFlash' );

		if ( empty( $template ) ) {
			$template = 'template';
		}
		

		$WxH = $this->db->get_row( "SELECT sspWidth, sspHeight, sspName, xmlFileType, xmlFilePath, startAlbumID, startContentID FROM ".$this->table_name." WHERE sspGalleryId = '".$this->db->escape( $sspGalleryId )."'", ARRAY_A );

		if ( empty( $WxH ) ) {
			$error = "<p style='font-size:12px; font-family:Arial; background:#ffdfdf url(" . $this->url . "css/error.png) no-repeat 10px center; border-width:1px 0; border-style:solid; border-color:#ff0000; line-height:1.5em; margin:10px 0; padding:10px 10px 10px 36px;'>SlidePress cannot find the gallery with this id: <code style='font-size:12px; background:#ccc;'>{$sspGalleryId}</code></p>";
			if ( $echo ) {
				echo $error;
				return;
			}
			else {
				return $error;
			}
		}


		extract( $WxH );
		
				
		$host = parse_url($xmlFilePath);
		if( ($host['host'] != FALSE) && ($xmlFileType == 'Director')  )
		{
			if( get_transient( $sspGalleryId ) )
				delete_transient( $sspGalleryId );

			$path = parse_url( $xmlFilePath );
			$path = str_replace('images.php', '', $path['path'] );
			$embed_url = 'http://'.$host['host'].$path.'m/embed.js';
			$ssp_djs = $this->is_valid_url($embed_url);
			if( $ssp_djs !== FALSE )
			{
				$use_director_embed = true;
				set_transient($sspGalleryId, TRUE);
				
				//get params
				$director_params = $this->db->get_row( "SELECT * FROM ".$this->table_name." WHERE sspGalleryId = '".$this->db->escape( $sspGalleryId )."'", ARRAY_A );
				$director_params = $this->params_to_object( $director_params );
			}
			else {
				$use_director_embed = false;
			}
		}
		
		switch ( $noFlash_opt ) {
			case 'html':
				$alternative_content = get_option( 'ssp_noFlashHtml' );
				break;
			case 'images':
				if ( $xmlFileType == 'WordPress Gallery' ) {
					$alternative_content = gallery_shortcode( array( 'id' => $xmlFilePath ) );
				}
				elseif ( $xmlFileType == 'Manual Entry' ) {
					$pathinfo = pathinfo($xmlFilePath);
					$xml_path = $this->upload_path . 'xml/' . $pathinfo['filename'] . '.' . $pathinfo['extension'];
					$xml      = @simplexml_load_file( $xml_path );

					foreach ( $xml->children() as $album ) {
						$alternative_content .= '<p class="slidepress-album-title">' . $album['title'] . '</p>';
						$alternative_content .= '<ul>';
						foreach ( $album->children() as $img ) {
							$img_src              = ( empty( $album['lgpath'] ) ? '' : trailingslashit( $album['lgpath'] ) ) . $img['src'];
							$alternative_content .= '<li>';
							$alternative_content .= '<a href="' . $img_src . '"><img src="' . $img_src . '" alt="' . $img['alt'] . '" /></a>';
							$alternative_content .= '</li>';
						}
						$alternative_content .= '</ul>';
					}
				}
				else {
					$alternative_content = get_option( 'ssp_noFlashHtml' );
				}
				break;
		}

		if ( is_null( $sspGalleryTitle ) ) $sspGalleryTitle = $sspName;

		ob_start();
		include( $this->path . 'tpl/' . 'ssp_' . $template . '.php' );
		$ssp = ob_get_contents();
		ob_end_clean();

		if ( $echo ) {
			echo $ssp;
		}
		else {
			return $ssp;
		}
	}

	function get_current_user_role() {
		global $current_user, $wp_roles;
		$role_names = $wp_roles -> get_names();
		return preg_replace( "/\|.*/", '', $role_names[$current_user->roles[0]]);
	}

	function get_current_version( ) {
		$table_exists = $this->db->get_row( "SHOW TABLES LIKE '{$this->table_name}'" );
		if ( empty( $table_exists ) )
			return false;

		$version_conditions = array(
			'captionElements' => '1.11',
			'captionHeaderBackgroundAlpha' => '1.12',
			'feedbackPreloaderTextSize' => '1.13',
		);

		foreach ( $version_conditions as $new_field => $version )
		{
			$column = $this->db->get_row( "SHOW COLUMNS FROM {$this->table_name} LIKE '{$new_field}'" );
			if ( empty( $column ) )
				return $version;
		}

		$current_version = get_option( 'ssp_version' );
		if ( empty( $current_version ) || $current_version < '1.16')
			return '1.15';

		return $current_version;
	}

	function get_options() {
		foreach ( $this->options as $option => $default_value ) {
			if ( in_array( $option, array( 'ssp_standaloneMode', 'ssp_version') ) ) continue;
			$ssp_options[$option] = get_option( $option, $default_value );
		}
		return $ssp_options;
	}

	function get_wp_upload_path() {
		$siteurl     = get_option( 'siteurl' );
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		if ( empty( $upload_path ) )
			$dir = WP_CONTENT_DIR . '/uploads';
		else
			$dir = $upload_path;

		// $dir is absolute, $path is (maybe) relative to ABSPATH
		$dir = path_join( ABSPATH, $dir );

		if ( !$url = get_option( 'upload_url_path' ) ) {
			if ( empty( $upload_path ) or ( $upload_path == $dir ) )
				$url = WP_CONTENT_URL . '/uploads';
			else
				$url = trailingslashit( $siteurl ) . $upload_path;
		}

		if ( defined('UPLOADS') ) {
			$dir = ABSPATH . UPLOADS;
			$url = trailingslashit( $siteurl ) . UPLOADS;
		}
		return array( 'url' => $url, 'dir' => $dir );
	}

	function get_xml_styles() {
		$styles_xml = array();
		# get styles from templates directory
		$i = 0;
		if ( file_exists( $this->upload_path . 'templates' ) ) {
			$files = scandir( $this->upload_path . 'templates' );
			if ( ! empty( $files ) )
			{
				foreach ( $files as $file ) {
					if ( ! strcmp( substr( $file, -3 ), 'xml' ) ) {
							$filename = strtolower( str_replace( '.xml', '', $file ) );
							if ( ! strcmp( $filename, 'default' ) ) continue;
							$styles_xml[$i++] = $filename;
					}
				}
			}
		}
		return $styles_xml;
	}

	function galleries_to_json( $galleries ) {
		$results = array();
		foreach ( $galleries as $gallery ) {
			$properties = array();
			foreach ( $gallery as $property => $value ) {
				$properties[] = "{$property} : '{$value}'";
			}
			$results[] = '{' . implode( ',', $properties ) . '}';
		}
		return ;
	}
	
	function params_to_object( $p = array() ) {
		global $ssp_params;
		$exclude = array(
						'sspName',
						'sspWidth',
						'sspHeight',
						'xmlFileType',
						'createThumbnails'
						);
		
		$param_names = array_keys($ssp_params);
		$param_names = array_diff($param_names, $exclude);
		$properties = array();
		foreach ( $param_names as $param_name ) 
		{
			$v = $p[$param_name];
			if( empty( $v ) && $v != 0 ) continue;
		
			$properties[] = $param_name.':"'.$v.'"';
		}
		
		$results = '{' . implode( ',', $properties ) . '}';
		return $results;
	}


	function map_roles( $value, $key ) {
		$this->roles[] = preg_replace( "/\|.*/", '', $value );
	}

	function prepare_directory_structure() {
		$required_dirs = array( '', 'flash', 'xml', 'templates' );

		foreach ( $required_dirs as $dir ) {
			$new_dir = $this->upload_path . $dir;
			$old_dir = $this->path . $dir;
			if ( ! file_exists( $new_dir ) )
				if ( ! mkdir( $new_dir ) )
					return FALSE;

			$this->chmod_recursive( $new_dir, 0775 );

			if ( !empty( $dir ) && file_exists( $old_dir ) )
				if (! $this->copy_dir( $old_dir, $new_dir ) )
					return FALSE;
		}

		return TRUE;
	}

	// beware: monster ahead
	function run_query( $action, $msg, $where = '' ) {
		global $ssp_params, $ssp_msg;

		if ( empty( $_POST['createThumbnails'] ) )
			$_POST['createThumbnails'] = 0;
		else
			$_POST['createThumbnails'] = 1;

		$sspName =	$this->db->escape( strtolower( sanitize_title_with_dashes( $_POST['sspName'] ) ) );

		// Create xml file for Manual Source Entry
		if ($_POST['xmlFileType'] == 'Manual Entry' && !empty($_POST['xmlManualSource'])) {
			$file = $this->upload_path . 'xml/' . $sspName . '.xml';
			$handle = fopen($file, 'w');
			fwrite($handle, stripslashes($_POST['xmlManualSource']));
			fclose($handle);
			$_POST['xmlFilePath'] = $this->upload_url . 'xml/' . $sspName . '.xml';
		}

		// Create thumbnails for WordPress Gallery source type
		if ($_POST['xmlFileType'] == 'WordPress Gallery' && $_POST['createThumbnails'] == '1') {
			if (!$this->update_thumbs($_POST['xmlFilePath'], $sspName, $_POST['navLinkPreviewSize'][0], $_POST['navLinkPreviewSize'][1]))
			{
				$ssp_msg = '<div class="ssp_error">Please enable write permission for the wp-content/upload folder to create thumbnails!</div>';
			}
		}
		$query = $action.' '.$this->table_name.' SET ';
		$query.= "sspGalleryId = '". $sspName ."', ";
		foreach ($ssp_params as $param => $value):

			/*
if ((isset($_POST[$param]) && $_POST[$param] === '' ) && ($param != 'xmlFilePath' || $_POST['xmlFileType'] != 'Manual Entry') && (!in_array($param, array('startContentID', 'startAlbumID')))) {
				unset($_POST['action']);
				$ssp_msg = '<div class="ssp_error">Sorry! Values cannot be blank, be sure to supply them all.</div>';
				return;
			}
*/
			if ($param == 'xmlManualSource' ) continue;
			# replace '#' with '0x' in color items
			if ($_POST[$param][0] == '#') $_POST[$param] = str_replace('#', '0x', $_POST[$param]);
			# check for unchecked checkboxes
			if (!isset($_POST[$param])) $_POST[$param] = strpos($param, 'Appearance') !== false ? 'Hidden' : 'Off';
			#captionHeaderText
			if (!isset($_POST[$param]) && $param == 'captionHeaderText')
				$_POST['captionHeaderText'] = '{imageTitle}';
			# merge array-like items
			if (is_array($_POST[$param])) $_POST[$param] = join(',', $_POST[$param]);
			# add staff to the query
			$query.= $param . " = '" . $this->db->escape(strip_tags(trim($_POST[$param]))) . "', ";
		endforeach;

		$query .= "active_style = '{$sspName}', ";

		$query .= "sspDescription = '{$_POST['sspDescription']}', ";
		
		

		$query = rtrim($query, ' ,');
		$query.= ' ' . $where;

		if ($action == "INSERT" && $this->db->get_results('SELECT id FROM ' . $this->table_name . ' WHERE sspName = "' . $_POST['sspName'] . '" OR sspGalleryId = "' . $sspName . '"')) {
			$ssp_msg = '<div class="ssp_error">The slideshow name you supplied already exists in the database.</div>';
		} else {
			$query_success = $this->db->query($query);
			if ($query_success)
			{
				if (!empty($ssp_msg))
					$ssp_msg = '<div class="ssp_msg">Gallery settings have been updated successfully, but thumbnails could not be created or updated because wp-content/uploads folder is not writable.</div>';
				else
					$ssp_msg = $msg;
					$ssp_msg .= "<script type='text/javascript'>var addedGallery = { sspName : '{$_POST['sspName']}', sspGalleryId : '{$sspName}' }</script>";
			}
			elseif ($query_success === 0) {
				if ($_POST['xmlFileType'] == 'WordPress Gallery' && $_POST['createThumbnails'] == '1')
				{
					if (empty($ssp_msg))
					$ssp_msg = '<div class="ssp_msg">Gallery thumbnails have been updated.</div>';
				}
				else
					$ssp_msg = '<div class="ssp_error">Gallery not updated. Seems like you haven\'t made any changes.</div>';
			}
			else {
				$ssp_msg = '<div class="ssp_error">Database Connection Error! ' . mysql_errno($this->db->dbh) . ': ' . mysql_error($this->db->dbh) . '<br />' . $query . '</div>';
			}
		}

	}

	function shortcode( $atts, $content = NULL ) {
		extract( shortcode_atts( array(
			'thickbox' => NULL,
			'lightview' => NULL,
			'gallery' => NULL,
			'title' => NULL,
			'image' => NULL,
		), $atts ) );

		if ( !is_null( $thickbox ))
		{
			return $this->display_gallery( $thickbox, 'thickbox', FALSE, $content, $title, $image );
		}

		if ( !is_null( $lightview ) )
		{
			return $this->display_gallery( $lightview, 'lightview', FALSE, $content );
		}

		if ( !is_null( $gallery ) )
		{
			return $this->display_gallery( $gallery, 'template' , FALSE, $content );
		}
	}

	function update_database( $current_version ) {
		
		// if gallery table doesn't exist, create it. otherwise, update
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $this->dbsql );
		
		$update_queries = array(
			'1.2.1' => array(
				"UPDATE {$this->table_name} SET mediaPlayerButtonColor = '0xFFFFFF', mediaPlayerButtonShadowAlpha = '.4', navButtonInactiveAlpha = '.4', navButtonShadowAlpha = '.4', sspDescription = ''",
			),
			'1.3.7' => array(
				"UPDATE {$this->table_name} SET captionTextShadowAlpha = '0'",
				"UPDATE {$this->table_name} SET galleryContentShadowAlpha = '0'",
				"UPDATE {$this->table_name} SET navButtonGlowAlpha = '.25'",
				"UPDATE {$this->table_name} SET navButtonGradientAlpha = '.6'",
				"UPDATE {$this->table_name} SET navButtonRolloverColor = '0xFFFFFF'",
				"UPDATE {$this->table_name} SET navButtonShadowStyle = 'Under'",
				"UPDATE {$this->table_name} SET navLinkAnimate = 'On'",
				"UPDATE {$this->table_name} SET navLinkPreviewShadowAlpha = '.6'",
				"UPDATE {$this->table_name} SET navLinksBackgroundShadowAlpha = '0'",
				"UPDATE {$this->table_name} SET soundEffectsVolume = '.2'",
				"UPDATE {$this->table_name} SET navLinkActiveColor = '0xEEEEEE'",
				"UPDATE {$this->table_name} SET navLinkInactiveColor = '0x999999'",
				"UPDATE {$this->table_name} SET navLinkShadowAlpha = '.6'",
			),
			'1.3.8' => array(),
			'1.3.9' => array(),
			'1.4.0' => array(
				"UPDATE {$this->table_name} SET navAppearance = 'Always Visible' WHERE navAppearance = 'Hidden When Gallery Open'",
				"UPDATE {$this->table_name} SET captionHeaderTextColor = '0xEEEEEE'",
				"UPDATE {$this->table_name} SET loop = 'Off'",
				"UPDATE {$this->table_name} SET permalinks = 'Off'",
				),
			'1.4.1' => array(
				
				),
				
			'1.4.2' => array(
				"UPDATE {$this->table_name} SET captionHeaderText = '{imageTitle}' WHERE captionHeaderText = 'Off'",
				),
			'1.4.3' => array(
				"UPDATE {$this->table_name} SET captionHeaderText = '{imageTitle}' WHERE captionHeaderText = ''",
				"UPDATE {$this->table_name} SET captionHeaderPadding = '6,6,2,6' WHERE captionHeaderPadding = ''",
				"UPDATE {$this->table_name} SET navAppearance = 'Always Visible' WHERE navAppearance = 'Hidden When Gallery Open'",
				"UPDATE {$this->table_name} SET captionHeaderTextColor = '0xEEEEEE' WHERE captionHeaderTextColor = ''",
				"UPDATE {$this->table_name} SET loop = 'Off' WHERE loop = ''",
				"UPDATE {$this->table_name} SET permalinks = 'Off' WHERE permalinks = ''",
				),
			'1.4.4' => array(
				"UPDATE {$this->table_name} SET captionHeaderPadding = '6,6,2,6'",
				),

		);

		while ( list( $version, $queries ) = each( $update_queries ) )
		{
			if ( version_compare( $version, $current_version, '>=' ) ) {
				foreach ( $queries as $query )
				{
					$this->db->query( $query );
				}
			}
		}
	}
	
	function update_thumbs( $post_id, $gallery_name, $width, $height ) {
		$images     = get_children( array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );
		$upload_dir = $this->get_wp_upload_path();
		$thumbs_dir = trailingslashit( $upload_dir['dir'] ) . $gallery_name. '_thumbs';
		if ( ! is_writeable( trailingslashit( $upload_dir['dir'] ) ) ) {
			return FALSE;
		}

		if ( ! is_dir( $thumbs_dir ) ) {
			mkdir( $thumbs_dir );
		}
		if ( ! empty( $images )) {
			foreach ( $images as $iid => $image ) {
				image_resize( get_attached_file( $iid ), $width, $height, false, 'thumb', $thumbs_dir );
			}
		}

		return TRUE;
	}
	
}

global $slidepress, $ssp_msg;
$slidepress = new SlidePress();
// template tag
function slidepress_display_gallery( $sspGalleryId, $template = 'template', $echo = TRUE, $content = NULL, $sspGalleryTitle = NULL, $sspGalleryImage = NULL ) {
	global $slidepress;
	$slidepress->display_gallery( $sspGalleryId, $template, $echo, $content, $sspGalleryTitle, $sspGalleryImage );
}