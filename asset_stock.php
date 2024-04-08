<?php
################################################################################################
# @Name : asset_stock.php 
# @Description : page to add multiple assets in one time in your stock based on serials numbers
# @Call : /dashboard.php
# @Author : Flox
# @Version : 3.2.4
# @Create : 18/12/2015
# @Update : 18/08/2020
################################################################################################

//initialize variables 
if(!isset($_POST['type'])) $_POST['type']= ''; 
if(!isset($_POST['save'])) $_POST['save']= ''; 
if(!isset($_POST['model'])) $_POST['model']= ''; 
if(!isset($_POST['manufacturer'])) $_POST['manufacturer']= ''; 
if(!isset($globalrow['model'])) $globalrow['model']= ''; 
if(!isset($globalrow['manufacturer'])) $globalrow['manufacturer']= ''; 
if(!isset($globalrow['type'])) $globalrow['type']= ''; 

//insert assets in database
if($rright['asset'] && $_POST['save'])
{
	//check if warranty is present on asset model
	$qry=$db->prepare("SELECT `warranty` FROM `tassets_model` WHERE id=:id");
	$qry->execute(array('id' => $_POST['model']));
	$row_model=$qry->fetch();
	$qry->closeCursor();
	
	if($row_model['warranty']!=0)
	{
		//calculate end warranty date
		$date_stock=date("Y");
		$year_end_warranty=$date_stock+$row_model['warranty'];
		$date_end_warranty=$year_end_warranty.'-'.date("m").'-'.date("d");
	} else {$date_end_warranty='0000-00-00';}
	
	//count serials number of serials text area
	$serials=explode("\r\n", $_POST['serials']);
	$nb=count($serials);
	
	//special case for user who want add multiple asset for company
	if($rright['asset_list_company_only']){$user_id=$_SESSION['user_id'];} else {$user_id=0;}
	
	for ($i=0; $i<$nb; $i++) {
		//find internal number of new asset
		$qry=$db->prepare("SELECT MAX(CONVERT(sn_internal, SIGNED INTEGER)) FROM `tassets`");
		$qry->execute();
		$row_sn_internal=$qry->fetch();
		$qry->closeCursor();
		$row_sn_internal=$row_sn_internal[0]+1;
		
		//get current date
		$date=date('Y-m-d');
		$qry=$db->prepare("
		INSERT INTO `tassets` (`sn_internal`,`sn_manufacturer`,`sn_indent`,`user`,`type`,`manufacturer`,`model`,`state`,`date_stock`,`date_end_warranty`,`disable`) 
		VALUES (:sn_internal,:sn_manufacturer,:sn_indent,:user,:type,:manufacturer,:model,'1',:date_stock,:date_end_warranty,'0')");
		$qry->execute(array(
			'sn_internal' => $row_sn_internal,
			'sn_manufacturer' => $serials[$i],
			'sn_indent' => $_POST['sn_indent'],
			'user' => $user_id,
			'type' => $_POST['type'],
			'manufacturer' => $_POST['manufacturer'],
			'model' => $_POST['model'],
			'date_stock' => $date,
			'date_end_warranty' => $date_end_warranty
			));
	}
}
?>
<div id="">
	<div class="">
		<div class="card bcard shadow" id="card-1" draggable="false">
			<form class="form-horizontal" name="myform" id="myform" enctype="multipart/form-data" method="post" action="" onsubmit="loadVal();" >
				<div class="card-header">
					<h5 class="card-title">
						<i class="fa fa-desktop"></i>
						<?php echo T_('Entrées en stock'); ?>
					</h5>
					
				</div>
				<div class="card-body">
					<div class="p-3">
						<div class="row">
							<div class="col-lg-9">
								<!-- START type model part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="type">
											<?php echo T_('Type'); ?> :
										</label>
									</div>
									<div class="col-sm-9">
										<select class="form-control col-3 d-inline-block" id="type" name="type" onchange="submit();" >
										<?php
											$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY name");
											$qry->execute();
											while($row=$qry->fetch()) 
											{
												if ($_POST['type'])
												{
													if ($_POST['type']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
												}
												else
												{
													if ($globalrow['type']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
												}
											}
											$qry->closeCursor();
											if ($globalrow['type']==0 && $_POST['type']==0) echo "<option value=\"\" selected></option>";
										?>
										</select>
										<select class="form-control col-3 d-inline-block" id="manufacturer" name="manufacturer" onchange="submit();" >
										<?php
											if ($_POST['type'])
											{
												$qry=$db->prepare("SELECT DISTINCT tassets_manufacturer.id, tassets_manufacturer.name FROM `tassets_manufacturer`,tassets_model WHERE tassets_manufacturer.id=tassets_model.manufacturer AND tassets_model.type=:type ORDER BY name ASC");
												$qry->execute(array('type' => $_POST['type']));
												while($row=$qry->fetch()) 
												{
													if ($_POST['manufacturer'])
													{
														if ($_POST['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
													else
													{
														if ($globalrow['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
												}
												$qry->closeCursor();
											}
											else
											{
												$qry=$db->prepare("SELECT id,name FROM `tassets_manufacturer` WHERE id LIKE :manufacturer ORDER BY name ASC");
												$qry->execute(array('manufacturer' => $globalrow['manufacturer']));
												while($row=$qry->fetch()) 
												{
													if ($_POST['manufacturer'])
													{
														if ($_POST['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
													else
													{
														if ($globalrow['manufacturer']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
												}
												$qry->closeCursor();
											}
											if ($globalrow['manufacturer']==0 && $_POST['manufacturer']==0) echo "<option value=\"\" selected></option>";
											?>
										</select>
										<select class="form-control col-3 d-inline-block" id="model" name="model" onchange="submit();" >
										<?php
											if ($_POST['type'])
											{
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` WHERE type LIKE :type ORDER BY name ASC");
												$qry->execute(array('type' => $_POST['type']));
												while($row=$qry->fetch()) 
												{
													if ($_POST['model'])
													{
														if ($_POST['model']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
													else
													{
														if ($globalrow['model']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
												}
												$qry->closeCursor();
											}
											else
											{
												$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` WHERE type LIKE :type ORDER BY name ASC");
												$qry->execute(array('type' => $globalrow['type']));
												while($row=$qry->fetch()) 
												{
													if ($_POST['model'])
													{
														if ($_POST['model']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
													else
													{
														if ($globalrow['model']==$row['id']) echo "<option value=\"$row[id]\" selected>$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
													}
												}
												$qry->closeCursor();
											}
											if($globalrow['model']==0 && $_POST['model']==0) echo "<option value=\"\" selected></option>";
										?>
										</select>
									</div>
								</div>
								<!-- END type model part -->
								
								<!-- START sn_indent part -->
								<div class="form-group row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="sn_indent"><?php echo T_('N° de commande'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<input class="form-control col-3 d-inline-block " name="sn_indent" id="sn_indent" type="text" size="15"  value=""  />
									</div>
								</div>
								<!-- END sn_indent part -->
								
								<!-- START serials part -->
								<div class="form-group  row">
									<div class="col-sm-2 col-form-label text-sm-right pr-0">
										<label class="mb-0" for="serials"><?php echo T_('Numéros de séries'); ?> :</label>
									</div>
									<div class="col-sm-9">
										<textarea class="form-control col-9 d-inline-block " rows="30" cols="50" name="serials" id="serials"  /></textarea>
									</div>
								</div>
								<!-- END serials part -->
								
							</div>
						</div> <!-- div row -->
						<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center">
							<button name="save" id="save" value="save" type="submit" class="btn btn-success">
								<i class="fa fa-plus "></i> 
								&nbsp;<?php echo T_('Ajouter'); ?>
							</button>
							&nbsp;
							<button name="cancel" id="cancel" value="cancel" type="submit" class="btn btn-danger">
								<i class="fa fa-times "></i> 
								&nbsp;<?php echo T_('Annuler'); ?>
							</button>
						</div> <!-- div row -->
					</div> <!-- div widget main -->
				</div> <!-- div widget body -->
			</form>
		</div> <!-- div end sm -->
	</div> <!-- div end x12 -->
</div> <!-- div end row -->