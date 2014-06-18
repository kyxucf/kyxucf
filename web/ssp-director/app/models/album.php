<?php

class Album extends AppModel {
    var $name = 'Album';
	var $components = array('Director');

	var $hasMany = array('Image' =>
	              		array('className'  => 'Image',
	                      	  'foreignKey' => 'aid',
							  'order' 	   => 'seq, active, src'	
	                	),
						'Tag' => 
						array('className'  => 'Tag',
							  'foreignKey' => 'aid'
						)
	               );

	var $actsAs = array('Containable');
	
	function bindPreview() {
		$hasOne = array('Preview' =>
							array(	'className' 	=> 'Image',
								  	'foreignKey' => false,
								   	'conditions' 	=> 'Preview.id = Album.preview_id'));
		$this->bindModel(array('hasOne' => $hasOne));
	}
	
	function beforeSave() {
		if (empty($this->id)) {
			$this->data['Album']['internal_id'] = md5(uniqid(rand(), true));
		}
		if (isset($this->data['Album']['tags'])) {
			$this->data['Album']['tags'] = $this->cleanTags($this->data['Album']['tags']);
		}
		if (isset($this->data['Album']['aTn'])) {
			if (isset($this->data['Album']['preview_id'])) {
				if (($this->data['Album']['preview_id'] == 0 && $this->data['Album']['aTn'] == '') || $this->data['Album']['preview_id'] > 0) {
					// Nothing to do, leave it be
				} else {
					unset($this->data['Album']['aTn']);
				} 
			} else {
				unset($this->data['Album']['aTn']);
			}
		}
		return parent::beforeSave();
	}
	
	function beforeFind($queryData) {
		if (is_array($queryData['conditions'])) {
			$queryData['conditions'][] = "Album.name <> ''";
		} else {
			if (!empty($queryData['conditions'])) {
				$queryData['conditions'] .= " AND ";
			}
			$queryData['conditions'] .= "Album.name <> ''";
		}
		return $queryData;
	}
	
	function afterFind($result) {
		if (!isset($result[0]['Album'])) { return $result; } 
		for($i = 0; $i < count($result); $i++) {
			if (array_key_exists('description', $result[$i]['Album'])) {
				$description = $result[$i]['Album']['description'];
				if (empty($description)) {
					$result[$i]['Album']['description_clean'] = __('This album does not have a description.', true);
				} else {
					$result[$i]['Album']['description_clean'] = $description;
				}
			}

			if (isset($result[$i]['Album']['tags'])) {
				$result[$i]['Album']['tags'] = trim(r(',', ' ', $result[$i]['Album']['tags']));
			}
			
			if (array_key_exists('aTn', $result[$i]['Album']) && empty($result[$i]['Album']['aTn'])) {
				$first = array();
				if ($result[$i]['Album']['smart']) {
					if (!empty($result[$i]['Album']['smart_query'])) {
						list($conditions, $order, $limit) = $this->smartConditions(unserialize($result[$i]['Album']['smart_query']), null);
						if (!is_null($conditions)) {
							$conditions .= " AND (is_video = 0 OR lg_preview_id > 0)";
							$members = $this->Image->find('all', array('conditions' => $conditions, 'limit' => 1, 'order' => $order, 'recursive' => -1));
							foreach($members as $image) {
								if ($image['Image']['is_video']) {
									if (!empty($image['Image']['lg_preview'])) {
										list($src, $x, $y) = explode(':', $image['Image']['lg_preview']);
										$result[$i]['Album']['aTn'] = "$src:{$image['Image']['aid']}:$x:$y";
										$result[$i]['Album']['preview_sub'] = $image['Image']['lg_preview_id'];
										break;
									}
								} else {
									$first = $image;
									break;
								}
							}
						}
					}
				} else {
					$first = $this->Image->find('first', array('conditions' => array('aid' => $result[$i]['Album']['id'], 'is_video' => 0), 'order' => 'seq ASC, active DESC', 'recursive' => -1));
				}
				if (!empty($first)) {
					$anchor = unserialize($first['Image']['anchor']);
					if (empty($anchor)) {
						$x = $y = 50;
					} else {
						$x = $anchor['x'];
						$y = $anchor['y'];
					}
					$result[$i]['Album']['aTn'] = $first['Image']['src'] . ':' . $first['Image']['aid'] . ':' . $x . ':' . $y; 
					$result[$i]['Album']['preview_sub'] = $first['Image']['id'];
				}
			}
		}
		return $result;
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave($created) {
		if ($created) {
			$api_targets = array('get_album_list', 'get_albums_list');
			$this->clearCache(array(), $api_targets);
		} else {
			$this->popCache();
		}
		App::import('Model', 'Tag');
		$this->Tag =& new Tag();
		$tags = $this->Tag->find('all', array('conditions' => array('aid' => $this->id), 'recursive' => -1, 'fields' => 'Tag.did'));
		if (!empty($tags)) {
			foreach($tags as $tag) {
				$this->Tag->Gallery->reorder($tag['Tag']['did']);
			}
		}
		$this->Tag->Gallery->refreshSmartCounts();
		return true;
	}
	
	function beforeDelete() {
		$this->popCache();
		return true;
	}
	
	function popCache($album = null) {
		if (is_null($album) || !isset($album['Tag'])) {
			$id = $this->id;
			$this->contain('Tag');
			$album = $this->read();
		} else {
			$id = $album['Album']['id'];
		}
		
		$targets = array("images_album_{$id}", "images_album_.*,{$id}_");
		$api_targets = array("get_album_{$id}", 'get_album_list', 'get_albums_list');
		if (!empty($album['Tag'])) {	
			$api_targets[] = 'get_gallery_list';
			foreach ($album['Tag'] as $tag) {
				$targets[] = 'images_gid_' . $tag['did'];
				$targets[] = 'images_gallery_' . $tag['did'];
				$api_targets[] = 'get_gallery_' . $tag['did'];
			}
		}
		$smarts = $this->Tag->Gallery->find('all', array('conditions' => array('smart' => 1), 'recursive' => -1, 'fields' => 'Gallery.id'));
		foreach($smarts as $s) {
			$targets[] = 'images_gallery_' . $s['Gallery']['id'];
			$api_targets[] = 'get_gallery_' . $s['Gallery']['id'];
		}
		$this->clearCache($targets, $api_targets);
	}
	
	////
	// Quickly return images in array
	////
	function returnImages($id) {
		$this->id = $id;
		$album = $this->read();
		return $album['Image'];
	}
	
	////
	// Reorder based on preset
	////
	function reorder($id, $manual = false) {
		// On really large albums, this might take a while
		if (function_exists('set_time_limit')) {
			set_time_limit(0);
		}
		$this->id = $id;
		$this->recursive = -1;
		$album = $this->read();
		$order = $album['Album']['sort_type'];
		$this->Image->coldSave = true;
		switch($order) {
			case('manual'):
				if ($manual) {
					$this->Image->recursive = -1;
					$images = $this->Image->find('all', array('conditions' => "aid = $id", 'order' => 'seq'));
					$i = 0;
					$this->Image->begin();
					foreach($images as $image) {
						$d = $i + 1;
						if ($image['Image']['seq'] != $d) {
							$this->Image->query("UPDATE " . DIR_DB_PRE . "images SET seq = $d WHERE id = {$image['Image']['id']}");
						}
						if ($image['Image']['active']) {
							$i++;
						}
					}
					$this->Image->commit();
				}
				break;
			case('file name (oldest first)'):
			case('file name (newest first)'):
				$images = $this->Image->find('all', array('conditions' => array('aid' => $id), 'recursive' => -1));
				$files = array();
				foreach($images as $i) {
					$files[] = $i['Image']['src'] . '__~~__' . $i['Image']['id'] . '__~~__' . $i['Image']['active'] . '__~~__' . $i['Image']['seq'];
				}
				natcasesort($files);
				if (strpos($order, 'newest') !== false) {
					$files = array_reverse($files);
				}
				$files = array_values($files);
				$seq = 0;
				$this->Image->begin();
				for($i = 0; $i < count($files); $i++) {
					$bits = explode('__~~__', $files[$i]);
					$d = $seq + 1;
					if ($bits[3] != $d) {
						$this->Image->query("UPDATE " . DIR_DB_PRE . "images SET seq = $d WHERE id = {$bits[1]}");
					}
					if ($bits[2]) {
						$seq++;
					}
				}
				$this->Image->commit();
				break;
			default:
				preg_match('/(date|captured|modified) \((.*)\)/', $order, $matches);
				$data = $matches[1];
				$order = $matches[2];
				switch($data) {
					case 'date':
						$column = 'created_on';
						break;
					case 'modified':
						$column = 'modified_on';
						break;
					default: 
						$column = 'captured_on';
						break;
				}
				$sql = "`Image`.$column";
				if ($order == 'newest first') { $sql .= ' DESC'; }
				$images = $this->Image->find('all', array('conditions' => array('aid' => $id), 'recursive' => -1, 'order' => $sql));
				$seq = 0;
				$this->Image->begin();
				for($i = 0; $i < count($images); $i++) {
					$d = $seq + 1;
					if ($images[$i]['Image']['seq'] != $d) {
						$this->Image->query("UPDATE " . DIR_DB_PRE . "images SET seq = $d WHERE id = {$images[$i]['Image']['id']}");
					}
					if ($images[$i]['Image']['active']) {
						$seq++;
					}
				}
				$this->Image->commit();
				break;
		}
		$this->Image->coldSave = false;
		return true;
	}
	
	function smartConditions($array, $no_video = false) {
		$conditions = $array['conditions'];
		if (empty($conditions)) {
			return array(null, null, null);
		} else {
			if ($array['any_all']) {
				$sep = ' AND ';
			} else {
				$sep = ' OR ';
			}
			$q = array();
			foreach($conditions as $c) {
				$bool = '';
				if (isset($c['bool']) && !$c['bool']) {
					$bool = 'NOT ';
				}
				switch($c['type']) {
					case 'tag':
						if (!empty($c['input'])) {
							$_q = "(Image.tags {$bool}LIKE '%{$c['input']},%'";
							if (!$c['bool']) {
								$_q .= ' OR Image.tags IS NULL)';
							} else {
								$_q .= ')';
							}
							if ($c['filter'] != 'all') {
								if ($c['filter'] == 0) {
									$_q = $_q . ' AND Image.album_active = 1';
								} else if ($c['filter'] != 'all') {
									$_q = $_q . ' AND Image.aid = ' . $c['filter'];
								}
							}
							$q[] = $_q;
						}
						break;
					case 'album':
						if (!empty($c['filter'])) {
							$_q = $bool . '(Image.aid = ' . $c['filter'] . ')';
							if ($bool == 'NOT ') {
								$_q .= ' AND Image.album_active = 1';
							}
							$q[] = $_q;
						}
						break;
					case 'date':
						$column = 'Image.' . $c['column'];
						@$offset = $_COOKIE['dir_time_zone'];
						switch($c['modifier']) {	
							case 'on':
								$start = strtotime($c['start'] . ' 00:00:00') - $offset;
								$end = strtotime($c['start'] . ' 23:59:59') - $offset;
								$q[] = "$column {$bool}BETWEEN $start AND $end AND Image.album_active = 1";
								break;
							case 'before':
								$start = strtotime($c['start'] . ' 00:00:00') - $offset;
								$q[] = "{$bool}($column < $start) AND $column IS NOT NULL AND $column <> 0 AND Image.album_active = 1";
								break;
							case 'after':
								$start = strtotime($c['start'] . ' 23:59:59') - $offset;
								$q[] = "{$bool}($column > $start) AND Image.album_active = 1";
								break;
							case 'between':
								$start = strtotime($c['start'] . ' 00:00:00') - $offset;
								$end = strtotime($c['end'] . ' 23:59:59') - $offset;
								$q[] = "$column {$bool}BETWEEN $start AND $end AND Image.album_active = 1";
								break;
							case 'within':
								$end_str = date('Y-m-d') . ' 23:59:59';
								$end = strtotime($end_str);
								$start = strtotime($end_str . ' -' . $c['within'] . ' ' . $c['within_modifier'] . 's');
								$q[] = "{$bool}($column > $start) AND Image.album_active = 1";
								break;
						}
						break;
				}
			}
		}	
		if (empty($q)) {
			$images = array();
		} else {
			$condition_for_query = '(' . join($sep, $q) . ') AND Image.active = 1';

			if ($no_video) {
				$condition_for_query .= ' AND is_video = 0';
			} else if (isset($array['limit_to']) && is_numeric($array['limit_to'])) {
				$condition_for_query .= ' AND is_video = ' . $array['limit_to'];
			}
			
			if (is_numeric($array['limit'])) {
				$limit = $array['limit'];
			} else {
				$limit = null;
			}
			
			$order = $array['order'];
			$no_results = false;
			switch($order) {
				case 'file':
					$images = $this->Image->find('all', array('conditions' => $condition_for_query, 'fields' => 'Image.id, Image.src, Image.album_active', 'recursive' => -1));
					if (empty($images)) {
						return array();
					}
					$files = array();
					foreach($images as $i) {
						$files[] = $i['Image']['src'] . '__~~__' . $i['Image']['id'];
					}
					natcasesort($files);
					$ids = array();
					foreach ($files as $f) {
						$bits = explode('__~~__', $f);
						$ids[] = $bits[1];
					}
					if ($array['order_direction'] == 'DESC') {
						$ids = array_reverse($ids);
					}
					$ids = join(',', $ids);
					$order_sql = "FIELD(Image.id, $ids)";
					break;
				default:
					if ($order == 'date') {
						$col = 'created_on';
					} else if ($order == 'modified') {
						$col = 'modified_on';
					} else {
						$col = 'captured_on';
					}
					$order_sql = "Image.$col {$array['order_direction']}";
					break;
			}
			return array($condition_for_query, $order_sql, $limit);
		}
	}
	
	function refreshSmartCounts() {
		$this->Behaviors->attach('Containable');
		$smarties = $this->find('all', array('conditions' => array('Album.smart' => 1), 'fields' => 'Album.smart_query, Album.id, Album.images_count, Album.video_count', 'contain' => 'Tag'));
		if (!empty($smarties)) {
			$this->begin();
			foreach($smarties as $s) {
				$id = $s['Album']['id'];
				$q = $s['Album']['smart_query'];
				@list($conditions, $order, $limit) = $this->smartConditions(unserialize($q));
				if (!empty($conditions)) {
					if (strpos($conditions, 'is_video = 1') !== false) {
						$count = $v_count = $this->Image->find('count', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => -1));
						if (is_numeric($limit) && $count > $limit) {
							$count = $v_count = $limit;
						}
					} else if (strpos($conditions, 'is_video = 0') !== false) {
						$v_count = 0;
						$count = $this->Image->find('count', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => -1));
						if (is_numeric($limit) && $count > $limit) {
							$count = $limit;
						}
					} else {
						$items = $this->Image->find('all', array('conditions' => $conditions, 'fields' => 'Image.is_video', 'limit' => $limit, 'order' => $order, 'recursive' => -1));
						$count = count($items);
						$v_count = 0;
						foreach($items as $item) {
							if ($item['Image']['is_video']) {
								$v_count++;
							}
						}
					}
					
					if ($s['Album']['images_count'] == $count && $s['Album']['video_count'] == $v_count) {
						$this->popCache($s);
					} else {
						$this->id = $id;
						$data = array();
						$data['Album']['images_count'] = $count;
						$data['Album']['video_count'] = $v_count;
						$this->save($data);
					}
				}
			}
			$this->commit();
		}
	}
}

?>