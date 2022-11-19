 //var __instituteCount  = 1;
    var __activeInstitute     = 0;
    var __instituteSelected   = new Array();
    var __courseSelected    = new Array();
    var __roleDescription   = new Object();
    
        __roleDescription[1] = 'Sub admin can do all the functionalities done by the Super Admin.';
        __roleDescription[3] = 'Tutor can manage contents and students of their assigned courses. Also they can create their own courses.';
        __roleDescription[4] = 'Content editor can manage courses with limited features and manage contents';
    function renderInstitutesHtml(institutes)
    {
        var institutesHtml  = '';
        if(Object.keys(institutes).length > 0 )
        {
            $.each(institutes, function(instituteKey, institute )
            {
                $('#institute_'+institute['id']).remove();
                institutesHtml += '<div class="rTableRow settings-table" data-institute-id="'+institute['id']+'" id="institute_'+institute['id']+'">';
                institutesHtml += renderInstituteHtml(institute);
                institutesHtml += '</div>';
                __activeInstitute = (__activeInstitute == 0)?institute['id']:__activeInstitute;    
            });
        }
        return institutesHtml;
    }
    
    function addInstituteForm()
    {
        cleanPopUpMessage();
        $('#institute_name').val('');
        $('#institute_code').val('');
        $('#institute_email').val('');
        $('#institute_password').val('');
        $('#send_mail').prop('checked', false);
        $('#create_institute').modal();
    }
    
    function addInstitute()
    {
        var instituteName        = $('#institute_name').val();
        var instituteCode        = $('#institute_code').val();
        var instituteEmail       = $('#institute_email').val();
        var institutePassword    = $('#institute_password').val();
        var sendMail           = $('#send_mail').prop('checked');
            sendMail           = (sendMail==true)?'1':'0';
        var errorCount         = 0;
        var errorMessage       = '';
        
        if(instituteName == '')
        {
            errorCount++;
            errorMessage += 'Enter Institute name<br />';            
        }else if(instituteName != ''){
            var regex = new RegExp("^(?![0-9]*$)[a-zA-Z0-9 ]+$");
            if(!(regex.test(instituteName))){
                errorCount++;
                errorMessage += 'Special characters are not allowed for Institute name<br />';  
            }
             
        }else{
            if(instituteName.length > 50)
            {
                errorCount++;
                errorMessage += 'Institute name length exceed the limit<br />';                    
            }
        }
        
        if(instituteCode == '')
        {
            errorCount++;
            errorMessage += 'Enter Institute code<br />';            
        }
        else
        {
            if(instituteCode.length > 6)
            {
                errorCount++;
                errorMessage += 'Institute code length exceed the limit<br />';                    
            }
        }
        
        if(instituteEmail == '')
        {
            errorCount++;
            errorMessage += 'Enter a valid email id<br />';
        }
        else
        {
            if(!validateEmail(instituteEmail))
            {
                errorCount++;
                errorMessage += 'Invalid email id<br />';
            }
        }
        if(institutePassword == '')
        {
            errorCount++;
            errorMessage += 'Enter admin password<br />';            
        }
        else
        {
            if(institutePassword.length < 6)
            {
                errorCount++;
                errorMessage += 'Password must be atleast 6 characters<br />';                    
            }
        }
        
        $('#popUpMessage').remove();
        if(errorCount > 0)
        {
            $('#create_institute .modal-body').prepend(renderPopUpMessage('error', errorMessage));  
        }
        else
        {
            $.ajax({
                url: admin_url+'institutes/create_institute',
                type: "POST",
                data:{"institute_name":instituteName, "institute_code":instituteCode, "institute_email":instituteEmail, "institute_password":institutePassword, 'send_mail':sendMail, "is_ajax":true},
                success: function(response) {
                    var data  = $.parseJSON(response);
                    if(data['error'] == false)
                    {
                        window.location = admin_url+'institutes/institute/'+data['id'];
                    }
                    else
                    {
                        $('#create_institute .modal-body').prepend(renderPopUpMessage('error', data['message']));  
                    }
                }
            });
        }
    }
    
    function preventSpecialCharector(e)
    {
        console.log(e);
       var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57||k == 46||k==38));
    }
    
    $(document).on('change', '#institute_role', function(){
        var institute_role = $(this).val();
            $('#role_funcationlity_details').html('');
        if(typeof __roleDescription[institute_role] != 'undefined')
        {
            $('#role_funcationlity_details').html(__roleDescription[institute_role] );
        }
    });

    var __searchTimeOut = '';
    $(document).on('keyup', '#institute_keyword', function(){
        clearTimeout(__searchTimeOut);
         __searchTimeOut = setTimeout(function(){
            __activeInstitute = 0;
            __offset = 1;
            loadInstitutes();
         }, 300);
    });
    $(document).on('click', '#searchclear', function(){
            __activeInstitute = 0;
            __offset = 1;
            loadInstitutes();
    });
    $(document).on('click', '#institute_search', function(){
        var institute_keyword = $('#institute_keyword').val().trim();        
        if(institute_keyword == '')
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
        }
        else{
            __activeInstitute = 0;
            __offset = 1;
            loadInstitutes();
        }
    });
    
    function filter_institute(institute_type)
    {
        clearCache();
        //__instituteCount      = 1;
        __activeInstitute     = 0;
        var institutesHtml   = '';
        var keyword = $('#institute_keyword').val();
            keyword = keyword.toLowerCase();
        if(institute_type == 0)
        {
            $('#dropdown_role_text').html('All Institutes <span class="caret"></span>');
            $('#dropdown_role_text').attr('data-role-id', 0);
        }
        else
        {
            $('#dropdown_role_text').html($('#dropdown_list_'+institute_type).text()+' <span class="caret"></span>');
            $('#dropdown_role_text').attr('data-role-id', institute_type);
        }
            var skipInstitute = false;
            if(Object.keys(__instituteObject).length > 0 )
            {
                $.each(__instituteObject, function(instituteKey, institute )
                {
                    if(institute['us_role_id'] != institute_type && institute_type > 0 )
                    {
                        skipInstitute=true;
                    }
                    if( keyword != '' && institute['us_name'].toLowerCase().search(keyword) == -1 && institute['us_institute_code'].toLowerCase().search(keyword) == -1)
                    {
                        skipInstitute=true;
                    }
                    if(skipInstitute==false)
                    {
                        institutesHtml += '<div class="rTableRow settings-table" data-institute-id="'+institute['id']+'" id="institute_'+institute['id']+'">';
                        institutesHtml += renderInstituteHtml(institute);
                        institutesHtml += '</div>';
                        __activeInstitute = (__activeInstitute == 0)?institute['id']:__activeInstitute;                                            
                    }
                    skipInstitute=false;
                });
            }   
        $('#institute_wrapper').html(institutesHtml);
        $('#institute_detail_wrapper').html('');
        if(__activeInstitute>0)
        {
            loadInstituteDetail(__activeInstitute);        
        }
    }
    
    function loadMoreInstitutes()
    {
        loadInstitutes();
    }

    var __filter_dropdown = 'active';
    function filter_institutes_by(filter)
    {  
        __activeInstitute    = 0;
        __offset             = 1;
        __filter_dropdown    = filter;
        $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
        loadInstitutes();
        scrollToTopOfPage();
        if(__filter_dropdown == "deleted")
        {
            $('#select_all').hide();
        }else{
            $('#select_all').show();
        }
    }

    function loadInstitutes()
    {
        $('#loadmorebutton').html('Loading..');
        var keyword  = $('#institute_keyword').val();

        var currentInstitutes   = __instituteObject;
        $.ajax({
            url: admin_url+'institutes/institutes_json',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword,'limit':__limit,'offset':__offset, 'filter':__filter_dropdown},
            success: function(response) {
                var data = $.parseJSON(response);
                var remaining = 0;
                $('#loadmorebutton').hide();
                if(Object.keys(data['institutes']).length > 0){
                    
                    for( var institute in data['institutes'])
                    {
                        currentInstitutes.push(data['institutes'][institute]);
                    }
                    __instituteObject = currentInstitutes;

                     __offset++;
                    if(__offset == 2)
                    {
                        remaining = (data['total_institutes'] - Object.keys(data['institutes']).length);
                        
                        scrollToTopOfPage();
                        clearCache();
                        $('#institute_wrapper').html(renderInstitutesHtml(data['institutes']));
                        initToolTip();
                        loadInstituteDetail(data['institutes'][0]['id']);
                    }
                    else
                    {
                        remaining = (data['total_institutes'] - (((__offset-2)*data['limit'])+Object.keys(data['institutes']).length));
                        $('.institute-checkbox-parent').prop('checked', false);
                        $('#institute_wrapper').append(renderInstitutesHtml(data['institutes']));  
                        initToolTip();
                    }
                    __activeInstitute = 0;
                }
                else
                {
                    clearCache();
                    $('#institute_detail_wrapper').html('');
                    $('#institute_wrapper').html(renderPopUpMessagePage('error', 'No Institutes found.'));
                    // $('#select_all').hide();
                    $('#popUpMessagePage .close').css('display', 'none');
                }
                if(data['show_load_button'] == true)
                {
                    $('#loadmorebutton').show();
                }
                remaining = (remaining>0)?'('+remaining+')':'';
                $('#loadmorebutton').html('Load More '+remaining+'<ripples></ripples>');
            }
        });
    }

    function renderInstituteHtml(institute)
    {
        var ins_img           = '';    
        var instituteHtml     = '';
        var instituteBgColor  = '';

        //consider the record is deleted and set the value if record deleted
        var action_class   = 'label-danger';
        var item_deleted   = 'item-deleted';
        var action         = 'Deleted';
        var disabled       = 'disabled="disabled"';
        var visibility     = '';
        if(__instituteSelected.length > 1)
        {
            visibility = 'style="visibility:hidden;"';
        }

        //case if record is not deleted
        if(institute['ib_deleted'] == 0)
        {
            item_deleted = '';
            if(institute['ib_status'] == 1)
            {
                action_class   = 'label-success';                                                                
                action         = 'Active';
            }
            else
            {
                action_class   = 'label-warning';                                                                
                action         = 'Inactive';
            }
        }
        instituteHtml += '';
        instituteHtml += '    <div class="rTableCell" onclick="loadInstituteDetail(\''+institute['id']+'\')"> ';
        instituteHtml += '        <input type="checkbox" class="institute-checkbox" id="institute_checkbox_'+institute['id']+'" value="'+institute['id']+'" '+((institute['ib_deleted'] == 1)?disabled:'')+'> ';
        //instituteHtml += '        <span class="font-bold">'+(__instituteCount++)+'</span>';
        instituteHtml += '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" width="30px" height="26px" viewBox="0 0 30 26" enable-background="new 0 0 30 26" xml:space="preserve" fill="#64277d" style="&#10; vertical-align: middle;&#10; margin: 0px 10px;&#10;"><g><path d="M6.041,7.719l0.416,0.625l9.222-6.138l7.159,4.764H6.041V7.719l0.416,0.625L6.041,7.719v0.75h19.277 c0.331,0,0.622-0.216,0.718-0.533c0.096-0.316-0.027-0.658-0.303-0.841l-9.64-6.415c-0.252-0.168-0.578-0.168-0.831,0L5.625,7.095 C5.35,7.278,5.228,7.62,5.323,7.937C5.419,8.253,5.71,8.469,6.041,8.469V7.719z"/><path d="M5.399,23.182h20.201c0.414,0,0.75-0.336,0.75-0.75s-0.336-0.75-0.75-0.75H5.399c-0.414,0-0.75,0.336-0.75,0.75 S4.985,23.182,5.399,23.182"/><path d="M3.476,25.445h24.049c0.414,0,0.75-0.336,0.75-0.75s-0.336-0.75-0.75-0.75H3.476c-0.414,0-0.75,0.336-0.75,0.75 S3.062,25.445,3.476,25.445"/><path d="M3.476,11.298h24.049c0.414,0,0.75-0.336,0.75-0.75s-0.336-0.75-0.75-0.75H3.476c-0.414,0-0.75,0.336-0.75,0.75 S3.062,11.298,3.476,11.298"/><path d="M8.006,12.984v6.932c0,0.414,0.336,0.75,0.75,0.75s0.75-0.336,0.75-0.75v-6.932c0-0.414-0.336-0.75-0.75-0.75 S8.006,12.57,8.006,12.984"/><path d="M14.798,12.984v6.932c0,0.414,0.336,0.75,0.75,0.75s0.75-0.336,0.75-0.75v-6.932c0-0.414-0.336-0.75-0.75-0.75 S14.798,12.57,14.798,12.984"/><path d="M21.587,12.984v3.325v3.607c0,0.414,0.336,0.75,0.75,0.75s0.75-0.336,0.75-0.75v-3.607v-3.325 c0-0.414-0.336-0.75-0.75-0.75S21.587,12.57,21.587,12.984z"/><polygon points="13.127,6.022 15.679,4.133 18.232,6.022 "/></g></svg>';
        /*instituteHtml += '        <span class="icon-wrap-round img" onclick="loadInstituteDetail(\''+institute['id']+'\')">';
        ins_img         = ((institute['ib_image'] == 'default.jpg')?webConfigs('default_institute_path'):webConfigs('institute_path')); 
        instituteHtml += '            <img src="'+ins_img+''+institute['ib_image']+'">';                
        instituteHtml += '        </span>';
       */ if(institute['ib_name'] !== null)
        {
            var instituteNameList = institute['ib_name'];
            var instituteNameListToolTip = '';
            if(institute['ib_name'].length > 33)
            {
                instituteNameList = institute['ib_name'].substr(0, 30)+'...';
                instituteNameListToolTip = 'data-toggle="tooltip" data-placement="right" title="'+ institute['ib_institute_code'] +' - '+institute['ib_name'] +'"';
            }    
            instituteHtml += '        <span class="wrap-mail ellipsis-hidden"> ';
            instituteHtml += '            <span class="ellipsis-style"><span class="institution-code">'+institute['ib_institute_code']+'</span><a href="javascript:void(0)" class="faculty-link" '+instituteNameListToolTip+'  > - '+((institute['ib_name'].length > 35)?(institute['ib_name'].substr(0, 32)+'...'):institute['ib_name'])+'</a></span>';
            instituteHtml += '        </span>';
        }        
        instituteHtml += '        <span class="normal-base-color"> ';
        //instituteHtml += '            <span class="badge '+instituteBgColor+' group-total">'+institute['rl_name']+'</span>';
        instituteHtml += '        </span>';
        instituteHtml += '    </div>';
        instituteHtml += '    <div class="rTableCell pad0">';
        instituteHtml += '        <div class="col-sm-12 pad0"><label class="pull-right label '+action_class+'">'+action+'</label></div>';
        instituteHtml += '    </div>';
        instituteHtml += '    <div class="td-dropdown rTableCell">';
        instituteHtml += '        <div class="btn-group lecture-control" '+visibility+'>';
        instituteHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
        instituteHtml += '                <span class="label-text">';
        instituteHtml += '                    <i class="icon icon-down-arrow"></i>';
        instituteHtml += '                </span>';
        instituteHtml += '                <span class="tilder"></span>';
        instituteHtml += '            </span>';
        instituteHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="user_action_'+institute['id']+'">';
            if(institute['ib_deleted'] == 0){
                instituteHtml += '<li id="status_btn_'+institute['id']+'">';
                var cb_status = (institute['ib_status']==1)?'deactivate':'activate'; 
                var cb_action = cb_status; 
                if(__permissions.indexOf('3') >= 0)
                {
                    instituteHtml += '<li>';
                    instituteHtml += '    <a href="'+admin_url+'institutes/institute/'+institute['id']+'" >'+lang('edit')+'</a>';
                    instituteHtml += '</li>';
                }
                if(__permissions.indexOf('1') >= 0)
                {
                    instituteHtml += '<li>';
                    instituteHtml += '     <a href="javascript:void(0);" onclick="sendMessageToInstitute(\''+institute['id']+'\')">'+lang('send_message')+'</a>';
                    instituteHtml += '</li>';
                }
                if(__permissions.indexOf('3') >= 0)
                {
                    instituteHtml += '<li id="status_btn_'+institute['id']+'">';
                    instituteHtml += '        <a href="javascript:void(0);"  onclick="changeUserStatus(\''+institute['id']+'\')" >'+lang(cb_status)+'</a>';
                    instituteHtml += '</li>';
                }      
                if(__permissions.indexOf('4') >= 0)
                {
                    instituteHtml += '<li>';
                    instituteHtml += '       <a href="javascript:void(0);" id="delete_btn_'+institute['id']+'" onclick="deleteInstitute(\''+ institute['id']+'\')">'+lang('delete')+'</a>';
                    instituteHtml += '</li>';
                }                
            }
            else
            {
                if(__permissions.indexOf('4') >= 0)
                {
                    instituteHtml += '<li>';
                    instituteHtml += '    <a href="javascript:void(0);" id="restore_btn_'+institute['id']+'" onclick="restoreInstitute(\''+institute['id']+'\')" >'+lang('restore')+'</a>';
                    instituteHtml += '</li>';
                }                
            }
        instituteHtml += '           </ul>';
        instituteHtml += '        </div>';
        instituteHtml += '    </div>';
        instituteHtml += '    <div class="rTableCell pos-rel active-institute-custom">';
        instituteHtml += '        <span class="active-arrow"></span>';
        instituteHtml += '    </div>';
        
        return instituteHtml;
    }
    
    function getInstituteObjectDetail(institute_id)
    {
        var _institute = {};
        if(Object.keys(__instituteObject).length > 0)
        {
            $.each(__instituteObject, function(key, institute )
            {
                if(institute['id'] == institute_id)
                {
                    _institute = institute;
                    return;
                }
            });    
        }
        return _institute;
    }

    function getInstituteObjectIndex(institute_id)
    {
        if(Object.keys(__instituteObject).length > 0)
        {
            $.each(__instituteObject, function(key, institute )
            {
                if(institute['id'] == institute_id)
                {
                    index = key;
                    return;
                }
            });    
        }
        return index;
    }

    function loadInstituteDetail(institute_id)
    {
        if((institute_id > 0) == false)
        {
            return false;
        }

        var institute             = getInstituteObjectDetail(institute_id);
        var institute_img         = ((institute['ib_image'] == 'default.jpg')?webConfigs('default_institute_path'):webConfigs('institute_path')); 
        var instituteDetailHtml   = '';
        
        $('#institute_detail_wrapper').html('<div class="row center-block"><h2>Loading...</h2></div>');
        $('.active-institute-custom').removeClass('active-table');
        $('#institute_'+institute_id+' .active-institute-custom').addClass('active-table');
            instituteDetailHtml += '<div class="row pattern-bg">';
            instituteDetailHtml += '    <div class="faculty-img pull-left"><span class="icon-wrap-round img"><img src="'+institute_img+''+institute['ib_image']+'"></span></div> ';
            
                        
            instituteDetailHtml += '<div class="faculty-info pull-left">   ';
            instituteDetailHtml += '  <span class="center-block faculty-name wrap"><h1>'+institute['ib_institute_code']+' - '+institute['ib_name']+' </h1></span>';
                
            instituteDetailHtml += '</div>';
            instituteDetailHtml += '</div>';
            instituteDetailHtml += '<div class="row line"></div>';
            
            if(institute['ib_about'] != null && institute['ib_about'] != '' )
            {
                instituteDetailHtml += '<div class="faculty-intro">';
                instituteDetailHtml += '    <h4 class="text-uppercase small-head">'+lang('about_institution')+'</h4>';
                instituteDetailHtml +=  '<p class="wrap">'+institute['ib_about']+'</p>';
                instituteDetailHtml += '</div> ';
            }
            instituteDetailHtml += '<div class="row">';
            instituteDetailHtml += '    <div class="col-sm-12">';
            instituteDetailHtml += '        <ul class="faculty-specs">';
            if(institute['ib_phone'] != null && institute['ib_phone'] != '' )
            {
                instituteDetailHtml += '<li><i class="icon"></i><b>Contact Number</b> : '+institute['ib_phone']+'</li>';
            }
            if(institute['ib_address'] != null && institute['ib_address'] != '' )
            {
                instituteDetailHtml += '            <li><i class="icon"></i><b>Location</b> : '+institute['ib_address']+'</li>';
            }
            
            if(institute['ib_head_name'] != null && institute['ib_head_name'] != '' )
            {
                instituteDetailHtml += '<li><i class="icon"></i><b>'+lang('institute_head_name')+'</b> : '+institute['ib_head_name']+'</li>';
            }

            if(institute['ib_head_email'] != null && institute['ib_head_email'] != '' ){
                instituteDetailHtml += '<li><i class="icon"></i><b>'+lang('institute_head_email')+'</b> : '+institute['ib_head_email']+'</li>';
            }

            if(institute['ib_head_phone'] != null && institute['ib_head_phone'] != '' ){
                instituteDetailHtml += '<li><i class="icon icon-location"></i><b>'+lang('institute_head_phone')+'</b> : '+institute['ib_head_phone']+'</li>';
            }

            if(institute['ib_officer_name'] != null && institute['ib_officer_name'] != '' ){
                instituteDetailHtml += '<li><i class="icon"></i><b>'+lang('nodal_officer_name')+'</b> : '+institute['ib_officer_name']+'</li>';
            }

            if(institute['ib_officer_email'] != null && institute['ib_officer_email'] != '' ){
                instituteDetailHtml += '<li><i class="icon"></i><b>'+lang('nodal_officer_email')+'</b> : '+institute['ib_officer_email']+'</li>';
            }

            if(institute['ib_officer_phone'] != null && institute['ib_officer_phone'] != '' ){
                instituteDetailHtml += '<li><i class="icon icon-location"></i><b>'+lang('nodal_officer_phone')+'</b> : '+institute['ib_officer_phone']+'</li>';
            }

            if(institute['ib_class_code'] != null && institute['ib_class_code'] != '' ){
                instituteDetailHtml += '<li><i class="icon"></i><b>'+lang('classroom_code')+'</b> : '+institute['ib_class_code']+'</li>';
            }
            if(institute['ib_class_strength'] != null && institute['ib_class_strength'] != '' ){
                instituteDetailHtml += '<li><i class="icon"></i><b>Class Room Strength</b> : '+institute['ib_class_strength']+'</li>';
            }
            instituteDetailHtml += '        </ul>    ';
            instituteDetailHtml += '    </div>';
            instituteDetailHtml += '</div> ';
            
        $('#institute_detail_wrapper').html(instituteDetailHtml);
        
        __activeInstitute = institute['id'];
    }
    
    function changeUserStatus(institute_id)
    {
        var institute       = getInstituteObjectDetail(institute_id);
        var afterMath       = 'If you activate the institute, Institute admin can be able to login into the website. Do you wish to proceed?';
        var action          = lang('activate');
        var ok_button_text  = 'ACTIVATE';
        if(institute['ib_status'] == 1)
        {
            ok_button_text  = 'DEACTIVATE';
            afterMath       = 'If you deactivate the institute, Institute admin won\'t be able to login into the website. Do you wish to proceed?';
            action          = lang('deactivate');
        }
        var header_text = 'Are you sure to '+action+' the institute named "'+institute['ib_name']+'"';
        /*$('#confirm_box_title').html(header_text);
        $('#confirm_box_content, #confirm_box_content_1').html('');
        $('#confirm_box_content_1').html(afterMath);
        $('#confirm_box_ok').unbind().html(ok_button_text+'<ripples></ripples>');
        $('#confirm_box_ok').click({"institute_id": institute_id}, changeStatusConfirmed);    */
        var messageObject = {
            'body': header_text,
            'button_yes':ok_button_text, 
            'button_no':'CANCEL',
            'continue_params':{'institute_id':institute_id},
        };
        callback_warning_modal(messageObject, changeStatusConfirmed);    
    }

    function changeStatusConfirmed(params){
        var instituteId = params.data.institute_id;
        $.ajax({
            url: admin_url+'institutes/change_status',
            type: "POST",
            data:{"institute_id":params.data.institute_id, "is_ajax":true},
            success: function(response) {
                clearCache();
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var index                = getInstituteObjectIndex(instituteId);
                    __instituteObject[index] = data['institute'];
                    $('#activate_user').modal('hide');
                    if(__filter_dropdown != 'all')
                    {
                        $('#institute_'+instituteId).remove();
                        if(data['institute']['id'] == __activeInstitute || __activeInstitute == 0)
                        {
                            $('#institute_detail_wrapper').html('');
                        }
                        // var message = (data['message'] == 'Activated')?' activated. ':' deactivated.';
                        // lauch_common_success_message('Updated', 'The institute is successfully '+message); 
                        if( $('.settings-table').length < 1 )
                        {
                            $('#select_all').hide();
                        }
                        else{
                            $('#select_all').show();
                        }

                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadInstitutes();
                        }
                    }
                    else
                    {
                        $('#institute_'+instituteId).html(renderInstituteHtml(__instituteObject[index]));
                        initToolTip();
                    }
                    var messageObject = {
                        'body':'Institute status changed successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);    
                    // filter_institutes_by(__filter_dropdown);
                    ifEmpty();
                    
                }
                else
                {
                    var messageObject = {
                        'body':data['message'],
                        'button_yes':'OK', 
                    };
                    callback_danger_modal(messageObject);
                }
            }
        });
    }

    function restoreInstitute(institute_id)
    {        
        var institute   = getInstituteObjectDetail(institute_id);
        /*$('#confirm_box_title').html();
        $('#confirm_box_content, #confirm_box_content_1').html('');
        $('#confirm_box_ok').unbind().html(+'<ripples></ripples>');
        $('#confirm_box_ok').click({"institute_id": institute_id}, restoreInstituteConfirmed);*/
        var messageObject = {
            'body': 'Are you sure to '+lang('restore')+' institute '+institute['ib_name']+'?',
            'button_yes':lang('RESTORE'), 
            'button_no':'CANCEL',
            'continue_params':{'institute_id':institute_id},
        };
        callback_warning_modal(messageObject, restoreInstituteConfirmed);    

    }

    function restoreInstituteConfirmed(params){
        var institute_id    = params.data.institute_id;
        $.ajax({
            url: admin_url+'institutes/restore',
            type: "POST",
            data:{"institute_id":params.data.institute_id, "is_ajax":true},
            success: function(response) {
                clearCache();
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    var institute_id    = data['institute']['id'];
                    var index           = getInstituteObjectIndex(institute_id);
                    __instituteObject[index] = data['institute'];
                    $('#activate_user').modal('hide');
                    if(__filter_dropdown != 'all')
                    {
                        $('#institute_'+institute_id).remove();
                        if(data['institute']['id'] == __activeInstitute || __activeInstitute == 0)
                        {
                            $('#institute_detail_wrapper').html('');
                        }
                        // lauch_common_success_message('Restored', 'The institute is successfully restored');
                        if( $('.settings-table').length < 1 )
                        {
                            $('#select_all').hide();
                        }
                        else{
                            $('#select_all').show();
                        }
                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadInstitutes();
                        }
                    }
                    else
                    {
                        $('#institute_'+institute_id).html(renderInstituteHtml(__instituteObject[index]));
                        initToolTip();
                    }
                    var messageObject = {
                        'body':'Institute restored successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                    ifEmpty();
                }
                else
                {
                    var messageObject = {
                        'body':data['message'],
                        'button_yes':'OK', 
                    };
                    callback_danger_modal(messageObject);
                }
            }
        });
    }
    function ifEmpty(){

        var nodeLength = $('.settings-table').length;
        if(nodeLength<=0){

            clearCache();
            $('#institute_detail_wrapper').html('');
            $('#institute_wrapper').html(renderPopUpMessagePage('error', 'No Institutes found.'));
            $('#popUpMessagePage .close').css('display', 'none');
        }
    }
    function deleteInstitute(institute_id)
    {
        var institute   = getInstituteObjectDetail(institute_id);
        /*$('#confirm_box_title').html('Are you sure to delete the institute "'+institute['ib_name']+'"?');
        $('#confirm_box_content, #confirm_box_content_1').html('');
        $('#confirm_box_content_1').html('If you delete the institute. However you have an option to restore.');
        $('#confirm_box_ok').unbind().html(lang('DELETE')+'<ripples></ripples>');
        $('#confirm_box_ok').click({"institute_id": institute_id}, deleteInstituteConfirmed);        */
        var messageObject = {
            'body':'Are you sure to delete the institute "'+institute['ib_name']+'"?',
            'button_yes':lang('DELETE'), 
            'button_no':'CANCEL',
            'continue_params':{'institute_id':institute_id},
        };
        callback_warning_modal(messageObject, deleteInstituteConfirmed);
    }

    function deleteInstituteConfirmed(params){
        var institute_id    = params.data.institute_id;
        $.ajax({
            url: admin_url+'institutes/delete',
            type: "POST",
            data:{"institute_id":params.data.institute_id, "is_ajax":true},
            success: function(response) {
                clearCache();
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {                    
                    var index           = getInstituteObjectIndex(institute_id);
                    __instituteObject[index] = data['institute'];
                    $('#activate_user').modal('hide');
                    if(__filter_dropdown != 'all')
                    {
                       var nextId = $('#institute_'+institute_id).next().attr("data-institute-id")
                        $('#institute_'+institute_id).remove();
                        if(data['institute']['id'] == __activeInstitute || __activeInstitute == 0)
                        {
                            $('.settings-table')
                            loadInstituteDetail(nextId);
                            // $('#institute_detail_wrapper').html('');
                        }
                        // lauch_common_success_message('Deleted', 'The institute is successfully deleted');
                        if( $('.settings-table').length < 1 )
                        {
                            $('#select_all').hide();
                        }
                        else{
                            $('#select_all').show();
                        }
                        if( $('.rTableRow').length < 1 ) {
                            __offset = 1;
                            loadInstitutes();
                        }
                    }
                    else
                    {
                        $('#institute_'+institute_id).html(renderInstituteHtml(__instituteObject[index]));
                        initToolTip();
                    }
                    var messageObject = {
                        'body':'Institute deleted successfully',
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                }
                else
                {
                    var messageObject = {
                        'body':data['message'],
                        'button_yes':'OK', 
                    };
                    callback_danger_modal(messageObject);
                }
            }
        });
    }
    
    function sendMessageToInstitute(institute_id)
    {
        $('#invite-user-bulk').modal();
        $('#popUpMessage').hide();
        
        // $('#redactor_invite').redactor('code.set', '');
        $('#redactor_invite').redactor('insertion.set','');
        // $('#redactor_invite').redactor('core.destroy');
        $('#invite_send_subject').val('');
        $('#message_send_button').attr('onclick','sendMessageBulk('+ institute_id +')');
    }
    
    var __sendEmailsBulk = new Array();
    function sendMessageBulk(institute_id)
    {
        institute_id = typeof institute_id != 'undefined' ? institute_id : '';
        var send_user_bulk_subject = $('#invite_send_subject').val();
        var send_user_bulk_message = btoa($('#redactor_invite').val());
        
        var errorCount   = 0;
        var errorMessage = '';
        if ($.trim(send_user_bulk_subject) == '') {
            errorCount++;
            errorMessage += 'Please enter subject<br />';
        }
        if ($.trim(send_user_bulk_message) == '') {
            errorCount++;
            errorMessage += 'Please enter message<br />';
        }
        
        if(errorCount > 0)
        {
            $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', errorMessage));
            scrollToTopOfPage();    
            return false;
        }
        $('#message_send_button').text('SENDING..');
        var institute_ids = [];
        if(__instituteSelected.length > 0)
        {
            institute_ids = __instituteSelected;
        }
        else
        {
            if(institute_id != '')
            {
                institute_ids.push(institute_id);
            }
        }
        $.ajax({
            url: admin_url+'institutes/send_message',
            type: "POST",
            data:{"is_ajax":true, "send_user_subject":send_user_bulk_subject, "send_user_message":send_user_bulk_message, "institute_ids": JSON.stringify(institute_ids)},
            success: function(response) {
                clearCache();
                var data            = $.parseJSON(response);
                if(data['error'] == false)
                {
                    $('#invite-user-bulk').modal('hide');
                    // $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('success', data['message']));                    
                    var messageObject = {
                        'body':data['message'],
                        'button_yes':'OK', 
                    };
                    callback_success_modal(messageObject);
                }
                else{
                    $('#invite-user-bulk .modal-body').prepend(renderPopUpMessage('error', data['message']));
                }
                $('#message_send_button').text('SEND'); 
            
                setTimeout(function(){
                    $('#institute_wrapper .lecture-control').css('visibility', 'visible');
                    $('#invite-user-bulk').modal('hide');               
                }, 2500);
            }
        }); 
        
    }
    
    function deleteInstituteBulk()
    {
        // $('#activate_user').modal('show');
        // $('#confirm_box_title').html('Are you sure to '+lang('delete_selected_institutes')+'?');
        // $('#confirm_box_content, #confirm_box_content_1').html('');
        // $('#confirm_box_ok').unbind().html('DELETE<ripples></ripples>');;
        // $('#confirm_box_ok').click({}, deleteInstituteBulkConfirmed);    
        var messageObject = {
            'body':'Are you sure to '+lang('delete_selected_institutes')+'?',
            'button_yes':'DELETE', 
            'button_no':'CANCEL',
        };
        callback_warning_modal(messageObject, deleteInstituteBulkConfirmed);
    }

    function deleteInstituteBulkConfirmed(){
        $.ajax({
            url: admin_url+'institutes/delete_institutes_bulk',
            type: "POST",
            data:{"institutes":JSON.stringify(__instituteSelected), "is_ajax":true},
            success: function(response) {
                clearCache();
                var data            = $.parseJSON(response);
                var totalinstitutes = Object.keys(data['institutes']).length;
                if(Object.keys(data['institutes']).length > 0 )
                {
                    $.each(data['institutes'], function(instituteKey, institute )
                    {
                        var index   = getInstituteObjectIndex(institute['id']);
                        __instituteObject[index] = institute;
                        
                        if(__filter_dropdown != 'all')
                        {
                            $('#institute_'+institute['id']).remove();    
                            if(institute['id'] == __activeInstitute || __activeInstitute == 0)
                            {
                                $('#institute_detail_wrapper').html('');
                            }          
                            if( $('.rTableRow').length < 1 ) {
                                __offset = 1;
                                loadInstitutes();
                            }              
                        }
                        else
                        {
                            $('#institute_'+institute['id']).html(renderInstituteHtml(institute));
                        }
                    });
                        // lauch_common_success_message('Deleted', 'The institutes successfully deleted');
                        var messageObject = {
                            'body':'Selected '+((totalinstitutes>1)?'institutes':'institute')+' deleted successsfully.',
                            'button_yes':'OK', 
                        };
                        callback_success_modal(messageObject);    
                        filter_institutes_by(__filter_dropdown);              
                }
                $('#institute_wrapper .lecture-control').css('visibility', 'visible');
                // $('#activate_user').modal('hide');
            }
        });
    }
    
    function changeStatusBulk(status)
    { 
        var ok_button_text  = 'DEACTIVATE';
        if( status == 1)
        {
            ok_button_text  = 'ACTIVATE';
        }
        // $('#activate_user').modal('show');
        // $('#confirm_box_title').html('Are you sure to '+lang(((status==1)?'activate':'deactivate')+'_selected_institutes')+'?');
        // $('#confirm_box_content, #confirm_box_content_1').html('');
        // $('#confirm_box_ok').unbind().html(ok_button_text+'<ripples></ripples>');
        // $('#confirm_box_ok').click({'status':status}, changeStatusBulkConfirmed);    
        var messageObject = {
            'body':'Are you sure to '+lang(((status==1)?'activate':'deactivate')+'_selected_institutes')+'?',
            'button_yes':ok_button_text, 
            'button_no':'CANCEL',
            'continue_params':{'status':status},
        };
        callback_warning_modal(messageObject, changeStatusBulkConfirmed);
    }

    function changeStatusBulkConfirmed(param){
        $.ajax({
            url: admin_url+'institutes/change_status_bulk',
            type: "POST",
            data:{"institutes":JSON.stringify(__instituteSelected), "is_ajax":true, 'status':param.data.status},
            success: function(response) {
                clearCache();
                var data            = $.parseJSON(response);
                var totalinstitutes = Object.keys(data['institutes']).length;
                if(Object.keys(data['institutes']).length > 0 )
                {
                    $.each(data['institutes'], function(instituteKey, institute )
                    {
                        var index                = getInstituteObjectIndex(institute['id']);
                        __instituteObject[index] = institute;
                        if(__filter_dropdown != 'all')
                        {
                            $('#institute_'+institute['id']).remove();    
                            if(institute['id'] == __activeInstitute || __activeInstitute == 0)
                            {
                                $('#institute_detail_wrapper').html('');
                            }     
                            if( $('.rTableRow').length < 1 ) {
                                __offset = 1;
                                loadInstitutes();
                            }                   
                        }
                        else
                        {
                            $('#institute_'+institute['id']).html(renderInstituteHtml(institute));
                        }
                    });
                        var messageObject = {
                            'body':'Selected '+((totalinstitutes>1)?'institutes':'institute')+' status changed successsfully.',
                            'button_yes':'OK', 
                        };
                        callback_success_modal(messageObject);
                        // var message = (param.data.status == '0')?' Deactivated':' Activated.';
                        // lauch_common_success_message('Updated', 'The institutes successfully'+message);
                        filter_institutes_by(__filter_dropdown);
                }
                $('#institute_wrapper .lecture-control').css('visibility', 'visible');
                // $('#activate_user').modal('hide');
            }
        });
    }

    function validateEmail(email)
    {
        var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
        if (filter.test(email)) {
            return true;
        }
        else {
            return false;
        }
    }

    $(document).on('change', '.institute-course', function(){
        if( $(this).prop('checked') == true)
        {
            __courseSelected.push($(this).val());
        }
        else
        {
            removeArrayIndex(__courseSelected, $(this).val());        
        }
    });
    
    $(document).on('change', '.institute-checkbox', function(){
        var institute_id = $(this).val();
        if($(this).is(':checked'))
        {
            __instituteSelected.push(institute_id);
            if(__instituteSelected.length > 1)
            {
                $('#institute_wrapper .lecture-control').css('visibility', 'hidden');
            }
            if($('.institute-checkbox').not(':disabled').length == $('.institute-checkbox:checked').not(':disabled').length)
            {
                $('.institute-checkbox-parent').prop('checked', true);
            }
        }
        else
        {
            removeArrayIndex(__instituteSelected, institute_id);
            $('.institute-checkbox-parent').prop('checked', false);
            if(__instituteSelected.length <= 1)
            {
                $('#institute_wrapper .lecture-control').css('visibility', 'visible');
            }
        }
        
        $("#selected_institutes_count").html('');
        $('#bulk_action_wrapper').hide();
        
        
        if(__instituteSelected.length > 0){
            $("#selected_institutes_count").html(' ('+__instituteSelected.length+')');
        }else{
            $("#selected_institutes_count").html('');
        }
        
        if(__instituteSelected.length > 1){
            $("#bulk_action_wrapper").css('display','block');
        }else{
            $("#bulk_action_wrapper").css('display','none');
        }
    });

    $(document).on('change', '.institute-checkbox-parent', function(){
        __instituteSelected = new Array();
        $("#selected_institutes_count").html('');
        if($('.institute-checkbox-parent').is(':checked'))
        {
            $('.institute-checkbox').not(':disabled').each(function( index, value ) {
                __instituteSelected.push($(this).val());
            });
            $('#institute_wrapper .lecture-control').css('visibility', 'hidden');
            $('.institute-checkbox').not(':disabled').prop('checked', true);
            $("#selected_institutes_count").html(' ('+__instituteSelected.length+')');
            if(__instituteSelected.length>1){
                $('#bulk_action_wrapper').show();
            }            
        }
        else
        {
            $('#institute_wrapper .lecture-control').css('visibility', 'visible');
            $('.institute-checkbox').prop('checked', false);
            $('#bulk_action_wrapper').hide();
        }
    });

    $(document).ready(function() {
        __instituteObject      = $.parseJSON(__instituteObject);
        clearCache();
        if(Object.keys(__instituteObject).length == 0 )
        {
            $('#select_all').hide();
            $('#institute_wrapper').html(renderPopUpMessagePage('error', 'No Institutes found.'));
            $('#popUpMessagePage .close').css('display', 'none');
        }
        else{
            $('#institute_wrapper').html(renderInstitutesHtml(__instituteObject));
            initToolTip();
            loadInstituteDetail(__activeInstitute);
            $(function(){
                $('.right-box').slimScroll({
                        height: '100%',
                        wheelStep : 3,
                        distance : '10px'
                });
            });
        }        
   });
  
    function clearCache()
    {
        __instituteSelected   = new Array();
        __courseSelected    = new Array();
        $('.institute-checkbox-parent').prop('checked', false);
        $('#selected_institutes_count').html('');
        $('#bulk_action_wrapper').hide();
    }
    function rating(selector, rate)
    {
        $(selector).rateYo({
            starWidth: "18px",
            rating: rate,
            readOnly: true,
            ratedFill : '#d94d38',
            normalFill : '#000000'
        });
    }
    
    function generatePassword()
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < 8; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        $('#institute_password').val(text);
    }
    
    $(function()
    {
        startTextToolbar();
    });

    function startTextToolbar()
    {
        $('#redactor_invite').redactor({
            imageUpload: admin_url+'configuration/redactore_image_upload',
            source: false,
            plugins: ['table', 'alignment'],
            callbacks: {
                imageUploadError: function(json, xhr)
                {
                     alert('Please select a valid image');
                     return false;
                }
            }   
        });
    }

    var __uploaded_files    = '';
    $(document).on('change', '#import_institute', function(e){
      $('#percentage_bar').hide();
      __uploaded_files                = e.currentTarget.files[0];
      __uploaded_files['extension']   = __uploaded_files['name'].split('.').pop();
      $('#upload_institute_file').val(__uploaded_files['name']);
    });
    
    function uploadInstitute()
    {
        if(__uploaded_files=='')
        {
            lauch_common_message('File missing', 'Please choose file to upload.');
            return false;
        }
        
        var filename = __uploaded_files['name'];
        var valid_extensions = /(\.csv)$/i;   
        
        if(valid_extensions.test(filename))
        {
            $('#percentage_bar').show();
            var uploadURL                   = admin_url+'institutes/import_institutes'
            var fileObj                     = new processFileName(__uploaded_files['name']);
            var param                       = new Array;
                param["file_name"]          = fileObj.uniqueFileName();        
                param["extension"]          = fileObj.fileExtension();
                param["file"]               = __uploaded_files;
            uploadFiles(uploadURL, param, uploadUserCompleted); 
        }
        else
        {
           lauch_common_message('Invalid File', 'Choose .csv file to upload');
           return false;
        }
    }

    function uploadUserCompleted(response)
    {
        var response = $.parseJSON(response);
        __uploading_file = '';
        
        switch(response.status)
        {
            case 1:
                $('#import-institutes .modal-body').prepend(renderPopUpMessage('success', response.message));
                setTimeout(function(){window.location.reload();}, 2500);
            break;
            case 2:
                $('#import-institutes .modal-body').prepend(renderPopUpMessage('error', response.message));
                setTimeout(function(){
                    location.href = response['redirect_url'];
                }, 500) ;
                /*$(window).on('beforeunload', function(){
                    return 'Please allow system to refresh the page inorder to get inserted contents.';
                });*/              
            break;
            case 3:
                $('#import-institutes .modal-body').prepend(renderPopUpMessage('error', response.message));
            break;
        }
        $('#percentage_bar').hide();
        scrollToTopOfPage();
    }

    $(document).on('hidden.bs.modal', '#import-institutes', function(){
        __uploaded_files = '';
        $(this).find("input").val('').end();
        $('#percentage_bar').hide();
        $('#popUpMessage').remove();
    });


    function exportInstitutes() {
        var param           = {};
        param['keyword']    = $.trim($('#institute_keyword').val());
        param['filter']     = __filter_dropdown;
        location.href       = admin_url+'institutes/export_institutes/'+btoa(JSON.stringify(param));
    }