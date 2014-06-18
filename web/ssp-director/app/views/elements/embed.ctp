<?php e($director->preDialogue('publish-options', false, 460)); ?>
				
	<h1><?php __('Publish'); ?> <span class="publish-type"></span></h1>
	
	<p><?php printf(__('Display "%s" as a slideshow on your web site or copy its data for use in the SlideShowPro Player.', true), '<span class="publish-title"></span>'); ?></p>
	
	<div class="dialogue-spacer" style="margin-bottom:15px;">
		<h4 class="icon_label_slideshow icon_mid"><?php __('Embed slideshow'); ?></h4>
		<button class="primary_sm" onclick="open_embed_dialogue(); Messaging.kill('publish-options');" style="float:right;" title="<?php __('Embed'); ?>"><?php __('Embed'); ?></button>
		<?php __('Supports Flash, iPhone, iPad and Android.'); ?>
	</div>
	
	<div class="dialogue-spacer">
		<h4 class="icon_label_link icon_mid"><?php __('Copy XML file path'); ?></h4>
		<?php $pid = 'publish-path'; ?>
		<div id="<?php e($pid); ?>" style="float:right;"><textarea class="publish-path" id="<?php e($pid); ?>_tocopy" style="display:none;"></textarea><button id="<?php e($pid) ?>_button" type="button" title="<?php __('Copy'); ?>" class="primary_sm"><?php __('Copy'); ?></button><div id="<?php e($pid) ?>_target" style="position:absolute;top:0;left:0;z-index:1500;"></div></div>

		<code class="publish-path"></code>
	</div>
	
	<span class="messenger-bttns">
		<button onclick="Messaging.kill('publish-options');" class="primary_lg" title="<?php __('Done'); ?>"><?php __('Done'); ?></button>
	</span>
	
<?php e($director->postDialogue()); ?>

<?php e($director->preDialogue('embed-code', false, 840)); ?>
	
	<div style="height:32px;line-height:32px;margin-bottom:15px;vertical-align:baseline;">
	<h1 class="slideshow" style="float:left;padding:0 15px 0 42px;margin:0;"><?php __('Embed slideshow'); ?></h1>
	<p style="margin:0;padding:0;line-height:36px;"><?php printf(__('Display a slideshow of this %s on your web site.', true), '<span class="publish-type"></span>'); ?></p>
	</div>
	
	
	<div class="slideshow-embed">
		
		<ul class="dial-nav">
			<li id="embed-tab-slideshow" class="active"><a href="#" onclick="return false;" title="<?php __('Slideshow') ?>"><?php __('Slideshow') ?></a></li>
			<li id="embed-tab-poster"><a href="#" onclick="return false;" title="<?php __('Mobile poster') ?>"><?php __('Mobile poster') ?></a></li>
		</ul>
		
		<script type="text/javascript" charset="utf-8">
			$$('ul.dial-nav')[0].observe('click', function(e) {
				var e = $(e.element().parentNode.id);
				if (!e.hasClassName('active')) {
					$$('ul.dial-nav li.active')[0].removeClassName('active');
					e.addClassName('active');
					var tgt = e.id.split('-tab-')[1];
					$$('div.slideshow-embed-panes').each(function(d) {
						d.hide();
					});
					$('slideshow-embed-' + tgt + '-preview').show();
					if (tgt == 'poster') {
						load_poster();
					}
				}
				return false;
			});
		</script>
		<div class="slideshow-embed-w">
			
			<div class="slideshow-embed-panes" id="slideshow-embed-poster-preview" style="display:none">
				
				<div class="slideshow-embed-l" id="slideshow-embed-poster-target"></div>
			
				<div class="slideshow-embed-r">
				
					<div class="slideshow-embed-options">
						
						<h4><?php __('Mobile poster options') ?></h4>
						
						<?php
							$poster_folder = new Folder(ROOT . DS . 'm' . DS . 'posters');
							$posters = array_pop($poster_folder->ls(true, true));
						?>
						
						<fieldset>
							<label class="inline"><?php __('Style') ?>:</label>&nbsp;&nbsp;<select id="poster-style" onchange="update_embed_code(false);load_poster(true);">
								<?php foreach($posters as $t):
									$v = str_replace('.js', '', $t);
									$display = ucfirst(str_replace('-', ' ', $v));
								?>
								<option value="<?php e($v); ?>"<?php e(ife($display == 'Default', ' selected="selected"')); ?>><?php e($display); ?></option>
								<?php endforeach; ?>
							</select>
						</fieldset>
												
						<fieldset>
						<h4><?php __('About mobile posters') ?></h4>
							<?php printf(__('Mobile posters automatically replace the Flash slideshow on iOS (iPhone, iPad, iPod Touch) and Android devices. They provide a link to view the same slideshow content in a special HTML5 mobile interface designed for their device. Information on how to create your own poster can be found %s.', true), '<a href="http://wiki.slideshowpro.net/SSPdir/Tips-EditSlideshowOptions" target="_blank">' . __('in our wiki', true) . '</a>'); ?>
						</fieldset>
						<?php if (!strpos(strtolower(env('HTTP_USER_AGENT')), 'webkit')): ?>
						<fieldset>
							<p class="warn"><strong><?php __('Note') ?>:</strong> <?php __('Posters are optimized for iOS and Android web browsers. Your browser is not capable of rendering some styles. Use Safari or Chrome for most accurate preview.') ?></p>
						</fieldset>
						<?php endif; ?>
					</div>
				
				</div>
			</div>
		
			<div class="slideshow-embed-panes" id="slideshow-embed-slideshow-preview">

				<div class="slideshow-embed-l">
		
					<div id="embed-preview">
						<div id="fc"></div>
					</div>
		
				</div>
		
				<div class="slideshow-embed-r">
		
	
					<?php
						$themes_folder = new Folder(ROOT . DS . 'm' . DS . 'params');
						$themes = array_pop($themes_folder->ls(true, true));
						
						$themes_folder = new Folder(ROOT . DS . 'plugins' . DS . 'params');
						$user_themes = array_pop($themes_folder->ls(true, true));
					?>
	                     
	
					<div class="slideshow-embed-options">
				
						<h4><?php __('Slideshow options') ?></h4>
				
									
							<fieldset>
								<label class="inline"><?php __('Size') ?>:</label>&nbsp;&nbsp;<input class="small" onkeyup="update_embed_code(false);" id="embed-w" type="text" size="2" value="550" />&nbsp;&nbsp;x&nbsp;&nbsp;<input class="small" onkeyup="update_embed_code(false);" id="embed-h" type="text" size="2" value="400" />&nbsp;&nbsp;px
							</fieldset>
					
							<fieldset>
								<label class="inline"><?php __('Style') ?>:</label>&nbsp;&nbsp;<select id="embed-style" onchange="update_embed_code(true);">
									<?php foreach($themes as $t): if ($t != 'single-video.xml'):
										$display = ucfirst(str_replace('-', ' ', str_replace('.xml', '', $t)));
									?>
									<option value="/m/params/<?php e($t); ?>"<?php e(ife($display == 'Default', ' selected="selected"')); ?>><?php e($display); ?></option>
									<?php endif; endforeach; ?>
									<?php if (!empty($user_themes)): ?>
										<optgroup label="<?php __('Custom styles') ?>">
									<?php foreach($user_themes as $t):
										if (!fnmatch('*.xml', $t)) { continue; }
										
										$display = ucfirst(str_replace('-', ' ', str_replace('.xml', '', $t)));
									?>
										<option value="/plugins/params/<?php e($t); ?>"<?php e(ife($display == 'Default', ' selected="selected"')); ?>><?php e($display); ?></option>
									<?php endforeach; ?>
										</optgroup>
									<?php endif; ?>
								</select>
							</fieldset>
				
				

						<fieldset>
						<label class="inline"><?php __('Content scale') ?>:</label>&nbsp;&nbsp;<select id="content-scale" onchange="update_embed_code(true);">
						   	<option value="Downscale Only"><?php __('Downscale Only') ?></option>
							<option value="Proportional"><?php __('Proportional') ?></option>
							<option value="Crop to Fit"><?php __('Crop to Fit') ?></option>
							<option value="Crop to Fit All"><?php __('Crop to Fit All') ?></option>
						</select>
					</fieldset>
				
						<fieldset>
							<label class="inline"><?php __('Transition') ?>:</label>&nbsp;&nbsp;<select id="transition" onchange="update_embed_code(true);">
								<option value="Blur"><?php __('Blur') ?></option>
								<option value="Cross Fade" selected="selected"><?php __('Cross Fade') ?></option>
								<option value="Fade to Background"><?php __('Fade to Background') ?></option>
								<option value="Dissolve"><?php __('Dissolve') ?></option>
								<option value="Drop"><?php __('Drop') ?></option>
								<option value="Lens"><?php __('Lens') ?></option>
								<option value="Photo Flash"><?php __('Photo Flash') ?></option>
								<option value="Push"><?php __('Push') ?></option>
								<option value="Wipe"><?php __('Wipe') ?></option>
								<option value="Wipe and Fade"><?php __('Wipe and Fade') ?></option>
								<option value="Wipe to Background"><?php __('Wipe to Background') ?></option>
								<option value="Wipe and Fade to Background"><?php __('Wipe and Fade to Background') ?></option>
							</select> for <select id="duration" onchange="update_embed_code(true);">
								<option value="0.5">0.5 sec</option>
								<option value="1">1 sec</option>
								<option value="2" selected="selected">2 sec</option>
								<option value="3">3 sec</option>
								<option value="4">4 sec</option>
							</select>
						</fieldset>

						<fieldset>
							<label class="inline"><?php __('Show each item for') ?>:</label>&nbsp;&nbsp;<select id="pause" onchange="update_embed_code(true);">
								<option value="1">1 sec</option>
								<option value="2">2 sec</option>
								<option value="3">3 sec</option>
								<option value="4" selected="selected">4 sec</option>
								<option value="5">5 sec</option>
								<option value="6">6 sec</option>
								<option value="7">7 sec</option>
								<option value="8">8 sec</option>
								<option value="9">9 sec</option>
								<option value="10">10 sec</option>
							</select>
						</fieldset>

						<fieldset>
							<label class="inline"><?php __('Preloader') ?>:</label>&nbsp;&nbsp;<select id="preloader" onchange="update_embed_code(true);">
								<option value="Hidden"><?php __('Hidden') ?></option>
								<option value="Bar"><?php __('Bar') ?></option>
								<option value="Beam" selected="selected"><?php __('Beam') ?></option>
								<option value="Line"><?php __('Line') ?></option>
								<option value="Pie"><?php __('Pie') ?></option>
								<option value="Pie Spinner"><?php __('Pie Spinner') ?></option>
								<option value="Spinner"><?php __('Spinner') ?></option>
							</select>
						</fieldset>
				
						<fieldset>
							<table cellspacing="0" cellpadding="0">
								<tr>
									<td class="first check">
										<input id="playback" type="checkbox" checked="checked" onchange="update_embed_code(true);" /> 
									</td>
									<td>
										<label for="playback" class="inline"><?php __('Auto playback') ?></label>
									</td>
									<td class="check">
										<input id="gallery" type="checkbox" onchange="update_embed_code(true);" /> 
									</td>
									<td class="last">
										<label for="gallery" class="inline"><?php __('Open gallery') ?></label>
									</td>
								</tr>
								<tr>
									<td class="check first">
										<input id="pan-zoom" type="checkbox" onchange="update_embed_code(true);" />
									</td>
									<td>
										<label for="pan-zoom" class="inline"><?php __('Pan zoom') ?></label>
									</td>
									<td class="check">
										<input id="auto-start" type="checkbox" checked="checked" onchange="update_embed_code(true);" />
									</td>
									<td class="last">
										<label for="auto-start" class="inline"><?php __('Auto start videos') ?></label>
									</td>
								</tr>
								<tr class="last">
									<td class="first check">
										<input id="nav-roll" type="checkbox" onchange="update_embed_code(true);" /> 
									</td>
									<td>
										<label for="nav-roll" class="inline"><?php __('Mouseover navigation') ?></label>
									</td>
									<td class="check">
										<input id="thumb-links" type="checkbox" onchange="update_embed_code(true);" checked="checked" />
									</td>
									<td class="last">
										<label for="thumb-links" class="inline"><?php __('Thumbnail links') ?></label>
									</td>
								</tr>
							</table>
							</fieldset>

				
					</div>
				
				</div>
			
			</div>
		
		</div>
		
	</div>
	
	<br clear="all" />
	
	<?php $eid = 'embed' ?>
	
	<span class="messenger-bttns" style="margin-top:0;"><div id="<?php e($eid); ?>" style="float:right;"><textarea id="<?php e($eid); ?>_tocopy" style="display:none;"></textarea><button id="<?php e($eid) ?>_button" type="button" title="<?php __('Copy'); ?>" class="primary_lg"><?php __('Copy embed code'); ?></button><div id="<?php e($eid) ?>_target" style="position:absolute;top:0;left:0;z-index:1500;"></div></div><button onclick="Messaging.kill('embed-code');" class="primary_lg nudgeR" title="<?php __('Done') ?>" style="float:right"><?php __('Done') ?></button></span> 
	
	<div style="font-size:9px;height:24px;line-height:24px;">
		<?php printf(__('Additional slideshow options are available by %s.', true), '<a href="http://wiki.slideshowpro.net/SSPdir/Tips-EditSlideshowOptions" title="' . __('editing the player embed code', true) . '" target="_blank">' . __('editing the player embed code', true) . '</a>'); ?>
	</div>

<script type="text/javascript" charset="utf-8">
	var embed = {
		host: '<?php e(DIR_HOST); ?>',
		init: false
	};
</script>
<?php e($director->postDialogue()); ?>