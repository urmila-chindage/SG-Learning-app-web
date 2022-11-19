<?php include 'header_beta_new.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<script type="text/javascript">
    var challenge_details     = '<?php echo base64_encode($challenge_details) ?>';

    //var challenge_details_arr = JSON.parse(challenge_details.replace(/ 0+(?![\. }])/g, ' '));
    var challenge_details_arr = $.parseJSON(atob(challenge_details));
    //console.log(challenge_details_arr);
</script>
<style>
    #challenge{
        overflow: auto;
    }
</style>
<script type="text/javascript">
    var variable      = '<?php echo count($wishlist_courses)?>';
    <?php 
    $rates= array();
    for($i=0;$i<count($wishlist_courses);$i++){
        $ratting = $wishlist_courses[$i]["ratting"];
        array_push($rates,$ratting);
    }
    ?>
    var rateContent = new Array();
    <?php foreach($rates as $key => $val){ ?>
        rateContent.push('<?php echo $val; ?>');
    <?php } ?>
</script>
<div class="grey_main">
    <div class="wrapper">
        <div class="sction_1">
            <div class="row">
                <div class="col-xs-12">
                    <div class="sction_div">
                        <ul class="nav nav-tabs responsive" id="myTab">
                            <li class="active"><a href="#courses"><?php echo lang('my_courses');?></a></li>
                            <?php /* ?><li><a href="#teachers"><?php echo lang('my_teachers');?></a></li><?php  */?>
                            <li><a href="#result"><?php echo lang('result');?></a></li>
                            <li><a href="#friends"><?php echo lang('friends');?></a></li>
                            <li><a href="#wishlist"><?php echo lang('wishlist');?></a></li>
                            <li><a href="#setting"><?php echo lang('setting');?></a></li>
                            <li id="my_profile"><a href="#profile"><?php echo lang('my_profile');?></a></li>
                            <li id="my_profile"><a href="#challenge"><?php echo 'Challenge Zone';?></a></li>
                        </ul>
                        <div class="tab-content responsive">
                            <div class="tab-pane active" id="courses">
                                <div class="row">
                                	<div class="col-xs-12">
                                	   <h2 class="tab_hd"><?php echo lang('subsrcibed_courses'); ?></h2>
                                	</div>
                                </div>
                                <div class="row">
                                <?php foreach($course_details as $course){ ?>
                                <?php 
                                    $image_first_name   = substr($course['cb_image'],0,-4);
                                    $image_dimension    = '_300x160.jpg';
                                    $image_new_name     = $image_first_name.$image_dimension;
                                ?>
                                	<div class="col-xs-6 col-sm-6 col-md-3">
                                    	<div class="tab_box2">
                                        	<div class="img_hover"><img class="img-responsive" src="<?php echo (($course['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course['id']))).$image_new_name; ?>" alt="img"> <span><a href="<?php echo site_url().'/materials/course/'.$course['id']; ?>">Go to course</a></span></div>
                                        	<h2><?php echo $course['cb_title']; ?></h2>
                                        	<h3>By <?php echo (empty($course['assigned_tutors']))?$admin:$course['assigned_tutors']; ?></h3>
                                                
                                                <?php if($course['cs_approved'] == 0){?>
                                                    <div class="my-course-status  yellowtext"><?php echo lang('suspended');?></div>
                                                <?php }else { ?>
                                                    <?php if(empty($course['percentage']) && $course['percentage'] == 0){ ?>
                                                        <div class="my-course-status"><?php echo lang('not_yet_started');?></div>
                                                    <?php }elseif($course['percentage'] > 95){?>
                                                        <div class="my-course-status  greentext"><?php echo lang('completed');?></div>
                                                    <?php }else{ ?>
                                                        <div class="progress_main">
                                                            <div class="progress">
                                                               <div class="progress-bar" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo intval($course['percentage']); ?>%"></div>
                                                            </div>
                                                            <span class="sr-only"><?php echo intval($course['percentage']); ?>% Complete</span>
                                                        </div>
                                                        <?php /* ?><div class="lectures">
                                                            <div class="left"><?php echo intval($course['lecture_completion_count']) ?> out of <?php echo intval($course['lecture_count']) ?> Lectures</div>
                                                            <div class="right"><?php echo intval($course['assessment_completion_count']) ?> out of <?php echo intval($course['assessment_count']) ?> tests</div>
                                                        </div><?php */ ?>
                                                    <?php }
                                                } ?>
                                                        <?php   
                                                                    $expire = date_diff(date_create($today),date_create($course['cs_end_date'])); 
                                                                    $expire_in = $expire->format("%R%a");
                                                                    $expire_in_days = $expire->format("%a");
                                                                    $end_date = strtotime($course['cs_end_date']);
                                                                    $validity_format_date = date('d-m-Y',$end_date);
                                                                    
                                                                    $expire_lang = ($expire_in == 0 || $expire_in < 0)?'expired':'expire_in';
                                                                    $expire_class = ($expire_in == 0 || $expire_in < 0)?'redtext':'greentext';
                                                                    $expire_days = ($expire_in_days > 1)?lang('expire_days'):'day';
                                                                ?>
                                                            <div class="my-course-status <?php echo $expire_class ?> smallmob"> <?php echo lang($expire_lang) ?> <?php if($expire_in > 0){ echo $expire_in_days." ".$expire_days;} ?>
                                                            </div>
                                    	</div>
                                	</div>
                                <?php } ?>
                                </div>
                            </div>
                            <div class="tab-pane" id="teachers">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('explore_teacher');?></h2>
                                    </div>
                                </div>
                                Will be updated soon...
                            </div>
                            <div class="tab-pane" id="result">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('result');?></h2>
                                    </div>
                                    <?php if ((!empty($user_attempted_challenge_zone)) || (!empty($user_attempted_assessment))) { ?>
                                    <?php //echo "<pre>"; print_r($user_attempted_assessment); 
                                    if (!empty($user_attempted_assessment)): ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('assesment_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_assessment)?$total_attempted_assessment:'0'; ?> <?php echo lang('assesments');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_attempted_assessment as $attempted_assessment): 
                                                    $duration_in_minutes     = $attempted_assessment['aa_duration']/60;
                                                    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4);
                                                ?>
                                                <div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <?php /* ?><a href="<?php echo site_url().'/dashboard/assessment_details/'.$attempted_assessment['aa_assessment_id'].'/'.$session['id'].'/'.$attempted_assessment['attempted_id']; ?>"><?php */ ?>
                                                            <a href="<?php echo site_url().'/material/assesment_report_item/'.$attempted_assessment['attempted_id']; ?>">
                                                            <?php echo $attempted_assessment['cb_title'].' -  '.$attempted_assessment['cl_lecture_name']?>
                                                            </a>

                                                        </div>
                                                        <?php $format_date = strtotime($attempted_assessment['aa_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($attempted_assessment['correct'])?$attempted_assessment['correct']:'0' ?> Correct <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($attempted_assessment['incorrect'])?$attempted_assessment['incorrect']:'0' ?> Wrong <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($attempted_assessment['count_not_tried'])?$attempted_assessment['count_not_tried']:'0' ?> Not tried <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($attempted_assessment['success_percent'])?round($attempted_assessment['success_percent']):'0' ?>% Success <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $cut_duration_in_minutes; ?> minutes <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                
                                    <?php if (!empty($user_attempted_challenge_zone)): ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('challenge_zone_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_challenge_zone)?$total_attempted_challenge_zone:'0'; ?> <?php echo lang('challenge_zone');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_attempted_challenge_zone as $attempted_challenge_zone): ?>
                                               <?php $duration_in_minutes     = $attempted_challenge_zone['cza_duration']/60;
                                                    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4); ?>
						<div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <a href="<?php echo site_url().'/material/challenge_zone_report_item/'.$attempted_challenge_zone['challenge_zone_attempt_id']; ?>">
                                                            <?php echo $attempted_challenge_zone['cz_title']?>
                                                            </a>

                                                        </div>
                                                        <?php $format_date = strtotime($attempted_challenge_zone['cza_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($attempted_challenge_zone['cz_correct'])?$attempted_challenge_zone['cz_correct']:'0' ?> <?php echo lang('correct');?> <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($attempted_challenge_zone['cz_incorrect'])?$attempted_challenge_zone['cz_incorrect']:'0' ?> <?php echo lang('wrong');?> <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($attempted_challenge_zone['cz_count_not_tried'])?$attempted_challenge_zone['cz_count_not_tried']:'0' ?> <?php echo lang('not_tried');?> <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($attempted_challenge_zone['cz_success_percent'])?round($attempted_challenge_zone['cz_success_percent']):'0' ?>% <?php echo lang('success');?> <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $cut_duration_in_minutes ?> <?php echo lang('minutes');?> <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php } else { ?>
                                        <?php //echo lang('no_results'); ?>
                                    <?php } ?>

                                    <?php if(!empty($user_generated_test)){ ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('user_generated_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_user_generated_test)?$total_attempted_user_generated_test:'0'; ?> <?php echo lang('user_generated');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_generated_test as $user_test){ ?>
						 <?php $duration_in_minutes     = $user_test['uga_duration']/60;
                                                    $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4); ?>
                                                <div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <a href="<?php echo site_url().'/material/user_generated_test_report_item/'.$user_test['attempted_id']; ?>">
                                                            <?php echo $user_test['uga_title']?>
                                                            </a>

                                                        </div>
                                                        <?php $format_date = strtotime($user_test['uga_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($user_test['correct'])?$user_test['correct']:'0' ?> <?php echo lang('correct');?> <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($user_test['incorrect'])?$user_test['incorrect']:'0' ?> <?php echo lang('wrong');?> <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($user_test['count_not_tried'])?$user_test['count_not_tried']:'0' ?> <?php echo lang('not_tried');?> <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($user_test['percentage'])?round($user_test['percentage']):'0' ?>% <?php echo lang('success');?> <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $cut_duration_in_minutes ?> <?php echo lang('minutes');?> <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else { ?>
                                        <?php //echo lang('no_results');?>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="tab-pane" id="friends">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('friends');?></h2>
                                    </div>
                                </div>
                                Will be updated soon...
                            </div>
                            <div class="tab-pane" id="wishlist">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('wishlist');?></h2>
                                    </div>
                                </div>


                                <div class="row">
                                  <!-- Modal pop up contents:: Delete Section popup-->
                                    <div class="modal fade alert-modal-new" data-backdrop="static" data-keyboard="false" id="remove-wishlist" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-small" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <b id="confirm_box_title"></b>
                                                        <p class="m0" id="confirm_box_content"><?php echo lang('remove_wishlist');?></p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-red" data-dismiss="modal"><?php echo lang('cancel') ?></button>
                                                    <button type="button" class="btn btn-green" id="confirm_box_ok" ><?php echo lang('continue') ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php //$admin = "OLP"; 
                                $var = 0; foreach($wishlist_courses as $wishlist){ ?>
                                <?php 
                                    $image_first_name   = substr($wishlist['wished_courses']['cb_image'],0,-4);
                                    $image_dimension    = '_300x160.jpg';
                                    $image_new_name     = $image_first_name.$image_dimension;
                                ?>
                                <a href="<?php echo site_url().'/'.$wishlist['wished_courses']['cb_slug']; ?>" >
                                    <div class="col-xs-6 col-sm-6 col-sm-3" id="wishlist_<?php echo $wishlist['cw_course_id'] ?>">
                                        <span href="javascript:void(0);" id="delete_btn_<?php echo $wishlist['cw_course_id'] ?>" onclick="removeCourse('<?php echo $wishlist['cw_course_id'] ?>','<?php echo $session['id']; ?>','<?php echo $wishlist['wished_courses']['cb_title']; ?>')" data-toggle="modal"  data-target="#remove-wishlist">
                                            <i class="demo-icon icon-trash"></i>
                                        </span>
                                	    <span>
                                            <img src="<?php echo (($wishlist['wished_courses']['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $wishlist['cw_course_id']))).$image_new_name; ?>" class="img-responsive img" alt="img" />
                                        </span>
                                        <div class="explore">
                                            <h3><?php echo $wishlist['wished_courses']['cb_title']; ?></h3>
                                            <h4><?php echo lang('by') ?> 
                                           <?php //foreach($course_details as $course){
                                               echo (empty($course['assigned_tutors']))?$admin:$course['assigned_tutors']; 
                                            //}?></h4>
                                            <div id="rateYo_<?php echo $var; ?>" > </div>
                                            <span><?php if($wishlist_courses[$var]["ratting"]!='0') { ?>(<?php echo $wishlist_courses[$var]["ratting"]; ?>) <?php } ?></span>
                                                
                                            <div class="price">
                                                <div class="left"><?php  echo ($wishlist['wished_courses']['cb_is_free'] == '1')?'FREE':(($wishlist['wished_courses']['cb_discount'] != '0')?"RS. ".$wishlist['wished_courses']['cb_discount']:"RS. ".$wishlist['wished_courses']['cb_price']); ?>
                                                    
                                                </div>
                                                <div class="right"><?php echo ($wishlist['wished_courses']['cb_is_free'] == '1')?'':(($wishlist['wished_courses']['cb_discount'] != '0')?"RS. ".$wishlist['wished_courses']['cb_price']:''); ?>
                                                </div>
                                            </div>
                                	    </div>
                                    </div>
                                </a>
                                <?php  $var++; } ?>
                                </div>
                            </div>

                            <div class="tab-pane" id="setting">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('setting') ?></h2>
                                    </div>
                                </div>
                                Will be updated soon...
                            </div>
                            <div class="tab-pane" id="profile">
                                <div class="row whitebg profilesection">
                                    <div class="col-xs-9 col-sm-8 col-md-8">
                                        <span class="pull-left spr"><img src="<?php echo (($user_details['us_image'] == 'default.jpg')?default_user_path():user_path()).$user_details['us_image']; ?>" class="img-circle profilepic"></span>
                                        <span class="pull-left tpt">
                                            <h4 class="greytext"><span class="overflow center-block user_name"><?php echo $user_details['us_name'] ?></span> 
                                                <div class="dropdown pull-right spl">
                                                    <button class="transparentbutton dropdown-toggle" type="button" data-toggle="dropdown">
                                                        <i class="icon-angle-down"></i>
                                                    </button>
                                                    <ul class="dropdown-menu usertoggle">
                                                        <li><a href="javascript:void(0);" data-target="#change_password" data-toggle="modal">Change Password</a></li>
                                                        <li><a href="javascript:void(0);" data-target="#edit-profile" data-toggle="modal">Edit Profile</a></li>
                                                    </ul>
                                                </div>
                                            </h4>
                                            <span class="student-title"><?php echo lang('student');?></span>
                                        </span>
                                        <div id="succ_msg" class="succ-msg"></div>
                                    </div>
                                    <!--<div class="col-xs-3 col-sm-4 col-md-4"><span class="orangebigtext pull-right">2999 Credits</span></div>-->
                                </div>
                                <div class="row offbg">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <ul class="scrollto_section">
                                            <li><a href="#about_section"><?php echo lang('about')?></a></li>
                                            <?php if (!empty($user_course_enrolled)): ?><li><a href="#courses_section">Courses</a></li><?php endif; ?>
                                            <?php if (!empty($user_attempted_assessment)): ?><li><a href="#assessment_section">Assessment results</a></li><?php endif;?>
                                            <?php if (!empty($user_attempted_challenge_zone)): ?><li><a href="#challenge_section">Challenge Zone results</a></li><?php endif;?>
                                            <?php if (!empty($user_generated_test)): ?><li><a href="#user_generated_test_section">User Generated Test results</a></li><?php endif;?>
                                            
                                        </ul>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="panel panel-default whitepanel" id="about_section">
                                            <div class="panel-heading"><h4 class="greytext"><?php echo lang('about')?></h4></div>
                                            <div class="panel-body greytextsmall spacerrow">
                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo lang('email')?>:</div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo $user_details['us_email'] ?></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo lang('phone')?>:</div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo $user_details['us_phone'] ?></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo lang('lectures_completed')?>:</div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"></div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6"><?php echo lang('tests_completed')?>:</div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6"></div>
                                                </div>
                                                <!--<div class="row">
                                                    <div class="col-xs-6 col-sm-6 col-md-6">Leader board position:</div>
                                                    <div class="col-xs-6 col-sm-6 col-md-6">213</div>
                                                </div>-->                                                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6">
                                        <div class="panel panel-default whitepanel">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('bio')?></h4>
                                            </div>
                                            <div class="panel-body">
                                                <?php echo $user_details['us_about']; ?>    
                                            </div>
                                        </div>
                                    </div>

<?php if(!empty($profile_blocks)): ?>
    <?php foreach($profile_blocks as $block): ?>
        <div class="col-xs-12 col-sm-6 col-md-6" id="block_row_<?php echo $block['id'] ?>">
            <div class="panel panel-default whitepanel">
                <div class="panel-heading"><h4 class="greytext"><?php echo $block['pb_name'] ?><i class="demo-icon pull-right field_edit_icon icon-pencil" onclick="editBlock('<?php echo $block['id'] ?>')"></i></h4></div>
                <div class="panel-body greytextsmall spacerrow">
                    <?php if(!empty($block['profile_fields'])): ?>
                        <?php $field_ids = array(); $display_html = '';?>
                            
                                <?php foreach($block['profile_fields'] as $field): ?>
                                    <?php 
                                        $field_ids[]    = array('id' => $field['id'], 'field_mandatory' => $field['pf_mandatory'], 'field_name' => $field['pf_name']);
                                        $field_label    = $field['pf_label'].(($field['pf_mandatory'])?' * ':'');
                                        $field_value    = isset($user_profile_fields[$field['id']])?$user_profile_fields[$field['id']]:$field['pf_default_value'];
                                        $display_html  .= '<div class="row display_wrapper_'.$block['id'].'"> <div class="col-xs-6 col-sm-6 col-md-6" id="field_label_'.$field['id'].'">'.$field['pf_label'].':</div><div class="col-xs-6 col-sm-6 col-md-6" id="field_value_'.$field['id'].'">'.$field_value.'</div></div>'; 
                                    ?>
                                    <div class="form-group hide form_wrapper_<?php echo $block['id'] ?>">
                                        <div class="col-sm-12">
                                            <?php echo $field_label ?>: 
                                            <input type="text" class="form-control keyword_for_auto_value" data-pf-id="<?php echo $field['id'] ?>" onKeyup="getAutoFieldsValue(event)" id="<?php echo $field['pf_name'] ?>" auto-suggestion-status="<?php echo $field['pf_auto_suggestion'] ?>" name="<?php echo $field['pf_name'] ?>" value="<?php echo $field_value ?>" placeholder="<?php echo $field['pf_placeholder'] ?>" >
                                            <ul id ="fieldListId-<?php echo $field['pf_name'] ?>" class="field_values_list" style="display:none;list-style-type: none;">
                                            </ul>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                <?php endforeach; ?>
                                <div class="form-group hide form_wrapper_<?php echo $block['id'] ?> ">
                                    <div class="col-sm-12">
                                        <input class="btn btn-green" value="SAVE" id="save_block_<?php echo $block['id'] ?>" onclick="saveBLock('<?php echo base64_encode(json_encode($field_ids)); ?>', this.id)" type="button"><ripples></ripples>
                                        <input class="btn btn-red" value="CANCEL" id="cancel_block_<?php echo $block['id'] ?>" onclick="cancelEdit('<?php echo $block['id'] ?>')" type="button"><ripples></ripples>
                                    </div>
                                </div>
                                <?php echo ($display_html)?$display_html:''; ?>
                        <?php endif;?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
                                    
                                    <?php if (!empty($user_course_enrolled)): ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="courses_section">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('courses');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_enrolled_course)?$total_enrolled_course:'0' ?> Courses</span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                
                                                    <?php foreach ($user_course_enrolled as $user_course): ?>
                                                        <div class="row">
                                                            
                                                                <div class="col-xs-5 col-sm-5 col-md-5 overflow smallmob">
                                                                    <i class="bigicon icon-graduation-cap"></i><a href="<?php echo site_url('/').$user_course['cb_slug'] ?>"><?php echo $user_course['cb_title'] ?></a>
                                                                </div>
                                                            
                                                            <?php if($user_course['cs_approved'] == 0){?>
                                                                <div class="col-xs-3 col-sm-3  yellowtext text-center smallmob"><?php echo lang('suspended');?></div>
                                                            <?php }else { ?>
                                                                <?php if(empty($user_course['percentage']) && $user_course['percentage'] == 0){ ?>
                                                                    <div class="col-xs-3 col-sm-3  text-center smallmob"><?php echo lang('not_yet_started');?></div>
                                                                <?php }elseif($user_course['percentage'] > 95){?>
                                                                    <div class="col-xs-3 col-sm-3  greentext text-center smallmob"><?php echo lang('completed');?></div>
                                                                <?php }else{ ?>
                                                                    <div class="col-xs-3 col-sm-3  text-center smallmob"><?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?>%
                                                                        <div class="progress">
                                                                            <div class="progress-bar" role="progressbar" aria-valuenow="<?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo isset($user_course['percentage'])?$user_course['percentage']:'0'?>%"></div>
                                                                        </div>
                                                                    </div>
                                                                <?php 
                                                                    }
                                                                } ?>
                                                                <?php   
                                                                    $expire = date_diff(date_create($today),date_create($user_course['cs_end_date'])); 
                                                                    $expire_in = $expire->format("%R%a");
                                                                    $expire_in_days = $expire->format("%a");
                                                                    $end_date = strtotime($user_course['cs_end_date']);
                                                                    $validity_format_date = date('d-m-Y',$end_date);
                                                                    
                                                                    $expire_lang = ($expire_in == 0 || $expire_in < 0)?'expired':'expire_in';
                                                                    $expire_class = ($expire_in == 0 || $expire_in < 0)?'redtext':'greentext';
                                                                    $expire_days = ($expire_in_days > 1)?lang('expire_days'):'day';
                                                                ?>
                                                            <div class="col-xs-4 col-sm-4 col-md-4 <?php echo $expire_class ?> text-right smallmob"> <?php echo lang($expire_lang) ?> <?php if($expire_in > 0){ echo $expire_in_days." ".$expire_days;} ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                
                                            </div>
                                        </div>
                                    </div> 
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user_attempted_assessment)): ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="assessment_section">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('assesment_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_assessment)?$total_attempted_assessment:'0'; ?> <?php echo lang('assesments');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_attempted_assessment as $attempted_assessment): ?>
                                                <div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <a href="<?php echo site_url().'/dashboard/assessment_details/'.$attempted_assessment['aa_assessment_id'].'/'.$session['id'].'/'.$attempted_assessment['attempted_id']; ?>">
                                                            <?php echo $attempted_assessment['cb_title'].' -  '.$attempted_assessment['cl_lecture_name']?>
                                                            </a>
                                                        </div>
                                                        <?php $format_date = strtotime($attempted_assessment['aa_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($attempted_assessment['correct'])?$attempted_assessment['correct']:'0' ?> Correct <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($attempted_assessment['incorrect'])?$attempted_assessment['incorrect']:'0' ?> Wrong <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($attempted_assessment['count_not_tried'])?$attempted_assessment['count_not_tried']:'0' ?> Not tried <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($attempted_assessment['success_percent'])?round($attempted_assessment['success_percent']):'0' ?>% Success <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $attempted_assessment['a_duration'] ?> minutes <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($user_attempted_challenge_zone)): ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="challenge_section">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('challenge_zone_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_challenge_zone)?$total_attempted_challenge_zone:'0'; ?> <?php echo lang('challenge_zone');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_attempted_challenge_zone as $attempted_challenge_zone): ?>
                                                <div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <a href="<?php echo site_url().'/challenge_report/details/'.$attempted_challenge_zone['cza_challenge_zone_id'].'/'.$session['id'].'/'.$attempted_challenge_zone['challenge_zone_attempt_id']; ?>">
                                                            <?php echo $attempted_challenge_zone['cz_title']?>
                                                            </a>
                                                        </div>
                                                        <?php $format_date = strtotime($attempted_challenge_zone['cza_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($attempted_challenge_zone['cz_correct'])?$attempted_challenge_zone['cz_correct']:'0' ?> <?php echo lang('correct');?> <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($attempted_challenge_zone['cz_incorrect'])?$attempted_challenge_zone['cz_incorrect']:'0' ?> <?php echo lang('wrong');?> <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($attempted_challenge_zone['cz_count_not_tried'])?$attempted_challenge_zone['cz_count_not_tried']:'0' ?> <?php echo lang('not_tried');?> <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($attempted_challenge_zone['cz_success_percent'])?round($attempted_challenge_zone['cz_success_percent']):'0' ?>% <?php echo lang('success');?> <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $attempted_challenge_zone['cz_duration'] ?> <?php echo lang('minutes');?> <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if(!empty($user_generated_test)){ ?>
                                    <div class="col-xs-12 col-sm-12 col-md-12"> 
                                        <div class="panel panel-default whitefullpanel" id="user_generated_test_section">
                                            <div class="panel-heading">
                                                <h4 class="greytext"><?php echo lang('user_generated_results');?></h4>
                                                <span class="orangebigtext righttext"><?php echo isset($total_attempted_user_generated_test)?$total_attempted_user_generated_test:'0'; ?> <?php echo lang('user_generated');?></span>
                                            </div>
                                            <div class="panel-body greytextsmall spacer_bottomline">
                                                <?php foreach ($user_generated_test as $user_test){ ?>
                                                <div class="row">
                                                    <div class="toprow">
                                                        <div class="col-xs-8 col-sm-9 col-md-9 overflow">
                                                            <i class="bigicon icon-beaker"></i>
                                                            <a href="<?php echo site_url().'/dashboard/user_generated_test_details/'.$user_test['assessment_id'].'/'.$session['id'].'/'.$user_test['attempted_id']; ?>">
                                                            <?php echo $user_test['uga_title']?>
                                                            </a>
                                                        </div>
                                                        <?php $format_date = strtotime($user_test['uga_attempted_date']);
                                                        $attempt_date = date("F d", $format_date);?>
                                                            <div class="col-xs-4 col-sm-3  text-right"><?php echo $attempt_date; ?></div>
                                                    </div>
                                                    <div class="downrow">
                                                        <div class="col-xs-12 col-sm-10 col-md-10">
                                                            <ul class="statusmenu"> 
                                                                <li class="greentext"><?php echo isset($user_test['correct'])?$user_test['correct']:'0' ?> <?php echo lang('correct');?> <i class="icon-ok hidden-xs"></i></li>
                                                                <li class="redtext"><?php echo isset($user_test['incorrect'])?$user_test['incorrect']:'0' ?> <?php echo lang('wrong');?> <i class="icon-cancel hidden-xs"></i></li>
                                                                <li><?php echo isset($user_test['count_not_tried'])?$user_test['count_not_tried']:'0' ?> <?php echo lang('not_tried');?> <i class="icon-attention-alt hidden-xs"></i></li>
                                                                <li class="bluetext"><?php echo isset($user_test['percentage'])?round($user_test['percentage']):'0' ?>% <?php echo lang('success');?> <i class="icon-chart-pie hidden-xs"></i></li>
                                                                <li class="orangetext"><?php echo $user_test['uga_duration'] ?> <?php echo lang('minutes');?> <i class="icon-clock hidden-xs"></i></li>
                                                            </ul>
                                                        </div>
                                                        <div class="col-sm-2 col-md-2 hidden-xs"></div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php }else { ?>
                                        <?php //echo lang('no_results');?>
                                    <?php } ?>



                                </div>
                            </div>
                            <div class="tab-pane" id="challenge">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <h2 class="tab_hd"><?php echo lang('challenge_zone');?></h2>
                                        
                                    </div>
                                </div>
                                <?php if($challenges_stat == 1){ ?>
                                <div class="challenge_zone">
                                    <div class="">
                                        <select id="challenge_zone" onchange="select_challenge(this)" class="challenge-select">

                                            <?php foreach($categories as $category){ ?>
                                                <?php if(count($category['challenges']) > 0){ ?>
                                                            <option value="<?php echo $category['id']; ?>"><?php echo $category['ct_name']; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <div id="challenges_user">

                                        </div>
                                    </div>
                                </div>
                                <?php }else{ ?>
                                <span>No challenges found.</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script>
 var site_url = '<?php echo site_url() ?>';
 var __timeOut = '';


            /*
            * To get the autofill values for the profile block dynamic fields
            * Created by : Neethu KP
            * Created at : 06/01/2017
            */
            function getAutoFieldsValue(e){
                clearTimeout(__timeOut);
                __timeOut = setTimeout(function(){
                    var AutosuggestionStatus   = $(e.target).attr('auto-suggestion-status');
                    $('.field_values_list').hide();
                    if(AutosuggestionStatus == 1){
                        var userKeyword    = $(e.target).val();
                        var fieldValueId   = $(e.target).attr('id');
                        var field_id       = $(e.target).attr('data-pf-id');
                        var fieldListId    = 'fieldListId-'+fieldValueId;
                        var fieldHtml      = '<li>Loading...</li>';
                        $('#'+fieldListId).html(fieldHtml).show();
                        var keyword        = userKeyword.toLowerCase();
                        $.ajax({
                            url: site_url+'/dashboard/get_fileds_value',
                            type: "POST",
                            data:{"is_ajax":true, "keyword":keyword, "field_id":field_id},
                            success: function(response) {
                                if(response){
                                    var data        = $.parseJSON(response);
                                    var fieldHtml    = '';
                                    $('#'+fieldListId).html(fieldHtml);
                                    if(data['field_values'].length > 0 )
                                    {
                                        for (var i=0; i<data['field_values'].length; i++)
                                        {
                                            fieldHtml += '<li id="'+data['field_values'][i]['upf_field_id']+'">'+data['field_values'][i]['upf_field_value']+'</li>' ;
                                        }
                                        $('#'+fieldListId).append(fieldHtml).show();

                                    }
                                }
                            }
                        });
                    }          
                }, 600);

                
            }

             /*
            * To place the selected value from the auto suggestion list
            * Created by : Neethu KP
            * Created at : 06/01/2017
            */   
            $(document).on('click' , '.field_values_list li' ,function(){

                var fieldText     = $(this).text();
                var fieldListId   = $(this).parent().attr('id');
                var fieldValueId  = fieldListId.split('-');
                $('#'+fieldValueId[1]).val(fieldText);
                $('.field_values_list').hide();

            })
            
</script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/dashboard.js'; ?>"></script>
<style>
.form-control.error-field {
    border: 1px solid #a40000;
}
.field_edit_icon{ cursor: pointer;}
</style>
<?php include 'footer.php'; ?>