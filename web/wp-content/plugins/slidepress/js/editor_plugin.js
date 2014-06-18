(function(){
	SlidePress.insertGallery = function() {
		SlidePress.insertGalleryParam(this.sspGalleryId);
	}

	SlidePress.insertGalleryParam = function(id) {
		tinyMCE.activeEditor.execCommand('mceInsertContent', 0, "[slidepress gallery='" + id + "']");
	}

	SlidePress.triggerCloseFacebox = function() {
		if (typeof SlidePress.addedGallery != 'undefined') {
/*			var slidepress_button = tinyMCE.activeEditor.controlManager.get('slidepress_button');
			SlidePress.galleries[SlidePress.galleries.length] = SlidePress.addedGallery;
			if (SlidePress.galleries.length === 1) {
				slidepress_button.menu.addSeparator();
			}
			slidepress_button.menu.add({title : SlidePress.addedGallery.sspName, sspGalleryId : SlidePress.addedGallery.sspGalleryId, onclick : SlidePress.insertGallery}); */
			SlidePress.insertGalleryParam(SlidePress.addedGallery.sspGalleryId);
			SlidePress.galleries[SlidePress.galleries.length] = SlidePress.addedGallery;
			delete SlidePress.addedGallery; 
			//slidepress_button.menu.update();
		}
		jQuery(document).trigger('close.facebox');
	}

	SlidePress.revealFacebox = function() {
		//jQuery("#facebox iframe").hide();
		jQuery('#facebox .loading').remove();
		jQuery('#facebox .content').css({position : 'relative'}).append('<div class="loading" style="background:#f9f9f9 url('+jQuery.facebox.settings.loadingImage+') no-repeat center center; top:0; left:0; width:100%; height:100%; position:absolute;"></div>');
		jQuery('#facebox iframe').load(function(){
			jQuery('#facebox .loading').remove();
			jQuery(this).show();
		});
	}
	
	SlidePress.closeFacebox = function() {
		jQuery(document).unbind('reveal.facebox', SlidePress.revealFacebox);
	}

	SlidePress.createNew = function() {
		var html = '<iframe src="admin.php?page=ssp_show_admin_addgallery&iframe=1" width="870" height="400"></iframe>';
		jQuery(document).bind('reveal.facebox', SlidePress.revealFacebox);
		jQuery(document).bind('close.facebox', SlidePress.closeFacebox);
		jQuery.facebox(html);

		jQuery('#facebox .body').css({background : '#f9f9f9'});
	}
	
	SlidePress.selectGallery = function() {
		var content = jQuery('<div><h3>Select Gallery</h3>\
		<p>Click "Select" next to the gallery you wish to insert into this page/post.</p>\
		<div class="modal-scroller">\
		<table class="widefat" cellspacing="0" cellpadding="0">\
			<thead>\
				<tr>\
					<th scope="col">Gallery ID</th>\
					<th scope="col">Gallery Name</th>\
					<th scope="col" width="30%" class="actions">Action</th>\
				</tr>\
			</thead>\
			<tbody>\
			</tbody>\
		</table>\
		</div>\
		</div>');
		for (index in SlidePress.galleries) {
			var alt = 'alternate ' == alt ? '' : 'alternate ';
			var row = jQuery('<tr class="' + alt + 'author-self status-publish" valign="top">\
				<td class="id">' + SlidePress.galleries[index].sspGalleryId + '</td>\
				<td class="name">' + SlidePress.galleries[index].sspName + '</td>\
				<td class="actions">\
					<a href="#" class="use" title="Use this gallery">Select</a>\
				</td>\
			</tr>');
			row.appendTo(content.find('tbody'));
		}
		content.find('table').attr('id', 'slidepress-select-gallery');
		jQuery.facebox(content.html());
		jQuery('#slidepress-select-gallery .actions .use').click(function(e){
			e.preventDefault();
			SlidePress.insertGalleryParam(jQuery(this).parent().siblings('.id').text());
			jQuery(document).trigger('close.facebox');
		});
	}

	tinymce.create('tinymce.plugins.slidepress', {
		createControl: function(n, cm) {
			switch (n) {
				case 'slidepress_button':
					var c = cm.createSplitButton('slidepress_button', {
						title : 'SlidePress',
						image : SlidePress.sspurl + 'js/ssp_logo.png',
						onclick : function(){
							c.showMenu();
						}
					});
					c.onRenderMenu.add(function(c,m) {
						m.add({title : 'Create new gallery', onclick : SlidePress.createNew});
						if (SlidePress.galleries.length !== 0) {
							m.add({title : 'Select gallery', onclick : SlidePress.selectGallery});
							/* m.addSeparator();
							for (index in SlidePress.galleries) {
								m.add({title : SlidePress.galleries[index].sspName, sspGalleryId : SlidePress.galleries[index].sspGalleryId, onclick : SlidePress.insertGallery});
							} */
						}
					});
				return c;
			}
			return null;
		}
	});
	
	tinymce.PluginManager.add('slidepress', tinymce.plugins.slidepress);
})()