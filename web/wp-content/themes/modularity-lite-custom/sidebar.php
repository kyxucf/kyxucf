<div class="span-8 last">
	<div id="sidebar">
	<div id="sidebarswf">Hello, you either have JavaScript turned off or an old version of Adobe's Flash Player.<br />
<a href="http://www.adobe.com/go/getflashplayer/">Get the latest Flash player</a>.</div>
<div class="clear"></div>
	<?php //Load Variables
  	$sidebar_state = get_option('T_sidebar_state'); 
	?> 
    

		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) : ?>
		<?php endif; ?>
		
	</div>
</div>