<div id="header">
	<div class="logo">
		<h1>
			<?php echo $app_name;?>
			<br>
			<?php echo $company_name;?>
		</h1>
	</div>
	<div id="mainmenu">
		<?php 
		if($_SESSION[logged]){
			echo "<a class='homepage_menu topmenu_item blueBlock' href='index.php'><img src='images/home.png' align='middle' /> Home</a>";
			if(hasPermission('client','view',$_SESSION[current_user_id])){
				echo "<a class='homepage_menu topmenu_item blueBlock'href='client_list.php'><img src='images/client.png' align='middle' />Client</a> ";
			}
			if(hasPermission('user','view',$_SESSION[current_user_id])){
				echo "<a class='homepage_menu topmenu_item blueBlock' href='user_list.php'><img src='images/user.png' align='middle' />User</a> ";
			}
			if(hasPermission('system','view',$_SESSION[current_user_id])){
				echo "<a class='homepage_menu topmenu_item blueBlock' href='system.php'><img src='images/settings.png' align='middle' />system</a> ";
			}
		}
		?>
	</div>
	<div class="user_info">
		<?php if($_SESSION[logged]){
			echo "Welcome! <b>".$_SESSION[current_user_fullname];
			echo " [L:".currentUserLevel()." | T:".currentUserTypeId()."]";
			echo "</b><br/><a href=\"logout.php\">logout</a><br/>";
			echo date("F j, Y");
	}?>
	</div>
</div>
<div class="clear"></div>
