<?php //echo '<pre>';print_r($session);die;
include_once "header.php"; ?>
<section>
    <div class="my-profile-strip">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="row" id="profile-details">
                    <div class="col-md-2 col-sm-2 col-sx-12">
                        <div class="my-profile-wrap">
                            <?php $user_img = (($session['us_image'] == 'default.jpg') ? default_user_path() : user_path()); ?>
                            <img class="my-profile-pic" id="my_profile_picture" src="<?php echo $user_img . $session['us_image'] ?>" alt="<?php echo $session['us_name'] ?>"/>
                            <span class="brows-img">
                                <span class="svg-cam-holder">
                                    <svg  x="0px" y="0px" viewBox="0 0 30.3 27.2" enable-background="new 0 0 30.3 27.2" xml:space="preserve">
                                    <g>
                                    <circle fill="#FFFFFF" cx="15.1" cy="15.1" r="4.8"/>
                                    <path fill="#FFFFFF" d="M10.6,0L7.8,3H3c-1.7,0-3,1.4-3,3v18.2c0,1.7,1.4,3,3,3h24.2c1.7,0,3-1.4,3-3V6.1c0-1.7-1.4-3-3-3h-4.8
                                          l-2.8-3H10.6z M15.1,22.7c-4.2,0-7.6-3.4-7.6-7.6s3.4-7.6,7.6-7.6s7.6,3.4,7.6,7.6S19.3,22.7,15.1,22.7z"/>
                                    </g>
                                    </svg>
                                </span>
                                <span class="cam-text" id="upload_button_text">Change</span>
                                <input type="file"  class="my-profile-brows" id="us_image">
                            </span>
                        </div><!--my-profile-wrap-->   
                    </div><!--columns-->
                    <div class="content-holder">
                        <div class="col-md-5 col-sm-4 col-xs-12">
                            <span class="my-profile-name" id="my_profile_name"><?php echo $session['us_name'] ?></span>
                            <?php
                                $badge = "Student";
                            ?>
                            <span class="my-profile-connection"><span class="my-profile-int"></span><?php echo $badge?> <?php //echo (isset($session['institute_name']))?'-'.$session['institute_name']:" "; ?></span>
                            <?php if (isset($session['courses_enrolled']) && $session['courses_enrolled'] != ''): ?>
                                <span class="my-profile-enroll">Enrolled in <span class="my-profile-civil"><?php echo $session['courses_enrolled'] ?></span></span>
                            <?php endif; ?>
                        </div><!--columns-->

                        <div class="col-md-5 col-sm-6 col-xs-12">
                            <div class="outline-btn-wrap edit-profile-wrapper">
                                <a href="javascript:void(0)" class="outline-btn" data-toggle="modal"  data-keyboard="true" id="change_password_button" data-target="#teachers-change">Change Password</a>
                                <a href="javascript:void(0)" class="outline-btn edit-profile" onclick="editProfile()">Edit Profile</a>
                            </div><!--outline-btn-wrap-->
                        </div><!--columns-->
                    </div><!--content-holder-->

                    <div class="col-md-10  second-item-holder">
                        <div class="row">
                            <div class="col-md-8">
                                <span class="name-placer-input-mask">
                                    <input class="name-holder-input" id="user_full_name" type="text">
                                </span>
                            </div>
                            <div class="col-md-4 text-right center-xs">
                                <!-- <a href="javascript:void(0)" class="btn  btn-orange2 my-profile-btn" id="save_profile_button" onclick="saveProfile()">Save Changes</a> -->
                                <button class="btn btn-orange2 my-profile-btn" onclick="cancelProfile()">Cancel</button>
                                <button class="btn btn-success my-profile-btn" onclick="saveProfile()">Save Changes</button>
                            </div>
                        </div>
                    </div>


                </div><!--row--> 


            </div><!--container-reduce-width-->
        </div><!--container container-altr-->
    </div><!--my-profile-strip-->
</section>

<section>
    <div class="my-profile-blocks">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <h3 class="biography-text">Overview</h3>
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="myprofile-cards-wraper">
                            <div class="myprofile-card-head">
                                <span class="my-profile-about">About</span>
                                <span class="pensil-wrap" id="my_about_edit">
                                <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil2.svg">
                                </span><!--pensil-wrap-->
                                <span class="save-close-wrap" id="my_about_action">
                                    <span class="save-head" id="my_about_save">Save</span><!--save-head-->
                                    <img class="edit-close" id="my_about_cancel" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-close.svg">
                                </span><!--save-close-wrap-->
                            </div><!--myprofile-card-head-->

                            <div class="myprofile-card-body">
                                <span class="table-wrap profile-info-row">
                                    <span class="table-cell-mail col-50">
                                        Email  
                                    </span>
                                    <span class="table-cell-e-address col-50 pad-11 change-date" id="email_id_wrapper">
                                        <span><?php echo ($session['us_email'])?$session['us_email']:'N/A'; ?></span>
                                    </span>
                                    <span class="table-cell-e-address col-50">
                                        <input type="text" class="replace-text" id="email_id" name="email_id" value="<?php echo $session['us_email'] ?>" placeholder="Email Id">        
                                    </span>
                                </span>
                                
                                <span class="table-wrap profile-info-row">
                                    <span class="table-cell-mail col-50">
                                        Phone <span class="field-required" id="phone_number_label">*</span>
                                    </span>
                                    <span class="table-cell-e-address col-50 pad-11 change-date" id="phone_number_wrapper">
                                        <span><?php echo ($session['us_phone'])?$session['us_phone']:'N/A' ?></span>
                                    </span>
                                    <span class="table-cell-e-address col-50">
                                        <input type="text" class="replace-text" maxlength="11" id="phone_number" name="phone_number" value="<?php echo $session['us_phone'] ?>" placeholder="Phone Number">        
                                    </span>
                                </span>
                                <?php /* ?>
                                <span class="table-wrap">
                                    <span class="table-cell-mail">Branch</span>
                                    <span class="table-cell-e-address pad-11"><?php echo isset($session['branch_name'])?$session['us_branch_code'].'-'.$session['branch_name']:'N/A'; ?></span>
                                </span><!--table-wrap-->
                                <?php */ ?>
                            </div><!--myprofile-card-body-->
                        </div><!--myprofile-cards-wraper-->
                    </div><!--columns-->
                    <?php if(!empty($profile_blocks)): ?>
                        <?php foreach($profile_blocks as $block): ?>
                            <div class="col-md-6 col-sm-6 col-xs-12" id="block_row_<?php echo $block['id'] ?>">
                                <div class="myprofile-cards-wraper">

                                        <?php if(!empty($block['profile_fields'])): ?>
                                            <?php $block_html = '<div class="myprofile-card-body">' ?>
                                            <?php $field_ids = array(); $display_html = '';?>
                                            <?php foreach($block['profile_fields'] as $field): ?>
                                                    <?php 
                                                        $field_ids[]    = array('id' => $field['id'], 'field_mandatory' => $field['pf_mandatory'], 'field_name' => $field['pf_name']);
                                                        $field_required = (($field['pf_mandatory'])?' <span class="field-required field-required-'.$block['id'].'">*</span> ':'');
                                                        $field_value    = isset($user_profile_fields[$field['id']])?$user_profile_fields[$field['id']]:'';
                                                    ?>
                                                    <?php 
                                                        $block_html .= '<span class="table-wrap profile-info-row">';
                                                        $block_html .= '    <span class="table-cell-mail col-50">'.$field['pf_label'].$field_required.'</span>';
                                                        $block_html .= '    <span class="table-cell-e-address col-50 pad-11 change-date field_label_display_'.$block['id'].'"  id="field_value_'.$field['id'].'">';
                                                        $block_html .= '        <span data-field-id="'.$field['pf_name'].'" class="field_values_current" >'.$field_value.'</span>';
                                                        $block_html .= '    </span>';
                                                        $block_html .= '    <span class="table-cell-e-address col-50">';
                                                        if($field['pf_field_input_type'] == '2')
                                                        {
                                                            $block_dropdown = '';
                                                            $block_options = explode(',', $field['pf_default_value']);
                                                            if(!empty($block_options))
                                                            {
                                                                $block_dropdown .= '<select class="replace-text field_label_form_'.$block['id'].' keyword_for_auto_value" name="'.$field['pf_name'].'" id="'.$field['pf_name'].'">';
                                                                $block_dropdown .= '<option value=""> Choose '.$field['pf_label'].'</option>';
                                                                foreach($block_options as $b_option)
                                                                {
                                                                    $block_dropdown .= '<option '.(($b_option==$field_value)?'selected="selected"':'').' value="'.$b_option.'">'.$b_option.'</option>';
                                                                }
                                                                $block_dropdown .= '</select>';
                                                            }
                                                            $block_html .= $block_dropdown;
                                                        }
                                                        else
                                                        {
                                                            $block_html .= '        <input type="text" class="replace-text field_label_form_'.$block['id'].' keyword_for_auto_value" data-pf-id="'.$field['id'].'" onKeyup="getAutoFieldsValue(event)" id="'.$field['pf_name'].'" auto-suggestion-status="'.$field['pf_auto_suggestion'].'" name="'.$field['pf_name'].'" value="'.$field_value.'" placeholder="'.$field['pf_placeholder'].'" >';
                                                        }
                                                        $block_html .= '        <ul id ="fieldListId-'.$field['pf_name'].'" class="field_values_list" style="display:none;list-style-type: none;">';
                                                        $block_html .= '        </ul>';
                                                        $block_html .= '    </span>';
                                                        $block_html .= '</span>';
                                                    ?>
                                            <?php endforeach; ?>
                                            <?php $block_html .= '</div>' ?>
                                            <div class="myprofile-card-head">
                                                <span class="my-profile-about"><?php echo $block['pb_name'] ?></span>
                                                <span class="pensil-wrap" onclick="editBlock('<?php echo $block['id'] ?>')" id="edit_block_<?php echo $block['id'] ?>">
                                                    <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil.svg">
                                                </span>
                                                <span class="save-close-wrap" id="block_action_<?php echo $block['id'] ?>">
                                                    <span class="save-head" id="save_block_<?php echo $block['id'] ?>" id="save_block_<?php echo $block['id'] ?>" onclick="saveBLock('<?php echo base64_encode(json_encode($field_ids)); ?>', this.id)">Save</span><!--save-head-->
                                                    <img class="edit-close" id="cancel_block_<?php echo $block['id'] ?>" onclick="cancelEdit('<?php echo $block['id'] ?>')" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-close.svg">
                                                </span>
                                            </div><!--myprofile-card-head-->    
                                            <?php echo $block_html; ?>
                                        <?php endif;?>
                                            
                                </div><!--myprofile-cards-wraper-->
                            </div><!--columns-->
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div><!--row-->                
            </div><!--container-reduce-width-->
        </div><!--container container-altr-->  
    </div><!--my-profile-blocks-->	
</section>
<script>
    var __controller = '<?php echo $this->router->fetch_class(); ?>';
    var __oldUserImg = '<?php echo $user_img . $session['us_image']; ?>';
    // const __siteUrl = '<?php //echo site_url(); ?>';
</script>
<script  src="<?php echo assets_url() ?>js/system.js"></script>
<script  src="<?php echo assets_url() ?>js/language_front.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.matchHeight.js"></script>
<script>
    $(document).on('click', '#my_bio_edit', function(){
        $('#my_bio_form').val($.trim($('#my_bio').text()));
        $('#my_bio, #my_bio_edit').hide();
        $('#my_bio_form, #my_bio_action').show();
    });
    
    $(document).on('click', '#my_bio_cancel', function(){
        $('#my_bio, #my_bio_edit').show();
        $('#my_bio_form, #my_bio_action').hide();
    });
    
    $(document).on('click', '#my_bio_save', function(){
        $('#my_bio_save').text('Saving..');
        $.ajax({
            url: __site_url + 'dashboard/save_profile',
            type: "POST",
            data: {"is_ajax": true, 'user_bio': $('#my_bio_form').val()},
            success: function (response) {
                $('#my_bio_save').text('Save');
                $('#my_bio, #my_bio_edit').show();
                $('#my_bio_form, #my_bio_action').hide();
                $('#my_bio').text($.trim($('#my_bio_form').val()));
            }
        });
    });

    $(document).on('click', '#my_about_edit', function(){
        var emailId = $.trim($('#email_id_wrapper').text());
        $('#email_id').val((emailId=='N/A')?'':emailId);
        var phoneNumber = $.trim($('#phone_number_wrapper').text());
        $('#phone_number').val((phoneNumber=='N/A')?'':phoneNumber);
        $('#my_about, #my_about_edit, #email_id_wrapper, #email_id_label, #phone_number_wrapper, #phone_number_label').hide();
        $('#my_about_form, #my_about_action, #email_id,#email_id_label, #phone_number,#phone_number_label').show();
    });
    
    $(document).on('click', '#my_about_cancel', function(){
        $('.field_values_list').html('');
        $('#my_about_form, #my_about_action,#phone_number_label,#email_id, #email_id_label, #phone_number,#phone_number_wrapper').hide();
        $('#my_about, #my_about_edit, #email_id_wrapper, #phone_number_wrapper').show();
    });

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

    function IsmobileNumber(Numbers){
        var IndNum = /^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }

    $(document).on('click', '#my_about_save', function(){
        var emailId         = $.trim($('#email_id').val());
        var phoneNumber     = $.trim($('#phone_number').val());
        var errorCount      = '';
        var errorMessage    = [];
        if( emailId == '' && registerNumber == '' ) {
            errorMessage.push('Both email id and username cannot be empty together');
            errorCount++;
        } else {
            if( emailId != '' && validateEmail(emailId) == false ) {
                errorMessage.push('Invalid email id');
                errorCount++;
            }
        }
        if(phoneNumber == '')
        {            
            errorMessage.push('Phone Number cannot be empty');
            errorCount++;
        }
        else
        {
            if(phoneNumber.length!=10 || !IsmobileNumber(phoneNumber))
            {
                errorMessage.push('Phone Number is invalid');
                errorCount++;                
            }
        }

        if(errorCount > 0) {
            showCommonModal('Heading', errorMessage.join('<br />'), 2);
        } else {
            $('#my_about_save').text('Saving..');
            $.ajax({
                url: __site_url + 'dashboard/save_profile_about',
                type: "POST",
                data: {"is_ajax": true, 'phone_number': phoneNumber, 'email_id': emailId},
                success: function (response) {
                    var data = $.parseJSON(response);
                    $('#my_about_save').text('Save');
                    if( $('#email_id').val() == "" || $('#email_id').val() == undefined)
                    {
                        $('#information_bar_verify').css('display','none');
                    }
                    switch(data['status']) {
                        case 1:
                            $('#my_about_form, #my_about_action, #email_id, #email_id_label,#phone_number_label,#phone_number,#phone_number_wrapper').hide();
                            $('#my_about, #my_about_edit, #email_id_wrapper, #phone_number_wrapper').show();
                            $('#email_id_wrapper span').text((emailId!='')?emailId:'N/A');
                            $('#phone_number_wrapper span').text((phoneNumber!='')?phoneNumber:'N/A');
                        break;
                        case 2:
                            showCommonModal('Heading', data['message'], 2);
                        break;
                        case 3:
                            $('#my_about_form, #my_about_action, #email_id, #email_id_label,#phone_number_label, #phone_number,#phone_number_wrapper').hide();
                            $('#my_about, #my_about_edit, #email_id_wrapper, #phone_number_wrapper').show();
                            $('#email_id_wrapper span').text((emailId!='')?emailId:'N/A');
                            $('#phone_number_wrapper span').text((phoneNumber!='')?phoneNumber:'N/A');
                            $('#information_bar_verify').css('display','block');
                            $('#information_bar_verify').find('.item').addClass('active');
                            $('#information_bar').hide();
                            showCommonModal('Heading', data['message'], 1);
                        break;
                        
                    }
                }
            });
        }
    });
    
    var __userName = '<?php echo $session['us_name'] ?>';
    var __site_url = '<?php echo site_url() ?>';
    function editProfile()
    {
        $(".content-holder").hide();
        $(".second-item-holder").show();
        $(".brows-img").show();
        $("#user_full_name").val(__userName);
    }
    function cancelProfile()
    {
        __uploading_file.length = 0;
        $('#us_image').val('');
        $('#my_profile_picture, #my_profile_image_header').attr('src', __oldUserImg);
        $(".second-item-holder").hide();
        $(".content-holder").show();
        $(".brows-img").hide();
        $('#save_profile_button').html('Save Changes');
    }
    function saveProfile()
    {
        var userFullName = $("#user_full_name").val();
        if (userFullName == '')
        {
            showCommonModal('Validation Error', 'Name cannot be empty.', 2);
            return false;
        }

        if(userFullName.length > 30){
            showCommonModal('Validation Error', 'Name length exceeds allowed limit(30).', 2);
            return false;
        }

        $('#save_profile_button').html('Applying Changes..');
        
        __userName = userFullName;

        if(typeof __uploading_file[0] != 'undefined')
        {
            var i = 0;
            var uploadURL = __site_url + "dashboard/upload_user_image";
            var fileObj = new processFileName(__uploading_file[i]['name']);
            var param = new Array;
            param["file_name"] = fileObj.uniqueFileName();
            param["user_name"] = userFullName;
            param["extension"] = fileObj.fileExtension();
            param["file"]      = __uploading_file[i];
            $('#upload_button_text').text('Loading..');
            uploadFiles(uploadURL, param, uploadUserImageCompleted);
        }
        else
        {
            $.ajax({
                url: __site_url + 'dashboard/save_profile',
                type: "POST",
                data: {"is_ajax": true, 'user_name': userFullName},
                success: function (response) {
                    var data = $.parseJSON(response);
                    $('#my_profile_name, #my_profile_name_header span').text(__userName);
                    $(".second-item-holder").hide();
                    $(".content-holder").show();
                    $(".brows-img").hide();
                    $('#save_profile_button').html('Save Changes');
                }
            });
        }
    }
    var __uploading_file = new Array();
    $(document).on('change', '#us_image', function (e) {
        __uploading_file = e.currentTarget.files;
        
        if (__uploading_file.length > 1)
        {
            $('#us_image').val('');
            return false;
        }
        if (__uploading_file[0].size > 1148744)
        {
            __uploading_file = [];
            $('#us_image').val('');
            showCommonModal('Error Occured', 'File size exceeded the limit.',2);
            return false;
        }
        var fileObj = __uploading_file[0].name;
        if(['jpg','jpeg'].indexOf(fileObj.split('.').pop()) == -1){
            __uploading_file = [];
            $('#us_image').val('');
            showCommonModal('Validation Error', 'Unsupported file type. Supported files types are jpg/jpeg.', 2);
            return false;
        }

        var readerObj = new FileReader();
        readerObj.onload = function(element) {
            $('#my_profile_picture').attr('src', element.target.result);
        }
        readerObj.readAsDataURL(e.target.files[0]);
    });
    function uploadUserImageCompleted(response)
    {
        var imageUploadedDate = new Date();
        var data = $.parseJSON(response);
        __oldUserImg = data['user_image']+"?"+imageUploadedDate.getTime();
        $('#my_profile_picture, #my_profile_image_header').attr('src', __oldUserImg);
        $('#my_profile_name, #my_profile_name_header span').text(__userName);
        $('#upload_button_text').text('Change');
        $(".second-item-holder").hide();
        $(".content-holder").show();
        $(".brows-img").hide();
        $('#save_profile_button').html('Save Changes');
    }
    $(function() {
        $('.myprofile-card-body').matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        });
    });
    $(document).on('click','#change_pass_btn',function(){
        var currPass        = $("#curr_password_beta").val();
        var newPass         = $("#password_confirmation_beta").val();
        var confirmPass     = $("#password_beta").val();
        if(currPass=="" || newPass=="" || confirmPass=="")
        {
            if(currPass=="")
            {
                var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
                error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
                error_html+= '  All fields are required  </div>';
                error_html+= '</div>';
                $("#password_change_message").css('display','block');
                $("#password_change_message").html(error_html);
            }
            if(newPass=="")
            {
                var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
                error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
                error_html+= '  All fields are required  </div>';
                error_html+= '</div>';
                $("#password_change_message").css('display','block');
                $("#password_change_message").html(error_html);
            }
            if(confirmPass=="")
            {
                var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
                error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
                error_html+= '  All fields are required  </div>';
                error_html+= '</div>';
                $("#password_change_message").css('display','block');
                $("#password_change_message").html(error_html);
            }
        }
        else
        {
            if(newPass!=confirmPass)
            {
                var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
                error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
                error_html+= '  New passwords does not match  </div>';
                error_html+= '</div>';
                $("#password_change_message").css('display','block');
                $("#password_change_message").html(error_html);
            }
            else
            {
                $.ajax({
                    url: __site_url+'dashboard/change_password',
                    method: "POST",
                    data: {
                    "is_ajax":true,
                    "current_pass": currPass,
                    "new_password": newPass,
                    "confirm_pass": confirmPass
                    },
                    success: function(response){
                        var obj = JSON.parse(response);
                        $("#curr_password_beta, #password_confirmation_beta, #password_beta").val('');
                        if(obj['message']=='success')
                        {
                            $('.modal').modal('hide');
                            showCommonModal('Heading', 'Password changed successfully', 1);
                        }
                        else
                        {
                            var error_html = '<div class="alert alert-error alert-danger" id="alert_danger">';
                            error_html+= '    <a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>';
                            error_html+= '  Old password is incorrect  </div>';
                            error_html+= '</div>';
                            $("#password_change_message").css('display','block');
                            $("#password_change_message").html(error_html);
                        }
                    }
                });
            }
        }
    });
</script>
<script>
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
        var errorMessage    = '';
        var errorCount      = 0;

        if(fields.length > 0 )
        {
            for(var i=0; i<fields.length; i++)
            {
                fieldValues[fields[i]['field_name']] = $('#'+fields[i]['field_name']).val();
                $('#'+fields[i]['field_name']).removeClass('error-fields');
                if(fields[i]['field_mandatory'] == 1 && fieldValues[fields[i]['field_name']] == '')
                {
                    $('#'+fields[i]['field_name']).addClass('error-fields');
                    errorCount++;
                }
            }

            if (errorCount > 0)
            {
                return false;
            } 
            $('.field_values_list').html('');
            $('#save_block_'+blockId).text('Saving..');                                                
            $.ajax({
                url: site_url+'/dashboard/save_profile_values',
                type: "POST",
                data:{ "is_ajax":true, 'profile_values':JSON.stringify(fieldValues)},
                success: function(response) {
                    for(var i=0; i<fields.length; i++)
                    {
                        $('#field_value_'+fields[i]['id']+' .field_values_current').text(fieldValues[fields[i]['field_name']]);
                    }
                    $('.field_label_display_'+blockId).show();
                    $('.field_label_form_'+blockId).hide();
                    $('#edit_block_'+blockId).show();
                    $('#block_action_'+blockId).hide();
                    $('#save_block_'+blockId).text('Save');      
                    $('.field_values_list').hide();
                    $('.field-required-'+blockId).hide();
                }
            });    

        }
    }
}
/*End*/

 var site_url = '<?php echo site_url() ?>';
 var __timeOut = '';
    /*
    * To get the autofill values for the profile block dynamic fields
    * Created by : Neethu KP
    * Created at : 06/01/2017
    */
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
                    url: site_url+'dashboard/get_fileds_value',
                    type: "POST",
                    data:{"is_ajax":true, "keyword":keyword, "field_id":field_id},
                    success: function(response) {
                        if(response){
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
                    }
                });
            }          
        }, 600);
    }

    $(document).on('click' , '.field_values_list li' ,function(){

        var fieldText     = $(this).text();
        var fieldListId   = $(this).parent().attr('id');
        var fieldValueId  = fieldListId.split('-');
        $('#'+fieldValueId[1]).val(fieldText);
        $('.field_values_list').hide();

    });
            
            
var  __progress = false;
function add_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
			$.ajax({
				url: base_url+'course/change_whishlist',
				method: "POST",
				data: {
					cid: cid,
					uid: uid,
					stat: 1,
					page: 'search'
				},
				success: function(response){
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						$("#whishdiv_"+key).html(data.str);
						//$('#'+cid).addClass('wish-added');
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			//console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
	
}

function remove_wishlist(cid, uid, obj){
	key = $(obj).attr('data-key');
	if(uid != ''){
		if(!__progress){
			__progress = true;
			$.ajax({
				url: base_url+'course/change_whishlist',
				method: "POST",
				data: {
					cid: cid,
					uid: uid,
					stat: 0,
					page: 'search'
				},
				success: function(response){
					__progress = false;
					data = $.parseJSON(response);
					if(data.stat == '1'){
						//$('#'+cid).removeClass('wish-added');
						$("#whishdiv_"+key).html(data.str);
						$(".wish-icon-search").on('click', function(e) { e.preventDefault(); });
					}
					else{
						window.location = base_url+'login';
					}
					
				}
			});
		}else{
			//console.log('on progress');
		}
	}
	else{
		window.location = base_url+'login';
	}
}

</script>
<script type="text/javascript">
    $( ".block-load-in" ).click(function( event ) {
        event.preventDefault();
    });
    $(document).on('click', '#change_password_button', function(){
        $('#password_change_message').hide();
        $("#curr_password_beta, #password_confirmation_beta, #password_beta").val('');
    });
</script>

<style>
.error-fields{ border: 1px solid #a41f1f;}
.field-required{ display: none;}
</style>
<?php include_once "modals.php"; ?>
<?php include_once "footer.php"; ?>