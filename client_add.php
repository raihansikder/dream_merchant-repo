<?php
include('config.php');

$valid = true;
$alert = array();
$param = $_REQUEST['param'];
$client_id = $_REQUEST[client_id];

if ($param == 'add' || $param == 'edit') {
	if (isset($_POST[submit])) {
		$exception_field = array('submit', 'client_user_ids', 'param', 'client_id');


		/*
		 * 	server side validation
		*/
		if (empty($_POST[client_company_name])) {
			$valid = false;
			array_push($alert, "Please give a valid client_company_name");
		}
		if (count($_REQUEST[client_user_ids]) == 0) {
			$valid = false;
			array_push($alert, "Please select users");
		} else {
			$client_user_ids = trim(implode(',', $_REQUEST[client_user_ids]), ', ');
		}
		if (!hasApprover($client_user_ids)) {
			$valid = false;
			array_push($alert, "Please select at least one approver");
		}

		/*         * ********************************** */
		if ($valid) {
			if ($param == 'add') {
				/*
				 * 	Check whether current user has permission to add client
				*/
				if (hasPermission('client', 'add', $_SESSION[current_user_id])) {
					/*
					 * 	Create the insert query substring.
					*/
					$str = createMySqlInsertString($_POST, $exception_field);
					$str_k = $str['k'];
					$str_v = $str['v'];
					/*                     * ********************************** */
					$sql = "INSERT INTO client($str_k,client_reg_date,client_reg_by,client_user_ids) values ($str_v,now(),'" . $_SESSION[current_user_id] . "','$client_user_ids')";
					mysql_query($sql) or die(mysql_error());
					$client_id = mysql_insert_id();
					$param = 'edit';
					array_push($alert, "The client has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to add client");
				}
			} else if ($param == 'edit') {
				/*
				 * 	Check whether current user has permission to edit client
				*/
				if (hasPermission('client', 'edit', $_SESSION[current_user_id])) {
					/*
					 * 	Create the update query substring.
					*/
					$str = createMySqlUpdateString($_REQUEST, $exception_field);
					/*                     * ********************************** */
					$sql = "UPDATE client set $str,client_user_ids='$client_user_ids' where client_id='$client_id'";
					mysql_query($sql) or die(mysql_error());
					array_push($alert, "The client has been saved!");
				} else {
					$valid = false;
					array_push($alert, "You don't have permission to edit client");
				}
			}
			//echo $sql;
		}
	}
}
$sql = "Select * from client where client_id='$client_id'";
$r = mysql_query($sql) or die(mysql_error());
$a = mysql_fetch_assoc($r);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php"); ?>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div id="top1">
				<?php include('top.php'); ?>
			</div>
			<div id="mid">
				<?php
				if ($param != 'add') {
					include('snippets/client/clientmenu.php');
				}
				?>
				<h2>Add/Edit client</h2>
				<div class="alert">
					<?php
					if (isset($_POST[submit])) {
						printAlert($valid, $alert);
					}
					?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="param" value="<?php echo $param ?>" />
					<?php if ($param != 'add') { ?>
					<input type="hidden" name="client_id" value="<?php echo $client_id ?>" />
					<?php } ?>
					<table width="586">
						<tr>
							<td colspan="2">
								<strong>Client Information</strong>
							</td>
						</tr>
						<tr>
							<td width="129">Company name:</td>
							<td width="457">
								<input name="client_company_name" type="text" value="<?php echo addEditInputField('client_company_name'); ?>" size="50" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td>Contact name:</td>
							<td>
								<input name="client_contact_name" type="text" value="<?php echo addEditInputField('client_contact_name'); ?>" size="50" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<strong>Contact details</strong>
							</td>
						</tr>
						<tr>
							<td>Address:</td>
							<td>
								<textarea name="client_address" cols="40" rows="4"><?php echo addEditInputField('client_address'); ?>
								</textarea>
							</td>
						</tr>
						<tr>
							<td>e-mail:</td>
							<td>
								<input name="client_email" type="text" value="<?php echo addEditInputField('client_email'); ?>" class="validate[required] validate[custom[email]]" />
							</td>
						</tr>
						<tr>
							<td>Phone # 1:</td>
							<td>
								<input name="client_phone1" type="text" value="<?php echo addEditInputField('client_phone1'); ?>" />
							</td>
						</tr>
						<tr>
							<td>Note:</td>
							<td>
								<textarea name="client_note" cols="40" rows="4">
                                        <?php echo addEditInputField('client_note'); ?>
                                    </textarea>
							</td>
						</tr>
						<tr>
							<td>Associated users:</td>
							<td>
								<?php
								$result = mysql_query("select * from user where user_active='1' order by user_name asc") or die(mysql_error());
								$user_array = mysql_fetch_rowsarr($result);
								$total_user = mysql_num_rows($result);
								?>
								<select class="multiselect validate[required]" multiple="multiple" name="client_user_ids[]">
									<?php foreach ($user_array as $ua) { ?>
									<option value="<?php echo $ua[user_id]; ?>"
									<?php
									if (isset($_REQUEST[client_user_ids])) { // selects the users matching from the post value
										if (in_array($ua[user_id], $_REQUEST[client_user_ids])) {
										echo ' selected="selected" ';
									}
									} else if (isset($a[client_user_ids])) {
										if (in_array($ua[user_id], explode(',', $a[client_user_ids]))) { // selects the users matching from db value
											echo ' selected="selected" ';
										}
									} else if (($param == 'add') && ($_SESSION[current_user_id] == $ua[user_id])) { // Automatically select the current user while adding a clinet for the first time
										echo ' selected="selected" ';
									}
									?>
									>
										<?php
										echo $ua[user_fullname];
										if (hasApprover($ua[user_id])) { // checks whether the given set of user ids(comma separated if multiple) have at least one user with costsheet approval priviledge
											echo ' (Approver)';
										}
										?>
									</option>
									<?php } ?>
								</select>
							</td>
						</tr>
					</table>
					<div class="clear"></div>
					<?php if ($param == 'add' || $param == 'edit') { ?>
					<input class="button bgblue" type="submit" name="submit" value="Save" />
					<?php } ?>
					<input class="button bgblue" type="button" name="cancel" value="Cancel" onclick="history.go(-1)" />
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php'); ?>
		</div>
	</div>
</body>
</html>
