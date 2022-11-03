var form = document.getElementById("add");
$("#add").submit(function(event){
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
            var html = "";
            var confirmButtonText = "Ok";
            if(response['status'] == 'success'){
                html +=  response['status-message'];
                html +=  '<a href="../../../../../locatarios" class="btn btn-primary btn-lg">Listar Locatarios/Inquilinos </a>';
                confirmButtonText = '<i class="fas fa-user-friends"></i> Adicionar outro Locatario/Inquilino';
                form.reset();
            }
            Swal.fire({
                icon: response['status'],
                title: "Opa!",
                text: response['status-message'],
                html: html,
                confirmButtonText:confirmButtonText
              }).then((value) => {})
        }
    });
});   

