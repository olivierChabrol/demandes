<?php
################################################################################
# @Name : /plugins/availability/index.php
# @Description : display availability
# @Call : /menu.php
# @Parameters : 
# @Author : Flox
# @Create : 18/04/2014
# @Update : 23/01/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($_GET['previous'])) $_GET['previous']='';
if(!isset($_GET['next'])) $_GET['next']='';
if(!isset($dependancy_time)) $dependancy_time='';
if(!isset($hourdependancy)) $hourdependancy='';
if(!isset($mindependancy)) $mindependancy='';
if(!isset($hour_planned)) $hour_planned='';
if(!isset($min_planned )) $min_planned ='';
if(!isset($_GET['page'])) $_GET['page'] = '';
if(!isset($_GET['year'])) $_GET['year'] = '';

//default settings
if($_GET['year']=='') {$year=date('Y');} else {$year=$_GET['year'];}

//get median calculate
include('median.php');

//display head
echo '
<div class="page-header position-relative">
	<h1 <h1 class="page-title text-primary-m2">
		<i class="fa fa-clock text-primary-m2"></i> '.T_("Disponibilité du Système d'information").' '.$rparameters['company'].'
	</h1>
</div>
<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
    ';
    //display link to years page
    $queryyears = $db->query("SELECT DISTINCT YEAR(date_create) FROM tincidents WHERE date_create NOT LIKE '%0000%' ORDER BY YEAR(date_create) DESC");
    while ($rowyear=$queryyears->fetch())
    {
        echo '<button onclick=\'window.location.href="./index.php?page=plugins/availability/index&year='.$rowyear[0].'";\' title="Accès direct à l\'année '.$rowyear[0].'" 
            class="btn btn-info" >
            '.$rowyear[0].'
            </button>
            &nbsp;&nbsp;&nbsp;';
    }
	$queryyears->closeCursor(); 
	echo '
            
    <a href="./plugins/availability/print.php?year='.$year.'&token='.$_COOKIE['token'].'" target="_blank" <i title="'.T_('Imprimer').'" class="fa fa-print text-success"></i></a>

</div>
<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large"></div>
<script src="./components/Highcharts/highcharts.js"></script>
<script src="./components/Highcharts/modules/exporting.js"></script>';
echo '
<table border="0">
    <tr>
    	<td>
            <a id="'.$year.'"></a>
            <br />
            <h3>'.T_('Pour l\'année').' '.$year.'</h3>
            <blockquote class="ml-4" >
            	'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
                <i class="fa fa-circle text-dark-l2"></i> '.T_('Médiane des taux de disponibilité cible des applications').': <font color="green">'.$median_target.'%</font><br />
                <i class="fa fa-circle text-dark-l2"></i> '.T_('Médiane du taux de disponibilité constaté').': <font color="green">'.$median_global.'%</font><br />
                <i class="fa fa-circle text-dark-l2"></i> '.T_('Médiane du taux de disponibilité constaté').' (hors interventions planifiées): <font color="green">'.$median_none_planned.'%</font><br />
                ';
            	$querysubcat = $db->query("SELECT * FROM `tavailability`");
    		    while ($rowsubcat=$querysubcat->fetch())
    		    { 
    		    	//var init
    		    	$total_hour_planned=0;
    		    	$total_min_planned =0;
    		    	
    		        //get subcat name
    		   		$sname= $db->query("SELECT name FROM `tsubcat` WHERE id='$rowsubcat[subcat]'"); 
    				$sname= $sname->fetch();

    		        //statistics calculate
    		        include('core.php');
    		        //find color red or green for tx
    				if ($tx_target>$tx) $color_tx="red"; else $color_tx="green";
    				if ($tx_target>$tx_planned) $color_tx_planned="red"; else $color_tx_planned="green";
    		        
    		        //display table
    				echo '
    				<table '; if ($_GET['page']=='plugins/availability/index') {echo 'width=\"800\"';} echo ' border="0">
    					<tr>
    				        <td>
							<h4 class="pt-2">'.T_('Pour').' '.$sname[0].' '.T_('sur').' '.$year.'</h4>
    					        <blockquote class="ml-4" >
    					        	'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
    					            <i class="fa fa-circle text-success"></i> <b>'.T_("Taux de disponibilité constaté pour l'année").' '.$year.': <font color="'.$color_tx.'" size="3">'.$tx.' %</font> '.T_('sur').' '.$tx_target.' % '.T_('attendu').'</b><br />
    					            <i class="fa fa-circle text-success"></i> '.T_("Taux de disponibilité hors interventions planifiées pour l'année").' '.$year.': <font color="'.$color_tx_planned.'">'.$tx_planned.' %</font> '.T_('sur').' '.$tx_target.' % '.T_('attendu').'<br />
    			                    <br />
    			                    <i class="fa fa-circle text-primary"></i> <b>'.T_("Durée d'indisponibilité pour l'année").' '.$year.': <font color="'.$color_tx.'">'.$global_hour.' h '.$global_min.' min</font></b><br />
    			                    <br />
    			                    <i class="fa fa-circle text-purple"></i> <b>'.T_('Liste des arrêts planifiés pour').' '.$sname[0].': ('.$total_hour_planned.' h '.$total_min_planned.' min)</b><br />
    			                    <blockquote class="ml-4" >
    			                    	'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
    			                        <i class="fa fa-caret-right text-purple"></i> <u>'.T_('Maintenance de').' '.$sname[0].'</u><br />
    			                        ';
    			                        //find and display planned ticket
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                        				$queryticket = $db->query("$queryticket");
                        				while ($rowticket=$queryticket->fetch()) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                                           	$time_min=60*"0.$time_hour[1]";
                        					$time_min=round($time_min);
                                           	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo '&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' min)<br />';
                        				}
    			                        echo '
    			                        <i class="fa fa-caret-right text-purple"></i> <u>'.T_('Autres maintenances').'</u><br />
    			                        ';
    			                        //find dependancy of planned tickets
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                        				$queryticket = $db->query("$queryticket");
                        				while ($rowticket=$queryticket->fetch()) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                    	                   	$time_min=60*"0.$time_hour[1]";
                    						$time_min=round($time_min);
                    	                   	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' m)<br />';
                        				}
    			                        echo '
    			                    </blockquote>
    			                    <i class="fa fa-circle text-warning"></i> <b>'.T_('Liste des arrêts non planifiés pour').' '.$sname[0].': ('.$total_hour_none_planned.' h '.$total_min_none_planned.' min)</b><br />
    			                    <blockquote class="ml-4" >
    			                    	'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
    			                        <i class="fa fa-caret-right text-warning"></i> <u>'.T_('Problèmes de').' '.$sname[0].'</u><br />
    			                        ';
    			                       //find and display non planned ticket
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                        				$queryticket = $db->query("$queryticket");
                        				while ($rowticket=$queryticket->fetch()) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                                           	$time_min=60*"0.$time_hour[1]";
                        					$time_min=round($time_min);
                                           	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' min)<br />';
                        				}
    			                        echo '
    			                        <i class="fa fa-caret-right text-warning"></i> <u>'.T_('Autres problème').'</u><br />
    			                        ';
    			                        //find dependancy of non planned tickets
    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                        				$queryticket = $db->query("$queryticket");
                        				while ($rowticket=$queryticket->fetch()) 
                        				{
                        					//calc time by ticket
                        					$t1 =strtotime($rowticket['start_availability']) ;
                                            $t2 =strtotime($rowticket['end_availability']) ;
                                           	$time=(($t2-$t1)/60)/60;
                                           	$time=number_format($time,2);
                                           	$time_hour=explode(".", $time);
                    	                   	$time_min=60*"0.$time_hour[1]";
                    						$time_min=round($time_min);
                    	                   	$time_hour=$time_hour[0];
                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' m)<br />';
                        				}
    			                        echo "
    			                    </blockquote>
    					        </blockquote>
    				        </td>
    				        <td>
    	   	    			";
								echo '<script type="text/javascript" src="./components/jquery/jquery.min.js"></script>';
    				   	    	//display graphic
    							$container="container".$sname[0].$year;
    							if ($_GET['page']=='plugins/availability/index') {include('./stat_bar_stacked.php');}
								if (($_GET['page']=='plugins/availability/index') || ($_GET['page']=='plugins/availability/print') ) {echo "<div id=\"$container\" style=\"min-width: 300px; height: 300px; margin: 0 auto\"></div>";}
    							echo "
    	   	    			</td>
       	    			</tr>
       	    		</table>
    		        	";
    		    }
				$querysubcat->closeCursor(); 
				echo '<br /><hr /><h1>'.T_('Détails par trimestres').'</h1><br />';
    		    //////////////////////////////////display all trimesters of this year
    		    for ($i = 1; $i <= 4; $i++)
    		    {
    		        //launch month number quarter
    		        if($i==1){$m1="01"; $m2="02"; $m3="03"; $label=T_('Janvier à Mars'); $trim_hours=90;}
    		        if($i==2){$m1="04"; $m2="05"; $m3="06"; $label=T_('Avril à Juin'); $trim_hours=91;}
    		        if($i==3){$m1="07"; $m2="08"; $m3="09"; $label=T_('Juillet à Septembre'); $trim_hours=92;}
    		        if($i==4){$m1="10"; $m2="11"; $m3="12"; $label=T_('Octobre à Décembre'); $trim_hours=92;}
    		    	echo '
    		    		<hr>
    		        	<h3>'.T_('Pour le trimestre').' '.$i.' '.T_('de l\'année').' '.$year.' ('.T_('Période de').' '.$label.')</h3>
    		       		<blockquote class="ml-4" >';
            		        $querysubcat = $db->query("SELECT * FROM `tavailability`");
                		    while ($rowsubcat=$querysubcat->fetch())
                		    { 
                		        //call statistics calc
    		                    include('core.php');
    		                    
    		                    //get subcat name
                		   		$sname= $db->query("SELECT name FROM `tsubcat` WHERE id='$rowsubcat[subcat]'"); 
                				$sname= $sname->fetch();
                				
    		                    //check 100% case
    		                    if($tx!="100.00")
    		                    {
                    				 //find color red or green for tx
                    				if ($tx_target>$tx) $color_tx="red"; else $color_tx="green";
                    				if ($tx_target>$tx_planned) $color_tx_planned="red"; else $color_tx_planned="green";
                    				
                    		        echo '
                    		        <table '; if ($_GET['page']=='plugins/availability/index') {echo 'width="800"';} echo ' border=0>
                		    			<tr>
                	        		        <h4>'.T_('Pour').' '.$sname[0].' '.T_('sur le trimestre').' '.$i.' '.T_('de l\'année').' '.$year.'</h4>
                	        		        <td>
                		        		        <blockquote class="ml-4" >
                		        		        	'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
                		        		            <i class="fa fa-circle text-success"></i> <b>'.T_('Taux de disponibilité constaté pour le trimestre').' '.$i.' '.T_('de l\'année').' '.$year.': <font color="'.$color_tx.'" size="3">'.$tx.' %</font> '.T_('sur').' '.$tx_target.' % '.T_('attendu').'.</b><br />
                		        		            <i class="fa fa-circle text-success"></i> '.T_('Taux de disponibilité hors interventions planifiées pour l\'année').' '.$year.': <font color="'.$color_tx_planned.'" >'.$tx_planned.' %</font> '.T_('sur').' '.$tx_target.' '.T_('attendu').'.<br />
                		                            <br />
                		                            <i class="fa fa-circle text-primary"></i> <b>'.T_('Durée d\'indisponibilité pour le trimestre').' '.$i.' '.T_('de l\'année').' '.$year.': <font color="green"> '.$global_hour.' h '.$global_min.' min</font></b><br />
                		                            <br />
                		                            <i class="fa fa-circle text-purple"></i> <b>'.T_('Liste des arrêts planifiés pour').' '.$sname[0].':</b> ('.$total_hour_planned.' h '.$total_min_planned.' min)<br />
                		                            <blockquote class="ml-4" >
														'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
                		                                <i class="fa fa-caret-right text-purple"></i> <u>'.T_('Maintenance de').' '.$sname[0].'</u><br />
                		                                ';
                        			                     //find and display planned ticket
														$queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                                        				$queryticket = $db->query("$queryticket");
                                        				while ($rowticket=$queryticket->fetch()) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                                           	$time_min=60*"0.$time_hour[1]";
                                        					$time_min=round($time_min);
                                                           	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' min)<br />';
                                        				}
														$queryticket->closeCursor();  
                    			                        echo '
                		                                <i class="fa fa-caret-right text-purple"></i> <u>'.T_('Autres maintenances').'</u><br />
                		                                ';
                    			                        //find dependancy of planned tickets
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=1 ORDER BY tincidents.start_availability";
                                        				$queryticket = $db->query("$queryticket");
                                        				while ($rowticket=$queryticket->fetch()) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                                       	$time_hour=explode(".", $time);
                                    	                   	$time_min=60*"0.$time_hour[1]";
                        						$time_min=round($time_min);
                                    	                   	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href=\./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' m)<br />';
                                        				}
														$queryticket->closeCursor(); 
                    			                        echo '
                		                            </blockquote>
                		                            <i class="fa fa-circle text-warning"></i> <b>'.T_('Liste des arrêts non planifiés pour').' '.$sname[0].':</b> ('.$total_hour_none_planned.' h '.$total_min_none_planned.' min)<br />
                		                            <blockquote class="ml-4" >
														'; if ($_GET['page']=='plugins/availability/index') {echo '<br />';} echo '
                		                                <i class="fa fa-caret-right text-warning"></i> <u>'.T_('Problèmes de').' '.$sname[0].'</u><br />
                		                                ';
                    			                         //find and display non planned ticket
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality WHERE subcat=$rowsubcat[subcat] AND tincidents.criticality=tcriticality.id AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                                        				$queryticket = $db->query("$queryticket");
                                        				while ($rowticket=$queryticket->fetch()) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                                           	$time_min=60*"0.$time_hour[1]";
                                        					$time_min=round($time_min);
                                                           	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' min)<br />';
                                        				}
														$queryticket->closeCursor(); 
                    			                        echo '
                		                                <i class="fa fa-caret-right text-warning"></i> <u>'.T_('Autres problème').'</u><br />
                		                                ';
                    			                        //find dependency of non planned tickets
                    			                        $queryticket = "SELECT tincidents.* FROM tincidents, tcriticality, tavailability_dep WHERE tincidents.criticality=tcriticality.id AND tincidents.subcat=tavailability_dep.subcat AND tcriticality.id=$rparameters[availability_condition_value] AND tincidents.disable=0 AND tincidents.start_availability LIKE '$year%' $months AND tincidents.availability_planned=0 ORDER BY tincidents.start_availability";
                                        				$queryticket = $db->query("$queryticket");
                                        				while ($rowticket=$queryticket->fetch()) 
                                        				{
                                        					//calc time by ticket
                                        					$t1 =strtotime($rowticket['start_availability']) ;
                                                            $t2 =strtotime($rowticket['end_availability']) ;
                                                           	$time=(($t2-$t1)/60)/60;
                                                           	$time=number_format($time,2);
                                                           	$time_hour=explode(".", $time);
                                    	                   	$time_min=60*"0.$time_hour[1]";
                                    						$time_min=round($time_min);
                                    	                   	$time_hour=$time_hour[0];
                                                           	$dateticket=date("d/m/Y",strtotime($rowticket['start_availability']));
                                        					echo '&nbsp;&nbsp;&nbsp; - <a target="_blank" href="./index.php?page=ticket&id='.$rowticket['id'].'">'.T_('Ticket').' n°'.$rowticket['id'].'</a>: '.$rowticket['title'].' '.T_('le').' '.$dateticket.' ('.$time_hour.' h '.$time_min.' m)<br />';
                                        				}
														$queryticket->closeCursor(); 
                    			                        echo "
                		                            </blockquote>
                		            		    </blockquote>
                	            		    </td>
                	            		    <td>";
                	            		    //display graphic
                							$container="container".$sname[0].$i.$year;
                							include('./stat_bar_stacked.php');
											if ($_GET['page']=='plugins/availability/index') {echo "<div id=\"$container\" style=\"min-width: 300px; height: 300px; margin: 0 auto\"></div>";}
                							echo "
                	            		    </td>
                        				</tr>
                    			    </table>
                    		        ";
    		                    } else {
    		                        echo'<h4> '.T_('Pour').' '.$sname[0].' '.T_('sur le trimestre').' '.$i.' '.T_('de l\'année').' '.$year.', '.T_('le taux de disponibilité constaté est').' <font color="green">'.$tx.' %</font> '.T_('sur').' '.$tx_target.' % '.T_('attendu').'.</h4><br />';        
    		                    }
                		    }
							$querysubcat->closeCursor(); 
            		echo "</blockquote>
            		";
    		    }
    		    echo "
            </blockquote>
        </td>
    <tr>
</table>";
?>