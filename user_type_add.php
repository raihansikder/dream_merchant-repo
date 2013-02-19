<?php
include('config.php');

$valid=true;
$alert=array();
$param=$_REQUEST['param'];
$user_type_id=$_REQUEST[user_type_id];

if($param=='add'||$param=='edit'){
	if(isset($_POST[submit])){
		$exception_field=array('submit','param','p_ids');
		/*
		 *	server side validation
		*/
		if(empty($_POST['user_type_name'])){
			$valid=false;
			array_push($alert,"Please give a valid user_type_name");
		}else{
			if($param=='add'&& userTypeNameIsAlreadyTaken(trim($_POST['user_type_name']))){
				$valid=false;
				array_push($alert,"Use another user_type_name, this user_type_name is already taken - add error");
			}else if($param=='edit'){
				if(userTypeNameIsAlreadyTaken(trim($_POST['user_type_name']))){
					if( userTypeNameIsAlreadyTaken(trim($_POST['user_type_name']))!=$user_type_id){
						$valid=false;
						array_push($alert,"Use another user_type_name, this user_type_name is already taken - edit error");
					}
				}
			}
		}
		/*************************************/
		if($valid){
			if($param=='add'){
				/*
				 *	Check whether current user has permission to add user
				*/
				if(hasPermission('user_type','add',$_SESSION[current_user_id])){
					/*
					 *	Create the insert query substring.
					*/
					$str=createMySqlInsertString($_POST,$exception_field);
					$str_k=$str['k'];
					$str_v=$str['v'];
					/*************************************/
					$q="INSERT INTO user_type($str_k,user_type_datetime) values ($str_v,now())";
					mysql_query($q) or die(mysql_error());
					$user_type_id= mysql_insert_id();
					updatePermissionTable($_POST[p_ids],$user_type_id);
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
				if(hasPermission('user_type','edit',$_SESSION[current_user_id])){
					/*
					 *	Create the update query substring.
					*/
					$str=createMySqlUpdateString($_REQUEST,$exception_field);
					/*************************************/
					$q="UPDATE user_type set $str,user_type_datetime=now() where user_type_id='$user_type_id'";
					//echo $q."<br/><br/>"; // debug sql
					mysql_query($q) or die(mysql_error());
					//print_r($_POST[p_ids]);
					updatePermissionTable($_POST[p_ids],$user_type_id);
					array_push($alert,"The user type has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to edit user");
				}
			}
			//echo $sql;
		}
	}
}
$q="Select * from user_type where user_type_id='$user_type_id'";
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
					User type
				</h2>
				<div class="alert">
					<?php if(isset($_POST[submit])){
						printAlert($valid,$alert);
} ?>
				</div>
				<div class="right">
					<?php
					//if(hasPermission('costsheet','edit',$_SESSION[current_user_id])){
					echo "<a href='user_type_add.php?user_type_id=$user_type_id&param=edit'>[Edit user type]</a>" ;
					//}
					?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
					<input type="hidden" name="param" value="<?php echo $param?>" />
					<?php if($param!='add'){?>
					<input type="hidden" name="user_type_id" value="<?php echo $user_type_id?>" />
					<?php } ?>
					<input type="hidden" name="user_type_by" value="<?php echo $_SESSION[current_user_id];?>" />
					<table width="586" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td colspan="2">
								<strong>User type information</strong>
							</td>
						</tr>
						<tr>
							<td width="129">User type name:</td>
							<td width="457">
								<input name="user_type_name" type="text" value="<?php echo addEditInputField('user_type_name'); ?>" size="30" maxlength="20" class="validate[required,maxSize[20],minSize[4]]" />
							</td>
						</tr>
						<!--
          <tr>
            <td>Level:</td>
            <td><input name="user_type_level" type="text" value="<?php echo addEditInputField('user_type_level'); ?>" size="4" maxlength="2"  class="validate[required,custom[onlyLetterNumber],maxSize[2],minSize[1]]"/><span class="small">Put 0 for default.</span></td>
          </tr>
          -->
						<tr>
							<td>Permission:</td>
							<td>
								<?php

								$r=mysql_query("select * from permission where p_active='1' order by p_module_system_name asc")or die(mysql_error());
								$p_array=mysql_fetch_rowsarr($r);
								$total_p=mysql_num_rows($r);
								?>
								<select class="multiselect" multiple="multiple" name="p_ids[]">
									<?php foreach($p_array as $pa){?>
									<option value="<?php echo $pa[p_id]; ?>" <?php
									if(isset($_REQUEST[p_ids])){ // selects the users matching from the post value
										if(in_array($pa[p_id],$_REQUEST[p_ids])){
										echo ' selected="selected" ';
									}
									}else if(isset($a[user_type_id])){
										if(in_array($a[user_type_id],explode(',',$pa[p_user_type_ids]))){ // selects the users matching from db value
											echo ' selected="selected" ';
										}
									}
									?>>
										<?php echo $pa[p_module_system_name]."-".$pa[p_action];

										?>
									</option>
									<?php } ?>
								</select>
							</td>
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
