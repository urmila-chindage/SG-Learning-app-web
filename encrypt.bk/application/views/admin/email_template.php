<?php include_once 'header.php'; ?>

<?php include_once "email_tab.php"; ?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/redactor.css" />
    <link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<!-- ############################# --> <!-- END -->
<!-- MAIN TAB --> <!-- STARTS -->

<!-- MAIN TAB --> <!-- END -->

<!-- instruction wrapper -->
<div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
    <br/>
    <div class="list-group test-listings">
      <a href="javascript:void(0)" class="list-group-item active">
        <span class="font15">Instructions</span>
      </a>
      <a href="javascript:void(0)" class="list-group-item link-style">
          <span class="green-span"><i class="icon icon-ok-circled"></i></span>          
          <span class="listing-text">
            Please donot change any system variable. System variables are variables that is given inbetween <b>"{"</b> and <b>"}"</b> symbol. System variabes are used by system to generate dynamic values.
          </span></a>
    </div>
    <!--  Adding list group  --> <!-- END  -->
</div>
<!-- instruction wrapper ends -->

<section class="content-wrap contentwrap-custom-padding base-cont-top-heading">

    <!-- LEFT CONTENT --> <!-- STARTS -->
    <!-- ===========================  -->

    <div class="left-wrap col-sm-12 pad0">

        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="ev_form">
                        <!-- <?php //include_once('messages.php');?> -->
                        <form class="form-horizontal" onsubmit="return validateForm();" id="email_form" method="post" action="<?php echo admin_url('email_template/submit_email_template'); ?>">
                            <!-- Text Box  -->
                            <input type="hidden" name="email_id" value="<?php echo base64_encode($email_template['id']); ?>">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    Email Template Name * : 
                                    <input type="text" class="form-control" maxlength="80" placeholder="eg: Event Name" name="em_name" id="em_name" value="<?php echo htmlentities($email_template['em_name']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    Email Template Subject * : 
                                    <input type="text" class="form-control" id="em_subject" name="em_subject" value="<?php echo $email_template['em_subject'] ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    Email Template Message * : 
                                    <textarea class="form-control" id="em_message" name="em_message" value=""><?php echo $email_template['em_message'] ?></textarea>
                                </div>
                            </div>
                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <a href="<?php echo admin_url('email_template'); ?>" class="pull-right btn btn-green marg10" >Back</a>
                                    <input type="submit" id="ev_submit" class="pull-right btn btn-green marg10" value="SAVE">

                                </div>
                            </div>

                        </form>
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
<!-- JS -->
<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>
<script src="<?php echo assets_url() ?>js/system.js"></script>
<?php include_once 'footer.php'; ?>

<script>
    $(document).ready(function(){
       
        $('#em_message').redactor({
            minHeight: 300
        });
    });
    // $( "#ev_submit" ).click(function() {
    //     var em_name             = $('#em_name').val();
    //     var em_subject          = $('#em_subject').val();
    //     var em_message          = $('#em_message').val();
    //     if(em_name == ''|| em_subject == ''|| em_message == ''){
    //         alert('Please fill all the required fields');
    //         return false;
    //     }else{
    //         $("#email_form").submit();
    //     }
    // });

    function validateForm(){

        var em_name         = $('#em_name').val();
        var em_subject      = $('#em_subject').val();
        var em_message      = $('#em_message').text();
        var errorCount      = 0;
        var errorMessage    = '';
// //console.log(em_message);
        if(em_name == ''){
            errorCount++;
            errorMessage += 'Template name is required<br/>';
        }
        if(em_subject == ''){
            errorCount++;
            errorMessage += 'Template subject is required<br/>';
        }
        if(em_message.trim() == ''){
            errorCount++;
            errorMessage += 'Template message is required<br/>';
        }
        if(errorCount > 0){
            goTo();
            $('#email_form').prepend(renderPopUpMessage('error', errorMessage));
            return false;
        }else{
            return true;
        }
    }
    function goTo(){
        $([document.documentElement, document.body]).animate({
        scrollTop: $("body").offset().top
        }, 500);
    }
</script>