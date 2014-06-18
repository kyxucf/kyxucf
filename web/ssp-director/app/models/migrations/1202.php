<?php

	if (!defined('MIGRATE')) {exit;}
	
	App::import('Model', 'Image');
	$this->Image =& new Image();

	$images = $this->Image->find('all', array('conditions' => aa('created_on', null), 'fields' => 'Image.id, Image.modified, Image.created, Image.created_on', 'recursive' => '-1'));					
	
	foreach($images as $image) {
		if (empty($image['Image']['created_on'])) {
			$created = strtotime($image['Image']['created']);
			$created_gmt = $created - date('Z', $created);
			$modified = strtotime($image['Image']['modified']);
			$modified_gmt = $modified - date('Z', $modified);
			$this->_query("UPDATE $itbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$image['Image']['id']}");
		}
	}

?>