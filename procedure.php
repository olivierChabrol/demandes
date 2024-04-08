<?php
################################################################################
# @Name : procedure.php
# @Description : display, edit and add procedure
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 03/09/2013
# @Update : 28/08/2020
# @Version : 3.2.4
################################################################################

//initialize variables 
if(!isset($_POST['addprocedure'])) $_POST['addprocedure'] = '';
if(!isset($_POST['save'])) $_POST['save'] = '';
if(!isset($_POST['modif'])) $_POST['modif'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';
if(!isset($_POST['name'])) $_POST['name'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';

//delete procedure
if($_GET['action']=='delete' && $rright['procedure_delete']!=0)
{
	//disable procedure
	$qry=$db->prepare("UPDATE `tprocedures` SET `disable`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['id']));
	//display delete message
	echo DisplayMessage('success',T_('Procédure supprimée'));
	//redirect
	$www = "./index.php?page=procedure";
	echo "<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='$www'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//if delete file is submit
if($_GET['delete_file'] && $rright['procedure_modify']!=0 && $_GET['id'])
{
	//disable ticket
	if($_GET['id']) {unlink('./upload/procedure/'.$_GET['id'].'/'.$_GET['delete_file'].'');}
	//display delete message
	echo DisplayMessage('success',T_('Fichier supprimé'));
	//redirect
	$www = './index.php?page=procedure&action=edit&id='.$_GET['id'];
	echo "<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='$www'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//if add procedure is submit
if($_GET['action']=='add' && $rright['procedure_add']!=0)
{
	//database modification
	if($_POST['save'])
	{
		//create procedure folder if not exist
		if(!file_exists('./upload/procedure')) {
			mkdir('./upload/procedure', 0777, true);
		}
	
		//secure string
		$_POST['name']=strip_tags($_POST['name']);
		$_POST['category']=strip_tags($_POST['category']);
		$_POST['subcat']=strip_tags($_POST['subcat']);
		$_POST['company']=strip_tags($_POST['company']);
		
		$qry=$db->prepare("INSERT INTO `tprocedures` (`name`,`text`,`category`,`subcat`,`company_id`) VALUES (:name,:text,:category,:subcat,:company_id)");
		$qry->execute(array('name' => $_POST['name'],'text' => $_POST['text'],'category' => $_POST['category'],'subcat' => $_POST['subcat'],'company_id' => $_POST['company']));
			
		$procedure_id=$db->lastInsertId();
		
		//upload file in /upload/procedure directory
		if($_FILES['procedure_file']['name'])
		{
			$filename = $_FILES['procedure_file']['name'];
			//change special character in filename
			$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
			$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
			$file_rename = str_replace($a,$b,$_FILES['procedure_file']['name']);
			if(CheckFileExtension($file_rename)==true) {
				//create procedure directory if not exist
				if(!file_exists('./upload/procedure/'.$procedure_id.'/')) {
					mkdir('./upload/procedure/'.$procedure_id.'', 0777, true);
				}
				$dest_folder = './upload/procedure/'.$procedure_id.'/';
				if(move_uploaded_file($_FILES['procedure_file']['tmp_name'], $dest_folder.$file_rename)) 
				{
				} else {
				echo T_('Erreur de transfert vérifier le chemin').' '.$dest_folder;
				}
			} else {
				echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Fichier interdit').'.<br></div>';
			}
		}
		
		//display action message
		echo DisplayMessage('success',T_('La procédure a été sauvegardée'));
		//redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	////////////////////////////////////////////////////////// START FORM ADD NEW PROCEDURE ///////////////////////////////////////////////////
	echo '
		<div class="card bcard shadow" id="card-1">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fa fa-book text-primary-m2"></i> '.T_("Ajout d'une procédure").'
				</h5>
			</div><!-- /.card-header -->
			<div class="card-body p-0">
				<!-- to have smooth .card toggling, it should have zero padding -->
				<div class="p-3">
					<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" onsubmit="loadVal();" >
					<label for="name">'.T_('Nom').' :</label>
					<input name="name" style="width:auto;" class="form-control form-control-sm d-inline-block" type="text" value="'; echo $_POST['name']; echo '">
					<br />
					<br />';
					if($rright['procedure_company']!=0)
					{
						echo '
						<label for="company">'.T_('Société').' :</label>
						<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="company">
							';
							$qry2=$db->prepare("SELECT `id`,`name` FROM `tcompany` WHERE `disable`='0' ORDER BY name");
							$qry2->execute();
							while($row2=$qry2->fetch()) 
							{
								if($_POST['company'])
								{
									if($row2['id']==$_POST['company']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
								} elseif($row['company_id']==$row2['id']) 
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								} 
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor();
								
							echo '
						</select>
						<br />
						<br />
						';
					}
					echo '
					<label for="category">'.T_('Catégorie').' :</label>
					<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="category" onchange="submit();">
						';
						$qry2=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
						$qry2->execute();
						while($row2=$qry2->fetch()) 
						{
							if($_POST['category'])
							{
								if($row2['id']==$_POST['category']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
							} elseif(isset($row['category'])==$row2['id']) 
							{
								echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
							} 
							echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
						}
						$qry2->closeCursor();
						echo '
					</select>
					<br />
					<br />
					<label for="subcat">'.T_('Sous-catégorie').' :</label>
					<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="subcat">
					';
						if($_POST['category'])
						{
							$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE `cat` LIKE :cat ORDER BY `name` ASC"); 
							$qry2->execute(array('cat' => $_POST['category']));
						} else {
							$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE `cat`='1' ORDER BY `name` ASC");
							$qry2->execute();
						}
						while($row2=$qry2->fetch()) 
						{
							if($_POST['subcat'])
							{
								if($row2['id']==$_POST['subcat'])
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								}
							} elseif(isset($row['subcat'])==$row2['id']) {
								echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							
						}
						$qry2->closeCursor();
						echo '
					</select>
					<br /><br />
					<label for="procedure_file">'.T_('Joindre un fichier').' :</label>
					<input name="procedure_file"  type="file" style="display:inline" />
					<br /><br />
					<table border="1" style="width:auto; border: 1px solid #D8D8D8;" >
						<tr >
							<td>
								<div id="editor" class="bootstrap-wysiwyg-editor px-3 py-2" style="min-height:100px; min-width:330px;"></div>
							</td>
						</tr>
					</table>
					<input type="hidden" name="text" />
					<input type="hidden" name="text2" />
					<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
						<button name="save" value="save" id="save" type="submit" class="btn btn-success mr-2">
							<i class="fa fa-save bigger-110"></i>
							'.T_('Sauvegarder').'
						</button>
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="fa fa-undo bigger-110"></i>
							'.T_('Retour').'
						</button>
					</div>
				</form>



				</div>
			</div><!-- /.card-body -->
		</div>
	';
	////////////////////////////////////////////////////////// END FORM ADD NEW PROCEDURE ///////////////////////////////////////////////////
}
elseif($_GET['action']=='edit')
{
	
	//Database modification
	if($_POST['modif'])
	{
		//create procedure folder if not exist
		if(!file_exists('./upload/procedure')) {
			mkdir('./upload/procedure', 0777, true);
		}
		
		//upload file in /upload/procedure directory
		if($_FILES['procedure_file']['name'])
		{
			$filename = $_FILES['procedure_file']['name'];
			//change special character in filename
			$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
			$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
			$file_rename = str_replace($a,$b,$_FILES['procedure_file']['name']);
			if(CheckFileExtension($file_rename)==true) {
				//create procedure directory if not exist
				if(!file_exists('./upload/procedure/'.$_GET['id'].'/')) {
					mkdir('./upload/procedure/'.$_GET['id'].'', 0777, true);
				}
				$dest_folder = './upload/procedure/'.$_GET['id'].'/';
				if(move_uploaded_file($_FILES['procedure_file']['tmp_name'], $dest_folder.$file_rename)   ) 
				{
				} else {
				echo T_('Erreur de transfert vérifier le chemin').' '.$dest_folder;
				}
			} else {
				echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Fichier interdit').'.<br></div>';
			}
		}
		
		//secure string
		$_POST['name']=strip_tags($_POST['name']);
		$_POST['category']=strip_tags($_POST['category']);
		$_POST['subcat']=strip_tags($_POST['subcat']);
		$_POST['company']=strip_tags($_POST['company']);
	
		$qry=$db->prepare("UPDATE `tprocedures` SET `name`=:name, `text`=:text, `category`=:category,`subcat`=:subcat,`company_id`=:company_id  WHERE `id`=:id");
		$qry->execute(array('name' => $_POST['name'],'text' => $_POST['text'],'category' => $_POST['category'],'subcat' => $_POST['subcat'],'company_id' => $_POST['company'],'id' => $_GET['id']));
		
		//display action message
		echo DisplayMessage('success',T_('Procédure sauvegardée'));
		//redirect
		$www = "./index.php?page=procedure&id=$_GET[id]&action=edit&";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	if($_POST['return'])
	{
		//redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	//get data of current selected procedure
	$qry=$db->prepare("SELECT * FROM `tprocedures` WHERE id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	
	//detect <br> for wysiwyg transition from 2.9 to 3.0
	$findbr=stripos($row['text'], '<br>');
	if($findbr === false) {$text=nl2br($row['text']);} else {$text=$row['text'];}
	
	////////////////////////////////////////////////////////// START FORM VIEW OR MODIFY EXISTING PROCEDURE ///////////////////////////////////////////////////
	if($row['company_id']==$ruser['company'] || $rright['procedure_list_company_only']==0) //security check before display procedure
	{
		echo '
		
		<div class="card bcard mt-2" id="card-1">
			<div class="card-header">
				<h5 class="card-title">
					<i class="fa fa-book text-primary-m2"></i> '.T_('Procédure').' n°'.$row['id'].' : '.$row['name'].'
				</h5>
				
			</div><!-- /.card-header -->
			<div class="card-body p-0">
				<!-- to have smooth .card toggling, it should have zero padding -->
				<div class="p-3">
					<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" onsubmit="loadVal();" >
						<label for="name">'.T_('Nom de la procédure').' :</label>
						<input name="name" style="width:auto;" class="form-control form-control-sm d-inline-block" type="text" value="'.$row['name'].'" '; if($rright['procedure_modify']==0) {echo 'readonly="readonly"';} echo '>
						<br />
						<br />
						';
						if($rright['procedure_company']!=0)
						{
							echo '
							<label for="company">'.T_('Société').' :</label>
							<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="company" onchange="">
								';
								$qry2=$db->prepare("SELECT `id`,`name` FROM `tcompany` WHERE disable='0' ORDER BY `name`");
								$qry2->execute();
								while($row2=$qry2->fetch()) 
								{
									if($_POST['company']==$row2['id'])
									{
										if($row2['id']==$_POST['company']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
									} elseif($row['company_id']==$row2['id']) 
									{
										echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
									} else {
										echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
									}
								}
								$qry2->closeCursor();
								echo '
							</select>
							<br />
							<br />
							';
						}
						echo '
						<label for="category">'.T_('Catégorie').' :</label>
						<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="category" onchange="submit();" '; if($rright['procedure_modify']==0) {echo 'disabled="disabled"';} echo '>
							';
							$qry2=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
							$qry2->execute();
							while($row2=$qry2->fetch()) 
							{
								if($_POST['category'])
								{
									if($row2['id']==$_POST['category']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
								} elseif($row['category']==$row2['id']) 
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								} 
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor(); 
							echo '
						</select>
						<br />
						<br />
						<label for="subcat">'.T_('Sous-catégorie').' :</label>
						<select style="width:auto;" class="form-control form-control-sm d-inline-block" name="subcat" '; if($rright['procedure_modify']==0) {echo 'disabled="disabled"';} echo '>
						';
							if($_POST['category'])
							{
								$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY `name` ASC");
								$qry2->execute(array('cat' => $_POST['category']));
							} else {
								$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY `name` ASC");
								$qry2->execute(array('cat' => $row['category']));
							}
							while($row2=$qry2->fetch()) 
							{
								if($_POST['subcat'])
								{
									if($row2['id']==$_POST['subcat'])
									{
										echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
									}
								} elseif($row['subcat']==$row2['id']) {
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								}
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor();	
							echo '
						</select>
						<br /><br />
						';
						if($rright['procedure_modify']) {
							echo '
							<label for="procedure_file">'.T_('Joindre un fichier').' :</label>
							<input name="procedure_file"  type="file" style="display:inline" />
							<br /><br />
							';
						}
						
						//listing of attach file
						if(file_exists('./upload/procedure/'.$_GET['id'].'/')) {	
							if($handle = opendir('./upload/procedure/'.$_GET['id'].'/')) {
								while (false !== ($entry = readdir($handle))) {
									if($entry != "." && $entry != "..") {
										echo '
										<i class="fa fa-paperclip text-primary-m2"></i> 
										<a target="_blank" title="'.T_('Télécharger le fichier').' '.$entry.'" href="./upload/procedure/'.$_GET['id'].'/'.$entry.'">'.$entry.'</a>
										';
										if($rright['procedure_modify']!=0) {echo '<a href="./index.php?page=procedure&id='.$_GET['id'].'&action=edit&delete_file='.$entry.'" title="'.T_('Supprimer').'"<i class="fa fa-trash text-danger"></i></a>';}
										echo '
										<br />
										';
									}
								}
								closedir($handle);
							}
						}
						echo '<br />';
						if(!$rright['procedure_modify']) 
						{echo '<label for="procedure">'.T_('Procédure').' :</label><br /><br />'.$text;} 
						else
						{
							echo '
							<table border="1" style="border: 1px solid #D8D8D8;" >
								<tr>
									<td>
										<div id="editor" class="bootstrap-wysiwyg-editor px-3 py-2" style="min-height:100px;">'.$text.'</div>
									</td>
								</tr>
							</table>
							';
						}
						echo '
						<input type="hidden" name="text" />
						<input type="hidden" name="text2" />
						<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
							';
							if($rright['procedure_modify']) {
								echo '
								<button name="modif" value="modif" id="modif" type="submit" class="btn btn-success mr-2">
									<i class="fa fa-save bigger-110"></i>
									'.T_('Sauvegarder').'
								</button>
								';
							}
							echo '
							<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
								<i class="fa fa-undo bigger-110"></i>
								'.T_('Retour').'
							</button>
						</div>
					</form>
				</div>
			</div><!-- /.card-body -->
		</div>
		';
	} else {
		//display right error
		echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i> '.T_('Erreur').':</strong> '.T_("Vous n'avez pas les droits d'accès à cette procédure. Contacter votre administrateur.").'<br></div>';
	}
	////////////////////////////////////////////////////////// END FORM MODIFY EXISTING PROCEDURE ///////////////////////////////////////////////////
} else {
	//////////////////////////////////////////////////////////////// START PROCEDURE LIST ///////////////////////////////////////////////////////////
	
	if(!$procedurekeywords) {$procedurekeywords='%';} else {$procedurekeywords='%'.$procedurekeywords.'%';}
	if($rright['procedure_list_company_only'])
	{
		//get name of company of current user
		$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE id=:id AND disable='0'");
		$qry->execute(array('id' => $ruser['company']));
		$company=$qry->fetch();
		$qry->closeCursor();
		$company=T_(' de la société ').$company['name'];
		
		//count procedure
		$qry=$db->prepare("SELECT COUNT(*) FROM `tprocedures` WHERE `company_id`=:company_id AND (`text` LIKE :text OR `name` LIKE :text) AND `disable`=0");
		$qry->execute(array('company_id' => $ruser['company'], 'text' => $procedurekeywords));
		$row=$qry->fetch();
		$qry->closeCursor();
	} else {
		$company='';
		$qry=$db->prepare("SELECT COUNT(*) FROM `tprocedures` WHERE (`text` LIKE :text OR `name` LIKE :text) AND `disable`='0'");
		$qry->execute(array('text' => $procedurekeywords));
		$row=$qry->fetch();
		$qry->closeCursor();
	}
	
	echo '
		<div class="page-header position-relative">
			<h1 class="page-title text-primary-m2">
				<i class="fa fa-book text-primary-m2"></i> 
				'.T_('Liste des procédures').$company.'
				<small class="page-info text-secondary-d2">
					<i class="fa fa-angle-double-right text-80"></i>
					&nbsp;'.T_('Nombre').': '.$row[0].' &nbsp;&nbsp;
				</small>
			</h1>
		</div>
	';
	
	
	//begin table
	echo '
	<div class="mt-4 mt-lg-0 card bcard h-auto overflow-hidden shadow border-0">
		<div class="card-body p-0 table-responsive-xl">
			<table id="sample-table-1" class="table text-dark-m1 brc-black-tp10 mb-1">
				<thead>
					<tr>
						<th><i class="fa fa-circle"></i> '.T_('Numéro').'</th>
						<th><i class="fa fa-square"></i> '.T_('Catégorie').'</th>
						<th><i class="fa fa-sitemap"></i> '.T_('Sous-catégorie').'</th>
						<th><i class="fa fa-tag"></i> '.T_('Nom de la procédure').'</th>
						<th><i class="fa fa-play"></i> '.T_('Actions').'</th>
					</tr>
				</thead>
				<tbody>
					';
						//limit result to procedure of company of current connected user
						if($rright['procedure_list_company_only'])
						{
							$masterquery = $db->prepare("SELECT * FROM `tprocedures` WHERE `company_id`=:company_id AND (`text` LIKE :text OR `name` LIKE :text) AND `disable`='0' ORDER BY `category`,`subcat` ASC");
							$masterquery->execute(array('company_id' => $ruser['company'], 'text' => $procedurekeywords));
						} else {
							$masterquery = $db->prepare("SELECT * FROM `tprocedures` WHERE (`text` LIKE :text OR `name` LIKE :text) AND `disable`='0' ORDER BY `category`,`subcat` ASC");
							$masterquery->execute(array('text' => $procedurekeywords));
						}
						while ($row=$masterquery->fetch())
						{
							//get category name
							$qry=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
							$qry->execute(array('id' => $row['category']));
							$rcat=$qry->fetch();
							$qry->closeCursor();
							
							//get sub-category name
							$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
							$qry->execute(array('id' => $row['subcat']));
							$rscat=$qry->fetch();
							$qry->closeCursor();
							echo '
							<tr class="bgc-h-orange-l4" >	
								<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$row['id'].'</td>
								<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$rcat[0].'</td>
								<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$rscat[0].'</td>
								<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$row['name'].'</td>
								<td>
									<div class="hidden-phone visible-desktop btn-group">	
										';
										//display actions buttons
										if($rright['procedure_modify']) {echo'<a class="btn action-btn btn-xs btn-warning mr-1" href="./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit" title="'.T_('Modifier cette procédure').'" ><center><span style="color:#FFF;#"><i class="fa fa-pencil-alt"></i></span></center></a>';}
										if($rright['procedure_delete']) {echo'<a class="btn action-btn btn-xs btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette procédure ?').'\');" href="./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=delete" title="'.T_('Supprimer cette procédure').'" ><center><i class="fa fa-trash"></i></center></a>';}
										echo '
									</div>
								</td>
							</tr>
							';
						}
						$masterquery->closeCursor();
					echo '
				</tbody>
			</table>
		</div>	
	</div>
	';
	//////////////////////////////////////////////////////////////// END PROCEDURE LIST ///////////////////////////////////////////////////////////
}
?>