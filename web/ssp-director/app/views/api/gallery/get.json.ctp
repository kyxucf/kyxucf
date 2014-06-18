{
	"stat": "ok",
	"method": "director.api.gallery.get",
	"format": "rest",
	"data" : <?php e($api->json($api->gallery($gallery, $albums, $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users))); ?>
}