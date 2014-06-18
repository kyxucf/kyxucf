{
	"stat": "ok",
	"method": "director.api.album.get",
	"format": "rest",
	"data" : <?php e($api->json($api->album($album, $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users))); ?>
}