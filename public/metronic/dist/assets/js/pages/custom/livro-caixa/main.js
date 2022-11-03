function deleta(livro_caixa_id, list = false) {
  Swal.fire({
    icon: "error",
    title: "Tem certeza? <br> Esta ação será irreversivel!",
    // html: html,
    showCancelButton: true,
    showConfirmButton: true,
    showCloseButton: true,
    focusConfirm: false,
    allowEscapeKey: true,
    allowOutsideClick: true,
  }).then((value) => {
    if (value["isConfirmed"] == true) {
      $.ajax({
        type: "post",
        url: "../../../../../api/livro-caixa/disable",
        dataType: "json",
        data: {
          livro_caixa_id: parseInt(livro_caixa_id),
        },
        beforeSend: function () {
          KTApp.blockPage({
            opacity: 0.2,
            overlayColor: "#3699FF",
            state: "danger",
            message: "Enviando...",
          });
        },
        success: function (response) {
          KTApp.unblockPage();
          Swal.fire({
            icon: response["status"],
            title: response["status-message"],
            //html: html,
            showCancelButton: false,
            showConfirmButton: true,
            showCloseButton: true,
            focusConfirm: true,
            allowEscapeKey: true,
            allowOutsideClick: true,
          }).then((value) => {
            if (list) {
              searchQueryDt();
            } else {
              location.href = "../../../../../livro-caixa";
            }
          });
        },
        error: function (request, status, error) {},
      });
    }
  });
}

// SELECT TWO IMOVEIS
$(".imoveis").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url: "../../../../../api/imoveis/simplelistall",
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

$("#proprietarios").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url: "../../../../../api/proprietarios/simplelist",
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
function openModal() {
  $("#comissionado").attr("checked", false);
  $("#imoveis").val(null).trigger("change");
  $("#add").attr("action", "../../../../../api/livro-caixa/add");
  $("textarea#descricao").val("");
  $("#addLivroCaixa").modal("show");
  
  $("#data").val("");

  $("#status").val("");

  $("#valor").val("");
}

function livroCaixa(proprietariosHidden, dateHidden) {
  if (document.getElementById("proprietarioId")) {
    document.getElementById("proprietarioId").remove();
  }
  if (document.getElementById("dataHidden")) {
    document.getElementById("dataHidden").remove();
  }

  const proprietarios = `<input type='hidden' id='proprietarioId' name='proprietarioId' value='${proprietariosHidden}'>`;
  const date = `<input type='hidden' id='dataHidden' name='data' value='${dateHidden}'>`;

  $("#add").append(proprietarios + date);
}
