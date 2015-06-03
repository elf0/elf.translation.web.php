<?php
session_start();

if(!isset($_POST['l0']) or !isset($_POST['l0']) or !isset($_POST['id']) or !isset($_POST['bad']))
 die('Error: 1');

$l0 = $_POST['l0'];
$l1 = $_POST['l1'];
$id = $_POST['id'];
$bad = $_POST['bad'];

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

$vote_key = $language_pair . '_' . $id;
if(isset($_SESSION['vote']))
 if(isset($_SESSION['vote'][$vote_key]))
  die('0');
 else
  $_SESSION['vote'][$vote_key] = $bad;
else
 $_SESSION['vote'] = array($vote_key => $bad);

require 'config.php';

try{
 $db = new SQLite3($db_path . $language_pair. '.db', SQLITE3_OPEN_READWRITE);
}
catch(Exception $e){
 die('<p>Error: 2</p>');
}

$field = $bad != 0? 'f_bad' : 'f_good';
if($db->exec('Update t_translation set ' . $field . ' = ' . $field . ' + 1 where f_id = ' . $id))
 echo '1';
else
 echo '<p>Error: 3</p>';

$db->close();
?>
