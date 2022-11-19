<?php include('login_header.php'); ?>
<title><?php echo lang('set_new_password_title') ?></title>

<script type="text/javascript">
function setpasswordIsValid(){
  
  var set_email     = document.getElementById("set_email").value;
  set_email         = set_email.trim();
  var atpos         = set_email.indexOf("@");
  var dotpos        = set_email.lastIndexOf(".");
  var validate_mail = "<?php echo lang('email_validate') ?>";
  if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= set_email.length) {
      alert(validate_mail);
      return false;
  }

  var set_password      = document.getElementById("set_password").value;
  var validate_password = "<?php echo lang('new_password_validate') ?>";
  set_password          = set_password.trim();
  if (set_password == null || set_password == "") {
      alert(validate_password);
      return false;
  }

  return true;
}
</script>
<!-- Manin Iner container start -->
    <section class="Login-fullwdth">
        <div class="talbe-cell">
            <div class="login-wrap">
                <!-- Login box top section -->
                <div class="login-typography login-padding clearfix">
                    <h3><b><?php echo lang('reset_password'); ?></b></h3>
                    <?php include_once('messages.php') ?>
                    <?php  $attributes = array('onsubmit' => 'return setpasswordIsValid()','class' => 'set-new-password', 'id' => 'set_new_password');
                    echo form_open(base_url($this->config->item('admin_folder').'/login/password_set'),$attributes); ?>
                    <div class="form-group email">
                        <input type="text" class="login-input" id="set_email" name="username" placeholder="<?php echo lang('email'); ?>"/>
                    </div>
                    <div class="form-group password">
                        <input type="password" class="login-input" id="set_password" name="new_password" placeholder="<?php echo lang('new_password'); ?>"/>
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