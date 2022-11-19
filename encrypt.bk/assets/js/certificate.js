function generateCertificateConfirmed() {
  var c_title = $("#certificate_title").val();
  var c_description = $("#certificate_description").val();
  var lecture_id = $("#lecture_id").val();
  var section_id = $("#section_id").val();
  var course_id = $("#course_id").val();
  var certificate_id = $(".active-banner").attr("data-id");
  var certificate_file = $(".active-banner").attr("data-file");
  var errorCount = 0;
  var errorMessage = "";

  if (c_title == "") {
    errorCount++;
    errorMessage += "Please provide Certificate title <br />";
  }

  if (errorCount > 0) {
    $("#collapseOne .panel-body").prepend(
      renderPopUpMessage("error", errorMessage)
    );
  } else {
    $.ajax({
      url: admin_url + "coursebuilder/save_certificate",
      type: "POST",
      data: {
        course_id: course_id,
        is_ajax: true,
        certificate_title: c_title,
        certificate_description: c_description,
        section_id: section_id,
        lecture_id: lecture_id,
        certificate_id: certificate_id,
        certificate_file: certificate_file
      },
      beforeSend: function() {
        $("#save_certificate_btn").text("SAVING...");
        $("#save_certificate_btn").attr("disabled", "disabled");
      },
      success: function(response) {
        var data = $.parseJSON(response);
        if (data["error"] == false) {
          scrollToTopOfPage();
          var messageObject = {
            body: data["message"],
            button_yes: "OK"
          };
          callback_success_modal(messageObject, pageReload);
        } else {
          cleanPopUpMessage();
          $("#collapseOne .panel-body").prepend(
            renderPopUpMessage("error", data["message"])
          );
        }
        $('#save_certificate_btn').html('SAVE');
      }
    });
  }
}
function pageReload() {
  location.reload();
}

function saveCertificateSelection() {
  var lecture_id = $("#lecture_id").val();
  var certificate_id = $(".active-banner").attr("data-id");
  var certificate_file = $(".active-banner").attr("data-file");
  $.ajax({
    url: admin_url + "coursebuilder/save_certificate_only",
    type: "POST",
    data: {
      is_ajax: true,
      lecture_id: lecture_id,
      certificate_id: certificate_id,
      certificate_file: certificate_file
    },
    success: function(response) {
    }
  });
}

function renderCertificatesHtml(certificates) {
  var certificatesHtml  = '';
  if(Object.keys(certificates).length > 0 ) {
      $.each(certificates, function(certificateKey, certificate )
      {
          certificatesHtml += renderCertificateHtml(certificate);
      });
  }
  return certificatesHtml;
}

function renderCertificateHtml(certificate) {
  var certificateHtml = '';
      certificateHtml    +='<li class="certificate-thumbs">';
      certificateHtml    +='  <a href="javascript:void(0)" class="banner-thumb certificate-item" data-file="'+certificate['cm_filename']+'" data-id="'+certificate['id']+'">';
      certificateHtml    +='      <img src="'+__certificatePath+certificate['cm_image']+'" class="cer-preview" width="100%">';
      certificateHtml    +='      <span class="triangle"><i class="icon icon-ok-circled"></i></span>';
      certificateHtml    +='  </a>';
      certificateHtml    +='</li>';
  return certificateHtml;
}

$(document).ready(function(){
  if( typeof __activeCertificate['cl_org_file_name'] != 'undefined' && __activeCertificate['cl_org_file_name'] != ''&& __activeCertificate['cl_org_file_name'] != null ) {
      $('.certificate-item').removeClass('active-banner');
      $('a.certificate-item[data-id="'+__activeCertificate['cl_filename']+'"]').addClass('active-banner');
      var previewImage = $('a.certificate-item[data-id="'+__activeCertificate['cl_filename']+'"] img').attr('src');
          
          if(previewImage!=undefined){
            previewImage = previewImage.split('.');
            previewImage[previewImage.length - 1] = 'jpeg';
            previewImage = previewImage.join('.');
          }
      
      $('#previewImg').attr('src', previewImage);
  } else {
    $(".certificate-item:first").addClass('active-banner');
    var previewImage = $(".certificate-item:first img").attr('src');
    var imageExist=$(".certificate-item").length;
    if(imageExist!=0){

      previewImage = previewImage.split('.');
      previewImage[previewImage.length - 1] = 'jpeg';
      previewImage = previewImage.join('.');
      $('#previewImg').attr('src', previewImage);

    }else{
      var renderNoContent= "<p style='text-align:center'>No Templates to Display.Please upload one.</p>"
      $('.certificate-preview').html(renderNoContent);
    }
      
  }
 
  
});

$(document).on('change', '#import_certificate', function (e) {
  //console.log(e.currentTarget.files[0]);
  $('#percentage_bar_certificate').hide();
  __uploaded_files = e.currentTarget.files[0];
  __uploaded_files['extension'] = __uploaded_files['name'].split('.').pop();
  $('#upload_user_file').val(__uploaded_files['name']);
});
function beforeUploadClear(){
  $('#upload_user_file').val('');
  __uploaded_files = '';
  __uploading_file_certificate = new Array();
  cleanPopUpMessage();
}
function uploadCertificateProcessCompleted(response)
{
    $('#certificate_progress_div').css('display','none');
    $('#certificate_action_button_wrapper').show();
    var data             = $.parseJSON(response);
    console.log(data);
    if(data['error'] == true) {
      $('#addCertificate .modal-body').prepend(renderPopUpMessage('error', data['message']));
      scrollToTopOfPage();
    } else {
      $('#addCertificate .modal-body').prepend(renderPopUpMessage('success', 'Certificate uploaded successfully'));
      $('#certificates_list').prepend(renderCertificateHtml(data['certificate']));
      $('.certificate-item').removeClass('active-banner');
      $(".certificate-item:first").addClass('active-banner');
      var previewImage = $(".certificate-item:first img").attr('src');
        previewImage = previewImage.split('.');
        previewImage[previewImage.length - 1] = 'jpeg';
        previewImage = previewImage.join('.');
        $('#previewImg').attr('src', previewImage);
      $('#upload_user_file').val('');
      __uploaded_files = null;
      $('.progress-bar').css('width','0');
      setTimeout(() => {
        saveCertificateSelection();
        $('#addCertificate').modal('hide');
          cleanPopUpMessage();
      }, 500);
    }
}

function uploadCertificate() {
  cleanPopUpMessage();
  __uploading_file_certificate = __uploaded_files;
  if( __uploading_file_certificate == null||__uploading_file_certificate == '')
  {
      $('#addCertificate .modal-body').prepend(renderPopUpMessage('error', 'Please upload certificate.'));
      return false;
  }
  $('#certificate_progress_div').css('display','block');
  var uploadURL                   = admin_url+"environment/upload_certificate" ;
  var fileObj                     = new processFileName(__uploading_file_certificate['name']);
  var param                       = new Array;
      param["file_name"]          = fileObj.uniqueFileName();
      param["extension"]          = fileObj.fileExtension();
      param["file"]               = __uploading_file_certificate;
      param['processing']         = 'certificate_percentage_count';

      $('#certificate_progress_div').hide();
      if(param["extension"] == 'docx' )
      {
          $('#certificate_progress_div').show();
          $('#certificate_action_button_wrapper').hide();
          uploadFiles(uploadURL, param, uploadCertificateProcessCompleted);
      }
      else
      {
          
          $('#addCertificate .modal-body').prepend(renderPopUpMessage('error', 'Invalid file type'));
      }
      $('#upload_user_file').val('');
      __uploaded_files = '';
      __uploading_file_certificate = new Array();
}

$(document).on('click', '.certificate-item', function(){
  var item = this;
      $('.certificate-item').removeClass('active-banner');
      $(item).addClass('active-banner');
      var selectedImg = $(item).find('img.cer-preview').attr('src');
          selectedImg = selectedImg.split('.');
          selectedImg[selectedImg.length - 1] = 'jpeg';
          selectedImg = selectedImg.join('.');
      $('#previewImg').attr('src',selectedImg);
      saveCertificateSelection();
});
