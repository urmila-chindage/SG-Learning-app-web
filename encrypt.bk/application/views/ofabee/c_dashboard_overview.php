<?php include_once 'header.php';?>
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,500,700" rel="stylesheet">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontawesome-stars.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/assesment/sdpk/css/style.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/assesment/sdpk/css/animate.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk/curriculum.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk/assignment.css">
<style>
    body{overflow-x: hidden !important;}  
    .lang-ellipsis{
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }  
    .curriculum.curriculum-reused{margin-bottom: 25px;}
    /* @media only screen and (max-device-width : 767px) and (orientation : portrait) {
        .dashbord-blue{display:none !important;}
    }

    @media only screen and (min-device-width : 320px) and (max-device-width : 768px) and (orientation : landscape) {
        .dashbord-blue{display:none !important;}
    } */
</style>
<section>
    <div class="dashbord-blue">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <h1 class="dashbord-head"><?php echo $course['cb_title']; ?></h1>
                <span class="instructor-wrap">
                    <span class="instructor-label">Instructed by</span>
                    <?php
                        $tutors_list                                          = array();
                        foreach ($course['tutors'] as $tutor): $tutors_list[] = '<a href="' . site_url('teachers/view/' . $tutor['id']) . '"><span class="blue-text">' . $tutor['us_name'] . '</span></a>';
                        endforeach;
                    ?>
                    <span class="tutor-name-label"><?php echo (count($tutors_list) > 0) ? implode(' <span class="instructor-coma">,</span> ', $tutors_list) : $admin; ?></span>
                </span>
                <!--instructor-wrap-->
                <div class="row">
                    <div class="col-md-3 col-sm-4">
                        <div class="white-man-img-wraper hover-play-btn course-dashboard-container">
                            <?php
                                $image_first_name = substr($course['cb_image'], 0, -4);
                                $image_dimension  = '_300x160.jpg';
                                $image_new_name   = $image_first_name . $image_dimension;
                            ?>
                            <a href="<?php echo site_url('materials/course/' . $course['id'] . '/' . $course['subscription']['cs_last_played_lecture']); ?>">
                                <img  src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $image_new_name; ?>" alt="image">
                                <!-- play btn -->
                                <div class="play-btn"></div>
                            </a>
                        </div><!--white-man-img-wraper-->
                    </div><!--columns-->
                    <div class="col-md-9 col-sm-8">
                        <span class="lectures-count">
                            <span class="semibold">
                                <?php //echo '<pre>';print_r($course); exit; 
                                // echo $course['completed_lectures']; 
                                echo ($course_completion > 95) ? $course['lecture_count'] : $course['completed_lectures']; ?>
                        </span> of <span class="semibold">
                            <?php echo $course['lecture_count']; //change from count()?></span> 
                            <span class="lecture-completed-title">lectures completed (<?php echo $course_completion > 95 ? 100 : round($course_completion); ?>%)</span>
                        </span>
                        <span class="progress-bar-and-badge">
                            <span class="progressbar-rail">
                                <span class="progressbar-green" style="width:<?php echo $course_completion > 95 ? 100 : intval($course_completion); ?>%;"></span><!--progressbar-green-->
                            </span><!--progressbar-rail-->
                        </span><!--progress-bar-and-badge-->
                        <?php
                            $now       = time(); // or your date as well
                            $your_date = strtotime($course['subscription']['cs_end_date'].' +1 day');
                            $datediff  = $your_date - $now;
                            $datediff  = ceil($datediff / (60 * 60 * 24));
                        ?>
                        <?php if ($course['cb_has_certificate'] == '1'): ?>
                            <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/progress-badge.svg" class="progress-bade-svg">
                        <?php endif;?>
                        <span class="below-progress-bar-details-wrap">
                            <?php if($datediff>0): ?>
                                <span class="progress-btn-wrap">
                                    <?php if ($course['subscription']['cs_course_validity_status'] == 0) {?>
                                        <a href="<?php echo site_url() . 'materials/course/' . $course['id'] ?><?php echo $course['subscription']['cs_percentage'] > 0 ? '/' . $course['subscription']['cs_last_played_lecture'].'/resume' : ''; ?>" class="orange-flat-btn progress-bar-btn-size">
                                            <?php if($course_completion == 100): ?>
                                                Completed
                                            <?php else: ?>
                                                <?php echo $course_completion > 0 ? 'Resume Learning' : 'Start Learning'; ?>
                                            <?php endif; ?>
                                        </a>
                                    <?php } else if ($course['subscription']['cs_course_validity_status'] == 0 && $datediff < 0) {?>
                                    <?php } else {?>
                                        <?php if ($course['subscription']['cs_course_validity_status'] == 2 && $datediff <= 0) {?>
                                        <?php } else {?>
                                            <a href="<?php echo ($datediff > 0) ? (site_url() . 'materials/course/' . $course['id']) : (($course['cb_is_free'] == 1) ? 'javascript:void(0)' : 'javascript:void(0)'); ?>" class="orange-flat-btn progress-bar-btn-size"><?php echo ($datediff > 0) ? ($course['subscription']['cs_percentage'] > 0 ? 'Resume Learning' : 'Start Learning') : 'Expired'; ?></a>
                                        <?php }?>
                                    <?php }?>
                                </span><!--progress-btn-wrap-->
                            <?php endif; ?>
                            <span class="progress-bar-course-details-wrap">
                                <span class="Progress-course-validity-label">Course validity</span>
                                
                                <span class="progress-days"><?php if ($course['subscription']['cs_course_validity_status'] == 0) {?>
                                        Lifetime Validity
                                        <?php
                                } else {
                                echo ($datediff > 0) ? $datediff . ' days left' : 'Expired';
                                }
?>
                                </span>
                            </span><!--progress-bar-course-details-wrap-->
                            <?php /**/ ?>
                            <span class="progress-bar-course-details-wrap">
                                <span class="Progress-course-validity-label label-margin-btm" id="rate_course_label"><?php echo $course['subscription']['my_rating'] == 0 ? 'Rate this course' : 'Your rating'; ?></span>
                                <select id="my_rating">
                                    <option value=""></option>
                                    <?php for ($i = 1; $i < 6; $i++) {?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $course['subscription']['my_rating'] ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                                    <?php }?>
                                </select>
                            </span><!--progress-bar-course-details-wrap-->

                            <!-- Share on large screen -->
                            <div class="share-via-soial d-inlineflex">
                                <ul class="d-flex">
                                    <li>
                                    <a target="_blank" href="<?php echo 'https://www.facebook.com/sharer/sharer.php?u=' . current_url(); ?>" class="fb-share-button facebook-active">
                                        <i class="icon-facebook"></i>
                                    </a>
                                    </li>
                                    <li>
                                    <a target="_blank" href="<?php echo 'https://twitter.com/intent/tweet?text=' . current_url(); ?>" class="twitter-share-button share-twitter">
                                        <i class="icon-twitter"></i>
                                    </a>
                                    </li>
                                    <li>
                                    <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . current_url(); ?>" class="google-ser" style="border-right:none">
                                        <i class="icon-google"></i>
                                    </a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Share on large screen ends -->

                            <?php /**/ ?>
                            </span>
                            <div class="course-share" id="mobile-share-trigger">
                                <span>Share</span>
                                <span style="display: inline-block;vertical-align: middle;">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" style="enable-background:new 0 0 512 512;fill: #fff;width: 25px;height: 25px;" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><g><path d="M352,377.9H102.4V198.2h57.5c0,0,14.1-19.7,42.7-38.2H83.2c-10.6,0-19.2,8.5-19.2,19.1v217.9c0,10.5,8.6,19.1,19.2,19.1   h288c10.6,0,19.2-8.5,19.2-19.1V288L352,319.4V377.9z M320,224v63.9l128-95.5L320,96v59.7C165.2,155.7,160,320,160,320   C203.8,248.5,236,224,320,224z"></path></g></svg>
                                </span>
                            </div>
                    </div><!--columns-->
                </div><!--row-->
            </div><!--changed-container-for-forum-->
        </div>
        <!--container container-res-chnger-frorm-page-->
    </div><!--dashbord-blue-->
    <div class="bread-crumb-wrap">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum tab-nav-center">
                <ul class="nav nav-tabs bread-parent slidable-tabs">
                    <li>
                        <a id="loadCurriculumBtn" href="javascript:void(0)" onclick="loadCurriculum();">Curriculum</a>
                    </li>
                    <!-- <li>
                        <a id="loadOverViewBtn" onclick="loadOverView();" href="javascript:void(0)">Overview</a>
                    </li> -->
                    <li>
                        <a id="loadQaBtn" href="javascript:void(0)" onclick="loadQa();">Q&amp;A </a>
                    </li>
                    <li>
                        <a id="loadReportsBtn" href="javascript:void(0)" onclick="loadReports();">Reports</a>
                    </li>
                    <!--<li>
                        <a id="loadQuizBtn" href="javascript:void(0)" onclick="loadQuiz();">Quiz</a>
                    </li>
                    <li>
                        <a id="loadAssignmentBtn" href="javascript:void(0)" onclick="loadAssignments();">Assignments</a>
                    </li>-->
                    <li>
                        <a id="loadAnouncementsBtn" href="javascript:void(0)" onclick="loadAnouncements();">Announcements</a>
                    </li>
                </ul><!--bread-parent-->

            </div><!--changed-container-for-forum-->
        </div><!--bread-crumb-wrap-->
    </div><!--container container-res-chnger-frorm-page-->
</section>

<section>
    <div class="tab-content course-tab-content">
    <!-- <div class="tab-content"> -->

        <div class="tab-pane" style="display:none;" id="curriculum">
            <div class="curriculum curriculum-reused">
                <div class="container container-res-chnger-frorm-page">
                    <div class="changed-container-for-forum" id="curriculum_div">
                        <h3 class="formpage-heading">Curriculum</h3>
                    </div><!--change-size-of-abt-course-->
                </div><!--container-->
            </div>
        </div> <!--curriculam-->

        <div class="tab-pane" style="display:none;" id="overview">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <div class="change-size-of-abt-course">
                        <h3 class="formpage-heading course_dashboard_title">About Course</h3>
                        <div class="icon-text-para">
                            <div style="margin-bottom: 30px; display: flex;" class="language-col">
                                <?php /*<div class="col-md-3 holding-icon-text">
                                    <svg version="1.1" class="svg-common" x="0px" y="0px" width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                                    <g>
                                    <path fill="#808080" d="M10.49,0.5c-5.52,0-9.99,4.48-9.99,10s4.47,10,9.99,10c5.53,0,10.01-4.48,10.01-10S16.02,0.5,10.49,0.5z
                                          M10.5,18.5c-4.42,0-8-3.58-8-8s3.58-8,8-8s8,3.58,8,8S14.92,18.5,10.5,18.5z"/>
                                    <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"/>
                                    <path fill="#808080" d="M11,5.5H9.5v6l5.25,3.15l0.75-1.23L11,10.75V5.5z"/>
                                    </g>
                                    </svg> &nbsp;
                                    Course Length : <span class="hourse-bold"><?php echo gmdate('H.i', $course['cb_video_duration']); ?> Hrs</span> 
                                </div>*/?>
                                <!--holding-icon-text-->
                            </div>                
                        </div>
                        <div class="show-more-data-wrap show-more-collapse redactor-content course_about_content">
                            <p id="c_description"><?php echo $course['cb_description']; ?></p>
                            <a href="javascript:void(0)" class="Showmore-btn">Read full details</a>
                        </div>
                        <?php if ($course['cb_video_count'] != 0 || $course['cb_docs_count'] != 0 || $course['cb_live_count'] != 0 || $course['cb_assessment_count'] != 0): ?>
                            <h3 class="formpage-heading tab-include-top course_dashboard_title">Includes</h3>
                        <?php endif;?>
                        <div class="course-vald">
                            <?php if ($course['cb_video_count'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <span class="course-icon video-icon"></span>
                                        <label class="fundamental-right-font-size">Videos</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block  duration-color"><?php echo $course['cb_video_count']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
                            <?php endif;?>

                            <?php if ($course['cb_docs_count'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <span class="course-icon doc-icon"></span>
                                        <label class="fundamental-right-font-size">Docs</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course['cb_docs_count']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
                            <?php endif;?>

                            <?php if ($course['cb_live_count'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <span class="course-icon live-icon"></span>
                                        <label class="fundamental-right-font-size">Live Classes</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course['cb_live_count']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
                            <?php endif;?>

                            <?php if ($course['cb_assessment_count'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <span class="course-icon assessment-icon"></span>
                                        <label class="fundamental-right-font-size">Quiz</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course['cb_assessment_count']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
                            <?php endif;?>

                            <?php if(count($course['cb_language']) != 0): ?>
                            <?php
                                $languages          = array();
                                if(count($course['cb_language']) == 1){
                                    $languages[] = $course['cb_language'][0]['cl_lang_name'];
                                }else{
                                    foreach ($course['cb_language'] as $language): 
                                        $languages[] = substr($language['cl_lang_name'], 0, 2);
                                    endforeach;
                                }
                            ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <span>
                                            <svg version="1.1" x="0px" y="0px" class="svg-common" width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                                                <g>
                                                <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path>
                                                <path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10c5.5,0,10-4.5,10-10S16,0.5,10.5,0.5z M17.4,6.5h-2.9
                                                    c-0.3-1.3-0.8-2.4-1.4-3.6C14.9,3.6,16.5,4.8,17.4,6.5z M10.5,2.5c0.8,1.2,1.5,2.5,1.9,4H8.6C9,5.1,9.7,3.7,10.5,2.5z M2.8,12.5
                                                    c-0.2-0.6-0.3-1.3-0.3-2s0.1-1.4,0.3-2h3.4C6.1,9.2,6,9.8,6,10.5s0.1,1.3,0.1,2H2.8z M3.6,14.5h3c0.3,1.3,0.8,2.5,1.4,3.6
                                                    C6.1,17.4,4.5,16.2,3.6,14.5z M6.5,6.5h-3c1-1.7,2.5-2.9,4.3-3.6C7.3,4.1,6.9,5.3,6.5,6.5z M10.5,18.5c-0.8-1.2-1.5-2.5-1.9-4h3.8
                                                    C12,15.9,11.3,17.3,10.5,18.5z M12.8,12.5H8.2c-0.1-0.7-0.2-1.3-0.2-2s0.1-1.4,0.2-2h4.7c0.1,0.6,0.2,1.3,0.2,2
                                                    S12.9,11.8,12.8,12.5z M13.1,18.1c0.6-1.1,1.1-2.3,1.4-3.6h2.9C16.5,16.1,14.9,17.4,13.1,18.1z M14.9,12.5c0.1-0.7,0.1-1.3,0.1-2
                                                    s-0.1-1.3-0.1-2h3.4c0.2,0.6,0.3,1.3,0.3,2s-0.1,1.4-0.3,2H14.9z"></path>
                                                </g>
                                            </svg> 
                                        </span>
                                            <label class="fundamental-right-font-size">Language</label>
                                        </div><!--columns-->

                                        <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                            <label class="text-left display-block duration-color lang-ellipsis" title="<?php echo implode(', ', $languages); ?>"><?php echo implode(', ', $languages); ?></label>
                                        </div><!--columns-->
                                </div>
                            <?php endif; ?>

                            <?php
$total_rating = 0;
$total_rate   = 0;
foreach ($course['rating'] as $cr) {
 $total_rating += $cr['ratings'];
 $total_rate += $cr['rating'];
}
$rating[1]['percentage'] = 0;
$rating[1]['count']      = 0;
$rating[2]['percentage'] = 0;
$rating[2]['count']      = 0;
$rating[3]['percentage'] = 0;
$rating[3]['count']      = 0;
$rating[4]['percentage'] = 0;
$rating[4]['count']      = 0;
$rating[5]['percentage'] = 0;
$rating[5]['count']      = 0;
foreach ($course['rating'] as $cr) {
 $rating[$cr['cc_rating']]['percentage'] = round(($cr['ratings'] * 100) / $total_rating, 2);
 $rating[$cr['cc_rating']]['count']      = $cr['ratings'];
}
$sum_rating = 0;
if (count($course['rating']) != 0) {
 $sum_rating = round(($total_rate / count($course['rating'])), 1);
}
?>
                            <?php /* ?>
                            <div class="reviews">
                                <h3 class="formpage-heading tab-include-top course_dashboard_title"><?php //echo '<pre>';print_r($course_rating);    ?>Reviews</h3>
                                <div class="bar-rating clearfix">
                                    <div class="star-rating-left star-rating-padding">
                                        <span class="big-rating-no"><?php echo $sum_rating; ?></span><!--big-rating-no-->
                                        <div class="star-ratings-sprite-two">
                                            <span style="width:<?php echo round(($sum_rating * 20), 2) ?>%" class="star-ratings-sprite-rating-two"></span>
                                        </div><!--star-ratings-sprite-->
                                        <span class="strip-font-grey"><?php echo $total_rating; ?> <?php echo $total_rating > 1 ? 'Ratings' : 'Rating'; ?></span>
                                    </div><!--star-rating-left-->
                                    <div class="bar-rating-right">
                                        <?php for ($i = 5; $i > 0; $i--): ?>
                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum">
                                                    <span class="strip-font-grey star-barrating-text"><?php echo $i; ?></span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve"><path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path></svg></span>
                                                </span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo $rating[$i]['percentage'] ?>%"></span>
                                                </span><!--star-progress-->
                                                <span class="strip-font-grey percent-align"><?php echo $rating[$i]['count']; ?></span>
                                            </span>
                                        <?php endfor;?>
                                    </div><!--bar-rating-right-->
                                </div><!--bar-rating-->
                            </div><!--reviews-->
                            <?php */ ?>
                            <?php /* Commented as per demand ?>
                                <div class="profile-name">
                                <ul class="profile-list" id="review_list">
                                </ul><!--profile-list-->
                                </div><!--profile-name-->
                            <?php */?>
                        </div>
                    </div><!--change-size-of-abt-course-->
                </div>
                <!-- changed-container-for-forum-->
            </div>
            <!-- container container-res-chnger-frorm-page-->
        </div><!--no-contents-->

        <div class="tab-pane floating_panel" style="display:none;" id="discussions">
            <div class="panel_head">
                <span class="back-arrow" onclick="loadCurriculum();">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" id="Layer_1" style="enable-background:new 0 0 32 32;vertical-align: super;" version="1.1" viewBox="0 0 32 32" width="20px" xml:space="preserve"><path d="M28,14H8.8l4.62-4.62C13.814,8.986,14,8.516,14,8c0-0.984-0.813-2-2-2c-0.531,0-0.994,0.193-1.38,0.58l-7.958,7.958  C2.334,14.866,2,15.271,2,16s0.279,1.08,0.646,1.447l7.974,7.973C11.006,25.807,11.469,26,12,26c1.188,0,2-1.016,2-2  c0-0.516-0.186-0.986-0.58-1.38L8.8,18H28c1.104,0,2-0.896,2-2S29.104,14,28,14z"></path></svg>
                </span>
                <span class="panel_head_title">Q&A</span>
            </div>
            <div class="container container-res-chnger-frorm-page">
                <iframe id="custom-scroller" width="100%" height="100%" frameborder="0" style="width: 100%;height: 600px;overflow-y: auto;" src="<?php echo base_url('/forum_service/'.$course['id'].'/0/main'); ?>"></iframe>
            </div>
        </div><!--discussions-->

        <div class="tab-pane floating_panel" style="display:none;" id="reports">
            <div class="panel_head">
                <span class="back-arrow" onclick="loadCurriculum();">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" id="Layer_1" style="enable-background:new 0 0 32 32;vertical-align: super;" version="1.1" viewBox="0 0 32 32" width="20px" xml:space="preserve"><path d="M28,14H8.8l4.62-4.62C13.814,8.986,14,8.516,14,8c0-0.984-0.813-2-2-2c-0.531,0-0.994,0.193-1.38,0.58l-7.958,7.958  C2.334,14.866,2,15.271,2,16s0.279,1.08,0.646,1.447l7.974,7.973C11.006,25.807,11.469,26,12,26c1.188,0,2-1.016,2-2  c0-0.516-0.186-0.986-0.58-1.38L8.8,18H28c1.104,0,2-0.896,2-2S29.104,14,28,14z"></path></svg>
                </span>
                <span class="panel_head_title">Reports</span>
            </div>
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum" id="report_div">
                    
                </div><!--changed-container-for-forum-->
            </div> <!-- container container-res-chnger-frorm-page-->
        </div><!--discussions-->

        <div class="tab-pane floating_panel" style="display:none;" id="anouncements">
            <div class="panel_head">
                <span class="back-arrow" onclick="loadCurriculum();">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" id="Layer_1" style="enable-background:new 0 0 32 32;vertical-align: super;" version="1.1" viewBox="0 0 32 32" width="20px" xml:space="preserve"><path d="M28,14H8.8l4.62-4.62C13.814,8.986,14,8.516,14,8c0-0.984-0.813-2-2-2c-0.531,0-0.994,0.193-1.38,0.58l-7.958,7.958  C2.334,14.866,2,15.271,2,16s0.279,1.08,0.646,1.447l7.974,7.973C11.006,25.807,11.469,26,12,26c1.188,0,2-1.016,2-2  c0-0.516-0.186-0.986-0.58-1.38L8.8,18H28c1.104,0,2-0.896,2-2S29.104,14,28,14z"></path></svg>
                </span>
                <span class="panel_head_title">Announcements</span>
            </div>
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum" id="announcement_div">
                   <!--no-discussion-wrap-->
                </div><!--changed-container-for-forum-->
            </div> <!-- container container-res-chnger-frorm-page-->
        </div><!--discussions-->

        <div class="tab-pane floating_panel" style="display:none;" id="quiz">
            <div class="panel_head">
                <span class="back-arrow" onclick="loadCurriculum();">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" id="Layer_1" style="enable-background:new 0 0 32 32;vertical-align: super;" version="1.1" viewBox="0 0 32 32" width="20px" xml:space="preserve"><path d="M28,14H8.8l4.62-4.62C13.814,8.986,14,8.516,14,8c0-0.984-0.813-2-2-2c-0.531,0-0.994,0.193-1.38,0.58l-7.958,7.958  C2.334,14.866,2,15.271,2,16s0.279,1.08,0.646,1.447l7.974,7.973C11.006,25.807,11.469,26,12,26c1.188,0,2-1.016,2-2  c0-0.516-0.186-0.986-0.58-1.38L8.8,18H28c1.104,0,2-0.896,2-2S29.104,14,28,14z"></path></svg>
                </span>
                <span class="panel_head_title">Quiz</span>
            </div>
            <div class="container container-res-chnger-frorm-page quiz-timeline-wrapper">
                <div class="changed-container-for-forum" id="quizArea">
                    
                </div><!--changed-container-for-forum-->
            </div> <!-- container container-res-chnger-frorm-page-->
        </div> <!--curriculam-->

        <div class="tab-pane floating_panel" style="display:none;" id="assignments">
            <div class="panel_head">
                <span class="back-arrow" onclick="loadCurriculum();">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="20px" id="Layer_1" style="enable-background:new 0 0 32 32;vertical-align: super;" version="1.1" viewBox="0 0 32 32" width="20px" xml:space="preserve"><path d="M28,14H8.8l4.62-4.62C13.814,8.986,14,8.516,14,8c0-0.984-0.813-2-2-2c-0.531,0-0.994,0.193-1.38,0.58l-7.958,7.958  C2.334,14.866,2,15.271,2,16s0.279,1.08,0.646,1.447l7.974,7.973C11.006,25.807,11.469,26,12,26c1.188,0,2-1.016,2-2  c0-0.516-0.186-0.986-0.58-1.38L8.8,18H28c1.104,0,2-0.896,2-2S29.104,14,28,14z"></path></svg>
                </span>
                <span class="panel_head_title">Assignments</span>
            </div>
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum" id="assignmentsArea">
                
                </div><!--changed-container-for-forum-->
            </div> <!-- container container-res-chnger-frorm-page-->
        </div> <!--curriculam-->

    </div><!--tab-contents-->
</section>
<script type="text/javascript">
    let __my_rating = '<?php echo $course['subscription']['my_rating'] ?>';
    let __reviews = atob('<?php echo base64_encode(json_encode($course['course_reviews'])); ?>');
    let __curriculum            = {};
        __curriculum.sections   = atob('<?php echo base64_encode(json_encode($course['sections'])); ?>');
        __curriculum.lectures   = atob('<?php echo base64_encode(json_encode($course['lectures'])); ?>');
        __curriculum.log        = atob('<?php echo base64_encode($course['subscription']['cs_lecture_log']); ?>');
        let __site_url = '<?php echo site_url(); ?>';
    let __theme_url = '<?php echo assets_url().'themes/'.config_item('theme'); ?>';
    let __assets_url = '<?php echo assets_url(); ?>';
    let __user_path = {};
    __user_path.default = '<?php echo default_user_path() ?>';
    __user_path.native = '<?php echo user_path() ?>';
    let __loaded = [];
    let __start = true;
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/course_dashboard/main.js"></script>
<script type="text/javascript" src="<?php echo assets_url() . 'themes/' . $this->config->item('theme') . '/js/jquery.barrating.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/assesment/sdpk/js/wow.min.js" ></script>

<?php include_once 'footer.php';?>

<div id="rate_course" class="modal fade" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header ofabee-modal-header border-bottom-replaced">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
                <h4 class="modal-title ofabee-modal-title">Rate this course</h4>
            </div>
            <div class="modal-body ofabee-modal-body textarea-top">
                <div class="starrating-inside">
                    <span class="rate-this-label ratelabel-block">Your rating</span>
                    <select id="example2">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <script>
                    $(document).ready(function() {
                        $( "#review_course" ).keyup(function( event ) {
                            var textlenth = $('#review_course').val().length;
                            $('#review_course_char_left').text(textlenth +' / '+ (300 - textlenth));
                        });
                    });
                </script>
                <textarea class="ofabee-textarea" id="review_course" maxlength="300" placeholder="Tell others what you think about this course and why did you leave this rating"></textarea>
                <small id="review_course_char_left">0 / 300</small>
            </div>
            <div class="modal-footer ofabee-modal-footer btn-center-responsive d-flex align-center" style="justify-content: center;">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
                <button id="submit_rating_course" type="button" class="btn ofabee-orange" >Submit</button>
            </div>
        </div>

    </div>
</div>

<div id="rate_course_preview" class="modal fade ofabee-modal" role="dialog">
    <div class="modal-dialog modal-md">
        <div class="modal-content ofabee-modal-content">
            <div class="modal-header ofabee-modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    &times;
                </button>
            </div>
            <div class="modal-body ofabee-modal-body textarea-top">
                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/Successful_icon.svg" class="blocked-image">
                <span class="your_review">Your review has been
                    <br />
                    submitted</span>
                <div class="starrating-inside text-center">
                    <span class="blocked_rating">Your rating</span>
                    <select id="example4">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <span class="preview_purpose" id="preview_review_course"></span>
            </div>
            <div class="modal-footer ofabee-modal-footer modal-footer-text-center">
                <button type="button" class="btn ofabee-dark" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>

    </div>
</div>

<script>
	$(".Showmore-btn").click(function(){
		$('.show-more-collapse').css('max-height','unset');
		$(".Showmore-btn").hide();
    });  
    
    async function AndroidNativeShare(Title,URL,Description){
        if(typeof navigator.share==='undefined' || !navigator.share){
            alert('Your browser does not support Android Native Share, it\'s tested on chrome 63+');
        } else if(window.location.protocol!='https:'){
            alert('Android Native Share support only on Https:// protocol');
        } else {
            if(typeof URL==='undefined'){
            URL = window.location.href;
            }
            if(typeof Title==='undefined'){
            Title = document.title;
            }
            if(typeof Description==='undefined'){
            Description = 'Share your thoughts about '+Title;
            } 
            const TitleConst = Title;
            const URLConst = URL;
            const DescriptionConst = Description;
            
            try{
            await navigator.share({title:TitleConst, text:DescriptionConst, url:URLConst});
            } catch (error) {
            //console.log('Error sharing: ' + error);
            return;
            }   
        }
    } 
    $(document).ready(function(){
        $("#mobile-share-trigger").click(function(BodyEvent){
            var meta_desc,meta_title,meta_url
            if(document.querySelector('meta[property="og:description"]')!=null) {
                meta_desc = document.querySelector('meta[property="og:description"]').content;
            }
            if(document.querySelector('meta[property="og:title"]')!=null) {
                meta_title = document.querySelector('meta[property="og:title"]').content;
            }
            if(document.querySelector('meta[property="og:meta_url"]')!=null) {
                meta_url = document.querySelector('meta[property="og:meta_url"]').content;
            }
            AndroidNativeShare(meta_title, meta_url,meta_desc); 
        });
    })
</script>