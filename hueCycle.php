<?php

date_default_timezone_set('Europe/Stockholm');

if (!(is_file(getcwd().'/settings.txt'))) die('settings.txt does not exist');
$ini = parse_ini_file(getcwd().'/settings.txt');

if (isset($argv[1])) {
    $thirsty = $argv[1];
} else {
    echo "No input THIRSTY, assuming 83\n";
    $thirsty = 83;
}

if (isset($argv[2])) {
    $dayOfTheWeek = $argv[2];
} else {
    $dayOfTheWeek = date('N');
}

if (isset($argv[3])) {
    $hour = $argv[3];
} else {
    $hour = date('H');
}

$dayColors = array( 1 => 12750, //yellow
                    2 => 56100, //pink
                    3 => 25500, //green
                    4 => 12000, //orange
                    5 => 47000, //light blue
                    6 => 56000, //purple
                    7 => 65280, //red
                  );

// Hue beroende på dag i veckan
//$dayOfTheWeek = rand(1, 7);
$hue = $dayColors[$dayOfTheWeek];

// Saturation ju mer ju törstigare
$modifyer = ($thirsty-83)*6.8;
if ($modifyer < 0) $modifyer *= -1;
$sat = round(255-$modifyer);
if ($sat < 0) $sat *= -1;

if (isset($argv[3])) {
    $oneHour = 10*10;
} else {
    $oneHour = (10*60*60)-20;
}

// Brightness ju mer ju mörkare
// Mörkt mest hela tiden så här års (december)

// If kl 15, bri: 250 transitiontime 10*60*60 (1h)
// Kanske inte fullt bri mitt i natten.. hitta bra max-värde
// Kl 00, fade to new day
// If kl 8, bri: 0, transitiontime 10*60*60 (1h)
// If kl 9, stäng av

if ($hour == 15) {
    $onOffStr = '"on":true,';
    $briStr = '"bri":150,';
    $transitionTime = $oneHour;
} elseif ($hour == 0) {
    $onOffStr = '';
    $briStr = '';
    $transitionTime = $oneHour;
} elseif ($hour == 8) {
    $onOffStr = '';
    $briStr = '"bri":0,';
    $transitionTime = $oneHour;
} elseif ($hour == 9) {
    $onOffStr = '"on":false,';
    $briStr = '';
    $transitionTime = 0;
} else {
    $onOffStr = '';
    $briStr = '';
    $transitionTime = 0;
}

//echo "Stuffs:\n";
//var_dump($thirsty, $dayOfTheWeek, $hour, $hue, $sat, $briStr);

$json = '{'.$onOffStr.$briStr.'"sat":'.$sat.',"hue":'.$hue.',"transitiontime":'.$transitionTime.'}';
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

/*
Day         Color of the day    Unlucky color   Celestial Body  God of the day
Sunday      red                 blue            Sun             Surya
Monday      yellow or cream     red             Moon            Chandra
Tuesday     pink                yellow & white  Mars            Mangala
Wednesday   green               pink            Mercury         Budha
Thursday    orange or brown     purple          Jupiter         Brihaspati
Friday      light blue          black & blue    Venus           Shukra
Saturday    purple or black     green           Saturn          Shani
*/

