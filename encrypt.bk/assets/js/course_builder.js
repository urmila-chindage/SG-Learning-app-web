var __live_lecture_start_date = '';
var cur_date = new Date();
var cur_hours= cur_date.getHours()+1;
var cur_min  = cur_date.getMinutes();
var cur_sec  = cur_date.getSeconds();
var timepicker_obj;
$(function(){
    var today = new Date();
    $("#descriptive_submission_date").datepicker({
        language: 'en',
        minDate: today,
        dateFormat: 'dd-mm-yy',
        autoClose: true
    });
});


$('#total_mark,#descriptive_words_limit').bind("cut copy paste",function(e) {
    e.preventDefault();
   });
$(document).ready(function(){
    $('#desc_percentage_bar, #scorm_percentage_bar, #percentage_bar, #create_new_section_cancel, #create_new_section_cancel_assesment, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #create_new_section_cancel_youtube, #create_new_section_cancel_import_content, #section_name, #section_name_assesment, #section_name_survey, #section_name_live_lecture, #section_name_html, #section_name_youtube, #section_name_import_content, #section_name_descriptive, #section_name_certificate, #create_new_desciptive_test_cancel', '#create_new_section_scorm_cancel', '#section_name_scorm').hide();
    
    $('#live_lecture_start_time').timepicker({ 
        timeFormat: 'h:i A',
    });
    /*$('#live_lecture_start_time').on('changeTime', function() {
        $('#live_lecture_start_time').addClass('white');
        setTimeout(function(){
            __live_lecture_start_date = $('#live_lecture_start_time').val().trim();
            var start_time = __live_lecture_start_date.split(" ");;
            $('#live_lecture_start_time').val(start_time[0]);
            $('#live_lecture_start_time_noon').html(start_time[1]);
            $('#live_lecture_start_time').removeClass('white');                      
        },500);
    });
    $('#live_lecture_start_time').trigger('changeTime');*/
});

$(document).on('selectTime', '#live_lecture_start_time', function() {
    $('#live_lecture_start_time').addClass('white');
    setTimeout(function(){
        __live_lecture_start_date = $('#live_lecture_start_time').val().trim();
        var start_time = __live_lecture_start_date.split(" ");console.log(start_time);
        $('#live_lecture_start_time').val(start_time[0]);
        $('#live_lecture_start_time_noon').text(start_time[1]);    
        $('#live_lecture_start_time').removeClass('white');                  
    },100);
});

function changeCourseStatus(course_id, status)
{
    header_text ='Are you sure to Activate this Course ?';
    var btn_txt = 'ACTIVATE';
    if(status==0){
        header_text="Are you sure to Deactivate this Course ?"
        btn_txt = 'DEACTIVATE';
    }
    var messageObject = {
        'body': header_text,
        'button_yes': btn_txt,
        'button_no': 'CANCEL',
        'continue_params': {
            "course_id": course_id
        },
    };
    callback_warning_modal(messageObject, changeStatusConfirmed);
}

function changeStatusConfirmed(params){
console.log(params);
    var course_id=params.data.course_id;
    $.ajax({
        url: admin_url+'coursebuilder/change_status',
        type: "POST",
        data:{
            "course_id":course_id,
             "is_ajax":true
            },
        success: function(response) {

            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                if(data.status=='1'){
                    statusClass='bg-green';
                    statusBadge='Active';
                    cb_label_right='DEACTIVATE';
                    cb_class_right='orange'
                    
                  }else{
                    statusClass='bg-yellow';
                    statusBadge='Inactive';

                    cb_label_right='ACTIVATE';
                    cb_class_right='green';
                   
                }
                var vstatus = (data.status=='0')?'1':'0';
                $('#status_right_button_'+course_id).attr("onclick","changeCourseStatus('"+course_id+"', '"+vstatus+"')");
                $('#status_right_button_'+course_id).html(cb_label_right.toUpperCase() +'<ripples></ripples>').removeClass('btn-green').removeClass('btn-orange').addClass('btn-'+cb_class_right);
                $('#status_btn_'+course_id+' a').attr("onclick","changeCourseStatus('"+course_id+"', '"+vstatus+"')");
                $('#status_btn_'+course_id+' a').html(cb_label_right);
                $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(statusBadge).addClass(statusClass);
                
                if(data.cb_status == '0'){
                    changeCourseStatusLabel(data.cb_status);
                }
                var messageObject = {
                    'body': 'Course status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function restoreCourse(course_id, header_text)
{
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content').html('');
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({"course_id": course_id}, restoreCourseConfirmed);        
}

function restoreCourseConfirmed(params){
    $.ajax({
        url: admin_url+'coursebuilder/restore',
        type: "POST",
        data:{"course_id":params.data.course_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#action_status_'+params.data.course_id).html('<a class="btn btn-green" onclick="changeCourseStatus(\''+params.data.course_id+'\', \''+lang('publish')+' '+$('#action_status_'+params.data.course_id).attr('data-coursename')+' '+  lang('course')+'\')" href="javascript:void(0);">'+lang('activate').toUpperCase()+'<ripples></ripples></a>');
                $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(data['actions']['action']).addClass('bg-yellow');
                $('#course_action_'+params.data.course_id).html(data['action_list']);
                $('#action_class_'+params.data.course_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_'+params.data.course_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_'+params.data.course_id).html(data['actions']['button_text']);
                $('#activate').modal('hide');
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

var __uploading_file = '';
$('#drop-to-pop').on('drop', function (e) 
{
    e.preventDefault();
    $('#percentage_bar').hide();
    __uploading_file = e.originalEvent.dataTransfer.files;
    if( __uploading_file.length > 1 )
    {
        lauch_common_message('Multiple file not allowed', 'You are not allowed to upload more than one file.');    
        return false;
    }
    $('#attached_file_name').html('<label>'+lang('attached')+' : '+__uploading_file[0]['name']+'</label>')   
    //saveLecture();
    $('#section_id').html(getSectionsOptionHtml());
    $("#upload-lecture").modal();
    $('#save_lecture').unbind();
    $('#save_lecture').click({}, saveLecture);    
});

function getSectionsOptionHtml()
{
    var sectionOption   = '<option value="">'+lang('choose_sections')+'</option>';
    $.ajax({
        url: admin_url+'coursebuilder/section_json',
        type: "POST",
        async:false,
        data:{ "is_ajax":true, "course_id":__course_id},
        success: function(response) {
            var data            = $.parseJSON(response);
            if(data['sections'].length > 0 )
            {
                for (var i=0; i<data['sections'].length ; i++)
                {
                    sectionOption += '<option value="'+data['sections'][i]['id']+'">'+data['sections'][i]['s_name']+'</option>';
                }
            }
        }
    });
    return sectionOption;
}

function getCoursesOptionHtml()
{
    var courseOption   = `<option value="">${lang('choose_courses')}</option>`; 
    $.ajax({
        url: admin_url+'course/course_json',
        type: "POST",
        async:false,
        data:{ "is_ajax":true, 'not_deleted':'1'},
        success: function(response) {
            var data            = $.parseJSON(response);
            if(data['courses'].length > 0 )
            {
                for (var i=0; i<data['courses'].length ; i++)
                {
                    if(data['courses'][i]['id']==__course_id)
                    {
                        continue;
                    }
                    courseOption += `<option value="${data['courses'][i]['id']}">${data['courses'][i]['cb_title']} ${(data['courses'][i]['cb_status'] == '0')?' (Inactive)':' (Active)'} </option>`;
                }
            }
        }
    });
    return courseOption;
}
$(document).on('click', '#upload-lecture .close, #upload-lecture .btn-red', function(){
    if($(this).parent().attr('id') != 'popUpMessage' ) {
        $("#lecture_file_upload_manual").val('');
    }
});
$(document).on('change', '#lecture_file_upload_manual', function(e){
    console.log(e.currentTarget.files[0]);
    __uploading_file = e.currentTarget.files;
    if( __uploading_file.length > 1 )
    {
        lauch_common_message('Multiple file not allowed', 'You are not allowed to upload more than one file.');    
        return false;
    }
    var fileObj                     = new processFileName(__uploading_file[0]['name']);
        fileObj.uniqueFileName();        
    if(inArray(fileObj.fileExtension(), __allowed_files) == false)
    {
        $("#lecture_file_upload_manual").val('');
        lauch_common_message('Invalid File', 'This type of file is not allowed.');    
        return false;        
    }
    $('#percentage_bar').hide();
    $('#attached_file_name').html('<label>'+lang('attached')+' : '+__uploading_file[0]['name']+'</label>')   
    $('#section_id').html(getSectionsOptionHtml());
    $("#upload-lecture").modal();
    $('#save_lecture').unbind();
    $('#save_lecture').click({}, saveLecture);    
});

var __create_section_as_new = false;
var __create_description_as_new = false;

// $(document).on('click', '#create_new_section, #create_new_section_assesment, #create_new_section_live_lecture, #create_new_section_html, #create_new_section_youtube, #create_new_section_import_content', function(){
//     __create_section_as_new = true;
//     $('#create_new_section, #section_id, #create_new_section_assesment, #create_new_section_live_lecture, #create_new_section_html, #section_id_assesment, #section_id_live_lecture, #section_id_html, #create_new_section_youtube, #section_id_youtube, #create_new_section_import_content, #section_id_import_content').hide();
//     $('#create_new_section_cancel, #section_name, #create_new_section_cancel_assesment, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #section_name_assesment, #section_name_live_lecture, #section_name_html, #create_new_section_cancel_youtube, #section_name_youtube, #create_new_section_cancel_import_content, #section_name_import_content').show();
//     $('#section_id, #section_id_assesment, #section_id_live_lecture, #section_id_html, #section_id_youtube, #section_id_import_content').val("");
    
// });

// $(document).on('click', '#create_new_section_cancel, #create_new_section_cancel_assesment, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #create_new_section_cancel_youtube, #create_new_section_cancel_import_content', function(){
//     __create_section_as_new = false;
//     $('#create_new_section_cancel, #section_name, #create_new_section_cancel_assesment, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #section_name_assesment, #section_name_live_lecture, #section_name_html, #create_new_section_cancel_youtube, #section_name_youtube, #create_new_section_cancel_import_content, #section_name_import_content').hide();
//     $('#create_new_section, #section_id, #create_new_section_assesment, #create_new_section_live_lecture, #create_new_section_html, #section_id_assesment, #section_id_live_lecture, #section_id_html, #create_new_section_youtube, #section_id_youtube, #create_new_section_import_content, #section_id_import_content').show();
//     $('#section_name, #section_name_assesment, #section_name_live_lecture, #section_name_html, #section_name_youtube, #section_name_import_content').val("");
// });

$(document).on('click', '#create_new_section, #create_new_section_certificate, #create_new_section_assesment, #create_new_section_survey, #create_new_desciptive_test, #create_new_section_scorm, #create_new_section_live_lecture, #create_new_section_html, #create_new_section_youtube, #create_new_section_import_content', function(){
    __create_section_as_new = true;
    __create_description_as_new = true;
    $('#create_new_section, #section_id, #create_new_section_assesment, #create_new_section_certificate, #section_id_certificate, #create_new_section_survey, #create_new_desciptive_test, #create_new_section_scorm, #create_new_section_live_lecture, #create_new_section_html, #section_id_assesment, #section_id_survey, #section_id_live_lecture, #section_id_html, #create_new_section_youtube, #section_id_youtube, #create_new_section_import_content, #section_id_import_content, #section_id_descriptive, #section_id_scorm').hide();
    $('#create_new_section_cancel, #section_name, #create_new_section_cancel_assesment, #create_new_section_cancel_certificate, #create_new_section_cancel_survey, #create_new_desciptive_test_cancel, #create_new_section_scorm_cancel, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #section_name_assesment, #section_name_certificate, #section_name_survey, #section_name_live_lecture, #section_name_html, #create_new_section_cancel_youtube, #section_name_youtube, #create_new_section_cancel_import_content, #section_name_import_content, #section_name_descriptive, #section_name_scorm').show();
    $('#section_id, #section_id_assesment, #section_id_survey, #section_id_certificate, #section_id_live_lecture, #section_id_html, #section_id_youtube, #section_id_import_content, #section_id_descriptive, #section_id_scorm').val("");
    
});

$(document).on('click', '#create_new_section_cancel, #create_new_section_cancel_certificate, #create_new_desciptive_test_cancel, #create_new_section_scorm_cancel,#create_new_section_cancel_assesment, #create_new_section_cancel_survey, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #create_new_section_cancel_youtube, #create_new_section_cancel_import_content', function(){
    __create_section_as_new = false;
    __create_description_as_new = false;
    $('#create_new_section_cancel, #section_name, #create_new_section_cancel_certificate, #create_new_desciptive_test_cancel, #create_new_section_scorm_cancel, #create_new_section_cancel_assesment, #create_new_section_cancel_survey, #create_new_section_cancel_live_lecture, #create_new_section_cancel_html, #section_name_assesment, #section_name_survey, #section_name_certificate, #section_name_live_lecture, #section_name_html, #create_new_section_cancel_youtube, #section_name_youtube, #create_new_section_cancel_import_content, #section_name_import_content, #section_name_descriptive, #section_name_scorm').hide();
    $('#create_new_section, #section_id, #create_new_desciptive_test, #create_new_section_scorm, #create_new_section_assesment, #create_new_section_certificate, #create_new_section_survey, #create_new_section_live_lecture, #create_new_section_html, #section_id_assesment, #section_id_certificate,  #section_id_survey, #section_id_live_lecture, #section_id_html, #create_new_section_youtube, #section_id_youtube, #create_new_section_import_content, #section_id_import_content, #section_id_descriptive, #section_id_scorm').show();
    $('#section_name, #section_name_assesment, #section_name_survey, #section_name_certificate, #section_name_live_lecture, #section_name_html, #section_name_youtube, #section_name_import_content, #section_name_descriptive').val("");
});

function addSection()
{
    cleanPopUpMessage();
    $('#section_logo').attr('src',_default_course_url+'default-section.jpg');
    $('#site_logo_btn').val('');
    $('#section_name_create').val('');
    $('#add_section_save_ok').html(lang('create').toUpperCase());
    $('#add_section_save_ok').unbind();
    $('#add_section_save_ok').click({"section_name":"addsection","section_id": 0, 'section_input_id':'section_name_create'}, saveSectionConfirmed);    
}

function renameSection(section_id)
{ 
    $.ajax({
        url : getSectionDetails,
        type : 'post',
        data : {'id' : section_id },
        success : function(data){
            //console.log(data);
            //     }
            // });
            data            = JSON.parse(data);
            var imageName   = data['data']['s_image'];
            //console.log(imageName+"image");
            sectionImage    = imageName.split("?");
            if(typeof sectionImage[0] === 'undefined'){
                sectionImage[0] = '';
            }
            var image       = new Image();
            var version     = Math.floor((Math.random() * 100) + 1);
            var url_image   = section_url+sectionImage[0]+'?v='+version;
            //console.log("image_url"+url_image);
            image.src = url_image;
            $.ajax({
                url : check_file,
                type : 'post',
                data : {'section_id' : section_id, 'file_url' : url_image, 'course_id' : __course_id},
                success : function(data)
                {
                    //console.log(data);
                    data = JSON.parse(data);
                    if(data['s3'] == true){
                        $('#section_logo_Edit').attr('src',data['s3_section_url']);
                        $('#section_logo_Edit').attr('image_name','');
                        console.log('s3 found');
                    }
                    else{
                        if(data['file_exist']){
                            $('#section_logo_Edit').attr('src',url_image);
                            $('#section_logo_Edit').attr('image_name','');
                            console.log('image found');
                        }
                        else{
                            $('#section_logo_Edit').attr('image_name','default-section.jpg');
                            $('#section_logo_Edit').attr('src',_default_course_url+'default-section.jpg');
                            console.log('image not found');
                        }
                    }
                }
            })
            cleanPopUpMessage();
            $('#section_logo_btn_edit').attr('section-id',section_id);
            var new_section_name = $('#section_wrapper_'+section_id).attr('data-section-name');
            new_section_name     = new_section_name.replace(/["<>{}]/g, '');
            new_section_name     = new_section_name.trim();
            is_new_sec           = false;
            $('#section_name_rename').val(new_section_name);
            $('#section_save_ok').html(lang('update').toUpperCase());
            $('#section_save_ok').unbind();
            $('#section_save_ok').click({"section_name":"rename_section","section_id": section_id, 'section_input_id':'section_name_rename'}, saveSectionConfirmed);    
        }
    });
}
function isUrlExists(url, cb){
    jQuery.ajax({
        url:      url,
        dataType: 'text',
        type:     'GET',
        complete:  function(xhr){
            if(typeof cb === 'function')
               cb.apply(this, [xhr.status]);
        }
    });
}
function saveSectionConfirmed(params)
{   
    var formData = new FormData();
    var errorCount              = 0;
    var errorMessage            = '';
    if(params.data.section_id == 0)
    {
        if( (cb_has_lecture_image == 1)  && ($('#site_logo_btn')[0].files[0] == undefined)  )
        {
            errorCount++;
            errorMessage       += 'Please upload Section image.<br>';
        }
        formData.append('section_image', $('#site_logo_btn')[0].files[0]);
        var section_modal       = params.data.section_name;
    }
    else{
        var lecture_image       = $('#lecture_image_edit').attr('image_name');
        if( (cb_has_lecture_image == 1) && lecture_image == 'default-lecture.jpg' && ($('#section_logo_btn_edit')[0].files[0] == undefined)  )
        {
            errorCount++;
            errorMessage       += 'Please upload Section image.<br>';
        }
        var section_modal   = 'edit_section';
        formData.append('section_image', $('#section_logo_btn_edit')[0].files[0]);
    }
    var section_name            = $('#'+params.data.section_input_id).val().trim();
    section_name                = section_name.replace(/["<>{}]/g, '');
    section_name                = section_name.trim();
    formData.append('course_id', __course_id);
    formData.append('course_name', __course_name);
    formData.append('section_name', section_name);
    formData.append('section_id', params.data.section_id);
    formData.append('is_ajax', true);
    formData.append('structure', $("#sortable").sortable('serialize'));
    $('#section_logo_btn_edit').val('');
    if( section_name == ''||section_name == 'undefined'||section_name == null)
    {
        errorCount++;
        errorMessage            = 'section title required <br />';
    }
    if( errorCount > 0 )
    { 
        $('#'+section_modal+' .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        return false;
    }else{
        $.ajax({
            url: admin_url+'coursebuilder/save_section',
            type: "POST", 
            data:formData,
            processData:false,
            contentType:false,
            cache:false,
            async:false,
            // data:{formData,"course_id":__course_id, "files": section_form, "course_name":__course_name, "section_name":section_name, "section_id":params.data.section_id, "is_ajax":true, 'structure':$("#sortable").sortable('serialize')},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == 'false' )
                {
                    if( params.data.section_id == 0 )
                    {
                        var sectionHtml = '';
                            sectionHtml += '<li class="section" id="section_wrapper_'+data['id']+'" data-section-name="'+section_name+'">';
                            sectionHtml += '    <div class="section-title-holder d-flex justify-between">';
                            sectionHtml += '      <div class="section-title">';
                            sectionHtml += '        <div class="drager">';
                            sectionHtml += '            <img src="'+assets_url+'images/drager.png">';
                            sectionHtml += '        </div>';
                            sectionHtml += '        <div class="section-counter"></div>';
                            sectionHtml += '        <span class="section-name" id="section_name_'+data['id']+'"> '+section_name+'</span>';
                            sectionHtml += '      </div>';
                            sectionHtml += '      <div class="lecture-action-holder d-flex align-center">';
                            var status_class = 'Inactive';
                            var status_label = 'inactive';
                            if( data['s_status'] == 1 )
                            {
                                status_class = 'active';
                                status_label = 'active';
                            }
                            sectionHtml += '        <div id="section_status_wraper_'+data['id']+'" class="'+status_class+'-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="section_status_text_'+data['id']+'">'+lang(status_label)+'</span></div>';
                            //--------
                            sectionHtml += '        <div class="btn-group section-control sectiontitle-dropalign">';
                            sectionHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                            sectionHtml += '                <span class="label-text">';
                            sectionHtml += '                    <i class="icon icon-down-arrow"></i>';
                            sectionHtml += '                </span>';
                            sectionHtml += '            <span class="tilder"></span>';
                            sectionHtml += '            </span>';
                            sectionHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                            sectionHtml += '                <li>';
                            sectionHtml += '                    <a href="javascript:void(0)" data-toggle="modal" data-target="#edit_section" onclick="renameSection(\''+data['id']+'\')">'+lang('edit')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '                <li>';
                            sectionHtml += '                    <a href="javascript:void(0)" onclick="deleteSection(\''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('delete')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '                <li id="section_action_status_'+data['id']+'">';
                            sectionHtml += '                    <a href="javascript:void(0)"  onclick="changeSectionStatus(\'0\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('activate_all')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '                <li id="section_action_status_'+data['id']+'">';
                            sectionHtml += '                    <a href="javascript:void(0)"  onclick="changeSectionStatus(\'1\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('deactivate_all')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '            </ul>';
                            sectionHtml += '        </div>';
                            //--------
                            sectionHtml += '    </div>';
                            sectionHtml += '      </div>';
                            sectionHtml += '    <ul class="lecture-wrapper ui-sortable" id="section_lecture_'+data['id']+'">';
                            
                            sectionHtml += '    </ul>';
                            sectionHtml += '</li>';

                            $('#sortable').append(sectionHtml);
                                __total_sections = parseInt(__total_sections+1);
                                var totallect_text = __total_lecture==1?'Lecture':lang('lectures');
                                var totalsec_text  = __total_sections==1?'Section':lang('sections');
                                $('#section_lecture_count').html(__total_sections+' '+totalsec_text+' - '+__total_lecture+' '+totallect_text );
                                parant_sort();
                            }
                            else
                            {
                                $('#section_name_'+params.data.section_id).html(section_name);
                                $('#section_wrapper_'+params.data.section_id).attr('data-section-name', section_name);
                            }
                            $("#edit_section, #addsection").modal('hide');
                            }
                                else
                                {
                                    $('#'+section_modal+' .modal-body').prepend(renderPopUpMessage('error', data['message']));
                                }
                            }
                                });
                            }
                        }
                        


function addSectionOnDrag(position)
{
    cleanPopUpMessage();
    $('#section_name_create_on_drag_drop').val('');
    $('#save_section_drag_drop, #cancel_section_drag_drop, #addsectiondraganddrop .icon-cancel-1').unbind();
    $('#save_section_drag_drop').click({"section_id": 0, 'position':position}, addSectionOnDragConfirmed);    
    $('#cancel_section_drag_drop, #addsectiondraganddrop .icon-cancel-1').click(function(){
        $('#section_wrapper_0').remove();
    });
    $('#addsectiondraganddrop').modal('show');
}

function addSectionOnDragConfirmed(param)
{
    var section_name     = $('#section_name_create_on_drag_drop').val().trim();
    var position         = parseInt(param.data.position+1);
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( section_name == '')
    {
        errorCount++;
        errorMessage += 'section name required <br />';
    }
    cleanPopUpMessage();
    if( errorCount > 0 )
    {
        $('#addsectiondraganddrop .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }

    $.ajax({
        url: admin_url+'coursebuilder/save_section',
        type: "POST",
        data:{"course_id":__course_id, "section_name":section_name, "section_id":param.data.section_id, "position":position, "is_ajax":true, 'structure':$("#sortable").sortable('serialize')},
        success: function(response) {
            var data  = $.parseJSON(response);

            if(data['error'] == 'false'){

                $('#section_wrapper_0').attr('id', 'section_wrapper_'+data['id']);
                $('#section_wrapper_'+data['id']).attr('data-section-name', section_name);
                $('#section_name_0').attr('id', 'section_name_'+data['id']);
                $('#section_name_'+data['id']).text(section_name);
                $('#section_name_'+data['id']).parent().append('<div class="Inactive-section" id="section_status_wraper_'+data['id']+'"><i class="icon icon-ok-circled"></i><span id="section_status_text_'+data['id']+'" class="ap_cont">'+lang('inactive')+'</span></div>');
                $('#button_group_0').attr('id', 'button_group_'+data['id']);
                $('#section_wrapper_'+data['id']+' .lecture-wrapper').attr('id', 'section_lecture_'+data['id']);

                var buttonHtml  = '';
                    buttonHtml += '                <li>';
                    buttonHtml += '                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rename_section" onclick="renameSection(\''+data['id']+'\')">'+lang('rename')+'</a>';
                    buttonHtml += '                </li>';
                    buttonHtml += '                <li>';
                    buttonHtml += '                    <a href="javascript:void(0)" onclick="deleteSection(\''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('delete')+'</a>';
                    buttonHtml += '                </li>';
                    buttonHtml += '                <li id="section_action_status_'+data['id']+'">';
                    buttonHtml += '                    <a href="javascript:void(0)"  onclick="changeSectionStatus(\'0\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('activate_all')+'</a>';
                    buttonHtml += '                </li>';
                    buttonHtml += '                <li id="section_action_status_'+data['id']+'">';
                    buttonHtml += '                    <a href="javascript:void(0)"  onclick="changeSectionStatus(\'1\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['id']+'\')">'+lang('deactivate_all')+'</a>';
                    buttonHtml += '                </li>';
                $('#button_group_'+data['id']).html(buttonHtml);
                __total_sections = parseInt(__total_sections+1);
                $('#section_lecture_count').html(__total_sections+' '+lang('sections')+' - '+__total_lecture+' '+lang('lessons') )
                $('#addsectiondraganddrop').modal('hide');
                cleanPopUpMessage();
            }else{
                $('#addsectiondraganddrop  .modal-body').prepend(renderPopUpMessage('error', data['message']));
            }
            
        }
    });
}

function changeSectionStatus(current_status, section_name, section_id)
{
    var header_status = ((current_status==1)?'deactivate':'activate');
console.log();
    var messageObject = {
        'body': lang('if_'+header_status+'_section'),
        'body': 'Are you sure you want to '+header_status+' all lectures under this section?',
        'button_yes': header_status.toUpperCase(),
        'button_no': 'CANCEL',
        'continue_params': {
            "section_id": section_id,
            "section_name": decodeURIComponent(escape(atob(section_name))),
            'course_id': __course_id ,
            'is_ajax':'true',
            "status":((current_status==1)?0:1)
        },
    };
    callback_warning_modal(messageObject, changeSectionStatusConfirmed);
}

function changeSectionStatusConfirmed(param)
{

    var section_id  = param.data.section_id;
    var status      = param.data.status;
    var course_id   = param.data.course_id;
    var sectionName = param.data.section_name;

    $.ajax({
        url: admin_url+'coursebuilder/change_section_status',
        type: "POST",
        data:{ "is_ajax":true,'course_name':__course_name,'section_name':sectionName ,'section_id':section_id,'course_id':course_id, 'status':status},
        success: function(response) {
            
            var data  = $.parseJSON(response);
            if(data['error'] == true){
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
                return false;
            }else{
                if(status == 1)
                {
                    var messageObject = {
                        'body': 'Lectures Activated Successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    $('#section_status_text_'+section_id).html(lang('active'));
                    $('#section_status_wraper_'+section_id).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                    //$('#section_action_status_'+param.data.section_id).html('<a onclick="changeSectionStatus(\'1\', \''+param.data.section_name+'\', \''+param.data.section_id+'\')" data-toggle="modal" data-target="#Deactivate" href="javascript:void(0)">'+lang('deactivate')+'</a>');
                    $.each( data['lectures'], function( index, value ){
                        $('#lecture_status_text_'+value['id']).html(lang('active'));
                        $('#lecture_id_'+value['id']).removeClass('active-lecture').removeClass('Inactive-lecture').addClass('active-lecture');
                        $('#lecture_status_wraper_'+value['id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                        $('#lecture_action_status_'+value['id']).html('<a onclick="changeLectureStatus(\'1\', \''+btoa(unescape(encodeURIComponent(value['cl_lecture_name'])))+'\', \''+value['id']+'\', \''+value['cl_sent_mail_on_lecture_creation']+'\')"  href="javascript:void(0)">'+lang('deactivate')+'</a>');
                    });            

                }
                else
                {
                    var messageObject = {
                        'body': 'Lectures Deactivated Successfully',
                        'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                    if(data.cb_status == '0'){
                        changeCourseStatusLabel(data.cb_status);
                    }
                    $('#section_status_text_'+section_id).html(lang('inactive'));
                    $('#section_status_wraper_'+section_id).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                    //$('#section_action_status_'+param.data.section_id).html('<a onclick="changeSectionStatus(\'0\', \''+param.data.section_name+'\', \''+param.data.section_id+'\')" data-toggle="modal" data-target="#Deactivate" href="javascript:void(0)">'+lang('activate')+'</a>');
                    $.each( data['lectures'], function( index, value ){
                        //console.log(data['lectures']['cl_lecture_name']); 
                        $('#lecture_status_text_'+value['id']).html(lang('inactive'));
                        $('#lecture_id_'+value['id']).removeClass('active-lecture').removeClass('Inactive-lecture').addClass('Inactive-lecture');
                        $('#lecture_status_wraper_'+value['id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                        $('#lecture_action_status_'+value['id']).html('<a onclick="changeLectureStatus(\'0\', \''+btoa(unescape(encodeURIComponent(value['cl_lecture_name'])))+'\', \''+value['id']+'\', \''+value['cl_sent_mail_on_lecture_creation']+'\')" href="javascript:void(0)">'+lang('activate')+'</a>');                
                    });  
                    if(data['course']!=''){
                        if(data['course'].status=='1'){
                            statusClass='bg-green';
                            statusBadge='Active';
                            cb_label_right='DEACTIVATE';
                            cb_class_right='orange'
                            
                        }else{
                            statusClass='bg-yellow';
                            statusBadge='Inactive';
        
                            cb_label_right='ACTIVATE';
                            cb_class_right='green';
                           
                        }
                        var vstatus = (data['course'].status=='0')?'1':'0';
                        $('#status_right_button_'+course_id).attr("onclick","changeCourseStatus('"+course_id+"', '"+vstatus+"')");
                        $('#status_right_button_'+course_id).html(cb_label_right.toUpperCase() +'<ripples></ripples>').removeClass('btn-green').removeClass('btn-orange').addClass('btn-'+cb_class_right);
                        $('#status_btn_'+course_id+' a').attr("onclick","changeCourseStatus('"+course_id+"', '"+vstatus+"')");
                        $('#status_btn_'+course_id+' a').html(cb_label_right);
                        $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(statusBadge).addClass(statusClass);
                       
                    }
                     
                }
                
            }
            
        }
    });
}

function deleteSection(section_name, section_id)
{

    var messageObject = {
        'body': 'Are you sure you want to delete Section <b>"'+''+decodeURIComponent(escape(atob(section_name)))+'"</b>?',
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            "section_id": section_id,
            "section_name":decodeURIComponent(escape(atob(section_name)))
        },
    };
    callback_warning_modal(messageObject, deleteSectionConfirmed);
}

function deleteSectionConfirmed(param)
{
    var section_id = param.data.section_id;
    var sectionName = param.data.section_name;
    $.ajax({
        url: admin_url+'coursebuilder/delete_section',
        type: "POST",
        data:{ "is_ajax":true,
            'section_id':section_id,
            'course_id':__course_id,
            'section_name':sectionName,
            'course_name':__course_name
        },
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data.error==false){

                $('#section_wrapper_'+section_id).remove();
                __total_sections = parseInt(__total_sections-1);
                updateCount();
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                
                callback_success_modal(messageObject);
                if(data.cb_status == '0'){
                    changeCourseStatusLabel(data.cb_status);
                }
            }else{
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
           
            
        }
    });
}

function changeCourseStatusLabel(cb_status){
    if(cb_status){
        var statusClass='bg-yellow';
        var statusBadge='Inactive';
        var cb_label_right='ACTIVATE';
        var cb_class_right='green';
        var vstatus = '1';
        $('#status_right_button_'+__course_id).attr("onclick","changeCourseStatus('"+__course_id+"', '"+vstatus+"')");
        $('#status_right_button_'+__course_id).html(cb_label_right.toUpperCase() +'<ripples></ripples>').removeClass('btn-green').removeClass('btn-orange').addClass('btn-'+cb_class_right);
        $('#status_btn_'+__course_id+' a').attr("onclick","changeCourseStatus('"+__course_id+"', '"+vstatus+"')");
        $('#status_btn_'+__course_id+' a').html(cb_label_right);
        $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(statusBadge).addClass(statusClass);
    }
}

function updateSectionPositon(position, selector)
{
    var current_position = parseInt(position+1);
    var section_id       = selector.split('_');
        section_id       = section_id[2];
     $.ajax({
        url: admin_url+'coursebuilder/update_section_position',
        type: "POST",
        data:{ "is_ajax":true, "course_id":__course_id ,'section_id':section_id, 'position':position, 'structure':$("#sortable").sortable('serialize')},
        success: function(response) {
        }
    });    
}

function updateLecturePosition(section_id, items)
{
     $.ajax({
        url: admin_url+'coursebuilder/update_lecture_position',
        type: "POST",
        data:{ "is_ajax":true, "course_id":__course_id, 'section_id':section_id, 'structure':items},
        success: function(response) {
            var data = $.parseJSON(response);

            // if(typeof data['cl_status'] != 'undefined' && data['cl_status'] == '0' ) { 
            //     $('#section_status_text_'+data['section_id']).html(lang('inactive'));
            //     $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
            // }
            if( $('#section_lecture_'+data['section_id']+' .active-lecture').length > 0) {
                $('#section_status_text_'+data['section_id']).html(lang('active'));
                $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
            }

            if( $('#section_lecture_'+data['section_id']+' .active-lecture').length <= 0) {
                $('#section_status_text_'+data['section_id']).html(lang('inactive'));
                $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
            }

        }
    });    
}

function createAssesment()
{
    $('#assesment_name').val('');
    $('#section_id_assesment').val('');
    $('#section_name_assesment').val('');
    $('#assesment_description').val('');
    $('#section_id_assesment,#create_new_section_assesment').show();
    $('#section_name_assesment,#create_new_section_cancel_assesment').hide();
    $('#create_new_section_assesment').show();
    $('#create_new_section_cancel_assesment').hide();
    $('#assesment_description_char_left').html('1000 Characters left');
    $('#popUpMessage').hide();
    
    $('#section_id_assesment').html(getSectionsOptionHtml());   
}

function createAssesmentConfirmed()
{
    
    var assesment_name                  = $('#assesment_name').val().trim();
    var section_id                      = $('#section_id_assesment').val().trim();
    var section_name                    = $('#section_name_assesment').val().trim();
    var assesment_description           = $('#assesment_description').val().trim();
    var show_categories = $('#a_show_categories').prop('checked');
        show_categories = (show_categories==true)?'1':'0';
    var sent_mail_on_assesment_creation = $('#sent_mail_on_assesment_creation').prop('checked');
    sent_mail_on_assesment_creation = (sent_mail_on_assesment_creation==true)?'1':'0';
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( assesment_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter quiz name <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'Please choose section <br />';
    }

    if( assesment_description == '')
    {
        //errorCount++;
        //errorMessage += 'please enter assesment description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#assesment .modal-body').prepend(renderPopUpMessage('error', errorMessage));    
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }

    $('#createAssesmentConfirmed').removeAttr('onclick');
    $('#createAssesmentConfirmed').text('SAVING...');
        $.ajax({
            url: admin_url+'coursebuilder/save_assesment',
            type: "POST",
            data:{ "course_id":__course_id,"course_name":__course_name, "show_categories":show_categories, "is_ajax":true, 'assesment_name':assesment_name, 'sent_mail_on_assesment_creation':sent_mail_on_assesment_creation, 'assesment_description':assesment_description, 'section_id':section_id, 'section_name':section_name, 'sent_mail_on_assesment_creation':sent_mail_on_assesment_creation},
            success: function(response) {
                
                //$('#createAssesmentConfirmed').attr('onclick', 'createAssesmentConfirmed()');
                var data  = $.parseJSON(response);
                
                if(data.error == false)
                {
                    window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                }
                else
                {
                    $('#createAssesmentConfirmed').text('CREATE');
                    $('#createAssesmentConfirmed').attr('onclick', 'createAssesmentConfirmed()');
                    cleanPopUpMessage();
                    $('#assesment .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
            }
        });    
}

function createSurvey()
{
    $('#survey_name').val('');
    $('#section_id_survey').val('');
    $('#section_name_survey').val('');
    $('#survey_description').val('');
    $('#section_id_survey,#create_new_section_survey').show();
    $('#section_name_survey,#create_new_section_cancel_survey').hide();
    $('#create_new_section_survey').show();
    $('#create_new_section_cancel_survey').hide();
    $('#survey_description_char_left').html('1000 Characters left');
    $('#popUpMessage').hide();
    $('#survey_regular').prop('checked', true).trigger('change');
    $('#section_id_survey').html(getSectionsOptionHtml());   
}

function createSurveyConfirmed()
{
    var survey_name                  = $('#survey_name').val().trim();
    var section_id                   = $('#section_id_survey').val().trim();
    var section_name                 = $('#section_name_survey').val().trim();
    var survey_description           = $('#survey_description').val().trim();
    var survey_type                  = $('input[name="survey_type"]:checked').val().trim();
    var tutor_id                     = $('#survey_tutor_list').val();
    var tutor_name                   = '';

    var errorCount                   = 0;
    var errorMessage                 = '';
    var sent_mail_on_survey_creation = $('#sent_mail_on_survey_creation').prop('checked');
    sent_mail_on_survey_creation = (sent_mail_on_survey_creation==true)?'1':'0';
    //end

    //validation process
    if( survey_name == '')
    {
        errorCount++;
        errorMessage += 'please enter survey name <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }

    if(survey_type == 'tutor' && tutor_id <= 0 ) {
        errorCount++;
        errorMessage += 'please choose tutor <br />';
    }

    if( survey_description == '')
    {
        //errorCount++;
        //errorMessage += 'please enter assesment description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#survey .modal-body').prepend(renderPopUpMessage('error', errorMessage));    
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }
    
    if(survey_type == 'regular') {
        tutor_id = '';
    }
    if(tutor_id != '') {
        tutor_name                   = $("#survey_tutor_list option:selected").html();
    }
    $("#createSurveyConfirmed").removeAttr('onclick');
    $("#createSurveyConfirmed").text('SAVING...');
    setTimeout(() => {
        $.ajax({
            url: admin_url+'coursebuilder/save_survey',
            type: "POST",
            data:{ "course_id":__course_id,"course_name":__course_name, "is_ajax":true, 'survey_name':survey_name, 'survey_description':survey_description, 'section_id':section_id, 'section_name':section_name, 'tutor_id':tutor_id, 'tutor_name':tutor_name, 'sent_mail_on_survey_creation':sent_mail_on_survey_creation},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    location.href = admin_url+'coursebuilder/lecture/'+data['id'];
                }
                else
                {
                    $("#createSurveyConfirmed").text('CREATE');
                    $("#createSurveyConfirmed").attr('onclick','createSurveyConfirmed');
                    cleanPopUpMessage();
                    $('#survey .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
            }
        });
    }, 1000);
       
}

var pdfobj = '';
var __uploading_file = '';
var descriptive_allowed = ['pdf'];
var scorm_allowed       = ['zip'];
$(document).on('change', "#descriptive_test_file", function(e){
    if ($(this).val()) {
        var fileName = $(this).val().split('/').pop().split('\\').pop();
        $('.file-descriptive').css('display','block');
        $('#attached_file_descriptive').html('<label>'+lang('attached')+' : '+fileName+'</label>');
    }
    pdfobj = e.currentTarget.files;
    $('#upload_descriptive_file').val(pdfobj[0]['name']);
    
    var fileObj                     = new processFileName(pdfobj[0]['name']);
        fileObj.uniqueFileName();        
    if(inArray(fileObj.fileExtension(), descriptive_allowed) == false)
    {
        lauch_common_message('Invalid File', 'This type of file is not allowed.');    
        return false;        
    }
});
var scormobj    = '';
$(document).on('change', '#scorm_file', function(e){
    if ($(this).val() ) {
        var fileName = $(this).val().split('/').pop().split('\\').pop();
        $('.file-descriptive').css('display','block');
        $('#attached_file_scorm').html('<label>'+lang('attached')+' : '+fileName+'</label>');
    }
    scormobj    = e.currentTarget.files;
    $('#upload_scorm').val(scormobj[0]['name']);
    var fileObj                     = new processFileName(scormobj[0]['name']);
        fileObj.uniqueFileName();        
    if(inArray(fileObj.fileExtension(), scorm_allowed) == false)
    {
        lauch_common_message('Invalid File', 'This type of file is not allowed.');    
        return false;        
    }
});

function createScorm() {
    $('#scorm_name').val('');
    $('#section_id_scorm').val('');
    $('#section_name_scorm').val('');
    $('#section_id_scorm').show();
    $('#section_name_scorm').hide();
    $('#create_new_section_scorm').show();
    $('#create_new_section_scorm_cancel').hide();
    $('#descriptive_test_file').val('');
    $('#scorm_description').val('');
    $('#attached_file_scorm').hide();
    $('#scorm_percentage_bar').hide();
    $('#assesment_description_char_left').html('1000 Characters left');
    $('#upload_scorm').val('');
    $('#popUpMessage').hide();
    
    $('#section_id_scorm').html(getSectionsOptionHtml());
}

function createScormConfirm() {
    var scorm_title         = $('#scorm_name').val().trim();
    var scorm_section_id    = $('#section_id_scorm').val().trim();
    var scorm_section_name  = $('#section_name_scorm').val().trim();
    var scorm_description   = $('#scorm_description').val().trim();
    var errorCount          = 0;
    var errorMessage        = '';
    __uploading_file        = scormobj;

    if( scorm_title == '')
    {
        errorCount++;
        errorMessage += 'Please enter Scorm title<br />';
    }

    if(__create_description_as_new == true && scorm_section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_description_as_new == false && scorm_section_id == '')
    {
        errorCount++;
        errorMessage += 'Please choose section <br />';
    }

    if( __uploading_file.length == 0 ){
        errorCount++;
        errorMessage += 'Please select a file <br />';
    }

    if( __uploading_file.length > 1 ){
        errorCount++;
        errorMessage += 'More than one file not allowed<br />';
    }
    if(__uploading_file != '')
    {
        var fileObj                     = new processFileName(__uploading_file[0]['name']);
            fileObj.uniqueFileName();        
        if(inArray(fileObj.fileExtension(), scorm_allowed) == false)
        {
            errorCount++;
            errorMessage += 'Uploaded file type is not allowed.<br />';        
        }
    }

    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('.scorm_form').html(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        scorm_section_name   = scorm_section_name;                    
        scorm_section_id     = '';
    }
    else
    {
        scorm_section_name   = '';
        scorm_section_id     = scorm_section_id;                    
    }

    var i                           = 0;
    
    var uploadURL                   = admin_url+"coursebuilder/upload_to_home_server_scorm"; 
    var fileObj                     = new processFileName(__uploading_file[i]['name']);
    var param                       = new Array;
        param["file_name"]          = fileObj.uniqueFileName();        
        param["extension"]          = fileObj.fileExtension();
        param["file"]               = __uploading_file[i];                
        param['section_id']         = scorm_section_id;
        param['section_name']       = scorm_section_name;
        param['lecture_name']       = scorm_title;
        param['lecture_description']  = scorm_description;
        param['course_id']          = __course_id;
        param['course_name']        = __course_name;
    $('#scorm_percentage_bar').show();
    uploadFiles(uploadURL, param, uploadScormComplete);

}

function uploadScormComplete(response){
    var data  = $.parseJSON(response);
    if(data['error'] == "false")
    {
        
        window.location = admin_url+'coursebuilder/lecture/'+data['id'];
    }
    else
    {
        $('#scorm_modal').modal('hide');
        var messageObject = {
            'body': data['message'],
            'button_yes': 'OK',
        };
        callback_danger_modal(messageObject);
    }
}

function createDescriptive(){
    $('#descriptive_test_name').val('');
    $('#total_mark').val('');
    $('#section_id_descriptive').val('');
    $('#section_name_descriptive').val('');
    $('#section_id_descriptive').show();
    $('#section_name_descriptive').hide();
    $('#create_new_desciptive_test').show();
    $('#create_new_desciptive_test_cancel').hide();
    $('#descriptive_description').val('');
    $('#descriptive_test_file').val('');
    $('#descriptive_submission_date').val('');
    $('#descriptive_words_limit').val('');
    
    $('#assesment_description_char_left').html('1000 Characters left');
    $('#popUpMessage').hide();
    
    $('#section_id_descriptive').html(getSectionsOptionHtml());
}

function createDescriptiveTest(){
        var descriptive_test_name       = $("#descriptive_test_name").val().trim();
        var descriptive_section_id      = $("#section_id_descriptive").val().trim();
        var descriptive_section_name    = $("#section_name_descriptive").val().trim();
        var descriptive_test_description= $("#descriptive_description").val().trim();
        var submission_date             = $("#descriptive_submission_date").val().trim();
        var descriptive_words_limit     = $("#descriptive_words_limit").val().trim();
        var total_mark                  = $("#total_mark").val().trim();
        var errorCount                  = 0;
        var errorMessage                = '';
        var testid                      = '';
        var sent_mail_on_descriptive_creation = $('#sent_mail_on_descriptive_creation').prop('checked');
        sent_mail_on_descriptive_creation = (sent_mail_on_descriptive_creation==true)?'1':'0';
        
    // __uploading_file                = pdfobj;

        if( descriptive_test_name == '')
        {
            errorCount++;
            errorMessage += 'Please enter assignment name<br />';
        }

        if(__create_description_as_new == true && descriptive_section_name == '')
        {
            errorCount++;
            errorMessage += 'Section required <br />';
        }

        if(__create_description_as_new == false && descriptive_section_id == '')
        {
            errorCount++;
            errorMessage += 'Please choose section <br />';
        }

        if( total_mark == ''){
            errorCount++;
            errorMessage += 'Please enter total mark <br />';
        }

        if(total_mark < 0){
            errorCount++;
            errorMessage += 'Total mark should be grater than zero.<br />';
        }

        if(descriptive_words_limit < 0){
            errorCount++;
            errorMessage += 'Words limit should be grater than zero.<br />';
        }

        if( submission_date == ''){
            errorCount++;
            errorMessage += 'Please enter the submission date <br />';
        }

        cleanPopUpMessage();
        if(errorCount > 0 )
        {
            $('.descriptive_form').html(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
            return false;
        }
        
        if(__create_section_as_new==true)
        {
            section_name   = section_name;                    
            section_id     = '';
        }
        else
        {
            section_name   = '';
            section_id     = section_id;                    
        }
        $('#createDescriptiveTest').removeAttr('onclick');
        $('#createDescriptiveTest').text('SAVING...');
        setTimeout(() => {
            $.ajax({
                url: admin_url+'coursebuilder/save_descriptive_test',
                type: "POST",
                data:{ "course_id":__course_id,"course_name":__course_name, "is_ajax":true,  'total_mark':total_mark, 'descriptive_test_name':descriptive_test_name, 'descriptive_test_description':descriptive_test_description, 'descriptive_section_id':descriptive_section_id, 'descriptive_section_name':descriptive_section_name, 'submission_date':submission_date, 'descriptive_words_limit':descriptive_words_limit, 'sent_mail_on_descriptive_creation':sent_mail_on_descriptive_creation},
                success: function(response) {
                    //$('#createDescriptiveTest').attr('onclick', 'createDescriptiveTest()');
                    var data  = $.parseJSON(response);
                    if(data['error'] == "false")
                    {
                        window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                    }
                    else
                    {
                        $('#createDescriptiveTest').text('CREATE');
                        $('#createDescriptiveTest').attr('onclick', 'createDescriptiveTest()');
                        cleanPopUpMessage();
                        $('.descriptive_form').html(renderPopUpMessage('error', data['message']));
                    }
                }
            });
        }, 1000);
            
        var param                       = new Array;
        param['descriptive_test_name'] = descriptive_test_name;
        param['testid']             = testid;
        param['descriptive_section_id'] = descriptive_section_id;
        param['descriptive_section_name'] = descriptive_section_name;
        param['descriptive_test_description'] = descriptive_test_description;
        param['course_id']          = __course_id;
        param['total_mark']         = total_mark;
  
}

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function toDate(dateStr) {
    var parts = dateStr.split("-")
    return new Date(parts[2], parts[1] - 1, parts[0])
}

function uploadDescriptiveTestPdf(response){
    var data  = $.parseJSON(response);
    if(data['error'] == "false")
    {
        window.location = admin_url+'coursebuilder/lecture/'+data['id'];
    }
    else
    {
        // alert(data['message']);
    }
}

function uploadDescriptiveTestToS3Completed(){

}

function changeLectureStatus(current_status, lecture_name, lecture_id)
{
    var header_status = ((current_status==1)?'deactivate':'activate');
    var messageObject = {
        'body': 'Are you sure you want to '+header_status+' <b>"'+decodeURIComponent(escape(atob(lecture_name)))+'"</b>?',
        'button_yes': header_status.toUpperCase(),
        'button_no': 'CANCEL',
        'continue_params': {
            "lecture_id": lecture_id,
            "status":((current_status==1)?0:1),
            "lecture_name":decodeURIComponent(escape(atob(lecture_name)))
        },
    };
    callback_warning_modal(messageObject, changeLectureStatusConfirmed);
   
}

function changeLectureStatusConfirmed(param)
{
    var lecture_id  = param.data.lecture_id;
    var status      = param.data.status;
    var lecture_name= param.data.lecture_name;
    $.ajax({
        url: admin_url+'coursebuilder/change_lecture_status',
        type: "POST",
        data:{ "is_ajax":true,
            'course_id' :__course_id,
            'lecture_id':lecture_id,
            'status'    :status,
            'course_name':__course_name
        },
        success: function(response){
            var data  = $.parseJSON(response);
            console.log(data);
            if(data['error'] == false)
            {
                if(status == 1) {
                    $('#lecture_status_text_'+lecture_id).html(lang('active'));
                    $('#lecture_id_'+lecture_id).removeClass('active-lecture').removeClass('Inactive-lecture').addClass('active-lecture');
                    $('#lecture_status_wraper_'+lecture_id).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                    $('#lecture_action_status_'+lecture_id).html('<a onclick="changeLectureStatus(\'1\', \''+btoa(unescape(encodeURIComponent(lecture_name)))+'\', \''+lecture_id+'\')" href="javascript:void(0)">'+lang('deactivate')+'</a>');
                } else {
                    $('#lecture_status_text_'+lecture_id).html(lang('inactive'));
                    $('#lecture_id_'+lecture_id).removeClass('active-lecture').removeClass('Inactive-lecture').addClass('Inactive-lecture');
                    $('#lecture_status_wraper_'+lecture_id).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                    $('#lecture_action_status_'+lecture_id).html('<a onclick="changeLectureStatus(\'0\', \''+btoa(unescape(encodeURIComponent(lecture_name)))+'\', \''+lecture_id+'\')" href="javascript:void(0)">'+lang('activate')+'</a>');                
                    if(data['course']!=''){
                        if(data['course'].status=='1'){
                            statusClass='bg-green';
                            statusBadge='Active';
                            cb_label_right='DEACTIVATE';
                            cb_class_right='orange'
                            
                        }else{
                            statusClass='bg-yellow';
                            statusBadge='Inactive';
        
                            cb_label_right='ACTIVATE';
                            cb_class_right='green';
                           
                        }
                        var vstatus = (data['course'].status=='0')?'1':'0';
                        $('#status_right_button_'+__course_id).attr("onclick","changeCourseStatus('"+__course_id+"', '"+vstatus+"')");
                        $('#status_right_button_'+__course_id).html(cb_label_right.toUpperCase() +'<ripples></ripples>').removeClass('btn-green').removeClass('btn-orange').addClass('btn-'+cb_class_right);
                        $('#status_btn_'+__course_id+' a').attr("onclick","changeCourseStatus('"+__course_id+"', '"+vstatus+"')");
                        $('#status_btn_'+__course_id+' a').html(cb_label_right);
                        $('#status_badge').removeClass('bg-yellow').removeClass('bg-green').removeClass('bg-red').html(statusBadge).addClass(statusClass);
                    }
                }
                if(data['active_lecture_count'] > 0) {
                    $('#section_status_text_'+data['section_id']).html(lang('active'));
                    $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                }

                if(data['active_lecture_count'] <= 0) {
                    $('#section_status_text_'+data['section_id']).html(lang('inactive'));
                    $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                
                }

                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                if(data.cb_status == '0'){
                    changeCourseStatusLabel(data.cb_status);
                }
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            } 
        }
    });
}

function deleteLecture(lecture_name, lecture_id)
{
    var messageObject = {
        //'body': lang('are_you_sure')+' '+lang('you_want_to_delete')+' '+atob(lecture_name),
        'body': 'Are you sure to Delete the Lecture named "'+decodeURIComponent(escape(atob(lecture_name)))+'" ?',
        'button_yes': 'DELETE',
        'button_no': 'CANCEL',
        'continue_params': {
            "lecture_id": lecture_id
        },
    };
    callback_warning_modal(messageObject, deleteLectureConfirmed);
}

function deleteLectureConfirmed(param)
{
    lecture_id = param.data.lecture_id;
    $.ajax({
        url: admin_url+'coursebuilder/delete_lecture',
        type: "POST",
        data:{
             "is_ajax":true,
             'lecture_id':lecture_id,
             "course_id":__course_id,
             "course_name":__course_name
            },
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == 'false'){
                $('#lecture_id_'+lecture_id).remove();
                __total_lecture = parseInt(__total_lecture -1);
                updateCount();
                // if(typeof data['cl_status'] != 'undefined' && data['cl_status'] == '0' ) { 
                //     $('#section_status_text_'+data['section_id']).html(lang('inactive'));
                //     $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                // }

                if(data['active_lecture_count'] > 0) {
                    $('#section_status_text_'+data['section_id']).html(lang('active'));
                    $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('active-section');
                }

                if(data['active_lecture_count'] <= 0) {
                    $('#section_status_text_'+data['section_id']).html(lang('inactive'));
                    $('#section_status_wraper_'+data['section_id']).removeClass('active-section').removeClass('Inactive-section').addClass('Inactive-section');
                }

    
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                if(data.cb_status == '0'){
                    changeCourseStatusLabel(data.cb_status);
                }
            }else{
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function createYoutubeLecture()
{
    $('#youtube_name').val('');
    $('#section_id_youtube').val('');
    $('#section_name_youtube').val('');
    $('#section_id_youtube').show();
    $('#section_name_youtube').hide();
    $('#create_new_section_youtube').show();
    $('#create_new_section_cancel_youtube').hide();
    $('#youtube_description').val('');
    $('#youtube_url').val('');
    $('#youtube_description_char_left').html('1000 Characters left');
    $('#popUpMessage').hide();
    
    $('#section_id_youtube').html(getSectionsOptionHtml());   
}

function createYoutubeConfirmed()
{ 
    var youtube_name                    = $('#youtube_name').val().trim();
    var section_id                      = $('#section_id_youtube').val().trim();
    var section_name                    = $('#section_name_youtube').val().trim();
    var youtube_description             = $('#youtube_description').val().trim();
    var youtube_url                     = $('#youtube_url').val().trim();
   
    var errorCount                      = 0;
    var errorMessage                    = '';
    var sent_mail_on_youtube_creation = $('#sent_mail_on_youtube_creation').prop('checked');
    sent_mail_on_youtube_creation = (sent_mail_on_youtube_creation==true)?'1':'0';
    //end

    //validation process
    if( youtube_name == '')
    {
        errorCount++;
        errorMessage += 'please enter Lecture title <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }
    
    if( youtube_url == "")
    {
        errorCount++;
        errorMessage += 'please enter url<br />';        
    }
    else
    {
        if( isValidYoutubeOrVimeoURL(youtube_url) == false)
        {
            errorCount++;
            errorMessage += 'invalid url<br />';        
        }
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#youtube .modal-body').prepend(renderPopUpMessage('error', errorMessage));   
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }
    $("#create_btn").attr('disabled','disabled');
    
        $.ajax({
            url: admin_url+'coursebuilder/save_youtube',
            type: "POST",
            data:{ 
                "course_id":__course_id, 
                "is_ajax":true, 
                'youtube_url':youtube_url, 
                'youtube_name':youtube_name, 
                'youtube_description':youtube_description, 
                'section_id':section_id, 
                'section_name':section_name,
                'course_name':__course_name,
                'sent_mail_on_youtube_creation':sent_mail_on_youtube_creation
            },
            beforeSend: function() {
                $("#create_btn").text('SAVING...');
                $("#create_btn").attr('disabled','disabled');
             },
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                }
                else
                {
                    $("#create_btn").text('CREATE');
                    cleanPopUpMessage();
                    $('#youtube .modal-body').prepend(renderPopUpMessage('error', data['message']));
    
                }
                $("#create_btn").text('CREATE');
                $("#create_btn").removeAttr("disabled");
            }
        });
      
}

function loadSectionsAndLecures(course_id)
{
    $('#import_section_list').html('');
    __lecture_selected = new Array();
    if(course_id == '')
    {
        return false;
    }
    $.ajax({
        url: admin_url+'coursebuilder/course_objects',
        type: "POST",
        data:{ "course_id":course_id, "is_ajax":true},
        success: function(response) {
            var data                = $.parseJSON(response);
            var courseObjectHtml    = '';
            if(data['sections'].length > 0 )
            {
                var lectureIcons  = data['lecture_icons']; 
                for (var i=0; i< data['sections'].length ; i++)
                {
                    courseObjectHtml+= `<li><b>${lang('section')}:</b>${data['sections'][i]['s_name']} </label>`;
                    var section_lecture = data['sections'][i]['lecture'];
                    if(section_lecture.length > 0 )
                    {
                        courseObjectHtml+= '    <ul>';
                        for (var j=0; j< section_lecture.length ; j++)
                        {
                            courseObjectHtml+= `<li class="lectr-deep-innr">
                                                    
                                                    <label class="check-box-holder">
                                                        <label class="custom-checkbox" style="position: relative;top:4px;">
                                                            <input data-id="49" class="list-checkbox-featured lecture-checkbox" type="checkbox" value="${section_lecture[j]['id']}">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <span class="${lectureIcons[section_lecture[j]['cl_lecture_type']]['child']}"  style="margin-right: 15px;min-width:25px;"></span>
                                                        <span class="showin-home-text">${section_lecture[j]['cl_lecture_name']}</span>
                                                    </label>
                                                </li>`;
                        }
                        courseObjectHtml+= '    </ul>';
                    }
                    courseObjectHtml+= '</li>';
                }
                $('#import_section_list').html(courseObjectHtml);
            }
            else {
                $('#import_section_list').html('No lecture found!');
            }
        }
    });
}

var __lecture_selected = new Array();
$(document).on('click', '.lecture-checkbox', function(){
    var lecture_id = $(this).val().trim();
    if ($(this).is(':checked')) {
        __lecture_selected.push(lecture_id);
    }else{
        removeArrayIndex(__lecture_selected, lecture_id);
    }
});

// function importContent()
// {
//     __lecture_selected  = new Array();
//     $('.lecture-checkbox').prop('checked', false);
//    $('#import_section_list').html('');
//    $('#section_id_import_content').html(getSectionsOptionHtml()); 
//    $('#course_id_import_content').html(getCoursesOptionHtml()); 
// }

// function importContentConfirm()
// {
//     var course_id                       = $('#course_id_import_content').val().trim();
//     var section_id                      = $('#section_id_import_content').val().trim();
//     var section_name                    = $('#section_name_import_content').val().trim();
//     var sent_mail_on_import_creation    = $('#sent_mail_on_import_creation').prop('checked');
//         sent_mail_on_import_creation    = (sent_mail_on_import_creation==true)?'1':'0';
//     var errorCount                      = 0;
//     var errorMessage                    = '';
//     //end

//     //validation process
//     if( __lecture_selected.length == 0)
//     {
//         errorCount++;
//         errorMessage += 'please choose atleast one lecture to import <br />';
//     }

//     if(__create_section_as_new == true && section_name == '')
//     {
//         errorCount++;
//         errorMessage += 'Section required <br />';
//     }

//     if(__create_section_as_new == false && section_id == '')
//     {
//         errorCount++;
//         errorMessage += 'please choose section <br />';
//     }
//     cleanPopUpMessage();
//     if(errorCount > 0 )
//     {
//         $('#importContent .modal-body').prepend(renderPopUpMessage('error', errorMessage));   
//         return false;
//     }
    
//     if(__create_section_as_new==true)
//     {
//         section_name   = section_name;                    
//         section_id     = '';
//     }
//     else
//     {
//         section_name   = '';
//         section_id     = section_id;                    
//     }
    
//     $.ajax({
//         url: admin_url+'coursebuilder/import_lecture',
//         type: "POST",
//         data:{ "is_ajax":true, "course_id":__course_id, 'sent_mail_on_import_creation':sent_mail_on_import_creation, 'lecture_selected':JSON.stringify(__lecture_selected), 'section_id':section_id, 'section_name':section_name},
//         success: function(response) {
//             var data            = $.parseJSON(response);
//             var lecture_icons   = data['lecture_icons'];
//             if(data['error'] == "false")
//             {
//                 if( section_id == '' ) 
//                 {
//                     var sectionHtml = '';
//                         sectionHtml += '<li class="section" id="section_wrapper_'+data['section']['id']+'" data-section-name="'+section_name+'">';
//                         sectionHtml += '    <div class="section-title-holder">';
//                         sectionHtml += '        <div class="section-counter"></div>';
//                         sectionHtml += '        <div class="drager">';
//                         sectionHtml += '            <img src="'+assets_url+'images/drager.png">';
//                         sectionHtml += '        </div>';
//                         sectionHtml += '        <span class="section-name" id="section_name_'+data['section']['id']+'"> '+section_name+'</span>';
//                         sectionHtml += '        <div class="btn-group section-control sectiontitle-dropalign">';
//                         sectionHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
//                         sectionHtml += '                <span class="label-text">';
//                         sectionHtml += '                    <i class="icon icon-down-arrow"></i>';
//                         sectionHtml += '                </span>';
//                         sectionHtml += '            <span class="tilder"></span>';
//                         sectionHtml += '            </span>';
//                         sectionHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
//                         sectionHtml += '                <li>';
//                         sectionHtml += '                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rename_section" onclick="renameSection(\''+data['section']['id']+'\')">'+lang('rename')+'</a>';
//                         sectionHtml += '                </li>';
//                         sectionHtml += '                <li>';
//                         sectionHtml += '                    <a href="javascript:void(0)" data-toggle="modal" data-target="#deleteSection" onclick="deleteSection(\''+btoa(section_name)+'\', \''+data['section']['id']+'\')">'+lang('delete')+'</a>';
//                         sectionHtml += '                </li>';
//                         sectionHtml += '                <li id="section_action_status_'+data['section']['id']+'">';
//                         sectionHtml += '                    <a href="javascript:void(0)" data-target="#Deactivate" data-toggle="modal" onclick="changeSectionStatus(\'0\', \''+btoa(section_name)+'\', \''+data['section']['id']+'\')">'+lang('inactive')+'</a>';
//                         sectionHtml += '                </li>';
//                         sectionHtml += '            </ul>';
//                         sectionHtml += '        </div>';
//                     var status_class = 'Inactive';
//                     var status_label = 'deactivate';
//                         sectionHtml += '        <div id="section_status_wraper_'+data['section']['id']+'" class="'+status_class+'-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="section_status_text_'+data['section']['id']+'">'+lang(status_label)+'</span></div>';
//                         sectionHtml += '    </div>';
//                         sectionHtml += '    <ul class="lecture-wrapper" id="section_lecture_'+data['section']['id']+'">';
//                         sectionHtml += '    </ul>';
//                         sectionHtml += '</li>';
//                     $('#sortable').append(sectionHtml);
//                     __total_sections = parseInt(__total_sections+1);
//                 }
//                 if( data['lectures'].length > 0 )
//                 {
//                     var lecturesHtml = '';
//                     for (var i=0; i< data['lectures'].length ; i++)
//                     {
//                         lecturesHtml += '<li id="lecture_id_'+data['lectures'][i]['id']+'">';
//                         lecturesHtml += '    <div class="lecture-hold">';
//                         lecturesHtml += '        <div class="lecture-counter"></div>';
//                         lecturesHtml += '        <div class="drager ui-sortable-handle">';
//                         lecturesHtml += '            <img src="'+assets_url+'images/drager.png">';
//                         lecturesHtml += '        </div>';
//                         lecturesHtml += '        <a class="lecture-innerclick" href="'+admin_url+'coursebuilder/lecture/'+data['lectures'][i]['id']+'">';
//                         lecturesHtml += '            <span class="lecture-icon '+lecture_icons[data['lectures'][i]['cl_lecture_type']]['parent']+'">';
//                         lecturesHtml += '                <i class="icon '+lecture_icons[data['lectures'][i]['cl_lecture_type']]['child']+'"></i>';
//                         lecturesHtml += '            </span>';
//                         lecturesHtml += '            <span class="lecture-name">'+data['lectures'][i]['cl_lecture_name']+'</span>';
//                         lecturesHtml += '        </a>';
//                         lecturesHtml += '        <div class="btn-group lecture-control">';
//                         lecturesHtml += '            <span data-toggle="dropdown" class="dropdown-tigger">';
//                         lecturesHtml += '                <span class="label-text">';
//                         lecturesHtml += '                    <i class="icon icon-down-arrow"></i>';
//                         lecturesHtml += '                </span>';
//                         lecturesHtml += '                <span class="tilder"></span>';
//                         lecturesHtml += '            </span>';
//                         lecturesHtml += '            <ul role="menu" class="dropdown-menu pull-right">';
//                         lecturesHtml += '                <li id="lecture_action_status_'+data['lectures'][i]['id']+'">';
//                         lecturesHtml += '                    <a onclick="changeLectureStatus(\'0\', \''+btoa(data['lectures'][i]['cl_lecture_name'])+'\', \''+data['lectures'][i]['id']+'\')" data-toggle="modal" data-target="#Deactivate" href="javascript:void(0)">'+lang('activate')+'</a>';
//                         lecturesHtml += '                </li>';
//                         lecturesHtml += '                <li>';
//                         lecturesHtml += '                    <a href="'+admin_url+'coursebuilder/report/'+data['lectures'][i]['id']+'">'+lang('report')+'</a>';
//                         lecturesHtml += '                </li>';
//                         lecturesHtml += '                <li>';
//                         lecturesHtml += '                    <a href="'+admin_url+'coursebuilder/settings/'+data['lectures'][i]['id']+'">'+lang('settings')+'</a>';
//                         lecturesHtml += '                </li>';
//                         lecturesHtml += '                <li>';
//                         lecturesHtml += '                    <a onclick="deleteLecture(\''+btoa(data['lectures'][i]['cl_lecture_name'])+'\', \''+data['lectures'][i]['id']+'\')" data-target="#deleteSection" data-toggle="modal" href="javascript:void(0)">'+lang('delete')+'</a>';
//                         lecturesHtml += '                </li>';
//                         lecturesHtml += '            </ul>';
//                         lecturesHtml += '        </div>';
//                         lecturesHtml += '    </div>';
//                         lecturesHtml += '</li>';
//                     }
//                     $('#section_lecture_'+data['section']['id']).html(lecturesHtml);
//                     __total_lecture     = parseInt(__total_lecture+__lecture_selected.length);
//                 }
//                 $('#section_lecture_count').html(__total_sections+' '+lang('sections')+' - '+__total_lecture+' '+lang('lessons') )
//                 __lecture_selected  = new Array();
//                 $('.lecture-checkbox').prop('checked', false);
//                 $('#importContent').modal('hide');
//             }
//             else
//             {
//                 cleanPopUpMessage();
//                 $('#importContent .modal-body').prepend(renderPopUpMessage('error', data['message']));
//             }
//         }
//     });   
// }

function createHtml()
{
    $('#html_name').val('');
    $('#section_id_html').val('');
    $('#section_name_html').val('');
    $('#section_id_html').show();
    $('#section_name_html').hide();
    $('#create_new_section_html').show();
    $('#create_new_section_cancel_html').hide();
    $('#html_description').val('');
    $('#popUpMessage').hide();
    $('#html_description_char_left').html('1000 Characters left');
    
    $('#section_id_html').html(getSectionsOptionHtml());   
}

function createHtmlConfirmed()
{
    var html_name                       = $('#html_name').val().trim();
    var section_id                      = $('#section_id_html').val().trim();
    var section_name                    = $('#section_name_html').val().trim();
    var html_description                = $('#html_description').val().trim();
    
    var errorCount                      = 0;
    var errorMessage                    = '';
    var sent_mail_on_htmlcode_creation = $('#sent_mail_on_htmlcode_creation').prop('checked');
        sent_mail_on_htmlcode_creation = (sent_mail_on_htmlcode_creation==true)?'1':'0';
    //end

    //end

    //validation process
    if( html_name == '')
    {
        errorCount++;
        errorMessage += 'please enter html lecture name <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }

    if( html_description == '')
    {
        //errorCount++;
        //errorMessage += 'please enter html lecture description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#htmlcode .modal-body').prepend(renderPopUpMessage('error', errorMessage));   
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }
    $('#createHtmlConfirmed').removeAttr('onclick');
    $('#createHtmlConfirmed').text('SAVING...');
        setTimeout(() => {
            $.ajax({
                url: admin_url+'coursebuilder/save_html',
                type: "POST",
                data:{ "course_id":__course_id,"course_name":__course_name ,"is_ajax":true, 'html_name':html_name,'html_description':html_description, 'section_id':section_id, 'section_name':section_name, 'sent_mail_on_htmlcode_creation':sent_mail_on_htmlcode_creation},
                success: function(response) {
                    var data  = $.parseJSON(response);
                    if(data['error'] == "false")
                    {
                        window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                    }
                    else
                    {
                        $('#createHtmlConfirmed').text('CREATE');
                        $('#createHtmlConfirmed').attr('onclick','createHtmlConfirmed()');
                        cleanPopUpMessage();
                        $('#htmlcode .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    }
                }
            });
        }, 1000);
       
}


function scheduleLiveLecture()
{
    $('#live_lecture_name').val('');
    $('#section_id_live_lecture').val('');
    $('#section_name_live_lecture').val('');
    $('#section_id_live_lecture').show();
    $('#section_name_live_lecture').hide();
    $('#create_new_section_live_lecture').show();
    $('#create_new_section_cancel_live_lecture').hide();
    $('#live_lecture_description').val('');
    $('#schedule-date').val('');
    $('#live_lecture_start_time').val('');
    $('#live_lecture_duration').val('');
    $('#studio_list').val('');
    $('#live_lecture_description_char_left').html('1000 Characters left');
    $('#popUpMessage').hide();
    
    $('#section_id_live_lecture').html(getSectionsOptionHtml());   
}
function convertTime12to24(time12h) {
	const time_modifier = time12h.split(' ');
	const time 			= time_modifier[0], modifier = time_modifier[1];
	const hours_minutes = time.split(':');
	let hours 			= hours_minutes[0], minutes = hours_minutes[1];

    // const [time, modifier] = time12h.split(' ');
    // let [hours, minutes] = time.split(':');
  
    if (hours === '12') {
      hours = '00';
    }
  
    if (modifier === 'PM') {
      hours = parseInt(hours, 10) + 12;
    }
  
    return hours + ':' + minutes;
  }

function scheduleLiveLectureConfirmed()
{
    var live_lecture_name                   = $('#live_lecture_name').val().trim();
    var section_id                          = $('#section_id_live_lecture').val().trim();
    var section_name                        = $('#section_name_live_lecture').val().trim();
    var live_lecture_description            = $('#live_lecture_description').val().trim();
    var schedule_date                       = $('#schedule_date').val().trim();
    var live_lecture_start_time             = $('#live_lecture_start_time').val().trim();
    var live_lecture_duration               = $('#live_lecture_duration').val().trim();
    var studio_id                           = $('#studio_list').val().trim();
    var timeformat                          = $('#live_lecture_start_time_noon').text();
    var errorCount                          = 0;
    var errorMessage                        = '';
    var sent_mail_on_live_lecture_creation = $('#sent_mail_on_live_lecture_creation').prop('checked');
        sent_mail_on_live_lecture_creation = (sent_mail_on_live_lecture_creation==true)?'1':'0';
    //end
    //end
    live_lecture_start_time = convertTime12to24(live_lecture_start_time+" "+timeformat);
    //validation process 
    
    if( live_lecture_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter live lecture name <br />';
    }

    if(studio_id == "") {
        errorCount++;
        errorMessage += 'Please choose studio <br />';
    }

    if( schedule_date == '')
    {
        errorCount++;
        errorMessage += 'Please enter date <br />';
    }
    // else if(!isValidDate(schedule_date)){
    //     errorCount++;
    //     errorMessage += 'Not a valid date <br />';
    // }
    if((live_lecture_start_time == ':undefined') || (live_lecture_start_time == 'NaN:undefined') || (live_lecture_start_time == ''))
    //if( live_lecture_start_time == '')
    {
        errorCount++;
        errorMessage += 'Please enter start time <br />';
    }

    if( live_lecture_duration == '')
    {
        errorCount++;
        errorMessage += 'Please enter duration <br />';
    }
    else{
        if(isNaN(live_lecture_duration) || live_lecture_duration < 0){
            errorCount++;
            errorMessage += 'Duration must be a valid number <br />';
        }
    }
    

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'Please choose section <br />';
    }

    if( live_lecture_description == '')
    {
        //errorCount++;
        //errorMessage += 'please enter live lecture description<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#livelecture .modal-body').prepend(renderPopUpMessage('error', errorMessage));   
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }
    // var initial = schedule_date.split(/\//);
    // schedule_date=[ initial[1], initial[0], initial[2] ].join('/');
    $('#scheduleLiveLectureConfirmed').removeAttr('onclick');
    $('#scheduleLiveLectureConfirmed').text('SAVING...');
    setTimeout(() => {
        $.ajax({
            url: admin_url+'coursebuilder/save_live_lecture',
            type: "POST",
            data:{ "schedule_date":schedule_date, 
                    "start_time":live_lecture_start_time, 
                    "duration":live_lecture_duration, 
                    "course_id":__course_id, 
                    "is_ajax":true, 
                    'live_lecture_name':live_lecture_name, 
                    'live_lecture_description':live_lecture_description, 
                    'section_id':section_id, 
                    'section_name':section_name,
                    'studio_id':studio_id,
                    'course_name':__course_name,
                    'sent_mail_on_live_lecture_creation':sent_mail_on_live_lecture_creation
                },
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == "false")
                {
                    window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                }
                else
                {
                    // $('#livelecture').hide();
                    // var messageObject = {
                    //     'body': data['message'],
                    //     'button_yes': 'OK',
                    // };
                    // callback_danger_modal(messageObject);
                    $('#scheduleLiveLectureConfirmed').text('CREATE');
                    $('#scheduleLiveLectureConfirmed').attr('onclick', 'scheduleLiveLectureConfirmed()');
                    cleanPopUpMessage();
                    $('#livelecture .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
            }
        }); 
    }, 1000);
      
}

function isValidDate(s) {
  var bits = s.split('/');
  var d = new Date(bits[2], bits[0] - 1, bits[1]);
  return d && (d.getMonth() + 1) == bits[0];
}
$('#addcertificate').on('hidden.bs.modal', function () {
    
    $('#certificate_title').val('');
    $('#certificate_description').val('');
});

function generateCertificate(){

    cleanPopUpMessage();
    $('#section_id_certificate').val('');
    $('#section_name_certificate').val('');
    $('#section_id_certificate,#create_new_section_certificate').show();
    $('#section_name_certificate,#create_new_section_cancel_certificate').hide();
    $('#create_new_section_certificate').show();
    $('#create_new_section_cancel_certificate').hide();    
    $('#section_id_certificate').html(getSectionsOptionHtml());   
}
function generateCertificateConfirmed(){

    var c_title       = $('#certificate_title').val().trim();
    var c_description = $('#certificate_description').val().trim();
    var section_id    = $('#section_id_certificate').val().trim();
    var section_name  = $('#section_name_certificate').val().trim();
    var errorCount    = 0;
    var errorMessage  = '';
    var sent_mail_on_certificate_creation = $('#sent_mail_on_certificate_creation').prop('checked');
    sent_mail_on_certificate_creation = (sent_mail_on_certificate_creation==true)?'1':'0';

    if(c_title ==''){

        errorCount++;
        errorMessage += 'Please provide Certificate title <br />';
    
    }
    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }
    
    if(errorCount>0){
        $('#addcertificate .modal-body').prepend(renderPopUpMessage('error', errorMessage));
    }
    else{

        $.ajax({
            url: admin_url+'coursebuilder/save_certificate',
            type: "POST",
            data:{ 
                "course_id":__course_id, 
                "course_name":__course_name,
                "is_ajax":true, 
                'certificate_title':c_title, 
                'certificate_description':c_description, 
                'section_id':section_id, 
                'section_name':section_name,
                'sent_mail_on_certificate_creation':sent_mail_on_certificate_creation
            },
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'coursebuilder/lecture/'+data['id'];
                }
                else
                {
                    cleanPopUpMessage();
                    $('#addcertificate .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
            }
        });
    }
}

function updateCount(){

    __total_sections = $('.section').length;
    __total_lecture  = $('.section-lecture').length;
    
    var messageSection = __total_sections+' '+'Sections';
    if(__total_sections==0) {
        messageSection = 'No Sections';
    }
    if(__total_sections==1) {
        messageSection = __total_sections+' '+'Section';
    }
    var messageLecture = __total_lecture+' '+'Lectures'
    if(__total_lecture==0) {
        messageLecture = 'No Lectures';
    }
    if(__total_lecture==1) {
        messageLecture = __total_lecture+' '+'Lecture';
    }

    var renderHtml   = messageSection+' - '+ messageLecture;
    $('#section_lecture_count').html(renderHtml)
}

var __tutorListHtml = '';
function processSurveyType(surveyType) {
    switch(surveyType) {
        case "regular":
            $('#survey_tutor_list_wrapper').css('visibility', 'hidden');
        break;
        case "tutor":
        if( __tutorListHtml == '' ) {
            $.ajax({
                url: admin_url+'coursebuilder/course_tutors',
                type: "POST",
                data:{ 
                    "course_id":__course_id, 
                    "tutor":true,
                    "is_ajax":true, 
                },
                success: function(response) {
                    var data  = $.parseJSON(response);
                    __tutorListHtml += '<option value="0">Choose Tutor</option>';
                    if( Object.keys(data['tutors']).length > 0 ) {
                        $.each(data['tutors'], function(tutorKey, tutor){
                            __tutorListHtml += '<option value="'+tutor['id']+'">'+tutor['us_name']+'</option>';
                        });
                    }
                    $('#survey_tutor_list').html(__tutorListHtml);
                    $('#survey_tutor_list_wrapper').css('visibility', 'visible');
                    $('#survey_tutor_list').val('0');
                }
            });
        } else {
            $('#survey_tutor_list_wrapper').css('visibility', 'visible');
            $('#survey_tutor_list').val('0');
        }
        break;
    }
}


function importContent()
{
    __lecture_selected      = new Array();
    __create_section_as_new = false;
   $('#import_section_list').html('');
   $('#popUpMessage').hide();
   $('#section_id_import_content').val('');
   $('#section_name_import_content').val('');
   $('#section_id_import_content').show();
   $('#section_name_import_content').hide();
   $('#create_new_section_import_content').show();
   $('#create_new_section_cancel_import_content').hide();
   $('#section_id_import_content').html(getSectionsOptionHtml()); 
   $('#course_id_import_content').html(getCoursesOptionHtml()); 
}

function importContentConfirm()
{
    
    var course_id                       = $('#course_id_import_content').val();
    var section_id                      = $('#section_id_import_content').val();
    var section_name                    = $('#section_name_import_content').val().trim();
    section_name                        = section_name.replace(/["<>{}]/g, '');
    section_name                        = section_name.trim();
    var sent_mail_on_import_creation    = $('#sent_mail_on_import_creation').prop('checked');
        sent_mail_on_import_creation    = (sent_mail_on_import_creation==true)?'1':'0';
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( __lecture_selected.length == 0)
    {
        errorCount++;
        errorMessage += 'please choose atleast one lecture to import <br />';
    }

    if(__create_section_as_new == true && section_name == '')
    {
        errorCount++;
        errorMessage += 'Section required <br />';
    }

    

    if(__create_section_as_new == false && section_id == '')
    {
        errorCount++;
        errorMessage += 'please choose section <br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#importContent .modal-body').prepend(renderPopUpMessage('error', errorMessage));   
        return false;
    }
    
    if(__create_section_as_new==true)
    {
        section_name   = section_name;                    
        section_id     = '';
    }
    else
    {
        section_name   = '';
        section_id     = section_id;                    
    }
    
    $.ajax({
        url: admin_url+'coursebuilder/import_lecture',
        type: "POST",
        data:{ "is_ajax":true, "course_id":__course_id, 'sent_mail_on_import_creation':sent_mail_on_import_creation, 'lecture_selected':JSON.stringify(__lecture_selected), 'section_id':section_id, 'section_name':section_name},
        beforeSend: function() {
            $("#importcontent_btn").text('SAVING...');
            $("#importcontent_btn").attr('disabled','disabled');
         },
        success: function(response) {
            var data            = $.parseJSON(response);
            var lecture_icons   = data['lecture_icons'];
            if(data['error'] == "false")
            {
                if( section_id == '' ) 
                {
                    var sectionHtml = ''; 
                        sectionHtml += '<li class="section" id="section_wrapper_'+data['section']['id']+'" data-section-name="'+section_name+'">';
                        sectionHtml += '    <div class="section-title-holder d-flex justify-between">';
                        sectionHtml += '      <div class="section-title">';
                        sectionHtml += '        <div class="drager">';
                        sectionHtml += '            <img src="'+assets_url+'images/drager.png">';
                        sectionHtml += '        </div>';
                        sectionHtml += '        <div class="section-counter"></div>';
                        sectionHtml += '        <span class="section-name" id="section_name_'+data['section']['id']+'"> '+section_name+'</span>';
                        sectionHtml += '      </div>';
                        sectionHtml += '      <div class="lecture-action-holder d-flex align-center">';
                        var status_class = 'Inactive';
                        var status_label = 'inactive';
                        sectionHtml += '        <div id="section_status_wraper_'+data['section']['id']+'" class="'+status_class+'-section"><i class="icon icon-ok-circled"></i><span class="ap_cont" id="section_status_text_'+data['section']['id']+'">'+lang(status_label)+'</span></div>';
                        sectionHtml += '        <div class="btn-group section-control">';
                        sectionHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                        sectionHtml += '                <span class="label-text">';
                        sectionHtml += '                    <i class="icon icon-down-arrow"></i>';
                        sectionHtml += '                </span>';
                        sectionHtml += '            <span class="tilder"></span>';
                        sectionHtml += '            </span>';
                        sectionHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                        
                        sectionHtml += '                <li><a href="#" data-toggle="modal" data-target="#edit_section" onclick="renameSection('+data['section']['id']+')">Edit</a></li>';
                        sectionHtml += '                <li id="section_action_status_'+data['section']['id']+'"><a href="#"  onclick="changeSectionStatus(\'0\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['section']['id']+'\')">'+lang('activate_all')+'</a></li>';
                        sectionHtml += '                <li id="section_action_status_'+data['section']['id']+'"><a href="#"  onclick="changeSectionStatus(\'1\', \''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['section']['id']+'\')">'+lang('deactivate_all')+'</a></li>'
                        sectionHtml += '                <li><a href="#"  onclick="deleteSection(\''+btoa(unescape(encodeURIComponent(section_name)))+'\', \''+data['section']['id']+'\')">Delete</a></li>';
                        //sectionHtml += '                <li>';
                        //sectionHtml += '                    <a href="#" data-toggle="modal" data-target="#rename_section" onclick="renameSection(\''+data['section']['id']+'\')">'+lang('rename')+'</a>';
                        //sectionHtml += '                </li>';
                        //sectionHtml += '                <li>';
                        //sectionHtml += '                    <a href="#" data-toggle="modal" data-target="#deleteSection" onclick="deleteSection(\''+btoa(section_name)+'\', \''+data['section']['id']+'\')">'+lang('delete')+'</a>';
                        //sectionHtml += '                </li>';
                        //sectionHtml += '                <li id="section_action_status_'+data['section']['id']+'">';
                        //sectionHtml += '                    <a href="#" data-target="#Deactivate" data-toggle="modal" onclick="changeSectionStatus(\'0\', \''+btoa(section_name)+'\', \''+data['section']['id']+'\')">'+lang('inactive')+'</a>';
                        //sectionHtml += '                </li>';
                        sectionHtml += '            </ul>';
                        sectionHtml += '        </div>';
                        sectionHtml += '      </div>';
                        sectionHtml += '    </div>';
                        sectionHtml += '    <ul class="lecture-wrapper" id="section_lecture_'+data['section']['id']+'">';
                        sectionHtml += '    </ul>';
                        sectionHtml += '</li>';
                    $('#sortable').append(sectionHtml);
                    __total_sections = parseInt(__total_sections+1);
                }
                if( data['lectures'].length > 0 )
                {
                    var status_class = 'active-section';
                    var status_label = 'inactive';
                    var lecturesHtml = '';
                    for (var i=0; i< data['lectures'].length ; i++)
                    {
                        status_class                = parseInt(data['lectures'][i]['cl_status'])==1?'active-section':'Inactive-section';
                        status_label                = parseInt(data['lectures'][i]['cl_status'])==1?'active':'inactive';

                         lectureUrl                 = admin_url+'coursebuilder/lecture/'+data['lectures'][i]['id'];
                         lectureStatus              = lang(status_label);
                         tickIconShowHide           = '<i class="icon icon-ok-circled">';
                         styleImport                = '';
                         labelSstyle                = '';
                         fileCopyFailed             = false;
                         actionList                 = '';
                        
                        if( data['lectures'][i]['cl_conversion_status'] == '6' ) {
                        
                            lectureUrl              = 'javascript:void(0)';
                            lectureStatus           = 'File Copy On Progress.......';
                            tickIconShowHide        = '';
                            styleImport             = 'style="display:none;"';
                            labelSstyle             = 'style="color: #51b957;font-style: normal;"';
                        }else if(data['lectures'][i]['cl_conversion_status'] == '7') {
                           lectureUrl              = 'javascript:void(0)';
                            lectureStatus           = 'File Copy Failed.......';
                            tickIconShowHide        = '';
                            styleImport             = 'style="display:none;"';
                            labelSstyle             = 'style="color: #e45a57;font-style: normal;"';
                            fileCopyFailed         = true;
                        }  
                        
                        if(fileCopyFailed){

                            var actionList      = ` <li>
                                                        <a onclick="reInitializeCopy({'lectureId':${data['lectures'][i]['id']}, 'copy_queue_id':${data['lectures'][i]['cl_copy_queue_id']}})" href="javascript:void(0)">Re-initialize</a>
                                                    </li>
                                                    <li>
                                                        <a href="${admin_url}coursebuilder/lecture/${data['lectures'][i]['id']}">${lang('settings')}</a>
                                                    </li>
                                                    <li>
                                                        <a onclick="deleteLecture('${btoa(unescape(encodeURIComponent(data['lectures'][i]['cl_lecture_name'])))}', '${data['lectures'][i]['id']}')"  href="#">${lang('delete')}</a>
                                                    </li>`;                         
                        } else {
                            var actionList      = ` <li id="lecture_action_status_${data['lectures'][i]['id']}">
                                                        <a onclick="changeLectureStatus('0', '${btoa(unescape(encodeURIComponent(data['lectures'][i]['cl_lecture_name'])))}', '${data['lectures'][i]['id']}')"  href="javascript:void(0)">${lang('activate')}</a>
                                                    </li>
                                                    <li>
                                                        <a href="${admin_url}coursebuilder/report/${data['lectures'][i]['id']}">${lang('report')}</a>
                                                    </li>
                                                    <li>
                                                        <a href="${admin_url}coursebuilder/lecture/${data['lectures'][i]['id']}">${lang('settings')}</a>
                                                    </li>
                                                    <li>
                                                        <a onclick="deleteLecture('${btoa(unescape(encodeURIComponent(data['lectures'][i]['cl_lecture_name'])))}', '${data['lectures'][i]['id']}')"  href="#">${lang('delete')}</a>
                                                    </li>`;
                        }


                        lecturesHtml        += `<li id="lecture_id_${data['lectures'][i]['id']}">
                                                    <div class="lecture-hold">
                                                        <div class="lecture-counter"></div>
                                                        <div class="drager ui-sortable-handle">
                                                        <img src="${assets_url}images/drager.png">
                                                    </div>
                                                    <div class="d-flex justify-between" style="width:100%;">
                                                        <a class="lecture-innerclick" href="${lectureUrl}">
                                                            <span class="lecture-icon ${lecture_icons[data['lectures'][i]['cl_lecture_type']]['parent']}">
                                                                <i class="icon ${lecture_icons[data['lectures'][i]['cl_lecture_type']]['child']}"></i>
                                                            </span>
                                                            <span class="lecture-name">${data['lectures'][i]['cl_lecture_name']}</span>
                                                        </a>
                                                        <div class="d-flex align-center">
                                                            <div id="lecture_status_wraper_${data['lectures'][i]['id']}" class="${status_class}">${tickIconShowHide}</i><span class="ap_cont lecture-group" id="lecture_status_text_${data['lectures'][i]['id']}" ${labelSstyle}>${lectureStatus}</span></div>
                                                                <div id="toggleOptions_${data['lectures'][i]['id']}" class="btn-group lecture-control" ${styleImport}>
                                                                    <span data-toggle="dropdown" class="dropdown-tigger">
                                                                        <span class="label-text">
                                                                            <i class="icon icon-down-arrow"></i>
                                                                        </span>
                                                                        <span class="tilder"></span>
                                                                    </span>
                                                                    <ul role="menu" class="dropdown-menu pull-right">
                                                                      ${actionList} 
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>`;
                    }
                    $('#section_lecture_'+data['section']['id']).html(lecturesHtml);
                    __total_lecture     = parseInt(__total_lecture+__lecture_selected.length);
                }
                $('#section_lecture_count').html(__total_sections+' '+lang('sections')+' - '+__total_lecture+' '+lang('lessons') )
                __lecture_selected  = new Array();
                $('.lecture-checkbox').prop('checked', false);
                $('#importContent').modal('hide');
            }
            else
            {
                cleanPopUpMessage();
                $('#importContent .modal-body').prepend(renderPopUpMessage('error', data['message']));
            }
            $("#importcontent_btn").text('IMPORT');
            $("#importcontent_btn").attr('disabled',false); 
        }
        
    });   
}
