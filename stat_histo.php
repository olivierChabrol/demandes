<?php
################################################################################
# @Name : stat_histo.php
# @Description : display histogram
# @Call : /stats/histo_load.php
# @Parameters : 
# @Author : Flox
# @Create : 06/11/2012
# @Update : 22/06/2020
# @Version : 3.2.2
################################################################################
?>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '<?php echo $container; ?>',
                type: 'column',
				backgroundColor:'<?php echo $bgc; ?>'
            },
			credits: {
				enabled: false
			},
            title: {
                text: '<?php echo $libchart; ?>'
            },
            subtitle: {
                text: "<?php echo T_("Nombre d'heures de travail restantes dans les tickets ouverts"); ?>"
            },
            xAxis: {
                categories: [
                   <?php
					for($i=0;$i<sizeof($xnom);$i++) 
					{ 
						$k=sizeof($values);
						$k=$k-1;
						if ($i==$k) echo "\"$xnom[$i]\""; else echo "\"$xnom[$i]\"".','; 
					} 
					?>
                ]
            },
            yAxis: {
                min: 0,
                title: {
                    text: "<?php echo T_("Nombre d'heures"); ?>"
                }
            },
            legend: {
                layout: 'vertical',
                backgroundColor: '#FFFFFF',
                align: 'left',
                verticalAlign: 'top',
                x: 100,
                y: 70,
                floating: true,
                shadow: true
            },
            tooltip: {
                formatter: function() {
                    return ''+
                        this.x +': '+ this.y +' h';
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
                series: [
				{
					name: '<?php echo T_('Charge en heures'); ?>',
					data: [
					 <?php
					for($i=0;$i<sizeof($values);$i++) 
					{ 
						$k=sizeof($values);
						$k=$k-1;
						if ($i==$k) echo "['$xnom[$i]', $values[$i]]"; else echo "['$xnom[$i]', $values[$i]],";
					} 
					?>
					]
				}
    
				]
        });
    });
});
</script>