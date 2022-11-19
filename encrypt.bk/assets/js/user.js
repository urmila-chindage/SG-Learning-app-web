var __user_path = '';
var __img_format = new Array('jpg', 'jpeg', 'png', 'gif');
var __upload_path = '';
var __uploaded_files = '';
var timeOut = '';
$(document).on('keyup', '#user_keyword', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getUsers();
    }, 600);
});

var __userObject = {};
$(document).ready(function () {

    var filter = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');
    var institute_id = getQueryStringValue('institute_id');
    var branch_id = getQueryStringValue('branch_id');
    var batch_id = getQueryStringValue('batch_id');
    var userAdd    = getQueryStringValue('add');
    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#user_keyword').val(keyword);
    }
    if (institute_id != '') {
        __institute_id = institute_id;
        var institude_code = $('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-'));
        if (institude_code == '') {
            $('#filter_institute').html('All Institutes <span class="caret"></span>');
            __institute_id = '';
            var messageObject = {
                'body': 'Institute does not exists, Click OK to load from all institutes',
                'button_yes': 'OK',
                'prevent_button_no': true
            };
            callback_warning_modal(messageObject, refreshListingLaunch);
        } else {
            $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
        }
    }
    if (branch_id != '') {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring(0, $('#filter_branch_' + branch_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    if (batch_id != '') {
        __batch_id = batch_id;
        $('#filter_batch').html('<span class="dropdown-filter" title="' + $('#filter_batch_' + batch_id).text() + '">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret batch-carrot"></span>');
    }
    if(userAdd == 'true') {
        $("#add_new_users").trigger("click");
    }

    $('#create_new_group_cancel, #group_name').hide();

    // $('.profile-pic').initial({
    //     width: 40,
    //     height: 40,
    //     fontSize: 20,
    //     fontWeight: 400
    // });
    // $('#user_row_wrapper').bind('DOMSubtreeModified', function (e) {
    //     $('.profile-pic').initial({
    //         width: 40,
    //         height: 40,
    //         fontSize: 20,
    //         fontWeight: 400
    //     });
    // });

    // $('#filter_batch_div').css('display', 'none');
    var users = {};
    __userObject = $.parseJSON(__users);
    users.users = $.parseJSON(__users);
    if (users.users.length > 0) {
        $('#user_row_wrapper').html(renderUserHtml(JSON.stringify(users)));
    } else {
        $('.user-count').html("No Students");
        $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Students found.'));
        $('#popUpMessagePage .close').css('display', 'none');
    }

    setBulkAction();
    // console.log('values called');
});

$(document).on('change', '#import_user', function (e) {
    //console.log(e.currentTarget.files[0]);
    $('#percentage_bar').hide();
    __uploaded_files = e.currentTarget.files[0];
    __uploaded_files.extension = __uploaded_files.name.split('.').pop();
    $('#upload_user_file').val(__uploaded_files.name);
});

function uploadUser() {
    if (__uploaded_files == '') {
        lauch_common_message('File missing', 'Please choose file to upload.');
        return false;
    }
    if ($('#student_institute_upload').val() == 0) {
        lauch_common_message('Choose Institute', 'Please choose any institute.');
        return false;
    }

    var filename = __uploaded_files.name;
    var valid_extensions = /(\.csv)$/i;

    if (valid_extensions.test(filename)) {
        $('#percentage_bar').show();
        var uploadURL = admin_url + 'user/import_users'

        var fileObj = new processFileName(__uploaded_files.name);
        var param = new Array;
        param["file_name"] = fileObj.uniqueFileName();
        param["extension"] = fileObj.fileExtension();
        param["file"] = __uploaded_files;
        param.institute_id = $('#student_institute_upload').val();
        param.processing = 'importing_user_process';
        uploadFiles(uploadURL, param, uploadUserCompleted);
    } else {
        lauch_common_message('Invalid File', 'Choose proper file to upload');
        return false;
    }
}

$('#addusers').on('hidden.bs.modal', () =>  {

    __uploaded_files = '';
    $('#upload_user_file').val('');
    $('#student_institute_upload').val('');
    $('#percentage_bar').hide();
    $('#popUpMessage').hide();
});



function uploadUserCompleted(response) {
    var response = $.parseJSON(response);
    // console.log(response);
    __uploaded_files = '';
    switch (response.status) {
        case 1:
            $('#addusers .modal-body').prepend(renderPopUpMessage('success', response.message));
            setTimeout(function () {
                window.location.reload();
            }, 2500);
            break;
        case 2:
            $('#addusers .modal-body').prepend(renderPopUpMessage('error', response.message));
            setTimeout(function () {
                location.href = response.redirect_url;
            }, 500);
            /*$(window).on('beforeunload', function(){
                return 'Please allow system to refresh the page inorder to get inserted contents.';
            });*/
            break;
        case 3:
            $('#addusers .modal-body').prepend(renderPopUpMessage('error', response.message));
            break;
    }
    $('#percentage_bar').hide();
    scrollToTopOfPage();

}


$(document).on('change', '#us_image', function (e) {
    __uploading_file = e.currentTarget.files;
    if (__uploading_file.length > 1) {
        lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
        return false;
    }
    var i = 0;
    var uploadURL = admin_url + "user/save_user_image";
    var fileObj = new processFileName(__uploading_file[i].name);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    param["id"] = user_id;
    uploadFiles(uploadURL, param, uploadUserImageCompleted);
});


function uploadUserImageCompleted(response) {
    var data = $.parseJSON(response);
    $('#user_image').attr('src', data.user_image)
}

$(document).on('click', '#basic-addon2', () =>  {
    var user_keyword = $('#user_keyword').val().trim();
    if (user_keyword == '') {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else {
        __offset = 1;
        getUsers();
    }
});



$(document).on('click', '#add_new_users', () =>  {
    cleanPopUpMessage();
    $('#student_name').val('');
    $('#student_email').val('');
    $('#phone_number').val('');
    $('#student_password').val('');
    // $('#student_institute').val('');
    var institute_div = $('#student_institute').parent().attr('style');
    if (institute_div != 'display:none;') {
        $('#student_institute').val('');
    }
    $('#student_branch').val('');
    $('#send_mail').prop('checked', false);
});

function extendValidityForCourse(course_id, course_title, course_validity, validity_date) {
    var htmlCourseValidity = '';

    if (course_validity > 0) {
        htmlCourseValidity += '<b>' + course_title + '</b> ' + lang('extend_validity_for_course') + validity_date + '. Extend validity if needed<br />';
        htmlCourseValidity += '<input type="text" placeholder="" class="form-control" id="course_validity_date">';
    } else {
        htmlCourseValidity += '<b>' + course_title + '</b> ' + lang('extend_validity_expired_for_course') + '<br />';
        htmlCourseValidity += '<input type="text" placeholder="" class="form-control" id="course_validity_date">';
    }
    $('#modal_extend_validity').html(htmlCourseValidity);
    var today = new Date();
    $("#course_validity_date").datepicker({
        language: 'en',
        minDate: today // Now can select only dates, which goes after today
    });
    $('#update_extended_validity').unbind();
    $('#update_extended_validity').click({
        "user_id": user_id,
        "course_title": course_title,
        "course_id": course_id
    }, extendValidityForCourseConfirmed);
}

function extendValidityForCourseConfirmed(params) {
    var updated_validity = $('#course_validity_date').val();

    $.ajax({
        url: admin_url + 'user/send_extend_validity',
        type: "POST",
        data: {
            "is_ajax": true,
            "updated_validity": updated_validity,
            "user_name": user_name,
            "course_title": params.data.course_title,
            "user_id": params.data.user_id,
            "user_email": user_email,
            "course_id": params.data.course_id
        },
        success: function (response) {
            var data = $.parseJSON(response);
            //$('#subscription_status_'+params.data.course_id).removeClass('prfle-expireRed').removeClass('prfle-expire').addClass('prfle-expire').html(data.date_of_expiry);
            location.reload();
            //$('#extend-validity').modal('hide');
        }
    });
}

function setAsComplete(user_id, course_id) {
    var user_name = $('#user_course_' + user_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_set_complete_course'));
    $('#confirm_box_content_course').html(lang('confirm_set_complete_for_user_course') + '<b>' + user_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "user_id": user_id,
        "course_id": course_id
    }, setAsCompleteConfirmed);
}

function setAsCompleteConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/set_as_complete',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "course_id": params.data.course_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                $('#publish-course').modal('hide');
                $('#progress_bar_user_' + params.data.user_id + ' .link-style').html('100%');
                $('#progress_bar_user_' + params.data.user_id + ' .progress-bar').css('width', '100%');
                $('#progress_bar_user_' + params.data.user_id + ' .sr-only').html('100% ' + lang('complete'));
            } else {
                $('#confirm_box_title').html(data.message);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function changeSubscriptionStatus(user_id, course_id, status) {
    var actionLabel = 'approval';
    var actionMessage = 'message_approve_user';
    var user_name = $('#user_course_' + user_id).attr('data-name');
    if (status == 0) {
        actionLabel = 'suspend';
        actionMessage = 'message_suspend_user';
    }
    $('#confirm_box_title_course').html(lang('confirm') + ' ' + lang(actionLabel));
    $('#confirm_box_content_course').html(lang(actionMessage) + ' <b>' + user_name + '</b>. ');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "user_id": user_id,
        "course_id": course_id,
        "status": status
    }, changeSubscriptionStatusConfirmed);
}

function changeSubscriptionStatusConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/change_subscription_status',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "course_id": params.data.course_id,
            "status": params.data.status,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                var action_list = '';
                if (params.data.status == 0) {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + params.data.user_id + '\', \'' + params.data.course_id + '\', \'1\' )" href="javascript:void(0);">' + lang('approve') + '</a>';
                } else {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + params.data.user_id + '\', \'' + params.data.course_id + '\', \'0\' )" href="javascript:void(0);">' + lang('suspend') + '</a>';
                }
                $('#status_btn_' + params.data.user_id).html(action_list);
                $('#publish-course').modal('hide');
            } else {
                $('#confirm_box_title_course').html(data.message);
                $('#confirm_box_content_course').html('');
            }
        }
    });
}

function resetResult(user_id, course_id) {
    var user_name = $('#user_course_' + user_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_reset_result_course'));
    $('#confirm_box_content_course').html(lang('confirm_reset_result_for_user_course') + '<b>' + user_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "user_id": user_id,
        "course_id": course_id
    }, resetResultConfirmed);
}

function resetResultConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/reset_result',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "course_id": params.data.course_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                $('#publish-course').modal('hide');
                $('#progress_bar_user_' + params.data.user_id + ' .link-style').html('0%');
                $('#progress_bar_user_' + params.data.user_id + ' .progress-bar').css('width', '0%');
                $('#progress_bar_user_' + params.data.user_id + ' .sr-only').html('0% ' + lang('complete'));
            } else {
                $('#confirm_box_title_course').html(data.message);
                $('#confirm_box_content_course').html('');
            }
        }
    });
}

function removeUserFromCourse(user_id, course_id) {
    var user_name = $('#user_course_' + user_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_delete_subscription'));
    $('#confirm_box_content_course').html(lang('confirm_delete_subscription_for_user') + '<b>' + user_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "user_id": user_id,
        "course_id": course_id
    }, removeUserFromCourseConfirmed);
}

function removeUserFromCourseConfirmed(params) {
    var userId = params.data.user_id;
    $.ajax({
        url: admin_url + 'course/delete_subscription',
        type: "POST",
        data: {
            "user_id": userId,
            "course_id": params.data.course_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                $('#user_row_' + userId).remove();
                $('#publish-course').modal('hide');
            } else {
                $('#confirm_box_title_course').html(data.message);
                $('#confirm_box_content_course').html('');
            }
        }
    });
}

var __user_selected = new Array();


var __course_selected = new Array();
$(document).on('click', '.user-checkbox', function()  {
    var user_id = $(this).val();
    if ($('.user-checkbox:checked').length == $('.user-checkbox').length) {
        $('.user-checkbox-parent').prop('checked', true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __user_selected.push(user_id);
    } else {
        $('.user-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__user_selected, user_id);
    }
    if (__user_selected.length > 1) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }
});

$(document).on('click', '.user-checkbox-parent', function() {
    var parent_check_box = this;
    __user_selected = new Array();
    $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').not(':disabled').each(function (index) {
            __user_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__user_selected.length > 1) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
        $("#user_bulk").css('display', 'block');
    } else {
        $("#selected_user_count").html('');
        $("#user_bulk").css('display', 'none');
    }
});




var __filter_dropdown = 'active';
var __course_id = '';
var __institute_id = '';
var __branch_id = '';
var __batch_id = '';
var __gettingUserInProgress = false;


var __totalUsers = 0;
var __shownUsers = 0;

function getUsers() {
    if (__gettingUserInProgress == true) {
        return false;
    }
    __gettingUserInProgress = true;
    //$('#loadmorebutton').html('Loading..');
    var keyword = $('#user_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || __institute_id != '' || __branch_id != '' || __batch_id != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__institute_id != '') {
            link += '&institute_id=' + __institute_id;
        }
        if (__branch_id != '') {
            link += '&branch_id=' + __branch_id;
        }
        if (__batch_id != '') {
            link += '&batch_id=' + __batch_id;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url + 'user/users_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "filter": __filter_dropdown,
            "institute_id": __institute_id,
            "branch_id": __branch_id,
            "batch_id": __batch_id,
            "keyword": keyword,
            'limit': __limit,
            'offset': __offset
        },
        success: function (response) {
            $('.user-checkbox-parent').prop('checked', false);
            // __user_selected = new Array();

            var data = $.parseJSON(response);


            // console.log(data);
            var remainingUser = 0;
            //$('#loadmorebutton').hide();
            clearUserCache();
            renderPagination(__offset, data.total_users);
            if (data.users.length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data.total_users;
                    __shownUsers = data.users.length;
                    remainingUser = (data.total_users - data.users.length);
                    var totalUsersHtml = data.total_users + ' ' + ((data.total_users == 1) ? "Student" : "Students"); //data.users.length + ' / ' + data.total_users + ' ' + ((data.total_users == 1) ? "Student" : "Students");
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                } else {
                    __totalUsers = data.total_users;
                    __shownUsers = ((__offset - 2) * data.limit) + data.users.length;
                    remainingUser = (data.total_users - (((__offset - 2) * data.limit) + data.users.length));
                    var totalUsersHtml = data.total_users + ' Students'; //(((__offset - 2) * data.limit) + data.users.length) + ' / ' + data.total_users + ' Students';
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderUserHtml(response));
                }
            } else {
                $('.user-count').html("No Students");
                $('#user_row_wrapper').html(renderPopUpMessagePage('error', 'No Students found.'));
                $('#popUpMessagePage .close').css('display', 'none');
            }
            if (data.show_load_button == true) {
                //$('#loadmorebutton').show();
            }
            if (data.batches.length > 0) {
                $('#filter_batch_div').attr('style', '');
                var batchHtml = '<li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch(\'all\')">All Batches </a></li>';
                for (var i in data.batches) {
                    var batchNameToolTip = '';
                    if (data.batches[i].batch_name.length > 15) {
                        batchNameToolTip = 'data-toggle="tooltip" title="' + data.batches[i].batch_name + '"';
                    }
                    batchHtml += '<li><a href="javascript:void(0)" id="filter_batch_' + data.batches[i].id + '" onclick="filter_batch(' + data.batches[i].id + ')" ' + batchNameToolTip + '>' + data.batches[i].batch_name + '</a></li>';
                }
                $('#batch_filter_list').html(batchHtml);
                if (__batch_id == '') {
                    $('#filter_batch').html('All Batches <span class="batch-carrot caret"></span>');
                }
            } else {
                $('#filter_batch_div').css('display', 'none');
            }
            remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
            //$('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
            __gettingUserInProgress = false;
        }
    });
}

function loadMoreUsers() {
    getUsers();
}

function renderUserHtml(response) {
    // $("#selected_user_count").html('');
    var data = $.parseJSON(response);
    // console.log(data);
    var userHtml = '';
    if (data.users.length > 0) {
        for (var i = 0; i < data.users.length; i++) {
            userHtml += '<div class="rTableRow user-listing-row" id="user_row_' + data.users[i].id + '" data-name="' + data.users[i].us_name + '" data-email="' + data.users[i].us_email + '">';

            userHtml += renderUserRow(data.users[i]);

            userHtml += '</div>';
        }
    }
    return userHtml;
}

function renderUserRow(data) {
    //console.log(data);
    var userHtml = '';
    if (data) {
        userHtml += '    <div class="rTableCell"> <label class="manage-stud-list" for="user_details_' + data.id + '"> <span class="list-user-name text-blue">';
        if (data.us_deleted == 0 || __filter_dropdown == "deleted") {
            userHtml += '        <input type="checkbox" class="user-checkbox" value="' + data.id + '" id="user_details_' + data.id + '"> ';
        } else {
            userHtml += '        <input type="checkbox" class="user-checkbox item-deleted" value="' + data.id + '" id="user_details_' + data.id + '" disabled="disabled"> ';
        }

        // userHtml += ' <span style="display: inline-block; vertical-align: middle;  margin-right: 10px;"><img class="profile-pic media-object pull-left img-circle" data-name="' + data.us_name + '"></span>';
        //  userHtml += '        <span class="wrap-mail ellipsis-hidden manage-stud-listwrapper"> ';
        // userHtml += '            <div class="ellipsis-style">';
        userHtml += '  <svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="16px" height="18px" viewBox="0 0 16 18" enable-background="new 0 0 16 18" fill="#64277d" xml:space="preserve"><g> <path d="M8,1.54v0.5c1.293,0.002,2.339,1.048,2.341,2.343C10.339,5.675,9.293,6.721,8,6.724C6.707,6.721,5.66,5.675,5.657,4.382 C5.66,3.088,6.707,2.042,8,2.04V1.54v-0.5c-1.846,0-3.342,1.496-3.342,3.343c0,1.845,1.497,3.341,3.342,3.341 c1.846,0,3.341-1.496,3.341-3.341C11.341,2.536,9.846,1.04,8,1.04V1.54z"/> <path d="M2.104,16.46c0-1.629,0.659-3.1,1.727-4.168C4.899,11.225,6.37,10.565,8,10.565s3.1,0.659,4.168,1.727 c1.067,1.068,1.727,2.539,1.727,4.168h1c0-3.808-3.087-6.894-6.895-6.895c-3.808,0-6.895,3.087-6.895,6.895H2.104z"/></g></svg>' + data.us_name + '</span><span class="list-institute-code  text-right" data-toggle="tooltip" data-placement="top" title="' + data.us_email + '">' + data.us_email + ' </span><span class="list-register-number text-right">' + data.us_phone + ' </span> </label>';
        // userHtml += '            </div>';
        // userHtml += '        </span>';
        userHtml += '    </div>';

        //consider the record is deleted and set the value if record deleted
        var label_class = 'spn-delete';
        var action_class = 'label-danger';
        var item_deleted = 'item-deleted';
        var action = lang('deleted');
        //case if record is not deleted
        if (data.us_deleted == 0) {
            item_deleted = '';
            if (data.us_status == 1) {
                action_class = 'label-success';
                label_class = 'spn-active';
                action = lang('active');
            } else if (data.us_status == 2) {
                action_class = 'label-warning';
                label_class = 'spn-inactive';
                action = 'Waiting Approval';
            } else {
                action_class = 'label-warning';
                label_class = 'spn-inactive';
                action = lang('inactive');
            }
        }

        userHtml += '    <div class="rTableCell pad0">';
        userHtml += '        <div class="col-sm-12 pad0">';
        userHtml += '            <label class="pull-right label ' + action_class + '" id="action_class_' + data.id + '">';
        userHtml += action;
        userHtml += '            </label>';
        userHtml += '        </div>';
        userHtml += '    </div>';

        userHtml += '    <div class="td-dropdown rTableCell" style="padding-bottom: 3px !important;">';
        var viewMailPrivilege   = false;
        var viewEditPrivilege   = false;
        var viewDeletePrivilege = false;
        var studentEnroll       = false;
        var batchEnroll         = false;
        var dropMenu            = false;
        if(data.us_email != '' && __user_privilege.view() == true){

            viewMailPrivilege       = true;
            dropMenu                = true;
        }
        if(__user_privilege.view() == true && __user_privilege.edit() == true){

            viewEditPrivilege       = true;
            dropMenu                = true;
        }
        if(__user_privilege.view() == true && __user_privilege.pdelete() == true){

            viewDeletePrivilege     = true;
            dropMenu                = true;
        }
        if(__studentEnrollPrivilege.view() == true && __studentEnrollPrivilege.add() == true){
            studentEnroll           = true;
            dropMenu                = true;
        }
        if (__batchEnrollPrivilege.view() == true && __batchEnrollPrivilege.add() == true) {
            batchEnroll             = true;
            dropMenu                = true;
        }
        if (dropMenu == true) {
            userHtml += '        <div class="btn-group lecture-control">';
            userHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            userHtml += '                <span class="label-text">';
            userHtml += '                  <i class="icon icon-down-arrow"></i>';
            userHtml += '                </span>';
            userHtml += '                <span class="tilder"></span>';
            userHtml += '            </span>';
            userHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="user_action_' + data.id + '">';
            if (data.us_deleted == 0) {
                //userHtml += '                    <li id="status_btn_'+data.id+'">';
                var cb_status = '';
                switch (+data.us_status) {
                    case 1:
                        cb_status = 'deactivate';
                        break;
                    case 2:
                        cb_status = 'approve';
                        break;
                    case 3:
                        cb_status = 'activate';
                        break;
                    default:
                        cb_status = 'activate';
                        break;
                }

                var cb_action = cb_status;


                if (cb_status != 'approve') {
                    if (viewEditPrivilege == true) {

                        userHtml += '                <li>';
                        userHtml += '                     <a href="' + admin_url + 'user/profile/' + data.id + '?v=1">View Profile</a>';
                        userHtml += '                </li>';
                        if (data.us_email != '') {
                            userHtml += '                <li>';
                            userHtml += '                     <a href="javascript:void(0);" onclick="resetPassword(\'' + data.id + '\',\'' + data.us_name + '\')">' + lang('reset_password') + '</a>';
                            userHtml += '                </li>';
                        }
                    }
                }
                if (viewMailPrivilege == true) {
                    userHtml += ' <li>';
                    userHtml += ' <a href="javascript:void(0);" onclick="sendMessageToUser(' + data.id + ')">' + lang('send_message') + '</a>';
                    userHtml += ' </li>';
                }
                if (viewDeletePrivilege == true) {

                    userHtml += '                <li>';
                    userHtml += '                       <a href="javascript:void(0);" id="delete_btn_' + data.id + '" onclick="deleteUser(\'' + data.id + '\',\'' + data.us_name + '\')" >' + lang('delete') + '</a>';
                    userHtml += '                </li>';
                }
                if (cb_status != 'approve') {
                    if (studentEnroll == true) {

                        userHtml += '                <li>';
                        userHtml += '                    <a href="javascript:void(0);" onclick="addUserToCourse(\'' + data.id + '\', \'' + data.us_name + '\')">' + lang('add_to_course') + '</a>';
                        userHtml += '                </li>';
                    }
                    if (batchEnroll == true) {
                        userHtml += '                <li>';
                        userHtml += '                    <a href="javascript:void(0);" onclick="addToGroup(\'' + data.id + '\', \'' + data.us_name + '\', \'' + data.us_email + '\')">' + lang('add_to_batch') + '</a>';
                        userHtml += '                </li>';
                    }

                }
                if (viewEditPrivilege == true) {
                    userHtml += '                <li id="status_btn_' + data.id + '">';
                    userHtml += '                        <a href="javascript:void(0);" onclick="changeUserStatus(\'' + data.id + '\',\'' + cb_action + '\', \'' + data.us_name + '\')" >' + lang(cb_status) + '</a>';
                    userHtml += '                </li>';
                }
            } else {
                if (viewDeletePrivilege == true) {
                    userHtml += '                    <li>';
                    userHtml += '                        <a href="javascript:void(0);" id="restore_btn_' + data.id + '" onclick="restoreUser(\'' + data.id + '\', \'' + data.us_name + '\')" >' + lang('restore') + '</a>';
                    userHtml += '                    </li>';
                }
            }
            userHtml += '           </ul>';
            userHtml += '        </div>';
        }
        userHtml += '    </div>';
    }
    return userHtml;
}


function filter_institute(institute_id) {
    if (institute_id == 'all') {
        __institute_id = '';
        $('#filter_institute').html('All Institutes <span class="caret"></span>');
    } else {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    __batch_id = '';
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
}



function filter_branch(branch_id) {
    if (branch_id == 'all') {
        __branch_id = '';
        $('#filter_branch').html('All Branches <span class="caret batch-carrot"></span>');
    } else {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring(0, $('#filter_branch_' + branch_id).text().indexOf('-')) + '<span class="caret batch-carrot"></span>');
    }
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
}

function filter_batch(batch_id) {
    if (batch_id == 'all') {
        __batch_id = '';
        $('#filter_batch').html('All Batches <span class="caret batch-carrot"></span>');
    } else {
        __batch_id = batch_id;
        $('#filter_batch').html('<span class="dropdown-filter" title="' + $('#filter_batch_' + batch_id).text() + '">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret batch-carrot"></span>');
    }
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
}

function sendMessageToUser(user_id) {
    var user_id_temp = (typeof user_id != 'undefined') ? user_id : '';
    $('#invite-user-bulk').modal();
    $('#popUpMessage').hide();
    $('#invite_send_subject').val('');
    // $('#redactor_invite').redactor('set', '');
    $('#redactor_invite').redactor('insertion.set', '');
    // $('#redactor_invite').redactor('core.destroy');
    startTextToolbar();
    $('#message_send_button').attr('onclick', 'sendMessageBulk(' + user_id_temp + ')');
}

function sendMessageBulk(user_id_temp) {
    var user_id = (typeof user_id_temp != 'undefined') ? user_id_temp : '';

    var send_user_bulk_subject = $('#invite_send_subject').val();
    var send_user_bulk_message = btoa($('#redactor_invite').val());

    var errorCount = 0;
    var errorMessage = '';

    var user_ids = [];
    if (__user_selected.length > 0) {
        user_ids = __user_selected;
    } else {
        if (user_id != '' && user_id > 0) {
            user_ids.push(user_id);
        } else {
            errorCount++;
            errorMessage += 'Email id cannot be empty<br />';
        }
    }
    // console.log(user_ids);
    if ($.trim(send_user_bulk_subject) == '') {
        errorCount++;
        errorMessage += 'Please enter subject<br />';
    }
    if ($.trim(send_user_bulk_message) == '') {
        errorCount++;
        errorMessage += 'Please enter message<br />';
    }

    if (errorCount > 0) {
        $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    $('#message_send_button').text('SENDING..');
    $.ajax({
        url: admin_url + 'user/send_message',
        type: "POST",
        data: {
            "is_ajax": true,
            "send_user_subject": send_user_bulk_subject,
            "send_user_message": send_user_bulk_message,
            "user_ids": JSON.stringify(user_ids)
        },
        success: function (response) {
            // clearCache();
            var data = $.parseJSON(response);
            if (data.error == false || data.success == true) {
                // $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('success', data.message));                    
                $('#invite-user-bulk').modal('hide');
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            } else {
                $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data.message));
            }
            $('#message_send_button').text('SEND');

            setTimeout(function () {
                // $('#faculty_wrapper .lecture-control').css('visibility', 'visible');
                $('#invite-user-bulk').modal('hide');
            }, 2500);
        }
    });
}

function filter_user_by(filter) {
    if (filter == 'all') {
        $('#user_keyword').val('');
        __institute_id = '';
        $('#filter_institute').html('All Institutes <span class="caret"></span>');
        __branch_id = '';
        $('#filter_branch').html('All Branches <span class="caret batch-carrot"></span>');
    }
    // if (filter == 'deleted') {
    //     $('.select-all-style').hide();
    // } else {
    //     $('.select-all-style').show();
    // }
    __filter_dropdown = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    __batch_id = '';
    __offset = 1;
    getUsers();
    $("#selected_user_count").html('');
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
    setBulkAction();
}

function setBulkAction() {
    var user_bulk = '';
    var bulk_activate = (__filter_dropdown == "not-approved") ? 'Approve' : 'Activate';
    user_bulk += '<span class="dropdown-tigger" data-toggle="dropdown">';
    user_bulk += '  <span class="label-text">';
    user_bulk += '  Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->';
    user_bulk += '  </span>';
    user_bulk += '  <span class="tilder"></span>';
    user_bulk += '</span>';
    user_bulk += '<ul class="dropdown-menu pull-right" role="menu">';

    if (__filter_dropdown == "deleted" && __user_privilege.pdelete() == true) {
        user_bulk += '                    <li>';
        user_bulk += '                        <a href="javascript:void(0);" onclick="restoreUserBulk()" >' + lang('restore') + '</a>';
        user_bulk += '                    </li>';
    } else {
        if (__user_privilege.view() == true) {
            // user_bulk   += '     <li>';
            // user_bulk   += '         <a href="javascript:void(0)" id="invite_user_message">Send Message</a> ';
            // user_bulk   += '     </li>';
            user_bulk += ' <li>';
            user_bulk += ' <a href="javascript:void(0);" onclick="sendMessageToUser()">' + lang('send_message') + '</a>';
            user_bulk += ' </li>';
        }

        if (__filter_dropdown != "deleted" && __user_privilege.pdelete() == true) {
            user_bulk += '     <li>';
            user_bulk += '         <a href="javascript:void(0)" onclick="deleteUserBulk()" > Delete </a>';
            user_bulk += '     </li>';
        }
        if (__filter_dropdown != "active" && __user_privilege.edit() == true) {
            var act_status = (bulk_activate.toLowerCase() == 'approve') ? 2 : 1;
            user_bulk += '     <li>';
            user_bulk += '         <a href="javascript:void(0)" onclick="changeUserStatusBulk(\'Are you sure to ' + bulk_activate + ' the selected students\', \'' + act_status + '\')" > ' + bulk_activate + ' </a>';
            user_bulk += '     </li>';
        }
        if (__filter_dropdown != "inactive" && __filter_dropdown != "not-approved" && __user_privilege.edit() == true) {
            user_bulk += '     <li>';
            user_bulk += '         <a href="javascript:void(0)" onclick="changeUserStatusBulk(\'Are you sure to Deactivate the selected students\', \'0\')" > Deactivate </a>';
            user_bulk += '     </li>';
        }
    }
    user_bulk += '</ul>';
    $('#user_bulk').html(user_bulk);
}

$(document).on('click', '#searchclear', () =>  {
    __user_selected = new Array();
    $("#user_bulk").css('display', 'none');
    __offset = 1;
    getUsers();
});

function changeUserStatus(user_id, action, user_name) {
    var ok_button_text = 'ACTIVATE';
    switch (action) {
        case "deactivate":
            ok_button_text = 'DEACTIVATE';
            break;
        case "approve":
            ok_button_text = 'APPROVE';
            break;
    }
    var header_text = 'Are you sure to ' + action + ' the student named "' + user_name + '" ?';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'user_id': user_id
        },
    };
    callback_warning_modal(messageObject, changeStatusConfirmed);
}

function changeStatusConfirmed(params) {
    var user_id = params.data.user_id;
    $.ajax({
        url: admin_url + 'user/change_status',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown == 'all') {

                    $('#user_row_' + user_id).html(renderUserRow(data.user));
                } else {
                    $('#user_row_' + user_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Students';
                    } else {
                        var totalUsersHtml = __totalUsers + ' Students'; //__shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.user-count').html(totalUsersHtml);
                }

                var messageObject = {
                    'body': 'Student status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}


function restoreUserBulk() {
    var messageObject = {
        'body': 'Are you sure to <b>Restore</b> selected students?',
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
    };
    callback_warning_modal(messageObject, restoreUserBulkConfirmed);
}

function changeUserStatusBulk(header_text, status) {
    var action = 'activate';
    var ok_button_text = 'ACTIVATE';

    var bulk_activate = (__filter_dropdown == "not-approved") ? 'Approve' : 'Activate';
    var act_status = (bulk_activate.toLowerCase() == 'approve') ? 2 : 1;

    var approve = 0;
    if (status == 0) {
        action = 'deactivate';
        ok_button_text = 'DEACTIVATE';
    }

    if (status == 2) {
        status = 1;
        action = 'Approve';
        ok_button_text = 'APPROVE';
        approve = 1;
    }

    if (header_text == 'Restore') {
        action = lang('restore');
        ok_button_text = lang('RESTORE');
    }
    if (header_text == '') {
        header_text = 'Are you sure to ' + action + ' the selected students ?';
    }

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status,
            'current': act_status,
            'approve': approve
        },
    };
    callback_warning_modal(messageObject, ChangeStatusBulkConfirmed);
}

function ChangeStatusBulkConfirmed(params) {
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'user/change_status_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__user_selected),
            "status": params.data.status,
            "approve": params.data.approve,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.users.length > 0) {
                for (var i in data.users) {
                    if (__filter_dropdown == 'all') {
                        $('#user_row_' + data.users[i].id).html(renderUserRow(data.users[i]));
                    } else {
                        $('#user_row_' + data.users[i].id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Students';
                        } else {
                            var totalUsersHtml = __totalUsers + ' Students'; //__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __user_selected = new Array();
            $("#user_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Students status changed successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_user_count").html('');
            $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
            refreshListing();
        }
    });
}

function restoreUser(user_id, user_name) {
    var messageObject = {
        'body': 'Are you sure to restore the student "' + user_name + '"?',
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
        'continue_params': {
            'user_id': user_id
        },
    };
    callback_warning_modal(messageObject, restoreUserConfirmed);
}

function restoreUserConfirmed(params) {
    var user_id = params.data.user_id;
    $.ajax({
        url: admin_url + 'user/restore',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {

                if (__filter_dropdown == 'all') {
                    $('#user_row_' + user_id).html(renderUserRow(data.user));
                } else {
                    $('#user_row_' + user_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Students';
                    } else {
                        var totalUsersHtml = __totalUsers + ' Students'; //__shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.user-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Student restored successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function deleteUser(user_id, user_name) {

    var messageObject = {
        'body': 'Are you sure to delete the student "' + user_name + '"?',
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'user_id': user_id
        },
    };
    callback_warning_modal(messageObject, deleteUserConfirmed);
}

function deleteUserConfirmed(params) {
    var user_id = params.data.user_id;
    $.ajax({
        url: admin_url + 'user/delete',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown == 'all') {
                    $('#user_row_' + user_id).html(renderUserRow(data.user));
                } else {
                    $('#user_row_' + user_id).remove();
                    __totalUsers = __totalUsers - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalUsers == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Students';
                    } else {
                        var totalUsersHtml = __totalUsers + ' Students'; //var totalUsersHtml = __shownUsers + ' / ' + __totalUsers + ' Students';
                    }
                    $('.user-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Student deleted successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}


function deleteUserBulk() {
    var header_text = 'Are you sure to delete the selected students ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, deleteUserBulkConfirmed);
}

function deleteUserBulkConfirmed(params) {
    $.ajax({
        url: admin_url + 'user/delete_user_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__user_selected),
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.users.length > 0) {
                for (var i in data.users) {
                    if (__filter_dropdown == 'all') {
                        $('#user_row_' + data.users[i].id).html(renderUserRow(data.users[i]));
                    } else {
                        $('#user_row_' + data.users[i].id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Students';
                        } else {
                            var totalUsersHtml = __totalUsers + ' Students'; //__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __user_selected = new Array();
            $("#user_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Students deleted successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_user_count").html('');
            $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
            refreshListing();
        }
    });
}


function addUserToCourse(user_id, username) {
    username = typeof username != 'undefined' ? username : '';

    var courseHtml = '';
    $('#course_list_wrapper').html(courseHtml);
    $('#add-users-course').modal('show');
    $.ajax({
        url: admin_url + 'course/course_new_json',
        type: "POST",
        data: {
            "is_ajax": true,
            'user_id': user_id,
            'not_deleted' : true
        },
        beforeSend: function () {
            $('#course_list_wrapper').html('<p>Loading...</p>');
        },
        success: function (response) {
            if (response != null) {

                var data = $.parseJSON(response);
                if (data.success == true) {


                    if (data.courses.length > 0) {
                        console.log(data.courses);
                        for (var i = 0; i < data.courses.length; i++) {

                            courseHtml += '<div class="active-list-padding" id="user_course_' + data.courses[i].id + '">';
                            courseHtml += '    <span class="chk-box">';
                            courseHtml += '        <label class="font14">';
                            courseHtml += '            <input type="checkbox" class="user-course" value="' + data.courses[i].id + '"> ' + data.courses[i].cb_title;
                            courseHtml += '        </label>';
                            courseHtml += '    </span>';
                            courseHtml += '    <span class="email-label pull-right">';

                            if (data.courses[i].cb_status == '1') {
                                courseHtml += '<div class="col-sm-12 pad0"><label class="pull-right label label-success" id="action_class_147">Active</label></div>';
                            } else {
                                courseHtml += '<div class="col-sm-12 pad0"><label class="pull-right label label-warning" id="action_class_157">Inactive</label></div>';
                            }
                            courseHtml += '    </span>';
                            courseHtml += '</div>';
                        }
                        $('#course_list_wrapper').html(courseHtml);
                        $('#coursetext').html('Select Courses for <b>' + username + '</b>');
                        $('#add_user_ok').unbind();
                        $('#add_user_ok').click({
                            "user_id": user_id,
                            "username": username
                        }, addUserToCourseConfirmed);
                    }
                } else {
                    errorHtml = '<p>No Course to Display</p>'
                    $('#course_list_wrapper').html(errorHtml);
                }

            } else {
                errorHtml = '<p>No Course to Display</p>'
                $('#course_list_wrapper').html(errorHtml);
            }


        }
    });
}

$(document).on('change', '.user-course', function() {
    if ($(this).prop('checked') == true) {
        __course_selected.push($(this).val());
    } else {
        removeArrayIndex(__course_selected, $(this).val());
    }
});

function addUserToCourseConfirmed(params) {

    var user_id = params.data.user_id;
    var username = params.data.username;
    if (__course_selected.length == 0) {

        var messageObject = {
            'body': 'please select aleast one course',
            'button_yes': 'OK',
            'prevent_button_no': true
        };
        callback_warning_modal(messageObject);
        return false;
    } else {
            $.ajax({
                url: admin_url+'course/check_course_valid',
                type: "POST",
                data:{"is_ajax":true,'course_ids':JSON.stringify(__course_selected)},
                beforeSend: function() { $('#enroll_user_confirmed').attr('disabled', 'disabled'); },
                success: function(response) {
                    $('#enroll_user_confirmed').removeAttr('disabled');
                    var data        = $.parseJSON(response);
                    if(data['error'] == true){
                        // if(data['active_course_list'].length > 0)
                        // {
                            var course_list     = (data['course_list']!='')?data['course_list'].split(","):'';
                            var messageHtml     = '<div id="message_title" style="font-weight: normal;margin-bottom:10px"><div style="padding: 15px 0px;">Below courses do not satisfy activation criteria</div>';
                            messageHtml     += '<ol style="padding: 0px;color: #757575;">';
                            
                            var sl = 1;
                            $.each( course_list, function( key, value ) {
                                messageHtml +='<li><h4>'+sl+'. <b>'+value+'</b></h4></li>';
                                sl++;
                            });
                            messageHtml     += '</ol>';
                            messageHtml     += '<div style="display: flex;justify-content: center;color: #f78834;font-size: 13px;padding-top: 13px;"><div><p>Note:</div> <div class="text-left" style="text-align: left;padding-left: 15px;font-size: 13px;">* refer <b>OVERVIEW</b> of these courses to make public.</p>';
                            if(data['active_course_list'].length > 0){
                                messageHtml     += '<p style="padding-top: 20px;font-size: 17px;font-weight: 600;">Do you want to add all other activated courses to the user.</p></div>';
                                var messageObject   = {
                                    'body': messageHtml,
                                    'button_yes': 'OK',
                                    'button_no': 'CANCEL',
                                    'continue_params': {
                                        "user_id": user_id,
                                        "course_ids": data['active_course_list'],
                                        "user_name" : username
                                    }
                                };
                                callback_warning_modal(messageObject, addToCourseConfirmed);
                            }else{
                                lauch_common_message('Something went Wrong' , messageHtml);
                            }
                            
                        
                    }else{
                        params                  = {'data':{'user_id':user_id, 'user_name' :username, 'course_ids':data['active_course_list']}};
                        addToCourseConfirmed(params);
                    }
                    
                }
            });

       

        
    }
}
function addToCourseConfirmed(params)
{
    var user_id    = params.data.user_id;
    var username   = params.data.user_name;
    var courses    = params.data.course_ids
    $.ajax({
        url: admin_url + 'user/add_user_to_course_new',
        type: "POST",
        data: {
            "courses": JSON.stringify(courses),
            "user_id": user_id,
            "username": username,
            "is_ajax": true
        },
        success: function (response) {
            if (response != null) {

                $('#add-users-course').modal('hide');
                __course_selected = new Array();
                __user_selected = new Array();
                var data = $.parseJSON(response);
                if (data.success == 'true') {

                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_success_modal(messageObject);
                } else {

                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_danger_modal(messageObject);
                }
            }

        }
    });
}

$(document).on('click', '#create_new_group_cancel', () =>  {
    __create_group_as_new = false;
    $('#create_new_group_cancel, #group_name').hide();
    $('#create_new_group, #group_id').show();
    $('#group_name').val("");
});


var __param_addToGroup = new Array();
var __param_saveGroup = new Array();
var __group_selected = new Array();

function addToGroup(user_id, username) {
    username = typeof username != 'undefined' ? username : '';

    var groupHtml = '';
    $('#group_list_wrapper').html(groupHtml);
    $('#batch_text').html('');
    __group_selected = new Array();
    $('#add-user-to-group').modal('show');
    $.ajax({
        url: admin_url + 'user/groups_json',
        type: "POST",
        data: {
            "is_ajax": true,
            'user_id': user_id
        },
        beforeSend: function () {
            $('#group_list_wrapper').html('<p>Loading...</p>');
        },
        success: function (response) {
            if (response != null) {

                var data = $.parseJSON(response); 
                // console.log(data);
                if (data.success == true) {
                    if (data.data.institute_name != null) {
                        $('#batch_text').html("Assign <b>" + username + "</b> to Batches under <b>" + data.data.institute_name + "</b>");
                    } else {
                        $('#batch_text').html("Assign <b>" + username + "</b> to Batches");
                    }

                    if (data.data.groups.length > 0) {
                        for (var i = 0; i < data.data.groups.length; i++) {

                            groupHtml += '<div class="active-list-padding" id="user_group_' + data.data.groups[i].id + '">';
                            groupHtml += '    <span class="chk-box">';
                            groupHtml += '        <label class="font14">';
                            groupHtml += '            <input type="checkbox" class="user-groups" value="' + data.data.groups[i].id + '"> ' + data.data.groups[i].gp_name;
                            groupHtml += '        </label>';
                            groupHtml += '    </span>';
                            groupHtml += '    <span class="email-label pull-right">';

                            // if (data.data.groups[i].gp_status == '1') {
                            //     groupHtml += '<div class="col-sm-12 pad0"><label class="pull-right label label-success" id="action_class_147">Active</label></div>';
                            // }
                            //  else {
                            //     courseHtml += '<div class="col-sm-12 pad0"><label class="pull-right label label-warning" id="action_class_157">Inactive</label></div>';
                            // }
                            groupHtml += '    </span>';
                            groupHtml += '</div>';
                        }
                        $('#group_list_wrapper').html(groupHtml);
                        $('#add_user_to_group').unbind();
                        $('#add_user_to_group').click({
                            "user_id": user_id
                        }, addUserToGroupConfirmed);

                    } else {
                        errorHtml = '<p class="no-content-text">No Batch to Display</p>'
                        $('#group_list_wrapper').html(errorHtml);
                    }

                } else {
                    errorHtml = '<p class="no-content-text">No Batch to Display</p>'
                    $('#group_list_wrapper').html(errorHtml);
                }

            } else {
                errorHtml = '<p class="no-content-text">No Batch to Display</p>'
                $('#group_list_wrapper').html(errorHtml);
            }


        }
    });

}

$(document).on('change', '.user-groups', function()  {
    if ($(this).prop('checked') == true) {
        __group_selected.push($(this).val());
    } else {
        removeArrayIndex(__group_selected, $(this).val());
    }
});

function addUserToGroupConfirmed(params) {

    if (__group_selected.length == 0) {

        var messageObject = {
            'body': 'please select aleast one batch',
            'button_yes': 'OK',
            'prevent_button_no': true
        };
        callback_warning_modal(messageObject);
        return false;
    } else {

        $.ajax({
            url: admin_url + 'user/add_user_to_group_new',
            type: "POST",
            data: {
                "groups": JSON.stringify(__group_selected),
                "user_id": params.data.user_id,
                "is_ajax": true
            },
            success: function (response) {
                if (response != null) {

                    $('#add-user-to-group').modal('hide');
                    __group_selected = new Array();
                    // __user_selected = new Array();
                    var data = $.parseJSON(response);
                    if (data.success == 'true') {

                        var messageObject = {
                            'body': data.message,
                            'button_yes': 'OK',
                            'prevent_button_no': true
                        };
                        callback_success_modal(messageObject);
                    } else {

                        var messageObject = {
                            'body': data.message,
                            'button_yes': 'OK',
                            'prevent_button_no': true
                        };
                        callback_danger_modal(messageObject);
                    }
                }

            }
        });
    }
}

var __isBulkRequest = false;

function initFetchGroupUsers(group_id) {
    if (__isBulkRequest == true) {
        fetchGroupUsersBulk(group_id);
    } else {
        fetchGroupUsers(group_id);
    }
}

// function fetchGroupUsers(group_id) {

//     if (group_id != '') {
//         var groupUserHtml = '';
//         //setting selected cousre details
//         groupUserHtml += '<div class="checkbox-wrap" id="group_user_' + __param_addToGroup.id + '" data-email="' + __param_addToGroup.email + '">';
//         groupUserHtml += '    <span class="chk-box">';
//         groupUserHtml += '        <label class="font14">';
//         groupUserHtml += '            ' + __param_addToGroup.name;
//         groupUserHtml += '        </label>';
//         groupUserHtml += '    </span>';
//         groupUserHtml += '    <span class="email-label pull-right">';
//         groupUserHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeUserIdFromGroup(' + __param_addToGroup.id + ')"><i class="icon icon-cancel-1 delte"></i></a></span>';
//         groupUserHtml += '    </span>';
//         groupUserHtml += '</div>';
//         //end
//         $('#group_users').html(groupUserHtml);

//         __param_saveGroup = new Array();
//         __param_saveGroup.push(__param_addToGroup.id);


//         $.ajax({
//             url: admin_url + 'user/group_users',
//             type: "POST",
//             data: { "is_ajax": true, "group_id": group_id },
//             success: function(response) {
//                 var data = $.parseJSON(response);
//                 groupUserHtml = '';
//                 if (data.group_users.length > 0) {
//                     for (var i = 0; i < data.group_users.length; i++) {
//                         $('#group_user_' + data.group_users[i].id).remove();

//                         //if( __param_addToGroup.id != data.group_users[i].id )
//                         //{
//                         groupUserHtml += '<div class="checkbox-wrap" id="group_user_' + data.group_users[i].id + '" data-email="' + data.group_users[i].us_email + '">';
//                         groupUserHtml += '    <span class="chk-box">';
//                         groupUserHtml += '        <label class="font14">';
//                         groupUserHtml += '            ' + data.group_users[i].us_name;
//                         groupUserHtml += '        </label>';
//                         groupUserHtml += '    </span>';
//                         groupUserHtml += '    <span class="email-label pull-right">';
//                         groupUserHtml += '       <!-- <span class="delete-cover"><a href="javascript:void(0)" onclick="removeUserIdFromGroup(' + data.group_users[i].id + ')"><i class="icon icon-cancel-1 delte"></i></a></span>-->';
//                         groupUserHtml += '    </span>';
//                         groupUserHtml += '</div>';
//                         __param_saveGroup.push(data.group_users[i].id);
//                         //}
//                     }
//                 }
//                 $('#group_users').append(groupUserHtml);
//             }
//         });
//     } else {

//         var groupUserHtml = '<p class="batch-user">Please select currently available batches from batch list</p>';
//         $('#group_users').html(groupUserHtml);
//     }
// }

function removeUserIdFromGroup(user_id) {
    if (__isBulkRequest == true) {
        __user_selected = jQuery.grep(__user_selected, function (value) {
            return value != user_id;
        });
        $('#user_details_' + user_id).prop('checked', false);
    }
    __param_saveGroup = jQuery.grep(__param_saveGroup, function (value) {
        return value != user_id;
    });
    if (__user_selected.length > 0) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
    } else {
        $("#selected_user_count").html('');
    }
    $('#group_user_' + user_id).remove();
}

function fetchGroupUsersBulk(group_id) {
    var groupUsersHtml = '';
    //setting selected cousre details
    __param_saveGroup = new Array();
    if (__user_selected.length > 0) {
        for (var i = 0; i < __user_selected.length; i++) {
            var multi_id = __user_selected[i];
            var multi_name = $('#user_row_' + __user_selected[i]).attr('data-name');
            var multi_email = $('#user_row_' + __user_selected[i]).attr('data-email');
            groupUsersHtml += '<div class="checkbox-wrap" id="group_user_' + multi_id + '" >';
            groupUsersHtml += '    <span class="chk-box">';
            groupUsersHtml += '        <label class="font14">';
            groupUsersHtml += '            ' + multi_name;
            groupUsersHtml += '        </label>';
            groupUsersHtml += '    </span>';
            groupUsersHtml += '    <span class="email-label pull-right">';
            groupUsersHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeUserIdFromGroup(' + multi_id + ')"><i class="icon icon-cancel-1 delte"></i></a></span>';
            groupUsersHtml += '    </span>';
            groupUsersHtml += '</div>';
            __param_saveGroup.push(multi_id);
        }
    }
    //end
    $('#group_users').html(groupUsersHtml);

    if (group_id != '') {
        $.ajax({
            url: admin_url + 'user/group_users',
            type: "POST",
            data: {
                "is_ajax": true,
                "group_id": group_id
            },
            success: function (response) {
                var data = $.parseJSON(response);
                groupUsersHtml = '';

                if (data.group_users.length > 0) {
                    for (var i = 0; i < data.group_users.length; i++) {
                        $('#group_user_' + data.group_users[i].id).remove();
                        //if( !inArray(data.group_users[i].id, __user_selected) )
                        //{
                        groupUsersHtml += '<div class="checkbox-wrap" id="group_user_' + data.group_users[i].id + '" data-email="' + data.group_users[i].us_email + '">';
                        groupUsersHtml += '    <span class="chk-box">';
                        groupUsersHtml += '        <label class="font14">';
                        groupUsersHtml += '            ' + data.group_users[i].us_name;
                        groupUsersHtml += '        </label>';
                        groupUsersHtml += '    </span>';
                        groupUsersHtml += '    <span class="email-label pull-right">';
                        groupUsersHtml += '      <!--  <span class="delete-cover"><a href="javascript:void(0)" onclick="removeUserIdFromGroup(' + data.group_users[i].id + ')"><i class="icon icon-cancel-1 delte"></i></a></span>-->';
                        groupUsersHtml += '    </span>';
                        groupUsersHtml += '</div>';
                        __param_saveGroup.push(data.group_users[i].id);
                        //}
                    }
                }
                $('#group_users').append(groupUsersHtml);
                $('#add-user-to-group').modal('show');
            }
        });
    }
}


function addToUserGroupProceed() {
    var group_name = $('#group_name').val();
    var group_id = $('#group_id').val();
    var errorCount = 0;
    var errorMessage = '';

    if (group_id == '') {
        errorCount++;
        errorMessage += 'please choose group <br />';
    }

    if (__param_saveGroup.length == 0) {
        errorCount++;
        errorMessage += 'please choose student<br />';
    }
    cleanPopUpMessage();
    if (errorCount == 0) {
        $.ajax({
            url: admin_url + 'user/save_group_users',
            type: "POST",
            data: {
                "is_ajax": true,
                'group_id': group_id,
                'user_ids': JSON.stringify(__param_saveGroup)
            },
            success: function (response) {
                var data = $.parseJSON(response);
                $('#add-user-to-group').modal('hide');
                $("#selected_user_count").html('');
                $('.user-checkbox, .user-checkbox-parent').prop('checked', false);
                __user_selected = new Array();
                if (data.error == 'false') {

                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_success_modal(messageObject);
                }
            }
        });
    } else {
        $('#add-user-to-group .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}



function createGroupBulk() {
    if ($('.item-deleted:checked').length > 0) {
        return false;
    }
    $('#add-user-to-group').modal();
    __create_group_as_new = false;
    $('#create_new_group_cancel, #group_name').show();
    $('#create_new_group, #group_id').hide();
    addToGroupBulk();
}




function addToGroupBulk() {
    if (__user_selected.length == 0 || $('.item-deleted:checked').length > 0) {

        $('#notify_bulk_action').modal('show');
        if ($('.item-deleted:checked').length > 0) {
            $('#notify_content').html('Please remove the deleted students from the list !');
        } else {
            $('#notify_content').html("You haven't selected any student !");
        }
        return false;
    }

    $('#add_user_to_group').unbind();
    $('#add_user_to_group').click({}, addToUserGroupProceed);

    __isBulkRequest = true;
    if (__create_group_as_new == true) {
        __create_group_as_new = false;
        $('#create_new_group_cancel, #group_name').hide();
        $('#create_new_group, #group_id').show();
    }
    var groupUsersHtml = '';
    __param_saveGroup = new Array();

    if (__user_selected.length > 0) {
        for (var i = 0; i < __user_selected.length; i++) {
            var multi_id = __user_selected[i];
            var multi_name = $('#user_row_' + __user_selected[i]).attr('data-name');
            var multi_email = $('#user_row_' + __user_selected[i]).attr('data-email');
            groupUsersHtml += '<div class="checkbox-wrap" id="group_user_' + multi_id + '" data-email="' + multi_email + '">';
            groupUsersHtml += '    <span class="chk-box">';
            groupUsersHtml += '        <label class="font14">';
            groupUsersHtml += '            ' + multi_name;
            groupUsersHtml += '        </label>';
            groupUsersHtml += '    </span>';
            groupUsersHtml += '    <span class="email-label pull-right">';
            groupUsersHtml += '        <span class="delete-cover"><a href="javascript:void(0)" onclick="removeUserIdFromGroup(' + multi_id + ')"><i class="icon icon-cancel-1 delte"></i></a></span>';
            groupUsersHtml += '    </span>';
            groupUsersHtml += '</div>';
            __param_saveGroup.push(multi_id);
        }
    }

    //setting selected cousre details
    $('#group_users').html(groupUsersHtml);
    //end

    $.ajax({
        url: admin_url + 'user/groups_json',
        type: "POST",
        data: {
            "is_ajax": true
        },
        success: function (response) {
            var groupHtml = '<option value="">Choose ' + lang('group') + '</option>';
            var data = $.parseJSON(response);
            if (data.groups.length > 0) {
                for (var i = 0; i < data.groups.length; i++) {
                    groupHtml += '<option value="' + data.groups[i].id + '">' + data.groups[i].gp_name + '</option>';
                }
            }
            $('#group_id').html(groupHtml);
            $('#add-user-to-group').modal('show');
        }
    });
}


function resetPassword(user_id, user_name) {
    var header_text = 'Are you sure to reset the password for ' + user_name +' ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'RESET',
        'button_no': 'CANCEL',
        'continue_params': {
            'user_id': user_id
        },
    };
    callback_warning_modal(messageObject, resetPasswordConfirmed);
}

function resetPasswordConfirmed(params) {
    $.ajax({
        url: admin_url + 'user/reset_password',
        type: "POST",
        data: {
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == 'false') {
                var messageObject = {
                    'body': 'Password reset successfully. New password has been sent to the student email id.',
                    'button_yes': 'OK',
                    'prevent_button_no': true,
                };
                callback_success_modal(messageObject);
            } else {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                    'prevent_button_no': true,
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function generatePassword() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for (var i = 0; i < 8; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    $('#student_password').val(text);
}

function addStudent() {
    var studentName         = $('#student_name').val().trim();
    var studentEmail        = $('#student_email').val().trim();
    var phoneNumber         = $('#phone_number').val().trim();
    var studentPassword     = $('#student_password').val().trim();
    var studentInstitute    = $('#student_institute').val();
    var studentBranch       = $('#student_branch').val();
    var sendMail            = $('#send_mail').prop('checked');
    sendMail                = (sendMail == true) ? '1' : '0';
    var errorCount          = 0;
    var errorMessage        = '';

    if (studentName == '') {
        errorCount++;
        errorMessage += 'Please Enter Student Name.<br />';
    } else if (studentName != '') {
        var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9. ]+$");
        if (!(regex.test(studentName))) {
            errorCount++;
            errorMessage += 'Enter valid Student Name.<br />';
        }

    } else {
        if (studentName.length > 50) {
            errorCount++;
            errorMessage += 'Student Name length exceed the limit.<br />';
        }
    }
    // if(studentEmail == '')
    // {
    //     errorCount++;
    //     errorMessage += 'Enter a valid email id.<br />';
    // }
    // else
    // {
    if(studentEmail == ''){
        errorCount++;
        errorMessage += 'Please Enter Student email id.<br />';
    }else{
        if (!validateEmail(studentEmail)) {
            errorCount++;
            errorMessage += `Invalid email id : <b>${studentEmail}</b><br />`;
        }
    }
    // }
    if (phoneNumber == '') {
        errorCount++;
        errorMessage += 'Please Enter Student phone number.<br />';
    }else{
        if(!IsmobileNumber(phoneNumber)){
            errorCount++;
            errorMessage += `Invalid phone number : <b>${phoneNumber}</b><br />`;
        }
    }
    if (studentPassword == '') {
        errorCount++;
        errorMessage += 'Please Enter Student Password.<br />';
    } else {
        if (studentPassword.length < 6) {
            errorCount++;
            errorMessage += 'Password must be atleast 6 characters.<br />';
        }
    }
    if (studentInstitute == '') {
        errorCount++;
        errorMessage += 'Please Choose Student Institute.<br />';
    }
    // if (studentBranch == '') {
    //     errorCount++;
    //     errorMessage += 'Choose Student Branch.<br />';
    // }
    cleanPopUpMessage();
    if (errorCount > 0) {
        $('#create_user .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } else {
        $.ajax({
            url: admin_url + 'user/create_user',
            type: "POST",
            data: {
                "student_name": studentName,
                "student_email": studentEmail,
                "phone_number": phoneNumber,
                "student_password": studentPassword,
                "student_institute": studentInstitute,
                "student_branch": studentBranch,
                'send_mail': sendMail,
                "is_ajax": true
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error == false) {
                    $('#create_user').modal('hide');
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_success_modal(messageObject);
                    setTimeout(function () {
                        //location.reload();
                        __offset = 1;
                        getUsers();
                    }, 1500);
                } else {
                    $('#create_user .modal-body').prepend(renderPopUpMessage('error', data.message));
                }
            }
        });
    }
}

function preventSpecialCharector(e){
    var k; 
    document.all ? k = e.keyCode : k = e.which; 
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57) || k == 46);
}


function uploadUserBulk() {
    if (__uploaded_files == '') {
        lauch_common_message('File missing', 'Please choose file to upload.');
        return false;
    }

    var filename = __uploaded_files.name;
    var valid_extensions = /(\.csv)$/i;

    if (valid_extensions.test(filename)) {
        $('#percentage_bar').show();
        var uploadURL = admin_url + 'institutes/import_institutes'
        var fileObj = new processFileName(__uploaded_files.name);
        var param = new Array;
        param["file_name"] = fileObj.uniqueFileName();
        param["extension"] = fileObj.fileExtension();
        param["file"] = __uploaded_files;
        uploadFiles(uploadURL, param, uploadUserCompleted);
    } else {
        lauch_common_message('Invalid File', 'Choose .csv file to upload');
        return false;
    }
}
$(function () {
    startTextToolbar();
});

function startTextToolbar() {
    $('#redactor_invite').redactor({
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        source: false,
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function (json, xhr) {
                alert('Please select a valid image');
                return false;
            }
        }
    });
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

function renderPagination(offset, totalUsers) {
    offset = Number(offset);
    totalUsers = Number(totalUsers);
    var totalPage = Math.ceil(totalUsers / __limit);
    if (offset <= totalPage && totalPage > 1) {
        var paginationHtml = '';
        paginationHtml += '<ul class="pagination pagination-wrapper"  style="left:65px;">';
        paginationHtml += generatePagination(offset, totalPage);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
        scrollToTopOfPage();
    } else {
        $('#pagination_wrapper').html('');
    }
}
$(document).on('click', '.locate-page', function()  {
    __offset = $(this).attr('data-page');
    getUsers();
});

function clearUserCache() {
    __user_selected = new Array();
    __course_selected = new Array();
    $("#selected_user_count").html('');
}

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
            getUsers();
        }
    } else {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            getUsers();
        }
    }
}

function restoreUserBulkConfirmed() {
    $.ajax({
        url: admin_url + 'user/restore_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__user_selected),
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.users.length > 0) {
                for (var i in data.users) {
                    if (__filter_dropdown == 'all') {
                        $('#user_row_' + data.users[i].id).html(renderUserRow(data.users[i]));
                    } else {
                        $('#user_row_' + data.users[i].id).remove();
                        __totalUsers = __totalUsers - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalUsers <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Students';
                        } else {
                            var totalUsersHtml = __totalUsers + ' Students'; //__shownUsers + ' / ' + __totalUsers + ' Students';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __user_selected = new Array();
            $("#user_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Students restored successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_user_count").html('');
            $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
            refreshListing();
        }
    });
}

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});


function refreshListingLaunch() {
    refreshListing();
    $('#common_message_advanced').hide();
}

function exportStudents() {

    if($('.user-listing-row').length > 0){

        if($('.inst-profile-export').length > 0 ) {
            $('#profile_field_modal').modal();
        } else {
            exportStudentsConfirmed();
        }
    }else{

        var messageObject = {
            'body': 'There are no students in list to export',
            'button_yes': 'OK',
        };
        callback_danger_modal(messageObject);
    }
    
}
function exportStudentsConfirmed() {

    var param               = {};
    param.keyword        = $.trim($('#user_keyword').val());
    param.filter         = __filter_dropdown;
    param.institute_id   = __institute_id;
    param.branch_id      = __branch_id;
    param.batch_id       = __batch_id;
    param.profiles       = {};
        
    $( ".inst-profile-export" ).each(function( index ) {
        if($( this ).prop('checked') == true) {
            param.profiles[$( this ).val()] = atob($( this ).attr('data-field-name'));
        }
    });
    $('#profile_field_modal').modal('hide');
    $('.inst-profile-export').prop('checked', false);
    location.href           = admin_url+'user/export_students/'+btoa(JSON.stringify(param));
    $('#profile_field_modal').modal('hide');
}