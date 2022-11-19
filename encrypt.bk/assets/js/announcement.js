__editFlag = false;
__announcementLimit = 5;
__announcementOffset = 0;
__announcementCount = '0';
__userpath = '';
__defaultpath = '';
var __fullGroupCount = '';
var __fullInstituteCount = '';

var __inst_selected = new Array();
var __group_selected = new Array();
var __institute_list_count    = '';

$(document).on('keyup', '#course_keyword', function() {

    var keyword         = $('#course_keyword').val();
    var filter          = keyword.toUpperCase();
    var institute_names = $('.inst-name');
    var institue_list   = $('.institute-list');

    for (var i = 0; i < institute_names.length; i++) {

        if (institute_names[i].innerHTML.toUpperCase().search(filter)!=-1){
            institue_list[i].style.display = "";
        } else {
            institue_list[i].style.display = "none";
        }
    }
    var instituteVisible = $('.institute-list:visible').length;
    if(instituteVisible<=0){
        $('#errortext').show();
        $('.select-all-style').hide();
    }else{
        $('#errortext').hide();
        $('.select-all-style').show();
    }
    institute_count();
    processInstituteCheckbox();
});
function institute_count(){

    if (__inst_selected.length > 0) {
        $("#count_reflect").html(__inst_selected.length);
    } else {
        $("#count_reflect").html('0');
    }
   
}

$(document).on('click', '#searchclear', function() {

    $('#course_keyword').val('');
    var institute_names = $('.inst-name');
    var institue_list   = $('.institute-list');
    for (var i = 0; i < institute_names.length; i++) {
        institue_list[i].style.display = "";
    }
    $('.select-all-style').show();
    processInstituteCheckbox();
});
$(document).on('click', '.inst-course', function() {
    __group_selected = new Array();
    var user_id = $(this).val();
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __inst_selected.push(user_id);
                        
    } else {
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__inst_selected, user_id);
    }
    institute_count();
    processInstituteCheckbox();
});

function selectAll(selectAll, institute) {

    var selectAllBtn    = $('#' + selectAll);
    var institute_list  = $('.' + institute+':visible');
    if (selectAllBtn.is(":checked")) {
        institute_list.each(function() { //loop through each checkbox
            var institute_id = $(this).val();
            __inst_selected.push(institute_id);
            
            $(this).prop('checked', true); //check
        });
        __inst_selected=$.unique(__inst_selected);
        institute_count();
    } else {
        institute_list.each(function() { //loop through each checkbox
            var institute_id = $(this).val();
            removeArrayIndex(__inst_selected, institute_id);
            $(this).prop('checked', false); //uncheck
        });
        
        institute_count();
    }
}

/* =========group ===========*/
$(document).on('click', '.group-course', function() {
    __inst_selected = new Array();
    var user_id = $(this).val();
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __group_selected.push(user_id);
    } else {
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__group_selected, user_id);
    }
    group_count();
});

function selectAllGroup(selectAll, group) {

    var selectAllBtn    = $('#' + selectAll);
    var group_list      = $('.' + group+':visible');
    if (selectAllBtn.is(":checked")) {
        group_list.each(function() { //loop through each checkbox
            var group_id = $(this).val();
            __group_selected.push(group_id);
            
            $(this).prop('checked', true); //check
        });
        __group_selected=$.unique(__group_selected);
    } else {
        group_list.each(function() { //loop through each checkbox
            var group_id = $(this).val();
            removeArrayIndex(__group_selected, group_id);
            $(this).prop('checked', false); //uncheck
        });
        
        
    }
    groupCheckAllReflect();
    group_count();
}

function group_count(){

    if (__group_selected.length > 0) {
        $("#count_reflect").html(__group_selected.length);
    } else {
        $("#count_reflect").html('0');
    }
    groupCheckAllReflect();
}

function showAndGetstudent() {
    __inst_selected = new Array();
    __group_selected = new Array();
    $(".institution-select").hide();
}

function model_init() {
    cleanPopUpMessage();
    $('#an_title').val('');
    $('#an_description').redactor('set', '');
    $('.add-continue').attr('data-step', '1');
    $('.add-continue').attr('data-action', 'add');
    $('.add-continue').attr('data-canid', '');
    $('.ann_add_step1').show();
    $('.ann_add_step2').hide();
    $('.add-continue').text('CONTINUE');
    $('#an_title_char_left').text('50 Characters left');
    $("#an_error").hide();
    $("#an_error").empty();
    $("input[name=ann_to][value=1]").attr('checked', 'checked');
    $("input[name=ann_to][value=1]").prop("checked", true);
    $("#batch-input .group-course").each(function() {
        $(this).prop("checked", false);
        $(this).attr('checked', false);
    });
    $("#institution-input .inst-course").each(function() {
        $(this).prop("checked", false);
        $(this).attr('checked', false);
    });
    showAndGetstudent();
}
$('#addannouncement').on('hidden.bs.modal', function() {
    model_init();
});
$(document).ready(function(e) {
    $('.redactor').redactor({
        minHeight: 250,
        maxHeight: 250,
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function(json, xhr) {
                var erorFileMsg = "This file type is not allowed. upload a valid image.";
                $('#assesment_form').prepend(renderPopUpMessage('error', erorFileMsg));
                scrollToTopOfPage();
                return false;
            }
        }
    });
});

function setDeleteId(id,title) {

    
    var messageObject = {
        'body': 'Are you sure you want to delete this Announcement ?',
        'button_yes': 'OK',
        'button_no': 'CANCEL',
        'continue_params': {
            'url': admin_url + 'course/ann_delete',
            'user_id': id,
            'announcement_title':title
        },
    };
    callback_warning_modal(messageObject, deleteAnn);
}

function deleteAnn(params) {
    var url = params.data.url;
    var id = params.data.user_id;
    var announcement_title = params.data.announcement_title;
    $.ajax({
        type: "POST",
        url: url,
        data: {
            an_id: id,
            'an_title':announcement_title
        },
        success: function() {
            location.href = location.href;
        }
    });
}

function ann_edit(an_id) {

    model_init();
    $('.add-continue').attr('data-action', 'edit');
    $('.add-continue').attr('data-canid', an_id);
    var title = $('#an_id' + an_id).attr('data-title');
    var desc = $('#an_' + an_id + '_des').html();
    var sendTo = $("[data-id=" + an_id + "]").attr('data-anto');
    var batch = $('#an_id' + an_id).attr('data-batch');
    var ins = $('#an_id' + an_id).attr('data-ins');

    $('.redactor-placeholder p').redactor('set', desc);
    $('#an_title').val(title);
    $('.redactor-in').html(desc);
    $("input[name=ann_to][value=" + sendTo + "]").prop("checked", true);
    $("input[name=ann_to][value=" + sendTo + "]").attr('checked', 'checked');
    
    
    if (sendTo == 1) {

        showAndGetstudent();
    } else if (sendTo == 2) {
        __editFlag = true;
        batches = batch.split(",");
        load_groups(batches);
        __editFlag = false;


    } else if (sendTo == 3) {

        __editFlag = true;
        inst = ins.split(",");
        load_institutions(inst);
        __editFlag = false;
       
    }
}

function submit_announcement(url) {
    var error = [];
    errorHtml = '';
    $("#an_error").hide();
    $("#an_error").empty();
    if ($('#an_title').val() == '') {
        error.push('Please Enter Announcement Title');
    }
    if ($('#an_description').val() == '') {
        error.push('Please Enter Announcement Description');
    }
    if (error.length == 0) {
        cleanPopUpMessage();
        var step = $('.add-continue').attr('data-step');
        if (step == 1) {
            $("#addannouncement .ann_add_step1").hide();
            $("#addannouncement .ann_add_step2").show();
            $('.add-continue').attr('data-step', 2);
            $('.add-continue').text('SEND');
        } else if (step == 2) {
            
            var an_option_to = $("input[name='ann_to']:checked").val();
            if (an_option_to == 1) {
                an_batch = 0;
                an_instution = 0;
            } else if (an_option_to == 2) {
                an_batch = [];
                $("#batch-input .group-course:checked").each(function() {
                    an_batch.push($(this).val());
                });
                an_instution = 0;
            } else if (an_option_to == 3) {
                an_batch = 0;
                an_instution = [];
                $("#institution-input .inst-course:checked").each(function() {
                    an_instution.push($(this).val());
                });
            }
            if ($('.add-continue').attr('data-action') == 'edit') {
                var c_an_id = $('.add-continue').attr('data-canid');
                var redirectUrl = location.href + "#an_id" + c_an_id;
                var senddata = {
                    an_title: $('#an_title').val(),
                    an_to: an_option_to,
                    an_description: $('#an_description').val(),
                    an_batches: an_batch,
                    an_instutions: an_instution,
                    an_id: c_an_id
                };
            } else {
                var redirectUrl = location.href;
                var senddata = {
                    an_title: $('#an_title').val(),
                    an_to: an_option_to,
                    an_description: $('#an_description').val(),
                    an_batches: an_batch,
                    an_instutions: an_instution
                };
            }
            $.ajax({
                type: "POST",
                url: url,
                data: senddata,
                success: function() {
                    location.href = location.href;
                }
            });
        }
    } else {
        errorHtml = '';
        $.each(error, function(i, val) {
            errorHtml += '* ' + val + '<br>';
        });
  
        $('#addannouncement .modal-body').prepend(renderPopUpMessage('error', errorHtml));  
    }
    return false;
}

function loadAnouncementsAdmin() {
    $('#loadmorebutton').css('display', 'none');
    var flag = __announcementOffset;

    $.ajax({
        url: admin_url + '/course/load_announcement/',
        type: "POST",
        data: {
            "is_ajax": '1',
            'limit': __announcementLimit,
            'offset': __announcementOffset,
            'course_id': __course_id,
            "count": __announcementCount
        },
        success: function(response) {
            var data = $.parseJSON(response);

            __announcementCount = data.total_records;
            __defaultpath = data.default_user_path;
            __userpath = data.user_path;
            if (data['success'] == true) {

                __announcementOffset = data['start'];
                var groupsHtml = '';
                if (Object.keys(data['announcement']).length > 0) {

                    $.each(data['announcement'], function(announcementsid, announcements) {
                        groupsHtml += renderhtml(announcements);
                    });

                    var load_button = '<div class="rTableCell text-center">' +
                        '<button id="loadmorebutton"  class="btn btn-green selected margin-12 " onclick="loadAnouncementsAdmin()">Load More' +
                        '<ripples></ripples>' +
                        '</button>' +
                        '</div>';
                    if (flag == '0') {
                        $('#announcementblock').html(groupsHtml);
                        $('#announcement').append(load_button);
                    } else {
                        $('#announcementblock').append(groupsHtml);

                    }
                    if (data['show_load_button'] == true) {
                        $('#loadmorebutton').show();
                    } else {
                        $('#loadmorebutton').hide();
                    }
                    $('#announcement').fadeIn('slow');
                }else{
                   var errorMsg = '<div id="popUpMessage" class="alert alert-danger">No Announcement found.</div>';
                    $('#announcementblock').html(errorMsg);
                }
            }
        }
    });

}

function renderhtml(announcements) {

    user_img = (announcements['us_image'] == 'default.jpg') ? __defaultpath : __userpath;
    user_img = user_img + announcements['us_image'];
    access = '';
    showhide = (__userPrivilege.indexOf(__privilege['edit']) == -1 && __userPrivilege.indexOf(__privilege['delete']) == -1) ? 'hidden' : '';
    if (__userPrivilege.indexOf(__privilege['edit']) != -1) {

        access += `<li>
                            <a href="javascript:void(0)" id="invite-group" data-toggle="modal" data-target="#addannouncement" onclick="ann_edit('` + announcements['id'] + `')">
                                Edit
                            </a>
                        </li>`;
    }
    if (__userPrivilege.indexOf(__privilege['delete']) != -1) {
        access += `<li>
                            <a href="javascript:void(0)" onclick="setDeleteId('` + announcements['id'] + `','`+ announcements['an_title'] +`')">
                                    Delete
                            </a>
                        </li>`;
    }
    return `
            <div class="panel-group anouncement-pannel" id="an_id` + announcements['id'] + `" data-id="` +
        announcements['id'] + `" data-title="` + announcements['an_title'] + `" data-anto="` +
        announcements['an_sent_to'] + `" data-batch="` + announcements['an_batch_ids'] + `" data-ins="` +
        announcements['an_institution_ids'] +
        `">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="anouncement-holder">
                            <div class="width-95" style="display:inline-block">
                                <div class="media">
                                    <div class="media-left">
                                        <span class="icon-wrap-round img">
                                            <img src="` +
        user_img +
        `">
                                        </span>
                                    </div>
                                    <div class="media-body">
                                        <span class="media-heading announcement-name">
                                            ` + announcements['us_name'] + `
                                        </span>
                                        <p>posted an announcement - ` + dateFormat(announcements['an_created_date']) + `
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="pull-right ` + showhide + `">
                                <div class="btn-group lecture-control">
                                    <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">
                                        <span class="label-text">
                                            <i class="icon icon-down-arrow"></i>
                                        </span>
                                        <span class="tilder"></span>
                                    </span>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                       ` + access + ` 
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="anouncement-content">
                            <div class="anouncement-title">
                                ` + announcements['an_title'] + `
                            </div>
                            <div id="an_` + announcements['id'] +
        `_des" class="redactor-editor">
                                ` + announcements['an_description'] + `
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
}

function dateFormat(data) {
    var mydate = new Date(data);
    var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
    ][mydate.getMonth()];
    str = mydate.getFullYear() + ' ' + month + ' ' + mydate.getDate();

    return str;
}

function validateMaxLength(selector) {
    
    var maxlength = $('#' + selector).attr('maxlength');
    var current_length = $('#' + selector).val().length;
    var remaining = parseInt(maxlength - current_length);
    var left_character = (remaining == 1) ? lang('character_left') : lang('characters_left');
    $('#' + selector + '_char_left').html(remaining + ' ' + left_character);
}

function load_institutions(inst) {

    $.ajax({
        url: admin_url + 'course/all_institutions/',
        type: "POST",
        data: {
            "is_ajax": true
        },
        success: function(response) {
            if (response) {
                var data = $.parseJSON(response);
                
                if (data.success == true) {
                    __fullInstituteCount = data.institution.length;
                    $(".institution-select").show();
                    option = {
                        'choice': '1'
                    };
                    renderInstitutionHtml(data, option);
                    if (__editFlag = true) {

                        $(".inst-course").each(function() {

                            if ($.inArray($(this).val(), inst) != -1) {
                                __inst_selected.push($(this).val());
                                $(this).attr('checked', 'checked');
                                $(this).prop("checked", true);
                            }
                        });
                        if(__fullInstituteCount==__inst_selected.length){
                            $('#instAll').prop('checked', true);
                        }else{
                            $('#instAll').prop('checked', false);
                        }
                        if (__inst_selected.length > 0) {
                            $("#count_reflect").html(__inst_selected.length);
                        } else {
                            $("#count_reflect").html('0');
                        }
                    }
                } else {
                    option = {
                        'choice': '3',
                        'error': 'Institution'
                    };
                    $(".institution-select").show();
                    renderInstitutionHtml(data, option);
                }
            }
            
        }
    });
    processInstituteCheckbox();
}

$(document).on('hide.bs.modal', '#addannouncement', function() {

    $('.redactor-in').html('');

});

function load_groups(batches) {

    $.ajax({
        url: admin_url + 'course/all_groups/',
        type: "POST",
        data: {
            "is_ajax": true,
            "course_id": __courseid
        },
        success: function(response) {

            var data = $.parseJSON(response);

            if (data.success == true) {

                __fullGroupCount = data.groups.course_groups.length;

                $(".institution-select").show();
                option = {
                    'choice': '2'
                };
                renderInstitutionHtml(data, option);

                if (__editFlag = true) {
                    $(".group-course").each(function() {

                        if ($.inArray($(this).val(), batches) != -1) {
                            __group_selected.push($(this).val());
                            $(this).attr('checked', 'checked');
                            $(this).prop("checked", true);
                        }
                    });
                
                    if(__fullGroupCount==__group_selected.length){
                        $('#groupAll').prop('checked', true);
                    }else{
                        $('#groupAll').prop('checked', false);
                    }
                    if (__group_selected.length > 0) {
                        $("#count_reflect").html(__group_selected.length);
                    } else {
                        $("#count_reflect").html('0');
                    }
                }
            } else {
                $(".institution-select").show();
                option = {
                    'choice': '3',
                    'error': 'Batches'
                };
                renderInstitutionHtml(data, option);
            }
        }
        
    });
}

function renderInstitutionHtml(data, option) {

    if (option.choice == '1') {

        var instituteHtml = '<div class="row">';
        instituteHtml += '<div class="rTable content-nav-tbl normal-tbl" style="background:#fff;">';
        instituteHtml += '<div class="rTableRow">';
        instituteHtml += '<div class="rTableCell">';
        instituteHtml += '<a href="javascript:void(0)" class="select-all-style">';
        instituteHtml += '<label>';
        instituteHtml += '<input onclick="selectAll(\'instAll\', \'inst-course\')" value="1" class="select-users-new-group-parent" id="instAll" type="checkbox"> Select All (<span id="count_reflect">0</span>/' + data.institution.length + ')</label>';
        instituteHtml += '</a>';
        instituteHtml += '</div>';
        // instituteHtml += '<div class="rTableCell dropdown">';
        instituteHtml += '<div class="rTableCell" style="width: 250px !important;">';
        instituteHtml += '<div class="input-group">';
        instituteHtml += '<input type="text" class="form-control srch_txt" id="course_keyword" placeholder="Search by name">';
        instituteHtml += '<span id="searchclear" style="display: block;">×</span>';
        instituteHtml += '<a class="input-group-addon" id="basic-addon2">';
        instituteHtml += '<i class="icon icon-search"> </i>';
        instituteHtml += '</a>';
        instituteHtml += '</div>';
        // instituteHtml += '</div>';

        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '<div class="add-selectn alignment-order">';
        instituteHtml += '<div class="inside-box-padding" id="institution-input">';
        for (var i = 0; i < data.institution.length; i++) {

            instituteHtml += '<div class="checkbox-wrap institute-list" id="inst_course_' + data['institution'][i].id + '">';
            instituteHtml += '<span class="chk-box">';
            instituteHtml += '<label class="font14">';
            instituteHtml += '<input type="checkbox" class="inst-course" value="' + data['institution'][i].id + '"><span class="inst-name">' + data['institution'][i].ib_institute_code + ' -' + data['institution'][i].ib_name + '</span>';
            instituteHtml += '</label>';
            instituteHtml += '</span>';
            instituteHtml += '<span class="email-label pull-right">';
            instituteHtml += '</span>';
            instituteHtml += '</div>';
        }
        instituteHtml += '<p id="errortext" style="display:none;font-size: 18px;color: #929292;text-align: center;padding: 30px;">No Institute matching the keyword</p>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
    } else if (option.choice == '2') {

        var courseInstitute = data.groups.institution;
        
        var courseGroups = data.groups.course_groups;
        var instituteHtml = '<div class="row">';
        instituteHtml += '<div class="rTable content-nav-tbl normal-tbl" style="">';
        instituteHtml += '<div class="rTableRow">';
        instituteHtml += '<div class="rTableCell">';
        instituteHtml += '<a href="javascript:void(0)" class="select-all-style"  style="padding: 0 15px;">';
        instituteHtml += '<label onclick="selectAllGroup(\'groupAll\', \'group-course\')">';
        instituteHtml += ' <input value="1" class="select-users-new-group-parent" id="groupAll" type="checkbox"> Select All (<span id="count_reflect">0</span>/' + courseGroups.length + ')</label>';
        instituteHtml += '</a>';
        instituteHtml += '</div>';
        instituteHtml += '<div class="rTableCell dropdown" style="width:70%;">';
        // instituteHtml += '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">All';
        // instituteHtml += '<span class="caret"></span>';
        // instituteHtml += '</a>';
       
        instituteHtml += '<select id="announcement_batch_select" style="border:none;padding: 11px 10px;width:100%;" onchange="filter_ins_by(this.value)">';

        instituteHtml += '<option value="all">';
        instituteHtml += '<a href="javascript:void(0)"  id="gfilter_all">All Institutes</a>';
        instituteHtml += '</option>';
        for (var i = 0; i < courseInstitute.length; i++) {
            instituteHtml += '<option value="'+ courseInstitute[i].id + '">';
            instituteHtml += '<a href="javascript:void(0)" data-toggle="tooltip" title="'+ courseInstitute[i].ib_institute_code + ' -' + courseInstitute[i].ib_name+'" id="gfilter_' + courseInstitute[i].id + '">' + courseInstitute[i].ib_institute_code + ' -' + courseInstitute[i].ib_name;
            instituteHtml += '</a>';
            instituteHtml += '</option>';
        }
        instituteHtml += '</select>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '<div class="add-selectn alignment-order">';
        instituteHtml += '<div class="inside-box-padding" id="batch-input">';
        for (var j = 0; j < courseGroups.length; j++) {
            instituteHtml += '<div class="checkbox-wrap group-filter" id="group_course_' + courseGroups[j].id + '" data-gpfilter="' + courseGroups[j].gp_institute_id + '">';
            instituteHtml += '<span class="chk-box">';
            instituteHtml += '<label class="font14">';
            instituteHtml += '<input type="checkbox" class="group-course" value="' + courseGroups[j].id + '">' + courseGroups[j].gp_name;
            instituteHtml += '</label>';
            instituteHtml += '</span>';
            instituteHtml += '<span class="email-label pull-right">';
            instituteHtml += '</span>';
            instituteHtml += '</div>';
        }
        instituteHtml += '</div>';
        instituteHtml += '</div>';
        instituteHtml += '</div>';
    } else {

        var instituteHtml = '<div class="row">';
        instituteHtml += '<p class="blank-announcement-alert">No ' + option.error + ' to Display</p>';
        instituteHtml += '</div>';
    }


    $('#render_data').html(instituteHtml);
    $('#render_data').fadeIn('slow');
    instituteHtml = '';
}
function filter_ins_by(filter) {
    if (filter == 'all') {
        $('.group-filter').show();
        groupCheckAllReflect();
       
    } else {
        filter=parseInt(filter);
        $('.group-filter').hide();
        $("[data-gpfilter=" + filter + "]").show();
        processCheckboxChecked();
    }
    // $('#filter_dropdown_text').html($('#gfilter_' + filter).text() + '<span class="caret"></span>');
    var instituteName='';
    $('#announcement_batch_select option').each(function(){
        if($(this).val()==filter){ 
            instituteName = $(this).text();    
        }
    });
    
    $('#announcement_batch_select').attr("title",instituteName);
}

function processCheckboxChecked() {
    $('#groupAll').prop('checked', !($('[data-gpfilter="'+($('#announcement_batch_select').val())+'"] .group-course').not(':checked').length));
    
}
function groupCheckAllReflect(){
    var checkedCount    = $('.group-course:visible:checked').length;
    var total           = $('.group-course:visible').length
    if(total==checkedCount){
        $('#groupAll').prop('checked', true);
    }else if(total>checkedCount){
        $('#groupAll').prop('checked', false);
    }
}
function processInstituteCheckbox(){

    var checkedCount    = $('.inst-course:visible:checked').length;
    var total           = $('.institute-list:visible').length
    if(total!=0){
        if(total==checkedCount){
            $('#instAll').prop('checked', true);
        }else{
            $('#instAll').prop('checked', false);
        }
    }
    
}