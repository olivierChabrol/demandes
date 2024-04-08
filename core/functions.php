<?php
################################################################################
# @Name : ./core/functions.php
# @Description : define all functions
# @Call :
# @Parameters : 
# @Author : Flox
# @Create : 06/02/2018  
# @Update : 28/05/2020  
# @Version : 3.2.2 p1
################################################################################
if(!function_exists("date_cnv")){
	//date conversion to fr format
	function date_cnv($date) 
	{
		return substr($date,8,2) . "/" . substr($date,5,2) . "/" . substr($date,0,4);
	}
}

if(!function_exists("DatetimeToDB")){
	//date conversion to fr format
	function DatetimeToDB($DatetimeToDB) 
	{
		$DatetimeToDB=DateTime::createFromFormat('d/m/Y H:i:s', $DatetimeToDB);
		$DatetimeToDB=$DatetimeToDB->format('Y-m-d H:i:s');
		return $DatetimeToDB;
	}
}

if(!function_exists("DatetimeToDisplay")){
	function DatetimeToDisplay($DatetimeToDisplay) 
	{
		if($DatetimeToDisplay!='0000-00-00 00:00:00' && $DatetimeToDisplay!='0000-00-00')
		{
			$DatetimeToDisplay=DateTime::createFromFormat('Y-m-d H:i:s', $DatetimeToDisplay);
			$DatetimeToDisplay=$DatetimeToDisplay->format('d/m/Y H:i:s');
			return $DatetimeToDisplay;
		}
	}
}
if(!function_exists("DateToDisplay")){
	function DateToDisplay($DateToDisplay) 
	{
		if($DateToDisplay!='0000-00-00')
		{
			$DateToDisplay=DateTime::createFromFormat('Y-m-d', $DateToDisplay);
			$DateToDisplay=$DateToDisplay->format('d/m/Y');
			return $DateToDisplay;
		}
	}
}
if(!function_exists("DatetimeToDate")){
	function DatetimeToDate($DatetimeToDate) 
	{
		return  substr($DatetimeToDate,8,2) . '/' . substr($DatetimeToDate,5,2) . '/' . substr($DatetimeToDate,0,4);
	}
}
if(!function_exists("CheckFileExtension")){
	function CheckFileExtension($filename) 
	{
		$blacklist = array('php', 'php1', 'php2','php3' ,'php4' ,'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'js', 'htm', 'html', 'phtml', 'exe', 'jsp' ,'pht', 'shtml', 'asa', 'cer', 'asax', 'swf', 'xap', 'phphp', 'inc', 'htaccess', 'sh', 'py', 'pl', 'jsp', 'asp', 'cgi', 'json', 'svn', 'git', 'lock', 'yaml', 'com', 'bat', 'ps1', 'cmd', 'vb', 'hta', 'reg', 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'der', 'exe', 'fxp', 'gadget', 'hlp', 'hta', 'inf', 'ins', 'isp', 'its', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml', 'msh2xml', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'plg', 'prf', 'prg', 'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'tmp', 'url', 'vb', 'vbe', 'vbs', 'vsmacros', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xnk', 'payload', 'shell', 'phar', 'phpt', 'pht', 'pgif');
		$extension=new SplFileInfo($filename);
		$extension=$extension->getExtension();
		if(in_array(strtolower($extension),$blacklist)) {$result=false;} else {$result=true;}
		return $result;
	}
}
if(!function_exists("logit")){
	function logit($type,$message,$user_id) 
	{
		//db connect
		require(__DIR__."/../connect.php");

		//load parameters table
		$qry=$db->prepare("SELECT `log` FROM `tparameters`");
		$qry->execute();
		$rparameters=$qry->fetch();
		$qry->closeCursor();

		if(!isset($_SERVER['REMOTE_ADDR'])) {$_SERVER['REMOTE_ADDR']='php_cli';}

		if($rparameters['log'])
		{
			$qry=$db->prepare("INSERT INTO `tlogs` (`type`,`date`,`message`,user,ip) VALUES (:type,:date,:message,:user,:ip)");
			$qry->execute(array('type' => $type,'date' => date('Y-m-d H:i:s'),'message' => $message, 'user' => $user_id,'ip' => $_SERVER['REMOTE_ADDR']));
		}
	}
}
//date conversion
function date_convert ($date) 
{return  substr($date,8,2) . '/' . substr($date,5,2) . '/' . substr($date,0,4) . ' '.T_('à').' ' . substr($date,11,2	) . 'h' . substr($date,14,2	);}

//date conversion
function MinToHour($min) 
{
	if($min>=60){$time=round($min/60,1).'h';} else {$time=$min.'m';}
	return  $time;
}

function gs_crypt($string, $action = 'e', $key) 
{
    $secret_key = $key;
    $secret_iv = 'G€$|$ùP!';
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if($action=='e') {
        $output='gs_en_'.base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    }
    elseif($action=='d'){
        $output=openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function DisplayMessage($type, $message)
{
	if($type=='error')
	{
		return '
		<div role="alert" class="alert alert-lg bgc-danger-l3 border-0 border-l-4 brc-danger-m1 mt-4 mb-3 pr-3 d-flex">
			<div class="flex-grow-1">
				<i class="fas fa-times mr-1 text-120 text-danger-m1"></i>
				<strong class="text-danger">'.T_('Erreur').' : '.$message.'. </strong>
			</div>
			<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
			</button>
		</div>
	';
	} 
	if($type=='success')
	{
		return '
		<div role="alert" class="alert alert-lg bgc-success-l3 border-0 border-l-4 brc-success-m1 mt-4 mb-3 pr-3 d-flex">
				<div class="flex-grow-1">
					<i class="fas fa-check mr-1 text-120 text-success-m1"></i>
					<strong class="text-success">'.$message.'</strong>
				</div>
				<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
				</button>
			</div>
			';
		}
		if($type=='info')
		{
			return '
			<div role="alert" class="alert alert-lg bgc-info-l3 border-0 border-l-4 brc-info-m1 mt-4 mb-3 pr-3 d-flex">
				<div class="flex-grow-1">
					<i class="fas fa-info-circle mr-1 text-120 text-info-m1"></i>
					<strong class="text-info">'.$message.'</strong>
				</div>
				<button type="button" class="close align-self-start" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times text-80"></i></span>
				</button>
			</div>
			';
		}
	}
?>