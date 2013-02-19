<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE );
session_start();

include_once('inc.globalvariables.php');
include_once('inc.functions.generic.php');
include_once('inc.functions.appspecific.php');

/*
 *	Following code segment deal with users landing to the system using urls with parameters.
The url is stored in session and after login user is redirected to the page where he intended to land.
*/

if(getFileName()!='login.php'){
	/* temporary code for PI invoice script to avoid session issue */
	if($_REQUEST[passcode]=='12345'){
		$_SESSION['logged']=true;
	}
	/***************************/
	if($_SESSION['logged']!=true){
		session_destroy();
		session_start();
		$str_k="";
		$exception_field=array('');
		foreach($_REQUEST as $k=>$v){
			if(!in_array($k,$exception_field)){
				if(!empty($k)){
					$str_k.="$k=".$v.'&';
				}
			}
		}
		$str_k=trim($str_k,'&');
		$_SESSION['redirect_url']=getFileName().'?'.$str_k;
		header("location:login.php");
	}
}else{
	if($_SESSION['logged']==true){
		header("location:index.php");
	}
}

?>