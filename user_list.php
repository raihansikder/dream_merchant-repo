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
			<h2>List of Users</h2>
			<?php include('snippets/user/usermenu.php');?>
			<?php
	if(hasPermission('user','add',$_SESSION[current_user_id])){ ?>
			<div class='add_button_large'>
				<a href="user_add.php?param=add"> <img src="images/plus.png" /> Add new User
				</a>
			</div>
			<?php } ?>
			<div id="mid">
				<div class="clear"></div>
				<table id="datatable" width="100%">
					<thead>
						<tr>
							<td width="176">User name - [id]</td>
							<td width="221">e-mail</td>
							<td width="102">type</td>
							<td width="75">Action</td>
						</tr>
					</thead>
					<tbody>
						<?php for($i=0;$i<$rows;$i++){?>
						<tr>
							<td>
								<?php echo $arr[$i][user_name]." - [".$arr[$i][user_id]."]";?>
							</td>
							<td>
								<?php echo $arr[$i][user_email]?>
							</td>
							<td>
								<?php echo getUserTypeName($arr[$i][user_type_id]);?>
							</td>
							<td>
								<?php
								if(hasPermission('user', 'view', $_SESSION[current_user_id])){
									//if($arr[$i][user_name]!='superadmin'){
									echo "<a href='user_add.php?user_id=".$arr[$i][user_id]."&param=view'>view</a>";
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
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
