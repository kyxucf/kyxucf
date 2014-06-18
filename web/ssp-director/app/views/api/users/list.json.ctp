<?php 

	$arr = array();
	foreach($user_ids as $id) {
		$arr[] = $api->json($api->user($id, $users, $this->data['user_size']));
	} 
	$users = join(',', $arr);
?>{
	"stat": "ok",
	"method": "director.api.gallery.list",
	"format": "rest",
	"data" : {
		"users": [
			<?php e($users); ?>
		]
	}
}