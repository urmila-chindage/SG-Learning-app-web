__participants              = jQuery.parseJSON(__participants);
__institutes                = jQuery.parseJSON(__institutes);
__groups                    = jQuery.parseJSON(__groups);
__selectedParticipants      = new Array();
var __groupSelected         = 0;
var __instituteSelected     = 0;

$( document ).ready(function() {
    $('#event_row_wrapper').html(renderParticipants(__participants));
    $('#inst_ul').html(renderInstitures(__institutes));
    $('#gp_ul').html(renderGroups(__groups));
});

function renderGroups(groups){
    var gruopsHtml = '';

    $.each(groups,function(g_key,group){
        gruopsHtml      += '<li><a href="javascript:void(0)" onclick="groupFilter(\''+btoa(group['id'])+'\')">'+group["gp_name"]+'</a></li>';
    });
    return gruopsHtml;
}

function renderInstitures(institutes){
    var insHtml = '';

    $.each(institutes,function(i_key,institute){
        insHtml      += '<li><a href="javascript:void(0)" onclick="instituteFilter(\''+btoa(institute['id'])+'\')">'+institute["us_name"]+'</a></li>';
    });

    return insHtml;
}


function renderParticipants(participants){
    var pHtml   = '';
    $.each(participants,function(p_key,participant){
        pHtml   += renderParticipant(participant);
    });

    return pHtml;
}

function renderParticipant(participant){
    var pHtml   = '';

    pHtml       += '<div class="rTableRow" id="user_row_'+participant['ep_user_id']+'" data-name="user'+participant['ep_user_id']+'">';
    pHtml       += '<div class="rTableCell">';
    pHtml       += '<span class="icon-wrap-round img">';
    if(participant['us_image'] == 'default.jpg'){
        pHtml       += '<img src="'+__default_image+participant['us_image']+'">';
    }else{
        pHtml       += '<img src="'+__image_url+participant['us_image']+'">';
    }
    pHtml       += '</span>';
    pHtml       += '<span class="wrap-mail ellipsis-hidden">';
    pHtml       += '<div class="ellipsis-style">';
    pHtml       += '<a href="javascript:void(0)">'+participant['us_name']+'</a>';
    pHtml       += '<br>'+participant['us_email'];
    pHtml       += '</div>';
    pHtml       += '</span>';
    pHtml       += '</div>';
    pHtml       += '<div class="rTableCell pad0">';
    pHtml       += '<div class="col-sm-12 pad0">';
    pHtml       += '<label class="pull-right label label-success">Added</label>';
    pHtml       += '</div>';
    pHtml       += '<div class="col-sm-12 pad0 pad-vert5 pos-inhrt">';
    pHtml       += '<span class="pull-right spn-active">Added by- '+participant['added_by']+' on 10 Aug 2017</span>';
    pHtml       += '</div>';
    pHtml       += '</div>';
    pHtml       += '<div class="td-dropdown rTableCell">';
    pHtml       += '<div class="btn-group lecture-control">';
    pHtml       += '<span class="dropdown-tigger" data-toggle="dropdown">';
    pHtml       += '<span class="label-text">';
    pHtml       += '<i class="icon icon-down-arrow"></i>';
    pHtml       += '</span>';
    pHtml       += '<span class="tilder"></span>';
    pHtml       += '</span>';
    pHtml       += '<ul class="dropdown-menu pull-right" role="menu">';
    pHtml       += '<li><a href="javascript:void(0)" onclick="removeParticipant(\''+btoa(participant["ep_user_id"])+'\')">Remove</a></li>';
    pHtml       += '</ul>';
    pHtml       += '</div>';
    pHtml       += '</div>';
    pHtml       += '</div>';

    return pHtml;
}


function addStudents(){
    __selectedParticipants.length = 0;
    $('#filter_dropdown_inst').html('All Students <span class="caret"></span>');
    $.ajax({
        url: __admin_url+'event/getusers',
        type: "POST",
        data:{ "is_ajax":true,"event_id":__event_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('.modal_users_list').html(renderModalParticipants(data['data']));
                $('#attach_event_users').modal('show');
            }
        }
    });
}

function renderModalParticipants(participants){
    var pHtml           = '';

    $.each(participants,function(p_key,participant){
        pHtml += renderModalParticipant(participant);
    });

    return pHtml;
}

function renderModalParticipant(participant){
    var pHtml       = '';

    pHtml       += '<div class="checkbox-wrap users-to-add-in-new-group" id="user_new_group_3">';
    pHtml       += '<span class="chk-box">';
    pHtml       += '<label class="font14">';
    pHtml       += '<input type="checkbox" value="'+participant['id']+'" class="select-users-new-group">'+participant['us_name'];
    pHtml       += '</label>';
    pHtml       += '</span>';
    pHtml       += '<span class="email-label pull-right">';
    pHtml       += '<span>'+participant['us_email']+'</span>';
    pHtml       += '</span>';
    pHtml       += '</div>';

    return pHtml;
}

$(document).on('click', '.select-users-new-group-parent', function(){
    var parent_check_box = this;
    __selectedParticipants = new Array();    
    $( '.select-users-new-group' ).prop('checked', $(parent_check_box).is(':checked'));
    if ($(parent_check_box).is(':checked') == true) {
        $( '.select-users-new-group' ).each(function( index ) {
           __selectedParticipants.push($( this ).val());
        });
    }
});

$(document).on('click', '.select-users-new-group', function(){
    var user_id = $(this).val();
    if ($(this).is(':checked')) {
        __selectedParticipants.push(user_id);
    }else{
        removeArrayIndex(__selectedParticipants, user_id);
    }
});

function addSelectedParticipants(){
    if(__selectedParticipants.length == 0){
        return false;
    }

    $.ajax({
        url: __admin_url+'event/addeventusers',
        type: "POST",
        data:{ "is_ajax":true,"event_id":__event_id,'event_users':JSON.stringify(__selectedParticipants)},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#event_row_wrapper').html(renderParticipants(data['data']));
                $("#addStudentCancelin").click();
                $("#addStudentCancelgp").click();
            }
        }
    });
}

function addGroups(){
    __selectedParticipants.length = 0;
    $('.modal_users_list').html('');
    $('#filter_dropdown_gp').html('Select Group <span class="caret"></span>');
    $('#attach_event_users_group').modal('show');
}

function groupFilter(groupId){
    __instituteSelected     = 0;
    __groupSelected         = atob(groupId);
    let index = __groups.findIndex( (group) => group.id === __groupSelected);
    $('#filter_dropdown_gp').html(__groups[index]['gp_name']+'<span class="caret"></span>');
    getStudents();
}

function instituteFilter(instituteId){
    __groupSelected     = 0;
    __instituteSelected = atob(instituteId);
    let index = __institutes.findIndex( (institute) => institute.id === __instituteSelected);
    $('#filter_dropdown_inst').html(__institutes[index]['us_name']+'<span class="caret"></span>');
    getStudents();
}

function getStudents(){
    $('.modal_users_list').html('');
    $.ajax({
        url: __admin_url+'event/getusers',
        type: "POST",
        data:{ "is_ajax":true,"event_id":__event_id,'institute_id':__instituteSelected,'group_id':__groupSelected},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('.modal_users_list').html(renderModalParticipants(data['data']));
            }
        }
    });
}

function removeParticipant(participant){
    var removeId = atob(participant);
    $.ajax({
        url: __admin_url+'event/removeuser',
        type: "POST",
        data:{ "is_ajax":true,"event_id":__event_id,'user_id':removeId},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['success'] == true)
            {
                $('#user_row_'+removeId).remove();
            }
        }
    });
}