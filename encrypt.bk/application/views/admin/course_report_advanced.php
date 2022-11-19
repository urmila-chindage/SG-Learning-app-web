<?php  include_once 'header.php'; ?>
<?php include_once('report_tab.php') ?>
<style>
.width-auto {width: auto !important;}
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
                <div class="report_title text-center"><h4>Generate Student Grade Report</h4></div>
                <div class="grade_select_container">
                    <div class="course-filter">
                        <select name="" class="" id="course-report-filter" onchange="updateUrlState()">
                            <option value="0">Select Course</option>
                            <?php foreach($courses as $course){  ?>
                                <option <?php echo ($course['id']==$course_id)?$selected:'' ?>  value="<?php echo $course['id']; ?>"><?php echo $course['cb_code'].' - '.$course['cb_title']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="grade-filters">
                    <?php if(!isset($role_manager['institute_id'])){ ?> 
                        <div class="choose-inst">
                            <select id="institute_select" class="no-select-style" onchange="getBatches()">
                                <option value="">Choose Institute</option>
                                <?php foreach($institutes as $institute): ?>
                                <option <?php echo ($institute['id']==$institute_id)?$selected:'' ?> value="<?php echo $institute['id']; ?>"><?php echo $institute['ib_institute_code'] . ' - ' . $institute['ib_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php } else { ?>
                    <input type="hidden" id="institute_select" value="<?php echo $role_manager['institute_id']; ?>">
                    <?php } ?>
                        <?php 
                        $half_width ='';
                        if(isset($role_manager['institute_id'])): 
                        $half_width = 'width-auto';
                        ?> 
                         <div class="half-width">
                        <?php endif;?>
                        <div class="choose-batch <?php echo $half_width; ?>">
                            <select name="" class="no-select-style" id="batch_select" onchange="updateUrlState()">
                                <option value="">Choose Batch</option>
                                <?php if(isset($batches)): ?>
                                <?php foreach($batches as $batch): ?>
                                    <option <?php echo ($batch['id']==$batch_id)?$selected:'' ?> value="<?php echo $batch['id'] ?>" ><?php echo $batch['batch_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <?php if(isset($role_manager['institute_id'])): ?> 
                        </div>
                        <div class="half-width">
                        <?php endif;?>
                        <div class="filter-by <?php echo $half_width; ?>">
                            <select name="" class="no-select-style" id="filter_select" onchange="updateUrlState()">
                                <option value="">Filter By</option>
                                <?php $filters = array('all' => 'All', 'completed' => 'Completed', 'not-started' => 'Not yet started') ?>
                                <?php if (!empty($filters)): ?>
                                    <?php foreach ($filters as $key => $filter): ?>
                                       <option <?php echo ($key==$filter_by)?$selected:'' ?> value="<?php echo $key; ?>"><?php echo $filter ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?php if(!empty($grades)):?>
                                <?php foreach($grades as $grade):?>
                                    <option <?php echo ($filter_by==$grade['gr_name'])?$selected:'' ?> value="<?php echo $grade['gr_name'] ?>"><?php echo 'Grade '. $grade['gr_name'] ?></option>
                                <?php endforeach;?>
                                <?php endif;?>
                            </select>
                        </div>
                        <?php if(isset($role_manager['institute_id'])): ?> 
                        </div>
                        <?php endif;?>
                    </div>
                    <div class="pull-right filter-btn">
                        <button class="btn btn-green selected" onclick="exportGradeReport()">EXPORT REPORT</button>
                        <button class="btn btn-green selected" onclick="getGradeReport()">VIEW REPORT</button>
                    </div>
                </div>
            </div>
            <!-- Generate student grade report ends -->
    </div>
</div>

<script type="text/javascript">
function getBatches(){
    updateUrlState();
    var instituteId                = $("#institute_select").val();
    $.ajax({
            url: webConfigs('admin_url')+'report/get_batches',
            type: "POST",
            data:{ "is_ajax":true,"institute_id":instituteId},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    $('#batch_select').html('');
                    if (data['batches'].length > 0) {
                        var batchesHtml      = '<option value="">Choose Batch</option>';
                        for (var i in data['batches']) {
                            batchesHtml += '<option value="'+data['batches'][i]['id']+'">'+data['batches'][i]['batch_name']+'</option>';
                        }
                    } 
                    else 
                    {
                        var batchesHtml      = '<option value="">Choose Batch</option>';
                    } 

                     $('#batch_select').html(batchesHtml);
                }
            }
        });
}

function exportGradeReport() {
    var course_id               = $("#course-report-filter").val();
    var institute_id            = $("#institute_select").val();
    var batch_id                = $("#batch_select").val();
    var filter_by               = $("#filter_select").val();
    if(course_id=="0"){
        var messageObject = {
            'body':'Please choose course',
            'button_yes':'OK', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    } 
    var param = {
        "course_id" : course_id,
        "institute" : institute_id,
        "batch"     : batch_id,
        "filter"    : btoa(filter_by)
    };
    param = JSON.stringify(param);
    // //console.log(param);
    var pathname                = '/admin/report/export_grade_report';
    var link = window.location.protocol + "//" + window.location.host + pathname;
    window.location = link + '/' + btoa(param);
}

function getGradeReport(){
    var pathname                = '/admin/report/grade_report';
    var link = window.location.protocol + "//" + window.location.host + pathname;
    var course_id               = $("#course-report-filter").val();
    var institute_id            = $("#institute_select").val();
    var batch_id                = $("#batch_select").val();
    var filter_by               = $("#filter_select").val();
    if(course_id=="0"){
        var messageObject = {
            'body':'Please choose course',
            'button_yes':'OK', 
            'button_no':'CANCEL'
        };
        callback_warning_modal(messageObject);
        return false;
    } 
    
    if(course_id != '' || institute_id != '' || batch_id != '' || filter_by != '') {
        link += '?';
    }

    if(course_id != '') {
        link += '&course='+course_id;
    }

    if (institute_id != '') {
        link += '&institute_id='+institute_id;
    }

    if (batch_id != '') {
        link += '&batch_id='+batch_id;
    }

    if (filter_by != '') {
        link += '&filter='+btoa(filter_by);
    }
    window.location = link;
}

function updateUrlState() {
    if (history.pushState) {
        var link = window.location.protocol + "//" + window.location.host + window.location.pathname;
            link = link.substring(0, link.indexOf('?'));
        var courseId = $.trim($('#course-report-filter').val());
            courseId = (courseId!='')?courseId:0;
            link += '?course_id=' + courseId;
            var quizId = $.trim($('#assignment_filter').val());
                if(quizId != '') {
                    link += '&quiz_id=' + quizId;
                }
        var instituteId = $.trim($('#institute_select').val());
            if(instituteId != '') {
                link += '&institute_id=' + instituteId;
                
                var batchId = $.trim($('#batch_select').val());
                    if(batchId != '') {
                        link += '&batch_id=' + batchId;
                    }
            }
        var filterBy = $.trim($('#filter_select').val());
            if(filterBy != '') {
                filterBy = btoa(filterBy);
                link += '&filter_by=' + filterBy;
            }
            window.history.pushState({
                path: link
            }, '', link);
    }
}
</script>
<?php include_once 'footer.php'; ?>

