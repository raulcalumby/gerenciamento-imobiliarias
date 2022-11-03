$("#edit.form").submit(function(event){
	event.preventDefault(); //prevent default action 
	var post_url = $(this).attr("action"); //get form action url
	var request_method = $(this).attr("method"); //get form GET/POST method
    var form_data = $(this).serialize(); //Encode form elements for submission
	$.ajax({
		url : post_url,
		type: request_method,
    data : form_data,
        dataType: "json",
        beforeSend: function( xhr ) {
            //Desativa o Form
            KTApp.blockPage({
                opacity: 0.2,
                overlayColor: '#3699FF',
                state: 'danger',
                message: 'Enviando...'
              });
        },
        success:function(response) {
            KTApp.unblockPage();
            console.log(response);

            Swal.fire("Opa!", response['status-message'], response['status']);
        }
    }).done(function(response){ //
        //Debug
        //console.log(response);
 
	});
});