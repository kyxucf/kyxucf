<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

    <title><?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ); ?></title>

    <meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
    <meta name="title" content="<?php wp_title( '-', true, 'right' ); echo wp_specialchars( get_bloginfo('name'), 1 ); ?>" />
	<meta name="description" content="<?php bloginfo('description') ?>" />
	<?php

   include('classes/DirectorPHP.php');
   $director = new Director(DIRECTOR_API_KEY, 'kyxucf.com/ssp-director');
$scope = array('album', 4);
   $recently_taken = $director->content->all(array('scope' => $scope));

   foreach($recently_taken as $image) {
      echo '<link rel="image_src" href="' . $image->original->url . '" />';
   }

?>
	<?php if(is_search()) { ?>
	<meta name="robots" content="noindex, nofollow" />
    <?php }?>

<!-- Styles  -->
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/screen.css" type="text/css" media="screen, projection" />
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/print.css" type="text/css" media="print" />
	<!--[if IE]><link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/ie.css" type="text/css" media="screen, projection" /><![endif]-->
	<!--[if lte IE 7]><link type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/library/styles/ie-nav.css" rel="stylesheet" media="all" /><![endif]-->
	<?php //Load Variables
  $css = get_option('T_background_css');
	?>
	<?php if ($css == 'Enabled') {?>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/library/functions/style.php" type="text/css" media="screen, projection" />
	<?php } ?>

	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<script type="text/javascript" src="http://kyxucf.com/swfobject/swfobject2.js"></script>
		<script type="text/javascript">
<!--
function flashPutHref(href) { location.href = href; }
-->
</script>

		<script type="text/javascript">
			var flashvars = {};
			var params = {};
			params.wmode = "transparent";
			params.allowfullscreen = "true";
			var attributes = {};
			attributes.id = "mainswf";
			swfobject.embedSWF("<?php bloginfo('template_url'); ?>/flash/mainpage/mainpage.swf", "mainswf", "960", "850", "9.0.15", "http://kyxucf.com/swfobject/expressInstall.swf", flashvars, params, attributes);
		</script>
        <script type="text/javascript">
			var flashvars = {};
			var params = {};
			params.wmode = "transparent";
			params.allowfullscreen = "true";
			var attributes = {};
			attributes.id = "sidebarswf";
			swfobject.embedSWF("<?php bloginfo('template_url'); ?>/flash/sidebar/sidebar.swf", "sidebarswf", "295", "518", "9.0.15", "http://kyxucf.com/swfobject/expressInstall.swf", flashvars, params, attributes);
		</script>
		<script type="text/javascript">
			var flashvars = {
			initialURL: escape(document.location)
		}
			var params = {};
			params.wmode = "transparent";
			params.allowfullscreen = "true";
			var attributes = {};
			attributes.id = "photosswf";
			swfobject.embedSWF("<?php bloginfo('template_url'); ?>/flash/photos/photossimpler.swf", "photosswf", "640", "616", "9.0.15", "http://kyxucf.com/swfobject/expressInstall.swf", flashvars, params, attributes);
		</script>
		<style>
				 @import url("<?php bloginfo('stylesheet_directory'); ?>/custom.css");
		</style>

</head>

<body>
<div id="top">
<div class="left">
<a href="<?php echo get_settings('home'); ?>/" title="Home" class="logo"><img src="<?php bloginfo('template_url'); ?>/images/crest65.png" alt="kyx-crest" /></a>
</div>
<!-- Begin Masthead -->
<div id="masthead">
 <h4 class="left"><a href="<?php echo get_settings('home'); ?>/" title="Home" class="logo"><?php bloginfo('name'); ?></a> <span class="description"><?php bloginfo('description'); ?></span></h4>
</div>

<?php include (TEMPLATEPATH . '/nav.php'); ?>

<div class="clear"></div>
</div>

<div class="container">
<div class="container-inner">
