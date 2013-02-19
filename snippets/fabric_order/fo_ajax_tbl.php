<?php
include_once('../../config.php');
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$po_id=$_REQUEST[po_id];

$r=mysql_query("Select * from purchaseorder where po_id='$po_id'")or die(mysql_error());
$a=mysql_fetch_assoc($r);

$r=mysql_query("Select * from podetails where podetails_po_id='$po_id' ")or die(mysql_error());
$podetails_rows=mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($podetails_rows > 0){
	$a_podetails=mysql_fetch_rowsarr($r);
	poColorPantonePairCsv($a_podetails);
}

function poColorPantonePairCsv($a_podetails){
	//$rowCount=count($a_podetails);
	$ColorPantonePairStr="";
	foreach($a_podetails as $podetails){
		$ColorPantonePairStr.= $podetails["podetails_color"]."[".$podetails["podetails_pantone_no"]."],";
	}
	//echo "ColorPantonePairStr".$ColorPantonePairStr;
	return trim($ColorPantonePairStr,", ");

}

?>

<div>
	<h2>
		Fabric-
		<?php echo $fabric;?>
		<input name="fo_fabric_no" class="validate[required, custom[number]]" value="" size="2" maxlength="2" style="float: none;
	display: inline;">
		Consumption details
	</h2>
	<div class="clear"></div>
	<input type="hidden" name="fo_po_id" value="<?php echo $po_id;?>" />
	<input type="hidden" name="fo_po_uid" value="<?php echo $a["po_uid"];?>" />
	<input type="hidden" name="fo_client_id" value="<?php echo $a["po_client_id"];?>" />
	<select name='fo_fab_cal_type' class="validate[required]">
		<option value="Size wise">Size wise</option>
		<option value="Average">Average</option>
	</select>
	<div class="clear"></div>
	<table id="fab_avg_cons_table" border="0" cellpadding="0" cellspacing="0" style="display: none;">
		<tr>
			<td>Average Consumption/Dozen:</td>
			<td>
				<input name="fo_avg_consperdoz" type="text" size="8" maxlength="8" value="" class="validate[required,custom[number]]" readonly="readonly" />
			</td>
			<td>Average Dia:</td>
			<td>
				<input name="fo_avg_dia" type="text" size="8" maxlength="8" value="" class="validate[required,custom[number]]" readonly="readonly" />
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<table>
		<tr style="vertical-align: top;">
			<td>&nbsp;
				
			</td>
			<!--      <td><b>Pantone no:</b><br/></td>
-->
			<td>
				<span style='float: left;'>
					Fabrict type:
					<br />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					Fabrict Composition:
					<br />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					GSM:
					<br />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					Dia Unit:
					<br />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					Dia Form:
					<br />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					Remark:
					<br />
				</span>
			</td>
		</tr>
		<tr style="vertical-align: top;">
			<td>
				<table id="color_pantone_table" width="100%">
					<tr style="font-weight: bold">
						<td>Color</td>
						<td>Color type</td>
						<td>Pantone no</td>
					</tr>
					<?php
					foreach($a_podetails as $podetails){
						echo "<tr class='color_tr' id='".$podetails["podetails_id"]."'>";
						echo "<td><input name='fo_color[]' value='".$podetails["podetails_color"]."' class='validate[required]' /></td>";
						echo "<td>";
						$selectedId = $podetails['podetails_color_type_id'];
						$customQuery = " WHERE color_type_active='1' ";
						createSelectOptions('color_type', 'color_type_id', 'color_type_name', $customQuery, $selectedId, 'fo_color_type_id[]', " class='validate[required]'");
						echo "</td>";
						echo "<td><input name='fo_pantone_no[]' value='".$podetails["podetails_pantone_no"]."' class='validate[required]'/></td>";				
						echo "</tr>";
		}?>
				</table>
				<div class="clear"></div>
			</td>
			<!-- DYNAMIC COLOR PANTON TABLE-->
			<!--      <td><input type="text" name="pantone_no[]" size="12" value="" class="validate[required]"/></td>
-->
			<td>
				<span style='float: left;'>
					<?php
					$selectedId = $a["po_fabric_type_id"];
					$customQuery = " WHERE fabric_type_active='1' ";
					createSelectOptions('fabric_type', 'fabric_type_id', 'fabric_type_name', $customQuery, $selectedId, 'fo_fabric_type_id', " class='validate[required]'");
					?>
				</span>
			</td>
			<td>
				<span style='float: left;'>
					<?php
					$selectedId=$a["po_fco_id"];
					$customQuery=" where fco_active='1'";
					createSelectOptions('fabric_composition_options','fco_id','fco_name',$customQuery,$selectedId,'fo_fco_id',"class='validate[required]'");
					?>
				</span>
			</td>
			<td>
				<span style='float: left;'>
					<input size="5" maxlength="10" name="fo_gsm" value="<?php echo $a["po_gsm"]; ?>" class="validate[required]" />
				</span>
			</td>
			<td>
				<span style='float: left;'>
					<?php
					echo "<select name='fo_dia_unit' >";
					echo "<option value='inch'";
					if($val=="inch"){
						echo " selected='selected' ";
					}
					echo " >inch</option>";
					echo "<option value='cm'";
					if($val=="cm"){
						echo " selected='selected' ";
					}
					echo " >cm</option>";
					echo "</select>";
					?>
				</span>
			</td>
			<td>
				<span style='float: left;'>
					<?php
					echo "<select name='fo_dia_form' >";
					echo "<option value='Open'";
					if($val=="Open"){
						echo " selected='selected' ";
					}
					echo " >Open</option>";
					echo "<option value='Tublar'";
					if($val=="Tublar"){
						echo " selected='selected' ";
					}
					echo " >Tublar</option>";
					echo "</select>";
					?>
				</span>
			</td>
			<td>
				<span style='float: left;'>
					<textarea name="fo_remark" cols="30" rows="1" class=""></textarea>
				</span>
			</td>
		</tr>
	</table>
	<div class="clear"></div>
	<table id="quantitydetails_table" border="0" cellpadding="0" cellspacing="0">
		<tr style="font-weight: bold;
	background: #E6E6E6">
			<td width="20%">&nbsp;</td>
			<td width="25%">&nbsp;</td>
			<?php
			for($z=1; $z<=10; $z++){

				$selectedId=addEditInputField('po_quantity_size'.$z.'_id');
				//echo $selectedId;
				if($selectedId){
					echo "<td  >";
					$customQuery=" where po_size_active='1'";
					createSelectOptions('po_size','po_size_id','po_size_name',$customQuery,$selectedId,'po_quantity_size1_id',"disabled='disabled' class=''");
				}
			}
			?>
		</tr>
		<?php


		/*
	  $tablenametArr=array("Fabric Type","Fabric Composition","GSM","Dia","Dia unit","Dia form","Consumption /dozen","Remards");
		$tablenameInitArr=array("foft","fofc","fog","fod","fodu","fodf","foc","for");
		*/
		$tablenametArr=array("Dia","Consumption /dozen");
		$tablenameInitArr=array("fod","foc");
		?>
		<?
		$i=0;
		foreach($tablenameInitArr as $tbl){
			?>
		<tr>
			<td>
				<!-- Hidden form values : start -->
				<input name="<?php echo $tbl; ?>_po_id[]" type="hidden" value="<?php echo $po_id; ?>" />
				<input name="<?php echo $tbl; ?>_po_uid[]" type="hidden" value="<?php echo $a["po_uid"];?>" />
				<input name="<?php echo $tbl; ?>_client_id[]" type="hidden" value="<?php echo $a["po_client_id"];?>" />
				<input name="<?php echo $tbl; ?>_fabric[]" type="hidden" value="<?php echo $fabric; ?>" />
				<!-- Hidden form values : end -->
				<input size="20" maxlength="40" name="placeholder" value="" disabled="disabled" style="background-color: #fff;
	border: 0px" />
			</td>
			<td align="right">
				<?php echo $tablenametArr[$i];?>
			</td>
			<?php
			for($j=1;$j<=10;$j++){

				if($a['po_quantity_size'.$j.'_id']){
					echo "<td>";
					fabricOrderInputOption($tbl,$j,"",$fabric);
					echo "</td>";
				}
			}

			?>

		</tr>
		<?php
		$i++;
		}
		?>
	</table>
	<?php
	function fabricOrderInputOption($tbl,$j,$val,$fabric){
		$name=$tbl."_".$j."[]";
		if($tbl=="foft"){
			$selectedId = $val;
			$customQuery = " WHERE fabric_type_active='1' ";
			createSelectOptions('fabric_type', 'fabric_type_id', 'fabric_type_name', $customQuery, $selectedId, 'po_fabric_type_id', " class=''");
		}else if($tbl=="fofc"){
			$selectedId = $val;
			$customQuery = " where fco_active='1'";
			createSelectOptions('fabric_composition_options', 'fco_id', 'fco_name', $customQuery, $selectedId, 'po_fco_id', "class=''");
		}else if($tbl=="fog" || $tbl=="fod" || $tbl=="foc" || $tbl=="for"){
			echo "<input id='$fabric' class='$tbl' type='text' name='$name' size='8' maxlength='8' value=''/>";
		}else if($tbl=="fodu"){
			echo "<select name='".$tbl."_".$j."[]' >";
			echo "<option value='inch'";
			if($val=="inch"){
				echo " selected='selected' ";
			}
			echo " >inch</option>";
			echo "<option value='cm'";
			if($val=="cm"){
				echo " selected='selected' ";
			}
			echo " >cm</option>";
			echo "</select>";
		}else if($tbl=="fodf"){
			echo "<select name='".$tbl."_".$j."[]' >";
			echo "<option value='Open'";
			if($val=="Open"){
				echo " selected='selected' ";
			}
			echo " >Open</option>";
			echo "<option value='Tublar'";
			if($val=="Tublar"){
				echo " selected='selected' ";
			}
			echo " >Tublar</option>";
			echo "</select>";
		}
	}

	?>
<script type="text/javascript">
$('select[name=fo_fab_cal_type]').change(function(){
	var fo_fab_cal_type= $(this).val();
	//alert(fo_fab_cal_type);
	if(fo_fab_cal_type=='Size wise'){
		$("input[name=fo_avg_consperdoz]").val('');
		$("input[name=fo_avg_consperdoz]").attr('readonly','readonly');
		$("input[class=foc]").removeAttr('readonly').val('');

		$("input[name=fo_avg_dia]").val('');
		$("input[name=fo_avg_dia]").attr('readonly','readonly');
		$("input[class=fod]").removeAttr('readonly').val('');
		$("table[id=fab_avg_cons_table]").hide();

	}else{ //fo_fab_cal_type=='Average'
		$("table[id=fab_avg_cons_table]").show();
		$("input[name=fo_avg_consperdoz]").removeAttr('readonly');
		$("input[class=foc]").attr('readonly','readonly');
		$("input[name=fo_avg_dia]").removeAttr('readonly');
		$("input[class=fod]").attr('readonly','readonly');
	}
});
$("input[name=fo_avg_consperdoz]").change(function(){
	var fo_avg_consperdoz=$(this).val();
	//alert(fo_avg_consperdoz);
	$("input[class=foc]").val(fo_avg_consperdoz);

});

$("input[name=fo_avg_dia]").change(function(){
	var fo_avg_dia=$(this).val();
	//alert(fo_avg_consperdoz);
	$("input[class=fod]").val(fo_avg_dia);
});
$("form").validationEngine();
</script>	
</div>
