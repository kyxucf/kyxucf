<!-- Begin slideshow -->
<?php 
		$slideshowcat = get_option('slideshowcat');
		$my_query = new WP_Query("category_name=$slideshowcat&showposts=10"); ?>
<ul id="portfolio">
<?php while ($my_query->have_posts()) : $my_query->the_post();
		$do_not_duplicate = $post->ID; ?>
<?php
// this gets the image filename
$values = get_post_custom_values("slideshow");
// this checks to see if an image exists
if (isset($values[0])) {
?><li>
<a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><img class="slideshow" src="<?php $key="slideshow"; echo get_post_meta($post->ID, $key, true); ?>" alt="<?php the_title(); ?>" /></a>
</li><?php } ?> 
<?php endwhile; wp_reset_query(); ?>
</ul>