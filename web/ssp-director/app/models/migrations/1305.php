<?php

	if (!defined('MIGRATE')) {exit;}

	$conditions = array();
	$conditions[] = array('type' => 'date', 'column' => 'created_on', 'start' => '', 'end' => '', 'modifier' => 'within', 'within' => '1', 'within_modifier' => 'month', 'bool' => true);
	
	$conditions_array = array('limit' => '', 'limit_to' => '', 'any_all' => 1, 'order' => 'date', 'order_direction' => 'DESC', 'conditions' => $conditions);
	
	$conditions_sql = serialize($conditions_array);
	
	$data = array();
	$data['Album']['name'] = 'Past month';
	$data['Album']['description'] = 'All content uploaded within the past month.';
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
	$count = $this->Album->Image->find('count', array('conditions' => $conditions));	
	
	$data = array();
	$data['Album']['images_count'] = $count;
	$data['Album']['path'] = $path;
	
	$this->Album->save($data);
?>