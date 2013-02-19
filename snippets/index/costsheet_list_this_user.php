<div class="clear"></div>
<div class="moduleBlock">
<h2>Your costsheets</h2>
<?php
$client_id=$_REQUEST[client_id];
$r=mysql_query("
		select * from costsheet s1
		where
		costsheet_prepared_by ='".$_SESSION[current_user_id]."' and
		costsheet_prepared_date=
		(select max(s2.costsheet_prepared_date) from costsheet s2
		where s1.costsheet_uid=s2.costsheet_uid);")or die(mysql_error());
$rows=mysql_num_rows($r);
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);

	?>
<table id="datatable" width="100%">
	<thead>
		<tr class="firstrow">
			<td>Date</td>
			<td>[id] Title</td>
			<td>Client name</td>
			<td>Prepared by</td>
			<td>status</td>
			<td>action</td>
		</tr>
	</thead>
	<tbody>
		<?php for($i=0;$i<$rows;$i++){?>
		<tr>
			<td>
				<?php echo $arr[$i][costsheet_prepared_date];?>
			</td>
			<td>
				[
				<?php echo $arr[$i][costsheet_id]?>
				]
				<?php echo $arr[$i][costsheet_title]?>
			</td>
			<td>
				<?php echo getClientCompanyNameFrmId($arr[$i][costsheet_client_id])?>
			</td>
			<td>
				<?php echo getUserNameFrmId($arr[$i][costsheet_prepared_by])?>
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
				if($arr[$i][costsheet_emailed_to_client]=='1'){
					echo "<span class='small'> - Emailed</span>";
				}
				?>
			</td>
			<td>
				<a href="costsheet_add.php?costsheet_id=<?php echo $arr[$i][costsheet_id]?>&param=view&client_id=<?php echo $arr[$i][costsheet_client_id]; ?>">view</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php }else{
	echo "You don't have any recent costsheet";
}?>
<div class="clear"></div>
</div>