var table = $("#kt_datatable_1");

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
    url: "../../../../../api/indices/list",
    type: "POST",
    data: {
      tipo: 'IPCA'
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

function searchQueryDt() {
  
  var filter_data = $("#filter_data").val(); 
  datatable.column(0).search(filter_data).draw();
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




