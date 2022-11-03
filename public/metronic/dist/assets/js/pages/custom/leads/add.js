var form = $("#add.form");

form.submit(function (event) {
  event.preventDefault(); 
  var post_url = $(this).attr("action"); 
  var request_method = $(this).attr("method"); 
  var form_data = $(this).serialize(); 
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
        $("#modalLeads").modal('hide');
        searchQueryDt();
        $('#add')[0].reset();
    
      } else {
        Swal.fire({
          icon: response["status"],
          title: "Opa!",
          text: response["status-message"],
          html: html,
          confirmButtonText: confirmButtonText,
        }).then((value) => { });
      }

    },
  });

});
