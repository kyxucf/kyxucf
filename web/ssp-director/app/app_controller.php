<?php

App::import('Vendor', 'asset_packager/asset_helper');

class AppController extends Controller {
	var $components = array('Director', 'RequestHandler', 'Kodak', 'Cookie', 'Pigeon');
	var $helpers = array('Asset', 'Director', 'Form');
	var $cookieKey = '7651029347yt0918h34t03';
	var $cookieName = 'DIRECTORDISTCOOKIE';
		
	////
	// Catch database errors
	////
	function appError($method, $params) {
		switch($method) {
			case 'missingTable':
				$this->webroot = str_replace('index.php?/', '', Configure::read('App.baseUrl') . '/app/webroot/');
				$this->viewPath = 'site';
				e($this->render('db_error', 'simple'));
				exit;
				break;
		}
	}
	
	////
	// Session check
	////
    function checkSession() {
        if ($this->Session->check('User')) {
			list($this->account, $users) = $this->Director->fetchAccount($this->action);
			if ($this->account['Account']['version'] != DIR_VERSION) {
				$allowable = array('1.1', '1.2', '1.3', '1.4', '1.5');
				$main = substr($this->account['Account']['version'], 0, 3);
				if (!in_array($main, $allowable)) {
					$this->webroot = str_replace('index.php?/', '', Configure::read('App.baseUrl') . '/app/webroot/');
					$this->viewPath = 'site';
					e($this->render('legacy_upgrade_error', 'simple'));
					exit;
				}
				if (isset($this->account['Account']['lang'])) {
					$this->Session->write('Language', $this->account['Account']['lang']);
				}
				$this->redirect("/install/upgrade");
				exit;
			} else if ($this->account['Account']['db_version'] != DB_VERSION) {
				$this->redirect("/install/upgrade");
				exit;
			}
			if (date('mdY', $this->account['Account']['last_schedule_check']) != date('mdY', time())) {
				$this->Director->scheduling();
				App::import('Model', 'Account');
				$this->Account =& new Account();
				$this->Account->id = $this->account['Account']['id'];
				$this->Account->saveField('last_schedule_check', time());
			}
			if ($this->account['Account']['trial']) {
				App::import('Model', 'Image');
				$this->Image =& new Image();
				$count = $this->Image->find('count');
				$state = 1;
				if ($count >= 50) {
					$state = 3;
				} else if ($count > 25) {
					$state = 2;
				}
				define('TRIAL_STATE', $state);
			} else if ((strtotime($this->account['Account']['last_check']) < time() || strtotime($this->account['Account']['last_check']) > strtotime('+1 week')) && $this->action != "activate" && !$this->Pigeon->isLocal()) {
				list($this->account, $users) = $this->Director->fetchAccount('activate');
				list($code, $response) = $this->Pigeon->activate($this->account['Account']['activation_key'], false);
				if ($code == 2 && $this->account['Account']['grace'] == 0) {
					$this->data['Account']['last_check'] = date('Y-m-d H:i:s', strtotime('+2 days'));
					$this->data['Account']['grace'] = 1;
					App::import('Model', 'Account');
					$this->Account =& new Account();
					$this->Account->id = $this->account['Account']['id'];
					$this->Account->save($this->data);
				} elseif ($code != 0) { 
					$this->data['Account']['grace'] = 0;
					App::import('Model', 'Account');
					$this->Account =& new Account();
					$this->Account->id = $this->account['Account']['id'];
					$this->Account->save($this->data);
					$this->redirect('/accounts/activate');
					exit;
				} else {
					App::import('Model', 'Account');
					$this->Account =& new Account();
					$this->Account->id = $this->account['Account']['id'];
					$this->data['Account']['last_check'] = date('Y-m-d H:i:s', strtotime('+1 week'));
					$this->Account->save($this->data);
				}
			}
			if (!defined('TRIAL_STATE')) {
				define('TRIAL_STATE', false);
			}
			$this->set('account', $this->account);
			$this->set('users', $users);
			$user = $this->Session->read('User');
			$this->set('user', $user);
			if (isset($user['lang'])) {
				$lang = $user['lang'];
			} else {
				$lang = $this->account['Account']['lang'];
			}
			
			Configure::write('Config.language', $lang);
			
			if (!defined('CUR_USER_ID')) {
				define('CUR_USER_ID', $this->Session->read('User.id'));	
				define('MAX_SIZE', $this->Director->returnBytes(ini_get('upload_max_filesize')));
				define('DIR_GD_VERSION', $this->Kodak->gdVersion());
			}
			$this->set('shows', $this->Director->fetchShows($this->account['Account']['id']));
			$this->set('controller', $this);
		} else if ($this->Cookie->read('Login')) {
			App::import('Model', 'User');
			$this->User =& new User(); 
			$someone = $this->User->findByUsr($this->Cookie->read('Login'));
            if (!empty($someone['User']['pwd']) && md5($someone['User']['pwd']) == $this->Cookie->read('Pass')) {
	        	$this->Session->write('User', $someone['User']);
				$this->User->id = $someone['User']['id'];
				$this->User->coldSave = true;
				$this->User->saveField('last_seen', $this->User->gm());
				$this->User->coldSave = false;
				$redirect_to = $this->Session->read('redirect_to');
				if (strrpos($redirect_to, '/') == (strlen($redirect_to)-1)) {
					$redirect_to = '';
				}
				if (empty($redirect_to)) {
					$location = $this->here;
				} else {
					$location = $redirect_to;
				}
				header("Location: $location");
	            exit;
			} else {
				// Force the user to login, record where they wanted to go
				if (!$this->Session->read('redirect_to')) {
					$this->Session->write('redirect_to', $this->here);
				}
	            $this->redirect("/users/login");
	            exit;
			}
		} else {
            // Force the user to login, record where they wanted to go
			if (!$this->Session->read('redirect_to')) {
				$this->Session->write('redirect_to', $this->here);
			}
			$here = explode('/index.php?', $this->here);
			$here = $here[count($here)-1];
			if ($here == '/' || $here == '/snapshot') {
            	$this->redirect("/users/login");
			} else {
				$this->redirect("/snapshot");
			}
            exit;
        }
    }

	////
	// Make sure ajax calls are actual ajax calls
	////
	function verifyAjax() {
		$this->layout = false;
		if (AJAX_CHECK) {
			if (!$this->RequestHandler->getAjaxVersion()) {
				$this->redirect("/");
				exit;
			}
		}
	}
	
	function verifyRight($level) {
		if ($this->Session->read('User.perms') >= $level) {
			return true;
		} else {
			$this->redirect("/");
            exit;
		}
	}
}

?>