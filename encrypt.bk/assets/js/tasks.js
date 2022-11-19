var __task_path = '';
var __img_format = new Array('jpg', 'jpeg', 'png', 'gif');
var __upload_path = '';
var __uploaded_files = '';
var timeOut = '';
var __task_id = null;


$(document).on('keyup', '#task_keyword', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getTasks();
    }, 600);
});

var __taskObject = {};
$(document).ready(function () {

    var filter = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');
    var priority = getQueryStringValue('priority');
    var branch_id = getQueryStringValue('branch_id');
    var tasks_id = getQueryStringValue('tasks_id');
    var userAdd    = getQueryStringValue('add');
    if (filter != '') {
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    }
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#task_keyword').val(keyword);
    }
    /*if (priority != '') {
        __priority = priority;
        var institude_code = $('#filter_institute_' + priority).text().substring(0, $('#filter_institute_' + priority).text().indexOf('-'));
        if (institude_code == '') {
            $('#filter_institute').html('All Institutes <span class="caret"></span>');
            __priority = '';
            var messageObject = {
                'body': 'Institute does not exists, Click OK to load from all institutes',
                'button_yes': 'OK',
                'prevent_button_no': true
            };
            callback_warning_modal(messageObject, refreshListingLaunch);
        } else {
            $('#filter_institute').html($('#filter_institute_' + priority).text().substring(0, $('#filter_institute_' + priority).text().indexOf('-')) + '<span class="caret"></span>');
        }
    }*/
    if (branch_id != '') {
        __branch_id = branch_id;
        $('#filter_branch').html($('#filter_branch_' + branch_id).text().substring(0, $('#filter_branch_' + branch_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    if (tasks_id != '') {
        __tasks_id = tasks_id;
        $('#filter_batch').html('<span class="dropdown-filter" title="' + $('#filter_batch_' + tasks_id).text() + '">' + $('#filter_batch_' + tasks_id).text() + '</span><span class="caret batch-carrot"></span>');
    }
    if(userAdd == 'true') {
        $("#add_new_tasks").trigger("click");
    }

    $('#create_new_group_cancel, #group_name').hide();

    // $('.profile-pic').initial({
    //     width: 40,
    //     height: 40,
    //     fontSize: 20,
    //     fontWeight: 400
    // });
    // $('#task_row_wrapper').bind('DOMSubtreeModified', function (e) {
    //     $('.profile-pic').initial({
    //         width: 40,
    //         height: 40,
    //         fontSize: 20,
    //         fontWeight: 400
    //     });
    // });

    // $('#filter_batch_div').css('display', 'none');
    var tasks = {};
    __taskObject = $.parseJSON(__tasks);
    tasks = $.parseJSON(__tasks);
    if (tasks.length > 0) {
        $('#task_row_wrapper').html(renderTasksHtml(JSON.stringify(tasks)));
    } else {
        $('.user-count').html("No Tasks");
        $('#task_row_wrapper').html(renderPopUpMessagePage('error', 'No Tasks found.'));
        $('#popUpMessagePage .close').css('display', 'none');
    }

    setBulkAction();
    // console.log('values called');
});

$(document).on('change', '#import_task', function (e) {
    //console.log(e.currentTarget.files[0]);
    $('#percentage_bar').hide();
    __uploaded_files = e.currentTarget.files[0];
    __uploaded_files.extension = __uploaded_files.name.split('.').pop();
    $('#upload_task_file').val(__uploaded_files.name);
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
        var uploadURL = admin_url + 'user/import_tasks'

        var fileObj = new processFileName(__uploaded_files.name);
        var param = new Array;
        param["file_name"] = fileObj.uniqueFileName();
        param["extension"] = fileObj.fileExtension();
        param["file"] = __uploaded_files;
        param.priority = $('#student_institute_upload').val();
        param.processing = 'importing_task_process';
        uploadFiles(uploadURL, param, uploadUserCompleted);
    } else {
        lauch_common_message('Invalid File', 'Choose proper file to upload');
        return false;
    }
}

$('#addusers').on('hidden.bs.modal', () =>  {

    __uploaded_files = '';
    $('#upload_task_file').val('');
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
    var uploadURL = admin_url + "user/save_task_image";
    var fileObj = new processFileName(__uploading_file[i].name);
    var param = new Array;
    param["file_name"] = fileObj.uniqueFileName();
    param["extension"] = fileObj.fileExtension();
    param["file"] = __uploading_file[i];
    param["id"] = task_id;
    uploadFiles(uploadURL, param, uploadUserImageCompleted);
});


function uploadUserImageCompleted(response) {
    var data = $.parseJSON(response);
    $('#task_image').attr('src', data.task_image)
}

$(document).on('click', '#basic-addon2', () =>  {
    var task_keyword = $('#task_keyword').val().trim();
    if (task_keyword == '') {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
    }
    else {
        __offset = 1;
        getTasks();
    }
});



$(document).on('click', '#add_new_tasks', () =>  {
    cleanPopUpMessage();
    // $('#student_name').val('');
    // $('#student_email').val('');
    // $('#phone_number').val('');
    // $('#student_password').val('');
    
    $('#send_mail').prop('checked', false);
});

function extendValidityForCourse(user_id, course_title, course_validity, validity_date) {
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
        "task_id": task_id,
        "course_title": course_title,
        "user_id": user_id
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
            "task_name": task_name,
            "course_title": params.data.course_title,
            "task_id": params.data.task_id,
            "task_email": task_email,
            "user_id": params.data.user_id
        },
        success: function (response) {
            var data = $.parseJSON(response);
            //$('#subscription_status_'+params.data.user_id).removeClass('prfle-expireRed').removeClass('prfle-expire').addClass('prfle-expire').html(data.date_of_expiry);
            location.reload();
            //$('#extend-validity').modal('hide');
        }
    });
}

function setAsComplete(task_id, user_id) {
    var task_name = $('#task_course_' + task_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_set_complete_course'));
    $('#confirm_box_content_course').html(lang('confirm_set_complete_for_task_course') + '<b>' + task_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "task_id": task_id,
        "user_id": user_id
    }, setAsCompleteConfirmed);
}

function setAsCompleteConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/set_as_complete',
        type: "POST",
        data: {
            "task_id": params.data.task_id,
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                $('#publish-course').modal('hide');
                $('#progress_bar_task_' + params.data.task_id + ' .link-style').html('100%');
                $('#progress_bar_task_' + params.data.task_id + ' .progress-bar').css('width', '100%');
                $('#progress_bar_task_' + params.data.task_id + ' .sr-only').html('100% ' + lang('complete'));
            } else {
                $('#confirm_box_title').html(data.message);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function changeSubscriptionStatus(task_id, user_id, status) {
    var actionLabel = 'approval';
    var actionMessage = 'message_approve_task';
    var task_name = $('#task_course_' + task_id).attr('data-name');
    if (status == 0) {
        actionLabel = 'suspend';
        actionMessage = 'message_suspend_task';
    }
    $('#confirm_box_title_course').html(lang('confirm') + ' ' + lang(actionLabel));
    $('#confirm_box_content_course').html(lang(actionMessage) + ' <b>' + task_name + '</b>. ');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "task_id": task_id,
        "user_id": user_id,
        "status": status
    }, changeSubscriptionStatusConfirmed);
}

function changeSubscriptionStatusConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/change_subscription_status',
        type: "POST",
        data: {
            "task_id": params.data.task_id,
            "user_id": params.data.user_id,
            "status": params.data.status,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                var action_list = '';
                if (params.data.status == 0) {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + params.data.task_id + '\', \'' + params.data.user_id + '\', \'1\' )" href="javascript:void(0);">' + lang('approve') + '</a>';
                } else {
                    action_list = '<a onclick="changeSubscriptionStatus(\'' + params.data.task_id + '\', \'' + params.data.user_id + '\', \'0\' )" href="javascript:void(0);">' + lang('suspend') + '</a>';
                }
                $('#status_btn_' + params.data.task_id).html(action_list);
                $('#publish-course').modal('hide');
            } else {
                $('#confirm_box_title_course').html(data.message);
                $('#confirm_box_content_course').html('');
            }
        }
    });
}

function resetResult(task_id, user_id) {
    var task_name = $('#task_course_' + task_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_reset_result_course'));
    $('#confirm_box_content_course').html(lang('confirm_reset_result_for_task_course') + '<b>' + task_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "task_id": task_id,
        "user_id": user_id
    }, resetResultConfirmed);
}

function resetResultConfirmed(params) {
    $.ajax({
        url: admin_url + 'course/reset_result',
        type: "POST",
        data: {
            "task_id": params.data.task_id,
            "user_id": params.data.user_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                $('#publish-course').modal('hide');
                $('#progress_bar_task_' + params.data.task_id + ' .link-style').html('0%');
                $('#progress_bar_task_' + params.data.task_id + ' .progress-bar').css('width', '0%');
                $('#progress_bar_task_' + params.data.task_id + ' .sr-only').html('0% ' + lang('complete'));
            } else {
                $('#confirm_box_title_course').html(data.message);
                $('#confirm_box_content_course').html('');
            }
        }
    });
}

function removeUserFromCourse(task_id, user_id) {
    var task_name = $('#task_course_' + task_id).attr('data-name');
    $('#confirm_box_title_course').html(lang('confirm_delete_subscription'));
    $('#confirm_box_content_course').html(lang('confirm_delete_subscription_for_task') + '<b>' + task_name + '<br />');
    $('#confirm_box_ok_course').unbind();
    $('#confirm_box_ok_course').click({
        "task_id": task_id,
        "user_id": user_id
    }, removeUserFromCourseConfirmed);
}


var __task_selected = new Array();



$(document).on('click', '.user-checkbox-parent', () =>  {
    var parent_check_box = this;
    __task_selected = new Array();
    $('.user-checkbox').not(':disabled').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.user-checkbox').not(':disabled').each(function (index) {
            __task_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if (__task_selected.length > 1) {
        $("#selected_task_count").html(' (' + __task_selected.length + ')');
        $("#task_bulk").css('display', 'block');
    } else {
        $("#selected_task_count").html('');
        $("#task_bulk").css('display', 'none');
    }
});




var __filter_dropdown = 'active';
var __priority_dropdown = '';
var __user_id = '';
var __priority = '';
var __branch_id = '';
var __tasks_id = '';
var __gettingUserInProgress = false;


var __totalTasks = 0;
var __shownUsers = 0;

function getTasks() {
    __task_id = null;
    if (__gettingUserInProgress == true) {
        return false;
    }
    __gettingUserInProgress = true;
    //$('#loadmorebutton').html('Loading..');
    var keyword = $('#task_keyword').val().trim();

    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || __priority != '' || __branch_id != '' || __tasks_id != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (__priority_dropdown != '') {
            link += '&priority=' + __priority_dropdown;
        }
        
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__priority != '') {
            link += '&priority=' + __priority;
        }
        if (__branch_id != '') {
            link += '&branch_id=' + __branch_id;
        }
        if (__tasks_id != '') {
            link += '&tasks_id=' + __tasks_id;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }

    //console.log();
    $.ajax({
        url: admin_url + 'tasks/tasks_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "filter": __filter_dropdown,
            "priority": __priority_dropdown,
            "branch_id": __branch_id,
            "tasks_id": __tasks_id,
            "keyword": keyword,
            'limit': __limit,
            'offset': __offset
        },
        success: function (response) {
            $('.user-checkbox-parent').prop('checked', false);
             __task_selected = new Array();

            var data = JSON.parse(response);

            var remainingUser = 0;
            //$('#loadmorebutton').hide();
            clearTaskCache();
            renderPagination(__offset, data.total_tasks);
            if (data.tasks.length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalTasks = data.total_tasks;
                    __shownUsers = data.tasks.length;
                    remainingUser = (data.total_tasks - data.tasks.length);
                    var totalUsersHtml = data.total_tasks + ' ' + ((data.total_tasks == 1) ?  "Task" :  "Tasks"); //data.users.length + ' / ' + data.total_tasks + ' ' + ((data.total_tasks == 1) ?  "Task" :  "Tasks");
                    $('.user-count').html(totalUsersHtml);
                    $('#task_row_wrapper').html(renderTasksHtml(JSON.stringify(data.tasks)));
                } else {
                    __totalTasks = data.total_tasks;
                    __shownUsers = ((__offset - 2) * data.limit) + data.length;
                    remainingUser = (data.total_tasks - (((__offset - 2) * data.limit) + data.length));
                    var totalUsersHtml = data.total_tasks + ' Tasks'; //(((__offset - 2) * data.limit) + data.users.length) + ' / ' + data.total_tasks + ' Tasks';
                    $('.user-count').html(totalUsersHtml);
                    $('#task_row_wrapper').html(renderTasksHtml(JSON.stringify(data.tasks)));
                }
            } else {
                $('.user-count').html("No Tasks");
                $('#task_row_wrapper').html(renderPopUpMessagePage('error', 'No Tasks found.'));
                $('#popUpMessagePage .close').css('display', 'none');
            }
            if (data.show_load_button == true) {
                //$('#loadmorebutton').show();
            }
            // if (data.batches.length > 0) {
            //     $('#filter_batch_div').attr('style', '');
            //     var batchHtml = '<li><a href="javascript:void(0)" id="filter_batch_all" onclick="filter_batch(\'all\')">All Batches </a></li>';
            //     for (var i in data.batches) {
            //         var batchNameToolTip = '';
            //         if (data.batches[i].batch_name.length > 15) {
            //             batchNameToolTip = 'data-toggle="tooltip" title="' + data.batches[i].batch_name + '"';
            //         }
            //         batchHtml += '<li><a href="javascript:void(0)" id="filter_batch_' + data.batches[i].id + '" onclick="filter_batch(' + data.batches[i].id + ')" ' + batchNameToolTip + '>' + data.batches[i].batch_name + '</a></li>';
            //     }
            //     $('#batch_filter_list').html(batchHtml);
            //     if (__tasks_id == '') {
            //         $('#filter_batch').html('All Batches <span class="batch-carrot caret"></span>');
            //     }
            // } else {
            //     $('#filter_batch_div').css('display', 'none');
            // }
            remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
            //$('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
            __gettingUserInProgress = false;
        }
    });
}

function loadMoreUsers() {
    getTasks();
}

function renderTasksHtml(response) {
    // $("#selected_task_count").html('');
    var data = JSON.parse(response);
    // console.log(data);
    var userHtml = '';
    if (data.length > 0) {
        for (var i = 0; i < data.length; i++) {
            userHtml += '<div class="rTableRow user-listing-row" id="task_row_' + data[i].id + '" data-name="' + data[i].ft_tittle + '" data-email="' + data[i].ft_description + '">';

            userHtml += renderTaskRow(data[i]);

            userHtml += '</div>';
        }
    }
    return userHtml;
}

function prioritychange(id, priority)
{
    $('#prioritychange'+id).html(`<select id="priorityupdated${id}" class="pull-right label" onchange="changePriority(this, ${id})">
                                    <option value="4" class="bg-primary">Low</option>
                                    <option value="3" class="bg-success">Normal</option>
                                    <option value="2" class="bg-warning">High</option>
                                    <option value="1" class="bg-danger">Urgent</option>
                                    </select>`);
    $('#priorityupdated'+id).val(priority);
    if(priority == '0'){
        $('#priorityupdated'+id).attr("style","background-color: #17a2b8!important");
    }else if(priority == '1'){
        $('#priorityupdated'+id).attr("style","background-color: #dc3545!important");
    }else if(priority == '2'){
        $('#priorityupdated'+id).attr("style","background-color: #ffc107!important");
    }else if(priority == '3'){
        $('#priorityupdated'+id).attr("style","background-color: #28a745!important");
    }else if(priority == '4'){
        $('#priorityupdated'+id).attr("style","background-color: #007bff!important");
    }
    
}


function taskStatusChange(id, status = '')
{ 
    $('#taskStatusChange'+id).html(`<select id="taskStatusChanged${id}" class="pull-right label" onchange="taskStatusChangecolor(this, ${id})">
                                        <option value="new" class="bg-info">New</option>
                                        <option value="pending" class="bg-danger">Pending</option>
                                        <option value="progress" class="bg-warning">On Progress</option>
                                        <option value="completed" class="bg-success">Completed</option>
                                    </select>`);
    $('#taskStatusChanged'+id).val(status);
    if (status == 'completed') {
        $('#taskStatusChanged'+id).attr("style","background-color: #28a745!important");
    } else if (status == 'pending') {
        $('#taskStatusChanged'+id).attr("style","background-color: #ffc107!important");
    }else if (status == 'progress') {
        $('#taskStatusChanged'+id).attr("style","background-color: #17a2b8!important");
    } else {
        $('#taskStatusChanged'+id).attr("style","background-color: #007bff!important");
    }
}

function taskStatusChangecolor(el, taskId = false){

    var status = typeof el == 'string' ? el : el.value;
    if(taskId){
        $.ajax({
            url: admin_url + 'tasks/changeStatus',
            type: "POST",
            data: {
                "taskId": taskId,
                "status": status,
                "is_ajax": true
            },
            success: function (response) {
                
                if (response.error == false) {
                    $('#task_row_' + taskId).html(renderTaskRow(response.tasks));
                    toastr["success"]('',response.message);
                    refreshListing();
                } else {
                    toastr["warning"]('',response.message);
                }
            }
        });
    }
}

$('#add_new_tasks').on('click', ()=> {
    //task_row_wrapper
    __lastTaskId++;
    var label_class         = '';
    var markAs              = 'Completed';
    var newStatus           = 'completed';
    var priorityClass       = 'success';
    var priorityLabel       = 'Normal';
    var assigneDetails      = {'us_image':'default.jpg','us_name':'Assignee'};
    var data                = {'id':__lastTaskId,'ft_tittle':'','ft_priority':'3'};
    $('#popUpMessagePage').remove();
    var newTskaHtml = `
                        <div class="rTableRow user-listing-row" id="task_row_${__lastTaskId}" data-name="qwerty" data-email="qwerty">
                            <div class="rTableCell" style="min-width: 400px;">
                                <div class="d-flex align-center">
                                    <div class="task-status-mark ${label_class}" data-toggle="tooltip" title="Mark as ${markAs}" data-placement="right" data-original-title="Mark as ${markAs}" onclick="taskStatusChangecolor('${newStatus}', ${__lastTaskId})">
                                        <svg class="MiniIcon CheckMiniIcon TaskRowCompletionStatus-checkIcon TaskRowCompletionStatus-checkIcon--withSpreadsheetGridEnabled" viewBox="0 0 24 24" style="fill: #828282;width: 16px;height: 16px;"><path d="M9.5,18.2c-0.4,0.4-1,0.4-1.4,0l-3.8-3.8C4,14,4,13.4,4.3,13s1-0.4,1.4,0l3.1,3.1l8.6-8.6c0.4-0.4,1-0.4,1.4,0s0.4,1,0,1.4 L9.5,18.2z"></path></svg>
                                    </div>
                                    <span id="editinline_${__lastTaskId}">
                                        <textarea class="form-control tasktext" onkeypress="return preventSpecialCharector(event)" onfocusout="createTask(${__lastTaskId},this.value)" id="edittask_tittle${__lastTaskId}" style="width: 550px;"></textarea>
                                    </span>
                                </div>
                            </div>
                            <div class="rTableCell" style="width:170px" id="duedateeditinline_${__lastTaskId}">
                                <div class="task-due-date">
                                    <div class="date-icon">
                                        <svg style="width: 16px;height: 16px;fill: #636363;" class="Icon CalendarIcon" focusable="false" viewBox="0 0 32 32"><path d="M24,2V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v1H10V1c0-0.6-0.4-1-1-1S8,0.4,8,1v1C4.7,2,2,4.7,2,8v16c0,3.3,2.7,6,6,6h16c3.3,0,6-2.7,6-6V8C30,4.7,27.3,2,24,2z M8,4v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4h12v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4c2.2,0,4,1.8,4,4v2H4V8C4,5.8,5.8,4,8,4z M24,28H8c-2.2,0-4-1.8-4-4V12h24v12C28,26.2,26.2,28,24,28z"></path></svg>
                                    </div>
                                    <div class="due-date-info">
                                        <div class="due-title">Due Date</div>
                                        <div class="due-date">11 Jul</div>
                                    </div>
                                    <div class="assignee-close" >&times;</div>
                                </div>
                            </div>
                            <div class="rTableCell">
                                <div class="assignee-list">
                                    <div class="d-flex align-center" onclick="assignee_search(${__lastTaskId})" id="selectedTaskElement_${__lastTaskId}">
                                        <div class="assignee-avatar">
                                            <img class="img-circle" src="${__userpath}${assigneDetails.us_image}">
                                        </div>
                                        <div class="assignee-list-info" id="assignee_name_${__lastTaskId}">${assigneDetails.us_name}</div>
                                        <div class="assignee-close" onclick="removeAssignee(${__lastTaskId})">&times;</div>
                                    </div>
                                    <div id="dropdownList_${__lastTaskId}" class="assignee-dropdown-content">
                                        <input type="text" placeholder="Search.." id="serachinput_${__lastTaskId}" onkeyup="filterFunction(${__lastTaskId})" class="myInput">
                                        <div class="search-items" id="searchItems_${__lastTaskId}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="rTableCell" style="width:100px">
                                <div class="col-sm-12" id="prioritychange${__lastTaskId}">
                                    <label class="pull-right label-${priorityClass}" id="actionp_class_${__lastTaskId}" onclick="prioritychange(${__lastTaskId}, '${data.ft_priority}')">${priorityLabel}</label>
                                </div>
                            </div>
                            <div class="td-dropdown rTableCell" style="padding-bottom: 3px !important;">
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown">
                                        <span class="label-text">
                                            <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu" id="task_action_${__lastTaskId}">
                                        <li>
                                            <a href="javascript:void(0);" onclick="viewTask(${__lastTaskId})">View</a>
                                        </li>

                                        <!--<li>
                                            <a href="javascript:void(0);" onclick="editTask(${__lastTaskId}, '${data.ft_tittle}')">${'Edit'}</a>
                                        </li>-->

                                        <li>
                                            <a href="javascript:void(0);" onclick="sendMessageToUser(${__lastTaskId})"> Remind Assignee</a>
                                        </li>
                        
                                        <li>
                                                <a href="javascript:void(0);" id="delete_btn_${__lastTaskId}" onclick="deleteTask(${__lastTaskId},'${data.ft_tittle}')" >${lang('delete')}</a>
                                        </li>
                        
                                        <li>
                                            <a href="javascript:void(0);" id="restore_btn_${__lastTaskId}" onclick="restoreTask(${__lastTaskId}, '${data.ft_tittle}')" >${lang('restore')}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>`;
    $('#task_row_wrapper').prepend(newTskaHtml);
});

function renderTaskRow(data) {
    
    var userHtml = '';
    if (data) {

        var priorityLabel;
        var priorityClass;
        if(data.ft_priority == '0'){
            priorityLabel = 'None';
            priorityClass = 'info';
        }else if(data.ft_priority == '1'){
            priorityLabel = 'Urgent';
            priorityClass = 'danger';
        }else if(data.ft_priority == '2'){
            priorityLabel = 'High';
            priorityClass = 'warning';
        }else if(data.ft_priority == '3'){
            priorityLabel = 'Normal';
            priorityClass = 'success';
        }else if(data.ft_priority == '4'){
            priorityLabel = 'Low';
            priorityClass = 'primary';
        }

        var label_class = '';
        var action_class = 'label-danger';
        var markAs = 'Completed';
        var newStatus = 'completed';
        var action = lang('deleted');
        //case if task is not deleted
        if (data.ft_deleted == 0) {
            item_deleted = '';
            if (data.ft_status == 'completed') {
                action_class = 'label-success';
                label_class = 'active';
                markAs      = 'In Complete';
                newStatus = 'pending';
                action = 'Completed';
            } else if (data.ft_status == 'pending') {
                action_class = 'label-warning';
                label_class = '';
                action = 'Pending';
            }else if (data.ft_status == 'progress') {
                action_class = 'label-info';
                label_class = '';
                action = 'On Progress';
            } else {
                action_class = 'label-primary';
                label_class = '';
                action = 'New';
            }
        }
        var assigneDetails = {};
        if(data.ft_assignee_details){
            assigneDetails = JSON.parse(data.ft_assignee_details);
        }else{
            assigneDetails.us_image = 'default.jpg';
            assigneDetails.us_name = 'Assignee';
        }
        
        userHtml += '    <div class="rTableCell" style="min-width: 400px;">';
        
        userHtml += `   <div class="d-flex align-center">
                            <div class="task-status-mark ${label_class}" data-toggle="tooltip" title="Mark as ${markAs}" data-placement="right" data-original-title="Mark as ${markAs}" onclick="taskStatusChangecolor('${newStatus}', ${data.id})">
                                <svg class="MiniIcon CheckMiniIcon TaskRowCompletionStatus-checkIcon TaskRowCompletionStatus-checkIcon--withSpreadsheetGridEnabled" viewBox="0 0 24 24" style="fill: #828282;width: 16px;height: 16px;"><path d="M9.5,18.2c-0.4,0.4-1,0.4-1.4,0l-3.8-3.8C4,14,4,13.4,4.3,13s1-0.4,1.4,0l3.1,3.1l8.6-8.6c0.4-0.4,1-0.4,1.4,0s0.4,1,0,1.4 L9.5,18.2z"></path></svg>
                            </div>
                    `;
        
//<svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="16px" height="18px" viewBox="0 0 16 18" enable-background="new 0 0 16 18" fill="#64277d" xml:space="preserve"><g> <path d="M8,1.54v0.5c1.293,0.002,2.339,1.048,2.341,2.343C10.339,5.675,9.293,6.721,8,6.724C6.707,6.721,5.66,5.675,5.657,4.382 C5.66,3.088,6.707,2.042,8,2.04V1.54v-0.5c-1.846,0-3.342,1.496-3.342,3.343c0,1.845,1.497,3.341,3.342,3.341 c1.846,0,3.341-1.496,3.341-3.341C11.341,2.536,9.846,1.04,8,1.04V1.54z"/> <path d="M2.104,16.46c0-1.629,0.659-3.1,1.727-4.168C4.899,11.225,6.37,10.565,8,10.565s3.1,0.659,4.168,1.727 c1.067,1.068,1.727,2.539,1.727,4.168h1c0-3.808-3.087-6.894-6.895-6.895c-3.808,0-6.895,3.087-6.895,6.895H2.104z"/></g></svg>
        userHtml += `<span id="editinline_${data.id}"><span onclick="editinline(${data.id},'${data.ft_tittle}')" style="width: 80%;">${data.ft_tittle}</span></span>`;
        //userHtml += ' <span class="list-user-name  text-left" data-toggle="tooltip" data-placement="top" title="' + data.ft_description + '">' + data.ft_description + ' </span>';
      //userHtml += ' <span class="list-user-name  text-right" data-toggle="tooltip" data-placement="top" title="' + data.ft_due_date + '">' + data.ft_due_date + ' </span>  </label>';
    
        userHtml += '    </div></div>';

        //consider the record is deleted and set the value if record deleted
        

        userHtml += `   <div class="rTableCell" style="width:170px" id="duedateeditinline_${data.id}">`;
        userHtml += `      <div class="task-due-date" id="duedatechange${data.id}"  onclick="duedatechange(${data.id},'${data.ft_due_date}')" >
                                <div class="date-icon">
                                    <svg style="width: 16px;height: 16px;fill: #636363;" class="Icon CalendarIcon" focusable="false" viewBox="0 0 32 32"><path d="M24,2V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v1H10V1c0-0.6-0.4-1-1-1S8,0.4,8,1v1C4.7,2,2,4.7,2,8v16c0,3.3,2.7,6,6,6h16c3.3,0,6-2.7,6-6V8C30,4.7,27.3,2,24,2z M8,4v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4h12v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4c2.2,0,4,1.8,4,4v2H4V8C4,5.8,5.8,4,8,4z M24,28H8c-2.2,0-4-1.8-4-4V12h24v12C28,26.2,26.2,28,24,28z"></path></svg>
                                </div>
                                <div class="due-date-info">
                                    <div class="due-title">Due Date</div>
                                    <div class="due-date">${data.ft_due_date ? data.ft_due_date : ''}</div>
                                </div>
                                <div class="assignee-close" >&times;</div>
                            </div>
                        </div>`;

        /*userHtml += `   <div class="rTableCell" style="width:150px" id="duedateeditinline_${data.id}">`;
        userHtml += `        <div class="col-sm-12" id="duedatechange${data.id}"  onclick="duedatechange(${data.id},'${data.ft_due_date}')" >${data.ft_due_date}</div>`;
        userHtml += '    </div>'; */

        userHtml += `<div class="rTableCell">
                        <div class="assignee-list">
                            <div class="d-flex align-center" onclick="assignee_search(${data.id})" id="selectedTaskElement_${data.id}">
                                <div class="assignee-avatar">
                                    <img class="img-circle" src="${__userpath}${assigneDetails.us_image}">
                                </div>
                                <div class="assignee-list-info" id="assignee_name_${data.id}">${assigneDetails.us_name}</div>
                                <div class="assignee-close" onclick="removeAssignee(${data.id})">&times;</div>
                            </div>
                            <div id="dropdownList_${data.id}" class="assignee-dropdown-content">
                                <input type="text" placeholder="Search.." id="serachinput_${data.id}" onkeyup="filterFunction(${data.id})" class="myInput">
                                <div class="search-items" id="searchItems_${data.id}"></div>
                            </div>
                        </div>
                    </div>`;


        userHtml += '    <div class="rTableCell" style="width:100px">';
        userHtml += '        <div class="col-sm-12" id="prioritychange'+data.id+'">';
        userHtml += '            <label class="pull-right label-' + priorityClass + '" id="actionp_class_' + data.id + '" onclick="prioritychange('+data.id+', \''+data.ft_priority+'\')">';
        userHtml +=                 priorityLabel;
        userHtml += '            </label>';
        userHtml += '        </div>';
        userHtml += '    </div>';


        /*userHtml += '    <div class="rTableCell" style="width:100px">';
        userHtml += '        <div class="col-sm-12" id="taskStatusChange'+data.id+'">';
        userHtml += '            <label class="pull-right label ' + action_class + '" id="actions_class_' + data.id + '" onclick="taskStatusChange('+data.id+', \''+data.ft_status+'\')">';
        userHtml +=                 action;
        userHtml += '            </label>';
        userHtml += '        </div>';
        userHtml += '    </div>';*/

        userHtml += '    <div class="td-dropdown rTableCell" style="padding-bottom: 3px !important;">';
        
        var viewDeletePrivilege = true;
       
    
            userHtml += '        <div class="btn-group lecture-control">';
            userHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            userHtml += '                <span class="label-text">';
            userHtml += '                  <i class="icon icon-down-arrow"></i>';
            userHtml += '                </span>';
            userHtml += '                <span class="tilder"></span>';
            userHtml += '            </span>';
            userHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="task_action_' + data.id + '">';
            if (data.ft_deleted == 0) {
            
            userHtml += '                <li>';
            userHtml += '                    <a href="javascript:void(0);" onclick="viewTask(\'' + data.id + '\')">' + 'View' + '</a>';
            userHtml += '                </li>';

            /*userHtml += '                <li>';
            userHtml += '                    <a href="javascript:void(0);" onclick="editTask(\'' + data.id + '\', \'' + data.ft_tittle + '\')">' + lang('edit') + '</a>';
            userHtml += '                </li>';*/

            userHtml += '                <li>';
            userHtml += '                   <a href="javascript:void(0);" onclick="sendMessageToUser(' + data.id + ')"> Remind Assignee</a>';
            userHtml += '                </li>';
            
            userHtml += '                <li>';
            userHtml += '                       <a href="javascript:void(0);" id="delete_btn_' + data.id + '" onclick="deleteTask(\'' + data.id + '\',\'' + data.ft_tittle + '\')" >' + lang('delete') + '</a>';
            userHtml += '                </li>';
                
            } else {
                if (viewDeletePrivilege == true) {
                    userHtml += '                    <li>';
                    userHtml += '                        <a href="javascript:void(0);" id="restore_btn_' + data.id + '" onclick="restoreTask(\'' + data.id + '\', \'' + data.ft_tittle + '\')" >' + lang('restore') + '</a>';
                    userHtml += '                    </li>';
                }
            }
            userHtml += '           </ul>';
            userHtml += '        </div>';
        
        userHtml += '    </div>';
    }
    return userHtml;
}
var __title     = null;
var __deuDate   = null;

function duedatechange(id, date){ 
    if(date){
    __deuDate = date;
    }
    $('#duedateeditinline_'+id).html(`<input type="text" value="${date != 'null' ? date : ''}" id="edittask_due_date${id}" class="form-control custom-date-picker hasDatepicker" readonly="true" onfocusout="inputtospanduedate(${id},this.value)">`);
    $( function() {
        $( ".hasDatepicker" ).datepicker({
            language: 'en',
            minDate: new Date(),
            defaultDate : new Date(date),
            dateFormat: 'yyyy-mm-dd',
            autoClose: true
        });
    });
    //$("#edittask_due_date"+id).click();
    //$('#edittask_due_date'+id).focus();
}

function inputtospanduedate(id, value = null){
    //console.log(value,__deuDate);
    if(value != __deuDate && value){
        
        $('#duedateeditinline_'+id).html(`
                                    <div class="task-due-date" id="duedatechange${id}"  onclick="duedatechange(${id},'${value}')" >
                                        <div class="date-icon">
                                            <svg style="width: 16px;height: 16px;fill: #636363;" class="Icon CalendarIcon" focusable="false" viewBox="0 0 32 32"><path d="M24,2V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v1H10V1c0-0.6-0.4-1-1-1S8,0.4,8,1v1C4.7,2,2,4.7,2,8v16c0,3.3,2.7,6,6,6h16c3.3,0,6-2.7,6-6V8C30,4.7,27.3,2,24,2z M8,4v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4h12v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4c2.2,0,4,1.8,4,4v2H4V8C4,5.8,5.8,4,8,4z M24,28H8c-2.2,0-4-1.8-4-4V12h24v12C28,26.2,26.2,28,24,28z"></path></svg>
                                        </div>
                                        <div class="due-date-info">
                                            <div class="due-title">Due Date</div>
                                            <div class="due-date">${value ? value : ''}</div>
                                        </div>
                                        <div class="assignee-close" >&times;</div>
                                    </div>
                                `);

            $.ajax({
                url: admin_url + 'tasks/update_task',
                type: "POST",
                data: {
                    "task_due_date": value,
                    'taskId' : id,
                    "is_ajax": true
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    if (data.error == false){
                        toastr["success"]('',"Task successfully updated");
                    }else{
                        toastr["warning"]('',"Failed to update task");
                    }
                    __title = null;
                }
            });
    }
    else
    {
        if(__deuDate){
            
            $('#duedateeditinline_'+id).html(`
                                    <div class="task-due-date" id="duedatechange${id}"  onclick="duedatechange(${id},'${value}')" >
                                        <div class="date-icon">
                                            <svg style="width: 16px;height: 16px;fill: #636363;" class="Icon CalendarIcon" focusable="false" viewBox="0 0 32 32"><path d="M24,2V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v1H10V1c0-0.6-0.4-1-1-1S8,0.4,8,1v1C4.7,2,2,4.7,2,8v16c0,3.3,2.7,6,6,6h16c3.3,0,6-2.7,6-6V8C30,4.7,27.3,2,24,2z M8,4v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4h12v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4c2.2,0,4,1.8,4,4v2H4V8C4,5.8,5.8,4,8,4z M24,28H8c-2.2,0-4-1.8-4-4V12h24v12C28,26.2,26.2,28,24,28z"></path></svg>
                                        </div>
                                        <div class="due-date-info">
                                            <div class="due-title">Due Date</div>
                                            <div class="due-date">${value ? value : ''}</div>
                                        </div>
                                        <div class="assignee-close" >&times;</div>
                                    </div>
                                `);
        }
    }
}

function editinline(id, title){
    if(title){
        __title = title;
    }

    $('#editinline_'+id).html(`<textarea class="form-control tasktext" type="text" onkeypress="return preventSpecialCharector(event)" onfocusout="inputtospan(${id},this.value)" id="edittask_tittle${id}" style="width: 550px;">${title}</textarea>`);
    $('#edittask_tittle'+id).focus();
}

function inputtospan(id, value){

    if(!value){
        $('#editinline_'+id).html(`<span onclick="editinline(${id},'${value}')" style="width: 80%;">${__title}</span>`);
        return false;
    }
    $('#editinline_'+id).html(`<span onclick="editinline(${id},'${value}')" style="width: 80%;">${value}</span>`);

   
    if(__title != value){

        $.ajax({
            url: admin_url + 'tasks/update_task',
            type: "POST",
            data: {
                "task_tittle": value,
                'taskId' : id,
                "is_ajax": true
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error == false){
                    toastr["success"]('',"Task successfully updated");
                }else{
                    toastr["warning"]('',"Failed to update task");
                }
                __title = null;
            }
        });
    }
}


function createTask(id, value){

    if(!value){
        $('#editinline_'+id).html(`<span onclick="editinline(${id},'${value}')" style="width: 80%;">${__title}</span>`);
        return false;
    }
    $('#editinline_'+id).html(`<span onclick="editinline(${id},'${value}')" style="width: 80%;">${value}</span>`);

   
    if(__title != value){

        $.ajax({
            url: admin_url + 'tasks/create_task',
            type: "POST",
            data: {
                "task_tittle": value,
                "task_description": '',
                "task_due_date": '',
                "faculties": '',
                "task_priority":'3',
                'send_mail': '',
                'taskId' : id,
                "is_ajax": true
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error == false){
                __lastTaskId    = data.lastTaskId;
                console.log(data.lastTaskId,'data.lastTaskId');
                    toastr["success"]('',"Task successfully created");
                }else{
                    toastr["warning"]('',"Failed to create task");
                }
                __title = null;
            }
        });
    }
}


function sendMessageToUser(task_id) {
    var task_id_temp = (typeof task_id != 'undefined') ? task_id : '';
    $('#invite-user-bulk').modal();
    $('#popUpMessage').hide();
    $('#invite_send_subject').val('');
    // $('#redactor_invite').redactor('set', '');
    $('#redactor_invite').redactor('insertion.set', '');
    // $('#redactor_invite').redactor('core.destroy');
    startTextToolbar();
    $('#message_send_button').attr('onclick', 'sendMessageBulk(' + task_id_temp + ')');
}

function sendMessageBulk(task_id_temp) {
    var task_id = (typeof task_id_temp != 'undefined') ? task_id_temp : '';

    var send_task_bulk_subject = $('#invite_send_subject').val();
    var send_task_bulk_message = btoa($('#redactor_invite').val());

    var errorCount = 0;
    var errorMessage = '';

    var task_ids = [];
    if (__task_selected.length > 0) {
        task_ids = __task_selected;
    } else {
        if (task_id != '' && task_id > 0) {
            task_ids.push(task_id);
        } else {
            errorCount++;
            errorMessage += 'Email id cannot be empty<br />';
        }
    }
    // console.log(task_ids);
    if ($.trim(send_task_bulk_subject) == '') {
        errorCount++;
        errorMessage += 'Please enter subject<br />';
    }
    if ($.trim(send_task_bulk_message) == '') {
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
            "send_task_subject": send_task_bulk_subject,
            "send_task_message": send_task_bulk_message,
            "task_ids": JSON.stringify(task_ids)
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

function filter_task_by(filter) {
    if (filter == 'all') {
        $('#task_keyword').val('');
        __priority = '';
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
    __tasks_id = '';
    __offset = 1;
    getTasks();
    $("#selected_task_count").html('');
    __task_selected = new Array();
    $("#task_bulk").css('display', 'none');
    setBulkAction();
}


function priority_task_by(priority){
    
    if(priority == 'none'){
        __priority_dropdown = '0';
    }else if(priority == 'urgent'){
        __priority_dropdown = '1';
    }else if(priority == 'high'){
        __priority_dropdown = '2';
    }else if(priority == 'normal'){
        __priority_dropdown = '3';
    }else if(priority == 'low'){
        __priority_dropdown = '4';
    }else{
        __priority_dropdown = '';
    }

    
    $('#priority_dropdown_text').html($('#priority_dropdown_list_' + priority).text() + '<span class="caret"></span>');
    __tasks_id = '';
    __offset = 1;
    getTasks();
    $("#selected_task_count").html('');
    __task_selected = new Array();
    $("#task_bulk").css('display', 'none');
    setBulkAction();
}

function setBulkAction() {
    var task_bulk = '';
    var bulk_activate = (__filter_dropdown == "not-approved") ? 'Approve' : 'Activate';
    task_bulk += '<span class="dropdown-tigger" data-toggle="dropdown">';
    task_bulk += '  <span class="label-text">';
    task_bulk += '  Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->';
    task_bulk += '  </span>';
    task_bulk += '  <span class="tilder"></span>';
    task_bulk += '</span>';
    task_bulk += '<ul class="dropdown-menu pull-right" role="menu">';

    if (__filter_dropdown == "deleted" && __task_privilege.pdelete() == true) {
        task_bulk += '                    <li>';
        task_bulk += '                        <a href="javascript:void(0);" onclick="restoreTaskBulk()" >' + lang('restore') + '</a>';
        task_bulk += '                    </li>';
    } else {
        if (__task_privilege.view() == true) {
            // task_bulk   += '     <li>';
            // task_bulk   += '         <a href="javascript:void(0)" id="invite_task_message">Send Message</a> ';
            // task_bulk   += '     </li>';
            task_bulk += ' <li>';
            task_bulk += ' <a href="javascript:void(0);" onclick="sendMessageToUser()">' + lang('send_message') + '</a>';
            task_bulk += ' </li>';
        }

        if (__filter_dropdown != "deleted" && __task_privilege.pdelete() == true) {
            task_bulk += '     <li>';
            task_bulk += '         <a href="javascript:void(0)" onclick="deleteTaskBulk()" > Delete </a>';
            task_bulk += '     </li>';
        }
        if (__filter_dropdown != "active" && __task_privilege.edit() == true) {
            var act_status = (bulk_activate.toLowerCase() == 'approve') ? 2 : 1;
            task_bulk += '     <li>';
            task_bulk += '         <a href="javascript:void(0)" onclick="changeUserStatusBulk(\'Are you sure to ' + bulk_activate + ' the selected Tasks\', \'' + act_status + '\')" > ' + bulk_activate + ' </a>';
            task_bulk += '     </li>';
        }
        if (__filter_dropdown != "inactive" && __filter_dropdown != "not-approved" && __task_privilege.edit() == true) {
            task_bulk += '     <li>';
            task_bulk += '         <a href="javascript:void(0)" onclick="changeUserStatusBulk(\'Are you sure to Deactivate the selected Tasks\', \'0\')" > Deactivate </a>';
            task_bulk += '     </li>';
        }
    }
    task_bulk += '</ul>';
    $('#task_bulk').html(task_bulk);
}

$(document).on('click', '#searchclear', () =>  {
    __task_selected = new Array();
    $("#task_bulk").css('display', 'none');
    __offset = 1;
    getTasks();
});

function changeUserStatus(task_id, action, task_name) {
    var ok_button_text = 'ACTIVATE';
    switch (action) {
        case "deactivate":
            ok_button_text = 'DEACTIVATE';
            break;
        case "approve":
            ok_button_text = 'APPROVE';
            break;
    }
    var header_text = 'Are you sure to ' + action + ' the Task named "' + task_name + '" ?';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'task_id': task_id
        },
    };
    callback_warning_modal(messageObject, changeStatusConfirmed);
}

function changeStatusConfirmed(params) {
    var task_id = params.data.task_id;
    $.ajax({
        url: admin_url + 'user/change_status',
        type: "POST",
        data: {
            "task_id": params.data.task_id,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                if (__filter_dropdown == 'all') {

                    $('#task_row_' + task_id).html(renderTaskRow(data.user));
                } else {
                    $('#task_row_' + task_id).remove();
                    __totalTasks = __totalTasks - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalTasks == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Tasks';
                    } else {
                        var totalUsersHtml = __totalTasks + ' Tasks'; //__shownUsers + ' / ' + __totalTasks + ' Tasks';
                    }
                    $('.user-count').html(totalUsersHtml);
                }

                var messageObject = {
                    'body': 'Task status updated successfully',
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


function restoreTaskBulk() {
    var messageObject = {
        'body': 'Are you sure to <b>Restore</b> selected Tasks?',
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
    };
    callback_warning_modal(messageObject, restoreTaskBulkConfirmed);
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
        header_text = 'Are you sure to ' + action + ' the selected Tasks ?';
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
            "users": JSON.stringify(__task_selected),
            "status": params.data.status,
            "approve": params.data.approve,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.users.length > 0) {
                for (var i in data.users) {
                    if (__filter_dropdown == 'all') {
                        $('#task_row_' + data.users[i].id).html(renderTaskRow(data.users[i]));
                    } else {
                        $('#task_row_' + data.users[i].id).remove();
                        __totalTasks = __totalTasks - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalTasks <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Tasks';
                        } else {
                            var totalUsersHtml = __totalTasks + ' Tasks'; //__shownUsers + ' / ' + __totalTasks + ' Tasks';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __task_selected = new Array();
            $("#task_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Tasks status updated successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_task_count").html('');
            $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
            refreshListing();
        }
    });
}

function restoreTask(task_id, task_name) {
    var messageObject = {
        'body': 'Are you sure to restore the Task "' + task_name + '"?',
        'button_yes': 'RESTORE',
        'button_no': 'CANCEL',
        'continue_params': {
            'task_id': task_id
        },
    };
    callback_warning_modal(messageObject, restoreTaskConfirmed);
}

function restoreTaskConfirmed(params) {
    var task_id = params.data.task_id;
    $.ajax({
        url: admin_url + 'tasks/restore',
        type: "POST",
        data: {
            "taskId": params.data.task_id,
            "is_ajax": true
        },
        success: function (response) {
            
            if (response.error == false) {

                if (__filter_dropdown == 'all') {
                    $('#task_row_' + task_id).html(renderTaskRow(response.user));
                } else {
                    $('#task_row_' + task_id).remove();
                    __totalTasks = __totalTasks - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalTasks == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Tasks';
                    } else {
                        var totalUsersHtml = __totalTasks + ' Tasks'; //__shownUsers + ' / ' + __totalTasks + ' Tasks';
                    }
                    $('.user-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Task restored successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': response.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function deleteTask(task_id, task_name) {

    var messageObject = {
        'body': 'Are you sure to delete the Task "' + task_name + '"?',
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'task_id': task_id
        },
    };
    callback_warning_modal(messageObject, deleteTaskConfirmed);
}

function deleteTaskConfirmed(params) {
    var task_id = params.data.task_id;
    $.ajax({
        url: admin_url + 'tasks/delete',
        type: "POST",
        data: {
            "taskId": params.data.task_id,
            "is_ajax": true
        },
        success: function (response) {
            //var data = $.parseJSON(response);
            if (response.error == false) {
                if (__filter_dropdown == 'all') {
                    $('#task_row_' + task_id).html(renderTaskRow(response.user));
                } else {
                    $('#task_row_' + task_id).remove();
                    __totalTasks = __totalTasks - 1;
                    __shownUsers = __shownUsers - 1;
                    if (__totalTasks == 0 && __shownUsers == 0) {
                        var totalUsersHtml = 'No Tasks';
                    } else {
                        var totalUsersHtml = __totalTasks + ' Tasks'; //var totalUsersHtml = __shownUsers + ' / ' + __totalTasks + ' Tasks';
                    }
                    $('.user-count').html(totalUsersHtml);
                }
                var messageObject = {
                    'body': 'Task deleted successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': response.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}


function deleteTaskBulk() {
    var header_text = 'Are you sure to delete the selected Tasks ?';

    var messageObject = {
        'body': header_text,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status
        },
    };
    callback_warning_modal(messageObject, deleteTaskBulkConfirmed);
}

function deleteTaskBulkConfirmed(params) {
    $.ajax({
        url: admin_url + 'user/delete_task_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__task_selected),
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.users.length > 0) {
                for (var i in data.users) {
                    if (__filter_dropdown == 'all') {
                        $('#task_row_' + data.users[i].id).html(renderTaskRow(data.users[i]));
                    } else {
                        $('#task_row_' + data.users[i].id).remove();
                        __totalTasks = __totalTasks - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalTasks <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Tasks';
                        } else {
                            var totalUsersHtml = __totalTasks + ' Tasks'; //__shownUsers + ' / ' + __totalTasks + ' Tasks';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __task_selected = new Array();
            $("#task_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Tasks deleted successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_task_count").html('');
            $('.user-checkbox-parent, .user-checkbox').prop('checked', false);
            refreshListing();
        }
    });
}


$(document).on('change', '.user-course', function(){
    if ($(this).prop('checked') == true) {
        __course_selected.push($(this).val());
    } else {
        removeArrayIndex(__course_selected, $(this).val());
    }
});

function editTask(taskId){
    __task_id = taskId;
    $.ajax({
        url: admin_url+'tasks/getTask',
        type: "POST",
        data:{"is_ajax":true,'taskId':taskId},
    beforeSend: function() { /*/console.log('getting task');*/ },
        success: function(response) {

            if(response.ft_priority == '0'){
                $('#task_priority').attr("style","background-color: #17a2b8!important");
            }else if(response.ft_priority == '1'){
                $('#task_priority').attr("style","background-color: #dc3545!important");
            }else if(response.ft_priority == '2'){
                $('#task_priority').attr("style","background-color: #ffc107!important");
            }else if(response.ft_priority == '3'){
                $('#task_priority').attr("style","background-color: #28a745!important");
            }else if(response.ft_priority == '4'){
                $('#task_priority').attr("style","background-color: #007bff!important");
            }

            $('#task_tittle').val(response.ft_tittle);
            $('#task_description').val(response.ft_description);
            $('#task_due_date').val(response.ft_due_date);
            $('#task_priority').val(response.ft_priority);
            /*var selectize = $select[0].selectize;
                selectize.setValue(response.ft_assignees.split(','));
            $('#create_task').modal('show');*/
        }
    });
}

function addUserToCourseConfirmed(params) {

    var task_id = params.data.task_id;
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
                data:{"is_ajax":true,'user_ids':JSON.stringify(__course_selected)},
                beforeSend: function() { $('#enroll_task_confirmed').attr('disabled', 'disabled'); },
                success: function(response) {
                    $('#enroll_task_confirmed').removeAttr('disabled');
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
                                        "task_id": task_id,
                                        "user_ids": data['active_course_list'],
                                        "task_name" : username
                                    }
                                };
                                callback_warning_modal(messageObject, addToCourseConfirmed);
                            }else{
                                lauch_common_message('Something went Wrong' , messageHtml);
                            }
                            
                        
                    }else{
                        params                  = {'data':{'task_id':task_id, 'task_name' :username, 'user_ids':data['active_course_list']}};
                        addToCourseConfirmed(params);
                    }
                    
                }
            });

       

        
    }
}
function addToCourseConfirmed(params)
{
    var task_id    = params.data.task_id;
    var username   = params.data.task_name;
    var courses    = params.data.user_ids
    $.ajax({
        url: admin_url + 'user/add_task_to_course_new',
        type: "POST",
        data: {
            "courses": JSON.stringify(courses),
            "task_id": task_id,
            "username": username,
            "is_ajax": true
        },
        success: function (response) {
            if (response != null) {

                $('#add-users-course').modal('hide');
                __course_selected = new Array();
                __task_selected = new Array();
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

$(document).on('change', '.user-groups', function ()  {
    if ($(this).prop('checked') == true) {
        __group_selected.push($(this).val());
    } else {
        removeArrayIndex(__group_selected, $(this).val());
    }
});


function changePriority(el, taskId = false){
    if(el.value == '0'){
        $(el).attr("style","background-color: #17a2b8!important");
    }else if(el.value == '1'){
        $(el).attr("style","background-color: #dc3545!important");
    }else if(el.value == '2'){
        $(el).attr("style","background-color: #ffc107!important");
    }else if(el.value == '3'){
        $(el).attr("style","background-color: #28a745!important");
    }else if(el.value == '4'){
        $(el).attr("style","background-color: #007bff!important");
    }

    if(taskId){
        $.ajax({
            url: admin_url + 'tasks/changePriority',
            type: "POST",
            data: {
                "taskId": taskId,
                "priority": el.value,
                "is_ajax": true
            },
            success: function (response) {
                
                if (response.error == false) {
                        $('#task_row_' + taskId).html(renderTaskRow(response.tasks));
                        toastr["success"]('',response.message);
                    refreshListing();
                } else {
                        toastr["warning"]('',response.message);
                }
            }
        });
    }
}

function addTask() {
    var task_tittle         = $('#task_tittle').val().trim();
    var task_description    = $('#task_description').val().trim();
    var task_due_date       = $('#task_due_date').val().trim();
    var task_priority       = $('#task_priority').val().trim();
    var faculties           = new Array();
    var facultiesSelected   = $(".facultiesSelected");
     for (i = 0; i < facultiesSelected.length; i ++){
        faculties.push(facultiesSelected.eq(i).val());
     }
    var sendMail            = $('#send_mail').prop('checked');
    sendMail                = (sendMail == true) ? '1' : '0';
    var errorCount          = 0;
    var errorMessage        = '';
    if (task_tittle == '') {
        errorCount++;
        errorMessage += 'Please Enter Task Tittle.<br />';
    } else if (task_tittle != '') {
        var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9. ]+$");
        if (!(regex.test(task_tittle))) {
            errorCount++;
            errorMessage += 'Enter valid Task Tittle.<br />';
        }

    } else {
        if (task_tittle.length > 50) {
            errorCount++;
            errorMessage += 'Task Tittle length exceed the limit.<br />';
        }
    }
    
    if(task_description == ''){
        errorCount++;
        errorMessage += 'Please Enter Task Description.<br />';
    }
    
    if (task_due_date == '') {
        errorCount++;
        errorMessage += 'Please Enter Task Due Date.<br />';
    }
    
    if (faculties == '') {
        errorCount++;
        errorMessage += 'Please Select Atleas One Faculty.<br />';
    } else {
        if (faculties.length < 1) {
            errorCount++;
            errorMessage += 'Please Select Atleas One Faculty.<br />';
        }
    }
    
    cleanPopUpMessage();
    if (errorCount > 0) {
        $('#create_task .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } else {
        $.ajax({
            url: admin_url + 'tasks/create_task',
            type: "POST",
            data: {
                "task_tittle": task_tittle,
                "task_description": task_description,
                "task_due_date": task_due_date,
                "faculties": faculties,
                "task_priority":task_priority,
                'send_mail': sendMail,
                'taskId' : __task_id,
                "is_ajax": true
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error == false) {
                    $('#create_task').modal('hide');
                    var messageObject = {
                        'body': data.message,
                        'button_yes': 'OK',
                        'prevent_button_no': true
                    };
                    callback_success_modal(messageObject);
                    setTimeout(function () {
                        //location.reload();
                        __offset = 1;
                        getTasks();
                    }, 1500);
                } else {
                    $('#create_task .modal-body').prepend(renderPopUpMessage('error', data.message));
                }
            }
        });
    }
}

/* Assignee Search Dropdown */
function assigneeSearch(id) {
    __selectedTaskElement = id;
    $('.show').removeClass('show');
    $("#dropdownLists_"+id).addClass('show');
    $('#serachinputs_'+id).focus().attr('placeholder', $('#assignee_names_'+id).text());
    
    assigneeFilters(id);
}

function assigneeFilters(id){
    var html = '';
    document.getElementById("serachinputs_"+id).value = '';
    $.grep(__assignee, function( n, i ) {
        html += `<div class="assignee-serch-item"> 
                    <div class="d-flex align-center" id="${n.id}" onclick="selectassignee(this)"> 
                        <div class="assignee-avatar">
                            <img class="img-circle" src="${__userpath}${n.us_image}">
                        </div>
                        <div class="assignee-list-info" id="assignee_names_${id}">${n.us_name}</div>
                    </div>
                </div>`;
      });
      $('#searchItems_'+id).html(html);
      __selectedTaskAssegnee = document.getElementById('selectedTaskElements_'+id);
}

function viewTask(taskId){
    __lastTaskId = taskId;
    $.ajax({
        url: admin_url+'tasks/getTask',
        type: "POST",
        data:{"is_ajax":true,'taskId':taskId},
        beforeSend: function() { /*console.log('getting task');*/ },
        success: function(response) {

            $('#view_task_tittle').text(response.ft_tittle);
            $('#view_task_description').text(response.ft_description);
            $('#view_task_due_date').text(response.ft_due_date);


            var priorityLabel;
            var priorityClass;
            if(response.ft_priority == '0'){
                priorityLabel = 'None';
                priorityClass = 'info';
            }else if(response.ft_priority == '1'){
                priorityLabel = 'Urgent';
                priorityClass = 'danger';
            }else if(response.ft_priority == '2'){
                priorityLabel = 'High';
                priorityClass = 'warning';
            }else if(response.ft_priority == '3'){
                priorityLabel = 'Normal';
                priorityClass = 'success';
            }else if(response.ft_priority == '4'){
                priorityLabel = 'Low';
                priorityClass = 'primary';
            }
            $('#view_task_priority').html(`&nbsp;&nbsp;<label class="pull-right label label-${priorityClass} default" id="prioritybutton">${priorityLabel}</label>`);

            //console.log();
            var assigneDetails = JSON.parse(response.ft_assignee_details);
            var html =`
                    <div class="assignee-list">
                        <div class="d-flex align-center" onclick="assigneeSearch(${__lastTaskId})" id="selectedTaskElements_${__lastTaskId}">
                            <div class="assignee-avatar">
                                <img class="img-circle" src="${__userpath}${assigneDetails.us_image}">
                            </div>
                            <div class="assignee-list-info" id="assignee_names_${__lastTaskId}">${assigneDetails.us_name}</div>
                            <div class="assignee-close" onclick="removeAssignee(${__lastTaskId})">&times;</div>
                        </div>
                        <div id="dropdownLists_${__lastTaskId}" class="assignee-dropdown-content">
                            <input type="text" placeholder="Search.." id="serachinputs_${__lastTaskId}" onkeyup="filterFunction(${__lastTaskId})" class="myInput">
                            <div class="search-items" id="searchItems_${__lastTaskId}"></div>
                        </div>
                    </div>`;
            $('#view_task_assigness').html(html);
            $('#view_task').modal('show');
        }
    });
}

$(document).ready(function() {
    var ctrlDown = false,
        ctrlKey = 17,
        cmdKey = 91,
        vKey = 86,
        cKey = 67;

    $(document).keydown(function(e) {
        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = true;
    }).keyup(function(e){
        if (e.keyCode == ctrlKey || e.keyCode == cmdKey) ctrlDown = false;
    });

    $(".tasktext").keydown(function(e) {
        if (ctrlDown && (e.keyCode == vKey || e.keyCode == cKey)) return false;
    });
    
    // Document Ctrl + C/V 
    $(document).keydown(function(e) {
        
        //if (ctrlDown && (e.keyCode == cKey)) console.log("Document catch Ctrl+C");
        if (ctrlDown && (e.keyCode == vKey)){
        //console.log();
        setTimeout(()=>{
            var val = $(e.target).val();
            var arr = val.split(/[\n\r]/g);
            console.log(arr);
            var count = arr.length - 1;
            $(e.target).val(arr[0]);
            for(var i = 1; i < count; i++){ 
                __lastTaskId++;
                var label_class         = '';
                var markAs              = 'Completed';
                var newStatus           = 'completed';
                var priorityClass       = 'success';
                var priorityLabel       = 'Normal';
                var assigneDetails      = {'us_image':'default.jpg','us_name':'Assignee'};
                var data                = {'id':__lastTaskId,'ft_tittle':arr[i],'ft_priority':'4'};
                $('#popUpMessagePage').remove();
                var newTskaHtml = `
                                    <div class="rTableRow user-listing-row" id="task_row_${__lastTaskId}" data-name="qwerty" data-email="qwerty">
                                        <div class="rTableCell" style="min-width: 400px;">
                                            <div class="d-flex align-center">
                                                <div class="task-status-mark ${label_class}" data-toggle="tooltip" title="Mark as ${markAs}" data-placement="right" data-original-title="Mark as ${markAs}" onclick="taskStatusChangecolor('${newStatus}', ${__lastTaskId})">
                                                    <svg class="MiniIcon CheckMiniIcon TaskRowCompletionStatus-checkIcon TaskRowCompletionStatus-checkIcon--withSpreadsheetGridEnabled" viewBox="0 0 24 24" style="fill: #828282;width: 16px;height: 16px;"><path d="M9.5,18.2c-0.4,0.4-1,0.4-1.4,0l-3.8-3.8C4,14,4,13.4,4.3,13s1-0.4,1.4,0l3.1,3.1l8.6-8.6c0.4-0.4,1-0.4,1.4,0s0.4,1,0,1.4 L9.5,18.2z"></path></svg>
                                                </div>
                                                <span id="editinline_${__lastTaskId}">
                                                    <textarea class="form-control tasktext" onkeypress="return preventSpecialCharector(event)" onfocusout="createTask(${__lastTaskId},this.value)" id="edittask_tittle${__lastTaskId}" style="width: 550px;">${arr[i]}</textarea>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="rTableCell" style="width:170px" id="duedateeditinline_${__lastTaskId}">
                                            <div class="task-due-date">
                                                <div class="date-icon">
                                                    <svg style="width: 16px;height: 16px;fill: #636363;" class="Icon CalendarIcon" focusable="false" viewBox="0 0 32 32"><path d="M24,2V1c0-0.6-0.4-1-1-1s-1,0.4-1,1v1H10V1c0-0.6-0.4-1-1-1S8,0.4,8,1v1C4.7,2,2,4.7,2,8v16c0,3.3,2.7,6,6,6h16c3.3,0,6-2.7,6-6V8C30,4.7,27.3,2,24,2z M8,4v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4h12v1c0,0.6,0.4,1,1,1s1-0.4,1-1V4c2.2,0,4,1.8,4,4v2H4V8C4,5.8,5.8,4,8,4z M24,28H8c-2.2,0-4-1.8-4-4V12h24v12C28,26.2,26.2,28,24,28z"></path></svg>
                                                </div>
                                                <div class="due-date-info">
                                                    <div class="due-title">Due Date</div>
                                                    <div class="due-date">11 Jul</div>
                                                </div>
                                                <div class="assignee-close" >&times;</div>
                                            </div>
                                        </div>
                                        <div class="rTableCell">
                                            <div class="assignee-list">
                                                <div class="d-flex align-center" onclick="assignee_search(${__lastTaskId})" id="selectedTaskElement_${__lastTaskId}">
                                                    <div class="assignee-avatar">
                                                        <img class="img-circle" src="${__userpath}${assigneDetails.us_image}">
                                                    </div>
                                                    <div class="assignee-list-info" id="assignee_name_${__lastTaskId}">${assigneDetails.us_name}</div>
                                                    <div class="assignee-close" onclick="removeAssignee(${__lastTaskId})">&times;</div>
                                                </div>
                                                <div id="dropdownList_${__lastTaskId}" class="assignee-dropdown-content">
                                                    <input type="text" placeholder="Search.." id="serachinput_${__lastTaskId}" onkeyup="filterFunction(${__lastTaskId})" class="myInput">
                                                    <div class="search-items" id="searchItems_${__lastTaskId}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rTableCell" style="width:100px">
                                            <div class="col-sm-12" id="prioritychange${__lastTaskId}">
                                                <label class="pull-right label-${priorityClass}" id="actionp_class_${__lastTaskId}" onclick="prioritychange(${__lastTaskId}, '${data.ft_priority}')">${priorityLabel}</label>
                                            </div>
                                        </div>
                                        <div class="td-dropdown rTableCell" style="padding-bottom: 3px !important;">
                                            <div class="btn-group lecture-control">
                                                <span class="dropdown-tigger" data-toggle="dropdown">
                                                    <span class="label-text">
                                                        <i class="icon icon-down-arrow"></i>
                                                    </span>
                                                    <span class="tilder"></span>
                                                </span>
                                                <ul class="dropdown-menu pull-right" role="menu" id="task_action_${__lastTaskId}">
                                                    <li>
                                                        <a href="javascript:void(0);" onclick="viewTask(${__lastTaskId})">View</a>
                                                    </li>

                                                    <!--<li>
                                                        <a href="javascript:void(0);" onclick="editTask(${__lastTaskId}, '${data.ft_tittle}')">${'Edit'}</a>
                                                    </li>-->

                                                    <li>
                                                        <a href="javascript:void(0);" onclick="sendMessageToUser(${__lastTaskId})"> Remind Assignee</a>
                                                    </li>
                                    
                                                    <li>
                                                            <a href="javascript:void(0);" id="delete_btn_${__lastTaskId}" onclick="deleteTask(${__lastTaskId},'${data.ft_tittle}')" >${lang('delete')}</a>
                                                    </li>
                                    
                                                    <li>
                                                        <a href="javascript:void(0);" id="restore_btn_${__lastTaskId}" onclick="restoreTask(${__lastTaskId}, '${data.ft_tittle}')" >${lang('restore')}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>`;
                $('#task_row_wrapper').prepend(newTskaHtml);
                $('#edittask_tittle'+__lastTaskId).focus();
                //console.log($('#edittask_tittle'+__lastTaskId).val());
            }
            //console.log(arr);
        },600);
        }
    });
});

function preventSpecialCharector(e){
    var k; 
    document.all ? k = e.keyCode : k = e.which; 
    //console.log(e.keyCode, e.which);
    return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57) || k == 46 || k == 44 || k == 59 || k == 13);
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
$(document).on('click', '.locate-page', function (){
    __offset = $(this).attr('data-page');
    getTasks();
});

function clearTaskCache() {
    __task_selected = new Array();
    __course_selected = new Array();
    $("#selected_task_count").html('');
}

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
            getTasks();
        }
    } else {
        if ($('.user-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            getTasks();
        }
    }
}

function restoreTaskBulkConfirmed() {
    $.ajax({
        url: admin_url + 'user/restore_bulk',
        type: "POST",
        data: {
            "users": JSON.stringify(__task_selected),
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.length > 0) {
                for (var i in data) {
                    if (__filter_dropdown == 'all') {
                        $('#task_row_' + data[i].id).html(renderTaskRow(data[i]));
                    } else {
                        $('#task_row_' + data[i].id).remove();
                        __totalTasks = __totalTasks - 1;
                        __shownUsers = __shownUsers - 1;
                        if (__totalTasks <= 0 && __shownUsers <= 0) {
                            var totalUsersHtml = 'No Tasks';
                        } else {
                            var totalUsersHtml = __totalTasks + ' Tasks'; //__shownUsers + ' / ' + __totalTasks + ' Tasks';
                        }
                        $('.user-count').html(totalUsersHtml);
                    }
                }
            }
            __task_selected = new Array();
            $("#task_bulk").css('display', 'none');
            var messageObject = {
                'body': 'Tasks restored successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            $("#selected_task_count").html('');
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


var __selectedTaskElement = null;
var __selectedTaskAssegnee = null;
/* Assignee Search Dropdown */
function assignee_search(id) {
    __selectedTaskElement = id;
    $('.show').removeClass('show');
    $("#dropdownList_"+id).addClass('show');
    $('#serachinput_'+id).focus().attr('placeholder', $('#assignee_name_'+id).text());
    
    assigneeFilter(id);
}

function assigneeFilter(id){
    var html = '';
    document.getElementById("serachinput_"+id).value = '';
    $.grep(__assignee, function( n, i ) {
        html += `<div class="assignee-serch-item"> 
                    <div class="d-flex align-center" id="${n.id}" onclick="selectassignee(this)"> 
                        <div class="assignee-avatar">
                            <img class="img-circle" src="${__userpath}${n.us_image}">
                        </div>
                        <div class="assignee-list-info" id="assignee_name_${id}">${n.us_name}</div>
                    </div>
                </div>`;
      });
      $('#searchItems_'+id).html(html);
      __selectedTaskAssegnee = document.getElementById('selectedTaskElement_'+id);
}


$(window).click(function(){
    if(typeof __selectedTaskAssegnee != 'undefined' && __selectedTaskAssegnee != null){
        document.addEventListener('click', function(event) {
            if (!__selectedTaskAssegnee.contains(event.target)) {
                if(__selectedTaskAssegnee){ 
                    $('#selectedTaskElement_'+__selectedTaskElement).html(__selectedTaskAssegnee.innerHTML);
                    __selectedTaskAssegnee = null;
                    $('.show').removeClass('show');
                    
                }
            }
        });
    }
});


function removeAssignee(taskId){
    $('.show').removeClass('show');
    var html = `<div class="assignee-avatar">
                    <img class="img-circle" src="${__userpath}${'default.jpg'}">
                </div>
                <div class="assignee-list-info" id="assignee_name_${taskId}">${'Assignee'}</div>
                <div class="assignee-close" onclick="removeAssignee(${taskId})">&times;</div>`;
    $('#selectedTaskElement_'+taskId).html(html);
    $.ajax({
        url: admin_url + 'tasks/removeAssignee',
        type: "POST",
        data: {
            "assignee": null,
            'taskId' : taskId,
            "is_ajax": true
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false){
                toastr["success"]('',"Task successfully updated");
            }else{
                toastr["warning"]('',"Failed to update task");
            }
        }
    });
     //alert(taskId);
 }

function selectassignee(el){
    $('.show').removeClass('show');
    $('#selectedTaskElement_'+__selectedTaskElement).html(el.innerHTML+'<div class="assignee-close">&times;</div>');
    var assigneeId = el.getAttribute('id');
    __selectedTaskAssegnee = null;
    if(typeof assigneeId != 'undefined'){
        
        $.ajax({
            url: admin_url + 'tasks/update_task',
            type: "POST",
            data: {
                "assignee": assigneeId,
                'taskId' : __selectedTaskElement,
                "is_ajax": true
            },
            success: function (response) {
                var data = $.parseJSON(response);
                if (data.error == false){
                    toastr["success"]('',"Task successfully updated");
                }else{
                    toastr["warning"]('',"Failed to update task");
                }
                __title = null;
            }
        });
    }
}
  
  function filterFunction(id) {
    var filter, html; //
    filter = document.getElementById("serachinput_"+id).value;
    if(typeof filter != 'undefined' && filter){
        $.get(admin_url + 'tasks/getAssignee', {keyword : filter}, (__assignee) => {
            if(__assignee.length){
                for( var i = 0; i < __assignee.length; i++){
                //console.log(__assignee[i], '</br>');
                    html = `<div class="assignee-serch-item"> 
                                <div class="d-flex align-center" id="${__assignee[i].id}" onclick="selectassignee(this)"> 
                                    <div class="assignee-avatar">
                                        <img class="img-circle" src="${__userpath}${__assignee[i].us_image}">
                                    </div>
                                    <div class="assignee-list-info" id="assignee_name_${id}">${__assignee[i].us_name}</div>
                                </div>
                            </div>`;
                }
            }else{
                html = `<div class="assignee-serch-item"> 
                                <div class="d-flex align-center"> 
                                    No results found
                                </div>
                            </div>`; 
            }
            //console.log(__assignee, 'html', filter);
            $('#searchItems_'+id).html(html);
        });
    }
    
  }
 



