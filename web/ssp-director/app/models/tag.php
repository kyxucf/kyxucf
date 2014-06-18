<?php

class Tag extends AppModel {
    var $name = 'Tag';
	var $useTable = 'dynamic_links';

	var $belongsTo = array('Album' =>
                           array('className'  => 'Album',
                                 'foreignKey' => 'aid'
                           ),
						   'Gallery' =>
						   array('className'  => 'Gallery',
								 'foreignKey' => 'did',
								 'order'      => 'main DESC'
						   )
                     );
				
	var $actsAs = array('Containable');
	
	function afterFind($result) {
		if (empty($result) || !isset($result[0]['Tag'])) { return $result; }
		App::import('Model', 'Album');
		$this->Album =& new Album();
		$result[0]['Tag'] = $this->Album->afterFind($result[0]['Tag']);
		return $result;
	}
	
	////
	// callbacks to clear the cache
	////
	function afterSave($created) {
		if ($created) {
			App::import('Model', 'Gallery');
			$this->Gallery =& new Gallery();
			$tag = $this->read(null, $this->id);
			$this->Gallery->id = $tag['Gallery']['id'];
			$this->Gallery->reorder($tag['Gallery']['id']);
			$this->Gallery->saveField('tag_count', $tag['Gallery']['tag_count'] + 1);
		}
		$this->popCache();
		return true;
	}
	
	function beforeDelete() {
		$id = $this->id;
		if (is_array($id)) {
			$id = $id['id'];
		}
		App::import('Model', 'Gallery');
		$this->Gallery =& new Gallery();
		$this->recursive = 1;
		$tag = $this->read(null, $id);
		$this->Gallery->id = $tag['Gallery']['id'];
		$this->Gallery->saveField('tag_count', $tag['Gallery']['tag_count'] - 1);
		$this->popCache();
		return true;
	}
	
	function popCache() {
		$id = $this->id;
		if (is_array($id)) {
			$id = $id['id'];
		}
		App::import('Model', 'Gallery');
		$this->Gallery =& new Gallery();
		$this->recursive = 1;
		$tag = $this->read(null, $id);
		$targets = array("images_gallery_{$tag['Tag']['did']}", "images_gid_{$tag['Tag']['did']}");
		$this->clearCache($targets);
		$this->Gallery->id = $tag['Tag']['did'];
		$this->Gallery->saveField('modified_on', $this->Gallery->gm());
	}
}

?>