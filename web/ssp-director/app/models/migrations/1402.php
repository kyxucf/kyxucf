<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $dtbl ADD smart_query TEXT");

?>