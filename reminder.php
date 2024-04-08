<?php
################################################################################
# @Name : reminder.php
# @Description : display popup event
# @Call : index.php
# @Parameters :  
# @Author : Flox
# @Create : 20/07/2011
# @Update : 04/03/2020
# @Version : 3.2.0
################################################################################

//disable event
if($_GET['event'] && $_GET['disable']==1)
{
	$qry=$db->prepare("UPDATE `tevents` SET `disable`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['event']));
}
//display event
$qry=$db->prepare("SELECT `id`,`date_start`,`incident` FROM `tevents` WHERE `technician`=:technician AND `disable`='0' AND `type`='1'");
$qry->execute(array('technician' => $_SESSION['user_id'])); 
while($event=$qry->fetch()) 
{
	$devent=explode(" ",$event['date_start']);
	//day check
	if($devent[0]<=$daydate) 
	{
		//hour check
		$currenthour=date("H:i:s");
		$eventhour=explode(" ",$event['date_start']);
		if($currenthour>$eventhour[1])
		{
			//get ticket data
			$qry=$db->prepare("SELECT `title` FROM `tincidents` WHERE `id`=:id");
			$qry->execute(array('id' => $event['incident']));
			$rticket=$qry->fetch();
			$qry->closeCursor();
			
			//display modal
			echo '
			<div class="modal fade" id="reminder" tabindex="-1" role="dialog">
				<div class="modal-dialog " role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title"><i class="fa fa-bell text-warning"></i>  Rappel pour le ticket n°'.$event['incident'].'</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">×</span>
							</button>
						</div>
						<div class="modal-body">
							<u>Titre:</u><br /> '.$rticket['title'].'
						</div>
						<div class="modal-footer">
							<button type="button" onclick="document.location.href=\'./index.php?page=ticket&id='.$event['incident'].'&hide=1\'"  class="btn btn-info"><i class="fa fa-eye"></i> '.T_('Voir le ticket').'</button>
							<button type="button" onclick="document.location.href=\'./index.php?page='.$_GET['page'].'&id='.$event['incident'].'&userid='.$_GET['userid'].'&state=%25&event='.$event['id'].'&disable=1\'" class="btn btn-success"><i class="fa fa-check"></i> '.T_('Accréditer').'</button>
						</div>
					</div>
				</div>
			</div>
			<script>$(document).ready(function(){$("#reminder").modal(\'show\');});</script>
			';
		}
	}
}
$qry->closeCursor();
?>