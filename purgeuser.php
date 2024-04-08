<?php

/****************************************************/
/* Script permettant de purger les users en doublon */
/****************************************************/

include("connect.php");

$res = $db->query('SELECT  *, COUNT(login) AS nbr_doublon
FROM     tusers
GROUP BY login
HAVING   COUNT(login) > 1');

while ($data = $res->fetch(PDO::FETCH_ASSOC))
{
  if($data['login']!='' && $data['ldap_guid']!='')
  {
    echo $data['login']." - ".$data['firstname']." ".$data['lastname']." - ".$data['mail']."<br>";

    //on recupère les différents id du user
    $sql="SELECT id FROM tusers WHERE login='".$data['login']."'";
    $resp = $db->query($sql);

    //stock les ids des tickets
    $idtickets=Array();

    //on boucle dans les différents id d'un user
    while ($ids = $resp->fetch(PDO::FETCH_ASSOC))
    {
      //print_r($ids); echo "<br>";


      foreach ($ids as $key => $value) {
        $idtickets[$value]=Array('tech'=>[],'user'=>[]);
      }

      //print_r($idtickets);echo "<br>";

      //pour chaque ID on check si il a des tickets en tant que demandeur ou tech
      foreach ($ids as $key => $id) {
        //DEMANDEUR
        $sql="SELECT * FROM tincidents WHERE user='".$id."'";

        $ret = $db->query($sql);

        while ($ticket = $ret->fetch(PDO::FETCH_ASSOC))
        {
            $idtickets[$id]['user'][]=$ticket['id'];
            echo "<span style=''>DEMANDEUR - </span><span>".$ticket['id']." - USER : ".$ticket['user']." - ".$ticket['title']."</span><br>";
        }

        //ADMIN
        $sql="SELECT * FROM tincidents WHERE technician='".$id."'";

        $ret = $db->query($sql);

        while ($ticket = $ret->fetch(PDO::FETCH_ASSOC))
        {
            $idtickets[$id]['tech'][]=$ticket['id'];
            echo "<span style=''>TECHNICIEN - </span><span>".$ticket['id']." - TECH : ".$ticket['technician']." - ".$ticket['title']."</span><br>";
        }


        //si on a pas de ticket ni en tech ni en admin pour ce user on peut le supprimer. Il sera recréé à la prochaine synchro
        if(empty($idtickets[$id]['tech']) && empty($idtickets[$id]['user']))
        {
          $idtickets[$id]=Array();
        }


      }


    }

    //permet d'afficher les comptes avec des tickets sur plusieurs ID (/!\ a éxecuter avant pour vérifier qu'on oublie rien )
    $ctl=0;
    foreach($idtickets as $userid => $tickets){
      if(count($idtickets[$userid], COUNT_RECURSIVE)>2)
      {

        $ctl++;
        if($ctl>1){
          echo "<span style='color:orange'>COMPTE AVEC DES TICKETS POUR DEUX ID</span><br>";
        }
      }
    }



    //on boucle dans l'ensemble des tickets d'un user pour faire le trie
    $idtmp='';
    foreach ($idtickets as $userid => $tickets) {




      // Si un id n'a pas de ticket on supprime l'ID
      if(empty($idtickets[$userid]))
      {
        $sql="DELETE FROM tusers WHERE id='".$userid."'";
        echo "<span style='color:red'>$sql</span><br>";
        $ret = $db->query($sql);
      }

      //si l'id a des tickets mais qu'aucun des id n'avait de ticket pour l'instant
      //on garde l'id en mémoire et on ne change rien aux tickets
      else if($idtmp==''){
        $idtmp=$userid;
        echo "On ne touche pas à cette ID<br>";
      }
      //si on a déjà trouver des tickets pour un autre id du user
      //on doit changer l'id dans les tickets de l'id en cours
      else{

            //on repmlace ceux en tant que tech
            foreach ($idtickets[$userid]['tech'] as $key => $tick) {
              $sql="UPDATE tincidents SET technician='".$idtmp."' WHERE id='".$tick."'";
              echo "<span style='color:green'>$sql</span><br>";
              $ret = $db->query($sql);

              $sql="DELETE FROM tusers WHERE id='".$userid."'";
              echo "<span style='color:red'>$sql</span><br>";
              $ret = $db->query($sql);
            }

            //on remplace ceux en tant que user
            foreach ($idtickets[$userid]['user'] as $key => $tick) {
              $sql="UPDATE tincidents SET user='".$idtmp."' WHERE id='".$tick."'";
              echo "<span style='color:green'>$sql</span><br>";
              $ret = $db->query($sql);

              $sql="DELETE FROM tusers WHERE id='".$userid."'";
              echo "<span style='color:red'>$sql</span><br>";
              $ret = $db->query($sql);
            }

      }

      //on met a jour les users avec un ldap_guid foireux (ex:c46sdf45-sdfds654) (evite que le user soit recréé si son ldap_guid est foireux)
      $sql="UPDATE tusers SET ldap_guid='' WHERE ldap_guid REGEXP '[-]'";
      echo "<span style='color:green'>$sql</span><br>";
      $ret = $db->query($sql);

    }


  }

  else if($data['login']==''){
    echo "PAS DE LOGIN : ";
    echo "<span style='color:purple'>".$data['login']." - ".$data['firstname']." ".$data['lastname']." - ".$data['mail']."</span><br>";
  }
  else if($data['ldap_guid']==''){
    echo "PAS DE ldap_guid : ";
    echo "<span style='color:purple'>".$data['login']." - ".$data['firstname']." ".$data['lastname']." - ".$data['mail']."</span><br>";
  }

}

?>
