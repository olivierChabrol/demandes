<?php
################################################################################
# @Name : modalbox.php
# @Description : display modalbox
# @Call : ticket, dashboard
# @Parameters : $boxtitle $boxtext $valid $cancel $action1 $action2
# @Author : Flox
# @Create : 19/10/2013
# @Update : 26/07/2016
# @Version : 3.1.10
################################################################################

//initialize variables 
if(!isset($boxtext)) $boxtext = '';
if(!isset($boxtitle)) $boxtitle = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
?>

<div id="dialog-confirm" >
	<div class="alert alert-info bigger-110">
		<?php echo "$boxtext"; ?>
	</div>
</div><!-- #dialog-confirm -->

<script type="text/javascript">
	window.jQuery || document.write("<script src='./template/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
</script>
<!-- inline scripts related to this page -->

<script type="text/javascript">
	jQuery(function($) {
		//override dialog's title function to allow for HTML titles
		$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
			_title: function(title) {
				var $title = this.options.title || '&nbsp;'
				if( ("title_html" in this.options) && this.options.title_html == true )
					title.html($title);
				else title.text($title);
			}
		}));
		
		$( "#dialog-confirm" ).removeClass('hide').dialog({
			resizable: false,
			modal: true,
			title: "<div class='widget-header widget-header-small'><h4 class='smaller'><?php echo $boxtitle; ?></h4></div>",
			title_html: true,
			buttons: [
				{
					html: "<i class='icon-ok bigger-110'></i>&nbsp; <?php echo $valid; ?>",
					"class" : "btn btn-success btn-xs",
					click: function() {
						<?php echo $action1; ?>
					}
				}
				,
				{
					html: "<i class='icon-remove bigger-110'></i>&nbsp; <?php echo $cancel; ?>",
					"class" : "btn btn-danger btn-xs",
					click: function() {
						<?php echo $action2; ?> 

					}
				}
			]
		});
	});
</script>