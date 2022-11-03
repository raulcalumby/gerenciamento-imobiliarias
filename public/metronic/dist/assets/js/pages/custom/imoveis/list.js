var table = $("#kt_datatable");

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
  pageLength: 15,
  processing: true,
  serverSide: true,
  ajax: {
    url: "../../../../../api/imoveis/list?v=" + new Date().getTime(),
    type: "POST",
    
  },
  columns: [
    { data: "codigo" },
    { data: "endereco_imovel" }, 
    { data: "nome_completo" }, 
    {data: "imoveis_id",render: function (data, type, row, meta) {
        return type === "display"
          ? '<button onclick="deleta('+data+', true)" class="btn btn-sm btn-icon btn-danger"><i class="far fa-trash-alt"></i></button> <a href="../../../../../imoveis/' +data +'/edit" class="btn btn-sm btn-icon btn-success"><i class="fas fa-edit"></i></a>': data;
      }
    } 
  ],
});

function searchQueryDt() {
  
  var buscaRapida = $("#busca_rapida").val(); 
  var tipo = $("#tipo").val(); 
  var proprietario = $('#proprietario').val();
  var endereco = $('#endereco').val();
  
 
  datatable.column(0).search(buscaRapida).column(1).search(tipo).column(2).search(proprietario).column(3).search(endereco).draw();
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




