<?php

	if (!defined('MIGRATE')) {exit;}
	
	App::import('Model', 'Album');
	$this->Album =& new Album();
				
	$albums = $this->Album->find('all', array('conditions' => aa('created_on', null), 'fields' => 'Album.id, Album.created, Album.modified, Album.created_on', 'recursive' => -1));

	foreach($albums as $album) {
		if (empty($album['Album']['created_on'])) {
			$created = strtotime($album['Album']['created']);
			$created_gmt = $created - date('Z', $created);
			$modified = strtotime($album['Album']['modified']);
			$modified_gmt = $modified - date('Z', $modified);
			$this->_query("UPDATE $atbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$album['Album']['id']}");
		}
	}

?>