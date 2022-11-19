<script> var __controller         = '<?php echo $this->router->fetch_class() ?>'; </script>
<?php include_once 'header.php';?>     
        
    
        <!-- MAIN TAB --> <!-- STARTS -->
        <?php include_once('report_tab.php') ?>
        <!-- MAIN TAB --> <!-- END -->
        
        <section class="content-wrap base-cont-top-nosidebar ">
            <div class="col-sm-12 marg-top10">
                <div class="basic-report">
                    <a href="<?php echo admin_url('report/course_report') ?>" class="btn btn-green" > BASIC REPORT</a>
                </div>

                <div class="advanced-report padding-top">
                    <a href="<?php echo admin_url('report/course_details') ?>" class="btn btn-green" >ADVANCED REPORT</a>
                </div>
            </div>
            
        </section>
        
<?php include_once 'footer.php';?>