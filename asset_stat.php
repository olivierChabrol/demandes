<?php
################################################################################
# @Name : asset_stat.php
# @Description : Display Assets Statistics
# @Call : /stat.php
# @Parameters : 
# @Author : Flox
# @Create : 25/01/2016
# @Update : 06/02/2020
# @Version : 3.2.0
################################################################################

//initialize variables 
if(!isset($_POST['model'])) $_POST['model']='';
?>

<form method="post" action="" name="filter" >
	<?php echo T_('Filtre'); ?> :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="tech" onchange="submit()">
		<?php
		$qry=$db->prepare("SELECT `id`,`firstname`,`lastname` FROM `tusers` WHERE (profile=0 OR profile=4) and disable=0 ORDER BY lastname");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			if ($row['id'] == $_POST['tech']) $selected1="selected" ;
			if ($row['id'] == $_POST['tech']) $find="1" ;
			echo "<option value=\"$row[id]\" $selected1>$row[firstname] $row[lastname]</option>"; 
			$selected1="";
		}
		$qry->closeCursor();
		if ($find!="1") {echo '<option value="%" selected >'.T_('Tous les techniciens').'</option>';} else {echo '<option value="%" >'.('Tous les techniciens').'</option>';}											
		?>
	</select>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="service" onchange="submit()">
		<?php
		$qry=$db->prepare("SELECT `id`,`name` FROM `tservices` WHERE disable=0 ORDER BY name");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			if($row['id']==$_POST['service']) {$selected2="selected";}
			echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
			$selected2="";
		}
		$qry->closeCursor();
		if ($_POST['service']=="%") {echo '<option value="%" selected>'.T_('Tous les services').'</option>';} else {echo '<option value="%" >'.T_('Tous les services').'</option>';}											
		?>
	</select>
	<?php
	if($company_filter==1)
	{
		echo '
			<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="company" onchange="submit()">
				';
				$qry=$db->prepare("SELECT `id`,`name` FROM `tcompany` WHERE disable='0' ORDER BY name");
				$qry->execute();
				while($row=$qry->fetch()) 
				{
					if($row['id']==$_POST['company']) {$selected2="selected";}
					echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
					$selected2="";
				}
				if ($_POST['company']=="%") {echo '<option value="%" selected>'.T_('Toutes les sociétés').'</option>';} else {echo '<option value="%" >'.T_('Toutes les sociétés').'</option>';}											
			echo '
			</select>
		';
	}
	?>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="type" onchange="submit()">
	<?php
	$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_type` ORDER BY name");
	$qry->execute();
	while($row=$qry->fetch()) 
	{
		if ($row['id'] == $_POST['type']) $selected2="selected" ;
		if ($row['id'] == $_POST['type']) $find="1";
		echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
		$selected2="";
	}
	$qry->closeCursor();
	echo '<option '; if ($_POST['type']=='%') echo 'selected'; echo' value="%" >'.T_('Tous les types').'</option>';										
	?>
	</select> 
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="model" onchange="submit()">
	<?php
	$qry=$db->prepare("SELECT `id`,`name` FROM `tassets_model` ORDER BY type");
	$qry->execute();
	while($row=$qry->fetch()) 
	{
		if ($row['id'] == $_POST['model']) $selected2="selected" ;
		if ($row['id'] == $_POST['model']) $find="1";
		echo "<option value=\"$row[id]\" $selected2>$row[name]</option>"; 
		$selected2="";
	}
	$qry->closeCursor();
	echo '<option '; if ($_POST['model']=='%') echo 'selected'; echo' value="%" >'.T_('Tous les modèles').'</option>';										
	?>
	</select> 
	<div class="pt-2"></div>
	<?php echo T_('Période'); ?> :
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="month" onchange="submit()">
		<option value="%" <?php if ($_POST['month'] == '%')echo "selected" ?>><?php echo T_('Tous les mois'); ?></option>
		<option value="01" <?php if ($_POST['month'] == '1')echo "selected" ?>><?php echo T_('Janvier'); ?></option>
		<option value="02" <?php if ($_POST['month'] == '2')echo "selected" ?>><?php echo T_('Février'); ?></option>
		<option value="03" <?php if ($_POST['month'] == '3')echo "selected" ?>><?php echo T_('Mars'); ?></option>
		<option value="04" <?php if ($_POST['month'] == '4')echo "selected" ?>><?php echo T_('Avril'); ?></option>
		<option value="05" <?php if ($_POST['month'] == '5')echo "selected" ?>><?php echo T_('Mai'); ?></option>
		<option value="06" <?php if ($_POST['month'] == '6')echo "selected" ?>><?php echo T_('Juin'); ?></option>
		<option value="07" <?php if ($_POST['month'] == '7')echo "selected" ?>><?php echo T_('Juillet'); ?></option>
		<option value="08" <?php if ($_POST['month'] == '8')echo "selected" ?>><?php echo T_('Août'); ?></option>
		<option value="09" <?php if ($_POST['month'] == '9')echo "selected" ?>><?php echo T_('Septembre'); ?></option>
		<option value="10" <?php if ($_POST['month'] == '10')echo "selected" ?>><?php echo T_('Octobre'); ?></option>
		<option value="11" <?php if ($_POST['month'] == '11')echo "selected" ?>><?php echo T_('Novembre'); ?></option>	
		<option value="12" <?php if ($_POST['month'] == '12')echo "selected" ?>><?php echo T_('Décembre'); ?></option>	
	</select>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="year" onchange="submit()">
		<?php
		$qry=$db->prepare("SELECT distinct year(date_install) as year FROM `tassets` WHERE date_install not like '0000-00-00' ORDER BY year(date_install)");
		$qry->execute(array('id' => $_GET['id']));
		while($row=$qry->fetch()) 
		{
			$selected=0;
			if ($_POST['year']==$row['year']) $selected="selected";  
			echo "<option value=$row[year] $selected>$row[year]</option>";
		}
		$qry->closeCursor();
		?>
		<option value="%" <?php if ($_POST['year'] == '%')echo "selected" ?>><?php echo T_('Toutes les années'); ?></option>
	</select>
</form>
<div class="pt-2"></div>
<?php
	//call all graphics files from ./stats directory
	require('./stats/line_assets.php');
	echo "<br />";
	require('./stats/pie_assets_service.php');
	echo "<br />";
	require('./stats/pie_assets_type.php');
?>