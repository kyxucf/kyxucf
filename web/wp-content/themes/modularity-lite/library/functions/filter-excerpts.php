<?php

// Add filter to the_excerpt
	add_filter('the_excerpt', 'my_excerpts');
	
	function my_excerpts($content = false) {

// If is the home page, an archive, or search results
	if(is_front_page() || is_archive() || is_search()) :
		global $post;
		$content = $post->post_excerpt;

	// If an excerpt is set in the Optional Excerpt box
		if($content) :
			$content = apply_filters('the_excerpt', $content);

	// If no excerpt is set
		else :
			$content = $post->post_content;
			$content = strip_shortcodes($content);
			$content = str_replace(']]>', ']]&gt;', $content);
			$content = strip_tags($content);
			$excerpt_length = 60;
			$words = explode(' ', $content, $excerpt_length + 1);
			if(count($words) > $excerpt_length) :
				array_pop($words);
				array_push($words, '&rarr;');
				$content = implode(' ', $words);
			endif;
			$content = '<p>' . $content . '</p>';

		endif;
	endif;

// Make sure to return the content
	return $content;

}

?>