<?php

class DirectorAlbum extends DirectorWrapper {
	public function get($id, $options = array()) {
		$defaults = array('only_active' => true);
		$options = array_merge($defaults, $options);
		$this->parent->post[] = 'data[only_active]=' . $options['only_active'];
		$this->parent->post[] = 'data[album_id]=' . $id;
		$response = $this->parent->send('get_album', 'get_album_' . $id . '_' . (int) $options['only_active']);
		return $response->album;
	}
	
	public function all($options = array()) {
		$defaults = array(	'only_published' => true, 
							'only_active' => true,
							'list_only' => false,
							'only_smart' => false,
							'exclude_smart' => false
						);
		$options = array_merge($defaults, $options);
		$this->parent->post[] = 'data[only_published]=' . $options['only_published'];
		$this->parent->post[] = 'data[only_active]=' . $options['only_active'];
		$this->parent->post[] = 'data[list_only]=' . $options['list_only'];
		$this->parent->post[] = 'data[only_smart]=' . $options['only_smart'];
		$this->parent->post[] = 'data[exclude_smart]=' . $options['exclude_smart'];
		$response = $this->parent->send('get_album_list', 'get_album_list_' . (int) $options['only_published'] . '_' . (int) $options['only_active'] . '_' . (int) $options['list_only'] . '_' . (int) $options['only_smart']);
		return $response->albums[0];
	}
	
	public function galleries($album_id, $options = array()) {
		$defaults = array('exclude' => 0);
		$options = array_merge($defaults, $options);
		if (is_array($options['exclude'])) {
			$options['exclude'] = join(',', $options['exclude']);
		}
		$this->parent->post[] = 'data[album_id]=' . $album_id;
		$this->parent->post[] = 'data[exclude]=' . $options['exclude'];
		$response = $this->parent->send('get_associated_galleries', 'get_associated_galleries_' . $album_id . '_' . $options['exclude']);
		return $response->galleries[0];
	}
}

?>