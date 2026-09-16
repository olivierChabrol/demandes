<?php
################################################################################
# @Name : download.php
# @Description : download file
# @Call : index.php, ticket.php
# @Parameters : download uid
# @Author : Flox
# @Create : 17/12/2019
# @Update : 11/02/2020
# @Version : 3.2.2 p1
################################################################################
// On s'assure que l'utilisateur est bien connecté (au cas où ce fichier serait appelé hors de index.php)
if(!isset($_SESSION['user_id'])) {
    exit('ERROR : Unauthorized access');
}

if(isset($_GET['download']))
{
	// Nettoyage de l'UID (sécurité de base, on n'accepte que l'alphanumérique)
    $uid = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['download']);
	
	//get download properties
	$qry=$db->prepare("SELECT `real_filename`,`storage_filename` FROM `tattachments` WHERE `uid`=:uid");
	$qry->execute(array('uid' => $uid));
	$attachment=$qry->fetch();
	$qry->closeCursor();
	
	if(!empty($attachment))
	{
		/* 
         * AJOUT RECOMMANDÉ : Contrôle d'accès (IDOR)
         * Ici, il faudrait idéalement appeler la logique de main.php ou une fonction
         * pour vérifier que $_SESSION['user_id'] a les droits sur $attachment['ticket_id'].
         * Si ce n'est pas le cas -> exit('ERROR : Access denied');
         */

		//$filepath='upload/ticket/'.$attachment['storage_filename'];
		$storage_filename = basename($attachment['storage_filename']);
        $real_filename = basename($attachment['real_filename']);
		$filepath = 'upload/ticket/' . $storage_filename;
		
		if(file_exists($filepath))
		{
			// Ajout des en-têtes de sécurité
            header('X-Content-Type-Options: nosniff');
			header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$real_filename.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			// Sécurisation du nettoyage du buffer de sortie
            if (ob_get_level()) { ob_clean(); }
            flush(); // Flush system output buffer
            readfile($filepath);
            die();
		} else {
			echo 'ERROR : File not exist';
			die;
		}
	} else {
		echo 'ERROR : invalid file';
		die;
	}
} else {
	echo 'ERROR : Download failed';
	die;
}
?>