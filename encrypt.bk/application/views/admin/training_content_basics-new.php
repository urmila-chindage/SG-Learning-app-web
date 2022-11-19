<?php include_once 'training_header.php'; ?>
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- START -->
       <!--  <div class="right-wrap pos-relative white-bg cont-course-big container-fluid pull-right pad0">
            <div class="container-fluid">
                <div class="row heading-right">
                    <div class="col-sm-12">
                        <h4><?php echo lang('latest_discussion');?></h4>
                    </div>
                </div>

                <div class="row right-cont-style">
                    <div class="col-sm-12">
                    <?php foreach($course_comments as $course_comment) { ?>
                        <div class="right-group-wrap clearfix">
                            <img class="box-style" src="<?php echo (($course_comment['us_image'] == 'default.jpg')?default_user_path():user_path()).$course_comment['us_image']; ?>" alt="<?php echo $course_comment['us_name'] ?>">
                            <span class="user-date">
                                <span class="user"><?php echo $course_comment['us_name'];?></span><br>
                                <?php 
                                        $today              = date('F d, Y');
                                        $today_date_time    = date('F d, Y h:i a');
                                        $date               = date('F d, Y h:i A', strtotime($course_comment['created_date']));
                                        $fetch_date         = date('F d, Y', strtotime($course_comment['created_date']));
                                        $yesterday          = date('F d, Y',strtotime("-1 days"));
                                        //die($today.'//'.$date);
                                        if($today==$fetch_date)
                                        {
                                            $time           = date('h:i A', strtotime($course_comment['created_date']));
                                            $date           = 'Today'.' '.$time;
                                        }
                                        if($yesterday==$fetch_date)
                                        {
                                            $time   = date('h:i A', strtotime($course_comment['created_date']));
                                            $date   = 'Yesterday'.' '.$time;
                                        }
                                    ?>
                                <span class="date"><?php echo $date;?></span>
                            </span>
                            <div class="content-text">
                                <?php echo $course_comment['comment'];?>
                                <a href="<?php echo admin_url('course/discussion').$course_comment['course_id'].'#'.$course_comment['id'] ?>" <?php /*?>onclick="showInputDisc(<?php echo $course_comment['id'];?>);"<?php */ ?> class="link-style pull-right">Reply</a>
                            </div>
                            <div class="form-group" id="input_<?php echo $course_comment['id'];?>" style="display:none;">
                                <textarea class="form-control reply-disc-input" data-id="<?php echo $course_comment['id'];?>" type="text" id="reply_disc_admin"></textarea>
                              </div>
                        </div>
                    <?php } ?>
                    </div>


                </div>

            </div>
        </div> -->
<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->
<section class="content-wrap cont-course-big top-spacing content-wrap-align">

    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12">

        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid pad0 course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12 pad0">
                    <h4 class="course-head"><?php echo lang('course_status') ?></h4>
                    <div class="rTable course-status-cont">
                        <div class="rTableRow">
                            <div class="rTableCell">
                                <a href="<?php echo admin_url('course/users/'.$course['id']).'?&filter=active' ?>">
                                    <span class="big-head">
                                        <?php echo $active_learners ?>
                                    </span>
                                    <p>
                                        Enrolled Students
                                    </p>
                                </a>
                            </div>
                           
                            <div class="rTableCell">
                                <a href="<?php echo admin_url('coursebuilder/home/'.$course['id']); ?>">
                                    <span class="big-head">
                                        <?php echo $quiz ?>
                                    </span>
                                    <p>
                                        Quiz
                                    </p>
                                </a>
                            </div>
                            <div class="rTableCell">
                                <a href="<?php echo admin_url('course/users/'.$course['id']).'?&filter=completed' ?>">
                                    <span class="big-head">
                                        <?php echo $completed ?>
                                    </span>
                                    <p>
                                        Students Completed Course
                                    </p>
                                </a>
                            </div>
                        </div>
                    </div>

                    <h4 class="course-head"><?php echo strtoupper('Launch your course in 04 easy steps');?></h4>
                    <div class="list-style-wrap container-fluid">
                        <h5><b>You may have created and launched your own course already, kindly ignore if so. 
                        <br /><br />Newbies, these are the steps that needs to be followed:</b></h5>


                        <!-- Accordion panel starts -->
                        <div class="accordion-container">

                          <button class="accordion">
                            <?php
                                    if($image_status!='default.jpg'){
                                ?>
                                        <span style="width: 15px;display: inline-block;">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }else{
                                ?>
                                        <span class="" style="color: red;font-size: 26px;font-weight: 600;line-height: 0px;vertical-align: sub;margin-right: 5px;">&times;</span>
                                <?php
                                    }
                                ?>
                                
                                STEP 1: SETTINGS
                          </button>
                          <div class="panel">
                            <div>
                                <ul>
                                    <li>- In the left menu, click on “Settings”.</li>
                                    <li>- Upload the course image(size:(width) X (height)).</li>
                                    <li>- Fill the basic details of the course, some of the details are mandatory.</li>
                                    <li>- Click on “Save” button to store your basic settings.</li>
                                </ul>
                            </div>
                          </div>

                          <button class="accordion">Section 2</button>
                          <div class="panel">
                            <div>
                                <?php
                                    if(count($assigned_tutors)>='1'){
                                ?>
                                        <span style="width: 15px;display: inline-block;">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }else{
                                ?>
                                        <span class="" style="color: red;font-size: 26px;font-weight: 600;line-height: 0px;vertical-align: sub;margin-right: 5px;">&times;</span>
                                <?php
                                    }
                                ?>
                                    STEP 2: ASSIGN FACULTIES
                                    <ul>
                                        <li>- In the left menu, click on “Assign Faculties”.</li>
                                        <li>- Assign faculties to the respective course if needed.</li>
                                        <li>- Faculties can upload contents if they have the permission do so.</li>
                                    </ul>
                                    
                                </p>
                          </div>

                          <button class="accordion">Section 3</button>
                          <div class="panel">
                            <div>
                                <?php
                                    if($lecture_status < '1'){
                                ?>
                                        <span class="" style="color: red;font-size: 26px;font-weight: 600;line-height: 0px;vertical-align: sub;margin-right: 5px;">&times;</span>
                                        
                                <?php
                                    }else{
                                ?>
                                        <span style="width: 15px;display: inline-block;">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }
                                ?>
                                
                                STEP 3: ADD COURSE CONTENTS
                                    <ul>
                                        <li>- In the left menu, click on “Course Content” and start uploading the contents. The system supports to upload multimedia, powerpoint presentations and document contents.</li>
                                        <li>- Each lecture needs to be uploaded under a section.</li>
                                        <li>- Quizzes, assignments, live lectures, HTML, Youtube, Recorded videos, surveys and Certificates can be added in a course.</li>
                                        <li>- Click on the dropdown respective to the section or lecture and select “Activate”. The course contents needs to be activated so that it can be displayed to the students (otherwise the contents shall be inactive).</li>
                                    </ul>
                                
                                </div>
                          </div>

                          <button class="accordion">Section 3</button>
                          <div class="panel">
                            <div>
                                <?php
                                    if($status=='1'){
                                ?>
                                        <span style="width: 15px;display: inline-block;">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }else{
                                ?>
                                        <span class="" style="color: red;font-size: 26px;font-weight: 600;line-height: 0px;vertical-align: sub;margin-right: 5px;">&times;</span>
                                <?php
                                    }
                                ?>
                                STEP 4: ACTIVATION
                                    <ul>
                                        <li>- In the content builder page itself, click on “Activate” button or else in the course listing page click on the dropdown respective to the course and select “Activate”.</li>
                                        <li>- On clicking “Activate” the course shall be launched. (At least one active lecture is required to activate the course).</li>
                                        
                                    </ul>
                             </div>
                         </div>

                        </div>
                        <!-- Accordion panel ends -->


                        <ul>
                            <li>
                                
                                
                            </li>

                            <li>
                                
                            </li>

                            <li>
                                
                            </li>

                            <li>
                            
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Nav section inside this wrap  --> <!-- END -->
  </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>
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

<script>
    // accordion panel
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      });
    }
</script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script> 
<?php include_once 'training_footer.php'; ?>
