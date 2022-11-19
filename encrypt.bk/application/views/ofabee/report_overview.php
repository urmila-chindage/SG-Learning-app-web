
    <div class="all-challenges">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="tab-content">
                    <div id="sdpkDashboard" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-xs-8 col-sm-8 col-md-8">
                                        <h4 class="sd-course-title">Grade Overview</h4>
                                    </div>                        
                                    <?php if(!empty($courses)): ?>
                                    <div class="col-xs-4 col-sm-4 col-md-4">
                                        <a href="#" class="more-challenges sd-challenge">View All</a> 
                                    </div>                        
                                    <?php endif ?>

                                </div>
                                <?php $assessment_html = ''; if(!empty($courses)): ?>
                                <?php foreach($courses as $course): ?>
                                <?php
                                    $image_first_name   = substr($course['cb_image'],0,-4);
                                    $image_dimension    = '_300x160.jpg';
                                    $image_new_name     = $image_first_name.$image_dimension;
                                ?>
                                <?php
                                $completion_percentage = 0; 
                                $total_marks = 0;
                                $total_marks_percentage = 0;
                                $grade_percentage = 0;
                                if(!empty($course['report']))
                                {
                                    $total_completed = 0;
                                    $total_not_completed = 0;
                                    $assessment_html = '';
                                    $count = 0;
                                    foreach($course['report'] as $report)
                                    {
                                        $count++;
                                        if($report['aa_attempted_date'])
                                        {
                                            $assessment_html .= '<li class="voilet-li">T'.$count.'</li>';
                                            $total_completed++;
                                            $total_marks_percentage = $total_marks_percentage+$report['ll_marks'];
                                            $total_marks = $total_marks+$report['aa_mark_scored'];
                                        }
                                        else
                                        {
                                            $assessment_html .= '<li>T'.$count.'</li>';
                                            $total_not_completed++;
                                        }
                                    }
                                    
                                    if($total_not_completed == 0)
                                    {
                                        $completion_percentage = '100';                                    
                                    }
                                    if($total_completed == 0)
                                    {
                                        $completion_percentage = '0';                                    
                                    }
                                    if($total_completed > 0 && $total_not_completed > 0 )
                                    {
                                        $completion_percentage = round(($total_completed/$count)*100);
                                    }
                                    
                                    if($total_completed!=0)
                                    {
                                        $grade_percentage = $total_marks_percentage/$total_completed;                                    
                                    }

                                }
                                ?>
                                    <div class="my-course course-block-1 report-course-block">
                                        <div class="course-img report-img"><img src="<?php echo (($course['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course['id']))).$image_new_name; ?>"></div>
                                        <div class="report-cont">

                                            <a href="javascript:void(0)"><h4 class="report-overview-head"><?php echo $course['cb_title'] ?></h4></a>
                                            <div class="progress course-progress report-progress">
                                                <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $completion_percentage ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $completion_percentage ?>%">
                                                    <span class="sr-only"><?php echo $completion_percentage ?>% Complete</span>
                                                </div>
                                            </div>
                                            <div class="progress-status"><?php echo $completion_percentage ?>%</div>
                                            <div class="grade-points">
                                                <ul>
                                                    <?php echo $assessment_html ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="report-grades">
                                            <span class="mark-block"><strong><?php echo $total_marks ?></strong> Marks</span>
                                            <span class="grade-block"><?php echo convert_to_percentage(round($grade_percentage)) ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-course-container">
                                        <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-results.svg">
                                        <span class="no-discussion no-content-text"><span>Oops! </span>Attend any assessment from any course to display the results here.</span>
                                    </div>
                                <?php endif; ?>
                                
<?php 
function convert_to_percentage($grade_percentage)
{
    $grade_percentage = floor($grade_percentage/10);
    $grade = array();
    $grade[0] = 'E';
    $grade[1] = 'E';
    $grade[2] = 'D';
    $grade[3] = 'D+';
    $grade[4] = 'C';
    $grade[5] = 'C+';
    $grade[6] = 'B';
    $grade[7] = 'B+';
    $grade[8] = 'A';
    $grade[9] = 'A+';
    $grade[10] = 'A+';
    return (isset($grade[$grade_percentage])?$grade[$grade_percentage]:'-');
}
?>
                                

                                                       



                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <h4 class="sd-course-title">Strength Report</h4>
                                    </div>                        

                                </div>
                                <div class="my-notify course-block-1">
                                    <?php $empty_categories = true; ?>
                                    <?php if(!empty($topic_wise)): ?>
                                    <ul class="list-group notification">
                                    <?php foreach($topic_wise as $topic): ?>
                                        <?php 
                                            $strength_perntage          = 0;
                                            $weak_perntage              = 0;
                                            $topic['scored_mark']       = $topic['scored_mark']==null?0:$topic['scored_mark'];
                                            $strength_perntage          = ($topic['scored_mark']/$topic['total_mark'])*100;
                                            $strength_perntage          = round($strength_perntage,2);
                                            $weak_perntage              = 100 - $strength_perntage;
                                        ?>
                                        <?php if($topic['duration']!=0): ?>
                                            <?php $empty_categories = false; ?>
                                            <li class="list-group-item strength-list">
                                                <div class="full-width strength-label"><span><?php echo $topic['qc_category_name'] ?></span> (<?php echo gmdate("i:s", (int)$topic['duration']) ?> Avg)</div>
                                                <div class="full-width">
                                                    <div class="strength-left">
                                                        <?php echo $strength_perntage; ?>%
                                                    </div>
                                                    <div class="progress strength-bar">
                                                        <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo $strength_perntage; ?>"
                                                             aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $strength_perntage; ?>%"></div>
                                                    </div>
                                                    <div class="strength-right">
                                                        <?php echo $weak_perntage; ?>%
                                                    </div> 
                                                </div>          
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </ul> 
                                    <?php endif; ?>
                                </div>          
                                <div class="no-course-container" <?php echo ((!$empty_categories)?'style="display:none"':''); ?>>
                                    <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-results.svg">
                                    <span class="no-discussion no-content-text"><span>Oops! </span>No assessments attended yet.</span>
                                    <div class="text-center">
                                        <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
                                    </div><!--text-center-->
                                </div>

                            </div>

                        </div>   
                    </div>


                    <div id="sdpkReportcard" class="tab-pane fade">

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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit sidebar-card"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                            <img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/android_img.jpg" class="card-img-fit"> 
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
                                    <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-wishlist.svg">
                                    <span class="no-discussion no-content-text"><span>Use wishlist </span>to keep track of courses you want to purchase</span>
                                    <div class="text-center">
                                        <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Browse Courses</a></span>
                                    </div><!--text-center-->
                                </div> 				

                            </div>              
                        </div>   
                    </div>
                </div>            
            </div>	<!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->