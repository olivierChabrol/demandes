<?php
################################################################################
# @Name : modify_pwd.php
# @Description : change password popup
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 05/02/2012
# @Update : 28/05/2020
# @Version : 3.2.2
################################################################################

//initialize variables 
if(!isset($_POST['modifypwd'])) $_POST['modifypwd'] = ''; 
if(!isset($_POST['oldpwd'])) $_POST['oldpwd'] = ''; 
if(!isset($_POST['newpwd1'])) $_POST['newpwd1'] = ''; 
if(!isset($_POST['newpwd2'])) $_POST['newpwd2'] = ''; 
if(!isset($updated)) $updated = ''; 
if(!isset($oldpassword)) $oldpassword = ''; 
if(!isset($secure_password)) $secure_password = ''; 
if(!isset($boxtext)) $boxtext = ''; 
if(!isset($error)) $error = 0; 
  
if($_POST['modifypwd'] && $_SESSION['user_id'])
{
	
	//get user informations
	$qry=$db->prepare("SELECT `salt`,`password` FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_SESSION['user_id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	//check old password
	$oldpassword=0;
	if(password_verify($_POST['oldpwd'],$row['password'])) {$oldpassword=1;}
	
	if($_POST['oldpwd']=="" || $_POST['newpwd1']=="" || $_POST['newpwd2']=="") //check empty password
	{
		$error=T_('Veuillez remplir tous les champs');
	}
	elseif ($oldpassword!='1') //check old password
	{
		$error=T_('Votre ancien mot de passe est erroné');
	}
	elseif ($_POST['newpwd1']!=$_POST['newpwd2']) //check new passwords
	{
		$error=T_('Les deux nouveaux mot de passes sont différents');
	}
	elseif($rparameters['user_password_policy'] && $_POST['newpwd1'] && (strlen($_POST['newpwd1'])<$rparameters['user_password_policy_min_lenght'])) //password policy
	{
		$error=T_('Le mot de passe doit faire').' '.$rparameters['user_password_policy_min_lenght'].' '.T_('caractères minimum');
	}
	elseif($rparameters['user_password_policy'] && $_POST['newpwd1'] && ($rparameters['user_password_policy_special_char'] && !preg_match('/[^a-zA-Z\d]/', $_POST['newpwd1']))) //password policy
	{
		$error=T_('Le mot de passe doit contenir un caractère spécial');
	}
	elseif($rparameters['user_password_policy_min_maj'] && (!preg_match('/[A-Z]/', $_POST['newpwd1']) || !preg_match('/[a-z]/', $_POST['newpwd1']))) //password policy
	{
		$error=T_('Le mot de passe doit au moins une lettre majuscule et une minuscule');
	}
	else
	{
		if($_POST['newpwd1']!='') { //update password
			$date=date('Y-m-d');
			$hash = password_hash($_POST['newpwd1'], PASSWORD_DEFAULT);
			$qry=$db->prepare("UPDATE `tusers` SET `chgpwd`=0, `last_pwd_chg`=:last_pwd_chg, `password`=:password WHERE `id`=:id");
			$qry->execute(array('last_pwd_chg' => $date,'password' => $hash,'id' => $_SESSION['user_id']));
			$updated=1;
		}
	} 
}
if($updated==1)
{
	//hide
} else {
	//display modal
	echo '
	<div class="modal fade" id="modify_pwd" tabindex="-1" role="dialog">
		<div class="modal-dialog " role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title"><i class="fa fa-lock text-primary-m2"></i> '.T_('Modification mot de passe').'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">×</span>
					</button>
				</div>
				';
					if($error){echo DisplayMessage('error',$error);}
				echo '
				<div class="modal-body">
					<form name="form" method="POST" action="" id="form">
						<input name="modifypwd" type="hidden" value="1">
						<label for="oldpwd" >'.T_('Ancien mot de passe').' :</label> 
						<input style="width:auto" class="form-control form-control-sm" name="oldpwd" type="password" >
						<label for="newpwd1" >'.T_('Nouveau mot de passe').' :</label> 
						<input style="width:auto" class="form-control form-control-sm" name="newpwd1" type="password" >
						<label for="newpwd2" >'.T_('Nouveau mot de passe').' :</label>
						<input style="width:auto" class="form-control form-control-sm" name="newpwd2" type="password" >
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" onclick="$(\'form#form\').submit();" class="btn btn-success"><i class="fa fa-check"></i> '.T_('Valider').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#modify_pwd").modal(\'show\');});</script>
	';
}
?>