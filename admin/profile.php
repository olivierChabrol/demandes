<?php
################################################################################
# @Name : profile.php
# @Description : rights management for all profiles
# @call : admin.php
# @parameters : 
# @Author : Flox
# @Create : 06/07/2013
# @Update : 17/08/2020
# @Version : 3.2.4
################################################################################

//init var
if(empty($rightkeywords)) {$rightkeywords='%';} else {$rightkeywords='%'.$rightkeywords.'%';}

//dynamic rights table
echo '
	<div class="page-header position-relative">
		<h1 class="page-title text-primary-m2">
			<i class="fa fa-lock"></i> '.T_('Gestion des droits').'
		</h1>
	</div>
	<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
		<div class="card-body p-0 table-responsive-xl">
			<table id="sample-table-1" class="table text-dark-m1 brc-black-tp10 mb-1">
				<thead>
					<tr class="bgc-white text-secondary-d3 text-95" >
						<th class="py-3 pl-35"><i class="fa fa-code"></i> '.T_('Code').'</th>
						<th class="py-3 pl-35"><i class="fa fa-comment"></i> '.T_('Description').'</th>
						<th class="py-3 pl-35">'.T_('Utilisateur').'</th>
						<th class="py-3 pl-35" style="min-width:210px" >'.T_('Utilisateur avec pouvoir').'</th>
						<th class="py-3 pl-35">'.T_('Superviseur').'</th>
						<th class="py-3 pl-35">'.T_('Technicien').'</th>
						<th class="py-3 pl-35">'.T_('Administrateur').'</th>
					</tr>
				</thead>
				<tbody>';
				$qry = $db->prepare("SHOW FULL COLUMNS FROM `trights` WHERE Field LIKE :field OR Comment LIKE :comment");
				$qry->execute(array('field' => $rightkeywords,'comment' => $rightkeywords));
				while ($row=$qry->fetch()) 
				{	
					//exclude id and profile
					if($row[0]!='id' && $row[0]!='profile')
					{
						//special char 
						$row['Comment']=$row['Comment'];
						echo '
						<tr class="bgc-h-orange-l4">
							<td class="text-secondary-d2 text-95 text-600">'.$row['0'].'</td>
							<td class="text-dark-m3">'.T_($row['Comment']).'</td>
							<td class="text-dark-m3">
								<center>';
									//find value
									$qry2 = $db->prepare("SELECT * FROM `trights` WHERE profile=2");
									$qry2->execute();
									$rv=$qry2->fetch();
									$qry2->closeCursor();
									if($rv[$row[0]]!=0)
									{
										echo'	
											<button id="2_button_'.$row[0].'" title="'.T_('Désactiver pour le profil utilisateur').'" onClick="update_profil(\''.$row[0].'\',2,0)"  class="btn action-btn btn-xs btn-success">
												<i id="2_icon_'.$row[0].'" class="fa fa-check"></i>
											</button>
										';
									} else {
										echo'
										<button id="2_button_'.$row[0].'" title="'.T_('Activer pour le profil utilisateur').'" onClick="update_profil(\''.$row[0].'\',2,2)" class="btn action-btn btn-xs btn-danger">
											<i id="2_icon_'.$row[0].'" class="fa fa-ban"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td class="text-dark-m3">
								<center>';
									//find value
									$qry2 = $db->prepare("SELECT * FROM `trights` WHERE profile='1'");
									$qry2->execute();
									$rv=$qry2->fetch();
									$qry2->closeCursor();
									if($rv[$row[0]]!=0)
									{
										echo'
											<button id="1_button_'.$row[0].'" title="'.T_('Désactiver pour le profil utilisateur avec pouvoir').'"  onClick="update_profil(\''.$row[0].'\',1,0)" class="btn action-btn btn-xs btn-success">
											<i id="1_icon_'.$row[0].'" class="fa fa-check"></i>
											</button>
										';
									} else {
										echo'
										<button id="1_button_'.$row[0].'" title="'.T_('Activer pour le profil utilisateur avec pouvoir').'"  onClick="update_profil(\''.$row[0].'\',1,2)" class="btn action-btn btn-xs btn-danger">
											<i id="1_icon_'.$row[0].'" class="fa fa-ban"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td class="text-dark-m3">
								<center>';
									//find value
									$qry2 = $db->prepare("SELECT * FROM `trights` WHERE profile='3'");
									$qry2->execute();
									$rv=$qry2->fetch();
									$qry2->closeCursor();
									if($rv[$row[0]]!=0)
									{
										echo'
											<button id="3_button_'.$row[0].'" title="'.T_('Désactiver pour le profil superviseur').'"  onClick="update_profil(\''.$row[0].'\',3,0)" class="btn action-btn btn-xs btn-success">
											<i id="3_icon_'.$row[0].'" class="fa fa-check"></i>
											</button>
										';
									} else {
										echo'
										<button id="3_button_'.$row[0].'" title="'.T_('Activer pour le profil superviseur').'" onClick="update_profil(\''.$row[0].'\',3,2)" class="btn action-btn btn-xs btn-danger">
											<i id="3_icon_'.$row[0].'" class="fa fa-ban"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td class="text-dark-m3">
								<center>';
									//find value
									$qry2 = $db->prepare("SELECT * FROM `trights` WHERE profile='0'");
									$qry2->execute();
									$rv=$qry2->fetch();
									$qry2->closeCursor();
									if($rv[$row[0]]!=0)
									{
										echo'
											<button id="0_button_'.$row[0].'" title="'.T_('Désactiver pour le profil technicien').'"  onClick="update_profil(\''.$row[0].'\',0,0)" class="btn action-btn btn-xs btn-success">
												<i id="0_icon_'.$row[0].'" class="fa fa-check"></i>
											</button>
										';
									} else {
										echo'
										<button id="0_button_'.$row[0].'" title="'.T_('Activer pour le profil technicien').'"  onClick="update_profil(\''.$row[0].'\',0,2)" class="btn action-btn btn-xs btn-danger">
											<i id="0_icon_'.$row[0].'" class="fa fa-ban"></i>
										</button>
										';
									}
									echo'
								</center>	
							</td>
							<td class="text-dark-m3">
								<center>';
									if($row[0]!='admin') //avoid disable admin right problem for admin profile
									{
										//find value
										$qry2 = $db->prepare("SELECT * FROM `trights` WHERE profile='4'");
										$qry2->execute();
										$rv=$qry2->fetch();
										$qry2->closeCursor();
										if($rv[$row[0]]!=0)
										{
											echo'
												<button id="4_button_'.$row[0].'" title="'.T_('Désactiver pour le profil administrateur').'" onClick="update_profil(\''.$row[0].'\',4,0)" class="btn action-btn btn-xs btn-success">
												<i id="4_icon_'.$row[0].'" class="fa fa-check"></i>
												</button>
											';
										} else {
											echo'
											<button id="4_button_'.$row[0].'" title="'.T_('Activer pour le profil administrateur').'" onClick="update_profil(\''.$row[0].'\',4,2)" class="btn action-btn btn-xs btn-danger">
												<i id="4_icon_'.$row[0].'" class="fa fa-ban"></i>
											</button>
											';
										}
									}
									echo'
								</center>	
							</td>
						</tr>
						';
					}
				}
				$qry->closeCursor(); 
				echo'
				</tbody>
			</table>
		</div>
	</div><!--/span-->';
	
	if($rright['admin'])
	{
		echo "
		<script>
		function update_profil(right,profile,enable)
		{
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if(xmlhttp.readyState == 4 && xmlhttp.status == 200) 
				{
					//alert(xmlhttp.responseText);
					if(enable==0)
					{
						document.getElementById(profile+'_icon_'+right).className = 'fa fa-ban';
						document.getElementById(profile+'_button_'+right).className = 'btn action-btn btn-xs btn-danger';
						document.getElementById(profile+'_button_'+right).setAttribute('onclick','update_profil(\''+right+'\','+profile+',2)')
					} else {
						document.getElementById(profile+'_icon_'+right).className = 'fa fa-check';
						document.getElementById(profile+'_button_'+right).className = 'btn action-btn btn-xs btn-success';
						document.getElementById(profile+'_button_'+right).setAttribute('onclick','update_profil(\''+right+'\','+profile+',0)')
					}
				}
			};
			var token = '$_COOKIE[token]' ;
			xmlhttp.open('GET', './includes/profile_update.php?right='+right+'&profile='+profile+'&enable='+enable+'&token='+token+'&user_id='+$_SESSION[user_id], true);
			xmlhttp.send();
		}
		</script>
		";
	}
?>