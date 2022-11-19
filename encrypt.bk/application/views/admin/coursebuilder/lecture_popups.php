<!-- 
        ###################################################################
            ********  Whole Modal popups Sections are here ************
        ###################################################################
  -->
    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="deleteSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <span><i class="icon icon-attention-alt"></i></span>
                        <div class="form-group">
                            <b id="delete_header_text"></b>
                            <p class="m0" id="delete_message"></p>
                            <p><?php echo lang('action_cannot_undone') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-red" id="delete_lecture_ok" ><?php echo strtoupper(lang('yes')) ?>, <?php echo strtoupper(lang('delete')) ?>!</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade active-popup" data-backdrop="static" data-keyboard="false" id="active-lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="header_text"></b>
                            <p class="m0"><?php echo lang('are_you_sure') ?>.</p>
                            <p id="popup_message"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-red" id="change_status_section" ><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
    
    <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="select_live_mode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="m0">Please select the type of live which needs to be published to the students. Accordingly user will be able to connect & learn.</p>
                    <div class="text-center pad-top25">
                        <button type="button" onclick="startOrStopLive('<?php echo $lecture['live_lecture_id'] ?>','<?php echo $lecture['cl_course_id']?>', '<?php echo (isset($lecture['ll_is_online']) && $lecture['ll_is_online'])?'0':'1' ?>');" class="btn btn-green"><?php echo 'Go LIVE' ?></button>
                        <button type="button" onclick="startOrStopLive('<?php echo $lecture['live_lecture_id'] ?>','<?php echo $lecture['cl_course_id']?>', '<?php echo (isset($lecture['ll_is_online']) && $lecture['ll_is_online'])?'0':'2' ?>');" class="btn btn-green"><?php echo 'Go Virtual' ?> </button>
                    </div>
                </div>
                <div class="modal-footer">
                    
                </div>
            </div>
        </div>
    </div>
    
    
    
    <!-- 
        ###################################################################
            ********  Whole Modal popups Sections are here ************
        ###################################################################
  -->
    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="deleteSection" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <span><i class="icon icon-attention-alt"></i></span>
                        <div class="form-group">
                            <b>Delete { Course Name } course</b>
                            <p class="m0">Are you sure? You want to delete { course name }.</p>
                            <p>This action cannot be undone.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" >YES, DELETE !</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
    
    
     <div class="modal fade active-popup" data-backdrop="static" data-keyboard="false" id="publish_recorded_video" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="header_text_record"></b>
                            <p class="m0-record"></p>
                            <p id="popup_message_record"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="publish_record_video" ><?php echo strtoupper(lang('publish')) ?></button>
                    </div>
                </div>
            </div>
        </div>

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade active-popup" id="active-lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <div class="form-group">
                            <b>Activate Lecture:{ Section / Lecture Name }.</b>
                            <p class="m0">Are you sure? You want to activate lecture.</p>
                            <p>On activating the lecture, users can view this lecture in there curriculum.</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" >YES, DELETE !</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents :: Upload a lecture -->
    <div class="modal fade padd-r20" data-backdrop="static" data-keyboard="false" id="addquestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">UPLOAD QUESTION</h4>
                </div>
                <div class="modal-body">
                    <p class="mb30">Follow these steps to import your questions to the current test</p>
                    <div class="form-group mb30">
                        <p><b>Step 1:</b> Download the given Document <a href="<?php echo base_url('uploads/questiontemplate.xls') ?>" class="link-style"><em>questiontemplate.xls</em></a> and analyze the format</p>
                    </div>  
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill your questions in the document format.</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the questions, Upload your document.</p>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" class="form-control upload" id="upload_question">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_assessment_file" type="text">
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
                        <p><b>Step 4:</b> Review your questions.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" onclick="uploadQuestion()">UPLOAD</button>
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->

    <!-- Modal pop up contents :: Upload a lecture -->
    <div class="modal fade padd-r20" data-backdrop="static" data-keyboard="false" id="uploaddescriptive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">UPLOAD DESCRIPTIVE QUESTION</h4>
                </div>
                <div class="modal-body">
                    <?php echo lang('upload_question_description'); ?>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input accept=".pdf" type="file" class="form-control upload" id="upload_question">
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" onclick="uploadQuestion()">UPLOAD</button>
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->