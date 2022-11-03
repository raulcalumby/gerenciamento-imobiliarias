
function adicionarUsuario(){ 
    var name = $('#nome').val();
    var username = $('#email').val();
    var level = $('#funcao').val();
    var passwd = $('#password').val();
    var cpasswd = $('#cpassword').val();
	
	//var cep = $('#cep').val().replace(/\D/g, '');
	var result = false;
	$.ajax({
			type:'post',
			url:'../../../../../api/users/add',
			dataType: "json",
			data:{
                name:name,
				username:username,
                passwd: passwd,
                cpasswd: cpasswd,
				level:level,
			},
			beforeSend: function(){
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
				//meuDropzone.processQueue();

				//result = JSON.parse(response.trim());
				result = response;
				if( result['status'] == 'success'){
                    var html = '';
			        html += '<b>Email</b>:' +result['result']['username'] + '<br>';
			        html += '<b>Senha</b>:' +result['result']['passwd'] + '<br>';

                    Swal.fire({
                        icon: 'success',
                        title: result['text-status'],
                        html: html,
                        showCancelButton: false,
                        showConfirmButton: true,
                        showCloseButton: true,
                        focusConfirm: false,
                        allowEscapeKey: true,
                        allowOutsideClick: false
                      }).then((value) => {
        
                         
/*                         $('#nome').val('');
                        $('#email').val('');
                        $('#funcao').val('');
                        $('#password').val('');
                        $('#cpassword').val(''); */ 
        
                    });
		
				}else{
					Swal.fire("Opa!", result['status-message'], result['status']);
				}
			}
	});
	return true;
}

