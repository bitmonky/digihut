<?php
$script = $_POST['digihutJS'];
$muid = getTag('?rad=','&',$script);
$j = new stdClass;
$j->url = getTag('async src="','"',$script);
if (strpos($j->url,'gsoft.bitmonky.com') === false){
  exit("No Change");
}
$resp = tryJFetchURL($j);
if ($resp->error === false){
  $localJS = $resp->data;
  $fname = dirname(__FILE__).'/digihutJS.php';
  file_put_contents($fname,$localJS);
  $script = str_replace('https://gsoft.bitmonky.com/whzon/adMgr/srvWebAssistantJS.php','/wp-content/plugins/digihut/digihutJS.php',$script);
}
else {
  exit(json_encode($resp));
}  
$fname = dirname(__FILE__).'/digihut.js';
file_put_contents($fname, $script);
echo 'OK'.$muid.$link; //safeSRV('SERVER_NAME');

function safeSRV($name){
  if (isset($_SERVER[$name])){
    return $_SERVER[$name];
  }
  return null;
}
function tryJFetchURL($j,$method='GET',$timeout=5){
    $resp = new stdClass;
    $crl = curl_init();
    curl_setopt ($crl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt ($crl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($crl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($crl, CURLOPT_URL,$j->url);
    curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt ($crl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt ($crl, CURLOPT_USERAGENT,safeSRV('HTTP_USER_AGENT'));
    curl_setopt ($crl, CURLOPT_MAXREDIRS,5);
    curl_setopt ($crl, CURLOPT_REFERER, safeSRV('SERVER_NAME'));
    curl_setopt ($crl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    if ($method == 'POST'){
      $j->post = "sending post data:".$j->postd;
      curl_setopt ($crl, CURLOPT_POSTFIELDS, $j->postd);
    }

    curl_setopt ($crl, CURLOPT_HTTPHEADER , array(
      'accept: application/json',
      'content-type: application/json')
    );

    $resp->data  = curl_exec($crl);
    if ($resp->data === null) {
      $resp->data = "Document tryJFetchURL  ".$j->url." Failed";
    }

    $resp->error = false;
    if ($resp->data === false) {
      $resp->error = curl_error($crl);
    }
    else {
      $info = curl_getinfo($crl);
      $resp->rcode = $info['http_code'];
      $resp->furl  = curl_getinfo($crl, CURLINFO_EFFECTIVE_URL);
    }
    curl_close($crl);
    return $resp;
}
function getTag($s,$e,$doc){
  $spos = stripos($doc,$s);
  if ($spos === false){
    return null;
  }
  $spos = $spos + strlen($s);
  $tag = right($doc,strlen($doc) - $spos);

  $epos = stripos($tag,$e);
  if ($epos === false){
    return null;
  }
  $tag = left($tag,$epos);
  if ($tag == '' || $tag ==  ' '){
   return null;
  }
  return $tag;
}
function left($str, $length) {
  if ($str === null){return null;}
  return substr($str, 0, $length);
}
function right($str, $length) {
     return substr($str, -$length);
}
?>
