<rsp stat="ok">
    <method>director.api.content.list</method>
    <format>rest</format>
	<contents>
		<?php foreach($data as $image): ?>
			<content>
			<?php e($api->xml($api->image($image['Image'], null, $this->data['size'], $this->data['user_size'], $active, $controller, $users, null, $image['Album']['watermark_id']))); ?>
			</content>
		<?php endforeach; ?>
	</contents>
</rsp>