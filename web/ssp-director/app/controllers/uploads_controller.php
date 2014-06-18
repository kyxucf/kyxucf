<?php

class UploadsController extends AppController {
	// Models needed for this controller
    var $name = 'Uploads';
	var $uses = array();
	
	////
	// Accepts file uploads
 	////
	function image($user_id, $id, $upload_type, $tags = '') {	
		$this->loadModel('Image');
		$this->loadModel('Account');
		// Make sure this is coming from the flash player and is a POST request
		if ((strpos(strtolower(env('HTTP_USER_AGENT')), 'flash') === false && !$this->Session->check('User')) || !$this->RequestHandler->isPost()) {
			exit;
		}
		
		$tags = str_replace(' ', ',', urldecode($tags));
		$tags = ereg_replace("[^,A-Za-z0-9._-]", "", $tags);
		if ($tags == 'null' || $tags == 'undefined') {
			$tags = '';
		}
		
		define('CUR_USER_ID', $user_id);
		
		// Make sure permissions are set correctly
		$old_mask = umask(0);
			
		// Get album
		if ($upload_type > 4) {
			$image = $this->Image->find('first', array('conditions' => array('Image.id' => $id), 'recursive' => -1));
			$id = $image['Image']['aid'];
		}
		
		$this->Image->Album->id = $id;
		$album = $this->Image->Album->read();
		$account = $this->Account->find('first');
		
		$album_active = $album['Album']['active']; 
		
		$top = $this->Image->find('first', array('conditions' => "aid = $id", 'order' => 'seq DESC', 'recursive' => -1));
		if (empty($top)) {
			$next = 1;
		} else {
			$next = $top['Image']['seq'];
			if ($top['Image']['active']) {
				$next++;
			}
		}
		// Flash uploads crap out when spaces are in the name
		$file = str_replace(" ", "_", $this->params['form']['Filedata']['name']);
		$file = ereg_replace("[^A-Za-z0-9._-]", "_", $file);

		$this->data['Image']['is_video'] = isVideo($file);
		// Get image extensions so we make sure
		// a safe file is uploaded
		$ext = $this->Director->returnExt($file);
		
		// Paths
		$the_temp = $this->params['form']['Filedata']['tmp_name'];
		$path = ALBUMS . DS . 'album-' . $album['Album']['id'];
		
		$lg_path = $path . DS . 'lg' . DS . $file;
		$lg_temp = $lg_path . '.tmp';
		
		$tn_path = $path . DS . 'tn' . DS . $file;
		$tn_temp = $tn_path . '.tmp';
				
		settype($upload_type, 'integer');
		
		$this->Director->setAlbumPerms($id);
		
		if (in_array($ext, a('jpg', 'jpeg', 'gif', 'png', 'mp3')) || isNotImg($file)) {
			switch($upload_type) {
				// Audio	
				case(4):
					if (is_uploaded_file($the_temp) && $this->Director->setPerms(AUDIO)) {
						$a_tmp = AUDIO . DS . $file . '.tmp';
						move_uploaded_file($the_temp, $a_tmp);
						copy($a_tmp, AUDIO . DS . $file);
						unlink($a_tmp);
						$this->Image->Album->saveField('audioFile', $file);
					}
					break;
				// Standard image or custom thumb
				default:
					if (is_uploaded_file($the_temp) && move_uploaded_file($the_temp, $lg_temp)) {
						copy($lg_temp, $lg_path);
						unlink($lg_temp);
						
						list($meta, $captured_on) = $this->Director->imageMetadata($lg_path);
						$keywords = $this->Director->parseMetaTags('iptc:keywords', $meta);
						$keywords = str_replace(' ', ',', urldecode($keywords));
						$keywords = ereg_replace("[^,A-Za-z0-9._-]", "", $keywords);
						if (!empty($tags)) {
							$keywords = ' ' . trim($keywords);
						}
						$check = $this->Image->find("aid = $id AND src = '$file'");
						
						if (empty($check)) {
							$this->data['Image']['src'] = $file;
							$this->data['Image']['aid'] = $id;
							$this->data['Image']['seq'] = $next;
							$this->data['Image']['filesize'] = filesize($lg_path);
							$this->data['Image']['captured_on'] = (int) $captured_on;
							$this->data['Image']['tags'] = $tags . $keywords;
							$this->data['Image']['album_active'] = $album_active; 
							
							if (in_array($upload_type, array(3,5,6))) {
								$this->data['Image']['active'] = 0;
								$this->data['Image']['seq'] = $next - 1;
							}
							$this->Image->save($this->data);
							$image_id = $this->Image->getLastInsertId();	
											
							if (isVideo($file)) {
								$ffmpeg = $this->Director->ffmpeg();
								
								if ($ffmpeg) {
									$info = pathinfo($file);
									$ext = $info['extension'];
								
									exec(FFMPEG_PATH_FINAL . " -i $lg_path 2>&1", $out);
								
									foreach($out as $line) {
										if (strpos($line, 'Duration') !== false) {
											preg_match('/Duration: ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $line, $matches);
											list(,$h, $m, $s) = $matches;
											$duration = ($h*60*60) + ($m*60) + $s;
											continue;
										} 
									}

									$duration = $duration - 2;
									$bits = ceil($duration/12);
									if ($bits == 0) {
										$bits = 1;
									}
									$rate = 1/$bits;
									if ($rate < 0.1) {
										$rate = 0.1;
									}
								
									$dir = dirname($lg_path) . DS;
									
									$i = 1;
									$cmd = array();
									while ($i < $duration) {
										$i_str = str_pad($i, 5, '0', STR_PAD_LEFT);
										$cmd[] = FFMPEG_PATH_FINAL . " -ss $i -r 1 -i \"$file\" -vframes 1 -an -f mjpeg \"__vidtn__{$image_id}_{$i_str}.jpg\"";
										$i += $bits;
									}
									
									chdir($dir);
									if (DS == '\\') {
										foreach($cmd as $c) {
											exec($c);
										}	
									} else {
										$cmd = join(' && ', $cmd);
										exec($cmd);	
									}
								
									$thumbs = glob($dir . DS . "__vidtn__{$image_id}_*.jpg");
									$tn_file = $lg_path . '.jpg';
								
									if (!empty($thumbs)) {
										copy($thumbs[0], $tn_file);
									}
								
									if (file_exists($tn_file)) {
										$vdata = array();
										$vdata['Image']['src'] = $file . '.jpg';
										$vdata['Image']['aid'] = $id;
										$vdata['Image']['seq'] = $next;
										$vdata['Image']['filesize'] = filesize($tn_file);
										$vdata['Image']['active'] = 0;
										$vdata['Image']['is_video'] = 0;
										$vdata['Image']['album_active'] = $album_active; 
										$this->Image->create();
										$this->Image->save($vdata);
									
										$this->data['Image']['lg_preview_id'] = $this->Image->getLastInsertId();
										$this->data['Image']['lg_preview'] = $file . '.jpg:50:50';
										$this->Image->create();
										$this->Image->id = $image_id;
										$this->Image->save($this->data);
									}
								}
							}
						} else {
							$image_id = $check['Image']['id'];
							$caches = glob(ALBUMS . DS . 'album-' . $check['Album']['id'] . DS . 'cache' . DS . $check['Image']['src'] . '*');
							if (!empty($caches)) {
								foreach($caches as $cache) {
									@unlink($cache);
								}
							}
							$this->Image->id = $image_id;
							$this->data['Image']['captured_on'] = $captured_on;
							$this->data['Image']['filesize'] = filesize($lg_path);
							$this->data['Image']['tags'] = $tags;
							$this->Image->save($this->data);
						}
						if ($upload_type == 3) {
							$album['Album']['aTn'] = "$file:$id:50:50";
							$album['Album']['preview_id'] = $image_id;
							$this->Image->Album->save($album);
						} else if ($upload_type > 4) {
							if ($upload_type == 5) {
								$data = array('lg_preview' => "'$file:50:50'", 'lg_preview_id' => $image_id);
							} else {
								$data = array('tn_preview' => "'$file:50:50'", 'tn_preview_id' => $image_id);
							}
							$this->Image->updateAll($data, array('Image.id' => $image['Image']['id']));
						}
						
						if (is_numeric($account['Account']['archive_w'])) {
							$this->Kodak->develop($lg_path, $lg_path, $account['Account']['archive_w'], $account['Account']['archive_w'], 100);
						}
					}
					break;
			}
		}
		
		// Reset umask
		umask($old_mask);
		@unlink(CACHE . DS . DIR_CACHE . DS . 'users.cache');
		// Exit with some empty space so onComplete always fires in flash/Mac
		exit(' ');
	}
	
	function watermark() {
		if (!is_dir(WATERMARKS)) {
			$this->Director->makeDir(WATERMARKS);
		}
		if ((strpos(strtolower(env('HTTP_USER_AGENT')), 'flash') === false && !$this->Session->check('User')) || !$this->RequestHandler->isPost()) {
			exit;
		}
		$file = str_replace(" ", "_", $this->params['form']['Filedata']['name']);
		$file = ereg_replace("[^A-Za-z0-9._-]", "_", $file);
		$ext = $this->Director->returnExt($file);
		
		$this->loadModel('Watermark');
		$data['Watermark']['name'] = r(".$ext", '', $file);
		$data['Watermark']['fn'] = $file;
		$this->Watermark->save($data);
		
		$id = $this->Watermark->getLastInsertId();
		
		$the_temp = $this->params['form']['Filedata']['tmp_name'];
		
		$path = WATERMARKS . DS . $id . '.' . $ext;
		if (in_array($ext, a('jpg', 'jpeg', 'gif', 'png'))) {
			if (is_uploaded_file($the_temp)) {
				move_uploaded_file($the_temp, $path);
			}
		}
		exit(' ');
	}
	
	function avatar($user_id) {
		if (!is_dir(AVATARS . DS . $user_id)) {
			$this->Director->makeDir(AVATARS . DS . $user_id);
		}
		if ((strpos(strtolower(env('HTTP_USER_AGENT')), 'flash') === false && !$this->Session->check('User')) || !$this->RequestHandler->isPost()) {
			exit;
		}
		$oldies = glob(AVATARS . DS . $user_id . DS . 'original.*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$oldies = glob(AVATARS . DS . $user_id . DS . 'cache' . DS . '*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$ext = $this->Director->returnExt($this->params['form']['Filedata']['name']);
		$the_temp = $this->params['form']['Filedata']['tmp_name'];
		$path = AVATARS . DS . $user_id . DS . 'original.' . $ext;
		if (!is_dir(dirname($path))) {
			$this->Director->makeDir(dirname($path));
		}
		if (in_array($ext, a('jpg', 'jpeg', 'gif', 'png'))) {
			if (is_uploaded_file($the_temp)) {
				move_uploaded_file($the_temp, $path);
			}
		}
		exit(' ');
	}
}

?>