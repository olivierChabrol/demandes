<?php
################################################################################
# @Name : asset_iface.php
# @Description : add or edit IP interface from an asset
# @call : ./asset.php
# @parameters :  
# @Author : Flox
# @Create : 22/03/2017
# @Update : 28/01/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($_POST['addiface'])) $_POST['addiface'] = ''; 
if(!isset($_POST['editiface'])) $_POST['editiface'] = ''; 


$db_id=strip_tags($db->quote($_GET['id']));
$db_iface=strip_tags($db->quote($_GET['iface']));

//submit actions
if($rright['asset'] && $_POST['addiface'])
{
	$qry=$db->prepare("INSERT INTO `tassets_iface` (`role_id`,`asset_id`,`netbios`,`ip`,`mac`,`disable`) VALUES (:role_id,:asset_id,'','','','0')");
	$qry->execute(array('role_id' => $_POST['role'],'asset_id' => $_GET['id']));
	
	//redirect
	$www = "./index.php?page=asset&id=$_GET[id]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}
if($rright['asset'] && $_POST['editiface'])
{
	$qry=$db->prepare("UPDATE `tassets_iface` SET `role_id`=:role_id WHERE `id`=:id");
	$qry->execute(array('role_id' => $_POST['role'],'id' => $_GET['iface']));
	
	//redirect
	$www = "./index.php?page=asset&id=$_GET[id]&$url_get_parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}


//form addiface
echo '
<div class="modal fade" id="addiface" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel"><i class="fa fa-plug text-success pr-2"></i>'.T_("Ajouter une interface IP").'</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form name="form1" method="POST" action="" id="form1">
					<input  name="addiface" type="hidden" value="1" />
					<label for="role">'.T_("Rôle de l'interface").' :</label>
					<select class="form-control col-3 d-inline-block" id="role" name="role">';
						$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_iface_role` WHERE disable='0' ORDER BY name ASC");
						$qry->execute();
						while($row=$qry->fetch()) 
						{
							echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
						}
						$qry->closeCursor();
						echo '
					</select>	
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" onclick="$(\'form#form1\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Ajouter').'</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
			</div>
		</div>
	</div>
</div>
';


//form editiface
if($rright['asset'] && $_GET['action']=='editiface' && $_GET['iface'])
{
	//get current iface
	$qry=$db->prepare("SELECT `role_id` FROM `tassets_iface` WHERE id=:id");
	$qry->execute(array('id' => $_GET['iface']));
	$role_id=$qry->fetch();
	$qry->closeCursor();
	
	echo '
	<div class="modal fade" id="editiface" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-plug text-warning pr-2"></i>'.T_("Modifier une interface IP").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form2" method="POST" action="" id="form2">
						<input  name="editiface" type="hidden" value="1" />
						<label for="role">'.T_("Rôle de l'interface").' :</label>
						<select class="form-control col-3 d-inline-block" id="role" name="role">';
							$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_iface_role` WHERE disable='0' ORDER BY name ASC");
							$qry->execute(array('id' => $_GET['id']));
							while($row=$qry->fetch()) 
							{
								if ($row['id']==$role_id[0]) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
							}
							$qry->closeCursor();
							echo '
						</select>	
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$(\'form#form2\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Modifier').'</button>
					<button type="button" class="btn btn-danger" onclick="$(\'form#form2\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#editiface").modal(\'show\');});</script>
	';
}
?>