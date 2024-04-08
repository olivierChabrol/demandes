<?php
#############################################################################################
# @Name : ./core/import_asset.php
# @Description : import asset from a csv file
# @Call : /admin/parameters.php
# @Parameters : filename $_FILE[asset_import][name] or server key for command line execution
# @Author : Flox
# @Create : 20/01/2017
# @Update : 26/05/2020
# @Version : 3.2.2
#############################################################################################

//include functions
require_once(__DIR__."/core/../functions.php");

//initialize variables 
if(!isset($_GET['server_key'])) $_GET['server_key'] = '';
 $_GET['server_key']=htmlspecialchars($_GET['server_key'], ENT_QUOTES, 'UTF-8');
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE']=0;
if(!isset($argv[0])) $argv[0] = '';
if(!isset($argv[1])) $argv[1] = '';
if(!isset($rparameters['debug'])) $rparameters['debug'] = '';
if(!isset($file_rename)) $file_rename='';
if(!isset($csv_mac_disp)) $csv_mac_disp='';
if(!isset($error)) $error='';

if($rparameters['debug']) {echo "<u><b>DEBUG:</b></u><br />";}

//check if file is launch from command line 
if(!$file_rename)
{
	//database connection
	require_once(__DIR__."/../core/init_get.php");
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
	
	//display head information for log and command line
	echo '--------------------------------------------------------------------'.PHP_EOL;
	echo '--------------------------ASSET AUTO IMPORT-------------------------'.PHP_EOL;
	echo '--------------------------------------------------------------------'.PHP_EOL;
	echo PHP_EOL;
} else {
	$import_dir='./upload/asset';
}

if(($argv[1]==$rparameters['server_private_key']) || $file_rename)
{
	
	if($argv[1])
	{
		//server key verification
		echo 'Server key validation: OK'.PHP_EOL;
		logit('asset_import','Starting asset import','0');
		
		//OS verification
		if(strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
		{ 
			echo 'OS Windows: OK'.PHP_EOL;
		} else {
			echo 'OS Linux: OK'.PHP_EOL;
		}
		//directory verification, if scandir not exist create it
		if(!is_dir($import_dir)){mkdir($import_dir, 0777);} 
	}
	
	//get file to import
	if($file_rename){$filename = $file_rename;}
	elseif($argv[1]){$filename = array_diff(scandir($import_dir), array('..', '.')); if($filename) {$filename=$filename[2];}}
	
	//check if is filename var exist 
	if($filename)
	{
		//check if file exist
		if(file_exists("$import_dir/$filename") || $argv[1]) 
		{
			//check csv file
			$extension=explode('.',$filename);
			$extension=strtolower(end($extension));
			if($extension=='csv')
			{
				//read file
				$file = fopen($import_dir.'/'.$filename, 'r');
				$i=0;
				while (($line = fgetcsv($file)) !== FALSE) 
				{
					//init var
					$new_asset=0;
					
					//detect title first line to exclude
					$line[0]=utf8_encode($line[0]);
					$read_line=explode(";",$line[0]);
					
					if($read_line[0]!="Numéro de l'équipement") //don't show first line if title line is detected
					{
						//increment counter
						$i++;
						//explode line date to keep array values
						$read_line=explode(";",$line[0]);
						
						//init var
						if(!isset($read_line[0])) {$read_line[0]='';}
						if(!isset($read_line[1])) {$read_line[1]='';}
						if(!isset($read_line[2])) {$read_line[2]='';}
						if(!isset($read_line[3])) {$read_line[3]='';}
						if(!isset($read_line[4])) {$read_line[4]='';}
						if(!isset($read_line[5])) {$read_line[5]='';}
						if(!isset($read_line[6])) {$read_line[6]='';}
						if(!isset($read_line[7])) {$read_line[7]='';}
						if(!isset($read_line[8])) {$read_line[8]='';}
						if(!isset($read_line[9])) {$read_line[9]='';}
						if(!isset($read_line[10])) {$read_line[10]='';}
						if(!isset($read_line[11])) {$read_line[11]='';}
						if(!isset($read_line[12])) {$read_line[12]='';}
						if(!isset($read_line[13])) {$read_line[13]='';}
						if(!isset($read_line[14])) {$read_line[14]='';}
						if(!isset($read_line[15])) {$read_line[15]='';}
						if(!isset($read_line[16])) {$read_line[16]='';}
						if(!isset($read_line[17])) {$read_line[17]='';}
						if(!isset($read_line[18])) {$read_line[18]='';}
						if(!isset($read_line[19])) {$read_line[19]='';}
						if(!isset($read_line[20])) {$read_line[20]='';}
						if(!isset($read_line[21])) {$read_line[21]='';}
						if(!isset($read_line[22])) {$read_line[22]='';}
						
						//put data to var and secure data
						$sn_internal=strip_tags($read_line[0]);
						$netbios=strip_tags($read_line[1]);
						$sn_manufacturer=strip_tags($read_line[2]);
						$sn_indent=strip_tags($read_line[3]);
						$ip=strip_tags($read_line[4]);
						$mac=strip_tags($read_line[5]);
						$description=strip_tags($read_line[6]);
						$type=strip_tags($read_line[7]);
						$manufacturer=strip_tags($read_line[8]);
						$model=strip_tags($read_line[9]);
						$user=strip_tags($read_line[10]);
						$state=strip_tags($read_line[11]);
						$department=strip_tags($read_line[12]);
						$location=strip_tags($read_line[13]);
						$date_install=$read_line[14];
						$date_end_warranty=$read_line[15];
						$date_stock=$read_line[16];
						$date_standbye=$read_line[17];
						$date_recycle=$read_line[18];
						$date_last_ping=$read_line[19];
						$socket=strip_tags($read_line[20]);
						$technician=strip_tags($read_line[21]);
						$maintenance=strip_tags($read_line[22]);
						
						//convert date to SQL format
						$date_install = DateTime::createFromFormat('d/m/Y', $date_install);
						if($date_install){$date_install=$date_install->format('Y-m-d');}
						$date_end_warranty = DateTime::createFromFormat('d/m/Y', $date_end_warranty);
						if($date_end_warranty){$date_end_warranty=$date_end_warranty->format('Y-m-d');}
						$date_stock = DateTime::createFromFormat('d/m/Y', $date_stock);
						if($date_stock){$date_stock=$date_stock->format('Y-m-d');}
						$date_standbye = DateTime::createFromFormat('d/m/Y', $date_standbye);
						if($date_standbye){$date_standbye=$date_standbye->format('Y-m-d');}
						$date_recycle = DateTime::createFromFormat('d/m/Y', $date_recycle);
						if($date_recycle){$date_recycle=$date_recycle->format('Y-m-d');}
						$date_last_ping = DateTime::createFromFormat('d/m/Y', $date_last_ping);
						if($date_last_ping){$date_last_ping=$date_last_ping->format('Y-m-d');}
						
						//check if asset already exist in GS db, compare with MAC address, and update all fields
						if($mac)
						{
							
							$mac=str_replace('-','',$mac);
							$mac=str_replace(':','',$mac);
							//convert in lowercase to compare
							$csv_mac=strtolower($mac);
							
							$qry=$db->prepare("SELECT * FROM tassets_iface WHERE mac=:mac AND disable='0'");
							$qry->execute(array('mac' => $csv_mac));
							$row=$qry->fetch();
							$qry->closeCursor();
							
							if(isset($row['mac']))
							{
								$gs_mac_disp=str_replace("'", "",$row['mac']);
								$gs_mac_disp=strtoupper($gs_mac_disp);
								$msg="[$gs_mac_disp] Asset already exist in GestSup database, check for update:";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								
								//load asset fiels from GS DB
								$qry2=$db->prepare("SELECT * FROM tassets WHERE id=(SELECT MAX(asset_id) FROM tassets_iface WHERE mac=:mac AND disable='0') AND disable='0'");
								$qry2->execute(array('mac' => $row['mac']));
								$gs=$qry2->fetch();
								$qry2->closeCursor();
								
								//compare sn_internal
								$gs['sn_internal']=$db->quote($gs['sn_internal']);
								if($gs['sn_internal']!=$sn_internal && $sn_internal!='\'\'') {
									$msg="[$gs_mac_disp] -Update sn_internal for asset_id $gs[id] with sn_internal $sn_internal";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET sn_internal=:sn_internal WHERE id=:id");
									$qry2->execute(array('sn_internal' => $sn_internal,'id' => $gs['id']));
								}
								//compare netbios
								$gs['netbios']=$db->quote($gs['netbios']);
								if($gs['netbios']!=$netbios && $netbios!='\'\'') {
									$msg="[$gs_mac_disp] -update name for asset_id $gs[id] with name $netbios";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET netbios=:netbios WHERE id=:id");
									$qry2->execute(array('netbios' => $netbios,'id' => $gs['id']));
								}
								//compare sn_manufacturer
								$gs['sn_manufacturer']=$db->quote($gs['sn_manufacturer']);
								if($gs['sn_manufacturer']!=$sn_manufacturer && $sn_manufacturer!='\'\'') {
									$msg="[$gs_mac_disp] -update sn_manufacturer for asset_id $gs[id] with name $sn_manufacturer";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET sn_manufacturer=:sn_manufacturer WHERE id=:id");
									$qry2->execute(array('sn_manufacturer' => $sn_manufacturer,'id' => $gs['id']));
								}
								//compare sn_indent
								$gs['sn_indent']=$db->quote($gs['sn_indent']);
								if($gs['sn_indent']!=$sn_indent && $sn_indent!='\'\'') {
									$msg="[$gs_mac_disp]  -update sn_indent for asset_id $gs[id] with name $sn_indent";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET sn_indent=:sn_indent WHERE id=:id");
									$qry2->execute(array('sn_indent' => $sn_indent,'id' => $gs['id']));
								}
								//compare description
								$gs['description']=$db->quote($gs['description']);
								if($gs['description']!=$description && $description!='\'\'') {
									$msg="[$gs_mac_disp] -update description for asset_id $gs[id] with name $description";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET description=:description WHERE id=:id");
									$qry2->execute(array('description' => $description,'id' => $gs['id']));
								}
								//date installation
								if($gs['date_install']!=$date_install && $date_install!='') {
									$msg="[$gs_mac_disp] -update date_install for asset_id $gs[id] with date_install $date_install";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_install=:date_install WHERE id=:id");
									$qry2->execute(array('date_install' => $date_install,'id' => $gs['id']));
								}
								//date end warranty
								if($gs['date_end_warranty']!=$date_end_warranty && $date_end_warranty!='') {
									$msg="[$gs_mac_disp] -update date_end_warranty for asset_id $gs[id] with date_end_warranty $date_end_warranty";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_end_warranty=:date_end_warranty WHERE id=:id");
									$qry2->execute(array('date_end_warranty' => $date_end_warranty,'id' => $gs['id']));
								}
								//date stock
								if($gs['date_stock']!=$date_stock && $date_stock!='') {
									$msg="[$gs_mac_disp] -update date_stock for asset_id $gs[id] with date_stock $date_stock";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_stock=:date_stock WHERE id=:id");
									$qry2->execute(array('date_stock' => $date_stock,'id' => $gs['id']));
								}
								//date standby
								if($gs['date_standbye']!=$date_standbye && $date_standbye!='') {
									$msg="[$gs_mac_disp] -update date_standbye for asset_id $gs[id] with date_standbye $date_standbye";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_standbye=:date_standbye WHERE id=:id");
									$qry2->execute(array('date_standbye' => $date_standbye,'id' => $gs['id']));
								}
								//date recycle
								if($gs['date_recycle']!=$date_recycle && $date_recycle!='') {
									$msg="[$gs_mac_disp] -update date_recycle for asset_id $gs[id] with date_recycle $date_recycle";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_recycle=:date_recycle WHERE id=:id");
									$qry2->execute(array('date_recycle' => $date_recycle,'id' => $gs['id']));
								}
								//date last ping
								if($gs['date_last_ping']!=$date_last_ping && $date_last_ping!='') {
									$msg="[$gs_mac_disp] -update date_last_ping for asset_id $gs[id] with date_last_ping $date_last_ping";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET date_last_ping=:date_last_ping WHERE id=:id");
									$qry2->execute(array('date_last_ping' => $date_last_ping,'id' => $gs['id']));
								}
								//socket
								$gs['socket']=$db->quote($gs['socket']);
								if($gs['socket']!=$socket && $socket!='\'\'') {
									$msg="[$gs_mac_disp] -update socket for asset_id $gs[id] with name $socket";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets SET socket=:socket WHERE id=:id");
									$qry2->execute(array('socket' => $socket,'id' => $gs['id']));
								}
								//ip address
								$row['ip']=$db->quote($row['ip']);
								if($row['ip']!=$ip && $ip!='\'\'') {
									$msg="[$gs_mac_disp] -update IP for asset_id $gs[id] with ip $ip";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets_iface SET ip=:ip WHERE id=:id");
									$qry2->execute(array('ip' => $ip,'id' => $row['id']));
								}
								//ip address
								$row['netbios']=$db->quote($row['netbios']);
								if($row['netbios']!=$netbios && $netbios!='\'\'') {
									$msg="[$gs_mac_disp] -update netbios for asset_id $gs[id] with netbios $netbios";
									if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
									else {if($rparameters['debug']) {echo "$msg<br />";}}
									$qry2=$db->prepare("UPDATE tassets_iface SET netbios=:netbios WHERE id=:id");
									$qry2->execute(array('netbios' => $netbios,'id' => $row['id']));
								}
								//type
								if($type!='\'\'') {
									//check if type already exist
									$qry2=$db->prepare("SELECT `id` FROM tassets_type WHERE `name`=:name");
									$qry2->execute(array('name' => $type));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new type asset_id $gs[id] with type $type";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tassets_type (name) VALUES (:name)");
										$qry2->execute(array('name' => $type));
									}
									//check if asset have different type between CSV and GS DB
									$qry2=$db->prepare("SELECT tassets_type.name,tassets_type.id FROM tassets_type,tassets WHERE tassets_type.id=tassets.type AND tassets.id=:id");
									$qry2->execute(array('id' => $gs['id']));
									$row2_type=$qry2->fetch();
									$qry2->closeCursor();
									if($row2_type['name']!=$type)
									{
										//find new type id
										$qry2=$db->prepare("SELECT id FROM tassets_type WHERE name=:name");
										$qry2->execute(array('name' => $type));
										$row2_type=$qry2->fetch();
										$qry2->closeCursor();
										$msg="[$gs_mac_disp] -update type asset_id $gs[id] with type $type";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("UPDATE tassets SET type=:type WHERE id=:id");
										$qry2->execute(array('type' => $row2_type['id'],'id' => $gs['id']));
									}
								}
								//manufacturer
								if($manufacturer!='\'\'') {
									//check if manufacturer already exist
									$qry2=$db->prepare("SELECT `id` FROM `tassets_manufacturer` WHERE UPPER(`name`)=UPPER(:name)");
									$qry2->execute(array('name' => $manufacturer));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new manufacturer asset_id $gs[id] with manufacturer $manufacturer";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tassets_manufacturer (name) VALUES (:name)");
										$qry2->execute(array('name' => $manufacturer));
									}
									//check if asset have different type between CSV and GS DB
									$qry2=$db->prepare("SELECT tassets_manufacturer.name,tassets_manufacturer.id FROM tassets_manufacturer,tassets WHERE tassets_manufacturer.id=tassets.manufacturer AND tassets.id=:id ");
									$qry2->execute(array('id' => $gs['id']));
									$row2_manufacturer=$qry2->fetch();
									$qry2->closeCursor();
									if($row2_manufacturer['name']!=$manufacturer)
									{
										//find new type id
										$qry2=$db->prepare("SELECT id FROM tassets_manufacturer WHERE name=:name");
										$qry2->execute(array('name' => $manufacturer));
										$row2_manufacturer=$qry2->fetch();
										$qry2->closeCursor();
										$msg="[$gs_mac_disp] -update manufacturer for asset_id $gs[id] with manufacturer $manufacturer";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("UPDATE tassets SET manufacturer=:manufacturer WHERE id=:id");
										$qry2->execute(array('manufacturer' => $row2_manufacturer['id'],'id' => $gs['id']));
									}
								}
								//model
								if($model!='') {
									//check if model already exist
									$qry2=$db->prepare("SELECT `id` FROM `tassets_model` WHERE `name`=:name");
									$qry2->execute(array('name' => $model));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new model asset_id $gs[id] with model $model";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("INSERT INTO `tassets_model` (`name`,`type`,`manufacturer`,`ip`) VALUES (:name,:type,:manufacturer,'1')");
										$qry3->execute(array('name' => $model,'type' => $row2_type['id'],'manufacturer' => $row2_manufacturer['id']));
									}
									//check if asset have different model between CSV and GS DB
									$qry2=$db->prepare("SELECT tassets_model.name FROM tassets_model,tassets WHERE tassets_model.id=tassets.model AND tassets.id=:id ");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if($row2['name']!=$model)
									{
										//find new type id
										$qry3=$db->prepare("SELECT id FROM tassets_model WHERE name=:name");
										$qry3->execute(array('name' => $model));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										
										$msg="[$gs_mac_disp] -update model for asset_id $gs[id] with model $model";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET model=:model WHERE id=:id");
										$qry3->execute(array('model' => $row3['id'],'id' => $gs['id']));
									}
								}
								//user
								if($user!='') {
									$user_array=explode(" ",$user);
									$csv_firstname=$user_array[0];
									$csv_lastname=$user_array[1];
									//check if user already exist and create it if not exist
									$qry2=$db->prepare("SELECT `id` FROM `tusers` WHERE `firstname` LIKE :firstname AND `lastname` LIKE :lastname ");
									$qry2->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new user $csv_firstname $csv_lastname";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tusers (firstname,lastname,profile) VALUES (:firstname,:lastname,'2')");
										$qry2->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
									}
									//check if asset have different user between CSV and GS DB
									$qry2=$db->prepare("SELECT tusers.id,tusers.firstname,tusers.lastname FROM tusers WHERE id=(SELECT user FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									$gs_firstname=strtolower($row2['firstname']); 
									$gs_lastname=strtolower($row2['lastname']); 
									$csv_firstname = strtolower($csv_firstname);
									$csv_lastname = strtolower($csv_lastname);
									if(($gs_firstname!=$csv_firstname) || ($gs_lastname!=$csv_lastname) )
									{
										//find new user id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tusers WHERE firstname LIKE :firstname AND lastname LIKE :lastname AND disable='0'");
										$qry3->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										$msg="[$gs_mac_disp] -update user for asset_id $gs[id] with user_id=$row3[0] $user_array[0] $user_array[1]";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET user=:user WHERE id=:id");
										$qry3->execute(array('user' => $row3['id'],'id' => $gs['id']));
									}
								}
								//technician
								if($technician!='') {
									$technician_array=explode(" ",$technician);
									$csv_firstname=$technician_array[0];
									$csv_lastname=$technician_array[1];
									//check if technician already exist and create it if not exist
									$qry2=$db->prepare("SELECT id FROM tusers WHERE firstname LIKE :firstname AND lastname LIKE :lastname ");
									$qry2->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2) {
										$msg="[$gs_mac_disp] -insert new technician $csv_firstname $csv_lastname";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tusers (firstname,lastname,profile) VALUES (:firstname,:lastname,'0')");
										$qry2->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
									}
									//check if asset have different technician between CSV and GS DB
									$qry2=$db->prepare("SELECT tusers.id,tusers.firstname,tusers.lastname FROM tusers WHERE id=(SELECT technician FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									$gs_firstname=strtolower($row2['firstname']); 
									$gs_lastname=strtolower($row2['lastname']); 
									$csv_firstname = strtolower($csv_firstname);
									$csv_lastname = strtolower($csv_lastname);
									if(($gs_firstname!=$csv_firstname) || ($gs_lastname!=$csv_lastname) )
									{
										//find new technician id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tusers WHERE firstname LIKE :firstname AND lastname LIKE :lastname AND disable='0'");
										$qry3->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										
										$msg="[$gs_mac_disp] -update technician for asset_id $gs[id] with user_id=$row3[0] $technician_array[0] $technician_array[1]";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET technician=:technician WHERE id=:id");
										$qry3->execute(array('technician' => $row3['id'],'id' => $gs['id']));
									}
								}
								//state
								if($state!='') {
									//check if state already exist and create it if not exist
									$qry2=$db->prepare("SELECT `id` FROM `tassets_state` WHERE `name`=:name");
									$qry2->execute(array('name' => $state));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									//echo "DEBUG $row2[id] SELECT `id` FROM `tassets_state` WHERE `name`=:name";
									
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new state $state";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tassets_state (name) VALUES (:name)");
										$qry2->execute(array('name' => $state));
									}
									//check if asset have different state between CSV and GS DB
									$qry2=$db->prepare("SELECT name FROM tassets_state WHERE id=(SELECT state FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									
									if(($row2['name']!=$state))
									{
										//find new state id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tassets_state WHERE name=:name AND disable='0'");
										$qry3->execute(array('name' => $state));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										$msg="[$gs_mac_disp] -update state for asset_id $gs[id] with state $state";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET state=:state WHERE id=:id");
										$qry3->execute(array('state' => $row3['id'],'id' => $gs['id']));
									}
								}
								//department
								if($department!='') {
									//check if service already exist and create it if not exist
									$qry2=$db->prepare("SELECT id FROM tservices WHERE name=:name");
									$qry2->execute(array('name' => $department));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2) {
										$msg="[$gs_mac_disp] -insert new service $department";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tservices (name) VALUES (:name)");
										$qry2->execute(array('name' => $department));
									}
									//check if asset have different service between CSV and GS DB
									$qry2=$db->prepare("SELECT name FROM tservices WHERE id=(SELECT department FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(($row2['name']!=$department))
									{
										//find new state id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tservices WHERE name=:name AND disable='0'");
										$qry3->execute(array('name' => $department));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										$msg="[$gs_mac_disp] -update service for asset_id $gs[id] with service $department";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET department=:department WHERE id=:id");
										$qry3->execute(array('department' => $row3['id'],'id' => $gs['id']));
									}
								}
								//maintenance
								if($maintenance!='') {
									//check if maintenance service already exist and create it if not exist
									$qry2=$db->prepare("SELECT id FROM tservices WHERE name=:name");
									$qry2->execute(array('name' => $maintenance));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2) {
										$msg="[$gs_mac_disp] -insert new maintenance service $maintenance";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tservices (name) VALUES (:name)");
										$qry2->execute(array('name' => $maintenance));
									}
									//check if asset have different service between CSV and GS DB
									$qry2=$db->prepare("SELECT name FROM tservices WHERE id=(SELECT maintenance FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(($row2['name']!=$maintenance))
									{
										//find new state id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tservices WHERE name=:name AND disable='0'");
										$qry3->execute(array('name' => $maintenance));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										$msg="[$gs_mac_disp] -update maintenance service for asset_id $gs[id] with maintenance service $maintenance";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET maintenance=:department WHERE id=:id");
										$qry3->execute(array('department' => $row3['id'],'id' => $gs['id']));
									}
								}
								//location
								if($location!='') {
									//check if location already exist and create it if not exist
									$qry2=$db->prepare("SELECT `id` FROM tassets_location WHERE `name`=:name AND `disable`='0'");
									$qry2->execute(array('name' => $location));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(!$row2['id']) {
										$msg="[$gs_mac_disp] -insert new location -$location-";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry2=$db->prepare("INSERT INTO tassets_location (name) VALUES (:name)");
										$qry2->execute(array('name' => $location));
									}
									//check if asset have different service between CSV and GS DB
									$qry2=$db->prepare("SELECT name FROM tassets_location WHERE id=(SELECT location FROM tassets WHERE id=:id)");
									$qry2->execute(array('id' => $gs['id']));
									$row2=$qry2->fetch();
									$qry2->closeCursor();
									if(($row2['name']!=$location))
									{
										//find new state id to associate with asset
										$qry3=$db->prepare("SELECT id FROM tassets_location WHERE name=:name AND disable='0'");
										$qry3->execute(array('name' => $location));
										$row3=$qry3->fetch();
										$qry3->closeCursor();
										$msg="[$gs_mac_disp] -update location for asset_id $gs[id] with location $location";
										if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
										else {if($rparameters['debug']) {echo "$msg<br />";}}
										$qry3=$db->prepare("UPDATE tassets SET location=:location WHERE id=:id");
										$qry3->execute(array('location' => $row3['id'],'id' => $gs['id']));
									}
								}
								//update discover flag
								$qry2=$db->prepare("UPDATE `tassets` SET `discover_import_csv`='1' WHERE `id`=:id");
								$qry2->execute(array('id' => $gs['id']));
							} else {
								$csv_mac_disp=str_replace("'", "",$csv_mac);
								$csv_mac_disp=strtoupper($csv_mac_disp);
								$msg="[$csv_mac_disp] Asset not exist in GestSup database, create new asset:";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$new_asset=1;
							}
						} 
						if($new_asset==1 || !$mac) //create new asset if no MAC adresse is present in CSV file or if asset not exist in GestSup DB
						{
							//get id value or create row in specific tables
							//type
							$find_type=0;
							$qry2=$db->prepare("SELECT `id`,`name` FROM `tassets_type`");
							$qry2->execute();
							while ($row=$qry2->fetch()){if(strtolower($type)==strtolower($row['name'])) $find_type=$row['id'];}
							$qry2->closeCursor();
							if($find_type==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create type $type: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_type` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $type));
								$type=$db->lastInsertId();
							} else {$type=$find_type;}
							//manufacturer
							$find_manufacturer=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_manufacturer`");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($manufacturer)==strtolower($row['name'])) $find_manufacturer=$row['id'];}
							$qry->closeCursor();
							if($find_manufacturer==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create manufacturer $manufacturer: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_manufacturer` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $manufacturer));
								$manufacturer=$db->lastInsertId();
							} else {$manufacturer=$find_manufacturer;}
							//model
							$find_model=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model`");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($model)==strtolower($row['name'])) $find_model=$row['id'];}
							$qry->closeCursor();
							if($find_model==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create model $model: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_model` (`name`,`manufacturer`,`type`,`ip`) VALUES (:name,:manufacturer,:type,'1')");
								$qry->execute(array('name' => $model,'manufacturer' => $manufacturer,'type' => $type));
								$model=$db->lastInsertId();
							} else {$model=$find_model;}
							//user
							$find_user=0;
							$csv_user=explode(" ", $user);
							if(!isset($csv_user[0])) $csv_user[0] = '';
							if(!isset($csv_user[1])) $csv_user[1] = '';
							$csv_firstname=$csv_user[0];
							$csv_lastname=$csv_user[1];
							$qry=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE disable='0'");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($csv_firstname)==strtolower($row['firstname']) && strtolower($csv_lastname)==strtolower($row['lastname'])) $find_user=$row['id'];}
							$qry->closeCursor();
							if($find_user==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create user $user: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tusers` (`firstname`,`lastname`,`profile`) VALUES (:firstname,:lastname,'2')");
								$qry->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
								$user=$db->lastInsertId();
							} else {$user=$find_user;}
							//state
							$find_state=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_state`");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($state)==strtolower($row['name'])) $find_state=$row['id'];}
							$qry->closeCursor();
							if($find_state==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create state $state: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_state` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $state));
								$state=$db->lastInsertId();
							} else {$state=$find_state;}
							//department
							$find_department=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE disable='0'");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($department)==strtolower($row['name'])) $find_department=$row['id'];}
							$qry->closeCursor();
							if($find_department==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create department $department: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tservices` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $department));
								$department=$db->lastInsertId();
							} else {$department=$find_department;}
							//location
							$find_location=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_location` WHERE disable='0'");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($location)==strtolower($row['name'])) $find_location=$row['id'];}
							$qry->closeCursor();
							if($find_location==0 && $location) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create location $location: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_location` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $location));
								$location=$db->lastInsertId();
							} else {$location=$find_location;}
							//technician
							$find_technician=0;
							$csv_technician=explode(" ", $technician);
							if(!isset($csv_technician[0])) $csv_technician[0] = '';
							if(!isset($csv_technician[1])) $csv_technician[1] = '';
							$csv_firstname=$csv_technician[0];
							$csv_lastname=$csv_technician[1];
							$qry=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE disable='0'");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($csv_firstname)==strtolower($row['firstname']) && strtolower($csv_lastname)==strtolower($row['lastname'])) $find_technician=$row['id'];}
							$qry->closeCursor();
							if($find_technician==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create technician $technician: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tusers` (`firstname`,`lastname`,`profile`) VALUES (:firstname,:lastname,'0')");
								$qry->execute(array('firstname' => $csv_firstname,'lastname' => $csv_lastname));
								$technician=$db->lastInsertId();
							} else {$technician=$find_technician;}
							//maintenance
							$find_maintenance=0;
							$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE disable='0'");
							$qry->execute();
							while ($row=$qry->fetch()){if(strtolower($maintenance)==strtolower($row['name'])) $find_maintenance=$row['id'];}
							$qry->closeCursor();
							if($find_maintenance==0) //create if not find in current table
							{
								$msg="[$csv_mac_disp] -create maintenance $maintenance: not found in GestSup database";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tservices` (`name`) VALUES (:name)");
								$qry->execute(array('name' => $maintenance));
								$maintenance=$db->lastInsertId();
							} else {$maintenance=$find_maintenance;}
							
							$query="INSERT INTO tassets 
							(sn_internal,netbios,sn_manufacturer,sn_indent,description,type,manufacturer,model,user,state,department,location,date_install,date_end_warranty,date_stock,date_standbye,date_recycle,date_last_ping,socket,technician,maintenance)
							VALUES
							($sn_internal, $netbios, $sn_manufacturer, $sn_indent, $description, $type, $manufacturer, $model, $user, $state, $department, $location, '$date_install', '$date_end_warranty', '$date_stock', '$date_standbye', '$date_recycle', '$date_last_ping', $socket, $technician, $maintenance)";
							if($rparameters['debug']) {if(!$argv[0]){echo "$query";}}
							$qry=$db->prepare("INSERT INTO `tassets` 
							(sn_internal,netbios,sn_manufacturer,sn_indent,description,type,manufacturer,model,user,state,department,location,date_install,date_end_warranty,date_stock,date_standbye,date_recycle,date_last_ping,socket,technician,maintenance)
							VALUES
							(:sn_internal,:netbios,:sn_manufacturer,:sn_indent,:description,:type,:manufacturer,:model,:user,:state,:department,:location,:date_install,:date_end_warranty,:date_stock,:date_standbye,:date_recycle,:date_last_ping,:socket,:technician,:maintenance)
							");
							$qry->execute(array(
								'sn_internal' => $sn_internal,
								'netbios' => $netbios,
								'sn_manufacturer' => $sn_manufacturer,
								'sn_indent' => $sn_indent,
								'description' => $description,
								'type' => $type,
								'manufacturer' => $manufacturer,
								'model' => $model,
								'user' => $user,
								'state' => $state,
								'department' => $department,
								'location' => $location,
								'date_install' => $date_install,
								'date_end_warranty' => $date_end_warranty,
								'date_stock' => $date_stock,
								'date_standbye' => $date_standbye,
								'date_recycle' => $date_recycle,
								'date_last_ping' => $date_last_ping,
								'socket' => $socket,
								'technician' => $technician,
								'maintenance' => $maintenance
								));
							$last_asset=$db->lastInsertId();
							
							//update discover flag
							$qry=$db->prepare("UPDATE `tassets` SET `discover_import_csv`='1' WHERE `id`=:id");
							$qry->execute(array('id' => $last_asset));
							
							//iface
							if($ip)
							{
								$msg="[$csv_mac_disp] -create iface with IP: $ip .";
								if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
								else {if($rparameters['debug']) {echo "$msg<br />";}}
								$qry=$db->prepare("INSERT INTO `tassets_iface` (`role_id`,`asset_id`,`netbios`,`ip`,`mac`,`disable`) VALUES ('1',:asset_id,:netbios,:ip,:mac,'0')");
								$qry->execute(array('asset_id' => $last_asset,'netbios' => $netbios,'ip' => $ip,'mac' => $mac,));
								$maintenance=$db->lastInsertId();
							}
						}
					}
				}
				fclose($file);
				//delete import file
				unlink("$import_dir/$filename");
				//display result
				if($i==0)
				{
					$msg="ERROR: file is empty";
					if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
					else {if($rparameters['debug']) {echo "$msg<br />";}}
				} else {
					$msg="Total checked: $i assets";
					if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
					else {
						echo DisplayMessage('success',T_('Import de').' '.$i.' '.T_('équipements effectué avec succès.'));
					}
				}
			} else {
				$msg="File extension: KO (file must be in csv format $filename)";
				if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
				else {
					$error='
						<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
							<div class="flex-grow-1">
								<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
								<strong class="text-danger">'.T_('Erreur').' : '.T_("L'extension du fichier d'import n'est pas supporté, importer un fichier de type CSV").'.</strong>
							</div>
							<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
							</button>
						</div>
					';
				}
			}
		} else {
			$msg="ERROR: Import file not exist";
			if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
			else {
				$error='
						<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
							<div class="flex-grow-1">
								<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
								<strong class="text-danger">'.T_('Erreur').' : '.T_("Le fichier d'import des équipements n'existe pas").'.</strong>
							</div>
							<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
								<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
							</button>
						</div>
					';
				}
		}
		
	} else {
		$msg="No new file detected: OK";
		if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
	}
} else {
	$msg="Server key validation: KO Wrong server key please check in Administration > System your private server key";
	if($argv[0]){echo $msg.PHP_EOL;logit('asset_import',$msg,'0');}
}
?>