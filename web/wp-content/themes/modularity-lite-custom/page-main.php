<?php
/*
Template Name: Main Page
*/
?>
<?php get_header(); ?>
<div class="span-24 last">
<div class="content">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div id="mainswf">Hello, you either have JavaScript turned off or an old version of Adobe's Flash Player.<br />
<a href="http://www.adobe.com/go/getflashplayer/">Get the latest Flash player</a>.</div>
<div class="clear"></div>
		<div class="post" id="post-<?php the_ID(); ?>">
			
			<?php include (THEMELIB . '/apps/multimedia.php'); ?>
			<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>
			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
		</div>
        <div class="clear"></div>
		<?php endwhile; endif; ?>
	<?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
	</div>
	</div>
<?php get_footer(); ?>