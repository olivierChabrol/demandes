<?php
################################################################################
# @Name : init_post.php
# @Description : init and secure all app var
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 08/11/2019
# @Update : 18/11/2019
# @Version : 3.2.0 p1
################################################################################

//POST var definition
$all_post_var=array(
	'date',
	'selectrow',
	'ticket',
	'technician',
	'technician_group',
	'title',
	'userid',
	'company',
	'user',
	'category',
	'subcat',
	'asset',
	'place',
	'service',
	'u_service',
	'sender_service',
	'agency',
	'date_create',
	'date_hope',
	'date_res',
	'date_start',
	'date_end',
	'state',
	'priority',
	'criticality',
	'type',
	'u_group',
	't_group',
	'Modifier',
	'Ajouter',
	'cat',
	'model',
	'ip',
	'wifi',
	'manufacturer',
	'name',
	'type',
	'confirm',
	'number',
	'allday'
);

//action on all post var
foreach($all_post_var as $post_var) {
	//init var
	if(!isset($_POST[$post_var])){$_POST[$post_var]='';}
	//secure var
	if($_GET['table']!='tservices') {$_POST[$post_var]=htmlspecialchars($_POST[$post_var], ENT_QUOTES, 'UTF-8');} // bug ldap sync service #4995
}
?>