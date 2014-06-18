<?php

class User extends AppModel {
    var $name = 'User';
	var $useTable = 'usrs';
	var $coldSave = false;
					
	function afterFind($result) {
		if (!isset($result[0]['User'])) { return $result; }
		for($i = 0; $i < count($result); $i++) {
			if (isset($result[$i]['User']['lang'])) {
				Configure::write('Config.language', $result[$i]['User']['lang']);
			}
			switch($result[$i]['User']['perms']) {
				case(1):
					$role = __('You are an Editor', true);
					$role_simple = __('Editor', true);
					break;
				case(2):
					$role = __('You are a Contributor', true);
					$role_simple = __('Contributor', true);
					break;
				default:
					$role = __('You are an Administrator', true);
					$role_simple = __('Administrator', true);
					break;
			}
			
			if (empty($result[$i]['User']['display_name'])) {
				$result[$i]['User']['display_name_fill'] = $result[$i]['User']['usr'];
			} else {
				$result[$i]['User']['display_name_fill'] = $result[$i]['User']['display_name'];
			}
			
			$result[$i]['User']['role'] = $role;
			$result[$i]['User']['role_label'] = $role_simple;
		}
		return $result;
	}
	
	function beforeDelete() {
		$this->popCache();
		return true;
	}
	
	function afterSave() {
		$this->popCache();
		return true;
	}
	
	function popCache() {
		if (!$this->coldSave) {
			cache(DIR_CACHE . DS . 'users.cache', null, '-1 day');
			$apis = glob(CACHE . 'api' . DS . '*');
			
			$api_cache = array();
			foreach($apis as $a) {
				if (!is_dir($a)) {
					$api_cache[] = str_replace('.cache', '', basename($a));
				}
			}
			$this->log($api_cache);
			$this->clearCache(array('images'), $api_cache);
		}
	} 
}

?>