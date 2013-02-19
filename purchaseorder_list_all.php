<?php
include_once('config.php');
include_once("class.pagination.php");

$r=mysql_query("
		select * from purchaseorder s1 where
		po_prepared_date=
		(select max(s2.po_prepared_date) from purchaseorder s2
		where s1.po_uid=s2.po_uid);")or die(mysql_error());

$rows=mysql_num_rows($r);
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include('inc.head.php');?>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div id="top1">
				<?php include('top.php');?>
			</div>
			<div id="mid">
				<h2>All purchase order</h2>
				<div class="alert">
					<?php printAlert($valid,$alert);?>
				</div>
				<form method="post" action="proforma_invoice_add.php">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
						<thead>
							<tr>
								<td>OCN</td>
								<td>client</td>
								<td>PO No</td>
								<td>Style No</td>
								<td>PO category</td>
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
								<td>
									<?php echo  "HWFL/OCN/".$arr[$i][po_id];?>
								</td>
								<td>
									<?php echo  getClientCompanyNameFrmId($arr[$i][po_client_id]);?>
								</td>
								<td>
									<span class='po_idname'> <?php echo $arr[$i][po_no] ;?>
									</span>
								</td>
								<td>
									<?php echo $arr[$i][po_style_no];?>
								</td>
								<td>
									<?php
									echo getPoCatNameFrmId($arr[$i]['po_category_id']);
									?>
								</td>
								<td>
									<?php echo getTotalQuantityFrmPoId($arr[$i][po_id]); ?>
								</td>
								<td>
									<?php echo $arr[$i][po_received_date];?>
								</td>
								<td>
									<?php echo $arr[$i][po_shipment_date];?>
								</td>
								<td>
									<?php echo getUserNameFrmId($arr[$i][po_prepared_by]);?>
								</td>
								<td>
									<a href="purchaseorder_add.php?po_id=<?php echo $arr[$i][po_id];?>&param=view&client_id=<?php echo $arr[$i][po_client_id]; ?>">view</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<!-- Selecting the purchase order shows the following button-->
					<input id="pi_generate_button" class="button bgblue" type="submit" name="submit" value="Generate Proforma invoice" style="display: none;" />
					<input name="client_id" type="hidden" value="<?php echo $client_id;?>" />
					<!--
            The following div holds the list of hidden input that is created by checking the checkboxes to send costsheet through e-mail.
        -->
					<div class="hidden_input_field"></div>
					<!---->
				</form>
			</div>
			<div id="footer">
				<?php include('footer.php');?>
			</div>
		</div>
	</div>
</body>
</html>
