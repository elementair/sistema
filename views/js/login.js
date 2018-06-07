jQuery(document).on('submit','#forming',function(event)){
	event.proventDefault(){
		jQuery.ajax({
			url: '../js/login.js',
			type: 'POST',
			dataType: 'json',
			data: ,
			beforeSend:function(){

			}
		})
		.done(function(respuesta){
			console.log("success");
		})
		.fail(function(resp){
			console.log("error");
		})
		.always(function(){
			console.log("complete");
		});
	}
}