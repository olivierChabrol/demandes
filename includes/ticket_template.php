<?php
################################################################################
# @Name : ./includes/ticket_template.php
# @Description : select and apply template ticket
# @Call : /core/ticket.php
# @Author : Flox
# @Update : 21/10/2014
# @Update : 20/07/2020
# @Version : 3.2.2 p1
################################################################################

//initialize variables 
if(!isset($_POST['duplicate'])) $_POST['duplicate'] = ''; 
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($row['title'])) $row['title'] = '';
if(!isset($row['user'])) $row['user'] = '';
if(!isset($row['u_group'])) $row['u_group'] = '';
if(!isset($row['priority'])) $row['priority'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['state'])) $row['state'] = '';
if(!isset($row['time'])) $row['time'] = '';
if(!isset($row['category'])) $row['category'] = '';
if(!isset($row['subcat'])) $row['subcat'] = '';
if(!isset($row['technician'])) $row['technician'] = '';
if(!isset($row['t_group'])) $row['t_group'] = '';
if(!isset($row['criticality'])) $row['criticality'] = '';
if(!isset($row['type'])) $row['type'] = '';

if($_POST['duplicate'] && $rright['ticket_template'])
{
	//get data from source ticket
	$qry=$db->prepare("SELECT * FROM `tincidents` WHERE id=:id");
	$qry->execute(array('id' => $_POST['template']));
	$source_ticket=$qry->fetch();
	$qry->closeCursor();
	
	//auto technician affection with category
	if($rparameters['ticket_cat_auto_attribute'])
	{
		$auto_tech_attribute=0;
		if($source_ticket['subcat'])
		{
			//check if association between technician and category exist
			$qry=$db->prepare("SELECT `id`,`technician`,`technician_group` FROM `tsubcat` WHERE (technician!=0 OR technician_group!=0) AND id=:id");
			$qry->execute(array('id' => $source_ticket['subcat']));
			$auto_tech=$qry->fetch();
			$qry->closeCursor();
			if($auto_tech)
			{
				if($auto_tech['technician']){$source_ticket['technician']=$auto_tech['technician']; $source_ticket['t_group']=0; $auto_tech_attribute=1;}
				elseif($auto_tech['technician_group']){$source_ticket['t_group']=$auto_tech['technician_group']; $source_ticket['technician']=0; $auto_tech_attribute=1;}
			}
		}
		if($source_ticket['category'] && !$auto_tech_attribute)
		{
			//check if association between technician and category exist
			$qry=$db->prepare("SELECT `id`,`technician`,`technician_group` FROM `tcategory` WHERE (technician!=0 OR technician_group!=0) AND id=:id");
			$qry->execute(array('id' => $source_ticket['category']));
			$auto_tech=$qry->fetch();
			$qry->closeCursor();
			if($auto_tech)
			{
				if($auto_tech['technician']){$source_ticket['technician']=$auto_tech['technician']; $source_ticket['t_group']=0;}
				elseif($auto_tech['technician_group']){$source_ticket['t_group']=$auto_tech['technician_group']; $source_ticket['technician']=0;}
			}
		}
	}
	
	if($_SESSION['profile_id']==2 || $_SESSION['profile_id']==1) //case for powerusers or users	 
	{
		$qry=$db->prepare("
		INSERT INTO `tincidents` 
		(
		`user`,
		`u_service`,
		`u_group`,
		`title`,
		`description`,
		`priority`,
		`state`,
		`time`,
		`category`,
		`subcat`,
		`date_create`,
		`technician`,
		`t_group`,
		`criticality`,
		`creator`,
		`place`,
		`type_answer`,
		`type`
		) VALUES (
		:user,
		:u_service,
		:u_group,
		:title,
		:description,
		:priority,
		:state,
		:time,
		:category,
		:subcat,
		:date_create,
		:technician,
		:t_group,
		:criticality,
		:creator,
		:place,
		:type_answer,
		:type
		)
		");
		$qry->execute(array(
			'user' => $_SESSION['user_id'],
			'u_service' => $source_ticket['u_service'],
			'u_group' => $source_ticket['u_group'],
			'title' => $source_ticket['title'],
			'description' => $source_ticket['description'],
			'priority' => $source_ticket['priority'],
			'state' => $source_ticket['state'],
			'time' => $source_ticket['time'],
			'category' => $source_ticket['category'],
			'subcat' => $source_ticket['subcat'],
			'date_create' => $datetime,
			'technician' => $source_ticket['technician'],
			't_group' => $source_ticket['t_group'],
			'criticality' => $source_ticket['criticality'],
			'creator' => $_SESSION['user_id'],
			'place' => $source_ticket['place'],
			'type_answer' => $source_ticket['type_answer'],
			'type' => $source_ticket['type']
			));
	} else { //case for other profile
		$qry=$db->prepare("
		INSERT INTO `tincidents` 
		(
		`user`,
		`u_service`,
		`u_group`,
		`title`,
		`description`,
		`priority`,
		`state`,
		`time`,
		`category`,
		`subcat`,
		`date_create`,
		`technician`,
		`t_group`,
		`criticality`,
		`creator`,
		`place`,
		`type_answer`,
		`type`
		) VALUES (
		:user,
		:u_service,
		:u_group,
		:title,
		:description,
		:priority,
		:state,
		:time,
		:category,
		:subcat,
		:date_create,
		:technician,
		:t_group,
		:criticality,
		:creator,
		:place,
		:type_answer,
		:type
		)
		");
		$qry->execute(array(
			'user' => $source_ticket['user'],
			'u_service' => $source_ticket['u_service'],
			'u_group' => $source_ticket['u_group'],
			'title' => $source_ticket['title'],
			'description' => $source_ticket['description'],
			'priority' => $source_ticket['priority'],
			'state' => $source_ticket['state'],
			'time' => $source_ticket['time'],
			'category' => $source_ticket['category'],
			'subcat' => $source_ticket['subcat'],
			'date_create' => $datetime,
			'technician' => $source_ticket['technician'],
			't_group' => $source_ticket['t_group'],
			'criticality' => $source_ticket['criticality'],
			'creator' => $_SESSION['user_id'],
			'place' => $source_ticket['place'],
			'type_answer' => $source_ticket['type_answer'],
			'type' => $source_ticket['type']
			));
	}
	//threads insert
	$newticketid=$db->lastInsertId(); //get id of created ticket
	//find threads of source ticket
	$qry=$db->prepare("SELECT `text`,`type` FROM `tthreads` WHERE ticket=:ticket");
	$qry->execute(array('ticket' => $source_ticket['id']));
	while($row=$qry->fetch()) 
	{
		//insert new threads
		$qry2=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`author`,`text`,`type`) VALUES (:ticket,:date,:author,:text,:type)");
		$qry2->execute(array('ticket' => $newticketid,'date' => $datetime,'author' => $_SESSION['user_id'],'text' => $row['text'],'type' => $row['type']
	));
	}
	$qry->closeCursor();

	//duplicate attachment
	$qry=$db->prepare("SELECT COUNT(id) FROM `tattachments` WHERE ticket_id=:ticket_id");
	$qry->execute(array('ticket_id' => $source_ticket['id']));
	$attachment=$qry->fetch();
	$qry->closeCursor();
	if($attachment[0]>0)
	{
		$qry=$db->prepare("SELECT `uid`,`ticket_id`,`storage_filename`,`real_filename` FROM `tattachments` WHERE ticket_id=:ticket_id");
		$qry->execute(array('ticket_id' => $source_ticket['id']));
		while($source_attachment=$qry->fetch()) 
		{
			//generate new attachment uid
			$uid=md5(uniqid());
			//copy file
			if(file_exists('upload/ticket/'.$source_attachment['storage_filename']))		
			{
				copy('upload/ticket/'.$source_attachment['storage_filename'], 'upload/ticket/'.$newticketid.'_'.$uid);
			}
			//insert db attachement
			$qry2=$db->prepare("INSERT INTO `tattachments` (`uid`,`ticket_id`,`storage_filename`,`real_filename`) VALUES (:uid,:ticket_id,:storage_filename,:real_filename)");
			$qry2->execute(array('uid' => $uid,'ticket_id' => $newticketid,'storage_filename' => $newticketid.'_'.$uid,'real_filename' =>$source_attachment['real_filename']));
		}
		$qry->closeCursor();
	}


	$message=T_('Le modèle a été appliqué au ticket en cours');
	echo '
	<div role="alert" class="alert alert-lg bgc-success-l3 border-0 border-l-4 brc-success-m1 mt-4 mb-3 pr-3 d-flex">
				<div class="flex-grow-1">
					<i class="fas fa-check mr-1 text-120 text-success-m1"></i>
					<strong class="text-success">'.$message.'</strong>
				</div>
				<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
				</button>
			</div>';

	echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]&state=$_GET[state]'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
	</SCRIPT>";
} elseif($rright['ticket_template']) {

	//form template
	echo '
	<div class="modal fade" id="template" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalLabel"><i class="fa fa-tags text-pink pr-2"></i>'.T_("Modèle de ticket").'</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form name="form7" method="POST" action="" id="form7">
						<input name="duplicate" type="hidden" value="1">
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="add_reminder">'.T_('Liste ').' :</label>
							</div>
							<div class="col-sm-5 ">
								<select class="form-control" id="template" name="template">
								';
								$qry=$db->prepare("SELECT `incident`,`name` FROM `ttemplates` ORDER BY `name` ASC");
								$qry->execute();
								while($row=$qry->fetch()) 
								{
									echo '<option value="'.$row['incident'].'">'.$row['name'].'</option>';
								}
								$qry->closeCursor();
								echo '
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" onclick="$(\'form#form7\').submit();" ><i class="fa fa-check pr-2"></i>'.T_('Appliquer').'</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</div>
		</div>
	</div>
	';
}
?>