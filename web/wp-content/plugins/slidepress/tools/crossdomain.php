<?php
	function ssp_crossdomain_xml($string) {
		$array = explode("\n",preg_replace('/\r\n|\r/', "\n", $string));
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'. "\n";
		$xml .= '<cross-domain-policy>' . "\n";
		foreach ($array as $domain) {
			$xml .= '<allow-access-from domain="' . htmlentities($domain) . '" />' . "\n";
		}
		$xml .= '</cross-domain-policy>';
		return $xml;
	}
?>