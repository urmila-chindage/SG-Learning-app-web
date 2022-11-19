<?php include_once 'header.php';?>

    <!-- SIDEBAR RIGHT SIDE FIXED --> <!-- END -->

    <section class="content-wrap base-cont-top course-content-wrap nopadd-right">        
        <div class="container-fluid nav-content nav-course-content">
            <div class="row">
            <div class="rTable content-nav-tbl" style="">
                <div class="rTableRow"></div>
                <p class="role-title text-left no-margin">Log Action Points</p>
                
            </div>

            </div>
        </div>

        <div class="left-wrap col-sm-12">
            <div class="row">   
                <div class="col-sm-12 course-cont-wrap wrap-fix-course" > 
                    <div class="log-action-head">
                        <span><b>Log actions</b> </span>
                        <span class="title-points"><b>Points </b></span>
                    </div>

                    <?php 
                    foreach($log_actions as $log_action)
                    {
                    ?>
                    <div class="log-action-row">
                        <span class="log-action-info"><?php echo $log_action['la_action_name'] ?></span>
                        <div class="log-action-controls">
                            <input type="text" class="form-control" id="<?php echo "point_".$log_action['id'] ?>" value="<?php echo $log_action['la_points']; ?>">
                            <button class="btn btn-success" onclick="logPoints('<?php echo $log_action['id'] ?>')">Save</button>
                        </div>
                    </div>
                    <?php 
                    }
                    ?>
                    <div class="table course-cont only-course rTable">
                        
                        
                    </div>
                </div>
            </div>

        </div>
    </section>

<?php include_once 'footer.php';?> 
<script type="text/javascript" src="<?php echo assets_url() ?>js/system.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/role.js"></script>

<script>
function logPoints(id){
    
    var point = $('#point_'+id).val(); 
    $.ajax({
            url: admin_url+'log_action/save_points',
            type: "POST",
            data:{"is_ajax":true,"id":id,"point":point},
            success: function(response) {
                var data  = $.parseJSON(response);    
                if(data['error'] == false)
                {
                    var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                    };
                    callback_success_modal(messageObject);
                }else{
                    var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                    };
                    callback_danger_modal(messageObject);
                }
            }
    });
}

</script>



