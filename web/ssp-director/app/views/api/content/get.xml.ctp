<rsp stat="ok">
    <method>director.api.content.get</method>
    <format>rest</format>
	<content>
		<?php e($api->xml($api->image($image['Image'], $image['Album'], $this->data['size'], $this->data['user_size'], false, $controller, $users, null, $image['Album']['watermark_id']))); ?>
	</content>
</rsp>