<?php
################################################################################
# @Name : pwd_recovery.php
# @Description : recover pwd
# @Call : 
# @Parameters : 
# @Author : Flox
# @Create : 19/03/2019
# @Update : 11/06/2020
# @Version : 3.2.2
################################################################################
?>
<h1>GestSup password recovery</h1>
<form method="POST" action="">
	<label for="login">Login :</label>
	<input autocomplete="off" type="text" name="login" />
	<label for="password">password :</label>
	<input autocomplete="off" type="password" name="password" />
	<input type="submit" />
</form>
<?php
if(isset($_POST['password']) && isset($_POST['login']))
{
	$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	
	$query="UPDATE `tusers` SET `password`='$hash', disable='0' WHERE `login`='$_POST[login]';";
	echo '
		Follow this steps :
		<ul>
			<li>STEP 1 : Connect to database (PhpMyAdmin or command line)</li>
			<li>STEP 2 : Select GestSup database (default bsup)</li>
			<li>STEP 3 : Execute the following request :</li>
			<br />
			 <b>'.$query.'</b>
		</ul>
	';
}
?>