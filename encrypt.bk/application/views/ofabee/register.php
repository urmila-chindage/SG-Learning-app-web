<?php include_once "header.php"; ?>


<section>
    <?php
    
    $captcha_access   = $this->settings->setting('has_google_recaptcha');
    $has_captcha      = false;
    if(($captcha_access['as_superadmin_value'] && $captcha_access['as_siteadmin_value']) == 1){ 
        $has_captcha      = true;
        foreach( $captcha_access['as_setting_value']['setting_value'] as $key=>$value )
        {
            $$key = $value;
        }
    }

    ?>
    <div class="signin-full-bg">
        <div class="container container-reduce-signin">
            <div class="row" style="display: flex;">
                <div class="col-md-6 col-sm-6 login-wraper">
                    <div class="login-card reg-card" style="height: 100%">
                        <h2>Register</h2>
                        <?php include_once('messages.php') ?>
                        <form method="post" id="registration_form" name="signin" action="<?php echo site_url('register') ?>">
                            <div class="">
                                <span class="error-msg" id="error_message_register_type"></span>
                                <input type="hidden" name="register_type" autocomplete="off" id="c1" value="2">
                            </div><!----> 
                            <div class="form-group ">
                                <span class="error-msg" id="error_message_first_name"></span>
                                <input type="text" value="<?php echo (isset($firstname)?$firstname:'') ?>" class="form-control form-alter" id="firstname" name="firstname" autocomplete="off" placeholder="Name" onFocus="this.placeholder = ''" onBlur="this.placeholder = 'Name'">
                            </div><!--form-group-->
                           
                            <div class="form-group ">
                                <span class="error-msg" id="error_message_email"></span>
                                <input type="email" class="form-control form-alter" value="<?php echo (isset($username)?$username:'') ?>" id="username" name="username" autocomplete="off" placeholder="Email" onFocus="this.placeholder = ''" onBlur="this.placeholder = 'Email'">
                            </div><!--form-group-->
                            <div class="form-group ">
                                <span class="error-msg" id="error_message_password"></span>
                                <input type="password" class="form-control form-alter" id="password" name="password" autocomplete="off" placeholder="Password" onFocus="this.placeholder = ''" onBlur="this.placeholder = 'Password'">
                            </div><!--form-group-->
                            <?php /* ?><div class="form-group ">
                                <span class="error-msg" id="error_message_password_confirm"></span>
                                <input type="password" class="form-control form-alter" id="password_confirmation" name="password_confirmation" autocomplete="off" placeholder="Confirm Password" onFocus="this.placeholder = ''" onBlur="this.placeholder = 'Confirm Password'">
                            </div><!--form-group--><?php */ ?>
                            <div class="form-group ">
                                <span class="error-msg" id="error_message_phone"></span>
                                <input type="text" class="form-control form-alter" value="<?php echo (isset($number)?$number:'') ?>" id="number" name="number" autocomplete="off" maxlength="10" minlength="10" placeholder="Phone Number" onFocus="this.placeholder = ''" onBlur="this.placeholder = 'Phone Number'">
                            </div><!--form-group-->  
                            <?php /* ?>
                            <div class="form-group ">
                                <span class="error-msg" id="error_message_branch"></span>
                                <select class="form-control form-alter" id="branch" name="branch">
                                    <option value="">Choose Branch</option>
                                    <?php if(!empty($branches)): ?>
                                    <?php foreach($branches as $branch_obj): ?>
                                        <option <?php echo ((isset($branch)&&$branch==$branch_obj['id'])?'selected="selected"':'') ?> value="<?php echo $branch_obj['id'] ?>"><?php echo $branch_obj['branch_code'].'-'.$branch_obj['branch_name'] ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <?php */ ?>
                            <input type="hidden" id="branch" name="branch" value="1" />



                            <!--form-group-->  
                            <div class="form-group " >
                                <span class="error-msg" id="error_message_institute"></span>
                                <select class="form-control form-alter" id="institute" name="institute">
                                    <!-- <option value="">Choose Institute</option> -->
                                    <?php if(!empty($institutes)): ?>
                                    <?php foreach($institutes as $institute_obj): ?>
                                        <option <?php echo ((isset($institute)&&$institute==$institute_obj['id'])?'selected="selected"':'') ?> value="<?php echo $institute_obj['id'] ?>"><?php echo $institute_obj['ib_institute_code'].'-'.$institute_obj['ib_name'] ?></option>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div><!--form-group-->  
                            <div class="form-group ">
                                <?php if($has_captcha): ?>
                                <div class="form-group">
                                    <a class="captcha g-recaptcha" data-sitekey="<?php echo isset($site_key)?$site_key:''; ?>"></a>
                                </div>
                                <?php endif; ?>  
                                <div class="form-group">                            	
                                    <a href="javascript:void(0)" onclick="registerUser()" class="btn signin-height btn-orange btn-orange-full-width">Register</a>
                                </div><!--columns-->  
                            </div>    
                            <input type="hidden" value="submitted" name="submitted"/>
                        </form>

                        <div class="mini-login-register-now">Already Have an Account?     <a href="<?php echo site_url('login') ?>" >Sign In Now</a></div>
                    </div><!--login-card-->
                </div><!--columns-->
                <div class="col-md-6 col-sm-6 col-xs-12 reg-login-info-wraper">
                    <div class="login-card newacnt-card" style="height: 100%">
                        <span class="register-wrap">
                            <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/Wink_face.svg" alt="image" class="img-responsive wink-align">
                            <span class="donthaveAcc">Already Have an <br> Account?</span>
                            <div class="text-center"><a href="<?php echo site_url('login') ?>" class="btn signin-height btn-orange  signin-full">Sign In</a></div>
                        </span>   <!--register-wrap--> 
                    </div>  <!--login-card-->    	
                </div><!--columns-->
            </div><!--row-->        	
        </div><!--container-->
    </div><!--signin-full-bg-->
</section>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
    
    function IsmobileNumber(Numbers){
        var IndNum = /^([0|\+[0-9]{1,5})?([6-9][0-9]{9})$/;
        if(IndNum.test(Numbers)){
            return true;
        }else{
            return false;
        }
    }

    $(document).on('keydown',"#number",function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    function registerUser()
    {
        var firstname           = $.trim($('#firstname').val());
        var username            = $.trim($('#username').val());
        var password            = $.trim($('#password').val());
        var password_confirm    = $.trim($('#password_confirmation').val());
        var phone_number        = $.trim($('#number').val());
        var institute           = $.trim($('#institute').val());
        var branch              = $.trim($('#branch').val());
        //var register_type   = $("input[name='register_type']:checked").val();
        var register_type   = $("input[name='register_type']").val();
        
        var errorCount      = 0;
        $('#error_message_first_name, #error_message_email, #error_message_password, #error_message_phone, #error_message_institute, #error_message_branch, #error_message_register_type').html('').css('visibility', 'visible');
        if(firstname == '')
        {
            $('#error_message_first_name').html('First Name cannot be empty');
            errorCount++;
        }
        if(username == '')
        {
            $('#error_message_email').html('Email id cannot be empty');
            errorCount++;
        }
        else
        {
            if(validateEmail(username) == false)
            {
                $('#error_message_email').html('Email id is invalid');
                errorCount++;
            }
            
        }

        if(phone_number == '')
        {            
            $('#error_message_phone').html('Phone Number cannot be empty');
            errorCount++;
        }
        else
        {
            if(phone_number.length!=10 || !IsmobileNumber(phone_number))
            {
                $('#error_message_phone').html('Phone Number is invalid');
                errorCount++;                
            }
        }
        
        if(password=='' || password.length <6)
        {
            $('#error_message_password').html('Password length cannot be less than 6');
            errorCount++; return;
        }

        if(!password.match('[a-zA-Z]') || !password.match('[0-9]')){
            $('#error_message_password').html('Password must contain alphanumeric characters');
            errorCount++;
        }

        /*if(password_confirm=='' || password_confirm.length <6)
        {
            $('#error_message_password_confirm').html('Confirm Password length cannot be less than 6');
            errorCount++;            
        }
        
        if( password != '' && password_confirm != '' )
        {
            if( password != password_confirm )
            {
                $('#error_message_password').html('Password and Confirm Password mismatch');
                errorCount++;                        
            }
        }*/

        if(institute == '')
        {            
            $('#error_message_institute').html('You must choose institute');
            errorCount++;
        }
        if(branch == '')
        {            
            $('#error_message_branch').html('You must choose branch');
            errorCount++;
        }

        if(register_type == '' || typeof register_type == 'undefined')
        {
            $('#error_message_register_type').html('Choose user type');
            errorCount++;
        }
        if(errorCount==0)
        {
            $('#registration_form').submit();
        }
        $(window).trigger('resize');
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

/*$(window).on('resize',function() {
	if( $(window).width() > 640){
		var registerHeight = $('.reg-card').height();
		$('.newacnt-card').height(registerHeight);
		$('.signin-height').click(function(){
			//$('.newacnt-card').height();
		});
	}
});*/


</script>
<?php include_once "footer.php"; ?>