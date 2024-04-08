<?php
################################################################################
# @Name : parameters.php
# @Description : admin parameters
# @Call : /admin.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

require_once('models/request/purchase_order/code_nacre.php');
require_once('models/request/mission_order/business_address.php');
require_once('models/tool/parameters.php');

use Models\Request\PurchaseOrder\CodeNacre;
use Models\Request\MissionOrder\BusinessAddress;
use Models\Tool\Parameters;

$errorMessage = '';

// Load extra parameters for request
$parameters = Parameters::getInstance();

//initialize variables 
if(!isset($extensionFichier)) $extensionFichier = '';
if(!isset($id_)) $id_ = '';
if(!isset($logo)) $logo = '';
if(!isset($filename)) $filename = '';
if(!isset($file_rename)) $file_rename = '';
if(!isset($mail_auto)) $mail_auto = '';
if(!isset($user_advanced)) $user_advanced= '';
if(!isset($mail_auth)) $mail_auth= '';
if(!isset($mail_secure)) $mail_secure= '';
if(!isset($nomorigine)) $nomorigine = '';
if(!isset($action)) $action = '';
if(!isset($error)) $error = '';
if(!isset($_POST['maxline'])) $_POST['maxline'] = '';
if(!isset($_POST['submit_general'])) $_POST['submit_general'] = '';
if(!isset($_POST['ticket_type'])) $_POST['ticket_type'] = '';
if(!isset($_POST['ticket_autoclose'])) $_POST['ticket_autoclose'] = '';
if(!isset($_POST['ticket_autoclose_delay'])) $_POST['ticket_autoclose_delay'] = '';
if(!isset($_POST['ticket_autoclose_state'])) $_POST['ticket_autoclose_state'] = '';
if(!isset($_POST['ticket_cat_auto_attribute'])) $_POST['ticket_cat_auto_attribute'] = '';
if(!isset($_POST['ticket_increment_number'])) $_POST['ticket_increment_number'] = '';
if(!isset($_POST['submit_connector'])) $_POST['submit_connector'] = '';
if(!isset($_POST['submit_function'])) $_POST['submit_function'] = '';
if(!isset($_POST['mail_username'])) $_POST['mail_username'] = '';
if(!isset($_POST['mail_password'])) $_POST['mail_password'] = '';
if(!isset($_POST['mail_secure'])) $_POST['mail_secure'] = '';
if(!isset($_POST['user_advanced'])) $_POST['user_advanced'] = '';
if(!isset($_POST['user_register'])) $_POST['user_register'] = '';
if(!isset($_POST['user_limit_ticket'])) $_POST['user_limit_ticket'] = '';
if(!isset($_POST['company_limit_ticket'])) $_POST['company_limit_ticket'] = '';
if(!isset($_POST['company_limit_hour'])) $_POST['company_limit_hour'] = '';
if(!isset($_POST['user_company_view'])) $_POST['user_company_view'] = '';
if(!isset($_POST['user_agency'])) $_POST['user_agency'] = '';
if(!isset($_POST['user_limit_service'])) $_POST['user_limit_service'] = '';
if(!isset($_POST['user_disable_attempt'])) $_POST['user_disable_attempt'] = '';
if(!isset($_POST['user_disable_attempt_number'])) $_POST['user_disable_attempt_number'] = '';
if(!isset($_POST['user_password_policy'])) $_POST['user_password_policy'] = '';
if(!isset($_POST['user_password_policy_min_lenght'])) $_POST['user_password_policy_min_lenght'] = '';
if(!isset($_POST['user_password_policy_special_char'])) $_POST['user_password_policy_special_char'] = '';
if(!isset($_POST['user_password_policy_min_maj'])) $_POST['user_password_policy_min_maj'] = '';
if(!isset($_POST['user_password_policy_expiration'])) $_POST['user_password_policy_expiration'] = '';
if(!isset($_POST['user_forgot_pwd'])) $_POST['user_forgot_pwd'] = '';
if(!isset($_POST['mail'])) $_POST['mail']= '';
if(!isset($_POST['mail_cci'])) $_POST['mail_cci']= '';
if(!isset($_POST['mail_auth'])) $_POST['mail_auth']= '';
if(!isset($_POST['mail_auto'])) $_POST['mail_auto']= '';
if(!isset($_POST['mail_auto_tech_modify'])) $_POST['mail_auto_tech_modify']= '';
if(!isset($_POST['mail_auto_tech_attribution'])) $_POST['mail_auto_tech_attribution']= '';
if(!isset($_POST['mail_auto_user_modify'])) $_POST['mail_auto_user_modify']= '';
if(!isset($_POST['mail_auto_user_newticket'])) $_POST['mail_auto_user_newticket']= '';
if(!isset($_POST['mail_newticket'])) $_POST['mail_newticket']= '';
if(!isset($_POST['mail_newticket_address'])) $_POST['mail_newticket_address']= '';
if(!isset($_POST['mail_template'])) $_POST['mail_template']= '';
if(!isset($_POST['mail_link'])) $_POST['mail_link']= '';
if(!isset($_POST['mail_smtp'])) $_POST['mail_smtp']= '';
if(!isset($_POST['mail_smtp_class'])) $_POST['mail_smtp_class']= '';
if(!isset($_POST['mail_port'])) $_POST['mail_port']= '';
if(!isset($_POST['mail_ssl_check'])) $_POST['mail_ssl_check']= '';
if(!isset($_POST['mail_order'])) $_POST['mail_order']= '';
if(!isset($_POST['ldap'])) $_POST['ldap']= '';
if(!isset($_POST['ldap_auth'])) $_POST['ldap_auth']= '';
if(!isset($_POST['ldap_sso'])) $_POST['ldap_sso']= '';
if(!isset($_POST['ldap_type'])) $_POST['ldap_type']= '';
if(!isset($_POST['ldap_login_field'])) $_POST['ldap_login_field']= '';
if(!isset($_POST['ldap_service'])) $_POST['ldap_service']= '';
if(!isset($_POST['ldap_service_url'])) $_POST['ldap_service_url']= '';
if(!isset($_POST['ldap_agency'])) $_POST['ldap_agency']= '';
if(!isset($_POST['ldap_agency_url'])) $_POST['ldap_agency_url']= '';
if(!isset($_POST['from_agency'])) $_POST['from_agency']= '';
if(!isset($_POST['dest_agency'])) $_POST['dest_agency']= '';
if(!isset($_POST['ldap_server'])) $_POST['ldap_server']= '';
if(!isset($_POST['ldap_server_url'])) $_POST['ldap_server_url']= '';
if(!isset($_POST['ldap_port'])) $_POST['ldap_port']= '';
if(!isset($_POST['ldap_domain'])) $_POST['ldap_domain']= '';
if(!isset($_POST['ldap_url'])) $_POST['ldap_url']= '';
if(!isset($_POST['ldap_user'])) $_POST['ldap_user']= '';
if(!isset($_POST['ldap_password'])) $_POST['ldap_password']= '';
if(!isset($_POST['ldap_disable_user'])) $_POST['ldap_disable_user']= '';
if(!isset($_POST['test_ldap'])) $_POST['test_ldap']= '';
if(!isset($_POST['planning'])) $_POST['planning']= '';
if(!isset($_POST['debug'])) $_POST['debug']= '';
if(!isset($_POST['notify'])) $_POST['notify']= '';
if(!isset($_POST['imap'])) $_POST['imap']= '';
if(!isset($_POST['imap_server'])) $_POST['imap_server']= '';
if(!isset($_POST['imap_port'])) $_POST['imap_port']= '';
if(!isset($_POST['imap_user'])) $_POST['imap_user']= '';
if(!isset($_POST['imap_ssl_check'])) $_POST['imap_ssl_check']= '';
if(!isset($_POST['imap_password'])) $_POST['imap_password']= '';
if(!isset($_POST['imap_reply'])) $_POST['imap_reply']= '';
if(!isset($_POST['imap_blacklist'])) $_POST['imap_blacklist']= '';
if(!isset($_POST['imap_post_treatment'])) $_POST['imap_post_treatment']= '';
if(!isset($_POST['imap_post_treatment_folder'])) $_POST['imap_post_treatment_folder']= '';
if(!isset($_POST['imap_mailbox_service'])) $_POST['imap_mailbox_service']= '';
if(!isset($_POST['imap_inbox'])) $_POST['imap_inbox']= '';
if(!isset($_POST['mailbox_service'])) $_POST['mailbox_service']= '';
if(!isset($_POST['mailbox_service_id'])) $_POST['mailbox_service_id']= '';
if(!isset($_POST['inbox'])) $_POST['inbox']= '';
if(!isset($_POST['procedure'])) $_POST['procedure']= '';
if(!isset($_POST['survey'])) $_POST['survey']= '';
if(!isset($_POST['survey_mail_text'])) $_POST['survey_mail_text']= '';
if(!isset($_POST['survey_ticket_state'])) $_POST['survey_ticket_state']= '';
if(!isset($_POST['survey_auto_close_ticket'])) $_POST['survey_auto_close_ticket']= '';
if(!isset($_POST['project'])) $_POST['project']= '';
if(!isset($_POST['ticket_places'])) $_POST['ticket_places']= '';
if(!isset($_POST['ticket_default_state'])) $_POST['ticket_default_state']= '';
if(!isset($_POST['availability'])) $_POST['availability']= '';
if(!isset($_POST['asset'])) $_POST['asset']= '';
if(!isset($_POST['asset_ip'])) $_POST['asset_ip']= '';
if(!isset($_POST['asset_warranty'])) $_POST['asset_warranty']= '';
if(!isset($_POST['asset_vnc_link'])) $_POST['asset_vnc_link']= '';
if(!isset($_POST['availability_all_cat'])) $_POST['availability_all_cat']= '';
if(!isset($_POST['category'])) $_POST['category']= '';
if(!isset($_POST['depcategory'])) $_POST['depcategory']= '';
if(!isset($_POST['meta_state'])) $_POST['meta_state']= '';
if(!isset($_POST['availability_dep'])) $_POST['availability_dep']= '';
if(!isset($_POST['availability_condition_type'])) $_POST['availability_condition_type']= $rparameters['availability_condition_type'];
if(!isset($_POST['availability_condition_value'])) $_POST['availability_condition_value']= $rparameters['availability_condition_type'];
if(!isset($_POST['timeout'])) $_POST['timeout']= '';
if(!isset($_POST['log'])) $_POST['log']= '';
if(!isset($_POST['notification-state'])) $_POST['notification-state']= '';
if(!isset($_POST['notification-enable'])) $_POST['notification-enable']= '';
if(!isset($_FILES['logo']['name'])) $_FILES['logo']['name'] = '';
if(!isset($_FILES['asset_import']['name'])) $_FILES['asset_import']['name'] = '';
if(!isset($_FILES['code-nacre']['name'])) $_FILES['code-nacre']['name'] = '';
if(!isset($_FILES['business-address']['name'])) $_FILES['business-address']['name'] = '';
if(!isset($_GET['error-message'])) $_GET['error-message']='';
//!\Ajouter par nous
if(!isset($_POST['alerte-demande'])) $_POST['alerte-demande']= '';
if(!isset($_POST['interval-alerte-demande'])) $_POST['interval-alerte-demande']= '';
if(!isset($_POST['fixed-validators'])) $_POST['fixed-validators']= '';

//default value
if($_POST['maxline']==0) {$_POST['maxline']=14;}
if($_GET['tab']=='') $_GET['tab'] = 'general';

if ($_POST['notification-state'] || $_POST['notification-enable'] || $_POST['alerte-demande'] || $_POST['fixed-validators']) {
    // save end status for notification in parameters
    $parameters
        ->getDatas($_POST)
        ->save();
}

//delete logo file
if($_GET['action']=="deletelogo" && $rright['admin']!=0)
{
	$qry=$db->prepare("UPDATE `tparameters` SET `logo`=''");
	$qry->execute();
	//reload
	$www = "./index.php?page=admin&subpage=parameters";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>'; 
}

if($_POST['submit_general'])
{
	$_POST['ticket_increment_number']=strip_tags($db->quote($_POST['ticket_increment_number']));
	$_POST['ticket_increment_number']=str_replace("'","",$_POST['ticket_increment_number']);
	//modify ticket increment number if specify
	if($_POST['ticket_increment_number'] && is_numeric($_POST['ticket_increment_number']))
	{
		$db->exec("ALTER TABLE `tincidents` auto_increment=$_POST[ticket_increment_number]");
	}
	
	//upload logo file
	if($_FILES['logo']['name'])
	{
	    $filename = $_FILES['logo']['name'];
		//change special character in filename
		$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
		$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
		$file_rename = str_replace($a,$b,$_FILES['logo']['name']);
	    //check file extension
	    $whitelist=array('png','jpg','jpeg','gif','bmp','tiff','webp','psd','raw','heif','indd','svg','ai','eps');
		$extension=new SplFileInfo($file_rename);
		$extension=$extension->getExtension();
        if(in_array(strtolower($extension),$whitelist)) {
            $repertoireDestination = "./upload/logo/";
    		if(move_uploaded_file($_FILES['logo']['tmp_name'], $repertoireDestination.$file_rename)   ) 
    		{
    		} else {
    		echo T_('Erreur de transfert vérifier le chemin').' '.$repertoireDestination;
    		}
        } else {
            echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').' :</strong> '.T_('Fichier interdit').'.<br></div>';
            $file_rename='logo.png';
        }
	} else {$file_rename=$rparameters['logo'];}

    //extract code nacre file
    if($_FILES['code-nacre']['name'])
    {
        try {
            if(pathinfo($_FILES['code-nacre']['name'], \PATHINFO_EXTENSION) != 'csv'){
                // file is not valid
                throw new Exception(T_('Le fichier doit être au format csv'));
            }

            $csvFile = file($_FILES['code-nacre']['tmp_name']);
            $firstLine = true;

            foreach ($csvFile as $line) {
                if ($firstLine) {
                    $firstLine = false;
                    continue;
                }
                $codeNacreArray = str_getcsv($line);

                if (!$codeNacreArray[0] || !$codeNacreArray[1]) {
                    continue;
                }

                $codeNacre = new CodeNacre();
                $codeNacre
                    ->setCode($codeNacreArray[0])
                    ->setWording($codeNacreArray[1])
                    ->save();
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }

    //extract business address file
    if($_FILES['business-address']['name'])
    {
        try {
            if(pathinfo($_FILES['business-address']['name'], \PATHINFO_EXTENSION) != 'json'){
                // file is not valid
                throw new Exception(T_('Le fichier doit être au format json'));
            }

            $jsonFile = file_get_contents($_FILES['business-address']['tmp_name']);
            $businessAddressList = json_decode($jsonFile, true);

            foreach ($businessAddressList as $title => $address) {
                $businessAddress = new BusinessAddress();
                $businessAddress
                    ->setTitle($title)
                    ->setAddress($address)
                    ->save();
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }

	//secure string
	$_POST['mail_txt']=str_replace('<script>','',$_POST['mail_txt']);
	$_POST['mail_txt']=str_replace('</script>','',$_POST['mail_txt']);
	$_POST['mail_txt_end']=str_replace('<script>','',$_POST['mail_txt_end']);
	$_POST['mail_txt_end']=str_replace('</script>','',$_POST['mail_txt_end']);
	
	$_POST['company']=strip_tags($_POST['company']);
	$_POST['server_url']=strip_tags($_POST['server_url']);
	$_POST['restrict_ip']=strip_tags($_POST['restrict_ip']);
	$_POST['server_timezone']=strip_tags($_POST['server_timezone']);
	$_POST['log']=strip_tags($_POST['log']);
	$_POST['maxline']=strip_tags($_POST['maxline']);
	$_POST['timeout']=strip_tags($_POST['timeout']);
	$_POST['mail_cc']=strip_tags($_POST['mail_cc']);
	$_POST['mail_cci']=strip_tags($_POST['mail_cci']);
	$_POST['mail_from_name']=strip_tags($_POST['mail_from_name']);
	$_POST['mail_from_adr']=strip_tags($_POST['mail_from_adr']);
	$_POST['mail_color_title']=strip_tags($_POST['mail_color_title']);
	$_POST['mail_color_bg']=strip_tags($_POST['mail_color_bg']);
	$_POST['mail_color_text']=strip_tags($_POST['mail_color_text']);
	$_POST['mail_link']=strip_tags($_POST['mail_link']);
	$_POST['mail_order']=strip_tags($_POST['mail_order']);
	$_POST['time_display_msg']=strip_tags($_POST['time_display_msg']);
	$_POST['user_disable_attempt_number']=strip_tags($_POST['user_disable_attempt_number']);
	$_POST['user_password_policy_min_lenght']=strip_tags($_POST['user_password_policy_min_lenght']);
	$_POST['ticket_autoclose_delay']=strip_tags($_POST['ticket_autoclose_delay']);
	$_POST['ticket_autoclose_state']=strip_tags($_POST['ticket_autoclose_state']);
	$_POST['ticket_cat_auto_attribute']=strip_tags($_POST['ticket_cat_auto_attribute']);

	//update general tab
	$qry=$db->prepare("
		UPDATE `tparameters` SET 
		`company`=:company,
		`server_url`=:server_url,
		`restrict_ip`=:restrict_ip,
		`server_timezone`=:server_timezone,
		`log`=:log,
		`maxline`=:maxline,
		`timeout`=:timeout,
		`mail_txt`=:mail_txt,
		`mail_txt_end`=:mail_txt_end,
		`mail_cc`=:mail_cc,
		`mail_cci`=:mail_cci,
		`mail_from_name`=:mail_from_name,
		`mail_from_adr`=:mail_from_adr,
		`mail_color_title`=:mail_color_title,
		`mail_color_bg`=:mail_color_bg,
		`mail_color_text`=:mail_color_text,
		`mail_link`=:mail_link,
		`mail_order`=:mail_order,
		`logo`=:logo,
		`time_display_msg`=:time_display_msg,
		`auto_refresh`=:auto_refresh,
		`login_state`=:login_state,
		`notify`=:notify,
		`user_advanced`=:user_advanced,
		`user_register`=:user_register,
		`user_agency`=:user_agency,
		`user_limit_service`=:user_limit_service,
		`user_disable_attempt`=:user_disable_attempt,
		`user_disable_attempt_number`=:user_disable_attempt_number,
		`user_password_policy`=:user_password_policy,
		`user_password_policy_min_lenght`=:user_password_policy_min_lenght,
		`user_password_policy_special_char`=:user_password_policy_special_char,
		`user_password_policy_min_maj`=:user_password_policy_min_maj,
		`user_password_policy_expiration`=:user_password_policy_expiration,
		`user_forgot_pwd`=:user_forgot_pwd,
		`company_limit_ticket`=:company_limit_ticket,
		`company_limit_hour`=:company_limit_hour,
		`user_limit_ticket`=:user_limit_ticket,
		`user_company_view`=:user_company_view,
		`mail_auto`=:mail_auto,
		`mail_auto_tech_modify`=:mail_auto_tech_modify,
		`mail_auto_tech_attribution`=:mail_auto_tech_attribution,
		`mail_auto_user_modify`=:mail_auto_user_modify,
		`mail_auto_user_newticket`=:mail_auto_user_newticket,
		`mail_newticket`=:mail_newticket,
		`mail_newticket_address`=:mail_newticket_address,
		`mail_template`=:mail_template,
		`debug`=:debug,
		`order`=:order,
		`ticket_places`=:ticket_places,
		`ticket_type`=:ticket_type,
		`ticket_autoclose`=:ticket_autoclose,
		`ticket_autoclose_delay`=:ticket_autoclose_delay,
		`ticket_autoclose_state`=:ticket_autoclose_state,
		`ticket_cat_auto_attribute`=:ticket_cat_auto_attribute,
		`ticket_default_state`=:ticket_default_state,
		`meta_state`=:meta_state
		WHERE
		`id`=:id
	");
	$qry->execute(array(
		'company' => $_POST['company'],
		'server_url' => $_POST['server_url'],
		'restrict_ip' => $_POST['restrict_ip'],
		'server_timezone' => $_POST['server_timezone'],
		'log' => $_POST['log'],
		'maxline' => $_POST['maxline'],
		'timeout' => $_POST['timeout'],
		'mail_txt' => $_POST['mail_txt'],
		'mail_txt_end' => $_POST['mail_txt_end'],
		'mail_cc' => $_POST['mail_cc'],
		'mail_cci' => $_POST['mail_cci'],
		'mail_from_name' => $_POST['mail_from_name'],
		'mail_from_adr' => $_POST['mail_from_adr'],
		'mail_color_title' => $_POST['mail_color_title'],
		'mail_color_bg' => $_POST['mail_color_bg'],
		'mail_color_text' => $_POST['mail_color_text'],
		'mail_link' => $_POST['mail_link'],
		'mail_order' => $_POST['mail_order'],
		'logo' => $file_rename,
		'time_display_msg' => $_POST['time_display_msg'],
		'auto_refresh' => $_POST['auto_refresh'],
		'login_state' => $_POST['login_state'],
		'notify' => $_POST['notify'],
		'user_advanced' => $_POST['user_advanced'],
		'user_register' => $_POST['user_register'],
		'user_agency' => $_POST['user_agency'],
		'user_limit_service' => $_POST['user_limit_service'],
		'user_disable_attempt' => $_POST['user_disable_attempt'],
		'user_disable_attempt_number' => $_POST['user_disable_attempt_number'],
		'user_password_policy' => $_POST['user_password_policy'],
		'user_password_policy_min_lenght' => $_POST['user_password_policy_min_lenght'],
		'user_password_policy_special_char' => $_POST['user_password_policy_special_char'],
		'user_password_policy_min_maj' => $_POST['user_password_policy_min_maj'],
		'user_password_policy_expiration' => $_POST['user_password_policy_expiration'],
		'user_forgot_pwd' => $_POST['user_forgot_pwd'],
		'company_limit_ticket' => $_POST['company_limit_ticket'],
		'company_limit_hour' => $_POST['company_limit_hour'],
		'user_limit_ticket' => $_POST['user_limit_ticket'],
		'user_company_view' => $_POST['user_company_view'],
		'mail_auto' => $_POST['mail_auto'],
		'mail_auto_tech_modify' => $_POST['mail_auto_tech_modify'],
		'mail_auto_tech_attribution' => $_POST['mail_auto_tech_attribution'],
		'mail_auto_user_modify' => $_POST['mail_auto_user_modify'],
		'mail_auto_user_newticket' => $_POST['mail_auto_user_newticket'],
		'mail_newticket' => $_POST['mail_newticket'],
		'mail_newticket_address' => $_POST['mail_newticket_address'],
		'mail_template' => $_POST['mail_template'],
		'debug' => $_POST['debug'],
		'order' => $_POST['order'],
		'ticket_places' => $_POST['ticket_places'],
		'ticket_type' => $_POST['ticket_type'],
		'ticket_autoclose' => $_POST['ticket_autoclose'],
		'ticket_autoclose_delay' => $_POST['ticket_autoclose_delay'],
		'ticket_autoclose_state' => $_POST['ticket_autoclose_state'],
		'ticket_cat_auto_attribute' => $_POST['ticket_cat_auto_attribute'],
		'ticket_default_state' => $_POST['ticket_default_state'],
		'meta_state' => $_POST['meta_state'],
		'id' => '1'
		));
	
	//redirect
    $params = http_build_query(array(
        'error-message' => $errorMessage
    ));
	$www = "./index.php?page=admin&subpage=parameters&tab=general&".$params;
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
}
if($_POST['submit_connector'] || $_POST['test_ldap'])
{
	//secure string
	$_POST['mail_smtp']=strip_tags($_POST['mail_smtp']);
	$_POST['mail_smtp']=str_replace('|','',$_POST['mail_smtp']);
	$_POST['mail_username']=strip_tags($_POST['mail_username']);
	$_POST['mail_password']=strip_tags($_POST['mail_password']);
	$_POST['ldap_server']=strip_tags($_POST['ldap_server']);
	$_POST['ldap_server']=str_replace('|','',$_POST['ldap_server']);
	$_POST['ldap_domain']=strip_tags($_POST['ldap_domain']);
	$_POST['ldap_user']=strip_tags($_POST['ldap_user']);
	$_POST['ldap_login_field']=strip_tags($_POST['ldap_login_field']);
	$_POST['ldap_password']=strip_tags($_POST['ldap_password']);
	$_POST['imap_server']=strip_tags($_POST['imap_server']);
	$_POST['imap_server']=str_replace('|','',$_POST['imap_server']);
	$_POST['imap_user']=strip_tags($_POST['imap_user']);
	$_POST['imap_password']=strip_tags($_POST['imap_password']);
	$_POST['imap_blacklist']=strip_tags($_POST['imap_blacklist']);
	
	$qry=$db->prepare("
	UPDATE `tparameters` SET 
	`mail`=:mail,
	`mail_smtp`=:mail_smtp, 
	`mail_smtp_class`=:mail_smtp_class, 
	`mail_port`=:mail_port, 
	`mail_ssl_check`=:mail_ssl_check, 
	`mail_secure`=:mail_secure, 
	`mail_auth`=:mail_auth, 
	`mail_username`=:mail_username, 
	`mail_password`=:mail_password, 
	`ldap`=:ldap, 
	`ldap_auth`=:ldap_auth, 
	`ldap_sso`=:ldap_sso, 
	`ldap_type`=:ldap_type, 
	`ldap_service`=:ldap_service, 
	`ldap_service_url`=:ldap_service_url, 
	`ldap_login_field`=:ldap_login_field, 
	`ldap_agency`=:ldap_agency, 
	`ldap_agency_url`=:ldap_agency_url, 
	`ldap_server`=:ldap_server, 
	`ldap_port`=:ldap_port, 
	`ldap_user`=:ldap_user, 
	`ldap_password`=:ldap_password, 
	`ldap_domain`=:ldap_domain, 
	`ldap_url`=:ldap_url, 
	`ldap_disable_user`=:ldap_disable_user, 
	`imap`=:imap, 
	`imap_server`=:imap_server, 
	`imap_port`=:imap_port, 
	`imap_ssl_check`=:imap_ssl_check, 
	`imap_user`=:imap_user, 
	`imap_password`=:imap_password, 
	`imap_reply`=:imap_reply, 
	`imap_blacklist`=:imap_blacklist, 
	`imap_post_treatment`=:imap_post_treatment, 
	`imap_post_treatment_folder`=:imap_post_treatment_folder, 
	`imap_mailbox_service`=:imap_mailbox_service, 
	`imap_inbox`=:imap_inbox
	WHERE `id`=:id
	");
	$qry->execute(array(
		'mail' => $_POST['mail'],
		'mail_smtp' => $_POST['mail_smtp'],
		'mail_smtp_class' => $_POST['mail_smtp_class'],
		'mail_port' => $_POST['mail_port'],
		'mail_ssl_check' => $_POST['mail_ssl_check'],
		'mail_secure' => $_POST['mail_secure'],
		'mail_auth' => $_POST['mail_auth'],
		'mail_username' => $_POST['mail_username'],
		'mail_password' => $_POST['mail_password'],
		'ldap' => $_POST['ldap'],
		'ldap_auth' => $_POST['ldap_auth'],
		'ldap_sso' => $_POST['ldap_sso'],
		'ldap_type' => $_POST['ldap_type'],
		'ldap_service' => $_POST['ldap_service'],
		'ldap_service_url' => $_POST['ldap_service_url'],
		'ldap_login_field' => $_POST['ldap_login_field'],
		'ldap_agency' => $_POST['ldap_agency'],
		'ldap_agency_url' => $_POST['ldap_agency_url'],
		'ldap_server' => $_POST['ldap_server'],
		'ldap_port' => $_POST['ldap_port'],
		'ldap_user' => $_POST['ldap_user'],
		'ldap_password' => $_POST['ldap_password'],
		'ldap_domain' => $_POST['ldap_domain'],
		'ldap_url' => $_POST['ldap_url'],
		'ldap_disable_user' => $_POST['ldap_disable_user'],
		'imap' => $_POST['imap'],
		'imap_server' => $_POST['imap_server'],
		'imap_port' => $_POST['imap_port'],
		'imap_ssl_check' => $_POST['imap_ssl_check'],
		'imap_user' => $_POST['imap_user'],
		'imap_password' => $_POST['imap_password'],
		'imap_reply' => $_POST['imap_reply'],
		'imap_blacklist' => $_POST['imap_blacklist'],
		'imap_post_treatment' => $_POST['imap_post_treatment'],
		'imap_post_treatment_folder' => $_POST['imap_post_treatment_folder'],
		'imap_mailbox_service' => $_POST['imap_mailbox_service'],
		'imap_inbox' => $_POST['imap_inbox'],
		'id' => '1'
		));
	
	//move ticket from agency to another if detected
	if($rparameters['user_agency']==1 && $_POST['from_agency'] && $_POST['dest_agency'])
	{
		//secure string
		$_POST['dest_agency']=strip_tags($_POST['dest_agency']);
		$_POST['from_agency']=strip_tags($_POST['from_agency']);
	
		$qry=$db->prepare("UPDATE `tincidents` SET `u_agency`=:u_agency1 WHERE `u_agency`=:u_agency2");
		$qry->execute(array('u_agency1' => $_POST['dest_agency'],'u_agency2' => $_POST['from_agency']));
	}
	
	//update imap multi mailbox service parameters
	if($rparameters['imap_mailbox_service']==1)
	{
		//add new association
		if($_POST['mailbox_service'] && $_POST['mailbox_password'] && $_POST['mailbox_service_id'])
		{
			//secure string
			$_POST['mailbox_service']=strip_tags($_POST['mailbox_service']);
			$_POST['mailbox_password']=strip_tags($_POST['mailbox_password']);
		
			$qry=$db->prepare("INSERT INTO `tparameters_imap_multi_mailbox` (`mail`,`password`,`service_id`) VALUES (:mail,:password,:service_id)");
			$qry->execute(array('mail' => $_POST['mailbox_service'],'password' => $_POST['mailbox_password'],'service_id' => $_POST['mailbox_service_id']));
		}
		//crypt password
		$qry=$db->prepare("SELECT `id`,`password` FROM `tparameters_imap_multi_mailbox` WHERE password NOT LIKE '%gs_en%'");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			//crypt password
			$enc_mailbox_password = gs_crypt($row['password'], 'e', $rparameters['server_private_key']);
			//update tparameters
			$qry2=$db->prepare("UPDATE `tparameters_imap_multi_mailbox` SET `password`=:mail_password WHERE `id`=:id");
			$qry2->execute(array('mail_password' => $enc_mailbox_password,'id' => $row['id']));
		}
		$qry->closeCursor();
	}
	
	//crypt connector password
	if($_POST['mail_password'] && !preg_match('/gs_en/',$_POST['mail_password']))
	{
		//crypt password
		$enc_mail_password = gs_crypt($_POST['mail_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `mail_password`=:mail_password WHERE `id`='1'");
		$qry->execute(array('mail_password' => $enc_mail_password));
	}
	if($_POST['ldap_password'] && !preg_match('/gs_en/',$_POST['ldap_password']))
	{
		//crypt password
		$enc_ldap_password = gs_crypt($_POST['ldap_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `ldap_password`=:ldap_password WHERE `id`='1'");
		$qry->execute(array('ldap_password' => $enc_ldap_password));
	}
	if($_POST['imap_password'] && !preg_match('/gs_en/',$_POST['imap_password']))
	{
		//crypt password
		$enc_imap_password = gs_crypt($_POST['imap_password'], 'e', $rparameters['server_private_key']);
		//update tparameters
		$qry=$db->prepare("UPDATE `tparameters` SET `imap_password`=:imap_password WHERE `id`='1'");
		$qry->execute(array('imap_password' => $enc_imap_password,));
	}
	
	//redirect
	$www = './index.php?page=admin&subpage=parameters&tab=connector&ldaptest='.$_POST['test_ldap'].'';
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>'; 
}
if($_POST['submit_function'])
{
	//upload assets file
	if($_FILES['asset_import']['name'])
	{
		//create asset folder if not exist
		if(!file_exists('./upload/asset')) {mkdir('./upload/asset', 0777, true);}
		
	    $filename = $_FILES['asset_import']['name'];
		//change special character in filename
		$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
		$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
		$file_rename = str_replace($a,$b,$_FILES['asset_import']['name']);
	    //check file extension
	    $whitelist =  array('csv');
        $extension=new SplFileInfo($file_rename);
		$extension=$extension->getExtension();
        if(in_array(strtolower($extension),$whitelist)) {
            $dest_folder = "./upload/asset/";
    		if(move_uploaded_file($_FILES['asset_import']['tmp_name'], $dest_folder.$file_rename)) 
    		{
				//launch import treatment
				require('./core/import_assets.php');
    		} else {
			echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Erreur').' :</strong> '.T_('Erreur de transfert vérifier le chemin').' ('.$dest_folder.')<br></div>';
    		}
        } else {
            echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').' :</strong> '.T_('Fichier interdit').'.<br></div>';
        }
	}
	
	//action on survey questions
	if($rparameters['survey']==1)
	{
		//update current question
		$qry = $db->prepare("SELECT `id` FROM `tsurvey_questions` WHERE `disable`='0'");
		$qry->execute();
		while ($row=$qry->fetch())
		{
			//define var
			if(!isset($_POST['survey_question_select_1_'.$row['id']])) $_POST['survey_question_select_1_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_2_'.$row['id']])) $_POST['survey_question_select_2_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_3_'.$row['id']])) $_POST['survey_question_select_3_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_4_'.$row['id']])) $_POST['survey_question_select_4_'.$row['id']]= '';
			if(!isset($_POST['survey_question_select_5_'.$row['id']])) $_POST['survey_question_select_5_'.$row['id']]= '';
			if(!isset($_POST['survey_question_scale_'.$row['id']])) $_POST['survey_question_scale_'.$row['id']]= '';
			
			//secure string
			$_POST['survey_question_number_'.$row['id']]=strip_tags($_POST['survey_question_number_'.$row['id']]);
			$_POST['survey_question_type_'.$row['id']]=strip_tags($_POST['survey_question_type_'.$row['id']]);
			$_POST['survey_question_text_'.$row['id']]=strip_tags($_POST['survey_question_text_'.$row['id']]);
			$_POST['survey_question_scale_'.$row['id']]=strip_tags($_POST['survey_question_scale_'.$row['id']]);
			$_POST['survey_question_select_1_'.$row['id']]=strip_tags($_POST['survey_question_select_1_'.$row['id']]);
			$_POST['survey_question_select_2_'.$row['id']]=strip_tags($_POST['survey_question_select_2_'.$row['id']]);
			$_POST['survey_question_select_3_'.$row['id']]=strip_tags($_POST['survey_question_select_3_'.$row['id']]);
			$_POST['survey_question_select_4_'.$row['id']]=strip_tags($_POST['survey_question_select_4_'.$row['id']]);
			$_POST['survey_question_select_5_'.$row['id']]=strip_tags($_POST['survey_question_select_5_'.$row['id']]);
			
			$qry2=$db->prepare("UPDATE tsurvey_questions SET `number`=:number, `type`=:type, `text`=:text, `scale`=:scale, `select_1`=:select_1, `select_2`=:select_2, `select_3`=:select_3, `select_4`=:select_4, `select_5`=:select_5 WHERE `id`=:id");
			$qry2->execute(array(
				'number' => $_POST['survey_question_number_'.$row['id']],
				'type' => $_POST['survey_question_type_'.$row['id']],
				'text' => $_POST['survey_question_text_'.$row['id']],
				'scale' => $_POST['survey_question_scale_'.$row['id']],
				'select_1' => $_POST['survey_question_select_1_'.$row['id']],
				'select_2' => $_POST['survey_question_select_2_'.$row['id']],
				'select_3' => $_POST['survey_question_select_3_'.$row['id']],
				'select_4' => $_POST['survey_question_select_4_'.$row['id']],
				'select_5' => $_POST['survey_question_select_5_'.$row['id']],
				'id' => $row['id']
				));
		}
		$qry->closeCursor();
		
		//insert new question
		if($_POST['survey_new_question_number'])
		{
			//secure string
			$_POST['survey_new_question_number']=strip_tags($_POST['survey_new_question_number']);
			$_POST['survey_new_question_text']=strip_tags($_POST['survey_new_question_text']);
			
			$qry=$db->prepare("INSERT INTO `tsurvey_questions` (`number`,`type`,`text`) VALUES (:number,:type,:text)");
			$qry->execute(array('number' => $_POST['survey_new_question_number'],'type' => $_POST['survey_new_question_type'],'text' => $_POST['survey_new_question_text']));
		}
	}
	
	//escape special char and secure string before database insert
	$_POST['survey_mail_text']=str_replace('<script>','',$_POST['survey_mail_text']);
	$_POST['survey_mail_text']=str_replace('</script>','',$_POST['survey_mail_text']);
	
	$qry=$db->prepare("
	UPDATE `tparameters` SET 
	`planning`=:planning,
	`procedure`=:procedure,
	`survey`=:survey,
	`survey_mail_text`=:survey_mail_text,
	`survey_ticket_state`=:survey_ticket_state,
	`survey_auto_close_ticket`=:survey_auto_close_ticket,
	`project`=:project,
	`asset`=:asset,
	`asset_ip`=:asset_ip,
	`asset_warranty`=:asset_warranty,
	`asset_vnc_link`=:asset_vnc_link,
	`availability`=:availability,
	`availability_all_cat`=:availability_all_cat,
	`availability_condition_type`=:availability_condition_type,
	`availability_condition_value`=:availability_condition_value,
	`availability_dep`=:availability_dep
	WHERE `id`=:id");
	$qry->execute(array(
		'planning' => $_POST['planning'],
		'procedure' => $_POST['procedure'],
		'survey' => $_POST['survey'],
		'survey_mail_text' => $_POST['survey_mail_text'],
		'survey_ticket_state' => $_POST['survey_ticket_state'],
		'survey_auto_close_ticket' => $_POST['survey_auto_close_ticket'],
		'project' => $_POST['project'],
		'asset' => $_POST['asset'],
		'asset_ip' => $_POST['asset_ip'],
		'asset_warranty' => $_POST['asset_warranty'],
		'asset_vnc_link' => $_POST['asset_vnc_link'],
		'availability' => $_POST['availability'],
		'availability_all_cat' => $_POST['availability_all_cat'],
		'availability_condition_type' => $_POST['availability_condition_type'],
		'availability_condition_value' => $_POST['availability_condition_value'],
		'availability_dep' => $_POST['availability_dep'],
		'id' => '1'
		));
	
	//add cat to availability list
	if($_POST['category']!=0)
	{
		$qry=$db->prepare("INSERT INTO `tavailability` (`category`,`subcat`) VALUES (:category,:subcat)");
		$qry->execute(array('category' => $_POST['category'],'subcat' => $_POST['subcat']));
	}
	//add dependency cat to availability list
	if($_POST['depcategory']!=0)
	{
		$qry=$db->prepare("INSERT INTO `tavailability_dep` (`category`,`subcat`) VALUES (:category,:subcat)");
		$qry->execute(array('category' => $_POST['depcategory'],'subcat' => $_POST['depsubcat']));
	}
	
	//find input name for target values
	$qry = $db->prepare("SELECT DISTINCT YEAR(date_create) FROM `tincidents`");
	$qry->execute(array());
	while ($rowyear=$qry->fetch())
	{
		$qry2 = $db->prepare("SELECT `subcat` FROM `tavailability`");
		$qry2->execute();
	    while ($rowsubcat=$qry2->fetch())
	    {
	    	$inputname="target_$rowyear[0]_$rowsubcat[subcat]";
			if(!isset($_POST[$inputname])) $_POST[$inputname] = '';
	    	if($_POST[$inputname]) {
	    		//check existing values
				$qry3 = $db->prepare("SELECT * FROM `tavailability_target` WHERE year=:year AND subcat=:subcat");
				$qry3->execute(array('year' => $rowyear[0],'subcat' => $rowsubcat['subcat'],));
				$check= $qry3->fetch();
	    		if($check[0])
	    		{
					$qry4=$db->prepare("UPDATE tavailability_target SET target=:target WHERE year=:year AND subcat=:subcat");
					$qry4->execute(array('target' => $_POST[$inputname],'year' => $rowyear[0],'subcat' => $rowsubcat['subcat']));
	    		} else {
					$qry4=$db->prepare("INSERT INTO tavailability_target (year,subcat,target) VALUES (:year,:subcat,:target)");
					$qry4->execute(array('year' => $rowyear[0],'subcat' => $rowsubcat['subcat'],'target' => $_POST[$inputname]));
	    		}
	    	}
	    }
		$qry2->closecursor();
	}
	$qry->closecursor();
	
	if(!$error)
	{
	//redirect
		$www = "./index.php?page=admin&subpage=parameters&tab=function";
		echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>'; 
	} else {echo $error;}
	
}
//delete question from survey
if($rparameters['survey']==1 && $_GET['deletequestion'] && $rright['admin']!=0)
{
	$qry=$db->prepare("DELETE FROM tsurvey_questions WHERE id=:id");
	$qry->execute(array('id' => $_GET['deletequestion']));
}
//remove cat from availability list
if($_GET['deleteavailability']!='' && $rright['admin']!=0)
{
	$qry=$db->prepare("DELETE FROM tavailability WHERE id=:id");
	$qry->execute(array('id' => $_GET['deleteavailability']));
}
//remove dep cat from availability dependency list
if($_GET['deleteavailabilitydep']!='' && $rright['admin']!=0)
{
	$qry=$db->prepare("DELETE FROM tavailability_dep WHERE id=:id");
	$qry->execute(array('id' => $_GET['deleteavailabilitydep']));
}
//delete imap mailbox service association
if($rparameters['imap_mailbox_service']==1 && $_GET['delete_imap_service'] && $rright['admin']!=0)
{
	$qry=$db->prepare("DELETE FROM tparameters_imap_multi_mailbox WHERE id=:id");
	$qry->execute(array('id' => $_GET['delete_imap_service']));
}
//clean old files
$test_file=file_exists('./fileupload.php' );
if($test_file==1){unlink('./fileupload.php');}
//detect install directory to display warning
$test_install_file=file_exists('./install/index.php' );
if($test_install_file==1)
{
	echo DisplayMessage('error',T_("Le dossier d'installation est toujours présent sur votre serveur, pour des raisons de sécurité veuillez supprimer le répertoire \"./install\" "));
}

if ($_GET['error-message']) {
    echo DisplayMessage('error', $_GET['error-message']);
}
?>
<!-- /////////////////////////////////////////////////////////////// general tab /////////////////////////////////////////////////////////////// -->
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2" >
		<i class="fa fa-cog"></i> <?php echo T_("Paramètres de l'application"); ?>
	</h1>
</div>
<div class="col-sm-12">	
	<div class="tabs-above shadow">
		<ul class="nav nav-tabs nav-justified" id="myTab">
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='general') {echo 'active';} ?>" href="./index.php?page=admin&subpage=parameters&tab=general">
					<i class="fa fa-wrench text-success"></i>
					<?php echo T_('Général'); ?>
				</a>
			</li>
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='connector') {echo 'active';} ?>" href="./index.php?page=admin&subpage=parameters&tab=connector">
					<i class="fa fa-link text-primary-m2"></i>
					<?php echo T_('Connecteurs'); ?>
				</a>
			</li>
			<li class="nav-item mr-1px">
				<a class="nav-link <?php if($_GET['tab']=='function') {echo 'active';} ?>" href="./index.php?page=admin&subpage=parameters&tab=function">
					<i class="fa fa-puzzle-piece text-warning"></i>
					<?php echo T_('Fonctions'); ?>
				</a>
			</li>
		</ul>
		<div class="tab-content" style="background-color:#FFF;">
			<div id="general"  class="tab-pane <?php if($_GET['tab']=='general' || $_GET['tab']=='') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="post" action="">
					<div class="table-responsive">
						<table class="table table table-bordered">
							<tbody>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-building text-blue-m3 pr-1"></i>
										<?php echo T_('Société'); ?> :
									</td>
									<td class="text-95 text-default-d3">
										<label class="lbl" for="company" ><?php echo T_("Nom de l'entreprise"); ?> : </label>
										<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="company" value="<?php echo $rparameters['company']; ?>" placeholder="<?php echo T_('Société'); ?>">
										<div class="pt-1"></div>
										<label class="lbl" for="logo"><?php echo T_('Logo'); ?> : </label>
										<?php
											if($rparameters['logo']!="")
											{
												echo '
													<img height="40" src="./upload/logo/'.$rparameters['logo'].'" />	
													<a title="'.T_('Supprimer ce logo').'" href="./index.php?page=admin&subpage=parameters&tab=general&action=deletelogo">
														<i class="fa fa-trash text-danger "></i>
													</a>
												';
											} else {
												echo '<input type="file" id="logo" name="logo" />';
											}
										?>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-server text-blue-m3 pr-1"></i>
										<?php echo T_('Serveur'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label class="lbl" for="server_url"><?php echo T_("URL d'accès au serveur"); ?> : </label>
										<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="server_url" value="<?php echo $rparameters['server_url']; ?>">
										<i title="<?php echo T_("URL de l'accès au serveur pour vos utilisateurs, utilisé dans l'envoi de mail (exemple: https://gestsup en LAN ou https://support.masociete.com sur Internet)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										<div class="pt-1"></div>
										<label class="lbl" for="restrict_ip"><?php echo T_('Restriction IP'); ?> : </label>
										<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="restrict_ip" value="<?php echo $rparameters['restrict_ip']; ?>">
										<i title="<?php echo T_("Permet de limiter l'accès des clients au serveur à une IP ou une plage d'IP. &#10;Si le champ n'est pas renseigné aucune restriction ne sera active. &#10;Vous pouvez ajouter plusieurs IP avec le séparateur virgule. &#10;Exemples : 192.168.0.1 ou 192.168.0 ou 192.168, 2001:0db8:85a3)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										<div class="pt-1"></div>
										<label class="lbl" for="server_timezone"><?php echo T_('Fuseau horaire'); ?> : </label>
										<select style="width:auto" class="form-control form-control-sm d-inline-block" name="server_timezone">
											<option <?php if($rparameters['server_timezone']=='') {echo 'selected';} ?> value="">Définit php.ini</option>
											<option <?php if($rparameters['server_timezone']=='America/Guadeloupe') {echo 'selected';} ?> value="America/Guadeloupe">America/Guadeloupe</option>
											<option <?php if($rparameters['server_timezone']=='America/Guyana') {echo 'selected';} ?> value="America/Guyana">America/Guyana</option>
											<option <?php if($rparameters['server_timezone']=='America/Martinique') {echo 'selected';} ?> value="America/Martinique">America/Martinique</option>
											<option <?php if($rparameters['server_timezone']=='America/Miquelon') {echo 'selected';} ?> value="America/Miquelon">America/Miquelon</option>
											<option <?php if($rparameters['server_timezone']=='America/New_York') {echo 'selected';} ?> value="America/New_York">America/New_York</option>
											<option <?php if($rparameters['server_timezone']=='America/St_Barthelemy') {echo 'selected';} ?> value="America/St_Barthelemy">America/St_Barthelemy</option>
											<option <?php if($rparameters['server_timezone']=='America/Toronto') {echo 'selected';} ?> value="America/Toronto">America/Toronto</option>
											<option <?php if($rparameters['server_timezone']=='Europe/Berlin') {echo 'selected';} ?> value="Europe/Berlin">Europe/Berlin</option>
											<option <?php if($rparameters['server_timezone']=='Europe/London') {echo 'selected';} ?> value="Europe/London">Europe/London</option>
											<option <?php if($rparameters['server_timezone']=='Europe/Madrid') {echo 'selected';} ?> value="Europe/Madrid">Europe/Madrid</option>
											<option <?php if($rparameters['server_timezone']=='Europe/Paris') {echo 'selected';} ?> value="Europe/Paris">Europe/Paris</option>
											<option <?php if($rparameters['server_timezone']=='Indian/Maldives') {echo 'selected';} ?> value="Indian/Maldives">Indian/Maldives</option>
											<option <?php if($rparameters['server_timezone']=='Indian/Mauritius') {echo 'selected';} ?> value="Indian/Mauritius">Indian/Mauritius</option>
											<option <?php if($rparameters['server_timezone']=='Indian/Mayotte') {echo 'selected';} ?> value="Indian/Mayotte">Indian/Mayotte</option>
											<option <?php if($rparameters['server_timezone']=='Indian/Reunion') {echo 'selected';} ?> value="Indian/Reunion">Indian/Reunion</option>
											<option <?php if($rparameters['server_timezone']=='Pacific/Tahiti') {echo 'selected';} ?> value="Pacific/Tahiti">Pacific/Tahiti</option>
										</select>
										<i title="<?php echo T_('Force le fuseau horaire, par défaut la valeur définie et celle présente dans le fichier php.ini'); ?>" class="fa fa-question-circle text-primary-m2"></i>
										<div class="pt-1"></div>
										<label  class="lbl">
											<input type="checkbox" <?php if($rparameters['log']) {echo "checked";}  ?> name="log" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Gestion des logs'); ?></span>
											<i title="<?php echo T_("Active l'enregistrement de données liées aux erreurs et à la sécurité dans le logiciel, affiche une nouvelle section dans Administration"); ?>." class="fa fa-question-circle text-primary-m2"></i></label>
									   </label>
									   <div class="pt-1"></div>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-desktop text-blue-m3 pr-1"></i>
										<?php echo T_('Affichage'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label class="lbl" for="timeout"><?php echo T_('Temps de déconnexion'); ?> : </label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="2" name="timeout" value="<?php echo $rparameters['timeout']; ?>"> m
											<i title="<?php echo T_("Valeur en minutes, permettant de déconnecter la session au bout d'un temps d'inactivité. Doit être inférieur au session.gc_maxlifetime définit en secondes dans le php.ini"); ?>" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label class="lbl" for="maxline"><?php echo T_('Nombre de lignes par page'); ?> : </label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="2" name="maxline" value="<?php echo $rparameters['maxline']; ?>">
											<i title="<?php echo T_("Si cette valeur est trop grande cela peut ralentir l'application"); ?>" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label class="lbl" for="time_display_msg"><?php echo T_("Temps d'affichage des messages d'actions"); ?> :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="time_display_msg" type="text" value="<?php echo $rparameters['time_display_msg']; ?>" size="4" /> ms<br />
											<label class="lbl" for="auto_refresh"><?php echo T_('Actualisation automatique'); ?> :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="auto_refresh" type="text" value="<?php echo $rparameters['auto_refresh']; ?>" size="3" /> s 
											<i title="<?php echo T_("Si la valeur est à 0, alors l'actualisation automatique est désactivée. Attention, cette fonction peut faire clignoter l'écran selon les navigateurs"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
											<div class="pt-1"></div>
											<label class="lbl" for="login_state"><?php echo T_("État par défaut à la connexion"); ?> :</label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="login_state" name="login_state" >
												<?php
													$qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY `number`");
													$qry->execute();
													while ($row = $qry->fetch())
													{
														if($rparameters['login_state']==$row['id'])
														{echo '<option selected value="'.$row['id'].'">'.T_('Vos tickets').' '.$row['name'].'</option>';}
														else 
														{echo '<option value="'.$row['id'].'">'.T_('Vos tickets').' '.$row['name'].'</option>';}
													}
													$qry->closeCursor();
													
													//check if user have right to display side all menu before display option
													$qry = $db->prepare("SELECT `side_all` FROM `trights` WHERE profile='2'");
													$qry->execute();
													$row=$qry->fetch();
													$qry->closeCursor();
													if($row['side_all']==2)
													{
														echo '<option '; if($rparameters['login_state']=='all') {echo "selected ";} echo 'value="all">'.T_('Tous les tickets').'</option>';
														if($rparameters['meta_state']==1) 
														{
															echo '<option '; if($rparameters['login_state']=='meta_all') {echo "selected ";} echo ' value="meta_all">'.T_('Tous les tickets à traiter').'</option>';
														}
													}
												?>
											</select>
											<i title="<?php echo T_("Détermine l'état par défaut affiché lors de la connexion de l'utilisateur, un utilisateur peut outrepasser ce paramètre en le modifiant dans ses paramètres personnels"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
											<div class="pt-1"></div>
											<label  class="lbl"><i class="fa fa-caret-right text-primary-m2"></i> <a target="_blank" href="./monitor.php?user_id=<?php echo $_SESSION['user_id']; ?>&key=<?php echo $rparameters['server_private_key']; ?>"><?php echo T_('Écran de supervision'); ?></a></label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-ticket-alt text-blue-m3 pr-1"></i>
										<?php echo T_('Tickets'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label  class="lbl" for="ticket_increment_number"><?php echo T_("Numéro d'incrémentation"); ?> : </label>
										<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="6" name="ticket_increment_number" value="">
										<i title="<?php echo T_("Permet d'initialiser le compteur de ticket à une valeur numérique. Attention vous devez spécifier une valeur supérieur au numéro de ticket actuel le plus haut et ne pourrez plus redéfinir le compteur à une valeur inférieur"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										<div class="pt-1"></div>
										<label class="lbl" for="order"><?php echo T_('Ordre de trie'); ?> :</label>
										<select style="width:auto" class="form-control form-control-sm d-inline-block" id="order" name="order" >
											<option <?php if($rparameters['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create') echo "selected "; ?> value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create"><?php echo T_('État > Priorité > Criticité > Date de création'); ?></option>
											<option <?php if($rparameters['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope') echo "selected "; ?> value="tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope"><?php echo T_('État > Priorité > Criticité > Date de résolution estimée'); ?></option>
											<option <?php if($rparameters['order']=='tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality') echo "selected "; ?> value="tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality"><?php echo T_('État > Date de résolution estimée > Priorité > Criticité'); ?></option>
											<option <?php if($rparameters['order']=='tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority') echo "selected "; ?> value="tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority"><?php echo T_('État > Date de résolution estimée > Criticité > Priorité'); ?></option>
											<option <?php if($rparameters['order']=='tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority') echo "selected "; ?> value="tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority"><?php echo T_('État > Criticité > Date de résolution estimée > Priorité'); ?></option>
											<option <?php if($rparameters['order']=='id') echo "selected "; ?>  value="id"><?php echo T_('Numéro de ticket'); ?></option>
										</select>
										<i title="<?php echo T_("Détermine l'ordre de classement des tickets dans la liste des tickets"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
										<div class="pt-1"></div>
										<label class="lbl" for="ticket_default_state"><?php echo T_('État par défaut lors de la création de tickets'); ?> :</label>
										<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ticket_default_state" name="ticket_default_state" >
											<?php
												$qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY `number`");
												$qry->execute();
												while ($row = $qry->fetch())
												{
													if($rparameters['ticket_default_state']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
												$qry->closeCursor();
											?>
										</select>
										<i title="<?php echo T_("Détermine l'état par défaut lors de la création des tickets par les utilisateurs ou par le connecteur IMAP"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
										<div class="pt-1"></div>
                                        <label class="lbl" for="notification-state"><?php echo T_('État par défaut lors de la finalisation d\'un ticket'); ?> :</label>
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" id="notification-state" name="notification-state" >
                                            <?php
                                            $qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY `number`");
                                            $qry->execute();
                                            while ($row = $qry->fetch())
                                            {
                                                $selected = $parameters->getNotificationState() == $row['id'];
                                                ?>
                                                <option <?php echo $selected ? 'selected' : ''; ?> value="<?php echo $row['id'] ?>">
                                                    <?php echo $row['name'] ?>
                                                </option>
                                                <?php
                                            }
                                            $qry->closeCursor();
                                            ?>
                                        </select>
                                        <i title="<?php echo T_("Détermine l'état par défaut lors de la clôture des tickets par les utilisateurs"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
                                        <div class="pt-1"></div>
										<label class="lbl" >
											<input type="checkbox" <?php if($rparameters['meta_state']) {echo "checked";}  ?> name="meta_state" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Gestion du méta état "à traiter"'); ?></span>
											<i title="<?php echo T_("Permet d'afficher un nouvel état regroupant les états en attente de PEC, en cours, et en attente de retour"); ?>." class="fa fa-question-circle text-primary-m2"></i></label>
									   </label>
									   <div class="pt-1"></div>
										<label class="lbl">
											<input type="checkbox" <?php if($rparameters['ticket_places']) {echo "checked";}  ?> name="ticket_places" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Gestion des lieux'); ?></span>
											<i title="<?php echo T_('Permet un rattachement du ticket à une localité, une liste des lieux est éditable dans la section liste, un nouveau champ sera disponible sur le ticket'); ?>." class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<div class="pt-1"></div>
										<label class="lbl">
											<input type="checkbox" <?php if($rparameters['ticket_type']) {echo "checked";} ?> name="ticket_type" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Gestion des types'); ?></span>
											<i title="<?php echo T_('Permet de définir un type à un ticket (ex: Demande, Incident...), ajoute une ligne sur le ticket, la liste des types est administrable dans Administration > Liste'); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<div class="pt-1"></div>
										<label class="lbl">
											<input type="checkbox" <?php if($rparameters['ticket_autoclose']) {echo "checked";} ?> name="ticket_autoclose" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Fermeture automatique'); ?></span>
											<i title="<?php echo T_("Permet de modifier automatiquement les tickets dans l'état résolu après X jours depuis la date de création du ticket. A noter les modifications sont réalisées une fois par jour lors de l'affichage de la page de login"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<?php
											if($rparameters['ticket_autoclose']==1)
											{
												echo '
													<blockquote>
														<label  class="lbl">
															'.T_('Délais').' : <input style="width:auto" class="form-control form-control-sm d-inline-block" name="ticket_autoclose_delay" type="text" value="'.$rparameters['ticket_autoclose_delay'].'" size="3" /> jours
															<i title="'.T_('Définit le nombre de jours avant la clôture automatique du ticket').'" class="fa fa-question-circle text-primary-m2"></i>
															<div class="pt-1"></div>
															'.T_('État').' :
															<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ticket_autoclose_state" name="ticket_autoclose_state">
																<option '; if($rparameters['ticket_autoclose_state']==0) {echo 'selected';} echo ' value="0">'.T_('Tous').'</option>
																<option '; if($rparameters['ticket_autoclose_state']==6) {echo 'selected';} echo ' value="6">'.T_('Attente retour').'</option>
															</select>
															<i title="'.T_("Spécifie si la fermeture automatique s'applique à tous les états ou uniquement aux tickets dans l'état Attente retour ").'" class="fa fa-question-circle text-primary-m2"></i>
														</label>
													</blockquote>
												';
											}
										?>
										<div class="pt-1"></div>
										<label  class="lbl">
											<input type="checkbox" <?php if($rparameters['ticket_cat_auto_attribute']) {echo "checked";} ?> name="ticket_cat_auto_attribute" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Attribution automatique à un technicien en fonction de la catégorie'); ?></span>
											<i title="<?php echo T_("Permet d'attribuer automatiquement un ticket à un technicien ou un groupe de technicien, en fonction de la catégorie ou sous-catégorie du ticket. &#10;Disponible uniquement lors de l'ouverture d'un ticket et que le champ technicien n'est pas affiché. &#10;Si un conflit existe entre une attribution définie sur une catégorie et sous-catégorie alors c'est la sous-catégorie qui sera prise en compte.&#10;(Cf Administration > Liste > Catégorie)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-volume-up text-blue-m3 pr-1"></i>
										<?php echo T_('Son'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label  class="lbl">
											<input type="checkbox" <?php if($rparameters['notify']) echo "checked"; ?> name="notify" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Activer la notification sonore pour les nouveaux tickets'); ?></span>
											<i title="<?php echo T_("Active l'avertisseur sonore pour le technicien si un utilisateur déclare un ticket (fonctionne uniquement sur Chrome, Firefox et Safari)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-user text-blue-m3 pr-1"></i>
										<?php echo T_('Utilisateurs'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_advanced']) echo "checked"; ?> name="user_advanced" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Utiliser les propriétés utilisateur avancées'); ?></span>
											<i title="<?php echo T_('Ajoute des champs supplémentaire aux propriétés utilisateurs, Société, FAX, Adresses...'); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_register']) echo "checked"; ?> name="user_register" value="1">
											<span class="lbl">&nbsp;<?php echo T_("Les utilisateurs peuvent s'enregistrer"); ?></span>
											<i title="<?php echo T_('Ajoute un bouton sur la page de connexion, permettant la création de nouveaux utilisateurs'); ?>." class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_limit_ticket']) echo "checked"; ?> name="user_limit_ticket" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Limite de tickets par utilisateur'); ?></span>
											<i title="<?php echo T_("Permet de limiter le nombre de tickets qu'un utilisateur peut ouvrir pour un période donnée, les paramètres se trouvent sur la fiche de l'utilisateur. Si aucun paramètres n'est définit la limitation ne sera pas active. Si la limite de ticket est atteinte alors la création de ticket est bloqué pour l'utilisateur"); ?>." class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['company_limit_ticket']) echo "checked"; ?> name="company_limit_ticket" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Limite de tickets par Société'); ?></span>
											<i title="<?php echo T_("Permet de limiter le nombre de tickets qu'une société peut ouvrir pour un période donnée, les paramètres se trouvent Administration > Listes > Société . Si aucun paramètres n'est définit la limitation ne sera pas active. Si la limite de ticket est atteinte alors la création de ticket est bloqué pour l'ensemble des utilisateurs associés à cette société"); ?>." class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['company_limit_hour']) echo "checked"; ?> name="company_limit_hour" value="1">
											<span class="lbl">&nbsp;<?php echo T_("Limite d'heures par Société"); ?></span>
											<i title="<?php echo T_("Permet d'associer un nombre d'heures à une société pour une période donnée, les heures utilisées sont basées sur le champ temps passé du ticket. Les paramètres se trouvent Administration > Listes > Société . Si aucun paramètres n'est définit la limitation ne sera pas active. Si la limite d'heures est atteinte alors la création de ticket est bloqué pour l'ensemble des utilisateurs associés à cette société"); ?>." class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_company_view']) echo "checked"; ?> name="user_company_view" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Les utilisateurs peuvent voir tous les tickets de leur société'); ?></span>
											<i title="<?php echo T_("Ajoute une nouvelle vue pour les utilisateurs, afin de visualiser tous les tickets déclarés par l'ensemble des utilisateurs associés à une société. (Le droit side_company est nécessaire pour disposer de l'accès, le droit de modification de société user_profil_company est à désactiver pour les utilisateurs)"); ?>.)" class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_agency']) echo "checked"; ?> name="user_agency" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Les utilisateurs appartiennent à des agences'); ?></span>
											<i title="<?php echo T_('Ajoute une nouvelle liste dans Administration > Liste > Agence'); ?>.)" class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_limit_service']) echo "checked"; ?> name="user_limit_service" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Les utilisateurs ne voient que les tickets de leurs services'); ?></span>
											<i title="<?php echo T_('Permet de cloisonner la liste de tickets ainsi que les catégories, droits associés : dashboard_service_only, side_all, side_all_service_disp, side_all_service_edit'); ?>.)" class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label  class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_forgot_pwd']) echo "checked"; ?> name="user_forgot_pwd" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Les utilisateurs peuvent réinitialiser leurs mots de passes'); ?></span>
											<i title="<?php echo T_("Ajoute un lien à l'écran de connexion permettant de réinitialiser le mot de passe de l'utilisateur, ne fonctionne que si le connecteur LDAP est désactivé et le connecteur SMTP activé"); ?>.)" class="fa fa-question-circle text-primary-m2"></i>
											<br />
										</label>
										<br />
										<label class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_disable_attempt']==1) echo "checked"; ?> name="user_disable_attempt" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Les utilisateurs sont désactivés après plusieurs tentatives de connexion infructueuses'); ?></span>
											<i title="<?php echo T_("Permet de désactiver automatiquement les utilisateurs après X tentatives d'authentification échoués (Le nombre de tentatives est paramétrable)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
											<br />
											<?php
											if($rparameters['user_disable_attempt'])
											{
												echo '
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right text-primary-m2"></i>
												Nombre de tentatives : 
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="user_disable_attempt_number" type="text" value="'.$rparameters['user_disable_attempt_number'].'" size="3">
												';
											}
											?>
										</label>
										<br />
										<label class="lbl"> 
											<input type="checkbox" <?php if($rparameters['user_password_policy']) {echo "checked";} ?> name="user_password_policy" value="1">
											<span class="lbl">&nbsp;<?php echo T_("Politique de gestion des mots de passes"); ?></span>
											<i title="<?php echo T_("Permet d'ajouter des contraintes lors de la définition de mot de passe utilisateur"); ?>" class="fa fa-question-circle text-primary-m2"></i>
											<br />
											<?php
											if($rparameters['user_password_policy'])
											{
												echo '
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right text-primary-m2"></i>
												Longueur minimum : <input style="width:auto" class="form-control form-control-sm d-inline-block"  name="user_password_policy_min_lenght" type="text" value="'.$rparameters['user_password_policy_min_lenght'].'" size="2"> '.T_('caractères').'<br />
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right text-primary-m2"></i>
												Caractères spéciaux obligatoire : <input style="vertical-align: middle;" type="checkbox" '; if($rparameters['user_password_policy_special_char']==1) {echo "checked";} echo ' name="user_password_policy_special_char" value="1">
												<span class="lbl">&nbsp;</span>
												<br />
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right text-primary-m2"></i>
												Minuscule et majuscule obligatoire : <input style="vertical-align: middle;" type="checkbox" '; if($rparameters['user_password_policy_min_maj']==1) {echo "checked";} echo ' name="user_password_policy_min_maj" value="1">
												<span class="lbl">&nbsp;</span>
												<br />
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-caret-right text-primary-m2"></i>
												Expiration après : <input style="width:auto" class="form-control form-control-sm d-inline-block" name="user_password_policy_expiration" type="text" value="'.$rparameters['user_password_policy_expiration'].'" size="2"> '.T_('jours').'
												<i title="'.T_("Si la valeur est définie à 0, alors ce paramètre est désactivé").'" class="fa fa-question-circle text-primary-m2"></i>
												';
											}
											?>
										</label>
									</td>
								</tr>
                                <tr>
                                    <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                                        <i class="fa fa-envelope text-blue-m3 pr-1"></i>
                                        <?php echo T_('Mails'); ?> :
                                    </td>
                                    <td class="text-95 text-default-d3">
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label  class="lbl"><?php echo T_("Envoi de mail automatique"); ?> :</label>
                                        <br />
                                        <div class="ml-4">
                                            <label  class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_auto']) echo "checked"; ?> name="mail_auto" value="1" />
                                                <?php echo T_("Au demandeur lors de l'ouverture ou fermeture d'un ticket par un technicien"); ?>
                                            </label>
                                            <br />
                                            <label  class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_auto_user_newticket']) echo "checked"; ?> name="mail_auto_user_newticket" value="1" />
                                                <?php echo T_("Au demandeur lors de l'ouverture d'un ticket par le demandeur"); ?>
                                            </label>
                                            <br />
                                            <label  class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_auto_user_modify']) echo "checked"; ?> name="mail_auto_user_modify" value="1" />
                                                <?php echo T_("Au demandeur lors de l'ajout ou la modification de la résolution d'un ticket par un technicien"); ?>
                                            </label>
                                            <br />
                                            <label class="lbl">
                                                <input type="checkbox" <?php if($parameters->isNotificationEnable()) echo "checked"; ?> name="notification-enable" value="1" />
                                                <?php echo T_("Au demandeur lors de la finalisation d'un ticket"); ?>
                                            </label>
                                            <br />
                                            <label  class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_auto_tech_modify']) echo "checked"; ?> name="mail_auto_tech_modify" value="1" />
                                                <?php echo T_("Au technicien lors de la modification d'un ticket par un demandeur"); ?>
                                            </label>
                                            <br />
                                            <label  class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_auto_tech_attribution']) echo "checked"; ?> name="mail_auto_tech_attribution" value="1" />
                                                <?php echo T_("Au technicien lors de l'attribution d'un ticket à un technicien"); ?>
                                            </label class="lbl">
                                            <br />
											<label  class="lbl">
                                                <input type="checkbox" <?php if($parameters->isAlerteDemande()) echo "checked"; ?> name="alerte-demande" value="1" />
                                                <?php echo T_("Au valideur si la demande n'est pas validée/refusée tous les "); ?>
												<input style="width:35px" class="form-control form-control-sm d-inline-block" name="interval-alerte-demande" type="text" value="<?php echo $parameters->getIntervalAlerteDemande(); ?>" size="35" />
												<?php echo T_(" jours"); ?>
                                            </label class="lbl">
                                            <br />
                                            <label class="lbl">
                                                <input type="checkbox" <?php if($rparameters['mail_newticket']) echo "checked"; ?> name="mail_newticket" value="1" />
                                                <?php echo T_("A une adresse mail lors de l'ouverture d'un ticket par un demandeur"); ?>
                                            </label>
                                            <br />
                                            <?php
                                            if($rparameters['mail_newticket']=='1')
                                            {
                                                echo '
													<div class="pt-1"></div>
														&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label>'.T_('Adresse mail').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_newticket_address" type="text" value="'.$rparameters['mail_newticket_address'].'" size="30" />
													<div class="pt-1"></div>
													';
                                            }
                                            ?>
                                        </div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label class="lbl"><?php echo T_('Texte début du mail'); ?> :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_txt" type="text" value="<?php echo $rparameters['mail_txt']; ?>" size="80" />
                                        <i title="<?php echo T_('Vous pouvez utiliser du code HTML (Exemple: <br />, <b></b>...)'); ?>" class="fa fa-question-circle text-primary-m2"></i>
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label class="lbl"><?php echo T_('Texte fin du mail'); ?> :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_txt_end" type="text" value="<?php echo $rparameters['mail_txt_end']; ?>" size="83" />
                                        <i title="<?php echo T_('Si vide texte automatique généré, pour le personnaliser sous pouvez utiliser du code HTML (<br />, <b></b>), des balises sont également disponible ([tech_name] Prénom et Nom du technicien, [tech_phone] téléphone du technicien, [link] Lien vers le ticket si le paramètre est activé)'); ?>" class="fa fa-question-circle text-primary-m2"></i>
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label class="lbl"><?php echo T_('Adresse en copie'); ?> :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_cc" type="text" value="<?php echo $rparameters['mail_cc']; ?>" size="30" /><br />
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label class="lbl"><?php echo T_("Intitulé de l'émetteur"); ?> :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_from_name" type="text" value="<?php echo $rparameters['mail_from_name']; ?>" size="30" /><br />
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label class="lbl"><?php echo T_("Adresse de l'émetteur"); ?> :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_from_adr" type="text" value="<?php echo $rparameters['mail_from_adr']; ?>" size="30" />
                                        <i title="<?php echo T_("Adresse d'envoi de tous les messages de l'application, si ce paramètre est vide les messages seront envoyés avec l'adresse mail de l'utilisateur connecté. Certains serveurs SMTP peuvent exiger que l'émetteur soit le même que le compte de connexion"); ?>. " class="fa fa-question-circle text-primary-m2"></i><br />
                                        <div class="pt-1"></div>
                                        <label class="lbl">
                                            <input type="checkbox" <?php if($rparameters['mail_cci']==1) echo "checked"; ?> name="mail_cci" value="1">
                                            <span class="lbl">&nbsp;<?php echo T_('Gestion de la copie cachée'); ?></span>
                                        </label>
                                        <i title="<?php echo T_("Ajoute une nouvelle section Copie cachée, sur la page : Paramètres du mail disponible depuis le ticket, permettant d'ajouter des destinataires en copie cachée"); ?>. " class="fa fa-question-circle text-primary-m2"></i><br />
                                        <div class="pt-1"></div>
                                        <label class="lbl">
                                            <input type="checkbox" <?php if($rparameters['mail_link']==1) echo "checked"; ?> name="mail_link" value="1">
                                            <span class="lbl">&nbsp;<?php echo T_('Intégrer un lien vers GestSup'); ?></span>
                                        </label>
                                        <div class="pt-1"></div>
                                        <label  class="lbl">
                                            <input type="checkbox" <?php if($rparameters['mail_order']==1) echo "checked"; ?> name="mail_order" value="1">
                                            <span class="lbl">&nbsp;<?php echo T_('Ordre antéchronologique dans les éléments de résolution'); ?></span>
                                            <i title="<?php echo T_("Permet d'inverser le sens du fil de suivi de la résolution les éléments les plus récents seront en premier"); ?>. " class="fa fa-question-circle text-primary-m2"></i><br />
                                        </label>
                                        <div class="pt-1"></div>
                                        <label  class="lbl" for="mail_template"><?php echo T_("Modèle de mail"); ?> :</label>
                                        <select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_template" name="mail_template" >
                                            <?php
                                            $mail_template = 'template/mail';
                                            $scanned_directory = array_diff(scandir($mail_template), array('..', '.', 'readme.txt'));
                                            foreach ($scanned_directory as $value)
                                            {
                                                if($value==$rparameters['mail_template']) {echo '<option selected value="'.$value.'">'.$value.'</option>';} else {echo '<option value="'.$value.'">'.$value.'</option>';}
                                            }
                                            ?>
                                        </select>
                                        <i title="<?php echo T_("Permet de sélectionner le modèle de mail utilisé dans les notifications, parmi les fichiers présents dans le repertoire /template/mail. Vous pouvez créer un nouveau modèle de mail en déposant un fichier .htm dans le repertoire /template/mail, le fichier readme.txt vous indiquera les tags disponibles"); ?>." class="fa fa-question-circle text-primary-m2"></i><br />
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label  class="lbl"><?php echo T_('Couleur du titre'); ?> :</label> #<input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_color_title" type="text" value="<?php echo $rparameters['mail_color_title']; ?>" size="6" /><br />
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label  class="lbl"><?php echo T_('Couleur du fond'); ?> :</label> #<input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_color_bg" type="text" value="<?php echo $rparameters['mail_color_bg']; ?>" size="6" /><br />
                                        <div class="pt-1"></div>
                                        <i class="fa fa-caret-right text-primary-m2"></i> <label  class="lbl"><?php echo T_('Couleur du texte'); ?> :</label> #<input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_color_text" type="text" value="<?php echo $rparameters['mail_color_text']; ?>" size="6" /><br />
                                    </td>
                                </tr>
																<tr>
																	<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                                        <i class="fa fa-user text-blue-m3 pr-1"></i>
                                        <?php echo T_('Valideurs'); ?> :
																	</td>
																	<td>
																		<label  class="lbl">
                                                <input type="checkbox" <?php if($parameters->isFixedValidators()) echo "checked"; ?> name="fixed-validators" value="1" />
                                                <?php echo T_("Utiliser des valideurs fixes par ligne/donnée budgétaire"); ?> <i title="<?php echo T_('Si coché les gestionnaires devront spécifier les valideurs par lignes/données budgétaires. Cela implique que les gestionnaires saisissent l\'ensemble des lignes budgétaires et leurs valideurs. Les demandeurs pourront toujours rajouter d\'autres valideurs que ceux imposés') ?>" class="fa fa-question-circle text-primary-m2"></i>
																		</label class="lbl">				
																	</td>
																</tr>
                                <tr>
                                    <td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
                                        <i class="fa fa-upload text-blue-m3 pr-1"></i>
                                        <?php echo T_('Imports'); ?> :
                                    </td>
                                    <td class="text-95 text-default-d3">
                                        <label class="lbl" for="code-nacre"><?php echo T_('Codes Nacres'); ?> : </label>
                                        <i title="<?php echo T_('Importe un fichier contenant la liste des Codes Nacres au format CSV') ?>" class="fa fa-question-circle text-primary-m2"></i>
                                        <br>
                                        <input type="file" id="code-nacre" name="code-nacre" />
                                        <div class="pt-1"></div>
                                        <label class="lbl" for="business-address"><?php echo T_('Adresses professionnelles'); ?> : </label>
                                        <i title="<?php echo T_('Importe un fichier contenant la liste des adresses professionnelles au format JSON') ?>" class="fa fa-question-circle text-primary-m2"></i>
                                        <br>
                                        <input type="file" id="business-address" name="business-address" />
                                    </td>
                                </tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-bug text-blue-m3 pr-1"></i>
									<?php echo T_('Debug'); ?> : 
									</td>
									<td class="text-95 text-default-d3">
										<label> 
											<input type="checkbox" <?php if($rparameters['debug']) echo "checked"; ?> name="debug" value="1">
											<span class="lbl">&nbsp;<?php echo T_('Activer le mode de débogage'); ?></span>
											<i title="<?php echo T_("Active le mode débogage afin d'afficher les éléments de résolution de problèmes"); ?>." class="fa fa-question-circle text-primary-m2"></i>
										</label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
						<button name="submit_general" id="submit_general" value="submit_general" type="submit" class="btn btn-success">
							<i class="fa fa-check mr-1"></i>
							<?php echo T_('Valider'); ?>
						</button>
					</div>
				</form>
			</div>
			<!-- /////////////////////////////////////////////////////////////// connectors part /////////////////////////////////////////////////////////////// -->
			<div id="connector" class="tab-pane <?php if($_GET['tab']=='connector') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="post" action="">
					<div class="table-responsive">
						<table class="table table table-bordered">
							<tbody>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-envelope text-blue-m3 pr-1"></i>
										SMTP :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<input type="checkbox" <?php if($rparameters['mail']==1) echo "checked"; ?> name="mail" value="1">
											<span class="lbl">&nbsp<?php echo T_('Activer la liaison SMTP'); ?>
											<i title="<?php echo T_("Connecteur permettant l'envoi de mails depuis GestSup vers un serveur de messagerie, afin que les mails puissent être envoyés"); ?>." class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<div class="pt-1"></div>
										<?php
										if($rparameters['mail']==1) 
										{
											echo '
											<div class="pt-1"></div>
											<label class="lbl" for="mail_smtp"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Serveur').' :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_smtp" type="text" value="'.$rparameters['mail_smtp'].'" size="20" />
											<i title="'.T_('Adresse IP ou Nom de votre serveur de messagerie (Exemple: 192.168.0.1 ou SRVMSG ou smtp.free.fr ou auth.smtp.1and1.fr ou SSL0.OVH.NET)').'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label class="lbl" for="mail_port"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Port').' :</label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_port" name="mail_port" >
												<option ';if($rparameters['mail_port']==587) echo "selected "; echo' value="587">587 (TLS)</option>
												<option ';if($rparameters['mail_port']==465) echo "selected "; echo' value="465">465 (SSL)</option>
												<option ';if($rparameters['mail_port']==25) echo "selected "; echo' value="25">25</option>
												<option ';if($rparameters['mail_port']==225) echo "selected "; echo' value="225">225</option>
											</select>
											<i title="'.T_('Port du serveur de messagerie par défaut le port 25 est utilisé, pour les connexions sécurisées les ports 465 et 587 sont utilisés. (exemple: OVH port 587 1&1 port 587)').'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label class="lbl" for="mail_ssl_check"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Vérification SSL').' :</label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_ssl_check" name="mail_ssl_check" >
												<option ';if($rparameters['mail_ssl_check']==1) echo "selected "; echo' value="1">'.T_('Activée').'</option>
												<option ';if($rparameters['mail_ssl_check']==0) echo "selected "; echo' value="0">'.T_('Désactivée').'</option>
											</select>
											<i title="'.T_('Désactivation de la verification du certificat serveur et autorise les certificats auto-signés').'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label class="lbl" for="mail_secure"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Préfixe').' :</label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_secure" name="mail_secure" >
												<option ';if($rparameters['mail_secure']==0) echo "selected "; echo' value="0">'.T_('Aucun').'</option>
												<option ';if($rparameters['mail_secure']=='SSL') echo "selected "; echo' value="SSL">ssl//</option>
												<option ';if($rparameters['mail_secure']=='TLS') echo "selected "; echo' value="TLS">tls//</option>
											</select>
											 ';
												if($rparameters['mail_secure']=='SSL' || $rparameters['mail_secure']=='TLS') {echo'<i>('.T_("l'extension php_openssl devra être activée").')</i>';} else {echo'<i title="Si votre serveur de messagerie est sécurisé avec SSL ou TLS (Exemple: Gmail utilise TLS, 1&1 aucun, OVH aucun)."  class="fa fa-question-circle text-primary-m2"></i>';} 
											 echo '
											<div class="pt-1"></div>
											<label class="lbl" for="mail_smtp_class"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Classe').' :</label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="mail_smtp_class" name="mail_smtp_class" >
												<option ';if($rparameters['mail_smtp_class']=='IsSMTP()') echo "selected "; echo' value="IsSMTP()">IsSMTP ('.T_('Défaut').')</option>
												<option ';if($rparameters['mail_smtp_class']=='IsSendMail()') echo "selected "; echo' value="IsSendMail()">IsSendMail</option>
											</select>
											<i title="'.T_("Classe PHPMailer, par défaut utiliser isSMTP(), certains hébergements n'autorisent que le isSendMail() (exemple: OVH et 1&1 utilise isSendMail() )").'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											<label>
												<input type="checkbox"'; if($rparameters['mail_auth']==1) {echo "checked";}  echo ' name="mail_auth" value="1">
												<span class="lbl">&nbsp;'.T_('Serveur authentifié').'</span>
												<i title="'.T_('Cochez cette case si votre serveur de messagerie nécessite un identifiant et mot de passe pour envoyer des messages. (exemple: 1&1 exige un SMTP authentifié)').'" class="fa fa-question-circle text-primary-m2"></i>
											</label>
											';
											if($rparameters['mail_auth']==1) 
											{
												echo '
												<br /><label class="lbl ml-4" for="mail_username"> '.T_('Utilisateur').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_username" type="text" value="'.$rparameters['mail_username'].'" size="30" />
												<br /><label class="lbl ml-4" for="mail_password"> '.T_('Mot de passe').' :</label> <input style="width:auto" class="form-control form-control-sm d-inline-block" name="mail_password" type="password" value="'.$rparameters['mail_password'].'" size="30" />
												';
											}
										}
										?>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-book text-blue-m3 pr-1"></i>
										LDAP :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<input type="checkbox" <?php if($rparameters['ldap']==1) echo "checked"; ?> name="ldap" value="1">
											<span class="lbl">&nbsp<?php echo T_('Activer la liaison LDAP'); ?> </span>	
											<i title="<?php echo T_("Connecteur permettant la synchronisation entre l'annuaire d'entreprise (Active Directory ou OpenLDAP) et GestSup"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<?php if($rparameters['ldap']=='1') 
										{
											echo '
											<div class="pt-1"></div>
											<label>
												<input type="checkbox"'; if($rparameters['ldap_auth']==1) echo "checked"; echo ' name="ldap_auth" value="1">
												<span class="lbl">&nbsp;'.T_("Activer l'authentification GestSup avec LDAP").'
												<i title="'.T_("Active l'authentification des utilisateurs dans Gestsup, avec les identifiants présents dans l'annuaire LDAP. Cela ne désactive pas l'authentification avec la base utilisateurs de GestSup").'." class="fa fa-question-circle text-primary-m2 "></i>
											</label>
											<div class="pt-1"></div>
											<label>
												<input type="checkbox"'; if($rparameters['ldap_sso']==1) echo "checked"; echo ' name="ldap_sso" value="1">
												<span class="lbl">&nbsp;'.T_("Activer le SSO").'
												<i title="'.T_("Permet la connexion d'un utilisateur sans la saisie de l'identifiant et du mot de passe, sur un poste Windows connecté à un domaine Active Directory, cf documentation").'." class="fa fa-question-circle text-primary-m2 "></i>
											</label>
											<div class="pt-1"></div>
											<label class="lbl">
												<input type="checkbox"'; if($rparameters['ldap_service']==1) echo "checked"; echo ' name="ldap_service" value="1">
												<span class="lbl">&nbsp;'.T_("Activer la synchronisation des groupes LDAP de services").'
												<i title="'.T_("Permet de synchroniser des groupes LDAP de service: création, renommage, désactivation de services GestSup, création utilisateurs GestSup membres du groupe LDAP et association entre les deux. (Tous les utilisateurs doivent appartenir à un groupe)").'." class="fa fa-question-circle text-primary-m2 "></i>
											</label>
											<div class="pt-1"></div>
											';
											if($rparameters['ldap_service']==1 )
											{
												echo '
												<label class="lbl ml-4" for="ldap_service_url">'.T_("Emplacement des groupes de service").' :</label>
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_service_url" type="text" value="'.$rparameters['ldap_service_url'].'" size="50" />
												<i title="'.T_("Emplacement des groupes de service dans l'annuaire LDAP. (exemple: ou=service, ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"></i> <br />
												<div class="pt-1"></div>
												';
											}
											if($rparameters['user_agency']==1)
											{
												echo '
												<label class="lbl">
													<input type="checkbox"'; if($rparameters['ldap_agency']==1) echo "checked"; echo ' name="ldap_agency" value="1">
													<span class="lbl">&nbsp;'.T_("Activer la synchronisation des groupes LDAP d'agences").'
													<i title="'.T_("Permet de synchroniser des groupes LDAP d'agence: création, renommage, désactivation d'agences GestSup, création utilisateurs GestSup membres du groupe LDAP et association entre les deux. (Tous les utilisateurs doivent appartenir à un groupe)").'." class="fa fa-question-circle text-primary-m2 "></i>
												</label>
												<div class="pt-1"></div>
												';
												if($rparameters['ldap_agency']==1)
												{
													echo '
													<label class="lbl ml-4" for="ldap_agency_url">'.T_("Emplacement des groupes d'agence").' :</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block"  name="ldap_agency_url" type="text" value="'.$rparameters['ldap_agency_url'].'" size="50" />
													<i title="'.T_("Emplacement des groupes d'agences dans l'annuaire LDAP. (exemple: ou=groupe_agence, ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"></i> <br />
													<div class="pt-1"></div>
													<label class="lbl ml-4" for="ldap_agency_url">'.T_("Déplacer les tickets associés à l'agence").' :</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block"  id="from_agency" name="from_agency" />
														';
														$qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` ORDER BY name");
														$qry->execute();
														while ($row=$qry->fetch()){echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
														$qry->closeCursor();
														echo'
													</select>
													<label class="lbl " for="dest_agency">'.T_("vers l'agence").' :</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block"  id="dest_agency" name="dest_agency" />
													';
														$qry = $db->prepare("SELECT `id`,`name` FROM `tagencies` ORDER BY name");
														$qry->execute();
														while ($row=$qry->fetch())	
														{
															echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
														}
														$qry->closeCursor();
														echo'
													</select>
													<div class="pt-1"></div>
													';
												}
											}
											echo '
											<label class="lbl" for="ldap_type"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Type de serveur').' : </label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_type" name="ldap_type" >
												<option ';if($rparameters['ldap_type']==0) echo "selected "; echo ' value="0">Active Directory</option>
												<option ';if($rparameters['ldap_type']==1) echo "selected "; echo ' value="1">OpenLDAP</option>
												<option ';if($rparameters['ldap_type']==3) echo "selected "; echo ' value="3">Samba4</option>
											</select>
											<i title="'.T_("Sélectionner si votre serveur d'annuaire est Windows Active Directory ou OpenLDAP").'." class="fa fa-question-circle text-primary-m2"></i><br />
											<div class="pt-1"></div>
											<label class="lbl" for="ldap_server"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Serveur').' :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_server" type="text" value="'.$rparameters['ldap_server'].'" size="20" />
											<i title="'.T_("Adresse IP ou nom netbios du serveur d'annuaire, sans suffixe DNS (Exemple: 192.168.0.1 ou SRVDC1)").'. " class="fa fa-question-circle text-primary-m2"></i><br />
											<div class="pt-1"></div>
											<label class="lbl" for="ldap_port"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Port').' : </label>
											<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_port" name="ldap_port" >
												<option ';if($rparameters['ldap_port']==389) echo "selected "; echo ' value="389">389</option>
												<option ';if($rparameters['ldap_port']==636) echo "selected "; echo ' value="636">636</option>
											</select>
											<i title="'.T_('Le port par défaut est 389 si vous utilisez un serveur LDAPS (sécurisé) le port est 636').'." class="fa fa-question-circle text-primary-m2"></i> <br />
											<div class="pt-1"></div>
											<label class="lbl" for="ldap_domain"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Domaine').' :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_domain" type="text" value="'.$rparameters['ldap_domain'].'" size="20" />
											<i title="'.T_('Nom du domaine FQDN (Exemple: exemple.local)').'." class="fa fa-question-circle text-primary-m2"></i> <br />
											<div class="pt-1"></div>
											<label class="lbl" for="ldap_url"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Emplacement des utilisateurs').' :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_url" type="text" value="'.$rparameters['ldap_url'].'" size="80" />
											<i title="'.T_("Emplacement dans l'annuaire des utilisateurs. Par défaut pour Active Directory cn=users, si vous utilisez plusieurs unités d'organisation séparer avec un point virgule (ou=France,ou=utilisateurs;ou=Belgique,ou=utilisateurs) Attention il ne doit pas être suffixé du domaine").'." class="fa fa-question-circle text-primary-m2"></i> <br />
											<div class="pt-1"></div>
											';
												if($rparameters['ldap_type']==0)
												{
													echo '
													<label class="lbl" for="ldap_login_field"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Champ identifiant').' : </label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_login_field" name="ldap_login_field" >
														<option ';if($rparameters['ldap_login_field']=='UserPrincipalName') {echo "selected ";} echo ' value="UserPrincipalName">UserPrincipalName</option>
														<option ';if($rparameters['ldap_login_field']=='SamAcountName') {echo "selected ";} echo ' value="SamAcountName">SamAcountName</option>
													</select>
													<i title="'.T_("Permet de configurer le champ AD à utiliser pour le login GestSup").'." class="fa fa-question-circle text-primary-m2"></i><br />
													<div class="pt-1"></div>
													';
												}
												
											echo '
											<label class="lbl" for="ldap_user"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Utilisateur').' : </label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_user" type="text" value="'.$rparameters['ldap_user'].'" size="20" />
											<i title="'.T_("Utilisateur présent dans l'annuaire LDAP, pour OpenLDAP l'utilisateur doit être à la racine et de type CN").'" class="fa fa-question-circle text-primary-m2"></i> <br />
											<div class="pt-1"></div>
											<label class="lbl" for="ldap_password"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Mot de passe').' :</label>
											<input style="width:auto" class="form-control form-control-sm d-inline-block" name="ldap_password" type="password" value="'.$rparameters['ldap_password'].'" size="20" /><br />
											';
											if($rparameters['ldap_agency']==0 && $rparameters['ldap_service']==0)
											{
												echo '
												<i class="fa fa-caret-right text-primary-m2"></i> '.T_('Désactiver les utilisateurs GestSup lors de la synchronisation').' : 
												<select style="width:auto" class="form-control form-control-sm d-inline-block" id="ldap_disable_user" name="ldap_disable_user" >
													<option ';if($rparameters['ldap_disable_user']==0) echo "selected "; echo ' value="0">Non</option>
													<option ';if($rparameters['ldap_disable_user']==1) echo "selected "; echo ' value="1">Oui</option>
												</select>
												<i title="'.T_("Désactive les utilisateurs présents dans GestSup, mais qui ne sont pas présent dans l'annuaire LDAP").'." class="fa fa-question-circle text-primary-m2"></i><br />
												<div class="pt-1"></div>
												';
											}
											echo '
											<br />
											<button name="test_ldap" value="1" type="submit" class="btn btn-xs btn-info">
												<i class="fa fa-exchange-alt"></i>
												'.T_('Test du connecteur LDAP').'
											</button>
											<br /><br />
											';
											//check LDAP parameters
											if($_GET['ldaptest']==1) {
												
												if($rparameters['ldap_sso']==1) {
													if(isset($_SERVER['REMOTE_USER']))
													{
														echo '&nbsp;&nbsp;<i title="'.T_('Le SSO est opérationnel').'" class="fa fa-check-circle text-success"></i> '.T_('Le SSO est opérationnel').'.';
													} else {
														echo '&nbsp;&nbsp;<i title="'.T_('le SSO ne fonctionne pas ').'" class="fa fa-times-circle text-danger"></i> '.T_('Le SSO ne fonctionne pas vérifier votre configuration serveur').'.';
													}
												}
												include('./core/ldap.php');
												echo "&nbsp;&nbsp;$ldap_connection<br />";
											} 
										}
										?>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-download text-blue-m3 pr-1"></i>
										IMAP :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<input type="checkbox" <?php if($rparameters['imap']==1) {echo "checked";} ?> name="imap" value="1">
											<span class="lbl">&nbsp<?php echo T_('Activer la liaison IMAP'); ?>
											<i title="<?php echo T_("Connecteur permettant de créer des tickets automatiquement en interrogeant une boite mail. Une fois le mail converti en ticket le message passe en lu dans la boite de messagerie. Attention une tâche planifiée doit être crée afin d'interroger de manière régulière la boite mail (cf FAQ)"); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
										<div class="pt-1"></div>
										<?php
										if($rparameters['imap']=='1') 
										{
											echo '
												<label class="lbl" for="imap_server" ><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Serveur').' :</label>
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_server" type="text" value="'.$rparameters['imap_server'].'" size="20" />
												<i title="'.T_('Adresse IP ou nom netbios ou nom FQDN du serveur IMAP de messagerie (ex: imap.free.fr, imap.gmail.com)').'" class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl" for="imap_port"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Port').' :</label>
												<select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_port" name="imap_port" >
													<option ';if($rparameters['imap_port']=='143') {echo "selected ";} echo ' value="143">143 (IMAP)</option>
													<option ';if($rparameters['imap_port']=="993/imap/ssl") {echo "selected ";} echo ' value="993/imap/ssl">993 (IMAP sécurisé)</option>
												</select>
												<i title="'.T_('Protocole utilisé sur le serveur POP ou IMAP sécurisé ou non (ex: pour free.fr sélectionner IMAP, pour gmail utiliser IMAP sécurisé)').'" class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl" for="imap_ssl_check"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Vérification SSL').' :</label>
												<select style="width:auto" class="form-control form-control-sm d-inline-block"  id="imap_ssl_check" name="imap_ssl_check" >
													<option ';if($rparameters['imap_ssl_check']==1) echo "selected "; echo' value="1">'.T_('Activée').'</option>
													<option ';if($rparameters['imap_ssl_check']==0) echo "selected "; echo' value="0">'.T_('Désactivée').'</option>
												</select>
												<i title="'.T_('Désactivation de la verification du certificat serveur et autorise les certificats auto-signés').'" class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl" for="inbox"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Dossier racine').' :</label>
												<select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_inbox" name="imap_inbox" >
													<option ';if($rparameters['imap_inbox']=='INBOX') {echo "selected ";} echo ' value="INBOX">INBOX</option>
													<option ';if($rparameters['imap_inbox']=='') {echo "selected ";} echo ' value="">'.T_('Aucun').'</option>
												</select>
												<i title="'.T_('Dossier racine ou se trouve les messages entrants (par défaut INBOX, pour gmail INBOX)').'" class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl" for="imap_user"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Adresse de messagerie').' :</label>
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_user" type="text" value="'.$rparameters['imap_user'].'" size="25" />
												<i title="'.T_("Adresse de la boite de messagerie à relever, pour exchange mettre le login utilisateur de la boite aux lettres ou le nom FQDN de l'utilisateur exemple: user@domain.local").'." class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl"  for="imap_password"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Mot de passe').' :</label>
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_password" type="password" value="'.$rparameters['imap_password'].'" size="20" /><br /><div class="pt-1"></div>
												<label class="lbl" >
													<input type="checkbox" '; if($rparameters['imap_reply']==1) {echo "checked";} echo ' name="imap_reply" value="1">
													<span class="lbl">&nbsp'.T_('Gérer les réponses aux mails').'
													<i title="'.T_("Ajoute des délimiteurs dans le mail, indiquant à l'utilisateur qu'il peut répondre au message envoyé").'" class="fa fa-question-circle text-primary-m2"></i>
												</label>
												<div class="pt-1"></div>
												<label>
													<input type="checkbox" '; if($rparameters['imap_mailbox_service']==1) {echo "checked";} echo ' name="imap_mailbox_service" value="1">
													<span class="lbl">&nbsp'.T_('Activer le multi boite aux lettres par service').'
													<i title="'.T_("Permet de relever plusieurs boites aux lettres et d'associer les tickets crées à des services GestSup").'" class="fa fa-question-circle text-primary-m2"></i>
												</label>
												<div class="pt-1"></div>
												';
												//display parameters of imap_mailbox_service
												if($rparameters['imap_mailbox_service']==1)
												{
													echo "<ul>";
													//display all existing association
													$qry = $db->prepare("SELECT `id`,`mail`,`service_id` FROM `tparameters_imap_multi_mailbox`");
													$qry->execute();
													while ($row = $qry->fetch()) 
													{
														//get service name do display
														$qry2 = $db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
														$qry2->execute(array('id' => $row['service_id']));
														$row2=$qry2->fetch();
														$qry2->closeCursor();
														echo '<li>'.$row['mail'].' > '.$row2['name'].' <a href="./index.php?page=admin&subpage=parameters&tab=connector&delete_imap_service='.$row['id'].'"><i title="'.T_("Supprimer l'association").'" class="fa fa-trash text-danger bigger-130"></i></a></li>';
													}
													$qry->closeCursor();
													echo "</ul>";
													//inputs for new association
													echo '&nbsp;&nbsp;&nbsp;
													<label class="lbl" for="mailbox_service">'.T_('Adresse mail').' :</label> <input name="mailbox_service" type="text" value="" size="20" />&nbsp
													<label class="lbl" for="mailbox_password">'.T_('Mot de passe').' :</label> <input name="mailbox_password" type="password" value="" size="20" />&nbsp
													<label class="lbl" for="mailbox_service_id">'.T_('Service').' :</label> 
													<select style="width:auto" class="form-control form-control-sm d-inline-block" id="mailbox_service_id" name="mailbox_service_id" >
														';
														$qry = $db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0'");
														$qry->execute();
														while ($row = $qry->fetch()) {echo'<option value="'.$row['id'].'">'.$row['name'].'</option>';}
														$qry->closeCursor();
														echo '
													</select>
													<div class="pt-1"></div>
													';
												}
												echo '
												<label class="lbl" for="imap_blacklist"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Adresses à exclure').' :</label>
												<input style="width:auto" class="form-control form-control-sm d-inline-block" name="imap_blacklist" type="text" value="'.$rparameters['imap_blacklist'].'" size="60" />
												<i title="'.T_("Permet d'ajouter des adresses mail et/ou des domaines à exclure de la récupération des messages. Le séparateur est le point virgule exemple: john.doe@example.com;example2.com;outlook").'." class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>
												<label class="lbl" for="imap_post_treatment"><i class="fa fa-caret-right text-primary-m2"></i> '.T_('Action post traitement').' :</label>
												<select style="width:auto" class="form-control form-control-sm d-inline-block" id="imap_post_treatment" name="imap_post_treatment" >
													<option ';if($rparameters['imap_post_treatment']=='move') {echo "selected ";} echo ' value="move">Déplacer le mail dans un repertoire</option>
													<option ';if($rparameters['imap_post_treatment']=='delete') {echo "selected ";} echo ' value="delete">Supprimer le mail</option>
													<option ';if($rparameters['imap_post_treatment']=='') {echo "selected ";} echo ' value="">'.T_('Passer en lu le mail').'</option>
												</select>
												<i title="'.T_('Permet de spécifier une action sur le mail de la boite aux lettre, une fois le mail convertit en ticket').'" class="fa fa-question-circle text-primary-m2"></i>
												';
												if($rparameters['imap_post_treatment']=='move') {echo '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.T_('Répertoire').': <input name="imap_post_treatment_folder" type="text" value="'.$rparameters['imap_post_treatment_folder'].'"  /> <i title="'.T_('Permet de spécifier un répertoire de la messagerie dans lequel le mail sera déplacé exemple: INBOX/vu').'" class="fa fa-question-circle text-primary-m2"></i>';}
												echo'
												<div class="pt-1"></div>
												<button name="test_imap" OnClick="window.open(\'./mail2ticket.php\')"  value="test_imap" type="submit" class="btn btn-xs btn-info">
													<i class="fa fa-download"></i>
													'.T_("Lancer l'import des mails").'
												</button>
											';
										}
										?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
							<button name="submit_connector" id="submit_connector" value="submit_connector" type="submit" class="btn btn-success">
								<i class="fa fa-check"></i>
								<?php echo T_('Valider'); ?>
							</button>
					</div>
				</form>
			</div>
			<!-- /////////////////////////////////////////////////////////////// functions tab /////////////////////////////////////////////////////////////// -->
			<div id="function" class="tab-pane <?php if($_GET['tab']=='function') echo 'active'; ?>">
				<form enctype="multipart/form-data" method="POST" action="">
					<div class="table-responsive">
						<table class="table table table-bordered">
							<tbody>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-calendar text-blue-m3 pr-1"></i>
									<?php echo T_('Calendrier'); ?> :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<input type="checkbox" <?php if($rparameters['planning']==1) echo "checked"; ?> name="planning" value="1">
											<span class="lbl"><?php echo T_('Activer la fonction Calendrier'); ?></span>
											<i title="<?php echo T_('Active la gestion de planning, nouvel onglet et gestion dans les tickets'); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-desktop text-blue-m3 pr-1"></i>
										<?php echo T_('Équipement'); ?> :
										</td>
										<td class="text-95 text-default-d3">
											<label>
											<input type="checkbox" <?php if($rparameters['asset']==1) echo "checked"; ?> name="asset" value="1" />
											<span class="lbl">&nbsp;<?php echo T_('Activer la fonction gestion des équipements'); ?></span>
											<i title="<?php echo T_('Active la gestion des équipements, affiche un nouvel item dans le menu de gauche'); ?>." class="fa fa-question-circle text-primary-m2"></i>
										</label>	
										<?php
										if($rparameters['asset']==1)
										{
											echo'
											<div class="pt-1"></div>
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_('Gestion des adresses IP').' :&nbsp;
											<label for="asset_ip">
												<input type="radio" value="1" name="asset_ip"'; if($rparameters['asset_ip']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
												<input type="radio" value="0" name="asset_ip"'; if($rparameters['asset_ip']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
											</label>
											<i title="'.T_("Permet d'afficher dans la liste des équipements une colonne adresse IP, active les également des champs additionnels sur les fiches des équipements").'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_('Gestion des garanties').' :&nbsp;
											<label for="asset_warranty">
												<input type="radio" value="1" name="asset_warranty"'; if($rparameters['asset_warranty']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
												<input type="radio" value="0" name="asset_warranty"'; if($rparameters['asset_warranty']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
											</label>
											<i title="'.T_('Affiche un nouvel item dans le menu des équipements, permettant de visualiser les équipements en fin de garantie').'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											';
											if($rparameters['asset_ip']==1)
											{
												echo '
												&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
												'.T_("Activer le lien VNC sur l'équipement").' :&nbsp;
												<label for="asset_vnc_link">
														<input type="radio" value="1" name="asset_vnc_link"'; if($rparameters['asset_vnc_link']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
														<input type="radio" value="0" name="asset_vnc_link"'; if($rparameters['asset_vnc_link']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
												</label>
												<i title="'.T_("Affiche un nouveau bouton sur la fiche de l'équipement permettant de prendre la main si un serveur VNC web est installé sur le client").'" class="fa fa-question-circle text-primary-m2"></i>
												<div class="pt-1"></div>';
											}
											echo '
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_('Importer des équipements').' :&nbsp;
											<input style="display:inline" type="file" id="asset_import" name="asset_import" />
											<a title="'.T_("Télécharger le modèle CSV pour ensuite pouvoir lancer l'import").'" href="./download/tassets_template.csv">'.T_('Modèle').'</a>
											<i title="'.T_("Permet d'importer des équipements en lot depuis un fichier CSV").'" class="fa fa-question-circle text-primary-m2"></i>
											';
										}
										?>	
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-book text-blue-m3 pr-1"></i>
									<?php echo T_('Procédure'); ?> :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<input type="checkbox" <?php if($rparameters['procedure']==1) {echo "checked";} ?> name="procedure" value="1" /> 
											<span class="lbl">&nbsp;<?php echo T_('Activer la fonction procédure'); ?></span>
											<i title="<?php echo T_('Active la gestion des procédures'); ?>" class="fa fa-question-circle text-primary-m2"></i>
										</label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-tasks text-blue-m3 pr-1"></i>
									<?php echo T_('Projet'); ?> :
									</td>
									<td class="text-95 text-default-d3">
										<label>
										<input type="checkbox" <?php if($rparameters['project']==1) {echo "checked";} ?> name="project" value="1" /> 
										<span class="lbl">&nbsp;<?php echo T_('Activer la fonction projet'); ?></span>
										<i title="<?php echo T_('Active la gestion des projets, visualisation de jonction de tickets'); ?>" class="fa fa-question-circle text-primary-m2"></i>
									</label>
									</td>
								</tr>
								<tr>
									<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4">
										<i class="fa fa-check text-blue-m3 pr-1"></i>
									<?php echo T_('Sondage'); ?> :
									</td>
									<td class="text-95 text-default-d3">
										<label>
											<?php
												if($rparameters['mail'] && $rparameters['mail_smtp']) //check if SMTP connector is enabled, before display survey section
												{
													echo '
													<input type="checkbox"  ';if($rparameters['survey']==1) {echo "checked";} echo ' name="survey" value="1" />
													<span class="lbl">&nbsp;'.T_('Activer la fonction sondage').'</span>
													<i title="'.T_("Active la gestion d'un sondage utilisateur, permettant à un utilisateur de remplir un questionnaire de satisfaction sur un ticket ").'" class="fa fa-question-circle text-primary-m2"></i>
													';
												} else {
													echo '<i class="fas fa-exclamation-triangle text-warning-m1"></i>&nbsp;<span class="text-warning-m1">'.T_('Le connecteur SMTP doit être configuré, pour activer cette fonction').'.</span>';
												}
											?>
										</label>
										<?php
										if($rparameters['survey']==1)
										{
											echo'
											<div class="pt-1"></div>
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_("Envoyer un mail avec un lien vers le sondage à l'utilisateur lorsque le ticket passe dans l'état").' :&nbsp;
											<select style="width:auto" class="form-control form-control-sm d-inline-block" name="survey_ticket_state">
												';
											$qry = $db->prepare("SELECT `id`,`name` FROM `tstates` ORDER by number");
											$qry->execute();
											while ($row=$qry->fetch())
											{
												if($rparameters['survey_ticket_state']==$row['id']) {$selected='selected';} else {$selected='';}
												echo '<option value="'.$row['id'].'" '.$selected.' >'.$row['name'].'</option>';
											}
											$qry->closecursor();
												echo '
											</select>
											<i title="'.T_("Envoi un mail en destination de l'utilisateur lorsque le ticket passe dans l'état sélectionné, exemple état attente retour client").'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_("Texte du mail envoyé à l'utilisateur pour le sondage, vous pouvez utiliser la balise [ticket_link] afin d'insérer un lien vers le ticket").' :&nbsp;<i title="'.T_("Texte du mail que l'utilisateur va recevoir afin de lui indiquer de remplir un sondage en cliquant sur un lien").'" class="fa fa-question-circle text-primary-m2"></i><br />
											&nbsp; &nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;<textarea style="width:auto" class="form-control form-control-sm d-inline-block" cols="60" rows="3" name="survey_mail_text">'.$rparameters['survey_mail_text'].'</textarea>
											<div class="pt-1"></div>&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_("Clôturer automatiquement le ticket lorsque le sondage a été validé par l'utilisateur").' :&nbsp;
											<label for="survey_auto_close_ticket">
													<input type="radio" value="1" name="survey_auto_close_ticket"'; if($rparameters['survey_auto_close_ticket']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
													<input type="radio" value="0" name="survey_auto_close_ticket"'; if($rparameters['survey_auto_close_ticket']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
											</label>
											<i title="'.T_("Modifie l'état du ticket en résolu si l'utilisateur à remplit et validé le sondage").'" class="fa fa-question-circle text-primary-m2"></i>
											<div class="pt-1"></div>
											&nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
											'.T_('Liste des questions du sondage').' :<br />';
											$qry = $db->prepare("SELECT * FROM `tsurvey_questions` ORDER by number");
											$qry->execute();
											while ($row=$qry->fetch())
											{
												echo '<div class="pt-1"></div>';
												echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
												echo '<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="survey_question_number_'.$row['id'].'" size="1" value="'.$row['number'].'"></input>&nbsp;&nbsp;';
												echo '<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" name="survey_question_text_'.$row['id'].'" size="50" value="'.$row['text'].'" ></input>&nbsp;&nbsp;';
												echo 'Type :&nbsp;';
												echo '
													<select style="width:auto" class="form-control form-control-sm d-inline-block" name="survey_question_type_'.$row['id'].'" >
														<option '; if($row['type']==1) {echo 'selected';} echo ' value="1">'.T_('Oui/Non').'</option>
														<option '; if($row['type']==2) {echo 'selected';} echo ' value="2">'.T_('Texte').'</option>
														<option '; if($row['type']==3) {echo 'selected';} echo ' value="3">'.T_('Liste déroulante').'</option>
														<option '; if($row['type']==4) {echo 'selected';} echo ' value="4">'.T_('Échelle').'</option>
													</select>
												';
												//display scale size filed if selected
												if($row['type']==4) {echo '&nbsp;&nbsp;Valeur maximum: <input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="2" name="survey_question_scale_'.$row['id'].'" value="'.$row['scale'].'" />';}
												if($row['type']==3) {
												echo '&nbsp;&nbsp;Choix :&nbsp;
													<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_1_'.$row['id'].'" value="'.$row['select_1'].'" />
													<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_2_'.$row['id'].'" value="'.$row['select_2'].'" />
													<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_3_'.$row['id'].'" value="'.$row['select_3'].'" />
													<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_4_'.$row['id'].'" value="'.$row['select_4'].'" />
													<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="5" name="survey_question_select_5_'.$row['id'].'" value="'.$row['select_5'].'" />
												';
												}
												echo '&nbsp;&nbsp;<a href="./index.php?page=admin&subpage=parameters&tab=function&deletequestion='.$row['id'].'"><i class="fa fa-trash text-danger bigger-130" title="'.T_('Supprimer la question').'"></i></a>';
												echo '<br />';
											}
											$qry->closecursor();
											//display fields for new question
											echo '
												<div class="pt-1"></div>
												&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
												<input style="width:auto" class="form-control form-control-sm d-inline-block" placeholder="N°" type="text" name="survey_new_question_number" size="1" ></input>&nbsp;
												<input style="width:auto" class="form-control form-control-sm d-inline-block" placeholder="'.T_('Texte de la nouvelle question').'" type="text" name="survey_new_question_text" size="50" ></input>&nbsp;
												Type :&nbsp;
												<select style="width:auto" class="form-control form-control-sm d-inline-block"  name="survey_new_question_type">
													<option value="1">'.T_('Oui/Non').'</option>
													<option value="2">'.T_('Texte').'</option>
													<option value="3">'.T_('Liste déroulante').'</option>
													<option value="4">'.T_('Échelle').'</option>
												</select>
											';
											//display export button
											echo '
											<br />
											<br />
											&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<button title="'.T_("Télécharger les résultats du sondage au format CSV").'" name="dump_survey" OnClick="window.open(\'./core/export_survey.php?token='.$_COOKIE['token'].'\')"  value="dump_survey" type="submit" class="btn btn-xs btn-info">
													<i class="fa fa-download"></i>
													'.T_("Exporter les résultats").'
												</button>
											<br />
											<br />
											';
										}
										?>
									</td>
								</tr>
								<?php include('./plugins/availability/admin/parameters.php') ?>
							</tbody>
						</table>
					</div>
					<div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center">
						<button name="submit_function" id="submit_function" value="submit_function" type="submit" class="btn btn-success">
							<i class="fa fa-check"></i>
							<?php echo T_('Valider'); ?>
						</button>
					</div>
					<div class="pt-1"></div>
				</form>				
			</div>
		</div>
	</div>
</div>