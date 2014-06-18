<?php

	if (!defined('MIGRATE')) {exit;}

	@$this->_query("ALTER TABLE $wtbl ADD fn VARCHAR(255) DEFAULT NULL");
?>