<?php
################################################################################
# @Name : ticket_print.php
# @Description : page to print ticket
# @Call : /ticket.php
# @Author : Flox
# @Version : 3.2.3
# @Create : 09/02/2014
# @Update : 08/07/2020
################################################################################

require_once("core/init_get.php");
require_once("core/functions.php");

//initialize variables
if(!isset($_COOKIE["token"])) $_COOKIE["token"] = '';
if(!isset($resolution)) $resolution = '';
//connexion script with database parameters
require "connect.php";

//switch SQL MODE to allow empty values with latest version of MySQL
$db->exec('SET sql_mode = ""');

//get userid to find language
$_SESSION['user_id']=$_GET['user_id'];

$db_id=strip_tags($db->quote($_GET['id']));
$db_session_user_id=strip_tags($_GET['user_id']);

//load user table
$qry=$db->prepare("SELECT * FROM tusers WHERE id=:id");
$qry->execute(array('id' => $_GET['user_id']));
$ruser=$qry->fetch();
$qry->closeCursor();

//load parameter table
$qry=$db->prepare("SELECT * FROM tparameters");
$qry->execute();
$rparameters=$qry->fetch();
$qry->closeCursor();

//define current language
require "localization.php";

//initialize variables
if(!isset($userreg)) $userreg = '';
if(!isset($u_group)) $u_group = '';
if(!isset($globalrow['u_group'])) $globalrow['u_group'] = '';
if(!isset($_POST['user'])) $_POST['user'] = '';
if(!isset($_POST['technician'])) $_POST['technician'] = '';
if(!isset($rtechgroup4['name'])) $rtechgroup4['name'] = '';
if(!isset($rtech5['firstname'])) $rtech5['firstname'] = '';
if(!isset($rtech5['lastname'])) $rtech5['lastname'] = '';

//master query
$qry=$db->prepare("SELECT * FROM `tincidents` WHERE id=:id");
$qry->execute(array('id' => $_GET['id']));
$globalrow=$qry->fetch();
$qry->closeCursor();

//secure connect
if($_GET['token'] && $_GET['token']==$_COOKIE["token"])
{
	//database queries to find values for create print
	$qry=$db->prepare("SELECT * FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['user']));
	$userrow=$qry->fetch();
	$qry->closeCursor();

	$qry=$db->prepare("SELECT * FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['technician']));
	$techrow=$qry->fetch();
	$qry->closeCursor();

	if ($globalrow['t_group']!=0)
	{
		$qry=$db->prepare("SELECT name FROM `tgroups` WHERE id=:id");
		$qry->execute(array('id' => $globalrow['t_group']));
		$grouptech=$qry->fetch();
		$qry->closeCursor();
	}

	if ($globalrow['u_group']!=0)
	{
		$qry=$db->prepare("SELECT name FROM `tgroups` WHERE id=:id");
		$qry->execute(array('id' => $globalrow['u_group']));
		$groupuser=$qry->fetch();
		$qry->closeCursor();
	}

	$qry=$db->prepare("SELECT name FROM `tstates` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['state']));
	$staterow=$qry->fetch();
	$qry->closeCursor();

	$qry=$db->prepare("SELECT name FROM `tcategory` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['category']));
	$catrow=$qry->fetch();
	$qry->closeCursor();

	$qry=$db->prepare("SELECT name FROM `tsubcat` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['subcat']));
	$subcatrow=$qry->fetch();
	$qry->closeCursor();

	if ($rparameters['ticket_places']==1)
	{
		$qry=$db->prepare("SELECT `id`,`name` FROM `tplaces` WHERE id=:id");
		$qry->execute(array('id' => $globalrow['place']));
		$placerow=$qry->fetch();
		$qry->closeCursor();

		if($placerow['id']!=0)
		{
			$place='
			<tr>
				<td colspan="2"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Lieu').':</b> '.$placerow['name'].'</font></td>
			</tr>
			';
		} else {$place='';}
	} else {$place='';}

	//generate resolution
	if($rparameters['mail_order']==1)
	{
		$qry=$db->prepare("SELECT date,type,text,author,group1,group2,tech1,tech2,state FROM `tthreads` WHERE ticket=:ticket AND private='0' ORDER BY date DESC");
		$qry->execute(array('ticket' => $_GET['id']));
	} else {
		$qry=$db->prepare("SELECT date,type,text,author,group1,group2,tech1,tech2,state FROM `tthreads` WHERE ticket=:ticket AND private='0' ORDER BY date ASC");
		$qry->execute(array('ticket' => $_GET['id']));
	}
	while($row = $qry->fetch())
	{
		//remove display date from old post
		$find_old=explode(" ", $row['date']);
		$find_old=$find_old[1];
		if ($find_old!='12:00:00') $date_thread=date_convert($row['date']); else  $date_thread='';

		if($row['type']==0)
		{
			//text back-line format
			$text=nl2br($row['text']);

			//test if author is not the technician
			if ($row['author']!=$globalrow['technician'])
			{
				//find author name
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['author']));
				$rauthor=$qry2->fetch();
				$qry2->closeCursor();

				$resolution="$resolution <b> $date_thread $rauthor[firstname] $rauthor[lastname]: </b><br /> $text  <hr />";
			} else {
				if ($date_thread!='')
				{
					$resolution="$resolution <b>$date_thread:</b><br />$text<hr />";
				} else {
					$resolution="$resolution  $text <hr />";
				}
			}
		}
		if ($row['type']==1)
		{
			//generate attribution thread
			if ($row['group1']!=0)
			{

				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group1']));
				$rtechgroup=$qry2->fetch();
				$qry2->closeCursor();

				$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Attribution du ticket au groupe').' '.$rtechgroup['name'].'.<br /><br />';
			} else {
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech1']));
				$rtech3=$qry2->fetch();
				$qry2->closeCursor();

				$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Attribution du ticket à').' '.$rtech3['firstname'].' '.$rtech3['lastname'].'.<br /><br />';
			}
		}
		if ($row['type']==4)
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

			$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Clôture du ticket').' par '.$rauthor['firstname'].' '.$rauthor['lastname'].'.<br /><br />';
		}
		if ($row['type']==5 && $row['state']==2)
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

			$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_("Changement d'état en cours").' par '.$rauthor['firstname'].' '.$rauthor['lastname'].'.<br /><br />';
		}
		if ($row['type']==6)
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

			$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Ticket facturable').' par '.$rauthor['firstname'].' '.$rauthor['lastname'].'.<br /><br />';
		}
		if ($row['type']==7)
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

			$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Ticket non facturable').' par '.$rauthor['firstname'].' '.$rauthor['lastname'].'.<br /><br />';
		}
		if($row['type']==2)
		{
			//generate transfert thread
			if ($row['group1']!=0 && $row['group2']!=0) //case group to group
			{
				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group1']));
				$rtechgroup1=$qry2->fetch();
				$qry2->closeCursor();

				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group2']));
				$rtechgroup2=$qry2->fetch();
				$qry2->closeCursor();

				$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Transfert du ticket du groupe').' '.$rtechgroup1['name'].' '.T_('au groupe ').' '.$rtechgroup2['name'].'. <br /><br />';
			} elseif(($row['tech1']==0 || $row['tech2']==0) && ($row['group1']==0 || $row['group2']==0)) { //case group to tech
				if ($row['tech1']!=0) {
					$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
					$qry2->execute(array('id' => $row['tech1']));
					$rtech4=$qry2->fetch();
					$qry2->closeCursor();
				} else {$rtech4['firstname']='';$rtech4['lastname']='';}
				if ($row['tech2']!=0) {
					$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
					$qry2->execute(array('id' => $row['tech2']));
					$rtech5=$qry2->fetch();
					$qry2->closeCursor();
				} else {$rtech5['firstname']='';$rtech5['lastname']='';}
				if ($row['group1']!=0) {
					$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
					$qry2->execute(array('id' => $row['group1']));
					$rtechgroup4=$qry2->fetch();
					$qry2->closeCursor();
				} else {$rtechgroup4['name']='';}
				if ($row['group2']!=0) {
					$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
					$qry2->execute(array('id' => $row['group2']));
					$rtechgroup5=$qry2->fetch();
					$qry2->closeCursor();
				} else {$rtechgroup5['name']='';}
				$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Transfert du ticket de').' '.$rtechgroup4['name'].$rtech4['firstname'].' '.$rtech4['lastname'].' '.T_('à ').' '.$rtechgroup5['name'].$rtech5['firstname'].' '.$rtech5['lastname'].'. <br /><br />';
		}elseif($row['tech1']!=0 && $row['tech2']!=0) { //case tech to tech
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech1']));
				$rtech1=$qry2->fetch();
				$qry2->closeCursor();

				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech2']));
				$rtech2=$qry2->fetch();
				$qry2->closeCursor();

				$resolution=$resolution.' <b>'.$date_thread.':</b> '.T_('Transfert du ticket de').' '.$rtech1['firstname'].' '.$rtech1['lastname'].' à '.$rtech2['firstname'].' '.$rtech2['lastname'].'. <br /><br />';
			}
		}
	}
	$qry->closeCursor();
	$description = $globalrow['description'];

	//dates conversions
	$date_create = date_convert("$globalrow[date_create]");
	$date_hope = date_cnv("$globalrow[date_hope]");
	$date_res = date_convert("$globalrow[date_res]");


	echo '
	<html>
		<head>
			<title>Impression du ticket n°'.sprintf("%'.08d\n", $globalrow['id']).'</title>
			<meta charset="UTF-8" />
		</head>
		<body onload="window.print()">
			<font face="Arial">
				<table width="800" cellspacing="0" cellpadding="10">
					<tr bgcolor="'.$rparameters['mail_color_title'].'" >
						<th>
							<span style="font-size: large; color: #FFFFFF;"> &nbsp; Ticket n°'.sprintf("%'.08d\n", $globalrow['id']).' &nbsp;</span>
						</th>
					</tr>
					<tr bgcolor="'.$rparameters['mail_color_bg'].'" >
					  <td>
						<font color="'.$rparameters['mail_color_text'].'"></font>
						<table  border="1" bordercolor="'.$rparameters['mail_color_title'].'" cellspacing="0"  cellpadding="5">
							<tr>
								<td><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Titre').':</b></b> '.$globalrow['title'].'</font></td>
								<td><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Catégorie').':</b></b> '.$catrow['name'].' - '.$subcatrow['name'].'</font></td>
							</tr>
							<tr>
								';
								//detect user group
								if ($globalrow['u_group']!=0)
								{echo '<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Demandeur').':</b></b> '.$groupuser['name'].'</font></td>';}
								else
								{echo '<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Demandeur').':</b></b> '.$userrow['firstname'].' '.$userrow['lastname'].'</font></td>';}
								//detect technician group
								if ($globalrow['t_group']!=0)
								{echo '<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Groupe de technicien en charge').':</b> '.$grouptech['name'].'</font></td>';}
								else
								{echo '<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Technicien en charge').':</b> '.$techrow['firstname'].' '.$techrow['lastname'].'</font></td>';}
								echo '
							</tr>
							<tr>
								<td><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('État').':</b> '.T_($staterow[0]).'</font></td>
								<td><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Date de la demande').':</b> '.$date_create.'</font></td>
							</tr>
							'.$place;
							//invert resolution and description part for antechronological case
							if($rparameters['mail_order']==1)
							{
								echo '
									<tr>
										<td colspan="2"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Résolution').':</b><br />'.$resolution.'</font></td>
									</tr>
									<tr>
										<td colspan="2"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Description').':</b> '.$description.'</font></td>
									</tr>
								';
							} else {
								echo '
									<tr>
										<td colspan="2"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Description').':</b> '.$description.'</font></td>
									</tr>
									<tr>
										<td colspan="2"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Résolution').':</b>'.$resolution.'</font></td>
									</tr>
								';
							}
							echo '
							<tr>
								<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Date de résolution estimée').':</b></b> '.$date_hope.'</font></td>
								<td width="400"><font color="'.$rparameters['mail_color_text'].'"><b>'.T_('Date de résolution').':</b> '.$date_res.'</font></td>
							</tr>
						</table>
					  </td>
					</tr>
				</table>
			</font>
		</body>
	</html>';
} else {
	echo DisplayMessage('error',T_("Vous n'avez pas les droits d'accès à cette page. Contacter votre administrateur"));
}

$db = null;
?>
