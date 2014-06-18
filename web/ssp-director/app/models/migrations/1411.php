<?php

	if (!defined('MIGRATE')) {exit;}

	@$this->_query("ALTER TABLE $wtbl ADD name VARCHAR(255) DEFAULT NULL");
?>