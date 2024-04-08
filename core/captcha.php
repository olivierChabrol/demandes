<?php
################################################################################
# @Name : captcha.php
# @Description : generate captcha image for user subscription
# @Call : ./register.php
# @Parameters :
# @Author : Flox
# @Create : 04/12/2019
# @Update : 04/12/2019
# @Version : 3.1.48
################################################################################
session_start();
header('Content-Type: image/png');
$width=80;
$height=25;
$lines=10;
$characters="ABCDEF123456789";
$image = imagecreatetruecolor($width, $height);
imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 255, 255, 255));
function hexargb($hex) {
    return array("r"=>hexdec(substr($hex,0,2)),"g"=>hexdec(substr($hex,2,2)),"b"=>hexdec(substr($hex,4,2)));
}
for($i=0;$i<=$lines;$i++){
    $rgb=hexargb(substr(str_shuffle("ABCDEF0123456789"),0,6));
    imageline($image,rand(1,$width-25),rand(1,$height),rand(1,$width+25),rand(1,$height),imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']));
}
$code1=substr(str_shuffle($characters),0,4);
$_SESSION['code']=$code1;
$code="";
for($i=0;$i<=strlen($code1);$i++){
    $code .=substr($code1,$i,1)." ";
}
imagestring($image, 5, 10, 5, $code, imagecolorallocate($image, 0, 0, 0));
imagepng($image);
imagedestroy($image);
?>