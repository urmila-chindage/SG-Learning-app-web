<!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="group-name" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                        <h4 class="modal-title" id="myModalLabel"><span id="batch_model">CREATE NEW BATCH</span></h4>
                    </div>
                    <div class="modal-body">
                        <!-- <div class="col-sm-12">
                            Course* :
                            <div class="form-group" id="course_div">
                                <select name="course" class="form-control" >

                                </select>
                            </div>
                        </div> -->
                        <div class="col-sm-12">
                            Batch Name* :
                            <div class="form-group">
                                <input id="group_name" onkeypress="return preventSpecialCharector(event)" type="text" class="form-control" placeholder="eg: Rockers">
                            </div>
                        </div>
                        <div class="col-sm-12" id="institution_div_wrapper">
                            Institution* :
                            <div class="form-group" id="institution_div">
                                <select name="institution" class="form-control" >

                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            Batch Year* :
                            <div class="form-group">
                                <input id="group_year" type="text" class="form-control" placeholder="eg: 2012" maxlength="4">
                            </div>
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
                        <a href="javascript:void(0)" class="btn btn-green" id="create-btn" onclick="saveGroup()" >CREATE</a>

                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents :: Create Group -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="attach-group-users" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="course_group_create"></h4>
                </div>
                <div class="modal-body ">
                    <div class="rTable content-nav-tbl normal-tbl" style="border-bottom: 0px;border-top: 1px solid #a7aaae;border-right: 1px solid #a7aaae;">
                        <div class="rTableRow">
                            <div class="rTableCell" style="background: #fff;">
                                <a href="javascript:void(0)" class="select-all-style">
                                    <label> 
                                        <input value="1" class="select-users-new-group-parent" type="checkbox">  Select All
                                    </label>
                                    <span id="reflectCount"></span>
                                </a>
                            </div>
                            <div class="rTableCell">
                                <div class="input-group">
                                    <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search Student"  style="border: 0px;">
                                    <span id="usersearchclear" style="">×</span>
                                    <a class="input-group-addon" id="user_keyword_btn" style="background: #fff;border: 0px;border-left: 1px solid #ccc;">
                                        <i class="icon icon-search"> </i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="inside-box pos-rel" id="users_new_group_wrapper" style="border-radius: 0px;"></div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-red " data-dismiss="modal" >CANCEL</a>
                    <a href="javascript:void(0)" id="create_group_users" type="button" class="btn btn-green">ATTACH</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Group  -->

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

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="confirm_messages_group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
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

<!-- Modal pop up contents:: Delete Section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="view-group-course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="course_group_course"></h4>
                </div>
                <div class="modal-body ">

                    <div class="inside-box pos-rel" id="course_group_wrapper">

                        <!-- Nav section inside this wrap  --> <!-- START -->
                        <!-- =========================== -->
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">

                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl" 
                                style="border-bottom: 1px solid #ccc;">
                                    <div class="rTableRow">
                                       

                                        <div class="rTableCell">
                                            <!-- <div class="input-group">
                                                <input type="text" class="form-control srch_txt" id="user_keyword" placeholder="Search Student">
                                                <span id="usersearchclear" style="">×</span>
                                                <a class="input-group-addon" id="user_keyword_btn">
                                                    <i class="icon icon-search"> </i>
                                                </a>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Nav section inside this wrap  --> <!-- END -->
                        <!-- =========================== -->

                    </div>
                </div>
                <div class="modal-footer">
                    <a type="button" class="btn btn-red " data-dismiss="modal" >CANCEL</a>
                    <!-- <a href="javascript:void(0)" id="create_group_users" type="button" class="btn btn-green">ATTACH</a> -->
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents:: Delete Section popup-->