<?php
include('config.php');
$valid = true;
$alert = array();
$client_id = $_REQUEST[client_id];
$param = $_REQUEST[param];
$po_id = $_REQUEST[po_id];
if (!strlen($client_id)) {
	header('location:index.php');
}

if(strlen($_REQUEST[confirm])){
	if($_REQUEST[param]=='delete'){
		if(strlen($_REQUEST[bom_id])){
			$sql="UPDATE bom set bom_active='0',bom_updated_by_user_id='".$_SESSION[current_user_id]."', bom_updated_datetime=now() where bom_id='".$_REQUEST[bom_id]."'";
			mysql_query($sql) or die(mysql_error() . "<br/><b>Query:</b>$sql<br/>");
			header("location:purchaseorder_add.php?po_id=$po_id&param=view&client_id=$client_id");
		}
	}
}

//myprint_r($_REQUEST);
$exception_field = array('submit', 'client_id', 'param', 'podetails_id', 'podetailis_po_id', 'podetails_color', 'podetails_pantone_no','podetails_color_type_id', 'podetails_1', 'podetails_2', 'podetails_3', 'podetails_4', 'podetails_5', 'podetails_6', 'podetails_7', 'podetails_8', 'podetails_9', 'podetails_10', 'bom_id', 'bom_po_id', 'bom_material', 'bom_quantity_per_pc', 'bom_wastage','bom_rate_per_dozen','bom_supplier_id','bom_delivery_date' );
if ($param == 'edit' || $param == 'add') {
	if (isset($_POST[submit])) {
		/*
		 * Server side validation
		*/
		if (!strlen($_REQUEST[po_no])) {
			$valid = false;
			array_push($alert, "Please insert po no");
		}
		/*         * *************************************************** */
		/*
		 * If data is valid then data is stored in the database
		*/
		if ($valid) {
			/*
			 * Capture form data to create a query string
			*/
			$str = createMySqlInsertString($_POST, $exception_field);
			/*             * *************************************************** */
			$str_k = $str['k'];
			$str_v = $str['v'];
			if ($param == 'add') {
				$po_uid = makeRandomKey();
				$sql = "INSERT INTO purchaseorder($str_k,po_prepared_date,po_uid) values($str_v,now(),'$po_uid')";
			} else if ($param == 'edit') {
				$sql = "INSERT INTO purchaseorder($str_k,po_prepared_date) values($str_v,now())";
			}
			//echo $sql;
			mysql_query($sql) or die(mysql_error() . "<br/><b>Query:</b>$sql<br/>");
			$po_id = mysql_insert_id();
			array_push($alert, "The po is registered successfully!");
			$param = "view";
			//header("location:purchaseorder_add.php?client_id=".$_REQUEST[client_id]."&param=edit&po_id=$po_id");
			/*
			 * 	insert product detail rows in database table
			*/
			$total_podetails = sizeof($_REQUEST["podetails_color"]); // Gets total number of podetails
			//echo $total_podetails."</br>";
			for ($j = 0; $j < $total_podetails; $j++) {
				$sql = "
				INSERT INTO podetails(
				podetails_po_id,
				podetails_color,
				podetails_color_type_id,
				podetails_pantone_no,
				podetails_1,
				podetails_2,
				podetails_3,
				podetails_4,
				podetails_5,
				podetails_6,
				podetails_7,
				podetails_8,
				podetails_9,
				podetails_10
				)VALUES(
				'" . $po_id . "',
				'" . $_REQUEST["podetails_color"][$j] . "',
				'" . $_REQUEST["podetails_color_type_id"][$j] . "',
				'" . $_REQUEST["podetails_pantone_no"][$j] . "',
				'" . $_REQUEST["podetails_1"][$j] . "',
				'" . $_REQUEST["podetails_2"][$j] . "',
				'" . $_REQUEST["podetails_3"][$j] . "',
				'" . $_REQUEST["podetails_4"][$j] . "',
				'" . $_REQUEST["podetails_5"][$j] . "',
				'" . $_REQUEST["podetails_6"][$j] . "',
				'" . $_REQUEST["podetails_7"][$j] . "',
				'" . $_REQUEST["podetails_8"][$j] . "',
				'" . $_REQUEST["podetails_9"][$j] . "',
				'" . $_REQUEST["podetails_10"][$j] . "'
				)
				";
				//echo $sql;
				mysql_query($sql) or die(mysql_error() . "<br/><b>Query:</b>$sql<br/>");
			}
			/*             * **************************************************************** */
			/*
			 * insert bom rows into database table
			*/
			$total_bom = sizeof($_REQUEST["bom_material"]);
			//echo $total_podetails."</br>";
			for ($j = 0; $j < $total_bom; $j++) {
				$sql = "
				INSERT INTO bom(
				bom_po_id,
				bom_material,
				bom_quantity_per_pc,
				bom_wastage,
				bom_rate_per_dozen,
				bom_supplier_id,
				bom_delivery_date
				)VALUES(
				'" . $po_id . "',
				'" . $_REQUEST["bom_material"][$j] . "',
				'" . $_REQUEST["bom_quantity_per_pc"][$j] . "',
				'" . $_REQUEST["bom_wastage"][$j] . "',
				'" . $_REQUEST["bom_rate_per_dozen"][$j] . "',
				'" . $_REQUEST["bom_supplier_id"][$j] . "',
				'" . $_REQUEST["bom_delivery_date"][$j] . "'
				)
				";
				//echo $sql;
				mysql_query($sql) or die(mysql_error());
			}
		}
	}
}
if($param=='finalize'){
	finalizePoQuantity($po_id);
	$param='view';
}
$r = mysql_query("Select * from purchaseorder where po_id='$po_id'") or die(mysql_error());
$a = mysql_fetch_assoc($r);
$r = mysql_query("Select * from podetails where podetails_po_id='$po_id' ") or die(mysql_error());
$podetails_rows = mysql_num_rows($r);
//echo $podetails_rows."<br />";
if ($podetails_rows > 0) {
	$a_podetails = mysql_fetch_rowsarr($r);
}
$r = mysql_query("Select * from bom where bom_po_uid='".$a[po_uid]."' AND bom_active='1' ") or die(mysql_error());
$bom_rows = mysql_num_rows($r);
//echo $podetails_rows."<br />";
if ($bom_rows > 0) {
	$a_bom = mysql_fetch_rowsarr($r);
}
if (newerPurchaseOrderExists($po_id)) {
	$valid = false;
	array_push($alert, "A newer verison of purchase order exists. Please work on the newer version.");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include_once("inc.head.php"); ?>
</head>
<body>
<!-- JQuery Modal Popup for delete : Start --->
<div id="dialog" title="Confirm" style="display: none;">
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    <input name="param" type="hidden" value="delete" />
    <input name="bom_id" type="hidden" value="" />
    <input name="client_id" type="hidden" value="<?=$client_id;?>" />
    <input name="po_id" type="hidden" value="<?=$po_id;?>" />
    <input name="confirm_checkbox" type="checkbox" value="confirmed" class="validate[required]" />
    <?php echo $defaultConfirmationMsg; ?>
    <div class="clear"></div>
    <input type="submit" name="confirm" value="confirm" class="bgblue button" />
  </form>
</div>
<!-- JQuery Modal Popup for delete : Ends --->
<div id="wrapper">
  <div id="container">
    <div id="top1">
      <?php include('top.php'); ?>
    </div>
    <div id="mid">
      <div id="client_menu">
        <?php
					include('snippets/client/clientmenu.php');
					?>
      </div>
      <h2> Client: <?php echo getClientCompanyNameFrmId($client_id); ?> - <?php echo ucfirst($param); ?> Purchase Order </h2>
      <div class="alert"> <?php printAlert($valid, $alert); ?> </div>
      <div class="right">
        <?php
					if (hasPermission('purchaseorder', 'edit', $_SESSION[current_user_id]) && !newerPurchaseOrderExists($po_id) && $param == 'view' && !FabricBookingStatusAgainstPoUid($po_uid)) {
						if($a[po_quantity_finalized]=='0'){
							echo "<a href='purchaseorder_add.php?po_id=$po_id&param=edit&client_id=$client_id'>[Edit Purchaseorder]</a>";
						}
						else{
							echo "<span class='bgred whiteText textBox'>This PO has been finalized. Cannot be edited </span>";
						}
					}
					?>
      </div>
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" <?php if($param=='view'){echo " class='disabled_input' ";}?>>
        <?php if ($param != 'add') { ?>
        <input type="hidden" name="po_uid" value="<?php echo addEditInputField('po_uid'); ?>" />
        <?php } ?>
        <input type="hidden" name="po_client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <input type="hidden" name="po_prepared_by" value="<?php echo $_SESSION[current_user_id]; ?>" />
        <input type="hidden" name="param" value="<?php echo $param; ?>" />
        <div class="clear"></div>
        <table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="25%">Client Name:</td>
            <td width="368"><?php echo getClientCompanyNameFrmId($client_id); ?></td>
          </tr>
          <tr>
            <td>Contact Person:</td>
            <td><?php echo getClientContactNameFrmId($client_id); ?></td>
          </tr>
          <tr>
            <td>Date:</td>
            <td><?php
								echo date("F j, Y");
								;
								?></td>
          </tr>
        </table>
        <div class="clear"></div>
        <table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="25%">PO no :</td>
            <td width="25%"><input name="po_no" type="text" value="<?php echo addEditInputField('po_no'); ?>" size="15" /></td>
            <td width="25%">Style no:</td>
            <td width="25%"><input name="po_style_no" type="text" value="<?php echo addEditInputField('po_style_no'); ?>" size="20" /></td>
          </tr>
          <tr>
            <td>Received date:</td>
            <td><script>
$(function() {
$("#po_received_date").datepicker({ dateFormat: "yy-mm-dd" });
});
</script>
              <input name="po_received_date" id="po_received_date" type="text" value="<?php echo addEditInputField('po_received_date'); ?>" size="15" class='validate[required, custom[date]]' /></td>
            <td>Shipment date:</td>
            <td><script>
$(function() {
$("#po_shipment_date").datepicker({ dateFormat: "yy-mm-dd" });
});
</script>
              <input name="po_shipment_date" id="po_shipment_date" type="text" value="<?php echo addEditInputField('po_shipment_date'); ?>" size="15" class='validate[required, custom[date]]' /></td>
          </tr>
          <tr>
            <td>Order confirmation number:</td>
            <td><?php
								if (strlen($po_id)) {
									echo "HWFL/OCN/$po_id";
								}else{
									echo "<span class='small'>(auto generate)</span>";
								}
								?></td>
            <td>Lead time:</td>
            <td><?php
				//$interval = date_diff($a[po_shipment_date], $a[po_received_date]);
				if ($param != 'add') {
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
				?></td>
          </tr>
          <tr>
            <td>Item description:</td>
            <td><textarea name="po_item_description" cols="40" rows="3"><?php echo addEditInputField('po_item_description'); ?></textarea></td>
            <td>Fabric type:</td>
            <td><?php
				$selectedId = addEditInputField('po_fabric_type_id');
				$customQuery = " WHERE fabric_type_active='1' ";
				createSelectOptions('fabric_type', 'fabric_type_id', 'fabric_type_name', $customQuery, $selectedId, 'po_fabric_type_id', "class='validate[required]'");
				?></td>
          </tr>
          <tr>
            <td>Fabric composition:</td>
            <td><?php
				$selectedId = addEditInputField('po_fco_id');
				$customQuery = " where fco_active='1'";
				createSelectOptions('fabric_composition_options', 'fco_id', 'fco_name', $customQuery, $selectedId, 'po_fco_id', "class='validate[required]'");
				?></td>
            <td>GSM:</td>
            <td><input name="po_gsm" type="text" value="<?php echo addEditInputField('po_gsm'); ?>" size="20" class="validate[custom[number]] validate[required]" /></td>
          </tr>
          <tr>
            <td>Unit price(USD):</td>
            <td><input name="po_unit_price" type="text" value="<?php echo addEditInputField('po_unit_price'); ?>" size="20" class="validate[required,custom[number]]" /></td>
            <td>Total price:</td>
            <td><span id="total_price"></span></td>
          </tr>
          <tr>
            <td>Category</td>
            <td><?php
				$selectedId = addEditInputField('po_category_id');
				$customQuery = " where po_cat_active='1'";
				createSelectOptions('po_category', 'po_cat_id', 'po_cat_name', $customQuery, $selectedId, 'po_category_id', "class='validate[required]'");
				?></td>
<td><input name="po_print" type="checkbox" value="1" <?php
				if (addEditInputField('po_print') == '1') {
					echo "checked='checked'";
				}
				?> />
              Print required </td>
            <td><input name="po_embriodery" type="checkbox" value="1" <?php
				if (addEditInputField('po_embriodery') == '1') {
					echo "checked='checked'";
				}
				?> />
              Embroidery </td>
          </tr>
        </table>
        <div class="clear"></div>
        <h4>Quantity Details</h4>
        <?php
		if($a[po_quantity_finalized]=='1'){
			echo "<span class='bgred whiteText textBox'>This table has been finalized. Values cannot be changed</span><br>";
		}
		?>
        <table id="quantitydetails_table" border="0" cellpadding="0" cellspacing="0">
          <tr style="font-weight: bold; background: #E6E6E6">
            <td width="20%">Color</td>
            <td width="20%">Color type</td>
            <td width="25%">Pantone no</td>
            <?php
			for($z=1; $z<=10; $z++){
				$selectedId=addEditInputField('po_quantity_size'.$z.'_id');
				//echo $selectedId;
				//if($selectedId){
				echo "<td  >";
				$customQuery=" where po_size_active='1'";
				//echo $selectedId;
				if(($param=='view' && $selectedId) || $param=="edit" || $param=="add"){
					createSelectOptions('po_size','po_size_id','po_size_name',$customQuery,$selectedId,'po_quantity_size'.$z.'_id'," class=''");
				};
				echo "</td>";
				//}
			}
			?>
            <td width="3%">Total</td>
            <td width="3%">&nbsp;</td>
          </tr>
          	<?php
			if ($podetails_rows > 0) {
				$quantity_details_array=array();
				$i=1;
				foreach ($a_podetails as $a_p) {
					?>
	<tr class="podetails_tr" id="<?php echo $a_p['podetails_id']; ?>">
	<td><input class="autocomplete_colour" id="<?php echo $a_p["podetails_id"]; ?>" size="20" maxlength="40" name="podetails_color[]" value="<?php echo $a_p["podetails_color"]; ?>" /></td>
	<td><?php
					$selectedId = $a_p['podetails_color_type_id'];
					$customQuery = " WHERE color_type_active='1' ";
					createSelectOptions('color_type', 'color_type_id', 'color_type_name', $customQuery, $selectedId, 'podetails_color_type_id[]', " class='validate[required]'");
			?></td>
            <td><input type="text" name="podetails_pantone_no[]" size="12" value="<?php echo $a_p["podetails_pantone_no"]; ?>" /></td>
            <?php
			$total_row=0;
			for($j=1; $j<=10; $j++){
				echo "<td>";
				if(($param=='view' && $a['po_quantity_size'.$j.'_id']) || $param=="edit" || $param=="add" ){
					$quantity_details_array[$i][$j]=$a_p["podetails_".$j];
					$total_row+=$a_p["podetails_".$j];
					echo "
					<input
					type='text'
					name='podetails_".$j."[]'
					size='8'
					maxlength='8'
					value='".$a_p['podetails_'.$j]."'
					class='validate[ custom[integer]] quantityInput'
					/>";
				}else{
					$quantity_details_array[$i][$j]=0;
				}
				echo "</td>";
			}
			?>
            <td>
			<?php if($param=="view"){
					echo $total_row;
				}?></td>
		<td><?php if($param!="view"){?>
              <input type="button" class='remove_row_podetails' id="<?php echo $a_p['podetails_id']; ?>" value="Remove" />
              <?php } ?>
              <script>
$("input[class=remove_row_podetails][id=<?php echo $a_p['podetails_id']; ?>]").click(function(){
$('tr[class=podetails_tr][id=<?php echo $a_p['podetails_id']; ?>]').remove();
});
</script></td>
          </tr>
          <?php
						$i++;
							}
						}
						?>
          <tr class="total">
            <td>&nbsp;</td>
            <!-- color -->
            <td>&nbsp;</td>
            <!-- color type -->
            <td>&nbsp;</td>
            <!-- pantone no -->
            <?php
							//myprint_r($quantity_details_array);
							$grand_total=0;
							for($m=1; $m<=10; $m++){
								echo "<td>";
								if($a['po_quantity_size'.$m.'_id']){
									$total_col=0;
									//echo "podetails_rows:".$podetails_rows."</br>";
									for($n=1; $n<=$podetails_rows; $n++){
										$total_col+=$quantity_details_array[$n][$m];
									}
									if($param=="view"){
										echo $total_col;
									}
									$grand_total+=$total_col;
								}
								echo "</td>";
							}
							?>
            <td><?php if($param=="view"){
									echo "<span id='grand_total'>$grand_total</span>";
								} ?></td>
            <td>&nbsp;</td>
          </tr>
        </table>
        <?php if ($param == 'add' || $param == 'edit') { ?>
        <!-- add button -->
        <div class="clear"></div>
        <?php if($a[po_quantity_finalized]!='1'){?>
        <input type="button" name="quantitydetails_add" value="Add more color" />
        <img id="ajax-loader-quantitydetails" src="images/ajax-loader-1.gif" style="display: none;" /> 
        <!---->
        <?php } ?>
        <?php } ?>
        <div class="clear"></div>
        <?php if($param=='view'){?>
        <div id="billofmaterials"> 
          <!-- BILL OF MATERIALS TABLE --> 
          <br />
          <h4>Bill of materials</h4>
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
              <td width="6%"></td>
            </tr>
            <?php
							if ($bom_rows > 0) {
								$i=0;
								foreach ($a_bom as $a_b) {
									?>
            <tr class="bom_tr" id="<?php echo $a_b['bom_id']; ?>">
              <td><input type="hidden" name="bom_id[]" value="<?php echo $a_b["bom_id"]; ?>" />
                <input class="autocomplete_material" id="<?php echo $a_b["bom_id"]; ?>" name="bom_material[]" value="<?php echo $a_b["bom_material"]; ?>" /></td>
              <td><input type="text" name="bom_quantity_per_pc[]" size="8" maxlength="8" value="<?php echo $a_b["bom_quantity_per_pc"]; ?>" /></td>
              <td><input type="text" name="bom_wastage[]" size="8" maxlength="8" value="<?php echo $a_b["bom_wastage"]; ?>" /></td>
              <td>
			  	<?php
				$total_quantity_wo_wastage=$grand_total*$a_b["bom_quantity_per_pc"];
				$wastage=$total_quantity_wo_wastage*$a_b["bom_wastage"]/100;
				$total_quantity_with_wastage=$total_quantity_wo_wastage+$wastage;
				echo $total_quantity_with_wastage;
				?></td>
              <td><input type="text" name="bom_rate_per_dozen[]" size="8" maxlength="8" value="<?php echo $a_b["bom_rate_per_dozen"]; ?>" /></td>
              <td>
			 	<?php
				$total_price=$total_quantity_with_wastage*$a_b["bom_rate_per_dozen"]/12;
				echo round($total_price,2);
				?></td>
              <td>
			  	<?php
				$selectedId = $a_b['bom_supplier_id'];
				$customQuery = " WHERE supplier_active='1' ";
				createSelectOptions('supplier', 'supplier_id', 'supplier_company_name', $customQuery, $selectedId, 'bom_supplier_id[]', "class='validate[required]'");
				?></td>
              <td><input name="bom_delivery_date[]" id="bom_delivery_date_<?php $random_tr_id=makeRandomKey();echo $random_tr_id; ?>" type="text" value="<?php echo $a_b['bom_delivery_date']; ?>" size="15" /></td>
              <td><a target="_blank" class='delete' id='<?php echo $a_b['bom_id']; ?>' href='#'>Delete</a> | <a href='purchaseorder_bom_order_sheet.php?po_id=<?=$po_id?>&param=view&client_id=<?=$client_id?>&bom_id=<?=$a_b['bom_id']?>'>Order sheet</a></td>
            </tr>
            <?php
				}
				$i++;
			}
			?>
          </table>
          <?php if ($param == 'add' || $param == 'edit') { ?>
          <div class="clear"></div>
          <input type="button" name="bom_add" value="Add new material" />
          <img id="ajax-loader-bom" src="images/ajax-loader-1.gif" style="display: none;" /> <br />
          <?php } ?>
        </div>
        <div class="clear"></div>
        <?php
					if (hasPermission('purchaseorder', 'edit', $_SESSION[current_user_id]) && !newerPurchaseOrderExists($po_id) && $param == 'view') {
						echo "<a href='purchaseorder_bom_add.php?po_id=$po_id&param=add&client_id=$client_id' class='button bgblue' rel='facebox'>+ Add materials</a>";
					}
					?>
        <?php } ?>
        <?php
					if (($param == 'add' || $param == 'edit') && !newerPurchaseOrderExists($po_id)) {
						/* TODO - need to add hasPermission() */
						?>
        <input class="button bgblue" type="submit" name="submit" value="Calculate and save" style="clear: both;" />
        <?php } ?>
        <?php
if($param == 'view' && !newerPurchaseOrderExists($po_id)){
if ($a['po_quantity_finalized']=='1') {
if(!FabricBookingStatusAgainstPoUid($a[po_uid])){
echo " <a href='fabric_order_add.php?param=view&client_id=$client_id&po_id=$po_id' class='button bgblue' rel='facebox'> + Add another frabric order </a>";
}else{
echo "<span class='button bgred'>Fabric already booked</span>";
}

?>
        <a href="fabric_order_sheet.php?param=view&amp;client_id=<?php echo $client_id; ?>&amp;po_id=<?php echo $po_id; ?>" class='button bgblue' target="_blank">View fabric order sheet</a>
        <?php }else{ ?>
        <a href="purchaseorder_add.php?po_id=<?php echo $a[po_id];?>&amp;client_id=<?php echo $client_id; ?>&amp;param=finalize" class='button bgblue'>Finalize Quantity/Size</a>
        <?php
}
}?>
      </form>
    </div>
    <?php
/*
//TODO - Versioning needs


if (hasFabricOrderAgainstPo($a["po_uid"]))
include_once('snippets/purchaseorder/fabric_order_against_po.php');
if (strlen($po_id))
include_once('snippets/purchaseorder/po_versions.php');
*/	?>
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
	
	$(".disabled_input select").attr('disabled','disabled');
	/*
	*	This is the code segment that works once the small button under qunatitiy details
	has been clicked
	*/
	$("input[name=quantitydetails_add]").click(function() {
		$("img#ajax-loader-quantitydetails").show();
		$.get('snippets/purchaseorder/po_ajax_quantitydetails_table_row.php?param=add', function(data) {
			//alert(data);
			//$("table#quantitydetails_table").append(data);
			$("table#quantitydetails_table tr:last").before(data);
			$("img#ajax-loader-quantitydetails").hide();
		});
	});
	$("input[name=bom_add]").click(function() {
		$("img#ajax-loader-bom").show();
		$.get('snippets/purchaseorder/po_ajax_bom_table_row.php?param=add&po_id=<?php echo $po_id;?>', function(data) {
			//alert(data);
			$("table#bom_table").append(data);
			$("img#ajax-loader-bom").hide();
		});
	});
	$(function() {
		$( ".autocomplete_material" ).autocomplete({
			source: "snippets/common/ajax_autosearch_material.php",
			minLength: 2,
			select: function( event, data ) {
				/*
				log( ui.item ?
				"Selected: " + ui.item.value + " aka " + ui.item.id :
				"Nothing selected, input was " + this.value );
				*/
			}
		});
	});
	$(function() {
		$( ".autocomplete_colour" ).autocomplete({
			source: "snippets/common/ajax_autosearch_colour.php",
				minLength: 2,
				select: function( event, data ) {
			}
		});
	});
	
	$("#dialog" ).dialog({ autoOpen: false, });
	
	/*
	*	Opens up pop-up window for cnofirmation of delete
	*/
	$('a.delete').click(function(){
		var bom_id = $(this).attr('id');
		//alert(sla_id);
		$('input[name=bom_id]').val(bom_id);
		$( "#dialog" ).dialog('open');
	});
	
	/* following code gathers unit_price and grand_total from different part of the script and shows total price*/
	var unit_price= $('input[name=po_unit_price]').val();
	var grand_total=<?=$grand_total?>;
	$('span[id=total_price]').html(unit_price*grand_total);
});
</script>