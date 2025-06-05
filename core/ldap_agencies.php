<?php
####################################################################################
# @Name : ldap_agency.php 
# @Description : Synchronize LDAP group of agencies with GestSup Agency, and members 
# @Call : /admin/user.php
# @Parameters : 
# @Author : Flox
# @Create : 05/04/2017
# @Update : 30/07/2020
# @Version : 3.2.3
####################################################################################

if(!isset($ldap_query)) $ldap_query = '';
if(!isset($find)) $find = '';
if(!isset($dcgen)) $dcgen = '';
if(!isset($find2_login)) $find2_login= '';
if(!isset($update)) $update= '';
if(!isset($find_dpt)) $find_dpt= '';
if(!isset($find_company)) $find_company= '';
if(!isset($samaccountname)) $samaccountname= '';
if(!isset($ldap_type)) $ldap_type= '';
if(!isset($ldap_auth)) $ldap_auth= '';
if(!isset($g_company)) $g_company= '';
if(!isset($cnt_group)) $cnt_group= 0;
if(!isset($cnt_users)) $cnt_users= 0;
if(!isset($cnt_total_users)) $cnt_total_users= 0;
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE']='';

//call via external script for cron 
if(!isset($rparameters['ldap_user']))
{
	require_once(__DIR__."/../core/init_get.php");
	//database connection
	require_once(__DIR__."/../connect.php");
	require_once(__DIR__."/../core/functions.php");
	
	//switch SQL MODE to allow empty values with latest version of MySQL
	$db->exec('SET sql_mode = ""');
	
	//load parameters table
	$qry=$db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//variable
	$_GET['ldap']='1';
	$_GET['action']='run';
	
	//locales
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if ($lang=='fr') {$_GET['lang'] = 'fr_FR';}
	else {$_GET['lang'] = 'en_US';}
	define('PROJECT_DIR', realpath('../'));
	define('LOCALE_DIR', PROJECT_DIR .'/locale');
	define('DEFAULT_LOCALE', '($_GET[lang]');
	require_once(__DIR__.'/../components/php-gettext/gettext.inc');
	$encoding = 'UTF-8';
	$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
	T_setlocale(LC_MESSAGES, $locale);
	T_bindtextdomain($_GET['lang'], LOCALE_DIR);
	T_bind_textdomain_codeset($_GET['lang'], $encoding);
	T_textdomain($_GET['lang']);
	
	//define PHP time zone
	date_default_timezone_set('Europe/Paris');
} else {
	//no external execution
	require_once('./core/init_get.php');
	require_once('./core/functions.php');
}

//check activate function
if(!$rparameters['ldap_agency']) {exit('ERROR : Function disabled');}
	
//LDAP connection parameters
$user=$rparameters['ldap_user']; 
if(preg_match('/gs_en/',$rparameters['ldap_password'])) {$rparameters['ldap_password']=gs_crypt($rparameters['ldap_password'], 'd' , $rparameters['server_private_key']);}
$password=$rparameters['ldap_password']; 
$domain=$rparameters['ldap_domain'];
if($rparameters['ldap_port']==636) {
	putenv('LDAPTLS_REQCERT=never') or die('Failed to setup the env'); //enable AD self sign cert
	$hostname='ldaps://'.$rparameters['ldap_server'];
} else {
	$hostname=$rparameters['ldap_server'];
}

//Generate DC Chain from domain parameter
$dcpart=explode(".",$domain);
$i=0;
while($i<count($dcpart)) {
	$dcgen="$dcgen,dc=$dcpart[$i]";
	$i++;
}
	
//LDAP URL for agencies emplacement
$ldap_agency_url="$rparameters[ldap_agency_url]$dcgen";
$ldap_url="$rparameters[ldap_url]$dcgen";

//display head title
if ($rparameters['ldap_type']==0) $ldap_type='Active Directory'; else $ldap_type='OpenLDAP';
if ($_GET['subpage']=='user')
{
	echo '
	<div class="page-header position-relative">
		<h1 class="page-title text-primary-m2">
			<i class="fa fa-sync"></i>   
			'.T_("Synchronisation des groupes d'agence").': '.$ldap_type.' > GestSup 
		</h1>
	</div>';
}

//LDAP connect
$ldap = ldap_connect($hostname,$rparameters['ldap_port']) or die("Impossible de se connecter au serveur LDAP.");
ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, 1);
if ($rparameters['ldap_type']==1) {ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);}
//check LDAP type for bind
if ($rparameters['ldap_type']==0) $ldapbind = ldap_bind($ldap, "$user@$domain", $password); else $ldapbind = ldap_bind($ldap, "cn=$user$dcgen", $password);	
//check ldap authentication
if ($ldapbind) {$ldap_connection='<i title="'.T_('Connecteur opérationnel').'" class="fa fa-check-circle text-success"></i> '.T_('Connecteur opérationnel').'.';} else {$ldap_connection='<i title="'.T_('Le connecteur ne fonctionne pas vérifier vos paramètres').'" class="fa fa-times-circle text-danger"></i> '.T_('Le connecteur ne fonctionne pas vérifier vos paramètres').'';}
if ($ldapbind) 
{
	$data = array();
	$data_temp = array();
	//ad group filter
	//$filter="(&(objectCategory=group)(cn=*)(!(cn=*OLD*)))";
	$filter="(&(objectCategory=group)(cn=*))";
	$query = ldap_search($ldap, $ldap_agency_url, $filter);
	if($rparameters['debug']){echo "<u>DEBUG:</u><br />query group ldap_search($ldap, $ldap_agency_url, $filter)<br /><br />";}
	//put all data to $data
	$data_temp = @ldap_get_entries($ldap, $query);
	$data = array_merge($data, $data_temp);
	//count LDAP number of groups
	$cnt_group += @ldap_count_entries($ldap, $query);
	
	//count gestsup number of agencies
	$qry=$db->prepare("SELECT COUNT(*) FROM `tagencies` WHERE name!=:name AND disable='0'");
	$qry->execute(array('name' => 'Aucune'));
	$cnt_gestsup=$qry->fetch();
	$qry->closeCursor();
	
	$qry=$db->prepare("SELECT COUNT(*) FROM `tagencies` WHERE name!=:name AND disable='0'");
	$qry->execute(array('name' => 'Aucune'));
	$cnt_gestsup2=$qry->fetch();
	$qry->closeCursor();
	
	echo '<i class="fa fa-book text-success"></i> <b><u>'.T_('Vérification des Annuaires').'</u></b><br />';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;'.T_("Nombre de groupes d'agences trouvés dans l'annuaire LDAP").' '.$ldap_type.': '.$cnt_group.'<br />';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;'.T_("Nombre d'agences trouvés dans GestSup").': '.$cnt_gestsup[0].' '.T_('activées et').' '.$cnt_gestsup2[0].' '.T_('désactivées').'<br /><br />';
	echo '<i class="fa fa-pencil-alt text-danger"></i> <b><u>'.T_('Modifications à apporter dans GestSup').':</u></b><br />';
	$array_ldap_group = array("");
	$array_ldap_user = array("");
	//display all data for debug
	$cnt_total_users=0;
	//for each LDAP group 
	for ($i=0; $i < $cnt_group; $i++) 
	{
		//Initialize variable for empty data
		if(!isset($data[$i]['distinguishedname'][0])) $data[$i]['distinguishedname'][0] = '';
		if(!isset($data[$i]['samaccountname'][0])) $data[$i]['samaccountname'][0] = '';
		if(!isset($data[$i]['objectguid'][0])) $data[$i]['objectguid'][0] = '';
		if(!isset($data[$i]['mail'][0])) $data[$i]['mail'][0] = '';
		
		//get group data from Windows AD & transform in UTF-8
		$LDAP_group_name=utf8_encode($data[$i]['distinguishedname'][0]);
		$LDAP_group_name=str_replace ('','OE', $LDAP_group_name); //special char oe treatment
		$LDAP_group_samaccountname=utf8_encode($data[$i]['samaccountname'][0]);
		$LDAP_group_samaccountname=str_replace ('','OE', $LDAP_group_samaccountname); //special char oe treatment
		$LDAP_group_mail=utf8_encode($data[$i]['mail'][0]);
		$LDAP_group_objectguid=unpack("H*hex",$data[$i]['objectguid'][0]);
		$LDAP_group_objectguid=$LDAP_group_objectguid['hex'];
		if($rparameters['debug']) {echo "<u>LDAP_group_name=<b>$LDAP_group_samaccountname</b> (<font size=\"1\">GUID: $LDAP_group_objectguid</font>):</u> ";}
		
		//keep agency guid to disable GS agency
		array_push($array_ldap_group, "$LDAP_group_objectguid", "$LDAP_group_samaccountname");
		
		//compare GS database & LDAP directory
		$qry=$db->prepare("SELECT `id`,`name`,`mail`,`ldap_guid` FROM `tagencies` WHERE ldap_guid=:ldap_guid");
		$qry->execute(array('ldap_guid' => $LDAP_group_objectguid));
		$GS_group=$qry->fetch();
		$qry->closeCursor();
		
		if($_GET['action']=='simul')
		{
			if(!$GS_group[0])
			{
				//insert new agency in GS db
				echo '<i class="fa fa-plus-circle text-success"></i><font class="text-success"> '.T_("Création de l'agence").': <b>'.$LDAP_group_samaccountname.'</b> (<font size="1">'.T_("Raison: Le guid du groupe LDAP n'a pas été trouvé dans la liste des agences GestSup").'</font>)</font><br />';
			} else {
				//check update group name in GS db
				if ($GS_group['name']!=$LDAP_group_samaccountname || $GS_group['mail']!=$LDAP_group_mail)
				{ 
					echo '<br /><i class="fa fa-arrow-circle-up text-success"></i><font class="text-success"> '.T_("Mise à jour du nom de l'agence").': <b>'.$LDAP_group_samaccountname.'</b> (<font size="1">'.T_("Raison: Un guid commun à été trouvé et le nom $GS_group[name] est différent ou l'adresse mail est différente").'</font>)</font><br />';
					if (preg_match("#_OLD#",$LDAP_group_samaccountname)) {echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Désactivation de l'agence").': <b>'.$LDAP_group_samaccountname.'</b> (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Le nom de l'agence comporte les lettres _OLD").'.</span></font>)<br />';}
				}
			}
		} elseif($_GET['action']=='run')
		{
			if(!isset($GS_group[0]))
			{
				//insert new agency in GS db
				echo '<br /><i class="fa fa-plus-circle text-success"></i><font class="text-success"> '.T_("Agence").': <b>'.$LDAP_group_samaccountname.'</b> '.T_("crée").' (<font size="1">'.T_("Raison: Le guid du groupe LDAP n'a pas été trouvé dans la liste des agences GestSup").'</font>)</font><br />';
				$qry=$db->prepare("INSERT INTO `tagencies` (`name`,`mail`,`ldap_guid`) VALUES (:name,:mail,:ldap_guid)");
				$qry->execute(array('name' => $LDAP_group_samaccountname,'mail' => $LDAP_group_mail,'ldap_guid' => $LDAP_group_objectguid));
				logit('ldap_agency','Create agency '.$LDAP_group_samaccountname,'0');
			} else {
				//check update group name in GS db
				if (($GS_group['name']!=$LDAP_group_samaccountname) || $GS_group['mail']!=$LDAP_group_mail)
				{
					echo '<br /><i class="fa fa-arrow-circle-up text-success"></i><font class="text-success"> '.T_("Le nom de l'agence").': <b>'.$LDAP_group_samaccountname.'</b> '.T_("à été mise à jour").' (<font size="1">'.T_("Raison: Un guid commun à été trouvé et le nom $GS_group[name] est différent ou l'adresse mail est différente").'</font>)</font><br />';
					$qry=$db->prepare("UPDATE `tagencies` SET `name`=:name,`mail`=:mail,`disable`='0' WHERE `ldap_guid`=:ldap_guid");
					$qry->execute(array('name' => $LDAP_group_samaccountname,'mail' => $LDAP_group_mail,'ldap_guid' => $LDAP_group_objectguid));
					logit('ldap_agency','Update agency name '.$LDAP_group_samaccountname.' or update agency mail','0');
					if (preg_match("#_OLD#",$LDAP_group_samaccountname)) {
						echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Agence").': <b>'.$LDAP_group_samaccountname.'</b> '.T_("désactivée").' (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Le nom de l'agence comporte les lettres _OLD").'.</span></font>)<br />';
						$qry=$db->prepare("UPDATE `tagencies` SET `disable`='1' WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('ldap_guid' => $LDAP_group_objectguid));
						logit('ldap_agency','Disable agency '.$LDAP_group_samaccountname,'0');
					}
				}
			}
		}
		//get members of this group
		$cnt_users=0;
		$data2 = array();
		$data2_temp = array();
		$filter2="(&(objectCategory=user)(memberof:1.2.840.113556.1.4.1941:=$LDAP_group_name))";
		$query2 = ldap_search($ldap, $ldap_url, $filter2);
		if($rparameters['debug']){echo "<font size='1'>query find users ldap_search($ldap, $ldap_url, $filter2)</font><br />";}
		//put all data to $data2
		$data2_temp = @ldap_get_entries($ldap, $query2);
		$data2 = array_merge($data2, $data2_temp);
		//count LDAP number of user
		$cnt_users += @ldap_count_entries($ldap, $query2);
		//display all group data for debug
		for ($i2=0; $i2 < $cnt_users; $i2++) 
		{
			
			//Initialize variable for empty data
			if(!isset($data2[$i2]['cn'][0])) $data2[$i2]['cn'][0] = '';
			if(!isset($data2[$i2]['samaccountname'][0])) $data2[$i2]['samaccountname'][0] = '';
			if(!isset($data2[$i2]['givenname'][0])) $data2[$i2]['givenname'][0] = '';
			if(!isset($data2[$i2]['sn'][0])) $data2[$i2]['sn'][0] = '';
			if(!isset($data2[$i2]['objectguid'][0])) $data2[$i2]['objectguid'][0] = '';
			if(!isset($data2[$i2]['useraccountcontrol'][0])) $data2[$i2]['useraccountcontrol'][0] = '';
			if(!isset($data2[$i2]['mail'][0])) $data2[$i2]['mail'][0] = '';
			if(!isset($data2[$i2]['telephonenumber'][0])) $data2[$i2]['telephonenumber'][0] = '';
			if(!isset($data2[$i2]['streetaddress'][0])) $data2[$i2]['streetaddress'][0] = '';
			if(!isset($data2[$i2]['postalcode'][0])) $data2[$i2]['postalcode'][0] = '';
			if(!isset($data2[$i2]['l'][0])) $data2[$i2]['l'][0] = '';
			// OC 05052025 set default value for company to 1, to avoid mysql update later after a user creation
			if(!isset($data2[$i2]['company'][0])) $data2[$i2]['company'][0] = '1';
			if(!isset($data2[$i2]['facsimiletelephonenumber'][0])) $data2[$i2]['facsimiletelephonenumber'][0] = '';
			if(!isset($data2[$i2]['title'][0])) $data2[$i2]['title'][0] = '';
			//get data from table data to variables
			$LDAP_user_guid=utf8_encode($data2[$i2]['objectguid'][0]);
			$LDAP_user_guid=unpack("H*hex",$data2[$i2]['objectguid'][0]);
			$LDAP_user_guid=$LDAP_user_guid['hex'];
			$LDAP_user_guid=str_replace(' ','', $LDAP_user_guid);
			$LDAP_user_cn=utf8_encode($data2[$i2]['cn'][0]);
			$LDAP_user_samaccountname=utf8_encode($data2[$i2]['samaccountname'][0]);
			$LDAP_user_givenname=utf8_encode($data2[$i2]['givenname'][0]);
			$LDAP_user_sn=utf8_encode($data2[$i2]['sn'][0]);
			$LDAP_user_uac=$data2[$i2]['useraccountcontrol'][0];
			$LDAP_user_mail=$data2[$i2]['mail'][0];
			$LDAP_user_telephonenumber=$data2[$i2]['telephonenumber'][0];  
			$LDAP_user_streetaddress=utf8_encode($data2[$i2]['streetaddress'][0]);  
			$LDAP_user_postalcode=$data2[$i2]['postalcode'][0]; 
			$LDAP_user_l=utf8_encode($data2[$i2]['l'][0]); 
			$LDAP_user_company=utf8_encode($data2[$i2]['company'][0]); 
			$LDAP_user_fax=$data2[$i2]['facsimiletelephonenumber'][0]; 
			$LDAP_user_title=utf8_encode($data2[$i2]['title'][0]); 
			
			if($rparameters['debug']) {echo "<font size=\"1\"> [<b>$LDAP_user_samaccountname</b>] LDAP DATA: guid=$LDAP_user_guid cn=$LDAP_user_cn samaccountname=$LDAP_user_samaccountname givenname=$LDAP_user_givenname sn=$LDAP_user_sn uac=$LDAP_user_uac mail=$LDAP_user_mail telephonenumber=$LDAP_user_telephonenumber streetaddress=$LDAP_user_streetaddress postalcode=$LDAP_user_postalcode l=$LDAP_user_l company=$LDAP_user_company fax=$LDAP_user_fax title=$LDAP_user_title</font><br>";}
			$cnt_total_users++;
			
			//user update and create and assoc with agency
			$find_user=0;
			$qry2=$db->prepare("SELECT * FROM `tusers` WHERE ldap_guid=:ldap_guid");
			$qry2->execute(array('ldap_guid' => $LDAP_user_guid));
			$GS_user=$qry2->fetch();
			$qry2->closeCursor();
			
			if ($GS_user)
			{
				//push data in array to remove user from agency in last part
				$GS_agency_id=$GS_group['id'];
				$GS_user_id=$GS_user['id'];
				$array_agency_members[$GS_agency_id][]=$GS_user_id;
				
				//update user if LDAP information is available
				$user_update='';
				if ($_GET['action']=='simul')
				{	
					if($LDAP_user_samaccountname!=$GS_user['login']) {$user_update=T_("l'identifiant").',';}
					if($LDAP_user_givenname!=$GS_user['firstname']) {$user_update.=T_("le prénom").',';}
					if($LDAP_user_sn!=$GS_user['lastname']) {$user_update.=T_("le nom").',';}
					if($LDAP_user_mail!=$GS_user['mail']) {$user_update.=T_("le mail").',';}
					if($LDAP_user_telephonenumber!=$GS_user['phone']) {$user_update.=T_("le téléphone").',';}
					if($LDAP_user_streetaddress!=$GS_user['address1']) {$user_update.=T_("l'adresse").',';}
					if($LDAP_user_postalcode!=$GS_user['zip']) {$user_update.=T_("le code postal").',';}
					if($LDAP_user_l!=$GS_user['city']) {$user_update.=T_("la ville").',';}
					if($LDAP_user_fax!=$GS_user['fax']) {$user_update.=T_("le FAX").',';}
					if($LDAP_user_title!=$GS_user['function']) {$user_update.=T_("la fonction");}
				
					//update user agency association
					$qry2=$db->prepare("SELECT `id` FROM `tagencies` WHERE ldap_guid=:ldap_guid");
					$qry2->execute(array('ldap_guid' => $LDAP_group_objectguid));
					$GS_agency=$qry2->fetch();
					$qry2->closeCursor();
					
					//check if agency guid exit in GS database
					$qry2=$db->prepare("SELECT `id` FROM `tusers_agencies` WHERE user_id=:user_id AND agency_id=:agency_id");
					$qry2->execute(array('user_id' => $GS_user['id'],'agency_id' => $GS_agency['id']));
					$assoc=$qry2->fetch();
					$qry2->closeCursor();
					
					if (isset($assoc[0])==0){$user_update.=T_("l'association avec l'agence.");}
					
					if($user_update) {echo '<i class="fa fa-arrow-circle-up text-success"></i><font class="text-success"> '.T_("Mise à jour de l'utilisateur").': <b>'.$LDAP_user_samaccountname.'</b> (<font size="1">'.T_("Raison: Le guid LDAP est identique à celui de GestSup et une différence à été trouvé dans ").' '.$user_update.'</font>)</font><br />';}

				} elseif($_GET['action']=='run') {
					//update GS user informations
					if($LDAP_user_samaccountname!=$GS_user['login']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `login`=:login WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('login' => $LDAP_user_samaccountname,'ldap_guid' => $LDAP_user_guid));
						$user_update=T_("l'identifiant").',';
					}
					if($LDAP_user_givenname!=$GS_user['firstname']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `firstname`=:firstname WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('firstname' => $LDAP_user_givenname,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le prénom").',';
					}
					if($LDAP_user_sn!=$GS_user['lastname']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `lastname`=:lastname WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('lastname' => $LDAP_user_sn,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le nom").',';
					}
					if($LDAP_user_mail!=$GS_user['mail']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `mail`=:mail WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('mail' => $LDAP_user_mail,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le mail").',';
					}
					if($LDAP_user_telephonenumber!=$GS_user['phone']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `phone`=:phone WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('phone' => $LDAP_user_telephonenumber,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le téléphone").',';
					}
					if($LDAP_user_streetaddress!=$GS_user['address1']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `address1`=:address1 WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('address1' => $LDAP_user_streetaddress,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("l'adresse").',';
					}
					if($LDAP_user_postalcode!=$GS_user['zip']) 
					{
						$qry=$db->prepare("UPDATE `tusers` SET `zip`=:zip WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('zip' => $LDAP_user_postalcode,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le code postal").',';
					}
					if($LDAP_user_l!=$GS_user['city']) {
						$qry=$db->prepare("UPDATE `tusers` SET `city`=:city WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('city' => $LDAP_user_l,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("la ville").',';
					}
					if($LDAP_user_fax!=$GS_user['fax']) {
						$qry=$db->prepare("UPDATE `tusers` SET `fax`=:fax WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('fax' => $LDAP_user_fax,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("le FAX").',';
					}
					if($LDAP_user_title!=$GS_user['function']) {
						$qry=$db->prepare("UPDATE `tusers` SET `function`=:function WHERE `ldap_guid`=:ldap_guid");
						$qry->execute(array('function' => $LDAP_user_title,'ldap_guid' => $LDAP_user_guid));
						$user_update.=T_("la fonction");
					}
					
					//update user agency association
					$qry2=$db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE ldap_guid=:ldap_guid");
					$qry2->execute(array('ldap_guid' => $LDAP_group_objectguid));
					$GS_agency=$qry2->fetch();
					$qry2->closeCursor();
					
					$qry2=$db->prepare("SELECT `id` FROM `tusers_agencies` WHERE user_id=:user_id AND agency_id=:agency_id");
					$qry2->execute(array('user_id' => $GS_user['id'],'agency_id' => $GS_agency['id']));
					$assoc=$qry2->fetch();
					$qry2->closeCursor();
					
					if (isset($assoc[0])==0)
					{
						$user_update.=T_("l'association avec l'agence.");
						$qry=$db->prepare("INSERT INTO `tusers_agencies` (`user_id`,`agency_id`) VALUES (:user_id,:agency_id)");
						$qry->execute(array('user_id' => $GS_user['id'],'agency_id' => $GS_agency['id']));
						logit('ldap_agency','Update association agency between user '.$GS_user['login'].' and agency '.$GS_agency['name'],'0');
					} 
					if($user_update) {
						echo '<i class="fa fa-arrow-circle-up text-success"></i><font class="text-success"> '.T_("Utilisateur").': <b>'.$LDAP_user_samaccountname.'</b> '.T_("mis à jour").' (<font size="1">'.T_("Raison: Le guid LDAP est identique à celui de GestSup et une différence à été trouvé dans ").' '.$user_update.'</font>)</font><br />';
						logit('ldap_agency','Update user informations from LDAP for user '.$LDAP_user_samaccountname.' ('.$user_update.')','0');
					}

				}
			} else {
				//create user
				if ($_GET['action']=='simul')
				{
					echo '<i class="fa fa-plus-circle text-success"></i><font class="text-success"> '.T_("Création de l'utilisateur").': <b>'.$LDAP_user_samaccountname.'</b> (<font size="1">'.T_("Raison: Le guid de l'utilisateur trouvé dans l'annuaire LDAP n'est pas présent dans GestSup").'</font>)</font><br />';
				}elseif ($_GET['action']=='run')
				{
					echo '<i class="fa fa-plus-circle text-success"></i><font class="text-success"> '.T_("Utilisateur").': <b>'.$LDAP_user_samaccountname.'</b> '.T_("crée").' (<font size="1">'.T_("Raison: Le guid de l'utilisateur trouvé dans l'annuaire LDAP n'est pas présent dans GestSup").'</font>)</font><br />';	
					//db insert
					$qry=$db->prepare("
					INSERT INTO `tusers` (`login`,`firstname`,`lastname`,`profile`,`mail`,`phone`,`address1`,`zip`,`city`,`fax`,`ldap_guid`,`company`) 
					VALUES (:login,:firstname,:lastname,'2',:mail,:phone,:address1,:zip,:city,:fax,:ldap_guid,:company)
					");
					$qry->execute(array(
						'login' => $LDAP_user_samaccountname,
						'firstname' => $LDAP_user_givenname,
						'lastname' => $LDAP_user_sn,
						'mail' => $LDAP_user_mail,
						'phone' => $LDAP_user_telephonenumber,
						'address1' => $LDAP_user_streetaddress,
						'zip' => $LDAP_user_postalcode,
						'city' => $LDAP_user_l,
						'fax' => $LDAP_user_fax,
						'company' => $LDAP_user_company,
						'ldap_guid' => $LDAP_user_guid
						));
					logit('ldap_agency','Create user '.$LDAP_user_samaccountname,'0');
				}
			}	
		}
		if ($rparameters['debug']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- TOTAL users for agency '.$LDAP_group_samaccountname.': '.$cnt_users.'<br />';}
	}
	
	//get all users from root user LDAP UO to check both agency group and service group, place all result in array for next step 
	$cnt_users=0;
	$data3 = array();
	$data3_temp = array();
	$filter3="(&(objectClass=user)(objectCategory=person)(cn=*))";
	if($rparameters['debug']){echo "<font size='1'>query find users ldap_search($ldap, $ldap_url, $filter3)</font><br />";}
	$query3 = ldap_search($ldap, $ldap_url, $filter3);
	//put all data to $data3
	$data3_temp = @ldap_get_entries($ldap, $query3);
	$data3 = array_merge($data3, $data3_temp);
	//count LDAP number of user
	$cnt_users += @ldap_count_entries($ldap, $query3);
	//display all group data for debug
	for ($i3=0; $i3 < $cnt_users; $i3++) 
	{
		if(!isset($data3[$i3]['objectguid'][0])) $data3[$i3]['objectguid'][0] = '';
		$LDAP_user_guid=utf8_encode($data3[$i3]['objectguid'][0]);
		$LDAP_user_guid=unpack("H*hex",$data3[$i3]['objectguid'][0]);
		$LDAP_user_guid=$LDAP_user_guid['hex'];
		$LDAP_user_guid=str_replace(' ','', $LDAP_user_guid);
		//push in array all ldap user guid for delete check on next step
		array_push($array_ldap_user, "$LDAP_user_guid");
	}
	
	//for each GS user
	$qry=$db->prepare("SELECT `id`,`login`,`ldap_guid` FROM `tusers` WHERE disable='0'"); //for each gestsup agency
	$qry->execute();
	while ($row=$qry->fetch())	
	{
		//remove user from GS agency if not present in LDAP agency
		$qry2=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE tusers_agencies.user_id=:user_id"); //for each gestsup agency
		$qry2->execute(array('user_id' => $row['id']));
		while($row2=$qry2->fetch()) 
		{
			//init var
			if(!array_key_exists($row2['agency_id'], $array_agency_members)){$array_agency_members[$row2['agency_id']]=array();}
			if (!in_array("$row[id]", $array_agency_members[$row2['agency_id']])) {
				//get agency name to display
				$qry3=$db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE id=:id");
				$qry3->execute(array('id' => $row2['agency_id']));
				$row3=$qry3->fetch();
				$qry3->closeCursor();
				
				if($_GET['action']=='simul')
				{
					echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Suppression de l'utilisateur ").': <b>'.$row['login'].'</b> '.T_("de l'agence").' <b>'.$row3['name'].'</b> (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Utilisateur présent dans l'agence GestSup mais pas dans l'agence LDAP").'.</span></font>)<br />';
				} elseif($_GET['action']=='run')
				{
					echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("L'utilisateur ").': <b>'.$row['login'].'</b> '.T_("à été supprimé de l'agence").' <b>'.$row3['name'].'</b> (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Utilisateur présent dans l'agence GestSup mais pas dans l'agence LDAP").'.</span></font>)<br />';
					$qry4=$db->prepare("DELETE FROM `tusers_agencies` WHERE user_id=:user_id AND agency_id=:agency_id");
					$qry4->execute(array('user_id' => $row['id'],'agency_id' => $row2['agency_id']));
					logit('ldap_agency','Delete agency association between user '.$row['login'].' and agency id '.$row3['name'],'0');
				}
			}
		}
		$qry2->closecursor();
		
		//disable user in GS if not present in LDAP
		if (!in_array($row['ldap_guid'], $array_ldap_user)) {
			if($_GET['action']=='simul')
			{
				echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Désactivation de l'utilisateur ").': <b>'.$row['login'].'</b> (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Utilisateur présent dans GestSup mais pas dans l'annuaire LDAP").'.</span></font>)<br />';
			} elseif($_GET['action']=='run')
			{
				echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("L'utilisateur ").': <b>'.$row['login'].'</b> '.T_("à été désactivé ").' (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Utilisateur présent dans GestSup mais pas dans l'annuaire LDAP").'.</span></font>)<br />';
				$qry2=$db->prepare("UPDATE `tusers` SET `disable`='1' WHERE `id`=:id");
				$qry2->execute(array('id' => $row['id']));
				logit('ldap_agency','Disable user '.$row['login'],'0');
			}
		}
	}
	$qry->closeCursor();
	
	
	//disable group in GS if not present in LDAP
	$qry=$db->prepare("SELECT `ldap_guid`,`name`,`id` FROM `tagencies` WHERE disable='0'");
	$qry->execute();
	while($row=$qry->fetch()) 
	{
		$find=0;
		foreach($array_ldap_group as $ldap_group)
		{
			if ($row['ldap_guid']==$ldap_group) {$find=1;}
		}
		if ($find==0) {
			if($_GET['action']=='simul')
			{
				echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Désactivation de l'agence").': <b>'.$row['name'].'</b> (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Agence non présente dans l'annuaire LDAP ou renommé en OLD").'.</span></font>)<br />';
			} elseif($_GET['action']=='run')
			{
				echo '<i class="fa fa-times-circle text-danger"></i><font class="text-danger"> '.T_("Agence").': <b>'.$row['name'].'</b> '.T_("désactivée").' (<span style="font-size: x-small;">'.T_('Raison').': '.T_("Agence non présente dans l'annuaire LDAP ou renommé en OLD").'.</span></font>)<br />';
				$qry2=$db->prepare("UPDATE `tagencies` SET `disable`='1' WHERE `id`=:id");
				$qry2->execute(array('id' => $row['id']));
				logit('ldap_agency','Disable agency '.$row['name'],'0');
			}
		}
	}
	$qry->closecursor();
	
	//unbind LDAP server
	ldap_unbind($ldap);
}

echo'
	<br />
	<br />
	<br />
	<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=agencies&amp;action=simul"\' type="submit" class="btn btn-primary">
		<i class="fa fa-flask"></i>
		'.T_('Lancer une simulation').'
	</button>
	<button onclick=\'window.location.href="index.php?page=admin&amp;subpage=user&amp;ldap=agencies&amp;action=run"\' type="submit" class="btn btn-primary">
		<i class="fa fa-bolt"></i>
		'.T_('Lancer la synchronisation').'
	</button>
	<button onclick=\'window.location.href="index.php?page=admin&subpage=user"\' type="submit" class="btn btn-primary btn-danger">
		<i class="fa fa-reply"></i>
		'.T_('Retour').'
	</button>					
';
?>