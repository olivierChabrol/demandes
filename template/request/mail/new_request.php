<style>
    <?php include 'template/assets/css/request.css'; ?>
</style>

<div>
    <?php echo T_('Bonjour')?>,
    <br><br>
    <?php
    if ($request->isPurchaseOrder()) {
        echo T_('Un nouveau Bon de commande vous a été attribué');
    }
    ?>
    <?php
    if ($request->isMissionOrder()) {
        echo T_('Un nouvel Ordre de Mission vous a été attribué');
    }
    ?>.<br>
    <?php
    if ($request->isPurchaseOrder()) {
        $typeForm = "purchase-order";
    } else if ($request->isMissionOrder()) {
        $typeForm = "mission-order";
    }
    ?>
    <div class="button-mail">
<?php
if ($request->isMissionOrder()) {
    include("template/request/ticket/mission_order.php");
} else if ($request->isPurchaseOrder()) {
    include('template/request/ticket/purchase_order.php');
}
?>

        <a class="button-reject" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?page=request_list&action=reject&action-id=<?php echo $request->getId(); ?>&action-type=<?php echo $typeForm; ?>">
            <?php echo T_('Refuser') ?>
        </a>
        <br>
        <?php
        $type = '';
        if ($request->isPurchaseOrder()) {
            $type = 'purchase-order';
        } else if ($request->isMissionOrder()) {
            $type = 'mission-order';
        }
        ?>
        <a class="button-modify" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?page=request_list&filter-status=1&request-page=1&select-validator=1&open-modify-id=<?php echo $request->getId(); ?>&open-modify-type=<?php echo $type ?>">
            <?php echo T_('Modifer') ?>
        </a>
        <br>
        <a class="button-valid" href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?page=request_list&action=valid&action-id=<?php echo $request->getId(); ?>&action-type=<?php echo $typeForm; ?>">
            <?php echo T_('Valider') ?>
        </a>
    </div>
    <br>
    <a href="https://<?php echo $_SERVER['SERVER_NAME'] ?>/index.php?page=request&id=<?php echo $request->getId(); ?>&type-form=<?php echo $typeForm; ?>"><?php echo T_('Vous pouvez consulter votre demande ici'); ?></a>

</div>

