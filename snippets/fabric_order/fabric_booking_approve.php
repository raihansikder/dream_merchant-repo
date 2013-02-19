<?php 
include_once('../../config.php');
$client_id=$_REQUEST['client_id'];
$po_id=$_REQUEST['po_id'];
$po_uid=getPoUidFrmId($po_id);

$q="INSERT into fabric_booking(
		fabric_booking_po_id,
		fabric_booking_po_uid,
		fabric_booking_client_id,
		fabric_booking_by_user_id,
		fabric_booking_status,
		fabric_booking_datetime
	)value(
		'$po_id',
		'$po_uid',
		'$client_id',
		'".$_SESSION[current_user_id]."',
		'Booking Approved',
		now()
	)
";
$r=mysql_query($q)or die(mysql_error()."<br/><b>Query:</b>$q<br/><br/>");
header("location:../../fabric_order_sheet.php?param=view&client_id=$client_id&po_id=$po_id&alert=booked_success");
?>