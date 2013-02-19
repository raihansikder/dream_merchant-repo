<?php
include("config.php");

$valid = true;
$alert = array();
$param = $_REQUEST['param'];
$facility_id = $_REQUEST['facility_id'];

/* * **************************************** */
/* Delete
 /****************************************** */
if ($_REQUEST[param] == 'delete' && is_numeric($facility_id)) {
	if (hasPermission('system', 'delete', $_SESSION[current_user_id])) {
		$sql = "DELETE FROM facility WHERE facility_id='$facility_id'";
		$r = mysql_query($sql) or die(mysql_error());
		//$a = mysql_fetch_assoc($r);
		//$rows=mysql_num_rows($r);
		if (mysql_affected_rows()) {
			header("location:facility.php?param=delete_success");
		}
	} else {
		$valid = false;
		array_push($alert, "You don't have permission to delete facility");
	}
}
/* * **************************************** */
/* Add/Edit
 /****************************************** */

if (!strlen($facility_id)) {
	$param = "add";
} else {
	$param = "edit";
}
if (isset($_POST[submit])) {
	if ($param == 'add' || $param == 'edit') {

		$exception_field = array('submit', 'param');
		/*
		 * 	server side validation starts
		*/
		/*         * ********************************** */
		/*
		 No server side validation code yet written
		*/
		/*         * ********************************** */
		if ($valid) {
			if ($param == 'add') {
				/*
				 * 	Check whether current facility has permission to add client
				*/
				if (hasPermission('system', 'add', $_SESSION[current_user_id])) {
					/*
					 * 	Create the insert query substring.
					*/
					$str = createMySqlInsertString($_POST, $exception_field);
					$str_k = $str['k'];
					$str_v = $str['v'];
					/*                     * ********************************** */

					$sql = "INSERT INTO facility($str_k,facility_updated_datetime) values ($str_v,now())";
					mysql_query($sql) or die(mysql_error() . "<b>Query:</b><br />$sql<br />");
					$facility_id = mysql_insert_id();
					$param = 'edit';
					array_push($alert, "The facility has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to add facility");
				}
			} else if ($param == 'edit') {
				/*
				 * 	Check whether current facility has permission to edit client
				*/
				if (hasPermission('system', 'edit', $_SESSION[current_user_id])) {
					/*
					 * 	Create the update query substring.
					*/
					$str = createMySqlUpdateString($_REQUEST, $exception_field);
					/*                     * ********************************** */
					$sql = "UPDATE facility set $str,facility_updated_datetime=now() where facility_id='" . $_REQUEST['facility_id'] . "'";
					mysql_query($sql) or die(mysql_error());
					array_push($alert, "The facility has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to edit facility");
				}
			}
			//echo $sql;
		}
	}
}


/* * **************************************** */
if ($facility_id) {
	$sql = "SELECT * FROM facility WHERE facility_id='$facility_id'";
	$r = mysql_query($sql) or die(mysql_error());
	$a = mysql_fetch_assoc($r);
	$rows = mysql_num_rows($r);
}

$sql = "SELECT * FROM facility";
$r = mysql_query($sql) or die(mysql_error());
$arr = mysql_fetch_rowsarr($r);
$rows = mysql_num_rows($r);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once('inc.head.php') ?>
</head>
<body>
	<!-- JQuery Modal Popup for delete : Start --->
	<div id="dialog" title="Confirm" style="display: none;">
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
			<input name="param" type="hidden" value="delete" />
			<input name="facility_id" type="hidden" value="" />
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
			<?php include("snippets/system/systemmenu.php"); ?>
			<div id="mid">
				<div class="clear"></div>
				<div id="left_m">
					<table width="100%">
						<tr>
							<td>
								<h2>
									<?php echo ucfirst($param); ?>
									Facility
								</h2>
							</td>
							<td align="right">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>">[+] Add new Facility</a>
							</td>
						</tr>
					</table>
					<?php printAlert($valid, $alert); ?>
					<form action="#" method="post" enctype="multipart/form-data">
						<table width="100%">
							<tr>
								<td>
									Facility Name <br />
									<input name="facility_name" class='validate[required]' value="<?php echo addEditInputField('facility_name') ?>" />
								</td>
							</tr>
							<tr>
								<td>
									Facility Location <br />
									<input name="facility_location" class='validate[required]' value="<?php echo addEditInputField('facility_location') ?>" />
								</td>
							</tr>
							<tr>
								<td>
									Status<br />
									<?php
									$selectedId = addEditInputField('facility_active');
									$customQuery = " WHERE option_group='active_status' AND option_active='1' ";
									createSelectOptions('options', 'option_value', 'option_name', $customQuery, $selectedId, 'facility_active', "  class='validate[required]'");
									?>
								</td>
							</tr>
						</table>
						<input name="submit" type="submit" class="bgblue button" value="Save" />
						<input type="hidden" name="facility_updated_by_user_id" value="<?php echo $_SESSION["current_user_id"]; ?>" />
						<?php if ($facility_id) { ?>
						<input type="hidden" name="facility_id" value="<?php echo $facility_id; ?>" />
						<?php } ?>
					</form>
				</div>
				<div id="right_m">
					<!--<h2>List of Departments</h2>-->
					<table id="datatable" width="100%">
						<thead>
							<tr>
								<td>Facility - [id]</td>
								<td>Name</td>
								<td>Location</td>
								<td>Updated at</td>
								<td>Status</td>
								<td width="100px">Action</td>
							</tr>
						</thead>
						<tbody>
							<?php for ($i = 0; $i < $rows; $i++) { ?>
							<tr>
								<td>
									<?php echo $arr[$i][facility_id]; ?>
								</td>
								<td>
									<?php echo $arr[$i][facility_name]; ?>
								</td>
								<td>
									<?php echo $arr[$i][facility_location]; ?>
								</td>
								<td>
									<?php echo $arr[$i][facility_updated_datetime]; ?>
								</td>
								<td>
									<?php echo getActiveStatus($arr[$i][facility_active]); ?>
								</td>
								<td>
									<?php
									if (hasPermission('system', 'view', $_SESSION[current_user_id])) {
										//if($arr[$i][facility_first_name]!='superadmin'){
										echo "<a href='facility.php?facility_id=" . $arr[$i][facility_id] . "&param=view'>View</a>";
										echo " | ";
										//echo "<a href='facility.php?facility_id=".$arr[$i][facility_id]."&param=delete'>Delete</a>";
										echo "<a class='delete' id='" . $arr[$i][facility_id] . "' href='#'>Delete</a>";
										//}
									}
									?>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php'); ?>
		</div>
	</div>
</body>
</html>
<script>
    $('document').ready(function(){
        $("#dialog" ).dialog({ autoOpen: false });
        $('a.delete').click(function(){
            var facility_id = $(this).attr('id');
            //alert(facility_id);
            $('input[name=facility_id]').val(facility_id);
            $( "#dialog" ).dialog('open');
        });
    });
</script>
