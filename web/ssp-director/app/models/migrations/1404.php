<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $atbl ADD video_count INT(11) DEFAULT 0");	
	
	App::import('Model', 'Album');
	$this->Album =& new Album();

	$albums = $this->Album->find('all', array('conditions' => aa('smart', 0), 'fields' => 'Album.id', 'recursive' => -1));
	$this->Album->begin();
	foreach($albums as $album) {
		$count = $this->Album->Image->find('count', array('conditions' => array('aid' => $album['Album']['id'], 'is_video' => 1), 'recursive' => -1));
		if ($count > 0) {
			$this->Album->id = $album['Album']['id'];
			$this->Album->saveField('video_count', $count);
		}
	}
	
	$this->Album->commit();

?>