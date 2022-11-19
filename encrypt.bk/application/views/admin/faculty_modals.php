<style>
.addcourse-table {
    width: 100%;
    background: #fff;
    border: 1px solid #a7aaae;
    top:0px !important;
}
.addcourse-table tr td {
    border-left: 1px solid #a7aaae;
    border-bottom: 1px solid #a7aaae;
    padding: 8px 15px;
}
.inside-box {border-top: 0px;}
.inside-box .checkbox-wrap {
    padding: 5px 0px;
}
</style>   
   
   <!-- ####### MODAL BOX STARTS ######### -->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_faculty" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"><?php echo lang('create_new_faculty') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label"><?php echo lang('name') ?>*:</label>
                        <input type="text" onkeypress="return preventSpecialCharector(event)" maxlength="50" id="faculty_name" placeholder="eg: John Milton" class="form-control">
                    </div>
                    <div class="form-group">
                        <label id="create_box_label"><?php echo lang('email') ?>*:</label>
                        <input type="text" maxlength="50" id="faculty_email" placeholder="eg: johnmilton@gmail.com" class="form-control">
                    </div>
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order custom-field">
                            <label><?php echo lang('password') ?> *:</label>
                        <input type="text" maxlength="50" id="faculty_password" class="form-control">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a class="btn btn-green" onclick="generatePassword();">GENERATE PASSWORD<ripples></ripples></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label><?php echo lang('faculty_role') ?>*:</label>
                        <select id="faculty_role" class="form-control">
                            <option value="">Choose Role</option>
                            <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id'] ?>"><?php echo $role['rl_name'] ?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                    <div class="form-group" id="institute_select" style="display: none;">
                        <label>Institute*:</label>
                        <select id="faculty_institute" class="form-control">
                            <option value="">Choose Institute</option>
                            <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id'] ?>"><?php echo $role['rl_name'] ?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                    <div class="form-group" id="role_funcationlity_details"></div>
                    <div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" name="send_mail" id="send_mail" value="1"><span class="ap_cont chk-box">Send an email to faculty</span></label></div>
                    </div>                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="addFaculty()" id="create_box_ok"><?php echo lang('create') ?></button>

                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->
<!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="activate_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b style="font-size:20px;">Alert !</b><br>
                            <b id="confirm_box_title"></b>
                            <p class="m0" id="confirm_box_content_1"> </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" id="confirm_box_cancel" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="confirm_box_ok">CONTINUE</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents :: ADD to Catalog -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="add-users-course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">ADD FACULTIES TO COURSE</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Select Courses :</label>
                        </div>
                    </div>
                    <!-- course name & course status head -->
                        <!-- <div class="addcourse-table-head">
                            <div class="text-left course-name">Course Name</div>
                            <div class="float-right course-status">Status</div>
                        </div> -->
                        <table class="addcourse-table" style="">
                            <tbody>
                                <tr>
                                    <td class="text-left course-name">Course Name</td>
                                    <td class="text-center">Status</td>
                                </tr>
                            </tbody>
                        </table>
                    <!-- ends -->
                    <div class="inside-box" id="course_list_wrapper">
                    </div>
                </div>

                <div class="modal-footer">
                   
                    <a type="button" class="btn btn-red" data-dismiss="modal">CANCEL</a>
                    <a href="javascript:void(0)" type="button" id="add_user_ok" class="btn btn-green">ADD</a>
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
                    <a href="javascript:void(0);" id="message_send_button" type="button" onclick="sendMessageBulk()" class="btn btn-green">SEND</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users -->

    <!-- Modal pop up contents :: Bulk import faculties -->
    <div class="modal fade padd-r20" id="import-faculties" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">IMPORT FACULTIES</h4>
                </div>
                <div class="modal-body">
                    <p class="mb30">Follow these steps to import faculties</p>
                    <div class="form-group mb30">
                        <p><b>Step 1:</b> Download the given Document <a href="<?php echo base_url() . config_item('upload_folder') . '/faculty/template.csv' ?>" class="link-style"><em>template.csv</em></a> and analyze the format</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill the faculties in the document format.</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the faculties, Upload your .csv file.</p>
                    </div>
                    <div class="form-group mb30">
                        <label><?php echo lang('faculty_role') ?>*:</label>
                        <select id="faculty_role_bulk" class="form-control">
                            <option value="">Choose Role</option>
                            <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                            <option value="<?php echo $role['id'] ?>"><?php echo $role['rl_name'] ?></option>
                            <?php endforeach;?>
                            <?php endif;?>
                        </select>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload" id="import_faculty">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_faculty_file" type="text">
                      </div>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                        <div class="progress width100">
                            <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                <span class="sr-only">60% Complete</span>
                            </div>
                        </div>
                        <span class="">Uploading...<b class="percentage-text">60%</b></span>
                    </div>

                    <div class="form-group mb30">
                        <p><b>Step 4:</b> Review faculties list.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green" onclick="uploadFaculty()">UPLOAD</button>
                    
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Import faculties -->