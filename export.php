<?php
ignore_user_abort(true);
set_time_limit(0);

require 'config.php';

$lockFilePathName = $db_path . 'export.lock';
$lock = fopen($lockFilePathName, 'x');
if($lock === false)
  die('Export...');
fclose($lock);

echo 'Wait, please!<br /> Maybe a long time...<br />';

//sleep(60);

function ExportLanguage($db_path, $db_file_name, $export_path,$export_date){
 try{
  $db = new SQLite3($db_path . $db_file_name, SQLITE3_OPEN_READONLY);
 }
 catch(Exception $e){
  return 1;
 }
/*
$lastExportDate = $db->querySingle('Select f_value from t_key_value where f_key = "last_export_date"');
if($lastExportDate === false){
  $db->close();
  unlink(lockFile);
  die('Error: 1');
}



if($lastExportDate === null){
  if(!$db->exec('Insert into t_key_value(f_key, f_value) values("last_export_date", "' . $exportDate . '")')){
    $db->close();
    unlink(lockFile);
    die('2');
  }
}
else{
  echo 'Last export: ' . $lastExportDate . '<br />';
  $datetime0 = date_create_from_format('Ymd', $lastExportDate);
  $datetime1 = date_create_from_format('Ymd', $exportDate);

  $interval = date_diff($datetime0, $datetime1);
  if($interval->days < 7){
    $db->close();
    unlink(lockFile);
    die('Wait '. (7 - $interval->days) . ' days, then export again!');
  }

  if(!$db->exec('Update t_key_value set f_value = "' . $exportDate . '" where f_key = "last_export_date"')){
    $db->close();
    unlink(lockFile);
    die('2');
  }
}

$db->close();
*/

 $PathNamePrefix = $export_path . $db_file_name . '-' . $export_date . '-';

 $fOutput = bzopen($PathNamePrefix . '1.bz2', 'w');
 if($fOutput === false){
  $db->close();
  return 2;
 }


 $rowCount = 0;
 $fileNumber = 1;
//Fixme: $result could be 'true', 'false' or 'SQLite3Result'
 $result = $db->query('Select f_text, f_good, f_bad from t_text order by f_good desc');
 while($row = $result->fetchArray(SQLITE3_NUM)){
  if(bzwrite($fOutput, $row[0] . '"' . $row[1] . '"' . $row[2] . "\n") === false){
    bzclose($fOutput);
    $db->close();
    return 3;
  }
 
  if(++$rowCount === 16777216){
   $rowCount = 0;
   bzclose($fOutput);
   $fOutput = bzopen($PathNamePrefix. ++$fileNumber . '.bz2', 'w');
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

 $fOutput = bzopen($PathNamePrefix . '1.bz2', 'w');
 if($fOutput === false){
  $db->close();
  return 2;
 }


 $rowCount = 0;
 $fileNumber = 1;
//Fixme: $result could be 'true', 'false' or 'SQLite3Result'
 $result = $db->query('Select f_language0_id, f_language1_id, f_good, f_bad from t_translation order by f_good desc');
 while($row = $result->fetchArray(SQLITE3_NUM)){
  if(bzwrite($fOutput, $row[0] . ',' . $row[1] . ',' . $row[2] . ',' . $row[3] . "\n") === false){
    bzclose($fOutput);
    $db->close();
    return 3;
  }
 
  if(++$rowCount === 16777216){
   $rowCount = 0;
   bzclose($fOutput);
   $fOutput = bzopen($PathNamePrefix. ++$fileNumber . '.bz2', 'w');
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

function Export($db_path, $export_path, $languages){
 $export_date = date('Ymd');

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

$result = Export($db_path, $export_path, $languages);
unlink($lockFilePathName);

if($result != 0)
 echo 'Error: ' . $result;
else
 echo 'Finished!';

?> 
