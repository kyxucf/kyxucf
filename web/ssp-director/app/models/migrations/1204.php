<?php

	if (!defined('MIGRATE')) {exit;}
	
	App::import('Model', 'Gallery');
	$this->Gallery =& new Gallery();

	$galleries = $this->Gallery->find('all', array('conditions' => aa('created_on', null), 'fields' => 'Gallery.id, Gallery.created, Gallery.modified, Gallery.created_on', 'recursive' => -1));

	foreach($galleries as $gallery) {
		if (empty($galleries['Gallery']['created_on'])) {
			$created = strtotime($gallery['Gallery']['created']);
			$created_gmt = $created - date('Z', $created);
			$modified = strtotime($gallery['Gallery']['modified']);
			$modified_gmt = $modified - date('Z', $modified);
			$this->_query("UPDATE $dtbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$gallery['Gallery']['id']}");
		}
	}

?>