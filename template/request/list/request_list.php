<div class="form-check mt-2 mb-4">
    <?php
    $selectValidatorNext = ($selectValidator) ? 0 : 1;
    ?>

    <div class="select-validator">
        <a href="<?php echo $urlAction.'&select-validator='.$selectValidatorNext ?>">
            <?php
            echo ($selectValidator)
                ?  T_('Sélectionner toutes les demandes')
                :  T_('Sélectionner mes demandes à valider');
            ?>
        </a>
    </div>
</div>

<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
    <div style="overflow-y: hidden;" class="table-responsive">
        <div class="col-xs-12">
            <table id="simple-table" class="table table-bordered table-bordered table-striped table-hover text-dark-m2 request-list">
                <thead class="text-dark-m3 bgc-grey-l4">
                    <tr class="bgc-white text-secondary-d3 text-95">
                        <th>
                            <center>
                                <a class="text-primary-m2" title="Numéro du ticket" href="<?php echo $urlAction . '&order-value=id&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-tag text-primary-m2"></i><br>
                                    <?php echo T_("Numéro") ?>
                                </a>
                            </center>
                        </th>

                        <th>
                            <center>
                                <a class="text-primary-m2" title="Type" href="<?php echo $urlAction . '&order-value=type&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-tag text-primary-m2"></i><br>
                                    <?php echo T_("Type") ?>
                                </a>
                            </center>
                        </th>

                        <th>
                            <center>
                                <a class="text-primary-m2" title="Titre" href="<?php echo $urlAction . '&order-value=title&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-tag text-primary-m2"></i><br>
                                    <?php echo T_("Titre") ?>
                                </a>
                            </center>
                        </th>

                        <th>
                            <center>
                                <a class="text-primary-m2" title="Demandeur" href="<?php echo $urlAction . '&order-value=lastname_owner&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-male text-primary-m2"></i><br>
                                    <?php echo T_("Demandeur") ?>
                                </a>
                            </center>
                        </th>

                        <th>
                            <center>
                                <a class="text-primary-m2" title="Valideur" href="<?php echo $urlAction . '&order-value=lastname_validator&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-male text-primary-m2"></i><br>
                                    <?php echo T_("Valideur") ?>
                                </a>
                            </center>
                        </th>

                        <th>
                            <center>
                                <a class="text-primary-m2" title="En État" href="<?php echo $urlAction . '&order-value=status&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-adjust text-primary-m2"></i><br>
                                    <?php echo T_("En État") ?>
                                </a>
                            </center>
                        </th>
<!--
                        <th>
                            <center>
                                <a class="text-primary-m2" title="Date depot" href="<?php echo $urlAction . '&order-value=status&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-adjust text-primary-m2"></i><br>
                                    <?php echo T_("Date de dépôt") ?>
                                </a>
                            </center>
			</th>
-->
<!--
                        <th>
                            <center>
                                <a class="text-primary-m2" title="Date alerte" href="<?php echo $urlAction . '&order-value=status&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-adjust text-primary-m2"></i><br>
                                    <?php echo T_("Date de dernière notification") ?>
                                </a>
                            </center>
			</th>
-->
                        <th>
                            <center>
                                <a class="text-primary-m2" title="Date depart" href="<?php echo $urlAction . '&order-value=status&order-direction='.$orderDirectionNext ?>">
                                    <i class="fa fa-adjust text-primary-m2"></i><br>
                                    <?php echo T_("Date de départ") ?>
                                </a>
                            </center>
                        </th>


                        <th>
                            <center>
                                <a class="text-primary-m2" title="État" href="">
                                    <i class="fa fa-adjust text-primary-m2"></i><br>
                                    <?php echo T_("Action") ?>
                                </a>
                            </center>
                        </th>
                    </tr>
                    <form name="filter" method="POST" action="<?php echo $urlAction ?>">
                        <tr class="bgc-white text-secondary-d3 text-95">
                            <td>
                                <center>
                                    <input class="form-control w-100" name="filter-id" onchange="submit();" type="text" style="width:80px" value="<?php echo $requestId ?>">
                                </center>
                            </td>

                            <td align="center">
                                <select class="form-control w-100" id="filter-type" name="filter-type" onchange="submit()">
                                    <option value=""></option>
                                    <option <?php echo ($requestType == 'purchase_order') ? 'selected' : ''; ?> value="purchase_order">
                                        <?php echo T_("Bon de Commande") ?>
                                    </option>
                                    <option <?php echo ($requestType == 'mission_order') ? 'selected' : ''; ?> value="mission_order">
                                        <?php echo T_("Order de Mission") ?>
                                    </option>
                                </select>
                            </td>

                            <td>
                                <center>
                                    <input class="form-control" name="filter-title" onchange="submit();" type="text" style="width:200px" value="<?php echo $requestTitle ?>">
                                </center>
                            </td>

                            <td align="center">
                                <select class="form-control select2 w-100" style="width:150px" id="filter-owner" name="filter-owner" onchange="submit()">
                                    <option value=""></option>
                                    <?php
                                    foreach ($users as $user) {
                                        ?>
                                        <option <?php echo ($user->getId() == $owner) ? 'selected' : '' ?> value="<?php echo $user->getId() ?>">
                                            <?php echo $user->getFullName() ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>

                            <td align="center">
                                <select class="form-control select2 w-100" style="width:150px" id="filter-validator" name="filter-validator" onchange="submit()">
                                    <option value=""></option>
                                    <?php
                                    foreach ($users as $user) {
                                        ?>
                                        <option <?php echo ($user->getId() == $validator) ? 'selected' : '' ?> value="<?php echo $user->getId() ?>">
                                            <?php echo $user->getFullName() ?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </td>

                            <td align="center">
                                <select class="form-control w-100" id="filter-status" name="filter-status" onchange="submit()">
                                    <option value=""></option>
                                    <option <?php echo ($status == \Models\Request\BaseRequest::STATUS_WAITING_VALIDATION) ? 'selected' : '' ?> value="<?php echo \Models\Request\BaseRequest::STATUS_WAITING_VALIDATION ?>">
                                        <?php echo T_("En attente de validation") ?>
                                    </option>
                                    <option <?php echo ($status == \Models\Request\BaseRequest::STATUS_MODIFY) ? 'selected' : '' ?> value="<?php echo \Models\Request\BaseRequest::STATUS_MODIFY ?>">
                                        <?php echo T_("En attente de modification") ?>
                                    </option>
                                    <option <?php echo ($status == \Models\Request\BaseRequest::STATUS_REJECT) ? 'selected' : '' ?> value="<?php echo \Models\Request\BaseRequest::STATUS_REJECT ?>">
                                        <?php echo T_("Refusé") ?>
                                    </option>
                                    <option <?php echo ($status == \Models\Request\BaseRequest::STATUS_VALID) ? 'selected' : '' ?> value="<?php echo \Models\Request\BaseRequest::STATUS_VALID ?>">
                                        <?php echo T_("Valide") ?>
                                    </option>
                                </select>
                            </td>

                            <td>
                                <center>
                                    <input class="form-control" name="filter-datedemande" onchange="submit();" type="text" style="width:200px" value="<?php echo $requestDateDemande ?>">
                                </center>
                            </td>

                            <td>
                                <center>
                                    <input class="form-control" name="filter-datealerte" onchange="submit();" type="text" style="width:200px" value="<?php echo $requestDateAlerte ?>">
                                </center>
                            </td>

                            <td align="center" style="width: 300px">
                            </td>
                        </tr>
                    </form>

                </thead>
                <tbody>
                    <form name="actionlist" method="POST"></form>
                        <?php
                        foreach ($requests['results'] as $request) {
                            $type = '';
                            $typeForm = '';
                            $status = '';

                            if ($request->isPurchaseOrder()) {
                                $typeForm = 'type-form=purchase-order';
                                $type = 'BdC';
                            }
                            if ($request->isMissionOrder()) {
                                $typeForm = 'type-form=mission-order';
                                $type = 'OM';
                            }

                            $viewRequest = 'index.php?page=request&id='.$request->getId().'&'.$typeForm.$urlParameters;

                            $fullUsername = '';
                            $truncatedUsername = '';
                            $user = null;

                            if (isset($users[$request->getOwner()->getId()])) {
                                $user = $users[$request->getOwner()->getId()];
                                $fullUsername = $user->getFullName();
                                $truncatedUsername = substr($user->getFirstName(), 0, 1).'. '.$user->getLastName();
                            }

                            switch ($request->getStatus()) {
                                case \Models\Request\BaseRequest::STATUS_WAITING_VALIDATION:
                                    $status = 'En attente de validation';
                                    break;
                                case \Models\Request\BaseRequest::STATUS_MODIFY:
                                    $status = 'A modifier';
                                    break;
                                case \Models\Request\BaseRequest::STATUS_REJECT:
                                    $status = 'Refusé';
                                    break;
                                case \Models\Request\BaseRequest::STATUS_VALID:
                                    $status = 'Validé';
                                    break;
                            }

                            ?>
                            <tr class="bgc-h-default-l3 d-style">
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $request->getId() ?>
                                        </a>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $type ?>
                                        </a>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $request->getTitle() ?>
                                        </a>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="<?php echo $fullUsername ?> " href="<?php echo $viewRequest ?>">
                                            <?php echo $truncatedUsername ?>
                                        </a>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center>
                                        <?php
                                        foreach ($request->getValidators() as $validator) {
                                            $fullUsername = $validator->getFullName();
                                            $truncatedUsername = substr($validator->getFirstName(), 0, 1).'. '.$validator->getLastName();
                                        ?>
                                            <a class="td" title="<?php echo $fullUsername ?> " href="<?php echo $viewRequest ?>">
                                                <span class="user-list">
                                                    <?php echo $truncatedUsername; ?>
                                                </span>
                                            </a>
                                        <?php
                                        }
                                        ?>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $status ?>
                                        </a>
                                    </center>
				</td>
<!--
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $request->getDate() ?>
                                        </a>
                                    </center>
				</td>
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                            <?php echo $request->getDateAlerte() ?>
                                        </a>
                                    </center>
				</td>
-->
                                <td onclick="document.location=''">
                                    <center>
                                        <a class="td" title="" href="<?php echo $viewRequest ?>">
                                        <?php 
			    // OC
                     if(!$request->isPurchaseOrder())
                        $newDate = $request->getDateStart()->format('d/m/Y');
                     echo $newDate; ?>
                                        </a>
                                    </center>
                                </td>
                                <td onclick="document.location=''">
                                    <center class="button-table">
                                        <?php
                                        if ($request->isPurchaseOrder()) {
                                            $type = 'purchase-order';
                                        } else if ($request->isMissionOrder()) {
                                            $type = 'mission-order';
                                        }

                                        if ($request->canValidate($_SESSION['user_id'])) {
                                            ?>
                                            <a class="td button-reject" title="" href="<?php echo $urlAction ?>&action=reject&action-id=<?php echo $request->getId() ?>&action-type=<?php echo $type ?>">
                                                <?php echo T_("Refuser") ?>
                                            </a>
                                            <a class="td button-modify" title="" href="<?php echo $urlAction ?>&action=modify&action-id=<?php echo $request->getId() ?>&action-type=<?php echo $type ?>">
                                                <?php echo T_("A modifier") ?>
                                            </a>
                                            <a class="td button-valid" title="" href="<?php echo $urlAction ?>&action=valid&action-id=<?php echo $request->getId() ?>&action-type=<?php echo $type ?>">
                                                <?php echo T_("Valider") ?>
                                            </a>
                                            <?php
                                        } else if ($request->getStatus() == \Models\Request\BaseRequest::STATUS_MODIFY) {
                                            ?>
                                            <span class="cannot-validate">
                                            <?php
                                            echo T_('En attente de modification par le demandeur');
                                            ?>
                                            </span>
                                            <?php
                                        } else {
                                            ?>
                                            <span class="cannot-validate">
                                            <?php
                                            echo T_('Vous ne pouvez pas valider cette demande');
                                            ?>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </center>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-4">
    <nav aria-label="Page navigation">
        <ul class="pagination nav-tabs-scroll is-scrollable mb-0">
            <?php
            if ($requestPage > 1) {
            ?>
                <li class="page-item">
                    <a class="page-link" title="<?php echo T_("Page précédente") ?>" href="<?php echo $urlAction ?>&request-page=<?php echo $requestPage-1 ?>">
                        <i class="fa fa-arrow-left"></i>
                    </a>
                </li>
            <?php
            }
            ?>
            <?php
            $first = true;
            $last = false;

            for ($i=1; $i <= $requests['count']; $i++) {
                $title = '';
                $range = $requestPage - $i;
                $concat = false;
                $urlParam = '&request-page='.$i;

                if ($i == $requests['count']) {
                    $last = true;
                }

                if (($range < -4 || $range > 4) && !$first && !$last) {
                    continue;
                }

                if (($range == -4 || $range == 4) && !$first && !$last) {
                    $urlParam = '';
                    $concat = true;
                }

                if ($first) {
                    $title = 'Première page';
                } else if ($last) {
                    $title = 'Dernière page';
                } else if($concat) {
                    $title = 'Page masquée';
                } else {
                    $title = 'Page '.$i;
                }

                if ($concat) {
                ?>
                    <li class="page-item">
                        <a class="page-link" title="<?php echo $title ?>" href="">
                            &nbsp;...
                        </a>
                    </li>
                <?php
                } else {
                ?>
                <li class="page-item <?php echo ($requestPage == $i) ? 'active' : '' ?>">
                    <a class="page-link" title="<?php echo $title ?>" href="<?php echo $urlAction.$urlParam ?>">
                        &nbsp;<?php echo $i ?>&nbsp;
                    </a>
                </li>
                <?php
                }

                $first = false;
            }
            ?>
            <?php
            if ($requests['count'] > 0 && $requestPage != $requests['count']) {
            ?>
                <li class="page-item">
                    <a class="page-link" title="<?php echo T_("Page suivante") ?>" href="<?php echo $urlAction ?>&request-page=<?php echo $requestPage+1 ?>">
                        <i class="fa fa-arrow-right"></i>
                    </a>
                </li>
            <?php
            }
            ?>
        </ul>
    </nav>
</div>

<div id="modify-comment">
    <div class="modify-title mb-3">
        <h2><?php echo T_("Commentaire") ?></h2>
        <i id="modify-close" class="fa fa-times"></i>
    </div>
    <form id="form-modify-comment" class="form-horizontal" enctype="multipart/form-data" method="post" action="">
        <div class="form-group row col-12">
            <textarea class="col-12" name="action-comment"></textarea>
        </div>
        <button type="submit" class="btn btn-secondary btn-success">
            <i class="fa fa-save"></i>
            <?php echo T_("Envoyer") ?>
        </button>
    </form>
</div>

<input type="hidden" id="url-action" name="url-action" value="<?php echo $urlAction; ?>">
<input type="hidden" id="open-modify-id" name="action-id" value="<?php echo isset($_GET['open-modify-id']) ? $_GET['open-modify-id'] : ''; ?>">
<input type="hidden" id="open-modify-type" name="action-type" value="<?php echo isset($_GET['open-modify-type']) ? $_GET['open-modify-type'] : ''; ?>">

<script>
    $(document).ready(function () {
        $('#owner').click(function () {
            window.location.href = $(this).attr('data-url');
        });
        $('.button-modify').click(function (e) {
            e.preventDefault();
            openModalComment($(this).attr("href"));
            return false;
        });
        $('#modify-close').click(function () {
            $("#modify-comment" ).dialog("close");
        });
        $('.select2').select2();

        // trick to display the arrow like other select
        $('b[role="presentation"]').hide();
        $('.select2-selection__arrow').append('<i class="fa fa-angle-down"></i>');

        let url = new URL(window.location);

        // remove actions parameters for preventing refresh page (refused, valid and modify)
        let params = new URLSearchParams(url.search);
        params.delete('action');
        params.delete('action-id');
        params.delete('action-type');
        window.history.pushState({}, document.title, url.origin + url.pathname + '?' + params.toString());

        if ($('#open-modify-id').val() && $('#open-modify-type').val()) {
            const searchParams = new URLSearchParams({
                'action': 'modify',
                'action-id': $('#open-modify-id').val(),
                'action-type': $('#open-modify-type').val()
            });
            openModalComment($('#url-action').val() + '&' + searchParams.toString());
        }

        function openModalComment(urlAction) {
            $('#form-modify-comment').attr('action', urlAction)
            $("#modify-comment" ).dialog({
                draggable: false,
                modal: true,
                height: 300,
                width: 600
            });
            $(".ui-dialog-titlebar").hide();
        }
    });
</script>
