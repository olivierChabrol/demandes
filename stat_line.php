<?php
################################################################################
# @Name : stat_line.php
# @Description : display line graphic
# @Call : /stats/line_tickets.php and ./stats/ticket_line.php
# @Parameters : 
# @Author : Flox
# @Create : 06/10/2012
# @Update : 22/06/2020
# @Version : 3.2.2
################################################################################

//customize label for graph selected
if($_GET['tab']=='asset')
{
	$label_type='';
	$label_state1=T_('Installés');
	$label_state2=T_('Recyclés');
	$label_state3='';
	$subtitle='<u>'.T_('Total').' '.$label_state1.' :</u> '.$count.' / <u>'.T_('Total').' '.$label_state2.' :</u> '.$count2.'  / <u>'.T_('Total depuis le début').' :</u> '.$count4;
} elseif ($container=='container1') {
	$label_type='';
	$label_state1=T_('Avancés *');
	$label_state2=T_('Ouverts');
	$label_state3=T_('Fermés');
	$subtitle='<u>'.$label_state2.' :</u> '.$count.' / <u>'.$label_state3.' :</u> '.$count2.'  / <u> '.$label_state1.' :</u> '.$count3.' / <u>'.T_('Total depuis le début').' :</u> '.$count4;
} elseif ($container=='container9') {
	$label_type=T_('Élément de <br />résolutions');
	$label_state1='';
	$label_state2='';
	$label_state3='';
	$current='/ <u>'.T_('Total en cours').' :</u> '.$count3;
	$subtitle='';
} else {
	$label_type='';
	$label_state1='';
	$label_state2='';
	$label_state3='';
	$current='';
	$subtitle='';
}
?>

<script type="text/javascript">
	$(function () {
		var chart1;
		$(document).ready(function() {
			chart1 = new Highcharts.Chart({
				chart: {
					backgroundColor: '<?php echo $bgc; ?>',
					renderTo: '<?php echo $container; ?>',
					type: 'line',
					marginRight: 20,
					marginBottom: 80,
				},
				credits: {
					enabled: false
				},
				title: {
					text: '<?php echo $libchart; ?>',
					x: -20 //center
				},
				subtitle: {
					text: "<?php echo $subtitle; ?>",
					x: -20
				},
				xAxis: {
				allowDecimals:false
				},
				yAxis: {
					allowDecimals:false,
					title: {
						text: "<?php echo $liby; ?>"
					},
					plotLines: [{
						value: 0,
						width: 1,
						color: '#808080'
					}]
				},
				tooltip: {
					formatter: function() {
							return '<b>'+ this.series.name +'</b><br/>'+
							this.x +': '+ this.y +' <?php echo $label_type; ?>';
					}
				},
				legend: {
					 layout: "horizontal",
					 itemDistance: 50,
					align: "center"
					
				},
				series: 
				[
				    {
    					name: '<?php echo $label_type.' '.$label_state1; ?>',
    					data: [
        					<?php
        					for($i=0;$i<sizeof($values1);$i++) 
							{ 
							    $last=sizeof($values1)-1;
						        if ($i!=$last) echo '['.$xnom1[$i].','.$values1[$i].'],'; else echo '['.$xnom1[$i].','.$values1[$i].']';
							} 
        					?>
    					]
				    }
					<?php
					if ($label_state2)
					{
						echo '
						,
						{
							name: \''.$label_type.' '.$label_state2.' \',
							data: [
								';
								for($i=0;$i<sizeof($values2);$i++) 
								{ 
									$last=sizeof($values2)-1;
									if ($i!=$last) echo '['.$xnom2[$i].','.$values2[$i].'],'; else echo '['.$xnom2[$i].','.$values2[$i].']';
								}
								echo '
							]
						}
						';
					}
					if ($label_state3)
					{
						echo '
						,
						{
							name: \''.$label_state3.' \',
							data: [
								';
								for($i=0;$i<sizeof($values3);$i++) 
								{ 
									$last=sizeof($values3)-1;
									if ($i!=$last) echo '['.$xnom3[$i].','.$values3[$i].'],'; else echo '['.$xnom3[$i].','.$values3[$i].']';
								}
								echo '
							]
						}
						';
					}
					?>
				]
			});
		});
	});
</script>