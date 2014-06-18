<?php

	if (!defined('MIGRATE')) {exit;}

	$images = $this->_query("SELECT id, tags FROM $itbl WHERE tags IS NOT NULL AND tags <> ''");
	if ($this->_rows($images) > 0) {
		while($row = $this->_array($images)) {
			$tag = $row['tags'];
			$tag = rtrim(preg_replace('/,+/', ',', r(' ', ',', trim($tag))), ',') . ',';
			if ($tag == ',,' || $tag == ',') {
				$tag = '';
			}
			$this->_query("UPDATE $itbl SET tags = '$tag' WHERE id = {$row['id']}");
		}
	}

?>