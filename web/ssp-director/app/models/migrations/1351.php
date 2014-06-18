<?php

	if (!defined('MIGRATE')) {exit;}
	App::import('Model', 'Image');
	$this->Image =& new Image();
	
	$vids = $this->Image->find('all', array('conditions' => array('Image.is_video' => 1, 'Image.lg_preview_id' => null), 'fields' => 'Image.id, Image.aid, Image.src', 'recursive' => -1));
	$this->Image->coldSave = true;
	foreach($vids as $v) {
		$path = ALBUMS . DS . 'album-' . $v['Image']['aid'];
		$customs = glob($path . DS . 'lg' . DS . '___tn___' . r($this->Director->returnExt($v['Image']['src']), '', $v['Image']['src']) . '*');
		if (!empty($customs)) {
			$src = basename($customs[0]);
			$lg_path = $path . DS . 'lg' . DS . $src;
			list($meta, $captured_on) = $this->Director->imageMetadata($lg_path);
			$this->Image->create();
			$data = array();
			$data['Image']['aid'] = $v['Image']['aid'];
			$data['Image']['src'] = $src;
			$data['Image']['seq'] = 1000;
			$data['Image']['active'] = 0;
			$data['Image']['captured_on'] = $captured_on;
			$data['Image']['filesize'] = filesize($lg_path);
			$this->Image->save($data);
			$noob = $this->Image->getLastInsertId();
			$str = "$src:50:50";
			$d = array('lg_preview' => "'$str'", 'tn_preview' => "'$str'", 'lg_preview_id' => $noob, 'tn_preview_id' => $noob);
			$this->Image->updateAll($d, array('Image.id' => $v['Image']['id']));
		}
	}
?>