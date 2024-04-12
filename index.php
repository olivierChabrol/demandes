<?php
################################################################################
# @Name : index.php
# @Description : main page include all sub-pages
# @Call :
# @Parameters :
# @Author : Flox
# @Create : 07/03/2010
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

//includes
require('core/init_get.php');
require('core/functions.php');
require_once('models/tool/sql.php');
require_once('models/tool/mailer.php');

use Models\Tool\Sql;
use Models\Tool\Mailer;

//initialize variables
if(!isset($guestid)) $guestid = 529;
if(!isset($currentpage)) $currentpage = '';
if(!isset($_SERVER['HTTP_USER_AGENT'])) $_SERVER['HTTP_USER_AGENT'] = '';
if(!isset($_COOKIE['token'])) $_COOKIE['token'] = '';
if(!isset($_GET['page'])) $_GET['page'] = '';

//cookies initialization
if($_GET['page']!='register'){session_name(md5_file('connect.php'));}
session_start();

if($_GET['page']!='ticket' && $_GET['page']!='admin' && $_GET['page'] && $_GET['page']!='procedure' && $_GET['page']!='request') //avoid upload problems
{
    //avoid back problem with browser
    if(!empty($_POST) OR !empty($_FILES))
    {
        $_SESSION['bkp_post'] = $_POST;
        if(!empty($_SERVER['QUERY_STRING'])){ $currentpage .= '?' . $_SERVER['QUERY_STRING'];}
        header('Location: ' . $currentpage);
        exit;
    }
    if(isset($_SESSION['bkp_post']))
    {
        $_POST=$_SESSION['bkp_post'];
        unset($_SESSION['bkp_post']);
    }
}

//mobile detection
if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr(isset($_SERVER['HTTP_USER_AGENT']),0,4)))
{$mobile=1;} else {$mobile=0;}




//initialize variables
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['profile_id'])) $_SESSION['profile_id'] = '';
if(!isset($_POST['keywords'])) $_POST['keywords'] = '';
if(!isset($_POST['userkeywords'])) $_POST['userkeywords'] = '';
if(!isset($_POST['assetkeywords'])) $_POST['assetkeywords'] = '';
if(!isset($_POST['rightkeywords'])) $_POST['rightkeywords'] = '';
if(!isset($_POST['procedurekeywords'])) $_POST['procedurekeywords'] = '';
if(!isset($keywords)) $keywords = '';
if(!isset($ruser['skin'])) $ruser['skin'] = '';

if($_SESSION['user_id'] == $guestid){
    session_destroy();
    session_start();
}

//default values
if(empty($_GET['page'])) $_GET['page'] = 'dashboard';
if(!isset($_GET['userid'])) $_GET['userid'] = $_SESSION['user_id'];

//redirect to home page on log-off
if($_GET['action'] == 'logout')
{
	$_SESSION = array();
	session_destroy();
	session_unset();
	session_start();
}

//init session variables
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SESSION['LAST_ACTIVITY'])) $_SESSION['LAST_ACTIVITY'] = 0;

//detect https connection
if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {$http='https';} else {$http='http';}

//detect web server
if(preg_match('#Microsoft-IIS#is', $_SERVER["SERVER_SOFTWARE"])) {$webserver='IIS';} else {$webserver='Apache';}

//redirect to install directory
if(preg_match('#db_name=\'\'#',file_get_contents('connect.php')))
{
	if(is_dir('install')) {echo "<SCRIPT LANGUAGE='JavaScript'>function redirect(){window.location='install'} setTimeout('redirect()',0);</SCRIPT>"; exit;}
}

//connexion script with database parameters
require('connect.php');
$sql = Sql::getInstance($db);

//switch SQL MODE to allow empty values
$db->exec('SET sql_mode = ""');

$db_userid=strip_tags($db->quote($_GET['userid']));
$db_id=strip_tags($db->quote($_GET['id']));

//load parameters table
$qry=$db->prepare("SELECT * FROM `tparameters`");
$qry->execute();
$rparameters=$qry->fetch();
$qry->closeCursor();

$mailer = Mailer::getInstance($rparameters);

//log off on timeout
if($rparameters['timeout'])
{
	if($rparameters['debug']) {$session_time='time='.(time() - $_SESSION['LAST_ACTIVITY']).'max='.(60*$rparameters['timeout']);}
	if($_SESSION['LAST_ACTIVITY'] && (time() - $_SESSION['LAST_ACTIVITY'] > 60*$rparameters['timeout'])) {
		session_unset();
		session_destroy();
		if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
		if(!isset($_SESSION['LAST_ACTIVITY'])) $_SESSION['LAST_ACTIVITY'] = '';
	}
	if($_GET['page']=='dashboard' && $rparameters['auto_refresh']!=0 ) {} else {$_SESSION['LAST_ACTIVITY'] = time();}
	if(!$_SESSION['LAST_ACTIVITY']) {$_SESSION['LAST_ACTIVITY'] = time();}
} elseif($rparameters['auto_refresh']!=0) {
	$maxlifetime = ini_get("session.gc_maxlifetime");
	if($rparameters['debug']) {$session_time='time='.(time() - $_SESSION['LAST_ACTIVITY']).'max='.$maxlifetime;}
	if($_SESSION['LAST_ACTIVITY'] && (time() - $_SESSION['LAST_ACTIVITY'] > $maxlifetime)) {
		session_unset();
		session_destroy();
		if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
		if(!isset($_SESSION['LAST_ACTIVITY'])) $_SESSION['LAST_ACTIVITY'] = '';
	}
	if($_GET['page']!='dashboard') {$_SESSION['LAST_ACTIVITY'] = time();}
	if(!$_SESSION['LAST_ACTIVITY']) {$_SESSION['LAST_ACTIVITY'] = time();}
}

//define timezone
if($rparameters['server_timezone']) {date_default_timezone_set($rparameters['server_timezone']);}

//load common variables
$daydate=date('Y-m-d');
$datetime=date("Y-m-d H:i:s");

//display error parameter
if($rparameters['debug']) {
	ini_set('display_errors', 'On');
	ini_set('display_startup_errors', 'On');
	ini_set('html_errors', 'On');
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', 'Off');
	ini_set('display_startup_errors', 'Off');
	ini_set('html_errors', 'Off');
	error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
}

//if user is connected
if($_SESSION['user_id'])
{
	//load variables
	$uid=$_SESSION['user_id'];

	//load user table
	$qry=$db->prepare("SELECT * FROM `tusers` WHERE id=:id");
	$qry->execute(array('id' => $_SESSION['user_id']));
	$ruser=$qry->fetch();
	$qry->closeCursor();

	//find profile id of connected user
	$_SESSION['profile_id']=$ruser['profile'];

	//load rights table
	$qry=$db->prepare("SELECT * FROM `trights` WHERE profile=:profile");
	$qry->execute(array('profile' => $_SESSION['profile_id']));
	$rright=$qry->fetch();
	$qry->closeCursor();

	//set token cookie
	if(!$_COOKIE['token'])
	{
		$token = uniqid(32);
		setcookie('token', $token, time()+1800);
		$_COOKIE['token']=$token;
	}

	//set role of profile
	if($_SESSION['profile_id']==0)	{$profile="technician";}
	elseif($_SESSION['profile_id']==1)	{$profile="user";}
	elseif($_SESSION['profile_id']==4)	{$profile="technician";}
	elseif($_SESSION['profile_id']==3) {$profile="user";}
	else {$profile="user";}
}
// user not connected
else {
    if ($_GET["token"]) {
        $qry = $db->prepare("SELECT `incident_id` FROM `dmission_order` WHERE `invitation_token` =:token");
        $qry->execute(array('token' => $_GET["token"]));
        if ($qry->rowCount()) {
            $_GET['id'] = $qry->fetch()['incident_id'];
            $_SESSION['user_id'] = $guestid;

            $qry = $db->prepare("SELECT * FROM `trights` WHERE profile=:profile");
            $qry->execute(array('profile' => 1));
            $rright = $qry->fetch();
            $qry->closeCursor();

            //set role of profile
            if ($_SESSION['profile_id'] == 0) {
                $profile = "technician";
            } elseif ($_SESSION['profile_id'] == 1) {
                $profile = "user";
            } elseif ($_SESSION['profile_id'] == 4) {
                $profile = "technician";
            } elseif ($_SESSION['profile_id'] == 3) {
                $profile = "user";
            } else {
                $profile = "user";
            }

            require "invitation.php";
            exit;
        };
    }
}

//define current language
require "localization.php";

//put keywords in variable
if($_POST['keywords']||$_GET['keywords']) {
	$keywords="$_GET[keywords]$_POST[keywords]";
	$keywords=htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8');
} else {$keywords='';}
if($_POST['userkeywords']||$_GET['userkeywords']) {
	$userkeywords="$_GET[userkeywords]$_POST[userkeywords]";
	$userkeywords=htmlspecialchars($userkeywords, ENT_QUOTES, 'UTF-8');
} else {$userkeywords='';}
if($_POST['assetkeywords']||$_GET['assetkeywords']) {
	$assetkeywords="$_GET[assetkeywords]$_POST[assetkeywords]";
	$assetkeywords=htmlspecialchars($assetkeywords, ENT_QUOTES, 'UTF-8');
} else {$assetkeywords='';}
if($_POST['rightkeywords']||$_GET['rightkeywords']) {
	$rightkeywords="$_GET[rightkeywords]$_POST[rightkeywords]";
	$rightkeywords=htmlspecialchars($rightkeywords, ENT_QUOTES, 'UTF-8');
} else {$rightkeywords='';}
if($_POST['procedurekeywords']||$_GET['procedurekeywords']) {
	$procedurekeywords="$_GET[procedurekeywords]$_POST[procedurekeywords]";
	$procedurekeywords=htmlspecialchars($procedurekeywords, ENT_QUOTES, 'UTF-8');
} else {$procedurekeywords='';}

//download backup file
if($_GET['download_backup'] && $rright['admin'] && $_SESSION['user_id']) {header("location: ./backup/$_GET[download_backup]");}

//download attachment file
if($_GET['download'] && $_SESSION['user_id']) {require('core/download.php'); exit;}
?>
<!doctype html>
<html lang="fr" style="--scrollbar-width:17px; --moz-scrollbar-thin:17px; font-size: 0.925rem;">
	<head>
	    <?php header('x-ua-compatible: ie=edge'); //disable ie compatibility mode ?>
		<meta charset="utf-8" />
		<meta name="theme-color" content="#4aa0df">
		<?php
		if($_SESSION['user_id'] && $rparameters['auto_refresh'] && $_GET['page']=='dashboard' && !$_POST['keywords'])
		{echo '<meta http-equiv="Refresh" content="'.$rparameters['auto_refresh'].';">'; }
		?>
		<title>SIGED | <?php echo T_('Gestion des demandes administratives'); ?></title>
		<link rel="shortcut icon" type="image/png" href="./images/
		<?php
		if($_GET['page']=='asset_list' || $_GET['page']=='asset' || $_GET['page']=='asset_stock') {echo 'favicon_asset.png';}
		elseif($_GET['page']=='procedure') {echo 'favicon_procedure.png';}
		elseif($_GET['page']=='calendar') {echo 'favicon_planning.png';}
		elseif($_GET['page']=='plugins/availability/index') {echo 'favicon_availability.png';}
		elseif($_GET['page']=='stat') {echo 'favicon_stat.png';}
		elseif($_GET['page']=='admin') {echo 'favicon_admin.png';}
		elseif($_GET['page']=='project') {echo 'favicon_project.png';}
		else {echo 'favicon_ticket.png';}
		?>"
		/>
		<meta name="description" content="gestsup" />
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">

		<!-- bootstrap styles -->
		<link rel="stylesheet" href="./components/bootstrap/dist/css/bootstrap.min.css" />

		<!-- fontawesome styles -->
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/solid.min.css">

		<?php
		//add special css for selected page
		if(($_GET['page']=='ticket') || ($_GET['page']=='asset')|| ($_GET['page']=='test') || ($_GET['page']=='dashboard' && $_GET['view']=='activity') || ($_GET['page']=='request'))
		{
			echo '
			<!-- datetimepicker styles -->
			<link rel="stylesheet" href="./components/tempus-dominus/build/css/tempusdominus-bootstrap-4.min.css" />
			';
		}
		if(($_GET['page']=='ticket') || ($_GET['page']=='asset') || ($_GET['page']=='dashboard') || ($_GET['page']=='admin/user') || ($_GET['subpage']=='user'))
		{
			echo '
			<!-- chosen styles -->
			<link rel="stylesheet" type="text/css" href="./components/chosen/chosen.min.css">
			';
		}
		if($_GET['page']=='calendar')
		{
			echo '
			<!-- fullcalendar5 styles -->
			<link rel="stylesheet" href="./components/fullcalendar/lib/main.min.css" />
			';
		}
		if($_GET['page']=='project')
		{
			echo '
			<!-- smartwizard styles -->
			<link rel="stylesheet" type="text/css" href="./components/smartwizard/dist/css/smart_wizard.min.css" />
			<link rel="stylesheet" type="text/css" href="./components/smartwizard/dist/css/smart_wizard_theme_circles.min.css" />
			';
		}
		?>
		<!-- ace styles -->
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-font.min.css" />
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace.min.css" />
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-themes.min.css" />

		<?php if($ruser['skin']=='skin-4') {echo '<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/gestsup-dark.min.css" />';} ?>

        <!-- Request page styles -->
        <link rel="stylesheet" type="text/css" href="template/assets/css/request.css" />
        <link rel="stylesheet" type="text/css" href="template/assets/css/select2.min.css" />

		<!-- JQuery script -->
		<script type="text/javascript" src="./components/jquery/jquery.min.js"></script>

        <link rel="stylesheet" href="./components/jquery-ui/jquery-ui.css">
        <script src="./components/jquery-ui/jquery-ui.js"></script>

        <!-- Request script -->
        <script type="text/javascript" src="./template/assets/js/request-common.js"></script>
	</head>
	<?php
		if($_SESSION['user_id']) //logon user case
		{
			require('main.php');
		} else { //not login
			//launch cron
			if($rparameters['cron_daily']!=date('Y-m-d')) {require('./core/cron.php');}

			//check restrict IP access
			if($rparameters['restrict_ip'])
			{
				$ipcheck=0;
				$allow_ip=explode(',',$rparameters['restrict_ip']);
				foreach($allow_ip as $ip)
				{
					if(preg_match("#$ip#", $_SERVER['REMOTE_ADDR'])) {$ipcheck=1;}
					//allow localhost
					if(preg_match("#127.0.0.1#", $_SERVER['REMOTE_ADDR'])) {$ipcheck=1;}
					if(preg_match("#localhost#", $_SERVER['REMOTE_ADDR'])) {$ipcheck=1;}
					if(preg_match("#fe80::#", $_SERVER['REMOTE_ADDR'])) {$ipcheck=1;}
					if(preg_match("#::1#", $_SERVER['REMOTE_ADDR'])) {$ipcheck=1;}
				}
				if($ipcheck==0) {echo '<div class="space-10"></div><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert"><i class="fa fa-times-circle text-danger"></i></button><strong><i class="fa fa-times-circle text-danger"></i> '.T_('Erreur').'</strong> : '.T_("Vous n'avez pas accès à ce logiciel.").' ('.$_SERVER['REMOTE_ADDR'].')<br></div>';}
			} else {$ipcheck=1;}

			//check SSO
			if($rparameters['ldap_sso'] && isset($_SERVER['REMOTE_USER']) && $_GET['action']!='logout' && $ipcheck==1)
			{
				require('core/sso.php');
			} elseif($ipcheck==1) {
				if($_GET['page']=='register') {require('register.php');}
				elseif($_GET['page']=='forgot_pwd') {require('forgot_pwd.php');}
				else {require('login.php');}
			}
		}
		//loading js scripts
		echo'
		  <script type="text/javascript" src="./components/popper-js/dist/umd/popper.min.js"></script>
		  <script type="text/javascript" src="./components/bootstrap/dist/js/bootstrap.min.js"></script>
		  <script type="text/javascript" src="./template/ace/dist/js/ace.min.js"></script>
		  <script type="text/javascript" src="./template/assets/js/select2/select2.min.js"></script>
		';
		if($_SESSION['user_id'])
		{
			//include specific script for page
			if($_GET['page']=='ticket' || $_GET['page']=='procedure' || $_GET['page']=='request') {include ('./wysiwyg.php');}

			if(($_GET['page']=='ticket') || ($_GET['page']=='asset') || ($_GET['page']=='dashboard') || ($_GET['page']=='admin/user') || ($_GET['subpage']=='user'))
			{
				echo '
				<script type="text/javascript" src="./components/chosen/chosen.jquery.min.js"></script>
				<script>
					if($(".chosen-select"))
						$(\'.chosen-select\').chosen({allow_single_deselect:true,no_results_text: "'.T_('Aucun résultat pour').'"});
						';
						if($_GET['page']=='dashboard'){echo '$(\'.chosen-drop\').css({width:"200px"});';}
						echo '
				</script>
				';
			}

			//log off popup 500000
			if(!$rparameters['ldap_sso'])
			{
				if($rparameters['timeout']) {$timeout=$rparameters['timeout']*60000;} else {$timeout=ini_get("session.gc_maxlifetime")*1000;}
				if($timeout>9000000000) {$timeout='9000000000';} #3661 bug
				echo '
					<script type="text/javascript">
						setInterval(function(){
							window.alert("'.T_('Session expirée').'");
							window.location.href="index.php?action=logout";
						},'.$timeout.');
					</script>
				';
			}

			//call reminder popup
			include "./reminder.php";

			//call pwd switch popup
			if($ruser['chgpwd']){include "./modify_pwd.php";}
			if($rparameters['user_password_policy'] && $rparameters['user_password_policy_expiration']!=0)
			{
				$password_expiration_date=date('Y-m-d', strtotime($ruser['last_pwd_chg']. ' + '.$rparameters['user_password_policy_expiration'].' days'));
				if($password_expiration_date < date('Y-m-d') && $ruser['last_pwd_chg']!='0000-00-00') {include "./modify_pwd.php";}
			}
		}
		//close database access
		$db = null;
        ?>
</html>