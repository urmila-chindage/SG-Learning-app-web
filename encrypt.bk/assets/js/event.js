const __previlage       = new Access();
var __filter_dropdown   = '';
    __events            = $.parseJSON(__events);
    __courses           = $.parseJSON(__courses);
    __institutes        = $.parseJSON(__institutes);
    __batches           = $.parseJSON(__batches);
    var _institutesTemp = __institutes;
    
    if((_institutesTemp != null) && (Object.keys(_institutesTemp).length > 1)) {
        __institutes = new Object;
        $.each(_institutesTemp, function(instituteKey, institute){
            if(typeof institute.id === 'undefined'){
                __institutes[instituteKey] = institute;
            }else{
            __institutes[institute.id] = institute;
            
            }
        });
    }
    
    var instituteHtml       = '<option value="all">Select Institutes</option>';
    var _instituteBatches   = new Object;
    var _batchCount         = 0;
    
    if( Object.keys(__batches).length > 0) {
        $.each(__batches, function(batchKey, batch){
            if(typeof _instituteBatches[batch['gp_institute_id']] == 'undefined' ) {
                _instituteBatches[batch['gp_institute_id']] =  new Object;
                if(typeof __institutes[batch['gp_institute_id']] != 'undefined') {
                    var institute  =  __institutes[batch['gp_institute_id']];
                    instituteHtml += '<option value="'+institute['id']+'">'+institute['ib_institute_code']+' - '+institute['ib_name']+'</option>';
                    
                }else{
                    instituteHtml += '<option value="'+__institutes['id']+'">'+__institutes['ib_institute_code']+' - '+__institutes['ib_name']+'</option>';
                }
            }
            _instituteBatches[batch['gp_institute_id']][batch['id']] =  batch;
            _batchCount++;
        });
    }

    var __offset            = 1;
    
$(function(){
    $('#event_institute_batches').html(instituteHtml);
    var filter  = getQueryStringValue('filter');
    var keyword = getQueryStringValue('keyword');

        filter  = (filter == '')?'active':filter;
        __filter_dropdown = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    
    if (keyword != '') {
        keyword = keyword.split('-').join(' ');
        $('#event_keyword').val(keyword);
        $('#searchclear').show();
    }
    $('#event_row_wrapper').html(renderEvents(__events));
    renderPagination(__offset, __totalEvents);
});

var timeOut = null;
$(document).on('keyup', '#event_keyword', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getEvents();
    }, 600);
});

$(document).on('click', '#searchclear', () =>  {
    clearTimeout(timeOut);
    timeOut = setTimeout(function () {
        __offset = 1;
        getEvents();
    }, 600);
});

function renderEvents(events){
    var eventHtml = '';
    if(Object.keys(events).length > 0 ) {
        $.each(events,function(event_key,event){
            eventHtml   += '<div class="rTableRow event-listing-row" id="event_row_'+event['id']+'">';
            eventHtml   +=      renderEvent(event);
            eventHtml   += '</div>';
        });  
        var total_events = (__totalEvents>1)?__totalEvents+' Events':__totalEvents+' Event';
        __visible_events = Number(Object.keys(events).length);
        $('#total-events').html(total_events);
        //$('#visible-events').html(__visible_events);

    } else {
        eventHtml += '<div id="popUpMessage" class="alert alert-danger">No events scheduled.</div>';
    }
    return eventHtml;
}

function renderEvent(event) {
    var eventHtml    = '';
        eventHtml   += '    <div class="rTableCell">';
        eventHtml   += '        <span class="icon-wrap-round color-box" style="margin-left:0px;" data-name="'+event['ev_name']+'"><i class="icon icon-calendar-1"></i></span>';
        eventHtml   += '        <span class="wrap-mail ellipsis-hidden">';
        eventHtml   += '            <div class="ellipsis-style">';
        eventHtml   += '                <a href="javascript:void(0)">'+event['ev_name']+'</a> <br>';
        eventHtml   += '            </div>';
        eventHtml   += '        </span>';
        eventHtml   += '    </div>';

        var inputDate   = new Date(event['ev_date']);//console.log(event['ev_date']);
        var todaysDate  = new Date();
        if(inputDate.setHours(0,0,0,0) >= todaysDate.setHours(0,0,0,0)) {
            eventHtml   += '    <div class="rTableCell text-center ">';
            eventHtml   += '        <span class="text-green">Upcoming</span> ';
            eventHtml   += '    </div>';
        }else if(inputDate.setHours(0,0,0,0) < todaysDate.setHours(0,0,0,0)){
            eventHtml   += '    <div class="rTableCell text-center ">';
            eventHtml   += '        <span class="text-red">Finished</span> ';
            eventHtml   += '    </div>';
        }
       

        var actionClass    = 'label-warning';
        var actionLabel    = 'Inactive';
        var actionText     = 'Activate';
        if((event['ev_status'] == '1')) {
            actionClass = 'label-success';
            actionLabel = 'Active';
            actionText  = 'Deactivate';
        }
        eventHtml   += '    <div class="rTableCell pad0">';
        eventHtml   += '        <div class="col-sm-12 pad0">';
        eventHtml   += '            <label style="margin-right:15px;" class="pull-right label '+actionClass+'" id="action_class_'+event['id']+'">'+actionLabel+'</label>';
        eventHtml   += '        </div>';
        eventHtml   += '        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt"></div>';
        eventHtml   += '    </div>';
    
        if( __previlage.hasAdd()  == true ||  __previlage.hasEdit()  == true || __previlage.hasDelete()  == true ) {            
            eventHtml   += '    <div class="td-dropdown rTableCell">';
            eventHtml   += '        <div class="btn-group lecture-control">';
            eventHtml   += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            eventHtml   += '                <span class="label-text"><i class="icon icon-down-arrow"></i></span>';
            eventHtml   += '                    <span class="tilder"></span>';
            eventHtml   += '                </span>';
            eventHtml   += '                <ul class="dropdown-menu pull-right" role="menu" id="event_action_'+event['id']+'">';
            if(__previlage.hasEdit()  == true) {
                eventHtml   += '                    <li><a href="javascript:void(0)" onclick="changeEventStatus(\''+event['id']+'\', \''+event['ev_status']+'\', \''+btoa(event['ev_name'])+'\')">'+actionText+'</a></li>';
                eventHtml   += '                    <li><a href="'+__admin_url+'event/basic/'+event['id']+'">Settings</a></li>';    
            }
            if(__previlage.hasAdd()  == true) {
                eventHtml   += '                    <li><a href="javascript:void(0)" onclick="inviteParticipant(\''+event['id']+'\', \''+btoa(event['ev_name'])+'\')">Invite Participant</a></li>';
            }
            if(__previlage.hasDelete()  == true) {
                eventHtml   += '                    <li><a href="javascript:void(0)" onclick="deleteEvent(\''+event['id']+'\', \''+btoa(event['ev_name'])+'\')">Delete</a></li>';
            }
            eventHtml   += '                </ul>';
            eventHtml   += '        </div>';
            eventHtml   += '    </div>';
        }
    return eventHtml;
}

function getQueryStringValue(key) {
    return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
}

function filter_events_by(filter) {
    __offset            = 1;
    __filter_dropdown   = filter;
    if (filter == 'all') {
        $('#event_keyword').val('');
    }
    $('#filter_dropdown_text').html($('#filer_dropdown_list_' + filter).text() + '<span class="caret"></span>');
    getEvents();
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
$(document).on('click', '.locate-page', () =>  {
    __offset = $(this).attr('data-page');
    getEvents();
});

var __gettingEventsInProgress = false;
function getEvents(){
    if (__gettingEventsInProgress == true) {
        return false;
    }
    __gettingEventsInProgress = true;
    var keyword = $('#event_keyword').val().trim();
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
        url: __admin_url+'event/ajaxgetevents',
        type: "POST",
        data:{             
            "is_ajax": true,
            "filter": __filter_dropdown,
            'limit': __limit,
            "keyword": keyword,
            'offset': __offset
        },
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['success'] == true) {
                $('#event_row_wrapper').html(renderEvents(data['events']));
            }
            renderPagination(__offset, data['total_events']);
            __totalEvents    = data['total_events'];
            var total_events = (__totalEvents>1)?__totalEvents+' Events':__totalEvents+' Event';
            __visible_events = Number(Object.keys(data['events']).length);
            $('#total-events').html(total_events);
            //$('#visible-events').html(__visible_events);
            __gettingEventsInProgress = false;
        }
    });
}
$(document).ready(function(){
    $('input:radio[name="event_type"]').change(function(){
        if ($(this).val() == '1') {
            
            $('#studio-wrapper').css('visibility','visible');
        }else{
            $('#studio_list').val('');
            $('#studio-wrapper').css('visibility','hidden');
        }
    });
    
    // const injectJqueryUi = document.createElement('script');
    // injectJqueryUi.setAttribute('src', __jqueryUi__);
    // document.head.appendChild(injectJqueryUi);
});
// var today = new Date();
var __datePickerInit = false;
function addEventInit() {
    loadDatePickerFiles();
    $('#studio_list').val('');
    $('#popUpMessage').remove();
    $('#event_name, #event_date, #event_time').val('');
    // $('#event_time').timepicker({ timeFormat: 'h:i A' });
    
    $('#short_description').redactor('insertion.set', '');
    $('#short_description').redactor({
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        source: false,
        minHeight: '15vh',
        maxHeight: '15vh',
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function (json, xhr) {
                alert('Please select a valid image');
                return false;
            }
        }
    });
    // if( __datePickerInit == false ) {
    //     $("#event_date").datepicker({
    //         language: 'en',
    //         minDate: today,
    //         format: 'dd-mm-yyyy',
    //         autoClose: true
    //     });
    //     __datePickerInit = true;
    // }
    $('#new_event').modal();
}


function saveEvent() {
    var eventName       = $.trim($('#event_name').val());
    var eventDesc       = $.trim($('#short_description').val());
    var eventDate       = $.trim($('#event_date').val());
    var eventTime       = $.trim($('#event_time').val());
    var eventType       = $.trim($('input[name="event_type"]:checked').val());
    var studio_list     = $.trim($('#studio_list').val());
    
    var message         = [];

    if(eventName == '') {
        message.push('Event name cannot be empty');
    }
    if(eventDate == '') {
        message.push('Event date cannot be empty');
    }

    if(stripHtmlTags(eventDesc) == '') {
        message.push('Event description cannot be empty');
    }
    if(eventTime == '') {
        message.push('Event time cannot be empty');
    }

    if(eventType == '') {
        message.push('Event type cannot be empty');
    }
    if(eventType=='1'){
        if(studio_list == '') {
            message.push('select an Event Studio');
        }
    }
    

    if(message.length > 0 ) {
        $('#new_event .modal-body').prepend(renderPopUpMessage('error', message.join('<br />')));
    } else {
        $.ajax({
            url: __admin_url+'event/addnewevent',
            type: "POST",
            data:{ "is_ajax":true,"event_name":eventName,'event_date':eventDate,'event_time':eventTime,'event_description':eventDesc, 'event_type':eventType,'event_studio':studio_list},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['success'] == true)
                {
                    $('#new_event').modal('hide');
                    __totalEvents    = parseInt(__totalEvents)+1;
                    var total_events = (__totalEvents>1)?__totalEvents+' Events':__totalEvents+' Event';
                    __visible_events = (__visible_events)<__limit?parseInt(__visible_events)+1:__visible_events;
                    $('#total-events').html(total_events);
                    //$('#visible-events').html(__visible_events);

                    var eventDated = eventDate.split("-").reverse().join("-");
                    //render events
                    var eventHtml = '';
                        eventHtml   += '<div class="rTableRow event-listing-row" id="event_row_'+data['event_id']+'" >';
                        eventHtml   +=      renderEvent({"ev_name":eventName, "ev_status":"1", "id":data['event_id'],'ev_date':eventDated});
                        eventHtml   += '</div>';   
                    $('#event_row_wrapper').prepend(eventHtml);
                    //end
                    setTimeout(function () {
                        inviteParticipant(data['event_id'], btoa(eventName));                        
                    }, 300);

                }
            }
        });    
    }
}

function changeEventStatus(event_id, action, event_name) {
    var ok_button_text  = (action == '0')?'ACTIVATE':'DEACTIVATE';
        event_name      = atob(event_name);
    var header_text     = 'Are you sure to ' + (ok_button_text.toLowerCase()) + ' the event named "' + event_name + '"';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
            'event_id': event_id
           ,'status': ((action != '0')?'0':'1'),
           'event_name':event_name
        },
    };
    callback_warning_modal(messageObject, changeEventStatusConfirmed);
}

function changeEventStatusConfirmed(params) {
    var event_id    = params.data.event_id;
    var status      = params.data.status;
    var event_name  = params.data.event_name;
    $.ajax({
        url: admin_url + 'event/change_status',
        type: "POST",
        data: {
            "event_id": event_id,
            "status": status,
            "is_ajax": true,
            "event_name":event_name
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                if (__filter_dropdown == 'all') {
                    $('#event_row_' + event_id).html(renderEvent(data['event']));
                } else {
                    $('#event_row_' + event_id).remove();
                    __totalEvents = __totalEvents - 1;
                }

                var messageObject = {
                    'body': 'Event status changed successfully',
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
        }
    });
}


function deleteEvent(event_id, event_name) {
        event_name      = atob(event_name);
    var header_text     = 'Are you sure to delete the event named "' + event_name + '"';

    var messageObject = {
        'body': header_text,
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            'event_id': event_id,
            'event_name':event_name
        },
    };
    callback_warning_modal(messageObject, deleteEventConfirmed);
}

function deleteEventConfirmed(params) {
    var event_id    = params.data.event_id;
    var event_name  = params.data.event_name;
    $.ajax({
        url: admin_url + 'event/delete_event',
        type: "POST",
        data: {
            "event_id": event_id,
            "is_ajax": true,
            "event_name":event_name
        },
        success: function (response) {
            var data = $.parseJSON(response);
            if (data['error'] == false) {
                $('#event_row_' + event_id).remove();
                __totalEvents    = __totalEvents - 1;
                __visible_events = __visible_events -1;
                refreshListing();
                var messageObject = {
                    'body': 'Event deleted successfully',
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

function refreshListing() {
    if ($('.pagination li.active a').hasClass('pagination-last-page') == true) {
        if ($('.event-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
            __offset = __offset - 1;
            if (__offset == 0) {
                __offset = 1;
            }
            getEvents();
        }
    } else {
        if ($('.event-listing-row').length == 0) {
            __offset = $('.pagination li.active a').attr('data-page');
        }
        getEvents();
    }
}

function inviteParticipant(eventId, eventName) {
    eventName = atob(eventName);
    var invitationType = $("input[name=invitation_type]:checked").val();
    $('#process_title').html('INVITE PARTICIPANTS - '+(eventName.toUpperCase()));

    $('.invitation-type-wrapper').hide();
    $('.invitation-type').prop('checked', false);
    $('#popUpMessage').remove();
    $("input[name='invitation_type'][value='course']").prop('checked', true).trigger('change');

    $('#invite_participant_confimed').unbind('click');
    $('#invite_participant_confimed').click({"eventId":eventId,"eventName":eventName}, inviteParticipantConfirmed);
    $('#invite_participant').modal();
}

function courseObjects() {
    var courseHtml      = '';
    var courseCount     = 0;
    var courseKeyword   = $.trim($('#course_keyword').val()).toLowerCase();
    //console.log(__courses);
    if(Object.keys(__courses).length > 0) {
        $.each(__courses, function(courseKey, course){
            if(!(courseKeyword != '' && course['cb_title'].toLowerCase().search(courseKeyword) == -1 && course['cb_code'].toLowerCase().search(courseKeyword) == -1)) {
                courseHtml += '<div class="checkbox-wrap invite-course-list">';
                courseHtml += '    <span class="chk-box">';
                courseHtml += '        <label class="font14">';
                courseHtml += '            <input '+((inArray(course['id'], __courseSelected))?'checked="checked"':'')+' type="checkbox" data-type="course" class="inst-course" value="'+courseKey+'">';
                courseHtml += '            <span class="inst-name">'+course['cb_code']+' - '+course['cb_title']+'</span>';
                courseHtml += '        </label>';
                courseHtml += '    </span>';
                courseHtml += '    <span class="email-label pull-right"></span>';
                courseHtml += '</div>';
                courseCount++;
            }
        });
    } else {
        courseHtml += '<p class="errortext">No course found</p>';
    }
    $('#type_course').show();
    $('#type_course .invitation-content-wrapper').html(courseHtml);
    refreshCourseItemCount();
}
function instituteObjects() {
    var instituteHtml      = '';
    var instituteCount     = 0;
    var instituteKeyword   = $.trim($('#institute_keyword').val()).toLowerCase();
    console.log(__institutes, 'instituteObjects');
    if( Object.keys(__institutes).length > 0) {
        
        if(typeof __institutes.id === 'undefined'){

            $.each(__institutes, function(instituteKey, institute){
                if(!(instituteKeyword != '' && institute['ib_name'].toLowerCase().search(instituteKeyword) == -1 && institute['ib_institute_code'].toLowerCase().search(instituteKeyword) == -1)) {
                    instituteHtml += '<div class="checkbox-wrap invite-institute-list">';
                    instituteHtml += '    <span class="chk-box">';
                    instituteHtml += '        <label class="font14">';
                    instituteHtml += '            <input '+((inArray(institute['id'], __instituteSelected))?'checked="checked"':'')+' type="checkbox" data-type="institute" class="inst-institute" value="'+institute['id']+'">';
                    instituteHtml += '            <span class="inst-name">'+institute['ib_institute_code']+' - '+institute['ib_name']+'</span>';
                    instituteHtml += '        </label>';
                    instituteHtml += '    </span>';
                    instituteHtml += '    <span class="email-label pull-right"></span>';
                    instituteHtml += '</div>';
                    instituteCount++;
                }
            });
            
        }else{
            
            var institute = __institutes;
            if(!(instituteKeyword != '' && institute['ib_name'].toLowerCase().search(instituteKeyword) == -1 && institute['ib_institute_code'].toLowerCase().search(instituteKeyword) == -1)) {
                instituteHtml += '<div class="checkbox-wrap invite-institute-list">';
                instituteHtml += '    <span class="chk-box">';
                instituteHtml += '        <label class="font14">';
                instituteHtml += '            <input '+((inArray(institute['id'], __instituteSelected))?'checked="checked"':'')+' type="checkbox" data-type="institute" class="inst-institute" value="'+institute['id']+'">';
                instituteHtml += '            <span class="inst-name">'+institute['ib_institute_code']+' - '+institute['ib_name']+'</span>';
                instituteHtml += '        </label>';
                instituteHtml += '    </span>';
                instituteHtml += '    <span class="email-label pull-right"></span>';
                instituteHtml += '</div>';
                instituteCount++;
            }
        }
    } else {
        instituteHtml += '<p class="errortext">No institute found</p>';
    }
    $('#type_institute').show();
    $('#type_institute .invitation-content-wrapper').html(instituteHtml);
    refreshInstituteItemCount();
}
function batchObjects() {
    $('#event_institute_batches').val('all').trigger('change');
    $('#type_batch').show();
    refreshBatchItemCount();
    initToolTip();
}

$(document).on('change', '#event_institute_batches', function(){
    var selectedInstitute = $('#event_institute_batches option:selected').text();
        $(this).attr('data-original-title', selectedInstitute);
        if(selectedInstitute.length > 48) {
            //console.log('enabled');
            // $("#event_institute_batches").tooltip("enable"); 
        } else {
            //console.log('disbled');
            // $("#event_institute_batches").tooltip("disable"); 
        }
        $(this).blur();
    var instituteId = $(this).val();
    var batchHTml   = '';
    var _checked    = '';
    var batches     = typeof _instituteBatches[instituteId] != 'undefined' ? _instituteBatches[instituteId] : {};
    if(Object.keys(batches).length > 0 ) {
        $.each(batches, function(batchKey, batch){
            _checked   = (inArray(batch['id'], __batchSelected)==true)?'checked="checked"':''; 
            batchHTml += '';
            batchHTml += '<div class="checkbox-wrap invite-batch-list">';
            batchHTml += '    <span class="chk-box">';
            batchHTml += '        <label class="font14">';
            batchHTml += '            <input '+_checked+' type="checkbox" data-type="batch" class="inst-batch" value="'+batch['id']+'">';
            batchHTml += '            <span class="inst-name">'+batch['gp_institute_code']+' - '+batch['gp_year']+' - '+batch['gp_name']+'</span>';
            batchHTml += '        </label>';
            batchHTml += '    </span>';
            batchHTml += '    <span class="email-label pull-right"></span>';
            batchHTml += '</div>';
        });
    }
    if(instituteId == 'all') {
        $('#selected_batch_bar').addClass('disabled-batch');
    } else {
        $('#selected_batch_bar').removeClass('disabled-batch');
    }
    $('#type_batch .invitation-content-wrapper').html(batchHTml);
    refreshBatchItemCount();
});



$(document).on('change', '.invitation-type', function(){
    $('#total_batch_selected').hide();
    __courseSelected = [], __instituteSelected = [], __batchSelected = [];
    $('.invitation-type-wrapper').hide();
    $('#course_keyword, #institute_keyword').val('');
    $('.searchclear').hide();


    switch($(this).val()) {
        case "course":
            courseObjects();
        break;
        case "institute":
            instituteObjects();
        break;
        case "batch":
            batchObjects();
        break;
    }
});


function inviteParticipantConfirmed(params) {
    var invitationType = $("input[name='invitation_type']:checked").val();
    if(__courseSelected.length == 0 && __instituteSelected.length == 0 && __batchSelected.length == 0) {
        $('#invite_participant .modal-body').prepend(renderPopUpMessage('error', 'Please choose atleast one participant to invite!'));
        return false;            
    }
    $.ajax({
        url: __admin_url+'event/send_invitation',
        type: "POST",
        data:{             
            "is_ajax": true,
            "event_name":params.data.eventName,
            "event_id": params.data.eventId,
            'invitation_type': invitationType,
            "course_selected": __courseSelected,
            "institute_selected": __instituteSelected,
            "batch_selected": __batchSelected,
        },
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#invite_participant').modal('hide');
            if(data['success'] == true) {
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
                callback_error_modal(messageObject);            
            }
        }
    });
}





var __courseSelected = [], __instituteSelected = [], __batchSelected = [];
$(document).on('change', '.inst-course, .inst-institute, .inst-batch', function(){
    var item = $(this).val();
    var itemChecked = $(this).prop('checked');
    switch($(this).attr('data-type')) {
        case "course":
            if( itemChecked == true) {
                __courseSelected.push(item);
            } else {
                removeArrayIndex(__courseSelected, item);
            }
            refreshCourseItemCount();
        break;
        case "institute":
            if( itemChecked == true) {
                __instituteSelected.push(item);
            } else {
                removeArrayIndex(__instituteSelected, item);
            }
            refreshInstituteItemCount();
        break;
        case "batch":
            if( itemChecked == true) {
                __batchSelected.push(item);
            } else {
                removeArrayIndex(__batchSelected, item);
            }
            refreshBatchItemCount();
        break;
    }
});





$(document).on('change', '.select-all-course', function(){
    if($(this).prop('checked') == true) {
        $( ".inst-course" ).each(function( key, item ) {
            __courseSelected.push(item.value);
            item.checked = true;
        });    
    } else {
        $( ".inst-course" ).each(function( key, item ) {
            removeArrayIndex(__courseSelected, item.value);
            item.checked = false;
        });    
    }
    refreshCourseItemCount();
});
$(document).on('change', '.select-all-institute', function(){
    if($(this).prop('checked') == true) {
        $( ".inst-institute" ).each(function( key, item ) {
            __instituteSelected.push(item.value);
            item.checked = true;
        });    
    } else {
        $( ".inst-institute" ).each(function( key, item ) {
            removeArrayIndex(__instituteSelected, item.value);
            item.checked = false;
        });    
    }
    refreshInstituteItemCount();
});
$(document).on('change', '.select-all-batch', function(){
    if($(this).prop('checked') == true) {
        $( ".inst-batch" ).each(function( key, item ) {
            __batchSelected.push(item.value);
            item.checked = true;
        });    
    } else {
        $( ".inst-batch" ).each(function( key, item ) {
            removeArrayIndex(__batchSelected, item.value);
            item.checked = false;
        });    
    }
    refreshBatchItemCount();
});





$(document).on('keyup', '#course_keyword', function(){
    $('#type_course .searchclear').show();
    courseObjects();
});
$(document).on('keyup', '#institute_keyword', function(){
    $('#type_institute .searchclear').show();
    instituteObjects();
});
$(document).on('click', '#type_institute .searchclear', function(){
    $('#institute_keyword').val('');
    $(this).hide();
    instituteObjects();  
});
$(document).on('click', '#type_course .searchclear', function(){
    $('#course_keyword').val('');
    $(this).hide();
    courseObjects();
});



function refreshCourseItemCount() {
    var totalItem = $( ".inst-course" ).length;
    var totalItemChecked = $( ".inst-course:checked" ).length;
    $('#selected_course_bar').html('<label><input value="1" '+((totalItem == totalItemChecked && totalItem > 0)?'checked="checked"':'')+' class="select-all-course" type="checkbox"> Select All (<span id="selected_course_count">'+totalItemChecked+'</span>/'+(totalItem)+')</label>');
}
function refreshInstituteItemCount() {
    var totalItem = $( ".inst-institute" ).length;
    var totalItemChecked = $( ".inst-institute:checked" ).length;
    $('#selected_institute_bar').html('<label><input value="1" '+((totalItem == totalItemChecked && totalItem > 0)?'checked="checked"':'')+' class="select-all-institute" type="checkbox"> Select All (<span id="selected_institute_count">'+totalItemChecked+'</span>/'+(totalItem)+')</label>');
}
function refreshBatchItemCount() {
    var totalItem = $( ".inst-batch" ).length;
    var totalItemChecked = $( ".inst-batch:checked" ).length;
    $('#selected_batch_bar').html('<label><input value="1" '+((totalItem == totalItemChecked && totalItem > 0)?'checked="checked"':'')+' class="select-all-batch" type="checkbox"> Select All (<span id="selected_batch_count">'+totalItemChecked+'</span>/'+(totalItem)+')</label>');
    var sumTotalBatch = Object.keys(__batchSelected).length;
    if( sumTotalBatch > 0 ) {
        $('#total_batch_selected').html('('+sumTotalBatch+')').show();
    } else {
        $('#total_batch_selected').hide();
    }
}


// $(document).on('keypress', '#event_date, #event_time', function(e){
//     e.preventDefault();
// });
