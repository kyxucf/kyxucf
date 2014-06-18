<rsp stat="ok">
    <method>director.api.album.list</method>
    <format>rest</format>
	<albums>
	<?php foreach($albums as $album): ?>
		<album>
			<?php e($api->xml($api->album($album, $this->data['preview'], $this->data['size'], $this->data['user_size'], true, $controller, $users))); ?>
		</album>
	<?php endforeach; ?>
	</albums>
</rsp>