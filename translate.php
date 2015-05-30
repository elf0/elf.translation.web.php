<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
//error_reporting(E_ALL);
//ini_set('display_errors', true);
if(!isset($_POST['s']) or !isset($_POST['d']) or !isset($_POST['t']))
 die('<p>Error: Invalid request!</p>');

$sourceText = $_POST['t'];
$tn = strlen($sourceText);
if($tn > 255 or $tn == 0 or strpos($sourceText, '"', 0))
 die('<p>Error: Invalid request!</p>');


require 'languages.php';

$ln = count($languages);

$sourceLanguage = $_POST['s'];
$si = array_search($sourceLanguage, $languages);
if($si === false)
 die('<p>Error: Invalid request!</p>');

$destinationLanguage = $_POST['d'];

$di = array_search($destinationLanguage, $languages);
if($di === false)
 die('<p>Error: Invalid request!</p>');

if($si == $di)
 die('<p>Error: Invalid request!</p>');

if($si > $di){
 $language_pair = $destinationLanguage . '_' . $sourceLanguage;
}
else{
 $language_pair = $sourceLanguage . '_' . $destinationLanguage;
}

$table = 't_' . $language_pair;

require 'config.php';

require 'AddText.php';

if($sourceLanguage == 'english')
 $sourceText = strtolower($sourceText);

$sid = InsertText($db_path . $sourceLanguage . '.db', $sourceText);
if($sid <= 0)
 die('<p>Error: ' . -$sid . '</p>');

try{
 $db_translation = new SQLite3($db_path . 'translation.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
  die('Error: 1');
}

$field = 'f_' . $sourceLanguage . '_id';
$sql = 'Select ' . 'f_' . $destinationLanguage . '_id, f_good, f_bad' . ' from ' . $table . ' where f_' . $sourceLanguage . '_id = ' . $sid;
$ids = $db_translation->query($sql);

//echo '<tr><td>' . $table . '</td><td>' . $sql . '</td></tr>' . "\n";

try{
  $db_destination = new SQLite3($db_path . $destinationLanguage . '.db', SQLITE3_OPEN_READONLY);
}
catch(Exception $e){
 $db_translation->close();
 die('Error: 1');
}


while($row = $ids->fetchArray(SQLITE3_NUM)){
 $destinationText = $db_destination->querySingle('Select f_text from t_text where f_id = ' . $row[0]);
 if($destinationText === NULL){
  $db_translation->close();
  $db_destination->close();
  die('Error: 2');
 }
 echo '<tr><td>' . $destinationText . '</td><td>' . $row[1] . '</td><td>' . $row[2] . '</td></tr>' . "\n";
}
//  echo '<p>Not found!</p>';


$db_translation->close();
$db_destination->close();
?>
