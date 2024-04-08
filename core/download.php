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
if(isset($_GET['download']))
{
	
	//get download properties
	$qry=$db->prepare("SELECT `real_filename`,`storage_filename` FROM `tattachments` WHERE `uid`=:uid");
	$qry->execute(array('uid' => $_GET['download']));
	$attachment=$qry->fetch();
	$qry->closeCursor();
	
	if(!empty($attachment))
	{
		$filepath='upload/ticket/'.$attachment['storage_filename'];
		if(file_exists($filepath))
		{
			header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.$attachment['real_filename'].'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			ob_clean();
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