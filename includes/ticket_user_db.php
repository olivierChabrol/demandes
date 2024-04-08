<?php
################################################################################
# @Name : ticket_user_db.php
# @Description : submit data from modal form to database
# @Call : ./includes/ticket_user.php
# @Parameters :  
# @Author : Flox
# @Create : 25/08/2020
# @Update : 25/08/2020
# @Version : 3.2.2 p1
################################################################################

//security check
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
{
    //db connection
    require('../connect.php');

    //switch SQL MODE to allow empty values
    $db->exec('SET sql_mode = ""');

    //load parameters table
    $qry=$db->prepare("SELECT * FROM `tparameters`");
    $qry->execute();
    $rparameters=$qry->fetch();
    $qry->closeCursor();

    //display error parameter
    if($rparameters['debug']==1) {
        ini_set('display_errors', 'On');
        ini_set('display_startup_errors', 'On');
        ini_set('html_errors', 'On');
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 'Off');
        ini_set('display_startup_errors', 'Off');
        ini_set('html_errors', 'Off');
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    }

    //init string
    if(!isset($_POST['add'])) $_POST['add'] = '';
    if(!isset($_POST['modifyuser'])) $_POST['modifyuser'] = '';
    if(!isset($_POST['firstname'])) $_POST['firstname'] = '';
    if(!isset($_POST['lastname'])) $_POST['lastname'] = '';
    if(!isset($_POST['phone'])) $_POST['phone'] = '';
    if(!isset($_POST['mobile'])) $_POST['mobile'] = '';
    if(!isset($_POST['mail'])) $_POST['mail'] = '';
    if(!isset($_POST['company'])) $_POST['company'] = '';
    if(!isset($_GET['user_id'])) $_GET['user_id'] = '';

    //secure string
    $_POST['firstname']=htmlspecialchars($_POST['firstname'], ENT_QUOTES, 'UTF-8');
    $_POST['lastname']=htmlspecialchars($_POST['lastname'], ENT_QUOTES, 'UTF-8');
    $_POST['phone']=htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8');
    $_POST['mobile']=htmlspecialchars($_POST['mobile'], ENT_QUOTES, 'UTF-8');
    $_POST['mail']=htmlspecialchars($_POST['mail'], ENT_QUOTES, 'UTF-8');
    $_POST['company']=htmlspecialchars($_POST['company'], ENT_QUOTES, 'UTF-8');

    //add user
    if($_POST['add']) 
    {
        $qry=$db->prepare("INSERT INTO `tusers` (`profile`,`firstname`,`lastname`,`phone`,`mobile`,`mail`,`company`) VALUES (2,:firstname,:lastname,:phone,:mobile,:mail,:company)");
        $qry->execute(array('firstname' => $_POST['firstname'],'lastname' => $_POST['lastname'],'phone' => $_POST['phone'],'mobile' => $_POST['mobile'],'mail' => $_POST['usermail'],'company' => $_POST['company']));
        echo json_encode(array("status" => "success", "user_id" => $db->lastInsertId(), "firstname" => $_POST['firstname'],"lastname" => $_POST['lastname']));
    //modify user
    }elseif($_POST['modifyuser']) 
    {
        $qry=$db->prepare("UPDATE `tusers` SET `firstname`=:firstname, `lastname`=:lastname, `phone`=:phone, `mobile`=:mobile, `mail`=:mail, `company`=:company WHERE `id`=:id");
        $qry->execute(array('firstname' => $_POST['firstname'],'lastname' => $_POST['lastname'],'phone' => $_POST['phone'],'mobile' => $_POST['mobile'],'mail' => $_POST['usermail'],'company' => $_POST['company'],'id' => $_GET['user_id']));
        echo json_encode(array("status" => "success", "user_id" => $_GET['user_id'], "firstname" => $_POST['firstname'],"lastname" => $_POST['lastname']));
    } else {
        echo json_encode(array("status" => "failed"));
    }
} else {
	echo json_encode(array("status" => "failed"));
}
?>