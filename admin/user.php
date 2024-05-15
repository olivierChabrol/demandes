<?php
################################################################################
# @Name : user.php 
# @Description : admin user
# @Call : admin.php
# @Author : Flox
# @Create : 12/01/2011
# @Update : 08/09/2020
# @Version : 3.2.4 p1
################################################################################

//initialize variables 
if(!isset($_SERVER['QUERY_URI'])) $_SERVER['QUERY_URI'] = '';
if(!isset($_POST['modify'])) $_POST['modify'] = '';
if(!isset($_POST['add'])) $_POST['add'] = '';
if(!isset($_POST['cancel'])) $_POST['cancel'] = '';
if(!isset($_POST['addview'])) $_POST['addview'] = '';
if(!isset($_POST['profil'])) $_POST['profil'] = '';
if(!isset($_POST['name'])) $_POST['name'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';
if(!isset($_POST['address1'])) $_POST['address1'] = '';
if(!isset($_POST['address2'])) $_POST['address2'] = '';
if(!isset($_POST['zip'])) $_POST['zip'] = '';
if(!isset($_POST['city'])) $_POST['city'] = '';
if(!isset($_POST['custom1'])) $_POST['custom1'] = '';
if(!isset($_POST['custom2'])) $_POST['custom2'] = '';
if(!isset($_POST['password'])) $_POST['password'] = '';
if(!isset($_POST['password2'])) $_POST['password2'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '%';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['firstname'])) $_POST['firstname'] = '';
if(!isset($_POST['lastname'])) $_POST['lastname'] = '';
if(!isset($_POST['viewname'])) $_POST['viewname'] = '';
if(!isset($_POST['service'])) $_POST['service'] = '';
if(!isset($_POST['agency'])) $_POST['agency'] = '';
if(!isset($_POST['function'])) $_POST['function'] = '';
if(!isset($_POST['limit_ticket_number'])) $_POST['limit_ticket_number'] = '';
if(!isset($_POST['limit_ticket_days'])) $_POST['limit_ticket_days'] = '';
if(!isset($_POST['limit_ticket_date_start'])) $_POST['limit_ticket_date_start'] = '';
if(!isset($_POST['mail'])) $_POST['mail'] = '';
if(!isset($_POST['login'])) $_POST['login'] = '';
if(!isset($_POST['phone'])) $_POST['phone'] = '';
if(!isset($_POST['mobile'])) $_POST['mobile'] = '';
if(!isset($_POST['fax'])) $_POST['fax'] = '';
if(!isset($_POST['default_ticket_state'])) $_POST['default_ticket_state'] = '';
if(!isset($_POST['dashboard_ticket_order'])) $_POST['dashboard_ticket_order'] = '';
if(!isset($_POST['disable_user'])) $_POST['disable_user'] = '';
if(!isset($user1['company'])) $user1['company'] = '';
if(!isset($password)) $password = '';
if(!isset($password2)) $password2 = '';
if(!isset($addeview)) $addview = '';
if(!isset($category)) $category = '%';
if(!isset($maxline)) $maxline = '';
if(!isset($_POST['chgpwd'])) $_POST['chgpwd'] = '';
$canEditProfile = false;

//defaults values
if(!$_GET['tab']) $_GET['tab'] = 'infos';
if($_GET['disable']=='') $_GET['disable'] = '0';
if($_GET['cursor']=='') $_GET['cursor'] = '0';
if($_GET['order']=='') $_GET['order'] = 'lastname';
if($_GET['way']=='') $_GET['way'] = 'ASC';
if($maxline=='') $maxline = $rparameters['maxline'];
if($_POST['userkeywords']=='') $userkeywords='%'; else $userkeywords=$_POST['userkeywords'];

$_GET['delete_assoc_service']=strip_tags($_GET['delete_assoc_service']);
$_GET['delete_assoc_agency']=strip_tags($_GET['delete_assoc_agency']);
$_GET['userid']=strip_tags($_GET['userid']);
$_GET['viewid']=strip_tags($_GET['viewid']);
$_GET['disable']=strip_tags($_GET['disable']);
$_GET['attachmentdelete']=strip_tags($_GET['attachmentdelete']);
$_GET['profileid']=strip_tags($_GET['profileid']);
$db_order=strip_tags($db->quote($_GET['order']));
$db_order=str_replace("'","",$db_order);
if($_GET['way']=='ASC' || $_GET['way']=='DESC') {$db_way=$_GET['way'];} else {$db_way='DESC';}
if(is_numeric($_GET['cursor'])) {$db_cursor=$_GET['cursor'];} else {$db_cursor=0;}

//special char rename
if($_POST['viewname']){$_POST['viewname']=strip_tags($_POST['viewname']);}

//secure string
$_POST['firstname']=strip_tags($_POST['firstname']);
$_POST['lastname']=strip_tags($_POST['lastname']);
$_POST['address1']=strip_tags($_POST['address1']);
$_POST['address2']=strip_tags($_POST['address2']);
$_POST['login']=strip_tags($_POST['login']);
$_POST['mail']=strip_tags($_POST['mail']);
$_POST['phone']=strip_tags($_POST['phone']);
$_POST['mobile']=strip_tags($_POST['mobile']);
$_POST['fax']=strip_tags($_POST['fax']);
$_POST['city']=strip_tags($_POST['city']);
$_POST['zip']=strip_tags($_POST['zip']);
$_POST['custom1']=strip_tags($_POST['custom1']);
$_POST['custom2']=strip_tags($_POST['custom2']);
$_POST['function']=strip_tags($_POST['function']);

if($_POST['disable_user'] && !$rright['admin']) {$_POST['disable_user']=0;}

//delete association user > service
if($_GET['delete_assoc_service'] && $rright['admin'])
{
	$qry=$db->prepare("DELETE FROM `tusers_services` WHERE id=:id");
	$qry->execute(array('id' => $_GET['delete_assoc_service']));
}

//delete assoc user > agency
if($_GET['delete_assoc_agency'] && $rright['admin'])
{
	$qry=$db->prepare("DELETE FROM `tusers_agencies` WHERE id=:id");
	$qry->execute(array('id' => $_GET['delete_assoc_agency']));
}

//modify case
if($_POST['modify'])
{
	//case user sync from AD without pwd and not already connected
	if($rparameters['ldap'] && $rparameters['ldap_auth'] && $_POST['password']=='')
	{
		$qry = $db->prepare("SELECT `password`,`salt` FROM `tusers` WHERE id=:id");
		$qry->execute(array('id' => $_GET['userid']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if($row['password']=='')
		{
			$pwd = substr(md5(uniqid(rand(), true)), 0, 5); //generate a random password
			$hash = password_hash($pwd, PASSWORD_DEFAULT);
			//update pwd
			$qry=$db->prepare("UPDATE `tusers` SET `password`=:password WHERE `id`=:id");
			$qry->execute(array('password' => $hash,'id' => $_GET['userid']));
			$_POST['password']=$pwd;
		}
	}
	$error=0;
	if($_POST['mail']) //mail control
	{
		if(!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {$error=T_("L'adresse mail est incorrecte");}
	}
	if($_POST['login']) //existing account control
	{
		$qry=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE login=:login AND disable=0 AND id!=:id");
		$qry->execute(array('login' => $_POST['login'],'id' => $_GET['userid']));
		$row=$qry->fetch();
		$qry->closeCursor();
		if($row) {$error=T_('Un autre compte utilise déjà cet identifiant ('.$row['lastname'].' '.$row['firstname'].')');}
	}
	if($_POST['password']!=$_POST['password2']) {$error=T_('Les mots de passes ne sont pas identiques');}
	//password policy
	if($rparameters['user_password_policy'] && $_POST['password'])
	{
		if(strlen($_POST['password'])<$rparameters['user_password_policy_min_lenght'])
		{
			$error=T_('Le mot de passe doit faire').' '.$rparameters['user_password_policy_min_lenght'].' '.T_('caractères minimum');
		}elseif($rparameters['user_password_policy_special_char'] && !preg_match('/[^a-zA-Z\d]/', $_POST['password']))
		{
			$error=T_('Le mot de passe doit contenir un caractère spécial');
		}elseif($rparameters['user_password_policy_min_maj'] && (!preg_match('/[A-Z]/', $_POST['password']) || !preg_match('/[a-z]/', $_POST['password'])))
		{
			$error=T_('Le mot de passe doit posséder au moins une lettre majuscule et une minuscule');
		}
	}
	
	if(!$error)
	{
		if($_POST['password'])
		{
			$hash=password_hash($_POST['password'], PASSWORD_DEFAULT);
			$last_pwd_chg=date('Y-m-d');
		} else {
			$qry=$db->prepare("SELECT `password`,`last_pwd_chg` FROM `tusers` WHERE id=:id AND disable=0 ");
			$qry->execute(array('id' => $_GET['userid']));
			$row=$qry->fetch();
			$qry->closeCursor();
			if(empty($row['password'])) {$row['password']='';}
			if(empty($row['last_pwd_chg'])) {$row['last_pwd_chg']='';}
			$hash=$row['password'];
			$last_pwd_chg=$row['last_pwd_chg'];
		}

		$qry=$db->prepare("
		UPDATE tusers SET
		`firstname`=:firstname,
		`lastname`=:lastname,
		`password`=:password,
		`mail`=:mail,
		`phone`=:phone,
		`mobile`=:mobile,
		`login`=:login,
		`fax`=:fax,
		`function`=:function,
		`company`=:company,
		`address1`=:address1,
		`address2`=:address2,
		`zip`=:zip,
		`city`=:city,
		`custom1`=:custom1,
		`custom2`=:custom2,
		`limit_ticket_number`=:limit_ticket_number,
		`limit_ticket_days`=:limit_ticket_days,
		`limit_ticket_date_start`=:limit_ticket_date_start,
		`skin`=:skin,
		`dashboard_ticket_order`=:dashboard_ticket_order,
		`default_ticket_state`=:default_ticket_state,
		`chgpwd`=:chgpwd,
		`last_pwd_chg`=:last_pwd_chg,
		`language`=:language,
		`disable`=:disable
		WHERE `id`=:id
		");
		$qry->execute(array(
			'firstname' => $_POST['firstname'],
			'lastname' => $_POST['lastname'],
			'password' => $hash,
			'mail' => $_POST['mail'],
			'phone' => $_POST['phone'],
			'mobile' => $_POST['mobile'],
			'login' => $_POST['login'],
			'fax' => $_POST['fax'],
			'function' => $_POST['function'],
			'company' => 1,//$_POST['company'],
			'address1' => $_POST['address1'],
			'address2' => $_POST['address2'],
			'zip' => $_POST['zip'],
			'city' => $_POST['city'],
			'custom1' => $_POST['custom1'],
			'custom2' => $_POST['custom2'],
			'limit_ticket_number' => $_POST['limit_ticket_number'],
			'limit_ticket_days' => $_POST['limit_ticket_days'],
			'limit_ticket_date_start' => $_POST['limit_ticket_date_start'],
			'skin' => $_POST['skin'],
			'dashboard_ticket_order' => $_POST['dashboard_ticket_order'],
			'default_ticket_state' => $_POST['default_ticket_state'],
			'chgpwd' => $_POST['chgpwd'],
			'last_pwd_chg' => $last_pwd_chg,
			'language' => $_POST['language'],
			'disable' => $_POST['disable_user'],
			'id' => $_GET['userid']
			));
		$qry->closeCursor();
		
		//log
		if($rparameters['log'])
		{
			require_once('core/functions.php');
			if($_POST['profile']==4)
			{
				if($_POST['password']){logit('security', 'Password change for and admin account '.$_POST['login'],$_SESSION['user_id']);}
				
				//get current profil of updated user
				$qry=$db->prepare("SELECT `profile` FROM `tusers` WHERE id=:id");
				$qry->execute(array('id' => $_GET['userid']));
				$user_profile=$qry->fetch();
				$qry->closeCursor();
				
				if($user_profile['profile']!=4){logit('security', 'Profile change to admin for account '.$_POST['login'],$_SESSION['user_id']);}
			}
			if($_POST['disable_user']){logit('security', 'User '.$_POST['login'].' disabled',$_SESSION['user_id']);}
		}
		
		//special case profil update check admin right
		if($rright['admin']!=0)
		{
			$qry=$db->prepare("UPDATE tusers SET profile=:profile WHERE id=:id ");
			$qry->execute(array('profile' => $_POST['profile'],'id' => $_GET['userid']));
			$qry->closeCursor();
		}
		
		//add service association to this user
		if($_POST['service']) {
			$qry=$db->prepare("INSERT INTO `tusers_services` (`user_id`,`service_id`) VALUES (:user_id,:service_id)");
			$qry->execute(array('user_id' => $_GET['userid'],'service_id' => $_POST['service']));
		}
		//add agency association to this user
		if($rparameters['user_agency'])
		{
			if($_POST['agency']) {
				$qry=$db->prepare("INSERT INTO `tusers_agencies` (`user_id`,`agency_id`) VALUES (:user_id,:agency_id)");
				$qry->execute(array('user_id' => $_GET['userid'],'agency_id' => $_POST['agency']));
			}
		}
		if($_POST['viewname'])
		{
			$qry=$db->prepare("INSERT INTO `tviews` (`uid`,`name`,`category`,`subcat`) VALUES (:uid,:name,:category,:subcat)");
			$qry->execute(array('uid' => $_GET['userid'],'name' => $_POST['viewname'],'category' => $_POST['category'],'subcat' => $_POST['subcat']));
		}
		//tech attachement insert
		if($_POST['attachment'])
		{
			$qry=$db->prepare("INSERT INTO `tusers_tech` (`user`,`tech`) VALUES (:user,:tech)");
			$qry->execute(array('user' => $_POST['attachment'],'tech' => $_GET['userid']));
		}
	}
	//redirect
	$url=$_SERVER['QUERY_URI'];
	$url=preg_replace('/%/','%25',$url);
	if($error)
	{
		echo '
			<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
				<div class="flex-grow-1">
					<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
					<strong class="text-danger">'.T_('Erreur').' : '.$error.'.</strong>
				</div>
				<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
				</button>
			</div>
		';
		echo "
		<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
				window.location='$url'	
			}
			setTimeout('redirect()',$rparameters[time_display_msg]+1000);
			-->
		</SCRIPT>";
	} else {
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$url.'");
		// -->
		</script>';
	}
}

if($_POST['add'] && $rright['admin'])
{
	$error=0;
	if(!$_POST['password']) {$error=T_('Vous devez spécifier un mot de passe');}
	if(!$_POST['login']) {$error=T_('Vous devez spécifier un identifiant');}
	if($_POST['password']!=$_POST['password2']) {$error=T_('Les mots de passe ne sont pas identiques');}
	if($_POST['mail'])
	{
		//email check
		if($_POST['mail'])
		{
			if(!strpos($_POST['mail'],'.')) {$error=T_("l'adresse mail doit posséder un point");}
			if(strpos($_POST['mail'],'@'))
			{
				$mail_domain=explode('@',$_POST['mail']);
				if(!strpos($mail_domain[1],'.')) {$error=T_("Le domaine de l'adresse mail doit posséder un point");}
			} else {$error=T_("l'adresse mail doit posséder un arobase");}
		}
	}
	if($_POST['login'])
	{
		$qry=$db->prepare("SELECT `id` FROM `tusers` WHERE login=:login AND disable='0'");
		$qry->execute(array('login' => $_POST['login']));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($row) {$error=T_('Un autre compte utilise déjà cet identifiant');}
	}
	//password policy
	if($rparameters['user_password_policy'] && $_POST['password'])
	{
		if(strlen($_POST['password'])<$rparameters['user_password_policy_min_lenght'])
		{
			$error=T_('Le mot de passe doit faire').' '.$rparameters['user_password_policy_min_lenght'].' '.T_('caractères minimum');
		}elseif($rparameters['user_password_policy_special_char'] && !preg_match('/[^a-zA-Z\d]/', $_POST['password']))
		{
			$error=T_('Le mot de passe doit contenir un caractère spécial');
		}elseif($rparameters['user_password_policy_min_maj'] && (!preg_match('/[A-Z]/', $_POST['password']) || !preg_match('/[a-z]/', $_POST['password'])))
		{
			$error=T_('Le mot de passe doit au moins une lettre majuscule et une minuscule');
		}
	}
	
	if(!$error)
	{
		//hash password
		$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
		
		$qry=$db->prepare("
		INSERT INTO tusers (
		`firstname`,
		`lastname`,
		`password`,
		`mail`,
		`phone`,
		`mobile`,
		`fax`,
		`company`,
		`address1`,
		`address2`,
		`zip`,
		`city`,
		`custom1`,
		`custom2`,
		`profile`,
		`login`,
		`chgpwd`,
		`last_pwd_chg`,
		`skin`,
		`function`
		) VALUES (
		:firstname,
		:lastname,
		:password,
		:mail,
		:phone,
		:mobile,
		:fax,
		:company,
		:address1,
		:address2,
		:zip,
		:city,
		:custom1,
		:custom2,
		:profile,
		:login,
		:chgpwd,
		:last_pwd_chg,
		:skin,
		:function
		)");
		$qry->execute(array(
			'firstname' => $_POST['firstname'],
			'lastname' => $_POST['lastname'],
			'password' => $hash,
			'mail' => $_POST['mail'],
			'phone' => $_POST['phone'],
			'mobile' => $_POST['mobile'],
			'fax' => $_POST['fax'],
			'company' => $_POST['company'],
			'address1' => $_POST['address1'],
			'address2' => $_POST['address2'],
			'zip' => $_POST['zip'],
			'city' => $_POST['city'],
			'custom1' => $_POST['custom1'],
			'custom2' => $_POST['custom2'],
			'profile' => $_POST['profile'],
			'login' => $_POST['login'],
			'chgpwd' => $_POST['chgpwd'],
			'last_pwd_chg' => date('Y-m-d'),
			'skin' => $_POST['skin'],
			'function' => $_POST['function']
			));
		$last_user_id=$db->lastInsertId();
		//if post service insert new assoc
		if($_POST['service'])
		{
			$qry=$db->prepare("INSERT INTO `tusers_services` (`user_id`,`service_id`) VALUES (:user_id,:service_id)");
			$qry->execute(array('user_id' => $last_user_id,'service_id' => $_POST['service']));
		}
		if($rparameters['user_agency'])
		{
			if($_POST['agency']) //if post agency insert new assoc
			{
				$qry=$db->prepare("INSERT INTO `tusers_agencies` (`user_id`,`agency_id`) VALUES (:user_id,:agency_id)");
				$qry->execute(array('user_id' => $last_user_id,'agency_id' => $_POST['agency']));
			}
		}
		if($rparameters['log'] && $_POST['profile']==4)
		{
			require_once('core/functions.php');
			logit('security', 'Admin account has been added '.$_POST['login'],$_SESSION['user_id']);
		}
		//redirect
		$www = "./index.php?page=admin&subpage=user";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
	} else {
		echo '
			<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
				<div class="flex-grow-1">
					<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
					<strong class="text-danger">'.T_('Erreur').' : '.$error.'.</strong>
				</div>
				<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
				</button>
			</div>
		';
	}
}
//cancel
if($_POST['cancel'])
{
	//redirect
	if($rright['admin'] && $_GET['subpage']=='user')
	{
		$www = "./index.php?page=admin&subpage=user";
	} else {
		$www = "./index.php?page=dashboard&userid=$uid&state=%25";
	}
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
//view Part
if($_GET['deleteview']=="1" && $rright['side_view']!=0)
{
	$qry=$db->prepare("DELETE FROM `tviews` WHERE id=:id");
	$qry->execute(array('id' => $_GET['viewid']));
	//redirect
	$url = "./index.php?page=admin/user&subpage=user&action=edit&tab=parameters&userid=$_GET[userid]";
	$url=preg_replace('/%/','%25',$url);
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$url.'");
	// -->
	</script>';
}
//delete tech attachement
if($_GET['attachmentdelete'] && ($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4))
{
	$qry=$db->prepare("DELETE FROM `tusers_tech` WHERE id=:id");
	$qry->execute(array('id' => $_GET['attachmentdelete']));
}
//display head page
if($rright['admin_user_profile'])
{
	if(!$_GET['ldap'])
	{
		//count users
		$qry = $db->prepare("SELECT COUNT(*) FROM `tusers` WHERE disable='0'");
		$qry->execute();
		$r1=$qry->fetch();
		$qry->closeCursor();
		
		$qry = $db->prepare("SELECT COUNT(*) FROM `tusers` WHERE disable='1'");
		$qry->execute();
		$r2=$qry->fetch();
		$qry->closeCursor();
		
		echo '
		<div class="page-header position-relative">
			<h1 class="page-title text-primary-m2" >
				<i class="fa fa-user"></i>  '.T_('Gestion des utilisateurs').'
				<small class="page-info text-secondary-d2">
					<i class="fa fa-angle-double-right"></i>
					&nbsp;'.T_('Nombre').' : '.$r1[0].' '.T_('Activés et').' '.$r2[0].' '.T_('Désactivés').'
				</small>
			</h1>
		</div>';
	}
}
/////////////////////////////////////////////////// display edit user page  /////////////////////////////////////////////////////////////
if(($_GET['action']=='edit') && (($_SESSION['user_id']==$_GET['userid']) || ($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4))) 
{
	//get user data
	$qry = $db->prepare("SELECT t1.*, t2.firstname as firstname_guardianship, t2.lastname as lastname_guardianship FROM `tusers` t1 LEFT JOIN `tusers` as t2 ON t2.id = t1.custom1 WHERE t1.id=:id");
	$qry->execute(array('id' => $_GET['userid']));
	$user1=$qry->fetch();
	$qry->closeCursor();

	//first letters
	$firstname_letter=strtoupper(substr($user1['firstname'],0,1));
	if(empty($firstname_letter)) {$firstname_letter='';}
	$lastname_letter=strtoupper(substr($user1['lastname'],0,1));
	if(empty($lastname_letter)) {$lastname_letter='';}

	if($user1['profile']==0)$lastname_letter_color='bgc-grey';
	if($user1['profile']==1)$lastname_letter_color='bgc-green';
	if($user1['profile']==2)$lastname_letter_color='bgc-primary';
	if($user1['profile']==3)$lastname_letter_color='bgc-orange';
	if($user1['profile']==4)$lastname_letter_color='bgc-dark';
	
	//display edit form.
	echo '
		<div class="col-12 cards-container">
			<div class="card bcard shadow">
				<div class="card-header">
					<h5 class="card-title">
						<span style="padding-top:5px;" class="d-inline-block text-center mr-2 pt-2 w-5 h-5 radius-round '.$lastname_letter_color.' text-white font-bolder text-90">'.$firstname_letter.$lastname_letter.'</span>
						'.$user1['firstname'].' '.$user1['lastname'].'
					</h5>
					<span class="card-toolbar">
						<button value="modify" id="modify" name="modify" type="submit" form="1" class="btn btn-success ml-1">
							<i title="'.T_('Enregistrer').'" class="fa fa-save text-130"></i>
						</button>
					</span>
				</div>
				<div class="card-body">
					<div class="card-main no-padding">
						<form id="1" name="form" method="POST" action="" class="form-horizontal">
                                <fieldset>
                                <div class="col-sm-12">
                            		<div class="tabs-above">
                            			<ul class="nav nav-tabs nav-justified" id="myTab">
                            				<li class="nav-item mr-1px">
                            					<a class="nav-link '; if($_GET['tab']=='infos') {echo 'active';} echo '" href="./index.php?page=admin/user&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=infos">
                            						<i class="fa fa-info-circle text-primary-m2"></i>
                            						'.T_('Informations').'
                            					</a>
                            				</li>
                            				<li class="nav-item mr-1px">
                            					<a class="nav-link '; if($_GET['tab']=='parameters') {echo 'active';} echo '"" href="./index.php?page=admin/user&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=parameters">
                            						<i class="fa fa-cog text-warning"></i>
                            						'.T_('Paramètres').'
                            					</a>
                            				</li>';
                                            //display attachment tab if it's not a technician or admin
                            				if((($user1['profile']==0) || ($user1['profile']==4)) && ($_SESSION['profile_id']!=1) && ($_SESSION['profile_id']!=2) && ($_SESSION['profile_id']!=3))
                            				{
												echo '
												<li class="nav-item mr-1px">
                                					<a class="nav-link '; if($_GET['tab']=='attachment') {echo 'active';} echo '" href="./index.php?page=admin/user&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=attachment">
                                						<i class="fa fa-user text-success"></i>
                                						'.T_('Rattachement à des utilisateurs').'
                                						<i title="'.T_("Permet d'attribuer automatiquement un technicien lors de la création de ticket par un utilisateur").'" class="fa fa-question-circle text-primary-m2"></i>
                                					</a>
                                				</li>';
                            				}
                            				echo'
                            			</ul>
                            			<div class="tab-content">
                            			    <div id="attachment" class="tab-pane'; if($_GET['tab']=='attachment' || $_GET['tab']=='') echo 'active'; echo '">
                                                <label class="control-label bolder text-primary-m2" for="attachment">'.T_('Associer des utilisateurs à ce technicien').' :</label>
                                                <div class="space-4"></div>
                                                <select style="width:auto;" class="form-control form-control-sm d-inline-block" name="attachment">
                                                    ';
                                                    //display list of user for attachment
													$qry = $db->prepare("SELECT tusers.* FROM `tusers` WHERE tusers.profile!=0 AND tusers.profile!='4' AND tusers.disable='0' AND tusers.id NOT IN (SELECT user FROM tusers_tech) ORDER BY tusers.lastname");
													$qry->execute();
													while ($row = $qry->fetch())
                                                    {
                                                        echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';
                                                    }
													$qry->closeCursor();
                                                    echo '
                                                    <option selected></option>
                                                </select>
                                                <hr />
                                                <label class="control-label bolder text-primary-m2" for="skin">'.T_('Liste des utilisateurs associés à ce technicien').' :</label>
                                                <div class="space-4"></div>
                                                ';
													$qry = $db->prepare("SELECT `id`,`user` FROM `tusers_tech` WHERE tech=:tech");
													$qry->execute(array('tech' => $_GET['userid']));
                                                    while ($row = $qry->fetch())
                                                    {
                                                        //find tech name
														$qry2 = $db->prepare("SELECT `lastname`,`firstname` FROM `tusers` WHERE id=:id");
														$qry2->execute(array('id' => $row['user']));
														$row2=$qry2->fetch();
														$qry2->closeCursor();
                                                    	echo'<i class="fa fa-caret-right text-primary-m2"></i> '.$row2['lastname'].' '.$row2['firstname'].'';
                                                    	echo '<a title="Supprimer" href="./index.php?page=admin/user&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=attachment&attachmentdelete='.$row['id'].'"> <i class="fa fa-trash text-danger"></i></a>';
                                                        echo '<br />';
                                                    }
													$qry->closeCursor();
                                                    echo '
                            			    </div>
                                            <div id="parameters" class="tab-pane'; if($_GET['tab']=='parameters' || $_GET['tab']=='') echo 'active'; echo '">
													<label class="control-label bolder text-primary-m2" for="language">'.T_('Langue').' :</label>
                                                    <div class="space-4"></div>
                    								<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="language">
                    									<option '; if($user1['language']=='fr_FR'){echo "selected";} echo ' value="fr_FR">'.T_('Français (France)').'</option>
                    									<option '; if($user1['language']=='en_US'){echo "selected";} echo ' value="en_US">'.T_('Anglais (États Unis)').'</option>
                    									<option '; if($user1['language']=='de_DE'){echo "selected";} echo ' value="de_DE">'.T_('Allemand (Allemagne)').'</option>
                    									<option '; if($user1['language']=='es_ES'){echo "selected";} echo ' value="es_ES">'.T_('Espagnol (Espagne)').'</option>
                    								</select>
                    								<hr />
                                                    <label class="control-label bolder text-primary-m2" for="skin">'.T_('Thème').' :</label>
                                                    <div class="space-4"></div>
                    								<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="skin">
                    									<option '; if($user1['skin']==''){echo "selected";} echo ' value="">'.T_('Bleu (Défaut)').'</option>
														<option '; if($user1['skin']=='skin-3'){echo "selected";} echo ' value="skin-3">'.T_('Gris').'</option>
                    									<option '; if($user1['skin']=='skin-1'){echo "selected";} echo ' value="skin-1">'.T_('Noir').'</option>
                    									<option '; if($user1['skin']=='skin-2'){echo "selected";} echo ' value="skin-2">'.T_('Violet').'</option>
                    									<option '; if($user1['skin']=='skin-5'){echo "selected";} echo ' value="skin-5">'.T_('Vert').'</option>
														<option '; if($user1['skin']=='skin-7'){echo "selected";} echo ' value="skin-7">'.T_('Vert et violet').'</option>
                    									<option '; if($user1['skin']=='skin-6'){echo "selected";} echo ' value="skin-6">'.T_('Orange').'</option>
														<option '; if($user1['skin']=='skin-8'){echo "selected";} echo ' value="skin-8">'.T_('Orange et violet').'</option>
														<option '; if($user1['skin']=='skin-4'){echo "selected";} echo ' value="skin-4">'.T_('Sombre').'</option>
                    								</select>
                    								';
                    								//display group attachment if exist
													$qry = $db->prepare("SELECT count(*) FROM `tgroups`, `tgroups_assoc` WHERE tgroups.id=tgroups_assoc.group AND tgroups_assoc.user=:user AND tgroups.disable='0'");
													$qry->execute(array('user' => $_GET['userid']));
													$row=$qry->fetch();
													$qry->closeCursor();
                    								if($row[0]!=0)
                    								{
                    									echo '<hr />';
                    									echo '<label class="control-label bolder text-primary-m2" for="group">'.T_('Membre des groupes').' :</label>';
														$qry = $db->prepare("SELECT tgroups.id AS id, tgroups.name AS name FROM tgroups, tgroups_assoc WHERE tgroups.id=tgroups_assoc.group AND tgroups_assoc.user=:user AND tgroups.disable='0'");
														$qry->execute(array('user' => $_GET['userid']));
                    									while ($row = $qry->fetch())
                    									{
                    										echo "<div class=\"space-4\"></div><i class=\"fa fa-caret-right text-primary-m2\"></i> <a href=\"./index.php?page=admin&subpage=group&action=edit&id=$row[id]\"> $row[name]</a>";
                    									}
														$qry->closeCursor();														
                    								}
                    								// Display profile list
                    								if($rright['admin_user_profile']!='0')
                    								{
                    									echo '
                    									<hr />
                    									<label class="control-label bolder text-primary-m2" for="profile">'.T_('Profil').' :</label>
                    									<div class="controls">
                    										<div class="radio">
                    											<label>
                    												<input type="radio" class="ace" name="profile" value="4" '; if($user1['profile']=='4')echo "checked"; echo '> <span class="lbl"> '.T_('Administrateur').' </span>
                    											</label>
                    										</div>
                    										<div class="radio">
                    											<label>
                    												<input type="radio" class="ace" name="profile" value="0" '; if($user1['profile']=='0')echo "checked"; echo '> <span class="lbl"> '.T_('Technicien').' </span>
                    											</label>
                    										</div>
                    										<div class="radio">
                    											<label>
                    												<input type="radio" class="ace" name="profile" value="3" '; if($user1['profile']=='3')echo "checked"; echo '> <span class="lbl"> '.T_('Superviseur').' </span>
                    											</label>
                    										</div>
                    										<div class="radio">
                    											<label>
                    												<input type="radio" class="ace" name="profile" value="1" '; if($user1['profile']=='1')echo "checked"; echo '> <span class="lbl"> '.T_('Utilisateur avec pouvoir').' </span>
                    											</label>
                    										</div>
                    										<div class="radio">
                    											<label>
                    												<input type="radio" class="ace" name="profile" value="2" '; if($user1['profile']=='2')echo "checked"; echo '> <span class="lbl"> '.T_('Utilisateur').' </span>
                    											</label>
                    										</div>
                    									</div>
                    									<hr />
                    									<label class="control-label bolder text-primary-m2" for="chgpwd">'.T_('Forcer le changement du mot de passe').' :</label>
                    									<br />
                    									<label>
                    											<input type="radio" class="ace" disable="disable" name="chgpwd" value="1" '; if($user1['chgpwd']=='1')echo "checked"; echo '> <span class="lbl"> '.T_('Oui').' </span>
                    											<input type="radio" class="ace" name="chgpwd" value="0" '; if($user1['chgpwd']=='0')echo "checked"; echo '> <span class="lbl"> '.T_('Non').' </span>
                    									</label>
														<hr />
                    									<label class="control-label bolder text-primary-m2" for="disable_user">'.T_('Utilisateur désactivé').' :</label>
                    									<br />
                    									<label>
                    											<input type="radio" class="ace" name="disable_user" value="1" '; if($user1['disable']=='1')echo "checked"; echo '> <span class="lbl"> '.T_('Oui').' </span>
                    											<input type="radio" class="ace" name="disable_user" value="0" '; if($user1['disable']=='0')echo "checked"; echo '> <span class="lbl"> '.T_('Non').' </span>
                    									</label>
                    									';
                    								}
                    								else
                    								{
                    									echo '<input type="hidden" name="profile" value="'.$user1['profile'].'" '; if($user1['profile']=='2')echo "checked"; echo '>';
                    								}
                    								//display personal view
                    								if($rright['admin_user_view']!='0')
                    								{
                    									echo '
                    										<hr />
                    										<label class="control-label bolder text-primary-m2" for="view">'.T_('Vues personnelles').' : </label>
                        									<i title="'.T_("associe des catégories à l'utilisateur").'" class="fa fa-question-circle text-primary-m2"></i>
                    										<div class="space-4"></div>';
                    											//check if selected user have view
																$qry = $db->prepare("SELECT `id` FROM `tviews` WHERE uid=:uid");
																$qry->execute(array('uid' => $_GET['userid']));
																$row=$qry->fetch();
																$qry->closeCursor();
																if(empty($row['id'])) {$row['id']='';}
                    											if($row['id'])
                    											{
                    												//display active views
																	$qry = $db->prepare("SELECT * FROM `tviews` WHERE uid=:uid ORDER BY uid");
																	$qry->execute(array('uid' => $_GET['userid']));
                    												while ($row = $qry->fetch())
                    												{
																		//get cat name
																		$qry2 = $db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
																		$qry2->execute(array('id' => $row['category']));
																		$cname=$qry2->fetch();
																		$qry2->closeCursor();
																		
                    													if($row['subcat']!='')
                    													{
																			$qry2 = $db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
																			$qry2->execute(array('id' => $row['subcat']));
																			$sname=$qry2->fetch();
																			$qry2->closeCursor();
                    													} else {$sname='';}
                    													echo '<i class="fa fa-caret-right text-primary-m2"></i> '.$row['name'].': ('.$cname['name'].' > '.$sname[0].') 
                    													<a title="'.T_('Supprimer cette vue').'" href="index.php?page=admin/user&subpage=user&action=edit&userid='.$_GET['userid'].'&viewid='.$row['id'].'&deleteview=1"><i class="fa fa-trash text-danger"></i></a>
                    													<br />';
                    												}
																	$qry->closeCursor();
                    												echo '<br />';
                    											}
                    											//display add view form
                    											echo '
                    												'.T_('Catégorie').' :
                    												<select style="width:auto;" class="form-control form-control-sm d-inline-block mt-2" name="category" onchange="submit()" style="width:100px" >
                    													<option value="%"></option>';
																		//case to limit service parameters is enable
																		if($rparameters['user_limit_service']==1 && $rright['admin']==0)
																		{
																			$qry = $db->prepare("SELECT `id`,`name` FROM `tcategory` WHERE `service` IN (SELECT `service_id` FROM `tusers_services` WHERE `user_id`=:user_id) ORDER BY `name`");
																			$qry->execute(array('user_id' => $_SESSION['user_id']));
																		} else {
																			$qry = $db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
																			$qry->execute();
																		}
                    													while ($row=$qry->fetch())
                    													{
                    														echo "<option value=\"$row[id]\">$row[name]</option>";
                    														if($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
                    													} 
																		$qry->closeCursor();
                    													echo '
                    												</select>
																	';
																	if($_POST['category']!='%')
																	{
																		echo '
																		<div class="m-2"></div>
																		'.T_('Sous-catégorie').' :
																		<select style="width:auto;" class="form-control form-control-sm d-inline-block mt-2" name="subcat" onchange="submit()" style="width:90px">
																			<option value="%"></option>';
																			$qry = $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE `cat`=:cat ORDER BY `name`");
																			$qry->execute(array('cat' => $_POST['category']));
																			while ($row = $qry->fetch())
																			{
																				echo "<option value=\"$row[id]\">$row[name]</option>";
																				if($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
																			} 
																			$qry->closeCursor();
																			echo '
																		</select>';
																	}
																	echo '
                    												<div class="space-4"></div>
                    												Nom : <input style="width:auto;" class="form-control form-control-sm d-inline-block mt-2" autocomplete="off" name="viewname" type="" value="'.$_POST['name'].'" size="20" />';
                    											
                    											//display default ticket state
                    										    echo '
                        										    <hr />
                        										    <label class="control-label bolder text-primary-m2" for="default_ticket_state">'.T_('État par défaut à la connexion').' :</label>
                        										    <i title="'.T_("État qui est directement affiché, lors de la connexion à l'application, si ce paramètre n'est pas renseigné, alors l'état par défaut est celui définit par l'administrateur").'." class="fa fa-question-circle text-primary-m2"></i>
                        										    <div class="space-4"></div>
                        										    <select style="width:auto;" class="form-control form-control-sm d-inline-block" name="default_ticket_state">
                                    									<option '; if($user1['default_ticket_state']==''){echo "selected";} echo ' value="">'.T_("Aucun (Géré par l'administrateur)").'</option>';
																		if($rparameters['meta_state']==1) 
																		{
																			echo '<option '; if($user1['default_ticket_state']=='meta'){echo "selected";} echo ' value="meta">'.T_('Vos tickets à traiter').'</option>';
																			echo '<option '; if($user1['default_ticket_state']=='meta_all'){echo 'selected';} echo ' value="meta_all">'.T_('Tous les tickets à traiter').'</option>';
																		}
                                                                        $qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY `number`");
																		$qry->execute();
                    													while ($row = $qry->fetch())
                    													{
                    														if($user1['default_ticket_state']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.T_('Vos tickets').' '.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.T_('Vos tickets').' '.$row['name'].'</option>';}
                    													}
																		$qry->closeCursor();
                    													echo '
																		<option '; if($user1['default_ticket_state']=='all'){echo 'selected';} echo ' value="all">'.T_('Tous les tickets').'</option>
                                    								</select>
                    										    ';
                    										    //display default ticket order
                    										    echo '
                        										    <hr />
                        										    <label class="control-label bolder text-primary-m2" for="dashboard_ticket_order">'.T_('Ordre de trie personnel par défaut').' :</label>
                        										    <i title="'.T_("Modifie l'ordre de trie des tickets dans la liste des tickets, si ce paramètre n'est pas renseigné, c'est le réglage par défaut dans la section administration qui est prit en compte").'." class="fa fa-question-circle text-primary-m2"></i>
                        										    <div class="space-4"></div>
                        										    <select style="width:auto;" class="form-control form-control-sm d-inline-block" name="dashboard_ticket_order">
                        										        <option '; if($user1['default_ticket_state']==''){echo "selected";} echo ' value="">'.T_("Aucun (Géré par l'administrateur)").'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create'){echo "selected";} echo ' value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create">'.T_('État  > Priorité > Criticité > Date de création').'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope'){echo "selected";} echo ' value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope">'.T_('État > Priorité > Criticité > Date de résolution estimée').'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='tincidents.date_hope'){echo "selected";} echo ' value="tincidents.date_hope"> '.T_('Date de résolution estimée').'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='tincidents.priority'){echo "selected";} echo ' value="tincidents.priority"> '.T_('Priorité').'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='tincidents.criticality'){echo "selected";} echo ' value="tincidents.criticality"> '.T_('Criticité').'</option>
                                    									<option '; if($user1['dashboard_ticket_order']=='id'){echo "selected";} echo ' value="id">'.T_('Numéro de ticket').'</option>
                                    								</select>
                    										    ';	
                    								    }
														//display ticket limit parameters
														if($rparameters['user_limit_ticket']==1 )
														{
															if($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2) $readonly='readonly'; else $readonly='';
															echo '
																<hr />
																<label class="control-label bolder text-primary-m2" for="limit_ticket_number">'.T_('Limite de tickets').' :</label>
																<i title="'.T_("Permet de limiter un utilisateur a un nombre de ticket définit, passer la limite l'ouverture de nouveau ticket n'est plus possible").'." class="fa fa-question-circle text-primary-m2"></i>
																<div class="space-4"></div>
																<label for="limit_ticket_number">'.T_('Nombre limite de ticket').' :</label>
																<input '.$readonly.' size="3" name="limit_ticket_number" type="text" value="'; if($user1['limit_ticket_number']) echo "$user1[limit_ticket_number]"; else echo ""; echo'" />
																<div class="space-4"></div>
																<label for="limit_ticket_days">'.T_('Durée de validé jours').' :</label>
																<input '.$readonly.' size="4" name="limit_ticket_days" type="text" value="'; if($user1['limit_ticket_days']) echo "$user1[limit_ticket_days]"; else echo ""; echo'" />
																<div class="space-4"></div>
																<label for="limit_ticket_date_start">'.T_('Date de début de validité (YYYY-MM-DD)').' :</label>
																<input '.$readonly.' size="10" name="limit_ticket_date_start" type="text" value="'; if($user1['limit_ticket_date_start']) echo "$user1[limit_ticket_date_start]"; else echo ""; echo'" />
															';
														}
                    								echo'
                                            </div>
                                            <div id="infos" class="tab-pane'; if($_GET['tab']=='infos' || $_GET['tab']=='') echo 'active'; echo '">
                                    			<label for="firstname">'.T_('Prénom').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="firstname" type="text" value="' .$user1['firstname'].'" />
                								<div class="space-4"></div>
                								<label for="lastname">'.T_('Nom').' :</label>
												';if($mobile==1) {echo '<br />';} echo '
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="lastname" type="text" value="'.$user1['lastname'].'" />
                								<div class="space-4"></div>
                                    			<label for="firstname">'.T_('Tutelle').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="guardianship" type="text" value="'.$user1['firstname_guardianship'].' '.$user1['lastname_guardianship'].'" />
                								<div class="space-4"></div>';
                								//not display login field for users for security
                								if($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4) {$label_hide='';$input_type='type="text"';} else {$label_hide='hidden';$input_type='type="hidden"';}
                                                echo '
                								<label '.$label_hide.' for="login">'.T_('Identifiant').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" autocomplete="off"  name="login" '.$input_type.' value="'.$user1['login'].'"  />
                								<div class="space-4"></div>
                								<label for="password">'.T_('Mot de passe').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" autocomplete="new-password" name="password" type="password" value="" />
												<div class="space-4"></div>
                								<label for="password2">'.T_('Confirmation mot de passe').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" autocomplete="new-password" name="password2" type="password" value="" />
                								<div class="space-4"></div>
                								';
                								$laboratory = '';
                                                $qry = $db->prepare("SELECT `name` FROM `tcompany` WHERE `id`=:company");
                                                $qry->execute(['company' => $user1['company']]);
                                                $results = $qry->fetch();
                                                if ($results['name']) {
                                                    $laboratory = $results['name'];
                                                }
                                                $qry->closeCursor();
                                                echo '
                								<label for="mail">'.T_('Laboratoire d\'origine').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="mail" type="text" value="'.$laboratory.'" />
                								<div class="space-4"></div>
                								<label for="mail">'.T_('Site de rattachement').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="mail" type="text" value="'.$user1['address1'].'" />                								
                								<div class="space-4"></div>
                								<label for="mail">'.T_('Adresse mail').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="mail" type="text" value="'.$user1['mail'].'" />                								
                								<div class="space-4"></div>
                								<label for="phone">'.T_('Téléphone fixe').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="phone" type="text" value="'.$user1['phone'].'" />
                								<div class="space-4"></div>
												<label for="mobile">'.T_('Téléphone portable').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="mobile" type="text" value="'.$user1['mobile'].'" />
                								<div class="space-4"></div>
                								<label for="fax">'.T_('Fax').' :</label>
												';if($mobile) {echo '<br />';} echo '
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="fax" type="text" value="'.$user1['fax'].'" />
                								<div class="space-4"></div>
                								<label for="service">'.T_('Service').' :</label>
												';
												if($mobile) {echo '<br />';}
												//service add field
												if($rright['user_profil_service'])
												{
													echo '<select style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="service">';
													echo '<option disabled value=""></option>';
														$qry = $db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `id`!=0 AND `id` NOT IN (SELECT DISTINCT(`service_id`) FROM `tusers_services` WHERE `user_id`=:user_id)");
														$qry->execute(array('user_id' => $_GET['userid']));
														while ($service=$qry->fetch())
														{
															echo '<option disabled value="'.$service['id'].'">'.$service['name'].'</option>';
														}
													echo '</select>';
												}

												//display current service associations
												$qry=$db->prepare("SELECT COUNT(`id`) FROM `tusers_services` WHERE user_id=:user_id");
												$qry->execute(array('user_id' => $_GET['userid']));
												$assoc=$qry->fetch();
												$qry->closeCursor();
												if($assoc[0]>0)
												{
													echo '
													<ul>
													';
														$qry = $db->prepare("SELECT `tservices`.`id`,`tservices`.`name`, `tusers_services`.`id` AS assoc_id  FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id");
														$qry->execute(array('user_id' => $_GET['userid']));
														while ($service=$qry->fetch())
														{
															echo '<li>';
															echo $service['name'];
															if($rright['user_profil_service'] && $canEditProfile) {echo '&nbsp;<a href="./index.php?page=admin&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=infos&delete_assoc_service='.$service['assoc_id'].'" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette association ?').'\');"><i title="'.T_("Supprimer l'association de ce service avec cet utilisateur").'" class="fa fa-trash text-danger"></i></a>';}
															echo '</li>';
														}
														echo '
													</ul>
													';
												}

												//agency field
												if($rparameters['user_agency'])
												{
													echo '<div class="space-4"></div>
													<label for="agency">'.T_('Agence').' :</label>
													';
													//agency add field
													if($rright['user_profil_agency'])
													{
														echo '<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="agency">';
														echo '<option value=""></option>';
															$qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE `id`!=0 AND `id` NOT IN (SELECT DISTINCT(`agency_id`) FROM `tusers_agencies` WHERE `user_id`=:user_id)");
															$qry->execute(array('user_id' => $_GET['userid']));
															while ($agency=$qry->fetch())
															{
																echo '<option value="'.$agency['id'].'">'.$agency['name'].'</option>';
															}
														echo '</select>';
													}
													//current agency associations
													$qry=$db->prepare("SELECT COUNT(`id`) FROM `tusers_agencies` WHERE user_id=:user_id");
													$qry->execute(array('user_id' => $_GET['userid']));
													$assoc=$qry->fetch();
													$qry->closeCursor();
													if($assoc[0]>0)
													{
														echo '
															<ul>
															';
																$qry = $db->prepare("SELECT `tagencies`.`id`,`tagencies`.`name`, `tusers_agencies`.`id` AS assoc_id  FROM `tagencies`,`tusers_agencies` WHERE `tagencies`.`id`=`tusers_agencies`.`agency_id` AND `tusers_agencies`.`user_id`=:user_id");
																$qry->execute(array('user_id' => $_GET['userid']));
																while ($agency=$qry->fetch())
																{
																	echo '<li>';
																	echo $agency['name'];
																	if($rright['user_profil_agency']) {echo '&nbsp;<a href="./index.php?page=admin&subpage=user&action=edit&userid='.$_GET['userid'].'&tab=infos&delete_assoc_agency='.$agency['assoc_id'].'" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette association ?').'\');"><i title="'.T_("Supprimer l'association de cette agence avec cet utilisateur").'" class="fa fa-trash text-danger"></i></a>';}
																	echo '</li>';
																}
																echo '
															</ul>
														';
													}
												}
												echo '
                								<div class="space-4"></div>
                								<label for="function">'.T_('Fonction').' :</label>
                								<input style="width:auto;"'.(!$rright['admin_user_profile'] ? "readonly" : "").' class="form-control form-control-sm d-inline-block" name="function" size="25" type="text" value="'.$user1['function'].'" />
                								';
                								//display advanced user informations
                								if($rparameters['user_advanced']!='0')
                								{
                								echo '
                									<div class="space-4"></div>
                									<label for="company">'.T_('Société').' :</label>
													';if($mobile==1) {echo '<br />';} echo '
                									<select style="width:150px;" class="chosen-select" name="company" '; if($rright['user_profil_company']==0) {echo 'disabled="disabled"';} echo '>
                    									';
														$qry = $db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY `name`");
														$qry->execute();
                    									while ($row = $qry->fetch())
                    									{
                    										if($user1['company']==$row['id']) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
                    									} 
														$qry->closeCursor();
                    									echo '
                							    	</select>
													';
													//send company value in disabled state
													if($rright['user_profil_company']==0)
													{
														echo '<input type="hidden" name="company" value="'.$user1['company'].'" />';
													}
													echo '
                									<div class="space-4 pt-1"></div>
                									<label for="address1">'.T_('Adresse').' 1 :</label>
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="address1" type="text" value="'; if($user1['address1']) echo "$user1[address1]"; else echo ""; echo'" />
                									<div class="space-4"></div>
                									<label  for="address2">'.T_('Adresse').' 2 :</label>
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="address2" type="text" value="'; if($user1['address2']) echo "$user1[address2]"; else echo ""; echo'" />
                									<div class="space-4"></div>
                									<label  for="city">'.T_('Ville').' :</label>
													';if($mobile==1) {echo '<br />';} echo '
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="city" type="text" value="'; if($user1['city']) echo "$user1[city]"; else echo ""; echo'" />
                									<div class="space-4"></div>
                									<label for="zip">'.T_('Code Postal').' :</label>
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="zip" type="text" value="'; if($user1['zip']) echo "$user1[zip]"; else echo ""; echo'" />
													<div class="space-4"></div>
                									<label for="custom1">'.T_('Champ personnalisé').' 1 :</label>
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="custom1" type="text" value="'; if($user1['custom1']) echo "$user1[custom1]"; else echo ""; echo'" />
                									<div class="space-4"></div>
                									<label for="custom2">'.T_('Champ personnalisé').' 2 :</label>
                									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="custom2" type="text" value="'; if($user1['custom2']) echo "$user1[custom2]"; else echo ""; echo'" />
                									<div class="space-4"></div>
                								';
                								}
                                            	echo '		
                            			    </div>
                            			</div>
                            		</div>
                            	</div>
							</fieldset>
							<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
								<button value="modify" id="modify" name="modify" type="submit" class="btn btn-success">
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
				</div>
			</div>
		</div>
	';
}
else if($_GET['action']=="add" && (($_SESSION['user_id']==$_GET['userid']) || ($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4)))
{
	//remove '' to display
	$_POST['firstname']=str_replace("'","",$_POST['firstname']);
	$_POST['lastname']=str_replace("'","",$_POST['lastname']);
	$_POST['login']=str_replace("'","",$_POST['login']);
	$_POST['password']=str_replace("'","",$_POST['password']);
	$_POST['mail']=str_replace("'","",$_POST['mail']);
	$_POST['phone']=str_replace("'","",$_POST['phone']);
	$_POST['mobile']=str_replace("'","",$_POST['mobile']);
	$_POST['fax']=str_replace("'","",$_POST['fax']);
	$_POST['function']=str_replace("'","",$_POST['function']);
	
	/////////////////////////////////////////////////////////////////////display add form///////////////////////////////////////////////////
	echo '
		<div class="col-12 cards-container">
			<div class="card bcard shadow">
				<div class="card-header">
					<h5 class="card-title">'.T_("Nouvel utilisateur").' :</h5>
					<span class="card-toolbar">
						<button title="'.T_('Ajouter un utilisateur').'" value="add" id="add" name="add" type="submit" form="1" class="btn btn-xs btn-success ml-1">
							<i class="fa fa-save text-110"></i>
						</button>
					</span>
				</div>
				<div class="card-body">
					<div class="card-main no-padding">
						<form id="1" name="form" method="POST"  action="">
							<fieldset>
								<label for="firstname">'.T_('Prénom').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="firstname" type="text" value="'.$_POST['firstname'].'" />
								<div class="space-4"></div>
								<label  for="lastname">'.T_('Nom').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="lastname" type="text" value="'.$_POST['lastname'].'" />
								<div class="space-4"></div>
								<label for="login">'.T_('Identifiant').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" autocomplete="off" name="login" type="text" value="'.$_POST['login'].'" />
								<div class="space-4"></div>
								<label  for="password">'.T_('Mot de passe').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" autocomplete="new-password" name="password" type="password" value="'.$_POST['password'].'" />
								<div class="space-4"></div>
								<label  for="password2">'.T_('Confirmation mot de passe').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" autocomplete="new-password" name="password2" type="password" value="'.$_POST['password2'].'" />
								<div class="space-4"></div>
								<label for="mail">'.T_('Adresse mail').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="mail" type="text" value="'.$_POST['mail'].'" />
								<div class="space-4"></div>
								<label  for="phone">'.T_('Téléphone fixe').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="phone" type="text" value="'.$_POST['phone'].'" />
								<div class="space-4"></div>
								<label  for="mobile">'.T_('Téléphone portable').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="mobile" type="text" value="'.$_POST['mobile'].'" />
								<div class="space-4"></div>
								<label  for="fax">'.T_('Fax').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="fax" type="text" value="'.$_POST['fax'].'" />
								<div class="space-4"></div>
								<label for="service">'.T_('Service').' :</label>
								<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="service" '; if($rright['user_profil_service']==0) {echo 'disabled="disabled"';} echo '>
									<option value=""></option>';
									$qry = $db->prepare("SELECT `id`,`name` FROM `tservices` ORDER BY `name`");
									$qry->execute();
									while ($row = $qry->fetch()) 
									{
										if($_POST['service']) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
									} 
									$qry->closeCursor();
									echo '
								</select>
								';
								if($rparameters['user_agency'])
								{
									echo '
									<div class="space-4"></div>
									<label for="agency">'.T_('Agence').' :</label>
									<select  name="agency" '; if($rright['user_profil_agency']==0) {echo 'disabled="disabled"';} echo '>
										<option value=""></option>';
										$qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` ORDER BY `name`");
										$qry->execute();
										while ($row = $qry->fetch()) 
										{
											if($_POST['agency']) {echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';} else {echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
										} 
										$qry->closeCursor();
										echo '
									</select>
									';
								}
								echo '
								<div class="space-4"></div>
								<label for="function">'.T_('Fonction').' :</label>
								<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="function" type="text" value="'.$_POST['function'].'" />
								';
								//display advanced user informations
								if($rparameters['user_advanced']!='0')
								{
								echo '
									<div class="space-4"></div>
									<label  for="company">'.T_('Société').' :</label>
									<select style="width:150px;" class="chosen-select" data-placeholder=" " name="company" '; if($rright['user_profil_company']==0) {echo 'disabled="disabled"';} echo '>
    									<option value=""></option>';
										$qry = $db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY `name`");
										$qry->execute();
    									while ($row = $qry->fetch()) 
    									{
    										if($user1['company']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
    									} 
										$qry->closeCursor(); 
    									echo '
							    	</select>
									
									<div class="space-4 pt-1"></div>
									<label for="address1">'.T_('Adresse').' 1 :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="address1" type="text" value="" />
									<div class="space-4"></div>
									<label for="address2">'.T_('Adresse').' 2 :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="address2" type="text" value="" />
									<div class="space-4"></div>
									<label for="city">'.T_('Ville').' :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="city" type="text" value="" />
									<div class="space-4"></div>
									<label for="zip">'.T_('Code Postal').' :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="zip" type="text" value="" />
									<div class="space-4"></div>
									<label for="custom1">'.T_('Champ personnalisé').' 1 :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="custom1" type="text" value="" />
									<div class="space-4"></div>
									<label for="custom2">'.T_('Champ personnalisé').' 2 :</label>
									<input style="width:auto;" class="form-control form-control-sm d-inline-block" name="custom2" type="text" value="" />
								';
								}
								//display theme selection
								echo '
								<hr />
								<label class="control-label bolder text-primary-m2" for="skin">'.T_('Thème').' :</label>
								<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="skin">
									<option value="">'.T_('Bleu (Défaut)').'</option>
									<option value="skin-3">'.T_('Gris').'</option>
									<option value="skin-1">'.T_('Noir').'</option>
									<option value="skin-2">'.T_('Violet').'</option>
									<option value="skin-5">'.T_('Vert').'</option>
									<option value="skin-7">'.T_('Vert et violet').'</option>
									<option value="skin-6">'.T_('Orange').'</option>
									<option value="skin-8">'.T_('Orange et violet').'</option>
									<option value="skin-4">'.T_('Sombre').'</option>
								</select>
								';
								// Display profile list
								if($rright['admin_user_profile']!='0')
								{
									echo '
									<hr />
									<label class="control-label bolder text-primary-m2" for="profile">'.T_('Profil').' :</label>
									<div class="controls">
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="4"> <span class="lbl"> '.T_('Administrateur').' </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="0"> <span class="lbl"> '.T_('Technicien').' </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="3"> <span class="lbl"> '.T_('Superviseur').' </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" value="1"> <span class="lbl"> '.T_('Utilisateur avec pouvoir').' </span>
											</label>
										</div>
										<div class="radio">
											<label>
												<input type="radio" class="ace" name="profile" checked value="2"> <span class="lbl"> '.T_('Utilisateur').' </span>
											</label>
										</div>
									</div>
									<hr />
									<label class="control-label bolder text-primary-m2" for="chgpwd">'.T_('Forcer le changement du mot de passe').' :</label>
									<div class="controls">
										<label>
											<input type="radio" class="ace" disable="disable" name="chgpwd" checked value="1"> <span class="lbl"> '.T_('Oui').' </span>
											<input type="radio" class="ace" name="chgpwd" value="0"> <span class="lbl"> '.T_('Non').' </span>
										</label>
									</div>
									';
								}
								else
								{
									echo '<input type="hidden" name="profile" value="">';
								}
								//display personal view
								if($rright['admin_user_view']!='0')
								{
									echo '
										<hr />
										<label class="control-label text-primary-m2" for="view">'.T_('Vues personnelles').' : <i class="fa fa-question-circle text-primary-m2" title="'.T_("associe des catégories à l'utilisateur").'"></i></label>
										<div class="controls">
											';
											//check if connected user have view
											$qry = $db->prepare("SELECT `id` FROM `tviews` WHERE uid=:uid");
											$qry->execute(array('uid' => $_GET['userid']));
											$row=$qry->fetch();
											$qry->closeCursor(); 
											if(!empty($row[0]))
											{
												//display actives views
												$qry = $db->prepare("SELECT * FROM `tviews` WHERE uid=:uid ORDER BY uid");
												$qry->execute(array('uid' => $_GET['userid']));
												while ($row = $qry->fetch())
												{
													$qry2 = $db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
													$qry2->execute(array('id' => $row['category']));
													$cname=$qry2->fetch();
													$qry2->closeCursor(); 
													
													if($row['subcat']!=0)
													{
														$qry2 = $db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
														$qry2->execute(array('id' => $row['subcat']));
														$sname= $qry2->fetch();
														$qry2->closeCursor(); 
													} else {$sname='';}
													echo '- '.$row['name'].': ('.$cname['name'].' > '.$sname[0].') 
													<a title="'.T_('Supprimer cette vue').'" href="index.php?page=admin&subpage=user&action=edit&userid='.$_GET['userid'].'&viewid='.$row['id'].'&deleteview=1"><img alt="delete" src="./images/delete.png" style="border-style: none" /></a>
													<br />';
												}
												$qry->closeCursor(); 
												echo '<br />';
											}
											//display add view form
											echo '
												'.T_('Catégorie').':
												<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="category" onchange="submit()" style="width:100px" >
													<option value="%"></option>';
													$qry = $db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY name");
													$qry->execute();
													while ($row = $qry->fetch()) 
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													$qry->closeCursor();
													echo '
												</select>
												<br />
												'.T_('Sous-catégorie').':
												<select style="width:auto;" class="form-control form-control-sm d-inline-block mt-2" name="subcat" onchange="submit()" style="width:90px">
													<option value="%"></option>';
													if($_POST['category']!='%')
													{
														$qry = $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat=:cat ORDER BY `name`");
														$qry->execute(array('cat' => $_POST['category']));
													}
													else
													{
														$qry = $db->prepare("SELECT `id`,`name` FROM `tsubcat` ORDER BY `name`");
														$qry->execute();
													}
													while ($row = $qry->fetch())
													{
														echo "<option value=\"$row[id]\">$row[name]</option>";
														if($_POST['subcat']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>";
													} 
													$qry->closeCursor();
													echo '
												</select>
												<br />
												'.T_('Nom').': <input style="width:auto;" class="form-control form-control-sm d-inline-block mt-2" autocomplete="off" name="viewname" type="" value="'.$_POST['name'].'" size="20" />
										</div>';
								}
								echo'
							</fieldset>
							<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
								<button value="add" id="add" name="add" type="submit" class="btn btn-success mr-2">
									<i class="fa fa-check"></i>
									'.T_('Ajouter').'
								</button>
								<button name="cancel" value="cancel" type="submit" class="btn btn-danger" >
									<i class="fa fa-reply"></i>
									'.T_('Retour').'
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	';
}
elseif($_GET['action']=="disable" && $rright['admin'])
{
	$qry=$db->prepare("UPDATE `tusers` SET `disable`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['userid']));
	
	if($rparameters['log'])
	{
		if(is_numeric($_GET['userid']))
		{
			$qry=$db->prepare("SELECT `login` FROM `tusers` WHERE id=:id");
			$qry->execute(array('id' => $_GET['userid']));
			$row=$qry->fetch();
			$qry->closeCursor();
			
			require_once('core/functions.php');
			logit('security', 'User '.$row['login'].' disabled',$_SESSION['user_id']);
		}
	}
	
	//home page redirection
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}elseif($_GET['action']=="delete" && $rright['admin'] && $_GET['userid'])
{
	//get id of delete_user
	$qry=$db->prepare("SELECT `id` FROM `tusers` WHERE login=:login");
	$qry->execute(array('login' => 'delete_user_gs'));
	$delete_user=$qry->fetch();
	$qry->closeCursor();
	$delete_user=$delete_user[0];


	//update ticket
	$qry=$db->prepare("UPDATE `tincidents` SET `user`=:delete_user WHERE `user`=:user");
	$qry->execute(array('delete_user' => $delete_user,'user' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tincidents` SET `technician`=:delete_user WHERE `technician`=:technician");
	$qry->execute(array('delete_user' => $delete_user,'technician' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tincidents` SET `creator`=:delete_user WHERE `creator`=:creator");
	$qry->execute(array('delete_user' => $delete_user,'creator' => $_GET['userid']));
	//update threads
	$qry=$db->prepare("UPDATE `tthreads` SET `author`=:delete_user WHERE `author`=:author");
	$qry->execute(array('delete_user' => $delete_user,'author' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tthreads` SET `tech1`=:delete_user WHERE `tech1`=:tech1");
	$qry->execute(array('delete_user' => $delete_user,'tech1' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tthreads` SET `tech2`=:delete_user WHERE `tech2`=:tech2");
	$qry->execute(array('delete_user' => $delete_user,'tech2' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tthreads` SET `user`=:delete_user WHERE `user`=:user");
	$qry->execute(array('delete_user' => $delete_user,'user' => $_GET['userid']));
	//update asset
	$qry=$db->prepare("UPDATE `tassets` SET `user`=:delete_user WHERE `user`=:user");
	$qry->execute(array('delete_user' => $delete_user,'user' => $_GET['userid']));
	$qry=$db->prepare("UPDATE `tassets` SET `technician`=:delete_user WHERE `technician`=:technician");
	$qry->execute(array('delete_user' => $delete_user,'technician' => $_GET['userid']));
	//remove calendar
	$qry=$db->prepare("DELETE FROM `tevents` WHERE `technician`=:technician");
	$qry->execute(array('technician' => $_GET['userid']));
	//remove groups
	$qry=$db->prepare("DELETE FROM `tgroups_assoc` WHERE `user`=:user");
	$qry->execute(array('user' => $_GET['userid']));
	//remove token
	$qry=$db->prepare("DELETE FROM `ttoken` WHERE `user_id`=:user_id");
	$qry->execute(array('user_id' => $_GET['userid']));
	//remove agencies
	$qry=$db->prepare("DELETE FROM `tusers_agencies` WHERE `user_id`=:user_id");
	$qry->execute(array('user_id' => $_GET['userid']));
	//remove services
	$qry=$db->prepare("DELETE FROM `tusers_services` WHERE `user_id`=:user_id");
	$qry->execute(array('user_id' => $_GET['userid']));
	//remove tech assoc
	$qry=$db->prepare("DELETE FROM `tusers_tech` WHERE `user`=:user");
	$qry->execute(array('user' => $_GET['userid']));
	$qry=$db->prepare("DELETE FROM `tusers_tech` WHERE `tech`=:tech");
	$qry->execute(array('tech' => $_GET['userid']));
	//remove view 
	$qry=$db->prepare("DELETE FROM `tviews` WHERE `uid`=:uid");
	$qry->execute(array('uid' => $_GET['userid']));
	//remove user 
	$qry=$db->prepare("DELETE FROM `tusers` WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['userid']));
	
	
	//home page redirection
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
elseif($_GET['action']=="enable" && $rright['admin'])
{
	$qry=$db->prepare("UPDATE `tusers` SET `disable`='0' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['userid']));
	//home page redirection
	$www = "./index.php?page=admin&subpage=user";
			echo '<script language="Javascript">
			<!--
			document.location.replace("'.$www.'");
			// -->
			</script>';
}
elseif($_GET['ldap']=="1")
{
	include('./core/ldap.php');
} elseif($_GET['ldap']=="agencies")
{
	include('./core/ldap_agencies.php');
} elseif($_GET['ldap']=="services")
{
	include('./core/ldap_services.php');
}
//display security warning for user who want access to edit another user profile
elseif(($_GET['action']=='edit') && ($_GET['userid']!='') && ($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4) && ($_GET['userid']!=$_SESSION['user_id']))
{
   echo '
	<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
		<div class="flex-grow-1">
			<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
			<strong class="text-danger">'.T_('Erreur').' : '.T_("Vous n'avez pas le droit d'accéder au profil d'un autre utilisateur").'.</strong>
		</div>
		<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
		</button>
	</div>
';
}
//display security warning for user who want access to add another user profile
elseif(($_GET['action']=='add') && ($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4) && ($_GET['userid']!=$_SESSION['user_id']))
{
	echo '
	<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
		<div class="flex-grow-1">
			<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
			<strong class="text-danger">'.T_('Erreur').' : '.T_("Vous n'avez pas le droit d'ajouter des utilisateurs").'.</strong>
		</div>
		<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
		</button>
	</div>
	';
}

//else display users list
else
{
	//display buttons
	echo '
		<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
			<p>
				<button onclick=\'window.location.href="index.php?page=admin&subpage=user&action=add";\' class="btn btn-success">
					<i class="fa fa-plus"></i> '.T_('Ajouter un utilisateur').'
				</button>
				';
				if($_GET['disable']==0)
				{
			    	echo '
    				<button onclick=\'window.location.href="index.php?page=admin&subpage=user&disable=1";\' class="btn btn-danger">
    					<i class="fa fa-ban"></i> '.T_('Afficher les utilisateurs désactivés').'
    				</button>
			    	';
				} else {
			    echo '
    				<button onclick=\'window.location.href="index.php?page=admin&subpage=user&disable=0";\' class="btn btn-success">
    					<i class="fa fa-check"></i> '.T_('Afficher les utilisateurs activés').'
    				</button>
			    	';  
				}

		if($rparameters['ldap']==1 && $rparameters['ldap_agency']==0)
		{
			echo '
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=1";\' class="btn btn-info">
					<i class="fa fa-sync"></i> '.T_('Synchronisation LDAP').'
				</button>
			';
		}
		if($rparameters['ldap']==1 && $rparameters['ldap_agency']==1)
		{
			echo '
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=agencies";\' class="btn btn-info">
					<i class="fa fa-sync"></i> '.T_('Synchronisation des agences LDAP').'
				</button>
			';
		}
		if($rparameters['ldap']==1 && $rparameters['ldap_service']==1)
		{
			echo '
				<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=services";\' class="btn  btn-info">
					<i class="fa fa-sync"></i> '.T_('Synchronisation des services LDAP').'
				</button>
			';
		}
	echo'
			</p>
		</div>
	';
	//Display user table
	if($_GET['way']=='DESC') $nextway='ASC'; else $nextway='DESC'; //find next way

	//build query
	$from='tusers';
	$where='';
	$join='';
	if($rparameters['user_agency']) {$join="LEFT OUTER JOIN tusers_agencies ON tusers_agencies.user_id=tusers.id LEFT OUTER JOIN tagencies ON tagencies.id=tusers_agencies.agency_id ";}
	$join.="
	LEFT OUTER JOIN tusers_services ON tusers_services.user_id=tusers.id 
	LEFT OUTER JOIN tservices ON tservices.id=tusers_services.service_id
	LEFT OUTER JOIN tcompany ON tcompany.id=tusers.company ";
	$where.="
	profile LIKE :profile AND
	tusers.id!=:id AND
	tusers.disable=:disable AND
	tusers.login!='delete_user_gs' AND
	(
	";
	if($rparameters['user_agency']) {$where.="tagencies.name LIKE :agency_name OR ";}
	$where.="
		tusers.lastname LIKE :lastname OR
		tusers.firstname LIKE :firstname OR
		tusers.mail LIKE :mail OR
		tusers.phone LIKE :phone OR
		tusers.mobile LIKE :mobile OR
		tusers.login LIKE :login OR
		tservices.name LIKE :service_name  OR
		tcompany.name LIKE :company
	)
	ORDER BY $db_order $db_way
	LIMIT $db_cursor,$maxline ";
	if($rparameters['debug']) {
		echo "
		<b><u>DEBUG MODE:</u></b><br />
		SELECT distinct tusers.* 
		FROM $from
		$join
		WHERE $where
		";
	}
	echo '
		<div class="mt-0 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
			<div class="card-body p-0 table-responsive-xl">
				<table id="sample-table-1" class="table text-dark-m1 brc-black-tp10 mb-1 table-hover">
					<thead>
						<tr class="bgc-white text-secondary-d3 text-95">
							<th class="py-3 pl-35" style="min-width:220px;">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=lastname&way='.$nextway.'">
									<i class="fa fa-male"></i> '.T_('Nom Prénom').'
									';
										if($_GET['order']=='lastname')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
								</a>
							</th>
							<th class="py-3 pl-35" style="min-width:140px;">
								<a  href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=login&way='.$nextway.'">
									<i class="fa fa-user"></i> '.T_('Identifiant').'
									';
										if($_GET['order']=='login')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
								</a>
							</th>
							';
							if($rparameters['user_advanced']==1)
							{
								echo '
								<th class="py-3 pl-35" style="min-width:120px;">
									<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=company&way='.$nextway.'">
										<i class="fa fa-building "></i> '.T_('Société').'
										';
											if($_GET['order']=='company')
											{
											if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
											if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
											}
										echo '
									</a>
								</th>
								';
							}
							if($rparameters['user_agency']==1)
							{
								echo '
								<th class="py-3 pl-35" style="min-width:130px;">
									<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=tagencies.name&way='.$nextway.'">
										<i class="fa fa-globe "></i> '.T_('Agences').'
										';
											if($_GET['order']=='tagencies.name')
											{
											if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
											if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
											}
										echo '
									</a>
								</th>
								';
							}
							echo '
							<th class="py-3 pl-35" style="min-width:120px;">
								<a class="text-primary-m2"href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=tservices.name&way='.$nextway.'">
									<i class="fa fa-users"></i> '.T_('Services').'
									';
										if($_GET['order']=='tservices.name')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
							</th>
							<th class="py-3 pl-35">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=tusers.mail&way='.$nextway.'">
									<i class="fa fa-envelope"></i> '.T_('Mail').'
									';
										if($_GET['order']=='tusers.mail')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
							</th>
							<th class="py-3 pl-35" style="min-width:140px;">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=phone&way='.$nextway.'">
									<i class="fa fa-phone"></i> '.T_('Téléphone').'
									';
										if($_GET['order']=='phone')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
								</a>
							</th>
							<th class="py-3 pl-35" style="min-width:100px;">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=profile&way='.$nextway.'">
									<i class="fa fa-lock"></i> '.T_('Profil').'
									';
										if($_GET['order']=='profile')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
								</a>
							</th>
							<th class="py-3 pl-35" style="min-width:160px;">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=last_login&way='.$nextway.'">
									<i class="fa fa-key"></i> '.T_('Connexion').'
									';
										if($_GET['order']=='last_login')
										{
										if($_GET['way']=='ASC')  echo '<i class="fa fa-sort-up"></i>';
										if($_GET['way']=='DESC') echo '<i class="fa fa-sort-down"></i>';
										}
									echo '
								</a>
							</th>
							<th class="py-3 pl-35" style="min-width:110px;">
								<a class="text-primary-m2" href="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'&order=lastname&way='.$nextway.'">
									<i class="fa fa-play"></i> '.T_('Actions').'&nbsp;&nbsp;
								</a>
							</th>
						</tr>
					</thead>
					<tbody>';
						
						//build each line
						$qry = $db->prepare("
							SELECT distinct tusers.* 
							FROM $from
							$join
							WHERE $where
						");
						
						
						if($rparameters['user_agency']) //agency case add name in searchengine
						{
							$qry->execute(array(
								'profile' => $_GET['profileid'],
								'id' => 0,
								'disable' => $_GET['disable'],
								'agency_name' => "%$userkeywords%",
								'lastname' => "%$userkeywords%",
								'firstname' => "%$userkeywords%",
								'mail' => "%$userkeywords%",
								'phone' => "%$userkeywords%",
								'mobile' => "%$userkeywords%",
								'login' => "%$userkeywords%",
								'service_name' => "%$userkeywords%",
								'company' => "%$userkeywords%",
							));
						} else {
							$qry->execute(array(
								'profile' => $_GET['profileid'],
								'id' => 0,
								'disable' => $_GET['disable'],
								'lastname' => "%$userkeywords%",
								'firstname' => "%$userkeywords%",
								'mail' => "%$userkeywords%",
								'phone' => "%$userkeywords%",
								'mobile' => "%$userkeywords%",
								'login' => "%$userkeywords%",
								'service_name' => "%$userkeywords%",
								'company' => "%$userkeywords%",
							));
						}
						
						
						while ($row = $qry->fetch()) 
						{
							//find profile name
							$qry2 = $db->prepare("SELECT `name` FROM `tprofiles` WHERE level=:level");
							$qry2->execute(array('level' => $row['profile']));
							$r=$qry2->fetch();
							$qry2->closeCursor();
							//display last login if exist
							if($row['last_login']=='0000-00-00 00:00:00') $lastlogin=''; else $lastlogin=substr($row['last_login'],0,16);
							//first letter of lastname
							$firstname_letter=strtoupper(substr($row['firstname'],0,1));
							if(empty($firstname_letter)) {$firstname_letter='';}
							$lastname_letter=strtoupper(substr($row['lastname'],0,1));
							if(empty($lastname_letter)) {$lastname_letter='';}
							if($row['profile']==0)$lastname_letter_color='bgc-grey';
							if($row['profile']==1)$lastname_letter_color='bgc-green';
							if($row['profile']==2)$lastname_letter_color='bgc-primary';
							if($row['profile']==3)$lastname_letter_color='bgc-orange';
							if($row['profile']==4)$lastname_letter_color='bgc-dark';
							
							//display line <span class="d-inline-block w-4 h-4 '.$lastname_letter_color.' text-white  text-center pt-1 radius-round">'.$firstname_letter.$lastname_letter.'</span>
							echo '
								<tr class="bgc-h-orange-l4">
									<td class="text-secondary-d2 text-95 text-600" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >
										
										<span class="d-inline-block text-center mr-2 pt-1 w-4 h-4 radius-round '.$lastname_letter_color.' text-white font-bolder text-100">'.$firstname_letter.$lastname_letter.'</span>
										'.strtoupper($row['lastname']).' '.ucfirst(substr($row['firstname'],0,1)).mb_strtolower(substr($row['firstname'],1)).' 
									</td>
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.$row['login'].'</td>
									';
									if($rparameters['user_advanced']==1) {
										//get company name
										$qry2 = $db->prepare("SELECT `name` FROM `tcompany` WHERE id=:id");
										$qry2->execute(array('id' => $row['company']));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.$row2['name'].'</td>';
									}
									if($rparameters['user_agency']==1) {
										//get agencies name
										echo '<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >';
										$qry2 = $db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
										$qry2->execute(array('user_id' => $row['id']));
										while ($row2=$qry2->fetch())
										{
											$qry3 = $db->prepare("SELECT `name` FROM `tagencies` WHERE id=:id");
											$qry3->execute(array('id' => $row2['agency_id']));
											$row3 = $qry3->fetch();
											echo "$row3[name]<br />";
											$qry3->closecursor();
										}
										$qry2->closecursor();
										echo '</td>';
									}
									echo '
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >
									';
										$qry2 = $db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
										$qry2->execute(array('user_id' => $row['id']));
										while ($row2=$qry2->fetch())
										{
											$qry3 = $db->prepare("SELECT `name` FROM `tservices` WHERE id=:id");
											$qry3->execute(array('id' => $row2['service_id']));
											$row3 = $qry3->fetch();
											if(empty($row3['name'])) {$row3['name']='';}
											echo "$row3[name]<br />";
											$qry3->closecursor();
										}
										$qry2->closecursor();
									echo'
									</td>
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.$row['mail'].'</td>
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.$row['phone'].' '.$row['mobile'].'</td>
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.T_($r['name']).'</td>
									<td class="text-dark-m3" onclick=\'window.location.href="index.php?page=admin&subpage=user&action=edit&userid='.$row['id'].'&tab=infos";\' >'.$lastlogin.'</td>
									<td>
									
										<a class="action-btn btn btn-sm btn-warning mr-1" href="index.php?page=admin&amp;subpage=user&amp;action=edit&amp;userid='.$row['id'].'&tab=infos"  title="'.T_("Modifier l'utilisateur").'" ><center><i style="color:#FFF;" class="fa fa-pencil-alt"></i></center></a>';
										if(($row['disable']!=1) && ($row['id']!=$_SESSION['user_id']))
										{
											echo '<a class="action-btn btn btn-sm btn-danger" href="index.php?page=admin&amp;subpage=user&amp;userid='.$row['id'].'&amp;action=disable"  title="'.T_("Désactiver l'utilisateur").'" ><center><i class="fa fa-ban "></i></center></a>';
										} elseif($row['id']!=$_SESSION['user_id'])
										{
											echo '<a class="action-btn btn btn-sm btn-success mr-1" href="index.php?page=admin&amp;subpage=user&amp;userid='.$row['id'].'&amp;action=enable"  title="'.T_("Activer l'utilisateur").'" ><center><i class="fa fa-check"></i></center></a>';
										}
										if($rright['admin'] && $row['disable']==1)
										{
											echo '<a class="action-btn btn btn-sm btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer définitivement cet utilisateur ? information également supprimée sur tous les tickets et dans tous le logiciel').'\');" href="index.php?page=admin&amp;subpage=user&amp;userid='.$row['id'].'&amp;action=delete"  title="'.T_("Supprimer l'utilisateur").'" ><center><i class="fa fa-trash"></i></center></a>';
										}
										echo '
									
									</td>
								</tr>
							';
						}
						$qry->closecursor();
						echo '
					</tbody>
				</table>
			</div>
		</div>
	';
	//multi-pages link
	if(!$_GET['cursor'])
	{
		$qry = $db->prepare("
			SELECT COUNT(DISTINCT tusers.id)
			FROM $from
			$join
			WHERE $where
			");
		if($rparameters['user_agency']) //agency case add name in searchengine
		{
			$qry->execute(array(
				'profile' => $_GET['profileid'],
				'id' => 0,
				'disable' => $_GET['disable'],
				'agency_name' => "%$userkeywords%",
				'lastname' => "%$userkeywords%",
				'firstname' => "%$userkeywords%",
				'mail' => "%$userkeywords%",
				'phone' => "%$userkeywords%",
				'mobile' => "%$userkeywords%",
				'login' => "%$userkeywords%",
				'service_name' => "%$userkeywords%",
				'company' => "%$userkeywords%",
			));
		} else {
			$qry->execute(array(
				'profile' => $_GET['profileid'],
				'id' => 0,
				'disable' => $_GET['disable'],
				'lastname' => "%$userkeywords%",
				'firstname' => "%$userkeywords%",
				'mail' => "%$userkeywords%",
				'phone' => "%$userkeywords%",
				'mobile' => "%$userkeywords%",
				'login' => "%$userkeywords%",
				'service_name' => "%$userkeywords%",
				'company' => "%$userkeywords%",
			));
		}
	}
	else
	{
		$qry = $db->prepare("
		SELECT COUNT(DISTINCT tusers.id)
		FROM $from
		$join
		WHERE 
		tusers.disable=:disable
		");
		$qry->execute(array('disable' => $_GET['disable']));
	}
				
	$resultcount = $qry->fetch();
	//multi-pages link
	if($resultcount[0]>$rparameters['maxline'])
	{
		//count number of page
		$total_page=ceil($resultcount[0]/$rparameters['maxline']);
		echo '
		<div class="row justify-content-center mt-4">
			<nav aria-label="Page navigation">
				<ul class="pagination nav-tabs-scroll is-scrollable">';
					//display previous button if it's not the first page
					if($_GET['cursor']!=0)
					{
						$cursor=$_GET['cursor']-$rparameters['maxline'];
						echo '<li class="page-item"><a class="page-link" title="'.T_('Page précédente').'" href="./index.php?page=admin&subpage=user&amp;disable='.$_GET['disable'].'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'"><i class="fa fa-arrow-left"></i></a></li>';
					}
					//display first page
					if($_GET['cursor']==0){$active='active';} else {$active='';}
					echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Première page').'" href="./index.php?page=admin&subpage=user&amp;disable='.$_GET['disable'].'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor=0">&nbsp;1&nbsp;</a></li>';
					//calculate current page
					$current_page=($_GET['cursor']/$rparameters['maxline'])+1;
					//calculate min and max page 
					if(($current_page-3)<3) {$min_page=2;} else {$min_page=$current_page-3;}
					if(($total_page-$current_page)>3) {$max_page=$current_page+4;} else {$max_page=$total_page;}
					//display all pages links
					for ($page = $min_page; $page <= $total_page; $page++) {
						//display start "..." page link
						if(($page==$min_page) && ($current_page>5)){echo '<li class="page-item"><a class="page-link" title="'.T_('Pages masqués').'" href="">&nbsp;...&nbsp;</a></li>';}
						//init cursor
						if($page==1) {$cursor=0;}
						$selectcursor=$rparameters['maxline']*($page-1);
						if($_GET['cursor']==$selectcursor){$active='active';} else	{$active='';}
						$cursor=(-1+$page)*$rparameters['maxline'];
						//display page link
						if($page!=$max_page) echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Page').' '.$page.'" href="./index.php?page=admin&subpage=user&amp;disable='.$_GET['disable'].'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'">&nbsp;'.$page.'&nbsp;</a></li>';
						//display end "..." page link
						if(($page==($max_page-1)) && ($page!=$total_page-1)) {
							echo '<li class="page-item"><a class="page-link" title="'.T_('Pages masqués').'" href="">&nbsp;...&nbsp;</a></li>';
						}
						//cut if there are more than 3 pages
						if($page==($current_page+4)) {
							$page=$total_page;
						} 
					}
					//display last page
					$cursor=($total_page-1)*$rparameters['maxline'];
					if($_GET['cursor']==$selectcursor){$active='active';} else	{$active='';}
					echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Dernière page').'" href="./index.php?page=admin&subpage=user&amp;disable='.$_GET['disable'].'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'">&nbsp;'.$total_page.'&nbsp;</a></li>';
					//display next button if it's not the last page
					if($_GET['cursor']<($resultcount[0]-$rparameters['maxline']))
					{
						$cursor=$_GET['cursor']+$rparameters['maxline'];
						echo '<li class="page-item"><a class="page-link" title="'.T_('Page suivante').'" href="./index.php?page=admin&subpage=user&amp;disable='.$_GET['disable'].'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'"><i class="fa fa-arrow-right"></i></a></li>';
					}
					echo '
				</ul>
			</nav>
		</div>
	';
	}
}
?>
