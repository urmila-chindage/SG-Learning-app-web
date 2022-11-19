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