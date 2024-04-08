<?php
require_once('models/request/purchase_order/purchase_order.php');
require_once('models/request/purchase_order/code_nacre.php');
require_once('models/request/mission_order/mission_order.php');
require_once('models/request/mission_order/business_address.php');
require_once('models/request/mission_order/administrative_vehicle.php');
require_once('models/request/mission_order/transport_choice.php');
require_once('models/request/base_request.php');
require_once('models/request/ticket/ticket.php');
require_once('models/user/user_request.php');
require_once('models/request/common/file.php');
require_once('models/request/common/model.php');
require_once('models/user/service.php');
require_once('models/request/common/budget_data.php');
require_once('models/user/user.php');

use Models\Request\PurchaseOrder\PurchaseOrder;
use Models\Request\PurchaseOrder\CodeNacre;
use Models\Request\MissionOrder\MissionOrder;
use Models\Request\MissionOrder\BusinessAddress;
use Models\Request\MissionOrder\AdministrativeVehicle;
use Models\Request\MissionOrder\TransportChoice;
use Models\Request\BaseRequest;
use Models\Request\Ticket\Ticket;
use Models\Request\Common\File;
use Models\Request\Common\Model;
use Models\Request\Common\Service;
use Models\Request\Common\BudgetData;
use Models\User\User;
use Models\User\UserRequest;

/* Class */
$purchaseOrder = new PurchaseOrder();
$missionOrder = new MissionOrder();
$userRequest = new UserRequest();
$userOwner = new UserRequest();

/* Variables */
$budgetDatas = BudgetData::getCollection();
$users = User::getCollection();
$codeNacre = CodeNacre::getCollection();
$services = Service::getCollection();
$transportChoices = TransportChoice::getCollection();
$businessAddresses = BusinessAddress::getCollection();
$administrativeVehicles = AdministrativeVehicle::getCollection();
$models = Model::load($_SESSION['user_id']);
$modelsPurchaseOrder = $models[Model::TYPE_PURCHASE_ORDER];
$modelsMissionOrder = $models[Model::TYPE_MISSION_ORDER];
$purchaseOrderSelected = false;
$missionOrderSelected = false;
$ticketSelected = false;

/* Load */
/* Load User Request */
$userRequest
    ->setId($_SESSION['user_id'])
    ->load();

if (isset($_GET['type-form'])) {
    if ($_GET['type-form'] == 'purchase-order') {
        $purchaseOrderSelected = true;

        if ($_GET['id']) {
            /* Load Purchase Order by id */
            $purchaseOrder
                ->setId($_GET['id'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsPurchaseOrder)
                ->load();
        } else if ($_GET['model']) {
            /* Load Purchase Order by model */
            $purchaseOrder
                ->setId($_GET['model'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsPurchaseOrder)
                ->load()
                ->setIsModel(true);
        }
    } else if ($_GET['type-form'] == 'purchase-order-end') {
        $purchaseOrderSelected = true;

        if ($_GET['id']) {
            /* Load Purchase Order by id */
            $purchaseOrder
                ->setId($_GET['id'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsPurchaseOrder)
                ->load();
        } else if ($_GET['model']) {
            /* Load Purchase Order by model */
            $purchaseOrder
                ->setId($_GET['model'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsPurchaseOrder)
                ->load()
                ->setIsModel(true);
        }
    } else if ($_GET['type-form'] == 'mission-order') {
        $missionOrderSelected = true;

        if ($_GET['id']) {
            /* Load Mission Order by id */
            $missionOrder
                ->setId($_GET['id'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsMissionOrder)
                ->load();
        } else if ($_GET['model']) {
            /* Load Mission Order by model */
            $missionOrder
                ->setId($_GET['model'])
                ->setCurrentUser($_SESSION['user_id'])
                ->setModels($modelsMissionOrder)
                ->load()
                ->setIsModel(true);
        }
    } else if ($_GET['type-form'] == 'ticket') {
        $ticketSelected = true;
    }
}

/* File */
if ($_GET['action'] == 'delete-file' && $_GET['id-file'] && $_GET['type-file']) {
    /* Delete File */
    $file = null;

    switch ($_GET['type-file']) {
        case File::TYPE_RIB_AND_SUPPLEMENTARY_SHEET:
            $file = $missionOrder->getRibAndSupplementarySheetById($_GET['id-file']);
            if ($file) {
                $file->delete();
                $missionOrder->removeRibAndSupplementarySheet($file);
            }
            break;
        case File::TYPE_QUOTE:
            $files = $purchaseOrder->getQuote();
            if ($files) {
              foreach($files as $file)
              {
                $file->delete();
                $purchaseOrder->setQuote(null);
              }
            }
            break;
    }
}

/* Forms */
if (isset($_POST['type-form'])) {

    $request = null;

    if ($_POST['type-form'] == 'purchase-order') {
        /* Save Purchase Order */
	    $request = $purchaseOrder;
    } else if ($_POST['type-form'] == 'purchase-order-end') {
        /* Save Purchase Order */
            $request = $purchaseOrder;
    } else if ($_POST['type-form'] == 'mission-order') {
	if ($_POST['om-for-guest'] == 0) { 
	  if ($userRequest->checkForm($_POST)) {
            /* Save Mission Order */
	    $request = $missionOrder;
	  }
	}
	// if invitation dont check userForm
	else {
		$request = $missionOrder;
	}
    }

    if ($request && $request->checkForm($_POST, $_FILES)) {
        // new request if request has no id or request is a model
        $isNewRequest = (!$request->getId() || $request->getIsModel()) ;

        //Form is valid, we get datas
        $request->getDatas($_POST);

        if ($isNewRequest) {
            // new request, we set owner
            $request->setOwner($users[$_SESSION['user_id']]);
        }
        else {
          //on change le status initial de la demande en STATUS_MODIFY si on est en modif de demande
          $request->setStatusOld(BaseRequest::STATUS_MODIFY);
        }

        $ownerIsValidator = $request->hasValidator($request->getOwner()->getId());

        ($ownerIsValidator)
            ? $request->setStatus(BaseRequest::STATUS_VALID)
            : $request->setStatus(BaseRequest::STATUS_WAITING_VALIDATION, true);



        if ($request->isMissionOrder()) {
            $guardianShip = '';

            if ($request->getTypeMission() == MissionOrder::TYPE_MISSION_WITHOUT_FEES || $request->getTypeMission() == MissionOrder::TYPE_MISSION_STANDING_MISSION_ORDER) {
                // Set Guardianship for mission without fees or standing mission order
                $guardianShip = $request->getOwner()->getCustom1();
            }
            $request->setGuardianShip($guardianShip);

            // Saving User Request
            $userRequest
                ->getDatas($_POST)
                ->save();
        }

        $request->save();
        //TODO-supprimer la ligne ci-dessous qui est l'ancienne methode
        //File::upload($request, $_FILES);
        if(file_exists($_POST['uploaddir']))
        {
          File::moveUploadedFile($request,$_POST['uploaddir']);
        }

        if ($isNewRequest || $request->getStatusOld() == BaseRequest::STATUS_MODIFY) {
            // load all informations (like services etc...)
            $request
                ->setCurrentUser($_SESSION['user_id'])
                ->load();

            if ($ownerIsValidator && $request->getStatus() == BaseRequest::STATUS_VALID) {
                // owner is validator, we create ticket directly
                $ticket = new Ticket();
                $ticket->createByRequest($request);
            }

            $request->canNotification();
	}
	$request->save();

    }
}

/* Delete request */
if (isset($_GET['delete'])) {
    $request = null;

    if ($_GET['type-form'] == 'purchase-order') {
        /* Save Purchase Order */
        $request = $purchaseOrder;
    } else if ($_GET['type-form'] == 'mission-order' && $userRequest->checkForm($_POST)) {
        /* Save Mission Order */
        $request = $missionOrder;
    }

    if ($request && $request->canSave() && $request->isOwner($_SESSION['user_id'])) {
        $request
            ->setStatus(BaseRequest::STATUS_CANCEL)
            ->save();
    }
}

/* Model */
if (isset($_GET['save-model'])) {
    /* Save Model */
    $model = new Model();
    $model
        ->setMissionOrder($missionOrder)
        ->setPurchaseOrder($purchaseOrder)
        ->setIdUser($_SESSION['user_id']);

    if ($missionOrder->getId() > 0 && !$missionOrder->hasModel($missionOrder->getId())) {
        $model->save();
        $missionOrder->addModel($model);
    }

    if ($purchaseOrder->getId() > 0 && !$purchaseOrder->hasModel($purchaseOrder->getId())) {
        $model->save();
        $purchaseOrder->addModel($model);
    }
} else if (isset($_GET['delete-model'])) {
    /* Delete Model */
    $model = new Model();

    $model
        ->setId($_GET['delete-model'])
        ->setIdUser($_SESSION['user_id'])
        ->delete();

    if ($missionOrder->getId() > 0) {
        $missionOrder->removeModel($model);
    }

    if ($purchaseOrder->getId()) {
        $purchaseOrder->removeModel($model);
    }
}

/* View */
include("template/request/request.php");
