<?php
include('config.php');
$valid = true;
$alert = array();
$client_id = $_REQUEST[client_id];
$param = $_REQUEST[param];
$po_id = $_REQUEST[po_id];
$bom_id=$_REQUEST[bom_id];
if (!strlen($client_id)) {
	header('location:index.php');
}


$q="Select * from purchaseorder where po_id='$po_id'";
$r=mysql_query($q) or die(mysql_error());
$a=mysql_fetch_assoc($r);

$q="Select * from podetails where podetails_po_id='$po_id'";
$r=mysql_query($q) or die(mysql_error());
$podetails_rows = mysql_num_rows($r);
if ($podetails_rows > 0) {
	$a_podetails = mysql_fetch_rowsarr($r);
}

$q="Select * from bom where bom_po_uid='".$a[po_uid]."' AND bom_active='1' AND bom_id='$bom_id'";
$r=mysql_query($q) or die(mysql_error());
$bom_rows = mysql_num_rows($r);
if ($bom_rows > 0) {
	$a_bom = mysql_fetch_assoc($r);
}

$q="Select * from supplier where supplier_id='".$a_bom[bom_supplier_id]."' ";
$r=mysql_query($q) or die(mysql_error());
$supplier_rows = mysql_num_rows($r);
if ($supplier_rows > 0) {
	$a_supplier = mysql_fetch_assoc($r);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include_once("inc.head.php"); ?>
<script>
$('document').ready(function(){
	$("div[id=disabled_input] input").attr('readonly','readonly');
	$("div[id=disabled_input] select").attr('disabled','disabled');
});
</script>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div class="right">
				<?php
				if(hasPermission('purchaseorder','edit',$_SESSION[current_user_id]) && !newerPurchaseOrderExists($po_id) && $param=='view'){
					echo "<a href='purchaseorder_add.php?po_id=$po_id&client_id=$client_id&param=view' class='button bgblue'>Go to Purchase Order</a>";
					//echo $a[po_uid];
					//echo BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id);
					if(!BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id)){
						echo "<a href='snippets/bom_order/bom_book.php?po_id=$po_id&client_id=$client_id&bom_id=$bom_id' class='button bgblue'>Book</a>";
					}else{
						if(BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id)=='Booked'){
							echo "<span class='button bgred'>".BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id)."</span>";
							if(hasPermission('purchaseorder','edit',$_SESSION[current_user_id])){
								echo "<a href='snippets/bom_order/bom_booking_approve.php?po_id=$po_id&client_id=$client_id&bom_id=$bom_id' class='button bgblue'>Approve</a>";
							}
						}else if(BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id)=='Booking approved'){
							echo "<span class='button bgred'>".BomBookingStatusAgainstPoUidBomId($a[po_uid],$bom_id)."</span>";
						}
						echo "<a href='#' class='button bgblue' onClick='window.print()'>Print</a>";
					}
				}
				?>
			</div>
			<div class="clear"></div>
			<table>
				<tr>
					<td align="left" valign="top">
						<img src="images/company_logo.png" width="200" />
					</td>
					<td align="left" valign="top">
						<h2>Accessories Purchase Order</h2>
					</td>
					<td align="right" valign="top">
						<span class="small"><?=$office_address?> </span>
					</td>
				</tr>
			</table>
			<div class="clear"></div>
			<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="25%">Accessories_purchase_order_no</td>
					<td width="368">
						:
						<?php echo "$company_shortcode/".strtoupper(substr(getClientCompanyNameFrmId($client_id),0,5))."/W".sprintf("%05d",$bom_id)."/". date('y',strtotime($a[po_prepared_date])); ?>
					</td>
				</tr>
				<tr>
					<td>supplier_company_name</td>
					<td>
						:
						<?=$a_supplier[supplier_company_name]?>
					</td>
				</tr>
				<tr>
					<td>supplier_address</td>
					<td>
						:
						<?=$a_supplier[supplier_address]?>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>Attention</td>
					<td>
						:
						<?=$a_supplier[supplier_contact_name]?>
					</td>
				</tr>
				<tr>
					<td>Subject</td>
					<td>
						: Work Order for <b><?=$a_bom[bom_material]?> </b>
					</td>
				</tr>
			</table>
			<div class="clear"></div>
			<?php
			if ($podetails_rows > 0) {
				$quantity_details_array=array();
				$i=1;
				foreach ($a_podetails as $a_p) {

					$total_row=0;
					for($j=1; $j<=10; $j++){
						if(($param=='view' && $a['po_quantity_size'.$j.'_id']) || $param=="edit" || $param=="add" ){
							$quantity_details_array[$i][$j]=$a_p["podetails_".$j];
							$total_row+=$a_p["podetails_".$j];
						}else{
							$quantity_details_array[$i][$j]=0;
						}
					}

					$i++;
				}
			}
			//myprint_r($quantity_details_array);
			$grand_total=0;
			for($m=1; $m<=10; $m++){
				if($a['po_quantity_size'.$m.'_id']){
					$total_col=0;
					//echo "podetails_rows:".$podetails_rows."</br>";
					for($n=1; $n<=$podetails_rows; $n++){
						$total_col+=$quantity_details_array[$n][$m];
					}
					$grand_total+=$total_col;
				}
			}
			?>
			<?php if($param!='add'){?>
			<div id="billofmaterials">
				<!-- BILL OF MATERIALS TABLE -->
				<br /> <br /> <br />
				<div>
					Dear Sir,<br /> Please arrange to supply the described goods as per following terms and conditions: <br />
				</div>
				<div id="disabled_input">
					<table id="" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr style="font-weight: bold; background: #E6E6E6">
							<td width="33%">Material</td>
							<td width="19%">Quantity/pc</td>
							<td width="17%">wastage %</td>
							<td width="25%">Total Quantity</td>
							<td width="6%">Rate/ Dozen</td>
							<td width="6%">Total Price (USD)</td>
							<td width="6%">Supplier</td>
							<td width="6%">Delivery Date</td>
							<td width="6%">&nbsp;</td>
						</tr>
						<tr class="bom_tr" id="<?php echo $a_bom['bom_id']; ?>">
							<td>
								<input class="autocomplete_material" id="<?php echo $a_bom["bom_id"]; ?>" name="bom_material[]" value="<?php echo $a_bom["bom_material"]; ?>" />
							</td>
							<td>
								<input type="text" name="bom_quantity_per_pc[]" size="8" maxlength="8" value="<?php echo $a_bom["bom_quantity_per_pc"]; ?>" />
							</td>
							<td>
								<input type="text" name="bom_wastage[]" size="8" maxlength="8" value="<?php echo $a_bom["bom_wastage"]; ?>" />
							</td>
							<td>
								<?php
								$total_quantity_wo_wastage=$grand_total*$a_bom["bom_quantity_per_pc"];
								$wastage=$total_quantity_wo_wastage*$a_bom["bom_wastage"]/100;
								$total_quantity_with_wastage=$total_quantity_wo_wastage+$wastage;
								echo $total_quantity_with_wastage;
								?>
							</td>
							<td>
								<input type="text" name="bom_rate_per_dozen[]" size="8" maxlength="8" value="<?php echo $a_bom["bom_rate_per_dozen"]; ?>" />
							</td>
							<td>
								<?php
								$total_price=$total_quantity_with_wastage*$a_bom["bom_rate_per_dozen"]/12;
								echo $total_price;
								?>
							</td>
							<td>
								<?php
								$selectedId = $a_bom['bom_supplier_id'];
								$customQuery = " WHERE supplier_active='1' ";
								createSelectOptions('supplier', 'supplier_id', 'supplier_company_name', $customQuery, $selectedId, 'bom_supplier_id[]', "class='validate[required]'");
								?>
							</td>
							<td>
								<input name="bom_delivery_date[]" id="bom_delivery_date_<?php $random_tr_id=makeRandomKey();echo $random_tr_id; ?>" type="text" value="<?php echo $a_bom['bom_delivery_date']; ?>" size="15" />
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<?php } ?>
				</div>
				<h2>Terms and Conditions:</h2>
				<ol style="list-style: decimal; margin-left: 20px;">
					<li>Goods must be delivered within <b><?php echo date('d-M-Y',strtotime($a_bom['bom_delivery_date'])); ?> </b>
					</li>
					<li>Goods must be free from faulty</li>
					<li>Management has the right to reject or accept faulty partly goods or cancel the order for any variations of goods.</li>
					<li>Bill to be supported by the received copy delivery Challan.</li>
					<li>Code No Mentioned in the work order must be mentioned in the bill.</li>
					<li>Payment will be made through L/C or A/C payee cheque subject to receive the proper.</li>
				</ol>
				<br /> <br /> <br />
				<table width="100%" border="1">
					<tr>
						<td>Prepared by (signature)</td>
						<td>Checked by (signature)</td>
						<td>MD/Director (signature)</td>
					</tr>
					<tr>
						<td>
							<br /> <br /> <br />
						</td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php'); ?>
		</div>
	</div>
</body>
</html>
<script type="text/javascript">
$('document').ready(function(){
	$(".disabled_input input").attr('disabled','disabled');
	<?php if($a[po_quantity_finalized]=='1'){?>
	$(".quantityInput").attr('readonly','readonly');
	<?php }?>

});
</script>
