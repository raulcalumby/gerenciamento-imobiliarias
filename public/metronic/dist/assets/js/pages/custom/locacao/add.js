
let checkClick = 0;
var form = $("#add.form");

/* Add Form */
function add() {
  var post_url = $(form).attr("action");
  var request_method = $(form).attr("method");
  var form_data = $(form).serializeArray();
  form_data.push({ name: "valorAdicionado", value: $("#valorAdicionado").val()});
  form_data.push({ name: "textoAdicional", value: $("#textoAdicional").val() });

  if (checkClick == 1) {
    return
  }

  checkClick = 1

  $.ajax({
    url: post_url,
    type: request_method,
    data: form_data,
    dataType: "json",
    beforeSend: function (xhr) {
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

        html += response["status-message"];
        html += '<a href="../../../../../locacao" class="btn btn-primary btn-lg">Listar Locações</a>';
        confirmButtonText = '<i class="fas fa-cart-plus"></i> Adicionar outra Locação';

        if (meuDropzone.getQueuedFiles().length > 0) {
          $("#locacao_id").val(response["locacao_id"]);
          meuDropzone.processQueue();
        } else {
          KTApp.unblockPage();
          Swal.fire({
            icon: response["status"],
            title: "Opa!",
            text: response["status-message"],
            html: html,
            confirmButtonText: confirmButtonText,
          }).then((value) => {
            if (value.isConfirmed || value.isDismissed) {
              window.location.href = '../../../../../locacao';
            }
          });
        }

      } else {
        KTApp.unblockPage();
        Swal.fire({
          icon: response["status"],
          title: "Opa!",
          text: response["status-message"],
          html: html,
          confirmButtonText: confirmButtonText,
        }).then((value) => {

          if (value.isConfirmed || value.isDismissed) {
            checkClick = 0;
          }

        });
      }
      $("#produtos_id").val("");
    },
  });
}

function calculadoraModal() {
  /* Pego os dados do imovel selecionado */
  let valorBloqueto = 0;
  if ($('#bloqueto').is(":checked")) {
    valorBloqueto = 3.95;
  }

  let iptu = imovelArr['iptu'];
  let condominio = imovelArr['condominio'];
  let seguroIncendio = imovelArr['seg_incendio_valor_total'];
  let valorLocacao = $('#locacao_valor').val();
  let valorParcela = $('#seg_fianca_valor').val();
  let dateKey = $('#dateKey').val();
  let dateStart = $('#data_inicio').val();
  let parcIptu = imovelArr['parcela_atual_iptu'];
  let parcFianca = $('#parcAtual').val()
  let tipoFianca = $('#tipoFianca').val()

  if (valorParcela.length <= 0) {
    valorParcela = 0;
  } else {
    valorParcela = toEnglishDecimal(valorParcela)
  }

  if (valorLocacao === '') {
    valorLocacao = 0;
  }



  let secondMonthHtml = secondMonth(iptu, condominio, seguroIncendio, valorLocacao, valorParcela, valorBloqueto, parcIptu , parcFianca,tipoFianca);
  /* Calculo o valor do aluguel do primeiro Mês */
  let letArrayValores = calculatePrice(dateKey, dateStart, valorLocacao, condominio);
  condominio = letArrayValores['condominio']
  valorLocacao = letArrayValores['valorLocacao']
  let firstMonthHtml = firstMonth(iptu, condominio, seguroIncendio, valorLocacao, valorParcela , valorBloqueto, parcIptu , parcFianca, tipoFianca);

  /*valor total */
  let valorTotal = condominio + parseFloat(seguroIncendio) + valorLocacao + valorParcela;

  /*Esvazio todos campos  */
  $("#valorTotalExtrato").empty();
  $("#firstMonth").empty();
  $("#secondMonth").empty();

  /* Insiro na modal e chamo ela  */
  $("#valorTotalExtrato").append(valorTotal.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
  $('#firstMonth').append(firstMonthHtml)
  $('#secondMonth').append(secondMonthHtml)
  $('#resumo').modal('show')
}


function toEnglishDecimal(val) {
  var valor = val.replace(".", "");
  valor = valor.replace(",", ".");
  valor = parseFloat(valor);
  return valor
}