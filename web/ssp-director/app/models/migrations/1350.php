<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("ALTER TABLE $itbl ADD lg_preview VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $itbl ADD tn_preview VARCHAR(255) DEFAULT NULL");
	@$this->_query("ALTER TABLE $itbl ADD lg_preview_id INT(11) DEFAULT NULL");
	@$this->_query("ALTER TABLE $itbl ADD tn_preview_id INT(11) DEFAULT NULL");
	@$this->_query("CREATE INDEX lg_preview_id ON $itbl (lg_preview_id)");
	@$this->_query("CREATE INDEX tn_preview_id ON $itbl (tn_preview_id)");
	
?>