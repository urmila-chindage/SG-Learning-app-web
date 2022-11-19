   <!-- ####### MODAL BOX STARTS ######### -->

    <!-- Modal pop up contents :: ADD Teacher -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="add-teacher" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel">ASSIGN FACULTIES</h4>
                </div>
                <div class="modal-body">
                    <div class="inside-box pos-rel pad-top50">
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">
                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl fixed-head">
                                    <div class="rTableRow">
                                        <!-- <div class="rTableCell">
                                            <a href="javascript:void(0)" class="select-all-style">
                                                <label>
                                                    <input value="1" class="select-users-new-group-parent" id="groupAll" type="checkbox"> Select All </label>
                                            </a>
                                        </div> -->
                                        <div class="rTableCell dropdown" style="min-width:300px;">
                                            <a href="javascript:void(0)" class="dropdown-toggle min-width115" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="filter_dropdown_text">All Faculties<span class="caret"></span></a>
                                            <ul class="dropdown-menu white override-dropdown" id="role_filters">
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="add-selectn alignment-order">
                                    <div class="inside-box-padding inside-box-scroll" id="get_tutor_list">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="import_tutor_confirmed()">ASSIGN</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: ADD Teacher -->

    <!-- Modal pop up contents :: ADD to Catalog -->
    <div class="modal fade" id="create-catalog" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_to_catalog') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            <?php echo lang('catalog_name') ?> *:
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" id="catalog_id" onchange="initFetchCatalogCourse(this.value)">
                                </select>
                                <input type="text" id="catalog_name" class="form-control" placeholder="eg: Catalog" aria-describedby="basic-addon2">
                            </div>
                        </div>
                        <div class="col-sm-1"><p class="pad-top10">Or</p></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <a href="javascript:void(0)" class="btn btn-green pull-right" id="create_new_catalog"><?php echo lang('create_new_catalog') ?></a>
                                <a href="javascript:void(0)" class="btn btn-danger pull-right" id="create_new_catalog_cancel"><?php echo lang('cancel') ?></a>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><?php echo lang('select_courses') ?> *:</label>
                        </div>
                    </div>

                    <div class="inside-box" id="catalog_course_list">

                    </div>
                </div>
                <div class="container-fluid">
                    <div class="pull-right">
                        <?php echo lang('total_selected_course_worth') ?>: <span class="green-font font-bold" id="total_price"></span>
                    </div>
                </div>

                <div class="container-fluid pad-top30">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                Catalog Price *:
                                <div class="input-group">
                                    <input type="text" id="catalog_price" class="form-control" placeholder="eg: 3000"    aria-describedby="basic-addon2">
                                    <span class="input-group-addon" id="basic-addon2"><?php echo lang('indian_rupees') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                Catalog Discount Price *:
                                <div class="input-group">
                                    <input type="text" id="catalog_price_discount" class="form-control" placeholder="eg: 200"    aria-describedby="basic-addon2">
                                    <span class="input-group-addon" id="basic-addon2"><?php echo lang('indian_rupees') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" class="btn btn-green" onclick="addToCatalogProceed();">CREATE</a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: ADD Teacher -->

     <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="publish-review" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title_review"></b>
                            <p class="m0" id="confirm_box_content_review"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="confirm_box_ok_review" ><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="publish-course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="confirm_box_title"></b>
                            <p class="m0" id="confirm_box_content"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                        <button type="button" class="btn btn-green" id="confirm_box_ok" ><?php echo lang('continue') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents:: uncheck inactive courses popup-->
        <div class="modal fade alert-modal-new" id="inactive_course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="notify_inactive_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="notify_ok_content"><?php echo lang('notify_inactive_courses') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" id="confirm_box_inactive_ok" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="notify_deleted" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade alert-modal-new" id="notify_inactive" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="noti_inactive_title" style="display:inline-block;font-size:17px;margin-bottom:15px;"><?php echo lang('alert') ?></b>
                            <p class="m0" style="font-size:17px;" id="notify_deleted_content"><?php echo lang('notify_inactive') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Delete Section popup-->

     <!-- Modal pop up contents:: Add to catalog bulk action error popup-->
        <div class="modal fade alert-modal-new" id="add_catalog_null" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="notify_deleted_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="notify_deleted_content"><?php echo lang('select_any') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Add to catalog bulk action error popup-->

 <!-- Modal pop up contents:: select any course-->
        <div class="modal fade alert-modal-new" id="select_any" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="notify_deleted_title"><?php echo lang('alert') ?></b>
                            <p class="m0" id="notify_deleted_content"><?php echo lang('select_any') ?></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('ok') ?></button>
                    </div>
                </div>
            </div>
        </div>
    <!-- !.Modal pop up contents :: Add to catalog bulk action error popup-->

    <!-- Modal pop up contents :: Create new section popup-->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="create_course" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="create_box_title">CREATE NEW COURSE</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Course Code *</label>
                        <input type="text" maxlength="5" id="course_code" placeholder="eg: MA44" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Course Name *</label>
                        <input type="text" maxlength="50" id="course_name" placeholder="eg: Quantitative Aptitude" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal" ><?php echo lang('cancel') ?></button>
                    <button type="button" class="btn btn-green" onclick="createCourseConfirmed()" id="create_box_ok"><?php echo lang('create') ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- !.Modal pop up contents :: create new section popup-->

     <!-- Modal pop up contents :: ADD to Catalog -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="create-catalog-new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('create_new_catalog') ?></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><?php echo lang('catalog_title') ?> *:</label>
                            <div>
                                <input type="text" maxlength="50" class="form-control" placeholder="eg: Mathematical Calculations" id="catalog_name_create">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><?php echo lang('select_courses') ?> *:</label>
                        </div>
                    </div>
                    <div class="inside-box" id="catalog_course_wrapper">
                    </div>
                </div>
                <div class="container-fluid">
                    <div class="pull-right">
                        <?php echo lang('total_selected_course_worth') ?>: <span class="green-font font-bold" id="total_catalog_course_price"></span>
                    </div>
                </div>

                <div class="container-fluid pad-top30">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo lang('catalog_price') ?> *:
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="eg: 3000" id="catalog_price_create"   aria-describedby="basic-addon2">
                                    <span class="input-group-addon" id="basic-addon2"><?php echo lang('indian_rupees') ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo lang('catalog_discount_price') ?> *:
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="eg: 200"  id="catalog_discount_create"  aria-describedby="basic-addon2">
                                    <span class="input-group-addon" id="basic-addon2"><?php echo lang('indian_rupees') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" class="btn btn-green" id="create_catalog_new"><?php echo lang('create') ?></a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
        </div>



     <!-- Modal pop up contents :: Create Group -->
    <div class="modal fade" id="add-user-to-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_group') ?></h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-sm-12">
                            Select a Batch* :
                        </div>
                        <div class="col-sm-7">
                            <div class="form-group">
                                <select class="form-control" id="group_id" onchange="initFetchGroupUsers(this.value)" >
                                </select>
                                <input type="text" aria-describedby="basic-addon2" placeholder="eg: Batch name" class="form-control" id="group_name">
                            </div>
                        </div>
                        <div class="col-sm-1"><p class="pad-top10">Or</p></div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <a href="javascript:void(0)" class="btn btn-green pull-right" id="create_new_group"><?php echo lang('create_new_group') ?></a>
                                <a id="create_new_group_cancel" class="btn btn-danger pull-right" href="javascript:void(0)" ><?php echo lang('cancel') ?></a>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-12 pad-top10">
                            <label><?php echo lang('select_users') ?> *:</label>
                        </div>
                    </div>
                    <div class="inside-box" id="group_users">

                    </div>
                </div>


                <div class="modal-footer">
                    <a href="javascript:void(0)" id="add_user_to_group" type="button" class="btn btn-green"><?php echo lang('create') ?></a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Group  -->

    <!-- Modal pop up contents:: Delete Section popup-->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="group-name" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                        <h4 class="modal-title" id="myModalLabel">CREATE NEW BATCH</h4>
                    </div>
                    <div class="modal-body">
                        <div class="col-sm-12">
                        Batch Name* :
                            <div class="form-group">
                                <input id="course_group_name" type="text" class="form-control" placeholder="eg: Rockers">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="javascript:void(0)" class="btn btn-green" id="create-btn" onclick="saveGroup()" >CREATE</a>
                        <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
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

    <!-- Modal pop up contents :: Create Group -->
    <div class="modal fade" data-backdrop="static" data-keyboard="false" id="attach-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="course_group_create"></h4>
                </div>
                <div class="modal-body ">

                    <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper">

                        <!-- Nav section inside this wrap  --> <!-- START -->
                        <!-- =========================== -->
                        <div class="container-fluid nav-content pos-abslt width-100p nav-js-height">

                            <div class="row">
                                <div class="rTable content-nav-tbl normal-tbl" style="">
                                    <div class="rTableRow">
                                        <div class="rTableCell">
                                            <a href="javascript:void(0)" class="select-all-style"><label> <input value="1" class="select-users-new-group-parent" type="checkbox">  Select All</label></a>

                                        </div>

                                        <div class="rTableCell">
                                            <div class="input-group">
                                                <input type="text" class="form-control srch_txt" id="user_for_new_group_keyword" placeholder="Search Batch">
                                                <a class="input-group-addon" id="search_user_for_new_group">
                                                    <i class="icon icon-search"> </i>
                                                </a>
                                            </div>
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
                    <a href="javascript:void(0)" id="create_group_users" type="button" class="btn btn-green">Attach</a>
                    <a type="button" class="btn btn-red " data-dismiss="modal" >CANCEL</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Create Group  -->

    <!-- Modal pop up created by Yadu Chandran :: Course Users Bulk-->
    <!-- Modal pop up contents :: Invite Users -->
    <div class="modal modal-full fade" data-backdrop="static" data-keyboard="false" id="invite-course-user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <h4 class="modal-title" id="myModalLabel"><?php echo lang('send_user_invite_title') ?></h4>
                </div>
                <div class="modal-body">

                    <div>
                        <div class="col-sm-12">
                            <?php echo lang('send_invite_to') ?>

                            <select id="tokenize_course_user" multiple="multiple" class="form-control tokenize-sample custom-token">

                            </select>
                        </div>
					</div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-7">
                                <?php echo lang('send_invite_subject') ?>
                                <div class="form-group">
                                    <input id="course_send_subject" type="text" class="form-control" placeholder="<?php echo lang('send_invite_subject_ph') ?>">
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <?php echo lang('send_invite_mail_template') ?>
                                <div class="form-group">
                                    <select class="form-control" id="email_template_list_course" class="email-template-list">
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <?php echo lang('send_invite_message') ?>

                                <textarea class="form-control invite-user-message min-430" id="redactor_course_bulk" name="ck-text-editor">

                                </textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" onclick="sendCourseMessageBulk()" class="btn btn-green"><?php echo lang('create') ?></a>
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
                    <a href="javascript:void(0);"  id="message_send_button" type="button" class="btn btn-green">SEND</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up created by Yadu Chandran :: Course Users individual mail send-->
    <!-- Modal pop up contents :: Invite Users -->


    <!-- Modal pop up created by Yadu Chandran :: Course User Groups mail send Bulk-->
    <!-- Modal pop up contents :: Invite Group Members Bulk -->
    <div class="modal modal-full fade" data-backdrop="static" data-keyboard="false" id="invite-group-bulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

                            <select id="tokenize_group_bulk" multiple="multiple" class="form-control tokenize-sample custom-token">

                            </select>
                        </div>
					</div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-7">
                                <?php echo lang('send_invite_subject') ?>
                                <div class="form-group">
                                    <input id="course_subject_group_bulk" type="text" class="form-control" placeholder="<?php echo lang('send_invite_subject_ph') ?>">
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <?php echo lang('send_invite_mail_template') ?>
                                <div class="form-group">
                                    <select class="form-control" id="email_template_group_bulk" class="email-template-list">
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <?php echo lang('send_invite_message') ?>

                                <textarea class="form-control invite-user-message min-430" id="redactor_group_bulk" name="ck-text-editor">

                                </textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" onclick="sendGroupMsgBulk()" class="btn btn-green"><?php echo lang('create') ?></a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal pop up contents :: Invite Users -->


    <!-- Modal pop up contents :: Send Mail To Course Group: chandu s -->

    <div class="modal modal-full fade" data-backdrop="static" data-keyboard="false" id="invite-group-email" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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

                            <select id="tokenize_group_individual" multiple="multiple" class="form-control tokenize-sample custom-token">

                            </select>
                        </div>
					</div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="col-sm-7">
                                <?php echo lang('send_invite_subject') ?>
                                <div class="form-group">
                                    <input id="course_subject_group_individual" type="text" class="form-control" placeholder="<?php echo lang('send_invite_subject_ph') ?>">
                                </div>
                            </div>

                            <div class="col-sm-5">
                                <?php echo lang('send_invite_mail_template') ?>
                                <div class="form-group">
                                    <select class="form-control" id="email_template_group_individual" class="email-template-list">
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <?php echo lang('send_invite_message') ?>

                                <textarea class="form-control invite-user-message min-430" id="redactor_group_individual" name="ck-text-editor">

                                </textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="javascript:void(0);" type="button" onclick="sendGroupMsgIndividual()" class="btn btn-green"><?php echo lang('create') ?></a>
                    <a type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pop up contents :: Send Mail To Course Group End -->
    <div class="modal fade alert-modal-new" id="select_live_mode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-small" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                </div>
                <div class="modal-body">
                    <p class="m0">Please select the type of live which needs to be published to the students. Accordingly user will be able to connect & learn.</p>
                    <?php if (isset($live_class)): ?>
                    <div class="text-center pad-top25">
                        <a class="btn btn-light-green" target="_blank" href="<?php echo site_url('live/golive/' . $live_class['live_lecture_id']) ?>"><?php echo 'Go LIVE'; ?></a>
                        <a class="btn btn-light-green" target="_blank" href="<?php echo site_url('live/join/' . $live_class['live_lecture_id']) ?>"><?php echo 'Go VIRTUAL'; ?></a>
                    </div>
                    <?php endif;?>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>