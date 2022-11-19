<?php include('header.php'); ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontawesome-stars.css">
<section>
    <div class="dashbord-blue">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <h1 class="dashbord-head"><?php echo $course_details['cb_title']; ?></h1>
                <span class="instructor-wrap">
                    <span class="instructor-label">Instructed by</span>
                    <?php $tutors_list = array();
                    foreach ($course_details['course_tutors'] as $tutor): $tutors_list[] = '<a target="__blank" href="' . site_url('teachers/view/' . $tutor['id']) . '"><span class="blue-text">' . $tutor['us_name'] . '</span></a>';
                    endforeach; ?>
                    <span class="tutor-name-label"><?php echo (count($tutors_list) > 0) ? implode(' <span class="instructor-coma">,</span> ', $tutors_list) : $admin_name; ?></span>
                </span>
                <!--instructor-wrap-->
                <div class="row">
                    <div class="col-md-3 col-sm-4">
                        <div class="white-man-img-wraper hover-play-btn">
                            <?php
                            $image_first_name = substr($course_details['cb_image'], 0, -4);
                            $image_dimension  = '_300x160.jpg';
                            $image_new_name   = $image_first_name . $image_dimension;
                            ?>
                            <img  src="<?php echo (($course_details['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course_details['id']))) . $image_new_name; ?>" alt="image">
                        </div><!--white-man-img-wraper-->
                    </div><!--columns-->
                    <div class="col-md-9 col-sm-8">
                        <span class="lectures-count"><span class="semibold"><?php echo $course_details['completed_lectures']['count']; ?></span> of <span class="semibold"><?php echo $course_details['total_lectures']; ?></span> lectures completed (<?php echo $course_details['percentage'] > 95 ? 100 : intval($course_details['percentage']); ?>%)</span>
                        <span class="progress-bar-and-badge">
                            <span class="progressbar-rail">
                                <span class="progressbar-green" style="width:<?php echo $course_details['percentage'] > 95 ? 100 : intval($course_details['percentage']); ?>%;"></span><!--progressbar-green-->
                            </span><!--progressbar-rail-->
                        </span><!--progress-bar-and-badge-->
                        <?php if ($course_details['cb_has_certificate'] == '1'): ?>
                            <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/progress-badge.svg" class="progress-bade-svg">
                                <?php endif; ?>
                        <span class="below-progress-bar-details-wrap">
                            <span class="progress-btn-wrap">
                                <?php
                                $now       = time(); // or your date as well
                                $your_date = strtotime($course_details['cs_end_date']);
                                $datediff  = $your_date - $now;
                                ?>
                                <?php if ($course_details['cs_course_validity_status'] == 0) { ?>
                                    <a href="<?php echo site_url() . 'materials/course/' . $course_details['id'] ?>" class="orange-flat-btn progress-bar-btn-size">Start Learning</a>
                                <?php } else if ($course_details['cs_course_validity_status'] == 0 && ceil($datediff / (60 * 60 * 24)) < 0) { ?>
                                <?php } else { ?>
                                    <?php if ($course_details['cs_course_validity_status'] == 2 && (ceil($datediff / (60 * 60 * 24)) <= 0)) { ?>
                                    <?php } else { ?>
                                        <a href="<?php echo (ceil($datediff / (60 * 60 * 24)) > 0) ? (site_url() . 'materials/course/' . $course_details['id']) : (($course_details['cb_is_free'] == 1) ? site_url() . 'checkout/standard/' . $course_details['id'] . '/' . base64_encode('course') : site_url() . 'checkout/feepal/' . $course_details['id'] . '/' . base64_encode('course')); ?>" class="orange-flat-btn progress-bar-btn-size"><?php echo (ceil($datediff / (60 * 60 * 24)) > 0) ? 'Start Learning' : 'Renew Now'; ?></a>
    <?php } ?>
<?php } ?>
                            </span><!--progress-btn-wrap-->
                            <span class="progress-bar-course-details-wrap">
                                <span class="Progress-course-validity-label">Course validity</span>
                                <?php
                                $now       = time(); // or your date as well
                                $your_date = strtotime($course_details['cs_end_date']);
                                $datediff  = $your_date - $now;
                                ?>
                                <span class="progress-days"><?php if ($course_details['cs_course_validity_status'] == 0) { ?>
                                        Lifetime Validity
                                    <?php
                                    } else {
                                        echo (ceil($datediff / (60 * 60 * 24)) > 0) ? ceil($datediff / (60 * 60 * 24)) . ' days left' : 'Expired';
                                    }
                                    ?>
                                </span>
                            </span><!--progress-bar-course-details-wrap-->
                            <?php /* ?>

                            <span class="progress-bar-course-details-wrap">
                                <span class="Progress-course-validity-label label-margin-btm" id="rate_course_label"><?php echo ($check_user_rating == 0) ? 'Rate this course' : 'Your rating'; ?></span>
<!--                                <div class="star-ratings-sprite-two margin-right custom-star stars-align"><span style="width:<?php echo $course_rating; ?>%" class="star-ratings-sprite-rating-two"></span></div>-->
                                <select id="example_course_dashboard">
                                    <option value=""></option>
                                    <?php for ($i = 1; $i < 6; $i++) { ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $course_rating ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </span><!--progress-bar-course-details-wrap-->
                            <?php */ ?>
                        </span><!--below-progress-bar-details-wrap-->
                    </div><!--columns-->
                </div><!--row-->
            </div><!--changed-container-for-forum-->
        </div> <!--container container-res-chnger-frorm-page-->   
    </div><!--dashbord-blue-->
    <div class="bread-crumb-wrap">
        <div class="container container-res-chnger-frorm-page">
            <div class="changed-container-for-forum">
                <ul class="nav nav-tabs bread-parent">
                    <li><a class="active-bread-parent" data-toggle="tab" href="#Overview">Overview</a></li>
                        <?php if (!empty($course_details['sections'])) { ?>
                                                <li><a data-toggle="tab" href="#curriculum">Curriculum</a></li>
                        <?php } ?>
                    <li><a  data-toggle="tab" href="#no-contents">Discussion </a></li>
                    <li id="my_reports"><a data-toggle="tab" href="#report">Reports</a></li> 
                </ul><!--bread-parent-->

            </div><!--changed-container-for-forum-->       
        </div><!--bread-crumb-wrap-->
    </div><!--container container-res-chnger-frorm-page-->
</section>

<section>  
    <div class="no-contents  tab-content">
        <div class="tab-pane  in active" id="Overview">
            <div class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <div class="change-size-of-abt-course">
                        <h3 class="formpage-heading">About Course</h3>
                        <div class="icon-text-para">
<?php if ($course_details['videos_length'] != 0): ?>
                                <span class="holding-icon-text">
                                    <svg version="1.1" class="svg-common" x="0px" y="0px" width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                                    <g>
                                    <path fill="#808080" d="M10.49,0.5c-5.52,0-9.99,4.48-9.99,10s4.47,10,9.99,10c5.53,0,10.01-4.48,10.01-10S16.02,0.5,10.49,0.5z
                                          M10.5,18.5c-4.42,0-8-3.58-8-8s3.58-8,8-8s8,3.58,8,8S14.92,18.5,10.5,18.5z"/>
                                    <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"/>
                                    <path fill="#808080" d="M11,5.5H9.5v6l5.25,3.15l0.75-1.23L11,10.75V5.5z"/>
                                    </g>
                                    </svg>
                                    &nbsp;
                                    Course Length  <span class="hourse-bold"><?php echo gmdate('H.i', $course_details['videos_length']); ?> Hrs</span>
                                </span><!--holding-icon-text-->
<?php endif; ?>
                            <span class="<?php if ($course_details['videos_length'] != 0): ?>glob-left-margin<?php endif; ?>">
                                <svg version="1.1"  x="0px" y="0px" class="svg-common"
                                     width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                                <g>
                                <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"/>
                                <path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10c5.5,0,10-4.5,10-10S16,0.5,10.5,0.5z M17.4,6.5h-2.9
                                      c-0.3-1.3-0.8-2.4-1.4-3.6C14.9,3.6,16.5,4.8,17.4,6.5z M10.5,2.5c0.8,1.2,1.5,2.5,1.9,4H8.6C9,5.1,9.7,3.7,10.5,2.5z M2.8,12.5
                                      c-0.2-0.6-0.3-1.3-0.3-2s0.1-1.4,0.3-2h3.4C6.1,9.2,6,9.8,6,10.5s0.1,1.3,0.1,2H2.8z M3.6,14.5h3c0.3,1.3,0.8,2.5,1.4,3.6
                                      C6.1,17.4,4.5,16.2,3.6,14.5z M6.5,6.5h-3c1-1.7,2.5-2.9,4.3-3.6C7.3,4.1,6.9,5.3,6.5,6.5z M10.5,18.5c-0.8-1.2-1.5-2.5-1.9-4h3.8
                                      C12,15.9,11.3,17.3,10.5,18.5z M12.8,12.5H8.2c-0.1-0.7-0.2-1.3-0.2-2s0.1-1.4,0.2-2h4.7c0.1,0.6,0.2,1.3,0.2,2
                                      S12.9,11.8,12.8,12.5z M13.1,18.1c0.6-1.1,1.1-2.3,1.4-3.6h2.9C16.5,16.1,14.9,17.4,13.1,18.1z M14.9,12.5c0.1-0.7,0.1-1.3,0.1-2
                                      s-0.1-1.3-0.1-2h3.4c0.2,0.6,0.3,1.3,0.3,2s-0.1,1.4-0.3,2H14.9z"/>
                                </g>
                                </svg>
                            </span>
                            &nbsp;
<?php $languages = array();
foreach ($course_details['languages'] as $language): $languages[] = $language['cl_lang_name'];
endforeach; ?>
                            <span>
                                Language   <span class="hourse-bold"><?php echo (count($languages) > 0) ? implode(',', $languages) : 'General'; ?></span>
                            </span>                
                        </div>
                        <!-- <div class="show-more-data-wrap show-more-collapse"> edited on 28-08-2018-->
                        <div class="">
                            <p id="c_description"><?php echo $course_details['cb_description']; ?></p>
                        <?php /* if (strlen($course_details['cb_description']) > 400): ?>
                                <a href="javascript:void(0)" class="Showmore-btm">Read full details</a>
                            <?php endif; */?>   
                        </div>
<?php if ($course_details['videos'] > 0 || $course_details['documents'] > 0 || $course_details['live_lectures'] > 0 || $course_details['assessments'] > 0) { ?>
                            <h3 class="formpage-heading tab-include-top">Includes</h3>
<?php } ?>

                        <div class="course-vald">               	
<?php if ($course_details['videos'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                                        <g>
                                        <g>
                                        <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"/>
                                        <path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"/>
                                        </g>
                                        </g>
                                        </svg>
                                        <label class="fundamental-right-font-size">Videos</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block  duration-color"><?php echo $course_details['videos']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
<?php endif; ?>

<?php if ($course_details['documents'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve">
                                        <g>
                                        <g>
                                        <path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        </g>
                                        <g>
                                        <path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"/>
                                        <path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8
                                              V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"/>
                                        </g>
                                        </svg>
                                        <label class="fundamental-right-font-size">Docs</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course_details['documents']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
<?php endif; ?>

<?php if ($course_details['live_lectures'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <svg version="1.1" class="svg-common" .333="" x="0px" y="0px" width="19px" height="21px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve">
                                        <g>
                                        <g>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        </g>
                                        <g>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        <g>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        <path fill="#808080" d="M20.5,4.5h-7.6l3.3-3.3l-0.7-0.7l-4,4l-4-4L6.8,1.2l3.3,3.3H2.5c-1.1,0-2,0.9-2,2v12c0,1.1,0.9,2,2,2h18
                                              c1.1,0,2-0.9,2-2v-12C22.5,5.4,21.6,4.5,20.5,4.5z M20.5,18.5h-18v-12h18V18.5z M8.5,8.5v8l7-4L8.5,8.5z"/>
                                        </g>
                                        </svg>
                                        <label class="fundamental-right-font-size">Live Classes</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course_details['live_lectures']; ?></label>
                                    </div><!--columns-->
                                </div><!--row-->
<?php endif; ?>

<?php if ($course_details['assessments'] != 0): ?>
                                <div class="row course-val-bottom-margin">
                                    <div class="col-md-3 col-sm-3 col-xs-5 supermin-left">
                                        <svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve">
                                        <g>
                                        <g>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        </g>
                                        <g>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        <g>
                                        <path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0z
                                              M8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"/>
                                        <path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"/>
                                        </g>
                                        </svg>
                                        <label class="fundamental-right-font-size">Assessments</label>
                                    </div><!--columns-->

                                    <div class="col-md-3 col-sm-3 col-xs-6 supermin-right">
                                        <label class="text-left display-block duration-color"><?php echo $course_details['assessments']; ?></label>
                                    </div><!--columns-->
                                </div><!--row--> 
<?php endif; ?>

<?php if ($course_details['rating']['average_rating'] > 0) { ?>
                                <div class="reviews">
                                    <h3 class="formpage-heading tab-include-top">Ratings</h3>
                                    <div class="bar-rating bar-rating-padd clearfix">
                                        <div class="star-rating-left star-rating-align">
                                            <span class="big-rating-no"><?php echo $course_details['rating']['average_rating']; ?></span><!--big-rating-no-->
                                            <div class="star-ratings-sprite-two">
                                                <span style="width:<?php echo $course_details['rating']['average_rating'] * 20; ?>%" class="star-ratings-sprite-rating-two"></span>
                                            </div><!--star-ratings-sprite-->
                                            <span class="strip-font-grey"><?php echo $course_details['rating']['total_ratings']; ?> <?php echo ($course_details['rating']['total_ratings'] > 1) ? 'Ratings' : 'Rating' ?></span>
                                        </div><!--star-rating-left-->
                                        <div class="bar-rating-right">
                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum"><span class="strip-font-grey star-barrating-text">5</span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve">
                                                        <path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path>
                                                        </svg></span></span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : round(($course_details['rating']['five'] / $course_details['rating']['total_ratings']) * 100); ?>%"></span>
                                                </span><!--star-progress-->

                                                <span class="strip-font-grey percent-align"><?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : $course_details['rating']['five']; //round(($course_details['rating']['five']/$course_details['rating']['total_ratings'])*100);  ?></span>
                                            </span><!--bar-star-number-warap-->


                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum"><span class="strip-font-grey star-barrating-text">4</span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve">
                                                        <path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path>
                                                        </svg></span></span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : round(($course_details['rating']['four'] / $course_details['rating']['total_ratings']) * 100); ?>%"></span>
                                                </span><!--star-progress-->

                                                <span class="strip-font-grey percent-align"><?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : $course_details['rating']['four']; //round(($course_details['rating']['four']/$course_details['rating']['total_ratings'])*100);  ?></span>
                                            </span><!--bar-star-number-warap-->

                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum"><span class="strip-font-grey star-barrating-text">3</span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve">
                                                        <path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path>
                                                        </svg></span></span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : round(($course_details['rating']['three'] / $course_details['rating']['total_ratings']) * 100); ?>%"></span>
                                                </span><!--star-progress-->

                                                <span class="strip-font-grey percent-align"><?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : $course_details['rating']['three']; //round(($course_details['rating']['three']/$course_details['rating']['total_ratings'])*100);  ?></span>
                                            </span><!--bar-star-number-warap-->

                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum"><span class="strip-font-grey star-barrating-text">2</span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve">
                                                        <path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path>
                                                        </svg></span></span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : round(($course_details['rating']['two'] / $course_details['rating']['total_ratings']) * 100); ?>%"></span>
                                                </span><!--star-progress-->



                                                <span class="strip-font-grey percent-align"><?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : $course_details['rating']['two']; //round(($course_details['rating']['two']/$course_details['rating']['total_ratings'])*100);  ?></span>
                                            </span><!--bar-star-number-warap-->

                                            <span class="bar-star-number-warap">
                                                <span class="starAndNum"><span class="strip-font-grey star-barrating-text">1</span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve">
                                                        <path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path>
                                                        </svg></span></span><!--starAndNum-->
                                                <span class="star-progress">
                                                    <span class="orange-progress" style="width:<?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : round(($course_details['rating']['one'] / $course_details['rating']['total_ratings']) * 100); ?>%"></span>
                                                </span><!--star-progress-->

                                                <span class="strip-font-grey percent-align"><?php echo ($course_details['rating']['total_ratings'] == 0) ? 0 : $course_details['rating']['one']; //round(($course_details['rating']['one']/$course_details['rating']['total_ratings'])*100);  ?></span>
                                            </span><!--bar-star-number-warap-->
                                        </div><!--bar-rating-right-->	
                                    </div><!--bar-rating-->
                                </div><!--reviews-->
<?php } ?>

                        </div>
                    </div><!--change-size-of-abt-course-->            
                </div> <!-- changed-container-for-forum-->               
            </div> <!-- container container-res-chnger-frorm-page-->   
        </div><!--no-contents-->


        <div class="tab-pane" id="no-contents">

<?php if (empty($course_details['discussions'])) { ?>
                <div class="container container-res-chnger-frorm-page">
                    <div class="changed-container-for-forum">
                        <div class="no-discussion-wrap">
                            <img class="no-questions-svg" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/no-questons.svg">
                            <span class="no-discussion"><span>Oops! </span>No discussions to show</span>
                            <div class="text-center">
                                <span class="noquestion-btn-wrap"><a href="<?php echo (ceil($datediff / (60 * 60 * 24)) > 0) ? (site_url() . 'materials/course/' . $course_details['id'] . '/browse_discussion') : (($course_details['cb_is_free'] == 1) ? site_url() . 'checkout/standard/' . $course_details['id'] . '/' . base64_encode('course') : site_url() . 'checkout/feepal/' . $course_details['id'] . '/' . base64_encode('course')); ?>" class="orange-flat-btn noquestion-btn">Ask a question</a></span>
                            </div><!--text-center-->
                        </div><!--no-discussion-wrap-->
                    </div><!--changed-container-for-forum-->
                </div> <!-- container container-res-chnger-frorm-page-->
<?php } else { ?>

                <div class="container container-res-chnger-frorm-page">
                    <div class="changed-container-for-forum">
                        <span class="question-ans-wrap">
                            <span class="question-wrap-serach"> <span class="olp-banner-search">
                                    <input class="olp-inputBox question-input" id="quest_search" placeholder="Search a question" onfocus="this.placeholder = ''" onkeyup="searchQuestion(this)" onblur="this.placeholder = 'Search a question'" type="text">
                                </span><!--olp-banner-search--> 
                                <span class="olp-search-btn"> <a href="javascript:void(0);" class="olp-link-btn question-search-alterd-btn"><svg version="1.1" x="0px" y="0px" viewBox="0 0 37.9 37.9" enable-background="new 0 0 37.9 37.9" xml:space="preserve">
                                        <g>
                                        <path fill="#FFFFFF" d="M24.3,26.9v-1.7l-1.1-1.1l-0.4,0.3c-2.5,2.1-5.6,3.3-8.8,3.3c-7.5,0-13.6-6.1-13.6-13.6S6.6,0.5,14.1,0.5
                                              s13.6,6.1,13.6,13.6c0,3.2-1.2,6.4-3.3,8.8l-0.3,0.4l1.1,1.1h1.7l10.3,10.3l-2.5,2.5L24.3,26.9z M14.1,3.8
                                              C8.4,3.8,3.8,8.4,3.8,14.1s4.6,10.2,10.3,10.2c5.7,0,10.2-4.6,10.2-10.2S19.7,3.8,14.1,3.8z"/>
                                        <path fill="#FFFFFF" d="M14.1,1c7.2,0,13.1,5.9,13.1,13.1c0,3.1-1.1,6.1-3.2,8.5l-0.6,0.7l0.7,0.7l0.6,0.6l0.3,0.3h0.4h1.3l9.8,9.8
                                              l-1.8,1.8l-9.8-9.8v-1.3V25l-0.3-0.3L24,24.1l-0.7-0.7L22.6,24c-2.4,2-5.4,3.2-8.5,3.2C6.9,27.2,1,21.3,1,14.1C1,6.9,6.9,1,14.1,1
                                              M14.1,24.8c5.9,0,10.8-4.8,10.8-10.8S20,3.3,14.1,3.3S3.3,8.2,3.3,14.1S8.2,24.8,14.1,24.8 M14.1,0C6.3,0,0,6.3,0,14.1
                                              c0,7.8,6.3,14.1,14.1,14.1c3.5,0,6.7-1.3,9.2-3.4l0.6,0.6v1.7l10.8,10.8l3.2-3.2L27.1,23.8h-1.7l-0.6-0.6c2.1-2.5,3.4-5.7,3.4-9.2
                                              C28.2,6.3,21.9,0,14.1,0L14.1,0z M14.1,23.8c-5.4,0-9.8-4.4-9.8-9.8s4.4-9.8,9.8-9.8c5.4,0,9.8,4.4,9.8,9.8S19.5,23.8,14.1,23.8
                                              L14.1,23.8z"/>
                                        </g>
                                        </svg></a> </span><!--olp-search-btn--> 
                            </span>

                            <span class="showing-questions-wraper question-showing">
                                Showing <b>&nbsp;<b id="discussions_show_count"></b>&nbsp;</b> of <b id="discussions_total_count"><?php echo $course_details['discussion_count'] > 0 ? $course_details['discussion_count'] : 0; ?></b> questions
                            </span>
                            <span class="ckkbox-ans">
                                <input id="show-ans" name="cc" type="checkbox">
                                <label class="label-narrow label-ans" for="show-ans"><span></span>Show my questions</label>
                            </span><!--ckkbox-ans-->
                        </span><!--question-ans-wrap-->

                        <ul class="question-post-ul">

                        </ul>
                    </div><!--changed-container-for-forum-->
                </div><!-- container container-res-chnger-frorm-page-->        
                    <?php } ?>   
        </div><!--no-contents-->

        <div class="tab-pane fade" id="curriculum">
            <div class="curriculum curriculum-reused">
                <div class="container container-res-chnger-frorm-page">
<?php if (!empty($course_details['sections'])) { ?>
                        <div class="changed-container-for-forum" id="curriculum_div">
                            <h3 class="formpage-heading">Curriculum zz</h3>
    <?php $l_count    = 0;
    $lect_count = 0;
    $quiz_count = 0;
    foreach ($course_details['sections'] as $key => $section):
        ?>
                                            <?php if (!empty($section['lectures'])): ?>
                                    <ul class="solution-list solution-list-for-curriculam">
                                        <li class="solution-child-head"><p class="solution-para"><span class="solution-section">Section <?php echo $key + 1; ?>:</span><span class="solution-intro"><?php echo $section['s_name']; ?></span></p></li>
                                                <?php foreach ($section['lectures'] as $key1 => $lecture): $l_count++; ?>
                                            <li onclick="location.href = '<?php echo site_url() . '/materials/course/' . $course_details['id'] . '#' . $lecture['id'] ?>'" style="cursor:pointer;" class="soulution-childs<?php echo $key1 + 1 == count($section['lectures']) ? ' no-bottom-border' : '' ?>">
                                                <span class="solution-child-l-r-margin solution-child-table-cell"><?php switch ($lecture['cl_lecture_type']) {
                                        case 1:
                                                            ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>
                                                            <?php break;
                                                        case 11:
                                                            ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>
                                                                                                                                                                                                                                                                                                                                                             <?php break;
                                                                                                                                                                                                                                                                                                                                                         case 12:
                                                                                                                                                                                                                                                                                                                                                             ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>
                                                                                                                                                                                                                                                                                                                                                             <?php break;
                                                                                                                                                                                                                                                                                                                                                         case 4:
                                                                                                                                                                                                                                                                                                                                                             ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="15px" height="15px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve"><g><g><path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10s10-4.5,10-10S16,0.5,10.5,0.5z M8.5,15V6l6,4.5L8.5,15z"></path></g></g></svg>
                                                                                                                                                                                                                                                                                                                                                             <?php break;
                                                                                                                                                                                                                                                                                                                                                         case 6:
                                                                                                                                                                                                                                                                                                                                                             ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8
                                                                                                                                                                                                                                V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>
                        <?php break;
                    case 5:
                        ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8
                                                                                                                                                                                                                                V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>
                                                                                                                                                                                                                                                                                                                                                                    <?php break;
                                                                                                                                                                                                                                                                                                                                                                case 2:
                                                                                                                                                                                                                                                                                                                                                                    ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8
                                                                                                                                                                                                                                V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>
                                                                                                                                                                                                                                                                                                                                                             <?php break;
                                                                                                                                                                                                                                                                                                                                                         case 7:
                                                                                                                                                                                                                                                                                                                                                             ?>
                                                            <svg version="1.1" class="svg-common" .333="" x="0px" y="0px" width="19px" height="21px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M20.5,4.5h-7.6l3.3-3.3l-0.7-0.7l-4,4l-4-4L6.8,1.2l3.3,3.3H2.5c-1.1,0-2,0.9-2,2v12c0,1.1,0.9,2,2,2h18
                                                                                                                                                                                                                                c1.1,0,2-0.9,2-2v-12C22.5,5.4,21.6,4.5,20.5,4.5z M20.5,18.5h-18v-12h18V18.5z M8.5,8.5v8l7-4L8.5,8.5z"></path></g></svg>
                                                            <?php break;
                                                        case 3:
                                                            ?>
                                                            <svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0z
                                                                                                                                                                                                                                M8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>
                                                            <?php break;
                                                        case 8:
                                                            ?>
                                                            <svg version="1.1" x="0px" y="0px" class="svg-common" width="19px" height="17px" viewBox="0 0 23 21" enable-background="new 0 0 23 21" xml:space="preserve"><g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g><g><path fill="#808080" d="M19.7,0H3.3C2.1,0,1,1.1,1,2.3v16.3C1,20,2.1,21,3.3,21h16.3c1.3,0,2.3-1,2.3-2.3V2.3C22,1.1,21,0,19.7,0z
                                                                                                                                                                                                                                M8,16.3H5.7V8.2H8V16.3z M12.7,16.3h-2.3V4.7h2.3V16.3z M17.3,16.3H15v-4.7h2.3V16.3z"></path><path fill="none" d="M-0.5-1.5h24v24h-24V-1.5z"></path></g></svg>
                        <?php break;
                    default :
                        ?>
                                                            <svg version="1.1" class="svg-common" x="0px" y="0px" width="17px" height="16px" viewBox="0 0 17 21" enable-background="new 0 0 17 21" xml:space="preserve"><g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path></g></g><g><path fill="none" d="M-3.5-1.5h24v24h-24V-1.5z"></path><path fill="#808080" d="M10.5,0.5h-8c-1.1,0-2,0.9-2,2l0,16c0,1.1,0.9,2,2,2h12c1.1,0,2-0.9,2-2v-12L10.5,0.5z M12.5,16.5h-8v-2h8
                                                                                                                                                                                                                                V16.5z M12.5,12.5h-8v-2h8V12.5z M9.5,7.5V2L15,7.5H9.5z"></path></g></svg>
                                                            <?php break;
                                                    }
                                                    ?></span>
                                                <span class="solution-child-l-r-margin solution-child-table-cell min-width-list"><?php
                                                    switch ($lecture['cl_lecture_type']) {
                                                        case 1: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 11: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 12: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 4: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 6: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 5: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 2: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 7: $lect_count++;
                                                            echo 'Lecture ' . $lect_count;
                                                            break;
                                                        case 3: $quiz_count++;
                                                            echo 'Quiz ' . $quiz_count;
                                                            break;
                                                        case 8: $quiz_count++;
                                                            echo 'Quiz ' . $quiz_count;
                                                            break;
                                                        default :break;
                                                    }
                                                    ?></span>
                                                <span class="solution-child-l-r-margin solution-child-table-cell lecture-des"><?php echo $lecture['cl_lecture_name']; ?></span>
                                                <span class="solution-child-l-r-margin pull-right solution-time-align time-hide "><?php
                                                    switch ($lecture['cl_lecture_type']) {
                                                        case 1: echo $lecture['unique'];
                                                            break;
                                                        case 12: echo $lecture['unique'];
                                                            break;
                                                        case 2: echo $lecture['cl_total_page'] > 1 ? $lecture['cl_total_page'] . ' Pages' : $lecture['cl_total_page'] . ' Page';
                                                            break;
                                                        case 7: echo $lecture['unique'];
                                                            break;
                                                        case 3: echo $lecture['unique'] > 1 ? $lecture['unique'] . ' Questions' : $lecture['unique'] . ' Question';
                                                            break;
                                                        case 8: echo $lecture['unique'] > 1 ? $lecture['unique'] . ' Pages' : $lecture['unique'] . ' Page';
                                                            break;
                                                        default :echo '';
                                                            break;
                                                    }
                                                    ?></span>   
                                            </li>
            <?php endforeach; ?>
                                    </ul>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if ($l_count < $course_details['lecture_count']): ?>
                                <a href="javascript:void(0)" onclick="full_curriculum(this)" id="loadMore">View full curriculum</a> 
    <?php endif; ?>
                        </div><!--change-size-of-abt-course--> 
<?php } ?>
                </div><!--container-->
            </div>
        </div> <!--curriculam-->

        <div class="tab-pane  in" id="report">
            <div id="db_rank" class="container container-res-chnger-frorm-page">
                <div class="changed-container-for-forum">
                    <h3 class="formpage-heading graph-heading">Rank Progress</h3>
                    <!-- Graph HTML -->
                    <div class="ling-graph-wrap">                                   
                        <div id="chartgreen" class="greencharts"></div>
                    </div>
                    <!-- end Graph HTML -->	                    
                </div><!--ling-graph-wrap-->
            </div>  <!-- container container-res-chnger-frorm-page-->


            <div id="db_bar_chart" class="progress-graph">
                <div class="container container-res-chnger-frorm-page">
                    <div class="changed-container-for-forum progress-graph-center">
                        <h3 class="formpage-heading graph-heading">Topic Strength</h3>
                        <!-- Graph HTML -->
                        <div class="ling-graph-wrap">
                            <div class="holder" id="topic_average">
                                
                            </div>   <!--holder-->
                            <div class="bar-details">
                                <div class="parent-bar-details">
                                    <span class="bar-tunnel green-bar"></span><!--bar-tunnel-->
                                    <span class="bar-text">Excellent (above 90%)</span>
                                </div><!--columns-->

                                <div class="parent-bar-details">
                                    <span class="bar-tunnel blue-bar"></span><!--bar-tunnel-->
                                    <span class="bar-text">Good (80% - 90%)</span>
                                </div><!--columns-->
                            </div><!--bar-details-->   

                            <div class="bar-details bardetails-second">
                                <div class="parent-bar-details">
                                    <span class="bar-tunnel bar-violet"></span><!--bar-tunnel-->
                                    <span class="bar-text">Average (60% - 80%)</span>
                                </div><!--columns-->

                                <div class="parent-bar-details">
                                    <span class="bar-tunnel bar-peach"></span><!--bar-tunnel-->
                                    <span class="bar-text">Needs Improvement (below 60%)</span>
                                </div><!--columns-->


                            </div><!--bar-details-->                                    
                        </div><!--ling-graph-wrap-->
                    </div><!--changed-container-for-forum-->
                </div> <!--container container-res-chnger-frorm-page-->  
            </div><!--progress-graph-->



            <div id="assessments" class="progress-graph">
                <div id="db_assessments" class="container container-res-chnger-frorm-page">
                    <div class="changed-container-for-forum">
                        <h3 class="formpage-heading graph-heading">Assessments</h3>
                        <ul class="discussion-forum-parent" id="assessments_ul">

                        </ul>
                        <div class="load-more pull-left full-width loadmore-margin-top"><a onclick="get_assessments()" id="assessment_load" href="javascript:void(0)" class="btn dark-round-btn center-block noborder black-btn-alter">Load More</a></div>	
                    </div><!--changed-container-for-forum-->
                </div> <!--container container-res-chnger-frorm-page-->  
            </div><!--progress-graph-->


        </div><!--report-->

    </div><!--tab-contents-->

    <div id="report_modal" data-internal-id="" class="modal fade ofabee-modal" role="dialog">
        <div class="modal-dialog"> 
            <div class="modal-content ofabee-modal-content">
                <div class="modal-header ofabee-modal-header">
                    <button type="button" class="close cancel_reporting">&times;</button>
                    <h4 class="modal-title ofabee-modal-title">Report Abuse</h4>
                </div>
                <div class="modal-body ofabee-modal-body">
                    <textarea id="report_reason" class="ofabee-textarea" placeholder="Type your reason for reporting."></textarea>
                </div>
                <div class="modal-footer ofabee-modal-footer">
                    <button type="button" class="btn ofabee-dark cancel_reporting">Cancel</button>
                    <button type="button" onclick="report_comment_confirm()" class="btn ofabee-orange">Submit</button>
                </div>
            </div>

        </div>
    </div>


</section>
<script type="text/javascript">
    var __assessment_count = 1;
    var __site_url = '<?php echo site_url() ?>';
    var __user_id = '<?php echo $session['id'] ?>';
    var __default_user_path = '<?php echo default_user_path() ?>';
    var __user_path = '<?php echo user_path() ?>';
    var __theme_img = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
<?php if (!empty($course_details['ratings'])) { ?>
            var __ratings_object = atob('<?php echo base64_encode(json_encode($course_details['ratings'])) ?>');
<?php } else { ?>
            var __ratings_object = '0';
<?php } ?>
        var __discussions_object = atob('<?php echo base64_encode(json_encode($course_details['discussions'])) ?>');
<?php if (!empty($course_details['rank_graph'])) { ?>
            var __rank_object = atob('<?php echo base64_encode(json_encode($course_details['rank_graph'])) ?>');
<?php } else { ?>
            var __rank_object = '0';
<?php } ?>
        var __offset_rating = 2;
        var __offset_discussion = 2;
        var __offset_discussion_child = [];
        var __child_limit = '<?php echo $child_limit; ?>';
        var __discussion_limit = '<?php echo $discussions_per_page; ?>';
        var __perPage = '<?php echo $rating_per_page ?>';
        var __start_ratings = false;
        var __start_discussions = false;
        var __course_id = '<?php echo $course_details['id']; ?>';
        var __topic_wise = atob('<?php echo base64_encode(json_encode($course_details['assessment_categories'])) ?>');
        var __filter_discussion = [];
        var __assessment_offset = 0;
        var __recieved_child = [];
        var __child_count = [];
        __child_count[0] = '<?php echo $course_details['discussion_count'] > 0 ? $course_details['discussion_count'] : 0; ?>';
</script>
<script>
    $(document).ready(function () {
        $('.in,.open').removeClass('in open');
        $('.dropdown-submenu a.test').on("click", function (e) {

            $(this).next('ul').toggle();
            e.stopPropagation();
            e.preventDefault();
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.bread-parent li a').click(function () {
            $('li a').removeClass("active-bread-parent");
            $(this).addClass("active-bread-parent");
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var showChar = 200;
        var ellipsestext = "...";
        var moretext = "Read full details";
        var lesstext = "Read less details";
        $('.more').each(function () {
            var content = $(this).html();

            if (content.length > showChar) {

                var c = content.substr(0, showChar);
                var h = content.substr(showChar - 1, content.length - showChar);

                var html = c + '<span class="moreelipses">' + ellipsestext + '</span><span class="morecontent"><span>' + h + '</span><a href="" class="morelink">' + moretext + '</a></span>';

                $(this).html(html);
            }

        });

        $(".morelink").click(function () {
            if ($(this).hasClass("less")) {
                $(this).removeClass("less");
                $(this).html(moretext);
            } else {
                $(this).addClass("less");
                $(this).html(lesstext);
            }
            $(this).parent().prev().toggle();
            $(this).prev().toggle();
            return false;
        });
    });
</script>


<script>
    setTimeout(function start() {

        $('.bar').each(function (i) {
            var $bar = $(this);
            $(this).append('<span class="count"></span>')
            setTimeout(function () {
                $bar.css('width', $bar.attr('data-percent'));
            }, i * 100);
        });

        $('.count').each(function () {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).parent('.bar').attr('data-percent')
            }, {
                duration: 2000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now) + '%');
                }
            });
        });

    }, 500)
</script>
<script>
    var __my_rating = '<?php echo ($check_user_rating == 0) ? false : true; ?>';
    var __rating_course = '<?php echo $course_rating ?>';
    var __start = true;
    // $(document).ready(function () {
    //     $(".Showmore-btm").click(function () {
    //         $(".show-more-data-wrap").removeClass("show-more-collapse");
    //         $(".show-more-data-wrap").css({'max-height': 'none'});
    //         $(".Showmore-btm").remove();
    //     });
    // });
    $(function () {
        $('#example_course_dashboard').barrating({
            theme: 'fontawesome-stars',
            readonly: __my_rating,
            onSelect: function (value, text) {
                __rating_course = value;
                if (__start == true) {
                    rate_course(__rating_course);
                }
            }
        });

    });
    function rate_course(__rating_course) {
        __start = false;
        $('#example2').barrating({
            theme: 'fontawesome-stars',
            readonly: false,
            onSelect: function (value, text) {
                __rating_course = value;
                $('#example_course_dashboard').barrating('set', __rating_course);
                //console.log('Bla : ' + __rating_course);
            }
        });
        $('#example2').barrating('set', __rating_course);
        $('#rate_course').modal('show');
    }


    $(document).on('hidden.bs.modal', '#rate_course', function (e) {
        __start = true;
        $('#example_course_dashboard').barrating('clear');
        $('#review_course').val('');
    });
    $(document).on('hidden.bs.modal', '#rate_course_preview', function (e) {
        __start = false;
        $('#rate_course_label').html('Your rating');
        $('#example_course_dashboard').barrating('set', __rating_course);
        $('#example_course_dashboard').barrating('readonly', true);
    });



    $(document).on('click', '#submit_rating_course', function () {

        var __review = $('#review_course').val();
        $.ajax({
            url: __site_url + 'material/save_rating_review',
            type: "POST",
            async: false,
            data: {"is_ajax": true, 'course_id': __course_id, 'rating': __rating_course, 'review': __review},
            success: function (response) {
                var data = $.parseJSON(response);
                $('#rate_course').modal('hide');
                $('#rate_course').on('hidden.bs.modal', function (e) {
                    $('#example4').barrating({
                        theme: 'fontawesome-stars',
                        readonly: true
                    });
                    $('#example4').barrating('set', __rating_course);
                    $('#preview_review_course').text(__review);
                    $("#rate_course_preview").modal('show');
                });

            }
        });
    });

</script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/course_dashboard.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/exporting.js"></script>
<script type="text/javascript" src="<?php echo assets_url() . 'themes/' . $this->config->item('theme') . '/js/jquery.barrating.min.js'; ?>" ></script>
<?php include('footer.php'); ?>
