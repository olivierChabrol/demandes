<?php
################################################################################
# @Name : /core/auto_mail.php
# @Description : page to send automatic mail
# @Call : ./core/ticket.php
# @Parameters : ticket id
# @Author : Flox
# @Update : 11/09/2020
# @Version : 3.2.4 p4
################################################################################

require_once('models/tool/parameters.php');
require_once('models/request/ticket/ticket.php');//!\AJOUTER PAR NOS SOINS

use Models\Tool\Parameters;
use Models\Request\Ticket\Ticket;//!\AJOUTER PAR NOS SOINS

$parameters = Parameters::getInstance();
//!\ AJOUTER PAR NOS SOINS
$ticket = new Ticket();
$ticket
		->setId((int) $_GET['id'])
		->loadObservers();
//!\FIN AJOUT
//initialize variables
if(!isset($send)) $send = '';
if(!isset($usermail['mail'])) $usermail['mail'] = '';
if(!isset($_POST['resolution'])) $_POST['resolution'] = '';
if(!isset($_POST['private'])) $_POST['private'] = '';
if(!isset($_POST['modify'])) $_POST['modify']= '';
if(!isset($_POST['quit'])) $_POST['quit'] = '';
if(!isset($_POST['send'])) $_POST['send'] = '';
if(!isset($autoclose)) $autoclose = 0; //call from cron job


//secure string
$db_id=strip_tags($db->quote($_GET['id']));

//check if mail is already sent
$qry = $db->prepare("SELECT `open`,`close` FROM `tmails` WHERE incident=:id");
$qry->execute(array('id' => $_GET['id']));
$mail_send=$qry->fetch();
$qry->closeCursor();

//check user group defined as sender on ticket
$qry = $db->prepare("SELECT u_group FROM `tincidents` WHERE tincidents.id=:id");
$qry->execute(array('id' => $_GET['id']));
$mail_u_group=$qry->fetch();
$qry->closeCursor();
if($mail_u_group['u_group']!=0)
{

	//check if group members have mail
	$qry = $db->prepare("SELECT `tusers`.mail FROM `tusers`,`tgroups_assoc` WHERE `tusers`.id=`tgroups_assoc`.user AND `tgroups_assoc`.group=:group AND `tusers`.disable='0'");
	$qry->execute(array('group' => $mail_u_group['u_group']));
	$mail_u_group_members=$qry->fetch();
	$qry->closeCursor();
	if($mail_u_group_members)
	{
		$usermail['mail']=1;
		if($rparameters['debug']) {echo "<b>AUTO MAIL SENDER:</b> group detected with mail adresses <br />";}
	} else {
		if($rparameters['debug']) {echo "<b>AUTO MAIL SENDER:</b> group detected without mail adresses <br />";}
	}

} else {
	//check if user have mail
	$qry = $db->prepare("SELECT tusers.mail FROM `tusers`,`tincidents` WHERE tincidents.user=tusers.id AND tincidents.id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$usermail=$qry->fetch();
	$qry->closeCursor();
	if($usermail && $rparameters['debug']) {echo "<b>AUTO MAIL SENDER:</b> user detected <br />";}
}

//debug
if($rparameters['debug']) {echo "<b>AUTO MAIL VAR:</b> SESSION[profile_id]=$_SESSION[profile_id] mail_auto_user_modify=$rparameters[mail_auto_user_modify] _POST[resolution]=$_POST[resolution] _POST[private]=$_POST[private] <br />";}

//case send auto mail to tech when technician attribution
if(($rparameters['mail_auto_tech_attribution']==1) && (($_POST['send'] || $_POST['modify'] || $_POST['quit']) && (($globalrow['technician']!=$_POST['technician']) || ($t_group)) && ($_POST['technician']!=$_SESSION['user_id'])))
{
	//debug
	if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM system TO tech  (Reason: mail_auto_tech_attribution ticket technician attribution is detected)<br> ";}

	if($rparameters['mail_from_adr']){$from=$rparameters['mail_from_adr'];} else {$from=$ruser['mail'];}

	//technician group detection
	if($t_group)
	{
		$to='';
		//check group change or attribution
		if($t_group!=$globalrow['t_group'])
		{
			$qry2=$db->prepare("SELECT `tusers`.mail FROM `tusers`,`tgroups_assoc` WHERE `tusers`.id=`tgroups_assoc`.user AND `tgroups_assoc`.group=:group AND `tusers`.disable='0' ");
			$qry2->execute(array('group' => $t_group));
			while($row2=$qry2->fetch()) {$to.=$row2['mail'].';';}
			$qry2->closeCursor();
		}
	} else {
		//get tech mail
		$qry = $db->prepare("SELECT `mail` FROM tusers WHERE id=:id");
		$qry->execute(array('id' => $_POST['technician']));
		$techrow=$qry->fetch();
		$qry->closeCursor();
		$to=$techrow['mail'];
	}
	$dest_mail=$to;

	//check if tech have mail
	if($to)
	{
		$object=T_('Le ticket').' n°'.$_GET['id'].' : '.$_POST['title'].' '.T_('vous a été attribué');
		//remove single quote in post data
		$description = str_replace("'", "", $_POST['description']);
		$title = str_replace("'", "", $_POST['title']);
		//get user name
		$qry = $db->prepare("SELECT `firstname`,`lastname` FROM tusers WHERE id=:id");
		$qry->execute(array('id' => $_POST['user']));
		$userrow=$qry->fetch();
		$qry->closeCursor();
		$message = '
		'.T_('Le ticket').' n°'.$_GET['id'].' '.T_('vous a été attribué').' <br />
		<br />
		<u>'.T_('Demandeur').' :</u><br />
		'.$userrow['firstname'].' '.$userrow['lastname'].'<br />
		<br />
		<u>'.T_('Objet').' :</u><br />
		'.$title.'<br />
		<br />
		<u>'.T_('Description').' :</u><br />
		'.$description.'<br />
		<br />
		'.T_("Pour plus d'informations vous pouvez consulter le ticket sur").' <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>.
		';
		$mail_auto=true;
		require('./core/message.php');
		if($mail_send) { //insert thread
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`dest_mail`) VALUES (:ticket,:date,'0','','3',:dest_mail)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => date('Y-m-d H:i:s'),'dest_mail' => $dest_mail));
		}
	} else {if($rparameters['debug']) {echo "technician mail is empty or no technician associated or tech group no change on this ticket";}}
}

//case send mail to user where ticket open by technician.
if(isset($rparameters['mail_auto']) && ($rparameters['mail_auto']==1) && ((($mail_send['open']=='') && ($_POST['modify'] || $_POST['quit']) && ($_SESSION['profile_id']!=2 && $_SESSION['profile_id']!=3 && $_SESSION['profile_id']!=1)) || $autoclose==1) && !$_POST['private'])
{
	if($usermail['mail'] || $rparameters['mail_cc'])
	{
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and open detect by technician.) <br />";}
		//auto send open notification mail
		$send=1;
		$mail_auto=true;
		include('./core/mail.php');
		//insert mail table
		$qry=$db->prepare("INSERT INTO `tmails` (`incident`,`open`,`close`) VALUES (:incident,'1','0')");
		$qry->execute(array('incident' => $_GET['id']));
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and open detect by technician.) but user have no mail and mail_cc empty message not sent<br />";}
	}
//case send mail to user where ticket close by technician.
} elseif(($rparameters['mail_auto']==1) && ($_POST['state']=='3') && ($_POST['modify'] || $_POST['quit'] || $_POST['close']) && ($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4))
{
	if($usermail['mail'] || $rparameters['mail_cc'])
	{
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and close detect by technician.)<br />";}

		if ($mail_send['open']=='1')
		{
			//check if is the first close mail
			if ($mail_send['close']=='0')
			{
				$send=1;
				$mail_auto=true;
				//auto send close notification mail
				include('./core/mail.php');
				//update mail table
				$qry=$db->prepare("UPDATE tmails SET close='1' WHERE incident=:incident");
				$qry->execute(array('incident' => $_GET['id']));
			} else {
				//close mail already sent
			}
		} else {
			//close not sent because no open mail was sent
		}
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and close detect by technician.) but user have no mail and mail_cc empty message not sent<br />";}
	}
}
//send mail to admin where user open new ticket
elseif($rparameters['mail_newticket'] && ($_POST['send'] || $_POST['upload']) && ($_SESSION['profile_id']!=0 && $_SESSION['profile_id']!=4))
{
	//find user name
	$qry = $db->prepare("SELECT tusers.`firstname` AS firstname,tusers.`lastname` AS lastname,tusers.`mail` AS mail,tcompany.`name` AS company FROM tusers,tcompany WHERE tusers.company=tcompany.id AND tusers.id=:id");
	$qry->execute(array('id' => $uid));
	$userrow=$qry->fetch();
	$qry->closeCursor();

	//mail parameters
	if(!$rparameters['mail_from_adr'])
	{
		if($userrow['mail']) {$from=$userrow['mail'];} else {$from=$rparameters['mail_cc'];}
	} else {
		$from=$rparameters['mail_from_adr'];
	}

	if($rparameters['mail_newticket_address'])
	{
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM user TO tech OR parameter_cc (Reason: mail_newticket enable and user open ticket.<br> ";} //debug
		if($userrow['company']){$company_name=' '.T_('de la société').' '.$userrow['company'];} else {$company_name='';}
		$to=$rparameters['mail_newticket_address'];
		$object=T_('Un nouveau ticket a été déclaré par ').$userrow['lastname'].' '.$userrow['firstname'].$company_name.' : '.$_POST['title'];
		$message = '
		'.T_('Le ticket').' n°'.$_GET['id'].' '.T_("a été déclaré par l'utilisateur").' '.$userrow['lastname'].' '.$userrow['firstname'].$company_name.'.<br />
		<br />
		<u>'.T_('Objet').' :</u><br />
		'.$_POST['title'].'<br />
		<br />
		<u>'.T_('Description').' :</u><br />
		'.$_POST['text'].'<br />
		<br />
		'.T_("Pour plus d'informations vous pouvez consulter le ticket sur").' <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>.
		';
		$mail_auto=true;
		require('./core/message.php');
		if($mail_send) { //insert thread
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`dest_mail`) VALUES (:ticket,:date,'0','','3',:dest_mail)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => date('Y-m-d H:i:s'),'dest_mail' => $to));
		}
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM user TO tech OR parameter_cc (Reason: mail_newticket enable and user open ticket, message not sent no administrator mail specified<br> ";}
	}

}
//case send mail to user and observers where technician add thread in ticket.
elseif (($rparameters['mail_auto_user_modify']==1) && ($_POST['resolution']!='') && ($_POST['resolution']!='\'\'') &&  ($_POST['private']!=1) && ($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4) && !empty($globalrow['user']))
{
	if($usermail['mail'] || $rparameters['mail_cc'])
	{

		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM tech TO user (Reason: mail_auto_user_modify enable and technician add thread.<br> ";}
		//check if user is the technician and not user
		if ($globalrow['user']!=$_SESSION['user_id'])
		{
			$_POST['withattachment']=1;
			$send=1;
			$mail_auto=true;
			include('./core/mail.php');
		}
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM tech TO user (Reason: mail_auto_user_modify enable and technician add thread) but user have no mail and mail_cc empty, message not sent.<br> ";}
	}
}
//!\ AJOUTER PAR NOS SOINS
//case send mail to user and technician when observer add thread in ticket.
elseif(($rparameters['mail_auto_user_modify']==1) && ($_POST['resolution']!='') && ($_POST['resolution']!='\'\'') &&  ($_POST['private']!=1) && $ticket->hasObserver($_SESSION['user_id']))
{
	//debug
	if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM observer TO user and tech (Reason: mail_auto_tech_modify enable and observer add thread in ticket.)<br> ";}
	//find user name
	//!\ OLD - $qry = $db->prepare("SELECT `mail` FROM tusers WHERE id=:id");
	/*/!\ Modifier par nos soins */ $qry = $db->prepare("SELECT * FROM tusers WHERE id=:id");
	$qry->execute(array('id' => $uid));
	$userrow=$qry->fetch();
	$qry->closeCursor();

	//define from mail
	if(!$rparameters['mail_from_adr'])
	{
		if($userrow['mail']) {$from=$userrow['mail'];} else {$from=$rparameters['mail_cc'];}
	} else {
		$from=$rparameters['mail_from_adr'];
	}
	//check if it's technician or technician group
	$send_it=0;
	$to='';
	if($t_group)
	{
		$qry=$db->prepare("SELECT `tusers`.mail FROM `tusers`,`tgroups_assoc` WHERE `tusers`.id=`tgroups_assoc`.user AND `tgroups_assoc`.group=:group AND `tusers`.disable='0' ");
		$qry->execute(array('group' => $t_group));
		while($row=$qry->fetch()) {$to.=$row['mail'].';';}
		$qry->closeCursor();
		$send_it=1;
	} else {
		//get tech mail
		//!\ OLD - $qry = $db->prepare("SELECT `id`,`mail` FROM tusers WHERE id=:id");
		/*/!\ Modifier par nos soins */$qry = $db->prepare("SELECT * FROM tusers WHERE id=:id");
		$qry->execute(array('id' => $globalrow['technician']));
		$techrow=$qry->fetch();
		$qry->closeCursor();
		$to=$techrow['mail'];
		if($techrow['id']!=$_SESSION['user_id']) {$send_it=1;}
	}

	//check if tech have mail
	if($to && $send_it)
	{
		//add user mail
		$to.=';'.$usermail['mail'];

		$object=T_('Votre ticket').' n°'.$_GET['id'].' : '.$_POST['title'].' '.T_('a été modifié');
		//remove single quote in post data
		$resolution = str_replace("'", "", $_POST['resolution']);
		$title = str_replace("'", "", $_POST['title']);
		//!\ old
		/*$message = '
		'.T_('Le ticket').' n°'.$_GET['id'].' '.T_('a été modifié ').' <br />
		<br />
		<u>'.T_('Objet').' :</u><br />
		'.$title.'<br />
		<br />
		<u>'.T_('Ajout du commentaire').' :</u><br />
		'.$resolution.'<br />
		<br />
		'.T_("Pour plus d'informations vous pouvez consulter le ticket sur").' <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>.
		';*/
		//!\ AJOUTER PAR NOS SOINS
		$message='';
		require('./core/mail2template.php');
		//!\ FIN AJOUT
		$mail_auto=true;
		require('./core/message.php');
		if($mail_send) { //insert thread
			if(is_array($to)) {$to=implode(' ',$to);}
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`dest_mail`) VALUES (:ticket,:date,'0','','3',:dest_mail)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => date('Y-m-d H:i:s'),'dest_mail' => $to));
		}
	} else {if($rparameters['debug']) {echo "technician mail is empty or no technician associated with this ticket";}}

}
//!\ FIN AJOUT
//send mail to technician and observers where an user add thread in ticket
elseif (($rparameters['mail_auto_tech_modify']==1) && ($_POST['modify'] || $_POST['quit'] || $_POST['upload']) && (($_POST['resolution']!='') && ($_POST['resolution']!='\'\'')))
{
	//debug
	if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b>  FROM user TO tech  (Reason: mail_auto_tech_modify enable and user add thread in ticket.)<br> ";}

	//find user name
	//!\ OLD - $qry = $db->prepare("SELECT `mail` FROM tusers WHERE id=:id");
	/*/!\ Modifier par nos soins */ $qry = $db->prepare("SELECT * FROM tusers WHERE id=:id");
	$qry->execute(array('id' => $uid));
	$userrow=$qry->fetch();
	$qry->closeCursor();

	//define from mail
	if(!$rparameters['mail_from_adr'])
	{
		if($userrow['mail']) {$from=$userrow['mail'];} else {$from=$rparameters['mail_cc'];}
	} else {
		$from=$rparameters['mail_from_adr'];
	}


	//check if it's technician or technician group
	$send_it=0;
	$to='';
	if($t_group)
	{
		$qry=$db->prepare("SELECT `tusers`.mail FROM `tusers`,`tgroups_assoc` WHERE `tusers`.id=`tgroups_assoc`.user AND `tgroups_assoc`.group=:group AND `tusers`.disable='0' ");
		$qry->execute(array('group' => $t_group));
		while($row=$qry->fetch()) {$to.=$row['mail'].';';}
		$qry->closeCursor();
		$send_it=1;
	} else {
		//get tech mail
		//!\ OLD - $qry = $db->prepare("SELECT `id`,`mail` FROM tusers WHERE id=:id");
		/*/!\ Modifier par nos soins */$qry = $db->prepare("SELECT * FROM tusers WHERE id=:id");
		$qry->execute(array('id' => $globalrow['technician']));
		$techrow=$qry->fetch();
		$qry->closeCursor();
		$to=$techrow['mail'];
		if($techrow['id']!=$_SESSION['user_id']) {$send_it=1;}
	}

	//check if tech have mail
	if($to && $send_it)
	{
		//!\AJOUTER PAR NOS SOINS
		//get observers mail
		$obsmails=$ticket->getObserversMail();
		if(!empty($obsmails)){
			foreach($obsmails as $obsmail)
			{
				$to.=";".$obsmail;
			}
		}
		//!\FIN AJOUT

		$object=T_('Votre ticket').' n°'.$_GET['id'].' : '.$_POST['title'].' '.T_('a été modifié');
		//remove single quote in post data
		$resolution = str_replace("'", "", $_POST['resolution']);
		$title = str_replace("'", "", $_POST['title']);
		/*/!\ - old 
		$message = '
		'.T_('Le ticket').' n°'.$_GET['id'].' '.T_('a été modifié ').' <br />
		<br />
		<u>'.T_('Objet').' :</u><br />
		'.$title.'<br />
		<br />
		<u>'.T_('Ajout du commentaire').' :</u><br />
		'.$resolution.'<br />
		<br />
		'.T_("Pour plus d'informations vous pouvez consulter le ticket sur").' <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>.
		';*/
		//!\ AJOUTER PAR NOS SOINS
		$message='';
		require('./core/mail2template.php');
		//!\ FIN AJOUT
		$mail_auto=true;
		require('./core/message.php');
		if($mail_send) { //insert thread
			if(is_array($to)) {$to=implode(' ',$to);}
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`dest_mail`) VALUES (:ticket,:date,'0','','3',:dest_mail)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => date('Y-m-d H:i:s'),'dest_mail' => $to));
		}
	} else {if($rparameters['debug']) {echo "technician mail is empty or no technician associated with this ticket";}}
}

//case send auto mail to user where user open ticket
if(($rparameters['mail_auto_user_newticket']==1) && ($mail_send['open']=='') && $_GET['action']=='new' && ($_POST['send'] || $_POST['modify'] || $_POST['quit']|| $_POST['upload']) && ($_SESSION['profile_id']==2 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==1))
{
	if($usermail['mail'] || $rparameters['mail_cc'])
	{
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM user TO user (Reason: mail_auto_user_newticket enable, and open detect by user.) <br />";}
		//auto send open notification mail
		$send=1;
		$mail_auto=true;
		include('./core/mail.php');
		//insert mail table
		$qry=$db->prepare("INSERT INTO `tmails` (`incident`,`open`,`close`) VALUES (:incident,'1','0')");
		$qry->execute(array('incident' => $_GET['id']));
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM user TO user (Reason: mail_auto_user_newticket enable, and open detect by user.) but user have no mail and mail_cc empty message not sent<br />";}
	}
}

//send mail to user from tech for survey where ticket is in survey parameter state
if(($rparameters['survey']==1) && ($_POST['modify'] || $_POST['quit'] || $_POST['close']) && ($_POST['state']==$rparameters['survey_ticket_state']))
{
	if($usermail['mail'])
	{
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: survey enable and technician switch ticket in state $rparameters[survey_ticket_state].)<br> ";}
		//check if survey answer already exist for this ticket
		$qry2 = $db->prepare("SELECT ticket_id FROM tsurvey_answers WHERE ticket_id=:ticket_id");
		$qry2->execute(array('ticket_id' => $_GET['id']));
		$row2=$qry2->fetch();
		$qry2->closeCursor();
		if(!$row2)
		{
			//insert a token
			$token=uniqid();
			$qry=$db->prepare("INSERT INTO ttoken (token,action,ticket_id) VALUES (:token,'survey',:ticket_id)");
			$qry->execute(array('token' => $token,'ticket_id' => $_GET['id']));
			//select sender address
			if($rparameters['mail_from_adr']=='')
			{
				//get tech mail
				$qry = $db->prepare("SELECT mail FROM tusers WHERE id=(SELECT technician FROM tincidents WHERE id=:id)");
				$qry->execute(array('id' => $_GET['id']));
				$from_adr=$qry->fetch();
				$from_adr=$from_adr['mail'];
				$qry->closeCursor();

			} else {
				$from_adr=$rparameters['mail_from_adr'];
			}
			//add ticket link if ticket tag detected
			$rparameters['survey_mail_text']=str_replace("[ticket_link]","<a target=\"_blank\" href=\"$rparameters[server_url]/index.php?page=ticket&id=$_GET[id]\">$_GET[id]</a>", $rparameters['survey_mail_text']);

			if($rparameters['mail_from_adr']!='') {$from=$rparameters['mail_from_adr'];} else {$from=$from_adr;}
			$to=$usermail['mail'];
			$object=T_("Sondage concernant votre ticket n°").$_GET['id'];
			$message=$rparameters['survey_mail_text'].'
			<br />
			<a href="'.$rparameters['server_url'].'/survey.php?token='.$token.'">'.T_('Répondre au sondage').'</a>
			';
			$mail_auto=true;
			require('./core/message.php');
		}
	} else {
		//debug
		if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: survey enable and technician switch ticket in state $rparameters[survey_ticket_state].) but user have no mail, message not sent.<br> ";}
	}
}

//case send mail to user where ticket reach final status
if($parameters->isNotificationEnable() && $parameters->getNotificationState() == $_POST['state'])
{
	if($usermail['mail'] || $rparameters['mail_cc'])
    {
        //debug
        if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and final status detect by technician.)<br />";}

        if ($mail_send['open']=='1')
        {
            //check if is the first close mail
            if ($mail_send['close']=='0')
            {
                $send=1;
                $mail_auto=true;
                //auto send close notification mail
                include('./core/mail.php');
                //update mail table
                $qry=$db->prepare("UPDATE tmails SET close='1' WHERE incident=:incident");
                $qry->execute(array('incident' => $_GET['id']));
            } else {
                //close mail already sent
            }
        } else {
            //close not sent because no open mail was sent
        }
    } else {
        //debug
        if($rparameters['debug']) {echo "<b>AUTO MAIL DETECT:</b> FROM tech TO user (Reason: mail_auto enable, and final status detect by technician.) but user have no mail and mail_cc empty message not sent<br />";}
    }
}
?>
