<?php include_once 'header.php';?>
<style>
    .user-badge-admin{
        margin-top: 7px;
    }
    .field-required{ display:none;}
    .remove-bundle-item {
        font-size: 28px;
    font-weight: 400;
    color: #fb5d5d;
    line-height: 16px;
    cursor: pointer;
    display: block;
    text-align: right;
    padding-right: 35px;
    vertical-align: baseline;
    padding-top: 10px;
}
</style>
<!-- The background image section -->
<div class="User_backgrd">
    <!--<span>2999 Credits</span>-->
</div>

<!-- Profile container starts here -->
<div class="profile-container">

    <div class="profile-header row">
        <div class="col-sm-12">
            <div class="row"> <!-- The row starts -->
                <div class="profle-img-container">
                    <?php if(isset($permission['edit']) && $permission['edit']): ?>
                        <input type="file" class="user-image-upload-btn" name="file" id="us_image" accept="image/*">
                        <button class="btn btn-green pos-abs selected"><?php echo lang('change_image')?><ripples></ripples></button>
                    <?php endif; ?>
                    <img id="user_image" src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():user_path()).$user['us_image']; ?>" alt="Profile image">
                </div>

                <!-- Header title top 1 -->
                <div class="col-md-9 col-sm-6 col-xs-6 prfle-heder-rite hdr-rite-calc">
                    <div class="pfle-title-drp clearfix">
                        <h3 id="profile_user_name"><?php echo isset($user['us_name'])?$user['us_name']:'' ?></h3>  <!-- Header title -->
                        

                        <!-- Drop down along with header title -->
                        <?php if(!($permission['edit'] == 0 && $user['us_deleted'] == 1)): ?>
                            <div class="btn-group lecture-control">
                                <span class="dropdown-tigger" data-toggle="dropdown">
                                    <span class="label-text">
                                        <i class="icon icon-down-arrow"></i>
                                    </span>
                                    <span class="tilder"></span>
                                </span>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if($user['us_deleted'] == 0): ?>
                                        <li>
                                            <a href="javascript:void(0);" data-toggle="modal" data-target="#send-user-message" id="send_user_message" ><?php echo lang('send_message') ?></a>
                                        </li>
                                        <?php if(isset($permission['edit']) && $permission['edit']): ?>
                                        <li <?php echo $user['us_email']==''?'style="display:none"':'' ?>>
                                            <a href="javascript:void(0);" id="reset_pwd_btn" onclick="resetPassword('<?php echo $user['id'] ?>')"><?php echo lang('reset_password') ?></a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if(isset($permission['delete']) && $permission['delete']): ?>
                                        <li>
                                            <a href="javascript:void(0);" id="delete_btn_<?php echo $user['id'] ?>" onclick="deleteUser('<?php echo $user['id'] ?>')">Delete Student</a>
                                        </li>
                                        <?php endif; ?>
                                        <?php if(isset($permission['edit']) && $permission['edit']): ?>
                                        <li id="user_status_btn_<?php echo $user['id'] ?>">
                                            <?php $cb_status = 'activate';
                                            switch($user['us_status']){
                                                case 2:
                                                $cb_status = 'approve';
                                                break;
                                                case 0:
                                                $cb_status = 'activate';
                                                break;
                                                default:
                                                $cb_status = 'deactivate';
                                                break;
                                            }
                                            ?>
                                            <?php $cb_action = $cb_status ?>
                                            <a href="javascript:void(0);" onclick="changeUserStatus('<?php echo $user['us_status'] ?>')"><?php echo lang($cb_status) ?></a>
                                        </li>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if(isset($permission['edit']) && $permission['edit']): ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="restoreUser('<?php echo $user['id'] ?>')"><?php echo lang('restore') ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <!--  !.Drop down along with header title -->
                    </div>                        
                    <div class="badge bg-olive group-total user-badge-admin"><?php echo ($user['us_role_id'] == 5)?'Parent':'Student'?><?php echo isset($user['institute_name'])?' - '.$user['institute_name']:''; ?></div>
                </div><!-- Header title top 1 ends -->

                <!-- Header title top 1 -->
                <div class="col-md-9 col-sm-6 col-xs-6 prfle-heder-rite hdr-rite-calc">
                    <div class="pfle-title-drp clearfix" style="padding-top: 4px;">
                        <div class="pull-left">
                            <ul class="headr-top-menu">
                                <li><a href="javascript:void(0)" onclick="scrollToDiv('about_user_profile')"><?php echo lang('about_profile') ?></a></li>
                                <li><a class="scrollto_usercourse" href="javascript:void(0)" onclick="scrollToDiv('user_course_wrapper')"><?php echo lang('courses')?></a></li>
                            </ul>
                        </div>
                    </div>
                </div><!-- Header title top 1 ends -->
            </div><!-- The row starts -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="myprofile-cards-wraper">
                <div class="myprofile-card-head">
                    <span class="my-profile-about">About</span>
                    <?php if(isset($permission['edit']) && $permission['edit']): ?>
                    <span class="pensil-wrap" id="my_about_edit">
                        <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil.svg">
                    </span><!--pensil-wrap-->
                    <?php endif; ?>
                    <span class="save-close-wrap" id="my_about_action">
                        <span class="save-head" id="my_about_save">Save</span><!--save-head-->
                        <img class="edit-close" id="my_about_cancel" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-close.svg">
                    </span><!--save-close-wrap-->
                </div><!--myprofile-card-head-->

                <div class="myprofile-card-body">
                    <span class="table-wrap profile-info-row">
                        <span class="table-cell-mail col-50">
                            Name  
                        </span>
                        <span class="table-cell-e-address col-50 pad-11 change-date" id="us_name_wrapper">
                            <span><?php echo ($user['us_name'])?$user['us_name']:'N/A'; ?></span>
                        </span>
                        <span class="table-cell-e-address col-50">
                            <input type="text" class="replace-text" maxlength="50" id="us_name" name="us_name" value="<?php echo $user['us_name'] ?>" placeholder="Name">        
                        </span>
                    </span>
                    <span class="table-wrap profile-info-row">
                        <span class="table-cell-mail col-50">
                            Email  
                        </span>
                        <span class="table-cell-e-address pad-11 change-date col-50" id="email_id_wrapper">
                            <span><?php echo ($user['us_email'])?$user['us_email']:'N/A'; ?></span>
                        </span>
                        <span class="table-cell-e-address col-50">
                            <input type="text" class="replace-text" id="email_id" name="email_id" value="<?php echo $user['us_email'] ?>" placeholder="Email Id">        
                        </span>
                    </span>                  
                    <span class="table-wrap profile-info-row">
                        <span class="table-cell-mail col-50">
                            Phone <span class="field-required" id="phone_number_label">*</span>
                        </span>
                        <span class="table-cell-e-address pad-11 change-date col-50" id="phone_number_wrapper">
                            <span><?php echo ($user['us_phone'])?$user['us_phone']:'N/A' ?></span>
                        </span>
                        <span class="table-cell-e-address col-50">
                            <input type="text" class="replace-text" id="phone_number" name="phone_number" value="<?php echo $user['us_phone'] ?>" placeholder="Phone Number" maxlength="11">        
                        </span>
                    </span>
                    <?php /* ?>
                    <span class="table-wrap profile-info-row">
                        <span class="table-cell-mail">Branch</span>
                        <span class="table-cell-e-address pad-11"><?php echo isset($user['branch_name'])?$user['us_branch_code'].'-'.$user['branch_name']:'N/A'; ?></span>
                    </span>
                    <?php */ ?>
                    <!--table-wrap profile-info-row-->
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
                                            $field_ids[]    = array('id' => $field['id'], 'field_mandatory' => $field['pf_mandatory'], 'field_name' => $field['pf_name'], 'field_label' => $field['pf_label']);
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
                                    <?php if(isset($permission['edit']) && $permission['edit']): ?>
                                        <span class="pensil-wrap" onclick="editBlock('<?php echo $block['id'] ?>')" id="edit_block_<?php echo $block['id'] ?>">
                                            <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil.svg">
                                        </span>
                                    <?php endif; ?>
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


    <!--  ################   Badges starts here ####################-->
    <div class="row" id="user_course_wrapper">
        <div class="col-sm-12">
            <div class="profile-box-layouts col-sm-12">
                <!-- Top Headeing of the tile -->
                
                <h4><?php echo strtoupper('Courses & Bundle') ?> <span class="hidden"><?php echo isset($total_enrolled_course)?$total_enrolled_course:'0' ?> <?php echo 'Count' ?></span></h4>

                <!-- Table structre starts here -->
                <?php if (!empty($user_bundle_enrolled)): ?>
                    <?php foreach ($user_bundle_enrolled as $user_course): ?> 
                        <div class="table course-cont rTable prfle-tble" id="user_bundle_<?php echo $user_course['id'] ?>" data-name="<?php echo $user_course['c_title'] ?>">
                            <div class="rTableRow">
                                <div class="row d-flex align-center">
                                    <div class="rTableCell col-lg-7 d-flex align-center"> <!-- Each table set cell -->
                                        <span class="icon-wrap-round">
                                            <i class="icon icon-graduation-cap"></i>
                                        </span>
                                         <span class="wrap-mail" data-name="<?php echo $user_course['c_title'] ?>" id="course_span_<?php echo $user_course['id'] ?>"> 
                                            <a ><?php echo $user_course['c_title'] ?>  <span class="badge bg-olive"> Bundle </span> <span class="badge bg-olive"><?php echo ($user_course['bs_bundle_details'])?'Items : '.count(json_decode($user_course['bs_bundle_details'],true)):''?></span> </a>
                                          </span>
                                    </div>

                                    <div class="rTableCell col-lg-4 col-sm-3" > <!-- Each table set cell -->
                                        <?php if($user_course['bs_approved'] == 0){?>
                                        <div class="cent-algn-txt">
                                        <label class="label label-orange"><!-- <p class="prfle-suspend"> --><?php echo lang('suspended') ?><!-- </p> --></label>
                                        </div>
                                        <?php }else { ?>
                                           
                                        <?php 
                                          
                                        } ?>
                                        <!--<label class="label label-red-tint">--> <!-- <p class="prfle-delte"> --><!--Deleted--> <!-- </p> --><!--</label>-->  
                                    </div>

                                    <div title="Remove" class="rTableCell col-lg-1">
                                        <a onclick="removeUserFromBundle('<?php echo $user_course['id'] ?>','<?php echo $user['id'] ?>','<?php echo $user_course['c_title'] ?>','<?php echo $user['us_name'] ?>')" class="remove-bundle-item" >×</a>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    <?php endforeach; ?>
                <?php endif; ?><!-- !.Table structre starts here -->
                <?php if (!empty($user_course_enrolled)): ?>
                    <?php foreach ($user_course_enrolled as $user_course): 
                     ?> 
                        <div class="table course-cont rTable prfle-tble" id="user_course_<?php echo $user_course['cs_course_id'] ?>" data-name="<?php echo $user['us_name'] ?>">
                            <div class="rTableRow">
                                <div class="row d-flex align-center">
                                    <div class="rTableCell col-lg-7 d-flex align-center"> <!-- Each table set cell -->
                                        <span class="icon-wrap-round">
                                            <i class="icon icon-graduation-cap"></i>
                                        </span>
                                         <span class="wrap-mail" data-name="<?php echo $user_course['cb_title'] ?>" id="course_span_<?php echo $user_course['id'] ?>"> 
                                            <a ><?php echo $user_course['cb_title'] ?></a>
                                          </span>
                                    </div>

                                    <div class="rTableCell col-lg-4"> <!-- Each table set cell -->
                                        <?php if($user_course['cs_approved'] == 0){?>
                                        <div class="cent-algn-txt">
                                        <label class="label label-orange"><!-- <p class="prfle-suspend"> --><?php echo lang('suspended') ?><!-- </p> --></label>
                                        </div>
                                        <?php }else { ?>
                                            <?php $user_course['percentage'] = isset($user_course['percentage'])?round($user_course['percentage']):0; ?>
                                            <?php if(empty($user_course['percentage']) && $user_course['percentage'] == 0){ ?>
                                                <div class="cent-algn-txt">
                                                    <label class="label label-grey"><?php echo lang('not_yet_started') ?></label>
                                                </div>
                                            <?php }elseif($user_course['percentage'] > 95){?>
                                                <div class="cent-algn-txt">
                                                    <label class="label label-dark-green"><?php echo lang('completed') ?></label>
                                                </div>
                                            <?php }else{ ?>
                                                <div class="cent-algn-txt">
                                                    <a class="link-style"><?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?><?php echo lang('percentage')?></a>
                                                </div>
                                                <div class="progress sml-progress">
                                                    <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?>%;">
                                                        <span class="sr-only"><?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?><?php echo lang('percentage')?> Complete</span>
                                                    </div>
                                                </div>
                                        <?php 
                                            }
                                        } ?>
                                        <!--<label class="label label-red-tint">--> <!-- <p class="prfle-delte"> --><!--Deleted--> <!-- </p> --><!--</label>-->  
                                    </div>

                                    <div title="Remove" class="rTableCell col-lg-1">
                                        <a onclick="removeUserFromCourse('<?php echo $user_course['cs_course_id'] ?>','<?php echo $user['id'] ?>','<?php echo addslashes($user['us_name']) ?>','<?php echo $user_course['cb_title'] ?>')" class="remove-bundle-item" >×</a>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    <?php endforeach; ?>
                <?php endif; ?><!-- !.Table structre starts here -->
            </div>
        </div>
    </div>
    <!--  ################   Badges ends here ####################-->
</div>
<!-- Profile container ends here -->

<script type="text/javascript">
    var user_id         = <?php echo $user['id'] ?>;
    var user_name       = "<?php echo $user['us_name'] ?>";
    var user_email      = "<?php echo $user['us_email'] ?>";
    var phone_number    = "<?php echo $user['us_phone'] ?>";
</script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/user_profile.js"></script>
<!-- initialising the tag plugin using tokenize  -->
    <!-- END -->
    <!-- ADDING REDACTOR PLUGIN INSIDE -->
    <!-- ############################# --> <!-- START -->
        <!-- JS -->
        <script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
        <script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
        <script>
            $(function()
            {
                startTextToolbar();
            });
            function startTextToolbar()
            {
                $('#redactor_send').redactor({
                    imageUpload: admin_url+'configuration/redactore_image_upload',
                    plugins: ['table', 'alignment'],
                    source:false,
                    minHeight: '170px',
                    maxHeight: '170px'
                });
            }


function scrollToDiv(selecter)
{
    var header_height = parseInt($('.header').height())+parseInt($('.breadcrumb').height())+20;
    $('html, body').animate({
           scrollTop: $('#'+selecter).offset().top - header_height
    }, 'slow');
}

function removeUserFromBundle(bundle_id, user_id = '',courseName='',userName = '') {

var user_ids = [];
user_ids.push(user_id);

var messageObject = {
    'body': 'Are you sure to remove item named '+courseName+' ?',
    'button_yes': 'OK',
    'button_no': 'CANCEL',
    'continue_params': {
        "user_id": user_ids,
        "bundle_id": bundle_id,
        "student_name":userName,
        "bundle_title":courseName
    },
};
callback_warning_modal(messageObject, removeUserFromBundleConfirmed);
}

function removeUserFromBundleConfirmed(params) {
    __user_selected         = new Array();
    var user_id             = params.data.user_id;
    var bundle_id           = params.data.bundle_id;
    var student_name        = params.data.student_name;
    var bundle_title        = params.data.bundle_title;
    
    $.ajax({
        url: admin_url + 'bundle/delete_subscription',
        type: "POST",
        data: {
            "user_id": JSON.stringify(user_id),
            "bundle_id": bundle_id,
            "is_ajax": true,
            "bundle_title":bundle_title,
            "student_name":student_name
        },
        success: function (response) {

            var data = $.parseJSON(response);
            if (data['error'] == false) {
                $('#user_bundle_'+bundle_id).remove();
               var messageObject = {
                    'body': 'Item named '+bundle_title+'  unsubscribed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
              
            } else {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
          
        }
    });
}

function removeUserFromCourse(course_id, user_id = '',username='',courseName = '') {

var user_ids = [];
user_ids.push(user_id);

var messageObject = {
    'body': 'Are you sure to remove item named '+courseName+' ?',
    'button_yes': 'OK',
    'button_no': 'CANCEL',
    'continue_params': {
        "user_id": user_ids,
        "course_id": course_id,
        "student_name":username,
        "courseTitle":courseName
    },
};
callback_warning_modal(messageObject, removeUserFromCourseConfirmed);
}

function removeUserFromCourseConfirmed(params) {


var user_id                 = params.data.user_id;
var course_id               = params.data.course_id;
var student_name            = params.data.student_name;
var __course_title          = params.data.courseTitle;

$.ajax({
    url: admin_url + 'course/delete_subscription',
    type: "POST",
    data: {
        "user_id": JSON.stringify(user_id),
        "course_id": course_id,
        "is_ajax": true,
        "course_title":__course_title,
        "student_name":student_name
    },
    success: function (response) {

        var data = $.parseJSON(response);
        if (data['error'] == false) {
            $('#user_course_'+course_id).remove();

            var messageObject = {
                'body': 'Item named '+__course_title+'  unsubscribed successfully',
                'button_yes': 'OK',
            };
            callback_success_modal(messageObject);
          
        } else {
            var messageObject = {
                'body': data['message'],
                'button_yes': 'OK',
            };
            callback_danger_modal(messageObject);
        }
       
    }
});
}


        </script>
<?php include_once 'footer.php';?>