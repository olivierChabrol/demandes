<?php
################################################################################
# @Name : /core/mail.php
# @Description : page to send mail
# @Call : /preview_mail.php, /core_automail.php
# @Parameters : ticket id destinataires
# @Author : Flox
# @Create : 15/07/2014
# @Update : 21/08/2020
# @Version : 3.2.4 p1
################################################################################

require_once('core/functions.php');

//initialize variables
if(!isset($_POST['usercopy'])) $_POST['usercopy'] = '';
if(!isset($_POST['usercopy2'])) $_POST['usercopy2'] = '';
if(!isset($_POST['usercopy3'])) $_POST['usercopy3'] = '';
if(!isset($_POST['usercopy4'])) $_POST['usercopy4'] = '';
if(!isset($_POST['usercopy5'])) $_POST['usercopy5'] = '';
if(!isset($_POST['usercopy6'])) $_POST['usercopy6'] = '';
if(!isset($_POST['manual_address'])) $_POST['manual_address'] = '';
if(!isset($_POST['usercopy_cci'])) $_POST['usercopy_cci'] = '';
if(!isset($_POST['usercopy2_cci'])) $_POST['usercopy2_cci'] = '';
if(!isset($_POST['usercopy3_cci'])) $_POST['usercopy3_cci'] = '';
if(!isset($_POST['usercopy4_cci'])) $_POST['usercopy4_cci'] = '';
if(!isset($_POST['usercopy5_cci'])) $_POST['usercopy5_cci'] = '';
if(!isset($_POST['usercopy6_cci'])) $_POST['usercopy6_cci'] = '';
if(!isset($_POST['manual_address_cci'])) $_POST['manual_address_cci'] = '';
if(!isset($_POST['receiver'])) $_POST['receiver'] = '';
if(!isset($_POST['withattachment'])) $_POST['withattachment'] = '';
if(!isset($_GET['state'])) $_POST['state'] = '';
if(!isset($_GET['userid'])) $_POST['userid'] = '';
if(!isset($_GET['view'])) $_POST['view'] = '';
if(!isset($_GET['date_start'])) $_POST['date_start'] = '';
if(!isset($_GET['date_end'])) $_POST['date_end'] = '';
if(!isset($fname11)) $fname11 = '';
if(!isset($fname21)) $fname21 = '';
if(!isset($fname31)) $fname31 = '';
if(!isset($fname41)) $fname41 = '';
if(!isset($fname51)) $fname51 = '';
if(!isset($resolution)) $resolution = '';
if(!isset($lastComment)) $lastComment = '';
if(!isset($mail_text_end)) $mail_text_end = '';
if(!isset($rtech4['firstname'])) $rtech4['firstname'] = '';
if(!isset($rtech4['lastname'])) $rtech4['lastname'] = '';
if(!isset($rtech5['firstname'])) $rtech5['firstname'] = '';
if(!isset($rtech5['lastname'])) $rtech5['lastname'] = '';
if(!isset($rtechgroup4['name'])) $rtechgroup4['name'] = '';
if(!isset($rtechgroup5['name'])) $rtechgroup5['name'] = '';
if(!isset($mail_auto)) $mail_auto=true;
if(!isset($creatorrow['mail'])) $creatorrow['mail']='';
if(!isset($placerow['name'])) $placerow['name']='';
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';

$mail_send_error=false;
$db_id=strip_tags($_GET['id']);
$dest_mail=0;

//database queries to find values for create mail
$qry=$db->prepare("SELECT * FROM `tincidents` WHERE `id`=:id");
$qry->execute(array('id' => $db_id));
$globalrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `id`,`mail`,`firstname`,`lastname`,`company` FROM `tusers` WHERE `id`=:id");
$qry->execute(array('id' => $globalrow['user']));
$userrow=$qry->fetch();
$qry->closeCursor();

//!\ AJOUTER PAR NOS SOINS
$obsmails=[];
$qry=$db->prepare("SELECT `id_observer`,`mail` FROM `dticket_observers` LEFT JOIN `tusers` ON `tusers`.`id`=`id_observer` WHERE `dticket_observers`.`id`=:id");
$qry->execute(array('id' => $db_id));
while($row=$qry->fetch()) {$obsmails[]=$row['mail'];}
$qry->closeCursor();
//!\FIN AJOUT

$qry=$db->prepare("SELECT `id`,`mail`,`firstname`,`lastname`,`phone`,`mobile`,`custom1`,`custom2`,`function` FROM `tusers` WHERE id=:id");
$qry->execute(array('id' => $globalrow['technician']));
$techrow=$qry->fetch();
$qry->closeCursor();

$technician_services='';
$qry=$db->prepare("SELECT `name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id");
$qry->execute(array('user_id' => $globalrow['technician']));
while($row=$qry->fetch()) {$technician_services.=$row['name'].' ';}
$qry->closeCursor();

$technician_service='';
$qry=$db->prepare("SELECT `name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id ORDER BY `tusers_services`.`id` LIMIT 1");
$qry->execute(array('user_id' => $globalrow['technician']));
while($row=$qry->fetch()) {$technician_service=$row['name'];}
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE disable='0' AND id=:id");
$qry->execute(array('id' => $userrow['company']));
$companyrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tpriority` WHERE id=:id");
$qry->execute(array('id' => $globalrow['priority']));
$priorityrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tcriticality` WHERE id=:id");
$qry->execute(array('id' => $globalrow['criticality']));
$criticalityrow=$qry->fetch();
$qry->closeCursor();

//group case
if($globalrow['t_group']!=0)
{
	$qry=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['t_group']));
	$grouptech=$qry->fetch();
	$qry->closeCursor();
}
if($globalrow['u_group']!=0)
{
	$qry=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['u_group']));
	$groupuser=$qry->fetch();
	$qry->closeCursor();
}

//case no send mail from mail2ticket
if($_SESSION['user_id'] && !$creatorrow['mail'])
{
	$qry=$db->prepare("SELECT mail FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_SESSION['user_id']));
	$creatorrow=$qry->fetch();
	$qry->closeCursor();
}

$qry=$db->prepare("SELECT `name` FROM `tstates` WHERE id=:id");
$qry->execute(array('id' => $globalrow['state']));
$staterow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
$qry->execute(array('id' => $globalrow['category']));
$catrow=$qry->fetch();
$qry->closeCursor();

$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
$qry->execute(array('id' => $globalrow['subcat']));
$subcatrow=$qry->fetch();
$qry->closeCursor();

//case place parameter
if($rparameters['ticket_places']==1)
{
	$qry=$db->prepare("SELECT `id`,`name` FROM `tplaces` WHERE id=:id");
	$qry->execute(array('id' => $globalrow['place']));
	$placerow=$qry->fetch();
	$qry->closeCursor();
}

//generate resolution
if($rparameters['mail_order']==1) {
	$qry=$db->prepare("SELECT * FROM `tthreads` WHERE ticket=:ticket AND private=0 ORDER BY date DESC");
} else {
	$qry=$db->prepare("SELECT * FROM `tthreads` WHERE ticket=:ticket AND private=0 ORDER BY date ASC");
}
$qry->execute(array('ticket' => $db_id));
$datePrevious = null;
$date = null;
$lastComment = '';
while($row=$qry->fetch())
{
	//remove display date from old post
	$find_old=explode(" ", $row['date']);
	$find_old=$find_old[1];
	if($find_old!='12:00:00') $date_thread=date_convert($row['date']); else $date_thread='';
    $date = new DateTime($row['date']);

	if($row['type']==0)
	{
		//text back-line format
		//$text=nl2br($row['text']); #5129
		$text=$row['text'];

		//test if author is not the technician
		if($row['author']!=$globalrow['technician'])
		{
			//find author name
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['author']));
			$rauthor=$qry2->fetch();
			$qry2->closeCursor();

            $resolution="$resolution <b> $date_thread $rauthor[firstname] $rauthor[lastname] : </b><br /> $text  <hr />";
            if ($date >= $datePrevious) {
                $lastComment="<b> $date_thread $rauthor[firstname] $rauthor[lastname] : </b><br /> $text  <hr />";
                $datePrevious = $date;
            }
		} else {
			if($date_thread!='')
			{
                $resolution="$resolution <b>$date_thread :</b><br />$text<hr />";
                if ($date >= $datePrevious) {
                    $lastComment = "<b>$date_thread :</b><br />$text<hr />";
                    $datePrevious = $date;
                }
			} else {
                $resolution="$resolution  $text <hr />";
                if ($date >= $datePrevious) {
                    $lastComment = "$text <hr />";
                    $datePrevious = $date;
                }
			}
		}
	} elseif($row['type']==1) {
		//generate attribution thread
		if($row['group1']!=0)
		{
			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group1']));
			$rtechgroup=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Attribution du ticket au groupe').' '.$rtechgroup['name'].'.<br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Attribution du ticket au groupe') . ' ' . $rtechgroup['name'] . '.<br /><br />';
                $datePrevious = $date;
            }
		} else {
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech1']));
			$rtech3=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Attribution du ticket à').' '.$rtech3['firstname'].' '.$rtech3['lastname'].'.<br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Attribution du ticket à') . ' ' . $rtech3['firstname'] . ' ' . $rtech3['lastname'] . '.<br /><br />';
                $datePrevious = $date;
            }
		}
	} elseif($row['type']==2) {
		//generate transfert thread
		if($row['group1']!=0 && $row['group2']!=0) //case group to group
		{
			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group1']));
			$rtechgroup1=$qry2->fetch();
			$qry2->closeCursor();

			$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
			$qry2->execute(array('id' => $row['group2']));
			$rtechgroup2=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket du groupe').' '.$rtechgroup1['name'].' '.T_('au groupe ').' '.$rtechgroup2['name'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket du groupe') . ' ' . $rtechgroup1['name'] . ' ' . T_('au groupe ') . ' ' . $rtechgroup2['name'] . '. <br /><br />';
                $datePrevious = $date;
            }
		} elseif(($row['tech1']==0 || $row['tech2']==0) && ($row['group1']==0 || $row['group2']==0)) { //case group to tech
			if($row['tech1']!=0) {
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech1']));
				$rtech4=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['tech2']!=0) {
				$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
				$qry2->execute(array('id' => $row['tech2']));
				$rtech5=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['group1']!=0) {
				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group1']));
				$rtechgroup4=$qry2->fetch();
				$qry2->closeCursor();
			}
			if($row['group2']!=0) {
				$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry2->execute(array('id' => $row['group2']));
				$rtechgroup5=$qry2->fetch();
				$qry2->closeCursor();
			}
            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket de').' '.$rtechgroup4['name'].$rtech4['firstname'].' '.$rtech4['lastname'].' '.T_('à').' '.$rtechgroup5['name'].$rtech5['firstname'].' '.$rtech5['lastname'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket de') . ' ' . $rtechgroup4['name'] . $rtech4['firstname'] . ' ' . $rtech4['lastname'] . ' ' . T_('à') . ' ' . $rtechgroup5['name'] . $rtech5['firstname'] . ' ' . $rtech5['lastname'] . '. <br /><br />';
                $datePrevious = $date;
            }
	} elseif($row['tech1']!=0 && $row['tech2']!=0) { //case tech to tech
			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech1']));
			$rtech1=$qry2->fetch();
			$qry2->closeCursor();

			$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
			$qry2->execute(array('id' => $row['tech2']));
			$rtech2=$qry2->fetch();
			$qry2->closeCursor();

            $resolution=$resolution.' <b>'.$date_thread.' :</b> '.T_('Transfert du ticket de').' '.$rtech1['firstname'].' '.$rtech1['lastname'].' '.T_('à').' '.$rtech2['firstname'].' '.$rtech2['lastname'].'. <br /><br />';
            if ($date >= $datePrevious) {
                $lastComment = ' <b>' . $date_thread . ' :</b> ' . T_('Transfert du ticket de') . ' ' . $rtech1['firstname'] . ' ' . $rtech1['lastname'] . ' ' . T_('à') . ' ' . $rtech2['firstname'] . ' ' . $rtech2['lastname'] . '. <br /><br />';
                $datePrevious = $date;
            }
		}
	}
}
$qry->closeCursor();
$description = $globalrow['description'];

//date conversion
$date_create = date_cnv("$globalrow[date_create]");
$date_hope = date_cnv("$globalrow[date_hope]");
$date_res = date_cnv("$globalrow[date_res]");

if($date_create=='00/00/0000') {$date_create='';}
if($date_hope=='00/00/0000') {$date_hope='';}
if($date_res=='00/00/0000') {$date_res='';}

//generate mail object via db state name
$qry=$db->prepare("SELECT `mail_object` FROM `tstates` WHERE id=:id");
$qry->execute(array('id' => $globalrow['state']));
$robject=$qry->fetch();
$qry->closeCursor();
$object=T_($robject['mail_object']).' '.T_('pour le ticket').' n°'.$db_id.' : '.$globalrow['title'];

//recipient user mail
$recipient=$userrow['mail'];

//check if unique sender mail address exist else get creator mail address
if(!$rparameters['mail_from_adr']){$sender=$creatorrow['mail'];} else {$sender=$rparameters['mail_from_adr'];}

if(!$sender)
{
	echo DisplayMessage('error',T_("Aucune adresse mail d'émission n'est définie").$sender);
	//log
	if($rparameters['log']) {logit('error', 'No sender mail defined', $_SESSION['user_id']);}
	die();
}

//display custom end text mail, else auto generate
if($rparameters['mail_txt_end'])
{
	//generate mail end text
	$mail_text_end=str_replace("[tech_name]", "$techrow[firstname] $techrow[lastname]", $rparameters['mail_txt_end']);
	$mail_text_end=str_replace("[tech_phone]", "$techrow[phone]", $mail_text_end);
	if($rparameters['mail_link'] && $rparameters['server_url']) {
		$link='<a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';
		$mail_text_end=str_replace("[link]", "$link", $mail_text_end);
	}
} else { //auto end mail
	if($rparameters['mail_link'] && $rparameters['server_url']) //integer link parameter
	{
		$link=', '.T_('ou consultez votre ticket sur ce lien').' : <a href="'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'">'.$rparameters['server_url'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';
	} else $link=".";
	if(($techrow['lastname']!='Aucun') && ($techrow['phone']!='')) //case technician phone
	{$mail_text_end=T_('Pour toutes informations complémentaires sur votre ticket, vous pouvez joindre').' '.$techrow['firstname'].' '.$techrow['lastname'].' '.T_('au').' '.$techrow['phone'].' '.$link;}
	elseif($rparameters['mail_link']==1) //case technician no phone
	{$mail_text_end=T_("Vous pouvez suivre l'état d'avancement de votre ticket sur ce lien : ").'<a href="'.$_SERVER['SERVER_NAME'] .'/index.php?page=ticket&id='.$_GET['id'].'">'.$_SERVER['SERVER_NAME'].'/index.php?page=ticket&id='.$_GET['id'].'</a>';}
}

//add tag in mail to split fonction of imap connector
if($rparameters['imap']==1 && $rparameters['imap_reply']==1) {$msg='---- '.T_('Repondre au dessus de cette ligne').' ----';} else {$msg='';}

#template filename definition
$template_filename=__DIR__.'/../'.'template/mail/'.$rparameters['mail_template'];

if(file_exists($template_filename))
{
	//load template
	$mail_template=file_get_contents($template_filename);

	//translate none values
	if($userrow['firstname']=='Aucun') {$userrow['firstname']=T_('Aucun');}
	if($userrow['lastname']=='Aucun') {$userrow['lastname']=T_('Aucun');}
	if($techrow['firstname']=='Aucun') {$techrow['firstname']=T_('Aucun');}
	if($techrow['lastname']=='Aucun') {$techrow['lastname']=T_('Aucun');}
	if($catrow['name']=='Aucune') {$catrow['name']=T_('Aucune');}
	//if($subcatrow['name']=='Aucune') {$subcatrow['name']=T_('Aucune');}
	//if($subcatrow['name']==0) {$subcatrow['name']=T_('Aucune');}
	if($subcatrow['name']) {$subcatrow['name']=T_('Aucune');}

	//replace mail tag
	$mail_template=str_replace('#mail_color_title#', $rparameters['mail_color_title'], $mail_template);
	$mail_template=str_replace('#mail_color_text#', $rparameters['mail_color_text'], $mail_template);
	$mail_template=str_replace('#mail_object#', $object, $mail_template);
	$mail_template=str_replace('#mail_txt#', T_($rparameters['mail_txt']), $mail_template);
	$mail_template=str_replace('#mail_txt_end#', $mail_text_end, $mail_template);
	$mail_template=str_replace('#mail_color_title#', $rparameters['mail_color_title'], $mail_template);
	$mail_template=str_replace('#mail_color_text#', $rparameters['mail_color_text'], $mail_template);
	$mail_template=str_replace('#mail_color_bg#', $rparameters['mail_color_bg'], $mail_template);

	//translate field name
	$mail_template=str_replace('#title#', T_('Titre'), $mail_template);
	$mail_template=str_replace('#category#', T_('Catégorie'), $mail_template);
	$mail_template=str_replace('#user#', T_('Demandeur'), $mail_template);
	$mail_template=str_replace('#technician#', T_('Technicien'), $mail_template);
	$mail_template=str_replace('#state#', T_('État'), $mail_template);
	$mail_template=str_replace('#place#', T_('Lieu'), $mail_template);
	$mail_template=str_replace('#date_create#', T_('Date de la demande'), $mail_template);
	$mail_template=str_replace('#description#', T_('Description'), $mail_template);
    $mail_template=str_replace('#resolution#', T_('Résolution'), $mail_template);
    $mail_template=str_replace('#last_comment#', T_('Dernier commentaire'), $mail_template);
	$mail_template=str_replace('#date_hope#', T_('Date de résolution estimée'), $mail_template);
	$mail_template=str_replace('#date_res#', T_('Date de résolution'), $mail_template);
	$mail_template=str_replace('#company#', T_('Société'), $mail_template);

	//replace ticket tag
	$mail_template=str_replace('#ticket_id#', $globalrow['id'], $mail_template);
	$mail_template=str_replace('#ticket_title#', $globalrow['title'], $mail_template);
	$mail_template=str_replace('#ticket_category#', $catrow['name'], $mail_template);
	$mail_template=str_replace('#ticket_subcat#', $subcatrow['name'], $mail_template);
	if($globalrow['u_group']) {$mail_template=str_replace('#ticket_user#', $groupuser['name'], $mail_template);} else {$mail_template=str_replace('#ticket_user#', $userrow['firstname'].' '.strtoupper($userrow['lastname']), $mail_template);}
	if($globalrow['t_group']) {$mail_template=str_replace('#ticket_technician#', $grouptech['name'], $mail_template);} else {$mail_template=str_replace('#ticket_technician#', $techrow['firstname'].' '.strtoupper($techrow['lastname']), $mail_template);}
	$mail_template=str_replace('#ticket_state#', T_($staterow['name']), $mail_template);
	$mail_template=str_replace('#ticket_priority#', $priorityrow['name'], $mail_template);
	$mail_template=str_replace('#ticket_criticality#', $criticalityrow['name'], $mail_template);
	$mail_template=str_replace('#ticket_place#', $placerow['name'], $mail_template);
	$mail_template=str_replace('#ticket_date_create#', $date_create, $mail_template);
	$mail_template=str_replace('#ticket_description#', $description, $mail_template);
    $mail_template=str_replace('#ticket_resolution#', $resolution, $mail_template);
    $mail_template=str_replace('#ticket_last_comment#', $lastComment, $mail_template);
	$mail_template=str_replace('#ticket_date_hope#', $date_hope, $mail_template);
	$mail_template=str_replace('#ticket_date_res#', $date_res, $mail_template);
	$mail_template=str_replace('#ticket_company#', $companyrow['name'], $mail_template);
	$mail_template=str_replace('#company_logo#', "$rparameters[server_url]/upload/logo/$rparameters[logo]", $mail_template);
	$mail_template=str_replace('#ticket_technician_phone#', $techrow['phone'], $mail_template);
	$mail_template=str_replace('#ticket_technician_mobile#', $techrow['mobile'], $mail_template);
	$mail_template=str_replace('#ticket_technician_custom1#', $techrow['custom1'], $mail_template);
	$mail_template=str_replace('#ticket_technician_custom2#', $techrow['custom2'], $mail_template);
	$mail_template=str_replace('#ticket_technician_mail#', $techrow['mail'], $mail_template);
	$mail_template=str_replace('#ticket_technician_function#', $techrow['function'], $mail_template);
	$mail_template=str_replace('#ticket_technician_services#', $technician_services, $mail_template);
	$mail_template=str_replace('#ticket_technician_service#', $technician_service, $mail_template);

	$msg.=$mail_template;
} else {
	echo 'ERROR : unable to find mail template, check your /template/mail directory';
}

//add tag in mail to split fonction of imap connector
if($rparameters['imap']==1 && $rparameters['imap_reply']==1) {$msg.='---- '.T_('Repondre au dessus du ticket').' ----';} else {$msg.='';}

if($send==1)
{
	if($rparameters['debug']) {echo '<b>SMTP SERVER :</b><br />';}

	require_once(__DIR__.'/../components/PHPMailer/src/PHPMailer.php');
	require_once(__DIR__.'/../components/PHPMailer/src/SMTP.php');
	require_once(__DIR__.'/../components/PHPMailer/src/Exception.php');
	$mail = new PHPMailer\PHPMailer\PHPMailer(true);
	try {
		//detect and convert image in mail
		if(preg_match_all('/<img.*?>/', $msg, $matches))
		{
			//for each images detected
			$i = 1;
			foreach ($matches[0] as $img)
			{
				//generate cid
				$cid = 'img'.($i++);
				if(strpos($img, 'base64') !== false)
				{
					if($rparameters['debug']) {echo 'DEBUG : Images base64 detected conversion ('.$img.')<br />';}
					//keep data of current image
					preg_match('/src="(.*?)"/', $img, $m);
					//extract image parameters
					$image_data=explode(',',$m[1]);
					$image_encoding=explode(';',$image_data[0]);
					$image_type=explode(':',$image_encoding[0]);
					$msg = str_replace($img, '<img alt="" src="cid:'.$cid.'" style="border: none;" />', $msg);
					//add to mail
					$mail->AddStringEmbeddedImage(base64_decode($image_data[1]), $cid, $cid, $image_encoding[1], $image_type[1]);
				} else {
					if($rparameters['debug']) {echo 'DEBUG : Images no base64 detected add EmbeddedImage('.$img.')<br />';}
					if(!preg_match('#/logo/#',$img))
					{
						$orig_filename=explode('src="',$img);
						if(!empty($orig_filename[1]))
						{
							$orig_filename=explode('"',$orig_filename[1]);
							$orig_filename=$orig_filename[0];
							if(file_exists($orig_filename))
							{
								$msg=str_replace($img, '<img src="cid:'.$cid.'" />',$msg);
								$mail->AddEmbeddedImage($orig_filename,$cid,$orig_filename,'base64', 'image/png');
							}
						}
					}
				}
			}
		}

		if($rparameters['user_agency'] && !empty($_POST['u_agency']) && $_GET['page']!='preview_mail') {
			//get agency mail
			$qry=$db->prepare("SELECT `mail` FROM `tagencies` WHERE id=:id");
			$qry->execute(array('id' => $_POST['u_agency']));
			$row=$qry->fetch();
			$qry->closeCursor();
			if($row['mail'])
			{
				if($userrow['mail']){$mail->AddCC("$row[mail]"); $dest_mail=1;} else {$mail->AddAddress("$row[mail]"); $dest_mail=1;}
			}
		}

		//add user agency mail if user have no mail and agency parameter is enable
		if($rparameters['user_agency']) {
			//send mail to agency on agency field if exist
			if(!empty($_POST['u_agency']) && $_GET['page']!='preview_mail')
			{
				//get agency mail
				$qry=$db->prepare("SELECT `mail` FROM `tagencies` WHERE id=:id");
				$qry->execute(array('id' => $_POST['u_agency']));
				$row=$qry->fetch();
				$qry->closeCursor();
				if($row['mail'])
				{
					if($userrow['mail']){$mail->AddCC("$row[mail]"); $dest_mail=1;} else {$mail->AddAddress("$row[mail]"); $dest_mail=1;}
				}
			} else {
				//get agency mail of user
				$qry=$db->prepare("SELECT `mail` FROM `tagencies` WHERE id IN (SELECT agency_id FROM tusers_agencies WHERE user_id=:user_id)");
				$qry->execute(array('user_id' => $userrow['id']));
				$row=$qry->fetch();
				$qry->closeCursor();
				if(!empty($row['mail']))
				{
					if($userrow['mail']){$mail->AddCC("$row[mail]"); $dest_mail=1;} else {$mail->AddAddress("$row[mail]"); $dest_mail=1;}
				}
			}
		}

		$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if string problems
		if($rparameters['mail_smtp_class']=='IsSendMail()') {$mail->IsSendMail();} else {$mail->IsSMTP();}
		if($rparameters['mail_secure']=='SSL')
		{$mail->Host = "ssl://$rparameters[mail_smtp]";}
		elseif($rparameters['mail_secure']=='TLS')
		{$mail->Host = "tls://$rparameters[mail_smtp]";}
		else
		{$mail->Host = "$rparameters[mail_smtp]";}
		$mail->SMTPAuth = $rparameters['mail_auth'];
		if($rparameters['debug']) $mail->SMTPDebug = 4;
		if($rparameters['mail_secure']!=0) $mail->SMTPSecure = $rparameters['mail_secure'];
		if($rparameters['mail_port']!=25) $mail->Port = $rparameters['mail_port'];
		$mail->Username = "$rparameters[mail_username]";
		if(preg_match('/gs_en/',$rparameters['mail_password'])) {$rparameters['mail_password']=gs_crypt($rparameters['mail_password'], 'd' , $rparameters['server_private_key']);}
		$mail->Password = "$rparameters[mail_password]";
		$mail->IsHTML(true);
		$mail->Timeout = 30;
		$mail->From = "$sender";
		$mail->FromName = "$rparameters[mail_from_name]";
		$mail->XMailer = ' ';

		//generate adresse list
		if($_POST['receiver']!='none') {
			if($globalrow['u_group']!=0)
			{
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $globalrow['u_group']));
				while($row=$qry2->fetch())
				{
					$mail->AddAddress("$row[0]");
					$dest_mail=1;
				}
				$qry2->closeCursor();
			} elseif($userrow['mail']) {$mail->AddAddress($userrow['mail']); $dest_mail=1;}
			//!\AJOUTER PAR NOS SOINS
			if(!empty($obsmails)){
				foreach($obsmails as $obsmail)
				{
						$mail->AddAddress($obsmail); $dest_mail=1;
				}
			}
			//!\FIN Ajout
		}
		if($rparameters['mail_from_adr']!='') {$mail->AddReplyTo($rparameters['mail_from_adr']);} elseif($techrow['mail']!='') {$mail->AddReplyTo($techrow['mail']);}
		if($rparameters['mail_cc']!='') {
			$addresses = explode(";",$rparameters['mail_cc']);
			foreach($addresses as $mailCC){
				$mail->AddCC("$mailCC");
				$dest_mail=1;
			}
		}
		if($_POST['usercopy']!='')
		{
			if(substr($_POST['usercopy'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy]"); $dest_mail=1;}
		}
		if($_POST['usercopy2']!='')
		{
			if(substr($_POST['usercopy2'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy2']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy2]");$dest_mail=1;}
		}
		if($_POST['usercopy3']!='')
		{
			if(substr($_POST['usercopy3'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy3']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy3]"); $dest_mail=1;}
		}
		if($_POST['usercopy4']!='')
		{
			if(substr($_POST['usercopy4'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy4']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy4]"); $dest_mail=1;}
		}
		if($_POST['usercopy5']!='')
		{
			if(substr($_POST['usercopy5'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy5']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy5]"); $dest_mail=1;}
		}
		if($_POST['usercopy6']!='')
		{
			if(substr($_POST['usercopy6'], 0, 1) =='G')
			{
				$groupid= explode("_", $_POST['usercopy6']);
				$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
				$qry2->execute(array('group' => $groupid[1]));
				while($row=$qry2->fetch()){$mail->AddCC("$row[0]"); $dest_mail=1;}
				$qry2->closeCursor();
			} else {$mail->AddCC("$_POST[usercopy6]"); $dest_mail=1;}
		}
		if($_POST['manual_address'])
		{
			$dest_mail=1;
			if(strpos($_POST['manual_address'],';'))
			{
				$_POST['manual_address']=explode(';',$_POST['manual_address']);
				foreach ($_POST['manual_address'] as &$email) {$mail->AddCC($email);}
			} else {$mail->AddCC($_POST['manual_address']);}
		}

		//add cci adresses
		if($rparameters['mail_cci'])
		{
			if($_POST['usercopy_cci'])
			{
				if(substr($_POST['usercopy_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy_cci']); $dest_mail=1;}
			}
			if($_POST['usercopy2_cci'])
			{
				if(substr($_POST['usercopy2_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy2_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy2_cci']); $dest_mail=1;}
			}
			if($_POST['usercopy3_cci'])
			{
				if(substr($_POST['usercopy3_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy3_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy3_cci']); $dest_mail=1;}
			}
			if($_POST['usercopy4_cci'])
			{
				if(substr($_POST['usercopy4_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy4_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy4_cci']); $dest_mail=1;}
			}
			if($_POST['usercopy5_cci'])
			{
				if(substr($_POST['usercopy5_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy5_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy5_cci']); $dest_mail=1;}
			}
			if($_POST['usercopy6_cci'])
			{
				if(substr($_POST['usercopy6_cci'], 0, 1) =='G')
				{
					$groupid= explode("_", $_POST['usercopy6_cci']);
					$qry2=$db->prepare("SELECT `mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
					$qry2->execute(array('group' => $groupid[1]));
					while($row=$qry2->fetch()){$mail->AddBCC($row['mail']); $dest_mail=1;}
					$qry2->closeCursor();
				} else {$mail->AddBCC($_POST['usercopy6_cci']); $dest_mail=1;}
			}
			if($_POST['manual_address_cci'])
			{
				$dest_mail=1;
				if(strpos($_POST['manual_address_cci'],';'))
				{
					$_POST['manual_address_cci']=explode(';',$_POST['manual_address_cci']);
					foreach ($_POST['manual_address_cci'] as &$email) {$mail->AddBCC($email);}
				} else {$mail->AddBCC($_POST['manual_address_cci']);}
			}
		}

		if($_POST['withattachment'])
		{
			//display all attachments of this ticket
			$qry=$db->prepare("SELECT `uid`,`storage_filename`,`real_filename` FROM `tattachments` WHERE `ticket_id`=:ticket_id");
			$qry->execute(array('ticket_id' => $_GET['id']));
			while($attachment=$qry->fetch())
			{
				if(!$attachment['uid']) //old upload file case
				{
					$mail->AddAttachment('./upload/'.$_GET['id'].'/'.$attachment['real_filename']);
				} else {
					$mail->AddAttachment('./upload/ticket/'.$attachment['storage_filename'], $attachment['real_filename']);
				}
			}
			$qry->closeCursor();
		}
		$mail->Subject = "$object";

		if($rparameters['mail_ssl_check']==0)
		{
			//bug fix 3292 & 3427
			$mail->smtpConnect([
			'ssl' => [
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				]
			]);
		}
		$mail->Body = "$msg";

		if($dest_mail)
		{
			$mail->send();
			//generate recipient list to trace
			$recipient_mail='';
			if($mail_auto==true) {
				$author=0;
				if(!empty($usermail['mail'])) {$recipient_mail=$usermail['mail'];} else {$recipient_mail='';}
			} else {
				$author=$_SESSION['user_id'];
				//get dest mail to trace in thread from manual send
				if($_POST['receiver']!='none') {$recipient_mail.=$_POST['receiver'].', ';}
				if($_POST['usercopy']) {$recipient_mail.=$_POST['usercopy'].', ';}
				if($_POST['usercopy2']) {$recipient_mail.=$_POST['usercopy2'].', ';}
				if($_POST['usercopy3']) {$recipient_mail.=$_POST['usercopy3'].', ';}
				if($_POST['usercopy4']) {$recipient_mail.=$_POST['usercopy4'].', ';}
				if($_POST['usercopy5']) {$recipient_mail.=$_POST['usercopy5'].', ';}
				if($_POST['usercopy6']) {$recipient_mail.=$_POST['usercopy6'].', ';}
				if($_POST['manual_address']) {$recipient_mail.=$_POST['manual_address'];}
			}

			//trace mail in thread
			$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`,`dest_mail`) VALUES (:ticket,:date,:author,'','3',:dest_mail)");
			$qry->execute(array('ticket' => $_GET['id'],'date' => $datetime,'author' => $author,'dest_mail' => $recipient_mail));

			if(isset($_SESSION['user_id'])) {
				echo DisplayMessage('success',T_('Message envoyé'));

				//redirect
				echo "
				<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&&state=$_GET[state]&userid=$_GET[userid]&view=$_GET[view]&date_start=$_GET[date_start]&date_end=$_GET[date_end]'
				}
				setTimeout('redirect()',$rparameters[time_display_msg]);
				-->
				</SCRIPT>
				";
			}
		} else {
			echo DisplayMessage('error',T_("Aucune adresse mail en destinataire renseignée"));
		}
		$mail->SmtpClose();

	} catch (Exception $e) {
		echo DisplayMessage('error',T_('Message non envoyé, vérifier les paramètres de votre connecteur SMTP').' ('.$e->getMessage().')');
		//log
		if($rparameters['log']) {logit('error', $e->getMessage(),$_SESSION['user_id']);}
	}
}
?>
