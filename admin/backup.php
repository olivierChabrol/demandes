<?php
################################################################################
# @Name : backup.php
# @Description : save and restore page
# @Call : /admin/admin.php
# @Parameters : 
# @Author : Flox
# @Create : 25/04/2013
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($action)) $action = '';
if(!isset($command)) $command = '';
if(!isset($date)) $date = '';
if(!isset($_FILES['restore']['name'])) $_FILES['restore']['name'] = '';
if(!isset($_FILES['logo']['name'])) $_FILES['logo']['name'] = '';
if(!isset($_POST['upload'])) $_POST['upload'] = '';

//export backup
if($_GET['action']=="backup" && $rright['admin'] && $rright['admin_backup'])
{
	//generate date name
	$date = date("Y_m_d_H_i_s");

	//dump SQL
	$file="./_SQL/backup-gestsup-$rparameters[version]-$date.sql";
	include_once("./components/mysqldump-php/src/Ifsnop/Mysqldump/Mysqldump.php");
	if(!isset($port)) {
		//get current port
		$qry=$db->prepare("SHOW VARIABLES WHERE Variable_name = 'port'");
		$qry->execute();
		$variable=$qry->fetch();
		$qry->closeCursor();
		$detected_port=$variable[1];
		
		$dump = new Ifsnop\Mysqldump\Mysqldump("mysql:host=$host;port=$detected_port;dbname=$db_name;charset=$charset","$user", "$password");
	} else {
		$dump = new Ifsnop\Mysqldump\Mysqldump("mysql:host=$host;port=$port;dbname=$db_name;charset=$charset","$user", "$password");
	}
	$dump->start($file);
	
	//backup files
	function Zip($source, $destination)
	{
		if(!extension_loaded('zip') || !file_exists($source)) {
			return false;
		}

		$zip = new ZipArchive();
		if(!$zip->open($destination, ZIPARCHIVE::CREATE)) {
			return false;
		}

		$source = str_replace('\\', '/', realpath($source));

		if(is_dir($source) === true)
		{
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file)
			{
			    if(strpos($file,'backup_gestsup') == false) {
    				$file = str_replace('\\', '/', $file);
    
    				// Ignore "." and ".." folders
    				if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
    					continue;
    
    				$file = realpath($file);
                    
    				if(is_dir($file) === true)
    				{
    					$zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
    				}
    				else if(is_file($file) === true)
    				{
    					$zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
    				}
			    }
			}
		}
		else if(is_file($source) === true)
		{
			$zip->addFromString(basename($source), file_get_contents($source));
		}

		return $zip->close();
	}
	Zip('./', './backup/backup_gestsup_'.$rparameters['version'].'_'.$date.'.zip');	
	
	//check SQL dump
	$check_sql_dump=0;
	$pattern='./_SQL/backup-gestsup-'.$rparameters['version'].'-'.date('Y').'_'.date('m').'_'.date('d').'_*.sql';
	foreach (glob($pattern) as $filename) {$check_sql_dump=1;}
	
	//check backup
	if(file_exists('./backup/backup_gestsup_'.$rparameters['version'].'_'.$date.'.zip') && $check_sql_dump==1) 
	{
		echo DisplayMessage('success',T_("La sauvegarde à été réalisée avec succès, une copie de l'archive se trouve dans le répertoire ./backup de votre serveur web"));
		$step=5;
	} else {
		if(!file_exists('./backup/backup_gestsup_'.$rparameters['version'].'_'.$date.'.zip'))
		{
			echo DisplayMessage('error',T_("La sauvegarde de l'application à échoué (Aucun fichier zip détecté dans ./backup)"));
		} elseif($check_sql_dump==0) {
			echo DisplayMessage('error',T_("La sauvegarde de la base de données de l'application à échoué (Aucun fichier SQL détecté dans ./_SQL)"));
		}
		$error=1;
	}
	
	//redirect
	$www = "./index.php?page=admin&subpage=backup&download_backup=backup_gestsup_$rparameters[version]_$date.zip";
	echo '<script language="Javascript">
		<!--
		document.location.replace("'.$www.'");
		// -->
		</script>';
}
//import backup
if($_POST['upload'])
{
	//todo
}
?>
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2">
		<i class="fa fa-save text-primary-m2"></i>  <?php echo T_('Sauvegardes de GestSup'); ?>
	</h1>
</div>
<?php echo DisplayMessage('info',T_("Il est recommandé d'automatiser la sauvegarde depuis le serveur, vous trouverez plus d'informations dans la ").'<a target="_blank" href="https://gestsup.fr/index.php?page=support&item1=backup#8">'.T_('documentation').'</a>'); ?>
<br />
<center>
	<button title="<?php echo T_("Télécharge une archive contenant les fichiers et la base de données de l'application"); ?>"  onclick='window.location.href="./index.php?page=admin&subpage=backup&action=backup"' type="submit" class="btn btn-primary">
		<i class="fa fa-download"></i>
		<?php echo T_('Lancer la sauvegarde'); ?>
	</button>
</center>