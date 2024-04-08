<?php
################################################################################
# @Name : pie_assets_service.php
# @Description : Display Statistics 
# @Call : /stat.php
# @Parameters : 
# @Author : Flox
# @Create : 13/02/2016
# @Update : 06/02/2020
# @Version : 3.2.0
################################################################################

//array declaration
$values = array();
$xnom = array();

//display title
$libchart=T_("Répartition du nombre d'équipements par type");
$unit=T_('Équipements');

//query
$query1 = "
SELECT tassets_type.name as type, COUNT(*) as nb
FROM tassets, tassets_type, tusers
WHERE 
tassets_type.id=tassets.type AND
tassets.user=tusers.id AND
tassets.disable='0' AND
tassets.type LIKE '$_POST[type]' AND
tassets.department LIKE '$_POST[service]' AND
tassets.model LIKE '$_POST[model]' AND
tassets.date_install LIKE '%-$_POST[month]-%' AND
tassets.date_install LIKE '$_POST[year]-%' AND
tassets.technician LIKE '$_POST[tech]' AND
tusers.company LIKE '$_POST[company]'
GROUP BY tassets_type.name 
ORDER BY nb
DESC ";
$query = $db->query($query1);
while ($row=$query->fetch()) 
{
	$name=substr($row[0],0,35);
	$name=str_replace("'","\'",$name); 
	array_push($values, $row[1]);
	array_push($xnom, $name);
}
$query->closecursor(); 
$container='container102';
include('./stat_pie.php');
echo '<div class="card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center" id="'.$container.'"></div>';
if ($rparameters['debug'])echo $query1;
?>