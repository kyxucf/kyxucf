<?php

	if (!defined('MIGRATE')) {exit;}
	
	$data = array();
	$data['Album']['name'] = 'Demonstration content';
	$data['Album']['description'] = 'Content intended for testing out SlideShowPro Director. Feel free to delete.';
	$data['Album']['title_template'] = '[director:album title]';
	$data['Album']['link_template'] = 'http://slideshowpro.net';
	$data['Album']['caption_template'] = '[iptc:caption]';
	$data['Album']['images_count'] = 3;
	$data['Album']['active'] = 1;
	
	App::import('Model', 'Album');
	$this->Album =& new Album();
	
	$this->Album->create();
	$this->Album->save($data);
	$this->Album->id = $album_id = $this->Album->getLastInsertId();
	
	$images = array('grass_sky.jpg', 'library.jpg', 'typography.jpg');
	
	$i = 1;
	foreach($images as $image) {
		$this->Album->Image->create();
		$data = array();
		$data['Image']['src'] = $image;
		$data['Image']['aid'] = $album_id;
		$data['Image']['seq'] = $i; $i++;
		$data['Image']['album_active'] = 1;
		$data['Image']['filesize'] = filesize(ALBUMS . DS . 'album-' . $album_id . DS . 'lg' . DS . $image);
		$this->Album->Image->save($data);
		
		if ($image == 'grass_sky.jpg') {
			$preview_id = $this->Album->Image->getLastInsertId();
			$this->Album->saveField('aTn', 'grass_sky.jpg:' . $preview_id . ':50:50');
		}
	}
	
	$this->Album->refreshSmartCounts();
	
	$main = $this->Album->Tag->Gallery->find('first', array('conditions' => 'Gallery.main = 1', 'recursive' => 1));
	$data['Tag']['did'] = $main['Gallery']['id'];
	$data['Tag']['aid'] = $album_id;
	$this->Album->Tag->save($data);
	
	$this->Album->Tag->Gallery->id = $main['Gallery']['id'];
	$this->Album->Tag->Gallery->saveField('tag_count', 1);
?>