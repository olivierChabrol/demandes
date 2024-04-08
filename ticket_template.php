<?php
################################################################################
# @Name : ./ticket_template.php
# @Description : select template incident
# @Call : /core/ticket.php
# @Author : Flox
# @Update : 21/10/2014
# @Update : 04/09/2017
# @Version : 3.1.25
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_POST['duplicate'])) $_POST['duplicate'] = ''; 
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($row['title'])) $row['title'] = '';
if(!isset($row['user'])) $row['user'] = '';
if(!isset($row['priority'])) $row['priority'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['time'])) $row['time'] = '';
if(!isset($row['category'])) $row['category'] = '';
if(!isset($row['subcat'])) $row['subcat'] = '';
if(!isset($row['technician'])) $row['technician'] = '';
if(!isset($row['criticality'])) $row['criticality'] = '';
if(!isset($row['type'])) $row['type'] = '';

if($_POST['duplicate'])
{
	$query= $db->query("SELECT * FROM `tincidents` WHERE id='$_POST[template]'");
	$row=$query->fetch();
	$query->closecursor();
	//escape special char to sql query
	$row['description']=$db->quote($row['description']);
	$row['title']=$db->quote($row['title']);

	if ($_SESSION['profile_id']==2 || $_SESSION['profile_id']==1)	
	{
		//case for powerusers or users
		$query= "
		INSERT INTO tincidents (
		user,title,description,priority,state,time,category,subcat,date_create,technician,criticality,creator,place,type
		) VALUES (
		'$_SESSION[user_id]',$row[title],$row[description],'$row[priority]','$row[state]','$row[time]','$row[category]','$row[subcat]','$datetime','$row[technician]','$row[criticality]','$_SESSION[user_id]','$row[place]','$row[type]'
		)
		";
	} else {
		//case for technician
		$query= "
		INSERT INTO tincidents (
		user,title,description,priority,state,time,category,subcat,date_create,technician,criticality,creator,place,type
		) VALUES (
		'$row[user]',$row[title],$row[description],'$row[priority]','$row[state]','$row[time]','$row[category]','$row[subcat]','$datetime','$row[technician]','$row[criticality]','$_SESSION[user_id]','$row[place]','$row[type]'
		)
		";
	}
	
	$db->exec("$query");
	
	////threads insert
	//find id of new ticket
	$query= $db->query("SELECT MAX(id) FROM `tincidents`");
	$newticketid=$query->fetch();
	//find tickets from source ticket
	$query= $db->query("SELECT * FROM `tthreads` WHERE ticket='$_POST[template]'");
	while ($row=$query->fetch()) {
		//escape special char to sql query
		$row['text']=$db->quote($row['text']);
		//insert new threads
		$db->exec("INSERT INTO tthreads (ticket,date,author,text,type) VALUES ('$newticketid[0]','$datetime','$_SESSION[user_id]',$row[text],'$row[type]')");
	}
	$query->closecursor();
	$boxtext= '<div class="alert alert-block alert-success"><center><i class="icon-ok green"></i>	'.T_('Le modèle à été appliqué au ticket en cours').'.</center></div>';
	echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
	</SCRIPT>";
} else {
	//display form
	$boxtext=' 
	<form name="form" method="POST" action="" id="form">
		<input name="duplicate" type="hidden" value="1">
		<label for="template">
		<select id="template" name="template">
			';
			$query= $db->query("SELECT * FROM `ttemplates` order by name ASC");
			while ($row=$query->fetch()) {
				$boxtext=$boxtext.'<option value="'.$row['incident'].'">'.$row['name'].'</option>';
			} 
			$query->closecursor();
			$boxtext=$boxtext.'
		</select>
	</form>
	';
}
$boxtitle="<i class='icon-tags blue bigger-120'></i>".T_('Liste des modèles');
$valid=T_('Utiliser');
$action1="$('form#form').submit();";
$cancel=T_('Fermer');
$action2="$( this ).dialog( \"close\" ); ";
include "./modalbox.php";
?>