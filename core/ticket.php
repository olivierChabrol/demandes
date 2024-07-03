<?php
################################################################################
# @Name : ./core/ticket.php
# @Description : actions page for tickets
# @Call : ./ticket.php
# @Author : Flox
# @Create : 28/10/2013
# @Update : 21/08/2020
# @Version : 3.2.4 p3
################################################################################

//!\Modifier par nos soins

require_once('models/request/ticket/ticket.php');
require_once('models/user/user.php');
require_once('models/tool/parameters.php');

use Models\Request\Ticket\Ticket;
use Models\User\User;
use Models\Tool\Parameters;

// load extra parameters for request
$parameters = Parameters::getInstance();

//initialize variable
if(!isset($_POST['close'])) $_POST['close'] = '';
if(!isset($_POST['text'])) $_POST['text'] = '';
if(!isset($_POST['send'])) $_POST['send'] = '';
if(!isset($_POST['action'])) $_POST['action'] = '';
if(!isset($_POST['edituser'])) $_POST['edituser'] = '';
if(!isset($_POST['editcat'])) $_POST['editcat'] = '';
if(!isset($_POST['start_availability'])) $_POST['start_availability'] = '';
if(!isset($_POST['end_availability'])) $_POST['end_availability'] = '';
if(!isset($_POST['availability_planned'])) $_POST['availability_planned'] = '';
if(!isset($_POST['u_agency'])) $_POST['u_agency'] = '';
if(!isset($start_availability)) $start_availability = '';
if(!isset($end_availability)) $end_availability = '';
if(!isset($error)) $error="0";

//find next incident number for new ticket
if($_GET['action']=='new')
{
	$qry=$db->prepare("SELECT MAX(`auto_increment`) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`=:database AND `table_name`='tincidents';");
	$qry->execute(array('database' => $db_name));
	$new_ticket_id=$qry->fetch();
	$qry->closeCursor();

	//check if ticket not already existing MySQL bug auto-increment value
	$qry=$db->prepare("SELECT `id` FROM `tincidents` WHERE id=:id");
	$qry->execute(array('id' => $new_ticket_id[0]));
	$row=$qry->fetch();
	$qry->closeCursor();
	if(empty($row['id'])) {$row['id']='';}

	if($row['id']) //ticket number already exist generate new one
	{
		$qry=$db->prepare("SELECT MAX(`id`) FROM `tincidents`");
		$qry->execute();
		$max_id=$qry->fetch();
		$qry->closeCursor();
		$_GET['id']=$max_id[0]+1;
		$db_id=$max_id[0]+1;
	} else {
		$_GET['id']=$new_ticket_id[0];
		$db_id=$new_ticket_id[0];
	}
}

//action delete ticket
if(($_GET['action']=="delete") && ($rright['ticket_delete']!=0) && $_GET['id'])
{
	$qry=$db->prepare("DELETE FROM `tincidents` WHERE id=:id"); //delete ticket
	$qry->execute(array('id' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tevents` WHERE incident=:incident"); //delete associate events
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tthreads` WHERE ticket=:ticket"); //delete threads
	$qry->execute(array('ticket' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tmails` WHERE incident=:incident"); //delete mails
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `tsurvey_answers` WHERE ticket_id=:ticket_id"); //delete survey
	$qry->execute(array('ticket_id' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `ttemplates` WHERE incident=:incident"); //delete template
	$qry->execute(array('incident' => $_GET['id']));
	$qry=$db->prepare("DELETE FROM `ttoken` WHERE ticket_id=:ticket_id"); //delete token
	$qry->execute(array('ticket_id' => $_GET['id']));

	//remove old upload files and folder if exist
	$upload_dir_to_remove='upload/'.$_GET['id'].'/';
	if(is_numeric($_GET['id']) && is_dir($upload_dir_to_remove))
	{
		//remove files before delete directory
		$files_to_remove = array_diff(scandir($upload_dir_to_remove), array('.','..'));
		foreach ($files_to_remove as $file_to_remove) {
			if(file_exists($upload_dir_to_remove.$file_to_remove)) {unlink($upload_dir_to_remove.$file_to_remove);}
		}
		rmdir($upload_dir_to_remove); //remove empty dir
	}

	//remove new upload files
	$qry=$db->prepare("SELECT COUNT(`id`) FROM `tattachments` WHERE ticket_id=:ticket_id");
	$qry->execute(array('ticket_id' => $_GET['id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	if($row[0]>0)
	{
		//remove files
		$qry=$db->prepare("SELECT `storage_filename` FROM `tattachments` WHERE ticket_id=:ticket_id");
		$qry->execute(array('ticket_id' => $_GET['id']));
		while($attachment=$qry->fetch())
		{
			if(file_exists('upload/ticket/'.$attachment['storage_filename'])) {unlink('upload/ticket/'.$attachment['storage_filename']);}
		}
		$qry->closeCursor();
		//delete in db
		$qry=$db->prepare("DELETE FROM `tattachments` WHERE ticket_id=:ticket_id");
		$qry->execute(array('ticket_id' => $_GET['id']));
	}

	//remove image attachment from IMAP connector
	$ticket_files = glob("upload/ticket/$_GET[id]_*");
	foreach ($ticket_files as $file_to_delete) {
		if(file_exists($file_to_delete)){unlink($file_to_delete); }
	}

	//display delete message
	echo DisplayMessage('success',T_('Ticket supprimé'));
	//redirect
	$url="./index.php?page=dashboard&state=$_GET[state]&userid=$_GET[userid]";
	$url=preg_replace('/%/','%25',$url);
	$url=preg_replace('/%2525/','%25',$url);
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
		function redirect(){window.location='$url'}
		setTimeout('redirect()',$rparameters[time_display_msg]);
	</SCRIPT>
	";
}

//action to lock thread
if($_GET['lock_thread'] && $rright['ticket_thread_private']!=0)
{
	$qry=$db->prepare("UPDATE `tthreads` SET `private`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['lock_thread']));
}

//action to unlock thread
if($_GET['unlock_thread'] && $rright['ticket_thread_private']!=0)
{
	$qry=$db->prepare("UPDATE `tthreads` SET `private`='0' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['unlock_thread']));
}

//master query
$qry=$db->prepare("SELECT `tincidents`.*, dmission_order.om_for_guest, dmission_order.guest_name, dmission_order.guest_mail,dmission_order.guest_birthdate,dmission_order.guest_phone_number,dmission_order.guest_labo,dmission_order.guest_country
	FROM `tincidents` 
    LEFT JOIN `dmission_order` ON `tincidents`.`id`=`dmission_order`.`incident_id` 
    WHERE `tincidents`.id=:id");
$qry->execute(array('id' => $_GET['id']));
$globalrow=$qry->fetch();
$qry->closeCursor();

if(empty($globalrow['user'])) {$globalrow['user']=0;}
if(empty($globalrow['u_group'])) {$globalrow['u_group']=0;}
if(empty($globalrow['t_group'])) {$globalrow['t_group']=0;}
if(empty($globalrow['technician'])) {$globalrow['technician']=0;}
if(empty($globalrow['u_service'])) {$globalrow['u_service']=0;}
if(empty($globalrow['u_agency'])) {$globalrow['u_agency']=0;}
if(empty($globalrow['asset_id'])) {$globalrow['asset_id']=0;}
if(empty($globalrow['sender_service'])) {$globalrow['sender_service']=0;}
if(empty($globalrow['techread'])) {$globalrow['techread']=0;}
if(empty($globalrow['userread'])) {$globalrow['userread']=0;}
if(empty($globalrow['state'])) {$globalrow['state']=5;}
if(empty($globalrow['time_hope'])) {$globalrow['time_hope']=5;}
if(empty($globalrow['time'])) {$globalrow['time']=0;}
if(empty($globalrow['title'])) {$globalrow['title']='';}
if(empty($globalrow['creator'])) {$globalrow['creator']=0;}

//include modalbox
if($rright['planning'] && $rparameters['planning'] && $rright['ticket_calendar']) {include('includes/ticket_calendar.php');}
if($rright['ticket_user_actions']) {include('includes/ticket_user.php');}
if($rright['ticket_cat_actions']) {include('includes/ticket_category.php');}
if($rright['ticket_template'] && $_GET['action']=='new') {include('includes/ticket_template.php');}

//user group detection switch values
if(substr($_POST['user'], 0, 1) =='G')
{
 	$u_group=explode("_", $_POST['user']);
	$u_group=$u_group[1];
	$_POST['user']='';
} elseif($globalrow['u_group']!=0 && $_POST['user']=='')
{
	$u_group=$globalrow['u_group'];
	$_POST['user']='';
}
//technician group detection switch values
if(substr($_POST['technician'], 0, 1) =='G')
{
 	$t_group=explode("_", $_POST['technician']);
	$t_group=$t_group[1];
	$_POST['technician']='';
} elseif($globalrow['t_group']!=0 && $_POST['technician']=='')
{
	$t_group=$globalrow['t_group'];
	$_POST['technician']='';
}

//database inputs if submit
if($rparameters['debug']){ echo "<b><u>DEBUG MODE :</u></b><br /> <b>VAR:</b> save=$save post_modify=$_POST[modify] post_quit=$_POST[quit] post_mail=$_POST[mail] post_upload=$_POST[upload] post_send=$_POST[send] post_action=$_POST[action] get_action=$_GET[action] post_category=$_POST[category] post_subcat=$_POST[subcat] post_technician=$_POST[technician] globalrow_technician=$globalrow[technician] post_u_service=$_POST[u_service] globalrow_u_service=$globalrow[u_service] post_u_agency=$_POST[u_agency] globalrow_u_agency=$globalrow[u_agency] post_asset_id=$_POST[asset_id] globalrow[asset_id]=$globalrow[asset_id] post_sender_service=$_POST[sender_service] globalrow_sender_service=$globalrow[sender_service] post_priority=$_POST[priority] post_title=$_POST[title] post_date_hope=$_POST[date_hope]<br />";}
if($_POST['addcalendar']||$_POST['addevent']||$_POST['modify']||$_POST['quit']||$_POST['close']||$_POST['mail']||$_POST['upload']||$save=="1"||$_POST['send']||$_POST['action'])
{
	//check mandatory fields
    if($rright['ticket_priority_mandatory'] && !$_POST['priority']) {$error=T_('Merci de renseigner la priorité');}
    if($rright['ticket_criticality_mandatory'] && !$_POST['criticality']) {$error=T_('Merci de renseigner la criticité');}
		if($rright['ticket_description_mandatory'] && ((ctype_space($_POST['text']) || $_POST['text']=='' || ctype_space(strip_tags($_POST['text']))==1 ) || strip_tags($_POST['text'])=='')) {$error=T_('Merci de renseigner la description de ce ticket');}
    if($rright['ticket_cat_mandatory'] && (!$_POST['category'] || !$_POST['subcat'])) {$error=T_("Merci de renseigner le champ catégorie et sous-catégorie");}
    if($rright['ticket_asset_mandatory'] && $rparameters['asset']==1 && !$_POST['asset_id']) {$error=T_("Merci de renseigner l'équipement");}
    if($rright['ticket_type_mandatory'] && $rparameters['ticket_type']==1 && !$_POST['type']) {$error=T_("Merci de renseigner le champ type");}
    if($rright['ticket_agency_mandatory'] && $rparameters['user_agency'] && !$_POST['u_agency']) {
		//check if current user have multiple agencies to display empty mandatory alert
		$qry2=$db->prepare("SELECT COUNT(*) FROM `tusers_agencies` WHERE user_id=:user_id");
		$qry2->execute(array('user_id' => $_SESSION['user_id']));
		$row2=$qry2->fetch();
		$qry2->closeCursor();
		if($row2[0]>1 && $_SESSION['profile_id']!=4) {$error=T_("Merci de renseigner l'agence");}
	}
	if($rright['ticket_tech_mandatory'] && !$_POST['technician'] && !$t_group) {$error=T_('Merci de renseigner le technicien associé à ce ticket');}
	//check user ticket limit
	if($rparameters['user_limit_ticket'] && $ruser['limit_ticket_number'] && $ruser['limit_ticket_days'] && $ruser['limit_ticket_date_start']!='0000-00-00' && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
	{
		$date_start_conv = date_create($ruser['limit_ticket_date_start']);
		date_add($date_start_conv, date_interval_create_from_date_string("$ruser[limit_ticket_days] days"));
		$date_end=date_format($date_start_conv, 'Y-m-d');

		//count number of ticket remaining in period
		$qry=$db->prepare("SELECT COUNT(*) FROM `tincidents` WHERE user=:user AND date_create BETWEEN :date_create AND :date_end AND disable='0'");
		$qry->execute(array('user' => $_SESSION['user_id'],'date_create' => $ruser['limit_ticket_date_start'],'date_end' => $date_end));
		$nbticketused=$qry->fetch();
		$qry->closeCursor();

		//check number of tickets in current range date
		if(date('Y-m-d')>$date_end || date('Y-m-d')<$ruser['limit_ticket_date_start'])
		{$nbticketremaining=0;}
		else
		{$nbticketremaining=$ruser['limit_ticket_number']-$nbticketused[0];}
		if($nbticketremaining<=0) {$error=T_('Votre limite de ticket est atteinte, prenez contact avec votre administrateur pour créditer votre compte').'.';}
	}
	//check company limit ticket
	if($rparameters['company_limit_ticket'] && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
	{
		//get company limit ticket parameters
		$qry=$db->prepare("SELECT * FROM `tcompany` WHERE id=:id");
		$qry->execute(array('id' => $ruser['company']));
		$rcompany=$qry->fetch();
		$qry->closeCursor();

		if($rcompany['limit_ticket_number'] && $rcompany['limit_ticket_days']&& $rcompany['limit_ticket_date_start']!='0000-00-00' )
		{
			//calculate end date
			$date_start_conv = date_create($rcompany['limit_ticket_date_start']);
			date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_ticket_days] days"));
			$date_end=date_format($date_start_conv, 'Y-m-d');

			//count number of ticket remaining in period
			$qry=$db->prepare("SELECT COUNT(*) FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable='0'");
			$qry->execute(array('company' => $rcompany['id'],'date_start' => $rcompany['limit_ticket_date_start'],'date_end' => $date_end));
			$nbticketused=$qry->fetch();
			$qry->closeCursor();

			//check number of tickets in current range date
			if(date('Y-m-d')>$date_end || date('Y-m-d')<$rcompany['limit_ticket_date_start'])
			{$nbticketremaining=0;}
			else
			{$nbticketremaining=$rcompany['limit_ticket_number']-$nbticketused[0];}
			if($nbticketremaining<=0) {$error=T_('La limite de ticket attribuée pour votre société est atteinte, prenez contact avec votre administrateur pour créditer votre compte.');}
		}
	}
	//check company limit hour
	if($rparameters['company_limit_hour'] && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
	{
		//get company limit ticket parameters
		$qry=$db->prepare("SELECT * FROM `tcompany` WHERE id=:id");
		$qry->execute(array('id' => $ruser['company']));
		$rcompany=$qry->fetch();
		$qry->closeCursor();

		if($rcompany['limit_hour_number'] && $rcompany['limit_hour_days']&& $rcompany['limit_hour_date_start']!='0000-00-00' )
		{
			//calculate end date
			$date_start_conv = date_create($rcompany['limit_hour_date_start']);
			date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_hour_days] days"));
			$date_end=date_format($date_start_conv, 'Y-m-d');

			//count number of hour remaining in period
			$qry=$db->prepare("SELECT SUM(tincidents.time)/60 FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable='0'");
			$qry->execute(array('company' => $rcompany['id'],'date_start' => $rcompany['limit_ticket_date_start'],'date_end' => $date_end));
			$nbhourused=$qry->fetch();
			$qry->closeCursor();

			//check number of tickets in current range date
			if(date('Y-m-d')>$date_end || date('Y-m-d')<$rcompany['limit_hour_date_start'])
			{$nbticketremaining=0;}
			else
			{$nbhourremaining=$rcompany['limit_hour_number']-$nbhourused[0];}
			if($nbhourremaining<=0) {$error=T_("La limite d'heures attribuée pour votre société est atteinte, prenez contact avec votre administrateur pour créditer votre compte.");}
		}
	}
	//automatic technician category attribution
	if($rparameters['ticket_cat_auto_attribute'])
	{
		//only if technician field not display
		if($_GET['action']=='new' && !$rright['ticket_new_tech_disp'])
		{
			$auto_tech_attribute=0;
			if($_POST['subcat'])
			{
				//check if association between technician and category exist
				$qry=$db->prepare("SELECT `id`,`technician`,`technician_group` FROM `tsubcat` WHERE (technician!=0 OR technician_group!=0) AND id=:id");
				$qry->execute(array('id' => $_POST['subcat']));
				$auto_tech=$qry->fetch();
				$qry->closeCursor();
				if($auto_tech)
				{
					if($auto_tech['technician']){$_POST['technician']=$auto_tech['technician']; $auto_tech_attribute=1;}
					elseif($auto_tech['technician_group']){$t_group=$auto_tech['technician_group']; $auto_tech_attribute=1;}
				}
			}
			if($_POST['category'] && !$auto_tech_attribute)
			{
				//check if association between technician and category exist
				$qry=$db->prepare("SELECT `id`,`technician`,`technician_group` FROM `tcategory` WHERE (technician!=0 OR technician_group!=0) AND id=:id");
				$qry->execute(array('id' => $_POST['category']));
				$auto_tech=$qry->fetch();
				$qry->closeCursor();
				if($auto_tech)
				{
					if($auto_tech['technician']){$_POST['technician']=$auto_tech['technician'];}
					elseif($auto_tech['technician_group']){$t_group=$auto_tech['technician_group'];}
				}
			}

		}
	}
	//escape special char and secure string before database insert
	$_POST['description']=$_POST['text'];
	$_POST['resolution']=$_POST['text2'];
	if($error=='0') {$_POST['title']=strip_tags($_POST['title']);}

	//remove <br> generate by IE browser
	$_POST['description']=str_replace('<br><br><br>','',$_POST['description']);
	$_POST['resolution']=str_replace('<br><br><br>','',$_POST['resolution']);
	if($_POST['description']=='<br>'){$_POST['description']='';}
	if($_POST['resolution']=='<br>'){$_POST['resolution']='';}

	//convert date
	if($_POST['start_availability'])
	{
		$start_availability=DateTime::createFromFormat('d/m/Y H:i:s',$_POST['start_availability']);
		$start_availability=$start_availability->format('Y-m-d H:i:s');
		$end_availability=DateTime::createFromFormat('d/m/Y H:i:s',$_POST['end_availability']);
		$end_availability=$end_availability->format('Y-m-d H:i:s');
	}

	//thread generation when no error detected
	if(!$error)
	{
		//detect transfert tech group change to group
		if($t_group!=$globalrow['t_group'] && $globalrow['technician']==0 && $t_group!='' && $globalrow['t_group']!=0 ) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`group1`,`group2`) VALUES (:ticket,:date,:author,'','2',:group1,:group2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'group1' => $globalrow['t_group'],'group2' => $t_group));
		}
		//detect transfert tech change to tech
		if($rright['ticket_tech'] && $_POST['technician']!=$globalrow['technician'] && $globalrow['technician']!=0 && $_POST['technician']!='') {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`tech1`,`tech2`) VALUES (:ticket,:date,:author,'','2',:tech1,:tech2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'tech1' => $globalrow['technician'],'tech2' => $_POST['technician']));
		}
		//detect transfert techgroup change to tech
		if($globalrow['t_group']!=0 && $_POST['technician']) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`group1`,`tech2`) VALUES (:ticket,:date,:author,'','2',:group1,:tech1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'group1' => $globalrow['t_group'],'tech1' => $_POST['technician']));
		}
		//detect transfert tech change to techgroup
		if($globalrow['technician']!=0 && $t_group) {
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`tech1`,`group2`) VALUES (:ticket,:date,:author,'','2',:tech1,:group2)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'tech1' => $globalrow['technician'],'group2' => $t_group));
		}
		//detect technician attribution
		if(($rright['ticket_tech'] || $rparameters['ticket_cat_auto_attribute']) && $globalrow['technician']==0 && $_POST['technician']!='' && $_POST['technician']!='0' && $globalrow['t_group']==0)
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`tech1`) VALUES (:ticket,:date,:author,'1',:tech1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'tech1' => $_POST['technician']));
		}
		//detect tech group attribution
		if($globalrow['t_group']==0 && $t_group!='' && $globalrow['technician']==0)
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`group1`) VALUES (:ticket,:date,:author,'1',:group1)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'group1' => $t_group));
		}
		//generate thread for switch state
		if($rright['ticket_state'] && $globalrow['state']!=$_POST['state'] && $_POST['state']!=3 && $_POST['technician']!='')
		{
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
		//auto modify state from 5 to 1 if technician change (not attribute to wait tech)
		if(!$globalrow['technician'] && $_POST['technician'] && $globalrow['state']=='5' && $_POST['state']==$globalrow['state'] && $t_group=='')
		{
			if($rparameters['debug']) {echo "<b>AUTO CHANGE STATE:</b> from 5 to 1 reason technician change detected (globalrow[state]=$globalrow[state] POST[state]=$_POST[state])<br />";}
			$_POST['state']='1';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
		//auto modify state from 5 to 1 if technician group change (not attribute to wait tech)
		if($globalrow['t_group']==0 && $globalrow['state']=='5' && $_POST['state']==$globalrow['state'] && $t_group!='')
		{
			if($rparameters['debug']) {echo "<b>AUTO CHANGE STATE:</b> from 5 to 1 reason technician group change detected (globalrow[state]=$globalrow[state] POST[state]=$_POST[state] t_group=$t_group)<br />";}
			$_POST['state']='1';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
		//auto modify state from 5 to 2 if technician add resolution thread (wait tech to current)
		if((($_POST['resolution']!='') && ($_POST['resolution']!='\'\'')) && ($globalrow['technician']==$_SESSION['user_id']) && ($_POST['state']=='1'))
		{
			if($rparameters['debug']) {echo "<b>AUTO CHANGE STATE</b> from 5 to 2 reason technician add resolution thread detected<br />";}
			$_POST['state']='2';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
		//auto modify state from 5 to 2 if technician add resolution thread on new ticket(wait tech to current)
		if(($_POST['resolution']!='') && ($_POST['resolution']!='\'\'') && ($_POST['technician']==$_SESSION['user_id']) && ($_POST['state']=='1') && ($_GET['action']=='new'))
		{
			if($rparameters['debug']) {echo "<b>AUTO CHANGE STATE</b> from 5 to 2 reason technician add resolution thread on new ticket detected<br />";}
			$_POST['state']='2';
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
		//auto modify state to default state from parameters if tech is null (attribution state)
		if($_POST['technician']==0 && $_GET['action']=='new' && $t_group=='')
		{
			if($rparameters['debug']) {echo "<b>AUTO CHANGE STATE</b> to default state $rparameters[ticket_default_state] reason no technician or technician group associated with ticket<br />";}
			$_POST['state']=$rparameters['ticket_default_state'];
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,'5',:state)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'state' => $_POST['state']));
		}
	}

	//insert resolution date if state is change to resolve (3)
	if($_POST['state']=='3' && $globalrow['state']!='3' && ($_POST['date_res']=='' || $_POST['date_res']=='0000-00-00 00:00:00')) {$_POST['date_res']=date("Y-m-d H:i:s");}

	//remove resolution date if state change from 3 to other state
	if($globalrow['state']=='3' && $_POST['state']!='3') {$_POST['date_res']='';}

	//unread ticket if another technician add thread
	if($_POST['resolution'] && ($globalrow['technician']!=$_SESSION['user_id'])) {$techread=0;}

	//auto-attribute ticket to technician if user attachment is detected
	if($_POST['user'])
	{
		$qry=$db->prepare("SELECT `tech` FROM `tusers_tech` WHERE user=:user");
		$qry->execute(array('user' => $_POST['user']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if(empty($row['tech'])) {$row['tech']='';}
        if($row['tech']) {
			if($rparameters['debug']) {echo '<br /><b>AUTO TECH CHANGE:</b> Auto assignment of this ticket, because technician attachment is detected.<br />';}
			$_POST['technician']=$row['tech'];
		}
	}

	//get user service to insert in tincidents table, or get selected service from field if ticket_service right
	if($_POST['user'] && !$_POST['u_service'] && !$rright['ticket_service_disp'])
	{
		$qry=$db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $_POST['user']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if(empty($row['service_id'])) {$row['service_id']='';}
        if($_POST['state']!=3) {$u_service=$row['service_id'];}
		elseif($_POST['state']==3 && $_GET['action']=='new') {$u_service=$row['service_id'];}
		else {$u_service=$globalrow['u_service'];}
		if($rparameters['debug']) {echo ' post_u_service='.$u_service.'<br />'; }
	} elseif($_POST['u_service'] || $rright['ticket_service']!=0)
	{
		$u_service=$_POST['u_service'];
	} else {$u_service=$globalrow['u_service'];}

	if(!isset($u_service)) $u_service=0;

	if(!$error)
	{
		//convert posted datetime to SQL format, if yyyy-mm-dd is detected
		if($_POST['date_create'] && !strpos($_POST['date_create'], "-") && preg_match('~[0-9]~', $_POST['date_create']))
		{
			//convert datetime if time is specified
			if(strpos($_POST['date_create'], ":"))
			{$_POST['date_create'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_create']);}
			else
			{$_POST['date_create'] = DateTime::createFromFormat('d/m/Y', $_POST['date_create']);}
			$_POST['date_create']=$_POST['date_create']->format('Y-m-d H:i:s');
		} elseif(!$_POST['date_create'] && isset($globalrow['date_create'])) {$_POST['date_create'] = $globalrow['date_create'];}
		elseif(!$_POST['date_create'] && !isset($globalrow['date_create'])) {$_POST['date_create'] = date('Y-m-d H:i:s');}

		if($_POST['date_hope'] && !strpos($_POST['date_hope'], "-") && preg_match('~[0-9]~', $_POST['date_hope']))
		{
			//convert datetime if time is specified
			if(strpos($_POST['date_hope'], ":"))
			{$_POST['date_hope'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_hope']);}
			else
			{$_POST['date_hope'] = DateTime::createFromFormat('d/m/Y', $_POST['date_hope']);}
			$_POST['date_hope']=$_POST['date_hope']->format('Y-m-d');
		}
		if($_POST['date_res'] && !strpos($_POST['date_res'], "-") && preg_match('~[0-9]~', $_POST['date_res']))
		{
			//convert datetime if time is specified
			if(strpos($_POST['date_res'], ":"))
			{$_POST['date_res'] = DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_res']);}
			else
			{$_POST['date_res'] = DateTime::createFromFormat('d/m/Y', $_POST['date_res']);}
			$_POST['date_res']=$_POST['date_res']->format('Y-m-d H:i:s');
		}
	}

	//SQL queries
	if(!$error && ($_GET['action']=='new'))
	{
		//modify read state
		if($globalrow['technician']!=$_SESSION['user_id']) {$techread=0;} //unread ticket case when creator is not technician
		if($_POST['technician']==$_SESSION['user_id']) {$techread=1; $techread_date=date("Y-m-d H:i:s"); $userread=0;} //read ticket
		if($_POST['user']==$_SESSION['user_id']) {$userread=1;}

		//insert ticket
		$qry=$db->prepare("INSERT INTO `tincidents`
		(`user`,`type`,`type_answer`,`u_group`,`u_service`,`u_agency`,`sender_service`,`technician`,`t_group`,`title`,`description`,`date_create`,`date_hope`,`date_res`,`priority`,`criticality`,`state`,`creator`,`time`,`time_hope`,`category`,`subcat`,`techread`,`techread_date`,`userread`,`place`,`asset_id`,`start_availability`,`end_availability`,`availability_planned`)
		VALUES
		(:user,:type,:type_answer,:u_group,:u_service,:u_agency,:sender_service,:technician,:t_group,:title,:description,:date_create,:date_hope,:date_res,:priority,:criticality,:state,:creator,:time,:time_hope,:category,:subcat,:techread,:techread_date,:userread,:place,:asset_id,:start_availability,:end_availability,:availability_planned)");
		$qry->execute(array(
			'user' => $_POST['user'],
			'type' => $_POST['type'],
			'type_answer' => $_POST['type_answer'],
			'u_group' => $u_group,
			'u_service' => $u_service,
			'u_agency' => $_POST['u_agency'],
			'sender_service' => $_POST['sender_service'],
			'technician' => $_POST['technician'],
			't_group' => $t_group,
			'title' => $_POST['title'],
			'description' => $_POST['description'],
			'date_create' => $_POST['date_create'],
			'date_hope' => $_POST['date_hope'],
			'date_res' => $_POST['date_res'],
			'priority' => $_POST['priority'],
			'criticality' => $_POST['criticality'],
			'state' => $_POST['state'],
			'creator' => $_SESSION['user_id'],
			'time' => $_POST['time'],
			'time_hope' => $_POST['time_hope'],
			'category' => $_POST['category'],
			'subcat' => $_POST['subcat'],
			'techread' => $techread,
			'techread_date' => $techread_date,
			'userread' => $userread,
			'place' => $_POST['ticket_places'],
			'asset_id' => $_POST['asset_id'],
			'start_availability' => $start_availability,
			'end_availability' => $end_availability,
			'availability_planned' => $_POST['availability_planned']
			));
	}elseif(!$error) {
		//modify read state
		if($_POST['technician']==$_SESSION['user_id']) {$techread=1; $techread_date=date("Y-m-d H:i:s");} //read ticket

		//check previous change, before update, concomitant user change #4471
		$qry=$db->prepare("SELECT `technician`,`state` FROM `tincidents` WHERE id=:id");
		$qry->execute(array('id' => $_GET['id']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if(!$rright['ticket_tech']) {if($row['technician']!=$_POST['technician']) {$_POST['technician']=$row['technician'];}}
		if(!$rright['ticket_state']) {if($row['state']!=$_POST['state']) {$_POST['state']=$row['state'];}}

		//update ticket
		$query = "UPDATE tincidents SET
		user='$_POST[user]',
		type='$_POST[type]',
		type_answer='$_POST[type_answer]',
		u_group='$u_group',
		u_service='$u_service',
		u_agency='$_POST[u_agency]',
		sender_service='$_POST[sender_service]',
		technician='$_POST[technician]',
		t_group='$t_group',
		title=$_POST[title],
		description=$_POST[description],
		date_create='$_POST[date_create]',
		date_hope='$_POST[date_hope]',
		date_res='$_POST[date_res]',
		priority='$_POST[priority]',
		criticality='$_POST[criticality]',
		state='$_POST[state]',
		time='$_POST[time]',
		time_hope='$_POST[time_hope]',
		category='$_POST[category]',
		subcat='$_POST[subcat]',
		techread='$techread',
		techread_date='$techread_date',
		place='$_POST[ticket_places]',
		asset_id='$_POST[asset_id]',
		start_availability='$start_availability',
		end_availability='$end_availability',
		availability_planned='$_POST[availability_planned]'
		WHERE
		id LIKE $db_id";
		if($rparameters['debug']) {echo "<br /><b>QUERY:</b><br /> $query<br />";}

		$qry=$db->prepare("UPDATE `tincidents` SET
		`user`=:user,
		`type`=:type,
		`type_answer`=:type_answer,
		`u_group`=:u_group,
		`u_service`=:u_service,
		`u_agency`=:u_agency,
		`sender_service`=:sender_service,
		`technician`=:technician,
		`t_group`=:t_group,
		`title`=:title,
		`description`=:description,
		`date_create`=:date_create,
		`date_hope`=:date_hope,
		`date_res`=:date_res,
		`priority`=:priority,
		`criticality`=:criticality,
		`state`=:state,
		`time`=:time,
		`time_hope`=:time_hope,
		`category`=:category,
		`subcat`=:subcat,
		`techread`=:techread,
		`techread_date`=:techread_date,
		`place`=:place,
		`asset_id`=:asset_id,
		`start_availability`=:start_availability,
		`end_availability`=:end_availability,
		`availability_planned`=:availability_planned
		WHERE `id`=:id");
		$qry->execute(array(
			'user' => $_POST['user'],
			'type' => $_POST['type'],
			'type_answer' => $_POST['type_answer'],
			'u_group' => $u_group,
			'u_service' => $u_service,
			'u_agency' => $_POST['u_agency'],
			'sender_service' => $_POST['sender_service'],
			'technician' => $_POST['technician'],
			't_group' => $t_group,
			'title' => $rright['ticket_title']==0 ? $globalrow['title'] : $_POST['title'],
			'description' => $_POST['description'],
			'date_create' => $_POST['date_create'],
			'date_hope' => $_POST['date_hope'],
			'date_res' => $_POST['date_res'],
			'priority' => $_POST['priority'],
			'criticality' => $_POST['criticality'],
			'state' => $_POST['state'],
			'time' => $_POST['time'],
			'time_hope' => $_POST['time_hope'],
			'category' => $_POST['category'],
			'subcat' => $_POST['subcat'],
			'techread' => $techread,
			'techread_date' => $techread_date,
			'place' => $_POST['ticket_places'],
			'asset_id' => $_POST['asset_id'],
			'start_availability' => $start_availability,
			'end_availability' => $end_availability,
			'availability_planned' => $_POST['availability_planned'],
			'id' => $_GET['id']
			));
		$qry=$db->prepare("UPDATE `dmission_order` 
			SET `title`=:title,`guest_name`=:guest_name,`guest_mail`=:guest_mail,`guest_birthdate`=:guest_birthdate,`guest_phone_number`=:guest_phone_number,`guest_labo`=:guest_labo,`guest_country`=:guest_country
			WHERE `incident_id`=:id");
		$qry->execute(array(
			'title' => $rright['ticket_title']==0 ? $globalrow['title'] : $_POST['title'],
			'guest_name' => $_POST['guest_name'],
			'guest_mail'=> $_POST['guest_mail'],
			'guest_birthdate'=> $_POST['guest_birthdate'],
			'guest_phone_number'=> $_POST['guest_phone_number'],
			'guest_labo'=> $_POST['guest_labo'],
			'guest_country'=> $_POST['guest_country'],
			'id' => $_GET['id']
		));
		if(isset($_POST['guest_mail']) && $globalrow['guest_mail'] != $_POST['guest_mail']){
			$qry = $db->prepare("SELECT `id` FROM `dmission_order` WHERE `incident_id`=:id");
			$qry->execute(array('id' => $_GET['id']));
			if($qry->rowCount()){
				$row = $qry->fetch();
				$missionOrder = new \Models\Request\MissionOrder\MissionOrder();
				$missionOrder->setId($row['id'])->load()->sendNotificationToGuestIfItExists();
			}
		}
	}
	//threads text generation
	if(!$error && $_POST['resolution'] && ($_POST['resolution']!="'<br>'") && ($_POST['resolution']!='\'\''))
	{
		if($_GET['threadedit'])
		{
			//get author from thread
			$qry=$db->prepare("SELECT `author` FROM `tthreads` WHERE id=:id");
			$qry->execute(array('id' => $_GET['threadedit']));
			$row=$qry->fetch();
			$qry->closeCursor();

			//check your own ticket for update thread right
			if($row['author']==$_SESSION['user_id'])
			{
				if($rright['ticket_thread_edit']) {
					$qry=$db->prepare("UPDATE `tthreads` SET `text`=:text WHERE `id`=:id");
					$qry->execute(array('text' => $_POST['resolution'],'id' => $_GET['threadedit']));
				}
			} else {
				if($rright['ticket_thread_edit_all']) {
					$qry=$db->prepare("UPDATE `tthreads` SET `text`=:text WHERE `id`=:id");
					$qry->execute(array('text' => $_POST['resolution'],'id' => $_GET['threadedit']));
				}
			}
		}elseif($_POST['resolution']) {
			//generate new thread for this ticket
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`private`) VALUES (:ticket,:date,:author,:text,'0',:private)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id'],'text' => $_POST['resolution'],'private' => $_POST['private']));
			//unread for user if technician add comment
			if($_POST['technician']==$_SESSION['user_id'])
			{
				$qry=$db->prepare("UPDATE `tincidents` SET `userread`='0' WHERE `id`=:id");
				$qry->execute(array('id' => $_GET['id']));
			}
		}
	}

	//threads insert close state
	if(!$error && $_POST['state']=='3' && $globalrow['state']!='3')
	{
		$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,:author,'4')");
		$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id']));
	}

	//uploading files
	include "./core/upload.php";//!\ modifier par nous - ancienne methode sans ajax

	// update observers for request
    if(!$error)
    {
			if(isset($_POST['observer']) && $_POST['observer']!='')
			{
				$ticket = new Ticket();
        $ticket->setId($_GET['id']);

        $observers = [];
        foreach ($_POST['observer'] as $idObserver) {
            $observer = new User();
            $observer->setId($idObserver);
            $observers[] = $observer;
        }
        $ticket
            ->setObservers($observers)
            ->deleteObservers()
            ->updateObservers();
			}


    }

	//auto send mail
	if(!$error)
	{
		if(($rparameters['mail_auto_user_newticket']==1) || ($rparameters['survey']==1) || ($rparameters['mail_auto']==1) || ($rparameters['mail_auto_user_modify']==1) || ($rparameters['mail_auto_tech_modify']==1) || ($rparameters['mail_auto_tech_attribution']==1) || ($rparameters['mail_auto_tech_modify']==1) || ($rparameters['mail_newticket']==1) && ($_POST['upload']=='')){
			if($rparameters['mail'] && $rparameters['mail_smtp'])
			{
				include('./core/auto_mail.php');
			} else {
				echo '<div class="alert alert-block alert-danger"><i class="fa fa-times red"></i> <b>'.T_('Erreur').' : </b> '.T_("Le connecteur SMTP n'est pas configuré.").'</div>';
			}
		} else if ($parameters->isNotificationEnable() && $parameters->getNotificationState() == $_POST['state']) {
            //case send mail to user where ticket reach final status
            //insert mail table
            $qry=$db->prepare("INSERT INTO `tmails` (`incident`,`open`,`close`) VALUES (:incident,'1','0')");
            $qry->execute(array('incident' => $_GET['id']));

            if($rparameters['mail'] && $rparameters['mail_smtp'])
            {
                include('./core/auto_mail.php');
            } else {
                echo '<div class="alert alert-block alert-danger"><i class="fa fa-times red"></i> <b>'.T_('Erreur').' : </b> '.T_("Le connecteur SMTP n'est pas configuré.").'</div>';
            }
        }
	}

	//display message
	if(!$error)
	{
		echo DisplayMessage('success',T_('Ticket sauvegardé'));
		if($_GET['action']=='new') {$hide_button=1;} //case press save button during saving ticket
	} else {
		echo DisplayMessage('error',$error);
	}

	//redirect to ticket list for quit or send button
	if(!$error && ($_POST['quit'] || $_POST['send']))
	{
		echo '<script language="Javascript">
		<!--
		document.location.replace("./index.php?page=dashboard&'.$url_get_parameters.'");
		-->
		</script>';
	}

	//send mail
	if(!$error && $_POST['mail'])
	{
		//redirect to preview mail page
		$url="./index.php?page=preview_mail&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]&category=$_GET[category]&subcat=$_GET[subcat]&viewid=$_GET[viewid]&view=$_GET[view]&date_start=$_GET[date_start]&date_end=$_GET[date_end]";
		$url=preg_replace('/%/','%25',$url);
		$url=preg_replace('/%2525/','%25',$url);
		echo '
		<script language="Javascript">
			<!--
			document.location.replace("'.$url.'");
			// -->
		</script>
		';
	}

    if(!$error && !$_POST['addcalendar'] && !$_POST['addevent'])
    {
		//global redirect on current ticket
		if($_GET['token'] == '')
			$url="./index.php?page=ticket&id=$_GET[id]&action=$_POST[action]&edituser=$_POST[edituser]&cat=$_POST[category]&editcat=$_POST[subcat]&$url_get_parameters$down";
		else
			$url="./index.php?token=".$_GET['token'];
		$url=preg_replace('/%/','%25',$url);
		$url=preg_replace('/%2525/','%25',$url);
		echo "
		<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
				window.location='$url'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
		</SCRIPT>";
    }

	//modify ticket state on close ticket button
	if(!$error && $_POST['close'] && $rright['ticket_close']!=0)
	{
		//update tincidents
		$qry=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res WHERE `id`=:id");
		$qry->execute(array('date_res' => $datetime,'id' => $_GET['id']));

		//auto send mail
		$_POST['state']=3;
		if($rparameters['mail_auto'] && !$_POST['upload']){include('core/auto_mail.php');}

		if($_SESSION['profile_id']!=0 && $_SESSION['profile_id']!=4)
		{
			//unread ticket for technician only if user close ticket
			$qry=$db->prepare("UPDATE `tincidents` SET `techread`='0' WHERE `id`=:id");
			$qry->execute(array('id' => $_GET['id']));
		}
		//update thread
		$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`type`,`author`) VALUES (:ticket,:date,'4',:author)");
		$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $_SESSION['user_id']));
		//redirect to tickets list
		echo "
		<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
				window.location='./index.php?page=dashboard&$url_get_parameters'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
		</SCRIPT>";
	}
}
//redirect to tickets list
if($_POST['cancel'])
{
	echo DisplayMessage('success',T_('Annulation pas de modification'));

	echo "
	<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='./index.php?page=dashboard&$url_get_parameters'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//read ticket technician
if(($globalrow['techread']=="0")&&($globalrow['technician']==$_SESSION['user_id']))
{
	$current_date_hour=date("Y-m-d H:i:s");
	$qry=$db->prepare("UPDATE `tincidents` SET `techread`='1',`techread_date`=:techread_date WHERE `id`=:id");
	$qry->execute(array('techread_date' => $current_date_hour,'id' => $_GET['id']));
}

//read ticket user
if(($globalrow['userread']=="0")&&($globalrow['user']==$_SESSION['user_id']))
{
	$qry=$db->prepare("UPDATE `tincidents` SET `userread`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['id']));
}

if($rright['ticket_next']!=0 && !$mobile && $_GET['action']!='new')
{
	//find next ticket
	$qry=$db->prepare("SELECT MIN(id) FROM `tincidents` WHERE id > :id AND id IN (SELECT id FROM tincidents WHERE technician=:technician AND state=:state AND id NOT LIKE :id) AND disable='0'");
	$qry->execute(array('id' => $_GET['id'],'technician' => $_SESSION['user_id'],'state' => $globalrow['state']));
	$next=$qry->fetch();
	$qry->closeCursor();
	//find previous ticket
	$qry=$db->prepare("SELECT MIN(id) FROM `tincidents` WHERE id < :id AND id IN (SELECT id FROM tincidents WHERE technician=:technician AND state=:state AND id NOT LIKE :id) AND disable='0'");
	$qry->execute(array('id' => $_GET['id'],'technician' => $_SESSION['user_id'],'state' => $globalrow['state']));
	$previous=$qry->fetch();
	$qry->closeCursor();
}

//calculate percentage of ticket resolution
if($globalrow['time_hope']!=0 && ($rright['ticket_time_disp']!=0 && $rright['ticket_time_hope_disp']!=0))
{
	$percentage=($globalrow['time']*100)/$globalrow['time_hope'];
	$percentage=round($percentage);
	if(($globalrow['time']!='1') && ($globalrow['time_hope']!='1') && ($globalrow['time_hope']>=$globalrow['time'])) {$percentage=' <span title="'.T_("Pourcentage d'avancement du ticket basé sur le temps passé et estimé").'">('.$percentage.'%)</span> ';} else {$percentage='';}
}

//cut title for long case
$nbtitle=strlen($globalrow['title']);
if($nbtitle>50)
{
	$title=substr($globalrow['title'], 0, 50);
	$title=$title.'...';
} else {$title=$globalrow['title'];}
?>
