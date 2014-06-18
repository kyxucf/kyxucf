<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $itbl ADD album_active TINYINT(1) DEFAULT 0");
	@$this->_query("CREATE INDEX album_active ON $itbl (album_active)");	
	
	@$this->_query("UPDATE $itbl SET album_active = 1");
	
	App::import('Model', 'Album');
	$this->Album =& new Album();

	$inactives = $this->Album->find('all', array('conditions' => 'active = 0', 'recursive' => -1, 'fields' => 'id'));
	
	$ids = array();
	foreach($inactives as $a) {
		$ids[] = $a['Album']['id'];
	}
	
	@$this->_query("UPDATE $itbl SET album_active = 1");
	@$this->_query("UPDATE $itbl SET album_active = 0 WHERE aid IN (" . join(',', $ids) . ")");
	
?>