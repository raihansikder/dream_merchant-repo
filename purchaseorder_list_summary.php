<?php
include_once('config.php');
include_once("class.pagination.php");

$r = mysql_query("
		select * from purchaseorder s1 where
		po_prepared_date=
		(select max(s2.po_prepared_date) from purchaseorder s2
		where s1.po_uid=s2.po_uid);") or die(mysql_error());
$stat = array();
$i = 0;
$rows = mysql_num_rows($r);
//echo "test".getMonthlyTotalQuantitySpecificTypeFrmPoId("12", "1", "2012", "10")."<br><br>";
if ($rows > 0) {
    $arr = mysql_fetch_rowsarr($r);
    foreach ($arr as $temp) {
        $stat[$i]["po_id"] = $temp["po_id"];
        $stat[$i]["po_client_id"] = $temp["po_client_id"];
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
$stat_row_count = $i - 1;
//echo "<br />$stat_row_count<br />";

$value_total_basic = array();
$value_total_semi_critic = array();
$value_total_cretical = array();
$value_total_extra_critical = array();
$total_month_value = array();
$current_year = date("Y");
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
$compare_year = (int) $current_year;
$year_array = array();
$year_array[0] = $compare_year - 1;
$year_array[1] = $compare_year;
$year_array[2] = $compare_year + 1;
for ($i = 0; $i < $rows; $i++) {
    $po_shipment_date = explode('-', $stat[$i]["po_shipment_date"]);
    $num = (int) $po_shipment_date[1];
    $set = 0;
    for ($j = 1; $j <= 12; $j++) {
        if ($num == $j && $set == 0) {
            if ($stat[$i]["po_category_id"] == "1") {
                $value_total_basic[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"];
                $set = 1;
            } else if ($stat[$i]["po_category_id"] == "2") {
                $value_total_semi_critic[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"];
                $set = 1;
            } else if ($stat[$i]["po_category_id"] == "3") {
                $value_total_cretical[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"];
                $set = 1;
            } else {
                $value_total_extra_critical[$po_shipment_date[0]][$j] += $stat[$i]["po_total_quantity"];
                $set = 1;
            }
        }
    }
}
/*
 * calculating the month value
 */
for ($i = 1; $i <= 12; $i++) {
    $total_month_value[$current_year][$i] = ($value_total_semi_critic[$current_year][$i] + $value_total_cretical[$current_year][$i] + $value_total_extra_critical[$current_year][$i]) + $value_total_basic[$current_year][$i];
    $total_month_value[$current_year + 1][$i] = ($value_total_semi_critic[$current_year + 1][$i] + $value_total_cretical[$current_year + 1][$i] + $value_total_extra_critical[$current_year + 1][$i]) + $value_total_basic[$current_year + 1][$i];
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <?php include('inc.head.php'); ?>
    </head>
    <body>
        <div id="wrapper">
            <div id="container">
                <div id="top1">
                    <?php include('top.php'); ?>
                </div>
                <div id="mid">
                <br/>
                    <h1>Current Booking Status Of Company </h1>
                    <div class="alert">
                        <?php printAlert($valid, $alert); ?>
                    </div>
                    <?php
                    $company_count = 0;
                    for ($i = 0; $i < $stat_row_count; $i++) {
                        if ($stat[$i]["po_client_id"] > $company_count) {
                            $company_count = $stat[$i]["po_client_id"];
                        }
                    }
                    $company_count+=1;

                 //  echo "<br> row count: $stat_row_count<br> company count: $company_count<br>";
                    ?>
                    <!--
                    *
                    *   report table for current year
                    *
                    *-->
                    <br /><br />
                    <h2>Booking Status Of year <?php echo $year_array[1]; ?></h2><br />
                    <table width="100%" border="1" cellpadding="0" cellspacing="0">
                    <thead>
                        <tr style="font-size:12px; font-weight:bold; background-color:#09F; color:#FFF; line-height:30px;">
                            
                            <th>S/L</th>
                            <th>Client</th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("1") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("2") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("3") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("4") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("5") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("6") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("7") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("8") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("9") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("10") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("11") . ", " . $year_array[1]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("12") . ", " . $year_array[1]; ?></th>
                        </tr>
                        <tr>
                            <td style="background-color:#999"></td>
                            <td style="background-color:#999"></td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                        </tr>
                        <?php
                        for ($j = 4; $j < $company_count; $j++) {
                            echo "<tr>";
                            echo "<td>" . ($j-3) . "</td>";
                            echo "<td style=\"font-size:12px; font-weight:bold;\">" . getClientCompanyNameFrmId($j) . "</td>";
                            for ($i = 1; $i < 13; $i++) {

                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "1", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "2", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "3", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "4", "$year_array[1]", "$i") . "</td>";
                                echo "<td></td>";
                                }

                            echo "</tr>";
                        }
                            echo "<tr>";
                            echo "<tr style=\"font-size:14px; font-weight:bold;\">";
                            echo "<td colspan=\"2\">TOTAL QTY-</td>";  
                            for ($i = 1; $i < 13; $i++) {
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("1", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("2", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("3", "$year_array[1]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("4", "$year_array[1]", "$i") . "</td>";
                                echo "<td></td>";
                            }
                            echo "</tr>";
                        
                        ?>
                    </table>

                    <!--
                    *
                    *   report table for current year+1
                    *
                    *-->

                    <p>&nbsp;</p><p>&nbsp;</p><p></p><p></p>
                  <h2>Booking Status Of year <?php echo $year_array[2]; ?></h2><br />
                    <table width="100%" border="1" cellpadding="0" cellspacing="0">
                        <tr style="font-size:12px; font-weight:bold; background-color:#09F; color:#FFF; line-height:30px;">
                            <th>S/L</th>
                            <th>Client</th>                            
                            <th colspan="5"><?php echo getmonthNameFromNumber("1") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("2") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("3") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("4") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("5") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("6") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("7") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("8") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("9") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("10") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("11") . ", " . $year_array[2]; ?></th>
                            <th colspan="5"><?php echo getmonthNameFromNumber("12") . ", " . $year_array[2]; ?></th>
                        </tr>
                        <tr>
                            <td style="background-color:#333"></td>
                            <td style="background-color:#333"></td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                            <td>Basic</td><td>Semi Critical</td><td>Critical</td><td>Extra Critical</td><td>Total</td>
                        </tr>
                        <?php
                        for ($j = 4; $j < $company_count; $j++) {
                            echo "<tr>";
                            echo "<td>" . ($j-3) . "</td>";
                            echo "<td style=\"font-size:14px; font-weight:bold;\">" . getClientCompanyNameFrmId($j) . "</td>";
                            for ($i = 1; $i < 13; $i++) {
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "1", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "2", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "3", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificTypeByClient("$j", "4", "$year_array[2]", "$i") . "</td>";
                                echo "<td></td>";
                            }
                            echo "</tr>";
                        }
                        echo "<tr style=\"font-size:14px; font-weight:bold;\">";
                            echo "<td colspan=\"2\">TOTAL QTY-</td>";                            
                            for ($i = 1; $i < 13; $i++) {
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("1", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("2", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("3", "$year_array[2]", "$i") . "</td>";
                                echo "<td>" . getMonthlyTotalQuantitySpecificType("4", "$year_array[2]", "$i") . "</td>";
                                echo "<td></td>";
                            }
                            echo "</tr>";
                        ?>
                    </table>
                    <!-- Selecting the purchase order shows the following button-->
                    <input id="pi_generate_button" class="button bgblue" type="submit" name="submit" value="Generate Proforma invoice" style="display: none;" />
                    <input name="client_id" type="hidden" value="<?php echo $client_id; ?>" />
                    <!--
The following div holds the list of hidden input that is created by checking the checkboxes to send costsheet through e-mail.
                    -->
                    <div class="hidden_input_field"></div>
                    <!---->
                    </form>
                </div>
                <div id="footer">
                    <?php include('footer.php'); ?>
                </div>
            </div>
        </div>
    </body>
</html>
