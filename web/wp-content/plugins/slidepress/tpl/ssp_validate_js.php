<?php //TODO rewrite this validate thing all over again ?>
<script>
		jQuery(document).ready(function() {
			// bind form validation
			jQuery('#ssp_form').bind('submit', function(event) {
				var checkResult = true;
				var error='<div class="ssp_error" id="ssp_error"><strong>Please fill in required fields to proceed!</strong><br/>';
				for (var i=0; i<this.elements.length; i++ ) {
					
					if (	(this.elements[i].type == 'text' 					&& 
							this.elements[i].className != 'ssp_colorpicker') 	|| 
							this.elements[i].name == 'xmlFileType' ) 
					{
						if (jQuery.trim(jQuery(this.elements[i]).val()) == '') {
							if (this.elements[i].name == 'xmlFileType') {
								error += 'You must select XML Type!<br />';	
								checkResult = false;
							}
							if ((this.elements[i].name == 'xmlFilePath' && !jQuery(this.elements[i]).hasClass('hidden')) || (this.elements[i].name != 'xmlFilePath') && (this.elements[i].name != 'startAlbumID') && (this.elements[i].name != 'startContentID') && (this.elements[i].name !='xmlExportName') && (this.elements[i].name != 'captionHeaderText')) {
								error += jQuery(this.elements[i]).parent().parent().find('label:first').text() + ' empty!<br />';
								jQuery(this.elements[i]).parent().parent().children().css('backgroundColor', '#FFFAD4');
								checkResult = false;
							}
						} else if ( /[\?@!\$%\^&\*\(\)'"\\\/\{\}\[\];\|~`+=]/.test(this.elements[i].value) ) {
							if (this.elements[i].name == 'xmlFilePath' || this.elements[i].name == 'panZoomScale' || this.elements[i].name == 'captionHeaderText' ) continue;
							error+=jQuery(this.elements[i]).parent().parent().find('label:first').text() +' includes invalid characters (?@!$%\^&\*\(\)\'"\/{}[];\|~`+=\)!<br />';
							jQuery(this.elements[i]).parent().parent().children().css('backgroundColor', '#FFFAD4');
							checkResult = false;
						} else {
							if (this.elements[i].name != 'xmlFileType') 
								jQuery(this.elements[i]).parent().parent().children().css('backgroundColor', '#EFF5FA'); 
						}
					} else if (this.elements[i].name == 'xmlManualSource' && jQuery('#xmlFilePath').hasClass('hidden') && this.elements[i].value == '') {

            error += jQuery(this.elements[i]).parent().parent().find('label:first').text() + ' empty!<br />';
						jQuery(this.elements[i]).parent().parent().children().css('backgroundColor', '#FFFAD4');
						checkResult = false;
					}
				}
				
				if (wpGalleryError) {
					error += "SlideShow XML Source should be a the <strong>post ID</strong> of the post containing the gallery.<br />";
					checkResult = false;
					jQuery('#xmlFilePath').parents('tr').children().css('backgroundColor', '#FFFAD4');
				}
				
				if (!checkResult) {
					error += '</div>';	
					jQuery('#ssp_error').remove();
					jQuery('.ssp_wrap:first').find('h2:first').after(error);
					window.location = '#ssp_error';
				}
				if (checkResult) jQuery('#ssp_form').find('input, select').each(function(i) {
					this.disabled = false;
				});
				return checkResult;
			});
			
			jQuery('#ssp_form').find(':text').blur(ssp_validate_field);
			function ssp_validate_field(event) {
				if (jQuery(this).hasClass('ssp_colorpicker')) return;
				if (jQuery.trim(this.value) == '' || (/[\?@!\$%\^&\*\(\)'"\\\/\{\}\[\];\,|~`+=]/.test(this.value))) {
						if (this.name == 'xmlFilePath') return;
						jQuery(this).css('borderColor', '#FF2700');
						jQuery(this).parent('td:first').prev('td:first').find('span:first').css('visibility', 'visible');
				} else {
					jQuery(this).css('borderColor', '#C6D9E9');
					jQuery(this).parent('td:first').prev('td:first').find('span:first').css('visibility', 'hidden');
					jQuery(this).parent().parent().children().css('backgroundColor', '#EFF5FA');
				}
			}
				
			// jQuery('#galleryAppearance, #mediaPlayerAppearance, #navAppearance, #captionAppearance, #feedbackTimerAppearance').each(function(i) { ssp_on_change(this); });
			
			// jQuery('#galleryAppearance, #mediaPlayerAppearance, #navAppearance, #captionAppearance, #feedbackTimerAppearance').bind('change', function (event) { ssp_on_change(this) } );
			
			function ssp_on_change(o) {
				var next = jQuery(o).attr('next');
				if (o.type == 'select-one') {
					if (jQuery(o).val() == 'Hidden') 
						ssp_disable_next(o, next);
					else 
						ssp_enable_next(o, next);
				}
				if (o.type == 'checkbox') {
					if (!o.checked) 
						ssp_disable_next(o, next);
					else 
						ssp_enable_next(o, next);
				}
			}
			
			function ssp_disable_next(o, num) {
				var table = jQuery(o).parent().parent().parent();
				table.find('input, select').each(function(i) {
					if (this != o && this.type != 'submit') this.disabled = true;
				});
			}
			
			function ssp_enable_next(o, num) {
				var table = jQuery(o).parent().parent().parent();
				table.find('input, select').each(function(i) {
					this.disabled = false;
				});
			}
			
			
		});
	</script>