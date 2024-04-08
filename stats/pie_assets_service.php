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
$libchart=T_("Répartition du nombre d'équipements par services");
$unit=T_('Équipements');

$qry=$db->prepare("
SELECT tservices.name as service, COUNT(*) as nb
FROM tassets, tservices, tusers 
WHERE 
tservices.id=tassets.department AND
tassets.user=tusers.id AND
tassets.disable='0' AND
tassets.type LIKE :type AND
tassets.department LIKE :department AND
tassets.model LIKE :model AND
tassets.date_install LIKE :date_install AND
tassets.date_install LIKE :date_install2 AND
tassets.technician LIKE :technician AND
tusers.company LIKE :company
GROUP BY tservices.name 
ORDER BY nb
DESC 
");

$date1="%-$_POST[month]-%";
$date2="$_POST[year]-%";
$qry->execute(array(
'type' => $_POST['type'],
'department' => $_POST['service'],
'model' => $_POST['model'],
'date_install' => $date1,
'date_install2' => $date2,
'technician' => $_POST['tech'],
'company' => $_POST['company']
));
while($row=$qry->fetch()) 
{
	$name=substr($row[0],0,35);
	$name=str_replace("'","\'",$name); 
	array_push($values, $row[1]);
	array_push($xnom, $name);
}
$qry->closeCursor();


$container='container101';
include('./stat_pie.php');
echo '<div class="card-body bgc-dark-l4 p-0 border-1 brc-default-l2 radius-2 px-1 mx-n2 mx-md-0 h-100 d-flex align-items-center" id="'.$container.'"></div>';
?>