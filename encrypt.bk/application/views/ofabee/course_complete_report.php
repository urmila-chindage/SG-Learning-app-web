<?php include('header.php'); ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/css-circular-prog-bar.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/zabuto_calendar.min.css">
<section>
    <div class="all-challenges">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="tab-content">
                    <div id="sdpkDashboard" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-3">
                                <h4>Study Plan</h4>
                                <?php $plan_completed = isset($study_plan['percentage'])?round($study_plan['percentage']):0; ?>
                                <div class="flex-card course-block-1">
                                    <div class="p10">
                                        <div class="progress-circle <?php echo ($plan_completed>49)?'over50':'' ?> p<?php echo $plan_completed ?>">
                                            <span><?php echo $plan_completed ?>%</span>
                                            <div class="left-half-clipper">
                                                <div class="first50-bar"></div>
                                                <div class="value-bar"></div>
                                            </div>
                                        </div>
                                    </div>    
                                    <h5 class="text-center border-bottom pb25 mb0">This Week</h5>
                                    <div class="sd-action-box">
                                        <?php /* ?><button class="btn btn-orange flex-round-btn" type="button">
                                            Go to Study Plan
                                        </button><?php */ ?>
                                        <a class="btn btn-orange flex-round-btn" href="<?php echo site_url('plan/study_plan') ?>">
                                            Go to Study Plan
                                        </a>
                                    </div>
                                </div> 

                                <div id="my-calendar"></div>


                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">My Subscriptions</h4>
                                    </div>                        
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <a href="<?php echo site_url('dashboard') ?>" class="more-challenges sd-challenge">View All</a> 
                                    </div>                        

                                </div>
                                
                                <?php if(!empty($course_completion)): ?>
                                <?php foreach($course_completion as $course_id => $course): ?>
                                <?php 
                                $image_first_name   = substr($course['cb_image'],0,-4);
                                $image_dimension    = '_300x160.jpg';
                                $image_new_name     = $image_first_name.$image_dimension;
                                $image              = (($course['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course_id))).$image_new_name;
                                ?>
                                    <div class="my-course course-block-1">
                                        <div class="course-img"><img src="<?php echo $image ?>"></div>
                                        <div class="course-cont">
                                            <h4><a href="<?php echo site_url('material/dashboard/'.$course_id) ?>"><?php echo $course['cb_title'] ?></a></h4>

                                            <p><span class="text-left spanblock"> <?php echo ($course['total_lectures']>0)?'Lecture '.$course['total_lectures']:'' ?></span><span class="text-right spanblock"><?php echo round($course['course_percentage']) ?>%</span></p>
                                            <div class="progress course-progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo round($course['course_percentage']) ?>%">
                                                    <span class="sr-only"><?php echo round($course['course_percentage']) ?>% Complete</span>
                                                </div>
                                            </div>                                
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?>

                                                        



                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">Notifications</h4>
                                    </div>                        
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <a href="#" class="more-challenges sd-challenge">View All</a> 
                                    </div>                        

                                </div>
                                <div class="my-notify course-block-1">
                                    <ul class="list-group notification">
                                        <li class="list-group-item">
                                            <span class="sl-no">1.</span>
                                            <span class="noti-content"><a href="#">First item notification text comes here</a></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="sl-no">2.</span>
                                            <span class="noti-content"><a href="#">First item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes hereFirst item notification text comes here</a></span>
                                        </li>
                                        <li class="list-group-item">
                                            <span class="sl-no">3.</span>
                                            <span class="noti-content"><a href="#">First item notification text comes here</a></span>
                                        </li>
                                    </ul> 
                                </div>                        
                            </div>
                            <div class="col-sm-3">
                                <h4>Upcoming Events</h4>
                                <div class="flex-card course-block-1">
                                    <ul class="list-group event-group">
                                        <?php function render_upcomming($param = array()){ 
                                                    $return = '';
                                                    $date   = '';
                                                    switch ($param['cl_lecture_type']) {
                                                        case 8:
                                                        $date = strtotime($param["dt_last_date"]);
                                                            $return .= '<li class="list-group-item single-event">';
                                                            $return .= '<a href="#"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                            $return .= '<span class="event-title">Submission of '.$param["clt_name"].' named '.$param["cl_lecture_name"].'</span><br/>';
                                                            $return .= '<span class="event-date">'.date('M d, Y',$date).'</span>';
                                                        break;

                                                        case 7:
                                                        $date = strtotime($param["ll_date"]);
                                                            $return .= '<li class="list-group-item single-event">';
                                                            $return .= '<a href="#"><span class="badge lefty round-label">'.date('d',$date).'<br>'.date('M',$date).'</span>';
                                                            $return .= '<span class="event-title">A '.$param["clt_name"].' named '.$param["cl_lecture_name"].' has been scheduled.</span><br/>';
                                                            $return .= '<span class="event-date">'.date('M d, Y  ',$date).date("g:i A", strtotime($param['ll_time']));'</span></a></li>';
                                                        break;

                                                    }
                                                return $return;
                                              } ?>

                                              <?php foreach($upcomming as $upcomming_event): 
                                                        echo render_upcomming($upcomming_event);
                                                    endforeach;
                                              ?>                               
                                    </ul>
                                </div>    
                            </div>                    
                        </div>   
                    </div>


                    <div id="sdpkCourses" class="tab-pane fade">

                        <div class="row">
                            <div class="col-xs-6 col-sm-4 col-md-4">
                                <h3 class="dashboard-mycourse-h3">Wishlisted Courses</h3>
                            </div>
                            <div class="col-xs-6 col-sm-8 col-md-8">
                                <div class="btn-group cat-menu dashboard-cat-menu">
                                    <button type="button" class="form-control btn dropdown-toggle big-input course-drop" data-toggle="dropdown">
                                        <h3 class="menu-h3 dash-h3">View by <strong>Category</strong></h3> 
                                        <span class="category-caret dash-caret">
                                            <svg version="1.1" x="0px" y="0px" width="12px" height="10px" viewBox="0 0 21 17" enable-background="new 0 0 21 17" xml:space="preserve">
                                            <g>
                                            <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"></path>
                                            <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"></path>
                                            </g>
                                            </svg>                                   
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu generate-dropdown dashboard-drop">
                                        <li><a href="#">Category Name</a></li>
                                        <li><a href="#">Category Name</a></li>
                                        <li><a href="#">Category Name</a></li>
                                    </ul>
                                </div> 
                            </div>
                        </div>
                        <div class="row course-cards-row">
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart tool-tip" data-toggle="tooltip" data-placement="left" title="Remove Wishlist"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                        <!--courser-bottom-half--> 
                                    </div>
                                    <!--course-block-1--> 
                                </a></div>

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                    <!--course-block-1--> 
                                </a>
                            </div>
                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                        <!--courser-bottom-half--> 
                                    </div>
                                    <!--course-block-1--> 
                                </a>
                            </div>


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit sidebar-card"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>                       

                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div> 


                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>



                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div>



                            <div class="col-xs-4 col-sm-3 col-md-3 tablet-courses mobile-courses">
                                <a class="block-link" href="javascript:void(0);">
                                    <div class="course-block-1">
                                        <div class="course-top-half">
                                            <span class="orange-heart"><i class="icon-heart heart-altr"></i></span>
                                            <img src="img/android_img.jpg" class="card-img-fit"> 
                                        </div>
                                        <!--course-top-half-->
                                        <div class="courser-bottom-half">
                                            <label class="block-head">SSC CGL Complete Study Material (Maths &amp; Eng)</label>             
                                            <p class="sub-head-des">By  M. K. Mohanty</p>


                                            <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:52%" class="star-ratings-sprite-rating"></span></div>

                                            <label class="amount">RS. 1000</label>
                                            <label class="discount">RS. 1000</label>
                                        </div>
                                    </div>
                                </a> 
                            </div> 
                        </div>

                        <div class="row">    
                            <div class="col-sm-12 dashboard-no-course">

                                <div class="no-course-container">
                                    <img class="no-questions-svg" src="img/no-wishlist.svg">
                                    <span class="no-discussion no-content-text"><span>Use wishlist </span>to keep track of courses you want to purchase</span>
                                    <div class="text-center">
                                        <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Browse Courses</a></span>
                                    </div><!--text-center-->
                                </div>              

                            </div>              
                        </div>   




                    </div>











                    <div id="sdpkWishlist" class="tab-pane fade">
                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3>Assessment Results</h3></span></div>
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4>5 assessments</h4></span></div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <ul class="discussion-forum-parent my-dshbord-mar-top">
                                    <li class="discussion-forum-white-lists my-dshbord-head">
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                <span class="my-dashbord-head">Mobile app designing form the scratch with sketch 3: UX and UI Complete</span>
                                            </div><!--columns-->
                                            <div class="col-md-3 col-md-offset-1 col-sm-3 col-sm-offset-1 col-xs-12 text-center">
                                                <span class="my-dashbord-point">102</span>
                                                <span class="my-dashbord-eng-points">Engagement Points</span>
                                            </div><!--columns-->

                                            <div class="col-md-2 col-sm-2 col-xs-12 text-center">
                                                <span class="my-dashbord-point">102</span>
                                                <span class="my-dashbord-eng-points">Total Marks</span>
                                            </div><!--columns-->
                                        </div><!--row-->
                                    </li>

                                    <li class="discussion-forum-white-lists dashboard-courses">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->

                                    <li class="discussion-forum-white-lists dashboard-courses">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                                

                                    <li class="discussion-forum-white-lists dashboard-courses">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"--> 
                                    <li class="discussion-forum-white-lists dashboard-courses">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     
                                    <li class="discussion-forum-white-lists dashboard-courses">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     
                                    <div class="my-dashbord-load-more">
                                        <Span>View More Results</Span>
                                    </div><!--my-dashbord-load-more-->                  
                                </ul>                        
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3>Challenge Zone Results</h3></span></div>
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4>5 challenges</h4></span></div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <ul class="discussion-forum-parent">
                                    <li class="discussion-forum-white-lists dashboard-challenges mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->

                                    <li class="discussion-forum-white-lists dashboard-challenges mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                                

                                    <li class="discussion-forum-white-lists dashboard-challenges mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"--> 
                                    <li class="discussion-forum-white-lists dashboard-challenges mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     
                                    <li class="discussion-forum-white-lists dashboard-challenges mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     

                                </ul>                        
                            </div>
                        </div>        



                        <div class="row">
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-left center-block my-dash-result"><h3>User Generated Test Results</h3></span></div>
                            <div class="col-xs-6 col-sm-6 col-md-6"><span class="text-right center-block my-dash-result"><h4>5 user generated tests</h4></span></div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <ul class="discussion-forum-parent">
                                    <li class="discussion-forum-white-lists dashboard-usertest mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->

                                    <li class="discussion-forum-white-lists dashboard-usertest mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                                

                                    <li class="discussion-forum-white-lists dashboard-usertest mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"--> 
                                    <li class="discussion-forum-white-lists dashboard-usertest mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     
                                    <li class="discussion-forum-white-lists dashboard-usertest mt10">
                                        <span class="forum-title-wrap result-title-wrap forum-title-wrap-alter-index">
                                            <span class="dashboard-result-icon"></span>
                                            <span class="forum-des result-des">Step by Step Mobile app designing from scratch with sketch 3 :UX and UI</span>
                                        </span><!--forum-title-wrap-->
                                        <span class="topic-xs result-mid-box">
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>Nov 30 2016</strong> <br/>Date Attended</span>
                                            <span class="topic-form-text quarter-result date-time-hide"><strong>130.45m</strong> <br/>Time Taken</span>
                                            <span class="topic-form-text quarter-result"><strong>1000</strong><br/>Marks Scored</span>
                                            <span class="topic-form-text quarter-result"><strong>120</strong><br/>Your Rank</span>

                                        </span> <!--topic-xs-->  
                                        <span class="last-post-forum-text last-col-result t-dash-details">
                                            <span class="by-name"><a class="name-orange dash-result-link">View Details</a></span><!--by-name-->
                                        </span><!--last-post-forum-text-->
                                    </li><!--discussion-forum-white-lists"-->                     

                                </ul>                        
                            </div>
                        </div>      


                        <div class="row">    
                            <div class="col-sm-12 dashboard-no-course">

                                <div class="no-course-container">
                                    <img class="no-questions-svg" src="img/no-results.svg">
                                    <span class="no-discussion no-content-text"><span>Oops! </span>No assessments, challenges or tests attended yet.</span>
                                    <div class="text-center">
                                        <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
                                    </div><!--text-center-->
                                </div>              

                            </div>              
                        </div>       


















                    </div>







                    <div id="sdpkAssignments" class="tab-pane fade">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="row">
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <h3 class="all-challenge-head">Challenges in <strong>Civil Service</strong></h3>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <a href="#" class="more-challenges">View All</a> 
                                </div>
                            </div>
                            <div class="category-news-content">
                                <div class="row">
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>      
                                        </div>                   
                                    </div>

                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>  
                                        </div> 
                                    </div>                      

                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>      
                                        </div>                   
                                    </div>


                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>      
                                        </div>                   
                                    </div>
                                </div>
                            </div>     
                            <span class="dark-line"></span>
                            <div class="row">
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <h3 class="all-challenge-head">Challenges in <strong>Banking</strong></h3>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <a href="#" class="more-challenges">View All</a> 
                                </div>
                            </div>

                            <div class="category-news-content">
                                <div class="row">
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>  
                                        </div>                   
                                    </div>


                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left">Attended</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer attend-orange">Attend Now</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> </a> 
                                            </div>  
                                        </div>                   
                                    </div>



                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">                                 
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head--> 
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Unattended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-blue">View Questions</span> 
                                                    <svg version="1.1" class="blue-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   



                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box">
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body">
                                                <span class="circles-wrap">
                                                    <span class="ongoing-left ongpoing-green">Ongoing</span>
                                                    <span class="ongoing-left hidden">Ongoing</span>
                                                </span><!--circles-wrap-->

                                                <span class="ends-details">
                                                    <span class="ends-on">Ends on:</span>
                                                    <span class="ends-on-date">January  02  2017
                                                        <span class="ends-time">11:00 am</span>
                                                    </span>
                                                </span><!--ends-details-->
                                            </span><!--challenge-body-->
                                            <span class="grey-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link"><span class="attend-now-footer">Attend Now</span> 
                                                    <svg version="1.1" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve">
                                                    <g><g><g>
                                                    <path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path>
                                                    </g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon>
                                                    </g></g></svg></a> 
                                            </div>      
                                        </div>                   
                                    </div>
                                </div>
                            </div>        
                            <span class="dark-line"></span>      

                            <div class="row">
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <h3 class="all-challenge-head">Challenges in <strong>Civil Service</strong></h3>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <a href="#" class="more-challenges">View All</a> 
                                </div>
                            </div>



                            <div class="category-news-content">
                                <div class="row">

                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ends on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ending on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div> 
                                </div>
                            </div>
                            <span class="dark-line"></span>
                            <div class="row">
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <h3 class="all-challenge-head">Challenges in <strong>SSC</strong></h3>
                                </div>
                                <div class="col-xs-4 col-sm-4 col-md-4">
                                    <a href="#" class="more-challenges">View All</a> 
                                </div>
                            </div>



                            <div class="category-news-content">
                                <div class="row">                


                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   
                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   


                                    <div class="col-xs-4 col-sm-3 col-md-3 mb30 table-challenge mobile-challenge">
                                        <div class="challenge-inside-block shadow-box"> 
                                            <span class="challenge-block-head">
                                                <span class="challege-inside-area">
                                                    Public Administration<br> Content Enrichment<br> Challenge
                                                </span>
                                            </span><!--challenge-block-head-->
                                            <span class="challenge-body"> 
                                                <span class="circles-wrap"> 
                                                    <span class="ongoing-left ongpoing-red">Ended</span> 
                                                    <span class="ongoing-left ">Attended</span> 
                                                </span><!--circles-wrap--> 
                                                <span class="ends-details"> 
                                                    <span class="ends-on">Ended on:</span> 
                                                    <span class="ends-on-date ends-on-date-narrow">January  02  2017 
                                                        <span class="ends-time ends-on-date-narrow">11:00 am</span> 
                                                    </span> 
                                                </span><!--ends-details--> 
                                            </span><!--challenge-body--> 
                                            <span class="hr-line"></span>
                                            <div class="challenge-footer"> 
                                                <a href="#" class="challenge-link">
                                                    <span class="attend-now-footer attend-orange">View Report</span> 
                                                    <svg version="1.1" class="orange-svg" x="0px" y="0px" viewBox="0 0 24 24" enable-background="new 0 0 24 24" xml:space="preserve"><g><g><g><path fill="#00C753" d="M12,24C5.4,24,0,18.6,0,12S5.4,0,12,0s12,5.4,12,12S18.6,24,12,24z M12,2C6.5,2,2,6.5,2,12s4.5,10,10,10s10-4.5,10-10S17.5,2,12,2z"></path></g></g><g>
                                                    <polygon fill="#00C753" points="9.8,5.4 8.2,7 13.3,12 8.2,17 9.8,18.6 16.4,12"></polygon></g></g></svg> 
                                                </a>
                                            </div><!--challenge-footer--> 
                                        </div>
                                    </div>                   
                                </div>                                                                                                                                 
                            </div>
                        </div>


                    </div>
                </div>            

            </div>  <!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->
</section>

<div id="EventModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header sdpk-modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title sdpk-modal-title"><span class="badge lefty round-label main-label"><span class="big-modal-date">17</span><br/>Jul</span>Events on June 17, 2017</h4>
      </div>
      <div class="modal-body sdpk-modal-body">
        <ul class="list-group event-group" id="modal_events">
          <li class="list-group-item single-event modal-event">
              <a href="#"><span class="badge lefty round-label modal-badge"><img src="img/date.svg" width="24"></span>
                <span class="event-title">Sample Event TitleSample Event TitleSample Event Title</span><br>
                <span class="event-date modal-date">Jun 17, 2017   11:44 PM</span>  
              </a>
          </li>
          <li class="list-group-item single-event modal-event">
              <a href="#"><span class="badge lefty round-label modal-badge"><img src="img/date.svg" width="24"></span>
                <span class="event-title">Sample Event Title</span><br>
                  <span class="event-date modal-date">Jun 17, 2017   <span class="event-time">11:44 PM</span> <span class="event-location">
                      <span class="event-location-map"><img src="img/map.svg" width="18">Thiruvananathapuram</span></span></span>  
                
              </a>
          </li>
          <li class="list-group-item single-event modal-event">
              <a href="#"><span class="badge lefty round-label modal-badge"><img src="img/date.svg" width="24"></span>
                <span class="event-title">Sample Event TitleSample Event TitleSample Event Title</span><br>
                <span class="event-date modal-date">Jun 17, 2017   11:44 PM</span>  
              </a>
          </li>  
          <li class="list-group-item single-event modal-event">
              <a href="#"><span class="badge lefty round-label modal-badge"><img src="img/date.svg" width="24"></span>
                <span class="event-title">Sample Event Title</span><br>
                <span class="event-date modal-date">Jun 17, 2017   11:44 PM</span>  
              </a>
          </li>
          <li class="list-group-item single-event modal-event">
              <a href="#"><span class="badge lefty round-label modal-badge"><img src="img/date.svg" width="24"></span>
                <span class="event-title">Sample Event Title</span><br>
                <span class="event-date modal-date">Jun 17, 2017   11:44 PM</span>  
              </a>
          </li>                                 
        </ul>          

      </div>
      <div class="modal-footer sdpk-modal-center">
        <!-- <button type="button" class="btn btn-orange flex-round-btn modal-btn">View More</button> -->
      </div>
    </div>

  </div>
</div>

<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/zabuto_calendar.js"></script>
<script type="application/javascript">
    var __site_url = '<?php echo site_url(); ?>';
    var __month_data;
    var __assets_url = '<?php echo assets_url(); ?>';
    $(document).ready(function () {
        $("#my-calendar").zabuto_calendar({
            ajax: {
                url: __site_url+"dashboard/calendar_events",
                modal: false
            },
            today: true
            ,action: function() { myDateFunction(this.id); }
        });
    });
    function myDateFunction(date){
        date = date.split('_');
        $.each(__month_data,function(key, m_data) {
            //console.log(m_data);
            if(m_data['date'] == date[3]){
                render_modal(m_data);
            }
        });
    }
    function render_modal(day_data){
        var date       = day_data['date'].split('-');
        //console.log(date);
        var months     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var monthsfull = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        var event_list = '';
        $('.sdpk-modal-title').html('<span class="badge lefty round-label main-label"><span class="big-modal-date">'+date[2]+'</span><br/>'+months[(date[1]-1)]+'</span>Events on '+monthsfull[date[1]-1]+' '+date[2]+', '+date[0]);
        $.each(day_data['events'],function(key, event) {
            event_list += '<li class="list-group-item single-event modal-event">';
            event_list += '<a href="#"><span class="badge lefty round-label modal-badge"><img src="'+__assets_url+'themes/ofabee/img/date.svg" width="24"></span>';
            event_list += '<span class="event-title">'+event['message']+'</span><br>';
            event_list += '<span class="event-date modal-date">'+months[date[1]-1]+' '+date[2]+', '+date[0]+'</span>';
            event_list += '</a>';
            event_list += '</li>';
        });

        $('#modal_events').html(event_list);
        $('#EventModal').modal('toggle');
    }  
</script>    
<?php include('footer.php'); ?>
