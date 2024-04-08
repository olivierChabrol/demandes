<?php
################################################################################
# @Name : ticket_stat.php
# @Description : Display Tickets Statistics
# @Call : /stat.php
# @parameters : 
# @Author : Flox
# @Create : 25/01/2016
# @Update : 08/09/2020
# @Version : 3.2.4 p1
################################################################################

if($rparameters['debug']) {echo '<u><b>DEBUG MODE :</b></u><br /><b>VAR:</b> where_service='.$where_service.' | where_agency='.$where_agency.' |  POST_service='.$_POST['service'].' | POST_agency='.$_POST['agency'].' | POST_state='.$_POST['state'].' | cnt_agency='.$cnt_agency.' | cnt_service='.$cnt_service;}
?>

<form method="post" action="" name="filter" >
	<?php echo T_('Filtre'); ?> :
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="tech" onchange="submit()">
		<?php
		if($_POST['tech']=='%') {echo '<option value="%" selected >'.T_('Tous les techniciens').'</option>';} else {echo '<option value="%" >'.T_('Tous les techniciens').'</option>';}											
		//display admin in technician list
		if($rright['ticket_tech_admin'])
		{
			$query = $db->query("SELECT id,firstname,lastname FROM tusers WHERE (profile='0' OR profile='4') AND disable=0 ORDER BY lastname");
		} else {
			$query = $db->query("SELECT id,firstname,lastname FROM tusers WHERE profile='0' AND disable=0 ORDER BY lastname");
		}
		while ($row=$query->fetch()) {
			if($row['id']==$_POST['tech']) {
				echo '<option value="'.$row['id'].'" selected>'.$row['firstname'].' '.$row['lastname'].'</option>'; 
			} else {
				echo '<option value="'.$row['id'].'">'.$row['firstname'].' '.$row['lastname'].'</option>'; 
			}
		}
		$query->closeCursor();
		?>
	</select>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" style="width:160px"name="service" onchange="submit()">
		<?php
		if($_POST['service']=='%') {echo '<option value="%" selected>'.T_('Tous les services').'</option>';} else {echo '<option value="%" >'.T_('Tous les services').'</option>';}											
		//case limit user service
		if($rparameters['user_limit_service'] && !$rright['admin'] && $cnt_agency)
		{
			$qry=$db->prepare("SELECT id,name FROM tservices WHERE id IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) AND disable=0 ORDER BY name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if($row['id']==$_POST['service']) {
					echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
				} else {
					echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
				} 
			}
			$qry->closeCursor();
		} else {
			$qry=$db->prepare("SELECT id,name FROM tservices WHERE disable=0 ORDER BY name");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if($row['id']==$_POST['service']) {$selected2='selected';} else {$selected2='';} 
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>';
			}
			$qry->closeCursor();
		}
		?>
	</select>
	<?php
	if($rparameters['user_agency'])
	{
		echo ' 
		<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="agency" onchange="submit()">';
			if($_POST['agency']=='%') {echo '<option value="%" selected>'.T_('Toutes les agences').'</option>';} else {echo '<option value="%" >'.T_('Toutes les agences').'</option>';}											
			//case limit user agency
			if($rparameters['user_agency'] && !$rright['admin'] && $rright['dashboard_agency_only'])
			{
				$qry=$db->prepare("SELECT id,name FROM tagencies WHERE id IN (SELECT agency_id FROM tusers_agencies WHERE user_id=:user_id) AND disable=0 ORDER BY name");
				$qry->execute(array('user_id' => $_SESSION['user_id']));
				while($row=$qry->fetch()) 
				{
					if($row['id']==$_POST['agency']) {
						echo '<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
					} else {
						echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
					} 
				}
				$qry->closeCursor();
			} else {
				$qry=$db->prepare("SELECT `id`,`name` FROM `tagencies` WHERE disable=0 AND id!=0 ORDER BY name");
				$qry->execute();
				while($row=$qry->fetch()) 
				{
					if($row['id']==$_POST['agency']) {$selected2='selected';} else {$selected2='';}
					echo '<option value="'.$row['id'].'" '.$selected2.'>'.$row['name'].'</option>'; 
				}
				$qry->closeCursor();
			}
			echo'	
		</select>';
	}
	if($rparameters['ticket_type'])
	{
		echo ' 
		<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="type" onchange="submit()">';
			if($_POST['type']=='%') {echo '<option value="%" selected>'.T_('Tous les types').'</option>';} else {echo '<option value="%" >'.T_('Tous les type').'</option>';}											
			$qry=$db->prepare("SELECT `id`,`name` FROM `ttypes` ORDER BY name");
			$qry->execute();
			while($row=$qry->fetch()) 
			{
				if($row['id']==$_POST['type']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.T_($row['name']).'</option>'; 
			}
			$qry->closeCursor();
			echo'	
		</select>';
	}
	?>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="category" onchange="submit()">
	<?php
		if($_POST['category']=='%') {echo '<option value="%" selected>'.T_('Toutes les catégories').'</option>';} else {echo '<option value="%" >'.T_('Toutes les catégories').'</option>';}	
		//case limit user service
		if($rparameters['user_limit_service'] && !$rright['admin'])
		{
			$qry=$db->prepare("SELECT id,name FROM tcategory WHERE service IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) ORDER BY tcategory.name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if($row['id'] == $_POST['category']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.T_($row['name']).'</option>'; 
			}
			$qry->closeCursor();
		} else {
			$qry=$db->prepare("SELECT id,name FROM tcategory ORDER BY tcategory.name");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if($row['id'] == $_POST['category']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.T_($row['name']).'</option>'; 
			}
			$qry->closeCursor();
		}				
		?>
	</select> 
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="criticality" onchange="submit()">
		<?php
		if($_POST['criticality']=='%') {echo '<option value="%" selected>'.T_('Toutes les criticités').'</option>';} else {echo '<option value="%" >'.T_('Toutes les criticités').'</option>';}																					
		if($rparameters['user_limit_service'] && !$rright['admin']) //case limit user service
		{
			$qry=$db->prepare("SELECT id,name FROM tcriticality WHERE service IN (SELECT service_id FROM tusers_services WHERE user_id=:user_id) ORDER BY number");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if($row['id'] == $_POST['criticality']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.T_($row['name']).'</option>'; 
			}
			$qry->closeCursor();
		} else { //case no limit user service
			$qry=$db->prepare("SELECT id,name FROM tcriticality ORDER BY number");
			$qry->execute(array('user_id' => $_SESSION['user_id']));
			while($row=$qry->fetch()) 
			{
				if($row['id'] == $_POST['criticality']) {$selected2='selected';} else {$selected2='';}
				echo '<option value="'.$row['id'].'" '.$selected2.'>'.T_($row['name']).'</option>'; 
			}
			$qry->closeCursor();
		}			
		?>
	</select> 
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="state" onchange="submit()">
		<?php
		if($_POST['state']=='%') {echo '<option value="%" selected>'.T_('Tous les états').'</option>';} else {echo '<option value="%" >'.T_('Tous les états').'</option>';}
		$qry=$db->prepare("SELECT `id`,`name` FROM `tstates` ORDER BY number");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			if($_POST['state']==$row['id']) 
			{
				echo '<option value="'.$row['id'].'" selected >'.T_($row['name']).'</option>';
			} else {
				echo '<option value="'.$row['id'].'">'.T_($row['name']).'</option>';
			}
		}
		$qry->closeCursor();
		if($rparameters['meta_state']) {
			if($_POST['state']=='meta') 
			{
				echo '<option value="meta" selected>'.T_('A traiter').'</option>';
			} else {
				echo '<option value="meta">'.T_('A traiter').'</option>';
			}
		}
		?>
	</select>
	<div class="pt-2"></div>
	<?php echo T_('Période'); ?> :
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="month" onchange="submit()">
		<option value="%" <?php if($_POST['month'] == '%')echo "selected" ?>><?php echo T_('Tous les mois'); ?></option>
		<option value="01"<?php if($_POST['month'] == '1')echo "selected" ?>><?php echo T_('Janvier'); ?></option>
		<option value="02"<?php if($_POST['month'] == '2')echo "selected" ?>><?php echo T_('Février'); ?></option>
		<option value="03"<?php if($_POST['month'] == '3')echo "selected" ?>><?php echo T_('Mars'); ?></option>
		<option value="04"<?php if($_POST['month'] == '4')echo "selected" ?>><?php echo T_('Avril'); ?></option>
		<option value="05"<?php if($_POST['month'] == '5')echo "selected" ?>><?php echo T_('Mai'); ?></option>
		<option value="06"<?php if($_POST['month'] == '6')echo "selected" ?>><?php echo T_('Juin'); ?></option>
		<option value="07"<?php if($_POST['month'] == '7')echo "selected" ?>><?php echo T_('Juillet'); ?></option>
		<option value="08"<?php if($_POST['month'] == '8')echo "selected" ?>><?php echo T_('Aout'); ?></option>
		<option value="09"<?php if($_POST['month'] == '9')echo "selected" ?>><?php echo T_('Septembre'); ?></option>
		<option value="10"<?php if($_POST['month'] == '10')echo "selected" ?>><?php echo T_('Octobre'); ?></option>
		<option value="11"<?php if($_POST['month'] == '11')echo "selected" ?>><?php echo T_('Novembre'); ?></option>	
		<option value="12"<?php if($_POST['month'] == '12')echo "selected" ?>><?php echo T_('Décembre'); ?></option>	
	</select>
	<select style="width:160px;" class="form-control form-control-sm d-inline-block" name="year" onchange="submit()">
		<?php
		echo '<option value="%"'; if($_POST['year'] == '%') {echo 'selected';} echo ' >'.T_('Toutes les années').'</option>';
		
		$qry=$db->prepare("SELECT DISTINCT YEAR(date_create) AS year FROM `tincidents` WHERE date_create not like '0000-00-00 00:00:00' ORDER BY YEAR(date_create)");
		$qry->execute();
		while($row=$qry->fetch()) 
		{
			$selected='';
			if($_POST['year']==$row['year']) {$selected="selected";}
			echo '<option value="'.$row['year'].'" '.$selected.' >'.$row['year'].'</option>';
		}
		$qry->closeCursor();
		?>
	</select>
</form>
<div class="pt-2"></div>
<?php
	//case filter by meta states
	if($rparameters['meta_state'] && $_POST['state']=='meta')
	{
		$where_state='(';
		//generate meta state list 
		$qry=$db->prepare("SELECT `id` FROM `tstates` WHERE meta='1'");
		$qry->execute(array('id' => $_GET['id']));
		while($state=$qry->fetch()) {$where_state.='tincidents.state='.$state['id'].' OR ';}
		$qry->closeCursor();
		$where_state=substr($where_state,0,-4);
		$where_state.=')';
	} else {
		$where_state="tincidents.state LIKE '$_POST[state]'";
	}
	//call all graphics files from ./stats directory
	require('stats/line_tickets.php');
	echo '<br />';
	echo '<a name="chart1"></a>';
	require('stats/pie_tickets_tech.php');
	echo '<br />';
	echo '<a name="chart3"></a>';
	if($rparameters['ticket_type'])
	{
		require('stats/pie_tickets_type.php');
		echo '<br />';
		echo '<a name="chart31"></a>';
	}
	require('./stats/pie_states.php');
	echo '<br />';
	echo '<a name="chart2"></a>';
	require('stats/pie_cat.php');
	echo '<br />';
	//display pie service if exist services
	$qry=$db->prepare("SELECT COUNT(id) AS svc_count FROM `tservices` WHERE id!=0");
	$qry->execute();
	$row=$qry->fetch();
	$qry->closeCursor();
	if(!empty($row['svc_count'])>0)
	{
		echo '<a name="chart7"></a>';
		require('stats/pie_services.php');
		echo '<br />';
	}
	if($company_filter && $rparameters['user_advanced'])
	{
		echo '<a name="chart8"></a>';
		require('stats/pie_company.php');
		echo '<br />';
	}
	echo '<a name="chart6"></a>';
	require('stats/pie_load.php');
	echo '<br />';
	require('stats/histo_load.php');
	require('./stats/tables.php');	
?>