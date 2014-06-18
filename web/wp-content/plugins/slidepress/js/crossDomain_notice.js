jQuery(function(){
	jQuery('.ssp_error .crossdomain-close-button').click(function(){
		jQuery(this).text('[hiding...]');
		var that = jQuery(this).parent();
		var path = SlidePress.siteurl + '/wp-admin/admin-ajax.php';
		jQuery.post(path, {
			action : "hide_crossdomain_notice",
			'cookie' : encodeURIComponent(document.cookie)
		}, function(str){
			that.fadeOut();
		});
	});
});