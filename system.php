<?php
################################################################################
# @Name : system.php
# @Description :  admin system
# @Call : ./admin.php, install/index.php
# @Parameters : 
# @Author : Flox
# @Create : 10/11/2013
# @Update : 11/09/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
require_once("core/init_get.php");

//for install call
if($_GET['page']=='admin') 
{
	require ('./connect.php');
} else {
	require ('../connect.php');
	
	//load parameters table
	$qry=$db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//mobile detection
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4)))
	{$mobile=1;} else {$mobile=0;}
}

//create private server key if not exist used to auto-installation URL
if($rparameters['server_private_key']=='') 
{
	$key=md5(uniqid());
	$qry=$db->prepare("UPDATE `tparameters` SET `server_private_key`=:server_private_key WHERE `id`=1");
	$qry->execute(array('server_private_key' => $key));
}

//detect https connection
if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {$http='https';} else {$http='http';}

//extract php info
ob_start();
phpinfo();
$phpinfo = array('phpinfo' => array());
if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match)
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        elseif(isset($match[3])){
			$ak=array_keys($phpinfo);
            $phpinfo[end($ak)][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
			}
        else
            {
			$ak=array_keys($phpinfo);
            $phpinfo[end($ak)][] = $match[2];
		}

//find PHP table informations, depends of PHP versions			
if(isset($phpinfo['Core'])!='') $vphp='Core';
elseif(isset($phpinfo['PHP Core'])!='') $vphp='PHP Core';
elseif(isset($phpinfo['HTTP Headers Information'])!='') $vphp='HTTP Headers Information'; 

//initialize variables 
if(!isset($_POST['Modifier'])) $_POST['Modifier'] = '';
if(!isset($phpinfo[$vphp]['file_uploads'][0])) $phpinfo[$vphp]['file_uploads'][0] = '';
if(!isset($phpinfo[$vphp]['memory_limit'][0])) $phpinfo[$vphp]['memory_limit'][0] = '';
if(!isset($phpinfo[$vphp]['upload_max_filesize'][0])) $phpinfo[$vphp]['upload_max_filesize'][0] = '';
if(!isset($phpinfo[$vphp]['post_max_size'][0])) $phpinfo[$vphp]['post_max_size'][0] = '';
if(!isset($phpinfo[$vphp]['max_execution_time'][0])) $phpinfo[$vphp]['max_execution_time'][0] = '';
if(!isset($phpinfo['date']['date.timezone'][0])) $phpinfo['date']['date.timezone'][0] = '';
if(!isset($i)) $i = '';
if(!isset($php_error)) $php_error = '';
if(!isset($php_warning)) $php_warning = '';

//SQL db connect
if($_GET['page']!='admin') {require('../connect.php');}

//get rdb database version 
$qry=$db->prepare("SHOW VARIABLES");
$qry->execute();
while($row=$qry->fetch()) 
{
	if($row[0]=="version") {
		$rdb_version=$row[1];
		if(strpos($rdb_version, 'MariaDB')) {
			$rdb_name='MariaDB';
			$rdb_icon=explode('-',$rdb_version);
			$rdb_icon=explode('.',$rdb_icon[0]);
			if($rdb_icon[0]>=10 && $rdb_icon[1]>=1) {$rdb_icon='ok';} else {$rdb_icon='ko';}
		} else {
			$rdb_name='MySQL';
			$rdb_icon='ok';
		}
	}
}
$qry->closeCursor();

//check OS
$os=$phpinfo['phpinfo']['System'];
$os= explode(" ",$os);
$os=$os[0];

//check and convert current ram value in MB value to check prerequisites
$ram=$phpinfo[$vphp]['memory_limit'][0];
if(preg_match("/M/",$ram)) {$ram_mb=explode('M',$ram);$ram_mb=$ram_mb[0];}
if(preg_match("/m/",$ram)) {$ram_mb=explode('m',$ram);$ram_mb=$ram_mb[0];}
if(preg_match("/G/",$ram)) {$ram_mb=explode('G',$ram);$ram_mb=$ram_mb[0]*1024;}
if(preg_match("/g/",$ram)) {$ram_mb=explode('g',$ram);$ram_mb=$ram_mb[0]*1024;}
if(!$ram_mb) {$ram_mb=$phpinfo[$vphp]['memory_limit'][0];}

$max_filesize=$phpinfo[$vphp]['upload_max_filesize'][0];
if(preg_match("/M/",$max_filesize)) {$max_filesize_mb=explode('M',$max_filesize);$max_filesize_mb=$max_filesize_mb[0];}
if(preg_match("/m/",$max_filesize)) {$max_filesize_mb=explode('m',$max_filesize);$max_filesize_mb=$max_filesize_mb[0];}
if(preg_match("/G/",$max_filesize)) {$max_filesize_mb=explode('G',$max_filesize);$max_filesize_mb=$max_filesize_mb[0]*1024;}
if(preg_match("/g/",$max_filesize)) {$max_filesize_mb=explode('g',$max_filesize);$max_filesize_mb=$max_filesize_mb[0]*1024;}
if(!$max_filesize_mb) {$max_filesize_mb=$phpinfo[$vphp]['upload_max_filesize'][0];}

$post_max_size=$phpinfo[$vphp]['post_max_size'][0];
if(preg_match("/M/",$post_max_size)) {$post_max_size_mb=explode('M',$post_max_size);$post_max_size_mb=$post_max_size_mb[0];}
if(preg_match("/m/",$post_max_size)) {$post_max_size_mb=explode('m',$post_max_size);$post_max_size_mb=$post_max_size_mb[0];}
if(preg_match("/G/",$post_max_size)) {$post_max_size_mb=explode('G',$post_max_size);$post_max_size_mb=$post_max_size_mb[0]*1024;}
if(preg_match("/g/",$post_max_size)) {$post_max_size_mb=explode('g',$post_max_size);$post_max_size_mb=$post_max_size_mb[0]*1024;}
if(!$post_max_size_mb) {$post_max_size_mb=$phpinfo[$vphp]['upload_post_max_size'][0];}

//get web server name
$web_server=$_SERVER['SERVER_SOFTWARE'];
$web_server=explode('/',$web_server);
$web_server_name=strtolower($web_server[0]);
if(isset($web_server[1])) {
	$web_server_version=$web_server[1];
	$web_server_version=explode(' ',$web_server_version);
	$web_server_version=$web_server_version[0];
} else {
	$web_server_version=T_('Non disponible');
}

if($web_server_name!='nginx')
{
	//get apache version
	$apache=$_SERVER['SERVER_SOFTWARE'];
	$apache=preg_split('[ ]', $apache); 
	$apache=preg_split('[/]', $apache[0]);
	if(isset($apache[1])) {
		$apache_version=$apache[1]; 
		$apache_display_version=1;
		$apache_icon=explode(".",$apache[1]);
		if($apache_icon[0]>=2 && $apache_icon[1]>=4){$web_server_icon='apache_ok.png';} else {$web_server_icon='apache_ko.png';}
	} else {
		$apache_version=T_('Version non disponible, serveur sécurisé');
		$apache_display_version=0;
		$web_server_icon='apache_ok.png';
	}
} else {
	$web_server_icon='nginx_ok.png';
}

//get components versions
if($_GET['page']!='admin')
{
	$phpmailer = file_get_contents('../components/PHPMailer/VERSION');
	$phpgettext = file_get_contents('../components/php-gettext/VERSION');
	$phpimap = file_get_contents('../components/PhpImap/VERSION');
	$highcharts = file_get_contents('../components/Highcharts/VERSION');
	$wol = file_get_contents('../components/wol/VERSION');
	$mysqldumpphp = file_get_contents('../components/mysqldump-php/VERSION');
	$fullcalendar = file_get_contents('../components/fullcalendar/VERSION');
	$jquery = file_get_contents('../components/jquery/VERSION');
	$bootstrap = file_get_contents('../components/bootstrap/VERSION');
	$bootstrapwysiwyg = file_get_contents('../components/bootstrap-wysiwyg/VERSION');
	$tempusdominus = file_get_contents('../components/tempus-dominus/VERSION');
	$moment = file_get_contents('../components/moment/VERSION');
	$chosen = file_get_contents('../components/chosen/VERSION');
	$jqueryhotkey = file_get_contents('../components/jquery-hotkeys/VERSION');
	$popperjs = file_get_contents('../components/popper-js/VERSION');
	$fontawesome = file_get_contents('../components/fontawesome/VERSION');
	$bootbox = file_get_contents('../components/bootbox/VERSION');
	$smartwizard = file_get_contents('../components/smartwizard/VERSION');
	$ace = file_get_contents('../template/ace/VERSION');
	
	//get log file to check version db and file version
	$changelog = file_get_contents('../changelog.php');
} else {
	$phpmailer = file_get_contents('./components/PHPMailer/VERSION');
	$phpgettext = file_get_contents('./components/php-gettext/VERSION');
	$phpimap = file_get_contents('./components/PhpImap/VERSION');
	$highcharts = file_get_contents('./components/Highcharts/VERSION');
	$wol = file_get_contents('./components/wol/VERSION');	
	$mysqldumpphp = file_get_contents('./components/mysqldump-php/VERSION');	
	$fullcalendar = file_get_contents('./components/fullcalendar/VERSION');	
	$jquery = file_get_contents('./components/jquery/VERSION');	
	$bootstrap = file_get_contents('./components/bootstrap/VERSION');	
	$bootstrapwysiwyg = file_get_contents('./components/bootstrap-wysiwyg/VERSION');	
	$tempusdominus = file_get_contents('./components/tempus-dominus/VERSION');	
	$moment = file_get_contents('./components/moment/VERSION');	
	$chosen = file_get_contents('./components/chosen/VERSION');	
	$jqueryhotkey = file_get_contents('./components/jquery-hotkeys/VERSION');	
	$popperjs = file_get_contents('./components/popper-js/VERSION');	
	$fontawesome = file_get_contents('./components/fontawesome/VERSION');	
	$bootbox = file_get_contents('./components/bootbox/VERSION');	
	$smartwizard = file_get_contents('./components/smartwizard/VERSION');	
	$ace = file_get_contents('./template/ace/VERSION');	
	
	//get log file to check version db and file version
	$changelog = file_get_contents('changelog.php');
}

//get php session max lifetime parameter
$maxlifetime = ini_get("session.gc_maxlifetime");

//get db size
function formatfilesize($data) {
    if($data < 1024) {return $data . " bytes";}
    else if($data < 1024000) {return round(($data / 1024 ), 1) . "k";}
    else {return round(($data / 1024000), 1) . "MB";}
}
$db_size=0;
$qry=$db->prepare("SHOW TABLE STATUS");
$qry->execute();
while($row=$qry->fetch()){$db_size += $row["Data_length"] + $row["Index_length"];}
$qry->closeCursor();
$db_size=formatfilesize($db_size);

/*
//check if latest stable version is installed
if($_GET['subpage']=='system')
{
	if(extension_loaded('ftp'))
	{
		//check if server is connected to Internet
		$connected = fsockopen("ftp.gestsup.fr", 21); 
		if($connected){
			//ftp check
			$conn_id = ftp_connect('ftp.gestsup.fr',21,5) or die('ERROR : enable to connect on GestSup FTP Server');
			$login_result = ftp_login($conn_id, 'gestsup', 'gestsup');
			$pasv = ftp_pasv($conn_id, true);
			$ftp_list=ftp_nlist($conn_id, "./versions/current/stable/");
			$patch_ftp_list = preg_grep("/patch_/", $ftp_list);
			$patch_ftp_array = array();
			foreach($patch_ftp_list as $patch){
				$patch=explode("_",$patch);
				$patch=explode(".zip",$patch[1]);
				array_push($patch_ftp_array, $patch[0]);
			}
			natsort($patch_ftp_array);
			$last_ftp_patch=end($patch_ftp_array);
			if($last_ftp_patch>$rparameters['version'])
			{$gestsup_version='<i style="width:20px;" title="'.T_("Votre version de l'application est obsolète, installer la dernière version stable").' '.$last_ftp_patch.'" class="fa fa-ticket-alt text-warning"></i>';} 
			fclose($connected);
		}else{
			$gestsup_version='<i style="width:20px;" title="'.T_("Impossible de vérifier la dernière version de GestSup, accès au FTP impossible").'" class="fa fa-ticket-alt text-warning"></i>';
		}
		
	}
}
*/

if(!isset($gestsup_version))
{
	if(strpos($changelog, $rparameters['version']))
	{
		$gestsup_version='<i style="width:20px;" class="fa fa-ticket-alt text-success"></i>';
		$gestsup_version_text='';
	} else {
		$gestsup_version='<i style="width:20px;" title="'.T_("Une incohérence de version de l'application à été détectée, entre votre base de données et vos fichiers").'" class="fa fa-exclamation-triangle text-danger"></i>';
		$gestsup_version_text=' <i>('.T_("Une incohérence de version de l'application à été détectée, entre votre base de données et vos fichiers, vérifier votre méthode d'installation des mises à jours").')</i>';
	}
}

function folderSize ($dir)
{
    $size = 0;
    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : folderSize($each);
    }
    return $size;
}
$upload_size=round(((folderSize('upload')/1024)/1024),2).'MB';

?>
<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
    <div class="card-body p-0 table-responsive-xl">
		<table class="table text-dark-m1 brc-black-tp10 mb-0">
			<tbody>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-server text-blue-m3 pr-1"></i><?php echo T_('Serveur'); ?></td>
					<td class="text-95 text-default-d3">
						<img src="./images/<?php echo strtolower($os); ?>_ok.png" style="border-style: none" alt="img" /> <?php echo "<b>OS :</b> {$phpinfo['phpinfo']['System']}<br />"; ?>
						<?php 
							echo '<img src="./images/'.$web_server_icon.'" style="border-style: none" alt="img" /> <b>'.ucfirst($web_server_name).' :</b> '.$web_server_version.' <i>('.$_SERVER['SERVER_PROTOCOL'].')</i><br />';
						?>
						<img style="width:20px;" src="./images/<?php echo strtolower($rdb_name).'_'.$rdb_icon.'.png'; ?>" style="border-style: none" alt="img" /> <?php echo '<b>'.$rdb_name.' :</b> '.$rdb_version.' <i>('.T_('base').' : '.$db_name.' '.$db_size.')</i><br />'; ?>
						
						<?php 
						//check php version
						$php_version=phpversion();
						if($php_version<'7.3.0'){
							echo '<i class="fa fa-times-circle text-danger"></i> <b>PHP :</b>  '.T_('Votre version de PHP ').phpversion().T_(' est obsolète, installer au minimum la version 7.3.0');
						}else{
							echo '<img class="pr-1" src="./images/php_ok.png" style="border-style: none" alt="img" /> <b>PHP :</b> '.phpversion();
						}
						?>  
						<br />
						<?php echo $gestsup_version; ?> <b><?php echo T_('GestSup'); ?> :</b> <?php echo $rparameters['version'].$gestsup_version_text; ?><br />
						<i style="width:16px;" class="fa fa-clock text-success"></i> &nbsp;<b><?php echo T_('Horloge'); ?> :</b> <?php echo date('Y-m-d H:i:s'); ?><br />
						<i style="width:16px;" class="fa fa-hdd text-success"></i> &nbsp;<b><?php echo T_('Fichiers chargés'); ?> :</b> <?php echo $upload_size; ?><br />
						<i style="width:19px;" class="fa fa-key text-success"></i> 
							<b><?php echo T_('Clé privée'); ?> :</b> 
							<span onclick="DisplayKey()" class="badge badge badge-primary"><?php echo T_('Afficher');?></span>
							<span id="private_key" style="display:none"><?php echo $rparameters['server_private_key']; ?> <i><?php echo T_("(Clé à ne pas divulguer)"); ?></i></span>
							<script>function DisplayKey() {document.getElementById("private_key").style.display= '';}</script>
					</td>
				</tr>
				<?php
				//check configuration
				$conf_error='';
					//check write
					if(!is_writable('./upload/ticket') && is_dir('./upload/ticket')){$conf_error.='<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Droit d'écriture").' : </b> '.T_('Répertoire').' "upload/ticket" '.T_('verrouillé').' <i>('.T_("Ajouter les droits d'écriture sur ce repertoire, afin de pouvoir charger des pièces jointes à un ticket").').</i><br />';}
					if(!is_writable('./upload/logo') && is_dir('./upload/ticket')){$conf_error.='<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Droit d'écriture").' : </b> '.T_('Répertoire').' "upload/logo" '.T_('verrouillé').' <i>('.T_("Ajouter les droits d'écriture sur ce repertoire, afin de pouvoir charger un logo").').</i><br />';}
					if(!is_writable('./upload/procedure') && is_dir('./upload/ticket')){$conf_error.='<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Droit d'écriture").' : </b> '.T_('Répertoire').' "upload/procedure" '.T_('verrouillé').' <i>('.T_("Ajouter les droits d'écriture sur ce repertoire, afin de pouvoir charger des pièces jointes à une procédure").').</i><br />';}
					if(!is_writable('./upload/asset') && is_dir('./upload/ticket')){$conf_error.='<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Droit d'écriture").' : </b> '.T_('Répertoire').' "upload/asset" '.T_('verrouillé').' <i>('.T_("Ajouter les droits d'écriture sur ce repertoire, afin de pouvoir charger une image de modèle d'équipement").').</i><br />';}
					//check SMTP configuration
					if($rparameters['mail'] && $rparameters['mail_smtp'] && $rparameters['mail_username'] && $rparameters['mail_username']!=$rparameters['mail_from_adr']){$conf_error.='<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_("Connecteur SMTP").' : </b> '.T_("L'adresse mail de l'émetteur est différente de l'adresse mail de connexion au serveur, vos mails peuvent être considérés en tant que SPAM.").'<br />';}

					if($conf_error)
					{
						echo '
						<tr>
							<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-cog text-blue-m3 pr-1"></i>'.T_('Configuration').'</td>
							<td class="text-95 text-default-d3">
								'.$conf_error.'
							</td>
						</tr>
						';
					}
			
				?>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-desktop text-blue-m3 pr-1"></i><?php echo T_('Client'); ?> </td>
					<td class="text-95 text-default-d3">
						<i class="fa fa-check-circle text-success"></i> <b><?php echo T_('Mobile'); ?> :</b> <?php if($mobile) {echo 'Oui';} else {echo 'Non';} ?><br />
						<i class="fa fa-check-circle text-success"></i> <b><?php echo T_('Navigateur'); ?> :</b> <?php echo $_SERVER['HTTP_USER_AGENT']; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b><?php if(strstr($_SERVER['REMOTE_ADDR'],':')) {echo 'IPv6';} else {echo 'IPv4';}  ?> :</b> <?php echo $_SERVER['REMOTE_ADDR']; ?><br />
					</td>
				</tr>
				<tr>
					<td style="width: 160px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-cog text-blue-m3 pr-1"></i><?php echo T_('Paramètres PHP'); ?> </td>
					<td class="text-95 text-default-d3">
						<?php
						if($phpinfo[$vphp]['file_uploads'][0]=="On") echo '<i class="fa fa-check-circle text-success"></i> <b>file_uploads</b> : '.T_('Activé').'<br />'; else echo '<i class="fa fa-times-circle text-danger"></i> <b>file_uploads :</b> '.T_('Désactivé').' <i>('.T_('Le chargement de fichiers sera impossible').')</i><br />';
						if($ram_mb>=512) echo '<i class="fa fa-check-circle text-success"></i> <b>memory_limit :</b> '.$phpinfo[$vphp]['memory_limit'][0].'<br />'; else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>memory_limit :</b> '.$phpinfo[$vphp]['memory_limit'][0].' <i>('.T_("Il est préconisé d'allouer plus de mémoire pour PHP valeur minimum 512M éditer votre fichier php.ini").')</i>.<br />';
						if($max_filesize_mb>=5) echo '<i class="fa fa-check-circle text-success"></i> <b>upload_max_filesize :</b> '.$max_filesize_mb.'M<br />'; else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>upload_max_filesize : </b>'.$max_filesize_mb.'M <i> ('.T_("Il est préconisé d'avoir une valeur supérieur ou égale à 5Mo, afin d'attacher des pièces jointes volumineuses").')</i>.<br />';
						if($post_max_size_mb>=5) echo '<i class="fa fa-check-circle text-success"></i> <b>post_max_size :</b> '.$post_max_size_mb.'M <br />'; else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>post_max_size : </b>'.$post_max_size_mb.'M <i> ('.T_("Il est préconisé d'avoir une valeur supérieur ou égale à 5Mo, afin d'attacher des pièces jointes volumineuses").')</i>.<br />';
						if($phpinfo[$vphp]['max_execution_time'][0]>="240") echo '<i class="fa fa-check-circle text-success"></i> <b>max_execution_time :</b> '.$phpinfo[$vphp]['max_execution_time'][0].'s<br />'; else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>max_execution_time : </b>'.$phpinfo[$vphp]['max_execution_time'][0].'s <i>('.T_('Valeur conseillé 240s, modifier votre php.ini relancer apache et actualiser cette page').'.)</i><br />';
						if($phpinfo['date']['date.timezone'][0]!='UTC' || $rparameters['server_timezone']) echo '<i class="fa fa-check-circle text-success"></i> <b>date.timezone :</b> '.date_default_timezone_get().'<br />'; else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>date.timezone :</b> '.date_default_timezone_get().' <i>('.T_("Il est préconisé de modifier la valeur date.timezone du fichier php.ini, et mettre Europe/Paris afin de ne pas avoir de problème d'horloge").'.)</i><br />';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 160px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-puzzle-piece text-blue-m3 pr-1"></i><?php echo T_('Extensions PHP'); ?> </td>
					<td class="text-95 text-default-d3">
						<?php
						if(extension_loaded('pdo_mysql')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_pdo_mysql :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_pdo_mysql</b> : '.T_("Désactivée").' <i>('.T_("L'interconnexion de base de données ne pourra être disponible").')</i>';
						echo "<br />";
						if(extension_loaded('openssl')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_openssl :</b> '.T_('Activée'); else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>php_openssl</b> : '.T_("Désactivée").' <i>('.T_("Si vous utilisez un serveur SMTP sécurisé les mails ne seront pas envoyés. apt-get install openssl").')</i>';
						echo "<br />";
						if(extension_loaded('ldap')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_ldap :</b> '.T_('Activée'); else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>php_ldap</b> : '.T_("Désactivée").' <i>('.T_("Aucune synchronisation ni authentification via un serveur LDAP ne sera possible. apt-get install php7.3-ldap").')</i>';
						echo "<br />";
						if(extension_loaded('zip')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_zip :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_zip</b> : '.T_("Désactivée").' <i>('.T_("La fonction de mise à jour automatique ne sera pas possible").')</i>';
						echo "<br />";
						if(extension_loaded('imap')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_imap :</b> '.T_('Activée'); else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>php_imap</b> : '.T_("Désactivée").' <i>('.T_("La fonction Mail2Ticket ne fonctionnera pas. apt-get install php7.3-imap").')</i>';
						echo "<br />";
						if(extension_loaded('ftp')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_ftp :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_ftp</b> : '.T_("Désactivée").' <i>('.T_("Aucune mise à jour du logiciel ne sera possible, dé-commenter la ligne extension=php_ftp votre php.ini'").')</i>';
						echo "<br />";
						if(extension_loaded('xml')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_xml :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_xml</b> : '.T_("Désactivée").' <i>('.T_("Le connecteur LDAP ne fonctionnera pas. apt-get install php7.3-xml").')</i>';
						echo "<br />";
						if(extension_loaded('curl')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_curl :</b> '.T_('Activée'); else echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>php_curl</b> : '.T_("Désactivée").' <i>('.T_("Le contrôle de sécurité sur le listing des répertoire ne fonctionnera pas. apt-get install php7.3-curl").')</i>';
						echo "<br />";
						if(extension_loaded('mbstring')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_mbstring :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_mbstring</b> : '.T_("Désactivée").' <i>('.T_("des erreurs sont possibles dans la liste des tickets et sur le connecteur IMAP. apt install php7.3-mbstring").')</i>';
						echo "<br />";
						if(extension_loaded('gd')) echo '<i class="fa fa-check-circle text-success"></i> <b>php_gd :</b> '.T_('Activée'); else echo '<i class="fa fa-times-circle text-danger"></i> <b>php_gd</b> : '.T_("Désactivée").' <i>('.T_("La confirmation visuelle lors de l'enregistrement d'un utilisateur ne fonctionnera pas. apt install php7.3-gd").')</i>';
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 160px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-shield-alt text-blue-m3 pr-1"></i><?php echo T_('Sécurité'); ?> </td>
					<td class="text-95 text-default-d3">
						<?php
						if($http=="https") 
						{echo '<i class="fa fa-check-circle text-success"></i> <b>HTTPS : </b>'.T_('Activée');}
						else 
						{echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>HTTPS : </b>'.T_("Désactivé").' <i>('.T_("Les connexions vers le serveur ne sont pas chiffrées, si votre serveur est publié sur Internet vous devez obligatoirement").' <a target="_blank" href="https://certbot.eff.org"> '.T_("installer un certificat SSL").'</a>.)</i>';}
						echo "<br />";
						if($web_server_name=='apache') 
						{
							if($apache_display_version==0)
							{echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Version Apache').' : </b>'.T_('Non affichée');}
							else
							{echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Version Apache').' : </b>'.T_("Affichée").' <i>('.T_("Pour plus de sécurité masquer la version d'apache que vous utilisez. Passer \"ServerTokens\" à \"Prod\" dans security.conf").'.)</i>';}	
							echo "<br />";
						}
						
						if($phpinfo[$vphp]['expose_php'][0]=='Off')
						{echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Version PHP').' : </b>'.T_('Non affichée');}
						else
						{echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Version PHP').' : </b>'.T_("Affichée").' <i>('.T_("Pour plus de sécurité masquer la version de PHP que vous utilisez. Passer le paramètre \"expose_php\" à  \"Off\" dans le php.ini").'.)</i>';}	
						echo "<br />";
						
						if($maxlifetime<=1440 && $rparameters['timeout']<=24) 
						{echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Durée de la session').' : </b> PHP='.$maxlifetime.'s GestSup='.$rparameters['timeout'].'m';}
						else
						{echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Durée de la session').' : </b> PHP='.$maxlifetime.'s GestSup='.$rparameters['timeout'].'m <i>('.T_("Pour plus de sécurité diminuer la durée à 24m minimum, paramètre \"session.gc_maxlifetime\" du php.ini et paramètre GestSup.").')</i>';}
						echo "<br />";
						if($_GET['subpage']=='system') //not display on installation page
						{
							if(!is_writable('./index.php'))
							{echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_("Droits d'écriture").' : </b>'.T_('Verrouillés');}
							else
							{echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_("Droits d'écriture").' : </b>'.T_('Non verrouillés').' <i>(<a target="_blank" href="https://gestsup.fr/index.php?page=support&item1=install&item2=debian#43">'.T_('cf documentation').'</a>).</i>';}  
							echo "<br />";
							$test_install_file=file_exists('./install/index.php' );
							if(!$test_install_file) 
							{echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_("Répertoire installation").' : </b>'.T_('Non présent');}
							else 
							{echo '<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Répertoire installation").' : </b>'.T_('Présent').' <i>('.T_("Supprimer le répertoire \"./install\" de votre serveur").').</i>';}
							echo "<br />";
						}
						//if curl extension is installed
						if(extension_loaded('curl'))
						{
							//test directory listing
							$url=$http.'://'.$_SERVER['SERVER_NAME'].'/components/';
							$c = curl_init($url);
							curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
							$html = curl_exec($c);
							if(curl_error($c)) die(curl_error($c));
							$status = curl_getinfo($c, CURLINFO_HTTP_CODE);
							curl_close($c);
							if($status=='200')
							{
								echo '<i class="fa fa-times-circle text-danger"></i> <b>'.T_("Listing des répertoires").' : </b>'.T_("Activé, vérifier l'option 'Indexes' de votre serveur Apache").'.<br />';
							} else {
								echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_("Listing des répertoires").' : </b>'.T_('Désactivé').'<br />';
							}
						}
						if($_GET['subpage']=='system')
						{
							//check secure SMTP
							if($rparameters['mail'])
							{
								if($rparameters['mail_port']=='587' || $rparameters['mail_port']=='465')
								{
									echo '<i class="fa fa-check-circle text-success"></i> <b>SMTP : </b>'.T_('Sécurisé').'<br />';
								} else {
									echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>SMTP : </b>'.T_('Non sécurisé').' <i>('.T_('Régler le port 465 ou 587, dans la configuration du connecteur').').</i><br />';
								}
							}
							//check secure IMAP
							if($rparameters['imap'])
							{
								if($rparameters['imap_port']=='993/imap/ssl')
								{
									echo '<i class="fa fa-check-circle text-success"></i> <b>IMAP : </b>'.T_('Sécurisé').'<br />';
								} else {
									echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>IMAP : </b>'.T_('Non sécurisé').' <i>('.T_('Régler le port 993, dans la configuration du connecteur').').</i><br />';
								}
							}
							//check secure LDAP
							if($rparameters['ldap'])
							{
								if($rparameters['ldap_port']=='636')
								{
									echo '<i class="fa fa-check-circle text-success"></i> <b>LDAP : </b>'.T_('Port sécurisé').'<br />';
								} else {
									echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>LDAP : </b>'.T_('Port non sécurisé').' <i>('.T_('Régler le port 636, dans la configuration du connecteur').').</i><br />';
								}
								//check LDAP user admin 
								if(strtoupper($rparameters['ldap_user'])=='ADMINISTRATEUR')
								{
									echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('LDAP').' : </b>'.T_('Utilisateur administrateur').' <i>('.T_("L'utilisateur administrateur est spécifié sur les paramètres du connecteur LDAP, l'application n'a pas besoins de ces privilèges, renseigner un utilisateur du domaine").').</i><br />';
								}
							}
							
							//check password policy
							if($rparameters['ldap_auth'])
							{
								echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Mots de passes').' : </b>'.T_('Géré par le serveur LDAP').'<br />';
							} elseif($rparameters['user_password_policy'])
							{
								if($rparameters['user_password_policy_min_lenght']>=8)
								{
									echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Mots de passes').' : </b>'.T_('Sécurisés').'<br />';
								} else {
									echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Mots de passes').' : </b>'.T_('Longueur de mot de passe trop faible').' <i>('.T_('Définir la longueur minimale à 8 caractères').').</i><br />';
								}
							} else {
								echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Mots de passes').' : </b>'.T_('Aucune politique définie').' <i>('.T_('Définissez une politique de mot de passe dans Administration > Paramètres > Général > Utilisateur').').</i><br />';
							}
							//check admin password
							$qry=$db->prepare("SELECT `last_pwd_chg` FROM `tusers` WHERE `login`='admin' AND `disable`='0'");
							$qry->execute();
							$admin_pwd=$qry->fetch();
							$qry->closeCursor();
							if($admin_pwd['last_pwd_chg']=='0000-00-00')
							{
								echo '<i class="fa fa-times-circle text-danger"></i> <b>'.T_('Mot de passe administrateur').' : </b>'.T_('Pas encore modifié').' <i>('.T_("Changer le mot de passe du compte ayant l'identifiant admin").').</i><br />';
							} else {
								echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Mot de passe administrateur').' : </b>'.T_('Modifié').'<br />';
							}

							//check enable log 
							if($rparameters['log'])
							{
								echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Logs').' : </b>'.T_('Activés').'<br />';
							} else {
								echo '<i class="fa fa-exclamation-triangle text-warning"></i> <b>'.T_('Logs').' : </b>'.T_('Désactivés').' <i>('.T_('Pour plus de sécurité vous pouvez activer les logs de sécurité dans Administration > Paramètres > Général > Serveur').').</i><br />';
							}
							//check limit IP
							if($rparameters['restrict_ip'])
							{
								echo '<i class="fa fa-check-circle text-success"></i> <b>'.T_('Restriction IP').' : </b>'.T_('Activé').'<br />';
							} else {
								echo '<i class="fa fa-info-circle text-info"></i> <b>'.T_('Restriction IP').' : </b>'.T_('Désactivé').' <i>('.T_("Pour plus de sécurité, il est possible de restreindre l'accès des clients à certaines adresses IP, cf Administration > Paramètres > Général > Serveur").').</i><br />';
							}
						}
						?>
					</td>
				</tr>
				<tr>
					<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-cubes text-blue-m3 pr-1"></i><?php echo T_('Composants'); ?> </td>
					<td class="text-95 text-default-d3">
						<i class="fa fa-check-circle text-success"></i> <b>Ace :</b> <?php echo $ace; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Bootbox :</b> <?php echo $bootbox; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Bootstrap :</b> <?php echo $bootstrap; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Bootstrap wysiwyg :</b> <?php echo $bootstrapwysiwyg; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Chosen :</b> <?php echo $chosen; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Fontawesome :</b> <?php echo $fontawesome; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>FullCalendar :</b> <?php echo $fullcalendar; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Highcharts :</b> <?php echo $highcharts; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>jQuery :</b> <?php echo $jquery; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>jQuery Hotkeys :</b> <?php echo $jqueryhotkey; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Moment :</b> <?php echo $moment; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>PHPmailer :</b> <?php echo $phpmailer; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>PHPimap :</b> <?php echo $phpimap; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>PHPgettext :</b> <?php echo $phpgettext; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>PHPmysqldump :</b> <?php echo $mysqldumpphp; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Popper :</b> <?php echo $popperjs; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Smart Wizard :</b> <?php echo $smartwizard; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>Tempus Dominus :</b> <?php echo $tempusdominus; ?><br />
						<i class="fa fa-check-circle text-success"></i> <b>WOL :</b> <?php echo $wol; ?><br />
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>