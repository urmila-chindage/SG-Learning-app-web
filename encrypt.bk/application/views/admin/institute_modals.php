   <!-- ####### MODAL BOX STARTS ######### -->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_institute" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"><?php echo lang('create_new_institute') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label">Institute Name*:</label>
                        <input type="text" onkeypress="return preventSpecialCharector(event)" id="institute_name" placeholder="eg: Technical Institute" class="form-control">
                    </div>
                    <div class="form-group">
                        <label id="create_box_label"><?php echo lang('institute_code') ?>*:</label>
                        <input type="text" maxlength="6" id="institute_code" placeholder="eg: KTU01" class="form-control">
                    </div>
                    <div class="form-group">
                        <label id="create_box_label">Admin Email*:</label>
                        <input type="text" maxlength="50" id="institute_email" placeholder="eg: institute@institute.com" class="form-control">
                    </div>
                    <div class="form-group add-category clearfix">
                        <div class="add-selectn alignment-order custom-field">
                            <label>Admin Password*:</label>
                        <input type="text" maxlength="50" id="institute_password" class="form-control">
                        </div>
                        <div class="add-btn alignment-order">
                            <label>Or</label>
                            <a class="btn btn-green" onclick="generatePassword();">GENERATE PASSWORD<ripples></ripples></a>
                        </div>
                    </div>
                    <input type="hidden" name="institute_role" id="institute_role" value="8">
                    <div class="form-group" id="role_funcationlity_details"></div>
                    <div class="form-group">
                        <div class="checkbox"><label><input type="checkbox" name="send_mail" id="send_mail" value="1"><span class="ap_cont chk-box">Send an email to institute</span></label></div>
                    </div>                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="addInstitute()" id="create_box_ok"><?php echo lang('add') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->
<!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new warning-alert" data-backdrop="static" data-keyboard="false" id="activate_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="icon-align text-center">
                                <span class="alert-icon"></span>
                            </div>
                            <p id="confirm_box_title"></p>
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
                        <div class="col-sm-12 pad-top10">
                            <label>Select Courses :</label>
                        </div>
                    </div>
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
    <div class="modal fade modal-redactor-height" data-backdrop="static" data-keyboard="false" id="invite-user-bulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                Subject* : <div class="form-group">
                                    <input id="invite_send_subject" type="text" class="form-control" placeholder="subject">
                                </div>
                            </div>
                            <div class="col-sm-12">
                                Message *:
                                <textarea class="form-control invite-user-message min-430 inviteuser-minheight" id="redactor_invite" name="ck-text-editor">

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

    <!-- Modal pop up contents :: Bulk import institutes -->
    <div class="modal fade padd-r20" data-backdrop="static" data-keyboard="false" id="import-institutes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">IMPORT INSTITUTES</h4>
                </div>
                <div class="modal-body">
                    <p class="mb30">Follow these steps to import institutes</p>
                    <div class="form-group mb30">
                        <p><b>Step 1:</b> Download the given Document <a href="<?php echo base_url() . config_item('upload_folder') . '/institute/template.csv' ?>" class="link-style"><em>template.csv</em></a> and analyze the format</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill the institutes in the document format.</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the institutes, Upload your .csv file.</p>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="upload custom-upload" id="import_institute">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_institute_file" type="text">
                      </div>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                        <div class="progress width100">
                            <div style="width: 60%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                <span class="sr-only">60% Complete</span>
                            </div>
                        </div>
                        <span class="">Importing...<b class="percentage-text">60%</b></span>
                    </div>

                    <div class="form-group mb30">
                        <p><b>Step 4:</b> Review institutes list.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green" onclick="uploadInstitute()">IMPORT</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Import institutes -->
