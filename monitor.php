<?php
################################################################################
# @Name : monitor.php
# @Description : display new ticket current ticket for monitoring screen
# @Call : /stat.php
# @Parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 11/06/2020
# @Version : 3.2.2
################################################################################

//initialize variables 
require('core/init_get.php');

//connexion script with database parameters
require "connect.php";

//get userid to find language
if(!$_GET['user_id']) {$_GET['user_id']=1;}
$_SESSION['user_id']=$_GET['user_id'];

//get key
$qry=$db->prepare("SELECT `server_private_key` FROM `tparameters`");
$qry->execute();
$key=$qry->fetch();
$qry->closeCursor();

$_GET['key']=str_replace(' ','+',$_GET['key']);

if($_GET['key']==$key['server_private_key'])
{
	//load user table
	$qry=$db->prepare("SELECT `language` FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_SESSION['user_id']));
	$ruser=$qry->fetch();
	$qry->closeCursor();

	//define current language
	require "localization.php";

	//switch SQL MODE to allow empty values with latest version of MySQL
	$db->exec('SET sql_mode = ""');

	//get current date
	$daydate=date('Y-m-d');

	//query today open ticket
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE date_create LIKE :date AND disable='0'");
	$qry->execute(array('date' => "$daydate%"));
	$nbday=$qry->fetch();
	$qry->closeCursor();

	//query new ticket not associate to technician
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE technician='0' AND t_group='0' AND disable='0'");
	$qry->execute();
	$cnt5=$qry->fetch();
	$qry->closeCursor();

	//query today resolve ticket
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE date_res LIKE :date AND state='3' AND disable='0'");
	$qry->execute(array('date' => "$daydate%"));
	$nbdayres=$qry->fetch();
	$qry->closeCursor();

	//query all open ticket 
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE (state='5' OR state='1' OR state='2' OR state='6') AND disable='0'");
	$qry->execute();
	$nbopen=$qry->fetch();
	$qry->closeCursor();

	//query all to do ticket
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE (state='1' OR state='2' OR state='6') AND disable='0'");
	$qry->execute();
	$nbtodo=$qry->fetch();
	$qry->closeCursor();

	//query all critical ticket
	$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tcriticality` WHERE tincidents.criticality=tcriticality.id AND (tincidents.state='5' OR tincidents.state='1' OR tincidents.state='2' OR tincidents.state='6') AND tcriticality.name LIKE 'Critique' AND disable='0'");
	$qry->execute();
	$nbcritical=$qry->fetch();
	$qry->closeCursor();

	//query ticket wait user state
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE state='6' AND disable='0'");
	$qry->execute();
	$nbwaituser=$qry->fetch();
	$qry->closeCursor();

	session_start();
	//initialize variables
	if(!isset($_SESSION['current_ticket'])) $_SESSION['current_ticket'] = '';

	//launch audio notification for new ticket
	if($_SESSION['current_ticket']<$cnt5[0]) {echo'<audio hidden="false" autoplay="true" src="./sounds/notify.ogg" controls="controls"></audio>';}

	//update current counter
	if($_SESSION['current_ticket']!=$cnt5[0]) {$_SESSION['current_ticket']=$cnt5[0];}

	echo '
	<!DOCTYPE html>
		<html lang="fr">
		<head>
			<meta charset="UTF-8" />
			<title>GestSup | '.T_('Moniteur').'</title>
			<link rel="shortcut icon" type="image/png" href="./images/favicon_ticket.png" />
			<meta name="description" content="gestsup" />
			<meta name="robots" content="noindex, nofollow">
			<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
			<!-- bootstrap styles -->
			<link rel="stylesheet" href="./components/bootstrap/dist/css/bootstrap.min.css" />
			<!-- fontawesome styles -->
			<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/fontawesome.min.css">
			<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/solid.min.css">
			<!-- ace styles -->
			<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-font.min.css">
			<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace.min.css">
			<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-themes.min.css">
			<meta http-equiv="Refresh" content="60">
		</head>
		<body class="m-3">
			';
			//generate color
			if($cnt5[0]>0) $color='danger'; else $color='success';
			
			//add pluriel
			if($cnt5[0]>1) $new=T_('Nouveaux'); else $new=T_('Nouveau');
			if($cnt5[0]>1) $ticket=T_('tickets'); else $ticket=T_('ticket');
			if($nbday[0]>1) $open=T_('Ouverts'); else $open=T_('Ouvert');
			if($nbdayres[0]>1) $res=T_('Résolus'); else $res=T_('Résolu');
			
			echo '
			<a href="#" class="btn btn-'.$color.' btn-app radius-4 mr-2 mr-2">
				'.$new.'<br />'.$ticket.'
				<div class="mt-4"></div>
				<i class="fa fa-ticket-alt text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$cnt5[0].'</div>
				<div class="mt-4"></div>
			</a>
			';
			
			if($nbcritical[0]>0)
			{
				echo '
				<a href="#" class="btn btn-warning btn-app radius-4 mr-2">
					'.T_('Ouverts').'<br />'.T_('critique').'
					<div class="mt-4"></div>
					<i class="fa fa-exclamation-triangle text-200"></i>
					<div class="mt-4"></div>
					<div class="text-180">'.$nbcritical[0].'</div>
					<div class="mt-4"></div>
				</a>
				';
			}
			echo '
			<a href="#" class="btn btn-primary btn-app radius-4 mr-2">
				'.$open.'<br />'.T_('du jour').'
				<div class="mt-4"></div>
				<i class="fa fa-calendar text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$nbday[0].'</div>
				<div class="mt-4"></div>
			</a>
			<a href="#" class="btn btn-purple btn-app radius-4 mr-2">
				'.$res.'<br />'.T_('du jour').'
				<div class="mt-4"></div>
				<i class="fa fa-calendar text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$nbdayres[0].'</div>
				<div class="mt-4"></div>
			</a>
			<a href="#" class="btn btn-grey btn-app radius-4 mr-2">
				'.T_('Tous les <br />ouverts').'
				<div class="mt-4"></div>
				<i class="fa fa-plus text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$nbopen[0].'</div>
				<div class="mt-4"></div>
			</a>
			<a href="#" class="btn btn-info btn-app radius-4 mr-2">
				'.T_('Tickets').' <br />'.T_('à traiter').'
				<div class="mt-4"></div>
				<i class="fa fa-check text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$nbtodo[0].'</div>
				<div class="mt-4"></div>
			</a>
			<a href="#" class="btn btn-pink btn-app radius-4 mr-2">
				'.T_('Attente').'<br />'.T_('retour').'
				<div class="mt-4"></div>
				<i class="fa fa-reply text-200"></i>
				<div class="mt-4"></div>
				<div class="text-180">'.$nbwaituser[0].'</div>
				<div class="mt-4"></div>
			</a>
		</body>
	</html>
	';
} else {
	echo '<br /><br /><center><div style="color:red;">Cette page a été déplacée, utiliser le lien présent dans Administration > Paramètres > Général "Écran de supervision"</div></center>';
}

//close database access
$db = null;
?>