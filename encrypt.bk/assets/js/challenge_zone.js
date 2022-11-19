var __filter_dropdown   = '';
var __category_id       = '';
var __single_choice     = 1;
var __removed_options   = new Array();


//Get category list for Auto suggest
var current = '';
var timeOut = '';
$(document).on('keyup', '#q_category', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){ 
        var keyword = $('#q_category').val();
        var url 	= admin_url+'challenge_zone/get_question_category_list';
        var tagHTML	= '';
        current 	= this;
        if( keyword ){
            $.ajax({
                    url: url,
                    type: "POST",
                    data:{ 'q_category':keyword, 'challenge_category':__category_id, 'is_ajax':true},
                    success: function (response){
                        var data    = $.parseJSON(response);
                        if( data['tags'].length > 0 ){
                            for( var i = 0; i < data['tags'].length; i++){
                                tagHTML += '<li id="'+data['tags'][i]['id']+'">'+data['tags'][i]['qc_category_name']+'</li>';
                            }
                        }
                        $("#listing_question_category").html(tagHTML).show();       
                    },
            });
        }
    }, 600);
});

var current_li = '';
$(document).on('click', '.auto-search-lister li', function(){
    $('#q_category').val($(this).text());
    $(this).parent().html('').hide();
});

$(document).on('click', '#cancel_challenge', function(){
    $('#challenge_name').val('');
    $('#challenge_zone_category').val('0');
    $('#challenge_duration').val('');
    $('#challenge_pop_start_date').val('');
    $('#challenge_pop_start_time').val('');
    $('#challenge_pop_end_date').val('');
    $('#challenge_pop_end_time').val('');
});

var __canSaveChallenge = true;
function saveChallengeZone()
{
    var challenge_id                      = $('#challenge_id').val();
    var challenge_name                    = $('#challenge_name').val();
        challenge_name                    =  challenge_name.trim();
    var challenge_instruction             = $('#challenge_instruction').val();
    var challenge_category                = $('#challenge_zone_category').val();
    var challenge_duration                = $('#challenge_duration').val();
    var challenge_start_date              = $('#challenge_start_date').val();
    var challenge_start_time              = __start_time;
    var challenge_end_date                = $('#challenge_end_date').val();
    var challenge_end_time                = __end_time;
    var show_categories = $('#cz_show_categories').prop('checked');
        show_categories = (show_categories==true)?'1':'0';
        
    var errorCount                      = 0;
    var errorMessage                    = '';
    //end

    //validation process
    if( challenge_name == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge name <br />';
    }

    if( challenge_duration == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge duration <br />';
    }
    else
    {
        if(challenge_duration <= 0 || isNaN(challenge_duration))
        {
            errorCount++;
            errorMessage += 'Invalid challenge duration <br />';            
        }
    }
    
    if( challenge_instruction == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge content<br />';
    }
    
    if( challenge_category == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge category<br />';
    }
    
    if( challenge_start_date == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge start date<br />';
    }
    
    if( challenge_start_time == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge start time<br />';
    }
    
    if( challenge_end_date == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge end date<br />';
    }
    
    if( challenge_end_time == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge end time<br />';
    }
    
    if(__canSaveChallenge == false)
    {
        scrollToTopOfPage();
        return false;
    }
    if(challenge_start_time == challenge_end_time ){
        errorCount++;
        errorMessage += 'Challenge start-time and end-time should not be same<br />';
    }
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('#challenge_form').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    
    $.ajax({
        url: admin_url+'challenge_zone/save_challenge_detail',
        type: "POST",
        data:{ "is_ajax":true, 'show_categories':show_categories,  "challenge_id":challenge_id, "challenge_name":challenge_name, "challenge_instruction":challenge_instruction, 'challenge_category':challenge_category, 'challenge_start_date':challenge_start_date, 'challenge_start_time':challenge_start_time, 'challenge_end_date':challenge_end_date, 'challenge_end_time':challenge_end_time, 'challenge_duration':challenge_duration},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#challenge_message').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
            else
            {
                $('#challenge_message').prepend(renderPopUpMessage('success', data['message']));
                scrollToTopOfPage(); 
                //location.reload();
            }
        }
    });
}



var challenge_selected = new Array();
$(document).on('click', '.challenge-checkbox', function(){
    var challenge_id = $(this).val();
    if ($('.challenge-checkbox:checked').length == $('.challenge-checkbox').length) {
        $('.challenge-checkbox-parent').prop('checked',true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        challenge_selected.push(challenge_id);
    }else{
        $('.challenge-checkbox-parent').prop('checked',false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(challenge_selected, challenge_id);
    }
    if(challenge_selected.length > 0){
        $("#selected_challenge_count").html(' ('+challenge_selected.length+')');
    }else{
        $("#selected_challenge_count").html('');
    }
    
    if(challenge_selected.length > 1){
        $("#challenge_bulk").css('display','block');
    }else{
        $("#challenge_bulk").css('display','none');
    }
});

$(document).on('click', '.challenge-checkbox-parent', function(){
    var parent_check_box = this;
    challenge_selected = new Array();    
    $( '.challenge-checkbox' ).prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $( '.challenge-checkbox' ).each(function( index ) {
           challenge_selected.push($( this ).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if(challenge_selected.length > 0){
        $("#selected_challenge_count").html(' ('+challenge_selected.length+')');
    }else{
        $("#selected_challenge_count").html(''); 
    }
    
    if(challenge_selected.length > 1){
        $("#challenge_bulk").css('display','block');
    }else{
        $("#challenge_bulk").css('display','none');
    }
});



$(document).on('click', '#add-more-option', function(){
    var total_options = $('#question_option_wrapper .option-element').length;
        total_options++;
    var question_type = $('#question_type').val();
    if( question_type == '' )
    {
        alert('choose question type');
        return false;
    }
    var optionHtml  = '';
        optionHtml += '<div id="option_wrapper_'+total_options+'" class="col-sm-12 option-element">';
        optionHtml += '    <span class="order-option">Option '+total_options+' :</span>';
        optionHtml += '    <div class="input-group">';
    var input_type = 'checkbox';
    var answer     = '['+total_options+']';                                     
        if( question_type == __single_choice )
        {
            input_type = 'radio';
            answer     = '';
        }
        optionHtml += '        <span class="input-group-addon option-type">';
        optionHtml += '            <input class="question-option-input-new" type="'+input_type+'" name="answer_new'+answer+'" value="'+total_options+'">';
        optionHtml += '        </span>';
         optionHtml += '       <textarea id="new_option_textarea_'+total_options+'" name="option_new['+total_options+']" class="form-control question-text-input" rows="4" ></textarea>';
        optionHtml += '        <span class="remove-cross remove-new-option"> X </span>';
        optionHtml += '    </div>';
        optionHtml += '</div>';
        $( optionHtml ).insertBefore( "#add_option_button" );
		$('#new_option_textarea_'+total_options).redactor({
                minHeight: 150,
                maxHeight: 150,
                limiter:20,
                imageUpload: admin_url+'configuration/redactore_image_upload',
       		plugins: [],
                buttonsHide: ['format', 'italic','deleted', 'lists','link','line'],
                callbacks: {
                    imageUploadError: function(json, xhr)
                    {
                         var erorFileMsg = "This file type is not allowed. upload a valid image.";
                         $('#challenge_form').prepend(renderPopUpMessage('error', erorFileMsg));
                         scrollToTopOfPage();
                         return false;
                    }
                } 
    	});
});

$(document).on('click', '.remove-existing-option', function(){
    var question_id = $(this).parents('.option-element').attr('id');
    __removed_options.push($('#'+question_id).attr('data-id'));
    $('#'+question_id).remove();
    
    $('span.order-option').text(function (i) {

        // returning the sum of i + 1 to compensate for
        // JavaScript's zero-indexing:
        return 'Option '+(i + 1)+':'; 
    });
});

$(document).on('click', '.remove-new-option', function(){
    $(this).parents('.option-element').remove();
    $('span.order-option').text(function (i) {

        // returning the sum of i + 1 to compensate for
        // JavaScript's zero-indexing:
        return 'Option '+(i + 1)+':';
    });
});

$(document).on('change', '#question_type', function(){
    var question_type = $('#question_type').val();
    $('.question-option-input, .question-option-input-new').prop('checked', false);
	$('.option-element, #add_option_button').show();
	switch (question_type)
	{
		case '1':
			$('.question-option-input').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer".substr(0, 6));
			});
			$('.question-option-input-new').each(function(){
					$(this).attr('type', 'radio').attr('name', "answer_new".substr(0, 10));
			});
		break;
		case '2':
			$('.question-option-input').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer["+$(this).val()+"]");        
			});
			$('.question-option-input-new').each(function(){
				$(this).attr('type', 'checkbox').attr('name', "answer_new["+$(this).val()+"]");        
			});
		break;
		case '3':
		$('.option-element, #add_option_button').hide();
		break;
	}
});

$(document).on('click', '.question-option-input, .question-option-input-new', function(){
    var question_type = $('#question_type').val();
    if( question_type == __single_choice )
    {
        $('.question-option-input, .question-option-input-new').prop('checked', false);
        $(this).prop('checked', true);
    }
});

$(document).on('change', '#upload_question', function(e){
    //console.log(e.currentTarget.files[0]);
    $('#percentage_bar').hide();
    var i                           = 0;
    __uploaded_files                = e.currentTarget.files[i];
    $('#upload_challenge_file').val(__uploaded_files['name']);
});

function uploadQuestion()
{
    if(__uploaded_files=='')
    {
        alert('choose file');return false;
    }
    $('#percentage_bar').show();
    var uploadURL                   = admin_url+'challenge_zone/upload_question'
    var param                       = new Array;
        param["file"]               = __uploaded_files;
        param["challenge_id"]         = __challenge_id;
    uploadFiles(uploadURL, param, uploadQuestionCompleted);    
}

function uploadQuestionCompleted(response)
{
    __uploaded_files = '';
    setTimeout(function(){window.location.reload();}, 500)
}

function deleteQuestion(question_id, challenge_id)
{
    $('#delete_message').hide();
    $('#delete_header_text').html(lang('are_you_sure_delete_this_question'));
    $('#deleteSection').modal('show');
    $('#delete_challenge_ok').unbind();
    $('#delete_challenge_ok').click({"question_id": question_id, "challenge_id": challenge_id}, deleteQuestionConfirmed); 
    //cleanPopUpMessage();   
}

function deleteQuestionConfirmed(param)
{
    $.ajax({
        url: admin_url+'challenge_zone/delete_challenge_question',
        type: "POST",
        data:{ "is_ajax":true, "question_id":param.data.question_id, "challenge_id": param.data.challenge_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] != "false")
            {                
                $('#delete_header_text').html(data['message']);
            }
            else
            {
               $('#question_wrapper_'+param.data.question_id).remove();
               $('#deleteSection').modal('hide');
               //$('#show_message_div').prepend(renderPopUpMessage('success', 'Challenge zone deleted successfully'));
               //scrollToTopOfPage();
            }
        }
    });
}

function saveQuestion()
{
    var errCount     = 0;
    var errMessage   = '';
    var question_type = $('#question_type').val();
    var total_options = $('#question_option_wrapper .option-element').length;
    
    if( total_options  < 2 && question_type != 3 )
    {
        errCount++;
        errMessage += 'please add atlease two options\n';
    }
	if( question_type != 3 )
	{
		$('.question-text-input').each(function(){
			if($(this).val()=='')
			{
				errMessage += 'options cannot be empty\n';   
				errCount++;
				return false;
			}
		});
	}
    var checked = 0;
    $('.question-option-input, .question-option-input-new').each(function(){
        if( $(this).prop('checked') == true )
        {
            checked++;
        }
    });
    
    if( checked == 0 && question_type != 3 )
    {
        errMessage += 'choose the answer\n';   
        errCount++;        
    }
    
    if( errCount > 0 )
    {
        alert(errMessage);
        return false;        
    }
    
    //$('#removed_options').val(JSON.stringify(__removed_options));
    $('#question_form').submit();
}

function activateChallenge(challenge_id, header_text)
{
    $('#challenge_confirm_box_title').html(atob(header_text));
    $('#challenge_confirm_box_content').html('');
    $('#challenge_confirm_box_ok').html('ACTIVATE');
    $('#challenge_confirm_box_ok').unbind();
    $('#challenge_confirm_box_ok').click({"challenge_id": challenge_id}, activateChallengeConfirmed);
    //cleanPopUpMessage();        
}

function activateChallengeConfirmed(params){
    $.ajax({
        url: admin_url+'challenge_zone/change_status', 
        type: "POST",
        data:{"challenge_id":params.data.challenge_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                /*$('#user_action_'+params.data.user_id).html(data['action_list']);
                $('#action_class_'+params.data.user_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass('label-danger').html(data['actions']['action']);
                $('#label_class_'+params.data.user_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass('spn-delete').html(data['actions']['label_text']);*/
                $('#challenge_delete').modal('hide');
                $('#activate_challenge_'+params.data.challenge_id).remove();
                $('#activate_challenge_label_'+params.data.challenge_id).html('<label class="pull-right label label-success">'+lang('active')+'</label>');
                $('#activate_challenge_modified_'+params.data.challenge_id).html(' <span class="pull-right spn-active" id="label_class_'+params.data.challenge_id+'"> Activated by- '+data['action_author']+' on '+data['action_date']+'</span>');
                $('#challenge_row_wrapper').prepend(renderPopUpMessage('success', data['message']));
                //location.reload();

            }
            else
            {
                $('#challenge_delete').modal('hide');
                lauch_common_message('Challenge zone', data['message']);
                $('#challenge_confirm_box_title').html(data['message']);
                $('#challenge_confirm_box_content').html('');
            }
        }
    });
}

function deleteChallenge(challenge_id, header_text)
{
    $('#challenge_confirm_box_title').html(atob(header_text));
    $('#challenge_confirm_box_content').html('');
    $('#challenge_confirm_box_ok').html('DELETE');
    $('#challenge_confirm_box_ok').unbind();
    $('#challenge_confirm_box_ok').click({"challenge_id": challenge_id}, deleteChallengeConfirmed);
    //cleanPopUpMessage();        
}

function deleteChallengeConfirmed(params){
    $.ajax({
        url: admin_url+'challenge_zone/delete',
        type: "POST",
        data:{"challenge_id":params.data.challenge_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                /*$('#user_action_'+params.data.user_id).html(data['action_list']);
                $('#action_class_'+params.data.user_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass('label-danger').html(data['actions']['action']);
                $('#label_class_'+params.data.user_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass('spn-delete').html(data['actions']['label_text']);*/
                $('#challenge_delete').modal('hide');
                location.reload();

            }
            else
            {
                $('#challenge_confirm_box_title').html(data['message']);
                $('#challenge_confirm_box_content').html('');
            }
        }
    });
}

function deleteChallengeZone(challenge_name, challenge_id)
{
    challenge_name = atob(challenge_name);
    $('#delete_header_text').html(lang('delete')+' '+lang('lecture')+' '+challenge_name);
    $('#delete_message').html(lang('are_you_sure')+' '+lang('you_want_to_delete')+' '+challenge_name);
    $('#delete_challenge_ok').html('DELETE');
    $('#delete_challenge_ok').unbind();
    $('#delete_challenge_ok').click({"challenge_id": challenge_id, 'is_ajax':'true'}, deleteChallengeZoneConfirmed);    
}

function deleteChallengeZoneConfirmed(param)
{
    $.ajax({
        url: admin_url+'challenge_zone/delete',
        type: "POST",
        data:{ "is_ajax":true, 'challenge_id':param.data.challenge_id}, 
        success: function(response) {
            $('#back_button').trigger('click');
        }
    });
}

function deleteChallengeZoneBulk(header_text)
{
    if(challenge_selected.length > 0 )
    {
        $('#challenge_delete').modal('show');
    }
    else
    {
        $('#challenge_validation').modal('show');
    }
    $('#challenge_confirm_box_title').html(header_text);
    $('#challenge_confirm_box_ok').html('DELETE');
    $('#challenge_confirm_box_ok').unbind();
    $('#challenge_confirm_box_ok').click({}, deleteChallengeZoneBulkConfirmed);    
}

function deleteChallengeZoneBulkConfirmed(params){
    $.ajax({
        url: admin_url+'challenge_zone/delete_challenge_bulk',
        type: "POST",
        data:{"challenges":JSON.stringify(challenge_selected), "is_ajax":true},
        success: function(response) {
            $('#challenge_row_wrapper').html(renderChallengeHtml(response));
            $('.challenge-checkbox-parent').prop('checked', false);
            $('#challenge_delete').modal('hide');
        }
    });
}

function getChallenges()
{
    $.ajax({
        url: admin_url+'challenge_zone/challenge_json',
        type: "POST",
        data:{"is_ajax":true, "category_id":__category_id},
        success: function(response) {
            var response_new = $.parseJSON(response);
            if(response_new['challenges'].length=='0'){
                $('#challenge_row_wrapper').html(renderPopUpMessage('error', 'No challenges available'));
                scrollToTopOfPage();
            }
            else{
                $('#challenge_row_wrapper').html(renderChallengeHtml(response));
            }
        }                        
                        

    });
}

function filter_category(category_id)
{
    cleanPopUpMessage();
   $('.challenge-checkbox-parent').prop('checked',false);
   __category_id        = category_id;
   $('#dropdown_text').html($('#dropdown_list_'+category_id).text()+'<span class="caret"></span>');
   getChallenges();
}

function createChallenge()
{
    var challenge_name          = $('#challenge_name').val();
        challenge_name          =  challenge_name.replace(/["<>{}]/g, '');
        challenge_name          =  challenge_name.trim();
    var challenge_category      = $('#challenge_zone_category').val();
    var challenge_duration      = $('#challenge_duration').val();
    var start_date              = $('#challenge_pop_start_date').val();
    var start_time              = __start_pop_time;
    var end_date                = $('#challenge_pop_end_date').val();
    var end_time                = __end_pop_time;
    var show_categories = $('#cz_show_categories').prop('checked');
        show_categories = (show_categories==true)?'1':'0';
    //console.log(start_date+"//"+start_time+"//"+end_date+"//"+end_time);
    var errorCount              = 0;
    var errorMessage            = '';
    //end

    //validation process
    if( challenge_name == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge name <br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }

    if( challenge_category == '' || challenge_category == '0')
    {
        errorCount++;
        errorMessage += 'please enter challenge category<br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }

    if( challenge_duration == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge duration <br />';
        cleanPopUpMessage();
        $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }   
    else
    {
        if(challenge_duration <= 0 || isNaN(challenge_duration))
        {
            errorCount++;
            errorMessage += 'Invalid challenge duration <br />';            
            cleanPopUpMessage();
            $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
            return false;
        }
    }


    if( start_date == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge start date<br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }
    
    if( start_time == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge start time<br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }
    
    if( end_date == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge end date<br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }
    
    if( end_date == '')
    {
        errorCount++;
        errorMessage += 'please enter challenge end time<br />';
        cleanPopUpMessage();
    $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        return false;
    }
    
    
    cleanPopUpMessage();
    if(errorCount > 0 )
    {
        $('.challenge_zone_form').html(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
        return false;
    }
    $.ajax({
        url: admin_url+'challenge_zone/create_challenge',
        type: "POST",
        data:{"is_ajax":true, 'show_categories':show_categories, 'challenge_name':challenge_name, 'challenge_category':challenge_category, 'challenge_duration':challenge_duration, 'start_date':start_date, 'start_time':start_time, 'end_date':end_date, 'end_time':end_time},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data.error == false)
            {
                window.location = admin_url+'challenge_zone/basics/'+data.id;
            }
            else
            {
                alert(data['message']);
            }
        }
    });
}

function renderChallengeHtml(response)
{
    $("#selected_challenge_count").html('');
    var data         = $.parseJSON(response);
    console.log(data);
    var catalogHtml  = '';
    if(data['challenges'].length > 0 )
    {
        for (var i=0; i<data['challenges'].length; i++)
        {
            
            //set the database value
            var action_label   = data['challenges'][i]['wa_name'];
            var action         = data['challenges'][i]['wa_name'];
            var action_date    = data['challenges'][i]['updated_date'];
                action_date    = new Date(action_date.replace(/-/g, '/'));
                action_date    = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
            var action_author  = (data['challenges'][i]['wa_name_author']!='')?data['challenges'][i]['wa_name_author']:'Admin';

            //consider the record is deleted and set the value if record deleted
            var label_class    = 'spn-delete';
            var action_class   = 'label-danger';
            var item_deleted   = 'item-deleted'; 
            var item_inactive  = '' ;
            //case if record is not deleted
            if(data['challenges'][i]['cz_deleted'] == 0)
            {
                item_deleted = '';
                if(data['challenges'][i]['action_id'] == 1)
                {
                    action_class     = 'label-warning';
                    item_inactive    = 'item_inactive';
                    var action_date  = data['challenges'][i]['created_date'];
                        action_date  = new Date(action_date.replace(/-/g, '/'));
                        action_date  = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
                    label_class      = 'spn-inactive';
                }
                else
                {
                    if(data['challenges'][i]['cz_status'] == 1)
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
                        item_inactive    = 'item_inactive';
                    }
                }
            }
            
            
            
            catalogHtml += '<div class="rTableRow" id="challenge_row_'+data['challenges'][i]['id']+'" data-title="'+data['challenges'][i]['cz_title']+'" >';
            catalogHtml += '    <div class="rTableCell cours-fix ellipsis-hidden"> ';
            catalogHtml += '        <div class="ellipsis-style">';  
            catalogHtml += '            <input type="checkbox" class="challenge-checkbox" value="'+data['challenges'][i]['id']+'" id="challenge_details_'+data['challenges'][i]['id']+'">'; 
            catalogHtml += '            <span class="icon-wrap-round">';
            var iconLetter = data['challenges'][i]['cz_title'].split(' ').join('').substr(0, 1);
            catalogHtml += '                <small class="icon-custom">'+iconLetter.toUpperCase()+'</small>';
            catalogHtml += '            </span>';
            catalogHtml += '            <a href="javascript:void(0);" class="cust-sm-6 padd0"> '+(data['challenges'][i]['cz_title'])+'</a>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';
            
            
            catalogHtml += '    <div class="rTableCell pad0">';
            catalogHtml += '        <div class="col-sm-12 pad0">';
            catalogHtml += '            <label class="pull-right label '+action_class+'" id="action_class_'+data['challenges'][i]['id']+'">';
            catalogHtml +=               action;
            catalogHtml += '            </label>';
            catalogHtml += '        </div>';


            catalogHtml += '        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   ';
            catalogHtml += '            <span class="pull-right '+label_class+'" id="label_class_'+data['challenges'][i]['id']+'"> '+action_label+' by- '+action_author+' on '+action_date+'</span>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';
            
            
            catalogHtml += '    <div class="td-dropdown rTableCell">';
            catalogHtml += '        <div class="btn-group challenge-control">';
            catalogHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            catalogHtml += '                 <span class="label-text">';
            catalogHtml += '                  <i class="icon icon-down-arrow"></i>';
            catalogHtml += '                </span>';
            catalogHtml += '                <span class="tilder"></span>';
            catalogHtml += '            </span>';
            catalogHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="challenge_action_'+data['challenges'][i]['id']+'">';
            if(data['challenges'][i]['cz_deleted'] == 0){
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="javascript:void(0);">'+lang('report')+'</a>';
            catalogHtml += '                    </li>';
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="'+admin_url+'challenge_zone/basics/'+data['challenges'][i]['id']+'">'+lang('settings')+'</a>'; 
            catalogHtml += '                    </li>';
            if(data['challenges'][i]['cz_status'] == 0){
            catalogHtml += '                    <li id="activate_challenge_'+data['challenges'][i]['id']+'">';
            catalogHtml += '                        <a href="javascript:void(0);" id="activate_btn_'+data['challenges'][i]['id']+'" data-toggle="modal" onclick="activateChallenge(\''+data['challenges'][i]['id']+'\', \''+btoa(lang('are_you_sure_to')+' '+lang('activate_challenge')+' - '+(data['challenges'][i]['cz_title'].replace(/'/g, "\\'"))+' ?')+'\')" data-target="#challenge_delete">'+lang('activate')+'</a>';
            catalogHtml += '                    </li>';
            }
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="javascript:void(0);" id="delete_btn_'+data['challenges'][i]['id']+'" data-toggle="modal" onclick="deleteChallenge(\''+data['challenges'][i]['id']+'\', \''+btoa(lang('are_you_sure_to')+' '+lang('delete_challenge')+' - '+(data['challenges'][i]['cz_title'].replace(/'/g, "\\'"))+' ?')+'\')" data-target="#challenge_delete">'+lang('delete')+'</a>';
            catalogHtml += '                    </li>';
            }
            catalogHtml += '            </ul>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';
            catalogHtml += '</div> ';
        }
    }
    else
    {

    }
    return catalogHtml;
}

$(document).on('change', '#challenge_pop_start_time',function(){
    var startTime = $(this).timepicker('getTime');
    var endTime = new Date(startTime.getTime() + 30*60000);   // add 30 minutes
    $('#challenge_pop_end_time').timepicker('option', { minTime: endTime });
})

if($('#challenge_start_time').length > 0 ){

    $(document).on('change', '#challenge_start_time',function(){
        var startTime = $(this).timepicker('getTime');
        var endTime = new Date(startTime.getTime() + 30*60000);   // add 30 minutes
        $('#challenge_end_time').timepicker('option', { minTime: endTime });
    })
}


    





