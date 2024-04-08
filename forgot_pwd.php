<?php
################################################################################
# @Name : ./forgot_pwd.php 
# @Description : send mail to user to init password
# @Call : /login.php
# @Author : Flox
# @Version : 3.2.2
# @Create : 25/10/2019
# @Update : 28/05/2020
################################################################################

//initialize variable
if(!isset($msg_error)) $msg_error = '';
if(!isset($msg_success)) $msg_success = '';

if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['password'])) $_POST['password'] = '';
if(!isset($_POST['password2'])) $_POST['password2'] = '';

$hide_form=0;

//secure string
$_POST['mail']=strip_tags($_POST['mail']);

if($rparameters['user_forgot_pwd'])
{
	//modify password
	if($_GET['token'])
	{
		//check token
		$qry=$db->prepare("SELECT `user_id` FROM `ttoken` WHERE token=:token AND action='forgot_pwd' AND user_id!='0'");
		$qry->execute(array('token' => $_GET['token']));
		$token=$qry->fetch();
		$qry->closeCursor();
		if($token)
		{
			
			if(isset($_POST['submit']))
			{
				//check passwords
				if(!$_POST['password'] || !$_POST['password2']) {$msg_error=T_('Les champs mot de passe doivent être renseignés.');}
				if($_POST['password']!=$_POST['password2']) {$msg_error=T_('Les mots de passes ne sont pas identiques.');}
				if($rparameters['user_password_policy'] && $_POST['password'] && !$msg_error)
				{
					if(strlen($_POST['password'])<$rparameters['user_password_policy_min_lenght'])
					{
						$msg_error=T_('Le mot de passe doit faire').' '.$rparameters['user_password_policy_min_lenght'].' '.T_('caractères minimum');
					}elseif($rparameters['user_password_policy_special_char'] && !preg_match('/[^a-zA-Z\d]/', $_POST['password']))
					{
						$msg_error=T_('Le mot de passe doit contenir un caractère spécial');
					}elseif($rparameters['user_password_policy_min_maj'] && (!preg_match('/[A-Z]/', $_POST['password']) || !preg_match('/[a-z]/', $_POST['password'])))
					{
						$msg_error=T_('Le mot de passe doit posséder au moins une lettre majuscule et une minuscule');
					}
				}
				
				if(!$msg_error && $token['user_id'])
				{
					//update password
					$hash=password_hash($_POST['password'], PASSWORD_DEFAULT);
					$last_pwd_chg=date('Y-m-d');
					$qry=$db->prepare("UPDATE `tusers` SET `password`=:password, `last_pwd_chg`=:last_pwd_chg WHERE `id`=:id");
					$qry->execute(array('password' => $hash, 'last_pwd_chg' => $last_pwd_chg, 'id' =>  $token['user_id']));
					
					//clean token
					$qry=$db->prepare("DELETE FROM `ttoken` WHERE user_id=:user_id AND action='forgot_pwd'");
					$qry->execute(array('user_id' => $token['user_id']));
					
					$msg_success=T_("Votre mot de passe à été modifié avec succès, vous pouvez vous connecter à l'application");
				}
			}
		} else {
			$msg_error=T_('Jeton invalide, contacter votre administrateur');
			$hide_form=1;
		}
	}elseif(isset($_POST['submit'])) //send mail init password
	{
	    if(!$_POST['mail']) {$msg_error=T_('Merci de renseigner une adresse mail');} 
		elseif(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {$msg_error=T_("L'adresse mail est incorrecte");}
		else {
			//check if mail exist on gestsup user
			$qry=$db->prepare("SELECT `id`,`mail` FROM `tusers` WHERE mail=:mail AND disable='0'");
			$qry->execute(array('mail' => $_POST['mail']));
			$user=$qry->fetch();
			$qry->closeCursor();
			if($user)
			{
				//check only one account have this mail	
				$qry=$db->prepare("SELECT COUNT(id) FROM `tusers` WHERE mail=:mail AND disable='0'");
				$qry->execute(array('mail' => $_POST['mail']));
				$mail_number=$qry->fetch();
				$qry->closeCursor();
				if($mail_number[0]==1)
				{
					//create token
					$token = uniqid(20);
					$qry=$db->prepare("INSERT INTO `ttoken` (`token`,`action`,`user_id`) VALUES (:token,'forgot_pwd',:user_id)");
					$qry->execute(array('token' => $token,'user_id' => $user['id']));
					
					//mail definition
					$from=$rparameters['mail_from_adr'];
					$to=$user['mail'];
					$object=T_('Ré-initialisation de votre mot de passe GestSup');
					$message = '
					'.T_('Bonjour,').' <br />
					<br />
					<br />
					'.T_('Vous avez fait une demande de ré-initialisation de votre mot de passe GestSup, merci de cliquer sur le lien ci-après pour définir un nouveau mot de passe').' :<br />
					<br />
					<a href="'.$rparameters['server_url'].'/index.php?page=forgot_pwd&token='.$token.'">'.T_('Ré-initialiser mon mot de passe').'</a><br />
					<br />
					'.T_('Cordialement').'.';
					require('./core/message.php');
					
					if($mail_send)
					{
						$msg_success=T_('Vous aller recevoir un mail pour ré-initialiser votre mot de passe');
					} else {
						$msg_error=T_("Émission de mail impossible, contacter votre administrateur.");
					}
				} else {
					$msg_error=T_("Plusieurs comptes GestSup possède cette adresse mail, contacter votre administrateur.");
				}
			} else {
				$msg_error=T_("Aucun compte GestSup ne possède cette adresse mail.");
			}
		}
	}
	//page
	echo '
	<body>
		<div class="body-container" style=" background-image: linear-gradient(#6baace, #264783); background-attachment: fixed; background-repeat: no-repeat;" >
			<div class="main-container container">
				<div role="main" class="main-content ">
					<div class="justify-content-center pb-2">
						';
							if($msg_success){echo DisplayMessage('success',$msg_success);}
							if($msg_error){echo DisplayMessage('error',$msg_error);}
							echo '
						<div class="d-flex flex-column align-items-center justify-content-start">
							<h1 class="mt-5"><i style="ms-transform:rotate(45deg); webkit-transform:rotate(45deg); transform:rotate(45deg);" class="fa fa-ticket-alt text-75 text-white" data-fa-transform="rotate-45"></i>
								<span class="text-90 text-white">GestSup</span>
							</h1>
						</div>
						<div class="d-flex flex-column align-items-center justify-content-start">
							
							<h5 class="text-dark-lt3">
								';if(isset($rparameters['company'])) echo $rparameters['company']; echo' 
							</h5>
						</div>
						<div class="d-flex flex-column align-items-center justify-content-start">
							';
							//re-size logo if height superior 40px
							$logo_width='';
							if($rparameters['logo'] && file_exists('./upload/logo/'.$rparameters['logo'])) 
							{
								$size=getimagesize('./upload/logo/'.$rparameters['logo']);
								if($size[0]>150) {$logo_width='width="150"';}
							}
							//display logo if image file exist
							if(file_exists("./upload/logo/$rparameters[logo]"))
							{
								echo '<img style="border-style: none" alt="logo" '.$logo_width.' src="./upload/logo/'; if(!$rparameters['logo']) {echo 'logo.png';} else {echo $rparameters['logo'];}  echo '" />';
							}
							echo '
						</div>
					</div>
					<div class="p-4 p-md-4 mh-2 ">
						<div class="row justify-content-center ">
							<div class="shadow radius-1 overflow-hidden bg-white col-12 col-lg-4 ">
								<div class="row ">
									<a href="index.php" title="'.T_('Retour').'" class="btn btn-light-default bg-transparent ml-3 mt-3"><i class="fa fa-arrow-left"></i></a>
									<div class="col-12 bgc-white px-0 pt-4 pb-4">
										<div class="" data-swipe="center">
											<div class="active show px-lg-0 pb-0 pt-0" id="id-tab-login">
												<div class="d-lg-block col-md-8 offset-md-2 px-0 pb-4">
													<h4 class="text-dark-tp4 border-b-1 brc-grey-l1 pb-1 text-130">
														<i class="fa fa-key text-brown-m2 mr-1"></i>
														'.T_('Mot de passe oublié').'
													</h4>
												</div>';
												if(!$hide_form)
												{
													if($_GET['token'])
													{
														//display init password form
														echo '
														<form id="conn" method="post" action="">	
															<div class="form-group col-md-8 offset-md-2 mb-6">
																<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2 ">
																	<input autocomplete="new-password" type="password" class="form-control form-control-lg pr-4 shadow-none" id="password" name="password" autocomplete="off" value="'.$_POST['password'].'" />
																	<i class="fa fa-key text-grey-m2 ml-n4"></i>
																	<label class="floating-label text-grey-l1 text-100 ml-n3" for="mail">'.T_('Nouveau mot de passe').'</label>
																</div>
															</div>
															<div class="form-group col-md-8 offset-md-2 mb-6">
																<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2 ">
																	<input autocomplete="new-password" type="password" class="form-control form-control-lg pr-4 shadow-none" id="password2" name="password2" autocomplete="off" value="'.$_POST['password2'].'" />
																	<i class="fa fa-key text-grey-m2 ml-n4"></i>
																	<label class="floating-label text-grey-l1 text-100 ml-n3" for="mail">'.T_('Confirmation nouveau mot de passe').'</label>
																</div>
															</div>
															<div class="form-group col-md-6 offset-md-3 mt-4">
																<button onclick="submit()" type="submit" id="submit" name="submit" class="btn btn-warning btn-block btn-md btn-bold mt-2 mb-4">
																	<i class="fa fa-check"></i>
																	'.T_('Valider').'
																</button>
															</div>
														</form>
														';
														
													} else {
														//display mail form
														echo '
														<form id="conn" method="post" action="">	
															<div class="form-group col-md-8 offset-md-2 mb-6">
																<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2 ">
																	<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="mail" name="mail" autocomplete="off" value="'.$_POST['mail'].'" />
																	<i class="fa fa-envelope text-grey-m2 ml-n4"></i>
																	<label class="floating-label text-grey-l1 text-100 ml-n3" for="mail">'.T_('Mail').'</label>
																</div>
															</div>
															<div class="form-group col-md-6 offset-md-3 mt-4">
																<button onclick="submit()" type="submit" id="submit" name="submit" class="btn btn-warning btn-block btn-md btn-bold mt-2 mb-4">
																	<i class="fa fa-check"></i>
																	'.T_('Valider').'
																</button>
															</div>
															<div class="space-4"></div>
														</form>
														';
													}
												}
												echo '
											</div>
										</div><!-- .tab-content -->
									</div>
								</div>
							 </div>
						</div>
					</div>
				</div><!-- /main -->
			</div><!-- /.main-container -->
		</div><!-- /.body-container -->
	</body>
		<!-- DO NOT DELETE OR MODIFY THIS LINE THANKS -->
			<span style="position: fixed; bottom: 0px; right: 0px;"><a title="'.T_('Ouvre un nouvel onglet vers le site gestsup.fr').'" target="_blank" href="https://gestsup.fr">GestSup.fr</a></span>
		<!-- DO NOT DELETE OR MODIFY THIS LINE THANKS -->
	';
	
} else {
	echo DisplayMessage('error',T_("La fonction de ré-initialisation des mots de passe est désactivée par votre administrateur"));
}
?>