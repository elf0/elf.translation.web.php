<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
//error_reporting(E_ALL);
//ini_set('display_errors', true);
if(!isset($_POST['l0']) or !isset($_POST['l1']) or !isset($_POST['t0']))
 die('<p>Error: 1</p>');

$text0 = $_POST['t0'];
$tn = strlen($text0);
if($tn > 255 or $tn == 0 or strpos($text0, '"', 0))
 die('<p>Error: 1</p>');


require 'languages.php';

$ln = count($languages);

$l0 = $_POST['l0'];
if($l0 >= $ln)
 die('<p>Error: 1</p>');

$l1 = $_POST['l1'];
if($l1 >= $ln)
 die('<p>Error: 1</p>');

if($l0 == $l1)
 die('<p>Error: 1</p>');

$language0 = $languages[$l0];
$language1 = $languages[$l1];


if($l0 > $l1){
 $language_pair = $language1 . '_' . $language0;
}
else{
 $language_pair = $language0 . '_' . $language1;
}

require 'config.php';

require 'AddText.php';

$text0 = strtolower($text0);

$l0_id = InsertText($db_path . $language0 . '.db', $text0);
if($l0_id <= 0)
 die('<p>Error: ' . -$l0_id . '</p>');

try{
 $db_translation = new SQLite3($db_path . $language_pair. '.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
  die('Error: 1');
}

$sql = 'Select f_id, f_language1_id, f_good, f_bad from t_translation where f_language0_id = ' . $l0_id;
$ids = $db_translation->query($sql);

try{
  $db1 = new SQLite3($db_path . $language1 . '.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
 $db_translation->close();
 die('Error: 1');
}


while($row = $ids->fetchArray(SQLITE3_NUM)){
 $text1 = $db1->querySingle('Select f_text from t_text where f_id = ' . $row[1]);
 if($text1 === NULL){
  $db_translation->close();
  $db1->close();
  die('Error: 2');
 }
 echo '<tr><td>' . $text1 . '</td><td><a href="#" onclick="vote(this, ' . $l0 . ', ' . $l1 . ', '
  . $row[0] . ', 0' . ')">' . $row[2] . '</a></td><td><a href="#" onclick="vote(this, ' . $l0 . ', ' . $l1 . ', ' . $row[0] . ', 1' . ')">' . $row[3] . '</a></td></tr>' . "\n";
}
//  echo '<p>Not found!</p>';


$db_translation->close();
$db1->close();
?>
