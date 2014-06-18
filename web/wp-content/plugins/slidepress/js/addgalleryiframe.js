jQuery(function(){
  var p = parent;
  var pj = p.jQuery;
  var postID = pj('input[name=post_ID]').val();

  var succeed = jQuery('.ssp_gallery_added');
  
  if (succeed.size() != 0) {
    //jQuery('.ssp_gallery_added').show();
    //pj('#facebox iframe').height(50);
    p.SlidePress.addedGallery = addedGallery;
    //setTimeout(p.SlidePress.triggerCloseFacebox, 2500);
    pj('#facebox iframe').remove();
    p.SlidePress.triggerCloseFacebox();
  }

  if (typeof postID != 'undefined') {
    jQuery('#xmlFileType option[selected]').removeAttr('selected');
    jQuery('#xmlFileType').val('WordPress Gallery').change();
    jQuery('#xmlFilePath').val(postID);
  }

  jQuery(document).keydown(function(e){
    if (e.keyCode == 27) {
      p.SlidePress.triggerCloseFacebox();
    }
  });
});