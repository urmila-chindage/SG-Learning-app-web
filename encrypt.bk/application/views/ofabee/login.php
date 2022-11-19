<?php include_once "header.php"; ?>

<style media="screen">
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
                    <div class="login-card">
                    	<h2>Sign In</h2>
                        
                        <?php include_once('messages.php') ?>
                        <form method="post" id="login_backend" name="signin" action="<?php echo site_url('login') ?>">
                        <div class="form-group ">
                            <span class="error-msg" id="email_message_holder"></span>
                            <input type="text" id="login_email" autocomplete="off" name="username" class="form-control form-alter" placeholder="Email Id or Phone number" onFocus="this.placeholder=''" onBlur="this.placeholder='Email Id or Phone number'">
                        </div><!--form-group-->
                        <div class="form-group ">
                                <span class="error-msg" id="password_message_holder"></span>
                                <input type="password" name="password" autocomplete="off" id="login_password" class="form-control form-alter" placeholder="Password" onFocus="this.placeholder=''" onBlur="this.placeholder='Password'">
                        </div><!--form-group-->
                        <div class="form-group ">
                                <label>
                                <input type="checkbox" name="remember" id="remember" class="form-control form-alter" style="display:inline; width:15px; height:17px; vertical-align: sub;" >
                                Remember Me</label>
                        </div><!--form-group-->
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <a href="javascript:void(0)"  onclick="loginUser()" class="btn signin-height btn-orange btn-orange-full-width">Sign In</a>
                            </div>  
                            <div class="col-md-6 col-sm-6">
                            	<a href="<?php echo site_url('/login/forgot') ?>" class="forgot-pswd">Forgot Password?</a>
                            </div><!--columns-->  
                        </div>    
                        <input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
                        <input type="hidden" value="submitted" name="submitted"/>
                        </form>

                        <div class="mini-login-register-now">Don’t Have an Account?     <a href="<?php echo site_url('register'); ?>">Register Now</a>
                    </div>
                    </div><!--login-card-->
                </div><!--columns-->
                <div class="col-md-6 col-sm-6 col-xs-12 reg-login-info-wraper">
                  <div class="login-card">
                    <span class="register-wrap">
                    	<img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/sad_face.svg" alt="image" class="img-responsive wink-align">
                        <span class="donthaveAcc">Don’t Have an <br> Account?</span>
                        <div class="text-center"><a href="<?php echo site_url('register'); ?>" class="btn signin-height btn-orange inline-blk">Register Now</a></div>
                    </span>   <!--register-wrap--> 
                  </div>  <!--login-card-->    	
                </div><!--columns-->
            </div><!--row-->        	
        </div><!--container-->
    </div><!--signin-full-bg-->
</section>
<script>
    $(document).on('keyup', '#login_email, #login_password', function(e){
        if(e.which == 13)
        {
            loginUser();        
        }
    });
    function loginUser()
    {
        var user_email      = $.trim($('#login_email').val());
        var user_password   = $.trim($('#login_password').val());
        var errorCount      = 0;
        $('#email_message_holder, #password_message_holder').html('').css('visibility', 'visible');
        if(user_email == '')
        {
            $('#email_message_holder').html('Enter your Email id or Phone number');
            errorCount++;
        }
        /*else
        {
            if(validateEmail(user_email) == false)
            {
                $('#email_message_holder').html('Email id is invalid');
                errorCount++;
            }
            
        }*/
        if(user_password == '')
        {            
            $('#password_message_holder').html('Password cannot be empty');
            errorCount++;
        }
        if(errorCount==0)
        {
            $('#login_backend').submit();
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
<?php include_once "footer.php"; ?>
