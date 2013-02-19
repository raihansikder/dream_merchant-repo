<?php
$r = mysql_query("
		select * from purchaseorder s1
		where
		po_prepared_date=
		(select max(s2.po_prepared_date) from purchaseorder s2
		where s1.po_uid=s2.po_uid);") or die(mysql_error());
$stat = array();
$row_count = mysql_num_rows($r);
$i = 0;
if (mysql_num_rows($r) > 0) {
	$po = mysql_fetch_rowsarr($r);
	foreach ($po as $temp) {
		$stat[$i]["po_id"] = $temp["po_id"];
		$stat[$i]["po_category_id"] = $temp["po_category_id"];
		$stat[$i]["po_shipment_date"] = $temp["po_shipment_date"];
		$stat[$i]["po_total_quantity"] = 0;
		$q = "select * from podetails where podetails_po_id='" . $temp["po_id"] . "'";
		$r = mysql_query($q) or die("<b>Query:</b> $q <br>");
		if (mysql_num_rows($r) > 0) {
			$po_details = mysql_fetch_rowsarr($r);
			foreach ($po_details as $pod) {
				$total_per_po = 0;
				for ($j = 1; $j <= 10; $j++) {
					//echo $pod["podetails_$j"];
					$total_per_po+=$pod["podetails_$j"];
				}
				$stat[$i]["po_total_quantity"] += $total_per_po;
			}
			//myprint_r($po_details);
		}
		$i++;
	}
}
//myprint_r($stat);
/*
 * Author               : Nasir Khan
* email                : nasir.khan@activationltd.com
* updated on           : 05-08-2012
* codeblock type       : function/procedure
* Name                 : Month wise order calculation
* parameter(s)         : N/A
* Output               : data will be fetched form the database and based on a
* specific rule the month wise order will be calcualted
* Developers Note      :
*/
$standard_value_basic = 1.5;
$standard_value_semi_critic = 2.50;
$standard_value_cretical = 4.0;
$standard_value_extra_critical = 6;
$value_total_basic = array();
$value_total_semi_critic = array();
$value_total_cretical = array();
$value_total_extra_critical = array();
$total_month_value = array();
$current_year = date("Y");
$current_month = date("F");
//echo $current_year;
// check and count month wise po data
for ($i = 0; $i < $row_count; $i++) {
	$po_shipment_date = explode('-', $stat[$i]["po_shipment_date"]);
	$num = (int) $po_shipment_date[1];
	$set = 0;
	for ($j = 1; $j <= 12; $j++) {
		if ($num == $j && $set == 0) {
			if ($stat[$i]["po_category_id"] == "1") {
				$value_total_basic[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"];
				$set = 1;
			} else if ($stat[$i]["po_category_id"] == "2") {
				$value_total_semi_critic[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
				$set = 1;
			} else if ($stat[$i]["po_category_id"] == "3") {
				$value_total_cretical[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
				$set = 1;
			} else {
				$value_total_extra_critical[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
				$set = 1;
			}
		}
	}
}
/*
 * calculating the month value
*/
for ($i = 1; $i <= 12; $i++) {
	$total_month_value[$current_year][$i] = ($value_total_semi_critic[$current_year][$i] + $value_total_cretical[$current_year][$i] + $value_total_extra_critical[$current_year][$i]) / $standard_value_basic + $value_total_basic[$current_year][$i];
	$total_month_value[$current_year + 1][$i] = ($value_total_semi_critic[$current_year + 1][$i] + $value_total_cretical[$current_year + 1][$i] + $value_total_extra_critical[$current_year + 1][$i]) / $standard_value_basic + $value_total_basic[$current_year + 1][$i];
}
//month name array
$month_names = array();
$month_names[0] = "NONE";
$month_names[1] = "January";
$month_names[2] = "February";
$month_names[3] = "March";
$month_names[4] = "April";
$month_names[5] = "May";
$month_names[6] = "June";
$month_names[7] = "July";
$month_names[8] = "August";
$month_names[9] = "September";
$month_names[10] = "October";
$month_names[11] = "November";
$month_names[12] = "December";
/*
 * END OF CODE BLOCK : Month wise order calculation
*/
//Capacity quantity population from database
$compare_year = (int)$current_year;
$year_array =  array();
$year_array[0] = $compare_year-1;
$year_array[1] = $compare_year;
$year_array[2] = $compare_year+1;
//echo gettype($year_array);
//$capacity_count_month[][]=0;
//$compare_year = (int)$year_array[];
for($j=0; $j<=2; $j++){
for($i=1; $i<=12; $i++){
	$capacity_count_month[$j][$i] = 0;
	$sql_capacity = mysql_query("SELECT * FROM capacity WHERE capacity_active= '1' AND capacity_year= '$year_array[$j]' AND capacity_month= '$i'");
	if(mysql_num_rows($sql_capacity)>0)
	{
		while($row = mysql_fetch_assoc($sql_capacity))
		{
				$capacity_count_month[$j][$i] += $row["capacity_quantity"] * $row["capacity_number_of_production_line"] * $row["capacity_no_of_workdays_in_month"] * $row["capacity_total_work_hour_per_day"];				
				
				
		}
	}
}
//$capacity_count_month[$j][$i] += $capacity_count_month[$j][$i]; 
}
?>

<div class="moduleBlock">
  <h2>Order booking status & capacity
    <?php mysql_num_rows($sql_capacity);?>
  </h2>
  <div class="clear"></div>
  <div id="visualization" style="float: left;"></div>
  <div id="visualization_next_year" style="float: left;"></div>
  <!--
You are free to copy and use this sample in accordance with the terms of the
Apache license (http://www.apache.org/licenses/LICENSE-2.0.html)
--> 
  <!--Google graphc--> 
  <script type="text/javascript" src="http://www.google.com/jsapi"></script> 
  <!----> 
  <script type="text/javascript">
    google.load('visualization', '1', {packages: ['corechart']});
	
    function drawVisualization() {
        // Create and populate the data table.
        var data = google.visualization.arrayToDataTable([
            ['Month', 'Order Quantity', 'Capacity'],
            ['<?php echo $month_names[1] ?>',  <?php echo ceil($total_month_value[$current_year][1]) ?>,   <?php echo $capacity_count_month[1][1]; ?>],
            ['<?php echo $month_names[2] ?>',  <?php echo ceil($total_month_value[$current_year][2]) ?>,   <?php echo $capacity_count_month[1][2]; ?>],
            ['<?php echo $month_names[3] ?>',  <?php echo ceil($total_month_value[$current_year][3]) ?>,   <?php echo $capacity_count_month[1][3]; ?>],
            ['<?php echo $month_names[4] ?>',  <?php echo ceil($total_month_value[$current_year][4]) ?>,   <?php echo $capacity_count_month[1][4]; ?>],
            ['<?php echo $month_names[5] ?>',  <?php echo ceil($total_month_value[$current_year][5]) ?>,  <?php  echo $capacity_count_month[1][5]; ?>],
            ['<?php echo $month_names[6] ?>',  <?php echo ceil($total_month_value[$current_year][6]) ?>,   <?php echo $capacity_count_month[1][6]; ?>],
            ['<?php echo $month_names[7] ?>',  <?php echo ceil($total_month_value[$current_year][7]) ?>,   <?php echo $capacity_count_month[1][7]; ?>],
            ['<?php echo $month_names[8] ?>',  <?php echo ceil($total_month_value[$current_year][8]) ?>,   <?php echo $capacity_count_month[1][8]; ?>],
            ['<?php echo $month_names[9] ?>',  <?php echo ceil($total_month_value[$current_year][9]) ?>,  <?php echo $capacity_count_month[1][9]; ?>],
            ['<?php echo $month_names[10] ?>', <?php echo ceil($total_month_value[$current_year][10])?>,   <?php echo $capacity_count_month[1][10]; ?>],
            ['<?php echo $month_names[11] ?>', <?php echo ceil($total_month_value[$current_year][11])?>,   <?php echo $capacity_count_month[1][11]; ?>],
            ['<?php echo $month_names[12] ?>', <?php echo ceil($total_month_value[$current_year][12])?>,  <?php echo $capacity_count_month[1][12]; ?>],
        ]);
        // Create and draw the visualization.
        new google.visualization.ColumnChart(document.getElementById('visualization')).
            draw(data,
        {title:"Order Booking Details <?php echo $current_year ?>",
            width:375, height:200,
            hAxis: {title: "Year: <?php echo $current_year ?>"}}
    );
        var data_next_year = google.visualization.arrayToDataTable([
            ['Month', 'Order Quantity', 'Capacity'],
            ['<?php echo $month_names[1] ?>',  <?php echo ceil($total_month_value[$current_year + 1][1]) ?>,   <?php echo $capacity_count_month[2][1]; ?>],
            ['<?php echo $month_names[2] ?>',  <?php echo ceil($total_month_value[$current_year + 1][2]) ?>,   <?php echo $capacity_count_month[2][2]; ?>],
            ['<?php echo $month_names[3] ?>',  <?php echo ceil($total_month_value[$current_year + 1][3]) ?>,   <?php echo $capacity_count_month[2][3]; ?>],
            ['<?php echo $month_names[4] ?>',  <?php echo ceil($total_month_value[$current_year + 1][4]) ?>,   <?php echo $capacity_count_month[2][4]; ?>],
            ['<?php echo $month_names[5] ?>',  <?php echo ceil($total_month_value[$current_year + 1][5]) ?>,   <?php  echo $capacity_count_month[2][5]; ?>],
            ['<?php echo $month_names[6] ?>',  <?php echo ceil($total_month_value[$current_year + 1][6]) ?>,   <?php  echo $capacity_count_month[2][6]; ?>],
            ['<?php echo $month_names[7] ?>',  <?php echo ceil($total_month_value[$current_year + 1][7]) ?>,   <?php  echo $capacity_count_month[2][7]; ?>],
            ['<?php echo $month_names[8] ?>',  <?php echo ceil($total_month_value[$current_year + 1][8]) ?>,   <?php  echo $capacity_count_month[2][8]; ?>],
            ['<?php echo $month_names[9] ?>',  <?php echo ceil($total_month_value[$current_year + 1][9]) ?>,   <?php  echo $capacity_count_month[2][9]; ?>],
            ['<?php echo $month_names[10] ?>',  <?php echo ceil($total_month_value[$current_year + 1][10]) ?>, <?php  echo $capacity_count_month[2][10]; ?>],
            ['<?php echo $month_names[11] ?>',  <?php echo ceil($total_month_value[$current_year + 1][11]) ?>, <?php  echo $capacity_count_month[2][11]; ?>],
            ['<?php echo $month_names[12] ?>',  <?php echo ceil($total_month_value[$current_year + 1][12]) ?>, <?php  echo $capacity_count_month[2][12]; ?>],
        ]);
        new google.visualization.ColumnChart(document.getElementById('visualization_next_year')).
            draw(data_next_year,
        {title:"Order Booking Details <?php echo $current_year + 1 ?>",
            width:375, height:200,
            hAxis: {title: "Year: <?php echo $current_year + 1 ?>"}}
    );
    }
    google.setOnLoadCallback(drawVisualization);
</script> 
</div>
