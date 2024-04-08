<?php
################################################################################
# @Name : /plugins/availability/core.php
# @Description : Statistics calculs
# @call : /plugins/availability/index.php
# @parameters : category
# @Author : Flox
# @Create : 05/05/2015
# @Update : 30/01/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($m1 )) $m1 ='';
if(!isset($m2 )) $m2 ='';
if(!isset($m3 )) $m3 ='';
if(!isset($i )) $i ='';
if(!isset($hourdependancy_planned )) $hourdependancy_planned ='';
if(!isset($mindependancy_planned )) $mindependancy_planned ='';

//get month selection from quarter section
if ($m1!="") {
    $months="
        AND
        (
            tincidents.start_availability LIKE '$year-$m1%' OR
            tincidents.start_availability LIKE '$year-$m2%' OR
            tincidents.start_availability LIKE '$year-$m3%' 
        )
    ";
}else{$months="";}

//var init
$hourdependancy=0;
$mindependancy=0;
$dependancy_time=0;
$hour_planned=0;
$min_planned=0;
	
//get time date for this subcat
$querydata= "
	SELECT tincidents.id, tincidents.category, tincidents.subcat, tincidents.start_availability, tincidents.end_availability,
	SUM(hour(TIMEDIFF(end_availability, start_availability))) AS hourdiff,
	SUM(minute(TIMEDIFF(end_availability, start_availability))) AS minutediff
	FROM tincidents,tcriticality,tavailability
	WHERE
	tincidents.criticality=tcriticality.id AND
	tincidents.subcat=tavailability.subcat AND
	tincidents.disable=0 AND
	tincidents.availability_planned=0 AND
	tincidents.start_availability LIKE '$year%' $months AND 
    tavailability.subcat=$rowsubcat[subcat]
    GROUP BY tincidents.subcat
";
$querydata = $db->query("$querydata");
$rowdata=$querydata->fetch();

//calculate none planned dependancy if exist
if ($rparameters['availability_dep']==1)
{
   //calculate dependancy time
   $querydependancy= "
	SELECT tincidents.id, tincidents.category, tincidents.subcat, tincidents.start_availability, tincidents.end_availability,
	SUM(hour(TIMEDIFF(end_availability, start_availability))) AS hourdiff,
	SUM(minute(TIMEDIFF(end_availability, start_availability))) AS minutediff
	FROM tincidents,tcriticality,tavailability_dep
	WHERE
	tincidents.criticality=tcriticality.id AND
	tincidents.availability_planned=0 AND
	tincidents.subcat=tavailability_dep.subcat AND
	tincidents.disable=0 AND
	tcriticality.id=$rparameters[availability_condition_value] AND
	tincidents.start_availability LIKE '$year%' $months
	GROUP BY tincidents.subcat";

	$querydependancy = $db->query("$querydependancy");
    while ($rowdependancy=$querydependancy->fetch()) 
    { 
         $dependancy_time=($rowdependancy['hourdiff']*60)+($rowdependancy['minutediff'])+$dependancy_time;
         $hourdependancy=$rowdependancy['hourdiff']+$hourdependancy;
         $mindependancy=$rowdependancy['minutediff']+$mindependancy;
    }

} else $dependancy_time=0;

//calculate availability percentage of planned ticket
$query_planned= "
	SELECT tincidents.id, tincidents.category, tincidents.subcat, tincidents.start_availability, tincidents.end_availability,
	SUM(hour(TIMEDIFF(end_availability, start_availability))) AS hourdiff,
	SUM(minute(TIMEDIFF(end_availability, start_availability))) AS minutediff
	FROM tincidents,tcriticality,tavailability
	WHERE
	tincidents.criticality=tcriticality.id AND
	tincidents.subcat=tavailability.subcat AND
	tincidents.availability_planned=1 AND
	tincidents.subcat=$rowsubcat[subcat] AND
	tincidents.disable=0 AND
	tincidents.start_availability LIKE '$year%' $months
	GROUP BY tincidents.subcat
";

$query_planned = $db->query($query_planned);
$row_planned = $query_planned->fetch();


//calculate dependancy of planned tickets
if ($rparameters['availability_dep']==1)
{
   	//var init
    $hourdependancy_planned=0;
    $mindependancy_planned=0;
    $dependancy_time_planned=0;
    //calculate dependancy time without planned ticket
    $querydependancy_planned= "
	SELECT tincidents.id, tincidents.category, tincidents.subcat, tincidents.start_availability, tincidents.end_availability,
	SUM(hour(TIMEDIFF(end_availability, start_availability))) AS hourdiff,
	SUM(minute(TIMEDIFF(end_availability, start_availability))) AS minutediff
	FROM tincidents,tcriticality,tavailability_dep
	WHERE
	tincidents.criticality=tcriticality.id AND
	tincidents.subcat=tavailability_dep.subcat AND
	tincidents.disable=0 AND
	tincidents.availability_planned=1 AND
	tcriticality.id=$rparameters[availability_condition_value] AND
	tincidents.start_availability LIKE '$year%' $months
	GROUP BY tincidents.subcat";
	
	$querydependancy_planned = $db->query("$querydependancy_planned");
    while ($rowdependancy_planned=$querydependancy_planned->fetch()) 
    { 
         $dependancy_time_planned=($rowdependancy_planned['hourdiff']*60)+($rowdependancy_planned['minutediff'])+$dependancy_time_planned;
         $hourdependancy_planned=$rowdependancy_planned['hourdiff']+$hourdependancy_planned;
         $mindependancy_planned=$rowdependancy_planned['minutediff']+$mindependancy_planned;
    }
} else $dependancy_time_planned=0;

//unavailability time for ticket without planned
if(!empty($row_planned['hourdiff'])||!empty($row_planned['minutediff']))
{
	if ($row_planned['minutediff']!=0)  //avoid problem division by 0.
	{
		$min_planned=explode(".", ($row_planned['minutediff'])/60);
	} else {$min_planned= array("0" => "0", "1"=>"0");}
	$hour_planned=($row_planned['hourdiff'])+$min_planned[0];
	if (!empty($min_planned[1])){$min_planned=(60*"0.$min_planned[1]");} else {$min_planned=(60*"0.00");}
	$min_planned=round($min_planned);
}

//convert in hours and minutes 
if(!empty($rowdata['hourdiff'])||!empty($rowdata['minutediff']))
{
	$min=explode(".", ($rowdata['minutediff'])/60);
	//case for min equal 1 or 0
	$testval=($rowdata['minutediff'])/60;
	if($testval==1) {$min[1]=0;}
	if($testval==0) {$min[1]=0;}
	
	if($min[1]=="") {$min[1]=0;}
	$hour=($rowdata['hourdiff'])+$min[0];
	$min=(60*"0.$min[1]");
	$min=round($min);
} else {$min=0; $hour=0;}

//get target tx for selected subcat
$tx_target= $db->query("SELECT target FROM `tavailability_target` WHERE subcat='$rowsubcat[subcat]' AND year='$year'"); 
$tx_target= $tx_target->fetch();
$tx_target=$tx_target[0];
/*
echo "
 	DATA:<br />
 		- dependence planifié :  hour $hourdependancy_planned min $mindependancy_planned <br />
 		- dependence non planifié :  hour $hourdependancy min $mindependancy <br />
 		- planifié:  hour $hour_planned min $min_planned <br />
 		- non planifié : hour $hour min $min <br />
";
*/
 //init values for empty case
if($hourdependancy_planned=='') {$hourdependancy_planned=0;}
if($mindependancy_planned=='') {$mindependancy_planned=0;}

//convert hour in minutes for non planned ticket
$hourcnv=$hour*60;
//convert hour in minutes for planned ticket
$hour_plannedcnv=$hour_planned*60;
//convert hour in minutes of dependence planned ticket
$hourdependancy_plannedcnv=$hourdependancy_planned*60;
//convert hour in minutes of dependence none planned ticket
$hourdependancycnv=$hourdependancy*60;
//convert hour in minutes for planned ticket
$hourplannedcnv=$hour_planned*60;

//calculate total min of planned tickets
$total_min_planned=$min_planned+$hour_plannedcnv+$hourdependancy_plannedcnv+$mindependancy_planned; 
//echo "CALC TOTAL MIN PLANNED: $min_planned+$hour_plannedcnv+$hourdependancy_plannedcnv+$mindependancy_planned <br>";

//calculate total min of none planned tickets
$total_min_none_planned=$min+$hourcnv+$hourdependancycnv+$mindependancy; 
// DEBUG echo "CALC TOTAL MIN NON PLANNED: min $min+ hourcnv $hourcnv+ hourdependancycnv $hourdependancycnv+ mindependancy $mindependancy<br>";

//get total periods day
if ($i) {$nb_day=$trim_hours;} else {$nb_day=365;}


//calculate tx for ticket without planned ticket
$tx_planned=100-(100*($total_min_none_planned/($nb_day*24*60)));
// DEBUG echo "CALC TX WITHOUT PLANNED : 100-(100*($total_min_none_planned/(365*24*60)))<br>";
$tx_planned=number_format($tx_planned,2);

//calculate global availability percentage
// DEBUG echo "CALC TX GLOBAL : 100-(100*($total_min_none_planned+$total_min_planned)/(365*24*60)))<br>";
$tx=100-(100*(($total_min_none_planned+$total_min_planned)/($nb_day*24*60)));
$tx=number_format($tx,2);

//calculate non planned availability percentage for median calc
$tx_none_planned=100-(100*(($total_min_none_planned)/($nb_day*24*60)));
$tx_none_planned=number_format($tx_none_planned,2);

//calculate availability percent for send to graphic
$tx2=100-$tx;
$tx2=number_format($tx2,2);

//convert minutes in hours and minutes for planned tickets
if ($total_min_planned!=0)
{
	$total_min_planned=number_format($total_min_planned/60, 2, '.', ''); 
	$total_min_planned=explode(".", ($total_min_planned));
	$total_hour_planned=$total_min_planned[0];
	$total_min_planned=(60*"0.$total_min_planned[1]");
	$total_min_planned=round($total_min_planned);
} else {
	$total_hour_planned=0;
}

//convert minutes in hours and minutes for none planned tickets
if ($total_min_none_planned!=0)
{
	$total_min_none_planned=number_format($total_min_none_planned/60, 2, '.', ''); //avoid 
	$total_min_none_planned=explode(".", ($total_min_none_planned));
	$total_hour_none_planned=$total_min_none_planned[0];
	$total_min_none_planned=(60*"0.$total_min_none_planned[1]");
	
	$total_min_none_planned=round($total_min_none_planned);
} else {
	$total_hour_none_planned=0;
}

//calculate global  
$global_min=$total_min_none_planned+$total_min_planned;
if($global_min>=60) {$global_min=$global_min-60; $add_hour=1;} else {$add_hour=0;}//case min > 60
$global_hour=$total_hour_none_planned+$total_hour_planned+$add_hour;

?>