<?php
require_once('models/tool/parameters.php');
require_once('models/request/base_request.php');
require_once('models/request/purchase_order/purchase_order.php');
require_once('models/request/mission_order/mission_order.php');

use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\Request\MissionOrder\MissionOrder;
use Models\Request\BaseRequest;
use Models\Tool\Parameters;

$parameters = Parameters::getInstance();
if($parameters->isAlerteDemande())
{
    //on récupère la liste de toutes les demandes
    $requestlist = BaseRequest::getAllRequest();

    foreach($requestlist as $type=>$reqs)
    {
        foreach($reqs as $req)
        {
            //on vérifie l'écrat entre la date actuelle et la date de dernière update de la demande
            $date=date("Y-m-d H:i:s");
            $dateday=new DateTimeImmutable($date);
            $datereq=new DateTimeImmutable($req->getDateAlerte());
            $ddiff=$datereq->diff($dateday);
            $nbjour= $ddiff->format('%a');


            if($nbjour>=$parameters->getIntervalAlerteDemande())
            {
                //On indique la nouvelle date de la demande
                $req->setDateAlerte($date);
                $req->updateDateAlerte();
                $req->setCurrentUser($_SESSION['user_id']);
                //on envoie le mail de notif
                $req->load();//on récupère toutes les infos de la demande
                $req->sendNotificationRappel();//on envoie le mail de Rappel       
            }
        }    
    }
}




?>
