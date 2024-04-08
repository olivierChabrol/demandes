<?php
################################################################################
# @Name : r.php
# @Description : add and modify user
# @Call : ./admin/profile.php
# @Parameters :  
# @Author : Flox
# @Create : 24/01/2020
# @Update : 26/05/2020
# @Version : 3.2.2
################################################################################

//initialize variables 
if(!isset($_GET['token'])) $_GET['token']=''; 
if(!isset($_COOKIE['token'])) $_COOKIE['token']=''; 

$_GET['token']=htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8');
$_GET['right']=htmlspecialchars($_GET['right'], ENT_QUOTES, 'UTF-8');
$_GET['profile']=htmlspecialchars($_GET['profile'], ENT_QUOTES, 'UTF-8');
$_GET['enable']=htmlspecialchars($_GET['enable'], ENT_QUOTES, 'UTF-8');
$_GET['user_id']=htmlspecialchars($_GET['user_id'], ENT_QUOTES, 'UTF-8');

if($_GET['token'] && $_GET['token']==$_COOKIE['token']) 
{
	if(isset($_GET['right']) && !empty($_GET['right']))
	{
		$right = $_GET['right'];
		$profile = $_GET['profile'];
		$enable = $_GET['enable'];
		//db connect
		require('../connect.php');
		$db->exec("UPDATE `trights` SET `$right`='$enable' WHERE `profile`='$profile'");
		//load parameters table
		$qry=$db->prepare("SELECT * FROM `tparameters`");
		$qry->execute();
		$rparameters=$qry->fetch();
		$qry->closeCursor();
		//log
		if($rparameters['log'])
		{
			require_once('../core/functions.php');

			if($profile==0) {$profile='technician';}
			if($profile==1) {$profile='poweruser';}
			if($profile==2) {$profile='user';}
			if($profile==3) {$profile='supervisor';}
			if($profile==4) {$profile='admin';}
			if($right=='admin' && $enable=='2')
			{
				logit('security', "Admin right added to new profile",$_GET['user_id']);
			} elseif($enable=='2') {
				logit('security', 'Right "'.$right.'" has been added for profile '.$profile,$_GET['user_id']);
			} else {
				logit('security', 'Right "'.$right.'" has been removed for profile '.$profile,$_GET['user_id']);
			}
		}
	}
} else {
	echo "ERROR : wrong token";
}
?>