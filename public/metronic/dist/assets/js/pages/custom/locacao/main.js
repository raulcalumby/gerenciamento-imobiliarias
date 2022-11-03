let imovelArr = []

// SELECT TWO IMOVEIS
$("#imoveis").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url:
      "../../../../../api/imoveis/available",
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
}).on('select2:select', function (e) {
  var data = e.params.data;
  $('#locacao_valor').val(parseFloat(data['locacao']).toLocaleString('pt-BR'))

  imovelArr = data;
});

// SELECT TWO Proprietarios
$("#locatarios").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url:
      "../../../../../api/locatarios/simplelist?v=" + new Date().getTime(),
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

// SELECT TWO Índices
$("#indices").select2({
  width: "100%",
  placeholder: "Clique para buscar e selecionar",
  allowClear: true,
  ajax: {
    url:
      "../../../../../api/indices/simplelist?v=" + new Date().getTime(),
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



function deleta(locacao_id, list = false) {
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
        url: "../../../../../api/locacao/disable",
        dataType: "json",
        data: {
          locacao_id: parseInt(locacao_id),
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
              location.href = "../../../../../locacao";
            }
          });
        },
        error: function (request, status, error) { },
      });
    }
  });
}

function calculaDataFim() {
  if ($('#data_inicio').val() == '' || $('#prazo').val() == '') {
    return
  }

  var prazo = $('#prazo').val()
  var data_inicio = $('#data_inicio').val()


  var data = new Date(data_inicio);
  data.setMonth(data.getMonth() + parseInt(prazo))


  month = '' + (data.getMonth() + 1),
    day = '' + data.getDate(),
    year = data.getFullYear();

  if (month.length < 2)
    month = '0' + month;
  if (day.length < 2)
    day = '0' + day;
  data = [year, month, day].join('-')
  $('#data_fim').val(data)

}

function editarFianca() {
  Swal.fire({
    icon: "warning",
    title: "Opa!",
    showCancelButton: true,
    text: "Ao atualizar , as parcelas anteriores serão excluidas. Tem certeza que gostaria de editar o seguro fiança?",
    confirmButtonText: 'Sim',
    cancelButtonText: "Cancelar",
  }).then((value) => {
    if (value["isConfirmed"]) {
      $("#qtd_parcelas_fianca").prop("readonly", false);
      $("#seg_fianca_valor").prop("readonly", false);
      $('#parcelaAtual').prop('readonly', false);
      $('#tipo_fianca').removeClass('block_select')
    }
  });
}

function calculaDiferencaMeses() {
  var data_inicio = $('#data_inicio').val()
  var data_fim = $('#data_fim').val()
  var monthDiff = getMonthDifference(new Date(data_inicio), new Date(data_fim));
  $('#prazo').val(monthDiff)

}

function getMonthDifference(startDate, endDate) {
  return (
    endDate.getMonth() - startDate.getMonth() + 12 * (endDate.getFullYear() - startDate.getFullYear())
  );
}

function firstMonth(iptu, condominio, seguroIncendio, valorLocacao, valorParcela, valorBloqueto, parcIptu, parcFianca, tipoFianca) {
  let firstMonthHtml = '';
  let firstParcFianca = parseInt(parcFianca);
  let firstParcIPTU = parseInt(parcIptu);

  firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Aluguel:</small></p></div><div class="col-6"><p class="h6  text-success">${valorLocacao.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`

  if (parseFloat(iptu) > 0) {
    firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>IPTU Parc ${firstParcIPTU}:</small></p></div><div class="col-6"><p class="h6  text-success">Calcular</p></div>`
  }

  if (condominio > 0) {
    firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Condominio:</small></p></div><div class="col-6"><p class="h6  text-success">${condominio.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }

  if (parseFloat(seguroIncendio) > 0) {
    firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Seguro Incendio:</small></p></div><div class="col-6"><p class="h6  text-success">${parseFloat(seguroIncendio).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }

  if (tipoFianca === 'imobiliaria') {
    if (valorParcela > 0) {
      firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Seguro Fiança Parc: ${firstParcFianca}: </small></p></div><div class="col-6"><p class="h6  text-success">${valorParcela.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
    }

  }

  if (parseFloat(valorBloqueto) > 0) {
    firstMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Taxa de Bloqueto:</small></p></div><div class="col-6"><p class="h6  text-success">${parseFloat(valorBloqueto).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }

  return firstMonthHtml;
}

function secondMonth(iptu, condominio, seguroIncendio, valorLocacao, valorParcela, bloqueto, parcIptu, parcFianca, tipoFianca) {


  let secondMonthHtml = '';
  let secondParcFianca = parseInt(parcFianca) + 1;
  let secondParcIPTU = parseInt(parcIptu) + 1;

  secondMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Aluguel:</small></p></div><div class="col-6"><p class="h6  text-success">${toEnglishDecimal(valorLocacao).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`

  if (parseFloat(iptu) > 0) {
    secondMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>IPTU Parc: ${secondParcIPTU}: </small></p></div><div class="col-6"><p class="h6  text-success">${parseFloat(iptu).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }

  if (parseFloat(condominio) > 0) {
    secondMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Condominio</small></p></div><div class="col-6"><p class="h6  text-success">${parseFloat(condominio).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }

  if (tipoFianca == 'imobiliaria') {
    if (parseFloat(valorParcela) > 0) {
      secondMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Seguro Fiança Parc: ${secondParcFianca}:</small></p></div><div class="col-6"><p class="h6  text-success">${parseFloat(valorParcela).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
    }
  }

  if (bloqueto > 0) {
    secondMonthHtml += `<div class="col-6"><p class="h6  text-monospace bold"><small>Taxa de Bloqueto:</small></p></div><div class="col-6"><p class="h6  text-success">${bloqueto.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p></div>`
  }
  
  return secondMonthHtml;
}




function toEnglishDecimal(val) {
  var valor = val.replace(".", "");
  valor = valor.replace(",", ".");
  valor = parseFloat(valor);
  return valor
}
/* Calc o valor do condominio e aluguel, baseado em qual dia ele pegou a Chave */
function calculatePrice(dateKey, dateStart, valorLocacao, condominio) {
  var response = Array();
  response['condominio'] = parseFloat(condominio)
  response['valorLocacao'] = parseFloat(valorLocacao)

  /*Calculando diferença entre duas datas */

  const diffInMs = new Date(dateStart) - new Date(dateKey)
  let dateObjStart = new Date(dateStart);
  let dateObjKey = new Date(dateKey);
  let diffInDays = diffInMs / (1000 * 60 * 60 * 24);

  if (dateObjStart.getMonth() !== dateObjKey.getMonth()) {
    let diffRemove = calculateDaysRemove(dateObjStart.getMonth(), dateObjStart.getFullYear())
    diffInDays = diffInDays + diffRemove;
  }

  var date = new Date(dateStart)
  var day = date.getUTCDate();
  var swtichDate = switchDates(day);

  /*Se Existir faço o calculo */
  if (diffInDays !== 0) {
    response['valorLocacao'] = (toEnglishDecimal(valorLocacao) / 30) * diffInDays;
    response['condominio'] = (parseFloat(condominio) / 30) * (swtichDate + diffInDays);
  }


  return response

}


function switchDates(day) {
  var returnDay = 30;

  switch (day) {
    case 1:
      returnDay = 30
      break;
    case 5:
      returnDay = 25
      break;
    case 10:
      returnDay = 20
      break;

    case 15:
      returnDay = 15
      break;

    case 25:
      returnDay = 10
      break;

  }

  return returnDay;

}

function calculateDaysRemove(month, year) {
  let day = 0;
  let i = 0;
  let daysMonth = getDiasMes(month, year).slice(-1)[0];

  if (daysMonth >= 30) {
    day = daysMonth
    i = 0;
    while (day > 30) {
      i--
      day--;
    }

  } else {
    day = daysMonth
    i = 0;
    while (day < 30) {
      i++
      day++;
    }

  }

  return i;
}

function getDiasMes(month, year) {
  month--;

  var date = new Date(year, month, 1);
  var days = [];
  while (date.getMonth() === month) {
    days.push(date.getDate());
    date.setDate(date.getDate() + 1);
  }
  return days;
}
