<?php
ignore_user_abort(true);
set_time_limit(0);

$export_date = date('Ymd');

require 'config.php';

$lockFilePathName = $db_path . 'export.lock';

$fLock = fopen($lockFilePathName, 'c+b');
if($fLock === false)
 die('Error: 1');

if(!flock($fLock, LOCK_EX)){
 fclose($fLock);
 die('Error: 2');
}

$last_export_date = fread($fLock, 8);

if($last_export_date !== false and strlen($last_export_date) == 8){
 $datetime0 = date_create_from_format('Ymd', $last_export_date);
 $datetime1 = date_create_from_format('Ymd', $export_date);

 $interval = date_diff($datetime0, $datetime1);
 if($interval->days < 7){
  flock($fLock, LOCK_UN);
  fclose($fLock);
  die('Wait '. (7 - $interval->days) . ' days, then export again!');
 }
}

fwrite($fLock, $export_date);
flock($fLock, LOCK_UN);
fclose($fLock);

echo 'Wait, please!<br /> Maybe a long time...<br />';

//sleep(60);

function ExportLanguage($db_path, $db_file_name, $export_path, $export_date){
 try{
  $db = new SQLite3($db_path . $db_file_name, SQLITE3_OPEN_READONLY);
 }
 catch(Exception $e){
  return 1;
 }

 $PathNamePrefix = $export_path . $db_file_name . '-' . $export_date . '-';

 $fOutput = bzopen($PathNamePrefix . '1.txt.bz2', 'w');
 if($fOutput === false){
  $db->close();
  return 2;
 }


 $rowCount = 0;
 $fileNumber = 1;
//Fixme: $result could be 'true', 'false' or 'SQLite3Result'
 $result = $db->query('Select f_id, f_text, f_good, f_bad from t_text order by f_good desc');
 while($row = $result->fetchArray(SQLITE3_NUM)){
  if(bzwrite($fOutput, $row[0] . '"' . $row[1] . '"' . $row[2] . '"' . $row[3] . "\n") === false){
    bzclose($fOutput);
    $db->close();
    return 3;
  }
 
  if(++$rowCount === 16777216){
   $rowCount = 0;
   bzclose($fOutput);
   $fOutput = bzopen($PathNamePrefix. ++$fileNumber . '.txt.bz2', 'w');
   if($fOutput === false){
    $db->close();
    return 2;
   }
  }
 }
 bzclose($fOutput);
 $db->close();
 echo $db_file_name . ' exported.<br />';
 return 0;
}

function ExportTranslation($db_path, $db_file_name, $export_path, $export_date){
 try{
  $db = new SQLite3($db_path . $db_file_name, SQLITE3_OPEN_READONLY);
 }
 catch(Exception $e){
  return 1;
 }

 $PathNamePrefix = $export_path . $db_file_name . '-' . $export_date . '-';

 $fOutput = bzopen($PathNamePrefix . '1.txt.bz2', 'w');
 if($fOutput === false){
  $db->close();
  return 2;
 }


 $rowCount = 0;
 $fileNumber = 1;
//Fixme: $result could be 'true', 'false' or 'SQLite3Result'
 $result = $db->query('Select f_language0_id, f_language1_id, f_good, f_bad from t_translation order by f_good desc');
 while($row = $result->fetchArray(SQLITE3_NUM)){
  if(bzwrite($fOutput, $row[0] . '"' . $row[1] . '"' . $row[2] . '"' . $row[3] . "\n") === false){
    bzclose($fOutput);
    $db->close();
    return 3;
  }
 
  if(++$rowCount === 16777216){
   $rowCount = 0;
   bzclose($fOutput);
   $fOutput = bzopen($PathNamePrefix. ++$fileNumber . '.txt.bz2', 'w');
   if($fOutput === false){
    $db->close();
    return 2;
   }
  }
 }
 bzclose($fOutput);
 $db->close();
 echo $db_file_name . ' exported.<br />';
 return 0;
}

function Export($db_path, $export_path, $languages, $export_date){
 $language_count = count($languages);
 $end = $language_count - 1;
 $begin = 0;
 while($begin != $end){
  $language0 = $languages[$begin];

  $result = ExportLanguage($db_path, $language0 . '.db', $export_path, $export_date);
  if($result != 0)
   return $result;

  for($i = $begin + 1; $i < $language_count; ++$i){
   $language1 = $languages[$i];
   $result = ExportTranslation($db_path, $language0 . '_' . $language1 . '.db', $export_path, $export_date);
   if($result != 0)
    return $result;
  }

  ++$begin;
 }

 return ExportLanguage($db_path, $languages[$end] . '.db', $export_path, $export_date);
}

require 'languages.php';

$result = Export($db_path, $export_path, $languages, $export_date);

if($result != 0)
 echo 'Error: ' . $result;
else
 echo 'Finished!';

//unlink($lockFilePathName);
?> 
