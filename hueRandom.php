<?php

if (!(is_file(getcwd().'/settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file(getcwd().'/settings.txt');

$bri = rand(150,255);
$sat = rand(1,255);
$hue = rand(1,65280);
$json = '{"on":true,"bri":'.$bri.',"sat":'.$sat.',"hue":'.$hue.'}';
$json = str_replace('"', '\"', $json);
$uri = $ini['hue_uri'];

try {
  ob_start();
  $curl = 'curl -s -X PUT --data "'.$json.'" '.$uri;
  passthru($curl);
  $response = ob_get_contents();
  ob_end_clean();

  $retArr = json_decode($response, true);

  return $retArr;
} catch (Exception $e) {
  throw($e);
}
