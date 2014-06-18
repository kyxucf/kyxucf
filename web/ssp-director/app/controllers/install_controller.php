<?php

class InstallController extends AppController
{
    var $name = 'Install';
	var $uses = array();
	var $helpers = array('Html', 'Javascript', 'Director');
	var $components = array('Director', 'Pigeon', 'Cookie', 'Session');
	var $mysqli = false;
	var $link;
	
	function beforeFilter() {
		if ($this->Session->read('Language')) {
			Configure::write('Config.language', $this->Session->read('Language'));
		}
		$this->pageTitle = __("Installing", true); 
		$this->set('config_path', ROOT . DS . 'config');
		$this->set('controller', $this);
		$this->layout = 'simple';
		
		if (version_compare(PHP_VERSION, '5.0.0', 'ge') && function_exists('mysqli_connect')) {
			if (!defined('DIR_DB_INT') || (defined('DIR_DB_INT') && DIR_DB_INT == 'mysqli')) {
				$this->mysqli = true;
			}
		}
	}
	
	////
	// Install landing page
	////
	function index() {
		$lang_folder = new Folder(APP . DS . 'locale');
		$langs = $lang_folder->ls(true, false);
		$actual = array();
		foreach ($langs[0] as $l) {
			if (($l != 'eng' && $l != 'SAMPLE') && file_exists(APP . DS . 'locale' . DS . $l . DS . 'LC_MESSAGES' . DS . 'welcome.po')) {
				$contents = file_get_contents(APP . DS . 'locale' . DS . $l . DS . 'LC_MESSAGES' . DS . 'welcome.po');
				preg_match_all('/msgstr "(.*)"/', $contents, $matches);	
				$actual[] = array(
					'locale' => $l,
					'welcome' => $matches[1][0],
					'action' => $matches[1][1]
				);
			}
		}
		if (empty($actual)) {
			$this->Session->write('Language', 'eng');
			$this->redirect('/install/license');
			exit;
		}
		$this->set('langs', $actual);
	}
	
	function lang($l) {
		$this->Session->write('Language', $l);
		$this->redirect('/install/license');
		exit;
	}
	
	////
	// Install license
	////
	function license() {}
	
	////
	// Activation
	////
	function activate() {
		if (isset($this->data['dummy'])) {
			if ($this->Pigeon->isLocal()) {
				$this->set('local', true);
				$this->Session->write('activation', 'local');
			}
		} elseif (isset($this->data['transfer'])) {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['key'], true);
			if ($code == 0) {
				$this->Session->write('activation', $this->data['Account']['key']);
				$this->redirect('/install/database');
				exit;
			} else {
				$this->set('error', $result);
			}
		} else {
			list($code, $result) = $this->Pigeon->activate($this->data['Account']['key']);
			if ($code == 0) {
				$this->Session->write('activation', $this->data['Account']['key']);
				$this->redirect('/install/database');
				exit;
			} else {
				$this->set('error', $result);
			}
		}
	}
	
	////
	// Perform server check
	////
	function test() {
		if ($this->data) {
			$php = version_compare(PHP_VERSION, '4.3.7', 'ge');
			extension_loaded('mysql') ? $mysql = true : $mysql = extension_loaded('mysqli');
			if (ini_get('safe_mode') == false || ini_get('safe_mode') == '' || strtolower(ini_get('safe_mode')) == 'off') {
				$no_safe_mode = true;
			} else {
				$no_safe_mode = false;
			}
			if ($php && $mysql && $no_safe_mode) {
				$this->set('success', true);
			} else {
				$this->set('success', false);
				$this->set('php', $php);
				$this->set('mysql', $mysql);
				$this->set('no_safe_mode', $no_safe_mode);
			}
		} else {
			$this->redirect('/install');
			exit;
		}
	}
	
	////
	// Enter database details and create config file
	////
	function database() {
		$this->set('db_select_error', false);
		$this->set('connection_error', false);
		$this->set('conf_exists', false);
		$this->set('write_error', false);
		$filename = ROOT . DS . 'config' . DS . 'conf.php';
		
		if ($this->data) {
			if (file_exists($filename)) {
				$this->set('conf_exists', true);
			} else {
				$details = $this->data['db'];
				$server = trim($details['server']);
				$name = trim($details['name']);
				$user = trim($details['user']);
				$pass = trim($details['pass']);
				$prefix = trim($details['prefix']);
				
				if ($this->mysqli) {
					$interface = 'mysqli';
				} else {
					$interface = 'mysql';
				}
				
				$port = $socket = null;
				
				if (strpos($server, ':') !== false) {
					$bits = explode(':', $server);
					$server = $bits[0];
					if (is_numeric($bits[1])) {
						$port = $bits[1];
					} else {
						$socket = $bits[1];
					}
				}
				
				$link = @$this->_connect($server, $user, $pass, $port, $socket);
				if (!$link) {
				    $this->set('connection_error', true);
					$this->set('mysql_error', $this->_error(true));
				} elseif (@!$this->_select($details['name'])) {
					$this->set('db_select_error', true);
					$this->set('mysql_error', $this->_error());
				} else {			
					$fill = "<?php\n\n\t";
					$fill .= '$interface = \''.	$interface	."';\n\t";
					$fill .= '$encoding = \'utf8\'' .";\n\t";
					$fill .= '$host = \''.	$server	."';\n\t";
					$fill .= '$db = \''.	$name	."';\n\t";
					$fill .= '$user = \''.	$user	."';\n\t";
					$fill .= '$pass = \''.	$pass	."';\n\n\t";
					$fill .= '$pre = \''.	$prefix	."';\n\n\t";
					$fill .= '$socket = \''.	$socket	."';\n\t";
					$fill .= '$port = \''.	$port	."';\n\n";
					$fill .= '?>';
			
					$handle = fopen($filename, 'w+');

					if (fwrite($handle, $fill) == false) {
						$this->set('write_error', true);
					} else {
						$this->redirect('/install/register');
						exit;
					}
				}
			}
		} else {
			if (file_exists($filename)) {
				$this->set('conf_exists', true);
			}
		}
	}
	
	////
	// Create the first user
	////
	function register() {}
	
	////
	// Install it already!
	////
	function finish() {
		if ($this->data) {
			$socket = $port = null;
			$check = DIR_PORT;
			if (!empty($check)) {
				if (is_numeric($check)) {
					$port = $check;
				} else {
					$socket = $check;
				}
			}
			$this->_connect(DIR_DB_HOST, DIR_DB_USER, DIR_DB_PASSWORD, $port, $socket);
			$this->_select(DIR_DB);
		
			$atbl = DIR_DB_PRE . 'albums';
			$itbl = DIR_DB_PRE . 'images';
			$dtbl = DIR_DB_PRE . 'dynamic';
			$dltbl = DIR_DB_PRE . 'dynamic_links';
			$stbl = DIR_DB_PRE . 'slideshows';
			$utbl = DIR_DB_PRE . 'usrs';
			$acctbl = DIR_DB_PRE . 'account';
			$wtbl = DIR_DB_PRE . 'watermarks';
		
			$this->set('error', '');
			$key = md5(uniqid(rand(), true));
			$internal_id = md5(uniqid(rand(), true));
			$now = time() - date('Z');
			$queries = array(
					"CREATE TABLE $atbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(100), description TEXT, path VARCHAR(50), tn TINYINT(1) NOT NULL DEFAULT '0', aTn VARCHAR(150), active TINYINT(1) NOT NULL DEFAULT '0', audioFile VARCHAR(100) DEFAULT NULL, audioCap VARCHAR(200) DEFAULT NULL, displayOrder INT(4) DEFAULT '999', target INT(1) NOT NULL DEFAULT '0', images_count INT NOT NULL DEFAULT 0, video_count INT NOT NULL DEFAULT 0, sort_type VARCHAR(255) NOT NULL DEFAULT 'manual', title_template VARCHAR(255), link_template TEXT, caption_template TEXT, modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), updated_by INT(11), created_by INT(11), smart TINYINT(1) DEFAULT 0, smart_query TEXT, place_taken VARCHAR(255), date_taken VARCHAR(20), preview_id INT(11) DEFAULT 0, tags TEXT, watermark_id INT(11) DEFAULT NULL, internal_id CHAR(32) DEFAULT NULL) default charset utf8",
					"CREATE TABLE $itbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), aid INT, title VARCHAR(255), src VARCHAR(255), caption TEXT, link TEXT, active TINYINT(1) NOT NULL DEFAULT '1', seq INT(4) NOT NULL DEFAULT '999', pause INT(4) NOT NULL DEFAULT '0', target INT(1) NOT NULL DEFAULT '0', modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), updated_by INT(11), created_by INT(11), anchor VARCHAR(255) DEFAULT NULL, filesize INT(11), tags TEXT, captured_on INT(10), is_video TINYINT(1) DEFAULT 0, start_on INT(10), end_on INT(10), lg_preview VARCHAR(255), lg_preview_id INT(11), tn_preview VARCHAR(255), tn_preview_id INT(11), album_active TINYINT(1) DEFAULT 0) default charset utf8",
					"CREATE TABLE $utbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), usr VARCHAR(50), pwd VARCHAR(50), email VARCHAR(255), perms INT(2) NOT NULL DEFAULT '1', modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), news TINYINT(1) DEFAULT 1, help TINYINT(1) DEFAULT 1, display_name VARCHAR(255), last_seen INT(10), first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, profile TEXT, externals TEXT, anchor VARCHAR(255) DEFAULT NULL, theme VARCHAR(255) DEFAULT NULL, lang VARCHAR(255) DEFAULT NULL) default charset utf8",
					"CREATE TABLE $dtbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(100), description TEXT, modified DATETIME DEFAULT NULL, created DATETIME DEFAULT NULL, created_on INT(10), modified_on INT(10), main TINYINT(1) DEFAULT 0, sort_type VARCHAR(255) NOT NULL DEFAULT 'manual', updated_by INT(11), created_by INT(11), tag_count INT(11) DEFAULT 0, smart TINYINT(1) DEFAULT 0, smart_query TEXT, internal_id CHAR(32) DEFAULT NULL) default charset utf8",
					"CREATE TABLE $dltbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), did INT, aid INT, display INT DEFAULT '800') default charset utf8",
					"CREATE TABLE $stbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(255), url VARCHAR(255)) default charset utf8",
					"CREATE TABLE $wtbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), name VARCHAR(255), fn VARCHAR(255), position INT(2) DEFAULT 5, main TINYINT(1) DEFAULT 0, opacity INT(4) DEFAULT 60) default charset utf8",
					"CREATE TABLE $acctbl(id INT AUTO_INCREMENT, PRIMARY KEY(id), externals TINYINT(1), internals TINYINT(1), version VARCHAR(255), activation_key VARCHAR(255), last_check DATETIME, last_schedule_check INT(11), theme VARCHAR(255) DEFAULT '/app/webroot/styles/default/default.css', lang VARCHAR(255) DEFAULT 'eng', api_key VARCHAR(255), grace TINYINT(1) DEFAULT 0, db_version VARCHAR(255) DEFAULT '" . DB_VERSION . "', archive_w INT(11) DEFAULT NULL, caption_template TEXT, title_template VARCHAR(255) DEFAULT NULL, link_template TEXT, first_time TINYINT(1) DEFAULT 0) default charset utf8",
					"INSERT INTO $acctbl (id, externals, internals, version, activation_key, last_check, lang, api_key) VALUES (NULL, 1, 1, '" . DIR_VERSION . "', '" . $this->Session->read('activation') . "', '" . date('Y-m-d H:i:s', strtotime('+2 weeks')) . "', '" . $this->Session->read('Language') . "', '" . $key . "')",
					"INSERT INTO $dtbl(name, description, main, created_on, modified_on, smart, internal_id) VALUES('All albums', 'This gallery contains all published albums.', 1, $now, $now, 1, '$internal_id')",
					"CREATE INDEX updated_by ON $atbl (updated_by)",
					"CREATE INDEX created_by ON $atbl (created_by)",
					"CREATE INDEX updated_by ON $dtbl (updated_by)",
					"CREATE INDEX created_by ON $dtbl (created_by)",
					"CREATE INDEX updated_by ON $itbl (updated_by)",
					"CREATE INDEX created_by ON $itbl (created_by)",
					"CREATE INDEX aid ON $itbl (aid)",
					"CREATE INDEX aid ON $dltbl (aid)",
					"CREATE INDEX did ON $dltbl (did)",
					"CREATE INDEX src ON $itbl (src)",
					"CREATE INDEX modified_on ON $itbl (modified_on)",
					"CREATE INDEX created_on ON $itbl (created_on)",
					"CREATE INDEX modified_on ON $atbl (modified_on)",
					"CREATE INDEX created_on ON $atbl (created_on)",
					"CREATE INDEX modified_on ON $dtbl (modified_on)",
					"CREATE INDEX created_on ON $dtbl (created_on)",
					"CREATE INDEX images_count ON $atbl (images_count)",
					"CREATE INDEX tag_count ON $dtbl (tag_count)",
					"CREATE INDEX main ON $dtbl (main)",
					"CREATE INDEX seq ON $itbl (seq)",
					"CREATE INDEX active ON $atbl (active)",
					"CREATE INDEX captured_on ON $itbl (captured_on)",
					"CREATE INDEX display ON $dltbl (display)",
					"CREATE INDEX usr ON $utbl (usr)",
					"CREATE INDEX start_on ON $itbl (start_on)",
					"CREATE INDEX end_on ON $itbl (end_on)",
					"CREATE INDEX album_active ON $itbl (album_active)",
					"CREATE INDEX watermark_id ON $atbl (watermark_id)",
					"CREATE INDEX main ON $wtbl (main)"
				);

			foreach($queries as $query) {
				if (!$this->_query($query) && strpos($query, 'CREATE INDEX') === false) {
					$this->set('error', $this->_error());
					e($this->render());
					exit;
				}
			}
			
			$this->_clean(CACHE . DS . 'models');
			
			$this->loadModel('User');
			$this->User->create();
			$this->data['User']['lang'] = $this->Session->read('Language');
			$this->data['User']['theme'] = '/app/webroot/styles/default/default.css';
			$this->User->save($this->data);
			$user_id = $this->User->getLastInsertId();
						
			$install_migrations = array('1303', '1304', '1305');
			$migrations_path = ROOT . DS . 'app' . DS . 'models' . DS . 'migrations';
			
			define('MIGRATE', true);
			
			// This one needs to run with no user id
			include($migrations_path . DS . "1000.php");
			
			define('CUR_USER_ID', $user_id);
			foreach($install_migrations as $step) {
				include($migrations_path . DS . "$step.php");
			}
		} else {
			$this->redirect('/install');
		}
	}
	
	function update() {
		if (!PRODUCTION) {
			// Don't want to ever run this in development
			die('good');
		}
		App::import('Vendor', 'bradleyboy/updater');
		
		$old_mask = umask(0);
		
		// Move these to off position in case we need to rollback
		$movers = array('app', 'm', 'cron.php', 'images.php', 'index.php', 'p.php', 'popup.php');
		foreach($movers as $m) {
			$path = ROOT . DS . $m;
			$to = $path . '.off';
			if (is_dir($path)) {
				$f = new Folder($path);
				$f->move($to);
				if (is_dir($path)) {
					umask($old_mask);
					rollback($movers);
					die('permfail');
				}
			} else if (file_exists($path)) {
				rename($path, $to);
				if (file_exists($path)) {
					umask($old_mask);
					rollback($movers);
					die('permfail');
				}
			}
		}
		
		$version = trim($this->Pigeon->version(true));
		
		if ((strpos($version, 'b') !== false || strpos($version, 'a') !== false) && BETA_TEST) {
			$core =	'http://www.sspdirector.com/zips/upgrade_beta.zip';
		} else {	
			$core =	'http://www.sspdirector.com/zips/upgrade.zip';
		}
		$zip_helper = 'http://www.sspdirector.com/zips/pclzip.lib.txt';
		$local_core = ROOT . DS . 'core.zip';
		$local_helper = ROOT . DS . 'pclzip.lib.php';
		if (download_file($core, $local_core) && download_file($zip_helper, $local_helper)) {
			require($local_helper);
			$archive = new PclZip('core.zip');
			$archive->extract(PCLZIP_CB_POST_EXTRACT, 'extract_callback');
		} else {
			umask($old_mask);
			rollback($movers);
			die('permfail');
		}
		
		foreach($movers as $m) {
			$path = ROOT . DS . $m;
			$to = $path . '.off';
			if (is_dir($to)) {
				$f = new Folder($path);
				$f->delete($to);
			} else if (file_exists($to)) {
				unlink($to);
			}
		}
		
		unlink($local_core);
		unlink($local_helper);
		umask($old_mask);
		die('good');
	}
	
	////
	// Perform upgrade
	////
	function upgrade($step = 1) {
		if (!$this->Session->check('User')) {
			$this->redirect('/users/login');
		}
		define('CUR_USER_ID', $this->Session->read('User.id'));
		
		// Make sure they have the appropriate version of PHP, as 1.0.8+ now requires 4.3.2+
		if (version_compare(PHP_VERSION, '4.3.7', '>=')) {
			
			if (function_exists('set_time_limit')) {
				set_time_limit(0);
			}
			$this->set('error', false);
			$this->set('step', $step);
			
			$socket = $port = null;
			$check = DIR_PORT;
			if (!empty($check)) {
				if (is_numeric($check)) {
					$port = $check;
				} else {
					$socket = $check;
				}
			}
			
			$this->_connect(DIR_DB_HOST, DIR_DB_USER, DIR_DB_PASSWORD, $port, $socket);
			$this->_select(DIR_DB);

			$version = DIR_VERSION;
			$atbl = DIR_DB_PRE . 'albums';
			$itbl = DIR_DB_PRE . 'images';
			$dtbl = DIR_DB_PRE . 'dynamic';
			$dltbl = DIR_DB_PRE . 'dynamic_links';
			$stbl = DIR_DB_PRE . 'slideshows';
			$utbl = DIR_DB_PRE . 'usrs';
			$acctbl = DIR_DB_PRE . 'account';
			$wtbl = DIR_DB_PRE . 'watermarks';
			
			$migrations_path = ROOT . DS . 'app' . DS . 'models' . DS . 'migrations';
			
			if ($step == 1) {
				$this->_query("ALTER TABLE $atbl DROP testcol");
				$alter = $this->_query("ALTER TABLE $atbl ADD testcol VARCHAR(255)");
				if ($alter) {
					$this->_query("ALTER TABLE $atbl DROP testcol");
					$this->set('alter', false);
					
					$acc = $this->_query("SELECT version, db_version FROM $acctbl WHERE version IS NOT NULL LIMIT 1");
					if (!$acc) {
						$this->_query("ALTER TABLE $acctbl ADD db_version VARCHAR(255)");
						$acc = $this->_query("SELECT version, db_version FROM $acctbl WHERE version IS NOT NULL LIMIT 1");
						$row = $this->_array($acc);
						$version = $row['version'];
						if (version_compare($version, '1.2.6') > 0) {
							$base = 1207;
						} else {
							$base = 1199;
						}
						$this->_query("UPDATE $acctbl SET db_version = $base");
						$acc = $this->_query("SELECT version, db_version FROM $acctbl WHERE version IS NOT NULL LIMIT 1");
					}
					$row = $this->_array($acc);
					$version = $row['version'];
					$db_version = $row['db_version'];
					$allowable = array('1.1', '1.2', '1.3', '1.4', '1.5');
					$main = substr($version, 0, 3);
					if (!in_array($main, $allowable)) {
						$this->set('dated', true);
						$this->set('version', $version);
					} else {
						$this->set('dated', false);
					}
					$migration_files = glob($migrations_path . DS . '*.php');
					$migrations = array();
					$force = array();
					if (isset($_GET['force'])) {
						$force = explode(',', $_GET['force']);
					}
					foreach($migration_files as $m) {
						$candidate = (int) r('.php', '', basename($m));
						if ($candidate > $db_version || in_array($candidate, $force)) {
							$migrations[] = $candidate;
						}
					}
					$this->set('migrations', $migrations);
				} else {
					$this->set('alter', true);
				}
			} else {
				define('MIGRATE', true);
				$this->_clean(CACHE . DS . 'models');
				$this->_clean(CACHE . DS . 'persistent');
				include($migrations_path . DS . "$step.php");
				if ($step != '5000') {
					$this->_query("UPDATE $acctbl SET db_version = $step");
				} else {
					sleep(1);
				}
				$this->_clean(CACHE . DS . 'persistent');
				die('ok');
			}
	 	} else {
			$this->set('error', true);
		}
	}
	
	////
	// Clean a directory
	////
	function _clean($dir) {
		if ($dh = @opendir($dir)) {
			while (($obj = readdir($dh))) {
		       if ($obj=='.' || $obj=='..' || $obj =='.svn') continue;
		       if (!@unlink($dir.'/'.$obj)) $this->Director->rmdirr($dir.'/'.$obj);
		   	}    
		}
	}
	
	function _connect($host, $user, $pass, $port, $socket) {
		if ($this->mysqli) {
			return $this->link = mysqli_connect($host, $user, $pass, null, $port, $socket);
		} else {
			if (!empty($port)) {
				$host = "$host:$port";
			} else if (!empty($socket)) {
				$host = "$host:$socket";
			}
			return $this->link = mysql_connect($host, $user, $pass);
		}	
	}
	
	function _error($connect = false) {
		if ($this->mysqli) {
			if ($connect) {
				return mysqli_connect_error();
			} else {
				return mysqli_error($this->link);
			}
		} else {
			return mysql_error($this->link);
		}
	}
	
	function _query($query) {
		if ($this->mysqli) {
			return mysqli_query($this->link, $query);
		} else {
			return mysql_query($query, $this->link);
		}
	}
	
	function _select($db) {
		if ($this->mysqli) {
			return mysqli_select_db($this->link, $db);
		} else {
			return mysql_select_db($db, $this->link);
		}
	}
	
	function _array($result) {
		if ($this->mysqli) {
			return mysqli_fetch_array($result);
		} else {
			return mysql_fetch_array($result);
		}
	}
	
	function _row($result) {
		if ($this->mysqli) {
			return mysqli_fetch_row($result);
		} else {
			return mysql_fetch_row($result);
		}
	}
	
	function _rows($result) {
		if ($this->mysqli) {
			return mysqli_num_rows($result);
		} else {
			return mysql_num_rows($result);
		}
	}
	
	function _insert_id() {
		if ($this->mysqli) {
			return mysqli_insert_id($this->link);
		} else {
			return mysql_insert_id();
		}
	}
}

?>