<html>
<!-- head start-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo isset($title)?$title:config_item('site_name'); ?></title>
    <link rel="icon" href="<?php echo base_url('favicon.png') ?>">
    <link rel="icon" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/images/favicon.ico">
    <!-- Customized bootstrap css library -->
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="<?php echo assets_url() ?>css/custom.css">
</head>
<!-- head end-->

<!-- body start-->
<body>
    <!-- Top head start-->
        <?php include_once 'head.php'; ?>
    <!-- Top head end-->

    <!-- Side Menu start-->
        <?php include_once "sidebar_content_editor.php"; ?>
    <!-- Side Menu end-->


    <!-- Manin Iner container start -->
    <div class='dashbrd-container pos-top50 main-content'>
        <?php 
        
     $now           = time(); // or your date as well
     $expiry_time   = strtotime(config_item('acct_expiry_date'));
     $datediff      = $now - $expiry_time;
     $expiry_days   = abs(floor($datediff/(60*60*24)));
         

?>
        <?php if($expiry_days <= 20 ): ?>
            <div class="dash-expry">
                <?php 
                    switch ($expiry_days)
                    {
                         case ($expiry_days <= 0):
                            echo sprintf(lang('trial_expired'), $expiry_days).' <a href="#!.">Upgrade</a>';                             
                             break;
                         case ($expiry_days > 0):
                            echo sprintf(lang('trial_expires'), $expiry_days).' <a href="#!.">Upgrade</a>';                             
                             break;
                    }
                ?>
                
            </div>
        <?php endif; ?>
        <?php $admin = $this->session->userdata('content_editor');?>
        <h3 class="dash-wecl-ttle"><?php echo lang('welcome_back') ?>, <span><?php echo $admin['us_name'] ?>!</span><br/><?php //echo lang('greetings') ?></h3>
        <ul class="dash-ico-items">
            <li><a href="<?php echo admin_url('course') ?>" class="dash-mc" ><i></i><span>Courses</span></a></li>
            <li><a href="<?php echo admin_url('page') ?>" class="dash-cms" ><i></i><span>CMS</span></a></li>
            <li><a href="<?php echo admin_url('termsofday') ?>" class="dash-terms-of-the-day" ><i></i><span>Daily Terms</span></a></li>
        </ul>
        
        <div class="dash-chart-wrap">
        </div>
    </div>
    
    <!-- Manin Iner container end -->
    <div class="modal fade active-popup" id="active-lecture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-small" role="document">
                <div class="modal-content">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="icon icon-cancel-1"></span></button>
                    <div class="modal-body">
                        <div class="form-group">
                            <b id="header_text"></b>
                            <p class="m0">Are you sure?.</p>
                            <p id="popup_message"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-green" data-dismiss="modal">CANCEL</button>
                        <button type="button" class="btn btn-red" id="change_status_section" >CONTINUE</button>
                    </div>
                </div>
            </div>
        </div>
</body>
<!-- body end-->
<?php 
function event_day($live_event_date)
{
    $current_date    = date('Y-m-d');
    $total_days      =  round(abs(strtotime($current_date)-strtotime($live_event_date))/86400);
    switch ($total_days) {
        case 0:
            $day = lang('today');
        break;
        case 1:
            $day = lang('tommorrow');
        break;
        default:
            $day = $live_event_date;
        break;
    }
    return $day;
}
?>
</html>
<!-- Jquery library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.min.js"></script>

<!-- bootstrap library -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/bootstrap.min.js"></script>
<!-- custom layput js handling tooltip and hide show switch -->
<script type="text/javascript" src="<?php echo assets_url() ?>js/layout.min.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/language.js"></script>
<script>
var admin_url    = '<?php echo admin_url(); ?>';
var site_url     = '<?php echo site_url(); ?>';
var myWindow;
var __controller = '<?php echo $this->router->fetch_class() ?>';
</script>