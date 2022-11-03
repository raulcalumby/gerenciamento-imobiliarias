function deleta(transportadoras_id, list = false) {
  Swal.fire({
      icon: 'error',
      title: "Tem certeza? <br> Esta ação será irreversivel!",
     // html: html,
      showCancelButton: true,
      showConfirmButton: true,
      showCloseButton: true,
      focusConfirm: false,
      allowEscapeKey: true,
      allowOutsideClick: true
    }).then((value) => {
         if(value['isConfirmed'] == true){
              $.ajax({
                  type:'post',
                  url:'../../../../../api/transportadoras/disable',
                  dataType:'json',
                  data:{
                    transportadoras_id:parseInt(transportadoras_id),
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
                      Swal.fire({
                          icon: response['status'],
                          title: response['status-message'],
                          //html: html,
                          showCancelButton: false,
                          showConfirmButton: true,
                          showCloseButton: true,
                          focusConfirm: true,
                          allowEscapeKey: true,
                          allowOutsideClick: true
                        }).then((value) => {
                          if(list){
                            searchQueryDt();
                          }else{
                            location.href = '../../../../../transportadoras';
                          }
                        })
                  }
              });
        }
  });
}
