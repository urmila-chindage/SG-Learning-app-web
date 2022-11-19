<?php include 'header.php';?>
<?php 
   $total_rating   = 0;
   $total_rate     = 0;
   $ratings        = 0;
   $average_rating = 0;
   
   foreach ( $bundle['rating'] as $cr ) 
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
   
   foreach ($bundle['rating'] as $cr) {
       $rating[$cr['cc_rating']]['percentage']   = round(($cr['ratings'] * 100) / $total_rating, 1);
       $rating[$cr['cc_rating']]['count']        = $cr['ratings'];
   }
   
   $sum_rating   = count($bundle['rating']) != 0 ? round(($total_rate / count($bundle['rating'])), 1) : 0.0;
   
   
   ?>
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/fontawesome-stars.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/sdpk/curriculum.css">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/jquery.rateyo.min.css" rel="stylesheet">
<link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/discussion-customized.css" rel="stylesheet">
<style media="screen">
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
  #course_list_wrapper{margin-bottom:0px !important;}
  @media only screen and (max-device-width : 767px) and (orientation : portrait) {
      .course-block-1 .star-ratings-sprite{display:none !important;}
      .card-pricing-row{display:none !important;}
      .progress_main{display:none !important;}
      .course-status{display:none !important;}
  }

  @media only screen and (min-device-width : 320px) and (max-device-width : 768px) and (orientation : landscape) {
    .course-block-1 .star-ratings-sprite{display:none !important;}
    .card-pricing-row{display:none !important;}
    .progress_main{display:none !important;}
    .course-status{display:none !important;}
  }

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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
.checked {
  color: orange;
}

    /*for bundle - 16-04-2019*/

    .course-top-half{max-height:130px;}
    .head-gradient .course-preview-pic{display: flex;padding: 10px 15px;}
    .head-gradient .banner-pic-width{ width: 480px;}
    .banner-pic-width img{border: 6px solid #fff;}
    .fundamentals-skill{width: 100%;}
    .bundle-course-number{margin: 15px 0px;}
    .bundle-course-number h2{
    font-size: 18px;
    color: #fff;
    margin: 0;
    }
    .bundle-course-price{
    display: flex;
    align-items: center;
    padding: 5px 0;
    }
    .discounted-price{
    font-size: 22px;
    font-weight: 600;
    color: #fff;
    display: inline-block;
    margin: 0px;
    margin-right: 15px;
    }
    .mrp{
    font-size: 16px;
    font-weight: 600;
    color: #fff;
    display: inline-block;
    margin: 0px;
    margin-right: 15px;
    line-height: 30px;
    }
    .bundle-course-buy{
    background: #e77b28;
    border: none;
    outline: none;
    color: #fff;
    padding: 7px 25px;
    border-radius: 2px;
    }
    .disabled-button{
    background: #cacaca;
    color: #444;
    cursor: not-allowed;
    pointer-events: none;
     
  }
    /*bundle label*/
    .bundle-label{
    background: #ff327a;
        width: 36px;
        height: 42px;
        border-radius: 0px 6px 6px 6px;
        position: absolute;
        top: -5px;
        right: 25px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .bundle-label:before{
        content: ' ';
        position: absolute;
        left: -4px;
        top: 0px;
        border-style: solid;
        border-width: 0 0 6px 4px;
        border-color: transparent transparent #aa28ac transparent;
    }
    .bundle-label .bundle-icon{height: 20px;margin: 0 auto;}
    .bundle-label .bundle-count{
        font-size: 12px;
        color: #fff;
        text-align: center;
        line-height: 15px;
    }
    .bundle-label .bundle-count span{font-weight: 600;}
    .bundle-label .bundle-count span.in{
        font-size: 10px;
        display: inline-block;
    }

    .bundle-course-price .discounted-price{padding-left: 0px; color: #fff;text-decoration: none;}

    .mrp .rupee {width: 11px;display: inline-block;}
    .mrp{position: relative;}
    .mrp:after {
        content: '';
        height: 1px;
        width: 100%;
        background: white;
        position: absolute;
        top: 50%;
    }
    .ex-course-container.bundle-courses-list{
    padding: 0px 0px;
    width: 100%;
    }
    .bundle-course-number{
    display: flex;
    align-items: center;
    }
    .bundle-course-number .num-of-course-icon{padding-right: 5px;}
    .bundle-course-number .num-of-course-icon img{width: 28px;}
    .plus{color: #0d8527;}
    .minus{color: #ff7878;}

@media (max-width:480px) and (orientation:portrait){
    #course_list_wrapper{margin-bottom: 50px;}
    .bundle-label{
    top: 5px;
    right: auto;
    left: 10px;
    }
    .course-top-half {
        max-height: 100%;
        min-height: 100%;
    }
    .bundle-course-number h2{
    font-size: 18px;
    color: #fff;
    margin: 0px
    }
    .bundle-course-price .discounted-price{padding-left: 0px; color: #fff;text-decoration: none;}

    .head-gradient .course-preview-pic {display: flex;padding: 0px 0px;}
    .head-gradient .banner-pic-width {width: 100%;}
    .fundamentals-skill {margin-left: 0px;padding: 0px 15px;}
    .bundle-course-price {padding: 15px 0;}
    .mrp .rupee {width: 11px;display: inline-block;}

    .mrp{position: relative;}
    .mrp:after {
        content: '';
        height: 1px;
        width: 100%;
        background: white;
        position: absolute;
        top: 50%;
    }
    .ex-course-container.bundle-courses-list{
    padding: 0px 0px;
    width: 100%;
    }
    .ex-course-container.bundle-courses-list .xs-replacer{padding: 0px}
    .img-and-vid.xs-visible-only img{border: 6px solid #fff;}
        .bundle-course-number{
    display: flex;
    align-items: center;
    }
    .bundle-course-number .num-of-course-icon{padding-right: 5px;}
    .bundle-course-number .num-of-course-icon img{width: 20px;}
}
.haveacoupon {cursor: pointer;}
.bundle-course-buy:hover{color:#fff !important;}
</style>
<?php
   if($bundle['item_type'] == 'bundle'){
     $item_type = 2;
   }
   ?>
<section>
   <div class="head-gradient">
      <div class="course-preview-pic container">
         <!-- <div class="img-and-vid  xs-hidden-only banner-pic-width"> -->
         <img src="<?php // echo (($bundle['c_image'] == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $bundle['id']))) . $bundle['c_image'] ?>" class="img-responsive">
         <!-- </div> -->
         <div class="fundamentals-skill">
            <div class="fundamentals-altr">
               <h2 class="fundamentals-head enrolling-course-title"> <?php echo $bundle['c_title']; ?></h2>
               <div class="bundle-course-number">
                  <div class="num-of-course-icon">
                     <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') .'/img/num-of-course.svg';?>" alt="">
                  </div>
                  <h2>No.of Courses : <span><?php echo $course_count;?></span></h2>
               </div>
               <div class="bundle-course-number">
                  <div class="num-of-course-icon">
                     <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') .'/img/clock.png';?>" alt="">
                  </div>
                  <h2>Validity : 
                     <span>
                     <?php //print_r($bundle);die;
                        switch ($bundle['c_access_validity']) {
                            case 0:
                                echo 'Lifetime';
                                break;
                            case 1:
                                echo $bundle['c_validity'] > 1 ? $bundle['c_validity'] . ' Days' : $bundle['c_validity'] . ' Day';
                                break;
                            case 2:
                                echo $bundle['c_validity_expired'] ? 'Expired' : date('d M y', strtotime($bundle['c_validity_date']));
                                break;
                            default:
                                break;
                        }
                        ?>
                     </span>
                  </h2>
               </div>
               <div class="bundle-course-price">
                  <h2 class="discounted-price">
                     <?php if($bundle['c_is_free'] == '1'): ?>
                     <span class="rupee-amount">FREE</span>
                     <?php else: 
                        if($bundle_subscribed == 0):
                        ?>
                     <span style="font-family: 'Roboto', sans-serif;">&#8377;</span><span class="price" style="font-weight: 600;"><?php echo $bundle['c_discount']>0?$bundle['c_discount']:$bundle['c_price']; ?><sup>*</sup></span>
                     <?php 
                        endif;
                        endif; 
                        ?>
                  </h2>
                  <?php 
                     if($bundle['c_is_free'] != '1' && $bundle['c_discount'] > 0): 
                         if($bundle_subscribed == 0):
                     ?>
                  <h2 class="mrp">
                     <span style="font-family: 'Roboto', sans-serif;">&#8377;</span>
                     <span class="price"><?php echo $bundle['c_price'] ?></span>
                  </h2>
                  <?php 
                     endif;
                     endif; 
                     ?>
                  <?php if(isset($bundle['c_rating_enabled']) && $bundle['c_rating_enabled'] == '1'):?>
                  <?php if(isset($bundle_subscribed) && $bundle_subscribed > 0): ?>
                  <!-- Bundle rating Start here -->
                  <?php //print_r($subscription['my_rating']);die;
                     $bundle_my_rating = isset($subscription['my_rating']) ? $subscription['my_rating'] : 0;
                     ?>
                  <span class="progress-bar-course-details-wrap">
                     <span class="Progress-course-validity-label label-margin-btm" id="rate_course_label">
                     <?php echo $bundle_my_rating == 0 ? 'Rate this course' : 'Your rating'; ?>
                     </span>
                     <select id="my_rating">
                        <option value=""></option>
                        <?php for ($i = 1; $i < 6; $i++) {?>
                        <option value="<?php echo $i; ?>" <?php echo $i == $bundle_my_rating ? 'selected="selected"' : ''; ?>><?php echo $i; ?></option>
                        <?php }?>
                     </select>
                  </span>
                  <!--progress-bar-course-details-wrap-->
                  <!-- Bundle rating Ends here -->
                  <?php endif;?>
                  <?php endif;?>
                  <?php
                     $error_expired = 0;
                     $disabled_class = '';
                     switch ($bundle['c_access_validity']) {
                         case 0:
                           $error_expired = 0;
                           break;
                         case 1:
                             if($bundle['c_validity'] > 1){
                               $error_expired = 0;
                             }
                             break;
                         case 2:
                             if($bundle['c_validity_expired']){
                               $error_expired  = 1;
                               $disabled_class = 'disabled-button';
                             } 
                             break;
                         default:
                             break;
                     }
                     
                     $is_free          = 'Buy Now';
                     
                     if($bundle['c_is_free'] == '1')
                     {
                       $is_free        = 'Enroll Now';
                     }
                     ?>
                  <?php
                     if(!empty($session))
                     {
                       if(isset($bundle['enrolled']) && $bundle['enrolled'] != '1')
                       {
                         if($bundle['c_is_free'] == '1')
                         {
                           ?><div class="" id="btn_loading1"><a <?php echo ($error_expired == 1)?'disabled':'';?> href="javascript:void(0)" onclick="btn_loading('btn_loading1')" class="<?php echo $disabled_class;  ?> btn bundle-course-buy hidden-xs" >
                           Enroll Now 
                            <!-- loading icon -->
                            <svg class="btn-loading-icon" xmlns="http://www.w3.org/2000/svg" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling" style="background: none;width: 25px;height: 25px;position: absolute;left: 0px;right: 0px;margin: 0 auto;top: 50%;transform: translateY(-50%);"><circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#9f9f9f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(99.0936 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></svg>
                            <!-- loading icon ends -->
                           </a></div>
                           <?php
                     }
                     else
                     {
                       ?> <button <?php echo ($error_expired == 1)?'disabled':'';?> onclick="enrollToCourse()" class="<?php echo $disabled_class;  ?> btn bundle-course-buy hidden-xs" >Buy Now
                           
                       </button><?php
                     }
                     }
                     }
                     else
                     {
                     ?>
                  <a <?php echo ($error_expired == 1)?'disabled':'';?> href="<?php echo site_url('login'); ?>"  class="<?php echo $disabled_class;  ?> bundle-course-buy hidden-xs" ><?php echo $is_free; ?></a>
                  <?php
                     }
                     ?>
               </div>
               <div class=''>
                     <?php if ($bundle['c_is_free'] != '1'): ?>
                        <div class="tax-info bundle-tax-info">* <?php echo ($bundle['c_tax_method'] == 0 )?'Inclusive of all taxes':'Exclusive of all taxes'?></div>
                     <?php endif;?>
               </div>
               <!--text-center-->
               <div class="img-and-vid xs-visible-only">
                  <img src="<?php echo (($bundle['c_image'] == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $bundle_id))) . $bundle['c_image']; ?>" class="img-responsive">
               </div>
               <!--img-and-vid-->
               <div class="right-side right-side-responsive xs-visible-only" id="btn_loading2">
                  <?php
                     if(!empty($session))
                     {
                       if(isset($bundle['enrolled']) && $bundle['enrolled'] != '1')
                       {
                         if($bundle['c_is_free'] == '1')
                         {
                           ?><a href="javascript:void(0)" onclick="btn_loading('btn_loading2')" class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large enroll-btn-dashboard">Enroll Now<!-- loading icon -->
                           <svg class="btn-loading-icon" xmlns="http://www.w3.org/2000/svg" width="200px" height="200px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-rolling" style="background: none;width: 25px;height: 25px;position: absolute;left: 0px;right: 0px;margin: 0 auto;top: 50%;transform: translateY(-50%);"><circle cx="50" cy="50" fill="none" ng-attr-stroke="{{config.color}}" ng-attr-stroke-width="{{config.width}}" ng-attr-r="{{config.radius}}" ng-attr-stroke-dasharray="{{config.dasharray}}" stroke="#9f9f9f" stroke-width="10" r="35" stroke-dasharray="164.93361431346415 56.97787143782138" transform="rotate(99.0936 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform></circle></svg>
                           <!-- loading icon ends --></a><?php
                     }
                     else
                     {
                       ?> <a onclick="enrollToCourse()" class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large enroll-btn-dashboard">Buy Now</a><?php
                     }
                     }
                     }
                     else
                     {
                     if($bundle['c_is_free'] == '1')
                     {
                     ?><a href="<?php echo site_url()."checkout/free_enrollment_bundle/".$bundle['id']."/".$item_type; ?>" class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large enroll-btn-dashboard" >Enroll Now</a><?php
                     }
                     else
                     {
                       ?> <a href="<?php echo site_url('login'); ?>"  class="btn btn-orange2 orange-btn-altr right-side-margin-top course-page-btn-large enroll-btn-dashboard" >Buy Now</a><?php
                     }
                     }
                     ?>
               </div>
               <!--right-side-->
            </div>
            <!--container fundamentals-altr-->
         </div>
      </div>
   </div>
   <!--head-gradient-->
</section>
<section>
  <div class="about-course">
    <div class="container fundamentals-altr bundle-info-content">
      <div class="change-size-of-abt-course">
        <!-- <h3 class="formpage-heading dashboard-about-title">Package Includes</h3> -->

        <!-- bundle course list -->
        <div class="ex-course-container bundle-courses-list" id="course_list_wrapper">
        
        </div>
        <!-- bundle course list ends -->

      </div>
   </div>
</section>
<section>
  <div class="about-course">
    <div class="container fundamentals-altr">
      <div class="change-size-of-abt-course">
        <h3 class="formpage-heading dashboard-about-title">Description</h3>
        <div class="icon-text-para">
        </div>
        <div style="max-height:600px;" class="redactor-content show-more-data-wrap show-more-collapse">
            <?php echo $bundle['c_description']; ?>
            <?php  if (strlen($bundle['c_description']) > 1000) {?>
                <a href="javascript:void(0)" class="Showmore-btm">Read full details
                </a>
          <?php } ?>
        </div>
        <!--show-more-data-wrap-->
      </div>
      <!--container-->
   </div>
   <!--about-course-->
</section>
<?php if(isset($bundle['c_rating_enabled']) && $bundle['c_rating_enabled'] == '1'):?>
<section>
   <div class="container course-content-tab">
      <?php if(isset($bundle['course_reviews']['count']) && $bundle['course_reviews']['count'] > 0): ?>    
      <h5 class="tab-title">Reviews</h5>
      <!-- review starts here -->
      <div class="review-container">
         <div id="tab-reviews" class="plugin-reviews section">
            <div class="plugin-reviews" id="tabreviews">
               <?php if(isset($bundle['course_reviews']['reviews']) && !empty($bundle['course_reviews']['reviews'])): $reviews = $bundle['course_reviews']['reviews']; //print_r($reviews);?>
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
               if($bundle['course_reviews']['count'] > $bundle['course_reviews']['limit'])
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
      <?php endif;?>
      <!-- Ratings starts here -->
      <div id="reviews">
         <h5 class="tab-title">Course Ratings</h5>
         <div class="overview-contents">
            <!-- Bar rating starts here -->
            <div class="overall-rating-box d-flex justify-between align-center">
               <!-- <h5 class="tab-title xs-visible-only">Rating</h5> -->
               <div class="star-rating-left">
                  <div class="big-rating-no"><?php echo $average_rating; ?></div>
                  <!--big-rating-no-->
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
                     </div>
                     <!--star-progress-->
                     <span class="percent-align "><?php echo $rating[$i]['count']; ?></span>
                  </div>
                  <?php endfor;?>
               </div>
               <!--bar-rating-right-->
            </div>
            <!-- Bar rating ends here -->
         </div>
      </div>
      <!-- Ratings ends here -->
   </div>
   <!-- reviews end here -->
  </section>

   <?php endif;?>

<!-- modal startss -->
<!--<div id="enroll_modal" class="modal info-modal info-modal-container" style="display: none;">
   <div class="modal-content">
     <!- <span class="close" data-dismiss="modal">×</span> ->
     <div id="enroll_modal_img" class="icon-holder text-center">
     </div>
     <p id="enroll_modal_content" class="text-center">
     </p>
     <div class="text-center">
       <button data-dismiss="modal" type="" id="enroll_modal_cancel" style="text-transform:uppercase" class="custom-btn">CANCEL
       </button>
       <button id="enroll_modal_continue" type="" class="custom-btn">CONTINUE
       </button>
     </div>
   </div>
   </div>-->
<?php //echo '<pre>'; print_r($bundle); ?>
<!-- Order Summary starts here-->
<div id="enroll_modal" class="modal info-modal info-modal-container order-modal in"
   aria-hidden="false">
   <div class="modal-content">
      <div class="modal-header">
         <div class="modal-header-title">Order Summary</div>
         <div class="modal-header-close "data-dismiss="modal"><span class="close">&times;<span></div>
      </div>
      <div class="modal-body" style="padding: 0;">
         <div id="promo-msg"></div>
         <div class="flex-row">
            <!-- course card holder starts-->
            <div class="course-card-holder">
               <div class="xs-replacer">
                  <div class="course-block-1">
                     <div class="course-top-half course-top-sm-alter"> 
                        <img id="course-preview-img" src="<?php echo (($bundle['c_image'] == 'default.jpg') ? default_catalog_path() : catalog_path(array('bundle_id' => $bundle['id']))) . $bundle['c_image'] ?>" class="card-img-fit">
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
                     <div class="courser-bottom-half">
                        <label class="block-head" id="item_tutors_list" ><?php echo $bundle['c_title']; ?></label>
                     </div>
                  </div>
               </div>
            </div>
            <!-- course card holder ends-->
            <!-- order-summary starts -->
            <div class="order-summary">
               <div id="enroll_modal_content" class="text-center">
                  <div id="tax-table" class="form-group table-holder" style="padding-right: 0;">
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
<!-- modal ends -->
<?php /* ?>
<div class="modal fade" id="billmodal" role="dialog">
   <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php echo $bundle['c_title']; ?></h4>
         </div>
         <div class="modal-body">
            <div class="bill-wrapper">
               <div class="bill-column">
                  <span class="pricing-item">Course Price</span>
                  <span class="pricing">
                     <span class="rupee">
                        <svg width="1792" height="1792" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="#fff">
                           <g>
                              <title>background</title>
                              <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"></rect>
                           </g>
                           <g>
                              <title>Layer 1</title>
                              <path id="svg_1" d="m1345,470l0,102q0,14 -9,23t-23,9l-168,0q-23,144 -129,234t-276,110q167,178 459,536q14,16 4,34q-8,18 -29,18l-195,0q-16,0 -25,-12q-306,-367 -498,-571q-9,-9 -9,-22l0,-127q0,-13 9.5,-22.5t22.5,-9.5l112,0q132,0 212.5,-43t102.5,-125l-427,0q-14,0 -23,-9t-9,-23l0,-102q0,-14 9,-23t23,-9l413,0q-57,-113 -268,-113l-145,0q-13,0 -22.5,-9.5t-9.5,-22.5l0,-133q0,-14 9,-23t23,-9l832,0q14,0 23,9t9,23l0,102q0,14 -9,23t-23,9l-233,0q47,61 64,144l171,0q14,0 23,9t9,23z"></path>
                           </g>
                        </svg>
                     </span>
                     <?php echo $bundle['c_price'];?> 
                  </span>
               </div>
               <div class="bill-column">
                  <span class="pricing-item">Discount Price</span>
                  <span class="pricing">
                     <span class="rupee">
                        <svg width="1792" height="1792" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="#fff">
                           <g>
                              <title>background
                              </title>
                              <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"></rect>
                           </g>
                           <g>
                              <title>Layer 1
                              </title>
                              <path id="svg_1" d="m1345,470l0,102q0,14 -9,23t-23,9l-168,0q-23,144 -129,234t-276,110q167,178 459,536q14,16 4,34q-8,18 -29,18l-195,0q-16,0 -25,-12q-306,-367 -498,-571q-9,-9 -9,-22l0,-127q0,-13 9.5,-22.5t22.5,-9.5l112,0q132,0 212.5,-43t102.5,-125l-427,0q-14,0 -23,-9t-9,-23l0,-102q0,-14 9,-23t23,-9l413,0q-57,-113 -268,-113l-145,0q-13,0 -22.5,-9.5t-9.5,-22.5l0,-133q0,-14 9,-23t23,-9l832,0q14,0 23,9t9,23l0,102q0,14 -9,23t-23,9l-233,0q47,61 64,144l171,0q14,0 23,9t9,23z"></path>
                           </g>
                        </svg>
                     </span>
                     <?php $course_price = ( $bundle['c_discount'] != 0 ) ? $bundle['c_discount'] : $bundle['c_price'];echo $course_price;?> 
                  </span>
               </div>
               <div class="bill-column">
                  <span class="pricing-item">Tax ( <?php echo round(($sgst + $cgst), 2) ?>)%</span>
                  <span class="pricing">
                     <span class="rupee">
                        <svg width="1792" height="1792" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="#fff">
                           <g>
                              <title>background
                              </title>
                              <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"></rect>
                           </g>
                           <g>
                              <title>Layer 1
                              </title>
                              <path id="svg_1" d="m1345,470l0,102q0,14 -9,23t-23,9l-168,0q-23,144 -129,234t-276,110q167,178 459,536q14,16 4,34q-8,18 -29,18l-195,0q-16,0 -25,-12q-306,-367 -498,-571q-9,-9 -9,-22l0,-127q0,-13 9.5,-22.5t22.5,-9.5l112,0q132,0 212.5,-43t102.5,-125l-427,0q-14,0 -23,-9t-9,-23l0,-102q0,-14 9,-23t23,-9l413,0q-57,-113 -268,-113l-145,0q-13,0 -22.5,-9.5t-9.5,-22.5l0,-133q0,-14 9,-23t23,-9l832,0q14,0 23,9t9,23l0,102q0,14 -9,23t-23,9l-233,0q47,61 64,144l171,0q14,0 23,9t9,23z"></path>
                           </g>
                        </svg>
                     </span>
                     <?php
                        $sgst_price         = ( $sgst / 100 ) * $course_price;
                        $cgst_price         = ( $cgst / 100 ) * $course_price;
                        echo round(($sgst_price + $cgst_price), 2);
                        ?>
                  </span>
               </div>
               <div class="bill-column">
                  <span class="pricing-item">Total</span>
                  <span class="pricing">
                     <span class="rupee">
                        <svg width="1792" height="1792" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1792 1792" fill="#fff">
                           <g>
                              <title>background
                              </title>
                              <rect fill="none" id="canvas_background" height="402" width="582" y="-1" x="-1"></rect>
                           </g>
                           <g>
                              <title>Layer 1
                              </title>
                              <path id="svg_1" d="m1345,470l0,102q0,14 -9,23t-23,9l-168,0q-23,144 -129,234t-276,110q167,178 459,536q14,16 4,34q-8,18 -29,18l-195,0q-16,0 -25,-12q-306,-367 -498,-571q-9,-9 -9,-22l0,-127q0,-13 9.5,-22.5t22.5,-9.5l112,0q132,0 212.5,-43t102.5,-125l-427,0q-14,0 -23,-9t-9,-23l0,-102q0,-14 9,-23t23,-9l413,0q-57,-113 -268,-113l-145,0q-13,0 -22.5,-9.5t-9.5,-22.5l0,-133q0,-14 9,-23t23,-9l832,0q14,0 23,9t9,23l0,102q0,14 -9,23t-23,9l-233,0q47,61 64,144l171,0q14,0 23,9t9,23z"></path>
                           </g>
                        </svg>
                     </span>
                     <?php 
                        $total_course_price = $course_price + $sgst_price + $cgst_price;  
                        echo round($total_course_price, 2);
                        ?>
                  </span>
               </div>
            </div>
         </div>
         <div class = "modal-footer">
            <a class="btn btn-success" href = "<?php echo site_url()."checkout/payment_request/".$bundle['id']."/".$item_type ?>">
            Continue
            </a>
         </div>
      </div>
   </div>
</div>
<?php
   */
   //print_r($course_details); die;
   $course_price = ( $bundle['c_discount'] != 0 ) ? $bundle['c_discount'] : $bundle['c_price'];
   $course_price = floatval($course_price);
   if($bundle['c_tax_method']=='1')
   {
     $gst_setting         = $this->settings->setting('has_tax');
     $cgst                = ($gst_setting['as_setting_value']['setting_value']->cgst != '')?$gst_setting['as_setting_value']['setting_value']->cgst:0;
     $sgst                = ($gst_setting['as_setting_value']['setting_value']->sgst != '')?$gst_setting['as_setting_value']['setting_value']->sgst:0;
     $cgst                = floatval($cgst);
     $sgst                = floatval($sgst);
     $sgst_price          = round(($sgst / 100) * $course_price,2);
     $cgst_price          = round(($cgst / 100) * $course_price,2);
     $total_course_price  = $course_price+$sgst_price+$cgst_price;
     
   }
   else 
   {
     $cgst                = 0;
     $sgst                = 0;
     $sgst_price          = 0;
     $cgst_price          = 0;
     $total_course_price  = $course_price;
   }
   $tax                   = $sgst_price+$cgst_price;
   ?>
<!-- Bill Modal ends -->
<script>
   var __theme_url             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
   var __site_url              = '<?php echo site_url() ?>';
   var __default_course_path   = '<?php echo default_course_path() ?>';
   var __default_catalog_path  = '<?php echo default_catalog_path() ?>';
   var __catalog_path          = '<?php echo catalog_path() ?>';
   var __course_path           = '<?php echo course_path() ?>';
   var __coursesObject         = atob('<?php echo base64_encode(json_encode($course_details)) ?>');
   var __admin_name            = '<?php echo $admin ?>';
   var __codeVersion = '<?php echo config_item('code_version')?>';
   let __course                = {
   };
   __course.course_id            = '<?php echo  $bundle['id']; ?>';
   __course.cb_is_free     = '<?php echo $bundle['c_is_free']; ?>';
   __course.cb_tax_method  = '<?php echo $bundle['c_tax_method']; ?>';
   __course.cb_cgst        = '<?php echo $cgst; ?>';
   __course.cb_price       = '<?php echo $course_price; ?>';
   __course.cb_cgst_price  = '<?php echo round($cgst_price,2); ?>';
   __course.cb_sgst        = '<?php echo $sgst; ?>';
   __course.cb_sgst_price  = '<?php echo round($sgst_price,2); ?>';
   __course.cb_total_price = '<?php echo $total_course_price; ?>';
   __course.cb_course_discount = '<?php echo $bundle['c_price'];?>';
   __course.cb_tax               = '<?php echo $tax; ?>';
   //console.log('cb_course_discount',__course.cb_course_discount);
   $(document).ready(function() {
   
     __coursesObject     = $.parseJSON(__coursesObject);
     console.log('__coursesObject',__coursesObject);
     $('#course_list_wrapper').html(renderCoursesHtml(__coursesObject));
   
     $(".Showmore-btm").click(function(){
       $(".show-more-data-wrap").removeClass("show-more-collapse");
       $(".show-more-data-wrap").css({
         'max-height' : 'none'});
       $(".Showmore-btm").remove();
     });
   });
var __course_subscribe_link = '<?php echo site_url()."checkout/free_enrollment_bundle/".$bundle['id']."/".$item_type;?>';
   function btn_loading(id){
      $('#'+id).addClass('btn-onloading');
      document.location.replace(__course_subscribe_link);
   }
   
   function getCgst(itemPrice){
     var cgst = (parseFloat(__course.cb_cgst)/100 * parseFloat(itemPrice)).toFixed(2);
     return cgst;
   }
   
   function getSgst(itemPrice){
     var sgst = (parseFloat(__course.cb_sgst)/100 * parseFloat(itemPrice)).toFixed(2);
     return sgst;
   }
   
   function renderCoursesHtml(courses)
   {
     //console.log(courses);
       var coursesHtml  = '';
       if(Object.keys(courses).length > 0 )
       {
           var count_course = 1;
           $.each(courses, function(courseKey, course )
           { //console.log(course.id, 'course poiuytrewqlkjhgfdsamnbvcxz');
               if(typeof course.id != 'undefined'){
                  coursesHtml     += __courseCard(course);
                  count_course++;
               }
           });
           
       }
       return coursesHtml;
   }
   function enrollToCourse(){
     resetCoupon();
   }
   
   
   function taxCalculation(){
     //console.log(__course);
       var click_function = '';
       if(+__course.cb_is_free == 1)
       {
           click_function     = 'applyPromo('+__course.course_id+')';
       }
       else
       {
         click_function     = 'applyPromo('+__course.course_id+')';
         var tax_table ='';
             tax_table +='<div id="tax-table" class="form-group table-holder" style="padding-right: 0;">';
             tax_table +='   <table class="billing-table" style="width:100%;border:0px">';
             tax_table +='   <tbody>';
             
                 tax_table +='       <tr>';
                 tax_table +='           <td class="text-left">Bundle Price</td>';
                 tax_table +='           <td class="text-right text-green"><span class="rupee" style="font-family: \'Roboto\', sans-serif;">₹</span><span class="price">'+__course.cb_price+'</span>';
                 tax_table +='       </tr>';
                 tax_table +='       <tr class="promocode-preview" id="promocode_offer" style="display:none;">';
                 tax_table +='           <td class="text-left"><span class="promocode"><span id="promocode_text">GET100</span><img src="'+__theme_url+'/images/scissors.png'+__codeVersion+'"></span><a class="remove-coupon" href="#" onClick="resetCoupon();">Remove</a></td>';
                 tax_table +='           <td class="text-right text-green"> <span>-</span> <span class="rupee" style="font-family: \'Roboto\', sans-serif;">₹</span> <span class="price" id="promocode_reduction"></span></td>';
                 tax_table +='       </tr>';
                 tax_table +='       <tr>';
                 /*tax_table +='           <td class="text-left">Discount</td>';
                 tax_table +='           <td class="text-right text-green"> ';
                 tax_table +='               <span class="plus">- </span> ';
                 tax_table +='               <span class="rupee" style="font-family: \'Roboto\', sans-serif;">₹</span> ';
                 tax_table +='               <span class="price" id="cgst_price">'+(parseFloat(__course.cb_course_discount) - parseFloat(__course.cb_price)).toString();+'</span>';
                 tax_table +='           </td>';
                 tax_table +='       </tr>';*/
             
            
             if(__course.cb_tax_method=='1')
             {
                 tax_table +='       <tr>';
                 tax_table +='           <td class="text-left">Tax ('+(parseFloat(__course.cb_cgst)+parseFloat(__course.cb_sgst)).toString()+'%)</td>';
                 tax_table +='           <td class="text-right text-green">';
                 tax_table +='               <span class="rupee" style="font-family: \'Roboto\', sans-serif;">₹</span> '; 
                 tax_table +='               <span class="price" id="tax_price">'+__course.cb_tax+'</span>';
                 tax_table +='           </td>';
                 tax_table +='       </tr>';
                 
             }                       
             tax_table +='       </tbody>';
             tax_table +='   </table>';  
              
             if(__course.cb_total_price > 0){
               tax_table +='   <div class="haveacoupon" onclick="showPromo();">';
               tax_table +='       <span>Have a Coupon ?</span>';
               tax_table +='   </div>';
             }
   
             tax_table +='   <div class="form-group promo-column" id="promo-column" style="display:none;">';
             tax_table +='       <input type="text" class="form-control" style="width:80%; text-transform: uppercase;" maxlength="12" id="promo_code" name="promo_code" placeholder="Apply Discount Code">';
             tax_table +='       <button id="promo_code_btn" onclick="applyCoupon()" class="custom-btn">Apply</button>';
             tax_table +='   </div>';
   
             tax_table +=' <div class="total-column">';
             tax_table +='       <div class="text-left"><b>Total</b></div>';
             tax_table +='           <div class="text-right">';
             tax_table +='               <span class="rupee" style="font-family: \'Roboto\',sans-serif;"><b>₹</b></span>';
             tax_table +='               <span class="price" id="net_total"><b>'+__course.cb_total_price+'</b></span>';
             tax_table +='           </div>';
             tax_table +='       </div>';
   
             tax_table +='       <div class="text-center">';
             tax_table +='           <button onclick="applyPromo()" type="" class="custom-btn btnorange checkout-btn">Checkout</button>';
             tax_table +='       </div>';
             tax_table +='</div>';
         
             showEnrollModal('Do you want to continue?',`${tax_table}`,5,`${click_function}`);
       }
   }
   
   function showPromo()
     {
       $(".haveacoupon").hide();
       $("#promo-column").show();
     }
   
   function applyPromo() {
     var promo_code = $("#promo_code").val();
     var link            = '';
     if(+__course.cb_is_free == 1)
     {
         link  = __site_url+'checkout/free_enrollment_bundle/'+__course.course_id+'/2';
     } else {
         link  = __site_url+'checkout/payment_request/'+__course.course_id+'/2';
     }
     if(promo_code!="") {
         msg                          = '<div class="alert alert-error alert-danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Please apply Discount Code.</div>';  
         $("#promo-msg").html(msg);
     } else {
         $("#promo-msg").html('');
         location.href   = link;
     }
   }
   function resetCoupon() {
     $('.alert').hide();
     $.ajax({
         url: __site_url+'checkout/reset_coupon',
         type: "POST",
         data:{"is_ajax":true},
         success: function(response) {
             var data             = $.parseJSON(response);
             if(data['error'] === false){
                 taxCalculation();
             } 
         }
     });
   }
   
   function applyCoupon()
   {
     var promo_code = $("#promo_code").val();
     if(promo_code == ''){
         var msg     = '<div class="alert alert-error alert-danger" id="alert_danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Discount Code should not be empty.</div>';  
         $("#promo-msg").html(msg);
         return false;
     } else {
         $.ajax({
             url: __site_url+'checkout/promocode_usage',
             type: "POST",
             data:{"is_ajax":true,'promo_code':promo_code},
             success: function(response) {
                 var data             = $.parseJSON(response);
                 var msg              = '';
                 var discount_rate    = '';
                 if(data['header']['success'] === true){
                   var pc_discount_rate = data.body.promocode.pc_discount_rate;
                     //console.log(pc_discount_rate, __course.cb_price);
                     
                     var discout_type             = data['body']['promocode']['pc_discount_type'];
                     if(discout_type=='1') {
                         discount_rate            = (data['body']['promocode']['pc_discount_rate']!=undefined)?data['body']['promocode']['pc_discount_rate']:'0';
                     } else {
                         var discount_percentage  = (data['body']['promocode']['pc_discount_rate']!=undefined)?data['body']['promocode']['pc_discount_rate']:'0';
                         discount_rate            = ((discount_percentage/100) * __course.cb_price).toFixed(2);
                     }
   
                     if(Number(discount_rate) > Number(__course.cb_price)){
                         msg     = '<div class="alert alert-error alert-danger" id="alert_danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Discount Code Could not be greater than Bundle Total price.</div>';  
                         $("#promo-msg").html(msg);
                         return false;
                     }else{
                     msg                          = '<div class="alert alert-success"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>Discount Code is applied</div>';  
                     $("#promo-msg").html(msg);
   
                     var course_price             = (parseFloat(discount_rate)<parseFloat(__course.cb_price))?(parseFloat(__course.cb_price)-parseFloat(discount_rate)):'0';
                     var discount_amount          = (parseFloat(discount_rate)<parseFloat(__course.cb_price))?parseFloat(discount_rate):parseFloat(__course.cb_price);
                     var course_net_price         = 0;
                     if(__course.cb_tax_method=='1')
                     {
                         var sgst_amount          = getSgst(course_price);
                         var cgst_amount          = getCgst(course_price);
                         var tax_amount           = parseFloat(sgst_amount)+parseFloat(cgst_amount);
                         course_net_price         = parseFloat(course_price)+parseFloat(sgst_amount)+parseFloat(cgst_amount);
                         //$("#cgst_price").html(cgst_amount);
                         //$("#sgst_price").html(sgst_amount);
                         $('#tax_price').html(tax_amount);
                         $('#new-course-price').html(course_price);
                     }
                     else 
                     {
                       course_net_price          = course_price;
                     }
                     course_net_price = (course_net_price>1)?course_net_price:0;
                     $(".haveacoupon").hide();
                     $('.promo-column').hide();
                     $("#promocode_text").html(promo_code.toUpperCase());
                     $("#promocode_reduction").html(discount_amount);
                     $("#net_total").html(course_net_price.toFixed(2));
                     $("#promocode_offer").show();
                     $("#new-price").show();
                     $("#promo_code").val('');
                   }
                 } else {
                     msg     = '<div class="alert alert-error alert-danger" id="alert_danger"><a class="close" data-dismiss="alert" id="dismiss_pass_pop">×</a>'+data['header']['message']+'</div>';  
                     $("#promo-msg").html(msg);
                 }
             }
         });
     }
   }
   
   function showEnrollModal(heading = '',message = '',type = 0,onclick = null){
     //console.log(heading,message,type,onclick);
     var svg = '';
     $('#enroll_modal_continue').hide();
     $('#enroll_modal_continue').attr('onclick','javascript:void(0)');
     $('#enroll_modal_cancel').addClass('btnorange');
     // $('#enroll_modal_title').removeClass('green-text').removeClass('red-text').removeClass('orange-text');
     switch(type){
         case 1:  //Success
             if(heading == ''){
                 heading = 'Success';
             }
             svg = `<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#00aa47" xml:space="preserve"><g><g><path d="M85.263,46.49L54.485,77.267L42.804,65.584c-0.781-0.782-2.047-0.782-2.828-0.002c-0.781,0.782-0.781,2.048,0,2.829    l14.51,14.513l33.605-33.607c0.781-0.779,0.781-2.046,0-2.827C87.31,45.708,86.044,45.708,85.263,46.49z M64.032,13.871    c-27.642,0-50.129,22.488-50.129,50.126c0.002,27.642,22.49,50.131,50.131,50.131h0.004c27.638,0,50.123-22.489,50.123-50.131    C114.161,36.358,91.674,13.871,64.032,13.871z M64.038,110.128h-0.004c-25.435,0-46.129-20.694-46.131-46.131    c0-25.434,20.693-46.126,46.129-46.126s46.129,20.693,46.129,46.126C110.161,89.434,89.471,110.128,64.038,110.128z"/></g></g></svg>`;
             $('#enroll_modal_title').addClass('green-text');
         break;
   
         case 2:  //Error
             if(heading == ''){
                 heading = 'Error';
             }
             svg = `<svg enable-background="new 0 0 128 128" height="60px" id="Layer_1" version="1.1" viewBox="0 0 128 128" width="60px" fill="#f44" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M84.815,43.399c-0.781-0.782-2.047-0.782-2.828,0L64.032,61.356L46.077,43.399c-0.781-0.782-2.047-0.782-2.828,0    c-0.781,0.781-0.781,2.047,0,2.828l17.955,17.957L43.249,82.141c-0.781,0.78-0.781,2.047,0,2.828    c0.391,0.39,0.902,0.585,1.414,0.585s1.023-0.195,1.414-0.585l17.955-17.956l17.955,17.956c0.391,0.39,0.902,0.585,1.414,0.585    s1.023-0.195,1.414-0.585c0.781-0.781,0.781-2.048,0-2.828L66.86,64.184l17.955-17.957C85.597,45.447,85.597,44.18,84.815,43.399z     M64.032,14.054c-27.642,0-50.129,22.487-50.129,50.127c0.002,27.643,22.491,50.131,50.133,50.131    c27.639,0,50.125-22.489,50.125-50.131C114.161,36.541,91.674,14.054,64.032,14.054z M64.036,110.313h-0.002    c-25.435,0-46.129-20.695-46.131-46.131c0-25.435,20.693-46.127,46.129-46.127s46.129,20.693,46.129,46.127    C110.161,89.617,89.47,110.313,64.036,110.313z"/></g></g></svg>`;
             $('#enroll_modal_cancel').removeClass('btnorange');
             $('#enroll_modal_cancel').html('Ok');
             $('#enroll_modal_title').addClass('btnorange');
         break;
   
         case 5:  //Error
             $('#enroll_modal_cancel').removeClass('btnorange');
             $('#enroll_modal_cancel').html('Ok');
             $('#enroll_modal_title').addClass('btnorange');
             $('#enroll_modal_cancel').addClass('btnorange');
             $('#enroll_modal_img').hide();
         break;
   
         default : //Info
             if(heading == ''){
                 heading = 'Warning';
             }
             svg = `<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                             width="60px" height="60px" fill="#f78700" viewBox="0 0 128 128" enable-background="new 0 0 128 128" xml:space="preserve">
                     <circle fill="none" stroke="#f78700" stroke-width="5" stroke-miterlimit="10" cx="64" cy="64" r="47.304"/>
                     <path d="M67.375,80.041c0,1.496-1.287,2.709-2.875,2.709l0,0c-1.588,0-2.875-1.213-2.875-2.709V37.876
                         c0-1.496,1.287-2.709,2.875-2.709l0,0c1.588,0,2.875,1.213,2.875,2.709V80.041z"/>
                     <path d="M67.542,91.382c0,1.681-1.362,3.042-3.042,3.042l0,0c-1.68,0-3.042-1.361-3.042-3.042v-0.264
                         c0-1.681,1.362-3.042,3.042-3.042l0,0c1.68,0,3.042,1.361,3.042,3.042V91.382z"/>
                     </svg>`;
             $('#enroll_modal_title').addClass('orange-text');
         break;
     }
     //$('#enroll_modal_cancel').html('Close');
     if(onclick != null && onclick != 'null'){
         $('#enroll_modal_continue').attr('onclick',onclick);
         $('#enroll_modal_continue').show();
         $('#enroll_modal_cancel').html('Cancel');
     }
     $('#enroll_modal_img').html(svg);
     //$('#enroll_modal_title').html(heading);
     $('#enroll_modal_title').hide();
     $('#enroll_modal_content').html(message);
     $('#enroll_modal').modal('show');
   }
     
</script>
<script type="text/javascript" src="<?php echo assets_url() . 'themes/' . $this->config->item('theme') . '/js/jquery.barrating.min.js'; ?>" ></script>
<script type="text/javascript">
   let __my_rating = '<?php echo $bundle_my_rating ?>';
   let __start = true;
   
   __my_rating = +__my_rating;
   $('#my_rating').barrating({
       theme: 'fontawesome-stars',
       readonly: (__my_rating != 0),
       onSelect: function (value, text) {
           __my_rating = value;
           if (__start == true) {
               rate_course(__my_rating);
           }
       }
   });
   
   $(function () {
   $('#example_course_dashboard').barrating({
       theme: 'fontawesome-stars',
       readonly: __my_rating,
       onSelect: function (value, text) {
           __rating_selected = value;
           if (__start == true) {
               rate_course(__rating_selected);
           }
       }
   });
   });
   
   var __rated = false;
   
   function rate_course(ratingSelected) {
     __rating_selected = ratingSelected;
     __start = false;
     $('#example2').barrating({
         theme: 'fontawesome-stars',
         readonly: false,
         onSelect: function (value, text) {
             __rating_selected = value;
             $('#example_course_dashboard').barrating('set', __rating_selected);
         }
     });
     $('#example2').barrating('set', __rating_selected);
     $('#rate_course').modal('show');
   }
   
   
   $(document).on('hidden.bs.modal', '#rate_course', function (e) {
     __start = true;
     $('#example_course_dashboard').barrating('clear');
     $('#review_course').val('');
     if(!__rated){
         $('#my_rating').barrating('clear');
     }
   });
   $(document).on('hidden.bs.modal', '#rate_course_preview', function (e) {
     __start = false;
     $('#rate_course_label').html('Your rating');
     $('#example_course_dashboard').barrating('set', __rating_selected);
     $('#example_course_dashboard').barrating('readonly', true);
   });
   $(document).on('click', '#submit_rating_course', function () {
     __rated = true;
     var __review = $('#review_course').val();
     $.ajax({
         url: __site_url + 'material/bundle_rating_review',
         type: "POST",
         async: false,
         data: {
             "is_ajax": true,
             'bundle_id': __course.course_id,
             'rating': __rating_selected,
             'review': __review
         },
         success: function (response) {
             var data = $.parseJSON(response);
             $('#rate_course').modal('hide');
             $('#rate_course').on('hidden.bs.modal', function (e) {
                 $('#example4').barrating({
                     theme: 'fontawesome-stars',
                     readonly: true
                 });
                 $('#example4').barrating('set', __rating_selected);
                 $('#my_rating').barrating('set', __rating_selected);
                 $('#my_rating').barrating('readonly', true);
                 $('#preview_review_course').text(__review);
                 $("#rate_course_preview").modal('show');
             });
         }
     });
   });
   
   var __theme_url             = '<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>';
   var __reviews 				      = atob('<?php echo base64_encode(json_encode($bundle['course_reviews'])); ?>');
   let __limit                 = '<?php echo $bundle['course_reviews']['limit'];?>';
   let __offset                = '<?php echo ($bundle['course_reviews']['count'] > $bundle['course_reviews']['limit']) ? $bundle['course_reviews']['limit'] : '0';?>';
   var __reviewsCount          = '<?php echo $bundle['course_reviews']['count'];?>';
   
   
   function loadMoreReviews(){
   //console.log('__limit', __limit, '__offset', __offset, '__reviewsCount', __reviewsCount);
   
   $('#loadmorebutton').html(`<span class="noquestion-btn-wrap" >
                                  <a href="javascript:void(0)" class="orange-flat-btn noquestion-btn" style="display: inline-block;">Loading. please wait...!</span></a>
                              </span>`);
   $.ajax({
       url: __site_url + 'bundle/load_reviews',
       type: "POST",
       data: {
           "is_ajax": '1', 
           'limit': __limit,
           'offset': __offset,
           'course_id': __course.course_id,
           "count": __reviewsCount
       },
       success: function(response) {
           var data            = $.parseJSON(response);
               __reviewsCount  = data.total_records;
               __defaultpath   = data.default_user_path;
               __userpath      = data.user_path;
   
           if (data['success'] == true) {
               __offset = data['start'];
               //console.log(__offset);
               var groupsHtml = '';
               //console.log(Object.keys(data['reviews']).length);
               if (Object.keys(data['reviews']).length > 0) {
   
                   $.each(data['reviews'], function(reviewsid, reviews) {
                       groupsHtml += renderhtml(reviews);
                   });
                   //console.log(groupsHtml);
                   
                   var load_button = `<span class="noquestion-btn-wrap" >
                                           <a href="javascript:void(0)" onclick="loadMoreReviews()" class="orange-flat-btn noquestion-btn" style="display: inline-block;">See more reviews</a>
                                      </span>`;
                   
                       $('#tabreviews').append(groupsHtml);
                       $('#loadmorebutton').html(load_button);
                   
                   if (data['show_load_button'] == true) {
                       $('#loadmorebutton').show();
                   } else {
                       $('#loadmorebutton').hide();
                   }
                   
               }else{
                   $('#loadmorebutton').hide();
               }
           }
       }
   });
   
   }
   
   
   function renderhtml(reviews) {
   //__user_path.default
   //__user_path.native 
   var user_img                = __userpath + reviews.cc_user_image;
   var cc_review_reply         = reviews.cc_admin_reply ? $.parseJSON(reviews.cc_admin_reply) : '';
   var cc_us_image             = __userpath + cc_review_reply.cc_us_image;
   //console.log(cc_us_image);
   var returns = `<div class="review-holder">
   <div class="review-title-row">
       <div class="review-avatar">
         <img alt="${reviews.cc_user_name} profile pic" src="${user_img}" class="avatar avatar-60 photo img-responsive">					
       </div>
       <div class="review-name-rating">
           <div class="reviewer-name">${reviews.cc_user_name}</div>
           <div class="review" style="display: flex;align-items: center;">
               <div class="star-ratings-sprite-two">
                 <span style="width: ${((reviews.cc_rating/5)*100)}%;" class="star-ratings-sprite-rating-two"></span>
               </div>
               <!--<small style="display:none; padding-left: 15px; margin-top:5px;">${dateFormat(reviews['created_date'])}</small>-->
               
           </div>
       </div>
   </div>
   <div class="review-content-row">
       <p class="review-content">${reviews.cc_reviews}</p>
   </div>`;
   if(cc_review_reply !='undefined' && cc_review_reply.cc_review_reply){
       returns += `<!-- admin reply --> 
       <div class="admin-reply review-title-row">
           <div class="review-avatar">
           <!--<img alt="" src="${__theme_url}/images/avatar.svg" class="avatar avatar-60 photo img-responsive">-->
           <img alt="" src="${cc_us_image}" class="avatar avatar-60 photo img-responsive">				
           </div>
           <div class="review-name-rating">
               <div class="reviewer-name"><b>${cc_review_reply.cc_user_name}</b></div>
               <div class="review">
                   <p>${cc_review_reply.cc_review_reply}</p>
               </div>
           </div>
       </div>
       <!-- admin reply -->`;
   }
   returns += `</div>`;
   return returns;
   }
   
   function dateFormat(data) {
   var mydate = new Date(data);
   var month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
       "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
   ][mydate.getMonth()];
   str = mydate.getFullYear() + ' ' + month + ' ' + mydate.getDate();
   
   return str;
   }

   
</script>
<?php include 'footer.php'; ?>
<div id="rate_course" class="modal fade" role="dialog">
   <div class="modal-dialog modal-md">
      <div class="modal-content ofabee-modal-content">
         <div class="modal-header ofabee-modal-header border-bottom-replaced">
            <button type="button" class="close" data-dismiss="modal">
            &times;
            </button>
            <h4 class="modal-title ofabee-modal-title">Rate this course</h4>
         </div>
         <div class="modal-body ofabee-modal-body textarea-top">
            <div class="starrating-inside">
               <span class="rate-this-label ratelabel-block">Your rating</span>
               <select id="example2">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
               </select>
            </div>
            <script>
               $(document).ready(function() {
                   $( "#review_course" ).keyup(function( event ) {
                       var textlenth = $('#review_course').val().length;
                       $('#review_course_char_left').text(textlenth +' / '+ (300 - textlenth));
                   });
               });
            </script>
            <textarea class="ofabee-textarea" id="review_course" maxlength="300" placeholder="Tell others what you think about this course and why did you leave this rating"></textarea>
            <small id="review_course_char_left">0 / 300</small>
         </div>
         <div class="modal-footer ofabee-modal-footer btn-center-responsive">
            <button type="button" class="btn ofabee-dark" data-dismiss="modal">Cancel</button>
            <button id="submit_rating_course" type="button" class="btn ofabee-orange" >Submit</button>
         </div>
      </div>
   </div>
</div>
<div id="rate_course_preview" class="modal fade ofabee-modal" role="dialog">
   <div class="modal-dialog modal-md">
      <div class="modal-content ofabee-modal-content">
         <div class="modal-header ofabee-modal-header">
            <button type="button" class="close" data-dismiss="modal">
            &times;
            </button>
         </div>
         <div class="modal-body ofabee-modal-body textarea-top">
            <img src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/img/Successful_icon.svg" class="blocked-image">
            <span class="your_review">Your review has been
            <br />
            submitted</span>
            <div class="starrating-inside text-center">
               <span class="blocked_rating">Your rating</span>
               <select id="example4">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
               </select>
            </div>
            <span class="preview_purpose" id="preview_review_course"></span>
         </div>
         <div class="modal-footer ofabee-modal-footer modal-footer-text-center">
            <button type="button" class="btn ofabee-dark" data-dismiss="modal">
            Close
            </button>
         </div>
      </div>
   </div>
</div>