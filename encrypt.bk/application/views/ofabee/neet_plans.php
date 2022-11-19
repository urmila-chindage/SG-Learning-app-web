<?php include_once('header_beta.php'); ?>

        <div class="plan-wrap">
            <h1 class="join-pro">Join Pro</h1>
            <?php if(isset($test_detail)&&!empty($test_detail)): ?>
                <h3 class="join-pro-sub">These are the available plans in the selected test "<?php echo $test_detail['cl_lecture_name']; ?>".</h3>
            <?php else: ?>
                <h3 class="join-pro-sub">These are the available plans for you.</h3>
            <?php endif; ?>
            <?php
            $column = 1;
            ?>
            <div class="plans-wrap">
                <?php foreach($plans as $p_key => $plan): ?>
                    <!-- plan -->
                    <div class="plan">
                        <div class="plan-head-wrap plan-taruish-bg">
                            <span class="plan-alpha">Plan <?php echo $count[$p_key]; ?></span>
                            <span class="plan-type"><?php echo $plan['p_name']; ?></span>
                            <span class="plan-alpha"><?php echo $plan['p_slogan']; ?></span>
                        </div>
                        <!-- plan-head-wrap -->
                        <div class="plan-body">
                            <span class="plan-body-head adavncedBodyheadcol"><?php echo $plan['p_short_description']; ?></span>
                            <div class="plans-list-div">
                                <ul class="plan-ul">
                                    <?php $p_features       = json_decode($plan['p_plan_features'],true); 
                                    if(!empty($p_features)):
                                    ?>
                                    <?php foreach($p_features as $p_feature): ?>
                                        <li>
                                            <span class="plan-green-bullet plan-torqusih"></span>
                                            <span class="plan-green-bullet-text"><?php echo $p_feature; ?></span>
                                        </li>
                                    <?php endforeach; 
                                    endif;
                                    ?>
                                </ul>
                                <div class="plan-smoke"></div>
                            </div>
                            <!-- plans-list-div -->
                            <a class="viewmore-green view-more-torquish" href="javascript:void(0)">View More</a>
                            <div class="plan-price-wrap plan-price-torquish">
                                <svg enable-background="new 0 0 50 50" version="1.1" viewBox="0 0 50 50" xml:space="preserve" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M40.971,3.793H28.452c2.202,1.745,3.793,4.25,4.25,7.741h8.269v3.793h-8.195C32.02,23.065,25.873,27.998,16.161,28.3   c5.918,6.147,12.368,13.885,18.74,21.7h-7.284C21.7,42.868,16.161,36.267,9.029,28.68v-4.704h4.173   c8.195,0,12.597-3.413,13.125-8.649H9.029v-3.793h17.072c-1.063-4.402-5.084-6.601-11.685-6.601H9.029V0h31.942V3.793z"/>
                                        </svg>
                                <div class="plan-price-amt"><?php echo $plan['p_plan_type']==0?$plan['p_price']:'FREE'; ?></div>
                            </div>
                            <?php
                                $p_validity_text        = ($plan['p_validity_type'] == 1)?($plan['p_validity']/30):$plan['p_validity'];
                                $validity               = '';
                                switch($plan['p_validity_type']){
                                    case 1:
                                        $validity        = ($p_validity_text>1)?' Months':' Month';
                                    break;
                                    default;
                                        $validity        = ($p_validity_text>1)?' Days':' Day';
                                    break;
                                }
                                $validity           = $p_validity_text.$validity;
                            ?>
                            <span class="plan-moth-duration"><?php echo $validity; ?></span>
                            <a class="plan-btn torqusih-btn-bg" href="<?php echo site_url('checkout/plan').'/'.base64_encode($plan['id']); ?>"><?php echo $plan['p_plan_type']==0?'Buy Now':'Enroll'; ?></a>
                        </div>
                        <!-- plan-body -->
                    </div>
                    <!-- plan -->
                    <?php 
                        if($column == 3)
                        {
                            echo '</div><div class="plans-wrap">';
                            $column = 0;
                        }
                        $column++;
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
        <!-- plan-wrap -->


    </div>
    <!-- dashbord-wrap -->


    <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo assets_url() ?>themes/<?php echo $this->config->item('theme') ?>/neet/assets/js/circle-progress.js"></script>

    <script>
        $(document).ready(function() {
            $(".viewmore-green").on("click", function() {
                $(this).prev().toggleClass("plan-full-show");
                $(this).prev().find(".plan-smoke").toggle();
                if ($(this).html() == "View More") {
                    $(this).html("View Less");
                } else {
                    $(this).html("View More");
                }
            });
        })
    </script>
</body>

</html>