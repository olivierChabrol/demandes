<?php
use Models\Request\BaseRequest;

$urlAction .= '&type-form=purchase-order';
$urlBase = $urlAction;

if ($purchaseOrder->getIsModel()) {
    // Build url for model
    $urlAction .= '&model=' . $purchaseOrder->getId();
}
$request = $purchaseOrder;

if ($purchaseOrder->getId() && !$purchaseOrder->getIsModel()) {
    // Build url for updating a purchase order
    $urlAction .= '&id='. $purchaseOrder->getId() .'&type-form=purchase-order';
}
$stateDisabled = [BaseRequest::STATUS_VALID, BaseRequest::STATUS_REJECT, BaseRequest::STATUS_CANCEL];
$disabled = false;

if (!$purchaseOrder->getIsModel() && in_array($purchaseOrder->getStatus(), $stateDisabled)) {
    // the purchase order is not a model and has reached its final state (valid/reject/cancel), we disable the form
    $disabled = true;
}

if ($purchaseOrder->getOwner()->getId() && $purchaseOrder->getOwner()->getId() != $userRequest->getId()) {
    // the user is not the owner of the purchase order so we need to load user owner for printing his services
    $userOwner
        ->setId($purchaseOrder->getOwner()->getId())
        ->load();
} else {
    // the user is the owner of the purchase order
    $userOwner = $userRequest;
}
?>

<div class="card bcard shadow mt-2 purchase-order-group" id="purchase-order-group" draggable="false">
	<form class="form-horizontal" id="purchase-order-form" enctype="multipart/form-data" method="post" action="<?php echo $urlAction ?>" >
		<div class="card-header">
			<h5 class="card-title">
				<i class="fa fa-ticket-alt"></i>
                <?php echo T_('Bon de commande'); ?>
                <?php
                if ($purchaseOrder->getId() || $purchaseOrder->getIsModel()) {
                    if ($purchaseOrder->getIsModel()) {
                        echo '- '.T_('Modèle').' ';
                    }
                    echo 'n° '.$purchaseOrder->getId().' : '.$purchaseOrder->getTitle();
                }
                ?>
			</h5>
            <?php
            if ((!$purchaseOrder->getId() || $purchaseOrder->getIsModel()) && count($modelsPurchaseOrder)) {
                ?>
                <div>
                    <h5 class="card-title">
                        <select id="model-list-po" name="model-list">
                            <option value=""><?php echo T_('Utiliser un modèle') ?></option>
                            <?php
                            foreach ($modelsPurchaseOrder as $model) {
                                ?>
                                <option value="<?php echo $model->getPurchaseOrder()->getId() ?>"><?php echo $model->getPurchaseOrder()->getTitle() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </h5>
                </div>
                <?php
            }

            $currentModel = $purchaseOrder->hasModel($purchaseOrder->getId());

            if ($purchaseOrder->getId() && !$purchaseOrder->getIsModel() && !$currentModel) {
                ?>
                <div>
                    <h5 class="card-title">
                        <a href="<?php echo $urlAction ?>&save-model=1">
                            <i class="fa fa-save"></i>
                            <?php echo T_('Sauvegarder pour une ré-utilisation ultérieure') ?>
                        </a>
                    </h5>
                </div>
                <?php
            }

            if ($purchaseOrder->getId() && !$purchaseOrder->getIsModel() && $currentModel) {
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
                        <label class="mb-0" for="title">
                            <?php
                            echo '<i id="user_warning" title="'.T_('Le champ Titre est requis.').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                            echo T_('Titre').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <input id="title" type="text" name="title"
                            value="<?php echo $purchaseOrder->getTitle() ?>"
                            <?php echo ($disabled) ? 'disabled' : '' ?>
                        />
                    </div>
                </div>

                <?php
                if ($purchaseOrder->getId() && !$purchaseOrder->getIsModel()) {
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
                            <input id="owner" type="text" name="owner" value="<?php echo $purchaseOrder->getOwner()->getFullName() ?>" disabled />
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
                  <div class='form-group row' <?php echo $hideteam; ?>>
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="service">
                            <?php
                            echo '<i id="user_warning" title="'.T_('Le champ Service est requis.').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                            echo T_('Votre equipe/service').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select select2" id="service" name="service" <?php echo ($disabled) ? 'disabled' : '' ?> >
                            <?php


                            foreach($userServices as $service) {
                                if ($first && $userServicesLength > 0 || ($purchaseOrder->getService()->getId() == $service->getId())) {
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
                        <label class="mb-0" for="quote">
                            <?php
                            echo T_('Devis').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" style="border:1px solid #D8D8D8; min-width:265px;">
                            <tbody>
                                <tr>
                                    <td style="padding:15px;">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="100000000">
                                        <input style="color:transparent; width:160px;" id="quote" class="file-to-upload ajax" type="file" name="quote[]" <?php echo ($disabled) ? 'disabled' : '' ?> multiple> &nbsp;
                                        <!--<button class="btn btn-sm btn-success" title="Enregistrer le ticket et charger le fichier" onclick="check_size();"
                                            name="upload" value="upload" type="submit" id="upload-po"
                                            <?php //echo ($disabled) ? 'disabled' : '' ?>
                                        >
                                            <i class="fa fa-upload"></i>
                                        </button>-->

                                        <div class="files-to-upload-group"></div>
                                        <?php
                                        //utile pour le multifile ajax
                                        $purchaseOrder->setUploadDir();
                                        $uploadDir="upload/tmp/".$purchaseOrder->getUploadDir();
                                        ?>
                                        <input type="hidden" id="uploaddir" name="uploaddir" value="<?php echo $uploadDir ?>">
                                        <?php
                                        //check size
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

                                        $files = $purchaseOrder->getQuote();

                                        if ($files) {
                                          foreach($files as $file) {

                                            ?>

                                            <div class="files-uploaded">
                                                <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>" style="text-decoration:none"><i style="vertical-align: middle;" class="fa fa-file text-info"></i>&nbsp;</a>
                                                <a target="_blank" title="Télécharger le fichier <?php echo $file->getName() ?>" href="<?php echo $file->getPath() ?>">
                                                    <?php echo $file->getName() ?>
                                                </a>
                                                <br/>
                                            </div>
                                            <?php
                                          }
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="additional-ordering-information">
                            <?php
                            echo T_('Informations de commande supplémentaires').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                            <tr>
                                <td style="padding:5px">
                                    <div id="additional-ordering-information-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="additional-ordering-information" type="hidden" name="additional-ordering-information"
                                        value="<?php echo $purchaseOrder->getAdditionalOrderingInformation() ?>"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="budget-data">
                            <?php
                            echo '<i id="user_warning" title="'.T_('Sélectionner les données budgétaires').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
                            echo T_('Données budgétaires').' :';
                            ?>
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select" id="budget-data" name="budget-data" <?php echo ($disabled) ? 'disabled' : '' ?> >
                            <option value=""></option>
                            <?php
                            foreach($budgetDatas as $budgetData) {
                                if ($budgetData->getCategory() != \Models\Request\Common\BudgetData::CATEGORY_PURCHASE_ORDER) {
                                    continue;
                                }

                                $isSelected = ($purchaseOrder->getBudgetData()->getId() == $budgetData->getId());
                                ?>
                                <option value="<?php echo $budgetData->getId() ?>" <?php if ($isSelected) { echo 'selected'; } ?>><?php echo $budgetData->getName() ?></option>
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
                                        value="<?php echo $purchaseOrder->getAdditionalBudgetInformation() ?>"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="validators-po">
                            <?php echo '<i id="user_warning" title="'.T_('Responsables de la ligne budgétaire').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
                            <?php echo T_('Responsables de la ligne budgétaire'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                            <?php
                            //on passe par un fichier externe pour les valideurs. De cette manière si un labo veut faire un dev différent sur cette partie on exclue le fichier validator.php du git pull
                            require_once("validator.php");
                            ?>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="supplier-contact">
                            <?php echo T_('Contact du fournisseur'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                            <tr>
                                <td style="padding:5px">
                                    <div id="supplier-contact-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="supplier-contact" type="hidden" name="supplier-contact"
                                           value="<?php echo $purchaseOrder->getSupplierContact() ?>"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="supplier-contact">
                            <?php echo T_('Adresse de livraison'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <table border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
                            <tr>
                                <td style="padding:5px">
                                    <div id="delivery-address-editor" class="bootstrap-wysiwyg-editor pl-2 pt-1 editor" style="min-height:100px; max-width:775px">
                                    </div>
                                    <input id="delivery-address" type="hidden" name="delivery-address"
                                           value="<?php echo $purchaseOrder->getDeliveryAddress() ?>"
                                    />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="purchase-card">
                            <?php echo T_('Carte achat'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9 mt-2">
                        <input id="purchase-card" name="purchase-card" type="checkbox"
                            value="1" <?php echo ($disabled) ? 'disabled' : '' ?>
                            <?php if ($purchaseOrder->isPurchaseCard()) { echo 'checked'; } ?>
                        >
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="code-nacre">
                            <?php echo T_('Code Nacres'); ?> :
                        </label>
                    </div>
                    <div class="col-sm-9">
                        <select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select select2" id="code-nacre" name="code-nacre[]" multiple="multiple" <?php echo ($disabled) ? 'disabled' : '' ?> >
                            <option value="<?php echo \Models\Request\PurchaseOrder\CodeNacre::ID_UNKNOW ?>" <?php if ($purchaseOrder->hasCodeNacre(\Models\Request\PurchaseOrder\CodeNacre::ID_UNKNOW)) { echo 'selected'; } ?>>
                                <?php echo T_('Non connu'); ?>
                            </option>
                            <?php
                            foreach($codeNacre as $codeNacreItem) {
                                $isSelected = $purchaseOrder->hasCodeNacre($codeNacreItem->getId());
                                ?>
                                <option value="<?php echo $codeNacreItem->getId() ?>" <?php if ($isSelected) { echo 'selected'; } ?>><?php echo $codeNacreItem->getCode().' - '.$codeNacreItem->getWording() ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>

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
                                    <input id="comment" type="hidden" name="comment" value="<?php echo $purchaseOrder->getComment() ?>" />
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <input id="type-form" type="hidden" name="type-form" value="purchase-order" />
                <input id="form-disabled" type="hidden" name="form-disabled" value="<?php echo $disabled; ?>"/>
                <input id="url-action" type="hidden" name="url-action" value="<?php echo $urlAction ?>"/>
                <input id="url-base" type="hidden" name="url-base" value="<?php echo $urlBase ?>"/>

                <!-- START buttons -->
                <div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center">
                    <button title="CTRL+S" accesskey="s" name="modify-po" id="modify-po" value="modify"
                        type="submit" class="btn btn-secondary btn-success"
                        <?php echo ($disabled) ? 'disabled' : '' ?>
                    >
                        <i class="fa fa-save"></i>
                        <?php
                        if(!$mobile) {echo '&nbsp;'.T_('Valider');}
                        ?>
                    </button>
                    <?php
                    if ($purchaseOrder->getId() && !$purchaseOrder->getIsModel() && $purchaseOrder->isOwner($_SESSION['user_id'])) {
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
                <!-- END buttons -->
			</div> <!-- div end p-3 -->
		</div> <!-- div end body card -->
	</form>
</div> <!-- div end card -->



<script>
    $(document).ready(function() {


        var formDisabled = $('#form-disabled').val();

        // init select2
        $('.select2').select2();
        $('#model-list-po').select2({
            templateResult: initSelectModel
        });

        // init wysiwig with purchase order values
        $('#additional-ordering-information-editor').html($('#additional-ordering-information').val());
        $('#additional-budget-information-editor').html($('#additional-budget-information').val());
        $('#supplier-contact-editor').html($('#supplier-contact').val());
        $('#delivery-address-editor').html($('#delivery-address').val());
        $('#comment-editor').html($('#comment').val());

        if (formDisabled) {
            // disabled wysiwig
            setTimeout(() => {
                $('#additional-ordering-information-editor').attr('contenteditable', false);
                $('#additional-budget-information-editor').attr('contenteditable', false);
                $('#supplier-contact-editor').attr('contenteditable', false);
                $('#comment-editor').attr('contenteditable', false);
            }, 500);
        }

        $('#model-list-po').change(function () {
            var paramsModel = $('#url-action').val();

            if ($('#model-list-po option:selected').val() !== "") {
                paramsModel +=  '&model=' + $('#model-list-po option:selected').val();
                window.location.href = paramsModel;
            }
        });

        $('#purchase-order-form').on('submit', function (evt) {
            evt.preventDefault();

            if (!canSubmit() ) {
                $(document).scrollTop(0);
                return false;
            }

            $('#additional-ordering-information').val($('#additional-ordering-information-editor').html().trim());
            $('#additional-budget-information').val($('#additional-budget-information-editor').html().trim());
            $('#supplier-contact').val($('#supplier-contact-editor').html().trim());
            $('#delivery-address').val($('#delivery-address-editor').html().trim());
            $('#comment').val($('#comment-editor').html().trim());

            evt.currentTarget.submit();
        });

        function initSelectModel(data) {
            if (!data.id) {
                return data.text;
            }

            var paramsView = $('#url-base').val();
            paramsView += '&id='+ data.id + '&preview-mode=1';
            var option = $('<span></span>');
            var preview = $('<a class="model-view" href="/'+ paramsView +'" title="Prévisualiser ce modèle" target="_blank"><i class="fa fa-eye"></i></a>');

            preview.on('mouseup', function (evt) {
                evt.stopPropagation();
            });

            option.text(data.text);
            option.append(preview);

            return option;
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
            if (!parseInt($('#budget-data').val())) {
                sendError("<?php echo T_('Le champ Données budgétaires est requis'); ?>");
                return false;
            }
            //permet de rendre le champs données budgétaire supplémentaire obligatoire uniquement pour un labo donné
            if(window.location.hostname.includes("imbe.fr") && !($('#additional-budget-information-editor').html()))
            {
                sendError("<?php echo T_('Le champ Informations de budget supplémentaires est requis'); ?>");
                return false;
            }
            if ($('#validators').val().length == 0) {
                sendError("<?php echo T_('Le champ Responsables de la ligne budgétaire est requis'); ?>");
                return false;
            }

            //on re-enable le champs valideur si il a été disabled (dans le cas on a utilisé les valideurs fixes)
            $('#validators').attr('disabled',false);


            return true;
        }
    });

    function check_size() {
        $('form').submit(function( e ) {
            if(!($('#quote')[0].files[0].size < 73400320 )) {
                //Prevent default and display error
                alert('Le fichier est joint est trop volumineux, la taille maximum est 70M');
                e.preventDefault();
            }
        });
    }
</script>
