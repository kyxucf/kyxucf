<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $acctbl ADD archive_w INT(11) DEFAULT NULL");
	@$this->_query("ALTER TABLE $acctbl ADD caption_template TEXT");
	@$this->_query("ALTER TABLE $acctbl ADD title_template VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $acctbl ADD link_template TEXT");

?>