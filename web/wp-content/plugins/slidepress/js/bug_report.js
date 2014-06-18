jQuery(function(){
	jQuery('textarea[name=description]').focus(function(){
		if (this.value == this.defaultValue)
		{
			jQuery(this).val('');
		}
	}).blur(function(){
		if (this.value == '')
		{
			jQuery(this).val(this.defaultValue);
		}
	});
});