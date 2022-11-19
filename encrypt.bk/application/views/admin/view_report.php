<script> 
    var __controller         = '<?php echo $this->router->fetch_class() ?>'; 
</script>
<?php include_once 'header.php';?>     
        
    
        <!-- MAIN TAB --> <!-- STARTS -->
        <section class="courses-tab base-cont-top-nosidebar">
            <ol class="nav nav-tabs offa-tab">
                <!-- active tab start -->
                <li class="active">
                    <a href="<?php echo admin_url('report') ?>"> Assessment Report</a>
                    <span class="active-arrow"></span>
                </li>
                <!-- active tab end -->
                <li >
                    <a href="<?php echo admin_url('report/excel_report') ?>">Excel Report</a>
                    <span class="active-arrow"></span>
                </li>
            </ol>
        </section>
        <!-- MAIN TAB --> <!-- END -->
        



        <section class="content-wrap base-cont-top-nosidebar ">
            
            <!-- Nav section inside this wrap  --> <!-- START -->
                <!-- =========================== -->
               
                <div class="container-fluid nav-content nav-cntnt100">

                    <div class="row">
                        <div class="rTable content-nav-tbl" style="">
                            <div class="rTableRow">
                                


                                <div class="rTableCell dropdown">

                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="course_name"> Select Course <span class="caret"></span></a>
                                        <ul class="dropdown-menu white">
                                            <!-- <li><a href="javascript:void(0)">Select Course</a></li> -->
                                        <?php foreach($courses as $course){ ?>
                                            <li><a href="javascript:void(0)" onclick="select_course(<?php echo $course['id']; ?>, this)"><?php echo $course['cb_title']; ?></a></li>
                                        <?php } ?>
                                        </ul>

                                </div>

                                <div class="rTableCell dropdown">
                                    <?php //echo $current_lecture; die;?>
                                        <input type="hidden" id="hidden_lecture_id" value="">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" id="test_name"> Select Test <span class="caret"></span></a>
                                        <ul class="dropdown-menu white" id="test_list">
                                            <!-- <li><a href="javascript:void(0)">Select Test</a></li> -->
                                        <?php foreach($tests as $test){ ?>
                                            <li id="test_old"><a href="javascript:void(0)" onclick="select_test(<?php echo $test['id']; ?>,this)"><?php echo $test['cl_lecture_name']; ?></a></li>
                                        <?php } ?>
                                        </ul>

                                </div>


                                <div class="rTableCell" id="div_export">
                                    <!-- lecture-control start -->
                                    <a class="btn btn-green" id="export_results"> EXPORT </a>
                                    <!-- lecture-control end -->
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- =========================== -->
                <!-- Nav section inside this wrap  --> <!-- END -->

            <!-- LEFT CONTENT --> <!-- STARTS -->
            <!-- ===========================  -->

            <div class="left-wrap col-sm-12 profile-wrap report-wrap">

                <!-- Content Section --> <!-- START -->
                <!-- =========================== -->

                <div class="container-fluid">
                    <div class="col-sm-12 marg-top10">
                        <table class="rTable table-with-border width-100p" id="tblcontent">
                            
                        </table>
                    
                    </div>
                </div>
                <!-- =========================== -->
                <!-- Content Section --> <!-- END -->

            </div>
            <!-- ==========================  -->
            <!--  LEFT CONTENT--> <!-- ENDS -->

            
        </section>


<?php include_once 'footer.php';?>     
<script type="text/javascript" src="<?php echo assets_url() ?>js/report.js"></script>
