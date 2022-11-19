<link rel="stylesheet" href="<?php echo assets_url() ?>css/datepicker.min.css">
<link rel="stylesheet" href="<?php echo assets_url() ?>css/timepicker.css">
<?php include_once 'training_header.php';?>
<section class="content-wrap cont-course-big nav-included content-wrap-align content-wrap-top">
    <div class="container-fluid nav-content nav-js-height content-filter-top content-filter-fullwidth d-flex align-center" style="justify-content: flex-end;">
        <div>
            <?php
                if (!empty($this->userPrivilege)) {
                    if (in_array($this->privilege['add'], $this->userPrivilege)) {
                    ?>
                        <a href="" class="btn btn-violet" data-toggle="modal" data-target="#addannouncement" onclick="model_init()">ADD ANNOUNCEMENT</a>
                                                        
                        <?php
                    }
                }
            ?>
        </div>
    </div>
    <!-- =========================== -->
    <div class="left-wrap col-sm-12">
        <div class="row">
            <div class="col-sm-12 " id="announcement">
                <div class="col-sm-12 " id="announcementblock">

                </div>
            </div>

        </div>
    </div>
</section>

<script src="<?php echo assets_url() ?>js/jquery.timeago.js"></script>


<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>

<script src="<?php echo assets_url() ?>js/redactor/js/redactor.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/table.js"></script>
<script src="<?php echo assets_url() ?>js/redactor/js/alignment.js"></script>

<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/datepicker.en.js"></script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.timepicker.min.js"></script>
<?php include_once 'training_footer.php';?>
<script src="<?php echo assets_url() ?>js/announcement.js?v=1.16"></script>
<script>
                        $(document).ready(function () {
                            startTextToolbar();
                            var today = new Date();
                            $(function () {
                                $('.full-questions').slimScroll({
                                    height: '100%',
                                    wheelStep: 3,
                                    distance: '10px'
                                });
                            });
                            $(".an_date").datepicker({
                                language: 'en',
                                minDate: today
                            });
                            loadAnouncementsAdmin();
                        })
</script>

<!-- Modal pop up contents:: Add new announcement popup-->
<div class="modal fade" id="addannouncement" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-small" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="icon icon-cancel-1"></span>
                </button>
                <h4 class="modal-title" id="create_box_title">Announcement</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" id="an_error_text" style="display: none">
                    <span class="alert_close_icon" data-dismiss="alert">&times;</span>
                    <div id="an_error"></div>
                </div>
                <div class="ann_add_step1">
                    <div class="form-group">
                        <label for="">Title: * </label>
                        <br>
                        <input type="text" name="an_title" id="an_title" onkeyup="validateMaxLength(this.id)" maxlength="50" class="form-control"
                               placeholder="Announcement Title">
                        <span id="an_title_char_left" class="pull-right light-grey">50 Characters left</span>
                    </div>
                    <div class="form-group">
                        <label for="">Description: *</label>
                        <textarea name="an_description" id="an_description" placeholder="Enter Announcement" onkeyup="validateMaxLength(this.id)"
                                  maxlength="1000" class="form-control redactor" rows="2"></textarea>
                        <span id="an_description_char_left" class="pull-right light-grey">maximum characters1000 </span>
                    </div>
                </div>
                <div class="ann_add_step2" style="display:none;">
                    <div class="form-group">
                        <label for="">Send Announcement To: * </label>
                        <div class="radio" style="display:inline;">
                            <label onclick="showAndGetstudent()">
                                <input type="radio" name="ann_to" checked="checked" value="1">All Student</label>
                        </div>
                        <div class="radio " style="display:inline;">
                            <label>
                                <input onchange="load_institutions()" type="radio" name="ann_to" value="3">Institution</label>
                        </div>
                        <div class="radio" style="display:inline;">
                            <label>
                                <input onchange="load_groups()" type="radio" name="ann_to" value="2">Batch </label>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="institution-select" style="display:none;">
                        <div class="inside-box pos-rel pad-top50" id="users_new_group_wrapper"  style="overflow-x: hidden;">
                            <div id="render_data" class="container-fluid nav-content pos-abslt width-100p nav-js-height">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button onclick="submit_announcement('<?php echo admin_url('course/announcement/' . $course['id']); ?>')" class="btn btn-green pull-right add-continue"
                        data-step="1" data-action="add" data-canid="">CONTINUE</button>
                <button type="button" class="btn btn-red" data-dismiss="modal">CANCEL</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal pop up contents:: Add new announcement popup end-->
<script>
    var __limit = '5';
    var __courseid = '<?php echo $course_id; ?>';
    var __defaultpath = '<?php echo default_user_path(); ?>';
    var __userpath = '<?php echo user_path(); ?>';
    var __privilege = <?php echo json_encode($this->privilege); ?>;
    var __userPrivilege = JSON.stringify(<?php echo json_encode($this->userPrivilege); ?>);
function startTextToolbar() {
    $('#redactor_invite').redactor({
        imageUpload: admin_url + 'configuration/redactore_image_upload',
        source: false,
        plugins: ['table', 'alignment'],
        callbacks: {
            imageUploadError: function(json, xhr) {
                alert('Please select a valid image');
                return false;
            }
        }
    });
}
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();   
});
</script>