<?php
################################################################################
# @Name : group.php
# @Description : group management 
# @Call : admin.php
# @Parameters : 
# @Author : Flox
# @Create : 06/07/2013
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['type'])) $_POST['type'] = '';
if(!isset($_POST['service'])) $_POST['service'] = '';
if(!isset($_POST['add'])) $_POST['add'] = '';

//default values
if($_GET['type']=='') $_GET['type']=0;

//display debug informations
if($rparameters['debug']) {
	echo '<u><b>DEBUG MODE:</b></u><br /> <b>VAR:</b> cnt_service='.$cnt_service;
	if($user_services) {echo ' user_services=';foreach($user_services as $value) {echo $value.' ';}}
}

//security check
if(
	$rright['admin'] || 
	($rright['admin_groups'] && $cnt_service!=0) ||
	($rright['admin_groups'] && !$rright['dashboard_service_only'])
)
{
	//submit actions
	if($_POST['Modifier'] && $_GET['id'])
	{
		//secure string
		$_POST['name']=strip_tags($_POST['name']);

		$qry=$db->prepare("UPDATE `tgroups` SET `name`=:name,`type`=:type,`service`=:service WHERE `id`=:id");
		$qry->execute(array('name' => $_POST['name'],'type' => $_POST['type'],'service' => $_POST['service'],'id' => $_GET['id']));
		
		//add user
		if($_POST['user']){
			$qry=$db->prepare("INSERT INTO `tgroups_assoc` (`group`,`user`) VALUES (:group,:user)");
			$qry->execute(array('group' => $_GET['id'],'user' => $_POST['user']));
		}
	}

	if($_POST['add'])
	{
		//secure string
		$_POST['name']=strip_tags($_POST['name']);
		
		$qry=$db->prepare("INSERT INTO `tgroups` (`name`,`type`,`service`) VALUES (:name,:type,:service)");
		$qry->execute(array('name' => $_POST['name'],'type' => $_POST['type'],'service' => $_POST['service']));
		
		//redirect
		$www = "./index.php?page=admin&subpage=group";
		echo '<script language="Javascript">
		// <!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	}

	if($_POST['cancel']){
		//redirect to group list
		$www = "./index.php?page=admin&subpage=group";
		echo '<script language="Javascript">
		// <!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	}

	//delete user in group
	if($_GET['action']=="delete" && $_GET['id'] && $_GET['user'])
	{
		$qry=$db->prepare("DELETE FROM `tgroups_assoc` WHERE `group`=:group AND `user`=:user");
		$qry->execute(array('group' => $_GET['id'], 'user' => $_GET['user']));
		
		//redirect
		$www = "./index.php?page=admin&subpage=group&action=edit&id=$_GET[id]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	}

	//delete group
	if($_GET['action']=="delete" && $_GET['id'] && !$_GET['user'])
	{
		$qry=$db->prepare("UPDATE `tgroups` SET `disable`='1' WHERE `id`=:id");
		$qry->execute(array('id' => $_GET['id']));
		
		//redirect
		$www = "./index.php?page=admin&subpage=group&type=$_GET[type]";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	}
	
	//count group
	$qry=$db->prepare("SELECT COUNT(id) AS counter FROM `tgroups` WHERE disable='0'");
	$qry->execute();
	$row=$qry->fetch();
	$qry->closeCursor();
	
	//display head page
	echo '
	<div class="page-header position-relative">
		<h1 class="page-title text-primary-m2">
			<i class="fa fa-users"></i>  '.T_('Gestion des groupes').'
			<small class="page-info text-secondary-d2">
				<i class="fa fa-angle-double-right"></i>
				&nbsp;'.T_('Nombre').' : '.$row['counter'].'
			</small>
		</h1>
	</div>';

	//edit group
	if($_GET['action']=='edit')
	{
		//get group data
		$qry=$db->prepare("SELECT id,name,type FROM `tgroups` WHERE id=:id");
		$qry->execute(array('id' => $_GET['id']));
		$rgroup=$qry->fetch();
		$qry->closeCursor();
		
		//display edit form
		echo '
		<div class="card bcard shadow" id="card-1">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fa fa-pencil-alt"></i> '.T_("Édition d'un groupe").' :
				</h5>
			</div><!-- /.card-header -->
			<div class="card-body p-0">
				<!-- to have smooth .card toggling, it should have zero padding -->
				<div class="p-3">
					<form id="1" name="form" method="post"  action="">
						<fieldset>
							<label for="name">'.T_('Nom').' :</label>
							<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'; if($rgroup['name']) echo "$rgroup[name]"; echo'" />
						</fieldset>
						<div class="pt-2"></div>
						';
						if($rparameters['user_limit_service'] && ($rright['admin_groups'] || $rright['admin']))
						{
							//find current service associated with group
							$qry=$db->prepare("SELECT service FROM `tgroups` WHERE id=:id AND disable='0'");
							$qry->execute(array('id' => $_GET['id']));
							$row=$qry->fetch();
							$qry->closeCursor();
							if($cnt_service<=1 && $rright['admin']==0) //not show select field, if there are only one service, send data in background
							{
								echo '<input type="hidden" name="service" value="'.$row['service'].'" />'; 
							} elseif($cnt_service>1 || $rright['admin']!=0) { //display select box for service
								echo '
									<fieldset>
										<label for="service">'.T_('Service').' :</label>
										<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
										';
											if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
												//display only service associated with this user
												$qry2=$db->prepare("SELECT tservices.id,tservices.name FROM `tservices`,`tusers_services` WHERE tservices.id=tusers_services.service_id AND tusers_services.user_id=:user_id AND tservices.disable='0' ORDER BY tservices.name");
												$qry2->execute(array('user_id' => $_SESSION['user_id']));
											} else {
												//display all services
												$qry2=$db->prepare("SELECT id,name FROM `tservices` WHERE disable='0' ORDER BY name");
												$qry2->execute(array('user_id' => $_SESSION['user_id']));
											}
											while($row2=$qry2->fetch())
											{
												echo '
												<option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
													'.T_($row2['name']).'
												</option>';
											}
											$qry2->closeCursor();
										echo '
										</select>
										<i title="'.T_("Permet de limiter l'affichage des groupes en fonction d'un service").'" class="fa fa-question-circle text-primary-m2"></i>
									</fieldset>
								';
							}
						}
						echo '
						<div class="radio">
							<label>
								<input value="0" '; if($rgroup['type']=='0')echo "checked"; echo ' name="type" type="radio" class="ace">
								<span class="lbl"> '.T_("Groupe d'utilisateurs").'</span>
							</label>
						</div>
						<div class="radio">
							<label>
								<input value="1" '; if($rgroup['type']=='1')echo "checked"; echo ' name="type" type="radio" class="ace">
								<span class="lbl"> '.T_('Groupe de techniciens').'</span>
							</label>
						</div>
						<fieldset>
							<label for="user">'.T_("Ajout d'un nouveau membre").' :</label>
							<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="user" >
								<option value=""></option>';
								//display technician or user list
								if($rgroup['type']=='1')
								{
									if($rright['ticket_tech_super']) //display supervisor
									{
										$qry=$db->prepare("SELECT id,lastname,firstname FROM `tusers` WHERE disable='0' AND (profile='0' OR profile='4' OR profile='3') ORDER BY lastname");
										$qry->execute();
									} else {
										$qry=$db->prepare("SELECT id,lastname,firstname FROM `tusers` WHERE disable='0' AND (profile='0' OR profile='4') ORDER BY lastname");
										$qry->execute();
									}
								}
								else
								{
									$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE `disable`='0' AND (`profile`!='0' OR `profile`!='4') ORDER BY `lastname`");
									$qry->execute();
								}
								while ($row=$qry->fetch()) 
								{
									echo "<option value=\"$row[id]\">$row[lastname] $row[firstname]</option>";
								} 
								$qry->closeCursor(); 
							echo '
							</select>
						</fieldset>
						<fieldset>
						<label for="name">'.T_('Membres actuels').' :</label><br />';
							//display current users in this group
							$qry=$db->prepare("SELECT tusers.firstname, tusers.lastname, tusers.id FROM `tusers`,tgroups_assoc WHERE tusers.id=tgroups_assoc.user AND tgroups_assoc.group=:group AND tusers.disable='0'");
							$qry->execute(array('group' => $_GET['id']));
							while ($rowuser=$qry->fetch()) 
							{
								echo '<i class="fa fa-caret-right text-primary-m2 pl-2"></i> <a title="'.T_('Fiche Utilisateur').'" href="./index.php?page=admin&subpage=user&action=edit&userid='.$rowuser[2].'" >'.$rowuser[0].' '.$rowuser[1].'</a> 
								<a title="'.T_("Enlever l'utilisateur du groupe").'" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cet utilisateur ?').'\');" href="./index.php?page=admin&amp;subpage=group&amp;id='.$_GET['id'].'&amp;user='.$rowuser[2].'&amp;action=delete"><i class="fa fa-trash text-danger"></i></a><br />';
							}
							$qry->closeCursor(); 
							echo '
						</fieldset>
						<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
							<button name="Modifier" value="Modifier" id="Modifier" type="submit" class="btn btn-success">
								<i class="fa fa-check"></i>
								'.T_('Modifier').'
							</button>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<button name="cancel" value="cancel" type="submit" class="btn btn-danger" >
								<i class="fa fa-reply"></i>
								'.T_('Retour').'
							</button>
						</div>
					</form>
				</div>
			</div><!-- /.card-body -->
		</div>
		';
	//display add form
	} else if($_GET['action']=="add") {
		echo '
		<div class="card bcard shadow" id="card-1">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fa fa-plus-circle"></i> '.T_("Ajout d'un groupe").' :
				</h5>
			</div><!-- /.card-header -->
			<div class="card-body p-0">
				<!-- to have smooth .card toggling, it should have zero padding -->
				<div class="p-3">
					<form id="1" name="form" method="post"  action="">
						<div class="pl-4">
							<fieldset>
								<label for="name">'.T_('Nom').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="name" type="text" value="" />
							</fieldset>
							<div class="pt-2"></div>
							';
							if($rparameters['user_limit_service']==1 && ($rright['admin_groups']!=0 || $rright['admin']!=0))
							{
								//find current service associated with group
								$qry = $db->prepare("SELECT service FROM tgroups WHERE id=:id and disable='0'");
								$qry->execute(array('id' => $_GET['id']));
								$row = $qry->fetch();
								$qry->closeCursor();
								
							if($cnt_service<=1 && $rright['admin']==0) //not show select field, if there are only one service, send data in background
								{
									echo '<input type="hidden" name="service" value="'.$user_services[0].'" />'; 
								} elseif($cnt_service>1 || $rright['admin']!=0) { //display select box for service
									echo '
										<fieldset>
											<label for="service">'.T_('Service').' :</label>
											<select name="service" id="form-field-select-1" >
											';
												if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
													//display only service associated with this user
													$qry2 = $db->prepare("SELECT tservices.id,tservices.name FROM `tservices`,`tusers_services` WHERE tservices.id=tusers_services.service_id AND tusers_services.user_id=:user_id AND tservices.disable='0' ORDER BY tservices.name");
													$qry2->execute(array('user_id' => $_SESSION['user_id']));
												} else {
													//display all services
													$qry2 = $db->prepare("SELECT id,name FROM `tservices` WHERE disable='0' ORDER BY name");
													$qry2->execute();
												}
												while ($row2=$qry2->fetch()) 
												{
													if(empty($row['service'])) {$row['service']='';}
													if(empty($row2['id'])) {$row2['id']='';}
													echo '
													<option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
														'.T_($row2['name']).'
													</option>';
												}
												$qry2->closeCursor();
											echo '
											</select>
											<i title="'.T_("Permet de limiter l'affichage des groupes en fonction d'un service").'" class="fa fa-question-circle text-primary-m2"></i>
										</fieldset>
										
									';
								}
							}
							echo '
							<div class="radio">
								<label>
									<input value="0" name="type" type="radio" class="ace">
									<span class="lbl"> '.T_("Groupe d'utilisateur").'</span>
								</label>
							</div>
							<div class="radio">
								<label>
									<input value="1" name="type" type="radio" class="ace">
									<span class="lbl"> '.T_('Groupe de techniciens').'</span>
								</label>
							</div>
						</div>
						<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
							<button name="add" value="add" id="add" type="submit"  class="btn btn-success">
								<i class="fa fa-check"></i>
								'.T_('Ajouter').'
							</button>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<button name="cancel" value="cancel" type="submit" class="btn btn-danger" >
								<i class="fa fa-reply"></i>
								'.T_('Retour').'
							</button>
						</div>
					</form>
				</div>
			</div><!-- /.card-body -->
		</div>
		';
	} else {
		//display group list
		echo'
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<p>
				<button onclick=\'window.location.href="index.php?page=admin&subpage=group&action=add";\' class="btn btn-success">
					<i class="fa fa-plus mr-1"></i>'.T_('Ajouter un groupe').'
				</button>
			</p>
		</div>
		<br />';
		//display user table
		echo'
		<div class="">
			<div class="col-12 tabs-above">
				<ul class="nav nav-tabs nav-justified shadow" role="tablist">
					<li class="nav-item mr-1px">
						<a class="nav-link text-left radius-0 '; if($_GET['type']==0) {echo 'active';} echo'" href="./index.php?page=admin&subpage=group&type=0">
							<i class="fa fa-users text-success"></i>
							'.T_("Groupe d'utilisateurs").'
						</a>
					</li>
				<li class="nav-item mr-1px">
						<a class="nav-link text-left radius-0 '; if($_GET['type']==1) {echo 'active';} echo'" href="./index.php?page=admin&subpage=group&type=1">
							<i class="fa fa-users text-warning"></i>
							'.T_('Groupe de techniciens').'
						</a>
					</li>
				</ul>
				<div class="tab-content shadow" style="background-color:#FFF;" >
					<table id="sample-table-1" class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th>'.T_('Nom').'</th>
								<th>'.T_('Membres').'</th>
								';
								if($rparameters['user_limit_service']==1) {echo '<th>'.T_('Service').'</th>';}
								echo '
								<th>'.T_('Actions').'</th>
							</tr>
						</thead>
						<tbody>';
							//secure string
							$_POST['type']=strip_tags($_POST['type']);
							
							//build each line
							$qry = $db->prepare("SELECT id,name FROM `tgroups` WHERE type=:type AND disable='0' ORDER BY type,name ");
							$qry->execute(array('type' => $_GET['type']));
							while ($rgroup=$qry->fetch()) 
							{
								echo "
								<tr>
									<td onclick=\"document.location='./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id=$rgroup[id]'\">
										$rgroup[name]
									</td>
									<td onclick=\"document.location='./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id=$rgroup[id]'\">";
										
										$qry2 = $db->prepare("SELECT tusers.firstname, tusers.lastname FROM `tusers`,tgroups_assoc WHERE tusers.id=tgroups_assoc.user AND tgroups_assoc.group=:group AND tusers.disable='0'");
										$qry2->execute(array('group' => $rgroup['id']));
										while ($rowuser=$qry2->fetch()) 
										{
											echo $rowuser['firstname'].' '.$rowuser['lastname'].'<br />';
										}
										$qry2->closeCursor(); 
										echo '
									</td>
									';
									//display associated service if parameter is enable
									if($rparameters['user_limit_service']==1) { 
										//find value
										$qry2 = $db->prepare("SELECT tservices.name FROM `tservices` WHERE id=(SELECT service FROM tgroups WHERE id=:id AND disable='0')");
										$qry2->execute(array('id' => $rgroup['id']));
										$row = $qry2->fetch();
										$qry2->closeCursor(); 
										echo '<td>'.T_($row['name']).'</td>';
									}
									echo '
									<td>
										<a class="btn btn-sm btn-warning" href="./index.php?page=admin&amp;subpage=group&amp;action=edit&amp;id='.$rgroup['id'].'"  title="'.T_('Modifier ce groupe').'" ><center><i style="color:#FFF;" class="fa fa-pencil-alt "></i></center></a>
										<a class="btn btn-sm btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer ce groupe ?').'\');" href="./index.php?page=admin&amp;subpage=group&amp;id='.$rgroup['id'].'&amp;type='.$_GET['type'].'&amp;action=delete"  title="'.T_('Supprimer ce groupe').'" ><center><i class="fa fa-trash"></i></center></a>
									</td>
								</tr>';
							}
							echo '
						</tbody>
					</table>
				</div>
			</div>
		</div>
		';
	}
} else {
	echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Erreur').':</strong> '.T_("Vous n'avez pas le droit d'accès à ce menu, ou vous ne disposer d'aucun service associé, contacter votre administrateur").'.<br></div>';
}
?>