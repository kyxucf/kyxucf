<?php

	if (!defined('MIGRATE')) {exit;}
	
	if (is_callable('exif_read_data') || is_callable('iptcparse')) {
		$result = $this->_query("SELECT i.*, a.path FROM $itbl AS i, $atbl AS a WHERE i.aid = a.id AND i.captured_on IS NULL");
		if ($this->_rows($result) > 0) {
			while($row = $this->_array($result)) {	
				$path = ALBUMS . DS . $row['path'] . DS . 'lg' . DS . $row['src'];
				if (!isNotImg(basename($path))) {
					$meta = array();
					if (is_callable('iptcparse')) {
						getimagesize($path, $info);
						if (!empty($info['APP13'])) {
							$meta['IPTC'] = iptcparse($info['APP13']);
						}
						if (!empty($iptc['2#055'][0]) && !empty($iptc['2#060'][0])) {
							$captured_on = strtotime($iptc['2#055'][0] . ' ' . $iptc['2#060'][0]);
						}
					}
					
					if (eregi('\.jpg|\.jpeg', basename($path)) && is_callable('exif_read_data')) {
						$exif_data = exif_read_data($path, 0, true);
						$meta['Exif'] = $exif_data;
						if (isset($meta['Exif']['EXIF']['DateTimeDigitized'])) {
							$dig = $meta['Exif']['EXIF']['DateTimeDigitized'];
							$bits = explode(' ', $dig);
							$captured_on = strtotime(str_replace(':', '-', $bits[0]) . ' ' . $bits[1]);
						}
					}
					
					if (isset($captured_on) && is_numeric($captured_on)) {
						$query = "UPDATE $itbl SET captured_on = $captured_on WHERE id = {$row['id']}";
					} else {
						$query = "UPDATE $itbl SET captured_on = 0 WHERE id = {$row['id']}";
					}
					$this->_query($query);
					$meta = array();
					unset($captured_on);
				}			
			}
		}
	}

?>