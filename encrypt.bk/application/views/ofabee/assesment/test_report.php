<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo isset($title) ? $title : config_item('site_name'); ?></title>
        <meta name="description" content="Free Web tutorials">
        <meta name="keywords" content="HTML,CSS,XML,JavaScript">
        <meta name="author" content="John Doe">
        <link rel="icon" href="<?php echo base_url('favicon.png'); ?>" type="image/x-icon"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/css/custom.css">
    </head>
<body>
    <div class="congtrz-wrap congtrz-wrap-alter test-wrap-bg">
        <img class="congratz-logo result-trophy-small" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/test completed.png" alt="Congratz_logo">
        <h4 class="test-title-head"><?php echo $test_name ?></h4>
        <span class="test-title-date"><?php echo $date ?></span>
        <?php if(isset($result_message) && $result_message): ?>
        <h3 class="test-status <?php echo ($passed)?'status-sucess':'status-failed' ?>"><?php echo $result_message ?></h3>
        <?php endif; ?>
        <ul class="score-box-ul">
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/congratz-icon.png" alt="">
                <span class="score-text-label">Score : <span class="score-box-value"><?php echo $manual_evaluation_needed ? "<span style='color:red'>Pending</span>" : $mark ?></span></span>
            </li>
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/green-tick.png" alt="">
                <span class="score-text-label">Correct : <span class="score-box-value score-green"><?php echo $write_answer ?></span></span>
            </li>
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/red-tick.png" alt="">
                <span class="score-text-label">Incorrect :  <span class="score-box-value score-red"><?php echo $wrong_answer ?></span></span>
            </li>
        </ul>
        <ul class="score-box-ul score-box-ul2">
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/magnify-tick.png" alt="">
                <div>
                    <span class="score-text-label">Accurate</span>
                    <span class="score-box-accurate-val"><?php echo $accuracy ?>%</span>
                </div>

            </li>
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/hrs-logo.png" alt="">
                <div>
                    <span class="score-text-label">QS/hour</span>
                    <span class="score-text-label-qs"><?php echo $speed ?></span>
                </div>
            </li>
            <li>
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/time-taken.png" alt="">
                <div>
                    <span class="score-text-label">Time Taken</span>
                    <span class="score-text-time-taken"><?php echo $time ?></span>
                </div>
            </li>
        </ul>
        <?php /* ?>
        <p class="plz-share">Share into your social network:</p>
        <div class="social-wrap">
            <a href="javascript:void(0)">
                <span class="fb sprite-s"></span>
            </a>
            <a href="javascript:void(0)">
                <span class="tw sprite-s"></span>
            </a>
            <a href="javascript:void(0)">
                <span class="gplus sprite-s"></span>
            </a>
        </div>
        <?php */ ?>
        <div class="btn-center-blue">
            <a class="btn  btn-grey grey-color goto-dashbord-margin" href="javascript:void(0)" onclick="close_window();">Close</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <!-- <a class="btn  btn-grey grey-color goto-dashbord-margin" href="<?php //echo site_url('dashboard'); ?>">Go to Dashboard</a> -->
            <!-- <input class="btn btn-blue" type="submit" onclick="location.href='<?php //echo site_url('material/assesment_report_item/'.$attempt_id); ?>'" value="View Detailed Report"> -->
            <?php 
            if(!$manual_evaluation_needed)
            {
                $user_token                 = isset($student_token)?$student_token:'';
                if($user_token == '') 
                {
            ?>
                <a class="btn btn-blue" href="javascript:void(0)" onclick="view_report()">View Detailed Report</a>
                <?php } else { ?>
                <a class="btn btn-blue" href="javascript:void(0)" onclick="mobile_view_report()">View Detailed Report</a>
            <?php    
            } }  
            ?> 
        </div>
    </div>
    <!-- congtrz-wrap -->
</body>
<script>
var __redirect_url = "<?php echo (!$manual_evaluation_needed)?site_url('material/assesment_report_item/'.$attempt_id.'?token='.$user_token.'&quick_report=true'):''; ?>";

function view_report() 
{
   //console.log(__redirect_url);
    close_window();
    if(window.opener) {
        window.opener.redirectToReports(__redirect_url);
    }
    WebAppInterface.openWindow();
    //AndroidAppInterface.openWindow();
   
}
function mobile_view_report()
{
    parent.postMessage("quiz_viewreport", "*");
    window.location = __redirect_url;
}
function close_window() 
{
    //console.log('close window');
    parent.postMessage("quiz_close", "*");
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if(iOS == true) {
        window.location.href = 'inapp://closewindow';
    }
    window.close();
}
</script>
</html>