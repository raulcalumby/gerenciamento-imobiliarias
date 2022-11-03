function updatePassUser(account_id){ 
    var currentpasswd = $('#currentPassword').val();
	var passwd = $('#newPassword').val();
	var cpasswd = $('#cnewPassword').val();
	$.ajax({
			type:'post',
            url:'../../../../../api/users/edit/pass',
            dataType: "json",
			data:{
				account_id:account_id,
				currentpasswd:currentpasswd,
				passwd:passwd,
				cpasswd:cpasswd
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
				result = response;
				Swal.fire("Opa!", result['status-message'], result['status']).then((value) => {
                    if(result['status'] == 'success'){
                        $('#currentPassword').val('');
                        $('#newPassword').val('');
                        $('#cnewPassword').val('');
                    }
			  	});

			}
	});
}