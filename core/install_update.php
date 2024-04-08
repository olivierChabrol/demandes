<?php
################################################################################
# @Name : install_update.php
# @Description : install update 
# @Call : update.php
# @Parameters : $dedicated $type $installfile
# @Author : Flox
# @Create : 06/11/2012
# @Update : 18/03/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($command)) $command= '';
if(!isset($cmd_update)) $cmd_update= '';
if(!isset($error)) $error= '';
if(!isset($_POST['step'])) $_POST['step']= '';
if(!isset($_POST['cancel'])) $_POST['cancel']= '';

//defaults values
if(!isset($step)) $step= '1';
if($autoinstall==1) $step= '4';

if($_POST['cancel'])
{
	//redirect
	$www = ".index.php?page=admin&subpage=update";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

//update current step
if($_POST['step']==1) $step=2;
if($_POST['step']==2) $step=3;
if($_POST['step']==3) $step=4;
if($_POST['step']==4) $step=5;
if($_POST['step']==5) $step=6;
if($_POST['step']==6) $step=7;
if($_POST['step']==7) $step=8;

//get current date
$date = date("Y-m-d");

//display backup warning
if($step==1 && ($autoinstall==0))
{
	echo '
	<div class="modal fade" id="install_1" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-save text-danger pr-2"></i>'.T_("Réaliser une sauvegarde").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form1" method="POST" action="" id="form1">
						<input name="step" type="hidden" value="2">
						<input name="install" type="hidden" value="1">
							'.T_("Si l'opération n'a pas déja été faites, réaliser une sauvegarde de vos fichiers et de votre base de données").'.
							<br />
							<br />
							<a target="_blank" href="https://gestsup.fr/index.php?page=support&item1=backup#8">'.T_("Plus d'informations").'</a>
					</form>
					<form name="form0" method="POST" action="" id="form0">
						<input name="cancel" type="hidden" value="1">
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$(\'form#form1\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Continuer').'</button>
					<button type="button" class="btn btn-danger" onclick="$(\'form#form0\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#install_1").modal(\'show\');});</script>
	';
}
//start display
if($step==3  && ($autoinstall==0))
{
	echo '
	<div class="modal fade" id="install_3" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-bolt text-danger pr-2"></i>'.T_("Lancement de l'installation").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form3" method="POST" action="" id="form3">
						<input name="step" type="hidden" value="3">
						<input name="install" type="hidden" value="1">
						'.T_("Voulez-vous lancer l'installation du fichier ").$installfile.' ?
					</form>
					<form name="form0" method="POST" action="" id="form0">
						<input name="cancel" type="hidden" value="1">
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$(\'form#form3\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Continuer').'</button>
					<button type="button" class="btn btn-danger" onclick="$(\'form#form0\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#install_3").modal(\'show\');});</script>
	';
}
//extract last version or last patch
if($step==4)
{	
	//create temporary directory
	if(file_exists(__DIR__ .'/../download/tmp')) {} else {mkdir(__DIR__ ."/../download/tmp");} 
	//extract data into temporary directory
	$zip = new ZipArchive;
    $res = $zip->open(__DIR__ .'/../download/'.$installfile.'');
    if($res === TRUE) {
        $zip->extractTo(__DIR__ .'/../download/tmp/');
        $zip->close();
	}
	//check extract
	if(file_exists(__DIR__ .'/../download/tmp/changelog.php'))
	{
		$result='- '.T_('Extraction des fichiers').': <i class="fa fa-check-circle text-success"></i><br />';
	    $step=5;
    } else {
		$result='- '.T_('Extraction des fichiers').': <i class="fa fa-times-circle text-danger"></i> open='.$res.' <br />';
		$error=1;
	}
}
//install SQL update
if($step==5)
{
	//case version update
	if($type=='version')
	{
		//find list of sql update
		$matches = glob(__DIR__ ."/../download/tmp/_SQL/*.sql"); 
		foreach ($matches as $filename) {
			if($rparameters['debug']) {echo "[EXECUTE SQL UPDATE]: $filename <br />";}
			//get only filename
			$filename=explode (__DIR__ .'/../download/tmp/_SQL/',$filename);
			$filename=$filename[1];
			//find source version of this file
			$src=explode('_',$filename);
			$src=$src[1];
			//find destination version of this file
			$dst=explode('_to_',$filename);
			$dst=explode('.sql',$dst[1]);
			$dst=$dst[0];
			//keep only superior patch
			$subsrc=explode('.',$src);
			if($subsrc[0]>=$current_version2[0])
			{
				if($subsrc[1]>=$current_version2[1]) 
				{
					if($subsrc[2]>=$current_version2[2]) 
					{
						//import script
						$sql_file=file_get_contents(__DIR__ .'/../download/tmp/_SQL/'.$filename.'');
						$sql_file=explode(";", $sql_file);
						foreach ($sql_file as $value) {
							if($value!='') {$db->query($value);}
						} 
					}
				}
			}
		}
		//check
		$qry=$db->prepare("SELECT `version` FROM `tparameters`");
		$qry->execute();
		$rvactu=$qry->fetch();
		$qry->closeCursor();
		
		$vactu="$rvactu[version]";
		if($vactu==$last_ftp_version) {
			$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-check-circle text-success"></i><br />';
			$step=6;
		} else {
			$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-times-circle text-danger"></i><br />';
			$error=1;
		}
	}
	//case patch update
	if($type=='patch')
	{
		if($dedicated==1)
		{
			$storefilename='update_'.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].'.'.$current_version2[3].'_to_'.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].'.'.$next_ftp_patch.'.sql';
		} else {
			$storefilename='update_'.$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].'_to_'.$current_version2[0].'.'.$current_version2[1].'.'.$next_ftp_patch.'.sql';
		}
		$sql_file=file_get_contents(__DIR__ .'/../download/tmp/_SQL/'.$storefilename.'');
		$sql_file=explode(";", $sql_file);
		foreach ($sql_file as $value) {
			if($value!='') {$db->query($value);}
		}
		//check
		$qry=$db->prepare("SELECT `version` FROM `tparameters`");
		$qry->execute();
		$rvactu=$qry->fetch();
		$qry->closeCursor();
		$vactu="$rvactu[version]";
		
		if($dedicated==1)
		{
			if($vactu==$current_version2[0].'.'.$current_version2[1].'.'.$current_version2[2].'.'.$next_ftp_patch) {
			$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-check-circle text-success"></i><br />';
			$step=6;
			} else {
				$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-times-circle text-danger"></i><br />'; 
				$error=1;
			}
		} else {
			if($vactu==$current_version2[0].'.'.$current_version2[1].'.'.$next_ftp_patch) {
			$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-check-circle text-success"></i><br />';
			$step=6;
			} else {
				$result=$result.'- '.T_('Modification base de données').': <i class="fa fa-times-circle text-danger"></i><br />'; 
				$error=1;
			}
		}
	}
}
//copy latest files
if($step==6)
{
	//backup current connect.php file
	copy(__DIR__ .'/../connect.php', __DIR__ .'/../backup/connect.php'); 
	//recursive copy with new files from the temp directory to production directory
	function recurse_copy($src,$dst) { 
		$dir = opendir($src); 
		@mkdir($dst); 
		while(false !== ( $file = readdir($dir)) ) { 
			if(( $file != '.' ) && ( $file != '..' )) { 
				if( is_dir($src . '/' . $file) ) { 
					recurse_copy($src . '/' . $file,$dst . '/' . $file); 
				} 
				else { 
					copy($src . '/' . $file,$dst . '/' . $file); 
				} 
			} 
		} 
		closedir($dir); 
	}  
	recurse_copy(__DIR__ ."/../download/tmp/",__DIR__ ."/../");
	//restore connect.php file previously backup-ed
	rename(__DIR__ .'/../backup/connect.php', __DIR__ .'/../connect.php'); 
	$result=$result.'- '.T_('Copie des nouveaux fichiers').': <i class="fa fa-check-circle text-success"></i><br />';
	
	
	$step=7;
}
//clean temporary folder.
if($step==7)
{
	//delete download file
	unlink(__DIR__ ."/../download/$installfile");
	//remove temporary directory
	function rrmdir($dir) {
	   if(is_dir($dir)) {
		 $objects = scandir($dir);
		 foreach ($objects as $object) {
		   if($object != "." && $object != "..") {
			 if(filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
		   }
		 }
		 reset($objects);
		 rmdir($dir);
	   }
	 }
	$dir=__DIR__ ."/../download/tmp/";
	rrmdir($dir);
	
	//remove install directory
	if(is_dir(__DIR__ ."/install/")) 
	{
		$dir=__DIR__ ."/install/";
		rrmdir($dir);
	}
	//remove install directory
	if(is_dir(__DIR__ ."/../install/")) 
	{
		$dir=__DIR__ ."/../install/";
		rrmdir($dir);
	}
	$result=$result.'- '.T_("Nettoyage de l'installation").': <i class="fa fa-check-circle text-success"></i><br />';
	$step=8;
}
if($step==8)
{
	if($cmd_update)
	{
		echo 'INSTALL SUCCESSFUL'.PHP_EOL;
	} else {
		echo '
		<div class="modal fade" id="install_8" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="ModalLabel"><i class="fa fa-cloud-upload-alt text-success pr-2"></i>'.T_("Rapport d'installation").'</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form name="form8" method="POST" action="" id="form8">
							<input name="cancel" type="hidden" value="1">
							<input name="install" type="hidden" value="1">
							'.T_("L'installation s'est correctement déroulée").':<br /><br />
							'.$result.'<br />
							'.T_("Afin de finaliser la procédure, déconnectez vous, videz le cache de votre navigateur, et relancer l'application").'.
						</form>
						<form name="form0" method="POST" action="" id="form0">
							<input name="cancel" type="hidden" value="1">
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" onclick="$(\'form#form0\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Fermer').'</button>
					</div>
				</div>
			</div>
		</div>
		<script>$(document).ready(function(){$("#install_8").modal(\'show\');});</script>
		';
	}
	
} elseif($error==1) {
	echo '
	<div class="modal fade" id="install_8" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-cloud-upload-alt text-danger pr-2"></i>'.T_("Rapport d'installation").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form8" method="POST" action="" id="form8">
						<input name="cancel" type="hidden" value="1">
						<input name="install" type="hidden" value="1">
						'.T_("Une erreur est survenue pendant l'installation, il est recommandé de restaurer votre base de données et vos fichiers, puis de lancer la procédure manuellement").'.:<br /><br />
						'.$result.'<br />
					</form>
					<form name="form0" method="POST" action="" id="form0">
						<input name="cancel" type="hidden" value="1">
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" onclick="$(\'form#form0\').submit();"><i class="fa fa-times pr-2"></i>'.T_('Fermer').'</button>
				</div>
			</div>
		</div>
	</div>
	<script>$(document).ready(function(){$("#install_8").modal(\'show\');});</script>
	';
}
?>