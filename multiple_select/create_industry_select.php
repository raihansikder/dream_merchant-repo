<script type="text/javascript">
    $(function(){
        //$.localise('ui-multiselect', {/*language: 'en',*/ path: 'js/locale/'});
        $(".multiselect").multiselect();
        //$('#switcher').themeswitcher();
    });
</script>
<?php
include('../common.php');
$var_name = $_REQUEST[var_name];
$result=mysql_query("select * from institute order by institute_name")or die(mysql_error());
$list_array=mysql_fetch_rowsarr($result);
?>
<select class="multiselect" multiple="multiple" name="<?php echo $var_name;?>[]">
	<?php foreach($list_array as $sa){?>
	<option value="<?php echo $sa[institute_id]; ?>">
		<?php echo $sa[institute_name];?>
	</option>
	<?php }?>
</select>
