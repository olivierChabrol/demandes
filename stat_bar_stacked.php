<?php
################################################################################
# @Name : stat_bar_stacked.php
# @Description : display bar graph to availability page
# @Call : /availability.php
# @Parameters : unit, values, names, libchart
# @Author : Flox
# @Create : 05/06/2014
# @Update : 20/02/2020
# @Version : 3.2.0
################################################################################
?>

<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: '<?php echo $container; ?>',
                type: 'column'
            },
			credits: {
				enabled: false
			},
            title: {
                text: ''
            },
            xAxis: {
                categories: ['<?php echo T_('Disponibilité'); ?>']
            },
            yAxis: {
                title: {
                    text: '<?php echo T_('Pourcentage de disponibilité'); ?>'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'right',
                x: -10,
                verticalAlign: 'top',
                y: 30,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColorSolid) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'%<br/>'+
                        'Total: '+ this.point.stackTotal+'%<br/>';
                }
            },
            plotOptions: {
                column: {
                    stacking: 'percent',
                    dataLabels: {
                    	formatter: function(){
                    		return '<br/><b>'+this.y+'%</b><br/>';
                    	},
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                    }
                }
            },
            series: [{
                name: '<?php echo T_('Indisponibilité'); ?>',
                data: [<?php echo $tx2; ?>],
                color: '#AA4643'
            }, {
                name: '<?php echo T_('Disponibilité'); ?>',
                data: [<?php echo $tx; ?>],
                color: '#89A54E'
            }]
        });
    });
});
</script>