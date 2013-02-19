<?php
$client_id_array=getClientIdsFromUserId($_SESSION[current_user_id]);
if(count($client_id_array)>0){
	$q="select * from client where client_id in(".implode(',',getClientIdsFromUserId($_SESSION[current_user_id])).")";
	//echo $q;
	$r=mysql_query($q)or die(mysql_error()."your_client.php 1");
	$rows=mysql_num_rows($r);
}
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);

	?>

<div class="">
<h2>Your Clients</h2>
<table id="datatable_min" style="width: 100%;">
	<thead>
		<tr class="firstrow">
			<td width="122">Client Name / Contacts</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i=0;$i<$rows;$i++){?>
		<tr>
			<td>
				<div style="float: left;">
					<a href="client_view.php?client_id=<?php echo $arr[$i][client_id];?>">
						<?php echo $arr[$i][client_company_name];?>
					</a>
					<!-- - <span class="small"><?php echo $arr[$i][client_contact_name];?></span> -->
				</div>
				<a class="none" href="purchaseorder_list.php?client_id=<?php echo $arr[$i][client_id];?>" style="float: right;">
					<img src="images/purchaseorder_small_icon.png" title="Purchase Order" alt="Purchase Order" height="20px" width="20px" />
				</a>
				<a class="none" href="costsheet_list.php?client_id=<?php echo $arr[$i][client_id];?>" style="float: right;">
					<img src="images/costsheet_small_icon.png" title="Cost sheet" alt="Cost sheet" height="20px" width="20px" />
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php }else{
	echo "You have no associated client";
}?>
</div>
<div class="clear"></div>
<div class="quickLinks">
<h2>Quick Links</h2>
<a href="purchaseorder_list_all.php" class="button bgblue " style="float:left; width:auto; "> View all purchase order </a>
<a href="purchaseorder_list_summary.php" class="button bgblue "style="float: left; width: auto;"> Booking Status </a>
</div>
