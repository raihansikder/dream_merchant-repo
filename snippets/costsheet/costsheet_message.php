<?php
$q="SELECT *
FROM costsheet_message
WHERE
cm_costsheet_uid='".$a['costsheet_uid']."' AND cm_active='1'
ORDER BY cm_posted_datetime DESC";
//echo $q;
$rm=mysql_query($q)or die(mysql_error());
if(mysql_num_rows($rm)){
	$a_msg=mysql_fetch_rowsarr($rm);
}else{
	$norecordfound= "No records found";
}
?>
<div class="clear"></div>
<div class="messages">
	<h2>Messages</h2>
	<div class="message_form">
		<textarea name="costsheet_msg" class="costsheet_msg"></textarea>
		<div class="clear"></div>
		<input type="button" name="costsheet_msg_post" class="button bgblue" value="Post message" />
		<img id="ajax-loader-message-post" src="images/ajax-loader-1.gif" style="display: none;" />
	</div>
	<div class="message_list">
		<?php echo $norecordfound;?>
		<?php
		if(mysql_num_rows($rm)){
			foreach($a_msg as $msg){
				echo "<div class='message_block' id='".$msg[cm_id]."'>";
				echo "<div class='cm_posted_by'><b>".getUserNameFrmId($msg[cm_posted_by])."</b><br/>".$msg[cm_posted_datetime]." </div>";
				echo "<div class='message_text'>".$msg[cm_message_text]."</div>";
				echo "</div>";
			}
		}
		?>
	</div>
</div>
