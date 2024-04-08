<?php
################################################################################
# @Name : pie_tickets_tech.php
# @Description : Display Statistics chart 1
# @call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 15/02/2014
# @Update : 04/03/2020
# @Version : 3.2.0
################################################################################

$values = array();
$xnom = array();
$libchart=T_('Tickets par techniciens');
$unit=T_('tickets');

//total
$query=$db->query("SELECT COUNT(*) FROM tincidents WHERE disable='0'");
$month1=$query->fetch();

$query1 = "
SELECT CONCAT_WS('. ', left(tusers.firstname, 1),  tusers.lastname) as Technicien, count(*) as resolve 
FROM tincidents 
INNER JOIN tusers 
ON (tincidents.technician=tusers.id ) 
WHERE
tincidents.technician LIKE '$_POST[tech]' AND
tincidents.type LIKE '$_POST[type]' AND
tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
$where_state AND
criticality like '$_POST[criticality]' AND
tincidents.category LIKE '$_POST[category]' AND
tincidents.date_create LIKE '%-$_POST[month]-%' AND
tincidents.date_create LIKE '$_POST[year]-%' AND
tincidents.disable LIKE '0'
GROUP BY tusers.id
ORDER by resolve DESC";

$query=$db->query($query1);
while ($row = $query->fetch()) 
{
	$name=addslashes(substr($row[0],0,42));
	array_push($values, $row[1]);
	array_push($xnom, $name);
} 	
$container='container2';
include('./stat_pie.php');
echo '<div class="card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center" id="'.$container.'"></div>';
if ($rparameters['debug']) echo $query1;
?>