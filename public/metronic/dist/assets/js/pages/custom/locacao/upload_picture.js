//Dropzone.autoDiscover = false;
var meuDropzone = new Dropzone("#dropzone", {
  url: "../../../../../api/locacao/upload/photo", // Set the url for your upload script location
  autoProcessQueue: false,
  paramName: "upload", // The name that will be used to transfer the file
  maxFiles: 1,
  maxFilesize: 30, // MB
  parallelUploads: 1,
  uploadMultiple: true,
  addRemoveLinks: true,
  acceptedFiles: "application/pdf",
  dictDefaultMessage: "Arraste ou Clique para selecionar a imagem.",
  dictFileTooBig:
    "O arquivo que você esta tentando enviar é muito grande, por favor entre em contato o administrador do sistema.",
  dictInvalidFileType: "O tipo de arquivo é inválido",
  dictCancelUpload: "Remover Arquivo",
  dictUploadCanceled: "O Arquivo foi removido",
  dictRemoveFile: "Remover Arquivo",
  timeout: 9999999999,

  accept: function (file, done) {
    done();
  },
});

meuDropzone.on("success", function (file, response) {

  KTApp.unblockPage();
 
  var html = "";
  html += response["status-message"];
  html +=
    '<a href="../../../../../locacao" class="btn btn-primary btn-lg">Listar Locações</a>';
  confirmButtonText = "Ok";

  Swal.fire({
    icon: response["status"],
    title: "Opa!",
    text: response["status-message"],
    html: html,
    confirmButtonText: confirmButtonText,
    
  }).then((value) => {
    if (value.isConfirmed || value.isDismissed) {
      window.location.href = '../../../../../locacao';
    }
  });
});

meuDropzone.on("sending", function (file, xhr, formData) {
  formData.append("locacao_id", $("#locacao_id").val()); 
});
