<?php
/*************************************/
/* Permet de gerer la suppression des fichiers en amont de la validation du formulaire des demandes
/*************************************/
$uploadDir=$_POST['dir'];

$path=$uploadDir."/";

if(unlink($path.$_POST['file'])){//remove file
    $data[0]['msgtype']='valid';
    $data[0]['msg']='Fichier supprimé';
  echo json_encode($data);
}
else{
  echo json_encode(array('msgtype'=>'error','msg'=>'Fichier non supprimé'));
}





 ?>
