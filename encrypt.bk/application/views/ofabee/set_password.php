<?php include('login_header.php'); ?>
<title><?php echo lang('set_new_password_title') ?></title>

<!-- Manin Iner container start -->
    <section class="Login-fullwdth">
        <div class="talbe-cell">
            <div class="login-wrap">
                <!-- Login box top section -->
                <div class="login-typography login-padding clearfix">
                    <h3><b><?php echo lang('reset_password'); ?></b></h3>
                    <?php include_once('messages.php') ?>
                    <?php  $attributes = array('class' => 'set-new-password', 'id' => 'set_new_password');
                    echo form_open(site_url('/login/password_set'),$attributes); ?>
                    
                        <div class="form-group email">
                            <input type="text" class="login-input" id="set_email" data-validation-error-msg-email="<?php echo lang('email_error'); ?>" data-validation='required email' data-validation-error-msg-required="<?php echo lang('required_error'); ?>" name="username" placeholder="<?php echo lang('email'); ?>"/>
                        </div>
                        <div class="form-group password">
                            <input type="password" class="login-input" id="set_password" data-validation-error-msg="<?php echo lang('new_password_error'); ?>" data-validation="strength", name="new_password" placeholder="<?php echo lang('new_password'); ?>"/>
                        </div>
                        <input type="hidden" value="<?php echo $code ?>" name="code" />
                        <input type="submit" class="btn btn-green-bvl btn-login width100" value="<?php echo lang('change_password'); ?>" />
                    
                    <?php echo form_close();?>
                </div>
            </div>
       </div>
    </section>
<!-- Manin Iner container end -->
  
<?php include('login_footer.php'); ?>