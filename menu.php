<?php
################################################################################
# @Name : /menu.php
# @Description : display left panel menu
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 06/09/2013
# @Update : 13/08/2020
# @Version : 3.2.4 p2
################################################################################

require_once('models/request/base_request.php');

use Models\Request\BaseRequest;

//initialize variables 
if(!isset($state)) $state = ''; 

echo '
<div class="sidebar-inner">
	<div class="flex-grow-1 ace-scroll" ace-scroll>
		<div class="sidebar-section my-2">
			<div class="sidebar-section-item fadeable-left">
				<div class="fadeinable sidebar-shortcuts-mini">
					<span class="btn btn-success p-0"></span>
				</div>
				<div class="fadeable">
					<div class="sub-arrow"></div>
					<div>
						';
						//new ticket button
						if($rright['side_open_ticket'] && (($_GET['page']!='asset_list') || ($rright['side_asset_create']==0)) && ($_GET['page']!='asset')  && ($_GET['page']!='procedure') && ($_GET['page']!='project'))
						{
							if($ruser['default_ticket_state']!='')
							{
								if($ruser['default_ticket_state']=='meta_all')
								{
									$target_url='./index.php?page=request&amp;action=new&amp;userid=%25&amp;state=meta&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
								} else {
									$target_url='./index.php?page=request&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$ruser['default_ticket_state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
								}
							} else {
								$target_url='./index.php?page=request&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
								$target_url='./index.php?page=request&amp;action=new&amp;userid='.$_SESSION['user_id'].'&amp;state='.$_GET['state'].'&view='.$_GET['view'].'&date_start='.$_GET['date_start'].'&date_end='.$_GET['date_end'];
							}

							echo'
							<a href="'.$target_url.'">
								<button accesskey="n" title="'.T_("Ajoute une nouvelle demande").' (ALT+n)" onclick=\'window.location.href="'.$target_url.'"\' class="btn btn-smd btn-success">
									<i class="fa fa-plus"></i>
									'.T_('Nouvelle demande').'
								</button>
							</a>
							';
						}
						//new asset button
						if($rright['side_asset_create'] && ($rparameters['asset']==1) && (($_GET['page']=='asset_list') || ($_GET['page']=='asset') ) && ($_GET['state']!='1'))
						{
							echo'
							<button accesskey="n" title="'.T_("Ajoute un nouvel équipement").' (SHIFT+ALT+n)" onclick=\'window.location.href="index.php?page=asset&amp;action=new"\' class="btn btn-smd btn-success">
								<i class="fa fa-plus"></i>
								'.T_('Nouvel équipement').'
							</button>
							';
						}
						//new asset lot button
						if($rright['side_asset_create'] && ($rparameters['asset']==1) && (($_GET['page']=='asset_list') || ($_GET['page']=='asset') ) && ($_GET['state']=='1'))
						{
							echo'
							<button accesskey="n" title="'.T_("Permet l'ajout de plusieurs équipements à la fois").' (SHIFT+ALT+n)" onclick=\'window.location.href="index.php?page=asset_stock"\' class="btn btn-smd btn-warning">
								<i class="fa fa-plus"></i>
								'.T_('Ajouter un lot').'
							</button>
							';
						}
						//new procedure button
						if($rright['procedure_add'] && ($_GET['page']=='procedure'))
						{
							echo'
							<button accesskey="n" title="'.T_("Ajoute une nouvelle procédure").' (SHIFT+ALT+n)" onclick=\'window.location.href="index.php?page=procedure&amp;action=add"\' class="btn btn-smd btn-success">
								<i class="fa fa-plus"></i>
								'.T_('Nouvelle procédure').'
							</button>
							';
						}
						if($rright['project'] && ($_GET['page']=='project'))
						{
							echo'
							<button accesskey="n" title="'.T_("Ajoute un nouveau projet").' (SHIFT+ALT+n)" onclick=\'window.location.href="index.php?page=project&amp;action=add"\' class="btn btn-smd btn-success">
								<i class="fa fa-plus"></i>
								'.T_('Nouveau projet').'
							</button>
							';
						}
						echo '
					</div>
				</div>
			</div>
			';
			if($rright['search']!='0')
			{
				echo '
				<div class="sidebar-section-item">
					<i class="fadeinable fa fa-search text-info mr-n1"></i>
					<div class="fadeable d-inline-flex align-items-center ml-3 ml-lg-0">
						<i class="fa fa-search mr-n3 text-info"></i>
						';
						if($_GET['subpage']=='user')
						{
							echo '<form method="POST" action="index.php?page=admin&subpage=user&disable='.$_GET['disable'].'" class="form-search">';
						}elseif($_GET['page']=='asset_list' || $_GET['page']=='asset') {
							echo '<form method="POST" action="index.php?page=asset_list" class="form-search">';
						}elseif($_GET['page']=='admin' && $_GET['subpage']=='profile') {
							echo '<form method="POST" action="index.php?page=admin&subpage=profile" class="form-search">';
						}elseif($_GET['page']=='procedure' && $rright['procedure']) {
							echo '<form method="POST" action="index.php?page=procedure" class="form-search">';
						} else { 
							echo '<form method="POST" action="./index.php?page=dashboard&userid='.$_GET['userid'].'&state='.$_GET['state'].'&companyview='.$_GET['companyview'].'" class="form-search">';
						}
							echo '
									<span class="input-icon">
										';
										if($_GET['subpage']=='user')
										{
											echo '<input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" title="'.T_("Lance une recherche dans la liste des utilisateurs").'" placeholder="'.T_('Recherche utilisateur').'" id="userkeywords" name="userkeywords" class="keywords" autocomplete="on" value="'.$userkeywords.'" />';
										} elseif($_GET['page']=='asset_list' || $_GET['page']=='asset' || $_GET['tab']=='asset') {
											echo '<input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" title="'.T_("Lance une recherche dans la liste des équipements").'" placeholder="'.T_('Recherche équipement').'" id="assetkeywords" name="assetkeywords" class="keywords" autocomplete="on" value="'.$assetkeywords.'" />';
										} elseif($_GET['page']=='admin' && $_GET['subpage']=='profile' && $rright['admin']) {
											echo '<input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" title="'.T_("Lance une recherche dans les droits").'" placeholder="'.T_('Recherche droit').'" id="rightkeywords" name="rightkeywords" class="keywords" autocomplete="on" value="'.$rightkeywords.'" />';
										}elseif($_GET['page']=='procedure' && $rright['procedure']) {
											echo '<input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" title="'.T_("Lance une recherche dans les procédures").'" placeholder="'.T_('Recherche procédure').'" id="procedurekeywords" name="procedurekeywords" class="keywords" autocomplete="on" value="'.$procedurekeywords.'" />';
										} else {
											echo '<input type="text" class="sidebar-search-input pl-4 pr-3 mr-n2" maxlength="60" title="'.T_("Lance une recherche dans la liste des tickets").'" placeholder="'.T_('Recherche ticket').'" id="keywords" name="keywords" class="keywords" autocomplete="on" value="'.$keywords.'" />';
										}
										echo '
									</span>
								</form>
					</div>
				</div>';
			} 
			echo '
		</div>
		<ul class="nav has-active-border" role="navigation" aria-label="Main">
		';
			if($rright['side_your'])
			{
				//special case to count technician ticket, included ticket where technician is sender 
				if($_SESSION['profile_id']==0 && $_GET['userid'] && $_GET['userid']!='%')
				{
					$where_profil="(user='$uid' OR technician='$uid')";
				} else {
					$where_profil="$profile='$uid'";
				}
				$query="SELECT count(*) FROM `tincidents` WHERE $where_profil $where_service_your $where_agency_your AND disable='0'";
				$query=$db->query($query);
				$cntall=$query->fetch();
				$query->closeCursor(); 
				if(($_GET['page']=='dashboard' || $_GET['page']=='ticket') && $_GET['userid']!='%' && $_GET['userid']!='0') {$selected_side_your=1;} else {$selected_side_your=0;}
				
				echo "
				<li "; if($selected_side_your) {echo 'class="nav-item active open"';} else {echo 'class="nav-item"';}  echo ">
					<a  href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=%25\" class=\"nav-link nav-link dropdown-toggle\" >
						<i class=\"nav-icon fa fa-ticket-alt\"></i>
						<span class=\"nav-text fadeable\">
							"; echo T_('Vos demandes validées');
								if($cnt3[0]>0 && $rright['side_your_not_read']!=0) echo '<span class="badge badge-transparent tooltip-error" title="" data-original-title="'.$cnt3[0].' Non lus"><i title="'.T_('Tickets non lus sont en attente').'" class="fas fa-exclamation-triangle text-120 text-warning-m2"></i></span>';
							echo '
						</span>
						<b class="caret fa fa-angle-left rt-n90"></b>
					</a>
					<div class="hideable submenu collapse '; if($selected_side_your) {echo 'show';} echo '">
						<ul class="submenu-inner" >';
							//display all states link
							if($_GET['userid']!='%' && $_GET['state']=='%') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';} echo "
									<a class=\"nav-link\" href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=%25&amp;ticket=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25\">
										<i class=\"nav-icon fa fa-angle-right pr-1\"></i>
										"; echo T_('Tous les états'); echo " ($cntall[0])
									</a>
							</li>";
							 //display meta states link
							if($rparameters['meta_state']==1 && $rright['side_your_meta']!=0)
							{
								if($_SESSION['profile_id']==0 && $_GET['userid'] && $_GET['userid']!='%' && $_GET['state']=='meta') //modify counter for this state only
								{
									$where_profil="(technician='$uid')";
								} 
								$query=$db->query("SELECT COUNT(tincidents.id) FROM `tincidents`,`tstates` WHERE  $where_profil $where_service_your $where_agency_your AND `tincidents`.`state`=`tstates`.`id` AND tincidents.disable='0' AND tstates.meta=1");
								$cntmeta=$query->fetch();
								$query->closeCursor();  
								if($_GET['userid']!='%' && $_GET['state']=='meta') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';} echo "
									<a class=\"nav-link\" title=\"$label_meta\" href=\"./index.php?page=dashboard&amp;userid=$_SESSION[user_id]&amp;state=meta&amp;ticket=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25\">
										<i class=\"nav-icon fa fa-angle-right pr-1\"></i>
										"; echo T_('A traiter '); echo "($cntmeta[0])
									</a>
								</li>";
								if($_SESSION['profile_id']==0 && $_GET['userid'] && $_GET['userid']!='%') //mo
								{
									$where_profil="(user='$uid' OR technician='$uid')";
								} 
							}
							//display unread ticket
							if($cnt3[0]>0 && $rright['side_your_not_read']!=0)
							{
								if($_GET['techread']!='' && $_GET['page']!='searchengine') echo '<li class="nav-item active">'; else echo '<li class="nav-item">'; echo '
									<a class="nav-link" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;techread=0">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_('Non lus').' ('.$cnt3[0].')&nbsp;&nbsp;&nbsp;<i title="'.T_('Tickets non lus sont en attente').'" class="fa fa-exclamation-triangle text-warning"></i>
									</a>
								</li>';
								
							}
							//for each state display in sub-menu
							$qry=$db->prepare("SELECT `id`,`description`,`name` FROM `tstates` WHERE id!=5 ORDER BY number");
							$qry->execute();
							while($row=$qry->fetch()) 
							{
								$query2=$db->query("SELECT count(id) FROM `tincidents` WHERE $where_profil $where_service_your $where_agency_your AND state='$row[id]' AND disable='0'");
								$cnt=$query2->fetch();
								$query2->closeCursor(); 
								echo '
								<li ';  
								if($_GET['userid']!='%' && $_GET['state']==$row['id']) {echo ' class="nav-item active"';} else {echo 'class="nav-item"';}
								echo '>
									<a class="nav-link" title="'.T_($row['description']).'" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;state='.$row['id'].'&amp;ticket=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_($row['name']).' ('.$cnt[0].') 
									</a>
								</li>';
							}
							$qry->closeCursor();
							
							//display technician group ticket
							if($rright['side_your_tech_group']!=0 && ($_SESSION['profile_id']==4 || $_SESSION['profile_id']==0 || $_SESSION['profile_id']==3) )
							{
								//check if technician have group
								$qry=$db->prepare("SELECT `group` FROM `tgroups_assoc`, `tgroups` WHERE `tgroups_assoc`.group=`tgroups`.id AND user=:user AND `tgroups`.disable=0");
								$qry->execute(array('user' => $_SESSION['user_id']));
								while($row=$qry->fetch()) 
								{
									//count number of tickets present in this group
									$qry2=$db->prepare("SELECT COUNT(id) FROM `tincidents` WHERE t_group=:t_group AND disable='0'");
									$qry2->execute(array('t_group' => $row['group']));
									$cntgrp=$qry2->fetch();
									$qry2->closeCursor();
									//get group name
									$qry2=$db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
									$qry2->execute(array('id' => $row['group']));
									$group_name=$qry2->fetch();
									$qry2->closeCursor();
									if($row['group']==$_GET['techgroup']) echo '<li class="nav-item active">'; else echo '<li class="nav-item">'; echo '
										<a class="nav-link" href="./index.php?page=dashboard&amp;userid='.$_SESSION['user_id'].'&amp;techgroup='.$row['group'].'">
											<i class="nav-icon fa fa-angle-right pr-1"></i>
											[G] '.$group_name['name'].' ('.$cntgrp[0].')
										</a>
									</li>';
								}
								$qry->closeCursor();
							}
							echo "
						</ul>
					</div>
				</li>
				";
			}
			//display side menu for company view, all tickets of current connected user company
			if($rparameters['user_company_view']==1 && $rright['side_company'] && $ruser['company']!=0)
			{
				if($_GET['page']=='dashboard' && ($_GET['userid']=='%' || $_GET['userid']=='0') && $_GET['viewid']=='' && $_GET['companyview']) {$selected_company_view=1;} else {$selected_company_view=0;}
				//count all company tickets
				$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tusers` WHERE tincidents.user=tusers.id AND tincidents.disable='0' AND tusers.company=:company AND tincidents.disable='0'");
				$qry->execute(array('company' => $ruser['company']));
				$cntall=$qry->fetch();
				$qry->closeCursor();
				
				//count all ticket not attribute of current user company
				$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM tincidents, tusers WHERE tincidents.user=tusers.id AND tusers.company=:company AND technician='0' AND t_group='0' AND tincidents.disable='0'");
				$qry->execute(array('company' => $ruser['company']));
				$cnt6=$qry->fetch();
				$qry->closeCursor();
				
				if($selected_company_view){echo '<li class="nav-item active open" >';} else {echo '<li class="nav-item" >';}  
				echo '
					<a href="./index.php?page=dashboard&amp;userid=%25&amp;state=%25" class="nav-link dropdown-toggle">
						<i class="nav-icon fa fa-ticket-alt"></i>
						<span class="nav-text fadeable"> 
							'.T_('Ma société').'
						</span>
						<b class="caret fa fa-angle-left rt-n90"></b>
					</a>
					<div class="hideable submenu collapse '; if($selected_company_view) {echo 'show';} echo ' ">
						<ul class="submenu-inner" >';
							if($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']=='%') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
							echo '
								<a class="nav-link" href="./index.php?page=dashboard&amp;userid=%25&amp;state=%25&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;companyview=1">
									<i class="nav-icon fa fa-angle-right pr-1"></i>
									'.T_('Tous les états').' ('.$cntall[0].')
								</a>
							</li>';
							 //display meta  states link
							if($rparameters['meta_state']==1  && $rright['side_all_meta']!=0)
							{
								$qry=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tusers`,`tstates` WHERE tincidents.user=tusers.id AND tincidents.state=tstates.id AND tincidents.disable='0' AND tstates.meta=1 AND tusers.company=:company");
								$qry->execute(array('company' => $ruser['company']));
								$cntmetaall=$qry->fetch();
								$qry->closeCursor();
								
								if($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']=='meta') {echo '<li class="nav-item active">';} else {echo '<li lass="nav-item">';}
								echo '
									<a class="nav-link" title="'.$label_meta.'" href="index.php?page=dashboard&amp;userid=%25&amp;state=meta&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;companyview=1">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'; echo T_('A traiter'); echo' ('.$cntmetaall[0].')
									</a>
								</li>';
							}	
							//for each state display in sub-menu
							$qry=$db->prepare("SELECT `id`,`name`,`description` FROM `tstates` WHERE id!=5 ORDER BY number");
							$qry->execute();
							while($row=$qry->fetch()) 
							{
								$qry2=$db->prepare("SELECT COUNT(tincidents.id) FROM `tincidents`,`tusers` WHERE tincidents.user=tusers.id AND state LIKE :state AND tusers.company=:company AND tincidents.disable='0'");
								$qry2->execute(array('state' => $row['id'],'company' => $ruser['company']));
								$cnt=$qry2->fetch();
								$qry2->closeCursor();
								
								if($_GET['page']=='dashboard' && $_GET['userid']=='%' && $_GET['state']==$row['id']) {echo '<li class="nav-item active">';} else {echo '<li lass="nav-item">';} 
								echo '
									<a class="nav-link" title="'.T_($row['description']).'" href="index.php?page=dashboard&amp;userid=%25&amp;state='.$row['id'].'&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;companyview=1">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_($row['name']).' ('.$cnt[0].')
									</a>
								</li>';
							}
							$qry->closeCursor();
							echo'
						</ul>
					</div>
				</li>';
			}
			//display side menu for all tickets of current connected user
			if(
				($rright['side_all'] && $rparameters['user_limit_service']==0) || 
				($rright['side_all'] && $rparameters['user_limit_service']==1 && ($cnt_service || $cnt_agency)) || 
				($rright['side_all'] && $rparameters['user_limit_service']==1 && $rright['admin']) ||  
				($rright['side_all'] && !$rright['admin'] && $_SESSION['profile_id']==0) #allow technician to view all tickets with user_limit_service parameter
			) //not display all tickets for supervisor without service or agency, without user_limit_service tech must view all tickets
			{
				if(($_GET['userid']=='%' || $_GET['userid']=='0') && $_GET['viewid']=='' && $_GET['companyview']=='') {$side_all_ticket_selected=1;} else {$side_all_ticket_selected=0;}
				$query="SELECT count(*) FROM `tincidents` WHERE disable='0' $where_agency $where_service $parenthese2";
				$query=$db->query($query);
				$cntall=$query->fetch();
				$query->closeCursor(); 
				if($side_all_ticket_selected) {echo '<li class="nav-item active open" >';} else {echo '<li class="nav-item" >';}  
				echo '
					<a href="./index.php?page=dashboard&amp;userid=%25&amp;state=2" class="nav-link dropdown-toggle">
						<i class="nav-icon fa fa-ticket-alt"></i>
							<span class="nav-text fadeable"> 
								'.T_('Toutes les demandes').'<br>'.T_('validées');
									if($cnt5[0]>0 && $rright['side_your_not_attribute']!=0) echo '&nbsp;&nbsp;<span title="'.T_("De nouveaux tickets sont en attente d'attribution").'" class="fas fa-exclamation-triangle text-110 text-danger-m2"></span>';
								echo '
							</span>
							<b class="caret fa fa-angle-left rt-n90"></b>
					</a>
					<div class="hideable submenu collapse '; if($side_all_ticket_selected) {echo 'show';} echo' ">
						<ul class="submenu-inner" >';
							if($_GET['userid']=='%' && $_GET['state']=='%') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
							echo '
									
								<a class="nav-link" href="./index.php?page=dashboard&amp;userid=%25&amp;state=%25&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25">
									<i class="nav-icon fa fa-angle-right pr-1"></i>
									'.T_('Tous les états').' ('.$cntall[0].')
								</a>
							</li>';
							//display new tickets if exist
							if($cnt5[0]>0 && $rright['side_your_not_attribute']!=0)
							{
								if($_GET['userid']=='0' && $_GET['state']=='%') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';} echo '
									<a class="nav-link" href="./index.php?page=dashboard&amp;userid=0&amp;t_group=0&amp;state=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_('Nouveaux').' ('.$cnt5[0].')
										';if($cnt5[0]>0 && $rright['side_your_not_attribute']!=0) echo '&nbsp;&nbsp;<span title="'.T_("De nouveaux tickets sont en attente d'attribution").'" class="fas fa-exclamation-triangle text-120 text-danger-m2"></span>'; echo '
									</a>
								</li>';
							}
							//display meta states link
							if($rparameters['meta_state']==1  && $rright['side_all_meta']!=0)
							{
								$query=$db->query("SELECT COUNT(`tincidents`.`id`) FROM `tincidents`,`tstates` WHERE `tincidents`.`state`=`tstates`.`id` AND `tincidents`.disable='0' AND `tstates`.`meta`='1' $where_agency $where_service $parenthese2");
								$cntmetaall=$query->fetch();
								$query->closeCursor(); 
								if($_GET['userid']=='%' && $_GET['state']=='meta') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
								echo '
									<a class="nav-link" title="'.$label_meta.'" href="./index.php?page=dashboard&amp;userid=%25&amp;state=meta&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_('A traiter').' ('.$cntmetaall[0].')
									</a>
								</li>';
							}
							//for each state display in sub-menu
							$qry=$db->prepare("SELECT `id`,`name`,`description` FROM `tstates` WHERE id!=5 ORDER BY number");
							$qry->execute();
							while($row=$qry->fetch()) 
							{
								$query2=$db->query("SELECT count(id) FROM `tincidents` WHERE state='$row[id]' $where_agency $where_service $parenthese2 AND disable='0'");
								$cnt=$query2->fetch();
								$query2->closeCursor(); 
								if($_GET['userid']=='%' && $_GET['state']==$row['id']) {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
								echo '
									<a class="nav-link" title="'.T_($row['description']).'" href="./index.php?page=dashboard&amp;userid=%25&amp;state='.$row['id'].'&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;category=%25&amp;subcat=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_($row['name']).' ('.$cnt[0].')
									</a>
								</li>';
							}
							$qry->closeCursor();
							echo	'
						</ul>
					</div>
				</li>';
			}
            $sideRequestSelected = false;
			$allStatus = false;
			$filterStatus = null;
            if($_GET['page'] == 'request_list') {
                $sideRequestSelected = true;
                if (isset($_GET['filter-status'])) {
                    $filterStatus = $_GET['filter-status'];
                } else {
                    $allStatus = true;
                }
            }
			?>
			<li class="nav-item <?php echo ($sideRequestSelected) ? 'active' : '' ?>">
				<a href="./index.php?page=dashboard&amp;userid=%25&amp;state=2" class="nav-link dropdown-toggle">
					<i class="nav-icon fa fa-ticket-alt"></i>
					<span class="nav-text fadeable">
						<?php
						echo T_('Vos demandes').'<br>'.T_('en cours de validation');
						?>
					</span>
					<b class="caret fa fa-angle-left rt-n90"></b>
				</a>
				<div class="hideable submenu <?php echo (!$sideRequestSelected) ? 'collapse' : '' ?>" style="">
					<ul class="submenu-inner">
						<li class="nav-item <?php echo ($allStatus) ? 'active' : '' ?>">
							<a class="nav-link" href="./index.php?page=request_list&show=request-owner">
								<i class="nav-icon fa fa-angle-right pr-1"></i>
								<?php
								echo T_('Tous les états');
								?>
							</a>
						</li>
						<li class="nav-item <?php echo ($filterStatus == BaseRequest::STATUS_MODIFY) ? 'active' : '' ?>">
							<a class="nav-link" href="./index.php?page=request_list&show=request-owner&filter-status=<?php echo \Models\Request\BaseRequest::STATUS_MODIFY ?>">
								<i class="nav-icon fa fa-angle-right pr-1"></i>
								<?php
								echo T_('Demandes en attente').'<br>'.T_('de modification');
								?>
							</a>
						</li>
						<li class="nav-item <?php echo ($filterStatus == BaseRequest::STATUS_WAITING_VALIDATION) ? 'active' : '' ?>">
							<a class="nav-link" href="./index.php?page=request_list&show=request-owner&filter-status=<?php echo \Models\Request\BaseRequest::STATUS_WAITING_VALIDATION ?>">
								<i class="nav-icon fa fa-angle-right pr-1"></i>
								<?php
								echo T_('Demandes en attente').'<br>'.T_('de validation');
								?>
							</a>
						</li>
						<li class="nav-item <?php echo ($filterStatus == BaseRequest::STATUS_REJECT) ? 'active' : '' ?>">
							<a class="nav-link" href="./index.php?page=request_list&show=request-owner&filter-status=<?php echo \Models\Request\BaseRequest::STATUS_REJECT ?>">
								<i class="nav-icon fa fa-angle-right pr-1"></i>
								<?php
								echo T_('Demandes refusée');
								?>
							</a>
						</li>
						<li class="nav-item <?php echo ($filterStatus == BaseRequest::STATUS_VALID) ? 'active' : '' ?>">
							<a class="nav-link" href="./index.php?page=request_list&show=request-owner&filter-status=<?php echo \Models\Request\BaseRequest::STATUS_VALID ?>">
								<i class="nav-icon fa fa-angle-right pr-1"></i>
								<?php
								echo T_('Demandes archivée');
								?>
							</a>
						</li>
					</ul>
				</div>
			</li>
			<?php
			if($rright['side_view'])
			{
				if($_GET['viewid']!='' || $_GET['page']=='view') {$side_view_selected=1;} else {$side_view_selected=0;}
				//if exist view for connected user then display link view
				$qry=$db->prepare("SELECT `id` FROM `tviews` WHERE uid=:uid ORDER BY name");
				$qry->execute(array('uid' => $_SESSION['user_id']));
				$row=$qry->fetch();
				$qry->closeCursor();
				if(!empty($row['id']))
				{
					if($side_view_selected) {echo '<li class="nav-item active open" >';} else {echo '<li class="nav-item" >';}  
					echo '
						<a href="./index.php?page=dashboard&viewid=1" class="nav-link dropdown-toggle">
							<i class="nav-icon fa fa-eye"></i>
							<span class="nav-text fadeable"> '.T_('Vos vues').' </span>
							<b class="caret fa fa-angle-left rt-n90"></b>
						</a>
						<div class="hideable submenu collapse '; if($side_view_selected) {echo 'show';} echo '">
							<ul class="submenu-inner">';
							//get view of connected user
							$qry=$db->prepare("SELECT `id`,`name`,`category`,`subcat` FROM `tviews` WHERE uid=:uid ORDER BY name");
							$qry->execute(array('uid' => $_SESSION['user_id']));
							while($row=$qry->fetch()) 
							{
								//case for no sub categories
								if($row['subcat']==0) {$subcat='%';} else {$subcat=$row['subcat'];}
								//count entries
								$query2="SELECT COUNT(*) FROM `tincidents` WHERE category='$row[category]' AND subcat LIKE '$subcat' $where_agency $where_service $parenthese2 AND disable='0'";
								$query2=$db->query($query2);
								$n=$query2->fetch();
								$query2->closeCursor();
								if($subcat=='%') {$subcat='%25';}
								if($_GET['viewid']==$row['id'])  {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
								echo '
									<a class="nav-link" href="./index.php?page=dashboard&amp;userid=%25&amp;category='.$row['category'].'&amp;subcat='.$subcat.'&amp;viewid='.$row['id'].'&amp;state=%25&amp;ticket=%25&amp;technician=%25&amp;user=%25&amp;title=%25&amp;date_create=%25&amp;priority=%25&amp;criticality=%25&amp;company=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.$row['name'].' ('.$n[0].')
									</a>
								</li>';
							}
							$qry->closeCursor();
							echo '
							</ul>
						</div>
					</li>';
				}
			}
			if($rright['asset'] && $rparameters['asset']==1)
			{
				if($_GET['page']=='asset_list' || $_GET['page']=='asset_stock' || $_GET['page']=='asset') {$side_asset_selected=1;} else {$side_asset_selected=0;}
				if($side_asset_selected) {echo '<li class="nav-item active open" >';} else {echo '<li class="nav-item" >';}  
				echo '
					<a class="nav-link dropdown-toggle" href="./index.php?page=asset_list&amp;state=2">
						<i class="nav-icon fa fa-desktop"></i>
						<span class="nav-text fadeable">'.T_('Équipements').'</span>
					</a>';
					if($rright['side_asset_all_state']!=0)
					{
						echo '
						<div class="hideable submenu collapse '; if($side_asset_selected) {echo 'show';} echo '">
							<ul class="submenu-inner">
								';
								//query count all assets or assets of company
								if($rright['asset_list_company_only']!=0)
								{
									$qry=$db->prepare("SELECT COUNT(tassets.id) FROM `tassets`,`tusers` WHERE `tassets`.`user`=`tusers`.`id` AND `tassets`.`disable`='0' AND `tusers`.`company`=:company");
									$qry->execute(array('company' => $ruser['company']));
									$cnt=$qry->fetch();
									$qry->closeCursor();
									
								} else {
									$qry=$db->prepare("SELECT COUNT(id) FROM `tassets` WHERE `disable`='0'");
									$qry->execute();
									$cnt=$qry->fetch();
									$qry->closeCursor();
								}
								if(($_GET['page']=='asset_list' || $_GET['page']=='asset') && $_GET['state']=='%') {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
									echo '
									<a class="nav-link" title="'.T_('Tous les équipements').'" href="./index.php?page=asset_list&amp;state=%25">
										<i class="nav-icon fa fa-angle-right pr-1"></i>
										'.T_('Tous').' ('.$cnt[0].')
									</a>
								</li>
								';
								//for each state display in sub-menu
								$qry=$db->prepare("SELECT `id`,`name`,`description` FROM `tassets_state` WHERE disable='0' ORDER BY `order`");
								$qry->execute();
								while($row=$qry->fetch()) 
								{
									//query count all assets or assets of company
									if($rright['asset_list_company_only']!=0)
									{
										$qry2=$db->prepare("SELECT COUNT(tassets.id) FROM `tassets`,`tusers` WHERE tassets.user=tusers.id AND tassets.state=:state AND tassets.disable='0' AND tusers.company=:company");
										$qry2->execute(array('state' => $row['id'],'company' => $ruser['company']));
										$cnt=$qry2->fetch();
										$qry2->closeCursor();
									} else {
										$qry2=$db->prepare("SELECT COUNT(id) FROM `tassets` WHERE `state`=:state AND `disable`='0'");
										$qry2->execute(array('state' => $row['id']));
										$cnt=$qry2->fetch();
										$qry2->closeCursor();
									}
									if(($_GET['page']=='asset_list' || $_GET['page']=='asset') && $_GET['state']==$row['id'] && $_GET['warranty']!=1) {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
									echo '
										<a class="nav-link" title="'.T_($row['description']).'" href="./index.php?page=asset_list&amp;state='.$row['id'].'">
											<i class="nav-icon fa fa-angle-right pr-1"></i>
											'.T_($row['name']).' ('.$cnt[0].')
										</a>
									</li>';
								}
								$qry->closeCursor();
								
								//display warranty link if parameter is enable
								if($rparameters['asset_warranty']==1)
								{
									$today=date('Y-m-d');
									//query count all assets or assets of company
									if($rright['asset_list_company_only']!=0)
									{
										$qry2=$db->prepare("SELECT COUNT(tassets.id) FROM `tassets`,`tusers` WHERE tassets.user=tusers.id AND tassets.state LIKE '2' AND tassets.date_end_warranty > :date_end_warranty AND tassets.disable='0' AND tusers.company=:company");
										$qry2->execute(array('date_end_warranty' => $today,'company' => $ruser['company']));
										$cnt=$qry2->fetch();
										$qry2->closeCursor();
									} else {
										$qry2=$db->prepare("SELECT count(id) FROM `tassets` WHERE state LIKE '2' AND date_end_warranty > :date_end_warranty AND disable='0'");
										$qry2->execute(array('date_end_warranty' => $today));
										$cnt=$qry2->fetch();
										$qry2->closeCursor();
									}
									if($_GET['page']=='asset_list' && $_GET['warranty']==1) {echo '<li class="nav-item active">';} else {echo '<li class="nav-item">';}
									echo '
										<a class="nav-link" title="'.T_('Liste des équipements en fonction de leurs garanties').'" href="./index.php?page=asset_list&amp;state=2&amp;warranty=1">
											<i class="nav-icon fa fa-angle-right pr-1"></i>
											'.T_('Garanties').'  ('.$cnt[0].')
										</a>
									</li>';
								}
								echo'
							</ul>
						</div>';
					}
					echo '
				</li>';
			}
			if($rright['procedure'] && $rparameters['procedure']==1)
			{
				if($_GET['page']=='procedure') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} 
				 echo '
					<a class="nav-link" href="index.php?page=procedure" >
						<i class="nav-icon fa fa-book"></i>
						<span class="nav-text fadeable">'.T_('Procédures').'</span>
					</a>
				</li>
				';
			}
			if($rright['planning'] && $rparameters['planning']==1)
			{
				if($_GET['page']=='calendar') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} 
				 echo '
					<a class="nav-link" href="./index.php?page=calendar" >
						<i class="nav-icon fa fa-calendar"></i>
						<span class="nav-text fadeable">'.T_('Calendrier').'</span>
					</a>
				</li>';
			}
			if($rright['availability'] && $rparameters['availability']==1)
			{
				if($_GET['page']=='plugins/availability/index') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} 
				 echo '
					<a class="nav-link" href="index.php?page=plugins/availability/index" >
						<i class="nav-icon fa fa-clock"></i>
						<span class="nav-text fadeable">'.T_('Disponibilité').'</span>
					</a>
				</li>';
			}
			if($rright['project'] && $rparameters['project']==1)
			{
				if($_GET['page']=='project') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} 
				 echo '
					<a class="nav-link" href="index.php?page=project">
						<i class="nav-icon fa fa-tasks"></i>
						<span class="nav-text fadeable">'.T_('Projets').'</span>
					</a>
				</li>';
			}
			if($rright['stat'])
			{
				if($_GET['page']=='stat') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} 
				echo '
					<a class="nav-link" href="./index.php?page=stat&tab=ticket">
						<i class="nav-icon fa fa-chart-line"></i>
						<span class="nav-text fadeable">'.T_('Statistiques').'</span>
					</a>
				</li>';
			}
			if($rright['admin'] || $rright['admin_groups']!=0 || $rright['admin_lists']!=0 )
			{
				//select destination page by rights
				if($rright['admin']!=0) {$dest_subpage='parameters';}
				if($rright['admin_groups']!=0) {$dest_subpage='group';}
				if($rright['admin_lists']!=0) {$dest_subpage='list';}
				if($_GET['page']=='admin') {echo '<li class="nav-item active open" >';} else {echo '<li class="nav-item" >';}  
				echo '
					<a class="nav-link dropdown-toggle" href="./index.php?page=admin&subpage='.$dest_subpage.'">
						<i class="nav-icon fa fa-cogs"></i>
						<span class="nav-text fadeable"> '.T_('Administration').' </span>
						<b class="caret fa fa-angle-left rt-n90"></b>
					</a>
					<div class="hideable submenu collapse '; if($_GET['page']=='admin') {echo 'show';} echo ' ">
						<ul class="submenu-inner">';
							if($rright['admin'])
							{
								if($_GET['page']=='admin' && $_GET['subpage']=='parameters') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=parameters">
										<i class="nav-icon fa fa-cog"></i>&nbsp;
										'.T_('Paramètres').'
									</a>
								</li>';
								if($_GET['page']=='admin' && $_GET['subpage']=='user') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=user">
										<i class="nav-icon fa fa-user"></i>&nbsp;
										'.T_('Utilisateurs').'
									</a>
								</li>';
							}
							if($rright['admin_groups'] || $rright['admin'])
							{
								if($_GET['page']=='admin' && $_GET['subpage']=='group') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=group">
										<i class="nav-icon fa fa-users"></i>&nbsp;
										'.T_('Groupes').'
									</a>
								</li>';
							}
							if($rright['admin'])
							{
								if($_GET['page']=='admin' && $_GET['subpage']=='profile') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=profile">
										<i class="nav-icon fa fa-lock"></i>&nbsp;
										'.T_('Droits').'
									</a>
								</li>';
							}
							if($rright['admin_lists'] || $rright['admin'])
							{
								if($_GET['page']=='admin' && $_GET['subpage']=='list') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=list">
										<i class="nav-icon fa fa-list"></i>&nbsp;
										'.T_('Listes').'
									</a>
								</li>';
							}
							if($rright['admin'])
							{
								if($rright['admin_backup'])
								{
									if($_GET['page']=='admin' && $_GET['subpage']=='backup') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
										<a class="nav-link" href="./index.php?page=admin&subpage=backup">
											<i class="nav-icon fa fa-save"></i>&nbsp;
											'.T_('Sauvegardes').'
										</a>
									</li>';
								}
								if($rparameters['update_menu'])
								{
									if($_GET['page']=='admin' && $_GET['subpage']=='update') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=update">
										<i class="nav-icon fa fa-cloud-upload-alt"></i>&nbsp;
										'.T_('Mise à jour').'
									</a>
									</li>';
								}
								if($_GET['page']=='admin' && $_GET['subpage']=='system') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=system">
										<i class="nav-icon fa fa-desktop"></i>&nbsp;
										'.T_('Système').'
									</a>
								</li>';
								if($rparameters['log'])
								{
									if($_GET['page']=='admin' && $_GET['subpage']=='log') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=log">
										<i class="nav-icon fa fa-clipboard-list"></i>&nbsp;
										'.T_('Logs').'
									</a>
									</li>';
								}
								
								if($_GET['page']=='admin' && $_GET['subpage']=='infos') {echo '<li class="nav-item active" >';} else {echo '<li class="nav-item" >';} echo '
									<a class="nav-link" href="./index.php?page=admin&subpage=infos">
										<i class="nav-icon fa fa-info-circle"></i>&nbsp;
										'.T_('Informations').'
									</a>
								</li>';
							}
							echo '
						</ul>
					</div>
				</li>';
			}
			echo '
			
		</ul>
	</div><!-- /.sidebar scroll -->
</div>
';
?>
