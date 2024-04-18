<?php

require_once('models/request/ticket/ticket.php');
require_once('models/request/base_request.php');
require_once('models/user/user.php');
require_once('models/request/purchase_order/purchase_order.php');
require_once('models/request/mission_order/mission_order.php');

use Models\Request\BaseRequest;
use Models\Request\Ticket\Ticket;
use Models\User\User;
use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\Request\MissionOrder\MissionOrder;

$requestPage = isset($_GET['request-page']) ? $_GET['request-page'] : 1;
$users = User::getCollection();
$requestId = null;
$requestType = null;
$requestTitle = null;
$requestDateAlerte = null;
$requestDateDemande = null;
$requestDateDeparture = null;
$owner = null;
$validator = null;
$selectValidator = false;
$status = null;
$orderValue = null;
$orderDirection = null;

if ($_GET['action'] && $_GET['action-id'] && $_GET['action-type']) {
    // validate actions
    $requestToModify = null;
    $extraDatas = [];

    if ($_GET['action-type'] == 'purchase-order') {
        // request is a Purchase Order
        $requestToModify = new PurchaseOrder();
        $requestToModify
            ->setId($_GET['action-id'])
            ->setCurrentUser($_SESSION['user_id'])
	    ->load();
    } else if ($_GET['action-type'] == 'purchase-order-end') {
        // request is a Purchase Order
        $requestToModify = new PurchaseOrder();
        $requestToModify
            ->setId($_GET['action-id'])
            ->setCurrentUser($_SESSION['user_id'])
            ->load();
    } else if ($_GET['action-type'] == 'mission-order') {
        // request is a Mission Order
        $requestToModify = new MissionOrder();
        $requestToModify
            ->setId($_GET['action-id'])
            ->setCurrentUser($_SESSION['user_id'])
            ->load();
    }

    if (isset($_POST['action-comment'])) {
        $extraDatas['comment'] = $_POST['action-comment'];
    }
    if ($requestToModify && $requestToModify->canValidate($_SESSION['user_id'])) {
        try {
            $msgSuccess = '';
            // request loaded and user have right to validate it
            switch($_GET['action']) {
                case 'reject':
                    $requestToModify->setStatus(BaseRequest::STATUS_REJECT);
                    $msgSuccess = T_('La demande a bien été refusée');
                    break;
                case 'modify':
                    if (!$extraDatas['comment']) {
                        throw new Exception(T_('Vous devez saisir un commentaire'));
                    }
                    $requestToModify->setStatus(BaseRequest::STATUS_MODIFY);
                    $msgSuccess = T_('Une demande de modification a bien été envoyée');
                    break;
                case 'valid':
                    $requestToModify->setStatus(BaseRequest::STATUS_VALID);
                    $msgSuccess = T_('La demande a bien été validée');

                    // creation of the ticket
                    $ticket = new Ticket();
                    $ticket->createByRequest($requestToModify);

                    break;
            }
            $requestToModify
                ->update()
                ->canNotification($extraDatas);
            echo DisplayMessage('success', $msgSuccess);
        } catch (Exception $e) {
            echo DisplayMessage('error', $e->getMessage());
        }
    } else {
        echo DisplayMessage('error', T_('Cette demande a déjà été traitée'));
    }
}


// Init parameters for loading requests
if (isset($_GET['select-validator'])) {
    $selectValidator = ($_GET['select-validator'] == 1) ? true : false;
    if ($selectValidator) {
        $validator = $_SESSION['user_id'];
    }
    $_GET['filter-validator'] = null;
}

if (isset($_POST['filter-id'])) {
    $requestId = $_POST['filter-id'];
    $requestPage = 1;
} else if (isset($_GET['filter-id'])) {
    $requestId = $_GET['filter-id'];
}

if (isset($_POST['filter-type'])) {
    $requestType = $_POST['filter-type'];
    $requestPage = 1;
} else if (isset($_GET['filter-type'])) {
    $requestType = $_GET['filter-type'];
}

if (isset($_POST['filter-title'])) {
    $requestTitle = $_POST['filter-title'];
    $requestPage = 1;
} else if (isset($_GET['filter-title'])) {
    $requestTitle = $_GET['filter-title'];
}

if (isset($_POST['filter-datealerte'])) {
    $requestDateAlerte = $_POST['filter-datealerte'];
    $requestPage = 1;
} else if (isset($_GET['filter-datealerte'])) {
    $requestDateAlerte = $_GET['filter-datealerte'];
}
/*
if (isset($_POST['filter-datedemande'])) {
    $requestDateDemande = $_POST['filter-datedemande'];
    $requestPage = 1;
} else if (isset($_GET['filter-datedemande'])) {
    $requestDateDemande = $_GET['filter-datedemande'];
}
//*/

if (isset($_POST['filter-datestart'])) {
    $requestDateDemande = $_POST['filter-datestart'];
    $requestPage = 1;
} else if (isset($_GET['filter-datestart'])) {
    $requestDateDemande = $_GET['filter-datestart'];
}

if (isset($_POST['filter-owner'])) {
    $owner = $_POST['filter-owner'];
    $requestPage = 1;
} else if (isset($_GET['filter-owner'])) {
    $owner = $_GET['filter-owner'];
}

if (isset($_POST['filter-validator'])) {
    $validator = $_POST['filter-validator'];
    $requestPage = 1;
    $selectValidator = ($validator == $_SESSION['user_id']);
    $_GET['select-validator'] = null;
} else if (isset($_GET['filter-validator'])) {
    $validator = $_GET['filter-validator'];
    $selectValidator = ($validator == $_SESSION['user_id']);
}

if (isset($_POST['filter-status'])) {
    $status = $_POST['filter-status'];
    $requestPage = 1;
} else if (isset($_GET['filter-status'])) {
    $status = $_GET['filter-status'];
}

if (isset($_POST['order-value'])) {
    $orderValue = $_POST['order-value'];
} else if (isset($_GET['order-value'])) {
    $orderValue = $_GET['order-value'];
}

if (isset($_POST['order-direction'])) {
    $orderDirection = $_POST['order-direction'];
} else if (isset($_GET['order-direction'])) {
    $orderDirection = $_GET['order-direction'];
}

$datas = [
    "order-value" => $orderValue,
    "order-direction" => $orderDirection,
    "filter-id" => $requestId,
    "filter-type" => $requestType,
    "filter-title" => $requestTitle,
    "filter-datealerte" => $requestDateAlerte,
    //"filter-datedemande" => $requestDateDemande,
    "filter-datedeparture" => $requestDateDeparture,
    "filter-owner" => $owner,
    "filter-validator" => $validator,
    "filter-status" => $status,
    "request-page" => $requestPage
];
$requests = BaseRequest::getCollection($datas, $requestPage);

// construct url with parameters for redirection
$baseUrl = '/index.php?page=request_list';
$urlParameters = '&'.http_build_query($datas);
$urlAction = $baseUrl.$urlParameters;
$orderDirectionNext = ($orderDirection == 'DESC') ? 'ASC' : 'DESC';

/* View */
include("template/request/list/request_list.php");
