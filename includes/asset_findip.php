<?php
################################################################################
# @Name : asset_findip.php
# @Description : search free IPv4 in selected network
# @call : ./asset.php
# @parameters :  
# @Author : Flox
# @Create : 16/12/2015
# @Update : 28/01/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($_POST['Ajouter'])) $_POST['Ajouter'] = ''; 
if(!isset($_POST['network'])) $_POST['network'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['ip'])) $_POST['ip'] = ''; 

if($_POST['add'] && $_POST['ip']!='')
{
	//redirect to close modal
	if($_GET['action']=='findip1')
	{$dest_iface='ip_lan_new';}
	elseif
	($_GET['action']=='findip2')
	{$dest_iface='ip_wifi_new';}
	else 
	{
		$dest_iface=explode('_',$_GET['action']);
		$dest_iface=$dest_iface[1];
	}
	$www = "./index.php?page=asset&id=$_GET[id]&iface=$dest_iface&findip=$_POST[ip]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if($_POST['network']!='')
{
	//get selected network informations
	$qry=$db->prepare("SELECT `netmask`,`network` FROM `tassets_network` WHERE id=:id");
	$qry->execute(array('id' => $_POST['network']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	$netmask=$row['netmask'];
	$network=$row['network'];
	$network=explode('.',$network);
	
	//find free ip in this network
	for ($i = 1; $i < 254; $i++) {
		//generate test ip
		$test_ip=$network[0].'.'.$network[1].'.'.$network[2].'.'.$i;
		//check if this ip exist
		$exist_ip=0;
		$qry=$db->prepare("
		SELECT tassets_iface.ip FROM `tassets_iface` 
		INNER JOIN tassets ON tassets.id=tassets_iface.asset_id
		INNER JOIN tassets_state ON tassets_state.id=tassets.state
		WHERE 
		tassets_iface.ip=:ip AND
		tassets_state.block_ip_search=1 AND
		tassets_iface.disable='0' AND
		tassets.disable='0'
		");
		$qry->execute(array('ip' => $test_ip));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if(isset($row[0])) {$exist_ip=1;} 
		if ($exist_ip!=1) {break;}
	}
	$findip=$test_ip;
} else {
	$findip=$_POST['ip'];

}

if($_POST['cancel'])
{
	$www = "./index.php?page=asset&id=$_GET[id]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}


//form editiface
if($rright['asset'])
{
	//get current iface
	$qry=$db->prepare("SELECT `role_id` FROM `tassets_iface` WHERE id=:id");
	$qry->execute(array('id' => $_GET['iface']));
	$role_id=$qry->fetch();
	$qry->closeCursor();
	
	echo '
	<div class="modal fade" id="findip" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-search text-success pr-2"></i>'.T_("Recherche d'adresse IP disponible").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form2" method="POST" action="" id="form2">
						<input name="add" type="hidden" value="1" />
						<label for="network" >'.T_('Réseau').' :</label> 
						<select class="form-control col-4 d-inline-block" id="network" name="network" onchange="submit();">
							';
								echo '<option value="">'.T_('Aucun').'</option>';
								$qry=$db->prepare("SELECT id,name FROM `tassets_network` WHERE disable='0' ORDER BY name ASC");
								$qry->execute();
								while($row=$qry->fetch()) 
								{
									if ($_POST['network']==$row['id']) 
									{
										echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
									} else {
										echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';	
									}
								}
								$qry->closeCursor();
								
								echo '	
						</select>
						<div class="pt-2"></div>
						<label for="ip">IP :</label> 
						<input class="form-control col-4 d-inline-block" name="ip" type="text" value="'.$findip.'" size="20">
					</form>
					<form name="form3" method="POST" action="" id="form3">
						<input name="cancel" type="hidden" value="1" />
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$(\'form#form2\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Modifier').'</button>
					<button type="button" class="btn btn-danger" onclick="$(\'form#form3\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#findip").modal(\'show\');});</script>
	';
}
?>