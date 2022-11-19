$(document).on('click', '#basic-addon2', function(){
    getExpertLectures();
});
$(document).on('keyup', '#expert_lecture_keyword', function(){
    getExpertLectures();
});

var __expert_lectures_selected   = new Array();
var __filter_dropdown = '';
function getExpertLectures()
{
    var keyword  = $('#expert_lecture_keyword').val();
    $.ajax({
        url: admin_url+'expert_lectures/expert_lectures_json',
        type: "POST",
        data:{"is_ajax":true, "filter":__filter_dropdown, "keyword":keyword},
        success: function(response) {
            var data_expert = $.parseJSON(response);
            console.log(data_expert['expert_lectures'].length);
            
            if(data_expert['expert_lectures'].length > 0){
                $('#expert_lectures_row_wrapper').html(renderVideoHtml(response));
            }else{
                $('#expert_lectures_row_wrapper').html(renderPopUpMessage('error', 'No expert lectures found.'));
            }
            __expert_lectures_selected = new Array();
            $('.expert-lecture-checkbox-parent').prop('checked', false);
        }
    });
}

$(document).on('click', '.expert-lecture-checkbox', function(){
    var expert_lecture_id = $(this).val();
    if ($('.expert-lecture-checkbox:checked').length == $('.expert-lecture-checkbox').length) {
        $('.expert-lecture-checkbox-parent').prop('checked',true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        __expert_lectures_selected.push(expert_lecture_id);
    }else{
        $('.expert-lecture-checkbox-parent').prop('checked',false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(__expert_lectures_selected, expert_lecture_id);
    }
    if(__expert_lectures_selected.length > 0){
        $("#selected_expert_lecture_count").html(' ('+__expert_lectures_selected.length+')');
    }else{
        $("#selected_expert_lecture_count").html(''); 
    }

    if(__expert_lectures_selected.length > 1){
        $("#expert_bulk").css('display','block');
    }else{
        $("#expert_bulk").css('display','none');
    }
});

$(document).on('click', '.expert-lecture-checkbox-parent', function(){
    var parent_check_box = this;
    __expert_lectures_selected = new Array();    
    $( '.expert-lecture-checkbox' ).prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $( '.expert-lecture-checkbox' ).each(function( index ) {
           __expert_lectures_selected.push($( this ).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if(__expert_lectures_selected.length > 0){
        $("#selected_expert_lecture_count").html(' ('+__expert_lectures_selected.length+')');
    }else{
        $("#selected_expert_lecture_count").html(''); 
    }

    if(__expert_lectures_selected.length > 0){
        $("#expert_bulk").css('display','block');
    }else{
        $("#expert_bulk").css('display','none');
    }
});

function filter_video_by(filter)
{
    if( filter == 'all' ){
        $('#expert_lecture_keyword').val('');
        $('#filter_dropdown_text').html(lang('expert_lectures')+' <span class="caret"></span>');
    }
   __filter_dropdown        = filter;
   $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
   getExpertLectures();
}

function createExpertLecture(header_text, label)
{
    $('#expert_lecture_name').val('');
    $('#popUpMessage').hide();
    $('#create_box_title').html(header_text);
    $('#create_box_label').html(label);
    $('#create_box_ok').unbind();
    $('#create_box_ok').click({}, createExpertLectureConfirmed);    
}

function createExpertLectureConfirmed()
{
    var expert_name      = $('#expert_lecture_name').val();
        expert_name      = expert_name.replace(/["<>{}]/g, '');
        expert_name      = expert_name.trim();
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( expert_name == '')
    {
        errorCount++;
        errorMessage += 'please enter Expert lecture name <br />';
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'expert_lectures/create_expert_lecture',
            type: "POST",
            data:{"is_ajax":true, 'expert_name':expert_name},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'expert_lectures/basics/'+data['id'];
                }
                else
                {
                    $('#create_expert_lecture .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create_expert_lecture .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}

function changeVideoStatus(expert_lecture_id, header_text, message, button_text)
{ 
    $('#activate_expert_lecture #confirm_box_title').html(atob(header_text));
    $('#activate_expert_lecture #confirm_box_content').html(message);
    $('#activate_expert_lecture #confirm_box_ok').html(button_text);
    $('#activate_expert_lecture #confirm_box_ok').unbind();
    $('#activate_expert_lecture #confirm_box_ok').click({"expert_lecture_id": expert_lecture_id,"statmess": button_text}, changeStatusConfirmed);
    cleanPopUpMessage();    
}

function changeStatusConfirmed(params){
    $.ajax({
        url: admin_url+'expert_lectures/change_status',
        type: "POST",
        data:{"expert_lecture_id":params.data.expert_lecture_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#action_class_'+params.data.expert_lecture_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_'+params.data.expert_lecture_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_'+params.data.expert_lecture_id).html(data['action_list']);
                $('#activate_expert_lecture').modal('hide');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Expert lecture '+params.data.statmess+'d successfully'));
                scrollToTopOfPage();
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function changeExpertLectureStatusBulk(header_text, status, button_text)
{
    if(__expert_lectures_selected.length > 0 && $('.item-deleted:checked').length == 0 )
    {
        $('#activate_expert_lecture').modal('show');
    }
    if($('.item-deleted:checked').length > 0)
    {
        $('#notify_deleted_expert').modal('show');
    }
    $('#activate_expert_lecture #confirm_box_title').html(header_text);
    $('#activate_expert_lecture #confirm_box_ok').html(status);
    $('#activate_expert_lecture #confirm_box_ok').html(button_text);
    $('#activate_expert_lecture #confirm_box_ok').unbind();
    $('#activate_expert_lecture #confirm_box_ok').click({'status':status,'message':button_text}, ChangeVideoBulkConfirmed);  
    cleanPopUpMessage();  
}

function ChangeVideoBulkConfirmed(params){
    $.ajax({
        url: admin_url+'expert_lectures/change_status_bulk',
        type: "POST",
        data:{"expert_lectures":JSON.stringify(__expert_lectures_selected), "status":params.data.status, "is_ajax":true},
        success: function(response) {
            $('#expert_lectures_row_wrapper').html(renderVideoHtml(response));
            $('.expert-lecture-checkbox-parent, .expert-lecture-checkbox').prop('checked', false);
            $('#activate_expert_lecture').modal('hide');
            $('#show_message_div').prepend(renderPopUpMessage('success', 'Expert lectures '+params.data.message+'d successfully'));
            scrollToTopOfPage();
        }
    });
}

function deleteVideo(expert_lecture_id, header_text, button_text)
{
    $('#activate_expert_lecture #confirm_box_title').html(atob(header_text));
    $('#activate_expert_lecture #confirm_box_content').html('');
    $('#activate_expert_lecture #confirm_box_ok').html(button_text);
    $('#activate_expert_lecture #confirm_box_ok').unbind();
    $('#activate_expert_lecture #confirm_box_ok').click({"expert_lecture_id": expert_lecture_id}, deleteVideoConfirmed); 
    cleanPopUpMessage();       
}

function deleteVideoConfirmed(params){
    $.ajax({
        url: admin_url+'expert_lectures/delete',
        type: "POST",
        data:{"expert_lecture_id":params.data.expert_lecture_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#expert_lecture_action_'+params.data.expert_lecture_id).html(data['action_list']);
                $('#expert_lecture_details_'+params.data.expert_lecture_id).addClass('item-deleted');
                $('#action_class_'+params.data.expert_lecture_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass('label-danger').html(data['actions']['action']);
                $('#label_class_'+params.data.expert_lecture_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass('spn-delete').html(data['actions']['label_text']);
                $('#activate_expert_lecture').modal('hide');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Expert lecture deleted successfully'));
                scrollToTopOfPage();
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function deleteExpertLectureBulk(header_text, status, button_text)
{
    if(__expert_lectures_selected.length > 0 )
    {
        $('#activate_expert_lecture').modal('show');
    }
    $('#activate_expert_lecture #confirm_box_title').html(header_text);
    $('#activate_expert_lecture #confirm_box_ok').html(button_text);
    $('#activate_expert_lecture #confirm_box_ok').unbind();
    $('#activate_expert_lecture #confirm_box_ok').click({}, deleteVideoBulkConfirmed); 
    cleanPopUpMessage();   
}

function deleteVideoBulkConfirmed(params){
    $.ajax({
        url: admin_url+'expert_lectures/delete_video_bulk',
        type: "POST",
        data:{"expert_lectures":JSON.stringify(__expert_lectures_selected), "is_ajax":true},
        success: function(response) {
            __expert_lectures_selected = new Array();
            $('#expert_lectures_row_wrapper').html(renderVideoHtml(response));
            $('.expert-lecture-checkbox-parent').prop('checked', false);
            $('#activate_expert_lecture').modal('hide');
            $('#show_message_div').prepend(renderPopUpMessage('success', 'Expert lectures deleted successfully'));
            scrollToTopOfPage();
        }
    });
}

function restoreVideo(expert_lecture_id, header_text, button_text)
{
    $('#activate_expert_lecture #confirm_box_title').html(atob(header_text));
    $('#activate_expert_lecture #confirm_box_content').html('');
    $('#activate_expert_lecture #confirm_box_ok').html(button_text);
    $('#activate_expert_lecture #confirm_box_ok').unbind();
    $('#activate_expert_lecture #confirm_box_ok').click({"expert_lecture_id": expert_lecture_id}, restoreVideoConfirmed); 
    cleanPopUpMessage();         
}

function restoreVideoConfirmed(params){
    $.ajax({
        url: admin_url+'expert_lectures/restore',
        type: "POST",
        data:{"expert_lecture_id":params.data.expert_lecture_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#expert_lecture_action_'+params.data.expert_lecture_id).html(data['action_list']);
                $('#expert_lecture_details_'+params.data.expert_lecture_id).removeClass('item-deleted');
                $('#action_class_'+params.data.expert_lecture_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_'+params.data.expert_lecture_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#activate_expert_lecture').modal('hide');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Expert lecture restored successfully'));
                scrollToTopOfPage();
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function renderVideoHtml(response)
{
    $("#selected_expert_lecture_count").html(''); 
    var data        = $.parseJSON(response);
    console.log(data);
    var expertHtml  = '';
    if(data['expert_lectures'].length > 0 )
    {
        for (var i=0; i<data['expert_lectures'].length; i++)
        {
            //set the database value
            var action_label   = data['expert_lectures'][i]['wa_name'];
            var action         = data['expert_lectures'][i]['wa_name'];
            var action_date    = data['expert_lectures'][i]['updated_date'];
                action_date    = new Date(action_date.replace(/-/g, '/'));
                action_date    = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
            var action_author  = (data['expert_lectures'][i]['wa_name_author']!='')?data['expert_lectures'][i]['wa_name_author']:'Admin';

            //consider the record is deleted and set the value if record deleted
            var label_class    = 'spn-delete';
            var action_class   = 'label-danger';
            var item_deleted   = 'item-deleted'; 
            var item_inactive  = '' ;
            //case if record is not deleted
            if(data['expert_lectures'][i]['el_deleted'] == 0)
            {
                item_deleted = '';
                if(data['expert_lectures'][i]['action_id'] == 1)
                {
                    action_class     = 'label-warning';   
                    item_inactive    = 'item_inactive';   
                    var action_date  = data['expert_lectures'][i]['created_date'];
                        action_date  = new Date(action_date.replace(/-/g, '/'));
                        action_date  = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
                    label_class      = 'spn-inactive';
                }
                else
                {
                    if(data['expert_lectures'][i]['el_status'] == 1)
                    {
                        action_class   = 'label-success';                                                                
                        label_class    = 'spn-active';                                        
                        action         = lang('active');
                    }
                    else
                    {
                        action_class   = 'label-warning';                                                                
                        label_class    = 'spn-inactive';                                        
                        action         = lang('inactive');
                        item_inactive  = 'item_inactive';
                    }
                }
            }
            
            expertHtml += '<div class="rTableRow" id="expert_lectures_row_'+data['expert_lectures'][i]['id']+'" data-name="'+data['expert_lectures'][i]['el_title']+'">';
            expertHtml += '    <div class="rTableCell"> ';
            expertHtml += '        <input type="checkbox" class="expert-lecture-checkbox '+item_deleted+' '+item_inactive+'" value="'+data['expert_lectures'][i]['id']+'" id="expert_lecture_details_'+data['expert_lectures'][i]['id']+'"> ';
            expertHtml += '        <span class="icon-wrap-round">';
            var iconLetter = data['expert_lectures'][i]['el_title'].split(' ').join('').substr(0, 1);
            expertHtml += '            <small class="icon-custom">'+iconLetter.toUpperCase()+'</small>';
            expertHtml += '        </span>';
            expertHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
            expertHtml += '            <div class="ellipsis-style" style="font-size:14px">';
            if(data['expert_lectures'][i]['el_deleted'] != '1'){
            expertHtml += '                <a href="'+admin_url+'expert_lectures/basics/'+data['expert_lectures'][i]['id']+'" >';
            }
            expertHtml += '                 '+data['expert_lectures'][i]['el_title']+'';
            if(data['expert_lectures'][i]['el_deleted'] != '1'){
            expertHtml += '                </a> <br>';
            }
            expertHtml += '            </div>';
            expertHtml += '        </span>';
            expertHtml += '    </div>';

            

            expertHtml += '    <div class="rTableCell pad0">';
            expertHtml += '        <div class="col-sm-12 pad0">';
            expertHtml += '            <label class="pull-right label '+action_class+'" id="action_class_'+data['expert_lectures'][i]['id']+'">';
            expertHtml +=               action;
            expertHtml += '            </label>';
            expertHtml += '        </div>';


            expertHtml += '        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   ';
            expertHtml += '            <span class="pull-right '+label_class+'" id="label_class_'+data['expert_lectures'][i]['id']+'"> '+action_label+' by- '+action_author+' on '+action_date+'</span>';
            expertHtml += '        </div>';
            expertHtml += '    </div>';

            expertHtml += '    <div class="td-dropdown rTableCell">';
            expertHtml += '        <div class="btn-group lecture-control">';
            expertHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            expertHtml += '                <span class="label-text">';
            expertHtml += '                  <i class="icon icon-down-arrow"></i>';
            expertHtml += '                </span>';
            expertHtml += '                <span class="tilder"></span>';
            expertHtml += '            </span>';
            expertHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="expert_lecture_action_'+data['expert_lectures'][i]['id']+'">';
            if(data['expert_lectures'][i]['el_deleted'] == 0){
                var cb_status = (data['expert_lectures'][i]['el_status']==1)?'deactivate':'activate'; 
                var cb_action = cb_status; 
                expertHtml += '                <li id="status_btn_'+data['expert_lectures'][i]['id']+'">';
                expertHtml += '                        <a href="javascript:void(0);" data-toggle="modal" onclick="changeVideoStatus(\''+data['expert_lectures'][i]['id']+'\', \''+btoa(lang('are_you_sure_to')+' '+cb_action+' '+lang('expert_lecture')+' - '+data['expert_lectures'][i]['el_title']+' ?')+'\', \''+lang(cb_action)+'\',\''+lang(cb_status)+'\')" data-target="#activate_expert_lecture">'+lang(cb_status)+'</a>';
                expertHtml += '                </li>';
                expertHtml += '                <li>';
                expertHtml += '                       <a href="'+admin_url+'expert_lectures/basics/'+data['expert_lectures'][i]['id']+'" >'+lang('settings')+'</a>';
                expertHtml += '                </li>';
                expertHtml += '                <li>';
                expertHtml += '                       <a href="javascript:void(0);" id="delete_btn_'+data['expert_lectures'][i]['id']+'" data-toggle="modal" onclick="deleteVideo(\''+ data['expert_lectures'][i]['id']+'\', \''+btoa(lang('are_you_sure_to')+' '+lang('delete')+' - '+data['expert_lectures'][i]['el_title']+' ?')+' \',\''+lang('delete')+'\')" data-target="#activate_expert_lecture">'+lang('delete')+'</a>';
                expertHtml += '                </li>';
            }
            else
            {
                expertHtml += '                    <li>';
                expertHtml += '                        <a href="javascript:void(0);" id="restore_btn_'+data['expert_lectures'][i]['id']+'" data-toggle="modal" onclick="restoreVideo(\''+data['expert_lectures'][i]['id']+'\', \''+btoa(lang('are_you_sure_to')+' '+lang('restore')+' - '+data['expert_lectures'][i]['el_title']+' ?')+' \',\''+lang('restore')+'\')" data-target="#activate_expert_lecture">'+lang('restore')+'</a>';
                expertHtml += '                    </li>';
            }
            expertHtml += '           </ul>';
            expertHtml += '        </div>';
            expertHtml += '    </div>';
            expertHtml += '</div>';    
        }
    }
    else
    {

    }
    return expertHtml;
}