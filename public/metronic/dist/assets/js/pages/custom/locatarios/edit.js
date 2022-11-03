$("#edit").submit(function (event) {
  event.preventDefault(); //prevent default action
  var post_url = $(this).attr("action"); //get form action url
  var request_method = $(this).attr("method"); //get form GET/POST method
  var form_data = $(this).serializeArray(); //Encode form elements for submission
  var data = {};

  $(form_data).each(function (index, obj) {
    data[obj.name] = obj.value;
  });

  $.ajax({
    url: post_url,
    type: request_method,
    data: data,
    dataType: "json",
    beforeSend: function (xhr) {
      //Desativa o Form
      KTApp.blockPage({
        opacity: 0.2,
        overlayColor: "#3699FF",
        state: "danger",
        message: "Enviando...",
      });
    },
    success: function (response) {
      KTApp.unblockPage();
      Swal.fire("Opa!", response["status-message"], response["status"]);
    },
  }).done(function (response) {});
});
