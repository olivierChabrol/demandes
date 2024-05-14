<?php
use Models\Request\MissionOrder\MissionOrder;
use Models\Request\BaseRequest;

$urlAction .= '&type-form=mission-order';
$urlBase = $urlAction;

$invitation = 0;
if (isset($_GET['invite']) || $missionOrder->isOmForGuest()) {
  $invitation = 1;
}

if ($missionOrder->getIsModel()) {
    // Build url for model
    $urlAction .= '&model=' . $missionOrder->getId();
}
$request = $missionOrder;
if ($missionOrder->getId() && !$missionOrder->getIsModel()) {
    // Build url for updating a mission order
    $urlAction .= '&id=' . $missionOrder->getId();
}
$stateDisabled = [BaseRequest::STATUS_VALID, BaseRequest::STATUS_REJECT, BaseRequest::STATUS_CANCEL];
$disabled = false;

if (!$missionOrder->getIsModel() && in_array($missionOrder->getStatus(), $stateDisabled)) {
    // the mission order is not a model and has reached its final state (valid/reject/cancel), we disable the form
    $disabled = true;
}

if ($missionOrder->getOwner()->getId() && $missionOrder->getOwner()->getId() != $userRequest->getId()) {
    // the user is not the owner of the mission order so we need to load user owner for printing his services
    $userOwner
        ->setId($missionOrder->getOwner()->getId())
        ->load();
} else {
    // the user is the owner of the mission order
    $userOwner = $userRequest;
}
?>

<div class="card bcard shadow mt-2 mission-order-group" id="mission-order-group" draggable="false">
    <form class="form-horizontal" id="mission-order-form" enctype="multipart/form-data" method="post"
          action="<?php echo $urlAction ?>">
        <div class="card-header">
            <h5 class="card-title">
	    <i class="fa fa-ticket-alt"></i>
                <?php
                  if(!$missionOrder->isOmForGuest()) {
                ?>
                <?php echo T_('Ordre de mission') ?>
                <?php
                  }
                  else
	          {
?>
<?php echo T_('Invitation') ?>
<?php
	          }
                if ($missionOrder->getId() || $missionOrder->getIsModel()) {
                    if ($missionOrder->getIsModel()) {
                        echo '- '.T_('Modèle').' ';
                    }
                    echo ' n° '.$missionOrder->getId().' : '.$missionOrder->getTitle();
                }
                ?>
            </h5>
            <?php
            if ((!$missionOrder->getId() || $missionOrder->getIsModel()) && count($modelsMissionOrder)) {
            ?>
                <div>
                    <h5 class="card-title">
                        <select id="model-list-mo" class="select2" name="model-list-mo">
                            <option value=""><?php echo T_('Utiliser un modèle') ?></option>
                            <?php
                            foreach ($modelsMissionOrder as $model) {
                            ?>
                                <option value="<?php echo $model->getMissionOrder()->getId() ?>"><?php echo $model->getMissionOrder()->getTitle() ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </h5>
                </div>
            <?php
            }

            $currentModel = $missionOrder->hasModel($missionOrder->getId());

            if ($missionOrder->getId() && !$missionOrder->getIsModel() && !$currentModel) {
            ?>
            <div>
                <h5 class="card-title">
                    <a href="<?php echo $urlAction ?>&save-model=1">
                        <i class="fa fa-save"></i>
                        <?php echo T_('Sauvegarder pour ré-utilisation ultérieure') ?>
                    </a>
                </h5>
            </div>
            <?php
            }

            if ($missionOrder->getId() && !$missionOrder->getIsModel() && $currentModel) {
            ?>
            <div>
                <h5 class="card-title">
                    <a href="<?php echo $urlAction ?>&delete-model=<?php echo $currentModel->getId() ?>">
                        <i class="fa fa-times"></i>
                        <?php echo T_('Supprimer ce modèle') ?>
                    </a>
                </h5>
            </div>
            <?php
            }
            ?>
        </div>
        <div class="card-body p-0">
            <div class="p-3">
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="title" title="Préciser la thématique de votre mission : conférence, groupe de travail, etc.">
                            <?php
                            echo '<i id="user_warning" title="'.T_('Le champ Titre doit être renseigné.').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                            echo T_('Intitulé détaillé de la demande').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input id="title" type="text" name="title" value="<?php echo $missionOrder->getTitle() ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                    </div>
		</div>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="reason-for-mission">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Motif de la mission doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Motif de la mission'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if ($mobile == 0) {
                            echo '780';
                        } else {
                            echo '285';
                        } ?>" style="border: 1px solid #D8D8D8;">
                            <tr>
				<td style="padding:5px">

                                    <div id="reason-for-mission-editor"
                                         class="bootstrap-wysiwyg-editor pl-2 pt-1 editor"
                                         style="min-height:100px; max-width:775px">
				    </div>

<!-- <div id="reason-for-mission-editor" contenteditable style="min-height:100px; max-width:775px"> -->
                                    </div>
                                    <input id="reason-for-mission" type="hidden" name="reason-for-mission"
                                        value="<?php echo $missionOrder->getReasonForMission() ?>"/>
                                </td>
                            </tr>
                        </table>
                    </div>
		</div>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
			<label class="mb-0" for="title">
                            <?php
                            echo T_('Type de mission').' :';
                            ?>
                        </label>
                    </div>
		    <div class="col-sm-6">
                        <select id="mission_type" name="mission_type" class="form-control chosen-select">
			<option value="0" <?php echo ($missionOrder->getMissionType() == 0) ? "selected" : "" ?> >Aucune</option>
                          <option value="1" <?php echo ($missionOrder->getMissionType() == 1) ? "selected" : "" ?> >Colloque</option>
			  <option value="2" <?php echo ($missionOrder->getMissionType() == 2) ? "selected" : "" ?> >Recherche en équipe / Collaboration</option>
			  <option value="3" <?php echo ($missionOrder->getMissionType() == 3) ? "selected" : "" ?> >Mission</option>
			  <option value="4" <?php echo ($missionOrder->getMissionType() == 4) ? "selected" : "" ?> >Service central</option>
			  <option value="5" <?php echo ($missionOrder->getMissionType() == 5) ? "selected" : "" ?> >Visite contact pour projet</option>
			  <option value="6" <?php echo ($missionOrder->getMissionType() == 6) ? "selected" : "" ?> >Acquisition de nouvelles compétences</option>
			  <option value="7" <?php echo ($missionOrder->getMissionType() == 7) ? "selected" : "" ?> >Administration de la recherche</option>
			  <option value="8" <?php echo ($missionOrder->getMissionType() == 8) ? "selected" : "" ?> >Enseignement dispensé</option>
			  <option value="9" <?php echo ($missionOrder->getMissionType() == 9) ? "selected" : "" ?> >Formation</option>
			  <option value="10" <?php echo ($missionOrder->getMissionType() == 10) ? "selected" : "" ?> >Recherche documentaire sur le terrain</option>
			  <option value="11" <?php echo ($missionOrder->getMissionType() == 11) ? "selected" : "" ?> >Jury de thèse</option>
	              </select>
                    </div>
                </div>

                <?php
                if ($missionOrder->getId() && !$missionOrder->getIsModel()) {
                    ?>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="owner">
                                <?php
                                echo T_('Demandeur').' :';
                                ?>
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input id="owner" type="text" name="owner" value="<?php echo $missionOrder->getOwner()->getFullName() ?>" disabled />
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                //on récupère les equipes pour le select qui suit
                $userServices = $userOwner->getServices();
                $userServicesLength = count($userServices);
                $first = true;
                $hideteam='';

                //si on a zero equipe alors on affiche toutes les equipes
                if ($userServicesLength == 0) {
                    $userServices = $services;
                }
                //si on a qu'une equipe on cache le champs
                if ($userServicesLength == 1) {
                  $hideteam='hidden';
                }

                ?>

                <div class="form-group row" <?php echo $hideteam; ?>>
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="service">
                            <?php
                            echo '<i id="user_warning" title="'.T_('Le champ Service doit être renseigné.').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                            echo T_('Votre equipe/service').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select select2" id="service" name="service" <?php echo ($disabled) ? 'disabled' : '' ?> >
                            <?php

                            foreach($userServices as $service) {
                                if ($first && $userServicesLength > 0 || ($missionOrder->getService()->getId() == $service->getId())) {
                                    $isSelected = true;
                                    $first = false;
                                } else {
                                    $isSelected = false;
                                }
                                ?>
                                <option value="<?php echo $service->getId() ?>" <?php if ($isSelected) { echo 'selected'; } ?>><?php echo $service->getName() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Première demande de mission'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="first-mission-request" name="first-mission-request" type="checkbox"
                               value="1" <?php if ($missionOrder->isFirstMissionRequest()) {
                            echo 'checked';
                        } ?> <?php echo ($disabled) ? 'disabled' : '' ?> >
                    </div>
		</div>
                <input id="om-for-guest" name="om-for-guest" type="hidden"
		           <?php if ($missionOrder->isOmForGuest() || $invitation == 1) {
                            echo ' value="1" ';
                            echo 'checked="1"';
                        } ?> <?php //echo ($disabled) ? 'disabled' : '' ?> >
<!--
		<div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('OM pour un invité'); ?> :
                        </label>
		    </div>

                    <div class="col-sm-9 mt-2">
                        <input id="om-for-guest" name="om-for-guest" type="hidden"
                               value="1" <?php if ($missionOrder->isOmForGuest() || $invitation == 1) {
                            echo 'checked="1"';
                        } ?> <?php echo ($disabled) ? 'disabled' : '' ?> >
                    </div>
		</div>
-->

                <div id="guest-group" class="d-none">
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest">
                                <?php echo T_('Prénom et Nom de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <input id="guest-name" type="text" name="guest-name" value="<?php echo $missionOrder->getGuestName() ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest-birthdate">
                                <?php echo T_('Date de naissance de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <input id="guest-birthdate" type="date" name="guest-birthdate" value="<?php if ($missionOrder->getGuestBirthDate()) echo $missionOrder->getGuestBirthDate()->format('Y-m-d') ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest-phonenumber">
                                <?php echo T_('Numéro de téléphone de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <input id="guest-phonenumber" type="tel" name="guest-phonenumber" value="<?php echo $missionOrder->getGuestPhoneNumber() ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest-mail">
                                <?php echo '<i id="user_warning" title="' . T_('Le mail de l\'invité doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Mail de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <input id="guest-mail" type="text" name="guest-mail" required value="<?php echo $missionOrder->getGuestMail() ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest">
                                <?php echo T_('Nom et adresse du laboratoire de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <textarea type="text" class="form-control mt-2 col-12" id="guest-labo" name="guest-labo" value="" <?php echo ($disabled) ? 'disabled' : '' ?>/><?php echo $missionOrder->getGuestLabo() ?></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="guest">
                                <?php echo T_('Pays du laboratoire de l\'invité'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9 mt-2">
                            <input id="guest-country" type="text" name="guest-country" value="<?php echo $missionOrder->getGuestCountry() ?>" <?php echo ($disabled) ? 'disabled' : '' ?> />
                        </div>
                    </div>
                    
                </div>
                
<!--
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="collective-mission">
                            <?php echo T_('Mission collective'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="collective-mission" name="collective-mission" type="checkbox"
                               value="1" <?php if ($missionOrder->isCollectiveMission()) {
                            echo 'checked';
                        } ?> <?php echo ($disabled) ? 'disabled' : '' ?> >
                    </div>
                </div>

                <div id="collective-mission-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="list-people-involved-assignment">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Liste des personnes concernées pour la mission doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Liste des personnes concernées pour la mission'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                            <tr>
                                <td style="padding:5px">
                                    <div id="list-people-involved-assignment-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="list-people-involved-assignment" type="hidden" name="list-people-involved-assignment"
                                           value="<?php echo $missionOrder->getListPeopleInvolvedAssignment() ?>"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
		</div>
-->

                <div class="p-3">
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="user">
                                <?php echo T_('Pièces justificatives (Programme de l’événement / RIB / Formulaire de création agent/AAE, etc)'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <table border="1" style="border:1px solid #D8D8D8; min-width:265px;">
                                <tbody>
                                <tr>
                                    <td style="padding:15px;">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                                        <input style="color:transparent; width:160px;" id="rib-and-supplementary-sheet" class="file-to-upload ajax"
                                               type="file" name="rib-and-supplementary-sheet[]" multiple <?php echo ($disabled) ? 'disabled' : '' ?> > &nbsp;
                                        <!--<button class="btn btn-sm btn-success"
                                                title="Enregistrer l'ordre de mission et charger le fichier"
                                                onclick="check_size();" name="upload" value="upload" type="submit"
                                                id="upload-mo" <?php //echo ($disabled) ? 'disabled' : '' ?>
                                        >
                                            <i class="fa fa-upload"></i>
                                        </button>-->
                                        <div class="files-to-upload-group"></div>
                                        <?php
                                        //utile pour le multifile ajax
                                        $missionOrder->setUploadDir();
                                        $uploadDir="upload/tmp/".$missionOrder->getUploadDir();
                                        ?>
                                        <input type="hidden" id="uploaddir" name="uploaddir" value="<?php echo $uploadDir ?>">
                                        <?php
                                        $upload_max_size_error=T_('Le fichier est joint est trop volumineux, la taille maximum est ').ini_get('post_max_size');
                                      	$upload_max_size=(preg_replace('/[^0-9.]+/', '', ini_get('post_max_size')))*1024*1024;
                                      	if($_GET['action']!='adduser' && $_GET['action']!='edituser'  && $_GET['action']!='addcat' && $_GET['action']!='editcat' && $_GET['action']!='template' && ini_get('post_max_size'))
                                      	{
                                      		echo "
                                      		<script>
                                            var uploadDir= '$uploadDir';

                                            function check_size() {
                                      				$('form').submit(function( e ) {
                                      					if(!($('#file')[0].files[0].size < $upload_max_size )) {
                                      						//Prevent default and display error
                                      						alert('$upload_max_size_error');
                                      						e.preventDefault();
                                      					}
                                      				});
                                      			}
                                      		</script>
                                      		";
                                      	}
                                        ?>
                                        <div class="files-uploaded">
                                            <?php
                                            foreach ($missionOrder->getRibAndSupplementarySheet() as $file) {
                                                ?>
                                                <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>" style="text-decoration:none"><i style="vertical-align: middle;" class="fa fa-file text-info"></i>&nbsp;</a>
                                                <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>">
                                                    <?php echo $file->getName() ?>
                                                </a>
                                                <?php
                                                if (!$disabled) {
                                                ?>
                                                <a title="Supprimer ce fichier" onclick="javascript: return confirm('Êtes-vous sur de vouloir supprimer ce fichier ?');" href="./index.php?page=request&id=<?php echo $missionOrder->getId() ?>&type-form=mission-order&action=delete-file&id-file=<?php echo $file->getId() ?>&type-file=<?php echo $file->getType() ?>"> <i class="fa fa-trash text-danger"></i></a>
                                                <?php
                                                }
                                                ?>
                                                <br/>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="type-mission">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Type de mission doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Type de mission'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 pt-2">
                        <input type="radio" id="with-fees" name="type-mission"
                           value="<?php echo MissionOrder::TYPE_MISSION_WITH_FEES; ?>"
                            <?php if ($missionOrder->getTypeMission() == MissionOrder::TYPE_MISSION_WITH_FEES) {
                                echo 'checked';
                            } ?>
                        >
                        <label for="with-fees"><?php echo T_('Avec frais'); ?></label>

                        <input type="radio" id="without-fees" name="type-mission"
                               value="<?php echo MissionOrder::TYPE_MISSION_WITHOUT_FEES ?>"
                                <?php if ($missionOrder->getTypeMission() == MissionOrder::TYPE_MISSION_WITHOUT_FEES) {
                                    echo 'checked';
                                } ?>
                        >
                        <label for="without-fees"><?php echo T_('Sans frais'); ?></label>
<!--
                        <input type="radio" id="standing-mission-order" name="type-mission"
                               value="<?php echo MissionOrder::TYPE_MISSION_STANDING_MISSION_ORDER ?>"
                                <?php if ($missionOrder->getTypeMission() == MissionOrder::TYPE_MISSION_STANDING_MISSION_ORDER) {
                                    echo 'checked';
                                } ?>
                        >
			<label for="standing-mission-order"><?php echo T_('Ordre de mission permanent'); ?></label>
-->
                    </div>
                </div>

                <div id="with-fees-group" class="d-none">
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="budget-data">
                                <?php echo '<i id="user_warning" title="' . T_('Le champ Données budgétaires doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Données budgétaires'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <select autofocus style="display:inline; <?php if ($mobile) {
                                echo 'max-width:240px;';
                            } else {
                                if ($rright['ticket_user_company']) {
                                    echo 'width:auto;';
                                } else {
                                    echo 'max-width:269px;';
                                }
                            } ?>" class="form-control chosen-select select2" id="budget-data" name="budget-data" <?php echo ($disabled) ? 'disabled' : '' ?>>
                                <option value=""></option>
                                <?php
                                foreach ($budgetDatas as $budgetData) {
                                    if ($budgetData->getCategory() != \Models\Request\Common\BudgetData::CATEGORY_MISSION_ORDER) {
                                        continue;
                                    }

                                    $isSelected = $missionOrder->getBudgetData()->getId() == $budgetData->getId();
                                    ?>
                                    <option value="<?php echo $budgetData->getId() ?>" <?php if ($isSelected) {
                                        echo 'selected';
                                    } ?>><?php echo $budgetData->getName() ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="additional-budget-information">
                                <?php
                                if(strpos($_SERVER['HTTP_HOST'], 'imbe.fr') !== false){
                                  echo '<i id="user_warning" title="'.T_('Le champs informations de budget supplémentaires est requis').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                                }
                                echo T_('Informations de budget supplémentaires')."<br>".T_('(ligne budgétaire, informations particulières, ...)');
                                ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                                <tr>
                                    <td style="padding:5px">
                                        <div id="additional-budget-information-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                        </div>
                                        <input id="additional-budget-information" type="hidden" name="additional-budget-information"
                                               value="<?php echo $missionOrder->getAdditionalBudgetInformation() ?>"
                                        />
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="without-fees-group" class="d-none">
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="care-organization">
                                <?php echo '<i id="user_warning" title="' . T_('L\'organisme de prise en charge doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Organisme de prise en charge'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-5">
                            <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="care-organization" name="care-organization" value="<?php echo $missionOrder->getCareOrganization() ?>"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                            />
                        </div>
                    </div>
                </div>

                <div id="resp-cred" class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="validators">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Responsable des crédits doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Responsable des crédits'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                            <?php
                            //on passe par un fichier externe pour les valideurs. De cette manière si un labo veut faire un dev différent sur cette partie on exclue le fichier validator.php du git pull
                            require_once("validator.php");
                            ?>
                    </div>
                </div>
<!--
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="reason-for-mission">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Motif de la mission doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Motif de la mission'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if ($mobile == 0) {
                            echo '780';
                        } else {
                            echo '285';
                        } ?>" style="border: 1px solid #D8D8D8;">
                            <tr>
                                <td style="padding:5px">
                                    <div id="reason-for-mission-editor"
                                         class="bootstrap-wysiwyg-editor pl-2 pt-1 editor"
                                         style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="reason-for-mission" type="hidden" name="reason-for-mission"
                                        value="<?php echo $missionOrder->getReasonForMission() ?>"/>
                                </td>
                            </tr>
                        </table>
                    </div>
		</div>
-->

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('La date et l\'heure de départ doivent être renseignées') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Date et heure de départ'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control <?php if ($mobile) {
                            echo 'col-7';
                        } else {
                            echo 'col-5';
                        } ?> datetimepicker-input" id="date-start" data-toggle="datetimepicker"
                           data-target="#date-start" name="date-start" autocomplete="off"
                           value="<?php
                           //on affiche pas de date si c'est une nouvelle demande
                           //pour une nouvelle demande la date est initialisé à la date courante donc on doit la tester pour la virer
                              $datestart=$missionOrder->getDateStart()->format('d/m/Y H');
                              $dateact=date('d/m/Y H');
                              if($datestart==$dateact)
                              {
                                echo '';
                              }
                              else{
                                echo $missionOrder->getDateStart()->format('d/m/Y H:i:s');
                              }
                           ?>"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        />
                    </div>
                </div>
<?php 
      if ($invitation == 0) {
?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('Le lieu de départ doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Lieu de départ'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        $businessAddressSelected = null;
			$personalAddressSelected = $userOwner->isPersonalAddress($missionOrder->getPlaceStart());
                        $personalAddress = $userRequest->getPersonalAddress(); 
			if ($userRequest->getId() != $userRequest->getPersonalAddress()) {
				$personalAddressSelected = $userOwner->getPersonalAddress() == $missionOrder->getPlaceStart();
				$personalAddress = $userOwner->getPersonalAddress();
			}
//			echo "<br> moi ID : ".$missionOrder->getOwner()->getId();
//echo "<br> userRequest ID : ".$userRequest->getId();
//echo "<br> Adresse utilisateur identifie : ".$userRequest->getPersonalAddress();
//echo "<br> owner getPersonalAddress : ".$userOwner->getPersonalAddress();
//echo "<br> Mission place start : ".$missionOrder->getPlaceStart();
//echo "<br> equality : '".($userOwner->getPersonalAddress() == $missionOrder->getPlaceStart())."'";
                        foreach ($businessAddresses as $businessAddress) {
                            if ($businessAddress->isAddress($missionOrder->getPlaceStart())) {
                                $businessAddressSelected = $businessAddress->getId();
                                break;
                            }
                        }

                        $freeAddress = (!$businessAddressSelected && !$personalAddressSelected);
                        ?>
                        <select autofocus class="form-control chosen-select" id="place-start" name="place-start" <?php echo ($disabled) ? 'disabled' : '' ?>>
                            <option value="<?php if ($freeAddress) { echo 'free_address'; } ?>"><?php if ($freeAddress) { echo $missionOrder->getPlaceStart(); } ?></option>
                            <option data-address="<?php echo $personalAddress; ?>" value="personal_address" <?php if ($personalAddressSelected || (!$missionOrder->getPlaceStart() && $userRequest->getPersonalAddressDefault())) {
                                echo 'selected';
                            } ?>><?php echo T_('Adresse personnelle'); ?></option>
                            <?php
                            foreach ($businessAddresses as $businessAddress) {
                                $isSelected = (($businessAddress->getId() == $businessAddressSelected) || (!$missionOrder->getPlaceStart() && $userRequest->getBusinessAddressDefault() == $businessAddress->getId()));
                                ?>
                                <option data-address="<?php echo $businessAddress->getAddress() ?>" value="<?php echo $businessAddress->getId() ?>" <?php if ($isSelected) {
                                    echo 'selected';
                                } ?>><?php echo $businessAddress->getTitle() ?></option>
                                <?php
			    }
			
                            ?>
                        </select>
                        <textarea type="text" class="form-control mt-2 col-12 d-none" id="place-start-input"
                                  name="place-start-input" value="" <?php echo ($disabled) ? 'disabled' : '' ?>/></textarea>
                        <div id="place-start-address-save-group" class="mt-2 d-none">
                            <?php echo T_('Sauvegarder pour une ré-utilisation ultérieure'); ?>:&nbsp;
                            <input type="checkbox" class="mt-1" id="place-start-address-save"
                                name="place-start-address-save" value="1"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                            />
                        </div>
                        <div id="place-start-address-default-group" class="mt-2 d-none">
                            <?php echo T_('Adresse par défaut'); ?>:&nbsp;
                            <input type="checkbox" class="mt-1" id="place-start-address-default"
                                name="place-start-address-default" value="1"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                            />
                        </div>
                    </div>
                    <?php
                    if (!$disabled) {
                    ?>
                    <div class="col-sm-1 mt-2 pl-0">
                        <i id="place-start-free-address" class="fa fa-plus-circle text-success text-150 pl-1"
                           title="<?php echo T_('Utiliser une adresse non listée'); ?>"></i>
                    </div>
                    <?php
                    }
                    ?>
                </div>
<?php
  } // first part of the non-invitation part
?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('La date et l\'heure de retour doivent être renseignées') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Date et heure de retour'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="hidden" name="hide" id="hide-date-return" value="1" <?php echo ($disabled) ? 'disabled' : '' ?>/>
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?> datetimepicker-input" id="date-return" data-toggle="datetimepicker"
                           data-target="#date-return" name="date-return" autocomplete="off"
                           value="<?php
                           //on affiche pas de date si c'est une nouvelle demande
                           //pour une nouvelle demande la date est initialisé à la date courante donc on doit la tester pour la virer
                              $dateret=$missionOrder->getDateReturn()->format('d/m/Y H');
                              $dateact=date('d/m/Y H');
                              if($dateret==$dateact)
                              {
                                echo '';
                              }
                              else{
                                echo $missionOrder->getDateReturn()->format('d/m/Y H:i:s');
                              }
                            ?>"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        />
                    </div>
                </div>
<?php
    if ($invitation == 0) {
?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Lieu de retour différent'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="place-return-different" name="place-return-different" type="checkbox"
                           value="1"
                            <?php if ($missionOrder->isPlaceReturnDifferent()) {
                                echo 'checked';
                            } ?>
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        >
                    </div>
                </div>

                <div class="form-group row place-return-form <?php echo $missionOrder->isPlaceReturnDifferent() ? "" : "d-none" ?>">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Lieu de retour'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-6">
                        <?php
                        $businessAddressSelected = null;
                        $personalAddressSelected = $userRequest->isPersonalAddress($missionOrder->getPlaceReturn());

                        foreach ($businessAddresses as $businessAddress) {
                            if ($businessAddress->isAddress($missionOrder->getPlaceReturn())) {
                                $businessAddressSelected = $businessAddress->getId();
                                break;
                            }
                        }

                        $freeAddress = (!$businessAddressSelected && !$personalAddressSelected);
                        ?>
                        <select autofocus class="form-control chosen-select" id="place-return" name="place-return" <?php echo ($disabled) ? 'disabled' : '' ?>>
                            <option value="<?php if ($freeAddress) { echo 'free_address'; } ?>"><?php if ($freeAddress) { echo $missionOrder->getPlaceReturn(); } ?></option>
                            <option data-address="<?php echo $userRequest->getPersonalAddress(); ?>" value="personal_address" <?php if ($personalAddressSelected || (!$missionOrder->getPlaceReturn() && $userRequest->getPersonalAddressDefault())) {
                                echo 'selected';
                            } ?>><?php echo T_('Adresse personnelle'); ?></option>
                            <?php
                            foreach ($businessAddresses as $businessAddress) {
                                $isSelected = (($businessAddress->getId() == $businessAddressSelected) || (!$missionOrder->getPlaceReturn() && $userRequest->getBusinessAddressDefault() == $businessAddress->getId()));
                                ?>
                                <option data-address="<?php echo $businessAddress->getAddress() ?>" value="<?php echo $businessAddress->getId() ?>" <?php if ($isSelected) {
                                    echo 'selected';
                                } ?>><?php echo $businessAddress->getTitle() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                        <textarea type="text" class="form-control mt-2 col-12 d-none" id="place-return-input"
                                  name="place-return-input" value="" <?php echo ($disabled) ? 'disabled' : '' ?>/></textarea>
                        <div id="place-return-address-save-group" class="mt-2 d-none">
                            <?php echo T_('Sauvegarder pour une ré-utilisation ultérieure'); ?>:&nbsp;
                            <input type="checkbox" class="mt-1" id="place-return-address-save"
                                name="place-return-address-save" value="1"
                                <?php echo ($disabled) ? 'disabled' : '' ?>/>
                        </div>
                        <div id="place-return-address-default-group" class="mt-2 d-none">
                            <?php echo T_('Adresse par défaut'); ?>:&nbsp;
                            <input type="checkbox" class="mt-1" id="place-return-address-default"
                                name="place-return-address-default" value="1"
                                <?php echo ($disabled) ? 'disabled' : '' ?>/>
                        </div>
                    </div>
                    <?php
                    if (!$disabled) {
                    ?>
                    <div class="col-sm-1 mt-2 pl-0">
                        <i id="place-return-free-address" class="fa fa-plus-circle text-success text-150 pl-1"
                           title="<?php echo T_('Addresse libre'); ?>"></i>
                    </div>
                    <?php
                    }
                    ?>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('La ville de séjour doit être renseignée') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Ville de séjour'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="city-stay" name="city-stay" value="<?php echo $missionOrder->getCityStay() ?>"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        />
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('Le pays de séjour doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Pays de séjour'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="country-stay" name="country-stay"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                            value="<?php echo $missionOrder->getCountryStay() ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="private-stay">
                            <?php echo T_('Séjour privé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="private-stay" name="private-stay" type="checkbox"
                            value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->getPrivateStay()->getIsPrivateStay()) {
                                echo 'checked';
                            } ?>>
                    </div>
                </div>

                <div id="private-stay-date-begin-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="private-stay-date-begin">
                            <?php echo '<i id="user_warning" title="' . T_('La date de début du séjour privé doit être renseignée') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Date de début du séjour privé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?> datetimepicker-input" id="private-stay-date-begin" data-toggle="datetimepicker"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                           data-target="#private-stay-date-begin" name="private-stay-date-begin" autocomplete="off"
                           value="<?php
                           //on affiche pas de date si c'est une nouvelle demande
                           //pour une nouvelle demande la date est initialisé à la date courante donc on doit la tester pour la virer
                              $dateprivstart=$missionOrder->getPrivateStay()->getDateBegin('d/m/Y H');
                              $dateact=date('d/m/Y H');
                              if($dateprivstart==$dateact)
                              {
                                echo '';
                              }
                              else{
                                echo $missionOrder->getPrivateStay()->getDateBegin('d/m/Y H:i:s');
                              }
                           ?>"/>
                    </div>
                </div>

                <div id="private-stay-date-end-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="private-stay-date-end">
                            <?php echo '<i id="user_warning" title="' . T_('La date de fin du séjour privé doit être renseignée') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Date de fin du séjour privé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?> datetimepicker-input" id="private-stay-date-end" data-toggle="datetimepicker"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                           data-target="#private-stay-date-end" name="private-stay-date-end" autocomplete="off"
                           value="<?php
                           //on affiche pas de date si c'est une nouvelle demande
                           //pour une nouvelle demande la date est initialisé à la date courante donc on doit la tester pour la virer
                              $dateprivret=$missionOrder->getPrivateStay()->getDateEnd('d/m/Y H');
                              $dateact=date('d/m/Y H');
                              if($dateprivret==$dateact)
                              {
                                echo '';
                              }
                              else{
                                echo $missionOrder->getPrivateStay()->getDateEnd('d/m/Y H:i:s');
                              }
                           ?>"/>
                    </div>
                </div>

                <div id="private-stay-place-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('Le Lieu de séjour privé doit renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Lieu de séjour privé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <input type="text" class="form-control
                            <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="private-stay-place" name="private-stay-place"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                           value="<?php echo $missionOrder->getPrivateStay()->getPlace() ?>"/>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Prise en charge de l\'hébergement'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="off-market-accommodation" name="off-market-accommodation" type="checkbox"
                           value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->isOffMarketAccomodation()) {
                                echo 'checked';
                            } ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Demande d’avance'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="advance-request" name="advance-request" type="checkbox"
                           value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->isadvanceRequest()) {
                                echo 'checked';
                            } ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Transport sur le marché'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="transport-market" name="transport-market" type="checkbox"
                           value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->isTransportMarket()) {
                                echo 'checked';
                            } ?>>
                    </div>
                </div>

                <div id="transport-market-justification-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Justification doit renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Justification'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <table border="1" width="<?php if ($mobile == 0) {
                            echo '780';
                        } else {
                            echo '285';
                        } ?>" style="border: 1px solid #D8D8D8;">
                            <tr>
                                <td style="padding:5px">
                                    <div id="transport-market-justification-editor"
                                         class="bootstrap-wysiwyg-editor pl-2 pt-1 editor"
                                         style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="transport-market-justification" type="hidden"
                                       name="transport-market-justification"
                                       value="<?php echo $missionOrder->getTransportMarketJustification() ?>"
                                        <?php echo ($disabled) ? 'disabled' : '' ?>
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Choix du transport doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Choix du transport'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if ($mobile) {
                            echo 'max-width:240px;';
                        } else {
                            if ($rright['ticket_user_company']) {
                                echo 'width:auto;';
                            } else {
                                echo 'max-width:269px;';
                            }
                        } ?>" class="form-control chosen-select select2" id="transport-choice" name="transport-choice[]"
                                multiple <?php echo ($disabled) ? 'disabled' : '' ?>
                        >
                            <option value=""></option>
                            <?php
                            foreach ($transportChoices as $transportChoice) {
                                $isSelected = $missionOrder->hasTransportChoices($transportChoice->getId());
                                ?>
                                <option value="<?php echo $transportChoice->getId() ?>" <?php if ($isSelected) {
                                    echo 'selected';
                                } ?>><?php echo $transportChoice->getName() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="transport-choice-administrative-vehicle-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="transport-choice-administrative-vehicle">
                            <?php echo '<i id="user_warning" title="' . T_('Le champ Véhicule administratif doit être renseigné') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Véhicule administratif'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if ($mobile) {
                            echo 'max-width:240px;';
                        } else {
                            if ($rright['ticket_user_company']) {
                                echo 'width:auto;';
                            } else {
                                echo 'max-width:269px;';
                            }
                        } ?>" class="form-control chosen-select select2" id="transport-choice-administrative-vehicle"
                                name="transport-choice-administrative-vehicle" <?php echo ($disabled) ? 'disabled' : '' ?>
                        >
                            <?php
                            foreach ($administrativeVehicles as $administrativeVehicle) {
                                $isSelected = $missionOrder->getAdministrativeVehicle()->getId() == $administrativeVehicle->getId();
                                ?>
                                <option value="<?php echo $administrativeVehicle->getId() ?>" <?php if ($isSelected) {
                                    echo 'selected';
                                } ?>><?php echo $administrativeVehicle->getName() . ' - ' . $administrativeVehicle->getNumberPlate() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div id="transport-choice-personal-vehicle-group" class="d-none">
                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="personal-vehicle-numberplate">
                                <?php echo '<i id="user_warning" title="' . T_('Plaque d\'immatriculation') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Plaque d\'immatriculation'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control
                                <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="personal-vehicle-numberplate" name="personal-vehicle-numberplate"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                                   value="<?php echo $missionOrder->getPersonalVehicle()->getNumberplate() ?>"
                            />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="personal-vehicle-horsepower">
                                <?php echo '<i id="user_warning" title="' . T_('Nombre de chevaux') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Nombre de chevaux'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" class="form-control
                                <?php if ($mobile) {
                                echo 'col-7';
                            } else {
                                echo 'col-5';
                            } ?>" id="personal-vehicle-horsepower" name="personal-vehicle-horsepower"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                                   value="<?php echo $missionOrder->getPersonalVehicle()->getHorsepower() ?>"
                            />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="personal-vehicle-trip-mileage">
                                <?php echo '<i id="user_warning" title="' . T_('Kilométrage du trajet') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Kilométrage du trajet'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <input type="number" class="form-control
                                <?php if ($mobile) {
                                    echo 'col-7';
                                } else {
                                    echo 'col-5';
                                } ?>"
                               id="personal-vehicle-trip-mileage" name="personal-vehicle-trip-mileage"
                                <?php echo ($disabled) ? 'disabled' : '' ?>
                               value="<?php echo $missionOrder->getPersonalVehicle()->getTripMileage() ?>"
                            />
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-2 col-form-label text-sm-right pr-0">
                            <label class="mb-0" for="personal-vehicle-passenger">
                                <?php echo '<i id="user_warning" title="' . T_('Noms des passagers') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                                <?php echo T_('Noms des passagers'); ?> :
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <select autofocus style="display:inline; <?php if ($mobile) {
                                echo 'max-width:240px;';
                            } else {
                                if ($rright['ticket_user_company']) {
                                    echo 'width:auto;';
                                } else {
                                    echo 'max-width:269px;';
                                }
                            } ?>" class="form-control chosen-select select2" id="personal-vehicle-passenger"
                                    name="personal-vehicle-passenger[]" multiple="multiple" <?php echo ($disabled) ? 'disabled' : '' ?>
                            >
                                <?php
                                foreach ($users as $passenger) {
                                    $isSelected = $missionOrder->getPersonalVehicle()->hasPassenger($passenger->getId());
                                    ?>
                                    <option value="<?php echo $passenger->getId() ?>" <?php if ($isSelected) {
                                        echo 'selected';
                                    } ?> ><?php echo $passenger->getFullName() ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="other-fees">
                            <?php echo T_('Autre frais (péage, parking...)'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if ($mobile == 0) {
                            echo '780';
                        } else {
                            echo '285';
                        } ?>" style="border: 1px solid #D8D8D8;">
                            <tr>
                                <td style="padding:5px">
                                    <div id="other-fees-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor"
                                         style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="other-fees" type="hidden" name="other-fees"
                                        value="<?php echo $missionOrder->getOtherFees() ?>"
                                        <?php echo ($disabled) ? 'disabled' : '' ?>
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="user">
                            <?php echo T_('Colloques'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="colloquiums" name="colloquiums" type="checkbox"
                           value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->getColloquiums()->getIsColloquiums()) {
                                echo 'checked';
                            } ?>>
                    </div>
                </div>

                <div id="colloquiums-group" class="form-group row d-none">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="colloquiums-registration-fees">
                            <?php echo '<i id="user_warning" title="' . T_('Frais d’inscription') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Frais d’inscription'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if ($mobile == 0) {
                            echo '780';
                        } else {
                            echo '285';
                        } ?>" style="border: 1px solid #D8D8D8;">
                            <tr>
                                <td style="padding:5px">
                                    <div id="colloquiums-registration-fees-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor"
                                         style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="colloquiums-registration-fees" type="hidden" name="colloquiums-registration-fees"
                                        value="<?php echo $missionOrder->getColloquiums()->getRegistrationFees() ?>"
                                        <?php echo ($disabled) ? 'disabled' : '' ?>
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="colloquiums-purchasing-card">
                            <?php echo '<i id="user_warning" title="' . T_('Carte achat') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Carte achat'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input id="colloquiums-purchasing-card" class="mt-2" name="colloquiums-purchasing-card" type="checkbox"
                            value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($missionOrder->getColloquiums()->isPurchasingCard()) {
                                echo 'checked';
                            } ?>>
                    </div>
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="colloquiums-program">
                            <?php echo '<i id="user_warning" title="' . T_('Programme') . '" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Programme'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" style="border:1px solid #D8D8D8; min-width:265px;">
                            <tbody>
                            <tr>
                                <td style="padding:15px;">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                                    <input style="color:transparent; width:160px;" id="colloquiums-program" class="file-to-upload"
                                           type="file" name="colloquiums-program[]" <?php echo ($disabled) ? 'disabled' : '' ?>
                                    > &nbsp;
                                    <button class="btn btn-sm btn-success"
                                            title="Enregistrer l'ordre de mission et charger le fichier"
                                            onclick="check_size();" name="upload" value="upload" type="submit"
                                            id="upload-mo" <?php echo ($disabled) ? 'disabled' : '' ?>
                                    >
                                        <i class="fa fa-upload"></i>
                                    </button>
                                    <div class="files-to-upload-group"></div>
                                    <?php
                                    $file = $missionOrder->getColloquiums()->getProgram();

                                    if ($file) {
                                    ?>
                                    <div id="colloquiums-program-uploaded" class="files-uploaded">
                                        <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>" style="text-decoration:none"><i style="vertical-align: middle;" class="fa fa-file text-info"></i>&nbsp;</a>
                                        <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>">
                                            <?php echo $file->getName() ?>
                                        </a>
                                        <br/>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
<?php
  } // end invitation
?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="comment">
                            <?php echo T_('Commentaire'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                            <tr>
                                <td style="padding:5px">
                                    <div id="comment-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="comment" type="hidden" name="comment" value="<?php echo $missionOrder->getComment() ?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
		</div>
                <?php
      $disable_amount_max_field = $missionOrder->getId() && $missionOrder->hasValidator($_SESSION['user_id']);
      $e = '';
      if (!$disable_amount_max_field) {

      }
?>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="comment">
                            <?php echo T_('Montant Estimé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
              <input id="amount_estimated" class="form-control col-5" type="text" name="amount_estimated" value="<?php echo $missionOrder->getEstimatedAmount() ?>">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="comment">
                            <?php echo T_('Montant Max'); ?> :
                        </label>
                    </div>
		    <div class="col-sm-9">
<?php
      if (!$disable_amount_max_field) {
?>
	      <input id="amount_max_display" class="form-control col-5" type="text" name="amount_max_display" value="<?php echo $missionOrder->getAmountMax() ?>" disabled>
              <input id="amount_max" type="hidden" name="amount_max" value="<?php echo $missionOrder->getAmountMax() ?>">
<?php
      } else {
?>
              <input id="amount_max" class="form-control col-5" type="text" name="amount_max" value="<?php echo $missionOrder->getAmountMax() ?>">
<?php
      }
?>
                    </div>
		</div>
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="comment">
                            <?php echo T_('Montant réalisé'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
<?php
      if (!$disable_amount_max_field) {
?>
              <input id="real_amount_display" class="form-control col-5" type="text" name="amount_max_display" value="<?php echo $missionOrder->getRealAmount() ?>" disabled>
              <input id="amount_real" type="hidden" name="amount_real" value="<?php echo $missionOrder->getRealAmount() ?>">
<?php
      } else {
?>
              <input id="amount_real" class="form-control col-5" type="text" name="amount_real" value="<?php echo $missionOrder->getRealAmount() ?>">
<?php
      }
?>
                    </div>
		</div>

                <input id="type-form" type="hidden" name="type-form" value="mission-order"/>
                <input id="url-action" type="hidden" name="url-action" value="<?php echo $urlAction ?>"/>
                <input id="url-base" type="hidden" name="url-base" value="<?php echo $urlBase ?>"/>
                <input id="personal-address-default" type="hidden" name="personal-address-default" value="<?php echo $userRequest->getPersonalAddressDefault(); ?>"/>
                <input id="business-address" type="hidden" name="business-address" value="<?php echo $userRequest->getBusinessAddressDefault(); ?>"/>
                <input id="form-disabled" type="hidden" name="form-disabled" value="<?php echo $disabled; ?>"/>

                <!-- START buttons -->
                <div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center">
                    <button title="CTRL+S" accesskey="s" name="modify-mo" id="modify-mo"
                        value="modify" type="submit" class="btn btn-secondary btn-success"
                        <?php echo ($disabled) ? 'disabled' : '' ?>
                    >
                        <i class="fa fa-save"></i>
                    <?php
                        if (!$mobile) {
                            echo '&nbsp;' . T_('Enregistrer');
                        }
                    ?>
                    </button>
                    <?php
                    if ($missionOrder->getId() && !$missionOrder->getIsModel() && $missionOrder->isOwner($_SESSION['user_id'])) {
                    ?>
                    <a href="<?php echo $urlAction ?>&delete=1" onclick="javascript: return confirm('Êtes-vous sur de vouloir supprimer cette demande ?');">
                        <button title="CTRL+D" accesskey="d" name="delete" id="delete" type="button"
                                value="delete" class="btn btn-secondary btn-danger"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        >
                            <i class="fa fa-trash"></i>
                            <?php
                            if (!$mobile) {
                                echo '&nbsp;' . T_('Supprimer');
                            }
                            ?>
                        </button>
                    </a>
                    <?php
                    }
                    ?>
                    <a href="<?php echo $urlAction ?>">
                        <button title="ALT+SHIFT+c" accesskey="c" name="cancel-po" id="cancel-po" value="cancel"
                                type="button" class="btn btn-secondary btn-danger" formnovalidate
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        >
                            <i class="fa fa-times"></i>
                            <?php
                            if(!$mobile) {echo '&nbsp;'.T_('Annuler');}
                            ?>
                        </button>
                    </a>
                </div>
            </div> <!-- div end p-3 -->
        </div> <!-- div end body card -->
    </form>
</div> <!-- div end card -->

<!-- datetime picker scripts  -->
<script type="text/javascript" src="./components/moment/min/moment.min.js"></script>
<?php
if($ruser['language']=='fr_FR') {echo '<script src="./components/moment/locale/fr.js" charset="UTF-8"></script>';}
if($ruser['language']=='de_DE') {echo '<script src="./components/moment/locale/de.js" charset="UTF-8"></script>';}
if($ruser['language']=='es_ES') {echo '<script src="./components/moment/locale/es.js" charset="UTF-8"></script>';}
?>
<script src="./components/tempus-dominus/build/js/tempusdominus-bootstrap-4.min.js" charset="UTF-8"></script>

<script>
    $(document).ready(function () {
        var typeMissionCopy = $("input[name='type-mission']:checked").val();
	var formDisabled = $('#form-disabled').val();
	var budgetValidator = {
<?php
        foreach(MissionOrder::getBudgetValidator() as $key=>$value) {
          echo '"'.$key.'":"'.$value.'",';
        }
?>
        };


        // init select2
        $('.select2').select2();
        $('#model-list-mo').select2({
            templateResult: initSelectModel
        });

        // init datetime picker
        var date = moment($('#date-start').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
        $('#date-start').datetimepicker({date: date, format: 'DD/MM/YYYY HH:mm:ss'});
        var date = moment($('#date-return').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
        $('#date-return').datetimepicker({date: date, format: 'DD/MM/YYYY HH:mm:ss'});
        var date = moment($('#private-stay-date-begin').val(), 'DD-MM-YYYY').toDate();
        $('#private-stay-date-begin').datetimepicker({date: date, format: 'DD/MM/YYYY'});
        var date = moment($('#private-stay-date-end').val(), 'DD-MM-YYYY').toDate();
        $('#private-stay-date-end').datetimepicker({date: date, format: 'DD/MM/YYYY'});

        // init wysiwig with mission order values
        //$('#list-people-involved-assignment-editor').html($('#list-people-involved-assignment').val());
        $('#additional-budget-information-editor').html($('#additional-budget-information').val());
        $('#reason-for-mission-editor').html($('#reason-for-mission').val());
        $('#other-fees-editor').html($('#other-fees').val());
        $('#transport-market-justification-editor').html($('#transport-market-justification').val());
        $('#colloquiums-registration-fees-editor').html($('#colloquiums-registration-fees').val());
        $('#comment-editor').html($('#comment').val());

        if (formDisabled) {
            // disabled wysiwig
            setTimeout(() => {
                //$('#list-people-involved-assignment-editor').attr('contenteditable', false);
                $('#additional-budget-information-editor').attr('contenteditable', false);
                //$('#reason-for-mission-editor').attr('contenteditable', false);
                $('#other-fees-editor').attr('contenteditable', false);
                $('#transport-market-justification-editor').attr('contenteditable', false);
                $('#colloquiums-registration-fees-editor').attr('contenteditable', false);
                $('#comment-editor').attr('contenteditable', false);
            }, 500);
	}
<?php
$disable_amount_max_field = $missionOrder->getId() && $missionOrder->hasValidator($_SESSION['user_id']);
 if (!$disable_amount_max_field) {
?>
   $('#amount_max').attr('contenteditable', false);
<?php
 }
?>

        // init om for guest input
        initOmForGuest();

        // init collective mission
        initCollectiveMission();

	// init place start and place return
<?php
      if($invitation == 0) {
?>
        initChoosePlace('place-start');
        initChoosePlace('place-return');
        initAddressDefault('place-start-address-default', 'place-return-address-default');
        initAddressDefault('place-return-address-default', 'place-start-address-default');
        // init private stay
        initPrivateStay();

        // init transport market
        initTransportMarket();

<?php
      }
?>
        // init colloquiums
        // init transport choice
        initAdministrativeVehicle();
        initPersonalVehicle();
        initColloquiums();

        // events list
        $('#mission-order-form').on('submit', function (evt) {
            evt.preventDefault();

            //$('#list-people-involved-assignment').val($('#list-people-involved-assignment-editor').html().trim());
            $('#additional-budget-information').val($('#additional-budget-information-editor').html().trim());
	    $('#reason-for-mission').val($('#reason-for-mission-editor').html().trim());
<?php
      if($invitation == 0) {
?>
	    $('#other-fees').val($('#other-fees-editor').html().trim());
            $('#transport-market-justification').val($('#transport-market-justification-editor').html().trim());
            $('#colloquiums-registration-fees').val($('#colloquiums-registration-fees-editor').html().trim());
<?php
      }
?>
            $('#comment').val($('#comment-editor').html().trim());
            $('input[name="type-mission"]').attr('disabled', false);

            if (!canSubmit()) {
                $(document).scrollTop(0);
                return false;
            }

            evt.currentTarget.submit();
        });

        $('#om-for-guest').on('click', function () {
            initOmForGuest();
        });

        $('#collective-mission').on('click', function () {
            initCollectiveMission();
        });

        $('input[name="type-mission"]').on('click', function () {
            initTypeMission();
        });

        $('#place-start').change(function () {
            initChoosePlace('place-start');
            if ($('#place-return option:selected').val() != "free_address") {
                initAddressDefault('place-return-address-default', 'place-start-address-default');
            }
        });

        $('#place-return').change(function () {
            initChoosePlace('place-return');
            if ($('#place-start option:selected').val() != "free_address") {
                initAddressDefault('place-start-address-default', 'place-return-address-default');
            }
        });

        $('#place-start-address-default').click(function () {
            initAddressDefault('place-start-address-default', 'place-return-address-default');
        });

        $('#place-return-address-default').click(function () {
            initAddressDefault('place-return-address-default', 'place-start-address-default');
        });

        $('#place-start-free-address').click(function () {
            initFreeAddress('place-start');
        });

        $('#place-return-free-address').click(function () {
            initFreeAddress('place-return');
        });

        $('#place-return-different').on('click', function () {
            if ($("#place-return-different:checked").val()) {
                $('.place-return-form').removeClass('d-none');
                $('.place-return-form').addClass('d-flex');
            } else {
                $('.place-return-form').removeClass('d-flex');
                $('.place-return-form').addClass('d-none');
            }
        });

        $('#private-stay').on('click', function () {
            initPrivateStay();
        });

        $('#transport-market').on('click', function () {
            initTransportMarket();
        });

        $('#transport-choice').change(function () {
            initAdministrativeVehicle();
            initPersonalVehicle();
        });

        $('#colloquiums').on('click', function () {
            initColloquiums();
        });

        $('#model-list-mo').change(function () {
            var paramsModel = $('#url-action').val();

            if ($('#model-list-mo option:selected').val() !== "") {
                paramsModel +=  '&model=' + $('#model-list-mo option:selected').val();
                window.location.href = paramsModel;
            }
	});

        $("#budget-data").change(function () {
		console.log(budgetValidator[$(this).val()]);
		//$('#validators').val(udgetValidator[$(this).val()]);
		//$('#validators').trigger('change');
        });

	function initOmForGuest() {
            console.log("[initOmForGuest]");
            console.log("[initOmForGuest] $('#om-for-guest').attr('checked') : " + $('#om-for-guest').attr('checked'));
            if ($('#om-for-guest').attr('checked')) {
                typeMissionCopy = $("input[name='type-mission']:checked").val();
                $('input[name="type-mission"]').attr('disabled', true);
                $('#guest-group').removeClass('d-none');
                $('#with-fees').prop('checked', true);
                $('input[name="guest-name"]').attr('hidden',false);
            } else {
                $('input[name="type-mission"]').attr('disabled', false);
                $('#with-fees').prop('checked', (typeMissionCopy == 1));
                $('#without-fees').prop('checked', (typeMissionCopy == 2));
                $('#standing-mission-order').prop('checked', (typeMissionCopy == 3));
                $('#guest-group').addClass('d-none');

            }
            initTypeMission();
        }

        function initCollectiveMission() {
            toggleElement('#collective-mission-group', $("#collective-mission:checked").val());
        }

	function initTypeMission() {
	    console.log("[initTypeMission] input[name='type-mission']:checked : " + $("input[name='type-mission']:checked").val());
            if ($("input[name='type-mission']:checked").val() == 1) {
                $('#with-fees-group').removeClass('d-none');
            } else {
                $('#with-fees-group').addClass('d-none');
            }
            if ($("input[name='type-mission']:checked").val() == 2) {
                $('#without-fees-group').removeClass('d-none');
                $('#validators').val('<?php echo $_SESSION["user_id"]?>').trigger('change');
                $('#resp-cred').addClass('d-none');
	    } else {
                $('#without-fees-group').addClass('d-none');
                $('#resp-cred').removeClass('d-none');
                //$('#validators').val(null).trigger('change');
            }
            if ($("input[name='type-mission']:checked").val() == 3) {
                $('#validators').val('<?php echo $_SESSION["user_id"]?>').trigger('change');
                $('#resp-cred').addClass('d-none');
            }


            if (formDisabled) {
                $('input[name="type-mission"]').attr('disabled', true);
            }

        }

	function initChoosePlace(target) {
		console.log("[initChoosePlace(" + target + ")]");
		console.log("val : " + $('#' + target + ' option:selected').val())
            if ($('#' + target + ' option:selected').val() == "") {
                $('#' + target + '-input').val("");
                toggleElement('#' + target + '-input', false);
                toggleElement('#' + target + '-address-save-group', false);
                toggleElement('#' + target + '-address-default-group', false);
            }

            if ($('#' + target + ' option:selected').val() != "") {
                var canShowPersonalAddress = $('#' + target + ' option:selected').val() == "personal_address";
                var canShowFreeAddress = $('#' + target + ' option:selected').val() == "free_address";
                var isReadOnly = (!canShowFreeAddress && !canShowPersonalAddress);
                var placeStartShowValue = "";
                var addressDefault = false;

                if (canShowPersonalAddress) {
                    addressDefault = ($('#personal-address-default').val() == 1) ? true : false;
                } else {
                    addressDefault = ($('#business-address').val() == $('#'+ target +' option:selected').val()) ? true : false;
                }

                placeStartShowValue = $('#' + target + ' option:selected').attr('data-address');

                $('#' + target + '-input').val(placeStartShowValue);
                $('#' + target + '-input').prop('readonly', isReadOnly);
                $('#' + target + '-address-default').prop('checked', addressDefault);
                toggleElement('#' + target + '-input', true);
                toggleElement('#' + target + '-address-save-group', canShowPersonalAddress);
                toggleElement('#' + target + '-address-default-group', !canShowFreeAddress);
            }
        }

        function initFreeAddress(target) {
            $('#' + target + '-input').val("");
            $('#' + target + ' option').prop('selected', false);
            $('#' + target + '-input').prop('readonly', false);
            toggleElement('#' + target + '-input', true);
            toggleElement('#' + target + '-address-save-group', false);
            toggleElement('#' + target + '-address-default-group', false);
        }

        function initPrivateStay() {
            var isChecked = $('#private-stay').is(':checked', true);

            toggleElement('#private-stay-date-begin-group', isChecked);
            toggleElement('#private-stay-date-end-group', isChecked);
            toggleElement('#private-stay-place-group', isChecked);
        }

        function initTransportMarket() {
            var isChecked = $('#transport-market').is(':checked');

            toggleElement('#transport-market-justification-group', !isChecked);
        }

        function initAdministrativeVehicle() {
            var isSelected = isAdministrativeVehicle();

            toggleElement('#transport-choice-administrative-vehicle-group', isSelected);
        }

        function initPersonalVehicle() {
            var isSelected = isPersonalVehicle();

            toggleElement('#transport-choice-personal-vehicle-group', isSelected, 'block');
        }

        function initColloquiums() {
            var isChecked = $('#colloquiums').is(':checked', true);

            toggleElement('#colloquiums-group', isChecked);
        }

        function initSelectModel(data) {
            if (!data.id) {
                return data.text;
            }

            var paramsView = $('#url-base').val();
            paramsView += '&id='+ data.id + '&preview-mode=1';
            var option = $('<span></span>');
            var preview = $('<a class="model-view" href="/'+ paramsView +'" target="_blank"><i class="fa fa-eye"></i></a>');

            preview.on('mouseup', function (evt) {
                evt.stopPropagation();
            });

            option.text(data.text);
            option.append(preview);

            return option;
        }

	function isPersonalVehicle() {
            if($('#transport-choice').length) {
              var transportChoicesSelected = $('#transport-choice').val();
              return transportChoicesSelected.includes('3');
            }
            return false; 
        }

	function isAdministrativeVehicle() {
            if($('#transport-choice').length) {
              var transportChoicesSelected = $('#transport-choice').val();
              return transportChoicesSelected.includes('5')
	    }
            return false;
        }

        function canSubmit() {
            if (!$('#title').val()) {
                sendError("<?php echo T_('Le champ Titre est requis'); ?>");
                return false;
            }
            if (!$('#service').val()) {
                sendError("<?php echo T_('Le champ Service est requis'); ?>");
                return false;
            }
            //if ($('#collective-mission').is(':checked') && !$('#list-people-involved-assignment').val()) {
            //    sendError("<?php echo T_('Le champ Liste des personnes concernées pour la mission est requis'); ?>");
            //    return false;
            // }
            if (!checkTypeMission()) {
                return false;
            }
            if ($('#validators').val().length == 0) {
                sendError("<?php echo T_('Le champ Responsable des crédits est requis'); ?>");
                return false;
            }
            if (!$('#reason-for-mission').val()) {
                sendError("<?php echo T_('Le champ Motif de la mission est requis'); ?>");
                return false;
	    }
<?php
      if($invitation == 0) {
?>
            if (!checkPlace()) {
                return false;
            }
            if (!$('#city-stay').val()) {
                sendError("<?php echo T_('Le champ Ville de séjour est requis'); ?>");
                return false;
            }
            if (!$('#country-stay').val()) {
                sendError("<?php echo T_('Le champ Pays de séjour est requis'); ?>");
                return false;
            }
            if (!checkPrivateStay()) {
                return false;
            }
            if (!$('#transport-market').is(':checked') && !$('#transport-market-justification').val()) {
                sendError("<?php echo T_('Le champ Justification est requis'); ?>");
                return false;
            }
            if (!checkTransportChoice()) {
                return false;
	    }
<?php
      }
?>
            if(!checkColloquiums()) {
                return false;
            }

            //on re-enable le champs valideur si il a été disabled (dans le cas on a utilisé les valideurs fixes)
            $('#validators').attr('disabled',false);

            return true;
        }

        function initAddressDefault(from, target) {
            var startAddressIsSelected = $('#place-start-address-default').prop('checked');
            var returnAddressIsSelected = $('#place-return-address-default').prop('checked');
            var isSameAddress = $('#place-start option:selected').val() == $('#place-return option:selected').val();

            if (isSameAddress) {
                $('#place-start-address-default').prop('checked', $('#' + from).prop('checked'));
                $('#place-return-address-default').prop('checked',  $('#' + from).prop('checked'));
            } else if (startAddressIsSelected && returnAddressIsSelected) {
                $('#' + target).prop('checked', false)
            }

            $('#place-start-address-default').val($('#place-start').val());
            $('#place-return-address-default').val($('#place-return').val());
        }

	function checkTypeMission() {
            console.log("[checkTypeMission]");
            if ($('input[name="type-mission"]:checked').val() === undefined) {
                sendError("<?php echo T_('Le champ Type de mission est requis'); ?>");
                return false;
	    }
            console.log("[checkTypeMission] type-mission val : " + $('input[name="type-mission"]:checked').val());
            console.log("[checkTypeMission] budget-data : " + $('#budget-data').val());
            if ($('input[name="type-mission"]:checked').val() == 1) {
                if (!$('#budget-data').val()) {
                    sendError("<?php echo T_('Le champ Données budgétaires est requis'); ?>");
                    return false;
                }
                //permet de rendre le champs données budgétaire supplémentaire obligatoire uniquement pour un labo donné
                if(window.location.hostname.includes("imbe.fr") && !($('#additional-budget-information-editor').html()))
                {
                    sendError("<?php echo T_('Le champ Informations de budget supplémentaires est requis'); ?>");
                    return false;
                }
            }
            if ($('input[name="type-mission"]:checked').val() == 2) {
                if (!$('#care-organization').val()) {
                    sendError("<?php echo T_('Le champ Organisme de prise en charge est requis'); ?>");
                    return false;
                }
            }

            return true;
        }

        function checkPlace() {
            var dateStart = moment($('#date-start').val(), 'DD-MM-YYYY hh:mm:ss');
            var dateReturn = moment($('#date-return').val(), 'DD-MM-YYYY hh:mm:ss');

            if (!$('#date-start').val()) {
                sendError("<?php echo T_('Le champ Date et heure de départ est requis'); ?>");
                return false;
            }
	    if (!$('#place-start-input').val()) {
                console.log($('#place-start-input').val());
                sendError("<?php echo T_('Le champ Lieu de départ est requis'); ?>");
                return false;
            }
            if (!$('#date-return').val()) {
                sendError("<?php echo T_('Le champ Date et heure de retour est requis'); ?>");
                return false;
            }
            if ($('#place-return-different:checked').val() && !$('#place-return-input').val()) {
                sendError("<?php echo T_('Le champ Lieu de retour est requis'); ?>");
                return false;
            }
            if (dateStart > dateReturn) {
                sendError("<?php echo T_('Le champ Date et heure de retour doit être après le champ Date et heure de départ'); ?>");
                return false;
            }

            return true;
        }

        function checkPrivateStay() {
            var datePrivateStayBegin = moment($('#private-stay-date-begin').val(), 'DD-MM-YYYY');
            var datePrivateStayEnd = moment($('#private-stay-date-end').val(), 'DD-MM-YYYY');

            if ($('#private-stay:checked').val()) {
                if (!$('#private-stay-date-begin').val()) {
                    sendError("<?php echo T_('Le champ Date de début du séjour privé est requis'); ?>");
                    return false;
                }
                if (!$('#private-stay-date-end').val()) {
                    sendError("<?php echo T_('Le champ Date de fin du séjour privé est requis'); ?>");
                    return false;
                }
                if (!$('#private-stay-place').val()) {
                    sendError("<?php echo T_('Le champ Lieu de séjour privé est requis'); ?>");
                    return false;
                }
                if (datePrivateStayBegin > datePrivateStayEnd) {
                    sendError("<?php echo T_('Le champ Date de fin du séjour privé doit être après le champ Date de début du séjour privé'); ?>");
                    return false;
                }
            }

            return true;
        }

        function checkTransportChoice() {
            if ($('#transport-choice').val().length == 0) {
                sendError("<?php echo T_('Le champ Choix du transport est requis'); ?>");
                return false;
            }
            if (isAdministrativeVehicle() && !$('#transport-choice-administrative-vehicle').val()) {
                sendError("<?php echo T_('Le champ Véhicule administratif est requis'); ?>");
                return false;
            }
            if (isPersonalVehicle() && !checkPersonalVehicle()) {
                return false;
            }

            return true;
        }

        function checkPersonalVehicle() {
            if (!$('#personal-vehicle-numberplate').val()) {
                sendError("<?php echo T_('Le champ Plaque d\'immmatriculation est requis'); ?>");
                return false;
            }
            if (!$('#personal-vehicle-horsepower').val()) {
                sendError("<?php echo T_('Le champ Nombre de chevaux est requis'); ?>");
                return false;
            }

            if (!$('#personal-vehicle-trip-mileage').val()) {
                sendError("<?php echo T_('Le champ Kilométrage du trajet est requis'); ?>");
                return false;
            }
            if ($('#personal-vehicle-passenger').val().length == 0) {
                sendError("<?php echo T_('Le champ Nom des passagers est requis'); ?>");
                return false;
            }

            return true;
        }

        function checkColloquiums() {
            if ($('#colloquiums:checked').val()) {
                if (!$('#colloquiums-registration-fees').val()) {
                    sendError("<?php echo T_('Le champ Frais d\'inscription est requis'); ?>");
                    return false;
                }
                if (!$('#colloquiums-program').val() && $('#colloquiums-program-uploaded').children().length == 0) {
                    sendError("<?php echo T_('Le champ Programme est requis'); ?>");
                    return false;
                }
            }

            return true;
        }
    });

    function check_size() {
        $('form').submit(function (e) {
            if (!($('#rib')[0].files[0].size < 73400320)) {
                //Prevent default and display error
                alert('Le fichier est joint est trop volumineux, la taille maximum est 70M');
                e.preventDefault();
            }
        });
    }

    jQuery(function ($) {
        //CTRL+S to save ticket
        $(document).keydown(function (e) {
            var key = undefined;
            var possible = [e.key, e.keyIdentifier, e.keyCode, e.which];
            while (key === undefined && possible.length > 0) {
                key = possible.pop();
            }
            if (key && (key == '115' || key == '83') && (e.ctrlKey || e.metaKey) && !(e.altKey)) {
                e.preventDefault();
                $('#myform #modify').click();
                return false;
            }
            return true;
        });
    });

    //datetimepicker icon default
    $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
        icons: {
            time: 'fa fa-clock text-info',
            date: 'fa fa-calendar text-info',
            up: 'fa fa-arrow-up',
            down: 'fa fa-arrow-down',
            previous: 'fa fa-chevron-left',
            next: 'fa fa-chevron-right',
            today: 'fa fa-calendar-check-o',
            clear: 'fa fa-trash',
            close: 'fa fa-times'
        } });
</script>
