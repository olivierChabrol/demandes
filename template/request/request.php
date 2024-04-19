<?php
$editPurchaseOrder = false;
$editMissionOrder = false;
$canShowRequestSelect = false;
$urlAction = 'index.php?page=request';

/*  */
if($ruser['default_ticket_state']!='')
{
    if($ruser['default_ticket_state']=='meta_all')
    {
        $urlTicket='&amp;action=new&amp;userid=%25&amp;state=meta&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
    } else {
        $urlTicket='&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$ruser['default_ticket_state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
    }
} else {
    $urlTicket='&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
    $urlTicket='&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
}


if ($purchaseOrder->getId()) {
    $editPurchaseOrder = true;
    $purchaseOrderSelected = true;
}
if ($missionOrder->getId()) {
    $editMissionOrder = true;
    $missionOrderSelected = true;
}

if (
    ($editPurchaseOrder && $purchaseOrder->getIsModel()) ||
    ($editMissionOrder && $missionOrder->getIsModel()) ||
    (!$editPurchaseOrder && !$editMissionOrder)
) {
    $canShowRequestSelect = true;
}
?>

<div class="request">
    <?php
    if ($canShowRequestSelect) {
        ?>
        <div class="card bcard shadow mt-2 request-select" draggable="false">
            <div class="card-header">
                <h5 class="card-title">
                    <i class="fa fa-ticket-alt"></i>
                    <?php echo T_('Nouvelle demande'); ?>
                </h5>
            </div>
            <div class="card-body">
		<div class="radio-group">
<b><a href="https://www.i2m.univ-amu.fr/missions-et-invitations/" target="other">Avez vous lu toutes les informations concernant les missions ?</a></b>
                </div>
                <div class="radio-group">
                    <!--<div class="radio-element p-2">
                        <input <?php //echo $purchaseOrderSelected ? 'checked' : ''  ?>
                                data-link="<?php //echo $urlAction.'&type-form=purchase-order' ?>"
                                type="radio" id="purchase-order"
                                name="purchase-order" value="purchase-order"
                        >
                        <label for="purchase-order"><?php //echo T_('Bon de commande'); ?></label>
                    </div>
                    <div class="radio-element p-2">
                        <input <?php //echo $missionOrderSelected ? 'checked' : ''  ?>
                                data-link="<?php //echo $urlAction.'&type-form=mission-order' ?>"
                                type="radio" id="mission-order"
                                name="mission-order" value="mission-order"
                        >
                        <label for="mission-order"><?php //echo T_('Ordre de mission'); ?></label>
                    </div>

                    <div class="radio-element p-2">
                        <input <?php //echo $ticketSelected ? 'checked' : ''  ?>
                                data-link="<?php //echo $urlAction.'&type-form=ticket'.$urlTicket ?>"
                                type="radio" id="ticket"
                                name="ticket" value="ticket">
                        <label for="ticket"><?php //echo T_('Autre demande'); ?></label>
                    </div>
                    -->
<!--
                    <div class="button-element p-2">
                        <button class='btn btn-primary <?php echo $purchaseOrderSelected ? 'active' : ''  ?>' id="purchase-order" name="purchase-order" data-link="<?php echo $urlAction.'&type-form=purchase-order' ?>"><?php echo T_('Bon de commande'); ?></button>
		    </div>
                    <div class="button-element p-2">
                        <button class='btn btn-primary <?php echo $purchaseOrderSelected ? 'active' : ''  ?>' id="purchase-order" name="purchase-order" data-link="<?php echo $urlAction.'&type-form=purchase-order-end' ?>"><?php echo T_('Service fait'); ?></button>
		    </div>
-->
                    <div class="button-element p-2">
                        <button class='btn btn-primary <?php echo $missionOrderSelected ? 'active' : ''  ?>' id="mission-order" name="mission-order" data-link="<?php echo $urlAction.'&type-form=mission-order' ?>"><?php echo T_('Départ en mission d’un membre de l\'I2M'); ?></button>
		    </div>
                    <div class="button-element p-2">
                        <button class='btn btn-primary <?php echo $missionOrderSelected ? 'active' : ''  ?>' id="mission-order-invitation" name="mission-order-invitation" data-link="<?php echo $urlAction.'&type-form=mission-order&invite=1' ?>"><?php echo T_('Invitation d’une personnalité extérieure'); ?></button>
		    </div>
<!--
                    <div class="button-element p-2">
                        <button class='btn btn-primary <?php echo $missionOrderSelected ? 'active' : ''  ?>' id="mission-order" name="mission-order" data-link="<?php echo $urlAction.'&type-form=mission-order-end' ?>"><?php echo T_('Liquidation de Mission'); ?></button>
		    </div>
-->
                    <div class="button-element p-2">
                         <button class='btn btn-primary <?php echo $purchaseOrderSelected ? 'active' : ''  ?>' id="purchase-order" name="purchase-order" data-link="<?php echo $urlAction.'&type-form=purchase-order'.$urlTicket ?>"><?php echo T_('Demande d’achat diverse pour les membres de l\'I2M'); ?></button>
<!--                        <button class='btn btn-primary --><?php //echo $ticketSelected ? '' : ''  ?><!--' id="ticket" name="ticket" data-link="#">--><?php //echo T_('Demande d’achat diverse pour les membres de l\'I2M'); ?><!--</button>-->
		            </div>
		</div>
            </div>
        </div>
        <?php
    }
    ?>

    <?php
    if ($purchaseOrderSelected) {
        include("template/request/purchase_order.php");
    }
    if ($missionOrderSelected) {
        include("template/request/mission_order.php");
    }
    if ($ticketSelected) {
        include("ticket.php");
    }
    ?>
</div>

<script>
    $(document).ready(function() {
        $(".purchase-order-group").show("slow");
        $(".mission-order-group").show("slow");
        $(".ticket-group").show("slow");

       /* $(".radio-element").on('click', function() {
            $(this).siblings().children("input[type='radio']").prop("checked", false);
            $(this).children("input[type='radio']").prop("checked", true);

            window.location.href = $(this).children("input[type='radio']").attr('data-link');
        });*/

        $(".button-element").on('click', function() {
            $(this).siblings().children("button").removeClass("active");
            $(this).children("button").addClass("active");

            window.location.href = $(this).children("button").attr('data-link');
        });
    });
</script>
