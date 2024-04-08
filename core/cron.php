<?php
################################################################################
# @Name : cron.php
# @Description : execute tasks in time interval
# @Call : ./index.php
# @Parameters : 
# @Author : Flox
# @Create : 09/05/2019
# @Update : 29/10/2019
# @Version : 3.1.46
################################################################################

if($rparameters['ticket_autoclose'] && $rparameters['ticket_autoclose_delay']!=0)
{
	//autoclose treatment
	if($rparameters['ticket_autoclose_state']==6) //case ticket in state wait user
	{
		$qry=$db->prepare("SELECT `id` FROM `tincidents` WHERE date_create<(NOW() - INTERVAL :delay DAY) AND `state`='6' AND `disable`='0'");
		$qry->execute(array('delay' => $rparameters['ticket_autoclose_delay']));
		while($row=$qry->fetch()) 
		{
			//modify state to 3
			$qry2=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res  WHERE `id`=:id");
			$qry2->execute(array('id' => $row['id'],'date_res' => date('Y-m-d H:i:s')));
			//insert close thread
			$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,'0','4')");
			$qry2->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s')));
			//send notifications mails
			if($rparameters['mail_auto'])
			{
				$autoclose=1;
				$_GET['id']= $row['id'];
				require('core/auto_mail.php');
			}
		}
		$qry->closeCursor();
	} else { //case for all states
		$qry=$db->prepare("SELECT `id` FROM `tincidents` WHERE date_create<(NOW() - INTERVAL :delay DAY) AND `state`!='3' AND `disable`='0'");
		$qry->execute(array('delay' => $rparameters['ticket_autoclose_delay']));
		while($row=$qry->fetch()) 
		{
			//modify state to 3
			$qry2=$db->prepare("UPDATE `tincidents` SET `state`='3',`date_res`=:date_res  WHERE `id`=:id");
			$qry2->execute(array('id' => $row['id'],'date_res' => date('Y-m-d H:i:s')));
			//insert close thread
			$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`type`) VALUES (:ticket,:date,'0','4')");
			$qry2->execute(array('ticket' => $row['id'],'date' => date('Y-m-d H:i:s')));
			//send notifications mails
			if($rparameters['mail_auto'])
			{
				$autoclose=1;
				$_GET['id']= $row['id'];
				require('core/auto_mail.php');
			}
		}
		$qry->closeCursor();
	}
	//update last execution time
	$qry=$db->prepare("UPDATE `tparameters` SET `cron_daily`=:cron_daily");
	$qry->execute(array('cron_daily' => date('Y-m-d')));
}
?>