<div>
    <?php echo T_('Bonjour')?>,
    <br><br>
    <?php
    if ($request->isPurchaseOrder()) {
        echo T_('Votre Bon de commande a été validé');
    }
    ?>
    <?php
    if ($request->isMissionOrder()) {
        echo T_('Votre Ordre de Mission a été validé');
    }
    ?>.
    <br><br>
    <?php
    if ($request->isPurchaseOrder()) {
        $typeForm = "purchase-order";
    } else if ($request->isMissionOrder()) {
        $typeForm = "mission-order";
    }
    ?>
    <a href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?page=ticket&id=<?php echo $request->getTicket()->getId(); ?>"><?php echo T_('Vous pouvez consulter votre demande ici'); ?></a>
</div>

<?php
if ($request->isMissionOrder()) {
    include("template/request/ticket/mission_order.php");
} else if ($request->isPurchaseOrder()) {
    include('template/request/ticket/purchase_order.php');
}
?>