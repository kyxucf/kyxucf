<?php

class ApiController extends AppController {

	var $name = 'Api';
	var $components = array('RequestHandler', 'Director');
	var $uses = array();
	var $helpers = array('Api', 'Director', 'Xml');
	var $version = DIR_VERSION;
	var $atbl, $itbl, $dtbl, $dltbl, $utbl;
	var $only_active, $cache_key, $cache_file, $cache_listing;
	var $disableSessions = true;
	var $cache_me = false;
	var $respondsAs = 'xml';
	
	function beforeFilter() {
		$this->get = false;
		if (!$this->data && $_GET) {
			$this->data = $_GET;
			$this->get = true;
			if (!isset($this->data['format'])) {
				$this->data['format'] = 'json';
			}
		} else {
			$this->data['api_key'] = r('hosted-', '', $this->data['api_key']);
		}
		if (isset($this->data['format']) && $this->data['format'] == 'json') {
			$this->layout = false;
			$this->RequestHandler->respondAs('javascript');
			$this->respondsAs = 'json';
			// Debugging time stamp breaks JS parsing
			Configure::write('debug', 0);
		} else {
			$this->layout = 'xml';
			$this->RequestHandler->respondAs('xml');
		}
		
		if ($this->get) {
			$host = env('HTTP_HOST');
			$refer = env('HTTP_REFERER');

			if ($refer && $host && file_exists(ROOT . DS . 'domains')) {
				$host = $this->_parse_host($host);
				$refer = $this->_parse_host($refer);

				if ($host == $refer) {
					// Let them through
				} else {
					$contents = file_get_contents(ROOT . DS . 'domains');
					$contents = explode("\n", $contents);
					$clean = array();
					foreach($contents as $c) {
						$clean[] = $this->_parse_host('http://' . str_replace('http://', '', $c));
					}
					$refer_wild = substr_replace($refer, '*', 0, strpos($refer, '.'));
					$refer_sub = '*.' . $refer;
					if (in_array($refer, $clean) || in_array($refer_sub, $clean) || in_array($refer_wild, $clean)) {
						// Allowed
					} else {
						$this->_error('Crossdomain access denied.');
					}
				}
			}
		}
		
		$this->loadModel('Account');
		$this->Account->recursive = -1;
		$this->account = $this->Account->find();
		
		if (date('mdY', $this->account['Account']['last_schedule_check']) != date('mdY', time())) {
			$this->Director->scheduling();
			$this->Account->id = $this->account['Account']['id'];
			$this->Account->saveField('last_schedule_check', time());
		}
		
		$this->_master_cache();

		$api_key = $this->account['Account']['api_key'];
		if ((empty($this->data['api_key']) || ($this->data['api_key'] != $api_key)) && !$this->get) {
			$this->_error('Invalid API key');
		}
		if (!isset($this->data['size'])) {
			$this->data['size'] = array();
		}
		
		if (!isset($this->data['user_size'])) {
			$this->data['user_size'] = array();
		}
		
		if (!isset($this->data['preview'])) {
			$this->data['preview'] = '';
		}
		
		if (!isset($this->data['album_fields'])) {
			$this->data['album_fields'] = '*';
		}
		
		if (isset($this->data['include_metadata'])) {
			$this->includeMeta = $this->data['include_metadata'];
		} else {
			$this->includeMeta = true;
		}
		
		if (!isset($this->data['only_active'])) {
			$this->only_active = true;
		} else {
			$this->only_active = (bool) $this->data['only_active'];
		}
		$this->set('active', $this->only_active);
		$this->set('users', $this->Director->fetchUsers());
		$this->atbl = DIR_DB_PRE . 'albums';
		$this->itbl = DIR_DB_PRE . 'images';
		$this->dtbl = DIR_DB_PRE . 'dynamic';
		$this->dltbl = DIR_DB_PRE . 'dynamic_links';
		$this->utbl = DIR_DB_PRE . 'usrs';
		
		if (isset($this->data['invalidator'])) {
			$this->_cache_invalidator($this->data['invalidator'], $this->action);
		}
		
		$this->loadModel('Watermark');
		$watermarks = $this->Watermark->find('all');
		$watermark_arr = array();
		if (!empty($watermarks)) {
			foreach($watermarks as $w) {
				$watermark_arr[$w['Watermark']['id']] = $w['Watermark'];
			}
		}
		
		$this->watermarks = $watermark_arr;
		$this->set('controller', $this);
	}
	
	function _parse_host($str) {
		$a = parse_url($str);
		return str_replace('www.', '', $a['host']);
		
	}
	
	function afterFilter() {
		if ($this->cache_me) {
			$this->_cache($this->cache_file, $this->output);
		}
		if ($_GET && isset($_GET['callback'])) {
			// JSONP
			$this->output = $_GET['callback'] . '(' . $this->output . ')';
		}
	}
	
	function _master_cache() {
		$this->cache_listing = $this->_cache('master');
		$this->data['action'] = $this->action;
		$data_check = $this->data;
		unset($data_check['callback']);
		$this->cache_key = base64_encode(serialize($data_check));
		if (empty($this->cache_listing)) {
			$this->cache_listing = array();
		} elseif (array_key_exists($this->cache_key, $this->cache_listing)) {
			$info = $this->cache_listing[$this->cache_key];
			$file = $info['file'];
			$xml = $this->_cache($file);
			if (empty($xml)) {
				unset($this->cache_listing[$this->cache_key]);
				$this->_cache('master', $this->cache_listing);
			} else {
				if ($_GET && isset($_GET['callback'])) {
					// JSONP
					$xml = $_GET['callback'] . '(' . $xml . ')';
				}
				die($xml);
			}
		}
		$this->cache_file = uniqid(null, true);
	}
	
	function _update_master_cache($desc) {
		$noob = array($this->cache_key => array('type' => $desc, 'file' => $this->cache_file));
		$this->cache_listing = array_merge($this->cache_listing, $noob);
		$this->_cache('master', $this->cache_listing);
		$this->cache_me = true;
	}
	
	function _error($msg) {
		$this->set('error', $msg);
		$pre = $post = '';
		if ($_GET && isset($_GET['callback'])) {
			// JSONP
			$pre = $_GET['callback'] . '(';
			$post = ')';
		}
		e($pre . $this->render('error.' . $this->respondsAs) . $post);
		exit;
	}

	function auth() {
		$this->loadModel('User');
		$user = $this->User->findByUsr($this->data['username']);
		
		if (empty($user)) {
			$this->_error('User not found');
		} else {
			if ($user['User']['pwd'] != $this->data['password']) {
				$this->_error('Password incorrect');
			} else {
				$this->set('user', $user);
				$this->render('user.xml');
			}
		}
	}
	
	////
	// APP
	////
	
	function app_version() {
		$this->set('version', $this->version);
		$this->render('app/version.' . $this->respondsAs);
	}
	
	function app_totals() {
		$this->loadModel('Image');
		$result = $this->Image->query("SELECT COUNT({$this->itbl}.id) as image_count, SUM(filesize) as total_size FROM {$this->itbl} WHERE src IS NOT NULL");
		$this->set('data', $result[0][0]);
		$this->render('app/totals.' . $this->respondsAs);
	}
	
	function app_limits() {
		list($max, $post_max_broken) = $this->Director->uploadLimit();
		$this->set('max', $max);
		$this->render('app/limits.' . $this->respondsAs);
	}
	
	////
	// ALBUM
	////
	
	function get_album() {
		if (!isset($this->data['album_id'])) {
			$this->_error('Required parameter \'album_id\' is missing.');
		}
		
		if (is_numeric($this->data['album_id'])) {
			$column = 'id';
			$key = $id = $this->data['album_id'];
		} else {
			$column = 'internal_id';
			$id = substr($this->data['album_id'], 32, strlen($this->data['album_id']));
			$key = substr($this->data['album_id'], 0, 32);
			if (!is_numeric($id)) {
				$this->_error('Access denied');
			}
		}
		
		$cache_key = 'get_album' . DS . $id;
		if (isset($this->data['with_content'])) {
			$cache_key .= '_' . $this->data['with_content'];
			$with_content = $this->data['with_content'];
		} else {
			$cache_key .= '_1';
			$with_content = true;
		}
		
		$this->_update_master_cache($cache_key);
		$album = $this->_cache($cache_key);
		if (empty($album)) {
			$this->loadModel('Album');
			if (!$with_content) {
				$this->Album->recursive = -1;
			}
			$album = $this->Album->find('first', array('conditions' => array($column => $key)));
			if ($album['Album']['smart'] && $with_content) {
				list($conditions, $order, $limit) = $this->Album->smartConditions(unserialize($album['Album']['smart_query']));
				$this->Album->Image->contain('Album');
				$images = $this->Album->Image->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order));
				$album['Smart'] = $images;
			}
			$this->_cache($cache_key, $album);
		}
		if (empty($album)) {
			$this->_error('No album found with an id of ' . $this->data['album_id']);
		}
		$this->set('album', $album);
		$this->render('album/get.' . $this->respondsAs);
	}
	
	function get_album_list() {
		$cache_key = 'get_albums_list' . DS;
		$params = array();
		if (isset($this->data['only_published']) && $this->data['only_published']) {
			$params[] = aa('Album.active', 1);
			$cache_key .= '1';
		} else {
			$params = '';
			$cache_key .= '0';
		}
		
		if (isset($this->data['list_only']) && $this->data['list_only']) {
			$list_only = true;
			$cache_key .= '_1';
		} else {
			$list_only = false;
			$cache_key .= '_0';
		}
		
		if (isset($this->data['only_smart']) && $this->data['only_smart']) {
			$params[] = aa('Album.smart', 1);
			$cache_key .= '_1';
		} else if (isset($this->data['exclude_smart']) && $this->data['exclude_smart']) {
			$params[] = aa('Album.smart', 0);
			$cache_key .= '_2';
		} else {
			$cache_key .= '_0';
		}
		
		if (isset($this->data['tags']) && !empty($this->data['tags'])) {
			$tags = '_' . str_replace(',', '_', $this->data['tags']);
			$tag_query = "(Album.tags LIKE ";
			$tags_arr = array();
			foreach(explode(',', $this->data['tags']) as $t) {
				$tags_arr[] = "'%" . addslashes($t) . ",%'";
			}
			if (isset($this->data['tags_exclusive']) && $this->data['tags_exclusive']) {
				$tag_query .= implode(' AND Album.tags LIKE ', $tags_arr) . ')';
				$tags .= '_1';
			} else {
				$tag_query .= implode(' OR Album.tags LIKE ', $tags_arr) . ')';
				$tags .= '_0';
			}
			$params[] = $tag_query;
			$cache_key .= $tags;
		} 
		
		$this->_update_master_cache('get_albums_list');		
		$albums = $this->_cache($cache_key);
		if (empty($albums)) {
			$this->loadModel('Album');
			if ($list_only) {
				$this->Album->unbindModel(array('hasMany' => array('Image', 'Tag')));
			}
			$albums = $this->Album->findAll($params, null, 'name');
			if (!$list_only) {
				$new_albums = array();
				foreach($albums as $album) {
					if ($album['Album']['smart']) {
						list($conditions, $order, $limit) = $this->Album->smartConditions(unserialize($album['Album']['smart_query']));
						$this->Album->Image->contain('Album');
						$images = $this->Album->Image->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order));
						$album['Smart'] = $images;
					}
					$new_albums[] = $album;
				}
				$albums = $new_albums;
			}
			$this->_cache($cache_key, $albums);
		}
		$this->set('albums', $albums);
		$this->render('album/list.' . $this->respondsAs);
	}
	
	function get_associated_galleries() {
		if (!isset($this->data['album_id'])) {
			$this->_error('Required parameter \'album_id\' is missing.');
		}
		$cache_key = 'get_associated_galleries_' . $this->data['album_id'] . '_' . $this->data['exclude'];
		$this->_update_master_cache($cache_key);
		$galleries = $this->_cache($cache_key);
		if (empty($galleries)) {
			$this->loadModel('Gallery');
			$q = "SELECT did FROM {$this->dltbl} AS Tag WHERE aid = {$this->data['album_id']}";
			if (!empty($this->data['exclude']) && preg_match('/^[,0-9]+$/', $this->data['exclude'])) {
				$q .= ' AND did NOT IN (' . $this->data['exclude'] . ')';
			}
			$data = $this->Gallery->query($q);
			$ids = array();
			foreach($data as $d) {
				$ids[] = $d['Tag']['did'];
			}
			if (empty($ids)) {
				$galleries = array();
			} else {
				$this->Gallery->recursive = -1;
				$galleries = $this->Gallery->findAll(aa('id', $ids), null, 'name');
			}
			$this->_cache($cache_key, $galleries);
		}
		$this->set('galleries', $galleries);
		$this->render('gallery/list.' . $this->respondsAs);
	}
		
	function create_album() {
		if ($this->get) {
			$this->_error('This action is not supported for GET requests.');
		}
		$this->loadModel('Watermark');
		$watermark = $this->Watermark->find('first', array('conditions' => array('main' => 1)));
		if (!empty($watermark)) {
			$this->data['Album']['watermark_id'] = $watermark['Watermark']['id'];
		}
		$this->data['Album']['caption_template'] = $this->account['Account']['caption_template'];
		$this->data['Album']['title_template'] = $this->account['Account']['title_template'];
		$this->data['Album']['link_template'] = $this->account['Account']['link_template'];
		
		$this->loadModel('Album');
		if ($this->Album->save($this->data)) {
			// Make directories and set path
			$this->Album->id = $this->Album->getLastInsertId();
			$path = 'album-' . $this->Album->id;
			$this->Director->makeDir(ALBUMS . DS . $path);
			$this->Director->createAlbumDirs($this->Album->id);
			$this->set('album_id', $this->Album->id);
			$this->render('album/create.xml');
		}
	}
	
	////
	// CONTENT
	////
		
	function get_content_list() {
		$this->_update_master_cache('get_content_list');
		$wheres = array();
		$sort_options = array('created_on', 'modified_on', 'captured_on', 'filename', 'random');
		$sort_order = array('DESC', 'ASC');
		
		if (isset($this->data['sort_on']) && in_array($this->data['sort_on'], $sort_options)) {
			$sort = $this->data['sort_on'];
		} else {
			$sort = 'created_on';
		}
		
		if (isset($this->data['sort_direction']) && in_array($this->data['sort_direction'], $sort_order)) {
			$sort_direction = $this->data['sort_direction'];
		} else {
			$sort_direction = 'DESC';
		}
		
		if (isset($this->data['limit']) && is_numeric($this->data['limit'])) {
			$limit = $this->data['limit'];
		} else {
			$limit = 0;
		}
		
		if (isset($this->data['only_images']) && $this->data['only_images'] == 1) {
			$only_images = 1;
		} else {
			$only_images = 0;
		}
		
		if (isset($this->data['tags']) && !empty($this->data['tags'])) {
			$tags = '_' . str_replace(',', '_', $this->data['tags']);
			$tag_query = "(Image.tags LIKE ";
			$tags_arr = array();
			foreach(explode(',', $this->data['tags']) as $t) {
				$tags_arr[] = "'%" . addslashes($t) . ",%'";
			}
			if (isset($this->data['tags_exclusive']) && $this->data['tags_exclusive']) {
				$tag_query .= implode(' AND Image.tags LIKE ', $tags_arr) . ')';
				$tags .= '_1';
			} else {
				$tag_query .= implode(' OR Image.tags LIKE ', $tags_arr) . ')';
				$tags .= '_0';
			}
			$wheres[] = $tag_query;
		} else {
			$tags = '';
			$tag_query = '';
		}
		
		if (isset($this->data['scope']) && isset($this->data['scope_id'])) {
			$model = $this->data['scope'];
			$model_id = $this->data['scope_id'];
			$scope_cache = '_' . $model . '_' . $model_id;
			if ($model == 'gallery') {
				$this->loadModel('Gallery');
				$g = $this->Gallery->find(aa('Gallery.id', $model_id));
				$album_ids = array();
				if (!empty($g['Tag'])) {
					foreach($g['Tag'] as $tag) {
						$album_ids[] = $tag['aid'];
					}
					$id_str = implode(',', $album_ids);
				}
			} else {
				$id_str = $model_id;
			}
			if (isset($id_str)) {
			 	$scope_query = "Image.aid IN ($id_str)";
			} else {
				$scope_query = 'Image.aid IN (0)';
			}
			$wheres[] = $scope_query;
		} else {
			$scope_cache = '';
			$scope_query = '';
		}
		
		if ($this->only_active) {
			$wheres[] = "Image.active = 1";
		}
		
		$cache_key = 'get_content_list' . DS . $sort . '_' . $sort_direction . '_' . $limit . '_' . $only_images . $tags . $scope_cache . '_' . (int) $this->only_active;
		
		if ($sort == 'random') {
			$cache_key .= '_' . rand(1,10);
		}
		
		$result = $this->_cache($cache_key);
		if (empty($result)) {
			$this->loadModel('Image');
			$this->Image->Behaviors->attach('Containable');
			$this->Image->contain('Album');
			$q = '';
			if ($only_images) {
				$wheres[] = "(Image.src LIKE '%.gif' OR Image.src LIKE '%.jpg' OR Image.src LIKE '%.jpeg' OR Image.src LIKE '%.png')";
			}
			$q .= implode(' AND ', $wheres);
			if ($sort == 'random') {
				$order = 'RAND()';
			} else {
				$order = " Image.{$sort} $sort_direction";
			}
			if ($limit !== 0) {
			 	$limit_sql = $limit;
			} else {
				$limit_sql = '';
			}
			$result = $this->Image->find('all', array('conditions' => $q, 'order' => $order, 'limit' => $limit_sql));
			$this->_cache($cache_key, $result);
		}
		$this->set('data', $result);
		$this->render('content/list.' . $this->respondsAs);
	}
	
	function get_content() {
		if (!isset($this->data['content_id'])) {
			$this->_error('Required parameter \'content_id\' is missing.');
		}
		$this->data['content_id'] = r('content-', '', $this->data['content_id']);
		$cache_key = 'get_content_' . $this->data['content_id'];
		$this->_update_master_cache('get_content_' . $this->data['content_id']);
		$image = $this->_cache($cache_key);
		if (empty($image)) {
			$this->loadModel('Image');
			$image = $this->Image->read(null, $this->data['content_id']);
			if (empty($image)) {
				$this->_error('No content found with an id of ' . $this->data['content_id']);
			}
			$this->_cache($cache_key, $image);
		}
		$this->set('image', $image);
		$this->render('content/get.' . $this->respondsAs);
	}

	function upload() {
		if ($this->get) {
			$this->_error('This action is not supported for GET requests.');
		}
		$src = $_FILES['data']['name']['photo'];
		$tmp = $_FILES['data']['tmp_name']['photo'];
		$src = str_replace(" ", "_", $src);
		$src = ereg_replace("[^A-Za-z0-9._-]", "_", $src);
		
		$this->loadModel('Image');
		
		$album = $this->Image->Album->read(null, $this->data['Album']['id']);
		$album_active = $album['Album']['active']; 
		
		$dest = ALBUMS . DS . 'album-' . $album['Album']['id'] . DS . 'lg' . DS . $src;
		
		if (!allowableFile($src)) {
			$this->_error("$src is not an allowed file type.");
		}
		
		$check = $this->Image->find("aid = {$this->data['Album']['id']} AND src = '$src'");
		if (is_uploaded_file($tmp) && move_uploaded_file($tmp, $dest)) {
			list($meta, $captured_on) = $this->Director->imageMetadata($dest);
			$keywords = $this->Director->parseMetaTags('iptc:keywords', $meta);
			$keywords = str_replace(' ', ',', urldecode($keywords));
			$keywords = ereg_replace("[^,A-Za-z0-9._-]", "", $keywords);
			if (empty($this->data['Image']['tags'])) {
				$this->data['Image']['tags'] = $keywords;
			}
			
			if (empty($check)) {
				$top = $this->Image->find('first', array('conditions' => "aid = {$this->data['Album']['id']}", 'order' => 'seq DESC'));
				if (empty($top)) {
					$next = 0;
				} else {
					$next = $top['Image']['seq'];
					if ($top['Image']['active']) {
						$next++;
					}
				}
				$data['Image']['title'] = $this->data['Image']['title'];
				$data['Image']['caption'] = $this->data['Image']['caption'];
				$data['Image']['tags'] = $this->data['Image']['tags'];
				$data['Image']['src'] = $src;
				$data['Image']['aid'] = $album['Album']['id'];
				$data['Image']['seq'] = $next;
				$data['Image']['created_by'] = 	$data['Image']['updated_by'] = $this->data['User']['id'];
				$data['Image']['captured_on'] = $captured_on;
				$data['Image']['filesize'] = filesize($dest);
				$data['Image']['is_video'] = isVideo($src);
				$data['Image']['album_active'] = $album_active; 
				$this->Image->save($data);
				$this->Image->Album->reorder($this->data['Album']['id']);
				$this->set('image_id', $this->Image->getLastInsertId());
			} else {
				$image_id = $check['Image']['id'];
				$caches = glob(ALBUMS . DS . 'album-' . $check['Album']['id'] . DS . 'cache' . DS . $check['Image']['src'] . '*');
				if (!empty($caches)) {
					foreach($caches as $cache) {
						@unlink($cache);
					}
				}
				$this->Image->id = $image_id;
				$data['Image']['captured_on'] = $captured_on;
				$data['Image']['filesize'] = filesize($dest);
				$data['Image']['tags'] = $this->data['Image']['tags'];;
				$this->Image->save($this->data);
				$this->set('image_id', $image_id);
			}
			
			if (is_numeric($this->account['Account']['archive_w'])) {
				$this->Kodak->develop($lg_path, $lg_path, $this->account['Account']['archive_w'], $this->account['Account']['archive_w'], 100);
			}
		}
		$this->render('content/upload.xml');
	}
	
	////
	// GALLERY
	////
	
	function get_gallery() {
		if (!isset($this->data['gallery_id'])) {
			$this->_error('Required parameter \'gallery_id\' is missing.');
		}
		
		if (is_numeric($this->data['gallery_id'])) {
			$column = 'id';
			$key = $id = $this->data['gallery_id'];
		} else {
			$column = 'internal_id';
			$id = substr($this->data['gallery_id'], 32, strlen($this->data['gallery_id']));
			$key = substr($this->data['gallery_id'], 0, 32);
			if (!is_numeric($id)) {
				$this->_error('Access denied');
			}
		}
		
		$cache_key = 'get_gallery' . DS . $id;
		$this->_update_master_cache($cache_key);
		
		$order_override = $limit_override = false;
		
		if (isset($this->data['order'])) {
			$cache_key .= '_' . $this->data['order'];
			$order = $this->data['order'];
			if ($order != 'display') {
				$order = "Album.$order";
				$order_override = true;
			}
		} else {
			$cache_key .= '_display';
			$order = 'display';
		}
		
		if (isset($this->data['limit']) && is_numeric($this->data['limit'])) {
			$cache_key .= '_' . $this->data['limit'];
			$limit = $this->data['limit'];
			$limit_override = true;
		} else {
			$cache_key .= '_0';
			$limit = null;
		}
		
		if (isset($this->data['with_content'])) {
			$cache_key .= '_' . $this->data['with_content'];
			$with_content = $this->data['with_content'];
		} else {
			$cache_key .= '_0';
			$with_content = true;
		}
		
		list($gallery, $albums) = $this->_cache($cache_key);
		if (empty($gallery)) {
			$this->loadModel('Gallery');
			$this->Gallery->recursive = -1;
			$gallery = $this->Gallery->find('first', array('conditions' => array($column => $key)));
			
			if (empty($gallery)) {
				$this->_error('No gallery found with an id of ' . $this->data['gallery_id']);
			}
			
			if ($gallery['Gallery']['smart'] && !$gallery['Gallery']['main']) {
				if ($with_content) {
					$recursive = 1;
				} else {
					$recursive = -1;
				}
				list($conditions, $sorder, $slimit) = $this->Gallery->smartConditions(unserialize($gallery['Gallery']['smart_query']));
				if (!$order_override) {
					$order = $sorder;
				}
				if (!$limit_override) {
					$limit = $slimit;
				}
				$tags = $this->Gallery->Tag->Album->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => $recursive));
			} else {
				if ($with_content) {
					$recursive = 2;
				} else {
					$recursive = 1;
				}
				$conditions = array('conditions' => array('did' => $gallery['Gallery']['id']), 'order' => $order, 'recursive' => $recursive);
				if (!is_null($limit)) {
					$conditions = array_merge($conditions, array('limit' => $limit));
				}
				$tags = $this->Gallery->Tag->find('all', $conditions);
			}
			if (!empty($tags)) {
				$albums = array();
				foreach($tags as $album) {
					if ($with_content) {
						if ($album['Album']['smart']) {
							list($conditions, $order, $limit) = $this->Gallery->Tag->Album->smartConditions(unserialize($album['Album']['smart_query']));
							$this->Gallery->Tag->Album->Image->contain('Album');
							$images = $this->Gallery->Tag->Album->Image->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order));
							$album['Smart'] = $images;
						} elseif (!isset($album['Image'])) {
							$album['Image'] = $album['Album']['Image'];
						}
					}
					$albums[] = $album;
				}
			} else {
				$albums = array();
			}
			$this->_cache($cache_key, array($gallery, $albums));
		}
		$this->set('gallery', $gallery);
		$this->set('albums', $albums);
		$this->render('gallery/get.' . $this->respondsAs);		
	}
	
	function get_gallery_list() {
		$cache_key = 'get_gallery' . DS . 'list';
		$galleries = $this->_cache($cache_key);
		$this->_update_master_cache($cache_key);
		if (empty($galleries)) {
			$this->loadModel('Gallery');
			$gals = $this->Gallery->find('all', array('order' => 'name', 'recursive' => 2));
			$galleries = array();
			foreach($gals as $gallery) {
				if ($gallery['Gallery']['smart'] && !$gallery['Gallery']['main']) {
					list($conditions, $order, $limit) = $this->Gallery->smartConditions(unserialize($gallery['Gallery']['smart_query']));
					$tags = $this->Gallery->Tag->Album->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => -1));
					$gallery['Tag'] = $tags;
				}
				$galleries[] = $gallery;
			}
			$this->_cache($cache_key, $galleries);
		}
		$this->set('galleries', $galleries);
		$this->render('gallery/list.' . $this->respondsAs);
	}
	
	function get_users() {
		$sort = $this->data['user_sort'];
		$this->_update_master_cache('get_users');
		$cache_key = 'get_users' . DS . $sort;
		if (isset($this->data['user_scope_model'])) {
			$model = $this->data['user_scope_model'];
			$model_id = $this->data['user_scope_id'];
			$model_all = (int) $this->data['user_scope_all'];
			$cache_key .= "_{$model}_{$model_id}_{$model_all}";
		} 
		
		$ids = $this->_cache($cache_key);
		if (empty($ids)) {
			$this->loadModel('User');
			if (isset($model)) {
				$ids = array();
				if ($model == 'gallery') {
					$this->loadModel('Gallery');
					$g = $this->Gallery->find(aa('Gallery.id', $model_id));
					$album_ids = array();
					if (!empty($g['Tag'])) {
						foreach($g['Tag'] as $tag) {
							$album_ids[] = $tag['aid'];
						}
						$id_str = implode(',', $album_ids);
					}
				} else {
					$id_str = $model_id;
				}
				if (isset($id_str)) {
					$q = "SELECT DISTINCT User.*, COUNT(Image.id) as cnt FROM {$this->utbl} as User, {$this->itbl} as Image WHERE User.id = Image.created_by AND Image.aid IN ($id_str) GROUP BY Image.created_by";
					$result = $this->User->query($q);
					if (!empty($result)) {
						foreach($result as $u) {
							$ids[] = array('id' => $u['User']['id'], 'last_name' => $u['User']['last_name'], 'first_name' => $u['User']['first_name'], 'usr' => $u['User']['usr'], 'count' => $u[0]['cnt']);
						}
					}
				}
			} else {
				$users = $this->User->findAll(null, null, 'last_name, first_name, usr');
				$ids = array();
				$this->loadModel('Image');
				foreach($users as $u) {
					if (is_array($u['User']['id'])) {
						$u['User']['id'] = $u['User']['id'][0];
					}
					$count = $this->Image->find('count', array('conditions' => 'Image.created_by = ' . $u['User']['id']));
					$ids[] = array('id' => $u['User']['id'], 'last_name' => $u['User']['last_name'], 'first_name' => $u['User']['first_name'], 'usr' => $u['User']['usr'], 'count' => $count);
				}
			}
			if (!empty($ids)) {
				foreach ($ids as $key => $row) {
					if (is_null($row['last_name'])) {
						$last_name[$key] = '';
					} else {
						$last_name[$key] = $row['last_name'];
					}
				    if (is_null($row['first_name'])) {
						$first_name[$key] = '';
					} else {
						$first_name[$key] = $row['first_name'];
					}
					$usr[$key]  = $row['usr'];
					$count_arr[$key]  = $row['count'];					
				}
				if ($this->data['user_sort'] == 'name') {
					array_multisort($last_name, SORT_ASC, $first_name, SORT_ASC, $usr, SORT_ASC, $count_arr, SORT_DESC, $ids);
				} else {
					array_multisort($count_arr, SORT_DESC, $last_name, SORT_ASC, $first_name, SORT_ASC, $usr, SORT_ASC, $ids);
				}
				$this->_cache($cache_key, $ids);
			}
		}
		$this->set('user_ids', $ids);
		$this->render('users/list.' . $this->respondsAs);
	}
	
	function _cache($key, $data = null) {
		$path = 'api' . DS . $key . '.cache';
		$dir = dirname(CACHE . DS . $path);
		if (!is_dir($dir)) {
			$f = new Folder();
			$f->create($dir);
		}
		if (is_null($data)) {
			return unserialize(cache($path, null, '+6 hours'));
		} else {
			cache($path, serialize($data));
		}
	}
	
	function _cache_invalidator($invalidator, $action) {
		$file = CACHE . 'api' . DS . 'invalidators' . DS . $action . '.cache';
		if (!is_dir(dirname($file))) {
			$this->Director->makeDir(dirname($file));
		}
		if (file_exists($file)) {
			$contents = unserialize(file_get_contents($file));
		} else {
			$contents = array();
		}
		
		$path = $invalidator['path'];
		$name = $invalidator['name'];
		
		$exists = false;
		if (!empty($contents)) {
			foreach ($contents as $c) {
				if ($c['name'] == $name && $c['path'] == $path) {
					$exists = true;
				}
			}
		}	
		
		if (!$exists) {
			$contents[] = array('path' => $path, 'name' => $name);
			file_put_contents($file, serialize($contents));
		}
	}
}

?>