<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("CREATE TABLE $wtbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), position INT(2) DEFAULT 5, main TINYINT(1) DEFAULT 0, opacity INT(4) DEFAULT 60)");	

	@$this->_query("ALTER TABLE $atbl ADD watermark_id INT(11) DEFAULT NULL");
?>