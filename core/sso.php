<?php
################################################################################
# @Name : sso.php
# @Description : sso connect
# @Call : index.php
# @Parameters : 
# @Author : Flox
# @Create : 26/12/2019
# @Update : 29/01/2020
# @Version : 3.2.0
################################################################################

if($webserver=='IIS')
{
	$ssologin=explode('\\',$_SERVER['REMOTE_USER']);
	$qry = $db->prepare("SELECT `id` FROM `tusers` WHERE login=:login AND disable='0'");
	$qry->execute(array('login' => $ssologin[1]));
	$row=$qry->fetch();
	$qry->closeCursor();
} else { 
	$ssologin=explode('@',$_SERVER['REMOTE_USER']);
	$qry = $db->prepare("SELECT `id` FROM `tusers` WHERE login=:login AND disable='0'");
	$qry->execute(array('login' => $ssologin[0]));
	$row=$qry->fetch();
	$qry->closeCursor();
}
//check SSO user exist un GestSup user DB
if(!empty($row))
{
	echo '<i class="fa fa-spinner fa-spin text-info text-120"></i></i>&nbsp;Connexion SSO...';
	$_SESSION['user_id']=$row['id'];
	//redirect
	if($ruser['default_ticket_state']) $redirectstate=$ruser['default_ticket_state']; else $redirectstate=1;
	if($_GET['id']) {
		$www='./index.php?page=ticket&id='.$_GET['id'].'';
	} else {
		if($redirectstate=='meta_all')
		{
			$www='./index.php?page=dashboard&userid=%25&state=meta';
		} else {
			$www='./index.php?page=dashboard&userid='.$_SESSION['user_id'].'&state='.$redirectstate;
		}
	}
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='$www'
				}
				setTimeout('redirect()');
				-->
			</SCRIPT>";
} else {
	require('./login.php');
}
?>