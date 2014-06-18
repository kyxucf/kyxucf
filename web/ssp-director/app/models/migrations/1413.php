<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("CREATE INDEX watermark_id ON $atbl (watermark_id)");
	@$this->_query("CREATE INDEX main ON $wtbl (main)");
?>