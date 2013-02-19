<?php
include('config.php');
include_once("class.pagination.php");
$client_id=$_REQUEST[client_id];

//$r=mysql_query("Select * from purchaseorder where po_client_id='$client_id' and po_active='1' order by po_prepared_date desc ")or die(mysql_error());

$r=mysql_query("
		select * from purchaseorder s1
		where po_client_id='$client_id' and
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
<script>
$('document').ready(function(){
	countChecked();
	$(":checkbox").click(function(){
		if(countChecked()){
			$('input[id=pi_generate_button]').show();
		}else{
			$('input[id=pi_generate_button]').hide();
		}
	});
	
});
</script>
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
				<div class='add_button_large'>
					<a href="purchaseorder_add.php?param=add&client_id=<?php echo $client_id;?>"> <img src="images/plus.png" /> Add new purchase order
					</a>
				</div>
				<div class="alert">
					<?php printAlert($valid,$alert);?>
				</div>
				<form method="post" action="proforma_invoice_add.php">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
						<thead>
							<tr>
								<td>OCN</td>
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
								<td>
									<?php echo  "HWFL/OCN/".$arr[$i][po_id];?>
								</td>
								<td>
									<span class="pocheckbox"> <?php if(hasPermission('purchaseorder','add',$_SESSION[current_user_id])){?> <input name="po_id_selector[]" class="selector_<?php echo $arr[$i][po_id]?>" type="checkbox" value="<?php echo $arr[$i][po_id]?>" /> <script>
                    $('input[class=<?php echo "selector_".$arr[$i][po_id]; ?>]').click(function(){
                        var po_id=$(this).val();
                        var exists=$("input[class='temp_"+po_id+"']").length;
                        if (exists!=0){
                            $("input[class=temp_"+po_id+"]").remove();
                        }else{
                            $('div[class=hidden_input_field]').append("<input name='po_id[]' type='hidden' class='temp_"+po_id+"' value='"+po_id+"'/>");
                        }
						if(countChecked()){
							$('input[id=pi_generate_button]').show();
						}else{
							$('input[id=pi_generate_button]').hide();
						}
                    })
                    </script> <?php } ?>
									</span> <span class='po_idname'> <?php echo $arr[$i][po_no] ;?>
									</span>
								</td>
								<td>
									<?php echo $arr[$i][po_style_no];?>
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
									<a href="purchaseorder_add.php?po_id=<?php echo $arr[$i][po_id];?>&param=view&client_id=<?php echo $client_id; ?>">view</a> | <a href="fabric_order_sheet.php?param=view&client_id=<?php echo $client_id; ?>&po_id=<?php echo$arr[$i][po_id]; ?>" target="_blank">View fabric order sheet</a>
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
