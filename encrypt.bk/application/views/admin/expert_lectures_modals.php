<!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" id="create_expert_lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label"></label>
                        <input type="text" maxlength="80" id="expert_lecture_name" placeholder="eg: Lecture on Organic Chemistry." class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" id="create_box_ok"><?php echo lang('create') ?></button>
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->

        <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="activate_expert_lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
    
    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="notify_deleted_expert" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="notify_deleted_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="notify_deleted_content"><?php echo lang('notify_deleted') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->