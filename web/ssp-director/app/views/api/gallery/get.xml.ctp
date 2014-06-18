<rsp stat="ok">
    <method>director.api.gallery.get</method>
    <format>rest</format>
	<gallery>
		<?php e($api->xml($api->gallery($gallery, $albums, $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users))); ?>
	</gallery>
</rsp>