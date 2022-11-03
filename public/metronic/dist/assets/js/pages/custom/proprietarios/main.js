function deleta(proprietarios_id, list = false) {
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
        url: "../../../../../api/proprietarios/disable",
        dataType: "json",
        data: {
          proprietarios_id: parseInt(proprietarios_id),
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
              location.href = "../../../../../proprietarios";
            }
          });
        },
        error: function (request, status, error) {},
      });
    }
  });
}

// SELECT TWO IMOVEIS
$("#imoveis")
  .select2({
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
  })
  .on("select2:select", function (e) {
    let data = e.params.data;
    let select2 = e.target;
    $(select2).val(null).trigger("change");
    selecionaImovel(data, select2);
  });


// Seleciona o Imovel
function selecionaImovel(data) {
  const imovelId = data["imoveis_id"];
  const proprietarioId = $("#proprietario_id").val();

  $.ajax({
    async: false,
    type: "post",
    url: "../../../../../api/imoveis/proprietario",
    dataType: "json",
    data: {
      imoveis_id: imovelId,
      proprietario_id: proprietarioId,
    },
    success: function (response) {
      if(response['status'] == 'success')
      {
        datatable.ajax.reload();
      }
    },
  });
}


$(".cpf").inputmask({
  mask: ['999.999.999-99', '99.999.999/9999-99'],
  keepStatic: true
});
$(".cel").inputmask({
  mask: ['(99) 99999-9999'],
  keepStatic: true
});

$(".tel").inputmask({
  mask: ['(99) 9999-9999'],
  keepStatic: true
});


$(".percentage").inputmask("decimal", {
  radixPoint: ".",
  groupSeparator: ",",
  autoGroup: true,
  suffix: " %",
  clearMaskOnLostFocus: false
});