<?php
$sql="SELECT * from bom where bom_active='1' AND bom_id not in
		( SELECT bom_booking_bom_id from bom_booking
			WHERE bom_booking_status='Booking approved' AND bom_booking_active='1'
		)AND bom_id in
		( SELECT bom_booking_bom_id from bom_booking
			WHERE bom_booking_status='Booked' AND bom_booking_active='1'
		)";
$r=mysql_query($sql) or die(mysql_error()."<br>___<br>$sql<br>");
$bom_rows = mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($bom_rows>0) {
	$a_bom=mysql_fetch_rowsarr($r);
?>

<div class="clear"></div>
<div class="moduleBlock">
  <h2>List of BOM booking that requires admin approval</h2>
  <table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
    <thead>
      <tr>
        <td>Material</td>
        <td>Quantity/pc</td>
        <td>wastage %</td>
        <td>Total Quantity</td>
        <td>Rate/ Dozen</td>
        <td>Total Price (USD)</td>
        <td>Supplier</td>
        <td>Delivery Date</td>
        <td></td>
      </tr>
    <thead>
    <tbody>
    <?php
	if ($bom_rows > 0) {
		$i=0;
		foreach ($a_bom as $a_b){?>	
          <tr class="bom_tr" id="<?php echo $a_b['bom_id']; ?>">
            <td><?php echo $a_b["bom_id"]; ?></td>
            <td><?php echo $a_b["bom_quantity_per_pc"]; ?></td>
            <td><?php echo $a_b["bom_wastage"]; ?></td>
            <td><?php
                $total_quantity_wo_wastage=getTotalQuantityFrmPoId($a_b[bom_po_id])*$a_b["bom_quantity_per_pc"];
                $wastage=$total_quantity_wo_wastage*$a_b["bom_wastage"]/100;
                $total_quantity_with_wastage=$total_quantity_wo_wastage+$wastage;
                echo $total_quantity_with_wastage;
                ?></td>
            <td><?php echo $a_b["bom_rate_per_dozen"]; ?></td>
            <td><?php
                $total_price=$total_quantity_with_wastage*$a_b["bom_rate_per_dozen"]/12;
                echo $total_price;
                ?></td>
            <td><?php
                echo getSupplierNameFrmId($a_b['bom_supplier_id']);
                ?></td>
            <td><?php echo $a_b['bom_delivery_date']; ?></td>
            <td><a href='purchaseorder_bom_order_sheet.php?po_id=<?=$a_b[bom_po_id]?>&param=view&client_id=<?=getClientIdFrmPoId($a_b[bom_po_id])?>&bom_id=<?=$a_b['bom_id']?>'>Order sheet</a></td>
          </tr>
    <?php
		}
		$i++;
	}?>
    </tbody>
  </table>
</div>
<div class="clear"></div>
<?php }else{
	//echo "No approval request";
} ?>
