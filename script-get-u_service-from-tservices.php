<?php
/**************************************/
/* Crée par Alrick Dias // 15/07/2020 */
/**************************************/
/* Permet de mettre remettre les services dans les tickets (u_services) en fonction des services attribués aux users */
/* Utile lors du passage de l'ancienne version de la base à la nouvelle */
/* A éxecuter une fois les services des users créés */

//database connection
require_once(__DIR__."/connect.php");

//switch SQL MODE to allow empty values with lastest version of MySQL
$db->exec('SET sql_mode = ""');

//load parameters table
$query=$db->query("SELECT * FROM tparameters");
$rparameters=$query->fetch();
$query->closeCursor();

//on récupère les Tickets
$query=$db->query("SELECT * FROM tincidents");
$tickets=$query->fetchAll();
$query->closeCursor();

foreach ($tickets as $key => $ticket) {
  //on récupère le service du user
  if($ticket['user']!='')
  {
    $query=$db->query("SELECT tservices.id FROM tservices LEFT JOIN tusers_services ON tservices.id=tusers_services.service_id WHERE tusers_services.user_id=".$ticket['user']);
    $service=$query->fetch();
    $query->closeCursor();


    if($service['id']!='')
    {
      echo "On met a jout le ticket ".$ticket['id']." avec le service ".$service['id']."<br />";

      $query=$db->exec("UPDATE tincidents SET u_service=".$service['id']." WHERE tincidents.id=".$ticket['id']);

    }

  }

}


 ?>
