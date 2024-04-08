<?php
################################################################################
# @Name : ./admin/list.php
# @Description : administration of tables
# @Call : /admin.php
# @Parameters :
# @Author : Flox
# @Create : 15/03/2011
# @Update : 08/09/2020
# @Version : 3.2.4 p2
################################################################################

// Load extra parameters for request
require_once('models/tool/parameters.php');
use Models\Tool\Parameters;
$parameters = Parameters::getInstance();

//initialize variables
require('core/init_post.php');
if(!isset($nbchamp)) $nbchamp = '';
if(!isset($champ0)) $champ0 = '';
if(!isset($champ1)) $champ1 = '';
if(!isset($champ2)) $champ2 = '';
if(!isset($champ3)) $champ3 = '';
if(!isset($champ4)) $champ4 = '';
if(!isset($champ5)) $champ5 = '';
if(!isset($champ6)) $champ6 = '';
if(!isset($reqchamp)) $reqchamp = '';
if(!isset($set)) $set = '';
if(!isset($i)) $i = '';
if(!isset($extensionFichier)) $extensionFichier = '';
if(!isset($nomorigine)) $nomorigine = '';
if(!isset($number)) $number = '';
if(!isset($_FILES['file1']['name'])) $_FILES['file1']['name'] = '';

//default table
if($_GET['table']=='') $_GET['table']='tcategory';

//default page
if($_GET['action']=='') $_GET['action']='disp_list';

//escape special char and secure string before database insert
$champ0=strip_tags($db->quote($champ0));
$champ1=strip_tags($db->quote($champ1));
$champ2=strip_tags($db->quote($champ2));
$champ3=strip_tags($db->quote($champ3));
$champ4=strip_tags($db->quote($champ4));
$champ5=strip_tags($db->quote($champ5));
$champ6=strip_tags($db->quote($champ6));
$db_id=strip_tags($db->quote($_GET['id']));
$db_table=strip_tags($db->quote($_GET['table']));
$db_table=str_replace("'","`",$db_table);

//display debug informations
if($rparameters['debug']) {
	echo '<u><b>DEBUG MODE:</b></u><br /> <b>VAR:</b> cnt_service='.$cnt_service;
	if($user_services) {echo ' user_services=';foreach($user_services as $value) {echo $value.' ';}}
}

//retrieve selected table description
$qry = $db->prepare("DESC $db_table");
$qry->execute();
while($row=$qry->fetch()) {
	${'champ' . $nbchamp} =$row[0];
	$nbchamp++;
}
$qry->closeCursor();
$nbchamp1=$nbchamp;
$nbchamp=$nbchamp-1;

if(($rright['admin'] || $rright['admin_groups'] || $rright['admin_lists'] || $rright['admin_lists_category'] || $rright['admin_lists_subcat'] || $rright['admin_lists_criticality'] || $rright['admin_lists_priority']) && $_GET['action']=="delete")
{
	if($_GET['table']=='tcompany' && $_GET['id'])
	{
		//update company on user table before delete company
		$qry=$db->prepare("UPDATE `tusers` SET `company`='0' WHERE `company`=:company");
		$qry->execute(array('company' => $_GET['id']));
	}
	if($_GET['table']=='tservices' && $_GET['id'])
	{
		//delete user association before delete service
		$qry=$db->prepare("DELETE FROM `tusers_services` WHERE `service_id`=:service_id");
		$qry->execute(array('service_id' => $_GET['id']));

		//update on category table
		$qry=$db->prepare("UPDATE `tcategory` SET `service`='0' WHERE `service`=:service");
		$qry->execute(array('service' => $_GET['id']));
	}
	if($_GET['table']=='tagencies' && $_GET['id'])
	{
		//delete user association before delete agency
		$qry=$db->prepare("DELETE FROM `tusers_agencies` WHERE `agency_id`=:agency_id");
		$qry->execute(array('agency_id' => $_GET['id']));
	}
	if($_GET['table']=='tcategory' && $_GET['id'])
	{
		//update subcat association before delete category
		$qry=$db->prepare("UPDATE `tsubcat` SET `cat`='0' WHERE `cat`=:cat");
		$qry->execute(array('cat' => $_GET['id']));

		//update tincidents association before delete category
		$qry=$db->prepare("UPDATE `tincidents` SET `category`='0' WHERE `category`=:category");
		$qry->execute(array('category' => $_GET['id']));
	}
	if($_GET['table']=='tsubcat' && $_GET['id'])
	{
		//update tincidents association before delete subcategory
		$qry=$db->prepare("UPDATE `tincidents` SET `subcat`='0' WHERE `subcat`=:subcat");
		$qry->execute(array('subcat' => $_GET['id']));
	}

	$db->exec("DELETE FROM $db_table WHERE id = $db_id");
	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
if($_GET['action']=="update")
{
	if($_GET['table']=='tcategory') //special case category
	{
		//default qry
		$qry=$db->prepare("UPDATE `tcategory` SET `number`=:number,`name`=:name WHERE `id`=:id");
		$qry->execute(array('number' => $_POST['number'],'name' => $_POST['category'],'id' => $_GET['id']));
		//case tech auto attribute
		if($rparameters['ticket_cat_auto_attribute'])
		{
			if($_POST['technician'] && $_POST['technician_group'])
			{
				//error technician OR technician group
			} else {
				$qry=$db->prepare("UPDATE `tcategory` SET `technician`=:technician,`technician_group`=:technician_group WHERE `id`=:id");
				$qry->execute(array('technician' => $_POST['technician'],'technician_group' => $_POST['technician_group'],'id' => $_GET['id']));
			}
		}
		//case service limit
		if($rparameters['user_limit_service'])
		{
			$qry=$db->prepare("UPDATE `tcategory` SET `service`=:service WHERE `id`=:id");
			$qry->execute(array('service' => $_POST['service'],'id' => $_GET['id']));
		}
	}elseif($_GET['table']=='tsubcat') //special case subcat
	{
		//secure string
		$_POST['subcat']=strip_tags($_POST['subcat']);

		if($_POST['technician'] && $_POST['technician_group']) {$_POST['technician']=0; $_POST['technician_group']=0;}

		$qry=$db->prepare("UPDATE `tsubcat` SET `cat`=:cat, `name`=:name,`technician`=:technician,`technician_group`=:technician_group WHERE id=:id");
		$qry->execute(array('cat' => $_POST['cat'],'name' => $_POST['subcat'],'technician' => $_POST['technician'],'technician_group' => $_POST['technician_group'],'id' => $_GET['id']));
		/*Ajouter par nous*/
		$subcatid=$_GET['id'];
		//on commence par purger tous les liens subcat/user
		$qry=$db->prepare("DELETE FROM `vprojets` WHERE `id_tsubcat`=:subcatid");
		$qry->execute(array('subcatid' => $subcatid));
		//on ajoute la jointure subcat user
		foreach ($_POST['valideur'] as $key => $valideur) {
			$qry=$db->prepare("INSERT INTO `vprojets` (`id_tsubcat`,`id_tuser`) VALUES (:id_tsubcat,:id_tuser)");
			$qry->execute(array('id_tsubcat' => $subcatid,'id_tuser' => $valideur));
		}

		/*Fin ajout par nous*/
	}
	elseif($_GET['table']=='tassets_model') //special case  asset model
	{
		//secure string
		$_POST['model']=strip_tags($_POST['model']);
		$_POST['warranty']=strip_tags($_POST['warranty']);

		///upload file
		if($_FILES['file1']['name'])
		{
			//white list exclusion for extension
			$whitelist =  array('png','jpg','jpeg' ,'gif' ,'bmp','');
			$file_name = basename($_FILES['file1']['name']);
			//secure check for extension
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			if(in_array($ext,$whitelist) ) {
				$repertoireDestination = dirname(__FILE__)."../../images/model/$file_name";
				move_uploaded_file($_FILES['file1']['tmp_name'], $repertoireDestination);

				$qry=$db->prepare("UPDATE `tassets_model` SET `type`=:type, `manufacturer`=:manufacturer, `name`=:name, `image`=:image,`ip`=:ip,`wifi`=:wifi,`warranty`=:warranty WHERE `id`=:id");
				$qry->execute(array(
					'type' => $_POST['type'],
					'manufacturer' => $_POST['manufacturer'],
					'name' => $_POST['model'],
					'image' => $file_name,
					'ip' => $_POST['ip'],
					'wifi' => $_POST['wifi'],
					'warranty' => $_POST['warranty'],
					'id' => $_GET['id']
					));

			} else {echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Type de fichier interdit').'.<br></div>';}
		} else {
			$qry=$db->prepare("UPDATE `tassets_model` SET `type`=:type, `manufacturer`=:manufacturer, `name`=:name, `ip`=:ip,`wifi`=:wifi,`warranty`=:warranty WHERE `id`=:id");
			$qry->execute(array(
				'type' => $_POST['type'],
				'manufacturer' => $_POST['manufacturer'],
				'name' => $_POST['model'],
				'ip' => $_POST['ip'],
				'wifi' => $_POST['wifi'],
				'warranty' => $_POST['warranty'],
				'id' => $_GET['id']
				));
		}
	}
	else
	{
		for ($i=0; $i <= $nbchamp; $i++)
		{
			$reqchamp="${'champ' . $i}";
			if(!isset($_POST[$reqchamp])) $_POST[$reqchamp] = '';
			$_POST[$reqchamp] = strip_tags($db->quote($_POST[$reqchamp]));
			if($i=='1') $set="`$reqchamp`=$_POST[$reqchamp]"; else $set="$set, `$reqchamp`=$_POST[$reqchamp]";
		}
		$db->exec("UPDATE $db_table SET $set WHERE id=$db_id");
	}

	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}

if($_GET['action']=="add")
{
	if($_GET['table']=='tcategory') //special case category
	{
		//secure string
		$_POST['category']=strip_tags($_POST['category']);
		//avoid double attribute problem
		if($_POST['technician'] && $_POST['technician_group']) {$_POST['technician']=0;$_POST['technician_group']=0;}
		$qry=$db->prepare("INSERT INTO `tcategory` (`name`,`service`,`technician`,`technician_group`) VALUES (:name,:service,:technician,:technician_group)");
		$qry->execute(array('name' => $_POST['category'],'service' => $_POST['service'],'technician' => $_POST['technician'],'technician_group' => $_POST['technician_group']));
	}
	elseif($_GET['table']=='tsubcat') //special case subcat
	{
		//secure string
		$_POST['subcat']=strip_tags($_POST['subcat']);
		//avoid double attribute problem
		if($_POST['technician'] && $_POST['technician_group']) {$_POST['technician']=0;$_POST['technician_group']=0;}
		$qry=$db->prepare("INSERT INTO `tsubcat` (`cat`,`name`,`technician`,`technician_group`) VALUES (:cat,:name,:technician,:technician_group)");
		$qry->execute(array('cat' => $_POST['cat'],'name' => $_POST['subcat'],'technician' => $_POST['technician'],'technician_group' => $_POST['technician_group']));
		/*Ajouter par nous*/
		$subcatid=$db->lastInsertId();
		//on ajoute la jointure subcat user
		foreach ($_POST['valideur'] as $key => $valideur) {
			$qry=$db->prepare("INSERT INTO `vprojets` (`id_tsubcat`,`id_tuser`) VALUES (:id_tsubcat,:id_tuser)");
			$qry->execute(array('id_tsubcat' => $subcatid,'id_tuser' => $valideur));
		}
		/*Fin ajout par nous*/
	}
	elseif($_GET['table']=='tassets_model') //special case and asset model
	{
		//secure string
		$_POST['model']=strip_tags($_POST['model']);
		$_POST['warranty']=strip_tags($_POST['warranty']);

		///upload file
		if($_FILES['file1'])
		{
			//white list exclusion for extension
			$whitelist =  array('png','jpg','jpeg' ,'gif' ,'bmp','');
			$file_name = basename($_FILES['file1']['name']);
			//secure check for extension
			$ext = pathinfo($file_name, PATHINFO_EXTENSION);
			if(in_array($ext,$whitelist) ) {
				$repertoireDestination = dirname(__FILE__)."../../images/model/$file_name";
				move_uploaded_file($_FILES['file1']['tmp_name'], $repertoireDestination);
				$qry=$db->prepare("INSERT INTO `tassets_model` (`type`,`manufacturer`,`image`,`name`,`ip`,`wifi`,`warranty`) VALUES (:type,:manufacturer,:image,:name,:ip,:wifi,:warranty)");
				$qry->execute(array(
					'type' => $_POST['type'],
					'manufacturer' => $_POST['manufacturer'],
					'image' => $file_name,
					'name' => $_POST['model'],
					'ip' => $_POST['ip'],
					'wifi' => $_POST['wifi'],
					'warranty' => $_POST['warranty']
					));
			} else {echo '<div class="alert alert-danger"><strong><i class="fa fa-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Type de fichier interdit').'.<br></div>';}
		} else{
			$qry=$db->prepare("INSERT INTO `tassets_model` (`type`,`manufacturer`,`name`,`ip`,`wifi`,`warranty`) VALUES (:type,:manufacturer,:name,:ip,:wifi,:warranty)");
			$qry->execute(array(
				'type' => $_POST['type'],
				'manufacturer' => $_POST['manufacturer'],
				'name' => $_POST['model'],
				'ip' => $_POST['ip'],
				'wifi' => $_POST['wifi'],
				'warranty' => $_POST['warranty']
				));
		}
	}
	else
	{
		//generate sql row name for selected table
		for ($i=1; $i <= $nbchamp; $i++)
		{
			if($i!="1") {$reqchamp="$reqchamp,${'champ' . $i}";} else {$reqchamp="`${'champ' . $i}`";}
		}
		//generate sql value for selected table
		for ($i=1; $i <= $nbchamp; $i++)
		{
			$nomchamp="${'champ' . $i}";
			if(!isset($_POST[$nomchamp])) $_POST[$nomchamp] = '';
			$_POST[$nomchamp] = strip_tags($db->quote($_POST[$nomchamp]));
			if($i!="1") {$reqvalue="$reqvalue,$_POST[$nomchamp]";} else {$reqvalue="$_POST[$nomchamp]";}
		}
		$db->exec("INSERT INTO $db_table ($reqchamp) VALUES ($reqvalue)");
	}

	$www = "./index.php?page=admin&subpage=list&table=$_GET[table]&action=disp_list";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';

}
?>
<div class="page-header position-relative">
	<h1 class="page-title text-primary-m2">
		<i class="fa fa-list text-primary-m2"></i>
		<?php
		echo T_('Administration des listes');
		?>
	</h1>
</div>
<!------------------------------------------------ Display list of tables to edit with right ------------------------------------------------------>
<div class="card bcard bgc-transparent shadow">
	<div class="card-body tabs-left p-0">
	<ul class="nav nav-tabs align-self-start" role="tablist">
			<?php
			if($rright['admin_lists_category'] || $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tcategory" class="nav-link text-left py-3 '; if($_GET['table']=='tcategory') {echo 'active';} echo '">
						'.T_('Catégories').'
					</a>
				</li>
				';
			}
			if($rright['admin_lists_subcat'] || $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tsubcat" class="nav-link text-left py-3 '; if($_GET['table']=='tsubcat') {echo 'active';} echo'">
						'.T_('Données budgétaires').'
					</a>
				</li>
				';
			}
			//for user agency parameter
			if($rparameters['user_agency'] && $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tagencies" class="nav-link text-left py-3 '; if($_GET['table']=='tagencies') echo 'active'; echo '">
						'.T_('Agences').'
					</a>
				</li>
				';
			}
			if($rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tservices" class="nav-link text-left py-3 '; if($_GET['table']=='tservices') {echo 'active';} echo'">
						'.T_('Services').'
					</a>
				</li>
				';
			}
			if($rright['admin_lists_priority'] || $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tpriority" class="nav-link text-left py-3 '; if($_GET['table']=='tpriority') {echo 'active';} echo'">
						'.T_('Priorités').'
					</a>
				</li>
				';
			}
			if($rright['admin_lists_criticality'] || $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tcriticality" class="nav-link text-left py-3 '; if($_GET['table']=='tcriticality') {echo 'active';} echo'">
						'.T_('Criticités').'
					</a>
				</li>
				';
			}
			if(($rparameters['ticket_type'] && $rright['admin_lists_type']) || $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=ttypes" class="nav-link text-left py-3 '; if($_GET['table']=='ttypes') {echo 'active';} echo'">
						'.T_('Types tickets').'
					</a>
				</li>
				';
			}
			//display ticket type answer table if one profil have right
			$qry=$db->prepare("SELECT COUNT(`id`) FROM `trights` WHERE `ticket_type_answer_disp`!=0;");
			$qry->execute();
			$row=$qry->fetch();
			$qry->closeCursor();
			if($row[0])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm">
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=ttypes_answer" class="nav-link text-left py-3 '; if($_GET['table']=='ttypes_answer') {echo 'active';} echo'">
						'.T_('Types réponse tickets').'
					</a>
				</li>
				';
			}
			if($rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tstates" class="nav-link text-left py-3 ';if($_GET['table']=='tstates') {echo 'active';} echo '">
						'.T_('États tickets').'
					</a>
				</li>
				';
			}
			if($rparameters['ticket_places'] && $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tplaces" class="nav-link text-left py-3 '; if($_GET['table']=='tplaces') {echo 'active';} echo '">
						'.T_('Lieux').'
					</a>
				</li>
				';
			}
			if($rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=ttemplates" class="nav-link text-left py-3 '; if($_GET['table']=='ttemplates') {echo 'active';} echo '">
						'.T_('Modèles tickets').'
					</a>
				</li>
				';
			}
			//for advanced user parameter
			if($rparameters['user_advanced'] && $rright['admin'])
			{
				echo '
				<li class="nav-item brc-primary shadow-sm" >
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=tcompany" class="nav-link text-left py-3 '; if($_GET['table']=='tcompany') echo 'active'; echo '">
						'.T_('Sociétés').'
					</a>
				</li>
				';
			}
            if($rright['admin'])
            {
                echo '
				<li class="nav-item brc-primary shadow-sm">
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=ttime" class="nav-link text-left py-3 '; if($_GET['table']=='ttime') {echo 'active';} echo '">
						'.T_('Temps').'
					</a>
				</li>
				';
            }
            if($rright['admin'])
            {
                echo '
				<li class="nav-item brc-primary shadow-sm">
					<a href="./index.php?page=admin&amp;subpage=list&amp;table=dadministrative_vehicle" class="nav-link text-left py-3 '; if($_GET['table']=='dadministrative_vehicle') {echo 'active';} echo '">
						'.T_('Véhicules administratifs').'
					</a>
				</li>
				';
            }

			if($rright['admin'])
			{
				if($rparameters['asset'])
				{
					echo '
					<li class="nav-item brc-primary shadow-sm">
						<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_type" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_type') {echo 'active';} echo'">
							'.T_('Types équipements').'
						</a>
					</li>
					<li class="nav-item brc-primary shadow-sm">
						<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_manufacturer" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_manufacturer') {echo 'active';} echo'">
							'.T_('Fabricants équipements').'
						</a>
					</li>
					<li class="nav-item brc-primary shadow-sm">
						<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_model" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_model') {echo 'active';} echo'">
							'.T_('Modèles équipements').'
						</a>
					</li>
					<li class="nav-item brc-primary shadow-sm">
						<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_state" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_state') {echo 'active';} echo'">
							'.T_('États équipements').'
						</a>
					</li>
					<li class="nav-item brc-primary shadow-sm">
						<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_location" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_location') {echo 'active';} echo'">
							'.T_('Localisations équipements').'
						</a>
					</li>
					';
					if($rparameters['asset_ip']==1)
					{
						echo '
						<li class="nav-item brc-primary shadow-sm">
							<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_iface_role" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_iface_role') {echo 'active';} echo'">
								'.T_('Rôles interfaces équipements').'
							</a>
						</li>
						<li class="nav-item brc-primary shadow-sm">
							<a href="./index.php?page=admin&amp;subpage=list&amp;table=tassets_network" class="nav-link text-left py-3 '; if($_GET['table']=='tassets_network') {echo 'active';} echo'">
								'.T_('Réseaux équipements').'
							</a>
						</li>
						';
					}
				}
			}
			?>
	</ul>
		<!------------------------------------------------ display edit entry page ------------------------------------------------>
		<div class="tab-content" style="background-color:#FFF;">
			<?php
			//Display
			if($_GET['action']=="disp_edit")
			{
				//check right before display list
				if(
					$rright['admin']!='0' ||
					($_GET['table']=='tcategory' && $rright['admin_lists_category']!='0') ||
					($_GET['table']=='tsubcat' && $rright['admin_lists_subcat']!='0') ||
					($_GET['table']=='tcriticality' && $rright['admin_lists_criticality']!='0') ||
					($_GET['table']=='tpriority' && $rright['admin_lists_priority']!='0') ||
					($_GET['table']=='ttypes' && $rright['admin_lists_type']!='0')
				)
				{
					echo '
						<div class="pr-4 pl-4">
							<div class="widget-box">
								<div class="pt-4 pb-2">
									<h5 class="text-primary-m2"><i class="fa fa-pencil-alt"></i> '.T_("Édition d'une entrée").' :</h5>
									<hr class="mb-3 border-dotted">
								</div>
								<div class="widget-body">
									<div class="widget-main no-padding">
										<form method="post" enctype="multipart/form-data" action="./index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=update&id='.$_GET['id'].'" >
										';
											//specific views
											if($_GET['table']=='tcategory')
											{
												//find value
												$qry=$db->prepare("SELECT `name`,`number`,`technician`,`technician_group` FROM `tcategory` WHERE id=:id");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();

												echo'
												<fieldset>
													<label for="number">'.T_('Ordre').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
													<br />
													<br />
													<label for="category">'.T_('Catégorie').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="category" type="text" value="'.$row['name'].'" />
													';
													//service limit case
													if($rparameters['user_limit_service'])
													{
														//find service value
														$qry=$db->prepare("SELECT `service` FROM `tcategory` WHERE id=:id");
														$qry->execute(array('id' => $_GET['id']));
														$row2=$qry->fetch();
														$qry->closeCursor();

														if($cnt_service==1) //not show select field, if there are only one service, send data in background
														{
															echo '<input type="hidden" name="service" value="'.$row2['service'].'" />';
														} else { //display select box for service
															echo '
																<div class="space-10"></div>
																<label for="service">'.T_('Service').'</label>
																<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
																';
																	if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
																		//display only service associated with this user
																		$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																		$qry->execute(array('user_id' => $_SESSION['user_id']));
																	} else {
																		//display all services
																		$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																		$qry->execute();
																	}
																	while ($row3=$qry->fetch())
																	{
																		echo '
																		<option '; if($row2['service']==$row3['id']) {echo 'selected';} echo ' value="'.$row3['id'].'">
																			'.$row3['name'].'
																		</option>';
																	}
																	$qry->closeCursor();
																echo '
																</select>
															';
														}
													}
													//auto tech attribute case
													if($rparameters['ticket_cat_auto_attribute'])
													{
														//display technician list
														echo '
															<div class="space-10"></div>
															<label for="technician">'.T_('Attribution automatique ').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
															';
															$qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
															$qry2->execute();
															while ($row2=$qry2->fetch())
															{
																echo '
																<option '; if($row['technician']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																	'.$row2['firstname'].' '.$row2['lastname'].'
																</option>';
															}
															$qry2->closeCursor();
															echo '
															</select>
															'.T_('ou').'
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
															';
															$qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
															$qry2->execute();
															while ($row2=$qry2->fetch())
															{
																echo '
																<option '; if($row['technician_group']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																	'.$row2['name'].'
																</option>';
															}
															$qry2->closeCursor();
															echo '
															</select>
														';
													}
												echo '</fieldset>';
											}elseif($_GET['table']=='tcriticality')
											{
												//find value
												$qry=$db->prepare("SELECT `number`,`name`,`color` FROM `tcriticality` WHERE id=:id");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();

												echo'
												<fieldset>
													<label for="number">'.T_('Numéro').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
													<br /><br />
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
													<br /><br />
													<label for="color">'.T_('Couleur').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="'.$row['color'].'" />
													<br /><br />
												';

												if($rparameters['user_limit_service'])
												{
													$qry=$db->prepare("SELECT `service` FROM `tcriticality` WHERE id=:id");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();

													if($cnt_service==1) //not show select field, if there are only one service, send data in background
													{
														echo '<input type="hidden" name="service" value="'.$row['service'].'" />';
													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));

															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row2=$qry->fetch())
															{
																echo '
																<option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																	'.$row2['name'].'
																</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}elseif($_GET['table']=='tpriority')
											{
												//find value
												$qry=$db->prepare("SELECT `number`,`name`,`color` FROM `tpriority` WHERE id=:id");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();

												echo'
												<fieldset>
													<label for="number">'.T_('Numéro').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="'.$row['number'].'" />
													<br /><br />
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
													<br /><br />
													<label for="color">'.T_('Couleur').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="'.$row['color'].'" />
													<br /><br />
												';

												if($rparameters['user_limit_service'])
												{
													//find value
													$qry=$db->prepare("SELECT `service` FROM `tpriority` WHERE id=:id");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();

													if($cnt_service==1) //not show select field, if there are only one service, send data in background
													{
														echo '<input type="hidden" name="service" value="'.$row['service'].'" />';
													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));
															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row2=$qry->fetch())
															{
																echo '
																<option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																	'.$row2['name'].'
																</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}elseif($_GET['table']=='ttypes')
											{
												//find value
												$qry=$db->prepare("SELECT `name` FROM `ttypes` WHERE `id`=:id");
												$qry->execute(array('id' => $_GET['id']));
												$row=$qry->fetch();
												$qry->closeCursor();
												echo'
												<fieldset>
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="'.$row['name'].'" />
													<br /><br />
												';

												if($rparameters['user_limit_service'])
												{
													//find value
													$qry=$db->prepare("SELECT `service` FROM `ttypes` WHERE `id`=:id");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();

													if($cnt_service==1) //not show select field, if there are only one service, send data in background
													{
														echo '<input type="hidden" name="service" value="'.$row['service'].'" />';
													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));
															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row2=$qry->fetch())
															{
																echo '
																<option '; if($row['service']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																	'.$row2['name'].'
																</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}elseif($_GET['table']=='tsubcat')
											{
													//find value
													$qry=$db->prepare("SELECT `id`,`name`,`cat`,`technician`,`technician_group` FROM `tsubcat` WHERE `id`=:id");
													$qry->execute(array('id' => $_GET['id']));
													$row=$qry->fetch();
													$qry->closeCursor();

													//find category name
													$qry=$db->prepare("SELECT `id` FROM `tcategory` WHERE `id`=:id");
													$qry->execute(array('id' => $row['cat']));
													$rowcatfind=$qry->fetch();
													$qry->closeCursor();

													echo '
														<fieldset>
															<label for="cat">'.T_('Catégorie').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="cat" id="form-field-select-1">
															';
																if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
																	//display only category associated services of this current user
																	$qry=$db->prepare("SELECT `tcategory`.`id`,`tcategory`.`name` FROM `tcategory` WHERE `tcategory`.`service` IN (SELECT `service_id` FROM `tusers_services` WHERE `user_id`=:user_id) ORDER BY `tcategory`.`name`");
																	$qry->execute(array('user_id' => $_SESSION['user_id']));
																} else {
																	//display all category
																	$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
																	$qry->execute();
																}

																while ($row2=$qry->fetch())
																{
																	echo '
																	<option '; if($rowcatfind['id']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">
																		'.$row2['name'].'
																	</option>';
																}
																$qry->closeCursor();
															echo '
															</select>
															<div class="space-4"></div>
															<label for="subcat">'.T_('Données budgétaires').'</label>
															<input style="width:auto" class="form-control form-control-sm d-inline-block" name="subcat" type="text" value="'.$row['name'].'" />
															';
															//auto tech attribute case
															if($rparameters['ticket_cat_auto_attribute'])
															{
																//display technician list
																echo '
																	<div class="space-4"></div>
																	<label for="technician">'.T_('Attribution automatique ').'</label>
																	<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
																	';
																	$qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
																	$qry2->execute();
																	while ($row2=$qry2->fetch())
																	{
																		echo '<option '; if($row['technician']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">'.$row2['firstname'].' '.$row2['lastname'].'</option>';
																	}
																	$qry2->closeCursor();
																	echo '
																	</select>
																	'.T_('ou').'
																	<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
																	';
																	$qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
																	$qry2->execute();
																	while ($row2=$qry2->fetch())
																	{
																		echo '<option '; if($row['technician_group']==$row2['id']) {echo 'selected';} echo ' value="'.$row2['id'].'">'.$row2['name'].'</option>';
																	}
																	$qry2->closeCursor();
																	echo '
																	</select>
																';
															}

															//!\ Ajouter par nous
															if($parameters->isFixedValidators())
															{
																//on liste les users pouvant etre valideurs.

																echo '<div class="space-4"></div>
																<label for="valideur">Valideurs</label>
																<select style="width:auto" class="form-control form-control-sm d-inline-block select2 chosen-select" multiple="multiple" name="valideur[]" id="form-field-select-2" multiple>
																';
																//display users
																	$qry=$db->prepare("SELECT `tusers`.`id`,`tusers`.`firstname`, `tusers`.`lastname` FROM `tusers` ORDER BY `tusers`.`lastname`");
																	$qry->execute();

																	$qry2=$db->prepare("SELECT `id_tuser` FROM `vprojets` WHERE `id_tsubcat`=:idsubcat");
																	$qry2->execute(array('idsubcat' => $_GET['id']));
																	$users=[];
																	while ($rowu=$qry2->fetch())
																	{
																		$users[]=$rowu['id_tuser'];
																	}

																	while ($row2=$qry->fetch())
																	{
																		echo '
																		<option '; if(in_array($row2['id'],$users)) {echo 'selected';} echo ' value="'.$row2['id'].'">
																			'.$row2['lastname'].' '.$row2['firstname'].'
																		</option>';
																	}
																	$qry->closeCursor();
																echo '
																</select>
																';
															}
															//!\ Fin Ajout
															echo '
														</fieldset>';
											}
											elseif($_GET['table']=='tassets_model')
											{
													//find value
													$qry=$db->prepare("SELECT * FROM `tassets_model` WHERE `id`=:id");
													$qry->execute(array('id' => $_GET['id']));
													$req=$qry->fetch();
													$qry->closeCursor();

													//find type name
													$qry=$db->prepare("SELECT `id` FROM `tassets_type` WHERE `id`=:id");
													$qry->execute(array('id' => $req['type']));
													$row=$qry->fetch();
													$qry->closeCursor();

													//find manufacturer name
													$qry=$db->prepare("SELECT `id` FROM `tassets_manufacturer` WHERE `id`=:id");
													$qry->execute(array('id' => $req['manufacturer']));
													$rowmodelfind=$qry->fetch();
													$qry->closeCursor();

													echo '
														<fieldset>
															<label for="type">'.T_('Type').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="type" id="form-field-select-1">
															';
																$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY `name`");
																$qry->execute();
																while ($rtype=$qry->fetch())
																{
																	echo '
																	<option '; if($row['id']==$rtype['id']) {echo 'selected';} echo ' value="'.$rtype['id'].'">
																		'.$rtype['name'].'
																	</option>';
																}
																$qry->closeCursor();
															echo '
															</select>
															<div class="space-4"></div>
															<label for="manufacturer">'.T_('Fabriquant').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="manufacturer" id="form-field-select-1">
															';
																$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_manufacturer` ORDER BY `name`");
																$qry->execute();
																while ($rman=$qry->fetch())
																{
																	echo '
																	<option '; if($rowmodelfind['id']==$rman['id']) {echo 'selected';} echo ' value="'.$rman['id'].'">
																		'.$rman['name'].'
																	</option>';
																}
																$qry->closeCursor();
															echo '
															</select>
															<div class="space-4"></div>
															<label for="file1">'.T_('Image').': <span style="font-size: x-small;"><i>(250px x 250px max)</i></span></label>
															';
															//display existing image
															if($req['image']) {echo '<br /><img src="./images/model/'.$req['image'].'" /> <br /><br />';}
															echo '
															<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
															<input name="file1" type="file"  />
															<div class="space-4"></div>
															<label for="model">'.T_('Modèle').'</label>
															<input style="width:auto" class="form-control form-control-sm d-inline-block" name="model" type="text" value="'.$req['name'].'" />
															<div class="space-4"></div>
															';
															if($rparameters['asset_ip']==1)
															{
																echo '
																<label for="ip">'.T_('Équipement IP').'&nbsp;</label>
																<input type="radio" class="ace" value="1" name="ip"'; if($req['ip']==1) {echo "checked";} echo ' > <span class="lbl"> '.T_('Oui').' </span>
																<input type="radio" class="ace" value="0" name="ip"'; if($req['ip']==0) {echo "checked";} echo ' > <span class="lbl"> '.T_('Non').' </span>
																<div class="space-4"></div>
																<label for="wifi">'.T_('Équipement Wifi').'&nbsp;</label>
																<input type="radio" class="ace" value="1" name="wifi"'; if($req['wifi']==1) {echo "checked";} echo ' > <span class="lbl"> '.T_('Oui').' </span>
																<input type="radio" class="ace" value="0" name="wifi"'; if($req['wifi']==0) {echo "checked";} echo ' > <span class="lbl"> '.T_('Non').' </span>
																<div class="space-4"></div>
																';
															} else {echo '<input type="hidden" name="ip" value="0" /><input type="hidden" name="wifi" value="0" />';}
															echo '
															<label for="warranty">'.T_("Nombre d'années de garantie").'</label>
															<input style="width:auto" class="form-control form-control-sm d-inline-block" name="warranty" type="text" size="2" value="'.$req['warranty'].'" />
														</fieldset>
													';
											}
											else
											{
												for ($i=1; $i <= $nbchamp; $i++)
												{
													$query2 = $db->query("SELECT `${'champ' . $i}` FROM $db_table WHERE id=$db_id");
													$req = $query2->fetch();
													$query2->closeCursor();

													//translate label name
													$label_name=${'champ' . $i}; //default value
													if(${'champ' . $i}=='id') {$label_name=T_('Identifiant');}
													if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
													if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
													if(${'champ' . $i}=='cat') {$label_name=T_('Catégorie');}
													if(${'champ' . $i}=='disable') {$label_name=T_('Désactivé');}
													if(${'champ' . $i}=='number') {$label_name=T_('Ordre');}
													if(${'champ' . $i}=='color') {$label_name=T_('Couleur');}
													if(${'champ' . $i}=='description') {$label_name=T_('Description');}
													if(${'champ' . $i}=='mail_object') {$label_name=T_('Objet du mail');}
													if(${'champ' . $i}=='display') {$label_name=T_("Couleur d'affichage");}
													if(${'champ' . $i}=='incident') {$label_name=T_("Numéro ticket");}
													if(${'champ' . $i}=='address') {$label_name=T_("Adresse");}
													if(${'champ' . $i}=='zip') {$label_name=T_("Code postal");}
													if(${'champ' . $i}=='city') {$label_name=T_("Ville");}
													if(${'champ' . $i}=='country') {$label_name=T_("Pays");}
													if(${'champ' . $i}=='limit_ticket_number') {$label_name=T_("Nombre de limite de ticket");}
													if(${'champ' . $i}=='limit_ticket_days') {$label_name=T_("Nombre de limite de jours");}
													if(${'champ' . $i}=='limit_ticket_date_start') {$label_name=T_("Date de début de la limite de jours");}
													if(${'champ' . $i}=='limit_hour_number') {$label_name=T_("Nombre de limite d'heures");}
													if(${'champ' . $i}=='limit_hour_days') {$label_name=T_("Nombre de limite de jours");}
													if(${'champ' . $i}=='limit_hour_date_start') {$label_name=T_("Date de début de la limite de jours");}
													if(${'champ' . $i}=='min') {$label_name=T_("Minutes");}
													if(${'champ' . $i}=='virtualization') {$label_name=T_("Virtualisation");}
													if(${'champ' . $i}=='manufacturer') {$label_name=T_("Fabricant");}
													if(${'champ' . $i}=='image') {$label_name=T_("Image");}
													if(${'champ' . $i}=='ip') {$label_name=T_("Équipement IP");}
													if(${'champ' . $i}=='type') {$label_name=T_("Type");}
													if(${'champ' . $i}=='wifi') {$label_name=T_("Équipement WIFI");}
													if(${'champ' . $i}=='warranty') {$label_name=T_("Années de garantie");}
													if(${'champ' . $i}=='order') {$label_name=T_("Ordre");}
													if(${'champ' . $i}=='block_ip_search') {$label_name=T_("Blocage de recherche IP");}
													if(${'champ' . $i}=='mail') {$label_name=T_("Adresse mail");}
													if(${'champ' . $i}=='service') {$label_name=T_("Service");}
													if(${'champ' . $i}=='network') {$label_name=T_("Réseau");}
													if(${'champ' . $i}=='netmask') {$label_name=T_("Masque");}
													if(${'champ' . $i}=='scan') {$label_name=T_("Scan");}
													if(${'champ' . $i}=='meta') {$label_name=T_("État à traiter");}

													//hide specific field
													if((${'champ' . $i}=='limit_ticket_number' || ${'champ' . $i}=='limit_ticket_days' || ${'champ' . $i}=='limit_ticket_date_start') && !$rparameters['company_limit_ticket'] && $_GET['table']=='tcompany')
													{
														//hide company limit ticket if parameter is off
													}elseif((${'champ' . $i}=='limit_hour_number' || ${'champ' . $i}=='limit_hour_days' || ${'champ' . $i}=='limit_hour_date_start') && !$rparameters['company_limit_hour'] && $_GET['table']=='tcompany')
													{
														//hide company limit hour if parameter is off
													} else {
														echo "
														<fieldset>
															<label for=\"${'champ' . $i}\">$label_name</label>
																<input style=\"width:auto\" class=\"form-control form-control-sm d-inline-block\"  name=\"${'champ' . $i}\" type=\"text\" value=\"$req[0]\" />
														</fieldset>
														<div class=\"space-4\"></div>
														";
													}
												}
											}

											//display color informations and information on critical table
											if(($_GET['table']=='tcriticality' || $_GET['table']=='tpriority') && ($_GET['action']=='disp_edit' || $_GET['action']=='disp_add'))
											{
												echo T_('Liste des couleurs par défaut').' : ';
												echo '<b><span style="color:#82af6f">#82af6f</span></b>&nbsp;';
												echo '<b><span style="color:#f8c806">#f8c806</span></b>&nbsp;';
												echo '<b><span style="color:#f89406">#f89406</span></b>&nbsp;';
												echo '<b><span style="color:#d15b47">#d15b47</span></b>&nbsp;';
												echo '<br /><br /><i class="fa fa-question-circle text-primary-m2"></i> '.T_("Le numéro permet de sélectionner l'ordre de trie");
											}
											if(($_GET['table']=='tstates' || $_GET['table']=='tassets_state'))
											{
												echo ''.T_('Liste des styles par défaut').' :<br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-success text-white">badge text-75 border-l-3 brc-black-tp8 bgc-success text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white">badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white">badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-info text-white">badge text-75 border-l-3 brc-black-tp8 bgc-info text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white">badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white">badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white">badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-yellow">badge text-75 border-l-3 brc-black-tp8 bgc-yellow</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white">badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-light">badge text-75 border-l-3 brc-black-tp8 bgc-light</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-default text-white">badge text-75 border-l-3 brc-black-tp8 bgc-default text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white">badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white</span><br />';
												echo '<br />';
												echo '<span class="badge text-75 badge-info arrowed-in arrowed-in-right">badge text-75 badge-info arrowed-in arrowed-in-right</span><br />';
												echo '<span class="badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10">badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10</span><br />';
												echo '<span class="badge badge-warning badge-pill px-25">badge badge-warning badge-pill px-25</span><br />';
												echo '<span class="badge badge-sm badge-light">badge badge-sm badge-light</span><br />';
												echo '<span class="badge badge-sm badge-dark">badge badge-sm badge-dark</span><br />';
											}

											echo '
											<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
												<button type="submit" class="btn btn-success">
													<i class="fa fa-check"></i>
													'.T_('Modifier').'
												</button>
											</div>
										</form>
									</div>
								</div>

							</div>
						</div>
					';
				} else {
					echo DisplayMessage('error',T_("Vous n'avez pas le droit de modifier une entrée sur cette liste, contacter votre administrateur"));
				}
			}
			// ------------------------------------------------ display add entry page ------------------------------------------------
			if($_GET['action']=="disp_add")
			{
				//check right before display list
				if(
					$rright['admin']!='0' ||
					($_GET['table']=='tcategory' && $rright['admin_lists_category']!='0') ||
					($_GET['table']=='tsubcat' && $rright['admin_lists_subcat']!='0') ||
					($_GET['table']=='tcriticality' && $rright['admin_lists_criticality']!='0') ||
					($_GET['table']=='tpriority' && $rright['admin_lists_priority']!='0') ||
					($_GET['table']=='ttypes' && $rright['admin_lists_type']!='0')
				)
				{
					echo '
						<div class="pr-4 pl-4">
							<div class="widget-box">
								<div class="pt-4 pb-2">
									<h5 class="text-primary-m2"><i class="fa fa-plus-circle"></i> '.T_("Ajout d'une entrée").' :</h5>
									<hr class="mb-3 border-dotted">
								</div>
								<div class="widget-body">
									<div class="widget-main no-padding">
										<form method="post" enctype="multipart/form-data" action="./index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=add" >';
											if($_GET['table']=='tcategory') //special case for limit service parameters
											{
												echo'
												<fieldset>
													<label for="category">'.T_('Catégorie').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="category" type="text" value="" />
													';
													if($rparameters['user_limit_service'])
													{
														if($cnt_service==1)
														{
															echo '<input type="hidden" name="service" value="'.$user_services[0].'" />';
														} else {
															echo '
																<div class="space-4"></div>
																<label for="service">'.T_('Service').'</label>
																<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1" >
																';
																	if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
																		//display only service associated with this user
																		$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																		$qry->execute(array('user_id' => $_SESSION['user_id']));
																	} else {
																		//display all services
																		$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `id`!=0,`name`");
																		$qry->execute();
																	}
																	while ($row=$qry->fetch())
																	{
																		echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
																	}
																	$qry->closeCursor();
																echo '
																</select>
															';
														}
													}
													//auto tech attribute case
													if($rparameters['ticket_cat_auto_attribute'])
													{
														//display technician list
														echo '
															<div class="space-4"></div>
															<label for="technician">'.T_('Attribution automatique ').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
																';
																$qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
																$qry2->execute();
																while ($row2=$qry2->fetch())
																{
																	echo '<option value="'.$row2['id'].'">'.$row2['firstname'].' '.$row2['lastname'].'</option>';
																}
																$qry2->closeCursor();
																echo '
															</select>
															'.T_('ou').'
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
																';
																$qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
																$qry2->execute();
																while ($row2=$qry2->fetch())
																{
																	echo '
																	<option value="'.$row2['id'].'">
																		'.$row2['name'].'
																	</option>';
																}
																$qry2->closeCursor();
																echo '
															</select>
														';
													}
												echo '</fieldset>';
											}elseif($_GET['table']=='tsubcat') //special case subcat
											{
												echo '
												<fieldset>
													<label for="cat">'.T_('Catégorie').'</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block" name="cat" id="form-field-select-1">
													';
														if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
															//display only category associated services of this current user
															$qry=$db->prepare("SELECT `tcategory`.`id`,`tcategory`.`name` FROM `tcategory` WHERE `tcategory`.`service` IN (SELECT `service_id` FROM `tusers_services` WHERE user_id=:user_id) ORDER BY `tcategory`.`name`");
															$qry->execute(array('user_id' => $_SESSION['user_id']));
														} else {
															//display all category
															$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
															$qry->execute();
														}
														while ($row=$qry->fetch())
														{
															echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
														}
														echo '
													</select>
													<div class="space-4"></div>
													<label for="subcat">'.T_('Données budgétaires').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="subcat" type="text" value="" />

													';
													//auto tech attribute case
													if($rparameters['ticket_cat_auto_attribute'])
													{
														//display technician list
														echo '
															<div class="space-4"></div>
															<label for="technician">'.T_('Attribution automatique ').'</label>
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician" id="form-field-select-1" >
																';
																$qry2=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (`profile`='0' OR `profile`='4') AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`lastname`");
																$qry2->execute();
																while ($row2=$qry2->fetch())
																{
																	echo '<option value="'.$row2['id'].'">'.$row2['firstname'].' '.$row2['lastname'].'</option>';
																}
																$qry2->closeCursor();
																echo '
															</select>
															'.T_('ou').'
															<select style="width:auto" class="form-control form-control-sm d-inline-block" name="technician_group" id="form-field-select-1" >
																';
																$qry2=$db->prepare("SELECT `id`,`name` FROM `tgroups` WHERE `type`='1' AND `disable`='0' OR `id`='0' ORDER BY `id`!=0,`name`");
																$qry2->execute();
																while ($row2=$qry2->fetch())
																{
																	echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
																}
																$qry2->closeCursor();
																echo '
															</select>
															<div class="space-4"></div>
														';
													}
													//!\ Ajouter par nous
													if($parameters->isFixedValidators())
													{		
													//on liste les users pouvant etre valideurs.
													echo '<div class="space-4"></div>
													<label for="valideur">Valideurs</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block select2" multiple="multiple"  name="valideur[]" id="form-field-select-2">
													';
													 //display users
														$qry=$db->prepare("SELECT `tusers`.`id`,`tusers`.`firstname`, `tusers`.`lastname` FROM `tusers` ORDER BY `tusers`.`lastname`");
														$qry->execute();


														while ($row2=$qry->fetch())
														{
															echo '
															<option  value="'.$row2['id'].'">
																'.$row2['lastname'].' '.$row2['firstname'].'
															</option>';
														}
														$qry->closeCursor();
													echo '
													</select>
													';
													}
													//!\ Fin Ajout
													echo '
												</fieldset>';
											}
											elseif($_GET['table']=='tcriticality') //special case for limit service parameters
											{
												echo'
												<fieldset>
													<label for="number">'.T_('Numéro').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="" />
													<br /><br />
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="" />
													<br /><br />
													<label for="color">'.T_('Couleur').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="" />
													<br /><br />
												';
												if($rparameters['user_limit_service'])
												{
													if($cnt_service==1)
													{
														echo '<input type="hidden" name="service" value="'.$user_services[0].'" />';

													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1">
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));
															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row=$qry->fetch())
															{
																echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}
											elseif($_GET['table']=='tpriority') //special case for limit service parameters
											{
												echo'
												<fieldset>
													<label for="number">'.T_('Numéro').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="number" type="text" value="" />
													<br /><br />
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="" />
													<br /><br />
													<label for="color">'.T_('Couleur').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="color" type="text" value="" />
													<br /><br />
												';
												if($rparameters['user_limit_service'])
												{
													if($cnt_service==1)
													{
														echo '<input type="hidden" name="service" value="'.$user_services[0].'" />';

													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1">
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 && $_SESSION['profile_id']!=4) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));
															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row=$qry->fetch())
															{
																echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}
											elseif($_GET['table']=='ttypes') //special case for limit service parameters
											{
												echo'
												<fieldset>
													<label for="name">'.T_('Nom').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="name" type="text" value="" />
													<br /><br />
												';
												if($rparameters['user_limit_service'])
												{
													if($cnt_service==1)
													{
														echo '<input type="hidden" name="service" value="'.$user_services[0].'" />';

													} else {
														echo '
														<label for="service">'.T_('Service').'</label>
														<select style="width:auto" class="form-control form-control-sm d-inline-block" name="service" id="form-field-select-1">
														';
															if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1) {
																//display only service associated with this user
																$qry=$db->prepare("SELECT `tservices`.`id`,`tservices`.`name` FROM `tservices`,`tusers_services` WHERE `tservices`.`id`=`tusers_services`.`service_id` AND `tusers_services`.`user_id`=:user_id AND `tservices`.`disable`='0' ORDER BY `tservices`.`name`");
																$qry->execute(array('user_id' => $_SESSION['user_id']));
															} else {
																//display all services
																$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE `disable`='0' ORDER BY `name`");
																$qry->execute();
															}
															while ($row=$qry->fetch())
															{
																echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
															}
															$qry->closeCursor();
														echo '
														</select>
														';
													}
												}
												echo '
												<fieldset>
												<div class=\"space-4\"></div>';
											}
											elseif($_GET['table']=='tassets_model') //special case assets_model
											{
												echo '
												<fieldset>
													<label for="type">'.T_('Type').'</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block" name="type" id="form-field-select-1">
													';
														$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY `name`");
														$qry->execute();
														while ($rtype=$qry->fetch())
														{
															echo '<option value="'.$rtype['id'].'">'.$rtype['name'].'</option>';
														}
														$qry->closeCursor();
														echo '
													</select>
													<div class="space-4"></div>
													<label for="manufacturer">'.T_('Fabriquant').'</label>
													<select style="width:auto" class="form-control form-control-sm d-inline-block" name="manufacturer" id="form-field-select-1">
													';
														$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_manufacturer` ORDER BY `name`");
														$qry->execute();
														while ($rman=$qry->fetch())
														{
															echo '<option value="'.$rman['id'].'">'.$rman['name'].'</option>';
														}
														$qry->closeCursor();
														echo '
													</select>
													<div class="space-4"></div>
													<label for="model">'.T_('Modèle').'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="model" type="text" value="" />
													<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
													<div class="space-4"></div>
													<label for="file1">'.T_('Image').' <span style="font-size: x-small;"><i>(250px x 250px max)</i></span></label>
													<input name="file1" type="file" />
													<div class="space-4"></div>
													';
													if($rparameters['asset_ip']==1)
													{
														echo '
														<label for="ip">'.T_('Équipement IP').'&nbsp;</label>
														<input type="radio" class="ace" value="1" name="ip"> <span class="lbl"> '.T_('Oui').' </span>
														&nbsp;
														<input type="radio" class="ace" value="0" name="ip"> <span class="lbl"> '.T_('Non').' </span>
														<div class="space-4"></div>
														<label for="wifi">'.T_('Équipement WIFI').'&nbsp;</label>
														<input type="radio" class="ace" value="1" name="wifi"> <span class="lbl"> '.T_('Oui').' </span>
														&nbsp;
														<input type="radio" class="ace" value="0" name="wifi"> <span class="lbl"> '.T_('Non').' </span>
														<div class="space-4"></div>
														';
													} else {echo '<input type="hidden" name="ip" value="0" /><input type="hidden" name="wifi" value="0" />';}
													echo '
													<label for="warranty">'.T_("Nombre d'années de garantie").'</label>
													<input style="width:auto" class="form-control form-control-sm d-inline-block" name="warranty" type="text" size="2" value="0" />
													<br />
												</fieldset>
												';
											} else
											{
												echo "<fieldset>";
												for ($i=1; $i <= $nbchamp; $i++)
												{
													//translate label name
													$label_name=${'champ' . $i}; //default value
													if(${'champ' . $i}=='id') {$label_name=T_('Identifiant');}
													if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
													if(${'champ' . $i}=='name') {$label_name=T_('Libellé');}
													if(${'champ' . $i}=='cat') {$label_name=T_('Catégorie');}
													if(${'champ' . $i}=='disable') {$label_name=T_('Désactivé');}
													if(${'champ' . $i}=='number') {$label_name=T_('Ordre');}
													if(${'champ' . $i}=='color') {$label_name=T_('Couleur');}
													if(${'champ' . $i}=='description') {$label_name=T_('Description');}
													if(${'champ' . $i}=='mail_object') {$label_name=T_('Objet du mail');}
													if(${'champ' . $i}=='display') {$label_name=T_("Couleur d'affichage");}
													if(${'champ' . $i}=='incident') {$label_name=T_("Numéro ticket");}
													if(${'champ' . $i}=='address') {$label_name=T_("Adresse");}
													if(${'champ' . $i}=='zip') {$label_name=T_("Code postal");}
													if(${'champ' . $i}=='city') {$label_name=T_("Ville");}
													if(${'champ' . $i}=='country') {$label_name=T_("Pays");}
													if(${'champ' . $i}=='limit_ticket_number') {$label_name=T_("Nombre de limite de ticket");}
													if(${'champ' . $i}=='limit_ticket_days') {$label_name=T_("Nombre de limite de jours");}
													if(${'champ' . $i}=='limit_ticket_date_start') {$label_name=T_("Date de début de la limite de jours");}
													if(${'champ' . $i}=='limit_hour_number') {$label_name=T_("Nombre de limite d'heures");}
													if(${'champ' . $i}=='limit_hour_days') {$label_name=T_("Nombre de limite de jours");}
													if(${'champ' . $i}=='limit_hour_date_start') {$label_name=T_("Date de début de la limite de jours");}
													if(${'champ' . $i}=='min') {$label_name=T_("Minutes");}
													if(${'champ' . $i}=='virtualization') {$label_name=T_("Virtualisation");}
													if(${'champ' . $i}=='manufacturer') {$label_name=T_("Fabricant");}
													if(${'champ' . $i}=='image') {$label_name=T_("Image");}
													if(${'champ' . $i}=='ip') {$label_name=T_("Équipement IP");}
													if(${'champ' . $i}=='type') {$label_name=T_("Type");}
													if(${'champ' . $i}=='wifi') {$label_name=T_("Équipement WIFI");}
													if(${'champ' . $i}=='warranty') {$label_name=T_("Années de garantie");}
													if(${'champ' . $i}=='order') {$label_name=T_("Ordre");}
													if(${'champ' . $i}=='block_ip_search') {$label_name=T_("Blocage de recherche IP");}
													if(${'champ' . $i}=='mail') {$label_name=T_("Adresse mail");}
													if(${'champ' . $i}=='service') {$label_name=T_("Service");}
													if(${'champ' . $i}=='network') {$label_name=T_("Réseau");}
													if(${'champ' . $i}=='netmask') {$label_name=T_("Masque");}
													if(${'champ' . $i}=='scan') {$label_name=T_("Scan");}
													if(${'champ' . $i}=='meta') {$label_name=T_("État à traiter");}

													if($_GET['table']=='tcompany' && !$rparameters['company_limit_ticket'] && ($i==8 || $i==9 || $i==10))
													{
													}elseif($_GET['table']=='tcompany' && !$rparameters['company_limit_hour'] && ($i==11 || $i==12 || $i==13))
													{
													} else {
														echo "
														<label for=\"${'champ' . $i}\">$label_name</label>
														<input style=\"width:auto\" class=\"form-control form-control-sm d-inline-block\" name=\"${'champ' . $i}\" type=\"text\" value=\"\" />
														<div class=\"space-4\"></div>
														";
													}


												}
												echo '</fieldset>';
											}
											//display color informations and information on critical table
											if(($_GET['table']=='tcriticality' || $_GET['table']=='tpriority') && ($_GET['action']=='disp_edit' || $_GET['action']=='disp_add'))
											{
												echo T_('Liste des couleurs par défaut').' : ';
												echo '<b><span style="color:#82af6f">#82af6f</span></b>&nbsp;';
												echo '<b><span style="color:#f8c806">#f8c806</span></b>&nbsp;';
												echo '<b><span style="color:#f89406">#f89406</span></b>&nbsp;';
												echo '<b><span style="color:#d15b47">#d15b47</span></b>&nbsp;';
												echo '<br /><br /><i class="fa fa-question-circle text-primary-m2"></i> '.T_("Le numéro permet de sélectionner l'ordre de trie");
											}
											if(($_GET['table']=='tstates' || $_GET['table']=='tassets_state'))
											{
												echo ''.T_('Liste des styles par défaut').' :<br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-success text-white">badge text-75 border-l-3 brc-black-tp8 bgc-success text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white">badge text-75 border-l-3 brc-black-tp8 bgc-danger text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white">badge text-75 border-l-3 brc-black-tp8 bgc-warning text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-primary text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white">badge text-75 border-l-3 brc-black-tp8 bgc-secondary text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-info text-white">badge text-75 border-l-3 brc-black-tp8 bgc-info text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white">badge text-75 border-l-3 brc-black-tp8 bgc-dark text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white">badge text-75 border-l-3 brc-black-tp8 bgc-pink text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white">badge text-75 border-l-3 brc-black-tp8 bgc-purple text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-yellow">badge text-75 border-l-3 brc-black-tp8 bgc-yellow</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white">badge text-75 border-l-3 brc-black-tp8 bgc-grey text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-light">badge text-75 border-l-3 brc-black-tp8 bgc-light</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-default text-white">badge text-75 border-l-3 brc-black-tp8 bgc-default text-white</span><br />';
												echo '<span class="badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white">badge text-75 border-l-3 brc-black-tp8 bgc-brown text-white</span><br />';
												echo '<br />';
												echo '<span class="badge text-75 badge-info arrowed-in arrowed-in-right">badge text-75 badge-info arrowed-in arrowed-in-right</span><br />';
												echo '<span class="badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10">badge bgc-secondary-l1 text-dark-tp4 border-1 brc-black-tp10</span><br />';
												echo '<span class="badge badge-warning badge-pill px-25">badge badge-warning badge-pill px-25</span><br />';
												echo '<span class="badge badge-sm badge-light">badge badge-sm badge-light</span><br />';
												echo '<span class="badge badge-sm badge-dark">badge badge-sm badge-dark</span><br />';
											}
											echo '
											<div class="border-t-1 brc-secondary-l1 bgc-secondary-l3 py-3 text-center mt-5">
												<button type="submit" class="btn action-btn btn-success">
													<i class="fa fa-check"></i>
													'.T_('Ajouter').'
												</button>
											</div>
										</form>
									</div>
								</div>

							</div>
						</div>
					';
				} else  {
					echo DisplayMessage('error',T_("Vous n'avez pas le droit d'ajouter une entrée sur cette liste, contacter votre administrateur"));
				}
			}
			// ------------------------------------------------ display selected table ------------------------------------------------
			if($_GET['action']=="disp_list")
			{
				//check right before display list
				if(
					($rright['admin']!='0') ||
					(
						($_GET['subpage']=='group' && $rright['admin_groups']!=0) ||
						($_GET['subpage']=='list' && $_GET['table']=='tcategory' && $rright['admin_lists_category']!='0') ||
						($_GET['subpage']=='list' && $_GET['table']=='tsubcat' && $rright['admin_lists_subcat']!='0') ||
						($_GET['subpage']=='list' && $_GET['table']=='tcriticality' && $rright['admin_lists_criticality']!='0') ||
						($_GET['subpage']=='list' && $_GET['table']=='tpriority' && $rright['admin_lists_priority']!='0') ||
						($_GET['subpage']=='list' && $_GET['table']=='ttypes' && $rright['admin_lists_type']!='0')
					)
				)
				{
					echo '
					<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
						<p>
							<button onclick=\'window.location.href="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;action=disp_add";\' class="btn action-btn btn-success">
								<i class="fa fa-plus"></i> '.T_('Ajouter une entrée').'
							</button>
						</p>
					</div>
					<div class="table-responsive">
						<table id="sample-table-1" class="table table-striped table-bordered table-hover">
							<thead>
								<tr>';
									//build title line
									$query = $db->query("DESC $db_table");
									while ($row=$query->fetch())
									{
										if(($_GET['table']=='tcategory' || $_GET['table']=='tcriticality' || $_GET['table']=='tpriority' || $_GET['table']=='ttypes') && $rparameters['user_limit_service']==0 && $row['Field']=='service') {} else
										{
											if($row['Field']!='ldap_guid')
											{
												//translate column name
												$col_name=$row['Field']; //default value
												if($row['Field']=='id') {$col_name=T_('Identifiant');}
												if($row['Field']=='name') {$col_name=T_('Libellé');}
												if($row['Field']=='cat') {$col_name=T_('Catégorie');}
												if($row['Field']=='disable') {$col_name=T_('Désactivé');}
												if($row['Field']=='number') {$col_name=T_('Ordre');}
												if($row['Field']=='color') {$col_name=T_('Couleur');}
												if($row['Field']=='description') {$col_name=T_('Description');}
												if($row['Field']=='mail_object') {$col_name=T_('Objet du mail');}
												if($row['Field']=='display') {$col_name=T_("Couleur d'affichage");}
												if($row['Field']=='incident') {$col_name=T_("Numéro ticket");}
												if($row['Field']=='address') {$col_name=T_("Adresse");}
												if($row['Field']=='zip') {$col_name=T_("Code postal");}
												if($row['Field']=='city') {$col_name=T_("Ville");}
												if($row['Field']=='country') {$col_name=T_("Pays");}
												if($row['Field']=='limit_ticket_number') {$col_name=T_("Nombre de limite de ticket");}
												if($row['Field']=='limit_ticket_days') {$col_name=T_("Nombre de limite de jours");}
												if($row['Field']=='limit_ticket_date_start') {$col_name=T_("Date de début de la limite de jours");}
												if($row['Field']=='limit_hour_number') {$col_name=T_("Nombre de limite d'heures");}
												if($row['Field']=='limit_hour_days') {$col_name=T_("Nombre de limite de jours");}
												if($row['Field']=='limit_hour_date_start') {$col_name=T_("Date de début de la limite de jours");}
												if($row['Field']=='min') {$col_name=T_("Minutes");}
												if($row['Field']=='virtualization') {$col_name=T_("Virtualisation");}
												if($row['Field']=='manufacturer') {$col_name=T_("Fabricant");}
												if($row['Field']=='image') {$col_name=T_("Image");}
												if($row['Field']=='ip') {$col_name=T_("Équipement IP");}
												if($row['Field']=='type') {$col_name=T_("Type");}
												if($row['Field']=='wifi') {$col_name=T_("Équipement WIFI");}
												if($row['Field']=='warranty') {$col_name=T_("Années de garantie");}
												if($row['Field']=='order') {$col_name=T_("Ordre");}
												if($row['Field']=='block_ip_search') {$col_name=T_("Blocage de recherche IP");}
												if($row['Field']=='mail') {$col_name=T_("Adresse mail");}
												if($row['Field']=='service') {$col_name=T_("Service");}
												if($row['Field']=='network') {$col_name=T_("Réseau");}
												if($row['Field']=='netmask') {$col_name=T_("Masque");}
												if($row['Field']=='scan') {$col_name=T_("Scan");}
												if($row['Field']=='technician') {$col_name=T_("Affectation automatique technicien");}
												if($row['Field']=='technician_group') {$col_name=T_("Affectation automatique groupe de techniciens");}
												if($row['Field']=='meta') {$col_name=T_("État à traiter");}

												if($_GET['table']=='tcompany' && !$rparameters['company_limit_ticket'] && ($row['Field']=='limit_ticket_number' || $row['Field']=='limit_ticket_days' || $row['Field']=='limit_ticket_date_start'))
												{
													//hide col if parameter is off
												}elseif($_GET['table']=='tcompany' && !$rparameters['company_limit_hour'] && ($row['Field']=='limit_hour_number' || $row['Field']=='limit_hour_days' || $row['Field']=='limit_hour_date_start'))
												{
													//hide col if parameter is off
												}elseif(!$rparameters['ticket_cat_auto_attribute'] && ($row['Field']=='technician' || $row['Field']=='technician_group')) {
													//hide col for tech cat auto attribute is disabled
												} else {
													echo '<th>'.$col_name.'</th>';
												}
											}
										}
									}
									$query->closeCursor();
									echo '
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>';

							//define order
							if($_GET['table']=='tassets_model'){$order='ORDER BY tassets_model.type,tassets_model.manufacturer ';}
							elseif($_GET['table']=='tcategory'){$order='ORDER BY number,service,name';}
							elseif($_GET['table']=='tsubcat'){$order='ORDER BY cat,name';}
							elseif($_GET['table']=='tcriticality'){$order='ORDER BY service,number';}
							elseif($_GET['table']=='tstates'){$order='ORDER BY number';}
							elseif($_GET['table']=='tpriority'){$order='ORDER BY number';}
							elseif($_GET['table']=='tassets_state'){$order='ORDER BY `order`';}
							elseif($_GET['table']=='tassets_network'){$order='ORDER BY `network`';}
							elseif($_GET['table']=='ttime'){$order='ORDER BY min';}
							else {$order='ORDER BY name';}


							if($rright['dashboard_service_only']!=0 && $rparameters['user_limit_service']==1 &&  $_SESSION['profile_id']!=4){
								$where_service_list=str_replace('tincidents.u_service','service',$where_service);
								if($_GET['table']=='tsubcat') {
									$query="SELECT tsubcat.id,tsubcat.cat,tsubcat.name FROM `tsubcat`,`tcategory` WHERE tsubcat.cat=tcategory.id $where_service_list ORDER BY tsubcat.name";
								} else {
									$query="SELECT * FROM $db_table WHERE 1=1 $where_service_list $order";
								}
							} else {$query="SELECT * FROM $db_table WHERE id!=0 $order";}

							//build each line
							if($rparameters['debug']) {echo '<b>QUERY:</b> '.$query;}
							$query = $db->query($query);
							while ($row=$query->fetch())
							{

								echo '
								<tr >
								';
								for($i=0; $i < $nbchamp1; ++$i)
								{
									//special case to customize table display, $i var represent column
									if($_GET['table']=='tcompany' && !$rparameters['company_limit_ticket'] && ($i==8 || $i==9 || $i==10))
									{
									}elseif($_GET['table']=='tcompany' && !$rparameters['company_limit_hour'] && ($i==11 || $i==12 || $i==13))
									{
									}elseif($_GET['table']=='tsubcat' && $i==1)
									{
										$qry2 = $db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$rcat=$qry2->fetch();
										$qry2->closeCursor();
										if(empty($rcat['name'])) {$rcat['name']=T_('Inconnue');}
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$rcat['name'].'</td>';
									}elseif($_GET['table']=='tsubcat' && ($i==3 || $i==4) && !$rparameters['ticket_cat_auto_attribute'])
									{
										//hide cols
									}
									elseif($_GET['table']=='tassets_model' && $i==1)
									{
										$qry2 = $db->prepare("SELECT `name` FROM `tassets_type` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$ratype=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$ratype['name'].'</td>';
									}
									elseif($_GET['table']=='tassets_model' && $i==2)
									{
										$qry2 = $db->prepare("SELECT `name` FROM `tassets_manufacturer` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$raman=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$raman['name'].'</td>';
									}elseif((($_GET['table']=='tcategory' && $i==3) || ($_GET['table']=='tcriticality' && $i==4) || ($_GET['table']=='tpriority' && $i==4) || ($_GET['table']=='ttypes' && $i==2)) && $rparameters['user_limit_service']==0)
									{
									}elseif((($_GET['table']=='tcategory' && $i==3) || ($_GET['table']=='tcriticality' && $i==4) || ($_GET['table']=='tpriority' && $i==4) || ($_GET['table']=='ttypes' && $i==2)) && $rparameters['user_limit_service']==1)
									{
										$qry2 = $db->prepare("SELECT `name` FROM `tservices` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$row2['name'].'</td>';
									}elseif($_GET['table']=='tcategory' && ($i==4 || $i==5) && !$rparameters['ticket_cat_auto_attribute'])
									{
										//hide row if disable function
									}elseif(($_GET['table']=='tcategory' && $i==4) || ($_GET['table']=='tsubcat' && $i==3)  && $rparameters['ticket_cat_auto_attribute'])
									{
										$qry2 = $db->prepare("SELECT `firstname`,`lastname` FROM `tusers` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$row2['firstname'].' '.$row2['lastname'].'</td>';
									}elseif(($_GET['table']=='tcategory' && $i==5) || ($_GET['table']=='tsubcat' && $i==4) && $rparameters['ticket_cat_auto_attribute'])
									{
										$qry2 = $db->prepare("SELECT `name` FROM `tgroups` WHERE id=:id");
										$qry2->execute(array('id' => $row[$i]));
										$row2=$qry2->fetch();
										$qry2->closeCursor();
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >'.$row2['name'].'</td>';
									}elseif(($_GET['table']=='tagencies' && $i==3) || ($_GET['table']=='tservices' && $i==2)) //hide ldap_guid
									{
									}else{
										echo '<td onclick=\'window.location.href="index.php?page=admin&subpage=list&table='.$_GET['table'].'&action=disp_edit&id='.$row['id'].'";\' >';
										if($row[$i]!='') {echo T_($row[$i]);} else {echo $row[$i];}
										echo '</td>';
									}
								}
								echo '
									<td width="104px">';
										/*Ajouter par nous */
										if((($_GET['table']=='tcategory' && $row['id']!=1 && $row['id']!=2)) || ($_GET['table']!='tcategory'))
										{/*Fin ajout*/
											echo '<a class="btn action-btn btn-sm btn-warning" href="index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;action=disp_edit&amp;id='.$row['id'].'"  title="'.T_('Éditer cette ligne').'" ><i style="color:#FFF;"  class="fa fa-pencil-alt"></i></a>&nbsp;';
											if(($_GET['table']!='tstates' || $row['id']>6) && $row['id']!=0 && ($_GET['table']!='tassets_iface_role' || $row['id']>2) && ($_GET['table']!='tassets_state' || $row['id']>4))
											{
												echo '<a class="btn action-btn btn-sm btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette ligne ?').'\');" href="./index.php?page=admin&amp;subpage=list&amp;table='.$_GET['table'].'&amp;id='.$row['id'].'&amp;action=delete"  title="'.T_('Supprimer cette ligne').'" ><i class="fa fa-trash"></i></a>&nbsp;';
											}
										/*Ajouter par nous*/
										}
										else{
											echo "<span style='color:#888;font-size:0.9em;'>Obligatoire</span>";
										}
										echo "
									</td>
								</tr>";
							}
							$query->closeCursor();
							echo '
							</tbody>
						</table>
					</div>
					';
				} else {
					echo DisplayMessage('error',T_("Vous n'avez pas accès à cette liste, contacter votre administrateur"));
				}
			}
			?>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
        var formDisabled = $('#form-disabled').val();

        // init select2
        $('.select2').select2();

		});
</script>
