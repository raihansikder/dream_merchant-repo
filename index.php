<?php
include_once("config.php");
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
			<div id="mid">
				<?php echo $company_intro; ?>
				<?php
				if(hasPermission('costsheet','approve',$_SESSION[current_user_id])){
					include('snippets/index/costsheet_approval_request_list.php');
				}
				?>
				<div id="mid_left">
					<?php
					if(hasPermission('costsheet','approve',$_SESSION[current_user_id])){
						include('snippets/index/fabric_booking_approval_request_list.php');
					}
					if(hasPermission('costsheet','approve',$_SESSION[current_user_id])){
						include('snippets/index/bom_booking_approval_request_list.php');
					}
					?>
					<?php include('snippets/index/costsheet_list_this_user.php')?>
					<?php include('snippets/index/booking_quantity_graph.php')?>
				</div>
				<div id="mid_right">
					<?php include('snippets/index/your_clients.php')?>
				</div>
			</div>
			<div id="footer">
				<?php include('footer.php');?>
			</div>
		</div>
	</div>
</body>
</html>
