<?php include 'header.php';?>
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontello.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/styles.css" rel="stylesheet">
<script type="text/javascript">
	var course_id = '<?php echo $course_id; ?>';
	var ratting   = '<?php echo $course_details["ratting"]; ?>';
	var categoryid= '<?php echo $category_id; ?>';
	var user_id   = '<?php echo $session['id']; ?>';
</script>
<div class="container">
	<div class="wrapper">
		<div class="sction_1">
			<div class="row">
				<?php include 'sidebar.php'; ?>
				<div class="col-sm-9 padding_L">
					<div class="course_description">
						<h2><?php echo $course_details['cb_title']; ?></h2>
					<div class="row">
					<div class="col-sm-8 right_p">
					<?php if(isset($course_details['cb_promo']) && $course_details['cb_promo'] != ''){ ?>
					<iframe class="" width="100%" height="255" src="<?php echo isset($course_details['cb_promo'])?generate_youtube_url($course_details['cb_promo']):'' ?>" frameborder="0" allowfullscreen></iframe>
					<?php }else{  ?>
						<img src="<?php echo (($course_details['cb_image'] == 'default.jpg')?default_course_path():course_path(array('course_id' => $course_details['id']))).$course_details['cb_image']?>" />
					<?php }       ?>
					</div>
					<div class="col-sm-4 padding_L2">
						<div class="course_description_text">
							<h4><strong><?php echo $course_details['subscribed_students']; ?></strong> <?php echo lang('student_enrolled'); ?></h4>
							<?php if ($course_details['purchase']=="0"){ ?>
								<div id="whishdiv">
									<?php if($whish_list_stat == 0){ ?>
										 <i class="demo-icon icon-heart-empty" onclick="add_wishlist(<?php echo $course_id; ?>, '<?php echo $session['id']; ?>')" rel="tooltip" title="Add To Whishlist" ></i>
									<?php }else if($whish_list_stat == 1){ ?>
										 <i class="demo-icon icon-heart" onclick="remove_wishlist(<?php echo $course_id; ?>, '<?php echo $session['id']; ?>')" rel="tooltip" title="Remove From Whishlist" ></i>
									<?php }  ?>
								</div>
							<?php } ?>
							<div class="ratting"><div id="rate_course"></div><span><?php if($course_details["ratting"]!='0') { echo $course_details["ratting"]; } ?></span></div>
							<div class="rate">
								<?php if(($course_details['cb_is_free'] == 1) || (($course_details['cb_discount']== 0) && ($course_details['cb_price'] == 0))){ ?>
									<h2><?php echo lang('free'); ?></h2>
								<?php }else if(intval($course_details['cb_discount']) != 0){ ?>
									<h2>Rs.<?php echo $course_details['cb_discount']; ?><span>Rs.<?php echo $course_details['cb_price']; ?></span></h2>
								<?php }else{ ?>
									<h2>Rs.<?php echo $course_details['cb_price']; ?></h2>
								<?php } ?>
							</div>

							<?php if($course_details['purchase'] == 0){
                                    if($course_details['expired'] == 0){
                                    	if(!empty($session)){ ?>

                                             <?php 

                                             	if(($course_details['cb_is_free'] == 1 ) || (($course_details['cb_discount']== 0) && ($course_details['cb_price'] == 0))){ ?>
                                                
	                                                <a href="<?php echo site_url('/checkout/standard/'.$course_id.'/'.base64_encode('course')) ?>"><?php echo lang('by_course'); ?></a>
	                                     <?php 	}else { ?>
	                                                
	                                                <a href="<?php echo site_url('/checkout/feepal/'.$course_id.'/'.base64_encode('course')) ?>"><?php echo lang('by_course'); ?></a>
	                                     <?php } 
	                                          ?>


                            			<?php 

                            			}  else { ?>

					                            <a href="<?php echo site_url('/login') ?>"><?php echo lang('by_course'); ?></a>
					        <?php 		} 

                                    }else{ ?>
                                            <?php if($course_details['cb_access_validity'] != '2'){ 
                                            			if(($course_details['cb_is_free'] == 1) || (($course_details['cb_discount']== 0) && ($course_details['cb_price'] == 0))){ ?>

                                                          	<a href="<?php echo site_url('/checkout/standard/'.$course_id.'/'.base64_encode('course')) ?>"><?php echo "Renew Course"; ?></a>
                                                  <?php } else { ?>

                                                            <a href="<?php echo site_url('/checkout/feepal/'.$course_id.'/'.base64_encode('course')) ?>"><?php echo "Renew Course"; ?></a>

                                                  <?php } 
                                            } 
                                    }

                            }else{  ?>
									<a href="<?php echo site_url('/materials/course/'.$course_id) ?>"><?php echo lang('goto_course'); ?></a>
					<?php 	} ?>
                                                                         
							<?php 
                                    if(($user_preview_time != $course_details['cb_preview_time']) && ($user_preview_time <= $course_details['cb_preview_time'])){

                                            if($course_details['cb_preview'] == '1'){

                                                if(!empty($session)){ 

                                                    if($course_details['purchase'] == '0' || ($session['us_status'] == '1' && $session['us_deleted'] == '0')){ ?>

                                                        <a class="btn-margin-bottom" href="<?php echo site_url('/materials/course/'.$course_id) ?>"><?php echo lang('free_preview'); ?></a>
                                             <?php 
                                                    }
                                                }else if($course_details['cb_access_validity'] != '2'){ ?>
                                                        <a class="btn-margin-bottom" href="<?php echo site_url('/login') ?>"><?php echo lang('free_preview'); ?></a>
                                              <?php }else if($course_details['expired'] == 0){  ?>
                                              			<a class="btn-margin-bottom" href="<?php echo site_url('/login') ?>"><?php echo lang('free_preview'); ?></a>

                                              <?php	}
                                        	}
                                    } ?>
                                                                    
							<div class="share">
							<h5><?php echo lang('share');?></h5>
								<!-- <img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/images/share.png'; ?>" class="img-responsive" alt="img"> -->
								<div class="fb-share-button" 
									data-href="<?php echo $current_url; ?>" 
									data-layout="button">
								</div>
								<a style="display:none;" href="https://twitter.com/share" class="twitter-share-button" data-show-count="true">Tweet</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
								<g:plus action="share" data-action="share" data-annotation="none" width="100" ></g:plus>
							</div>
						</div>
					</div>
					</div>
					<div class="lectures_list">
					<ul>
					<li>
					<?php if($course_details['lecture_count'] != '' && $course_details['lecture_count'] != 0){ ?>
						<?php echo lang('lectures'); ?>
						<span>
						<?php echo $course_details['lecture_count']; ?>
						</span>
					<?php }     ?>
					</li>
					<li>
					<?php if($course_details['online_tests_count'] != '' && $course_details['online_tests_count'] != 0){ ?>
						<?php echo lang('online_tests'); ?> 
						<span>
						<?php echo $course_details['online_tests_count']; ?>
						</span>
					<?php }     ?>
					</li>
					<li>
					<?php if($course_details['document_count'] != '' && $course_details['document_count'] != 0){ ?>
						<?php echo lang('documents'); ?>
						<span>
						<?php echo $course_details['document_count']; ?>
						</span>
					<?php }     ?>
					</li>
                                        <li>
					<?php if($course_details['live_count'] != '' && $course_details['live_count'] != 0){ ?>
						<?php echo lang('live_classes'); ?>
						<span>
						<?php echo $course_details['live_count']; ?>
						</span>
					<?php }     ?>
					</li>
					</ul>
					</div>
					<div class="course_text">
					<h2><?php echo lang('course_description'); ?></h2>
					<p>
						<?php echo $course_details['cb_description']; ?>
					</p>
					
					<h2><?php echo lang('curriculum');?></h2>
					<div id="curriculum">
					<?php 
					$cnt = 1;
					$lecture_count = 1;
					$quiz_count    = 1;
					foreach($course_details['sections'] as $section){ 
					?>
					<h3>Section <?php echo $cnt; ?>: <?php echo $section['s_name']; ?></h3>
					<div class="section_list">
						<?php 
						foreach ($section['lectures'] as $lecture) { ?>
						<ul>
							<li>
								<?php if($lecture['cl_lecture_type'] == 3 || $lecture['cl_lecture_type'] == 8){ ?>
									<img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/images/icn3.png';?>" alt="img">
								<?php }else if($lecture['cl_lecture_type'] == 1 || $lecture['cl_lecture_type'] == 4){ ?>
									<img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/images/icn1.png';?>" alt="img">
								<?php }else{ ?>
									<img src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/images/icn2.png';?>" alt="img">
								<?php }      ?>
							</li>
							<li>
								<?php  
								if($lecture['cl_lecture_type'] == 3 || $lecture['cl_lecture_type'] == 8){ 
									echo 'Quiz '.$quiz_count;
									$quiz_count++;
								}else{
									echo 'Lecture '.$lecture_count;
									$lecture_count++;
								}
								?>
							</li>
							<li>
								<?php echo $lecture['cl_lecture_name'];?>
							</li>
							<li>
								<?php 
								if(intval($lecture['cl_lecture_type']) == 1){
									$dh 	= $lecture['duration_hm'];
									$dh_arr = explode(':',$dh);
									if(isset($dh_arr[0])){
										if(intval($dh_arr[0]) < 10){
											echo '0'.$dh_arr[0].':';
										}
										else{
											echo $dh_arr[0].':';
										}
									}
									if(isset($dh_arr[1])){
										if(intval($dh_arr[1]) < 10){
											echo '0'.$dh_arr[1];
										}
										else{
											echo $dh_arr[1];
										}
									}
								}
								else if(intval($lecture['cl_lecture_type']) == 2 || intval($lecture['cl_lecture_type']) == 8){
                                                                    $page = ($lecture['cl_total_page'] == '1')?' Page':' Pages';
                                                                    echo $lecture['cl_total_page'].$page;
								}
								else if(intval($lecture['cl_lecture_type']) == 3){
									echo $lecture['num_of_question'].' Questions';
								}
								?>
							</li>
						</ul>
						<?php } ?>

					</div>
					<?php 
					$cnt++;
					} 
					?>
					</div>
                                        <?php if($course_details['all_lectures_count'] > 6){ ?>
                                        <a class="top" id="full_curriculum_list" href="javascript:void(0)" onclick="fullcurriculum()"><?php echo lang('full_carriculam'); ?></a>
                                        <?php }?>
                                            <div class="row">
						<div class="col-xs-12">
							<h3><?php echo lang('instucted_by'); ?></h3>
							<ul class="tutornames">
							<?php foreach($course_details["course_tutors"] as $tutors) { ?>
								<li>
								<img src="<?php echo (($tutors['us_image'] == 'default.jpg')?default_user_path():  user_path()).$tutors['us_image']; ?>" alt="img" title="<?php echo $tutors['us_name']; ?>" >
								</li>
							<?php } ?>
							</ul>
						</div>
					</div>
					<div class="row">
					<div class="col-xs-12">
					<div class="reviews_section">
					<h3><?php echo lang('reviews'); ?></h3>
					<h4><?php echo lang('average_ratting'); ?></h4>
					<div class="rate">
						<span><?php echo $course_details["ratting"]; ?></span>
						<div id="rate_review_course"></div>
					</div>
					</div>
					</div>
					</div>
						<?php if(empty($course_details['reviews'])): ?>
						<div class="comment_section">
							No Comments Yet
						</div>
						<?php else: 
						foreach ($course_details['reviews'] as $review) {
                                                    if(!empty($review['cc_reviews'])){
						?>
						<div class="comment_section">
							<div class="comment_section_user">
								<span><img src="<?php echo (($review['cc_user_image'] == 'default.jpg')?default_user_path():user_path()).$review['cc_user_image']?>" alt="skillsjunxion"></span>
								<h3><?php echo $review['cc_user_name'] ?></h3>
								<h4><?php echo get_day_name($review['created_date']) ?></h4>
							</div>
                                                        <div class="course-rating">
                                                            <div id="student_rate_<?php echo $review['cc_admin_rating_id']?>"></div>
                                                        </div>
							<p><?php echo $review['cc_reviews'] ?></p>
						</div>
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                        $("#student_rate_<?php echo $review['cc_admin_rating_id']?>").rateYo({
                                                            starWidth: "15px",
                                                            rating: <?php echo $review['cc_rating'] ?>,
                                                            readOnly: true
                                                        });
                                                    });
                                                </script>
                                                    <?php }
                                                } ?>
						<?php endif; ?>
					</div>
					</div>
					</div>
			</div>
		</div>
	</div>
</div>


<?php

function get_day_name($datetime) {

    $date = date('Y-m-d', strtotime($datetime));
    $time = date('h:i a', strtotime($datetime));

    if($date == date('Y-m-d')) {
      $date = 'Today '.$time;
    } 
    else if($date == date('Y-m-d',time() - (24 * 60 * 60))) {
      $date = 'Yesterday '.$time;
    }else{
        $date = date('d-m-Y h:i a', strtotime($datetime));
    }
    return $date;
}

 function generate_youtube_url($url=false)
{
    $pattern = 
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if ($result) {
        //return $matches[1];
        return 'https://www.youtube.com/embed/'.$matches[1];
    }
    return false;
}
 ?>

<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/jquery.rateyo.min.js'; ?>" ></script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/course_description.js'; ?>" ></script>
<?php include 'footer.php'; ?>
