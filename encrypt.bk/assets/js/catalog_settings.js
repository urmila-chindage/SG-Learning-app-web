var __catalog_path = '';
var __img_format = new Array('jpg', 'jpeg', 'png', 'gif');
var __upload_path = '';

var __courses_selected = new Array();
var __course_total = 0;
var __course_count;
//Live Price update
var c_price = $('#c_price'),
    c_live_price = $('#c_live_price');

//Live Discount update
var c_discount = $('#c_discount'),
    c_live_discount = $('#c_live_discount');

$(document).on('click', '#save_catalog_youtube', function() {
    $('.message_container').remove();
    var URL = $('#myUrl').val();
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = URL.match(regExp);
    if (URL != undefined || URL != '') {
        if (match && match[7].length == 11) {
            $('#myUrl').removeClass('border-error');
            cleanPopUpMessage();
            $("#catalog_form_youtube").submit();
            return true;
        } else {
            cleanPopUpMessage();
            $('#catalog_div_youtube').prepend(renderPopUpMessage('error', 'Please enter youtube URL'));
            $('#myUrl').addClass('border-error');
            scrollToTopOfPage();
            return false;
        }

    }
});

$(document).on('click', '#catalog_save_button', function() {
    if (parseInt(c_price.val()) < parseInt(c_discount.val())) {
        $('#catalog_basics').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
        scrollToTopOfPage();
    } else if (parseInt(c_price.val()) == parseInt(c_discount.val())) {
        $('#catalog_basics').prepend(renderPopUpMessage('error', 'Discount price should not be equal to price amount.'));
        scrollToTopOfPage();
    } else {
        cleanPopUpMessage();
        $("#catalog_basics").submit();
    }
});

$(document).on('click', '#c_is_free', function() {
    if ($(this).is(':checked')) {
        $("#catalog_price_val").hide();
        $("#catalog_discount_val").hide();
    } else {
        $("#catalog_price_val").show();
        $("#catalog_discount_val").show();
    }
});

if ($('#c_is_free').is(':checked')) {
    $("#catalog_price_val").hide();
    $("#catalog_discount_val").hide();
} else {
    $("#catalog_price_val").show();
    $("#catalog_discount_val").show();
}

$(document).ready(function() {


    courseCount(__course_count);
    if ($("#c_is_free").is(":checked")) {
        $("#c_price").attr('data-validation-optional', 'true');
    }

    c_discount.trigger("keyup");

    if (typeof catalog_course != 'undefined') {
        $("#c_description").trigger("keyup");
        $("#c_meta_description").trigger("keyup");

        if (catalog_course == 0) {
            $("#get_course_id").val('');
        } else {
            __courses_selected = $.parseJSON($("#get_course_id").val());
        }

        __course_count = __courses_selected.length;
        courseCount(__course_count);

        $.ajax({
            url: admin_url + 'catalog_settings/get_course_details',
            type: "POST",
            data: {
                "is_ajax": true,
                "course": __courses_selected
            },
            success: function(response) {
                var rendercourseHtml = '';
                var data = $.parseJSON(response);
                var displayPrice = '';
                if (data['courses'].length > 0) {
                    for (var i = 0; i < data['courses'].length; i++) {
                        if (data['courses'][i]['cb_is_free'] == 1) {
                            displayPrice = 'Free';
                        } else {
                            if ((data['courses'][i]['cb_discount'] == '') || (data['courses'][i]['cb_discount'] == 0)) {
                                displayPrice = data['courses'][i]['cb_price'];
                            } else {
                                displayPrice = data['courses'][i]['cb_discount'];
                            }
                            __course_total += Number(displayPrice);

                        }
                        rendercourseHtml += '<div class="rTableRow" id="course_catalog_' + data['courses'][i]['id'] + '">';
                        rendercourseHtml += '    <div class="rTableCell">';
                        rendercourseHtml += '        <span class="icon-wrap-round">';
                        rendercourseHtml += '            <i class="icon icon-graduation-cap"></i>';
                        rendercourseHtml += '        </span>';
                        rendercourseHtml += '        <a href="' + admin_url + 'course/basic/' + data['courses'][i]['id'] + '">' + data['courses'][i]['cb_title'] + '</a>';
                        rendercourseHtml += '    </div>';
                        rendercourseHtml += '    <div class="rTableCell pad0 txt-right">' + displayPrice + '</div>';
                        rendercourseHtml += '    <div class="rTableCell">';
                        rendercourseHtml += '        <a id="' + data['courses'][i]['id'] + '" onclick="deleteCourseCatalog(this.id,\'' + displayPrice + '\')"><i class="icon icon-cancel-1 delte"></i></a>';
                        rendercourseHtml += '    </div>';
                        rendercourseHtml += '</div>';
                    }
                }
                courseTotal(__course_total);
                $('#course_assigned_catalog').html(rendercourseHtml);
            }
        });
    }

});

$(document).on('change', '#c_image', function(e) {
    __uploading_file = e.currentTarget.files;
    if (__uploading_file.length > 1) {
        lauch_common_message('Multiple file not allowed', 'You are not allowed to upload more than one file.');
        return false;
    }
    var i = 0;
    var uploadURL = admin_url + "catalog_settings/upload_catalog_image_to_localserver";
    var fileObj = new processFileName(__uploading_file[i]['name']);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    param["id"] = catalog_id;

    uploadFiles(uploadURL, param, uploadCatalogImageCompleted);
});

function uploadCatalogImageCompleted(response) {
    var data = $.parseJSON(response);
    console.log(data);
    $('#catalog_image').attr('src', data['catalog_image'])
}

//Live Title update
var c_title = $('#c_title'),
    c_live_title = $('#c_live_title');

c_title.keyup(function(e) {
    c_live_title.text(c_title.val());
    if (c_title.val() === '') {
        c_live_title.text("AngularJS do with Dan Wahlin");
    }
});

// //Live character count update
// var c_description = 1000;
// $('#c_description_remain').html(c_description + ' Characters left');

// $('#c_description').keyup(function() {
//     var c_description_length = $('#c_description').val().length;
//     var c_description_remain = c_description - c_description_length;
//     $('#c_description_remain').html(c_description_remain + ' Characters left');
// });

//Get category list for Auto suggest
var current = '';
var timeOut = '';
$(document).on('keyup', '#c_category', function() {
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        var keyword = $('#c_category').val();
        var url = admin_url + 'catalog_settings/get_category_list';
        var tagHTML = '';
        current = this;
        if (keyword) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    'c_category': keyword,
                    'is_ajax': true
                },
                success: function(response) {
                    var data = $.parseJSON(response);
                    if (data['tags'].length > 0) {
                        for (var i = 0; i < data['tags'].length; i++) {
                            tagHTML += '<li id="' + data['tags'][i]['id'] + '">' + data['tags'][i]['ct_name'] + '</li>';
                        }
                    }
                    $("#listing_category").html(tagHTML).show();
                },
            });
        }
    }, 600);
});

var current_li = '';
$(document).on('click', '.auto-search-lister li', function() {
    $('#c_category').val($(this).text());
    $(this).parent().html('').hide();
});

c_price.keyup(function(e) {
    c_live_discount.hide();
    c_live_price.removeClass('strike-txt');
    c_live_price.text(c_price.val());
    if (c_price.val() === '') {
        c_live_price.text("$100");
        c_live_price.addClass('strike-txt');
        c_live_discount.text("$55");
        c_live_discount.show();
    }
});

c_discount.keyup(function(e) {
    c_live_discount.show();
    c_live_price.addClass('strike-txt');
    c_live_discount.text(c_discount.val());
    if (c_discount.val() === '') {
        c_live_discount.hide();
        c_live_price.removeClass('strike-txt');
        c_live_discount.text("$55");
    }
});

function courseTotal(total) {
    __course_total = total;
    $('#course_assigned_total').html(__course_total);
}

function courseCount(count) {
    __course_count = count;
    $("#catalog_live_courses").html(__course_count);
}

function addCoursesToCatalog() {
    if (catalog_course == 0) {
        $("#get_course_id").val('');
    } else {
        __courses_selected = $.parseJSON($("#get_course_id").val());
    }

    $.ajax({
        url: admin_url + 'catalog_settings/get_courses',
        type: "POST",
        data: {
            "is_ajax": true
        },
        success: function(response) {
            var coursesHtml = '';
            var data = $.parseJSON(response);
            var displayPrice = '';
            if (data['courses'].length > 0) {
                for (var i = 0; i < data['courses'].length; i++) {
                    if (data['courses'][i]['cb_is_free'] == 1) {
                        displayPrice = 'Free';
                    } else {
                        if ((data['courses'][i]['cb_discount'] == '') || (data['courses'][i]['cb_discount'] == 0)) {
                            displayPrice = data['courses'][i]['cb_price'];
                        } else {
                            displayPrice = data['courses'][i]['cb_discount'];
                        }
                    }
                    coursesHtml += '<div class="checkbox-wrap">';
                    coursesHtml += '    <label class="chk-box font14">';
                    coursesHtml += '        <input id="course_details_' + data['courses'][i]['id'] + '" class="course-checkbox" value="' + data['courses'][i]['id'] + '" type="checkbox"> ' + data['courses'][i]['cb_title'];
                    coursesHtml += '    </label>';
                    coursesHtml += '    <span class="email-label pull-right">' + displayPrice + '</span>';
                    coursesHtml += '</div>';
                }
            }
            $('#get_courses_list').html(coursesHtml);
            $(".course-checkbox").each(function() {
                if (inArray($(this).val(), __courses_selected)) {
                    $(this).prop("checked", true);
                }
            });
        }
    });
}

function deleteCourseCatalog(course_id, course_price) {
    $("#course_catalog_" + course_id).remove();
    if (course_price != "Free") {
        __course_total -= course_price;
    }
    __course_count -= 1;
    courseTotal(__course_total);
    courseCount(__course_count);
    removeArrayIndex(__courses_selected, course_id);
    var courses = __courses_selected;
    $("#get_course_id").val(JSON.stringify(courses))
}

$(document).on('click', '.course-checkbox', function() {
    var course_id = $(this).val();
    if ($(this).is(':checked')) {
        __courses_selected.push(course_id);
    } else {
        removeArrayIndex(__courses_selected, course_id);
    }
});

function removeArrayIndex(array, index) {
    for (var i = array.length; i--;) {
        if (array[i] === index) {
            array.splice(i, 1);
        }
    }
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle) return true;
    }
    return false;
}

function importCoursesToCatalog() {
    var courses = __courses_selected;
    $("#get_course_id").val(JSON.stringify(courses));
    $('#add-catalog').modal('hide');
    __course_total = 0;
    __courses_selected = $.parseJSON($("#get_course_id").val());
    __course_count = __courses_selected.length;
    courseCount(__course_count);

    $.ajax({
        url: admin_url + 'catalog_settings/get_course_details',
        type: "POST",
        data: {
            "is_ajax": true,
            "course": __courses_selected
        },
        success: function(response) {
            var rendercourseHtml = '';
            var data = $.parseJSON(response);
            var displayPrice = '';
            if (data['courses'].length > 0) {
                for (var i = 0; i < data['courses'].length; i++) {
                    if (data['courses'][i]['cb_is_free'] == 1) {
                        displayPrice = 'Free';
                    } else {
                        if ((data['courses'][i]['cb_discount'] == '') || (data['courses'][i]['cb_discount'] == 0)) {
                            displayPrice = data['courses'][i]['cb_price'];
                        } else {
                            displayPrice = data['courses'][i]['cb_discount'];
                        }
                        __course_total += Number(displayPrice);

                    }
                    rendercourseHtml += '<div class="rTableRow" id="course_catalog_' + data['courses'][i]['id'] + '">';
                    rendercourseHtml += '    <div class="rTableCell">';
                    rendercourseHtml += '        <span class="icon-wrap-round">';
                    rendercourseHtml += '            <i class="icon icon-graduation-cap"></i>';
                    rendercourseHtml += '        </span>';
                    rendercourseHtml += '        <a href="' + admin_url + 'course/basic/' + data['courses'][i]['id'] + '">' + data['courses'][i]['cb_title'] + '</a>';
                    rendercourseHtml += '    </div>';
                    rendercourseHtml += '    <div class="rTableCell pad0 txt-right">' + displayPrice + '</div>';
                    rendercourseHtml += '    <div class="rTableCell">';
                    rendercourseHtml += '        <a id="' + data['courses'][i]['id'] + '" onclick="deleteCourseCatalog(this.id,\'' + displayPrice + '\')"><i class="icon icon-cancel-1 delte"></i></a>';
                    rendercourseHtml += '    </div>';
                    rendercourseHtml += '</div>';
                }
            }
            courseTotal(__course_total);
            $('#course_assigned_catalog').html(rendercourseHtml);
        }
    });
}

function changeCatalogStatus(catalog_id, header_text, message) {
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content').html(message);
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({
        "catalog_id": catalog_id
    }, changeStatusConfirmed);
}

function changeStatusConfirmed(params) {
    $.ajax({
        url: admin_url + 'catalog_settings/change_status',
        type: "POST",
        data: {
            "catalog_id": params.data.catalog_id,
            "is_ajax": true
        },
        success: function(response) {
            var data = $.parseJSON(response);
            console.log(data);
            if (data['error'] == false) {
                //$('#action_class_'+params.data.catalog_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                //$('#label_class_'+params.data.catalog_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#catalog_settings_' + params.data.catalog_id).html(data['action_list']);
                $('#publish-course').modal('hide');
            } else {
                lauch_common_message('Error occured', data['message']);
                $('#publish-course').modal('hide');
                //$('#confirm_box_title').html(data['message']);
                //$('#confirm_box_content').html('');
            }
        }
    });
}

$("#c_is_free").change(function() {
    if ($(this).is(":checked")) {
        $("#c_price").attr('data-validation-optional', 'true');
    } else {
        $("#c_price").removeAttr('data-validation-optional');
    }
});