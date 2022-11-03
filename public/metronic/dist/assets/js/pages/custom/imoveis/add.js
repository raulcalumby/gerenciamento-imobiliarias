var form = $("#add.form");
form.submit(function (event) {
  event.preventDefault(); //prevent default action
  var post_url = $(this).attr("action"); //get form action url
  var request_method = $(this).attr("method"); //get form GET/POST method
  var form_data = $(this).serialize(); //Encode form elements for submission

  if (!validation()) {
    Swal.fire({
      icon: "warning",
      title: "Opa!",
      text: "A parcela atual está maior que a quantidade de parcelas",
      confirmButtonText: "Entendi !",
    });
    return;
  }

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
      if (response["status"] == "success") {
        $("#imoveis_id").val(response["imoveis_id"]);

        html += response["status-message"];
        html +=
          '<a href="../../../../../imoveis" class="btn btn-primary btn-lg">Listar Imóveis</a>';
        confirmButtonText =
          '<i class="fas fa-cart-plus"></i> Adicionar outro Imóvel';
        if (meuDropzone.getQueuedFiles().length > 0) {
          meuDropzone.processQueue();
        } else {
          KTApp.unblockPage();
          Swal.fire({
            icon: response["status"],
            title: "Opa!",
            text: response["status-message"],
            html: html,
            confirmButtonText: confirmButtonText,
          }).then((value) => {});
        }
        form[0].reset();
      } else {
        KTApp.unblockPage();
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
