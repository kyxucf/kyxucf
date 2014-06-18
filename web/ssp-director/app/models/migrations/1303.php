<?php

	if (!defined('MIGRATE')) {exit;}

	$conditions = array();
	$conditions[] = array('type' => 'date', 'column' => 'created_on', 'start' => '01/01/2000', 'end' => '', 'modifier' => 'after', 'within' => '', 'within_modifier' => '', 'bool' => true);
	
	$conditions_array = array('limit' => 50, 'limit_to' => 0, 'any_all' => 1, 'order' => 'date', 'order_direction' => 'DESC', 'conditions' => $conditions);
	
	$conditions_sql = serialize($conditions_array);
	
	$data = array();
	$data['Album']['name'] = 'Recent images';
	$data['Album']['description'] = 'The 50 most recently uploaded images.';
	$data['Album']['smart'] = 1;
	$data['Album']['smart_query'] = $conditions_sql;

	App::import('Model', 'Album');
	$this->Album =& new Album();
	
	$this->Album->create();
	$this->Album->save($data);
	$this->Album->id = $this->Album->getLastInsertId();
	$path = 'album-' . $this->Album->id;
	$this->Director->makeDir(ALBUMS . DS . $path);
	$this->Director->createAlbumDirs($this->Album->id);
	
	list($conditions, $order, $limit) = $this->Album->smartConditions($conditions_array);
	$count = $this->Album->Image->find('count', array('conditions' => $conditions, 'limit' => $limit, 'order' => $order));	
	
	$data = array();
	if ($count > 50) {
		$count = 50;
	}
	$data['Album']['images_count'] = $count;
	$data['Album']['path'] = $path;
	
	$this->Album->save($data);
?>