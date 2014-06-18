<?php

	$displayName = __('Show location on map (requires GPS data)', true);
	$template = 'http://maps.google.com/maps?z=17&q=loc:[exif:latitude] [exif:longitude]';
	$target = 1;
	$diff_with = 'http://maps.google.com/maps?z=17&q=loc: ';

?>