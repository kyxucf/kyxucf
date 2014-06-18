<?php

	function download_file($remote, $local = null) {
		if (is_null($local)) {
			$local = basename($remote);
		}
		if (extension_loaded('curl')) {
			$cp = curl_init($remote);
			$fp = fopen($local, "w");
			if (!$fp) {
				curl_close($cp);
				return false;
			} else {
				curl_setopt($cp, CURLOPT_FILE, $fp);
				curl_exec($cp);
				curl_close($cp);
				fclose($fp);
			}	
		} elseif (ini_get('allow_url_fopen')) {
			if (!copy($remote, $local)) {
				return false;
			}
		} else {
			$bits = explode('slideshowpro.net/', $remote);
			$relative_path = $bits[1];

			$headers = "GET /$relative_path HTTP/1.1\r\n";
			$headers .= "Host: install.slideshowpro.net\r\n";

			$socket = fsockopen('install.slideshowpro.net', 80, $errno, $errstr, 60);

			if ($socket) {
				fwrite($socket, $headers.$post."\r\nConnection: Close\r\n\r\n");
				$response = '';
				while (!feof($socket)) {
					$response .= fgets($socket, 1024);
				}
				$response = explode("\r\n\r\n", $response, 2);
				$response = $response[1];
				fclose($socket);
			} else {
				return false;
			}
		
			$fp = fopen($local, "w");
			if (!$fp) {
				return false;
			} else {
				fwrite($fp, $response);
				fclose($fp);
			}
		}
		return true;
	}
	
	function extract_callback($p_event, &$p_header) {
		$current_dir = ROOT;
		$current_perms = substr(sprintf('%o', fileperms($current_dir)), -4);
		chmod($current_dir . DS . $p_header['filename'], octdec($current_perms));
		return 1;
	}
	
	function rollback($movers) {
		foreach($movers as $m) {
			$path = ROOT . DS . $m;
			$to = $path . '.off';
			if (is_dir($to)) {
				if (is_dir($path)) {
					$fr = new Folder($path);
					$fr->delete();
				}
				$f = new Folder($to);
				$f->move($path);
			} else if (file_exists($to)) {
				if (file_exists($path)) {
					unlink($path);
				}
				rename($to, $path);
			}
		}
	}

?>