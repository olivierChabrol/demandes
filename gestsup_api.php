<?php
################################################################################
# @Name : gestsup_api.php 
# @Description : Display short ticket declaration interface to integer in other website (ex: intranet)
# @Author : Flox
# @Create : 29/10/2013
# @Update : 28/10/2019
# @Version : 3.1.46
################################################################################


############################## START EDITABLE PART #############################
$host='localhost'; //SQL server name
$db_name=''; //database name
$charset='utf8'; //database charset default utf8
$user='root'; //database user name
$password=''; //database password
############################## END EDITABLE PART #############################

//database connection
try {$db = new PDO	("mysql:host=$host;dbname=$db_name;charset=$charset", "$user", "$password" , array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));}
catch (Exception $e)
{die('Error : ' . $e->getMessage());}
 
//initialize variables
if(!isset($_POST['send'])) $_POST['send']= '';

if ($_POST['send']) //database input
{
	$date=date('Y-m-d H:m:s');
	
	//escape special char
	$_POST['description'] = strip_tags($_POST['description']);
	$_POST['title'] = strip_tags($_POST['title']);
	
	$qry=$db->prepare("
	INSERT INTO `tincidents` 
	(`user`,`title`,`description`,`state`,`date_create`,`creator`,`criticality`,`techread`) 
	VALUES 
	(:user,:title,:description,'5',:date_create,:creator,'4','0')");
	$qry->execute(array('user' => $_POST['user'],'title' => $_POST['title'],'description' => $_POST['description'],'date_create' => $date,'creator' => $_POST['user']));
	
	//load parameters table
	$qry=$db->prepare("SELECT * FROM `tparameters`");
	$qry->execute();
	$rparameters=$qry->fetch();
	$qry->closeCursor();
	
	//find incident number  
	$qry=$db->prepare("SELECT MAX(id) FROM tincidents");
	$qry->execute();
	$row=$qry->fetch();
	$qry->closeCursor();
	$number =$row[0];
	
	echo '
	<font color="green">
		La demande <b>#'.$number.'</b> à bien été prise en compte.<br />
	</font>
	Pour suivre vos demandes vous pouvez vous rendre sur la page <a target="_blank" href="'.$rparameters['server_url'].'">'.$rparameters['server_url'].'</a>
	';
}
else //display form
{
	echo '
	<form method="POST" action="" id="myform">
		<table border="0">
			<tr>
				<td><label for="user">Nom:</label></td>
				<td>
					<select name="user" />
						';
						$qry=$db->prepare("SELECT `id`,`lastname`,`firstname` FROM `tusers` WHERE disable='0' ORDER BY lastname");
						$qry->execute();
						while($row=$qry->fetch()) 
						{
							echo '<option value="'.$row['id'].'">'.$row['lastname'].' '.$row['firstname'].'</option>';
						}
						$qry->closeCursor();
						echo '
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="title">Titre:</label>
				</td>
				<td>
					<input name="title" type="text" size="30px" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<label for="description">Demande:</label>
				<br />
				<textarea name="description" cols="50" rows="10" ></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" value=" Envoyer votre demande " id="send" name="send" />
				</td>
			</tr>
		</table>
	</form>';
}
//close database access
$db = null;
?>