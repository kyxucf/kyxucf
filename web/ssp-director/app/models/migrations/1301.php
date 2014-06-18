<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $atbl ADD preview_id INT(11) DEFAULT 0");
	
	$albums = $this->_query("SELECT id, aTn FROM $atbl WHERE aTn IS NOT NULL AND aTn <> ''");
	if ($this->_rows($albums) > 0) {
		while($row = $this->_array($albums)) {
			$tn = $row['aTn'];
			list($tn, $aid) = explode(':', $tn);
			if (empty($aid)) {
				$aid = $row['id'];
			}
			$image = $this->_query("SELECT id,anchor,src FROM $itbl WHERE src = '$tn' AND aid = $aid");
			$i = $this->_array($image);
			list($x, $y) = parse_anchor($i['anchor']);
			$tn = join(':', array($i['src'], $aid, $x, $y));
			if (is_numeric($i['id'])) {
				$this->_query("UPDATE $atbl SET preview_id = {$i['id']}, aTn = '$tn' WHERE id = {$row['id']}");
			}
		}
	}

?>