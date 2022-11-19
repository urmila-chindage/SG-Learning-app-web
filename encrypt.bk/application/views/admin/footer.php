 </div>
    <!-- Manin Inner container end -->
    
<?php 
switch ($this->router->fetch_class()) {
    case 'course':
        include_once 'course_modals.php';
        break;
    case 'user':
        include_once 'user_modals.php';
        break;
    case 'page':
        include_once 'page_modals.php';
        break;
    case 'termsofday':
        include_once 'terms_modals.php';
        break;
    case 'notification':
        include_once 'notification_modals.php';
        break;
    case 'catalog_settings':
        include_once 'catalog_settings_modals.php';
        break;
    case 'challenge_zone':
        include_once 'challenge_modals.php';
        break;
    case 'expert_lectures':
        include_once 'expert_lectures_modals.php';
        break;
    case 'generate_test':
        include_once 'generate_test_modals.php';
        break;
    case 'test_manager':
        include_once 'test_manager_modals.php';
        break;
    case 'faculties':
        include_once 'faculty_modals.php';
        break;
    case 'plans':
        include_once 'plan_modals.php';
        break;
    case 'institutes':
        include_once 'institute_modals.php';
        break;
    case 'groups':
        include_once 'group_modals.php';
        break;
    case 'wishlist':
        include_once 'group_modals.php';
        break;
    case 'daily_news':
        include_once 'daily_news_bulletin_modals.php';
        break;
    case 'categories':
        include_once 'category_modals.php';
        break;
    case 'role':
        include_once 'role_modals.php';
        break;
    case 'promo_code':
        include_once 'promocode_modals.php';
        break;
    case 'tasks':
        include_once 'task_modals.php';
        break;
    default:
        include_once 'course_modals.php';
        break;
}
?> 
<?php include_once "common_modals.php" ?>
</body>
<!-- body end-->

</html>

<!-- Basic All Javascript -->
    <!-- bootstrap library -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            initToolTip();
        });
        function initToolTip()
        {
            $('[data-toggle="tooltip"]').tooltip({
                trigger : 'hover'
            });
        }
    </script>
    
    <script src="<?php echo base_url('assets')?>/js/jquery.form-validator.min.js" type="text/javascript"></script>
    <!-- custom layput js handling tooltip and hide show switch -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>

<!-- Page Level Javascript -->
    <script type="text/javascript" src="<?php echo assets_url() ?>js/app.js"></script>
    <?php $popup    = $this->session->flashdata('popup');  ?>
<?php if(isset($popup['message'])&& $popup['message'] != ''): ?>
<script type="text/javascript">
    var popup_message       = atob('<?php echo base64_encode(json_encode($popup)) ?>');
        popup_message   = $.parseJSON(popup_message);
    if(popup_message != null){
        var messageObject = {
            'body':popup_message.message,
            'button_yes':'OK',
            'prevent_button_no': true
        };
        if(popup_message.success == true){
            callback_success_modal(messageObject);
        } else {
            callback_danger_modal(messageObject);
        }
    }
</script>
<?php endif; ?>

<?php $error    = $this->session->flashdata('error');  ?>
<?php if($error): ?>
<script type="text/javascript">
    var messageObject = {
        'body':'<?php echo $error ?>',
        'button_yes':'OK',
        'prevent_button_no': true
    };
    callback_danger_modal(messageObject);
</script>
<?php endif; ?>

<?php $message    = $this->session->flashdata('message');  ?>
<?php if($message): ?>
<script type="text/javascript">
    var messageObject = {
        'body':'<?php echo $message ?>',
        'button_yes':'OK',
        'prevent_button_no': true
    };
    callback_success_modal(messageObject);
</script>
<?php endif; ?>

    <script>
       
        $(document).ready(function() {
            checkMessageCount();
            App.init();
            <?php 
            switch ($this->router->fetch_class()) {
                case 'user':
                    ?>
                    /*JQUERY TOKENIZE*/
                   App.initTag();

                   /* For modal box START */
                   App.initWindowsHeight(".TokensContainer",200);

                   App.initWindowsHeightHeightAuto(".redactor-editor",270+40);
                   /* For modal box END */
                    <?php 
                    break;
                default:
                    break;
            }
            ?>
        });
</script>


