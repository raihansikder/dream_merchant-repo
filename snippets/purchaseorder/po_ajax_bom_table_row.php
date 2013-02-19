<?php
include_once('../../config.php');
$param=$_REQUEST[param];
if($param=='add'){
	$random_tr_id=makeRandomKey();
	?>
<tr class="bom_tr" id="<?php echo $random_tr_id;?>">
	<td>
		<input id="<?php echo $random_tr_id;?>" name="bom_material[]" type="text" class="autocomplete_material validate[required]" />
	</td>
	<td>
		<input type="text" name="bom_quantity_per_pc[]" size="8" maxlength="8" value="" class='validate[required, custom[number]]' />
	</td>
	<td>
		<input type="text" name="bom_wastage[]" class='validate[required, custom[number]]' size="8" maxlength="8" value="" />
	</td>
	<td>--</td>
	<td>
		<input type="text" name="bom_rate_per_dozen[]" size="8" maxlength="8" value="" class='validate[required, custom[number]]' />
	</td>
	<td>--</td>
	<td>
		<?php
		 $customQuery = " WHERE supplier_active='1' ";
		 createSelectOptions('supplier', 'supplier_id', 'supplier_company_name', $customQuery, $selectedId, 'bom_supplier_id[]', "class='validate[required]'");
		 ?>
	</td>
	<td>
		<script>
	$(function() {
		$("#bom_delivery_date_<?php echo $random_tr_id; ?>").datepicker({ dateFormat: "yy-mm-dd" });
	});
	 </script>
		<input name="bom_delivery_date[]" id="bom_delivery_date_<?php echo $random_tr_id; ?>" type="text" value="" size="15" readonly="readonly" class='validate[required, custom[date]]' />
	</td>
	<td>
		<input type="button" class='remove_row_bom' id="<?php echo $random_tr_id;?>" value="Remove" />
	</td>
</tr>
<script>
	$("input[class=remove_row_bom][id=<?php echo $random_tr_id;?>]").click(function(){
		  $('tr[class=bom_tr][id=<?php echo $random_tr_id;?>]').remove();
	});
	$(function() {
		$( ".autocomplete_material" ).autocomplete({
			source: "snippets/common/ajax_autosearch_material.php",
			minLength: 1,
			select: function( event, data ) {
			}
		});
	});
</script>
<?php } ?>

