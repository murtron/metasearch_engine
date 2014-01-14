$(document).ready(function() {
 	$("#search_form").validate({
		onfocusout: false,
		rules:{
				query: "required"
			},
		messages: {
				query: "Please enter a search term!"
			}
	});
});