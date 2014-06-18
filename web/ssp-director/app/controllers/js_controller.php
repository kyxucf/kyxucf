<?php

class JsController extends AppController {
	// Models needed for this controller
	var $uses = array();
	var $disableSessions = true;
	
	function translate($lang) {
		Configure::write('Config.language', $lang);
		$this->render('translate', 'ajax');
	}
}

?>