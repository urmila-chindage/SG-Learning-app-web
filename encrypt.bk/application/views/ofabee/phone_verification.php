<?php include_once 'header.php'; 
//unset($session['us_phone_verfified']);
?>

<section>
    <div class="my-profile-blocks">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="row">
                    <div class="col-md-12"><?php include_once('messages.php'); ?></div>
                    <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-2" style="margin-top: 50px;">
                        <div class="myprofile-cards-wraper">
                            <div class="myprofile-card-head">
                                <span class="my-profile-about">Verify your Phone Number</span>
                                <span class="pensil-wrap" id="mobile_edit" onclick="step_edit_mobile()">
                                    <img class="edit-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-pencil.svg">
                                </span><!--pensil-wrap-->
                            
                                <span class="save-close-wrap" style="display: inline;">
                                    <span class="save-head" onclick="step_save_mobile_edit()" id="mobile_save" style="display: none;">Save</span><!--save-head-->
                                    
                                    <img class="edit-close" id="mobile_cancel" onclick="cancel_step_edit_mobile()" style="display: none;" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/edit-close.svg">
                                </span><!--save-close-wrap-->
                            </div><!--myprofile-card-head-->

                            <div class="myprofile-card-body">
                                <span class="table-wrap">
                                    <span class="table-cell-mail">Email</span>
                                    <span class="table-cell-e-address pad-11"><?php echo $user_details['us_email'] ?></span>
                                </span><!--table-wrap-->

                                <span class="table-wrap">
                                    <span class="table-cell-mail">Phone</span>
                                    <span class="table-cell-e-address  pad-11" id="myphone">
                                        <?php echo $user_details['us_phone'] ?>  
                                    </span>
                                    <span class="table-cell-e-address">
                                        <input type="text" style="display:none;" maxlength="10" id="mobile_form" class="replace-text field_label_form" placeholder="Phone Number">
                                    </span>
                                </span><!--table-wrap-->

<!--                                <span class="table-wrap">
                                    <span class="table-cell-mail">Lectures Completed</span>
                                    <span class="table-cell-e-address"></span>
                                </span>table-wrap

                                <span class="table-wrap">
                                    <span class="table-cell-mail">Tests Completed</span>
                                    <span class="table-cell-e-address"></span>
                                </span>table-wrap-->

                            </div><!--myprofile-card-body-->
                        </div><!--myprofile-cards-wraper-->
                    </div><!--columns-->
                </div><!--row-->               
                <!-- <div class="row"><div class="col-md-4"></div><div class="col-md-4"><a href="<?php echo site_url('/dashboard/complete_confirm'); ?>" class="btn  btn-orange2 my-profile-btn">Complete Profile</a></div><div class="col-md-4"></div></div>  -->
            </div><!--container-reduce-width-->
        </div><!--container container-altr-->  
    </div><!--my-profile-blocks-->
</section>
<section>

<section>
    <div id="SendOtp" class="modal fade ofabee-modal" role="dialog">
        <div class="modal-dialog ofabee-modal-dialog">
            <div class="modal-content ofabee-modal-content">
                <div class="modal-header ofabee-modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title ofabee-modal-title">Send OTP</h4>
            </div>
            <div class="modal-body ofabee-modal-body">
                <input type="text" id="number_preview" disabled="disabled" class="form-control input-sm">
                <span class="save-head" style="color: red;"><br/>You will not be allowed to change your mobile number once you complete your profile.</span>
            </div>
            <div class="modal-footer ofabee-modal-footer">
                <button type="button" id="sendOtpcancelBtn" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
                <button type="button" id="sendOtpBtn" onclick="step_sendOtpConfirmed()" class="btn ofabee-orange">Send</button>
            </div>
        </div>
    </div>
</section>
<section>
    <div id="VerifyOtp" class="modal fade ofabee-modal" role="dialog">
        <div class="modal-dialog ofabee-modal-dialog">
            <div class="modal-content ofabee-modal-content">
                <div class="modal-header ofabee-modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title ofabee-modal-title">Verify OTP</h4>
            </div>
            <div class="modal-body ofabee-modal-body">
                <span class="save-head" id="otpMessage"></span>
                <input type="text" id="otpText" placeholder="Enter OTP." maxlength="4" class="form-control input-sm">
            </div>
            <div class="modal-footer ofabee-modal-footer">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn ofabee-orange" id="verifyOtpBtn" onclick="step_verifyOtp()">Verify</button>
            </div>
        </div>
    </div>
</section> 
<script  src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.matchHeight.js"></script>
<script type="text/javascript">
var __site_url = '<?php echo site_url() ?>';
</script>
<?php include_once('complete_phone_verification_script.php'); ?>

<?php include_once 'footer.php'; ?>