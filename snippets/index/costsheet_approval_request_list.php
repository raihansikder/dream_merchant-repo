
<?php
$client_id=$_REQUEST[client_id];
$r=mysql_query("
		select * from costsheet s1
		where
		costsheet_approval_state ='Requested_approval' and
		costsheet_approver_user_id ='".$_SESSION[current_user_id]."' and
		costsheet_prepared_date=
		(select max(s2.costsheet_prepared_date) from costsheet s2
		where s1.costsheet_uid=s2.costsheet_uid);")or die(mysql_error());
$rows=mysql_num_rows($r);
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);

	?>
<div class="clear"></div>
<h2>Costsheet Approval request list</h2>
<table id="datatable">
	<thead>
		<tr class="firstrow graybg">
			<td>Date</td>
			<td>StyleNo</td>
			<td>Fabrication</td>
			<td>GSM</td>
			<td>Fabric Consumption</td>
			<td>Fabric cost</td>
			<td>Trim cost</td>
			<td>CM</td>
			<td>Bank</td>
			<td>Garments Price</td>
			<td>Quoted Price</td>
			<td>status</td>
			<td width="100">action</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i=0;$i<$rows;$i++){?>
		<tr>
			<td>
				<?php echo $arr[$i][costsheet_prepared_date];?>
			</td>
			<td>
				<?php echo $arr[$i][costsheet_title]?>
				[
				<?php echo $arr[$i][costsheet_id];?>
				]
			</td>
			<td>
				<?php echo $arr[$i][costsheet_fabrication];?>
			</td>
			<td>
				<?php echo $arr[$i][costsheet_gsm];?>
			</td>
			<td>
				<?php echo FabricConsumption($arr[$i][costsheet_id]); ?>
			</td>
			<td>
				<?php echo totalFabricCost($arr[$i][costsheet_id]); ?>
			</td>
			<td>
				<?php echo totalTrimCost($arr[$i][costsheet_id]);?>
			</td>
			<td>
				<?php echo $arr[$i][costsheet_cm_price];?>
			</td>
			<td>
				<?php echo $arr[$i][costsheet_bankothers];?>
			</td>
			<td>
				<?php echo GarmentsPrice($arr[$i][costsheet_id]);?>
			</td>
			<td>
				<?php echo $arr[$i][costsheet_quoted_price_perpiece];?>
			</td>
			<td>
				<?php
				if(costsheetApproved($arr[$i][costsheet_id])){
					echo "<span class='greenText' >Approved</span>";
				}else if(costsheetApprovalRequested($arr[$i][costsheet_id])){
					echo "<span class='orangeText' >Requested</span>";
				}
				else{
					echo "<span class='redText'>Unapproved</span>";
				}
				?>
			</td>
			<td>
				<a class="none" target="_blank" href="costsheet_add.php?costsheet_id=<?php echo $arr[$i][costsheet_id]?>&param=view&client_id=<?php echo $arr[$i][costsheet_client_id]; ?>">
					<img src="images/view-more.png" title="Details">
				</a>
				<a class="none" target="_blank" href="costsheet_add.php?costsheet_id=<?php echo $arr[$i][costsheet_id]?>&param=approve&client_id=<?php echo $arr[$i][costsheet_client_id]; ?>">
					<img src="images/approve.png" title="Approve">
				</a>
				<a class="none" target="_blank" href="costsheet_add.php?costsheet_id=<?php echo $arr[$i][costsheet_id]?>&param=disapprove&client_id=<?php echo $arr[$i][costsheet_client_id]; ?>">
					<img src="images/disapprove.png" title="Disapprove">
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<div class="clear"></div>
<?php }else{
	//echo "No approval request";
} ?>
