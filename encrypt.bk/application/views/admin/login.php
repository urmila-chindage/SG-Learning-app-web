<?php include('login_header.php'); ?>
<title><?php echo lang('login_page_title'); ?></title>

<?php 
$fb_access   = $this->settings->setting('has_facebook');
if(($fb_access['as_superadmin_value'] && $fb_access['as_siteadmin_value']) == 1){
    $grant_facebook_access = 1;
  } else{
    $grant_facebook_access = 0;
  }
   ?>

<!-- Manin Iner container start -->
    <section class="Login-fullwdth">
        <div class="talbe-cell">

            <div class="login-wrap">
                <!-- Login box top section -->
                <div class="login-typography login-padding clearfix">
                    <h3><b><?php echo lang('login') ?></b></h3>
                    <p><?php echo lang('login_here') ?></p>
                    <?php include_once('messages.php') ?>
                    <div class="alert alert-error alert-danger" id="alert_danger">
                      <a class="close" data-dismiss="alert">×</a>
                    </div>
                    <?php  $attributes = array('onsubmit' => 'return loginIsValid()','class' => 'login-backend', 'id' => 'login_backend');
                    echo form_open(base_url($this->config->item('admin_folder').'/login'),$attributes); ?>
                    <div class="form-group email">
                        <?php echo form_input(array('name'=>'username', 'id'=>'login_email', 'class'=>'login-input', 'placeholder' => lang('email'))); ?>
                    </div>
                    <div class="form-group password">
                        <?php echo form_password(array('name'=>'password', 'id'=>'login_password', 'class'=>'login-input', 'placeholder' => lang('password'))); ?>
                    </div>
                    <label class="check-box"><?php echo form_checkbox(array('name'=>'remember_me', 'value'=>'true')); ?> <?php echo lang('remember_me');?></label>
                    <input class="btn btn-green-bvl btn-login" type="submit" value="<?php echo lang('login');?>"/>
                    <a class="pull-right" href="<?php echo base_url($this->config->item('admin_folder').'/login/forgot') ?>"><?php echo lang('forget_password')?></a>
                    <input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
					<input type="hidden" value="submitted" name="submitted"/>
					<?php echo  form_close(); ?>
                </div>
                <!-- login bottom section  -->
                 <div class="login-box-bottom clearfix">
                    <span class="login-round-or">OR</span>
                    <?php if($grant_facebook_access == 1){ ?>
                    <a href="javascript:void(0)" onclick="login_facebook()" class="facebook"><i class="icon icon-facebook"></i><?php echo lang('facebook_login');?></a>
                    <?php } ?>
                    <p><?php echo lang('no_account'); ?>  <a href=""><?php echo lang('register'); ?></a></p>
                </div>
            </div>
       </div>
    </section>
    <!-- Manin Iner container end -->


<script type="text/javascript">
function loginIsValid(){
  var login_email   = document.getElementById("login_email").value;
  login_email       = login_email.trim();
  var atpos         = login_email.indexOf("@");
  var dotpos        = login_email.lastIndexOf(".");
  var validate_mail = "<?php echo lang('email_validate') ?>";
  var alert_HTML    = document.getElementById("alert_danger");
  if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= login_email.length) {
    //alert(validate_mail);
    alert_HTML.innerHTML = validate_mail;
    return false;
  }

  var login_password    = document.getElementById("login_password").value;
  var validate_password = "<?php echo lang('password_validate') ?>";
  login_password     = login_password.trim();
  if (login_password == null || login_password == "") {
    //alert(validate_password);
    alert_HTML.innerHTML = validate_password;
    return false;
  }

  return true;
}
</script>
<?php if($grant_facebook_access == 1){ ?>
  <script type="text/javascript">
    var appId     = "<?php echo $fb_access['as_setting_value']['setting_value']->app_id; ?>";
    var admin_url = "<?php echo admin_url('login/facebook') ?>";
  </script>
  <script type="text/javascript" src="<?php echo base_url('assets')?>/js/facebook_login.js"></script>
<?php } ?>

<?php include('login_footer.php'); ?>


<?php 
/*echo form_open(base_url($this->config->item('admin_folder').'/login'));
echo form_input(array('name'=>'username', 'class'=>'form-control', 'placeholder' => 'Username'));
echo form_password(array('name'=>'password', 'class'=>'form-control', 'placeholder' => 'Password'));
echo form_checkbox(array('name'=>'remember_me', 'value'=>'true'));
?>
<input class="btn btn-primary" type="submit" value="Submit"/>
<input type="hidden" value="<?php echo $redirect; ?>" name="redirect"/>
<input type="hidden" value="submitted" name="submitted"/>
<?php echo  form_close();*/ ?>