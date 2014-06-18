<rsp stat="ok">
    <method>director.api.user.list</method>
    <format>rest</format>
	<users>
	<?php foreach ($user_ids as $id): ?>
		<user>
			<?php e($api->xml($api->user($id, $users, $this->data['user_size']))); ?>
		</user>
	<?php endforeach; ?>
	</users>
</rsp>