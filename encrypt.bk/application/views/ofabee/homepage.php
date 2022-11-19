<?php include 'header.php'; ?>
<?php //echo '<pre>'; print_r($latest_challenge);die; ?>

<?php ?>
  <link href="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/css/owl.carousel.css" rel="stylesheet">
<?php ?>

<!-- Banner section -->
<style type="text/css" media="screen">
  .orange-flat-btn{
    padding: 8px 20px;
    border-radius: 4px;
    font-size: 14px;
  }
  html {scroll-behavior: smooth;}
  .bundle-label {
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
.bundle-label:before {
    content: ' ';
    position: absolute;
    left: -4px;
    top: 0px;
    border-style: solid;
    border-width: 0 0 6px 4px;
    border-color: transparent transparent #aa28ac transparent;
}
.bundle-label .bundle-icon {
    height: 20px;
    margin: 0 auto;
}
.bundle-label .bundle-count {
    font-size: 12px;
    color: #fff;
    text-align: center;
    line-height: 15px;
}
.bundle-label .bundle-count span {
    font-weight: 600;
}
.bundle-label .bundle-count span.in {
    font-size: 10px;
    display: inline-block;
}


/* if no content minimum height */

</style>
<?php if(isset($banner) && $banner!="") { ?>
<section>
  <div class="olp-banner">
    <img class="dynamic-banner" src="<?php echo banner_path().$banner ?>">
    <div class="banner-overly"></div>
    <div class="container container-res-chnger-frorm-page">
      <div class="changed-container-for-forum">
        <div class="banner-text-margin-top">
          <h1 class="online-course-head"><?php echo config_item('banner_text'); ?></h1>
          <div class="search-and-higlights-waraper"> 
            <span class="search-bar-in-banner-wraper custom-dashboard-searchbar"> 
              <span class="olp-banner-search">
                  <input type="search" class="olp-inputBox" autocomplete="off" placeholder="Find a course" onFocus="this.placeholder=''" id="searchid" onBlur="this.placeholder='Find a course'">
              </span><!--olp-banner-search--> 
              <span class="olp-search-btn"> 
                  <a class="olp-link-btn" id="searchbtn">
                    <svg version="1.1"  x="0px" y="0px"
                                         viewBox="0 0 37.9 37.9" enable-background="new 0 0 37.9 37.9" xml:space="preserve">
                            <g>
                    <path fill="#FFFFFF" d="M24.3,26.9v-1.7l-1.1-1.1l-0.4,0.3c-2.5,2.1-5.6,3.3-8.8,3.3c-7.5,0-13.6-6.1-13.6-13.6S6.6,0.5,14.1,0.5
                                                      s13.6,6.1,13.6,13.6c0,3.2-1.2,6.4-3.3,8.8l-0.3,0.4l1.1,1.1h1.7l10.3,10.3l-2.5,2.5L24.3,26.9z M14.1,3.8
                                                      C8.4,3.8,3.8,8.4,3.8,14.1s4.6,10.2,10.3,10.2c5.7,0,10.2-4.6,10.2-10.2S19.7,3.8,14.1,3.8z"/>
                    <path fill="#FFFFFF" d="M14.1,1c7.2,0,13.1,5.9,13.1,13.1c0,3.1-1.1,6.1-3.2,8.5l-0.6,0.7l0.7,0.7l0.6,0.6l0.3,0.3h0.4h1.3l9.8,9.8
                                                      l-1.8,1.8l-9.8-9.8v-1.3V25l-0.3-0.3L24,24.1l-0.7-0.7L22.6,24c-2.4,2-5.4,3.2-8.5,3.2C6.9,27.2,1,21.3,1,14.1C1,6.9,6.9,1,14.1,1
                                                       M14.1,24.8c5.9,0,10.8-4.8,10.8-10.8S20,3.3,14.1,3.3S3.3,8.2,3.3,14.1S8.2,24.8,14.1,24.8 M14.1,0C6.3,0,0,6.3,0,14.1
                                                      c0,7.8,6.3,14.1,14.1,14.1c3.5,0,6.7-1.3,9.2-3.4l0.6,0.6v1.7l10.8,10.8l3.2-3.2L27.1,23.8h-1.7l-0.6-0.6c2.1-2.5,3.4-5.7,3.4-9.2
                                                      C28.2,6.3,21.9,0,14.1,0L14.1,0z M14.1,23.8c-5.4,0-9.8-4.4-9.8-9.8s4.4-9.8,9.8-9.8c5.4,0,9.8,4.4,9.8,9.8S19.5,23.8,14.1,23.8
                                                      L14.1,23.8z"/>
                    </g>
                    </svg>
                  </a> 
              </span><!--olp-search-btn--> 
            </span><!--search-bar-in-banner-wraper--> 
            <?php if(!empty($categories)): ?>
            <span class="highlight-in-banner-wraper">
            <ul class="highlight-text">
            <?php if(count($categories)<=5){ ?>
                <?php foreach ($categories as $category) { ?>
                    <li><a href="<?php echo site_url('course/listing?&categoryids='.$category['id']); ?>" class="highlight-text-link"><?php echo $category['ct_name']; ?></a></li>      
                <?php }} ?>
            </ul>
            </span><!--highlight-in-banner-wraper-->
        <?php endif; ?>
       
          </div>
          <!--search-and-higlights-waraper--> 
        </div>
        <!--banner-text-margin-top-->
        <div class="arrow-down">
          <a href="#course_wrapper">
            <svg style="cursor:pointer;" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="    http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 33 20.4" enable-background="new 0 0 33 20.4" xml:space="preserve"> <g><g><polygon fill="#FFFFFF" points="29.1,0 16.5,12.6 3.9,0 0,3.9 16.5,20.4 33,3.9"/></g></g>
            </svg> 
          </a>
        </div>
      </div>
      <!--changed-container-for-forum--> 
    </div>
    <!--container--> 
  </div>
  <!--olp-banner--> 
</section>

<?php } ?>
<!-- End Banner section -->

<?php if(!empty($top_course)): ?>
<?php //echo '<pre>';print_r($top_course);die(); ?>
<!-- Top courses start -->
<section>
  <div class="ex-course">
    <div id="course_wrapper" class="container container-res-chnger-frorm-page no-padding-xs">
      <div class="changed-container-for-forum">
        <div class="dashboard-top-courses exp-course">Top Courses</div>
        <?php 
            $items = array();
            if(!empty($user_course_enrolled)){
                foreach($user_course_enrolled as $course_enrolled) {
                    $items[] = $course_enrolled['id'];
                }
            }
        ?>
          <ul class="ex-course-container">
        <?php foreach($top_course as $key => $course){
          if($course['item_type'] == 'course')
          {
            $course['course_id'] = $course['cs_course_id'];
          }
          course_card($course);
        } ?>
        
        
        </ul>
        <?php if(isset($total_courses) && $total_courses > 8): ?>
        <div class="btn-center-div" id="load_more_courses_list">
          <a href="<?php echo site_url('/course/listing')?>" class="btn  orange-flat-btn  orange-course-btn inline-blk">Load More Courses</a>
        </div>
        <?php endif; ?>
        <!--btn-center-div--> 
      </div>
      <!--changed-container-for-forum--> 
    </div>
    <!--container--> 
  </div>
  <!--ex-course--> 
</section>
<!--Course section end-->
<?php endif; ?>
<!--Testimonials section start-->
<?php  if(!empty($testimonials)): ?>
<section>
  <div class="testimonial-page clearfix">
    <h2 class="hear-what"><b><?php echo lang('what_students_have_to_say');?></b></h2>
    <div class="wrapper testimonial-slider-wrapper">
        <div class="owl-carousel owl-theme">
          <?php foreach($testimonials as $testimonial): ?>
          <div class="item">
            <!-- ============= -->
            <div class="testimonial-card testimonial-slide">
                <div class="testimonial-author-info">
                  <div class="avatar">
                      <img src="<?php echo testimonial_path().$testimonial['t_image']; ?>" alt="">
                  </div>
                  <div class="author-details">
                      <h4 class="name"><?php echo $testimonial['t_name']?></h4>
                      <h5 class="designation"><?php echo $testimonial['t_other_detail']?></h5>
                  </div>
                </div>
                <div class="testimonial-writeup">
                  <p><?php echo $testimonial['t_text']?></p>
                </div>
            </div>
            <!-- =============== -->
          </div>
          <?php endforeach; ?> 
        </div>
      <div class="btn-center-div">
        <a href="<?php echo site_url('testimonials'); ?>" class="btn testimonial-more-btn orange-flat-btn  orange-course-btn more-changes-btn-padding inline-blk">View More Testimonials</a>
      </div>   
    </div><!--wrapper-->
  </div>
</section>
<!--Testimonial section end-->
<?php endif;  ?>

<script type="text/javascript">
  
    // < ?php if(!empty($information_bars)):?>
    // $(document).ready(function(){
    //   $('#information-modal').modal('show');
    // });
    // < ?php endif;?>

 
  // $(document).on('click','#Layer_1',function(){
  //   $('html,body').animate({
  //       scrollTop: $('#course_wrapper').offset().top
  //   }, 800);
  // });
  $("#searchid").keypress(function(e){
    var val = $("#searchid").val().trim();
        if(e.which == 13) {
          if(val.length=="0") {
             $(this).css('border', '2px solid rgb(220, 81, 81)'); 
          } else {
            $("#searchbtn").click();
          } 
        }
  });
  $("#searchbtn").click(function(){
    var val = $("#searchid").val().trim();
    var res = val.split(" ");
        str = encodeURIComponent(val);
        if(val.length=="0") {
          $("#searchid").css('border', '2px solid rgb(220, 81, 81)'); 
        } else {
          window.location = base_url + 'course/listing?search='+str;
        }
  });
</script>
<?php include_once 'modals.php'; ?>
<?php include 'footer.php'; ?>
<script src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/js/owl.carousel.js"></script>
<script type="text/javascript">
// Testimonial - Owl Slider
$('.owl-carousel').owlCarousel({
    loop:true,
    margin:10,
    nav:false,
    responsive:{
        0:{
            items:1
        },
        600:{
            items:1
        },
        1000:{
            items:2
        }
    }
})
</script>
