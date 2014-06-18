<rsp stat="ok">
    <method>director.api.gallery.list</method>
    <format>rest</format>
	<galleries>
	<?php foreach ($galleries as $gallery): ?>
		<gallery>
			<?php e($api->xml($api->gallery($gallery, @$gallery['Tag'], $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users))); ?>
		</gallery>
	<?php endforeach; ?>
	</galleries>
</rsp>