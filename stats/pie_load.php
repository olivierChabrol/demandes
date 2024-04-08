<?php
################################################################################
# @Name : pie_load.php
# @Description : Display Statistics of chart6 by categories
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 06/02/2020
# @Version : 3.2.0
################################################################################

$values = array();
$xnom = array();
$libchart=T_('Répartition de la charge de travail par catégories sur les tickets ouverts');
$unit='h';
$current_month=date('m');
$current_year=date('Y');

//total
if ($_POST['category']=='') $_POST['category']='%';
$qtotal = $db->query("SELECT count(*) FROM tincidents WHERE category NOT LIKE '0' AND category LIKE '$_POST[category]'");
$rtotal=$qtotal->fetch();

if ($_POST['category']!='%')
{
	if (($_POST['year']==$current_year) && ($_POST['month']==$current_month))
	{
		$query1 = "
		SELECT tsubcat.name AS subcat, (SUM(tincidents.time_hope-tincidents.time))/60 AS time
		FROM `tincidents` 
		INNER JOIN tsubcat
		ON (tincidents.subcat=tsubcat.id )
		WHERE
		tincidents.technician LIKE '$_POST[tech]' AND
		tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
		$where_state AND
		criticality like '$_POST[criticality]' AND
		tincidents.category LIKE '$_POST[category]'  AND
		tincidents.type LIKE '$_POST[type]' AND
		tincidents.time_hope-tincidents.time > 0 AND
		tincidents.disable='0'  
		GROUP BY tsubcat.name
		ORDER BY time DESC
	";
	} else {
		$query1 = "
		SELECT tsubcat.name AS subcat, tincidents.time/60 AS time
		FROM `tincidents` 
		INNER JOIN tsubcat
		ON (tincidents.subcat=tsubcat.id )
		WHERE
		tincidents.technician LIKE '$_POST[tech]' AND
		tincidents.type LIKE '$_POST[type]' AND
		tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
		$where_state AND
		criticality like '$_POST[criticality]' AND
		tincidents.category LIKE '$_POST[category]'  AND
		tincidents.disable='0' AND
		tincidents.date_create LIKE '$_POST[year]-%' AND
		tincidents.date_create LIKE '%-$_POST[month]-%' AND
		tincidents.state='3' 
		GROUP BY tsubcat.name
		ORDER BY time DESC
	";
	}
} else {
	if (($_POST['year']==$current_year) && ($_POST['month']==$current_month))
	{
		$query1 = "
			SELECT tcategory.name AS technicien, ((SUM(tincidents.time_hope)-SUM(tincidents.time)))/60 AS time
			FROM `tincidents`
			INNER JOIN tcategory 
			ON (tincidents.category=tcategory.id ) 
			WHERE 
			tincidents.technician LIKE '$_POST[tech]' AND
			tincidents.type LIKE '$_POST[type]' AND
			tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
			$where_state AND
			criticality like '$_POST[criticality]' AND
			tincidents.category LIKE '$_POST[category]' AND
			tincidents.disable='0' AND
			tincidents.time_hope-tincidents.time > 0 AND
			(tincidents.state='1' OR tincidents.state='2' OR tincidents.state='6' )
			GROUP BY tcategory.name
			ORDER BY time DESC
			";
	} else {
			$query1 = "
			SELECT tcategory.name AS technicien, tincidents.time/60 AS time
			FROM `tincidents`
			INNER JOIN tcategory 
			ON (tincidents.category=tcategory.id ) 
			WHERE 
			tincidents.technician LIKE '$_POST[tech]' AND
			tincidents.type LIKE '$_POST[type]' AND
			tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
			$where_state AND
			tincidents.category LIKE '$_POST[category]' AND
			criticality like '$_POST[criticality]' AND
			tincidents.disable='0' AND
			tincidents.date_create LIKE '$_POST[year]-%' AND
			tincidents.date_create LIKE '%-$_POST[month]-%' AND
			tincidents.state='3' 
			GROUP BY tcategory.name
			ORDER BY time DESC
			";
	}
}

$query = $db->query($query1);
while ($row = $query->fetch())
{ 
    $data=round($row[1], 0);
	$name=addslashes(substr($row[0],0,42));
	array_push($values, $data);
	array_push($xnom, $name);
} 
$container='container6';
include('./stat_pie.php');
echo '<div class="card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center" id="'.$container.'"></div>';
if ($rparameters['debug'])echo $query1;
?>