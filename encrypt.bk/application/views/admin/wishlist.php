<?php include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<section class="content-wrap create-group-wrap settings-top">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="col-sm-12 pad0 settings-left-wrap">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content width-130p nav-js-height">
            <div class="row">
                <div class="rTable content-nav-tbl" style="">
                    <div class="rTableRow">
                        <div class="rTableCell">
                            <div class="input-group">
                                <input class="form-control srch_txt" placeholder="Search Course" id="group_keyword" type="text">
                                <a class="input-group-addon" id="search_group" >
                                    <i class="icon icon-search"> </i>
                                </a>
                            </div> 
                        </div>
                        <div class="rTableCell">
                            <!-- lecture-control start -->
                            <a href="javascript:void(0)" onclick="ExportAllWishlist(0)" type="button" class="btn btn-green selected">Export All<ripples></ripples></a>
                            <!-- lecture-control end -->
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->

        <!-- Group content section  -->
        <!-- ====================== -->

        <div class="col-sm-12 group-content course-cont-wrap group-top wishlist-top"> 
            <div class="table course-cont rTable" style="" id="group_wrapper">
                
            </div>
        </div>
        <!-- ====================== -->
        <!-- Group content section  -->
    </div>

    <div class="col-sm-6 pad0 right-content">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid nav-content no-nav-style nav-js-height width-130p" id="preview_wrapper">
                    <div class="col-sm-12">
                        <div class="rTable content-nav-tbl" style="">
                            <div class="rTableRow">
                                <div class="rTableCell txt-left">
                                    <a href="javascript:void(0)" class="select-all-style"><label> <input class="user-checkbox-parent" type="checkbox">  Select All</label><span id="selected_users_count"></span></a>
                                </div>
                                <div class="rTableCell" >
                                    <!-- lecture-control start -->
                                    <div class="btn-group lecture-control btn-right-align" id="bulk_action_wrapper" style="display:none;margin-top: 0px;">
                                        <span class="dropdown-tigger" data-toggle="dropdown">
                                            <span class='label-text'>
                                               <b>Bulk Action </b> <?php /* ?><span class="icon icon-down-arrow"></span><?php */ ?>
                                            </span>
                                            <span class="tilder"></span>
                                        </span>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <li>
                                                <a href="javascript:void(0)" onclick="sendMessageToUser(0)">Send Message</a> 
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- lecture-control end -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <!-- Nav section inside this wrap  --> <!-- END -->
        <!-- =========================== -->
        <div class="container-fluid right-box">
            <div class="row">
                <div class="col-sm-12 rel-top80 course-cont-wrap"> 
                    <div class="table course-cont rTable" style="" id="group_detail_wrapper">
                    </div>

                </div>
            </div>
        </div>

    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
var __userSelected  = new Array();
var __disabled      = 'disabled="disabled"';
var __checked       = 'checked="checked"';
var __groupObject   = new Array;
    __groupObject   = atob('<?php echo base64_encode(json_encode($wishlist)) ?>');
var __keyword       = '';
var __count = 0;
var __courseKeyword = '<?php echo (($keyword)?$keyword:'') ?>';
</script>
<script>
    var __activeGroup = 0;
    $(document).ready(function(){
        __groupObject      = $.parseJSON(__groupObject);
        $('#group_wrapper').html(renderGroupsHtml(__groupObject));
        if(Object.keys(__groupObject).length == 0)
        {
            $('#group_wrapper').html(renderPopUpMessage('error', 'No courses found.'));
        }
        loadGroupDetail(__activeGroup);
    });
    
    function renderGroupsHtml(groups)
    {
        __count = 0;
        clearCache();
        var groupsHtml  = '';
        if(Object.keys(groups).length > 0 )
        {
            $.each(groups, function(groupKey, group )
            {
                __count++;
                if(Object.keys(group['users']).length != 0)
                {
                    groupsHtml += '<div class="rTableRow" id="group_'+group['id']+'">';
                    groupsHtml += renderGroupHtml(group);
                    groupsHtml += '</div>';
                    __activeGroup = (__activeGroup == 0)?group['id']:__activeGroup;
                }
            });
        }
        return groupsHtml;
    }
    
    function renderGroupHtml(group)
    {
        var groupHtml  = '';
            groupHtml += '    <div class="rTableCell"> ';
            groupHtml += '        <span class="icon-wrap-round blue">';
            groupHtml += '            <i class="icon icon-heart"></i>';
            groupHtml += '        </span>';
            groupHtml += '        <span><a href="javascript:void(0)" onclick="loadGroupDetail('+group['id']+')" class="normal-base-color grp-click-fn">';
            groupHtml += '                <span class="font-bold">'+group['cb_title']+' -</span> ';
            groupHtml += '                <span class="label-active group-total">'+Object.keys(group['users']).length+'</span>';
            if(Object.keys(group['users']).length>1){
                groupHtml += 'Students';
            }else{
                groupHtml += 'Student';
            }
            groupHtml += '            </a>';
            groupHtml += '        </span>';
            groupHtml += '    </div>';
            groupHtml += '    <div class="td-dropdown rTableCell">';
            groupHtml += '        <div class="btn-group lecture-control">';
            groupHtml += '            <span class="dropdown-tigger" data-toggle="dropdown" aria-expanded="false">';
            groupHtml += '                 <span class="label-text">';
            groupHtml += '                  <i class="icon icon-down-arrow"></i>';
            groupHtml += '                </span>';
            groupHtml += '                <span class="tilder"></span>';
            groupHtml += '            </span>';
            groupHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
            groupHtml += '                <li>';
            groupHtml += '                      <a href="javascript:void(0)" onclick="sendMessageToUserFromGroup('+group['id']+')">Send Message</a>';
            groupHtml += '                </li>';
            groupHtml += '                <li>';
            groupHtml += '                    <a href="javascript:void(0)" onclick="ExportAllWishlist('+group['id']+')">Export</a>';
            groupHtml += '                </li>';
            groupHtml += '            </ul>';
            groupHtml += '        </div>';
            groupHtml += '    </div>';
            groupHtml += '    <div class="rTableCell pos-rel active-user-custom">';
            groupHtml += '        <span class="active-arrow group-arrow" style=""></span>';
            groupHtml += '    </div>';
        return groupHtml;
    }
    
    function renderAttachUserFormInit(group_id)
    {
        __activeGroup = group_id; 
        renderAttachUserForm();
        loadGroupDetail(__activeGroup);
    }
    
    var __searchGroupsTimeOut = '';
    $(document).on('keyup', '#group_keyword', function(){
        clearTimeout(__searchGroupsTimeOut);
        __searchGroupsTimeOut = setTimeout(function(){
            filterGroups($('#group_keyword').val());
        }, 300);
    });
    $(document).on('click', '#search_group', function(){
        filterGroups($('#group_keyword').val());
    });
    
    function filterGroups(groupKeyword)
    {
        clearCache();
        __activeGroup       = 0;
        var groupsHtml      = '';
        var keyword         = groupKeyword.toLowerCase();
        __keyword           = keyword;
        if(keyword == '')
        {
            groupsHtml = renderGroupsHtml(__groupObject)
        }
        else
        {
            if(Object.keys(__groupObject).length > 0 )
            {
                __count = 0;
                $.each(__groupObject, function(groupKey, group )
                {
                    if(!(group['cb_title'].toLowerCase().indexOf(keyword) == -1) == true  )
                    {
                        __count++;
                        groupsHtml += '<div class="rTableRow" id="group_'+group['id']+'">';
                        groupsHtml += renderGroupHtml(group);
                        groupsHtml += '</div>';
                        __activeGroup = (__activeGroup == 0)?group['id']:__activeGroup;                    
                    }
                });
            }  
        }
        $('#group_detail_wrapper').html('');
        $('#preview_wrapper').hide();
        if(groupsHtml == ''){
            $('#group_wrapper').html(renderPopUpMessage('error', 'Sorry no result found for search.'));
        }else{
            $('#group_wrapper').html(groupsHtml);
        }
        if(__activeGroup > 0 )
        {
            loadGroupDetail(__activeGroup);
        }
    }
    
    function loadGroupDetail(group_id)
    {
        clearCache();
        $('#preview_wrapper').hide();
        var group             = (typeof __groupObject[group_id] != 'undefined')?__groupObject[group_id]:new Object;
        var groupDetailHtml   = '';
        var user_img          = '';
        $('.active-user-custom').removeClass('active-table');
        $('#group_'+group_id+' .active-user-custom').addClass('active-table');
            //console.log(__groupObject);
            if(typeof group['users'] != 'undefined' && Object.keys(group['users']).length > 0 )
            {
                $('#group_detail_wrapper').html('<div class="row center-block"><h2>Loading...</h2></div>');
                $.each(group['users'], function(userKey, user )
                {
                    var user_img     = ((user['us_image'] == 'default.jpg')?webConfigs('default_user_path'):webConfigs('user_path')); 
                    groupDetailHtml += '<div class="rTableRow" id="group_user_'+user['id']+'">';
                    groupDetailHtml += '    <div class="rTableCell"> ';
                    groupDetailHtml += '        <input type="checkbox" class="user-checkbox" id="user_checkbox_'+user['id']+'" value="'+user['id']+'" '+((user['us_deleted'] == 1)?__disabled:'')+'> ';
                    groupDetailHtml += '        <span class="icon-wrap-round img">';
                    groupDetailHtml += '            <img src="'+user_img+''+user['us_image']+'">';
                    groupDetailHtml += '        </span>';
                    groupDetailHtml += '        <span class="wrap-mail"> ';
                    groupDetailHtml += '            <a href="'+webConfigs('admin_url')+'user/profile/'+user['id']+'" target="_blank">'+user['us_name']+'</a> <br>';
                    groupDetailHtml += '            '+user['us_email'];
                    groupDetailHtml += '        </span>';
                    groupDetailHtml += '    </div>';
                    groupDetailHtml += '    <div class="td-dropdown rTableCell">';
                    groupDetailHtml += '        <div class="btn-group lecture-control">';
                    groupDetailHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
                    groupDetailHtml += '                 <span class="label-text">';
                    groupDetailHtml += '                  <i class="icon icon-down-arrow"></i>';
                    groupDetailHtml += '                </span>';
                    groupDetailHtml += '                <span class="tilder"></span>';
                    groupDetailHtml += '            </span>';
                    groupDetailHtml += '            <ul class="dropdown-menu pull-right" role="menu">';
                    groupDetailHtml += '                <li>';
                    groupDetailHtml += '                    <a href="javascript:void(0)" onclick="sendMessageToUser(\''+user['id']+'\')">Send Message</a>';
                    groupDetailHtml += '                </li>';
                    groupDetailHtml += '            </ul>';
                    groupDetailHtml += '        </div>';
                    groupDetailHtml += '    </div>';
                    groupDetailHtml += '</div> ';                    
                });
                $('#preview_wrapper').show();
            }
        $('#group_detail_wrapper').html(groupDetailHtml);
        __activeGroup = group['id'];
    }
    
    $(document).on('change', '.user-checkbox', function(){
        var user_id = $(this).val();
        if($(this).is(':checked'))
        {
            __userSelected.push(user_id);
            if($('.user-checkbox').not(':disabled').length == $('.user-checkbox:checked').not(':disabled').length)
            {
                $('.user-checkbox-parent').prop('checked', true);
            }
        }
        else
        {
            removeArrayIndex(__userSelected, user_id);
            $('.user-checkbox-parent').prop('checked', false);
        }
        
        $("#selected_users_count").html('');
        $('#bulk_action_wrapper').hide();
        if(__userSelected.length > 0)
        {
            $("#selected_users_count").html(' ('+__userSelected.length+')');
            $('#bulk_action_wrapper').show();
        }
    });

    $(document).on('change', '.user-checkbox-parent', function(){
        __userSelected = new Array();
        $("#selected_users_count").html('');
        if($('.user-checkbox-parent').is(':checked'))
        {
            $('.user-checkbox').not(':disabled').each(function( index, value ) {
                __userSelected.push($(this).val());
               
            });
            $('.user-checkbox').not(':disabled').prop('checked', true);
            $("#selected_users_count").html(' ('+__userSelected.length+')');
            $('#bulk_action_wrapper').show();
        }
        else
        {
            $('.user-checkbox').prop('checked', false);
            $('#bulk_action_wrapper').hide();
        }
    });
    
    /*        $('#group_wrapper, #group_detail_wrapper').slimScroll({
                height: '100%',
                wheelStep : 3,
                distance : '10px'
        });
*/  
    
    
    
    function renderAttachUserForm()
    {
        $('.users-to-add-in-new-group').remove();
        $('#user_keyword').val('');
        $('#course_group_create').html('Attach user to group - '+__groupObject[__activeGroup]['gp_name']);
        $('#attach-group-users').modal();
        $('#create_group_users').unbind();
        $('#create_group_users').click({}, attachUsers);    
    }
    
    function attachUsers(param)
    {
        if(__userSelected.length == 0)
        {
            $('#add-user-to-group .modal-body').prepend(renderPopUpMessage('error', 'Please select atleast one user to group'));
            scrollToTopOfPage();
            return false;
        }
        $.ajax({
            url: admin_url+'groups/save_group_users',
            type: "POST",
            data:{"is_ajax":true, "group_id":__activeGroup, "user_ids":JSON.stringify(__userSelected)},
            success: function(response) {
                var data                                = $.parseJSON(response);                
                __groupObject[__activeGroup]['users']   = data['group_users'];
                $('#group_'+__activeGroup).html(renderGroupHtml(__groupObject[__activeGroup]));
                loadGroupDetail(__activeGroup);
                $('#attach-group-users').modal('hide');
            }
        });
    } 
    
    var __searchUsersTimeOut = '';
    $(document).on('keyup', '#user_keyword', function(){
        clearTimeout(__searchUsersTimeOut);
        __searchUsersTimeOut = setTimeout(function(){
            searchUser($('#user_keyword').val());
        }, 300);
    });
    
    $(document).on('click', '#user_keyword_btn', function(){
        searchUser($('#user_keyword').val());
    });

    function searchUser(userKeyword)
    {
        $('.users-to-add-in-new-group').remove();
        $('#users_new_group_wrapper').append('<div class="checkbox-wrap users-to-add-in-new-group"><span class="chk-box"><label class="font14">Loading...</label></span></div>');
        var userHtml    = '';
        var keyword     = userKeyword.toLowerCase();
        $.ajax({
            url: admin_url+'groups/users',
            type: "POST",
            data:{"is_ajax":true, "keyword":keyword, "exclude_group_id":__activeGroup, 'not_deleted':'1'},
            success: function(response) {
                var data        = $.parseJSON(response);
                var userHtml    = '';
                $('.users-to-add-in-new-group').remove();
                if(data['users'].length > 0 )
                {
                    for (var i=0; i<data['users'].length; i++)
                    {
                        userHtml += '<div class="checkbox-wrap users-to-add-in-new-group" id="user_new_group_'+data['users'][i]['id']+'">';
                        userHtml += '    <span class="chk-box">';
                        userHtml += '        <label class="font14">';
                        userHtml += '            <input type="checkbox" '+((inArray(data['users'][i]['id'], __userSelected) == true)?__checked:'')+' value="'+data['users'][i]['id']+'" class="select-users-new-group">';
                        userHtml += '            '+data['users'][i]['us_name']+'';
                        userHtml += '        </label>';
                        userHtml += '    </span>';
                        userHtml += '    <span class="email-label pull-right">';
                        userHtml += '        <span>'+data['users'][i]['us_email']+'</span>';
                        userHtml += '    </span>';
                        userHtml += '</div>';
                    }
                    $('#users_new_group_wrapper').append(userHtml);
                }
            }
        });
    }
    
    var __userSelected = new Array();
    $(document).on('click', '.select-users-new-group', function(){
        var user_id = $(this).val();
        if ($(this).is(':checked')) {
            __userSelected.push(user_id);
        }else{
            removeArrayIndex(__userSelected, user_id);
        }
    });

    $(document).on('click', '.select-users-new-group-parent', function(){
        var parent_check_box = this;
        __userSelected = new Array();    
        $( '.select-users-new-group' ).prop('checked', $(parent_check_box).is(':checked'));
        if ($(parent_check_box).is(':checked') == true) {
            $( '.select-users-new-group' ).each(function( index ) {
               __userSelected.push($( this ).val());
            });
        }
    });
    
    
    
    function sendMessageToUser(user_id)
    {
        $('#popUpMessage').remove();
        if(user_id > 0 )
        {
            __userSelected = new Array();
            __userSelected.push(user_id);
            $('.user-checkbox, .user-checkbox-parent').prop('checked', false);
            $('#user_checkbox_'+user_id).prop('checked', true);
            $("#selected_users_count").html(' ('+__userSelected.length+')');
        }
        
        if(__userSelected.length == 0 )
        {
            return false;
        }
        
        $('#invite-user-bulk').modal();
        $('#tokenize_invite').html('');
        $('#tokenize_invite').tokenize().clear();
        
        var group = __groupObject[__activeGroup];
        if(typeof group['users'] != 'undefined' && Object.keys(group['users']).length > 0 )
        {
           $.each(group['users'], function(userKey, user )
           {
               if(inArray(user['id'], __userSelected) == true)
               {
                    $('#tokenize_invite').tokenize().tokenAdd(user['us_email'], user['us_email']); 
               }
           });
        }

        $('#redactor_invite').html('');
        $('#email_template_list_invite_message').val('0');
        $('#invite_send_subject').val('');
        $.ajax({
            url: admin_url+'user/get_email_templates',
            type: "POST",
            data:{"is_ajax":true},
            success: function(response) {
                var renderEmailTemplate = '';
                var data                = $.parseJSON(response);
                if(data['mail_template'].length > 0){
                        renderEmailTemplate += '<option value="0">Select Template</option>'
                    for (var i=0; i < data['mail_template'].length; i++){  
                        renderEmailTemplate += '<option value="'+data['mail_template'][i]['id']+'">'+data['mail_template'][i]['et_name']+'</option>';
                    }
                }
                $('#email_template_list_invite_message').html(renderEmailTemplate);
            }
        });
    }
    
    function sendMessageToUserFromGroup(group_id)
    {
        loadGroupDetail(group_id);
        __userSelected    = new Array();
        var group = __groupObject[group_id];
        if(typeof group['users'] != 'undefined' && Object.keys(group['users']).length > 0 )
        {
            $.each(group['users'], function(userKey, user )
            {
                __userSelected.push(user['id']);
            });
            sendMessageToUser(0);
        }
    }
    
    $(document).on('change', '#email_template_list_invite_message', function(){
        var template_id = $(this).val();
        $.ajax({
            url: admin_url+'user/get_template_data',
            type: "POST",
            data:{"is_ajax":true, "mail_template_id":template_id},
            success: function(response) {
                var data                    = $.parseJSON(response);            
                var renderEmailTemplateData = atob(data['mail_template_data']['et_body']);
                $('#redactor_invite').redactor('code.set', renderEmailTemplateData);  
            }
        }); 
    });
    
    var __sendEmailsBulk = new Array();
    function sendMessageBulk()
    {
        var send_user_bulk_subject = $('#invite_send_subject').val();
        var send_user_bulk_message = btoa($('#redactor_invite').val());
        var send_user_bulk_emails  = JSON.stringify($('#tokenize_invite').val());
        __sendEmailsBulk           = $('#tokenize_invite').val();

        var errorCount   = 0;
        var errorMessage = '';
        if ($.trim(__sendEmailsBulk) != '')
        {
            $.each(__sendEmailsBulk, function( index, value ) {
                if (!validateEmail(value)) {
                    errorCount++;
                    errorMessage = 'Invalid email id<br />';
                }
            });
        }
        else
        {
            errorCount++;
            errorMessage += 'Email id cannot be empty<br />';
        }
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
        $.ajax({
            url: admin_url+'user/send_message_user',
            type: "POST",
            data:{"is_ajax":true, "send_user_subject":send_user_bulk_subject, "send_user_message":send_user_bulk_message, "send_user_emails":send_user_bulk_emails},
            success: function(response) {
                $('#invite-user-bulk').modal('hide');
                $('#message_send_button').text('SEND');
                __userSelected    = new Array();
            }
        }); 
    }
    
    $(document).on('click', '#message_send_button_cancel, #invite-user-bulk .close', function(){
        __userSelected    = new Array();        
        $('.user-checkbox').prop('checked', false);
        $('#selected_users_count').html('');
    });
    
    function clearCache()
    {
        __userSelected    = new Array();
        $('.user-checkbox-parent').prop('checked', false);
        $('#selected_users_count').html('');
        $('#bulk_action_wrapper').hide();        
    }
    
    $(function()
    {
        startTextToolbar();
        var dec_base = atob(__courseKeyword);
        $('#group_keyword').val(dec_base);
        $('#search_group').trigger('click');
    });

    function startTextToolbar()
    {
        $('#redactor_invite').redactor({
            imageUpload: admin_url+'configuration/redactore_image_upload',
            plugins: ['table', 'alignment', 'source'],
            callbacks: {
                imageUploadError: function(json, xhr)
                {
                     alert('Please select a valid image');
                     return false;
                }
            }   
        });
    }
    
    function ExportAllWishlist(id){
        var admin_url = webConfigs('admin_url');
        var toencode = admin_url+'wishlist/export/';
        var encode = btoa('#'+id+'#'+__keyword);
        var url = toencode+encode;
        if(__count > 0){
            var win = window.open(url, '_blank');
            win.focus();
        }else{
            $('#group_wrapper').html(renderPopUpMessage('error', 'No data selected to export.'));
        }

    }
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
$(document).ready(function(e) {
    $(function(){
        $('.right-box').slimScroll({
            height: '100%',
            width: '100%',
            wheelStep : 3,
            distance : '10px'
        });
    });
});
</script>
<?php include_once 'footer.php'; ?>
