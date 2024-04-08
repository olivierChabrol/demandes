<?php
################################################################################
# @Name : update.php
# @Description : page to update GestSup
# @Call : admin.php
# @Parameters : 
# @Author : Flox
# @Create : 20/01/2011
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($contents[0])) $contents[0] = '';
if(!isset($_POST['update_channel'])) $_POST['update_channel'] = '';
if(!isset($_POST['check'])) $_POST['check'] = '';
if(!isset($_POST['download'])) $_POST['download'] = '';
if(!isset($_POST['install'])) $_POST['install'] = '';
if(!isset($_POST['install_update'])) $_POST['install_update'] = '';
if(!isset($argv[1])) $argv[1] = '';
if(!isset($argv[2])) $argv[2] = '';
if(!isset($findpatch)) $findpatch = '';
if(!isset($message)) $message = '';
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE']='';

//parameters of GestSup update server 
$ftp_server="ftp.gestsup.fr";
$ftp_user_name="gestsup";
$ftp_user_pass="gestsup";

//check if script is executed from command line
if(php_sapi_name()=='cli') {$cmd_update=1;} else {$cmd_update=0;}

if($cmd_update) {
	if(!$argv[1]) {echo 'ERROR : you must specify autoinstall in first argument from command line'; exit;}
	if(!$argv[2]) {echo 'ERROR : you must specify server key in second argument from command line'; exit;}
}

//check autoinstall for command line options
if ($argv[1]=='autoinstall') {
	require(__DIR__ . '/../connect.php');
	//load parameters table
	$qry = $db->prepare("SELECT * FROM `tparameters`");
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
	
	if($argv[2]==$rparameters['server_private_key'])
	{
		echo "SERVER KEY VALIDATION : OK".PHP_EOL;
		$autoinstall=1;
	} else {
		echo "ERROR : Wrong server key, go to admin system panel";
		exit;
		$autoinstall=0;
	}
} else {
	$autoinstall=0;
}
 
//check dedicated version
if(substr_count($rparameters['version'], '.')==3) {$dedicated=1;} else {$dedicated=0;}
 
//display title
if(!$cmd_update)
{
	echo '
	<div class="page-header position-relative">
		<h1 class="page-title text-primary-m2">
			<i class="fa fa-cloud-upload-alt text-primary-m2"></i>  '.T_('Mise à jour de GestSup').'
		</h1>
	</div>
	';
}

//check rights permission on files
if(!$cmd_update && (!is_writable('./core/ticket.php') || !is_writable('./index.php') || !is_writable('./admin/parameters.php') || !is_writable('./download/readme.txt')))
{
	echo DisplayMessage('error',T_("Les fichiers serveur ne sont pas accessible en écriture, l'installation semi-automatique ne fonctionnera pas, modifier les droits d'écriture temporairement pour l'installation puis remettre les droits par défaut"));	
}
//check if php ftp extension is loaded
if (!extension_loaded('ftp')) {
	if($cmd_update)
	{
		echo 'UPDATE ERROR : you must load PHP_ftp extension from your php.ini'.PHP_EOL;
		exit;
	} else {
		echo DisplayMessage('error',T_("L'extension ftp de PHP n'est pas activée, mise à jour impossible. (Modifier le fichier php.ini)"));
	}
}

//update update channel parameter
if($_POST['update_channel']) 
{
	$qry=$db->prepare("UPDATE `tparameters` SET `update_channel`=:update_channel");
	$qry->execute(array('update_channel' => $_POST['update_channel']));
}

//get current channel 
$qry = $db->prepare("SELECT `update_channel` FROM `tparameters`");
$qry->execute();
$update_channel=$qry->fetch();
$update_channel= $update_channel[0];
$qry->closeCursor();

if($dedicated==0)
{
	//find current version
	$current_version=$rparameters['version'];
	$current_version2= explode('.',$current_version);
	if($rparameters['debug']) {echo "<b><u>DEBUG MODE:</u></b><br /> [CHANNEL] $update_channel<br /> [GET DATA] Local server version: $current_version (Version: $current_version2[0].$current_version2[1] Patch: $current_version2[2])<br />";}

	//find number of current patch
	$current_patch=$current_version2[2];

	//open ftp connection
	$conn_id = ftp_connect($ftp_server,21,2) or die(
	'
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="fa fa-remove"></i>
		</button>
		<strong>
			<i class="fa fa-remove"></i>
			'.T_('Erreur').':
		</strong>
		'.T_('Le serveur de mises à jour').' <b>'.$ftp_server.'</b> '.T_('est inaccessible, vérifier votre accès Internet ou l\'ouverture de votre firewall sur le port 21').'.
		</div>'
	);
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	$pasv = ftp_pasv($conn_id, true);

	//get ftp data of current update channel
	$ftp_list=ftp_nlist($conn_id, "./versions/current/$rparameters[update_channel]/");

	//get patch only
	$patch_ftp_list = preg_grep("/patch_$current_version2[0].$current_version2[1]/", $ftp_list);
	$patch_ftp_array = array();
	foreach($patch_ftp_list as $patch){
		$patch=explode("_",$patch);
		$patch=explode(".zip",$patch[1]);
		$patch=explode(".",$patch[0]);
		array_push($patch_ftp_array, $patch[2]);
	}
	asort($patch_ftp_array);
	$last_ftp_patch=end($patch_ftp_array);
	if ($last_ftp_patch) 
	{
		if($rparameters['debug']) {echo "[GET DATA] Last patch available on FTP server: $last_ftp_patch of version $current_version2[0].$current_version2[1]  <br />"; /*var_dump($patch_ftp_array);*/}
	} else {
		if($rparameters['debug']) {echo "[GET DATA] No patch available for version $current_version2[0].$current_version2[1]  <br />"; /*var_dump($patch_ftp_array);*/}
	}

	//get version only
	$version_ftp_list = preg_grep("/gestsup*/", $ftp_list);
	$version_ftp_array = array();
	foreach($version_ftp_list as $version){
		$version=explode("_",$version);
		$version=explode(".zip",$version[1]);
		$version=explode(".",$version[0]);
		array_push($version_ftp_array, $version);
	}
	asort($version_ftp_array);
	$last_ftp_version2=end($version_ftp_array);
	$last_ftp_version="$last_ftp_version2[0].$last_ftp_version2[1].$last_ftp_version2[2]";
	if ($last_ftp_version) 
	{
		if($rparameters['debug']) {echo "[GET DATA] Last version available on FTP server: $last_ftp_version<br />"; /*var_dump($version_ftp_array);*/}
	} else {
		if($rparameters['debug']) {echo "[GET DATA] No new version is available on FTP server. <br />"; /*var_dump($version_ftp_array)*/;}
	}

	//close ftp connection
	ftp_close($conn_id);

	//generate name of current version to display only
	$current_version_name='('.T_('Version').' '.$current_version2[0].'.'.$current_version2[1].' '.T_('avec patch').' '.$current_version2[2].')';
	 
	//check update server
	if ($last_ftp_version!=''){
		$serverstate='<i class="fa fa-check-circle text-success"></i> <span class="text-success">'.T_('Serveur de mises à jour').' <b>'.$ftp_server.'</b> '.T_('est disponible').'.</span>';
		$findversion=0;
		$findpatch=0;
		//compare versions check two first number of version name
		if (($current_version2[0]==$last_ftp_version2[0]) && ($current_version2[1]==$last_ftp_version2[1]))
		{
			if($rparameters['debug']) {echo "[COMPARE VERSIONS] Local server version $current_version2[0].$current_version2[1] is the same as FTP server $last_ftp_version2[0].$last_ftp_version2[1] <br />"; }
			//compare patchs
			if ($current_patch==$last_ftp_patch)
			{
				if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is the same that FTP server $last_ftp_patch <br />"; }
				$message=T_('Votre version est à jour');
				if($cmd_update){echo 'OK : CURRENT VERSION IS ALREADY UP TO DATE'; exit;}
			} 
			elseif ($current_patch>$last_ftp_patch)
			{
				if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is superior than FTP server $last_ftp_patch <br />"; }
				$message=T_('Votre version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_patch.' '.T_('est supérieur à la dernière version disponible, vous devez avoir changé de canal de mises à jour');
				if($cmd_update){echo 'OK : CURRENT VERSION SUPERIOR THAN LATEST AVAILABLE VERSION'; exit;}
			}
			elseif ($current_patch<$last_ftp_patch)
			{
				$findpatch=1;
				//generate n+1 name if more than one patch is available
				if (($last_ftp_patch-$current_patch)>1) {$next_ftp_patch=$current_patch+1;} else {$next_ftp_patch=$last_ftp_patch;}
				if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is inferior than FTP Server $last_ftp_patch <br />"; }
				$message=T_('Un nouveau patch').' '.$next_ftp_patch.' '.T_('pour votre version').' '.$current_version2[0].'.'.$current_version2[1].' '.T_('est disponible en téléchargement');
				$cmd_msg='NEW PATCH '.$next_ftp_patch.' AVAILABLE'.PHP_EOL;
			}
		}
		else if (($current_version2[0]<$last_ftp_version2[0]) || ($current_version2[1]<$last_ftp_version2[1]))
		{
			if($rparameters['debug']) {echo "[COMPARE VERSIONS] Local server version $current_version2[0].$current_version2[1] is inferior than FTP server $last_ftp_version2[0].$last_ftp_version2[1]<br />"; }
			$message=T_('La version').' '.$last_ftp_version.' '.T_('est disponible au téléchargement');
			$findversion=1;
		}
		else if (($current_version2[0]>$last_ftp_version2[0]) || (($current_version2[0]>$last_ftp_version2[0])&&($current_version2[1]>$last_ftp_version2[1])))
		{
			if($rparameters['debug']) {echo "[COMPARE VERSIONS] Local server version $current_version2[0].$current_version2[1] is superior than FTP server GestSup $last_ftp_version2[0].$last_ftp_version2[1], you are maybe a developer.<br />"; }
		}

		//display check message
		if($cmd_update){echo $cmd_msg;} else {if($_POST['check']) {echo DisplayMessage('success',$message);}}

		//downloads
		if($_POST['download'] || ($autoinstall==1))
		{
			if ($findversion==1) //version
			{
				$file_ftp_url="/versions/current/$update_channel/gestsup_$last_ftp_version.zip";
				$file_local_url=__DIR__ ."/../download/gestsup_$last_ftp_version.zip";
				$conn_id = ftp_connect($ftp_server);
				$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
				if ((!$conn_id) || (!$login_result)) {
					
					if($cmd_update)
					{
						echo 'ERROR DURING DOWNLOAD THE LATEST VERSION'.PHP_EOL;
						exit;
					}
					else {
						echo DisplayMessage('error',T_('Erreur').' : '.T_("Le téléchargement de la dernière version à échoué, vérifiez les droits d'écriture sur le répertoire ./download de votre serveur web"));
						die;
					}
				}
				$pasv = ftp_pasv($conn_id, true);
				$download = ftp_get($conn_id, $file_local_url, $file_ftp_url, FTP_BINARY);
				if (!$download) 
				{
					if($cmd_update)
					{
						echo 'ERROR DURING DOWNLOAD THE LATEST VERSION'.PHP_EOL;
						exit;
					} else {
						echo DisplayMessage('error',T_('Le téléchargement de la dernière version à échoué'));
					}
				}
				else 
				{
					if($cmd_update)
					{
						echo 'DOWNLOAD GESTSUP VERSION COMPLETED'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('La version').' '.$last_ftp_version.' '.T_('à été téléchargée dans le répertoire "./download" du serveur web'));
					}
				}
				ftp_quit($conn_id);
			}
			else if ($findpatch==1) //patch
			{
				$file_ftp_url="/versions/current/$update_channel/patch_$current_version2[0].$current_version2[1].$next_ftp_patch.zip";
				$file_local_url=__DIR__ ."/../download/patch_$current_version2[0].$current_version2[1].$next_ftp_patch.zip";
				$conn_id = ftp_connect($ftp_server);
				$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
				if ((!$conn_id) || (!$login_result)) {
					if($cmd_update)
					{
						echo 'ERROR DURING DOWNLOAD THE LATEST PATCH'.PHP_EOL;
					} else {
						echo DisplayMessage('error',T_('Le téléchargement du dernier patch à échoué. (connexion impossible)'));
						die;
					}
				} 
				$pasv = ftp_pasv($conn_id, true);
				$download = ftp_get($conn_id, $file_local_url, $file_ftp_url, FTP_BINARY);
				if (!$download) 
				{
					if($cmd_update)
					{
						echo 'ERROR DURING DOWNLOAD THE LATEST PATCH'.PHP_EOL;
					} else {
						echo DisplayMessage('error',T_('Le téléchargement du dernier patch à échoué. (Téléchargement impossible)'));
					}
				}
				else 
				{
					if($cmd_update)
					{
						echo 'DOWNLOAD GESTSUP PATCH COMPLETED'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('Le patch').'	'.$next_ftp_patch.' '.T_('à été téléchargé dans le répertoire').' "./download" '.T_('du serveur web'));
					}
				}
				ftp_quit($conn_id);
			} else {
				if($cmd_update)
				{
					echo 'CURRENT VERSION ALREADY UP TO DATE NO DOWNLOAD AVAILABLE'.PHP_EOL;
				} else {
					echo DisplayMessage('success',T_('Votre version').' '.$current_version.' '.T_('est à jour, pas de téléchargement nécessaire'));
				}
			}
		}
		//install version
		if($_POST['install'] || ($autoinstall==1))
		{
			if ($findpatch==0 && $findversion==0)
			{
				if($cmd_update)
				{
					echo 'UNABLE TO INSTALL UPDATE'.PHP_EOL;
				} else {
					echo DisplayMessage('success',T_('Installation impossible votre version est à jour'));
				}
			} 
			if($findversion!=0) 
			{
				if(file_exists(__DIR__ ."/../download/gestsup_$last_ftp_version.zip"))
				{
					$installfile="gestsup_$last_ftp_version.zip";
					if($cmd_update)
					{
						echo 'INSTALLING UPDATE...'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('Installation du fichier').' '.$installfile.' '.T_('en cours...'));
					}
					$type="version";
					include(__DIR__ ."/../core/install_update.php");
				} else {
					if($cmd_update)
					{
						echo 'ERROR YOU MUST DOWNLOAD LATEST VERSION FIRST'.PHP_EOL;
					} else {
						echo DisplayMessage('error',T_("Vous devez d'abord télécharger la dernière version").' '.$last_ftp_version);
					}
				}
			}
			if($findpatch!=0)
			{
				if(file_exists(__DIR__ ."/../download/patch_$current_version2[0].$current_version2[1].$next_ftp_patch.zip"))
				{
					$installfile="patch_$current_version2[0].$current_version2[1].$next_ftp_patch.zip";
					if($cmd_update)
					{
						echo 'INSTALLING UPDATE...'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('Installation du fichier').' '.$installfile.' '.T_('en cours...'));
					}
					$type="patch";
					include(__DIR__ ."/../core/install_update.php");
				} else {
					if($cmd_update)
					{
						echo 'ERROR : YOU MUST DOWNLOAD THE LATEST PATCH BEFORE'.PHP_EOL;
					} else {
						echo DisplayMessage('error',T_("Vous devez d'abord télécharger le dernier patch").' '.$next_ftp_patch);
					}
				}
			}
		}
		
	} else {
		$serverstate='<i class="fa fa-times text-danger"></i> <span class="text-danger">'.T_("Serveur de mise à jour GestSup indisponible, ou vous avez un problème de connection internet ou vous n'avez pas autorisé le port 21 sur votre firewall").'.</span>';
	}

	//display informations
	if(!$cmd_update)
	{
		echo'
			<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
				<div class="card-body p-0 table-responsive-xl">
					<table class="table text-dark-m1 brc-black-tp10 mb-0">
						<tbody>
							<tr>
								<td style="width: 160px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-tag text-blue-m3 pr-1"></i>'.T_('Version actuelle').'</td>
								<td class="text-95 text-default-d3">'.$rparameters['version'].' <span style="font-size: x-small;">'.$current_version_name.'</span></td>
							</tr>
							<tr>
								<td style="width: 160px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-code-branch text-blue-m3 pr-1"></i>'.T_('Canal').'</td>
								<td class="text-95 text-default-d3">
									<form method="POST" name="form">
										<select style="width:auto;" class="form-control form-control-sm " name="update_channel" onchange="submit()">
											<option value="stable" '; if ($update_channel=='stable') echo 'selected'; echo '>'.T_('Stable').'</option>
											<option value="beta" '; if ($update_channel=='beta') echo 'selected'; echo '>'.T_('Bêta').'</option>
										</select>
									</form>
								</td>
							</tr>
							<tr>
								<td style="width:220px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-server text-blue-m3 pr-1"></i>'.T_('Serveur de mise à jour').'</td>
								<td class="text-95 text-default-d3">'.$serverstate.'</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		
			<div class=" brc-secondary-l1 bgc-secondary-l4 py-3 text-center mt-5">
				<form method="POST" action="">
					<button  title="'.T_('Vérifie sur le serveur FTP de GestSup si une version plus récente existe').'." name="check" value="check" type="submit" class="btn btn-primary m-1">
						<i class="fa fa-check-circle pr-1"></i>
						1 - '.T_('Vérifier').' 
					</button>
					<button  title="'.T_("Redirige vers la section sauvegarde de l'application").'" onclick=\'window.open("./index.php?page=admin&subpage=backup")\'  type="submit" class="btn btn-primary m-1">
						<i class="fa fa-save pr-1"></i>
						2 - '.T_('Réaliser une sauvegarde').'
					</button>
					<button title="'.T_('Lance le téléchargement depuis le serveur FTP de GestSup si une version plus récente existe').'." name="download" value="download" type="submit" class="btn btn-primary m-1">
						<i class="fa fa-download pr-1"></i>
						3 - '.T_('Télécharger').'
					</button>
					<button title="'.T_("Lance l'installation de la version téléchargée").'" name="install" value="install" type="submit" class="btn btn-primary m-1">
						<i class="fa fa-hdd pr-1"></i>
						4 - '.T_('Installation semi-automatique').'
					</button>
				</form>
				<br />
				<button title="'.T_('Lance le site web dans la section documentation').'" onclick=\'window.open("https://gestsup.fr/index.php?page=support&item1=update&item2=manual#53")\' type="submit" class="btn btn-grey m-1">
					<i class="fa fa-book pr-1"></i>
					'.T_('Installation manuelle').'
				</button>
				<button title="'.T_('Lance le site web dans la section documentation').'" onclick=\'window.open("https://gestsup.fr/index.php?page=support&item1=update&item2=auto#51")\' type="submit" class="btn btn-grey m-1">
					<i class="fa fa-book pr-1"></i>
					'.T_('Installation automatique').'
				</button>
			</div>
		';
	}
} else { //dedicated version
	//find current version
	$current_version=$rparameters['version'];
	$current_version2= explode('.',$current_version);
	
	//find number of current patch
	$current_patch=$current_version2[3];
	
	if($rparameters['debug']) {echo "<b><u>DEBUG MODE:</u></b><br /> [VERSION] Dedicated <br />[GET DATA] Local server version: $current_version (Version: $current_version2[0].$current_version2[1].$current_version2[2] Patch: $current_patch)<br />";}

	//open ftp connection
	$conn_id = ftp_connect($ftp_server,21,2) or die(
	'
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">
			<i class="fa fa-remove"></i>
		</button>
		<strong>
			<i class="fa fa-remove"></i>
			'.T_('Erreur').':
		</strong>
		'.T_('Le serveur de mises à jour').' <b>'.$ftp_server.'</b> '.T_('est inaccessible, vérifier votre accès Internet ou l\'ouverture de votre firewall sur le port 21').'.
		</div>'
	);
	$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
	$pasv = ftp_pasv($conn_id, true);
	
	if(!$conn_id) {
		$serverstate='<i class="fa fa-times text-danger"></i> <span class="text-danger">'.T_("Serveur de mise à jour GestSup indisponible, ou vous avez un problème de connection internet ou vous n'avez pas autorisé le port 21 sur votre firewall").'.</span>';
	} else {
		$serverstate='<i class="fa fa-check-circle text-success"></i> <span class="text-success">'.T_('Serveur de mise à jour').' <b>'.$ftp_server.'</b> '.T_('est disponible').'.</span>';
	}
	
	//check dedicated version exist
	if(!isset($command)) $command= '';
	if (!ftp_chdir($conn_id,"./versions/dedicated/$rparameters[server_private_key]/")){
		if($rparameters['debug']) {echo "[DEDICATED] Directory not found<br />";}
	} else {
		if($rparameters['debug']) {echo "[DEDICATED] Directory found<br />";}
		//list ftp data from dedicated version
		$ftp_list=ftp_nlist($conn_id, "");
		//get patch only
		$patch_ftp_list = preg_grep("/patch_$current_version2[0].$current_version2[1].$current_version2[2]/", $ftp_list);
		$patch_ftp_array = array();
		foreach($patch_ftp_list as $patch){
			$patch=explode("_",$patch);
			$patch=explode(".zip",$patch[1]);
			$patch=explode(".",$patch[0]);
			array_push($patch_ftp_array, $patch[3]);
		}
		asort($patch_ftp_array);
		$last_ftp_patch=end($patch_ftp_array);
		if ($last_ftp_patch) 
		{
			if($rparameters['debug']) {echo "[GET DATA] Last patch available on FTP server: $last_ftp_patch of version $current_version2[0].$current_version2[1].$current_version2[2]  <br />"; /*var_dump($patch_ftp_array);*/}
		} else {
			if($rparameters['debug']) {echo "[GET DATA] No patch available for version $current_version2[0].$current_version2[1].$current_version2[2]  <br />"; /*var_dump($patch_ftp_array);*/}
		}

		//close ftp connection
		ftp_close($conn_id);

		//generate name of current version to display only
		$current_version_name='('.T_('Version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].' '.T_('avec patch').' '.$current_version2[3].')';
		 
		//check update server
		$findversion=0;
		$findpatch=0;

		if(!$last_ftp_patch)
		{
			if($cmd_update)
			{
				echo 'NO NEW PATCH AVAILABLE FOR YOUR VERSION'.PHP_EOL;
				exit;
			} else {
				$message=T_('Aucun nouveau patch pour votre version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].' Patch '.$current_patch.' '.T_("n'est encore disponible");
			}
		}elseif ($current_patch==$last_ftp_patch)
		{
			if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is the same that FTP server $last_ftp_patch <br />"; }
			$message=T_('Votre version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].' Patch '.$current_patch.' '.T_('est à jour');
		} 
		elseif ($current_patch>$last_ftp_patch)
		{
			if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is superior than FTP server $last_ftp_patch <br />"; }
			if($cmd_update)
			{
				echo 'CURRENT VERSION IS SUPERIOR THAN AVAILABLE VERSION'.PHP_EOL;
				exit;
			} else {
				$message=T_('Votre version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].' Patch '.$current_patch.' '.T_('est supérieur à la dernière version disponible, vous devez avoir changé de canal de mises à jour');
			}
		}
		elseif ($current_patch<$last_ftp_patch)
		{
			$findpatch=1;
			//generate n+1 name if more than one patch is available
			if (($last_ftp_patch-$current_patch)>1) {$next_ftp_patch=$current_patch+1;} else {$next_ftp_patch=$last_ftp_patch;}
			if($rparameters['debug']) {echo "[COMPARE PATCH] Local server patch $current_patch is inferior than FTP Server $last_ftp_patch <br />"; }
			if($cmd_update)
			{
				echo 'A NEW PATCH IS AVAILABLE FOR YOUR VERSION'.PHP_EOL;
			} else {
				$message=T_('Un nouveau patch').' '.$next_ftp_patch.' '.T_('pour votre version').' '.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].' '.T_('est disponible en téléchargement');
			}
		}
		//display check message
		if($cmd_update)
		{
			echo 'A NEW PATCH IS AVAILABLE FOR YOUR VERSION'.PHP_EOL;
		} else {
			if($_POST['check']) {echo DisplayMessage('success',$message);}
		}

		//downloads
		if($_POST['download'] || ($autoinstall==1))
		{
			if ($findpatch==1) //patch
			{
				$file_ftp_url="/versions/dedicated/$rparameters[server_private_key]/patch_$current_version2[0].$current_version2[1].$current_version2[2].$next_ftp_patch.zip";
				$file_local_url=__DIR__ ."/../download/patch_$current_version2[0].$current_version2[1].$current_version2[2].$next_ftp_patch.zip";
				$conn_id = ftp_connect($ftp_server);
				$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
				if ((!$conn_id) || (!$login_result)) {
					if($cmd_update)
					{
						echo 'ERROR : UNABLE TO DOWNLOAD THE LATEST PATCH'.PHP_EOL;
						exit;
					} else {
						echo DisplayMessage('error',T_('Le téléchargement du dernier patch à échoué. (connexion impossible)'));
						die;
					}
				} 
				$pasv = ftp_pasv($conn_id, true);
				$download = ftp_get($conn_id, $file_local_url, $file_ftp_url, FTP_BINARY);
				if (!$download) 
				{
					if($cmd_update)
					{
						echo 'ERROR DURING DOWNLOAD THE LATEST PATCH'.PHP_EOL;
						exit;
					} else {
						echo DisplayMessage('error',T_('Le téléchargement du dernier patch à échoué. (Téléchargement impossible)'));
					}
				}
				else 
				{
					if($cmd_update)
					{
						echo 'LATEST PATCH SUCCESSFULLY DOWNLOADED'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('Le patch').'	'.$next_ftp_patch.' '.T_('à été téléchargé dans le répertoire').' "./download" '.T_('du serveur web'));
					}
				}
				ftp_quit($conn_id);
			} else {
				if($cmd_update)
				{
					echo 'YOUR VERSION IS UP TO DATE'.PHP_EOL;
				} else {
					echo DisplayMessage('success',T_('Votre version').' '.$current_version.' '.T_('est à jour, pas de téléchargement nécessaire'));
				}
			}
		}
		//install patch
		if($_POST['install'] || ($autoinstall==1))
		{
			if ($findpatch==0)
			{
				if($cmd_update)
				{
					echo 'UNABLE TO INSTALL YOUR VERSION IS UP TO DATE'.PHP_EOL;
					exit;
				} else {
					echo DisplayMessage('success',T_('Installation impossible votre version est à jour'));
				}
			} 
			if($findpatch!=0)
			{
				if(file_exists(__DIR__ ."/../download/patch_$current_version2[0].$current_version2[1].$current_version2[2].$next_ftp_patch.zip"))
				{
					$installfile="patch_$current_version2[0].$current_version2[1].$current_version2[2].$next_ftp_patch.zip";
					if($cmd_update)
					{
						echo 'INSTALLING PATCH...'.PHP_EOL;
					} else {
						echo DisplayMessage('success',T_('Installation du fichier').' '.$installfile.' '.T_('en cours...'));
					}
					$type="patch";
					include(__DIR__ ."/../core/install_update.php");
				} else {
					if($cmd_update)
					{
						echo 'YOU MUST DOWNLOAD THE LATEST PATCH BEFORE'.PHP_EOL;
						exit;
					} else {
						echo DisplayMessage('error',T_("Vous devez d'abord télécharger le dernier patch").' '.$next_ftp_patch);
					}
				}
			}
		}

		//display informations
		if(!$cmd_update)
		{
			echo'
			<table class="table table table-bordered">
				<tbody>
					<tr>
						<td style="width: 220px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-tag text-blue-m3 pr-1"></i>'.T_('Version actuelle').'</td>
						<td class="text-95 text-default-d3"><a href="./index.php?page=changelog">'.$rparameters['version'].' <span style="font-size: x-small;">'.$current_version_name.'</td>
					</tr>
					<tr>
						<td style="width: 220px;" class="text-95 text-default-d3 bgc-secondary-l4"><i class="fa fa-server text-blue-m3 pr-1"></i>'.T_('Serveur de mises à jour').'</td>
						<td class="text-95 text-default-d3">'.$serverstate.'</td>
					</tr>
				</tbody>
			</table>
			<div class="border-t-1 brc-secondary-l1 bgc-secondary-l4 py-3 text-center mt-5">
				<form method="POST" action="">
					<button  title="'.T_('Vérifie sur le serveur FTP de GestSup si une version plus récente existe').'." name="check" value="check" type="submit" class="btn btn-primary mr-1">
						<i class="fa fa-check-circle "></i>
						1- '.T_('Vérifier').' 
					</button>
					<button title="'.T_("Redirige vers la section sauvegarde de l'application").'" onclick=\'window.open("./index.php?page=admin&subpage=backup")\' type="submit" class="btn btn-primary mr-1">
						<i class="fa fa-save "></i>
						2- '.T_('Réaliser une sauvegarde').'
					</button>
					<button title="'.T_('Lance le téléchargement depuis le serveur FTP de GestSup si une version plus récente existe').'." name="download" value="download" type="submit" class="btn btn-primary mr-1">
						<i class="fa fa-download "></i>
						3- '.T_('Télécharger').'
					</button>
					<button title="'.T_("Lance l'installation de la version téléchargée").'" name="install" value="install" type="submit" class="btn btn-primary">
						<i class="fa fa-hdd "></i>
						4- '.T_('Installation semi-automatique').'
					</button>
				</form>
					<br />
					<button title="'.T_('Lance le site web dans la section documentation').'" onclick=\'window.open("https://gestsup.fr/index.php?page=support&item1=update&item2=manual#53")\' type="submit" class="btn btn-grey mr-1">
						<i class="fa fa-book "></i>
						'.T_('Installation manuelle').'
					</button>
					<button title="'.T_('Lance le site web dans la section documentation').'" onclick=\'window.open("https://gestsup.fr/index.php?page=support&item1=update&item2=auto#51")\' type="submit" class="btn btn-grey">
						<i class="fa fa-book "></i>
						'.T_('Installation automatique').'
					</button>
			</div>
			';
		}
	}
}
?>