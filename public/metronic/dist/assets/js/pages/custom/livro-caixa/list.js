var table = $('#kt_datatable');
var checkDatatablesCreated = false;

function searchQueryDt() {

  if (document.getElementById('proprietarios').value == '' || document.getElementById('search_date').value == '' ) {
    table.DataTable().clear();
    table.DataTable().destroy();
    $('#addLivroDiv').addClass('d-none');
    checkDatatablesCreated = false
    return;
  }



  if (!checkDatatablesCreated) {

    table.DataTable({
      language: {
        url: "../../../../../metronic/dist/assets/js/datatable/pt-br.json",
      },
      sDom: "lrtip",

      responsive: true,
      paging: true,
      searching: true,
      ordering: false,
      lengthChange: false,
      pageLength: 15,
      processing: true,
      serverSide: true,
      ajax: {
        url: "../../../../../api/livro-caixa/list?v=" + new Date().getTime(),
        type: "POST",
      },
      columns: [
        { data: "data" }, // 0 
        {
          data: "descricao",
          render: function (data, type, row, meta) {
            if (data.length > 60) {
              return type === 'display' ?
                `${data.substring(0, 60)} ...` :
                data;
            } else {
              return type === 'display' ?
                `${data}` :
                ''
            }

          }
        },
        {
          data: "status",
          render: function (data, type, row, meta) {
            if (data == "0" || data == "2") {
              return type === 'display' ?
                `<span class="label label-lg label-light-danger label-inline font-weight-bolder d-none">${parseFloat(0).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</span>` :
                data;
            } else if (data == "1") {
              return type === 'display' ?
                `<span class="label label-lg label-light-danger label-inline font-weight-bolder">${parseFloat(row.valor).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</span>` :
                ''
            }
          }
        },
        {
          data: "status",
          render: function (data, type, row, meta) {
            if (data == "0" || data == "1") {
              return type === 'display' ?
                `<span class="label label-lg label-light-success label-inline font-weight-bolder d-none">${parseFloat(0).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</span>` :
                data;
            } else if (data == "2") {
              return type === 'display' ?
                `<span class="label label-lg label-light-success label-inline font-weight-bolder">${parseFloat(row.valor).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</span>` :
                ''
            }
          }
        },
        {
          data: "livro_caixa_id", render: function (data, type, row, meta) {
            return type === "display"
              ? `<button onclick="deleta(${data}, true)" class="btn btn-sm btn-icon btn-danger"><i class="far fa-trash-alt"></i></button> <button onclick="editModal(${data}, '${row.descricao}', ${row.status},'${row.valor}', ${row.comissao})" class="btn btn-sm btn-icon btn-success"><i class="fas fa-edit"></i></button>` : data;
          }
        } //4
      ],
    });
      checkDatatablesCreated = true;

      $('#addLivroDiv').removeClass('d-none');
     
  }


  var search_date = $("#search_date").val();
  var proprietarios = $("#proprietarios").val();

  table.DataTable().column(0).search(search_date).column(1).search(proprietarios).draw();
  livroCaixa(proprietarios , search_date)

}

// On click bt search
$("#search_dt").on("click", function () {
  searchQueryDt();
});
// On enter
$(".serch_input").on("keypress", function (e) {
  if (e.which === 13) {
    searchQueryDt();
  }
});

function editModal(livroCaixaId,  descricao, status, valor, comissionado) {

  const proprietarios = $("#proprietarios").val();
  const search_date = $("#search_date").val();
  livroCaixa(proprietarios, search_date) 

  $('#add').attr('action', '../../../../../api/livro-caixa/' + livroCaixaId + '/update');
  $('textarea#descricao').val(descricao);
  $('#addLivroCaixa').modal('show')
 
  $('#status').val(status)
  $('#valor').val(parseFloat(valor).toLocaleString('pt-br', { minimumFractionDigits: 2 }))
  
  if (comissionado == 1) {
    $('#comissionado').attr('checked', true) 

  } else {
    $('#comissionado').attr('checked', false)
  }

}


function formatDate(date) {
  let d = new Date(date);
  let month = (d.getMonth() + 1).toString();
  let day = d.getDate().toString();
  let year = d.getFullYear();
  if (month.length < 2) {
    month = '0' + month;
  }
  if (day.length < 2) {
    day = '0' + day;
  }

  return [year, month].join('-');
}

