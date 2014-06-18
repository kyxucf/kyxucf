<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $atbl ADD smart TINYINT(1) DEFAULT 0");
	@$this->_query("ALTER TABLE $atbl ADD smart_query TEXT");
	@$this->_query("UPDATE $itbl REPLACE(tags, ' ', ',') WHERE tags IS NOT NULL AND tags <> ''");
	@$this->_query("UPDATE $itbl SET tags = CONCAT(tags, ',') WHERE tags IS NOT NULL AND tags <> ''");
	@$this->_query("ALTER TABLE $itbl ADD is_video TINYINT(1) DEFAULT 0");
	@$this->_query("UPDATE $itbl SET is_video = 1 WHERE src LIKE '%.flv' OR src LIKE '%.f4v' OR src LIKE '%.mov' OR src LIKE '%.mp4' OR src LIKE '%.m4a' OR src LIKE '%.m4v' OR src LIKE '%.3gp' OR src LIKE '%.3g2' OR src LIKE '%.f4v'");
	@$this->_query("CREATE INDEX is_video ON $itbl (is_video)");
	@$this->_query("ALTER TABLE $dtbl ADD smart TINYINT(1) DEFAULT 0");
	@$this->_query("UPDATE $dtbl SET smart = 1 WHERE main = 1");
	@$this->_query("ALTER TABLE $atbl ADD date_taken VARCHAR(20)");
	@$this->_query("ALTER TABLE $atbl ADD place_taken VARCHAR(255)");	
	@$this->_query("ALTER TABLE $atbl CHANGE description description TEXT");
	@$this->_query("ALTER TABLE $itbl ADD anchor VARCHAR(255) DEFAULT NULL");		

?>