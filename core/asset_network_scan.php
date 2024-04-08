<?php
################################################################################################
# @Name : ./core/asset_network_scan.php
# @Requirement: nbtscan, dnsutils for Linux (apt-get install nbtscan dnsutils)
# @Description : check all network in GS table tassets_network and create or update asset
# @Call : command line Windows or Linux only
# @Parameters : Server key
# @Author : Flox
# @Create : 10/05/2017
# @Update : 26/05/2020
# @Version : 3.2.4 p2
################################################################################################

//initialize variables 
require_once(__DIR__."/core/../init_get.php");
require_once(__DIR__."/core/../functions.php");
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE']=0;
if(!isset($argv[1])) $argv[1] = '';
$nbtscan_check=1;

//database connection
require_once(__DIR__."/../connect.php");

//switch SQL mode to allow empty values in queries
$db->exec('SET sql_mode = ""');

//load parameters table
$qry = $db->prepare("SELECT * FROM `tparameters`");
$qry->execute();
$rparameters=$qry->fetch();
$qry->closeCursor();

//locales
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if($lang=='fr') {$_GET['lang'] = 'fr_FR';}
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

//define PHP time zone for log entries
date_default_timezone_set('Europe/Paris');

if($argv[1]==$rparameters['server_private_key'])
{
	//display head information
	echo '--------------------------------------------------------------------'.PHP_EOL;
	echo '------------------------ASSET NETWORK SCANNER-----------------------'.PHP_EOL;
	echo '--------------------------------------------------------------------'.PHP_EOL;
	echo PHP_EOL;
	//server key validation
	echo 'Server key validation: OK'.PHP_EOL;
	logit('asset_network_scan', 'Start network scanner','0');
	//OS verification
	if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
	{ 
		echo 'OS Windows: OK'.PHP_EOL;
	} else {
		echo 'OS Linux: OK'.PHP_EOL;
		//check if nbtscan is installed
		$nbtscan=exec('nbtscan 2> /dev/null | grep "Usage:"');
		if($nbtscan!='Usage:')
		{
			echo 'NBTscan check: KO (nbtscan is not installed on your server apt-get install nbtscan)'.PHP_EOL;
			logit('asset_network_scan','ERROR : NBTscan check KO (nbtscan is not installed on your server apt-get install nbtscan)','0');
			$nbtscan_check=0;
		} else {
			echo 'NBTscan check: OK'.PHP_EOL;
		}
	}
	
	if($nbtscan_check==1) //case nbtscan not installed for linux environnement need to get netbios name without DNS
	{
		//for each network
		$qry = $db->prepare("SELECT * FROM `tassets_network` WHERE `scan`='1' AND `disable`='0'"); //for each Gestsup network 
		$qry->execute();
		while ($row=$qry->fetch())	
		{
			//check network
			$error='';
			$cnt=0;
			//check if network have points
			if(!preg_match('#\.#', $row['network'])) {$error='error no point detected';}
			//check if network have number and inferior than 255
			foreach (explode('.',$row['network']) as $val) {$cnt++;if(!is_numeric($val)) { $error='not numeric value'; break;} if($val>254) { $error='error bloc more than 255'; break;}}
			//check if network have 3 points
			if(substr_count($row['network'], '.')!=3) {$error='not 3 points';};
			//check if last char is 0
			if(substr($row['network'], -1)!=0) {$error='end of network must be 0';};

			if(!$error) 
			{
				echo '[SCAN '.$row['name'].'] Network check : OK'.PHP_EOL;
				logit('asset_network_scan','[SCAN '.$row['name'].'] Network check : OK','0');
				//check netmask
				if($row['netmask']=='255.255.255.0')
				{
					echo '[SCAN '.$row['name'].'] Netmask check: OK'.PHP_EOL;
					logit('asset_network_scan','[SCAN '.$row['name'].'] Netmask check : OK','0');
					//for each address on network ping
					for ($i=1;$i<255;$i++)
					{
						//generate ip to check
						$net=explode('.',$row['network']);
						$ip="$net[0].$net[1].$net[2].$i";
						
						//check if scan is disable on current ip 
						$qry2 = $db->prepare("SELECT `net_scan` FROM `tassets` WHERE `id`=(SELECT `asset_id` FROM `tassets_iface` WHERE ip=:ip AND disable='0')"); //for each Gestsup network 
						$qry2->execute(array('ip' => $ip));
						$row2=$qry2->fetch();
						$qry2->closecursor();
						if(empty($row2['net_scan']) && !empty($row2['net_scan'])) {
							echo '[SCAN '.$row['name'].'] ['.$ip.'] No verification for this IP, scan disable on asset'.PHP_EOL;
							logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] No verification for this IP, scan disable on asset','0');
						} else {
							//check ping
							if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') //////////////////////////////////////////////windows case
							{ 
								$result=exec("ping -n 2 -w 1 $ip");	
								if((preg_match('#Minimum#', $result))) //asset ping ok
								{
									//check if an existing asset in GS DB exist else to update last ping flag else create it
									$qry2 = $db->prepare("SELECT id,asset_id,netbios,ip,mac FROM tassets_iface WHERE ip=:ip AND disable='0'"); //for each Gestsup network 
									$qry2->execute(array('ip' => $ip));
									$row2 = $qry2->fetch();
									$qry2->closecursor();
									if($row2)
									{
										echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Update last ping flag for asset_id='.$row2['asset_id'].')'.PHP_EOL;
										logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Update last ping flag for asset_id='.$row2['asset_id'].')','0');
										//update last ping flag on asset
										$date=date('Y-m-d');
										$qry3 = $db->prepare("UPDATE tassets SET date_last_ping=:date_last_ping,discover_net_scan='1' WHERE id=:id");
										$qry3->execute(array('date_last_ping' => $date,'id' => $row2['asset_id']));
										
										//update last ping ok on asset iface
										$qry3 = $db->prepare("UPDATE tassets_iface SET date_ping_ok=:date_ping_ok WHERE id=:id");
										$qry3->execute(array('date_ping_ok' => date('Y-m-d H-i-s'), 'id' => $row2['id']));
										
										//update mac address if empty in GS
										if($row2['mac']=='')
										{
											//force to add new arp entry
											$ping=exec("ping $ip");	
											//get mac address of current IP
											$arp_cmd=exec("arp -a $ip");	
											$arp=preg_split('/[\s,]+/', $arp_cmd);
											if($arp[1]==$ip) {$mac=str_replace('-','',$arp[2]);} else {$mac='';}
											if($mac)
											{
												echo '[SCAN '.$row['name'].'] ['.$ip.'] Updating MAC (GestSup MAC is empty and find '.$mac.')'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Updating MAC (GestSup MAC is empty and find '.$mac.')','0');
												$qry3 = $db->prepare("UPDATE tassets_iface SET mac=:mac WHERE id=:id"); 
												$qry3->execute(array('mac' => $mac,'id' => $row2['id']));
											} else {
												echo '[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get mac (ping='.$ip.' arp_cmd='.$arp_cmd.')'.PHP_EOL;
												logit('asset_network_scan',"[SCAN $row[name]] [$ip] FAILED to get mac (arp_cmd=$arp_cmd)",'0');
											}
										}
										//update netbios of iface if empty in GS
										if($row2['netbios']=='')
										{
											$netbios=gethostbyaddr($ip);
											if($netbios && $netbios!=$ip)
											{
												echo '[SCAN '.$row['name'].'] ['.$ip.'] Updating NETBIOS (GestSup NETBIOS is empty for iface and find '.$netbios.')'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Updating NETBIOS (GestSup NETBIOS is empty for iface and find '.$netbios.')','0');
												//update iface
												$qry3 = $db->prepare("UPDATE tassets_iface SET netbios=:netbios WHERE id=:id"); 
												$qry3->execute(array('netbios' => $netbios,'id' => $row2['id']));
												//update asset name if empty
												$qry3 = $db->prepare("UPDATE tassets SET netbios=:netbios WHERE id=:id AND netbios=:netbios2"); 
												$qry3->execute(array('netbios' => $netbios,'id' => $row2['asset_id'],'netbios2' => ''));
												
											} else {
												echo '[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get netbios (netbios='.$netbios.' )'.PHP_EOL;
												logit('asset_network_scan',"[SCAN $row[name]] [$ip] FAILED to get netbios (netbios=$netbios)",'0');
											}
										}
									} else {
										//get mac
										$arp_cmd=exec("arp -a $ip");	
										$arp=preg_split('/[\s,]+/', $arp_cmd);
										if($arp[1]==$ip) {$mac=str_replace('-','',$arp[2]);} else {$mac='';}
										//get netbios
										$netbios=gethostbyaddr($ip);
										if($netbios==$ip) {$netbios='';}
										//generate asset sn_internal
										$qry3 = $db->prepare("SELECT MAX(CONVERT(sn_internal, SIGNED INTEGER)) FROM tassets"); 
										$qry3->execute();
										$sn_internal=$qry3->fetch();
										$qry3->closeCursor(); 
										$sn_internal=$sn_internal[0]+1;
										//current date
										$date=date('Y-m-d');
										//create asset in GS DB
										$qry3 = $db->prepare("INSERT INTO tassets (sn_internal,netbios,state,date_last_ping,date_install,discover_net_scan,disable) VALUES (:sn_internal,:netbios,'2',:date_last_ping,:date_install,'1','0')"); 
										$qry3->execute(array('sn_internal' => $sn_internal,'netbios' => $netbios,'date_last_ping' => $date,'date_install' => $date));
										//create iface for this asset
										$asset_id=$db->lastInsertId();
										$qry3 = $db->prepare("INSERT INTO tassets_iface (role_id,asset_id,netbios,mac,ip,date_ping_ok,disable) VALUES ('1',:asset_id,:netbios,:mac,:ip,:date_ping_ok,'0')"); 
										$qry3->execute(array('asset_id' => $asset_id,'netbios' => $netbios,'mac' => $mac,'ip' => $ip,'date_ping_ok' => date('Y-m-d H-i-s')));
										echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Asset not present in GestSup create new asset with id '.$asset_id.'and sn_internal '.$sn_internal.' and netbios '.$netbios.')'.PHP_EOL;
										logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Asset not present in GestSup create new asset with id '.$asset_id.'and sn_internal '.$sn_internal.' and netbios '.$netbios.')','0');
									}
								} else {
									echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping: KO'.PHP_EOL;
								}
							} else { //////////////////////////////////////////////////////linux case
								$result=exec("ping -W 1 -c 1 $ip");
								if((preg_match('#min#', $result)))
								{
									//check if an existing asset in GS DB exist else to update last ping flag else create it
									$qry2 = $db->prepare("SELECT id,asset_id,netbios,ip,mac FROM tassets_iface WHERE ip=:ip AND disable='0'");
									$qry2->execute(array('ip' => $ip));
									$row2 = $qry2->fetch();
									$qry2->closecursor();
									if($row2)
									{
										echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Update last ping flag for asset_id='.$row2['asset_id'].')'.PHP_EOL;
										logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Update last ping flag for asset_id='.$row2['asset_id'].')','0');
										//update last ping flag on asset
										$date=date('Y-m-d');
										$qry2=$db->prepare("UPDATE tassets SET date_last_ping=:date_last_ping, discover_net_scan='1' WHERE id=:id");
										$qry2->execute(array('date_last_ping' => $date,'id' => $row2['asset_id']));
										//update last ping ok on asset iface
										$qry3 = $db->prepare("UPDATE tassets_iface SET date_ping_ok=:date_ping_ok WHERE id=:id");
										$qry3->execute(array('date_ping_ok' => date('Y-m-d H-i-s'), 'id' => $row2['id']));
										//update mac address if empty in GS
										if($row2['mac']=='')
										{
											//get mac address of current IP
											$arp_cmd=exec("arp -an $ip");	
											$arp=preg_split('/[\s,]+/', $arp_cmd);
											if($arp[3]) {
												if(preg_match('#:#',$arp[3])) {$mac=str_replace(':','',$arp[3]);} else {$mac='';}
											} else {$mac='';}
											if($mac)
											{
												echo '[SCAN '.$row['name'].'] ['.$ip.'] Updating MAC (GestSup MAC is empty and find '.$mac.')'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Updating MAC (GestSup MAC is empty and find '.$mac.')','0');
												$qry2=$db->prepare("UPDATE tassets_iface SET mac=:mac WHERE id=:id");
												$qry2->execute(array('mac' => $mac,'id' => $row2['id']));
											} else {
												echo '[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get mac (arp_cmd='.$arp_cmd.')'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get mac (arp_cmd='.$arp_cmd.')','0');
											}
										}
										//update netbios of iface if empty in GS
										if($row2['netbios']=='')
										{
											//try to get netbios by PHP function
											$netbios=gethostbyaddr($ip);
											if($netbios!=$ip) {$netbios=explode('.',$netbios); $netbios=$netbios[0];} else {$netbios='';}
											//try to get netbios by DNS
											if(!$netbios)
											{
												$netbios=exec("nslookup $ip | grep 'name' | cut -d '=' -f 2");
												$netbios=explode('.',str_replace(' ','',$netbios));
												$netbios=$netbios[0];
											}
											//try to get netbios by nbtscan
											if(!$netbios){$netbios=exec("nbtscan $ip -s- | cut -d'-' -f2");} //need apt-get install nbtscan
											
											if($netbios && $netbios!=$ip)
											{
												echo '[SCAN '.$row['name'].'] ['.$ip.'] Updating NETBIOS (GestSup NETBIOS is empty for iface and find '.$netbios.')'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Updating NETBIOS (GestSup NETBIOS is empty for iface and find '.$netbios.')','0');
												//update iface
												$qry2=$db->prepare("UPDATE tassets_iface SET netbios=:netbios WHERE id=:id");
												$qry2->execute(array('netbios' => $netbios,'id' => $row2['id']));
												//update asset name if empty
												$qry2=$db->prepare("UPDATE tassets SET netbios=:netbios WHERE id=:id AND netbios=:netbios2");
												$qry2->execute(array('netbios' => $netbios,'id' => $row2['asset_id'],'netbios2' => ''));
											} else {
												echo '[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get netbios (netbios='.$netbios.' )'.PHP_EOL;
												logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] FAILED to get netbios (netbios='.$netbios.' )','0');
											}
										}
									} else { //create new asset
										//get mac address of current IP
										$arp_cmd=exec("arp -an $ip");	
										$arp=preg_split('/[\s,]+/', $arp_cmd);
										if($arp[3]) {
											if(preg_match('#:#',$arp[3])) {$mac=str_replace(':','',$arp[3]);} else {$mac='';}
										} else {$mac='';}
										
										//try to get netbios by PHP function
										$netbios=gethostbyaddr($ip);
										if($netbios!=$ip) {$netbios=explode('.',$netbios); $netbios=$netbios[0];} else {$netbios='';}
										//try to get netbios by DNS
										if(!$netbios)
										{
											$netbios=exec("nslookup $ip | grep 'name' | cut -d '=' -f 2");
											$netbios=explode('.',str_replace(' ','',$netbios));
											$netbios=$netbios[0];
										}
										//try to get netbios by nbtscan
										if(!$netbios){$netbios=exec("nbtscan $ip -s- | cut -d'-' -f2");} //need apt-get install nbtscan
										
										if($netbios==$ip) {$netbios='';}
										//generate asset sn_internal
										$qry2=$db->prepare("SELECT MAX(CONVERT(sn_internal, SIGNED INTEGER)) FROM tassets");
										$qry2->execute();
										$sn_internal=$qry2->fetch();
										$qry2->closeCursor(); 
										$sn_internal=$sn_internal[0]+1;
										//current date
										$date=date('Y-m-d');
										//create asset in GS DB
										$qry3 = $db->prepare("INSERT INTO tassets (sn_internal,netbios,state,date_last_ping,date_install,discover_net_scan,disable) VALUES (:sn_internal,:netbios,'2',:date_last_ping,:date_install,'1','0')"); 
										$qry3->execute(array('sn_internal' => $sn_internal,'netbios' => $netbios,'date_last_ping' => $date,'date_install' => $date));
										
										//create iface for this asset
										$asset_id=$db->lastInsertId();
										$qry3 = $db->prepare("INSERT INTO tassets_iface (role_id,asset_id,netbios,mac,ip,date_ping_ok,disable) VALUES ('1',:asset_id,:netbios,:mac,:ip,:date_ping_ok,'0')"); 
										$qry3->execute(array('asset_id' => $asset_id,'netbios' => $netbios,'mac' => $mac,'ip' => $ip,'date_ping_ok' => date('Y-m-d H-i-s')));
										echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Asset not present in GestSup create new asset asset_id='.$asset_id.', sn_internal='.$sn_internal.', netbios='.$netbios.')'.PHP_EOL;
										logit('asset_network_scan','[SCAN '.$row['name'].'] ['.$ip.'] Check ping : OK (Asset not present in GestSup create new asset asset_id='.$asset_id.', sn_internal='.$sn_internal.', netbios='.$netbios.')','0');
									}
								} else {
									echo '[SCAN '.$row['name'].'] ['.$ip.'] Check ping: KO'.PHP_EOL;
								}
							}
							//temporize 
							sleep(1);
						}
					}
				} else {
					echo '[SCAN '.$row['name'].'] Netmask check: KO (must be 255.255.255.0)'.PHP_EOL;
					logit('asset_network_scan',"[SCAN $row[name]] Netmask check: KO (must be 255.255.255.0)",'0');
				}
			} else {
				echo '[SCAN '.$row['name'].'] Network check: KO ('.$error.')'.PHP_EOL;
				logit('asset_network_scan','[SCAN '.$row['name'].'] Network check: KO ('.$error.')','0');
			}
		}
		$qry->closecursor();
	}
} 
else
{
	echo 'Server key validation: KO Wrong server key please check in Administration > System your private server key ('.$argv['1'].')'.PHP_EOL;
	logit('asset_network_scan','ERROR : Wrong server key parameter','0');
}
?>