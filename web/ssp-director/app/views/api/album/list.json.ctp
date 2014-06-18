<?php 

	$arr = array();
	foreach($albums as $album) {
		$arr[] = $api->json($api->album($album, $this->data['preview'], $this->data['size'], $this->data['user_size'], true, $controller, $users));
	} 
	$albums = join(',', $arr);
?>{
	"stat": "ok",
	"method": "director.api.album.list",
	"format": "rest",
	"data" : {
		"albums": [
			<?php e($albums); ?>
		]
	}
}