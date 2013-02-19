<?php 
include_once('../../config.php');
$client_id=$_REQUEST['client_id'];
$po_id=$_REQUEST['po_id'];
$bom_id=$_REQUEST['bom_id'];
$po_uid=getPoUidFrmId($po_id);

$q="INSERT into bom_booking(
		bom_booking_po_id,
		bom_booking_po_uid,
		bom_booking_bom_id,
		bom_booking_client_id,
		bom_booking_by_user_id,
		bom_booking_status,
		bom_booking_datetime
	)value(
		'$po_id',
		'$po_uid',
		'$bom_id',
		'$client_id',
		'".$_SESSION[current_user_id]."',
		'Booking Approved',
		now()
	)
";
$r=mysql_query($q)or die(mysql_error()."<br/><b>Query:</b>$q<br/><br/>");
header("location:../../purchaseorder_bom_order_sheet.php?param=view&client_id=$client_id&po_id=$po_id&bom_id=$bom_id&alert=booked_success");
?>