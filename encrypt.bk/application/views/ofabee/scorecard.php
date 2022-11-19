<?php include 'header.php'; $my_course_categories =array(); $my_wishlist_categories =array() ?>
<section id="nav-group">
    <?php include_once "dashboard_header.php"; ?>
</section>
 
<section>
<div class="all-challenges">
    <div>
        <div class="container-reduce-width">
              <div class="">
                <div id="dashboard-my-score-card" class="">
                    <section class="score-card-tabs" id="tab_test_report" style="display: block;">
                        <?php include_once 'report_test_report.php'; ?>
                    </section>
                </div>
            </div>  <!--container-reduce-width-->
        </div><!--container altr-->       
    </div><!--all-challenges-->
</section>


<?php include 'footer.php'; ?>