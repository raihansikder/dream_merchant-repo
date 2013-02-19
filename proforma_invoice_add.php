<?php
include('config.php');
$valid=true;
$alert=array();
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
//myprint_r($_SERVER);
//echo getcwd() ;
//myprint_r($_REQUEST);
//echo $po_ids;

if($param=='add'){
	if(isset($_POST[submit])){
		$exception_field=array('submit','param','client_id','pi_beneficiary_select','pi_bank_details_select');
		/*
		 *	server side validation TODO
		*/

		/*************************************/
		if($valid){
			if($param=='add'){
				/*
				 *	Check whether current user has permission to add user
				*/
				if(hasPermission('purchaseorder','add',$_SESSION[current_user_id])){ // TODO : need to update permission table
					/*
					 *	Create the insert query substring.
					*/
					$str=createMySqlInsertString($_POST,$exception_field);
					$str_k=$str['k'];
					$str_v=$str['v'];
					/*************************************/
					$q="INSERT INTO proforma_invoice($str_k,pi_created_datetime,pi_created_by) values ($str_v,now(),'".$_SESSION[current_user_id]."')";
					mysql_query($q) or die(mysql_error());
					$pi_id= mysql_insert_id();
					$param='view';
					array_push($alert,"The Proforma invoice has been saved!");

					$q="Select * from proforma_invoice where pi_id='$pi_id'";
					//echo $q;
					$r=mysql_query($q)or die(mysql_error());
					if(mysql_num_rows($r)){
						$a=mysql_fetch_assoc($r);
					}

					$po_ids=$a[pi_po_ids];

				}else{
					$valid=false;
					array_push($alert,"You don't have permission to add user");
				}
			}
			//echo $sql;
		}
	}
}else if($param=='view'){
	$pi_id=$_REQUEST[pi_id];
	$q="Select * from proforma_invoice where pi_id='$pi_id'";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
	}
	$po_ids=$a[pi_po_ids];
}else{
	$po_ids=trim(implode(',',$_POST[po_id]),', ');
}
$r=mysql_query("Select * from purchaseorder where po_id in($po_ids)")or die(mysql_error());
$rows=mysql_num_rows($r);
if($rows>0){
	$a_po=mysql_fetch_rowsarr($r);
}




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php");?>
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
					- Proforma Invoice
				</h2>
				<div id="client_menu">
					<?php include('snippets/client/clientmenu.php');?>
				</div>
				<?php if(strlen($pi_id)){?>
				<div class='add_button_large'>
					<a target="_blank" href="proforma_invoice_pdf.php?<?php echo "pi_id=$pi_id&client_id=$client_id&request_type=download";?>"> <img src="images/pdf-logo.png" /> Download as PDF
					</a>
				</div>
				<div class='add_button_large'>
					<a target="_blank" href="proforma_invoice_pdf.php?<?php echo "pi_id=$pi_id&client_id=$client_id&request_type=email";?>"> Email PDF</a>
				</div>
				<?php } ?>
				<div class="alert">
					<?php if(isset($_POST[submit])){
						printAlert($valid,$alert);
					} ?>
				</div>
				<form id="pi_form" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
					<input name="param" type="hidden" value="add" />
					<input name="pi_po_ids" type="hidden" value="<?php echo $po_ids?>" />
					<input name="pi_client_id" type="hidden" value="<?php echo $client_id?>" />
					<input name="client_id" type="hidden" value="<?php echo $client_id?>" />
					<div class="clear" style="padding: 20px"></div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="50%">
								<img src="images/company_logo.png" width="250px" height="80px" />
							</td>
							<td>
								<?=$office_address?>
							</td>
						</tr>
					</table>
					<h2 style="text-align: center;">PROFORMA INVOICE</h2>
					<table width="100%" cellpadding="2" cellspacing="0" border="1">
						<tr>
							<td width="50%">
								<b> <span class="left">P.I.NO:</span> <input name="pi_no" type="text" value="<?php
				 if($param=='edit'){echo addEditInputField('pi_no');}
				 else echo generatePiNo($client_id);
				 ?>" size="30" maxlength="30" class="validate[required]" readonly="readonly" />
								</b>
							</td>
							<td>
								<b>Date:</b>
								<?php echo date("F j, Y");?>
							</td>
						</tr>
						<tr>
							<td>
								<span class="left">TO:&nbsp;&nbsp;</span> <textarea name="pi_to" cols="50" rows="5" readonly="readonly"><?php
								if($param=='edit'){
									echo addEditInputField('pi_to');
								}
								else echo getClientAddressFrmId($client_id);?></textarea>
							</td>
							<td>
								<span class="left"> <b>Beneficiary:&nbsp;&nbsp;</b>
								</span>
								<?php
								if($_POST[submit]=='Generate Proforma invoice'){
									//$selectedId=addEditInputField('pi_beneficiary_select');
									$customQuery = " WHERE beneficiary_active='1' ";
									createSelectOptions('beneficiary','beneficiary_id','beneficiary_name',$customQuery,$selectedId,'pi_beneficiary_select',"class='validate[required]'");

								}
								?>
								<span id="beneficiary_loader"></span> <textarea name="pi_beneficiary" cols="50" rows="5"><?php echo addEditInputField('pi_beneficiary');?>
								</textarea>
								<script>
              	$('select[name=pi_beneficiary_select]').change(function(){
					var loadUrl="snippets/proforma_invoice/pi_ajax_beneficiary_details.php";
					var ajax_load="<img src='images/ajax-loader-1.gif' class='loading'  alt='loading...' />";
					var val = $(this).val();
					$("#beneficiary_loader").html(ajax_load);
					$.ajax({
					  type: "POST",
					  url: loadUrl,
					  data: { beneficiary_id: val}
					}).done(function( msg ) {
					  //alert( "Data Saved: " + msg );
					  $('#beneficiary_loader').empty();
					  $('textarea[name=pi_beneficiary]').html(msg);
					});
				})
              </script>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<span class="left"> <b>Bank Details:&nbsp;&nbsp;</b>
								</span>
								<?php
								if($_POST[submit]=='Generate Proforma invoice'){
									//$selectedId=addEditInputField('pi_beneficiary_select');
									$customQuery = " WHERE bank_details_active='1' ";
									createSelectOptions('bank_details','bank_details_id','bank_details_name',$customQuery,$selectedId,'pi_bank_details_select',"class='validate[required]'");

								}
								?>
								<span id="bank_details_loader"></span><textarea name="pi_bank_details" cols="50" rows="5"><?php echo addEditInputField('pi_bank_details');?></textarea>
								<script>
								$('select[name=pi_bank_details_select]').change(function(){
									var loadUrl="snippets/proforma_invoice/pi_ajax_bank_details.php";
									var ajax_load="<img src='images/ajax-loader-1.gif' class='loading'  alt='loading...' />";
									var val = $(this).val();
									$("#bank_details_loader").html(ajax_load);
									$.ajax({
									  type: "POST",
									  url: loadUrl,
									  data: { bank_details_id: val}
									}).done(function( msg ) {
									  //alert( "Data Saved: " + msg );
									  $('#bank_details_loader').empty();
									  $('textarea[name=pi_bank_details]').html(msg);
									});
								})
							  </script>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<div style="padding: 15px 0px; display: block;">We do hereby confirm selling following merchandise under the terms and condition stated below:</div>
					<table width="100%" cellpadding="0" cellspacing="0" border="1">
						<tr class="bold">
							<td>SL#</td>
							<td>PO#</td>
							<td>Style#</td>
							<td>Description</td>
							<td>Fabrication</td>
							<td>Size</td>
							<td>Total Qty Pc</td>
							<td>
								Unit Price <br /> PC in $
							</td>
							<td>Total pric($)</td>
						</tr>
						<?php
						$count=1;
						$grandTotalQtyPc=0;
						$grandTotalPrice=0;
						foreach($a_po as $po){
							$totalQtyPc=totalQtyFromPoId($po['po_id']);
							$totalPrice=$po['po_unit_price']*$totalQtyPc;
							echo "<tr>";
							echo "<td>".$count.".</td>";
							echo "<td>".$po['po_no']."</td>";
							echo "<td>".$po['po_style_no']."</td>";
							echo "<td>".$po['po_item_description']."</td>";
							echo "<td>".$po['po_fabric_composition']."</td>";
							echo "<td>".quantityDetailsSizeListCsv($po['po_id'])."</td>";
							echo "<td>".$totalQtyPc."</td>";
							echo "<td>".$po['po_unit_price']."</td>";
							echo "<td>".$totalPrice."</td>";
							echo "</tr>";
							$grandTotalQtyPc+=$totalQtyPc;
							$grandTotalPrice+=$totalPrice;
							$count++;
			}?>
						<tr class="bold">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>
								<?php echo $grandTotalQtyPc; ?>
							</td>
							<td>&nbsp;</td>
							<td>
								<?php echo $grandTotalPrice; ?>
							</td>
						</tr>
					</table>
					<div class="clear" style="padding: 20px 0px">
						Total in word: <b> <?php echo ucfirst(convert_number_to_words($grandTotalPrice));?> USD only
						</b>
					</div>
					<table width="100%" cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td width="27%">
								<b>Terms &amp; Conditions</b>
							</td>
							<td width="73%">&nbsp;</td>
						</tr>
						<tr>
							<td>01. Terms of Payment</td>
							<td>
								<?php
								if($param!='view'){
									$selectedId=addEditInputField('pi_pm_id');
									$customQuery = " WHERE payment_method_active='1' ";
									createSelectOptions('payment_method','payment_method_id','payment_method_name',$customQuery,$selectedId,'pi_pm_id',"class='validate[required] pi_pm' style='float:none;'");
								}else{
									echo paymentMethodFrmID(addEditInputField('pi_pm_id'));
								}
								?>
							</td>
						</tr>
						<tr>
							<td>02. Shipment Terms</td>
							<td>
								<?php
								if($param!='view'){
									$selectedId=addEditInputField('pi_st_id');
									$customQuery = " WHERE st_active='1' ";
									createSelectOptions('shipping_term','st_id','st_name',$customQuery,$selectedId,'pi_st_id',"class='validate[required] pi_st' style='float:none;'");
								}else{
									echo shippingTermFrmID(addEditInputField('pi_st_id'));
								}
								?>
								<script>
              	$('select[name=pi_st_id]').change(function(){
					var loadUrl="snippets/proforma_invoice/pi_ajax_port_of_loading.php";
					var ajax_load="<img src='images/ajax-loader-1.gif' class='loading'  alt='loading...' />";
					var val = $(this).val();
					$("#port_of_loading_loader").html(ajax_load);
					$.ajax({
					  type: "POST",
					  url: loadUrl,
					  data: { st_id: val}
					}).done(function( msg ) {
					  //alert( "Data Saved: " + msg );
					  $('.loading').hide();
					  $('input[name=pi_port_of_loading]').attr('value',msg);
					  //$('#port_of_loading_loader').html(msg);
					});
				})
              </script>
							</td>
						</tr>
						<tr>
							<td>03. Port of Loading</td>
							<td>
								<input name="pi_port_of_loading" type="text" value="<?php
				 echo addEditInputField('pi_port_of_loading');
				 ?>" size="60" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td>04. Port of Delivery</td>
							<td>
								<input name="pi_port_of_delivery" type="text" value="<?php
				 echo addEditInputField('pi_port_of_delivery');
				 ?>" size="60" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td>05. Port of Discharges</td>
							<td>
								<input name="pi_port_of_discharge" type="text" value="<?php
				echo addEditInputField('pi_port_of_discharge');
				 ?>" size="60" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td>06. Final Destination</td>
							<td>
								<input name="pi_final_destination" type="text" value="<?php
				 echo addEditInputField('pi_final_destination');
				 ?>" size="60" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<!--
            <tr>
              <td>07. Last Shipment Date</td>
              <td><input id="datepicker" name="pi_latest_ship_date" type="text" value="<?php echo addEditInputField('pi_latest_ship_date');?>" class="validate[required]" /></td>
            </tr>
            -->
						<tr>
							<td onmousedown="">08. Expiry Place &amp; Date</td>
							<td>
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td>
											<input name="pi_expire_place" type="text" value="<?php echo addEditInputField('pi_expire_place');?>" size="30" maxlength="40" class="validate[required]" />
											<span class="small">(place)</span>
										</td>
										<td>
											<input id="pi_expire_date" name="pi_expire_date" type="text" value="<?php echo addEditInputField('pi_expire_date');?>" class="validate[required]" size="12" readonly="readonly" />
											<span class="small">(yy-mm-dd)</span>
											<script>
			  $("#pi_expire_date").datepicker({ dateFormat: "yy-mm-dd" });
			  </script>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>09. Tolerance (+/-) 5%</td>
							<td>Value &amp; Quantity</td>
						</tr>
						<tr>
							<td>10. Partial Shipment</td>
							<td>
								<?php
								if($_POST[submit]=='Generate Proforma invoice'){
									//$selectedId=addEditInputField('pi_beneficiary_select');
									$customQuery = " WHERE pso_active='1' ";
									createSelectOptions('partial_shipment_options','pso_name','pso_name',$customQuery,$selectedId,'pi_partial_shipment',"class='validate[required]'");

								}else{
									echo addEditInputField('pi_partial_shipment');;
								}
								?>
							</td>
						</tr>
						<tr>
							<td>11. Trans Shipment</td>
							<td>Allowed</td>
						</tr>
						<tr>
							<td>12. Period of Presentation</td>
							<td>15 Days</td>
						</tr>
						<tr>
							<td>13. Documents Made out</td>
							<td>In English Language</td>
						</tr>
					</table>
					<br />
					<?php if(!strlen($pi_id)){?>
					<input class="button bgblue" type="submit" name="submit" value="Save" />
					<?php } ?>
					<a href="proforma_invoice_list.php?client_id=<?php echo $client_id;?>" class='button bgblue'>Back</a>
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
