var __groupRequests = new Array();

var __Base64 = {
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    encode: function(e) {
        var t = "";
        var n, r, i, s, o, u, a;
        var f = 0;
        e = __Base64._utf8_encode(e);
        while (f < e.length) {
            n = e.charCodeAt(f++);
            r = e.charCodeAt(f++);
            i = e.charCodeAt(f++);
            s = n >> 2;
            o = (n & 3) << 4 | r >> 4;
            u = (r & 15) << 2 | i >> 6;
            a = i & 63;
            if (isNaN(r)) {
                u = a = 64
            } else if (isNaN(i)) {
                a = 64
            }
            t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
        }
        return t
    },
    decode: function(e) {
        var t = "";
        var n, r, i;
        var s, o, u, a;
        var f = 0;
        e = e.replace(/[^A-Za-z0-9+/=]/g, "");
        while (f < e.length) {
            s = this._keyStr.indexOf(e.charAt(f++));
            o = this._keyStr.indexOf(e.charAt(f++));
            u = this._keyStr.indexOf(e.charAt(f++));
            a = this._keyStr.indexOf(e.charAt(f++));
            n = s << 2 | o >> 4;
            r = (o & 15) << 4 | u >> 2;
            i = (u & 3) << 6 | a;
            t = t + String.fromCharCode(n);
            if (u != 64) {
                t = t + String.fromCharCode(r)
            }
            if (a != 64) {
                t = t + String.fromCharCode(i)
            }
        }
        t = __Base64._utf8_decode(t);
        return t
    },
    _utf8_encode: function(e) {
        e = e.replace(/rn/g, "n");
        var t = "";
        for (var n = 0; n < e.length; n++) {
            var r = e.charCodeAt(n);
            if (r < 128) {
                t += String.fromCharCode(r)
            } else if (r > 127 && r < 2048) {
                t += String.fromCharCode(r >> 6 | 192);
                t += String.fromCharCode(r & 63 | 128)
            } else {
                t += String.fromCharCode(r >> 12 | 224);
                t += String.fromCharCode(r >> 6 & 63 | 128);
                t += String.fromCharCode(r & 63 | 128)
            }
        }
        return t
    },
    _utf8_decode: function(e) {
        var t = "";
        var n = 0;
        var r = c1 = c2 = 0;
        while (n < e.length) {
            r = e.charCodeAt(n);
            if (r < 128) {
                t += String.fromCharCode(r);
                n++
            } else if (r > 191 && r < 224) {
                c2 = e.charCodeAt(n + 1);
                t += String.fromCharCode((r & 31) << 6 | c2 & 63);
                n += 2
            } else {
                c2 = e.charCodeAt(n + 1);
                c3 = e.charCodeAt(n + 2);
                t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                n += 3
            }
        }
        return t
    }
}

$(document).ready(function() {
    var keyword = getQueryStringValue('keyword');
    var institute_id = getQueryStringValue('institute_id');

    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#group_keyword').val(keyword);
    }
    if (institute_id != '') {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
    }

});

$(document).on('click', '#search_group', function() {
    var user_keyword = $('#group_keyword').val();
            
    if(user_keyword.match(/^ \s+ $/))
    {
        lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
        
        $('#group_keyword').val('');
    } 
    else
    {
        __offset = 1;
        __group_selected = new Array();
        getGroups();
    }     
});


$(document).on("keypress", "#group_keyword", function(e){

    $("#selected_group_count").html('');
    $('.group-checkbox-parent, .group-checkbox').prop('checked', false);
    $("#group_bulk").css('display', 'none');
    if(e.which == 13){
        var user_keyword = $('#group_keyword').val();
        
        if(user_keyword.match(/^ \s+ $/))
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
            
            $('#group_keyword').val('');
        } 
        else
        {
            __offset = 1;
            __group_selected = new Array();
            getGroups();
        }  
        
    }
    
});


$(document).on('click', '#searchclear', function() {
    __group_selected = new Array();
    $("#selected_group_count").html('');
    $('.group-checkbox-parent, .group-checkbox').prop('checked', false);
    $("#group_bulk").css('display', 'none');
    __offset = 1;
    getGroups();
});


var __institute_id = '';

function filter_institute(institute_id) {
    if (institute_id == 'all') {
        __institute_id = '';
        $('#filter_institute').html('All Institutes <span class="caret"></span>');
    } else {
        __institute_id = institute_id;
        $('#filter_institute').html($('#filter_institute_' + institute_id).text().substring(0, $('#filter_institute_' + institute_id).text().indexOf('-')) + '<span class="caret"></span>');
    }
    __offset = 1;
    getGroups();
    $("#selected_group_count").html('');
    __group_selected = new Array();
    $("#group_bulk").css('display', 'none');
    $('.group-checkbox-parent, .group-checkbox').prop('checked', false);
}

function loadMoreGroups() {
    getGroups();
}
function getGroups() {
    var keyword = $('#group_keyword').val();
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;

        if (__institute_id != '' || keyword != '') {
            link += '?';
        }
        if (keyword != '') {
            var uSearch = keyword.split(' ').join('-');
            link += '&keyword=' + uSearch;
        }
        if (__institute_id != '') {
            link += '&institute_id=' + __institute_id;
        }
        window.history.pushState({
            path: link
        }, '', link);
    }
    abortPreviousAjaxRequest(__groupRequests);
        __groupRequests.push($.ajax({
        url: admin_url + 'course/groups_json',
        type: "POST",
        data: {
            "is_ajax": true,
            'course_id': __course_id,
            'institute_id': __institute_id,
            'keyword': keyword,
            'limit': __limit,
            'offset': __offset
        },
        success: function(response) {
            var data_groups = $.parseJSON(response);
            console.log(data_groups);
            $('#loadmorebutton').hide();
            if (data_groups['groups'].length > 0) {
                __offset++;
                if (__offset == 2) {
                    __totalUsers = data_groups['total_groups'];
                    __shownUsers = data_groups['groups'].length;
                    remainingUser = (data_groups['total_groups'] - data_groups['groups'].length);
                    var totalUsersHtml = data_groups['groups'].length + ' / ' + data_groups['total_groups'] + ' ' + ((data_groups['total_groups'] == 1) ? "Batch" : "Batches");
                    scrollToTopOfPage();
                    $('.user-count').html(totalUsersHtml);
                    $('#group_row_wrapper').html(renderGroupsHtml(response));
                } else {
                    __totalUsers = data_groups['total_groups'];
                    __shownUsers = ((__offset - 2) * data_groups['limit']) + data_groups['groups'].length;
                    remainingUser = (data_groups['total_groups'] - (((__offset - 2) * data_groups['limit']) + data_groups['groups'].length));
                    var totalUsersHtml = (((__offset - 2) * data_groups['limit']) + data_groups['groups'].length) + ' / ' + data_groups['total_groups'] + ' Batches';
                    $('.user-count').html(totalUsersHtml);
                    $('.group-checkbox-parent').prop('checked', false);
                    $('#group_row_wrapper').append(renderGroupsHtml(response));
                }

            } else {
                $('.user-count').html('No Batches');
                scrollToTopOfPage();
                $('.user-count').html(totalUsersHtml);
                $('#group_row_wrapper').html(renderPopUpMessagePage('error', 'No Batches found'));
            }
            if (data_groups['show_load_button'] == true) {
                $('#loadmorebutton').show();
                remainingUser = (remainingUser > 0) ? '(' + remainingUser + ')' : '';
                $('#loadmorebutton').html('Load More ' + remainingUser + '<ripples></ripples>');
            }

        }
    }));
}

function renderGroupsHtml(response) {
    var data = $.parseJSON(response);
    var groupHtml = '';
    if (data['groups'].length > 0) {
        for (var i = 0; i < data['groups'].length; i++) {
            groupHtml += '<div class="rTableRow user-listing-row" id="group_row_' + data['groups'][i]['id'] + '" data-name="' + data['groups'][i]['gp_name'] + '">';
            groupHtml += '    <div class="rTableCell"> ';
            groupHtml += '        <label class="pointer-cursor">';
            groupHtml += '            <input type="checkbox" class="group-checkbox" value="' + data['groups'][i]['id'] + '" id="group_details_' + data['groups'][i]['id'] + '"> ';
           // groupHtml += '            <span class="icon-wrap-round blue">';
           // groupHtml += '                <i class="icon icon-users"></i>';
           // groupHtml += '            </span>';
            groupHtml += '<svg style="vertical-align: middle; margin: 0px 10px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="26px" height="18px" viewBox="0 0 26 18" enable-background="new 0 0 26 18" fill="#64277d" xml:space="preserve"><g><path d="M15.73,4.37h-0.5c0.005,0.626-0.278,1.617-0.73,2.384c-0.225,0.386-0.489,0.718-0.755,0.936 C13.477,7.911,13.228,8.01,13,8.01c-0.227,0-0.476-0.099-0.744-0.321c-0.4-0.328-0.791-0.917-1.061-1.55 c-0.273-0.63-0.428-1.311-0.425-1.779h-0.5l0.5,0.02c0.048-1.204,1.036-2.142,2.229-2.142L13.09,2.24h0h0 c1.16,0.048,2.093,0.981,2.142,2.142l0.5-0.021h-0.5v0.01H15.73h0.5V4.36V4.349V4.338c-0.072-1.68-1.419-3.027-3.1-3.098h0h0 l-0.131-0.003c-1.727,0-3.159,1.36-3.228,3.102l0,0.01v0.01c0.005,0.884,0.332,1.965,0.867,2.893 c0.27,0.462,0.592,0.883,0.98,1.206C12.002,8.779,12.472,9.009,13,9.01c0.527,0,0.997-0.229,1.382-0.549 c0.581-0.483,1.023-1.184,1.342-1.919c0.315-0.738,0.505-1.506,0.507-2.171H15.73z"/><path d="M7.87,16.2c0-1.419,0.573-2.698,1.502-3.628c0.93-0.929,2.209-1.502,3.627-1.502c1.419,0,2.698,0.573,3.628,1.502 c0.929,0.93,1.502,2.209,1.502,3.628h1c0-3.387-2.743-6.13-6.13-6.13c-3.386,0-6.13,2.743-6.13,6.13H7.87z"/><path d="M22.55,6.18h-0.5c0.005,0.512-0.231,1.36-0.609,2.012c-0.187,0.328-0.407,0.609-0.622,0.79 C20.6,9.166,20.406,9.241,20.24,9.24c-0.165,0-0.358-0.075-0.577-0.26c-0.326-0.273-0.654-0.777-0.879-1.315 C18.556,7.13,18.428,6.554,18.43,6.18c0.001-1.005,0.805-1.809,1.811-1.81c1.006,0,1.817,0.808,1.819,1.81h0.5v-0.5h-0.01h-0.5v0.5 H22.55v0.5h0.01h0.5v-0.5c0-1.558-1.267-2.807-2.819-2.81c-0.775,0-1.481,0.313-1.989,0.821C17.743,4.698,17.43,5.405,17.43,6.18 c0.006,0.759,0.284,1.689,0.741,2.5c0.23,0.403,0.507,0.774,0.844,1.062c0.335,0.285,0.751,0.497,1.226,0.498 c0.474-0.001,0.889-0.211,1.224-0.495c0.504-0.43,0.881-1.042,1.153-1.682c0.269-0.642,0.431-1.306,0.433-1.883H22.55v0.5V6.18z"/><path d="M17.662,12.858c0.771-0.584,1.672-0.863,2.57-0.864c1.294,0.001,2.569,0.583,3.41,1.688l-0.002-0.002l-0.001-0.002 c0.556,0.745,0.86,1.65,0.86,2.581h1c0-1.149-0.375-2.264-1.06-3.179l-0.001-0.002l-0.002-0.002 c-1.036-1.362-2.613-2.083-4.205-2.083c-1.106,0-2.225,0.349-3.174,1.067L17.662,12.858z"/><path d="M3.45,6.18h-0.5c0.005,0.768,0.284,1.7,0.741,2.508C3.922,9.09,4.199,9.459,4.536,9.746 c0.335,0.284,0.75,0.494,1.224,0.495c0.475-0.001,0.891-0.213,1.225-0.499c0.504-0.433,0.88-1.047,1.152-1.687 C8.406,7.412,8.567,6.751,8.57,6.18c0-0.775-0.313-1.482-0.821-1.989C7.242,3.684,6.535,3.37,5.76,3.37 C4.207,3.373,2.94,4.622,2.94,6.18v0.5h0.51V6.18h-0.5H3.45v-0.5H3.44v0.5h0.5c0.002-1.002,0.813-1.81,1.82-1.81 c1.006,0.001,1.809,0.804,1.81,1.81C7.575,6.681,7.339,7.531,6.96,8.185C6.773,8.514,6.553,8.798,6.337,8.98 C6.118,9.166,5.925,9.241,5.76,9.24c-0.167,0-0.36-0.074-0.578-0.258C4.856,8.711,4.528,8.211,4.304,7.674 C4.076,7.14,3.948,6.563,3.95,6.18v-0.5h-0.5V6.18z"/><path d="M8.942,12.062c-0.949-0.719-2.068-1.067-3.174-1.067c-1.592,0-3.17,0.721-4.206,2.083l-0.001,0.002L1.56,13.081 C0.875,13.996,0.5,15.11,0.5,16.26h1c0-0.931,0.304-1.836,0.86-2.581l-0.001,0.002l-0.001,0.002 c0.841-1.105,2.116-1.688,3.41-1.688c0.898,0.001,1.799,0.28,2.57,0.864L8.942,12.062z"/></g></svg>';
            groupHtml += '            <span class="normal-base-color"> ';
            groupHtml += '                <span>' + data['groups'][i]['batch_name'] + '</span> ';
            groupHtml += '            </span>';
            groupHtml += '        </label>';
            groupHtml += '                <span class="pull-right groups-student-count-holder"> <span class="label-active group-total">' + data['groups'][i]['group_strength'] + '</span> ' + lang('users') + '</span>';
            groupHtml += '    </div>';
            var groupCount=parseInt(data['groups'][i]['group_strength']);
            var optionView   = false;
            var optionEdit   = false;
            var optionDelete = false;
            
            if(__batchEnroll_privilege.view()==true){

                if (groupCount >0 ) {
                    optionView = true;
                }
            }
            if(__batchEnroll_privilege.edit()==true){
                if (groupCount > 0) {
                    optionEdit = true;
                }
            }
            if(__batchEnroll_privilege.pdelete()==true){
                optionDelete = true;
            }
            if(optionView == true||optionEdit == true||optionDelete == true){
            groupHtml += '    <div class="td-dropdown rTableCell">';
            groupHtml += '        <div class="btn-group lecture-control">';
            groupHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            groupHtml += '                <span class="label-text">';
            groupHtml += '                    <i class="icon icon-down-arrow"></i>';
            groupHtml += '                </span>';
            groupHtml += '                <span class="tilder"></span>';
            groupHtml += '            </span>';
            groupHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
            
            if (optionView == true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="sendMessageToGroups(\'' + data['groups'][i]['id'] + '\')" >Send Message</a>';
                groupHtml += '                </li>';
            }
            if (optionEdit ==true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="setAsCompleteGroup(\'' + data['groups'][i]['id'] + '\',\'' + data['groups'][i]['gp_name'] + '\')">' + lang('set_as_complete') + '</a>';
                groupHtml += '                </li>';
            }
            if (optionEdit ==true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="resetResultGroup(\'' + data['groups'][i]['id'] + '\',\'' + data['groups'][i]['gp_name'] + '\')">' + lang('reset_result') + '</a>';
                groupHtml += '                </li>';
            }
            if (optionEdit ==true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroup(\'' + data['groups'][i]['id'] + '\', \'0\',\'' + data['groups'][i]['gp_name'] + '\')" >Suspend All Students</a>';
                groupHtml += '                </li>';
            }
            if (optionEdit ==true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0);" onclick="changeSubscriptionStatusGroup(\'' + data['groups'][i]['id'] + '\', \'1\',\'' + data['groups'][i]['gp_name'] + '\')" >Approve All Students</a>';
                groupHtml += '                </li>';
            }
            if (optionDelete == true) {
                groupHtml += '                <li>';
                groupHtml += '                    <a href="javascript:void(0)" onclick="removeUserFromCourseGroup(\'' + data['groups'][i]['id'] + '\',\'' + data['groups'][i]['gp_name'] + '\')">' + lang('remove_from_course') + '</a>';
                groupHtml += '                </li>';
            }

            groupHtml += '            </ul>';
            groupHtml += '        </div>';
            groupHtml += '    </div>';
        }
            groupHtml += '</div>';
        }
    }
    return groupHtml;
}


var __group_selected = new Array();
$(document).on('click', '.group-checkbox', function() {
    var group_id = $(this).val();
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __group_selected.push(group_id);
    } else {
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__group_selected, group_id);
    }

    if (__group_selected.length > 0) {
        $("#selected_group_count").html(' (' + __group_selected.length + ')');
    } else {
        $("#selected_group_count").html('');
    }
    if (__group_selected.length > 1) {
        $("#group_bulk").css('display', 'block');
    } else {
        $("#group_bulk").css('display', 'none');
    }

    var numItems = $('.group-checkbox').length;
    if (__group_selected.length == numItems) {
        $('.group-checkbox-parent').prop('checked', true);
    } else {
        $('.group-checkbox-parent').prop('checked', false);
    }

});

$(document).on('click', '.group-checkbox-parent', function() {
    var parent_check_box = this;
    __group_selected = new Array();
    $('.group-checkbox').prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $('.group-checkbox').each(function(index) {
            __group_selected.push($(this).val());
        });
        $('.list-button').removeClass('list-disabled');
    }

    if(__group_selected.length > 0) {
        $("#selected_group_count").html(' (' + __group_selected.length + ')');
    } else {
        $("#selected_group_count").html('');
    }
    if (__group_selected.length > 1) {
        $("#group_bulk").css('display', 'block');
    } else {
        $("#group_bulk").css('display', 'none');
    }
});

function changeSubscriptionStatusGroupBulk(status) {

    if (__group_selected.length > 0) {

        var textLang = 'confirm_approval_for_selected_groups';
        if (status == 0) {
            textLang = 'confirm_suspend_for_selected_groups';
        }
        var messageObject = {
            'body': lang(textLang),
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                'course_id': __course_id,
                'status': status
            },
        };
        callback_warning_modal(messageObject, changeSubscriptionStatusGroupBulkConfirmed);
    }
}

function changeSubscriptionStatusGroupBulkConfirmed(params) {

    $.ajax({
        url: admin_url + 'course/change_subscription_status_group_bulk',
        type: "POST",
        data: {
            "groups": JSON.stringify(__group_selected),
            "course_id": params.data.course_id,
            "status": params.data.status,
            "is_ajax": true
        },
        success: function(response) {
            clearCache();
            var data = $.parseJSON(response);
            if (data['error'] == false) {

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
        }
    });
}

$(document).on('click', '#search_not_add_groups', function() {
    getNotAddedGroups();
});
var __not_added_groups = new Array();

function getNotAddedGroups() {
    var keyword = $('#not_add_group_keyword').val();
    $.ajax({
        url: admin_url + 'course/not_added_groups_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "not_added": true,
            "course_id": __course_id,
            "keyword": keyword
        },
        success: function(response) {
            $('#not_added_groups_wrapper').html(renderNotAddedGroupsHtml(response));
            __not_added_groups = new Array();
            $('.not-added-group-checkbox-parent').prop('checked', false);
        }
    });
}

function renderNotAddedGroupsHtml(response) {
    var data = $.parseJSON(response);
    var groupHtml = '';
    if (data['groups'].length > 0) {
        for (var i = 0; i < data['groups'].length; i++) {
            groupHtml += '<div class="rTableRow" id="not_added_group_row_' + data['groups'][i]['id'] + '">';
            groupHtml += '    <div class="rTableCell"> ';
            groupHtml += '        <label class="pointer-cursor div-style">';
            groupHtml += '            <input type="checkbox" value="' + data['groups'][i]['id'] + '" class="not-added-group-checkbox"> ';
            groupHtml += '            <span ><small>' + data['groups'][i]['gp_name'] + '</small></span>';
            groupHtml += '            <span class="pull-right sm-txt">' + data['groups'][i]['group_strength'] + ' ' + lang('users') + '</span>';
            groupHtml += '        </label>';
            groupHtml += '    </div>';
            groupHtml += '</div>';
        }
    }
    return groupHtml;
}
$(document).on('click', '.not-added-group-checkbox', function() {
    var group_id = $(this).val();
    if ($(this).is(':checked')) {
        __not_added_groups.push(group_id);
    } else {
        removeArrayIndex(__not_added_groups, group_id);
    }
});

$(document).on('click', '.not-added-group-checkbox-parent', function() {
    var parent_check_box = this;
    __not_added_groups = new Array();
    $('.not-added-group-checkbox').prop('checked', $(parent_check_box).is(':checked'));
    if ($(parent_check_box).is(':checked') == true) {
        $('.not-added-group-checkbox').each(function(index) {
            __not_added_groups.push($(this).val());
        });
    }
});

function addGroupToCourse() {
    if (__not_added_groups.length <= 0) {
        lauch_common_message('Choose atleats one group', ' Please choose atleast one group.');
        return false;
    }
    var mail_after_subscription = $('#mail_after_group_subscription').prop('checked');
    mail_after_subscription = (mail_after_subscription == true) ? '1' : '0';
    $.ajax({
        url: admin_url + 'course/subscribe_group',
        type: "POST",
        data: {
            "is_ajax": true,
            "course_id": __course_id,
            "groups": JSON.stringify(__not_added_groups),
            'mail_after_subscription': mail_after_subscription
        },
        success: function(response) {
            $('#group_row_wrapper').html(renderGroupsHtml(response));
            for (var i = 0; i < __not_added_groups.length; i++) {
                $('#not_added_group_row_' + __not_added_groups[i]).remove();
            }
            __not_added_groups = new Array();
            $('.not-added-group-checkbox-parent, .not-added-group-checkbox').prop('checked', false);
        }
    });
}

function createGroup() {
    $('#group-name').modal('show');
}

function saveGroup() {
    cleanPopUpMessage();
    var group_name = $('#course_group_name').val();
    __group_id = 0;
    if (group_name == '') {
        $('#group-name .modal-body').prepend(renderPopUpMessage('error', 'Please enter group name'));
        scrollToTopOfPage();
        return false;
    }
    $.ajax({
        url: admin_url + 'course/save_group',
        type: "POST",
        data: {
            "is_ajax": true,
            "group_name": group_name
        },
        success: function(response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                __group_id = data['group_id'];
                var groupHtml = '';
                groupHtml += '<div class="rTableRow" id="not_added_group_row_' + data['group_id'] + '">';
                groupHtml += '    <div class="rTableCell"> ';
                groupHtml += '        <label class="pointer-cursor div-style">';
                groupHtml += '            <input type="checkbox" value="' + data['group_id'] + '" class="not-added-group-checkbox"> ';
                groupHtml += '            <span >GROUP - <small>' + group_name + '</small></span>';
                groupHtml += '            <span class="pull-right sm-txt" id="group_user_count_' + data['group_id'] + '"> 0 ' + lang('users') + '</span>';
                groupHtml += '        </label>';
                groupHtml += '    </div>';
                groupHtml += '</div>';
                $('#not_added_groups_wrapper').append(groupHtml);
                $('#group-name').modal('hide');
                $('#attach-group').modal('show');
                $('#course_group_name').val('');
                //rendering attach user modal
                $('#course_group_create').html(lang('attach_users_to') + ' - ' + group_name);
                $('#create_group_users').click({
                    'group_id': __group_id
                }, attachUsers);
                //end
            } else {
                $('#group-name .modal-body').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
        }
    });
}
var __group_id = 0;

function attachUsers(param) {
    if (__users_selected.length == 0) {
        $('#add-user-to-group .modal-body').prepend(renderPopUpMessage('error', 'Please select atleast one user to group'));
        scrollToTopOfPage();
        return false;
    }
    $.ajax({
        url: admin_url + 'course/save_group_users',
        type: "POST",
        data: {
            "is_ajax": true,
            "group_id": __group_id,
            "user_ids": JSON.stringify(__users_selected)
        },
        success: function(response) {
            var data = $.parseJSON(response);
            $('#attach-group').modal('hide');
            $('#group_user_count_' + __group_id).html(__users_selected.length + ' ' + lang('users'))
            __group_id = 0;
        }
    });
}

$(document).on('click', '#search_user_for_new_group', function() {
    var keyword = $('#user_for_new_group_keyword').val();
    $.ajax({
        url: admin_url + 'user/users_json',
        type: "POST",
        data: {
            "is_ajax": true,
            "keyword": keyword,
            "group_id": __group_id,
            'not_deleted': '1'
        },
        success: function(response) {
            var data = $.parseJSON(response);
            var userHtml = '';
            $('.users-to-add-in-new-group').remove();
            if (data['users'].length > 0) {
                for (var i = 0; i < data['users'].length; i++) {
                    userHtml += '<div class="checkbox-wrap users-to-add-in-new-group" id="user_new_group_' + data['users'][i]['id'] + '">';
                    userHtml += '    <span class="chk-box">';
                    userHtml += '        <label class="font14">';
                    userHtml += '            <input type="checkbox" value="' + data['users'][i]['id'] + '" class="select-users-new-group">';
                    userHtml += '            ' + data['users'][i]['us_name'] + '';
                    userHtml += '        </label>';
                    userHtml += '    </span>';
                    userHtml += '    <span class="email-label pull-right">';
                    userHtml += '        <span>' + data['users'][i]['us_email'] + '</span>';
                    userHtml += '    </span>';
                    userHtml += '</div>';
                }
                $('#users_new_group_wrapper').append(userHtml);
            }
        }
    });
});

var __users_selected = new Array();
$(document).on('click', '.select-users-new-group', function() {
    var user_id = $(this).val();
    if ($(this).is(':checked')) {
        __users_selected.push(user_id);
    } else {
        removeArrayIndex(__users_selected, user_id);
    }
});

$(document).on('click', '.select-users-new-group-parent', function() {
    var parent_check_box = this;
    __users_selected = new Array();
    $('.select-users-new-group').prop('checked', $(parent_check_box).is(':checked'));
    if ($(parent_check_box).is(':checked') == true) {
        $('.select-users-new-group').each(function(index) {
            __users_selected.push($(this).val());
        });
    }
});

$(document).on('click', '.cancel-group-creation', function() {
    __users_selected = new Array();
    $('.not-added-group-checkbox, .not-added-group-checkbox-parent').prop('checked', false);
});


function validateEmail(email) {
    var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
    if (filter.test(email)) {
        return true;
    } else {
        return false;
    }
}


/* Created By Kiran
 * from here onwards
 */
function sendMessageToGroups(group_id = '') {

    cleanPopUpMessage();
    if (__group_selected.length > 0) {
        $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('warning', 'System will skip batches with no students. However message will be send to non empty batches.'));
    }
    $('#invite-user-bulk').modal();
    $('#invite_send_subject').val('');
    $('#redactor_invite').redactor('insertion.set', '');
    startTextToolbar();
    $('#message_send_button').attr('onclick', 'sendGroupMsg(' + group_id + ')');
}

function sendGroupMsg(group_id = '') {

    var send_user_bulk_subject = $('#invite_send_subject').val();
    var send_user_bulk_message = btoa($('#redactor_invite').val());

    var errorCount = 0;
    var errorMessage = '';

    var group_ids = [];
    if (__group_selected.length > 0) {
        group_ids = __group_selected;
    } else {
        if (group_id != '' && group_id > 0) {
            group_ids.push(group_id);
        }
    }
    // console.log(group_ids);
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
        url: admin_url + 'course/send_group_message',
        type: "POST",
        data: {
            "is_ajax": true,
            "send_user_subject": send_user_bulk_subject,
            "send_user_message": send_user_bulk_message,
            "group_ids": JSON.stringify(group_ids)
        },
        success: function(response) {
            clearCache();
            var data = $.parseJSON(response);
            if (data['success'] == true) {
                $('#invite-user-bulk').modal('hide');
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            } else {
                $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data['message']));
            }
            $('#message_send_button').text('SEND');

            setTimeout(function() {
                $('#invite-user-bulk').modal('hide');
            }, 2500);
        }
    });
}

function removeUserFromCourseGroup(group_id = '',group_name='') {
   
    var message= '';
    var group_ids = [];
    if (__group_selected.length > 0) {
        group_ids = __group_selected;
        message='Are you sure to remove students under selected batch from the course and override if added ?';
        var messageObject = {
            'body': message,
            'button_yes': 'REMOVE',
            'button_no': 'CANCEL',
            'continue_params': {
                "group_id": group_ids,
                "course_id": __course_id,
                "group_name":group_name
            }
        };
        callback_warning_modal(messageObject, removeUserFromCourseConfirmedGroup);
    } else {
        if (group_id != '' && group_id > 0) {
            group_ids.push(group_id);
            $.ajax({
                url: admin_url+'test_manager/check_override_batch',
                type: "POST",
                data:{"is_ajax":true, "group_id":group_id},
                success: function(response) {
                    var data        = $.parseJSON(response);
                    if(data.length>0){
                        message='Are you sure to remove students under this batch from the course and override ?';   
                    } else {
                        message='Are you sure to remove students under this batch from the course ?';  
                    }
                    var messageObject = {
                        'body': message,
                        'button_yes': 'REMOVE',
                        'button_no': 'CANCEL',
                        'continue_params': {
                            "group_id": group_ids,
                            "course_id": __course_id,
                            "group_name":group_name
                        }
                    };
                    callback_warning_modal(messageObject, removeUserFromCourseConfirmedGroup);
                }
            });
        }
    }
    

   

}

function removeUserFromCourseConfirmedGroup(params) {

    __group_selected = new Array();
    var group_id = params.data.group_id;
    var groupname = params.data.group_name;
    var course_id = params.data.course_id;

    $.ajax({
        url: admin_url + 'course/delete_subscription_group',
        type: "POST",
        data: {
            "group_id": JSON.stringify(group_id),
            "course_id": course_id,
            "is_ajax": true,
            "group_name":groupname,
            "course_title":__course_title
        },
        success: function(response) {

            clearCache();
            var data = $.parseJSON(response);
            
            if (data['error'] == false) {

                for (var i = 0; i < group_id.length; i++) {

                    $('#group_row_' + group_id[i]).remove();
                }
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
            
        }
    });

}

function setAsCompleteGroup(group_id = '',group_name='') {

    var group_ids = [];
    if (__group_selected.length > 0) {
        group_ids = __group_selected;
        var messageObject = {
            'body': 'Are you sure to set course complete for the students under selected Batches ?',
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "group_id": group_ids,
                "course_id": __course_id,
                "group_name":group_name
            },
        };
    } else {
        if (group_id != '' && group_id > 0) {

            group_ids.push(group_id);
            var messageObject = {
                'body':'Are you sure to set course complete for students belong to this Batch ?',
                'button_yes': 'OK',
                'button_no': 'CANCEL',
                'continue_params': {
                    "group_id": group_ids,
                    "course_id": __course_id,
                    "group_name":group_name
                },
            };

        }
    }

    callback_warning_modal(messageObject, setAsCompleteConfirmedGroup);
}

function setAsCompleteConfirmedGroup(params) {

    var group_id = params.data.group_id;
    var groupname = params.data.group_name;
    var course_id = params.data.course_id;

    $.ajax({
        url: admin_url + 'course/set_as_complete_group',
        type: "POST",
        data: {
            "group_id": JSON.stringify(group_id),
            "course_id": course_id,
            "is_ajax": true,
            "group_name":groupname,
            "course_title":__course_title
        },
        success: function(response) {
            // console.log(response);
            clearCache();
            var data = $.parseJSON(response);
            if (data['error'] == false) {

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);

            } else {
                var messageObject = {
                    'body': 'Failed to change',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });

}

function resetResultGroup(group_id = '',group_name='') {

    var group_ids = [];
    if (__group_selected.length > 0) {
        group_ids = __group_selected;
        var messageObject = {
            'body': 'Are you sure to reset result for students under selected batch ?',
            'button_yes': 'OK',
            'button_no': 'CANCEL',
            'continue_params': {
                "group_id": group_ids,
                "course_id": __course_id,
                "group_name":group_name
            },
        };
    } else {
        if (group_id != '' && group_id > 0) {

            group_ids.push(group_id);
            var messageObject = {
                'body':' Are you sure to reset result for students under this batch ?',
                'button_yes': 'OK',
                'button_no': 'CANCEL',
                'continue_params': {
                    "group_id": group_ids,
                    "course_id": __course_id,
                    "group_name":group_name
                },
            };

        }
    }

    callback_warning_modal(messageObject, resetResultConfirmedGroup);

}

function resetResultConfirmedGroup(params) {

    var group_id = params.data.group_id;
    var groupname = params.data.group_name;
    var course_id = params.data.course_id;
    $.ajax({
        url: admin_url + 'course/reset_result_group',
        type: "POST",
        data: {
            "group_id": JSON.stringify(group_id),
            "course_id": course_id,
            "is_ajax": true,
            "group_name":groupname,
            "course_title":__course_title
        },
        success: function(response) {
            clearCache();
            var data = $.parseJSON(response);
            if (data['error'] == false) {

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);

            } else {
                var messageObject = {
                    'body': 'Failed to change',
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function changeSubscriptionStatusGroup(group_id, status,groupname = '') {

    var actionLabel = 'approval';
    var actionMessage = 'message_approve_group';
    var group_name = $('#group_row_' + group_id).attr('data-name');
    if (status == 0) {
        actionLabel = 'suspend';
        actionMessage = 'message_suspend_group';
    }
    var messageObject = {
        'body': lang(actionMessage) + ' <b>' + group_name + '</b>. ',
        'button_yes': 'OK',
        'button_no': 'CANCEL',
        'continue_params': {
            "group_id": group_id,
            "course_id": __course_id,
            "status": status,
            "group_name":groupname
        }
    };
    callback_warning_modal(messageObject, changeSubscriptionStatusConfirmedGroup);
}

function changeSubscriptionStatusConfirmedGroup(params) {
    $.ajax({
        url: admin_url + 'course/change_subscription_status_group',
        type: "POST",
        data: {
            "group_id": params.data.group_id,
            "course_id": params.data.course_id,
            "status": params.data.status,
            "group_name":params.data.group_name,
            "is_ajax": true,
            "course_title":__course_title
        },
        success: function(response) {
            clearCache();
            var data = $.parseJSON(response);
            if (data['error'] == false) {

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
        }
    });
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}
function clearCache(){

    __offset = 1;
    __group_selected = new Array();
    $("#selected_group_count").html('');
    $('.group-checkbox-parent, .group-checkbox').prop('checked', false);
    $("#group_bulk").css('display', 'none');
    
    timeOut = setTimeout(function() {
        getGroups();
    }, 600);
}