<style type="text/css">
    .nouseclass-as:before {
      display: table-cell;
      counter-increment: acounter;
      content: counter(acounter) '.';
    }
    ul {
      counter-reset: acounter;                
      list-style-type: none;
    }
</style>
<div class="all-challenges">
        <div class="container container-altr">
            <div class="container-reduce-width">
                <div class="tab-content">
                    <div id="sdpkDashboard" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-12">
                                <?php if(isset($test_report['assessment_courses']) && !empty($test_report['assessment_courses'])): ?>
                                <?php foreach($test_report['assessment_courses'] as $a_course): ?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <h4 class="sd-course-title testreport-title"><span class="report-grad"><img src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/school-dark.svg"></span><?php echo $a_course['cb_title']; ?></h4>
                                        </div>                        
                                    </div>
                                    <ul class="list-group report-lists">
                                        <?php print_assessments($a_course['report']); ?>
                                    </ul>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-course-container">
                                        <img class="no-questions-svg" src="<?php echo assets_url('themes/'.$this->config->item('theme')); ?>img/no-results.svg">
                                        <span class="no-discussion no-content-text"><span>Oops! </span>No assessments attended yet.</span>
                                        <div class="text-center">
                                            <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
                                        </div><!--text-center-->
                                    </div>
                                <?php endif; ?>
                                
                            
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
                                            <path fill="#333333" d="M16.2,4.2L10.5,10L4.8,4.2L3,6l7.5,7.5L18,6L16.2,4.2z"/>
                                            <path fill="none" d="M-4.5-6.5h30v30h-30V-6.5z"/>
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
    </div>

<?php function print_assessments($assessments = array()){ ?>
                                    <?php $theme = config_item('theme'); ?>
                                    <?php foreach($assessments as $assessment): ?>
                                        <?php if($assessment['aa_mark_scored'] == ''): ?>
                                            <li class="list-group-item single-report">
                                                <span class="report-assignmenticon"><img src="<?php echo assets_url('themes/'.$theme); ?>img/test-report.svg"></span>
                                                <span class="report-slnmbr"><span class="nouseclass-as"></span></span>
                                                <span class="report-desc"><?php echo $assessment['cl_lecture_name'] ?></span>
                                                <span class="report-date">-</span>
                                                <span class="report-time">-</span>
                                                <span class="report-mark">-</span>
                                                <span class="report-action"><a class="report-btn" target="_blank" href="<?php echo site_url('material/test/'.$assessment['cl_course_id'].'/0/0#'.$assessment['lecture_id']); ?>">Attend</a></span>
                                            </li>
                                        <?php else: ?>
                                            <li class="list-group-item single-report">
                                                <span class="report-assignmenticon"><img src="<?php echo assets_url('themes/'.$theme); ?>img/test-report.svg"></span>
                                                <span class="report-slnmbr"><span class="nouseclass-as"></span></span>
                                                <span class="report-desc"><?php echo $assessment['cl_lecture_name'] ?></span>
                                                <span class="report-date"><?php echo date('M d, Y',strtotime($assessment['aa_attempted_date'])) ?></span>
                                                <span class="report-time"><?php echo gmdate("i:s", (int)$assessment['aa_duration'])?></span>
                                                <span class="report-mark"><?php echo $assessment['aa_mark_scored']; echo ($assessment['aa_mark_scored']==0)?' Mark':' Marks'; ?></span>
                                                <span class="report-action"><a target="_blank" href="<?php echo site_url('material/test/'.$assessment['cl_course_id'].'/0/0#'.$assessment['lecture_id']); ?>" class="report-link">Retry</a></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php } ?>