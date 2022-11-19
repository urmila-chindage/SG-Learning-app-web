<?php include_once 'header.php'; ?>

<?php include_once "grade_tab.php"; ?>

<section class="content-wrap base-cont-top-heading">
    <div class="left-wrap col-sm-12 pad0">

        <!-- Nav section inside this wrap  --> <!-- START -->
        <!-- =========================== -->

        <div class="container-fluid course-create-wrap">
            <div class="row-fluid course-cont">
                <div class="col-sm-12">
                    <div class="form-horizontal" id="ev_form">
                        <?php include_once('messages.php');?>
                        <form class="form-horizontal" id="grade_form" method="post" action="<?php echo admin_url('grade/submit_grade'); ?>">
                            <!-- Text Box  -->
                            <input type="hidden" name="grade_id" value="<?php echo base64_encode($grade['id']); ?>">
                            <div class="form-group">

                                <div class="col-sm-12">
                                    Grade Name * : 
                                    <input type="text" class="form-control" maxlength="10" placeholder="eg: A+, A, B+, B" name="gr_name" id="gr_name" value="<?php echo htmlentities($grade['gr_name']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                   Grade Range From * : 
                                    <input type="text" class="form-control" maxlength="10" placeholder="eg: 100, 95, 90, 85" name="gr_range_from" id="gr_range_from" value="<?php echo htmlentities($grade['gr_range_from']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                   Grade Range To * : 
                                    <input type="text" class="form-control" maxlength="10" placeholder="eg: 95, 90, 85, 80" name="gr_range_to" id="gr_range_to" value="<?php echo htmlentities($grade['gr_range_to']); ?>" />
                                </div>
                            </div>
                            <!-- This is a test message  -->
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <a href="<?php echo admin_url('grade'); ?>" class="pull-right btn btn-green marg10" >Back</a>
                                    <input type="button" id="gr_submit" class="pull-right btn btn-green marg10" value="SAVE">

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
<script src="<?php echo assets_url() ?>js/jquery.form-validator.min.js"></script>
</script>
<script>
    $(document).ready(function(){
       
        $('#em_message').redactor({
            minHeight: 300
        });
    });
    $( "#gr_submit" ).click(function() {
        var gr_name             = $('#gr_name').val();
        var gr_range_from       = $('#gr_range_from').val();
        var gr_range_to         = $('#gr_range_to').val();
        if(gr_name == ''|| gr_range_from == ''|| gr_range_to == ''){
            alert('Please fill all the required fields');
            return false;
        }else{
            $("#grade_form").submit();
        }
    });


</script>
<?php include_once 'footer.php'; ?>

