<?php
################################################################################
# @Name : ./core/ping.php
# @Description : test if asset is connected
# @Call : ./core/asset.php or via windows or linux command line
# @Parameters : GET_[ip] or globalping and server key for command line execution
# @Author : Flox
# @Create : 19/12/2015
# @Update : 28/05/2020
# @Version : 3.2.2
################################################################################

//initialize variables 
if(!isset($rparameters['server_private_key'])) $rparameters['server_private_key'] = '';
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE']=0;

if(!isset($argv[1])) $argv[1] = '';
if(!isset($argv[2])) $argv[2] = '';

//call via external script for cron 
if(!$rparameters['server_private_key'])
{
	require_once(__DIR__."/../core/init_get.php");
	//database connection
	require_once(__DIR__."/../connect.php");
	
	//load parameters table
	$qry=$db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
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
	
	if($rparameters['server_timezone']) {date_default_timezone_set($rparameters['server_timezone']);}
}

$today=date("Y-m-d");
$datetime=date("Y-m-d H:i:s");

//multi ping for update all assets last ping value
if ($argv[1]=='globalping')
{
	if($argv[2]==$rparameters['server_private_key'] && $rparameters['server_private_key'])
	{
		//load all enabled iface of all enabled assets with ip adresse
		$qry=$db->prepare("SELECT tassets.id AS asset_id,tassets_iface.id AS asset_iface_id, tassets.netbios AS asset_netbios, tassets_iface.ip AS asset_iface_ip  FROM tassets_iface,tassets WHERE tassets.id=tassets_iface.asset_id AND tassets_iface.ip!='' AND tassets.state='2' AND tassets.disable='0' AND tassets_iface.disable='0' ORDER BY tassets.id");
		$qry->execute();
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') //windows server case
		{
			while ($row=$qry->fetch())
			{
				//check if ipv4 is well formed
				$error='';
				$cnt=0;
				if(!preg_match('#\.#', $row['asset_iface_ip'])) {$error='error no point detected';}
				foreach (explode('.',$row['asset_iface_ip']) as $val) {$cnt++;if (!is_numeric($val)) { $error='not numeric value'; break;} if($val>254) { $error='error bloc more than 255'; break;}}
				if(!$error) {if ($cnt!=4) {$error='error not 4 blocs';}}
				
				if(!$error)
				{
					echo '[WIN] ping '.$row['asset_netbios'].' (id:'.$row['asset_id'].') on IP '.$row['asset_iface_ip'].':';
					$result=exec("ping -n 1 -w 1 $row[asset_iface_ip]");
					if((preg_match('#ms#', $result)))
					{
						echo ' OK (updating asset last ping flag)'.PHP_EOL;
						//update asset ping flag
						$qry2=$db->prepare("UPDATE `tassets` SET `date_last_ping`=:date_last_ping WHERE `id`=:id");
						$qry2->execute(array('date_last_ping' => $today, 'id' => $row['asset_id']));
						
						//update iface ping flag
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ok`=:date_ping_ok WHERE `id`=:id");
						$qry2->execute(array('date_ping_ok' => $datetime, 'id' => $row['asset_iface_id']));
					} else {
						echo ' KO '.PHP_EOL;
						//update iface ping flag
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ko`=:date_ping_ko WHERE `id`=:id");
						$qry2->execute(array('date_ping_ko' => $datetime, 'id' => $row['asset_iface_id']));
					}
					sleep(1); //timeout 1 seconds to limit network trafic
				} else {
					echo '[WIN] ping '.$row['asset_netbios'].' (id:'.$row['asset_id'].') on IP '.$row['asset_iface_ip'].': no check, invalid ip address ('.$error.')'.PHP_EOL;
				}
			}
			$qry->closeCursor();
		} else { //linux server case
			while ($row=$qry->fetch())
			{
				//check if ipv4 is well formed
				$error='';
				$cnt=0;
				if(!preg_match('#\.#', $row['asset_iface_ip'])) {$error='error no point detected';}
				foreach (explode('.',$row['asset_iface_ip']) as $val) {$cnt++;if (!is_numeric($val)) { $error='not numeric value'; break;} if($val>254) { $error='error bloc more than 255'; break;}}
				if(!$error) {if ($cnt!=4) {$error='error not 4 blocs';}}
				
				if (!$error)
				{
					echo '[LINUX] ping '.$row['asset_netbios'].' (id:'.$row['asset_id'].') on IP '.$row['asset_iface_ip'].':';
					$result=exec("ping -W 1 -c 1  $row[asset_iface_ip]");
					if((preg_match('#min#', $result)))
					{
						echo ' OK (updating asset last ping flag)'.PHP_EOL;
						//update asset ping flag
						$qry2=$db->prepare("UPDATE `tassets` SET `date_last_ping`=:date_last_ping WHERE `id`=:id");
						$qry2->execute(array('date_last_ping' => $today, 'id' => $row['asset_id']));
						
						//update iface ping flag
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ok`=:date_ping_ok WHERE `id`=:id");
						$qry2->execute(array('date_ping_ok' => $datetime, 'id' => $row['asset_iface_id']));
					} else {
						echo ' KO '.PHP_EOL;
						//update iface ping flag
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ko`=:date_ping_ko WHERE `id`=:id");
						$qry2->execute(array('date_ping_ko' => $datetime, 'id' => $row['asset_iface_id']));
					}
					sleep(1); //timeout 1 seconds to limit network trafic
				} else {
					echo '[LINUX] ping '.$row['asset_netbios'].' (id:'.$row['asset_id'].') on IP '.$row['asset_iface_ip'].': no check, invalid ip address ('.$error.')'.PHP_EOL;;
				}
			}
			$qry->closeCursor();
		}
	} else {echo "ERROR: Wrong server key go to application system page to get your key";}
} elseif($_GET['iptoping']) { //single ping from ticket with OS detection
	//test each iface with ip
	$qry=$db->prepare("SELECT `id`,`ip`,`date_ping_ok`,`date_ping_ko` FROM `tassets_iface` WHERE asset_id=:asset_id AND disable='0'");
	$qry->execute(array('asset_id' => $globalrow['id']));
	while($row=$qry->fetch()) 
	{
		$msg_error='';
		$msg_success='';
		if($row['ip'])
		{
			$test_ip=$row['ip'];
			//check if ipv4 is well formed
			$error='';
			$cnt=0;
			if(!preg_match('#\.#', $test_ip)) {$error='error no point detected';}
			foreach (explode('.',$test_ip) as $val) {$cnt++;if (!is_numeric($val)) { $error='not numeric value'; break;} if($val>254) { $error='error bloc more than 255'; break;}}
			if(!$error) {if ($cnt!=4) {$error='error not 4 blocs';}}
			
			if(!$error)
			{
				if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
					$result=exec("ping -n 1 -w 1 $test_ip");	
					//test result
					if((preg_match('#Minimum#', $result)))
					{
						$msg_success=T_('PING').' '.$test_ip.' : OK <span class="small">('.$result.')</span>';
						//update asset flag
						$qry2=$db->prepare("UPDATE `tassets` SET `date_last_ping`=:date_last_ping WHERE `id`=:id");
						$qry2->execute(array('date_last_ping' => $today,'id' => $globalrow['id']));
						
						$current_datetime=date('Y-m-d H:i:s');
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ok`=:date_ping_ok WHERE `id`=:id");
						$qry2->execute(array('date_ping_ok' => $current_datetime,'id' => $row['id']));
						
					} else {
						$result=mb_convert_encoding($result, "UTF-8");
						$msg_error=T_('PING').' '.$test_ip.' : KO <span class="small">('.$result.')</span>';
						
						$current_datetime=date('Y-m-d H:i:s');
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ko`=:date_ping_ko WHERE `id`=:id");
						$qry2->execute(array('date_ping_ko' => $current_datetime,'id' => $row['id']));
				}
				} else {
					$result=exec("ping -W 1 -c 1 $test_ip");
					//test result
					if((preg_match('#min#', $result)))
					{
						//display message
						$msg_success=T_('PING').' '.$test_ip.' : OK <span class="small">('.$result.')</span>';
						//update asset flag
						$qry2=$db->prepare("UPDATE `tassets` SET `date_last_ping`=:date_last_ping WHERE `id`=:id");
						$qry2->execute(array('date_last_ping' => $today,'id' => $globalrow['id']));
						
						$current_datetime=date('Y-m-d H:i:s');
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ok`=:date_ping_ok WHERE `id`=:id");
						$qry2->execute(array('date_ping_ok' => $current_datetime,'id' => $row['id']));
					} else {
						$result=mb_convert_encoding($result, "UTF-8");
						$msg_error=T_('PING').' '.$test_ip.' : KO <span class="small">('.$result.')</span>';
						$current_datetime=date('Y-m-d H:i:s');
						$qry2=$db->prepare("UPDATE `tassets_iface` SET `date_ping_ko`=:date_ping_ko WHERE `id`=:id");
						$qry2->execute(array('date_ping_ko' => $current_datetime,'id' => $row['id']));
					}
				}
			} else {
				$msg_error=T_('PING').' '.$test_ip.'</b> : KO <span class="small">(Invalid IPv4 address: '.$error.')</span>';
			}
			if($msg_error){echo DisplayMessage('error',$msg_error);}
			if($msg_success){echo DisplayMessage('success',$msg_success);}
		}
	}
	$qry->closeCursor();
}
?>