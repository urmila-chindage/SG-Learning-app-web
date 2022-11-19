<div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_role" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="top: 30px;">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label id="create_box_label">Role Title</label>
                        <input type="text" maxlength="50" id="role_name" placeholder="eg: Student" class="form-control">
                    </div>
                    <div class="form-group" id="role_status_div">
                        <label>Status:</label>
                        <select id="role_status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="1">Active</option> 
                            <option value="0">Inactive</option>                                                        
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-green" id="create_box_ok"><?php echo lang('create') ?></button>
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade alert-modal-new" id="activate_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="confirm_box_ok">CONTINUE</button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->
