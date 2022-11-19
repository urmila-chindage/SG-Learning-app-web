
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

    <!-- Modal pop up contents:: uncheck Delete Section popup-->
        <div class="modal fade alert-modal-new" id="uncheck_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="confirm_box_content_1"><?php echo lang('uncheck_delete') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: uncheck Delete Section popup-->

    <!-- Modal pop up contents:: please check any popup-->
        <div class="modal fade alert-modal-new" id="check_any" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="confirm_box_content_1"><?php echo lang('check_any') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: please check any popup-->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_page" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label"></label>
                        <input type="text" maxlength="80" id="page_name" placeholder="eg: Article on Aptitude" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" id="create_box_ok"><?php echo lang('create') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->