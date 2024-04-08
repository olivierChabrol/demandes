<?php
/*************************************/
/* Permet de gerer l'upload des fichiers en amont de la validation du formulaire des demandes
/*************************************/
//print_r($_FILES);

$nbfiles=count($_FILES['files']['name']);
$uploadDir=$_POST['dir'];
$path=dirname(__FILE__)."/".$uploadDir."/";
$return=array();

if(!file_exists($path))
{
  mkdir($path, 0777, true);
}




for($i=0; $i<$nbfiles; $i++)
{

  if (!isset($_FILES['files']['size'][$i]) || $_FILES['files']['size'][$i] == 0) {
      continue;
  }
  //echo $i." fichier<br>";

  if(isset($_POST['id']))//cas d'un ticket direct
  {
    $real_filename=$_POST['id'].'_'.md5(uniqid()).'_'.preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['files']['name'][$i]);
  }
  else { //cas d'une demande à valider
    $real_filename=preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['files']['name'][$i]);
  }

    if(move_uploaded_file($_FILES['files']['tmp_name'][$i], $path.$real_filename)){

      $data[$i]['name']=$_FILES['files']['name'][$i];
      $data[$i]['realname']=$real_filename;
      //content check
      $file_content = file_get_contents($path.$_FILES['files']['name'][$i], true);
      if(preg_match('{\<\?php}',$file_content) || preg_match('/system\(/',$file_content)) {
        unlink($path.$_FILES['files']['name'][$i]); //remove file
        $data[$i]['msgtype']="error";
        $data[$i]['msg']="Fichier interdit";
      }
      else {
        $data[$i]['msgtype']="valid";
        $data[$i]['msg']="Fichier bien ajouté";
      }

    }




}

echo json_encode($data);


 ?>
