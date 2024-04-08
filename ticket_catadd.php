<?php
################################################################################
# @Name : edit_categories.php
# @Description : add and modify categories
# @Call : ./ticket.php
# @Parameters :  
# @Author : Flox
# @Create : 07/01/2014
# @Update : 04/12/2017
# @Version : 3.1.28 p2
################################################################################

//initialize variables 
if(!isset($_GET['id'])) $_GET['id'] = ''; 
if(!isset($_GET['subcat'])) $_GET['subcat'] = ''; 
if(!isset($_GET['cat'])) $_GET['cat'] = ''; 

if(!isset($_POST['addsubcat'])) $_POST['addsubcat'] = ''; 
if(!isset($_POST['modifysubcat'])) $_POST['modifysubcat'] = ''; 
if(!isset($_POST['subcatname'])) $_POST['subcatname'] = '';
 
if(!isset($subcat)) $subcat = '';
if(!isset($subcatname)) $subcatname = '';
if(!isset($name)) $name = '';

$db_cat=strip_tags($db->quote($_GET['cat']));
$db_editcat=strip_tags($db->quote($_GET['editcat']));

if($_POST['addsubcat']){
	$qry=$db->prepare("INSERT INTO `tsubcat` (`cat`,`name`) VALUES (:cat,:name)");
	$qry->execute(array('cat' => $_GET['cat'],'name' => $_POST['subcatname']));
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
if($_POST['modifysubcat']){
	$qry=$db->prepare("UPDATE `tsubcat` SET name=:name WHERE id=:id");
	$qry->execute(array('name' => $_POST['name'],'id' => $_GET['editcat']));
	//redirect
	$www = "./index.php?page=ticket&id=$_GET[id]&userid=$_GET[userid]";
	echo '<script language="Javascript">
	<!--
	document.location.replace("'.$www.'");
	// -->
	</script>';
}
// new subcat
if ($_GET['action']=="addcat")
{
	$boxtitle='<i class=\'icon-sitemap blue bigger-120\'></i> '.T_('Ajout d\'une sous-catégorie');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input name="addsubcat" type="hidden" value="1">
		<label for="cat">'.T_('Catégorie').':</label>
		<br />
		<select id="cat" name="cat">
			';
			$query = $db->query("SELECT * FROM `tcategory` order by name ASC");
			while ($row = $query->fetch()) 
			{
				if($row['id']==0)
				{$boxtext= $boxtext.'<option value="'.$row['id'].'">'.T_($row['name']).'</option>';}
				else
				{$boxtext= $boxtext.'<option value="'.$row['id'].'">'.$row['name'].'</option>';}
			} 
			$query->closeCursor(); 
			$query = $db->query("SELECT * FROM `tcategory` WHERE id like $db_cat");
			$row=$query->fetch();
			$query->closeCursor();
			$boxtext= $boxtext.'<option value="'.$row['id'].'" selected>'.$row['name'].'</option>';
        	$boxtext= $boxtext.'				
		</select>
		<br />
		<label for="subcat"> '.T_('Sous-catégorie').':</label>
		<input  name="subcatname" type="text" value="'.$subcatname.'" size="26">
	</form>
	';
	$valid=T_('Ajouter');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
//edit subcat
else
{
	$boxtitle='<i class=\'icon-user blue bigger-120\'></i>'.T_('Modification sous-catégorie');
	$boxtext= '
	<form name="form" method="POST" action="" id="form">
		<input  name="modifysubcat" type="hidden" value="1">
		'.T_('Catégorie').':
		<br />
		<select id="cat" name="cat">
		';
		$query = $db->query("SELECT * FROM `tcategory` order by name ASC");
		while ($row = $query->fetch())
		{
			$query2 = $db->query("SELECT id FROM `tcategory` WHERE id like $db_cat");
			$row2=$query2->fetch();
			$query2->closeCursor();
			if($row2['id']==$row['id']) {$selected='selected';} else {$selected='';}
			if($row['id']==0)
			{$boxtext= $boxtext.'<option value="'.$row['id'].'" '.$selected.'>'.T_($row['name']).'</option>';}
			else
			{$boxtext= $boxtext.'<option value="'.$row['id'].'" '.$selected.'>'.$row['name'].'</option>';}
		}
		$query->closeCursor(); 
		$query=$db->query("SELECT * FROM tsubcat WHERE id LIKE $db_editcat");
		$row=$query->fetch();
		$query->closeCursor();
		$boxtext=$boxtext.'
		</select>
		<br />
		'.T_('Sous-catégorie').':
		<input  name="name" type="text" size="26" value="'.$row['name'].'">
		<br /><br />
	</form>
	';
	$valid=T_('Modifier');
	$action1="$('form#form').submit();";
	$cancel=T_('Fermer');
	$action2="$( this ).dialog( \"close\" ); ";
}
include "./modalbox.php"; 
?>