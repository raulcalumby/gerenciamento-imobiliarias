var form = $("#addModal.form");
form.submit(function (event) {
  event.preventDefault(); //prevent default action
  var post_url = $(this).attr("action"); //get form action url
  var request_method = $(this).attr("method"); //get form GET/POST method
  var form_data = $(this).serialize(); //Encode form elements for submission
  $.ajax({
    url: post_url,
    type: request_method,
    data: form_data,
    dataType: "json",
    beforeSend: function (xhr) {
      //Desativa o Form
      KTApp.blockPage({
        opacity: 0.2,
        overlayColor: "#3699FF",
        state: "danger",
        message: "Enviando...",
      });
    },
    success: function (response) {
      var html = "";
      var confirmButtonText = "Ok";
      KTApp.unblockPage();

      if (response["status"] == "success") {

        $("#modalPropietario").modal('hide');

        $("#addLivroCaixa").modal('hide');
        searchQueryDt()
        $('#addModal')[0].reset();

        
      } else {
       
        Swal.fire({
          icon: response["status"],
          title: "Opa!",
          text: response["status-message"],
          html: html,
          confirmButtonText: confirmButtonText,
        }).then((value) => {});
      }
      
    },
  });
});
