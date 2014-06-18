<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $acctbl ADD first_time TINYINT(1) DEFAULT 1");
	
?>