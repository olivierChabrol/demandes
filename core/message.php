<?php
################################################################################
# @Name : /core/message.php
# @Description : page to send mail
# @Call : /core/auto_mail.php
# @parameters : $from, $to, $message, $object
# @Author : Flox
# @Create : 21/11/2012
# @Update : 11/09/2020
# @Version : 3.2.4
################################################################################

//init var
$mail_send=1;

//functions
require_once(__DIR__.'/../core/functions.php');

//load mailer
require_once(__DIR__.'/../components/PHPMailer/src/PHPMailer.php');
require_once(__DIR__.'/../components/PHPMailer/src/SMTP.php');
require_once(__DIR__.'/../components/PHPMailer/src/Exception.php');
$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
	$mail->CharSet = 'UTF-8'; //ISO-8859-1 possible if string problems
	if($rparameters['mail_smtp_class']=='IsSendMail()') {$mail->IsSendMail();} else {$mail->IsSMTP();}
	if($rparameters['mail_secure']=='SSL')
	{$mail->Host = "ssl://$rparameters[mail_smtp]";}
	elseif($rparameters['mail_secure']=='TLS')
	{$mail->Host = "tls://$rparameters[mail_smtp]";}
	else
	{$mail->Host = "$rparameters[mail_smtp]";}
	$mail->SMTPAuth = $rparameters['mail_auth'];
	if($rparameters['debug']) {$mail->SMTPDebug = 4;}
	if($rparameters['mail_secure']!=0) {$mail->SMTPSecure = $rparameters['mail_secure'];}
	if($rparameters['mail_port']!=25) {$mail->Port = $rparameters['mail_port'];}
	$mail->Username = "$rparameters[mail_username]";
	if(preg_match('/gs_en/',$rparameters['mail_password'])) {$rparameters['mail_password']=gs_crypt($rparameters['mail_password'], 'd' , $rparameters['server_private_key']);}
	$mail->Password = $rparameters['mail_password'];
	$mail->IsHTML(true);
	$mail->Timeout = 30;
	$mail->From = $from;
	$mail->FromName = $rparameters['mail_from_name'];
	$mail->XMailer = ' ';

	//multi address case
	if(preg_match('#;#',$to))
	{
		$to=explode(';',$to);
		foreach ($to as &$mailadr) {if($mailadr){$mail->AddAddress("$mailadr");}}
	} else { $mail->AddAddress("$to");}

	if($from){$mail->AddReplyTo($from);}
	$mail->Subject = "$object";
	if ($rparameters['mail_ssl_check']==0)
	{
		$mail->smtpConnect([
		'ssl' => [
			'verify_peer' => false,
			'verify_peer_name' => false,
			'allow_self_signed' => true
			]
		]);
	}
	$mail->Body = "$message";
	$mail->send();
	$mail->SmtpClose();
} catch (Exception $e) {
	echo DisplayMessage('error',T_('Message non envoyé, vérifier les paramètres de votre connecteur SMTP').'('.$mail->ErrorInfo.')');
	//log
	if($rparameters['log']) {logit('error', $mail->ErrorInfo,$_SESSION['user_id']);}
	$mail_send=0;
}
?>
