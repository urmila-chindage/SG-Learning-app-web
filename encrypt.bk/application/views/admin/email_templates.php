<?php include_once "header.php"; ?> 

<style>
.mail-temp-section{height: 47px !important;}
.mail-temp-section li{border-bottom: none !important;}
</style>

<section class="mail-temp-section courses-tab base-cont-top" > 
    <ol class="nav nav-tabs offa-tab">
        <li class="active">
            <a href="<?php echo admin_url('email_template'); ?>">Email Templates</a>
            <span class="active-arrow" style="background: rgb(246, 248, 250);"></span>
        </li>
        <li>
            <div class="input-group" style="width: 450px;">
                <input type="text" class="form-control srch_txt" id="email_keyword" placeholder="Search by name">
                <span id="searchclear" style="display: none;">×</span>
                <a class="input-group-addon" id="basic-addon2">
                    <i class="icon icon-search"> </i>
                </a>
            </div>
        </li>
    </ol>
</section>
<link rel="stylesheet" href="<?php echo assets_url('css').'datepicker.min.css'; ?>">
<link rel="stylesheet" href="<?php echo assets_url('css').'timepicker.css'; ?>">

<section class="content-wrap content-wrap-fullwidth base-cont-top">
    
    <!-- =========================== -->
    <!-- Nav section inside this wrap  --> <!-- END -->


    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12 pad0">

        <!-- Content Section --> <!-- START -->
        <!-- =========================== -->
        <div class="container-fluid">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap" id="show_message_div"> 
                    <div class="table course-cont only-course rTable" style="" id="event_row_wrapper">
    
                    </div>
                </div>
            </div>
        </div>
        <!-- =========================== -->
        <!-- Content Section --> <!-- END -->
        <script type="text/javascript" src="<?php echo assets_url('js').'datepicker.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo assets_url('js').'datepicker.en.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo assets_url('js').'jquery.timepicker.js'; ?>"></script>




    </div>
    <!-- ==========================  -->
    <!--  LEFT CONTENT--> <!-- ENDS -->


</section>

<script type="text/javascript">
   <?php /* var __emails            = atob('<?php// echo base64_encode(json_encode($email_templates)) ?>');
    var __actions           = atob('<?php //echo base64_encode(json_encode($actions)) ?>');*/?>
    // __actions               = jQuery.parseJSON(__actions);
    var __admin_url         = '<?php echo admin_url(); ?>';
    
    $(document).on('click', '#basic-addon2', function(){
    var email_keyword = $('#email_keyword').val().trim();        
        if(email_keyword == '')
        {
            lauch_common_message('KEYWORD MISSING', 'Please enter a valid input to search.');
        }
        else{
            __offset = 1;
            getTemplates();
            scrollToTopOfPage();
        }
    });

    var timeOut = '';
    $(document).on('keyup', '#email_keyword', function(){
        clearTimeout(timeOut);
        timeOut = setTimeout(function(){
            __offset = 1;
            getTemplates();
        }, 600);
        scrollToTopOfPage();
    });

    $(document).on('click', '#searchclear', function(){
        __offset = 1;
        getTemplates();
    });

    
</script>

<!-- Basic All Javascript -->
<script src="<?php echo assets_url() ?>js/email_templates.js"></script>
<!-- END -->
<?php include_once 'footer.php'; ?>

