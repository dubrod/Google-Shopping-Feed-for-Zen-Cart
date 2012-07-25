$(document).ready(function(){

	$("#dataForm").submit(function(){
	
		var str = $(this).serialize();
		
		   $.ajax({
		   type: "POST",
		   url: "feed-processor.php",
		   data: str,
		   success: function(msg){
			
				$("#response").ajaxComplete(function(event, request, settings){
		
					if(msg == 'OK') // Message Sent? Show the 'Thank You' message and hide the form
						{
							result = 'Word Up';
							$("#fields").hide();
						}
					else
						{
							result = msg;
						}
				
					$(this).html(result);
				
					});
		
				}
		
		 });
	
		return false;
	
	});

});