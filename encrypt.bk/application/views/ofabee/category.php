<?php include 'header.php'; ?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<script type="text/javascript">
    var rattings = $.parseJSON('<?php echo $rattings; ?>');
    var categoryid= '<?php echo $category_id; ?>';
    var user_id   = '<?php echo $session['id']; ?>';
</script>
<!--- banner -->
<div class="innerbanner">
    <div class="wrapper">
        <div class="row">
            <div class="col-xs-12">
                <h2><?php echo lang('join_intensive_ias_program') ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h3><?php echo lang('online_video_test_series') ?></h3>
            </div>
        </div>


    </div>
</div>
<!-- banner end -->

<div class="wrapper">
    <div class="sction_1">
        <div class="row">
            <?php include 'sidebar.php'; ?>
            <div class="col-sm-9 padding_L">
                <div class="sction_2">
                    <ul class="nav nav-tabs responsive" id="myTab">
                        <li class="active"><a href="#courses"><?php echo lang('courses') ?></a></li>
                        <li><a href="#teachers"><?php echo lang('teachers') ?></a></li>
                    </ul>
                    <div class="tab-content responsive">
                        <div class="tab-pane active" id="courses">
                            <div class="row">
                            <div class="col-xs-12">
                                <h2 class="tab_hd"><?php echo lang('explore_course') ?></h2>
                            </div>
                            </div>
                            <div class="row">
                                <?php 
                                    $items = array();
                                    if(!empty($user_course_enrolled)){
                                        foreach($user_course_enrolled as $course_enrolled) {
                                            $items[] = $course_enrolled['id'];
                                        }
                                    }
                                ?>
                            <?php if(!empty($category_course)) : ?>    
                            <?php foreach($category_course as $key => $course): ?>   
                            <a href="<?php echo site_url().'/'.$course['cb_slug']; ?>">
                            <?php 
                                $image_first_name   = substr($course['cb_image'],0,-4);
                                $image_dimension    = '_300x160.jpg';
                                $image_new_name     = $image_first_name.$image_dimension;
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 <?php echo ($key>2)?'margin':'' ?>">
                                <div class="tab_box">
                                <?php if (!in_array($course['id'], $items)){ ?>
                                    <div id="whishdiv_<?php echo $course['id']; ?>">
                                    <?php echo $course['wish_stat']; ?>
                                    </div>
                                <?php } ?>
                                    <img class="img-responsive" src="<?php echo (($course['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $course['id']))).$image_new_name; ?>" alt="">
                                    <h2><?php echo $course['cb_title'] ?></h2>
                                    <?php $tutor_names = array(); foreach($course['assigned_tutors'] as $course_tutor) : 
                                        $tutor_names[] = $course_tutor['us_name'];
                                    endforeach; ?>
                                    <h3><?php echo lang('by') ?><?php echo (empty($tutor_names))?$admin:implode(', ',$tutor_names) ?></h3>
                                    <div class="star">
                                        <div id="rate_div_<?php echo $key; ?>">
                                        </div>
                                        <span></span>
                                        <div class="rate">
                                            <h2><?php echo ($course['cb_is_free'] == '1')?'FREE':(($course['cb_discount'] != '0')?"RS. ".$course['cb_discount']:"RS. ".$course['cb_price']); ?> 
                                                <span><?php echo ($course['cb_is_free'] == '1')?'':(($course['cb_discount'] != '0')?"RS. ".$course['cb_price']:''); ?></span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </a>
                            <?php endforeach; ?>  
                            <?php else :?>
                                <div class="col-xs-12">
                                <h2 class="tab_hd"><?php echo lang('no_course_found'); ?></h2>
                                </div>
                            <?php endif; ?>
                                
                            
                            <!--<div class="more_link"><a href="#">More</a></div>-->
                            </div>
                        </div>
                        <div class="tab-pane" id="teachers">
                            <div class="row">
                            <div class="col-xs-12">
                                <h2 class="tab_hd"><?php echo lang('explore_teacher') ?></h2>
                            </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    Our Expert Teachers will be coming soon...
                                </div>
                            
                            
                            <!--<div class="more_link"><a href="#">More</a></div>-->
                            </div>
                        </div>

                    </div>

                </div>
            </div>
</div>
</div>
</div>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/category.js'; ?>" ></script>
<?php include 'footer.php'; ?>

