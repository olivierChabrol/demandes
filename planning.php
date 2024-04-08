<?php
################################################################################
# @Name : planning.php
# @Description : display planning
# @Call : /menu.php
# @Parameters : 
# @Author : Flox
# @Create : 28/12/2012
# @Update : 21/11/2017
# @Version : 3.1.28
################################################################################

//initialize variables 
if(!isset($_GET['view'])) $_GET['view'] = '';
if(!isset($mon_color)) $mon_color = '';
if(!isset($tue_color)) $tue_color = '';
if(!isset($wed_color)) $wed_color = '';
if(!isset($thu_color)) $thu_color = '';
if(!isset($fri_color)) $fri_color = '';
if(!isset($sat_color)) $sat_color = '';
if(!isset($sun_color)) $sun_color = '';
if(!isset($cursor)) $cursor = '';
if(!isset($previous)) $previous = '';
if(!isset($next)) $next = '';
if(!isset($_POST['technician'])) $_POST['technician']= $_SESSION['user_id'];

if(!isset($_GET['next'])) $_GET['next'] = 0;
if(!isset($_GET['previous'])) $_GET['previous'] = 0;
if(!isset($_GET['cursor'])) $_GET['cursor'] = 0;
if(!isset($_GET['delete'])) $_GET['delete'] = '';

//default settings
if ($_GET['view']=='') $_GET['view']="week";
if ($next=='') $next=0;
if ($previous=='') $previous=0;

//calculate dates
$cursor=intval($_GET['cursor'])+intval($_GET['next'])-intval($_GET['previous']);
$current = date("Y-m-d H:i");
$week = date("W") + $cursor;
$year = date("Y");

$monday = new DateTime();
$monday->setISODate($year,$week,1); 
$monday=$monday->format('d');
$monday_month = new DateTime();
$monday_month->setISODate($year,$week,1);
$monday_month=$monday_month->format('m')-1;

$tuesday = new DateTime();
$tuesday->setISODate($year,$week,2);
$tuesday=$tuesday->format('d');
$tuesday_month = new DateTime();
$tuesday_month->setISODate($year,$week,2);
$tuesday_month=$tuesday_month->format('m')-1;

$wednesday = new DateTime();
$wednesday->setISODate($year,$week,3);
$wednesday=$wednesday->format('d');
$wednesday_month = new DateTime();
$wednesday_month->setISODate($year,$week,3);
$wednesday_month=$wednesday_month->format('m')-1;

$thursday = new DateTime();
$thursday->setISODate($year,$week,4);
$thursday=$thursday->format('d');
$thursday_month = new DateTime();
$thursday_month->setISODate($year,$week,4);
$thursday_month=$thursday_month->format('m')-1;

$friday = new DateTime();
$friday->setISODate($year,$week,5);
$friday=$friday->format('d');
$friday_month = new DateTime();
$friday_month->setISODate($year,$week,5);
$friday_month=$friday_month->format('m')-1;

$saturday = new DateTime();
$saturday->setISODate($year,$week,6);
$saturday=$saturday->format('d');
$saturday_month = new DateTime();
$saturday_month->setISODate($year,$week,6);
$saturday_month=$saturday_month->format('m')-1;

$sunday = new DateTime();
$sunday->setISODate($year,$week,7);
$sunday=$sunday->format('d');
$sunday_month = new DateTime();
$sunday_month->setISODate($year,$week,7);
$sunday_month=$sunday_month->format('m')-1;

$frday = array (T_('Lundi'), T_('Mardi'), T_('Mercredi'), T_('Jeudi'), T_('Vendredi'), T_('Samedi'),T_('Dimanche'), );
$frmonth = array (T_('Janvier'), T_('Février'), T_('Mars'), T_('Avril'), T_('Mai'), T_('Juin'), T_('Juillet'), T_('Août'), T_('Septembre'), T_('Octobre'), T_('Novembre'), T_('Décembre'));

//delete events
if($_GET['delete']!='')
{
	$db_delete=strip_tags($db->quote($_GET['delete']));
	//disable ticket
	$db->exec("DELETE FROM tevents WHERE incident=$db_delete");
}

//select technician selection
if($_POST['technician']!='%')
{
   //select name of technician
   $querytech= $db->query("SELECT * FROM tusers WHERE id = $_POST[technician]"); 
   $resulttech=$querytech->fetch();
   $displaytech=T_('pour').'  '.$resulttech['firstname'].' '.$resulttech['lastname'];
}
else
{
   $_POST['technician']='%';
   $displaytech=T_('de tous les techniciens');
}

//display head
echo '
<div class="page-header position-relative">
	<h1>
		<i class="icon-calendar"></i>  
';
if ($_GET['view']=='day') echo T_('Planning').' '.$displaytech.' '.T_('du').' '.$frday[date('w')].' '.date("d/m/Y"); 
if ($_GET['view']=='week') echo T_('Planning').' '.$displaytech.' '.T_('depuis').' '.date("d/m/Y", strtotime('First Monday January '.$year.' +'.($week-1).' Week')).' '.T_('au').' '.date("d/m/Y", strtotime('First Monday January '.$year.' +'.$week.' Week -1 day')); 
echo '
	</h1>
</div>';
echo'
	<div class="sidebar-shortcuts-large" id="sidebar-shortcuts-large">
		<button title="'.T_('Semaine précédente').'" onclick=\'window.location.href="./index.php?page=planning&view=week&cursor='.$cursor.'&previous=1";\' class="btn btn-info">
			<i class="icon-arrow-left"></i>
		</button>
		<button title="'.T_('Semaine suivante').'" onclick=\'window.location.href="./index.php?page=planning&view=week&cursor='.$cursor.'&next=1";\' class="btn btn-info">
			<i class="icon-arrow-right"></i>
		</button>
		&nbsp;&nbsp;
		<th colspan="8">'.T_('Semaine').' '.date("W", strtotime('First Monday January '.$year.' +'.($week-1).' Week')).' </th>
	</div>
	<br />
';
?>
<form method="post" action="" name="technician">
	<?php echo T_('Technicien'); ?>:
  <select name="technician" onchange=submit()>
	 <?php
	 $query = $db->query("SELECT * FROM tusers WHERE (profile=0 OR profile=4) and disable=0");            
	 while ($row = $query->fetch()) 
	 {
		if ($row['id'] == $_POST['technician']) 
		{ 
			echo '<option value="'.$row['id'].'" selected>'.$row['firstname'].' '.$row['lastname'].'</option>'; 
 		} else {
			echo '<option value="'.$row['id'].'" >'.$row['firstname'].' '.$row['lastname'].'</option>'; 
		}
	 } 
	 if ($_POST['technician']=='%') {echo '<option value="%" selected >'.T_('Tous').'</option>'; } else {echo '<option value="%">'.T_('Tous').'</option>'; }
	 ?>
  </select> 
</form>
<br />
<?php
////////////////////////////////////////////////////////////WEEK VIEW//////////////////////////////////////////////////////////////////
if ($_GET['view']=='week') 
{
	$period=T_('Semaine').' '.date("W"); 
	$date=date("Y-m-d");
	//find day for display green on current day
	$current_monday = new DateTime();
	$current_monday->setISODate($year,$week,1);
	$current_monday=$current_monday->format('Y-m-d');
	if($date==$current_monday) {$mon_color='bgcolor="#CEF6CE"';}
	$current_tuesday = new DateTime();
	$current_tuesday->setISODate($year,$week,2);
	$current_tuesday=$current_tuesday->format('Y-m-d');
	if($date==$current_tuesday) {$tue_color='bgcolor="#CEF6CE"';}
	$current_wednesday = new DateTime();
	$current_wednesday->setISODate($year,$week,3);
	$current_wednesday=$current_wednesday->format('Y-m-d');
	if($date==$current_wednesday) {$wed_color='bgcolor="#CEF6CE"';}
	$current_thursday = new DateTime();
	$current_thursday->setISODate($year,$week,4);
	$current_thursday=$current_thursday->format('Y-m-d');
	if($date==$current_thursday) {$thu_color='bgcolor="#CEF6CE"';}
	$current_friday = new DateTime();
	$current_friday->setISODate($year,$week,5);
	$current_friday=$current_friday->format('Y-m-d');
	if($date==$current_friday) {$fri_color='bgcolor="#CEF6CE"';}
	$current_saturday = new DateTime();
	$current_saturday->setISODate($year,$week,6);
	$current_saturday=$current_saturday->format('Y-m-d');
	$current_sunday = new DateTime();
	$current_sunday->setISODate($year,$week,7);
	$current_sunday=$current_sunday->format('Y-m-d');
	
	$sat_color='bgcolor="#F2F5A9"';
	$sun_color='bgcolor="#F2F5A9"';
	
	echo"<table class=\"table table-striped table-bordered table-hover\">";
	//Display first Line
	echo '<tr>
			<td></td>
			<td '.$mon_color.' align="center">
				<b>
				'.$frday[0].'
				'.$monday.'
				'.$frmonth[$monday_month].' 
				</b>
			</td>
			<td '.$tue_color.' align="center">
				<b>
				'.$frday[1].'
				'.$tuesday.'
				'.$frmonth[$tuesday_month].' 
				</b>
			</td>
			<td '.$wed_color.' align="center">
				<b>
				'.$frday[2].'
				'.$wednesday.'
				'.$frmonth[$wednesday_month].' 
				</b>
			</td>
			<td '.$thu_color.' align="center">
				<b>
				'.$frday[3].'
				'.$thursday.'
				'.$frmonth[$thursday_month].' 
				</b>
			</td>
			<td '.$fri_color.' align="center">
				<b>
				'.$frday[4].'
				'.$friday.'
				'.$frmonth[$friday_month].' 
				</b>
			</td>
			<td '.$sat_color.' align="center">
				<b>
				'.$frday[5].'
				'.$saturday.'
				'.$frmonth[$saturday_month].' 
				</b>
			</td>
			<td '.$sun_color.' align="center">
				<b>
				'.$frday[6].'
				'.$sunday.'
				'.$frmonth[$sunday_month].' 
				</b>
			</td align="center">
	</tr>';
	//Display each time line
	for ($i = 7; $i <= 19; $i++) 
	{ 
		echo '
		<tr>
			<td><b>'.$i.'h</b></td>
			<td '.$mon_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_monday $i:00' OR tevents.date_end='$current_monday $i:00' OR (tevents.date_start<'$current_monday $i:00' AND tevents.date_end>'$current_monday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor(); 
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor(); 
			echo '
			</td>
			<td '.$tue_color.' >';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_tuesday $i:00' OR tevents.date_end='$current_tuesday $i:00' OR (tevents.date_start<'$current_tuesday $i:00' AND tevents.date_end>'$current_tuesday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor();
					//echo '<span class="label label-sm label-info arrowed-in" title="tickets en attente de prise en charge">#2430</span>&nbsp;';
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor(); 
			echo '
			</td>
			<td '.$wed_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_wednesday $i:00' OR tevents.date_end='$current_wednesday $i:00' OR (tevents.date_start<'$current_wednesday $i:00' AND tevents.date_end>'$current_wednesday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
			echo '
			</td>
			<td '.$thu_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_thursday $i:00' OR tevents.date_end='$current_thursday $i:00' OR (tevents.date_start<'$current_thursday $i:00' AND tevents.date_end>'$current_thursday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor(); 
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor();
			echo '
			</td>
			<td '.$fri_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_friday $i:00' OR tevents.date_end='$current_friday $i:00' OR (tevents.date_start<'$current_friday $i:00' AND tevents.date_end>'$current_friday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor();
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor();
			echo '
			</td>
			<td '.$sat_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_saturday $i:00' OR tevents.date_end='$current_saturday $i:00' OR (tevents.date_start<'$current_saturday $i:00' AND tevents.date_end>'$current_saturday $i:00')) ");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor();
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor();
			echo '
			</td>
			<td '.$sun_color.'>';
				$query = $db->query("SELECT tevents.* FROM tevents,tincidents WHERE tevents.incident=tincidents.id AND tincidents.disable=0 AND tevents.technician LIKE '$_POST[technician]' AND (tevents.date_start='$current_sunday $i:00' OR tevents.date_end='$current_sunday $i:00' OR (tevents.date_start<'$current_sunday $i:00' AND tevents.date_end>'$current_sunday $i:00'))");
				while ($row = $query->fetch())
				{
					if ($row['type']==1) $type='<i class="icon-bell-alt orange"></i>'; else $type='<i class="icon-calendar" blue></i>';
					$queryi= $db->query( "SELECT * FROM `tincidents` WHERE id=$row[incident] AND disable=0");
					$rowi = $queryi->fetch();
					//Select name of technician
					if($_POST['technician']=='%') $querytech= $db->query("SELECT * FROM tusers WHERE id =$rowi[technician]"); else $querytech= $db->query("SELECT * FROM tusers WHERE id =$_POST[technician]"); 
					$resulttech=$querytech->fetch();
					$querytech->closeCursor();
					echo '<a title="'.T_('Voir le ticket').' '.$rowi['id'].'" href="./index.php?page=ticket&id='.$rowi['id'].'">'.$type.' '.$resulttech['firstname'].' '.$resulttech['lastname'].': '.$rowi['title'].'</a>';
					echo '<a title="'.T_('Supprimer cet évènement').'" href="./index.php?page=planning&view='.$_GET['view'].'&cursor='.$_GET['cursor'].'&next='.$_GET['next'].'&delete='.$rowi['id'].'"> <i class="icon-trash red"></i></a>';
					echo '<br />';
				}
				$query->closeCursor();
			echo '
			</td>
		</tr>';
	}
		
	echo "</table>";
} 
?>