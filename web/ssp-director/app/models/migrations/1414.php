<?php

	if (!defined('MIGRATE')) {exit;}
	App::import('Model', 'Image');
	$this->Image =& new Image();

	$inactives = $this->Image->find('all', array('conditions' => 'active = 0', 'recursive' => -1, 'fields' => 'aid'));
	$albums = array();
	
	if (!empty($inactives)) {
		foreach($inactives as $i) {
			if (!in_array($i['Image']['aid'], $albums)) {
				$aid = $i['Image']['aid'];
				$albums[] = $aid;
				$this->Image->Album->reorder($aid, true);
				
				$count = $this->Image->find('count', array('conditions' => array('aid' => $aid, 'active' => 1), 'recursive' => -1));
				$vcount = $this->Image->find('count', array('conditions' => array('aid' => $aid, 'is_video' => 1, 'active' => 1), 'recursive' => -1));
				$this->Image->Album->saveField('images_count', $count);
				$this->Image->Album->saveField('video_count', $vcount);
			}
		}
	}
?>