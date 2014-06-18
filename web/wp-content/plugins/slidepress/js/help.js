jQuery(function(){
	jQuery('.ssp_form-table .help label').each(function(){
		var label = jQuery(this).parent();
		label.append('<img class="help-img" src="' + SlidePress.sspurl + 'css/information.png" />');
	});
	jQuery('.ssp_form-table .help img').click(function(){
		var label = jQuery(this).siblings('label');
		jQuery.facebox(function(){
			jQuery.get(SlidePress.sspurl + 'help.php', {
				topic :  label.attr('for')
			}, function(data) {
				jQuery.facebox(data);
			});
		});
	});
});