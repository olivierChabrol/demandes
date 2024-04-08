<?php
################################################################################
# @Name : /plugins/availability/median.php
# @Description : calculate median
# @Call : /plugins/availability/index.php
# @Parameters : category
# @Author : Flox
# @Create : 23/05/2015
# @Update : 21/01/2016
# @Version : 3.1.5
################################################################################

//function median
function calculate_median($arr) {
    sort($arr);
    $count = count($arr); //total numbers in array
    $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
    if($count % 2) { // odd number, middle is the median
        $median = $arr[$middleval];
    } else { // even number, calculate avg of 2 medians
		
		//avoid negative values for php warning without values
		if ($middleval!='-1') { 
			$low = $arr[$middleval];
			$high = $arr[$middleval+1];
			$median = (($low+$high)/2);
		} else {$median=0;}

    }
    return $median;
}


//target median
$median_target_array = array();
$q = $db->query("SELECT target FROM `tavailability_target` WHERE year='$year' ");
while ($r=$q->fetch())
{
    array_push($median_target_array, "$r[0]");
}
$median_target = calculate_median($median_target_array);


//global median
$median_global_array = array();
$querysubcat = $db->query("SELECT * FROM `tavailability`");
while ($rowsubcat=$querysubcat->fetch())
{
    include('core.php');
    array_push($median_global_array, "$tx");
}
$median_global = calculate_median($median_global_array);

//none planned median
$median_none_planned_array = array();
$querysubcat = $db->query("SELECT * FROM `tavailability`");
while ($rowsubcat=$querysubcat->fetch())
{
    include('core.php');
    array_push($median_none_planned_array, "$tx_none_planned");
}
$median_none_planned = calculate_median($median_none_planned_array);
 
 
?>