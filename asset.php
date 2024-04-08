<?php
################################################################################
# @Name : asset.php 
# @Description : page to display create and edit asset
# @Call : /dashboard.php
# @Parameters : 
# @Author : Flox
# @Create : 27/11/2015
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($_POST['sn_internal'])) $_POST['sn_internal']= ''; 
if(!isset($_POST['sn_manufacturer'])) $_POST['sn_manufacturer']= ''; 
if(!isset($_POST['type'])) $_POST['type']= ''; 
if(!isset($_POST['manufacturer'])) $_POST['manufacturer']= ''; 
if(!isset($_POST['model'])) $_POST['model']= '';
if(!isset($_POST['department'])) $_POST['department']= ''; 
if(!isset($_POST['virtualization'])) $_POST['virtualization']= ''; 
if(!isset($_POST['user'])) $_POST['user']= ''; 
if(!isset($_POST['sn_indent'])) $_POST['sn_indent']= ''; 
if(!isset($_POST['state'])) $_POST['state']= ''; 
if(!isset($_POST['netbios'])) $_POST['netbios']= ''; 
if(!isset($_POST['description'])) $_POST['description']= ''; 
if(!isset($_POST['location'])) $_POST['location']= ''; 
if(!isset($_POST['socket'])) $_POST['socket']= ''; 
if(!isset($_POST['technician'])) $_POST['technician']= ''; 
if(!isset($_POST['maintenance'])) $_POST['maintenance']= '';  
if(!isset($_POST['date_stock'])) $_POST['date_stock']= '';  
if(!isset($_POST['date_install'])) $_POST['date_install']= '';  
if(!isset($_POST['date_stock'])) $_POST['date_stock']= '';  
if(!isset($_POST['date_standbye'])) $_POST['date_standbye']= '';  
if(!isset($_POST['date_recycle'])) $_POST['date_recycle']= '';  
if(!isset($_POST['date_end_warranty'])) $_POST['date_end_warranty']= ''; 
if(!isset($_POST['cursor'])) $_POST['cursor']= '';  

//core asset actions
include('./core/asset.php');

//default values for new asset
if(!isset($globalrow['id'])) $globalrow['id']= ''; 
if(!isset($globalrow['sn_internal'])) $globalrow['sn_internal']= ''; 
if(!isset($globalrow['sn_manufacturer'])) $globalrow['sn_manufacturer']= ''; 
if(!isset($globalrow['type'])) $globalrow['type']= ''; 
if(!isset($globalrow['manufacturer'])) $globalrow['manufacturer']= ''; 
if(!isset($globalrow['model'])) $globalrow['model']= ''; 
if(!isset($globalrow['department'])) $globalrow['department']= ''; 
if(!isset($globalrow['virtualization'])) $globalrow['virtualization']= ''; 
if(!isset($globalrow['net_scan'])) $globalrow['net_scan']='1'; 
if(!isset($globalrow['user'])) $globalrow['user']= ''; 
if(!isset($globalrow['sn_indent'])) $globalrow['sn_indent']= ''; 
if(!isset($globalrow['state'])) $globalrow['state']= '2'; 
if(!isset($globalrow['netbios'])) $globalrow['netbios']= ''; 
if(!isset($globalrow['description'])) $globalrow['description']= ''; 
if(!isset($globalrow['location'])) $globalrow['location']= ''; 
if(!isset($globalrow['socket'])) $globalrow['socket']= ''; 
if(!isset($globalrow['technician'])) $globalrow['technician']= $_SESSION['user_id']; 
if(!isset($globalrow['maintenance'])) $globalrow['maintenance']= ''; 
if(!isset($globalrow['u_group'])) $globalrow['u_group']= ''; 
if(!isset($globalrow['date_stock'])) $globalrow['date_stock']= '';  
if(!isset($globalrow['date_standbye'])) $globalrow['date_standbye']= '';  
if(!isset($globalrow['date_install'])) $globalrow['date_install']= date('Y-m-d');  
if(!isset($globalrow['date_recycle'])) $globalrow['date_recycle']= '';  
if(!isset($globalrow['date_end_warranty'])) $globalrow['date_end_warranty']= '';  

$ip_asset='0';
$wifi_asset='0';
$ping_ip='';
$wol_mac='';
$debug_error='';

if(!isset($globalrow['cursor'])) $globalrow['cursor']= '';

//avoid problem new asset check iface
if($_GET['action']=='new' && !isset($globalrow['id'])) {$globalrow['id']= '';}

//get iface to check if asset have ip
if($_GET['action']!='new' && $_GET['id'])
{
	$qry=$db->prepare("SELECT `ip` FROM `tassets_iface` WHERE asset_id=:asset_id AND disable='0'");
	$qry->execute(array('asset_id' => $_GET['id']));
	$iface=$qry->fetch();
	$qry->closeCursor();	
}else{$iface='';}

//test if asset is IP to display specific inputs
if($_GET['id'] && ($_GET['action']!='new'))
{
	$qry=$db->prepare("SELECT `tassets_model`.`ip` FROM `tassets_model`,`tassets` WHERE tassets.model=tassets_model.id AND tassets.id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$ripmodel=$qry->fetch();
	$qry->closeCursor();
	if(empty($ripmodel['ip'])) {$ripmodel['ip']='';}
	if($ripmodel['ip']=='1') {$ip_asset='1';} else {$ip_asset='0';}
} elseif($_POST['model']!='') {
	$qry=$db->prepare("SELECT `tassets_model`.`ip` FROM `tassets_model` WHERE id=:id");
	$qry->execute(array('id' => $_POST['model']));
	$ripmodel=$qry->fetch();
	$qry->closeCursor();
	if($ripmodel['ip']=='1') {$ip_asset='1';} else {$ip_asset='0';}
}

//test if wifi asset to display specific row
if($_GET['id'] && ($_GET['action']!='new'))
{
	$qry=$db->prepare("SELECT `tassets_model`.`wifi` FROM `tassets_model`,`tassets` WHERE tassets.model=tassets_model.id AND tassets.id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$ripmodel=$qry->fetch();
	$qry->closeCursor();
	if(empty($ripmodel['wifi'])) {$ripmodel['wifi']='';}
	if($ripmodel['wifi']=='1') {$wifi_asset='1';} else {$wifi_asset='0';}
} elseif($_POST['model']!='') {
	$qry=$db->prepare("SELECT `tassets_model`.`wifi` FROM `tassets_model` WHERE id=:id");
	$qry->execute(array('id' => $_POST['model']));
	$ripmodel=$qry->fetch();
	$qry->closeCursor();
	if($ripmodel['wifi']=='1') {$wifi_asset='1';} else {$wifi_asset='0';}
} 
if($rparameters['debug']) {echo "VAR: IP_ASSET=$ip_asset | WIFI_ASSET=$wifi_asset";}

//convert YYYY-mm-dd date to FR format
if($globalrow['date_stock']=='0000-00-00' || $globalrow['date_stock']=='') {
	$globalrow['date_stock']='';
} else {
	$globalrow['date_stock'] = DateTime::createFromFormat('Y-m-d', $globalrow['date_stock']);
	$globalrow['date_stock']=$globalrow['date_stock']->format('d/m/Y');
}
if($globalrow['date_install']=='0000-00-00' || $globalrow['date_install']=='') {
	$globalrow['date_install']='';
} else {
	$globalrow['date_install'] = DateTime::createFromFormat('Y-m-d', $globalrow['date_install']);
	$globalrow['date_install']=$globalrow['date_install']->format('d/m/Y');
}
if($globalrow['date_end_warranty']=='0000-00-00' || $globalrow['date_end_warranty']=='') {
	$globalrow['date_end_warranty']='';
} else {
	$globalrow['date_end_warranty'] = DateTime::createFromFormat('Y-m-d', $globalrow['date_end_warranty']);
	$globalrow['date_end_warranty']=$globalrow['date_end_warranty']->format('d/m/Y');
}
if($globalrow['date_standbye']=='0000-00-00' || $globalrow['date_standbye']=='') {
	$globalrow['date_standbye']='';
} else {
	$globalrow['date_standbye'] = DateTime::createFromFormat('Y-m-d', $globalrow['date_standbye']);
	$globalrow['date_standbye']=$globalrow['date_standbye']->format('d/m/Y');
}
if($globalrow['date_recycle']=='0000-00-00' || $globalrow['date_recycle']=='') {
	$globalrow['date_recycle']='';
} else {
	$globalrow['date_recycle'] = DateTime::createFromFormat('Y-m-d', $globalrow['date_recycle']);
	$globalrow['date_recycle']=$globalrow['date_recycle']->format('d/m/Y');
}
?>
<div id="">
	<div class="">
		<div class="card bcard shadow" id="card-1" draggable="false">
			<form class="form-horizontal" name="myform" id="myform" enctype="multipart/form-data" method="post" action="" onsubmit="" >
				<div class="card-header">
					<h5 class="card-title">
						<i class="fa fa-desktop"></i>
						<?php
    						//display card title
    						if($_GET['action']=='new') {
								echo T_("Ajout d'un équipement"); 
							} else { 
								//get internal id of this asset
								$qry=$db->prepare("SELECT `sn_internal`,`netbios` FROM `tassets` WHERE id=:id");
								$qry->execute(array('id' => $_GET['id']));
								$rsn=$qry->fetch();
								$qry->closeCursor();
								if($mobile==0){echo T_("Édition de l'équipement").' n°'.$rsn['sn_internal'].' : '.$rsn['netbios'].'</i>';} 
								else {echo 'n°'.$rsn['sn_internal'].': '.$rsn['netbios'].'</i>';}
							}
						?>
					</h5>
					<span class="card-toolbar">
						<?php 
							//display specific buttons for IP asset
							if(($ip_asset || $wifi_asset || $iface) && $rparameters['asset_ip'])
							{
								if($rparameters['asset_vnc_link'] && isset($iface['ip'])){echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-purple ml-2" title="'.T_('Ouvre un nouvel onglet sur le prise de contrôle distant web VNC').'" target="_blank" href="http://'.$iface['ip'].':5800"><i class="fa fa-desktop text-130"></i></a>&nbsp;&nbsp;';}
								if($rright['asset_net_scan']){
									if($globalrow['net_scan'])
									{echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-success" href="./index.php?page=asset&id='.$_GET['id'].'&scan=0&'.$url_get_parameters.'"><i title="'.T_('Scan IP activé sur cet équipement, cliquer pour désactiver').'" class="fa fa-wifi text-120" /></i></a>&nbsp;&nbsp;';
									} else {echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-danger" href="./index.php?page=asset&id='.$_GET['id'].'&scan=1&'.$url_get_parameters.'"><i title="'.T_('Scan IP désactivé sur cet équipement, cliquer pour activer').'" class="fa fa-wifi text-120" /></i></a>&nbsp;&nbsp;';}
								}
								echo '<button type="button" style="width:31px; height:29px;" class="btn btn-xs btn-success" data-toggle="modal" data-target="#addiface"><i title="'.T_('Ajouter une interface IP').'" class="fa fa-plug text-130" /></i></button>&nbsp;&nbsp;';
								//select IP display ping button
								$qry=$db->prepare("SELECT `role_id`,`ip`,`mac` FROM tassets_iface WHERE asset_id=:id AND disable='0'");
								$qry->execute(array('id' => $globalrow['id']));
								while($row=$qry->fetch()) 
								{
									if($row['role_id']==1 && $row['ip']!='') {$ping_ip=$row['ip'];}
									if($ping_ip=='') {$ping_ip=$row['ip'];}
									if($row['role_id']==1 && $row['mac']!='') {$wol_mac=$row['mac'];}
									if($wol_mac=='') {$wol_mac=$row['mac'];}
								}
								$qry->closeCursor();
								
								if($ping_ip) {echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-info" href="./index.php?page=asset&id='.$globalrow['id'].'&action=ping&iptoping='.$ping_ip.'&'.$url_get_parameters.'"><i title="'.T_("Ping de cet équipement sur l'adresse IP:").' '.$ping_ip.' " class="fa fa-exchange-alt text-130"></i></a>&nbsp;&nbsp;';}
								if($wol_mac) {echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-warning" href="./index.php?page=asset&id='.$globalrow['id'].'&action=wol&mac='.$wol_mac.'&'.$url_get_parameters.'"><i title="'.T_("Allumer cet équipement avec l'adresse MAC:").' '.$wol_mac.' " style="color:#FFF;" class="fa fa-power-off text-130"></i></a>&nbsp;&nbsp;';}
							}
							if($rright['asset_delete']!=0) {
								if($_GET['action']!='new') {echo '<a style="width:31px; height:29px; padding:5px 0px 0px 0px;" class="btn btn-xs btn-danger" title="'.T_('Supprimer').'" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cet équipement ?').'\');" href="./index.php?page=asset&id='.$_GET['id'].'&action=delete&'.$url_get_parameters.'"><i class="fa fa-trash text-130"></i></a>&nbsp;&nbsp;';}
								echo '<button class="btn btn-xs btn-success" title="'.T_('Sauvegarder').'" name="modify" value="modify" type="submit" id="modify2"><i class="fa fa-save text-130"></i></button>&nbsp;&nbsp;';
								echo '<button class="btn btn-xs btn-purple" title="'.T_('Sauvegarder et quitter').'" name="quit" value="quit" type="submit" id="quit"><i class="fa fa-save text-130"></i></button>';
							}
						?>
					</span>
				</div>
				<div class="card-body p-0">
					<div class="p-3">
						<div class="row">
							<div class="col-lg-9">
								<!-- START sn_internal part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="sn_internal"><?php echo T_('Numéro'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<input class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" name="sn_internal" id="sn_internal" type="text" size="25"  value="<?php if($_POST['sn_internal']) echo $_POST['sn_internal']; else echo $globalrow['sn_internal']; ?>"  />
									</div>
								</div>
								<!-- END sn_internal part -->
								<!-- START type model part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="type">
											<?php if(($globalrow['type']==0) && ($_POST['type']==0)) echo '<i title="Aucun type sélectionné." class="fa fa-exclamation-triangle text-danger"></i>&nbsp;'; ?>
											<?php echo T_('Type'); ?> :
										</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control col-3 d-inline-block" id="type" name="type" onchange="submit();" >
											<?php
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY name");
												$qry->execute();
												while($row=$qry->fetch()) 
												{
													if($_POST['type']){
														if($_POST['type']==$row['id']) {echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
													}
													else
													{
														if($globalrow['type']==$row['id']) {echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
													}
												}
												$qry->closeCursor();
												if($globalrow['type']==0 && $_POST['type']==0) {echo '<option value="" selected></option>';}
											?>
										</select>
										<select class="form-control col-3 d-inline-block" id="manufacturer" name="manufacturer" onchange="submit();" >
											<?php
												if($globalrow['manufacturer']==0 && $_POST['manufacturer']==0) {echo '<option value="" selected></option>';} else {echo '<option value=""></option>';}
												if($_POST['type'])
												{
													$qry=$db->prepare("SELECT DISTINCT tassets_manufacturer.id, tassets_manufacturer.name FROM `tassets_manufacturer`,tassets_model WHERE tassets_manufacturer.id=tassets_model.manufacturer AND tassets_model.type=:type ORDER BY name ASC");
													$qry->execute(array('type' => $_POST['type']));
												}
												elseif($globalrow['type']!='')
												{
													$qry=$db->prepare("SELECT DISTINCT tassets_manufacturer.id, tassets_manufacturer.name FROM `tassets_manufacturer`,tassets_model WHERE tassets_manufacturer.id=tassets_model.manufacturer AND tassets_model.type=:type ORDER BY name ASC");
													$qry->execute(array('type' => $globalrow['type']));
												}
												else
												{
													$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_manufacturer` ORDER BY name ASC");
													$qry->execute();
												}
												while($row=$qry->fetch()) 
												{
													if($_POST['type'])
													{
														if($_POST['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
													else
													{
														if($globalrow['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
												}
												$qry->closeCursor();
											?>
										</select>
										<select class="form-control col-3 d-inline-block" id="model" name="model" onchange="submit();" >
										<?php
											if($_POST['manufacturer'])
											{
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` WHERE manufacturer LIKE :manufacturer AND type=:type ORDER BY name ASC");
												$qry->execute(array('manufacturer' => $_POST['manufacturer'], 'type' => $_POST['type']));
											}
											elseif($globalrow['manufacturer']!='')
											{
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` WHERE manufacturer LIKE :manufacturer AND type=:type ORDER BY name ASC");
												$qry->execute(array('manufacturer' => $globalrow['manufacturer'], 'type' => $globalrow['type']));
											}
											else
											{
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` ORDER BY name ASC");
												$qry->execute();
											}
											while($row=$qry->fetch()) 
											{
												if($_POST['model'])
												{
													if($_POST['model']==$row['id']) {echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
												else
												{
													if($globalrow['model']==$row['id']) {echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											}
											$qry->closeCursor();
											if($globalrow['model']==0 && $_POST['model']==0) {echo '<option value="" selected></option>';}
										?>
										</select>
									</div>
								</div>
								<!-- END type model part -->
								
								<!-- START virtualization part -->
								<?php
								if($rright['asset_virtualization_disp']!=0)
								{
									//check if type is virtual
									$qry=$db->prepare("SELECT `virtualization` FROM `tassets_type` WHERE id=:id");
									$qry->execute(array('id' => $globalrow['type']));
									$row=$qry->fetch();
									$qry->closeCursor();
									if($row['virtualization']==1)
									{
										echo '
										<div class="form-group row">
											<div class="col-sm-2 col-form-label text-sm-right pr-0">
												<label class="mb-0" for="virtualization">'.T_('Équipement virtuel').'</label>
											</div>
											<div class="col-sm-9">
												<label>
													<input class="form-control col-3" name="virtualization" id="virtualization" type="checkbox" ';if($globalrow['virtualization']==1) {echo "checked";} echo ' class="ace" value="1">
													<span class="lbl"></span>
												</label>
											</div>
										</div>
										';
									}
								}
								?>
								<!-- END virtualization part -->
								
								<!-- START netbios part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="netbios"><?php echo T_('Nom équipement'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<input class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" name="netbios" id="netbios" type="text" size="25"  value="<?php if($_POST['netbios']) echo $_POST['netbios']; else echo $globalrow['netbios']; ?>"  />
									</div>
								</div>
								<!-- END netbios part -->
								
								<!-- START user part -->	
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="user"><?php echo T_('Utilisateur'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" <?php if($mobile==0) {echo 'class="chosen-select"';}?> id="user" name="user">
											<?php
											//limit select list to users who have the same company than current connected user
											if($rright['asset_list_company_only'])
											{
												//display user list
												$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE disable='0' AND company=:company ORDER BY lastname ASC, firstname ASC");
												$qry->execute(array('company' => $ruser['company']));
												while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';}
												$qry->closeCursor();
												
											} else {
												//display user list
												$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE disable='0' ORDER BY lastname ASC, firstname ASC");
												$qry->execute();
												while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';}
												$qry->closeCursor();
											}
											//selection
											if($_POST['user']){$user=$_POST['user'];}elseif($globalrow['user']!=''){$user=$globalrow['user'];} else {$user=0;}
											$qry=$db->prepare("SELECT `lastname`,`firstname` FROM `tusers` WHERE id=:id");
											$qry->execute(array('id' => $user));
											$row=$qry->fetch();
											$qry->closeCursor();
											if($user==0) {echo '<option selected value="0">'.T_('Aucun').'</option>';} else  {echo '<option selected value="'.$user.'">'.$row['lastname'].' '.$row['firstname'].'</option>';}
											?>
										</select>
									</div>
								</div>
								<!-- END user part -->
								
								<!-- START department part -->	
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="department">
											<?php echo T_('Service'); ?> :
										</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" id="department" name="department" >
											<?php
											echo '<option value="0">Aucun</option>';
											//display service list
											$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE disable='0' ORDER BY name ASC");
											$qry->execute();
											while($row=$qry->fetch()) 
											{
												if($row['id']==0) {$row['name']=T_($row['name']);} //translate none value from db
												if($_POST['department']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
												elseif($globalrow['department']==$row['id']) {
													echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';
												} else {
													echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
												}
											}
											$qry->closeCursor();
											?>
										</select>
									</div>
								</div>
								<!-- END service part -->
								
								<!-- START iface part -->
								<?php
									if($rparameters['asset_ip'])
									{
										//check if asset iface exist and display it
										$qry=$db->prepare("SELECT COUNT(id) FROM `tassets_iface` WHERE asset_id=:asset_id AND disable='0'");
										$qry->execute(array('asset_id' => $globalrow['id']));
										$iface_counter=$qry->fetch();
										$qry->closeCursor();
										if($iface_counter[0]>0)
										{
											if($rparameters['debug']) {$debug_error=$iface_counter[0].' IFACE DETECTED: Display each iface';}
											//display each iface
											$qry=$db->prepare("SELECT id,role_id,date_ping_ok,date_ping_ko,netbios,mac,ip FROM `tassets_iface` WHERE asset_id=:asset_id AND disable='0' ORDER BY role_id ASC");
											$qry->execute(array('asset_id' => $globalrow['id']));
											while($row=$qry->fetch()) 
											{
												//init var
												if(!isset($_POST["netbios_$row[id]"])) $_POST["netbios_$row[id]"] = '';
												if(!isset($_POST["ip_$row[id]"])) $_POST["ip_$row[id]"] = '';
												if(!isset($_POST["mac_$row[id]"])) $_POST["mac_$row[id]"] = '';
												//get name of role of current iface
												$qry2=$db->prepare("SELECT `name` FROM `tassets_iface_role` WHERE id=:id");
												$qry2->execute(array('id' => $row['role_id']));
												$row2=$qry2->fetch();
												$qry2->closeCursor();
												$iface_name=$row2[0];
												//display if bloc
												echo '
												<div class="form-group row">
													<div class="col-sm-2 col-form-label text-sm-right pr-0">
														<label class="mb-0" for="iface">
															';
															//display ping flags
															if($row['date_ping_ok']>$row['date_ping_ko'])
															{
																echo '<i title="'.T_('Dernier ping réussi le').' '.date("d/m/Y H:i:s", strtotime($row['date_ping_ok'])).'" class="fa fa-flag text-success"></i>';
															} elseif($row['date_ping_ko']>$row['date_ping_ok']) 
															{
																echo '<i title="'.T_('Dernier ping échoué le').' '.date("d/m/Y H:i:s", strtotime($row['date_ping_ko'])).'" class="fa fa-flag text-danger"></i>';
															}
															echo '
															'.T_('Interface IP').' '.$iface_name.' :
														</label>
													</div>
													<div class="col-sm-9">
														<input class="form-control col-3 d-inline-block" name="netbios_'.$row['id'].'" id="netbios_'.$row['id'].'" type="text" placeholder="Nom NetBIOS" size="12" value="';if($_POST["netbios_$row[id]"]) {echo $_POST["netbios_$row[id]"];} else { echo $row['netbios'];} echo'" />
														<input class="form-control col-3 d-inline-block" name="ip_'.$row['id'].'" id="ip_'.$row['id'].'" type="text" size="14" placeholder="Adresse IP" value="';if($_GET['findip'] && $_GET['iface']==$row['id']) {echo $_GET['findip'];} elseif($_POST["ip_$row[id]"]) {echo $_POST["ip_$row[id]"];} else { echo $row['ip'];} echo'" />
														<input class="form-control col-3 d-inline-block" title="'.T_('Noter sans séparateurs : ou - ').'" name="mac_'.$row['id'].'" id="mac_'.$row['id'].'" type="text" size="14" placeholder="Adresse MAC" value="';if($_POST["mac_$row[id]"]) {echo $_POST["mac_$row[id]"];} else { echo $row['mac'];} echo'" />
														<i class="fa fa-search text-success text-110" title="'.T_('Trouver une adresse IP pour cette interface').'" onclick="document.forms[\'myform\'].action.value=\'findip_'.$row['id'].'\';document.forms[\'myform\'].submit();"></i>
														&nbsp;<a href="./index.php?page=asset&id='.$globalrow['id'].'&state='.$_GET['state'].'&action=editiface&iface='.$row['id'].'&'.$url_get_parameters.'"><i class="fa fa-pencil-alt text-warning text-110" title="'.T_("Modifier le rôle de l'interface").'" onclick="document.forms[\'myform\'].action.value=\'editiface\';document.forms[\'myform\'].submit();"></i></a>
														&nbsp;<a href="./index.php?page=asset&id='.$globalrow['id'].'&state='.$_GET['state'].'&action=delete_iface&iface='.$row['id'].'&'.$url_get_parameters.'"><i class="fa fa-trash text-danger text-110"  title="'.T_("Supprimer l'interface").'"></i></a>
													</div>
												</div>
												';
											}
											$qry->closeCursor();
										}

										//display default iface fields when asset not have iface and when it's ip asset
										$qry=$db->prepare("SELECT COUNT(id) FROM `tassets_iface` WHERE asset_id=:asset_id AND disable='0'");
										$qry->execute(array('asset_id' => $globalrow['id']));
										$iface_lan_counter=$qry->fetch();
										$qry->closeCursor();
										
										$qry=$db->prepare("SELECT COUNT(id) FROM `tassets_iface` WHERE asset_id=:asset_id AND role_id='2' AND disable='0'");
										$qry->execute(array('asset_id' => $globalrow['id']));
										$iface_wifi_counter=$qry->fetch();
										$qry->closeCursor();
										
										if($ip_asset=='1' && $iface_lan_counter[0]==0)
										{
											if($rparameters['debug']) {$debug_error='NO LAN IFACE DETECTED: display default LAN input';}
											echo '
											<div class="form-group row">
												<div class="col-sm-2 col-form-label text-sm-right pr-0">
													<label class="mb-0" for="ip">'.T_('Interface IP LAN').' :</label>
												</div>
												<div class="col-sm-9">
													<input class="form-control col-3 d-inline-block" name="netbios_lan_new" id="netbios_lan_new" type="text" placeholder="Nom NetBIOS" size="12" value="'.$_POST['netbios_lan_new'].'" />
													<input class="form-control col-3 d-inline-block" name="ip_lan_new" id="ip_lan_new" type="text" size="14" placeholder="Adresse IP" value="'; if($_GET['findip'] && $_GET['iface']=='ip_lan_new') {echo $_GET['findip'];} else {echo $_POST['ip_lan_new'];} echo'" />
													<input class="form-control col-3 d-inline-block" title="'.T_('Noter sans les séparateurs : ou - ').'" name="mac_lan_new" id="mac_lan_new" type="text" size="14" placeholder="Adresse MAC" value="'.$_POST['mac_lan_new'].'" />
													&nbsp;<i class="fa fa-search text-success" title="'.T_('Trouver une adresse IP pour cette interface').'" onclick="document.forms[\'myform\'].action.value=\'findip1\';document.forms[\'myform\'].submit();"></i>
												</div>
											</div>
											';
										}
										if($wifi_asset=='1' && $iface_wifi_counter[0]==0)
										{
											if($rparameters['debug']) {$debug_error='NO WIFI IFACE DETECTED: display default WIFI input';}
											echo '
											<div class="form-group row">
												<div class="col-sm-2 col-form-label text-sm-right pr-0">
													<label class="mb-0" for="wifi">'.T_('Interface IP WIFI').' :</label>
												</div>
												<div class="col-sm-9">
													<input class="form-control col-3 d-inline-block" name="netbios_wifi_new" id="netbios_wifi_new" type="text" placeholder="Nom NetBIOS" size="12" value="'.$_POST['netbios_wifi_new'].'" />
													<input class="form-control col-3 d-inline-block" name="ip_wifi_new" id="ip_wifi_new" type="text" size="14" placeholder="Adresse IP" value="';if($_GET['findip'] && $_GET['iface']=='ip_wifi_new') {echo $_GET['findip'];} else {echo $_POST['ip_wifi_new'];} echo'" />
													<input class="form-control col-3 d-inline-block" title="'.T_('Noter sans séparateurs : ou - ').'" name="mac_wifi_new" id="mac_wifi_new" type="text" size="14" placeholder="Adresse MAC" value="'.$_POST['mac_wifi_new'].'" />
													&nbsp;<i class="fa fa-search text-success" title="'.T_('Trouver une adresse IP pour cette interface').'" onclick="document.forms[\'myform\'].action.value=\'findip2\';document.forms[\'myform\'].submit();"></i>
												</div>
											</div>
											';
										}
										//need to use onclick action of findip
										echo'<input type="hidden" name="action" value="">'; 
									}
								?>
								<!-- END iface part -->
								
								<!-- START sn_manufacturer part -->
								<?php
								if($globalrow['virtualization']==0)
								{
									echo '
									<div class="form-group row">
										<div class="col-sm-2 col-form-label text-sm-right pr-0">
											<label class="mb-0" for="sn_manufacturer">'.T_('Numéro série fabricant').' :</label>
										</div>
										<div class="col-sm-9">
											<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" name="sn_manufacturer" id="sn_manufacturer" type="text" value="'; if($_POST['sn_manufacturer']) echo $_POST['sn_manufacturer']; else echo $globalrow['sn_manufacturer']; echo '"  />
										</div>
									</div>
									';
								}
								?>
								<!-- END sn_manufacturer part -->
								
								<!-- START sn_indent part -->
								<?php 
								if($globalrow['virtualization']==0)
								{
									echo '
									<div class="form-group row">
										<div class="col-sm-2 col-form-label text-sm-right pr-0">
											<label class="mb-0" for="sn_indent">'.T_('Numéro de commande').' :</label>
										</div>
										<div class="col-sm-9">
											<input class="form-control  '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" name="sn_indent" id="sn_indent" type="text" value="'; if($_POST['sn_indent']) {echo $_POST['sn_indent'];} else {echo $globalrow['sn_indent'];} echo '"  />
										</div>
									</div>
									';
								}
								?>
								<!-- END sn_indent part -->
								
								<!-- START description part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="description"><?php echo T_('Description'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<textarea class="form-control <?php if($mobile){echo 'col-12';} else {echo 'col-6';}?>" name="description" id="description" rows="4"><?php if($_POST['description']) echo $_POST['description']; else echo $globalrow['description']; ?></textarea>
									</div>
								</div>
								<!-- END description part -->
								
								<!-- START location part -->
								<?php
								if($rright['asset_location_disp']!=0 && $globalrow['virtualization']==0)
								{
									echo '
									<div class="form-group row">
										<div class="col-sm-2 col-form-label text-sm-right pr-0">
											<label class="mb-0" for="location">
												'.T_('Localisation').' :
											</label>
										</div>
										<div class="col-sm-9">
											<select class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo ' chosen-select" id="location" name="location">
												';
												//display location list
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_location` WHERE disable='0' ORDER BY id!=0,name ASC");
												$qry->execute();
												while($row=$qry->fetch()) 
												{
													if($globalrow['location']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} 
													else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
												$qry->closeCursor();
												echo '
											</select>
										</div>
									</div>
									';
								}
								?>
								<!-- END location part -->
								
								<!-- START socket part -->
								<?php
								if($ip_asset=='1' && $globalrow['virtualization']==0 && $rparameters['asset_ip'])
									{
										echo '
										<div class="form-group row">
											<div class="col-sm-2 col-form-label text-sm-right pr-0">
												<label class="mb-0" for="socket">'.T_('Numéro de prise').' :</label>
											</div>
											<div class="col-sm-9">
												<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" name="socket" id="socket" type="text" size="25"  value="'; if($_POST['socket']) echo $_POST['socket']; else echo $globalrow['socket']; echo '"  />
											</div>
										</div>
										';
									}
								?>
								<!-- END socket part -->
								
								<!-- START technician part -->	
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="technician">
											<?php if(($_POST['technician']==0) && ($globalrow['technician']==0)) echo '<i title="'.T_('Sélectionner un technicien').'." class="fa fa-exclamation-triangle text-danger"></i>&nbsp;'; ?>
											<?php echo T_('Installateur'); ?> :
										</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" id="technician" name="technician">
											<?php
											//display technician list
											$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE (profile='0' || profile='4') AND disable='0' ORDER BY lastname ASC, firstname ASC");
											$qry->execute();
											while($row=$qry->fetch()) 
											{
												echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';
											}
											$qry->closeCursor();
											
											//selection
											if($_POST['technician']){$user=$_POST['technician'];}elseif($globalrow['technician']!=''){$user=$globalrow['technician'];} else {$user=0;}
											$qry=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE (profile='0' || profile='4') AND id LIKE :id");
											$qry->execute(array('id' => $user));
											$row=$qry->fetch();
											$qry->closeCursor();
											
											echo '<option selected value="'.$user.'">'.$row['lastname'].' '.$row['firstname'].'</option>';
											if($user==0) {echo '<option selected value="0">'.T_('Aucun').'</option>';}
											?>
										</select>
									</div>
								</div>
								<!-- END technician part -->
								
								<!-- START maintenance part -->	
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="maintenance">
											<?php echo T_('Maintenance'); ?> :
										</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" id="maintenance" name="maintenance">
											<?php
											echo '<option value="0">'.T_('Aucun').'</option>';
											//display service list
											$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE disable='0' ORDER BY name ASC");
											$qry->execute();
											while($row=$qry->fetch()) 
											{
												if($globalrow['maintenance']==$row['id']) {
													echo "<option selected value=\"$row[id]\">$row[name]</option>";
												} else {
													echo "<option value=\"$row[id]\">$row[name]</option>";
												}
											}
											$qry->closeCursor();
											?>
										</select>
									</div>
								</div>
								<!-- END maintenance part -->
								
								<!-- START stock date part -->
								<?php
								if($globalrow['virtualization']==0)
								{
									echo '
									<div class="form-group row">
										<div class="col-sm-2 col-form-label text-sm-right pr-0">
											<label class="mb-0" for="date_stock">'.T_("Date d'achat").' :</label>
										</div>
										<div class="col-sm-9">
											<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" autocomplete="off" data-toggle="datetimepicker" data-target="#date_stock" type="text" size="25" name="date_stock" id="date_stock" value="'; if($_POST['date_stock']) echo $_POST['date_stock']; else echo $globalrow['date_stock']; echo '" >
										</div> 
									</div>
									';
								}
								?>
								<!-- END stock date part -->
								
								<!-- START install date part -->
								<?php
									echo '
										<div class="form-group row">
											<div class="col-sm-2 col-form-label text-sm-right pr-0">
												<label class="mb-0" for="date_install">'.T_("Date d'installation").' :</label>
											</div>
											<div class="col-sm-9">
												<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" data-toggle="datetimepicker" data-target="#date_install" autocomplete="off" type="text" size="25" name="date_install" id="date_install" value="'; if($_POST['date_install']) echo $_POST['date_install']; else echo $globalrow['date_install']; echo'" >
											</div> 
										</div>
									';
								?>
								<!-- END install date part -->
								
								<!-- START end warranty date part -->
								<?php
									if($globalrow['virtualization']==0)
									{
										if($_POST['date_end_warranty']) {$globalrow['date_end_warranty']=$_POST['date_end_warranty'];}

										//define color of warranty
										if($globalrow['date_end_warranty'])
										{
											//convert date format to calculate if incorrect format is detected
											if(strpos($globalrow['date_end_warranty'], '-') !== false) {$date_end_warranty_conv=$globalrow['date_end_warranty'];} 
											else {
												$date_end_warranty_conv=DateTime::createFromFormat('d/m/Y', $globalrow['date_end_warranty']);
												$date_end_warranty_conv=$date_end_warranty_conv->format('Y-m-d');
											}
											
											if($globalrow['date_stock']=='0000-00-00')
											{
												$warranty_icon='<i title="'.T_("La date d'achat n'est pas renseignée").'" class="fa fa-certificate text-warning"></i>';
											}elseif($date_end_warranty_conv >  date('Y-m-d')){
												$warranty_icon='<i title="'.T_('Équipement sous garantie').'" class="fa fa-certificate text-success"></i>';
											}elseif($globalrow['state']==4)  {
												$warranty_icon='';
											}else{
												$warranty_icon='<i title="'.T_('Équipement hors garantie').'" class="fa fa-certificate text-danger"></i>';
											}
										} else {
											$warranty_icon='';
										}
										echo '
											<div class="form-group row">
												<div class="col-sm-2 col-form-label text-sm-right pr-0">
													<label class="mb-0" for="date_end_warranty">'.$warranty_icon.' '.T_('Date de fin de garantie').' :</label>
												</div>
												<div class="col-sm-9">
													<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" data-toggle="datetimepicker" data-target="#date_end_warranty"  autocomplete="off" type="text" size="25" name="date_end_warranty" id="date_end_warranty" value="'.$globalrow['date_end_warranty'].'" >
												</div> 
											</div>
										';
									}
								?>
								<!-- END end warranty date part -->
								
								<!-- START Standbye date part -->
								<?php
									if($_GET['action']!='new' && $globalrow['state']!='2' && $globalrow['state']!='1' )
									{
										echo '
											<div class="form-group row">
												<div class="col-sm-2 col-form-label text-sm-right pr-0">
													<label class="mb-0" for="date_standbye">'.T_('Date de standbye').'</label>
												</div>
												<div class="col-sm-9">
													<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" data-toggle="datetimepicker" data-target="#date_standbye" type="text" size="25" name="date_standbye" id="date_standbye" value="'; if($_POST['date_standbye']) echo $_POST['date_standbye']; else echo $globalrow['date_standbye']; echo '" >
												</div> 
											</div>
										';
									}
								?>
								<!-- END Standbye date part -->
								
								<!-- START recycle date part -->
								<?php
								if($globalrow['state']=='4' || $_POST['state']=='4')
								{
									echo '
										<div class="form-group row">
											<div class="col-sm-2 col-form-label text-sm-right pr-0">
												<label class="mb-0" for="date_recycle">'.T_('Date de recyclage').' :</label>
											</div>
											<div class="col-sm-9">
												<input class="form-control '; if($mobile){echo 'col-7';} else {echo 'col-3';} echo '" data-toggle="datetimepicker" data-target="#date_recycle" type="text" size="25" name="date_recycle" id="date_recycle" value="'; if($_POST['date_recycle']) echo $_POST['date_recycle']; else echo $globalrow['date_recycle']; echo '" >
											</div> 
										</div>
									';
								}
								?>
								<!-- END recycle date part -->
								
								<!-- START state part -->	
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="state">
											<?php echo T_('État'); ?> :
										</label>
										
									</div>
									<div class="col-sm-9">
										<select class="form-control <?php if($mobile){echo 'col-7';} else {echo 'col-3';} ?>" id="state" name="state">
											<?php
											//display states list
											$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_state` WHERE disable='0' ORDER BY `order` ASC");
											$qry->execute();
											while($row=$qry->fetch()) 
											{
												if($globalrow['state']==$row['id']) {
													echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';
												} else {
													echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
												}
											}
											$qry->closeCursor();
											
											?>
										</select>
									</div>
								</div>
								<!-- END state part -->
								
							</div>
							<!-- SECOND COLUMN PART -->
							<div class="col-lg-3" >
								<br /><br /><br /><br /><br />
								<?php 
									//display model image if exist, display in priority model if image exist for more precision
									if($_POST['model']!='') 
									{
										$qry=$db->prepare("SELECT `image` FROM `tassets_model` WHERE id LIKE :id");
										$qry->execute(array('id' => $_POST['model']));
										$row=$qry->fetch();
										$qry->closeCursor();
										if(empty($row['image'])) {$row['image']='';}
										$model=$_POST['model'];
									} elseif($_POST['type']!='') 
									{
										$qry=$db->prepare("SELECT `image` FROM `tassets_model` WHERE type LIKE :id");
										$qry->execute(array('id' => $_POST['type']));
										$row=$qry->fetch();
										$qry->closeCursor();
										if(empty($row['image'])) {$row['image']='';}
										$model=$_POST['model'];
									} else {
										$qry=$db->prepare("SELECT `image` FROM `tassets_model` WHERE id LIKE :id");
										$qry->execute(array('id' => $globalrow['model']));
										$row=$qry->fetch();
										$qry->closeCursor();
										if(empty($row['image'])) {$row['image']='';}
										$model=$globalrow['model'];
									}
									if($ip_asset==1) {
										//find ip lan to create link
										$qry2=$db->prepare("SELECT `ip` FROM `tassets_iface` WHERE asset_id=:asset_id AND role_id='1' AND disable='0'");
										$qry2->execute(array('asset_id' => $globalrow['id']));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										if(empty($row2['ip'])) {$row2['ip']='';}
										if($row2['ip'] && $row['image']!='') {echo '<a href="http://'.$row2['ip'].'" target="_blank" title="'.T_("Accédez à l'interface web de cet équipement :").' http://'.$row2['ip'].'" >';}
									}
									//display and re-size asset too large image
									if($row['image']!='') 
									{
										//check if file exist before display it
										if(file_exists("./images/model/$row[image]"))
										{
											$img_size = getimagesize("./images/model/$row[image]");
											$img_width=$img_size[0];
											if($img_width>250) {$img_width='width="250"';} else {$img_width='';}
											echo '<img align="left" border="1" alt="image du modèle" '.$img_width.' src="./images/model/'.$row['image'].'" />';
										} 
									}
									if($ip_asset==1) {if($row2['ip'] && $row['image']!='') {echo '</a>'; }}	
								?>
							</div>
						</div> <!-- div row -->
						<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center" >	
							<button title="ALT+SHIFT+s" accesskey="s" name="modify" id="modify" value="modify" type="submit" class="btn btn-success">
								<i class="fa fa-save fa fa-on-right bigger-110"></i> 
								<?php if(!$mobile) {echo T_('Enregistrer');} ?>
							</button>
							&nbsp;
							<button title="ALT+SHIFT+c" accesskey="c" name="quit" id="quit2" value="quit" type="submit" class="btn btn-purple">
								<i class="fa fa-save fa fa-on-right bigger-110"></i> 
								<?php if(!$mobile) {echo T_('Enregistrer et Fermer');} ?>
							</button>
							&nbsp;
							<button title="ALT+SHIFT+x" accesskey="x" name="cancel" id="cancel" value="cancel" type="submit" class="btn btn-danger">
								<i class="fa fa-times fa fa-on-right bigger-110"></i> 
								<?php if(!$mobile) {echo T_('Annuler');} ?>
							</button>
						</div> <!-- div form-actions -->
					</div> <!-- div widget main -->
				</div> <!-- div widget body -->
			</form>
		</div> <!-- div end sm -->
	</div> <!-- div end x12 -->
</div> <!-- div end row -->

<?php if($rparameters['debug'] && $debug_error) {echo "<u><b>DEBUG MODE:</b></u><br /> $debug_error";} ?>

<!-- chosen script  -->
<script type="text/javascript" src="./components/chosen/chosen.jquery.min.js"></script>

<!-- datetime picker scripts  -->
<script src="./components/moment/min/moment.min.js" charset="UTF-8"></script>
<?php 
	if($ruser['language']=='fr_FR') {echo '<script src="./components/moment/locale/fr.js" charset="UTF-8"></script>';} 
	if($ruser['language']=='de_DE') {echo '<script src="./components/moment/locale/de.js" charset="UTF-8"></script>';} 
	if($ruser['language']=='es_ES') {echo '<script src="./components/moment/locale/es.js" charset="UTF-8"></script>';} 
?>
<script src="./components/tempus-dominus/build/js/tempusdominus-bootstrap-4.min.js" charset="UTF-8"></script> 

<script type="text/javascript">
	jQuery(function($) {
		$(".chosen-select").chosen({
			allow_single_deselect:true,
			no_results_text: "<?php echo T_('Aucun résultat pour'); ?>"
		});
		
		var date = moment($('#date_install').val(), 'DD-MM-YYYY').toDate();
		$('#date_install').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
		var date = moment($('#date_end_warranty').val(), 'DD-MM-YYYY').toDate();
		$('#date_end_warranty').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
		var date = moment($('#date_stock').val(), 'DD-MM-YYYY').toDate();
		$('#date_stock').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
		var date = moment($('#date_recycle').val(), 'DD-MM-YYYY').toDate();
		$('#date_recycle').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
		var date = moment($('#date_standbye').val(), 'DD-MM-YYYY').toDate();
		$('#date_standbye').datetimepicker({ date:date, format: 'DD/MM/YYYY' });
	});
</script>		