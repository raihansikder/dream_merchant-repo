<?php include_once('../../config.php');$st_id=$_REQUEST[st_id];$r=mysql_query("select st_port_of_landing from shipping_term where st_id='$st_id'")or die(mysql_error());$a=mysql_fetch_assoc($r);echo  $a[st_port_of_landing];?>