<?php
################################################################################
# @Name : searchengine_asset.php
# @Description : search engine in database asset
# @Call : /asset_list.php
# @Parameters : assetkeywords
# @Author : Flox
# @Create : 18/12/2015
# @Update : 30/05/2018
# @Version : 3.1.33
################################################################################

//case when assetkeywords contain '
$assetkeywords = str_replace("'","\'",$assetkeywords);
//assetkeywords table space separation
$assetkeywords_search=explode(" ",$assetkeywords);
//count $assetkeywords
$nbassetkeywords= sizeof($assetkeywords_search);

if ($nbassetkeywords==2)
{
	$from ='tassets';
	$join='
		LEFT JOIN tassets_state ON tassets.state=tassets_state.id 
		LEFT JOIN tusers ON tassets.user=tusers.id 
		LEFT JOIN tassets_iface ON tassets.id=tassets_iface.asset_id 
		LEFT JOIN tassets_location ON tassets.location=tassets_location.id
	';
	$where="
		(
			tassets.sn_internal LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[0]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets.description LIKE '%$assetkeywords_search[0]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[0]%' OR
			tassets_iface.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[0]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[0]%'  
		) AND (
			tassets.sn_internal LIKE '%$assetkeywords_search[1]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[1]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[1]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[1]%' OR 
			tassets.description LIKE '%$assetkeywords_search[1]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.netbios LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[1]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[1]%'  
		) AND (
			tassets.sn_internal LIKE '$_POST[sn_internal]%'
			AND	tassets.user LIKE '$_POST[user]'
			AND	tassets.type LIKE '$_POST[type]'
			AND	tassets.model LIKE '$_POST[model]'
			AND	tassets.description LIKE '%$_POST[description]%'
			AND	tassets.date_stock LIKE '%$_POST[date_stock]%'
			AND	tassets.state LIKE '$_POST[state]'
			AND	tassets.department LIKE '$_POST[department]' 
		)
		AND tassets.disable='0'
		AND (tassets_iface.disable='0' OR tassets_iface.disable IS NULL)
		AND tassets.virtualization LIKE '$_POST[virtual]'
	"; 	
}
else if ($nbassetkeywords==3)
{
	$from ='tassets';
	$join='
		LEFT JOIN tassets_state ON tassets.state=tassets_state.id 
		LEFT JOIN tusers ON tassets.user=tusers.id 
		LEFT JOIN tassets_iface ON tassets.id=tassets_iface.asset_id 
		LEFT JOIN tassets_location ON tassets.location=tassets_location.id
	';
	$where="
		(
			tassets.sn_internal LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[0]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets.description LIKE '%$assetkeywords_search[0]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[0]%' OR
			tassets_iface.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[0]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[0]%'  
		) AND (
			tassets.sn_internal LIKE '%$assetkeywords_search[1]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[1]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[1]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[1]%' OR 
			tassets.description LIKE '%$assetkeywords_search[1]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.netbios LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[1]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[1]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[1]%'  
		) AND (
			tassets.sn_internal LIKE '%$assetkeywords_search[2]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[2]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[2]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[2]%' OR 
			tassets.description LIKE '%$assetkeywords_search[2]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[2]%' OR 
			tassets_iface.netbios LIKE '%$assetkeywords_search[2]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[2]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[2]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[2]%'  
		) AND (
			tassets.sn_internal LIKE '$_POST[sn_internal]%'
			AND	tassets.user LIKE '$_POST[user]'
			AND	tassets.type LIKE '$_POST[type]'
			AND	tassets.model LIKE '$_POST[model]'
			AND	tassets.description LIKE '%$_POST[description]%'
			AND	tassets.date_stock LIKE '%$_POST[date_stock]%'
			AND	tassets.state LIKE '$_POST[state]'
			AND	tassets.department LIKE '$_POST[department]' 
		)
		AND tassets.disable='0'
		AND (tassets_iface.disable='0' OR tassets_iface.disable IS NULL)
		AND tassets.virtualization LIKE '$_POST[virtual]'
	"; 	
}
else
{
	$from ='tassets';
	$join='
		LEFT JOIN tassets_state ON tassets.state=tassets_state.id 
		LEFT JOIN tusers ON tassets.user=tusers.id 
		LEFT JOIN tassets_iface ON tassets.id=tassets_iface.asset_id 
		LEFT JOIN tassets_location ON tassets.location=tassets_location.id
	';
	$where="
		(
			tassets.sn_internal LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_manufacturer LIKE '%$assetkeywords_search[0]%' OR 
			tassets.sn_indent LIKE '%$assetkeywords_search[0]%' OR 
			tassets.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets.description LIKE '%$assetkeywords_search[0]%' OR 
			tassets.socket LIKE '%$assetkeywords_search[0]%' OR
			tusers.firstname LIKE '%$assetkeywords_search[0]%' OR
			tusers.lastname LIKE '%$assetkeywords_search[0]%' OR
			tassets_iface.netbios LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.ip LIKE '%$assetkeywords_search[0]%' OR 
			tassets_iface.mac LIKE '%$assetkeywords_search[0]%' OR
			tassets_location.name LIKE '%$assetkeywords_search[0]%' 
		) AND (
			tassets.sn_internal LIKE '$_POST[sn_internal]%'
			AND	tassets.user LIKE '$_POST[user]'
			AND	tassets.type LIKE '$_POST[type]'
			AND	tassets.model LIKE '$_POST[model]'
			AND	tassets.description LIKE '%$_POST[description]%'
			AND	tassets.date_stock LIKE '%$_POST[date_stock]%'
			AND	tassets.state LIKE '$_POST[state]'
			AND	tassets.department LIKE '$_POST[department]' 
		)
		AND tassets.disable='0'
		AND (tassets_iface.disable='0' OR tassets_iface.disable IS NULL)
		AND tassets.virtualization LIKE '$_POST[virtual]'
	";
}	
?>