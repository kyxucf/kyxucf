<?php

	if (!defined('MIGRATE')) {exit;}
	
	$albums = $this->_query("SELECT id FROM $atbl WHERE internal_id IS NULL LIMIT 20");
	if ($this->_rows($albums) > 0) {
		while($row = $this->_array($albums)) {
			$token = md5(uniqid(rand(), true));
			$this->_query("UPDATE $atbl SET internal_id = '$token' WHERE id = {$row['id']}");
		}
		die('again');
	}
	
?>
