<?php
################################################################################
# @Name : pie_tickets_type.php
# @Description : Display Statistics chart 
# @call : /ticket_stat.php
# @parameters : 
# @Author : Flox
# @Create : 02/12/2019
# @Update : 06/02/2020
# @Version : 3.2.0
################################################################################

$values=array();
$xnom=array();
$query=$db->query("SELECT COUNT(*) FROM tincidents WHERE disable='0'");
$rtotal=$query->fetch();

$libchart=T_('Tickets par type');
$unit=T_('tickets');
$query1 = "
SELECT ttypes.name as type, COUNT(*) as nb
FROM tincidents INNER JOIN ttypes ON (tincidents.type=ttypes.id)
WHERE
tincidents.disable LIKE '0' AND
tincidents.technician LIKE '$_POST[tech]' AND
tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
$where_state AND
tincidents.type LIKE '$_POST[type]' AND
criticality like '$_POST[criticality]' AND
tincidents.category LIKE '$_POST[category]' AND
tincidents.date_create LIKE '%-$_POST[month]-%' AND
tincidents.date_create LIKE '$_POST[year]-%'
GROUP BY ttypes.id
ORDER BY nb
DESC
";
$query=$db->query($query1);
while ($row = $query->fetch()) 
{
	array_push($values, $row[1]);
	array_push($xnom, T_($row['type']));
} 
$container='container31';
include('./stat_pie.php');
echo '<div class="card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center" id="'.$container.'"></div>';
if ($rparameters['debug']==1)echo $query1;
?>