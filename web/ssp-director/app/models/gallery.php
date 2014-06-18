<?php

class Gallery extends AppModel {
    var $name = 'Gallery';
	var $useTable = 'dynamic';

	var $hasMany = array('Tag' => 
						array('className'  => 'Tag',
							  'foreignKey' => 'did',
							  'dependent'  => true,
							  'order'      => 'display'
						)
	               );

	var $actsAs = array('Containable');

	function afterFind($result) {
		if (!isset($result[0]['Gallery'])) { return $result; } 
		if (!array_key_exists('description', $result[0]['Gallery'])) { return $result; }
		for($i = 0; $i < count($result); $i++) {
			$description = $result[$i]['Gallery']['description'];
			if (empty($description)) {
				$result[$i]['Gallery']['description_clean'] = __('This gallery does not have a description.', true);
			} else {
				$result[$i]['Gallery']['description_clean'] = $description;
			}
		}
		return $result;
	}
	
	function beforeSave() {
		if (empty($this->id)) {
			$this->data['Gallery']['internal_id'] = md5(uniqid(rand(), true));
		}
		return parent::beforeSave();
	}
	////
	// callbacks to clear the cache
	////
	function afterSave() {
		$this->popCache();
		return true;
	}
	
	function beforeDelete() {
		$this->popCache();
		return true;
	}
	
	function popCache() {
		$id = $this->id;
		$targets = array("images_gid_{$id}", "images_gallery_{$id}");
		$api_targets = array('get_gallery_list', 'get_gallery_' . $id);
		$apis = glob(CACHE . 'api' . DS . 'get_associated_*');
		foreach($apis as $a) {
			if (!is_dir($a)) {
				$api_targets[] = basename($a);
			}
		}
		$this->clearCache($targets, $api_targets);
	}
	
	function isMain($id) {
		$this->id = $id;
		$gallery = $this->read();
		return $gallery['Gallery']['main'];
	}
	
	function members($data) {
		if (!empty($data['Tag'])) {
			$ids = array();
			foreach($data['Tag'] as $t) {
				if (is_numeric($t['aid'])) {
					$ids[] = $t['aid'];
				}
			}
			if (empty($ids)) {
				$albums = array();
			} else {
				$id_str = implode(',', $ids);
				$albums = $this->Tag->Album->find('all', array('conditions' => array('id' => $ids), 'order' => "FIELD(Album.id, $id_str)", 'recursive' => -1));
			}
		} else {
			$albums = array();
		}
		return $albums;
	}
	
	////
	// Reorder based on preset
	////
	function reorder($id) {
		// On really large galleries, this might take a while
		if (function_exists('set_time_limit')) {
			set_time_limit(0);
		}
		$this->id = $id;
		$this->recursive = 1;
		$gallery = $this->read();
		$albums = $this->members($gallery);
		$order = $gallery['Gallery']['sort_type'];
		App::import('Model', 'Tag');
		$this->Tag =& new Tag();
		if ($order != 'manual') {
			$ids = array();
			switch($order) {
				case('album title (newest first)'):
				case('album title (oldest first)'):
					$names = array();
					foreach($albums as $i => $a) {
						$names[] = $a['Album']['name'] . '__~~__' . $a['Album']['id'];
					}
					natcasesort($names);
					if (strpos($order, 'newest') !== false) {
						$names = array_reverse($names);
					}
					$names = array_values($names);
					$this->Tag->begin();
					for($i = 0; $i < count($names); $i++) {
						$bits = explode('__~~__', $names[$i]);
						$d = $i+1;
						$this->Tag->query("UPDATE " . DIR_DB_PRE . "dynamic_links SET display = $d WHERE aid = {$bits[1]} AND did = $id");
					}
					$this->Tag->commit();
					break;
				default:
					preg_match('/(date|modified) \((.*)\)/', $order, $matches);
					$data = $matches[1];
					$order = $matches[2];
					if ($data == 'date') {
						$sql = '`Album`.created_on';
					} else {
						$sql = '`Album`.modified_on';
					}
					if ($order == 'newest first') { $sql .= ' DESC'; }
					$aids = array();
					foreach($albums as $a) {
						if (is_numeric($a['Album']['id'])) {
							$aids[] = $a['Album']['id']; 
						}
					}
					$aids_str = join(',', $aids);
					$conditions = "`Album`.id IN ($aids_str)";
					$this->Tag->Album->contain();
					$new_albums = $this->Tag->Album->find('all', array(
						'conditions' => $conditions,
						'order' => $sql
					));
					$i = 1;
					$this->Tag->begin();
					foreach($new_albums as $album) {
						if ($album['Album']['id'] != $aids[$i-1]) {
							$this->Tag->query("UPDATE " . DIR_DB_PRE . "dynamic_links SET display = $i WHERE aid = {$album['Album']['id']} AND did = {$gallery['Gallery']['id']}");
						}
						$i++;
					}
					$this->Tag->commit();
					break;
			}
		}
		return true;
	}
	
	function smartConditions($array) {
			$conditions = $array['conditions'];
			if (empty($conditions)) {
				return array();
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
								$_q = "(Album.tags {$bool}LIKE '%{$c['input']},%'";
								if (!$c['bool']) {
									$_q .= ' OR Album.tags IS NULL)';
								} else {
									$_q .= ')';
								}
								$q[] = $_q;
							}
							break;
						case 'date':
							$column = 'Album.' . $c['column'];
							@$offset = $_COOKIE['dir_time_zone'];
							switch($c['modifier']) {	
								case 'on':
									$start = strtotime($c['start'] . ' 00:00:00') - $offset;
									$end = strtotime($c['start'] . ' 23:59:59') - $offset;
									$q[] = "$column {$bool}BETWEEN $start AND $end";
									break;
								case 'before':
									$start = strtotime($c['start'] . ' 00:00:00') - $offset;
									$q[] = "{$bool}($column < $start) AND $column IS NOT NULL AND $column <> 0";
									break;
								case 'after':
									$start = strtotime($c['start'] . ' 23:59:59') - $offset;
									$q[] = "{$bool}($column > $start)";
									break;
								case 'between':
									$start = strtotime($c['start'] . ' 00:00:00') - $offset;
									$end = strtotime($c['end'] . ' 23:59:59') - $offset;
									$q[] = "$column {$bool}BETWEEN $start AND $end";
									break;
								case 'within':
									$end_str = date('Y-m-d') . ' 23:59:59';
									$end = strtotime($end_str);
									$start = strtotime($end_str . ' -' . $c['within'] . ' ' . $c['within_modifier'] . 's');
									$q[] = "{$bool}($column > $start)";
									break;
							}
							break;
					}
				}
			}	
			if (empty($q)) {
				$images = array();
			} else {
				$condition_for_query = '(' . join($sep, $q) . ') AND Album.active = 1';
				if (isset($array['limit_to']) && is_numeric($array['limit_to'])) {
					$condition_for_query .= ' AND Album.smart = ' . $array['limit_to'];
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
						$albums = $this->Tag->Album->find('all', array('conditions' => $condition_for_query, 'fields' => 'Album.id, Album.name', 'recursive' => -1));
						if (empty($albums)) {
							return array();
						}
						$files = array();
						foreach($albums as $a) {
							$files[] = $a['Album']['name'] . '__~~__' . $a['Album']['id'];
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
						$order_sql = "FIELD(Album.id, $ids)";
						break;
					default:
						if ($order == 'date') {
							$col = 'created_on';
						} else {
							$col = 'modified_on';
						}
						$order_sql = "Album.$col {$array['order_direction']}";
						break;
				}
				return array($condition_for_query, $order_sql, $limit);
			}
		}

		function refreshSmartCounts() {
			$smarties = $this->find('all', array('conditions' => array('Gallery.smart' => 1, 'not' => array('Gallery.main' => 1)), 'fields' => 'Gallery.smart_query, Gallery.id, Gallery.tag_count', 'recursive' => -1));
			if (!empty($smarties)) {
				$this->begin();
				foreach($smarties as $s) {
					$id = $s['Gallery']['id'];
					$q = $s['Gallery']['smart_query'];
					@list($conditions, $order, $limit) = $this->smartConditions(unserialize($q));
					if (!empty($conditions)) {
						$count = $this->Tag->Album->find('count', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => -1));
					
						$this->id = $id;
						$this->saveField('tag_count', $count);
					}
				}
				$this->commit();
			}
		}
}

?>