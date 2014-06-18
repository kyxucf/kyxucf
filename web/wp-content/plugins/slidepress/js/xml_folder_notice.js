jQuery(function(){
	jQuery('.ssp_error .xml-folder-close-button').click(function(){
		jQuery(this).text('[hiding...]');
		var that = jQuery(this).parent();
		var path = SlidePress.siteurl + '/wp-admin/admin-ajax.php';
		jQuery.post(path, {
			action : "hide_xml_folder_notice",
			'cookie' : encodeURIComponent(document.cookie)
		}, function(str){
			that.fadeOut();
		});
	});
});