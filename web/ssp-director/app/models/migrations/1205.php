<?php

	if (!defined('MIGRATE')) {exit;}
	
	App::import('Model', 'User');
	$this->User =& new User();

	$users = $this->User->find('all', array('conditions' => aa('created_on', null), 'fields' => 'User.id, User.modified, User.created, User.created_on', 'recursive' => -1));

	foreach($users as $user) {
		if (empty($user['User']['created_on'])) {
			$created = strtotime($user['User']['created']);
			$created_gmt = $created - date('Z', $created);
			$modified = strtotime($user['User']['modified']);
			$modified_gmt = $modified - date('Z', $modified);
			$this->_query("UPDATE $utbl SET created_on = $created_gmt, modified_on = $modified_gmt WHERE id = {$user['User']['id']}");
		}	
	}

?>