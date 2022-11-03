function locationGo(url){
    location.href = url;
}
function showMsg(mensagem, tipo, btn_txt){
	swal.fire({
		text: mensagem,
		icon: tipo,
		buttonsStyling: false,
		confirmButtonText: btn_txt,
		customClass: {
			confirmButton: "btn font-weight-bold btn-light-primary"
		}
	}).then(function() {
		KTUtil.scrollTop();
	});
}

function getId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
    const match = url.match(regExp);

    return (match && match[2].length === 11)
      ? match[2]
      : null;
}


function dataAtualFormatada(date){
    var data = date,
        dia  = data.getDate().toString(),
        diaF = (dia.length == 1) ? '0'+dia : dia,
        mes  = (data.getMonth()+1).toString(), //+1 pois no getMonth Janeiro começa com zero.
        mesF = (mes.length == 1) ? '0'+mes : mes,
        anoF = data.getFullYear();
    return anoF+"-"+mesF+"-"+diaF;
}

	function limpa_formulário_cep() {
		// Limpa valores do formulário de cep.
		$("#endereco").val("");
		$("#endereco_bairro").val("");
		$("#cidade").val("");
		$("#estado").val("");
		//$("#ibge").val("");
	}
	
	//Quando o campo cep perde o foco.
	function cepSearch(){ 

		//Nova variável "cep" somente com dígitos.
		var cep = $('#cep').val().replace(/\D/g, '');

		//Verifica se campo cep possui valor informado.
		if (cep != "" && cep.length == 8)  {

			//Expressão regular para validar o CEP.
			var validacep = /^[0-9]{8}$/;

			//Valida o formato do CEP.
			if(validacep.test(cep)) {

				//Preenche os campos com "..." enquanto consulta webservice.
				$("#endereco").val("...");
				$("#bairro").val("...");
				$("#cidade").val("...");
				$("#estado").val("...");
				//$("#ibge").val("...");

				//Consulta o webservice viacep.com.br/
				$.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {

					if (!("erro" in dados)) {
						//Atualiza os campos com os valores da consulta.
						$("#endereco").val(dados.logradouro);
						$("#bairro").val(dados.bairro);
						$("#cidade").val(dados.localidade);
						$("#estado").val(dados.uf);
					} //end if.
					else {
						//CEP pesquisado não foi encontrado.
						limpa_formulário_cep();
						$.notify({
							// options
							message: 'CEP não encontrado.',
						},{
							// settings
							type: 'danger'
						});
						//alert("CEP não encontrado.");
					}
				});
			} //end if.
			else {
				//cep é inválido.
				limpa_formulário_cep();
				//alert("Formato de CEP inválido.");
				$.notify({
					// options
					message: 'Formato de CEP inválido.',
				},{
					// settings
					type: 'danger'
				});
			}
		} //end if.
		else {
			//cep sem valor, limpa formulário.
			limpa_formulário_cep();
		}
	}

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
		  var c = ca[i];
		  while (c.charAt(0) == ' ') {
			c = c.substring(1);
		  }
		  if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		  }
		}
		return "";
	  }
  
	function searchStringInArray (str, strArray) {
		for (var j=0; j<strArray.length; j++) {
			if (strArray[j].match(str)) return j;
		}
		return false;
	}
 

 
 
	$(document).ready(function() {
		$('.mostrar_senha').click(function(e) {  
		  var id_pass = $(this).attr( "data-target-id" );
		  var type = $("#" + id_pass).attr( "type" );
		  switch (type) {
			  case 'text':
				$("#" + id_pass).prop("type", "password");
				$(this).html('<i class="far fa-eye"></i>Mostrar Senha</div>');
				break;
			  case 'password':
				$("#" + id_pass).prop("type", "text");
				$(this).html('<i class="far fa-eye-slash"></i>Esconder Senha</div>');
				break;
		  }
		});
	});

 