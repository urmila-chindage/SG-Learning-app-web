var __ajaxInProgress = 0;
var __categoryId     = 0;
var __mFromId        = 0;
var __fromQId        = 0;
var __toQId          = 0;

function editQueCategory(categoryID)
{
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'environment/edit_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'id':categoryID},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                __categoryId = categoryID;
                $('#question_parent_category').val('');
                $('#question_category_name').val('');
                if(__categoryId > 0)
                {
                    var category_data = data['que_category'];
                    $('#question_parent_category').val(category_data['qc_parent_id']);
                    $('#question_category_name').val(category_data['qc_category_name']);
                }
                $('#question_category_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data['message']);
            }
        }
    });
}

function saveQueCategory()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var parent_question_category    = $('#question_parent_category').val();
    var old_parent_category         = $('#question_parent_category'+__categoryId).val();
    var parent_cat_name             = $("#question_parent_category option:selected").text();
    var category_name               = $('#question_category_name').val();
    var errorCount                  = 0;
    var errorMessage                = '';

    if (parent_question_category == '')
    {
        errorMessage += 'Please select parent category<br />';
        errorCount++;
    }
    if (category_name == '')
    {
        errorMessage += 'Enter Category Name.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#question_category_manage .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    } 
    else
    {
        __ajaxInProgress = 1;
        
        $.ajax({
            url: admin_url + 'environment/save_question_topic',
            type: "POST",
            data: {"is_ajax": true,'old_parent_category':old_parent_category,'new_parent_category': parent_question_category, 'cat_id': __categoryId, 'cat_name':category_name},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                console.log(data);
                if(data['error']==false)
                {
                    $('#save_que_category_btn').html('SAVING...<ripples></ripples>');
                    var category_data = data['ques_category'];
                    var renderCategoryHtml = '';
                    
                    if(data['exist'] == '1')
                    {
                        $('#question_category_'+category_data['id']+' .lecture-name').html(parent_cat_name+' - '+category_data['qc_category_name']+' (<span id="qcount_'+category_data['id']+'">'+category_data['count']+'</span>)');
                        $('#question_parent_category'+__categoryId).val(category_data['qc_parent_id']);
                    }else
                    {
                       
                        renderCategoryHtml += '<li id="question_category_'+category_data['id']+'">';
                        renderCategoryHtml += ' <div class="lecture-hold question-category-lecturehold"> <div class="lecture-counter"></div>';
                        renderCategoryHtml += '     <a href="javascript:void(0)" class="lecture-innerclick category-innerclick">';
                        renderCategoryHtml += '         <span class="lecture-name">'+parent_cat_name+' - '+category_data['qc_category_name']+' (<span id="qcount_'+category_data['id']+'">'+category_data['count']+'</span>)</span>';
                        renderCategoryHtml += '     </a>';
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editQueCategory(\''+category_data['id']+'\')">Edit</a></li>';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#questions_migrate" onclick="migrateQueCategory(\''+category_data['qc_parent_id']+'\',\''+category_data['id']+'\',true)">Migrate</a></li>';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteQueCategory(\''+btoa(category_data['ct_name'])+'\',\''+category_data['id']+'\')">Delete</a></li>';
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        renderCategoryHtml += ' </div>';
                        renderCategoryHtml += '</li>';
                        
                        $('#question_category_manage_wrapper').prepend(renderCategoryHtml);
                        
                    }
                    $('#question_category_manage').modal('hide');
                    __categoryId = 0;
                }
                else
                {
                    $('#question_category_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                $('#save_que_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function deleteQueCategory(category_name, category_id)
{
    $.ajax({
        url: admin_url + 'environment/check_question_topic_connection',
        type: "POST",
        data: {"is_ajax": true, 'cat_name': atob(category_name), 'cat_id': category_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data['error']==false)
            {
                $('#topic_delete_header_text').html('Delete Topic named "'+atob(category_name)+'"');
                $('#topic_delete_message').html('Are you sure to delete the topic named "'+atob(category_name)+'?"');
                $('#deleteQueCategory').modal('show');
                $('#delete_topic_ok').unbind();
                $('#delete_topic_ok').click({"cat_id": category_id, 'is_ajax':'true'}, deleteQueCategoryConfirmed);
            }else
            {
                lauch_common_message('Error in deleting categories', data['message']);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteQueCategoryConfirmed(param)
{
    $.ajax({
        url: admin_url+'environment/delete_question_topic',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':param.data.cat_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#question_category_'+param.data.cat_id).remove();
            $('#deleteQueCategory').modal('hide');
            $('#question_category_manage_wrapper').prepend(renderPopUpMessage('success', data['message']))
        }
    });
}

var __from_que_cat = 0;

function migrateQueCategory(parent_category_id,child_category_id, isFirst)
{
	$('#popUpMessage').remove();
    $('#main_category_selected_to_migrate').val('');
    __mFromId = parent_category_id;
    __fromQId = child_category_id;
    var categories = getAjaxCat(0);
    var renderHtml = '<option value="0">Select Category</option>';
    renderHtml += renderQMCategories(categories,__mFromId);
    $('#main_category_from').html(renderHtml);

    renderHtml   = '<option value="0">Select Category</option>';
    renderHtml  += renderQMCategories(categories,0);
    $('#main_category_select_migrate').html(renderHtml);

    var cCategories = getAjaxCat(__mFromId);
    var rrenderHtml  = '';

    rrenderHtml      += '<option value="0">Select Category</option>';
    rrenderHtml      += renderQCCategories(cCategories,__fromQId,1);
    $('#question_category_from').html(rrenderHtml);  
}

$(document).on('change', '#main_category_select_migrate', function(){
    var cCategories = this.value != 0?getAjaxCat(this.value):new Array();
    var renderHtml  = '';
    __toQId         = 0;
    renderHtml      += '<option value="0">Select Category</option>';
    renderHtml      += renderQCCategories(cCategories,__fromQId,2);
    $('#question_category_select_migrate').html(renderHtml);
})

$(document).on('change', '#question_category_select_migrate', function(){
    __toQId         = this.value;
})

function renderQMCategories(categories,category_id){
    var renderHtml = '';

    $.each(categories,function(c_key,category){
        if(category_id != 0 && category_id == category['id']){
            renderHtml += '<option value="'+category['id']+'" selected>'+category['ct_name']+'</option>';
        }else{
            renderHtml += '<option value="'+category['id']+'">'+category['ct_name']+'</option>';
        }
    });
    return renderHtml;
}

function getAjaxCat(parent){
    var categories = new Array();
    $.ajax({
        url: admin_url+'environment/get_question_category',
        type: "POST",
        async:false,
        data:{ "is_ajax":true, 'parent_cat_id':parent},
        success: function(response) {
            var data  = $.parseJSON(response);
            categories = data['filter_category'];
        }
    });
    return categories;
}

$(document).on('change', '#main_category_from', function() {
    var cCategories = this.value != 0?getAjaxCat(this.value):new Array();
    var renderHtml  = '';
    __fromQId       = 0;
    renderHtml      += '<option value="0">Select Category</option>';
    renderHtml      += renderQCCategories(cCategories,__fromQId,1);
    $('#question_category_from').html(renderHtml);
    resetToQcat();
})

$(document).on('change', '#question_category_from', function() {
    __fromQId = this.value;
    resetToQcat();
})

function resetToQcat(){
    var categories = getAjaxCat(0);
    __toQId        = 0;
    var renderHtml = '<option value="0">Select Category</option>';
    renderHtml  += renderQMCategories(categories,0);
    $('#main_category_select_migrate').html(renderHtml);
    $('#question_category_select_migrate').html('');
}

function renderQCCategories(categories,category_id,stat){
    var renderHtml = '';

    switch(stat){
        case 1:
            $.each(categories,function(c_key,category){
                if(category_id != 0 && category_id == category['id']){
                    renderHtml += '<option value="'+category['id']+'" selected>'+category['qc_category_name']+'</option>';
                }else{
                    renderHtml += '<option value="'+category['id']+'">'+category['qc_category_name']+'</option>';
                }
            });
        break

        case 2:
            $.each(categories,function(c_key,category){
                if(category_id != 0 && category_id != category['id']){
                    renderHtml += '<option value="'+category['id']+'">'+category['qc_category_name']+'</option>';
                }
            });
        break;
    }
    return renderHtml;
}

function migrateQueCategoryConfirmed()
{
    var migrate_from = __fromQId;
    var migrate_to   = __toQId;
    var errorCount            = 0;
    var errorMessage          = '';
    
    if ((migrate_from!=0&&migrate_to!=0)&&(migrate_from == migrate_to))
    {
        errorMessage += 'You cannot migrate to same category.<br />';
        errorCount++;
    }
    if (migrate_to == 0)
    {
        errorMessage += 'Please choose a destination category.<br />';
        errorCount++;
    }
    if(migrate_from == 0){
        errorMessage += 'Please choose a source category.<br />';
        errorCount++;
    }
    $('#popUpMessage').remove();
    if (errorCount > 0)
    {
        $('#questions_migrate .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
    else{
        $.ajax({
            url: admin_url+'environment/migrate_question_category',
            type: "POST",
            data:{ "is_ajax":true, 'from':migrate_from,'to':migrate_to},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false){
                    $('#qcount_'+data['from']).html('0');
                    $('#qcount_'+data['to']).html(data['to_count']);
                	$('#questions_migrate').modal('hide');
                	$('#question_category_manage_wrapper').prepend(renderPopUpMessage('success', data['message']));
                }else{
                	$('#question_category_manage_wrapper').prepend(renderPopUpMessage('error', data['message']));
                }
            }
        });
    }
}