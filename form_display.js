$(document).ready(function() {
	
	$("#aggregate, #nonagg").change(function(){
		if($("#nonagg").is(":checked"))
		 {
			$("#cluster + label").css("display","none");
			$("#cluster").attr("checked",false)
		 }
		else $("#cluster + label").css("display","inline-block");
	 });
});