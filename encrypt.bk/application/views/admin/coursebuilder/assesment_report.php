<?php _once(__DIR__ . '/../report_tab.php') ?>
<section class="content-wrap base-cont-top-nosidebar ">
	<div class="container-fluid nav-content nav-cntnt100">
        <div class="row">
            <div class="rTable content-nav-tbl">
                <div class="rTableRow">
                    <div class="rTableCell dropdown">
                        <?php if($is_id) { ?>
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name"> <?php echo $current_course; ?> <span class="caret"></span></a>
                        <?php } else { ?> 
                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name"> Select Course <span class="caret"></span></a>
                        <?php } ?> 
                            <ul class="dropdown-menu white assesment-report-dropdown">
                                <?php foreach($courses as $course){ ?>
                                    <li><a href="javascript:void(0)" onclick="select_course(<?php echo $course['id']; ?>, this)"><?php echo $course['cb_title']; ?></a></li>
                                <?php } ?>
                            </ul>
                    </div>
                    <div class="rTableCell dropdown">
                        <input type="hidden" id="hidden_lecture_id" value="<?php if($is_id) { echo $lecture_id; } ?>">
                        <?php if($is_id) { ?>
                            <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="test_name"> <?php echo $current_lecture; ?> <span class="caret"></span></a>
                        <?php } else { ?> 
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="test_name"> Select Test <span class="caret"></span></a>
                        <?php } ?>    
                            <ul class="dropdown-menu white test-select-dropdown" id="test_list">
                        <?php if($is_id) { ?>
                            <?php foreach($tests as $test){ ?>
                                <li><a href="javascript:void(0)" onclick="select_test(<?php echo $test['id']; ?>,this)"><?php echo $test['cl_lecture_name']; ?></a></li>
                            <?php } ?>
                        <?php } ?>     
                            </ul>
                    </div>
                    <div class="rTableCell" id="div_export">
                                    <!-- lecture-control start -->
                                    <a class="btn btn-green" id="export_results"> EXPORT </a>
                                    <!-- lecture-control end -->
                    </div>
                </div>
            </div>
        </div>
	</div>
    <div class="left-wrap col-sm-12 profile-wrap report-wrap">

        <div class="container-fluid">
        <div class="col-sm-12 marg-top10">
            <table class="rTable table-with-border width-100p" id="tblcontent">
                <?php if($is_id) { ?>
                <?php foreach($users as $user){ ?>
                    <tr class="rTableRow">
                        <td class="rTableCell">
                            <a href="<?php echo admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$user['id'].'/'.$user['attempt_id']; ?>">
                                <span class="icon-wrap-round img">
                                    <img src="<?php echo (($user['us_image'] == 'default.jpg')?default_user_path():  user_path()).$user['us_image']; ?>">
                                </span>
                                <span class="line-h36"><?php echo $user['us_name']; ?></span>
                            </a>
                        </td>
                        <td class="rTableCell">
                            <?php
                                $dt = new DateTime($user['aa_attempted_date']);
                                echo strtoupper($dt->format('M d Y'));
                            ?>
                        </td>
                        <?php if($user['q_type'] == 3){ ?>
                            <td class="rTableCell">
                                Explanatory question found, Need Manual evaluation
                            </td>
                            <td class="rTableCell"></td>
                            <td class="rTableCell"></td>
                            <td class="rTableCell"></td>
                            <td class="rTableCell"><a href="<?php echo admin_url().'coursebuilder/evaluate_assessment/'.$lecture_id.'/'.$user['id'].'/'.$user['attempt_id']; ?>" class="btn btn-green" > EVALUATE</a></td>
                        <?php }else{ 
                            $duration_in_minutes     = $user['aa_duration']/60;
                            $cut_duration_in_minutes = substr($duration_in_minutes, 0, 4);
                            ?>
                            <td class="rTableCell font-green"><?php if(isset($user['correct'])){ echo $user['correct'];}else{echo '0';} ?> Correct</td>
                            <td class="rTableCell font-red"><?php if(isset($user['incorrect'])){echo $user['incorrect'];}else{echo '0';} ?> Wrong</td>
                            <td class="rTableCell font-lgt-grey"><?php if(isset($user['count_not_tried'])){echo $user['count_not_tried'];}else{echo '0';} ?> Not Tried</td>
                            <td class="rTableCell font-green"><?php if(isset($user['success_percent'])){echo $user['success_percent'];}else{echo '0';} ?>% success</td>
                            <td class="rTableCell"><?php echo $cut_duration_in_minutes; ?> min</td>
                        <?php } ?>
                        
                    </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
        </div>
    </div>
</section>
<script type="text/javascript" src="<?php echo assets_url() ?>js/assessment_report.js"></script>
