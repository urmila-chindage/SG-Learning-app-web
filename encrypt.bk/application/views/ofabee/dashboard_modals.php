<!-- Modal pop up contents:: Change Password Popup-->
<div class="modal fade" id="change_password" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo lang('change_pass'); ?>
                </h4>
                <div id="message" class="error-message-password"></div>
            </div>
            <form role="form" method="post">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('enter_pass'); ?></label>
                        <input type="password" class="form-control"
                               name="curr_password" id="curr_password"  placeholder="<?php echo lang('enter_pass'); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1"><?php echo lang('new_pass'); ?></label>
                        <input type="password" class="form-control"
                               id="password_confirmation" name="password_confirmation" placeholder="<?php echo lang('new_pass'); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1"><?php echo lang('confirm_new_pass'); ?></label>
                        <input type="password" class="form-control"
                               id="password" name="password"   placeholder="<?php echo lang('confirm_new_pass'); ?>"/>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">
                        <?php echo lang('cancel') ?>
                    </button>
                    <button type="button" class="btn btn-green" id="change_pass_btn">
                        <?php echo lang('change_pass') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pop up contents:: Edit Profile Popup-->
<div class="modal fade" id="edit-profile" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo lang('edit_profile'); ?>
                </h4>
                <div id="edit_profile_message" class="edit-profile-message"></div>
            </div>
            <form role="form" method="post">
                <!-- Modal Body -->
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo lang('user_fullname'); ?></label>
                        <input type="text" class="form-control" name="us_name" id="user_fullname" value="<?php echo isset($user_details['us_name']) ? $user_details['us_name'] : ''; ?>"  placeholder="<?php echo lang('user_fullname_ph'); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1"><?php echo lang('user_phone'); ?></label>
                        <input type="text" class="form-control" id="user_phone" name="us_phone" value="<?php echo isset($user_details['us_phone']) ? $user_details['us_phone'] : ''; ?>" placeholder="<?php echo lang('user_phone_ph'); ?>"/>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1"><?php echo lang('user_about_me'); ?></label>
                        <textarea class="form-control" id="us_about" name="us_about" placeholder="<?php echo lang('user_about_me_ph'); ?>">
                            <?php echo isset($user_details['us_about']) ? $user_details['us_about'] : ''; ?>
                        </textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-red" data-dismiss="modal">
                        <?php echo lang('cancel') ?>
                    </button>
                    <button type="button" class="btn btn-green" id="update_user_profile">
                        <?php echo strtoupper(lang('update')); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="teachers-change" tabindex='-1' class="modal fade ofabee-modal" role="dialog">
    <div class="modal-dialog"> 
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header modal-head-space">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title modal-pswd-head">Change Password</h4>
            </div>
            <div class="modal-body">
                <div id="password_change_message" class="error-message-password" style="display:  none;">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-12">            
                            <span class="pswd-labels">Old password</span>
                        </div><!--columns-->
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="curr_password_beta" class="change-password-fields"  type="password">
                        </div><!--columns-->
                    </div><!--row-->
                </div>
            

            
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-12">            
                            <span class="pswd-labels">New password</span>
                        </div><!--columns-->
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="password_confirmation_beta" class="change-password-fields"  type="password">
                        </div><!--columns-->
                    </div><!--row-->
                </div>
           

           
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5 col-sm-5 col-xs-12">            
                            <span class="pswd-labels">Confirm New password</span>
                        </div><!--columns-->
                        <div class="col-md-6 col-sm-6 col-xs-12">
                            <input id="password_beta" class="change-password-fields"  type="password">
                        </div><!--columns-->
                    </div><!--row-->            
                </div>
          
            </div>
            <div class="modal-footer ofabee-modal-footer text-center-alter">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
                <button id="change_pass_btn" type="button" class="btn ofabee-orange">Submit</button>
            </div>
        </div>
    </div>
</div>