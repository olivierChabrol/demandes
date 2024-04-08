<?php
################################################################################
# @Name : localization.php 
# @Description : page to call php-gettext components and configure it
# @call : /index.php
# @Author : Flox
# @Version : 3.1.46
# @Create : 12/12/2016
# @Update : 07/11/2019
################################################################################

//initialize variables
if(!isset($_SESSION['user_id'])) $_SESSION['user_id'] = '';
if(!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) $_SERVER['HTTP_ACCEPT_LANGUAGE'] = '';

if($_SESSION['user_id'])
{
	//get language from user profile 
	$_GET['lang']=$ruser['language'];
} else {
	//get language from browser 
	$lang = 'fr';
	if (! empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	}
	
	//get available language from app
	$lang_available=glob('./locale/'.'*', GLOB_ONLYDIR);
	foreach ($lang_available as $value) {
		$full_lang=explode('./locale/',$value);
		$full_lang=$full_lang[1];
		$short_lang=explode('_',$full_lang);
		$short_lang=$short_lang[0];
		if($lang==$short_lang) {$_GET['lang']=$full_lang; }
		if($lang=='fr') {$_GET['lang']='fr_FR'; }
	}
	//default language
	if ($_GET['lang']=='') {$_GET['lang'] = 'en_US';}
}

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

//PHP 7 & Windows server fix
putenv("LC_ALL=$_GET[lang]");
setlocale(LC_ALL, $_GET['lang']);
?>