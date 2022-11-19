
var __institute_id = '';
var __branch_id = '';
var __batch_id = '';
var timeOut = '';
var __filter_dropdown = '';
var __user_selected = new Array();
var __course_selected = new Array();
var __current_validity = '';
var __extend_date      = '';
const __previlage       = new Access();

function changeSubscriptionStatusBulkConfirmed(params) {

    var bundle_id = params.data.bundle_id;
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'bundle/change_subscription_status_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__user_selected),
            "bundle_id": bundle_id,
            "status": status,
            "is_ajax": true,
            'bundle_title':__course_title
        },
        success: function (response) {

            var data = $.parseJSON(response);
            if (data['error'] == false) {
                // var action_list = '';
                // if (status == 0 || status == 2) {
                //     action_list = '<a onclick="changeSubscriptionStatus(\'' + user_id + '\', \'' + course_id + '\', \'1\' )" href="javascript:void(0);">' + lang('approve') + '</a>';
                // } else {
                //     action_list = '<a onclick="changeSubscriptionStatus(\'' + user_id + '\', \'' + course_id + '\', \'0\' )" href="javascript:void(0);">' + lang('suspend') + '</a>';
                // }
                // $('#status_btn_' + user_id).html(action_list);
                if(__filter_dropdown=='suspended'||__filter_dropdown=='active'){

                    //$('#user_row_'+user_id[i]).remove();
                    __totalUsers =parseInt($('.user-count').html());
                    if(__totalUsers > 0){
                        __totalUsers = __totalUsers - __user_selected.length;
                        var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                        
                        $('.user-count').html(totalUsersHtml);

                    } else {
                        $('.user-count').html('No Students');
                      
                        $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                        $('.select-all-style').css('display', 'none');
                        clearUserCache();
                    }
                }
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                if((__filter_dropdown == 'active' && status == 0)  || (__filter_dropdown == 'suspended' && status == 1)) {
                    for (var i = 0; i < __user_selected.length; i++) {
                        $('#user_row_' + __user_selected[i]).remove();
                    }    
                }
                refreshListing();
            } else {
                var messageObject = {
                    'body': 'Failed to change subscription',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            clearUserCache();
        }
    });
}
function approveSubscriptionStatusBulk(bundle_id) {

    if (__user_selected.length > 0) {

        var messageObject = {
            'body': lang('confirm_approval_for_selected_users'),
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "bundle_id": bundle_id,
                "status": '1'
            },
        };
        callback_warning_modal(messageObject, changeSubscriptionStatusBulkConfirmed);
    }

}

function suspendSubscriptionStatusBulk(bundle_id) {

    if (__user_selected.length > 0) {

        var messageObject = {
            'body': lang('confirm_suspend_for_selected_users'),
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "bundle_id": bundle_id,
                "status": '0'
            },
        };
        callback_warning_modal(messageObject, changeSubscriptionStatusBulkConfirmed);
    }

}
function removeUserFromCourse(bundle_id, user_id = '',username='') {

    var user_ids = [];
    if (__user_selected.length > 0) {
        user_ids = __user_selected;
    } else {
        if (user_id != '' && user_id > 0) {
            user_ids.push(user_id);
        }
    }

    var messageObject = {
        'body': 'Are you sure to remove student from this Bundle',
        'button_yes': 'OK',
        'button_no': 'CANCEL',
        'continue_params': {
            "user_id": user_ids,
            "bundle_id": bundle_id,
            "student_name":username
        },
    };
    callback_warning_modal(messageObject, removeUserFromCourseConfirmed);
}

function removeUserFromCourseConfirmed(params) {

    __user_selected = new Array();
    var user_id     = params.data.user_id;
    var bundle_id   = params.data.bundle_id;
    var student_name= params.data.student_name;
    console.log(user_id);
    $.ajax({
        url: admin_url + 'bundle/delete_subscription',
        type: "POST",
        data: {
            "user_id": JSON.stringify(user_id),
            "bundle_id": bundle_id,
            "is_ajax": true,
            "bundle_title":__course_title,
            "student_name":student_name
        },
        success: function (response) {

            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if(__totalUsers > 0){
                    __totalUsers = __totalUsers-user_id.length;
                    var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                    
                    $('.user-count').html(totalUsersHtml);

                } else {
                    $('.user-count').html('No Students');
                  
                    // $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                    // $('.select-all-style').css('display', 'none');
                    // clearUserCache();
                }
                for (var i = 0; i < user_id.length; i++) {
                    $('#user_row_' + user_id[i]).remove();
                }

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            clearUserCache();
        }
    });
}

function changeSubscriptionStatus(user_id, bundle_id, status) {

    var actionLabel = 'approval';
    var button_content = 'APPROVE';
    var actionMessage = 'message_approve_user';
    var user_name = $('#user_row_' + user_id).attr('data-name');
    if (status == 0) {
        button_content = 'SUSPEND';
        actionLabel = 'suspend';
        actionMessage = 'message_suspend_user';
    }

    var messageObject = {
        'body': lang(actionMessage) + '<b>' + user_name + '</b> ',
        'button_yes': 'OK',
        'button_no': 'CANCEL',
        'continue_params': {
            "user_id": user_id,
            "bundle_id": bundle_id,
            "status": status,
            "username" : user_name
        },
    };
    callback_warning_modal(messageObject, changeSubscriptionStatusConfirmed);

}

function changeSubscriptionStatusConfirmed(params) {

    var user_id = params.data.user_id;
    var bundle_id = params.data.bundle_id;
    var status = params.data.status;
    var student = params.data.username;
    $.ajax({
        url: admin_url + 'bundle/change_subscription_status',
        type: "POST",
        data: {
            "user_id": user_id,
            "bundle_id": bundle_id,
            "status": status,
            "is_ajax": true,
            "student" : student,
            "bundle_title":__course_title
        },
        success: function (response) {

            
            var data = $.parseJSON(response);

            if (data['error'] == false) {
                var action_list = '';
                if (status == 0 || status == 2) {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + user_id + '\', \'' + bundle_id + '\', \'1\' )" href="javascript:void(0);">' + lang('approve') + '</a>';
                } else {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + user_id + '\', \'' + bundle_id + '\', \'0\' )" href="javascript:void(0);">' + lang('suspend') + '</a>';
                }
                $('#status_btn_' + user_id).html(action_list);

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',    
                };
                callback_success_modal(messageObject);
                
                if((__filter_dropdown == 'active' && status == 0)  || (__filter_dropdown == 'suspended' && status == 1)) {
                    $('#user_row_' + user_id).remove();
                    __totalUsers =parseInt($('.user-count').html());
                    __totalUsers = __totalUsers -1;
                    console.log(user_id);
                    if(__totalUsers > 0){
                        var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                      
                        $('.user-count').html(totalUsersHtml);
                    }else {
                        $('.user-count').html('No Students');
                  
                        $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                        clearUserCache();
                    }
                }
                refreshListing();
            } else {
                var messageObject = {
                    'body': 'Failed to change subscription',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}
function resetResult(bundle_id, user_id = '') {
    var username = '';
    var user_ids = [];
    if (__user_selected.length > 0) {
        user_ids = __user_selected;
        var messageObject = {
            'body': lang('confirm_reset_result_for_user'),
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "user_id": user_ids,
                "bundle_id": bundle_id,
                "user_name":username
            },
        };
    } else {
        if (user_id != '' && user_id > 0) {
            username = $('#user_row_'+user_id).attr('data-name');
            user_ids.push(user_id);
            var messageObject = {
                'body': 'Do you want to reset the lecture of selected student',
                'button_yes': 'OK',
                'button_no': 'CANCEL',
                'continue_params': {
                    "user_id": user_ids,
                    "bundle_id": bundle_id,
                    "user_name":username
                },
            };

        }
    }
    callback_warning_modal(messageObject, resetResultConfirmed);
}

function resetResultConfirmed(params) {
    var user_id = params.data.user_id;
    var bundle_id = params.data.bundle_id;
    var student_name = params.data.user_name;
    $.ajax({
        url: admin_url + 'bundle/reset_result',
        type: "POST",
        data: {
            "user_id": JSON.stringify(user_id),
            "bundle_id": bundle_id,
            "is_ajax": true,
            "bundle_title":__course_title,
            "student_name":student_name
        },
        success: function (response) {

            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if(__filter_dropdown=='completed'){

                    total_user=user_id.length;
                    __totalUsers=__totalUsers-total_user;
                }
                if(__filter_dropdown=='incompleted'){

                    total_user=user_id.length;
                    __totalUsers=__totalUsers-total_user;
                }

                for (var i = 0; i < user_id.length; i++) {

                    $('#progress_bar_user_' + user_id[i] + '  .link-style').html('0%');
                    $('#progress_bar_user_' + user_id[i] + '  .progress-bar').css('width', '0%');
                    $('#progress_bar_user_' + user_id[i] + '  .sr-only').html('0% ' + lang('complete'));

                    userHtml = '';
                    userHtml += '                    <a href="javascript:void(0)" onclick="setAsComplete(\'' + bundle_id + '\',\'' + user_id[i] + '\')">' + lang('set_as_complete') + '</a>';

                    $('#complete_btn_' + user_id[i]).html(userHtml);
                    $('#reset_btn_' + user_id[i]).html('');

                    if(__filter_dropdown=='completed'){

                        $('#user_row_'+user_id[i]).remove();
                        if(__totalUsers > 0){
                            var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                            $('.user-count').html(totalUsersHtml);
                        }else {
                            $('.user-count').html('No Students');
                            $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                            clearUserCache();
                        }
                    }
                    if(__filter_dropdown=='incompleted'){
                        $('#user_row_'+user_id[i]).remove();
                        if(__totalUsers > 0){
                            var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                            $('.user-count').html(totalUsersHtml);
                        }else {
                            $('.user-count').html('No Students');
                            $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                            clearUserCache();
                        }
                    }
                }
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
                scrollToTopOfPage();
            } else {
                var messageObject = {
                    'body': 'Failed to change',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                refreshListing();
            }
            clearUserCache();

        }
    });
}
function setAsComplete(bundle_id, user_id = '',username='') {

    var user_ids = [];
    if (__user_selected.length > 0) {
        user_ids = __user_selected;
        var messageObject = {
            'body': 'Do you want to set all the lectures are completed for selected students',
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "user_id": user_ids,
                "bundle_id": bundle_id,
                "username":username
            },
        };
    } else {
        if (user_id != '' && user_id > 0) {
            user_ids.push(user_id);
            var messageObject = {
                'body': 'Do you want to set the lecture is completed for selected student',
                'button_yes': 'OK',
                'button_no': 'CANCEL',
                'continue_params': {
                    "user_id": user_ids,
                    "bundle_id": bundle_id,
                    "username":username
                },
            };

        }
    }

    callback_warning_modal(messageObject, setAsCompleteConfirmed);

}

function setAsCompleteConfirmed(params) {

    var user_id = params.data.user_id;
    var bundle_id = params.data.bundle_id;
    var student_name = params.data.username;
    $.ajax({
        url: admin_url + 'bundle/set_as_complete',
        type: "POST",
        data: {
            "user_id": JSON.stringify(user_id),
            "bundle_id": bundle_id,
            "is_ajax": true,
            "student_name":student_name,
            "bundle_title":__course_title
            
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if(__filter_dropdown=='not_started'){

                    total_user=user_id.length;
                    __totalUsers=__totalUsers-total_user;
                }
                if(__filter_dropdown=='incompleted'){

                    total_user=user_id.length;
                    __totalUsers=__totalUsers-total_user;
                }
                for (var i = 0; i < user_id.length; i++) {

                    $('#progress_bar_user_' + user_id[i] + '  .link-style').html('100%');
                    $('#progress_bar_user_' + user_id[i] + '  .progress-bar').css('width', '100%');
                    $('#progress_bar_user_' + user_id[i] + '  .sr-only').html('100% ' + lang('complete'));
                    userHtml = '';
                    userHtml = '                    <a href="javascript:void(0)" onclick="resetResult(\'' + bundle_id + '\',\'' + user_id[i] + '\')">' + lang('reset_result') + '</a>';

                    $('#complete_btn_' + user_id[i]).html('');
                    $('#reset_btn_' + user_id[i]).html(userHtml);
                    if(__filter_dropdown=='not_started'){

                        $('#user_row_'+user_id[i]).remove();
                        if(__totalUsers > 0){
                            var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                            $('.user-count').html(totalUsersHtml);
                        } else {
                            $('.user-count').html('No Students');
                          
                            $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                            $('.select-all-style').css('display', 'none');
                            clearUserCache();
                        }
                    }
                    if(__filter_dropdown=='incompleted'){
                        $('#user_row_'+user_id[i]).remove();
                        if(__totalUsers > 0){
                            var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                          
                            $('.user-count').html(totalUsersHtml);
                        }else {
                            $('.user-count').html('No Students');
                      
                            $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                            clearUserCache();
                        }
                    }

                }
                scrollToTopOfPage();
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': 'Failed to change',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            clearUserCache();
        }
    });
}

function extendValidityForCourse(course_id, user_id = '', current_validity = '',user_name='') {

    $('#extend_date').val('');
    $('#update_extended_validity').attr('onclick', 'extendValidityForCourseConfirmed(\'' + course_id + '\',\'' + user_id + '\',\'' + user_name + '\')');
    if (current_validity != '') {
        __current_validity = current_validity;
        $('#validity_time').html('Current Validity Date is: <b>' + FormateDate(__current_validity) + '</b>');
        $('#validity_time').css('display', 'block');
    }else{
        $('#validity_time').html('');
        $('#validity_time').css('display', 'none');
    }
    if(user_id!=''){
        $('#status_text').text('Change Validity Period for this Student');
    }else{
        $('#status_text').text('Change Validity Period for the selected Student');
    }
    current_validity='';
}

function extendValidityForCourseConfirmed(course_id, user_id = '',student_name='') {

    var user_ids = [];
    if (__user_selected.length > 0) {
        user_ids = __user_selected;
    } else {
        if (user_id != '' && user_id > 0) {
            user_ids.push(user_id);
        }
    }

    var date        = $('#extend_date').val(); 
    __extend_date   = date;
    if (date == '') {
        setTimeout(function () {
            $('#popUpMessage').hide();
        }, 2000);
        $('#extend-validity .modal-body').prepend(renderPopUpMessage('error', 'Date need to be selected'));
    } else {
        $('#extend-validity').modal('hide');
        $('#extend_date').val('');
        $.ajax({
            url: admin_url + 'bundle/send_extend_validity',
            type: "POST",
            data: {
                "user_id": JSON.stringify(user_ids),
                "bundle_id": course_id,
                'updated_validity': __extend_date,
                "is_ajax": true,
                "course_title":__course_title,
                "student_name":student_name
            },
            success: function (response) {

                var data = $.parseJSON(response);
                if (data['error'] == false) {

                    for (var i = 0; i < user_ids.length; i++) {
                        console.log('extend-time_' + user_ids[i]);
                        $('#extend-time_' + user_ids[i]).attr('onclick', 'extendValidityForCourse(\'' + course_id + '\',\'' + user_ids[i] + '\',\'' + reformatDate(__extend_date) + '\')');
                    }
                    __current_validity = '';
                    __extend_date = '';
                    var messageObject = {
                        'body': data['message'],
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);

                } else {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
                clearUserCache();
            }
        });
    }

}
function sendMessageToUser(user_id = '') {

    $('#invite-user-bulk').modal();
    $('#invite_send_subject').val('');
    $('#redactor_invite').redactor('insertion.set', '');
    startTextToolbar();
    $('#message_send_button').attr('onclick', 'sendMessageBulk(' + user_id + ')');
}

function sendMessageBulk(user_id = '') {

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
        }
    }
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

            var data = $.parseJSON(response);
            if (data['success'] == true) {
                $('#invite-user-bulk').modal('hide');
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            } else {
                $('#invite-user-bulk').modal('hide');
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            $('#message_send_button').text('SEND');

            setTimeout(function () {
                $('#invite-user-bulk').modal('hide');
            }, 2500);
            clearUserCache();
        }
    });
}
$(document).ready(function () {
    $('#create_new_group_cancel, #group_name').hide();
    var today = new Date();
    $("#extend_date").datepicker({
        language: 'en',
        minDate: today // Now can select only dates, which goes after today
    });

    var filter = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');
    var institute_id = getQueryStringValue('institute_id');
    var branch_id = getQueryStringValue('branch_id');
    var batch_id = getQueryStringValue('batch_id');
    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }
    else
    {
        __filter_dropdown = 'active';
    }
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#user_keyword').val(keyword);
    }
    if (institute_id != '') {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    if (branch_id != '') {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring(0, $('#filter_branch_' + branch_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    if (batch_id != '') {
        __batch_id = batch_id;
        $('#filter_batch').html($('#filter_batch_' + batch_id).text() + '<span class="caret"></span>');
    }
    setBulkAction();
});
$(document).on('click', '.user-checkbox', function()  {
    var user_id = $(this).val();
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __user_selected.push(user_id);
    } else {
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__user_selected, user_id);
    }

    if (__user_selected.length > 0) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
    } else {
        $("#selected_user_count").html('');
    }

    if (__user_selected.length > 1) {
        $("#course_bulk").css('display', 'block');
    } else {
        //$("#selected_user_count").html('');
        $("#course_bulk").css('display', 'none');
    }


    var numItems = $('.user-checkbox').length;
    if (__user_selected.length == numItems) {
        $('.user-checkbox-parent').prop('checked', true);
    } else {
        $('.user-checkbox-parent').prop('checked', false);
    }


});
$(document).on('click', '.user-checkbox-parent', function()  {
    var parent_check_box = this;
    __user_selected = new Array();
    $('.user-checkbox').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').each(function (index) {
            __user_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }

    if (__user_selected.length > 0) {
        $("#selected_user_count").html(' (' + __user_selected.length + ')');
    } else {
        $("#selected_user_count").html('');
    }
    if (__user_selected.length > 1) {
        $("#course_bulk").css('display', 'block');
    } else {
        $("#course_bulk").css('display', 'none');
    }

});
$(document).on('click', '#basic-addon2', function() {
    var user_keyword = $('#user_keyword').val().trim();        
    if(user_keyword == '')
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else{
        __user_selected = new Array();
        $("#selected_user_count").html('');
        $("#course_bulk").css('display', 'none');
        __offset = 1;
        getSubscribers();
    }
});
function reformatDate(dateString) {
    
    var output = dateString.replace(/(\d\d)\/(\d\d)\/(\d{4})/, "$3-$1-$2");console.log(output);
    return output;
}
function FormateDate(dateString) {
    var p = dateString.split(/\D/g);
    return [p[2], p[1], p[0]].join("-")

}
$(document).on('click', '#searchclear', function()  {
    __user_selected = new Array();
    $("#selected_user_count").html('');
    $("#course_bulk").css('display', 'none');
    __offset = 1;
    getSubscribers();
});
function getSubscribers() {
    // $("#selected_user_count").html('');
    // $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
    // $("#course_bulk").css('display', 'none');
    var keyword = $('#user_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || __institute_id != '' || __branch_id != '' || __batch_id != '' || __offset != '' || keyword != '') {
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
        url: admin_url + 'bundle/enrolled_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "filter": __filter_dropdown,
            'course_id': __course_id,
            'keyword': keyword,
            'institute_id': __institute_id,
           // 'branch_id': __branch_id,
            'batch_id': __batch_id,
            'limit': __limit,
            'offset': __offset
        },
        success: function (response) {

            var data_user = $.parseJSON(response);
            //$('#loadmorebutton').hide();

            renderPagination(__offset, data_user['total_enrolled']);
            if (data_user['enrolled_users'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data_user['total_enrolled'];
                    __shownUsers = data_user['enrolled_users'].length;
                    //remainingUser = (data_user['total_enrolled'] - data_user['enrolled_users'].length);
                    var totalUsersHtml = data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                    scrollToTopOfPage();
                    $('.user-count').html(totalUsersHtml);
                    $('#user_row_wrapper').html(renderSubscribersHtml(response));
                } else {
                    __totalUsers = data_user['total_enrolled'];
                    __shownUsers = ((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length;
                    //remainingUser = (data_user['total_enrolled'] - (((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length));
                    var totalUsersHtml = data_user['total_enrolled'] + ' Students'; //(((__offset - 2) * data_user['limit']) + data_user['enrolled_users'].length) + ' / ' + data_user['total_enrolled'] + ' Students';
                    $('.user-count').html(totalUsersHtml);
                    $('.user-checkbox-parent').prop('checked', false);
                    $('#user_row_wrapper').html(renderSubscribersHtml(response));
                }
                // scrollToTopOfPage();

                if (data_user['batches'].length > 0) {
                    $('#filter_batch_div').attr('style', '');
                    var batchHtml = '<li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch(\'all\')">All batches </a></li>';
                    for (var i in data_user['batches']) {
                        var batchNameToolTip = '';
                        if (data_user['batches'][i]['batch_name'].length > 15) {
                            batchNameToolTip = 'data-toggle="tooltip" title="' + data_user['batches'][i]['batch_name'] + '"';
                        }
                        batchHtml += '<li><a href="javascript:void(0)" id="filter_batch_' + data_user['batches'][i]['id'] + '" onclick="filter_batch(' + data_user['batches'][i]['id'] + ')" ' + batchNameToolTip + '>' + data_user['batches'][i]['batch_name'] + '</a></li>';
                    }
                    $('#batch_filter_list').html(batchHtml);
                    if (__batch_id == '') {
                        $('#filter_batch').html('All Batches <span class="caret"></span>');
                    }
                } else {
                    $('#filter_batch_div').css('display', 'none');
                }

            } else {
                $('.user-count').html('No Students');
                scrollToTopOfPage();

                $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                clearUserCache();

            }
            var enrollUserAvailable = $('.user-listing-row:visible').length;
            if(enrollUserAvailable<=0){
                $('.select-all-style').hide();
            }else{
                $('.select-all-style').show();
            }
            if (data_user['show_load_button'] == true) {
                //$('#loadmorebutton').show();
            }
            //remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
            //$('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
            
            clearUserCache();
        }
    });
    
}
$(document).on('keyup', '#user_keyword', function()  {
    __offset = 1;
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        getSubscribers();
    }, 600);
});

function loadMoreUsers() {
    getSubscribers();
}
function filter_user_by(filter) {
    if (filter == 'all') {
        $('#user_keyword').val('');
    }
    __filter_dropdown = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    __offset = 1;
    getSubscribers();
    setBulkAction();
    __user_selected = new Array();
    $("#selected_user_count").html('');
    $("#course_bulk").css('display', 'none');
    $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
}

function setBulkAction() {
    var filter   = __filter_dropdown;
    var bulkHtml = `<span class="dropdown-tigger" data-toggle="dropdown">
                        <span class="label-text">Bulk Action </span>
                        <span class="tilder"></span>
                    </span>
                    <ul class="dropdown-menu pull-right" role="menu">
                        <li>
                            <a href="javascript:void(0)" onclick="sendMessageToUser()">Send Message</a>
                        </li>
                       
                    </ul>`;
 // <li>
                        //     <a onclick="removeUserFromBundle('`+__course_id+`')" href="javascript:void(0)">Remove From Bundle</a>
                        // </li>
 
    // if(__previlage.hasEdit() == true) {
    // bulkHtml += '<li>';
    // bulkHtml += '     <a href="javascript:void(0);" data-toggle="modal" data-target="#extend-validity" onclick="extendValidityForCourse(' + __course_id + ')">Change Validity Period</a>';
    // bulkHtml += ' </li>';
   
    // if (filter != 'completed') {
    //     bulkHtml += ' <li>';
    //     bulkHtml += '     <a onclick="setAsComplete(' + __course_id + ')" href="javascript:void(0)">Set as Complete</a>';
    //     bulkHtml += ' </li>';
    // }
    // if ((filter != 'not_started')) {
    //     bulkHtml += ' <li>';
    //     bulkHtml += '     <a onclick="resetResult(' + __course_id + ')" href="javascript:void(0)">Reset Result</a>';
    //     bulkHtml += ' </li>';
    // }
    // if (filter != 'active') {
    //     bulkHtml += ' <li>';
    //     bulkHtml += '     <a href="javascript:void(0)" onclick="approveSubscriptionStatusBulk(' + __course_id + ')">Approve</a>';
    //     bulkHtml += ' </li>';
    // }
    // if (filter != 'suspended') {
    //     bulkHtml += ' <li>';
    //     bulkHtml += '     <a href="javascript:void(0)" onclick="suspendSubscriptionStatusBulk(' + __course_id + ')">Suspend</a>';
    //     bulkHtml += ' </li>';
    // }
    // }
    // bulkHtml += '<li>';
    // bulkHtml += '    <a onclick="resetCertificate(' + __course_id + ')" href="javascript:void(0)">Reset Certificates</a>';
    // bulkHtml += '</li>';
    // if(__previlage.hasDelete() == true) {
    // bulkHtml += '<li>';
    // bulkHtml += '    <a onclick="removeUserFromCourse(' + __course_id + ')" href="javascript:void(0)">Remove From Bundle</a>';
    // bulkHtml += '</li>';
    // }
    $("#course_bulk").html(bulkHtml);
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
    getSubscribers();
    __user_selected = new Array();
    $("#selected_user_count").html('');
    $("#course_bulk").css('display', 'none');
    $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
}
function filter_branch(branch_id) {
    if (branch_id == 'all') {
        __branch_id = '';
        $('#filter_branch').html('All Branches <span class="caret"></span>');
    } else {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring(0, $('#filter_branch_' + branch_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    __offset = 1;
    getSubscribers();
    __user_selected = new Array();
    $("#selected_user_count").html('');
    $("#course_bulk").css('display', 'none');
    $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
}
function filter_batch(batch_id) {
    if (batch_id == 'all') {
        __batch_id = '';
        $('#filter_batch').html('All Batches <span class="caret"></span>');
    } else {
        __batch_id = batch_id;
        $('#filter_batch').html('<span class="dropdown-filter">' + $('#filter_batch_' + batch_id).text() + '</span><span class="caret"></span>');
    }
    __offset = 1;
    getSubscribers();
    __user_selected = new Array();
    $("#selected_user_count").html('');
    $("#course_bulk").css('display', 'none');
    $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
}
function renderNonSubscribedUserHtml(response) {
    var data = $.parseJSON(response);
    var userHtml = '';
    if (data['users'].length > 0) { 
        for (var i = 0; i < data['users'].length; i++) {
            userHtml += '<div class="rTableRow" id="user_not_subscribed_row_' + data['users'][i]['id'] + '">';
            userHtml += '    <div class="rTableCell"> ';
            userHtml += '        <input type="checkbox" class="user-checkbox-not-subscribed" value="' + data['users'][i]['id'] + '"> ';
            userHtml += '        <span class="pad-right20">' + data['users'][i]['us_name'] + '</span>';
            userHtml += '        <span>' + data['users'][i]['us_email'] + '</span>';
            userHtml += '    </div>';
            userHtml += '</div>  ';
        }
    }
    return userHtml;
}
function renderSubscribersHtml(response) {
    var data          = $.parseJSON(response);
    var userHtml      = '';
    console.log(data);
    if (data['enrolled_users'].length > 0) {
        $('#bundleExport').show(); 
        for (var i = 0; i < data['enrolled_users'].length; i++) {
            userHtml += '<div class="rTableRow user-listing-row" id="user_row_' + data['enrolled_users'][i]['bs_user_id'] + '" data-name="' + data['enrolled_users'][i]['us_name'] + '" data-email="' + data['enrolled_users'][i]['us_email'] + '">';
            userHtml += '    <div class="rTableCell"> ';
            userHtml += '        <input type="checkbox" class="user-checkbox" value="' + data['enrolled_users'][i]['bs_user_id'] + '" id="user_details_' + data['enrolled_users'][i]['bs_user_id'] + '"> ';
            userHtml +='        <svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="16px" height="18px" viewBox="0 0 16 18" enable-background="new 0 0 16 18" fill="#64277d" xml:space="preserve"><g> <path d="M8,1.54v0.5c1.293,0.002,2.339,1.048,2.341,2.343C10.339,5.675,9.293,6.721,8,6.724C6.707,6.721,5.66,5.675,5.657,4.382 C5.66,3.088,6.707,2.042,8,2.04V1.54v-0.5c-1.846,0-3.342,1.496-3.342,3.343c0,1.845,1.497,3.341,3.342,3.341 c1.846,0,3.341-1.496,3.341-3.341C11.341,2.536,9.846,1.04,8,1.04V1.54z"/> <path d="M2.104,16.46c0-1.629,0.659-3.1,1.727-4.168C4.899,11.225,6.37,10.565,8,10.565s3.1,0.659,4.168,1.727 c1.067,1.068,1.727,2.539,1.727,4.168h1c0-3.808-3.087-6.894-6.895-6.895c-3.808,0-6.895,3.087-6.895,6.895H2.104z"/></g></svg>';
            userHtml += '   <div class="manage-stud-list" style="display:inline !important;line-height: 11px;position:relative;top: 5px;">';
            userHtml += '        <a href="' + admin_url + 'user/profile/' + data['enrolled_users'][i]['bs_user_id'] + '" >';
            userHtml += '            <span class="list-user-name text-left text-blue" style="width: 35%;">' + data['enrolled_users'][i]['us_name'] + '</span>';
            userHtml += '            <span class="list-institute-code  text-left">' + data['enrolled_users'][i]['us_institute_code'] + '</span>';
            userHtml += '            <span class="list-register-number">' + data['enrolled_users'][i]['us_phone'] + '</span>';
            userHtml += '        </a>';
            userHtml += '    </div>';
            userHtml += '    </div>';
            // userHtml += '    <div class="rTableCell pad0" style="min-width:100px;" id="progress_bar_user_' + data['enrolled_users'][i]['bs_user_id'] + '">';
            // userHtml += '        <div class="cent-algn-txt float-r">';
            // userHtml += '            <a href="javascrip:void(0)" class="link-style">' + data['enrolled_users'][i]['bs_percentage'] + '%</a>';
            // userHtml += '        </div>';
            // userHtml += '        <div class="progress sml-progress stud-course-progress">';
            // userHtml += '            <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: ' + data['enrolled_users'][i]['bs_percentage'] + '%;">';
            // userHtml += '                <span class="sr-only">' + data['enrolled_users'][i]['bs_percentage'] + '% ' + lang('complete') + '</span>';
            // userHtml += '            </div>';
            // userHtml += '        </div>';
            // userHtml += '    </div>';
            userHtml += '    <div class="td-dropdown rTableCell">';
            userHtml += '        <div class="btn-group lecture-control">';
            userHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            userHtml += '                <span class="label-text">';
            userHtml += '                    <i class="icon icon-down-arrow"></i>';
            userHtml += '                </span>';
            userHtml += '                <span class="tilder"></span>';
            userHtml += '            </span>';
            userHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
            // if (__studentEnrollPrivilege.view() == true) {
            userHtml += '                <li>';
            userHtml += '                    <a href="javascript:void(0)" onclick="sendMessageToUser(' + data['enrolled_users'][i]['bs_user_id'] + ')" >Send Message</a>';
            userHtml += '                </li>';
            userHtml += '                <li>';
            userHtml += `                     <a onclick="removeUserFromBundle(${data['enrolled_users'][i]['bs_bundle_id']},${data['enrolled_users'][i]['bs_user_id']},'${data['enrolled_users'][i]['bs_user_name']}')" href="javascript:void(0)">Remove From Bundle</a>`;
            userHtml += '                 </li>';
            // }
            // if (__studentEnrollPrivilege.edit() == true) {
                // userHtml += '                <li>';
                // userHtml += '                <a href="javascript:void(0);" data-toggle="modal" data-target="#extend-validity" onclick="extendValidityForCourse(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\',\'' + data['enrolled_users'][i]['bs_end_date'] + '\',\'' + data['enrolled_users'][i]['us_name'] + '\')">Change Validity Period</a>';
                // userHtml += '                </li>';
                // userHtml += '                <li id="complete_btn_' + data['enrolled_users'][i]['bs_user_id'] + '">';
                // if (data['enrolled_users'][i]['bs_percentage'] >= '0' && data['enrolled_users'][i]['bs_percentage'] != '100') {
                //     userHtml += '                    <a href="javascript:void(0)" onclick="setAsComplete(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\',\'' + data['enrolled_users'][i]['us_name'] + '\')">' + lang('set_as_complete') + '</a>';
                // }
                // userHtml += '                </li>';


                // userHtml += '                <li id="reset_btn_' + data['enrolled_users'][i]['bs_user_id'] + '">';
                // if (data['enrolled_users'][i]['bs_percentage'] >= '0' && data['enrolled_users'][i]['bs_percentage'] != '0') {
                //     userHtml += '                    <a href="javascript:void(0)" onclick="resetResult(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\',\'' + data['enrolled_users'][i]['us_name'] + '\')">' + lang('reset_result') + '</a>';
                // }
                // userHtml += '                </li>';


                // userHtml += '                <li id="status_btn_' + data['enrolled_users'][i]['bs_user_id'] + '">';
                // var cb_status = (+data['enrolled_users'][i]['bs_approved'] == 1) ? 'suspend' : 'approve';
                // userHtml += '                    <a href="javascript:void(0);" onclick="changeSubscriptionStatus(\'' + data['enrolled_users'][i]['bs_user_id'] + '\', \'' + data['enrolled_users'][i]['bs_bundle_id'] + '\', \'' + ((data['enrolled_users'][i]['bs_approved'] == '1') ? '0' : '1') + '\')" >' + lang(cb_status) + '</a>';
                // userHtml += '                </li>';
                // userHtml += '                <li>';
                // userHtml += '                    <a href="javascript:void(0)"  onclick="resetCertificate(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\',\'' + data['enrolled_users'][i]['us_name'] + '\')">Reset Certificates</a>';
                // userHtml += '                </li>';
            // }
            // if (__studentEnrollPrivilege.pdelete() == true) {
                // userHtml += '                <li>';
                // userHtml += '                    <a href="javascript:void(0)"  onclick="removeUserFromCourse(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\',\'' + data['enrolled_users'][i]['us_name'] + '\')">' + lang('remove_from_bundle') + '</a>';
                // userHtml += '                </li>';
            // }
                // var forumBlockLabel     = +data['enrolled_users'][i]['bs_forum_blocked']==0?'Block from Forum':'Unblock from Forum';
                // userHtml += '                <li>'; 
                // userHtml += '                    <a href="javascript:void(0)"  onclick="blockUserFromForum(\'' + data['enrolled_users'][i]['bs_bundle_id'] + '\',\'' + data['enrolled_users'][i]['bs_user_id'] + '\','+data['enrolled_users'][i]['bs_forum_blocked']+')">' + forumBlockLabel + '</a>';
                // userHtml += '                </li>';

            userHtml += '            </ul>';
            userHtml += '        </div>';
            userHtml += '    </div>';
            userHtml += '</div> ';
        }
    }
    return userHtml;
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
        paginationHtml += '<ul class="pagination-wrapper pagination">';
        paginationHtml += generatePagination(offset, totalPage);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
    } else {
        $('#pagination_wrapper').html('');
    }
}
$(document).on('click', '.locate-page', function()  {
    __offset = $(this).attr('data-page');
    getSubscribers();
});

function clearUserCache() {
    __user_selected = new Array();
    __course_selected = new Array();
    $("#selected_user_count").html('');
    $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
    $('#course_bulk').hide();
}

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
            getSubscribers();
        }
    } else {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = (typeof __offset == 'undefined') ? 1 : __offset;
            getSubscribers();
        }
    }
}
var enrollUserAvailable = $('.user-listing-row:visible').length;
if(enrollUserAvailable<=0){
    $('.select-all-style').hide();
}else{
    $('.select-all-style').show();
}

function exportSubscribersReport(){
    var keyword     = getQueryStringValue('keyword');
    var branch_id   = getQueryStringValue('branch_id');
    var param = {
        "filter": __filter_dropdown,
        "course_id": __course_id,
        "keyword": keyword,
        "institute_id": __institute_id,
        'batch_id': __batch_id,
        "branch_id" : branch_id
    };
    
    param               = JSON.stringify(param);
    var pathname        = '/admin/bundle/export_subscriptions';
    var link            = window.location.protocol + "//" + window.location.host + pathname;
    window.location     = link + '/' + btoa(param);
}

function removeUserFromBundle(bundle_id, user_id = '',username='') {

    if (__user_selected.length > 0 && !user_id) {
        var user_ids = __user_selected;
    } else {
        if (user_id != '' && user_id > 0) {
            var user_ids = [];
            user_ids.push(user_id);
        }else{
            return false;
        }
    }

    var messageObject = {
        'body': 'Are you sure to remove student from this Bundle',
        'button_yes': 'OK',
        'button_no': 'CANCEL',
        'continue_params': {
            "user_id": user_ids,
            "bundle_id": bundle_id,
            "student_name":username
        },
    };
    callback_warning_modal(messageObject, removeUserFromBundleConfirmed);
}

function removeUserFromBundleConfirmed(params) {
    __user_selected         = new Array();
    var user_id             = params.data.user_id;
    var bundle_id           = params.data.bundle_id;
    var student_name        = params.data.student_name;
    console.log(user_id);
    $.ajax({
        url: admin_url + 'bundle/delete_subscription',
        type: "POST",
        data: {
            "user_id": JSON.stringify(user_id),
            "bundle_id": bundle_id,
            "is_ajax": true,
            "bundle_title":__course_title,
            "student_name":student_name
        },
        success: function (response) {

            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if(__totalUsers > 0){
                    __totalUsers = __totalUsers-user_id.length;
                    var totalUsersHtml = __totalUsers + ' ' + ((__totalUsers == 1) ? "Student" : "Students"); //data_user['enrolled_users'].length + ' / ' + data_user['total_enrolled'] + ' ' + ((data_user['total_enrolled'] == 1) ? "Student" : "Students");
                    
                    $('.user-count').html(totalUsersHtml);

                } else {
                    $('.user-count').html('No Students');
                  
                    // $('#user_row_wrapper').html(renderPopUpMessage('error', 'No Students found.'));
                    // $('.select-all-style').css('display', 'none');
                    // clearUserCache();
                }
                for (var i = 0; i < user_id.length; i++) {
                    $('#user_row_' + user_id[i]).remove();
                }

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
            clearUserCache();
        }
    });
}

