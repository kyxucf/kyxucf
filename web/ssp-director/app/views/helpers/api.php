<?php

class ApiHelper extends AppHelper {
	var $helpers = array('Director');
	
	function album($album, $preview = '', $size = array(), $user_size = array(), $active = true, $controller, $users) {
		$data = array();
		if (!empty($album['Album']['aTn'])) {
			list($src, $path, $x, $y) = explode(':', $album['Album']['aTn']);
			$local_path = ALBUMS . DS . 'album-' . $path . DS . 'lg' . DS . $src;
			list($_w, $_h) = getimagesize($local_path);
			$original = DIR_HOST . '/' . ALBUM_DIR . '/album-' . $path . DS . 'lg' . DS . $src;
			$data['tn'] = array(
				'src' => $src,
				'focal' => array('x' => $x, 'y' => $y),
				'original' => array(
					'url' => $original,
					'width' => $_w,
					'height' => $_h
				)
			);
			if (!empty($preview)) {
				$data['preview'] = array();
				$s = explode(',', $preview);
				$data['preview']['url'] = __p(array('src' => $src,
				 										'album_id' => $path,
				 										'width' => $s[0],
				 										'height' => $s[1],
				 										'square' => $s[2],
				 										'quality' => $s[3],
				 										'sharpening' => $s[4],
				 										'anchor_x' => $x,
				 										'anchor_y' => $y,
														'modified_on' => $album['Album']['modified_on']));
				list($w, $h) = computeSize(ALBUMS . DS . 'album-' . $path . DS . 'lg' . DS . $src, $s[0], $s[1], $s[2]);
				$data['preview']['width']= $w;
				$data['preview']['height']= $h;
			}
		} 
		$audio = '';
		if (!empty($album['Album']['audioFile'])) {
			$audio = DIR_HOST . '/album-audio/' . $album['Album']['audioFile'];
		}
		$creator = $this->user($album['Album']['created_by'], $users, $user_size);
		$updater = $this->user($album['Album']['updated_by'], $users, $user_size);
		$public = $this->user(null, $users, $user_size);
		
		$album['Album']['name'] = convert_smart_quotes($album['Album']['name']);
		$album['Album']['description'] = convert_smart_quotes($album['Album']['description']);
		$album['Album']['audioCap'] = convert_smart_quotes($album['Album']['audioCap']);
		
		$data['id'] = $album['Album']['id'];
		$data['name'] = $album['Album']['name'];
		$data['description'] = $album['Album']['description'];
		$data['tags'] = $album['Album']['tags'];
		$data['audio'] = $audio;
		$data['audio_caption'] = $album['Album']['audioCap'];
		$data['modified'] = $album['Album']['modified_on'];
		$data['created'] = $album['Album']['created_on'];
		$data['date_taken'] = $album['Album']['date_taken'];
		$data['place_taken'] = $album['Album']['place_taken'];
		$data['smart'] = $album['Album']['smart'];
		$data['creator'] = $creator;
		$data['updater'] = $updater;
		$data['public'] = $public;
		$data['internal_id'] = $album['Album']['internal_id'];
		$data['total_count'] = $album['Album']['images_count'];
		$data['video_count'] = $album['Album']['video_count'];
		$data['mobile_link'] = DIR_HOST . '/m/?album=' . $album['Album']['internal_id'];
		
		if (isset($album['Image']) || isset($album['Smart'])) {
			$data['contents'] = array();
			if (isset($album['Smart'])) {
				foreach($album['Smart'] as $image) {
					if ($active && $image['Image']['active'] || !$active) {
						$data['contents'][] = $this->image($image['Image'], $image['Album'], $size, $user_size, $active, $controller, $users, $album['Album'], $album['Album']['watermark_id']);
					}
				}
			} else {
				foreach($album['Image'] as $image) {
					if ($active && $image['active'] || !$active) {
						$data['contents'][] = $this->image($image, $album['Album'], $size, $user_size, $active, $controller, $users, null, $album['Album']['watermark_id']);
					}
				}
			}
		}
		return $data;
	}
	
	////
	// Output functions
	////
	
	function json($arr) { 
	  	if (function_exists('json_encode')) return json_encode($arr);
	    $parts = array(); 
	    $is_list = false; 

	    //Find out if the given array is a numerical array 
	    $keys = array_keys($arr); 
	    $max_length = count($arr)-1; 
	    if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1 
	        $is_list = true; 
	        for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position 
	            if($i != $keys[$i]) { //A key fails at position check. 
	                $is_list = false; //It is an associative array. 
	                break; 
	            } 
	        } 
	    } 

	    foreach($arr as $key=>$value) { 
	        if(is_array($value)) { //Custom handling for arrays 
	            if($is_list) $parts[] = $this->json($value); /* :RECURSION: */ 
	            else $parts[] = '"' . $key . '":' . $this->json($value); /* :RECURSION: */ 
	        } else { 
	            $str = ''; 
	            if(!$is_list) $str = '"' . $key . '":'; 

	            if(is_numeric($value)) $str .= $value; //Numbers 
	            elseif($value === false) $str .= 'false'; //The booleans 
	            elseif($value === true) $str .= 'true'; 
	            else $str .= '"' . addslashes(str_replace(array("\r\n", "\r", "\n"), "<br />", $value)) . '"'; //All other things 
	            // :TODO: Is there any more datatype we should be in the lookout for? (Object?) 

	            $parts[] = $str; 
	        } 
	    } 
	    $json = implode(',',$parts); 

	    if($is_list) return '[' . $json . ']';//Return numerical JSON 
	    return '{' . $json . '}';//Return associative JSON 
	}
	
	function xml($arr, $parent = null) {
		$str = '';
		foreach($arr as $key => $val) {
			if (is_numeric($key) && !is_null($parent)) {
				$key = rtrim($parent, 's');
			}
			if (is_array($val)) {
				$str .= "<$key>\n";
				$str .= $this->xml($val, $key);
				$str .= "</$key>\n";
			} else {
				if (is_numeric($val) || $val === true || $val === false) {
					$inside = $val;
				} else {
					if (function_exists('mb_detect_encoding')) {
						$encoding = mb_detect_encoding($val);
						if ($encoding == 'ASCII') {
							if (utf8_encode(utf8_decode($val)) == $val) {
								// No need to encode, utf-8 already
							} else {
								$val = urlencode($val);
							}
						}
					}
					$inside = "<![CDATA[$val]]>";
				}
				$str .= "<$key>$inside</$key>\n";
			}
		}
		return $str;
	}
	
	function image($image, $album, $size = array(), $user_size = array(), $active = true, $controller, $users, $smart = null, $watermark = null) {
		$data = array();
		if ($active && !$image['active']) { return ''; }
		$size_str = '';
		
		$arr = unserialize($image['anchor']);
		if (empty($arr)) {
			$x = $y = 50;
		} else {
			$x = $arr['x'];
			$y = $arr['y'];
		}
		
		if (isImage($image['src'])) {
			foreach($size as $s) {
				$s = explode(',', $s);
				
				$pre_array = array(	'width' => $s[1], 
									'height' => $s[2], 
									'square' => $s[3], 
									'quality' => $s[4], 
									'sharpening' => $s[5], 
									'anchor_x' => $x, 
									'anchor_y' => $y);
				
				if (!is_null($watermark) && $watermark > 0) {
					$watermark_array = array(	'watermark_id' => $watermark,
												'watermark_location' => $controller->watermarks[$watermark]['position'],
												'watermark_opacity' => $controller->watermarks[$watermark]['opacity']);
					$pre_water_array = array_merge($pre_array, $watermark_array);
				}

				list($w, $h) = computeSize(ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $image['src'], $s[1], $s[2], $s[3]);
				$data[$s[0]] = array();
				$data[$s[0]]['url'] =  __p(array_merge(array(	'src' => $image['src'], 
																'album_id' => $image['aid']),
																$pre_array));
				if (isset($pre_water_array)) {
					$data[$s[0]]['watermarked_url'] = __p(array_merge(array(	'src' => $image['src'], 
																				'album_id' => $image['aid'],
																				'modified_on' => $image['modified_on']), $pre_water_array));
				}
				$data[$s[0]]['width'] = $w;
				$data[$s[0]]['height'] = $h;
			}
		} else {
			if (!empty($image['lg_preview'])) {
				list($p, $x, $y) = explode(':', $image['lg_preview']);
				$local_path = ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $p;
				$original = DIR_HOST . '/' . ALBUM_DIR . '/album-' . $image['aid'] . DS . 'lg' . DS . $p;
				list($_w, $_h) = getimagesize($local_path);
				$data['lg_preview'] = array(
					'src' => $p,
					'focal' => array('x' => $x, 'y' => $y),
					'original' => array(
						'url' => $original,
						'width' => $_w,
						'height' => $_h
					)
				);
				foreach($size as $s) {
					$s = explode(',', $s);
					$pre_array = array(	'width' => $s[1], 
										'height' => $s[2], 
										'square' => $s[3], 
										'quality' => $s[4], 
										'sharpening' => $s[5], 
										'anchor_x' => $x, 
										'anchor_y' => $y);

					if (!is_null($watermark)) {
						$watermark_array = array(	'watermark_id' => $watermark['id'],
											'watermark_location' => $watermark['position'],
											'watermark_opacity' => $watermark['opacity']);
						$pre_array = array_merge($pre_array, $watermark_array);
					}
					
					list($w, $h) = computeSize(ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $p, $s[1], $s[2], $s[3]);
					$data[$s[0]]['url'] = __p(array_merge(array(	'src' => $p, 
																	'album_id' => $image['aid'],
																	'modified_on' => $image['modified_on']),
																	$pre_array));
					$data[$s[0]]['width'] = $w;
					$data[$s[0]]['height'] = $h;
				}
			}
			
			if (!empty($image['tn_preview'])) {
				list($p, $x, $y) = explode(':', $image['tn_preview']);
				$local_path = ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $p;
				list($_w, $_h) = getimagesize($local_path);
				$original = DIR_HOST . '/' . ALBUM_DIR . '/album-' . $image['aid'] . DS . 'lg' . DS . $p;
				$data['thumb_preview'] = array(
					'src' => $p,
					'focal' => array('x' => $x, 'y' => $y),
					'original' => array(
						'url' => $original,
						'width' => $_w,
						'height' => $_h
					)
				);
				foreach($size as $s) {
					$s = explode(',', $s);
					$pre_array = array(	'width' => $s[1], 
										'height' => $s[2], 
										'square' => $s[3], 
										'quality' => $s[4], 
										'sharpening' => $s[5], 
										'anchor_x' => $x, 
										'anchor_y' => $y);
					list($w, $h) = computeSize(ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $p, $s[1], $s[2], $s[3]);
					$data['thumb_preview'][$s[0]]['url'] = __p(array_merge(array(	'src' => $p, 
																					'album_id' => $image['aid'],
																					'modified_on' => $image['modified_on']),
																					$pre_array));
					$data['thumb_preview'][$s[0]]['width'] = $w;
					$data['thumb_preview'][$s[0]]['height'] = $h;
				}
			}
		}
		
		$local_path = ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $image['src'];
		if (isImage($image['src'])) {
			list($original_w, $original_h) = getimagesize($local_path);
		} else {
			$original_w = $original_h = 0;
		}	

		$original = DIR_HOST . '/' . ALBUM_DIR . '/album-' . $image['aid'] . '/lg/' . $image['src'];
		if (empty($image['title']) && !empty($album['title_template'])) {
			if (is_null($smart)) {
				$image['title'] = $controller->Director->formTitle($image, $album);
			} else {
				$image['title'] = $controller->Director->formTitle($image, $smart, $album);
			}
		}
		if (empty($image['caption']) && !empty($album['caption_template'])) {
			if (is_null($smart)) {
				$image['caption'] = $controller->Director->formCaption($image, $album);
			} else {
				$image['caption'] = $controller->Director->formCaption($image, $smart, $album);
			}
		}
		if (empty($image['link']) && !empty($album['link_template'])) {
			@list($image['link'], $image['target']) = $controller->Director->formLink($image, $album);
		}
		
		if (!empty($image['start_on']) || !empty($image['end_on'])) {
			$data['schedule'] = array('begin' => $image['start_on'], 'end' => $image['end_on']);
		} 
		
		$data['creator'] = $this->user($image['created_by'], $users, $user_size);
		$data['updater'] = $this->user($image['updated_by'], $users, $user_size);
		$data['public'] = $this->user(null, $users, $user_size);
		
		$data['title'] = convert_smart_quotes($image['title']);
		$data['caption'] = convert_smart_quotes($image['caption']);
		$data['id'] = $image['id'];
		$data['src'] = $image['src'];
		$data['album_id'] = $image['aid'];
		$data['is_video'] = $image['is_video'];
		$data['tags'] = $image['tags'];
		$data['link'] = $image['link'];
		$data['active'] = $image['active'];
		$data['seq'] = $image['seq'];
		$data['pause'] = $image['pause'];
		$data['target'] = $image['target'];
		$data['modified'] = $image['modified_on'];
		$data['created'] = $image['created_on'];
		$data['public'] = $image['public'];
		$data['captured_on'] = $image['captured_on'];
		$data['filesize'] = $image['filesize'];
		$data['original'] = array(
			'url' => $original,
			'width' => $original_w,
			'height' => $original_h
		);
		$data['focal'] = array(
			'x' => $x,
			'y' => $y
		);
		
		$mimes = array(
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'gif' => 'image/gif',
			'png' => 'image/png',
			'flv' => 'video/x-flv',
			'f4v' => 'video/f4v',
			'swf' => 'application/x-shockwave-flash',
			'mov' => 'video/quicktime',
			'mp4' => 'video/mp4',
			'm4v' => 'video/x-m4v',
			'3gp' => 'video/3gpp',
			'3g2' => 'video/3gpp2',
		);
		
		$info = pathinfo($local_path);
		
		if (array_key_exists(strtolower($info['extension']), $mimes)) {
			$data['mime_type'] = $mimes[$info['extension']];
		} else if (function_exists('mime_content_type')) {
			$data['mime_type'] = mime_content_type($local_path);
		} else {
			$data['mime_type'] = '';
		}
		
		if ($controller->includeMeta) {
			list($mdata, $dummy) = $controller->Director->imageMetaData(ALBUMS . DS . 'album-' . $image['aid'] . DS . 'lg' . DS . $image['src']);
			if (!empty($mdata)) {
				$data['iptc'] = array();
				$data['exif'] = array();
				foreach($controller->Director->iptcTags as $tag) {
					$tag_clean = str_replace(' ', '_', $tag);
					$data['iptc'][$tag_clean] = $controller->Director->parseMetaTags("iptc:$tag", $mdata, 'w');
				}
				foreach($controller->Director->exifTags as $tag) {
					$tag_clean = str_replace(' ', '_', $tag);
					$data['exif'][$tag_clean] = $controller->Director->parseMetaTags("exif:$tag", $mdata, 'w');
				}
			}
		}
		
		if (!is_null($smart)) {
			$data['original_album'] = array(
				'id' => $album['id'],
				'title' => $album['name'],
				'tags' => $album['tags']
			);
		}
		return $data;
	}
	
	function gallery($gallery, $albums = array(), $preview = '', $size = array(), $user_size = array(), $active = true, $controller = null, $users) {
		$data = array();
		$data['id'] = $gallery['Gallery']['id'];
		$data['name'] = $gallery['Gallery']['name'];
		$data['description'] = $gallery['Gallery']['description'];
		$data['created'] = $gallery['Gallery']['created_on'];
		$data['modified'] = $gallery['Gallery']['modified_on'];
		$data['internal_id'] = $gallery['Gallery']['internal_id'];
		$data['mobile_link'] = DIR_HOST . '/m/?gallery=' . $gallery['Gallery']['internal_id'];
		$data['creator'] = $this->user($gallery['Gallery']['created_by'], $users, $user_size);
		$data['updater'] = $this->user($gallery['Gallery']['updated_by'], $users, $user_size);
		$data['public'] = $this->user(null, $users, $user_size);
		
		if (!empty($albums)) {		
			$data['albums'] = array();
			foreach($albums as $album) {
				$data['albums'][] = $this->album($album, $preview, $size, $user_size, $active, $controller, $users);
			}
		}
		return $data;
	}
	
	function _parseUser($id, $field, $u) {
		return $u[$id][$field];
	}
	
	function user($id, $users, $size = array()) {
		if (is_null($id)) {
			$id = $users['king'];
		}
		$data = array();
		if (is_array($id)) {
			$user_id = $id['id'];
			$u = $users[$user_id];
			if (isset($id['count'])) {
				$count = $id['count'];
			} else {
				$count = $u['image_count'];
			}
		} else if ($id == 0) {
			return '';
		} else {
			$user_id = $id;
			$u = $users[$user_id];
			$count = $u['image_count'];
		}
		
		$externals = unserialize($u['externals']);
		$ex_str = '';
		if (!empty($externals)) {
			$data['externals'] = array();
			foreach($externals as $a) {
				$data['externals'][] = array('name' => $a['name'], 'url' => $a['url']);
			}
		}
		
		$originals = glob(AVATARS . DS . $user_id . DS . 'original.*');
		$size_str = '';
		if (!empty($size) && (count($originals) != 0)) {
			$data['photos'] = array();
			foreach($size as $s) {
				$s = explode(',', $s);
				$data['photos'][$s[0]] = array();
				$arr = unserialize($u['anchor']);
				if (empty($arr)) {
					$x = $y = 50;
				} else {
					$x = $arr['x'];
					$y = $arr['y'];
				}
				list($w, $h) = computeSize($originals[0], $s[1], $s[2], $s[3]);
				$data['photos'][$s[0]]['url'] = __p(array('src' => basename($originals[0]),
				 										'album_id' => "avatar-$user_id",
				 										'width' => $s[1],
				 										'height' => $s[2],
				 										'square' => $s[3],
				 										'quality' => $s[4],
				 										'sharpening' => $s[5],
				 										'anchor_x' => $x,
				 										'anchor_y' => $y));
				$data['photos'][$s[0]]['width'] = $w;
				$data['photos'][$s[0]]['height'] = $h;
			}
		}
		$data['id'] = $user_id;
		$data['username'] = $u['usr'];
		$data['first'] = $u['first_name'];
		$data['last'] = $u['last_name'];
		$data['display_name'] = $u['display_name'];
		$data['profile'] = $u['profile'];
		$data['content_count'] = $count;
		return $data;
	}
}

?>