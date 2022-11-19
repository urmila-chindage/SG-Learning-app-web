<?php $class_name = $this->router->fetch_class();  ?>
<?php $sidebar_contents = get_sidebar_contents($category_id,$class_name); //echo '<pre>';print_r($sidebar_contents['challenge_zone'][0]['id']);die; ?>
<?php //echo '<pre>'; print_r($sidebar_contents); ?>
<div class="col-sm-3 col-md-3 col-lg-3 category-sidebar col-lg-pull-9 col-md-pull-9 col-sm-pull-9">
    <div class="category-sidebar-contents">
    <?php if(!empty($sidebar_contents['rated_course'])) : ?> 
        <h3>Explore Courses</h3>
        <a class="block-link" href="<?php echo site_url().$sidebar_contents['rated_course']['cb_slug']; ?>">
            <div class="course-block-1">
                <div class="course-top-half">
                <div class="block-load-in" id="whishdiv_<?php echo $sidebar_contents['rated_course']['id']; ?>">
                    <?php echo $sidebar_contents['rated_course']['whishlist']; ?>
                </div>
                    <?php 
                        $image_first_name   = substr($sidebar_contents['rated_course']['cb_image'],0,-4);
                        $image_dimension    = '_300x160.jpg';
                        $image_new_name     = $image_first_name.$image_dimension;
                    ?>
                    <img src="<?php echo (($sidebar_contents['rated_course']['cb_image'] == 'default.jpg')?default_course_path():  course_path(array('course_id' => $sidebar_contents['rated_course']['id']))).$image_new_name; ?>" class="card-img-fit sidebar-card"> 
                </div><!--course-top-half--> 
                <div class="courser-bottom-half">
                    <label class="block-head"><?php echo $sidebar_contents['rated_course']['cb_title']; ?></label> 
                    <?php $tutor_names = array(); foreach($sidebar_contents['rated_course']['lectures'] as $course_tutor) : 
                        $tutor_names[] = $course_tutor['us_name'];
                    endforeach; ?>            
                    <p class="sub-head-des"><?php echo 'By '; ?><?php echo (empty($sidebar_contents['rated_course']['lectures']))?$sidebar_contents['admin']:implode(', ',$tutor_names) ?></p>
                    <div class="star-ratings-sprite star-ratings-sprite-block"><span style="width:<?php echo $sidebar_contents['rated_course']['rating']*20; ?>%" class="star-ratings-sprite-rating"></span></div>
                    <label class="amount"><?php echo ($sidebar_contents['rated_course']['cb_is_free'] == '1')?'FREE':(($sidebar_contents['rated_course']['cb_discount'] != '0')?"RS. ".$sidebar_contents['rated_course']['cb_discount']:"RS. ".$sidebar_contents['rated_course']['cb_price']); ?></label>
                    <label class="discount"><?php echo ($sidebar_contents['rated_course']['cb_is_free'] == '1')?'':(($sidebar_contents['rated_course']['cb_discount'] != '0')?"RS. ".$sidebar_contents['rated_course']['cb_price']:''); ?></label>
                </div> <!--courser-bottom-half-->          
            </div> <!--course-block-1-->  
        </a> 
        <span class="read-more block"><a href="<?php echo site_url($sidebar_contents['course_slug']->ct_slug); ?>">View More Courses</a></span>
        <span class="hr-line pt20"></span>
    <?php endif; ?>
    
    <?php /* ?><?php if($class_name != 'course'): ?>
    <a class="btn orange-flat-btn orange-course-btn more-changes-btn-padding full-btn rounded mt20 generate-test" href="<?php echo site_url().'course/generate_test_view/'.$category_id; ?>">Generate Test</a>
    <?php endif; ?><?php */ ?>
              
    </div>
</div>
    <?php if(!empty($header_categories)): ?>
       <div class="col-sm-3 col-md-3 col-lg-3 category-sidebar col-lg-pull-9 col-md-pull-9 col-sm-pull-9">
           <ul class="list-group">
           <?php foreach ($header_categories as $category): ?>
                 <a href="<?php echo site_url($category['ct_slug']) ?>" class="<?php echo (($this->uri->segment(2)==$category['id'])?'active-category':'') ?> break-word list-group-item list-group-item-action"><?php echo $category['ct_name']; ?></a>
           <?php endforeach; ?>
           </ul> 
       </div>
   <?php endif; ?>
<style>
.break-word{ word-wrap: break-word;}
.active-category{ background: #f5f5f5; color: #f58700 !important;}
</style>
<script type="text/javascript">
    $( ".block-load-in" ).click(function( event ) {
        event.preventDefault();
    });
</script>
<script type="text/javascript" src="<?php echo assets_url().'themes/'.$this->config->item('theme').'/js/sidebar.js'; ?>" ></script>