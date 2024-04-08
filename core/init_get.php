<?php
################################################################################
# @Name : init_get.php
# @Description : init and secure all app var
# @Call : ./index.php
# @Parameters : 
# @Author : Flox
# @Create : 07/11/2019
# @Update : 15/05/2020
# @Version : 3.2.2
################################################################################

//GET var definition
$all_get_var=array(
	'page',
	'id',
	'userid',
	'action',
	'keywords',
	'technician',
	'u_group',
	't_group',
	'ticket',
	'category',
	'subcat',
	'asset',
	'place',
	'service',
	'sender_service',
	'agency',
	'cursor',
	'searchengine',
	'company',
	'user',
	'date_create',
	'date_res',
	'date_hope',
	'date_start',
	'date_end',
	'date_range',
	'view',
	'state',
	'priority',
	'title',
	'criticality',
	'type',
	'place',
	'way',
	'order',
	'techread',
	'companyview',
	'techgroup',
	'userkeywords',
	'rightkeywords',
	'procedurekeywords',
	'download',
	'download_file',
	'download_backup',
	'subpage',
	'ldap',
	'disable',
	'tab',
	'assetkeywords',
	'profileid',
	'findip',
	'findip2',
	'iface',
	'sn_internal',
	'ip',
	'netbios',
	'user',
	'model',
	'description',
	'date_stock',
	'date_end_warranty',
	'department',
	'location',
	'virtual',
	'warranty',
	'delimg',
	'asset',
	'event',
	'hide',
	'planning',
	'token',
	'lang',
	'viewid',
	'warranty',
	'user_id',
	'key',
	'procedure',
	'edit',
	'delete_file',
	'task_action',
	'task_id',
	'threaddelete',
	'threadedit',
	'lock_thread',
	'unlock_thread',
	'cat',
	'editcat',
	'edituserid',
	'table',
	'ldaptest',
	'deleteavailability',
	'deleteavailabilitydep',
	'delete_imap_service',
	'deletequestion',
	'value',
	'profile',
	'object',
	'install_update',
	'deleteview',
	'attachmentdelete',
	'delete_assoc_service',
	'delete_assoc_agency',
	'fromnew',
	'iptoping',
	'scan',
	'month',
	'year',
	'userid',
	'time',
	'post',
    'request',
    'request_list'
);

//action on all get var
foreach($all_get_var as $get_var) {
	//init var
	if(!isset($_GET[$get_var])){$_GET[$get_var]='';}
	//secure var
    $_GET[$get_var]=htmlspecialchars($_GET[$get_var], ENT_QUOTES, 'UTF-8');
}
//check page 
$page_white_list=array('dashboard','ticket','preview_mail','asset_list','asset','asset_stock','procedure','calendar','project','stat','admin','admin/user','plugins/availability/index','changelog','register','forgot_pwd','test', 'request', 'request_list');
if(!in_array($_GET['page'],$page_white_list)) {$_GET['page']='';} 
//check subpage 
$subpage_white_list=array('system','infos','pwd_recovery','backup','group','profile','update','user','parameters','phpinfos','list','log');
if(!in_array($_GET['subpage'],$subpage_white_list)) {$_GET['subpage']='';} 
?>