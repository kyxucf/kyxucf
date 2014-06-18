<?php 

	$arr = array();
	foreach($galleries as $gallery) {
		$arr[] = $api->json($api->gallery($gallery, @$gallery['Tag'], $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users));
	} 
	$galleries = join(',', $arr);
?>{
	"stat": "ok",
	"method": "director.api.gallery.list",
	"format": "rest",
	"data" : {
		"galleries": [
			<?php e($galleries); ?>
		]
	}
}