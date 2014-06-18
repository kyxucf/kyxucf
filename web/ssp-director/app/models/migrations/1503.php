<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $atbl ADD internal_id CHAR(32) DEFAULT NULL");
	@$this->_query("ALTER TABLE $dtbl ADD internal_id CHAR(32) DEFAULT NULL");
	
?>