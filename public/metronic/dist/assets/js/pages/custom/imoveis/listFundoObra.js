var table = $("#kt_datatable");
var arrayObras = [];
// begin first table
var datatable = table.DataTable({
  language: {
    url: "../../../../../metronic/dist/assets/js/datatable/pt-br.json",
  },
  sDom: "lrtip",

  responsive: true,
  paging: true,
  searching: true,
  ordering: false,
  lengthChange: false,
  pageLength: 4,
  processing: true,
  serverSide: true,
  ajax: {
    url: "../../../../../api/livro-caixa/imoveis/list?v=" + new Date().getTime(),
    type: "POST",
  },
  columns: [

    { data: "fundo_descricao" },
    {
      data: "fundo_valor", render: function (data, type, row, meta) {
        return type === "display"
          ? `<p data-id = '${row.fundo_obra_id}'>${parseFloat(data).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })}</p>` : data;
      }
    },
    { data: "fundo_qtd_parcelas" },

  ],
});

function searchQueryDt() {

  var imovel_id = $("#modalImovelId").val();



  datatable.column(0).search(imovel_id).column(1).draw();
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

searchQueryDt()


$('#t-body').on('click', 'tr td', function () {

  var elementWithAtrr = this.parentNode.childNodes[1].querySelector('p')
  var id = elementWithAtrr.getAttribute('data-id')
  var detalhesObra = arrayObras.find(element => element['fundo_obra_id'] == id);

  //Inserindo dados na modal

  $('#cobraComissao').text('Não');
  if(detalhesObra['fundo_comissao'] == 1)
  {
    $('#cobraComissao').text('Sim');
  }

  $('#qtd_parc').text(detalhesObra['fundo_qtd_parcelas']);
  $('#valor').text(parseFloat(detalhesObra['fundo_valor']).toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
  //$('#data_parcela').text(detalhesObra['fundo_primeira_parcela'])
  $('#descricao').text(detalhesObra['fundo_descricao'])
  $('#parcActual').text(detalhesObra['parcela_atual'])
  $('#excluir').attr('onclick' , `excluirFundo(${detalhesObra['fundo_obra_id']})`)

  
  $('#detalhesObra').modal('show');
  console.log(detalhesObra)
});


datatable.on('xhr', function () {
  //Response puro do Datatable
  var json = datatable.ajax.json();
  if (json['gotData']) {
   arrayObras = json['data']
  }

});
