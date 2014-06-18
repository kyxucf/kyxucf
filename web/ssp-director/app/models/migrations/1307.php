<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $utbl ADD theme VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $utbl ADD lang VARCHAR(255) DEFAULT NULL");

	$account = $this->_array($this->_query("SELECT * FROM $acctbl LIMIT 1"));
	
	$lang = $account['lang'];
	$theme = $account['theme'];
	$this->_query("UPDATE $utbl SET theme = '$theme', lang = '$lang'");
	
?>