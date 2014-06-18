<?php

class WatermarksController extends AppController
{
    var $name = 'Watermarks';
	var $helpers = array('Html', 'Javascript', 'Ajax');
	
	// Only logged in users should see this controller's actions
 	function beforeFilter() {
		$this->verifyAjax();
		// Check session
		$this->checkSession();
	}
	
	////
	// Slideshows listing
	////
	function listing() {
		$this->Watermark->cacheQueries = false;
		$this->set('watermarks', $this->Watermark->find('all'));
		$this->render('list', 'ajax');
	}
	
	////
	// Delete gallery
	////
	function delete() {
		if ($this->Watermark->del($this->data['Watermark']['id'])) {
			$this->listing();
			$this->_clear_album_cache($this->data['Watermark']['id']);
			$this->Album->updateAll(array('watermark_id' => "NULL"), array('watermark_id' => $this->data['Watermark']['id']));
			$delete = glob(WATERMARKS . DS . $this->data['Watermark']['id'] . '.*');
			if (!empty($delete)) {
				unlink($delete[0]);
			}
		}
	}
	
	function _clear_album_cache($id) {
		$this->loadModel('Album');
		$this->Album->Behaviors->attach('Containable');
		$albums = $this->Album->find('all', array('conditions' => array('watermark_id' => $id), 'contain' => 'Tag', 'fields' => 'Album.id'));
		foreach($albums as $album) {
			$this->Album->popCache($album);
		}
	}
	function update() {
		if (is_numeric($this->data['Watermark']['main'])) {
			$this->Watermark->updateAll(array('main' => 0));
		} else {
			$this->data['Watermark']['main'] = 0;
		}
		$this->Watermark->save($this->data);
		$this->_clear_album_cache($this->data['Watermark']['id']);
		$this->listing();
	}
}

?>