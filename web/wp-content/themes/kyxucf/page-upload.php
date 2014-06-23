<?php
/*
Template Name: Upload
*/
?>
<?php get_header(); ?>
<div class="span-<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			echo "17 colborder home";
		}
		else {
			echo "24 last";
		}
		?>">
<div class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<!-- in-a-flash Uploader -->
			<?php
require_once 'uploader/class.FlashUploader.php'; IAF_display_js(); $uploader = new FlashUploader('uploader', 'uploader/uploader',
'http://kyxucf.com/uploader/uploader-debug.php');
$uploader->set(ÔbgcolorÕ, Ô0x000000Õ);
$uploader->display();
?>
			<?php include (THEMELIB . '/apps/multimedia.php'); ?>
			<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>
		<?php endwhile; endif; ?>
        <div class="clear"></div>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>
	</div>
	<?php
		$sidebar_state = get_option('T_sidebar_state');

		if($sidebar_state == "On") {
			get_sidebar() ;
		}
		else {
			echo "";
		}
		?>
<!-- Begin Footer -->
<?php get_footer(); ?>