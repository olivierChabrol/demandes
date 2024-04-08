<?php
################################################################################
# @Name : wol.php
# @Description : wake on lan ip asset
# @Call : ./core/asset.php
# @Parameters : $_GET[mac]
# @Author : Flox
# @Create : 19/12/2015
# @Update : 28/05/2020
# @Version : 3.2.2
################################################################################

if(ctype_xdigit($_GET['mac'])) {
	//OS detect
	if(strtoupper(substr(PHP_OS, 0, 3))=='WIN') {
		$rootfolder=dirname(__FILE__);
		$rootfolder=str_replace('\\', '\\\\',$rootfolder);
		$rootfolder=str_replace('core', '',$rootfolder);
		$rootfolder=$rootfolder.'components\\\\wol';
		$result=exec("\"$rootfolder\\\\wol.exe\" $_GET[mac]");
	} else {
		$mac=str_split($_GET['mac'], 2);
		$mac="$mac[0]:$mac[1]:$mac[2]:$mac[3]:$mac[4]:$mac[5]";
		$result=exec("wakeonlan $mac");
	}
} else {$result=" No hexadecimal digit detected";}

//test result
if(($result=="Wake-up packet sent successfully.") || (strtoupper(substr(PHP_OS, 0, 3)) != 'WIN'))
{
	echo DisplayMessage('success',T_('Allumage de').' <b>'.$globalrow['netbios'].'</b> : OK <span style="font-size: x-small;">('.$result.')</span>');
} else {
	echo DisplayMessage('error',T_('Vérifier le wake on lan est bien installé (LINUX: apt-get install wakeonlan)').''.$result);
}
?>