<?php
include('config.php');
include_once("class.pagination.php");
$client_id=$_REQUEST[client_id];
$r=mysql_query("
		select * from costsheet s1
		where costsheet_client_id='$client_id' and
		costsheet_prepared_date=
		(select max(s2.costsheet_prepared_date) from costsheet s2
		where s1.costsheet_uid=s2.costsheet_uid);")or die(mysql_error());
$rows=mysql_num_rows($r);
if($rows>0){
	$arr=mysql_fetch_rowsarr($r);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php"); ?>
<script>
//countChecked();
$('document').ready(function(){
	$(":checkbox").click(function(){
		if(countChecked()){
			$('input[id=email_button]').show();
		}else{
			$('input[id=email_button]').hide();
		}
	});
});
</script>
</head>
<body>
	<div id="wrapper">
		<div id="container">
			<div id="top1">
				<?php include('top.php');?>
			</div>
			<div id="mid">
				<h2>
					<?php echo getClientCompanyNameFrmId($client_id); ?>
				</h2>
				<?php include('snippets/client/clientmenu.php');?>
				<div class='add_button_large'>
					<a href="costsheet_add.php?param=add&client_id=<?php echo $client_id;?>"> <img src="images/plus.png" /> Add new cost sheet
					</a>
				</div>
				<div class="alert">
					<?php printAlert($valid,$alert);?>
				</div>
				<form method="post" action="costsheet_email.php">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" id="datatable">
						<thead>
							<tr class="firstrow">
								<td width="199">Date</td>
								<td width="230">Costsheet title</td>
								<td width="96">Prepared by</td>
								<td width="57">Approval</td>
								<td width="36">action</td>
							</tr>
						</thead>
						<tbody>
							<?php for($i=0;$i<$rows;$i++){?>
							<tr>
								<td>
									<?php echo $arr[$i][costsheet_prepared_date];?>
								</td>
								<td>
									<span class="costsheetcheckbox"> <?php if(costsheetApproved($arr[$i][costsheet_id])&& hasPermission('costsheet','send_by_email',$_SESSION[current_user_id])){?> <input name="costsheet_id_selector[]" class="selector_<?php echo $arr[$i][costsheet_id]?>" type="checkbox" value="<?php echo $arr[$i][costsheet_id]?>" /> <script>
                $('input[class=<?php echo "selector_".$arr[$i][costsheet_id]; ?>]').click(function(){
                    var costsheet_id=$(this).val();
                    //alert(costsheet_id);
                    var exists=$("input[class='temp_"+costsheet_id+"']").length;
                    //alert(exists);

                    if (exists!=0){
                    //alert('exists')
                    $("input[class=temp_"+costsheet_id+"]").remove();
                    }else{
                    //alert('doesnt exists')
                    $('div[class=hidden_input_field]').append("<input name='costsheet_id[]' type='hidden' class='temp_"+costsheet_id+"' value='"+costsheet_id+"'/>");
                    }
                })
                </script> <?php } ?>
									</span> <span class='cs_idname'> <?php echo "[".$arr[$i][costsheet_id]."] ".$arr[$i][costsheet_title];?>
									</span>
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
									<a href="costsheet_add.php?costsheet_id=<?php echo $arr[$i][costsheet_id]?>&param=view&client_id=<?php echo $client_id; ?>">View</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<input type="hidden" name="client_id" value="<?php echo $client_id;?>" />
					<!-- Selecting the approved costsheet shows the following button-->
					<input id="email_button" class="button bgblue" type="submit" name="SendEmail" value="E-mail selected costsheet" style="display: none;" />
					<!--
        	The following div holds the list of hidden input that is created by checking the checkboxes to send costsheet through e-mail.
        -->
					<div class="hidden_input_field"></div>
					<!---->
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
