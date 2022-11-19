
<?php include_once 'header_beta.php'; 
//print_r($user);
//die();
?>
<style>
.btn-grey-round {
    display: block;
    width: 10px;
    height: 10px;
    -webkit-border-radius: 100%;
    border-radius: 100%;
    background-clip: padding-box;
    background: #949494;
}
</style>
            <div class="dashbord-center">
                <div class="dashbordHead-user-wrap">
                    <svg class="back-btn" style="display:none;" fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M0 0h24v24H0z" fill="none"/>
                    <path d="M21 11H6.83l3.58-3.59L9 6l-6 6 6 6 1.41-1.41L6.83 13H21z"/>
                    </svg>
                    <div class="dashbord-title-wrap">
                        <h1 class="my-overview-title">My Overview</h1>

                        <p>Listing marks you scored on each test</p>
                    </div>
                    <!-- dashbord-title-wrap -->
                    <div class="user-login userlogin-first">
                        <div class="dropdown">
                            <button class="dpdropbtn" type="button">
                                <img class="dpimgae" src="<?php echo (($user['us_image'] == 'default.jpg') ? default_user_path() : user_path()) . $user['us_image'] ?>" alt="<?php echo $user['us_name'] ?>">
                                <?php echo (($unseen_msg_count)?'<span class="dpBadge site_notification_count">'.$unseen_msg_count.'</span>':'') ?></button>
                            <div class="neetrefresh-drops">
                                <div class="notification-drops-title">
                                    <h1>Notifications</h1>
                                    <?php if($unseen_msg_count || $seen_msg_count): ?>
                                    <span class="cleardropsbtn" onclick="markAllAsRead()">
                                        <span class="clear_text">Clear</span>
                                        <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                                        </svg>
                                    </span>
                                    <?php endif; ?>
                                    <!-- cleardropsbtn -->
                                </div>
                                <?php $notification_html = ''; ?>
                                <ul class="neetrefresh-ul">
                                    <?php 
                                        if(isset($site_notification['unseen']) && sizeof($site_notification['unseen'])): 
                                            $empty = false; 
                                            foreach($site_notification['unseen'] as $msg_id => $msg_objects): 
                                                $notification_html .= '<li class="'.$msg_id.'">';
                                                $notification_html .= '    <a onclick="markAsRead(\''.$msg_id.'\', \''.base64_encode(json_encode($msg_objects)).'\')" href="javascript:void(0)">';
                                                $notification_html .= '        <div class="neetrefresh-bulletList"></div>';
                                                $notification_html .= '        <div class="neetfresh-bullets-content">';
                                                $notification_html .= '            <h1 class="neetfresh-li-title">Notifications</h1>';
                                                $notification_html .= '            <div class="neetfresh-li-para">'.$msg_objects['message'].'</div>';
                                                $notification_html .= '            <div class="neetfresh-li-timmer-wrap">';
                                                $notification_html .= '                <svg fill="#909090" height="15" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg">';
                                                $notification_html .= '                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>';
                                                $notification_html .= '                <path d="M0 0h24v24H0z" fill="none"/>';
                                                $notification_html .= '                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>';
                                                $notification_html .= '                </svg>';
                                                $notification_html .= '                <span>'.(isset($msg_objects['time'])?get_time($msg_objects['time']):'').'</span>';
                                                $notification_html .= '            </div>';
                                                $notification_html .= '        </div>';
                                                $notification_html .= '    </a>';
                                                $notification_html .= '</li>';
                                             endforeach;
                                        endif;
                                        if(isset($site_notification['seen']) && sizeof($site_notification['seen'])):
                                            $empty = false;
                                            foreach($site_notification['seen'] as $msg_id => $msg_objects):
                                                $notification_html .= '<li class="'.$msg_id.'">';
                                                $notification_html .= '    <a onclick="markAsRead(\''.$msg_id.'\', \''.base64_encode(json_encode($msg_objects)).'\')" href="javascript:void(0)">';
                                                $notification_html .= '        <div class="neetrefresh-bulletList"></div>';
                                                $notification_html .= '        <div class="neetfresh-bullets-content">';
                                                $notification_html .= '            <h1 class="neetfresh-li-title">Notifications</h1>';
                                                $notification_html .= '            <div class="neetfresh-li-para">'.$msg_objects['message'].'</div>';
                                                $notification_html .= '            <div class="neetfresh-li-timmer-wrap">';
                                                $notification_html .= '                <svg fill="#909090" height="15" viewBox="0 0 24 24" width="15" xmlns="http://www.w3.org/2000/svg">';
                                                $notification_html .= '                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>';
                                                $notification_html .= '                <path d="M0 0h24v24H0z" fill="none"/>';
                                                $notification_html .= '                <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>';
                                                $notification_html .= '                </svg>';
                                                $notification_html .= '                <span>'.(isset($msg_objects['time'])?get_time($msg_objects['time']):'').'</span>';
                                                $notification_html .= '            </div>';
                                                $notification_html .= '        </div>';
                                                $notification_html .= '    </a>';
                                                $notification_html .= '</li>';
                                            endforeach;
                                        endif;
                                        if($empty):
                                            $notification_html .= '<li>';
                                            $notification_html .= '    <a href="javascript:void(0)">';
                                            $notification_html .= '        <div class="neetfresh-bullets-content">';
                                            $notification_html .= '            <h1 class="neetfresh-li-title"></h1>';
                                            $notification_html .= '            <div class="neetfresh-li-para">No notification to show</div>';
                                            $notification_html .= '        </div>';
                                            $notification_html .= '    </a>';
                                            $notification_html .= '</li>';
                                        endif; 
                                    ?>
                                    <?php echo $notification_html ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- dashbordHead-user-wrap -->
                <div class="main-wrapper-scroll">
                    <div class="score-main-wrap">
                        <div class="overall-test-score-wrap">
                            <h4 class="testscore-title">Overall Test Scores</h4>
                            <?php /* ?><svg class="refresh" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/>
                            <path d="M0 0h24v24H0z" fill="none"/>
                            </svg><?php */ ?>
                            <div class="percentage-wraper">
                                <div class="donut-percentage-wrap">
                                    <div id="circle" class="donutmaster">
                                        <span class="chart-percentage-show"></span>
                                    </div>
                                </div>
                                <div class="donut-details-wrap">
                                    <span class="bulletd-gradient"></span>
                                    <span class="percentage-shown-area"><?php echo ($scores['overall_score']['overal_score'])?round($scores['overall_score']['overal_score']):'0' ?>%</span>
                                    <?php if($scores['overall_score']['total_test_attended']): ?>
                                    <div class="from-wrap">
                                        <span>FROM</span>
                                        <span>-Last <span class="last-15ex"><?php echo $scores['overall_score']['total_test_attended'] ?></span> Exams</span>
                                    </div>
                                    <?php endif; ?>
                                    <!-- from-wrap -->
                                </div>
                            </div>
                            <!-- percentage-wraper -->
                        </div>
                        <!-- overall-test-score-wrap -->
                        <div class="miniscore-wrap">
                            <?php $last_child = array(1 => '', 2 => '', 3 => 'test-last-noChild'); $count = 1; ?>
                            <?php if(!empty($test_objects)): ?>
                            <?php foreach($test_objects as $course_id => $c_objects): ?>
                            <div class="test-bar <?php echo $last_child[$count] ?>">
                                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/grand test icon.png" alt="">
                                <span class="testname"><?php echo $c_objects['course']['cb_title'] ?></span>
                                <div class="testbarPercnWrap">
                                    <?php 
                                    $overal_score_temp = $scores['course_score'][$c_objects['course']['id']]['overal_score'];
                                    $overal_score_temp = ($overal_score_temp)?$overal_score_temp:'0';
                                    
                                    $total_test_attended_temp = $scores['course_score'][$c_objects['course']['id']]['total_test_attended'];
                                    $total_test_attended_temp = ($total_test_attended_temp)?$total_test_attended_temp:'0';
                                    ?>
                                    <span class="percentage"><?php echo $overal_score_temp  ?>%</span>
                                    <span class="exams-count"><?php echo $total_test_attended_temp ?> Exams</span>
                                </div>
                                <!-- testbarPercnWrap -->
                            </div>
                            <?php 
                                if($count == 3)
                                {
                                    break;
                                }
                                $count++; 
                            ?>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <!-- sdfsdf -->
                    </div>
                    <!-- score-main-wrap -->
                    <div class="testblock-wrap">
                        <?php $count = 0; ?>
                        <?php if(!empty($test_objects)): ?>
                            <?php foreach($test_objects as $course_id => $c_objects): ?>
                            <?php 
                                $count++; 
                                if($count < 4)
                                {
                                    continue;
                                }
                            ?>
                            <div class="test-bar">
                                <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/grand test icon.png" alt="">
                                <span class="testname"><?php echo $c_objects['course']['cb_title'] ?></span>
                                <div class="testbarPercnWrap">
                                    <?php 
                                    $overal_score_temp = $scores['course_score'][$c_objects['course']['id']]['overal_score'];
                                    $overal_score_temp = ($overal_score_temp)?$overal_score_temp:'0';
                                    
                                    $total_test_attended_temp = $scores['course_score'][$c_objects['course']['id']]['total_test_attended'];
                                    $total_test_attended_temp = ($total_test_attended_temp)?$total_test_attended_temp:'0';
                                    ?>
                                    <span class="percentage"><?php echo $overal_score_temp  ?>%</span>
                                    <span class="exams-count"><?php echo $total_test_attended_temp ?> Exams</span>
                                </div>
                                <!-- testbarPercnWrap -->
                            </div>
                            <?php endforeach; ?>
                            <?php endif; ?>
                    </div>
                    <!-- testblock-wrap -->
                </div>
                <!-- main-wrapper-scroll -->

            </div>
            <!-- dashbord-center -->
            <div class="dashbord-right">
                <div class="dashbord-title-wrap">
                    <div class="overfview-title-wrap">
                        <svg class="myTest-back" id="back-button" fill="#ffffff" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M21 11H6.83l3.58-3.59L9 6l-6 6 6 6 1.41-1.41L6.83 13H21z"></path>
                        </svg>
                        <h1 class="my-overview-title"><?php echo $plan['p_name'] ?></h1>
                        <div class="user-login userloginSecond">
                            <div class="dropdown">
                                <button class="dpdropbtn" type="button">
                                    <img class="dpimgae" src="<?php echo (($user['us_image'] == 'default.jpg') ? default_user_path() : user_path()) . $user['us_image'] ?>" alt="<?php echo $user['us_name'] ?>">
                                    <?php echo (($unseen_msg_count)?'<span class="dpBadge site_notification_count">'.$unseen_msg_count.'</span>':'') ?></button>
                                <div class="neetrefresh-drops">
                                    <div class="notification-drops-title">
                                        <h1>Notifications</h1>
                                        <?php if($unseen_msg_count || $seen_msg_count): ?>
                                        <span class="cleardropsbtn" onclick="markAllAsRead()">
                                            <span class="clear_text">Clear</span>
                                            <svg fill="#000000" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M14.59 8L12 10.59 9.41 8 8 9.41 10.59 12 8 14.59 9.41 16 12 13.41 14.59 16 16 14.59 13.41 12 16 9.41 14.59 8zM12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                            </svg>
                                        </span>
                                        <?php endif; ?>
                                        <!-- cleardropsbtn -->
                                    </div>
                                    <ul class="neetrefresh-ul">
                                        <?php echo $notification_html ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="login-para"><?php echo $plan['p_slogan'] ?> </p>
                    <img class="expand-btn" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/expand.png" alt="expand button">
                </div>
                <ul class="nav nav-tabs nav-tabs-test">
                    <?php $active_class = 'class="active"'; ?>
                    <?php if(!empty($test_objects)): ?>
                        <?php foreach($test_objects as $course_id => $c_objects): ?>
                            <li <?php echo $active_class ?>>
                                <a data-toggle="tab" href="#course_tab_<?php echo $course_id ?>"><?php echo $c_objects['course']['cb_title'] ?>
                                    <svg class="naigating-arrow"  height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M8 5v14l11-7z"/>
                                    <path d="M0 0h24v24H0z" fill="none"/>
                                    </svg>
                                </a>
                            </li>           
                            <?php $active_class = ''; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>

                <div class="tab-content tab-contentMin-height">
                    <!-- test-drops-wrap -->
                    <?php $active_tab = 'in active'; ?>
                    <?php if(!empty($test_objects)): ?>
                        <?php foreach($test_objects as $course_id => $c_objects): ?>
                            <div id="course_tab_<?php echo $course_id ?>" class="tab-pane fade <?php echo $active_tab ?>">
                                <div class="test-drops-wrap" >
                                    <div class="dropdown">
                                        <button id="filter_dropdown_<?php echo $course_id ?>" class="btn btn-trans dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span class="btn-grey-round"></span>All Test                                
                                            <svg fill="#949494" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"/>
                                            <path d="M0-.75h24v24H0z" fill="none"/>
                                            </svg>
                                        </button>
                                        <ul class="dropdown-menu dropdown-left-aligned">
                                            <li>
                                                <a onclick="filterTest('<?php echo $course_id ?>', 'all')" href="javascript:void(0)"><span class="btn-grey-round round"></span><span>All Test</span></a>
                                            </li>
                                            <li>
                                                <a onclick="filterTest('<?php echo $course_id ?>', 'pending')" href="javascript:void(0)"><span class="btn-blue-round round blue-bg"></span><span>Pending</span></a>
                                            </li>
                                            <li>
                                                <a onclick="filterTest('<?php echo $course_id ?>', 'pro')" href="javascript:void(0)"><span class="btn-gold-round round bg-gold"></span><span>Pro</span></a>
                                            </li>
                                            <li>
                                                <a onclick="filterTest('<?php echo $course_id ?>', 'completed')" href="javascript:void(0)"><span class="btn-green-round round bg-green "></span><span>Completed</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php $active_tab = ''; ?>
                                <ul class="test-level-card-ul">
                                    <?php if(!empty($c_objects['lectures'])): ?>
                                        <?php foreach($c_objects['lectures'] as $lecture_id => $l_objects): ?>
                                            <?php 
                                            $under_plan = in_array($lecture_id, $c_objects['plan_lectures']);
                                            ?>
                                            <li class="card-reveal">
                                                <div class="test-lvel-icon-wrap <?php echo (!$under_plan)?'test-level-disbaled':''  ?>">
                                                    <?php
                                                    $status = '';
                                                    $completed = false;
                                                    $icon_html = '';
                                                    $report_html = '';
                                                    $button_html = '';
                                                    if(!empty($l_objects['attempt_hisory']))
                                                    {
                                                        if($l_objects['attempt_hisory']['attempted_questions'] >= $l_objects['assesment']['a_questions'])
                                                        {
                                                            $icon_html = '<img src="'.assets_url().'themes/'.$this->config->item('theme').'/neet/assets/images/test completed.png" alt="test completed.png">';
                                                            $report_html .= '<div class="finishtest-show">';
                                                            $report_html .= '    <h3 class="wldyoulike">Would you like to restart your finished test?</h3>';
                                                            $report_html .= '    <div class="test-accept-wrap">';
                                                            $report_html .= '        <a id="view_result"  name="VIEW RESULT"   href="'.site_url().'material/assesment_report_item/'.$l_objects['attempt_hisory']['attempt_id'].'">VIEW RESULT</a>';
                                                            $report_html .= '        <a id="restart_test" name="RESTART TEST"  href="'.site_url().'material/test/'.$l_objects['assesment']['assesment_id'].'">RESTART TEST</a>';
                                                            $report_html .= '    </div>';
                                                            $report_html .= '</div>';
                                                            //$report_button_html = '<a href="'.site_url().'material/assesment_report_item/'.$l_objects['attempt_hisory']['attempt_id'].'" class="btn btn-green">Result</a>';
                                                            $report_button_html = '<a href="javascript:void(0)" class="btn btn-green">Result</a>';
                                                            $status = 'completed';
                                                        }
                                                        else 
                                                        {
                                                            $percentage = 0;
                                                            if($l_objects['attempt_hisory']['attempted_questions'] && $l_objects['assesment']['a_questions'])
                                                            {
                                                                $percentage = (($l_objects['attempt_hisory']['attempted_questions']/$l_objects['assesment']['a_questions']));                                            
                                                            }
                                                            $icon_html .= '<div>';
                                                            $icon_html .= '    <div data-percentage="'.($percentage).'" class="donutmaster-2 test-percetage-custom">';
                                                            $icon_html .= '        <span class="chart-percentage-show"></span>';
                                                            $icon_html .= '    </div>';
                                                            $icon_html .= '</div>';
                                                            $button_html = '<a href="'.site_url('material/test/'.$l_objects['assesment']['assesment_id'].'/'.$l_objects['attempt_hisory']['attempt_id']).'" class="btn btn-blue">Restart</a>';
                                                            $report_button_html = $button_html;
                                                            $status = 'pending';
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $button_html = '<a href="'.site_url('material/test/'.$l_objects['assesment']['assesment_id']).'" class="btn btn-green">Attend</a>';
                                                        $report_button_html = $button_html;
                                                        $icon_html .= '<div>';
                                                        $icon_html .= '    <div data-percentage="0" class="donutmaster-2 test-percetage-custom">';
                                                        $icon_html .= '        <span class="chart-percentage-show"></span>';
                                                        $icon_html .= '    </div>';
                                                        $icon_html .= '</div>';
                                                        $status = 'pending';
                                                    }
                                                    ?>

                                                    <?php echo $icon_html ?>
                                                    <div class="test-level-head-wrap">
                                                        <h3 class="test-level-title"><?php echo $l_objects['cl_lecture_name'] ?></h3>
                                                        <div class="test-level-quest-marks">
                                                            <div class="ques-test-wrap"><span class="blue-text"><?php echo $l_objects['assesment']['a_questions'] ?> </span> Questions</div>
                                                            <div class="quest-amswer-time"><img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/images/<?php echo ($under_plan)?'time icon.png':'timeicon-grey.png' ?>"><span class="red-text"><?php echo round($l_objects['assesment']['a_duration']/60) ?></span>&nbsp; Min</div>
                                                        </div>
                                                        <!-- test-level-quest-marks -->
                                                    </div>
                                                </div>
                                                <div class="testlevel-details">
                                                    <?php if($under_plan): ?>
                                                        <?php echo $report_button_html ?>
                                                    <?php else: ?>
                                                        <a href="<?php echo site_url('dashboard/plans/'.base64_encode($lecture_id)) ?>" class="btn btn-gold">Join Pro</a>
                                                        <?php $status = 'pro'; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if($under_plan): ?>
                                                    <?php echo $report_html ?>
                                                <?php endif; ?>
                                                <span class="current_test_status" style="display: none;" data-status="<?php echo $status ?>"></span>
                                                <?php if($status == 'pending'): ?>
                                                    <div class="finishtest-show button-hack-mobile">
                                                        <div class="test-accept-wrap">
                                                            <?php echo $button_html ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    <?php if($c_objects['show_load_button']): ?>
                                            <li><a href="javascript:void(0)" id="load_more_button_<?php echo $course_id ?>" onclick="loadMoreLectures('<?php echo $course_id ?>')" class="btn btn-green">Load More</a></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- dashbord-right -->

        </div>
        <!-- dashbord-wrap -->


        <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/circle-progress.js"></script>
        <script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
        <script>
            var __windowWidth, __miniView = false, __mobileView = false, __overviewTab = true, __overviewTabDiv, __detailsTab;
            function refreshPageView()
            {
                __windowWidth = $(window).width();
                __mobileView = false;
            
                if(__windowWidth <= 991)
                {
                    $('.test-level-card-ul').removeClass('dimension-change');
                    __miniView = true;
                    __overviewTab = !__overviewTabDiv.hasClass('dashbord-center-toggle');
                    if(__windowWidth <= 400)
                    {
                        __mobileView = true;
                    }
                }
                else
                {
                    $('.test-level-card-ul').addClass('dimension-change');
                    __miniView = false;
                    __overviewTab = __overviewTabDiv.is(':visible');
                }
                
                if(__miniView == true)
                {
                    __overviewTabDiv.removeClass("dashbord-center-toggle");
                    __detailsTab.removeClass("dashbord-right-toggle");
                    if(__overviewTab == true)
                    {
                        __overviewTabDiv.show();
                        __detailsTab.hide();
                    }
                    else
                    {
                        __overviewTabDiv.hide();
                        __detailsTab.show();                    
                    }
                    $('.nav-tabs-test li').removeAttr('style');
                    if(__mobileView == true)
                    {
                        $('.testlevel-details').hide();                    
                        $('.test-level-card-ul li').addClass('card-reveal');
                    }
                    else
                    {
                        $('.testlevel-details').removeAttr('style');      
                        $('.current_test_status[data-status="pending"]').parent('li').removeClass('card-reveal');
                    }
                }
                else
                {
                    $('.current_test_status[data-status="pending"]').parent('li').removeClass('card-reveal');
                    $('.tab-contentMin-height').show();
                    $('.testlevel-details').show();                    
                    if(__overviewTab == true)
                    {      
                        __overviewTabDiv.show();
                        __detailsTab.show();
                        $('.test-level-card-ul').removeClass('dimension-change');
                        $('.nav-tabs-test li:gt(2)').hide();
                        $('.nav-tabs-test li:first-child a').trigger('click');
                    }
                    else
                    {
                        $('.test-level-card-ul').addClass('dimension-change');
                        __overviewTabDiv.addClass('dashbord-center-toggle');
                        __detailsTab.addClass('dashbord-right-toggle');
                        $('.nav-tabs-test li').removeAttr('style');
                    }
                }
            }

            $(document).ready(function () {
                __overviewTabDiv = $('.dashbord-center'), __detailsTab = $('.dashbord-right');
                //navbar
                $(".nav-left-ul li").on("click", function () {
                    $(".nav-left-ul li").removeClass("active-dashbord");
                    $(this).addClass("active-dashbord");
                });
                
                //notification bar
                $(".dpdropbtn").on("click", function () {
                    $(this).next().stop().slideToggle("fast");
                    return false;
                });
                $(document).on("click", function () {
                    $(".dpdropbtn").next().stop().slideUp("fast");
                });
                $(".neetrefresh-drops").on("click", function (e) {
                    e.stopPropagation();
                });

                //percentage circle
                $('#circle').circleProgress({
                    value: <?php echo ($scores['overall_score']['overal_score'])?($scores['overall_score']['overal_score']/100):0 ?>,
                    size: 130.0,
                    startAngle: 30,
                    lineCap: 'butt',
                    fill: {
                        gradient: ['#a0d5e9', '#93bff8']
                    },
                }).on('circle-animation-progress', function (event, progress, value) {
                    $(this).find('span').html(Math.round(100 * value) + '%');
                });
                
                initPercentage();
                
                /*$('#circle_test_2').circleProgress({
                    value: 0,
                    size: 55.0,
                    startAngle: 30,
                    lineCap: 'butt',
                    fill: {
                        color: '#ffa500'
                    },
                    emptyFill: "rgba(0, 0, 0, 0.2)",
                })*/
                
                
                //expand button
                $(".expand-btn, #overview_tab").on("click", function () {
                    if(__miniView == true)
                    {
                        __overviewTabDiv.toggle();
                        __detailsTab.toggle();                    
                        __overviewTab = __overviewTabDiv.is(':visible');
                        $('#back-button').unbind('click');
                        $('#back-button').click(function(){
                            $('#overview_tab').trigger('click')
                        });
                    }
                    else
                    {
                        __overviewTabDiv.toggleClass("dashbord-center-toggle");
                        __detailsTab.toggleClass("dashbord-right-toggle");
                        $(".userloginSecond").stop().fadeToggle(700);
                        $(".userlogin-first").stop().fadeToggle(700);                                                
                        __overviewTab = !__overviewTabDiv.hasClass('dashbord-center-toggle');
                        if(__overviewTab == true)
                        {
                            __overviewTabDiv.removeAttr('style');
                            $(".test-level-card-ul").removeClass("dimension-change");    
                            $('.nav-tabs-test li:gt(2)').hide();
                            $('.nav-tabs-test li:first-child a').trigger('click');
                        }
                        else
                        {
                            $(".test-level-card-ul").addClass("dimension-change");    
                            $('.nav-tabs-test li').removeAttr('style');
                        }
                    }
                });
                
                $(window).resize(function(){
                    refreshPageView();
                });
                refreshPageView();
                /*if(__miniView != true)
                {
                    if(window.location.hash == "#my_tests"){
                        __overviewTabDiv.addClass("dashbord-center-toggle");
                        __detailsTab.addClass("dashbord-right-toggle");
                        $(".userloginSecond").show();
                        $(".userlogin-first").hide();                                               
                    }
                }*/
            });
            
            $(document).on('click', '.nav-tabs-test li', function(){
                if(__miniView == true)
                {
                    $('.nav-tabs-test li').hide();
                    $('.my-overview-title').text($(this).find('a').text());
                    $('.tab-contentMin-height').show();
                    $('#back-button').show();
                    $('#back-button').unbind('click');
                    $('#back-button').click(function(){
                        loadSections();
                    });
                }
            });
            var __testName = atob('<?php echo base64_encode($plan['p_name']) ?>');
            $(document).on('click', '#back-button', function(){
                loadSections();
            });
            
            function loadSections()
            {
                $('.nav-tabs-test li').show();
                $('.my-overview-title').text(__testName);
                $('.tab-contentMin-height').hide();
                
                $('#back-button').unbind('click');
                $('#back-button').click(function(){
                    $('#overview_tab').trigger('click')
                });
            }
            
            var __site_url = '<?php echo site_url() ?>';
            var __offset   = 2;
            var __requestInProgress = false;
            function loadMoreLectures(course_id)
            {
                $('#load_more_button_'+course_id).html('Loading..');
                if(__requestInProgress == true)
                {
                    return false;
                }
                __requestInProgress = true;
                $.ajax({
                    url: __site_url+'material/tests_json',
                    type: "POST",
                    data:{"is_ajax":true,'course_id':course_id, 'offset':__offset},
                    success: function(response) {
                        
                        var data = $.parseJSON(response);
                        //console.log(data);
                        $('#load_more_button_'+course_id).parent().remove();
                        if(data['error'] == false)
                        {
                            if(Object.keys(data['test_objects']).length > 0 )
                            {
                                __offset++;
                                if(__offset == 2)
                                {
                                    $('#course_tab_'+course_id+' ul').html(renderTestHtml(data['test_objects']));
                                }
                                else
                                {
                                    //console.log(data['test_objects']);
                                    $('#course_tab_'+course_id+' ul').append(renderTestHtml(data['test_objects']));
                                }
                                if(data['show_load_button'] == true)
                                {
                                    $('#course_tab_'+course_id).append('<li><a href="javascript:void(0)" onclick="loadMoreLectures(\''+course_id+'\')" class="btn btn-green">Load More</a></li>');
                                }
                                initPercentage();
                            }
                        }
                        else
                        {
                            alert(data["message"]);
                        }
                        __requestInProgress = false;
                    }
                });
            }
            
            var __assets_url = '<?php echo assets_url() ?>';
            var __theme = '<?php echo $this->config->item('theme') ?>';
            function renderTestHtml(tests)
            { 
                var testHtml  = '';
                //console.log(tests['lectures']['section']);
                if(Object.keys(tests['lectures']['section']).length > 0 )
                {
                    $.each(tests['lectures']['section'], function(testKey, test )
                    {
                        //console.log(test['sec_lectures']);
                        testHtml += renderLectureHTML(test['sec_lectures'],tests['plan_lectures']);
                    });
                }
                return testHtml;
            }

            function renderLectureHTML(lecture_array, plan_lectures){
                var testsHtml  = '';
                //console.log(lecture_array);
                if(Object.keys(lecture_array).length > 0 )
                {
                    $.each(lecture_array, function(testKey, test )
                    {
                        //console.log(test);
                        var under_plan = inArray(test['id'], plan_lectures);
                            testsHtml += '<li class="card-reveal">';
                            testsHtml += '  <div class="test-lvel-icon-wrap '+((under_plan==false)?'test-level-disbaled':'')+'">';
                        var completed   = false;
                        var icon_html   = '';
                        var report_html = '';
                        var report_button_html = '';
                            if(test['attempt_hisory'] != null)
                            {
                                if(test['attempt_hisory']['attempted_questions'] >= test['assesment']['a_questions'])
                                {
                                    icon_html = '<img src="'+__assets_url+'themes/'+__theme+'/neet/assets/images/test completed.png" alt="test completed.png">';
                                    report_html += '<div class="finishtest-show">';
                                    report_html += '    <h3 class="wldyoulike">Would you like to restart your finished test?</h3>';
                                    report_html += '    <div class="test-accept-wrap">';
                                    report_html += '        <a id="view_result"  name="VIEW RESULT"   href="'+__site_url+'material/assesment_report_item/'+test['attempt_hisory']['attempt_id']+'">VIEW RESULT</a>';
                                    report_html += '        <a id="restart_test" name="RESTART TEST"  href="'+__site_url+'material/test/'+test['assesment']['assesment_id']+'">RESTART TEST</a>';
                                    report_html += '    </div>';
                                    report_html += '</div>';
                                    //report_button_html = '<a href="'+__site_url+'material/assesment_report_item/'+test['attempt_hisory']['attempt_id']+'" class="btn btn-green">Result</a>';
                                    report_button_html = '<a href="javascript:void(0)" class="btn btn-green">Result</a>';
                                }
                                else 
                                {
                                    var percentage = 0;
                                    if(test['attempt_hisory']['attempted_questions'] && test['assesment']['a_questions'])
                                    {
                                        percentage = ((test['attempt_hisory']['attempted_questions']/test['assesment']['a_questions']));                                            
                                    }
                                    icon_html += '<div>';
                                    icon_html += '    <div data-percentage="'+(percentage)+'" class="donutmaster-2 test-percetage-custom">';
                                    icon_html += '        <span class="chart-percentage-show"></span>';
                                    icon_html += '    </div>';
                                    icon_html += '</div>';
                                    report_button_html = '<a href="'+__site_url+'material/test/'+test['id']+'/'+test['attempt_hisory']['attempt_id']+'" class="btn btn-blue">Restart</a>';
                                }
                            }
                            else 
                            {
                                report_button_html = '<a href="'+__site_url+'material/test/'+test['id']+'" class="btn btn-green">Attend</a>';
                                icon_html += '<div>';
                                icon_html += '    <div data-percentage="0" class="donutmaster-2 test-percetage-custom">';
                                icon_html += '        <span class="chart-percentage-show"></span>';
                                icon_html += '    </div>';
                                icon_html += '</div>';
                            }    
                            testsHtml += icon_html;
                            testsHtml += '<div class="test-level-head-wrap">';
                            testsHtml += '        <h3 class="test-level-title">'+test['cl_lecture_name']+'</h3>';
                            testsHtml += '        <div class="test-level-quest-marks">';
                            testsHtml += '            <div class="ques-test-wrap"><span class="blue-text">'+test['assesment']['a_questions']+'</span> Questions</div>';
                            testsHtml += '            <div class="quest-amswer-time"><img src="'+__assets_url+'themes/'+__theme+'/neet/assets/images/'+((under_plan == true)?'time icon.png':'timeicon-grey.png')+'"><span class="red-text">'+Math.round(test['assesment']['a_duration']/60)+'</span>&nbsp; Min</div>';
                            testsHtml += '        </div>';
                            testsHtml += '    </div>';
                            testsHtml += '</div>';
                            testsHtml += '<div class="testlevel-details">';
                            if(under_plan == true)
                            {
                                testsHtml += report_button_html;
                            }
                            else
                            {
                                testsHtml += '    <a href="'+__site_url+'dashboard/plans/'+btoa(test['id'])+'" class="btn btn-gold">Join Pro</a>';
                            }
                            testsHtml += '</div>';
                            if(under_plan == true)
                            {
                                testsHtml += report_html;
                            }
                            testsHtml += '</li>';
                    });
                }
                return testsHtml;
            }
            
            function filterTest(course_id, filter)
            {
                $('#course_tab_'+course_id+' .current_test_status').parent().hide()
                var filterType = '';
                var filterHtml = '';
                switch(filter)
                {
                    case "all":
                        $('#course_tab_'+course_id+' .current_test_status').parent().show();
                        filterType = 'All Test';
                        filterHtml += '<span class="btn-grey-round round grey-bg"></span>'+filterType;
                        break;
                    case "pending":
                        $('#course_tab_'+course_id+' .current_test_status[data-status="'+filter+'"]').parent().show();
                        filterType = 'Pending';
                        filterHtml += '<span class="btn-blue-round round blue-bg"></span>'+filterType;
                        break;
                    case "pro":
                        $('#course_tab_'+course_id+' .current_test_status[data-status="'+filter+'"]').parent().show();
                        filterType = 'Pro';
                        filterHtml += '<span class="btn-gold-round round bg-gold"></span>'+filterType;
                        break;
                    case "completed":
                        $('#course_tab_'+course_id+' .current_test_status[data-status="'+filter+'"]').parent().show();
                        filterType = 'Completed';
                        filterHtml += '<span class="btn-green-round round bg-green "></span>'+filterType;
                        break;
                }
                    filterHtml += '<svg fill="#949494" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">';
                    filterHtml += '<path d="M7.41 7.84L12 12.42l4.59-4.58L18 9.25l-6 6-6-6z"/>';
                    filterHtml += '<path d="M0-.75h24v24H0z" fill="none"/>';
                    filterHtml += '</svg>';
                    $('#filter_dropdown_'+course_id).html(filterHtml);
            }
            
            function initPercentage()
            {
                $( ".test-percetage-custom" ).each(function( index ) {
                    $(this).circleProgress({
                        value: $(this).attr('data-percentage'),
                        size: 55.0,
                        startAngle: 30,
                        lineCap: 'butt',
                        fill: {
                            color: '#fe8f8b'
                        },
                        emptyFill: "rgba(0, 0, 0, 0.5)",
                    }).on('circle-animation-progress', function (event, progress, value) {
                        $(this).find('span').html(Math.round(100 * value) + '%');
                    });
                });
            }
            
        
        </script>
        <?php include_once 'system.js.php'; ?>
    </body>

</html>