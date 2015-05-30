<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
if(!isset($_POST['s']) or !isset($_POST['d']) or !isset($_POST['st']) or !isset($_POST['dt']))
 die('<p>Error: Invalid request!</p>');

$sourceText = $_POST['st'];
$stn = strlen($sourceText);
if($stn > 255 or $stn == 0 or strpos($sourceText, '"', 0))
 die('<p>Error: Invalid request!</p>');

$destinationText = $_POST['dt'];
$dtn = strlen($destinationText);
if($dtn > 255 or $dtn == 0 or strpos($destinationText, '"', 0))
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

//die('<tr><td>' . $sourceLanguage . '(' . $sourceText . ')' . $destinationLanguage . '(' . $destinationText . ')'. '</td></tr>');
require 'AddText.php';

function AddTranslation($db_path, $table, $sourceLanguage, $sourceText, $destinationLanguage, $destinationText){
 //die('<tr><td>' . $sourceLanguage . '(' . $sourceText . ')' . $destinationLanguage . '(' . $destinationText . ')'. '</td></tr>');
 if($sourceLanguage == 'english')
  $sourceText = strtolower($sourceText);

 if($destinationLanguage == 'english')
  $destinationText = strtolower($destinationText);

 $sid = InsertText($db_path . $sourceLanguage . '.db', $sourceText);
 if($sid <= 0)
  return -$sid;

 $did = InsertText($db_path . $destinationLanguage . '.db', $destinationText);
 if($did <= 0)
  return -$did;

 try{
  $db = new SQLite3($db_path . 'translation.db', SQLITE3_OPEN_READWRITE);
 }
 catch(Exception $e){
  return 1;
 }

 $sql = 'Insert or ignore into ' . $table . '(f_' . $sourceLanguage . '_id, f_' . $destinationLanguage . '_id) values(' . $sid . ', ' . $did . ')';
 //die($sql);
 $bResult = $db->exec($sql);
 $db->close();
 return $bResult? 0 : 2;
}

require 'config.php';


$result = AddTranslation($db_path, $table, $sourceLanguage, $sourceText, $destinationLanguage, $destinationText);
if($result > 0)
 die('<p>Error: ' . $result . '</p>');
else
 die('<p>Finished!</p>');
?>
