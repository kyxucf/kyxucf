{
	"stat": "ok",
	"method": "director.api.app.totals",
	"format": "rest",
	"data" : {
		"count": "<?php e($data['image_count']); ?>",
		"size": "<?php e($data['total_size']); ?>"
	}
}