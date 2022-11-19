<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>Ofabee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="shortcut icon" type="image/png" href=""/>
    <meta name="title" content=" ">
    <meta name="description" content="">
    <meta property="og:image" content="">
    <meta property="og:image:width" content="400" />
    <meta property="og:image:height" content="300" />
    <link href="<?php echo assets_url(); ?>themes/onboarding/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo assets_url(); ?>themes/onboarding/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <section>
        <div class="container-fluid dashboard-section">
        <?php
        $user_name       = $this->session->userdata('admin')['us_name'];
        ?>
          <div class="dashboard-welcome-text">
              <div class="welcome-title">Welcome <span class="green-text"><?php echo $user_name; ?> !</span></div>
              <div class="welcome-info">You are just 2 steps  away from launching your own e-learning platform</div>
          </div>
          <div class="dashboard-navigator">
            <div class="navigator">
              <div class="navigator-icon"><img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/create-course.jpg"></div>
              <div class="navigator-title">Create a Course</div>
              <div class="navigator-info">Your courses can have any number of sections and lessons. You can upload videos, PDFs, presentations, blogs and quizzes within lessons.</div>
              <div class="text-center"><a class="btn green-btn" href="<?php echo admin_url(); ?>course?create=true">Create Now</a></div>
            </div>
            <div class="navigator">
              <div class="navigator-icon"><img src="<?php echo assets_url(); ?>themes/onboarding/assets/img/enroll-learner.jpg"></div>
              <div class="navigator-title">Enroll Learners</div>
              <div class="navigator-info">You can add learners from the admin panel itself. And have a track on the progress of the enrolled learners.</div>
              <div class="text-center"><a class="btn green-btn" href="<?php echo admin_url(); ?>user?add=true">Enroll Now</a></div>
            </div>
          </div>


        </div>   
    </section>

    <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/popper.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url(); ?>themes/onboarding/assets/js/custom.js"></script>
<script>
$(document).ready(function() {
    window.history.pushState(null, "", window.location.href);        
    window.onpopstate = function() {
        window.history.pushState(null, "", window.location.href);
    };
});
</script>
</body>
</html>