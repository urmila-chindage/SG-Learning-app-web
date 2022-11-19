var __course_path = '';
var __img_format = new Array('jpg', 'jpeg', 'png', 'gif');
var __upload_path = '';
var __selcted_tutor = new Array();

//Live Price update
var cb_price = $('#cb_price'),
    cb_live_price = $('#cb_live_price');

//Live Discount update
var cb_discount = $('#cb_discount'),
    cb_live_discount = $('#cb_live_discount');

$(document).on('click', '#_youtube', function() {

    $('.message_container').remove();
    var URL = $('#myUrl').val();
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = URL.match(regExp);
    if (URL != undefined || URL != '') {
        if (match && match[7].length == 11) {
            $('#myUrl').removeClass('border-error');
            cleanPopUpMessage();
            $("#course_form_youtube").submit();
            return true;
        } else {
            cleanPopUpMessage();
            $('#course_div_youtube').prepend(renderPopUpMessage('error', 'Please enter youtube URL'));
            $('#myUrl').addClass('border-error');
            scrollToTopOfPage();
            return false;
        }

    }
});

$(document).on('click', '#save_course_youtube', function() {
    $("#course_form_youtube").submit();
    return true;

});

$(document).on('click', '#course_savenext_button', function() {
    $('#savenextform').val(1);
    $("#course_save_button").trigger("click");
});
$(document).on('click', '#course_savenext_buttonadvanced', function() {
    $('#savenextform').val(1);
    $("#save_course_youtube").trigger("click");
});
$(document).on('click', '#course_save_button', function() {
    
    $('.alert-success').remove();
    var errorCount      = 0;
    var errorMessage    = '';
    var keyword         = $('.bootstrap-tagsinput input').val();
    var exist_lang      = $('.bootstrap-tagsinput span').text();
    var us_language_speaks = $('#cb_language').val();
    var cb_title        = $("#cb_title").val().trim();

    if (keyword == '' && exist_lang == '') {
        errorCount++;
        errorMessage += 'Enter course language <br />';
    }

    if (cb_title == '') {
        errorCount++;
        errorMessage += 'Enter course title <br />';
    }else{
        $.ajax({
            url: admin_url+'course/ajax_name_check',
            global: false,
            type: 'POST',
            data: { c_id : __course_id, cb_title : cb_title },
            async: false, //blocks window close
            success: function(data) { 
                var res = jQuery.parseJSON(data);
                if(res.error){
                    errorMessage += `The course title <b>${res.cb_title}</b> is already in use<br />`;
                    errorCount++;
                }
            }
        });
    
}
//return false;
        var cb_description = $("#cb_description").val().trim();
        if(!stripHtmlTags(cb_description)){
            errorCount++;
            errorMessage    += 'Course description is required.<br /> '; 
        }
    

    if ($('#cb_access_limited').is(':checked')) {
        if ($('.cb_validity').val() == '' || $('.cb_validity').val() == '0') {
            $('#course_form').prepend(renderPopUpMessage('error', 'Enter course validity days'));
            scrollToTopOfPage();
            return false;
        }
    } else if ($('#cb_access_limited_by_date').is(':checked')) {
        if ($('.cb_validity_date').val() == '' || $('.cb_validity_date').val() == '00-00-0000') {
            $('#course_form').prepend(renderPopUpMessage('error', 'Choose course expiry date'));
            scrollToTopOfPage();
            return false;
        }
    }

    if ((cb_price.val() <= 0) && $("#cb_is_free").is(":checked") == false) {
        errorCount++;
        errorMessage += 'Course price should be greater than zero <br />';
    }
    if ((cb_discount.val() != '' && cb_discount.val() <= 0) && $("#cb_is_free").is(":checked") == false) {
        errorCount++;
        errorMessage += 'Course discount price should be greater than zero <br />';
    }

    if (parseInt(cb_price.val()) < parseInt(cb_discount.val())) {
        $('#course_form').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
        scrollToTopOfPage();
    } else if (parseInt(cb_price.val()) == parseInt(cb_discount.val())) {
        $('#course_form').prepend(renderPopUpMessage('error', 'Discount price should not be equal to price amount.'));
        scrollToTopOfPage();
    } else {
        if (errorCount > 0) { 
            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
            return false;
        } else {
            if (us_language_speaks != '') {

                var url = admin_url + 'faculties/check_valid_language';
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        'us_language_speaks': us_language_speaks,
                        'is_ajax': true
                    },
                    success: function(response) {
                        var data = $.parseJSON(response);

                        if (data['error'] == true) {
                            errorMessage += data['message'] + '<br />';
                            $('#course_form').prepend(renderPopUpMessage('error', errorMessage));
                            scrollToTopOfPage();
                            return false;
                        } else {
                            $('#save_course_basics').submit();
                        }

                    },
                });

            }
        }
    }
});

$(document).on('click', '#course_revenue_button', function() {
    $('.message_container').remove();
    var cb_revenue_share = $('#cb_revenue_share').val();
    var errorCount = 0;
    if (isNaN(cb_revenue_share) || cb_revenue_share <= 0) {
        $('#save_course_basics').prepend(renderPopUpMessage('error', 'Enter a valid percentage.'));
        scrollToTopOfPage();
        errorCount++;
    } else {
        if (cb_revenue_share > 100) {
            $('#save_course_basics').prepend(renderPopUpMessage('error', 'Percentage should be in between 1 and 100.'));
            scrollToTopOfPage();
            errorCount++;
        }
    }
    if (errorCount == 0) {
        $("#save_course_basics").submit();
    }
});

function preventAlphabetsPercentage(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    //alert(charCode);
    if ((charCode != 46 && charCode > 31 &&
            (charCode < 48 || charCode > 57)))
        return false;

    return true;
}

function readImageData(imgData) {
    if (imgData.files && imgData.files[0]) {
        var readerObj = new FileReader();

        readerObj.onload = function(element) { 
            $('#site_logo').attr('src', element.target.result);
            $('#site_logo').attr('data-id', 'true');
            //console.log(readerObj.result.split(';')[0].split('/')[1]);
            var validImageTypes = ['gif', 'jpeg', 'png', 'jpg'];
            if (!validImageTypes.includes(readerObj.result.split(';')[0].split('/')[1])) {
                $('#site_logo').attr('src',__course_loaded_img);
                $('#site_logo').attr('data-id', 'default.jpg');
                lauch_common_message('Image Size', 'The image you have choosen is not supported! please choose a valid file.');
                return false;
            }

            var img = new Image;

            img.onload = function() { 

                if(img.width < 740 || img.height < 452){
                    lauch_common_message('Image Size', 'The file you have chosen is too small and cannot be uploaded.');
                    $('#site_logo').attr('src',__course_loaded_img);
                    $('#site_logo').attr('data-id', 'default.jpg');
                    return false;
                }
                
            };

            img.src = element.target.result;
        }
        readerObj.readAsDataURL(imgData.files[0]);
    }
}
$(document).ready(function() {
    readerObj='';
    $('.message_container').delay(3000).fadeOut();
    $("#cb_description").trigger("keyup");
    $("#cb_meta_description").trigger("keyup");
    if ($("#cb_is_free").is(":checked")) {
        $("#cb_price").attr('data-validation-optional', 'true');
    }

    cb_discount.trigger("keyup");

    $("#site_logo_btn").change(function() {
        readImageData(this); //Call image read and render function
    });


});

$(document).on('click', '#cb_is_free', function() {
    if ($(this).is(':checked')) {
        $("#course_price_val").hide();
        $("#course_discount_val").hide();
    } else {
        $("#course_price_val").show();
        $("#course_discount_val").show();
    }
});

$(document).on('change', '.cb_preview', function() {
    if ($(this).val() == '0') {
        $("#course_preview_time").hide();
        $("#course_preview_time").val('');
    } else {
        
        $("#course_preview_time").show();

    }
});

$(document).on('change', '.self_enroll', function() {

    if ($(this).val() == '0') {
        $("#close_date").val('');
        $("#course_self_date_block").hide();
        
    } else {
        $("#course_self_date_block").show();

    }
});



if ($('#self_enroll_yes').is(':checked')) {

    $("#course_self_date_block").show();

} else {
    $("#course_self_date_block").hide();

}


if ($('#cb_preview_yes').is(':checked')) {

    $("#course_preview_time").show();
} else {
    $("#course_preview_time").hide();
}

$(document).ready(function(e) {
    var today = new Date();
    $('#cb_validity_date').datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yyyy',
        setDate: "10/12/2012"
    });
    $('#course_self_date').datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yyyy',
        setDate: "10/12/2012"
    });

    //var course_top_offset = $(".header").height() + $(".breadcrumb").height() + $(".courses-tab").height();
    $('#cb_description').redactor({
        //toolbarFixedTopOffset: course_top_offset,
        maxHeight: '250px',
        maxHeight: '250px',
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment', 'source'],
        callbacks: {
            // imageUploadError: function(json, xhr) {
            //     var erorFileMsg = "This file type is not allowed. upload a valid image.";
            //     $('#course_form').prepend(renderPopUpMessage('error', erorFileMsg));
            //     scrollToTopOfPage();
            //     return false;
            // }

            image: {
                uploadError: function(response)
                {
                    $('#redactorAlert').remove();
                    $('.redactor-modal-footer').append(`<div class="alert alert-danger alert-dismissible" id="redactorAlert" style="margin: 0auto important;">
                                                    ${response.message}
                                                    </div>`);
                    console.log(response.message);
                    //var erorFileMsg = "This file type is not allowed. upload a valid image.";
                    //$('#course_form').prepend(renderPopUpMessage('error', response.message));
                    scrollToTopOfPage();
                    return false;
                    /*$('#redactor_image_upload_response').remove();
                    $('.redactor-modal-body').append(`<div id="redactor_image_upload_response" class="alert alert-danger alert-dismissible" style="margin-top: 20px;">
                    <a href="#" class="close" aria-label="close" onclick="$('.alert').remove()">&times;</a>
                    <strong>${response.message}</strong>
                  </div>`);*/
                }
            },
            file: {
                uploadError: function(response)
                {
                    console.log(response.message);
                }
            }
        }
    });
});

$(document).on('change', '#cb_image', function(e) {
    __uploading_file = e.currentTarget.files;
    if (__uploading_file.length > 1) {
        alert('more than one file not allowed');
        return false;
    }
    var i = 0;
    var uploadURL = admin_url + "course_settings/upload_course_image_to_localserver";
    var fileObj = new processFileName(__uploading_file[i]['name']);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    param["id"] = course_id;

    uploadFiles(uploadURL, param, uploadCourseImageCompleted);
});


function uploadCourseImageCompleted(response) {
    var data = $.parseJSON(response);
    console.log(data);
    $('#course_image').attr('src', data['course_image'])
}


//Live Title update
var cb_title = $('#cb_title'),
    cb_live_title = $('#cb_live_title');
var revenue_share = $('#cb_revenue_share');

cb_title.keyup(function(e) {
    cb_live_title.text(cb_title.val());
    if (cb_title.val() === '') {
        cb_live_title.text("Mathematical Calculations");
    }
});

revenue_share.keyup(function(e) {

    var share_percent = revenue_share.val();

    var isInvalid = false;
    if (share_percent > 100) {
        isInvalid = true;
    }
    var admin_share = 0;

    if (discount_price != 0) {
        admin_share = (share_percent / 100) * discount_price;
    } else {
        admin_share = (share_percent / 100) * original_price;
    }
    admin_share = admin_share.toFixed(2)
    var teacher_share = (100 - share_percent);

    if (isNaN(admin_share)) {
        admin_share = 0;
    }
    if (isNaN(teacher_share)) {
        teacher_share = 0;
    }


    //if(revenue_share.val() != 0){
    var shareHtml = '';

    shareHtml += '<table style="width:100%">';
    shareHtml += '  <tr>';
    shareHtml += '      <th>Title</th>';
    shareHtml += '      <th>Price</th>';
    shareHtml += '      <th>Online Profesor share</th>';
    shareHtml += '      <th>Teacher share</th>';
    shareHtml += '      <th>You get</th>';
    shareHtml += '  </tr>';
    shareHtml += '  <tr>';
    if (isInvalid == true) {
        shareHtml += '      <td>' + course_title + '</td>';
        shareHtml += '      <td>Rs.' + price + '/-</td>';
        shareHtml += '      <td>0%</td>';
        shareHtml += '      <td>0%</td>';
        shareHtml += '      <td></td>';
    } else {
        shareHtml += '      <td>' + course_title + '</td>';
        shareHtml += '      <td>Rs.' + price + '/-</td>';
        shareHtml += '      <td>' + share_percent + '%</td>';
        shareHtml += '      <td>' + teacher_share + '%</td>';
        shareHtml += '      <td>Rs.' + admin_share + '/-</td>';

    }
    shareHtml += '  </tr>';
    shareHtml += '</table>';

    $("#share_percentage").html(shareHtml);
    //}
});


//Live character count update
var cb_description = 1000;
$('#cb_description_remain').html(cb_description + ' Characters left');

$('#cb_description').keyup(function() {
    var cb_description_length = $('#cb_description').val().length;
    var cb_description_remain = cb_description - cb_description_length;
    $('#cb_description_remain').html(cb_description_remain + ' Characters left');
});

//Live character count update
var cb_meta_description = 200;
$('#cb_meta_description_remain').html(cb_meta_description + ' Characters left');

$('#cb_meta_description').keyup(function() {
    var cb_meta_description_length = $('#cb_meta_description').val().length;
    var cb_meta_description_remain = cb_meta_description - cb_meta_description_length;
    $('#cb_meta_description_remain').html(cb_meta_description_remain + ' Characters left');
});

var current_li = '';
$(document).on('click', '.auto-search-lister li', function() {
    $('#cb_category').val($(this).text());
    $(this).parent().html('').hide();
});

//Get language list for Auto suggest
var current_lang = '';
var timeOut_lang = '';
$(document).on('keyup', '.bootstrap-tagsinput input', function() {

    var keyword = $(this).val();
    var method = $(this).parents('.user-taginput-type').attr('id');
    __currentLanguage = this;
    switch (method) {
        case "language_speaks":
            getLanguagess(keyword);
            break;
        case "category_id":
            getCategoriess(keyword);
            break;

    }
});

var current_li = '';
$(document).on('click', '.auto-search-lister-lang li', function() {
    $('#cb_language').tagsinput('add', $(this).text());
    $('.bootstrap-tagsinput input').val('');
    $(current_lang).keypress();
    $(this).parent().html('').hide();
});

$(document).on('click', '.auto-search-lister li', function() {
    $('#cb_category').tagsinput('add', $(this).text());
    $('.bootstrap-tagsinput input').val('');
    $(current_lang).keypress();
    $(this).parent().html('').hide();
});

cb_price.keyup(function(e) {
    cb_live_discount.hide();
    cb_live_price.removeClass('strike-txt');
    cb_live_price.text("Rs " + cb_price.val());
    var typed_price = $(this).val();
    // if(parseInt($(this).val()) < parseInt(cb_discount.val())){
    //     $('.form-horizontal').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
    //     cleanPopUpMessage();
    //     scrollToTopOfPage();
    //     return false;
    // }
    // else{
    //        cleanPopUpMessage();
    //        return true;
    // }
    if (cb_price.val() == '') {
        cb_live_price.text("Rs 100");
        cb_live_price.addClass('strike-txt');
        cb_live_discount.text("Rs 55");
        cb_live_discount.show();
    }
});

cb_discount.keyup(function(e) {

    cb_live_discount.show();
    cb_live_price.addClass('strike-txt');
    cb_live_discount.text("Rs " + cb_discount.val());
    var typed_disc_price = $(this).val();
    // if(parseInt($(this).val()) > parseInt(cb_price.val())){
    //     $('.form-horizontal').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
    //     cleanPopUpMessage();
    //     scrollToTopOfPage();
    // }
    // else{
    //        cleanPopUpMessage();
    // }
    if (cb_discount.val() === '' || cb_discount.val() === 0) {

        cb_live_discount.hide();
        cb_live_price.removeClass('strike-txt');
        cb_live_discount.text("Rs 55");
    }
});

if ($('#cb_access_unlimited').is(':checked')) {
    $('#course_validity').hide();
    $('#course_validity_date').hide();
} else if ($('#cb_access_limited').is(':checked')) {
    $('#course_validity').show();
    $('#course_validity_date').hide();
} else {
    $('#course_validity_date').show();
    $('#course_validity').hide();
}

$('#cb_access_unlimited').click(function() {
    if ($('#cb_access_unlimited').is(':checked')) {
        $('#course_validity').hide();
        $('#course_validity_date').hide();
    }
});

$('#cb_access_limited').click(function() {
    $('#course_validity_date').hide();
    if ($('#cb_access_limited').is(':checked')) {
        $('#course_validity').show();
    }
});

$('#cb_access_limited_by_date').click(function() {
    $('#course_validity').hide();
    if ($('#cb_access_limited_by_date').is(':checked')) {
        $('#course_validity_date').show();
    }
});

function addTeacherToCourse() {
    var tutorHtml = '';
    $('#get_tutor_list').html(tutorHtml);

    $.ajax({
        url: admin_url + 'course_settings/get_tutors',
        type: "POST",
        data: {
            "is_ajax": true
        },
        success: function(response) {
            
            var data = $.parseJSON(response);
            if (data['tutors'].length > 0) {
                for (var i = 0; i < data['tutors'].length; i++) {
                    tutorHtml += '<div class="checkbox-wrap">';
                    tutorHtml += '  <span class="chk-box">';
                    tutorHtml += '      <label class="font14">';
                    tutorHtml += '          <input id="tutor_details_' + data['tutors'][i]['id'] + '" class="tutor-checkbox" value="' + data['tutors'][i]['id'] + '" type="checkbox">' + data['tutors'][i]['us_name'];
                    tutorHtml += '      </label>';
                    tutorHtml += '  </span>';
                    tutorHtml += '  <span class="email-label pull-right">' + data['tutors'][i]['us_email'] + '</span>';
                    tutorHtml += '</div>';
                }
            }
            $('#get_tutor_list').html(tutorHtml);
            $(".tutor-checkbox").each(function() {
                if (inArray($(this).val(), __selcted_tutor)) {
                    $(this).prop("checked", true);
                }
            });
        }
    });
}

var tutor_selected = new Array();
$(document).on('click', '.tutor-checkbox', function() {
    var tutor_id = $(this).val();
    if ($(this).is(':checked')) {
        tutor_selected.push(tutor_id);
    } else {
        removeArrayIndex(tutor_selected, tutor_id);
    }
});

function removeArrayIndex(array, index) {
    for (var i = array.length; i--;) {
        if (array[i] === index) {
            array.splice(i, 1);
        }
    }
}


//alert('hello js');
function import_tutor() {
    alert('inside import tutor function');
    $.ajax({
        url: admin_url + 'course_settings/save_tutor',
        type: "POST",
        data: {
            "tutors": JSON.stringify(tutor_selected),
            "id": course_id,
            "is_ajax": true
        },
        success: function(response) {
            var data = $.parseJSON(response);
            var tutor_names = Array();
            var tutors_img = '';
            var tutors_name = '';
            __selcted_tutor = [];
            for (i in data) {
                tutor_names.push(data[i]['us_name']);
                tutors_img = tutors_img + data[i]['img_org'];
                __selcted_tutor.push(data[i]['id']);
            }

            tutor_name = tutor_names.join(',');
            if (tutor_name == '') {

                $("#cb_tutor").html("By: " + "<a href='javascript:void(0);' data-toggle='modal' onclick='addTeacherToCourse()' data-target='#add-teacher' class='link-style add-teach'>" + lang('course_add_teacher') + "</a>");
            } else {

                $("#cb_tutor").html("By: " + tutor_name + " <a href='javascript:void(0);' data-toggle='modal' onclick='addTeacherToCourse()' data-target='#add-teacher' class='link-style add-teach'>" + lang('course_add_teacher') + "</a>");
            }
            $("#list-teacher-image").html(tutors_img);
            $("#add-teacher").modal('hide');
        },
        error: function() {
            $("#add-teacher").modal('hide');
        }
    });
}

$("#add-tutor-advanced").on('click', function() {

    addTeacherToCourse();
});


$("#cb_is_free").change(function() {
    if ($(this).is(":checked")) {
        $("#cb_price").attr('data-validation-optional', 'true');
    } else {
        $("#cb_price").removeAttr('data-validation-optional');
    }
});

