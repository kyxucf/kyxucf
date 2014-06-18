<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $acctbl ADD last_schedule_check INT(11) DEFAULT NULL");
	$now = strtotime('-1 day');
	@$this->_query("UPDATE $acctbl set last_schedule_check = $now");
	
?>