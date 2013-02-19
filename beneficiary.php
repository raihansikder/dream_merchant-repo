<?php
include("config.php");

$valid = true;
$alert = array();
$param = $_REQUEST['param'];
$beneficiary_id = $_REQUEST['beneficiary_id'];

/* * **************************************** */
/* Delete
 /****************************************** */
if ($_REQUEST[param] == 'delete' && is_numeric($beneficiary_id)) {
	if (hasPermission('system', 'delete', $_SESSION[current_user_id])) {
		$sql = "DELETE FROM beneficiary WHERE beneficiary_id='$beneficiary_id'";
		$r = mysql_query($sql) or die(mysql_error());
		//$a = mysql_fetch_assoc($r);
		//$rows=mysql_num_rows($r);
		if (mysql_affected_rows()) {
			header("location:beneficiary.php?param=delete_success");
		}
	} else {
		$valid = false;
		array_push($alert, "You don't have permission to delete beneficiary");
	}
}
/* * **************************************** */
/* Add/Edit
 /****************************************** */

if (!strlen($beneficiary_id)) {
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
				 * 	Check whether current beneficiary has permission to add client
				*/
				if (hasPermission('system', 'add', $_SESSION[current_user_id])) {
					/*
					 * 	Create the insert query substring.
					*/
					$str = createMySqlInsertString($_POST, $exception_field);
					$str_k = $str['k'];
					$str_v = $str['v'];
					/*                     * ********************************** */

					$sql = "INSERT INTO beneficiary($str_k,beneficiary_updated_datetime) values ($str_v,now())";
					mysql_query($sql) or die(mysql_error() . "<b>Query:</b><br />$sql<br />");
					$beneficiary_id = mysql_insert_id();
					$param = 'edit';
					array_push($alert, "The beneficiary has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to add beneficiary");
				}
			} else if ($param == 'edit') {
				/*
				 * 	Check whether current beneficiary has permission to edit client
				*/
				if (hasPermission('system', 'edit', $_SESSION[current_user_id])) {
					/*
					 * 	Create the update query substring.
					*/
					$str = createMySqlUpdateString($_REQUEST, $exception_field);
					/*                     * ********************************** */
					$sql = "UPDATE beneficiary set $str,beneficiary_updated_datetime=now() where beneficiary_id='" . $_REQUEST['beneficiary_id'] . "'";
					mysql_query($sql) or die(mysql_error());
					array_push($alert, "The beneficiary has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to edit beneficiary");
				}
			}
			//echo $sql;
		}
	}
}


/* * **************************************** */
if ($beneficiary_id) {
	$sql = "SELECT * FROM beneficiary WHERE beneficiary_id='$beneficiary_id'";
	$r = mysql_query($sql) or die(mysql_error());
	$a = mysql_fetch_assoc($r);
	$rows = mysql_num_rows($r);
}

$sql = "SELECT * FROM beneficiary";
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
			<input name="beneficiary_id" type="hidden" value="" />
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
									Beneficiary
								</h2>
							</td>
							<td align="right">
								<a href="<?php echo $_SERVER['PHP_SELF']; ?>">[+] Add new Beneficiary</a>
							</td>
						</tr>
					</table>
					<?php printAlert($valid, $alert); ?>
					<form action="#" method="post" enctype="multipart/form-data">
						<table width="100%">
							<tr>
								<td>
									Beneficiary Name <br />
									<input name="beneficiary_name" class='validate[required]' value="<?php echo addEditInputField('beneficiary_name') ?>" />
								</td>
							</tr>
							<tr>
								<td>
									Beneficiary Value <br />
									<input name="beneficiary_value" class='validate[required]' value="<?php echo addEditInputField('beneficiary_value') ?>" />
								</td>
							</tr>
							<tr>
								<td>
									Status<br />
									<?php
									$selectedId = addEditInputField('beneficiary_active');
									$customQuery = " WHERE option_group='active_status' AND option_active='1' ";
									createSelectOptions('options', 'option_value', 'option_name', $customQuery, $selectedId, 'beneficiary_active', "  class='validate[required]'");
									?>
								</td>
							</tr>
						</table>
						<input name="submit" type="submit" class="bgblue button" value="Save" />
						<input type="hidden" name="beneficiary_updated_by_user_id" value="<?php echo $_SESSION["current_user_id"]; ?>" />
						<?php if ($beneficiary_id) { ?>
						<input type="hidden" name="beneficiary_id" value="<?php echo $beneficiary_id; ?>" />
						<?php } ?>
					</form>
				</div>
				<div id="right_m">
					<!--<h2>List of Departments</h2>-->
					<table id="datatable" width="100%">
						<thead>
							<tr>
								<td>Beneficiary - [id]</td>
								<td>Beneficiary Name</td>
								<td>Beneficiary Value</td>
								<td>Updated at</td>
								<td>Status</td>
								<td width="100px">Action</td>
							</tr>
						</thead>
						<tbody>
							<?php for ($i = 0; $i < $rows; $i++) { ?>
							<tr>
								<td>
									<?php echo $arr[$i][beneficiary_id]; ?>
								</td>
								<td>
									<?php echo $arr[$i][beneficiary_name]; ?>
								</td>
								<td>
									<?php echo $arr[$i][beneficiary_value]; ?>
								</td>
								<td>
									<?php echo $arr[$i][beneficiary_updated_datetime]; ?>
								</td>
								<td>
									<?php echo getActiveStatus($arr[$i][beneficiary_active]); ?>
								</td>
								<td>
									<?php
									if (hasPermission('system', 'view', $_SESSION[current_user_id])) {
										//if($arr[$i][beneficiary_first_name]!='superadmin'){
										echo "<a href='beneficiary.php?beneficiary_id=" . $arr[$i][beneficiary_id] . "&param=view'>View</a>";
										echo " | ";
										//echo "<a href='beneficiary.php?beneficiary_id=".$arr[$i][beneficiary_id]."&param=delete'>Delete</a>";
										echo "<a class='delete' id='" . $arr[$i][beneficiary_id] . "' href='#'>Delete</a>";
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
            var beneficiary_id = $(this).attr('id');
            //alert(beneficiary_id);
            $('input[name=beneficiary_id]').val(beneficiary_id);
            $( "#dialog" ).dialog('open');
        });
    });
</script>
