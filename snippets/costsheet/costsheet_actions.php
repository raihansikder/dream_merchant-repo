<?php
/*
 *	First check whether a new version exists. If yes then all action should be taken in that version. obsolete version will not have any optoin to take action like save, edit approve etc.
*/
if(!newerCostsheetExists($costsheet_id)){
	/*
	 *	Conditions for [Save button]
	*	Following code shows the [Request approval] link/button in costsheet
	*/
	$submit_button_text='Save draft';
	if($param=='add'||$param=='edit'){
		/*
		 *	Conditions for [Save button]
		*	Following code shows the [Save button] link/button in costsheet
		*/
		if(hasPermission('costsheet','edit',$_SESSION[current_user_id])){
			if(!costsheetFreezed($a['costsheet_id'])){
				/*
				 *	Print submit/save button
				*/
				echo "<input class='button bgblue' type='submit' name='submit' value='".$submit_button_text."' />";
			}else{
				/* TODO */
				if(hasPermission('costsheet','super_admin',$_SESSION[current_user_id])){
					echo "<input class='button bgblue' type='submit' name='submit' value='".$submit_button_text."' />";
				}
			}
		}
	}
	/************************************************************************/
	/*
	 *	Conditions for [Back]
	*/
	echo "<a href='costsheet_list.php?client_id=$client_id' class='button bgblue'>back</a>";
	/************************************************************************/
	echo "<div class='clear'></div>"; //
	if(strlen($costsheet_id)){ // this returns true if a costsheet already exists in database. the code below will not execute while adding a costsheet for the first time. Thus not showing options like approve, unapprove, send by e-mail etc costsheet specific options.
		/*
		 *	Conditions for [Request approval]
		*	Following code shows the [Request approval] link/button in costsheet
		*/
		if(hasPermission('costsheet','request_approval',$_SESSION[current_user_id])){
			if(!costsheetApproved($a['costsheet_id'])){
				if(costsheetApprovalRequested($a['costsheet_id'])){
					echo "<span class='red'>Approval already requested for this cost sheet. You cannot make any update to the sheet unless you are admin.</span><br/>";
				}
				else{
					echo "<a href='".$_SERVER['PHP_SELF']."?param=request_approval&costsheet_id=".$costsheet_id."&client_id=".$client_id."'> Request approval</a><br/>";
				}
			}
			/*********************************/
		}
		/*
		 *	Conditions for [approval]
		*	Following code shows the [Approve] link/button in costsheet
		*/
		if(hasPermission('costsheet','approve',$_SESSION[current_user_id])){  // fetches and checkes value from user_type>user_type_level
			if(!costsheetApproved($a['costsheet_id'])){
				echo "<a href='".$_SERVER['PHP_SELF']."?param=approve&costsheet_id=".$costsheet_id."&client_id=".$client_id."'> Approve</a><br/>";
			}
			/*********************************/
		}
		/*
		 *	Conditions for [Disapproved]
		*	Following code shows the [Disapproved] link/button in costsheet
		*/

		if(hasPermission('costsheet','disapprove',$_SESSION[current_user_id])){  // fetches and checkes value from user_type>user_type_level
			if(costsheetApproved($a['costsheet_id'])||costsheetApprovalRequested($a['costsheet_id'])){
				echo "<a href='".$_SERVER['PHP_SELF']."?param=disapprove&costsheet_id=".$costsheet_id."&client_id=".$client_id."'> Disapprove</a><br/>";
			}
			/*********************************/
		}
		/*
		 *	Conditions for [Send Quotation by mail]
		*	Following code shows the [Send Quotation by mail] link/button in costsheet
		*/
		if(hasPermission('costsheet','send_by_email',$_SESSION[current_user_id])){ // fetches and checkes value from user_type>user_type_level
			if(costsheetApproved($a['costsheet_id'])){
				echo "<a href='costsheet_email.php?client_id=".$client_id."&costsheet_id[]=". $costsheet_id."'>Send quotation by e-mail</a> ";
			}
			/*********************************/
		}
	}
}
?>