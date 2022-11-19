<?php include_once 'coursebuilder/lecture_header.php';?>
<!-- ADDING REDACTOR PLUGIN INSIDE -->
<!-- ############################# --> <!-- START -->
<link rel="stylesheet" href="<?php echo assets_url() ?>css/redactor/css/alignment.css" />
<style>
    .noborder{border: none !important;}
    .restriction-table td{
        padding: 5px;
    }
    .restrict-btn{
         margin: 30px 0;
    }
    #addrow{
        font-size: 26px;
        font-weight: 800;
        color: #09bf63;
        cursor: pointer;
        user-select:none;
    }
    #addrow:hover{
        color:#048544;
    }
    .inline-block{
        display: inline-block;
    }
    .access-container{
        background: #fff;
        border-radius: 3px;
        padding: 30px;
        margin: 10px;
    }
    .ui-timepicker-standard{
        border: none;
    }
    .rem-icn{
        width: 25px;
        height: 25px;
        cursor: pointer;
    }
    td.percentage{width: 75px;}
    .delrow{
        font-size: 26px;
        font-weight: 800;
        color: #f70000;
        cursor: pointer;
        user-select:none;
    }
    .addtest-checkbox input[type='checkbox'] {
        opacity: 1;
        left: 0px;
        margin-right: 0px;
        width: 18px;
        height: 18px;
        top: 0px;
        z-index: 999;
        cursor: pointer;
    }
 
</style>

    <?php include_once('test_header.php'); ?>
    <div class="right-wrap small-width base-cont-top-heading container-fluid pull-right rightwrap-top-update">
        <br/>
        <div class="list-group test-listings">
            <a href="javascript:void(0)" class="list-group-item active">
                <span class="font15">Instructions</span>
            </a>
            <a href="javascript:void(0)" class="list-group-item link-style">
                <span class="green-span"><i class="icon icon-ok-circled"></i></span> 
                <span class="listing-text">Once the quiz is published, then only the student can access it.</span>
            </a>
            <a href="javascript:void(0)" class="list-group-item link-style">
                <span class="green-span"><i class="icon icon-ok-circled"></i></span> 
                <span class="listing-text">The quiz can be accessible by the students only between the scheduled date and time.</span> 
            </a>
        </div> 
    </div>
    <section class="content-wrap small-width base-cont-top-heading content-top-update">
        <!-- LEFT CONTENT --> <!-- STARTS -->
        <!-- =========================== -->
        <div class="left-wrap col-sm-12 pad0">
        <!-- Nav section inside this wrap --> <!-- START -->
        <!-- =========================== -->
            <div class="container-fluid course-create-wrap">

                <div class="row-fluid course-cont">
                    <div class="col-sm-12">
                        <div class="form-horizontal" id="course_form">
                            <div class="each-steps" id="step-four">
                                <div class="form-group"> 
                                    <div class="row">
                                        <div class="col-sm-12 arrangement-grouping">
                                            <div class="arrangement-panel">Access Restriction Settings</div>
                                                <div class="addtest-container-new">
                                                    <div class="addtest-checkbox" style="border-bottom:solid 1px #ccc;padding-bottom:15px;">
                                                        <div class="access-container">
                                                    
                                                            <?php include_once 'coursebuilder/access_restriction_quiz.php';?>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>
                                        </div> 
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <input onclick="saveNext()" type="button" id="saveNext_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE & NEXT">
                                        <input onclick="saveAccessRestriction()" type="button" id="save_button" class="pull-right btn btn-green marg10" data-div="step-two" value="SAVE">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </section>

<script type="text/javascript">
   function saveNext() {
        $('#redirect_to').val('<?php echo admin_url('test_manager/test_assign/').base64_encode($test['id']) ?>');
        saveAccessRestriction();
    }

    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) 
        {
            return false;
        }
        return true;
    }
</script>

<?php include_once 'footer.php';?>