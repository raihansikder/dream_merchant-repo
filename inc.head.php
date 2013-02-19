<?php include_once('inc.css-js.php');?>
<script type="text/javascript">



$(document).ready(function($) {
	/*
	*	Initiate Auto grow on all text area
	*/
	$('textarea').autogrow();
	/*
	*	Initiate the forms validator Engine for all HTML FORM throughout the sytem
	*/
	$("form").validationEngine();
	/*******************************************************************************/	
	/*Initiate facebox */
	$('a[rel*=facebox]').facebox();
	/*******************************************************************************/	
	/*Initiate datatable */
	$('table#datatable').dataTable({
    	"sPaginationType": "full_numbers",
		"aaSorting": [[ 0, "desc" ]]
  	});
	
	$('table#datatable_min').dataTable({
    	//"sPaginationType": "two_button",
		"bPaginate": false,
		"bLengthChange": false,
		"bInfo": false,
		"bFilter": false,
		"aaSorting": [[ 0, "desc" ]]
  	});

	/*******************************************************************************/	
	/*Initiate datepicker */
	$("#datepicker").datepicker({ dateFormat: "yy-mm-dd" });
	/*******************************************************************************/		
	/*Disable all input, select, checkbox, textbox if user is seeing 'view' paramerter*/
	/*
	var pageparam = $('input[name=param]').val();
	if(pageparam=='view'){
		$('input').attr('readonly', true);
		//$('input').attr('readonly', true);
	}
	*/
	$(".multiselect").multiselect();
})
/*
* 	Common js functions
*/
function countChecked() {
  var n = $("input:checked").length;
  return n;
}
/**************************************/
</script>
