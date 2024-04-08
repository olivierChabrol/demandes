<?php
################################################################################
# @Name : userbar.php 
# @Description : ui for connected user
# @Call : /main.php
# @Author : Flox
# @Create : 26/12/2019
# @Update : 11/06/2020
# @Version : 3.2.2
################################################################################

//get agencies associated with this user
$qry=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
$qry->execute(array('user_id' => $_SESSION['user_id']));
$cnt_agency=$qry->rowCount();
$qry->closeCursor();

//get services associated with this user
$qry=$db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
$qry->execute(array('user_id' => $_SESSION['user_id']));
$cnt_service=$qry->rowCount();
$row=$qry->fetch();
$qry->closeCursor();
$operator='AND'; $parenthese1=''; $parenthese2='';

//special case to limit ticket to services
if($rright['dashboard_service_only'] && $rparameters['user_limit_service']) 
{
	$where_service='';
	$where_service_your='';
	if(!isset($_GET['userid'])) $_GET['userid'] = '';
	$user_services=array();
	if($cnt_service==0) {$where_service.='';}
	elseif($cnt_service==1) {
		if($_SESSION['profile_id']==0) //special case to allow technician to view ticket open for another service
		{
			$where_service_your.="AND (tincidents.u_service='$row[service_id]' OR tincidents.technician LIKE $db_userid OR tincidents.user LIKE $db_userid) ";
		} else {
			$where_service_your.="AND (tincidents.u_service='$row[service_id]' OR tincidents.$profile LIKE $db_userid) ";
		}
		if($_GET['page']=='dashboard' || $_GET['page']=='ticket') {
			$where_service.="$operator (tincidents.u_service='$row[service_id]' OR tincidents.user LIKE '$_SESSION[user_id]') "; //display service ticket + user tickets
		} else {
			$where_service.="$operator tincidents.u_service='$row[service_id]' "; //display service ticket + user tickets
		}
		array_push($user_services, $row['service_id']);
	} else {
		$cnt2=0;
		$where_service.="$operator (";
		$qry=$db->prepare("SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $_SESSION['user_id']));
		while($row=$qry->fetch()) 
		{
			$cnt2++;
			$where_service.="tincidents.u_service='$row[service_id]'";
			array_push($user_services, $row['service_id']);
			if($cnt_service!=$cnt2) $where_service.=' OR '; 
		}
		$qry->closeCursor();
		$where_service.=') ';
	}
} else {$where_service=''; $where_service_your=''; $user_services=''; $cnt_service='';}
//special case to limit ticket to agency
if($rright['dashboard_agency_only'])
{
	//get agencies associated with this user
	$qry=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
	$qry->execute(array('user_id' => $_SESSION['user_id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	$where_agency='';
	$where_agency_your='';
	if(!isset($_GET['userid'])) $_GET['userid'] = '';
	$user_agencies=array();
	if($cnt_agency==0) {$where_agency.='';}
	elseif($cnt_agency==1) {
		if($_SESSION['profile_id']==0) //special case for technician to view only our ticket sender and tech (avoid limit display ticket problem)
		{
			$where_agency_your.=" ";
		} else {
			$where_agency_your.="AND (tincidents.u_agency='$row[agency_id]' OR tincidents.$profile LIKE $db_userid)  ";
		}
		
		$where_agency.="AND $parenthese1 tincidents.u_agency='$row[agency_id]' ";
		array_push($user_agencies, $row['agency_id']);
	} else {
		$cnt2=0;
		$where_agency.="AND $parenthese1 (";
		$qry=$db->prepare("SELECT `agency_id` FROM `tusers_agencies` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $_SESSION['user_id']));
		while($row=$qry->fetch()) 
		{
			$cnt2++;
			$where_agency.="tincidents.u_agency='$row[agency_id]'";
			array_push($user_agencies, $row['agency_id']);
			if($cnt_agency!=$cnt2) $where_agency.=' OR '; 
		}
		$qry->closeCursor();
		$where_agency.=') ';
	}
} else {$where_agency=''; $where_agency_your=''; $user_agencies=''; $cnt_agency='';}

//get role of profiles
if($_SESSION['profile_id']==0)	{$profile="technician";}
elseif($_SESSION['profile_id']==1)	{$profile="user";}
elseif($_SESSION['profile_id']==4)	{$profile="technician";}
elseif($_SESSION['profile_id']==3) {$profile="user";}
else {$profile="user";}

//get counter data
$query="SELECT COUNT(`id`) FROM `tincidents` WHERE $profile='$uid' AND `state` LIKE '3' $where_agency $where_service $parenthese2 AND `disable`='0'";
$query=$db->query($query);
$nbres=$query->fetch();
$query->closeCursor(); 

$query=$db->query("SELECT COUNT(`id`) FROM `tincidents` WHERE $profile='$_SESSION[user_id]' $where_service_your $where_agency_your AND `techread`='0' AND `disable`='0'");
$cnt3=$query->fetch();
$query->closeCursor(); 

$query="SELECT COUNT(`id`) FROM `tincidents` WHERE `technician`='0' AND `t_group`='0' $where_agency $where_service $parenthese2 AND `disable`='0'";
$query=$db->query($query);
$cnt5=$query->fetch();
$query->closeCursor(); 

//meta state
$query="SELECT COUNT(`tincidents`.`id`) FROM `tincidents`,`tstates` WHERE `tincidents`.`state`=`tstates`.`id` AND $profile='$uid' AND `tstates`.`meta`='1' $where_agency $where_service $parenthese2 AND `tincidents`.`disable`='0'";
$query=$db->query($query);
$nbatt2=$query->fetch();
$query->closeCursor(); 

$label_meta=T_('Tickets dans les états').' : ';
$qry=$db->prepare("SELECT `name` FROM `tstates` WHERE `meta`='1'");
$qry->execute();
while($row=$qry->fetch()) {$label_meta.=T_($row['name']).' ';}
$qry->closeCursor();

$qry=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
$qry->execute(array('id' => $uid));
$reqfname=$qry->fetch();
$qry->closeCursor();

$query=$db->query("SELECT SUM(`time_hope`-`time`) FROM `tincidents` WHERE `time_hope`-`time`>0 AND `technician` LIKE '$uid' AND `disable`='0' AND (`state`='1' OR `state`='2' OR `state`='6') $where_agency $where_service $parenthese2");
$nbtps=$query->fetch();
$query->closeCursor();
if(!$nbtps[0]) {$nbtps[0]=0;} else {$nbtps[0]=round($nbtps[0]/60);}

$query=$db->query("SELECT SUM(`time_hope`-`time`) FROM `tincidents` WHERE `time_hope`-`time`>0 AND `technician` LIKE '$uid' AND `disable`='0' AND (`state`='1' OR `state`='2' OR `state`='6') $where_agency $where_service $parenthese2");
$ra1=$query->fetch();
$query->closeCursor();

$query=$db->query("SELECT COUNT(`id`) FROM `tincidents` WHERE `technician` LIKE '$uid' AND `date_create` LIKE '$daydate' $where_agency $where_service $parenthese2 AND `disable`='0';");
$ra2=$query->fetch();
$query->closeCursor();

$query=$db->query("SELECT COUNT(`id`) FROM `tincidents` WHERE `technician`='0' AND `t_group`='0' AND `techread`='0' $where_agency $where_service $parenthese2 AND `disable`='0'");
$nbun=$query->fetch();
$query->closeCursor();

if($rright['userbar']) //extended informations
{

	if($cnt5[0]>0 && $rright['side_your_not_attribute'])
	{
	echo'
		<li class="nav-item dropdown dropdown-mega">
			<a title="'.T_("Ticket en attente d'attribution").'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4" href="index.php?page=dashboard&amp;userid=0&amp;t_group=0&amp;state=%25" role="button" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-bell text-110 icon-animated-bell mr-lg-2"></i> 
				<span id="id-navbar-badge1" class="badge badge-sm badge-danger radius-round text-80 border-1 brc-white-tp5">'.$cnt5[0].'</span>
			</a>
		</li>
		';
		
	}
	if($cnt3[0]>0 && $rright['side_your_not_read'])
	{
	echo'
		<li class="nav-item dropdown dropdown-mega">
			<a title="'.T_('Tickets en attente de lecture').'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;techread=0" role="button" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-bookmark text-110 icon-animated-bell mr-lg-2"></i> 
				<span id="id-navbar-badge1" class="badge badge-sm badge-warning radius-round text-80 border-1 brc-white-tp5">'.$cnt3[0].'</span>
			</a>
		</li>
		';
	}
	echo'
	<li class="nav-item dropdown dropdown-mega">
		<a title="'.T_('Tickets ouverts, fermés ou sur lesquels un élément de résolution a été ajouté').'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4" href="index.php?page=dashboard&amp;userid=%25&amp;state=%25&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25&amp;view=activity&amp;date_start='.date("d/m/Y").'&date_end='.date("d/m/Y").'" role="button" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-calendar text-110 mr-lg-2"></i> 
			';if($mobile==0) {echo T_('Activité');} echo '
		</a>
	</li>
	';
	//generate in treatment state
	if($rparameters['meta_state']) {$link_meta_state='./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=meta';} else {$link_meta_state='./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=1';}
	echo'
	<li class="nav-item dropdown dropdown-mega">
		<a title="'.$label_meta.'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="'.$link_meta_state.'" role="button" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-flag text-110 mr-lg-2"></i> 
			'; 
			if(!$mobile) {echo T_('A traiter');}
			echo '
			<span id="id-navbar-badge1" class="badge badge-sm badge-secondary radius-round text-80 border-1 brc-white-tp5">'.$nbatt2[0].'</span>
		</a>
	</li>
	
	';
	//display current technician load if parameters are on
	if($rright['ticket_time_disp']!=0 && $rright['ticket_time_hope_disp']!=0)
	{
		echo '
		<li class="nav-item dropdown dropdown-mega">
			<a title="'.T_("Nombre d'heures de travail estimé dans vos tickets ouverts").'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=1" role="button" aria-haspopup="true" aria-expanded="false">
				<i class="fa fa-tachometer-alt text-110 mr-lg-2"></i> 
				'; 
				if(!$mobile) {echo T_('Charge');}
				echo '
				<span id="id-navbar-badge1" class="badge badge-sm badge-info radius-round text-80 border-1 brc-white-tp5">'.$nbtps[0].'h</span>
			</a>
		</li>
		';
	}
}

//display remain tickets for user ticket limit
if($rparameters['user_limit_ticket'] && $ruser['limit_ticket_number'] && $ruser['limit_ticket_days'] && $ruser['limit_ticket_date_start']!='0000-00-00' &&($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
{
	//generate date start and date end
	$date_start=$ruser['limit_ticket_date_start'];
	
	//calculate end date	
	$date_start_conv = date_create($ruser['limit_ticket_date_start']);
	date_add($date_start_conv, date_interval_create_from_date_string("$ruser[limit_ticket_days] days"));
	$date_end=date_format($date_start_conv, 'Y-m-d');

	//count number of ticket remaining in period
	$qry=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE user=:user AND date_create BETWEEN :date_start AND :date_end AND disable='0'");
	$qry->execute(array('user' => $_SESSION['user_id'],'date_start' => $date_start,'date_end' => $date_end));
	$nbticketused=$qry->fetch();
	$qry->closeCursor();
	
	//check number of tickets in current range date
	if(date('Y-m-d')>$date_end || date('Y-m-d')<$date_start){$nbticketremaining=0;} else {$nbticketremaining=$ruser['limit_ticket_number']-$nbticketused[0];}
	echo '
	<li class="nav-item dropdown dropdown-mega">
		<a title="'.T_('Nombre de tickets restants disponibles, valable du').' '.$date_start.' '.T_('au').' '.$date_end.'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=3" role="button" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-ticket-alt text-110 mr-lg-2"></i> 
			'; if(!$mobile) {echo T_('Tickets disponibles');} echo '
			<span id="id-navbar-badge1" class="badge badge-sm badge-warning radius-round text-80 border-1 brc-white-tp5">'.$nbticketremaining.'</span>
		</a>
	</li>
	';
}
//display remain tickets for company ticket limit
if($rparameters['company_limit_ticket'] && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
{
	//get company limit ticket parameters
	$qry=$db->prepare("SELECT `id`,`limit_ticket_number`,`limit_ticket_days`,`limit_ticket_date_start` FROM `tcompany` WHERE id=:id");
	$qry->execute(array('id' => $ruser['company']));
	$rcompany=$qry->fetch();
	$qry->closeCursor();
	
	if($rcompany['limit_ticket_number'] && $rcompany['limit_ticket_days'] && $rcompany['limit_ticket_date_start']!='0000-00-00' )
	{
		//generate date start and date end
		$date_start=$rcompany['limit_ticket_date_start'];
		
		//calculate end date	
		$date_start_conv = date_create($rcompany['limit_ticket_date_start']);
		date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_ticket_days] days"));
		$date_end=date_format($date_start_conv, 'Y-m-d');
	
		//count number of ticket remaining in period
		$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable='0'");
		$qry->execute(array('company' => $rcompany['id'],'date_start' => $date_start,'date_end' => $date_end));
		$nbticketused=$qry->fetch();
		$qry->closeCursor();
		
		//check number of tickets in current range date
		if(date('Y-m-d')>$date_end || date('Y-m-d')<$date_start)
		{
			$nbticketremaining=0;
		} else {
			$nbticketremaining=$rcompany['limit_ticket_number']-$nbticketused[0];
		}
		echo '
		<li class="nav-item dropdown dropdown-mega">
		<a title="'.T_('Nombre de tickets restants disponibles, valable du').' '.$date_start.' '.T_('au').' '.$date_end.'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=3" role="button" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-ticket-alt text-110 mr-lg-2"></i> 
			'; if(!$mobile) {echo T_('Tickets disponibles');} echo '
			<span id="id-navbar-badge1" class="badge badge-sm badge-warning radius-round text-80 border-1 brc-white-tp5">'.$nbticketremaining.'</span>
		</a>
	</li>
		';
	}
}
//display remain hours for company hour limit
if($rparameters['company_limit_hour'] && ($_SESSION['profile_id']==1 || $_SESSION['profile_id']==2))
{
	//get company limit ticket parameters
	$qry=$db->prepare("SELECT `id`,`limit_hour_number`,`limit_hour_days`,`limit_hour_date_start` FROM `tcompany` WHERE id=:id");
	$qry->execute(array('id' => $ruser['company']));
	$rcompany=$qry->fetch();
	$qry->closeCursor();
	
	if($rcompany['limit_hour_number'] && $rcompany['limit_hour_days'] && $rcompany['limit_hour_date_start']!='0000-00-00' )
	{
		//generate date start and date end
		$date_start=$rcompany['limit_hour_date_start'];
		
		//calculate end date	
		$date_start_conv = date_create($rcompany['limit_hour_date_start']);
		date_add($date_start_conv, date_interval_create_from_date_string("$rcompany[limit_hour_days] days"));
		$date_end=date_format($date_start_conv, 'Y-m-d');
	
		//count number of ticket remaining in period
		$qry=$db->prepare("SELECT SUM(tincidents.time)/60 FROM `tincidents`,`tusers` WHERE tusers.id=tincidents.user AND tusers.company=:company AND date_create BETWEEN :date_start AND :date_end AND tincidents.disable='0'");
		$qry->execute(array('company' => $rcompany['id'],'date_start' => $date_start,'date_end' => $date_end));
		$nbhourused=$qry->fetch();
		$qry->closeCursor();
		//check number of hour in current range date
		if(date('Y-m-d')>$date_end || date('Y-m-d')<$date_start)
		{
			$nbhourremaining=0;
		} else {
			$nbhourremaining=$rcompany['limit_hour_number']-$nbhourused[0];
			$nbhourremaining=round($nbhourremaining,1);
		}
		echo '
		<li class="nav-item dropdown dropdown-mega">
		<a title="'.T_("Nombre d'heures restantes disponibles, valable du").' '.$date_start.' '.T_('au').' '.$date_end.'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=3" role="button" aria-haspopup="true" aria-expanded="false">
			<i class="fa fa-ticket-alt text-110 mr-lg-2"></i> 
			'; if(!$mobile) {echo T_('Heures disponibles');} echo '
			<span id="id-navbar-badge1" class="badge badge-sm badge-warning radius-round text-80 border-1 brc-white-tp5">'.$nbhourremaining.'h</span>
		</a>
	</li>
		';
	}
}


echo '
<li class="nav-item dropdown dropdown-mega">
	<a title="'.T_('Vos tickets résolus').'" class="nav-link dropdown-toggle pl-lg-3 pr-lg-4 purple" href="index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state=3" role="button" aria-haspopup="true" aria-expanded="false">
		<i class="fa fa-check text-110 mr-lg-2"></i> 
		'; if(!$mobile) {echo T_('Résolus');} echo '
		<span id="id-navbar-badge1" class="badge badge-sm badge-success radius-round text-80 border-1 brc-white-tp5">'.$nbres[0].'</span>
	</a>
</li>
';
?>