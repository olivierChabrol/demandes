<?php
################################################################################
# @Name : ./plugins/availability/admin/parameters.php
# @Description : admin parameters for availability plugins
# @Call : /admin/parameters.php
# @Author : Flox
# @Create : 28/04/2015
# @Update : 14/04/2020
# @Version : 3.2.1
################################################################################

//initialize variables 
if(!isset($_POST['subcat'])) $_POST['subcat']= ''; 
if(!isset($_POST['depsubcat'])) $_POST['depsubcat']= ''; 

?>
<tr>
	<td style="width: 150px;" class="text-95 text-default-d3 bgc-secondary-l3">
		<i class="fa fa-clock text-blue-m3 pr-1"></i>
		<?php echo T_('Disponibilité'); ?> :
	</td>
	<td class="text-95 text-default-d3">
			<label>
				<input  type="checkbox" <?php if ($rparameters['availability']==1) echo "checked"; ?> name="availability" value="1">
				<span class="lbl">&nbsp;<?php echo T_('Activer la fonction disponibilité'); ?></span>
				<i title="<?php echo T_('Active le suivi des catégories afin de produire des statistiques de disponibilité'); ?>." class="fa fa-question-circle text-primary-m2"></i>
			</label>
			<?php
			    if ($rparameters['availability']==1)
			    {
		    	    echo '
		    	        <div class="space-4"></div>
		    	        &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i> 1 - '.T_('Surveiller toutes les catégories').' :&nbsp;
		    	        <label for="availability_all_cat">
							<input type="radio"  value="1" name="availability_all_cat"'; if ($rparameters['availability_all_cat']==1) {echo "checked";} echo '> <span class="lbl"> '.T_('Oui').' </span>
							<input type="radio"  value="0" name="availability_all_cat"'; if ($rparameters['availability_all_cat']==0) {echo "checked";} echo '  > <span class="lbl"> '.T_('Non').' </span>
						</label>
		    	    ';
		    	    if ($rparameters['availability_all_cat']==0)
		    	    {
		    	        echo '
		    	            <div class="space-4"></div>
		    	            &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="fa fa-caret-right text-primary-m2"></i> '.T_('Sélection des catégories ou sous-catégories à surveiller').' :<br />
		    	            ';
		    	            //display availability list
							$qry=$db->prepare("SELECT * FROM `tavailability`");
							$qry->execute();
							while($row=$qry->fetch()) 
							{
								$qry2=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
								$qry2->execute(array('id' => $row['category']));
								$cname=$qry2->fetch();
								$qry2->closeCursor();
								
								if ($row['subcat']!='')
								{
									$qry2=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
									$qry2->execute(array('id' => $row['subcat']));
									$sname=$qry2->fetch();
									$qry2->closeCursor();
								} else {$sname='';}
								echo '
									&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
									<i class="fa fa-caret-right text-success"></i> ('.$cname['name'].' > '.$sname[0].') 
									<a title="'.T_('Supprimer cette catégorie').'" href="./index.php?page=admin&subpage=parameters&tab=function&deleteavailability='.$row['id'].'"><i class="fa fa-trash text-danger"></i></a>
									<br />
								';
                            }
							$qry->closeCursor();
							//display add category form
							echo'
							&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="fa fa-caret-right text-primary-m2"></i> 
							'.T_('Ajouter la catégorie').' :
							<select name="category" onchange="submit()" style="width:auto" class="form-control form-control-sm d-inline-block" >
								<option value="%"></option>';
								$qry=$db->prepare("SELECT * FROM `tcategory` ORDER BY name");
								$qry->execute();
								while($row=$qry->fetch()) 
								{
									if($_POST['category']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								}
								$qry->closeCursor();
							echo'
							</select>
						    &nbsp;'.T_('et la Sous-Catégorie').' :
							<select name="subcat" onchange="submit()" style="width:auto" class="form-control form-control-sm d-inline-block">
								<option value="%"></option>';
								echo "-$_POST[category]-";
								if($_POST['category']!='%' && $_POST['category']!='')
								{
									$qry=$db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY name");
									$qry->execute(array('cat' => $_POST['category']));
									while($row=$qry->fetch())
									{
										if($_POST['subcat']==$row['id']) {echo "<option selected value=\"$row[id]\">$row[name]</option>";} else {echo "<option value=\"$row[id]\">$row[name]</option>";}
									}
									$qry->closeCursor();
								} else {
									$qry=$db->prepare("SELECT `id`,`name` FROM `tsubcat` ORDER BY name");
									$qry->execute();
									while($row=$qry->fetch())
									{
										if($_POST['subcat']==$row['id']) {echo "<option selected value=\"$row[id]\">$row[name]</option>";} else {echo "<option value=\"$row[id]\">$row[name]</option>";}
									}
									$qry->closeCursor();
								}
								echo '
							</select>
							<div class="space-4"></div>
							';
		    	    }
		    	    echo '
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
		    	    2 - '.T_("Condition de prise en compte d'un ticket").' :&nbsp;
		    	    <select name="availability_condition_type" onchange="submit()" style="width:auto" class="form-control form-control-sm d-inline-block">
		    	        <option value="" '; if($_POST['availability_condition_type']=='' && $rparameters['availability_condition_type']=='') {echo ' selected';} echo ' ></option>
		    	        <option value="types" '; if($_POST['availability_condition_type']=='types' ) {echo ' selected';} echo ' >'.T_('Type').'</option>
		    	        <option value="criticality" '; if($_POST['availability_condition_type']=='criticality') {echo ' selected';} echo '>'.T_('Criticité').'</option>
		    	    </select> 
		    	    ';
			    	if($_POST['availability_condition_type'] || $rparameters['availability_condition_value']!='' )
		    	    {
		    	    	if ($_POST['availability_condition_type']) 
						{
							if ($_POST['availability_condition_type']!='') {$table="$_POST[availability_condition_type]";} else {$table='criticality';}
						} else {
							if ($rparameters['availability_condition_type']!='') {$table="$rparameters[availability_condition_type]";} else {$table='criticality';}
						}
						//check $table value
						if ($table!='criticality' && $table!='types') {$table='criticality';}
						
		    	        $query = $db->query("SELECT * FROM t$table ORDER BY name");
		    	        echo 'est ';
		    	        echo '<select style="width:auto" class="form-control form-control-sm d-inline-block" name="availability_condition_value" >';
							while ($row = $query->fetch())
							{
								echo '<option '; if($rparameters['availability_condition_value']==$row['id']) {echo 'selected';} echo ' value="'.$row['id'].'">'.T_($row['name']).'</option>';
							} 
							$query->closeCursor();
						echo '</select>';
		    	    }
		    	    echo'
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
		    	    3 - '.T_('Dépendances').' :&nbsp;
		    	    <label for="availability_dep">
							<input type="radio"  value="1" name="availability_dep"'; if ($rparameters['availability_dep']==1) {{echo "checked";}} echo '> <span class="lbl"> '.T_('Oui').' </span>
							<input type="radio"  value="0" name="availability_dep"'; if ($rparameters['availability_dep']==0) echo "checked"; echo '  > <span class="lbl"> '.T_('Non').' </span>
					</label>
					<i title="'.T_("Permet de définir des sous-catégories qui seront comptabilisées dans toutes les statistiques si elles possèdent la même condition. (ex: un ticket réseau critique, entraîne une indisponibilité d'une application)").'" class="fa fa-question-sign blue"></i>
					';
				    if ($rparameters['availability_dep']==1)
		    	    {
		    	        echo '
		    	            <div class="space-4"></div>
		    	            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="fa fa-caret-right text-primary-m2"></i> '.T_('Liste des sous-catégories impactant toutes les catégories surveillées').':<br />
		    	            ';
		    	            //display availability dependency list
							$qry=$db->prepare("SELECT * FROM `tavailability_dep`");
							$qry->execute();
							while($row=$qry->fetch()) 
							{
								$qry2=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
								$qry2->execute(array('id' => $row['category']));
								$cname=$qry2->fetch();
								$qry2->closeCursor();
								if ($row['subcat']!='')
								{
									$qry2=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
									$qry2->execute(array('id' => $row['subcat']));
									$sname=$qry2->fetch();
									$qry2->closeCursor();
									
								} else {$sname='';}
								echo '
									&nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
									<i class="fa fa-caret-right text-success"></i> ('.$cname['name'].' > '.$sname[0].') 
									<a title="'.T_('Supprimer cette catégorie').'" href="./index.php?page=admin&subpage=parameters&tab=function&deleteavailabilitydep='.$row['id'].'"><i class="fa fa-trash text-danger"></i></a>
									<br />
								';
                            }
							$qry->closeCursor();
		    	            //display add cat form
							echo'
							&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
		    	            <i class="fa fa-caret-right text-primary-m2"></i> 
							'.T_('Ajouter la catégorie').' :
							<select name="depcategory" onchange="submit()" style="width:auto" class="form-control form-control-sm d-inline-block" >
								<option value="%"></option>';
								$qry=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY name");
								$qry->execute();
								while($row=$qry->fetch()) 
								{
									if ($_POST['depcategory']==$row['id']) echo "<option selected value=\"$row[id]\">$row[name]</option>"; else echo "<option value=\"$row[id]\">$row[name]</option>";
								}
								$qry->closeCursor();
							echo'
							</select>
						    	&nbsp;'.T_('et la Sous-Catégorie').':
							<select name="depsubcat" onchange="submit()" style="width:auto" class="form-control form-control-sm d-inline-block">
								<option value="%"></option>';
								if($_POST['depcategory'])
								{
									$qry=$db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY name");
									$qry->execute(array('cat' => $_POST['depcategory']));
									while($row=$qry->fetch()) 
									{
										if($_POST['depsubcat']==$row['id']){echo "<option selected value=\"$row[id]\">$row[name]</option>";} else {echo "<option value=\"$row[id]\">$row[name]</option>";}
									}
									$qry->closeCursor();
								} else {
									$qry=$db->prepare("SELECT `id`,`name` FROM `tsubcat` ORDER BY name");
									$qry->execute();
									while($row=$qry->fetch()) 
									{
										if($_POST['depsubcat']==$row['id']){echo "<option selected value=\"$row[id]\">$row[name]</option>";} else {echo "<option value=\"$row[id]\">$row[name]</option>";}
									}
									$qry->closeCursor();
								}
								echo '
							</select>
							<div class="space-4"></div>
							';
		    	    }
		    	    //target tx part
		    	   	echo'
		    	    <div class="space-4"></div>
		    	    &nbsp; &nbsp; &nbsp;<i class="fa fa-circle text-success"></i>
		    	    4 - '.T_('Définition des taux de disponibilités cible par années, par sous-catégories').' :&nbsp;
					<i title="'.T_('Permet de fixer des objectifs de disponibilité, pour chaque sous catégorie, qui peuvent fluctuer chaque année').'." class="fa fa-question-sign blue"></i>
				   	<br />
				   ';
					//find tickets year and display subcat 
					$qry=$db->prepare("SELECT DISTINCT YEAR(date_create) FROM `tincidents` ORDER BY YEAR(date_create) DESC");
					$qry->execute();
					while($rowyear=$qry->fetch()) 
					{
						echo ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <i class="fa fa-caret-right text-primary-m2"></i> <b>'.$rowyear[0].'</b> <br />';
						$querysubcat = $db->query("SELECT * FROM `tavailability`");
						
						$qry2=$db->prepare("SELECT `subcat` FROM `tavailability`");
						$qry2->execute();
					    while ($rowsubcat=$qry2->fetch())
					    {
					    	//get subcat name
							$qry3=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
							$qry3->execute(array('id' => $rowsubcat['subcat']));
							$sname=$qry3->fetch();
							$qry3->closeCursor();
							
							//get target tx data from tavailability_target table
							$qry3=$db->prepare("SELECT `target` FROM `tavailability_target` WHERE subcat=:subcat AND year=:year");
							$qry3->execute(array('subcat' => $rowsubcat['subcat'],'year' => $rowyear[0]));
							$targettx=$qry3->fetch();
							$qry3->closeCursor();
							if(empty($targettx['target'])) {$targettx['target']='0';}
							
							echo ' &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; <i class="fa fa-caret-right text-success"></i> <u>'.$sname['name'].'</u> 
							'.T_('taux de disponibilité cible').': 
							<input style="width:auto" class="form-control form-control-sm d-inline-block" type="text" size="4" name="target_'.$rowyear[0].'_'.$rowsubcat['subcat'].'" value="'.$targettx['target'].'" />
							%
							<br />';
					    }
						$qry2->closeCursor();
					} 
					$qry->closeCursor();
			    }
		?>
	</td>
</tr>