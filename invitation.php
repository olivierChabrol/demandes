<?php
################################################################################
# @Name : index.php
# @Description : main page include all sub-pages
# @Call :
# @Parameters :
# @Author : Flox
# @Create : 07/03/2010
# @Update : 08/09/2020
# @Version : 3.2.4
################################################################################


//define current language
require "localization.php";

//put keywords in variable
if($_POST['keywords']||$_GET['keywords']) {
	$keywords="$_GET[keywords]$_POST[keywords]";
	$keywords=htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8');
} else {$keywords='';}
if($_POST['userkeywords']||$_GET['userkeywords']) {
	$userkeywords="$_GET[userkeywords]$_POST[userkeywords]";
	$userkeywords=htmlspecialchars($userkeywords, ENT_QUOTES, 'UTF-8');
} else {$userkeywords='';}
if($_POST['assetkeywords']||$_GET['assetkeywords']) {
	$assetkeywords="$_GET[assetkeywords]$_POST[assetkeywords]";
	$assetkeywords=htmlspecialchars($assetkeywords, ENT_QUOTES, 'UTF-8');
} else {$assetkeywords='';}
if($_POST['rightkeywords']||$_GET['rightkeywords']) {
	$rightkeywords="$_GET[rightkeywords]$_POST[rightkeywords]";
	$rightkeywords=htmlspecialchars($rightkeywords, ENT_QUOTES, 'UTF-8');
} else {$rightkeywords='';}
if($_POST['procedurekeywords']||$_GET['procedurekeywords']) {
	$procedurekeywords="$_GET[procedurekeywords]$_POST[procedurekeywords]";
	$procedurekeywords=htmlspecialchars($procedurekeywords, ENT_QUOTES, 'UTF-8');
} else {$procedurekeywords='';}

?>
<!doctype html>
<html lang="fr" style="--scrollbar-width:17px; --moz-scrollbar-thin:17px; font-size: 0.925rem;">
	<head>
	    <?php header('x-ua-compatible: ie=edge'); //disable ie compatibility mode ?>
		<meta charset="utf-8" />
		<meta name="theme-color" content="#4aa0df">
		<?php
		echo '<meta http-equiv="Refresh" content="'.$rparameters['auto_refresh'].';">'; 
		?>
		<title>SIGED | <?php echo T_('Gestion des demandes administratives'); ?></title>
		<link rel="shortcut icon" type="image/png" href="./images/
		<?php
		echo 'favicon_ticket.png';
		?>"
		/>
		<meta name="description" content="gestsup" />
		<meta name="robots" content="noindex, nofollow">
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">

		<!-- bootstrap styles -->
		<link rel="stylesheet" href="./components/bootstrap/dist/css/bootstrap.min.css" />

		<!-- fontawesome styles -->
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" type="text/css" href="./components/fontawesome/css/solid.min.css">

			<!-- datetimepicker styles -->
			<link rel="stylesheet" href="./components/tempus-dominus/build/css/tempusdominus-bootstrap-4.min.css" />
			<!-- chosen styles -->
			<link rel="stylesheet" type="text/css" href="./components/chosen/chosen.min.css">
		<!-- ace styles -->
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-font.min.css" />
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace.min.css" />
		<link rel="stylesheet" type="text/css" href="./template/ace/dist/css/ace-themes.min.css" />


        <!-- Request page styles -->
        <link rel="stylesheet" type="text/css" href="template/assets/css/request.css" />
        <link rel="stylesheet" type="text/css" href="template/assets/css/select2.min.css" />

		<!-- JQuery script -->
		<script type="text/javascript" src="./components/jquery/jquery.min.js"></script>

        <link rel="stylesheet" href="./components/jquery-ui/jquery-ui.css">
        <script src="./components/jquery-ui/jquery-ui.js"></script>

        <!-- Request script -->
        <script type="text/javascript" src="./template/assets/js/request-common.js"></script>
	</head>
	<?php
	   require('main.php');
		//loading js scripts
		echo'
		  <script type="text/javascript" src="./components/popper-js/dist/umd/popper.min.js"></script>
		  <script type="text/javascript" src="./components/bootstrap/dist/js/bootstrap.min.js"></script>
		  <script type="text/javascript" src="./template/ace/dist/js/ace.min.js"></script>
		  <script type="text/javascript" src="./template/assets/js/select2/select2.min.js"></script>
		';
		//include specific script for page
		include ('./wysiwyg.php');

				echo '
				<script type="text/javascript" src="./components/chosen/chosen.jquery.min.js"></script>
				<script>
					if($(".chosen-select"))
						$(\'.chosen-select\').chosen({allow_single_deselect:true,no_results_text: "'.T_('Aucun résultat pour').'"});
						';
						echo '</script>';


			//call reminder popup
			include "./reminder.php";

		//close database access
		$db = null;
		?>
</html>
