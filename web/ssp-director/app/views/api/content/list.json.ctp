<?php 

	$arr = array();
	foreach($data as $image) {
		$arr[] = $api->json($api->image($image['Image'], null, $this->data['size'], $this->data['user_size'], $active, $controller, $users, null, $image['Album']['watermark_id']));
	} 
	$images = join(',', $arr);
?>{
	"stat": "ok",
	"method": "director.api.content.list",
	"format": "rest",
	"data" : {
		"contents": [
			<?php e($images); ?>
		]
	}
}