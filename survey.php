<?php
################################################################################
# @Name : ./survey.php 
# @Description : display survey
# @Call : auto mail
# @Parameters : token
# @Author : Flox
# @Version : 3.2.4
# @Create : 22/04/2017
# @Update : 08/09/2020
################################################################################

//initialize variables 
require('core/init_get.php');
require('core/functions.php');

if(!isset($error)) $error = '';
if(!isset($question_number)) $question_number = '';
if(!isset($_POST['next'])) $_POST['next'] = '';
if(!isset($_POST['previous'])) $_POST['previous'] = '';
if(!isset($_POST['answer'])) $_POST['answer'] = '';
if(!isset($_POST['question_number'])) $_POST['question_number'] = '';
if(!isset($_POST['question_id'])) $_POST['question_id'] = '';
if(!isset($_POST['validation'])) $_POST['validation'] = '';

//default value
if(!$question_number) {$question_number=1;}
if(!$_POST['question_id']) $_POST['question_id'] = 1;

//db connection
require "connect.php";
$db->exec('SET sql_mode = ""');

$db_token=strip_tags($db->quote($_GET['token']));

//load parameters table
$qry=$db->prepare("SELECT * FROM `tparameters`");
$qry->execute();
$rparameters=$qry->fetch();
$qry->closeCursor();

//display error parameter
if ($rparameters['debug']) {
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

if($_GET['token'])
{
	//check if token exist
	$qry=$db->prepare("SELECT `ticket_id` FROM `ttoken` WHERE token=:token");
	$qry->execute(array('token' => $_GET['token']));
	$row=$qry->fetch();
	$qry->closeCursor();

	if($row)
	{
		$token=true;
		$ticket_id=$row['ticket_id'];
	} else {
		$token=false;
		$ticket_id='';
	}
} else {
	$ticket_id='';
	$token=false;
}

//define PHP time zone
if($rparameters['server_timezone']) {date_default_timezone_set($rparameters['server_timezone']);}

$datetime=date('Y-m-d H:i:s');

//load locales
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
if($lang=='fr') {$_GET['lang'] = 'fr_FR';}
else {$_GET['lang'] = 'en_US';}
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locale');
define('DEFAULT_LOCALE', '($_GET[lang]');
require_once('./components/php-gettext/gettext.inc');
$encoding = 'UTF-8';
$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;
T_setlocale(LC_MESSAGES, $locale);
T_bindtextdomain($_GET['lang'], LOCALE_DIR);
T_bind_textdomain_codeset($_GET['lang'], $encoding);
T_textdomain($_GET['lang']);

//get ticket data
$qry=$db->prepare("SELECT `title`,`user` FROM `tincidents` WHERE id=:id");
$qry->execute(array('id' => $ticket_id));
$ticket=$qry->fetch();
$qry->closeCursor();

//if survey is already begin then goto last empty question
$qry=$db->prepare("SELECT `number` FROM `tsurvey_questions` WHERE id=(SELECT MAX(question_id) FROM tsurvey_answers WHERE ticket_id=:ticket_id)");
$qry->execute(array('ticket_id' => $ticket_id));
$current_question_number=$qry->fetch();
$qry->closeCursor();
if(empty($current_question_number['number'])) {$current_question_number['number']=0;}

if($current_question_number['number']) {$question_number=$current_question_number['number']+1;}

//actions on submit
if($_POST['previous'] || $_POST['next'] || $_POST['validation']) {
	
	//check error
	if($_POST['answer'] && (strlen(trim($_POST['answer']))!=0) && ($_POST['next'] || $_POST['validation'])) {
		//check previous answer
		$qry=$db->prepare("SELECT `answer` FROM `tsurvey_answers` WHERE ticket_id=:ticket_id AND question_id=:question_id");
		$qry->execute(array('ticket_id' => $ticket_id,'question_id' => $_POST['question_id']));
		$row=$qry->fetch();
		$qry->closeCursor();
		
		if($row)
		{
			if($row['answer']!=$_POST['answer']) //if answer is different than db row update it
			{
				$_POST['answer'] = strip_tags($_POST['answer']);
				//update answer
				$qry=$db->prepare("UPDATE `tsurvey_answers` SET `date`=:date, `answer`=:answer WHERE ticket_id=:ticket_id AND question_id=:question_id");
				$qry->execute(array('date' => $datetime,'answer' => $_POST['answer'],'ticket_id' => $ticket_id,'question_id' => $_POST['question_id']));
			}
		} else {
			$_POST['answer'] = strip_tags($_POST['answer']);
			//insert answer
			$qry=$db->prepare("INSERT INTO `tsurvey_answers` (`date`,`ticket_id`,`question_id`,`answer`) VALUES (:date,:ticket_id,:question_id,:answer)");
			$qry->execute(array('date' => $datetime,'ticket_id' => $ticket_id,'question_id' => $_POST['question_id'],'answer' => $_POST['answer']));
		}
	} else {if(!$_POST['previous']){$error=T_("Aucune réponse n'a été saisie");}}
	//change current question number
	if(!$error)
	{
		if($_POST['next']){$question_number=$_POST['question_number']+1;}
		if($_POST['previous']){$question_number=$_POST['question_number']-1;}
	} else {$question_number=$_POST['question_number'];}
}

//get question id
$qry=$db->prepare("SELECT `id` FROM `tsurvey_questions` WHERE number=:number");
$qry->execute(array('number' => $question_number));
$question_id=$qry->fetch();
$qry->closeCursor();
$question_id=$question_id[0];

//display debug
if($rparameters['debug']) {echo '<u><b>DEBUG MODE:</b></u><br /><b>VAR:</b> POST_answer='.$_POST['answer'].' question_number='.$question_number.' post_question_number='.$_POST['question_number'].' question_id='.$question_id.' post_question_id='.$_POST['question_id']; }

if($_POST['validation'] && !$error && $ticket_id)
{
	//delete token
	$qry=$db->prepare("DELETE FROM `ttoken` WHERE ticket_id=:ticket_id");
	$qry->execute(array('ticket_id' => $ticket_id));
	
	if($rparameters['survey_auto_close_ticket']==1)
	{
		//modify ticket state in close and unread tag
		$qry=$db->prepare("UPDATE `tincidents` SET `state`='3',date_res=:date_res WHERE `id`=:id");
		$qry->execute(array('date_res' => $datetime,'id' => $ticket_id));
		
		//insert close thread
		$qry=$db->prepare("INSERT INTO `tthreads` (`ticket`,`date`,`type`,`author`) VALUES (:ticket,:date,'4',:author)");
		$qry->execute(array('ticket' => $ticket_id,'date' => $datetime,'author' => $ticket['user']));
	}
}
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>GestSup | <?php echo T_('Sondage'); ?></title>
		<link rel="shortcut icon" type="image/png" href="./images/favicon_survey.png" />
		<meta name="description" content="gestsup" />
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
		<!-- bootstrap styles -->
		<link rel="stylesheet" href="./components/bootstrap/dist/css/bootstrap.min.css" />
		<!-- fontawesome styles -->
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/solid.min.css">
		<!-- smartwizard styles -->
		<link rel="stylesheet" type="text/css" href="./components/smartwizard/dist/css/smart_wizard.min.css">
		<link rel="stylesheet" type="text/css" href="./components/smartwizard/dist/css/smart_wizard_theme_circles.min.css">
		
		<!-- ace styles -->
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-font.min.css">
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace.min.css">
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-themes.min.css">
	</head>
	<body class="bgc-white">
		<div style="body-container" >
			<nav class="navbar navbar-expand-lg navbar-fixed navbar-skyblue">
				<div class="navbar-inner">
					<div class="navbar-content">
						<a href="#" class="navbar-brand">
							<a class="navbar-brand text-white" href="#">
								<i class="fa fa-check text-80" ></i>&nbsp;
								<span><?php echo T_('Sondage'); ?></span>&nbsp;
								<?php 
								//re-size logo if height superior 40px
								if($rparameters['logo']!='') 
								{
									$height = getimagesize("./upload/logo/$rparameters[logo]");
									$height=$height[1];
									if($height>40) {$logo_size='height="40"';} else {$logo_size='';}
								} else {$logo_size='';}
								echo '&nbsp;<img style="border-style: none" '.$logo_size.' alt="logo" src="./upload/logo/'; if($rparameters['logo']=='') echo 'logo.png'; else echo $rparameters['logo'];  echo '" />';
								echo '&nbsp;'.$rparameters['company']; 
								?>
							</a><!-- /.navbar-brand -->
						</a><!--/.brand-->
					</div><!-- /.navbar-header -->
				</div><!--/.navbar-inner-->
			</nav>
			<div class="main-container p-4" id="main-container">
				<div role="main" class="main-content">
					<div class="card bcard shadow" id="card-1">
						<div class="card-header">
							<h5 class="card-title">
								<i class="fa fa-ticket-alt text-primary-m2"></i> <?php if($ticket_id) {echo T_('Ticket').' n°'.$ticket_id.' : '.$ticket['title'].'';} ?>
							</h5>
						</div><!-- /.card-header -->
						<div class="card-body p-0">
							<!-- to have smooth .card toggling, it should have zero padding -->
							<div class="p-3">
								<?php 
									if($rparameters['survey']==1)
									{
										if($token==true)
										{
											if($_POST['validation'] && !$error)
											{
												echo '<br /><br /><br />';
												echo DisplayMessage('success',T_('Votre sondage a été envoyé merci'));
											} 
											else
											{
												echo '
												<div class="">
													<div class="">
														<div id="smartwizard-1" class="sw-main sw-theme-circles">
															<ul class="mx-auto nav nav-tabs step-anchor">
																';
																$qry=$db->prepare("SELECT `id`,`number` FROM `tsurvey_questions` WHERE disable='0' ORDER BY `number`");
																$qry->execute();
																while($row=$qry->fetch()) 
																{
																	if($question_number>=$row['number']) {$active='done success';} else {$active='';}
																	echo '
																	<li data-target="" class="nav-item '.$active.'" >
																		<a class="nav-link" >
																			<span class="step-title">'.$row['number'].'</span>
																			<span class="step-title-done"><i class="fa fa-check text-success-m1"></i></span>
																		</a>
																	</li>
																	';
																}
																$qry->closeCursor();
																echo '
															</ul>
														</div>
														<hr>
														<form method="post" id="form" action="" class="form-horizontal" id="sample-form" >
															<div class="step-content row-fluid position-relative" id="step-container">
																';
																//display error
																if($error) {echo DisplayMessage('error',$error);} 
																echo '
																<div class="col-xs-6 col-sm-2"></div>
																<div class="col-xs-6 col-sm-10" id="step1">
																		';
																		//get question
																		$qry=$db->prepare("SELECT * FROM `tsurvey_questions` WHERE number=:number AND disable='0'");
																		$qry->execute(array('number' => $question_number));
																		$row=$qry->fetch();
																		$qry->closeCursor();
																		
																		//get question answer
																		$qry=$db->prepare("SELECT `answer` FROM `tsurvey_answers` WHERE ticket_id=:ticket_id AND question_id=:question_id");
																		$qry->execute(array('ticket_id' => $ticket_id, 'question_id' => $question_id,));
																		$answer=$qry->fetch();
																		$qry->closeCursor();
																		if(empty($answer['answer'])) {$answer['answer']='';}
																		//display question
																		echo '<h3 class="lighter text-success pb-4">'.T_('Question').' n°'.$row['number'].' : '.$row['text'].'</h3><div class="space-8"></div>';
																		
																		//yes / no question
																		if($row['type']==1)
																		{
																			//check if an existing value is present in db
																			if($answer['answer']==T_('Oui')) {$checked_yes='checked';} else {$checked_yes='';}
																			if($answer['answer']==T_('Non')) {$checked_no='checked';} else {$checked_no='';}
																			
																			echo '
																				<div class="pl-4">
																					<div class="radio">
																						<label>
																							<input name="answer" '.$checked_yes.' value="'.T_('Oui').'" type="radio" class="ace">
																							<span class="lbl"> '.T_('Oui').'</span>
																						</label>
																					</div>
																					<div class="radio">
																						<label>
																							<input name="answer" '.$checked_no.' value="'.T_('Non').'" type="radio" class="ace">
																							<span class="lbl"> '.T_('Non').'</span>
																						</label>
																					</div>
																				</div>
																			';
																		}
																		//text question
																		if($row['type']==2)
																		{
																			echo '
																				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
																				<textarea class="form-control" id="answer" name="answer" width="200" cols="100" rows="8">'.$answer['answer'].'</textarea>
																			';
																		}
																		//select question
																		if($row['type']==3)
																		{
																			echo '
																			<div class="pl-4">
																				<select style="width:auto;" class="form-control" width="20" id="answer" name="answer" >
																					';
																					if($row['select_1']) {echo '<option value="'.$row['select_1'].'" '; if($answer['answer']==$row['select_1']) {echo 'selected';} echo ' >'.$row['select_1'].'</option>';}	
																					if($row['select_2']) {echo '<option value="'.$row['select_2'].'" '; if($answer['answer']==$row['select_2']) {echo 'selected';} echo ' >'.$row['select_2'].'</option>';}	
																					if($row['select_3']) {echo '<option value="'.$row['select_3'].'" '; if($answer['answer']==$row['select_3']) {echo 'selected';} echo ' >'.$row['select_3'].'</option>';}	
																					if($row['select_4']) {echo '<option value="'.$row['select_4'].'" '; if($answer['answer']==$row['select_4']) {echo 'selected';} echo ' >'.$row['select_4'].'</option>';}	
																					if($row['select_5']) {echo '<option value="'.$row['select_5'].'" '; if($answer['answer']==$row['select_5']) {echo 'selected';} echo ' >'.$row['select_5'].'</option>';}	
																					echo '
																				</select>
																			</div>
																			';
																		}
																		if($row['type']==4)
																		{
																			for($i = 1; $i <= $row['scale']; $i++)
																			{
																				echo '
																				<div class="radio pl-4">
																					<label>
																						<input name="answer" value="'.$i.'" type="radio" '; if($answer['answer']==$i) {echo 'checked';} echo ' class="ace">
																						<span class="lbl"> '.$i.'</span>
																					</label>
																				</div>
																				';
																			}
																		}
																		echo '
																</div>
																<br /><br /><br />
																<br /><br /><br /><br /><br />
																<hr>
																<input type="hidden" name="question_number" value="'.$question_number.'">
																<input type="hidden" name="question_id" value="'.$row['id'].'">
																<div class="row-fluid wizard-actions">
																	<center>
																		';
																		if($question_number!=1)
																		{
																			echo '
																			<button type="submit" id="previous" name="previous" value="previous" class="btn btn-grey " data-last="Finish ">
																			<i class="fa fa-arrow-left fa fa-on-right"></i>
																				'.T_('Précédent').'
																			</button>
																			&nbsp;&nbsp;&nbsp;
																			';
																		}
																		//get last question number
																		$qry=$db->prepare("SELECT MAX(number) FROM `tsurvey_questions` WHERE disable='0'");
																		$qry->execute();
																		$row=$qry->fetch();
																		$qry->closeCursor();
																		
																		if($row[0]==$question_number)
																		{
																			echo '
																			<button type="submit" id="validation" name="validation" value="validation" class="btn btn-success btn-next" data-last="Finish">
																				'.T_('Valider').'
																				<i class="fa fa-arrow-right fa fa-on-right"></i>
																			</button>
																			';
																		} else {
																			echo '
																			<button type="submit" id="next" name="next" value="next" class="btn btn-info btn-next" data-last="Finish">
																				'.T_('Suivant').'
																				<i class="fa fa-arrow-right fa fa-on-right"></i>
																			</button>
																			';
																		}
																		
																		echo '
																		
																	</center>
																</div>
															</div>
														</form>
													</div><!-- /widget-main -->
												</div><!-- /widget-body -->
												';
											}
										} else {
											echo DisplayMessage('error',T_('Jeton invalide, contacter votre administrateur'));
										}
									} else {
										echo DisplayMessage('error',T_("La fonction sondage n'est pas activée contacter votre administrateur"));
									} 
									?>
							</div>
						</div><!-- /.card-body -->
					</div>	<!-- /.card -->		
				</div>
			</div>
		</div>
		<span style="position: absolute; bottom: 0; right: 0; font-size:10px; "><a target="_blank" href="https://gestsup.fr"><?php echo T_('Sondage généré par'); ?> GestSup</a></span>
	</body>

	<!-- include  scripts -->
	<script type="text/javascript" src="./components/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="./components/popper-js/dist/umd/popper.min.js"></script>
	<script type="text/javascript" src="./components/bootstrap/dist/js/bootstrap.min.js"></script>

	<!-- include ace scripts -->
	<script type="text/javascript" src="./template/ace/dist/js/ace.js"></script>
	<script type="text/javascript" src="./template/ace/assets/js/demo.js"></script>
	
</html>