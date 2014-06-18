<rsp stat="ok">
    <method>director.api.album.get</method>
    <format>rest</format>
	<album>
		<?php e($api->xml($api->album($album, $this->data['preview'], $this->data['size'], $this->data['user_size'], $active, $controller, $users))); ?>
	</album>
</rsp>