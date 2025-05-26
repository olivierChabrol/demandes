<?php
################################################################################
# @Name : dashboard.php
# @Description : Display tickets list
# @Call : /index.php
# @Parameters :
# @Author : Flox
# @Create : 17/07/2009
# @Update : 11/09/2020
# @Version : 3.2.4 p3
################################################################################

//!\ Ajouter par nous - A l'affichage de la page de dashboard on execute le script qui gère les rappel de demandes non traitées
require_once("./alerte_demande.php");

//initialize variables
if(!isset($asc)) $asc = '';
if(!isset($late)) $late= '';
if(!isset($from)) $from='';
if(!isset($filter)) $filter='';
if(!isset($col)) $col='';
if(!isset($view)) $view='';
if(!isset($nkeyword)) $nkeyword='';
if(!isset($rowlastname)) $rowlastname='';
if(!isset($resultcriticality['color'])) $resultcriticality['color']= '';
if(!isset($displayusername)) $displayusername= '';
if(!isset($displaytechname)) $displaytechname= '';
if(!isset($u_group)) $u_group= '';
if(!isset($t_group)) $t_group= '';
if(!isset($techread)) $techread= '';
if(!isset($userread)) $userread= '';
if(!isset($start_page)) $start_page= '';
if(!isset($cursor)) $cursor= '';
if(!isset($selectcursor)) $selectcursor= '';
if(!isset($date_start)) $date_start= '';
if(!isset($date_end)) $date_end= '';

//default values
if (!isset($_GET['ticket'])) $_GET['ticket']='';
if (!isset($_GET['technician'])) $_GET['technician']='';
if (!isset($_GET['title'])) $_GET['title']='';
if (!isset($_GET['company'])) $_GET['company']='';
if (!isset($_GET['address1'])) $_GET['address1']='';
if (!isset($_GET['user'])) $_GET['user']='';
if (!isset($_GET['category'])) $_GET['category']='';
if (!isset($_GET['subcat'])) $_GET['subcat']='';
if (!isset($_GET['asset'])) $_GET['asset']='';
if (!isset($_GET['place'])) $_GET['place']='';
if (!isset($_GET['service'])) $_GET['service']='';
if (!isset($_GET['sender_service'])) $_GET['sender_service']='';
if (!isset($_GET['agency'])) $_GET['agency']='';
if (!isset($_GET['date_create'])) $_GET['date_create']='';
if (!isset($_GET['time'])) $_GET['time']='';
if (!isset($_GET['date_hope'])) $_GET['date_hope']='';
if (!isset($_GET['date_res'])) $_GET['date_res']='';
if (!isset($_GET['date_start'])) $_GET['date_start']='';
if (!isset($_GET['date_end'])) $_GET['date_end']='';
if (!isset($_GET['state'])) $_GET['state']='';
if (!isset($_GET['priority'])) $_GET['priority']='';
if (!isset($_GET['criticality'])) $_GET['criticality']='';
if (!isset($_GET['type'])) $_GET['type']='';
if (!isset($_GET['u_group'])) $_GET['u_group']='';
if (!isset($_GET['t_group'])) $_GET['t_group']='';

//get value is for filter case
if(!isset($_POST['date'])) $_POST['date']= '';
if(!isset($_POST['selectrow'])) $_POST['selectrow']= '';
if(!isset($_POST['ticket'])) $_POST['ticket']=$_GET['ticket'];
if(!isset($_POST['technician'])) $_POST['technician']=$_GET['technician'];
if(!isset($_POST['title'])) $_POST['title']=$_GET['title'];
if(!isset($_POST['ticket'])) $_POST['ticket']= '';
if(!isset($_POST['userid'])) $_POST['userid']= '';
if(!isset($_POST['company'])) $_POST['company']= $_GET['company'];
/* /!\ new */if(!isset($_POST['address1'])) $_POST['address1']= $_GET['address1'];
if(!isset($_POST['user'])) $_POST['user']= $_GET['user'];
if(!isset($_POST['category'])) $_POST['category']=$_GET['category'];
if(!isset($_POST['subcat'])) $_POST['subcat']=$_GET['subcat'];
if(!isset($_POST['asset'])) $_POST['asset']=$_GET['asset'];
if(!isset($_POST['place'])) $_POST['place']=$_GET['place'];
if(!isset($_POST['service'])) $_POST['service']=$_GET['service'];
if(!isset($_POST['sender_service'])) $_POST['sender_service']=$_GET['sender_service'];
if(!isset($_POST['agency'])) $_POST['agency']=$_GET['agency'];
if(!isset($_POST['date_create'])) $_POST['date_create']=$_GET['date_create'];
if(!isset($_POST['time'])) $_POST['time']=$_GET['time'];
if(!isset($_POST['date_hope'])) $_POST['date_hope']=$_GET['date_hope'];
if(!isset($_POST['date_res'])) $_POST['date_res']=$_GET['date_res'];
if(!isset($_POST['date_start'])) $_POST['date_start']=$_GET['date_start'];
if(!isset($_POST['date_end'])) $_POST['date_end']=$_GET['date_end'];
if(!isset($_POST['state'])) $_POST['state']=$_GET['state'];
if(!isset($_POST['priority'])) $_POST['priority']=$_GET['priority'];
if(!isset($_POST['criticality'])) $_POST['criticality']=$_GET['criticality'];
if(!isset($_POST['type'])) $_POST['type']=$_GET['type'];
if(!isset($_POST['u_group'])) $_POST['u_group']=$_GET['u_group'];
if(!isset($_POST['t_group'])) $_POST['t_group']=$_GET['t_group'];

//init post var
require('core/init_post.php');

//default values
if($techread=='') $techread='%';
if($userread=='') $userread='%';
if($state=='')$state='%';
if($_GET['way']=='') $_GET['way']='DESC';
if($_GET['category']=='') $_GET['category']= '%';
if($_GET['t_group']=='') $_GET['t_group']= '%';
if($_GET['u_group']=='') $_GET['u_group']= '%';
if($_GET['subcat']=='') $_GET['subcat']= '%';
if($_GET['asset']=='') $_GET['asset']= '%';
if($_GET['place']=='') $_GET['place']= '%';
if($_GET['cursor']=='') $_GET['cursor']='0';
if($_GET['techread']=='') $_GET['techread']='%';
if(!isset($_GET['userread']) || $_GET['userread']=='') $_GET['userread']='%';
if($_GET['type']=='') {$_GET['type']='%'; }

if($_POST['criticality']=='') $_POST['criticality']= '%';
if($_POST['priority']=='') $_POST['priority']='%';
if($_POST['state']=='') {$_POST['state']='%'; }
if($_POST['type']=='') {$_POST['type']='%'; }

//avoid page 2 bug when technician switch
if(($_POST['technician']!=$_GET['technician']) && ($_GET['cursor']!=0)) {$_GET['cursor']=0;}

//default values check user profil parameters

//if admin user
if($_SESSION['profile_id']==0 || $_SESSION['profile_id']==4)
{
	if($_POST['technician']=='') $_POST['technician']= $_GET['userid'];
	if($_POST['user']=='') $_POST['user']= '%';
} else {
	if($_POST['user']=='') $_POST['user']= $_GET['userid'];
	if($_POST['technician']=='') $_POST['technician']= '%';
}

// check if user have right to display all user ticket
if(($_POST['user']=='%' || $_POST['user']=='%25') && $_POST['technician']!=$_SESSION['user_id']&& !$rright['side_all']&&!$_GET['companyview']&&!$_GET['techgup']) {$_POST['user']=$_SESSION['user_id'];}

if($_POST['date_create']=='') $_POST['date_create']= '%';
if($_POST['date_start']=='') $_POST['date_start']= '%';
if($_POST['time']=='') $_POST['time']= '%';
if($_POST['date_res']=='') $_POST['date_res']= '%';
if($_POST['sender_service']=='') $_POST['sender_service']= '%';
if($_POST['title']=='') $_POST['title']= '%';
if($_POST['ticket']=='') $_POST['ticket']= '%';
if($_POST['userid']=='') $_POST['userid']= '%';
if($_POST['category']=='') $_POST['category']= '%';
if($_POST['subcat']=='') $_POST['subcat']= '%';
if($_POST['asset']=='') $_POST['asset']= '%';
if($_POST['place']=='') $_POST['place']= '%';
if($_POST['service']=='') $_POST['service']= '%';
if($_POST['agency']=='') $_POST['agency']= '%';
if($_POST['company']=='') $_POST['company']= '%';
/* /!\ new */if($_POST['address1']=='') $_POST['address1']= '%';

//technician and technician group separate
if(substr($_POST['technician'], 0, 1) =='G')
{
 	$t_group = explode("_", $_POST['technician']);
	$t_group=$t_group[1];
	$_GET['t_group']=$t_group;
	$_POST['technician']='%';
}
//user and user group separate
if(substr($_POST['user'], 0, 1) =='G')
{
 	$u_group = explode("_", $_POST['user']);
	$u_group=$u_group[1];
	$_GET['u_group']=$u_group;
	$_POST['user']='%';
}
//special case to filter technician group is send
if($rright['side_your_tech_group'] && $_GET['techgroup']){$_POST['technician']="%";}

if($_GET['order']=='')
{
	$_GET['order']='date_start';
	$_GET['way']='ASC';
}

//select order
if(($filter=='on' || $_GET['order']=='')){
    if($ruser['dashboard_ticket_order'])
	{
		$_GET['order']=$ruser['dashboard_ticket_order'];
		if($ruser['dashboard_ticket_order']=='tincidents.date_hope') {$_GET['way']='ASC';} else {$_GET['way']='DESC';} #3697
	} else {
		//modify order to resolution date for state 3 and 4
		if(preg_match("#tstates.number, tincidents.date_hope#i", "'.$rparameters[order].'") && (($_GET['state']==3) || ($_GET['state']==4)))
		{
			$_GET['order']='tincidents.date_res';
			$_GET['way']='DESC';
		} else {
			$_GET['order']=$rparameters['order'];
		}
	}
	$_GET['order']=str_replace(' ','', $_GET['order']);
}

$db_order=strip_tags($db->quote($_GET['order']));
$db_order=str_replace("'","",$db_order);
if($_GET['way']=='ASC' || $_GET['way']=='DESC') {$db_way=$_GET['way'];} else {$db_way='DESC';}
$db_state=strip_tags($db->quote($_GET['state']));
$db_viewid=strip_tags($db->quote($_GET['viewid']));
$db_techgroup=strip_tags($db->quote($_GET['techgroup']));
$db_u_group=strip_tags($db->quote($_GET['u_group']));
$db_t_group=strip_tags($db->quote($_GET['t_group']));
$db_techread=strip_tags($db->quote($_GET['techread']));
$db_userread=strip_tags($db->quote($_GET['userread']));
$db_keywords=strip_tags($db->quote($_GET['keywords']));
if(is_numeric($_GET['cursor'])) {$db_cursor=$_GET['cursor'];} else {$db_cursor=0;}

//meta state generation
if($_GET['state']=='meta')
{
	$state='AND	(';
	$qry=$db->prepare("SELECT `id` FROM `tstates` WHERE `meta`='1'");
	$qry->execute();
	while($row=$qry->fetch())
	{
		$state.='tincidents.state LIKE '.$row['id'].' OR ';
	}
	$qry->closeCursor();
	$state.=' 1=0)';

    //change order in this case
    if($_GET['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_create') {$_GET['order']='tincidents.priority, tincidents.criticality, tincidents.date_create';}
    if($_GET['order']=='tstates.number, tincidents.priority, tincidents.criticality, tincidents.date_hope') {$_GET['order']='tincidents.priority, tincidents.criticality, tincidents.date_hope';}
    if($_GET['order']=='tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality') {$_GET['order']='tincidents.date_hope, tincidents.priority, tincidents.criticality';}
    if($_GET['order']=='tstates.number, tincidents.date_hope, tincidents.criticality, tincidents.priority') {$_GET['order']='tincidents.date_hope, tincidents.criticality, tincidents.priority';}
    if($_GET['order']=='tstates.number, tincidents.criticality, tincidents.date_hope, tincidents.priority') {$_GET['order']='tincidents.criticality, tincidents.date_hope, tincidents.priority';}
} else {
    $state='AND	tincidents.state LIKE \''.$_POST['state'].'\'';
}
$url_post_parameters="userid=$_GET[userid]&state=$_POST[state]&viewid=$_GET[viewid]&ticket=$_POST[ticket]&technician=$_POST[technician]&techgroup=$_GET[techgroup]&user=$_POST[user]&sender_service=$_POST[sender_service]&category=$_POST[category]&subcat=$_POST[subcat]&asset=$_POST[asset]&title=$_POST[title]&date_create=$_POST[date_create]&priority=$_POST[priority]&criticality=$_POST[criticality]&place=$_POST[place]&service=$_POST[service]&agency=$_POST[agency]&companyview=$_GET[companyview]&type=$_POST[type]&company=$_POST[company]&keywords=$keywords&view=$_GET[view]&date_start=$_POST[date_start]&date_end=$_POST[date_end]&time=$_POST[time]&techread=$_GET[techread]";
$url_post_parameters=preg_replace('/%/','%25',$url_post_parameters);
//special case redirect to all ticket if date create is filtered on activity view
if(!isset($today)) {$today=date('Y-m-d');}
if($_GET['view']=='activity' && $_POST['date_create']!=$today && $_POST['date_create']!='current' && $_POST['date_create']!='%')
{
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&userid=%25&state=%25&ticket=%25&technician=%25&user=%25&category=%25&subcat=%25&title=%25&date_create=$_POST[date_create]&priority=%25&criticality=%25&company=%'
				}
				setTimeout('redirect()',0);
				-->
		</SCRIPT>";
}
if($_GET['view']=='activity' && $_POST['date_res']!=$today && $_POST['date_res']!='current' && $_POST['date_res']!='%')
{
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&userid=%25&state=%25&ticket=%25&technician=%25&user=%25&category=%25&subcat=%25&title=%25&date_res=$_POST[date_res]&priority=%25&criticality=%25&company=%'
				}
				setTimeout('redirect()',0);
				-->
		</SCRIPT>";
}

//load in url parameter of filter, for using back button of browser on ticket page
if(
	($_POST['ticket']!='%' && $_GET['ticket']=='%') ||
	($_POST['technician']!='%' && $_GET['technician']=='%') ||
	($_POST['user']!='%' && $_GET['user']=='%') ||
	($_POST['sender_service']!='%' && $_GET['sender_service']=='%') ||
	($_POST['category']!='%' && $_GET['category']=='%') ||
	($_POST['subcat']!='%' && $_GET['subcat']=='%') ||
	($_POST['asset']!='%' && $_GET['asset']=='%') ||
	($_POST['title']!='%' && $_GET['title']=='%') ||
	($_POST['priority']!='%' && $_GET['priority']=='%') ||
	($_POST['criticality']!='%' && $_GET['criticality']=='%') ||
	($_POST['place']!='%' && $_GET['place']=='%') ||
	($_POST['service']!='%' && $_GET['service']=='%') ||
	/*/!\new*/($_POST['address1']!='%' && $_GET['address1']=='%') ||
	($_POST['agency']!='%' && $_GET['agency']=='%') ||
	($_POST['type']!='%' && $_GET['type']=='%') ||
	($_POST['company']!='%' && $_GET['company']=='%') ||
	($_GET['date_range']==1)
)
{
	$reload=1;
	//redirect
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='./index.php?page=dashboard&$url_post_parameters'
				}
				setTimeout('redirect()',0);
				-->
		</SCRIPT>";
	echo '<i class="fa fa-spinner fa-spin text-info text-120"></i>&nbsp;'.T_('Chargement...');
	exit;
} else {$reload=0;}

///// SQL QUERY
	//Date conversion for filter line
	if($_POST['date_create']!='%')
	{
		$date_create=$_POST['date_create'];
		$find='/';
		$find= strpos($date_create, $find);
		if($find!=false)
		{
			$date_create=explode("/",$date_create);
			if(isset($date_create[2]) && isset($date_create[1]) && isset($date_create[0])){$_POST['date_create']="$date_create[2]-$date_create[1]-$date_create[0]";}
		}
	}
    if($_POST['date_start']!='%')
    {
        $date_start = $_POST['date_start'];
        $find='/';
        $find= strpos($date_start, $find);
        if($find!=false)
        {
            $date_start=explode("/",$date_start);
            if(isset($date_start[2]) && isset($date_start[1]) && isset($date_start[0])){$_POST['date_start']="$date_start[2]-$date_start[1]-$date_start[0]";}
        }
    }
	if($_POST['date_hope']!='%')
	{
		$date_hope=$_POST['date_hope'];
		$find='/';
		$find= strpos($date_hope, $find);
		if($find!=false)
		{
			$date_hope=explode("/",$date_hope);
			if(isset($date_hope[2]) && isset($date_hope[1]) && isset($date_hope[0])){$_POST['date_hope']="$date_hope[2]-$date_hope[1]-$date_hope[0]";}
		}
	}
	if($_POST['date_res']!='%')
	{
		$date_res=$_POST['date_res'];
		$find='/';
		$find= strpos($date_res, $find);
		if($find!=false)
		{
			$date_res=explode("/",$date_res);
			if(isset($date_res[2]) && isset($date_res[1]) && isset($date_res[0])){$_POST['date_res']="$date_res[2]-$date_res[1]-$date_res[0]";}
		}
	}
	if($keywords && $reload==0)
	{
		include "./core/searchengine_ticket.php";
	} else {
		//escape special char and secure string before database insert
		$db_sender_service=strip_tags($db->quote($_POST['sender_service']));
		$db_category=strip_tags($db->quote($_POST['category']));
		$db_subcat=strip_tags($db->quote($_POST['subcat']));
		$db_asset=strip_tags($db->quote($_POST['asset']));
		$db_userid=strip_tags($db->quote($_POST['userid']));
		$db_user=strip_tags($db->quote($_POST['user']));
		$db_ticket=strip_tags($db->quote($_POST['ticket']));
		$db_priority=strip_tags($db->quote($_POST['priority']));
		$db_criticality=strip_tags($db->quote($_POST['criticality']));
		$db_type=strip_tags($db->quote($_POST['type']));
		$db_technician=strip_tags($db->quote($_POST['technician']));
		$db_title=strip_tags($db->quote($_POST['title']));
		$db_title=str_replace("'","",$db_title);
		$db_u_group=strip_tags($db->quote($_GET['u_group']));
		$db_t_group=strip_tags($db->quote($_GET['t_group']));
		$db_techread=strip_tags($db->quote($_GET['techread']));
		$db_userread=strip_tags($db->quote($_GET['userread']));

		//build SQL query
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
		tincidents.techread_date,
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
		tincidents.userread,
		tincidents.place,
		tincidents.asset_id,
        tincidents.date_start
		";
		$from="tincidents";
		$join='LEFT JOIN tstates ON tincidents.state=tstates.id ';
		$where="
		tincidents.disable='0'
		AND	tincidents.sender_service LIKE $db_sender_service
		AND	tincidents.u_group LIKE $db_u_group
		AND	tincidents.t_group LIKE $db_t_group
		AND	tincidents.techread LIKE $db_techread
		AND	tincidents.userread LIKE $db_userread
		AND	tincidents.category LIKE $db_category
		AND	tincidents.subcat LIKE $db_subcat
		AND	tincidents.asset_id LIKE $db_asset
		AND	tincidents.id LIKE $db_ticket
		AND	tincidents.user LIKE $db_userid
		AND tincidents.date_hope LIKE '$_POST[date_hope]%'
		AND	tincidents.priority LIKE $db_priority
		AND	tincidents.criticality LIKE $db_criticality
		AND	tincidents.type LIKE $db_type
		AND	tincidents.title LIKE '%$db_title%'
		$state
		";
		//special case to display ticket where technician is user associated to the ticket, and service limit is enabled
		if($_SESSION['profile_id']==0 && $rparameters['user_limit_service']==1 && $_GET['userid']!='%')
		{
			$where.="AND tincidents.user LIKE $db_user AND (tincidents.technician LIKE $db_technician OR tincidents.user LIKE $db_technician) ";
		} else {
			//!\OLD - $where.="AND tincidents.user LIKE $db_user AND tincidents.technician LIKE $db_technician ";
			/*/!\AJOUTER PAR NOS SOINS*/
			if($_SESSION['profile_id']==4){$where.="AND tincidents.user LIKE $db_user AND tincidents.technician LIKE $db_technician" ;}
			else { $where.="AND (tincidents.user LIKE $db_user OR (dticket_observers.id_observer LIKE ".$_SESSION['user_id']." AND tincidents.user NOT LIKE ".$_SESSION['user_id'].")) AND tincidents.technician LIKE $db_technician" ;}
			/* Fin ajout */
		}

		/*/!\AJOUTER PAR NOS SOINS */	$join.='LEFT JOIN dticket_observers ON tincidents.id=dticket_observers.id ';
		//special case to filter query
		if($rparameters['user_company_view']==1 && $rright['side_company'])
		{
			//check if company table is not empty, before add join
			$qry=$db->prepare("SELECT count(id) FROM `tcompany`");
			$qry->execute();
			$row=$qry->fetch();
			$qry->closeCursor();
			if($row[0]>1)
			{
				$join.='LEFT JOIN tusers ON tincidents.user=tusers.id ';
				$where.="AND tusers.company='$ruser[company]' ";
			}
		}
		//special case to filter query when user company right is enable
		if($rright['dashboard_col_company'])
		{
			$db_company=strip_tags($db->quote($_POST['company']));
			if(!preg_match('#LEFT JOIN tusers#',$join)) {$join.='LEFT JOIN tusers ON tincidents.user=tusers.id ';}
			$join.='LEFT JOIN tcompany ON tusers.company=tcompany.id';
			$where.="AND tcompany.id LIKE $db_company ";
		}

		//special case where user have service and agency
		if(($rparameters['user_agency']==1 && ($_GET['userid']=='%' || $_GET['userid']=='0'))&&($rparameters['user_limit_service']==1 && ($_GET['userid']=='%' || $_GET['userid']=='0'))&& $cnt_agency!=0 && $cnt_service!=0)
		{
			$where.= "$where_agency $where_service $parenthese2" ;
		} else {
			//special case query when agency parameter is enable to display agency ticket and user tickets
			if($rparameters['user_agency']==1 && ($_GET['userid']=='%' || $_GET['userid']=='0')){$where.=$where_agency;} else {$where.=$where_agency_your;}

			//special case query when service parameter is enable to display service ticket and user tickets
			if($rparameters['user_limit_service']==1 && ($_GET['userid']=='%' || $_GET['userid']=='0')){$where.=$where_service;} else {$where.=$where_service_your;}
		}

		//special case to filter query when place view is selected
		if($rparameters['ticket_places']==1){
			$db_place=strip_tags($db->quote($_POST['place']));
			$where.="AND tincidents.place LIKE $db_place ";
		}

		//special case to filter query when service parameter is enable
		if($rright['dashboard_col_service']){
			$db_service=strip_tags($db->quote($_POST['service']));
			$where.="AND tincidents.u_service LIKE $db_service ";
		}

		//!\ new - on applique le filtre sur le site
		$db_address1=strip_tags($db->quote($_POST['address1']));
		$where.="AND tusers.address1 LIKE $db_address1 ";
		//!\fin new

		//special case to filter query
		if($rright['dashboard_col_time']){
			$db_time=strip_tags($db->quote($_POST['time']));
			$where.="AND tincidents.time LIKE $db_time ";
		}

		//special case to filter query when agency parameter is enable
		if($rright['dashboard_col_agency']){
			$db_agency=strip_tags($db->quote($_POST['agency']));
			$where.="AND tincidents.u_agency LIKE $db_agency ";
		}

		//special case to filter query for activities tickets
		if($_GET['view']=='activity')
		{
			//case of range period selected else today tickets
			if($_POST['date_start'] && $_POST['date_end'])
			{
				if(preg_match('#/#', $_POST['date_start'])) //convert date only if slash detected
				{
					//convert date format
					$_POST['date_start']=DateTime::createFromFormat('d/m/Y', $_POST['date_start']);
					$_POST['date_start']=$_POST['date_start']->format('Y-m-d');
					$_POST['date_end']=DateTime::createFromFormat('d/m/Y', $_POST['date_end']);
					$_POST['date_end']=$_POST['date_end']->format('Y-m-d');
				}
				$from="tincidents,tthreads,tstates";
				$join='';
				$where.="AND tincidents.id=tthreads.ticket AND tincidents.state=tstates.id ";
				$where.="AND ((tincidents.date_create BETWEEN '$_POST[date_start] 00:00:00' AND '$_POST[date_end] 23:59:59') OR ((tincidents.date_res BETWEEN '$_POST[date_start] 00:00:00' AND '$_POST[date_end] 23:59:59') AND tincidents.date_res!='0000-00-00 00:00:00') OR (tthreads.date BETWEEN '$_POST[date_start] 00:00:00' AND '$_POST[date_end] 23:59:59' AND tthreads.type=0))";
			} else {
				$from="tincidents,tthreads,tstates";
				$join='';
				$where.="AND tincidents.id=tthreads.ticket AND tincidents.state=tstates.id ";
				$where.="AND (tincidents.date_create LIKE '$_POST[date_create]%' OR tincidents.date_res LIKE '$_POST[date_create]%' OR (tthreads.date LIKE '$_POST[date_create]%' AND tthreads.type=0))";
            }
			//case company col
			if($rright['dashboard_col_company'])
			{
				$from.=",tcompany,tusers";
				$where.="AND tincidents.user=tusers.id AND tusers.company=tcompany.id ";
				$where.="AND tcompany.id LIKE '$_POST[company]' ";
			}
			//case side_company and activity view
			if($rright['side_company'] && !preg_match('#tusers#',$from))
			{
				$from.=',tusers';
				$where.='AND tincidents.technician=tusers.id ';
			}
		} else {
			$where.="AND tincidents.date_create LIKE '$_POST[date_create]%' AND tincidents.date_res LIKE '$_POST[date_res]%'";
            $where .= "AND tincidents.date_start LIKE '%$_POST[date_start]%'";

        }
		//special case to filter technician group is send
		if($rright['side_your_tech_group'] && $_GET['techgroup'])
		{
			$where.="AND tincidents.t_group='$_GET[techgroup]' ";
		}
	}

	if($rparameters['debug'])
	{
		$where_debug=str_replace("AND", "AND <br />",$where);
		$where_debug=str_replace("OR", "OR <br />",$where_debug);
		$join_debug =str_replace("LEFT", "<br />LEFT",$join);
		echo "
		<b><u>DEBUG MODE :</u></b><br />
		<b>SELECT</b> $select<br />
		<b>FROM</b> $from
		$join_debug<br />
		<b>WHERE</b> <br />
		$where_debug<br />
		<b>ORDER BY</b> $db_order $db_way<br />
		<b>LIMIT</b> $db_cursor,$rparameters[maxline]<br />
		<b>VAR:</b>
		POST_keywords=$_POST[keywords] GET_keywords=$db_keywords |
		POST_state=$_POST[state] GET_state=$_GET[state] state=$state |
		POST_date_create=$_POST[date_create] GET_date_create=$_GET[date_create] |
		cnt_service=$cnt_service  |
		GET_view=$_GET[view] |
		POST_date_start=$_POST[date_start] |
		POST_date_end=$_POST[date_end]
		";

		if($user_services) {echo ' user_services=';foreach($user_services as $value) {echo $value.' ';}}
		echo '| cnt_agency='.$cnt_agency;
		if($user_agencies) {echo ' user_agencies=';foreach($user_agencies as $value) {echo $value.' ';}}

	}
	if($reload==0) //avoid double query for reload parameters in url optimization for large database
	{
        $queryForDownload="
		SELECT SQL_CALC_FOUND_ROWS $select
		FROM $from
		$join
		WHERE $where
		ORDER BY $db_order $db_way
		";
//        var_dump($queryForDownload);
		$masterquery = $db->query("
		$queryForDownload
		LIMIT $db_cursor,
		$rparameters[maxline]
		");
	} else {$masterquery='';}
    $query=$db->query("SELECT FOUND_ROWS();");
	$resultcount=$query->fetch();
	$query->closeCursor();

//check box selection SQL updates
if($_POST['selectrow'] && $_POST['selectrow']!='selectall')
{
	while ($row=$masterquery->fetch())
	{
		//initialize variables
		if(!isset($_POST['checkbox'.$row["id"]])) $_POST['checkbox'.$row["id"]] = '';
		if($_POST['checkbox'.$row['id']]!='')
		{
			//change state
			if($_POST['selectrow']=="delete" && $rright['ticket_delete'] && $row['id'])
			{
				$qry=$db->prepare("DELETE FROM `tincidents` WHERE id=:id"); //delete ticket
				$qry->execute(array('id' => $row['id']));
				$qry=$db->prepare("DELETE FROM `tevents` WHERE incident=:incident"); //delete associate events
				$qry->execute(array('incident' => $row['id']));
				$qry=$db->prepare("DELETE FROM `tthreads` WHERE ticket=:ticket"); //delete threads
				$qry->execute(array('ticket' => $row['id']));
				$qry=$db->prepare("DELETE FROM `tmails` WHERE incident=:incident"); //delete mails
				$qry->execute(array('incident' => $row['id']));
				$qry=$db->prepare("DELETE FROM `tsurvey_answers` WHERE ticket_id=:ticket_id"); //delete survey
				$qry->execute(array('ticket_id' => $row['id']));
				$qry=$db->prepare("DELETE FROM `ttemplates` WHERE incident=:incident"); //delete template
				$qry->execute(array('incident' => $row['id']));
				$qry=$db->prepare("DELETE FROM `ttoken` WHERE ticket_id=:ticket_id"); //delete token
				$qry->execute(array('ticket_id' => $row['id']));

				//remove upload files and folder if exist
				$upload_dir_to_remove='upload/'.$row['id'].'/';
				if(is_numeric($row['id']) && is_dir($upload_dir_to_remove))
				{
					//remove files before delete directory
					$files_to_remove = array_diff(scandir($upload_dir_to_remove), array('.','..'));
					foreach ($files_to_remove as $file_to_remove) {
						if(file_exists($upload_dir_to_remove.$file_to_remove)) {unlink($upload_dir_to_remove.$file_to_remove);}
					}
					rmdir($upload_dir_to_remove); //remove empty dir
				}

				//remove new upload files
				$qry2=$db->prepare("SELECT COUNT(`id`) FROM `tattachments` WHERE ticket_id=:ticket_id");
				$qry2->execute(array('ticket_id' => $row['id']));
				$row2=$qry2->fetch();
				$qry2->closeCursor();
				if($row2[0]>0)
				{
					//remove files
					$qry2=$db->prepare("SELECT `storage_filename` FROM `tattachments` WHERE ticket_id=:ticket_id");
					$qry2->execute(array('ticket_id' => $row['id']));
					while($attachment=$qry2->fetch())
					{
						if(file_exists('upload/ticket/'.$attachment['storage_filename'])) {unlink('upload/ticket/'.$attachment['storage_filename']);}
					}
					$qry2->closeCursor();
					//delete in db
					$qry2=$db->prepare("DELETE FROM `tattachments` WHERE ticket_id=:ticket_id");
					$qry2->execute(array('ticket_id' => $row['id']));
				}

				//remove image attachment from IMAP connector
				$ticket_files = glob("upload/ticket/$row[id]_*");
				foreach ($ticket_files as $file_to_delete) {
					if(file_exists($file_to_delete)){unlink($file_to_delete); }
				}
				echo DisplayMessage('success',T_('Ticket supprimé'));
			} else {
				$qry=$db->prepare("UPDATE `tincidents` SET `state`=:state WHERE `id`=:id");
				$qry->execute(array('state' => $_POST['selectrow'],'id' => $row['id']));
				//insert ticket threads
				if($_POST['selectrow']==3)
				{
					$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,4,3)");
					$qry->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s'),'author' => $_SESSION['user_id']));
				} elseif(is_numeric($_POST['selectrow']))
				{
					$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`,`state`) VALUES (:ticket,:date,:author,5,:state)");
					$qry->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s'),'author' => $_SESSION['user_id'],'state' => $_POST['selectrow']));
				}
				echo DisplayMessage('success',T_('Ticket').' '.$row['id'].' '.T_('modifié'));
				if($_POST['selectrow']==3) //case solved state
				{
					//insert current date in resolution date
					$currentdate=date("Y-m-d H:i:s");
					$qry=$db->prepare("UPDATE `tincidents` SET `date_res`=:date_res WHERE `id`=:id");
					$qry->execute(array('date_res' => $currentdate,'id' => $row['id']));
					//send mail notifications
					if($rparameters['mail_auto'])
					{
						$_GET['id']=$row['id'];
						$autoclose=1;
						require('core/auto_mail.php');
					}
				}
			}
		}
	}
	$masterquery->closeCursor();

	//redirect
	$url="./index.php?page=dashboard&state=$_GET[state]&userid=$_GET[userid]";
	$url=preg_replace('/%/','%25',$url);
	echo "<SCRIPT LANGUAGE='JavaScript'>
				<!--
				function redirect()
				{
				window.location='$url'
				}
				setTimeout('redirect()',$rparameters[time_display_msg]);
				-->
		</SCRIPT>";
}
?>

<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2">
		<?php
		if($keywords)
		{
			$disp_keyword=str_replace("'","",$keywords);
			echo '<i class="fa fa-search text-primary-m2"></i> '.T_('Recherche de tickets').' : '.$keywords.' ';
		}
		elseif($_GET['view']=='activity')
		{
			//convert and create date format to display
			if($_POST['date_start'] && $_POST['date_end']) // case post date
			{
				//convert date to display
				$date_start_db=DateTime::createFromFormat('Y-m-d', $_POST['date_start']);
				$date_end_db=DateTime::createFromFormat('Y-m-d', $_POST['date_end']);
				$date_start=$date_start_db->format('d/m/Y');
				$date_end=$date_end_db->format('d/m/Y');
				$date_start_db=$date_start_db->format('Y-m-d');
				$date_end_db=$date_end_db->format('Y-m-d');
			} else { //default date is today date
				$date_start_db=DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
				$date_end_db=DateTime::createFromFormat('Y-m-d', date('Y-m-d'));
				$date_start=$date_start_db->format('d/m/Y');
				$date_end=$date_end_db->format('d/m/Y');
				$date_start_db=$date_start_db->format('Y-m-d');
				$date_end_db=$date_end_db->format('Y-m-d');
			}
			//count open ticket for selected period
			$query="
			SELECT DISTINCT(id) FROM `tincidents`
			WHERE
			tincidents.date_create BETWEEN '$date_start_db 00:00:00' AND '$date_end_db 23:59:59'
			AND	tincidents.id LIKE $db_ticket
			AND	tincidents.technician LIKE '$_POST[technician]'
			AND	tincidents.user LIKE '$_POST[user]'
			AND	tincidents.category LIKE '$_POST[category]'
			AND	tincidents.subcat LIKE '$_POST[subcat]'
			AND	tincidents.title LIKE '%$db_title%'
			AND tincidents.date_create LIKE '$_POST[date_create]%'
			AND tincidents.date_hope LIKE '$_POST[date_hope]%'
			AND tincidents.date_res LIKE '$_POST[date_res]%'
			AND	tincidents.state LIKE '$_POST[state]'
			AND	tincidents.criticality LIKE '$_POST[criticality]'
			AND	tincidents.priority LIKE '$_POST[priority]'
			AND disable='0'
			$where_agency $where_service $parenthese2";
			$query = $db->query($query);
			$cnt_activity_open=$query->rowCount();
			$query->closecursor();
			//count advanced ticket for selected period (technician add text resolution and ticket not disable)
			$query="
			SELECT DISTINCT(tthreads.id) FROM `tthreads`,`tincidents`
			WHERE
			tincidents.id=tthreads.ticket
			AND tincidents.technician=tthreads.author
			AND tincidents.state!=3
			AND	tincidents.id LIKE $db_ticket
			AND	tincidents.technician LIKE '$_POST[technician]'
			AND	tincidents.user LIKE '$_POST[user]'
			AND	tincidents.category LIKE '$_POST[category]'
			AND	tincidents.subcat LIKE '$_POST[subcat]'
			AND	tincidents.title LIKE '%$db_title%'
			AND tincidents.date_create LIKE '$_POST[date_create]%'
			AND tincidents.date_hope LIKE '$_POST[date_hope]%'
			AND tincidents.date_res LIKE '$_POST[date_res]%'
			AND	tincidents.state LIKE '$_POST[state]'
			AND	tincidents.criticality LIKE '$_POST[criticality]'
			AND	tincidents.priority LIKE '$_POST[priority]'
			AND tincidents.disable=0
			AND tthreads.type='0'
			AND tthreads.date BETWEEN '$date_start_db 00:00:00' AND '$date_end_db 23:59:59'
			$where_agency $where_service $parenthese2
			";
			$query = $db->query($query);
			$cnt_activity_advanced=$query->rowCount();
			$query->closecursor();
			//count close tickets for selected period
			$query="
			SELECT DISTINCT(id) FROM `tincidents`
			WHERE
			tincidents.state='3'
			AND	tincidents.id LIKE $db_ticket
			AND	tincidents.technician LIKE '$_POST[technician]'
			AND	tincidents.user LIKE '$_POST[user]'
			AND	tincidents.category LIKE '$_POST[category]'
			AND	tincidents.subcat LIKE '$_POST[subcat]'
			AND	tincidents.title LIKE '%$db_title%'
			AND tincidents.date_create LIKE '$_POST[date_create]%'
			AND tincidents.date_hope LIKE '$_POST[date_hope]%'
			AND tincidents.date_res LIKE '$_POST[date_res]%'
			AND	tincidents.state LIKE '$_POST[state]'
			AND	tincidents.criticality LIKE '$_POST[criticality]'
			AND	tincidents.priority LIKE '$_POST[priority]'
			AND date_res BETWEEN '$date_start_db 00:00:00' AND '$date_end_db 23:59:59'
			AND disable='0'
			$where_agency $where_service $parenthese2";
			$query = $db->query($query);
			$cnt_activity_close=$query->rowCount();
			$query->closecursor();

			//display title with date selection form
			echo '
			<span title="'.T_("Liste des tickets modifiés: ouverts ou fermés ou sur lesquels un élément de résolution à été ajouté sur la période sélectionnée").'">
			<i class="fa fa-calendar text-primary-m2"></i>
			'.T_('Tickets modifiés du').'
			</span>
			<form style="display: inline-block;" class="form-horizontal" name="period" id="period" method="post" action="./index.php?page=dashboard&userid='.$_GET['userid'].'&state=%25&view=activity&date_range=1" onsubmit="loadVal();" >
				<input class="form-control-sm" data-toggle="datetimepicker" data-target="#date_start" type="text" autocomplete="off" size="10" name="date_start" id="date_start" value="'.$date_start.'" onchange="" >
				'.T_('au').'
				<input class="form-control-sm" data-toggle="datetimepicker" data-target="#date_end" type="text" autocomplete="off" size="10" name="date_end" id="date_end" value="'.$date_end.'" onchange="" >
				<button class="btn btn-xs btn-success" title="'.T_('Valider la sélection').'" name="modify" value="submit" type="submit" id="modify_btn"><i class="fa fa-check text-110"></i></button>
			</form>
			';
		}
		else
		{
		    //find state name for display in title
			$qry=$db->prepare("SELECT `description` FROM `tstates` WHERE id=:id");
			$qry->execute(array('id' => $_GET['state']));
			$rstate=$qry->fetch();
			$qry->closeCursor();

            if(!$rstate && !$_GET['viewid'] && !$_GET['techgroup']) $rstate['description']=T_('tickets non lus'); //case not read
            if($_GET['state']=='meta') $rstate['description']=T_('tickets à traiter'); //case not read
			//special case for service only add name of services at the end of the title
			if($rright['dashboard_service_only'] && $rparameters['user_limit_service']==1 && $cnt_service!=0 )
			{
				$service_title='';
				$cnt=0;
				//get services of current user
				$qry=$db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
				$qry->execute(array('user_id' => $_SESSION['user_id']));
				while($row=$qry->fetch())
				{
					$cnt++;
					//get service name to display in title
					$qry2=$db->prepare("SELECT `name` FROM `tservices` WHERE id=:id");
					$qry2->execute(array('id' => $row['service_id']));
					$row2=$qry2->fetch();
					$qry2->closeCursor();

					if($cnt==1){$service_title.=' '.$row2['name'];} else {$service_title.=' '.T_("et").' '.$row2['name'];}
				}
				$qry->closeCursor();
				$service_title=' '.T_("du service").' '.$service_title;
			} else {$service_title='';}
			//special case for agency only add name of agencies at the end of the title
			if($rright['dashboard_agency_only'] && $cnt_agency!=0 && $rparameters['user_agency'])
			{
				$agency_title='';
				$cnt=0;
				//get agencies of current user
				$qry=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
				$qry->execute(array('user_id' => $_SESSION['user_id']));
				while($row=$qry->fetch())
				{
					$cnt++;
					//get agency name to display in title
					$qry2=$db->prepare("SELECT `name` FROM `tagencies` WHERE id=:id");
					$qry2->execute(array('id' => $row['agency_id']));
					$row2=$qry2->fetch();
					$qry2->closeCursor();
					if($cnt==1){$agency_title.=' '.$row2['name'];} else {$agency_title.=' '.T_("et").' '.$row2['name'];}
				}
				$qry->closeCursor();
				$agency_title=' '.T_("de l'agence").' '.$agency_title;
			} else {$agency_title='';}
            //find view name to display in title
            if($_GET['viewid'])
            {
				$qry=$db->prepare("SELECT `name` FROM `tviews` WHERE id=:id");
				$qry->execute(array('id' => $_GET['viewid']));
				$rview=$qry->fetch();
				$qry->closeCursor();

                $rstate['description']=T_('tickets de la vue').' '.$rview['name'].'';
				echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tickets de la vue').' '.$rview['name'].'';
            }
            elseif($_GET['userid']=='%' && $_GET['companyview']=='')
			{
			    if($_GET['state']=='%') {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tous les tickets').$service_title.$agency_title;} else {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tous les').' '.T_($rstate['description']).$service_title.$agency_title;}
			}
			elseif($_GET['userid']=='%' && $_GET['companyview']!='')
			{
			    if($_GET['state']=='%') {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tous les tickets de ma société');} else {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_($rstate['description']).' '.T_('de ma société');}
			}
			elseif($_GET['userid']!='0'  && !$_GET['techgroup'])
			{
			    if($_GET['state']=='%') {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tous vos tickets').'';} else {echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Vos').' '.T_($rstate['description']).'';}
			}
			elseif($_GET['techgroup'])
			{
				//get name of current group
				$qry=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
				$qry->execute(array('id' => $_GET['techgroup']));
				$group_name=$qry->fetch();
				$qry->closeCursor();

				echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Vos tickets du groupe').' '.$group_name['name'];
			}
			if($_GET['state']=='%' && $_GET['userid']==0 && $_GET['userid']!='%') echo '<i class="fa fa-ticket-alt text-primary-m2"></i> '.T_('Tous les tickets non attribués').''; //case not read
		}
		?>
		<small class="page-info text-secondary-d2">
			<i class="fa fa-angle-double-right"></i>
			&nbsp;<?php if($mobile==0) {echo T_('Nombre').' :';} ?> <?php echo $resultcount[0]; ?></i>
			<?php
				//display counter section activity page
				if($_GET['view']=='activity')
				{
					echo ' 	|
					<span title="'.T_('Nombre de tickets pour lesquels la date de création est dans la période sélectionnée').'">'.T_('Ouverts').'</span> : '.$cnt_activity_open.'&nbsp;&nbsp;&nbsp;
					<span title="'.T_("Nombre de tickets pour lesquels un élément de résolution textuel à été ajouté par le technicien en charge dans la période sélectionnée et qui ne sont pas dans l'état résolu").'">'.T_('Avancés').'</span> : '.$cnt_activity_advanced.'&nbsp;&nbsp;&nbsp;
					<span title="'.T_("Nombre de tickets pour lesquels la date de résolution est dans la période sélectionnée et qui sont dans l'état résolu").'">'.T_('Fermés').' :</span> '.$cnt_activity_close.'</small>';
				}
			?>
		</small>
	</h1>
</div>
<?php
	//display message if search result is null
	if($resultcount[0]==0 && $keywords!="") {
		echo DisplayMessage('error',T_("Aucun ticket trouvé"));
	}
?>
<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
	<div style="overflow-y: hidden;" class="table-responsive">
		<div class="col-xs-12">
			<table id="simple-table" class="table table-bordered table-bordered table-striped table-hover text-dark-m2 ">
				<?php
				if($_GET['way']=='ASC') $arrow_way='DESC'; else $arrow_way='ASC';
				//*********************** FIRST LINE ***********************
				echo '
				<thead class="text-dark-m3 bgc-grey-l4">
					<tr class="bgc-white text-secondary-d3 text-95">
						<th '; if($_GET['order']=='id') echo 'class="active"'; echo '>
							<center>

								<a class="text-primary-m2" title="'.T_('Numéro du ticket').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=id&amp;way='.$arrow_way.'">
									<i class="fa fa-tag text-primary-m2"></i><br />
									'.T_('Numéro');
									//Display way arrows
									if($_GET['order']=='id'){
										if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
										if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
									}
									echo "
								</a>
							</center>
						</th>
						";
						//display tech column, do not display tech column if technician is connected
						if($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%')
						{
							echo '
								<th ';  if($_GET['order']=='technician') echo 'class="active"'; echo '>
									<center>
										<a class="text-primary-m2" title="'.T_('Technicien en charge du ticket').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=technician&amp;way='.$arrow_way.'">
											<i class="fa fa-user text-primary-m2"></i><br />
											'.T_('Gestionnaire');
											//Display arrows
											if($_GET['order']=='technician'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo'
										</a>
									</center>
								</th>
							';
						}
						//display user company column
						/*
						if($rright['dashboard_col_company'])
						{
							echo '
								<th ';  if($_GET['order']=='company') echo 'class="active"'; echo '>
									<center>
										<a class="text-primary-m2" title="'.T_('Société du demandeur').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=company&amp;way='.$arrow_way.'">
											<i class="fa fa-building text-primary-m2"></i><br />
											'.T_('Société');
											//Display arrows
											if($_GET['order']=='company'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo'
										</a>
									</center>
								</th>
							';
						}
						//*/
						//display user column
						//!\ old - if(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all'] && ($_GET['userid']=='%'|| $keywords!='')) || ($rparameters['user_company_view']!=0 && $_GET['userid']=='%' && ($rright['side_company'] || $keywords!='')))
						/*/!\AJOUTER PAR NOS SOINS*/
                        if(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==1 || $_SESSION['profile_id']==2 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all'] && ($_GET['userid']=='%'|| $keywords!='')) || ($rparameters['user_company_view']!=0 && $_GET['userid']=='%' && ($rright['side_company'] || $keywords!='')))
						{
							echo '
								<th '; if($_GET['order']=='user') echo 'class="active"'; echo '>
									<center>
										<a class="text-primary-m2" title="'.T_('Demandeur associé au ticket').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=user&amp;way='.$arrow_way.'">
											<i class="fa fa-male text-primary-m2"></i><br />
											'.T_('Demandeur');
											//Display arrows
											if($_GET['order']=='user'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo"
										</a>
									</center>
								</th>
							";
						}
						//display user service column
						if($rright['dashboard_col_user_service'])
						{
							echo '
							<th '; if($_GET['order']=='sender_service') {echo 'class="active"';} echo '>
								<center>
									<a class="text-primary-m2" title="'.T_('Service du demandeur').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=sender_service&amp;way='.$arrow_way.'">
										<i class="fa fa-users text-primary-m2"></i><br />
										'.T_('Service du demandeur');
										//Display arrows
										if($_GET['order']=='sender_service'){
											if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
											if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
										}
										echo"
									</a>
								</center>
							</th>
							";
						}
						//display ticket type column
						if($rright['dashboard_col_type'])
						{
							echo '
							<th '; if($_GET['order']=='type') {echo 'class="active"';} echo '>
								<center>
									<a class="text-primary-m2" title="'.T_('Type de ticket').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=type&amp;way='.$arrow_way.'">
										<i class="fa fa-flag text-primary-m2"></i><br />
										'.T_('Type');
										//Display arrows
										if($_GET['order']=='type'){
											if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
											if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
										}
										echo"
									</a>
								</center>
							</th>
							";
						}
						//display ticket category column
						if($rright['dashboard_col_category'])
						{
							echo '
								<th '; if($_GET['order']=='category') {echo 'class="active"';} echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Catégorie').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=category&amp;way='.$arrow_way.'">
											<i class="fa fa-square text-primary-m2"></i><br />
											'.T_('Catégorie');
											//Display arrows
											if($_GET['order']=='category'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
							';
						}
						//display ticket subcat column
						if($rright['dashboard_col_subcat'])
						{
							echo'
								<th '; if($_GET['order']=='subcat') {echo 'class="active"';} echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Sous-catégorie').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=subcat&amp;way='.$arrow_way.'">
											<i class="fa fa-sitemap text-primary-m2"></i><br />
											'.T_('Financement');
											//Display arrows
											if($_GET['order']=='subcat'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
							';
						}
						//display ticket asset column
						if($rright['dashboard_col_asset'])
						{
							echo'
								<th '; if($_GET['order']=='asset_id') {echo 'class="active"';} echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Équipement').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=asset_id&amp;way='.$arrow_way.'">
											<i class="fa fa-desktop text-primary-m2"></i><br />
											'.T_('Équipement');
											//Display arrows
											if($_GET['order']=='asset_id'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
							';
						}
						?>
						<?php if($rparameters['ticket_places']==1){ ?>
						<th <?php if($_GET['order']=='place') echo 'class="active"'; ?> >
							<center>
								<a class="text-primary-m2" title="<?php echo T_('Emplacement du ticket'); ?>"  href="<?php echo './index.php?page=dashboard&'.$url_post_parameters; ?>&amp;order=place&amp;way=<?php echo $arrow_way; ?>">
									<i class="fa fa-globe text-primary-m2"></i><br />
									<?php
									echo T_('Lieu');
									//Display arrows
									if($_GET['order']=='place'){
										if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
										if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<?php } ?>
						<?php
						if($rright['dashboard_col_service'])
						{
							echo'
								<th '; if($_GET['order']=='service') {echo 'class="active"';} echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Service').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=u_service&amp;way='.$arrow_way.'">
											<i class="fa fa-users text-primary-m2"></i><br />
											'.T_('Service');
											//Display arrows
											if($_GET['order']=='u_service'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
							';
						}
						/* /!\ new */
						// affichage de la colone address1 (site geographique)
/*
							echo '
								<th ';  if ($_GET['order']=='address1') echo 'class="active"'; echo '>
									<center>
										<a title="'.T_('Site géographique de l\'auteur').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=address1&amp;way='.$arrow_way.'">
											<i class="icon-building"></i><br />
											'.T_('Site');
											//Display arrows
											if ($_GET['order']=='site'){
												if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
												if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
											}
											echo'
										</a>
									</center>
								</th>
							';
						//*/
											/* /!\ fin new */
											/*
						echo '
						  <th';if ($_GET['order']=='start_date') echo 'class="active"'; echo '>
                                                    <center>
                                                      <a title="'.T_('Date début').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=start_date&amp;way='.$arrow_way.'">
                                                                                        <i class="icon-building"></i><br />
                                                                                        '.T_('Date début');
                                                                                        //Display arrows
                                                                                        if ($_GET['order']=='start_date'){
                                                                                                if ($_GET['way']=='ASC') {echo ' <i class="icon-sort-up"></i>';}
                                                                                                if ($_GET['way']=='DESC') {echo ' <i class="icon-sort-down"></i>';}
                                                                                        }
                                                                                        echo'
                                                      </a>
                                                    </center>
                                                  </th>
                                                  ';
											//*/
						/*
						if($rright['dashboard_col_agency'])
						{
							echo'
								<th '; if($_GET['order']=='u_agency') {echo 'class="active"';} echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Agence').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=u_agency&amp;way='.$arrow_way.'">
											<i class="fa fa-globe text-primary-m2"></i><br />
											'.T_('Agence');
											//Display arrows
											if($_GET['order']=='u_agency'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
							';
						}
						//*/
						?>
						<th <?php if($_GET['order']=='title') echo 'class="active"'; ?> >
							<center>
								<a class="text-primary-m2" title="<?php echo T_('Titre du ticket'); ?>"  href="<?php echo './index.php?page=dashboard&'.$url_post_parameters; ?>&amp;order=title&amp;way=<?php echo $arrow_way; ?>">
									<i class="fa fa-file-alt text-primary-m2"></i><br />
									<?php
									echo T_('Intitulé de la demande');
									//Display arrows
									if($_GET['order']=='title'){
										if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
										if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
									}
									?>
								</a>
							</center>
						</th>
						<?php
							if($rright['dashboard_col_date_create'])
							{
								echo '
								<th '; if($_GET['order']=='date_create') echo 'class="active"'; echo' >
									<center>
										<a class="text-primary-m2" title="'.T_('Date de création du ticket').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=date_create&amp;way='.$arrow_way.'">
											<i class="fa fa-calendar text-primary-m2"></i><br />
											'.T_('Date demande');
											//Display arrows
											if(preg_match("#date_create#i", "'.$_GET[order].'")){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
								';
							}
							if($rright['dashboard_col_date_hope'])
							{
								echo '
								<th '; if($_GET['order']=='date_hope') echo 'class="active"'; echo' >
									<center>
										<a class="text-primary-m2" title="'.T_('Date de résolution estimée du ticket').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=date_hope&amp;way='.$arrow_way.'">
											<i class="fa fa-calendar text-primary-m2"></i><br />
											'.T_('Date de résolution estimée');
											//Display arrows
											if(preg_match("#date_hope#i", "'.$_GET[order].'")){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
								';
							}
							if($rright['dashboard_col_date_res'])
							{
								echo '
								<th '; if($_GET['order']=='date_res') echo 'class="active"'; echo' >
									<center>
										<a class="text-primary-m2" title="'.T_('Date de résolution du ticket').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=date_res&amp;way='.$arrow_way.'">
											<i class="fa fa-calendar text-primary-m2"></i><br />
											'.T_('Date de résolution');
											//Display arrows
											if(preg_match("#date_res#i", "'.$_GET[order].'")){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
								';
							}
							if($rright['dashboard_col_time'])
							{
								echo '
								<th '; if($_GET['order']=='time') echo 'class="active"'; echo' >
									<center>
										<a class="text-primary-m2" title="'.T_('Date de résolution du ticket').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=time&amp;way='.$arrow_way.'">
											<i class="fa fa-clock text-primary-m2"></i><br />
											'.T_('Temps passé');
											//Display arrows
											if(preg_match("#time#i", "'.$_GET[order].'")){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
												if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
								';
							}
?>
<?php
 echo '
                                                  <th ';if ($_GET['order']=='date_start') echo 'class="active"'; echo '>
                                                    <center>
                                                      <a class="text-primary-m2" title="'.T_('Date début').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=date_start&amp;way='.$arrow_way.'">
                                                                                        <i class="fa fa-hourglass-start text-primary-m2"></i><br />
                                                                                        '.T_('Date début');
                                                                                        //Display arrows
                                                                                        if(preg_match("#date_start#i", "'.$_GET[order].'")){
                                                                                            if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
                                                                                            if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
                                                                                        }
                                                                                        echo'
                                                      </a>
                                                    </center>
                                                  </th>
                                                  ';
?>
						<th <?php if($_GET['order']=='state') echo 'class="active"'; ?> >
							<center>
								<a class="text-primary-m2" title="<?php echo T_('État'); ?>" href="<?php echo './index.php?page=dashboard&'.$url_post_parameters; ?>&amp;order=state&amp;way=<?php echo $arrow_way; ?>">
								<i class="fa fa-adjust text-primary-m2"></i><br />
								<?php
								if($_GET['view']=='activity') {echo T_('État actuel');} else {echo T_('État');}
								//Display arrows
								if(($_GET['order']=='state') || ($_GET['order']=='tstates.number, tincidents.date_hope, tincidents.priority, tincidents.criticality' && $_GET['state']=='%')){
									if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
									if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
								}
								?>
								</a>
							</center>
						</th>
						<?php
							//display priority column
							if($rright['dashboard_col_priority'])
							{
								echo '
								<th '; if($_GET['order']=='priority') echo 'class="active"'; echo ' >
									<center>
										<a class="text-primary-m2" title="'.T_('Priorité').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=priority&amp;way='.$arrow_way.'">
										<i class="fa fa-sort-amount-down-alt text-primary-m2"></i><br />
										'.T_('Priorité');
										//Display arrows
										if($_GET['order']=='priority'){
											if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
											if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
										}
										echo '
										</a>
									</center>
								</th>
								';
							}
							//display criticality column
							if($rright['dashboard_col_criticality'])
							{
								echo '
								<th
								'; if($_GET['order']=='criticality') echo 'class="active"'; echo '>
									<center>
										<a class="text-primary-m2" title="'.T_('Criticité').'"  href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order=criticality&amp;way='. $arrow_way.'">
											<i class="fa fa-bullhorn text-primary-m2"></i><br />
											'.T_('Criticité');
											//Display arrows
											if($_GET['order']=='criticality'){
												if($_GET['way']=='ASC') {echo ' <i class="fa fa-sort-up"></i>';}
											if($_GET['way']=='DESC') {echo ' <i class="fa fa-sort-down"></i>';}
											}
											echo '
										</a>
									</center>
								</th>
								';
							}
						?>
					</tr>
					<?php // *********************************** FILTER LINE ************************************** ?>
					<form name="filter" method="POST">
						<tr class="bgc-white text-secondary-d3 text-95">
							<td>
								<center>
									<input class="form-control" name="ticket" onchange="submit();" type="text" style="width:80px" value="<?php if($_POST['ticket']!='%') {echo $_POST['ticket'];} ?>" />
								</center>
							</td>
							<?php
								//display filter of technician column
								if($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%')
								{
									echo '
									<td align="center" id="technician_filter">
										<select class="chosen-select" data-placeholder=" " style="width:100px;" name="technician" onchange="submit()" >
											<option value="%"></option>
											<option value="0">'.T_('Aucun').'</option>';
											//tech list
											if($join)
											{
												$query="SELECT STRAIGHT_JOIN DISTINCT tusers.id,tusers.lastname,tusers.firstname FROM tusers INNER JOIN tincidents ON tusers.id=tincidents.technician INNER JOIN tcompany ON tusers.company=tcompany.id INNER JOIN tthreads ON tincidents.id=tthreads.ticket $join WHERE $where AND (profile='0' or profile='4' or profile='3') ORDER BY tusers.lastname";
												if(preg_match('#FROM tusers#',$query)) {$query=str_replace("LEFT JOIN tusers ON tincidents.user=tusers.id","",$query);} //avoid company column pb
												if(preg_match('#INNER JOIN tcompany ON tusers.company=tcompany.id#',$query)) {$query=str_replace("INNER JOIN tcompany ON tusers.company=tcompany.id","",$query);} //avoid company column pb
												if(preg_match("#AND tusers.company='$ruser[company]'#",$query)) {$query=str_replace("AND tusers.company='$ruser[company]'","",$query);} //fix empty technician filter on company view
											}
											else
											{$query="SELECT tusers.id,tusers.lastname,tusers.firstname FROM tusers WHERE (profile='0' or profile='4') and disable='0' ORDER BY lastname";}
											if($rparameters['debug']) {echo $query;}
											$query = $db->query($query);
											while ($row = $query->fetch())
											{
												if(extension_loaded('mbstring')) {
													$cutfname=mb_substr($row['firstname'], 0, 1);
												} else {
													$cutfname=substr($row['firstname'], 0, 1);
												}
												if($_POST['technician']==$row['id']) echo "<option selected value=\"$row[id]\">$cutfname. $row[lastname]</option>"; else echo "<option value=\"$row[id]\">$cutfname. $row[lastname]</option>";
											}
											//tech group list
											$query = $db->query("SELECT `id`,`name` FROM tgroups WHERE disable='0' AND type='1' ORDER BY name");
											while ($row = $query->fetch())
											{
												if($t_group==$row['id'] || $_GET['t_group']==$row['id']) echo "<option selected value=\"G_$row[id]\">[G] $row[name]</option>"; else echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
											}
										echo "
										</select>
									</td>";
								}

						                //display filter of user company column
                         				        /*	
								if($rright['dashboard_col_company'])
								{
									echo '
									<td align="center" >
										<select class="chosen-select" data-placeholder=" " style="width:100px;" name="company" onchange="submit()">
											<option value="%"></option>';
											//display company list
											if($join)
											{
												//!\ OLD - $query = $db->query("SELECT DISTINCT tcompany.id, tcompany.name FROM tcompany INNER JOIN tusers ON tusers.company=tcompany.id INNER JOIN tincidents ON tusers.id=tincidents.user INNER JOIN tthreads ON tincidents.id=tthreads.ticket WHERE $where ORDER BY tcompany.name");
								//!\AJOUTER PAR NOS SOINS
								$query = $db->query("SELECT DISTINCT tcompany.id, tcompany.name FROM tcompany INNER JOIN tusers ON tusers.company=tcompany.id INNER JOIN tincidents ON tusers.id=tincidents.user INNER JOIN tthreads ON tincidents.id=tthreads.ticket INNER JOIN dticket_observers ON tincidents.id=dticket_observers.id WHERE $where ORDER BY tcompany.name");
											}
											else
											{$query = $db->query("SELECT tcompany.id, tcompany.name FROM tcompany WHERE disable='0' ORDER BY name");} //query for searchengine
											while ($row=$query->fetch())
											{
												if($_POST['company']==$row['id'])
												{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
												else
												{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
											}
											echo '
										</select>
									</td>';
								}
                                                                //*/
								/*
								echo '<td align="center" id="applicant_filter">
								<select class="chosen-select" data-placeholder=" " style="width:100px;" name="user" onchange="submit()">
									<option value="%"></option>
									<option value="0">'.T_('Aucun').'</option>';
									// Display users list
									if($join)
									{
										$query="SELECT DISTINCT tusers.id,tusers.firstname,tusers.lastname, tcompany.name FROM tusers INNER JOIN tincidents ON tusers.id=tincidents.user INNER JOIN tcompany ON tusers.company=tcompany.id LEFT JOIN tthreads ON tincidents.id=tthreads.ticket $join WHERE $where ORDER BY tusers.lastname";
										if(preg_match('#FROM tusers#',$query)) {$query=str_replace("LEFT JOIN tusers ON tincidents.user=tusers.id","",$query);} //avoid company column pb
										if(preg_match('#INNER JOIN tcompany#',$query)) {$query=str_replace("LEFT JOIN tcompany ON tusers.company=tcompany.id","",$query);} //avoid company column pb
										if($rparameters['debug']) {echo $query;}
										$query = $db->query($query);
									}
									else
									{
										// AJOUT (tcompany)
										$query = $db->query("SELECT tusers.id,tusers.firstname,tusers.lastname, tcompany.name FROM tusers INNER JOIN tcompany ON tusers.company=tcompany.id WHERE disable='0' ORDER BY lastname"); //query for searchengine
										while ($row=$query->fetch())
										{
											if(extension_loaded('mbstring')) 
											{
												$cutfname=mb_substr($row['firstname'], 0, 1);
											} 
											else 
											{
												$cutfname=substr($row['firstname'], 0, 1);
											}
											if($_POST['user']==$row['id'])
											{// AJOUT (tcompany)
												echo '<option selected value="'.$row['id'].'">'.$cutfname.'. '.$row['lastname'].' ('.$row['name'].')</option>';}
											elseif($row['firstname']=='' && $row['lastname']=='') {}
											else
											{
												// AJOUT (tcompany)
												echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$cutfname.'. ('.$row['name'].')</option>';
											}
										}
										// User group list
										$query = $db->query("SELECT `id`,`name` FROM tgroups WHERE disable='0' AND type='0' ORDER BY name");
										while ($row=$query->fetch())
										{
											if($u_group==$row['id'] || $_GET['u_group']==$row['id']) echo "<option selected value=\"G_$row[id]\">[G] $row[name]</option>"; else echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
										}
									}
								echo '</select></td>';
								//*/
								//display filter of user column
								/*/!\ Ajout profile_id=2*/
								if(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==2 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all'] && ($_GET['userid']=='%'|| $keywords!='')) || ($rparameters['user_company_view']!=0 && $_GET['userid']=='%' && ($rright['side_company'] || $keywords!='')))
								{
									echo '<td align="center" id="applicant_filter">
										<select class="chosen-select" data-placeholder=" " style="width:100px;" name="user" onchange="submit()">
											<option value="%"></option>
											<option value="0">'.T_('Aucun').'</option>';
											//display users list
											if($join)
											{
												$query="SELECT DISTINCT tusers.id,tusers.firstname,tusers.lastname, tcompany.name FROM tusers INNER JOIN tincidents ON tusers.id=tincidents.user INNER JOIN tcompany ON tusers.company=tcompany.id LEFT JOIN tthreads ON tincidents.id=tthreads.ticket $join WHERE $where ORDER BY tusers.lastname";
												if(preg_match('#FROM tusers#',$query)) {$query=str_replace("LEFT JOIN tusers ON tincidents.user=tusers.id","",$query);} //avoid company column pb
												if(preg_match('#INNER JOIN tcompany#',$query)) {$query=str_replace("LEFT JOIN tcompany ON tusers.company=tcompany.id","",$query);} //avoid company column pb
												if($rparameters['debug']) {echo $query;}
												$query = $db->query($query);
											}
											else
											{/*/!\ AJOUT (tcompany)*/$query = $db->query("SELECT tusers.id,tusers.firstname,tusers.lastname, tcompany.name FROM tusers INNER JOIN tcompany ON tusers.company=tcompany.id WHERE disable='0' ORDER BY lastname");} //query for searchengine
												while ($row=$query->fetch())
												{
													if(extension_loaded('mbstring')) {
														$cutfname=mb_substr($row['firstname'], 0, 1);
													} else {
														$cutfname=substr($row['firstname'], 0, 1);
													}
													if($_POST['user']==$row['id'])
													{/*/!\ AJOUT (tcompany)*/echo '<option selected value="'.$row['id'].'">'.$cutfname.'. '.$row['lastname'].' ('.$row['name'].')</option>';}
													elseif($row['firstname']=='' && $row['lastname']=='') {}
													else
													{/*/!\ AJOUT (tcompany)*/echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$cutfname.'. ('.$row['name'].')</option>';}
												}
												//user group list
												$query = $db->query("SELECT `id`,`name` FROM tgroups WHERE disable='0' AND type='0' ORDER BY name");
												while ($row=$query->fetch())
												{
													if($u_group==$row['id'] || $_GET['u_group']==$row['id']) echo "<option selected value=\"G_$row[id]\">[G] $row[name]</option>"; else echo "<option value=\"G_$row[id]\">[G] $row[name]</option>";
												}
												echo '</select></td>';
											}
								//display filter of user service column
								if($rright['dashboard_col_user_service'])
								{
									echo'
									<td align="center" id="service_filter">
										<select class="form-control" style="width:60px" name="sender_service" onchange="submit()">
											<option value="%"></option>
											';
												if($join)
												{
													$query="SELECT DISTINCT tservices.id,tservices.name FROM tservices INNER JOIN tincidents ON tincidents.sender_service=tservices.id $join WHERE $where AND tservices.disable='0' ORDER BY tservices.name";
													if($rparameters['debug']) {echo $query;}
													$query = $db->query($query);
												}
												else
												{$query = $db->query("SELECT tservices.id,tservices.name FROM tservices WHERE disable='0' ORDER BY name");}

												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
													if($_POST['sender_service']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
								//display filter of type column
								if($rright['dashboard_col_type'])
								{
									echo '
										<td align="center" id="type_filter">
											<select class="form-control" style="width:92px" name="type" onchange="submit()">
												<option value="%"></option>';
												//display type list
												$qry=$db->prepare("SELECT `id`,`name` FROM `ttypes` ORDER BY name");
												$qry->execute();
												while($row=$qry->fetch())
												{
													if($_POST['type']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
												}
												$qry->closeCursor();
												echo '
											</select>
										</td>
									';
								}
								//display filter of category column
								if($rright['dashboard_col_category'])
								{
									echo '
									<td align="center" id="category_filter">
										<select class="form-control" style="width:65px" name="category" onchange="submit()" >
											<option value="%"></option>
											';
												if($join)
												{$query="SELECT DISTINCT tcategory.id,tcategory.name FROM tcategory INNER JOIN tincidents ON tincidents.category=tcategory.id $join WHERE $where ORDER BY tcategory.name";}
												else
												{$query="SELECT tcategory.id,tcategory.name FROM tcategory ORDER BY name";}
												if($rparameters['debug']) {echo $query;}
												$query = $db->query($query);
												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
													if($_POST['category']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
								//display filter of subcat column
								if($rright['dashboard_col_subcat'])
								{
									echo'
									<td align="center">
										<select class="form-control" style="width:60px" name="subcat" onchange="submit()">
											<option value="%"></option>
											';
												if($_POST['category']!='%')
												{
													if($join)
													{$query="SELECT DISTINCT tsubcat.id,tsubcat.name FROM tsubcat INNER JOIN tincidents ON tincidents.subcat=tsubcat.id $join WHERE $where AND cat LIKE $_POST[category] ORDER BY tsubcat.name";}
													else
													{$query="SELECT tsubcat.id,tsubcat.name FROM tsubcat WHERE cat LIKE $_POST[category] ORDER BY name";}
												}
												else
												{
													if($join)
													{$query="SELECT DISTINCT tsubcat.id,tsubcat.name FROM tsubcat INNER JOIN tincidents ON tincidents.subcat=tsubcat.id $join WHERE $where AND tsubcat.name!='' ORDER BY tsubcat.name";}
													else
													{$query="SELECT tsubcat.id,tsubcat.name FROM tsubcat ORDER BY name";}
												}
												if($rparameters['debug']) {echo $query;}
												$query = $db->query($query);
												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
													if($_POST['subcat']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
								//display filter of asset column
								if($rright['dashboard_col_asset'])
								{
									echo '
									<td align="center">
										<select class="form-control" style="width:65px" name="asset" onchange="submit()" >
											<option value="%"></option>
											';
												if($join)
												{$query = $db->query("SELECT DISTINCT tassets.id,tassets.netbios FROM tassets INNER JOIN tincidents ON tincidents.asset_id=tassets.id $join WHERE $where AND netbios!='' ORDER BY tassets.netbios");}
												else
												{$query = $db->query("SELECT DISTINCT tassets.id,tassets.netbios FROM tassets WHERE netbios!='' ORDER BY netbios");}
												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['netbios']=T_($row['netbios']);} //translate only none database value
													if($_POST['asset']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['netbios'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['netbios'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
							?>
							<?php if($rparameters['ticket_places']==1){ ?>
								<td align="center">
									<select class="form-control" style="width:65px" name="place" onchange="submit()" >
										<option value="%"></option>
										<?php
										if($join)
										{$query = $db->query("SELECT DISTINCT tplaces.id,tplaces.name FROM tplaces INNER JOIN tincidents ON tincidents.place=tplaces.id $join WHERE $where ORDER BY tplaces.name");}
										else
										{$query = $db->query("SELECT tplaces.id,tplaces.name FROM tplaces ORDER BY name");}
										while ($row=$query->fetch())
										{
											if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
											if($_POST['place']==$row['id'])
											{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
											else
											{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
										}
										?>
									</select>
								</td>
							<?php } ?>
							<?php
								//display filter for service column
								if($rright['dashboard_col_service'])
								{
									echo'
									<td align="center">
										<select class="form-control" style="width:60px" name="service" onchange="submit()">
											<option value="%"></option>
											';
												if($join && $_GET['order']!='tservices.name')
												{
													$query="SELECT DISTINCT tservices.id,tservices.name FROM tservices INNER JOIN tincidents ON tincidents.u_service=tservices.id $join WHERE $where AND tservices.disable='0' ORDER BY tservices.name";
													if($rparameters['debug']) {echo $query;}
													$query = $db->query($query);
												}
												else
												{$query = $db->query("SELECT tservices.id,tservices.name FROM tservices WHERE disable='0' ORDER BY name");}
												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
													if($_POST['service']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
								/* /!\ new */
								/* affichage adress1 (site geographique)

									echo '
									<td align="center" >
										<select class="form-control" style="width:60px" name="address1" onchange="submit()">
											<option value="%"></option>';
											//display address1 list
											if ($join)
											{
												$query="SELECT DISTINCT tusers.address1 FROM tusers INNER JOIN tincidents ON tusers.id=tincidents.user INNER JOIN tcompany ON tusers.company=tcompany.id INNER JOIN tthreads ON tincidents.id=tthreads.ticket $join WHERE $where ORDER BY tusers.address1";
												if(preg_match('#FROM tusers#',$query)) {$query=str_replace("LEFT JOIN tusers ON tincidents.user=tusers.id","",$query);} //avoid company columun pb
												if(preg_match('#INNER JOIN tcompany#',$query)) {$query=str_replace("LEFT JOIN tcompany ON tusers.company=tcompany.id","",$query);} //avoid company columun pb
												if($rparameters['debug']==1) {echo $query;}
												$query = $db->query($query);
											}
											else
											{
												$query = $db->query("SELECT tusers.id,tusers.firstname,tusers.lastname FROM tusers WHERE disable='0' ORDER BY lastname");} //query for searchengine
											while ($row=$query->fetch())
											{
												if ($_POST['address1']==$row['address1'])
												{echo '<option selected value="'.$row['address1'].'">'.$row['address1'].'</option>';}
												else
												{echo '<option value="'.$row['address1'].'">'.$row['address1'].'</option>';}
											}
											echo '
										</select>
										</td>';
								//*/

								/* /!\ fin new */
								//display filter of agency column
								if($rright['dashboard_col_agency'])
								{
									echo'
									<td align="center">
										<select class="form-control" style="width:60px" name="agency" onchange="submit()">
											<option value="%"></option>
											';
												if($join)
												{$query="SELECT DISTINCT tagencies.id,tagencies.name FROM tagencies INNER JOIN tincidents ON tincidents.u_agency=tagencies.id $join WHERE $where AND tagencies.disable='0' ORDER BY tagencies.name";}
												else
												{$query="SELECT tagencies.id,tagencies.name FROM tagencies WHERE tagencies.disable='0' ORDER BY name";}
												if($rparameters['debug']) {echo $query;}
												$query = $db->query($query);
												while ($row=$query->fetch())
												{
													if($row['id']==0) {$row['name']=T_($row['name']);} //translate only none database value
													if($_POST['agency']==$row['id'])
													{echo '<option selected value="'.$row['id'].'">'.$row['name'].'</option>';}
													else
													{echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';}
												}
											echo '
										</select>
									</td>
									';
								}
							?>
							<td>
								<input class="form-control" name="title" style="width:100%" onchange="submit();" type="text"  value="<?php if($_POST['title']!='%')echo $_POST['title']; ?>" />
							</td>
							<?php
								//display filter of date create column
								if($rright['dashboard_col_date_create'])
								{
									if($_POST['date_create']!='%' && $_POST['date_create']  && $_POST['date_create']!='current')
									{
										//format date if detect
										if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['date_create']))
										{
											//convert date format to display format
											$_POST['date_create']=DateTime::createFromFormat('Y-m-d', $_POST['date_create']);
											$_POST['date_create']=$_POST['date_create']->format('d/m/Y');
										}
									}
									echo '
									<td>
										<center>
											<input class="form-control" title="'.T_('La date doit être au format JJ/MM/AAAA').'" name="date_create" style="max-width:130px;" onchange="submit();" type="text"  value="'; if($_POST['date_create']!='%' && !$_GET['view']) {echo $_POST['date_create'];} echo '" />
										</center>
									</td>
									';
								}
								//display filter of date hope column
								if($rright['dashboard_col_date_hope'])
								{
									if($_POST['date_hope']!='%' && $_POST['date_hope'])
									{
										//convert date format to display format
										if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['date_hope']))
										{
											$_POST['date_hope']=DateTime::createFromFormat('Y-m-d', $_POST['date_hope']);
											$_POST['date_hope']=$_POST['date_hope']->format('d/m/Y');
										}
									}
									echo '
									<td>
										<center>
											<input class="form-control" title="'.T_('La date doit être au format JJ/MM/AAAA').'" name="date_hope" style="max-width:130px;" onchange="submit();" type="text"  value="'; if($_POST['date_hope']!='%' && !$_GET['view']) {echo $_POST['date_hope'];} echo '" />
										</center>
									</td>
									';
								}
								//display filter of date res column
								if($rright['dashboard_col_date_res'])
								{
									if($_POST['date_res']!='%' && $_POST['date_res'])
									{
										//convert date format to display format
										if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$_POST['date_res']))
										{
											$_POST['date_res']=DateTime::createFromFormat('Y-m-d', $_POST['date_res']);
											$_POST['date_res']=$_POST['date_res']->format('d/m/Y');
										}
									}
									echo '
									<td>
										<center>
											<input class="form-control" title="'.T_('La date doit être au format JJ/MM/AAAA').'" name="date_res" style="max-width:130px;" onchange="submit();" type="text"  value="'; if($_POST['date_res']!='%' && !$_GET['view']) {echo $_POST['date_res'];} echo '" />
										</center>
									</td>
									';
								}
								//display filter of time column
								if($rright['dashboard_col_time'])
								{
									echo '
									<td>
										<center>
											<input class="form-control" title="'.T_('Le temps doit être renseigné en minutes').'" name="time" style="max-width:130px;" onchange="submit();" type="text"  value="'; if($_POST['time']!='%' && !$_GET['view']) {echo $_POST['time'];} echo '" />
										</center>
									</td>
									';
								}
                                //display filter of start date column
                                if ($_POST['date_start'] != '%' && $_POST['date_start'] && $_POST['date_start'] != 'current') {
                                    //format date if detect
                                    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST['date_start'])) {
                                        //convert date format to display format
                                        $_POST['date_start'] = DateTime::createFromFormat('Y-m-d', $_POST['date_start']);
                                        $_POST['date_start'] = $_POST['date_start']->format('d/m/Y');
                                    }
                                }
                                echo '
                                        <td>
                                            <center>
                                                <input class="form-control" title="' . T_('La date doit être au format JJ/MM/AAAA') . '" name="date_start" style="max-width:130px;" onchange="submit();" type="text"  value="';
                                                if ($_POST['date_start'] != '%' && !$_GET['view']) {
                                                    echo $_POST['date_start'];
                                                }
                                echo '" />
                                            </center>
                                        </td>
                                ';
                            ?>
							<td align="center">
								<select class="form-control" style="width:50px" id="state" name="state" onchange="submit()" >
									<option value=""></option>
									<?php
									if($join)
									//{$query = $db->query("SELECT DISTINCT tstates.id,tstates.number,tstates.name FROM tstates INNER JOIN tincidents ON tincidents.state=tstates.id INNER JOIN tusers ON tusers.id=tincidents.user INNER JOIN tcompany ON tusers.company=tcompany.id  INNER JOIN tthreads ON tincidents.id=tthreads.ticket WHERE $where ORDER BY tstates.number");}
									{
										$query="SELECT DISTINCT tstates.id,tstates.number,tstates.name FROM tstates INNER JOIN tincidents ON tincidents.state=tstates.id INNER JOIN tusers ON tusers.id=tincidents.user INNER JOIN tcompany ON tusers.company=tcompany.id  INNER JOIN tthreads ON tincidents.id=tthreads.ticket $join WHERE $where ORDER BY tstates.number";
										if(preg_match('#INNER JOIN tusers#',$query)) {$query=str_replace("LEFT JOIN tusers ON tincidents.user=tusers.id","",$query);} //avoid company column pb
										if(preg_match('#INNER JOIN tcompany#',$query)) {$query=str_replace("LEFT JOIN tcompany ON tusers.company=tcompany.id","",$query);} //avoid company column pb
										if(preg_match('#LEFT JOIN tstates ON tincidents.state=tstates.id#',$query)) {$query=str_replace('LEFT JOIN tstates ON tincidents.state=tstates.id','',$query);}
										if($rparameters['debug']) {echo $query;}
										$query = $db->query($query);
									}
									else
									{
										$query = $db->query("SELECT tstates.id,tstates.number,tstates.name FROM tstates ORDER BY name");
									}
									//display each value of query
									while ($row=$query->fetch())  {
										if($_POST['state']==$row['id']) {
										echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';
										} else {
										echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
										}
									}
									//special case meta state
									if($_GET['state']=='meta') {echo '<option selected value="meta">'.T_('A traiter').'</option>';}
									?>
								</select>
							</td>
							<?php
								//display filter of priority column
								if($rright['dashboard_col_priority'])
								{
									echo '
									<td align="center">
										<select class="form-control" style="width:45px" id="priority" name="priority" onchange="submit()">
											<option value=""></option>
											';
											if($join)
											{$query = $db->query("SELECT DISTINCT tpriority.id,tpriority.name FROM tpriority INNER JOIN tincidents ON tincidents.priority=tpriority.id $join WHERE $where ORDER BY tpriority.number");}
											else
											{$query = $db->query("SELECT tpriority.id,tpriority.name FROM tpriority ORDER BY number");}
											while ($row=$query->fetch()){
												if($_POST['priority']==$row['id']) echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
											}
											echo '
										</select>
									</td>
									';
								}
								//display filter of criticality column
								if($rright['dashboard_col_criticality'])
								{
									echo '
									<td align="center">
										<select class="form-control" style="width:50px" id="criticality" name="criticality" onchange="submit()">
											<option value=""></option>
											';
											if($join)
											{$query = $db->query("SELECT DISTINCT tcriticality.id,tcriticality.number,tcriticality.name FROM tcriticality INNER JOIN tincidents ON tincidents.criticality=tcriticality.id $join WHERE $where ORDER BY tcriticality.number");}
											else
											{$query = $db->query("SELECT `id`,`name` FROM tcriticality ORDER BY number");}
											while ($row=$query->fetch())
											{
												if($_POST['criticality']==$row['id']) echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>'; else echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
											}
										echo '
										</select>
									</td>
									';
								}
							?>
						</tr>
						<input name="filter" type="hidden" value="on" />
					</form>
				</thead>
				<tbody>
<?php
//var_dump($masterquery);
//var_dump($_POST);
?>
				<form name="actionlist" method="POST">
					<?php
					if($reload==0)
					{
						while ($row=$masterquery->fetch())
						{
							//select name of states
							$qry=$db->prepare("SELECT `display`,`description`,`name` FROM `tstates` WHERE `id`=:id");
							$qry->execute(array('id' => $row['state']));
							$resultstate=$qry->fetch();
							$qry->closeCursor();
							if(empty($resultstate['display'])) {$resultstate['display']='';}
							if(empty($resultstate['description'])) {$resultstate['description']='';}
							if(empty($resultstate['name'])) {$resultstate['name']='';}
							

							if($rright['dashboard_col_priority'])
							{
								//select name of priority
								$qry=$db->prepare("SELECT `name`,`color` FROM `tpriority` WHERE `id`=:id");
								$qry->execute(array('id' => $row['priority']));
								$resultpriority=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultpriority['name'])) {$resultpriority['name']='';}
								if(empty($resultpriority['color'])) {$resultpriority['color']='';}
							}
							//select name of user
							//!\ origin - $qry=$db->prepare("SELECT `id`,`phone`,`lastname`,`firstname` FROM `tusers` WHERE `id`=:id");
							/*/!\ New */ $qry=$db->prepare("SELECT `id`,`phone`,`lastname`,`firstname`,`address1` FROM `tusers` WHERE `id`=:id");
							$qry->execute(array('id' => $row['user']));
							$resultuser=$qry->fetch();
							$qry->closeCursor();
							if(empty($resultuser['id'])) {$resultuser['id']=0;}
							if(empty($resultuser['lastname'])) {$resultuser['lastname']='';}
							if(empty($resultuser['firstname'])) {$resultuser['firstname']='';}
							if(empty($resultuser['phone'])) {$resultuser['phone']='';}

							//select name of user group
							$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `id`=:id");
							$qry->execute(array('id' => $row['u_group']));
							$resultusergroup=$qry->fetch();
							$qry->closeCursor();
							if($resultusergroup === FALSE) { $resultusergroup= Array(); }
							if(empty($resultusergroup['id'])) {$resultusergroup['id']=0;}
							if(empty($resultusergroup['name'])) {$resultusergroup['name']='';}

							//select name of technician
							$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE `id`=:id");
							$qry->execute(array('id' => $row['technician']));
							$resulttech=$qry->fetch();
							$qry->closeCursor();
							if(empty($resulttech['id'])) {$resulttech['id']=0;}
							if(empty($resulttech['lastname'])) {$resulttech['lastname']='';}
							if(empty($resulttech['firstname'])) {$resulttech['firstname']='';}

							//test if attachment exist and display in title col
							$qry=$db->prepare("SELECT `id` FROM `tattachments` WHERE `ticket_id`=:ticket_id");
							$qry->execute(array('ticket_id' => $row['id']));
							$attachment_exist=$qry->fetch();
							$qry->closeCursor();
							if(!empty($attachment_exist)) {$attachment_exist='&nbsp;<i title="'.T_('Une pièce jointe est associée à ce ticket').'" class="fa fa-paperclip text-primary-m2"></i>';} else {$attachment_exist='';}

							//select name of technician group
							if($row['t_group'])
							{
								$qry=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `id`=:id");
								$qry->execute(array('id' => $row['t_group']));
								$resulttechgroup=$qry->fetch();
								$qry->closeCursor();
								if(empty($resulttechgroup['id'])) {$resulttechgroup['id']=0;}
								if(empty($resulttechgroup['name'])) {$resulttechgroup['name']='';}
							} else {$resulttechgroup['id']=0; $resulttechgroup['name']=T_('Aucun');}

							if($rright['dashboard_col_category'])
							{
								//select name of category
								$qry=$db->prepare("SELECT `name` FROM `tcategory` WHERE `id`=:id");
								$qry->execute(array('id' => $row['category']));
								$resultcat=$qry->fetch();
								$qry->closeCursor();
								if ($row['category']==0) {$resultcat['name']=T_($resultcat['name']);}
								if(empty($resultcat['name'])) {$resultcat['name']=T_('Aucune');}
                            }
							if($rright['dashboard_col_subcat'])
							{
								//select name of subcategory
								$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE `id`=:id");
								$qry->execute(array('id' => $row['subcat']));
								$resultscat=$qry->fetch();
								$qry->closeCursor();
								//if($row['subcat']==0) {$resultscat['name']=T_($resultscat['name']);}
								if($resultscat === FALSE) { $resultscat= Array(); }
								if(empty($resultscat['name'])) {$resultscat['name']=T_('Aucune')."(".$row['subcat'].")";}
							}
							if($rright['dashboard_col_asset'])
							{
								//select name of asset
								$qry=$db->prepare("SELECT `netbios` FROM `tassets` WHERE `id`=:id");
								$qry->execute(array('id' => $row['asset_id']));
								$resultasset=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultasset['netbios'])) {$resultasset['netbios']='';}
							}
							if($rright['dashboard_col_criticality'])
							{
								//select name of criticality
								$qry=$db->prepare("SELECT `name`,`color` FROM `tcriticality` WHERE `id`=:id");
								$qry->execute(array('id' => $row['criticality']));
								$resultcriticality=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultcriticality['name'])) {$resultcriticality['name']='';}
								if(empty($resultcriticality['color'])) {$resultcriticality['color']='';}
							}
							if($rright['dashboard_col_type'])
							{
								//select name of type
								$qry=$db->prepare("SELECT `name` FROM `ttypes` WHERE `id`=:id");
								$qry->execute(array('id' => $row['type']));
								$resulttype=$qry->fetch();
								$qry->closeCursor();
								if(empty($resulttype['name'])) {$resulttype['name']='';}
							}
							if($rparameters['ticket_places']) {
								//select name of place
								$qry=$db->prepare("SELECT `name` FROM `tplaces` WHERE `id`=:id");
								$qry->execute(array('id' => $row['place']));
								$resultplace=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultplace['name'])) {$nameplace=T_('Aucun');} else {$nameplace = $resultplace['name'];}
							}
							if($rright['dashboard_col_service'])
							{
								//select name of service
								$qry=$db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
								$qry->execute(array('id' => $row['u_service']));
								$resultservice=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultservice['name'])) {$nameservice=T_('Aucun');} else {$nameservice = $resultservice['name'];}
							}
							if($rright['dashboard_col_user_service'])
							{
								//get user service data
								$qry=$db->prepare("SELECT `name` FROM `tservices` WHERE `id`=:id");
								$qry->execute(array('id' => $row['sender_service']));
								$resultsenderservice=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultsenderservice['name'])) {$name_sender_service=T_('Aucun');} else {$name_sender_service = $resultsenderservice['name'];}
							}
							if($rright['dashboard_col_agency'])
							{
								//select name of agency
								$qry=$db->prepare("SELECT `name` FROM `tagencies` WHERE `id`=:id");
								$qry->execute(array('id' => $row['u_agency']));
								$resultagency=$qry->fetch();
								$qry->closeCursor();
								if(empty($resultagency['name'])) {$nameagency=T_('Aucune');} else {$nameagency = $resultagency['name'];}
							}

							if($rright['dashboard_firstname'])
							{
								$Fname=$resultuser['firstname'];
								$Ftname=$resulttech['firstname'];

							} else {
								//cut first letter of firstname
								if(extension_loaded('mbstring')) {
									$Fname=mb_substr($resultuser['firstname'], 0, 1).'.';
									$Ftname=mb_substr($resulttech['firstname'], 0, 1).'.';
								} else {
									$Fname=substr($resultuser['firstname'], 0, 1).'.';
									$Ftname=substr($resulttech['firstname'], 0, 1).'.';
								}
							}


							//display user name or group name
							if($resultusergroup['id']!=0) {
								$displayusername="[G] $resultusergroup[name]";
							} else {
								if($resultuser['id']==0) {$displayusername=$resultuser['lastname'];} else {$displayusername=$Fname.' '.$resultuser['lastname'];}
							}
							if($resulttechgroup['id']!=0) {
								$displaytechname="[G] $resulttechgroup[name]";
							} else {
								if($resulttech['id']==0) {$displaytechname=$resulttech['lastname'];} else {$displaytechname=$Ftname.' '.$resulttech['lastname'];}
							}
							//display user company name
							if($rright['dashboard_col_company'])
							{
								$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE id=(SELECT `company` FROM `tusers` WHERE `id`=:user_id)");
								$qry->execute(array('user_id' => $row['user']));
								$resultusercompany=$qry->fetch();
								$qry->closeCursor();
								$displaycompanyname = $resultusercompany['name'];
							}
							//convert SQL date to human readable date
							$rowdate_hope= date_cnv($row['date_hope']);
							$rowdate_res= date_cnv($row['date_res']);
							// OC
//							$start_date_query = $db->prepare("SELECT date_start from dmission_order WHERE incident_id=".$row['id']);
//							$start_date_query->execute();
//							$result_date_query = $start_date_query->fetch();
//							$start_date_query->closeCursor();
//var_dump("SELECT date_start from dmission_order WHERE id=".$row['id']);
							//var_dump($result_date_query[0]);

							$rowdate_start = date_create($row['date_start']);
							$rowdate_start = date_format($rowdate_start, 'd/m/Y');
							if($rright['dashboard_col_date_create_hour']) //display hour in create date column
							{
								$rowdate_create=date_create($row['date_create']);
								$rowdate_create= date_format($rowdate_create, 'd/m/Y');
							} else {$rowdate_create= date_cnv($row['date_create']);}
							//date hope
							$late='';
							if($rright['ticket_date_hope_disp'])
							{
								if(!isset($row['date_hope'])) $row['date_hope']= '';

								$qry=$db->prepare("SELECT DATEDIFF(NOW(), :date_hope) ");
								$qry->execute(array('date_hope' => $row['date_hope']));
								$resultdiff=$qry->fetch();
								$qry->closeCursor();

								if(($resultdiff[0]>0) && ($row['state']!='3') && ($row['state']!='4')) $late = '<i title="'.$resultdiff[0].' '.T_('jours de retard').'" class="fa fa-clock fa-pulse text-warning-m2 "></i>';
							}
							//colorize ticket id and add tag
							$new_ticket='';
							$bgcolor='badge-primary'; //default value
							$comment='';
							if($_GET['view']=='activity')
							{
								//ticket open in selected period
								$date_create_day=explode(' ',$row['date_create']);
								$date_create_day=$date_create_day[0];
								if($date_create_day>=$date_start_db && $date_create_day<=$date_end_db) {$new_ticket='<i title="'.T_("Ticket ouvert dans la période sélectionnée le").' '.$rowdate_create.'" class="fa fa-certificate text-success-m2 "></i>';}
								//colorize ticket id in red for unread technician in selected period
								if($row['techread_date']!='0000-00-00 00:00:00') {
									if($row['techread_date']>"$date_end_db 23:59:59"){$bgcolor="badge-danger"; $comment=T_("Ticket non lu par le technicien en charge dans la période indiquée");}
								} elseif($row['techread']==0) {
									$bgcolor="badge-danger"; $comment=T_("Ticket non lu par le technicien en charge dans la période indiquée");
								}
								//colorize ticket id in orange read by technician but no text résolution in selected period
								if($row['techread_date']!='0000-00-00 00:00:00' || $row['techread']==1) {
									$date_start=$date_start_db.' 00:00:00';
									$date_end=$date_end_db.' 23:59:59';
									$qry=$db->prepare("SELECT id FROM tthreads WHERE ticket=:ticket AND date BETWEEN :date_start AND :date_end AND type='0' AND author=:author");
									$qry->execute(array('ticket' => $row['id'],'date_start' => $date_start,'date_end' => $date_end,'author' => $row['technician']));
									$tech_add_res=$qry->rowCount();
									$qry->closeCursor();

									if($tech_add_res==0)
									{
										if($row['state']==3)
										{
											$bgcolor="badge-primary"; $comment=T_("Ticket avancé, qui à été fermé dans la période sélectionnée");
										} else {
											$bgcolor="badge-warning"; $comment=T_("Ticket lu par le technicien en charge mais aucun élément de réponse n'a été ajouté dans la période sélectionnée");
										}
									}
									else
									{$bgcolor="badge-primary"; $comment=T_("Ticket avancé, sur lequel un élément de résolution à été ajouté par le technicien en charge dans la période sélectionnée");}
								}
							} else {
								//ticket open today
								if(date('Y-m-d')==date('Y-m-d',strtotime($row['date_create']))) {$new_ticket='<i title="'.T_("Ticket ouvert aujourd'hui le").' '.$rowdate_create.'" class="fa fa-certificate text-success-m2 pt-1"></i>';}
								//colorize ticket id
								if($row['techread']==0 && $row['t_group']==0)
								{
									$bgcolor="badge-danger"; $comment=T_("Ticket non lu par le technicien en charge");
								} elseif(date('Y-m-d')==date('Y-m-d',strtotime($row['date_res'])))
								{
									$bgcolor="badge-success"; $comment=T_("Ticket fermé aujourd'hui");
								} else {
									$date=date('Y-m-d').'%';
									$qry=$db->prepare("SELECT id FROM tthreads WHERE ticket=:ticket AND date LIKE :date AND type='0' AND author=:author");
									$qry->execute(array('ticket' => $row['id'],'date' => $date,'author' => $row['technician']));
									$today_res=$qry->fetch();
									$qry->closeCursor();

									if($today_res!=0)
									{
										$bgcolor="badge-primary"; $comment=T_("Couleur par défaut des tickets");
									} else {
										$qry=$db->prepare("SELECT `id` FROM `tthreads` WHERE ticket=:ticket AND type='0' AND author=:author");
										$qry->execute(array('ticket' => $row['id'],'author' => $row['technician']));
										$tech_add_res=$qry->rowCount();
										$qry->closeCursor();
										if($tech_add_res==0) {$bgcolor="badge-warning"; $comment=T_("Ticket lu par le technicien en charge mais aucun élément de réponse n'a été ajouté");}
									}
								}
							}
							if($comment=='') {$comment='Couleur par défaut des tickets';}

							$title=htmlentities($row['title']);

							//display warning if date res hope is null and if it's mandatory field
							if($rright['ticket_date_hope_mandatory'] && ($row['date_hope']=='0000-00-00') && ($row['technician']!='0') && ($row['state']!='3') && ($row['state']!='4')) {$warning_hope='<i title="'.T_("La date de résolution estimée n'a pas été renseignée").'" class="fa fa-exclamation-triangle text-danger-m2 "></i> ';} else {$warning_hope='';}
							//generate open ticket link
							$open_ticket_link="./index.php?page=ticket&amp;id=$row[id]&amp;$url_post_parameters&amp;order=$_GET[order]&amp;way=$_GET[way]&amp;cursor=$_GET[cursor]";

                            // *********************************** DISPLAY EACH LINE **************************************
							echo '
								<tr class="bgc-h-default-l3 d-style">
									<td class="text-left pr-0 pos-rel">
										<div class="position-tl h-100 ml-n1px border-l-4 brc-info-m1 v-hover"></div>
										';
										//display checkbox for each line
										if($rright['task_checkbox']) {
											if($_POST['selectrow']=='selectall') {$checked='checked';} else {$checked='';}
											echo '<input class="mt-1" type="checkbox" name="checkbox'.$row['id'].'" value="'.$row['id'].'" '.$checked.' />';
										}
										echo '
										&nbsp<a href="'.$open_ticket_link.'"><span style="min-width:30px" title="'.$comment.'" class="badge '.$bgcolor.' "><span style="color:#FFF;">'.$row['id'].'</span></span></a>
										'.$new_ticket.'
										'.$late.'
										'.$warning_hope.'
									</td>
									';
									//display tech
									if($_SESSION['profile_id']!=0 || $_SESSION['profile_id']!=4 || $_GET['userid']=='%')
									{
                                        echo '<td onclick="document.location=\' '.$open_ticket_link.' \'" ><center><a class="td" title="'.$resulttech['firstname'].' '.$resulttech['lastname'].'" href="'.$open_ticket_link.'">'.T_(" $displaytechname").'</a></center></td>';
                                    }
									//display user company
									/*
									if($rright['dashboard_col_company'])
									{
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'"><center><a class="td" href="'.$open_ticket_link.'">'.T_(" $displaycompanyname").'</a></center></td>';
									}
									//*/
									//display user
									//!\OLD - if(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all'] && ($_GET['userid']=='%'|| $keywords!='')) || ($rparameters['user_company_view']!=0 && $_GET['userid']=='%' && ($rright['side_company'] || $keywords!='')))
									/*/!\ AJOUTER PAR NOS SOINS */
									if(($_SESSION['profile_id']==0 || $_SESSION['profile_id']==1 || $_SESSION['profile_id']==2 || $_SESSION['profile_id']==3 || $_SESSION['profile_id']==4) || ($rright['side_all'] && ($_GET['userid']=='%'|| $keywords!='')) || ($rparameters['user_company_view']!=0 && $_GET['userid']=='%' && ($rright['side_company'] || $keywords!='')))
									{
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'"><center><a class="td" title="'.$resultuser['firstname'].' '.$resultuser['lastname'].' '.T_('Tel').': '.$resultuser['phone'].' " href="'.$open_ticket_link.'">'.T_(" $displayusername").'</a></center></td>';
                                    }
									//display user service
									if($rright['dashboard_col_user_service']) {
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<center><a class="td" href="'.$open_ticket_link.'">'.T_($name_sender_service).'</a></center>
										</td>';
									}
									//display type
									if($rright['dashboard_col_type'])
									{
										echo'
										<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<a class="td" href="'.$open_ticket_link.'">'.T_($resulttype['name']).'</a>
										</td>
										';
									}
									//display category
									if($rright['dashboard_col_category'])
									{
										echo'
											<td onclick="document.location=\' '.$open_ticket_link.' \'">
												<a class="td" href="'.$open_ticket_link.'">'.$resultcat['name'].'</a>
											</td>
										';
									}
									//display subcat
									if($rright['dashboard_col_subcat'])
									{
										echo'
											<td onclick="document.location=\' '.$open_ticket_link.' \'">
												<a class="td" href="'.$open_ticket_link.'">'.$resultscat['name'].'</a>
											</td>
										';
									}
									//display asset
									if($rright['dashboard_col_asset'])
									{
										echo'
											<td onclick="document.location=\' '.$open_ticket_link.' \'">
												<a class="td" href="'.$open_ticket_link.'">'.$resultasset['netbios'].'</a>
											</td>
										';
									}
									//display place
									if($rparameters['ticket_places']){
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<center><a class="td" href="'.$open_ticket_link.'">'.T_($nameplace).'</a></center>
										</td>';
									}
									//display service
									if($rright['dashboard_col_service']){
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<center><a class="td" href="'.$open_ticket_link.'">'.T_($nameservice).'</a></center>
										</td>';
									}
									/* /!\ new */
									//display address (site geographique)
									/*
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<center><a class="td" href="'.$open_ticket_link.'">'.T_($resultuser['address1']).'</a></center>
										</td>';
									/* /!\ fin new */
									//display agency
									if($rright['dashboard_col_agency']){
										echo '<td onclick="document.location=\' '.$open_ticket_link.' \'">
											<center><a class="td" href="'.$open_ticket_link.'">'.T_($nameagency).'</a></center>
										</td>';
									}
									//display title
									echo "<td onclick=\"document.location='$open_ticket_link'\">
										<a class=\"td\" title=\"$title \" href=\"$open_ticket_link\">$title</a>
										$attachment_exist
									</td>
									";
									//display date create
									if($rright['dashboard_col_date_create']){
										echo "
										<td onclick=\"document.location='$open_ticket_link'\">
											<center><a class=\"td\" href=\"$open_ticket_link\">$rowdate_create</a></center>
										</td>
										";
									}
									// OC
									echo '<td onclick="document.location='.$open_ticket_link.'">';
									echo '    <center><a class="td" href="'.$open_ticket_link.'">'.$rowdate_start.'</a></center>';
									echo '</td>';
									//display date hope
									if($rright['dashboard_col_date_hope']){
										echo "
										<td onclick=\"document.location='$open_ticket_link'\">
											<center><a class=\"td\" href=\"$open_ticket_link\">$rowdate_hope</a></center>
										</td>
										";
									}
									//display resolution date
									if($rright['dashboard_col_date_res'])
									{
										echo "
										<td onclick=\"document.location='$open_ticket_link'\">
											<center><a class=\"td\" href=\"$open_ticket_link\">$rowdate_res</a></center>
										</td>
										";
									}
									//display time
									if($rright['dashboard_col_time'])
									{
										echo '
										<td onclick="document.location=\''.$open_ticket_link.'\'">
											<center><a class="td" href="'.$open_ticket_link.'">'.MinToHour($row['time']).'</a></center>
										</td>
										';
									}
									//display state
									echo '
									<td onclick="document.location=\''.$open_ticket_link.'\'">
										<center><a class="td" href="'.$open_ticket_link.'"><span class="'.$resultstate['display'].'" title="'.T_($resultstate['description']).'">'.T_($resultstate['name']).'</a></center>
									</td>
									';
									//display priority
									if($rright['dashboard_col_priority'])
									{
										echo '
										<td onclick="document.location=\''.$open_ticket_link.'\'">
											<center><a title="'.T_('Priorité').' '.T_($resultpriority['name']).'" class="td" href="'.$open_ticket_link.'"> <i title="'.T_($resultpriority['name']).'" class="fa fa-exclamation-triangle" style="color:'.$resultpriority['color'].'"></i></a></center>
										</td>
										';
									}
									//display criticality
									if($rright['dashboard_col_criticality'])
									{
										echo '
										<td onclick="document.location=\''.$open_ticket_link.'\'">
											<a title="'.T_('Criticité').' '.T_($resultcriticality['name']).'" class="td" href="'.$open_ticket_link.'" > <center><i title="'.T_($resultcriticality['name']).'" class="fa fa-bullhorn" style="color:'.$resultcriticality['color'].'" ></i></a></center>
										</td>
										';
									}
									echo '
								</tr>
							';
						}
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="row">
	<?php
	//display multi check options
	if($rright['task_checkbox'] && $resultcount[0]>0)
	{
		echo '
			<i class="fa fa-level-down-alt fa-rotate-180 text-130 mb-3 ml-2 pr-4 text-secondary-d2"></i>&nbsp&nbsp&nbsp
			<select style="width:auto" class="form-control form-control-sm mt-4" title="'.T_('Effectue des actions pour les tickets sélectionnés dans la liste des tickets').'." name="selectrow" onchange="if(confirm(\''.T_('Êtes-vous sûr de réaliser cette opération sur les tickets sélectionnés').'?\')) this.form.submit();">
				<option value="selectall"> > '.T_('Sélectionner tout').'</option>
				<option selected> > '.T_('Pour la sélection').' :</option>
				';
				if($rright['ticket_delete']){
					echo '<option value="delete">'.T_('Supprimer').'</option>';
				}
				//display list of ticket states
				$qry=$db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY name");
				$qry->execute();
				while($row=$qry->fetch())
				{
					echo '<option value="'.$row['id'].'">'.T_('Marquer comme').' "'.T_($row['name']).'"</option>';
				}
				$qry->closeCursor();
				echo '
			</select>
		';
	}
	echo "</form>"; //end form for task_checkbox
	echo '
       <form action="/" method="post">
               <input type="hidden" name="query" value="'.base64_encode($queryForDownload).'">          
               <button type="submit" class="btn btn-outline-success btn-sm mt-4" name="download" value="download">Télécharger</button>
       </form>
</div> <!-- end row -->';
	//multi-pages link
	if($resultcount[0]>$rparameters['maxline'])
	{
		//count number of page
		$total_page=ceil($resultcount[0]/$rparameters['maxline']);
		echo '
		<div class="row justify-content-center mt-4">
			<nav aria-label="Page navigation">
				<ul class="pagination nav-tabs-scroll is-scrollable mb-0">';
					//display previous button if it's not the first page
					if($_GET['cursor']!=0)
					{
						$cursor=$_GET['cursor']-$rparameters['maxline'];
						echo '<li class="page-item"><a class="page-link" title="'.T_('Page précédente').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'"><i class="fa fa-arrow-left"></i></a></li>';
					}
					//display first page
					if($_GET['cursor']==0){$active='active';} else {$active='';}
					echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Première page').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor=0">&nbsp;1&nbsp;</a></li>';
					//calculate current page
					$current_page=($_GET['cursor']/$rparameters['maxline'])+1;
					//calculate min and max page
					if(($current_page-3)<3) {$min_page=2;} else {$min_page=$current_page-3;}
					if(($total_page-$current_page)>3) {$max_page=$current_page+4;} else {$max_page=$total_page;}
					//display all pages links
					for ($page = $min_page; $page <= $total_page; $page++) {
						//display start "..." page link
						if(($page==$min_page) && ($current_page>5)){echo '<li class="page-item"><a class="page-link" title="'.T_('Pages masqués').'" href="">&nbsp;...&nbsp;</a></li>';}
						//init cursor
						if($page==1) {$cursor=0;}
						$selectcursor=$rparameters['maxline']*($page-1);
						if($_GET['cursor']==$selectcursor){$active='active';} else	{$active='';}
						$cursor=(-1+$page)*$rparameters['maxline'];
						//display page link
						if($page!=$max_page) echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Page').' '.$page.'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'">&nbsp;'.$page.'&nbsp;</a></li>';
						//display end "..." page link
						if(($page==($max_page-1)) && ($page!=$total_page-1)) {
							echo '<li class="page-item"><a class="page-link" title="'.T_('Pages masqués').'" href="">&nbsp;...&nbsp;</a></li>';
						}
						//cut if there are more than 3 pages
						if($page==($current_page+4)) {
							$page=$total_page;
						}
					}
					//display last page
					$cursor=($total_page-1)*$rparameters['maxline'];
					if($_GET['cursor']==$selectcursor){$active='active';} else	{$active='';}
					echo '<li class="page-item '.$active.'"><a class="page-link" title="'.T_('Dernière page').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'">&nbsp;'.$total_page.'&nbsp;</a></li>';
					//display next button if it's not the last page
					if($_GET['cursor']<($resultcount[0]-$rparameters['maxline']))
					{
						$cursor=$_GET['cursor']+$rparameters['maxline'];
						echo '<li class="page-item"><a class="page-link" title="'.T_('Page suivante').'" href="./index.php?page=dashboard&'.$url_post_parameters.'&amp;order='.$_GET['order'].'&amp;way='.$_GET['way'].'&amp;cursor='.$cursor.'"><i class="fa fa-arrow-right"></i></a></li>';
					}
					echo '
				</ul>
			</nav>
		</div>
	';
	if($rparameters['debug']){echo "<br /><b><u>DEBUG MODE</u></b><br />&nbsp;&nbsp;&nbsp;&nbsp;[Multi-page links] _GET[cursor]=$_GET[cursor] | current_page=$current_page | total_page=$total_page | min_page=$min_page | max_page=$max_page";}
	}
//play notify sound for tech and admin in new ticket case
if($rparameters['notify']==1 && ($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0) && $_GET['keywords']=='')
{
	$query="SELECT id FROM `tincidents` WHERE technician='0' and t_group='0' and techread='0' and disable='0' and notify='0' $where_agency $where_service $parenthese2";
	if($rparameters['debug']) {echo "[Notification] $query";}
	$query=$db->query($query);
	$row=$query->fetch();
	if(!empty($row[0])) {
		echo'<audio hidden="false" autoplay="true" src="./sounds/notify.ogg" controls="controls"></audio>';
		$qry=$db->prepare("UPDATE `tincidents` SET `notify`='1' WHERE `id`=:id");
		$qry->execute(array('id' => $row['id']));
	}
}

//display date picker for period selection in today tickets
if($_GET['view']=='activity')
{
	echo '
	<!-- datetime picker scripts  -->
	<script type="text/javascript" src="./components/moment/min/moment.min.js"></script>
	';
	if($ruser['language']=='fr_FR') {echo '<script src="./components/moment/locale/fr.js" charset="UTF-8"></script>';}
	if($ruser['language']=='de_DE') {echo '<script src="./components/moment/locale/de.js" charset="UTF-8"></script>';}
	if($ruser['language']=='es_ES') {echo '<script src="./components/moment/locale/es.js" charset="UTF-8"></script>';}
	echo '
	<script src="./components/tempus-dominus/build/js/tempusdominus-bootstrap-4.min.js" charset="UTF-8"></script>
	';
	echo "
	<script type=\"text/javascript\">
		var date = moment($('#date_start').val(), 'DD-MM-YYYY').toDate();
		$('#date_start').datetimepicker({ format: 'DD/MM/YYYY' });
		var date = moment($('#date_end').val(), 'DD-MM-YYYY').toDate();
		$('#date_end').datetimepicker({  format: 'DD/MM/YYYY' });

	</script>
	";
}
?>
