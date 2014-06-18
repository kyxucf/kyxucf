<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

# help page
?>
<div class="wrap ssp_wrap">
	
	<br class="clear"/>  
	
	<?php include_once( 'ssp_header.php' ); ?> 
	
	<?php include_once( 'ssp_subnav.php' ); ?>

	<h2>Help</h2>

	<p>
		All SlidePress documentation is at <a href="http://wiki.slideshowpro.net/SlidePress/SlidePress" title="SlidePress Help Documentation" target="_blank">wiki.slideshowpro.net</a>.
		</p>

</div>
