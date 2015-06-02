<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
//error_reporting(E_ALL);
//ini_set('display_errors', true);
if(!isset($_POST['l0']) or !isset($_POST['l1']) or !isset($_POST['t0']))
 die('<p>Error: Invalid request!</p>');

$text0 = $_POST['t0'];
$tn = strlen($text0);
if($tn > 255 or $tn == 0 or strpos($text0, '"', 0))
 die('<p>Error: Invalid request!</p>');


require 'languages.php';

$ln = count($languages);

$language0 = $_POST['l0'];
$si = array_search($language0, $languages);
if($si === false)
 die('<p>Error: Invalid request!</p>');

$language1 = $_POST['l1'];

$di = array_search($language1, $languages);
if($di === false)
 die('<p>Error: Invalid request!</p>');

if($si == $di)
 die('<p>Error: Invalid request!</p>');

if($si > $di){
 $language_pair = $language1 . '_' . $language0;
}
else{
 $language_pair = $language0 . '_' . $language1;
}

$table = 't_' . $language_pair;

require 'config.php';

require 'AddText.php';

$text0 = strtolower($text0);

$sid = InsertText($db_path . $language0 . '.db', $text0);
if($sid <= 0)
 die('<p>Error: ' . -$sid . '</p>');

try{
 $db_translation = new SQLite3($db_path . $language_pair. '.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
  die('Error: 1');
}

$sql = 'Select f_language1_id, f_good, f_bad from t_translation where f_language0_id = ' . $sid;
$ids = $db_translation->query($sql);

try{
  $db1 = new SQLite3($db_path . $language1 . '.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
 $db_translation->close();
 die('Error: 1');
}


while($row = $ids->fetchArray(SQLITE3_NUM)){
 $text1 = $db1->querySingle('Select f_text from t_text where f_id = ' . $row[0]);
 if($text1 === NULL){
  $db_translation->close();
  $db1->close();
  die('Error: 2');
 }
 echo '<tr><td>' . $text1 . '</td><td><a class="good" href="#">' . $row[1] . '</a></td><td><a class="bad" href="#">' . $row[2] . '</a></td></tr>' . "\n";
}
//  echo '<p>Not found!</p>';


$db_translation->close();
$db1->close();
?>
