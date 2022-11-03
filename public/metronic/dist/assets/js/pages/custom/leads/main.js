function deleta(leads_id, list = false) {
  Swal.fire({
    icon: "error",
    title: "Tem certeza? <br> Esta ação será irreversivel!",
    // html: html,
    showCancelButton: true,
    showConfirmButton: true,
    showCloseButton: true,
    focusConfirm: false,
    allowEscapeKey: true,
    allowOutsideClick: true,
  }).then((value) => {
    if (value["isConfirmed"] == true) {
      $.ajax({
        type: "post",
        url: "../../../../../api/leads/disable",
        dataType: "json",
        data: {
          leads_id: parseInt(leads_id),
        },
        beforeSend: function () {
          KTApp.blockPage({
            opacity: 0.2,
            overlayColor: "#3699FF",
            state: "danger",
            message: "Enviando...",
          });
        },
        success: function (response) {
          KTApp.unblockPage();
          Swal.fire({
            icon: response["status"],
            title: response["status-message"],
            //html: html,
            showCancelButton: false,
            showConfirmButton: true,
            showCloseButton: true,
            focusConfirm: true,
            allowEscapeKey: true,
            allowOutsideClick: true,
          }).then((value) => {
            if (list) {
              searchQueryDt();
            } else {
              location.href = "../../../../../leads";
            }
          });
        },
        error: function (request, status, error) {},
      });
    }
  });
}


function editModal(id, name , email, phone, message_text)
{

  $('#add')[0].reset();
  $('#add').attr('action', `../../../../../api/leads/${id}/update`);

  $('#leads_id').val(id)

  $('#name').val(name)
  $('#phone').val(phone)
  $('#email').val(email)
  $('#message_text').val(message_text)
  $('#modalLeads').modal('show');
}

function addModal()
{
  $('#add')[0].reset();
  $('#add').attr('action', `../../../../../api/leads/add`);
  $('#modalLeads').modal('show');
}
console.log('dsadsadd')