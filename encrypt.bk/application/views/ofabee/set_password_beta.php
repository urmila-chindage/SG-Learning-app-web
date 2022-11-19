<?php include('header.php'); ?>

<section> 
    <div class="signin-full-bg">
        <div class="container container-reduce-signin">
            <div class="row row-top-margin">
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="login-card forgot-card">
                        <h2><?php echo lang('set_new_password_title') ?></h2>
                        <br />
                        <?php include_once('messages.php') ?>
                    <?php  $attributes = array('class' => 'set-new-password', 'id' => 'set_new_password');
                    echo form_open(site_url('/login/password_set'),$attributes); ?>
                            <span class="forgot-paswd-des">Please enter your new password to login into your account</span>
                            <div class="form-group form-group-alter">
                                <span class="error-msg" id="email_password_holder"></span>
                                <input type="password" id="set_password" name="new_password" autocomplete="off" class="form-control form-alter" placeholder="<?php echo lang('new_password'); ?>" onFocus="this.placeholder=''" onBlur="this.placeholder='<?php echo lang('new_password'); ?>'">
                            </div><!--form-group--> 
                            <div class="form-group form-group-alter">
                                <span class="error-msg" id="email_confirm_password_holder"></span>
                                <input type="password" id="confirm_set_password" name="confirm_new_password" autocomplete="off" class="form-control form-alter" placeholder="Confirm password" onFocus="this.placeholder=''" onBlur="this.placeholder='Confirm password.'">
                            </div><!--form-group--> 
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <input type="hidden" value="<?php echo $token ?>" name="token" autocomplete="off" />
                                    <a href="javascript:void(0)" onclick="resetPasswordSubmit()" class="btn signin-height btn-orange btn-orange-full-width"><?php echo lang('change_password'); ?></a>
                                </div>
                            </div>  
                        </form>
                    </div><!--login-card-->
                </div><!--columns-->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="login-card">
                        <span class="register-wrap">
                            <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/sad_face.svg" alt="image" class="img-responsive wink-align">
                            <span class="donthaveAcc">Don’t Have an <br> Account?</span>
                            <div class="text-center"><a href="<?php echo site_url('register'); ?>" class="btn signin-height btn-orange inline-blk">Sign Up</a></div>
                        </span>   <!--register-wrap--> 
                    </div>  <!--login-card-->    	
                </div><!--columns-->
            </div><!--row-->        	
        </div><!--container-->
    </div><!--signin-full-bg-->
</section>
<script>
    function resetPasswordSubmit()
    {
        var user_password           = $.trim($('#set_password').val());
        var user_password_confirm   = $.trim($('#confirm_set_password').val());
        $('#email_password_holder, #email_confirm_password_holder').html('').css('visibility', 'visible');
        if(user_password === '')
        {
            $('#email_password_holder').html('Password cannot be empty');
            return;
        }else{
            if(user_password_confirm === ''){
                $('#email_confirm_password_holder').html('Confirm password cannot be empty');
                return;
            }else{
                if(user_password === user_password_confirm){
                    if(user_password.length <6){
                        $('#email_password_holder').html('Password is too short. (Min 6 charaters)');
                        $('#email_confirm_password_holder').html('');
                        return;
                    }

                    if(!user_password.match('[a-zA-Z]') || !user_password.match('[0-9]')){
                        $('#email_password_holder').html('Password must contain alphanumeric characters');
                        $('#email_confirm_password_holder').html('');
                        return;
                    }

                    if(user_password.length > 15){
                        $('#email_password_holder').html('Password length exceeds limit. (Max 15 characters)');
                        $('#email_confirm_password_holder').html('');
                        return;
                    }
                    $('#set_new_password').submit();
                }else{
                    $('#email_password_holder').html('Password doesnot match.');
                    $('#email_confirm_password_holder').html('');
                    return;
                }
            }
        }
    }
</script>
<?php include('footer.php'); ?>