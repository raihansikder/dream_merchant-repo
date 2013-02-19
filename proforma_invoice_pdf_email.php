<?php
include('config.php');
$valid=true;
$alert=array();
$client_id=$_REQUEST[client_id];
$pi_id=$_REQUEST[pi_id];
$file=$_REQUEST[file];

$r=mysql_query("Select * from proforma_invoice where pi_id='$pi_id'")or die(mysql_error());
$rows=mysql_num_rows($r);
if($rows>0){
	$a=mysql_fetch_assoc($r);
}

if(isset($_POST[submit])){
	if(empty($_POST[email_to])){
		$valid=false;
		array_push($alert,"Please give a valid TO: address");
	}
	if($valid){

		$to_array=explode(",", trim($_REQUEST[email_to],', '));
		foreach($to_array as $to){
			//echo $to;
			$mail->AddAddress(trim($to),trim($to));
		}

		$cc_array=explode(",", trim($_REQUEST[email_cc],', '));
		foreach($cc_array as $cc){
			//echo $to;
			$mail->AddCC(trim($cc),trim($cc));
		}

		$mail->WordWrap = 50; // set word wrap
		//$mail->AddAttachment("D:/a.txt"); // attachment
		//echo getcwd()."/temp/$file";
		$mail->AddAttachment(getcwd()."/temp/$file"); // attachment
		$mail->IsHTML(true); // send as HTML
		$mail->Subject = trim($_REQUEST[email_subject]);
		$mail->Body = $_REQUEST[email_body]."<br/><br/>"; //HTML Body
		$mail->AltBody = $_REQUEST[email_body]."<br/><br/>".$_REQUEST[email_body_note];; //Text Body
		if(!$mail->Send()){
			echo "Mailer Error: " . $mail->ErrorInfo;
		}else{array_push($alert,"E-mail has been sent");
		}
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php include_once("inc.head.php");?>
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
					- Send Proforma Invoice by e-mail
				</h2>
				<div id="client_menu">
					<?php include('snippets/client/clientmenu.php');	?>
				</div>
				<div class="alert">
					<?php if(isset($_POST[submit])){
						printAlert($valid,$alert);
} ?>
				</div>
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
					<input name="file" value="<?php echo $file;?>" type="hidden">
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<strong>Client Information</strong>
								</td>
								<td width="480">&nbsp;</td>
							</tr>
							<tr>
								<td width="106">
									<strong>To</strong> (comma separated):
								</td>
								<td>
									<textarea style="text-transform: lowercase" name="email_to" cols="30"><?php if(!strlen(trim($_REQUEST[email_to]))){
										echo getClientEmailFrmId($a[pi_client_id]);
									} else echo trim($_REQUEST[email_to]);?>
									</textarea>
								</td>
							</tr>
							<tr>
								<td>
									<strong>Cc:</strong> (comma separated):
								</td>
								<td>
									<textarea style="text-transform: lowercase" name="email_cc" cols="30"><?php
									if(strlen(trim($_POST[email_cc]))){
										echo $_POST[email_cc];
									}else{
										echo $_SESSION[current_user_email];
									}?>
									</textarea>
								</td>
							</tr>
							<tr>
								<td>Subject</td>
								<td>
									<input name="email_subject" type="text" value="<?php echo $_REQUEST[email_subject];?>" size="30" maxlength="60" class='validate[required]' />
								</td>
							</tr>
							<tr>
								<td>
									<strong>E-mail body</strong>
								</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<?php
									$email_body=
									"Dear ".getClientContactNameFrmId($a[pi_client_id])."<br /> ".
									"Please find the attached cost sheet.<br /><br />";

									//echo $email_body;
									?>
									<textarea class="ckeditor" cols="80" id="editor1" name="email_body" rows="10"><?php echo $email_body; ?>
									</textarea>
									<script type="text/javascript">
		//<![CDATA[

			// Replace the <textarea id="editor"> with an CKEditor
			// instance, using default configurations.
			CKEDITOR.replace( 'email_body',
				{
					toolbar :
							[
								{ name: 'basicstyles', items : [ 'Bold','Italic' ] },
								{ name: 'paragraph', items : [ 'NumberedList','BulletedList' ] },
								{ name: 'colors', items : [ 'TextColor','BGColor' ] },
								{ name: 'styles', items : [ 'Styles','Format' ] }

								/*
								{ name: 'document', items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
								{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
								{ name: 'editing', items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
								{ name: 'forms', items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton',
									'HiddenField' ] },
								'/',
								{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
								{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv',
								'-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
								{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
								{ name: 'insert', items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe' ] },
								'/',
								{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
								{ name: 'colors', items : [ 'TextColor','BGColor' ] },
								{ name: 'tools', items : [ 'Maximize', 'ShowBlocks','-','About' ] }
								*/

							],
					extraPlugins : 'autogrow',
					autoGrow_maxHeight : 800,
					// Remove the Resize plugin as it does not make sense to use it in conjunction with the AutoGrow plugin.
					removePlugins : 'resize'

				} );

		//]]>
		</script>
							
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
									<input class="button bgblue" type="submit" name="submit" value="Send Email" />
								</td>
							</tr>
						</table>
				
				</form>
			</div>
		</div>
		<div id="footer">
			<?php include('footer.php');?>
		</div>
	</div>
</body>
</html>
