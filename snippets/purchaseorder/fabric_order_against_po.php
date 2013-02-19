<?php
$q="SELECT *
FROM fabric_order
WHERE
fo_po_uid='".$a['po_uid']."' AND fo_active='1'
ORDER BY fo_prepared_datetime DESC
LIMIT 0,1 ";
//echo $q;
$r=mysql_query($q)or die(mysql_error()."<br/>Query: $q<br/>");
if(mysql_num_rows($r)){
	$a_fo=mysql_fetch_assoc($r);
}
?>