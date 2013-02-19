<?php
include_once('config.php');
$client_id=$_REQUEST[client_id];
$r=mysql_query("Select * from client where client_id='$client_id'")or die(mysql_error());
$a=mysql_fetch_assoc($r);
$_SESSION[client_id]=$a[client_id];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php");?>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div id="top1">
				<?php include('top.php');?>
			</div>
			<div id="mid">
				<h2>
					<?php echo getClientCompanyNameFrmId($client_id); ?>
				</h2>
				<?php include('snippets/client/clientmenu.php');?>
				<div class="alert">
					<?php if(isset($_POST[submit])){
						printAlert($valid,$alert);
} ?>
				</div>
				<table width="586" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<strong>Client Information</strong>
						</td>
						<td width="460">
							<?php if(hasPermission('client','edit',$_SESSION[current_user_id])){?>
							<a href="client_add.php?param=edit&client_id=<?php echo $a[client_id]?>"> [Edit client]</a>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td width="126">Company name:</td>
						<td>
							<?php echo $a[client_company_name];?>
						</td>
					</tr>
					<tr>
						<td>Contact name:</td>
						<td>
							<?php echo $a[client_contact_name];?>
						</td>
					</tr>
					<tr>
						<td>
							<strong>Contact details</strong>
						</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Address:</td>
						<td>
							<?php echo $a[client_address];?>
						</td>
					</tr>
					<tr>
						<td>Phone:</td>
						<td>
							<?php echo $a[client_phone1];?>
						</td>
					</tr>
					<tr>
						<td>Note:</td>
						<td>
							<?php echo $a[client_note];?>
						</td>
					</tr>
					<tr>
						<td colspan="3">
							<span class="small"> This client has been registered on date: <b> <?php echo gmdate("M d, Y",convertMySQLDatetimetoTimestamp($a[client_reg_date]));?>
							</b> by: <b> <?php echo getUserNameFrmId($a[client_reg_by]);?>
							</b>
							</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
