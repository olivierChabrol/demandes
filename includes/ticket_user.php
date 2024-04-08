<?php
################################################################################
# @Name : ticket_user.php
# @Description : add and modify user
# @Call : ./core/ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 16/01/2020
# @Update : 25/08/2020
# @Version : 3.2.2 p2
################################################################################

//initialize variables 
if(!isset($_POST['adduser'])) $_POST['adduser'] = ''; 
if(!isset($_POST['add'])) $_POST['add'] = ''; 
if(!isset($_POST['modifyuser'])) $_POST['modifyuser'] = ''; 
if(!isset($_POST['firstname'])) $_POST['firstname'] = ''; 
if(!isset($_POST['lastname'])) $_POST['lastname'] = ''; 
if(!isset($_POST['phone'])) $_POST['phone'] = ''; 
if(!isset($_POST['mobile'])) $_POST['mobile'] = ''; 
if(!isset($_POST['usermail'])) $_POST['usermail'] = ''; 
if(!isset($_POST['company'])) $_POST['company'] = ''; 
if(!isset($_POST['user'])) $_POST['user'] = ''; 

if($_POST['user']) {$selecteduser=$_POST['user'];} else {$selecteduser=$globalrow['user'];} 

//user add form
echo '
<div class="modal fade" id="user_add_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel"><i class="fa fa-user text-info pr-2"></i>'.T_('Ajouter un nouvel utilisateur').'</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form name="form" method="POST" action="" id="user_add_form">
				<div class="modal-body">
					<input name="add" type="hidden" value="1">
					<div class="form-group row p-0 m-0">
						<div class="col-sm-4 col-form-label text-sm-right pt-1">
							<label for="firstname">'.T_('Prénom').' :</label> 
						</div>
						<div class="col-sm-5 ">
							<input class="form-control form-control-sm d-inline-block" name="firstname" id="firstname" type="text" >
						</div>
					</div>
					<div class="form-group row p-0 m-0">
						<div class="col-sm-4 col-form-label text-sm-right pt-1">
							<label for="lastname">'.T_('Nom').' :</label> 
						</div>
						<div class="col-sm-5 ">
							<input class="form-control form-control-sm d-inline-block" name="lastname" type="text" >
						</div>
					</div>
					<div class="form-group row p-0 m-0">
						<div class="col-sm-4 col-form-label text-sm-right pt-1">
							<label for="phone">'.T_('Tél. fixe').' :</label> 
						</div>
						<div class="col-sm-5 ">
							<input class="form-control form-control-sm d-inline-block" name="phone" type="text" >
						</div>
					</div>
					<div class="form-group row p-0 m-0">
						<div class="col-sm-4 col-form-label text-sm-right pt-1">
							<label for="mobile">'.T_('Tél. portable').' :</label> 
						</div>
						<div class="col-sm-5 ">
							<input class="form-control form-control-sm d-inline-block" name="mobile" type="text" >
						</div>
					</div>
					<div class="form-group row p-0 m-0">
						<div class="col-sm-4 col-form-label text-sm-right pt-1">
							<label for="usermail">'.T_('Mail').' :</label> 
						</div>
						<div class="col-sm-5 ">
							<input class="form-control form-control-sm d-inline-block" name="usermail" type="text" >
						</div>
					</div>
					';
					//display advanced user informations
					if($rparameters['user_advanced'])
					{
						echo '
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="company">'.T_('Société').' :</label><br />
							</div>
							<div class="col-sm-5">
								<select class="form-control col-9 d-inline-block" id="company" name="company">';
									$qry=$db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY `name`ASC");
									$qry->execute();
									while($row=$qry->fetch()) {echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
									$qry->closeCursor();
									echo '
								</select>
								<a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"><i class="fa fa-plus-circle text-success text-130 pl-1" title="'.T_('Ajouter une société').'" ></i></a>
							</div>
						</div>
						';
					}
					echo '
					<a target="blank" href="./index.php?page=admin&subpage=user&action=add">'.T_('Plus de champs').'...</a>
				</div>
				<div class="modal-footer">
					<button type="button" id="user_add_button" class="btn btn-success" ><i class="fa fa-check pr-2"></i>'.T_('Ajouter').'</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</form>
		</div>
	</div>
</div>

';

//user edit form
$qry=$db->prepare("SELECT `firstname`,`lastname`,`phone`,`mobile`,`mail`,`company` FROM `tusers` WHERE id=:id");
$qry->execute(array('id' => $selecteduser));
$userform=$qry->fetch();
$qry->closeCursor();

echo '
<div class="modal fade" id="user_modify_modal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalLabel"><i class="fa fa-user text-info pr-2"></i>'.T_('Modifier un utilisateur').'</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form name="form" method="POST" action="" id="user_modify_form">
				<div class="modal-body">
						<input name="modifyuser" type="hidden" value="1">
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="firstname">'.T_('Prénom').' :</label> 
							</div>
							<div class="col-sm-5 ">
								<input class="form-control form-control-sm d-inline-block" name="firstname" type="text" value="'.$userform['firstname'].'" >
							</div>
						</div>
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="lastname">'.T_('Nom').' :</label> 
							</div>
							<div class="col-sm-5 ">
								<input class="form-control form-control-sm d-inline-block" name="lastname" type="text" value="'.$userform['lastname'].'" >
							</div>
						</div>
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="phone">'.T_('Tél. fixe').' :</label> 
							</div>
							<div class="col-sm-5 ">
								<input class="form-control form-control-sm d-inline-block" name="phone" type="text" value="'.$userform['phone'].'" >
							</div>
						</div>
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="mobile">'.T_('Tél. portable').' :</label> 
							</div>
							<div class="col-sm-5 ">
								<input class="form-control form-control-sm d-inline-block" name="mobile" type="text" value="'.$userform['mobile'].'" >
							</div>
						</div>
						<div class="form-group row p-0 m-0">
							<div class="col-sm-4 col-form-label text-sm-right pt-1">
								<label for="usermail">'.T_('Mail').' :</label> 
							</div>
							<div class="col-sm-5 ">
								<input class="form-control form-control-sm d-inline-block" name="usermail" type="text" value="'.$userform['mail'].'" >
							</div>
						</div>
						';
						//display advanced user informations
						if($rparameters['user_advanced'])
						{
							echo '
							<div class="form-group row p-0 m-0">
								<div class="col-sm-4 col-form-label text-sm-right pt-1">
									<label for="company">'.T_('Société').' :</label><br />
								</div>
								<div class="col-sm-5">
									<select class="form-control col-9 d-inline-block" id="company" name="company">';
										$qry=$db->prepare("SELECT `id`,`name` FROM `tcompany` ORDER BY `name`ASC");
										$qry->execute();
										while($row=$qry->fetch()) {
											if($row['id']==$userform['company'])
											{
												echo '<option selected value="'.$row['id'].'">'.T_($row['name']).'</option>';
											} else {
												echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
											}
										}
										$qry->closeCursor();
										echo '
									</select>
									<a target="blank" href="./index.php?page=admin&subpage=list&table=tcompany&action=disp_add"><i class="fa fa-plus-circle text-success text-130 pl-1" title="'.T_('Ajouter une société').'" ></i></a>
								</div>
							</div>
							';
						}
						echo '
						<a target="blank" href="./index.php?page=admin&subpage=user&action=edit&userid='.$selecteduser.'">'.T_('Plus de champs').'...</a>
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-success" id="user_modify_button"  ><i class="fa fa-check pr-2"></i>'.T_('Modifier').'</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times pr-2"></i>'.T_('Annuler').'</button>
				</div>
			</form>
		</div>
	</div>
</div>
';
?>
<script>
	//////////////////////////////////////////////////////////////////////////////// AJAX ADD USER

	//Variable to hold request
	var request;
	// Bind to the submit event of our form
	$("#user_add_button").click(function () {
		//prevent default posting of form - put here to work in case of errors
		event.preventDefault();
		//abort any pending request
		if (request) {
			request.abort();
		}
		//get form
		var $form = $("#user_add_form");
		//select and cache all the fields
		var $inputs = $form.find("input, select, button, textarea");
		//serialize the data in the form
		var serializedData = $form.serialize();
		//fire off the request to db
		request = $.ajax({
			url: "includes/ticket_user_db.php",
			type: "post",
			data: serializedData
		});
		//callback handler that will be called on success
		request.done(function (response, textStatus, jqXHR){
			//log a message to the console
			<?php if($rparameters['debug']) {echo 'console.log(response);';}?>
			var response = JSON.parse(response);
			//modal close
			$("#user_add_modal").modal("hide");
			//update user field
			$('#user').append('<option value="'+response.user_id+'" selected="selected">'+response.lastname+' '+response.firstname+'</option>');
			$('#user').trigger('chosen:updated');
		});
		//callback handler that will be called on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			//log the error to the console
			console.error(
				"The following error occurred: "+
				textStatus, errorThrown
			);
		});
	});

	//////////////////////////////////////////////////////////////////////////////// AJAX UPDATE USER

	//Variable to hold request
	var request;
	// Bind to the submit event of our form
	$("#user_modify_button").click(function () {
		//prevent default posting of form - put here to work in case of errors
		event.preventDefault();
		//abort any pending request
		if (request) {
			request.abort();
		}
		//get form
		var $form = $("#user_modify_form");
		//select and cache all the fields
		var $inputs = $form.find("input, select, button, textarea");
		//serialize the data in the form
		var serializedData = $form.serialize();
		//fire off the request to db
		request = $.ajax({
			url: "includes/ticket_user_db.php?user_id=<?php echo $selecteduser; ?>",
			type: "post",
			data: serializedData
		});
		//callback handler that will be called on success
		request.done(function (response, textStatus, jqXHR){
			//log a message to the console
			<?php if($rparameters['debug']) {echo 'console.log(response);';}?>
			var response = JSON.parse(response);
			//modal close
			$("#user_modify_modal").modal("hide");
			//update user field
			$('#user').append('<option value="'+response.user_id+'" selected="selected">'+response.lastname+' '+response.firstname+'</option>');
			$('#user').trigger('chosen:updated');
		});
		//callback handler that will be called on failure
		request.fail(function (jqXHR, textStatus, errorThrown){
			//log the error to the console
			console.error(
				"The following error occurred: "+
				textStatus, errorThrown
			);
		});
	});
</script>