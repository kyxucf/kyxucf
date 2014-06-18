<?php

  require_once('crossdomain.php');
  $ssp_crossDomain = $_POST['ssp_crossDomain'];
  $xml = ssp_crossdomain_xml($ssp_crossDomain);
  
  $filename = 'crossdomain.xml';
  
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private", false);
  header("Content-type: application/force-download");
  header("Content-Disposition: attachment; filename=\"".$filename."\";" );
  header("Content-Transfer-Encoding: binary");
  header("Content-Length: ".strlen($xml));
  echo $xml;
  exit();
  
?>