<?php include('login_header.php') ?>
<title><?php echo lang('forget_page_title'); ?></title>

<script type="text/javascript">
function forgotIsValid(){
  var forgot_email  = document.getElementById("forgot_email").value;
  forgot_email      = forgot_email.trim();
  var atpos         = forgot_email.indexOf("@");
  var dotpos        = forgot_email.lastIndexOf(".");
  var validate_mail = "<?php echo lang('email_validate') ?>";
  if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= forgot_email.length) {
      alert(validate_mail);
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
                    <h3><b><?php echo lang('forget_password') ?></b></h3>
                    <p><strong><?php echo lang('happens') ?></strong></p>
                    <p><?php echo lang('email_sent_reset') ?></p>
                    <?php include_once('messages.php') ?>
                    <?php $attributes = array('onsubmit' => 'return forgotIsValid()','class' => 'forgot-password', 'id' => 'forgot_password');
                    echo form_open($this->config->item('admin_folder').'/login/forgot',$attributes) ?>
                    <div class="form-group email">
                        <input type="text" name="username" id="forgot_email" class="login-input" placeholder="<?php echo lang('email'); ?>"/>
                    </div>
                    <input type="submit" class="btn btn-green-bvl btn-login width100" value="<?php echo lang('reset'); ?>" />
                    <?php echo  form_close(); ?>
                </div>

            </div>
       </div>
    </section>
<!-- Manin Iner container end -->
<?php include('login_footer.php') ?>