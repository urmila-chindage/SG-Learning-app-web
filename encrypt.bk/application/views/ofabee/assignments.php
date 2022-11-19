<?php
include 'header.php';
$my_wishlist_categories = array()
?>
<section id="nav-group">
<?php include_once "dashboard_header.php"; ?>
</section>

<section>
    <div class="all-challenges">
        <div class="container container-altr">
            <div class="container-reduce-width">

                <div class="tab-content">


                    <div id="dashboard-my-assignments" class="tab-pane active">
                        <div class="row dash-row">    
                            <div class="col-sm-12">
                                <?php if (!empty($assignments)): ?>
    <?php foreach ($assignments as $assignment): ?>
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12">
                                                <h4 class="sd-course-title testreport-title"><span class="report-grad"><img src="<?php echo assets_url('themes/' . $this->config->item('theme')); ?>img/school-dark.svg"></span><?php echo $assignment['cb_title'] ?></h4>
                                                <ul class="list-group assignment-lists">
        <?php print_assignments($assignment['assignments']); ?>
                                                </ul>
                                            </div>                        
                                        </div>
                                    <?php endforeach; ?>
<?php else: ?>
                                    <div class="no-course-container">
                                        <img class="no-questions-svg" src="<?php echo assets_url('themes/' . $this->config->item('theme')); ?>img/no-results.svg">
                                        <span class="no-discussion no-content-text"><span>Oops! </span>No assignments to show.</span>
                                        <div class="text-center">
                                            <span class="noquestion-btn-wrap mobile-btn"><a href="javascript:void(0)" class="orange-flat-btn noquestion-btn">Attend Now</a></span>
                                        </div><!--text-center-->
                                    </div>
                                <?php endif; ?>
                                <?php

                                function print_assignments($assigs) {
                                    $count = 0;
                                    $theme = config_item('theme');
                                    ?>
                                    <?php foreach ($assigs as $assig):$count++; ?>
        <?php if ($assig['scored_mark'] == ''): ?>
                                            <!-- Not yet submitted -->
                                            <li class="list-group-item single-assignment">
                                                <span class="report-assignmenticon"><img src="<?php echo assets_url('themes/' . $theme); ?>img/asignment-report.svg"></span>
                                                <span class="assignment-slnmbr"><?php echo $count; ?></span>
                                                <span class="assignment-desc">
                                                    <p class="assignment-content"><?php echo $assig['a_name']; ?></p>
            <?php print_lastDate($assig['last_date']); ?>
                                                    <span class="assignment-time">Submitted Date: <span>Not yet submitted</span></span>
                                                </span>
                                                <span class="assignment-mark">-</span>
                                                <span class="report-action assignment-action"><a target="_blank" href="<?php echo site_url('materials/course/' . $assig['cl_course_id'] . '/0/0#' . $assig['id']) ?>" class="report-link">View Details</a></span>
                                            </li>

        <?php elseif ($assig['scored_mark'] == -1): ?>
                                            <!-- Awaiting for correction -->
                                            <li class="list-group-item single-assignment">
                                                <span class="report-assignmenticon"><img src="<?php echo assets_url('themes/' . $theme); ?>img/asignment-report.svg"></span>
                                                <span class="assignment-slnmbr"><?php echo $count; ?></span>
                                                <span class="assignment-desc">
                                                    <p class="assignment-content"><?php echo $assig['a_name']; ?></p>
            <?php print_lastDate($assig['last_date']); ?>
                                                    <span class="assignment-time">Submitted Date: <span><?php echo date('M d, Y', strtotime($assig['submitted_date'])) ?></span></span>
                                                </span>
                                                <span class="assignment-mark">Awaiting result</span>
                                                <span class="report-action assignment-action"><a target="_blank" href="<?php echo site_url('materials/course/' . $assig['cl_course_id'] . '/0/0#' . $assig['id']) ?>" class="report-link">View Details</a></span>
                                            </li>
        <?php else: ?>
                                            <!-- Corrected -->
                                            <li class="list-group-item single-assignment">
                                                <span class="report-assignmenticon"><img src="<?php echo assets_url('themes/' . $theme); ?>img/asignment-report.svg"></span>
                                                <span class="assignment-slnmbr"><?php echo $count; ?></span>
                                                <span class="assignment-desc">
                                                    <p class="assignment-content"><?php echo $assig['a_name']; ?></p>
            <?php print_lastDate($assig['last_date']); ?>
                                                    <span class="assignment-time">Submitted Date: <span><?php echo date('M d, Y', strtotime($assig['submitted_date'])) ?></span></span>
                                                </span>
                                                <span class="assignment-mark"><?php
                                                    echo $assig['scored_mark'];
                                                    echo ($assig['scored_mark'] == 0) ? ' Mark' : ' Marks';
                                                    ?></span>
                                                <span class="report-action assignment-action"><a target="_blank" href="<?php echo site_url('materials/course/' . $assig['cl_course_id'] . '/0/0#' . $assig['id']) ?>" class="report-link">View Details</a></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php } ?>
                                <?php

                                function print_lastDate($last_date) { ?>
                                    <?php if ($last_date != '0000-00-00'): ?>
                                        <span class="assignment-date">Last Date: <span><?php echo date('M d, Y', strtotime($last_date)) ?></span></span>
                                    <?php else: ?>
                                        <span class="assignment-date">Last Date: <span>Not Assigned</span></span>
    <?php endif; ?>
<?php } ?>

                            </div>
                        </div>
                    </div>


                </div>


            </div>  <!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->
</section>




<?php include 'footer.php'; ?>
