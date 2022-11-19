<?php include_once 'bundle_header.php';?>
<style>
body{overflow: hidden !important;}
.list-style-wrap {
    max-height: 62vh !important;
    padding-bottom: 40px !important;
}   
</style>

<!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->
<section class="content-wrap cont-course-big top-spacing content-wrap-align">

    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12">

        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid pad0 course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12 pad0">
                    <h4 class="course-head"><?php echo lang('course_status') ?></h4>
                    <div class="rTable course-status-cont">
                        <div class="rTableRow">
                            <div class="rTableCell col-md-4 text-left no-padding" style="max-width:120px;">
                                <a class="text-center" href="<?php echo admin_url('bundle/basic/'.$bundle['id']).'?&filter=active' ?>"  stye="display:inline-block;">
                                    <span class="big-head text-center" style="display: block;">
                                        <?php echo $active_courses ?>
                                    </span>
                                    <p>
                                        Included Items
                                    </p>
                                </a>
                            </div>
                           
                        </div>
                    </div>

                    <h4 class="course-head"><?php echo strtoupper('Launch your Bundle in 03 easy steps');?></h4>
                    <div class="list-style-wrap container-fluid">
                        <h5 class="overview-info">You may have created and launched your own bundle already, kindly ignore if so. 
                        <br /><br />Newbies, these are the steps that needs to be followed:</h5>

                        <!-- Accordion panel -->
                        <div class="accordion-container">

                          <!-- Step 1 -->
                          <button class="accordion">
                                <?php
                                    if($image_status!='default.jpg'){
                                ?>
                                        <span class="icon-tick">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512; vertical-align: super;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }else{
                                ?>
                                        <span class="icon-close">&times;</span>
                                <?php
                                    }
                                ?>
                                <span class="step-title">STEP 1: SETTINGS</span>
                          </button>
                          <div class="panel">
                            <ul>
                                <li>- In the left menu, click on “Settings”.</li>
                                <li>- Upload the bundle image(size:760 X 420)px.</li>
                                <li>- Fill the basic details of the bundle, some of the details are mandatory.</li>
                                <li>- Click on “Save” button to store your basic settings.</li>
                            </ul>
                          </div>
                          <!-- Step 1 ends -->
    
                          <!-- Step 3 -->
                          <button class="accordion">
                            <?php
                                    if($active_courses < 1){
                                ?>
                                        <span class="icon-close">&times;</span>
                                        
                                <?php
                                    }else{
                                ?>
                                        <span class="icon-tick">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512; vertical-align: super;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }
                            ?>
                            <span class="step-title">STEP 2: ADD ITEMS</span>
                          </button>
                          <div class="panel">
                            <ul>
                                <li>- Click on “ADD Items TO BUNDLE” and start adding items to bundle. </li>
                                <li>- Bundle must contain items to make it active.</li>
                            </ul>
                          </div>
                          <!-- Step 3 ends -->

                          <!-- Step 4 -->
                          <button class="accordion">
                            <?php
                            
                                    if($bundle['active_status']=='1'){
                                ?>
                                        <span class="icon-tick">
                                            <svg id="Layer_1" style="enable-background:new 0 0 512 512; vertical-align: super;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">
                                                .st0{fill:#41AD49;}</style><g><polygon class="st0" points="434.8,49 174.2,309.7 76.8,212.3 0,289.2 174.1,463.3 196.6,440.9 196.6,440.9 511.7,125.8 434.8,49     "/></g>
                                            </svg>
                                        </span>
                                <?php
                                    }else{
                                ?>
                                        <span class="icon-close">&times;</span>
                                <?php
                                    }
                            ?>
                            <span class="step-title">STEP 3: ACTIVATION</span>
                          </button>
                          <div class="panel">
                            <ul>
                            <li>- In the bundle listing page click on the dropdown respective to the bundle and select “Activate”.</li>
                               <li>- On clicking “Activate” the bundle shall be launched. (At least one course is required to activate the bundle).</li>
                            </ul>
                          </div>
                          <!-- Step 4 ends -->

                        </div>
                        <!-- Accordion panel -->
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
<?php 
function event_day($live_event_date)
{
    $current_date    = date('Y-m-d');
    $total_days      =  round(abs(strtotime($current_date)-strtotime($live_event_date))/86400);
    switch ($total_days) {
        case 0:
            $day = lang('today');
        break;
        case 1:
            $day = lang('tommorrow');
        break;
        default:
            $day = $live_event_date;
        break;
    }
    return $day;
}
?>

<script>
// Accordion panel
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight){
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script> 
<script src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script>
$(document).ready(function(e) {
    $(function(){
        $('.list-style-wrap').slimScroll({
            height: '100%',
            width: '100%',
            wheelStep : 3,
            distance : '10px'
        });
    });
});    
</script>

<?php include_once 'training_footer.php'; ?>
