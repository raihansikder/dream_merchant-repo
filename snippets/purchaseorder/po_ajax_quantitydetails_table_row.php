<?php
include_once('../../config.php');
$param=$_REQUEST[param];
if($param=='add'){
$random_tr_id=makeRandomKey();
?>
    <tr class="podetails_tr" id="<?php echo $random_tr_id;?>">
      <td><input type="text" name="podetails_color[]" id="<?php echo $random_tr_id;?>" size="20" class="autocomplete_colour validate[required]" maxlength="40" /></td>
      <td><?php
			//$selectedId = $a_p['podetails_color_type_id'];
			$customQuery = " WHERE color_type_active='1' ";
			createSelectOptions('color_type', 'color_type_id', 'color_type_name', $customQuery, $selectedId, 'podetails_color_type_id[]', " class='validate[required]'");
			?></td>
      <td><input type="text" name="podetails_pantone_no[]" size="12" maxlength="12" /></td>
      <td><input type="text" name="podetails_1[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_2[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_3[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_4[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_5[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_6[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_7[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_8[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_9[]" value="" size="8" maxlength="8" /></td>
      <td><input type="text" name="podetails_10[]" value="" size="8" maxlength="8" /></td>
      <td></td>
      <td><input type="button" class='remove_row_podetails' id="<?php echo $random_tr_id;?>" value="Remove" /></td>
    </tr>
    <script>
	$("input[class=remove_row_podetails][id=<?php echo $random_tr_id;?>]").click(function(){
		  $('tr[class=podetails_tr][id=<?php echo $random_tr_id;?>]').remove();
	});
	/*
	$(function() {
		var availableTags = [
			"ActionScript",
			"AppleScript",
			"Asp",
			"BASIC",
			"C",
			"C++",
			"Clojure",
			"COBOL",
			"ColdFusion",
			"Erlang",
			"Fortran",
			"Groovy",
			"Haskell",
			"Java",
			"JavaScript",
			"Lisp",
			"Perl",
			"PHP",
			"Python",
			"Ruby",
			"Scala",
			"Scheme"
		];
		$( ".autocomplete_colour" ).autocomplete({
			source: availableTags
		});
	});
	*/
	$(function() {
		$( ".autocomplete_colour" ).autocomplete({
			source: "snippets/common/ajax_autosearch_colour.php",
			minLength: 2,
			select: function( event, data ) {
				//alert(data)
			}
		});
	});
</script>
<?php } ?>



