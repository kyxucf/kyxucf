<?php

	if (!defined('MIGRATE')) {exit;}
	
	$oldies = $this->_query("SELECT id, path FROM $atbl WHERE path NOT LIKE 'album-%' AND path IS NOT NULL LIMIT 1");
	if ($this->_rows($oldies) == 1) {
		$row = $this->_array($oldies);
		$old = ALBUMS . DS . $row['path'];
		$path = 'album-' . $row['id'];
		$new = ALBUMS . DS . $path;
		if (is_dir($old)) {
			$f = new Folder($old);
			$f->chmod($old, 0777);
			if ($f->move($new)) {
				$this->_query("UPDATE $atbl SET path = '$path-old-{$row['path']}' WHERE id = {$row['id']}");
			}
		} else {
			$this->_query("UPDATE $atbl SET path = '$path-old-{$row['path']}' WHERE id = {$row['id']}");
		}
		die('again');
	}
	
?>
