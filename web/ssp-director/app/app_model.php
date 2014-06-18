<?php

////
// Needed for RSS parsing and shared cache clearing function
////
class AppModel extends Model {
	
	////
	// Catch database failure for all models and redirect to error page
	////
	function onError() {
		if (strpos(env('QUERY_STRING'), 'install') === false) {
			header('Location: ' . Configure::read('App.baseUrl') . '/site/db_error');
			exit;
		}
	}
	
	function cleanTags($value) {
		$value = rtrim(preg_replace('/,+/', ',', r(' ', ',', trim($value))), ',') . ',';
		if ($value == ',') {
			$value = '';
		}
		return $value;
	}
	
	////
	// Clear cache
	////
	function clearCache($files, $api = null) {
		if (!empty($files)) {
			umask(0);
			@chmod(XML_CACHE, 0777);
			foreach($files as $file) {
				$caches = glob(XML_CACHE . DS . "$file*");
				if (!empty($caches)) {
					foreach($caches as $cache) {
						@unlink($cache);
					}
				}
				
				$caches = glob(CACHE . DS . 'xml' . DS . "$file*");
				if (!empty($caches)) {
					foreach($caches as $cache) {
						@unlink($cache);
					}
				}
			}
		}
		
		if (!is_null($api)) {
			$master_list = unserialize(cache('api' . DS . 'master.cache', null, '+1 year'));
			$masters_to_clear = array();
			$all = array();
			foreach($api as $a) {
				if (in_array('curl', get_loaded_extensions())) {
					$curl = true;
				} else {
					$curl = false;
				}
				cache('api' . DS . $a . '.cache', null, '-1 day');
				$masters_to_clear[] = $a;
					preg_match('/(get_albums_list|get_gallery_list|get_album|get_gallery|get_content_list|get_content|get_users|get_associated_galleries)/', $a, $matches);
				@$basename = $matches[1];
				$masters_to_clear[] = $basename;
				$invalidator = CACHE . 'api' . DS . 'invalidators' .  DS . $basename . '.cache';
				if (file_exists($invalidator)) {
					$contents = unserialize(file_get_contents($invalidator));
					if (!empty($contents)) {
						foreach($contents as $c) {
							if (isset($all[$c['path']]) && !in_array($c['name'], $all[$c['path']])) {
								$all[$c['path']][] = $c['name'];
							} else {
								$all[$c['path']] = array($c['name']);
							}
						}
					}
					@unlink($invalidator);
				}

				$f = new Folder();
				if (strpos($a, 'content') || strpos($a, 'album')) {
					$f->delete(CACHE . DS . 'api' . DS . "get_content_list");
					$masters_to_clear[] = 'get_content_list';
				}
				
				if (strpos($a, 'users')) {
					$f->delete(CACHE . DS . 'api' . DS . "get_users");
					$masters_to_clear[] = 'get_users';
				}
				
				if ($a == 'get_albums_list') {
					$f->delete(CACHE . DS . 'api' . DS . "get_albums_list");
					$masters_to_clear[] = 'get_albums_list';
				}
				
				if (strpos($a, 'get_gallery_') !== false) {
					$f->delete(CACHE . DS . 'api' . DS . "get_gallery");
					$bits = explode('_', $a);
					$masters_to_clear[] = $bits[0] . '_' . $bits[1] . '/' . $bits[2];
				}
				
				if (strpos($a, 'get_album_') !== false) {
					$f->delete(CACHE . DS . 'api' . DS . "get_album");
					$bits = explode('_', $a);
					$to_clear = $bits[0] . '_' . $bits[1] . '/' . $bits[2];
					$masters_to_clear[] = $to_clear;
					$masters_to_clear[] = $to_clear . '_0';
					$masters_to_clear[] = $to_clear . '_1';
				}
			}
						
			if (!empty($masters_to_clear) && !empty($master_list)) {
				foreach($master_list as $key => $val) {
					if (in_array($val['type'], $masters_to_clear)) {
						cache('api' . DS . $val['file'] . '.cache', null, '-1 day');
						unset($master_list[$key]);
						cache('api' . DS . 'master.cache', serialize($master_list));
					}
				} 
			}

			foreach($all as $host => $name) {
				$name = implode(',', $name);
				if ($curl) {
					$call = $host . '?name=' . $name;
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, "http://$call");
					curl_setopt($ch, CURLOPT_HEADER, 0);
					@curl_exec($ch);
					curl_close($ch);
				} else {
					$split = strpos($host, '/');
					$host = substr($host, 0, $split);
					$path = substr($host, $split) . '?name=' . $name;
					$headers = "GET $path HTTP/1.0\r\n";
					$headers .= "Host: {$host}\r\n";
					$headers .= "Connection:close\r\n\r\n";
					$socket = @fsockopen($host, 80, $errno, $errstr, 5);
					if ($socket) {
						fwrite($socket, $headers);
					} 
					fclose($socket);
				}
			}
		}
	}
	
	function beforeSave() {
		if ($this->hasField('created_by') && empty($this->id) && defined('CUR_USER_ID')) { 
			$this->data[$this->name]['created_by'] = CUR_USER_ID;
        }
		if ($this->hasField('modified') && defined('CUR_USER_ID')) {
			$this->data[$this->name]['updated_by'] = CUR_USER_ID;
		}
		$gmt = $this->gm();
		if ($this->hasField('created_on') && empty($this->id)) { 
			$this->data[$this->name]['created_on'] = $gmt;
        }
		if ($this->hasField('modified_on')) {
			$this->data[$this->name]['modified_on'] = $gmt;
		}
		return true;
	}
	
	function gm() {
		return time() - date('Z');
	}
}

?>