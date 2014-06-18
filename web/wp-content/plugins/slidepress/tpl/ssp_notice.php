<?php

# cannot call this file directly
if ( strpos( basename( $_SERVER['PHP_SELF']) , __FILE__ ) !== false ) exit;

# notice page
?>
<div class="wrap">
	<br class="clear"/>
	<h2><?php echo $ssp_notice_h2;?></h2>
	
	<?php if (!is_null($ssp_msg)) echo $ssp_msg; ?>
	
	<br class="clear"/> 
	<p><?php echo $ssp_notice_p;?></p>
	<br class="clear"/> 
</div>