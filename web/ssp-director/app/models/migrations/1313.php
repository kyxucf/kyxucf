<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("CREATE INDEX start_on ON $itbl (start_on)");
	@$this->_query("CREATE INDEX end_on ON $itbl (end_on)");
	
?>