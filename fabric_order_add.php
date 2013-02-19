<?php
include('config.php');
$valid=true;
$alert=array();
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$po_id=$_REQUEST[po_id];
$fabric_no=$_REQUEST[fo_fabric_no];

if(!strlen($client_id)){
	header('location:index.php');
}

//myprint_r($_REQUEST);
/*
 $tablenameInitArr=array("foft","fofc","fog","fod","fodu","fodf","foc","for");
$tablename=array("fabric_order_fabric_type","fabric_order_fabric_composition","fabric_order_gsm","fabric_order_dia","fabric_order_dia_unit","fabric_order_dia_form","fabric_order_consumption","fabric_order_remark");
*/
$tablenameInitArr=array("fod","foc");
$tablename=array("fabric_order_dia","fabric_order_consumption");
$exception_field=array('submit','param');

if($param=='edit' || $param=='add'){
	if(isset($_POST[submit])){
		/*
		 * Server side validation
		*/
		//if(!strlen($_REQUEST[po_no])){
		//$valid=false;
		//array_push($alert,"Please insert po no");
		//}
		/******************************************************/
		/*
		 * If data is valid then data is stored in the database
		*/
		if($valid && sizeof($_REQUEST["fo_color"])>0){
			/*
			 * Capture form data to create a query string
			*/
			//$str=createMySqlInsertString($_POST, $exception_field);
			/******************************************************/
			//$str_k=$str['k'];
			//$str_v=$str['v'];
			if($param=='add'){ // SQL Query To add a new fabric order
				$num_of_color=sizeof($_REQUEST["fo_color"]);
				for($i=0;$i<$num_of_color;$i++){
					/* DB insert fabric_order */
					$sql="INSERT INTO fabric_order(
					fo_po_id,
					fo_po_uid,
					fo_client_id,
					fo_fab_cal_type,
					fo_fabric_no,
					fo_color,
					fo_color_type_id,
					fo_pantone_no,
					fo_fabric_type_id,
					fo_fco_id,
					fo_gsm,
					fo_dia_unit,
					fo_dia_form,
					fo_remark,
					fo_prepared_datetime,
					fo_prepared_user_id)
					values(
					'".$_REQUEST["fo_po_id"]."',
					'".$_REQUEST["fo_po_uid"]."',
					'".$_REQUEST["fo_client_id"]."',
					'".$_REQUEST["fo_fab_cal_type"]."',
					'".$_REQUEST["fo_fabric_no"]."',
					'".$_REQUEST["fo_color"][$i]."',
					'".$_REQUEST["fo_color_type_id"][$i]."',
					'".$_REQUEST["fo_pantone_no"][$i]."',
					'".$_REQUEST["fo_fabric_type_id"]."',
					'".$_REQUEST["fo_fco_id"]."',
					'".$_REQUEST["fo_gsm"]."',
					'".$_REQUEST["fo_dia_form"]."',
					'".$_REQUEST["fo_dia_unit"]."',
					'".$_REQUEST["fo_remark"]."',
					now(),
					'".$_SESSION["current_user_id"]."')";
					//echo $sql;
					mysql_query($sql) or die(mysql_error()."<br/><b>Query:</b>$sql<br/>");
					$fo_id=mysql_insert_id();
					/*********************/
					for($j=0; $j<sizeof($tablenameInitArr);$j++){
						$dbtable=$tablename[$j];
						$dbtableInit=$tablenameInitArr[$j];
						for($k=0;$k<sizeof($_REQUEST[$dbtableInit."_po_id"]);$k++){
							$sql="
							INSERT INTO $dbtable(
							".$dbtableInit."_fo_id,
							".$dbtableInit."_po_id,
							".$dbtableInit."_po_uid,
							".$dbtableInit."_fabric_no,
							".$dbtableInit."_1,
							".$dbtableInit."_2,
							".$dbtableInit."_3,
							".$dbtableInit."_4,
							".$dbtableInit."_5,
							".$dbtableInit."_6,
							".$dbtableInit."_7,
							".$dbtableInit."_8,
							".$dbtableInit."_9,
							".$dbtableInit."_10
							)VALUES(
							'".$fo_id."',
							'".$_REQUEST[$dbtableInit."_po_id"][$k]."',
							'".$_REQUEST[$dbtableInit."_po_uid"][$k]."',
							'".$fabric_no."',
							'".$_REQUEST[$dbtableInit."_1"][$k]."',
							'".$_REQUEST[$dbtableInit."_2"][$k]."',
							'".$_REQUEST[$dbtableInit."_3"][$k]."',
							'".$_REQUEST[$dbtableInit."_4"][$k]."',
							'".$_REQUEST[$dbtableInit."_5"][$k]."',
							'".$_REQUEST[$dbtableInit."_6"][$k]."',
							'".$_REQUEST[$dbtableInit."_7"][$k]."',
							'".$_REQUEST[$dbtableInit."_8"][$k]."',
							'".$_REQUEST[$dbtableInit."_9"][$k]."',
							'".$_REQUEST[$dbtableInit."_10"][$k]."'
							)
							";
							//echo $sql;
							mysql_query($sql) or die(mysql_error()."<br/><b>Query:</b>$sql<br/>");
						}
					}
				}
			}
			header("location:fabric_order_sheet.php?po_id=".$_REQUEST[po_id]."&param=view&client_id=".$_REQUEST[client_id]);
		}
	}
}

$r=mysql_query("Select * from purchaseorder where po_id='$po_id'")or die(mysql_error());
$a=mysql_fetch_assoc($r);

$r=mysql_query("Select * from podetails where podetails_po_id='$po_id' ")or die(mysql_error());
$podetails_rows=mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($podetails_rows > 0){
	$a_podetails=mysql_fetch_rowsarr($r);
}

$r=mysql_query("Select * from bom where bom_po_id='$po_id' ")or die(mysql_error());
$bom_rows=mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($bom_rows > 0){
	$a_bom=mysql_fetch_rowsarr($r);
}
if(newerPurchaseOrderExists($po_id)){
	$valid=false;
	array_push($alert,"A newer verison of purchase order exists. Please work on the newer version.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php //include_once("inc.head.php");?>
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery-ui.js" type="text/javascript"></script>
<script src="js/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$('document').ready(function(){	
	$("div[id=disabled_input] input").attr('readonly','readonly');
	$("div[id=disabled_input] select").attr('disabled','disabled');
	$('div#fabric').load("snippets/fabric_order/fo_ajax_tbl.php?po_id=<?php echo $po_id;?>&client_id=<?php echo $client_id;?>");
});

</script>
</head>
<body>
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
	<form name="order_fabric" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<div id="fabric" class="fabtable">Loading...</div>
		<div class="clear"></div>
		<input name="param" type="hidden" value="add" />
		<input name="client_id" type="hidden" value="<?php echo $client_id;?>" />
		<input name="po_id" type="hidden" value="<?php echo $po_id;?>" />
		<input class="button bgblue" type="submit" name="submit" value="Calculate and save" />
	</form>
</body>
</html>
