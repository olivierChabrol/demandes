<?php
################################################################################
# @Name : login.php
# @Description : Login page for enter credentials and redirect to register page
# @Call : index.php
# @Parameters : 
# @Author : Flox
# @Create : 07/03/2010
# @Update : 28/08/2020
# @Version : 3.2.4
################################################################################
	
//initialize variable
if(!isset($msg_success)) $msg_success = '';
if(!isset($msg_error)) $msg_error = '';
if(!isset($info)) $info = '';

if(!isset($_POST['login'])) $_POST['login'] = '';
if(!isset($_POST['password'])) $_POST['password'] = '';
if(!isset($_POST['password2'])) $_POST['password2'] = '';
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['phone'])) $_POST['phone'] = '';
if(!isset($_POST['firstname'])) $_POST['firstname'] = '';
if(!isset($_POST['lastname'])) $_POST['lastname'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';
if(!isset($_POST['captcha'])) $_POST['captcha'] = '';
if(!isset($_GET['page'])) $_GET['page'] = '';

//secure string
$_POST['login']=strip_tags($_POST['login']);
$_POST['password']=strip_tags($_POST['password']);
$_POST['password2']=strip_tags($_POST['password2']);
$_POST['mail']=strip_tags($_POST['mail']);
$_POST['phone']=strip_tags($_POST['phone']);
$_POST['firstname']=strip_tags($_POST['firstname']);
$_POST['lastname']=strip_tags($_POST['lastname']);
$_POST['company']=strip_tags($_POST['company']);

//default values
$defaultprofile=2; //1 is poweruser, 2 is single user 

//block direct access 
if(!$_GET['page']) {echo 'ERROR : please use link in login page'; die;}

if($rparameters['user_register'])
{
	
	//actions on using mail link
	if($_GET['token'])
	{
		//sanitize string
		$_GET['token']=htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');
		
		//check existing token
		$qry=$db->prepare("SELECT `id`,`user_id` FROM `ttoken` WHERE token=:token");
		$qry->execute(array('token' => $_GET['token']));
		$token=$qry->fetch();
		$qry->closeCursor();
		
		if(isset($token['user_id']))
		{
			//enable account
			$qry=$db->prepare("UPDATE `tusers` SET `disable`='0' WHERE `id`=:id");
			$qry->execute(array('id' => $token['user_id']));
			//delete token
			$qry=$db->prepare("DELETE FROM `ttoken` WHERE id=:id AND action='subscription'");
			$qry->execute(array('id' => $token['id']));
			$msg_success=T_("Votre compte à été activé, vous pouvez-vous connecter à l'application en cliquant sur le lien Retour");
		} else {
			$msg_error=T_("Jeton invalide, merci de contacter votre administrateur");
		}
	}
    //actions on submit
	if(isset($_POST['submit']))
	{
	    //check inputs
	    if($_POST['firstname']) {
    	    if($_POST['lastname']) {
        	    if($_POST['login']) {
        	        if($_POST['password']) {
        	             if($_POST['password2']) {
            	             if($_POST['mail']) {
            	                 if($_POST['password2']==$_POST['password']) {
									//check if user id already exist
									$exist_user = false;
									$qry = $db->prepare("SELECT `id` FROM `tusers` WHERE `mail`=:mail OR `login`=:login ");
									$qry->execute(array('mail' => $_POST['mail'],'login' => $_POST['login']));	
									while ($row = $qry->fetch()) {$exist_user = true;}
									$qry->closeCursor();									
									if($exist_user==true)
									{
										$msg_error=T_("L'identifiant ou l'adresse mail renseignée existe déjà.");
									} else {
										if(extension_loaded('gd')) //check captcha
										{
											if(isset($_POST['captcha'])){
												if($_POST['captcha']!=$_SESSION['code']){
													
													$msg_error=T_("Captcha invalide, merci de recopier les caractères présents dans l'image.");
												}
											}
										}
										if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) //check if mail is valid
										{
											$msg_error=T_("Votre adresse mail est invalide.");
										}
										elseif(!$rparameters['mail'] || !$rparameters['mail_smtp']) //check smtp connector enable
										{
											$msg_error=T_("Votre connecteur SMTP n'est pas configuré, prenez contact avec votre administrateur.");
										}
										elseif($rparameters['user_password_policy']) //check password policy
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
										if(!$msg_error && !$msg_success)
										{
											$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
											
											if($rparameters['user_advanced'] && $_POST['company']) //find company if exist else create it
											{
												$qry=$db->prepare("SELECT `id` FROM `tcompany` WHERE name LIKE name");
												$qry->execute(array('name' => $_POST['company']));
												$company=$qry->fetch();
												$qry->closeCursor();
												if(empty($company['id']))
												{
													$qry=$db->prepare("INSERT INTO `tcompany` (`name`) VALUES (:name)");
													$qry->execute(array('name' => $_POST['company']));
													$company['id']=$db->lastInsertId();
												}
											} else {$company['id']=0;}
											
											//insert a disable user
											$qry=$db->prepare("INSERT INTO `tusers` (`firstname`,`lastname`,`password`,`mail`,`phone`,`profile`,`login`,`company`,`chgpwd`,`disable`) VALUES (:firstname,:lastname,:password,:mail,:phone,:profile,:login,:company,'0','1')");
											$qry->execute(array('firstname' => $_POST['firstname'],'lastname' => $_POST['lastname'],'password' => $hash,'mail' => $_POST['mail'],'phone' => $_POST['phone'],'profile' => $defaultprofile,'login' => $_POST['login'],'company' => $company['id']));
											$user_id=$db->lastInsertId();
											
											//create token
											$token = uniqid(20);
											$qry=$db->prepare("INSERT INTO `ttoken` (`token`,`action`,`user_id`) VALUES (:token,'subscription',:user_id)");
											$qry->execute(array('token' => $token,'user_id' => $user_id));
											
											//mail definition
											$from=$rparameters['mail_from_adr'];
											$to=$_POST['mail'];
											$object=T_('Activation de votre compte GestSup');
											$message = '
											'.T_('Bonjour,').' <br />
											<br />
											<br />
											'.T_("Vous avez fait une demande d'enregistrement sur l'application GestSup").'.<br />
											'.T_("Pour activer votre compte merci de cliquer sur le lien ci-après").' :<br />
											<br />
											<a href="'.$rparameters['server_url'].'/index.php?page=register&token='.$token.'">'.T_('Activer mon compte').'</a>	<br />
											<br />
											'.T_('Cordialement').'.';
											require('core/message.php');
											
											//message to display
											$msg_success=T_('Un mail vous a été envoyé pour valider votre compte');
										}
									}
            	                } else {$msg_error=T_("Vos mots de passes ne sont pas identiques");}
            	              } else {$msg_error=T_("Vous devez spécifier une adresse mail");}
        	             } else {$msg_error=T_("Vous devez spécifier un mot de passe");}
        	        } else {$msg_error=T_("Vous devez spécifier un mot de passe");}
        	    } else {$msg_error=T_("Vous devez spécifier un identifiant");}
        	} else {$msg_error=T_("Vous devez spécifier un nom");}
        } else {$msg_error=T_("Vous devez spécifier un prénom");}
	}
	
	//if user isn't connected then display authentication else display dashboard
	echo '
	<body>
		<div class="body-container" style=" background-image: linear-gradient(#6baace, #264783); background-attachment: fixed; background-repeat: no-repeat;" >
			<div class="main-container container bgc-transparent">
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
												<div class="d-lg-block col-md-8 offset-md-2 px-0">
													<h4 class="text-dark-tp4 border-b-1 brc-grey-l1 pb-1 text-130">
														<i class="fa fa-user text-success-m2 mr-1"></i>
														'.T_('Créer un compte').'
													</h4>
												</div>
												<form id="conn" method="post" action="" class="form-row mt-4"> 
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="firstname" name="firstname" autocomplete="off" value="'.$_POST['firstname'].'" />
															<i class="fa fa-user text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="firstname">'.T_('Prénom').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="lastname" name="lastname" autocomplete="off" value="'.$_POST['lastname'].'" />
															<i class="fa fa-user text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="lastname">'.T_('Nom').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="login" name="login" autocomplete="off" value="'.$_POST['login'].'" />
															<i class="fa fa-user text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="login">'.T_('Identifiant').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="password" class="form-control form-control-lg pr-4 shadow-none" id="password" name="password" autocomplete="off" value="'.$_POST['password'].'" />
															<i class="fa fa-lock text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="password">'.T_('Mot de passe').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="password" class="form-control form-control-lg pr-4 shadow-none" id="password2" name="password2" autocomplete="off" value="'.$_POST['password2'].'" />
															<i class="fa fa-lock text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="password2">'.T_('Confirmation mot de passe').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="mail" name="mail" autocomplete="off" value="'.$_POST['mail'].'" />
															<i class="fa fa-envelope text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="mail">'.T_('Mail').'</label>
														</div>
													</div>
													<div class="form-group col-md-8 offset-md-2">
														<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="phone" name="phone" autocomplete="off" value="'.$_POST['phone'].'" />
															<i class="fa fa-phone text-grey-m2 ml-n4"></i>
															<label class="floating-label text-grey-l1 text-100 ml-n3" for="phone">'.T_('Téléphone').'</label>
														</div>
													</div>';
													if($rparameters['user_advanced']) //display company field
													{
														echo '
														<div class="form-group col-md-8 offset-md-2">
															<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
																<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="company" name="company" autocomplete="off" value="'.$_POST['company'].'" />
																<i class="fa fa-building text-grey-m2 ml-n4"></i>
																<label class="floating-label text-grey-l1 text-100 ml-n3" for="company">'.T_('Société').'</label>
															</div>
														</div>
														';
													}
													if(extension_loaded('gd')) //display captcha validation
													{
														echo '
														<div class="form-group col-md-8 offset-md-2">
																<img class="mb-2" src="core/captcha.php" alt="captcha" style="cursor:pointer;">
															<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
															
																<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="captcha" name="captcha" autocomplete="off" value="'.$_POST['captcha'].'" />
																<i class="fa fa-image text-grey-m2 ml-n4"></i>
																<label class="floating-label text-grey-l1 text-100 ml-n3" for="captcha">'.T_('Captcha').'</label>
															</div>
														</div>
														';
													}
													echo '
													<div class="form-group col-md-6 offset-md-3">
														<button type="submit" onclick="submit()" name="submit" class="btn btn-success btn-block btn-md btn-bold mt-2 mb-1">
															<i class="fa fa-check"></i>
															'.T_('Valider').'
														</button>
													</div>
												</form>
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
		<script type="text/JavaScript">
			document.getElementById("firstname").focus();
		</script>
	';
} else {
	echo DisplayMessage('error',T_("La fonction d'enregistrement des utilisateurs est désactivée par votre administrateur"));
}
?>