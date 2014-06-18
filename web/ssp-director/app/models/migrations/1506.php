<?php

	if (!defined('MIGRATE')) {exit;}
	
	$gals = $this->_query("SELECT id FROM $dtbl WHERE internal_id IS NULL LIMIT 20");
	if ($this->_rows($gals) > 0) {
		while($row = $this->_array($gals)) {
			$token = md5(uniqid(rand(), true));
			$this->_query("UPDATE $dtbl SET internal_id = '$token' WHERE id = {$row['id']}");
		}
		die('again');
	}
	
?>
