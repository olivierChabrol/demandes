<?php
################################################################################
# @Name : line_tickets_activity.php
# @Description : show line graph with resolution elements
# @call : /ticket_stat.php
# @parameters : 
# @Author : Flox
# @Create : 21/03/2017
# @Update : 06/05/2017
# @Version : 3.1.21
################################################################################

$user_id=$_SESSION['user_id'];

//query for year selection
if (($_POST['month'] == '%') && ($_POST['year']!=='%'))
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$libchart=T_('Évolution des éléments de résolution ajoutés aux tickets sur').' '.$_POST['year'];
	$query1=$db->query("
	SELECT month(date) as x,count(*) as y
	FROM `tthreads`
	INNER JOIN tincidents ON tincidents.id=tthreads.ticket
	WHERE tincidents.technician LIKE '$_POST[tech]' AND
	tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
	tincidents.criticality like '$_POST[criticality]' AND
	tincidents.type LIKE '$_POST[type]' AND
	tincidents.category LIKE '$_POST[category]' AND
	tincidents.date_create not like '0000-00-00 00:00:00' AND
	(tincidents.date_create like '$_POST[year]-$_POST[month]-%' OR (tthreads.date LIKE '$_POST[year]-$_POST[month]-%' AND tthreads.type='0')) AND
	tincidents.disable='0'
	GROUP BY x ");
	
	// push data in table
	while($data = $query1->fetch())
	{
		array_push($values1 ,$data['y']);
		array_push($xnom1 ,$data['x']);
	}
	$query1->closeCursor(); 
}
//query for month selection
else if ($_POST['month']!='%')
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$monthm=$_POST['month'];
	if($_POST['year']=='%') {$postyear=T_('de toutes les années');} else {$postyear=$_POST['year'];}
	$libchart=T_('Évolution des éléments de résolution ajoutés aux tickets pour le mois de').' '.$mois[$monthm].' '.$postyear;
	$query1="
	SELECT day(date) as x,count(*) as y FROM `tthreads`
	INNER JOIN tincidents ON tincidents.id=tthreads.ticket
	WHERE tincidents.technician LIKE '$_POST[tech]' AND
	tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
	tincidents.criticality like '$_POST[criticality]' AND
	tincidents.type LIKE '$_POST[type]' AND
	tincidents.category LIKE '$_POST[category]' AND
	tincidents.date_create not like '0000-00-00 00:00:00' AND
	tthreads.date not like '0000-00-00 00:00:00' AND
	(tincidents.date_create like '$_POST[year]-$_POST[month]-%' OR (tthreads.date LIKE '$_POST[year]-$_POST[month]-%' AND tthreads.type='0')) AND
	tincidents.disable='0'
	GROUP BY x 
	";
	if($rparameters['debug']==1) {echo $query1;}
	$query1=$db->query($query1);

	//push data in table
	while($data = $query1->fetch())
	{
    	array_push($values1 ,$data['y']);
    	array_push($xnom1 ,$jour[$data['x']]);
	}
	$query1->closeCursor(); 
}
//query for all years selection
else if ($_POST['year']=='%')
{
    $values1 = array();
    $values2 = array();
    $xnom1 = array();
    $xnom2 = array();
	$libchart=T_('Évolution des éléments de résolution ajoutés aux tickets sur toutes les années');
	$query1=$db->query("
	SELECT year(date) as x,count(*) as y
	FROM `tthreads`
	INNER JOIN tincidents ON tincidents.id=tthreads.ticket
	WHERE tincidents.technician LIKE '$_POST[tech]' AND
	tincidents.u_service LIKE '$_POST[service]' $where_service $where_agency AND
	tincidents.criticality like '$_POST[criticality]' AND
	tincidents.type LIKE '$_POST[type]' AND
	tincidents.category LIKE '$_POST[category]' AND
	tincidents.date_create not like '0000-00-00 00:00:00' AND
	(tincidents.date_create like '$_POST[year]-$_POST[month]-%' OR (tthreads.date LIKE '$_POST[year]-$_POST[month]-%' AND tthreads.type='0')) AND
	tincidents.disable='0'
	GROUP BY x ");
	// push data in table
	while($data = $query1->fetch())
	{
		array_push($values1 ,$data['y']); array_push($xnom1 ,$data['x']);	
	}	
	$query1->closeCursor(); 
}

if ($count!=0) 
{
	$liby=T_("Nombre d\\'éléments de résolution");
	$container="container9";		
	include('./stat_line.php');
	echo '<div id="'.$container.'" style="min-width: 400px; height: 400px; margin: 0 auto"></div>';
}
else { echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i> '.T_('Erreur').':</strong> '.T_('Aucun élément de résolution ajouté dans la plage indiqué').'.</div>';}

//display query on debug mode
if($rparameters['debug']==1)
{
    print_r($values1);echo "<br />";
    for($i=0;$i<sizeof($values1);$i++) 
    { 
    $last=sizeof($values1)-1;
    if ($i!=$last) echo '['.$xnom1[$i].','.$values1[$i].'],'; else echo '['.$xnom1[$i].','.$values1[$i].']';
    } 
    echo "<br />";
    print_r($values2);echo "<br />";
    for($i=0;$i<sizeof($values2);$i++) 
    { 
    $last=sizeof($values2)-1;
    if ($i!=$last) echo '['.$xnom2[$i].','.$values2[$i].'],'; else echo '['.$xnom2[$i].','.$values2[$i].']';
    } 
}
?>	