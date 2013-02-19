<?php
$q="SELECT *
FROM purchaseorder
WHERE
po_uid='".$a['po_uid']."' AND po_active='1'
ORDER BY po_prepared_date DESC";
//echo $q;
$rc=mysql_query($q)or die(mysql_error());
if(mysql_num_rows($rc)){
	$a_cs=mysql_fetch_rowsarr($rc);
}else{
	$norecordfound= "No records found";
}
?>
<div class="clear"></div>
<div class="po_versions">
	<h2>Purchase Order Versions</h2>
	<div class="version_list">
		<?php echo $norecordfound;?>
		<?php
		if(mysql_num_rows($rc)){
			echo "<table id='datatable' width='100%'>
			<thead>
			<tr>
			<td>Date</td>
			<td>Version</td>
			<td>[id] Title</td>
			<td>prepared by</td>
			<td>Action</td>
			</tr>
			</thead>
			<tbody>";
			$total_versions= mysql_num_rows($rc);
			foreach($a_cs as $cs){
				echo "<tr>
				<td>".$cs['po_prepared_date']."</td>
				<td>".$total_versions--."</td>
				<td>[".$cs['po_id']."] ".$cs['po_title']."</td>
				<td>".getUserNameFrmId($cs['po_prepared_by'])."</td>
				<td><a href='purchaseorder_add.php?param=view&po_id=".$cs['po_id']."&client_id=".$cs['po_client_id']."'>view</a></td>
				</tr>";
			}
			echo "</tbody>
			</table>";
		}
		?>
	</div>
</div>
