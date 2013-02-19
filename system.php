<?php
include("config.php");
$sql = "select * from user";
$result = mysql_query($sql)or die(mysql_error());
$arr = mysql_fetch_rowsarr($result);
$rows=mysql_num_rows($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once('inc.head.php')?>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div id="top1">
				<?php include('top.php');?>
			</div>
			<h2>System</h2>
			<div id="mid">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td valign="top">
							<h2>User Setup</h2>
							<ul>
								<li><a href="user_type_list.php" <?php if(getFileName()=='user_type_list.php'){echo "class='selected'";}?>>User types</a></li>
							</ul>
						</td>
						<td valign="top">
							<h2>Company Information Setup</h2>
							<ul>
								<li><a href="bank_details.php" <?php if(getFileName()=='bank_details.php'){echo "class='selected'";}?>>Bank Details</a></li>
								<li><a href="beneficiary.php" <?php if(getFileName()=='beneficiary.php'){echo "class='selected'";}?>>Beneficiary</a></li>
								<li><a href="payment_method.php" <?php if(getFileName()=='payment_method.php'){echo "class='selected'";}?>>Payment Method </a></li>
								<li><a href="facility.php" <?php if(getFileName()=='facility.php'){echo "class='selected'";}?>>Facility </a></li>
							</ul>
						</td>
						<td valign="top">
							<h2>Proforma Invoice Setup</h2>
							<ul>
								<li><a href="shipping_term.php" <?php if(getFileName()=='shipping_term.php'){echo "class='selected'";}?>>Shipping Term </a></li>
							</ul>
						</td>
						<td valign="top">
							<h2>Purchase Order Setup</h2>
							<ul>
								<li><a href="fabric_type.php" <?php if(getFileName()=='fabric_type.php'){echo "class='selected'";}?>>Fabric Type </a></li>
								<li><a href="fabric_composition_options.php" <?php if(getFileName()=='fabric_composition_options.php'){echo "class='selected'";}?>>Fabric Composition Options </a></li>
								<li><a href="material.php" <?php if(getFileName()=='material.php'){echo "class='selected'";}?>>Material </a></li>
								<li><a href="po_category.php" <?php if(getFileName()=='po_category.php'){echo "class='selected'";}?>>PO Category </a></li>
								<li><a href="po_size.php" <?php if(getFileName()=='po_size.php'){echo "class='selected'";}?>>PO Size </a></li>
								<li><a href="shipping_term.php" <?php if(getFileName()=='shipping_term.php'){echo "class='selected'";}?>>Shipping Term </a></li>
								<li><a href="color_type.php" <?php if(getFileName()=='color_type.php'){echo "class='selected'";}?>>Color Type </a></li>
								<li><a href="colour.php" <?php if(getFileName()=='colour.php'){echo "class='selected'";}?>>Color </a></li>
								<li><a href="accessories.php" <?php if(getFileName()=='accessories.php'){echo "class='selected'";}?>>Accessories </a></li>
							</ul>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<h2>Planning and Order Status Setup</h2>
							<ul>
								<li><a href="capacity.php" <?php if(getFileName()=='capacity.php'){echo "class='selected'";}?>>Capacity </a></li>
							</ul>
						</td>
						<td>
							<h2>Supplier Information Setup</h2>
							<ul>
								<li><a href="capacity.php" <?php if(getFileName()=='supplier.php'){echo "class='selected'";}?>>Supplier </a></li>
							</ul>
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
