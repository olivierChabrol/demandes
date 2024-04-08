<?php
################################################################################
# @Name : preview_mail.php
# @Description : page to preview mail
# @Call: ticket.php
# @Parameters: mail.php
# @Author : Flox
# @Create : 01/10/2014
# @Update : 11/06/2020
# @Version : 3.2.2
################################################################################

require_once('models/request/ticket/ticket.php');

use Models\Request\Ticket\Ticket;

//initialize variables
if(!isset($send)) $send = '';
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';

// load Observers
$ticket = new Ticket();
$ticket
    ->setId((int) $_GET['id'])
    ->loadObservers();
$observers = $ticket->getObservers();

//send message and trace in thread
if($_POST['mail'])
{
	if($rparameters['mail'] && $rparameters['mail_smtp'])
	{
		//send
		$send=1;
		$mail_auto=false;
		require('./core/mail.php');
	} else {
		echo '<div class="alert alert-block alert-danger"><i class="fa fa-exclamation-triangle text-danger"></i> <b>'.T_('Erreur').' : </b> '.T_("Le connecteur SMTP n'est pas configuré.").'</div>';
	}
}
//return to previous page
elseif ($_POST['return'])
{
	$send=0;
	echo "
	<SCRIPT LANGUAGE='JavaScript'>
	<!--
	function redirect()
	{
	window.location='./index.php?page=ticket&id=$_GET[id]&state=$_GET[state]&userid=$_GET[userid]'
	}
	setTimeout('redirect()',0);
	-->
	</SCRIPT>
	";
}
//display preview mail and parameters
else
{
	$send=0;
	include('./core/mail.php');
	echo '
	<div class="card bcard shadow" id="card-1" draggable="false">
		<form name="mail" method="post" action="">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fa fa-envelope"></i>
					'.T_('Paramètres du mail').'
				</h5>
				<span class="card-toolbar">
					<button class="btn btn-sm btn-success" title="'.T_('Envoyer le mail').'" name="mail" value="Enregistrer" type="submit" id="mail"><i class="fa fa-envelope text-130"></i></button>
				</span>
			</div>
			<div class="card-body p-0">
				<div class="p-3">
					<table class="table table  table-bordered">
						<tbody>
							<tr>
								<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-sign-out-alt text-blue-m3 pr-1"></i>'.T_('Émetteur').'</td>
								<td class="text-95 text-default-d3">'.$sender.'</td>
							</tr>
							<tr>
								<td class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-sign-in-alt text-blue-m3 pr-1"></i>'.T_('Destinataire').'</td>
								<td class="text-95 text-default-d3">
								';
								if($globalrow['u_group']!=0)
								{
									echo '	
									<select class="form-control" id="receiver" name="receiver" >';
										$qry=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id  AND disable='0'");
										$qry->execute(array('id' => $globalrow['u_group']));
										$rgroup=$qry->fetch();
										$qry->closeCursor();
										echo '<option selected value="group" > Groupe '.$rgroup['name'].'</option>
										<option value="none">'.T_('Aucun').'</option>
									</select>
									';
									$qry=$db->prepare("SELECT `tusers`.`mail` FROM `tusers`, `tgroups_assoc` WHERE tgroups_assoc.user=tusers.id AND tgroups_assoc.group=:group AND tusers.disable='0'");
									$qry->execute(array('group' => $globalrow['u_group']));
									while($row=$qry->fetch()) {echo $row['mail'].' ';}
									$qry->closeCursor();
								} else {
									echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="receiver" name="receiver" >
											<option selected value="'.$userrow['mail'].'">'.$userrow['lastname'].' '.$userrow['firstname'].' ('.$userrow['mail'].')</option>
											<option value="none">'.T_('Aucun').'</option>
										</select>
									';
									if($userrow['mail']=='') {echo '&nbsp;<i title="'.T_("Le destinataire ne possède pas d'adresse mail").'." class="fa fa-exclamation-triangle text-danger"></i>';}
								}
								echo '
								</td>
							</tr>
							<tr>
								<td class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-copy text-blue-m3 pr-1"></i>'.T_('Copie').' </td>
								<td class="text-95 text-default-d3">
									';
									if($rparameters['mail_cc']) {echo $rparameters['mail_cc'].',&nbsp;'; if($mobile) {echo '<br />';}}
											echo'
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy" name="usercopy">
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[0]) ? $observers[0] : null;
												while($row=$qry->fetch()) {
												    $selected = '';
												    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch())
												{
													//auto select technician group if ticket is assigned to technician group
													if($globalrow['t_group']==$row['id'])
													{echo '<option selected value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
                                                    else
                                                    {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												}
												$qry->closeCursor();

												//auto select tech if it's not the current tech
												$different_tech=0;
												if (($_SESSION['user_id']!=$techrow['id']) && ($globalrow['t_group']==0)) {
                                                    $selected = (!$observer) ? 'selected' : '';
												    echo '<option '. $selected .' value="'.$techrow['mail'].'">'.$techrow['lastname'].' '.$techrow['firstname'].'</option>'; $different_tech=1;
												}

												//auto select mail agency if parameters is enable and if agency have mail and user have no mail
												if ($rparameters['user_agency']==1 && $different_tech==0) {
												    $selected = (!$observer) ? 'selected' : '';
													//get agency mail
													$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();
													if($row['mail']) {echo '<option '. $selected .' value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}
												}
												echo '
											</select>
											,&nbsp; 
											'; if($mobile) {echo '<br />';} echo '
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy2" name="usercopy2" >
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[1]) ? $observers[1] : null;
												while($row=$qry->fetch()) {
                                                    $selected = '';
                                                    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();

												//auto select mail agency if parameters is enable and if agency have mail and user have no mail
												if ($rparameters['user_agency']==1 && $different_tech==1) {
                                                    $selected = (!$observer) ? 'selected' : '';
													//get agency mail
													$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();
													if($row['mail']) {echo '<option '. $selected .' value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}

												} else {
                                                    $selected = (!$observer) ? 'selected' : '';
													echo '<option '. $selected .' value=""></option>';
												}
												echo '
												
											</select>
											,&nbsp; 
											'; if($mobile) {echo '<br />';} echo '
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy3" name="usercopy3" >
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[2]) ? $observers[2] : null;
												while($row=$qry->fetch()) {
                                                    $selected = '';
                                                    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
                                                $selected = (!$observer) ? 'selected' : '';
												echo '
												<option '. $selected .' value=""></option>
											</select>
											,&nbsp; 
											'; if($mobile) {echo '<br />';} echo '
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy4" name="usercopy4" >
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[3]) ? $observers[3] : null;
												while($row=$qry->fetch()) {
                                                    $selected = '';
                                                    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
                                                $selected = (!$observer) ? 'selected' : '';
												echo '
												<option '. $selected .' value=""></option>
											</select>
											,&nbsp; 
											'; if($mobile) {echo '<br />';} echo '
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy5" name="usercopy5" >
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[4]) ? $observers[4] : null;
												while($row=$qry->fetch()) {
                                                    $selected = '';
                                                    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
                                                $selected = (!$observer) ? 'selected' : '';
												echo '
												<option '. $selected .' value=""></option>
											</select>
											,&nbsp; 
											'; if($mobile) {echo '<br />';} echo '
											<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy6" name="usercopy6" >
												';
												//display users
												$qry=$db->prepare("SELECT `tusers`.`id`, `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
												$qry->execute();
                                                $observer = isset($observers[5]) ? $observers[5] : null;
												while($row=$qry->fetch()) {
                                                    $selected = '';
                                                    if ($observer && $observer->getId() == $row['id']) {
                                                        $selected = 'selected';
                                                    }
													if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
													if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
													if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
													echo '<option value="'.$row['mail'].'" '. $selected .'>'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
												}
												$qry->closeCursor();
												//display groups
												$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												$qry->closeCursor();
                                                $selected = (!$observer) ? 'selected' : '';
												echo '
												<option '. $selected .' value=""></option>
											</select>
											<div class="p-1"></div>
											<input '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" placeholder="'.T_('Autre adresse mail').'" size="40" type="text" name="manual_address" />
										
								</td>
							</tr>
							';
							if($rparameters['mail_cci'])
							{
								echo '
									<tr>
										<td class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-copy text-blue-m3 pr-1"></i>'.T_('Copie cachée').'</td>
										<td class="text-95 text-default-d3">
									
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy_cci" name="usercopy_cci">
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch())
											{
												//auto select technician group if ticket is assigned to technician group
												if($globalrow['t_group']==$row['id'])
												{echo '<option selected value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
												else
												{echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											}
											$qry->closeCursor();

											//auto select tech if it's not the current tech
											$different_tech=0;

											//auto select mail agency if parameters is enable and if agency have mail and user have no mail
											if ($rparameters['user_agency']==1 && $different_tech==0) {
												//get agency mail
												$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();
												if($row['mail']) {echo '<option selected value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}
											}
											echo '
										</select>
										,&nbsp; 
										'; if($mobile) {echo '<br />';} echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy2_cci" name="usercopy2_cci" >
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											$qry->closeCursor();

											//auto select mail agency if parameters is enable and if agency have mail and user have no mail
											if ($rparameters['user_agency']==1 && $different_tech==1) {
												//get agency mail
												$qry=$db->prepare("SELECT `mail`,`name` FROM `tagencies` WHERE id IN (SELECT `u_agency` FROM `tincidents` WHERE id=:id)");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();
												if($row['mail']) {echo '<option selected value="'.$row['mail'].'">'.T_('Agence').' '.$row['name'].' ('.$row['mail'].')</option>';}

											} else {
												echo '<option selected value=""></option>';
											}
											echo '
											
										</select>
										,&nbsp; 
										'; if($mobile) {echo '<br />';} echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy3_cci" name="usercopy3_cci" >
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											$qry->closeCursor();
											echo '
											<option selected value=""></option>
										</select>
										,&nbsp; 
										'; if($mobile) {echo '<br />';} echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy4_cci" name="usercopy4_cci" >
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											$qry->closeCursor();
											echo '
											<option selected value=""></option>
										</select>
										,&nbsp; 
										'; if($mobile) {echo '<br />';} echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy5_cci" name="usercopy5_cci" >
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											$qry->closeCursor();
											echo '
											<option selected value=""></option>
										</select>
										,&nbsp; 
										'; if($mobile) {echo '<br />';} echo '
										<select '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" id="usercopy6_cci" name="usercopy6_cci" >
											';
											//display users
											$qry=$db->prepare("SELECT `tcompany`.`name` AS company,`tusers`.`mail`,`tusers`.`firstname`,`tusers`.`lastname` FROM `tusers`,`tcompany` WHERE `tusers`.`company`=`tcompany`.`id` AND (`tusers`.`mail`!='' AND `tusers`.`disable`='0') OR `tusers`.`id`='0' GROUP BY `tusers`.`id` ORDER BY `tusers`.`id`!='0', `tusers`.`lastname` ASC, `tusers`.`firstname` ASC");
											$qry->execute();
											while($row=$qry->fetch()) {
												if($row['company']!='Aucune') {$user_company=$row['company'].', ';} else {$user_company='';}
												if($row['mail']) {$user_mail=$row['mail'];} else {$user_mail='';}
												if($row['lastname']=='Aucun') {$row['lastname']=T_($row['lastname']);}
												echo '<option value="'.$row['mail'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$user_company.$user_mail.')</option>';
											}
											$qry->closeCursor();
											//display groups
											$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE disable='0'");
											$qry->execute();
											while($row=$qry->fetch()) {echo '<option value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
											$qry->closeCursor();
											echo '
											<option selected value=""></option>
										</select>
										<div class="p-1"></div>
										<input '; if($mobile) {echo 'style="max-width:190px"';} echo ' class="form-control form-control-sm col-md-2 d-inline-block" placeholder="'.T_('Autre adresse mail').'" size="40" type="text" name="manual_address_cci" />
									</td>
								</tr>
								';
							}
							echo '
							<tr>
								<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-flag text-blue-m3 pr-1"></i>'.T_('Objet').'</td>
								<td class="text-95 text-default-d3">'.$object.'</td>
							</tr>
							<tr>
								<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-file-alt text-blue-m3 pr-1"></i>'.T_('Corps').'</td>
								<td class="text-95 text-default-d3">'.$msg.'</td>
							</tr>
							';
								//check if attachment exist for this tickets
								$qry=$db->prepare("SELECT `id`FROM `tattachments` WHERE `ticket_id`=:ticket_id");
								$qry->execute(array('ticket_id' => $_GET['id']));
								$attachment=$qry->fetch();
								$qry->closeCursor();
								if(!empty($attachment))
								{
									echo '
									<tr>
										<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-file-alt text-blue-m3 pr-1"></i>'.T_('Pièces jointes').'</td>
										<td class="text-95 text-default-d3">
											<select class="form-control form-control-sm col-md-2 d-inline-block" id="withattachment" name="withattachment" >
												<option selected value="1">'.T_('Avec pièces jointes').'</option>
												<option value="0">'.T_('Sans pièces jointes').'</option>
											</select>
											<div class="p-1"></div>
											';
											//display all attachments of this ticket
											$qry=$db->prepare("SELECT `uid`,`storage_filename`,`real_filename` FROM `tattachments` WHERE `ticket_id`=:ticket_id");
											$qry->execute(array('ticket_id' => $_GET['id']));
											while($attachment=$qry->fetch())
											{
												if(!$attachment['uid']) //old upload file case
												{
													echo '<a target="_blank" title="'.T_('Télécharger le fichier').'" href="./upload/'.$_GET['id'].'/'.$attachment['storage_filename'].'"><i style="vertical-align: middle;" class="fa fa-file text-info"></i>&nbsp;'.$attachment['storage_filename'].'</a>&nbsp;&nbsp;';
												} else {
													echo '<a target="_blank" title="'.T_('Télécharger le fichier').'" href="index.php?page=ticket&download='.$attachment['uid'].'"><i style="vertical-align: middle;" class="fa fa-file text-info"></i>&nbsp;'.$attachment['real_filename'].'</a>&nbsp;&nbsp;';
												}
											}
											$qry->closeCursor();
											echo '
										</td>
									</tr>
									';
								}
							echo '
						</tbody>
					</table>
				</div> 
			</div>
			<div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
				<button name="mail" id="mail" value="mail" type="submit" class="btn btn-success">
					<i class="fa fa-envelope"></i> 
					&nbsp;'.T_('Envoyer le mail').'
				</button>
				&nbsp;
				<button name="return" id="return" value="return" type="submit" class="btn btn-danger">
					<i class="fa fa-reply"></i> 
					&nbsp;'.T_('Retour').'
				</button>
			</div>
		</form>
	</div>
	';
}
?>