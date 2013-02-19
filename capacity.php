<?php 
include("config.php");

$valid=true;
$alert=array();
$param=$_REQUEST['param'];
$capacity_id=$_REQUEST['capacity_id'];

/*******************************************/
/* Delete
/*******************************************/
if($_REQUEST[param]=='delete' && is_numeric($capacity_id)){
	if(hasPermission('system','delete',$_SESSION[current_user_id])){
		$sql = "DELETE FROM capacity WHERE capacity_id='$capacity_id'";
		$r = mysql_query($sql)or die(mysql_error());
		//$a = mysql_fetch_assoc($r);
		//$rows=mysql_num_rows($r);
		if(mysql_affected_rows()){
			header("location:capacity.php?param=delete_success");
		}
	}else{
		$valid=false;
		array_push($alert,"You don't have permission to delete capacity");
	}
}
/*******************************************/
/* Add/Edit
/*******************************************/

if(!strlen($capacity_id)){
	$param="add";
}else{
	$param="edit";
}
if(isset($_POST[submit])){
	if($param=='add'||$param=='edit'){

		$exception_field=array('submit','param');
		/*
		*	server side validation starts
		*/
		/*************************************/
		/*
		No server side validation code yet written
		*/
		/*************************************/
		if($valid){
			if($param=='add'){
				/*
				*	Check whether current capacity has permission to add client
				*/
				if(hasPermission('system','add',$_SESSION[current_user_id])){
					/*
					*	Create the insert query substring.
					*/
					$str=createMySqlInsertString($_POST,$exception_field);
					$str_k=$str['k'];
					$str_v=$str['v'];
					/*************************************/
						
					$sql="INSERT INTO capacity($str_k,capacity_updated_datetime) values ($str_v,now())";
					mysql_query($sql) or die(mysql_error()."<b>Query:</b><br>$sql<br>");
					$capacity_id= mysql_insert_id();
					$param='edit';
					array_push($alert,"The capacity has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to add capacity");
				}
			}else if($param=='edit'){
				/*
				*	Check whether current capacity has permission to edit client
				*/
				if(hasPermission('system','edit',$_SESSION[current_user_id])){
					/*
					*	Create the update query substring.
					*/
					$str=createMySqlUpdateString($_REQUEST,$exception_field);
					/*************************************/
					$sql="UPDATE capacity set $str,capacity_updated_datetime=now() where capacity_id='".$_REQUEST['capacity_id']."'";
					mysql_query($sql) or die(mysql_error());
					array_push($alert,"The capacity has been saved!");
				}else{
					$valid=false;
					array_push($alert,"You don't have permission to edit capacity");
				}
			}
			//echo $sql;
		}
	}
}


/*******************************************/
if($capacity_id){
	$sql = "SELECT * FROM capacity WHERE capacity_id='$capacity_id'";
	$r = mysql_query($sql)or die(mysql_error());
	$a = mysql_fetch_assoc($r);
	$rows=mysql_num_rows($r);
}

$sql = "SELECT * FROM capacity";
$r = mysql_query($sql)or die(mysql_error());
$arr = mysql_fetch_rowsarr($r);
$rows=mysql_num_rows($r);


//-------------------------------------------



?>
									$selectedId=addEditInputField('capacity_facility_id');
									$customQuery = " WHERE facility_active='1' ";
									createSelectOptions('facility','facility_id','facility_name',$customQuery,$selectedId,'capacity_facility_id', "  class='validate[required]'");?>
									$selectedId=addEditInputField('capacity_active');
									$customQuery = " WHERE option_group='active_status' AND option_active='1' ";
			createSelectOptions('options','option_value','option_name',$customQuery,$selectedId,'capacity_active', "  class='validate[required]'");?>
									if(hasPermission('system', 'view', $_SESSION[current_user_id])){
										//if($arr[$i][capacity_first_name]!='superadmin'){
										echo "<a href='capacity.php?capacity_id=".$arr[$i][capacity_id]."&param=view'>View</a>";
										echo " | ";
										//echo "<a href='capacity.php?capacity_id=".$arr[$i][capacity_id]."&param=delete'>Delete</a>";
										echo "<a class='delete' id='".$arr[$i][capacity_id]."' href='#'>Delete</a>";
										//}
									}
									?>
$('document').ready(function(){
	$("#dialog" ).dialog({ autoOpen: false, });
	$('a.delete').click(function(){
		var capacity_id = $(this).attr('id');
		//alert(capacity_id);
		$('input[name=capacity_id]').val(capacity_id);
		$( "#dialog" ).dialog('open');
	});
});
</script>