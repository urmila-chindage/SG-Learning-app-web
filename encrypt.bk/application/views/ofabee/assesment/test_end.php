<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="description" content="Free Web tutorials">
        <meta name="keywords" content="HTML,CSS,XML,JavaScript">
        <meta name="author" content="John Doe">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/css/custom.css">
    </head>
<body>
    <div class="congtrz-wrap">
        <img class="congratz-logo" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/Congratz.png" alt="Congratz_logo">
        <h2 class="congratz-title">Congratulations.! <?php echo $user_name ?></h2>
        <p class="congratz-text"><?php echo $end_message ?> </p>
        <?php if($evaluation_completed): ?>
        <div class="congratz-score-wrap">
            <img class="congratz-icon" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/congratz-icon.png" alt="congratz-icon">
            <div class="score-wrap">
                <span class="score">Score - </span>
                <span class="score-point"><?php echo $score ?></span>
            </div>
            <!-- score-wrap -->
        </div>
        <div class="btn-center-blue">
        <?php
         $user_token                 = isset($_REQUEST['token'])?$_REQUEST['token']:'';
        ?>
            <input class="btn btn-blue" type="button" onclick="window.location='<?php echo site_url('dashboard/result_preview/'.$attempt_id.'/'.$user_token) ?>'" value="View Report">
        </div>
        <?php else: ?>
        <p class="congratz-text" style="color: #a40000;">Note : This quiz contains questions that requires manual evaluation. You can see the report once the manual evaluation is completed.</p>
        <div class="btn-center-blue">
            <a class="btn  btn-grey grey-color goto-dashbord-margin" href="#" onclick="close_window();">Ok</a>
        </div>
        <?php endif; ?>
        <!-- congratz-score-wrap -->
       
    </div>
    <!-- congtrz-wrap -->
</body>
<script>
function close_window() 
{
    parent.postMessage("quiz_close", "*");
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if(iOS == true) {
        window.location.href = 'inapp://closewindow';
    }
    window.close();
}
</script>
</html>