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
    url: "../../../../../api/leads/list?v=" + new Date().getTime(),
    type: "POST",
    
  },
  columns: [
    { data: "name" },
    { data: "email" }, 
    { data: "phone" }, 
    {data: "leads_id",render: function (data, type, row, meta) {
        return type === "display"
          ? `<button onclick="deleta(${data}, true)" class="btn btn-sm btn-icon btn-danger"><i class="far fa-trash-alt"></i></button> <a onclick ="editModal('${data}', '${row.name}',  '${row.email}', '${row.phone}',  '${row.message_text}')" class="btn btn-sm btn-icon btn-success"><i class="fas fa-edit"></i></a>`: data;
      }
    } 
  ],
});

function searchQueryDt() {
  
  var search_name = $("#search_name").val(); 

  
 
  datatable.column(0).search(search_name).draw();
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



