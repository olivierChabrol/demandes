<?php
################################################################################
# @Name : ticket_useradd.php
# @Description : dd and modify user
# @call : ./ticket.php
# @parameters :  
# @Author : Flox
# @Create : 07/03/2014
# @Update : 04/12/2017
# @Version : 3.1.28
################################################################################

//initialize variables 
if(!isset($_GET ['id'])) $_GET['id'] = ''; 
if(!isset($_GET ['edituserid'])) $_GET['edituserid'] = ''; 
if(!isset($_POST['adduser'])) $_POST['adduser'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['modifyuser'])) $_POST['modifyuser'] = ''; 
if(!isset($_POST['firstname'])) $_POST['firstname'] = ''; 
if(!isset($_POST['lastname'])) $_POST['lastname'] = ''; 
if(!isset($_POST['usermail'])) $_POST['usermail'] = ''; 
if(!isset($_POST['company'])) $_POST['company'] = ''; 

$db_edituser=strip_tags($db->quote($_GET['edituser']));

//secure text string and remove special char
$_POST['firstname'] =$db->quote($_POST['firstname']);
$_POST['lastname'] =$db->quote($_POST['lastname']);

//submit actions
if($_POST['add'])
{
	$db->exec("INSERT INTO tusers (profile,firstname,lastname,phone,mail,company) VALUES ('2',$_POST[firstname],$_POST[lastname],'$_POST[phone]','$_POST[usermail]','$_POST[company]')");
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}
if($_POST['modifyuser'])
{
	$db->exec("UPDATE tusers SET lastname=$_POST[lastname], phone='$_POST[phone]', mail='$_POST[usermail]', firstname=$_POST[firstname], company='$_POST[company]' where id LIKE $db_edituser");
	
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//case for create new user
if ($_GET['action']=="adduser")
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i> '.T_('Ajouter un nouvel utilisateur');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="add" type="hidden" value="1">
		<label for="firstname">'.T_('Prénom').':</label> 
		<input  name="firstname" type="text" size="26">
		<br />
		<label for="lastname">'.T_('Nom').':</label> 
		<input  name="lastname" type="text" size="26">
		<br />
		<label for="phone">'.T_('Tel').':</label> 
		<br />
		<input  name="phone" type="text" size="26">
		<br />
		<label for="usermail">'.T_('Mail').':</label> 
		<br />
		<input  name="usermail" type="text" value="" size="26">';
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext.'
		    <label for="company">'.T_('Société').':</label><br />
		    <select id="company" name="company">';
    	    $query = $db->query("SELECT * FROM `tcompany` ORDER BY name ASC");
			while ($rcompany = $query->fetch()) {
				//translate non state
				if ($rcompany['id']==0)
				{
					$boxtext= $boxtext.'<option value="'.$rcompany['id'].'">'.T_($rcompany['name']).'</option>';
				} else {
					$boxtext= $boxtext.'<option value="'.$rcompany['id'].'">'.$rcompany['name'].'</option>';
				}
			} 
        	$query->closeCursor(); 
			$boxtext= $boxtext.'
			</select>
            <a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter une société').'" ></i></a>';
		}
		$boxtext=$boxtext.'
		<br /><br />
		<a target="blank" href="./index.php?page=admin&subpage=user&action=add">'.T_('Plus de champs').'...</a>
		<br />		
	</form>
	';
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
else //case for modify an existing user
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i> '.T_('Modification d\'un utilisateur');
	$query=$db->query("SELECT * FROM tusers WHERE id LIKE $db_edituser");
	$row=$query->fetch();
	$query->closeCursor();
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="modifyuser" type="hidden" value="1">
		<label>'.T_('Prénom').':</label> 
		<input name="firstname" type="text" size="26" value="'.$row['firstname'].'">
		<br />
		<label>'.T_('Nom').':</label> 
		<input name="lastname" type="text" size="26" value="'.$row['lastname'].'">
		<br />
		<label>'.T_('Tel').':</label> 
		<br />
		<input name="phone" type="text" size="26" value="'.$row['phone'].'">
		<br />
		<label>'.T_('Mail').':</label> 
		<br />
		<input name="usermail" type="text" size="26" value="'.$row['mail'].'" >
		';
		
		//display advanced user informations
		if ($rparameters['user_advanced']!=0)
		{
		    $boxtext=$boxtext.'
		    <label for="company">'.T_('Société').':</label><br />
		    <select id="company" name="company">';
    	    $query = $db->query("SELECT * FROM `tcompany` ORDER BY name ASC");
			while ($rcompany = $query->fetch())
			{
				
				$query2=$db->query("SELECT id FROM `tcompany` WHERE id like '$row[company]'");
				$row2=$query2->fetch();
				$query2->closeCursor();
				if ($row2['id']==$rcompany['id']) {$selected='selected';} else {$selected='';}
				//translate non state
				if ($rcompany['id']==0)
				{
					$boxtext= $boxtext.'<option value="'.$rcompany['id'].'" '.$selected.'>'.T_($rcompany['name']).'</option>';
				} else {
					$boxtext= $boxtext.'<option value="'.$rcompany['id'].'" '.$selected.'>'.$rcompany['name'].'</option>';
				}
			}
			$query->closeCursor();
        	$boxtext= $boxtext.'</select>
        	<a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"> <i class="icon-plus-sign green bigger-130" title="'.T_('Ajouter une société').'" ></i></a>
            ';
		}
		$boxtext=$boxtext.'
		<br /><br />
		<a target="blank" href="./index.php?page=admin&subpage=user&action=edit&userid='.$_GET['edituser'].'">'.T_('Plus de champs').'...</a>
		<br />		
	</form>
	';
	$valid=T_('Modifier');
	$action1="$('form#form').submit();
	$( this ).dialog( \"close\" );";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>