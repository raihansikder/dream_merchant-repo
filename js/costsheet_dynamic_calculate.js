$("input").blur(function(){
  	//$("input").css("background-color","#D6D6FF");  
	/************************************************************
	*	costsheet_sewing_thread_quantity_costperdozen
	*************************************************************/
	
	var costsheet_quoted_price_perdozen;
  	costsheet_quoted_price_perdozen = $('input[name=costsheet_quoted_price_perdozen]').val();	
	//alert(costsheet_quoted_price_perdozen);
	
	$('input[name=costsheet_quoted_price_perpiece]').attr('value',(costsheet_quoted_price_perdozen/12).toFixed(2));
	//Math.round(number).toFixed(2);

	
	/************************************************************/
}); 