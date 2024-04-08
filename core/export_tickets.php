<?php
################################################################################
# @Name : ./core/export_tickets.php
# @Encoding : UTF-8 with BOM
# @Description : dump csv files of current query
# @Call : /stat.php
# @Parameters : 
# @Author : Flox
# @Create : 27/01/2014
# @Update : 21/0087/2020
# @Version : 3.2.4
################################################################################

//database connection
require "../connect.php"; 

//get language of connected user
$qry=$db->prepare("SELECT `language` FROM `tusers` WHERE id=:id");
$qry->execute(array('id' => $_GET['userid']));
$user=$qry->fetch();
$qry->closeCursor();
$_GET['lang']=$user['language'];

//locales
define('PROJECT_DIR', realpath('../'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', '($_GET[lang]');
require_once('../components/php-gettext/gettext.inc');
$encoding = 'UTF-8';
$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain($_GET['lang'], LOCALE_DIR);
T_bind_textdomain_codeset($_GET['lang'], $encoding);
T_textdomain($_GET['lang']);

//initialize variables 
require_once(__DIR__."/../core/init_get.php");
if(!isset($_COOKIE['token'])) $_COOKIE['token'] = ''; 
if(!isset($cnt_service)) $cnt_service=''; 

$db_userid=strip_tags($_GET['userid']);
$db_agency=strip_tags($_GET['agency']);
$db_technician=strip_tags($_GET['technician']);
$db_service=strip_tags($_GET['service']);
$db_type=strip_tags($_GET['type']);
$db_criticality=strip_tags($_GET['criticality']);
$db_category=strip_tags($_GET['category']);
$db_state=strip_tags($_GET['state']);
$db_month=strip_tags($_GET['month']);
$db_year=strip_tags($_GET['year']);

//secure connect from authenticated user
if($_GET['token']==$_COOKIE['token'] && $_GET['token']) 
{
	//get current date
	$daydate=date('Y-m-d');

	// output headers so that the file is downloaded rather than displayed
	header('Content-Encoding: UTF-8');
	header('Content-type: text/csv; charset=UTF-8');
	header('Content-Disposition: attachment; filename='.$daydate.'_GestSup_export_tickets.csv');

	//load parameters table
	$qry = $db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//display error parameter
	if($rparameters['debug']) {
		ini_set('display_errors', 'On');
		ini_set('display_startup_errors', 'On');
		ini_set('html_errors', 'On');
		error_reporting(E_ALL);
	} else {
		ini_set('display_errors', 'Off');
		ini_set('display_startup_errors', 'Off');
		ini_set('html_errors', 'Off');
		error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
	}
	
	//load rights table
	$qry = $db->prepare("SELECT * FROM trights WHERE profile=(SELECT profile FROM tusers WHERE id=:id)");
	$qry->execute(array('id' => $db_userid));
	$rright=$qry->fetch();
	$qry->closeCursor();
	
	$where='';
	
	//get services associated with this user
	$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
	$qry->execute(array('user_id' => $db_userid));
	$cnt_service=$qry->rowCount();
	$row=$qry->fetch();
	$qry->closeCursor();
	
	//case limit user service
	if($rparameters['user_limit_service'] && !$rright['admin'] && $_GET['service']=='%' && $cnt_service!=0)
	{
		//get services associated with this user
		$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $db_userid));
		$cnt_service=$qry->rowCount();
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($cnt_service==0) {$where_service.='';}
		elseif($cnt_service==1) {
			$where.="u_service='$row[service_id]' AND ";
		} else {
			$cnt2=0;
			$qry = $db->prepare("SELECT service_id FROM `tusers_services` WHERE user_id=:user_id");
			$qry->execute(array('user_id' => $db_userid));
			$where.='(';
			while ($row=$qry->fetch())	
			{
				$cnt2++;
				$where.="u_service='$row[service_id]'";
				if($cnt_service!=$cnt2) $where.=' OR '; 
			}
			$where.=' OR user='.$db_userid.' OR technician='.$db_userid.' ';
			$where.=') AND ';
			$qry->closecursor();
		}
	}

	//get agency associated with this user
	$qry = $db->prepare("SELECT agency_id FROM `tusers_agencies` WHERE user_id=:user_id");
	$qry->execute(array('user_id' => $db_userid));
	$cnt_agency=$qry->rowCount();
	$row=$qry->fetch();
	$qry->closeCursor();

	//case limit user agency
	if($rparameters['user_agency'] && !$rright['admin'] && $_GET['agency']=='%' && $cnt_agency!=0)
	{
		//get agencies associated with this user
		$qry = $db->prepare("SELECT agency_id FROM `tusers_agencies` WHERE user_id=:user_id");
		$qry->execute(array('user_id' => $db_userid));
		$cnt_agency=$qry->rowCount();
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($cnt_agency==0) {$where_agency.='';}
		elseif($cnt_agency==1) {
			$where.="u_agency='$row[agency_id]' AND ";
		} else {
			$cnt2=0;
			$qry = $db->prepare("SELECT agency_id FROM `tusers_agencies` WHERE user_id=:user_id");
			$qry->execute(array('user_id' => $db_userid));
			$where.='(';
			while ($row=$qry->fetch())	
			{
				$cnt2++;
				$where.="u_agency='$row[agency_id]'";
				if ($cnt_agency!=$cnt2) $where.=' OR '; 
			}
			$where.=' OR user='.$db_userid.' OR technician='.$db_userid.' ';
			$where.=') AND ';
			$qry->closecursor();
		}
	}

	//case limit user agency AND user service
	if($rparameters['user_agency'] && $rparameters['user_limit_service'] && !$rright['admin'] && $cnt_agency>0 && $cnt_service>0)
	{
		if($cnt_agency>1 && $cnt_service=1)
		{
			$where='('.str_replace('AND (','OR ', $where);
		} elseif($cnt_agency=1 && $cnt_service=1) {
			//case 1 service and 1 agency
			$where='('.str_replace('AND u_agency','OR u_agency', $where).')';
			$where=str_replace('AND','', $where).' AND ';
		}
	}

	//create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');
	
	//output the column headings
	$select='';
	fputcsv($output,array(T_('Numéro du ticket'), T_('Type'), T_('Type de réponse'), T_('Technicien'), T_('Demandeur'), T_('Service'), T_('Service du demandeur'), T_('Agence'), T_('Date de première réponse'), T_('Société'),T_('Créateur'), T_('Catégorie'), T_('Sous-catégorie'), T_('Lieu'),T_('Titre'), T_('Temps passé'), T_('Date de création'),T_('Date de résolution estimée'), T_('Date de clôture'), T_('État'), T_('Priorité'), T_('Criticité')),";");
	$select.='sender_service,u_agency, img2, img1,';
	$where.="u_agency LIKE '$db_agency' AND";
	
	//special case to filter by meta state
	if($db_state=='meta') {
		$query="
		SELECT id,type,type_answer,technician,user,u_service, $select creator,category,subcat,place,title,time,date_create,date_hope,date_res,state,priority,criticality 
		FROM tincidents 
		WHERE
		technician LIKE '$db_technician' AND
		u_service LIKE '$db_service' AND
		type LIKE '$db_type' AND
		criticality LIKE '$db_criticality' AND
		category LIKE '$db_category' AND
		(state=1 OR state=2 OR state=6) AND
		date_create LIKE '%-$db_month-%' AND
		date_create LIKE '$db_year-%' AND
		u_agency LIKE '$db_agency' AND
		$where
		disable=0
		";
	} else {
		$query="
		SELECT id,type,type_answer,technician,user,u_service, $select creator,category,subcat,place,title,time,date_create,date_hope,date_res,state,priority,criticality 
		FROM tincidents 
		WHERE
		technician LIKE '$db_technician' AND
		u_service LIKE '$db_service' AND
		type LIKE '$db_type' AND
		criticality LIKE '$db_criticality' AND
		category LIKE '$db_category' AND
		state LIKE '$db_state' AND
		date_create LIKE '%-$db_month-%' AND
		date_create LIKE '$db_year-%' AND
		u_agency LIKE '$db_agency' AND
		$where
		disable=0
		";
	}
	if($rparameters['debug']) {echo $query;}
	$query = $db->query($query);
	while ($row = $query->fetch(PDO::FETCH_ASSOC)) 
	{
		//detect technician group to display group name instead of technician name
		if($row['technician']==0)
		{
			//check if group exist on this ticket
			$qry2=$db->prepare("SELECT t_group FROM tincidents WHERE id=:id");
			$qry2->execute(array('id' => $row['id']));
			$row2=$qry2->fetch();
			$qry2->closeCursor();
			if($row2['t_group']!='0')
			{
				//get group name
				$qry2=$db->prepare("SELECT `name` FROM tgroups WHERE id=:id");
				$qry2->execute(array('id' => $row2['t_group']));
				$row2=$qry2->fetch();
				$qry2->closeCursor();
				$row['technician']="$row2[name]";
			}
		} else {
			$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id ");
			$qry2->execute(array('id' => $row['technician']));
			$resulttech=$qry2->fetch();
			$qry2->closeCursor();
			$row['technician']="$resulttech[firstname] $resulttech[lastname]";
		}
		
		$qry2=$db->prepare("SELECT `name` FROM ttypes WHERE id=:id ");
		$qry2->execute(array('id' => $row['type']));
		$resulttype=$qry2->fetch();
		$qry2->closeCursor();
		$row['type']=$resulttype['name'];
		
		$qry2=$db->prepare("SELECT `name` FROM ttypes_answer WHERE id=:id ");
		$qry2->execute(array('id' => $row['type_answer']));
		$resulttype_answer=$qry2->fetch();
		$qry2->closeCursor();
		if(isset($resulttype_answer['name'])) {$row['type_answer']=$resulttype_answer['name'];} else {$row['type_answer']='Aucun';}
		
		$qry2=$db->prepare("SELECT `name` FROM tcompany,tusers WHERE tusers.company=tcompany.id AND tusers.id=:id");
		$qry2->execute(array('id' => $row['user']));
		$resultcompany=$qry2->fetch();
		$qry2->closeCursor();
		$row['img1']="$resultcompany[name]";
		
		//detect user group to display group name instead of user name
		if($row['user']=='')
		{
			//check if group exist on this ticket
			$qry2=$db->prepare("SELECT u_group FROM tincidents WHERE id=:id");
			$qry2->execute(array('id' => $row['id']));
			$row2=$qry2->fetch();
			$qry2->closeCursor();
			if($row2['u_group']!='0')
			{
				//get group name
				$qry2=$db->prepare("SELECT `name` FROM tgroups WHERE id=:id");
				$qry2->execute(array('id' => $row2['u_group']));
				$row2=$qry2->fetch();
				$qry2->closeCursor();
				$row['user']="$row2[name]";
			}
		} else {
			$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id");
			$qry2->execute(array('id' => $row['user']));
			$resultuser=$qry2->fetch();
			$qry2->closeCursor();
			$row['user']="$resultuser[firstname] $resultuser[lastname]";
		}
		
		//get sender service name
		$qry2=$db->prepare("SELECT `name` FROM tservices WHERE id=:id");
		$qry2->execute(array('id' => $row['sender_service']));
		$result_sender_service=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($result_sender_service['name'])) {$row['sender_service']=$result_sender_service['name'];} else {$row['sender_service']=T_('Aucun');}
		//get agency name
		$qry2=$db->prepare("SELECT `name` FROM tagencies WHERE id=:id");
		$qry2->execute(array('id' => $row['u_agency']));
		$resultagency=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultagency['name'])) {$row['u_agency']=$resultagency['name'];} else {$row['u_agency']=T_('Aucun');}
		//find date first answer
		$qry2=$db->prepare("SELECT MIN(date) FROM `tthreads` WHERE ticket=:ticket AND type='0'");
		$qry2->execute(array('ticket' => $row['id']));
		$resultfirst=$qry2->fetch();
		$qry2->closeCursor();
		$row['img2']="$resultfirst[0]";
		//service name
		$qry2=$db->prepare("SELECT `name` FROM tservices WHERE id=:id");
		$qry2->execute(array('id' => $row['u_service']));
		$resultservice=$qry2->fetch();
		$qry2->closeCursor();
		if(isset($resultservice['name'])) {$row['u_service']=$resultservice['name'];} else {$row['u_service']=T_('Aucun');}
		//creator name
		$qry2=$db->prepare("SELECT firstname,lastname FROM tusers WHERE id=:id");
		$qry2->execute(array('id' => $row['creator']));
		$resultcreator=$qry2->fetch();
		$qry2->closeCursor();
		$row['creator']="$resultcreator[firstname] $resultcreator[lastname]";
		//category name
		$qry2=$db->prepare("SELECT `name` FROM tcategory WHERE id=:id");
		$qry2->execute(array('id' => $row['category']));
		$resultcat=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultcat['name'])) {$row['category']=$resultcat['name'];} else {$row['category']=T_('Inconnue');}
		//subcat name
		$qry2=$db->prepare("SELECT `name` FROM tsubcat WHERE id=:id");
		$qry2->execute(array('id' => $row['subcat']));
		$resultscat=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultscat['name'])) {$row['subcat']=$resultscat['name'];} else {$row['subcat']=T_('Inconnue');}
		//place name
		$qry2=$db->prepare("SELECT `name` FROM tplaces WHERE id=:id");
		$qry2->execute(array('id' => $row['place']));
		$resultplace=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultplace['name'])) {$row['place']=$resultplace['name'];} else {$row['place']=T_('Inconnu');}
		//state name
		$qry2=$db->prepare("SELECT `name` FROM tstates WHERE id=:id");
		$qry2->execute(array('id' => $row['state']));
		$resultstate=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultstate['name'])) {$row['state']=$resultstate['name'];} else {$row['state']=T_('Inconnu');}
		//priority name
		$qry2=$db->prepare("SELECT `name` FROM tpriority WHERE id=:id");
		$qry2->execute(array('id' => $row['priority']));
		$resultpriority=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultpriority['name'])) {$row['priority']=$resultpriority['name'];} else {$row['priority']=T_('Inconnue');}
		//criticality name
		$qry2=$db->prepare("SELECT `name` FROM tcriticality WHERE id=:id");
		$qry2->execute(array('id' => $row['criticality']));
		$resultcriticality=$qry2->fetch();
		$qry2->closeCursor();
		if(!empty($resultcriticality['name'])) {$row['criticality']=$resultcriticality['name'];} else {$row['criticality']=T_('Inconnue');}
	
		fputcsv($output, $row,';');
	}
	$qry->closeCursor();
} else {
	echo '<br /><br /><center><span style="font-size: x-large; color: red;"><b>'.T_("Erreur d'accès à la page, essayer de recharger la page statistique, si le problème persiste contacter votre administrateur").'.</b></span></center>';		
}
$db = null;
?>