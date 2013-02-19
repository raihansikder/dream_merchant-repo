<?php
/*
 *	Basic client configuration
*/
$app_name = "Dream Merchant - local";
$app_version = "v1.0";
/*
 *	Basic client configuration
*/
$company_name = "Horizon Fashion Wear";
$company_slogan = "Horizon";
$company_intro = "";
$sitename="Horizon Fashion";
$company_shortcode="HFWL";
/***********************************/
/*
 *	Basic site setup
*/
$scriptpath='http://localhost/activation/dreammerchant/layouts/site'; //office testing script path
//$scriptpath='174.120.107.7/~dreammer/main/app'; //office testing script path without '/' at the end
$tmpDir='temp';
/***********************************/
/*
 *	Database information and connection
*/

$dbhost='localhost';
$dbuser='dreammer_main';
$dbpass='activation';
$dbname='dreammer_main';
mysql_select_db($dbname,mysql_connect($dbhost, $dbuser, $dbpass));
/***********************************/
/*
$dbhost='localhost';
$dbuser='dreammer_dev1';
$dbpass='activation';
$dbname='dreammer_dev1';
mysql_select_db($dbname,mysql_connect($dbhost, $dbuser, $dbpass));
/***********************************/
/*
 *	file upload parameters
*/
$max_upload_img_size=10000000; // maximum image upload size in kb
$img_upload_types=array("images/jpeg","images/gif","images/pjpeg","images/jpg","images/png");
/***********************************/
$month_arr=array('month','jan','feb','mar','apr','may','jun','july','aug','sep','oct','nov','dec');

/*
 * initiate PHPMailer
*/
require_once("phpmailer/class.phpmailer.php");
$mail = new PHPMailer();
$mail->IsSMTP(); // send via SMTP
$mail->Host = "ssl://smtp.gmail.com";
$mail->Port = 465;
$mail->SMTPAuth = true; // turn on SMTP authentication
$mail->Username = "horizon.fashion.wear@gmail.com"; // SMTP username
$mail->Password = "dreammerchant"; // SMTP password
//$webmaster_email = "username@doamin.com"; //Reply to this email ID
//$email="spider.xy@gmail.com"; // Recipients email ID
//$name="name"; // Recipient's name
$mail->From = "horizon.fashion.wear@gmail.com";
$mail->FromName = "Horizon Fashion Wear";
$mail->AddReplyTo($_SESSION[current_user_email],$_SESSION[current_user_fullname]);
$mail->WordWrap = 50; // set word wrap
//$mail->AddAttachment("D:/a.txt"); // attachment
//$mail->AddAttachment("/tmp/image.jpg", "new.jpg"); // attachment
$mail->IsHTML(true); // send as HTML
/***********************************************/

?>