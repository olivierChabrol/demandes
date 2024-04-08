<?php
################################################################################
# @Name : admin.php
# @Description : admin parent page check right to admin part
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 12/01/2011
# @Update : 27/08/2020
# @Version : 3.2.4
################################################################################

//default settings
if($_GET['subpage']=='') $_GET['subpage']='user';
if($_GET['subpage']=='user')
if($_GET['profileid']=='') if($_GET['subpage']=='user') $_GET['profileid'] = '%';
if($_GET['subpage']=='profile' && $_GET['profileid']=='') $_GET['profileid']=0;

//check rights for admin page
if($rright['admin']){include ('./admin/'.$_GET['subpage'].'.php');}
elseif($rright['admin_groups'] && $_GET['subpage']=='group'){include ('./admin/'.$_GET['subpage'].'.php');}
elseif($rright['admin_lists'] && $_GET['subpage']=='list') {include ('./admin/'.$_GET['subpage'].'.php');}
elseif($rright['admin_user_view'] && $_GET['subpage']=='user') {include ('./admin/'.$_GET['subpage'].'.php');} //case to allow superuser to delete personal views
else {echo DisplayMessage('error',T_("Vous n'avez pas accès au menu administration, contacter votre administrateur"));}
?>