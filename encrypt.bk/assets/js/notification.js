var __notification_selected   = new Array();
var __filter_dropdown         = '';
var __barTypes                = [];
    __barTypes[1]             = 'Popup Bar';
    __barTypes[2]             = 'Top Bar';
__notifications = $.parseJSON(__notifications);
const __previlage = new Access();
$(function() {
    var filter = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');

    filter = (filter == '') ? 'active' : filter;
    __filter_dropdown = filter;
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');

    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#notification_keyword').val(keyword);
    }

    $('#notification_row_wrapper').html(renderNotificationHtml(__notifications));
    renderPagination(__offset, __totalNotifications);
    setBulkAction();
})
function getNotifications()
{
    var keyword  = $('#notification_keyword').val().trim();
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__filter_dropdown != '' || keyword != '') {
            link += '?';
        }
        if (__filter_dropdown != '') {
            link += '&filter=' + __filter_dropdown;
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        __offset = (typeof __offset == 'undefined') ? 1 : __offset;
        link += '&offset=' + __offset;
        window.history.pushState({
            path: link
        }, '', link);
    }
    $.ajax({
        url: admin_url+'notification/filter_notifications',
        type: "POST",
        data:{
                "filter" : __filter_dropdown,
                "limit"  : __limit,        
                "keyword": keyword,
                "offset" : __offset
            },
        success: function(response) {
            $('.notification-checkbox-parent').prop('checked', false);
            var filteredNotifications  = $.parseJSON(response);
            if (filteredNotifications.notifications.length == '0') {
                $('#notification_row_wrapper').html('<div id="popUpMessagePage" class="alert alert-danger">No Information Bars are found.</div>');
            }else{
                $('#notification_row_wrapper').html(renderNotificationHtml(filteredNotifications.notifications));
            }
            __notification_selected = new Array();
            $('.notification-checkbox-parent').prop('checked', false);
            renderPagination(__offset, filteredNotifications.total_notifications);
            __totalNotifications    = filteredNotifications.total_notifications;
            var total_notifications = (__totalNotifications>1)?__totalNotifications+' Information Bars':__totalNotifications+' Information Bar';
            $('#total-notifications').html(total_notifications);
        }
    });
}


$(document).on('click', '.notification-checkbox', function(){
    var notification_id = $(this).val();
    if ($('.notification-checkbox:checked').length == $('.notification-checkbox').length) {
        $('.notification-checkbox-parent').prop('checked',true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __notification_selected.push(notification_id);
    }else{
        $('.notification-checkbox-parent').prop('checked',false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__notification_selected, notification_id);
    }
    if(__notification_selected.length > 0){
        $("#selected_notification_count").html(' ('+__notification_selected.length+')');
    }else{
        $("#selected_notification_count").html('');
    }

    if(__notification_selected.length > 1){
        $("#notification_bulk").css('display','block');
    }else{
        $("#notification_bulk").css('display','none');
    }
});

$(document).on('click', '.notification-checkbox-parent', function(){
    var parent_check_box = this;
    __notification_selected = new Array();    
    $( '.notification-checkbox' ).prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $( '.notification-checkbox' ).each(function( index ) {
           __notification_selected.push($( this ).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if(__notification_selected.length > 0){
        $("#selected_notification_count").html(' ('+__notification_selected.length+')');
    }else{
        $("#selected_notification_count").html('');
    }

    if(__notification_selected.length > 1){
        $("#notification_bulk").css('display','block');
    }else{
        $("#notification_bulk").css('display','none');
    }
});

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

$(document).on('click', '#searchclear', function() {
    __offset = 1;
    $("#notification_keyword").val('');
    getNotifications();
});

function deleteNotification( notification_id, notification_name )
{
    notification_name   = atob(notification_name);
    var headerText      = 'Are you sure to delete the Information Bar named "' + unescape(notification_name) + '"?';

    var messageObject   = {
                            'body'            : headerText,
                            'button_yes'      : 'DELETE',
                            'button_no'       : 'CANCEL',
                            'continue_params' : {
                                                   'notification_id'   : notification_id
                                                },
                          };
    callback_warning_modal(messageObject, deleteNotificationConfirmed);       
}

function deleteNotificationConfirmed( params ){
    var notification_id    = params.data.notification_id;
    $.ajax({
        url: admin_url+'notification/delete_notification',
        type: "POST",
        data:{
            "notification_id": notification_id
        },
        success: function(response) {
            var removeNotification = $.parseJSON(response);
            if(removeNotification.error == false) {
            $('#notification_row_' + notification_id).remove();
            __totalNotifications    = __totalNotifications - 1;
            refreshListing();
            var messageObject = {
                'body': removeNotification.message,
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
            } else {
            var messageObject = {
                'body': removeNotification.message,
                'button_yes': 'OK',
            };
            callback_danger_modal(messageObject);
            }
        }
    });
}


function deleteNotificationBulk()
{
    var header_text = 'Are you sure to delete the selected Information Bars?';

    var messageObject = {
                            'body': header_text,
                            'button_yes': 'DELETE',
                            'button_no': 'CANCEL',
                            'continue_params': {
                                'status': status
                            },
                        };
    callback_warning_modal(messageObject, deleteNotificationBulkConfirmed);  
}

function deleteNotificationBulkConfirmed(){
    $.ajax({
        url: admin_url+'notification/delete_notification_bulk',
        type: "POST",
        data:{
            "notifications":JSON.stringify(__notification_selected)
            },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                for (var i in __notification_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#notification_row_' + __notification_selected[i]).remove();
                        __totalNotifications = __totalNotifications - 1;
                    }
                }
                var messageObject = {
                    'body': data.message,
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
            __notification_selected = new Array();
            $("#notification_bulk").css('display', 'none');
            $("#selected_notification_count").html('');
            $('.notification-checkbox-parent, .notification-checkbox').prop('checked', false);
        }
    });
}

function renderNotificationHtml( notifications )
{
    //console.log(notifications);
    $("#selected_notification_count").html('');
    var notificationHtml    = '';
    if(notifications.length > 0 )
    {
        for (var i=0; i<notifications.length; i++)
        {
            notificationHtml += '<div class="rTableRow notification-listing-row" id="notification_row_'+notifications[i]['id']+'" data-name="'+notifications[i]['n_title']+'">';
            notificationHtml += '    <div class="rTableCell"> ';
            notificationHtml += '        <input type="checkbox" class="notification-checkbox" value="'+notifications[i]['id']+'" id="notification_details_'+notifications[i]['id']+'">';
            notificationHtml += '        <span class="icon-wrap-round" style="margin-left:0px;" data-name="'+notifications[i]['n_title']+'">';
            //notificationHtml += '           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;width: 22px;height: 22px;vertical-align: bottom;margin-top: 3px;" xml:space="preserve" width="512px" height="512px" class=""><g><g><g><path d="M438.21,415.136l-24.977-46.878V252.489c0-71.565-48.062-132.111-113.603-151.069V43.632C299.631,19.573,280.059,0,256,0    c-24.059,0-43.631,19.573-43.631,43.632v57.789C146.83,120.38,98.766,180.925,98.766,252.49v115.768l-24.976,46.878    c-4.81,9.027-4.538,19.652,0.726,28.423c5.265,8.769,14.514,14.005,24.743,14.005h99.158C200.106,487.865,225.283,512,256,512    c30.716,0,55.894-24.135,57.583-54.436h99.159c10.228,0,19.477-5.236,24.743-14.005    C442.747,434.788,443.019,424.163,438.21,415.136z M235.455,43.632c0-11.33,9.216-20.546,20.544-20.546    s20.544,9.216,20.544,20.546V96.6c-6.726-0.882-13.583-1.342-20.544-1.342c-6.963,0-13.819,0.461-20.544,1.342V43.632z     M121.853,252.489c0-73.968,60.178-134.147,134.147-134.147s134.147,60.179,134.147,134.147v106.217H121.853V252.489z     M256,488.914c-17.981,0-32.795-13.792-34.435-31.35h68.872C288.795,475.122,273.979,488.914,256,488.914z M417.69,431.676    c-0.506,0.843-1.999,2.802-4.948,2.802H99.259c-2.948,0-4.443-1.959-4.948-2.802c-0.506-0.841-1.532-3.082-0.145-5.685    l23.549-44.199h276.572l23.549,44.199C419.221,428.594,418.194,430.834,417.69,431.676z" data-original="#000000" class="active-path" data-old_color="#ffffff" fill="#ffffff"></path></g></g><g><g><path d="M256,149.113c-57.002,0-103.378,46.376-103.378,103.378c0,6.374,5.168,11.543,11.543,11.543    c6.375,0,11.543-5.169,11.543-11.543c0-44.272,36.019-80.292,80.292-80.292c6.375,0,11.543-5.169,11.543-11.543    C267.543,154.28,262.375,149.113,256,149.113z" data-original="#000000" class="active-path" data-old_color="#ffffff" fill="#ffffff"></path></g></g></g></svg>';
            notificationHtml += '           <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;width: 22px;height: 22px;vertical-align: bottom;margin-top: 3px;"><g><g fill="#fff"><path d="m338.6,136.1c-23.6-22.7-54.6-34.4-87.4-33.2-62.9,2.5-114.2,55.7-114.4,118.7-0.1,27.9 9.7,55.1 27.6,76.5 25.8,31 40,70.1 40,110.2 0,0-2.7,18.4 10.4,18.4h82.4c13.4,0 10.4-18.4 10.4-18.4 0-39.2 14.4-78 41.7-112.2 16.9-21.3 25.9-46.9 25.9-74.1 0-32.7-13-63.3-36.6-85.9zm-5.6,147c-29.7,37.2-45.6,79.6-46.2,122.8h-61.6c-0.6-44.1-16.4-87-44.8-121.1-14.7-17.7-22.8-40.1-22.7-63.1 0.2-52 42.5-95.9 94.4-98 27.1-1 52.7,8.7 72.1,27.4 19.5,18.7 30.2,43.9 30.2,70.9-0.1,22.5-7.4,43.6-21.4,61.1z"/><path d="m393.2,67.8c-4.1-4.1-10.7-4.1-14.7,0l-25.4,25.4c-4.1,4.1-4.1,10.7 0,14.7s10.7,4.1 14.7,0l25.4-25.4c4.1-4 4.1-10.6 0-14.7z"/><path d="m297.6,440.9h-83.2c-5.8,0-10.4,4.7-10.4,10.4 0,5.8 4.7,10.4 10.4,10.4h83.2c5.8,0 10.4-4.7 10.4-10.4 0-5.7-4.7-10.4-10.4-10.4z"/><path d="m281.9,480.1h-51.7c-5.8,0-10.4,4.7-10.4,10.4 0,5.8 4.7,10.4 10.4,10.4h51.7c5.8,0 10.4-4.7 10.4-10.4 5.68434e-14-5.7-4.7-10.4-10.4-10.4z"/><path d="M256,67.7c5.8,0,10.4-4.7,10.4-10.4V21.4c0-5.8-4.7-10.4-10.4-10.4s-10.4,4.7-10.4,10.4v35.9    C245.6,63.1,250.2,67.7,256,67.7z"/><path d="m158.9,108c4.1-4.1 4.1-10.7 0-14.7l-25.4-25.4c-4.1-4.1-10.7-4.1-14.7,0s-4.1,10.7 0,14.7l25.4,25.4c4.1,4 10.7,4 14.7,0z"/><path d="m439.6,194.6h-35.9c-5.8,0-10.4,4.7-10.4,10.4s4.7,10.4 10.4,10.4h35.9c5.8,0 10.4-4.7 10.4-10.4s-4.7-10.4-10.4-10.4z"/><path d="m108.3,194.6h-35.9c-5.8,0-10.4,4.7-10.4,10.4s4.7,10.4 10.4,10.4h35.9c5.8,0 10.4-4.7 10.4-10.4s-4.6-10.4-10.4-10.4z"/></g></g></svg>';
            notificationHtml += '        </span>';
            notificationHtml += '        <span class="wrap-mail ellipsis-hidden">';
            notificationHtml += '            <div class="ellipsis-style">';
            notificationHtml += '                <a href="'+admin_url+'notification/basics/'+notifications[i]['id']+'" >'+notifications[i]['n_title']+'</a>';
            notificationHtml += '            </div>';
            notificationHtml += '        </span>';
            notificationHtml += '    </div>';

            var lastDate            = notifications[i]['n_expiry_date'].split('-');
            var expiryDate          = lastDate[2] + '-' + lastDate[1] + '-' + lastDate[0];
            var thisDay             = new Date();
            var thisMonth           = thisDay.getMonth()+1;
            var thisDate            = thisDay.getDate();
            var todaysDate          = thisDay.getFullYear() + '-' + (thisMonth<10 ? '0' : '') + thisMonth + '-' + (thisDate<10 ? '0' : '') + thisDate;
            //var todaysDate          = (thisDate<10 ? '0' : '') + thisDate + '-' + (thisMonth<10 ? '0' : '') + thisMonth + '-' + thisDay.getFullYear();

            notificationHtml += '    <div class="rTableCell"> ';
            notificationHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
            notificationHtml += '            <div class="ellipsis-style">';
            notificationHtml += '                <a href="'+admin_url+'notification/basics/'+notifications[i]['id']+'" >'+expiryDate+'</a>';
            notificationHtml += '            </div>';
            notificationHtml += '        </span>';
            notificationHtml += '    </div>';
            notificationHtml += '    <div class="rTableCell"> ';
            notificationHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
            notificationHtml += '            <div class="ellipsis-style">';
            notificationHtml += '               <p style="color:#3daa3b;">'+__barTypes[notifications[i]['n_notification_bar_type']]+'</p>';
            notificationHtml += '            </div>';
            notificationHtml += '        </span>';
            notificationHtml += '    </div>';

            var actionClass         = 'label-warning';
            var actionLabel         = 'Inactive';
            var actionText          = 'Activate';
            if(notifications[i]['n_status'] == '1') {
                actionClass         = 'label-success';
                actionLabel         = 'Active';
                actionText          = 'Deactivate';
            }
            if(notifications[i]['n_expiry_date'] < todaysDate) {
                actionClass         = 'label-danger';
                actionLabel         = 'Expired';
            }

            notificationHtml += '    <div class="rTableCell pad0">';
            notificationHtml += '        <div class="col-sm-12 pad0">';
            notificationHtml += '            <label class="pull-right label '+actionClass+'" id="action_class_'+notifications[i]['id']+'">';
            notificationHtml +=               actionLabel;
            notificationHtml += '            </label>';
            notificationHtml += '        </div>';
            notificationHtml += '    </div>';

            if(__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
                notificationHtml += '    <div class="td-dropdown rTableCell">';
                notificationHtml += '        <div class="btn-group lecture-control">';
                notificationHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                notificationHtml += '                <span class="label-text">';
                notificationHtml += '                  <i class="icon icon-down-arrow"></i>';
                notificationHtml += '                </span>';
                notificationHtml += '                <span class="tilder"></span>';
                notificationHtml += '            </span>';
                notificationHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="notification_action_'+notifications[i]['id']+'">';
                if(__previlage.hasEdit() == true && notifications[i]['n_expiry_date'] >= todaysDate) {
                    notificationHtml += '                <li id="status_btn_'+notifications[i]['id']+'">';
                    notificationHtml += '                        <a href="javascript:void(0);" data-toggle="modal" onclick="changeNotificationStatus(\''+notifications[i]['id']+'\', \''+notifications[i]['n_status']+'\', \''+btoa(escape(notifications[i]['n_title']))+'\')">'+actionText+'</a>';
                    notificationHtml += '                </li>';
                }
                if(__previlage.hasEdit() == true) {
                    notificationHtml += '                <li>';
                    notificationHtml += '                       <a href="'+admin_url+'notification/basics/'+notifications[i]['id']+'" >'+lang('settings')+'</a>';
                    notificationHtml += '                </li>';
                }
                if(__previlage.hasDelete() == true) {
                    notificationHtml += '                <li>';
                    notificationHtml += '                       <a href="javascript:void(0);" id="delete_btn_'+notifications[i]['id']+'" data-toggle="modal" onclick="deleteNotification(\''+notifications[i]['id']+'\', \''+btoa(escape(notifications[i]['n_title']))+'\')">Delete</a>';
                    notificationHtml += '                </li>';
                }
                notificationHtml += '           </ul>';
                notificationHtml += '        </div>';
                notificationHtml += '    </div>';
            }
            notificationHtml += '</div>';    
        }
        var total_notifications = (__totalNotifications>1)?__totalNotifications+' Information Bars':__totalNotifications+' Information Bar';
        $('#total-notifications').html(total_notifications);
    }
    else
    {
        notificationHtml += '<div id="popUpMessagePage" class="alert alert-danger">No Information Bars are found.</div>';
        $('#total-notifications').html('0 Information Bars');

    }
    return notificationHtml;
}

var timeOut = null;
$(document).on('keyup', '#notification_keyword', function() {
    clearTimeout(timeOut);
    timeOut = setTimeout(function() {
        __offset = 1;
        getNotifications();
        $("#selected_notification_count").html('');
        __notification_selected = new Array();
        $("#notification_bulk").css('display', 'none');
    }, 600);
});

function filter_notification_by(filter) {
    __offset = 1;
    __filter_dropdown = filter;
    if (filter == 'all') {
        //$('#page_keyword').val('');
        $('#dropdown_text').html(lang('notifications') + ' <span class="caret"></span>');
    }
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    getNotifications();
    $("#selected_notification_count").html('');
    __notification_selected = new Array();
    $("#notification_bulk").css('display', 'none');
    setBulkAction();
}

function renderPagination(offset, totalNotifications) {
    offset = Number(offset);
    totalNotifications = Number(totalNotifications);
    var totalNotification = Math.ceil(totalNotifications / __limit);
    if (offset <= totalNotification && totalNotification > 1) {
        var paginationHtml = '';
        paginationHtml += '<ul class="pagination pagination-wrapper"  style="left:65px;">';
        paginationHtml += generatePagination(offset, totalNotification);
        paginationHtml += '</ul>';
        $('#pagination_wrapper').html(paginationHtml);
        scrollToTopOfPage();
    } else {
        $('#pagination_wrapper').html('');
    }
}

$(document).on('click', '.locate-page', function() {
    __offset = $(this).attr('data-page');
    
    getNotifications();
    $("#selected_notification_count").html('');
    __notification_selected = new Array();
    $("#notification_bulk").css('display', 'none');
    setBulkAction();
});

function refreshListing() { 
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.notification-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        } else {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
        }
        getNotifications();
    } else {
        if ($('.notification-listing-row').length != 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        }
        getNotifications();
    }
}

function setBulkAction() {
    var notification_bulk = '';
    if(__previlage.hasEdit() == true || __previlage.hasDelete() == true) {
        notification_bulk += '<span class="dropdown-tigger" data-toggle="dropdown">';
        notification_bulk += ' <span class="label-text">';
        notification_bulk += '     Bulk Action  <!-- <span class="icon icon-down-arrow"></span> -->';
        notification_bulk += ' </span>';
        notification_bulk += ' <span class="tilder"></span>';
        notification_bulk += '</span>';
        notification_bulk += '<ul class="dropdown-menu pull-right" role="menu">';
    
        if(__previlage.hasDelete() == true) {
            notification_bulk += ' <li>';
            notification_bulk += '     <a href="javascript:void(0)" onclick="deleteNotificationBulk()" > Delete </a>';
            notification_bulk += ' </li>';
        }
        if(__previlage.hasEdit() == true) {
            if (__filter_dropdown != "active" && __filter_dropdown != "expired") {
                notification_bulk += '<li>';
                notification_bulk += ' <a href="javascript:void(0)" onclick="changeNotificationStatusBulk(\'Are you sure to Activate the selected Information Bars?\', \'1\')" > Activate </a>';
                notification_bulk += '</li>';
            }
            if (__filter_dropdown != "inactive" && __filter_dropdown != "expired") {
                notification_bulk += '<li>';
                notification_bulk += ' <a href="javascript:void(0)" onclick="changeNotificationStatusBulk(\'Are you sure to Deactivate the selected Information Bars?\', \'0\')" > Deactivate </a>';
                notification_bulk += '</li>';
            }
        }
        notification_bulk += '</ul>';
    }
    $('#notification_bulk').html(notification_bulk);
}

function changeNotificationStatus(notification_id, action, notification_name)
{
    var ok_button_text  = (action == '0')?'ACTIVATE':'DEACTIVATE';
    notification_name   = atob(notification_name);
    var header_text     = 'Are you sure to ' + (ok_button_text.toLowerCase()) + ' the Information Bar named "' + unescape(notification_name) + '"?';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'notification_id': notification_id,
            'status': ((action != '0')?'0':'1')
        },
    };
    callback_warning_modal(messageObject, changeNotificationStatusConfirmed);    
}

function changeNotificationStatusConfirmed( params ){
    var notification_id    = params.data.notification_id;
    var status             = params.data.status;
    $.ajax({
        url: admin_url+'notification/change_notification_status',
        type: "POST",
        data:{
            "notification_id": notification_id, 
            "status": status
            },
        success: function(response) {
            var notificationStatus  = $.parseJSON(response);
            if (notificationStatus.error == false) {
                if (__filter_dropdown != 'all') {
                    $('#notification_row_' + notification_id).remove();
                    __totalNotifications = __totalNotifications - 1;
                    }

                var messageObject = {
                    'body': notificationStatus.message,
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                refreshListing();
            } else {
                var messageObject = {
                    'body': notificationStatus.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                refreshListing();
            }
        }
    });
}

function changeNotificationStatusBulk( header_text, status ){
    var action = 'Activate';
    var ok_button_text = 'ACTIVATE';

    if (status == 0) {
        action = 'Deactivate';
        ok_button_text = 'DEACTIVATE';
    }
    if (header_text == '') {
        header_text = 'Are you sure to ' + action + ' the selected Information Bars?';
    }

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'status': status,
        },
    };
    callback_warning_modal(messageObject, ChangeNotificationStatusBulkConfirmed);    
}

function ChangeNotificationStatusBulkConfirmed(params){
    var status = params.data.status;
    $.ajax({
        url: admin_url + 'notification/change_notification_status_bulk',
        type: "POST",
        data: {
            "notifications" : JSON.stringify(__notification_selected),
            "status"        : status
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data.error == false) {
                for (var i in __notification_selected) {
                    if (__filter_dropdown != 'all') {
                        $('#notification_row_' + __notification_selected[i]).remove();
                        __totalNotifications = __totalNotifications - 1;
                    }
                }
                var messageObject = {
                    'body': data.message,
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
                refreshListing();
                }
            __notification_selected = new Array();
            $("#notification_bulk").css('display', 'none');
            $("#selected_notification_count").html('');
            $('.notification-checkbox-parent, .notification-checkbox').prop('checked', false);
            
        }
    });
}

function createNotification(header_text, label)
{
    $('#notification_name').val('');
    $('#popUpMessage').hide();
    $('#create_box_title').html(header_text);
    $('#create_box_label').html(label);
    $('#create_box_ok').unbind();
    $('#create_box_ok').click({}, createNotificationConfirmed);    
}

function createNotificationConfirmed()
{
    var notification_name    =  $('#notification_name').val();
        notification_name    =  notification_name.replace(/["<>{}]/g, '');
        notification_name    =  notification_name.trim();
    var errorCount           =  0;
    var errorMessage         =  '';
    
    if( notification_name == '')
    {
        errorCount++;
        errorMessage += 'Please Enter Information Bar Name <br />';
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'notification/create_notification',
            type: "POST",
            data:{"is_ajax":true, 'notification_name':notification_name},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'notification/basics/'+data['id'];
                }
                else
                {
                    $('#create_notification .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create_notification .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}