function deleta(imoveis_id, list = false) {
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
        url: "../../../../../api/imoveis/disable",
        dataType: "json",
        data: {
          imoveis_id: parseInt(imoveis_id),
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

// SELECT TWO RESPONSAVEL
$("#proprietarios").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url:
      "../../../../../api/proprietarios/simplelist?v=" + new Date().getTime(),
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
  $("#imoveis").val(null).trigger("change");
  $("#add").attr("action", "../../../../../api/livro-caixa/add");
  $("textarea#descricao").val("");
  $("#addLivroCaixa").modal("show");
  $("#data").val("");
  $("#status").val("");
  $("#valor").val("");
}

function excluirFundo(fundo_id) {
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
        url: "../../../../../api/livro-caixa/imoveis/disable",
        dataType: "json",
        data: {
          fundo_id: parseInt(fundo_id),
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
          searchQueryDt();
          $("#detalhesObra").modal("hide");
          KTApp.unblockPage();
        },
        error: function (request, status, error) {},
      });
    }
  });
}

function modalPropietario() {
  $("#modalPropietario").modal("show");
}

$(".cpf").inputmask({
  mask: ["999.999.999-99", "99.999.999/9999-99"],
  keepStatic: true,
});
$(".telefone").inputmask({
  mask: ["(99) 99999-9999"],
  keepStatic: true,
});

function atualizarIptu() {
  Swal.fire({
    icon: "warning",
    title: "Opa!",
    showCancelButton: true,
    text: "Tem certeza que deseja atualizar o IPTU ?",
    confirmButtonText: 'Sim',
    cancelButtonText: "Cancelar",
  }).then((value) => {
    if (value["isConfirmed"]) {
      $("#iptu").prop("readonly", false); 
      $("#parcela_atual").prop("readonly", false); 
      $("#qtd_parcelas_iptu").prop("readonly", false); 
      
     
      $('#opcoes_iptu').removeClass('block_select')
      document.getElementById('taxa_iptu').removeAttribute("onclick"); 
    }
  });
}

function validation()
{
  let error = true

  const parcelaAtual = $('#parcela_atual').val();
  const parcelas = $('#qtd_parcelas_iptu').val();
  
  if( parseInt(parcelaAtual) > parseInt(parcelas))
  {

    
    error = false;
  }


  return error;
}


function desbloquearCondominio()
{
  Swal.fire({
    icon: "warning",
    title: "Deseja atualizar o valor do condominio ?",
    showCancelButton: true,
    text: "Se você editar após o dia 10 ira somar a diferença para o mês que vem!",
    confirmButtonText: 'Sim',
    cancelButtonText: "Cancelar",
  }).then((value) => {
    if (value["isConfirmed"]) {
      $("#condominio_resp").prop("readonly", false);
      $("#condominio").prop("readonly", false);
      document.getElementById('condominio_resp').removeAttribute("onclick"); 
      document.getElementById('divCondResp').removeAttribute("onclick"); 
      document.getElementById('condominio').removeAttribute("onclick"); 
    }
  });
  
}