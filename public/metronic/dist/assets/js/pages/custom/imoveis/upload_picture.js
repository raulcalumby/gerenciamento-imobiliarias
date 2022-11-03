//Dropzone.autoDiscover = false;
var meuDropzone = new Dropzone("#dropzone", {
  url: "../../../../../api/imoveis/upload/photo", // Set the url for your upload script location
      autoProcessQueue: false,
        paramName: "upload", // The name that will be used to transfer the file
        maxFiles: 20,
        maxFilesize: 999, // MB
        parallelUploads: 999,
        resizeWidth: 700,
        resizeHeight: 400,
        resizeQuality: 0.4,
        uploadMultiple: true,
        addRemoveLinks: true,
        acceptedFiles: "image/*",
        dictDefaultMessage: "Arraste ou Clique para selecionar a imagem.",
        dictFileTooBig: "O arquivo que você esta tentando enviar é muito grande, por favor entre em contato o administrador do sistema.",
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
    '<a href="../../../../../imoveis" class="btn btn-primary btn-lg">Listar Imóveis</a>';
  confirmButtonText = "Ok";

  Swal.fire({
    icon: response["status"],
    title: "Opa!",
    text: response["status-message"],
    html: html,
    confirmButtonText: confirmButtonText,
    
  }).then((value) => {
    location.reload();
  });
});

meuDropzone.on("sending", function (file, xhr, formData) {
  formData.append("imoveis_id", $("#imoveis_id").val()); 
});
