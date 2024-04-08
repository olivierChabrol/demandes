<?php
################################################################################
# @Name : searchengine_ticket.php
# @Description : search engine in database tickets
# @call : /dashboard.php
# @parameters : keywords
# @Author : Flox
# @Create : 12/01/2011
# @Update : 26/05/2020
# @Version : 3.2.2
################################################################################

//initialize session variables
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';

$db_u_group=strip_tags($db->quote($_GET['u_group']));
$db_t_group=strip_tags($db->quote($_GET['t_group']));
$db_techread=strip_tags($db->quote($_GET['techread']));

//case when keywords contain '
$keywords = str_replace("'","\'",$keywords);

//keywords table space separation
$keyword=explode(" ",$keywords);

//count $keywords
$nbkeyword= sizeof($keyword);

//case meta state detect
if($_GET['state']=='meta'){$state="AND (tincidents.state=1 OR tincidents.state=2 OR tincidents.state=6)";} else {$state='';}

//case user company view
if($_GET['companyview']){$where_company="AND tusers.company='$ruser[company]'";} else {$where_company='';}

$select= "
		DISTINCT 
		tincidents.id,
		tincidents.type,
		tincidents.technician,
		tincidents.t_group,
		tincidents.title,
		tincidents.user,
		tincidents.u_group,
		tincidents.u_service,
		tincidents.u_agency,
		tincidents.sender_service,
		tincidents.date_create,
		tincidents.date_hope,
		tincidents.date_res,
		tincidents.time,
		tincidents.state,
		tincidents.priority,
		tincidents.criticality,
		tincidents.category,
		tincidents.subcat,
		tincidents.techread,
		tincidents.place,
		tincidents.asset_id
		";
$join='';

//special case limit service, with user agency and service
if($rparameters['user_limit_service']==1 && $rright['admin']==0 && $cnt_service!=0 && $cnt_agency!=0)
{
	//modify QRY 
	$where_service=preg_replace('/OR/', 'AND', $where_service, 1);
	$where_agency=str_replace('AND (', '', $where_agency);
	$where_service=str_replace('AND (',"AND ($where_agency OR ",$where_service);
}

if($nbkeyword==2)
{
	$from = "tincidents, tstates, tthreads, tusers";
	$where="
	tincidents.user=tusers.id AND
	tincidents.state=tstates.id AND
	tincidents.id=tthreads.ticket AND
	(
		tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[0]%' OR lastname LIKE '%$keyword[0]%') AND disable=0) 
	) AND (
		tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[1]%' OR lastname LIKE '%$keyword[1]%') AND disable=0)
	) AND (
		tincidents.user LIKE '$_POST[user]'
		AND	tincidents.disable='0'
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.technician LIKE '$_POST[technician]'
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.category LIKE '$_POST[category]'
		AND	tincidents.subcat LIKE '$_POST[subcat]'
		AND	tincidents.id LIKE '$_POST[ticket]'
		AND	tincidents.user LIKE '$_POST[userid]'
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE '$_POST[priority]'
		AND	tincidents.criticality LIKE '$_POST[criticality]'
		AND	tincidents.type LIKE '$_POST[type]'
		AND	tincidents.title LIKE '$_POST[title]'
	)
	$where_service
	$where_company
	$state
	AND tincidents.disable='0'
"; 
}
else if($nbkeyword==3)
{
	$from = "tincidents, tstates, tthreads, tusers";
	$where="
	tincidents.state=tstates.id AND
	tincidents.user=tusers.id AND
	tincidents.id=tthreads.ticket AND
	(
		tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[0]%' OR lastname LIKE '%$keyword[0]%') AND disable=0) 
	) AND (
		tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[1]%' OR lastname LIKE '%$keyword[1]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[2]%' OR 
		tincidents.description LIKE '%$keyword[2]%' OR 
		tthreads.text LIKE '%$keyword[2]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[2]%' OR lastname LIKE '%$keyword[2]%') AND disable=0)
	) AND (
		tincidents.user LIKE '$_POST[user]'
		AND	tincidents.disable='0'
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.technician LIKE '$_POST[technician]'
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.category LIKE '$_POST[category]'
		AND	tincidents.subcat LIKE '$_POST[subcat]'
		AND	tincidents.id LIKE '$_POST[ticket]'
		AND	tincidents.user LIKE '$_POST[userid]'
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE '$_POST[priority]'
		AND	tincidents.criticality LIKE '$_POST[criticality]'
		AND	tincidents.type LIKE '$_POST[type]'
		AND	tincidents.title LIKE '$_POST[title]'
	)
	$where_service
	$where_company
	$state
	AND tincidents.disable='0'
"; 
} 
else if($nbkeyword==4)
{
	$from = "tincidents, tstates, tthreads, tusers";
	$where="
	tincidents.user=tusers.id AND
	tincidents.state=tstates.id AND
	tincidents.id=tthreads.ticket AND
	(
		tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[0]%' OR lastname LIKE '%$keyword[0]%') AND disable=0) 
	) AND (
		tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[1]%' OR lastname LIKE '%$keyword[1]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[2]%' OR 
		tincidents.description LIKE '%$keyword[2]%' OR 
		tthreads.text LIKE '%$keyword[2]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[2]%' OR lastname LIKE '%$keyword[2]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[3]%' OR 
		tincidents.description LIKE '%$keyword[3]%' OR 
		tthreads.text LIKE '%$keyword[3]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[3]%' OR lastname LIKE '%$keyword[3]%') AND disable=0)
	) AND (
		tincidents.user LIKE '$_POST[user]'
		AND	tincidents.disable='0'
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.technician LIKE '$_POST[technician]'
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.category LIKE '$_POST[category]'
		AND	tincidents.subcat LIKE '$_POST[subcat]'
		AND	tincidents.id LIKE '$_POST[ticket]'
		AND	tincidents.user LIKE '$_POST[userid]'
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE '$_POST[priority]'
		AND	tincidents.criticality LIKE '$_POST[criticality]'
		AND	tincidents.type LIKE '$_POST[type]'
		AND	tincidents.title LIKE '$_POST[title]'
	)
	$where_service
	$where_company
	$state
	AND tincidents.disable='0'
"; 
} else if($nbkeyword==5)
{
	$from = "tincidents, tstates, tthreads, tusers";
	$where="
	tincidents.user=tusers.id AND
	tincidents.state=tstates.id AND
	tincidents.id=tthreads.ticket AND
	(
		tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[0]%' OR lastname LIKE '%$keyword[0]%') AND disable=0) 
	) AND (
		tincidents.title LIKE '%$keyword[1]%' OR 
		tincidents.description LIKE '%$keyword[1]%' OR 
		tthreads.text LIKE '%$keyword[1]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[1]%' OR lastname LIKE '%$keyword[1]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[2]%' OR 
		tincidents.description LIKE '%$keyword[2]%' OR 
		tthreads.text LIKE '%$keyword[2]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[2]%' OR lastname LIKE '%$keyword[2]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[3]%' OR 
		tincidents.description LIKE '%$keyword[3]%' OR 
		tthreads.text LIKE '%$keyword[3]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[3]%' OR lastname LIKE '%$keyword[3]%') AND disable=0)
	) AND (
		tincidents.title LIKE '%$keyword[4]%' OR 
		tincidents.description LIKE '%$keyword[4]%' OR 
		tthreads.text LIKE '%$keyword[4]%' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[4]%' OR lastname LIKE '%$keyword[4]%') AND disable=0)
	) AND (
		tincidents.user LIKE '$_POST[user]'
		AND	tincidents.disable='0'
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.technician LIKE '$_POST[technician]'
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.category LIKE '$_POST[category]'
		AND	tincidents.subcat LIKE '$_POST[subcat]'
		AND	tincidents.id LIKE '$_POST[ticket]'
		AND	tincidents.user LIKE '$_POST[userid]'
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE '$_POST[priority]'
		AND	tincidents.criticality LIKE '$_POST[criticality]'
		AND	tincidents.type LIKE '$_POST[type]'
		AND	tincidents.title LIKE '$_POST[title]'
	)
	$where_service
	$where_company
	$state
	AND tincidents.disable='0'
"; 
}
else
{
	$from = "tincidents, tstates, tthreads, tsubcat, tcategory, tassets, tusers";
	$where="
	tincidents.user=tusers.id AND
	tincidents.state=tstates.id AND
	tincidents.id=tthreads.ticket AND
	tincidents.subcat=tsubcat.id AND
	tincidents.category=tcategory.id AND
	tincidents.asset_id=tassets.id AND
	(
		tincidents.title LIKE '%$keyword[0]%' OR 
		tincidents.description LIKE '%$keyword[0]%' OR 
		tthreads.text LIKE '%$keyword[0]%' OR
		tsubcat.name LIKE '$keyword[0]' OR
		tassets.netbios LIKE '$keyword[0]' OR
		tcategory.name LIKE '$keyword[0]' OR
		tincidents.id = '$keyword[0]' OR
		tincidents.user LIKE (SELECT max(id) FROM tusers where (firstname LIKE '%$keyword[0]%' OR lastname LIKE '%$keyword[0]%') AND disable=0)
	) AND (
		tincidents.user LIKE '$_POST[user]'
		AND	tincidents.disable='0'
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.technician LIKE '$_POST[technician]'
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.category LIKE '$_POST[category]'
		AND	tincidents.subcat LIKE '$_POST[subcat]'
		AND	tincidents.id LIKE '$_POST[ticket]'
		AND	tincidents.user LIKE '$_POST[userid]'
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE '$_POST[priority]'
		AND	tincidents.criticality LIKE '$_POST[criticality]'
		AND	tincidents.type LIKE '$_POST[type]'
		AND	tincidents.title LIKE '$_POST[title]'
	)
	$where_service
	$state
	$where_company
	AND tincidents.disable='0'
	"; 
}	
?>