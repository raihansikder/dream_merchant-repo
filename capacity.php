<?php 
include("config.php");

$valid=true;
$alert=array();
$param=$_REQUEST['param'];
$capacity_id=$_REQUEST['capacity_id'];

/*******************************************/
/* Delete
/*******************************************/
if($_REQUEST[param]=='delete' && is_numeric($capacity_id)){
	if(hasPermission('system','delete',$_SESSION[current_user_id])){
		$sql = "DELETE FROM capacity WHERE capacity_id='$capacity_id'";
		$r = mysql_query($sql)or die(mysql_error());
		//$a = mysql_fetch_assoc($r);
		//$rows=mysql_num_rows($r);
		if(mysql_affected_rows()){
			header("location:capacity.php?param=delete_success");
		}
	}else{
		$valid=false;
		array_push($alert,"You don't have permission to delete capacity");
	}
}
/*******************************************/
/* Add/Edit
/*******************************************/

if(!strlen($capacity_id)){
	$param="add";
}else{
	$param="edit";
}
if(isset($_POST[submit])){
	if($param=='add'||$param=='edit'){

		$exception_field=array('submit','param');
		/*
		*	server side validation starts
		*/
		/*************************************/
		/*
		No server side validation code yet written
		*/
		/*************************************/
		if($valid){
			if($param=='add'){
				/*
				*	Check whether current capacity has permission to add client
				*/
				if(hasPermission('system','add',$_SESSION[current_user_id])){
					/*
					*	Create the insert query substring.
					*/
					$str=createMySqlInsertString($_POST,$exception_field);
					$str_k=$str['k'];
					$str_v=$str['v'];
					/*************************************/
						
					$sql="INSERT INTO capacity($str_k,capacity_updated_datetime) values ($str_v,now())";
					mysql_query($sql) or die(mysql_error()."<b>Query:</b><br>$sql<br>");
					$capacity_id= mysql_insert_id();
					$param='edit';
					array_push($alert,"The capacity has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to add capacity");
				}
			}else if($param=='edit'){
				/*
				*	Check whether current capacity has permission to edit client
				*/
				if(hasPermission('system','edit',$_SESSION[current_user_id])){
					/*
					*	Create the update query substring.
					*/
					$str=createMySqlUpdateString($_REQUEST,$exception_field);
					/*************************************/
					$sql="UPDATE capacity set $str,capacity_updated_datetime=now() where capacity_id='".$_REQUEST['capacity_id']."'";
					mysql_query($sql) or die(mysql_error());
					array_push($alert,"The capacity has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to edit capacity");
				}
			}
			//echo $sql;
		}
	}
}


/*******************************************/
if($capacity_id){
	$sql = "SELECT * FROM capacity WHERE capacity_id='$capacity_id'";
	$r = mysql_query($sql)or die(mysql_error());
	$a = mysql_fetch_assoc($r);
	$rows=mysql_num_rows($r);
}

$sql = "SELECT * FROM capacity";
$r = mysql_query($sql)or die(mysql_error());
$arr = mysql_fetch_rowsarr($r);
$rows=mysql_num_rows($r);


//-------------------------------------------



?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><head><?php include_once('inc.head.php')?></head><body>	<!-- JQuery Modal Popup for delete : Start --->	<div id="dialog" title="Confirm" style="display: none;">		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">			<input name="param" type="hidden" value="delete" />			<input name="capacity_id" type="hidden" value="" />			<input name="confirm_checkbox" type="checkbox" value="confirmed" class="validate[required]" />			<?php echo $defaultConfirmationMsg; ?>			<div class="clear"></div>			<input type="submit" name="confirm" value="confirm" class="bgblue button" />		</form>	</div>	<!-- JQuery Modal Popup for delete : Ends --->	<div id="wrapper">		<div id="container">			<div id="top1">				<?php include('top.php');?>			</div>			<?php include("snippets/system/systemmenu.php");?>			<div id="mid">				<div class="clear"></div>				<div id="left_m">					<table width="100%">						<tr>							<td>								<h2>									<?php echo ucfirst($param); ?>									Capacity								</h2>							</td>							<td align="right">								<a href="<?php echo $_SERVER['PHP_SELF']; ?>">[+] Add new capacity</a>							</td>						</tr>					</table>					<?php printAlert($valid,$alert);?>					<form action="#" method="post" enctype="multipart/form-data">						<table width="100%">							<tr>								<td>									Facility: <br><?php 
									$selectedId=addEditInputField('capacity_facility_id');
									$customQuery = " WHERE facility_active='1' ";
									createSelectOptions('facility','facility_id','facility_name',$customQuery,$selectedId,'capacity_facility_id', "  class='validate[required]'");?>																</td>							</tr>							<tr>								<td>									Capacity Per Line Per Hour: <br> <input name="capacity_quantity" class='validate[required]' value="<?php echo addEditInputField('capacity_quantity') ?>" />																</td>							</tr>							<tr>								<td>									Number Of Production Line: <br> <input name="capacity_number_of_production_line" class='validate[required]' value="<?php echo addEditInputField('capacity_number_of_production_line') ?>" />																</td>							</tr>							<tr>								<td>									Year: <br> <!--            <input name="capacity_year" class='validate[required]' value="<?php echo addEditInputField('capacity_year') ?>"  />--> <select name="capacity_year" id="capacity_year">											<option value="2011" selected="selected">2011</option>											<option value="2012">2012</option>											<option value="2013">2013</option>											<option value="2014">2014</option>											<option value="2015">2015</option>											<option value="2016">2016</option>											<option value="2017">2017</option>											<option value="2018">2018</option>											<option value="2019">2019</option>											<option value="2020">2020</option>											<option value="2021">2021</option>											<option value="2022">2022</option>											<option value="2023">2023</option>											<option value="2024">2024</option>											<option value="2025">2025</option>											<option value="2026">2026</option>											<option value="2027">2027</option>											<option value="2028">2028</option>											<option value="2029">2029</option>											<option value="2030">2030</option>									</select>																</td>							</tr>							<tr>								<td>									Month: <br> <!--<input name="capacity_month" class='validate[required]' value="<?php echo addEditInputField('capacity_month') ?>"  />--> <select name="capacity_month" id="capacity_month">											<option value="1" selected="selected">January</option>											<option value="2">February</option>											<option value="3">March</option>											<option value="4">April</option>											<option value="5">May</option>											<option value="6">June</option>											<option value="7">July</option>											<option value="8">August</option>											<option value="9">September</option>											<option value="10">October</option>											<option value="11">November</option>											<option value="12">December</option>									</select>																</td>							</tr>							<tr>								<td>									Workdays/Month: <br> <input name="capacity_no_of_workdays_in_month" class='validate[required]' value="<?php echo addEditInputField('capacity_no_of_workdays_in_month') ?>" />																</td>							</tr>							<tr>								<td>									Workhours/Day: <br> <input name="capacity_total_work_hour_per_day" class='validate[required]' value="<?php echo addEditInputField('capacity_total_work_hour_per_day') ?>" />																</td>							</tr>							<tr>								<td>									Status<br><?php 
									$selectedId=addEditInputField('capacity_active');
									$customQuery = " WHERE option_group='active_status' AND option_active='1' ";
			createSelectOptions('options','option_value','option_name',$customQuery,$selectedId,'capacity_active', "  class='validate[required]'");?>																</td>							</tr>						</table>						<input name="submit" type="submit" class="bgblue button" value="Save" />						<input type="hidden" name="capacity_updated_by_user_id" value="<?php echo $_SESSION["current_user_id"]; ?>" />						<?php if($capacity_id){?>						<input type="hidden" name="capacity_id" value="<?php echo $capacity_id; ?>" />						<?php }?>					</form>				</div>				<div id="right_m">					<!--<h2>List of Departments</h2>-->					<table id="datatable" width="100%">						<thead>							<tr align="center">								<td>Capacity-[id]</td>								<td>Total Capacity</td>								<td>Facility Name</td>								<td>Quantity</td>								<td>Number Of Production Line</td>								<td>Year</td>								<td>Month</td>								<td>Workdays/Month</td>								<td>Workhours/Day</td>								<td>Status</td>								<td width="100px">Action</td>							</tr>						</thead>						<tbody>							<?php for($i=0;$i<$rows;$i++){?>							<tr align="center">								<td>									<?php echo $arr[$i][capacity_id];?>								</td>								<td>									<?php echo getFacilityMonthlyCapacity($arr[$i][capacity_facility_id],$arr[$i][capacity_year],$arr[$i][capacity_month])?>								</td>								<td>									<?php echo getFacilityNameFrmId($arr[$i][capacity_facility_id]);?>								</td>								<td>									<?php echo $arr[$i][capacity_quantity];?>								</td>								<td>									<?php echo $arr[$i][capacity_number_of_production_line];?>								</td>								<td>									<?php echo $arr[$i][capacity_year];?>								</td>								<td>									<?php echo getmonthNameFromNumber($arr[$i][capacity_month]);?>								</td>								<td>									<?php echo $arr[$i][capacity_no_of_workdays_in_month];?>								</td>								<td>									<?php echo $arr[$i][capacity_total_work_hour_per_day];?>								</td>								<td>									<?php echo getActiveStatus($arr[$i][capacity_active]);?>								</td>								<td>									<?php 
									if(hasPermission('system', 'view', $_SESSION[current_user_id])){
										//if($arr[$i][capacity_first_name]!='superadmin'){
										echo "<a href='capacity.php?capacity_id=".$arr[$i][capacity_id]."&param=view'>View</a>";
										echo " | ";
										//echo "<a href='capacity.php?capacity_id=".$arr[$i][capacity_id]."&param=delete'>Delete</a>";
										echo "<a class='delete' id='".$arr[$i][capacity_id]."' href='#'>Delete</a>";
										//}
									}
									?>								</td>							</tr>							<?php } ?>						</tbody>					</table>				</div>			</div>		</div>		<div id="footer">			<?php include('footer.php');?>		</div>	</div></body></html><script>
$('document').ready(function(){
	$("#dialog" ).dialog({ autoOpen: false, });
	$('a.delete').click(function(){
		var capacity_id = $(this).attr('id');
		//alert(capacity_id);
		$('input[name=capacity_id]').val(capacity_id);
		$( "#dialog" ).dialog('open');
	});
});
</script>