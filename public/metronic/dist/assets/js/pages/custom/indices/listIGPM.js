var tableIGPM = $("#kt_datatable_3");

// begin first table
var datatableIGPM = tableIGPM.DataTable({
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
    url: "../../../../../api/indices/list",
    type: "POST",
    data: {
      tipo: 'IGPM'
    }
  },
  columns: [
    {data: "aliquota" , render : function ( data, type, row, meta ) {
			return type === 'display'  ?
			`<p class="label label-lg label-light-success label-inline font-weight-bolder">${data} %</p>` :
				data;
		}},
    { data: "data" }, // 2
    {data: "indices_id",render: function (data, type, row, meta){
        return type === "display"
          ? '<button onclick="deleta('+data+', true)" class="btn btn-sm btn-icon btn-danger"><i class="far fa-trash-alt"></i></button> <a href="../../../../../indices/' +data +'/edit" class="btn btn-sm btn-icon btn-success"><i class="fas fa-edit"></i></a>': data;
      }
    } //3
  ],
});

function searchQueryDtIGPM() {
  
  var buscaRapida = $("#busca_rapida_IGPM").val(); 
  datatableIGPM.column(0).search(buscaRapida).draw();
}
// On click bt search
$("#search_dt").on("click", function () {
  searchQueryDtIGPM();
});
// On enter
$(".serch_input").on("keypress", function (e) {
  if (e.which === 13) {
    searchQueryDtIGPM();
  }
});




