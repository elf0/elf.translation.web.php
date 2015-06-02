<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
function InsertText($db_path_name, $text){
 try{
  $db = new SQLite3($db_path_name, SQLITE3_OPEN_READWRITE);
 }
 catch(Exception $e){
  return -1;
 }

 $id = $db->querySingle('Select f_id from t_text where f_text = "' . $text . '"');
 if($id !== NULL){
  $db->exec('Update t_text set f_good = f_good + 1 where f_id = ' . $id);
  $db->close();
  return $id;
 }

 $bResult = $db->exec('Insert into t_text(f_text) values("' . $text . '")');
 if($bResult)
  $id = $db->lastInsertRowID();
 else
  $id = -2;

 $db->close();
 return $id;
}

?>
