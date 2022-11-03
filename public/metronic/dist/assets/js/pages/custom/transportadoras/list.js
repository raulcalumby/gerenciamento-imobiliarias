
	var table = $('#kt_datatable');

	// begin first table
	var datatable = table.DataTable({
		"language": {
            "url": "../../../../../metronic/dist/assets/js/datatable/pt-br.json"
        },
		sDom: 'lrtip',
		"columnDefs": [
	
			{
				"targets": [ 4, 5, 7, 8 , 9, 10 ],
				"visible": false
			},
			{
				"targets": [ 0, 1, 2 ,3, 6, 11, 12],
				"visible": true
			},
		
		],
		responsive: true,
/* 			responsive: {
			details: {
				display: $.fn.dataTable.Responsive.display.modal( {
					header: function ( row ) {
						var data = row.data();
						return 'Detalhes de '+data['nome_fantasia'];
					}
				} ),
				renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
					tableClass: 'table'
				} )
			}
		}, */
		"paging": true,
		searching: true,
		"ordering": false,
		"lengthChange": false,
		"pageLength": 15,
		processing: true,
		serverSide: true,
		ajax: {
			url: '../../../../../api/transportadoras/list?v=' + new Date().getTime(),
			type: 'POST',
/* 				data: {
				// parameters for custom backend script demo
				columnsDef: [
					'OrderID', 'Country',
					'ShipAddress', 'CompanyName', 'ShipDate',
					'Status', 'Type', 'Actions'],
			}, */
		},
		"columns": [
			{ "data": "transportadoras_id" }, // 0 
			{ "data": "nome_fantasia" }, // 1
			{ "data": "razao_social" }, // 2
			{ "data": "cnpj" }, // 3
			{ "data": "uf" }, //4
			{ "data": "cidade" }, // 5
			{ "data": "telefone" }, // 6 
			{ "data": "celular" }, // 7
			{ "data": "nome_responsavel" }, // 8
			{ "data": "email" }, //9
			{ "data": "created" }, // 10
			{data: "transportadoras_id" , render : function ( data, type, row, meta ) {
				return type === 'display'  ?
				'<button onclick="deleta('+data+', true)" class="btn btn-sm btn-icon btn-danger"><i class="far fa-trash-alt"></i></button>' :
					data;
			}},
			{data: "transportadoras_id" , render : function ( data, type, row, meta ) {
				console.log(meta);
				return type === 'display'  ?
				'<a href="../../../../../transportadoras/'+ data + '/edit" class="btn btn-sm btn-icon btn-primary"><i class="fas fa-edit"></i></a>':
					data;
				}},
		]
	});
	
	function searchQueryDt(){
		var nome_fantasia_q = $("#nome_fantasia_q").val();
		var cnpj_q = $("#cnpj_q").inputmask('unmaskedvalue');
		datatable.column(1).search(nome_fantasia_q).column(3).search(cnpj_q).draw();
	}
	// On click bt search
	$('#search_dt').on( 'click', function () {
		searchQueryDt();
	} );
	// On enter
	$('.serch_input').on('keypress', function (e) {
		if(e.which === 13){
			searchQueryDt();
		}
  	});