
function cleanPopUpMessage(){
    $('#popUpMessage').remove();
}

function createRole(header_text, label)
{
    cleanPopUpMessage();
    $('#create_box_title').html(header_text);
    $('#create_box_label').html(label);
    $('#create_box_ok').unbind();
    $('#create_box_ok').html('CREATE');
    $('#role_status_div').hide();
    $('#role_name').val('');
    $('#role_status').val('');
    $('#full_course').prop('checked', true)
    $('#create_box_ok').click({}, createRoleConfirmed);    
}

function editRole(header_text, role)
{
    cleanPopUpMessage();
    var data  = $.parseJSON(atob(role));
    $('#create_box_title').html(header_text);
    $('#create_box_ok').unbind();
    $('#role_status_div').show();
    $('#create_box_ok').html('UPDATE');
    $('#create_box_ok').click({role_id: data['id']}, editRoleConfirmed);
    $('#role_name').val(data['rl_name']);
    $('#role_status').val(data['rl_status']);
    (data['rl_full_course'] == '1') ? $('#full_course').prop('checked', true):$('#full_course').prop('checked', false);
}

function restoreRole(role_id)
{ 
    var messageObject = {
        'body':'Are you sure to restore the role?',
        'button_yes':lang('RESTORE'), 
        'button_no':'CANCEL',
        'continue_params':{'role_id':role_id},
    };
    callback_warning_modal(messageObject, restoreRoleConfirmed);
}

function restoreRoleConfirmed(params){
    $.ajax({
        url: admin_url+'role/restore_role',
        type: "POST",
        data:{"is_ajax":true, 'role_id':params.data.role_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                var messageObject = {
                    'body':'Role restored successfully',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject, function(){window.location.reload()});
                // window.location = admin_url+'role';
                // setTimeout(function(){
                //     window.location.reload();            
                // }, 2500);
            }
            else
            {
                $('#create_role .modal-body').prepend(renderPopUpMessage('error', data['message']));
                // scrollToTopOfPage();
            }
        }
    });
}

function createRoleConfirmed()
{
    var role_name      = $('#role_name').val();
        role_name      =  role_name.replace(/["<>{}]/g, '');
        role_name      =  role_name.trim();
    // var courseAccess    = ( $('#full_course').is(":checked") == true )? '1':'0';
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( role_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter role name <br />';
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'role/create_role',
            type: "POST",
            data:{"is_ajax":true, 'role_name':role_name},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var role_id     = data['role_id'];
                    window.location = admin_url+'role_settings/basics/'+role_id;
                }
                else
                {
                    $('#create_role .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    // scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create_role .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        // scrollToTopOfPage();
    }
}


function editRoleConfirmed(event) {
    var role_id        = event.data.role_id;
    var role_name      = $('#role_name').val();
        role_name      =  role_name.replace(/["<>{}]/g, '');
        role_name      =  role_name.trim();
    var role_status    = $('#role_status').val();
    // var courseAccess    = ( $('#full_course').is(":checked") == true )? '1':'0';
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( role_name == '')
    {
        errorCount++;
        errorMessage += 'Please enter role name <br />';
    }
    if(role_status == '')
    {
        errorCount++;
        errorMessage += 'Please choose any status <br />';
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'role/update_role',
            type: "POST",
            data:{"is_ajax":true, 'role_id':role_id, 'role_name':role_name, 'role_status':role_status},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'role';
                    // window.location = admin_url+'role_settings/basics/'+role_id;
                }
                else
                {
                    $('#create_role .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    //scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create_role .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}

function deleteRole(role_id,role_name)
{ 
    var messageObject = {
        'body':'Are you sure to delete the role?',
        'button_yes':lang('DELETE'), 
        'button_no':'CANCEL',
        'continue_params':{'role_id':role_id,'role_name':role_name},
    };
    callback_warning_modal(messageObject, deleteRoleConfirmed);      
}

function deleteRoleConfirmed(params) {
    var roleName = params.data.role_name;
    var roleId   = params.data.role_id;
    $.ajax({
        url: admin_url+'role/delete_role',
        type: "POST",
        data:{"is_ajax":true,'role_name':roleName, 'role_id':roleId},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                var messageObject = {
                    'body':'Role deleted successfully',
                    'button_yes':'OK', 
                };
                callback_success_modal(messageObject, function(){window.location.reload()});
                // setTimeout(function(){
                //     window.location.reload();            
                // }, 2500);
            }
            else
            {
                $('#confirm_box_title').prepend(renderPopUpMessage('error', data['message']));
                scrollToTopOfPage();
            }
        }
    });
}
