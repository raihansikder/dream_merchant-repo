<?php
include("config.php");
$sql = "select * from client where client_active='1' ";
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
			<h2>List of Clients</h2>
			<?php
	if(hasPermission('client','add',$_SESSION[current_user_id])){ ?>
			<div class='add_button_large'>
				<a href="client_add.php?param=add"> <img src="images/plus.png" /> Add new Client
				</a>
			</div>
			<?php } ?>
			<div id="mid">
				<div class="clear"></div>
				<table id="datatable" width="100%">
					<thead>
						<tr>
							<td width="24">ID</td>
							<td width="176">Client Company name</td>
							<td width="221">Client contact name</td>
							<td width="102">e-mail</td>
							<td width="102">Phone</td>
							<td width="75">Action</td>
						</tr>
					</thead>
					<tbody>
						<?php for($i=0;$i<$rows;$i++){?>
						<tr>
							<td>
								<?php echo $arr[$i][client_id];?>
							</td>
							<td>
								<?php echo $arr[$i][client_company_name];?>
							</td>
							<td>
								<?php echo $arr[$i][client_contact_name];?>
							</td>
							<td>
								<?php echo $arr[$i][client_email];?>
							</td>
							<td>
								<?php echo $arr[$i][client_phone1];?>
							</td>
							<td>
								<?php
								if(hasPermission('client','view',$_SESSION[current_user_id])){
									echo "<a href='client_view.php?client_id=".$arr[$i][client_id]."'>view</a>";
								}
								?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
