<?php

	if (!defined('MIGRATE')) {exit;}
	
	@$this->_query("UPDATE $atbl SET title_template = REPLACE(title_template, '[img_name]', '[director:image filename]')");
	@$this->_query("UPDATE $atbl SET title_template = REPLACE(title_template, '[album_name]', '[director:album name]')");
	@$this->_query("UPDATE $atbl SET caption_template = REPLACE(caption_template, '[img_name]', '[director:image filename]')");
	@$this->_query("UPDATE $atbl SET caption_template = REPLACE(caption_template, '[iptc_caption]', '[iptc:caption]')");
	@$this->_query("UPDATE $itbl SET tags = REPLACE(tags, 'undefined', '') WHERE tags LIKE '%undefined%'");
	
	@$this->_query("UPDATE $atbl SET sort_type = 'file name (oldest first)' WHERE sort_type = 'file name'");
	@$this->_query("UPDATE $dtbl SET sort_type = 'album title (oldest first)' WHERE sort_type = 'album title'");

?>