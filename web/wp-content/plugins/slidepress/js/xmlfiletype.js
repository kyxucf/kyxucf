var wpGalleryError = false;

function checkWpGallery() {
	if (isNaN(this.value) && jQuery.trim(this.value) != '') {
		wpGalleryError = true;
	} else {
		wpGalleryError = false;
	}
}

jQuery(function() {
	var xmlFileType = jQuery('#xmlFileType').val();
	if (xmlFileType == 'Manual Entry') {
		jQuery('#xmlFilePath').addClass('hidden').hide();
		jQuery('#xmlManualSource').show();
	}
	if (xmlFileType == 'WordPress Gallery') {
		jQuery('#createThumbnailsField, #sspDescriptionField').show();
		jQuery('#xmlFilePath').bind('change', checkWpGallery).trigger('change');
	}
	if (this.value == 'WordPress Gallery' || this.value == 'Media RSS') {
		jQuery('#xmlTypeAdvanced').hide();
	}
	else
	{
		jQuery('#xmlTypeAdvanced').show();
	}
	jQuery('#xmlFileType').change(function(){
		if (this.value == 'Manual Entry') {
			jQuery('#xmlFilePath').addClass('hidden').hide();
			jQuery('#createThumbnailsField').hide();
			jQuery('#xmlManualSource').show();
		} else {
			if (jQuery('#xmlFilePath').hasClass('hidden')) {
				jQuery('#xmlFilePath').removeClass('hidden').show();
				jQuery('#xmlManualSource').hide();
			}
			if (this.value == 'WordPress Gallery') {
				jQuery('#createThumbnailsField, #sspDescriptionField').show();
				jQuery('#xmlFilePath').bind('change', checkWpGallery).trigger('change');
			} else {
				jQuery('#createThumbnailsField, #sspDescriptionField').hide();
				jQuery('#xmlFilePath').unbind('change', checkWpGallery);
				wpGalleryError = false;
			}
		}
		
		if (this.value == 'WordPress Gallery' || this.value == 'Media RSS') {
			jQuery('#xmlTypeAdvanced').hide();
			jQuery('#sspAdvancedSettings').hide();
		}
		else
		{
			jQuery('#xmlTypeAdvanced').show();
		}
	});
	
	jQuery('#xmlTypeAdvanced').toggle(function(){
		jQuery('#sspAdvancedSettings').show();
	},
	function(){
		jQuery('#sspAdvancedSettings').hide();
	});
		
});