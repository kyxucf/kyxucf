<?php

	if (!defined('MIGRATE')) {exit;}
	
	// Do stuff here every upgrade
	
	$result = $this->_query("SELECT id FROM $dtbl");
	if ($this->_rows($result) > 0) {
		$ids = array();
		while($row = $this->_array($result)) {				
			$ids[] = $row['id'];
		}
		$id_str = join(',', $ids);
		$this->_query("DELETE FROM $dltbl WHERE did NOT IN ($id_str)");
	}
	
	@$this->_query("DELETE FROM $dtbl WHERE name IS NULL");
	@$this->_query("DELETE FROM $dltbl WHERE aid IS NULL OR did IS NULL");


	// Clean XML cache and model cache
	$this->_clean(XML_CACHE);
	$this->_clean(CACHE . DS . 'models');
	$this->_clean(CACHE . DS . 'director');
	
	$this->Cookie->del('Login');
	$this->Cookie->del('Pass');
	$this->Session->delete('User');
		
	@$this->_query("UPDATE $acctbl SET version = '$version'");

?>