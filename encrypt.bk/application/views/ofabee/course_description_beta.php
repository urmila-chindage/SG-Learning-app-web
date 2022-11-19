<style>
/* onload button action */
.btn-onloading{position:relative}
.btn-onloading .btn{
  background: #f2f3f5 !important;
  color: #d2d2d2 !important;
  pointer-events:none;
}
.btn-loading-icon{display:none;}
.btn-onloading .btn-loading-icon{display:block;}
</style>


<?php

    $tutors_names   = array();
    $tutors_count   = 0;

    $total_lectures = ( !empty($course['lectures']) )?count($course['lectures']):0;
    
    $total_rating   = 0;
    $total_rate     = 0;
    $ratings        = 0;
    $average_rating = 0;
    
    foreach ( $course['rating'] as $cr ) 
    {
        $total_rating       += $cr['ratings'];
        $total_rate         += $cr['rating'];
        $ratings            += $cr['ratings'] * $cr['rating'];
    }

    if ($total_rating > 0)
    {
        $average_rating = round($ratings / $total_rating, 1);
    }
    
    $rating                       = array();
    $rating[1]['percentage']      = 0;
    $rating[1]['count']           = 0;
    $rating[2]['percentage']      = 0;
    $rating[2]['count']           = 0;
    $rating[3]['percentage']      = 0;
    $rating[3]['count']           = 0;
    $rating[4]['percentage']      = 0;
    $rating[4]['count']           = 0;
    $rating[5]['percentage']      = 0;
    $rating[5]['count']           = 0;

    foreach ($course['rating'] as $cr) {
        $rating[$cr['cc_rating']]['percentage']   = round(($cr['ratings'] * 100) / $total_rating, 1);
        $rating[$cr['cc_rating']]['count']        = $cr['ratings'];
    }

    $sum_rating   = count($course['rating']) != 0 ? round(($total_rate / count($course['rating'])), 1) : 0.0;
    $error_expired = 0;
    switch ($course['cb_access_validity']) {
        case 0:
          $error_expired = 0;
          break;
        case 1:
            if($course['cb_validity'] > 1){
              $error_expired = 0;
            }
            break;
        case 2:
            if($course['cb_validity_expired']){
              $error_expired = 1;
            } 
            break;
        default:
            break;
    }
?>
<?php include 'header.php';?>
<!-- <link rel="stylesheet" href="<?php // echo assets_url() ?>themes/<?php //echo $this->config->item('theme') ?>/css/custom_beta.css"> -->
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk/curriculum.css">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/discussion-customized.css" rel="stylesheet">
<style>
  .alert-modal-new .modal-header{
    border-bottom: 0;
    float: right;
    width: 40px;
    height: 40px;
    background: none;
  }
  .alert-modal-new .modal-header .close {
    font-size: 24px;
    color: #838383;
    right: 13px;
    top: 9px;
    z-index: 9;
    position: relative;
  }

  @media (max-width:768px){
    .page-footer{display:none;}
  }
  .disabled_cursor_style{
    pointer-events: inherit !important;
    cursor: not-allowed !important;
  }
</style>
<script type="text/javascript">
  let __course_id = '<?php echo $course['id']; ?>';
</script>
<!--head-title section --->
<?php 
  $discount_in_percentage = 0;
  if($course['cb_price'] > 0 && $course['cb_discount'] > 0)
  {
    $discount_in_percentage = round((1- ($course['cb_discount']/$course['cb_price']))*100);
  }
?>
<section>
  <div class="head-gradient course-main-info-section">
    <div class="container relative pad0">
      <div class="col-md-9 pad0">
        <div class="course-banner-info" id="course-heading-trigger">
          <h2 class="course-heading"> <?php echo $course['cb_title']; ?></h2>
          <div class="course-image-preview mt-15 xs-visible-only">
            <img src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $course['cb_image'] ?>" class="img-responsive">
          </div>
          <div class="d-flex align-center justify-between duration-social-column">
            <!-- <div class="course-duration">
                < ?php echo $total_lectures?> Lessons  < ?php if($course['cb_total_video_hours'] >= 1):?>, < ?php echo $course['cb_total_video_hours']?> Hours of Videos < ?php endif;?>
            </div> -->
            <div class="share-via-soial">
              <ul>
                <li>
                  <a target="_blank" href="<?php echo 'https://www.facebook.com/sharer/sharer.php?u=' . current_url(); ?>" class="fb-share-button facebook-active">
                    <i class="icon-facebook"></i>
                  </a>
                </li>
                <li>
                  <a target="_blank" href="<?php echo 'https://twitter.com/intent/tweet?text=' . current_url(); ?>" class="twitter-share-button share-twitter">
                    <i class="icon-twitter"></i>
                  </a>
                </li>
                <li>
                  <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . current_url(); ?>" class="google-ser" style="border-right:none">
                    <i class="icon-google"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="course-description">
            <p><?php echo $course['cb_short_description']?> </p>
          </div>
          <div class="tutor-info-row">
          <ul>
            <?php if(!empty($course["tutors"])):
                    foreach ($course["tutors"] as $tutors):
                      $tutor_image  = (($tutors['us_image'] == 'default.jpg')?default_user_path():  user_path()).$tutors['us_image'];
                      $tutors_names[] = $tutors['us_name'];
                      $tutors_count++;
                    ?> 
                      <li>
                        <a href="<?php echo site_url() . 'teachers/view/' . $tutors['id']; ?>" class="d-flex align-center">
                          <div class="tutor-avatar">
                            <img src="<?php echo $tutor_image?>" alt="">
                          </div>
                          <div class="tutor-info">
                            <h4 class="tutor-name"> <?php echo $tutors['us_name']; ?></h4>
                            <h5 class="tutor-designation">Instructor</h5>
                          </div>
                        </a>
                      </li>
                    <?php endforeach;
                    $tutors_name  = (!empty($tutors_names)) ? implode(',', $tutors_names) : " ";
              else:
                $tutors_name  = $this->config->item('us_name');
                $logo         = $this->config->item('site_logo');
                $tutor_image  = ($logo == 'default.png') ? base_url('uploads/site/logo/default.png') : logo_path() . $logo;
                $tutors_count++;
              ?>
              <li>
                  <a href="javacript:void(0);" class="d-flex align-center">
                    <div class="tutor-avatar">
                    <img src="<?php echo $tutor_image;?>" alt="">
                    </div>
                    <div class="tutor-info">
                      <h4 class="tutor-name"> <?php echo $this->config->item('us_name'); ?></h4>
                      <h5 class="tutor-designation">Instructor</h5>
                    </div>
                  </a>
                </li>
            <?php endif;?>
          </ul>
          <div class="clearfix"></div>
        </div><?php  ?>
        <div class="share-via-mobile xs-visible-only" id="mobile-share-trigger">
          <span>Share</span>
          <span style="display: inline-block;vertical-align: middle;">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" style="enable-background:new 0 0 512 512;fill: #fff;width: 20px;height: 20px;" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><g><path d="M352,377.9H102.4V198.2h57.5c0,0,14.1-19.7,42.7-38.2H83.2c-10.6,0-19.2,8.5-19.2,19.1v217.9c0,10.5,8.6,19.1,19.2,19.1   h288c10.6,0,19.2-8.5,19.2-19.1V288L352,319.4V377.9z M320,224v63.9l128-95.5L320,96v59.7C165.2,155.7,160,320,160,320   C203.8,248.5,236,224,320,224z"></path></g></svg>
          </span>
        </div>
        <div class="course-access xs-visible-only">
          Course Access : 
          <?php
              switch ($course['cb_access_validity']) {
                  case 0:
                      echo 'Lifetime';
                      break;
                  case 1:
                      echo $course['cb_validity'] > 1 ? $course['cb_validity'] . ' Days' : $course['cb_validity'] . ' Day';
                      break;
                  case 2:
                      echo $course['cb_validity_expired'] ? 'Expired' : date('d M y', strtotime($course['cb_validity_date']));
                      break;
                  default:
                      break;
              } //echo $courseaccess;
          ?>
        </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 pad0">
    <div class="pricing-preview-right">
      <div class="course-image-preview xs-hidden-only">
        <img src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $course['cb_image'] ?>" class="img-responsive">
      </div>
      <div class="course-valdity-table" id="course-valdity-table">
        <div class="course-pricing-info">
          <div class="offer-price">
            <?php if ($course['cb_is_free'] == '1'): ?>
              <span class="rupee-amount free">FREE</span>
            <?php else: ?>
                <span class="rupee-icon">₹</span> <?php echo $course['cb_discount'] > 0 ? $course['cb_discount'] : $course['cb_price']; ?><sup>*</sup>
              <?php endif;?>
          </div>
          <?php //echo '<pre style="display:none;">'; print_r($course); echo '</pre>'; ?>
          <?php if ($course['cb_is_free'] != '1' && $course['cb_discount'] > 0): ?>
            <div class="real-price">
              <span class="rupee-icon">₹</span>
              <span class="line-through"><?php echo $course['cb_price'] ?></span>
            </div>
            
            <?php echo (($discount_in_percentage>0)?'<div class="offer-strip hidden">'.$discount_in_percentage.'% OFF</div>':''); ?>
          <?php endif;?>  
          </div>
          <?php if ($course['cb_is_free'] != '1'): ?>
            <div class="tax-info">* <?php echo ($course['cb_tax_method'] == 0 )?'Inclusive of all taxes':'Exclusive of all taxes'?></div>
          <?php endif;?>
          <?php $enroll_button_text =($course['cb_is_free'] == '1') ? 'Enroll' : 'Buy Now';?>
          <?php $error_expired == 1 ? $enroll_button_text = 'Expired' : 1 + 1;?>
          
            <?php $button_css  = $course['cb_has_self_enroll'] == '0' ? "disabled disabled_cursor_style" : "" ?>
          <div class="enroll-course-navigater">
            <div class="buynow-holder" id="btn-onloading1">
              <?php $disabled = null; if(($course['self_enroll'] == 0) || ( $error_expired == 1 )): $disabled = 'disabled'; endif;?>
        
              <a <?php echo $disabled;?> class="btn buynow-btn" <?php echo (( !empty($session) ) && ( $course['self_enroll'] == 1 && $error_expired == 0 )) ? 'onclick="enrollToCourse()"' : ''; ?>  href="
              <?php echo !empty($session) ? 'javascript:void(0)' : site_url('login'); ?>">
                <?php echo $enroll_button_text; ?>&nbsp;&nbsp;<span class="glyphicon glyphicon-triangle-right"></span>
                <!-- loading icon -->
                <svg class="btn-loading-icon" xmlns="http://www.w3.org/2000/svg" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling" style="background: none;width: 25px;height: 25px;position: absolute;left: 0px;right: 0px;margin: 0 auto;top: 50%;transform: translateY(-50%);"><circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#9f9f9f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(99.0936 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></svg>
                <!-- loading icon ends -->
              </a>

             </div>
                <?php if ($course['cb_is_free'] != '1'): ?>
                  <?php if ($course['cb_preview']):
                    $preview_link = isset($session['id']) ? site_url('materials/course/' . $course['id']) : site_url('login');
                  ?>
                    <div class="freeprev-holder">
                      <!-- <a class="btn freeprev-btn" href="<?php //echo $preview_link?>">Free Preview</a> -->
                   
                    <?php $link = isset($session['id']) ? site_url('materials/course/' . $course['id']) : site_url('login'); ?>
                      <?php //if (($course['cb_preview']) && ($course['cb_is_free'] != '1')):?>
                          <?php if (!isset($session['id']) || empty($session['id'])): ?> 
                            <a class="btn freeprev-btn" href="<?php echo $link; ?>">Free Preview</a>
                          <?php else: ?>
                              <?php if(isset($course['remaning_preview_time']['cpt_course_time']) && $course['remaning_preview_time']['cpt_course_time'] < $course['cb_preview_time']): ?>
                              <a class="btn freeprev-btn" href="<?php echo $link; ?>">Free Preview</a>
                              <?php endif; ?>
                              <?php if(!isset($course['remaning_preview_time']['cpt_course_time'])){ ?>
                                  <a class="btn freeprev-btn" href="<?php echo $link; ?>">Free Preview</a>
                              <?php } ?>
                          <?php endif; ?>
                      <?php //endif;?>
                         </div>
                  <?php endif;?>
                <?php endif;?>
          </div>
          <?php if((( $course['cb_has_rating'] == 1 ) && (round(($average_rating * 20), 2) > 0 )) || (($course['cb_has_show_total_enrolled'] == 1) && ( $course['cb_total_enrolled_users'] > 0 )) ):?>
            <div class="course-rating d-flex justify-between">
              <?php if($course['cb_has_rating'] == 1 && round(($average_rating * 20), 2) > 0 ):?>
              <div class="star-ratings-sprite-two">
                <span style="width: <?php echo round(($average_rating * 20), 2) ?>%;" class="star-ratings-sprite-rating-two"></span>
              </div>
              <?php endif;?>
              <?php if( ( $course['cb_has_show_total_enrolled'] == 1 ) && ( $course['enrolled_students'] > 0 ) ):?>
                <div class="enrolled-count"><?php echo $course['enrolled_students'];?> Enrolled</div>
              <?php endif;?>
            </div>
          <?php endif;?>
          <table width="100%" id="course-pricing-table">
            <tbody>
              <tr>
                <td class="text-left">Course Access</td>
                <td class="text-center">:</td>
                <td class="text-right">
                  <?php
                    switch ($course['cb_access_validity']) {
                        case 0:
                            echo 'Lifetime';
                            break;
                        case 1:
                            echo $course['cb_validity'] > 1 ? $course['cb_validity'] . ' Days' : $course['cb_validity'] . ' Day';
                            break;
                        case 2:
                            echo $course['cb_validity_expired'] ? 'Expired' : date('d M y', strtotime($course['cb_validity_date']));
                            break;
                        default:
                            break;
                    } //echo $courseaccess;
                  ?>
                </td>
              </tr>
              <?php  if($course['cb_has_certificate'] == 1):?>
                <tr>
                  <td class="text-left">Certification</td>
                  <td class="text-center">:</td>
                  <td class="text-right"><?php echo ($course['cb_has_certificate'] == 1)?'Yes':'No';?></td>
                </tr>
              <?php endif;?>              
              
                              <tr>
                                 <td class="text-left">Instructors</td>
                                 <td class="text-center">:</td>
                                 <td class="text-right"><?php echo $tutors_count ;?></td>
                              </tr>
                              <?php if($course['cb_total_video_hours'] >= 1):?>
                                <tr>
                                  <td class="text-left">Duration</td>
                                  <td class="text-center">:</td>
                                  <td class="text-right"><?php echo $course['cb_total_video_hours']?> Hours</td>
                                </tr>
                              <?php endif;?>
                              
                              <?php if ($course['cb_docs_count'] != '' && $course['cb_docs_count'] != 0): ?>
                              <tr>
                                 <td class="text-left">Documents</td>
                                 <td class="text-center">:</td>
                                 <td class="text-right"><?php echo $course['cb_docs_count']; ?></td>
                              </tr>
                              <?php endif;?>
                              
                              <?php if ($course['cb_video_count'] != '' && $course['cb_video_count'] != 0):?>
                              <tr>
                                 <td class="text-left">Videos</td>
                                 <td class="text-center">:</td>
                                 <td class="text-right"><?php echo $course['cb_video_count']; ?></td>
                              </tr>
                              <?php endif;?>
                              <?php if($course['cb_assessment_count'] != '' && $course['cb_assessment_count'] != 0):?>
                              <tr>
                                 <td class="text-left">Quiz</td>
                                 <td class="text-center">:</td>
                                 <td class="text-right"><?php echo $course['cb_assessment_count']; ?></td>
                              </tr>
                              <?php endif;?>
                              <?php if (count($course['cb_language']) != 0): 
                                    $languages          = array();
                                    $all_languages      = array();
                                    if (count($course['cb_language']) == 1) 
                                    {
                                        $languages[] = $course['cb_language'][0]['cl_lang_name'];
                                    }
                                    else 
                                    {
                                        foreach ($course['cb_language'] as $language):
                                            $languages[]      = substr($language['cl_lang_name'], 0, 3);
                                            $all_languages[]  = $language['cl_lang_name'];
                                        endforeach;
                                    
                                    }
                                ?>
                              <tr>
                                 <td class="text-left">Languages</td>
                                 <td class="text-center">:</td>
                                 <td class="text-right lang-ellipsis" title="<?php echo implode(' / ', $all_languages); ?>"><?php echo implode(' / ', $languages); ?></td>
                              </tr>
                              <?php endif;?>
                              
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!--head-gradient-->
      </section>
<!--head-title section end--->
<section class="sticky-enroll-strip justify-between" id="sticky-enroll-strip">
         <div class="course-pricing-info">
          <?php if ($course['cb_is_free'] == '1'): ?>
            <div class="offer-price">Free</div>
          <?php else: ?>
            <div class="offer-price"><span class="rupee-icon">₹</span>  <?php echo $course['cb_discount'] > 0 ? $course['cb_discount'] : $course['cb_price']; ?></div>
          <?php endif;?> 
          <?php if ($course['cb_is_free'] != '1' && $course['cb_discount'] > 0): ?>
            <div class="real-price">
               <span class="rupee-icon">₹</span>
               <span class="line-through"><?php echo $course['cb_price'] ?></span>
            </div>
          <?php endif;?> 
          <?php echo (($discount_in_percentage > 0) && ($course['cb_is_free'] != '1')?'<div class="offer-strip">'.$discount_in_percentage.'% OFF</div>':''); ?>
         </div>
         <div class="">
         <?php if(!(($course['self_enroll'] == 0) || ( $error_expired == 1 ))): ?>
           <?php $enroll_button_text = ($course['cb_is_free'] == '1') ? 'Enroll' : 'Buy Now';?>
            <a <?php echo !empty($session) ? 'onclick="enrollToCourse()"' : ''; ?>  href="
              <?php echo !empty($session) ? 'javascript:void(0)' : site_url('login'); ?>"  ><button class="btn buynowbtn-sticky"><?php echo $enroll_button_text?></button>
            </a>
          <?php endif; ?>
         </div>
</section>
<section>
         <div class="course-content-wrapper">
            <!-- Tab nav starts -->
            <div class="tab-container" id="tab-container">
               <div class="container pad0">
                  <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#curriculam">Curriculum</a></li>
                     <li><a data-toggle="tab" href="#overview">Overview</a></li>
                     <?php if(isset($course['course_reviews']['reviews']) && !empty($course['course_reviews']['reviews']) && $course['cb_has_rating'] == 1):?>
                     <li><a data-toggle="tab" href="#reviews">Reviews</a></li>
                     <?php endif;?>
                  </ul>
               </div>
            </div>
            <!-- Tab nav ends here -->
            <div class="container course-content-tab pad0">
               <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 pad0">
                  <!-- Couses Content tab starts here  -->
                  <div class="tab-content"  id="tab-content">
                     <div id="overview" class="tab-pane fade ">
                        <?php $cb_what_u_get = json_decode($course['cb_what_u_get']);
                              if(isset($cb_what_u_get) && !empty($cb_what_u_get)):
                        ?>
                        <div class="overview-contents">
                           <h4 class="overview-title">What you'll Get</h4>
                           <ul class="overview-list">
                              <?php foreach($cb_what_u_get as $key=>$cb_what):?>
                                <?php if(trim($cb_what) != ''):?>
                                  <li><?php echo $cb_what;?></li>
                                <?php endif;?>
                              <?php endforeach;?>
                           </ul>
                        </div>
                          
                        <?php endif;?>

                        <?php $cb_requirements = json_decode($course['cb_requirements']);
                              if(isset($cb_requirements) && !empty($cb_requirements)):
                        ?>
                        <div class="overview-contents">
                           <h4 class="overview-title">Requirements</h4>
                           <ul class="overview-list">
                              <?php foreach($cb_requirements as $key=>$cb_requirement):?>
                                <?php if(trim($cb_requirement) != ''):?>
                                  <li><?php echo $cb_requirement?></li>
                                <?php endif;?>
                              <?php endforeach;?>
                           </ul>
                        </div>
                          
                        <?php endif;?>

                        <div class="overview-contents">
                           <h4 class="overview-title">Description</h4>
                           <div style="" class="overview-list show-more-data-wrap show-more-collapse">
                              <?php echo $course['cb_description']; ?> 
                              <?php if(strlen($course['cb_description']) > 999): ?>
                                <div class="see-more">
                                  <a href="javascript:void(0)" class="Showmore-btm">See More</a>
                                </div>
                              <?php endif;?>
                           </div>
                        </div>
                     </div>

                     <div id="curriculam" class="tab-pane fade active in">
                        <!-- Curriculam Collapse -->               
                        <div class="curriculam-collapse-wrapper" id="curriculum_div">
                           <!-- load from javascript -->
                        </div>
                        <!-- Curriculam Collapse ends -->
                     </div>

                     <div id="reviews" class="tab-pane fade">
                      <h5 class="tab-title">Reviews</h5>
                       
                        
                        <!-- review starts here -->
                        <div class="review-container">
                           <div id="tab-reviews" class="plugin-reviews section">
                              <div class="plugin-reviews" id="tabreviews">
                                <?php if(isset($course['course_reviews']['reviews']) && !empty($course['course_reviews']['reviews'])): $reviews = $course['course_reviews']['reviews']; //print_r($reviews);?>
                                  <?php for($i = 0; $i < count($reviews); $i++):?>
                                    <div class="review-holder">
                                      <div class="user-review review-title-row">
                                        <div class="review-avatar">
                                          <img alt="<?php echo $reviews[$i]['cc_user_name'];?> profile pic" src="<?php echo ($reviews[$i]['cc_user_image'] ?  user_path() : default_user_path()).$reviews[$i]['cc_user_image'];?>" class="avatar avatar-60 photo img-responsive">					
                                        </div>
                                        <div class="review-name-rating">
                                            <div class="reviewer-name"><?php echo $reviews[$i]['cc_user_name'];?></div>
                                            <div class="review">
                                              <div class="star-ratings-sprite-two">
                                                  <span style="width: <?php echo (100 / 5 ) * $reviews[$i]['cc_rating'];?>%;" class="star-ratings-sprite-rating-two"></span>
                                              </div>
                                            </div>
                                        </div>
                                      </div>
                                      <div class="review-content-row">
                                        <p class="review-content"><?php echo $reviews[$i]['cc_reviews'];?></p>
                                      </div>
                                      <?php if(isset($reviews[$i]['cc_admin_reply']) && $reviews[$i]['cc_admin_reply']): ?>
                                        <!-- admin reply -->
                                        <?php   $adminreplay = json_decode($reviews[$i]['cc_admin_reply']);
                                                $admin_image = isset($adminreplay->cc_us_image) ?  $adminreplay->cc_us_image : '';
                                                $us_image    = ($admin_image ?  user_path() : default_user_path()).$admin_image;
                                        ?>
                                        <div class="admin-reply review-title-row">
                                          <div class="review-avatar">
                                            <img alt="" src="<?php echo $us_image; ?>" class="avatar avatar-60 photo img-responsive">             
                                          </div>
                                          <div class="review-name-rating">
                                            <div class="reviewer-name"><b><?php echo isset($adminreplay->cc_user_name) ? $adminreplay->cc_user_name : '';?></b></div>
                                              <div class="review">
                                                <p><?php echo isset($adminreplay->cc_review_reply) ? $adminreplay->cc_review_reply : '';?></p>
                                              </div>
                                            </div>
                                          </div>
                                        <!-- admin reply -->
                                      <?php endif;?>
                                  </div>
                                  <?php endfor;?>
                                <?php endif;?>
                                 
                              </div>
                              <?php $style = '';
                                if($course['course_reviews']['count'] > $course['course_reviews']['limit'])
                                {
                                  $style = 'display:block';
                                }
                                else
                                {
                                  $style = 'display:none';
                                }
                              ?>
                              <div class="text-center text-center-btn" id="loadmorebutton" style="<?php echo $style?>">
                                 <span class="noquestion-btn-wrap" style="">
                                 <a href="javascript:void(0)" onclick="loadMoreReviews()" class="orange-flat-btn noquestion-btn" style="display: inline-block;">See more reviews</a>
                                 </span>
                              </div>
                           </div>
                        </div>

                        <h5 class="tab-title">Course Ratings</h5>
                        <div class="overview-contents">
                            <!-- Bar rating starts here -->
                            <div class="overall-rating-box d-flex justify-between align-center">
                                <!-- <h5 class="tab-title xs-visible-only">Rating</h5> -->
                                <div class="star-rating-left">
                                  <div class="big-rating-no"><?php echo $average_rating; ?></div><!--big-rating-no-->
                                  <div>
                                      <div class="star-ratings-sprite-two">
                                        <span style="width:<?php echo round(($average_rating * 20), 2) ?>%" class="star-ratings-sprite-rating-two"></span>
                                      </div>
                                      <span class="strip-font-grey"><?php echo $total_rating; ?> <?php echo $total_rating > 1 ? 'Ratings' : 'Rating'; ?></span>
                                  </div>
                                </div>
                                <!--star-rating-left-->
                                <div class="bar-rating-right">
                                  <?php for ($i = 5; $i > 0; $i--): ?>
                                    <div class="bar-star-number-wrap justify-between">
                                        <div class="starAndNum">
                                          <span class="star-barrating-text"><?php echo $i; ?> Star</span>
                                        </div>
                                        <!--starAndNum-->
                                        <div class="star-progress">
                                          <span class="purple-progress justify-between p-0" style="width:<?php echo $rating[$i]['percentage'] ?>%"></span>
                                        </div><!--star-progress-->
                                        <span class="percent-align "><?php echo $rating[$i]['count']; ?></span>
                                    </div>
                                  <?php endfor;?>
                                </div>
                                <!--bar-rating-right-->
                            </div>
                           <!-- Bar rating ends here -->
                        </div>
                        <!-- reviews end here -->
                     </div>
                  </div>
                  <!-- Course Content tab ends here -->
                  <!--img-and-vid-->
               </div>
               <!--columns-->
               <!--row-->
            </div>
            <!--container-->
         </div>
         <!--fundamentals-->
      </section>

<?php //echo $course_details['videos_length'];die; ?>
<?php $length = isset($course['cb_video_duration']) ? gmdate("H.i", $course['cb_video_duration']) : '00.00';?>



<?php

function time_elapsed_string($datetime, $full = false) {
  $now = new DateTime;
  $ago = new DateTime($datetime);
  $diff = $now->diff($ago);

  $diff->w = floor($diff->d / 7);
  $diff->d -= $diff->w * 7;

  $string = array(
      'y' => 'year',
      'm' => 'month',
      'w' => 'week',
      'd' => 'day',
      'h' => 'hour',
      'i' => 'minute',
      's' => 'second',
  );
  foreach ($string as $k => &$v) {
      if ($diff->$k) {
          $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
      } else {
          unset($string[$k]);
      }
  }

  if (!$full) $string = array_slice($string, 0, 1);
  return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function get_day_name($datetime)
{
    $date = date('Y-m-d', strtotime($datetime));
    $time = date('h:i a', strtotime($datetime));
    if ($date == date('Y-m-d')) {
        $date = 'Today ' . $time;
    } else if ($date == date('Y-m-d', time() - (24 * 60 * 60))) {
        $date = 'Yesterday ' . $time;
    } else {
        $date = date('d-m-Y h:i a', strtotime($datetime));
    }
    return $date;
}
function generate_youtube_url($url = false)
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
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return false;
}
$tax = '';
if ($course['cb_discount'] != 0) {
    $course_price = $course['cb_price'];
    $course_discount_price = $course['cb_discount'];
} else {
    $course_price = '';
    $course_discount_price = $course['cb_price'];
}
if ($course['cb_tax_method'] == '1') {
  $gst_setting = $this->settings->setting('has_tax');
  $cgst = ($gst_setting['as_setting_value']['setting_value']->cgst != '') ? $gst_setting['as_setting_value']['setting_value']->cgst : 0;
  $sgst = ($gst_setting['as_setting_value']['setting_value']->sgst != '') ? $gst_setting['as_setting_value']['setting_value']->sgst : 0;
  $cgst = floatval($cgst);
  $sgst = floatval($sgst);
  $sgst_price = round(($sgst / 100) * $course_discount_price, 2);
  $cgst_price = round(($cgst / 100) * $course_discount_price, 2);
  $tax = $sgst_price + $cgst_price;
  $total_course_price = $course_discount_price + $sgst_price + $cgst_price;
} else {
  $cgst = 0;
  $sgst = 0;
  $sgst_price = 0;
  $cgst_price = 0;
  $total_course_price = $course_discount_price;
}
?>
<script>
  var __theme_url             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
  var __reviews 				      = atob('<?php echo base64_encode(json_encode($course['course_reviews'])); ?>');
  let __site_url 				      = '<?php echo site_url(); ?>';
  let __limit                 = '<?php echo $course['course_reviews']['limit'];?>';
  let __offset                = '<?php echo ($course['course_reviews']['count'] > $course['course_reviews']['limit']) ? $course['course_reviews']['limit'] : '0';?>';
  var __reviewsCount          = '<?php echo $course['course_reviews']['count'];?>';

  let __course                = {
  };
  __course.course_id            = '<?php echo $course['id']; ?>';
  __course.cb_access_validity   = '<?php echo $course['cb_access_validity']; ?>';
  __course.cb_validity          = '<?php echo $course['cb_validity']; ?>';
  __course.cb_validity_expired  = '<?php echo $course['cb_validity_expired']; ?>';
  __course.cb_expire_on         = '<?php echo $course['cb_expire_on']; ?>';
  __course.cb_has_self_enroll   = '<?php echo $course['cb_has_self_enroll']; ?>';
  __course.self_enroll          = '<?php echo $course['self_enroll']; ?>';
  __course.cb_is_free           = '<?php echo $course['cb_is_free']; ?>';
  __course.cb_tax_method        = '<?php echo $course['cb_tax_method']; ?>';
  __course.cb_cgst              = '<?php echo $cgst; ?>';
  __course.cb_price             = '<?php echo $course_discount_price; ?>';
  __course.cb_course_discount   = '<?php echo $course_price; ?>';
  __course.cb_cgst_price        = '<?php echo round($cgst_price, 2); ?>';
  __course.cb_sgst              = '<?php echo $sgst; ?>';
  __course.cb_sgst_price        = '<?php echo round($sgst_price, 2); ?>';
  __course.cb_total_price       = '<?php echo $total_course_price; ?>';
  __course.cb_tax               = '<?php echo $tax; ?>';
  let __user_path         = {
  };
  __user_path.default     = '<?php echo default_user_path() ?>';
  __user_path.native      = '<?php echo user_path() ?>';
  //alert(__user_path.default+ ' ' +__user_path.native);
  let __curriculum            = {};
  __curriculum.sections   = atob('<?php echo base64_encode(json_encode($course['sections'])); ?>');
  __curriculum.lectures   = atob('<?php echo base64_encode(json_encode($course['lectures'])); ?>');
  $(document).ready(function(){
    $(".Showmore-btm").click(function(){
      $(".show-more-data-wrap").removeClass("show-more-collapse");
      $(".show-more-data-wrap").css({
        'max-height' : 'none'});
      $(".Showmore-btm").remove();
    });
  });

  var _freeCourse = false;

</script>
<?php if($course['cb_is_free'] == '1'){ ?>
  <script> _freeCourse = true; </script>
<?php } ?>
<script type="text/javascript" src="<?php echo assets_url() . 'themes/' . $this->config->item('theme') . '/js/course_description.js'; ?>" >
</script>
<script type="text/javascript">
  $(function () {
    $(".solution-list").slice(0, 2).show();
    $("#loadMore").on('click', function (e) {
      e.preventDefault();
      $(".solution-list:hidden").slideDown();
      if ($(".solution-list:hidden").length == 0) {
        $("#load").fadeOut('slow');
      }
    }
                     );
  }
   );
</script>
<script>
  window.onscroll = function() {
    myFunction()};
  var header = document.getElementById("sticky-enroll-strip");
  var sticky = header.offsetTop;
  function myFunction() {
    if (window.pageYOffset > sticky) {
      header.classList.add("sticky");
    }
    else {
      header.classList.remove("sticky");
    }
  }
</script>

<!-- Order Summary starts here-->
<div id="enroll_modal" class="modal info-modal info-modal-container order-modal in"
     aria-hidden="false">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-header-title">Order Summary</div>
                <div class="modal-header-close "data-dismiss="modal"><span class="close">&times;<span></div>
            </div>

            <div class="modal-body">
                <div id="promo-msg"></div>
                <div class="flex-column">
                  <!-- course card holder starts-->
                  <div class="course-card-holder">
                      <div class="xs-replacer">
                          <div class="course-block-1">
                              <div class="course-top-half course-top-sm-alter"> 
                                  <img id="course-preview-img" src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $course['cb_image'] ?>" class="card-img-fit">
                              </div>
                              <!-- <div class="bundle-label">
                                  <div class="bundle-icon"> 
                                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 296.999 296.999" style="enable-background:new 0 0 296.999 296.999;fill: #fff;width: 30px;height: 22px;" xml:space="preserve">
                                          <g><g><g><path d="M45.432,35.049c-0.008,0-0.017,0-0.025,0c-2.809,0-5.451,1.095-7.446,3.085c-2.017,2.012-3.128,4.691-3.128,7.543     v159.365c0,5.844,4.773,10.61,10.641,10.625c24.738,0.059,66.184,5.215,94.776,35.136V84.023c0-1.981-0.506-3.842-1.461-5.382     C115.322,40.849,70.226,35.107,45.432,35.049z"></path>
                                          <path d="M262.167,205.042V45.676c0-2.852-1.111-5.531-3.128-7.543c-1.995-1.99-4.639-3.085-7.445-3.085c-0.009,0-0.018,0-0.026,0     c-24.793,0.059-69.889,5.801-93.357,43.593c-0.955,1.54-1.46,3.401-1.46,5.382v166.779     c28.592-29.921,70.038-35.077,94.776-35.136C257.394,215.651,262.167,210.885,262.167,205.042z"></path>
                                          <path d="M286.373,71.801h-7.706v133.241c0,14.921-12.157,27.088-27.101,27.125c-20.983,0.05-55.581,4.153-80.084,27.344     c42.378-10.376,87.052-3.631,112.512,2.171c3.179,0.724,6.464-0.024,9.011-2.054c2.538-2.025,3.994-5.052,3.994-8.301V82.427     C297,76.568,292.232,71.801,286.373,71.801z"></path>
                                          <path d="M18.332,205.042V71.801h-7.706C4.768,71.801,0,76.568,0,82.427v168.897c0,3.25,1.456,6.276,3.994,8.301     c2.545,2.029,5.827,2.78,9.011,2.054c25.46-5.803,70.135-12.547,112.511-2.171c-24.502-23.19-59.1-27.292-80.083-27.342     C30.49,232.13,18.332,219.963,18.332,205.042z"></path>
                                          </g></g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
                                      </svg>
                                  </div>
                                  <div class="bundle-count">
                                      <span>2</span>
                                      <span class="in">in</span>
                                      <span>1</span>
                                  </div>
                              </div> -->
                              <div class="courser-bottom-half"> <a onclick="javascript:void(0)" > 
                                <label class="block-head" id="item_tutors_list" ><?php echo $course['cb_title']; ?></label>
                                <p class="sub-head-des-pre" id="item_tutors_list"><?php //echo $tutors_name; ?></p></a>
                              </div>
                          </div>
                      </div>
                  </div>
                  <!-- course card holder ends-->

                  <!-- order-summary starts -->
                  <div class="order-summary">
                      <div id="enroll_modal_content" class="text-center">
                          <div id="tax-table" class="form-group table-holder" style="padding-right: 0;">
                              <table class="billing-table" style="width:100%;border:0px">
                                  <tbody>
                                      <tr>
                                          <td class="text-left">Course Price</td>
                                          <td class="text-right text-green">
                                              <span class="rupee" style="font-family: 'Roboto', sans-serif;">₹ </span> 
                                              <span class="price">9500</span>
                                          </td>
                                      </tr>
                                      
                                      <tr id="new-price" class="border-row" style="display:none;">
                                          <td class="text-left">Price After Discount</td>
                                          <td class="text-right text-green">₹ <span id="new-course-price"></span></td>
                                      </tr>
                                      <tr>
                                          <td class="text-left">Discount (10%)</td>
                                          <td class="text-right text-green"> 
                                              <span class="plus">- </span> 
                                              <span class="rupee" style="font-family: 'Roboto', sans-serif;">₹</span>  
                                              <span class="price">1000 </span>
                                          </td>
                                      </tr>
                                      <tr class="promocode-preview" id="promocode_offer">
                                          <td class="text-left">
                                              <span class="promocode">
                                                  <span id="promocode_text">GET100</span>
                                                  <img src="https://SGlearningapp.enfinlabs.com/assets/themes/ofabee/images/scissors.png">
                                              </span>
                                              <a href="#" class="remove-coupon" onclick="resetCoupon();">Remove</a></td>
                                          <td class="text-right text-green"> 
                                              <span>-</span>
                                              <span class="rupee" style="font-family: 'Roboto', sans-serif;">₹</span>   
                                              <span id="promocode_reduction">100</span>
                                          </td>
                                      </tr>
                                      <tr>
                                          <td class="text-left">Tax</td>
                                          <td class="text-right text-green">
                                              <span class="rupee" style="font-family: 'Roboto', sans-serif;">₹</span>  
                                              <span class="price" id="tax_price">150 </span>
                                          </td>
                                      </tr>
                                      
                                  </tbody>
                              </table>

                              <div class="haveacoupon">
                                  <span>Have a Coupon?</span>
                              </div>

                              <div class="form-group promo-column" >
                                  <input type="text" class="form-control" style="width:80%;" maxlength="10" id="promo_code" name="promo_code" placeholder="Apply Promo Code">
                                  <button id="promo_code_btn" onclick="applyCoupon()" class="custom-btn">Apply</button>
                              </div>

                              <div class="total-column">
                                  <div class="text-left"><b>Total</b></div>
                                  <div class="text-right">
                                      <span class="rupee" style="font-family: 'Roboto',sans-serif;"><b>₹</b></span>
                                      <span id="net_total"><b>9260</b></span>
                                  </div>
                              </div>

                              <div class="text-center">
                                  <button data-dismiss="modal" type="" class="custom-btn btnorange checkout-btn" onclick="applyPromo()">Checkout</button>
                              </div>

                          </div>
                      </div>
                  </div>
                  <!-- order summary ends -->
                </div>
            </div>

            <div class="modal-footer text-center">
                <img src="<?php echo assets_url().'themes/'.$this->config->item('theme'); ?>/images/payment-method.svg" alt="">
            </div>

        </div>
    </div>
    <!-- Order Summary ends here-->



<div class="modal fade" id="previewModal" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <div id="player" data-plyr-provider="vimeo" data-plyr-embed-id=""></div>
        </div>
       
      </div>
    </div>
  </div>
</div>
  <link rel="stylesheet" href="<?php echo assets_url() ?>plyr/1.8.2/skin.css">
  
  <link rel="stylesheet" href="<?php echo assets_url() ?>plyr/plyr.css">
  <script src="<?php echo assets_url() ?>plyr/plyr.min.js"></script>
  
<?php include 'footer.php';?>
<script>
    $(document).ready(function () {
      // const player ;
      // Attach Button click event listener 
      $(".curriculam-preview").click(function(){
        // show Modal
          $('#previewModal').modal('show');
          var video_id = $(this).attr('data-field');
          $('#player').attr('data-plyr-embed-id',video_id);
          player = new Plyr('#player', {
              /* options */
          });

          player.play();
      });

      $('#previewModal').on('hidden.bs.modal', function () {
        
        player.destroy();
      })
    });
  
  </script>
  <script>
    var __codeVersion = '<?php echo config_item('code_version')?>';



    window.onscroll = function() {
            stickytab();
            enrollfixed();
            validitytablefixed();
         };
         var collapse_tab = document.getElementById("tab-container");
         var mobilesticky_trigger = document.getElementById("course-heading-trigger");
         var fixedtop = mobilesticky_trigger.offsetTop;
         function stickytab() {
            if (window.pageYOffset > fixedtop) {
              collapse_tab.classList.add("sticky");
            }
            else {
              collapse_tab.classList.remove("sticky");
            }
         }
         var enrollsticky = document.getElementById("sticky-enroll-strip");
         function enrollfixed() {
            if (window.pageYOffset > fixedtop) {
               enrollsticky.classList.add("sticky");
            }
            else {
               enrollsticky.classList.remove("sticky");
            }
         }
         var validity_table = document.getElementById("course-valdity-table");
         var stickybill = validity_table.offsetTop + 225;
         function validitytablefixed() {
            if (window.pageYOffset > stickybill) {
              validity_table.classList.add("sticky");
            }
            else {
              validity_table.classList.remove("sticky");
            }
         }

         // ====================
         $.fn.isOnScreen = function(){
            var win = $(window);
            var viewport = {
            top : win.scrollTop(),
            left : win.scrollLeft()
          };
          viewport.right = viewport.left + win.width();
          viewport.bottom = viewport.top + win.height();
          var bounds = this.offset();
          bounds.right = bounds.left + this.outerWidth();
          bounds.bottom = bounds.top + this.outerHeight();
          return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
          };
          $(document).ready(function(){
            $(window).scroll(function(){
              if ($('#price-table-trigger').isOnScreen()) {
                // console.log('footer is visible');
                validity_table.classList.remove("sticky");
              } else {
            
              }
            });
          });
          // ========================

          //Course pricing table Top Alignment on Small Devices starts here	
          // if(window.innerWidth < 992) {
          //   var coursepricetable = document.getElementById('course-pricing-table').offsetHeight;
          //   document.getElementById('tab-content').style.top = coursepricetable+105+'px';
          //   console.log(coursepricetable);
          // }else {
          //   document.getElementById('tab-content').style.top = 'auto';
          //   console.log(coursepricetable);
          // }	
          //Course pricing table Top Alignment on Small Devices ends here


          // curricculum collapse icon toggle		
          $(document).ready(function() {		
            $('.panel-collapse').on('show.bs.collapse', function () {		
              $(this).siblings('.panel-heading').addClass('active');		
            });		
            $('.panel-collapse').on('hide.bs.collapse', function () {		
              $(this).siblings('.panel-heading').removeClass('active');		
            });		
          });		
          // curricculum collapse icon toggle ends		
          //tab offset to top when tab toggle		
          var navtab_list = $('.nav-tabs>li.active').offset().top;		
          $(".nav-tabs>li,active").click(function(e){		
            e.preventDefault();		
            $("html, body").animate({ scrollTop: navtab_list },"50000");		
          });		
          //tab offset to top when tab toggle ends

    function showPromo()
    {
      $(".haveacoupon").hide();
      $("#promo-column").show();
    }

    async function AndroidNativeShare(Title,URL,Description){
        if(typeof navigator.share==='undefined' || !navigator.share){
            alert('Your browser does not support Android Native Share, it\'s tested on chrome 63+');
        } else if(window.location.protocol!='https:'){
            alert('Android Native Share support only on Https:// protocol');
        } else {
            if(typeof URL==='undefined'){
            URL = window.location.href;
            }
            if(typeof Title==='undefined'){
            Title = document.title;
            }
            if(typeof Description==='undefined'){
            Description = 'Share your thoughts about '+Title;
            }
            const TitleConst = Title;
            const URLConst = URL;
            const DescriptionConst = Description;

            try{
            await navigator.share({title:TitleConst, text:DescriptionConst, url:URLConst});
            } catch (error) {
            //console.log('Error sharing: ' + error);
            return;
            }
        }
    }
    $(document).ready(function(){
        $("#mobile-share-trigger").click(function(BodyEvent){
            var meta_desc,meta_title,meta_url
            if(document.querySelector('meta[property="og:description"]')!=null) {
                meta_desc = document.querySelector('meta[property="og:description"]').content;
            }
            if(document.querySelector('meta[property="og:title"]')!=null) {
                meta_title = document.querySelector('meta[property="og:title"]').content;
            }
            if(document.querySelector('meta[property="og:meta_url"]')!=null) {
                meta_url = document.querySelector('meta[property="og:meta_url"]').content;
            }
            AndroidNativeShare(meta_title, meta_url,meta_desc);
        });
    })
</script>