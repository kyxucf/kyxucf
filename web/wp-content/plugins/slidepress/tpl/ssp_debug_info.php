<div class="wrap ssp_wrap">   
	
	<br class="clear"/>
	
	<?php include_once( 'ssp_header.php' ); ?> 
	
	<?php include_once( 'ssp_subnav.php' ); ?>
	
	<?php if (isset($_POST['submit'])): ?>
		<?php if ($mail_result): ?>
			<div class="ssp_msg">Report sent successfully!</div>
		<?php else: ?>
			<div class="ssp_error">Report delivery failed, please try manually sending an email.</div>
		<?php endif; ?>
	<?php endif; ?>
	<h2>Bug Report</h2>
	<p>The information presented below is used to diagnose any problems you might encounter with SlidePress. Please be sure to check our <a href="http://wiki.slideshowpro.net/SSPsa/SP-SlidePress" title="Help documentation" target="_blank">help documentation</a>, as well as the <a href="http://forums.slideshowpro.net" title="Support forums" target="_blank">Support Forums on our website</a> for more information and helpful tips.</p>
	<p>You can click the "Submit Report" button below to submit it, or if that doesn't work, copy and paste the text below into an email and <a href="mailto:support@slideshowpro.net" title="Support email")>send to us</a>.</p>
<form method="post">
	<p><textarea name="description" cols=70 rows=5><?php echo (isset($_POST['description'])) ? $description : "Type your bug description here"; ?></textarea></p>
	<p><textarea name="report" readonly="readonly" cols="70" rows="20"><?php foreach (array_keys($params) as $section ): ?>
<?php echo $section."\n"; ?>---------------------
<?php if (is_array($params[$section])):?>
<?php foreach ($params[$section] as $param => $value):?>
<?php echo strtoupper($param);?>: <?php echo htmlentities($value)."\n"; ?>
<?php endforeach; ?>
<?php else: ?>
<?php echo $params[$section]."\n"; ?>
<?php endif; ?>
---------------------
<?php endforeach; ?></textarea>
</p>
<p class="submit">
	<input type="submit" name="submit" value="Submit Report" />
	<?php wp_nonce_field('ssp_submit_report'); ?>
</p>
</form>
</div>