<?php
$purchaseCard = ($request->isPurchaseCard()) ? T_('Oui') : T_('Non');
?>

<h1 style="text-align: center; font-size: 20px;">
    <?php echo T_('DEMANDE DE BON DE COMMANDE'); ?>
</h1>
<h2 style="text-align: center; margin: 0px;">
    <?php echo $request->getOwner()->getFullName();?><br><br>
    <?php echo $request->getTitle(); ?>
</h2>
<br>

<div class="display: block; clear: both; width: 100%">
    <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
        <?php echo T_('PRISE EN CHARGE') ?>
    </h2>
</div>

<table style="table-layout:fixed; width: 100%">
    <tr>
        <td style="vertical-align: top;">
            <b><?php echo T_('Equipe/Service') ?> :</b> <?php echo $request->getService()->getName() ?>
        </td>
        <td style="vertical-align: top;">
            <b><?php echo T_('Informations de commande supplémentaires') ?> :</b> <?php echo $request->getAdditionalOrderingInformation() ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <b><?php echo T_('Données budgétaires') ?> :</b> <?php echo $request->getBudgetData()->getName() ?>
        </td>
        <td style="vertical-align: top;">
            <b><?php echo T_('Informations de budget supplémentaires') ?> :</b> <?php echo $request->getAdditionalBudgetInformation() ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <b><?php echo T_('Valideurs') ?> :</b> <?php echo $request->getValidatorAsString() ?>
        </td>
        <td style="vertical-align: top;">
            <b><?php echo T_('Contact du fournisseur') ?> :</b> <?php echo $request->getSupplierContact() ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <b><?php echo T_('Carte achat') ?> :</b> <?php echo $purchaseCard ?>
        </td>
        <td style="vertical-align: top;">
            <b><?php echo T_('Adresse de livraison') ?> :</b> <?php echo $request->getDeliveryAddress() ?>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;">
            <b><?php echo T_('Code Nacre') ?> :</b> <?php echo $request->getCodeNacreHasString() ?>
        </td>
        <td style="vertical-align: top;">
            <b><?php echo T_('Commentaire') ?> :</b> <?php echo $request->getComment() ?>
        </td>
    </tr>
</table>
