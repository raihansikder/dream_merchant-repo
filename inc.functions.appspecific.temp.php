<?php
// including the global variables
include_once('inc.globalvariables.php');

/*
 * 	This is a temporary script to test new functions. This funcitons will be migrated to inc.funtions.appspecific.php once approved
*/

/*
 * Author : Raihan Sikder
* email: raihan@activationltd.com
*/

function testFuncT($param) {
	return 0;
}
 

/* * **************************************************** */


/*
 * Author : Enamul Haque
* email: raihan@activationltd.com
*/

function totalFabricCost($costsheet_id) {
	$r = mysql_query("select * from costsheet where costsheet_id='$costsheet_id' ") or die(mysql_error());
	$a = mysql_fetch_assoc($r);

	if ($a[costsheet_unitofmeasures] == 'INCH') {
		$a[costsheet_bodylength]+=4;
	} else if ($a[costsheet_unitofmeasures] == 'CM') {
		$a[costsheet_bodylength]+=10;
	}

	//echo $a[costsheet_bodylength]."-";
	/*     * ******************************* */
	if ($a[costsheet_unitofmeasures] == 'INCH')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'CM')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;

	$total_fabric1_consumption_wastage = $total_fabric1_consumption_withoout_wastage * $a[costsheet_fabric1_consumptionwastage] / 100;
	$total_fabric1_consumption_kg_with_wastage = ($total_fabric1_consumption_withoout_wastage + $total_fabric1_consumption_wastage) * 12;

	if ($a[costsheet_unitofmeasures] == 'inch')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'cm')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;

	$total_fabric_cost_without_wastage = $a[costsheet_yarnpriceperkg] + $a[costsheet_lycraprice] + $a[costsheet_knittingcost] + $a[costsheet_dyeingcost]
	+ $a[costsheet_extrafinishingcost];
	$total_fabric_consumption_per_doz = $total_fabric1_consumption_kg_with_wastage + $a[costsheet_fabric2_consumption];



	$total_fabric_cost_without_wastage =
	$a[costsheet_yarnpriceperkg] +
	$a[costsheet_lycraprice] +
	$a[costsheet_knittingcost] +
	$a[costsheet_dyeingcost] +
	$a[costsheet_extrafinishingcost];

	$total_fabric_cost_with_wastage = ($total_fabric_cost_without_wastage + ($total_fabric_cost_without_wastage * $a[costsheet_pricewestage] / 100));
	$costsheet_additional_febric_quantity_costperdozen = $a[costsheet_additional_febric_quantity] * $a[costsheet_additional_febric_unitprice];
	$total_fabric_cost = ($total_fabric_cost_with_wastage * $total_fabric_consumption_per_doz) + $costsheet_additional_febric_quantity_costperdozen;

	return round($total_fabric_cost, 2);
}

function FabricConsumption($costsheet_id) {
	$r = mysql_query("select * from costsheet where costsheet_id='$costsheet_id' ") or die(mysql_error());
	$a = mysql_fetch_assoc($r);

	if ($a[costsheet_unitofmeasures] == 'INCH') {
		$a[costsheet_bodylength]+=4;
	} else if ($a[costsheet_unitofmeasures] == 'CM') {
		$a[costsheet_bodylength]+=10;
	}

	//echo $a[costsheet_bodylength]."-";
	/*     * ******************************* */
	if ($a[costsheet_unitofmeasures] == 'INCH')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'CM')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;

	$total_fabric1_consumption_wastage = $total_fabric1_consumption_withoout_wastage * $a[costsheet_fabric1_consumptionwastage] / 100;
	$total_fabric1_consumption_kg_with_wastage = ($total_fabric1_consumption_withoout_wastage + $total_fabric1_consumption_wastage) * 12;

	if ($a[costsheet_unitofmeasures] == 'inch')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'cm')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;
	$total_fabric_consumption_per_doz = $total_fabric1_consumption_kg_with_wastage + $a[costsheet_fabric2_consumption];
	return round($total_fabric_consumption_per_doz, 2);
}

function totalTrimCost($costsheet_id) {
	$r = mysql_query("select * from costsheet where costsheet_id='$costsheet_id' ") or die(mysql_error());
	$a = mysql_fetch_assoc($r);
	$costsheet_sewing_thread_quantity_costperdozen = ($a[costsheet_sewing_thread_quantity] / 4000) * $a[costsheet_sewing_thread_price];
	$total_trim_cost_per_dozen = $costsheet_sewing_thread_quantity_costperdozen;
	$q = "Select * from costsheet_accessories where ca_costsheet_id='$costsheet_id' and ca_active='1' ";
	$qq = mysql_query($q) or die(mysql_error());
	$ca_rows = mysql_num_rows($qq);
	if ($ca_rows > 0) {
		$a_ca = mysql_fetch_rowsarr($qq);
	}
	for ($i = 0; $i < $ca_rows; $i++) {
		$temp_acc_cost_per_dozen = $a_ca[$i]["ca_quantity_per_dozen"] * $a_ca[$i]["ca_unit_price_per_dozen"];
		$total_trim_cost_per_dozen+=$temp_acc_cost_per_dozen;
	}

	$total_cost_perdozen_with_wastage = $total_trim_cost_per_dozen + ($total_trim_cost_per_dozen * .05);
	return round($total_cost_perdozen_with_wastage, 2);
}

function GarmentsPrice($costsheet_id) {
	$r = mysql_query("select * from costsheet where costsheet_id='$costsheet_id' ") or die(mysql_error());
	$a = mysql_fetch_assoc($r);

	if ($a[costsheet_unitofmeasures] == 'INCH') {
		$a[costsheet_bodylength]+=4;
	} else if ($a[costsheet_unitofmeasures] == 'CM') {
		$a[costsheet_bodylength]+=10;
	}
	if ($a[costsheet_unitofmeasures] == 'INCH')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'CM')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;

	$total_fabric1_consumption_wastage = $total_fabric1_consumption_withoout_wastage * $a[costsheet_fabric1_consumptionwastage] / 100;
	$total_fabric1_consumption_kg_with_wastage = ($total_fabric1_consumption_withoout_wastage + $total_fabric1_consumption_wastage) * 12;

	if ($a[costsheet_unitofmeasures] == 'inch')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 1) * 2 * $a[costsheet_gsm]) / 1550000;
	else if ($a[costsheet_unitofmeasures] == 'cm')
		$total_fabric1_consumption_withoout_wastage = (($a[costsheet_bodylength] + $a[costsheet_sleevelength]) * ($a[costsheet_bodywidth] + 2.54) * 2 * $a[costsheet_gsm]) / 1000000;

	$total_fabric_cost_without_wastage = $a[costsheet_yarnpriceperkg] + $a[costsheet_lycraprice] + $a[costsheet_knittingcost] + $a[costsheet_dyeingcost]
	+ $a[costsheet_extrafinishingcost];
	$total_fabric_consumption_per_doz = $total_fabric1_consumption_kg_with_wastage + $a[costsheet_fabric2_consumption];

	$total_fabric_cost_without_wastage =
	$a[costsheet_yarnpriceperkg] +
	$a[costsheet_lycraprice] +
	$a[costsheet_knittingcost] +
	$a[costsheet_dyeingcost] +
	$a[costsheet_extrafinishingcost];

	$total_fabric_cost_with_wastage = ($total_fabric_cost_without_wastage + ($total_fabric_cost_without_wastage * $a[costsheet_pricewestage] / 100));
	$costsheet_additional_febric_quantity_costperdozen = $a[costsheet_additional_febric_quantity] * $a[costsheet_additional_febric_unitprice];
	$total_fabric_cost = ($total_fabric_cost_with_wastage * $total_fabric_consumption_per_doz) + $costsheet_additional_febric_quantity_costperdozen;


	$costsheet_sewing_thread_quantity_costperdozen = ($a[costsheet_sewing_thread_quantity] / 4000) * $a[costsheet_sewing_thread_price];
	$total_trim_cost_per_dozen = $costsheet_sewing_thread_quantity_costperdozen;

	$q = "Select * from costsheet_accessories where ca_costsheet_id='$costsheet_id' and ca_active='1' ";
	$qq = mysql_query($q) or die(mysql_error());
	$ca_rows = mysql_num_rows($qq);
	if ($ca_rows > 0) {
		$a_ca = mysql_fetch_rowsarr($qq);
	}
	for ($i = 0; $i < $ca_rows; $i++) {
		$temp_acc_cost_per_dozen = $a_ca[$i]["ca_quantity_per_dozen"] * $a_ca[$i]["ca_unit_price_per_dozen"];
		$total_trim_cost_per_dozen+=$temp_acc_cost_per_dozen;
	}

	$total_cost_perdozen_with_wastage = $total_trim_cost_per_dozen + ($total_trim_cost_per_dozen * .05);
	$banking_cost = (($total_cost_usd_perdozen_with_wastage + $a[costsheet_printembroidery_price] + $a[costsheet_cm_price]) * $a[costsheet_bankothers]) / 100;
	$total_garments_cost_perdozen = $total_fabric_cost + $total_cost_perdozen_with_wastage + $a[costsheet_printembroidery_price] + $a[costsheet_cm_price] + $banking_cost;
	$margin_perdozen = round((($total_garments_cost_perdozen * $a[costsheet_margin]) / 100), 2);
	$garments_fob_price_perdozen = $total_garments_cost_perdozen + $margin_perdozen;
	$garments_price = round(($garments_fob_price_perdozen + $a[costsheet_freight_charges]), 2);
	return ($garments_price);
}

/*
 * Author 		: Nasir Khan
* email		: nasir.khan@activationltd.com
* updated on           : 26/07/12
* codeblock type       : function/procedure
* Name			: cleanUpTmpDir
* parameter(s)         : $secondsOld
* Output		: delete files from a directory in that are $secondsOld seconds old.
directory is specifed in $tmpDir [in FILE: inc.globalvariables.php]
* Developers Note      : included the inc.globalvariables.php for using the variable $tmpDir
*/
function getFacilityNameFrmId($facility_id)
{
	$r=mysql_query("select facility_name from facility where facility_id='$facility_id'")or die(mysql_error());
	$a=mysql_fetch_assoc($r);
	return $a[facility_name];
}


function getFacilityMonthlyCapacity($facility_id,$year,$month_number)
{
	$sql = "select capacity_quantity,capacity_number_of_production_line, capacity_total_work_hour_per_day, capacity_no_of_workdays_in_month from capacity where capacity_facility_id='$facility_id' and capacity_month='$month_number' and capacity_year='$year'";
	$r = mysql_query($sql)or die(mysql_error());
	$result= mysql_fetch_array($r);
	
	$capacity_quantity= $result[capacity_quantity]; 
	$capacity_number_of_production_line= $result[capacity_number_of_production_line]; 
	$capacity_total_work_hour_per_day= $result[capacity_total_work_hour_per_day]; 
	$capacity_no_of_workdays_in_month= $result[capacity_no_of_workdays_in_month]; 
		
	$total_capacity= ($capacity_quantity * $capacity_number_of_production_line * $capacity_total_work_hour_per_day *  $capacity_no_of_workdays_in_month);
	
	return $total_capacity;
	
}
function getTotalMonthlyCapacityAllFacilities($year, $month_number) {
    $sql = "select capacity_quantity,capacity_number_of_production_line, capacity_total_work_hour_per_day, capacity_no_of_workdays_in_month from capacity where capacity_year='$year' and capacity_month='$month_number'";
    $r = mysql_query($sql) or die(mysql_error());
    $result = mysql_fetch_rowsarr($r);
    $rows = mysql_num_rows($r);

    $total_capacity = 0;

    while ($rows > 0) {
        $total_capacity += ($result[$rows - 1][capacity_quantity] * $result[$rows - 1][capacity_number_of_production_line] * $result[$rows - 1][capacity_total_work_hour_per_day] * $result[$rows - 1][capacity_no_of_workdays_in_month]);
        $rows--;
    }

    return $total_capacity;
}
function getFacilityCapacityMonthRange($facility_id,$year,$start_month_number, $end_month_number)
{
	$sql = "select capacity_quantity,capacity_number_of_production_line, capacity_total_work_hour_per_day, capacity_no_of_workdays_in_month from capacity where capacity_facility_id='$facility_id' and capacity_year='$year' and capacity_month >= '$start_month_number' and capacity_month <= '$end_month_number'";
    $r = mysql_query($sql) or die(mysql_error());
    $result = mysql_fetch_rowsarr($r);
    $rows = mysql_num_rows($r);
	
    $total_capacity = 0;

    while ($rows > 0) {
        $total_capacity += ($result[$rows - 1][capacity_quantity] * $result[$rows - 1][capacity_number_of_production_line] * $result[$rows - 1][capacity_total_work_hour_per_day] * $result[$rows - 1][capacity_no_of_workdays_in_month]);
        $rows--;
    }

    return $total_capacity;
}


function cleanUpTmpDir($secondsOld) {
	if ($handle = opendir("$tmpDir/")) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				if (filemtime($entry) < (time() - $secondsOld)) {
					error_log("File deleted: $tmpDir/$entry on date(DATE_RFC822)", 0); /* store deleted file info in the error log */
					unlink("$tmpDir/$entry");
				}
			}
		}
		closedir($handle);
	}

	return 0;
}

// Author: Enamul Haque
//Date: 12-05-2012
function getmonthNameFromNumber($month_number)
{
	if ($month_number==1)
	$month_name = "January";
	if ($month_number==2)
	$month_name = "February";
	if ($month_number==3)
	$month_name = "March";
	if ($month_number==4)
	$month_name = "April";
	if ($month_number==5)
	$month_name = "May";
	if ($month_number==6)
	$month_name = "June";
	if ($month_number==7)
	$month_name = "July";
	if ($month_number==8)
	$month_name = "August";
	if ($month_number==9)
	$month_name = "September";
	if ($month_number==10)
	$month_name = "October";
	if ($month_number==11)
	$month_name = "November";
	if ($month_number==12)
	$month_name = "December";
	
	return $month_name;
}

//    nasir khan
//    27-Dec, 2012    
function getMonthlyTotalQuantitySpecificTypeByClient($po_client_id, $po_category_id, $year,$monthNo){
	$q = "SELECT * from podetails,purchaseorder where 
                po_client_id='" .$po_client_id. "' AND 
                po_category_id='$po_category_id' AND 
                po_shipment_date>='$year-$monthNo-01' AND po_shipment_date<='$year-$monthNo-31'
                AND po_id = podetails_po_id";
        
	$r = mysql_query($q) or die("<b>Query:</b> $q <br>");
       // $qq = mysql_query("SELECT * FROM order")or die("<b>Query:</b> $qq <br>");
//        echo "<b>Query:</b> $q <br>";        
	$total_per_po = 0;
	if (mysql_num_rows($r) > 0) {
		$po_details = mysql_fetch_rowsarr($r);
		foreach ($po_details as $pod) {
			for ($j = 1; $j <= 10; $j++) {
				$total_per_po+=$pod["podetails_$j"];
			}
		}
	}
	return $total_per_po;
}
function getMonthlyTotalQuantitySpecificType($po_category_id, $year,$monthNo){
	$q = "select * from podetails,purchaseorder where 
                po_category_id='$po_category_id' AND 
                po_shipment_date>='$year-$monthNo-01' AND po_shipment_date<='$year-$monthNo-31'
                AND po_id=podetails_po_id";
	$r = mysql_query($q) or die("<b>Query:</b> $q <br>");
//        echo "<b>Query:</b> $q <br>";        
	$total_per_po = 0;
	if (mysql_num_rows($r) > 0) {
		$po_details = mysql_fetch_rowsarr($r);
		foreach ($po_details as $pod) {
			for ($j = 1; $j <= 10; $j++) {
				$total_per_po+=$pod["podetails_$j"];
			}
		}
	}
	return $total_per_po;
}

?>