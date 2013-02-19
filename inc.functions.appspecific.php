<?php
$office_address=
"Head Office : House# 39(1st Floor), Road#  6, Sector#4<br />
Uttara  Model Town, Uttara, Dhaka -1230 Bangladesh<br />
Tel:  880-2-8955871,8961911,8915335 Fax: 880-2-8955922<br />
E-mail: <a href='mailto:info@textilehorizon.com'>info@textilehorizon.com</a> <br />
Factory:  Shajahan Mansion (2nd, 3rd &amp; 4th Floor)  Cerag Ali Market<br />
Nishadnagar,  Tongi, Gazipur, Bangladesh, Tel: 9815476";

/*
 *	Functions written by different contributor as placed in [inc.functions.appspecific.temp.php] for review. Once QAed and varifed codes are migrated to [inc.functions.appspecific.php]
*/
include_once('inc.functions.appspecific.temp.php');
$defaultConfirmationMsg="Please confirm this action";
/*************************************************************
 *
usage: general
IF val==0 returns Inactive, IF val==1 returns active.
As general practice the system represents '1' and '0' (as enum value options in MySQL DB) to indicate whether that element is active or not.
Exammple TABLE: user > FIELD user_active = '1' means that user is in active system status
*/
function getActiveStatus($val){
	if($val=='1')echo "<span class='active'>Active</span>";
	else if($val=='0')echo "<span class='inactive'>Inactive</span>";
}
/*************************************************************
 *
usage: purchaseOrder
- gets the puchase order category name from a given puchase order category id
*/
function getPoCatNameFrmId($po_cat_id){
	$r=mysql_query("select po_cat_name from po_category where po_cat_id='$po_cat_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	//echo $a[user_name];
	return $a[po_cat_name];
}
/*************************************************************
 *
usage: purchaseOrder
- gets po_uid from po_id
*/
function getPoUidFrmId($po_id){
	$r=mysql_query("select po_uid from purchaseorder where po_id='$po_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	//echo $a[user_name];
	return $a[po_uid];
}
/*************************************************************
 *
usage: purchaseOrder
- gets po_uid from po_id
*/
function FabricBookingStatusAgainstPoUid($po_uid){
	$sql = "select * from fabric_booking where fabric_booking_po_uid='$po_uid' AND fabric_booking_active='1' ORDER BY fabric_booking_datetime DESC limit 0,1";
	$r=mysql_query($sql)or die(mysql_error()."<br>____<br>$sql<br>");
	//echo "<br>____<br>$sql<br>";
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a[fabric_booking_status];
	}else{
		return false;
	}
}
/*************************************************************/
/*
 *
*/
function BomBookingStatusAgainstPoUidBomId($po_uid,$bom_id){
	$sql = "select * from bom_booking where bom_booking_po_uid='$po_uid' AND bom_booking_bom_id='$bom_id' AND bom_booking_active='1' ORDER BY bom_booking_datetime DESC limit 0,1";
	$r=mysql_query($sql)or die(mysql_error()."<br>____<br>$sql<br>");
	//echo "<br>____<br>$sql<br>";
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a[bom_booking_status];
	}else{
		return false;
	}
}
/*************************************************************

*
usage: purchaseOrder
- gets total requirement Quantity of all size and color and returns the value
*/
function totalQtyFromPoId($po_id){
	$r=mysql_query("Select * from podetails where podetails_po_id='$po_id' ")or die(mysql_error());
	$podetails_rows=mysql_num_rows($r);
	//echo $podetails_rows."<br />";
	if($podetails_rows > 0){
		$a_podetails=mysql_fetch_rowsarr($r);
		$total_1=0;
		$total_2=0;
		$total_3=0;
		$total_4=0;
		$total_5=0;
		$total_6=0;
		$total_7=0;
		$total_8=0;
		$total_9=0;
		$total_10=0;
		$grand_total=0;
		foreach($a_podetails as $a_p){
			$podetails_total_color=$a_p["podetails_1"]+$a_p["podetails_2"]+$a_p["podetails_3"]+$a_p["podetails_4"]+$a_p["podetails_5"]+$a_p["podetails_6"]+$a_p["podetails_7"]+$a_p["podetails_8"]+$a_p["podetails_9"]+$a_p["podetails_10"];
			$grand_total+=$podetails_total_color;
			//echo $podetails_total_color;
			$total_1+=$a_p["podetails_1"];
			$total_2+=$a_p["podetails_2"];
			$total_3+=$a_p["podetails_3"];
			$total_4+=$a_p["podetails_4"];
			$total_5+=$a_p["podetails_5"];
			$total_6+=$a_p["podetails_6"];
			$total_7+=$a_p["podetails_7"];
			$total_8+=$a_p["podetails_8"];
			$total_9+=$a_p["podetails_9"];
			$total_10+=$a_p["podetails_10"];
		}
		return $grand_total;
	}else return "ERROR: function name - totalQtyFromPoId(). podetails_po_id($po_id) not found";
}
/*************************************************************
 *
usage: proforma invoice
- generates a proforma invoiec no in format "company_shortcode/client_shortcode/pi_number/$year"
*/
function generatePiNo($client_id){
	global $company_shortcode;
	$temp_array=str_split(str_replace(" ",'',getClientCompanyNameFrmId($client_id)),5);
	$client_shortcode= strtoupper($temp_array[0]);
	$r=mysql_query("select count(*) as total from proforma_invoice where pi_client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	$pi_number=sprintf("%05d",$a[total]+1);
	$year=date('Y');
	return "$company_shortcode/$client_shortcode/$pi_number/$year";
}
/**************************************************************/
/*************************************************************
 *
usage: user
- gets user_name from user_id
*/
function getUserNameFrmId($user_id){
	$r=mysql_query("select user_name from user where user_id='$user_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	//echo $a[user_name];
	return $a[user_name];
}
/*************************************************************
 *
usage: user
- gets user_email from user_id
*/
function getUserEmailFrmId($user_id){
	$r=mysql_query("select user_email from user where user_id='$user_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	//echo $a[user_email]."<br/>";
	return $a[user_email];
}
/*************************************************************
 *
usage: client
- gets client_company_name from client_id
*/
function getClientCompanyNameFrmId($client_id){
	$r=mysql_query("select client_company_name from client where client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[client_company_name];
}
/*************************************************************
 *
usage: client
- gets client_address from client_id
*/
function getClientAddressFrmId($client_id){
	$r=mysql_query("select client_address from client where client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[client_address];
}
/*************************************************************
 *
usage: user > user_type
- gets user_type_level from user_type_id
*/
function getUserTypeLevel($user_type_id){
	$r=mysql_query("select user_type_level from user_type where user_type_id='$user_type_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a['user_type_level'];
}
/*************************************************************
 *
usage: user > user_type
- gets user_type_name from user_type_id
*/
function getUserTypeName($user_type_id){
	$r=mysql_query("select user_type_name from user_type where user_type_id='$user_type_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a['user_type_name'];
}
/*************************************************************
 *
usage: client
- gets client_contact_name from client_id
*/
function getClientContactNameFrmId($client_id){
	$r=mysql_query("select client_contact_name from client where client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[client_contact_name];
}
/*************************************************************
 *
usage: client
- gets client_email from client_id
*/
function getClientEmailFrmId($client_id){
	$r=mysql_query("select client_email from client where client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[client_email];
}
/*************************************************************
 *
usage: client
- gets client_user_ids from client_id, returns standard comma separated value without any leading or trailing comma
*/
function getClientUserIds($client_id){
	$r=mysql_query("select client_user_ids from client where client_id='$client_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[client_user_ids];
}
/*************************************************************
 usage: costsheet
Costsheet approval state check*
*/
//'Unapproved','Requested_approval','Disapproved','Approved'
function costsheetApproved($costsheet_id){
	$r=mysql_query("select costsheet_approval_state from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	if($a[costsheet_approval_state]=='Approved')return true;
	else return false;
}
/*************************************************************
 usage: costsheet
checks whether approval is already requested for a costsheet. If requested the funtion returns 'true' otherwise 'false'
*/
function costsheetApprovalRequested($costsheet_id){
	$r=mysql_query("select costsheet_approval_state from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	if($a[costsheet_approval_state]=='Requested_approval')return true;
	else return false;
}
/*************************************************************
 usage: costsheet
checks whether costsheet is unapproved. if YES(unapproved) returns 'true' otherwise 'false'
*/
function costsheetUnapproved($costsheet_id){
	$r=mysql_query("select costsheet_approval_state from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	if($a[costsheet_approval_state]=='Unapproved')return true;
	else return false;
}
/*************************************************************
 usage: costsheet
checks whether costsheet is Disapproved. if YES(Disapproved) returns 'true' otherwise 'false'
*/
function costsheetDisapproved($costsheet_id){
	$r=mysql_query("select costsheet_approval_state from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	if($a[costsheet_approval_state]=='Disapproved')return true;
	else return false;
}
/*************************************************************
 usage: costsheet
checks whether costsheet is Freezed. if YES(Freezed) returns 'true' otherwise 'false'
*/
function costsheetFreezed($costsheet_id){
	$r=mysql_query("select costsheet_freeze from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	if($a[costsheet_freeze]=='1')return true;
	else return false;
}

function getSupplierNameFrmId($supplier_id){
	$r=mysql_query("select supplier_company_name from supplier where supplier_id='$supplier_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[supplier_company_name];
}

function costsheetApproverIdfromCostsheetId($costsheet_id){
	$r=mysql_query("select costsheet_approver_user_id from costsheet where costsheet_id='$costsheet_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[costsheet_approver_user_id];
}
function getClientIdsFromUserId($user_id){ // returns an array with client_ids
	$r=mysql_query("select client_id,client_user_ids from client")or die(mysql_error());
	$client_ids_array= array();
	if(mysql_num_rows($r)){
		$a=mysql_fetch_rowsarr($r);
		foreach($a as $client){
			if(in_array($user_id,explode(',',trim($client['client_user_ids'],', ')))){
				array_push($client_ids_array,$client['client_id']);
			}
		}
	}
	return $client_ids_array;
}
function getClientIdFrmPoId($po_id){ // returns an array with client_ids
	$r=mysql_query("select po_client_id from purchaseorder where po_id='$po_id'")or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a[po_client_id];
	}return false;
}

// retunrs list of sizes associated with a purchase order in CSV
// Example X,XXL,3XL
function quantityDetailsSizeListCsv($po_id){
	$q="select * from purchaseorder where po_id='$po_id'";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
	}
	//myprint_r($a);
	$size_array=array();
	for($i=1;$i<=10;$i++){
		if(strlen($a["po_quantity_size".$i."_id"])){
			//echo $a["po_quantity_size".$i."_id"]."<br/>";
			//echo getSizeNameFrmId($a["po_quantity_size".$i."_id"])."<br/>";
			array_push($size_array,getSizeNameFrmId($a["po_quantity_size".$i."_id"]));
		}
	}
	return trim(implode(',',$size_array),', ');
}
/*
 *
*/
function getSizeNameFrmId($po_size_id){
	$q="select po_size_name from po_size where po_size_id='$po_size_id'";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		//print_r($a);
		//echo $a['po_size_id'];
		return $a['po_size_name'];
	}
	else return false;
}
/*
 *	returns corresponding user_id if matching e-mail is found. else returns false;
*/
function emailIsAlreadyTaken($emailAddress){
	$q="select user_id from user where user_email='$emailAddress'";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a['user_id'];
	}
	else return false;
}
function usernameIsAlreadyTaken($userName){
	$q="select user_id from user where user_name='$userName'";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a['user_id'];
	}
	else return false;
}
function userTypeNameIsAlreadyTaken($userTypeName){
	$q="select user_type_id from user_type where user_type_name='$userTypeName'";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a['user_type_id'];
	}
	else return false;
}
function paymentMethodFrmID($payment_method_id){
	$r=mysql_query("select payment_method_name from payment_method where payment_method_id='$payment_method_id'")or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a['payment_method_name'];
	}
	else return false;
}
function shippingTermFrmID($st_id){
	$r=mysql_query("select st_name from shipping_term where st_id='$st_id'")or die(mysql_error());
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a['st_name'];
	}
	else return false;
}
/*
 *	Costsheet permission functions. CAREFULLY PUT MODULE NAME DONT TOUCH THE MODULE TABLE
*	returns true if the function has permission
*/
function hasPermission($module_system_name,$action,$user_id){
	/* then get the user_type_ids from permission table that matches teh module/action*/
	$p_user_type_ids=userTypeIdsPermittedForAction($module_system_name,$action);
	//echo $p_user_type_ids;
	if(strlen($p_user_type_ids)){
		/* first get the user_typ_id*/
		$q="select * from user where user_id='$user_id' and user_type_id in($p_user_type_ids)";
		$r=mysql_query($q)or die(mysql_error());
		if(mysql_num_rows($r))return true;
		else return false;
	}else{
		return false;
	}
}
/*
 *
*/
function addUserTypeIdInPermission($p_id,$new_user_type_id){
	$existing_utids_csv=userTypeIdsFromPId($p_id);
	$existing_utids_array=explode(',',$existing_utids_csv);
	if(!in_array($new_user_type_id,$existing_utids_array)){
		array_push($existing_utids_array,$new_user_type_id);	// if not then value is added to array
		sort($existing_utids_array);
		$new_utids_csv=trim(implode(',',$existing_utids_array),', ');
		$q="UPDATE permission
		SET p_user_type_ids='$new_utids_csv'
		Where p_id='$p_id'";
		$r=mysql_query($q)or die(mysql_error());
	}
}
function removeUserTypeIdInPermission($p_id,$user_type_id_to_remove){
	$existing_utids_csv=userTypeIdsFromPId($p_id);
	$existing_utids_array=explode(',',$existing_utids_csv);
	if(in_array($user_type_id_to_remove,$existing_utids_array)){
		$new_utids_csv=trim(str_replace(",$user_type_id_to_remove,","",",$existing_utids_csv,"),', ');
		$q="UPDATE permission
		SET p_user_type_ids='$new_utids_csv'
		Where p_id='$p_id'";
		$r=mysql_query($q)or die(mysql_error());
	}
}
/***********************************************************/
/*
 *	Inserts uer_type_id in 'p_user_type_ids' field of each p_id
*
*/
function updatePermissionTable($p_ids_array,$user_type_id){
	$q="SELECT * FROM permission";
	$r=mysql_query($q)or die(mysql_error());
	if(mysql_num_rows($r)){
		$pa=mysql_fetch_rowsarr($r);
		foreach($pa as $a){
			if(count($p_ids_array)>0){
				if(in_array($a[p_id],$p_ids_array)){
					addUserTypeIdInPermission($a[p_id],$user_type_id);
				}
				else{
					removeUserTypeIdInPermission($a[p_id],$user_type_id);
				}
			}
			else{
				removeUserTypeIdInPermission($a[p_id],$user_type_id);
			}
		}
	}
}
/***********************************************************/
/*
 *	returns a string of CSV values of user_type_ids matching 'module_system_name' and 'action'
*/
function userTypeIdsPermittedForAction($module_system_name,$action){
	$q="select p_user_type_ids from permission where p_module_system_name='$module_system_name' and p_action='$action'";
	$r=mysql_query($q)or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return trim($a['p_user_type_ids'],', ');
}
/***********************************************************/
/*
 *	returns a string of CSV values of user_type_ids
*/
function userTypeIdsFromPId($p_id){
	$q="select p_user_type_ids from permission where p_id='$p_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return trim($a['p_user_type_ids'],', ');
}
/***********************************************************/
/*
 *	Returns total quantity under a purchase order
*/
function getTotalQuantityFrmPoId($po_id){
	$q = "select * from podetails where podetails_po_id='" .$po_id. "'";
	$r = mysql_query($q) or die("<b>Query:</b> $q <br>");
	$total_per_po = 0;
	if (mysql_num_rows($r) > 0) {
		$po_details = mysql_fetch_rowsarr($r);
		foreach ($po_details as $pod) {
			for ($j = 1; $j <= 10; $j++) {
				//echo $pod["podetails_$j"];
				$total_per_po+=$pod["podetails_$j"];
			}
		}
	}
	return $total_per_po;
}
function currentUserLevel(){
	return $_SESSION[current_user_type_level];
}
function currentUserTypeId(){
	return $_SESSION[current_user_type_id];
}
function hasApprover($user_ids_csv){
	if(strlen(trim($user_ids_csv,', '))){
		$q="select user_id from user
		where 	user_id in ($user_ids_csv)
		AND user_type_id in(
		Select p_user_type_ids from permission where p_module_system_name='costsheet' and p_action='approve'
		)";
		$r=mysql_query($q)or die(mysql_error(). "- hasApprover()");
		if(mysql_num_rows($r))return true;
	}
	return false;
}
function newerCostsheetExists($costsheet_id){
	$q="select costsheet_uid,costsheet_prepared_date from costsheet where costsheet_id='$costsheet_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a_costsheet_1=mysql_fetch_assoc($r);
	$q="select costsheet_prepared_date from costsheet where costsheet_uid='".$a_costsheet_1[costsheet_uid]."' order by costsheet_prepared_date desc limit 0,1";
	$r=mysql_query($q)or die(mysql_error());
	$a_costsheet_2=mysql_fetch_assoc($r);
	//echo $a_costsheet_1[costsheet_prepared_date]."-".$a_costsheet_2[costsheet_prepared_date];
	if($a_costsheet_1[costsheet_prepared_date]!=$a_costsheet_2[costsheet_prepared_date]){
		return true;
	}else return false;
}
function newerPurchaseOrderExists($po_id){
	$q="select po_uid,po_prepared_date from purchaseorder where po_id='$po_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a_po_1=mysql_fetch_assoc($r);
	$q="select po_prepared_date from purchaseorder where po_uid='".$a_po_1[po_uid]."' order by po_prepared_date desc limit 0,1";
	$r=mysql_query($q)or die(mysql_error());
	$a_po_2=mysql_fetch_assoc($r);
	if($a_po_1[po_prepared_date]!=$a_po_2[po_prepared_date]){
		return true;
	}else return false;
}
/**************************************************************
 *	Costsheet specific functions
***************************************************************/
function getCostsheetUidFromId($costsheet_id){
	$q="select costsheet_uid from costsheet where costsheet_id='$costsheet_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[costsheet_uid];
}
function initCostsheetApprovalRequest($costsheet_id,$user_id){
	global $mail;
	global $scriptpath;
	global $alert;
	$q="select * from costsheet where costsheet_id='$costsheet_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a_costsheet=mysql_fetch_assoc($r);
	if($a_costsheet[costsheet_approval_state]=='Approved'){
		array_push($alert,"This costsheet is already approved");
	}else if($a_costsheet[costsheet_approval_state]=='Requested_approval'){
		array_push($alert,"Already requested for Approval");
	}else{
		/*
		 *	Update status in costsheet table
		*/
		$sql= 	"UPDATE costsheet
		set
		costsheet_approval_state='Requested_approval',
		costsheet_freeze='1'
		where
		costsheet_id='$costsheet_id'";
		$r=mysql_query($sql)or die(mysql_error());
		$q="select * from costsheet where costsheet_id='$costsheet_id'";
		$r=mysql_query($q)or die(mysql_error());
		$a_costsheet=mysql_fetch_assoc($r);
		/*
		 *	Update status in costsheet_history table
		*/
		$sql= 	"INSERT INTO costsheet_history(
		ch_costsheet_id,
		ch_costsheet_uid,
		ch_action,
		ch_user_id,
		ch_datetime)
		values(
		'".$costsheet_id."',
		'".getCostsheetUidFromId($costsheet_id)."',
		'Requested_approval',
		'".$user_id."',
		now())";
		$r=mysql_query($sql)or die(mysql_error());
		/*
		 *	Send E-mail
		*/
		$mail->From = "info.cost_quote@textilehorizon.com";
		$mail->AddAddress(getUserEmailFrmId($a_costsheet[costsheet_approver_user_id]),getUserNameFrmId($a_costsheet[costsheet_approver_user_id]));
		$mail->AddCC(getUserEmailFrmId($a_costsheet[costsheet_prepared_by]),getUserNameFrmId($a_costsheet[costsheet_prepared_by]));
		$Subject = "Costsheet approval Requested -".$a_costsheet['costsheet_title'].": Client-".getClientCompanyNameFrmId($a_costsheet['costsheet_client_id']);
		$lb='<br/>';
		$Body =
		"<span style='font-family:Courier New, Courier, monospace; font-size:12px'>".
		"Costsheet Information $lb".
		"========================== $lb".
		"Costsheet Approver: 				".getUserNameFrmId($a_costsheet[costsheet_approver_user_id])."$lb".
		"Costsheet Approval status:			".$a_costsheet[costsheet_approval_state]."$lb".
		"Costsheet Approval requested by:	".getUserNameFrmId($user_id)."$lb".
		"Costsheet title: 					".$a_costsheet[costsheet_title]."$lb".
		"Costsheet Client: 					".getClientCompanyNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet Client: 					".getClientContactNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet prepared by: 			".getUserNameFrmId($a_costsheet[costsheet_prepared_by])."$lb".
		"==========================$lb".
		"Click the url below to go to approve page$lb".
		"<a href='".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."'>
		".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."
		</a>$lb".
		"==========================$lb".
		"</span>"
		;      //HTML Body
		$mail->Subject = $Subject;
		$mail->Body = $Body;
		if(!$mail->Send()){
			echo "Mailer Error: " . $mail->ErrorInfo;
		}else{
			array_push($alert,"E-mail has been sent");
			$sql= 	"INSERT INTO email(
			email_from,
			email_to,
			email_cc,
			email_bcc,
			email_subject,
			email_body,
			email_datetime)
			values(
			'".$mail->From."',
			'".getUserEmailFrmId($a_costsheet[costsheet_approver_user_id])."',
			'".getUserEmailFrmId($a_costsheet[costsheet_prepared_by])."',
			'',
			'".$mail->Subject."',
			'".mysql_real_escape_string($Body)."',
			now())";
			$r=mysql_query($sql)or die(mysql_error());
			$sql= 	"INSERT INTO history(
			history_component,
			history_datetime,
			history_textlog,
			history_user_id,
			history_client_id,
			history_costsheet_id,
			history_po_id)
			values(
			'costsheet',
			now(),
			'Costsheet approval requested',
			'',
			'',
			'$costsheet_id',
			'')";
			$r=mysql_query($sql)or die(mysql_error());
		}
	}
}
function initCostsheetApprove($costsheet_id,$user_id){
	global $mail;
	global $scriptpath;
	global $alert;
	$q="select * from costsheet where costsheet_id='$costsheet_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a_costsheet=mysql_fetch_assoc($r);
	if($a_costsheet[costsheet_approval_state]=='Approved'){
		array_push($alert,"This costsheet is already approved");
	}else if($a_costsheet[costsheet_approver_user_id]!=$user_id){
		array_push($alert,"You are not the selected approve/disapprove for this costsheet.");
	}else{
		/*
		 *	Update status in costsheet table. 'Unapproved','Requested_approval','Disapproved','Approved'
		*/
		$sql= 	"UPDATE costsheet
		set
		costsheet_approval_state='Approved',
		costsheet_freeze='1'
		where
		costsheet_id='$costsheet_id'";
		$r=mysql_query($sql)or die(mysql_error());
		$q="select * from costsheet where costsheet_id='$costsheet_id'";
		$r=mysql_query($q)or die(mysql_error());
		$a_costsheet=mysql_fetch_assoc($r);
		/*
		 *	Update status in costsheet_history table
		*/
		$sql= 	"INSERT INTO costsheet_history(
		ch_costsheet_id,
		ch_costsheet_uid,
		ch_action,
		ch_user_id,
		ch_datetime)
		values(
		'".$costsheet_id."',
		'".getCostsheetUidFromId($costsheet_id)."',
		'Approved',
		'".$user_id."',
		now())";
		$r=mysql_query($sql)or die(mysql_error());
		/*
		 *	Send E-mail
		*/
		$mail->From = "info.cost_quote@textilehorizon.com";
		$mail->AddCC(getUserEmailFrmId($a_costsheet[costsheet_approver_user_id]),getUserNameFrmId($a_costsheet[costsheet_approver_user_id]));
		$mail->AddAddress(getUserEmailFrmId($a_costsheet[costsheet_prepared_by]),getUserNameFrmId($a_costsheet[costsheet_prepared_by]));
		$Subject = "Costsheet Approved -".$a_costsheet['costsheet_title'].": Client-".getClientCompanyNameFrmId($a_costsheet['costsheet_client_id']);
		$lb='<br/>';
		$Body =
		"<span style='font-family:Courier New, Courier, monospace; font-size:12px'>".
		"Costsheet Information $lb".
		"========================== $lb".
		"Costsheet Approver: 				".getUserNameFrmId($a_costsheet[costsheet_approver_user_id])."$lb".
		"Costsheet Approval status:			".$a_costsheet[costsheet_approval_state]."$lb".
		"Costsheet Approval requested by:	".getUserNameFrmId($user_id)."$lb".
		"Costsheet title: 					".$a_costsheet[costsheet_title]."$lb".
		"Costsheet Client: 					".getClientCompanyNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet Client: 					".getClientContactNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet prepared by: 			".getUserNameFrmId($a_costsheet[costsheet_prepared_by])."$lb".
		"==========================$lb".
		"Click the url below to go to approve page$lb".
		"<a href='".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."'>
		".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."
		</a>$lb".
		"==========================$lb".
		"</span>"
		;      //HTML Body
		$mail->Subject = $Subject;
		$mail->Body = $Body;
		if(!$mail->Send()){
			echo "Mailer Error: " . $mail->ErrorInfo;
		}else{
			array_push($alert,"E-mail has been sent");
			$sql= 	"INSERT INTO email(
			email_from,
			email_to,
			email_cc,
			email_bcc,
			email_subject,
			email_body,
			email_datetime)
			values(
			'".$mail->From."',
			'".getUserEmailFrmId($a_costsheet[costsheet_prepared_by])."',
			'".getUserEmailFrmId($a_costsheet[costsheet_approver_user_id])."',
			'',
			'".$mail->Subject."',
			'".mysql_real_escape_string($Body)."',
			now())";
			$r=mysql_query($sql)or die(mysql_error());
			$sql= 	"INSERT INTO history(
			history_component,
			history_datetime,
			history_textlog,
			history_user_id,
			history_client_id,
			history_costsheet_id,
			history_po_id)
			values(
			'costsheet',
			now(),
			'Costsheet Approved',
			'',
			'',
			'$costsheet_id',
			'')";
			$r=mysql_query($sql)or die(mysql_error());
		}
	}
}
function initCostsheetDisapprove($costsheet_id,$user_id){
	global $mail;
	global $scriptpath;
	global $alert;
	$q="select * from costsheet where costsheet_id='$costsheet_id'";
	$r=mysql_query($q)or die(mysql_error());
	$a_costsheet=mysql_fetch_assoc($r);
	if($a_costsheet[costsheet_approval_state]=='Dispproved'){
		array_push($alert,"This costsheet is already disapproved");
	}else if($a_costsheet[costsheet_approver_user_id]!=$user_id){
		array_push($alert,"You are not the selected approve/disapprove for this costsheet.");
	}else{
		/*
		 *	Update status in costsheet table. 'Unapproved','Requested_approval','Disapproved','Approved'
		*/
		$sql= 	"UPDATE costsheet
		set
		costsheet_approval_state='Dispproved',
		costsheet_freeze='0'
		where
		costsheet_id='$costsheet_id'";
		$r=mysql_query($sql)or die(mysql_error());
		$q="select * from costsheet where costsheet_id='$costsheet_id'";
		$r=mysql_query($q)or die(mysql_error());
		$a_costsheet=mysql_fetch_assoc($r);
		/*
		 *	Update status in costsheet_history table
		*/
		$sql= 	"INSERT INTO costsheet_history(
		ch_costsheet_id,
		ch_costsheet_uid,
		ch_action,
		ch_user_id,
		ch_datetime)
		values(
		'".$costsheet_id."',
		'".getCostsheetUidFromId($costsheet_id)."',
		'Dispproved',
		'".$user_id."',
		now())";
		$r=mysql_query($sql)or die(mysql_error());
		/*
		 *	Send E-mail
		*/
		$mail->From = "info.cost_quote@textilehorizon.com";
		$mail->AddCC(getUserEmailFrmId($a_costsheet[costsheet_approver_user_id]),getUserNameFrmId($a_costsheet[costsheet_approver_user_id]));
		$mail->AddAddress(getUserEmailFrmId($a_costsheet[costsheet_prepared_by]),getUserNameFrmId($a_costsheet[costsheet_prepared_by]));
		$Subject = "Costsheet Dispproved -".$a_costsheet['costsheet_title'].": Client-".getClientCompanyNameFrmId($a_costsheet['costsheet_client_id']);
		$lb='<br/>';
		$Body =
		"<span style='font-family:Courier New, Courier, monospace; font-size:12px'>".
		"Costsheet Information $lb".
		"========================== $lb".
		"Costsheet Approver: 				".getUserNameFrmId($a_costsheet[costsheet_approver_user_id])."$lb".
		"Costsheet Approval status:			".$a_costsheet[costsheet_approval_state]."$lb".
		"Costsheet Approval requested by:	".getUserNameFrmId($user_id)."$lb".
		"Costsheet title: 					".$a_costsheet[costsheet_title]."$lb".
		"Costsheet Client: 					".getClientCompanyNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet Client: 					".getClientContactNameFrmId($a_costsheet[costsheet_client_id])."$lb".
		"Costsheet prepared by: 			".getUserNameFrmId($a_costsheet[costsheet_prepared_by])."$lb".
		"==========================$lb".
		"Click the url below to go to approve page$lb".
		"<a href='".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."'>
		".$scriptpath."/costsheet_add.php?costsheet_id=".$a_costsheet[costsheet_id]."&param=view&client_id=".$a_costsheet[costsheet_client_id]."
		</a>$lb".
		"==========================$lb".
		"</span>"
		;      //HTML Body
		$mail->Subject = $Subject;
		$mail->Body = $Body;
		if(!$mail->Send()){
			echo "Mailer Error: " . $mail->ErrorInfo;
		}else{
			array_push($alert,"E-mail has been sent");
			$sql= 	"INSERT INTO email(
			email_from,
			email_to,
			email_cc,
			email_bcc,
			email_subject,
			email_body,
			email_datetime)
			values(
			'".$mail->From."',
			'".getUserEmailFrmId($a_costsheet[costsheet_prepared_by])."',
			'".getUserEmailFrmId($a_costsheet[costsheet_approver_user_id])."',
			'',
			'".$mail->Subject."',
			'".mysql_real_escape_string($Body)."',
			now())";
			$r=mysql_query($sql)or die(mysql_error());
			$sql= 	"INSERT INTO history(
			history_component,
			history_datetime,
			history_textlog,
			history_user_id,
			history_client_id,
			history_costsheet_id,
			history_po_id)
			values(
			'costsheet',
			now(),
			'Costsheet Approved',
			'',
			'',
			'$costsheet_id',
			'')";
			$r=mysql_query($sql)or die(mysql_error());
		}
	}
}
/*
 *	function checks whether there is a FabricOrder that exists Against a PO UID
*/
function hasFabricOrderAgainstPo($fo_po_uid){
	$q="select * from fabric_order where fo_po_uid='$fo_po_uid'";
	$r=mysql_query($q)or die(mysql_error()."<br/>QUERY: $q<br/>");
	if(mysql_num_rows($r)){
		//echo "hasFabricOrderAgainstPo($fo_po_uid): TRUE";
		return true;
	}else{
		//echo "hasFabricOrderAgainstPo($fo_po_uid): FALSE";
		return false;
	}
	//$a=mysql_fetch_assoc($r);
}
function consumptionPerSizePerColor($foc_id, $size_column_no){
	$q="SELECT * FROM fabric_order_consumption WHERE foc_id='$foc_id' ";
	//echo $q;
	$r_foq=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r_foq)){
		$a_foq=mysql_fetch_assoc($r_foq);
		$q="SELECT * FROM podetails WHERE podetails_id='".$a_foq[foc_podetails_id]."'";
		$r_pod=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
		$a_pod=mysql_fetch_assoc($r_pod);
		if(getFab1CalTypeFrmFoId($a_foq["foc_fo_id"])=="Average"){
			return round(($a_pod["podetails_".$size_column_no]/12)*getFab1CosPerDozFrmFoId($a_foq["foc_fo_id"]),2);
		}else if(getFab1CalTypeFrmFoId($a_foq["foc_fo_id"])=="Size wise"){
			return round(($a_pod["podetails_".$size_column_no]/12)*$a_foq["foc_".$size_column_no],2);
		}
	}else{
		$norecordfound= "No records found";
	}
}
/*
 * returns fo_fab1_cal_type from TABLE: fabric_order where ID= fo_id
*/
function getFab1CalTypeFrmFoId($fo_id){
	$q="SELECT fo_fab1_cal_type FROM fabric_order WHERE fo_id='$fo_id' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a["fo_fab1_cal_type"];
	}else{
		echo "Query did not retunr any row: <br/> $q ";
		return false;
	}
}
/*
 * returns fo_fab1_consperdoz from TABLE: fabric_order where ID= fo_id
*/
function getFab1CosPerDozFrmFoId($fo_id){
	$q="SELECT fo_fab1_consperdoz FROM fabric_order WHERE fo_id='$fo_id' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a["fo_fab1_consperdoz"];
	}else{
		echo "Query did not retunr any row: <br/> $q ";
		return false;
	}
}
/*
 *	Return a field value from  a TABLE row mapping with ID
*/
function retVal($tableName,$idColumnName, $idval, $columnName){
	$q="SELECT $columnName FROM $tableName WHERE $idColumnName='$idval' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a["$columnName"];
	}else{
		echo "Query did not retunr any row: <br/> $q ";
		return false;
	}
}
function getFabricCompositionOptionFrmId($fco_id){
	$q="SELECT fco_name FROM fabric_composition_options WHERE fco_id='$fco_id' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a["fco_name"];
	}else{
		echo "Query did not retunr any row: <br/> $q ";
		return false;
	}
}
function getFabricTypeNameFrmId($fabric_type_id){
	$q="SELECT fabric_type_name FROM fabric_type WHERE fabric_type_id='$fabric_type_id' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_num_rows($r)){
		$a=mysql_fetch_assoc($r);
		return $a["fabric_type_name"];
	}else{
		echo "Query did not retunr any row: <br/> $q ";
		return false;
	}
}
function finalizePoQuantity($po_id){
	global $alert, $valid;
	$q="UPDATE purchaseorder set
	po_quantity_finalized='1',
	po_quantity_finalized_by_user_id='".$_SESSION[current_user_id]."',
	po_quantity_finalized_datetime=now()
	WHERE po_id='$po_id' ";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
	if(mysql_affected_rows()){
		$valid = true;
		array_push($alert, "PO has been finalized");
		return true;
	}else{
		$valid = false;
		array_push($alert, "PO has not been finalized");
		return false;
	}
}
?>