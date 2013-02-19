
<?php
$sql="SELECT * from purchaseorder where po_active='1' AND po_id not in
		( SELECT fabric_booking_po_id from fabric_booking
			WHERE fabric_booking_status='Booking approved' AND fabric_booking_active='1'
		)AND po_id in
		( SELECT fabric_booking_po_id from fabric_booking
			WHERE fabric_booking_status='Booked' AND fabric_booking_active='1'
		)";
$r=mysql_query($sql)or die(mysql_error()."<br>___<br>$sql<br>");

$rows=mysql_num_rows($r);
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);

?>

<div class="clear"></div>
<div class="moduleBlock">

<h2>List of Fabric booking that requires admin approval</h2>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
  <thead>
    <tr>
      <td>OCN</td>
      <td>client</td>
      <td>PO No</td>
      <td>Style No</td>
      <td>Total Quantity</td>
      <td>Received Date</td>
      <td>Shipment Date</td>
      <td>Prepared By</td>
      <td>Action</td>
    </tr>
  </thead>
  <tbody>
    <?php for($i=0;$i<$rows;$i++){?>
    <tr>
      <td><?php echo  "HWFL/OCN/".$arr[$i][po_id];?></td>
      <td><?php echo  getClientCompanyNameFrmId($arr[$i][po_client_id]);?></td>
      <td><span class='po_idname'> <?php echo $arr[$i][po_no] ;?> </span></td>
      <td><?php echo $arr[$i][po_style_no];?></td>
      <td><?php echo getTotalQuantityFrmPoId($arr[$i][po_id]); ?></td>
      <td><?php echo $arr[$i][po_received_date];?></td>
      <td><?php echo $arr[$i][po_shipment_date];?></td>
      <td><?php echo getUserNameFrmId($arr[$i][po_prepared_by]);?></td>
      <td><a href="fabric_order_sheet.php?po_id=<?php echo $arr[$i][po_id];?>&param=view&client_id=<?php echo $arr[$i][po_client_id]; ?>">view</a></td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<div class="clear"></div><?php }else{
	//echo "No approval request";
} ?>
