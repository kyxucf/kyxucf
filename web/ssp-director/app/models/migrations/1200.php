<?php
	
	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("DELETE FROM $itbl WHERE aid IS NULL");
	
	// 1.2
	App::import('Model', 'Image');
	$this->Image =& new Image();
	
	@$this->_query("ALTER TABLE $acctbl ADD api_key VARCHAR(255)");
	@$this->_query("ALTER TABLE $itbl ADD filesize INT(11)");
	
	$key = md5(uniqid(rand(), true));
	@$this->_query("UPDATE $acctbl SET api_key = '$key' WHERE api_key IS NULL OR api_key = ''");
	
	$result = $this->_query("SELECT i.*, a.path FROM $itbl AS i, $atbl AS a WHERE i.aid = a.id AND i.filesize IS NULL");
	if ($this->_rows($result) > 0) {
		while($row = $this->_array($result)) {				
			$size = filesize(ALBUMS . DS . $row['path'] . DS . 'lg' . DS . $row['src']);
			$this->_query("UPDATE $itbl SET filesize = $size WHERE id = {$row['id']}");
		}
	}
	
	$result = $this->_query("SELECT * FROM $dtbl WHERE main = 1");
	if ($this->_rows($result) == 0) {
		$now = time() - date('Z');
		$this->_query("INSERT INTO $dtbl(name, description, main, created_on, modified_on) VALUES('All albums', 'This gallery contains all published albums.', 1, $now, $now)");
		$new = $this->_query("SELECT * FROM $dtbl WHERE main = 1 LIMIT 1");
		$noob = $this->_insert_id();
		$actives = $this->_query("SELECT * FROM $atbl WHERE active = 1");
		if ($this->_rows($actives) > 0) {
			$i = 0;
			while($row = $this->_array($actives)) {				
				$this->_query("INSERT INTO $dltbl(aid, did) VALUES({$row['id']}, $noob)");
				$i++;
			}
			$this->_query("UPDATE $dtbl SET tag_count = $i WHERE id = $noob");
		}
	}
	
	@$this->_query("ALTER TABLE $utbl ADD last_seen INT(10)");
	@$this->_query("ALTER TABLE $utbl ADD display_name VARCHAR(255)");
	@$this->_query("UPDATE $utbl SET display_name = $utbl.usr");
	@$this->_query("ALTER TABLE $atbl ADD created_on INT(10)");
	@$this->_query("ALTER TABLE $atbl ADD modified_on INT(10)");
	@$this->_query("ALTER TABLE $itbl ADD created_on INT(10)");
	@$this->_query("ALTER TABLE $itbl ADD modified_on INT(10)");
	@$this->_query("ALTER TABLE $dtbl ADD created_on INT(10)");
	@$this->_query("ALTER TABLE $dtbl ADD modified_on INT(10)");
	@$this->_query("ALTER TABLE $utbl ADD created_on INT(10)");
	@$this->_query("ALTER TABLE $utbl ADD modified_on INT(10)");
	
	@$this->_query("ALTER TABLE $itbl ADD captured_on INT(10)");
	@$this->_query("ALTER TABLE $itbl DROP meta");
	
	@$this->_query("ALTER TABLE $utbl ADD first_name VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $utbl ADD last_name VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $utbl ADD profile TEXT");
	@$this->_query("ALTER TABLE $utbl ADD externals TEXT");
	@$this->_query("ALTER TABLE $utbl ADD anchor VARCHAR(255) DEFAULT NULL");

	@$this->_query("ALTER TABLE $itbl ADD tags TEXT");
	@$this->_query("ALTER TABLE $acctbl ADD grace TINYINT(1) DEFAULT 0");
	@$this->_query("ALTER TABLE $dtbl ADD tag_count INT(11) DEFAULT 0");
	
	@$this->_query("CREATE INDEX updated_by ON $atbl (updated_by)");
	@$this->_query("CREATE INDEX created_by ON $atbl (created_by)");
	@$this->_query("CREATE INDEX updated_by ON $dtbl (updated_by)");
	@$this->_query("CREATE INDEX created_by ON $dtbl (created_by)");
	@$this->_query("CREATE INDEX updated_by ON $itbl (updated_by)");
	@$this->_query("CREATE INDEX created_by ON $itbl (created_by)");
	@$this->_query("CREATE INDEX aid ON $itbl (aid)");
	@$this->_query("CREATE INDEX aid ON $dltbl (aid)");
	@$this->_query("CREATE INDEX did ON $dltbl (did)");
	
	@$this->_query("CREATE INDEX src ON $itbl (src)");
	@$this->_query("CREATE INDEX modified_on ON $itbl (modified_on)");
	@$this->_query("CREATE INDEX created_on ON $itbl (created_on)");
	@$this->_query("CREATE INDEX modified_on ON $atbl (modified_on)");
	@$this->_query("CREATE INDEX created_on ON $atbl (created_on)");
	@$this->_query("CREATE INDEX modified_on ON $dtbl (modified_on)");
	@$this->_query("CREATE INDEX created_on ON $dtbl (created_on)");
	@$this->_query("CREATE INDEX images_count ON $atbl (images_count)");
	@$this->_query("CREATE INDEX tag_count ON $dtbl (tag_count)");
	@$this->_query("CREATE INDEX main ON $dtbl (main)");
	@$this->_query("CREATE INDEX seq ON $itbl (seq)");
	@$this->_query("CREATE INDEX active ON $atbl (active)");
	@$this->_query("CREATE INDEX captured_on ON $itbl (captured_on)");
	@$this->_query("CREATE INDEX display ON $dltbl (display)");
	
	$results = $this->_query("SELECT * FROM $dtbl");
	while ($row = $this->_array($results)) {
		$r = $this->_query("SELECT COUNT(id) FROM $dltbl WHERE did = {$row['id']}");
		$irow = $this->_row($r);
		$this->_query("UPDATE $dtbl SET tag_count = {$irow[0]} WHERE id = {$row['id']}");
	}	
	
?>