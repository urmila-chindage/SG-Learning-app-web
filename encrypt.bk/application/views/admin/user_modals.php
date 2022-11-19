<!-- Modal pop up contents :: Invite Users -->
    <div class="modal modal-full fade" data-backdrop="static" data-keyboard="false" id="invite-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('send_user_invite_title') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="">
                        <div class="col-sm-12">
                            <?php echo lang('send_invite_to') ?>

                            <select id="tokenize" multiple="multiple" class="form-control tokenize-sample custom-token">

                            </select>
                        </div>
					</div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-7">
                                <?php echo lang('send_invite_subject') ?>
                                <div class="form-group">
                                    <input id="invite_subject" type="text" class="form-control" placeholder="<?php echo lang('send_invite_subject_ph') ?>">
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <?php echo lang('send_invite_mail_template') ?>
                                <div class="form-group">
                                    <select class="form-control" id="email_template_list" class="email-template-list">
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <?php echo lang('send_invite_message') ?>

                                <textarea class="form-control invite-user-message min-430" id="redactor" name="ck-text-editor">

                                </textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" onclick="user_invitation()" class="btn btn-green"><?php echo lang('send') ?></a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users -->
    <div class="modal fade modal-redactor-height" id="invite-user-bulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="false" data-keyboard="false">
        <div class="modal-dialog modal-small"  style="font-weight:unset;"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">SEND MESSAGE</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-12">
                                Subject* :
                                <div class="form-group">
                                    <input id="invite_send_subject" type="text" class="form-control" placeholder="subject">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                Message *:
                                <textarea class="form-control invite-user-message min-430" id="redactor_invite" name="ck-text-editor">

                                </textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">

                    <a type="button" class="btn btn-red" data-dismiss="modal">CANCEL</a>
                    <a href="javascript:void(0);" id="message_send_button" type="button" onclick="sendMessageToUser()" class="btn btn-green">SEND</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users -->
    <div class="modal modal-small fade" id="extend-validity" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('extend_validity') ?></h4>
                </div>
                <div class="modal-body">

                         <div id="extend_date_err" style="color:#a94442;display:none"></div>
                        <div class="form-group" id="modal_extend_validity">
                        </div>


                </div>

                <div class="modal-footer">
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                    <a href="javascript:void(0);" type="button" id="update_extended_validity" class="btn btn-green"><?php echo lang('update') ?></a>
                   
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users -->

    <!-- Modal pop up contents :: Upload a lecture -->
    <div class="modal fade padd-r20" id="addusers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">IMPORT STUDENTS</h4>
                </div>
                <div class="modal-body">
                    <p class="mb30">Follow these steps to import students</p>
                    <div class="form-group mb30">
                        <p><b>Step 1:</b> Download the given Document <a href="<?php echo base_url() . config_item('upload_folder') . '/usertemplate.csv' ?>" class="link-style"><em>usertemplate.csv</em></a> and analyze the format</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill the students in the document format.<br>( Maximum 1000 students per upload )</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the students, Upload your .csv file.</p>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload" id="import_user">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_user_file" type="text">
                      </div>
                      
                      <div <?php if ($admin['us_institute_id']!='0'): ?> style="display: none;" <?php endif;?> class="form-group">
                            <label><b>Step 4:</b><?php echo ' ' . lang('student_institute') ?>*:</label>
                            <select id="student_institute_upload" class="form-control">
                                <option value=""><?php echo lang('add_user_institute') ?></option>
                                <?php if (!empty($institutes)): ?>
                                <?php foreach ($institutes as $institute): ?>
                                    <option <?php if (isset($admin) && $admin['us_institute_id'] == $institute['id']): ?> selected <?php endif;?> value="<?php echo $institute['id'] ?>"><?php echo $institute['ib_institute_code'] . " - " . $institute['ib_name'] ?></option>
                                <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                        <div class="progress width100">
                            <div style="width: 0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="0" role="progressbar" class="progress-bar progress-bar-success">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <span class="">Uploading...<b class="percentage-text" id="importing_user_process">0%</b></span>
                    </div>

                    <div class="form-group mb30">
                        <p><b>Step <?php if ($admin['us_institute_id']!='0'): ?>4<?php else: ?>5<?php endif;?>:</b> Review students list.</p>
                    </div>
                </div>
                <div class="modal-footer">

                    <button type="button" class="btn btn-red"  data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green" onclick="uploadUser()">UPLOAD</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->

    <!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="publish-course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-group">
                        <b id="confirm_box_title_course"></b>
                        <p class="m0" id="confirm_box_content_course"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" id="confirm_box_ok_course" ><?php echo lang('continue') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up created by Yadu Chandran :: Invite Users Bulk-->
    <!-- Modal pop up contents :: Invite Users -->

    <!-- Modal pop up contents :: Invite Users -->


    <!-- Modal pop up contents :: Send message to Users -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="send-user-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('send_user_invite_title') ?></h4>
                </div>
                <div class="modal-body">
                    <div>
                        <?php echo lang('send_invite_subject') ?>
                        <div class="form-group">
                            <input id="send_message_subject" type="text" class="form-control" placeholder="<?php echo lang('send_invite_subject_ph') ?>">
                        </div>
                    </div>
                    <div>
                        <?php echo lang('send_invite_message') ?>
                        <textarea class="form-control send-user-message min-430" id="redactor_send" name="ck-text-editor">
                        </textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                    <a href="javascript:void(0);" type="button" id="send_mail_button" onclick="send_message_user()" class="btn btn-green"><?php echo lang('send') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Send message to Users -->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="activate_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title"></b>
                            <p class="m0" id="confirm_box_content_1"> </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="confirm_box_ok"><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents :: Create Group -->
    <div class="modal fade" id="add-user-to-group" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_to_batch') ?></h4>
                </div>
                <div class="modal-body">

                <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><p id="batch_text"></p></label>
                        </div>
                    </div>
                    <div class="inside-box pos-rel" id="group_list_wrapper">

                    </div>
                </div>


                <div class="modal-footer">

                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                    <a href="javascript:void(0)" id="add_user_to_group" type="button" class="btn btn-green"><?php echo 'ADD' ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Group  -->

    <!-- Modal pop up contents :: ADD to Catalog -->
    <div class="modal fade" id="add-users-course" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_user_course') ?></h4>
                </div>
                <div class="modal-body">



                    <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><span id="coursetext"></span></label>
                        </div>
                    </div>
                    <div class="inside-box" id="course_list_wrapper">
                    </div>
                </div>

                <div class="modal-footer">

                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                    <a href="javascript:void(0)" type="button" id="add_user_ok" class="btn btn-green"><?php echo 'ADD' ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: ADD Teacher -->

    <!--Notification for Bulk Action-->

        <div aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="notify_bulk_action" class="modal fade alert-modal-new in" style="display: none; padding-right: 13px;">
            <div role="document" class="modal-dialog modal-small">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="notify_deleted_title" style="font-size:20px;">Alert!</b>
                            <p id="notify_content" class="m0"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-red selected" type="button">OK<ripples><ripple style="left: 50px; top: 19px; height: 162px; width: 162px; margin: -81px; transform: scale(1.11); opacity: 0;"></ripple></ripples></button>
                    </div>
                </div>
            </div>
        </div>

    <!--notification end -->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" id="create_user" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"><?php echo lang('add_user') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label"><?php echo lang('add_user_name') ?> <span class="text-danger">*</span>:</label>
                        <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" id="student_name" placeholder="eg: John Milton" class="form-control">
                    </div>
                    <div class="form-group">
                        <label id="create_box_label"><?php echo lang('add_user_email') ?> <span class="text-danger">*</span>:</label>
                        <input type="text" maxlength="50" id="student_email" placeholder="eg: johnmilton@gmail.com" class="form-control">
                    </div>
                    <div class="form-group">
                        <label id="create_box_label">Phone <span class="text-danger">*</span>:</label>
                        <input type="text" onkeypress="return preventCharector(event)" maxlength="10" id="phone_number" placeholder="eg: 11416024" class="form-control">
                    </div>
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order custom-field">
                            <label><?php echo lang('add_user_password') ?> <span class="text-danger">*</span>:</label>
                        <input type="text" maxlength="50" id="student_password" class="form-control">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a class="btn btn-green" onclick="generatePassword();">GENERATE PASSWORD<ripples></ripples></a>
                        </div>
                    </div>
                    <div <?php if (isset($admin) && $admin['us_role_id'] == 8): ?> style="display:none;" <?php endif;?> class="form-group">
                        <label><?php echo lang('student_institute') ?> <span class="text-danger">*</span>:</label>
                        <select id="student_institute" class="form-control">
                            <option value=""><?php echo lang('add_user_institute') ?></option>
                            <?php if (!empty($institutes)): ?>
                            <?php foreach ($institutes as $institute): ?>
                            <option <?php if ((isset($admin) && $admin['us_role_id'] == 8) && $admin['us_institute_id'] == $institute['id']): ?> selected <?php endif;?> value="<?php echo $institute['id'] ?>"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                        
                    </div>
                    <input type="hidden" id="student_branch" value="1" />
                    <?php /* ?>
                    <div class="form-group">
                        <label>Branch * :</label>
                        <select id="student_branch" class="form-control">
                            <option value="">Choose Branch</option>
                            <?php if (!empty($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                            <option value="<?php echo $branch['id'] ?>"><?php echo $branch['branch_code'] . ' - ' . $branch['branch_name'] ?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                    <?php */ ?>
                    <div class="form-group" id="role_funcationlity_details"></div>
                    <div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" name="send_mail" id="send_mail" value="1"><span class="ap_cont chk-box">Send an email to student</span></label></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="addStudent()" id="create_box_ok"><?php echo lang('create') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->


    <div class="modal fade" id="profile_field_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title">EXPORT STUDENTS</h4>
                </div>
                <div class="modal-body">
                    <?php if(isset($profiles) && !empty($profiles)): ?>
                    <p class="mb30">Export additional student data by choosing the following details</p>
                    <div class="institution-select">
                        <div class="inside-box pos-rel pad-top50" style="overflow-x: hidden;">
                            <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                                <div class="row invitation-type-wrapper">
                                    <div class="add-selectn alignment-order">
                                        <div class="inside-box-padding invitation-content-wrapper">
                                            <?php foreach($profiles as $profile): ?>
                                            <div class="checkbox-wrap invite-course-list">    
                                                <span class="chk-box">        
                                                    <label class="font14">            
                                                        <input type="checkbox" data-field-name="<?php echo base64_encode($profile['pf_label']) ?>" class="inst-profile-export" value="<?php echo $profile['id'] ?>">            
                                                        <span class="inst-name"><?php echo $profile['pf_label'] ?></span>        
                                                    </label>
                                                </span>    
                                            </div>  
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="exportStudentsConfirmed()">EXPORT</button>
                </div>
            </div>
        </div>
    </div>