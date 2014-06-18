<?php

class UsersController extends AppController
{
    var $name = 'Users';
	var $helpers = array('Html', 'Javascript', 'Ajax');
	var $components = array('Cookie');
	
	// No session check for these actions
	var $no_check = array('login', 'password', 'send_password');
	// Which actions should have simple layout
	var $simpletons = array('login', 'password', 'send_password', 'logout');
	// Verify that any action *not* in this list is an Ajax call
	var $non_ajax_actions = array('index', '_list', 'profile', 'login', 'logout', 'password', 'clear_avatar', 'avatar_uri', 'theme', 'language');

	// Only logged in users should see this controller's actions
 	function beforeFilter() {
		// Protect ajax actions
		if (!in_array($this->action, $this->non_ajax_actions)) {
			$this->verifyAjax();
		}
		
		// Check session
		if (!in_array($this->action, $this->no_check)) {
			$this->checkSession();
		}
		
		$this->set('controller', $this);
		
		if (in_array($this->action, $this->simpletons)) {
			$this->layout = 'simple';
			list($this->account, $users) = $this->Director->fetchAccount();
			$this->set('account', $this->account);
			Configure::write('Config.language', $this->account['Account']['lang']);
		}
	}
	
	function check() {
		$check = $this->User->find('first', array('conditions' => "usr = '{$this->data['User']['usr']}' OR email = '{$this->data['User']['usr']}'"));
		if (empty($check)) {
			exit;
		} else {
			header("HTTP/1.0 404 Not Found");
		}
	}
	
	////
	// Manage users
	////
	function index() {
		$this->verifyRight(3);
		$this->pageTitle = __('Users', true);
		$this->_list();
	}
	
	////
	// Acts as both the login display and actual login ui
	////
	function login() {
		$this->pageTitle = __('Sign in', true);

        if (!empty($this->data)) {
			$this->User->recursive = -1;
            $someone = $this->User->findByUsr($this->data['User']['usr']);
            if (!empty($someone['User']['pwd']) && $someone['User']['pwd'] == $this->data['User']['pwd']) {
				if ($this->Session->write('User', $someone['User'])) {
					if (isset($this->data['remember'])) {
						$this->Cookie->write('Login', $someone['User']['usr'], true, YEAR);
						$this->Cookie->write('Pass', md5($someone['User']['pwd']), true, YEAR);
					}
					$this->User->id = $someone['User']['id'];
					$this->User->coldSave = true;
					$this->User->saveField('last_seen', $this->User->gm());
					$this->User->coldSave = false;
					$redirect_to = $this->Session->read('redirect_to');
					if (strrpos($redirect_to, '/') == (strlen($redirect_to)-1)) {
						$redirect_to = '';
					}
					if (empty($redirect_to)) {
	                	$this->redirect('/snapshot');
					} else {
						$this->Session->delete('redirect_to');
						header("Location: $redirect_to");
						exit();
					}
				} else {
					$this->set('error', 'session');
				}
            } else {
                $this->set('error', __('Sign in incorrect', true));
            }
        }
    }

	////
	// Password/login retrieval
	////
	function password() {
		$this->pageTitle = __('Password Reminder', true);
	}
	
	////
	// Password/login retrieval action
	////
	function send_password() {
		if ($this->data) {
			$cred = $this->data['User']['cred'];
			$user = $this->User->find('first', array('conditions' => array('or' => array('usr' => $cred, 'email' => $cred)), 'recursive' => -1));
			if (empty($user)) {
				$success = 0;
			} else {
				if (empty($user['User']['email'])) {
					$success = 1;
				} else {
					$path = DIR_HOST;
					$email = $user['User']['email'];
					$usr = $user['User']['usr'];
					$pwd = $user['User']['pwd'];
				
					$message = __('This is a password reminder sent from SlideShowPro Director.',  true) . "\n\n------------------------------\n\n";
					$message .= __('Sign in here', true) . ': ' . $path . "/\n";
					$message .= __('Username', true) . ": $usr\n";
					$message .= __('Password', true) . ": $pwd\n\n";

					$headers = 'From: ' . $email . "\n";
					$headers .= "Content-Type: text/plain;charset=UTF-8";

					$subject = __('SlideShowPro Director Sign In Reminder', true);
					
					if (function_exists('mb_convert_encoding')) {
						$subject = mb_convert_encoding($subject, "ISO-8859-1", "UTF-8");
						$subject = mb_encode_mimeheader($subject);
					}
					
					if (mail($email, $subject, $message, $headers)) {
						$success = 3;
					} else {
						$success = 2;
					}
				}
			}
			$this->set('success', $success);
			$this->render('send_password', 'ajax');
	 	} else {
			exit;
		}
	}
	
	////
	// Log the user out
	////
	function logout() {	
		$this->pageTitle = __('Sign out', true);
		$this->Cookie->del('Login');
		$this->Cookie->del('Pass');
        $this->Session->delete('User');
    }

	////
	// Edit user profile
	////
	function profile($id = null) {
		$viewer = $this->Session->read('User');
		if (is_null($id)) {
			$id = $this->Session->read('User.id');
		}
		$this->data = $this->User->read(null, $id);
		// Admins can't view/edit master account
		if ($viewer['perms'] == 3 && $this->data['User']['perms'] == 4) {
			$this->redirect('/users');
		}
		
		uses('Folder');
		$themes_folder = new Folder(THEMES);
		$themes = $themes_folder->ls(true, false);
		$this->set('themes', $themes[0]);
		$custom_themes_folder = new Folder(USER_THEMES);
		$custom_themes_templates = $custom_themes_folder->ls(true, array('sample', '.', '..', '.svn'));
		$this->set('custom_themes', $custom_themes_templates[0]);
		$lang_folder = new Folder(APP . DS . 'locale');
		$langs = $lang_folder->ls(true, false);
		$this->set('langs', $langs[0]);
		
		$this->pageTitle = __('User Profile', true);
		$this->set('viewer', $viewer);
	}
	
	function theme($new_theme, $id) {
		$this->User->id = $id;
		$theme = r('--', '/', strtolower($new_theme));
		$this->User->recursive = -1;
		$this->User->saveField('theme', '/' . $theme . '.css');
		$this->_rewrite();
		$this->redirect('/users/profile/' . $id);
		exit;
	}
	
	function language($new_lang, $id) {
		$this->User->id = $id;
		$this->User->recursive = -1;
		$this->User->saveField('lang', $new_lang);
		Configure::write('Config.language', $new_lang);
		$this->_rewrite();
		$this->redirect('/users/profile/' . $id);
	}
	
	function _rewrite() {
		$u = $this->User->find('id=' . CUR_USER_ID);
		$this->Session->write('User', $u['User']);
	}
	
	function clear_avatar($id) {
		$oldies = glob(AVATARS . DS . $id . DS . 'original.*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$oldies = glob(AVATARS . DS . $id . DS . 'cache' . DS . '*');
		foreach($oldies as $o) {
			unlink($o);
		}
		$this->User->id = $id;
		$this->User->saveField('anchor', null);
		$this->redirect('/users/profile/' . $id);
		exit;
	}
	
	function avatar_uri($id, $w, $h, $x, $y) {
		$avs = glob(AVATARS . DS . $id . DS . 'original.*');
		$t = filemtime($avs[0]);
		e(__p(array('src' => basename($avs[0]),
		 			'album_id' => "avatar-$id",
		 			'width' => $w,
		 			'height' => $h,
		 			'square' => 0,
		 			'anchor_x' => $x,
		 			'anchor_y' => $y)));
		exit;
	}
	
	function avatar_focal($id) {
		$this->User->id = $id;
		$this->User->saveField('anchor', serialize($this->data));
		if (CUR_USER_ID == $id) {
			$u = $this->User->find(aa('id', $id));
			$this->Session->write('User', $u['User']);
		}
		$avs = glob(AVATARS . DS . $id . DS . 'original.*');
		$t = filemtime($avs[0]);
		$url = __p(array(	'src' => basename($avs[0]), 
							'album_id' => "avatar-$id",
							'width' => 100,
							'height' => 100,
							'anchor_x' => $this->data['x'],
							'anchor_y' => $this->data['y']));
		die('<img src="' . $url . '" />');
	}
	
	////
	// Update a user record
	////
	function update($id) {
		$this->User->id = $id;
		if (!empty($this->params['form']['pass1'])) {
			$this->data['User']['pwd'] = $this->params['form']['pass1'];
		}
		if (isset($this->data['User']['externals_1_name'])) {
			$externals = array();
			$externals[] = array('name' => $this->data['User']['externals_1_name'], 'url' => str_replace('http://', '', $this->data['User']['externals_1_url']));
			$externals[] = array('name' => $this->data['User']['externals_2_name'], 'url' => str_replace('http://', '', $this->data['User']['externals_2_url']));
			$externals[] = array('name' => $this->data['User']['externals_3_name'], 'url' => str_replace('http://', '', $this->data['User']['externals_3_url']));
			$this->data['User']['externals'] = serialize($externals);
		}
		$this->User->save($this->data);
		$u = $this->data = $this->User->find(aa('id', $id));
		if (CUR_USER_ID == $id) {
			$this->Session->write('User', $u['User']);
		}
	}
	
	function update_options() {
		$this->User->id = $id = $this->data['User']['id'];
		$this->User->save($this->data);
		if (CUR_USER_ID == $id) {
			$u = $this->User->find(aa('id', $id));
			$this->Session->write('User', $u['User']);
		}
		exit;
	}
	
	////
	// Delete a user
	////
	function delete() {
		$this->User->del($this->data['User']['id']);
		$this->_list();
		$this->render('users', 'ajax');
	}
	
	////
	// Create user
	////
	function create() {
		if (!empty($this->data['User'])) {
			$from = $this->Session->read('User');
			$this->data['User']['email'] = $this->data['User']['usr'];
			$this->data['User']['lang'] = $this->account['Account']['lang'];
			$this->data['User']['theme'] = $this->account['Account']['theme'];
			if ($this->User->save($this->data)) {
				$this->User->id = $this->User->getLastInsertId();
				$pwd = $this->Director->randomStr();
				$this->User->saveField('pwd', $pwd);
				$path = DIR_HOST;
				$email = $this->data['User']['usr'];
			
				$message = $this->params['form']['message'];
				$message .= "\n\n------------------------------\n\n";
				$message .= __('Sign in here', true) . ': ' . $path . "/\n";
				$message .= __('Username', true) . ": $email\n";
				$message .= __('Password', true) . ": $pwd\n\n";
				$message .= __('Once you sign in you can change your password to something more familiar.', true);

				$headers = __('From', true) . ': ' . $from['email'] . "\n";
				$headers .= "Content-Type: text/plain;charset=UTF-8";
			
				$subject = __('SlideShowPro Director Sign In Info', true);
				if (function_exists('mb_convert_encoding')) {
					$subject = mb_convert_encoding($subject, "ISO-8859-1", "UTF-8");
					$subject = mb_encode_mimeheader($subject);
				}
			
				mail($email, $subject, $message, $headers);
				$this->data['User'] = null;
				$this->_list();
				$this->render('users', 'ajax');
			}
		}
	}
	
	////
	// Get list of users for manage actions
	////
	function _list() {
		$this->set('users_hot', $this->User->findAll());
		$this->set('users', $this->Director->fetchUsers());
		$this->set('viewer', $this->Session->read('User'));
	}
}

?>