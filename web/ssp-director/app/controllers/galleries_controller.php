<?php

class GalleriesController extends AppController {
	// Helpers
	var $helpers = array('Html', 'Javascript', 'Ajax');
    var $name = 'Galleries';

	var $non_ajax_actions = array('index', 'edit', '_memberData', 'refresh');
	var $paginate = array('limit' => 50, 'page' => 1, 'order' => array('name' => 'asc')); 
	
	// Only logged in users should see this controller's actions
 	function beforeFilter() {
		// Protect ajax actions
		if (!in_array($this->action, $this->non_ajax_actions)) {
			$this->verifyAjax();
		}
		// Check session
		$this->checkSession();
	}
	
	////
	// Galleries listing
	////
	function index() {
		$filters = array();
		$params = $this->params;
		$page = 1;
		$filtered = false;
		
		if ($this->RequestHandler->isAjax()) { 
			$this->set('empty', true);

			if (isset($this->data['Gallery']['search'])) {
				$search = $this->data['Gallery']['search'];
			} elseif ($this->Session->check('Gallery.search')) {
				$search = $this->Session->read('Gallery.search');
			}

			if (isset($search)) {
				if (empty($search)) {
					$this->Session->del('Gallery.search');
				} else {
					$filtered = true;
					$filters[] = "(lower(Gallery.name) like '%" . low($search) . "%' OR lower(Gallery.description) like '%" . low($search) . "%')"; 
					$this->Session->write('Gallery.search', $search);
					$this->data['Gallery']['search'] = $search;
				}
			}
			
			$type = 2;
			if (isset($this->data['Gallery']['type'])) {
				if ($this->data['Gallery']['type'] == 2) {
					$this->Session->del('Gallery.type');
				}
				$type = $this->data['Gallery']['type'];
			} elseif ($this->Session->check('Gallery.type')) {
				$type = $this->Session->read('Gallery.type');
				$this->data['Gallery']['type'] = $type;
			}

			if ($type != 2) {
				$filtered = true;
				$filters[] = "Gallery.smart = " . $type;
				$this->Session->write('Gallery.type', $type);
			}
			if (isset($params['named']['page'])) {
				$page = $params['named']['page'];
				$this->Session->write('Gallery.page', $page);
			} elseif ($this->Session->check('Gallery.page')) {
				$page = $this->Session->read('Gallery.page');
			}
		} else {
			// Available imports?
			$imports = $this->Director->checkImports();
			if (empty($imports) || !$imports) {
				$this->set('imports', false);
			} else {
				$this->set('imports', $imports);
			}
			$this->Session->del('Gallery.search');
			$this->Session->del('Gallery.page');
		}
		
		if (isset($params['named']['sort'])) {
			$sort = $params['named']['sort'];
			$dir = $params['named']['direction'];
			$this->Cookie->write('Gallery.sort', "$sort $dir", true, 32536000);
		} elseif ($this->Cookie->read('Gallery.sort')) {
			$val = $this->Cookie->read('Gallery.sort');
			@list($sort, $dir) = explode(' ', $val);
		}

		if (isset($sort) && in_array($sort, array('name', 'smart', 'tag_count', 'created_on', 'modified_on')) && in_array(strtolower($dir), array('desc', 'asc'))) {
			$this->paginate = array_merge($this->paginate, array('order' => array($sort => $dir)));
		}
		
		$this->paginate = array_merge($this->paginate, array('page' => $page));
		$this->Gallery->recursive = -1;
		$this->set('galleries', $this->paginate('Gallery', $filters));
		$this->set('filtered', $filtered);
		if ($this->RequestHandler->isAjax()) { 
			$this->render('list', 'ajax');
		}
	}
	
	////
	// Create a new gallery
	////	
	function create() {
		if ($this->Gallery->save($this->data)) {
			if ($this->data['redirect'] == 2) {
				$this->set('galleries', $this->Gallery->findAll(null, null, 'Gallery.modified_on DESC', 5));
			} else {
				$this->set('id', $this->Gallery->getLastInsertID());
			} 
		}
	}
	
	////
	// Update Gallery
	////
	function update($id) {
		$this->Gallery->id = $id;
		if ($this->Gallery->save($this->data)) {
			$this->set('gallery', $this->Gallery->read());
		}
	}
	
	////
	// Delete gallery
	////
	function delete() {
		$this->Gallery->del($this->params['form']['id']);
		$this->redirect('/galleries/index');
	}
	
	////
	// Edit gallery
	////
	function edit($id, $tab = 'settings') {
		$this->pageTitle = __('Galleries', true);
		$this->set('tab', $tab);
		if ($tab == 'settings') {
			$non_members = false;
		} else {
			$non_members = true;
		}
		$this->_memberData($id, $non_members);
		$this->set('all_gals', $this->Gallery->find('all', array('order' => 'name', 'recursive' => -1)));
	}
	
	////
	// Link and delink albums to galleries
	////
	function link() {
		$this->Gallery->Tag->save($this->data);
		
		if ($this->Gallery->isMain($this->data['Tag']['did'])) {
			$this->Gallery->Tag->Album->id = $this->data['Tag']['aid'];
			$this->Gallery->Tag->Album->saveField('active', 1);
		}
		$this->_memberData($this->data['Tag']['did']);
		$this->set('show_dialogue', true);
		$this->render('refresh_edit_pane', 'ajax');
	}
	
	function delink() {
		$link = $this->Gallery->Tag->find('first', array('conditions' => array('id' => $this->data['Tag']['id']), 'recursive' => -1));
		$id = $link['Tag']['did'];
		$aid = $link['Tag']['aid'];
		$this->Gallery->Tag->delete($this->data);
		
		if ($this->Gallery->isMain($id)) {
			$this->Gallery->Tag->Album->id = $aid;
			$this->Gallery->Tag->Album->saveField('active', 0);
		}
		
		$this->_memberData($id);
		$this->render('refresh_edit_pane', 'ajax');
	}
	
	function toggle($refresh = 'album') {
		if (isset($this->data['active'])) {
			if ($this->data['active']) {
				$this->Gallery->Tag->save($this->data);
			} else {
				$this->Gallery->Tag->deleteAll(sprintf('did = %d AND aid = %d', $this->data['Tag']['did'], $this->data['Tag']['aid']));
			}
		}
		$count = $this->Gallery->Tag->find('count', array('conditions' => 'aid = ' . $this->data['Tag']['aid']));
		printf(__('Active in %s.', true), '<strong>' . $count . '</strong> ' . ife($count > 1, __('galleries', true), __('gallery', true)));
		exit;
	}
	
	////
	// Reset order type and refresh the album order as needed
	////
	function order_type($id) {
		$this->Gallery->id = $id;
		$this->Gallery->save($this->data);
		$this->Gallery->cacheQueries = false;
		$this->Gallery->reorder($id);
		$this->_memberData($id);
		$this->render('order_type', 'ajax');
	}
	
	function page_non_members($id) {
		$this->_memberData($id);
	}
	
	////
	// Private function to refresh gallery members
	////
	function _memberData($id, $non_members = true) {		
		$this->Gallery->contain('Tag');
		if (isset($this->data['search'])) {
			$search = $this->data['search'];
		}
		$this->data = $this->Gallery->find('first', array('conditions' => array('id' => $id)));
		$this->set('gallery', $this->data);
		
		if ($this->data['Gallery']['smart'] && !$this->data['Gallery']['main']) {
			list($albums,) = $this->_smart_content(unserialize($this->data['Gallery']['smart_query']));
			$non_members = false;
		} else {
			$albums = $this->Gallery->members($this->data);
		}
		
		$this->set('albums', $albums);
		
		if ($this->data['Gallery']['main']) {
			$is_main = true;
		} else {
			$is_main = false;
		}
		$this->set('is_main', $is_main);
		
		if ($non_members) {
			$params = $this->params;

			if ($this->RequestHandler->isAjax()) { 
				if (isset($search)) {
					if (empty($search)) {
						$this->Session->del('MA.search');
					} else {
						$this->Session->write('MA.search', $search);
					}
				} elseif ($this->Session->check('MA.search')) {
					$search = $this->Session->read('MA.search');
				}

				if (isset($params['named']['page'])) {
					$page = $params['named']['page'];
					$this->Session->write('MA.page', $page);
				} elseif ($this->Session->check('MA.page')) {
					$page = $this->Session->read('MA.page');
				}
				if (isset($page)) {
					$this->paginate = array_merge($this->paginate, array('page' => $page));
				}
			} else {
				$this->Session->del('MA.search');
				$this->Session->del('MA.page');
			}
			
			if (isset($search)) {
				$this->data['search'] = $search;
			} 
		
			if (!$this->data['Gallery']['main']) {
				$member_ids_arr = array();
				foreach ($this->data['Tag'] as $l) { 
					$member_ids_arr[] = $l['aid'];
				}
		
				// Find active albums, gallery members, and the diff
				$all_albums = $this->Gallery->Tag->Album->find('all', array('conditions' => array('active' => 1), 'order' => 'name', 'recursive' => -1));
				$non_member_ids_arr = array();
				foreach ($all_albums as $a) { 
					$aid = $a['Album']['id'];
					if (!in_array($aid, $member_ids_arr)) {
						$non_member_ids_arr[] = $aid;
					}
				}
				if (empty($non_member_ids_arr)) {
					$non_members = array();
				} else {
					$non_member_ids = join(',', $non_member_ids_arr);
					$filters = array();
					$filters[] = "id IN ($non_member_ids)";
					if (isset($search)) {
						$filters[] = "(lower(Album.name) like '%" . low($search) . "%' OR lower(Album.description) like '%" . low($search) . "%')"; 
					}
					$this->loadModel('Album');
					$this->Album->recursive = -1;
					$non_members = $this->paginate('Album', $filters);
					if (empty($non_members)) {
						$this->Session->del('MA.search');
						$this->data['search'] = '';
						$filters = array();
						$filters[] = "id IN ($non_member_ids)";
						$this->Album->recursive = -1;
						$non_members = $this->paginate('Album', $filters);
					}
				}
		
				$this->set('non_members', $non_members);
			}
		}
	}
	
	function add_smart_rule() {	}
	
	function page_smart($id) {
		$this->cacheAction = 30000;
		$this->Gallery->recursive = -1;
		$this->data = $this->Gallery->read(null, $id);
		list($albums,) = $this->_smart_content(unserialize($this->data['Gallery']['smart_query']));
		$this->set('albums', $albums);
		$this->set('gallery', $this->data);
		$this->set('options', unserialize($this->data['Gallery']['smart_query']));
		$this->render('smart', 'ajax');
	}
	
	function smart($id) {
		if (isset($this->data['conditions'])) {
			$conditions = array();
			$switch = '';
			foreach($this->data['conditions'] as $key => $c) {
				if (isset($c['switch'])) {
					$switch = $c['switch'];
					$bool = $c['bool'];
					list(,$random) = explode('_', $key);
					if ($switch == 'created') {
						$sw_str = 'date';
					} else {
						$sw_str = $switch;
					}
					$target = $this->data['conditions']["{$sw_str}_{$random}"];
					switch($switch) {
						case 'tag':
							if (isset($target['tag']) && !empty($target['tag'])) {
								$conditions[] = array('type' => 'tag', 'input' => $target['tag'], 'bool' => $bool);
							}
							break;
						case 'created':
							$go = false;
							$column = 'created_on';
							if ($target['modifier'] == 'within') {
								$target['filter_start'] = $target['filter_end'] = '';
								if (!empty($target['filter_within']) && is_numeric($target['filter_within'])) {
									$go = true;
								}
							} elseif (isset($target['filter_start']) && !empty($target['filter_start'])) {
								$go = true;
								$target['filter_within'] = $target['modifier_within'] = '';
							}
							
							if ($go) {
								$conditions[] = array('type' => 'date', 'column' => $column, 'start' => $target['filter_start'], 'end' => $target['filter_end'], 'modifier' => $target['modifier'], 'within' => $target['filter_within'], 'within_modifier' => $target['modifier_within'], 'bool' => $bool);
							}
							break;
					}
				}
			}
			if (isset($this->data['limit_on']) && $this->data['limit_on'] && is_numeric($this->data['limit'])) {
				$limit = $this->data['limit'];
			} else {
				$limit = '';
			}
			
			if (isset($this->data['limit_to']) && $this->data['limit_to'] && is_numeric($this->data['limit_to_filter'])) {
				$limit_to = $this->data['limit_to_filter'];
			} else {
				$limit_to = '';
			}
			
			@$conditions_array = array('limit' => $limit, 'limit_to' => $limit_to, 'any_all' => $this->data['any_all'], 'order' => $this->data['order'], 'order_direction' => $this->data['order_direction'], 'conditions' => $conditions);
			list($albums, $count) = $this->_smart_content($conditions_array);
		} else {
			$albums = $conditions_array = array();
			$count = 0;
		}
		$data['Gallery']['smart_query'] = serialize($conditions_array);
		if (is_numeric($count)) {
			$data['Gallery']['tag_count'] = $count;
		}
		$this->Gallery->id = $id;
		$this->Gallery->save($data);
		$this->set('options', $conditions_array);
		$this->set('gallery', $this->Gallery->read(null, $id));
		$this->set('albums', $albums);
	}
	
	function _smart_content($array, $page = true) {	
		if (empty($array) || empty($array['conditions'])) {
			$albums = array();
			$count = 0;
		} else {
			list($conditions, $order, $limit) = $this->Gallery->smartConditions($array);
			if (is_null($limit) && $page) {
				$this->loadModel('Album');
				$this->Album->recursive = -1;
				$this->paginate = array_merge($this->paginate, array('limit' => 20, 'order' => $order));
				$albums = $this->paginate('Album', $conditions);
				$count = $this->params['paging']['Album']['count'];
			} else {
				$albums = $this->Gallery->Tag->Album->find('all', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order, 'recursive' => -1));	
				if (count($albums) < $limit) {
					$count = count($albums);
				} else {
					$count = $limit;
				}
			}
		}
		return array($albums, $count);
	}
}

?>