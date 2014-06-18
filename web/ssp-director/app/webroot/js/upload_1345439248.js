
var list_count=0;var tn_count=0;var audio_count=0;var thumb_count=0;var complete=false;var dash_flash=false;var swf_path='';var upload_url='';var max_size=0;var dash_av=false;var dash_water=false;var dash_prv=false;var vid_prv=false;var dash_audio=false;function what_type(){if(dash_flash){return 1;}else if(dash_av||dash_water){return 5;}else if(dash_prv||vid_prv){return 3;}else if(dash_audio){return 4;}else{var opt=$F("upload_type");return opt;}}
function set_av_upload(){$('avatar-1').show();if(!dash_av){var h=Element.getHeight('browse-button');var w=Element.getWidth('browse-button');$('browse_wrapper').setStyle({width:w+5+'px',height:h+'px'});var flashvars={aid:"0",dash:"2",max_size:max_size,upload_url:upload_url}
var params={allowScriptAccess:"always",wmode:"transparent"}
var attributes={id:"_uploader"}
swfobject.embedSWF(swf_path,"flash_target",w,h,"9",false,flashvars,params,attributes,after_swf_init);dash_av=true;}}
function set_water_upload(){$('upload-watermark').show();if(!dash_water){var h=Element.getHeight('browse-button');var w=Element.getWidth('browse-button');$('browse_wrapper').setStyle({width:w+5+'px',height:h+'px'});var flashvars={aid:"0",dash:"7",max_size:max_size,upload_url:upload_url}
var params={allowScriptAccess:"always",wmode:"transparent"}
var attributes={id:"_uploader"}
swfobject.embedSWF(swf_path,"flash_target",w,h,"9",false,flashvars,params,attributes,after_swf_init);dash_water=true;}}
function set_prv_upload(){$('edit-preview').show();if(!dash_prv){var h=Element.getHeight('browse-button');var w=Element.getWidth('browse-button');$('browse_wrapper').setStyle({width:w+5+'px',height:h+'px'});var flashvars={aid:aid,dash:"3",max_size:max_size,upload_url:upload_url}
var params={allowScriptAccess:"always",wmode:"transparent"}
var attributes={id:"_uploader"}
swfobject.embedSWF(swf_path,"flash_target",w,h,"9",false,flashvars,params,attributes,after_swf_init);dash_prv=true;}}
function set_vid_prv_upload(){if($('edit-preview').visible()){var start='edit-preview';}else{var start='edit-preview-existing';}
Messaging.d2d(start,'edit-preview-upload');if(vid_prv_lg==1){var d=5;}else{var d=6;}
if(!vid_prv){var h=Element.getHeight('browse-button-vid');var w=Element.getWidth('browse-button-vid');$('browse_wrapper_vid').setStyle({width:w+5+'px',height:h+'px'});var flashvars={aid:current_image_edit,dash:d,max_size:max_size,upload_url:upload_url}
var params={allowScriptAccess:"always",wmode:"transparent"}
var attributes={id:"_uploader_v"}
swfobject.embedSWF(swf_path,"flash_target_vid",w,h,"9",false,flashvars,params,attributes,after_swf_init);vid_prv=true;}else{window.setTimeout(function(dash,aid){$("_uploader_v").js_set_dash(dash);$("_uploader_v").js_set_aid(aid);},200,d,current_image_edit);}}
function on_select_vid_preview(){$('edit-preview-upload').setStyle({opacity:0.0001});Messaging.hello(__("Uploading preview..."),1,false,true);$("_uploader_v").js_upload();on_progress();}
function on_select_prv(){$('edit-preview').setStyle({opacity:0.0001});Messaging.hello(__("Uploading preview..."),1,false,true);$("_uploader").js_upload();on_progress();}
function on_select_vid_prv(){$('edit-preview').setStyle({opacity:0.0001});Messaging.hello(__("Uploading preview..."),1,false,true);$("_uploader").js_upload();on_progress();}
function on_select_watermark(){$('upload-watermark').setStyle({opacity:0.0001});Messaging.hello(__("Uploading watermark..."),1,false,true);$("_uploader").js_upload();on_progress();}
function on_select_audio_int(){Messaging.hello(__("Uploading audio..."),1,false,true);}
function on_select_audio_fail(){Messaging.hello(__("The selected file exceeds your server's upload file size limit. Please raise the server's upload limit or choose a smaller file to upload."),3,false,false);}
function on_select_audio(){$("_uploader").js_upload();on_progress();}
function upload_set_aid(id){if(id==0){$('browse-button').hide();}else{$('browse-button').show();if(!dash_flash){var h=Element.getHeight('browse-button');var w=Element.getWidth('browse-button');$('browse_wrapper').setStyle({width:w+5+'px',height:h+'px'});var flashvars={aid:id,dash:"1",max_size:max_size,upload_url:upload_url}
var params={allowScriptAccess:"always",wmode:"transparent"}
var attributes={id:"_uploader"}
swfobject.embedSWF(swf_path,"flash_target",w,h,"9",false,flashvars,params,attributes,after_swf_init);dash_flash=true;}else{$("_uploader").js_set_aid(id);}
aid=id;}}
function upload(){Messaging.hello(__("Uploading new files..."),1,false,true);set_tags();$("_uploader").js_upload();on_progress();}
function set_tags(){var val=$F('ImageTags');if(val!=''){$("_uploader").js_set_tags(encodeURI(val));}}
function upload_dash(){upload();$('quick-upload').setStyle({opacity:0.0001});}
function upload_avatar(){Messaging.hello(__("Uploading..."),1,false,true);$('avatar-1').setStyle({opacity:0.0001});$("_uploader").js_upload();on_progress();}
function on_select_dash(list,tn_list,thumb_list,audio_list,rejects){list_count=list.length;tn_count=tn_list.length;audio_count=audio_list.length;thumb_count=thumb_list.length;$('upload-stat').innerHTML=sprintf(__('You have selected %d files to upload.'),list_count);$('upload-stat').show();$('upload-tag').show();if(list_count>0){$('upload-button').show();}else{$('upload-button').hide();}}
function on_select_avatar(list,tn_list,thumb_list,audio_list,rejects){if(list.length>0){var new_av=list[0].name;$('av-feedback').innerHTML='<strong>'+__('File selected')+':</strong> '+new_av;$('av-feedback').show();$('av-upload').disabled=false;}}
function kill_quick_upload(){if(dash_flash){$("_uploader").js_rm_all();}
Messaging.kill('quick-upload');$('upload-stat').hide();$('upload-tag').hide();list_count=tn_count=audio_count=thumb_count=0;$('upload-button').hide();$('browse-button').value=__('Browse for content');$('create-q-album-btn').value=__('Create album and browse for content');$('create-q-album-btn').disabled=true;$('AlbumName2').value='';$('quick-up-form').hide();}
function on_select(list,tn_list,thumb_list,audio_list,rejects){list_count=list.length;tn_count=tn_list.length;audio_count=audio_list.length;thumb_count=thumb_list.length;var tgt=$("file_list");if(list_count==0&&tn_count==0&&rejects.length==0&&audio_count==0&&thumb_count==0){Effect.BlindUp('files');}else if(list_count==0&&tn_count==0&&audio_count==0&&thumb_count==0&&rejects.length>0){Effect.BlindUp('files');Messaging.hello(__('All of the selected files exceed the maximum size allowed by the server.'),3,false);}else{var summary=new Array();str='<table id="file_list" cellspacing="0" cellpadding="0"><tr><th class="left">'+__('Files')+'</th><th>'+__('Size')+'</th><th>'+__('Action')+'</th></tr>';if(list_count>0){summary.push(list_count+' '+__('images'));str+="<tr><td class=\"left error\" colspan=\"3\">"+__('Full size images')+":</td></tr>";for(i=0;i<list_count;i++)
{str+="<tr><td class=\"left\">"+list[i].name+"</td><td>"+convert_bits(list[i].size)+"</td><td><a href=\"#\" onclick=\"rm_file('"+list[i].name+"', 1); return false;\">"+__("Remove")+"</a></td></tr>";}}
if(tn_count>0){summary.push(tn_count+' '+__('thumbnails'));str+="<tr><td class=\"left error\" colspan=\"3\">"+__("Thumbnails")+":</td></tr>";for(i=0;i<tn_count;i++)
{str+="<tr><td class=\"left\">"+tn_list[i].name+"</td><td>"+convert_bits(tn_list[i].size)+"</td><td><a href=\"#\" onclick=\"rm_file('"+tn_list[i].name+"', 2); return false;\">"+__("Remove")+"</a></td></tr>";}}
if(thumb_count>0){summary.push(thumb_count+' '+__('album preview'));str+="<tr><td class=\"left error\" colspan=\"3\">"+__('Album preview')+":</td></tr>";for(i=0;i<thumb_count;i++)
{str+="<tr><td class=\"left\">"+thumb_list[i].name+"</td><td>"+convert_bits(thumb_list[i].size)+"</td><td><a href=\"#\" onclick=\"rm_file('"+thumb_list[i].name+"', 3); return false;\">"+__("Remove")+"</a></td></tr>";}}
if(audio_count>0){summary.push(audio_count+' audio file');str+="<tr><td class=\"left error\" colspan=\"3\">"+__('Audio')+":</td></tr>";for(i=0;i<audio_count;i++)
{str+="<tr><td class=\"left\">"+audio_list[i].name+"</td><td>"+convert_bits(audio_list[i].size)+"</td><td><a href=\"#\" onclick=\"rm_file('"+audio_list[i].name+"', 4); return false;\">"+__("Remove")+"</a></td></tr>";}}
if(rejects.length>0){str+="<tr><td class=\"left\" style=\"padding:0;\" colspan=\"3\"><p class=\"warning warn-fixed\"><span>"+__('The following files exceed the maximum allowed size and will not be uploaded')+":</span></p></td></tr>";for(i=0;i<rejects.length;i++){str+="<tr><td class=\"left\">"+rejects[i].name+"</td><td>"+convert_bits(rejects[i].size)+"</td><td></td></tr>";}}
str+='</table>';$('file_list').innerHTML=str;if(!Element.visible('files')){Effect.BlindDown('files');}}}
function on_progress(){if(!complete){var progress=$("_uploader").js_check_progress();if(progress>=100){$('progress').style.width='100%';}else{Messaging.pingProgress(progress);window.setTimeout(on_progress,500);}}}
function done(){$('progress').style.width='100%';complete=true;on_complete();}
function rm_file(file_name,t){$("_uploader").js_remove_file(file_name,t);}
function on_complete(){if(dash_av){avatar_step_2();}else if(dash_water){var myAjax=new Ajax.Updater('watermark-fill',base_url+'watermarks/listing',{method:'get',onComplete:function(){Messaging.hello(__("Uploading watermark..."),2,true);$('upload-watermark').hide();$('upload-watermark').setStyle({opacity:1});}});}else if(vid_prv){vid_preview_step_2();}else if(dash_prv){preview_step_2();}else if(dash_audio){var myAjax=new Ajax.Updater('audio-tgt',base_url+'albums/refresh_audio/'+aid,{method:'get',onComplete:function(){Messaging.hello(__("Uploading audio..."),2,true);}});}else{Messaging.hello(__("Files uploaded...redirecting..."),2,false);window.setTimeout(redirect,2000);}}
function redirect_av(){location.href=here;}
function http_error(){Messaging.hello(__("An error occured when trying to upload your images.")+"<br />"+sprintf(__("See %s for more information on resolving this error."),"<a href=\"http://wiki.slideshowpro.net/SSPdir/CP-ImageUploadsFail\" target=\"_blank\">"+__("this page on the SlideShowPro wiki")+"</a>"),4,false);Effect.BlindUp('files');}
function redirect(){redir="albums/reorder/"+aid;location.href=base_url+redir;}
function convert_bits(bytes){kb=bytes/1024;if(kb<1024){return Math.round(kb)+' KB';}else{mb=kb/1024;return Math.round(mb*10)/10+' MB';}}
function cancel_av_upload(){$('av-feedback').innerHTML='';$('av-feedback').hide();$('av-upload').disabled=true;Messaging.kill('avatar-1');}
function start_file(fn){Messaging.hello(__("Uploading")+' '+fn+'...',1,false,true);}
function process_file(fn){Messaging.hello(__("Processing")+' '+fn+'...',1,false,true);}