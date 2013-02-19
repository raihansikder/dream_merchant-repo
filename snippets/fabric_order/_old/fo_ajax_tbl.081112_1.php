<?php 
include_once('../../config.php');
$client_id=$_REQUEST[client_id];
$param=$_REQUEST[param];
$po_id=$_REQUEST[po_id];
$fabric=$_REQUEST[fabric];

$r=mysql_query("Select * from purchaseorder where po_id='$po_id'")or die(mysql_error());	
$a=mysql_fetch_assoc($r);

$r=mysql_query("Select * from podetails where podetails_po_id='$po_id' ")or die(mysql_error());
$podetails_rows=mysql_num_rows($r);
//echo $podetails_rows."<br />";
if($podetails_rows > 0){	
	$a_podetails=mysql_fetch_rowsarr($r);
}	

?>
<style>
select {
	/*width: 60px;*/
}
;
</style>
<div style="background-color:#FFC; margin-top:10px;">
<h2><?php echo $fabric;?> Consumption details</h2>
<div class="clear"></div>
<input type="hidden" name="fo_po_id[]" value="<?php echo $po_id;?>" /> 
<input type="hidden" name="fo_po_uid[]" value="<?php echo $a["po_uid"];?>" /> 
<input type="hidden" name="fo_client_id[]" value="<?php echo $a["po_client_id"];?>" /> 
<input type="hidden" name="fo_fabric[]" value="<?php echo $fabric;?>" /> 

<select name='cal_type' id="<?php echo $fabric;?>" class="validate[required]">
  <option value="Size wise">Size wise</option>
  <option value="Average">Average</option>
</select>
<input type="hidden" id="fo_fab_cal_type_<?php echo $fabric; ?>" name="fo_fab_cal_type[]" value="Size wise" /> 
<div class="clear"></div>
<table id="fab_avg_cons_table_<?php echo $fabric; ?>" border="0" cellpadding="0" cellspacing="0" style="display:none;">
  <tr>
    <td><?php echo $fabric;?> A verage Consumption/Dozen: </td>
    <td><input name="fo_avg_consperdoz[]" id="<?php echo "fo_avg_consperdoz_".$fabric;?>" type="text"  size="8" maxlength="8" value="" class="validate[required,custom[number]]" readonly="readonly"/></td>
  </tr>
</table>
<div class="clear"></div>
<table>
  <tr style="vertical-align:top;">
    <td><b>Color:</b><br/></td>
    <td><b>Pantone no:</b><br/></td>
    <td>
        <span style='float:left;'>Fabrict type:<br/>
        </span>
        </td>
        <td>
        <span style='float:left;'>Fabrict Composition:<br/>
        </span>
        </td>
        <td>
        <span style='float:left;'>GSM:<br/>
        </span>
        </td>
        <td>
        <span style='float:left;'>Dia Unit:<br/>
        </span>
        </td>
        <td>
        <span style='float:left;'>Dia Form:<br/>
        </span>
        </td>
        <td>
      <span style='float:left;'>Remark:<br/>
      </span>
        
	</td>
 </tr>   
  <tr style="vertical-align:top;">
    <td><input size="20" maxlength="40" name="color[]"  value="" class="validate[required]" /></td>
    <td><input type="text" name="pantone_no[]" size="12" value="" class="validate[required]"/></td>
    <td>
        <span style='float:left;'>
        <?php 
        $selectedId = $a["po_fabric_type_id"];
            $customQuery = " WHERE fabric_type_active='1' ";
            createSelectOptions('fabric_type', 'fabric_type_id', 'fabric_type_name', $customQuery, $selectedId, 'fo_fabric_type_id[]', " class=''");
        ?>
        </span>
      </td>
        <td>
        <span style='float:left;'>
        <?php 
        $selectedId=$a["po_fco_id"];
		$customQuery=" where fco_active='1'";
		createSelectOptions('fabric_composition_options','fco_id','fco_name',$customQuery,$selectedId,'fo_fco_id[]',"class='validate[required]'");
        ?>
        </span>
      </td>
        <td>
        <span style='float:left;'>
        <input size="5" maxlength="10" name="fo_gsm[]"  value="<?php echo $a["po_gsm"]; ?>" class="validate[required]" />
        </span>
      </td>
        <td>
        <span style='float:left;'>
        <?php 
		echo "<select name='fo_dia_unit[]' >";
		echo "<option value='inch'";
		if($val=="inch"){echo " selected='selected' ";}
		echo " >inch</option>";
		echo "<option value='cm'";
		if($val=="cm"){echo " selected='selected' ";}
		echo " >cm</option>";
		echo "</select>";
        ?>
        </span>
        </td>
        <td>
        <span style='float:left;'>
        <?php 
		echo "<select name='fo_dia_form[]' >";
		echo "<option value='Open'";
		if($val=="Open"){echo " selected='selected' ";}
		echo " >Open</option>";
		echo "<option value='Tublar'";
		if($val=="Tublar"){echo " selected='selected' ";}
		echo " >Tublar</option>";
		echo "</select>";
        ?>
        </span>
        </td>
        <td>
      <span style='float:left;'>
        <textarea name="fo_remark[]" cols="30" rows="1" class=""></textarea>
      </span>
        
	</td>
 </tr>   
</table>
<div class="clear"></div>
<table id="quantitydetails_table" border="0" cellpadding="0" cellspacing="0">
  <tr style="font-weight:bold; background:#E6E6E6">
    <td width="20%" >&nbsp;</td>
    <td width="25%" >&nbsp;</td>
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
    
    <td width="3%" >Total</td>
    <td width="3%" >Action</td>
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
    <td><!-- Hidden form values : start -->
      <input name="<?php echo $tbl; ?>_po_id[]" type="hidden" value="<?php echo $po_id; ?>" />
      <input name="<?php echo $tbl; ?>_po_uid[]" type="hidden" value="<?php echo $a["po_uid"];?>" />
      <input name="<?php echo $tbl; ?>_client_id[]" type="hidden" value="<?php echo $a["po_client_id"];?>" />     
      <input name="<?php echo $tbl; ?>_fabric[]" type="hidden" value="<?php echo $fabric; ?>" />
      
      <!-- Hidden form values : end -->
    <input size="20" maxlength="40" name="placeholder"  value="" disabled="disabled"  />  
    </td>
    <td align="right"><?php echo $tablenametArr[$i];?></td>
    <?php 
	for($j=1;$j<=10;$j++){

		if($a['po_quantity_size'.$j.'_id']){
			echo "<td>";
			fabricOrderInputOption($tbl,$j,"",$fabric); 
			echo "</td>";
		}
	}
	
    ?>
    <td></td>
    <td></td>
  </tr>
  <?php	
	$i++;} 
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
		if($val=="inch"){echo " selected='selected' ";}
		echo " >inch</option>";
		echo "<option value='cm'";
		if($val=="cm"){echo " selected='selected' ";}
		echo " >cm</option>";
		echo "</select>";
	}else if($tbl=="fodf"){
		echo "<select name='".$tbl."_".$j."[]' >";
		echo "<option value='Open'";
		if($val=="Open"){echo " selected='selected' ";}
		echo " >Open</option>";
		echo "<option value='Tublar'";
		if($val=="Tublar"){echo " selected='selected' ";}
		echo " >Tublar</option>";
		echo "</select>";
	}
}

?>
<div class="clear"></div>
<input class="button bgred" type="button" id="<?php echo $fabric; ?>" name="RemoveFabric" value="Remove <?php echo $fabric; ?>" />
<script type="text/javascript">
$('input[id=<?php echo $fabric; ?>][name=RemoveFabric]').click(function(){
	$('div[id=<?php echo $fabric; ?>]').html('');
	$('input[name=AddFabric][id=<?php echo $fabric; ?>]').show();

});
$('select[name=cal_type][id=<?php echo $fabric; ?>]').change(function(){
	var fo_fab_cal_type= $(this).val();
	//alert(fo_fab_cal_type);
	$("input[id=fo_fab_cal_type_<?php echo $fabric; ?>]").val(fo_fab_cal_type);
	//alert('test');
	//alert(fo_fab_cal_type);
	if(fo_fab_cal_type=='Size wise'){		
		$("input[id=<?php echo "fo_avg_consperdoz_".$fabric;?>]").val('');
		$("input[id=<?php echo "fo_avg_consperdoz_".$fabric;?>]").attr('readonly','readonly');
		$("input[class=foc][id=<?php echo $fabric; ?>]").removeAttr('readonly').val('');
		$("table[id=fab_avg_cons_table_<?php echo $fabric; ?>]").hide();
		
	}else{ //fo_fab_cal_type=='Average'
		$("table[id=fab_avg_cons_table_<?php echo $fabric; ?>]").show();
		$("input[id=<?php echo "fo_avg_consperdoz_".$fabric;?>]").removeAttr('readonly');
		$("input[class=foc][id=<?php echo $fabric; ?>]").attr('readonly','readonly');
	}
});
$("input[id=<?php echo "fo_avg_consperdoz_".$fabric;?>]").change(function(){
	var fo_avg_consperdoz=$(this).val();
	//alert(fo_avg_consperdoz);	
	$("input[class=foc][id=<?php echo $fabric; ?>]").val(fo_avg_consperdoz);
});
</script>
<div class="clear"></div>
</div>