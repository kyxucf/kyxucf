<?php

////
// Changes to any code in this file will result in the immediate termination
// of your license for SlideShowPro Director.
////

class PigeonComponent extends Object {
   	var $controller = true;
 	var $curl 		= false;
	var $host 		= 'www.sspdirector.com';
	var $hole 		= '/';

    function startup (&$controller) {
        $this->controller = &$controller;
		$this->curl = in_array('curl', get_loaded_extensions());
    }

	function activate($key, $xfer = false) {
		$post = 'domain=' . $this->baseDomain() . '&key=' . $key . '&transfer=' . $xfer . '&php=' . PHP_VERSION . '&os=' . PHP_OS . '&server_software=' . env('SERVER_SOFTWARE') . '&dir_version=' . DIR_VERSION;
		list($code, $result) = $this->_ping('Activation Ping', $post);
		if (strpos($result, 'Please try again in a few minutes') !== false && $code == 0) {
			$code = 0;
		} elseif ($result != 'SUCCESS' && $code == 0) {
			$code = 1;
		}
		return array($code, $result);
	}
	
	function test() {
		return $this->_ping('Test Ping', 'testing=1');
	}
	
	function news() {
		$cache_dir = DIR_CACHE . DS . 'news.cache';
		$news = cache($cache_dir, null, '+2 hours');
		if (empty($news)) {
			$this->host = 'feeds.feedburner.com';
			$this->hole = '/slideshowpro';
			list($status, $xml) = $this->_ping('News Ping');
			if (!preg_match('/not connect to feeds.feedburner.com/', $xml)) {
				$latest = $this->__parseTag('item', $xml);
				$description = $this->__parseTag('description', $latest);
				preg_match('/^<!\[CDATA\[(.*)\]\]>$/s', $description, $matches);
				if (!empty($matches)) {
					$description = $matches[1];
				}
				$description = str_replace('[...]', '', $description);
				$news = array(
							'title' => $this->__parseTag('title', $latest), 
							'description' => $description,
							'date' => $this->__parseTag('pubDate', $latest), 
							'link' => $this->__parseTag('guid', $latest)
							);
				cache($cache_dir, serialize($news));
			} else {
				$news = array();
				cache('director/news', 'no news');
			}
			$this->host = 'www.sspdirector.com';
		} else if ($news == 'no news') {
			$news = array();
		} else {
			$news = unserialize($news);
		}
		return $news;
	}
	
	function version($force = false) {
		$cache_dir = DIR_CACHE . DS . 'version.cache';
		$version = cache($cache_dir, null, '+30 minutes');
		if (empty($version) || $force) {
			if (defined('BETA_TEST') && BETA_TEST) {
				$v = 3;
			} else {
				$v = 2;
			}
			$this->hole = '/?version=' . $v;
			list($status, $version) = $this->_ping('Version Ping');
			if ($status != 2) {
				cache($cache_dir, trim($version));
			} else {
				$version = DIR_VERSION;
			}
		}
		return $version;
	}
	
	function quick_start() {
		$cache_dir = DIR_CACHE . DS . 'quick_start.cache';
		$news = cache($cache_dir, null, '+1 day');
		if (empty($news)) {
			$this->hole = '/rss/help_spotlight.xml';
			list($status, $xml) = $this->_ping('Quick Start Ping');
			if (!preg_match('/not connect to sspdirector.com/', $xml)) {
				$all = $this->__parseTag('item', $xml, true);
				$quicks = array();
				foreach($all as $latest) {
				$quicks[] = array(
							'title' => $this->__parseTag('title', $latest), 
							'link' => $this->__parseTag('link', $latest)
							);
				}
				cache($cache_dir, serialize($quicks));
			} else {
				$quicks = array();
				cache($cache_dir, 'no news');
			}
		} else if ($news == 'no news') {
			$quicks = array();
		} else {
			$quicks = unserialize($news);
		}
		return $quicks;
	}
	
	function __parseTag($tag, $haystack, $all = false) {
		$pattern = '/<' . $tag . '[^>]*>(.+)<\/' . $tag . '\>/iUs';
		if ($all) {
			preg_match_all($pattern, $haystack, $matches);
		} else {
			preg_match($pattern, $haystack, $matches);
		}
		return $matches[1];
	}
	
	function isLocal() {
		return (preg_match('/^(127\.0\.0\.1|localhost)(:\d+)?$/i', $this->baseDomain())) ? true : false;
	}
	
	function baseDomain() {
		return preg_replace('/(^www\.|:\d+$)/', '', env('HTTP_HOST'));
	}
	
	function _ping($action, $post = false) {
		$status = 0;
		$pinger = "X-director-ping: $action";
		if ($this->curl) {
			$handle	= curl_init("http://{$this->host}{$this->hole}");
			curl_setopt($handle, CURLOPT_HTTPHEADER, array($pinger));
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($handle, CURLOPT_PORT, 80);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
			if (defined('ACTIVATION_PROXY')) {
				curl_setopt($handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
				if (defined('ACTIVATION_LOGIN')) {		
					curl_setopt($handle, CURLOPT_PROXYUSERPWD, ACTIVATION_LOGIN);
				}
				curl_setopt($handle, CURLOPT_PROXY, ACTIVATION_PROXY);
				curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
			}
			if ($post) {
				curl_setopt($handle, CURLOPT_POST, true);
				curl_setopt($handle, CURLOPT_POSTFIELDS, $post);
			}
			$response = curl_exec($handle);
			if (curl_errno($handle)) {
				$status = 2;
				$response = 'Could not connect to sspdirector.com (using cURL): ' . curl_error($handle);
			}
			curl_close($handle);
		} else {
			$headers = ($post ? 'POST' : 'GET') . " {$this->hole} HTTP/1.0\r\n";
			$headers .= "Host: {$this->host}\r\n";
			$headers .= "{$pinger}\r\n";
			if ($post) {
				$headers .= "Content-type: application/x-www-form-urlencoded\r\n";
				$headers .= "Content-length: " . strlen($post) . "\r\n";
			}
			$headers .= "\r\n";
			
			$socket = @fsockopen($this->host, 80, $errno, $errstr, 10);
			
			if ($socket) {
				$towrite = $headers;
				if ($post) { $towrite .= $post; }
				fwrite($socket, $towrite);
				$response = '';
				while (!feof($socket)) {
					$response .= fgets ($socket, 1024);
				}
				$response = explode("\r\n\r\n", $response, 2);
				$response = trim($response[1]);
			} else {
				$status = 2;
				$response = 'Could not connect to sspdirector.com (using fsockopen): '.$errstr.' ('.$errno.')';
			}
		}
		return array($status, $response);
	}
}

?>