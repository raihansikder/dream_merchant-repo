<?php
include('config.php');
$valid=true;
$alert=array();
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$po_id=$_REQUEST[po_id];
$po_uid=$_REQUEST[po_uid];

if (!strlen($client_id)) {
	header('location:index.php');
}

//myprint_r($_REQUEST);

if ($param == 'add') {
	if (isset($_POST[submit])) {
		/*
		 * Server side validation
		*/
		/*
		 if (!strlen($_REQUEST[po_no])) {
		$valid = false;
		array_push($alert, "Please insert po no");
		}
		*/
		/*         * *************************************************** */
		/*
		 * If data is valid then data is stored in the database
		*/
		if ($valid) {
			/*
			 * insert bom rows into database table
			*/
			$total_bom = sizeof($_REQUEST["bom_material"]);
			//echo $total_podetails."</br>";
			for ($j = 0; $j < $total_bom; $j++) {
				$sql = "
				INSERT INTO bom(
				bom_po_id,
				bom_po_uid,
				bom_material,
				bom_quantity_per_pc,
				bom_wastage,
				bom_rate_per_dozen,
				bom_supplier_id,
				bom_delivery_date,
				bom_updated_by_user_id,
				bom_updated_datetime
				)VALUES(
				'" . $po_id . "',
				'" . $po_uid . "',
				'" . $_REQUEST["bom_material"][$j] . "',
				'" . $_REQUEST["bom_quantity_per_pc"][$j] . "',
				'" . $_REQUEST["bom_wastage"][$j] . "',
				'" . $_REQUEST["bom_rate_per_dozen"][$j] . "',
				'" . $_REQUEST["bom_supplier_id"][$j] . "',
				'" . $_REQUEST["bom_delivery_date"][$j] . "',
				'" . $_SESSION["current_user_id"]. "',
				now()
				)

				";
				//echo $sql;
				mysql_query($sql) or die(mysql_error()."<b>SQL:</b>$sql");
				header("location:purchaseorder_add.php?po_id=$po_id&param=view&client_id=$client_id");

			}

		}

	}

}

$r = mysql_query("Select * from purchaseorder where po_id='$po_id'") or die(mysql_error());
$a = mysql_fetch_assoc($r);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php //include_once("inc.head.php"); ?>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery-ui.js" type="text/javascript"></script>
<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$('document').ready(function(){
	$("form").validationEngine();

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
});
</script>
</head>
<body>
	<div id="">
		<div id="purchaseorder_bom_add">
			<div id="top1"></div>
			<div id="mid">
				<h2>
					Client:
					<?php echo getClientCompanyNameFrmId($client_id); ?>
					-
					<?php echo ucfirst($param); ?>
					Purchase Order
				</h2>
				<div class="alert">
					<?php printAlert($valid, $alert); ?>
				</div>
				<div class="right"></div>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="po_uid" value="<?php echo $a['po_uid']; ?>" />
					<input type="hidden" name="po_id" value="<?php echo $a['po_id']; ?>" />
					<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
					<input type="hidden" name="param" value="<?php echo $param; ?>" />
					<div class="clear"></div>
					<div id="billofmaterials">
						<!-- BILL OF MATERIALS TABLE -->
						<br />
						<h4>Bill of materials</h4>
						<table id="bom_table" width="586" border="0" cellpadding="0" cellspacing="0">
							<tr class="bom_tr" style="font-weight: bold; background: #E6E6E6">
								<td width="33%">Material</td>
								<td width="19%">Quantity/pc</td>
								<td width="17%">wastage %</td>
								<td width="25%">Total Quantity</td>
								<td width="6%">Rate/ Dozen</td>
								<td width="6%">Total Price (USD)</td>
								<td width="6%">Supplier</td>
								<td width="6%">Delivery Date</td>
								<td width="6%">Action</td>
							</tr>
						</table>
						<?php if ($param=='add') { ?>
						<div class="clear"></div>
						<input type="button" name="bom_add" value="Add new material" />
						<img id="ajax-loader-bom" src="images/ajax-loader-1.gif" style="display: none;" /> <br />
						<?php } ?>
					</div>
					<div class="clear"></div>
					<?php
					if (hasPermission('purchaseorder', 'edit', $_SESSION[current_user_id]) && !newerPurchaseOrderExists($po_id) && $param == 'view') {
						echo "<a href='purchaseorder_add.php?po_id=$po_id&param=edit&client_id=$client_id' class='button bgblue'>+ Add materials</a>";
					}
					?>
					<input class="button bgblue" type="submit" name="submit" value="Calculate and save" style="clear: both;" />
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php'); ?>
		</div>
	</div>
</body>
</html>
