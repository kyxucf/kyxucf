<?php

class Darkroom {

	////
	// Grab the extension of of any file
	////
	function returnExt($file) {
		$pos = strrpos($file, '.');
		return strtolower(substr($file, $pos+1, strlen($file)));
	}
	
	function watermark_original($source, $dest, $water_id, $water_location, $water_opacity) {
		$composite = str_replace('convert', 'composite', MAGICK_PATH_FINAL);
		$water_path = ROOT . DS . ALBUM_DIR . DS . 'watermarks' . DS . $water_id;
		$find = glob("$water_path.*");
		if (!empty($find)) {
			$info = pathinfo($find[0]);
			$water_path .= '.' . $info['extension'];
			$gd = $this->gdVersion();
			if ($gd >= 3) {
				switch((int) $water_location) {
					case(1):
						$postion = 'northwest';
						break;
					case(2):
						$postion = 'north';
						break;
					case(3):
						$postion = 'northeast';
						break;
					case(4):
						$postion = 'west';
						break;
					case(5):
						$postion = 'center';
						break;
					case(6):
						$postion = 'east';
						break;
					case(7):
						$postion = 'southwest';
						break;
					case(8):
						$postion = 'south';
						break;
					case(9):
						$postion = 'southeast';
						break;
				}
				$strip = '';
				if ($gd > 4) { $strip .= ' -limit thread 1'; }
				$cmd .= "$composite{$strip} -dissolve $water_opacity -gravity $postion $water_path $source $dest";
				exec($cmd);
			} else {
				$ext = $this->returnExt($source);
				switch(true) {
					case preg_match("/jpg|jpeg|JPG|JPEG/", $ext):
						if (imagetypes() & IMG_JPG) {
							$dst_img = imagecreatefromjpeg($source);
							$type = 'jpg';
						} else {
							return;
						}
						break;
					case preg_match("/png/", $ext):
						if (imagetypes() & IMG_PNG) {
							$dst_img = imagecreatefrompng($source);
							$type = 'png';
						} else {
							return;
						}
						break;
					case preg_match("/gif|GIF/", $ext):
						if (imagetypes() & IMG_GIF) { 
							$dst_img = imagecreatefromgif($source);
							$type = 'gif';
						} else {
							return;
						}
						break;
				}
				
				
				switch(true) {
					case preg_match("/jpg|jpeg|JPG|JPEG/", $info['extension']):
						if (imagetypes() & IMG_JPG) {
							$water_img = imagecreatefromjpeg($water_path);
						}
						break;
					case preg_match("/png/", $info['extension']):
						if (imagetypes() & IMG_PNG) {
							$water_img = imagecreatefrompng($water_path);
						}
						break;
					case preg_match("/gif|GIF/", $info['extension']):
						if (imagetypes() & IMG_GIF) { 
							$water_img = imagecreatefromgif($water_path);
						}
						break;
				}
				
				if (isset($water_img)) {
					$water_x = imagesx($water_img); 
					$water_y = imagesy($water_img);
				
					if (in_array($water_location, array(1,4,7))) {
						$insert_x = 0;
					} elseif (in_array($water_location, array(2,5,8))) {
						$insert_x = (imagesx($dst_img)/2) - ($water_x/2);
					} else {
						$insert_x = imagesx($dst_img) - $water_x;
					}
				
					if (in_array($water_location, array(1,2,3))) {
						$insert_y = 0;
					} elseif (in_array($water_location, array(4,5,6))) {
						$insert_y = (imagesy($dst_img)/2) - ($water_y/2);
					} else {
						$insert_y = imagesy($dst_img) - $water_y;
					}
				
					if ($water_opacity == 100) {
						imagecopy($dst_img, $water_img, $insert_x, $insert_y, 0, 0, $water_x, $water_y);
					} else {
						$bg_cut = imagecreatetruecolor($water_x, $water_y);
						imagecopy($bg_cut, $dst_img, 0, 0, $insert_x, $insert_y, $water_x, $water_y); 
						imagecopy($dst_img, $water_img, $insert_x, $insert_y, 0, 0, $water_x, $water_y);
						imagecopymerge($dst_img, $bg_cut, $insert_x, $insert_y, 0, 0, $water_x, $water_y, 100-$water_opacity);
					}
					
					if ($type == 'png') {
						imagealphablending($dst_img, false);
						imagesavealpha($dst_img, true);
						imagepng($dst_img, $dest); 
					} elseif ($type == 'gif') {
						imagegif($dst_img, $dest);
					} else {
						imagejpeg($dst_img, $dest, 100); 
					}
				}
			}	
		}
	}
	
	////
	// The workhorse develop function
	////
	function develop($name, $filename, $new_w, $new_h, $quality, $square = false, $gd = null, $sharpening, $x, $y, $force = false, $water_id, $water_location, $water_opacity) {
		$old_mask = umask(0);

		$water = false;
		if ($water_id > 0) { $water = true; }
		
		if (is_null($gd)) {
			if (defined('DIR_GD_VERSION')) {
				$gd = DIR_GD_VERSION;
			} else {
				$gd = $this->gdVersion();
			}
		}
	
		settype($gd, 'integer');
			
		// ImageMagick
		if ($gd >= 3) {
			$info = getimagesize($name); $w = $info[0]; $h = $info[1];
			
			if ($new_w == $w && $new_h == $h && !$force) {
				if ($water) {
					$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
				} else {
					copy($name, $filename);
				}
				return;
			}
			
			$original_aspect = $w/$h;
			$new_aspect = $new_w/$new_h;
			$strip = $water_limit = '';
			if ($gd > 3) { $strip .= ' -strip'; }
			if ($gd > 4) { $strip .= ' -limit thread 1'; $water_limit = ' -limit thread 1'; }
			if ($square) {
				if (($new_w > $w || $new_h > $h) && !$force) {
					if ($water) {
						$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
					} else {
						copy($name, $filename);
					}
					return;
				}
				if ($original_aspect >= $new_aspect) {
					$size_str = 'x' . $new_h;
					$int_w = ($w*$new_h)/$h;
					$int_h = $new_h;
					$pos_x = $int_w * ($x/100);
					$pos_y = $new_h * ($y/100);
					$size_hint = "{$int_w}{$size_str}";
				} else {
					$size_str = $new_w . 'x';
					$int_h = ($h*$new_w)/$w;
					$int_w = $new_w;
					$pos_x = $new_w * ($x/100);
					$pos_y = $int_h * ($y/100);
					$size_hint = "{$size_str}{$int_h}";
				}
				$crop_y = $pos_y - ($new_h/2);
				$crop_x = $pos_x - ($new_w/2);
				if ($crop_y < 0) { 
					$crop_y = 0;
				} else if (($crop_y+$new_h) > $int_h) {
					$crop_y = $int_h - $new_h;
				}
				if ($crop_x < 0) { 
					$crop_x = 0;
				} else if (($crop_x+$new_w) > $int_w) {
					$crop_x = $int_w - $new_w;
				}
				$cmd = MAGICK_PATH_FINAL . $strip . " -size $size_hint \"$name\" -depth 8 -quality $quality -resize $size_str -crop {$new_w}x{$new_h}+{$crop_x}+{$crop_y}";
				if ($gd == 4) {
					$cmd .= ' +repage';
				} else {
					$cmd .= ' -page 0+0';
				}
			} else {
				if ((($original_aspect >= $new_aspect && $new_w > $w) || ($original_aspect < $new_aspect && $new_h > $h)) && !$force) {
					if ($water) {
						$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
					} else {
						copy($name, $filename);
					}
					return;
				}
				$cmd = MAGICK_PATH_FINAL . $strip . " -size {$new_w}x{$new_h} \"$name\" -depth 8 -quality $quality -resize {$new_w}x{$new_h}";
			}
			
			if ($sharpening > 0) {
				// Add sharpening
				$sigma = $sharpening/2;
				if ($sigma < 1) { $sigma = 1; }
				$cmd .= " -unsharp {$sharpening}x{$sigma}+1.0+0.10";
			}
				
			if ($water_id > 0) {
				$composite = str_replace('convert', 'composite', MAGICK_PATH_FINAL);
				$water_path = ROOT . DS . ALBUM_DIR . DS . 'watermarks' . DS . $water_id;
				$find = glob("$water_path.*");
				if (!empty($find)) {
					$info = pathinfo($find[0]);
					$water_path .= '.' . $info['extension'];
				
					switch((int) $water_location) {
						case(1):
							$postion = 'northwest';
							break;
						case(2):
							$postion = 'north';
							break;
						case(3):
							$postion = 'northeast';
							break;
						case(4):
							$postion = 'west';
							break;
						case(5):
							$postion = 'center';
							break;
						case(6):
							$postion = 'east';
							break;
						case(7):
							$postion = 'southwest';
							break;
						case(8):
							$postion = 'south';
							break;
						case(9):
							$postion = 'southeast';
							break;
					}
					
					$cmd .= " - | $composite{$water_limit} -dissolve $water_opacity -gravity $postion $water_path -";
				}
			}
			
			$cmd .= " \"$filename\"";
			exec($cmd);
		} else {	
			$ext = $this->returnExt($name);	
			// Find out what we are dealing with
			switch(true) {
				case preg_match("/jpg|jpeg|JPG|JPEG/", $ext):
					if (imagetypes() & IMG_JPG) {
						$src_img = imagecreatefromjpeg($name);
						$type = 'jpg';
					} else {
						return;
					}
					break;
				case preg_match("/png/", $ext):
					if (imagetypes() & IMG_PNG) {
						$src_img = imagecreatefrompng($name);
						$type = 'png';
					} else {
						return;
					}
					break;
				case preg_match("/gif|GIF/", $ext):
					if (imagetypes() & IMG_GIF) { 
						$src_img = imagecreatefromgif($name);
						$type = 'gif';
					} else {
						return;
					}
					break;
			}
	
			if (!isset($src_img)) { return; };

			$old_x = imagesx($src_img);
			$old_y = imagesy($src_img);
			
			if ($new_w == $old_x && $new_h == $old_y && !$force) {
				if ($water) {
					$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
				} else {
					imagedestroy($src_img);
					copy($name, $filename);
				}
				return;
			}
			
			$original_aspect = $old_x/$old_y;
			$new_aspect = $new_w/$new_h;

			if ($square) {
				if (($new_w > $old_x || $new_h > $old_y) && !$force) {
					if ($water) {
						$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
					} else {
						copy($name, $filename);
					}
					return;
				}
				if ($original_aspect >= $new_aspect) {
					$thumb_w = ($new_h*$old_x)/$old_y;
					$thumb_h = $new_h;				
					$pos_x = $thumb_w * ($x/100);
					$pos_y = $thumb_h * ($y/100);
				} else {
					$thumb_w = $new_w;
					$thumb_h = ($new_w*$old_y)/$old_x;
					$pos_x = $thumb_w * ($x/100);
					$pos_y = $thumb_h * ($y/100);
				}
				$crop_y = $pos_y - ($new_h/2);
				$crop_x = $pos_x - ($new_w/2);
				if ($crop_y < 0) { 
					$crop_y = 0;
				} else if (($crop_y+$new_h) > $thumb_h) {
					$crop_y = $thumb_h - $new_h;
				}
				if ($crop_x < 0) { 
					$crop_x = 0;
				} else if (($crop_x+$new_w) > $thumb_w) {
					$crop_x = $thumb_w - $new_w;
				}
			} else {
			 	$crop_y = 0;
				$crop_x = 0;

				if ($original_aspect >= $new_aspect) {
					if ($new_w > $old_x && !$force) {
						if ($water) {
							$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
						} else {
							imagedestroy($src_img);
							copy($name, $filename);
						}
						return;
					}
					$thumb_w = $new_w;
					$thumb_h = ($new_w*$old_y)/$old_x;
				} else { 
					if ($new_h > $old_y && !$force) {
					 	if ($water) {
							$this->watermark_original($name, $filename, $water_id, $water_location, $water_opacity);
						} else {
							imagedestroy($src_img);
							copy($name, $filename);
						}
						return;
					}
					$thumb_w = ($new_h*$old_x)/$old_y;
					$thumb_h = $new_h;
				}
			}

			if ($gd != 2) {
				$dst_img_one = imagecreate($thumb_w, $thumb_h);
				imagecopyresized($dst_img_one, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);    
			} else {
				if ($type == 'png') {
					$dst_img_one = imagecreatetruecolor($thumb_w, $thumb_h);
				    $trans_colour = imagecolorallocatealpha($dst_img_one, 0, 0, 0, 127);
				    imagefill($dst_img_one, 0, 0, $trans_colour);
				} else {
					$dst_img_one = imagecreatetruecolor($thumb_w, $thumb_h);
				}
				imagecopyresampled($dst_img_one, $src_img, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y); 
			}        

			if ($square) {
				if ($gd != 2) {
					$dst_img = imagecreate($new_w, $new_h);
					imagecopyresized($dst_img, $dst_img_one, 0, 0, $crop_x, $crop_y, $new_w, $new_h, $new_w, $new_h);    
				} else {
					if ($type == 'png') {
						$dst_img = imagecreatetruecolor($new_w, $new_h);
					    $trans_colour = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
					    imagefill($dst_img, 0, 0, $trans_colour);
					} else {
						$dst_img = imagecreatetruecolor($new_w, $new_h);
					}
					imagecopyresampled($dst_img, $dst_img_one, 0, 0, $crop_x, $crop_y, $new_w, $new_h, $new_w, $new_h); 
				}
			} else {
				$dst_img = $dst_img_one;
			}

			if ($water_id > 0) {
				$water_path = ROOT . DS . ALBUM_DIR . DS . 'watermarks' . DS . $water_id;
				$find = glob("$water_path.*");
				if (!empty($find)) {
					$info = pathinfo($find[0]);
					$water_path .= '.' . $info['extension'];
					switch(true) {
						case preg_match("/jpg|jpeg|JPG|JPEG/", $info['extension']):
							if (imagetypes() & IMG_JPG) {
								$water_img = imagecreatefromjpeg($water_path);
							}
							break;
						case preg_match("/png/", $info['extension']):
							if (imagetypes() & IMG_PNG) {
								$water_img = imagecreatefrompng($water_path);
							}
							break;
						case preg_match("/gif|GIF/", $info['extension']):
							if (imagetypes() & IMG_GIF) { 
								$water_img = imagecreatefromgif($water_path);
							}
							break;
					}
					if (isset($water_img)) {
						$water_x = imagesx($water_img); 
						$water_y = imagesy($water_img);
					
						if (in_array($water_location, array(1,4,7))) {
							$insert_x = 0;
						} elseif (in_array($water_location, array(2,5,8))) {
							$insert_x = (imagesx($dst_img)/2) - ($water_x/2);
						} else {
							$insert_x = imagesx($dst_img) - $water_x;
						}
					
						if (in_array($water_location, array(1,2,3))) {
							$insert_y = 0;
						} elseif (in_array($water_location, array(4,5,6))) {
							$insert_y = (imagesy($dst_img)/2) - ($water_y/2);
						} else {
							$insert_y = imagesy($dst_img) - $water_y;
						}

						if ($water_opacity == 100) {
							imagecopy($dst_img, $water_img, $insert_x, $insert_y, 0, 0, $water_x, $water_y);
						} else {
							$bg_cut = imagecreatetruecolor($water_x, $water_y);
							imagecopy($bg_cut, $dst_img, 0, 0, $insert_x, $insert_y, $water_x, $water_y); 
							imagecopy($dst_img, $water_img, $insert_x, $insert_y, 0, 0, $water_x, $water_y);
							imagecopymerge($dst_img, $bg_cut, $insert_x, $insert_y, 0, 0, $water_x, $water_y, 100-$water_opacity);
						}
					}
				}
			}
			
			if ($type == 'png') {
				imagealphablending($dst_img, false);
				imagesavealpha($dst_img, true);
				imagepng($dst_img, $filename); 
			} elseif ($type == 'gif') {
				imagegif($dst_img, $filename);
			} else {
				imagejpeg($dst_img, $filename, $quality); 
			}

			imagedestroy($dst_img);
			imagedestroy($dst_img_one); 
			imagedestroy($src_img); 
			umask($old_mask);
		}
	}

		////
	// Check GD
	////
	function gdVersion() {
		if (function_exists('exec') && (DS == '/' || (DS == '\\' && MAGICK_PATH_FINAL != 'convert')) && !FORCE_GD) {
			exec(MAGICK_PATH_FINAL . ' -version', $out);
			$test = $out[0];
			if (!empty($test) && strpos($test, ' not ') === false) {
				$bits = explode(' ', $test);
				$version = $bits[2];
				if (version_compare($version, '6.0.0', '>')) {
					exec(str_replace('convert', 'identify', MAGICK_PATH_FINAL) . ' -list resource', $limits);
					if (strpos(strtolower($limits[0]), 'thread') !== false)
					{
						return 5;
					}	
					return 4;
				} else {
					return 3;
				}
			} else {
				return $this->_gd();
			}
		} else {
			return $this->_gd();
		}
	}

	function _gd() {
		if (function_exists('gd_info')) {
			$gd = gd_info();
			$version = ereg_replace('[[:alpha:][:space:]()]+', '', $gd['GD Version']);
			settype($version, 'integer');
			return $version;
	 	} else {
			return 0;
		}
	}
}

?>