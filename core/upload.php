<?php
################################################################################
# @Name : upload.php
# @Description : upload attached files
# @Call : ticket.php
# @Parameters :
# @Author : Flox
# @Create : 12/08/2013
# @Update : 28/05/2020
# @Version : 3.2.2
################################################################################

session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    // Redirection ou refus d'accès
    header("HTTP/1.1 403 Forbidden");
    exit("Accès non autorisé.");
}

//initialize variables
if(!isset($_FILES['file']['name'])) {$_FILES['file']['name']='';}

// 1. Sécurisation stricte de l'ID du ticket
$ticket_id = (int)$_GET['id'];
if ($ticket_id <= 0) {
    exit("ID de ticket invalide.");
}

//create ticket directory if not exist
if(!is_dir("./upload/ticket"))  {mkdir('./upload/ticket', 0755, true);}

if($_FILES['file']['name'] && $_GET['id'])
{
	$real_filename=preg_replace("/[^A-Za-z0-9\_\-\.\s+]/", '', $_FILES['file']['name']);
	//$real_filename=$_FILES['file']['name'];
    if(CheckFileExtension($real_filename)==true) {

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $_FILES['file']['tmp_name']);

		// Autoriser uniquement une liste blanche (ex: PDF et images basiques)
		$allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
		if (!in_array($mimeType, $allowedMimes)) {
			exit("Type de fichier non autorisé.");
		}

		// 4. Contrôle du contenu AVANT le déplacement sur le disque
        $file_content = file_get_contents($_FILES['file']['tmp_name']);
        if(preg_match('{\<\?php}i', $file_content) || preg_match('/system\(/i', $file_content)) {
            exit(DisplayMessage('error',T_("Fichier interdit")));
        }

		//create upload folder if not exist
        $target_folder='./upload/ticket/';
		//generate storage filename
		$storage_filename=$_GET['id'].'_'.md5(uniqid().'_'.$real_filename);
		if(move_uploaded_file($_FILES['file']['tmp_name'], $target_folder.$storage_filename))
		{
			$uid=md5(uniqid());
			$qry=$db->prepare("INSERT INTO `tattachments` (`uid`,`ticket_id`,`storage_filename`,`real_filename`) VALUES (:uid,:ticket_id,:storage_filename,:real_filename)");
			if($qry->execute(array('uid' => $uid,'ticket_id' => $_GET['id'],'storage_filename' => $storage_filename,'real_filename' => $real_filename))){
				include "change_state_on_upload.php";
			}
		} else {
			echo DisplayMessage('error',T_("Transfert impossible"));
		}
    } else {
		echo DisplayMessage('error',T_("Fichier interdit"));
		if($rparameters['log'] && is_numeric($_GET['id'])) {logit('security','Blacklisted file "'.$real_filename.'" blocked on ticket '.$_GET['id'],$_SESSION['user_id']);}
	}
}
?>
