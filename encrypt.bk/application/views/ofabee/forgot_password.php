<?php include('header.php'); ?>

<style type="text/css" media="screen">
@media (max-width:780px){
    .signin-full-bg{height: calc(100vh - 51px);}
    .container-reduce-signin{
        position: relative;
        top: 50%;
        transform: translate(0%, -50%);
    }
}    
</style>

<section>
    <div class="signin-full-bg">
        <div class="container container-reduce-signin">
            <div class="row">
                <div class="col-md-6 col-sm-6 login-wraper">
                    <div class="login-card forgot-card">
                        <h2>Forgot Password?</h2>
                        <br />
                        <?php include_once('messages.php') ?>
                        <form method="post" id="forgot_password" name="forgot_psd" action="<?php echo site_url('login/forgot') ?>">
                            <span class="forgot-paswd-des">Please enter your email address and we will send you an email about how to reset your password.</span>
                            <div class="form-group form-group-alter">
                                <span class="error-msg" id="email_message_holder"></span>
                                <input type="text" id="forgot_email" name="username" autocomplete="off" class="form-control form-alter" placeholder="Email" onFocus="this.placeholder=''" onBlur="this.placeholder='Email'">
                            </div><!--form-group-->                       
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <a href="javascript:void(0)" onclick="forgotPasswordSubmit()" class="btn signin-height btn-orange btn-orange-full-width">Continue</a>
                                </div>
                            </div>  
                        </form>
                    </div><!--login-card-->
                </div><!--columns-->
                <div class="col-md-6 col-sm-6 col-xs-12 reg-login-info-wraper">
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
    function forgotPasswordSubmit()
    {
        var user_email      = $.trim($('#forgot_email').val());
        var errorCount      = 0;
        $('#email_message_holder').html('').css('visibility', 'visible');
        if(user_email == '')
        {
            $('#email_message_holder').html('Email id cannot be empty');
            errorCount++;
        }
        else
        {
            if(validateEmail(user_email) == false)
            {
                $('#email_message_holder').html('Email id is invalid');
                errorCount++;
            }
            
        }
        if(errorCount==0)
        {
            $('#forgot_password').submit();
        }
    }
    function validateEmail(email)
    {
        var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
        if (filter.test(email)) {
            return true;
        }
        else {
            return false;
        }
    }
</script>
<?php include('footer.php'); ?>