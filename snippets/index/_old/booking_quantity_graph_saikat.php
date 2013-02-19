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
        $r = mysql_query($q) or die("<b>Query:</b> $q <br/>");
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

myprint_r($stat);

/*
 * prepare the valus for the the graph
 * 31-07-2012
 * nasir
 * **************************************************************************** */
// the month wise po

$standard_value_basic = 1.75;
$standard_value_semi_critic = 2.50;
$standard_value_cretical = 4.5;
$standard_value_extra_critical = 6;

$value_total = array();
$value_total_basic = array();
$value_total[][] = 0;
$value_total_basic[][] = 0;

$total_month_value = array();

$current_year = date("Y");
//echo $current_year;
// check and count month wise po data
for ($i = 0; $i < $row_count; $i++) {
    $po_shipment_date = explode('-', $stat[$i]["po_shipment_date"]);

    if ($po_shipment_date[1] == "01") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][1] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][1] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][1] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][1] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "02") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][2] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][2] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][2] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][2] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "03") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][3] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][3] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][3] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][3] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "04") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][4] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][4] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][4] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][4] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "05") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][5] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][5] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][5] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][5] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } elseif ($po_shipment_date[1] == "06") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][6] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][6] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][6] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][6] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "07") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][7] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][7] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][7] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][7] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "08") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][8] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][8] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][8] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][8] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "09") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][9] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][9] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][9] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][9] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "10") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][10] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][10] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][10] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][10] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "11") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][11] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][11] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][11] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][11] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    } else if ($po_shipment_date[1] == "12") {
        if ($stat[$i]["po_category_id"] == "1") {
            $value_total_basic[$po_shipment_date[0]][12] += $stat[$i]["po_total_quantity"];
        } else if ($stat[$i]["po_category_id"] == "2") {
            $value_total[$po_shipment_date[0]][12] += $stat[$i]["po_total_quantity"] * $standard_value_semi_critic;
        } else if ($stat[$i]["po_category_id"] == "3") {
            $value_total[$po_shipment_date[0]][12] += $stat[$i]["po_total_quantity"] * $standard_value_cretical;
        } else {
            $value_total[$po_shipment_date[0]][12] += $stat[$i]["po_total_quantity"] * $standard_value_extra_critical;
        }
    }
}
/*
 * graph value calculation
 * ***************************************************************************** */

/*
 * assign 0 value for | $value_total_basic
 */
//values for current year 
for ($i = 1; $i <= 12; $i++) {
    if (!($value_total_basic[$current_year][$i] > 0)) {
        $value_total_basic[$current_year][$i] = 0;
    }
}
//values for next year
for ($i = 1; $i <= 12; $i++) {
    if (!($value_total_basic[$current_year + 1][$i] > 0)) {
        $value_total_basic[$current_year + 1][$i] = 0;
    }
}
/*
 * assign 0 value for | $value_total
 */
//values for current year 
for ($i = 1; $i <= 12; $i++) {
    if (!($value_total[$current_year][$i] > 0)) {
        $value_total[$current_year][$i] = 0;
    }
}
//values for next year
for ($i = 1; $i <= 12; $i++) {
    if (!($value_total[$current_year + 1][$i] > 0)) {
        $value_total[$current_year + 1][$i] = 0;
    }
}
/*
 * test print 
 */
echo "<br/> Before calculation <br/>";
for ($i = 1; $i <= 12; $i++) {
    echo "<br/> month subtotal:" . $i . " : " . $value_total[$current_year][$i];
    echo "<br/> month:" . $i . " : " . $total_month_value[$current_year][$i];
}

/*
 * calculating the month values
 */
//values for current year
for ($i = 1; $i <= 12; $i++) {
    if ($value_total[$current_year][$i] == 0) {
        $total_month_value[$current_year][$i] = $value_total_basic[$current_year][$i];
        echo "<br/> month:" . $i . " : " . $total_month_value[$current_year][$i];
    } else {
        $total_month_value[$current_year][$i] = $value_total[$current_year][$i] / $value_total_basic[$current_year][$i] + $standard_value_basic;
        echo "<br/> month:" . $i . " : " . $total_month_value[$current_year][$i];
    }
}
//values for next year
for ($i = 1; $i <= 12; $i++) {
    if ($value_total[$current_year + 1][$i] == 0) {
        $total_month_value[$current_year + 1][$i] = $value_total_basic[$current_year + 1][$i];
    } else {
        $total_month_value[$current_year + 1][$i] = $value_total[$current_year + 1][$i] / $value_total_basic[$current_year + 1][$i] + $standard_value_basic;
    }
}



//    month name arrey
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
?>
<div id="visualization" style="float:left;"></div>
<div id="visualization_next_year" style="float:left;"></div>
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
            ['Month',       '<?php echo $month_names[1] ?>', '<?php echo $month_names[2] ?>', '<?php echo $month_names[3] ?>', '<?php echo $month_names[4] ?>', '<?php echo $month_names[5] ?>', '<?php echo $month_names[6] ?>', '<?php echo $month_names[7] ?>', '<?php echo $month_names[8] ?>', '<?php echo $month_names[9] ?>', '<?php echo $month_names[10] ?>', '<?php echo $month_names[11] ?>', '<?php echo $month_names[12] ?>'],
            ['Oredered quantity',<?php echo $total_month_value[$current_year][1]; ?>,<?php echo $total_month_value[$current_year][2]; ?>,<?php echo $total_month_value[$current_year][3]; ?>,<?php echo $total_month_value[$current_year][4]; ?>,<?php echo $total_month_value[$current_year][5]; ?>,<?php echo $total_month_value[$current_year][6]; ?>,<?php echo $total_month_value[$current_year][7]; ?>,<?php echo $total_month_value[$current_year][8]; ?>,<?php echo $total_month_value[$current_year][9]; ?>,<?php echo $total_month_value[$current_year][10]; ?>,<?php echo $total_month_value[$current_year][11]; ?>,    <?php echo $total_month_value[$current_year][12]; ?>]
                
        ]);
          
        // Create and draw the visualization.
        new google.visualization.ColumnChart(document.getElementById('visualization')).
            draw(data,
        {title:"Project Progress report for the Year <?php echo $current_year ?>",
            width:610, height:400,
            hAxis: {title: "Year: <?php echo $current_year ?>"}}
    );
        var data_next_year = google.visualization.arrayToDataTable([
            ['Month',       '<?php echo $month_names[1] ?>', '<?php echo $month_names[2] ?>', '<?php echo $month_names[3] ?>', '<?php echo $month_names[4] ?>', '<?php echo $month_names[5] ?>', '<?php echo $month_names[6] ?>', '<?php echo $month_names[7] ?>', '<?php echo $month_names[8] ?>', '<?php echo $month_names[9] ?>', '<?php echo $month_names[10] ?>', '<?php echo $month_names[11] ?>', '<?php echo $month_names[12] ?>'],
            ['Oredered quantity',<?php echo $total_month_value[$current_year + 1][1]; ?>,<?php echo $total_month_value[$current_year + 1][2]; ?>,<?php echo $total_month_value[$current_year + 1][3]; ?>,<?php echo $total_month_value[$current_year + 1][4]; ?>,<?php echo $total_month_value[$current_year + 1][5]; ?>,<?php echo $total_month_value[$current_year + 1][6]; ?>,<?php echo $total_month_value[$current_year + 1][7]; ?>,<?php echo $total_month_value[$current_year + 1][8]; ?>,<?php echo $total_month_value[$current_year + 1][9]; ?>,<?php echo $total_month_value[$current_year + 1][10]; ?>,<?php echo $total_month_value[$current_year + 1][11]; ?>,    <?php echo $total_month_value[$current_year + 1][12]; ?>]
                
        ]);
        new google.visualization.ColumnChart(document.getElementById('visualization_next_year')).
            draw(data_next_year,
        {title:"Project Progress report for the Year <?php echo $current_year + 1 ?>",
            width:610, height:400,
            hAxis: {title: "Year: <?php echo $current_year + 1 ?>"}}
    );
    }
      

    google.setOnLoadCallback(drawVisualization);
</script>
