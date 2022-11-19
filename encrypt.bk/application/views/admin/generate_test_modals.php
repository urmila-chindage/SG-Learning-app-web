<div class="modal modal-full fade" data-backdrop="static" data-keyboard="false" id="question_preview" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document" style="font-weight: normal;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">PREVIEW QUESTIONS</h4>
                </div>
                <div class="modal-body" style="padding: 0 !important;">
                    <iframe id="preview_frame" src="" style="width: 100%;"></iframe>
                </div>

                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
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
                    <button type="button" class="btn btn-green" data-dismiss="modal"><?php echo strtoupper(lang('cancel')) ?></button>
                    <button type="button" class="btn btn-red" id="delete_generate_ok"><?php echo strtoupper(lang('yes')) ?>, <?php echo strtoupper(lang('delete')) ?> !</button>
                </div>
            </div>
        </div>
    </div>
<!-- !.Modal pop up contents :: Delete Section popup--> 

<!-- Modal pop up contents:: Delete Section popup-->
<div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="common_message_generate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 9999">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="generate_message_header"></b>
                            <p class="m0" id="generate_message_content"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal">OK</button>
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
                    <h4 class="modal-title" id="myModalLabel">IMPORT QUESTION</h4>
                </div>
                <div class="modal-body">
                    <p class="mb30">Follow these steps to import your questions.</p>
                    <div class="form-group mb30">
                    <p><b>Step 1:</b> Download the given document <a href="<?php echo base_url('uploads/questiontemplate.xls') ?>" class="link-style"><em>questiontemplate.xls</em></a> or <a href="<?php echo uploads_url('uploads/questiontemplate.docx') ?>" class="link-style"><em>questiontemplate.docx</em></a> and analyze the format</p>                    </div>  
                     <div class="form-group mb30">
                        <p><b>Step 2:</b> Fill your questions in the document format.</p>
                    </div>
                     <div class="form-group mb30">
                        <p><b>Step 3:</b> After you have filled with the questions, Upload your document.</p>
                    </div>
                    <!--p>Add a course category</p-->
                    <div class="form-group clearfix">
                        <select class="form-control" id="q_parent_cat" name="q_parent_cat"> 
                            <option value="0">Select Category</option>
                            <?php foreach ($q_parent_category as $category):?>
                               <option value="<?php echo $category['id'] ?>"><?php echo $category['ct_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group clearfix">
                      <div class="fle-upload">
                        <label class="fle-lbl">BROWSE</label>
                        <input type="file" value="" class="form-control upload" id="upload_question">
                        <input value="" readonly="" class="form-control upload-file-name" id="upload_file_name" type="text">
                      </div>
                    </div>
                    <div class="clearfix progress-custom" id="percentage_bar" style="display: none">
                        <div class="progress width100">
                            <div style="width: 0%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="60" role="progressbar" class="progress-bar progress-bar-success">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <span class="" >Uploading...<b class="percentage-text" id="question_upload_processing">0%</b></span>
                    </div>

                    <div class="form-group mb30">
                        <p><b>Step 4:</b> Review your questions.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-green" id="save_upload_question" onclick="uploadQuestion()">UPLOAD</button>
                </div>
            </div>
        </div>
    </div>
    <div id="question_template_preview" style="display:none;">
    </div>
    <!-- Modal pop up contents :: Upload a lecture -->
