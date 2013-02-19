<?php
include('config.php');

$valid=true;
$alert=array();
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$po_id=$_REQUEST[po_id];
/*
 if(!strlen($client_id)){
header('location:index.php');
}
*/

$r=mysql_query("Select * from purchaseorder where po_id='$po_id'")or die(mysql_error());
$a=mysql_fetch_assoc($r);

$r=mysql_query("Select * from podetails where podetails_po_id='$po_id' ")or die(mysql_error());
$podetails_rows=mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($podetails_rows > 0){
	$a_podetails=mysql_fetch_rowsarr($r);
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php");?>
<script type="text/javascript">
$('document').ready(function(){
	$("div[id=disabled_input] input").attr('readonly','readonly');
	$("div[id=disabled_input] select").attr('disabled','disabled');
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
				<div id="client_menu">
					<?php
					include('snippets/client/clientmenu.php');
					?>
				</div>
				<h2>
					Client:
					<?php echo getClientCompanyNameFrmId($client_id);?>
					-
					<?php echo ucfirst($param);?>
					Fabrict Order
				</h2>
				<div class="alert">
					<?php printAlert($valid,$alert); ?>
				</div>
				<div class="right">
					<?php
					if(hasPermission('purchaseorder','edit',$_SESSION[current_user_id]) && !newerPurchaseOrderExists($po_id) && $param=='view'){
						echo "<a href='purchaseorder_add.php?po_id=$po_id&param=edit&client_id=$client_id'>[Edit Purchaseorder]</a>" ;
					}
					?>
				</div>
				<div id="disabled_input">
					<div class="clear"></div>
					<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
						<!--
          <tr>
            <td>Po Title<span class="red">*</span></td>
            <td><input name="po_title" type="text" value="<?php echo addEditInputField('po_title');?>" size="50" maxlength="50" class="validate[required]" /></td>
          </tr>
          -->
						<tr>
							<td width="25%">Client Name:</td>
							<td width="368">
								<?php echo getClientCompanyNameFrmId($client_id);?>
							</td>
						</tr>
						<tr>
							<td>Contact Person:</td>
							<td>
								<?php echo getClientContactNameFrmId($client_id);?>
							</td>
						</tr>
						<tr>
							<td>Date:</td>
							<td>
								<?php echo date("F j, Y");    ;?>
							</td>
						</tr>
					</table>
					<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="25%">PO no :</td>
							<td width="25%">
								<input name="po_no" type="text" value="<?php echo addEditInputField('po_no'); ?>" size="15" />
							</td>
							<td width="25%">Style no:</td>
							<td width="25%">
								<input name="po_style_no" type="text" value="<?php echo addEditInputField('po_style_no'); ?>" size="20" />
							</td>
						</tr>
						<tr>
							<td>Received date:</td>
							<td>
								<script>
			$(function() {
				$("#po_received_date").datepicker({ dateFormat: "yy-mm-dd" });
			});
            </script>
								<input name="po_received_date" id="po_received_date" type="text" value="<?php echo addEditInputField('po_received_date'); ?>" size="15" />
							</td>
							<td>Shipment date:</td>
							<td>
								<script>
			$(function() {
				$("#po_shipment_date").datepicker({ dateFormat: "yy-mm-dd" });
            });
            </script>
								<input name="po_shipment_date" id="po_shipment_date" type="text" value="<?php echo addEditInputField('po_shipment_date'); ?>" size="15" />
							</td>
						</tr>
						<tr>
							<td>Order confirmation number:</td>
							<td>
								<?php if(strlen($po_id)){
									echo  "HWFL/OCN/$po_id";
								}else{
									echo "<span class='small'>(auto generate)</span>";
								}?>
							</td>
							<td>Lead time:</td>
							<td>
								<?php
								//$interval = date_diff($a[po_shipment_date], $a[po_received_date]);
								if($param!='add'){
									/*
									 $datetime1 = date_create($a[po_shipment_date]);
									$datetime2 = date_create($a[po_received_date]);
									$interval = date_diff($datetime2, $datetime1);
									echo $interval->format('%R%a days');
									*/

									$datetime1 = $a[po_shipment_date];
									$datetime2 = $a[po_received_date];
									echo my_date_diff($datetime2, $datetime1);
									//echo $interval->format('%R%a days');


								}
								?>
							</td>
						</tr>
						<tr>
							<td>Item description:</td>
							<td>
								<textarea name="po_item_description" cols="40" rows="3"><?php echo addEditInputField('po_item_description'); ?>
								</textarea>
							</td>
							<td>Fabric type:</td>
							<td>
								<?php
								$selectedId=addEditInputField('po_fabric_type_id');
								$customQuery = " WHERE fabric_type_active='1' ";
							createSelectOptions('fabric_type','fabric_type_id','fabric_type_name',$customQuery,$selectedId,'po_fabric_type_id',"class='validate[required]'");?>
							</td>
						</tr>
						<tr>
							<td>Fabric composition:</td>
							<td>
								<?php
								$selectedId=addEditInputField('po_fco_id');
								$customQuery=" where fco_active='1'";
							createSelectOptions('fabric_composition_options','fco_id','fco_name',$customQuery,$selectedId,'po_fco_id',"class='validate[required]'");?>
							</td>
							<td>GSM:</td>
							<td>
								<input name="po_gsm" type="text" value="<?php echo addEditInputField('po_gsm'); ?>" size="20" class="validate[custom[number]] validate[required]" />
							</td>
						</tr>
						<tr>
							<td>Unit price(USD):</td>
							<td>
								<input name="po_unit_price" type="text" value="<?php echo addEditInputField('po_unit_price'); ?>" size="20" class="validate[required,custom[number]]" />
							</td>
							<td>Yarn Count(/s):</td>
							<td>
								<input name="po_yarn_count" type="text" value="<?php echo addEditInputField('po_yarn_count'); ?>" size="20" class="validate[custom[number]] validate[required]" />
							</td>
						</tr>
						<tr>
							<td>Category</td>
							<td>
								<?php
								$selectedId=addEditInputField('po_category_id');
								$customQuery=" where po_cat_active='1'";
							createSelectOptions('po_category','po_cat_id','po_cat_name',$customQuery,$selectedId,'po_category_id',"class='validate[required]'");?>
							</td>
							<td>
								<input name="po_print" type="checkbox" value="1" <?php if(addEditInputField('po_print')=='1'){echo "checked='checked'";}?> />
								Print required
							</td>
							<td>
								<input name="po_embriodery" type="checkbox" value="1" <?php if(addEditInputField('po_embriodery')=='1'){echo "checked='checked'";}?> />
								Embroidery
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<h4>Quantity Details</h4>
					<table id="quantitydetails_table" border="0" cellpadding="0" cellspacing="0">
						<tr style="font-weight: bold; background: #E6E6E6">
							<td width="20%">color</td>
							<td width="25%">Color type</td>
							<td width="25%">Pantone no</td>
							<?php
							for($z=1; $z<=10; $z++){

								$selectedId=addEditInputField('po_quantity_size'.$z.'_id');
								//echo $selectedId;
								//if($selectedId){

								$customQuery=" where po_size_active='1'";
								if(($param=='view' && $selectedId) || $param=="edit"){
									echo "<td  >";
									createSelectOptions('po_size','po_size_id','po_size_name',$customQuery,$selectedId,'po_quantity_size1_id',"disabled='disabled' class=''");
									echo "</td>";
								};

								//}
							}
							?>
							<td width="3%">Total</td>
							<td width="3%">Action</td>
						</tr>
						<?php
						if ($podetails_rows > 0) {
							$quantity_details_array=array();
							$i=1;
							foreach ($a_podetails as $a_p) {
								?>
						<tr class="podetails_tr" id="<?php echo $a_p['podetails_id']; ?>">
							<td>
								<input class="autocomplete_colour" id="<?php echo $a_p["podetails_id"]; ?>" size="20" maxlength="40" name="podetails_color[]" value="<?php echo $a_p["podetails_color"]; ?>" />
							</td>
							<td>
								<?php
								$selectedId = $a_p['podetails_color_type_id'];
								$customQuery = " WHERE color_type_active='1' ";
								createSelectOptions('color_type', 'color_type_id', 'color_type_name', $customQuery, $selectedId, 'podetails_color_type_id[]', " class='validate[required]'");
								?>
							</td>
							<td>
								<input type="text" name="podetails_pantone_no[]" size="12" value="<?php echo $a_p["podetails_pantone_no"]; ?>" />
							</td>
							<?php
							$total_row=0;
							for($j=1; $j<=10; $j++){
								if(($param=='view' && $a['po_quantity_size'.$j.'_id']) || $param=="edit"){
									$quantity_details_array[$i][$j]=$a_p["podetails_".$j];
									$total_row+=$a_p["podetails_".$j];
									?>
							<td>
								<input type="text" name="podetails_<?php echo $j;?>[]" size="8" maxlength="8" value="<?php echo $a_p["podetails_".$j]; ?>" />
							</td>
							<?php }else $quantity_details_array[$i][$j]=0;
							}
							?>
							<td>
								<?php if($param=="view"){
									echo $total_row;
								}?>
							</td>
							<td></td>
						</tr>
						<?php
						$i++;
							}
						}
						?>
						<tr class="total">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<?php
							//myprint_r($quantity_details_array);
							$grand_total=0;
							for($m=1; $m<=10; $m++){
								if($a['po_quantity_size'.$m.'_id']){
									$total_col=0;
									//echo "podetails_rows:".$podetails_rows."</br>";
									for($n=1; $n<=$podetails_rows; $n++){
										$total_col+=$quantity_details_array[$n][$m];
									}
									echo "<td>";
									if($param=="view"){
										echo $total_col;
									}
									echo "</td>";
									$grand_total+=$total_col;
								}

							}
							?>
							<td>
								<?php if($param=="view"){
									echo $grand_total;
								} ?>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<?php
					/***************************************************************************************/
					/*get unique fabric no*/
					$q = "SELECT DISTINCT fo_fabric_no FROM fabric_order
					WHERE fo_po_uid='".$a['po_uid']."'
					AND fo_active='1'
					ORDER BY fo_fabric_no ASC" ;
					//echo $q;
					$r=mysql_query($q)or die(mysql_error()."<b>Query:</b><br/>$q<br/>");
					$fabric_no_rows=mysql_num_rows($r);
					if($fabric_no_rows > 0){
						$a_fabric_no=mysql_fetch_rowsarr($r);
						foreach($a_fabric_no as $temp){
							echo "<h2>Fabric-".$temp['fo_fabric_no']."</h2>";
							$q = "SELECT * FROM fabric_order
							WHERE fo_fabric_no='".$temp['fo_fabric_no']."'
							AND fo_po_uid='".$a['po_uid']."'
							AND fo_active='1'
							ORDER BY fo_id ASC" ;
							$r=mysql_query($q)or die(mysql_error()."<b>Query:</b><br/>$q<br/>");
							$fo_rows=mysql_num_rows($r);

							if($fo_rows > 0){
								$a_fo=mysql_fetch_rowsarr($r);
								echo "<b>Composition:</b> ".getFabricCompositionOptionFrmId($a_fo[0]["fo_fco_id"]);
								echo "   <b>Type:</b> ".getFabricTypeNameFrmId($a_fo[0]["fo_fco_id"]);
								echo "   <b>GSM:</b> ".$a_fo[0]["fo_gsm"];
								echo "   <b>Remarks:</b> ".$a_fo[0]["fo_remark"];
								echo "<br/>";

								?>
					<table id="quantitydetails_table" border="0" cellpadding="0" cellspacing="0">
						<tr style="font-weight: bold; background: #E6E6E6">
							<td width="20%">Color</td>
							<td width="25%">Color type</td>
							<td width="25%">Pantone no</td>
							<?php
							for($z=1; $z<=10; $z++){
								$selectedId=addEditInputField('po_quantity_size'.$z.'_id');
								//echo $selectedId;
								//if($selectedId){

								$customQuery=" where po_size_active='1'";
								if(($param=='view' && $selectedId) || $param=="edit"){
									echo "<td  >";
									createSelectOptions('po_size','po_size_id','po_size_name',$customQuery,$selectedId,'po_quantity_size1_id',"disabled='disabled' class=''");
									echo "</td>";
								};

								//}
							}
							?>
							<td width="3%">Total</td>
							<td width="3%">Action</td>
						</tr>
						<?php
						if ($fo_rows > 0) {
							$fabric_quantity_array=array();
							for($p=0,$i=1; $p<$fo_rows; $p++,$i++){

								$q = "SELECT * FROM fabric_order_consumption
								WHERE foc_fo_id='".$a_fo[$p]['fo_id']."'
								AND foc_active='1'";
								//echo $q;
								$r=mysql_query($q)or die(mysql_error()."<b>Query:</b><br/>$q<br/>");
								$foc_rows=mysql_num_rows($r);
								//echo "foc_rows".$foc_rows;
								if($foc_rows){
									$a_foc=mysql_fetch_assoc($r);
								}

								$q = "SELECT * FROM fabric_order_dia
								WHERE fod_fo_id='".$a_fo[$p]['fo_id']."'
								AND fod_active='1'";
								//echo $q;
								$r=mysql_query($q)or die(mysql_error()."<b>Query:</b><br/>$q<br/>");
								$fod_rows=mysql_num_rows($r);
								//echo "foc_rows".$foc_rows;
								if($fod_rows){
									$a_fod=mysql_fetch_assoc($r);
									//myprint_r($a_fod);
								}


								?>
						<tr class="foc_tr" id="<?php echo $a_foc['foc_id']; ?>">
							<td>
								<input size="20" maxlength="40" name="fo_color[]" value="<?php echo $a_fo[$p]["fo_color"]; ?>" />
							</td>
							<td>
								<?php
								$selectedId = $a_fo[$p]['fo_color_type_id'];
								$customQuery = " WHERE color_type_active='1' ";
								createSelectOptions('color_type', 'color_type_id', 'color_type_name', $customQuery, $selectedId, 'podetails_color_type_id[]', " class='validate[required]'");
								?>
							</td>
							<td>
								<input type="text" name="fo_pantone_no[]" size="12" value="<?php echo $a_fo[$p]["fo_pantone_no"]; ?>" />
							</td>
							<?php
							$total_row=0;
							for($j=1; $j<=10; $j++){
								if(($param=='view' && $a['po_quantity_size'.$j.'_id']) || $param=="edit"){
									$fabric_quantity_array[$i][$j]=round($a_foc["foc_".$j]*$quantity_details_array[$i][$j]/12,2);
									$total_row+=$fabric_quantity_array[$i][$j];
									?>
							<td>
								<span class='small gray'> Dia: <?php echo $a_fod['fod_'.$j]; ?>
								</span> <br />
								<input type="text" name="podetails_<?php echo $j;?>[]" size="8" maxlength="8" value="<?php echo $fabric_quantity_array[$i][$j]; ?>" />
							</td>
							<?php }else $fabric_quantity_array[$i][$j]=0;
							}
							?>
							<td>
								<?php if($param=="view"){
									echo $total_row;
								}?>
							</td>
							<td>
								<a href="snippets/fabric_order/fabric_order_delete.php?fo_id=<?php echo $a_foc['foc_fo_id'];?>&client_id=<?php echo $client_id;?>&po_id=<?php echo $po_id;?>">Delete</a>
							</td>
						</tr>
						<?php


							}
						}
						?>
						<tr class="total">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<?php
							//myprint_r($fabric_quantity_array);
							$grand_total=0;
							for($m=1; $m<=10; $m++){
								if($a['po_quantity_size'.$m.'_id']){
									$total_col=0;
									//echo "podetails_rows:".$podetails_rows."</br>";
									for($n=1; $n<=$fo_rows; $n++){
										$total_col+=$fabric_quantity_array[$n][$m];
									}
									echo "<td>";
									if($param=="view"){
										echo $total_col;
										//echo "[".$fabric_quantity_array[$n][$m]."]";
									}
									echo "</td>";
									$grand_total+=$total_col;
								}

							}
							?>
							<td>
								<?php if($param=="view"){
									echo $grand_total;
								} ?>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<?

							}
						}
						//$fo_fabric_no_csv = implode(',',$a_fo_fabric_no[0]);
						//echo "fo_fabric_no_csv".$fo_fabric_no_csv;
					}
					/***********************/


					/***************************************************************************************/
					?>
				</div>
			</div>
			<?php
			//if(strlen($po_id))include_once('snippets/purchaseorder/po_versions.php');
			?>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
