<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
if(!isset($_POST['l0']) or !isset($_POST['l1']) or !isset($_POST['t0']) or !isset($_POST['t1']))
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

$text0 = $_POST['t0'];
$t0n = strlen($text0);
if($t0n > 255 or $t0n == 0 or strpos($text0, '"', 0))
 die('<p>Error: 1</p>');

$text1 = $_POST['t1'];
$t1n = strlen($text1);
if($t1n > 255 or $t1n == 0 or strpos($text1, '"', 0))
 die('<p>Error: 1</p>');

$language0 = $languages[$l0];
$language1 = $languages[$l1];

if($l0 > $l1){
 $language_pair = $language1 . '_' . $language0;
}
else{
 $language_pair = $language0 . '_' . $language1;
}

require 'AddText.php';

function AddTranslation($db_path, $language_pair, $language0, $text0, $language1, $text1){
 //die('<tr><td>' . $language0 . '(' . $text0 . ')' . $language1 . '(' . $text1 . ')'. '</td></tr>');
 $text0 = strtolower($text0);

 $text1 = strtolower($text1);

 $id0 = InsertText($db_path . $language0 . '.db', $text0);
 if($id0 <= 0)
  return -$id0;

 $id1 = InsertText($db_path . $language1 . '.db', $text1);
 if($id1 <= 0)
  return -$id1;

 try{
  $db = new SQLite3($db_path . $language_pair . '.db', SQLITE3_OPEN_READWRITE);
 }
 catch(Exception $e){
  return 1;
 }

 $sql = 'Insert or ignore into t_translation(f_language0_id, f_language1_id) values(' . $id0 . ', ' . $id1 . ')';
 //die($sql);
 $bResult = $db->exec($sql);
 $db->close();
 return $bResult? 0 : 2;
}

require 'config.php';


$result = AddTranslation($db_path, $language_pair, $language0, $text0, $language1, $text1);
if($result > 0)
 die('<p>Error: ' . $result . '</p>');
else
 die('<p>Finished!</p>');
?>
