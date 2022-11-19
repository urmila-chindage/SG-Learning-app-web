var __user_path         = '';
var __img_format        = new Array('jpg', 'jpeg', 'png', 'gif');
var __upload_path       = '';
var __course_selected   = new Array();


    /*for profile*/
    function editBlock(blockId)
    {
        $('.field_label_display_'+blockId).hide();
        $('.field_label_form_'+blockId).show();
        $('#edit_block_'+blockId).hide();
        $('#block_action_'+blockId).show();
        $('.field-required-'+blockId).show();
        $('.field_values_list').html('');
        $('#block_row_'+blockId+' .field_values_current').each(function(index, value) {
            $('#'+$(this).attr('data-field-id')).val($(this).text());
        });
        $('.error-fields').removeClass('error-fields');
    }

    function cancelEdit(blockId)
    {
        $('.field_values_list').html('');
        $('.field_label_display_'+blockId).show();
        $('.field_label_form_'+blockId).hide();
        $('#edit_block_'+blockId).show();
        $('#block_action_'+blockId).hide();
        $('.field-required-'+blockId).hide();
    }

    function saveBLock(field_ids, btn_selector)
    {
        var blockId = btn_selector;
            blockId = btn_selector.split('_');
            blockId = blockId[2]
        if(field_ids!='')
        {
            var fields          = $.parseJSON(atob(field_ids));
            var fieldValues     = new Object;
            var errorMessage    = [];
            var errorCount      = 0;

            if(fields.length > 0 )
            {
                for(var i=0; i<fields.length; i++)
                {
                    fieldValues[fields[i]['field_name']] = $('#'+fields[i]['field_name']).val();
                    $('#'+fields[i]['field_name']).removeClass('error-fields');
                    if(fields[i]['field_mandatory'] == 1 && fieldValues[fields[i]['field_name']] == '')
                    {
                        errorMessage.push(fields[i]['field_label']+' cannot be empty');
                        errorCount++;
                    }
                }

                if (errorCount > 0)
                {
                    var messageObject = {
                        'body':errorMessage.join('<br />'),
                        'button_yes':'OK', 
                        'prevent_button_no':true
                    };
                    callback_danger_modal(messageObject);
                    return false;
                } 
                $('.field_values_list').html('');
                $('#save_block_'+blockId).text('Saving..');                                                
                $.ajax({
                    url: admin_url+'/user/save_profile_values',
                    type: "POST",
                    data:{ "is_ajax":true, 'profile_values':JSON.stringify(fieldValues), 'id':user_id,'user_name':user_name,'user_phone':phone_number},
                    success: function(response) {
                        var data = $.parseJSON(response);
                        $('#save_block_'+blockId).text('Save');                                                
                        if(data['error'] == false) {
                            for(var i=0; i<fields.length; i++) {
                                $('#field_value_'+fields[i]['id']+' .field_values_current').text(fieldValues[fields[i]['field_name']]);
                            }
                            $('.field_label_display_'+blockId).show();
                            $('.field_label_form_'+blockId).hide();
                            $('#edit_block_'+blockId).show();
                            $('#block_action_'+blockId).hide();
                            $('#save_block_'+blockId).text('Save');      
                            $('.field-required').hide();
                            $('.field_values_list').hide();
                            var messageObject = {
                                'body':data['message'],
                                'button_yes':'OK', 
                                'prevent_button_no':true
                            };
                            callback_success_modal(messageObject);
                        } else {
                            var messageObject = {
                                'body':data['message'],
                                'button_yes':'OK', 
                                'prevent_button_no':true
                            };
                            callback_danger_modal(messageObject);
                        }
                    }
                });    

            }
        }
    }
    /*End*/

    /*
    * To get the autofill values for the profile block dynamic fields
    * Created by : Neethu KP
    * Created at : 06/01/2017
    */
   var __timeOut = '';
   function getAutoFieldsValue(e){
        clearTimeout(__timeOut);
        __timeOut = setTimeout(function(){
            var AutosuggestionStatus   = $(e.target).attr('auto-suggestion-status');
            $('.field_values_list').hide();
            if(AutosuggestionStatus == 1){
                var userKeyword    = $(e.target).val();
                var fieldValueId   = $(e.target).attr('id');
                var field_id       = $(e.target).attr('data-pf-id');
                var fieldListId    = 'fieldListId-'+fieldValueId;
                var fieldHtml      = '<li>Loading...</li>';
                $('#'+fieldListId).html(fieldHtml).show();
                var keyword        = userKeyword.toLowerCase();
                $.ajax({
                    url: admin_url+'user/get_fileds_value',
                    type: "POST",
                    data:{"is_ajax":true, "keyword":keyword, "field_id":field_id},
                    success: function(response) {
                        var data        = $.parseJSON(response);
                        var fieldHtml    = '';
                        $('#'+fieldListId).html(fieldHtml);
                        if(data['field_values'].length > 0 )
                        {
                            for (var i=0; i<data['field_values'].length; i++)
                            {
                                fieldHtml += '<li id="'+data['field_values'][i]+'">'+data['field_values'][i]+'</li>' ;
                            }
                            $('#'+fieldListId).append(fieldHtml).show();

                        }
                    }
                });
            }          
        }, 600);
    }

    /*
    * To place the selected value from the auto suggestion list
    * Created by : Neethu KP
    * Created at : 06/01/2017
    */   
    $(document).on('click' , '.field_values_list li' ,function(){

        var fieldText     = $(this).text();
        var fieldListId   = $(this).parent().attr('id');
        var fieldValueId  = fieldListId.split('-');
        $('#'+fieldValueId[1]).val(fieldText);
        $('.field_values_list').hide();

    });

    $(document).on('click', '#my_about_edit', function(){
        var emailId = $.trim($('#email_id_wrapper').text());
        $('#email_id').val((emailId=='N/A')?'':emailId);
        var usName = $.trim($('#us_name_wrapper').text());
        $('#us_name').val((usName=='N/A')?'':usName);
        var phoneNumber = $.trim($('#phone_number_wrapper').text());
        $('#phone_number').val((phoneNumber=='N/A')?'':phoneNumber);
        $('#my_about, #my_about_edit, #email_id_wrapper, #email_id_label, #phone_number_wrapper, #phone_number_label, #us_name_wrapper, #us_name_label').hide();
        $('#my_about_form, #my_about_action, #email_id,#email_id_label, #phone_number,#phone_number_label, #us_name,#us_name_label').show();
    });
    
    $(document).on('click', '#my_about_cancel', function(){
        $('.field_values_list').html('');
        $('#my_about, #my_about_edit, #email_id_wrapper, #email_id_label, #phone_number_wrapper, #phone_number_label, #us_name_wrapper, #us_name_label').show();
        $('#my_about_form, #my_about_action, #email_id,#email_id_label, #phone_number,#phone_number_label, #us_name,#us_name_label').hide();
    });

    $(document).on('click', '#my_about_save', function(){
        var userEmail       = $.trim($('#email_id').val());
        var phoneNumber     = $.trim($('#phone_number').val());
        var usName          = $.trim($('#us_name').val());
        var errorCount      = '';
        var errorMessage    = '';
        if( userEmail == '' && registerNumber == '' ) {
            errorMessage = 'Both email id and username cannot be empty together <br/>';
            errorCount++;
        } else {
            if( userEmail != '' && validateEmail(userEmail) == false ) {
                errorMessage = 'Invalid email id <br/>';
                errorCount++;
            }
        }
        if(usName == '')
        {            
            errorMessage += 'Name cannot be empty <br/>';
            errorCount++;
        }
        if(phoneNumber == '')
        {            
            errorMessage += 'Phone Number cannot be empty <br/>';
            errorCount++;
        }
        else
        {
            if(phoneNumber.length!=10 || IsmobileNumber(phoneNumber) == false)
            {
                errorMessage += 'Phone Number is invalid <br/>';
                errorCount++;                
            }
        }

        if(errorCount > 0) {
            var messageObject = {
                'body':errorMessage,
                'button_yes':'OK', 
                'prevent_button_no':true
            };
            callback_danger_modal(messageObject);
        } else {
            user_email = userEmail;
            $('#my_about_save').text('Saving..');
            $.ajax({
                url: admin_url + 'user/save_profile_about',
                type: "POST",
                data: {"is_ajax": true, 'us_name':usName, 'id':user_id, 'phone_number': phoneNumber, 'email_id': user_email},
                success: function (response) {
                    var data = $.parseJSON(response);
                    $('#my_about_save').text('Save');
                    switch(data['status']) {
                        case 1:
                            $('#my_about, #my_about_edit, #email_id_wrapper, #email_id_label, #phone_number_wrapper, #phone_number_label, #us_name_wrapper, #us_name_label').show();
                            $('#my_about_form, #my_about_action, #email_id,#email_id_label, #phone_number,#phone_number_label, #us_name,#us_name_label').hide();
                            $('#email_id_wrapper span').text((user_email!='')?user_email:'N/A');
                            $('#us_name_wrapper span').text((usName!='')?usName:'N/A');
                            $('#profile_user_name').html(usName);
                            $('#phone_number_wrapper span').text((phoneNumber!='')?phoneNumber:'N/A');
                            var messageObject = {
                                'body':data['message'],
                                'button_yes':'OK', 
                            };
                            callback_success_modal(messageObject);
                            updateBulkAction();
                        break;
                        case 2:
                            var messageObject = {
                                'body':data['message'],
                                'button_yes':'OK', 
                                'prevent_button_no':true
                            };
                            callback_danger_modal(messageObject);
                        break;
                        case 3:
                            $('#my_about, #my_about_edit, #email_id_wrapper, #email_id_label, #phone_number_wrapper, #phone_number_label, #us_name_wrapper, #us_name_label').show();
                            $('#my_about_form, #my_about_action, #email_id,#email_id_label, #phone_number,#phone_number_label, #us_name,#us_name_label').hide();
                            $('#email_id_wrapper span').text((user_email!='')?user_email:'N/A');
                            $('#us_name_wrapper span').text((usName!='')?usName:'N/A');
                            $('#profile_user_name').html(usName);
                            $('#phone_number_wrapper span').text((phoneNumber!='')?phoneNumber:'N/A');
                            var messageObject = {
                                'body':data['message'],
                                'button_yes':'OK', 
                            };
                            callback_success_modal(messageObject);
                            updateBulkAction();
                        break;
                        
                    }
                }
            });
        }
    });

    function IsmobileNumber(Numbers){
        var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }


    $(document).on('click', '#send_user_message', function(){
        $('#redactor_send').redactor('insertion.set','');
        $('#send_message_subject').val('');
    });

    function send_message_user() {
        var send_user_subject = $.trim($('#send_message_subject').val());
        var send_user_message = $.trim($('#redactor_send').val()); 
        var errorCount        = 0;
        var errorMessage      = [];
        if( user_email == '' ) {
            errorMessage = 'Email Id cannot be empty';
            errorCount++;
        } else {
            if( validateEmail(user_email) == false ) {
                errorMessage.push('Invalid email id');
                errorCount++;
            }
        }
        
        if ( send_user_subject == '' ) {
            errorMessage.push('Subject cannot be empty');
            errorCount++;
        }
        if ( send_user_message == '' ) {
            errorMessage.push('Message cannot be empty');
            errorCount++;
        }
        if(errorCount>0) {
            var messageObject = {
                'body':errorMessage.join('<br />'),
                'button_yes':'OK', 
            };
            callback_danger_modal(messageObject);
        } else {
            $('#send_mail_button').html('SENDING..');
            $.ajax({
                url: admin_url+'user/send_message_user',
                type: "POST",
                data:{"is_ajax":true, "send_user_subject":send_user_subject, "send_user_message":btoa(send_user_message), "user_email":user_email, 'user_id': user_id},
                success: function(response) {
                    $('#send_mail_button').html('SEND');
                    $('#send-user-message').modal('hide');
                    var messageObject = {
                        'body':'Message send successfully',
                        'button_yes':'OK', 
                    };
                    setTimeout(() => {
                        callback_success_modal(messageObject);                        
                    }, 700);
                }
            });     
        }
    }

    function resetPassword(user_id)
    {
        var messageObject = {
            'body': lang('sure_to_reset_password'),
            'button_yes':'RESET', 
            'button_no':'CANCEL',
            'continue_params':{'user_id':user_id},
        };
        callback_warning_modal(messageObject, resetPasswordConfirmed);    
    }
    
    function resetPasswordConfirmed(params){
        $.ajax({
            url: admin_url+'user/reset_password',
            type: "POST",
            data:{"user_id":params.data.user_id, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == 'false')
                {
                    $('#activate_user').modal('hide');
                    lauch_common_success_message('Password Reset Success', 'Student password reset succcessfully and mail sent to the user with new credentials.')
                    $('#common_message .btn').addClass('btn-green').removeClass('btn-red');
                }
                else
                {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                        'prevent_no_button':true,
                    };
                    callback_danger_modal(messageObject);    
                    //$('#confirm_box_title').html(data['message']);
                    //$('#confirm_box_content').html('');
                }
            }
        });
    }


    function deleteUser(user_id)
    {
        /*$('#confirm_box_title').html(header_text);
        $('#confirm_box_content_1').html('');
        $('#confirm_box_ok').unbind();
        $('#confirm_box_ok').click({"user_id": user_id}, deleteUserConfirmed);        */
        var messageObject = {
            'body': 'Are you sure to delete student <b>'+user_name+'</b>',
            'button_yes':'DELETE', 
            'button_no':'CANCEL',
            'continue_params':{'user_id':user_id},
        };
        callback_warning_modal(messageObject, deleteUserConfirmed);    
    }
    
    function deleteUserConfirmed(params){
        $.ajax({
            url: admin_url+'user/delete',
            type: "POST",
            data:{"user_id":params.data.user_id, "is_ajax":true},
            success: function(response) {
                console.log(response);
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);    
                    setTimeout(() => {
                        location.reload();                        
                    }, 700);
                }
                else
                {
                    /*$('#confirm_box_title').html(data['message']);
                    $('#confirm_box_content').html('');*/
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                        'prevent_no_button':true,
                    };
                    callback_danger_modal(messageObject);    
                }
            }
        });
    }

    function changeUserStatus(status)
    {
        var message = '';
        var buttonText = '';
        switch(Number(status)) {
            case 0:
                message = 'Activate student <b>'+user_name+'</b>';
                buttonText = 'ACTIVATE';
            break;
            case 1:
                message = 'Deactivate student <b>'+user_name+'</b>';
                buttonText = 'DEACTIVATE';
            break;
            case 2:
                message = 'Approve student <b>'+user_name+'</b>';
                buttonText = 'APPROVE';
            break;
        }
        var messageObject = {
            'body': message,
            'button_yes':buttonText, 
            'button_no':'CANCEL',
            'continue_params':{'user_id':user_id},
        };
        callback_warning_modal(messageObject, changeStatusConfirmed);    
    }
    
    function changeStatusConfirmed(params){
        var userId = params.data.user_id;
        $.ajax({
            url: admin_url+'user/change_status',
            type: "POST",
            data:{"user_id":userId, "is_ajax":true},
            success: function(response) {
                //console.log(response);
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var status = data['user']['us_status'];
                    var buttonLabel  = '';
                    switch(status) {
                        case '0':
                            buttonLabel = 'Activate';
                        break;
                        case '1':
                            buttonLabel = 'Deactivate';
                        break;
                        case '2':
                            buttonLabel = 'Deactivate';
                        break;
                    }
            
                    $('#user_status_btn_'+userId).html('<a href="javascript:void(0);" onclick="changeUserStatus(\''+status+'\')">'+buttonLabel+'</a>');
                    var messageObject = {
                        'body': 'Status changed successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);    
                }
                else
                {
                    var messageObject = {
                        'body': 'Error occured',
                        'button_yes':'OK', 
                    };
                    callback_danger_modal(messageObject);    
                }
            }
        });
    }

    function restoreUser(user_id)
    {
        /*$('#confirm_box_title').html(header_text);
        $('#confirm_box_content').html('');
        $('#confirm_box_ok').unbind();
        $('#confirm_box_ok').click({"user_id": user_id}, restoreUserConfirmed);        */
        var messageObject = {
            'body': 'Are you sure to restore student <b>'+user_name+'</b>',
            'button_yes':'RESTORE', 
            'button_no':'CANCEL',
            'continue_params':{'user_id':user_id},
        };
        callback_warning_modal(messageObject, restoreUserConfirmed);    
    }

    function restoreUserConfirmed(params){
        $.ajax({
            url: admin_url+'user/restore',
            type: "POST",
            data:{"user_id":params.data.user_id, "is_ajax":true},
            success: function(response) {
                console.log(response);
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);    
                    setTimeout(() => {
                        location.reload();                        
                    }, 700);
                }
                else
                {
                    var messageObject = {
                        'body': data['message'],
                        'button_yes':'OK', 
                        'prevent_no_button':true,
                    };
                    callback_danger_modal(messageObject);    
                }
            }
        });
    }
    
    $(document).on('change', '#us_image', function(e){
        __uploading_file = e.currentTarget.files;
        console.log(__uploading_file);
        if( __uploading_file.length > 1 )
        {
            lauch_common_message('Error Occured', 'You are not allowed to upload more that one file.');
            return false;
        }
        var fileObj = __uploading_file[0].name;
        if(['jpg','jpeg','png'].indexOf(fileObj.split('.').pop()) == -1){
            __uploading_file = [];
            $('#us_image').val('');
            lauch_common_message('Validation Error', 'Unsupported file type. Supported files types are jpg/jpeg.');
            return false;
        }
        if (__uploading_file[0].size > 1148744)
        {
            __uploading_file = [];
            $('#us_image').val('');
            lauch_common_message('Error Occured', 'File size exceeded the limit.');
            return false;
        }
        var i                           = 0;
        var uploadURL                   = admin_url+"user/save_user_image" ; 
        var fileObj                     = new processFileName(__uploading_file[i]['name']);
        var param                       = new Array;
            param["file_name"]          = fileObj.uniqueFileName();        
            param["extension"]          = fileObj.fileExtension();
            param["file"]               = __uploading_file[i];
            param["id"]                 = user_id;
        uploadFiles(uploadURL, param, uploadUserImageCompleted);
    });
        
    
    function uploadUserImageCompleted(response) {
        var imageUploadedDate = new Date();
        var data = $.parseJSON(response);
        $('#user_image').attr('src', data['user_image']+"?"+imageUploadedDate.getTime())
    }

    $(document).ready(function(){
        var url  = window.location.search;
            url  = url.replace("?v=", ''); // remove the ?
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.pushState({
                path: link
            }, '', link);
        updateBulkAction();
    });

    function updateBulkAction() {
        if( user_email == '') {
            $('#send_user_message').parent().hide();
            $('#reset_pwd_btn').parent().hide();
        } else {
            $('#send_user_message').parent().show();
            $('#reset_pwd_btn').parent().show();
        }
    }
    