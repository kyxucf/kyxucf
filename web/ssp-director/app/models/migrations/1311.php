<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $itbl ADD start_on INT(11) DEFAULT NULL");
	@$this->_query("ALTER TABLE $itbl ADD end_on INT(11) DEFAULT NULL");
	
?>