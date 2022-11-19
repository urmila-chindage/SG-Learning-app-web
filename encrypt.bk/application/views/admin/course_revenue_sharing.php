<?php include_once 'header.php';?>
<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>

<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<!-- ############################# --> <!-- END -->
<link href="<?php echo assets_url() ?>css/bootstrap-tagsinput.css" rel="stylesheet" type="text/css" />
<!--Tag input js-->
<script src="<?php echo assets_url() ?>js/bootstrap-tagsinput.js" type="text/javascript"></script>

<!-- MAIN TAB --> <!-- STARTS -->
<section class="courses-tab base-cont-top-heading">
    <h4> <?php echo isset($cb_title)?$cb_title:'' ;?></h4>
    <ol class="nav nav-tabs offa-tab" style="padding-left: 40px;">
        <!-- active tab start -->
        <li>
            <a href="<?php echo admin_url('course_settings').'basics/'.$id ?>"> <?php echo lang('basic_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <!-- active tab end -->
        <li>
            <a href="<?php echo admin_url('course_settings').'advanced/'.$id ?>"><?php echo lang('advanced_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <li>
            <a href="<?php echo admin_url('course_settings').'seo/'.$id ?>"><?php echo lang('seo_tab') ?></a>
            <span class="active-arrow"></span>
        </li>
        <?php if(!isset($teacher)):
        if((!isset($cb_is_free)) || ($cb_price > 0)){ ?>
            <li class="active">
                <a href="<?php echo admin_url('course_settings').'revenue/'.$id ?>"><?php echo lang('revenue_tab') ?></a>
                <span class="active-arrow"></span>
            </li>
        <?php } 
        endif;?>
    </ol>
</section>
<!-- MAIN TAB --> <!-- END -->
            
<?php include_once 'course_sidebar.php';?>
<?php 
        $admin    = $this->auth->is_logged_in(false, false, 'admin'); 
        $teacher  = $this->auth->is_logged_in(false, false, 'teacher');

?>



<section class="content-wrap small-width base-cont-top-heading">
    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->
    <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid course-create-wrap">

            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="course_form">
                        <?php include_once('messages.php') ?>
                        <?php 
                            $admin_share    = '0';
                            $teacher_share  = '0';
                            if($cb_revenue_share != 0 ){ 
                                if($cb_discount!=0){ 
                                    $admin_share   = ($cb_revenue_share/100)*$cb_discount;
                                    $teacher_share = ((100-$cb_revenue_share)/100)*$cb_discount;
                                }else{
                                    $admin_share   = ($cb_revenue_share/100)*$cb_price;
                                    $teacher_share = ((100-$cb_revenue_share)/100)*$cb_price;
                                }
                                $admin_share = round($admin_share, 2);
                            } 
                        ?>
                        <?php if($admin){ ?>
                        <form class="form-horizontal" id="save_course_basics" method="post" action="<?php echo admin_url('course_settings/revenue/'.$id); ?>">
                            <!-- Text Box  -->
                            <div class="form-group">

                                <div class="col-sm-6">
                                    <?php echo $this->config->item('site_name') ?> share
                                    <div class="input-group">
                                        <?php /* ?><input type="text" maxlength="3" name="cb_revenue_share" class="form-control" id="cb_revenue_share" data-validation-allowing="range[1;100]" data-validation-error-msg-number="<?php echo lang('revenue_range_error_per') ?>" value="<?php echo isset($cb_revenue_share)?$cb_revenue_share:'' ;?>" aria-describedby="basic-addon1" data-validation="number" data-validation-error-msg-required="Enter numeric value"><?php */ ?>
                                        <input onkeypress="return preventAlphabetsPercentage(event)" type="text" maxlength="4" name="cb_revenue_share" class="form-control" id="cb_revenue_share" value="<?php echo isset($cb_revenue_share)?$cb_revenue_share:'' ;?>" aria-describedby="basic-addon1" >
                                            <span class="input-group-addon" id="basic-addon1"><?php echo lang('per_required_symbol') ?></span>
                                        </div>
                                </div>
                            </div>
                         
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <input type="button" id="course_revenue_button" class="pull-left btn btn-green marg10" value="<?php echo lang('save') ?>">

                                </div>
                            </div>
                        </form>
                        <?php 
                            //if($cb_revenue_share != 0 ){ ?>

                                <div class="alert alert-success" id="share_percentage" style="background-color: #C0C0C0; color: #000;margin-top:70px;">

                                    <?php if($cb_discount == 0 && $cb_price == 0) { ?>
                                     The cost of <strong><?php echo $cb_title;?></strong> is Rs.<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>/-. So, there is no share for this course subscription.       

                                    <?php }else{ ?>
                                    <table style="width:100%">
                                        <tr>
                                            <th>Title</th>
                                            <th>Price</th>
                                            <th><?php echo $this->config->item('site_name') ?> share</th>
                                            <th>Teacher share</th>
                                            <th>You get</th>
                                        </tr>
                                        <tr>
                                            <td><?php echo $cb_title;?></td>
                                            <td>Rs.<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>/-</td>
                                            <td><?php echo $cb_revenue_share ?>%</td>
                                            <td><?php echo (100-$cb_revenue_share) ?>%</td>
                                            <td>Rs.<?php echo $admin_share?>/-</td>
                                        </tr>
                                    </table> 
                                     
                                    <?php /* ?>The cost of <strong><?php echo $cb_title;?></strong> is Rs.<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>/- and your share of this course is <?php echo $cb_revenue_share ?>%. So, You will get Rs.<?php echo $admin_share;?>/- for each subscription. <?php */ ?>
                                    
                                    <?php } ?>      

                                </div>
                        <?php   //} 
                        ?>
                        
                        </div>

                        <?php }/*else { 

                            if($cb_revenue_share != 0 ){ ?>
                                <div class="alert alert-success" style="background-color: #C0C0C0; color: #000;margin-top:70px;">
                                    <?php if($cb_discount == 0 && $cb_price == 0) { ?>
                                         The cost of <strong><?php echo $cb_title;?></strong> is Rs.<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>/-. So, there is no share for this course subscription.       

                                    <?php }else{ ?>
                                    The cost of <strong><?php echo $cb_title;?></strong> is Rs.<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>/- and your share of this course is <?php echo (100-$cb_revenue_share) ?>%. So, You will get Rs.<?php echo $teacher_share; ?>/- for each subscription. 
                                    <?php } ?>          
                                </div>
                        <?php    } 
                        }*/ ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Nav section inside this wrap  --> <!-- END -->
    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->
</section>

<script type="text/javascript">

var course_title   = '<?php echo $cb_title ?>';       
var original_price = '<?php echo $cb_price ?>';
var discount_price = '<?php echo $cb_discount ?>';
var price = '<?php echo ($cb_discount)?$cb_discount:$cb_price; ?>';
</script>
<!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/source.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/course_settings.js"></script>
<?php include_once 'footer.php';?>