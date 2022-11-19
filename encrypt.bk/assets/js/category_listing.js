var __ajaxInProgress = 0;
var __categoryId = 0;
var __categoriesRecieved = new Array();
var __fromId        = 0;
var __toId          = 0;
function editCategory(categoryID)
{
    $('#popUpMessage').remove();
    $.ajax({
        url: admin_url+'environment/edit_category',
        type: "POST",
        data:{ "is_ajax":true, 'id':categoryID},
        success: function(response) {
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                __categoryId = categoryID;
                $('#category_name').val('');
                if(__categoryId > 0)
                {
                    var category_data = data['category'];
                    $('#category_name').val(category_data['ct_name']);
                }
                $('#category_manage').modal();                
            }
            else
            {
                lauch_common_message('Error Occured', data['message']);
            }
        }
    });
}

function saveCategory()
{
    if(__ajaxInProgress > 0)
    {
        return false;
    }
    var category_name         = $('#category_name').val();
    var errorCount            = 0;
    var errorMessage          = '';

    if (category_name == '')
    {
        errorMessage += 'Enter Category Name.<br />';
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
            url: admin_url + 'environment/save_category',
            type: "POST",
            data: {"is_ajax": true, 'cat_name': category_name, 'cat_id': __categoryId},
            success: function (response) {
                __ajaxInProgress = 0;
                var data = $.parseJSON(response);
                
                if(data['error']==false)
                {
                    $('#save_category_btn').html('SAVING...<ripples></ripples>');
                    var category_data = data['category'];
                    var renderCategoryHtml = '';
                    
                    if(data['exist'] == '1')
                    {
                        $('#category_'+category_data['id']+' .lecture-name').html(category_data['ct_name']);
                    }else
                    {
                       
                        renderCategoryHtml += '<li id="category_'+category_data['id']+'">';
                        renderCategoryHtml += ' <div class="lecture-hold question-category-lecturehold"> <div class="lecture-counter"></div> ';
                        renderCategoryHtml += '     <a href="javascript:void(0)" class="lecture-innerclick category-innerclick">';
                        renderCategoryHtml += '         <span class="lecture-name">'+category_data['ct_name']+'</span>';
                        renderCategoryHtml += '     </a>';
                        renderCategoryHtml += '     <div class="btn-group lecture-control">';
                        renderCategoryHtml += '         <span class="dropdown-tigger" data-toggle="dropdown">';
                        renderCategoryHtml += '             <span class="label-text">';
                        renderCategoryHtml += '                 <i class="icon icon-down-arrow"></i>';
                        renderCategoryHtml += '             </span>';
                        renderCategoryHtml += '             <span class="tilder"></span>';
                        renderCategoryHtml += '         </span>';
                        renderCategoryHtml += '         <ul class="dropdown-menu pull-right" role="menu">';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="editCategory(\''+category_data['id']+'\')">Edit</a></li>';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" data-toggle="modal" data-target="#category_migrate" onclick="migrateCategory(\''+btoa(unescape(encodeURIComponent(category_data['ct_name'])))+'\', \''+category_data['id']+'\')">Migrate</a></li>';
                        renderCategoryHtml += '             <li><a href="javascript:void(0)" onclick="deleteCategory(\''+btoa(category_data['ct_name'])+'\',\''+category_data['id']+'\')">Delete</a></li>';
                        renderCategoryHtml += '         </ul>';
                        renderCategoryHtml += '     </div>';
                        renderCategoryHtml += ' </div>';
                        renderCategoryHtml += '</li>';
                        
                        $('#category_manage_wrapper').prepend(renderCategoryHtml);
                        
                    }
                    $('#category_manage').modal('hide');
                }
                else
                {
                    $('#category_manage .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
                //__categoryId = 0;
                $('#save_category_btn').html('SAVE<ripples></ripples>');
            }
        });
    }
}

function deleteCategory(category_name, category_id)
{
    $.ajax({
        url: admin_url + 'environment/check_category_connection',
        type: "POST",
        data: {"is_ajax": true, 'cat_name': atob(category_name), 'cat_id': category_id},
        success: function (response) {
            var data = $.parseJSON(response);
            if(data['error']==false)
            {
                $('#category_delete_header_text').html('Delete Category named "'+atob(category_name)+'"');
                $('#category_delete_message').html('Are you sure to delete the category named "'+atob(category_name)+'"');
                $('#deleteCategory').modal('show');
                $('#delete_category_ok').unbind();
                $('#delete_category_ok').click({"cat_id": category_id, 'is_ajax':'true'}, deleteCategoryConfirmed);
            }else
            {
                lauch_common_message('Error in deleting categories', data['message']);
                scrollToTopOfPage();
                
            }
        }
    });
}

function deleteCategoryConfirmed(param)
{
    $.ajax({
        url: admin_url+'environment/delete_category',
        type: "POST",
        data:{ "is_ajax":true, 'cat_id':param.data.cat_id},
        success: function(response) {
            var data  = $.parseJSON(response);
            $('#category_'+param.data.cat_id).remove();
            $('#deleteCategory').modal('hide');
            $('#category_manage_wrapper').prepend(renderPopUpMessage('success', data['message']))
        }
    });
}

function migrateCategory(category_name, category_id)
{
    $('#category_select_migrate').val('');
    $.ajax({
        url: admin_url+'environment/get_category',
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
    __fromId = category_id;
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
    __fromId = this.value;
     __toId  = 0;
    renderCatListing += '<option value="0">Choose Category</option>';
    renderCatListing += renderCategoriesLi(__categoriesRecieved,this.value,2);
    $('#category_select_migrate').html(renderCatListing);
})

$(document).on('change', '#category_select_migrate', function() {
    __toId = this.value;
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
            url: admin_url+'environment/migrate_category',
            type: "POST",
            data:{ "is_ajax":true, 'cat_id':migrate_category_id, 'previous_cat_id':__fromId},
            success: function(response) {
                var data  = $.parseJSON(response);
                $('#category_migrate').modal('hide');
                $('#category_manage_wrapper').prepend(renderPopUpMessage('success', data['message']))
            }
        });
    }
}