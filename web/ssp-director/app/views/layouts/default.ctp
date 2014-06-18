<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
		<title>SlideShowPro Director &raquo; <?php e($title_for_layout); ?></title>
		<script type="text/javascript" charset="utf-8">
			var base_url = '<?php e($html->url('/')); ?>';
		</script>
		<?php
			// Grab CSS and JS files
			e($asset->css('base'));

			// Apply theme CSS
			if (isset($user['theme'])) {
				e($director->css($user['theme']));
			} else if (isset($account)) {
			    e($director->css($account['Account']['theme']));
			} else {
				e($director->css('/styles/default/default.css'));
			}
			
			/// Bring in extra sheets if necessary
			$agent = env('HTTP_USER_AGENT');
			if (strpos($agent, 'MSIE 7.0') !== false):
				e($html->css('ie7.css?' . DIR_VERSION) . "\n");
			elseif (strpos($agent, 'MSIE 6.0') !== false):
				e($html->css('ie6.css?' . DIR_VERSION) . "\n");
			elseif (strpos($agent, 'Firefox') !== false):
				e($html->css('firefox.css?' . DIR_VERSION) . "\n");
			endif;
			
			if (isset($javascript)):
				e('<script type="text/javascript" src="' . DIR_HOST . '/index.php?/js/translate/' . $user['lang'] . '"></script>');
				e($asset->js('base'));
			endif;
			
		?>
			<?php if (isset($load_maps_js)): ?>
			<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
			<?php endif; ?>
			<script type="text/javascript" src="<?php e(DIR_HOST) ?>/m/embed.js"></script>
	</head>
	
	<body onload="init();">
		<?php if (TRIAL_STATE): ?>
			<div id="top-message">
				<div class="msg-<?php echo TRIAL_STATE > 1 ? 'warn' : 'update' ?>">
					<div class="icon"></div>
					<div id="radius-warn">
						<?php if (TRIAL_STATE == 3): ?>
							<?php __('You have uploaded your limit of 50 items.'); ?>
						<?php elseif (TRIAL_STATE == 2): ?>
							<?php __('You are nearing your limit of 50 items.'); ?>
						<?php else: ?>
							<?php __('Welcome to the SlideShowPro Director trial, which is limited to 50 uploads.'); ?>
						<?php endif; ?>
						
						<?php printf(__('%s to unlock Director.', true), $html->link(ucfirst(__('click here', true)), '/accounts/activate/edit', array('class' => 'pill'))); ?>
					</div>
				</div>
			</div>
		<?php elseif (isset($version_link) && AUTO_UPDATE): ?>
			<div id="top-message" style="display:none">
				<div class="msg-update">
					<div class="icon"></div>
					<div id="radius-warn">
						<?php printf(__('A new version of SlideShowPro Director is available. %s', true), '<a href="#" class="pill" onclick="Messaging.dialogue(\'update\'); return false">' . ucfirst(__('click here for more information', true)) . '</a>'); ?>
					</div>
				</div>
			</div>
			<script type="text/javascript">var show_warn = true;</script>
		<?php endif; ?>
		
		<?php e($this->element('messenger')); ?>
		
		<div id="helper" style="display:none;">
			<p>&nbsp;</p>
		</div>

		<div id="dummy" style="display:none;"></div>
				
		<?php e($this->renderElement('header')); ?>
	
		<?php echo $content_for_layout ?>
		
		<?php e($this->renderElement('footer')); ?>
				
		<div id="clip-container"></div> 
		
		<script type="text/javascript"> 
			var clip_path = '<?php e($this->webroot . 'swf/clipboard.swf?' . md5(DIR_VERSION)); ?>';      
			var flashvars = {}
			var params = {  
				allowScriptAccess: "always",
				wmode: "transparent"      
			}
			var attributes = {
				id: "_clip"
			}
			swfobject.embedSWF(clip_path, "clip-container", 1, 1, "8", false, flashvars, params, attributes);
		</script>
		
		<?php e($this->element('embed')); ?>
	</body>
</html><?php exit; ?>