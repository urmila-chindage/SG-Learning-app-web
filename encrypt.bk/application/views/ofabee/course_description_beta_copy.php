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
</style>
<script type="text/javascript">
  let __course_id = '<?php echo $course['id']; ?>';
</script>

<section> 
  <div class="head-gradient">
    <div class="fundamentals-skill">
      <div class="container fundamentals-altr">
        <h2 class="fundamentals-head enrolling-course-title">
          <?php echo $course['cb_title']; ?>
        </h2>
        <?php
         //echo '<pre>'; print_r($course['rating']);die; 

$total_rating = 0;
$total_rate = 0;
$ratings = 0;
$average_rating = 0;
foreach ($course['rating'] as $cr) {
    $total_rating += $cr['ratings'];
    $total_rate += $cr['rating'];
    $ratings += $cr['ratings'] * $cr['rating'];
}
if ($total_rating > 0)
{
  $average_rating = round($ratings / $total_rating, 1);
}
 
//die;
$rating = array();
$rating[1]['percentage'] = 0;
$rating[1]['count'] = 0;
$rating[2]['percentage'] = 0;
$rating[2]['count'] = 0;
$rating[3]['percentage'] = 0;
$rating[3]['count'] = 0;
$rating[4]['percentage'] = 0;
$rating[4]['count'] = 0;
$rating[5]['percentage'] = 0;
$rating[5]['count'] = 0;
foreach ($course['rating'] as $cr) {
    $rating[$cr['cc_rating']]['percentage'] = round(($cr['ratings'] * 100) / $total_rating, 1);
    $rating[$cr['cc_rating']]['count'] = $cr['ratings'];
}
$sum_rating = count($course['rating']) != 0 ? round(($total_rate / count($course['rating'])), 1) : 0.0;
?>
        <ul class="fundamental-sub-strip fundamental-sub-strip-responsive">
          <li class="padding-left-remove">
            <div class="star-ratings-sprite margin-right">
              <span style="width:<?php echo round(($average_rating * 20), 2) ?>%" class="star-ratings-sprite-rating">
              </span>
            </div>
            <span class="font-bold count-bold">
              <?php echo $average_rating;//number_format((float) $sum_rating, 1, '.', ''); ?>
            </span>
            <span class="slash">|
            </span>
            <span class="count-bold">
              <?php echo $total_rating; ?>
            </span>
            <span class="ratings-no">
              <?php echo $total_rating > 1 ? 'Ratings' : 'Rating'; ?>
            </span>
          </li>
          <div class="certification-row-xs">
            <?php /* ?>
<li style="display:none;">
<i class="icon-user  margin-right">
</i>
<span class="count-bold">
<?php echo $course['enrolled_students']; ?>
</span>
<span class="student-text">
<?php echo $course['enrolled_students'] > 1 ? 'Students enrolled' : 'Student enrolled'; ?>
</span>
</li>
<?php */?>
            <?php if ($course['cb_has_certificate'] == 1) {?>
            <li>
              <svg x="0px" y="0px" width="21px" height="17px" viewBox="0 0 21 17" style="enable-background:new 0 0 21 17;vertical-align: sub;" xml:space="preserve">
                <g>
                  <g>
                    <path class="svg-badge" d="M10.5,11.2c3,0,5.5-2.4,5.5-5.5s-2.4-5.5-5.5-5.5S5,2.7,5,5.7S7.5,11.2,10.5,11.2z M10.5,2.8c1.6,0,3,1.3,3,3
                                               s-1.3,3-3,3s-3-1.3-3-3S8.9,2.8,10.5,2.8z"/>
                    <path class="svg-badge" d="M3.8,14.7l2.2,0l1,2l2.7-4.3c-1.3-0.2-2.5-0.7-3.5-1.5L3.8,14.7z"/>
                    <path class="svg-badge" d="M14,16.7l1-2l2.2,0L14.8,11c-1,0.8-2.2,1.4-3.5,1.5L14,16.7z"/>
                  </g>
                </g>
              </svg>
              <span class="certificate-label">Certified course
              </span>
            </li>
            <?php }?>
            <li class="share-hover">
              <i class="icon-share icon-share-white margin-right">
              </i>
              <span class="share-label">Share
              </span>
              <ul class="share-dropdown">
                <li>
                  <a target="_blank" href="<?php echo 'https://www.facebook.com/sharer/sharer.php?u=' . current_url(); ?>" class="fb-share-button facebook-active">
                    <i class="icon-facebook">
                    </i>
                  </a>
                </li>
                <li>
                  <a target="_blank" href="<?php echo 'https://twitter.com/intent/tweet?text=' . current_url(); ?>" class="twitter-share-button share-twitter">
                    <i class="icon-twitter">
                    </i>
                  </a>
                </li>
                <li>
                  <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . current_url(); ?>" class="google-ser" style="border-right:none">
                    <i class="icon-google">
                    </i>
                  </a>
                </li>
              </ul>
            </li>
          </div>

        </ul>
        <div class="text-center">
          <ul class="share-dropdown2">
            <li>
              <a href="<?php echo 'https://www.facebook.com/sharer/sharer.php?u=' . current_url(); ?>" class="facebook-active">
                <i class="icon-facebook">
                </i>
              </a>
            </li>
            <li>
              <a href="<?php echo 'https://twitter.com/intent/tweet?text=' . current_url(); ?>" class="share-twitter">
                <i class="icon-twitter">
                </i>
              </a>
            </li>
            <li>
              <a href="<?php echo 'https://plus.google.com/share?url=' . current_url(); ?>" class="google-ser" style="border-right:none">
                <i class="icon-google">
                </i>
              </a>
            </li>
          </ul>
        </div>
        <!--text-center-->
        <ul class="fundamental-sub-strip margin-bottom-fundamental padding- instructedby-row">
          <li class="padding-left-remove">
            <span class="instructed-label">
              <?php echo lang('instucted_by'); ?>
            </span>
          </li>
          <?php if (!empty($course["tutors"])) {
    $tutors_names = array();
    ?>
          <li>
            <?php
$i = 0;
    $j = count($course["tutors"]);
    foreach ($course["tutors"] as $tutors) {echo $i == 0 || $i == $j ? '' : ' <span class="blue-text">,</span> ';
        $i++;
        $tutors_names[] = $tutors['us_name'];
        ?>
            <a href="<?php echo site_url() . 'teachers/view/' . $tutors['id']; ?>">
              <span class="blue-text">
                <?php echo $tutors['us_name']; ?>
              </span>
            </a>
            <?php
}
    $tutors_name = (!empty($tutors_names)) ? implode(',', $tutors_names) : " ";
    ?>
          </li>
          <?php } else {
    $tutors_name = $this->config->item('us_name');
    ?>
          <li>
            <a href="javacript:void(0);">
              <span class="blue-text">
                <?php echo $this->config->item('us_name'); ?>
              </span>
            </a>
          </li>
          <?php }?>
        </ul>
        <div class="img-and-vid xs-visible-only">
          <?php if (isset($course['cb_promo']) && $course['cb_promo'] != '') {?>
          <div class="embed-responsive embed-responsive-16by9">
            <iframe width="auto" height="auto" src="<?php echo isset($course['cb_promo']) ? generate_youtube_url($course['cb_promo']) : '' ?>" frameborder="0" allowfullscreen>
            </iframe>
          </div>
          <!--video-tag-->
          <?php } else {?>
          <img src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $course['cb_image'] ?>" class="img-responsive">
          <?php }?>
        </div>
        <!--img-and-vid-->
        <div class="right-side right-side-responsive xs-visible-only">
          <a
             <?php echo !empty($session) ? 'onclick="enrollToCourse()"' : ''; ?>  href="
             <?php echo !empty($session) ? 'javascript:void(0)' : site_url('login'); ?>" class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large enroll-btn-dashboard">Enroll
          </a>
        <?php if ($course['cb_preview']): ?>
        <?php
          $link = isset($session['id']) ? site_url('materials/course/' . $course['id']) : site_url('login');
          
        ?>
        <!-- <a class="btn orange-btn-altr orange-btn-white right-side-margin-top course-page-btn-large" href="<?php //echo $link; ?>">Preview 1
        </a> -->
        <?php endif;?>
      </div>
      <!--right-side-->

      <div class="course-share" id="mobile-share-trigger" style="padding-top: 14px;    clear: both;">
          <span>Share</span>
          <span style="display: inline-block;vertical-align: middle;">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="512px" id="Layer_1" style="enable-background:new 0 0 512 512;fill: #fff;width: 25px;height: 25px;" version="1.1" viewBox="0 0 512 512" width="512px" xml:space="preserve"><g><path d="M352,377.9H102.4V198.2h57.5c0,0,14.1-19.7,42.7-38.2H83.2c-10.6,0-19.2,8.5-19.2,19.1v217.9c0,10.5,8.6,19.1,19.2,19.1   h288c10.6,0,19.2-8.5,19.2-19.1V288L352,319.4V377.9z M320,224v63.9l128-95.5L320,96v59.7C165.2,155.7,160,320,160,320   C203.8,248.5,236,224,320,224z"></path></g></svg>
          </span>
      </div>

    </div>
    <!--container fundamentals-altr-->
  </div>
  <!--fundamentals-skill fundamentals-customized-->
  </div>
<!--head-gradient-->
</section>
<section class="sticky-enroll-strip" id="sticky-enroll-strip">
  <div class="pricing-column">
    <span class="original-price">

      <?php if ($course['cb_is_free'] == '1'): ?>
      <span class="rupee-amount">FREE
      </span>
      <?php else: ?>
      <span class="rupee" style="font-family: 'Roboto', sans-serif;">&#8377;</span>
      <span class="rupee-amount">
        <?php echo $course['cb_discount'] > 0 ? $course['cb_discount'] : $course['cb_price']; ?>
      </span>
      <?php endif;?>
    </span>
    <?php if ($course['cb_is_free'] != '1' && $course['cb_discount'] > 0): ?>

    <span class="discounted-price-new">
      <span class="rupee" style="font-family: 'Roboto', sans-serif;">&#8377;</span>
      <span class="rupee-amount"><?php echo $course['cb_price'] ?></span>
    </span>
    <?php endif;?>
  </div>
  <div class="right-side right-side-responsive ">
    <?php $enroll_button_text = ($course['cb_is_free'] == '1') ? 'Enroll' : 'Buy Now';?>
    <a
       <?php echo !empty($session) ? 'onclick="enrollToCourse()"' : ''; ?>  href="
    <?php echo !empty($session) ? 'javascript:void(0)' : site_url('login'); ?>" class="btn btn-orange2 right-side-margin-top course-page-btn-large enroll-btn-dashboard"><?php echo $enroll_button_text; ?>
    </a>
  <?php if ($course['cb_preview']): ?>
  <?php
$link = isset($session['id']) ? site_url('materials/course/' . $course['id']) : site_url('login');
?>

  <?php endif;?>
  </div>
</section>
<section>
  <div class="fundamentals-skill fundamentals-customized  course-validity-wrapper">
    <div class="container fundamentals-altr">
      <div class="">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
          <div class="img-and-vid  xs-hidden-only">
            <?php if (isset($course['cb_promo']) && $course['cb_promo'] != '') {?>
            <div class="embed-responsive embed-responsive-16by9">
              <iframe width="auto" height="auto" src="<?php echo isset($course['cb_promo']) ? generate_youtube_url($course['cb_promo']) : '' ?>" frameborder="0" allowfullscreen>
              </iframe>
            </div>
            <!--video-tag-->
            <?php } else {?>
            <img src="<?php echo (($course['cb_image'] == 'default.jpg') ? default_course_path() : course_path(array('course_id' => $course['id']))) . $course['cb_image'] ?>" class="img-responsive">
            <?php }?>
          </div>
          <!--img-and-vid-->
        </div>
        <!--columns-->
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        <?php
if ($course['enrolled'] != 1) {
    ?>
          <div class="right-side right-side-responsive  xs-hidden-only">
            <?php
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
            <?php if ($course['cb_is_free'] != '1'): ?>
            <?php if ($course['cb_discount'] > 0): ?>
            <label class="bold-price">
              <span class="rupee" style="font-family: 'Roboto', sans-serif;">&#8377;</span>
              <?php echo $course['cb_discount'] ?>
            </label>
            <label class="discount-cut">
              <span class="rupee" style="font-family: 'Roboto', sans-serif;">&#8377;</span>
              <?php echo $course['cb_price'] ?>
            </label>
            <?php else: ?>
            <label class="bold-price">
              <span class="rupee" style="font-family: 'Roboto', sans-serif;">&#8377;</span>
              <?php echo $course['cb_price'] ?>
            </label>
            <?php endif;?>
            <?php if ($course['cb_tax_method'] == '0'): ?>
            <span style="font-size:13px;">Inclusive of all taxes
            </span>
            <?php else: ?>
            <span style="font-size:13px;">
              Exclusive of all taxes
            </span>
            <?php endif;?>
            <?php endif;?>
            <?php
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
            <a <?php echo ($error_expired == 1)?'style="pointer-events: none;" disabled':'';?>
               <?php echo !empty($session) ? 'onclick="enrollToCourse()"' : ''; ?>  href="
            <?php echo !empty($session) ? 'javascript:void(0)' : site_url('login'); ?>" class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large">Enroll
            </a>
            <?php $link = isset($session['id']) ? site_url('materials/course/' . $course['id']) : site_url('login'); ?>
            <?php if (($course['cb_preview']) && ($course['cb_is_free'] != '1')):?>
            
                <?php if (!isset($session['id']) || empty($session['id'])): ?> 
                  <a class="btn  orange-btn-altr orange-btn-white right-side-margin-top course-page-btn-large" href="<?php echo $link; ?>">Preview</a>
                <?php else: ?>
                    <?php if(isset($course['remaning_preview_time']['cpt_course_time']) && $course['remaning_preview_time']['cpt_course_time'] < $course['cb_preview_time']): ?>
                    <a class="btn  orange-btn-altr orange-btn-white right-side-margin-top course-page-btn-large" href="<?php echo $link; ?>">Preview</a>
                    <?php endif; ?>
                    <?php if(!isset($course['remaning_preview_time']['cpt_course_time'])){ ?>
                        <a class="btn  orange-btn-altr orange-btn-white right-side-margin-top course-page-btn-large" href="<?php echo $link; ?>">Preview</a>
                    <?php } ?>
                <?php endif; ?>

            <?php endif;?>
        </div>
        <!--right-side-->
        <?php
}
?>
        <div class="course-vald right-side-margin-top course-valid-responsive">
          <div class="row course-val-master-bottom">
            <div class="col-md-7 col-sm-4 col-xs-6 supermin-left">
              <label class="course-bold d-align">
                <span class="course-validity-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="74" height="74" viewBox="0 0 74 74" style=" width: 22px; height: 22px;">
                    <g>
                      <title>background
                      </title>
                      <rect fill="none" id="canvas_background" height="76" width="76" y="-1" x="-1">
                      </rect>
                    </g>
                    <g>
                      <title>Layer 1
                      </title>
                      <path fill="#8c8c8c" id="svg_2" d="m63.3,3l-52.6,0c-4.2,0 -7.7,3.5 -7.7,7.7l0,52.5c0,4.3 3.5,7.7 7.7,7.7l52.5,0c4.3,0 7.7,-3.5 7.7,-7.7l0,-52.5c0.1,-4.2 -3.4,-7.7 -7.6,-7.7zm3.7,60.3c0,2.1 -1.7,3.7 -3.7,3.7l-52.6,0c-2,0 -3.7,-1.7 -3.7,-3.7l0,-33.2l54.1,0c1.1,0 2,-0.9 2,-2s-0.9,-2 -2,-2l-54.1,0l0,-15.4c0,-2 1.7,-3.7 3.7,-3.7l52.5,0c2.1,0 3.7,1.7 3.7,3.7l0,52.6l0.1,0z" class="">
                      </path>
                      <path fill="#8c8c8c" id="svg_3" d="m20.3,10.3c-3.4,0 -6.2,2.8 -6.2,6.2c0,3.4 2.8,6.2 6.2,6.2s6.2,-2.8 6.2,-6.2c0,-3.4 -2.8,-6.2 -6.2,-6.2zm0,8.4c-1.2,0 -2.2,-1 -2.2,-2.2s1,-2.2 2.2,-2.2s2.2,1 2.2,2.2s-1,2.2 -2.2,2.2z" class="">
                      </path>
                      <path fill="#8c8c8c" id="svg_4" d="m53.7,10.3c-3.4,0 -6.2,2.8 -6.2,6.2c0,3.4 2.8,6.2 6.2,6.2c3.4,0 6.2,-2.8 6.2,-6.2c0,-3.4 -2.8,-6.2 -6.2,-6.2zm0,8.4c-1.2,0 -2.2,-1 -2.2,-2.2s1,-2.2 2.2,-2.2s2.2,1 2.2,2.2s-1,2.2 -2.2,2.2z" class="">
                      </path>
                      <rect id="svg_11" height="7.862181" width="7.862181" y="35.732328" x="15.671744" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                      <rect id="svg_12" height="7.862181" width="7.862181" y="35.732328" x="32.399788" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                      <rect id="svg_13" height="7.862181" width="7.862181" y="35.732328" x="49.127831" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                      <rect id="svg_21" height="7.862181" width="7.862181" y="52.460371" x="15.671744" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                      <rect id="svg_22" height="7.862181" width="7.862181" y="52.293091" x="32.399788" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                      <rect id="svg_23" height="7.862181" width="7.862181" y="52.293091" x="49.127831" stroke-opacity="null" stroke-width="null" stroke="null" fill="#8c8c8c">
                      </rect>
                    </g>
                  </svg>
                </span>
                <span>
                <?php
                /*
if ($course['cb_access_validity'] == 2) {
    echo $course['cb_validity_expired'] ? 'Expired' : 'Valid Till';
} else {
    echo '';
}*/
?>Validity
                </span>
              </label>
            </div>
            <!--columns-->
            <div class="col-md-5 col-sm-4 col-xs-6 supermin-right">
              <label class="course-bold text-right display-block">
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
}
?>
              </label>
            </div>
            <!--columns-->
          </div>
          <!--row-->
          <?php if ($course['cb_video_count'] != '' && $course['cb_video_count'] != 0) {?>
          <div class="row course-val-bottom-margin course-val-info">
            <div class="col-md-7 col-sm-5 col-xs-6 supermin-left">
              <!-- video icon -->
              <span class="course-icon video-icon">
              </span>
              <!-- video icon ends -->
              <label class="fundamental-right-font-size">Videos
              </label>
            </div>
            <!--columns-->
            <div class="col-md-5 col-sm-3 col-xs-6 supermin-right">
              <label class="text-right display-block  duration-color"><?php echo $course['cb_video_count']; ?>
              </label>
            </div>
            <!--columns-->
          </div>
          <?php }?>
          <?php if ($course['cb_docs_count'] != '' && $course['cb_docs_count'] != 0) {?>
          <div class="row course-val-bottom-margin course-val-info">
            <div class="col-md-7 col-sm-5 col-xs-6 supermin-left">
              <!-- document icon -->
              <span class="course-icon doc-icon">
              </span>
              <!-- document icon ends -->
              <label class="fundamental-right-font-size">Docs
              </label>
            </div>
            <!--columns-->
            <div class="col-md-5 col-sm-3 col-xs-6 supermin-right">
              <label class="text-right display-block duration-color"><?php echo $course['cb_docs_count']; ?>
              </label>
            </div>
            <!--columns-->
          </div>
          <?php }?>
          <?php if ($course['cb_assessment_count'] != '' && $course['cb_assessment_count'] != 0) {?>
          <div class="row course-val-bottom-margin course-val-info">
            <div class="col-md-7 col-sm-5 col-xs-6 supermin-left">
              <!-- assesment icon -->
              <span class="course-icon assessment-icon">
              </span>
              <!-- assesment icon ends -->
              <label class="fundamental-right-font-size"><?php echo lang('assessments'); ?>
              </label>
            </div>
            <!--columns-->
            <div class="col-md-5 col-sm-3 col-xs-6 supermin-right">
              <label class="text-right display-block duration-color"><?php echo $course['cb_assessment_count']; ?>
              </label>
            </div>
            <!--columns-->
          </div>
          <?php }?>
          <?php if (count($course['cb_language']) != 0): ?>
          <?php
$languages = array();
$all_languages = array();
if (count($course['cb_language']) == 1) {
    $languages[] = $course['cb_language'][0]['cl_lang_name'];
} else {
    foreach ($course['cb_language'] as $language):
        $languages[] = substr($language['cl_lang_name'], 0, 2);
        $all_languages[] = $language['cl_lang_name'];
    endforeach;

}
// echo "<pre>";print_r($course['cb_language']);exit;
?>
          <div class="row course-val-bottom-margin">
            <div class="col-md-7 col-sm-5 col-xs-6 supermin-left">
              <!-- assesment icon -->
              <span>
                <svg version="1.1" x="0px" y="0px" class="svg-common" width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
                  <g>
                    <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z">
                    </path>
                    <path fill="#808080" d="M10.5,0.5c-5.5,0-10,4.5-10,10s4.5,10,10,10c5.5,0,10-4.5,10-10S16,0.5,10.5,0.5z M17.4,6.5h-2.9
                                            c-0.3-1.3-0.8-2.4-1.4-3.6C14.9,3.6,16.5,4.8,17.4,6.5z M10.5,2.5c0.8,1.2,1.5,2.5,1.9,4H8.6C9,5.1,9.7,3.7,10.5,2.5z M2.8,12.5
                                            c-0.2-0.6-0.3-1.3-0.3-2s0.1-1.4,0.3-2h3.4C6.1,9.2,6,9.8,6,10.5s0.1,1.3,0.1,2H2.8z M3.6,14.5h3c0.3,1.3,0.8,2.5,1.4,3.6
                                            C6.1,17.4,4.5,16.2,3.6,14.5z M6.5,6.5h-3c1-1.7,2.5-2.9,4.3-3.6C7.3,4.1,6.9,5.3,6.5,6.5z M10.5,18.5c-0.8-1.2-1.5-2.5-1.9-4h3.8
                                            C12,15.9,11.3,17.3,10.5,18.5z M12.8,12.5H8.2c-0.1-0.7-0.2-1.3-0.2-2s0.1-1.4,0.2-2h4.7c0.1,0.6,0.2,1.3,0.2,2
                                            S12.9,11.8,12.8,12.5z M13.1,18.1c0.6-1.1,1.1-2.3,1.4-3.6h2.9C16.5,16.1,14.9,17.4,13.1,18.1z M14.9,12.5c0.1-0.7,0.1-1.3,0.1-2
                                            s-0.1-1.3-0.1-2h3.4c0.2,0.6,0.3,1.3,0.3,2s-0.1,1.4-0.3,2H14.9z">
                    </path>
                  </g>
                </svg>
              </span>
              <!-- assesment icon ends -->
              <label class="fundamental-right-font-size">Language
              </label>
            </div>
            <!--columns-->
            <div class="col-md-5 col-sm-3 col-xs-6 supermin-right">
              <label class="text-right display-block duration-color lang-ellipsis" title="<?php echo isset($all_languages) ? implode(', ', $all_languages) : ''; ?>"><?php echo implode(', ', $languages); ?>
              </label>
            </div>
            <!--columns-->
          </div>
          <?php endif;?>
        </div>
      </div>
      <!--columns-->
    </div>
    <!--row-->
  </div>
  <!--container-->
  </div>
<!--fundamentals-->
</section>
<?php //echo $course_details['videos_length'];die; ?>
<?php $length = isset($course['cb_video_duration']) ? gmdate("H.i", $course['cb_video_duration']) : '00.00';?>
<section>
  <div class="about-course">
    <div class="container fundamentals-altr">
      <div class="change-size-of-abt-course">
        <h3 class="formpage-heading dashboard-about-title">About Course
        </h3>
        <div class="icon-text-para">
          <?php if ($length != '00.00'): ?>
          <span class="holding-icon-text">
            <svg version="1.1" class="svg-common" x="0px" y="0px" width="21px" height="18px" viewBox="0 0 21 21" enable-background="new 0 0 21 21" xml:space="preserve">
              <g>
                <path fill="#808080" d="M10.49,0.5c-5.52,0-9.99,4.48-9.99,10s4.47,10,9.99,10c5.53,0,10.01-4.48,10.01-10S16.02,0.5,10.49,0.5z
                                        M10.5,18.5c-4.42,0-8-3.58-8-8s3.58-8,8-8s8,3.58,8,8S14.92,18.5,10.5,18.5z"/>
                <path fill="none" d="M-1.5-1.5h24v24h-24V-1.5z"/>
                <path fill="#808080" d="M11,5.5H9.5v6l5.25,3.15l0.75-1.23L11,10.75V5.5z"/>
              </g>
            </svg>
            &nbsp;
            Course Length
            <span class="hourse-bold">
              <?php echo $length . ' Hrs' ?>
            </span>
          </span>
          <!--holding-icon-text-->
          <?php endif;?>
        </div>
        <div style="max-height:600px;" class="redactor-content show-more-data-wrap show-more-collapse">
          <?php echo $course['cb_description']; ?>
          <?php if (strlen($course['cb_description']) > 1000) {?>
          <a href="javascript:void(0)" class="Showmore-btm">Read full details
          </a>
          <?php }?>
        </div>
        <!--show-more-data-wrap-->
      </div>
      <!--change-size-of-abt-course-->
    </div>
    <!--container-->
  </div>
  <!--about-course-->
</section>
<section>
  <div class="curriculum">
    <div class="container fundamentals-altr">
      <div class="change-size-of-abt-course" id="curriculum_div">
        <h3 class="formpage-heading dashboard-curriculam-title">Curriculum
        </h3>
      </div>
      <!--change-size-of-abt-course-->
    </div>
    <!--container-->
  </div>
  <!--curriculum-->
</section>
<section>
  <div class="reviews">
    <div class="container fundamentals-altr">
      <div class="change-size-of-abt-course">
        <h3 class="formpage-heading rating-section-title-xs">Ratings</h3>
        <div class="bar-rating clearfix">
            <div class="star-rating-left">
                <span class="big-rating-no"><?php echo $average_rating; //echo number_format((float) $sum_rating, 1, '.', ''); ?></span><!--big-rating-no-->
                  <div class="star-ratings-sprite-two">
                      <span style="width:<?php echo round(($average_rating * 20), 2) ?>%" class="star-ratings-sprite-rating-two"></span>
                    </div><!--star-ratings-sprite-->
                    <span class="strip-font-grey"><?php echo $total_rating; ?> <?php echo $total_rating > 1 ? 'Ratings' : 'Rating'; ?></span>
              </div><!--star-rating-left-->
              <div class="bar-rating-right">
                  <?php for ($i = 5; $i > 0; $i--): ?>
                      <span class="bar-star-number-warap">
                          <span class="starAndNum">
                              <span class="strip-font-grey star-barrating-text"><?php echo $i; ?></span><span class="svg-common"><svg version="1.1" x="0px" y="0px" width="24px" height="24px" viewBox="0 0 21 19" enable-background="new 0 0 21 19" xml:space="preserve"><path fill="#ffc000" xmlns="http://www.w3.org/2000/svg" d="M9.362,9.158c0,0-3.16,0.35-5.268,0.584c-0.19,0.023-0.358,0.15-0.421,0.343s0,0.394,0.14,0.521    c1.566,1.429,3.919,3.569,3.919,3.569c-0.002,0-0.646,3.113-1.074,5.19c-0.036,0.188,0.032,0.387,0.196,0.506    c0.163,0.119,0.373,0.121,0.538,0.028c1.844-1.048,4.606-2.624,4.606-2.624s2.763,1.576,4.604,2.625    c0.168,0.092,0.378,0.09,0.541-0.029c0.164-0.119,0.232-0.318,0.195-0.505c-0.428-2.078-1.071-5.191-1.071-5.191    s2.353-2.14,3.919-3.566c0.14-0.131,0.202-0.332,0.14-0.524s-0.23-0.319-0.42-0.341c-2.108-0.236-5.269-0.586-5.269-0.586    s-1.31-2.898-2.183-4.83c-0.082-0.173-0.254-0.294-0.456-0.294s-0.375,0.122-0.453,0.294C10.671,6.26,9.362,9.158,9.362,9.158z"></path></svg></span>
                          </span><!--starAndNum-->
                          <span class="star-progress">
                              <span class="orange-progress" style="width:<?php echo $rating[$i]['percentage'] ?>%"></span>
                          </span><!--star-progress-->
                          <span class="strip-font-grey percent-align"><?php echo $rating[$i]['count']; ?></span>
                          </span>
                  <?php endfor;?>
              </div><!--bar-rating-right-->
          </div><!--bar-rating-->
      </div>
      <!--change-size-of-abt-course-->
    </div>
    <!--container-->
  </div>
  <!--reviews-->
</section>



 <!-- review starts here-->

<?php if(isset($course['course_reviews']['reviews']) && !empty($course['course_reviews']['reviews'])): $reviews = $course['course_reviews']['reviews']; //print_r($reviews);?>

<section>
  <div class="reviews">
      <div class="container fundamentals-altr">
          <div class="change-size-of-abt-course">
          <h3 class="formpage-heading rating-section-title-xs">Reviews</h3>
              <div class="clearfix"></div>

              <div class="review-container">
                  <div id="tab-reviews" class="plugin-reviews section">
                      <div class="plugin-reviews" id="tabreviews">
                        <?php for($i = 0; $i < count($reviews); $i++):?>

                          <div class="review-holder">
                              <div class="review-title-row">
                                  <div class="review-avatar">
                                    <img alt="<?php echo $reviews[$i]['cc_user_name'];?> profile pic" src="<?php echo ($reviews[$i]['cc_user_image'] ?  user_path() : default_user_path()).$reviews[$i]['cc_user_image'];?>" class="avatar avatar-60 photo img-responsive">					
                                  </div>
                                  <div class="review-name-rating">
                                      <div class="reviewer-name"><?php echo $reviews[$i]['cc_user_name'];?></div>
                                      <div class="review" style="display: flex;align-items: center;">
                                          <div class="star-ratings-sprite-two">
                                            <span style="width: <?php echo (100 / 5 ) * $reviews[$i]['cc_rating'];?>%;" class="star-ratings-sprite-rating-two"></span>
                                          </div>
                                          <small style="/*display:none;*/ padding-left: 15px; margin-top:5px;"><?php echo time_elapsed_string($reviews[$i]['created_date']);?></small>
                                          
                                      </div>
                                  </div>
                              </div>
                              <div class="review-content-row">
                                  <p class="review-content"><?php echo $reviews[$i]['cc_reviews'];?></p>
                              </div>
                            <?php if(isset($reviews[$i]['cc_admin_reply']) && $reviews[$i]['cc_admin_reply']): ?>
                              <!-- admin reply -->
                              <?php $adminreplay = json_decode($reviews[$i]['cc_admin_reply']);?>
                              <div class="admin-reply review-title-row">
                                  <div class="review-avatar">
                                  <?php $admin_image = isset($adminreplay->cc_us_image) ?  $adminreplay->cc_us_image : '';?>
                                  <?php $us_image = ($admin_image ?  user_path() : default_user_path()).$admin_image;?>
                                  <?php //$us_image = file_exists($us_image) ? $us_image : assets_url().'themes/ofabee/images/avatar.svg'?>
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
                        <div class="text-center text-center-btn" id="loadmorebutton" style="<?php echo $style;?>">
                            <span class="noquestion-btn-wrap" style="">
                                <a href="javascript:void(0)" onclick="loadMoreReviews()" class="orange-flat-btn noquestion-btn" style="display: inline-block;">See more reviews</a>
                            </span>
                        </div>
                    </div>
                  </div>
              </div>
        <!--change-size-of-abt-course-->
      </div>
    <!--container-->
  </div>
  <!--reviews-->
</section>
<?php 



?>
<?php endif;?>

<?php /* Commented as per demand ?>
<section>
<div class="profile-name">
<div class="container fundamentals-altr">
<div class="change-size-of-abt-course">
<ul id="review_list" class="profile-list">
</ul>
<!--profile-list-->
</div>
<!--.change-size-of-abt-course-->
</div>
<!--container-->
</div>
<!--profile-name-->
</section>
<?php */?>
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
?>
<script>
  var __theme_url             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
  let __reviews 				      = atob('<?php echo base64_encode(json_encode($course['course_reviews'])); ?>');
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
  let __curriculum            = {
  };
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
</script>
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
                                <p class="sub-head-des-pre" id="item_tutors_list"><?php echo $tutors_name; ?></p></a>
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


<script>
    var __codeVersion = '<?php echo config_item('code_version')?>';
    window.onscroll = function() {myFunction()};
    var header = document.getElementById("sticky-enroll-strip");
    var sticky = header.offsetTop;
    function myFunction() {
      if (window.pageYOffset > sticky) {
        header.classList.add("sticky");
      } else {
        header.classList.remove("sticky");
      }
    }
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
            console.log('Error sharing: ' + error);
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
<?php include 'footer.php';?>