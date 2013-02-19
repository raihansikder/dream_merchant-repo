<?php
include('config.php');
$valid=true;
$alert=array();
$costsheet_client_id=$_REQUEST[client_id];
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$costsheet_id=$_REQUEST[costsheet_id];
//myprint_r($_REQUEST);
if(!strlen($client_id)){
	header('location:index.php');
}
if($param=='request_approval'||$param=='approve'||$param=='disapprove'){
	if(!strlen($costsheet_id)){
		header('location:index.php');
	}
}

if($param=='add'||$param=='edit'){
	if(isset($_POST[submit])){
		/*
		 *	Create the insert query substring.
		*/
		$exception_field=array('submit','client_id','param','ca_name','ca_quantity_per_dozen','ca_unit_price_per_dozen');
		$str=createMySqlInsertString($_POST, $exception_field);
		$str_k=$str['k'];
		$str_v=$str['v'];
		/******************************************************/
		/*
		 *	Server side validation
		*/

		if(!strlen($_REQUEST[costsheet_client_id])){
			$valid=false;
			array_push($alert,"The System cannot determine for which client you are putting this entry. Please go to the clients registered account first and then try adding this entry");
		}
		if(!strlen($_REQUEST[costsheet_title])){
			$valid=false;
			array_push($alert,"Please insert costsheet title");
		}
		if(!strlen($_REQUEST[costsheet_unitofmeasures])){
			$valid=false;
			array_push($alert,"Please insert costsheet unit of measures");
		}
		/******************************************************/
		if($valid){

			/*
			 $sql="UPDATE costsheet SET $str_k WHERE costsheet_id='$costsheet_id'";
			*/
			if($param=='add'){
				$costsheet_uid=makeRandomKey(); // create a new costsheet_uid (unique random number) for the first time
				$sql="INSERT INTO costsheet($str_k,costsheet_prepared_date,costsheet_uid) values($str_v,now(),'$costsheet_uid')";
			}else if($param=='edit'){
				$sql="INSERT INTO costsheet($str_k,costsheet_prepared_date) values($str_v,now())";
			}
			//echo $sql;
			mysql_query($sql) or die(mysql_error());
			$costsheet_id= mysql_insert_id();
			array_push($alert,"The costsheet is registered successfully!");
			$param="edit";

			/*
			 * 	insert product detail rows in database table
			*/
			$total_accessories= sizeof($_REQUEST["ca_name"]); // Gets total number of podetails
			//echo $total_podetails."</br>";
			for($j=0; $j<$total_accessories;$j++){

				$sql="
				INSERT INTO costsheet_accessories(
				ca_costsheet_id,
				ca_name,
				ca_quantity_per_dozen,
				ca_unit_price_per_dozen
				)VALUES(
				'".$costsheet_id."',
				'".$_REQUEST["ca_name"][$j]."',
				'".$_REQUEST["ca_quantity_per_dozen"][$j]."',
				'".$_REQUEST["ca_unit_price_per_dozen"][$j]."'
				)
				";
				//echo $sql. "<br/>";
				mysql_query($sql) or die(mysql_error());
			}
		}
	}
}
//'Unapproved','Requested_approval','Disapproved','Approved'
if($param=='request_approval'){
	if(hasPermission('costsheet','request_approval',$_SESSION[current_user_id])){
		initCostsheetApprovalRequest($costsheet_id,$_SESSION[current_user_id]);
		array_push($alert,"Approval Request sent");
	}else{
		$valid=false;
		array_push($alert,"You are not permitted to request approval");
	}
}

if($param=='approve'){
	if(hasPermission('costsheet','approve',$_SESSION[current_user_id])){
		if($_SESSION[current_user_id]!=costsheetApproverIdfromCostsheetId($costsheet_id)){
			$valid=false;
			array_push($alert,"You have not been selected as the approver for this costsheet");
		}else{
			initCostsheetApprove($costsheet_id,$_SESSION[current_user_id]);
			array_push($alert,"Costsheet is approved");
		}
	}else{
		$valid=false;
		array_push($alert,"You don't have the level of permission to approve costsheet");
	}
}
if($param=='disapprove'){
	if(hasPermission('costsheet','disapprove',$_SESSION[current_user_id])){
		if($_SESSION[current_user_id]!=costsheetApproverIdfromCostsheetId($costsheet_id)){
			$valid=false;
			array_push($alert,"You have not been selected as the approver for this costsheet");
		}else{
			initCostsheetDisapprove($costsheet_id,$_SESSION[current_user_id]);
			array_push($alert,"Costsheet is Unapproved");
		}
	}else{
		$valid=false;
		array_push($alert,"You don't have the level of permission to disapprove costsheet");
	}
}

$sql="Select * from costsheet where costsheet_id='$costsheet_id'";
$r=mysql_query($sql)or die(mysql_error());
$a=mysql_fetch_assoc($r);

$q="Select * from costsheet_accessories where ca_costsheet_id='$costsheet_id' and ca_active='1' ";
$r=mysql_query($q)or die(mysql_error());
$ca_rows=mysql_num_rows($r);
if($ca_rows> 0){
	$a_ca=mysql_fetch_rowsarr($r);
}
if(newerCostsheetExists($costsheet_id)){
	$valid=false;
	array_push($alert,"A newer verison of costsheet exists. Please work on the newer version.");
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include('inc.head.php');?>
<script>
/*
*	updates the message list
*/
$('document').ready(function(){
	$("input[name=costsheet_msg_post]").click(function() {
		var message_text = $('textarea[name=costsheet_msg]').val();
		message_text=$.trim(message_text);
		if(message_text.length > 0){
			//alert(message_text);
			var costsheet_uid = $('input[name=costsheet_uid]').val();
			$("img#ajax-loader-message-post").show();
			$.get('snippets/costsheet/costsheet_message_ajax_add.php?costsheet_uid='+costsheet_uid+'&message_text='+message_text, function(data) {
			  //alert(data);
			  $("div.message_list").prepend(data);
			  $("img#ajax-loader-message-post").hide();
			  $('textarea[name=costsheet_msg]').val('');
			});
		}else{
			alert('Please write your message!');
		}
	});

	/**************************************/

	/*
	*	dynamically adds row in accessories table
	*/
	$("input[name=accessories_add]").click(function() {
		$("img#ajax-loader-accessories").show();
		$.get('snippets/costsheet/costsheet_ajax_accessories_table_row.php?param=add', function(data) {
		  //alert(data);
		  $("table#accessories_table").append(data);
		  $("img#ajax-loader-accessories").hide();
		});
	});

	/*
	*	Auto complete accessoires
	*/
	$(function() {
		$( ".autocomplete_accessories" ).autocomplete({
			source: "snippets/common/ajax_autosearch_accessories.php",
			minLength: 2,
			select: function( event, data ) {
				//alert(data)
			}
		});
	});
	/**************************************/
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
				<?php include('snippets/client/clientmenu.php');	?>
				<h2>
					Client:
					<?php echo getClientCompanyNameFrmId($client_id);?>
					-
					<?php echo ucfirst($param);?>
					costsheet
				</h2>
				<div class="alert">
					<?php printAlert($valid,$alert);?>
				</div>
				<div class="right">
					<?php
					echo "status : ".$a[costsheet_approval_state];
					if(hasPermission('costsheet','edit',$_SESSION[current_user_id])){
						echo " | <a href='costsheet_add.php?costsheet_id=$costsheet_id&param=edit&client_id=$client_id'>[Edit Costsheet]</a>" ;
					}
					?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
					<?php if($param!='add'){?>
					<input type="hidden" name="costsheet_uid" value="<?php echo addEditInputField('costsheet_uid');?>" />
					<?php } ?>
					<input type="hidden" name="costsheet_client_id" value="<?php echo $costsheet_client_id;?>" />
					<input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
					<input type="hidden" name="costsheet_prepared_by" value="<?php echo $_SESSION[current_user_id]; ?>" />
					<input type="hidden" name="param" value="<?php echo $param;?>" />
					<div class="clear"></div>
					<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td>
								Style/Ref No <span class="red">*</span>
							</td>
							<td>
								<input name="costsheet_title" type="text" value="<?php echo addEditInputField('costsheet_title');?>" size="50" maxlength="50" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<h4>General information for costing</h4>
							</td>
						</tr>
						<tr>
							<td width="218">Client name:</td>
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
							<td width="25%">Size range :</td>
							<td width="25%">
								<input name="costsheet_sizerange" type="text" value="<?php echo addEditInputField('costsheet_sizerange'); ?>" size="15" />
							</td>
							<td width="25%">Fabrication:</td>
							<td width="25%">
								<input name="costsheet_fabrication" type="text" value="<?php echo addEditInputField('costsheet_fabrication'); ?>" size="20" />
							</td>
						</tr>
						<tr>
							<td>Fabric composition:</td>
							<td>
								<input name="costsheet_fabric_composition" type="text" value="<?php echo addEditInputField('costsheet_fabric_composition'); ?>" size="15" />
							</td>
							<td>GSM:</td>
							<td>
								<input name="costsheet_gsm" type="text" value="<?php echo addEditInputField('costsheet_gsm'); ?>" size="8" class="validate[required,custom[number]]" />
							</td>
						</tr>
					</table>
					<br />
					<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4">
								<h4>Consumption calculation(per pc)</h4>
								<a href="infotabs/fabric1_total_consumption.php" rel="facebox"> <img src="images/info-small.png" alt="info" align="middle" />
								</a>
							</td>
						</tr>
						<tr>
							<td>Unit of measures:</td>
							<td>
								<select name="costsheet_unitofmeasures" class="validate[required]">
									<option value="" <?php if(!strlen($_REQUEST[costsheet_unitofmeasures])&&!strlen($a[costsheet_unitofmeasures])){echo "selected='selected'";}?>>Select</option>
									<option value="INCH" <?php if($_REQUEST[costsheet_unitofmeasures]=='INCH'||$a[costsheet_unitofmeasures]=='INCH'){echo "selected='selected'";} ?>>INCH</option>
									<option value="CM" <?php if($_REQUEST[costsheet_unitofmeasures]=='CM'||$a[costsheet_unitofmeasures]=='CM'){echo "selected='selected'";} ?>>CM</option>
								</select>
							</td>
							<td>Body length:</td>
							<td>
								<input name="costsheet_bodylength" type="text" value="<?php echo addEditInputField('costsheet_bodylength'); ?>" size="8" />
							</td>
						</tr>
						<tr>
							<td width="25%">Sleeve length:</td>
							<td width="25%">
								<input name="costsheet_sleevelength" type="text" value="<?php echo addEditInputField('costsheet_sleevelength'); ?>" size="8" />
							</td>
							<td width="25%">Body width:</td>
							<td width="25%">
								<input name="costsheet_bodywidth" type="text" value="<?php echo addEditInputField('costsheet_bodywidth'); ?>" size="8" />
							</td>
						</tr>
						<tr>
							<td>Wastage%:</td>
							<td>
								<input name="costsheet_fabric1_consumptionwastage" type="text" value="<?php echo addEditInputField('costsheet_fabric1_consumptionwastage'); ?>" size="8" />
							</td>
							<td>Fabric 1 Total consumption(kg/doz):</td>
							<td>
								<?php
								/*add ing 4 inch with total length*/
								if($a[costsheet_unitofmeasures]=='INCH'){
									$a[costsheet_bodylength]+=4;
								}
								else if($a[costsheet_unitofmeasures]=='CM'){
									$a[costsheet_bodylength]+=10;
								}

								//echo $a[costsheet_bodylength]."-";
								/**********************************/
								if($a[costsheet_unitofmeasures]=='INCH')
								{
									$total_fabric1_consumption_withoout_wastage=(($a[costsheet_bodylength]+$a[costsheet_sleevelength])*($a[costsheet_bodywidth]+1)*2*$a[costsheet_gsm])/1550000;
								}
								else if($a[costsheet_unitofmeasures]=='CM'){
									$total_fabric1_consumption_withoout_wastage=(($a[costsheet_bodylength]+$a[costsheet_sleevelength])*($a[costsheet_bodywidth]+2.54)*2*$a[costsheet_gsm])/10000000;
								}

								$total_fabric1_consumption_wastage=($total_fabric1_consumption_withoout_wastage*$a[costsheet_fabric1_consumptionwastage])/100;
								$total_fabric1_consumption_kg_with_wastage= ($total_fabric1_consumption_withoout_wastage+$total_fabric1_consumption_wastage)*12;
			echo "<b>".round($total_fabric1_consumption_kg_with_wastage,2)."</b> "?>
								(auto calculate)
							</td>
						</tr>
						<tr>
							<td>Fabrict 2 (rib) consumption(kg/doz):</td>
							<td>
								<input name="costsheet_fabric2_consumption" type="text" value="<?php echo addEditInputField('costsheet_fabric2_consumption'); ?>" size="8" />
							</td>
							<td>Total fabric consumption (kg/doz):</td>
							<td>
								<?php
								/*if($a[costsheet_unitofmeasures]=='inch')
								 $total_fabric1_consumption_withoout_wastage=(($a[costsheet_bodylength]+$a[costsheet_sleevelength])*($a[costsheet_bodywidth]+1)*2*$a[costsheet_gsm])/1550000;
								else if($a[costsheet_unitofmeasures]=='cm')
									$total_fabric1_consumption_withoout_wastage=(($a[costsheet_bodylength]+$a[costsheet_sleevelength])*($a[costsheet_bodywidth]+2.54)*2*$a[costsheet_gsm])/1000000;*/

								$total_fabric_cost_without_wastage=$a[costsheet_yarnpriceperkg]+$a[costsheet_lycraprice]+$a[costsheet_knittingcost]+$a[costsheet_dyeingcost]
								+$a[costsheet_extrafinishingcost];
								$total_fabric_consumption_per_doz = $total_fabric1_consumption_kg_with_wastage + $a[costsheet_fabric2_consumption];
			echo "<b>".round($total_fabric_consumption_per_doz,2)."</b> ";?>
								(auto calculate)
							</td>
						</tr>
					</table>
					<br />
					<table id="list" border="0" cellpadding="0" cellspacing="0" width="586">
						<tr>
							<td colspan="4">
								<h4>Fabric Price Calculation(per dozen unit)</h4>
							</td>
						</tr>
						<tr>
							<td width="25%">Yarn count:</td>
							<td width="25%">
								<input name="costsheet_yarncount" type="text" value="<?php echo addEditInputField('costsheet_yarncount'); ?>" size="8" maxlength="8" />
							</td>
							<td width="25%">Yarn price/kg:</td>
							<td width="25%">
								<input name="costsheet_yarnpriceperkg" type="text" value="<?php echo addEditInputField('costsheet_yarnpriceperkg'); ?>" size="8" />
								USD
							</td>
						</tr>
						<tr>
							<td>Lycra Price:</td>
							<td>
								<input name="costsheet_lycraprice" type="text" value="<?php echo addEditInputField('costsheet_lycraprice'); ?>" size="8" />
								USD
							</td>
							<td>Knitting cost:</td>
							<td>
								<input name="costsheet_knittingcost" type="text" value="<?php echo addEditInputField('costsheet_knittingcost'); ?>" size="8" />
								USD
							</td>
						</tr>
						<tr>
							<td>Dyeing cost:</td>
							<td>
								<input name="costsheet_dyeingcost" type="text" value="<?php echo addEditInputField('costsheet_dyeingcost'); ?>" size="8" />
								USD
							</td>
							<td>Extra finishing cost:</td>
							<td>
								<input name="costsheet_extrafinishingcost" type="text" value="<?php echo addEditInputField('costsheet_extrafinishingcost'); ?>" size="8" />
								USD
							</td>
						</tr>
						<tr>
							<td>Wastage %:</td>
							<td>
								<input name="costsheet_pricewestage" type="text" value="<?php echo addEditInputField('costsheet_pricewestage'); ?>" size="8" />
							</td>
							<td>Total Fabric cost:</td>
							<td>
								<?php
								$total_fabric_cost_without_wastage=
								$a[costsheet_yarnpriceperkg]+
								$a[costsheet_lycraprice]+
								$a[costsheet_knittingcost]+
								$a[costsheet_dyeingcost]+
								$a[costsheet_extrafinishingcost];

								$total_fabric_cost_with_wastage=($total_fabric_cost_without_wastage+($total_fabric_cost_without_wastage*$a[costsheet_pricewestage]/100));
								echo "<b>".$total_fabric_cost_with_wastage."</b>";
								?>
								USD (auto calculate)
							</td>
						</tr>
					</table>
					<p>&nbsp;</p>
					<table id="accessories_table" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr style="font-weight: bold; background: #E6E6E6">
							<td width="5%">#</td>
							<td width="45%">Fabric</td>
							<td width="10%">Consumption</td>
							<td width="10%">Unit price/ Dozen</td>
							<td width="10%">Cost/ Dozen</td>
							<td width="10%">Cost/ Pc</td>
							<td width="10%">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Main fabric cost</td>
							<td>na</td>
							<td>na</td>
							<td>
								<?php
								$total_fabric_cost_with_wastage=($total_fabric_cost_without_wastage+($total_fabric_cost_without_wastage*$a[costsheet_pricewestage]/100));
							echo round ($total_fabric_cost_with_wastage*$total_fabric_consumption_per_doz,2);?>
							</td>
							<td>
								<?php echo round(($total_fabric_cost_with_wastage*$total_fabric_consumption_per_doz)/12,2);?>
							</td>
							<td>
								<a href="infotabs/test_infotab.php" rel="facebox"> <img src="images/info-small.png" alt="info" />
								</a>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Other fabric cost</td>
							<td>
								<input name="costsheet_additional_febric_quantity" type="text" value="<?php echo addEditInputField('costsheet_additional_febric_quantity'); ?>" size="8" />
							</td>
							<td>
								<input name="costsheet_additional_febric_unitprice" type="text" value="<?php echo addEditInputField('costsheet_additional_febric_unitprice'); ?>" size="8" />
							</td>
							<td>
								<?php
								$costsheet_additional_febric_quantity_costperdozen = $a[costsheet_additional_febric_quantity]*$a[costsheet_additional_febric_unitprice];
							echo $costsheet_additional_febric_quantity_costperdozen;?>
							</td>
							<td>
								<?php	echo round($costsheet_additional_febric_quantity_costperdozen/12,2);?>
							</td>
							<td>
								<a href="infotabs/test_infotab.php" rel="facebox"> <img src="images/info-small.png" alt="info" />
								</a>
							</td>
						</tr>
						<tr style="border-top: 2px solid #333; font-weight: bold">
							<td>&nbsp;</td>
							<td>
								<strong>Total Fabric Cost</strong>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>
								<?php
								$total_fabric_cost= ($total_fabric_cost_with_wastage*$total_fabric_consumption_per_doz)+ $costsheet_additional_febric_quantity_costperdozen;
								echo round($total_fabric_cost,2)
								?>
							</td>
							<td>
								<?php echo round(($total_fabric_cost/12),2)?>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr>
							<td></td>
						</tr>
						<tr style="font-weight: bold; background: #E6E6E6">
							<td width="5%">&nbsp;</td>
							<td width="35%">Trim &amp; Accessories</td>
							<td width="25%">Required Quantity/Pc</td>
							<td>Price/Dozen</td>
							<td>Total Cost</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<?php
							/*
							 * 	Total Trim cost
							*/

							/***************************************/
							?>
							<td>&nbsp;</td>
							<td>Sweing thread</td>
							<td>
								<input name="costsheet_sewing_thread_quantity" type="text" value="<?php echo addEditInputField('costsheet_sewing_thread_quantity'); ?>" size="8" />
							</td>
							<td>
								<input name="costsheet_sewing_thread_price" type="text" value="<?php echo addEditInputField('costsheet_sewing_thread_price'); ?>" size="8" />
							</td>
							<td>
								<?php
								$costsheet_sewing_thread_quantity_costperdozen = ($a[costsheet_sewing_thread_quantity]/4000)*$a[costsheet_sewing_thread_price];

								echo $costsheet_sewing_thread_quantity_costperdozen;
								$total_trim_cost_per_dozen=$costsheet_sewing_thread_quantity_costperdozen;
								?>
							</td>
							<td>
								<a href="infotabs/test_infotab.php" rel="facebox"> <img src="images/info-small.png" alt="info" />
								</a>
							</td>
						</tr>
						<?php
						if($ca_rows>0){
			for($i=0; $i<$ca_rows; $i++){?>
						<tr class="ca_tr" id="<?php echo  $a_ca[$i]['ca_id'];?>">
							<td>&nbsp;</td>
							<td>
								<input type="text" name="ca_name[]" size="12" maxlength="12" value="<?php echo $a_ca[$i]["ca_name"];?>" class="autocomplete_accessories" />
								<script>
			</script>
							</td>
							<td>
								<input name="ca_quantity_per_dozen[]" type="text" size="8" value="<?php echo $a_ca[$i]["ca_quantity_per_dozen"];?>" />
							</td>
							<td>
								<input name="ca_unit_price_per_dozen[]" type="text" value="<?php echo $a_ca[$i]["ca_unit_price_per_dozen"];?>" size="8" />
							</td>
							<?php $a_ca[$i]["ca_total_price_per_dozen"]= ($a_ca[$i]["ca_quantity_per_dozen"]*$a_ca[$i]["ca_unit_price_per_dozen"]); ?>
							<td>
								<?php
								$temp_acc_cost_per_dozen=  $a_ca[$i]["ca_quantity_per_dozen"]* (($a_ca[$i]["ca_unit_price_per_dozen"])/12);
								$total_trim_cost_per_dozen+=$temp_acc_cost_per_dozen;
								echo round($temp_acc_cost_per_dozen,2);
								?>
							</td>
							<td>
								<input type="button" class='remove_row_accessories' id="<?php echo $a_ca[$i]['ca_id'];?>" value="Remove" />
								<script>
            $("input[class=remove_row_accessories][id=<?php echo  $a_ca[$i]['ca_id'];?>]").click(function(){
                  $('tr[class=ca_tr][id=<?php echo  $a_ca[$i]['ca_id'];?>]').remove();
            });
            </script>
							</td>
						</tr>
						<?php
			}
		  } ?>
					</table>
					<div class="clear"></div>
					<?php if($param=='add'||$param=='edit'){?>
					<input type="button" name="accessories_add" value="Click to add more" />
					<img id="ajax-loader-accessories" src="images/ajax-loader-1.gif" style="display: none;" />
					<?php } ?>
					<div class="clear"></div>
					<table width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="4">
								<strong>Total Trims Cost (USD):&nbsp;&nbsp; </strong>
							</td>
							<td>
								<?php	echo round($total_trim_cost_per_dozen,2);?>
							</td>
							<td>
								<?php echo round($total_trim_cost_per_dozen/12,2);?>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr style="background: #ccc">
							<td colspan="4">
								<strong>Total Trims Cost (USD) With wastage(5%) </strong> :&nbsp;&nbsp;
							</td>
							<td>
								<b> <?php
								$total_cost_perdozen_with_wastage=$total_trim_cost_per_dozen+($total_trim_cost_per_dozen*.05);
								echo round($total_cost_perdozen_with_wastage,2);
								?>
								</b>
							</td>
							<td>
								<b> <?php	echo round($total_cost_perdozen_with_wastage/12,2);?>
								</b>
							</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<br />
					<table id="list" width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="25%">
								<strong>Other Cost</strong>
							</td>
							<td width="25%">&nbsp;</td>
							<td width="50%">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<strong>Cost/Dozen</strong>
							</td>
							<td>
								<strong>Cost/Pc</strong>
							</td>
						</tr>
						<tr>
							<td>Print &amp; Embroidery</td>
							<td>
								<input name="costsheet_printembroidery_price" type="text" value="<?php echo addEditInputField('costsheet_printembroidery_price'); ?>" size="8" />
							</td>
							<td>
								<?php echo round($a[costsheet_printembroidery_price]/12,2); ?>
							</td>
						</tr>
						<tr>
							<td>cm</td>
							<td>
								<input name="costsheet_cm_price" type="text" value="<?php echo addEditInputField('costsheet_cm_price'); ?>" size="8" />
							</td>
							<td>
								<?php echo round($a[costsheet_cm_price]/12,2); ?>
							</td>
						</tr>
						<tr>
							<td>Bank &amp; Others (%)</td>
							<td>
								<input name="costsheet_bankothers" type="text" value="<?php echo addEditInputField('costsheet_bankothers'); ?>" size="8" />
							</td>
							<?php $banking_cost=(($total_cost_usd_perdozen_with_wastage+$a[costsheet_printembroidery_price]+$a[costsheet_cm_price])*$a[costsheet_bankothers])/100?>
							<td>
								<?php echo round($banking_cost/12,2); ?>
							</td>
						</tr>
						<tr>
							<td>
								<b>Total Garments Cost</b>
							</td>
							<td>
								<?php
								$total_garments_cost_perdozen=$total_fabric_cost+$total_cost_perdozen_with_wastage+$a[costsheet_printembroidery_price]+$a[costsheet_cm_price]+$banking_cost;
								echo round($total_garments_cost_perdozen,2);
								?>
							</td>
							<td>
								<?php echo round($total_garments_cost_perdozen/12,2); ?>
							</td>
						</tr>
						<tr>
							<td>Margin(%of Total Garments Cost)</td>
							<td>
								<input name="costsheet_margin" type="text" value="<?php echo addEditInputField('costsheet_margin'); ?>" size="8" />
							</td>
							<td>--</td>
						</tr>
						<tr>
							<td>
								<strong>Garments FOB Price</strong>
							</td>
							<td>
								<?php
								$margin_perdozen = round((($total_garments_cost_perdozen*$a[costsheet_margin])/100),2);
								$garments_fob_price_perdozen = $total_garments_cost_perdozen+$margin_perdozen;
							echo round($garments_fob_price_perdozen,2);?>
							</td>
							<td>
								<?php echo round($garments_fob_price_perdozen/12,2);?>
							</td>
						</tr>
						<tr>
							<td>Freight Charges</td>
							<td>
								<input name="costsheet_freight_charges" type="text" value="<?php echo addEditInputField('costsheet_freight_charges'); ?>" size="8" />
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
								<b>Garments Price / Pc</b> <br /> C&amp;F
							</td>
							<td>
								<?php echo round(($garments_fob_price_perdozen+$a[costsheet_freight_charges]),2); ?>
							</td>
							<td>
								<?php echo round(($garments_fob_price_perdozen+$a[costsheet_freight_charges])/12,2); ?>
							</td>
						</tr>
						<tr>
							<td>
								<strong>QUOTED PRICE</strong>
							</td>
							<td>
								<input name="costsheet_quoted_price_perdozen" type="text" class="bold" value="<?php echo addEditInputField('costsheet_quoted_price_perdozen'); ?>" size="8" />
							</td>
							<td style="font-weight: bold; font-size: 12px">
								<input name="costsheet_quoted_price_perpiece" type="text" class="bold" value="<?php echo addEditInputField('costsheet_quoted_price_perpiece'); ?>" size="8" />
								<script>
              	$('input[name=costsheet_quoted_price_perdozen]').blur(function(){
					var val=$('input[name=costsheet_quoted_price_perdozen]').val();
					//alert(val);
					$('input[name=costsheet_quoted_price_perpiece]').val(val/12);
				})
              </script>
							</td>
						</tr>
						<tr>
							<td>Costsheet approver</td>
							<td>
								<?php
								$selectedId=addEditInputField('costsheet_approver_user_id');
								$customQuery = " WHERE user_active='1'AND user_type_id = '1' AND user_type_id in(".userTypeIdsPermittedForAction('costsheet','approve').") and user_id in(".getClientUserIds($client_id).")";
			createSelectOptions('user','user_id','user_name',$customQuery,$selectedId,'costsheet_approver_user_id',"class='validate[required]'");?>
							</td>
							<td style="font-weight: bold; font-size: 12px">&nbsp;</td>
						</tr>
					</table>
					<div class="actionUrlBlock">
						<?php include_once('snippets/costsheet/costsheet_actions.php');?>
					</div>
				</form>
			</div>
			<?php if(strlen($costsheet_id))include_once('snippets/costsheet/costsheet_message.php');?>
			<?php if(strlen($costsheet_id))include_once('snippets/costsheet/costsheet_versions.php');?>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
