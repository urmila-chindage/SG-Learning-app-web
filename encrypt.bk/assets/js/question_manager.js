var __ajaxInProgress = 0;
var __categoryId = 0;
var __subjectId = 0;
var __topicId = 0;
var __categoriesRecieved = new Array();
var __fromId        = 0;
var __toId          = 0;
var __migrate_from  = '';
var __migrate_to    = '';
var __status_label  = '';

function editCategory(categoryID, status_label=false)
{
    __status_label = status_label;
    //console.log(__status_label, status_label);
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_category',
        type: "POST",
        data:{ "is_ajax":true, 'id':categoryID},
        success: function(response) {
            
            var data  = JSON.parse(response);
            //console.log(data.category.ct_name);
            if(data.error == false)
            {
                //console.log('hcvbcv bn');
                __categoryId = categoryID;
                $('#category_name').val('');
                if(Number(__categoryId) > 0)
                {
                    var category_data = data.category;
                    
                    //console.log( category_data.ct_name);
                    $('#category_name').val(category_data.ct_name);
                }
                $('#category_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data.message);
            }
        }
    });
}

function saveCategory()
{
    //console.log('saveCategory 1');
    //console.log(__status_label, 'saveCategory');
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category_name         = $('#category_name').val().trim();
    var errorCount            = 0;
    var errorMessage          = '';

    if (category_name == '')
    {
        errorMessage += 'Enter category name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#category_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_category',
            type: "POST",
            data: {"is_ajax": true, 'cat_name': category_name, 'cat_id': __categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);

                var actionClass = __status_label == 'private' ? 'warning' : 'success';
                
                if(data.error==false)
                {
                    $('#save_category_btn').html('SAVING...<ripples></ripples>');
                    var category_data = data.category;
                    var renderCategoryHtml = '';
                    
                    if(data.exist == '1')
                    {
                        renderCategoryHtml += ' <div class="drager"><div class="drager-icon">............</div></div><div class="lecture-hold question-category-lecturehold">';
                        renderCategoryHtml += '         <span data-toggle="tooltip" data-placement="top" data-original-title="'+((category_data.ct_name.length > 25)?(category_data.ct_name):'')+'" class="lecture-name question-category-lecturename catagory">'+((category_data.ct_name.length > 25)?(category_data.ct_name.substr(0, 25)+'...'):category_data.ct_name)+'<label class="pull-right label label-'+actionClass+'" style="margin-top: 10px;" id="action_class_'+__categoryId+'">'+lang(__status_label)+'</label></span>';
                        if((__cat_permission.indexOf("3")!=-1) || (__cat_permission.indexOf("4")!=-1)){
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__cat_permission.indexOf("3")!=-1){
                        renderCategoryHtml += `             <li id="status_btn_${category_data.id}">`;
                                                                var ct_status       = __status_label == 'public' ? 'make_private' : 'make_public';
                                                                var ct_status_label = __status_label;//category_data.ct_status == '1' ? 'public' : 'private';
                        renderCategoryHtml +=`                  <a href="javascript:void(0);" onclick="changeCategoryStatus('${category_data.id}', '${ct_status}','${addslashes(category_data.ct_name)}' )" > ${lang(ct_status)}</a>
                                                            </li>`;
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editCategory(\''+category_data.id+'\', \''+ct_status_label+'\')">Edit</a></li>';
                        }
                        if(__cat_permission.indexOf("2")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory(\''+btoa(unescape(encodeURIComponent(category_data.ct_name)))+'\', \''+category_data.id+'\')">Migrate</a></li>';
                        }
                        if(__cat_permission.indexOf("4")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteCategory(\''+btoa(unescape(encodeURIComponent(category_data.ct_name)))+'\',\''+category_data.id+'\')">Delete</a></li>';
                        }
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        }
                        renderCategoryHtml += ' </div>';
                        $('#category_'+category_data.id).html(renderCategoryHtml);
                    }
                    $('#category_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip(); 
                }
                else
                {
                    $('#category_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                $('#save_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveQusetionSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var subject_action              = $('#subject_action').val().trim();
    var category_name               = $('#cat_'+__sel_category).text();
    var category                    = __sel_category;   
    var subject_name                = $('#qusetion_subject').val().trim();
    var editQuestionSubjectSubId    = $('#editQuestionSubjectSubId').val().trim();
    var errorCount                  = 0;
    var errorMessage                = '';
    if(subject_action==0){
        if (category == 0)
        {
            errorMessage += 'Please add course category<br />';
            errorCount++;
        } 
    }
    if (subject_name == '')
    {
        errorMessage += 'Enter subject name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#subject_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_question_subject',
            //url: admin_url + 'question_manager/save_bulk_subject',
            type: "POST",
            //data: {"is_ajax": true,'category':category, 'subject_name':subject_name, 'subject_id':__subjectId,'category_name':category_name},
            data: {
                "is_ajax": true,
                'category':category, 
                'subject_name':subject_name, 
                'subject_id':__subjectId,
                'category_name':category_name,
                'course_category': category,
                'sub_names': subject_name,
                'editQuestionSubjectSubId' : editQuestionSubjectSubId
            },
            success: function (response) {
                //console.log(response); return;
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error == false)
                {
                    $('#save_que_subject_btn').html('SAVING...<ripples></ripples>');
                    var subject_data = data.ques_subject;
                    var renderSubjectHtml = '';
                    $(".subject-data").html('');
                    if(data.exist == '1')
                    {
                        //$('#subject_'+subject_data.id+' .lecture-name').html(subject_data.qs_subject_name);
                        renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                        renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"   id="sub_'+__sel_subject+'"   data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data.qs_subject_name.length > 35)?(subject_data.qs_subject_name):'')+'">'+((subject_data.qs_subject_name.length > 35)?(subject_data.qs_subject_name.substr(0, 32)+'...'):subject_data.qs_subject_name)+'</span>';
                        if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                        renderSubjectHtml += '     <div class="btn-group lecture-control">';
                        renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderSubjectHtml += '             <span class="label-text">';
                        renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderSubjectHtml += '             </span>';
                        renderSubjectHtml += '             <span class="tilder"></span>';
                        renderSubjectHtml += '         </span>';
                        renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__qus_permission.indexOf("3")!=-1){
                        
                        renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data.id+'\')">Edit</a></li>';
                        }
                        if(__qus_permission.indexOf("4")!=-1){
                        renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data.qs_subject_name)))+'\',\''+subject_data.id+'\')">Delete</a></li>';
                        }
                        renderSubjectHtml += '         </ul>';
                        renderSubjectHtml += '     </div>';
                        }
                        renderSubjectHtml += ' </div>';
                        $('#subject_'+subject_data.id).html(renderSubjectHtml);
                    }
                    $('#subject_manage').modal('hide');
                    __categoryId = 0;
                    $('[data-toggle="tooltip"]').tooltip();
                }
                else
                {
                    $('#subject_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                $('#save_que_subject_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveBulkCategory()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category_names        = $('#bulk_categories').val().trim();
    var errorCount            = 0;
    var errorMessage          = '';

    if (category_name == '')
    {
        errorMessage += 'Enter Categories Name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_category_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_category',
            type: "POST",
            data: {"is_ajax": true, 'cat_names': category_names},
            success: function (response) {
                __ajaxInProgress = 0;
                //console.log(response, 'saveBulkCategory');
                var data = $.parseJSON(response);
                
                if(data.error == false)
                {
                    $('#save_category_btn').html('SAVING...<ripples></ripples>');
                    category_data      = [];
                    for (i = 0; i < data.category.length; i++) { 
                        var renderCategoryHtml = '';
                        var category_data  = data.category[i];
                        renderCategoryHtml += '<li class="dragging ui-sortable-handle" id="category_'+category_data.id+'" onclick="populateSubjects('+category_data.id+')">';
                        renderCategoryHtml += ' <div class="drager"><div class="drager-icon">............</div></div><div class="lecture-hold question-category-lecturehold">';
                        renderCategoryHtml += '         <span class="lecture-name question-category-lecturename catagory" id="cat_'+category_data.id+'" onclick="populateSubjects('+category_data.id+')"  data-toggle="tooltip" data-placement="top" data-original-title="'+((category_data.ct_name.length > 35)?(category_data.ct_name):'')+'">'+((category_data.ct_name.length > 25)?(category_data.ct_name.substr(0, 22)+'...'):category_data.ct_name)+'<label class="pull-right label label-success" style="margin-top: 10px;" id="action_class_'+__categoryId+'">'+lang('public')+'</label></span>';
                        if((__cat_permission.indexOf("3")!=-1) || (__cat_permission.indexOf("4")!=-1)){
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger"  data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__cat_permission.indexOf("3")!=-1){
                        renderCategoryHtml += `             <li id="status_btn_${category_data.id}">`;
                                                                var ct_status       = category_data.ct_status == '1' ? 'make_private' : 'make_public';
                                                                var ct_status_label = category_data.ct_status == '1' ? 'public' : 'private';
                        renderCategoryHtml +=` 
                                                                <a href="javascript:void(0);" onclick="changeCategoryStatus('${category_data.id}', '${ct_status}','${addslashes(category_data.ct_name)}' )" > ${lang(ct_status)}</a>
                                                            </li>`;
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editCategory(\''+category_data.id+'\',  \''+ct_status_label+'\')">Edit</a></li>';
                        }
                        if(__cat_permission.indexOf("2")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory(\''+btoa(unescape(encodeURIComponent(category_data.ct_name)))+'\', \''+category_data.id+'\')">Migrate</a></li>';
                        }
                        if(__cat_permission.indexOf("4")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteCategory(\''+btoa(unescape(encodeURIComponent(category_data.ct_name)))+'\',\''+category_data.id+'\')">Delete</a></li>';
                        }
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        }
                        renderCategoryHtml += ' </div>';
                        renderCategoryHtml += '</li>';
                        category_data      = [];
                        $('#category_manage_wrapper').prepend(renderCategoryHtml);
                    }
                    $('#category_manage_wrapper .subject-data').html('');
                    $('#bulk_categories').val('');
                    $('#bulk_category_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip();
                    $("#category_manage_wrapper ul li:first").click(); 
                    var sel_id = $('ul#category_manage_wrapper li:first').attr('id');
                        sel_id = sel_id.substring(sel_id.lastIndexOf("_"));
                    $("#cat"+sel_id).trigger('click');
                }
                else
                {
                    $('#bulk_category_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveBulkSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    //var course_category        = $('#question_course_category').val();
    var course_category        = __sel_category; 
    var bulk_subject           = $('#bulk_subject').val().trim();
    var errorCount             = 0;
    var errorMessage           = '';
    var category_name          = $('#cat_'+__sel_category).text().trim();

    if (course_category == '')
    {
        errorMessage += 'Please select the course category<br />';
        errorCount++;
    }

    if (bulk_subject == '')
    {
        errorMessage += 'Please enter the subject names<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_subject_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_subject',
            type: "POST",
            data: {
                "is_ajax": true, 
                'course_category': course_category,
                'sub_names': bulk_subject,
                'category_name':category_name
            },
            success: function (response) {
                $(".subject-data").html('');
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                if(data.error==false)
                {
                    $('#save_subject_btn').html('SAVING...<ripples></ripples>');
                    if(__sel_category==course_category){
                        var subject_data      = '';
                        for (i = 0; i < data.subject.length; i++) { 
                            var renderSubjectHtml = '';
                            subject_data         = data.subject[i];
                            renderSubjectHtml += '<li id="subject_'+subject_data.id+'"  onclick="populateTopics('+__sel_category+','+subject_data.id+')">';
                            renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory" id="sub_'+subject_data.id+'"   data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data.qs_subject_name.length > 35)?subject_data.qs_subject_name:'')+'">'+((subject_data.qs_subject_name.length > 35)?(subject_data.qs_subject_name.substr(0, 32)+'...'):subject_data.qs_subject_name)+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderSubjectHtml += '     <div class="btn-group lecture-control">';
                            renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderSubjectHtml += '             <span class="label-text">';
                            renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderSubjectHtml += '             </span>';
                            renderSubjectHtml += '             <span class="tilder"></span>';
                            renderSubjectHtml += '         </span>';
                            renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data.id+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data.qs_subject_name)))+'\',\''+subject_data.id+'\')">Delete</a></li>';
                            }
                            renderSubjectHtml += '         </ul>';
                            renderSubjectHtml += '     </div>';
                            }
                            renderSubjectHtml += ' </div>';
                            renderSubjectHtml += '</li>';
                            subject_data   = '';
                            $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                            if(i==(data.subject.length-1)){
                                __sel_subject       = data.subject[i].id;
                            }
                        }
                        $("#question_subject_manage_wrapper>li.select").removeClass("select");
                        $("#subject_"+__sel_subject).addClass("select");
                        $('#bulk_subject').val('');
                        $('#bulk_subject_manage').modal('hide');
                        $('[data-toggle="tooltip"]').tooltip();
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                        populateTopics(__sel_category,__sel_subject);
                    }
                   
                }
                else
                {
                    $('#bulk_subject_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_subject_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function populateSubjects(categoryId)
{   
    if(__qus_permission.indexOf("1")!=-1){
        __ajaxInProgress = 1;
        __sel_category   = categoryId;
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#question_topic_manage_wrapper").html("");
                    $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>No topics available.</p>");
                    $("#question_subject_manage_wrapper").html(" ");
                       
                        var renderSubjectHtml = '';
                        var subject_data      = [];
                        if(data.subject.length!=0){
                            for (i = 0; i < data.subject.length; i++) { 
                            subject_data       = data.subject[i];
                            renderSubjectHtml += '<li id="subject_'+subject_data.id+'" onclick="populateTopics('+__sel_category+','+subject_data.id+')">';
                            renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"  id="sub_'+subject_data.id+'"    data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data.qs_subject_name.length > 35)?subject_data.qs_subject_name:'')+'">'+((subject_data.qs_subject_name.length > 35)?(subject_data.qs_subject_name.substr(0, 32)+'...'):subject_data.qs_subject_name)+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderSubjectHtml += '     <div class="btn-group lecture-control">';
                            renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderSubjectHtml += '             <span class="label-text">';
                            renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderSubjectHtml += '             </span>';
                            renderSubjectHtml += '             <span class="tilder"></span>';
                            renderSubjectHtml += '         </span>';
                            renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data.id+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data.qs_subject_name)))+'\',\''+subject_data.id+'\')">Delete</a></li>';
                            }
                            renderSubjectHtml += '         </ul>';
                            renderSubjectHtml += '     </div>';
                            }
                            renderSubjectHtml += ' </div>';
                            renderSubjectHtml += '</li>';
                         }
                        __sel_subject = data.subject[0].id;
                        $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                        $("#subject_"+__sel_subject).addClass("select");
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                        populateTopics(__sel_category,__sel_subject);
                        } 
                        else
                        {
                            $("#question_subject_manage_wrapper").html("<p class='subject-data' style='margin-top:10em;text-align: center;'>No subjects available.</p>");
                            __sel_subject =0;
                        }
                    $('[data-toggle="tooltip"]').tooltip(); 
                }
            }
        });
    } else {
        return false;
    }

}

function generateSubjects()
{
    $('.alert-danger').remove();      
    $('#merge_topic_name').val('');
    $('#merge_subject_name').val('');
        var categoryId   = $('#questions_course_category').val().trim();
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#questions_course_subject").html(" ");
                       // subject_data      = [];
                    if(data.subject.length!=0){
                        for (i = 0; i < data.subject.length; i++) { 
                        var renderSubjectHtml = '';
                        var subject_data   = data.subject[i];
                        renderSubjectHtml += '<option value="'+subject_data.id+'">'+subject_data.qs_subject_name+'</option>';
                        $('#questions_course_subject').prepend(renderSubjectHtml);
                        
                       }
                    } 
                }
            }
        });

}

function populateTopics(categoryId,subjectId)
{   
    if(__qus_permission.indexOf("1")!=-1){
        __sel_subject    = subjectId;
        __ajaxInProgress = 1;
        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading...</p>");
        $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId, 'question_subject': subjectId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                       // subject_data      = [];
                    if(data.topic.length!=0){
                        var renderTopicHtml = '';
                        for (i = 0; i < data.topic.length; i++) { 
                            var topic_data   = data.topic[i];
                            renderTopicHtml += '<li id="topic_'+topic_data.id+'">';
                            renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data.qt_topic_name.length > 35)?topic_data.qt_topic_name:'')+'">'+((topic_data.qt_topic_name.length > 35)?(topic_data.qt_topic_name.substr(0, 32)+'...'):topic_data.qt_topic_name)+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderTopicHtml += '     <div class="btn-group lecture-control">';
                            renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderTopicHtml += '             <span class="label-text">';
                            renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderTopicHtml += '             </span>';
                            renderTopicHtml += '             <span class="tilder"></span>';
                            renderTopicHtml += '         </span>';
                            renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data.id+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data.qt_topic_name)))+'\',\''+topic_data.id+'\')">Delete</a></li>';
                            }
                            renderTopicHtml += '         </ul>';
                            renderTopicHtml += '     </div>';
                            }
                            renderTopicHtml += ' </div>';
                            renderTopicHtml += '</li>';
                        }
                        $("#question_topic_manage_wrapper").html(" ");
                        $('#question_topic_manage_wrapper').prepend(renderTopicHtml);
                        $('[data-toggle="tooltip"]').tooltip();
                    } else {
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>No topics available.</p>");
                     }

                }
            }
        });
    } else {
        return false;
    }
}

function deleteCategory(category_name, category_id)
{ 
    var category_name = atob(category_name);
    $.ajax({
        url: admin_url + 'question_manager/check_category_connection',
        type: "POST",
        data: {"is_ajax": true, 'cat_name': category_name, 'cat_id': category_id},
        success: function (response) {
            var data = $.parseJSON(response);
            //console.log(data);
            if(data.error == false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the category named "'+category_name+'" ?',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'cat_id':category_id,'category_name':category_name, 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteCategoryConfirmed);
            }else
            {
                var errors      = JSON.parse(response);
                var courseList  = errors.courses;
                var questions   = errors.questions;
                var subjects    = errors.subjects;
                var message     = '';
                var plural      = 'is';
                var links       = '';
                var sl          = 0;
                //console.log(errors); //check_category_connection
            
                if(courseList > 0){
                    
                    message += 'Courses';
                    
                    sl++;
                    links +='<h5>'+sl+'. Click <a target="_blank" href="'+admin_url+'course/?&filter=all&category='+category_id+'">here</a> to see the assigned courses.</h5>';
                    
                }
    
                if(questions > 0){

                    var questions = subjects > 0 && courseList > 0 ? ', Questions and' : (subjects > 0 || courseList > 0 || questions > 0 ? ' Questions and' : ' and Questions');
                    
                    message += questions;
                    
                    sl++;
                    links +='<h5>'+sl+'. Click <a target="_blank" href="'+admin_url+'generate_test/?&filter=all&type=all&category='+category_id+'&subject=all&topic=all&offset=1">here</a> to see the assigned questions.</h5>';
                }
    
                if(subjects > 0){
                    
                    message += ' Subjects';

                    sl++;
                    links +='<h5>'+sl+'. Migrate the assigned subjects</h5>';
                    
                }
                
                var header_text = '<h5><b>Some '+message+' are linked to this category. You have to unassign/migrate them before deleting.</b></h5>'+links;
                var labelButton = data.status == '0' ? 'OK' : `<span href="javascript:void(0);" onclick="changeCategoryStatus('${category_id}', 'make_private','${category_name}','1' )">MAKE PRIVATE, INSTEAD</span>`;
                //console.log(data.status);
                 var messageObject = {
                'body':header_text,
                'button_yes':labelButton, 
            };
            callback_warning_modal(messageObject);
            }
        }
    });
}

function deleteCategoryConfirmed(param)
{
    var catgory_id    = param.data.cat_id;
    var category_name = param.data.category_name;
    $.ajax({
        url: admin_url+'question_manager/delete_category',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':catgory_id,'category_name':category_name},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#category_'+catgory_id).remove();
            if($("#category_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" style="margin-top:10em;text-align:center;">No categories available.</p>';
                $("#category_manage_wrapper").prepend(htmlElement);
                __sel_category =0;
                __sel_subject  =0;
            } else {
                $("#category_manage_wrapper ul li:first").click();
            }
            var messageObject = {
                'body':data.message,
                'button_yes':'OK', 
            };
            callback_success_modal(messageObject);
        }
    });
}
function generateSubjectList()
{
        $('.alert-danger').remove();      
        $('#merge_topic_name').val(''); 
        $('#merge_subject_name').val('');
        var categoryId   = $('#topic_bulk_category').val().trim();
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#topic_bulk_subject").html(" ");
                       // subject_data      = [];
                    if(data.subject.length!=0){
                        for (i = 0; i < data.subject.length; i++) { 
                        var renderSubjectHtml = '';
                        var subject_data   = data.subject[i];
                        renderSubjectHtml += '<option value="'+subject_data.id+'">'+subject_data.qs_subject_name+'</option>';
                        $('#topic_bulk_subject').prepend(renderSubjectHtml);
                        
                       }
                    } 
                }
            }
        });

}

function generateCategoryList()
{
        __ajaxInProgress = 1;
        $.ajax({
            url: admin_url + 'question_manager/get_category',
            type: "POST",
            data: {"is_ajax": true},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#question_parent_category").html(" ");
                    $("#questions_course_category").html(" ");
                    
                       // subject_data      = [];
                       var renderCategoryHtml = '';
                    if(data.filter_category.length!=0){
                        var renderCategoryHtml = '<option>Choose course category</option>';
                        for (i = 0; i < data.filter_category.length; i++) { 
                        var category_data      = data.filter_category[i];
                        renderCategoryHtml += '<option value="'+category_data.id+'">'+category_data.ct_name+'</option>';
                       }
                        $('#question_parent_category').prepend(renderCategoryHtml);
                        $('#questions_course_category').prepend(renderCategoryHtml);
                    } 
                }
            }
        });

}
function migrateCategory(category_name, category_id)
{
    $('#category_select_migrate').val('');
    $.ajax({
        url: admin_url+'question_manager/get_category',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':category_id },
        success: function(response) {
            var data  = $.parseJSON(response);
            var renderCatListing = '';
            var renderCatListingto = '';
            __categoriesRecieved = data.filter_category;
            
            renderCatListing += '<option value="0">Choose Category</option>';
            renderCatListing += renderCategoriesLi(__categoriesRecieved,category_id,1);
            $('#category_selected_migrate').html(renderCatListing);
            
            renderCatListingto += '<option value="0">Choose Category</option>';
            renderCatListingto += renderCategoriesLi(__categoriesRecieved,category_id,2);
            $('#category_select_migrate').html(renderCatListingto);
        }
    });
    __fromId         = category_id;
    __migrate_from   = category_name;

    $('#save_migrate_category_btn').unbind();
    $('#save_migrate_category_btn').click({"cat_id": category_id, 'cat_name':category_name, 'is_ajax':'true'}, migrateCategoryConfirmed);
}

function renderCategoriesLi(categories,selected,type){
    var cHtml = '';

    switch(type){
        case 1:
            $.each(categories,function(c_key,category){
                if(selected != 0 && category.id == selected){
                    cHtml += '<option value="'+category.id+'" selected >'+category.ct_name+'</option>';
                }else{
                    cHtml += '<option value="'+category.id+'">'+category.ct_name+'</option> ';
                }
            });
        break;

        case 2:
            $.each(categories,function(c_key,category){
                if(selected != 0 && category.id != selected){
                    cHtml += '<option value="'+category.id+'">'+category.ct_name+'</option>';
                }
            });
        break;
    }

    return cHtml;
}

$(document).on('change', '#category_selected_migrate', function() {
    var renderCatListing = '';
    __fromId             = this.value;
    __migrate_from       = btoa(unescape(encodeURIComponent($("#category_selected_migrate").find(":selected").text())));
    __toId               = 0;
    __migrate_to         = '';
    renderCatListing += '<option value="0">Choose Category</option>';
    renderCatListing += renderCategoriesLi(__categoriesRecieved,this.value,2);
    $('#category_select_migrate').html(renderCatListing);
})

$(document).on('change', '#category_select_migrate', function() {
    __toId          = this.value;
    __migrate_to    = btoa(unescape(encodeURIComponent($("#category_select_migrate").find(":selected").text())));
})

function migrateCategoryConfirmed(param)
{
    var migrate_category_id = __toId;
    var errorCount            = 0;
    var errorMessage          = '';
    //alert(migrate_category_id); //return;
    if (migrate_category_id == 0)
    {
        errorMessage += 'Please select category.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#category_migrate .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
    else{
        $.ajax({
            url: admin_url+'question_manager/migrate_category',
            type: "POST",
            data:{ "is_ajax":true, 'cat_id':migrate_category_id, 'previous_cat_id':__fromId,'from_category':atob(__migrate_from),'to_category':atob(__migrate_to)},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#category_migrate').modal('hide');
                $('#category_manage_wrapper').prepend(renderPopUpMessage('success', data.message));
                $("#category_manage_wrapper ul li:first").click();
                __toId = 0;
                var sel_id = $('ul#category_manage_wrapper li:first').attr('id');
                    sel_id = sel_id.substring(sel_id.lastIndexOf("_"));
                    $("#cat_"+sel_id).trigger('click'); 
            }
        });
    }
}

function editQuestionSubject(subjectID)
{
    
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_question_subject',
        type: "POST",
        async: false,
        data:{ "is_ajax":true, 'id':subjectID},
        success: function(response) {
            generateCategoryList();
            var data  = $.parseJSON(response);
            console.log(data);
            if(data.error == false)
            {
                __subjectId = subjectID;
                $('#qusetion_subject').val('');
                if(__subjectId > 0)
                {
                    $('#subject_category_selection').remove();
                    $('#subject_action').val('1');
                    var subject_data = data.subject;
                    $('#qusetion_subject').val(subject_data.qs_subject_name);
                    $('#editQuestionSubjectSubId').val(subject_data.id);
                } else {
                    //alert(__sel_category);
                    $('#question_parent_category').val(__sel_category);
                }
                $('#subject_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data.message);
            }
        }
    });
}

function deleteSubject(subject_name, subject_id)
{
    $.ajax({
        url: admin_url + 'question_manager/check_subject_connection',
        type: "POST",
        data: {"is_ajax": true, 'subject_name': atob(subject_name), 'subject_id': subject_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data.error==false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the subject named "'+atob(subject_name)+'" ?',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'subject_id':subject_id,'subject_name': atob(subject_name), 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteSubjectConfirmed);
           }else
            {
                lauch_common_message('Error in deleting categories', data.message);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteSubjectConfirmed(param)
{
    var category_name               = $('#cat_'+__sel_category).text();
    $.ajax({
        url: admin_url+'question_manager/delete_question_subject',
        type: "POST",
        data:{ "is_ajax":true, 'subject_id':param.data.subject_id,'subject_name':param.data.subject_name,'category_name':category_name},
        success: function(response) {
            var data            = $.parseJSON(response);
            $('#subject_'+data.subject_id).remove();
            if($("#question_subject_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" style="margin-top:10em;text-align:center;">No subjects available.</p>';
                $("#question_subject_manage_wrapper").prepend(htmlElement);
                __sel_subject=0;
            }
            else
            {
                $("#question_subject_manage_wrapper ul li:first").click();
                var sel_id    = $('ul#question_subject_manage_wrapper li:first').attr('id');
                    sel_id    = sel_id.substring(sel_id.lastIndexOf("_"));
                $("#sub"+sel_id).trigger('click');
            }
            var messageObject = {
                'body':data.message,
                'button_yes':'OK', 
            };
        callback_success_modal(messageObject);  
         }
        
    });
}

function editQuestionTopic(topicID)
{
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'id':topicID},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data.error == false)
            {
                __topicId = topicID;
                $('#question_topic').val('');
                if(__topicId > 0)
                {
                    var topic_data = data.topic;
                    $('#question_topic').val(topic_data.qt_topic_name);
                    $('#topic-action-selection').remove();
                    $('#topic_action').val('1');
                }
                //generateCategoryList();
                $('#topic_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data.message);
            }
        }
    });
}

function saveQusetionTopic()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category                    = __sel_category;
    var subject                     = __sel_subject;
    var question_topic              = $('#question_topic').val().trim();
    var errorCount                  = 0;
    var errorMessage                = '';
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    
    if (category == '')
    {
        errorMessage += 'Please select course category<br />';
        errorCount++;
    }
    if (subject == 0)
    {
        errorMessage += 'Please add question subject<br />';
        errorCount++;
    }

    if (question_topic == '')
    {
        errorMessage += 'Enter topic name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#topic_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_question_topic',
            type: "POST",
            data: {"is_ajax": true,'category':category, 'question_subject':subject,'topic_name':question_topic,'topic_id':__topicId,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $('#save_que_topic_btn').html('SAVING...<ripples></ripples>');
                    var topic_data = data.ques_topic;
                    var renderTopicHtml = '';
                    $("#topic-data").html('');
                    if(data.exist == '1')
                    {
                     
                        renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                        renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data.qt_topic_name.length > 35)?(topic_data.qt_topic_name):'')+'">'+((topic_data.qt_topic_name.length > 35)?(topic_data.qt_topic_name.substr(0, 32)+'...'):topic_data.qt_topic_name)+'</span>';
                        if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                        renderTopicHtml += '     <div class="btn-group lecture-control">';
                        renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderTopicHtml += '             <span class="label-text">';
                        renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderTopicHtml += '             </span>';
                        renderTopicHtml += '             <span class="tilder"></span>';
                        renderTopicHtml += '         </span>';
                        renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__qus_permission.indexOf("3")!=-1){
                        renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data.id+'\')">Edit</a></li>';
                        }
                        if(__qus_permission.indexOf("4")!=-1){
                        renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data.qt_topic_name)))+'\',\''+topic_data.id+'\')">Delete</a></li>';
                        }
                        renderTopicHtml += '         </ul>';
                        renderTopicHtml += '     </div>';
                        }
                        renderTopicHtml += ' </div>';
                        $('#topic_'+topic_data.id).html(renderTopicHtml);
                    }
                    $('#topic_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip();
                    __categoryId = 0;
                    __subjectId  = 0;
                }
                else
                {
                    $('#topic_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                $('#save_que_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function deleteTopic(topic_name, topic_id)
{
    $.ajax({
        url: admin_url + 'question_manager/check_topic_connection',
        type: "POST",
        data: {"is_ajax": true, 'topic_name': atob(topic_name), 'topic_id': topic_id, 'category_id':__sel_category},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data.error==false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the topic named "'+atob(topic_name)+'" ?',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'topic_id':topic_id,'topic_name': atob(topic_name), 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteTopicConfirmed);
           }else
            {
                lauch_common_message('Error in deleting topics', data.message);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteTopicConfirmed(param)
{
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    $.ajax({
        url: admin_url+'question_manager/delete_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'topic_id':param.data.topic_id, 'topic_name':param.data.topic_name,'category_name':category_name,'subject_name':subject_name},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#topic_'+data.topic_id).remove();
            if($("#question_topic_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" id="topic-data" style="margin-top:10em;text-align:center;">No topics available.</p>';
                $("#question_topic_manage_wrapper").prepend(htmlElement);
            }else {
                $("#question_topic_manage_wrapper ul li:first").click();
            }
            var messageObject = {
                'body':data.message,
                'button_yes':'OK', 
            };
        callback_success_modal(messageObject);  }
    });
}

function saveBulkTopic()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var course_category           = __sel_category;
    var subject_id                = __sel_subject;
    var category_name             = $('#cat_'+__sel_category).text();
    var subject_name              = $('#sub_'+__sel_subject).text();
    
    var bulk_topics            = $('#bulk_topics').val().trim();
    var errorCount             = 0;
    var errorMessage           = '';

    if (course_category == '')
    {
        errorMessage += 'Please choose the course category<br />';
        errorCount++;
    }

    if (subject_id == 0)
    {
        errorMessage += 'Please choose the question subject<br />';
        errorCount++;
    }

    if (bulk_topics == '')
    {
        errorMessage += 'Please enter the topic names<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_topic',
            type: "POST",
            data: {"is_ajax": true, 'course_category': course_category,'subject_id': subject_id,'bulk_topics': bulk_topics,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $('#save_bulk_topic_btn').html('SAVING...<ripples></ripples>');
                    $("#topic-data").html('');
                    if((__sel_category==course_category)&&(__sel_subject==subject_id)){
                        var topic_data      = [];
                        var renderTopicHtml = '';
                        for (i = 0; i < data.topic.length; i++) { 
                            topic_data       = data.topic[i];
                            renderTopicHtml += '<li id="topic_'+topic_data.id+'">';
                            renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data.qt_topic_name.length > 35)?(topic_data.qt_topic_name):'')+'">'+((topic_data.qt_topic_name.length > 35)?(topic_data.qt_topic_name.substr(0, 32)+'...'):topic_data.qt_topic_name)+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderTopicHtml += '     <div class="btn-group lecture-control">';
                            renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderTopicHtml += '             <span class="label-text">';
                            renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderTopicHtml += '             </span>';
                            renderTopicHtml += '             <span class="tilder"></span>';
                            renderTopicHtml += '         </span>';
                            renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data.id+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data.qt_topic_name)))+'\',\''+topic_data.id+'\')">Delete</a></li>';
                            }
                            renderTopicHtml += '         </ul>';
                            renderTopicHtml += '     </div>';
                            }
                            renderTopicHtml += ' </div>';
                            renderTopicHtml += '</li>';
                            $('#question_topic_manage_wrapper').prepend(renderTopicHtml); 
                            topic_data       = '';
                            renderTopicHtml  = '';
                        }
                        
                        $("#question_topic_manage_wrapper ul li:first").click();
                    }
                    $('#bulk_topic_manage').modal('hide');
                    $('#bulk_topics').val('');
                    $('[data-toggle="tooltip"]').tooltip();
                }
                else
                {
                    $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_bulk_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveMergeSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var merge_subject_list       = [];
    var merged_subject_names     = [];
    
    $("input:checkbox[name=merge_subject_list]:checked").each(function(){
    merge_subject_list.push($(this).val().trim());
    merged_subject_names.push($(this).next('label').text());
    });
    var merge_subject_name     = $('#merge_subject_name').val().trim();
    var errorCount             = 0;
    var errorMessage           = '';
    var category_name               = $('#cat_'+__sel_category).text().trim();

    if(merge_subject_list.length < 2){
        errorMessage += 'Please choose more than one Subjects<br />';
        errorCount++;
    }

    if (merge_subject_list.length==0)
    {
        errorMessage += 'Please choose the question subject<br />';
        errorCount++;
    }

    if (merge_subject_name == '')
    {
        errorMessage += 'Please enter the subject name<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_subject_merge .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/merge_subject',
            type: "POST",
            data: {"is_ajax": true, 'merge_subjects': merge_subject_list,'merge_subject_name': merge_subject_name,'category_id':__sel_category,'merged_subject_names':merged_subject_names,'category_name':category_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    toastr["success"]('',"Subjects successfully merged");
                    $('#question_subject_manage_wrapper li.select').removeClass('select');
                    var renderSubjectHtml = '';
                    renderSubjectHtml += '<li id="subject_'+data.subject_id+'" class="select" onclick="populateTopics('+__sel_category+','+data.subject_id+')"  >';
                    renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                    renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"  id="sub_'+data.subject_id+'"    data-toggle="tooltip" data-placement="top" data-original-title="'+((merge_subject_name.length > 35)?merge_subject_name:'')+'">'+((merge_subject_name.length > 35)?(merge_subject_name.substr(0, 32)+'...'):merge_subject_name)+'</span>';
                    if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                    renderSubjectHtml += '     <div class="btn-group lecture-control">';
                    renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                    renderSubjectHtml += '             <span class="label-text">';
                    renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                    renderSubjectHtml += '             </span>';
                    renderSubjectHtml += '             <span class="tilder"></span>';
                    renderSubjectHtml += '         </span>';
                    renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                    if(__qus_permission.indexOf("3")!=-1){
                    renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+data.subject_id+'\')">Edit</a></li>';
                    }
                    if(__qus_permission.indexOf("4")!=-1){
                    renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(merge_subject_name)))+'\',\''+data.subject_id+'\')">Delete</a></li>';
                    }
                    renderSubjectHtml += '         </ul>';
                    renderSubjectHtml += '     </div>';
                    }
                    renderSubjectHtml += ' </div>';
                    renderSubjectHtml += '</li>';
                    $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                    data.merge_subject_ids.forEach(function(element) {
                      $('#subject_'+element).remove();
                    });
                    $('#bulk_subject_merge').modal('hide');
                    $('#merge_subject_name').val('');
                    $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                    $('[data-toggle="tooltip"]').tooltip();
                    populateTopics(__sel_category,data.subject_id);
                    
                }
                else
                {
                    $('#subjectMergeError').html(`<div class="alert alert-danger alert-dismissible">
                          <a href="#" onclick="$(this).parent('div').remove();" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                          ${data.message}
                          </div>`);
                    $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_bulk_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveMergeTopic()
{
    var merge_topic_list       = [];
    var merged_topic_names     = [];
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    
    $("input:checkbox[name=merge_topic_list]:checked").each(function(){
        merge_topic_list.push($(this).val().trim());
        merged_topic_names.push($(this).next('label').text().trim());
    });
    var merge_topic_name     = $('#merge_topic_name').val().trim();
    var errorCount             = 0;
    var errorMessage           = '';

    if (merge_topic_list.length==0)
    {
        errorMessage += 'Please choose the question topics<br />';
        errorCount++;
    }

    if(merge_topic_list.length < 2){
        errorMessage += 'Please choose more than one Topic<br />';
        errorCount++;
    }

    if (merge_topic_name == '')
    {
        errorMessage += 'Please enter the topic name<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_topic_merge .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        $.ajax({
            url: admin_url + 'question_manager/merge_topic',
            type: "POST",
            data: {"is_ajax": true, 'merge_topics': merge_topic_list,'merge_topic_name': merge_topic_name,'category_id':__sel_category,'subject_id':__sel_subject,'merged_topic_names':merged_topic_names,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    toastr["success"]('',"Topics successfully merged");
                    $('#question_topic_manage_wrapper li.select').removeClass('select');
                    //$("#question_subject_manage_wrapper").html('');
                    var renderTopicHtml = '';
                    renderTopicHtml += '<li id="topic_'+data.topic_id+'" class="select">';
                    renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                    renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+merge_topic_name+'">'+merge_topic_name+'</span>';
                    if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                    renderTopicHtml += '     <div class="btn-group lecture-control">';
                    renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                    renderTopicHtml += '             <span class="label-text">';
                    renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                    renderTopicHtml += '             </span>';
                    renderTopicHtml += '             <span class="tilder"></span>';
                    renderTopicHtml += '         </span>';
                    renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                    if(__qus_permission.indexOf("3")!=-1){
                    renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+data.topic_id+'\')">Edit</a></li>';
                    }
                    if(__qus_permission.indexOf("4")!=-1){
                    renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(merge_topic_name)))+'\',\''+data.topic_id+'\')">Delete</a></li>';
                    }
                    renderTopicHtml += '         </ul>';
                    renderTopicHtml += '     </div>';
                    }
                    renderTopicHtml += ' </div>';
                    renderTopicHtml += '</li>';
                    $('#question_topic_manage_wrapper').prepend(renderTopicHtml);
                    data.merge_topic_ids.forEach(function(element) {
                      $('#topic_'+element).remove();
                    });
                    $('#bulk_topic_merge').modal('hide');
                    $('#merge_topic_name').val('');
                    $('[data-toggle="tooltip"]').tooltip();
                    
                }
                else
                {
                    $('#bulk_topic_merge .modal-body').prepend(renderPopUpMessage('error', data.message));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_merge_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function generateSubjectList()
{
    $('.alert-danger').remove();      
    $('#merge_topic_name').val('');
    $('#merge_subject_name').val('');
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': __sel_category},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#merge_subjects").html(" ");
                       // subject_data      = [];
                       var renderSubjectHtml = '';
                       //renderSubjectHtml = '<option value="">Choose Course Subject</option>';
                    if(data.subject.length!=0){
                        for (i = 0; i < data.subject.length; i++) { 
                        var subject_data   = data.subject[i];
                         renderSubjectHtml += ' <div class="checkbox-wrap"  id="inst_course_'+i+'"><span class="chk-box"><input type="checkbox" name="merge_subject_list" class="inst-course" value="'+subject_data.id+'"><label class="font14">'+subject_data.qs_subject_name+'</label></span><span class="email-label pull-right"></span></div>';   
                        //renderSubjectHtml += '<option value="'+subject_data.id+'">'+subject_data.qs_subject_name+'</option>';
                       }
                       $('#merge_subjects').prepend(renderSubjectHtml);
                    } 
                }
            }
        });

}

function generateTopicList()
{
    $('.alert-danger').remove(); 
    $('#merge_topic_name').val('');
    $('#merge_subject_name').val('');
        $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': __sel_category, 'question_subject': __sel_subject},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data.error==false)
                {
                    $("#merge_topics").html(" ");
                    var renderTopicHtml = '';
                   // renderSubjectHtml = '<option value="">Choose Course Topic</option>';

                    if(data.topic.length!=0){
                        for (i = 0; i < data.topic.length; i++) { 
                        var topic_data    = data.topic[i];
                        renderTopicHtml += ' <div class="checkbox-wrap"  id="inst_course_'+i+'"><span class="chk-box"><input type="checkbox" name="merge_topic_list" class="inst-course" value="'+topic_data.id+'"><label class="font14">'+topic_data.qt_topic_name+'</label></span><span class="email-label pull-right"></span></div>';   
                        }
                        $('#merge_topics').prepend(renderTopicHtml);
                        
                    }
                }
            }
        });
}

function changeCategoryStatus(category_id, status, category_name, instead = '')
{
    var action      = status;
    var ct_status   = 0;
    var courses     = '';

    
    //$.get(admin_url+'question_manager/get_courses_by_category?id='+category_id, function(courseList){
    $.post(admin_url+'question_manager/check_category_connection?id='+category_id,{cat_id : category_id}, function(data){
        errors = JSON.parse(data);
        var courseList  = errors.courses;
        var questions   = errors.questions;
        var subjects    = errors.subjects;
        var message     = '';
        var links       = '';
        var sl          = 0;
        //console.table(errors);
            

        switch(status) {
            case "make_private":
                ok_button_text    = lang('make_private').toUpperCase();
                ct_status         = '0';
                
                // $.each( courseList, function( key, course ) {
                //     courses +='<h6>'+sl+'. <a>'+course.cb_code+' click here </a>to see the assigned courses</h6>';
                //     sl++;
                // });
                if(courseList > 0){
                    message += 'Courses';
                    sl++;
                    links +='<h5>'+sl+'. Click <a target="_blank" href="'+admin_url+'course/?&filter=all&category='+category_id+'">here</a> to see the assigned courses.</h5>';
                    
                }
    
                if(questions > 0){
                    var questions = subjects > 0 && courseList > 0 ? ', Questions' : (subjects > 0 || courseList > 0 ? ' Questions' : ' and Questions');
                    message += questions;
                    sl++;
                    links +='<h5>'+sl+'. Click <a target="_blank" href="'+admin_url+'generate_test/?&filter=all&type=all&category='+category_id+'&subject=all&topic=all&offset=1">here</a> to see the assigned questions.</h5>';
                }
    
                if(subjects > 0){
                    
                    message += ' and Subjects ';
                    
                    //links +='<h5 target="_blank" href="'+admin_url+'course/?&filter=all&category='+category_id+'">Click <a>here</a> to see assigned subjects</h5>';
                    sl++;
                }

                if(sl > 0) {

                    var header_text = '<h5><b> Some '+message+' are linked to this category. Are you sure to make this category private ?</b></h5>'+links;
                
                }else{
                    
                    var header_text = 'Are you sure to make this category private ?'+links;
                    
                }
                

        break;
            case "make_public":
                ok_button_text = lang('make_public').toUpperCase();
                var header_text = 'Are you sure to make this category Public ?';
                ct_status         = '1';
            break;
        }

    //var header_text = courses+'Are you sure to ' + lang(action).toLowerCase() + ' the category named "' + category_name + '"';
    

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
        'category_id': category_id,
        'ct_status'  : ct_status
        },
    };

    if(instead){
        changeStatusConfirmed({'data':{'category_id': category_id,'ct_status': ct_status}});
    }else{
        callback_warning_modal(messageObject, changeStatusConfirmed);
    }
    
    });
}


function changeStatusConfirmed(params){
    var category_id = params.data.category_id;
    $.ajax({
        url: admin_url+'question_manager/change_category_status',
        type: "POST",
        data:{"category_id":category_id, "ct_status" : params.data.ct_status, "is_ajax":true},
        success: function(response) {
             //console.log(response); //return;
            var data  = $.parseJSON(response);
            if(data.error == false)
            {
                $('#category_' + category_id).html(renderCategoryRow(data.category));
                
                var messageObject = {
                    'body': 'category status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                // scrollToTopOfPage();                
            }
            else
            {
                var messageObject = {
                    'body': data.message,
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function renderCategoryRow(data){
    var ct_status_label = data.ct_status == '1' ? 'public' : 'private';
    var ct_status_class = data.ct_status == '1' ? 'success' : 'warning';
    var html = `<div class="drager ui-sortable-handle">
                    <!--<img src="${__asstes_url}images/drager.png">-->
                    <div class="drager-icon">............</div>
                </div>
                <div class="lecture-hold question-category-lecturehold">
                    <span id="cat_${data.id}"  class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="${data.ct_name}">`+((data.ct_name.length > 25)?(data.ct_name.substr(0, 22)+'...'):data.ct_name)+`
                        <label class="pull-right label label-${ct_status_class}" style="margin-top: 10px;" id="action_class_${__categoryId}">${lang(ct_status_label)}</label>
                    </span>
                        <div class="btn-group lecture-control"  >
                            <span class="dropdown-tigger"   data-toggle="dropdown">
                                <span class="label-text">
                                    <i class="icon icon-down-arrow"></i>
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li id="status_btn_${data.id}">`;
                                    var ct_status = data.ct_status == '1' ? 'make_private' : 'make_public';
                                    //console.log(data.ct_status, ct_status, lang(ct_status));
        html +=` 
                                    <a href="javascript:void(0);" onclick="changeCategoryStatus('${data.id}', '${ct_status}','${addslashes(data.ct_name)}' )" > ${lang(ct_status)}</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="editCategory('${data.id}', '${ct_status_label}')">Edit</a>
      
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory('${btoa(unescape(encodeURIComponent(data.ct_name)))}', '${data.id}')">Migrate</a>
                                </li>`;
                                if(jQuery.inArray( 4, data.manager)){
        html +=`
                                <li>
                                    <a href="javascript:void(0)" onclick="deleteCategory('${btoa(unescape(encodeURIComponent(data.ct_name)))}', '${data.id}')">Delete</a>
                                </li>`;

                                }
        html +=`
                            </ul>
                        </div>
                    </div>`;
   return html;
}

function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}
var __ajaxInProgress = 0;
var __categoryId = 0;
var __subjectId = 0;
var __topicId = 0;
var __categoriesRecieved = new Array();
var __fromId        = 0;
var __toId          = 0;
var __migrate_from  = '';
var __migrate_to    = '';
var __status_label  = '';

function editCategory(categoryID, status_label=false)
{
    __status_label = status_label;
    //console.log(__status_label, status_label);
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_category',
        type: "POST",
        data:{ "is_ajax":true, 'id':categoryID},
        success: function(response) {
            
            var data  = JSON.parse(response);
            //console.log(data.category.ct_name);
            if(data.error == false)
            {
                //console.log('hcvbcv bn');
                __categoryId = categoryID;
                $('#category_name').val('');
                if(Number(__categoryId) > 0)
                {
                    var category_data = data.category;
                    
                    //console.log( category_data.ct_name);
                    $('#category_name').val(category_data.ct_name);
                }
                $('#category_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data.message);
            }
        }
    });
}

function saveCategory()
{
    //console.log('saveCategory 1');
    //console.log(__status_label, 'saveCategory');
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category_name         = $('#category_name').val();
    var errorCount            = 0;
    var errorMessage          = '';

    if (category_name == '')
    {
        errorMessage += 'Enter category name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#category_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_category',
            type: "POST",
            data: {"is_ajax": true, 'cat_name': category_name, 'cat_id': __categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);

                var actionClass = __status_label == 'private' ? 'warning' : 'success';
                
                if(data['error']==false)
                {
                    $('#save_category_btn').html('SAVING...<ripples></ripples>');
                    var category_data = data['category'];
                    var renderCategoryHtml = '';
                    
                    if(data['exist'] == '1')
                    {
                        renderCategoryHtml += ' <div class="drager"><div class="drager-icon">............</div></div><div class="lecture-hold question-category-lecturehold">';
                        renderCategoryHtml += '         <span data-toggle="tooltip" data-placement="top" data-original-title="'+((category_data['ct_name'].length > 25)?(category_data['ct_name']):'')+'" class="lecture-name question-category-lecturename catagory">'+((category_data['ct_name'].length > 25)?(category_data['ct_name'].substr(0, 25)+'...'):category_data['ct_name'])+'<label class="pull-right label label-'+actionClass+'" style="margin-top: 10px;" id="action_class_'+__categoryId+'">'+lang(__status_label)+'</label></span>';
                        if((__cat_permission.indexOf("3")!=-1) || (__cat_permission.indexOf("4")!=-1)){
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__cat_permission.indexOf("3")!=-1){
                        renderCategoryHtml += `             <li id="status_btn_${category_data['id']}">`;
                                                                var ct_status       = __status_label == 'public' ? 'make_private' : 'make_public';
                                                                var ct_status_label = __status_label;//category_data['ct_status'] == '1' ? 'public' : 'private';
                        renderCategoryHtml +=`                  <a href="javascript:void(0);" onclick="changeCategoryStatus('${category_data['id']}', '${ct_status}','${addslashes(category_data['ct_name'])}' )" > ${lang(ct_status)}</a>
                                                            </li>`;
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editCategory(\''+category_data['id']+'\', \''+ct_status_label+'\')">Edit</a></li>';
                        }
                        if(__cat_permission.indexOf("2")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory(\''+btoa(unescape(encodeURIComponent(category_data['ct_name'])))+'\', \''+category_data['id']+'\')">Migrate</a></li>';
                        }
                        if(__cat_permission.indexOf("4")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteCategory(\''+btoa(unescape(encodeURIComponent(category_data['ct_name'])))+'\',\''+category_data['id']+'\')">Delete</a></li>';
                        }
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        }
                        renderCategoryHtml += ' </div>';
                        $('#category_'+category_data['id']).html(renderCategoryHtml);
                    }
                    $('#category_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip(); 
                }
                else
                {
                    $('#category_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                $('#save_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveQusetionSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var subject_action              = $('#subject_action').val();
    var category_name               = $('#cat_'+__sel_category).text();
    var category                    = __sel_category;   
    var subject_name                = $('#qusetion_subject').val();
    var errorCount                  = 0;
    var errorMessage                = '';
    if(subject_action==0){
        if (category == 0)
        {
            errorMessage += 'Please add course category<br />';
            errorCount++;
        } 
    }
    if (subject_name == '')
    {
        errorMessage += 'Enter subject name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#subject_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_question_subject',
            type: "POST",
            data: {"is_ajax": true,'category':category, 'subject_name':subject_name, 'subject_id':__subjectId,'category_name':category_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#save_que_subject_btn').html('SAVING...<ripples></ripples>');
                    var subject_data = data['ques_subject'];
                    var renderSubjectHtml = '';
                    $(".subject-data").html('');
                    if(data['exist'] == '1')
                    {
                        //$('#subject_'+subject_data['id']+' .lecture-name').html(subject_data['qs_subject_name']);
                        renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                        renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"   id="sub_'+__sel_subject+'"   data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data['qs_subject_name'].length > 35)?(subject_data['qs_subject_name']):'')+'">'+((subject_data['qs_subject_name'].length > 35)?(subject_data['qs_subject_name'].substr(0, 32)+'...'):subject_data['qs_subject_name'])+'</span>';
                        if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                        renderSubjectHtml += '     <div class="btn-group lecture-control">';
                        renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderSubjectHtml += '             <span class="label-text">';
                        renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderSubjectHtml += '             </span>';
                        renderSubjectHtml += '             <span class="tilder"></span>';
                        renderSubjectHtml += '         </span>';
                        renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__qus_permission.indexOf("3")!=-1){
                        
                        renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data['id']+'\')">Edit</a></li>';
                        }
                        if(__qus_permission.indexOf("4")!=-1){
                        renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data['qs_subject_name'])))+'\',\''+subject_data['id']+'\')">Delete</a></li>';
                        }
                        renderSubjectHtml += '         </ul>';
                        renderSubjectHtml += '     </div>';
                        }
                        renderSubjectHtml += ' </div>';
                        $('#subject_'+subject_data['id']).html(renderSubjectHtml);
                    }
                    $('#subject_manage').modal('hide');
                    __categoryId = 0;
                    $('[data-toggle="tooltip"]').tooltip();
                }
                else
                {
                    $('#subject_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                $('#save_que_subject_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveBulkCategory()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category_names        = $('#bulk_categories').val();
    var errorCount            = 0;
    var errorMessage          = '';

    if (category_name == '')
    {
        errorMessage += 'Enter Categories Name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_category_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_category',
            type: "POST",
            data: {"is_ajax": true, 'cat_names': category_names},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#save_category_btn').html('SAVING...<ripples></ripples>');
                    category_data      = [];
                    for (i = 0; i < data['category'].length; i++) { 
                        var renderCategoryHtml = '';
                        var category_data  = data['category'][i];
                        renderCategoryHtml += '<li class="dragging ui-sortable-handle" id="category_'+category_data['id']+'" onclick="populateSubjects('+category_data['id']+')">';
                        renderCategoryHtml += ' <div class="drager"><div class="drager-icon">............</div></div><div class="lecture-hold question-category-lecturehold">';
                        renderCategoryHtml += '         <span class="lecture-name question-category-lecturename catagory" id="cat_'+category_data['id']+'" onclick="populateSubjects('+category_data['id']+')"  data-toggle="tooltip" data-placement="top" data-original-title="'+((category_data.ct_name.length > 35)?(category_data.ct_name):'')+'">'+((category_data.ct_name.length > 25)?(category_data.ct_name.substr(0, 22)+'...'):category_data.ct_name)+'<label class="pull-right label label-success" style="margin-top: 10px;" id="action_class_'+__categoryId+'">'+lang('public')+'</label></span>';
                        if((__cat_permission.indexOf("3")!=-1) || (__cat_permission.indexOf("4")!=-1)){
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger"  data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__cat_permission.indexOf("3")!=-1){
                        renderCategoryHtml += `             <li id="status_btn_${category_data['id']}">`;
                                                                var ct_status       = category_data['ct_status'] == '1' ? 'make_private' : 'make_public';
                                                                var ct_status_label = category_data['ct_status'] == '1' ? 'public' : 'private';
                        renderCategoryHtml +=` 
                                                                <a href="javascript:void(0);" onclick="changeCategoryStatus('${category_data['id']}', '${ct_status}','${addslashes(category_data['ct_name'])}' )" > ${lang(ct_status)}</a>
                                                            </li>`;
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editCategory(\''+category_data['id']+'\',  \''+ct_status_label+'\')">Edit</a></li>';
                        }
                        if(__cat_permission.indexOf("2")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory(\''+btoa(unescape(encodeURIComponent(category_data['ct_name'])))+'\', \''+category_data['id']+'\')">Migrate</a></li>';
                        }
                        if(__cat_permission.indexOf("4")!=-1){
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteCategory(\''+btoa(unescape(encodeURIComponent(category_data['ct_name'])))+'\',\''+category_data['id']+'\')">Delete</a></li>';
                        }
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        }
                        renderCategoryHtml += ' </div>';
                        renderCategoryHtml += '</li>';
                        category_data      = [];
                        $('#category_manage_wrapper').prepend(renderCategoryHtml);
                    }
                    $('#category_manage_wrapper .subject-data').html('');
                    $('#bulk_categories').val('');
                    $('#bulk_category_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip();
                    $("#category_manage_wrapper ul li:first").click(); 
                    var sel_id = $('ul#category_manage_wrapper li:first').attr('id');
                        sel_id = sel_id.substring(sel_id.lastIndexOf("_"));
                    $("#cat"+sel_id).trigger('click');
                }
                else
                {
                    $('#bulk_category_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveBulkSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    //var course_category        = $('#question_course_category').val();
    var course_category        = __sel_category; 
    var bulk_subject           = $('#bulk_subject').val();
    var errorCount             = 0;
    var errorMessage           = '';
    var category_name          = $('#cat_'+__sel_category).text();

    if (course_category == '')
    {
        errorMessage += 'Please select the course category<br />';
        errorCount++;
    }

    if (bulk_subject == '')
    {
        errorMessage += 'Please enter the subject names<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_subject_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_subject',
            type: "POST",
            data: {"is_ajax": true, 'course_category': course_category,'sub_names': bulk_subject,'category_name':category_name},
            success: function (response) {
                $(".subject-data").html('');
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                if(data['error']==false)
                {
                    $('#save_subject_btn').html('SAVING...<ripples></ripples>');
                    if(__sel_category==course_category){
                        var subject_data      = '';
                        for (i = 0; i < data['subject'].length; i++) { 
                            var renderSubjectHtml = '';
                            subject_data         = data['subject'][i];
                            renderSubjectHtml += '<li id="subject_'+subject_data['id']+'"  onclick="populateTopics('+__sel_category+','+subject_data['id']+')">';
                            renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory" id="sub_'+subject_data['id']+'"   data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data['qs_subject_name'].length > 35)?subject_data['qs_subject_name']:'')+'">'+((subject_data['qs_subject_name'].length > 35)?(subject_data['qs_subject_name'].substr(0, 32)+'...'):subject_data['qs_subject_name'])+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderSubjectHtml += '     <div class="btn-group lecture-control">';
                            renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderSubjectHtml += '             <span class="label-text">';
                            renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderSubjectHtml += '             </span>';
                            renderSubjectHtml += '             <span class="tilder"></span>';
                            renderSubjectHtml += '         </span>';
                            renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data['id']+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data['qs_subject_name'])))+'\',\''+subject_data['id']+'\')">Delete</a></li>';
                            }
                            renderSubjectHtml += '         </ul>';
                            renderSubjectHtml += '     </div>';
                            }
                            renderSubjectHtml += ' </div>';
                            renderSubjectHtml += '</li>';
                            subject_data   = '';
                            $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                            if(i==(data['subject'].length-1)){
                                __sel_subject       = data['subject'][i]['id'];
                            }
                        }
                        $("#question_subject_manage_wrapper>li.select").removeClass("select");
                        $("#subject_"+__sel_subject).addClass("select");
                        $('#bulk_subject').val('');
                        $('#bulk_subject_manage').modal('hide');
                        $('[data-toggle="tooltip"]').tooltip();
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                        populateTopics(__sel_category,__sel_subject);
                    }
                   
                }
                else
                {
                    $('#bulk_subject_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_subject_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function populateSubjects(categoryId)
{   
    if(__qus_permission.indexOf("1")!=-1){
        __ajaxInProgress = 1;
        __sel_category   = categoryId;
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#question_topic_manage_wrapper").html("");
                    $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>No topics available.</p>");
                    $("#question_subject_manage_wrapper").html(" ");
                       
                        var renderSubjectHtml = '';
                        var subject_data      = [];
                        if(data['subject'].length!=0){
                            for (i = 0; i < data['subject'].length; i++) { 
                            subject_data       = data['subject'][i];
                            renderSubjectHtml += '<li id="subject_'+subject_data['id']+'" onclick="populateTopics('+__sel_category+','+subject_data['id']+')">';
                            renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"  id="sub_'+subject_data['id']+'"    data-toggle="tooltip" data-placement="top" data-original-title="'+((subject_data['qs_subject_name'].length > 35)?subject_data['qs_subject_name']:'')+'">'+((subject_data['qs_subject_name'].length > 35)?(subject_data['qs_subject_name'].substr(0, 32)+'...'):subject_data['qs_subject_name'])+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderSubjectHtml += '     <div class="btn-group lecture-control">';
                            renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderSubjectHtml += '             <span class="label-text">';
                            renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderSubjectHtml += '             </span>';
                            renderSubjectHtml += '             <span class="tilder"></span>';
                            renderSubjectHtml += '         </span>';
                            renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+subject_data['id']+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(subject_data['qs_subject_name'])))+'\',\''+subject_data['id']+'\')">Delete</a></li>';
                            }
                            renderSubjectHtml += '         </ul>';
                            renderSubjectHtml += '     </div>';
                            }
                            renderSubjectHtml += ' </div>';
                            renderSubjectHtml += '</li>';
                         }
                        __sel_subject = data['subject'][0]['id'];
                        $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                        $("#subject_"+__sel_subject).addClass("select");
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                        populateTopics(__sel_category,__sel_subject);
                        } 
                        else
                        {
                            $("#question_subject_manage_wrapper").html("<p class='subject-data' style='margin-top:10em;text-align: center;'>No subjects available.</p>");
                            __sel_subject =0;
                        }
                    $('[data-toggle="tooltip"]').tooltip(); 
                }
            }
        });
    } else {
        return false;
    }

}

function generateSubjects()
{
        var categoryId   = $('#questions_course_category').val();
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#questions_course_subject").html(" ");
                       // subject_data      = [];
                    if(data['subject'].length!=0){
                        for (i = 0; i < data['subject'].length; i++) { 
                        var renderSubjectHtml = '';
                        var subject_data   = data['subject'][i];
                        renderSubjectHtml += '<option value="'+subject_data['id']+'">'+subject_data['qs_subject_name']+'</option>';
                        $('#questions_course_subject').prepend(renderSubjectHtml);
                        
                       }
                    } 
                }
            }
        });

}

function populateTopics(categoryId,subjectId)
{   
    if(__qus_permission.indexOf("1")!=-1){
        __sel_subject    = subjectId;
        __ajaxInProgress = 1;
        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading...</p>");
        $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId, 'question_subject': subjectId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                       // subject_data      = [];
                    if(data['topic'].length!=0){
                        var renderTopicHtml = '';
                        for (i = 0; i < data['topic'].length; i++) { 
                            var topic_data   = data['topic'][i];
                            renderTopicHtml += '<li id="topic_'+topic_data['id']+'">';
                            renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data['qt_topic_name'].length > 35)?topic_data['qt_topic_name']:'')+'">'+((topic_data['qt_topic_name'].length > 35)?(topic_data['qt_topic_name'].substr(0, 32)+'...'):topic_data['qt_topic_name'])+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderTopicHtml += '     <div class="btn-group lecture-control">';
                            renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderTopicHtml += '             <span class="label-text">';
                            renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderTopicHtml += '             </span>';
                            renderTopicHtml += '             <span class="tilder"></span>';
                            renderTopicHtml += '         </span>';
                            renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data['id']+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data['qt_topic_name'])))+'\',\''+topic_data['id']+'\')">Delete</a></li>';
                            }
                            renderTopicHtml += '         </ul>';
                            renderTopicHtml += '     </div>';
                            }
                            renderTopicHtml += ' </div>';
                            renderTopicHtml += '</li>';
                        }
                        $("#question_topic_manage_wrapper").html(" ");
                        $('#question_topic_manage_wrapper').prepend(renderTopicHtml);
                        $('[data-toggle="tooltip"]').tooltip();
                    } else {
                        $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>No topics available.</p>");
                     }

                }
            }
        });
    } else {
        return false;
    }
}

function deleteCategory(category_name, category_id)
{
    $.ajax({
        url: admin_url + 'question_manager/check_category_connection',
        type: "POST",
        data: {"is_ajax": true, 'cat_name': atob(category_name), 'cat_id': category_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data['error']==false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the category named "'+atob(category_name)+'"',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'cat_id':category_id,'category_name':atob(category_name), 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteCategoryConfirmed);
            }else
            {
                 var messageObject = {
                'body':data['message'],
                'button_yes':'OK', 
            };
            callback_warning_modal(messageObject);
            }
        }
    });
}

function deleteCategoryConfirmed(param)
{
    var catgory_id    = param.data.cat_id;
    var category_name = param.data.category_name;
    $.ajax({
        url: admin_url+'question_manager/delete_category',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':catgory_id,'category_name':category_name},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#category_'+catgory_id).remove();
            if($("#category_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" style="margin-top:10em;text-align:center;">No categories available.</p>';
                $("#category_manage_wrapper").prepend(htmlElement);
                __sel_category =0;
                __sel_subject  =0;
            } else {
                $("#category_manage_wrapper ul li:first").click();
            }
            var messageObject = {
                'body':data['message'],
                'button_yes':'OK', 
            };
            callback_success_modal(messageObject);
        }
    });
}
function generateSubjectList()
{
        var categoryId   = $('#topic_bulk_category').val();
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#topic_bulk_subject").html(" ");
                       // subject_data      = [];
                    if(data['subject'].length!=0){
                        for (i = 0; i < data['subject'].length; i++) { 
                        var renderSubjectHtml = '';
                        var subject_data   = data['subject'][i];
                        renderSubjectHtml += '<option value="'+subject_data['id']+'">'+subject_data['qs_subject_name']+'</option>';
                        $('#topic_bulk_subject').prepend(renderSubjectHtml);
                        
                       }
                    } 
                }
            }
        });

}

function generateCategoryList()
{
        __ajaxInProgress = 1;
        $.ajax({
            url: admin_url + 'question_manager/get_category',
            type: "POST",
            data: {"is_ajax": true},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#question_parent_category").html(" ");
                    $("#questions_course_category").html(" ");
                    
                       // subject_data      = [];
                       var renderCategoryHtml = '';
                    if(data['filter_category'].length!=0){
                        var renderCategoryHtml = '<option>Choose course category</option>';
                        for (i = 0; i < data['filter_category'].length; i++) { 
                        var category_data      = data['filter_category'][i];
                        renderCategoryHtml += '<option value="'+category_data['id']+'">'+category_data['ct_name']+'</option>';
                       }
                        $('#question_parent_category').prepend(renderCategoryHtml);
                        $('#questions_course_category').prepend(renderCategoryHtml);
                    } 
                }
            }
        });

}
function migrateCategory(category_name, category_id)
{
    $('#category_select_migrate').val('');
    $.ajax({
        url: admin_url+'question_manager/get_category',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':category_id },
        success: function(response) {
            var data  = $.parseJSON(response);
            var renderCatListing = '';
            var renderCatListingto = '';
            __categoriesRecieved = data['filter_category'];
            
            renderCatListing += '<option value="0">Choose Category</option>';
            renderCatListing += renderCategoriesLi(__categoriesRecieved,category_id,1);
            $('#category_selected_migrate').html(renderCatListing);
            
            renderCatListingto += '<option value="0">Choose Category</option>';
            renderCatListingto += renderCategoriesLi(__categoriesRecieved,category_id,2);
            $('#category_select_migrate').html(renderCatListingto);
        }
    });
    __fromId         = category_id;
    __migrate_from   = category_name;

    $('#save_migrate_category_btn').unbind();
    $('#save_migrate_category_btn').click({"cat_id": category_id, 'cat_name':category_name, 'is_ajax':'true'}, migrateCategoryConfirmed);
}

function renderCategoriesLi(categories,selected,type){
    var cHtml = '';

    switch(type){
        case 1:
            $.each(categories,function(c_key,category){
                if(selected != 0 && category['id'] == selected){
                    cHtml += '<option value="'+category['id']+'" selected >'+category['ct_name']+'</option>';
                }else{
                    cHtml += '<option value="'+category['id']+'">'+category['ct_name']+'</option> ';
                }
            });
        break;

        case 2:
            $.each(categories,function(c_key,category){
                if(selected != 0 && category['id'] != selected){
                    cHtml += '<option value="'+category['id']+'">'+category['ct_name']+'</option>';
                }
            });
        break;
    }

    return cHtml;
}

$(document).on('change', '#category_selected_migrate', function() {
    var renderCatListing = '';
    __fromId             = this.value;
    __migrate_from       = btoa(unescape(encodeURIComponent($("#category_selected_migrate").find(":selected").text())));
    __toId               = 0;
    __migrate_to         = '';
    renderCatListing += '<option value="0">Choose Category</option>';
    renderCatListing += renderCategoriesLi(__categoriesRecieved,this.value,2);
    $('#category_select_migrate').html(renderCatListing);
})

$(document).on('change', '#category_select_migrate', function() {
    __toId          = this.value;
    __migrate_to    = btoa(unescape(encodeURIComponent($("#category_select_migrate").find(":selected").text())));
})

function migrateCategoryConfirmed(param)
{
    var migrate_category_id = __toId;
    var errorCount            = 0;
    var errorMessage          = '';
    
    if (migrate_category_id == 0)
    {
        errorMessage += 'Please select category.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#category_migrate .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
    else{
        $.ajax({
            url: admin_url+'question_manager/migrate_category',
            type: "POST",
            data:{ "is_ajax":true, 'cat_id':migrate_category_id, 'previous_cat_id':__fromId,'from_category':atob(__migrate_from),'to_category':atob(__migrate_to)},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#category_migrate').modal('hide');
                $('#category_manage_wrapper').prepend(renderPopUpMessage('success', data['message']));
                $("#category_manage_wrapper ul li:first").click();
                var sel_id = $('ul#category_manage_wrapper li:first').attr('id');
                    sel_id = sel_id.substring(sel_id.lastIndexOf("_"));
                    $("#cat_"+sel_id).trigger('click'); 
            }
        });
    }
}

function editQuestionSubject(subjectID)
{
    
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_question_subject',
        type: "POST",
        async: false,
        data:{ "is_ajax":true, 'id':subjectID},
        success: function(response) {
            generateCategoryList();
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                __subjectId = subjectID;
                $('#qusetion_subject').val('');
                if(__subjectId > 0)
                {
                    $('#subject_category_selection').remove();
                    $('#subject_action').val('1');
                    var subject_data = data['subject'];
                    $('#qusetion_subject').val(subject_data['qs_subject_name']);
                } else {
                    //alert(__sel_category);
                    $('#question_parent_category').val(__sel_category);
                }
                $('#subject_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data['message']);
            }
        }
    });
}

function deleteSubject(subject_name, subject_id)
{
    $.ajax({
        url: admin_url + 'question_manager/check_subject_connection',
        type: "POST",
        data: {"is_ajax": true, 'subject_name': atob(subject_name), 'subject_id': subject_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data['error']==false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the subject named "'+atob(subject_name)+'"',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'subject_id':subject_id,'subject_name': atob(subject_name), 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteSubjectConfirmed);
           }else
            {
                lauch_common_message('Error in deleting categories', data['message']);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteSubjectConfirmed(param)
{
    var category_name               = $('#cat_'+__sel_category).text();
    $.ajax({
        url: admin_url+'question_manager/delete_question_subject',
        type: "POST",
        data:{ "is_ajax":true, 'subject_id':param.data.subject_id,'subject_name':param.data.subject_name,'category_name':category_name},
        success: function(response) {
            var data            = $.parseJSON(response);
            $('#subject_'+data.subject_id).remove();
            if($("#question_subject_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" style="margin-top:10em;text-align:center;">No subjects available.</p>';
                $("#question_subject_manage_wrapper").prepend(htmlElement);
                __sel_subject=0;
            }
            else
            {
                $("#question_subject_manage_wrapper ul li:first").click();
                var sel_id    = $('ul#question_subject_manage_wrapper li:first').attr('id');
                    sel_id    = sel_id.substring(sel_id.lastIndexOf("_"));
                $("#sub"+sel_id).trigger('click');
            }
            var messageObject = {
                'body':data['message'],
                'button_yes':'OK', 
            };
        callback_success_modal(messageObject);  
         }
        
    });
}

function editQuestionTopic(topicID)
{
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'question_manager/edit_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'id':topicID},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                __topicId = topicID;
                $('#question_topic').val('');
                if(__topicId > 0)
                {
                    var topic_data = data['topic'];
                    $('#question_topic').val(topic_data['qt_topic_name']);
                    $('#topic-action-selection').remove();
                    $('#topic_action').val('1');
                }
                //generateCategoryList();
                $('#topic_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data['message']);
            }
        }
    });
}

function saveQusetionTopic()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category                    = __sel_category;
    var subject                     = __sel_subject;
    var question_topic              = $('#question_topic').val();
    var errorCount                  = 0;
    var errorMessage                = '';
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    
    if (category == '')
    {
        errorMessage += 'Please select course category<br />';
        errorCount++;
    }
    if (subject == 0)
    {
        errorMessage += 'Please add question subject<br />';
        errorCount++;
    }

    if (question_topic == '')
    {
        errorMessage += 'Enter topic name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#topic_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_question_topic',
            type: "POST",
            data: {"is_ajax": true,'category':category, 'question_subject':subject,'topic_name':question_topic,'topic_id':__topicId,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#save_que_topic_btn').html('SAVING...<ripples></ripples>');
                    var topic_data = data['ques_topic'];
                    var renderTopicHtml = '';
                    $("#topic-data").html('');
                    if(data['exist'] == '1')
                    {
                     
                        renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                        renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data['qt_topic_name'].length > 35)?(topic_data['qt_topic_name']):'')+'">'+((topic_data['qt_topic_name'].length > 35)?(topic_data['qt_topic_name'].substr(0, 32)+'...'):topic_data['qt_topic_name'])+'</span>';
                        if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                        renderTopicHtml += '     <div class="btn-group lecture-control">';
                        renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderTopicHtml += '             <span class="label-text">';
                        renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderTopicHtml += '             </span>';
                        renderTopicHtml += '             <span class="tilder"></span>';
                        renderTopicHtml += '         </span>';
                        renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        if(__qus_permission.indexOf("3")!=-1){
                        renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data['id']+'\')">Edit</a></li>';
                        }
                        if(__qus_permission.indexOf("4")!=-1){
                        renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data['qt_topic_name'])))+'\',\''+topic_data['id']+'\')">Delete</a></li>';
                        }
                        renderTopicHtml += '         </ul>';
                        renderTopicHtml += '     </div>';
                        }
                        renderTopicHtml += ' </div>';
                        $('#topic_'+topic_data['id']).html(renderTopicHtml);
                    }
                    $('#topic_manage').modal('hide');
                    $('[data-toggle="tooltip"]').tooltip();
                    __categoryId = 0;
                    __subjectId  = 0;
                }
                else
                {
                    $('#topic_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                $('#save_que_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function deleteTopic(topic_name, topic_id)
{
    $.ajax({
        url: admin_url + 'question_manager/check_topic_connection',
        type: "POST",
        data: {"is_ajax": true, 'topic_name': atob(topic_name), 'topic_id': topic_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data['error']==false)
            {
                var messageObject = {
                    'body':'Are you sure to delete the topic named "'+atob(topic_name)+'"',
                    'button_yes':'CONTINUE', 
                    'button_no':'CANCEL',
                    'continue_params':{'topic_id':topic_id,'topic_name': atob(topic_name), 'is_ajax':'true'}
                };
                callback_warning_modal(messageObject, deleteTopicConfirmed);
           }else
            {
                lauch_common_message('Error in deleting topics', data['message']);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteTopicConfirmed(param)
{
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    $.ajax({
        url: admin_url+'question_manager/delete_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'topic_id':param.data.topic_id, 'topic_name':param.data.topic_name,'category_name':category_name,'subject_name':subject_name},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#topic_'+data.topic_id).remove();
            if($("#question_topic_manage_wrapper ul li").length==0){
                var htmlElement = '';
                htmlElement    +='<p class="subject-data" id="topic-data" style="margin-top:10em;text-align:center;">No topics available.</p>';
                $("#question_topic_manage_wrapper").prepend(htmlElement);
            }else {
                $("#question_topic_manage_wrapper ul li:first").click();
            }
            var messageObject = {
                'body':data['message'],
                'button_yes':'OK', 
            };
        callback_success_modal(messageObject);  }
    });
}

function saveBulkTopic()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var course_category           = __sel_category;
    var subject_id                = __sel_subject;
    var category_name             = $('#cat_'+__sel_category).text();
    var subject_name              = $('#sub_'+__sel_subject).text();
    
    var bulk_topics            = $('#bulk_topics').val();
    var errorCount             = 0;
    var errorMessage           = '';

    if (course_category == '')
    {
        errorMessage += 'Please choose the course category<br />';
        errorCount++;
    }

    if (subject_id == 0)
    {
        errorMessage += 'Please choose the question subject<br />';
        errorCount++;
    }

    if (bulk_topics == '')
    {
        errorMessage += 'Please enter the topic names<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/save_bulk_topic',
            type: "POST",
            data: {"is_ajax": true, 'course_category': course_category,'subject_id': subject_id,'bulk_topics': bulk_topics,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#save_bulk_topic_btn').html('SAVING...<ripples></ripples>');
                    $("#topic-data").html('');
                    if((__sel_category==course_category)&&(__sel_subject==subject_id)){
                        var topic_data      = [];
                        var renderTopicHtml = '';
                        for (i = 0; i < data['topic'].length; i++) { 
                            topic_data       = data['topic'][i];
                            renderTopicHtml += '<li id="topic_'+topic_data['id']+'">';
                            renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                            renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+((topic_data['qt_topic_name'].length > 35)?(topic_data['qt_topic_name']):'')+'">'+((topic_data['qt_topic_name'].length > 35)?(topic_data['qt_topic_name'].substr(0, 32)+'...'):topic_data['qt_topic_name'])+'</span>';
                            if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                            renderTopicHtml += '     <div class="btn-group lecture-control">';
                            renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                            renderTopicHtml += '             <span class="label-text">';
                            renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                            renderTopicHtml += '             </span>';
                            renderTopicHtml += '             <span class="tilder"></span>';
                            renderTopicHtml += '         </span>';
                            renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                            if(__qus_permission.indexOf("3")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+topic_data['id']+'\')">Edit</a></li>';
                            }
                            if(__qus_permission.indexOf("4")!=-1){
                            renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(topic_data['qt_topic_name'])))+'\',\''+topic_data['id']+'\')">Delete</a></li>';
                            }
                            renderTopicHtml += '         </ul>';
                            renderTopicHtml += '     </div>';
                            }
                            renderTopicHtml += ' </div>';
                            renderTopicHtml += '</li>';
                            $('#question_topic_manage_wrapper').prepend(renderTopicHtml); 
                            topic_data       = '';
                            renderTopicHtml  = '';
                        }
                        
                        $("#question_topic_manage_wrapper ul li:first").click();
                    }
                    $('#bulk_topic_manage').modal('hide');
                    $('#bulk_topics').val('');
                    $('[data-toggle="tooltip"]').tooltip();
                }
                else
                {
                    $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_bulk_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveMergeSubject()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var merge_subject_list       = [];
    var merged_subject_names     = [];
    $("input:checkbox[name=merge_subject_list]:checked").each(function(){
    merge_subject_list.push($(this).val());
    merged_subject_names.push($(this).next('label').text());
    });
    var merge_subject_name     = $('#merge_subject_name').val();
    var errorCount             = 0;
    var errorMessage           = '';
    var category_name               = $('#cat_'+__sel_category).text();

    if (merge_subject_list.length==0)
    {
        errorMessage += 'Please choose the question subject<br />';
        errorCount++;
    }

    if (merge_subject_name == '')
    {
        errorMessage += 'Please enter the subject name<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_subject_merge .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/merge_subject',
            type: "POST",
            data: {"is_ajax": true, 'merge_subjects': merge_subject_list,'merge_subject_name': merge_subject_name,'category_id':__sel_category,'merged_subject_names':merged_subject_names,'category_name':category_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#question_subject_manage_wrapper li.select').removeClass('select');
                    var renderSubjectHtml = '';
                    renderSubjectHtml += '<li id="subject_'+data['subject_id']+'" class="select" onclick="populateTopics('+__sel_category+','+data['subject_id']+')"  >';
                    renderSubjectHtml += ' <div class="lecture-hold question-category-lecturehold">';
                    renderSubjectHtml += '         <span class="lecture-name question-category-lecturename catagory"  id="sub_'+data['subject_id']+'"    data-toggle="tooltip" data-placement="top" data-original-title="'+((merge_subject_name.length > 35)?merge_subject_name:'')+'">'+((merge_subject_name.length > 35)?(merge_subject_name.substr(0, 32)+'...'):merge_subject_name)+'</span>';
                    if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                    renderSubjectHtml += '     <div class="btn-group lecture-control">';
                    renderSubjectHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                    renderSubjectHtml += '             <span class="label-text">';
                    renderSubjectHtml += '                 <i class="icon icon-down-arrow"></i>';
                    renderSubjectHtml += '             </span>';
                    renderSubjectHtml += '             <span class="tilder"></span>';
                    renderSubjectHtml += '         </span>';
                    renderSubjectHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                    if(__qus_permission.indexOf("3")!=-1){
                    renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionSubject(\''+data['subject_id']+'\')">Edit</a></li>';
                    }
                    if(__qus_permission.indexOf("4")!=-1){
                    renderSubjectHtml += '             <li><a href="javascript:void(0)" onclick="deleteSubject(\''+btoa(unescape(encodeURIComponent(merge_subject_name)))+'\',\''+data['subject_id']+'\')">Delete</a></li>';
                    }
                    renderSubjectHtml += '         </ul>';
                    renderSubjectHtml += '     </div>';
                    }
                    renderSubjectHtml += ' </div>';
                    renderSubjectHtml += '</li>';
                    $('#question_subject_manage_wrapper').prepend(renderSubjectHtml);
                    data['merge_subject_ids'].forEach(function(element) {
                      $('#subject_'+element).remove();
                    });
                    $('#bulk_subject_merge').modal('hide');
                    $('#merge_subject_name').val('');
                    $("#question_topic_manage_wrapper").html("<p id='topic-data' style='margin-top:10em;text-align: center;'>Loading</p>");
                    $('[data-toggle="tooltip"]').tooltip();
                    populateTopics(__sel_category,data['subject_id']);
                    
                }
                else
                {
                    $('#bulk_topic_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_bulk_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function saveMergeTopic()
{
    var merge_topic_list       = [];
    var merged_topic_names     = [];
    var category_name               = $('#cat_'+__sel_category).text();
    var subject_name                = $('#sub_'+__sel_subject).text();
    $("input:checkbox[name=merge_topic_list]:checked").each(function(){
        merge_topic_list.push($(this).val());
        merged_topic_names.push($(this).next('label').text());
    });
    var merge_topic_name     = $('#merge_topic_name').val();
    var errorCount             = 0;
    var errorMessage           = '';

    if (merge_topic_list.length==0)
    {
        errorMessage += 'Please choose the question topic<br />';
        errorCount++;
    }

    if (merge_topic_name == '')
    {
        errorMessage += 'Please enter the topic name<br />';
        errorCount++;
    }

    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#bulk_topic_merge .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        $.ajax({
            url: admin_url + 'question_manager/merge_topic',
            type: "POST",
            data: {"is_ajax": true, 'merge_topics': merge_topic_list,'merge_topic_name': merge_topic_name,'category_id':__sel_category,'subject_id':__sel_subject,'merged_topic_names':merged_topic_names,'category_name':category_name,'subject_name':subject_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $('#question_topic_manage_wrapper li.select').removeClass('select');
                    //$("#question_subject_manage_wrapper").html('');
                    var renderTopicHtml = '';
                    renderTopicHtml += '<li id="topic_'+data['topic_id']+'" class="select">';
                    renderTopicHtml += ' <div class="lecture-hold question-category-lecturehold">';
                    renderTopicHtml += '         <span class="lecture-name question-category-lecturename catagory"  data-toggle="tooltip" data-placement="top" data-original-title="'+merge_topic_name+'">'+merge_topic_name+'</span>';
                    if((__qus_permission.indexOf("3")!=-1) || (__qus_permission.indexOf("4")!=-1)){
                    renderTopicHtml += '     <div class="btn-group lecture-control">';
                    renderTopicHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                    renderTopicHtml += '             <span class="label-text">';
                    renderTopicHtml += '                 <i class="icon icon-down-arrow"></i>';
                    renderTopicHtml += '             </span>';
                    renderTopicHtml += '             <span class="tilder"></span>';
                    renderTopicHtml += '         </span>';
                    renderTopicHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                    if(__qus_permission.indexOf("3")!=-1){
                    renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="editQuestionTopic(\''+data['topic_id']+'\')">Edit</a></li>';
                    }
                    if(__qus_permission.indexOf("4")!=-1){
                    renderTopicHtml += '             <li><a href="javascript:void(0)" onclick="deleteTopic(\''+btoa(unescape(encodeURIComponent(merge_topic_name)))+'\',\''+data['topic_id']+'\')">Delete</a></li>';
                    }
                    renderTopicHtml += '         </ul>';
                    renderTopicHtml += '     </div>';
                    }
                    renderTopicHtml += ' </div>';
                    renderTopicHtml += '</li>';
                    $('#question_topic_manage_wrapper').prepend(renderTopicHtml);
                    data['merge_topic_ids'].forEach(function(element) {
                      $('#topic_'+element).remove();
                    });
                    $('#bulk_topic_merge').modal('hide');
                    $('#merge_topic_name').val('');
                    $('[data-toggle="tooltip"]').tooltip();
                }
                else
                {
                    $('#bulk_topic_merge .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_merge_topic_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function generateSubjectList()
{
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'question_manager/get_subjects',
            type: "POST",
            data: {"is_ajax": true, 'course_category': __sel_category},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#merge_subjects").html(" ");
                       // subject_data      = [];
                       var renderSubjectHtml = '';
                       //renderSubjectHtml = '<option value="">Choose Course Subject</option>';
                    if(data['subject'].length!=0){
                        for (i = 0; i < data['subject'].length; i++) { 
                        var subject_data   = data['subject'][i];
                         renderSubjectHtml += ' <div class="checkbox-wrap"  id="inst_course_'+i+'"><span class="chk-box"><input type="checkbox" name="merge_subject_list" class="inst-course" value="'+subject_data['id']+'"><label class="font14">'+subject_data['qs_subject_name']+'</label></span><span class="email-label pull-right"></span></div>';   
                        //renderSubjectHtml += '<option value="'+subject_data['id']+'">'+subject_data['qs_subject_name']+'</option>';
                       }
                       $('#merge_subjects').prepend(renderSubjectHtml);
                    } 
                }
            }
        });

}

function generateTopicList()
{
        $.ajax({
            url: admin_url + 'question_manager/get_topics',
            type: "POST",
            data: {"is_ajax": true, 'course_category': __sel_category, 'question_subject': __sel_subject},
            success: function (response) {
                var data = $.parseJSON(response);
                //console.log(data);
                if(data['error']==false)
                {
                    $("#merge_topics").html(" ");
                    var renderTopicHtml = '';
                   // renderSubjectHtml = '<option value="">Choose Course Topic</option>';

                    if(data['topic'].length!=0){
                        for (i = 0; i < data['topic'].length; i++) { 
                        var topic_data    = data['topic'][i];
                        renderTopicHtml += ' <div class="checkbox-wrap"  id="inst_course_'+i+'"><span class="chk-box"><input type="checkbox" name="merge_topic_list" class="inst-course" value="'+topic_data['id']+'"><label class="font14">'+topic_data['qt_topic_name']+'</label></span><span class="email-label pull-right"></span></div>';   
                        }
                        $('#merge_topics').prepend(renderTopicHtml);
                        
                    }
                }
            }
        });
}

function changeCategoryStatus(category_id, status, category_name)
{
    var action      = status;
    var ct_status   = 0;
    var courses     = '';

    
    $.get(admin_url+'question_manager/get_courses_by_category?id='+category_id, function(courseList){
        courseList = JSON.parse(courseList);
        //console.table(courseList);

        switch(status) {
            case "make_private":
                ok_button_text    = lang('make_private').toUpperCase();
                ct_status         = '0';
                var plural        = '';
                if(courseList.length > 0){
                    plural        = courseList.length > 1 ? 's are' : ' is';
                    $('.message-body').css('padding', '5px');
                        courses   ='<h5>Please be aware that the below listed course'+plural+' under this category</h5>';
                        var sl    = 1;
                        $.each( courseList, function( key, course ) {
                            courses +='<h6>'+sl+'. <b>'+course.cb_code+' -</b> '+course.cb_title+'</h6>';
                            sl++;
                        });
                }

        break;
            case "make_public":
                ok_button_text = lang('make_public').toUpperCase();
                ct_status         = '1';
            break;
        }

    var header_text = courses+'Are you sure to ' + lang(action).toLowerCase() + ' the category named "' + category_name + '"';

    var messageObject = {
        'body': header_text,
        'button_yes': ok_button_text,
        'button_no': 'CANCEL',
        'continue_params': {
        'category_id': category_id,
        'ct_status'  : ct_status
        },
    };
    //console.log(header_text);
    callback_warning_modal(messageObject, changeStatusConfirmed);
    
    });
}


function changeStatusConfirmed(params){
    var category_id = params.data.category_id;
    $.ajax({
        url: admin_url+'question_manager/change_category_status',
        type: "POST",
        data:{"category_id":category_id, "ct_status" : params.data.ct_status, "is_ajax":true},
        success: function(response) {
             //console.log(response); //return;
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#category_' + category_id).html(renderCategoryRow(data['category']));
                
                var messageObject = {
                    'body': 'category status changed successfully',
                    'button_yes': 'OK',
                };
                callback_success_modal(messageObject);
                // scrollToTopOfPage();                
            }
            else
            {
                var messageObject = {
                    'body': data['message'],
                    'button_yes': 'OK',
                };
                callback_danger_modal(messageObject);
            }
        }
    });
}

function renderCategoryRow(data){
    var ct_status_label = data['ct_status'] == '1' ? 'public' : 'private';
    var ct_status_class = data['ct_status'] == '1' ? 'success' : 'warning';
    var html = `<div class="drager ui-sortable-handle">
                    <!--<img src="${__asstes_url}images/drager.png">-->
                    <div class="drager-icon">............</div>
                </div>
                <div class="lecture-hold question-category-lecturehold">
                    <span id="cat_${data['id']}"  class="lecture-name question-category-lecturename catagory" data-toggle="tooltip" data-placement="top" data-original-title="${data['ct_name']}">`+((data.ct_name.length > 25)?(data.ct_name.substr(0, 22)+'...'):data.ct_name)+`
                        <label class="pull-right label label-${ct_status_class}" style="margin-top: 10px;" id="action_class_${__categoryId}">${lang(ct_status_label)}</label>
                    </span>
                        <div class="btn-group lecture-control"  >
                            <span class="dropdown-tigger"   data-toggle="dropdown">
                                <span class="label-text">
                                    <i class="icon icon-down-arrow"></i>
                                </span>
                                <span class="tilder"></span>
                            </span>
                            <ul class="dropdown-menu pull-right" role="menu">
                                <li id="status_btn_${data['id']}">`;
                                    var ct_status = data['ct_status'] == '1' ? 'make_private' : 'make_public';
                                    //console.log(data['ct_status'], ct_status, lang(ct_status));
        html +=` 
                                    <a href="javascript:void(0);" onclick="changeCategoryStatus('${data['id']}', '${ct_status}','${addslashes(data['ct_name'])}' )" > ${lang(ct_status)}</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="editCategory('${data['id']}', '${ct_status_label}')">Edit</a>
      
                                </li>
                                <li>
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory('${btoa(unescape(encodeURIComponent(data['ct_name'])))}', '${data['id']}')">Migrate</a>
                                </li>`;
                                if(jQuery.inArray( 4, data['manager'])){
        html +=`
                                <li>
                                    <a href="javascript:void(0)" onclick="deleteCategory('${btoa(unescape(encodeURIComponent(data['ct_name'])))}', '${data['id']}')">Delete</a>
                                </li>`;

                                }
        html +=`
                            </ul>
                        </div>
                    </div>`;
   return html;
}

function addslashes(str) {
    str = str.replace(/\\/g, '\\\\');
    str = str.replace(/\'/g, '\\\'');
    str = str.replace(/\"/g, '\\"');
    str = str.replace(/\0/g, '\\0');
    return str;
}
 
function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

function bulkCategory(){
     $('#popUpMessage').remove();
     $('#bulk_categories').val('');
}
function bulkSubjects(){
    $('#popUpMessage').remove();
    $('#bulk_subject').val('');
}
function bulkTopics(){
    $('#popUpMessage').remove();
    $('#bulk_topics').val('');
}



function stripslashes(str) {
    str = str.replace(/\\'/g, '\'');
    str = str.replace(/\\"/g, '"');
    str = str.replace(/\\0/g, '\0');
    str = str.replace(/\\\\/g, '\\');
    return str;
}

function bulkCategory(){
     $('#popUpMessage').remove();
     $('#bulk_categories').val('');
}
function bulkSubjects(){
    $('#popUpMessage').remove();
    $('#bulk_subject').val('');
}
function bulkTopics(){
    $('#popUpMessage').remove();
    $('#bulk_topics').val('');
}



