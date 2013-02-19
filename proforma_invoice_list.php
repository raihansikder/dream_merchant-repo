<?php
include('config.php');
include_once("class.pagination.php");
$client_id=$_REQUEST[client_id];

//$r=mysql_query("Select * from purchaseorder where po_client_id='$client_id' and po_active='1' order by po_prepared_date desc ")or die(mysql_error());

$r=mysql_query("
		select * from proforma_invoice s1
		where pi_client_id='$client_id' ")or die(mysql_error());

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
				<h2>
					<?php echo getClientCompanyNameFrmId($client_id); ?>
				</h2>
				<?php include('snippets/client/clientmenu.php');?>
				<!--<div class='add_button_large'><a href="purchaseorder_add.php?param=add&client_id=<?php echo $client_id;?>"><img src="images/plus.png"/>Add new purchase order</a></div>-->
				<div class="alert">
					<?php printAlert($valid,$alert);?>
				</div>
				<form method="post" action="purchaseorder_proforma_invoice_generate.php">
					<input name="client_id" type="hidden" value="<?php echo $client_id;?>">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
							<thead>
								<tr>
									<td width="64">Prepared Date</td>
									<td width="132">Pi#</td>
									<td width="132">Shipping date</td>
									<td width="85">Prepared By</td>
									<td width="85">Action</td>
								</tr>
							</thead>
							<tbody>
								<?php for($i=0;$i<$rows;$i++){?>
								<tr>
									<td>
										<?php echo $arr[$i][pi_created_datetime];?>
									</td>
									<td>
										<?php echo $arr[$i][pi_no];?>
									</td>
									<td>
										<?php echo $arr[$i][pi_latest_ship_date];?>
									</td>
									<td>
										<?php echo getUserNameFrmId($arr[$i][pi_created_by]);?>
									</td>
									<td>
										<a href="proforma_invoice_add.php?pi_id=<?php echo $arr[$i][pi_id];?>&param=view&client_id=<?php echo $client_id; ?>">view</a>
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
						<!-- Selecting the purchase order shows the following button-->
						<input id="pi_generate_button" class="button bgblue" type="submit" name="submit" value="Generate Proforma invoice" style="display: none;" />
						<input name="client_id" type="hidden" value="<?php echo $client_id;?>">
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
