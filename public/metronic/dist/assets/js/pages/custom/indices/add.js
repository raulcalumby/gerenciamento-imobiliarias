var form = document.getElementById("add");
$("#add").submit(function (event) {
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
      KTApp.unblockPage();
      var html = "";
      var confirmButtonText = "Ok";
      if (response["status"] == "success") {
        html += response["status-message"];
        html +=
          '<a href="../../../../../indices" class="btn btn-primary btn-lg">Listar Índices</a>';
        confirmButtonText =
          '<i class="fas fa-user-friends"></i> Adicionar outro Índice';
        form.reset();
      }
      Swal.fire({
        icon: response["status"],
        title: "Opa!",
        text: response["status-message"],
        html: html,
        confirmButtonText: confirmButtonText,
      }).then((value) => {});
    },
  });
});

// SELECT TWO IMOVEIS
$("#imoveis_add").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url: "../../../../../api/imoveis/simplelist?v=" + new Date().getTime(),
    dataType: "json",
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        //page: params.page
      };
    },
    processResults: function (data, params) {
      params.page = params.page || 1;

      return {
        results: data.data,
      };
    },

    cache: true,
  },
});
