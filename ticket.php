<?php
################################################################################
# @Name : ticket.php
# @Description : page to display create and edit ticket
# @call : dashboard
# @parameters :
# @Author : Flox
# @Create : 07/01/2007
# @Update : 20/08/2019
# @Update : 27/08/2020
# @Version : 3.2.4 p3
################################################################################

// require needed by request
require_once('models/user/user_request.php');
require_once('models/user/user.php');
require_once('models/request/ticket/ticket.php');

use Models\User\User;
use Models\User\UserRequest;
use Models\Request\Ticket\Ticket;

// load User Request and Observers
$owner = $_SESSION['user_id'];
$users = User::getCollection();
$ticket = new Ticket();
$ticket
    ->setId((int) $_GET['id'])
    ->loadObservers();

//initialize variables
if(!isset($userreg)) $userreg = '';
if(!isset($category)) $category = '';
if(!isset($subcat)) $subcat = '';
if(!isset($title)) $title = '';
if(!isset($date_hope)) $date_hope = '';
if(!isset($date_create)) $date_create = '';
if(!isset($state)) $state = '';
if(!isset($description)) $description = '';
if(!isset($resolution)) $resolution = '';
if(!isset($priority)) $priority = '';
if(!isset($percentage)) $percentage = '';
if(!isset($id)) $id = '';
if(!isset($id_in)) $id_in = '';
if(!isset($save)) $save = '';
if(!isset($techread)) $techread = '';
if(!isset($techread_date)) $techread_date = '';
if(!isset($userread)) $userread = '';
if(!isset($next)) $next = '';
if(!isset($previous)) $previous = '';
if(!isset($user)) $user = '';
if(!isset($down)) $down = '';
if(!isset($u_group)) $u_group = '';
if(!isset($t_group)) $t_group = '';
if(!isset($userid)) $userid = '';
if(!isset($u_service)) $u_service = '';
if(!isset($date_hope_error)) $date_hope_error = '';
if(!isset($selected_time)) $selected_time = '';

if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['upload'])) $_POST['upload'] = '';
if(!isset($_POST['title'])) $_POST['title'] = '';
if(!isset($_POST['description'])) $_POST['description'] = '';
if(!isset($_POST['resolution'])) $_POST['resolution'] = '';
if(!isset($_POST['Submit'])) $_POST['Submit'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
/*/!\ AJOUT PAR NOS SOINS*/if(!isset($_POST['observer'])) $_POST['observer'] = '';
if(!isset($_POST['user'])) $_POST['user'] = '';
if(!isset($_POST['type'])) $_POST['type'] = '';
if(!isset($_POST['type_answer'])) $_POST['type_answer'] = '';
if(!isset($_POST['modify'])) $_POST['modify'] = '';
if(!isset($_POST['quit'])) $_POST['quit'] = '';
if(!isset($_POST['date_create'])) $_POST['date_create'] = '';
if(!isset($_POST['date_hope'])) $_POST['date_hope'] = '';
if(!isset($_POST['date_res'])) $_POST['date_res'] = '';
if(!isset($_POST['priority'])) $_POST['priority'] = '';
if(!isset($_POST['criticality'])) $_POST['criticality'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';
if(!isset($_POST['time'])) $_POST['time'] = '';
if(!isset($_POST['time_hope'])) $_POST['time_hope'] = '';
if(!isset($_POST['state'])) $_POST['state'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['technician'])) $_POST['technician'] = '';
if(!isset($_POST['ticket_places'])) $_POST['ticket_places'] = '';
if(!isset($_POST['text2'])) $_POST['text2'] = '';
if(!isset($_POST['start_availability_d'])) $_POST['start_availability_d'] = '';
if(!isset($_POST['end_availability_d'])) $_POST['end_availability_d'] = '';
if(!isset($_POST['private'])) $_POST['private'] = '';
if(!isset($_POST['u_service'])) $_POST['u_service'] = '';
if(!isset($_POST['asset_id'])) $_POST['asset_id'] = '';
if(!isset($_POST['asset'])) $_POST['asset'] = '';
if(!isset($_POST['u_agency]'])) $_POST['u_agency]'] = '';
if(!isset($_POST['sender_service'])) $_POST['sender_service'] = '';
if(!isset($_POST['addcalendar'])) $_POST['addcalendar'] = '';
if(!isset($_POST['addevent'])) $_POST['addevent'] = '';

$db_id=strip_tags($db->quote($_GET['id']));
$db_lock_thread=strip_tags($db->quote($_GET['lock_thread']));
$db_unlock_thread=strip_tags($db->quote($_GET['unlock_thread']));
$db_threadedit=strip_tags($db->quote($_GET['threadedit']));

$hide_button=0;

if(!isset($globalrow['technician'])) $globalrow['technician'] = '';
if(!isset($globalrow['time'])) $globalrow['time'] = '';

//core ticket actions
include('./core/ticket.php');

//defaults values for new tickets
if(!isset($globalrow['creator'])) $globalrow['creator'] = '0';
if(!isset($globalrow['t_group'])) $globalrow['t_group'] = '';
if(!isset($globalrow['u_group'])) $globalrow['u_group'] = '';
if(!isset($globalrow['category'])) $globalrow['category'] = '';
if(!isset($globalrow['subcat'])) $globalrow['subcat'] = '';
if(!isset($globalrow['title'])) $globalrow['title'] = '';
if(!isset($globalrow['description'])) $globalrow['description'] = '';
if(!isset($globalrow['date_create'])) $globalrow['date_create'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['date_hope'])) $globalrow['date_hope'] = '';
if(!isset($globalrow['date_res'])) $globalrow['date_res'] = '';
if(!isset($globalrow['time_hope'])) $globalrow['time_hope'] = '5';
if(!isset($globalrow['time'])) $globalrow['time'] = '';
if(!isset($globalrow['priority'])) $globalrow['priority'] = '';
if(!isset($globalrow['state'])) $globalrow['state'] = '1';
if(!isset($globalrow['type'])) $globalrow['type'] = '0';
if(!isset($globalrow['type_answer'])) $globalrow['type_answer'] = '0';
if(!isset($globalrow['start_availability'])) $globalrow['start_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['end_availability'])) $globalrow['end_availability'] = date("Y-m-d").' '.date("H:i:s");
if(!isset($globalrow['availability_planned'])) $globalrow['availability_planned'] = 0;
if(!isset($globalrow['place'])) $globalrow['place'] = '0';
if(!isset($globalrow['criticality'])) $globalrow['criticality'] = '0';
if(!isset($globalrow['u_service'])) $globalrow['u_service'] = '0';
if(!isset($globalrow['asset_id'])) $globalrow['asset_id'] = '';
if(!isset($globalrow['u_agency'])) $globalrow['u_agency'] = '0';
if(!isset($globalrow['sender_service'])) $globalrow['sender_service'] = '0';

//default values for tech and admin and super
if($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0 || $_SESSION['profile_id']==3)
{
	if($globalrow['technician']==0 && $_GET['action']=='new') {$globalrow['technician']=$_SESSION['user_id'];} //auto select current technician on new tickets
	if(!isset($globalrow['user'])) $globalrow['user']=0;
} else {
	if(!isset($globalrow['technician'])) {$globalrow['technician']='';}
	if($globalrow['user']==0) {$globalrow['user']=$_SESSION['user_id'];}
}

?>
<div class="card bcard shadow mt-2 ticket-group" id="card-1" draggable="false">
	<form class="form-horizontal" name="myform" id="myform" enctype="multipart/form-data" method="post" action="" onsubmit="loadVal();" >
		<div class="card-header">
			<h5 class="card-title">
				<i class="fa fa-ticket-alt"></i>
				<?php
					//display widget title
					if($_GET['action']=='new') {
						if($mobile){echo 'n°'.$_GET['id'].' ';}
						else {echo T_('Ouverture du ticket').' n° '.$_GET['id'];}
					} else {
						if($mobile){echo 'n°'.$_GET['id'].' ';}
						else {echo T_('Édition du ticket').' n° '.$_GET['id'].' '.$percentage.': '.$title;}
					}
					//display clock if alarm
					$qry=$db->prepare("SELECT `date_start` FROM `tevents` WHERE incident=:incident AND disable='0' AND type='1'");
					$qry->execute(array('incident' => $_GET['id']));
					$alarm=$qry->fetch();
					$qry->closeCursor();
					if($alarm) {echo ' <i class="fa fa-bell text-warning" title="'.T_('Alarme activée le').' '.$alarm['date_start'].'" /></i>';}
					//display calendar if planned
					$qry=$db->prepare("SELECT `date_start` FROM `tevents` WHERE incident=:incident AND disable='0' AND type='2'");
					$qry->execute(array('incident' => $_GET['id']));
					$plan=$qry->fetch();
					$qry->closeCursor();
					if($plan && !$mobile) echo '&nbsp;<a target="_blank" href="./index.php?page=calendar"><i class="fa fa-calendar text-info" title="'.T_('Ticket planifié dans le calendrier le').' '.$plan['date_start'].'" /></i></a>';
					//display member of project
					if($rparameters['project']==1 && $rright['project'] && !$mobile)
					{
						//check if current ticket is a task of project
						$qry=$db->prepare("SELECT `tprojects`.`name`,`tprojects_task`.`project_id` FROM `tprojects_task`,`tprojects` WHERE `tprojects_task`.project_id=`tprojects`.id AND `tprojects_task`.ticket_id=:ticket_id");
						$qry->execute(array('ticket_id' => $_GET['id']));
						$row=$qry->fetch();
						$qry->closeCursor();
						if($row) echo '&nbsp;<a target="_blank" href="./index.php?page=project"><i class="fa fa-tasks text-purple" title="'.T_('Le ticket est une tâche du projet').' '.$row['name'].'" /></i></a>';
					}
				?>
			</h5>
			<span class="card-toolbar">
				<?php
					if($rparameters['asset']==1 && $rparameters['asset_vnc_link']==1 && $_POST['user'] ){
						//check if user have asset with IP
						$qry=$db->prepare("SELECT `tassets_iface`.`ip` FROM `tassets_iface`,`tassets` WHERE tassets_iface.asset_id=tassets.id AND user=:user");
						$qry->execute(array('user' => $_POST['user']));
						$row=$qry->fetch();
						$qry->closeCursor();
						if($row) {echo '<a target="_blank" href="http://'.$row['ip'].':5800"><img title="'.T_('Ouvre un nouvel onglet sur le prise de contrôle distant web VNC').'" src="./images/remote.png" /></a>&nbsp;&nbsp;';}
					}
					if($rright['ticket_next']!=0 && !$mobile && $_GET['action']!='new')
					{
						if($previous[0]) echo'<a style="vertical-align:middle;" href="./index.php?page=ticket&amp;id='.$previous[0].'&amp;state='.$globalrow['state'].'&amp;userid='.$_GET['userid'].'"><i title="'.T_('Ticket précédent de cet état').'" class="fa fa-arrow-circle-left text-130 text-primary-m2 mr-1"></i></a>';
						if($next[0]) echo'<a style="vertical-align:middle;" href="./index.php?page=ticket&amp;id='.$next[0].'&amp;state='.$globalrow['state'].'&amp;userid='.$_GET['userid'].' "><i title="'.T_('Ticket suivant de cet état').'" class="fa fa-arrow-circle-right text-130 text-primary-m2 mr-1"></i></a>';
					}
					if($rright['ticket_print']!=0 && $_GET['action']!='new')
					{
						echo '
						<a style="width:31px; height:27px; padding-left:6px;" class="btn btn-xs btn-default" target="_blank" onClick="parentNode.submit();" href="ticket_print.php?id='.$_GET['id'].'&user_id='.$_SESSION['user_id'].'&token='.$_COOKIE['token'].'">
							<i title="'.T_('Imprimer ce ticket').'" class="fa fa-print text-130"></i>
						</a>&nbsp;';
					}
					if($rright['ticket_template']!=0 && $_GET['action']=='new')
					{
						//check if template exist
						$qry=$db->prepare("SELECT `id` FROM `ttemplates`");
						$qry->execute();
						$template=$qry->fetch();
						$qry->closeCursor();
						if(!empty($template['id']))
						{
							echo '<button type="button" style="width:31px; height:27px; padding:6px;" class="btn btn-xs btn-pink" title="'.T_('Modèle de tickets').'" data-toggle="modal" data-target="#template" ><i class="fa fa-tags text-110"></i></button>&nbsp;';
						}
					}
					if($rright['planning'] && $rparameters['planning'] && $rright['ticket_event'] && $_GET['action']!='new')
					{
						echo '<button type="button" style="width:31px; height:27px; padding-top:5px;" class="btn btn-xs btn-warning" title="'.T_('Créer un rappel pour ce ticket').'" data-toggle="modal" data-target="#add_event" ><span style="color:#FFF;"><i class="fa fa-bell text-120"></i></span></button>&nbsp;';
					}
					if(($rright['planning']) && $rparameters['planning'] && $rright['ticket_calendar'] && $_GET['action']!='new')
					{
						echo '<button type="button" style="width:31px; height:27px; padding-left:6px;  padding-top:5px;" class="btn btn-xs btn-info" title="'.T_('Planifier une intervention dans le calendrier').'" data-toggle="modal" data-target="#add_planification" ><i class="fa fa-calendar text-120"></i></button>&nbsp;';
					}
					if($rright['ticket_delete'] && $_GET['action']!='new')
					{
						echo '<a style="width:31px; height:27px; padding-top:5px;" class="btn btn-xs btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer ce ticket ? les données et les pièces jointes seront définitivement supprimées').'\');" href="./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&state='.$_GET['state'].'&action=delete"  title="'.T_('Supprimer ce ticket').'" ><i class="fa fa-trash text-120"></i></a>&nbsp;';
					}
					if($rright['ticket_save'] && !$hide_button)
					{
						echo '<button style="width:31px; height:27px;" class="btn btn-xs btn-success" title="'.T_('Enregistrer').'" name="modify" value="submit" type="submit" id="modify_btn"><i class="fa fa-save text-130"></i></button>';
						echo '&nbsp;';
						echo '<button style="width:31px; height:27px;" class="btn btn-xs btn-purple" title="'.T_('Enregistrer et quitter').'" name="quit" value="quit" type="submit" id="quit_btn"><i class="fa fa-save text-130"></i></button>';
					}
					?>
			</span>
		</div>
		<div class="card-body p-0">
			<div class="p-3">
				<!-- START sender part -->
				<div class="form-group row <?php if((!$rright['ticket_user_disp'] && $_GET['action']!='new') || (!$rright['ticket_new_user_disp'] && $_GET['action']=='new')) {echo 'd-none';} ?>" >
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="user">
							<?php
								if(($_POST['user']==0) && ($globalrow['user']==0) && ($u_group=='')) echo '<i id="user_warning" title="'.T_('Sélectionner un demandeur').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
								echo T_('Demandeur').' :';
							?>
						</label>
					</div>
					<div class="col-sm-9">
						<!-- START sender list part -->
						<select autofocus style="display:inline; <?php if($mobile) {echo 'max-width:240px;';} else {if($rright['ticket_user_company']) {echo 'width:auto;';} else {echo 'max-width:269px;';}}?>" class="form-control chosen-select" id="user" name="user" <?php if(($rright['ticket_user']==0 && $_GET['action']!='new') || ($rright['ticket_new_user']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
							<?php
							//define order of user list in case with company prefix
							if($rright['ticket_user_company']!=0)
							{
								$qry=$db->prepare("SELECT tusers.id,tusers.company,tusers.firstname,tusers.lastname,tusers.disable,tcompany.name AS user_company FROM `tusers`, `tcompany` WHERE tusers.company=tcompany.id AND (tusers.lastname!='' OR tusers.firstname!='') ORDER BY tcompany.name, tusers.lastname");
							} else {
								/*/!\ AJOUT (tcompany)*/$qry=$db->prepare("SELECT tusers.id,company,firstname,lastname,tusers.disable,name FROM `tusers` LEFT JOIN `tcompany` ON tusers.company=tcompany.id  WHERE (lastname!='' OR firstname!='')  ORDER BY lastname ASC, firstname ASC");
							}
							//display user list and keep selected an disable user
							$qry->execute();
							while($row=$qry->fetch())
							{
								if($rright['ticket_user_company']!=0 && $row['company']!=0){$user_company='['.$row['user_company'].'] ';} else {$user_company='';}
								if($_POST['user']==$row['id']) {$selected='selected';} elseif(($_POST['user']=='') && ($globalrow['user']==$row['id'])) {$selected='selected';} else {$selected='';}
							  /*/!\ AJOUT (tcompany)*/if($row['id']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.T_(" $row[lastname]").' '.$row['firstname'].' ('.$row['name'].')</option>';} //case no user
								/*/!\ AJOUT (tcompany)*/if($row['disable']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$user_company.$row['lastname'].' '.$row['firstname'].' ('.$row['name'].')</option>';} //all enable users and technician
								/*/!\ AJOUT (tcompany)*/if(($row['disable']==1) && ($selected=='selected') && $row['id']!=0) {echo '<option '.$selected.' value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].' ('.$row['name'].')</option>';} //case disable user always attached to this ticket
							}
							$qry->closeCursor();
							//display group list and keep selected an disable group
							$qry=$db->prepare("SELECT `id`,`name`,`disable` FROM `tgroups` WHERE `type`='0' ORDER BY `name`");
							$qry->execute();
							while($row=$qry->fetch())
							{
								if($row['id']==$u_group) {$selected='selected';} else {$selected='';}
								if($row['disable']==0) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.T_(" $row[name]").'</option>';}
								if(($row['disable']==1) && ($selected=='selected')) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
							}
							$qry->closeCursor();
							?>
						</select>

						<?php
							//send data in disabled case
							if((!$rright['ticket_user'] && $_GET['action']!='new') || (!$rright['ticket_new_user'] && $_GET['action']=='new')) {echo ' <input type="hidden" name="user" value='.$globalrow['user'].' /> ';}
						?>
						<!-- END sender list part -->
						<!-- START sender actions part -->
						<?php
						if($rright['ticket_user_actions'])
						{
							echo '<span class="d-inline-block">';
								echo'<input type="hidden" name="action" value="">';
								echo'<input type="hidden" name="edituser" value="">';
								echo '<i class="fa fa-plus-circle text-success text-130 pl-1" title="'.T_('Ajouter un utilisateur').'" data-toggle="modal" data-target="#user_add_modal"></i>';
								if($u_group)
								{
									echo '<i class="fa fa-pencil-alt text-warning text-130 pl-1" title="'.T_('Modifier le groupe').'" value="useredit" onClick="parent.location=\'./index.php?page=ticket&id='.$_GET['id'].'&userid='.$_GET['userid'].'&action=edituser&edituser='.$u_group.'\'"  /></i>&nbsp;&nbsp;';
								}
								else
								{
									if($_POST['user']) {$selecteduser=$_POST['user'];} else {$selecteduser=$globalrow['user'];}
									//hide modify link when none user selected
									if($selecteduser){echo '<i class="fa fa-pencil-alt text-warning text-130 d-inline-block pl-1" title="'.T_('Modifier un utilisateur').'" data-toggle="modal" data-target="#user_modify_modal" ></i>';}
								}
							echo '</span>';
						}
						?>
						<!-- END sender actions part -->
						<!-- START user info part -->
						<?php
							if($mobile==0)
							{
								//data get by ajax script refer /includes/
								echo '
								<span style="font-size:15px;">
									<span id="user_phone"></span>
									<span id="user_mobile"></span>
									<span id="user_mail"></span>
									<span id="user_function"></span>
									<span id="user_service"></span>
									<span id="user_agency"></span>
									';
									if(!$rright['ticket_user_company']) {echo '<span id="user_company"></span>';}
									echo '
									<span id="user_other_ticket"></span>
									<span id="user_asset"></span>
									<span id="user_ticket_remaining"></span>
									<span id="user_hour_remaining"></span>
								</span>
								';
							}
						?>
						<!-- START user info part -->
					</div>
				</div>
				<!-- END sender part -->
				<!-- START destination service part -->
				<?php
					if($rright['ticket_service_disp']!=0)
					{
						echo'
						<div class="form-group row '; if($rright['ticket_new_service_disp']==0 && $_GET['action']=='new') {echo 'd-none';} echo '" >
							<div class="col-sm-2 col-form-label text-sm-right pr-0">
								<label class="mb-0" for="u_service">'.T_('Equipe/Service').' :</label>
							</div>
							<div class="col-sm-5">
								<select class="form-control col-5" id="u_service" name="u_service" '; if(($rright['ticket_service']==0 && $_GET['action']!='new') || ($rright['ticket_new_service']==0 && $_GET['action']=='new')) {echo ' disabled="disabled" ';} if($rright['ticket_service_mandatory']!=0) {echo ' required="required" ';}  echo' onchange="loadVal(); submit();">
									';
                                    $first = true;
                                    $userRequest = new UserRequest();
                                    $userRequest
                                        ->setId($globalrow['user'])
                                        ->loadServices();
                                    $services = $userRequest->getServices();
                                    $selected = null;

                                    if (isset($services[$globalrow['u_service']])) {
                                        $selected = $services[$globalrow['u_service']];
                                    }

                                    if (count($services) > 0) {
                                        /* New system for printing user services only */
                                        if ($_POST['u_service']) {
                                            $selected = $services[$_POST['u_service']];
                                            $first = false;
                                        }

                                        foreach ($services as $service) {
                                            $isSelected = false;

                                            if ($first || ($selected && $service->getId() == $selected->getId())) {
                                                $isSelected = true;
                                            }
                                            if ($first) {
                                                $first = false;
                                            }
                                            ?>
                                            <option value="<?php echo $service->getId() ?>" <?php echo ($isSelected) ? 'selected' : '' ?> ><?php echo T_($service->getName()) ?></option>
                                            <?php
                                        }
                                    } else if($_POST['u_service']) {
                                        /* old system with all services */
                                        echo '<option value="">Aucun</option>';
                                        $qry2=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE id=:id");
                                        $qry2->execute(array('id' => $_POST['u_service']));
                                        $row2=$qry2->fetch();
                                        $qry2->closeCursor();
                                        echo '<option value="'.$_POST['u_service'].'" selected >'.T_($row2['name']).'</option>';
                                        $qry2=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE id!=:id AND disable='0' ORDER BY id!=0, name");
                                        $qry2->execute(array('id' => $_POST['u_service']));
                                        while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
                                        $qry2->closeCursor();
                                    }
                                    else
                                    {
                                        echo '<option value=""></option>';
                                        $qry2=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE id=:id AND id!='0' ORDER BY id");
                                        $qry2->execute(array('id' => $globalrow['u_service']));
                                        $row2=$qry2->fetch();
                                        $qry2->closeCursor();
                                        if(!empty($row2)) {echo '<option value="'.$globalrow['u_service'].'" selected >'.T_($row2['name']).'</option>';}
                                        $qry2=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE id!=:id AND disable='0' AND id!='0' ORDER BY name");
                                        $qry2->execute(array('id' => $globalrow['u_service']));
                                        while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
                                        $qry2->closeCursor();
                                    }
									echo'
								</select>
								';
								//send data in disabled case
								if($rright['ticket_service']==0 && $_POST['u_service']==0 && $globalrow['u_service']!=0) echo '<input type="hidden" name="u_service" value="'.$globalrow['u_service'].'" />';
								echo '
							</div>
						</div>
						';
					}
				?>
				<!-- END destination service part -->
				<!-- START type part -->
			   <?php
					if($rparameters['ticket_type']==1)
					{
						echo'
						<div class="form-group row '; if((!$rright['ticket_type_disp'] && $_GET['action']!='new') || (!$rright['ticket_new_type_disp'] && $_GET['action']=='new')) {echo 'd-none';} echo'">
							<div class="col-sm-2 col-form-label text-sm-right pr-0">
								<label class="mb-0" for="type">
									'.T_('Type').' :
								</label>
							</div>
							<div class="col-sm-5">
								<select id="type" name="type" class="form-control col-5" '; if($rright['ticket_type_mandatory']) { echo ' onchange="CheckMandatory();" ';} if(($rright['ticket_type']==0 && $_GET['action']!='new') || ($rright['ticket_new_type']==0 && $_GET['action']=='new')) {echo 'disabled="disabled"';} echo' >';
									//limit service type
									if($rparameters['user_limit_service']==1 && $rright['ticket_type_service_limit']!=0)
									{
										if($rright['ticket_service_disp'] || $rright['ticket_new_service_disp']) //case service field display
										{
											if($_POST['u_service'])
											{$where=' service=\''.$_POST['u_service'].'\' ';} else {$where=' service=\''.$globalrow['u_service'].'\' ';}
										} else { //case no service field display
											if($cnt_service==1){$where=' service='.$user_services['0'].' OR service=0 OR id=0 ';}
											elseif($cnt_service>1) //multi services case
											{
												$where='';
												foreach($user_services as $user_service) {$where.=" service='$user_service' OR ";}
												$where.='service=0';
											} else {$where='1=1';}
										}
										$old_type=1;
										$query2 = $db->query("SELECT id,name FROM `ttypes` WHERE $where OR id=0 ORDER BY id=0 DESC,name");
										while ($row2 = $query2->fetch()) {
											//select entry
											$selected='';
											if($_POST['type'] && $row2['id']==$_POST['type'])
											{$selected='selected';}
											elseif($globalrow['type'] && $row2['id']==$globalrow['type'])
											{$selected='selected'; }
											if($globalrow['type']==$row2['id']) {$old_type=0;}
											echo '<option '.$selected.' value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
										}
										$query2->closeCursor();
										//keep old data
										if($old_type==1 && $_GET['action']!='new') {
											$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes` WHERE `id`=:id");
											$qry2->execute(array('id' => $globalrow['type']));
											$row2=$qry2->fetch();
											$qry2->closeCursor();
											echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
										}
									} else {
										if($_POST['type']!='')
										{
											$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes` WHERE `id`=:id");
											$qry2->execute(array('id' => $_POST['type']));
											$row2=$qry2->fetch();
											$qry2->closeCursor();
											echo '<option value="'.$_POST['type'].'" selected >'.T_($row2['name']).'</option>';
											$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes` WHERE `id`!=:id ORDER BY `name`");
											$qry2->execute(array('id' => $_POST['type']));
											while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
											$qry2->closeCursor();
										}
										else
										{
											$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes` WHERE `id`=:id ");
											$qry2->execute(array('id' => $globalrow['type']));
											$row2=$qry2->fetch();
											$qry2->closeCursor();
											echo '<option value="'.$globalrow['type'].'" selected >'.T_($row2['name']).'</option>';
											$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes` WHERE  `id`!=:id ORDER BY `name`");
											$qry2->execute(array('id' => $globalrow['type']));
											while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
											$qry2->closeCursor();
										}
									}
									echo'
								</select>
								';
								//send data in disabled case
								if($rright['ticket_type']==0 && $_GET['action']!='new') echo '<input type="hidden" name="type" value="'.$globalrow['type'].'" />';
								echo '
							</div>
						</div>
						';
					}
				?>
				<!-- END type part -->
				<!-- START type answer part -->
				<?php
					if($rright['ticket_type_answer_disp'])
					{
						echo'
						<div class="form-group row">
							<div class="col-sm-2 col-form-label text-sm-right pr-0">
								<label class="mb-0" for="type_answer">'.T_('Type de réponse').' :</label>
							</div>
							<div class="col-sm-5">
								<select class="form-control col-5" name="type_answer">';
									if($_POST['type_answer'])
									{
										$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes_answer` WHERE id=:id");
										$qry2->execute(array('id' => $_POST['type_answer']));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										if(empty($row2['name'])) {$row2['name']='';}
										echo '<option value="'.$_POST['type_answer'].'" selected >'.T_($row2['name']).'</option>';
										$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes_answer` WHERE id!=:id ORDER BY name");
										$qry2->execute(array('id' => $_POST['type_answer']));
										while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
										$qry2->closeCursor();
									}
									else
									{
										$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes_answer` WHERE id=:id ORDER BY name");
										$qry2->execute(array('id' => $globalrow['type_answer']));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										if(empty($row2['name'])) {$row2['name']='';}
										echo '<option value="'.$globalrow['type_answer'].'" selected >'.T_($row2['name']).'</option>';
										$qry2=$db->prepare("SELECT `id`,`name` FROM `ttypes_answer` WHERE  id!=:id ORDER BY name");
										$qry2->execute(array('id' => $globalrow['type_answer']));
										while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';}
										$qry2->closeCursor();
									}
									echo'
								</select>
							</div>
						</div>
						';
					} else {
						echo '<input type="hidden" name="type_answer" value="'.$globalrow['type_answer'].'" />';
					}
				?>
				<!-- END type answer part -->
				<!-- START technician part -->
				<?php
				//lock technician field if technician open ticket for another service and limit service is enable
				if($rparameters['user_limit_service']==1 && $rright['ticket_tech_service_lock']!=0)
				{
					if($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3) //for technician and supervisor
					{
						//check if current technician or supervisor is member of selected service
						if(($_POST['u_service'] && $_POST['u_service']!=0 && $_GET['action']=='new') || ($_GET['action']!='new' && $globalrow['u_service']!=0) && $user_services)
						{
							if($_GET['action']=='new') {$chk_svc=$_POST['u_service'];} else {$chk_svc=$globalrow['u_service'];}
							$check_tech_svc=0;
							foreach($user_services as $value) {if($chk_svc==$value){$check_tech_svc=1;}}
							if($check_tech_svc==0) {$lock_tech=1;} else {$lock_tech=0;}
						} else {$lock_tech=0;}
					} else {$lock_tech=0;}
				} else {$lock_tech=0;}
				?>
				<div class="form-group row <?php if(($rright['ticket_tech_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_tech_disp']==0 && $_GET['action']=='new')) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="technician">
						<?php
							if($lock_tech==0) //case lock field
							{
								if(($_POST['technician']==0 && $_POST['technician']!='') || ($_POST['technician']=='' && $globalrow['technician']==0) && $globalrow['t_group']==0)
								{
									echo '<i title="'.T_('Aucun gestionnaire associé à ce ticket').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';
								}
							}
							echo T_('Gestionnaire').' :';
						?>
						</label>
					</div>
					<div class="col-sm-5">
						<!-- onchange="submit();" -->
						<select class="form-control col-5 d-inline-block" id="technician" name="technician" onchange="loadVal();  <?php if($rright['ticket_tech_mandatory']) {echo ' CheckMandatory(); ';}?>" <?php if($rright['ticket_tech']==0 || $lock_tech==1) {echo ' disabled="disabled" ';}?> >
							<?php
							//add service filter to technician list
							if($rparameters['user_limit_service']==1 && $rright['dashboard_service_only']!=0) //case for user who open ticket to auto-select categories of the service
							{
								if($_POST['u_service']) {$where_service=$_POST['u_service'];} else {$where_service=$globalrow['u_service'];}
								if($rright['ticket_tech_super']!=0 && $rright['ticket_tech_admin']!=0) { //display supervisor and admin in technician list
									$query="SELECT * FROM `tusers` WHERE (profile='0' || profile='4' || profile='3') AND ( id IN (SELECT user_id FROM tusers_services WHERE service_id=$where_service)) OR id='0' ORDER BY lastname ASC, firstname ASC";
								} elseif($rright['ticket_tech_super']!=0) //display supervisor in technician list
								{
									$query="SELECT id,lastname,firstname,disable FROM `tusers` WHERE (profile='0' || profile='3') AND ( id IN (SELECT user_id FROM tusers_services WHERE service_id=$where_service)) OR id='0' ORDER BY id!=0, lastname ASC, firstname ASC";
								} elseif($rright['ticket_tech_admin']!=0)  { //display technician and admin in technician list
									$query="SELECT id,lastname,firstname,disable FROM `tusers` WHERE (profile='0' || profile='4') AND ( id IN (SELECT user_id FROM tusers_services WHERE service_id=$where_service)) OR id='0' ORDER BY id!=0, lastname ASC, firstname ASC";
								} else { //display only technician in technician list
									$query="SELECT id,lastname,firstname,disable FROM `tusers` WHERE profile='0' AND ( id IN (SELECT user_id FROM tusers_services WHERE service_id=$where_service)) OR id='0' ORDER BY id!=0, lastname ASC, firstname ASC";
								}
								//display technician groups
								$query2="SELECT id,name,disable FROM `tgroups` WHERE type='1' AND service=$where_service ORDER BY name";
							} else {
								//display technician and admin in technician list
								 if($rright['ticket_tech_super']!=0 && $rright['ticket_tech_admin']!=0) { // supervisor, admin, technician
									$query = "SELECT * FROM `tusers` WHERE (profile='0' || profile='4' || profile='3') OR id=0 ORDER BY lastname ASC, firstname ASC" ;
								} elseif($rright['ticket_tech_super']!=0)
								{
									$query = "SELECT id,lastname,firstname,disable FROM `tusers` WHERE (profile='0' || profile='3') OR id=0 ORDER BY id!=0, lastname ASC, firstname ASC" ;
								} elseif($rright['ticket_tech_admin']!=0)
								{
									$query = "SELECT id,lastname,firstname,disable FROM `tusers` WHERE (profile='0' || profile='4') OR id=0 ORDER BY id!=0, lastname ASC, firstname ASC" ;
								} else {
									$query="SELECT id,lastname,firstname,disable FROM `tusers` WHERE profile='0' OR id='0' ORDER BY id!=0, lastname ASC, firstname ASC";
								}
								//display technician groups
								$query2="SELECT id,name,disable FROM `tgroups` WHERE type='1' ORDER BY name";
							}

							//display technician list
							if($rparameters['debug']) {echo $query;}
							$query = $db->query($query);
							$tech_selected='0';
							echo '<option value="0">Aucun gestionnaire</option>';
							while ($row = $query->fetch())
							{
								//select technician
								if($_POST['technician']==$row['id']) {
									$selected="selected";
									$tech_selected=$row['id'];
								} elseif(($_POST['technician']=='') && ($globalrow['technician']==$row['id']) && $selected=='') {
									$selected="selected";
									$tech_selected=$row['id'];
								} else {
									$selected='';
								}
								//display each entry
								if($row['id']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.strtoupper(T_($row['lastname'])).' '.$row['firstname'].'</option>';} //case no technician TEMP 3.1.20 && (($_POST['technician']==0) && ($globalrow['technician']!=$row['id']))
								if($row['disable']==0) {echo '<option '.$selected.' value="'.$row['id'].'">'.strtoupper($row['lastname']).' '.$row['firstname'].'</option>';} //all enable technician
								if(($row['disable']==1) && ($selected=='selected') && $row['id']!=0) {echo '<option '.$selected.' value="'.$row['id'].'">'.strtoupper($row['lastname']).' '.$row['firstname'].'</option>';} //case disable technician always attached to this ticket
							}
							$query->closeCursor();

							//display technician group list
							$query2 = $db->query($query2);
							while ($row = $query2->fetch()) {
								if($row['id']==$t_group) {$selected='selected';} else {$selected='';}
								if($row['disable']==0) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.T_($row['name']).'</option>';}
								if(($row['disable']==1) && ($selected=='selected')) {echo '<option '.$selected.' value="G_'.$row['id'].'">[G] '.$row['name'].'</option>';}
							}
							$query2->closeCursor();
							?>
						</select>
						<?php
						//send data in disabled case
						if($rright['ticket_tech']==0) {
							if($globalrow['t_group'])
							{
								echo '<input type="hidden" name="technician" value="G_'.$globalrow['t_group'].'" />';
							} else {
								echo '<input type="hidden" name="technician" value="'.$globalrow['technician'].'" />';
							}
						}
						if($lock_tech==1) echo '<input type="hidden" name="technician" value="'.$tech_selected.'" />';
						?>
					</div>
				</div>
				<!-- END technician part -->
				<!-- START asset part -->
				<?php
					if($rparameters['asset']==1)
					{
						if($rright['ticket_new_asset_disp'])
						{
							echo'
							<div class="form-group row '; if(($rright['ticket_new_asset_disp']==0 && $_GET['action']=='new') || ($rright['ticket_asset_disp']==0 && $_GET['action']!='new')) {echo 'd-none';} echo '" >
								<div class="col-sm-2 col-form-label text-sm-right pr-0">
									<label class="mb-0" for="asset">
										'.T_('Équipement').' :
									</label>
								</div>
								<div class="col-sm-5">
									<select class="form-control col-5" id="asset_id" name="asset_id" '; if(($rright['ticket_asset']==0 && $_GET['action']!='new') || ($rright['ticket_new_asset_disp']==0 && $_GET['action']=='new')) {echo 'disabled="disabled"';} echo' onchange="loadVal(); '; if($rright['ticket_asset_mandatory']) {echo 'CheckMandatory();';} echo '">
										';
										if($_POST['asset_id'])
										{
											$qry2=$db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE id=:id");
											$qry2->execute(array('id' => $_POST['asset_id']));
											$row2=$qry2->fetch();
											$qry2->closeCursor();
											echo '<option value="'.$row2['id'].'" selected >'.T_($row2['netbios']).'</option>';
											if(($globalrow['asset_id'] && $globalrow['user']) || ($_SESSION['profile_id']==3 || $_SESSION['profile_id']==2))
											{
												$qry2=$db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE netbios!='' AND disable='0' AND user=:user ORDER BY id!=0, netbios");
												$qry2->execute(array('user' => $globalrow['user']));
												while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['netbios']).'</option>';}
												$qry2->closeCursor();
											} else {
												$qry2=$db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE netbios!='' AND disable='0' ORDER BY id!=0, netbios");
												$qry2->execute();
												while($row2=$qry2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['netbios']).'</option>';}
												$qry2->closeCursor();
											}
										}
										else
										{
											//find existing value
											$qry2=$db->prepare("SELECT `id`,`netbios` FROM `tassets` WHERE id=:id ORDER BY id");
											$qry2->execute(array('id' => $globalrow['asset_id']));
											$row2=$qry2->fetch();
											$qry2->closeCursor();
											//select none if new ticket
											if($_GET['action']=='new')
											{
												echo '<option value="0">'.T_('Aucun').'</option>';

											} else {
												echo '<option value="'.$row2['id'].'" selected>'.$row2['netbios'].'</option>';
											}
											//user restricted list
											if($_SESSION['profile_id']==3 || $_SESSION['profile_id']==2)
											{
												$query2 = $db->query("SELECT id,netbios FROM `tassets` WHERE id!='$globalrow[asset_id]' AND netbios!='' AND disable='0' AND user='$_SESSION[user_id]' ORDER BY id!=0, netbios");
											} elseif($_POST['user']) {
												$query2 = $db->query("SELECT id,netbios FROM `tassets` WHERE id!='$globalrow[asset_id]' AND netbios!='' AND user='$_POST[user]' AND disable='0' ORDER BY id!=0, netbios");
											} elseif($globalrow['user']) {
												$query2 = $db->query("SELECT id,netbios FROM `tassets` WHERE id!='$globalrow[asset_id]' AND netbios!='' AND user='$globalrow[user]' AND disable='0' ORDER BY id!=0, netbios");
											} else {
												$query2 = $db->query("SELECT id,netbios FROM `tassets` WHERE id!='$globalrow[asset_id]' AND netbios!='' AND disable='0' ORDER BY id!=0, netbios");
											}
											while ($row2 = $query2->fetch()) {echo '<option value="'.$row2['id'].'">'.T_($row2['netbios']).'</option>';}
											$query2->closeCursor();
										}
										echo'
									</select>
									';
									//send data in disabled case
									if($rright['ticket_asset']==0 && $_GET['action']!='new') echo '<input type="hidden" name="asset_id" value="'.$globalrow['asset_id'].'" />';
									echo '
								</div>
							</div>
							';
						}
					}
				?>
				<!-- END asset part -->

				<!-- START category part -->
				<div class="form-group row <?php if(($rright['ticket_cat_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat_disp']==0 && $_GET['action']=='new')) echo 'd-none';?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="category">
							<?php if(($globalrow['category']==0) && ($_POST['category']==0)) {echo '<i id="warning_empty_category" title="'.T_('Aucune catégorie associée').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;';} ?>
							<?php echo T_('Catégorie').' :'; ?>
						</label>
					</div>
					<div class="col-sm-5">
						<!-- onchange="loadVal(); submit(); <?php //if($rright['ticket_cat_mandatory']) {echo ' CheckMandatory(); ';} ?>" -->
						<!--//!\orig - <select <?php if($mobile) {echo 'style="max-width:105px;"';}else{echo 'style="width:auto;"';}?>  class="form-control d-inline-block mb-1 mb-md-0" title="<?php echo T_('Catégorie'); ?>" id="category" name="category" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?>>-->
						<!--/!\new--><select <?php if($mobile) {echo 'style="max-width:105px;"';}else{echo 'style="width:auto;"';}?>  class="form-control d-inline-block mb-1 mb-md-0" title="<?php echo T_('Catégorie'); ?>" id="category" name="category" onchange="loadVal(); submit();" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?>>
						<?php
							//if user limit service restrict category to associated service
							if($rparameters['user_limit_service']==1 && $rright['dashboard_service_only']!=0) //case for user who open ticket to auto-select categories of the service
							{
								//case service field is display
								if($rright['ticket_service_disp'] || $rright['ticket_new_service_disp'])
								{
									if($_POST['u_service']) {$where='WHERE service='.$_POST['u_service'].' OR id=0 ';} else {$where='WHERE service='.$globalrow['u_service'].' OR id=0 ';}
								} else { //case service field not display, using service associated to current user
									//one service case
									if($cnt_service==1){$where='WHERE service='.$user_services['0'].' OR service=0 OR id=0 ';}
									elseif($cnt_service>1) //multi services case
									{
										$where='WHERE ';
										foreach($user_services as $user_service) {$where.="service='$user_service' OR ";}
										$where.='service=0';
									} else {$where='';}
								}
							}else{$where='';}
							$query= $db->query("SELECT id,name FROM `tcategory` $where ORDER BY id!='0', number,name"); //order to display none in first
							while ($row = $query->fetch())
							{
								if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none
								if($_POST['category']!=''){if($_POST['category']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
								else
								{if($globalrow['category']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
							}
							$query->closeCursor();
						?>
						</select>
						<?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="category" value="'.$globalrow['category'].'" />'; //send data in disabled case ?>
						<select <?php if($mobile) {echo 'style="max-width:105px;"';}else{echo 'style="width:auto;"';}?> class="form-control d-inline-block mb-1 mb-md-0" title="<?php echo T_('Sous-catégorie'); ?>" id="subcat" name="subcat" onchange="loadVal(); <?php if($rright['ticket_cat_mandatory']) {echo ' CheckMandatory(); ';} ?>" <?php if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new')) echo ' disabled="disabled" ';?> >
						<?php
							if($_POST['category'])
							{$query= $db->query("SELECT id,name FROM `tsubcat` WHERE cat LIKE '$_POST[category]' ORDER BY name ASC");}
							else
							{$query= $db->query("SELECT id,name FROM `tsubcat` WHERE cat LIKE '$globalrow[category]' ORDER BY name ASC");}
							while ($row = $query->fetch())
							{
								if($row['id']==0) {$row['name']=T_($row['name']);}
								if($_POST['subcat'])
								{
									if($_POST['subcat']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
								}
								else
								{
									if($globalrow['subcat']==$row['id']) echo '<option value="'.$row['id'].'" selected>'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
								}
							}
							$query->closeCursor();
							if($globalrow['subcat']==0 && $_POST['subcat']==0) echo "<option value=\"\" selected></option>";
						?>
						</select>
						<?php
						//send data in disabled case
						if(($rright['ticket_cat']==0 && $_GET['action']!='new') || ($rright['ticket_new_cat']==0 && $_GET['action']=='new'))  echo '<input type="hidden" name="subcat" value="'.$globalrow['subcat'].'" />';
						//cat action buttons
						if($rright['ticket_cat_actions']!=0)
						{
							echo '
							<i class="fa fa-plus-circle text-success text-130 pl-1" title="'.T_('Ajouter une catégorie').'" data-toggle="modal" data-target="#add_cat" ></i>
							<i class="fa fa-pencil-alt text-warning text-130 pl-1" title="'.T_('Modifier une catégorie').'" data-toggle="modal" data-target="#edit_cat" ></i>
							';
						}
						if(!$rright['ticket_cat_mandatory']){echo '<span id="category_field_mandatory"></span><span id="cat_label_mandatory"></span>'; } // avoid error on ticket.js
						?>
					</div>
				</div>
				<!-- END category part -->

                <!-- START observer part -->
                <div class="form-group row">
                    <div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <label class="mb-0" for="category">
                            <?php echo T_('Observateur').' :'; ?>
                        </label>
                    </div>
                    <div class="col-sm-5">
                        <select <?php if($mobile) {echo 'style="max-width:105px;"';}else{echo 'style="width:auto;"';}?> class="form-control d-inline-block mb-1 mb-md-0 select2" title="<?php echo T_('Observateurs'); ?>" id="observer" name="observer[]" multiple>
                            <option><?php echo T_('Aucun'); ?></option>
                            <?php
                            foreach ($users as $observer) {
                                $isSelected = $ticket->hasObserver($observer->getId());
                                ?>
                                <option value="<?php echo $observer->getId() ?>" <?php if ($isSelected) {
                                    echo 'selected';
                                } ?> ><?php echo $observer->getFullName()." (".$observer->getLabo().")" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- END observer part -->

				<!-- START agency part -->
				<?php
				if($rparameters['user_agency'])
				{
					//check if current user have multiple agencies to display select, else no display select and get value of agency
					$qry2=$db->prepare("SELECT COUNT(id) FROM `tusers_agencies` WHERE user_id=:user_id");
					$qry2->execute(array('user_id' => $_SESSION['user_id']));
					$row2=$qry2->fetch();
					$qry2->closeCursor();
					if(($row2[0]==0 && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2) || $rright['ticket_agency']==0)) //case no agency for current user
					{
						echo '<input type="hidden" name="u_agency" value="'.$globalrow['u_agency'].'" />'; //send data without display
					}
					elseif($row2[0]==1 && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2)) //case one agency for current user hide field and transmit data
					{
						$qry3=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
						$qry3->execute(array('user_id' => $_SESSION['user_id']));
						$row3=$qry3->fetch();
						$qry3->closeCursor();
						echo '<input type="hidden" name="u_agency" value="'.$row3['agency_id'].'" />'; //send data without display
					} else //else display field to select agency
					{
						//display select agency field
						echo'
						<div class="form-group row">
							<div class="col-sm-2 col-form-label text-sm-right pr-0">
								<label class="mb-0" for="u_agency">
									'.T_('Agence').' :
								</label>
							</div>
							<div class="col-sm-5">
								<select class="form-control col-5" id="u_agency" name="u_agency" '; if($rright['ticket_agency_mandatory']) {echo ' onchange="CheckMandatory();" ';}echo' >
									';
									$find_agency_id=0;
									if($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2) //display list of agency of current user if it's a user or poweruser
									{
										$query3=$db->query("SELECT agency_id FROM `tusers_agencies` WHERE user_id='$_SESSION[user_id]' AND agency_id IN (SELECT id AS agency_id FROM `tagencies` WHERE disable=0)");
									} elseif(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3) && $_POST['user']) { //case display only user agencies for technician or supervisor profile
										$query3=$db->query("SELECT agency_id FROM `tusers_agencies` WHERE user_id='$_POST[user]' AND agency_id IN (SELECT id AS agency_id FROM `tagencies` WHERE disable=0)");
									} elseif(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3) && $globalrow['user']) {
										$query3=$db->query("SELECT agency_id FROM `tusers_agencies` WHERE user_id='$globalrow[user]' AND agency_id IN (SELECT id AS agency_id FROM `tagencies` WHERE disable=0)");
									} else {
										$query3=$db->query("SELECT id AS agency_id FROM `tagencies` WHERE disable=0 ORDER BY name");
									}
									$count = $query3->rowCount();
									while ($row3 = $query3->fetch())
									{
										//get agency name
										$qry4=$db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE id=:id");
										$qry4->execute(array('id' => $row3['agency_id']));
										$row4=$qry4->fetch();
										$qry4->closeCursor();
										if($globalrow['u_agency']==$row4['id']) {$selected='selected';} else {$selected='';}
										if($count==1 && $_GET['action']=='new') {$selected='selected'; $find_agency_id=1;}
										echo '<option value="'.$row4['id'].'" '.$selected.' >'.T_($row4['name']).'</option>';
									}
									//case for no agency selected
									if($globalrow['u_agency']==0 && $find_agency_id==0 && $_POST['u_agency']==0) {echo '<option value="0" selected >'.T_("Aucune").'</option>';}
									$query3->closeCursor();
									echo'
								</select>
							</div>
						</div>
						';
						if($_GET['action']!='new' && $_POST['u_agency']==$globalrow['u_agency'] && $_POST['u_agency']!=0) {echo '<input type="hidden" name="u_agency" value="'.$globalrow['u_agency'].'" />';} //send data in disabled case
					}
				}
				?>
				<!-- END agency part -->

				<!-- START sender service part -->
				<?php
					if($rright['ticket_sender_service_disp']!=0 && ($_SESSION['profile_id']=='0' || $_SESSION['profile_id']=='3' || $_SESSION['profile_id']=='4'))
					{
						//get service of selected sender
						if($_POST['user']) {
							$query2=$db->query("SELECT id,name FROM tservices WHERE id IN (SELECT service_id FROM tusers_services WHERE user_id=$_POST[user])");
							$cnt_sender_svc=$query2->rowCount();

						} elseif($globalrow['user'])
						{
							$query2=$db->query("SELECT id,name FROM tservices WHERE id IN (SELECT service_id FROM tusers_services WHERE user_id=$globalrow[user])");
							$cnt_sender_svc=$query2->rowCount();
						} else {
							$cnt_sender_svc=0;
						}

						if($cnt_sender_svc>=1)
						{
							echo'
							<div class="form-group row" >
								<div class="col-sm-2 col-form-label text-sm-right pr-0">
									<label class="mb-0" for="sender_service">
										'.T_('Service du demandeur').':
									</label>
								</div>
								<div class="col-sm-5">
									<select class="form-control col-5" id="sender_service" name="sender_service" onchange="loadVal();">
										<option value="0">'.T_('Aucun').'</option>
										';
											//echo '<option value="'.$globalrow['sender_service'].'" selected >'.T_($row2['name']).'</option>';
											while ($row2 = $query2->fetch())
											{
												if($_POST['sender_service']==$row2['id'])
												{
													echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>'; //selected case
												} elseif($globalrow['sender_service']==$row2['id']) {
													echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
												} elseif($cnt_sender_svc==1){
													echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
												} else {
													echo '<option value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
												}
											}
										echo'
									</select>
									';
									//send data in disabled case
									echo '
								</div>
							</div>
							';
						} else {
							if($globalrow['sender_service']!=0) //if value exist keep value
							{
								echo '<input type="hidden" name="sender_service" value="'.$globalrow['sender_service'].'" />';
							}
						}
						$query2->closeCursor();
					} else { //single user case
						if($globalrow['sender_service']!=0) //if value exist keep value
						{
							//disable or hide case to keep value
							echo '<input type="hidden" name="sender_service" value="'.$globalrow['sender_service'].'" />';
						} else {
							//get sender service id to put in database
							$qry2=$db->prepare("SELECT MAX(id) FROM tservices WHERE id IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id)");
							$qry2->execute(array('user_id' => $_SESSION['user_id']));
							$sender_svc_id=$qry2->fetch();
							$qry2->closeCursor();
							if($sender_svc_id[0]) {echo '<input type="hidden" name="sender_service" value="'.$sender_svc_id[0].'" />';}
						}
					}
				?>
				<!-- END sender service part -->
				<!-- START place part if parameter is on -->
				<?php
				if($rparameters['ticket_places']==1)
				{
					echo '
					<div class="form-group row">
						<div class="col-sm-2 col-form-label text-sm-right pr-0">
							<label class="mb-0" for="ticket_places">'.T_('Lieu').' :</label>
						</div>
						<div class="col-sm-5">
							<select class="form-control col-5" id="ticket_places" name="ticket_places" '; if($rright['ticket_place']==0 && $_GET['action']!='new') {echo 'disabled="disabled"';} echo' >
								';
								if($_POST['ticket_places'])
								{
									$qry=$db->prepare("SELECT `id`,`name` FROM `tplaces` ORDER BY name ASC");
									$qry->execute();
									while($row=$qry->fetch()) {if($_POST['ticket_places']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';} else {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}}
									$qry->closeCursor();
								} else {
									$qry=$db->prepare("SELECT `id`,`name` FROM `tplaces` ORDER BY name ASC");
									$qry->execute();
									while($row=$qry->fetch()){if($globalrow['place']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';} else {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}}
									$qry->closeCursor();
								}
							echo '
							</select>
							';
							if($rright['ticket_place']==0 && $_GET['action']!='new')  echo '<input type="hidden" name="ticket_places" value="'.$globalrow['place'].'" />'; //send data in disabled case
							echo '
						</div>
					</div>
					';
				}
				?>
				<!-- END place part -->
				<!-- START title part -->
				<div class="form-group row <?php if($rright['ticket_title_disp']==0) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
                        <?php echo '<i id="warning_empty_category" title="'.T_('Le champ Titre est requis').'" class="fa fa-exclamation-triangle text-danger-m2 text-130"></i>&nbsp;'; ?>
						<label class="mb-0" for="title"><?php echo T_('Titre');?> :</label>
					</div>
					<div class="col-sm-5">
						<input class="form-control col-10" name="title" id="title" type="text" maxlength="100"
							size="<?php if(!$mobile) {echo '50';} else {echo '30';}?>"
							value="<?php if($_POST['title']) {echo htmlspecialchars($_POST['title']);} else {echo htmlspecialchars($globalrow['title']);} ?>"
							<?php
							if($rright['ticket_title']==0 && $_GET['action']!='new') {echo ' readonly="readonly" ';}
							if($rright['ticket_title_mandatory']) {echo ' required="required" onchange="CheckMandatory();" ';}
							?>
						/>
					</div>
				</div>
				<!-- END title part -->
				<!-- START description part -->
				<div class="form-group row <?php if($rright['ticket_description_disp']==0) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="text">
						<?php
						echo T_('Description');
						?> :
						</label>
					</div>
					<div class="col-sm-5">
						<table id="description" border="1" width="<?php if($mobile==0) {echo '780';} else {echo '285';}?>" style="border: 1px solid #D8D8D8;" >
							<tr>
								<td <?php if(!$rright['ticket_description']) {echo 'style="padding:5px"';} ?>>
									<?php
									if($rright['ticket_description'] || $_GET['action']=='new')
									{
										//display editor
										echo '
										<div id="editor" '; if($rright['ticket_description_mandatory']) {echo 'onchange="CheckMandatory();"';} echo' class="bootstrap-wysiwyg-editor pl-2 pt-1" style="min-height:100px; max-width:775px">';
											if($_POST['text'] && $_POST['text']!='') echo "$_POST[text]"; else echo $globalrow['description'];
											if($_GET['action']=='new' && !$_POST['user']) {echo '';}	 echo'
										</div>
										<input id="hidden_description" type="hidden" id="text" name="text" />
										';
									} else {
										echo $globalrow['description'];
										echo '<input id="hidden_description" type="hidden" name="text" value="'.htmlentities($globalrow['description']).'" />';
									}
									?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<!-- END description part -->
				<!-- START resolution part -->
				<div class="form-group row <?php if(($rright['ticket_resolution_disp']==0 && $_GET['action']!='new') || ($rright['ticket_new_resolution_disp']==0 && $_GET['action']=='new')) echo 'd-none';?>" >
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="text"><?php echo T_('Résolution'); ?> :</label>
					</div>
					<div class="col-sm-5">
						<?php include "./thread.php"; ?>
					</div>
				</div>
				<a id="down"></a>
				<!-- END resolution part -->
				<!-- START attachement part -->
				<?php
				if($rright['ticket_attachment'])
				{
					//check existing attachments
					$qry=$db->prepare("SELECT `id` FROM `tattachments` WHERE `ticket_id`=:ticket_id");
					$qry->execute(array('ticket_id' => $_GET['id']));
					$row=$qry->fetch();
					$qry->closeCursor();
					if($globalrow['state']==3 && empty($row[0]))
					{
						//hide field
					} else {
						echo '
						<div class="form-group row">
							<div class="col-sm-2 col-form-label text-sm-right pr-0">
								<label class="mb-0" for="attachment">'.T_('Fichier joint').' :</label>
							</div>
							<div class="col-sm-5">
								<table border="1" style="border:1px solid #D8D8D8; min-width:265px;" >
									<tr>
										<td style="padding:15px;">';
											include "./attachment.php";
											echo '
										</td>
									</tr>
								</table>
							</div>
						</div>';
					}
				}

				?>
				<!-- END attachement part -->
				<!-- START create date part -->
				<div class="form-group row  <?php if(!$rright['ticket_date_create_disp']) {echo 'd-none';}?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="date_create"><?php echo T_('Date de création'); ?> :</label>
					</div>
					<div class="col-sm-5">
						<input type="hidden" name="hide" id="hide" value="1"/>
						<input type="text" class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> datetimepicker-input" id="date_create" data-toggle="datetimepicker" data-target="#date_create" name="date_create" autocomplete="off" value="<?php if($_POST['date_create']) {echo $_POST['date_create'];} else {echo DatetimeToDisplay($globalrow['date_create']);} ?>" <?php if(!$rright['ticket_date_create']) {echo 'readonly="readonly"';}?> />
					</div>
				</div>
				<!-- END create date part -->
				<!-- START hope date part -->
<!--
				<div class="form-group row <?php echo $date_hope_error; if(!$rright['ticket_date_hope_disp']) {echo ' d-none';}?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="date_hope">
							<?php
								//display warning if hope date is passed
								$qry=$db->prepare("SELECT DATEDIFF(NOW(), :date_hope)");
								$qry->execute(array('date_hope' => $globalrow['date_hope']));
								$row=$qry->fetch();
								$qry->closeCursor();
								if($row[0]>0 && ($globalrow['state']!=3 && $globalrow['state']!=4)) echo '<i title="'.T_('Date de résolution dépassée de').' '.$row[0].' '.T_('jours').'" class="fa fa-exclamation-triangle text-warning text-130" ></i>&nbsp;';
								echo T_('Date de résolution estimée');
							?> :
						</label>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> datetimepicker-input" <?php if($rright['ticket_date_hope_mandatory']) {echo 'onchange="CheckMandatory();"'; } ?> id="date_hope" data-toggle="datetimepicker" data-target="#date_hope" autocomplete="off" name="date_hope"
							value="<?php if($_POST['date_hope']) {echo $_POST['date_hope'];} elseif($globalrow['date_hope'] && $globalrow['date_hope']!='0000-00-00') {echo DateToDisplay($globalrow['date_hope']);} ?>"
							<?php if(!$rright['ticket_date_hope']) {echo ' readonly="readonly" ';} if($rright['ticket_date_hope_mandatory']) {echo ' required="required" ';}?>
						/>
					</div>
				</div>
-->
				<!-- END hope date part -->
				<!-- START resolution date part -->
<!--
				<div class="form-group row <?php if(!$rright['ticket_date_res_disp']) {echo 'd-none';}?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for=""><?php echo T_('Date de résolution'); ?> :</label>
					</div>
					<div class="col-sm-5">
						<input type="text" class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> datetimepicker-input" id="date_res" data-toggle="datetimepicker" data-target="#date_res" autocomplete="off" name="date_res"
							value="<?php if($_POST['date_res'] && strpos($_POST['date_res'],'-')) {echo DatetimeToDisplay($_POST['date_res']);}elseif($_POST['date_res']) {echo $_POST['date_res'];} elseif($globalrow['date_res']) {echo DatetimeToDisplay($globalrow['date_res']);} ?>"
							<?php if(!$rright['ticket_date_res']) {echo 'readonly="readonly"';}?>
						/>
					</div>
				</div>
-->
				<!-- END resolution date part -->
				<!-- START time part -->
<!--
				<div class="form-group row <?php if(!$rright['ticket_time_disp']) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="time"><?php echo T_('Temps passé'); ?> :</label>
					</div>
					<div class="col-sm-5">
						<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?>" id="time" name="time" <?php if(!$rright['ticket_time']) {echo 'disabled';}?> >
							<?php
								$qry=$db->prepare("SELECT `min`,`name` FROM `ttime` ORDER BY min ASC");
								$qry->execute();
								while($row=$qry->fetch())
								{
									if(($_POST['time']==$row['min'])||($globalrow['time']==$row['min']))
									{
										echo '<option selected value="'.$row['min'].'">'.$row['name'].'</option>';
										$selected_time=$row['min'];
									} else {
										echo '<option value="'.$row['min'].'">'.$row['name'].'</option>';
									}
								}
								$qry->closeCursor();
								//special case when time entry was modify or delete from admin time list
								$qry=$db->prepare("SELECT `id` FROM `ttime` WHERE min=:min");
								$qry->execute(array('min' => $globalrow['time']));
								$row=$qry->fetch();
								$qry->closeCursor();
								if(!$row && $_GET['action']!='new') { echo '<option selected value="'.$globalrow['time'].'">'.$globalrow['time'].'m</option>';}
							?>
						</select>
						<?php
						//send value in lock select case
						if(!$rright['ticket_time']) {echo '<input type="hidden" name="time" value="'.$selected_time.'" />';}
						?>
					</div>
				</div>
-->
				<!-- END time part -->
				<!-- START time hope part -->
<!--
				<div class="form-group row <?php if(!$rright['ticket_time_hope_disp']) {echo 'd-none';}?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="time_hope">
						<?php
							//display error if time hope < time pass
							if(($globalrow['time_hope']<$globalrow['time']) && $globalrow['state']!='3') {echo '<i class="pr-1 fa fa-exclamation-triangle text-danger-m2 text-130" title="'.T_('Le temps est sous-estimé').'"></i>';}
							echo T_('Temps estimé');
						?> :
						</label>
					</div>
					<div class="col-sm-5">
						<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?>" id="time_hope" name="time_hope" <?php if($rright['ticket_time_hope']==0) echo 'disabled';?> >
							<?php

							$qry=$db->prepare("SELECT `min`,`name` FROM `ttime` ORDER BY min ASC");
							$qry->execute();
							while($row=$qry->fetch())
							{
								if(($_POST['time_hope']==$row['min']) || ($globalrow['time_hope']==$row['min']))
								{
									echo '<option selected value="'.$row['min'].'">'.$row['name'].'</option>';
									$selected_time_hope=$row['min'];
								} else {
									echo '<option value="'.$row['min'].'">'.$row['name'].'</option>';
									$selected_time_hope=$row['min'];
								}
							}
							$qry->closeCursor();
							//special case when time entry was modify or delete from admin time list
							$qry=$db->prepare("SELECT `id` FROM `ttime` WHERE min=:min");
							$qry->execute(array('min' => $globalrow['time_hope']));
							$row=$qry->fetch();
							$qry->closeCursor();
							if(!$row) { echo '<option selected value="'.$globalrow['time_hope'].'">'.$globalrow['time_hope'].'m</option>';}
							?>
						</select>
						<?php
						//send value in lock or hide case
						if($rright['ticket_time_hope']==0 || $rright['ticket_time_hope_disp']==0) {
							echo '<input type="hidden" name="time_hope" value="'.$globalrow['time_hope'].'" />';
						}
						?>
					</div>
				</div>
-->
				<!-- END time hope part -->
				<!-- START priority part -->
<!--
				<div class="form-group row <?php if(!$rright['ticket_priority_disp']) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="priority">
							<?php echo T_('Priorité'); ?> :
						</label>
					</div>
					<div class="col-sm-5">
						<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> d-inline-block mb-1 mb-md-0" id="priority" name="priority" <?php if(!$rright['ticket_priority']) {echo ' disabled ';} if($rright['ticket_priority_mandatory']) {echo ' required onchange="CheckMandatory();" ';} ?>>
							<?php
							if($rright['ticket_priority_mandatory']) {echo '<option value=""></option>';}
							//if user limit service restrict priority to associated service
							if($rparameters['user_limit_service'] && $rright['dashboard_service_only'] && $rright['ticket_priority_service_limit'])
							{
								if($_POST['u_service']) {$where=' service='.$_POST['u_service'].' ';} else {$where=' service='.$globalrow['u_service'].' ';}
								$old_priority=1;
								$query2 = $db->query("SELECT id,name FROM `tpriority` WHERE $where OR id=0 ORDER BY number DESC");
								while ($row2 = $query2->fetch()) {
									//select entry
									$selected='';
									if($_POST['priority'] && $row2['id']==$_POST['priority'])
									{$selected='selected';}
									elseif($globalrow['priority'] && $row2['id']==$globalrow['priority'])
									{$selected='selected';}
									if($globalrow['priority']==$row2['id']) {$old_priority=0;}
									echo '<option '.$selected.' value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
								}
								$query2->closeCursor();
								//keep old data
								if($old_priority==1 && $_GET['action']!='new') {
									$qry2=$db->prepare("SELECT `id`,`name` FROM `tpriority` WHERE id=:id");
									$qry2->execute(array('id' => $globalrow['priority']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
								}
							} else { //case no limit service
								if($_POST['priority'])
								{
									//find row to select
									$qry=$db->prepare("SELECT `name` FROM `tpriority` WHERE id=:id");
									$qry->execute(array('id' => $_POST['priority']));
									$row=$qry->fetch();
									$qry->closeCursor();
									echo '<option value="'.$_POST['priority'].'" selected >'.T_($row['name']).'</option>';
									//display all entries without selected
									$selected_priority=$_POST['priority'];
									$qry=$db->prepare("SELECT DISTINCT(id),`name` FROM `tpriority` WHERE `id`!=:id ORDER BY `number` DESC");
									$qry->execute(array('id' => $_POST['priority']));
									while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
									$qry->closeCursor();
								} else {
									if($globalrow['priority'])
									{
										//find row to select
										$qry=$db->prepare("SELECT DISTINCT(id),`name` FROM `tpriority` WHERE `id`=:id");
										$qry->execute(array('id' => $globalrow['priority']));
										$row=$qry->fetch();
										$qry->closeCursor();
										echo '<option value="'.$globalrow['priority'].'" selected >'.T_($row['name']).'</option>';
										$selected_priority=$globalrow['priority'];
									} else {$selected_priority='';}
									$qry=$db->prepare("SELECT `id`,`name` FROM `tpriority` WHERE id!=:id ORDER BY `number` DESC");
									$qry->execute(array('id' => $globalrow['priority']));
									while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'" >'.T_($row['name']).'</option>';}
									$qry->closeCursor();
								}
							}
							?>
						</select>
						<?php
						//send value in lock select case
						if(!$rright['ticket_priority'] || !$rright['ticket_priority_disp']) {echo '<input type="hidden" name="priority" value="'.$globalrow['priority'].'" />';}

						//display priority icon
						if($_POST['priority']) {$check_id=$_POST['priority'];} elseif($globalrow['priority']) {$check_id=$globalrow['priority'];} else {$check_id=6;}
						$qry=$db->prepare("SELECT `name`,`color` FROM `tpriority` WHERE id=:id");
						$qry->execute(array('id' => $check_id));
						$row=$qry->fetch();
						$qry->closeCursor();
						if(empty($row['name'])) {$row['name']='';}
						if(empty($row['color'])) {$row['color']='';}
						if($row['name']) {echo '<i title="'.T_($row['name']).'" class="fa fa-exclamation-triangle text-130 pl-1" style=" color:'.T_($row['color']).'" ></i>';}
						?>
					</div>
				</div>
-->
				<!-- END priority part -->
				<!-- START criticality part -->
<!--
				<div class="form-group row <?php if(!$rright['ticket_criticality_disp']) {echo 'd-none';} ?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="criticality" >
							<?php echo T_('Criticité'); ?> :
						</label>
					</div>
					<div class="col-sm-5">
						<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> d-inline-block mb-1 mb-md-0" id="criticality" name="criticality" <?php if($rparameters['availability']==1) {echo 'onchange="loadVal(); submit();"';}  if($rright['ticket_criticality']==0) {echo ' disabled ';} if($rright['ticket_criticality_mandatory']) {echo ' required onchange="CheckMandatory();"';}?>>
							<?php
							if($rright['ticket_criticality_mandatory']) {echo '<option value=""></option>';}
							//if user limit service restrict criticality to associated service
							if($rparameters['user_limit_service'] && $rright['dashboard_service_only'] && $rright['ticket_criticality_service_limit'])
							{
								if($_POST['u_service']) {$where=' service='.$_POST['u_service'].' ';} else {$where=' service='.$globalrow['u_service'].' ';}
								$old_criticality=1;
								$query2 = $db->query("SELECT id,name FROM `tcriticality` WHERE $where OR id=0 ORDER BY number DESC");
								while ($row2 = $query2->fetch()) {
									//select entry
									$selected='';
									if($_POST['criticality'] && $row2['id']==$_POST['criticality'])
									{$selected='selected';}
									elseif($globalrow['criticality'] && $row2['id']==$globalrow['criticality'])
									{$selected='selected';}
									if($globalrow['criticality']==$row2['id']) {$old_criticality=0;}
									echo '<option '.$selected.' value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
								}
								$query2->closeCursor();

								//keep old data
								if($old_criticality==1 && $_GET['action']!='new') {
									$qry2=$db->prepare("SELECT `id`,`name` FROM `tcriticality` WHERE id=:id");
									$qry2->execute(array('id' => $globalrow['criticality']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									echo '<option selected value="'.$row2['id'].'">'.T_($row2['name']).'</option>';
								}
								$selected_criticality=''; //init var
							} else { //case no service limit
								if($_POST['criticality'])
								{
									//find row to select
									$qry=$db->prepare("SELECT `id`,`name` FROM `tcriticality` WHERE id=:id");
									$qry->execute(array('id' => $_POST['criticality']));
									$row=$qry->fetch();
									$qry->closeCursor();
									echo '<option value="'.$_POST['criticality'].'" selected >'.T_($row['name']).'</option>';
									//display all entries without selected
									$selected_criticality=$_POST['criticality'];
									$qry=$db->prepare("SELECT DISTINCT(id),name FROM `tcriticality` WHERE id!=:id ORDER BY number DESC");
									$qry->execute(array('id' => $_POST['criticality']));
									while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
									$qry->closeCursor();
								}
								else
								{
									if($globalrow['criticality'])
									{
										//find row to select
										$qry=$db->prepare("SELECT DISTINCT(id),name FROM `tcriticality` WHERE id=:id");
										$qry->execute(array('id' => $globalrow['criticality']));
										$row=$qry->fetch();
										$qry->closeCursor();
										echo '<option value="'.$globalrow['criticality'].'" selected >'.T_($row['name']).'</option>';
										$selected_criticality=$globalrow['criticality'];
									} else {$selected_criticality='';}
									//display all entries without selected
									$qry=$db->prepare("SELECT `id`,`name` FROM `tcriticality` WHERE id!=:id ORDER BY number DESC");
									$qry->execute(array('id' => $globalrow['criticality']));
									while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
									$qry->closeCursor();
								}
							}
							?>
						</select>
						<?php
						//send value in lock select case
						if($rright['ticket_criticality']==0) {echo '<input type="hidden" name="criticality" value="'.$selected_criticality.'" />';}

						//display criticality icon
						if($_POST['criticality']) {$check_id=$_POST['criticality'];} else {$check_id=$globalrow['criticality'];}
						$qry=$db->prepare("SELECT `color`,`name` FROM `tcriticality` WHERE `id`=:id");
						$qry->execute(array('id' => $check_id));
						$row=$qry->fetch();
						$qry->closeCursor();
						if($row['name']) {echo '&nbsp;<i title="'.T_($row['name']).'" class="fa fa-bullhorn text-130" style="color:'.$row['color'].'" ></i>';}
						?>
					</div>
				</div>
-->
				<!-- END criticality part -->
				<!-- START state part -->
				<div class="form-group row <?php if($rright['ticket_state_disp']==0) echo 'd-none';?>">
					<div class="col-sm-2 col-form-label text-sm-right pr-0">
						<label class="mb-0" for="state"><?php echo T_('État'); ?> :</label>
					</div>
					<div class="col-sm-5">
						<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-5';} ?> d-inline-block" id="state"  name="state" <?php if($rright['ticket_state']==0 || $lock_tech==1) echo 'disabled';?> >
							<?php
							//selected value
							if($_POST['state'])
							{
								$qry=$db->prepare("SELECT `name` FROM `tstates` WHERE `id`=:id");
								$qry->execute(array('id' => $_POST['state']));
								$row=$qry->fetch();
								$qry->closeCursor();
								echo '<option value="'.$_POST['state'].'" selected >'.T_($row['name']).'</option>';
								$selected_state=$_POST['state'];
							} else {
								$qry=$db->prepare("SELECT `name` FROM `tstates` WHERE `id`=:id");
								$qry->execute(array('id' => $globalrow['state']));
								$row=$qry->fetch();
								$qry->closeCursor();
								if(!$row) {
  								echo '<option value="'.$globalrow['state'].'" selected >'.T_($row['name']).'</option>';
								}
								$selected_state=$globalrow['state'];
							}
							$qry=$db->prepare("SELECT `id`,`name` FROM `tstates` WHERE `id`!=:id1 AND `id`!=:id2 ORDER BY `number`");
							$qry->execute(array('id1' => $_POST['state'],'id2' => $globalrow['state']));
							while($row=$qry->fetch())
							{
								if($_SESSION['profile_id']==2 && $row['id']==3){}  //special case to hide resolve state for user only
								else {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
							}
							$qry->closeCursor();
							?>
						</select>
						<?php
						//send value in lock select case
						if($rright['ticket_state']==0 || $lock_tech==1) {echo '<input type="hidden" name="state" value="'.$selected_state.'" />';}

						//display state icon
						$qry=$db->prepare("SELECT `display`,`description` FROM `tstates` WHERE `id`=:id");
						$qry->execute(array('id' => $globalrow['state']));
						$row=$qry->fetch();
						$qry->closeCursor();
						if (!$row) {

						}
						else {
                                                    echo '&nbsp;<span class="'.$row['display'].'" title="'.T_($row['description']).'">&nbsp;</span>';
						}
						?>
					</div>
				</div>
				<!-- END state part -->
				<!-- START availability part -->
				<?php

				//check if the availability parameter is on and condition parameter
				if($rparameters['availability']==1)
				{
						if(
							($rparameters['availability_condition_type']=='criticality' && ($globalrow['criticality']==$rparameters['availability_condition_value'] || $_POST['criticality']==$rparameters['availability_condition_value']))
							||
							($rparameters['availability_condition_type']=='types' && ($globalrow['type']==$rparameters['availability_condition_value'] || $_POST['type']==$rparameters['availability_condition_value']))
						)
						{
							//calculate time
							if($globalrow['start_availability']!='0000-00-00 00:00:00' && $globalrow['end_availability']!='0000-00-00 00:00:00')
							{
								$t1 =strtotime($globalrow['start_availability']) ;
								$t2 =strtotime($globalrow['end_availability']) ;
								$time=(($t2-$t1)/60)/60;
								$time="($time h)";
							} else $time='';

							if($_POST['start_availability'])
							{
								$start_availability=$_POST['start_availability'];
							} elseif($globalrow['start_availability']!='0000-00-00 00:00:00')
							{
								$start_availability=date("d/m/Y H:i:s",strtotime($globalrow['start_availability']));
							} else {
								$start_availability=date("d/m/Y H:i:s");
							}
							if($_POST['end_availability'])
							{
								$end_availability=$_POST['end_availability'];
							} else
							if($globalrow['start_availability']!='0000-00-00 00:00:00') {
								$end_availability=date("d/m/Y H:i:s",strtotime($globalrow['end_availability']));
							} else {
								$end_availability=date("d/m/Y H:i:s");
							}
							echo'
							<div class="form-group row '; if($rright['ticket_availability_disp']==0) echo 'd-none'; echo '">
								<div class="col-sm-2 col-form-label text-sm-right pr-0">
									<label class="mb-0" for="start_availability">'.T_("Début de l'indisponibilité").' :</label>
								</div>
								<div class="col-sm-2">
									<input  type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#start_availability" id="start_availability" name="start_availability"  value="'.$start_availability.'"';                							    	    echo '"';
												if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
									echo '
									>
								</div>
							</div>
							<div class="form-group row '; if($rright['ticket_availability_disp']==0) echo 'd-none'; echo '">
								<div class="col-sm-2 col-form-label text-sm-right pr-0">
									<label class="mb-0" for="end_availability">'.T_("Fin de l'indisponibilité").' :</label>
								</div>
								<div class="col-sm-2">
									<input type="text" class="form-control datetimepicker-input" data-toggle="datetimepicker" data-target="#end_availability" id="end_availability" name="end_availability"  value="'.$end_availability.'"';
										if($rright['ticket_availability']==0) echo ' readonly="readonly" ';
									echo '
									>
									'.$time.'
								 </div>
							</div>
							<div class="form-group row '; if($rright['ticket_availability_disp']==0) echo 'd-none'; echo '">
								<div class="col-sm-2 col-form-label text-sm-right pr-0">
									<label class="mb-0" for="availability_planned">'.T_('Indisponibilité planifiée').' :</label>
								</div>
								<div class="col-sm-2">
									<input type="checkbox"'; if($globalrow['availability_planned']==1) {echo "checked";} echo ' name="availability_planned" value="1" />
								</div>
							</div>
							';
						}
				}
				?>
				<!-- END availability part -->

				<!-- START buttons -->
				<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center">
					<?php
					if(!$hide_button)
					{
						if(($rright['ticket_save']!=0 && $_GET['action']!='new') || ($rright['ticket_new_save']!=0 && $_GET['action']=='new'))
						{
							echo '
							<button title="CTRL+S" accesskey="s" name="modify" id="modify" value="modify" type="submit" class="btn btn-secondary btn-success">
								<i class="fa fa-save"></i>
								';
								if(!$mobile) {echo '&nbsp;'.T_('Enregistrer');}
								echo '
							</button>
							&nbsp;
							';
						}
						if($rright['ticket_save_close']!=0)
						{
							echo '
							<button title="ALT+SHIFT+f" accesskey="f" name="quit" id="quit" value="quit" type="submit" class="btn btn-secondary btn-purple">
								<i class="fa fa-save"></i>
								';
								if(!$mobile) {echo '&nbsp;'.T_('Enregistrer et Fermer');}
								echo '
							</button>
							&nbsp;
							';
						}
						if($rright['ticket_new_send']!=0 && $_GET['action']=='new')
						{
							echo '
							<button name="send" id="send" value="send" type="submit" class="btn btn-secondary btn-success">
								';
								if(!$mobile) {echo T_('Envoyer').'&nbsp;';}
								echo '
								<i class="fa fa-arrow-right"></i>
							</button>
							&nbsp;
							';
						}
						if($rright['ticket_close']!=0 && $_POST['state']!='3' && $globalrow['state']!='3' && $_GET['action']!='new' && $lock_tech==0)
						{
							echo '
							<button name="close" id="close" value="close" type="submit" class="btn btn-secondary btn-grey">
								<i class="fa fa-check"></i>
								';
								if(!$mobile) {echo '&nbsp;'.T_('Clôturer le ticket');}
								echo '
							</button>
							&nbsp;
							';
						}
						if($rright['ticket_send_mail']!=0)
						{
							echo '
							<button title="ALT+SHIFT+m" accesskey="m" name="mail" id="mail" value="mail" type="submit" class="btn btn-secondary btn-info">
								<i class="fa fa-envelope"></i>
								';
								if(!$mobile) {echo '&nbsp;'.T_('Envoyer un mail');}
								echo '
							</button>
							&nbsp;
							';
						}
						if($rright['ticket_cancel']!=0)
						{
							echo '
							<button title="ALT+SHIFT+c" accesskey="c" name="cancel" id="cancel" value="cancel" type="submit" class="btn btn-secondary btn-danger" formnovalidate>
								<i class="fa fa-times"></i>
								';
								if(!$mobile) {echo '&nbsp;'.T_('Annuler');}
								echo '
							</button>
							';
						}
					}
					?>
				</div>
				<!-- END buttons -->
			</div> <!-- div end p-3 -->
		</div> <!-- div end body card -->
	</form>
</div> <!-- div end card -->

<!-- datetime picker scripts  -->
<script type="text/javascript" src="./components/moment/min/moment.min.js"></script>
<?php
	if($ruser['language']=='fr_FR') {echo '<script src="./components/moment/locale/fr.js" charset="UTF-8"></script>';}
	if($ruser['language']=='de_DE') {echo '<script src="./components/moment/locale/de.js" charset="UTF-8"></script>';}
	if($ruser['language']=='es_ES') {echo '<script src="./components/moment/locale/es.js" charset="UTF-8"></script>';}
?>
<script src="./components/tempus-dominus/build/js/tempusdominus-bootstrap-4.min.js" charset="UTF-8"></script>
<?php
	//call mandatory script to update fields color
	if(
		$rright['ticket_criticality_mandatory'] ||
		$rright['ticket_priority_mandatory'] ||
		$rright['ticket_date_hope_mandatory'] ||
		$rright['ticket_description_mandatory'] ||
		$rright['ticket_title_mandatory'] ||
		$rright['ticket_agency_mandatory'] ||
		$rright['ticket_asset_mandatory'] ||
		$rright['ticket_cat_mandatory'] ||
		$rright['ticket_tech_mandatory'] ||
		$rright['ticket_service_mandatory'] ||
		$rright['ticket_type_mandatory']
	) {require('includes/ticket_mandatory.php');}
?>
<script type="text/javascript">
	<?php
	if($rright['ticket_description'] || $_GET['action']=='new')
	{
		//allow past clipboard screenshot with chrome
		echo '
		document.getElementById("editor").focus();
		document.body.addEventListener("paste", function(e) {
			for (var i = 0; i < e.clipboardData.items.length; i++) {
				if(e.clipboardData.items[i].kind == "file" && e.clipboardData.items[i].type == "image/png") {
					var imageFile = e.clipboardData.items[i].getAsFile();
					var fileReader = new FileReader();
					fileReader.onloadend = function(e) {
						var image = document.createElement("IMG");
						image.src = this.result;
						var range = window.getSelection().getRangeAt(0);
						range.insertNode(image);
						range.collapse(false);
						var selection = window.getSelection();
						selection.removeAllRanges();
						selection.addRange(range);
					};
					fileReader.readAsDataURL(imageFile);
					e.preventDefault();
					break;
				}
			}
		});
		';
	}
	?>

	jQuery(function($) {
		//CTRL+S to save ticket
		$(document).keydown(function(e) {
			var key = undefined;
			var possible = [ e.key, e.keyIdentifier, e.keyCode, e.which ];
			while (key === undefined && possible.length > 0)
			{
				key = possible.pop();
			}
			if (key && (key == '115' || key == '83' ) && (e.ctrlKey || e.metaKey) && !(e.altKey))
			{
				e.preventDefault();
				 $('#myform #modify').click();
				return false;
			}
			return true;
		});

		<?php
			//datetimepicker parameters
			if($rright['ticket_date_create']){
				echo "
					var date = moment($('#date_create').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
					$('#date_create').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss' });
				";
			}
			if($rright['ticket_date_res'])
			{
				echo "
					var date = moment($('#date_res').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
					$('#date_res').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss'});
				";
			}
			if($rright['ticket_date_hope'])
			{
				echo "
					var date = moment($('#date_hope').val(), 'DD-MM-YYYY').toDate();
					$('#date_hope').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
				";
			}
			if($rparameters['availability'])
			{
				echo "
				var date = moment($('#start_availability').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
				$('#start_availability').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss' });
				var date = moment($('#end_availability').val(), 'DD-MM-YYYY hh:mm:ss').toDate();
				$('#end_availability').datetimepicker({ date:date, format: 'DD/MM/YYYY HH:mm:ss' });
				";
			}
		?>
		$('#add_calendar_start').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});
		$('#add_calendar_end').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});
		$('#add_reminder').datetimepicker({format: 'DD/MM/YYYY HH:mm:ss'});

		//get and display user informations
			//read informations of current field
			var e = document.getElementById("user");
			var user = e.options[e.selectedIndex].value;
			//get user information if exist
			if(user!=0){GetUserInfos(user);}
			//case switch by user
			$('#user').change(function(){
				//console.info('user switch detected');
				var user = $(this).val();
				if(user!=0){GetUserInfos(user);}
			});
			//function to add user information to current page
			function GetUserInfos(user) {
				var dataString = "user="+user;
				$.ajax({
					type: "POST",
					url: "includes/ticket_userinfos.php?token=<?php echo $_COOKIE["token"]; ?>&ticket=<?php echo $_GET['id']; ?>",
					data: dataString,
					success: function(result){
						//console.log('JSON received :', result)
						var data = JSON.parse(result);
						if(data.phone) {$("#user_phone").html('&nbsp;&nbsp;<a href="tel:'+data.phone+'"><i title="<?php echo T_('Téléphoner au'); ?> '+data.phone+'" class="fa fa-phone text-info"></i></a> '+data.phone);} else {$("#user_phone").html('');}
						if(data.mobile) {$("#user_mobile").html('&nbsp;&nbsp;<a href="tel:'+data.mobile+'"><i title="<?php echo T_('Téléphoner au'); ?> '+data.mobile+'" class="fa fa-mobile text-info"></i></a> '+data.mobile);} else {$("#user_mobile").html('');}
						if(data.mail) {$("#user_mail").html('&nbsp;&nbsp;<a href="mailto:'+data.mail+'"><i title="<?php echo T_('Envoyer un mail sur'); ?> '+data.mail+'" class="fa fa-envelope text-info"></i></a>');} else {$("#user_mail").html('');}
						if(data.function) {$("#user_function").html('&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Fonction'); ?> '+data.function+'" class="fa fa-user text-info"></i></a> '+data.function);} else {$("#user_function").html('');}
						if(data.service) {$("#user_service").html('&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Service'); ?> '+data.service+'" class="fa fa-users text-info"></i></a> '+data.service);} else {$("#user_service").html('');}
						if(data.agency) {$("#user_agency").html('&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Agence'); ?> '+data.agency+'" class="fa fa-globe text-info"></i></a> '+data.agency);} else {$("#user_agency").html('');}
						if(data.company) {$("#user_company").html('&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Société'); ?> '+data.company+'" class="fa fa-building text-info"></i></a> '+data.company);} else {$("#user_company").html('');}
						if(data.other_ticket) {$("#user_other_ticket").html('&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Autres tickets de cet utilisateur'); ?>" class="fa fa-ticket-alt text-info"></i></a>'+data.other_ticket);} else {$("#user_other_ticket").html('');}
						if(data.asset_id) {$("#user_asset").html('&nbsp;&nbsp;&nbsp;<a target="_blank" href="./index.php?page=asset&id='+data.asset_id+'"><i title="<?php echo T_('Équipement associé'); ?>" class="fa fa-desktop text-info"></i></a> '+data.asset_netbios);} else {$("#user_asset").html('');}
						if(data.ticket_remaining) {$("#user_ticket_remaining").html('&nbsp;&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Tickets restants'); ?>" class="fa fa-tachometer-alt text-info"></i></a> '+data.ticket_remaining);} else {$("#user_ticket_remaining").html('');}
						if(data.hour_remaining) {$("#user_hour_remaining").html('&nbsp;&nbsp;&nbsp;<a href=""><i title="<?php echo T_('Heures restantes'); ?>" class="fa fa-tachometer-alt text-info"></i></a> '+data.hour_remaining+'h');} else {$("#user_hour_remaining").html('');}
						$('#user_warning').css('display', 'none');
					}
				});
			}

		//update subcat list in category switch case
			//detect category switch
			$('#category').change(function(){
				//get value
				var CategorySelected = $(this).val();
				//display debug informations
				<?php if($rparameters['debug']) {echo "console.info('Switch new CategorySelected:'+CategorySelected);";} ?>
				//replace subcat field with new associated values
				$.ajax({
					url:"includes/ticket_subcat_db.php",
					type:"post",
					data: {CategoryId: CategorySelected},
					async:true,
					success: function(result) {
						<?php if($rparameters['debug']) {echo "console.info('JSON received :', result);";} ?>
						var data = JSON.parse(result);
						//reset and populate subcat field
						$("#subcat").empty();
						jQuery.each(data, function(index, value){
							$("#subcat").append("<option value='"+value['id']+"'>"+value['name']+"</option>");
						});
					},
					error: function() {
						console.log('ERROR : unable to get subcat for category '+CategorySelected)
					}
				});
				//remove warning label if value is selected
				if(CategorySelected!=0) {$('#warning_empty_category').css('display', 'none');} else {$('#warning_empty_category').css('display', 'inline');}
			});
            $('.select2').select2();
	});
	//datetimepicker icon default
	$.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
	icons: {
		time: 'fa fa-clock text-info',
		date: 'fa fa-calendar text-info',
		up: 'fa fa-arrow-up',
		down: 'fa fa-arrow-down',
		previous: 'fa fa-chevron-left',
		next: 'fa fa-chevron-right',
		today: 'fa fa-calendar-check-o',
		clear: 'fa fa-trash',
		close: 'fa fa-times'
	} });
</script>
