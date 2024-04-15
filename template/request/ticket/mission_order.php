<?php
use Models\Request\MissionOrder\MissionOrder;
?>

<h1 style="text-align: center; font-size: 20px;">
    <?php echo T_('RECAPITULATIF DE LA DEMANDE') ?>
</h1>
<h2 style="text-align: center; margin: 0px;">
    <?php echo $request->getOwner()->getFullName();?><br><br>
    <?php echo $request->getTitle() ?><br><i>
<?php
switch($request->getMissionType()) {
  case 0:
	  echo "";
	  break;
  case 1:
	  echo "Colloque";
	  break;
  case 2:
	  echo "Recherche en équipe / Collaboration";
	  break;
  case 3:
	  echo "Mission";
	  break;
  case 4:
	  echo "Service central";
	  break;
  case 5:
	  echo "Visite contact pour projet";
	  break;
  case 6:
	  echo "Acquisition de nouvelles compétences";
	  break;
  case 7:
	  echo "Administration de la recherche";
	  break;
  case 8:
	  echo "Enseignement dispensé";
	  break;
  case 9:
	  echo "Formation";
	  break;
  case 10:
	  echo "Recherche documentaire sur le terrain";
	  break;
}
?>
</i>
</h2>
<br>

<div class="display: block; clear: both; width: 100%">
    <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
        <?php echo T_('PRISE EN CHARGE') ?>
    </h2>
</div>

<?php
$firstMissionRequest = $request->isFirstMissionRequest() ? T_('Oui') : T_('Non');
$omForGuest = $request->isOmForGuest() ? T_('Oui') : T_('Non');
$collectiveMission = $request->isCollectiveMission() ? T_('Oui') : T_('Non');
$typeMission = '';

switch ($request->getTypeMission()) {
    case MissionOrder::TYPE_MISSION_WITH_FEES:
        $typeMission = T_('Avec frais');
        break;
    case MissionOrder::TYPE_MISSION_WITHOUT_FEES:
        $typeMission = T_('Sans frais');
        break;
    case MissionOrder::TYPE_MISSION_STANDING_MISSION_ORDER:
        $typeMission = T_('Ordre de mission permanent');
        break;
}

$rows = [];
$rows[] = [
    "title" => 'Equipe/Service',
    "value" => $request->getService()->getName()
];
$rows[] = [
    "title" => 'Première demande de mission',
    "value" => $firstMissionRequest
];
$rows[] = [
    "title" => 'OM pour un invité',
    "value" => $omForGuest
];
/*
$rows[] = [
    "title" => 'Mission collective',
    "value" => $collectiveMission
];
if ($request->isCollectiveMission()) {
    $rows[] = [
        "title" => 'Liste des personnes concernées pour la mission',
        "value" => $request->getListPeopleInvolvedAssignment()
    ];
}
//*/
$rows[] = [
    "title" => 'Type de mission',
    "value" => $typeMission
];
if ($request->getTypeMission() == MissionOrder::TYPE_MISSION_WITH_FEES) {
    $rows[] = [
        "title" => 'Données budgétaires',
        "value" => $request->getBudgetData()->getName()
    ];
    if ($request->getAdditionalBudgetInformation()) {
        $rows[] = [
            "title" => 'Informations de budget supplémentaires',
            "value" => $request->getAdditionalBudgetInformation()
        ];
    }
}
if ($request->getTypeMission() == MissionOrder::TYPE_MISSION_WITHOUT_FEES) {
    $rows[] = [
        "title" => 'Tutelle',
        "value" => $request->getGuardianShip()
    ];
    $rows[] = [
        "title" => 'Organisme de prise en charge',
        "value" => $request->getCareOrganization()
    ];
}
if ($request->getTypeMission() == MissionOrder::TYPE_MISSION_STANDING_MISSION_ORDER) {
    $rows[] = [
        "title" => 'Tutelle',
        "value" => $request->getGuardianShip()
    ];
}
$rows[] = [
    "title" => 'Valideurs',
    "value" => $request->getValidatorHasString()
];
$rows[] = [
    "title" => 'Motif de la mission',
    "value" => $request->getReasonForMission()
];

$rows[] = [
    "title" => 'Montant Max',
    "value" => $request->getAmountMax()
];

$rows[] = [
    "title" => 'Montant Estimé',
    "value" => $request->getEstimatedAmount()
];

$rows[] = [
    "title" => 'Montant réalisé',
    "value" => $request->getRealAmount()
];
?>

<table style="table-layout:fixed; width: 100%">
    <?php
    $td = 0;
    foreach ($rows as $row) {
        if ($td % 2 == 0) {
        ?>
        <tr>
        <?php
        }
        ?>
        <td><b><?php echo T_($row['title']) ?> :</b> <?php echo $row['value'] ?></td>
        <?php
        $td++;
        if ($td % 2 == 0) {
        ?>
        </tr>
        <?php
        }
    }
    ?>
</table>


<?php 
if($request->getGuestName()!=NULL){
    echo '<div class="display: block; clear: both; width: 100%">
        <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">';
    echo T_('COORDONEES DE L\'INVITE');
    echo "</h2>
    </div>";

    $rows = [];
    $rows[] = [
        "title" => 'Nom de l\'invité',
        "value" => $request->getGuestName()
    ];
    $rows[] = [
        "title" => 'Date de naissance de l\'invité',
        "value" => $request->getGuestBirthDate()->format('d/m/Y')
    ];
    $rows[] = [
        "title" => 'Mail de l\'invité',
        "value" => $request->getGuestMail()
    ];
    $rows[] = [
        "title" => 'Labo de l\'invité',
        "value" => $request->getGuestLabo()
    ];
    $rows[] = [
        "title" => 'Pays de l\'invité',
        "value" => $request->getGuestCountry()
    ];

    echo '<table style="table-layout:fixed; width: 100%">';
    
    $td = 0;
    foreach ($rows as $row) {
        if ($td % 2 == 0) {
            echo "<tr>";
        }
        echo "<td><b>".T_($row['title'])." :</b> ".$row['value'] ."</td>";
        $td++;
        if ($td % 2 == 0) {            
            echo '</tr>';
        }
    }

    echo '</table>';
} 


?>


<div class="display: block; clear: both; width: 100%">
    <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
        <?php echo T_('RENSEIGNEMENTS CONCERNANT LA MISSION') ?>
    </h2>
</div>

<?php
$privateStay = $request->getPrivateStay()->getIsPrivateStay() ? 'Oui' : 'Non';
$placeReturn = $request->isPlaceReturnDifferent()
    ? $request->getPlaceReturn()
    : $request->getPlaceStart();

$rows = [];
$rows[] = [
    'title' => 'Date et heure de départ',
    'value' => $request->getDateStart()->format('d/m/Y à H:i')
];
$rows[] = [
    'title' => 'Lieu de départ',
    'value' => $request->getPlaceStart()
];
$rows[] = [
    'title' => 'Date et heure de retour',
    'value' => $request->getDateReturn()->format('d/m/Y à H:i')
];
$rows[] = [
    'title' => 'Lieu de retour',
    'value' => $placeReturn
];
$rows[] = [
    'title' => 'Ville de séjour',
    'value' => $request->getCityStay()
];
$rows[] = [
    'title' => 'Pays de séjour',
    'value' => $request->getCountryStay()
];
$rows[] = [
    'title' => 'Séjour privé',
    'value' => $privateStay
];
if ($request->getPrivateStay()->getIsPrivateStay()) {
    $rows[] = [
        'title' => 'Date de début du séjour privé',
        'value' => $request->getPrivateStay()->getDateBegin('d/m/Y')
    ];
    $rows[] = [
        'title' => 'Date de fin du séjour privé',
        'value' => $request->getPrivateStay()->getDateEnd('d/m/Y')
    ];
    $rows[] = [
        'title' => 'Lieu de séjour',
        'value' => $request->getPrivateStay()->getPlace()
    ];
}
if ($request->getComment()) {
    $rows[] = [
        'title' => 'Commentaire',
        'value' => $request->getComment()
    ];
}
?>
<table style="table-layout:fixed; width: 100%">
    <?php
    $td = 0;
    foreach ($rows as $row) {
        if ($td % 2 == 0) {
            ?>
            <tr>
            <?php
        }
        ?>
        <td><b><?php echo T_($row['title']) ?> :</b> <?php echo $row['value'] ?></td>
        <?php
        $td++;
        if ($td % 2 == 0) {
            ?>
            </tr>
            <?php
        }
    }
    ?>
</table>

<div class="display: block; clear: both; width: 100%">
    <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
        <?php echo T_('VOYAGE') ?>
    </h2>
</div>

<?php
$offMarketAccomodation = $request->isOffMarketAccomodation() ? 'Oui' : 'Non';
$transportMarket = $request->isTransportMarket() ? 'Oui' : 'Non';
$advanceRequest = $request->isadvanceRequest() ? 'Oui' : 'Non';

$rows = [];
$rows[] = [
    'title' => 'Prise en charge de l\'hébergement',
    'value' => $offMarketAccomodation
];
$rows[] = [
    'title' => 'Demande d’avance',
    'value' => $advanceRequest
];
$rows[] = [
    'title' => 'Transport sur le marché',
    'value' => $transportMarket
];
if (!$request->isTransportMarket()) {
    $rows[] = [
        'title' => 'Justification',
        'value' => $request->getTransportMarketJustification()
    ];
}
?>
<table style="table-layout:fixed; width: 100%">
    <?php
    $td = 0;
    foreach ($rows as $row) {
        if ($td % 2 == 0) {
            ?>
            <tr>
            <?php
        }
        ?>
        <td><b><?php echo T_($row['title']) ?> :</b> <?php echo $row['value'] ?></td>
        <?php
        $td++;
        if ($td % 2 == 0) {
            ?>
            </tr>
            <?php
        }
    }
    ?>
</table>

<div class="display: block; clear: both; width: 100%">
    <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
        <?php echo T_('MOYEN DE TRANSPORT') ?>
    </h2>
</div>

<?php
$rows = [];
$rows[] = [
    'title' => 'Moyens de transport',
    'value' => $request->getTransportChoicesHasString()
];
if ($request->getPersonalVehicle()->getNumberplate()) {
    $rows[] = [
        'title' => 'Véhicule personnel - Plaque d\'immatriculation',
        'value' => $request->getPersonalVehicle()->getNumberplate()
    ];
    $rows[] = [
        'title' => 'Nombre de chevaux',
        'value' => $request->getPersonalVehicle()->getHorsepower()
    ];
    $rows[] = [
        'title' => 'Véhicule personnel - Kilométrage',
        'value' => $request->getPersonalVehicle()->getTripMileage()
    ];
    $rows[] = [
        'title' => 'Véhicule personnel - Passagers',
        'value' => $request->getPersonalVehicle()->getPassengersHasString()
    ];
}
if ($request->getAdministrativeVehicle()->getId()) {
    $rows[] = [
        'title' => 'Véhicule administratif',
        'value' => $request->getAdministrativeVehicle()->getName().' - '.$request->getAdministrativeVehicle()->getNumberplate()
    ];
}
if ($request->getOtherFees()) {
    $rows[] = [
        'title' => 'Autre frais',
        'value' => $request->getOtherFees()
    ];
}
?>

<table style="table-layout:fixed; width: 100%">
    <?php
    $td = 0;
    foreach ($rows as $row) {
        if ($td % 2 == 0) {
            ?>
            <tr>
            <?php
        }
        ?>
        <td><b><?php echo T_($row['title']) ?> :</b> <?php echo $row['value'] ?></td>
        <?php
        $td++;
        if ($td % 2 == 0) {
            ?>
            </tr>
            <?php
        }
    }
    ?>
</table>

<?php
if ($request->getColloquiums()->getIsColloquiums()) {
?>
    <div class="display: block; clear: both; width: 100%">
        <h2 style="background-color: #00c8f561; color: #000000; text-align: center; font-size: 15px; padding: 5px; width: 100%">
            <?php echo T_('COLLOQUES') ?>
        </h2>
    </div>

    <?php
    $purchasingCard = ($request->getColloquiums()->isPurchasingCard()) ? 'Oui': 'Non';

    $rows = [];
    $rows[] = [
        'title' => 'Frais d\'inscription',
        'value' => $request->getColloquiums()->getRegistrationFees()
    ];
    $rows[] = [
        'title' => 'Carte achat',
        'value' => $purchasingCard
    ];
    ?>
    <table style="table-layout:fixed; width: 100%">
        <?php
        $td = 0;
        foreach ($rows as $row) {
            if ($td % 2 == 0) {
                ?>
                <tr>
                <?php
            }
            ?>
            <td><b><?php echo T_($row['title']) ?> :</b> <?php echo $row['value'] ?></td>
            <?php
            $td++;
            if ($td % 2 == 0) {
                ?>
                </tr>
                <?php
            }
        }
        ?>
    </table>
<?php
}
?>
