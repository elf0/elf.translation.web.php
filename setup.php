<?php
//License: Public Domain
//Author: elf
//EMail: elf198012@gmail.com
require 'config.php';
require 'languages.php';

function Database_CreateLanguage($path, $language){
 try{
  $db = new SQLite3($path . $language . '.db');
 }
 catch(Exception $e){
  return 1;
 }

 $result = 0;
 if(!$db->exec('Begin')){
  $result = 2;
  goto END;
 }

 if(!$db->exec('Create table if not exists t_text(f_id integer primary key, f_text text not null unique, f_good integer default 0, f_bad integer default 0)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_text_good on t_text(f_good)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_text_bad on t_text(f_bad)')){
  $result = 3;
  goto END;
 }
  
 if(!$db->exec('Commit')){
  $result = 2;
 }

END:
 $db->close();
 return $result;
}

function Database_CreateTranslation($path, $language0, $language1){
 try{
  $db = new SQLite3($path . $language0 . '_' . $language1 . '.db');
 }
 catch(Exception $e){
  return 1;
 }

 $result = 0;
 if(!$db->exec('Begin')){
  $result = 2;
  goto END;
 }

 if(!$db->exec('Create table if not exists t_translation(f_id integer primary key, f_language0_id integer not null, f_language1_id integer not null, f_good integer default 0, f_bad integer default 0, unique(f_language0_id, f_language1_id))')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_translation_language0_id on t_translation(f_language0_id)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_translation_language1_id on t_translation(f_language1_id)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_translation_good on t_translation(f_good)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Create index if not exists i_translation_bad on t_translation(f_bad)')){
  $result = 3;
  goto END;
 }

 if(!$db->exec('Commit'))
  $result = 2;
 
END:
 $db->close();
 return $result;
}

function CreateDatabases($path, $languages){
 $language_count = count($languages);
 $end = $language_count - 1;
 $begin = 0;
 while($begin != $end){
  $language0 = $languages[$begin];

  $result = Database_CreateLanguage($path, $language0);
  if($result != 0)
   return $result;

  for($i = $begin + 1; $i < $language_count; ++$i){
   $language1 = $languages[$i];
   $result = Database_CreateTranslation($path, $language0, $language1);
   if($result != 0)
    return $result;
  }

  ++$begin;
 }

 return Database_CreateLanguage($path, $languages[$end]);
}


$result = CreateDatabases($db_path, $languages);

if($result != 0)
 echo 'Error: ' . $result;
else
 echo 'Finished!';
?>
