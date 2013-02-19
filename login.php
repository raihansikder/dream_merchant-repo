<?php
include('config.php');
//echo $_SESSION['redirect_url'];
$valid=true;
$alert=array();
if(isset($_POST[Submit])){
	$user_name=mysql_real_escape_string(trim($_POST[user_name]));
	$user_password=mysql_real_escape_string(trim($_POST[user_password]));

	if(!strlen($user_name)||!strlen($user_password)){
		$valid=false;
		array_push($alert,"You cannot leave username or password field empty");
	}

	if($valid){
		$q="select * from user where user_name='$user_name'  AND  user_password='$user_password' AND user_active='1'";
		//echo $q;
		$r=mysql_query($q)or die(mysql_error()."<br/><b>Query:</b>$sql<br/>");
		if(mysql_num_rows($r)>0){
			$a=mysql_fetch_assoc($r);
			/*
			 *  load user info in SESSION
			*/
			$_SESSION[current_user_id]=$a[user_id];
			$_SESSION[current_user_name]=$a[user_name];
			$_SESSION[current_user_fullname]=$a[user_fullname];
			$_SESSION[current_user_type_id]=$a[user_type_id];
			//$_SESSION[current_user_type_id]=$a[user_type_id];
			$_SESSION[current_user_type_level]=getUserTypeLevel($a[user_type_id]);
			$_SESSION[current_user_email]=$a[user_email];
			$_SESSION[logged]=true;
			//$_SESSION[$company_name]=$company_name;
			//echo $a[user_type_id];
			/*****************************/

			/*
			 * update user login in database
			*/
			$q="UPDATE user set user_logged='1' where user_id='".$_SESSION[current_user_id]."'";
			$r=mysql_query($q)or die(mysql_error());

		}else{
			$valid=false;
			array_push($alert,"username/password missmatch");
		}
	}
	if($_SESSION[logged]==true){
		if(strlen($_SESSION['redirect_url'])){
			header("location:".$_SESSION['redirect_url']);
		}else{
			header("location:index.php");
		}
	}
}
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
				<div class="alert">
					<?php if(isset($_POST[Submit])){
						printAlert($valid,$alert);

					}
					?>
				</div>
				<form action="" method="post" name="admin_login_form">
					<div style="float: left">
						<h2>User Login</h2>
						Please input correct username and password to access they system.
						<table width="100%">
							<tr>
								<td width="91">&nbsp;</td>
								<td width="495">&nbsp;</td>
							</tr>
							<tr>
								<td>
									<strong>Username</strong>
								</td>
								<td>
									<input type="text" name="user_name" class="validate[required]" />
								</td>
							</tr>
							<tr>
								<td>
									<strong>Password</strong>
								</td>
								<td>
									<input type="password" name="user_password" class="validate[required]" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input class="button bgblue" type="submit" name="Submit" value="Submit" />
									<input class="button bgblue" type="reset" name="Submit2" value="Reset" />
								</td>
							</tr>
						</table>
					</div>
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
