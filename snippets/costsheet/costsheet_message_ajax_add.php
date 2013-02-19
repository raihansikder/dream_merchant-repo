<?php
include_once('../../config.php');
$cm_costsheet_uid=$_REQUEST[costsheet_uid];
$cm_message_text=$_REQUEST[message_text];
$cm_posted_by=$_SESSION[current_user_id];
$q="INSERT INTO costsheet_message
		(
			cm_costsheet_uid,
			cm_message_text,
			cm_posted_datetime,
			cm_posted_by
		)
		values
		(
			'$cm_costsheet_uid',
			'$cm_message_text',
			now(),
			'$cm_posted_by'
		)" ;
mysql_query($q)or die(mysql_error());
$cm_id=mysql_insert_id();
$q="SELECT * FROM costsheet_message	WHERE cm_id='$cm_id'" ;
$rm=mysql_query($q)or die(mysql_error());
if(mysql_num_rows($rm)){
	/*
	* 	Get the costsheet_id
	*/
	$q="SELECT * FROM costsheet	WHERE costsheet_uid='$costsheet_uid' order by costsheet_prepared_date desc limit 0,1" ;
	$rc=mysql_query($q)or die(mysql_error());
	$cs=$msg=mysql_fetch_assoc($rc);
	/************************/
	$msg=mysql_fetch_assoc($rm);
	echo "<div class='message_block' id='".$msg[cm_id]."'>";
	echo "<div class='cm_posted_by'><b>".getUserNameFrmId($msg[cm_posted_by])."</b><br/>".$msg[cm_posted_datetime]." </div>";
	echo "<div class='message_text'>".$msg[cm_message_text]."</div>";
	echo "</div>";
}else{
	echo "No records found";
}

