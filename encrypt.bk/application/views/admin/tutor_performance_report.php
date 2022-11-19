<?php  include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<style>
.course-report-filter{
    height: 50px;
    width: 100%;
    padding: 15px;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    font-size: 14px;
    font-weight: 600;
}
.dropdown-menu.white {height: 209px;overflow: auto;}
.dropdown-menu.white.multi-drop{padding-left:15px;}
.rTable.content-nav-tbl .rTableRow > .rTableCell label{ padding: 0 0 13px;}
.load-reports{width: 100px;display: inline-block;left: 27px;position: absolute;top: 60px;}
</style>
<?php $selected = "selected='selected'"; ?>

<div class='dashbrd-container pos-top50 main-content'>
    <div class="course-report" id="course_report_container_wrapper" <?php echo ((!empty($lectures)) && (!empty($subscribers)))?'':'style="visibility: hidden;"'  ?> >

    	   <!-- Generate student grade report -->
            <div class="stud_grade_report" style="visibility:visible;">
                <div class="report_title text-center"><h4>Generate Tutor Performance Report</h4></div>
                <div class="grade_select_container">
                    <div class="half-width">
                        <div class="course-filter">
                            <select name="" onchange="getSurvey()" class="course-report-filter" id="tutor_select">
                                <option value="">Select Tutor</option>
                                <?php if(!empty($tutors)): ?>
                                <?php if($admin['role_id'] == '3'): ?>
                                <?php foreach($tutors as $tutor): 
                                if($tutor['id'] == $admin['id']):?>
                                    <option <?php echo (($tutor['id'] == $tutor_id)?$selected:'') ?>  value="<?php echo $tutor['id'] ?>"><?php echo $tutor['us_name'] ?></option>
                                <?php
                                endif;
                                endforeach;?>
                                <?php else: ?>
                                <?php foreach($tutors as $tutor): ?>
                                    <option <?php echo (($tutor['id'] == $tutor_id)?$selected:'') ?>  value="<?php echo $tutor['id'] ?>"><?php echo $tutor['us_name'] ?></option>
                                <?php endforeach;?>
                                <?php endif; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                     </div>
                     <div class="half-width">
                        <div class="course-filter">
                            <select name="" class="course-report-filter" id="survey_select" onchange="updateUrlState()">
                                <option value="">Select Survey</option>
                                <?php if(isset($surveys)): ?>
                                <?php foreach($surveys as $survey): ?>
                                    <option <?php echo (($survey['id']==$survey_id)?$selected:'') ?> value="<?php echo $survey['id'] ?>" ><?php echo $survey['s_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="pull-right filter-btn text-center" style="width:100%;">
                        <button class="btn btn-green selected" onclick="exportReport()">EXPORT REPORT</button>
                    </div>
                </div>
            </div>
            <!-- Generate student grade report ends -->
    </div>

</div>

<script type="text/javascript" src="<?php echo assets_url() ?>js/jquery.slimscroll.min.js"></script>
<script type="text/javascript">

    
function getSurvey(){
    updateUrlState();
    var tutorId                = $("#tutor_select").val();
    $.ajax({
            url: webConfigs('admin_url')+'report/get_surveys',
            type: "POST",
            data:{ "is_ajax":true,"tutor_id":tutorId},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    if (data['surveys'].length > 0) {
                        $('#survey_select').html('');
                        var surveyHtml      = '<option value="" >Select Survey</option>';
                        for (var i in data['surveys']) {
                            surveyHtml += '<option value="'+data['surveys'][i]['id']+'">'+data['surveys'][i]['s_name']+'</option>';
                        }
                        $('#survey_select').html(surveyHtml);
                    }
                    else
                    {
                        var surveyHtml      = '<option value="" >Select Survey</option>';
                        $('#survey_select').html(surveyHtml);
                    }
                }
            }
        });
}

function exportReport()
{
    var survey_id       = $('#survey_select').val();
    var tutor_id        = $('#tutor_select').val();
    
    if(tutor_id == ""){
        var messageObject = {
            'body':'Please choose tutor',
            'button_yes':'OK', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    }
    if(survey_id == ""){
        var messageObject = {
            'body':'Please choose survey',
            'button_yes':'OK', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    }
    var param               = {};
        param['survey_id']  = survey_id;
        param['tutor_id']   = tutor_id;
    location.href       = webConfigs('admin_url')+'report/survey_report/'+btoa(JSON.stringify(param));
}

function updateUrlState() {
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            link = link.substring(0, link.indexOf('?'));
        var tutorId = $.trim($('#tutor_select').val());
            if(tutorId != '') {
                link += '?tutor_id=' + tutorId;
                var surveyId = $.trim($('#survey_select').val());
                    if(surveyId != '') {
                        link += '&survey_id=' + surveyId;
                    }
                
            }
            window.history.pushState({
                path: link
            }, '', link);
    }
}
</script>
<script type="text/javascript" src="<?php echo assets_url() ?>js/multi-select/jquery.tokenize.js"></script>
<script>
$(document).ready(function(e) {
    $(function(){
        $('.right-box').slimScroll({
            height: '100%',
            width: '100%',
            wheelStep : 3,
            distance : '10px'
        });
    });
});
</script>
<?php include_once 'footer.php'; ?>

