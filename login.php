<?php
################################################################################
# @Name : login.php
# @Description : Login page for enter credentials and redirect to register page
# @Call : index.php
# @Parameters : 
# @Author : Flox
# @Create : 07/03/2010
# @Update : 10/09/2020
# @Version : 3.2.4 p5
################################################################################

//initialize variables 
if(!isset($state)) $state = ''; 
if(!isset($userid)) $userid = ''; 
if(!isset($techread)) $techread = '';
if(!isset($find_login)) $find_login = '';
if(!isset($profile)) $profile = '';
if(!isset($newpassword)) $newpassword = '';
if(!isset($salt)) $salt= '';
if(!isset($dcgen)) $dcgen= '';
if(!isset($ldap_type)) $ldap_type= '';
if(!isset($error)) $error= '';
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['login'])) $_SESSION['login'] = ''; 

if($_GET['state']=='') $_GET['state'] = '%';
	//actions on submit
	if(isset($_POST['submit']))
	{
		$login=(isset($_POST['login'])) ? $_POST['login'] : '';
		$pass=(isset($_POST['pass'])) ? $_POST['pass']  : '';
		
		$qry=$db->prepare("SELECT `id`,`login`,`password`,`salt`,`profile`,`disable` FROM `tusers`");
		$qry->execute();
		while ($row = $qry->fetch()) 
		{
			//uppercase login converter to compare
			$login=strtoupper($login);
			$db_login=strtoupper($row['login']);
			
			if($login && $pass && ($db_login == $login) && $row['password']!='' && $row['disable']==0) //check existing login
			{
				if(strlen($row['password'])>33) //hash detection
				{
					if(password_verify($pass, $row['password'])) {
						$find_login=$row['login'];
						$user_id=$row['id'];
						$profile=$row['profile'];
					}
				}elseif($row['password']==md5($row['salt'] . md5($pass))) //md5 has detect allow and convert hash, using for hash transition
				{ 
					$find_login=$row['login'];
					$user_id=$row['id'];
					$profile=$row['profile'];
					//update hash
					$hash=password_hash($pass, PASSWORD_DEFAULT);
					$qry2=$db->prepare("UPDATE `tusers` SET `password`=:password WHERE `id`=:id");
					$qry2->execute(array('password' => $hash,'id' => $row['id']));
				}
			}	
		}
		$qry->closeCursor();
		if($find_login) 
		{	
			$_SESSION['login']=$find_login;
			$_SESSION['user_id']=$user_id;
			//reset attempt counter
			if($rparameters['user_disable_attempt'])
			{
				$qry=$db->prepare("UPDATE `tusers` SET `auth_attempt`=0 WHERE `id`=:id");
				$qry->execute(array('id' => $_SESSION['user_id']));
			}
			//update last time connection
			$qry=$db->prepare("UPDATE `tusers` SET `last_login`=:last_login,`ip`=:ip WHERE `id`=:id");
			$qry->execute(array('last_login' => $datetime,'ip' => $_SERVER['REMOTE_ADDR'],'id' => $user_id));
			//display loading 
			echo '<i class="fa fa-spinner fa-spin text-info text-120"></i>&nbsp;Chargement...';
			//user pref default redirection state
			$qry = $db->prepare("SELECT * FROM `tusers` WHERE id=:id");
			$qry->execute(array('id' => $_SESSION['user_id']));
			$ruser=$qry->fetch();
			if(!$ruser['language']) {$ruser['language']='fr-FR';}
			$qry->closeCursor();
			if($ruser['default_ticket_state']) {$redirectstate=$ruser['default_ticket_state'];} else {$redirectstate=$rparameters['login_state'];}
			//check right side all
			$qry=$db->prepare("SELECT `side_all` FROM `trights` WHERE id=(SELECT `profile` FROM `tusers` WHERE id=:id);");
			$qry->execute(array('id' => $_SESSION['user_id']));
			$rright=$qry->fetch();
			$qry->closeCursor();
			//select page to redirect
			if($_GET['id'] ||isset($_GET['action-id']) || isset($_GET['open-modify-id'])) { //email case
			    $www='./index.php?'.http_build_query ($_GET);
			} else { //parameters case
				if($redirectstate=='meta_all')
				{
					$www="./index.php?page=dashboard&userid=%25&state=meta";
				}elseif($redirectstate=='all' && $rright['side_all'])
				{
					$www="./index.php?page=dashboard&userid=%25&state=%25";
				}elseif($redirectstate=='all' && !$rright['side_all'])
				{
					$www="./index.php?page=dashboard&userid=$user_id&state=%25";
				} else {
					$www="./index.php?page=dashboard&userid=$user_id&state=$redirectstate";
				}
			}
			//web redirection
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='$www'
						}
						setTimeout('redirect()');
						-->
					</SCRIPT>";
		}
		elseif($rparameters['ldap'] && $rparameters['ldap_auth']) // if gestsup user is not found and LDAP is enable, search in LDAP
		{
			//LDAP connect
			if($rparameters['ldap_port']==636) {$hostname='ldaps://'.$rparameters['ldap_server'];} else {$hostname=$rparameters['ldap_server'];}
			$ldap=ldap_connect($hostname,$rparameters['ldap_port']) or die("Unable to connect to LDAP server.");
			ldap_start_tls($ldap);
			ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			$domain=$rparameters['ldap_domain'];
			if($rparameters['ldap_type']==0 || $rparameters['ldap_type']==3) //AD and Samba4 
			{
				//if UPN not add domain suffix
				if($rparameters['ldap_login_field']=='UserPrincipalName')
				{
					$ldapbind = @ldap_bind($ldap, $login, $pass);
				} else { 
					$ldapbind = @ldap_bind($ldap, "$login@$domain", $pass);
				}
			} else { //Open LDAP
				//generate DC chain from domain parameter
				$dcpart=explode(".",$domain);
				$i=0;
				while($i<count($dcpart)) {
					$dcgen="$dcgen,dc=$dcpart[$i]";
					$i++;
				}
				if(preg_match('/gs_en/',$rparameters['ldap_password'])) {$rparameters['ldap_password']=gs_crypt($rparameters['ldap_password'], 'd' , $rparameters['server_private_key']);}
				
				$dn='uid='.$login.','.$rparameters['ldap_url'].$dcgen;
				$ldapbind = @ldap_bind($ldap, $dn, $pass);
				if(!$ldapbind)
				{
					//if user not in base dn search it in sub ou to get user dn
					$basedn=$rparameters['ldap_url'].$dcgen;
					$filter="(uid=$login)";
					$res = ldap_search($ldap, $basedn, $filter);
					$first = ldap_first_entry($ldap, $res);
					$dn = ldap_get_dn($ldap, $first);
					$ldapbind = @ldap_bind($ldap, $dn, $pass);
					if(!$ldapbind)
					{
						if($rparameters['debug']) {echo DisplayMessage('error',"Unable to bind on OpenLDAP Server (dn: $dn filter: cn=$login basedn: $basedn)");}
						$ldapbind=0;
					}
				}
			}

			if($ldapbind && $pass!='') 
			{
				$_SESSION['login'] = $login;
				$qry = $db->prepare("SELECT `id`,`password` FROM `tusers` WHERE `login`=:login AND `disable`='0'");
				$qry->execute(array('login' => $login));
				$r=$qry->fetch();
				$qry->closeCursor();
				$_SESSION['user_id'] = $r['id'];
				if($r['id']=='')
				{
					//if error with login or password 
					echo DisplayMessage('error', 'Utilisateur non synchronisé dans base utilisateurs GestSup');
					session_destroy();
					//web redirection to login page
					echo "<SCRIPT LANGUAGE='JavaScript'>
							<!--
							function redirect()
							{
							window.location='./index.php'
							}
							setTimeout('redirect()',$rparameters[time_display_msg]+1000);
							-->
						</SCRIPT>";
				} else {
					//update last time connection
					$qry=$db->prepare("UPDATE `tusers` SET `last_login`=:last_login,`ip`=:ip WHERE `id`=:id");
					$qry->execute(array('last_login' => $datetime,'ip' => $_SERVER['REMOTE_ADDR'],'id' => $r['id']));
					
					//update GS db pwd
					$newpassword = password_hash($pass, PASSWORD_DEFAULT);
					//update password
					$qry=$db->prepare("UPDATE `tusers` SET `password`=:password WHERE `id`=:id");
					$qry->execute(array('password' => $newpassword,	'id' => $r['id']));
					
					//user pref default redirection state
					$qry=$db->prepare("SELECT * FROM `tusers` WHERE `id`=:id");
					$qry->execute(array('id' => $_SESSION['user_id']));
					$ruser=$qry->fetch();
					$qry->closeCursor();

					//modify redirection state to personal user state if it's define else using admin parameter
					if($ruser['default_ticket_state']) {$redirectstate=$ruser['default_ticket_state'];} else {$redirectstate=$rparameters['login_state'];}
			
					//select page to redirect for email link case
					if($_GET['id']) {
						$www = './index.php?page=ticket&id='.$_GET['id'].'&userid='.$_SESSION['user_id'];
					} else {
						if($redirectstate=='meta_all')
						{
							//URL
							$www = "./index.php?page=dashboard&userid=%&state=meta";
						} elseif($redirectstate=='all')
						{
							//URL
							$www = "./index.php?page=dashboard&userid=%25&state=%25";
						} else {
							//URL
							$www = './index.php?page=dashboard&userid='.$_SESSION['user_id'].'&state='.$redirectstate;
						}
					}
					//web redirection
					echo "<SCRIPT LANGUAGE='JavaScript'>
							<!--
							function redirect()
							{
							window.location='$www'
							}
							setTimeout('redirect()');
							-->
						</SCRIPT>";
				}
			} else {
				// if error with login or password 
				$error=T_('Identifiant ou mot de passe invalide');
				session_destroy();
				//web redirection to login page
				echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='./index.php'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]+1500);
						-->
					</SCRIPT>";
			}
		}
		else
		{
			//secure check user attempt
			if($rparameters['user_disable_attempt'] && $_POST['login'] && $_POST['pass'])
			{
				//check if user exist
				$qry=$db->prepare("SELECT `id`,`auth_attempt`,`disable` FROM `tusers` WHERE login=:login");
				$qry->execute(array('login' => $_POST['login']));
				$row=$qry->fetch();
				$qry->closeCursor();
				if($row)
				{
					$attempt=$row['auth_attempt']+1;
					$qry=$db->prepare("UPDATE `tusers` SET `auth_attempt`=:auth_attempt WHERE `id`=:id");
					$qry->execute(array('auth_attempt' => $attempt,'id' => $row['id']));
					$attempt_remaing=$rparameters['user_disable_attempt_number']-$attempt;
					if($attempt_remaing>0)
					{
						$attempt_remaing=T_('Il reste').' '.$attempt_remaing.' '.T_('tentatives avant la désactivation de votre compte');
					} else {
						if($row['disable'])
						{
							$attempt_remaing=T_('Votre compte est désactivé, contacter votre administrateur');
						} else {
							$qry=$db->prepare("UPDATE `tusers` SET `disable`=1 WHERE `id`=:id");
							$qry->execute(array('id' => $row['id']));
							$attempt_remaing=T_('Votre compte a été désactivé, suite à').' '.$rparameters['user_disable_attempt_number'].' '.T_('tentatives de connexion infructueuses');
							if($rparameters['log'])
							{
								logit('security', 'User '.$_POST['login'].' disable after authentication failures',$row['id']);
							}
						}
					}
				} else {$attempt_remaing='';}
			} else {$attempt_remaing='';}
			// if error with login or password 
			$error=T_('Identifiant ou mot de passe invalide');
			if($attempt_remaing) {$error.='<br />'.$attempt_remaing;}
			session_destroy();
			//web redirection to login page
			echo "<SCRIPT LANGUAGE='JavaScript'>
						<!--
						function redirect()
						{
						window.location='./index.php'
						}
						setTimeout('redirect()',$rparameters[time_display_msg]+1500);
						-->
					</SCRIPT>";
		}
	}; 
	// if user isn't connected then display authentication else display dashboard
	if($_SESSION['login']=='') 
	{
		echo '
		<body>
		
			<div class="body-container" style=" background-image: linear-gradient(#6baace, #264783); background-attachment: fixed; background-repeat: no-repeat;" >
				<div class="main-container container bgc-transparent">
					<div role="main" class="main-content">
						<div class="justify-content-center pb-2">
						
							';
								if($error){echo DisplayMessage('error',$error);}
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
										<div class="col-12 bgc-white px-0 pt-5 pb-4">
											<div class="" data-swipe="center">
												<div class="active show px-3 px-lg-0 pb-0" id="id-tab-login">
													<div class="d-lg-block col-md-8 offset-md-2 mt-lg-4 px-0">
														<h4 class="text-dark-tp4 border-b-1 brc-grey-l1 pb-1 text-130">
															<i class="fa fa-lock text-success-m2 mr-1"></i>
															'.T_('Identification').'
														</h4>
													</div>
													<form id="conn" method="post" action="" class="form-row mt-4"> 
														<div class="form-group col-md-8 offset-md-2">
															<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
																<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="login" name="login" autocomplete="off" />
																<i class="fa fa-user text-grey-m2 ml-n4"></i>
																<label class="floating-label text-grey-l1 text-100 ml-n3" for="login">'.T_("Nom d'utilisateur").'</label>
															</div>
														</div>
														<div class="form-group col-md-8 offset-md-2 mt-2 mt-md-1">
															<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
																<input type="password" class="form-control form-control-lg pr-4 shadow-none" id="pass" name="pass" autocomplete="off" />
																<i class="fa fa-key text-grey-m2 ml-n4"></i>
																<label class="floating-label text-grey-l1 text-100 ml-n3" for="pass">'.T_('Mot de passe').'</label>
															</div>
														</div>
														';
														if($rparameters['user_forgot_pwd'] && !$rparameters['ldap'] && $rparameters['mail'])
														{
															echo '
															<div class="form-group col-md-8 offset-md-2 mt-0 pt-0 text-right">
																<a href="index.php?page=forgot_pwd" class="text-primary-m2 text-95">
																	'.T_('Mot de passe oublié').' ?
																</a>
															</div>
															';
														}
														echo '
														
														<div class="form-group col-md-6 offset-md-3">
															<button type="submit" onclick="submit()" name="submit" class="btn btn-primary btn-block px-4 btn-bold mt-2 mb-4">
																<i class="fa fa-sign-in-alt"></i>
																'.T_('Connexion').'
															</button>
														</div>
													</form>
													<div class="d-lg-block col-md-8 offset-md-2 mt-lg-2 px-0">
														<h4 class="text-dark-tp4 border-b-1 brc-grey-l1 pb-1 text-130">
															<i class="fa fa-user text-success-m2 mr-1"></i>
															'.T_('Vous êtes invité ?').'
														</h4>
													</div>
													<form method="get" action="index.php" class="form-row mt-4">
														<div class="form-group col-md-8 offset-md-2">
															<div class="d-flex align-items-center input-floating-label text-blue-m1 brc-blue-m2">
																<input type="text" class="form-control form-control-lg pr-4 shadow-none" id="token" name="token" />
																<i class="fa fa-user text-grey-m2 ml-n4"></i>
																<label class="floating-label text-grey-l1 text-100 ml-n3" for="token">'.T_("Jeton Invité").'</label>
															</div>
														</div>
														<div class="form-group col-md-4 offset-md-4">
															<button type="submit" class="btn btn-primary btn-block px-4 btn-bold mt-2 mb-2">
																<i class="fa fa-door-open"></i>
																'.T_('Valider').'
															</button>
														</div>
													</form>
													';
													if($rparameters['user_register'])
													{
														echo '
														<div class="form-row">
															<div class="col-12 col-md-6 offset-md-3 d-flex flex-column align-items-center justify-content-center">
																<hr class="brc-default-m4 mt-0 mb-2 w-100" />
																<div class="p-0 px-md-2 text-dark-tp3 my-3">
																	<a class="text-success-m2 text-600 mx-1" href="index.php?page=register">
																		'.T_("S'enregistrer").'
																	</a>
																</div>
															</div>
														</div>
														';
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
		<script type="text/JavaScript">
			document.getElementById("login").focus();
		</script>
		';
	}
?>
