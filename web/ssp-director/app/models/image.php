<?php

class Image extends AppModel {
    var $name = 'Image';
	var $coldSave = false;
	var $belongsTo = array('Album' =>
                           array('className'  => 'Album',
                                 'foreignKey' => 'aid'
                           )
                     );

	var $actsAs = array('Containable');
	
	function beforeSave() {
		if (isset($this->data['Image']['tags'])) {
			$this->data['Image']['tags'] = $this->cleanTags($this->data['Image']['tags']);
		}
		return parent::beforeSave();
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave() {
		if (!$this->coldSave) {
			$this->popCache();
			$this->Album->refreshSmartCounts();
		} else {
			$this->popApi();
		}
		return true;
	}
	
	function afterFind($results) {
		if (isset($results[0]['Image']['tags'])) {
			for($i = 0; $i < count($results); $i++) {
				if (!empty($results[$i]['Image']['tags'])) {
					$results[$i]['Image']['tags'] = trim(r(',', ' ', $results[$i]['Image']['tags']));
				}
			}
		}
		return $results;
	}
	
	function beforeDelete() {
		if (isset($this->data['Image'])) {
			$bit = $this->data['Image']['src'] . ':' . $this->data['Image']['aid'];
			$this->Album->updateAll(array('aTn' => "NULL", 'preview_id' => 0), array('aTn LIKE' => "$bit:%"));
		}
		
		if (!$this->coldSave) {
			@$this->popCache(false);
		} else {
			$this->popApi();
		}
		$this->clearFiles($this->data);
		return true;
	}
	
	function clearFiles($image) {
		$album_path = 'album-' . $image['Image']['aid'];
		// Delete it from the filesystem if no other albums use this path
		$path = ALBUMS . DS . $album_path . DS;
		@unlink($path . 'lg' . DS . $image['Image']['src']);
		$this->clearCaches($image['Image']['src'], $path);
		if ($image['Image']['is_video']) {
			$frames = glob($path . '*' . DS . '__vidtn__' . $image['Image']['id'] . '_*');
			if (!empty($frames)) {
				foreach($frames as $f) {
					@unlink($f);
				}
			}
		}
	}
	
	function clearCaches($str, $path) {
		$caches = glob($path . DS . 'cache' . DS . $str . '*');
		if (!empty($caches)) {
			foreach($caches as $cache) {
				@unlink($cache);
			}
		}
	}
	
	function popApi() {
		$id = $this->id;
		$api_targets = array('get_content_' . $id, 'get_users', 'get_content_list');
		$this->clearCache(array(), $api_targets);
	}
	
	function popCache($save = true) {
		$id = $this->id;
		$image = $this->read();
		$this->popApi();
		$album_id = $image['Image']['aid'];
		$this->cacheQueries = false;
		$count = $this->find('count', array('conditions' => array('aid' => $album_id, 'active' => 1), 'recursive' => -1));
		$vcount = $this->find('count', array('conditions' => array('aid' => $album_id, 'is_video' => 1, 'active' => 1), 'recursive' => -1));
		if (!$save && $image['Image']['active']) {
			$count -= 1;
			if ($image['Image']['is_video']) {
				$vcount -= 1;
			}
		}
		$this->Album->id = $album_id;
		$this->Album->recursive = -1;
		$album = $this->Album->read();
		if ($album && !$album['Album']['smart']) {
			$data['Album']['images_count'] = $count;
			$data['Album']['video_count'] = $vcount;
			$data['Album']['modified_on'] = $this->Album->gm();
			$this->Album->save($data);
		}
	}
}

?>