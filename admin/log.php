<?php
################################################################################
# @Name : ./admin/log.php
# @Description : display security log
# @Call : /admin.php
# @Parameters : 
# @Author : Flox
# @Create : 24/04/2020
# @Update : 24/08/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($_GET['clear'])) $_GET['clear']=''; 

$qry=$db->prepare("SELECT `type` FROM `tlogs` ORDER BY `type`");
$qry->execute();
$log=$qry->fetch();
$qry->closeCursor();

//head
echo '
	<div class="page-header position-relative">
		<h1 class="page-title text-primary-m2">
			<i class="fa fa-clipboard-list text-primary-m2"></i> 
			'.T_('Logs').'
		</h1>
	</div>
';

if(empty($log[0])) //case no log
{
	echo DisplayMessage('info',T_("Aucune information n'est disponible dans les logs."));
}
elseif($rright['admin'])
{
	//init var
	if(!isset($_GET['log'])) $_GET['log']=$log['type']; 
	//clear log
	if($_GET['clear'] && $_GET['log'])
	{
		$qry=$db->prepare("DELETE FROM `tlogs` WHERE type=:type");
		$qry->execute(array('type' => $_GET['log']));
	}
	echo '
	<div class="tabs-above shadow">
		<ul class="nav nav-tabs nav-justified" role="tablist">
			';
			$qry=$db->prepare("SELECT DISTINCT(`type`) FROM `tlogs` ORDER BY `type`");
			$qry->execute();
			while($log=$qry->fetch()) 
			{
				echo '
				<li class="nav-item mr-1px" >
					<a href="./index.php?page=admin&amp;subpage=log&amp;log='.$log['type'].'" class="nav-link '; if($_GET['log']==$log['type']) {echo 'active';} echo '">
						'.ucfirst($log['type']).'
					</a>
				</li>
				';
			}
			$qry->closeCursor();
			echo '
		</ul>
		<div class="tab-content" style="background-color:#FFF;">
			<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
				<p>
					<button onclick=\'window.location.href="./index.php?page=admin&amp;subpage=log&amp;log='.$_GET['log'].'&amp;clear=1";\' class="btn btn-danger">
						<i class="fa fa-trash"></i> '.T_('Effacer ce log').'
					</button>
				</p>
			</div>
			<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
				<div class="card-body p-0 table-responsive-xl">
					<table id="sample-table-1" class="table text-dark-m1 brc-black-tp10 mb-1">
						<thead>
							<tr class="bgc-white text-secondary-d3 text-95">
								<th>'.T_('Date').'</th>
								<th>'.T_('Message').'</th>
								<th>'.T_('Utilisateur').'</th>
								<th>'.T_('IP').'</th>
							</tr>
						</thead>
						<tbody>
							';
							$qry=$db->prepare("SELECT * FROM `tlogs` WHERE type=:type ORDER BY date DESC");
							$qry->execute(array('type' => $_GET['log']));
							while($log=$qry->fetch()) 
							{
								echo '<tr class="bgc-h-orange-l4">';
								//get user informations
								$qry2=$db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
								$qry2->execute(array('id' => $log['user']));
								$user=$qry2->fetch();
								$qry2->closeCursor();
								
								echo '<td>'.$log['date'].'</td>';
								echo '<td>'.T_($log['message']).'</td>';
								echo '<td>'.$user['firstname'].' '.$user['lastname'].'</td>';
								echo '<td>'.$log['ip'].'</td>';
								echo '</tr>';
							}
							$qry->closeCursor();
							echo '
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	';
} else {
	echo DisplayMessage('error',T_("Vous n'avez pas accès à cette section, contacter votre administrateur"));
}