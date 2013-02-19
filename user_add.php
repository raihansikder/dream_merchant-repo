<?php
include("config.php");
$valid=true;
$alert=array();
$param=$_REQUEST['param'];
$user_id=$_REQUEST[user_id];

if($param=='add'||$param=='edit'){
	if(isset($_POST[submit])){
		$exception_field=array('submit','param','user_password_re');
		/*
		 *	server side validation
		*/
		if(empty($_POST['user_name'])){
			$valid=false;
			array_push($alert,"Please give a valid user_name");
		}else{
			if($param=='add'&& usernameIsAlreadyTaken(trim($_POST['user_name']))){
				$valid=false;
				array_push($alert,"Use another username address, this username is already taken");
			}
		}
		if(!strlen($_POST['user_email'])){
			$valid=false;
			array_push($alert,"Please give a valid user_email");
		}else{
			if($param=='add'&& emailIsAlreadyTaken(trim($_POST['user_email']))){
				$valid=false;
				array_push($alert,"Use another e-mail address, this e-mail is already taken");
			}else if($param=='edit' && emailIsAlreadyTaken(trim($_POST['user_email']))!=$user_id){
				$valid=false;
				array_push($alert,"Use another e-mail address, this e-mail is already taken");
			}
		}

		/*************************************/
		if($valid){
			if($param=='add'){
				/*
				 *	Check whether current user has permission to add user
				*/
				if(hasPermission('user','add',$_SESSION[current_user_id])){
					/*
					 *	Create the insert query substring.
					*/
					$str=createMySqlInsertString($_POST,$exception_field);
					$str_k=$str['k'];
					$str_v=$str['v'];
					/*************************************/
					$q="INSERT INTO user($str_k,user_reg_datetime,user_reg_by) values ($str_v,now(),'".$_SESSION[current_user_id]."')";
					mysql_query($q) or die(mysql_error());
					$user_id= mysql_insert_id();
					$param='edit';
					array_push($alert,"The user has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to add user");
				}
			}else if($param=='edit'){
				/*
				 *	Check whether current user has permission to edit user
				*/
				if(hasPermission('user','edit',$_SESSION[current_user_id])){
					/*
					 *	Create the update query substring.
					*/
					$str=createMySqlUpdateString($_REQUEST,$exception_field);
					/*************************************/
					$q="UPDATE user set $str where user_id='$user_id'";
					mysql_query($q) or die(mysql_error());
					array_push($alert,"The user has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to edit user");
				}
			}
			//echo $sql;
		}
	}
}
$q="Select * from user where user_id='$user_id'";
//echo $q;
$r=mysql_query($q)or die(mysql_error());
if(mysql_num_rows($r)){
	$a=mysql_fetch_assoc($r);
}


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
				<?php include('snippets/user/usermenu.php');?>
				<h2>
					<?php echo ucfirst($param);?>
					user
				</h2>
				<div class="alert">
					<?php if(isset($_POST[submit])){
						printAlert($valid,$alert);
} ?>
				</div>
				<div class="right">
					<?php
					if(hasPermission('user','edit',$_SESSION[current_user_id])){
						echo "<a href='user_add.php?user_id=$user_id&param=edit'>[Edit user]</a>" ;
					}
					?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="param" value="<?php echo $param?>" />
					<?php if($param!='add'){?>
					<input type="hidden" name="user_id" value="<?php echo $user_id?>" />
					<?php } ?>
					<table width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2">
								<strong>System Information</strong>
							</td>
						</tr>
						<tr>
							<td width="129">System username:</td>
							<td width="457">
								<input name="user_name" type="text" value="<?php echo addEditInputField('user_name'); ?>" size="30" maxlength="30" class="validate[required,custom[onlyLetterNumber],maxSize[20],minSize[5]]" <?php if($param!='add'){inputReadonly();}?> />
								<span class="small">This is username(unique id) for log-in. Once user is added it cannot be further changed</span>
							</td>
						</tr>
						<tr>
							<td>Password:</td>
							<td>
								<input id="user_password" name="user_password" type="password" value="<?php echo addEditInputField('user_password'); ?>" size="30" maxlength="20" class="validate[required,custom[onlyLetterNumber],maxSize[20],minSize[8]]" />
							</td>
						</tr>
						<?php
						/*
						 * Shows passowrd retype option only when user is added for the first time
						*/
		  if($param=='add'){?>
						<tr>
							<td>Retype Password:</td>
							<td>
								<input name="user_password_re" type="password" value="<?php echo addEditInputField('user_password_re'); ?>" size="30" maxlength="60" class="validate[required,equals[user_password]]" />
							</td>
						</tr>
						<tr>
							<?php }/*******************************************/ ?>
							<td>Email:</td>
							<td>
								<input name="user_email" type="text" value="<?php echo addEditInputField('user_email'); ?>" size="30" maxlength="60" class="validate[required,custom[email]]" />
							</td>
						</tr>
						<tr>
							<td>User type:</td>
							<td>
								<?php
								$selectedId=addEditInputField('user_type_id');
								$customQuery=" where user_type_active='1'";
								$parameter="class=validate[required] ";
								if($a['user_name']=='superadmin'){
									$parameter.=" disabled='disabled' ";
								}
								createSelectOptions('user_type','user_type_id','user_type_name','$customQuery',$selectedId,'user_type_id',$parameter);
								?>
							</td>
						</tr>
						<tr>
							<td>
								<strong>Other Information</strong>
							</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>Full name:</td>
							<td>
								<input name="user_fullname" type="text" value="<?php echo addEditInputField('user_fullname'); ?>" size="30" maxlength="60" class="validate[required]" />
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
					<div class="clear"></div>
					<?php if($param=='edit'||$param=='add'){?>
					<input class="button bgblue" type="submit" name="submit" value="Save" />
					<?php }?>
					<input class="button bgblue" type="button" name="cancel" value="Cancel" onclick="history.go(-1)" />
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
