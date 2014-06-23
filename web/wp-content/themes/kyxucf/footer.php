<div class="clear"></div>
</div>
</div>
<div id="footer-wrap">
<div id="footer">
<div class="span-12 append-1 small">
</div>
<div class="column span-11 small last" style="text-align:right">
<p class="quiet">
		All content &copy; <?php echo date("Y"); ?> by <?php bloginfo('name'); ?><br />
        Questions or Errors regarding website, email the <a href="mailto:webmaster@kyxucf.com">webmaster</a>.<br />
        The Lambda Chapter is part of <a href="http://www.kyx.org/" target="_blank">Kappa Upsilon Chi</a><br />
        <!-- Call us at (407) 2GET-KYX -->
		<!-- <?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. -->
</p>
</div>
<div class="clear"></div>
</div>
</div>
<?php wp_footer(); ?>
<?php
	$tmp_tracking_code = get_option('T_tracking_code');
	if($tmp_tracking_code != ''){
		echo stripslashes($tmp_tracking_code);
	}
?>
</body>
</html>