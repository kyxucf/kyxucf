<?php

class DirectorGallery extends DirectorWrapper {
	public function get($id) {
		$this->parent->post[] = 'data[gallery_id]=' . $id;
		$response = $this->parent->send('get_gallery', 'get_gallery_' . $id);
		return $response->gallery;
	}
	
	public function all() {
		$response = $this->parent->send('get_gallery_list', 'get_gallery_list');
		return $response->galleries[0];
	}
}

?>