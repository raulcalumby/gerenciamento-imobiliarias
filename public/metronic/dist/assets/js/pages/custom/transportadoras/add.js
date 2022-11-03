var form = $("#add.form");
form.submit(function(event){
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

            //Swal.fire("Opa!", response['status-message'], response['status']);
            var html = "";
            var confirmButtonText = "Ok";
            if(response['status'] == 'success'){
                html +=  '<a href="../../../../../transportadoras" class="btn btn-primary btn-lg">Listar Transportadoras</a>';
                confirmButtonText = '<i class="la la-car-side"></i> Adicionar outra Transportadora';
                form[0].reset();
            }
            Swal.fire({
                icon: response['status'],
                title: "Opa!",
                text: response['status-message'],
                html: html,
                confirmButtonText:confirmButtonText
              }).then((value) => {
              })
        }
    });
});