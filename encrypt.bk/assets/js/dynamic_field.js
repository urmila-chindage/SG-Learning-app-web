var __button_drag   = false;
$(document).ready(function ()
    {
        $("#sortable").sortable(
        {
            connectWith: "#sortable", 
            placeholder: "section-highlight",
            handle: ".drager",
            scroll: true,
            stop: function()
            {
                parent_sort();
            },
            update: function(event, ui) { 
               if( __button_drag == false )
               {
                   updateBlockPositon(ui.item.index(), ui.item[0]['id']);               
               }
               else
               {
                   __button_drag = false;
                   addBlockOnDrag(ui.item.index());               
               }
            }
        });

        parent_sort();
        $("#sortable, .section").disableSelection();
        $('.btn-add-section').draggable(
        {
            helper: "clone", 
            revert: "invalid",
            connectToSortable: "#sortable",
            drag: function( event, ui ) {
                __button_drag = true;
            }
        });
        $('#sortable').droppable(
        {
            drop: function(event, ui)
            {
                var droped_element = $(this).find('.btn-add-section');
                droped_element.html($('<div class="section-title-holder"><div class="drager ui-sortable-handle"><img src="'+assets_url+'images/drager.png"></div><span class="section-name" id="block_name_0"> Block Name </span><div class="btn-group section-control"><span class="dropdown-tigger" data-toggle="dropdown"><span class="label-text"><i class="icon icon-down-arrow"></i></span><span class="tilder"></span></span><ul class="dropdown-menu pull-right" id="button_block_0" role="menu"><li><a href="javascript:void(0)">Edit</a></li><li><a href="javascript:void(0)">Delete</a></li></ul></div></div><ul class="lecture-wrapper ui-sortable"></ul>'));
                droped_element.removeClass();
                droped_element.addClass("section");
                droped_element.attr("id", "block_0");
                droped_element.removeAttr('style');
                return;
            }
        });
    });
    function parent_sort()
    {
        $(".lecture-wrapper").sortable(
        {
            connectWith: ".lecture-wrapper",
            placeholder: "lecture-highlight",
            handle: ".drager",
            scroll: false,
            update: function(){
                var itemList = $(this).sortable(
                    "serialize", {
                    attribute: "id",
                    key: 'field_id[]'
                });
                updateFieldPosition($(this).attr('id'), itemList);
            }
        });
    }
    function updateBlockPositon(position, selector)
    {
        if(__ajaxInProgress == true) {
            return false;
        }

        var current_position    = parseInt(position+1);
        var block_id            = selector.split('_');
            block_id            = block_id[2];
            //__ajaxInProgress = true;
        $.ajax({
            url: admin_url+'environment/update_block_position',
            type: "POST",
            data:{ "is_ajax":true, 'block_id':block_id, 'position':position, 'structure':$("#sortable").sortable('serialize')},
            success: function(response) {
                __ajaxInProgress = false;
            }
        });    
    }
    
    function updateFieldPosition(block_id, items)
    {
        //__ajaxInProgress = true;
         $.ajax({
            url: admin_url+'environment/update_field_position',
            type: "POST",
            data:{ "is_ajax":true, 'block_id':block_id, 'structure':items},
            success: function(response) {
            }
        });    
    }

    function addBlockOnDrag(position)
    {
        cleanPopUpMessage();
        $('#block_name_create_on_drag_drop').val('');
        $('#save_block_drag_drop, #cancel_block_drag_drop').unbind();
        $('#save_block_drag_drop').click({"block_id": 0, 'position':position}, addBlockOnDragConfirmed);    
        $('#cancel_block_drag_drop, #addblockdraganddrop .close').click(function(){
            $('#block_0').remove();
        });
        $('#addblockdraganddrop').modal('show');
    }

    function addBlockOnDragConfirmed(param)
    {
        if(__ajaxInProgress == true) {
            return false;
        }
        var block_name       = $('#block_name_create_on_drag_drop').val();
        var position         = parseInt(param.data.position+1);
        var errorCount       = 0;
        var errorMessage     = '';

        if( block_name == '')
        {
            errorCount++;
            errorMessage += 'Block name required <br />';
        }
        cleanPopUpMessage();
        if( errorCount > 0 )
        {
            $('#addblockdraganddrop .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
            return false;
        }
        __ajaxInProgress     = true;
        $.ajax({
            url: admin_url+'environment/save_block',
            type: "POST",
            data:{ "block_name":block_name, "block_id":param.data.block_id, "position":position, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#block_0').attr('id', 'block_'+data['id']);
                $('#block_'+data['id']).attr('data-block-name', block_name);
                $('#block_name_0').attr('id', 'block_name_'+data['id']);
                $('#block_name_'+data['id']).text(block_name);
                $('#button_block_0').attr('id', 'button_block_'+data['id']);
                $('#block_'+data['id']).addClass('block_listing');
                $('#block_'+data['id']+' .lecture-wrapper').attr('id', 'block_field_'+data['id']);
                var buttonHtml  = '';
                    buttonHtml += '<li>';
                    buttonHtml += '    <a onclick="renameBlock(\''+data['id']+'\')" data-target="#rename_block" data-toggle="modal" href="javascript:void(0)">'+lang('rename')+'</a>';
                    buttonHtml += '</li>';
                    buttonHtml += '<li>';
                    buttonHtml += '    <a onclick="deleteBlock(\''+btoa(block_name)+'\', \''+data['id']+'\')" href="javascript:void(0)">'+lang('delete')+'</a>';
                    buttonHtml += '</li>';
                $('#button_block_'+data['id']).html(buttonHtml);
                $('#addblockdraganddrop').modal('hide');
                cleanPopUpMessage();
                __ajaxInProgress = false;
            }
        });
    }

    function addBlock()
    {
        $('.alert').remove();
        $('#block_name_create').val('');
        $('#add_block_save_ok').html('CREATE');
        $('#add_block_save_ok').unbind();
        $('#add_block_save_ok').click({"block_id": 0, 'block_input_id':'block_name_create'}, saveBlockConfirmed);    
    }

    function renameBlock(block_id)
    {
        $('.alert').remove();
        $('#block_name_rename').val($('#block_'+block_id).attr('data-block-name'));
        $('#block_save_ok').html('UPDATE');
        $('#block_save_ok').unbind();
        $('#block_save_ok').click({"block_id": block_id, 'block_input_id':'block_name_rename'}, saveBlockConfirmed);    
    }

    function saveBlockConfirmed(params)
    {   
        if(__ajaxInProgress == true) {
            return false;
        }

        var block_name     = $('#'+params.data.block_input_id).val().trim();
        var errorCount       = 0;
        var errorMessage     = '';

        if( block_name == '')
        {
            errorCount++;
            errorMessage += 'Block name required <br />';
        }
        cleanPopUpMessage();
        var blockWrapperId = (params.data.block_id>0)?'rename_block':'addblock';
        if( errorCount > 0 )
        {
            $('#'+blockWrapperId+' .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            return false;
        }

        //__ajaxInProgress = true;
        $.ajax({
            url: admin_url+'environment/save_block',
            type: "POST",
            data:{"block_name":block_name, "block_id":params.data.block_id, "is_ajax":true},
            success: function(response) {
                var data  = $.parseJSON(response);
                
                if(data.error == false)
                {
                    if( params.data.block_id == 0 )
                    {
                        $('#no_field_button').remove();
                        var sectionHtml = '';
                            sectionHtml += '<li class="section" id="block_'+data['id']+'" data-block-name="'+block_name+'">';
                            sectionHtml += '    <div class="section-title-holder">';
                            sectionHtml += '        <div class="drager">';
                            sectionHtml += '            <img src="'+assets_url+'images/drager.png">';
                            sectionHtml += '        </div>';
                            sectionHtml += '        <span class="section-name" id="block_name_'+data['id']+'"> '+((block_name.length > 43)?(block_name.substr(0, 40)+'...'):block_name)+'</span>';
                            sectionHtml += '        <div class="btn-group section-control">';
                            sectionHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                            sectionHtml += '                <span class="label-text">';
                            sectionHtml += '                    <i class="icon icon-down-arrow"></i>';
                            sectionHtml += '                </span>';
                            sectionHtml += '            <span class="tilder"></span>';
                            sectionHtml += '            </span>';
                            sectionHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                            sectionHtml += '                <li>';
                            sectionHtml += '                    <a href="javascript:void(0)" data-toggle="modal" data-target="#rename_block" onclick="renameBlock(\''+data['id']+'\')">'+lang('rename')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '                <li>';
                            sectionHtml += '                    <a href="javascript:void(0)" onclick="deleteBlock(\''+btoa(block_name)+'\', \''+data['id']+'\')">'+lang('delete')+'</a>';
                            sectionHtml += '                </li>';
                            sectionHtml += '            </ul>';
                            sectionHtml += '        </div>';
                            sectionHtml += '    </div>';
                            sectionHtml += '    <ul class="lecture-wrapper ui-sortable" id="block_field_'+data['id']+'">';
                            sectionHtml += '    </ul>';
                            sectionHtml += '</li>';
                        $('#sortable').append(sectionHtml);
                        parent_sort();
                    }
                    else
                    {
                        $('#block_name_'+params.data.block_id).html(((block_name.length > 43)?(block_name.substr(0, 40)+'...'):block_name));
                        $('#block_'+params.data.block_id).attr('data-block-name', block_name);
                    }
                    $("#rename_block, #addblock").modal('hide');
                }
                else
                { 
                    //console.log(data['message']);
                    $('.alert').remove();
                    cleanPopUpMessage();
                    $('.modal-body').prepend(renderPopUpMessage('error', data['message']));
                    //$('#addblock').prepend(renderPopUpMessage('error', data['message']));
                }
                __ajaxInProgress = false;
            }
        });
    }
    function deleteBlock(block_name, block_id)
    {
        $('.alert').remove();
        var messageObject = {
            'body': 'Are you sure to delete the block named "'+atob(block_name)+'"',
            'button_yes':'DELETE', 
            'button_no':'CANCEL',
            'continue_params':{'block_id':block_id},
        };
        callback_warning_modal(messageObject, deleteBlockConfirmed);    
    }

    function deleteBlockConfirmed(param)
    {
        if(__ajaxInProgress == true) {
            return false;
        }
        var blockId = param.data.block_id;
        //__ajaxInProgress = true;
        $.ajax({
            url: admin_url+'environment/delete_block',
            type: "POST",
            data:{ "is_ajax":true, 'block_id':blockId},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#block_'+blockId).remove();
                if($('.block_listing').length === 0) {
                    $( '<p id="no_field_button" style="text-align:center;">Click the <b>"ADD FIELD"</b> button to create new field.</p>' ).insertBefore( ".listing_profile_fields_li" );
                }
                var messageObject = {
                    'body': 'Block removed successfully',
                    'button_yes':'OK', 
                };
                __ajaxInProgress = false;
                callback_success_modal(messageObject);    
            }
        });
    }
    
    function deleteField(field_name, field_id)
    {
        var messageObject = {
            'body': 'Are you sure to delete the field named "'+atob(field_name)+'"',
            'button_yes':'DELETE', 
            'button_no':'CANCEL',
            'continue_params':{'field_id':field_id},
        };
        callback_warning_modal(messageObject, deleteFieldConfirmed);    
    }

    function deleteFieldConfirmed(param)
    {
        if(__ajaxInProgress == true) {
            return false;
        }

        var fieldId = param.data.field_id;
        //__ajaxInProgress = true;
        $.ajax({
            url: admin_url+'environment/delete_field',
            type: "POST",
            data:{ "is_ajax":true, 'field_id':fieldId},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#field_'+fieldId).remove();
                var messageObject = {
                    'body': 'Field deleted successfully',
                    'button_yes':'OK', 
                };
                __ajaxInProgress = false;
                callback_success_modal(messageObject);    
            }
        });
    }
    
    function editField(fieldId)
    {
        
        if(__ajaxInProgress == true) {
            return false;
        }

        $('#field_input_type').val('1').trigger('change');
        $('.alert').remove();
        //__ajaxInProgress = true;
        $.ajax({
            url: admin_url+'environment/edit_field',
            type: "POST",
            data:{ "is_ajax":true, 'id':fieldId},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    //$('.alert').remove(); $('#profile_field').modal();return;
                    __fieldId = fieldId;
                    var blockOption   = '<option value="">Choose Block</option>';
                    if(data['blocks'].length > 0 )
                    {
                        for (var i=0; i<data['blocks'].length ; i++)
                        {
                            blockOption += '<option value="'+data['blocks'][i]['id']+'">'+data['blocks'][i]['pb_name']+'</option>';
                        }
                    }
                    $('#block_id_field').html(blockOption);
                    $('#field_label, #field_placeholder, #field_default_value, #block_id_field, #block_name_field').val('');
                    $('#block_id_field, #create_new_block_field').show();
                    $('#block_name_field, #create_new_block_cancel_field').hide().val('');
                    $('#block_field_wrapper').show();
                    $('#is_field_mandatory').prop('checked', false);
                    $('#enable_autosuggestion').prop('checked', false);
                    
                    if(__fieldId > 0)
                    {
                        $('#block_field_wrapper').hide();
                        var profile_field = data['profile_field'];
                        $('#field_label').val(profile_field['pf_label']);
                        var pf_placeholder = (profile_field['pf_placeholder'] != null) ? profile_field['pf_placeholder'] : '';
                        $('#field_placeholder').val(pf_placeholder);
                        var pf_default_value = (profile_field['pf_default_value'] != null) ? profile_field['pf_default_value'] : '';
                        $('#field_default_value').val(pf_default_value);     
                        $('#block_id_field').val(profile_field['pf_block_id']);
                        if(profile_field['pf_mandatory'] == 1)
                        {
                            $('#is_field_mandatory').prop('checked', true);
                        }
                        if(profile_field['pf_auto_suggestion'] == 1)
                        {
                            $('#enable_autosuggestion').prop('checked', true);
                        }
                        $('#field_input_type').val(profile_field['pf_field_input_type']);
                    }
                    updateFieldAttributes();
                    $('#profile_field').modal();                
                }
                else
                {
                    lauch_common_message('Error Occured', data['message']);
                }
                __ajaxInProgress = false;
               
            }
        });
    }
    
    $(document).on('click', '#create_new_block_field', function(){
        $('#block_id_field, #create_new_block_field').hide().val('');
        $('#block_name_field, #create_new_block_cancel_field').show();
    });
    $(document).on('click', '#create_new_block_cancel_field', function(){
        $('#block_id_field, #create_new_block_field').show();
        $('#block_name_field, #create_new_block_cancel_field').hide().val('');
    });
    
    var __ajaxInProgress = false;
    var __fieldId = 0;
    function saveField()
    {
        if(__ajaxInProgress == true) {
            return false;
        }
        var fieldLabel            = $('#field_label').val().trim();
        var fieldManadatory       = $('#is_field_mandatory').prop('checked');
            fieldManadatory       = (fieldManadatory==true)?1:0;
        var  enableAutosugestion  = $('#enable_autosuggestion').prop('checked');  
             enableAutosugestion  = (enableAutosugestion==true)?1:0; 
        var fieldPlaceholder      = $('#field_placeholder').val();
        var fieldDefaultValue     = $('#field_default_value').val();
        var block_id_field        = $('#block_id_field').val();
        var block_name_field      = $('#block_name_field').val();
        var fieldInputType        = $('#field_input_type').val();
        var errorCount            = 0;
        var errorMessage          = '';

        if (fieldLabel == '')
        {
            errorMessage += 'Enter field Label.<br />';
            errorCount++;
        }
        
        if (block_id_field+block_name_field == '')
        {
            errorMessage += 'Block required<br />';
            errorCount++;
        }
        
        fieldDefaultValue = processDefaultValues($('#field_default_value').val());
        if(fieldInputType == 2 && fieldDefaultValue == '')
        {
            errorMessage += 'Default values cannot be empty<br />';
            errorCount++;
        }
        
        $('.alert').remove();
        if (errorCount > 0)
        {
            $('#profile_field .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();
        } 
        else
        { 
            //__ajaxInProgress = true;
            $('#save_profile_field_btn').html('SAVING...<ripples></ripples>');
            $.ajax({
                url: admin_url + 'environment/save_profile_field',
                type: "POST",
                data: {
                    "is_ajax": true, 
                    'id': __fieldId, 
                    'block_id':block_id_field, 
                    'block_name':block_name_field,  
                    'field_label': fieldLabel, 
                    'field_mandatory': fieldManadatory, 
                    'field_placeholder': fieldPlaceholder, 
                    'field_default_value': fieldDefaultValue , 
                    'field_auto_suggestion' : enableAutosugestion, 
                    'field_input_type' : fieldInputType 
                },
                success: function (response) {
                    var data = $.parseJSON(response);
                    //console.log(data.message);
                    if(data['error']==false)
                    {
                        $('.alert').remove();
                        if(block_id_field == 0)
                        { 
                            var blockHtml = '';
                                blockHtml += '<li class="section" id="block_'+data['block']['id']+'" data-block-name="'+data['block']['pb_name']+'">';
                                blockHtml += '    <div class="section-title-holder">';
                                blockHtml += '        <div class="drager ui-sortable-handle">';
                                blockHtml += '            <img src="'+assets_url+'images/drager.png">';
                                blockHtml += '        </div>';
                                blockHtml += '        <span class="section-name" id="block_name_'+data['block']['id']+'"> '+data['block']['pb_name']+' </span>';
                                blockHtml += '        <div class="btn-group section-control">';
                                blockHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                                blockHtml += '                <span class="label-text">';
                                blockHtml += '                    <i class="icon icon-down-arrow"></i>';
                                blockHtml += '                </span>';
                                blockHtml += '                <span class="tilder"></span>';
                                blockHtml += '            </span>';
                                blockHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                                blockHtml += '                <li>';
                                blockHtml += '                    <a href="javascript:void(0)" data-toggle="modal" onclick="renameBlock(\''+data['block']['id']+'\')" data-target="#rename_block">Rename</a>';
                                blockHtml += '                </li>';
                                blockHtml += '                <li>';
                                blockHtml += '                    <a href="javascript:void(0)" onclick="deleteBlock(\''+btoa(data['block']['pb_name'])+'\', \''+data['block']['id']+'\')">Delete</a>';
                                blockHtml += '                </li>';
                                blockHtml += '            </ul>';
                                blockHtml += '        </div>';
                                blockHtml += '    </div>';
                                blockHtml += '    <ul class="lecture-wrapper ui-sortable" id="block_field_'+data['block']['id']+'"></ul>';
                                blockHtml += '</li>';
                                $('#sortable').append(blockHtml);
                                parent_sort();
                        }
                        $('#no_field_button').remove();
                        if(__fieldId > 0 )
                        {
                            $('#field_'+__fieldId).html(renderField(data['field']));
                        }
                        else
                        {
                            var fieldHtml = '';
                            fieldHtml += '<li id="field_'+data['id']+'">';
                            fieldHtml += renderField(data['field'])
                            fieldHtml += '</li>';
                            $('#block_field_'+data['field']['pf_block_id']).append(fieldHtml);
                        }
                        $('#profile_field').modal('hide')
                    }
                    else
                    {   
                        $('.modal-body').prepend(renderPopUpMessage('error', data['message']));
                        $('#profile_field_message').prepend(renderPopUpMessage('error', data['message']));
                        scrollToTopOfPage();
                    }
                    __fieldId = 0;
                    $('#save_profile_field_btn').html('SAVE<ripples></ripples>');
                    __ajaxInProgress = false;
                }
            });
        }
    }

    function preventHtmlTag(e) {
        return !(e.shiftKey == true && (e.which == 60 || e.which == 62));
    }

    function triggerProcessDefaultValues(event) {
        event.value =  processDefaultValues(event.value);
    }
    function processDefaultValues(fieldValue) {
        var fieldDefaultValue     = [];
        var fieldDefaultValueTemp = fieldValue;
            fieldDefaultValueTemp = fieldDefaultValueTemp.trim();
            fieldDefaultValueTemp = fieldDefaultValueTemp.split(",");
            if(fieldDefaultValueTemp.length > 0 ) {
                for(var i=0; i<fieldDefaultValueTemp.length;i++) {
                    var fieldValueTemp = fieldDefaultValueTemp[i].trim();
                    if(fieldDefaultValueTemp[i].trim() != '' ) {
                        fieldDefaultValue.push(fieldValueTemp);
                    }
                }
            }
        return fieldDefaultValue.join(',');
    }

    function renderField(data)
    {
        var fieldHtml = '';
        fieldHtml += '    <div class="lecture-hold">';
        fieldHtml += '        <div class="drager ui-sortable-handle">';
        fieldHtml += '            <img src="'+assets_url+'images/drager.png">';
        fieldHtml += '        </div>';
        fieldHtml += '        <a href="javascript:void(0)" class="lecture-innerclick">';
        fieldHtml += '            <span class="lecture-name">'+data['pf_label']+'</span>';
        fieldHtml += '        </a>';
        fieldHtml += '        <div class="btn-group lecture-control">';
        fieldHtml += '            <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">';
        fieldHtml += '                <span class="label-text">';
        fieldHtml += '                    <i class="icon icon-down-arrow"></i>';
        fieldHtml += '                </span>';
        fieldHtml += '                <span class="tilder"></span>';
        fieldHtml += '            </span>';
        fieldHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
        fieldHtml += '                <li>';
        fieldHtml += '                    <a href="javascript:void(0)" onclick="editField(\''+data['id']+'\')">Edit</a>';
        fieldHtml += '                </li>';
        fieldHtml += '                <li>';
        fieldHtml += '                    <a href="javascript:void(0)" onclick="deleteField(\''+btoa(data['pf_label'])+'\', \''+data['id']+'\')">Delete</a>';
        fieldHtml += '                </li>';
        fieldHtml += '            </ul>';
        fieldHtml += '        </div>';
        fieldHtml += '    </div>';
        return fieldHtml;
    }

    function updateFieldAttributes() {
        if($('#field_input_type').val() == '1') {
            $('#placeholder_wrapper').show();
            $('#default_value_wrapper').hide();
            $('#enable_autosuggestion_wrapper').show();
        } else {
            $('#placeholder_wrapper').hide();
            $('#default_value_wrapper').show();
            $('#enable_autosuggestion_wrapper').hide();
        }
    }