<?php 
include_once('../../config.php');
$client_id=$_REQUEST['client_id'];
$fo_id=$_REQUEST['fo_id'];
$po_id=$_REQUEST['po_id'];

$q="UPDATE fabric_order SET fo_active='0' where fo_id='$fo_id'";
$r=mysql_query($q)or die(mysql_error()."<br/><b>Query:</b>$q<br/><br/>");
header("location:../../fabric_order_sheet.php?param=view&client_id=$client_id&po_id=$po_id");	


	
?>