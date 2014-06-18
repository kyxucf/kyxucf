{
	"stat": "ok",
	"method": "director.api.content.get",
	"format": "rest",
	"data" : <?php e($api->json($api->image($image['Image'], $image['Album'], $this->data['size'], $this->data['user_size'], false, $controller, $users, null, $image['Album']['watermark_id']))); ?>
}