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
    url: "../../../../../api/imoveis/proprietario/list?v=" + new Date().getTime(),
    type: "POST",
    data:{
      proprietario_id: $("#proprietario_id").val(),
    }
  },
  columns: [
    { data: "codigo" }, // 0 
    { data: "endereco" }, // 3
    {data: "imoveis_id",render: function (data, type, row, meta) {
          return type === "display"
            ? '<a href="../../../../../imoveis/' +data +'/edit" class="btn btn-sm btn-icon btn-success"><i class="fas fa-edit"></i></a>': data;
        }
      } 
    ],
});

function searchQueryDt() {
  
  var buscaRapida = $("#busca_rapida").val(); 
  datatable.column(0).search(buscaRapida).draw();
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




