$("#edit.form").submit(function(event){
	event.preventDefault(); //prevent default action 
	var post_url = $(this).attr("action"); //get form action url
	var request_method = $(this).attr("method"); //get form GET/POST method
    var form_data = $(this).serializeArray(); //Encode form elements for submission
    var data = {};

    $(form_data).each(function(index, obj){
        data[obj.name] = obj.value;
    });
   
    if(!validation())
    {
        Swal.fire({
            icon: "warning",
            title: "Opa!",
        
            text: "A parcela atual está maior que a quantidade de parcelas",
            confirmButtonText: 'Entendi !',
            
          })
      return;
      
    }
    
	$.ajax({
		url : post_url,
		type: request_method,
        data : data,
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
         var html = "";
         var confirmButtonText = "Ok";
         if(response['status'] == 'success'){
             html +=  response['status-message'];
             if( meuDropzone.getQueuedFiles().length > 0){
                 $('#produtos_id').val(response['produtos_id']); 
                 meuDropzone.processQueue();
             }else{
                KTApp.unblockPage();
                Swal.fire({
                    icon: response['status'],
                    title: "Opa!",
                    text: response['status-message'],
                    html: html,
                    confirmButtonText:confirmButtonText
                  }).then((value) => {
                })
             }
         }else{
             KTApp.unblockPage();
             Swal.fire({
                 icon: response['status'],
                 title: "Opa!",
                 text: response['status-message'],
                 html: html,
                 confirmButtonText:confirmButtonText
               }).then((value) => {
             })
         }
         $('#produtos_id').val('');
        }
    }).done(function(response){ //
    
	});
});