
$(document).on('click', '#basic-addon2', function(){
    getCatalogs();
});

var timeOut = '';
$(document).on('keyup', '#catalog_keyword', function(){
    clearTimeout(timeOut);
    timeOut = setTimeout(function(){getCatalogs();}, 600);
});

var catalog_selected = new Array();
$(document).on('click', '.catalog-checkbox', function(){
    var catalog_id = $(this).val();
    if ($('.catalog-checkbox:checked').length == $('.catalog-checkbox').length) {
        $('.catalog-checkbox-parent').prop('checked',true);
    }
    if ($(this).is(':checked')) {
        $('.list-button').removeClass('list-disabled');
        catalog_selected.push(catalog_id);
    }else{
        $('.catalog-checkbox-parent').prop('checked', false);
        $('.list-button').addClass('list-disabled');
        removeArrayIndex(catalog_selected, catalog_id);
    }
    if(catalog_selected.length > 0){
        $("#selected_catalog_count").html(' ('+catalog_selected.length+')');
    }else{
        $("#selected_catalog_count").html(''); 
    }

    if(catalog_selected.length > 1){
        $("#catalog_bulk").css('display','block');
    }else{
        $("#catalog_bulk").css('display','none');
    }
});

$(document).on('click', '.catalog-checkbox-parent', function(){
    var parent_check_box = this;
    catalog_selected = new Array();    
    $( '.catalog-checkbox' ).prop('checked', $(parent_check_box).is(':checked'));
    $('.list-button').addClass('list-disabled');
    if ($(parent_check_box).is(':checked') == true) {
        $( '.catalog-checkbox' ).each(function( index ) {
           catalog_selected.push($( this ).val());
        });
        $('.list-button').removeClass('list-disabled');
    }
    if(catalog_selected.length > 0){
        $("#selected_catalog_count").html(' ('+catalog_selected.length+')');
    }else{
        $("#selected_catalog_count").html(''); 
    }

    if(catalog_selected.length > 0){
        $("#catalog_bulk").css('display','block');
    }else{
        $("#catalog_bulk").css('display','none');
    }
});



var __filter_dropdown = '';
var __category_id      = '';
function getCatalogs()
{
    var keyword  = $('#catalog_keyword').val();
    $.ajax({
        url: admin_url+'catalog/catalog_json',
        type: "POST",
        data:{"is_ajax":true, "filter":__filter_dropdown, "category_id":__category_id, "keyword":keyword},
        success: function(response) {
            var data_catalog = $.parseJSON(response);

            $('.catalog-checkbox-parent').prop('checked', false);
            catalog_selected = new Array();
            if(data_catalog['catalogs'].length > 0){
                $('#catalog_row_wrapper').html(renderCatalogHtml(response));
            }else{
                $('#catalog_row_wrapper').html(renderPopUpMessage('error', 'No catalogs found.'));
            }
            scrollToTopOfPage();
        }                        
                        

    });
}

function filter_category(category_id)
{
   __category_id        = category_id;
   $('#dropdown_text').html($('#dropdown_list_'+category_id).text()+'<span class="caret"></span>');
   getCatalogs();
}

function filter_catalog_by(filter)
{
   __filter_dropdown        = filter;
   $('#filter_dropdown_text').html($('#filer_dropdown_list_'+filter).text()+'<span class="caret"></span>');
   getCatalogs();
}
function changeCatalogStatus(catalog_id, header_text, message, button_text)
{
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content').html(message);
    $('#confirm_box_ok').html(button_text);
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({"catalog_id": catalog_id,"status_mess": message}, changeStatusConfirmed);  
    cleanPopUpMessage();   
}

function changeStatusConfirmed(params){
    $.ajax({
        url: admin_url+'catalog/change_status',
        type: "POST",
        data:{"catalog_id":params.data.catalog_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#action_class_'+params.data.catalog_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_'+params.data.catalog_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_'+params.data.catalog_id).html(data['action_list']);
                $('#publish-course').modal('hide');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Catalog '+params.data.status_mess+'d successfully'));
                scrollToTopOfPage();
            }
            else
            {
                lauch_common_message('Error occured', data['message']);    
                $('#publish-course').modal('hide');
                //$('#confirm_box_title').html(data['message']);
                //$('#confirm_box_content').html('');
            }
        }
    });
}
var statMess = '';
function changeStatusBulk(header_text, status,button_text)
{
    if(catalog_selected.length > 0 && $('.item-deleted:checked').length == 0 )
    {
        $('#publish-course').modal('show');
    }
    if($('.item-deleted:checked').length > 0 )
    {
        $('#uncheck_delete').modal('show');
    }
    if(status=='1')
    {
        statMess = 'activated';
    }
    if(status=='0')
    {
        statMess = 'deactivated';
    }
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_ok').html(button_text);
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({'status':status,'status_message':statMess}, ChangeStatusBulkConfirmed); 
    cleanPopUpMessage();   
}

function ChangeStatusBulkConfirmed(params){
    $.ajax({
        url: admin_url+'catalog/change_status_bulk',
        type: "POST",
        data:{"catalogs":JSON.stringify(catalog_selected), "status_bulk":params.data.status, "is_ajax":true},
        success: function(response) {
            var data  = $.parseJSON(response);
            console.log(catalog_selected);
            if(data['error'] == false)
            {
                $('#catalog_row_wrapper').html(renderCatalogHtml(response));
                $('.catalog-checkbox-parent').prop('checked', false);
                $('#publish-course').modal('hide');
                catalog_selected = new Array();
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Catalog '+params.data.status_message+' successfully'));
                scrollToTopOfPage();
            }
            else{
                lauch_common_message('Error occured', data['message']);    
                $('#publish-course').modal('hide');
            }
        }
    });
}

function restoreCatalog(catalog_id, header_text)
{
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content').html('');
    $('#confirm_box_ok').html("RESTORE");
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({"catalog_id": catalog_id}, restoreCatalogConfirmed); 
    cleanPopUpMessage();       
}

function restoreCatalogConfirmed(params){
    $.ajax({
        url: admin_url+'catalog/restore',
        type: "POST",
        data:{"catalog_id":params.data.catalog_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#catalog_action_'+params.data.catalog_id).html(data['action_list']);
                $('#action_class_'+params.data.catalog_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass(data['actions']['action_class']).html(data['actions']['action']);
                $('#label_class_'+params.data.catalog_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass(data['actions']['label_class']).html(data['actions']['label_text']);
                $('#status_btn_'+params.data.catalog_id).html(data['actions']['status_button']);
                $('#publish-course').modal('hide');
                $('#catalog_details_'+params.data.catalog_id).removeClass('item-deleted');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Catalog restored successfully'));
                scrollToTopOfPage();
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function deleteCatalog(catalog_id, header_text)
{
    $('#confirm_box_title').html(header_text);
    $('#confirm_box_content').html('');
    $('#confirm_box_ok').html("DELETE");
    $('#confirm_box_ok').unbind();
    $('#confirm_box_ok').click({"catalog_id": catalog_id}, deleteCatalogConfirmed); 
    cleanPopUpMessage();        
}

function deleteCatalogConfirmed(params){
    $.ajax({
        url: admin_url+'catalog/delete',
        type: "POST",
        data:{"catalog_id":params.data.catalog_id, "is_ajax":true},
        success: function(response) {
            console.log(response);
            var data  = $.parseJSON(response);
            if(data['error'] == false)
            {
                $('#catalog_action_'+params.data.catalog_id).html(data['action_list']);
                $('#action_class_'+params.data.catalog_id).removeClass('label-warning').removeClass('label-danger').removeClass('label-success').addClass('label-danger').html(data['actions']['action']);
                $('#label_class_'+params.data.catalog_id).removeClass('spn-delete').removeClass('spn-active').removeClass('spn-inactive').addClass('spn-delete').html(data['actions']['label_text']);
                $('#publish-course').modal('hide');
                $('#catalog_details_'+params.data.catalog_id).addClass('item-deleted');
                $('#show_message_div').prepend(renderPopUpMessage('success', 'Catalog deleted successfully'));
                scrollToTopOfPage();
            }
            else
            {
                $('#confirm_box_title').html(data['message']);
                $('#confirm_box_content').html('');
            }
        }
    });
}

function createCatalog()
{
    __total_price   = 0;
    __catalog_course = new Array();
    
    $('#total_catalog_course_price').html(__total_price+' INR');
    $('#catalog_course_wrapper').html('');

    $('#create_catalog_new').unbind();
    $('#create_catalog_new').click({}, createCatalogConfirmed);    
    $.ajax({
        url: admin_url+'course/course_json',
        type: "POST",
        data:{ "is_ajax":true, 'status':'1', 'filter':'active'},
        success: function(response) {
            //console.log(response);
            var data            = $.parseJSON(response);
            var courseHtml      = '';
            if(data['courses'].length > 0 )
            {
                for (var i=0; i<data['courses'].length; i++)
                {
                    if(data['courses'][i]['cb_discount'] > 0){
                        var course_price = data['courses'][i]['cb_discount'];
                    }else{
                        course_price = data['courses'][i]['cb_price'];
                    }
                    courseHtml += '<div class="checkbox-wrap" id="catalog_course_assign_'+data['courses'][i]['id']+'" data-amount="'+course_price+'">';
                    courseHtml += '    <label class="width-100p">';
                    courseHtml += '        <span class="chk-box">';
                    courseHtml += '            <input type="checkbox" value="'+data['courses'][i]['id']+'" class="catalog-course-assign">'+data['courses'][i]['cb_title'];
                    courseHtml += '        </span>';
                    courseHtml += '        <span class="email-label pull-right">'+course_price+' INR</span>';
                    courseHtml += '    </label>';
                    courseHtml += '</div>';
                }
                $('#total_catalog_course_price').html(__total_price+' INR');
                $('#catalog_course_wrapper').html(courseHtml);
            }
        }
    });
}

var __catalog_course = new Array();
var __total_price    = 0;
$(document).on('click', '.catalog-course-assign', function(){
    var course_id = $(this).val();
    if ($(this).is(':checked')) {
        __total_price = __total_price + Number($('#catalog_course_assign_'+course_id).attr('data-amount'));
        __catalog_course.push(course_id);
    }else{
        __total_price = __total_price - Number($('#catalog_course_assign_'+course_id).attr('data-amount'));
        removeArrayIndex(__catalog_course, course_id);
    }
    $('#total_catalog_course_price').html(__total_price+' INR');
});
      //Live Price update
 // $(document).ready(function(){
 //     var cat_price            = $('#catalog_price_create');
 //     //Live Discount update
 //     var cat_discount         = $('#catalog_discount_create');
 //     cat_price.keyup(function(e) {
 //         if(parseInt($(this).val()) < parseInt(cat_discount.val())){
 //             $('.modal-body').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
 //             cleanPopUpMessage();
 //             scrollToTopOfPage();
 //         }
 //         else{
 //             cleanPopUpMessage();
 //         }
 //     });

 //     cat_discount.keyup(function(e) {
 //         if(parseInt($(this).val()) > parseInt(cat_price.val())){
 //             $('.modal-body').prepend(renderPopUpMessage('error', 'Discount price should not be greater than price amount.'));
 //             cleanPopUpMessage();
 //             scrollToTopOfPage();
 //         }
 //         else{
 //             cleanPopUpMessage();
 //         }
 //     });
 // });

function createCatalogConfirmed()
{
    var catalog_name     = $('#catalog_name_create').val();
        catalog_name     = catalog_name.replace(/["<>{}]/g, '');
        catalog_name     = catalog_name.trim();
    var catalog_price    = $('#catalog_price_create').val();
    var catalog_discount = $('#catalog_discount_create').val();
    var errorCount       = 0;
    var errorMessage     = '';
    
    if( catalog_name == '')
    {
        errorCount++;
        errorMessage += 'please enter catalog name <br />';
    }

    if( __catalog_course.length == 0)
    {
        errorCount++;
        errorMessage += 'please choose any course<br />';
    }

    if( catalog_price == '')
    {
        errorCount++;
        errorMessage += 'please enter catalog price <br />';
    }
    else
    {
        if(parseInt(catalog_price) < parseInt(catalog_discount))
        {
            errorCount++;
            errorMessage += 'Discount price should not be greater than price amount. <br />';
        }else if(parseInt(catalog_price) == parseInt(catalog_discount)){
            errorCount++;
            errorMessage += 'Discount price should not be equal to price amount. <br />';
        }   
        if(isNaN(catalog_price))
        {
            errorCount++;
            errorMessage += 'please enter valid catalog price <br />';         
        }
    }

    if( catalog_discount == '')
    {
        errorCount++;
        errorMessage += 'please enter catalog discount price <br />';
    }
    else
    {
        if(isNaN(catalog_price))
        {
            errorCount++;
            errorMessage += 'please enter valid catalog discount price <br />';            
        }
    }
    cleanPopUpMessage();
    if( errorCount == 0 )
    {
        $.ajax({
            url: admin_url+'catalog/create_catalog',
            type: "POST",
            data:{"is_ajax":true, 'catalog_name':catalog_name, 'catalog_price':catalog_price, 'catalog_discount':catalog_discount, 'course_ids':JSON.stringify(__catalog_course)},
            success: function(response) {
                var data  = $.parseJSON(response);
                if(data['error'] == false)
                {
                    window.location = admin_url+'catalog_settings/basics/'+data['id'];
                }
                else
                {
                    $('#create-catalog-new .modal-body').prepend(renderPopUpMessage('error', data['message']));
                    scrollToTopOfPage();
                }
            }
        });
    }
    else
    {
        $('#create-catalog-new .modal-body').prepend(renderPopUpMessage('error', errorMessage));
        scrollToTopOfPage();
    }
}


function renderCatalogHtml(response)
{
    $("#selected_catalog_count").html(''); 
    var data         = $.parseJSON(response);
    var catalogHtml  = '';
    if(data['catalogs'].length > 0 )
    {
        for (var i=0; i<data['catalogs'].length; i++)
        {
            //set the database value
            var action_label   = data['catalogs'][i]['wa_name'];
            var action         = data['catalogs'][i]['wa_name'];
            var action_date    = data['catalogs'][i]['updated_date'];
                action_date    = new Date(action_date.replace(/-/g, '/'));
                action_date    = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
            var action_author  = (data['catalogs'][i]['us_name']!='')?data['catalogs'][i]['us_name']:'Admin';

            //consider the record is deleted and set the value if record deleted
            var label_class    = 'spn-delete';
            var action_class   = 'label-danger';
            var item_deleted   = 'item-deleted';
            //case if record is not deleted
            if(data['catalogs'][i]['c_deleted'] == 0)
            {
                item_deleted = '';
                if(data['catalogs'][i]['action_id'] == 1)
                {
                    action_class     = 'label-warning';                            
                    var action_date  = data['catalogs'][i]['created_date'];
                        action_date  = new Date(action_date.replace(/-/g, '/'));
                        action_date  = ((action_date.getDate()>9)?action_date.getDate():'0'+action_date.getDate())+' '+month[action_date.getMonth()]+' '+action_date.getFullYear();
                    label_class      = 'spn-inactive';
                }
                else
                {
                    if(data['catalogs'][i]['c_status'] == 1)
                    {
                        action_class   = 'label-success';                                                                
                        label_class    = 'spn-active';                                        
                        action         = lang('active');
                    }
                    else
                    {
                        action_class   = 'label-warning';                                                                
                        label_class    = 'spn-inactive';                                        
                        action         = lang('inactive');
                    }
                }
            }

            catalogHtml += '<div class="rTableRow" id="catalog_row_'+data['catalogs'][i]['id']+'" data-title="'+data['catalogs'][i]['c_title']+'" data-price="'+data['catalogs'][i]['c_price']+'">';
            catalogHtml += '    <div class="rTableCell cours-fix ellipsis-hidden"> ';
            catalogHtml += '        <div class="ellipsis-style">';  
            catalogHtml += '            <input type="checkbox" class="catalog-checkbox '+item_deleted+'" value="'+data['catalogs'][i]['id']+'" id="catalog_details_'+data['catalogs'][i]['id']+'">'; 
            catalogHtml += '            <span class="icon-wrap-round">';
            catalogHtml += '                <i class="icon icon-graduation-cap"></i>';
            catalogHtml += '            </span>';
            catalogHtml += '            <a href="'+admin_url+'catalog/basic/'+data['catalogs'][i]['id']+'" class="cust-sm-6 padd0"> '+data['catalogs'][i]['c_title']+'</a>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';

            catalogHtml += '    <div class="rTableCell pad0 cours-fix width70">';
            catalogHtml += '        <div class="col-sm-12 pad0">';
            catalogHtml += '            <label class="pull-right label '+action_class+'" id="action_class_'+data['catalogs'][i]['id']+'">';
            catalogHtml +=               action;
            catalogHtml += '            </label>';
            catalogHtml += '        </div>';
            catalogHtml += '        <div class="col-sm-12 pad0 pad-vert5 pos-inhrt">   ';
            catalogHtml += '            <span class="pull-right '+label_class+'" id="label_class_'+data['catalogs'][i]['id']+'"> '+action_label+' by- '+action_author+' on '+action_date+'</span>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';
            catalogHtml += '    <div class="td-dropdown rTableCell">';
            catalogHtml += '        <div class="btn-group lecture-control">';
            catalogHtml += '            <span class="dropdown-tigger" data-toggle="dropdown">';
            catalogHtml += '                 <span class="label-text">';
            catalogHtml += '                  <i class="icon icon-down-arrow"></i>';
            catalogHtml += '                </span>';
            catalogHtml += '                <span class="tilder"></span>';
            catalogHtml += '            </span>';
            catalogHtml += '            <ul class="dropdown-menu pull-right" role="menu" id="catalog_action_'+data['catalogs'][i]['id']+'">';
            if(data['catalogs'][i]['c_deleted'] == 0){
            catalogHtml += '                    <li id="status_btn_'+data['catalogs'][i]['id']+'">';
            var cb_status = (data['catalogs'][i]['c_status']==1)?'deactivate':'activate'; 
            var cb_action = cb_status; 
            catalogHtml += '                        <a href="javascript:void(0);" data-toggle="modal" onclick="changeCatalogStatus(\''+data['catalogs'][i]['id']+'\', \''+lang('are_you_sure_to')+' '+cb_action.charAt(0).toUpperCase()+''+cb_action.slice(1)+' '+lang('catalog')+' - '+data['catalogs'][i]['c_title']+' ?\', \''+lang(cb_status)+'\', \''+lang(cb_status)+'\')" data-target="#publish-course">'+lang(cb_status)+'</a>';
            catalogHtml += '                    </li>';
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="'+webConfigs('admin_url')+'catalog_settings/basics/'+data['catalogs'][i]['id']+'">'+lang('settings')+'</a>';
            catalogHtml += '                    </li>';
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="javascript:void(0);" id="delete_btn_'+data['catalogs'][i]['id']+'" data-toggle="modal" onclick="deleteCatalog(\''+data['catalogs'][i]['id']+'\', \''+lang('are_you_sure_to')+' '+lang('delete_catalog')+' - '+data['catalogs'][i]['c_title']+' ?\')" data-target="#publish-course">'+lang('delete')+'</a>';
            catalogHtml += '                    </li>';
            }
            else
            {
            catalogHtml += '                    <li>';
            catalogHtml += '                        <a href="javascript:void(0);" id="restore_btn_'+data['catalogs'][i]['id']+'" data-toggle="modal" onclick="restoreCatalog(\''+data['catalogs'][i]['id']+'\', \''+lang('are_you_sure_to')+' '+lang('restore_catalog')+' - '+data['catalogs'][i]['c_title']+' ?\')" data-target="#publish-course">'+lang('restore')+'</a>';
            catalogHtml += '                    </li>';
            }
            catalogHtml += '            </ul>';
            catalogHtml += '        </div>';
            catalogHtml += '    </div>';
            catalogHtml += '</div> ';
        }
    }
    else
    {

    }
    return catalogHtml;
}
